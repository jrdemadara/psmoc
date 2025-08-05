<?php

namespace App\Http\Controllers;

use App\Mail\MemberOnboardingMail;
use App\Models\Gunclub;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class UpdateMemberController extends Controller
{
    public function create(Request $request): Response
    {
        $profile = Profile::with([
            'gunclubMembers.gunclub',
            'firearms',
        ])
        ->where('qrcode', $request->qrcode)
        ->firstOrFail();

        // Map and append signed URLs

            $profile->photo = $profile->photo
                ? Storage::temporaryUrl($profile->photo, Carbon::now()->addMinutes(20))
                : null;

            $profile->signature = $profile->signature
                ? Storage::temporaryUrl($profile->signature, Carbon::now()->addMinutes(10))
                : null;


        dd($profile);

        return Inertia::render('settings.Profile', [
            'profile' => $profile,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
    }
}
