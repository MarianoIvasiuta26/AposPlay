<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('court_id')->nullable()->constrained('courts')->nullOnDelete();
            $table->string('sport_type');
            $table->enum('format', ['round_robin', 'single_elimination']);
            $table->unsignedInteger('max_teams');
            $table->unsignedInteger('min_players')->default(1);
            $table->unsignedInteger('max_players')->default(10);
            $table->decimal('entry_fee', 10, 2)->default(0);
            $table->text('prize_description')->nullable();
            $table->dateTime('registration_deadline');
            $table->date('starts_at');
            $table->date('ends_at')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
