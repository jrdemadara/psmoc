<?php

namespace App\Http\Controllers;

use App\Models\Gunclub;
use App\Models\Profile;
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

        return Inertia::render('Identity', [
            'profile' => [
                ...$profile->only([
                    'id', 'first_name', 'last_name', 'email', 'phone', // etc
                ]),
                'main_gunclub' => optional($profile->mainGunclub?->gunclub)->only(['id', 'name']),
                'rankings' => $profile->rankings->map(function ($ranking) {
                    return [
                        'id' => $ranking->id,
                        'match' => $ranking->match->name ?? null,
                        'division' => $ranking->division->name ?? null,
                        'category' => $ranking->category->name ?? null,
                        'score' => $ranking->score,
                    ];
                }),
            ]
        ]);

    }
}
