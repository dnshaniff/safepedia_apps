<?php

namespace App\Http\Controllers\hr;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportJobTitleRequest;
use App\Jobs\ImportJobTitlesJob;
use App\Models\JobTitle as ModelsJobTitle;
use App\Services\JobTitle\JobTitleDestroyService;
use App\Services\JobTitle\JobTitleForceService;
use App\Services\JobTitle\JobTitleIndexService;
use App\Services\JobTitle\JobTitleRestoreService;
use App\Services\JobTitle\JobTitleSelectService;
use App\Services\JobTitle\JobTitleStoreService;
use App\Services\JobTitle\JobTitleUpdateService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class JobTitle extends Controller
{
  public function view()
  {
    return view('content.hr.job_titles');
  }

  public function select(Request $request, JobTitleSelectService $service)
  {
    $search = trim((string) $request->get('q', ''));
    $page = max(1, (int) $request->get('page', 1));

    $perPage = max(1, min(100, (int) $request->get('per', 10)));

    $result = $service->execute($search, $page, $perPage);

    return response()->json($result);
  }

  public function index(Request $request, JobTitleIndexService $service)
  {
    return response()->json($service->execute([
      'search' => $request->input('search.value'),
      'start' => $request->input('start'),
      'length' => $request->input('length'),
      'draw' => $request->input('draw'),
    ]));
  }

  public function store(Request $request, JobTitleStoreService $service)
  {
    try {
      $validated = $request->validate(['title_name' => 'required|string|max:100']);

      $jobTitle = $service->execute($validated);

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

  public function update(Request $request, ModelsJobTitle $jobTitle, JobTitleUpdateService $service)
  {
    try {
      $validated = $request->validate(['title_name' => 'required|string|max:100']);

      $service->execute($jobTitle, $validated);

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

  public function destroy(ModelsJobTitle $jobTitle, JobTitleDestroyService $service)
  {
    try {
      $service->execute($jobTitle);

      return response()->json(['status' => 'success', 'message' => "Job title: {$jobTitle->title_name} deleted successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function restore(string $id, JobTitleRestoreService $service)
  {
    $jobTitle = ModelsJobTitle::withTrashed()->findOrFail($id);

    try {
      if ($jobTitle->trashed()) {
        $service->execute($jobTitle);

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

  public function force(string $id, JobTitleForceService $service)
  {
    $jobTitle = ModelsJobTitle::withTrashed()->findOrFail($id);

    try {
      if ($jobTitle->trashed()) {
        $service->execute($jobTitle);

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

      ImportJobTitlesJob::dispatch($path, Auth::id());

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
