<?php

use App\Http\Controllers\RegisterGunClubController;
use App\Http\Controllers\RegisterMemberController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'create'])
    ->name('home');

Route::get('register-member', [RegisterMemberController::class, 'create'])
    ->name('register-member');

Route::post('register-member', [RegisterMemberController::class, 'store'])
    ->name('register-member.store');

Route::get('register-gunclub', [RegisterGunClubController::class, 'create'])
    ->name('register-gunclub');

Route::post('register-gunclub', [RegisterGunClubController::class, 'store'])
    ->name('register-gunclub.store');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
