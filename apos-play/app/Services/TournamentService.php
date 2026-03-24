<?php

namespace App\Services;

use App\Enums\TournamentFormat;
use App\Enums\TournamentMatchStatus;
use App\Enums\TournamentStatus;
use App\Enums\TournamentTeamPaymentStatus;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\TournamentPlayerStat;
use App\Models\TournamentTeam;
use App\Models\TournamentTeamMember;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class TournamentService
{
    /**
     * Create a new tournament. Validates that the owner owns the court if provided.
     */
    public function create(array $data, User $owner): Tournament
    {
        if (!empty($data['court_id'])) {
            $court = \App\Models\Court::find($data['court_id']);
            if (!$court) {
                throw new \InvalidArgumentException('La cancha seleccionada no existe.');
            }
            // Validate ownership via complex if complex_id is set
            if ($court->complex_id && $owner->isOwner()) {
                $ownsComplex = $owner->complexesOwned()->where('id', $court->complex_id)->exists();
                if (!$ownsComplex && !$owner->isSuperadmin()) {
                    throw new \InvalidArgumentException('No tienes permiso para usar esa cancha.');
                }
            }
        }

        return DB::transaction(function () use ($data, $owner) {
            return Tournament::create(array_merge($data, ['owner_id' => $owner->id]));
        });
    }

    /**
     * Register a team in the tournament. Creates TournamentTeam + TournamentTeamMember for captain.
     */
    public function registerTeam(Tournament $tournament, string $teamName, User $captain): TournamentTeam
    {
        if (!$tournament->isRegistrationOpen()) {
            throw new \RuntimeException('Las inscripciones para este torneo no están abiertas.');
        }

        $alreadyInTeam = TournamentTeamMember::whereHas('team', function ($q) use ($tournament) {
            $q->where('tournament_id', $tournament->id)->whereNull('deleted_at');
        })->where('user_id', $captain->id)->whereNull('deleted_at')->exists();

        if ($alreadyInTeam) {
            throw new \RuntimeException('Ya estás inscripto en un equipo de este torneo.');
        }

        return DB::transaction(function () use ($tournament, $teamName, $captain) {
            $team = TournamentTeam::create([
                'tournament_id'  => $tournament->id,
                'name'           => $teamName,
                'captain_id'     => $captain->id,
                'payment_status' => TournamentTeamPaymentStatus::PENDING->value,
            ]);

            TournamentTeamMember::create([
                'team_id'    => $team->id,
                'user_id'    => $captain->id,
                'is_captain' => true,
            ]);

            return $team;
        });
    }

    /**
     * Add a member to a team. Validates the user is not already in another team in the same tournament.
     */
    public function addMember(TournamentTeam $team, User $user): TournamentTeamMember
    {
        $tournament = $team->tournament;

        $alreadyInTeam = TournamentTeamMember::whereHas('team', function ($q) use ($tournament) {
            $q->where('tournament_id', $tournament->id)->whereNull('deleted_at');
        })->where('user_id', $user->id)->whereNull('deleted_at')->exists();

        if ($alreadyInTeam) {
            throw new \RuntimeException('Este usuario ya está en un equipo de este torneo.');
        }

        $memberCount = $team->members()->count();
        if ($memberCount >= $tournament->max_players) {
            throw new \RuntimeException("El equipo ya tiene el máximo de {$tournament->max_players} jugadores.");
        }

        return DB::transaction(function () use ($team, $user) {
            return TournamentTeamMember::create([
                'team_id'    => $team->id,
                'user_id'    => $user->id,
                'is_captain' => false,
            ]);
        });
    }

    /**
     * Remove a member from a team.
     */
    public function removeMember(TournamentTeam $team, User $user): void
    {
        if ($user->id === $team->captain_id) {
            throw new \RuntimeException('No se puede remover al capitán del equipo.');
        }

        DB::transaction(function () use ($team, $user) {
            TournamentTeamMember::where('team_id', $team->id)
                ->where('user_id', $user->id)
                ->delete();
        });
    }

    /**
     * Generate fixture based on tournament format.
     * Uses only paid teams. Single elimination shuffles teams randomly.
     */
    public function generateFixture(Tournament $tournament): void
    {
        if ($tournament->status !== TournamentStatus::IN_PROGRESS) {
            throw new \RuntimeException('El torneo debe estar en curso para generar el fixture.');
        }

        $teams = $tournament->teams()
            ->where('payment_status', TournamentTeamPaymentStatus::PAID->value)
            ->get();

        $teamCount = $teams->count();

        if ($teamCount < 2) {
            throw new \RuntimeException('Se necesitan al menos 2 equipos con pago confirmado para generar el fixture.');
        }

        // Delete existing matches
        $tournament->matches()->delete();

        if ($tournament->format === TournamentFormat::ROUND_ROBIN) {
            $this->generateRoundRobinFixture($tournament, $teams);
        } else {
            // Shuffle teams randomly for single elimination bracket
            $shuffled = $teams->shuffle();
            $this->generateSingleEliminationFixture($tournament, $shuffled);
        }
    }

    /**
     * Round-robin scheduling algorithm.
     */
    private function generateRoundRobinFixture(Tournament $tournament, Collection $teams): void
    {
        $teamList = $teams->values()->toArray();
        $n = count($teamList);

        // If odd number of teams, add a bye
        if ($n % 2 !== 0) {
            $teamList[] = null; // bye
            $n++;
        }

        $rounds = $n - 1;
        $matchesPerRound = $n / 2;

        DB::transaction(function () use ($tournament, $teamList, $rounds, $matchesPerRound) {
            $rotating = array_slice($teamList, 1); // all except first

            for ($round = 1; $round <= $rounds; $round++) {
                $fixed = $teamList[0];
                $roundTeams = array_merge([$fixed], $rotating);

                for ($match = 0; $match < $matchesPerRound; $match++) {
                    $home = $roundTeams[$match];
                    $away = $roundTeams[$n - 1 - $match];

                    // Skip bye matches
                    if ($home === null || $away === null) {
                        continue;
                    }

                    TournamentMatch::create([
                        'tournament_id' => $tournament->id,
                        'round'         => $round,
                        'round_name'    => "Fecha {$round}",
                        'home_team_id'  => $home['id'],
                        'away_team_id'  => $away['id'],
                        'status'        => TournamentMatchStatus::PENDING->value,
                    ]);
                }

                // Rotate teams (keep first fixed, rotate the rest)
                $last = array_pop($rotating);
                array_unshift($rotating, $last);
            }
        });
    }

    /**
     * Single elimination bracket.
     */
    private function generateSingleEliminationFixture(Tournament $tournament, Collection $teams): void
    {
        $teamList = $teams->values()->toArray();
        $n = count($teamList);

        // Find next power of 2
        $bracket = 1;
        while ($bracket < $n) {
            $bracket *= 2;
        }

        DB::transaction(function () use ($tournament, $teamList, $bracket) {
            $round = 1;
            $roundName = $this->eliminationRoundName($bracket);

            for ($i = 0; $i < $bracket / 2; $i++) {
                $homeTeam = $teamList[$i * 2] ?? null;
                $awayTeam = $teamList[$i * 2 + 1] ?? null;

                TournamentMatch::create([
                    'tournament_id' => $tournament->id,
                    'round'         => $round,
                    'round_name'    => $roundName,
                    'home_team_id'  => $homeTeam ? $homeTeam['id'] : null,
                    'away_team_id'  => $awayTeam ? $awayTeam['id'] : null,
                    'status'        => TournamentMatchStatus::PENDING->value,
                ]);
            }

            // Generate empty subsequent rounds
            $currentBracket = $bracket / 2;
            $currentRound = 2;
            while ($currentBracket >= 2) {
                $roundName = $this->eliminationRoundName($currentBracket);
                $matchCount = $currentBracket / 2;

                for ($i = 0; $i < $matchCount; $i++) {
                    TournamentMatch::create([
                        'tournament_id' => $tournament->id,
                        'round'         => $currentRound,
                        'round_name'    => $roundName,
                        'home_team_id'  => null,
                        'away_team_id'  => null,
                        'status'        => TournamentMatchStatus::PENDING->value,
                    ]);
                }

                $currentBracket /= 2;
                $currentRound++;
            }
        });
    }

    private function eliminationRoundName(int $teamsInRound): string
    {
        return match($teamsInRound) {
            2  => 'Final',
            4  => 'Semifinal',
            8  => 'Cuartos de Final',
            16 => 'Octavos de Final',
            default => "Ronda de {$teamsInRound}",
        };
    }

    /**
     * Record match result + optional player stats.
     */
    public function recordResult(TournamentMatch $match, int $homeScore, int $awayScore, array $playerStats = []): void
    {
        DB::transaction(function () use ($match, $homeScore, $awayScore, $playerStats) {
            $match->update([
                'home_score' => $homeScore,
                'away_score' => $awayScore,
                'status'     => TournamentMatchStatus::COMPLETED->value,
            ]);

            // Delete existing stats for this match
            TournamentPlayerStat::where('match_id', $match->id)->delete();

            // Record player stats
            foreach ($playerStats as $stat) {
                if (empty($stat['user_id']) || empty($stat['team_id'])) {
                    continue;
                }
                TournamentPlayerStat::create([
                    'tournament_id' => $match->tournament_id,
                    'match_id'      => $match->id,
                    'team_id'       => $stat['team_id'],
                    'user_id'       => $stat['user_id'],
                    'goals'         => $stat['goals'] ?? 0,
                    'assists'       => $stat['assists'] ?? 0,
                    'yellow_cards'  => $stat['yellow_cards'] ?? 0,
                    'red_cards'     => $stat['red_cards'] ?? 0,
                ]);
            }
        });
    }

    /**
     * Get standings for round_robin tournaments.
     * Returns collection of [team, played, won, drawn, lost, gf, ga, gd, points]
     */
    public function getStandings(Tournament $tournament): Collection
    {
        $teams = $tournament->teams()->get()->keyBy('id');
        $standings = [];

        foreach ($teams as $team) {
            $standings[$team->id] = [
                'team'   => $team,
                'played' => 0,
                'won'    => 0,
                'drawn'  => 0,
                'lost'   => 0,
                'gf'     => 0,
                'ga'     => 0,
                'gd'     => 0,
                'points' => 0,
            ];
        }

        $completedMatches = $tournament->matches()
            ->where('status', TournamentMatchStatus::COMPLETED->value)
            ->get();

        foreach ($completedMatches as $match) {
            $homeId = $match->home_team_id;
            $awayId = $match->away_team_id;

            if (!isset($standings[$homeId]) || !isset($standings[$awayId])) {
                continue;
            }

            $homeScore = $match->home_score;
            $awayScore = $match->away_score;

            $standings[$homeId]['played']++;
            $standings[$awayId]['played']++;
            $standings[$homeId]['gf'] += $homeScore;
            $standings[$homeId]['ga'] += $awayScore;
            $standings[$awayId]['gf'] += $awayScore;
            $standings[$awayId]['ga'] += $homeScore;

            if ($homeScore > $awayScore) {
                $standings[$homeId]['won']++;
                $standings[$homeId]['points'] += 3;
                $standings[$awayId]['lost']++;
            } elseif ($homeScore < $awayScore) {
                $standings[$awayId]['won']++;
                $standings[$awayId]['points'] += 3;
                $standings[$homeId]['lost']++;
            } else {
                $standings[$homeId]['drawn']++;
                $standings[$homeId]['points']++;
                $standings[$awayId]['drawn']++;
                $standings[$awayId]['points']++;
            }
        }

        // Compute goal difference
        foreach ($standings as &$row) {
            $row['gd'] = $row['gf'] - $row['ga'];
        }

        return collect(array_values($standings))->sortByDesc(function ($row) {
            return [$row['points'], $row['gd'], $row['gf']];
        })->values();
    }

    /**
     * Get top scorers: user, team, goals, assists, yellow_cards, red_cards.
     */
    public function getPlayerStats(Tournament $tournament): Collection
    {
        return TournamentPlayerStat::where('tournament_id', $tournament->id)
            ->with(['user', 'team'])
            ->get()
            ->groupBy('user_id')
            ->map(function ($stats) {
                $first = $stats->first();
                return [
                    'user'         => $first->user,
                    'team'         => $first->team,
                    'goals'        => $stats->sum('goals'),
                    'assists'      => $stats->sum('assists'),
                    'yellow_cards' => $stats->sum('yellow_cards'),
                    'red_cards'    => $stats->sum('red_cards'),
                ];
            })
            ->sortByDesc('goals')
            ->values();
    }

    /**
     * Mark tournament as open (status=open).
     */
    public function openRegistration(Tournament $tournament): void
    {
        if ($tournament->status !== TournamentStatus::DRAFT) {
            throw new \RuntimeException('Solo se pueden abrir torneos en estado borrador.');
        }

        if (empty($tournament->name)) {
            throw new \RuntimeException('El torneo debe tener un nombre.');
        }

        if (empty($tournament->registration_deadline)) {
            throw new \RuntimeException('El torneo debe tener una fecha límite de inscripción.');
        }

        DB::transaction(function () use ($tournament) {
            $tournament->update(['status' => TournamentStatus::OPEN->value]);
        });
    }

    /**
     * Start tournament (status=in_progress). Validates paid teams >= min_teams.
     */
    public function startTournament(Tournament $tournament): void
    {
        if ($tournament->status !== TournamentStatus::OPEN) {
            throw new \RuntimeException('Solo se pueden iniciar torneos con inscripciones abiertas.');
        }

        $paidTeamsCount = $tournament->teams()
            ->where('payment_status', TournamentTeamPaymentStatus::PAID->value)
            ->count();

        $minTeams = $tournament->min_teams ?? 2;

        if ($paidTeamsCount < $minTeams) {
            throw new \RuntimeException(
                "Se necesitan al menos {$minTeams} equipos con pago confirmado para iniciar el torneo. Hay {$paidTeamsCount} actualmente."
            );
        }

        DB::transaction(function () use ($tournament) {
            $tournament->update(['status' => TournamentStatus::IN_PROGRESS->value]);
        });
    }

    /**
     * Withdraw a team from a tournament. Allowed while registration is open.
     * - More than 36h before start: full refund if paid.
     * - Less than 36h before start: withdrawal allowed but no refund (inscription fee is forfeited).
     */
    public function withdrawTeam(TournamentTeam $team): void
    {
        $tournament = $team->tournament;

        if (!in_array($tournament->status, [TournamentStatus::OPEN, TournamentStatus::DRAFT])) {
            throw new \RuntimeException('Solo se puede dar de baja un equipo mientras las inscripciones están abiertas.');
        }

        $startsAt = \Carbon\Carbon::parse($tournament->starts_at, 'America/Argentina/Buenos_Aires')->startOfDay();
        $hoursUntilStart = now('America/Argentina/Buenos_Aires')->diffInHours($startsAt, false);
        $eligible_for_refund = $hoursUntilStart >= 36;

        DB::transaction(function () use ($team, $eligible_for_refund) {
            if ($team->payment_status->value === TournamentTeamPaymentStatus::PAID->value && $eligible_for_refund) {
                $team->update(['payment_status' => TournamentTeamPaymentStatus::REFUNDED->value]);
            }
            $team->members()->delete();
            $team->delete();
        });
    }

    /**
     * Finish tournament.
     */
    public function finishTournament(Tournament $tournament): void
    {
        if ($tournament->status !== TournamentStatus::IN_PROGRESS) {
            throw new \RuntimeException('Solo se pueden finalizar torneos en curso.');
        }

        DB::transaction(function () use ($tournament) {
            $tournament->update(['status' => TournamentStatus::FINISHED->value]);
        });
    }

    /**
     * Create MercadoPago preference for team payment. Returns init_point URL.
     */
    public function createPaymentPreference(TournamentTeam $team): string
    {
        $tournament = $team->tournament;

        if ((float) $tournament->entry_fee <= 0) {
            throw new \RuntimeException('Este torneo no tiene costo de inscripción.');
        }

        $accessToken = config('services.mercadopago.access_token');
        if (empty($accessToken)) {
            throw new \RuntimeException('Error de configuración: Falta el Token de Acceso de Mercado Pago.');
        }

        MercadoPagoConfig::setAccessToken($accessToken);
        $client = new PreferenceClient();

        try {
            $preferenceData = [
                'items' => [[
                    'title'       => "Inscripción Torneo: {$tournament->name} - Equipo: {$team->name}",
                    'quantity'    => 1,
                    'unit_price'  => (float) $tournament->entry_fee,
                    'currency_id' => 'ARS',
                ]],
                'payer' => [
                    'email' => config('services.mercadopago.test_user_email') ?? $team->captain->email,
                ],
                'external_reference' => "tournament_team_{$team->id}",
                'back_urls'          => [
                    'success' => route('tournaments.payment.success'),
                    'failure' => route('tournaments.payment.failure'),
                    'pending' => route('tournaments.payment.pending'),
                ],
            ];

            $preference = $client->create($preferenceData);
            Log::info("Tournament payment preference created for team {$team->id}: {$preference->init_point}");

            return $preference->init_point;
        } catch (MPApiException $e) {
            Log::error('MercadoPago API Error (tournament payment): ' . json_encode($e->getApiResponse()?->getContent()));
            throw new \RuntimeException('Error de Mercado Pago: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Tournament payment error: ' . $e->getMessage());
            throw new \RuntimeException('Error al iniciar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Mark team as paid.
     */
    public function markTeamPaid(TournamentTeam $team, string $paymentId): void
    {
        DB::transaction(function () use ($team, $paymentId) {
            $team->update([
                'payment_status' => TournamentTeamPaymentStatus::PAID->value,
                'payment_id'     => $paymentId,
                'amount_paid'    => $team->tournament->entry_fee,
            ]);
        });
    }
}
