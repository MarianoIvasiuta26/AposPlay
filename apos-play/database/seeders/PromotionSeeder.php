<?php

namespace Database\Seeders;

use App\Enums\PromotionType;
use App\Models\CourtsXAdmin;
use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Generando promociones de ejemplo...');

        $admin = CourtsXAdmin::first();
        if (!$admin) {
            $this->command->warn('No hay administradores de canchas. Saltando seeder de promociones.');
            return;
        }

        Promotion::create([
            'name' => 'Combo Verano 2x1',
            'type' => PromotionType::COMBO,
            'discount_value' => 50,
            'conditions' => ['min_hours' => 2],
            'starts_at' => now(),
            'ends_at' => now()->addMonths(2),
            'is_active' => true,
            'created_by' => $admin->user_id,
        ]);

        Promotion::create([
            'name' => 'Puntos Extra Fin de Semana',
            'type' => PromotionType::EXTRA_POINTS,
            'discount_value' => 0,
            'points_bonus' => 10,
            'conditions' => ['days' => [5, 6]],
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'is_active' => true,
            'created_by' => $admin->user_id,
        ]);

        Promotion::create([
            'name' => 'Cupón Bienvenida',
            'type' => PromotionType::COUPON,
            'discount_value' => 20,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->subWeek(),
            'is_active' => false,
            'created_by' => $admin->user_id,
        ]);

        $this->command->info('Se crearon 3 promociones de ejemplo.');
    }
}
