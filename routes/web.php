<?php

use App\Http\Controllers\RegisterGunClubController;
use App\Http\Controllers\RegisterMemberController;
use App\Http\Controllers\RequestMemberUpdateController;
use App\Http\Controllers\UpdateMemberController;
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

Route::get('request-member-update', [RequestMemberUpdateController::class, 'create'])
    ->name('request-member-update');

Route::post('request-member-update', [RequestMemberUpdateController::class, 'store'])
    ->name('request-member-update.store');

Route::get('update-member/{token}', [UpdateMemberController::class, 'create'])
    ->name('update-member');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
