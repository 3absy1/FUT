<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('friendships');
    }

    public function down(): void
    {
        // Intentionally left empty. Legacy table should not be recreated automatically.
    }
};

