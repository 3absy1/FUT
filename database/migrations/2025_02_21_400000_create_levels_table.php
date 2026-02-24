<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('min_points')->default(0);
            $table->unsignedInteger('max_points')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        DB::table('levels')->insert([
            ['name' => 'Bronze', 'min_points' => 0, 'max_points' => 999, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Silver', 'min_points' => 1000, 'max_points' => 2999, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Gold', 'min_points' => 3000, 'max_points' => 9999, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Platinum', 'min_points' => 10000, 'max_points' => null, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
