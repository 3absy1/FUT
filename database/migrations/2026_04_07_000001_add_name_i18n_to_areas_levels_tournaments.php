<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->json('name_i18n')->nullable()->after('name');
        });

        Schema::table('levels', function (Blueprint $table) {
            $table->json('name_i18n')->nullable()->after('name');
        });

        Schema::table('tournaments', function (Blueprint $table) {
            $table->json('name_i18n')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->dropColumn('name_i18n');
        });

        Schema::table('levels', function (Blueprint $table) {
            $table->dropColumn('name_i18n');
        });

        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn('name_i18n');
        });
    }
};

