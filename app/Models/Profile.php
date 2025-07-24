<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'reg_type',
        'application_venue',

        'last_name',
        'first_name',
        'middle_name',
        'extension',

        'birth_date',
        'birth_place',
        'age',
        'gender',

        'civil_status',
        'blood_type',

        'address1',
        'address2',
        'barangay',
        'city_municipality',
        'province',

        'occupation',
        'company_organization',
        'position',
        'office_business_address',
        'offile_landline',
        'office_email',

        'photo',
        'signature',
        'expiry_date',
    ];
}
