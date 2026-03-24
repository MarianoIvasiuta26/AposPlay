<?php

namespace Database\Seeders;

use App\Enums\CouponType;
use App\Enums\ReservationStatus;
use App\Enums\TournamentFormat;
use App\Enums\TournamentMatchStatus;
use App\Enums\TournamentStatus;
use App\Enums\TournamentTeamPaymentStatus;
use App\Enums\UserRole;
use App\Models\Complex;
use App\Models\Court;
use App\Models\CourtAddress;
use App\Models\Dia;
use App\Models\Reservation;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\TournamentTeam;
use App\Models\TournamentTeamMember;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    // Días de la semana: Carbon dayOfWeek (0=Dom…6=Sáb) → nombre en tabla `dias`
    private const DAY_MAP = [
        0 => 'Domingo',
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
    ];

    public function run(): void
    {
        $now = now('America/Argentina/Buenos_Aires');

        // ─────────────────────────────────────────────
        // 1. OWNERS
        // ─────────────────────────────────────────────
        $ownerData = [
            ['name' => 'Carlos Mendez',  'email' => 'carlos@aposplay.dev'],
            ['name' => 'Laura Gimenez',  'email' => 'laura@aposplay.dev'],
            ['name' => 'Roberto Silva',  'email' => 'roberto@aposplay.dev'],
            ['name' => 'Valeria Torres', 'email' => 'valeria@aposplay.dev'],
            ['name' => 'Diego Leiva',    'email' => 'diego@aposplay.dev'],
            ['name' => 'Marcela Ruiz',   'email' => 'marcela@aposplay.dev'],
        ];

        $owners = collect($ownerData)->map(fn ($d) => User::firstOrCreate(
            ['email' => $d['email']],
            ['name' => $d['name'], 'password' => bcrypt('Owner@123'), 'role' => UserRole::OWNER]
        ));

        // ─────────────────────────────────────────────
        // 2. STAFF (2 por complejo)
        // ─────────────────────────────────────────────
        $staffData = [
            ['name' => 'Martin Acosta',   'email' => 'martin.staff@aposplay.dev'],
            ['name' => 'Ana Bravo',        'email' => 'ana.staff@aposplay.dev'],
            ['name' => 'Luis Ferreira',    'email' => 'luis.staff@aposplay.dev'],
            ['name' => 'Claudia Paz',      'email' => 'claudia.staff@aposplay.dev'],
            ['name' => 'Hernan Medina',    'email' => 'hernan.staff@aposplay.dev'],
            ['name' => 'Sandra Vera',      'email' => 'sandra.staff@aposplay.dev'],
            ['name' => 'Pablo Quiroga',    'email' => 'pablo.staff@aposplay.dev'],
            ['name' => 'Norma Casco',      'email' => 'norma.staff@aposplay.dev'],
            ['name' => 'Gustavo Pinto',    'email' => 'gustavo.staff@aposplay.dev'],
            ['name' => 'Alicia Romero',    'email' => 'alicia.staff@aposplay.dev'],
            ['name' => 'Federico Suarez',  'email' => 'federico.staff@aposplay.dev'],
            ['name' => 'Miriam Godoy',     'email' => 'miriam.staff@aposplay.dev'],
        ];

        $staffUsers = collect($staffData)->map(fn ($d) => User::firstOrCreate(
            ['email' => $d['email']],
            ['name' => $d['name'], 'password' => bcrypt('Staff@123'), 'role' => UserRole::STAFF]
        ));

        // ─────────────────────────────────────────────
        // 3. JUGADORES REGULARES
        // ─────────────────────────────────────────────
        $playerData = [
            ['name' => 'Juan Perez',      'email' => 'juan@aposplay.dev'],
            ['name' => 'Maria Lopez',     'email' => 'maria@aposplay.dev'],
            ['name' => 'Nicolas Garcia',  'email' => 'nicolas@aposplay.dev'],
            ['name' => 'Sofia Martinez',  'email' => 'sofia@aposplay.dev'],
            ['name' => 'Emiliano Diaz',   'email' => 'emiliano@aposplay.dev'],
            ['name' => 'Camila Ibarra',   'email' => 'camila@aposplay.dev'],
            ['name' => 'Lucas Rojas',     'email' => 'lucas@aposplay.dev'],
            ['name' => 'Valentina Mora',  'email' => 'valentina@aposplay.dev'],
            ['name' => 'Agustin Sosa',    'email' => 'agustin@aposplay.dev'],
            ['name' => 'Lucia Herrera',   'email' => 'lucia@aposplay.dev'],
            ['name' => 'Matias Flores',   'email' => 'matias@aposplay.dev'],
            ['name' => 'Paola Castro',    'email' => 'paola@aposplay.dev'],
            ['name' => 'German Navarro',  'email' => 'german@aposplay.dev'],
            ['name' => 'Florencia Ramos', 'email' => 'florencia@aposplay.dev'],
            ['name' => 'Ezequiel Cruz',   'email' => 'ezequiel@aposplay.dev'],
        ];

        $players = collect($playerData)->map(fn ($d) => User::firstOrCreate(
            ['email' => $d['email']],
            ['name' => $d['name'], 'password' => bcrypt('User@123'), 'role' => UserRole::USER]
        ));

        // El usuario "cliente" del seeder base también se usa para notificaciones
        $clienteUser = User::where('email', 'cliente@aposplay.dev')->first();

        // ─────────────────────────────────────────────
        // 4. COMPLEJOS + CANCHAS
        // ─────────────────────────────────────────────
        $complexData = [
            [
                'name'    => 'Complejo Deportivo El Progreso',
                'address' => 'Av. San Martín 450, Apóstoles, Misiones',
                'street'  => 'Av. San Martín',
                'courts'  => [
                    ['name' => 'Cancha 1 - Fútbol 5',  'type' => 'futbol', 'price' => 28000, 'players' => 10],
                    ['name' => 'Cancha 2 - Fútbol 5',  'type' => 'futbol', 'price' => 28000, 'players' => 10],
                    ['name' => 'Cancha Pádel A',        'type' => 'padel',  'price' => 32000, 'players' => 4],
                ],
            ],
            [
                'name'    => 'Complejo La Amistad',
                'address' => 'Calle Belgrano 1200, Apóstoles, Misiones',
                'street'  => 'Belgrano',
                'courts'  => [
                    ['name' => 'Fútbol Central',        'type' => 'futbol', 'price' => 25000, 'players' => 10],
                    ['name' => 'Fútbol Norte',          'type' => 'futbol', 'price' => 25000, 'players' => 10],
                    ['name' => 'Pádel 1',               'type' => 'padel',  'price' => 30000, 'players' => 4],
                    ['name' => 'Pádel 2',               'type' => 'padel',  'price' => 30000, 'players' => 4],
                ],
            ],
            [
                'name'    => 'Club Atlético Apóstoles',
                'address' => 'Ruta 14 Km 3, Apóstoles, Misiones',
                'street'  => 'Ruta 14',
                'courts'  => [
                    ['name' => 'Fútbol Profesional',   'type' => 'futbol', 'price' => 35000, 'players' => 14],
                    ['name' => 'Fútbol Recreativo',    'type' => 'futbol', 'price' => 30000, 'players' => 10],
                    ['name' => 'Pádel Techado',        'type' => 'padel',  'price' => 40000, 'players' => 4],
                ],
            ],
            [
                'name'    => 'Complejo Don Bosco',
                'address' => 'Paraguay 88, Apóstoles, Misiones',
                'street'  => 'Paraguay',
                'courts'  => [
                    ['name' => 'Cancha de Fútbol',     'type' => 'futbol', 'price' => 27000, 'players' => 10],
                    ['name' => 'Pádel Don Bosco',      'type' => 'padel',  'price' => 33000, 'players' => 4],
                ],
            ],
            [
                'name'    => 'Complejo Los Pinos',
                'address' => 'Av. Libertad 750, Apóstoles, Misiones',
                'street'  => 'Av. Libertad',
                'courts'  => [
                    ['name' => 'Fútbol Los Pinos 1',  'type' => 'futbol', 'price' => 26000, 'players' => 10],
                    ['name' => 'Fútbol Los Pinos 2',  'type' => 'futbol', 'price' => 26000, 'players' => 10],
                    ['name' => 'Pádel Los Pinos',     'type' => 'padel',  'price' => 35000, 'players' => 4],
                ],
            ],
            [
                'name'    => 'Centro Deportivo Municipal',
                'address' => 'Mitre 300, Apóstoles, Misiones',
                'street'  => 'Mitre',
                'courts'  => [
                    ['name' => 'Fútbol Municipal A',   'type' => 'futbol', 'price' => 25000, 'players' => 10],
                    ['name' => 'Fútbol Municipal B',   'type' => 'futbol', 'price' => 25000, 'players' => 10],
                    ['name' => 'Pádel Municipal 1',    'type' => 'padel',  'price' => 30000, 'players' => 4],
                    ['name' => 'Pádel Municipal 2',    'type' => 'padel',  'price' => 30000, 'players' => 4],
                ],
            ],
        ];

        // Obtener IDs de días de la tabla dias
        $diasByName = Dia::all()->keyBy('nombre');

        $complexes = collect();
        $allCourts = collect();

        foreach ($complexData as $idx => $cd) {
            $owner = $owners->get($idx);

            $complex = Complex::firstOrCreate(
                ['name' => $cd['name']],
                ['owner_id' => $owner->id, 'address' => $cd['address'], 'active' => true]
            );

            // Asignar 2 staff por complejo
            $s1 = $staffUsers->get($idx * 2);
            $s2 = $staffUsers->get($idx * 2 + 1);
            if ($s1 && !$complex->staff()->where('users.id', $s1->id)->exists()) {
                $complex->staff()->attach($s1->id);
            }
            if ($s2 && !$complex->staff()->where('users.id', $s2->id)->exists()) {
                $complex->staff()->attach($s2->id);
            }

            foreach ($cd['courts'] as $cIdx => $courtInfo) {
                $address = CourtAddress::create([
                    'street'   => $cd['street'],
                    'number'   => 100 + ($cIdx * 10),
                    'city'     => 'Apostoles',
                    'province' => 'Misiones',
                    'country'  => 'Argentina',
                    'zip_code' => '3316',
                ]);

                $court = Court::firstOrCreate(
                    ['name' => $courtInfo['name'], 'complex_id' => $complex->id],
                    [
                        'price'            => $courtInfo['price'],
                        'type'             => $courtInfo['type'],
                        'number_players'   => $courtInfo['players'],
                        'court_address_id' => $address->id,
                    ]
                );

                // ── Horarios: todos los días, dos turnos ──────────────────
                // Fútbol: mañana 08-13, tarde 17-23
                // Pádel: mañana 09-13, tarde 16-22
                if ($courtInfo['type'] === 'futbol') {
                    $shift1Start = '08:00:00';
                    $shift1End   = '13:00:00';
                    $shift2Start = '17:00:00';
                    $shift2End   = '23:00:00';
                } else {
                    $shift1Start = '09:00:00';
                    $shift1End   = '13:00:00';
                    $shift2Start = '16:00:00';
                    $shift2End   = '22:00:00';
                }

                foreach (self::DAY_MAP as $dayName) {
                    $dia = $diasByName->get($dayName);
                    if (!$dia) {
                        continue;
                    }

                    // Domingos solo turno mañana
                    if ($dayName === 'Domingo') {
                        $s2Start = null;
                        $s2End   = null;
                    } else {
                        $s2Start = $shift2Start;
                        $s2End   = $shift2End;
                    }

                    DB::table('court_schedules')->insertOrIgnore([
                        'court_id'     => $court->id,
                        'day_id'       => $dia->id,
                        'start_time_1' => $shift1Start,
                        'end_time_1'   => $shift1End,
                        'start_time_2' => $s2Start,
                        'end_time_2'   => $s2End,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]);
                }

                $allCourts->push($court);
            }

            $complexes->push($complex);
        }

        // ─────────────────────────────────────────────
        // 5. RESERVAS — PASADAS (historial variado)
        // ─────────────────────────────────────────────
        $pastReservations = [
            // [player_idx, court_idx, days_ago, hour, status, payment_status, paid]
            [0,  0, 20, '10:00:00', ReservationStatus::CONFIRMED, 'paid',    true],
            [1,  1, 18, '18:00:00', ReservationStatus::CONFIRMED, 'paid',    true],
            [2,  2, 15, '09:00:00', ReservationStatus::CONFIRMED, 'paid',    true],
            [3,  3, 14, '17:00:00', ReservationStatus::CANCELLED, 'pending', false],
            [4,  4, 12, '10:00:00', ReservationStatus::CONFIRMED, 'paid',    true],
            [5,  5, 11, '18:00:00', ReservationStatus::CONFIRMED, 'paid',    true],
            [6,  6, 10, '11:00:00', ReservationStatus::CANCELLED, 'pending', false],
            [7,  7,  9, '17:00:00', ReservationStatus::CONFIRMED, 'paid',    true],
            [8,  8,  8, '20:00:00', ReservationStatus::CONFIRMED, 'paid',    true],
            [9,  9,  7, '09:00:00', ReservationStatus::CONFIRMED, 'paid',    true],
            [10, 10, 6, '17:00:00', ReservationStatus::CANCELLED, 'refunded', true],
            [11, 11, 5, '10:00:00', ReservationStatus::CONFIRMED, 'paid',    true],
            [12, 12, 4, '19:00:00', ReservationStatus::CONFIRMED, 'paid',    true],
            [13, 13, 3, '08:00:00', ReservationStatus::CONFIRMED, 'paid',    true],
            [14,  0, 2, '17:00:00', ReservationStatus::CONFIRMED, 'paid',    true],
            // cliente@aposplay.dev — reservas pasadas para historial
            [0,  1, 25, '09:00:00', ReservationStatus::CONFIRMED, 'paid',    true],
            [0,  2, 19, '20:00:00', ReservationStatus::CONFIRMED, 'paid',    true],
            [0,  3, 13, '10:00:00', ReservationStatus::CANCELLED, 'pending', false],
        ];

        foreach ($pastReservations as $r) {
            [$pIdx, $cIdx, $daysAgo, $hour, $status, $payStatus, $paid] = $r;
            $court  = $allCourts->get($cIdx % $allCourts->count());
            $player = $pIdx === 0 && isset($clienteUser) ? ($players->get(0) ?? $clienteUser) : ($players->get($pIdx) ?? $players->first());
            $date   = $now->copy()->subDays($daysAgo)->toDateString();
            $price  = $court->price;

            Reservation::firstOrCreate(
                ['court_id' => $court->id, 'reservation_date' => $date, 'start_time' => $hour],
                [
                    'user_id'        => $player->id,
                    'schedule_id'    => null,
                    'duration_hours' => 1,
                    'status'         => $status->value,
                    'payment_status' => $payStatus,
                    'payment_id'     => $paid ? 'demo-past-' . $court->id . '-' . $daysAgo : null,
                    'total_price'    => $price,
                    'final_price'    => $price,
                    'amount_paid'    => $paid ? $price : 0,
                    'points_redeemed' => 0,
                    'points_discount' => 0,
                ]
            );
        }

        // ─────────────────────────────────────────────
        // 6. RESERVAS — PRÓXIMOS 7 DÍAS (testing en vista)
        // ─────────────────────────────────────────────
        // Distintos estados visibles en la grilla de disponibilidad
        $futureReservations = [
            // Mañana: mix de pagada, pendiente de pago, pendiente
            [0,  0, 1, '09:00:00', ReservationStatus::CONFIRMED,       'paid',            true],
            [1,  0, 1, '10:00:00', ReservationStatus::PENDING_PAYMENT, 'pending',         false],
            [2,  1, 1, '17:00:00', ReservationStatus::CONFIRMED,       'paid',            true],
            [3,  1, 1, '18:00:00', ReservationStatus::PENDING,         'pending',         false],
            // Pasado mañana
            [4,  2, 2, '09:00:00', ReservationStatus::CONFIRMED,       'paid',            true],
            [5,  2, 2, '11:00:00', ReservationStatus::PENDING_PAYMENT, 'pending',         false],
            [6,  3, 2, '17:00:00', ReservationStatus::CONFIRMED,       'paid',            true],
            [7,  4, 2, '20:00:00', ReservationStatus::PENDING,         'pending',         false],
            // 3 días
            [8,  5, 3, '10:00:00', ReservationStatus::CONFIRMED,       'paid',            true],
            [9,  5, 3, '12:00:00', ReservationStatus::CONFIRMED,       'paid',            true],
            [10, 6, 3, '18:00:00', ReservationStatus::PENDING_PAYMENT, 'pending',         false],
            // 4 días
            [11, 7, 4, '09:00:00', ReservationStatus::CONFIRMED,       'paid',            true],
            [12, 8, 4, '17:00:00', ReservationStatus::CONFIRMED,       'paid',            true],
            // 5 días
            [13, 9, 5, '10:00:00', ReservationStatus::PENDING,         'pending',         false],
            [14, 10, 5, '11:00:00', ReservationStatus::CONFIRMED,      'paid',            true],
            // 6 días
            [0,  11, 6, '08:00:00', ReservationStatus::CONFIRMED,      'paid',            true],
            [1,  12, 6, '17:00:00', ReservationStatus::PENDING_PAYMENT,'pending',         false],
            // 7 días
            [2,  13, 7, '09:00:00', ReservationStatus::CONFIRMED,      'paid',            true],
            [3,  14, 7, '20:00:00', ReservationStatus::PENDING,        'pending',         false],
        ];

        foreach ($futureReservations as $r) {
            [$pIdx, $cIdx, $daysFromNow, $hour, $status, $payStatus, $paid] = $r;
            $court  = $allCourts->get($cIdx % $allCourts->count());
            $player = $players->get($pIdx) ?? $players->first();
            $date   = $now->copy()->addDays($daysFromNow)->toDateString();
            $price  = $court->price;

            Reservation::firstOrCreate(
                ['court_id' => $court->id, 'reservation_date' => $date, 'start_time' => $hour],
                [
                    'user_id'        => $player->id,
                    'schedule_id'    => null,
                    'duration_hours' => 1,
                    'status'         => $status->value,
                    'payment_status' => $payStatus,
                    'payment_id'     => $paid ? 'demo-future-' . $court->id . '-' . $daysFromNow : null,
                    'total_price'    => $price,
                    'final_price'    => $price,
                    'amount_paid'    => $paid ? $price : 0,
                    'points_redeemed' => 0,
                    'points_discount' => 0,
                ]
            );
        }

        // ── Reserva especial para cliente@aposplay.dev ───────────────────────
        // Mañana a las 21hs: CONFIRMED/PAID → para probar GameReminder
        if ($clienteUser) {
            $tomorrowCourt = $allCourts->first();
            Reservation::firstOrCreate(
                [
                    'court_id'         => $tomorrowCourt->id,
                    'reservation_date' => $now->copy()->addDay()->toDateString(),
                    'start_time'       => '21:00:00',
                ],
                [
                    'user_id'        => $clienteUser->id,
                    'schedule_id'    => null,
                    'duration_hours' => 1,
                    'status'         => ReservationStatus::CONFIRMED->value,
                    'payment_status' => 'paid',
                    'payment_id'     => 'demo-cliente-reminder',
                    'total_price'    => $tomorrowCourt->price,
                    'final_price'    => $tomorrowCourt->price,
                    'amount_paid'    => $tomorrowCourt->price,
                    'points_redeemed' => 0,
                    'points_discount' => 0,
                ]
            );

            // En 2 días a las 19hs: PENDING_PAYMENT → para probar recordatorio de pago pendiente
            $secondCourt = $allCourts->get(1) ?? $tomorrowCourt;
            Reservation::firstOrCreate(
                [
                    'court_id'         => $secondCourt->id,
                    'reservation_date' => $now->copy()->addDays(2)->toDateString(),
                    'start_time'       => '19:00:00',
                ],
                [
                    'user_id'        => $clienteUser->id,
                    'schedule_id'    => null,
                    'duration_hours' => 1,
                    'status'         => ReservationStatus::PENDING_PAYMENT->value,
                    'payment_status' => 'pending',
                    'total_price'    => $secondCourt->price,
                    'final_price'    => $secondCourt->price,
                    'amount_paid'    => 0,
                    'points_redeemed' => 0,
                    'points_discount' => 0,
                ]
            );

            // Ayer: CANCELLED → para probar CancellationNotification en historial
            $thirdCourt = $allCourts->get(2) ?? $tomorrowCourt;
            Reservation::firstOrCreate(
                [
                    'court_id'         => $thirdCourt->id,
                    'reservation_date' => $now->copy()->subDay()->toDateString(),
                    'start_time'       => '18:00:00',
                ],
                [
                    'user_id'        => $clienteUser->id,
                    'schedule_id'    => null,
                    'duration_hours' => 1,
                    'status'         => ReservationStatus::CANCELLED->value,
                    'payment_status' => 'refunded',
                    'payment_id'     => 'demo-cliente-cancelled',
                    'total_price'    => $thirdCourt->price,
                    'final_price'    => $thirdCourt->price,
                    'amount_paid'    => $thirdCourt->price,
                    'points_redeemed' => 0,
                    'points_discount' => 0,
                ]
            );
        }

        // ─────────────────────────────────────────────
        // 7. TORNEOS
        // ─────────────────────────────────────────────
        $court0     = $allCourts->where('type', 'futbol')->values()->get(0);
        $court1     = $allCourts->where('type', 'futbol')->values()->get(2);
        $court2     = $allCourts->where('type', 'futbol')->values()->get(4);
        $padelCourt = $allCourts->where('type', 'padel')->values()->first();

        $tournamentsData = [
            // Finalizado
            [
                'name'                  => 'Liga Apertura Apóstoles 2025',
                'description'           => 'Torneo de fútbol 5 en formato liga. Se disputaron 4 equipos durante 3 rondas. Gran nivel competitivo.',
                'owner_id'              => $owners->get(0)->id,
                'court_id'              => $court0?->id,
                'sport_type'            => 'futbol',
                'format'                => TournamentFormat::ROUND_ROBIN->value,
                'max_teams'             => 8,
                'min_teams'             => 4,
                'min_players'           => 5,
                'max_players'           => 7,
                'entry_fee'             => 180000,
                'prize_description'     => 'Trofeo + $280.000 al equipo campeón y $120.000 al segundo',
                'registration_deadline' => $now->copy()->subDays(60),
                'starts_at'             => $now->copy()->subDays(50)->toDateString(),
                'ends_at'               => $now->copy()->subDays(10)->toDateString(),
                'status'                => TournamentStatus::FINISHED->value,
                'teams' => [
                    ['name' => 'Los Halcones',  'captain' => 0, 'members' => [0, 1, 2],   'paid' => true],
                    ['name' => 'Rayo Verde',    'captain' => 3, 'members' => [3, 4, 5],   'paid' => true],
                    ['name' => 'Estrellas FC',  'captain' => 6, 'members' => [6, 7, 8],   'paid' => true],
                    ['name' => 'Trueno Azul',   'captain' => 9, 'members' => [9, 10, 11], 'paid' => true],
                ],
                'matches' => [
                    ['home' => 0, 'away' => 1, 'home_score' => 3, 'away_score' => 2, 'round' => 1, 'status' => TournamentMatchStatus::COMPLETED],
                    ['home' => 2, 'away' => 3, 'home_score' => 1, 'away_score' => 1, 'round' => 1, 'status' => TournamentMatchStatus::COMPLETED],
                    ['home' => 0, 'away' => 2, 'home_score' => 2, 'away_score' => 0, 'round' => 2, 'status' => TournamentMatchStatus::COMPLETED],
                    ['home' => 1, 'away' => 3, 'home_score' => 4, 'away_score' => 2, 'round' => 2, 'status' => TournamentMatchStatus::COMPLETED],
                    ['home' => 0, 'away' => 3, 'home_score' => 2, 'away_score' => 1, 'round' => 3, 'status' => TournamentMatchStatus::COMPLETED],
                    ['home' => 1, 'away' => 2, 'home_score' => 3, 'away_score' => 0, 'round' => 3, 'status' => TournamentMatchStatus::COMPLETED],
                ],
            ],

            // En curso
            [
                'name'                  => 'Copa Verano AposPlay 2026',
                'description'           => 'Torneo de fútbol 5 actualmente en disputa. Formato liga con 6 equipos. No te pierdas los partidos.',
                'owner_id'              => $owners->get(1)->id,
                'court_id'              => $court1?->id,
                'sport_type'            => 'futbol',
                'format'                => TournamentFormat::ROUND_ROBIN->value,
                'max_teams'             => 8,
                'min_teams'             => 4,
                'min_players'           => 5,
                'max_players'           => 7,
                'entry_fee'             => 200000,
                'prize_description'     => 'Trofeo + $320.000 al campeón, $150.000 al subcampeón',
                'registration_deadline' => $now->copy()->subDays(10),
                'starts_at'             => $now->copy()->subDays(7)->toDateString(),
                'ends_at'               => $now->copy()->addDays(21)->toDateString(),
                'status'                => TournamentStatus::IN_PROGRESS->value,
                'teams' => [
                    ['name' => 'Atletico Norte',  'captain' => 0,  'members' => [0, 1, 2],   'paid' => true],
                    ['name' => 'Los Guerreros',   'captain' => 3,  'members' => [3, 4, 5],   'paid' => true],
                    ['name' => 'Fuerza Sur',      'captain' => 6,  'members' => [6, 7],      'paid' => true],
                    ['name' => 'Dream Team',      'captain' => 9,  'members' => [9, 10, 11], 'paid' => true],
                    ['name' => 'Los Invictos',    'captain' => 12, 'members' => [12, 13],    'paid' => true],
                    ['name' => 'Villa Deportiva', 'captain' => 14, 'members' => [14],        'paid' => true],
                ],
                'matches' => [
                    ['home' => 0, 'away' => 1, 'home_score' => 2, 'away_score' => 1, 'round' => 1, 'status' => TournamentMatchStatus::COMPLETED],
                    ['home' => 2, 'away' => 3, 'home_score' => 0, 'away_score' => 3, 'round' => 1, 'status' => TournamentMatchStatus::COMPLETED],
                    ['home' => 4, 'away' => 5, 'home_score' => 2, 'away_score' => 2, 'round' => 1, 'status' => TournamentMatchStatus::COMPLETED],
                    ['home' => 0, 'away' => 2, 'home_score' => null, 'away_score' => null, 'round' => 2, 'status' => TournamentMatchStatus::PENDING],
                    ['home' => 1, 'away' => 4, 'home_score' => null, 'away_score' => null, 'round' => 2, 'status' => TournamentMatchStatus::PENDING],
                    ['home' => 3, 'away' => 5, 'home_score' => null, 'away_score' => null, 'round' => 2, 'status' => TournamentMatchStatus::PENDING],
                ],
            ],

            // Inscripciones abiertas — eliminación directa fútbol
            [
                'name'                  => 'Torneo Eliminatorio Primavera 2026',
                'description'           => 'Torneo de eliminación directa para fútbol 5. ¡Inscribite con tu equipo!',
                'owner_id'              => $owners->get(2)->id,
                'court_id'              => $court2?->id,
                'sport_type'            => 'futbol',
                'format'                => TournamentFormat::SINGLE_ELIMINATION->value,
                'max_teams'             => 8,
                'min_teams'             => 4,
                'min_players'           => 5,
                'max_players'           => 7,
                'entry_fee'             => 150000,
                'prize_description'     => '$250.000 al campeón + trofeo personalizado',
                'registration_deadline' => $now->copy()->addDays(21),
                'starts_at'             => $now->copy()->addDays(28)->toDateString(),
                'ends_at'               => $now->copy()->addDays(56)->toDateString(),
                'status'                => TournamentStatus::OPEN->value,
                'teams' => [
                    ['name' => 'Campeones del Barrio', 'captain' => 0, 'members' => [0, 1, 2], 'paid' => true],
                    ['name' => 'Los Tigres',           'captain' => 3, 'members' => [3, 4],    'paid' => true],
                    ['name' => 'Resistencia FC',       'captain' => 6, 'members' => [6],       'paid' => false],
                ],
                'matches' => [],
            ],

            // Inscripciones abiertas — pádel
            [
                'name'                  => 'Copa Pádel Apóstoles 2026',
                'description'           => 'Primer torneo de pádel amateur de Apóstoles. Parejas mixtas bienvenidas.',
                'owner_id'              => $owners->get(3)->id,
                'court_id'              => $padelCourt?->id,
                'sport_type'            => 'padel',
                'format'                => TournamentFormat::ROUND_ROBIN->value,
                'max_teams'             => 8,
                'min_teams'             => 4,
                'min_players'           => 2,
                'max_players'           => 2,
                'entry_fee'             => 170000,
                'prize_description'     => 'Trofeo + $270.000 para la pareja ganadora',
                'registration_deadline' => $now->copy()->addDays(14),
                'starts_at'             => $now->copy()->addDays(21)->toDateString(),
                'ends_at'               => $now->copy()->addDays(42)->toDateString(),
                'status'                => TournamentStatus::OPEN->value,
                'teams' => [
                    ['name' => 'Viento y Raqueta', 'captain' => 1, 'members' => [1, 2], 'paid' => true],
                    ['name' => 'Smash Brothers',   'captain' => 4, 'members' => [4, 5], 'paid' => true],
                ],
                'matches' => [],
            ],

            // Borrador
            [
                'name'                  => 'Torneo Otoño Fútbol 2026',
                'description'           => 'Próximamente. Torneo de fútbol en formato liga para el otoño.',
                'owner_id'              => $owners->get(4)->id,
                'court_id'              => null,
                'sport_type'            => 'futbol',
                'format'                => TournamentFormat::ROUND_ROBIN->value,
                'max_teams'             => 8,
                'min_teams'             => 4,
                'min_players'           => 5,
                'max_players'           => 7,
                'entry_fee'             => 220000,
                'prize_description'     => 'Trofeo + $350.000 al campeón',
                'registration_deadline' => $now->copy()->addDays(60),
                'starts_at'             => $now->copy()->addDays(70)->toDateString(),
                'ends_at'               => $now->copy()->addDays(100)->toDateString(),
                'status'                => TournamentStatus::DRAFT->value,
                'teams'   => [],
                'matches' => [],
            ],

            // Cancelado
            [
                'name'                  => 'Torneo Invierno 2025 (Cancelado)',
                'description'           => 'Torneo cancelado por falta de equipos inscriptos.',
                'owner_id'              => $owners->get(5)->id,
                'court_id'              => null,
                'sport_type'            => 'futbol',
                'format'                => TournamentFormat::ROUND_ROBIN->value,
                'max_teams'             => 8,
                'min_teams'             => 4,
                'min_players'           => 5,
                'max_players'           => 7,
                'entry_fee'             => 160000,
                'prize_description'     => '$260.000 al campeón',
                'registration_deadline' => $now->copy()->subDays(90),
                'starts_at'             => $now->copy()->subDays(80)->toDateString(),
                'ends_at'               => null,
                'status'                => TournamentStatus::CANCELLED->value,
                'teams'   => [],
                'matches' => [],
            ],
        ];

        foreach ($tournamentsData as $td) {
            $teamDefs  = $td['teams'];
            $matchDefs = $td['matches'];
            unset($td['teams'], $td['matches']);

            $tournament = Tournament::firstOrCreate(['name' => $td['name']], $td);

            $createdTeams = [];
            foreach ($teamDefs as $ti => $teamDef) {
                $captain = $players->get($teamDef['captain']);
                if (!$captain) {
                    continue;
                }

                $team = TournamentTeam::firstOrCreate(
                    ['tournament_id' => $tournament->id, 'name' => $teamDef['name']],
                    [
                        'captain_id'     => $captain->id,
                        'payment_status' => $teamDef['paid']
                            ? TournamentTeamPaymentStatus::PAID->value
                            : TournamentTeamPaymentStatus::PENDING->value,
                        'amount_paid' => $teamDef['paid'] ? $td['entry_fee'] : null,
                        'payment_id'  => $teamDef['paid'] ? 'demo-team-' . $tournament->id . '-' . $ti : null,
                    ]
                );
                $createdTeams[] = $team;

                TournamentTeamMember::firstOrCreate(
                    ['team_id' => $team->id, 'user_id' => $captain->id],
                    ['is_captain' => true]
                );

                foreach ($teamDef['members'] as $mIdx) {
                    $member = $players->get($mIdx);
                    if ($member && $member->id !== $captain->id) {
                        TournamentTeamMember::firstOrCreate(
                            ['team_id' => $team->id, 'user_id' => $member->id],
                            ['is_captain' => false]
                        );
                    }
                }
            }

            foreach ($matchDefs as $md) {
                $homeTeam = $createdTeams[$md['home']] ?? null;
                $awayTeam = $createdTeams[$md['away']] ?? null;
                if (!$homeTeam || !$awayTeam) {
                    continue;
                }

                TournamentMatch::firstOrCreate(
                    [
                        'tournament_id' => $tournament->id,
                        'home_team_id'  => $homeTeam->id,
                        'away_team_id'  => $awayTeam->id,
                        'round'         => $md['round'],
                    ],
                    [
                        'home_score'   => $md['home_score'],
                        'away_score'   => $md['away_score'],
                        'status'       => $md['status']->value,
                        'scheduled_at' => $now->copy()->parse($td['starts_at'])->addDays(($md['round'] - 1) * 7),
                    ]
                );
            }
        }

        // ─────────────────────────────────────────────
        // 8. CUPÓN DE DEMO para probar CouponAssigned
        // ─────────────────────────────────────────────
        $adminUser = User::where('email', 'admin@aposplay.dev')->first();
        if ($adminUser) {
            \App\Models\Coupon::firstOrCreate(
                ['code' => 'DEMO-BIENVENIDA'],
                [
                    'description' => 'Cupón de bienvenida para nuevos usuarios. 20% de descuento en tu próxima reserva.',
                    'type'        => CouponType::PERCENTAGE,
                    'value'       => 20,
                    'max_uses'    => 100,
                    'times_used'  => 0,
                    'valid_from'  => now(),
                    'valid_until' => now()->addMonths(3),
                    'is_active'   => true,
                    'created_by'  => $adminUser->id,
                ]
            );
        }

        $this->command->info('DemoSeeder: ✓ 6 complejos | 20 canchas con horarios | 6 torneos | 15 jugadores | 12 staff | 6 owners | reservas pasadas y futuras.');
        $this->command->info('');
        $this->command->info('Usuarios para probar notificaciones:');
        $this->command->info('  cliente@aposplay.dev / Cliente@123  → reserva CONFIRMADA mañana (GameReminder)');
        $this->command->info('  cliente@aposplay.dev / Cliente@123  → reserva PENDING_PAYMENT en 2 días');
        $this->command->info('  cliente@aposplay.dev / Cliente@123  → reserva CANCELADA ayer (historial)');
        $this->command->info('');
        $this->command->info('Para disparar GameReminder manualmente:');
        $this->command->info('  php artisan reminders:send');
    }
}
