<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_schedule_requests', function (Blueprint $table) {
            $table->foreignId('requested_by_user_id')
                ->after('id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('team_source')->after('stadium_id')->default('club'); // club, friends
            $table->string('payment_status')->after('status')->default('unpaid'); // unpaid, paid
        });
    }

    public function down(): void
    {
        Schema::table('match_schedule_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('requested_by_user_id');
            $table->dropColumn(['team_source', 'payment_status']);
        });
    }
};

