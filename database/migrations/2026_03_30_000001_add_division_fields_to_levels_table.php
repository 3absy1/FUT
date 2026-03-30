<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            $table->unsignedInteger('matches_count')->default(0)->after('name');
            $table->json('checkpoints')->nullable()->after('matches_count');
        });
    }

    public function down(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            $table->dropColumn(['matches_count', 'checkpoints']);
        });
    }
};

