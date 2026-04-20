<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('position', ['attacker', 'defender', 'midfielder', 'goal_keeper'])
                ->nullable()
                ->after('rating');

            $table->unsignedTinyInteger('pac')->nullable()->after('position');
            $table->unsignedTinyInteger('sho')->nullable()->after('pac');
            $table->unsignedTinyInteger('pas')->nullable()->after('sho');
            $table->unsignedTinyInteger('dri')->nullable()->after('pas');
            $table->unsignedTinyInteger('def')->nullable()->after('dri');
            $table->unsignedTinyInteger('phy')->nullable()->after('def');

            $table->unsignedInteger('goals_scored')->default(0)->after('phy');
            $table->unsignedInteger('assists_count')->default(0)->after('goals_scored');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'position',
                'pac',
                'sho',
                'pas',
                'dri',
                'def',
                'phy',
                'goals_scored',
                'assists_count',
            ]);
        });
    }
};
