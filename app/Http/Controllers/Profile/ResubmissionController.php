<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;
use Inertia\Response;

class ResubmissionController extends Controller
{
    public function index(string $token): Response
    {

        // Get stored values from Redis hash
        $data = Redis::get("resubmission_token:{$token}");

        if (! $data) {
            abort(403, 'Invalid or expired link.');
        }

        $data = is_string($data) ? json_decode($data, true) : $data;

        $qrcode = $data['qrcode'] ?? null;
        $fields = $data['fields'] ?? [];

        if (! $qrcode || empty($fields)) {
            abort(403, 'Invalid or expired link.');
        }

        // Decode fields JSON back into array
        $qrcode = $data['qrcode'] ?? null;
        $fields = [];

        if (isset($data['fields'])) {
            if (is_string($data['fields'])) {
                $fields = json_decode($data['fields'], true) ?? [];
            } elseif (is_array($data['fields'])) {
                $fields = $data['fields'];
            }
        }

        $profile = Profile::where('qrcode', $qrcode)
            ->where('status', '=', 'on-hold')
            ->first();

        if (! $profile) {
            abort(404, 'Profile not found.');
        }

        // Only return requested fields
        $filteredProfile = collect($profile)->only($fields);

        return Inertia::render('Resubmission', [
            'token'   => $token,
            'fields'  => $fields,
            'profile' => $filteredProfile,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
        'token' => 'required|string|max:255',
        'fields' => 'required|array|min:1',
        ]);

        $qrcode = Redis::get("resubmission_token:{$request->token}");

        if (! $qrcode) {
              abort(403, 'Invalid or expired link.');
        }

        $profile = Profile::where('qrcode', $qrcode)
            ->where('status', '!=', 'approved')
            ->first();


            return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
