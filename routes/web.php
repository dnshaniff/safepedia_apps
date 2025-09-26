<?php

use App\Http\Controllers\pages\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pages\Login;

/**
 * Guest
 */
Route::middleware(['guest'])->group(function () {
  // Login
  Route::redirect('/login', '/auth/login')->name('login');
  Route::get('/auth/login', [Login::class, 'index'])->name('login.index');
  Route::post('/auth/login', [Login::class, 'store'])->name('login.store');
});

/**
 * Authenticated Users
 */
Route::middleware(['auth'])->group(function () {
  // Dashboard
  Route::get('/', [Dashboard::class, 'view'])->name('dashboard');

  // Logout
  Route::post('/auth/logout', [Login::class, 'destroy'])->name('login.destroy');
});
