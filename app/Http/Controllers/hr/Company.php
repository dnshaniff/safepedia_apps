<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use App\Models\Company as ModelsCompany;
use App\Services\Company\CompanyDestroyService;
use App\Services\Company\CompanyForceService;
use App\Services\Company\CompanyIndexService;
use App\Services\Company\CompanyRestoreService;
use App\Services\Company\CompanySelectService;
use App\Services\Company\CompanyStoreService;
use App\Services\Company\CompanyUpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class Company extends Controller
{
  public function view()
  {
    return view('content.hr.companies');
  }

  public function select(Request $request, CompanySelectService $service)
  {
    $search = trim((string) $request->get('q', ''));
    $page = max(1, (int) $request->get('page', 1));

    $perPage = max(1, min(100, (int) $request->get('per', 10)));

    $result = $service->execute($search, $page, $perPage);

    return response()->json($result);
  }

  public function index(Request $request, CompanyIndexService $service)
  {
    return response()->json($service->execute([
      'search' => $request->input('search.value'),
      'start' => $request->input('start'),
      'length' => $request->input('length'),
      'draw' => $request->input('draw'),
    ]));
  }

  public function store(Request $request, CompanyStoreService $service)
  {
    try {
      $validated = $request->validate([
        'company_name' => 'required|string|max:100',
        'company_code' => 'required|string|max:10|unique:companies,company_code'
      ]);

      $company = $service->execute($validated);

      return response()->json(['status' => 'success', 'message' => "Company: {$company->company_name} created successfully"
      ], 201);
    } catch (ValidationException $e) {
      $message = collect($e->errors())->flatten()->implode("\n");
      return response()->json(['status' => 'danger', 'message' => $message], 422);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function edit(ModelsCompany $company)
  {
    return response()->json($company, 200);
  }

  public function update(Request $request, ModelsCompany $company, CompanyUpdateService $service)
  {
    try {
      $validated = $request->validate([
        'company_name' => 'required|string|max:100',
        'company_code' => 'required|string|max:10|unique:companies,company_code,' .  $company->id
      ]);

      $service->execute($company, $validated);

      return response()->json(['status' => 'success', 'message' => "Company: {$company->company_name} updated successfully"], 200);
    } catch (ValidationException $e) {
      $message = collect($e->errors())->flatten()->implode("\n");
      return response()->json(['status' => 'danger', 'message' => $message, 'errors' => $e->errors()], 422);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function destroy(ModelsCompany $company, CompanyDestroyService $service)
  {
    try {
      $service->execute($company);

      return response()->json(['status' => 'success', 'message' => "Company: {$company->company_name} deleted successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function restore(string $id, CompanyRestoreService $service)
  {
    $company = ModelsCompany::withTrashed()->findOrFail($id);

    try {
      if ($company->trashed()) {
        $service->execute($company);

        return response()->json(['status' => 'success', 'message' => "Company: {$company->company_name} successfully restored"], 200);
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 200);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function force(string $id, CompanyForceService $service)
  {
    $company = ModelsCompany::withTrashed()->findOrFail($id);

    try {
      if ($company->trashed()) {
        $service->execute($company);

        return response()->json(['status' => 'success', 'message' => 'Company permanent delete successfully'], 200);
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 200);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }
}
