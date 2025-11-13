<?php

use App\Http\Controllers\pages\Login;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pages\Dashboard;
use App\Http\Controllers\authorization\Role;
use App\Http\Controllers\authorization\User;
use App\Http\Controllers\authorization\Permission;

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

/**
 * without permissions
 */
Route::middleware(['auth', 'status'])->group(function () {
  // Logout
  Route::post('/auth/logout', [Login::class, 'destroy'])->name('login.destroy');

  // Fetch Data
  Route::get('/roles/select', [Role::class, 'select']);
});

/**
 * with permissions
 */
Route::middleware(['auth', 'status', 'permission'])->group(function () {
  // Dashboard
  Route::get('/', [Dashboard::class, 'view'])->name('dashboard');

  // Authorization
  Route::get('/access-permissions', [Permission::class, 'view'])->name('access-permissions');
  Route::resource('/permissions', Permission::class)->except('create', 'show');

  Route::get('/access-roles', [Role::class, 'view'])->name('access-roles');
  Route::resource('/roles', Role::class)->except('create', 'show');

  Route::get('/access-users', [User::class, 'view'])->name('access-users');
  Route::resource('/users', User::class)->except('create', 'store', 'show');
  Route::post('/users/{user}/restore', [User::class, 'restore'])->name('users.restore');
  Route::delete('/users/{user}/force', [User::class, 'force'])->name('users.force');
});
