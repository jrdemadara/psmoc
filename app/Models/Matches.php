<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Matchches extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function rankings(): HasMany
    {
        return $this->hasMany(Ranking::class);
    }
}
