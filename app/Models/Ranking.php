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

    public function match(): BelongsTo
    {
        return $this->belongsTo(Matchches::class); // replace with your real model name
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
