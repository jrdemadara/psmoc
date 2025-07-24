<?php

namespace App\Http\Controllers;

use App\Models\Gunclub;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class GunClubRegisterController extends Controller
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

        $gunclubName = Str::slug($validated['name']);
        $extension = $request->file('logo')->getClientOriginalExtension();
        $filename = "{$gunclubName}.{$extension}";

        $path = $request->file('logo')->storeAs('gunclubs', $filename);

        Gunclub::create([
            ...$validated,
            'logo' => $path,
        ]);

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
