<?php

namespace App\Domains\Invoices\Services;

use App\Models\Invoice;
use Carbon\Carbon;
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
      if (!is_null($invoice->proforma_number)) {
        $invoice->proforma_number = $this->generateDocumentNumber(
          'PRO',
          'proforma_number',
          $invoice->issued_date
        );
      }

      if (!is_null($invoice->invoice_number)) {
        $invoice->invoice_number = $this->generateDocumentNumber(
          'INV',
          'invoice_number',
          $invoice->issued_date
        );
      }

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

  private function generateDocumentNumber(string $prefix, string $column, string $issuedDate): string
  {
    $period = Carbon::parse($issuedDate)->format('ym');

    $baseNumber = "{$prefix}/DNA/{$period}-";

    $lastNumber = Invoice::whereNotNull($column)->where($column, 'like', "{$baseNumber}%")
      ->get()->map(function ($invoice) use ($column) {
        preg_match('/(\d+)$/', $invoice->{$column}, $matches);

        return (int) ($matches[1] ?? 0);
      })->max();

    $nextNumber = ($lastNumber ?? 0) + 1;

    return $baseNumber . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
  }
}
