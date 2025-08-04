<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sanction extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    // 🔗 Relationships
    public function divisions():HasMany
    {
        return $this->hasMany(Division::class);
    }
}
