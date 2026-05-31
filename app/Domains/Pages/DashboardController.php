<?php

namespace App\Domains\Pages;

use App\Http\Controllers\Controller;
use App\Models\Invoice;

class DashboardController extends Controller
{
  public function view()
  {
    $proformaCount = Invoice::whereNull('invoice_number')->count();

    $proformaValue = Invoice::whereNull('invoice_number')->sum('grand_total');

    $invoiceCount = Invoice::whereNotNull('invoice_number')->count();

    $invoiceValue = Invoice::whereNotNull('invoice_number')->sum('grand_total');

    $amountPaid = Invoice::whereNotNull('invoice_number')->sum('paid_amount');

    $outstandingBalance = Invoice::whereNotNull('invoice_number')->sum('remaining_amount');

    return view(
      'content.pages.dashboard',
      compact(
        'proformaCount',
        'proformaValue',
        'invoiceCount',
        'invoiceValue',
        'amountPaid',
        'outstandingBalance'
      )
    );
  }
}
