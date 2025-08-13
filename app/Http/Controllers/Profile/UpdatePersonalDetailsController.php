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

class UpdatePersonalDetailsController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
        'token' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'extension' => 'nullable|string|max:255',
        'birth_date' => 'required|date',
        'birth_place' => 'required|string|max:255',
        'age' => 'required|integer|min:0',
        'gender' => 'required|string|max:255',
        'civil_status' => 'required|string|max:255',
        'blood_type' => 'required|string|max:3',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:15',
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
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'extension' => $request->extension,
            'email' => $request->email,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'birth_place' => $request->birth_place,
            'age' => $request->age,
            'gender' => $request->gender,
            'civil_status' => $request->civil_status,
            'blood_type' => $request->blood_type,
        ]);

        $data = [
            'section' => 'Personal Details',
            'name' => Str::upper($profile->first_name),
        ];

        Mail::mailer('smtp-noreply')->to($profile->email)->send(new ProfileUpdateSuccessMail($data));

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
