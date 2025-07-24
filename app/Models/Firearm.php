<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Firearm extends Model
{
    protected $fillable = [
        'profile_id',
        'type',
        'make',
        'model',
        'caliber',
        'serial_no',
        'ltopf_no',
        'license_type',
    ];

    public function profile():BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
