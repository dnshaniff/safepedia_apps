<?php

namespace App\Domains\Invoices\Services;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateService
{
  public function execute(Invoice $invoice, array $data): Invoice
  {
    return DB::transaction(function () use ($invoice, $data) {
      $items = collect($data['item-invoice']);

      $subtotal = 0;
      $discountTotal = 0;
      $grandTotal = 0;

      $calculatedItems = $items->map(function ($item) use (&$subtotal, &$discountTotal, &$grandTotal) {
        $quantity = (float) $item['quantity'];
        $unitPrice = (float) $item['unit_price'];
        $discount = (float) $item['discount'];

        $lineSubtotal = $quantity * $unitPrice;
        $discountAmount = $lineSubtotal * ($discount / 100);
        $lineTotal = $lineSubtotal - $discountAmount;

        $subtotal += $lineSubtotal;
        $discountTotal += $discountAmount;
        $grandTotal += $lineTotal;

        return [
          'product_id' => $item['product_id'],
          'quantity' => $quantity,
          'uom' => $item['uom'],
          'unit_price' => $unitPrice,
          'discount' => $discount,
          'line_total' => $lineTotal,
        ];
      });

      $paidAmount = (float) $invoice->paid_amount;
      $remainingAmount = max($grandTotal - $paidAmount, 0);

      $newPeriod = Carbon::parse($data['issued_date'])->format('ym');
      $currentProformaPeriod = $this->documentPeriod($invoice->proforma_number);

      $proformaNumber = $invoice->proforma_number;

      if ($currentProformaPeriod !== $newPeriod) {
        $proformaNumber = $this->generateDocumentNumber('PROFORMA', 'proforma_number', $data['issued_date']);
      }

      $invoice->update([
        'proforma_number' => $proformaNumber,
        'customer_name' => $data['customer_name'],
        'customer_address' => $data['customer_address'],
        'customer_phone' => $data['customer_phone'],
        'reference' => $data['reference'],
        'payment_terms' => $data['payment_terms'],
        'issued_date' => $data['issued_date'],
        'valid_until' => $data['valid_until'],
        'subtotal' => $subtotal,
        'discount' => $discountTotal,
        'grand_total' => $grandTotal,
        'remaining_amount' => $remainingAmount,
      ]);

      $invoice->items()->delete();
      $invoice->items()->createMany($calculatedItems->toArray());

      return $invoice->fresh('items');
    });
  }

  private function documentPeriod(?string $number): ?string
  {
    if (!$number) return null;

    preg_match('/\/DNA\/(\d{4})-/', $number, $matches);

    return $matches[1] ?? null;
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
