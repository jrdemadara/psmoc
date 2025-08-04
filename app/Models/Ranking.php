<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ranking extends Model
{
    protected $fillable = [
        'profile_id',
        'match_id',
        'division_id',
        'category_id',
        'score',
    ];

    // 🔗 Relationships
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
