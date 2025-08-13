<?php

namespace App\Http\Controllers;

use App\Mail\MemberProfileUpdateMail;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class RequestMemberUpdateController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('RequestMemberUpdate');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $profile = Profile::where('email', $request->email)
            ->where('status', 'pending')
            ->first();

        if ($profile) {
            $token = Str::random(64);
            Redis::setex("profile_update_token:{$token}", 86400, $profile->qrcode);
            $url = url("/update-member/{$token}");
            $data = [
                'url' => $url,
                'name' => Str::upper($profile->first_name),
            ];

            Mail::mailer('smtp-secureupdate')->to($request->email)->send(new MemberProfileUpdateMail($data));

            $status = true;
        } else {
            $status = false;
        }

        return back()->with('status', __($status));
    }
}
