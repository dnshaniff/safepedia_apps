<?php

namespace App\Domains\Invoices\InvoicePayments\Queries;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class IndexService

{
  public function execute(Invoice $invoice, array $params): array
  {
    $start = (int) ($params['start'] ?? 0);

    $length = (int) ($params['length'] ?? 10);

    $baseQuery = InvoicePayment::query()->where('invoice_id', $invoice->id);
    $totalData = (clone $baseQuery)->count();

    $totalFiltered = (clone $baseQuery)->count();

    $rows = $baseQuery->latest()->offset($start)->limit($length)->get();

    $data = [];
    $ids = $start;

    foreach ($rows as $row) {
      $data[] = [
        'fake_id' => ++$ids,
        'id' => $row->id,
        'payment_date' => Carbon::parse($row->payment_date)->format('d F Y'),
        'amount' => $row->amount,
        'payment_method' => $row->payment_method,
        'notes' => $row->notes,
        'file_name' => $row->file_name,
        'file_path' => $row->file_path ? Storage::url($row->file_path) : null,
        'creator' => $row->creator?->name ?? '-',
        'editor' => $row->editor?->name ?? '-',
        'created_at' => $row->created_at,
        'updated_at' => $row->updated_at,
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
}
