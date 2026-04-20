<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('gk_diving')->nullable()->after('phy');
            $table->unsignedTinyInteger('gk_handling')->nullable()->after('gk_diving');
            $table->unsignedTinyInteger('gk_kicking')->nullable()->after('gk_handling');
            $table->unsignedTinyInteger('gk_reflexes')->nullable()->after('gk_kicking');
            $table->unsignedTinyInteger('gk_positioning')->nullable()->after('gk_reflexes');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'gk_diving',
                'gk_handling',
                'gk_kicking',
                'gk_reflexes',
                'gk_positioning',
            ]);
        });
    }
};
