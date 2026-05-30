<?php

namespace App\Domains\Invoices;

use App\Domains\Invoices\Queries\IndexService;
use App\Domains\Invoices\Requests\StoreRequest;
use App\Domains\Invoices\Requests\UpdateRequest;
use App\Domains\Invoices\Services\PdfService;
use App\Domains\Invoices\Services\StoreService;
use App\Domains\Invoices\Services\TerminateService;
use App\Domains\Invoices\Services\UpdateService;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class InvoiceController extends Controller
{
  public function view()
  {
    return view('content.invoices.index');
  }

  public function index(Request $request, IndexService $service)
  {
    return response()->json(
      $service->execute([
        'search' => $request->input('search.value'),
        'start' => $request->input('start'),
        'length' => $request->input('length'),
        'draw' => $request->input('draw')
      ])
    );
  }

  public function store(StoreRequest $request, StoreService $service)
  {
    try {
      $service->execute($request->validated());
      return response()->json(['status' => 'success', 'message' => 'Invoice created succefully'], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while creating invoice', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function show(Invoice $invoice)
  {
    $invoice->load('items.product');

    return view('content.invoices.show', compact('invoice'));
  }

  public function edit(Invoice $invoice)
  {
    $invoice->load('items.product');

    return response()->json($invoice, 200);
  }

  public function update(UpdateRequest $request, Invoice $invoice, UpdateService $service)
  {
    try {
      if ($invoice->payments()->exists()) {
        return response()->json(['status' => 'danger', 'message' => 'Invoice cannot be updated because payment has already been recorded'], 422);
      }

      $service->execute($invoice, $request->validated());

      return response()->json(['status' => 'success', 'message' => 'Invoice updated succefully'], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while updating invoice', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function pdf(Invoice $invoice, PdfService $service)
  {
    try {
      return $service->execute($invoice);
    } catch (Throwable $e) {
      Log::error('Unexpected error while generating invoice PDF', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while generating invoice PDF'], 500);
    }
  }

  public function destroy(Invoice $invoice, TerminateService $service)
  {
    try {
      if ($invoice->payments()->exists()) {
        return response()->json(['status' => 'danger', 'message' => 'Invoice cannot be deleted because payment has already been recorded'], 422);
      }

      $service->delete($invoice);

      return response()->json(['status' => 'success', 'message' => "Invoice deleted successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while deleting brand', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function restore(string $id, TerminateService $service)
  {
    $invoice = Invoice::withTrashed()->findOrFail($id);

    try {
      if ($invoice->trashed()) {
        $service->restore($invoice);

        return response()->json(['status' => 'success', 'message' => "Invoice restored successfully"], 200);
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 422);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while restoring brand', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function force(string $id, TerminateService $service)
  {
    $brand = Invoice::withTrashed()->findOrFail($id);

    try {
      if ($brand->trashed()) {
        $service->force($brand);

        return response()->json(['status' => 'success', 'message' => "Invoice permanent delete successfully"], 200);
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 422);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while forcing brand deletion', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }
}
