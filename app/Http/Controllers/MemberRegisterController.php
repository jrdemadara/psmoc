<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MemberRegisterController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('MemberRegister',);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
        'reg_type' => 'required|in:new,renewal',
        'application_venue' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'extension' => 'nullable|string|max:255',
        'birth_date' => 'required|date',
        'birth_place' => 'required|string|max:255',
        'age' => 'required|integer|min:0',
        'gender' => 'required|in:male,female,other',
        'civil_status' => 'required|string|max:255',
        'blood_type' => 'required|string|max:3',
        'street' => 'required|string|max:255',
        'purok' => 'required|string|max:255',
        'barangay' => 'required|string|max:255',
        'city_municipality' => 'required|string|max:255',
        'province' => 'required|string|max:255',
        'occupation' => 'required|string|max:255',
        'email' => 'required|email|exists:profiles,email',
        'phone' => 'required|integer|exists:profiles,phone',

        'company_organization' => 'nullable|string|max:255',
        'position' => 'nullable|string|max:255',
        'office_business_address' => 'nullable|string|max:255',
        'office_landline' => 'nullable|string|max:20',
        'office_email' => 'nullable|email|max:255',
        'photo' => 'required|file|mimes:jpeg,jpg,png|max:5000',
        'signature' => 'required|file|mimes:png|max:5000',
        ]);

        Profile::create($validated);

        return redirect()->back()->with('success', 'Profile created successfully.');
    }

    public function update(Request $request, Profile $profile): RedirectResponse
    {
        $validated = $request->validate([
            'reg_type' => 'required|in:new,renewal',
            'expiry_date' => 'required|date',
            'application_venue' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'extension' => 'nullable|string|max:255',
            'birth_date' => 'required|date',
            'birth_place' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'gender' => 'required|in:male,female,other',
            'civil_status' => 'required|string|max:255',
            'blood_type' => 'required|string|max:3',
            'street' => 'required|string|max:255',
            'purok' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'city_municipality' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'occupation' => 'required|string|max:255',
            'email' => 'required|email|exists:profiles,email',
            'phone' => 'required|integer|exists:profiles,phone',

            'company_organization' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'office_business_address' => 'nullable|string|max:255',
            'office_landline' => 'nullable|string|max:20',
            'office_email' => 'nullable|email|max:255',

            'licensed_shooter' => 'boolean',
            'photo' => 'nullable|string',
            'signature' => 'nullable|string',
        ]);

        $profile->update($validated);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function destroy(Profile $profile): RedirectResponse
    {
        $profile->delete();

        return redirect()->back()->with('success', 'Profile deleted successfully.');
    }
}
