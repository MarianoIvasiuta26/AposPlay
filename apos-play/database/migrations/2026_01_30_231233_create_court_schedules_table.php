<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('court_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('court_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('day_id')
                ->constrained('dias');

            // Turno 1 (Principal)
            $table->time('start_time_1')->nullable();
            $table->time('end_time_1')->nullable();

            // Turno 2 (Secundario/Tarde)
            $table->time('start_time_2')->nullable();
            $table->time('end_time_2')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('court_schedules');
    }
};
