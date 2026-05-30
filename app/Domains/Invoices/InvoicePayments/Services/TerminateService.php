<?php

namespace App\Domains\Invoices\InvoicePayments\Services;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TerminateService
{
  public function delete(Invoice $invoice, InvoicePayment $invoicePayment): bool
  {
    $filePath = $invoicePayment->file_path;

    DB::transaction(function () use ($invoice, $invoicePayment) {
      $invoicePayment->delete();

      $paidAmount = $invoice->payments()->sum('amount');

      $invoiceData = [
        'paid_amount' => $paidAmount,
        'remaining_amount' => max(0, $invoice->grand_total - $paidAmount),
      ];

      if (!$invoice->payments()->exists()) {
        $invoiceData['invoice_number'] = null;
      }

      $invoice->update($invoiceData);
    });

    if ($filePath && Storage::disk('public')->exists($filePath)) {
      Storage::disk('public')->delete($filePath);
    }

    return true;
  }
}
