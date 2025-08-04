<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

Route::get('proxy/regions', function () {
    $response = Http::get('https://psgc.cloud/api/regions');
    //return 407;
    return $response->json();
});

Route::get('proxy/provinces/{code}', function ($code) {
    // Make an HTTP request and inject the code into the URL
    $response = Http::get("https://psgc.cloud/api/regions/{$code}/provinces");

    return $response->json();
});

Route::get('proxy/municipalities/{code}', function ($code) {
    // Make an HTTP request and inject the code into the URL
    $response = Http::get("https://psgc.cloud/api/provinces/${code}/cities-municipalities");

    return $response->json();
});

Route::get('proxy/barangays/{code}', function ($code) {
    // Make an HTTP request and inject the code into the URL
    $response = Http::get("https://psgc.cloud/api/cities-municipalities/${code}/barangays");
    return $response->json();
});



Route::post('/webhook/github', function(Request $request) {
    $secret = env('GITHUB_WEBHOOK_SECRET');
    $signature = $request->header('X-Hub-Signature-256');
    $payload = $request->getContent();

    // 🔐 Verify webhook signature
    $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    if (!hash_equals($expected, $signature)) {
        Log::warning('Invalid GitHub signature.');
        abort(403, 'Invalid signature');
    }

    // 🧠 Check if push is to `main` branch
    $data = json_decode($payload, true);
    $ref = $data['ref'] ?? '';
    if ($ref === 'refs/heads/main') {
        // 🧨 Execute root-level script
        $process = new Process(['/deploy/psmoc.sh']);
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error('Script failed', [
                'error' => $process->getErrorOutput(),
                'output' => $process->getOutput()
            ]);
            return response('Script failed', 500);
        }

        Log::info('psmoc.sh executed successfully', [
            'output' => $process->getOutput()
        ]);

        return response('Executed script', 200);
    }

    return response('No action: not main branch', 200);
});
