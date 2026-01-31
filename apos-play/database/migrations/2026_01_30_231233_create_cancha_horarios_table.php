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
        Schema::create('cancha_horarios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cancha_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('dia_id')
                ->constrained('dias');

            $table->time('hora_apertura');
            $table->time('hora_cierre');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancha_horarios');
    }
};
