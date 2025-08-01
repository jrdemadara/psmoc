<?php

namespace App\Http\Controllers;

use App\Models\Gunclub;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
    public function create(): Response
    {
        $gunClubs = Gunclub::select('id', 'name', 'logo')->orderBy('name')->get();

        foreach ($gunClubs as $gunClub) {
            $gunClub->logo = Storage::temporaryUrl($gunClub->logo, now()->addMinutes(10));
        }

        return Inertia::render('Welcome', [
            'gunClubs' => $gunClubs,
        ]);
    }
}
