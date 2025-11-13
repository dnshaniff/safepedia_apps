<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use App\Models\Department as ModelsDepartment;
use Illuminate\Http\Request;

class Department extends Controller
{
  public function view()
  {
    return view('content.master.departments');
  }

  public function index(Request $request) {}

  public function store(Request $request) {}

  public function show(ModelsDepartment $department, Request $request) {}

  public function edit(ModelsDepartment $department, Request $request) {}

  public function update(ModelsDepartment $department, Request $request) {}

  public function destroy(ModelsDepartment $department, Request $request) {}

  public function restore($id)
  {
    $department = ModelsDepartment::withTrashed()->findOrFail($id);
  }

  public function force($id)
  {
    $department = ModelsDepartment::withTrashed()->findOrFail($id);
  }
}
