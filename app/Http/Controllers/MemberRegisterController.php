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
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class MemberRegisterController extends Controller
{
    public function create(): Response
    {
        $gunClubs = Gunclub::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('MemberRegister', [
            'gunClubs' => $gunClubs,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Log::info('Starting profile store process.');
        $request->validate([
        'application_venue' => 'required|string|max:255',
        'licensed_shooter' => 'required|string|max:255',
        'ltopf_no' => 'required|string|max:255',
        'license_type' => 'required|string|max:255',

        'last_name' => 'required|string|max:255',
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'extension' => 'nullable|string|max:255',
        'birth_date' => 'required|date',
        'birth_place' => 'required|string|max:255',
        'age' => 'required|integer|min:0',
        'gender' => 'required|string|max:255',
        'civil_status' => 'required|string|max:255',
        'blood_type' => 'required|string|max:3',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:15',

        'region' => 'required|string|max:255',
        'province' => 'required|string|max:255',
        'city_municipality' => 'required|string|max:255',
        'barangay' => 'required|string|max:255',
        'purok' => 'required|string|max:255',
        'street' => 'required|string|max:255',

        'occupation' => 'required|string|max:255',
        'company_organization' => 'nullable|string|max:255',
        'position' => 'nullable|string|max:255',
        'office_business_address' => 'nullable|string|max:255',
        'office_landline' => 'nullable|string|max:20',
        'office_email' => 'nullable|email|max:255',

        'photo' => 'required|file|mimes:jpeg,jpg,png|max:5000',
        'signature' => 'required|file|mimes:png|max:5000',

        'gunclubs' => 'required|array|min:1',
        'gunclubs.*.gunclub_id' => 'required|exists:gunclubs,id',
        'gunclubs.*.years_no' => 'required|integer|min:0|max:100',
        'gunclubs.*.is_main' => 'required|boolean',

        'firearms' => 'required|array|min:1',
        'firearms.*.type' => 'required|string|max:255',
        'firearms.*.make' => 'required|string|max:255',
        'firearms.*.model' => 'required|string|max:255',
        'firearms.*.caliber' => 'required|string|max:50',
        'firearms.*.serial_no' => 'required|string|max:255',
        ]);
        Log::info('Request validated successfully.');

        DB::beginTransaction();

        try {
            // 1. Save Profile
            Log::info('Attempting to create profile...');
            $profile = Profile::create([
                'reg_type' => "new",
                'application_venue' => $request->application_venue,
                'licensed_shooter' => $request->licensed_shooter === 'Yes',
                'ltopf_no' => $request->ltopf_no,
                'license_type' => $request->license_type,

                'last_name' => $request->last_name,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'extension' => $request->extension,
                'email' => $request->email,
                'phone' => $request->phone,
                'birth_date' => $request->birth_date,
                'birth_place' => $request->birth_place,
                'age' => $request->age,
                'gender' => $request->gender,
                'civil_status' => $request->civil_status,
                'blood_type' => $request->blood_type,

                'region' => $request->region,
                'province' => $request->province,
                'city_municipality' => $request->city_municipality,
                'barangay' => $request->barangay,
                'purok' => $request->purok,
                'street' => $request->street,

                'occupation' => $request->occupation,
                'company_organization' => $request->company_organization,
                'position' => $request->position,
                'office_business_address' => $request->office_business_address,
                'office_landline' => $request->office_landline,
                'office_email' => $request->office_email,

                'expiry_date' => Carbon::now()->endOfYear()->toDateString(),
            ]);

            Log::info('Profile created.', ['profile_id' => $profile->id]);

            // 2. Save Gun Clubs
            Log::info('Saving gun club memberships...');
            foreach ($request->gunclubs as $index => $gunclub) {
                $profile->gunclubMembers()->create([
                    'gunclub_id' => $gunclub['gunclub_id'],
                    'years_no' => $gunclub['years_no'],
                    'is_main' => $gunclub['is_main'],
                ]);
                Log::info("Gun club #{$index} saved.", $gunclub);
            }

            // 3. Save Firearms
            Log::info('Saving firearms...');
            foreach ($request->firearms as $firearmData) {
                $profile->firearms()->create([
                    'type' => $firearmData['type'] ?? null,
                    'make' => $firearmData['make'] ?? null,
                    'model' => $firearmData['model'] ?? null,
                    'caliber' => $firearmData['caliber'] ?? null,
                    'serial_no' => $firearmData['serial_no'] ?? null,
                    // ... add other fields as needed
                ]);
                Log::info("Firearm #{$index} saved.", $firearmData);
            }

            // 4. Upload and Save Photo and Signature to S3
            Log::info('Uploading photo and signature to S3...');
            if ($request->hasFile('photo')) {
                $extension = $request->file('photo')->getClientOriginalExtension();
                $filename = "{$profile->id}.{$extension}";

                $photoPath = $request->file('photo')->storeAs('photos', $filename, 's3');
                if ($photoPath) {
                    $profile->photo = $photoPath;
                }
                Log::info('Photo uploaded.', ['path' => $photoPath]);
            }

            if ($request->hasFile('signature')) {
                $extension = $request->file('signature')->getClientOriginalExtension();
                $filename = "{$profile->id}.{$extension}";

                $signaturePath = $request->file('signature')->storeAs('signatures', $filename, 's3');
                if ($signaturePath) {
                    $profile->signature = $signaturePath;
                }
                Log::info('Signature uploaded.', ['path' => $signaturePath]);
            }

            $profile->save();
            Log::info('Profile saved with S3 URLs.');

            DB::commit();
            Log::info('Transaction committed successfully.');

            $data = [
                'name' => Str::upper($profile->first_name),
            ];

            Log::info('Attempting to send onboarding email.');
            Mail::to($profile->email)->send(new MemberOnboardingMail($data));
            Log::info('Onboarding email sent.');

            return redirect()->back()->with('success', 'Profile created successfully.');

        } catch (\Throwable $e) {
            Log::error('Profile save failed.', [
                'error_message' =>  $e->getMessage()
            ]);

             DB::rollBack();

             return redirect()->back()->with('error', $e->getMessage());
        }
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
