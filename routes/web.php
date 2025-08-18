<?php

use App\Http\Controllers\Profile\ResubmissionController;
use App\Http\Controllers\Profile\UpdateAddressController;
use App\Http\Controllers\Profile\UpdateApplicationDetailsController;
use App\Http\Controllers\Profile\UpdateFirearmsController;
use App\Http\Controllers\Profile\UpdateGunClubsController;
use App\Http\Controllers\Profile\UpdatePersonalDetailsController;
use App\Http\Controllers\Profile\UpdatePhotoSignatureController;
use App\Http\Controllers\Profile\UpdateWorkDetailsController;
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

Route::patch('update-member', [UpdateMemberController::class, 'store'])
    ->name('update-member.store');

Route::patch('update-application-details', [UpdateApplicationDetailsController::class, 'update'])
    ->name('update-application-details');

Route::patch('update-personal-details', [UpdatePersonalDetailsController::class, 'update'])
    ->name('update-personal-details');

Route::patch('update-address', [UpdateAddressController::class, 'update'])
    ->name('update-address');

Route::patch('update-work-details', [UpdateWorkDetailsController::class, 'update'])
    ->name('update-work-details');

Route::post('update-photosignature', [UpdatePhotoSignatureController::class, 'update'])
    ->name('update-photosignature');

Route::patch('update-gunclubs', [UpdateGunClubsController::class, 'update'])
    ->name('update-gunclubs');

Route::patch('update-firearms', [UpdateFirearmsController::class, 'update'])
    ->name('update-firearms');

Route::get('resubmission/{token}', [ResubmissionController::class, 'index'])
    ->name('resubmission');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
