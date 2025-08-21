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

class UpdateGunClubsController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
        'token' => 'required|string|max:255',
        'gunclubs' => 'required|array|min:1',
        'gunclubs.*.id' => 'required|integer',
        'gunclubs.*.gunclub_id' => 'required|exists:gunclubs,id',
        'gunclubs.*.years_no' => 'required|integer|min:0|max:100',
        'gunclubs.*.is_main' => 'required|boolean',
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

        // Get all request IDs for comparison
        $gunclubIdsFromRequest = collect($request->gunclubs)->pluck('id')->filter()->toArray();

        // Delete any gun clubs not in the request
        $profile->gunclubMembers()
            ->whereNotIn('id', $gunclubIdsFromRequest)
            ->delete();

        foreach ($request->gunclubs as $index => $gunclub) {
            if (!empty($gunclub['id'])) {
                $profile->gunclubMembers()
                    ->where('id', $gunclub['id'])
                    ->update([
                        'gunclub_id' => $gunclub['gunclub_id'],
                        'years_no'   => $gunclub['years_no'],
                        'is_main'    => $gunclub['is_main'],
                    ]);
            } else {
                $profile->gunclubMembers()->create([
                    'gunclub_id' => $gunclub['gunclub_id'],
                    'years_no'   => $gunclub['years_no'],
                    'is_main'    => $gunclub['is_main'],
                ]);
            }
        }

        $data = [
            'section' => 'Gun Clubs',
            'name' => Str::upper($profile->first_name),
        ];

        Mail::mailer('smtp-noreply')->to($profile->email)->send(new ProfileUpdateSuccessMail($data));

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
