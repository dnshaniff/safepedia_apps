<?php

namespace App\Http\Controllers\hr;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Company as ModelsCompany;
use Illuminate\Validation\ValidationException;

class Company extends Controller
{
  public function view()
  {
    return view('content.hr.companies');
  }

  public function select(Request $request)
  {
    $q     = trim((string) $request->get('q', ''));
    $page  = max(1, (int) $request->get('page', 1));
    $per   = max(1, min(100, (int) $request->get('per', 10)));

    $query = ModelsCompany::query()->select(['id', 'company_name']);

    if ($q !== '') {
      $tokens = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY) ?: [];
      foreach ($tokens as $t) {
        $t = str_replace(['%', '_'], ['\\%', '\\_'], $t);
        $query->where('company_name', 'LIKE', "%{$t}%");
      }
    }

    $query->orderBy('company_name');

    $rows = $query->skip(($page - 1) * $per)->take($per + 1)->get();

    $more = $rows->count() > $per;
    if ($more) $rows = $rows->slice(0, $per);

    return response()->json([
      'results' => $rows->map(fn($r) => [
        'id'   => $r->id,
        'text' => $r->company_name,
      ])->values(),
      'more' => $more
    ]);
  }

  public function index(Request $request)
  {
    $user = auth()->user();
    $isAdmin = $user->username === 'administrator';

    $search = $request->input('search.value');

    $query = ModelsCompany::query()->when($isAdmin, function ($q) {
      $q->withTrashed();
    });

    $totalData = $query->count();

    if (!empty($search)) {
      $query->where('company_name', 'LIKE', "%{$search}%")
        ->orWhere('company_code', 'LIKE', "%{$search}%");
    }

    $totalFiltered = $query->count();

    $companys = $query->offset($request->input('start'))->limit($request->input('length'))->latest()->get();

    $data = [];

    if (!empty($companys)) {
      $ids = $request->input('start');
      foreach ($companys as $company) {
        $nestedData['fake_id'] = ++$ids;
        $nestedData['id'] = $company->id;
        $nestedData['company_name'] = $company->company_name;
        $nestedData['company_code'] = $company->company_code;
        $nestedData['creator'] = $company->creator?->display_name ?? '-';
        $nestedData['created_at'] = $company->created_at;
        $nestedData['updated_at'] = $company->updated_at;
        $nestedData['deleted_at'] = $company->deleted_at;

        $data[] = $nestedData;
      }
    }

    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => intval($totalData),
      'recordsFiltered' => intval($totalFiltered),
      'code' => 200,
      'data' => $data,
    ]);
  }

  public function store(Request $request)
  {
    try {
      $validated = $request->validate([
        'company_name' => 'required|string|max:100',
        'company_code' => 'required|string|max:10'
      ]);

      $validated['created_by'] = auth()->user()->id;

      $company = DB::transaction(function () use ($validated) {
        return ModelsCompany::create($validated);
      });

      return response()->json(['status' => 'success', 'message' => "Company: {$company->company_name} created successfully"], 201);
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

  public function update(Request $request, ModelsCompany $company)
  {
    try {
      $validated = $request->validate([
        'company_name' => 'required|string|max:100',
        'company_code' => 'required|string|max:10'
      ]);

      DB::transaction(function () use ($company, $validated) {
        $company->update($validated);
      });

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

  public function destroy(ModelsCompany $company)
  {
    try {
      $company->delete();

      return response()->json(['status' => 'success', 'message' => "Company: {$company->company_name} deleted successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function restore(string $id)
  {
    $company = ModelsCompany::withTrashed()->findOrFail($id);

    try {
      if ($company->trashed()) {
        $company->restore();

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

  public function force(string $id)
  {
    $company = ModelsCompany::withTrashed()->findOrFail($id);

    try {
      if ($company->trashed()) {
        $company->forceDelete();

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
