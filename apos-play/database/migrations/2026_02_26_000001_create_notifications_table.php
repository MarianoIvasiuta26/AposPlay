<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Evitar notificaciones duplicadas
            $table->unique(['notifiable_id', 'notifiable_type', 'type', 'created_at'], 'unique_notifications');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};