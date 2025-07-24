<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    protected $fillable = [
        'event_id',
        'profile_id',
    ];

    public function event():BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function profile():BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
