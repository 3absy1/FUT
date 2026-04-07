<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function convertTable(string $table): void
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'name')) {
            return;
        }

        if (!Schema::hasColumn($table, 'name_json')) {
            Schema::table($table, function (Blueprint $tableBlueprint) {
                $tableBlueprint->json('name_json')->nullable()->after('name');
            });
        }

        $hasNameI18n = Schema::hasColumn($table, 'name_i18n');

        DB::table($table)
            ->select(array_merge(['id', 'name'], $hasNameI18n ? ['name_i18n'] : []))
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($table, $hasNameI18n) {
                foreach ($rows as $row) {
                    $existing = null;
                    if ($hasNameI18n && isset($row->name_i18n) && $row->name_i18n !== null) {
                        $decoded = is_string($row->name_i18n) ? json_decode($row->name_i18n, true) : $row->name_i18n;
                        $existing = is_array($decoded) ? $decoded : null;
                    }

                    $fallback = is_string($row->name) ? $row->name : '';
                    $translations = $existing ?: ['en' => $fallback, 'ar' => $fallback];

                    DB::table($table)
                        ->where('id', $row->id)
                        ->update(['name_json' => json_encode($translations, JSON_UNESCAPED_UNICODE)]);
                }
            });

        if (Schema::hasColumn($table, 'name') || ($hasNameI18n && Schema::hasColumn($table, 'name_i18n'))) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($table, $hasNameI18n) {
                if (Schema::hasColumn($table, 'name')) {
                    $tableBlueprint->dropColumn('name');
                }
                if ($hasNameI18n && Schema::hasColumn($table, 'name_i18n')) {
                    $tableBlueprint->dropColumn('name_i18n');
                }
            });
        }

        Schema::table($table, function (Blueprint $tableBlueprint) {
            $tableBlueprint->renameColumn('name_json', 'name');
        });
    }

    public function up(): void
    {
        $this->convertTable('areas');
        $this->convertTable('levels');
        $this->convertTable('tournaments');
    }

    public function down(): void
    {
        // Best-effort rollback: recreate string name + optional name_i18n, and fill name from translations.
        foreach (['areas', 'levels', 'tournaments'] as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'name')) {
                continue;
            }

            Schema::table($table, function (Blueprint $tableBlueprint) {
                $tableBlueprint->string('name_string')->nullable()->after('name');
                $tableBlueprint->json('name_i18n')->nullable()->after('name_string');
            });

            DB::table($table)
                ->select(['id', 'name'])
                ->orderBy('id')
                ->chunkById(200, function ($rows) use ($table) {
                    foreach ($rows as $row) {
                        $decoded = is_string($row->name) ? json_decode($row->name, true) : $row->name;
                        $translations = is_array($decoded) ? $decoded : [];
                        $fallback = $translations['en'] ?? $translations['ar'] ?? null;

                        DB::table($table)
                            ->where('id', $row->id)
                            ->update([
                                'name_string' => $fallback,
                                'name_i18n' => json_encode($translations, JSON_UNESCAPED_UNICODE),
                            ]);
                    }
                });

            Schema::table($table, function (Blueprint $tableBlueprint) {
                $tableBlueprint->dropColumn('name');
                $tableBlueprint->renameColumn('name_string', 'name');
            });
        }
    }
};

