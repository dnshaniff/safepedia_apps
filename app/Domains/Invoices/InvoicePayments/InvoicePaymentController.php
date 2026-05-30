<?php

namespace App\Domains\Invoices\InvoicePayments;

use App\Domains\Invoices\InvoicePayments\Queries\IndexService;
use App\Domains\Invoices\InvoicePayments\Requests\StoreRequest;
use App\Domains\Invoices\InvoicePayments\Requests\UpdateRequest;
use App\Domains\Invoices\InvoicePayments\Services\StoreService;
use App\Domains\Invoices\InvoicePayments\Services\TerminateService;
use App\Domains\Invoices\InvoicePayments\Services\UpdateService;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class InvoicePaymentController extends Controller
{
  public function index(Invoice $invoice, Request $request, IndexService $service)
  {
    return response()->json(
      $service->execute($invoice, [
        'search' => $request->input('search.value'),
        'start' => $request->input('start'),
        'length' => $request->input('length'),
        'draw' => $request->input('draw')
      ])
    );
  }

  public function store(Invoice $invoice, StoreRequest $request, StoreService $service)
  {
    try {
      $service->execute($invoice, $request->validated());
      return response()->json([
        'status' => 'success',
        'message' => 'Payment created succefully',
        'invoice' => [
          'paid_amount' => $invoice->fresh()->paid_amount,
          'remaining_amount' => $invoice->fresh()->remaining_amount,
        ]
      ], 201);
    } catch (Exception $e) {
      return response()->json(['status' => 'info', 'message' => $e->getMessage()], 422);
    } catch (Throwable $e) {
      Log::error('Unexpected error while creating payment', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function edit(Invoice $invoice, InvoicePayment $invoicePayment)
  {
    return response()->json([
      'id' => $invoicePayment->id,
      'payment_date' => $invoicePayment->payment_date,
      'amount' => $invoicePayment->amount,
      'payment_method' => $invoicePayment->payment_method,
      'notes' => $invoicePayment->notes,
      'file_name' => $invoicePayment->file_name,
      'file_path' => $invoicePayment->file_path ? asset('storage/' . $invoicePayment->file_path) : null,
    ]);
  }

  public function update(UpdateRequest $request, Invoice $invoice, InvoicePayment $invoicePayment, UpdateService $service)
  {
    try {
      $latestPayment = $invoice->payments()->latest()->first();

      if (!$latestPayment || $latestPayment->id !== $invoicePayment->id) {
        return response()->json(['status' => 'info', 'message' => 'Only the latest payment can be updated'], 422);
      }

      $service->execute($invoice, $invoicePayment, $request->validated());

      return response()->json([
        'status' => 'success',
        'message' => 'Invoice updated succefully',
        'invoice' => [
          'paid_amount' => $invoice->fresh()->paid_amount,
          'remaining_amount' => $invoice->fresh()->remaining_amount,
        ]
      ], 201);
    } catch (Exception $e) {
      return response()->json(['status' => 'info', 'message' => $e->getMessage()], 422);
    } catch (Throwable $e) {
      Log::error('Unexpected error while updating payment', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function destroy(Invoice $invoice, InvoicePayment $invoicePayment, TerminateService $service)
  {
    try {
      $latestPayment = $invoice->payments()->latest()->first();

      if (!$latestPayment || $latestPayment->id !== $invoicePayment->id) {
        return response()->json(['status' => 'info', 'message' => 'Only the latest payment can be deleted'], 422);
      }

      $service->delete($invoice, $invoicePayment);

      return response()->json([
        'status' => 'success',
        'message' => 'Payment deleted successfully',
        'invoice' => [
          'paid_amount' => $invoice->fresh()->paid_amount,
          'remaining_amount' => $invoice->fresh()->remaining_amount,
        ]
      ], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while deleting payment', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }
}
