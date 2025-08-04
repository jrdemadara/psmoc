<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    protected $fillable = [
        'sanction_id',
        'name',
        'description',
    ];

    public function sanction(): BelongsTo
    {
        return $this->belongsTo(Sanction::class);
    }

    public function rankings(): HasMany
    {
        return $this->hasMany(Ranking::class);
    }
}
