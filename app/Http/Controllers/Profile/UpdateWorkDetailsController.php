<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;

use App\Mail\ProfileUpdateSuccessMail;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class UpdateWorkDetailsController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
        'token' => 'required|string|max:255',
        'occupation' => 'required|string|max:255',
        'company_organization' => 'nullable|string|max:255',
        'position' => 'nullable|string|max:255',
        'office_business_address' => 'nullable|string|max:255',
        'office_landline' => 'nullable|string|max:20',
        'office_email' => 'nullable|email|max:255',
        ]);

        $qrcode = Redis::get("profile_update_token:{$request->token}");

        if (! $qrcode) {
              abort(403, 'Invalid or expired update link.');
        }

        $profile = Profile::where('qrcode', $qrcode)
            ->where('status', '!=', 'approved')
            ->first();

        if (! $profile) {
            abort(404, 'Profile not found.');
        }

        $profile->update([
            'occupation' => $request->occupation,
            'company_organization' => $request->company_organization,
            'position' => $request->position,
            'office_business_address' => $request->office_business_address,
            'office_landline' => $request->office_landline,
            'office_email' => $request->office_email,
        ]);

        $data = [
            'section' => 'Work Details',
            'name' => Str::upper($profile->first_name),
        ];

        Mail::mailer('smtp-noreply')->to($profile->email)->send(new ProfileUpdateSuccessMail($data));

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
