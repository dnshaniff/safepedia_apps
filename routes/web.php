<?php

use App\Http\Controllers\pages\Login;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pages\Dashboard;
use App\Http\Controllers\authorization\Role;
use App\Http\Controllers\authorization\User;
use App\Http\Controllers\authorization\Permission;
use App\Http\Controllers\ga\AssetCategory;
use App\Http\Controllers\ga\AssetLocation;
use App\Http\Controllers\ga\AssetType;
use App\Http\Controllers\hr\Employee;
use App\Http\Controllers\hr\EmployeeAgreement;
use App\Http\Controllers\hr\EmployeeOffboarding;
use App\Http\Controllers\hr\ManpowerPlan;
use App\Http\Controllers\hr\Company;
use App\Http\Controllers\hr\JobTitle;
use App\Http\Controllers\hr\OrgUnit;
use App\Http\Controllers\hr\TaCandidate;

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
  Route::get('/asset_categories/select', [AssetCategory::class, 'select']);
  Route::get('/asset_types/select', [AssetType::class, 'select']);
  Route::get('/companies/select', [Company::class, 'select']);
  Route::get('/employees/select', [Employee::class, 'select']);
  Route::get('/job_titles/select', [JobTitle::class, 'select']);
  Route::get('/org_units/select', [OrgUnit::class, 'select']);
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

  // General Affairs
  // Asset Categories
  Route::get('/masterga-asset_categories', [AssetCategory::class, 'view'])->name('masterga-asset_categories');
  Route::resource('/asset_categories', AssetCategory::class)->except('create', 'show');
  Route::post('/asset_categories/{asset_category}/restore', [AssetCategory::class, 'restore'])->name('asset_categories.restore');
  Route::delete('/asset_categories/{asset_category}/force', [AssetCategory::class, 'force'])->name('asset_categories.force');

  // Asset Types
  Route::get('/masterga-asset_types', [AssetType::class, 'view'])->name('masterga-asset_types');
  Route::resource('/asset_types', AssetType::class)->except('create', 'show');
  Route::post('/asset_types/{asset_type}/restore', [AssetType::class, 'restore'])->name('asset_types.restore');
  Route::delete('/asset_types/{asset_type}/force', [AssetType::class, 'force'])->name('asset_types.force');

  // Asset Locations
  Route::get('/masterga-asset_locations', [AssetLocation::class, 'view'])->name('masterga-asset_locations');
  Route::resource('/asset_locations', AssetLocation::class)->except('create', 'show');
  Route::post('/asset_locations/{asset_location}/restore', [AssetLocation::class, 'restore'])->name('asset_locations.restore');
  Route::delete('/asset_locations/{asset_location}/force', [AssetLocation::class, 'force'])->name('asset_locations.force');

  // Human Resources
  // Employees
  Route::get('/employee-employees', [Employee::class, 'view'])->name('employee-employees');
  Route::resource('/employees', Employee::class)->except('create');
  Route::post('/employees/import', [Employee::class, 'import'])->name('employees.import');
  Route::post('/employees/{employee}/storeUser', [Employee::class, 'storeUser'])->name('employees.storeUser');
  Route::post('/employees/{employee}/restore', [Employee::class, 'restore'])->name('employees.restore');
  Route::delete('/employees/{employee}/force', [Employee::class, 'force'])->name('employees.force');
  // Employee Agreements
  Route::resource('/employees/{employee}/employee_agreements', EmployeeAgreement::class)->except('create', 'show');
  Route::post('/employees/{employee}/employee_agreements/{employee_agreements}/restore', [EmployeeAgreement::class, 'restore'])->name('employee_agreements.restore');
  Route::delete('/employees/{employee}/employee_agreements/{employee_agreements}/force', [EmployeeAgreement::class, 'force'])->name('employee_agreements.force');

  // Upcoming Offboardings
  Route::get('/employee-offboardings', [EmployeeOffboarding::class, 'view'])->name('employee-offboardings');
  Route::get('/offboardings', [EmployeeOffboarding::class, 'index'])->name('offboardings.index');

  // Manpower Plans
  Route::get('/recruitment-manpower_plans', [ManpowerPlan::class, 'view'])->name('recruitment-manpower_plans');
  Route::resource('/manpower_plans', ManpowerPlan::class)->except('create');
  Route::post('/manpower_plans/{manpower_plan}/restore', [ManpowerPlan::class, 'restore'])->name('manpower_plans.restore');
  Route::delete('/manpower_plans/{manpower_plan}/force', [ManpowerPlan::class, 'force'])->name('manpower_plans.force');
  // Ta Candidates
  Route::resource('/manpower_plans/{manpower_plan}/ta_candidates', TaCandidate::class)->except('create', 'show');
  Route::post('/manpower_plans/{manpower_plan}/ta_candidates/{ta_candidate}/restore', [TaCandidate::class, 'restore'])->name('ta_candidates.restore');
  Route::delete('/manpower_plans/{manpower_plan}/ta_candidates/{ta_candidate}/force', [TaCandidate::class, 'force'])->name('ta_candidates.force');

  // Companies
  Route::get('/masterhr-companies', [Company::class, 'view'])->name('masterhr-companies');
  Route::resource('/companies', Company::class)->except('create', 'show');
  Route::post('/companies/{company}/restore', [Company::class, 'restore'])->name('companies.restore');
  Route::delete('/companies/{company}/force', [Company::class, 'force'])->name('companies.force');

  // Organization Units
  Route::get('/masterhr-org_units', [OrgUnit::class, 'view'])->name('masterhr-org_units');
  Route::patch('/org_units/reorder', [OrgUnit::class, 'reorder'])->name('org_units.reorder');
  Route::resource('/org_units', OrgUnit::class)->except('create', 'show');
  Route::post('/org_units/{org_unit}/restore', [OrgUnit::class, 'restore'])->name('org_units.restore');
  Route::delete('/org_units/{org_unit}/force', [OrgUnit::class, 'force'])->name('org_units.force');

  // Job Titles
  Route::get('/masterhr-job_titles', [JobTitle::class, 'view'])->name('masterhr-job_titles');
  Route::resource('/job_titles', JobTitle::class)->except('create', 'show');
  Route::post('/job_titles/{job_title}/restore', [JobTitle::class, 'restore'])->name('job_titles.restore');
  Route::delete('/job_titles/{job_title}/force', [JobTitle::class, 'force'])->name('job_titles.force');

  // Authorization
  // Permissions
  Route::get('/access-permissions', [Permission::class, 'view'])->name('access-permissions');
  Route::resource('/permissions', Permission::class)->except('create', 'show');

  // Roles
  Route::get('/access-roles', [Role::class, 'view'])->name('access-roles');
  Route::resource('/roles', Role::class)->except('create', 'show');

  // Users
  Route::get('/access-users', [User::class, 'view'])->name('access-users');
  Route::resource('/users', User::class)->except('create', 'store', 'show');
  Route::post('/users/{user}/restore', [User::class, 'restore'])->name('users.restore');
  Route::delete('/users/{user}/force', [User::class, 'force'])->name('users.force');
});
