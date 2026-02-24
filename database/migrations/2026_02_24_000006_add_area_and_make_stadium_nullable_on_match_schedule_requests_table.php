<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_schedule_requests', function (Blueprint $table) {
            $table->foreignId('area_id')
                ->nullable()
                ->after('club_id')
                ->constrained('areas')
                ->nullOnDelete();

            // We'll re-add this FK after altering nullability.
            $table->dropForeign(['stadium_id']);
        });

        DB::statement('ALTER TABLE match_schedule_requests MODIFY stadium_id BIGINT UNSIGNED NULL');

        Schema::table('match_schedule_requests', function (Blueprint $table) {
            $table->foreign('stadium_id')->references('id')->on('stadiums')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('match_schedule_requests', function (Blueprint $table) {
            $table->dropForeign(['stadium_id']);
        });

        DB::statement('ALTER TABLE match_schedule_requests MODIFY stadium_id BIGINT UNSIGNED NOT NULL');

        Schema::table('match_schedule_requests', function (Blueprint $table) {
            $table->foreign('stadium_id')->references('id')->on('stadiums')->cascadeOnDelete();
            $table->dropConstrainedForeignId('area_id');
        });
    }
};

