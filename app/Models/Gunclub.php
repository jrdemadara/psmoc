<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gunclub extends Model
{
    protected $fillable = [
        'name',
        'address',
        'contact_person',
        'contact_no',
        'email_address',
        'logo',
        'approved_by',
        'status',
    ];
}
