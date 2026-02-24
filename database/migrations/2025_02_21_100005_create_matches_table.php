<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_a_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('club_b_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('stadium_id')->constrained('stadiums')->cascadeOnDelete();
            $table->dateTime('scheduled_datetime');
            $table->string('status')->default('pending'); // pending, scheduled, ongoing, completed, cancelled
            $table->unsignedInteger('score_club_a')->default(0);
            $table->unsignedInteger('score_club_b')->default(0);
            $table->foreignId('tournament_id')->nullable()->constrained('tournaments')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
