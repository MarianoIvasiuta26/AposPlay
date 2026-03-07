<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->integer('points_redeemed')->default(0)->after('discount_amount');
            $table->decimal('points_discount', 8, 2)->default(0)->after('points_redeemed');
            $table->decimal('final_price', 8, 2)->nullable()->after('points_discount');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['points_redeemed', 'points_discount', 'final_price']);
        });
    }
};
