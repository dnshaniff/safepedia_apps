<?php

namespace App\Http\Controllers\ga;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\AssetType as ModelsAssetType;
use Illuminate\Validation\ValidationException;

class AssetType extends Controller
{
  public function view()
  {
    return view('content.ga.asset_types');
  }

  public function select(Request $request)
  {
    $q     = trim((string) $request->get('q', ''));
    $page  = max(1, (int) $request->get('page', 1));
    $per   = max(1, min(100, (int) $request->get('per', 10)));

    $categoryCode = trim((string) $request->get('category_code', ''));

    if ($categoryCode === '') {
      return response()->json(['results' => [], 'more' => false]);
    }

    $query = ModelsAssetType::query()->from('asset_types')->select(['asset_types.id', 'asset_types.type_name'])
      ->join('asset_categories as ac', 'ac.id', '=', 'asset_types.asset_category_id')
      ->where('ac.category_code', $categoryCode);

    if ($q !== '') {
      $tokens = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY) ?: [];
      foreach ($tokens as $t) {
        $t = str_replace(['%', '_'], ['\\%', '\\_'], $t);
        $query->where('type_name', 'LIKE', "%{$t}%");
      }
    }

    $query->orderBy('asset_types.type_name');

    $rows = $query->skip(($page - 1) * $per)->take($per + 1)->get();

    $more = $rows->count() > $per;
    if ($more) $rows = $rows->slice(0, $per);

    return response()->json([
      'results' => $rows->map(fn($r) => [
        'id'   => $r->id,
        'text' => $r->type_name,
      ])->values(),
      'more' => $more
    ]);
  }

  public function index(Request $request)
  {
    $user = auth()->user();
    $isAdmin = $user->username === 'administrator';

    $search = $request->input('search.value');

    $query = ModelsAssetType::query()->when($isAdmin, function ($q) {
      $q->withTrashed();
    });

    $totalData = $query->count();

    if (!empty($search)) {
      $query->where('type_name', 'LIKE', "%{$search}%");
    }

    $totalFiltered = $query->count();

    $assetTypes = $query->offset($request->input('start'))->limit($request->input('length'))->latest()->get();

    $data = [];

    if (!empty($assetTypes)) {
      $ids = $request->input('start');
      foreach ($assetTypes as $assetType) {
        $nestedData['fake_id'] = ++$ids;
        $nestedData['id'] = $assetType->id;
        $nestedData['category_name'] = $assetType->category?->category_name ?? '-';
        $nestedData['category_code'] = $assetType->category?->category_code ?? '-';
        $nestedData['type_name'] = $assetType->type_name;
        $nestedData['type_code'] = $assetType->type_code;
        $nestedData['creator'] = $assetType->creator?->display_name ?? '-';
        $nestedData['created_at'] = $assetType->created_at;
        $nestedData['updated_at'] = $assetType->updated_at;
        $nestedData['deleted_at'] = $assetType->deleted_at;

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
        'asset_category_id' => 'required|exists:asset_categories,id',
        'type_name' => 'required|string|max:100',
        'type_code' => 'required|string|max:20|unique:asset_types,type_code'
      ]);

      $validated['created_by'] = auth()->user()->id;

      $assetType = DB::transaction(function () use ($validated) {
        return ModelsAssetType::create($validated);
      });

      return response()->json(['status' => 'success', 'message' => "Asset type: {$assetType->type_name} created successfully"], 201);
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

  public function edit(ModelsAssetType $assetType)
  {
    $assetType->load(['category:id,category_name,category_code']);

    return response()->json($assetType, 200);
  }

  public function update(Request $request, ModelsAssetType $assetType)
  {
    try {
      $validated = $request->validate([
        'asset_category_id' => 'required|exists:asset_categories,id',
        'type_name' => 'required|string|max:100',
        'type_code' => 'required|string|max:20|unique:asset_types,type_code,' .  $assetType->id
      ]);

      DB::transaction(function () use ($assetType, $validated) {
        $assetType->update($validated);
      });

      return response()->json(['status' => 'success', 'message' => "Asset type: {$assetType->type_name} updated successfully"], 200);
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

  public function destroy(ModelsAssetType $assetType)
  {
    try {
      $assetType->delete();

      return response()->json(['status' => 'success', 'message' => "Asset type: {$assetType->type_name} deleted successfully"], 200);
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
    $assetType = ModelsAssetType::withTrashed()->findOrFail($id);

    try {
      if ($assetType->trashed()) {
        $assetType->restore();

        return response()->json(['status' => 'success', 'message' => "Asset type: {$assetType->type_name} successfully restored"], 200);
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
    $assetType = ModelsAssetType::withTrashed()->findOrFail($id);

    try {
      if ($assetType->trashed()) {
        $assetType->forceDelete();

        return response()->json(['status' => 'success', 'message' => 'Asset type permanent delete successfully'], 200);
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
