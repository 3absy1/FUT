<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = ['name', 'min_points', 'max_points', 'sort_order'];

    protected function casts(): array
    {
        return [
            'min_points' => 'integer',
            'max_points' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the level for a given exp amount (highest level where min_points <= exp).
     */
    public static function forExp(int $exp): ?self
    {
        return static::query()
            ->where('min_points', '<=', $exp)
            ->orderByDesc('min_points')
            ->first();
    }
}
