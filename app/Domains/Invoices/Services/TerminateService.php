<?php

namespace App\Domains\Invoices\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class TerminateService
{
  public function delete(Invoice $invoice): void
  {
    DB::transaction(function () use ($invoice) {
      $invoice->delete();
    });
  }

  public function restore(Invoice $invoice): bool
  {
    if (! $invoice->trashed()) {
      return false;
    }

    DB::transaction(function () use ($invoice) {
      $invoice->restore();
    });

    return true;
  }

  public function force(Invoice $invoice): bool
  {
    if (! $invoice->trashed()) {
      return false;
    }

    DB::transaction(function () use ($invoice) {
      $invoice->forceDelete();
    });

    return true;
  }
}
