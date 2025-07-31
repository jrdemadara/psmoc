<?php

use App\Http\Controllers\GunClubRegisterController;
use App\Http\Controllers\MemberRegisterController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('register-member', [MemberRegisterController::class, 'create'])
    ->name('register-member.create');

Route::post('register-member', [MemberRegisterController::class, 'store'])
    ->name('register-member.store');

Route::get('register-gunclub', [GunClubRegisterController::class, 'create'])
    ->name('register-gunclub.create');

Route::post('register-gunclub', [GunClubRegisterController::class, 'store'])
    ->name('register-gunclub.store');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
