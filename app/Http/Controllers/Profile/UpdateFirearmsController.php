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

class UpdateFirearmsController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
        'token' => 'required|string|max:255',
        'firearms' => 'required|array|min:1',
        'firearms.*.id' => 'required|integer',
        'firearms.*.type' => 'required|string|max:255',
        'firearms.*.make' => 'required|string|max:255',
        'firearms.*.model' => 'required|string|max:255',
        'firearms.*.caliber' => 'required|string|max:50',
        'firearms.*.serial_no' => 'required|string|max:255',
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

        // Get all request IDs for comparison
        $firearmIdsFromRequest = collect($request->firearms)->pluck('id')->filter()->toArray();

        // Delete any firearms not in the request
        $profile->firearms()
            ->whereNotIn('id', $firearmIdsFromRequest)
            ->delete();

        foreach ($request->firearms as $index => $firearmData) {
            if (!empty($firearmData['id'])) {
                $profile->firearms()
                    ->where('id', $firearmData['id'])
                    ->update([
                        'type'      => $firearmData['type'] ?? null,
                        'make'      => $firearmData['make'] ?? null,
                        'model'     => $firearmData['model'] ?? null,
                        'caliber'   => $firearmData['caliber'] ?? null,
                        'serial_no' => $firearmData['serial_no'] ?? null,
                    ]);
            } else {
                $profile->firearms()->create([
                    'type'      => $firearmData['type'] ?? null,
                    'make'      => $firearmData['make'] ?? null,
                    'model'     => $firearmData['model'] ?? null,
                    'caliber'   => $firearmData['caliber'] ?? null,
                    'serial_no' => $firearmData['serial_no'] ?? null,
                ]);
            }
        }


        $data = [
            'section' => 'Firearms',
            'name' => Str::upper($profile->first_name),
        ];

        Mail::mailer('smtp-noreply')->to($profile->email)->send(new ProfileUpdateSuccessMail($data));

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
