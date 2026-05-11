<?php

use App\Domains\Brands\BrandController;
use App\Domains\Pages\DashboardController;
use App\Domains\Pages\ProfileController;
use App\Domains\Pages\LoginController;
use App\Domains\Permissions\PermissionController;
use App\Domains\Products\ProductController;
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
 * without permissions
 */
Route::middleware(['auth', 'status'])->group(function () {
  // Logout
  Route::post('/auth/logout', [LoginController::class, 'destroy'])->name('login.destroy');

  // Fetch Data
  Route::get('/roles/select', [RoleController::class, 'select']);
  Route::get('/brands/select', [BrandController::class, 'select']);
});

/**
 * with permissions
 */
Route::middleware(['auth', 'status', 'permission'])->group(function () {
  // Dashboard
  Route::get('/', [DashboardController::class, 'view'])->name('dashboard');
  Route::get('/profile/{username}', [ProfileController::class, 'view'])->name('profile.view');
  Route::patch('/profile/{username}', [ProfileController::class, 'update'])->name('profile.update');

  // Invoices


  //Products
  Route::get('/page-products', [ProductController::class, 'view'])->name('page-products');
  Route::resource('/products', ProductController::class)->except('create');
  Route::post('/products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
  Route::delete('/products/{product}/force', [ProductController::class, 'force'])->name('products.force');

  // Brands
  Route::get('/page-brands', [BrandController::class, 'view'])->name('page-brands');
  Route::resource('/brands', BrandController::class)->except('create', 'show');
  Route::post('/brands/{brand}/restore', [BrandController::class, 'restore'])->name('brands.restore');
  Route::delete('/brands/{brand}/force', [BrandController::class, 'force'])->name('brands.force');

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
