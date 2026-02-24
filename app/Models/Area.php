<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $fillable = ['name', 'coordinates'];

    public function clubs(): HasMany
    {
        return $this->hasMany(Club::class);
    }

    public function stadiums(): HasMany
    {
        return $this->hasMany(Stadium::class);
    }
}
