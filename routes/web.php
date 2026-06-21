<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::inertia('/about', 'About')->name('about');
Route::inertia('/contact', 'Contact')->name('contact');
Route::inertia('/privacy-policy', 'PrivacyPolicy')->name('privacy');
Route::inertia('/terms', 'Terms')->name('terms');

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__ . '/settings.php';
