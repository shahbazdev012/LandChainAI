<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertyApprovalController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyDocumentController;
use App\Http\Controllers\PropertyVerificationController;
use App\Http\Controllers\PublicVerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public marketing & verification
|--------------------------------------------------------------------------
*/
Route::inertia('/', 'Welcome')->name('home');
Route::inertia('/about', 'About')->name('about');
Route::inertia('/features', 'Features')->name('features');
Route::inertia('/contact', 'Contact')->name('contact');
Route::inertia('/privacy-policy', 'PrivacyPolicy')->name('privacy');
Route::inertia('/terms', 'Terms')->name('terms');

Route::get('/verify', PublicVerificationController::class)->name('verify');

/*
|--------------------------------------------------------------------------
| Registry console (staff only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin|officer'])->group(function (): void {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('properties', PropertyController::class);

    Route::scopeBindings()->group(function (): void {
        Route::post('properties/{property}/documents', [PropertyDocumentController::class, 'store'])
            ->name('properties.documents.store');
        Route::get('properties/{property}/documents/{document}', [PropertyDocumentController::class, 'show'])
            ->name('properties.documents.show');
        Route::delete('properties/{property}/documents/{document}', [PropertyDocumentController::class, 'destroy'])
            ->name('properties.documents.destroy');
    });

    Route::post('properties/{property}/verify', [PropertyVerificationController::class, 'store'])
        ->name('properties.verify');

    Route::post('properties/{property}/approve', [PropertyApprovalController::class, 'approve'])
        ->name('properties.approve');
    Route::post('properties/{property}/reject', [PropertyApprovalController::class, 'reject'])
        ->name('properties.reject');
});

require __DIR__.'/settings.php';
