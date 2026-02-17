<?php

namespace App\Http\Controllers\ga;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Models\AssetLocation as ModelsAssetLocation;

class AssetLocation extends Controller
{
  public function view()
  {
    return view('content.ga.asset_locations');
  }

  public function select(Request $request)
  {
    $q     = trim((string) $request->get('q', ''));
    $page  = max(1, (int) $request->get('page', 1));
    $per   = max(1, min(100, (int) $request->get('per', 10)));

    $query = ModelsAssetLocation::query()->select(['id', 'location_name']);

    if ($q !== '') {
      $tokens = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY) ?: [];
      foreach ($tokens as $t) {
        $t = str_replace(['%', '_'], ['\\%', '\\_'], $t);
        $query->where('location_name', 'LIKE', "%{$t}%");
      }
    }

    $query->orderBy('location_name');

    $rows = $query->skip(($page - 1) * $per)->take($per + 1)->get();

    $more = $rows->count() > $per;
    if ($more) $rows = $rows->slice(0, $per);

    return response()->json([
      'results' => $rows->map(fn($r) => [
        'id'   => $r->id,
        'text' => $r->location_name,
      ])->values(),
      'more' => $more
    ]);
  }

  public function index(Request $request)
  {
    $user = auth()->user();
    $isAdmin = $user->username === 'administrator';

    $search = $request->input('search.value');

    $query = ModelsAssetLocation::query()->when($isAdmin, function ($q) {
      $q->withTrashed();
    });

    $totalData = $query->count();

    if (!empty($search)) {
      $query->where('location_name', 'LIKE', "%{$search}%");
    }

    $totalFiltered = $query->count();

    $assetLocations = $query->offset($request->input('start'))->limit($request->input('length'))->latest()->get();

    $data = [];

    if (!empty($assetLocations)) {
      $ids = $request->input('start');
      foreach ($assetLocations as $assetLocation) {
        $nestedData['fake_id'] = ++$ids;
        $nestedData['id'] = $assetLocation->id;
        $nestedData['location_name'] = $assetLocation->location_name;
        $nestedData['creator'] = $assetLocation->creator?->display_name ?? '-';;
        $nestedData['created_at'] = $assetLocation->created_at;
        $nestedData['updated_at'] = $assetLocation->updated_at;
        $nestedData['deleted_at'] = $assetLocation->deleted_at;

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
      $validated = $request->validate(['location_name' => 'required|string|max:100']);

      $validated['created_by'] = auth()->user()->id;

      $assetLocation = DB::transaction(function () use ($validated) {
        return ModelsAssetLocation::create($validated);
      });

      return response()->json(['status' => 'success', 'message' => "Asset location: {$assetLocation->location_name} created successfully"], 201);
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

  public function edit(ModelsAssetLocation $assetLocation)
  {
    return response()->json($assetLocation, 200);
  }

  public function update(Request $request, ModelsAssetLocation $assetLocation)
  {
    try {
      $validated = $request->validate(['location_name' => 'required|string|max:100']);

      DB::transaction(function () use ($assetLocation, $validated) {
        $assetLocation->update($validated);
      });

      return response()->json(['status' => 'success', 'message' => "Asset location: {$assetLocation->location_name} updated successfully"], 200);
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

  public function destroy(ModelsAssetLocation $assetLocation)
  {
    try {
      $assetLocation->delete();

      return response()->json(['status' => 'success', 'message' => "Asset location: {$assetLocation->location_name} deleted successfully"], 200);
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
    $assetLocation = ModelsAssetLocation::withTrashed()->findOrFail($id);

    try {
      if ($assetLocation->trashed()) {
        $assetLocation->restore();

        return response()->json(['status' => 'success', 'message' => "Asset location: {$assetLocation->location_name} successfully restored"], 200);
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
    $assetLocation = ModelsAssetLocation::withTrashed()->findOrFail($id);

    try {
      if ($assetLocation->trashed()) {
        $assetLocation->forceDelete();

        return response()->json(['status' => 'success', 'message' => 'Asset location permanent delete successfully'], 200);
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
