<?php

namespace App\Domains\Invoices\Queries;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class IndexService
{
  public function execute(array $params): array
  {
    $user = Auth::user();

    $isAdmin = $user->username === 'administrator';

    $search = $params['search'] ?? '';

    $start = (int) ($params['start'] ?? 0);

    $length = (int) ($params['length'] ?? 10);

    $baseQuery = Invoice::query();

    if ($isAdmin) {
      $baseQuery->withTrashed();
    }

    $totalData = (clone $baseQuery)->count();

    if (!empty($search)) {
      $baseQuery->where(function ($q) use ($search) {
        $q->where('customer_name', 'LIKE', "%{$search}%")
          ->orWhere('proforma_number', 'LIKE', "%{$search}%")
          ->orWhere('invoice_number', 'LIKE', "%{$search}%");
      });
    }

    $totalFiltered = (clone $baseQuery)->count();

    $rows = $baseQuery->latest()->offset($start)->limit($length)->get();

    $data = [];

    foreach ($rows as $row) {
      $data[] = [
        'id' => $row->id,
        'number' => $row->invoice_number ? $row->invoice_number : $row->proforma_number,
        'status' => $this->resolvePaymentStatus($row),
        'customer_name' => $row->customer_name,
        'customer_phone' => $row->customer_phone,
        'issued_date' => Carbon::parse($row->issued_date)->format('d F Y'),
        'grand_total' => $row->grand_total,
        'remaining_amount' => $row->remaining_amount,
        'deleted_at' => $row->deleted_at,
      ];
    }

    return [
      'draw' => (int) ($params['draw'] ?? 1),
      'recordsTotal' => $totalData,
      'recordsFiltered' => $totalFiltered,
      'code' => 200,
      'data' => $data,
    ];
  }

  private function resolvePaymentStatus(Invoice $invoice): string
  {
    $paidAmount = (float) $invoice->paid_amount;
    $remainingAmount = (float) $invoice->remaining_amount;
    $grandTotal = (float) $invoice->grand_total;

    if (
      empty($invoice->invoice_number) &&
      !empty($invoice->proforma_number) &&
      $invoice->valid_until &&
      $invoice->valid_until->isPast()
    ) {
      return 'EXPIRED';
    }

    if ($paidAmount <= 0) {
      return 'UNPAID';
    }

    if ($paidAmount >= $grandTotal || $remainingAmount <= 0) {
      return 'PAID';
    }

    if ($invoice->payment_terms === 'dp') {
      return 'DP PAID';
    }

    return 'PARTIAL PAID';
  }
}
