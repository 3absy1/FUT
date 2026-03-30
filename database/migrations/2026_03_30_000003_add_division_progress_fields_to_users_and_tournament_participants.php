<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table
                ->foreignId('division_id')
                ->nullable()
                ->after('exp')
                ->constrained('levels')
                ->nullOnDelete();

            $table->unsignedInteger('division_current_match')->default(0)->after('division_id');
            $table->unsignedInteger('division_last_checkpoint_match')->default(0)->after('division_current_match');
        });

        Schema::table('tournament_participants', function (Blueprint $table) {
            $table
                ->foreignId('division_id')
                ->nullable()
                ->after('club_id')
                ->constrained('levels')
                ->nullOnDelete();

            $table->unsignedInteger('current_match')->default(0)->after('division_id');
            $table->unsignedInteger('last_checkpoint_match')->default(0)->after('current_match');
        });
    }

    public function down(): void
    {
        Schema::table('tournament_participants', function (Blueprint $table) {
            $table->dropColumn(['current_match', 'last_checkpoint_match']);
            $table->dropConstrainedForeignId('division_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['division_current_match', 'division_last_checkpoint_match']);
            $table->dropConstrainedForeignId('division_id');
        });
    }
};

