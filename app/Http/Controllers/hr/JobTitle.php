<?php

namespace App\Http\Controllers\hr;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportJobTitleRequest;
use App\Jobs\ImportJobTitlesJob;
use App\Models\JobTitle as ModelsJobTitle;
use Illuminate\Validation\ValidationException;

class JobTitle extends Controller
{
  public function view()
  {
    return view('content.hr.job_titles');
  }

  public function select(Request $request)
  {
    $q     = trim((string) $request->get('q', ''));
    $page  = max(1, (int) $request->get('page', 1));
    $per   = max(1, min(100, (int) $request->get('per', 10)));

    $query = ModelsJobTitle::query()->select(['id', 'title_name']);

    if ($q !== '') {
      $tokens = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY) ?: [];
      foreach ($tokens as $t) {
        $t = str_replace(['%', '_'], ['\\%', '\\_'], $t);
        $query->where('title_name', 'LIKE', "%{$t}%");
      }
    }

    $query->orderBy('title_name');

    $rows = $query->skip(($page - 1) * $per)->take($per + 1)->get();

    $more = $rows->count() > $per;
    if ($more) $rows = $rows->slice(0, $per);

    return response()->json([
      'results' => $rows->map(fn($r) => [
        'id'   => $r->id,
        'text' => $r->title_name,
      ])->values(),
      'more' => $more
    ]);
  }

  public function index(Request $request)
  {
    $user = auth()->user();
    $isAdmin = $user->username === 'administrator';

    $search = $request->input('search.value');

    $query = ModelsJobTitle::query()->when($isAdmin, function ($q) {
      $q->withTrashed();
    });

    $totalData = $query->count();

    if (!empty($search)) {
      $query->where('title_name', 'LIKE', "%{$search}%");
    }

    $totalFiltered = $query->count();

    $jobTitles = $query->offset($request->input('start'))->limit($request->input('length'))->latest()->get();

    $data = [];

    if (!empty($jobTitles)) {
      $ids = $request->input('start');
      foreach ($jobTitles as $jobTitle) {
        $nestedData['fake_id'] = ++$ids;
        $nestedData['id'] = $jobTitle->id;
        $nestedData['title_name'] = $jobTitle->title_name;
        $nestedData['creator'] = $jobTitle->creator?->display_name ?? '-';
        $nestedData['created_at'] = $jobTitle->created_at;
        $nestedData['updated_at'] = $jobTitle->updated_at;
        $nestedData['deleted_at'] = $jobTitle->deleted_at;

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
      $validated = $request->validate(['title_name' => 'required|string|max:100']);

      $validated['created_by'] = auth()->user()->id;

      $jobTitle = DB::transaction(function () use ($validated) {
        return ModelsJobTitle::create($validated);
      });

      return response()->json(['status' => 'success', 'message' => "Job title: {$jobTitle->title_name} created successfully"], 201);
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

  public function edit(ModelsJobTitle $jobTitle)
  {
    return response()->json($jobTitle, 200);
  }

  public function update(Request $request, ModelsJobTitle $jobTitle)
  {
    try {
      $validated = $request->validate(['title_name' => 'required|string|max:100']);

      DB::transaction(function () use ($jobTitle, $validated) {
        $jobTitle->update($validated);
      });

      return response()->json(['status' => 'success', 'message' => "Job title: {$jobTitle->title_name} updated successfully"], 200);
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

  public function destroy(ModelsJobTitle $jobTitle)
  {
    try {
      $jobTitle->delete();

      return response()->json(['status' => 'success', 'message' => "Job title: {$jobTitle->title_name} deleted successfully"], 200);
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
    $jobTitle = ModelsJobTitle::withTrashed()->findOrFail($id);

    try {
      if ($jobTitle->trashed()) {
        $jobTitle->restore();

        return response()->json(['status' => 'success', 'message' => "Job title: {$jobTitle->title_name} successfully restored"], 200);
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
    $jobTitle = ModelsJobTitle::withTrashed()->findOrFail($id);

    try {
      if ($jobTitle->trashed()) {
        $jobTitle->forceDelete();

        return response()->json(['status' => 'success', 'message' => 'Job title permanent delete successfully'], 200);
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

  public function import(ImportJobTitleRequest $request)
  {
    try {
      $file = $request->file('file');
      $path = $file->store('imports/job_titles');

      ImportJobTitlesJob::dispatch($path, auth()->id());

      return response()->json(['status' => 'success', 'message' => 'Import process started'], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'Failed to start import process'], 500);
    }
  }
}
