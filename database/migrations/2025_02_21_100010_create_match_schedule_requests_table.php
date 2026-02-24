<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_schedule_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->dateTime('requested_datetime');
            $table->foreignId('stadium_id')->constrained('stadiums')->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamps();
            $table->dateTime('approved_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_schedule_requests');
    }
};
