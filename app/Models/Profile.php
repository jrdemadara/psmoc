<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends Model
{
    protected $fillable = [
        'qrcode',
        'reg_type',
        'licensed_shooter',
        'application_venue',
        'ltopf_no',
        'license_type',
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
        'email',
        'phone',
        'region',
        'province',
        'city_municipality',
        'barangay',
        'street',
        'purok',
        'occupation',
        'company_organization',
        'position',
        'office_business_address',
        'office_landline',
        'office_email',
        'photo',
        'signature',
        'expiry_date',
    ];

    public function gunclubMembers(): HasMany
    {
        return $this->hasMany(GunclubMember::class);
    }

    public function firearms():HasMany
    {
        return $this->hasMany(Firearm::class);
    }
}
