<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_schedule_request_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_schedule_request_id')
                ->constrained('match_schedule_requests')
                ->cascadeOnDelete();

            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_schedule_request_slots');
    }
};

