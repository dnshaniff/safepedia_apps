<?php

use App\Domains\Pages\DashboardController;
use App\Domains\Pages\ProfileController;
use App\Domains\Pages\LoginController;
use App\Domains\Permissions\PermissionController;
use App\Domains\Roles\RoleController;
use App\Domains\Users\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Guest
 */
Route::middleware(['guest'])->group(function () {
  // Login
  Route::redirect('/login', '/auth/login')->name('login');
  Route::get('/auth/login', [LoginController::class, 'view'])->name('login.index');
  Route::post('/auth/login', [LoginController::class, 'store'])->name('login.store');
});

/**
 * Authenticated Users
 */

/**
 * without permissionsw
 */
Route::middleware(['auth', 'status'])->group(function () {
  // Two Factor
  Route::get('/auth/two-factor', [LoginController::class, 'twofactorView'])->name('twofactor.index');
  Route::post('/auth/two-factor', [LoginController::class, 'twofactorStore'])->name('twofactor.store');

  // Logout
  Route::post('/auth/logout', [LoginController::class, 'destroy'])->name('login.destroy');

  // Fetch Data
  Route::get('/roles/select', [RoleController::class, 'select']);
});

/**
 * with permissions
 */
Route::middleware(['auth', 'status', 'permission', 'twofactor'])->group(function () {
  // Dashboard
  Route::get('/', [DashboardController::class, 'view'])->name('dashboard');
  Route::get('/profile/{username}', [ProfileController::class, 'view'])->name('profile.view');
  Route::patch('/profile/{username}', [ProfileController::class, 'update'])->name('profile.update');
  Route::post('/profile/{username}/generate-two-factor', [ProfileController::class, 'generateTwoFactor'])->name('profile.two_factor');

  // Authorization
  // Permissions
  Route::get('/access-permissions', [PermissionController::class, 'view'])->name('access-permissions');
  Route::resource('/permissions', PermissionController::class)->except('create', 'show');

  // Roles
  Route::get('/access-roles', [RoleController::class, 'view'])->name('access-roles');
  Route::resource('/roles', RoleController::class)->except('create', 'show');

  // Users
  Route::get('/access-users', [UserController::class, 'view'])->name('access-users');
  Route::resource('/users', UserController::class)->except('create', 'show');
  Route::post('/users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
  Route::delete('/users/{user}/force', [UserController::class, 'force'])->name('users.force');
});
