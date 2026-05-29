<?php

namespace App\Domains\Invoices\Services;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StoreService
{
  public function execute(array $data): Invoice
  {
    return DB::transaction(function () use ($data) {
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

      $invoice = Invoice::create([
        'proforma_number' => $this->generateDocumentNumber('PROFORMA', 'proforma_number', $data['issued_date']),
        'customer_name' => $data['customer_name'],
        'customer_address' => $data['customer_address'],
        'customer_phone' => $data['customer_phone'],
        'reference' => $data['reference'],
        'payment_terms' => $data['payment_terms'],
        'issued_date' => $data['issued_date'],
        'valid_until' => $data['valid_until'],
        'subtotal' => $subtotal,
        'discount_total' => $discountTotal,
        'grand_total' => $grandTotal,
        'paid_amount' => 0,
        'remaining_amount' => $grandTotal,
      ]);

      $invoice->items()->createMany($calculatedItems->toArray());

      return $invoice;
    });
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
