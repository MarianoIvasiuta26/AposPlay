<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Drop foreign keys that might rely on the index
            $table->dropForeign(['court_id']);
            $table->dropForeign(['schedule_id']);

            // Now drop the unique index
            $table->dropUnique('reservations_court_id_schedule_id_reservation_date_unique');

            // Add a new unique index that includes start_time to prevent double booking the exact same slot
            $table->unique(['court_id', 'reservation_date', 'start_time']);

            // Re-add foreign keys
            $table->foreign('court_id')->references('id')->on('courts')->onDelete('cascade');
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['court_id']);
            $table->dropForeign(['schedule_id']);

            $table->dropUnique(['court_id', 'reservation_date', 'start_time']);

            $table->unique(['court_id', 'schedule_id', 'reservation_date'], 'reservations_court_id_schedule_id_reservation_date_unique');

            $table->foreign('court_id')->references('id')->on('courts')->onDelete('cascade');
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
        });
    }
};
