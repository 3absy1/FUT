<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nick_name')->unique()->nullable()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->string('otp', 10)->nullable()->after('password');
            $table->dateTime('otp_expires_at')->nullable()->after('otp');
            $table->boolean('is_verified')->default(false)->after('otp_expires_at');
            $table->unsignedTinyInteger('age')->nullable()->after('is_verified');
            $table->float('rating')->default(0)->after('age');
            $table->decimal('wallet_balance', 12, 2)->default(0)->after('rating');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nick_name', 'phone', 'otp', 'otp_expires_at',
                'is_verified', 'age', 'rating', 'wallet_balance',
            ]);
        });
    }
};
