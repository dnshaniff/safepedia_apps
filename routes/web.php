<?php

use App\Domains\Users\UserController;
use App\Http\Controllers\authorization\Permission;
use App\Http\Controllers\authorization\Role;
use App\Http\Controllers\pages\Dashboard;
use App\Http\Controllers\pages\Login;
use Illuminate\Support\Facades\Route;

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
  Route::get('/profile/{username}', [Dashboard::class, 'profile'])->name('profile.view');
  Route::patch('/profile/{username}', [Dashboard::class, 'update'])->name('profile.update');

  // Authorization
  // Permissions
  Route::get('/access-permissions', [Permission::class, 'view'])->name('access-permissions');
  Route::resource('/permissions', Permission::class)->except('create', 'show');

  // Roles
  Route::get('/access-roles', [Role::class, 'view'])->name('access-roles');
  Route::resource('/roles', Role::class)->except('create', 'show');

  // Users
  Route::get('/access-users', [UserController::class, 'view'])->name('access-users');
  Route::resource('/users', UserController::class)->except('create', 'show');
  Route::post('/users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
  Route::delete('/users/{user}/force', [UserController::class, 'force'])->name('users.force');
});
