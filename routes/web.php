<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FraudCheckController;
use App\Http\Controllers\PropertyApprovalController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PublicVerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public marketing
|--------------------------------------------------------------------------
*/
Route::inertia('/', 'Welcome')->name('home');
Route::inertia('/about', 'About')->name('about');
Route::inertia('/features', 'Features')->name('features');
Route::inertia('/contact', 'Contact')->name('contact');
Route::inertia('/privacy-policy', 'PrivacyPolicy')->name('privacy');
Route::inertia('/terms', 'Terms')->name('terms');

/*
|--------------------------------------------------------------------------
| Public ownership verification (no auth)
|--------------------------------------------------------------------------
*/
Route::get('verify-property', [PublicVerificationController::class, 'index'])->name('verify-property.index');
Route::post('verify-property/scan', [PublicVerificationController::class, 'scan'])
    ->middleware('throttle:10,1')
    ->name('verify-property.scan');
Route::get('verify-property/{property}', [PublicVerificationController::class, 'show'])->name('verify-property.show');
Route::post('verify-property/{property}/verify', [PublicVerificationController::class, 'verify'])
    ->middleware('throttle:10,1')
    ->name('verify-property.verify');
Route::post('verify-property/{property}/verify-scan', [PublicVerificationController::class, 'verifyScan'])
    ->middleware('throttle:10,1')
    ->name('verify-property.verify-scan');

/*
|--------------------------------------------------------------------------
| Public document fraud check (no auth)
|--------------------------------------------------------------------------
*/
Route::get('fraud-check', [FraudCheckController::class, 'index'])->name('fraud-check.index');
Route::post('fraud-check', [FraudCheckController::class, 'check'])
    ->middleware('throttle:10,1')
    ->name('fraud-check.check');

/*
|--------------------------------------------------------------------------
| Registry console (staff only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin|officer|data_entry'])->group(function (): void {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('properties', PropertyController::class);

    Route::post('properties/{property}/approve', [PropertyApprovalController::class, 'approve'])
        ->name('properties.approve');
    Route::post('properties/{property}/reject', [PropertyApprovalController::class, 'reject'])
        ->name('properties.reject');
});

require __DIR__.'/settings.php';
