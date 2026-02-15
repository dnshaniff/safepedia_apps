<?php

namespace App\Jobs;

use Throwable;
use App\Imports\EmployeesImport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use App\Events\EmployeesImportFinished;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportEmployeesJob implements ShouldQueue
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
      $import = new EmployeesImport($this->userId);

      Excel::import($import, $this->path, 'local');

      Log::info('Employee import finished', [
        'inserted' => $import->getInsertedCount(),
      ]);

      Storage::delete($this->path);

      event(new EmployeesImportFinished($this->userId));
    } catch (Throwable $e) {
      Log::error('Employee import failed', [
        'error' => $e->getMessage(),
        'file'  => $this->path,
      ]);

      throw $e;
    }
  }
}
