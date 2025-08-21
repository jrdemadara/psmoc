<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;

use App\Mail\ProfileUpdateSuccessMail;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpdatePhotoSignatureController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required|string|max:255',
            'photo' => 'nullable|file|mimes:jpeg,jpg,png|max:5000',
            'signature' => 'nullable|file|mimes:png|max:5000',
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

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($profile->photo && Storage::exists($profile->photo)) {
                Storage::delete($profile->photo);
            }
            // Upload new photo
            $extension = $request->file('photo')->getClientOriginalExtension();
            $filename = "{$profile->id}.{$extension}";
            $photoPath = $request->file('photo')->storeAs('photos', $filename);

            if ($photoPath) {
                $profile->photo = $photoPath;
            }
        }

        if ($request->hasFile('signature')) {
            // Delete old signature if exists
            if ($profile->signature && Storage::exists($profile->signature)) {
                Storage::delete($profile->signature);
            }

            // Upload new signature
            $extension = $request->file('signature')->getClientOriginalExtension();
            $filename = "{$profile->id}.{$extension}";
            $signaturePath = $request->file('signature')->storeAs('signatures', $filename);

            if ($signaturePath) {
                $profile->signature = $signaturePath;
            }
        }

        $profile->save();

        $data = [
            'section' => 'Photo & Signature',
            'name' => Str::upper($profile->first_name),
        ];

        Mail::mailer('smtp-noreply')->to($profile->email)->send(new ProfileUpdateSuccessMail($data));

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
