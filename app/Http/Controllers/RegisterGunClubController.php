<?php

namespace App\Http\Controllers;

use App\Mail\ClubOnboardingMail;
use App\Models\Gunclub;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class RegisterGunClubController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('GunClubRegister',);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:gunclubs,name',
            'address' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_no' => 'required|string|max:255',
            'email_address' => 'required|email|max:255',
            'logo' => 'required|file|mimes:jpeg,jpg,png|max:5120',
        ]);

        // Save the gunclub first without the logo
        $gunclub = Gunclub::create([
            'name' => Str::lower(trim($validated['name'])),
            'address' => Str::lower(trim($validated['address'])),
            'contact_person' => Str::lower(trim($validated['contact_person'])),
            'contact_no' => $validated['contact_no'],
            'email_address' => Str::lower(trim($validated['email_address'])),
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $gunclubId = Str::slug($gunclub->id);
            $extension = $request->file('logo')->getClientOriginalExtension();
            $filename = "{$gunclubId}.{$extension}";
            $path = $request->file('logo')->storeAs('gunclubs', $filename, 's3');

            // Update the gunclub with the logo path
            $gunclub->update(['logo' => $path]);
        }

        $data = [
            'name' => Str::upper($gunclub->name),
        ];

        Mail::mailer('smtp-noreply')->to($gunclub->email_address)->send(new ClubOnboardingMail($data));

        return redirect()->back()->with('success', 'Gun Club created successfully.');
    }

    public function update(Request $request, Gunclub $gunclub): RedirectResponse
    {
        $validated = $request->validate([
        'name' => 'required|string|exists:gunclubs,name',
        'address' => 'nullable|string|max:255',
        'contact_person' => 'nullable|string|max:255',
        'contact_no' => 'nullable|string|max:255',
        'email_address' => 'required|email',
        'photo' => 'required|file|mimes:jpeg,jpg,png|max:5000',
        ]);

        $gunclub->update($validated);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
