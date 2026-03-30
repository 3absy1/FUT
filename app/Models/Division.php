<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $table = 'levels';

    protected $fillable = ['name', 'matches_count', 'checkpoints', 'sort_order'];

    protected function casts(): array
    {
        return [
            'matches_count' => 'integer',
            'checkpoints' => 'array',
            'sort_order' => 'integer',
        ];
    }
}

