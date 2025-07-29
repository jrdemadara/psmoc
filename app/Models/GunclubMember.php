<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GunclubMember extends Model
{
    protected $table = 'gunclub_members'; // or 'gunclub_profile' if that's your actual table name

    protected $fillable = [
        'profile_id',
        'gunclub_id',
        'is_main',
        'years_no',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function gunclub(): BelongsTo
    {
        return $this->belongsTo(Gunclub::class);
    }
}
