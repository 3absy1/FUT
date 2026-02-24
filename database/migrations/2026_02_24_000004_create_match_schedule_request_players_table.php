<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_schedule_request_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_schedule_request_id')
                ->constrained('match_schedule_requests')
                ->cascadeOnDelete();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('role')->default('player'); // captain, player
            $table->string('source')->default('club'); // club, friends, self

            $table->timestamps();

            $table->unique(
                ['match_schedule_request_id', 'user_id'],
                'msr_players_req_user_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_schedule_request_players');
    }
};

