<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('area_id')->nullable()->after('wallet_balance')
                ->constrained('areas')->nullOnDelete();

            $table->boolean('is_stadium_owner')->default(false)->after('area_id');
            $table->foreignId('stadium_id')->nullable()->after('is_stadium_owner')
                ->constrained('stadiums')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('stadium_id');
            $table->dropColumn('is_stadium_owner');
            $table->dropConstrainedForeignId('area_id');
        });
    }
};

