<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    // 🔗 Relationships
    public function rankings(): HasMany
    {
        return $this->hasMany(Ranking::class);
    }
}
