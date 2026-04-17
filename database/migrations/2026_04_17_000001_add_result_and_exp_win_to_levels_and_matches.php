<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table
                ->enum('result', ['club_a', 'club_b', 'draw'])
                ->nullable()
                ->after('score_club_b');
        });

        Schema::table('levels', function (Blueprint $table) {
            $table->unsignedInteger('exp_win')->default(5)->after('checkpoints');
        });
    }

    public function down(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            $table->dropColumn('exp_win');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn('result');
        });
    }
};

