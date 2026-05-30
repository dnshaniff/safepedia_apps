<?php

namespace App\Domains\Invoices\InvoicePayments\Services;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class StoreService
{
  public function execute(Invoice $invoice, array $data): InvoicePayment
  {
    $filePath = null;

    try {
      return DB::transaction(function () use ($invoice, $data, &$filePath) {
        $amount = (float) $data['amount'];

        if ($amount > $invoice->remaining_amount) {
          throw new Exception(
            'Payment amount exceeds remaining balance. Remaining balance is Rp ' .
              number_format(
                $invoice->remaining_amount,
                0,
                ',',
                '.'
              )
          );
        }

        if (empty($invoice->invoice_number)) {
          $invoice->update(['invoice_number' => $this->generateDocumentNumber('INVOICE', 'invoice_number', $data['payment_date'])]);

          $invoice->refresh();
        }

        $file = $data['file_upload'] ?? null;

        $paymentData = [
          'invoice_id' => $invoice->id,
          'payment_date' => $data['payment_date'],
          'amount' => $amount,
          'payment_method' => $data['payment_method'],
          'notes' => $data['notes'] ?? null,
        ];

        if ($file) {
          $paymentSequence = $invoice->payments()->count() + 1;

          $documentNumber = str_replace(['/', '\\'], '-', $invoice->invoice_number ?: $invoice->proforma_number);

          $paymentDate = Carbon::parse($data['payment_date'])->format('Ymd');

          $generatedName = sprintf('%s-%s-PAYMENT-%02d', $documentNumber, $paymentDate, $paymentSequence);

          $extension = $file->getClientOriginalExtension();

          $storedFileName = "{$generatedName}.{$extension}";

          $filePath = $file->storeAs("invoice-payments/{$documentNumber}", $storedFileName, 'public');

          $paymentData = array_merge($paymentData, [
            'file_name' => $storedFileName,
            'file_path' => $filePath,
            'file_mime' => $file->getMimeType(),
            'file_size' => $file->getSize(),
          ]);
        }

        $payment = InvoicePayment::create($paymentData);

        $paidAmount = $invoice->payments()->sum('amount');

        $invoice->update([
          'paid_amount' => $paidAmount,
          'remaining_amount' => max(0, $invoice->grand_total - $paidAmount)
        ]);

        return $payment;
      });
    } catch (Throwable $e) {
      if ($filePath && Storage::disk('public')->exists($filePath)) {
        Storage::disk('public')->delete($filePath);
      }

      throw $e;
    }
  }

  private function generateDocumentNumber(string $prefix, string $column, string $issuedDate): string
  {
    $period = Carbon::parse($issuedDate)->format('ym');
    $baseNumber = "{$prefix}/DNA/{$period}-";

    $latest = Invoice::where($column, 'like', $baseNumber . '%')->lockForUpdate()->orderByDesc($column)->first();

    $nextNumber = $latest ? ((int) substr($latest->{$column}, -3)) + 1 : 1;

    return $baseNumber . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
  }
}
