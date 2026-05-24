<?php

namespace App\Domains\Articles;

use App\Domains\Articles\Queries\IndexService;
use App\Domains\Articles\Requests\StoreRequest;
use App\Domains\Articles\Requests\UpdateRequest;
use App\Domains\Articles\Services\StoreService;
use App\Domains\Articles\Services\TerminateService;
use App\Domains\Articles\Services\UpdateService;
use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ArticleController extends Controller
{
  public function view()
  {
    return view('content.articles.index');
  }

  public function index(Request $request, IndexService $service)
  {
    return response()->json(
      $service->execute([
        'search' => $request->input('search.value'),
        'start' => $request->input('start'),
        'length' => $request->input('length'),
        'draw' => $request->input('draw')
      ])
    );
  }

  public function store(StoreRequest $request, StoreService $service)
  {
    try {
      $service->execute($request->validated());

      return response()->json(['status' => 'success', 'message' => 'Article created succefully'], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while creating article', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function edit(Article $article)
  {
    $article->load('images');

    return response()->json($article, 200);
  }

  public function update(UpdateRequest $request, Article $article, UpdateService $service)
  {
    try {
      $service->execute($article, $request->validated());

      return response()->json(['status' => 'success', 'message' => 'Article updated succefully'], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while updating article', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function destroy(Article $article, TerminateService $service)
  {
    try {
      $service->delete($article);

      return response()->json(['status' => 'success', 'message' => "Article deleted successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while deleting article', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function restore(string $id, TerminateService $service)
  {
    $article = Article::withTrashed()->findOrFail($id);

    try {
      if ($article->trashed()) {
        $service->restore($article);

        return response()->json(['status' => 'success', 'message' => "Article restored successfully"], 200);
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 422);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while restoring article', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function force(string $id, TerminateService $service)
  {
    $article = Article::withTrashed()->findOrFail($id);

    try {
      if ($article->trashed()) {
        $service->force($article);

        return response()->json(['status' => 'success', 'message' => "Article permanent delete successfully"], 200);
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 422);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while forcing article deletion', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }
}
