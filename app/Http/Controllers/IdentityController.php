<?php

namespace App\Http\Controllers;

use App\Models\Gunclub;
use App\Models\Profile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class IdentityController extends Controller
{
    public function index(Request $request): Response
    {

        $profile = Profile::with([
            'mainGunclub.gunclub',
            'rankings.match',
            'rankings.division',
            'rankings.category',
        ])->findOrFail($request->qrcode);

        // Generate temporary photo URL
        $photoUrl = $profile->photo
            ? Storage::temporaryUrl($profile->photo, Carbon::now()->addMinutes(20))
            : null;

        return Inertia::render('Identity', [
            'profile' => [
                ...$profile->only([
                    'first_name', 'last_name', 'email', 'phone',
                ]),
                'photo' => $photoUrl, // ✅ added here
                'main_gunclub' => optional($profile->mainGunclub?->gunclub)->only(['id', 'name']),
                'rankings' => $profile->rankings->map(fn($ranking) => [
                    'id' => $ranking->id,
                    'match' => $ranking->match->name ?? null,
                    'division' => $ranking->division->name ?? null,
                    'category' => $ranking->category->name ?? null,
                    'score' => $ranking->score,
                ]),
            ]
        ]);


    }
}
