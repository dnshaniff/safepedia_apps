<?php

namespace App\Jobs;

use Throwable;
use App\Imports\JobTitlesImport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Events\SystemResourceUpdated;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportJobTitlesJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected string $path;
  protected string $userId;

  public function __construct(string $path, string $userId)
  {
    $this->path = $path;
    $this->userId = $userId;
  }

  public function handle(): void
  {
    try {
      $import = new JobTitlesImport($this->userId);

      Excel::import($import, $this->path, 'local');

      Log::info('Job title import finished', [
        'inserted' => $import->getInsertedCount(),
      ]);

      Storage::disk('local')->delete($this->path);

      event(new SystemResourceUpdated(
        resource: 'job_titles',
        action: 'import',
        performedBy: $this->userId,
        message: 'Import job title completed',
        notifyAuthor: true
      ));
    } catch (Throwable $e) {
      Log::error('Employee import failed', [
        'error' => $e->getMessage(),
        'file'  => $this->path,
      ]);

      throw $e;
    }
  }
}
