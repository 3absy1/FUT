<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_schedule_requests', function (Blueprint $table) {
            $table->foreignId('opponent_club_id')
                ->nullable()
                ->after('club_id')
                ->constrained('clubs')
                ->nullOnDelete();

            $table->foreignId('opponent_joined_by_user_id')
                ->nullable()
                ->after('opponent_club_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('matched_slot_id')
                ->nullable()
                ->after('requested_datetime')
                ->constrained('match_schedule_request_slots')
                ->nullOnDelete();

            $table->foreignId('match_id')
                ->nullable()
                ->after('stadium_id')
                ->constrained('matches')
                ->nullOnDelete();
        });

        Schema::table('match_schedule_request_players', function (Blueprint $table) {
            $table->string('team')->default('A')->after('user_id'); // A, B
        });
    }

    public function down(): void
    {
        Schema::table('match_schedule_request_players', function (Blueprint $table) {
            $table->dropColumn('team');
        });

        Schema::table('match_schedule_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('match_id');
            $table->dropConstrainedForeignId('matched_slot_id');
            $table->dropConstrainedForeignId('opponent_joined_by_user_id');
            $table->dropConstrainedForeignId('opponent_club_id');
        });
    }
};

