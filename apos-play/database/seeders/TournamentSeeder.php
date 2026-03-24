<?php

namespace Database\Seeders;

use App\Enums\TournamentFormat;
use App\Enums\TournamentStatus;
use App\Enums\TournamentTeamPaymentStatus;
use App\Models\Court;
use App\Models\Tournament;
use App\Models\TournamentTeam;
use App\Models\TournamentTeamMember;
use App\Models\User;
use Illuminate\Database\Seeder;

class TournamentSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create an owner user
        $owner = User::where('role', 'owner')->first()
            ?? User::where('role', 'superadmin')->first()
            ?? User::first();

        if (!$owner) {
            $this->command->warn('No users found. Skipping TournamentSeeder.');
            return;
        }

        // Get a court if available
        $court = Court::first();

        // Get some regular users for teams
        $users = User::where('id', '!=', $owner->id)->take(10)->get();

        // -------------------------
        // Tournament 1: Open round-robin futbol
        // -------------------------
        $tournament1 = Tournament::create([
            'name'                  => 'Copa AposPlay Verano 2026',
            'description'           => 'Torneo de fútbol amateur. Ideal para equipos de 5 a 7 jugadores. Disputado en formato liga.',
            'owner_id'              => $owner->id,
            'court_id'              => $court?->id,
            'sport_type'            => 'futbol',
            'format'                => TournamentFormat::ROUND_ROBIN->value,
            'max_teams'             => 8,
            'min_players'           => 5,
            'max_players'           => 7,
            'entry_fee'             => 5000.00,
            'prize_description'     => 'Trofeo + $50.000 al equipo campeón',
            'registration_deadline' => now()->addDays(14),
            'starts_at'             => now()->addDays(21)->toDateString(),
            'ends_at'               => now()->addDays(60)->toDateString(),
            'status'                => TournamentStatus::OPEN->value,
        ]);

        // Add 3 teams to tournament 1
        $teamNames = ['Los Halcones', 'Rayo Verde', 'Estrellas FC'];
        foreach ($teamNames as $i => $teamName) {
            $captain = $users->get($i * 2) ?? $owner;

            $team = TournamentTeam::create([
                'tournament_id'  => $tournament1->id,
                'name'           => $teamName,
                'captain_id'     => $captain->id,
                'payment_status' => $i === 0 ? TournamentTeamPaymentStatus::PAID->value : TournamentTeamPaymentStatus::PENDING->value,
                'amount_paid'    => $i === 0 ? 5000.00 : null,
                'payment_id'     => $i === 0 ? 'test-payment-' . $captain->id : null,
            ]);

            // Add captain as member
            TournamentTeamMember::create([
                'team_id'    => $team->id,
                'user_id'    => $captain->id,
                'is_captain' => true,
            ]);

            // Add a second member if available
            $secondUser = $users->get($i * 2 + 1);
            if ($secondUser && $secondUser->id !== $captain->id) {
                TournamentTeamMember::create([
                    'team_id'    => $team->id,
                    'user_id'    => $secondUser->id,
                    'is_captain' => false,
                ]);
            }
        }

        // -------------------------
        // Tournament 2: Draft single-elimination padel
        // -------------------------
        Tournament::create([
            'name'                  => 'Torneo Pádel Primavera 2026',
            'description'           => 'Torneo de pádel en formato eliminación directa. Máximo 4 equipos (parejas).',
            'owner_id'              => $owner->id,
            'court_id'              => $court?->id,
            'sport_type'            => 'padel',
            'format'                => TournamentFormat::SINGLE_ELIMINATION->value,
            'max_teams'             => 4,
            'min_players'           => 2,
            'max_players'           => 2,
            'entry_fee'             => 3000.00,
            'prize_description'     => 'Trofeo personalizado para los ganadores',
            'registration_deadline' => now()->addDays(30),
            'starts_at'             => now()->addDays(35)->toDateString(),
            'ends_at'               => null,
            'status'                => TournamentStatus::DRAFT->value,
        ]);

        $this->command->info('TournamentSeeder: Created 2 tournaments with sample teams.');
    }
}
