<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'event_date',
        'event_name',
        'event_description',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];
}
