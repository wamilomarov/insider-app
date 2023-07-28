<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->references('id')->on('seasons')->cascadeOnDelete();
            $table->foreignId('host_team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->foreignId('guest_team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->integer('host_team_score');
            $table->integer('guest_team_score');
            $table->integer('week');
            $table->foreignId('winner_id')->nullable()->references('id')->on('teams')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
