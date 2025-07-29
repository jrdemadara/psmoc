<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

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
