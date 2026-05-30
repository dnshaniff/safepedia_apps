<?php

namespace App\Domains\Invoices\InvoicePayments\Services;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateService
{
  public function execute(
    Invoice $invoice,
    InvoicePayment $invoicePayment,
    array $data
  ): InvoicePayment {
    $oldFilePath = $invoicePayment->file_path;
    $newFilePath = null;

    try {
      return DB::transaction(function () use ($invoice, $invoicePayment, $data, &$newFilePath) {
        $amount = (float) $data['amount'];

        $availableBalance = $invoice->remaining_amount + $invoicePayment->amount;

        if ($amount > $availableBalance) {
          throw new Exception(
            'Payment amount exceeds remaining balance. Remaining balance is Rp ' .
              number_format(
                $availableBalance,
                0,
                ',',
                '.'
              )
          );
        }

        $paymentData = [
          'payment_date' => $data['payment_date'],
          'amount' => $amount,
          'payment_method' => $data['payment_method'],
          'notes' => $data['notes'] ?? null,
        ];

        $file = $data['file_upload'] ?? null;

        if ($file) {
          $documentNumber = $invoice->invoice_number ?: $invoice->proforma_number;

          $documentNumber = str_replace(['/', '\\'], '-', $documentNumber);

          $extension = $file->getClientOriginalExtension();

          $storedFileName = "{$documentNumber}-payment-{$invoicePayment->id}.{$extension}";

          $newFilePath = $file->storeAs("invoice-payments/{$invoice->id}", $storedFileName, 'public');

          $paymentData = array_merge(
            $paymentData,
            [
              'file_name' => $storedFileName,
              'file_path' => $newFilePath,
              'file_mime' => $file->getMimeType(),
              'file_size' => $file->getSize(),
            ]
          );
        }

        $invoicePayment->update($paymentData);

        $paidAmount = $invoice->payments()->sum('amount');

        $invoice->update([
          'paid_amount' => $paidAmount,
          'remaining_amount' => max(0, $invoice->grand_total - $paidAmount),
        ]);

        return $invoicePayment->fresh();
      });
    } catch (Throwable $e) {
      if ($newFilePath && Storage::disk('public')->exists($newFilePath)) {
        Storage::disk('public')->delete($newFilePath);
      }

      throw $e;
    } finally {
      if ($newFilePath && $oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
        Storage::disk('public')->delete($oldFilePath);
      }
    }
  }
}
