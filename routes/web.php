<?php

use App\Http\Controllers\pages\Login;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pages\Dashboard;
use App\Http\Controllers\authorization\Role;
use App\Http\Controllers\authorization\User;
use App\Http\Controllers\authorization\Permission;
use App\Http\Controllers\hr\Employee;
use App\Http\Controllers\hr\EmployeeAgreement;
use App\Http\Controllers\master\Company;
use App\Http\Controllers\master\JobTitle;
use App\Http\Controllers\master\OrgUnit;

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

  // Human Resources
  // Employees
  Route::get('/hr-employees', [Employee::class, 'view'])->name('hr-employees');
  Route::resource('/employees', Employee::class)->except('create');
  Route::post('/employees/{employee}/restore', [Employee::class, 'restore'])->name('employees.restore');
  Route::delete('/employees/{employee}/force', [Employee::class, 'force'])->name('employees.force');

  // Employee Agreements
  Route::resource('/employees/{employee}/employee_agreements', EmployeeAgreement::class)->except('create', 'show');
  Route::post('/employees/{employee}/employee_agreements/{employee_agreements}/restore', [EmployeeAgreement::class, 'restore'])->name('employee_agreements.restore');
  Route::delete('/employees/{employee}/employee_agreements/{employee_agreements}/force', [EmployeeAgreement::class, 'force'])->name('employee_agreements.force');

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

  // Master Data
  // Companies
  Route::get('/master-companies', [Company::class, 'view'])->name('master-companies');
  Route::resource('/companies', Company::class)->except('create', 'show');
  Route::post('/companies/{company}/restore', [Company::class, 'restore'])->name('companies.restore');
  Route::delete('/companies/{company}/force', [Company::class, 'force'])->name('companies.force');

  // Organization Units
  Route::get('/master-org_units', [OrgUnit::class, 'view'])->name('master-org_units');
  Route::patch('/org_units/reorder', [OrgUnit::class, 'reorder'])->name('org_units.reorder');
  Route::resource('/org_units', OrgUnit::class)->except('create', 'show');
  Route::post('/org_units/{org_unit}/restore', [OrgUnit::class, 'restore'])->name('org_units.restore');
  Route::delete('/org_units/{org_unit}/force', [OrgUnit::class, 'force'])->name('org_units.force');

  // Job Titles
  Route::get('/master-job_titles', [JobTitle::class, 'view'])->name('master-job_titles');
  Route::resource('/job_titles', JobTitle::class)->except('create', 'show');
  Route::post('/job_titles/{job_title}/restore', [JobTitle::class, 'restore'])->name('job_titles.restore');
  Route::delete('/job_titles/{job_title}/force', [JobTitle::class, 'force'])->name('job_titles.force');
});
