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

class UpdateApplicationDetailsController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
        'token' => 'required|string|max:255',
        'application_venue' => 'required|string|max:255',
        'licensed_shooter' => 'required|string|max:255',
        'ltopf_no' => 'required|string|max:255',
        'license_type' => 'required|string|max:255',
        ]);

        $qrcode = "";

        $data = $request->resubmit? Redis::get("resubmission_token:{$request->token}") : Redis::get("profile_update_token:{$request->token}");

        if ($request->resubmit) {
            $data = is_string($data) ? json_decode($data, true) : $data;
            $qrcode = $data['qrcode'] ?? null;
        } else {
            $qrcode = $data;
        }

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
            'application_venue' => Str::lower(trim($request->application_venue)),
            'licensed_shooter' => $request->licensed_shooter === 'Yes',
            'ltopf_no' =>  Str::lower(trim($request->ltopf_no)),
            'license_type' => Str::lower(trim($request->license_type)),
        ]);

        $data = [
            'section' => 'Application Details',
            'name' => Str::upper($profile->first_name),
        ];

        Mail::mailer('smtp-noreply')->to($profile->email)->send(new ProfileUpdateSuccessMail($data));

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
