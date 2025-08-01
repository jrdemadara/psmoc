<?php

use App\Http\Controllers\GunClubRegisterController;
use App\Http\Controllers\MemberRegisterController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [WelcomeController::class, 'create'])
    ->name('home');

Route::get('register-member', [MemberRegisterController::class, 'create'])
    ->name('register-member');

Route::post('register-member', [MemberRegisterController::class, 'store'])
    ->name('register-member.store');

Route::get('register-gunclub', [GunClubRegisterController::class, 'create'])
    ->name('register-gunclub');

Route::post('register-gunclub', [GunClubRegisterController::class, 'store'])
    ->name('register-gunclub.store');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
