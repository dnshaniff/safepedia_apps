@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Invoice ' . ($invoice->invoice_number ?: $invoice->proforma_number))

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/notyf/notyf.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/notyf/notyf.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/notiflix/notiflix.js', 'resources/assets/vendor/libs/fancybox/fancybox.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/invoices/show.js'])
@endsection

@section('content')
    <div class="card invoice-preview-card p-sm-10 p-6">
        <div class="card-body invoice-preview-header rounded">
            <div
                class="d-flex justify-content-between flex-xl-row flex-md-column flex-column align-items-xl-start align-items-start">
                <div class="mb-xl-0 mb-6 text-heading">
                    <div class="d-flex mb-6 gap-2 align-items-center">
                        <img src="{{ asset('assets/img/branding/logo-dna.png') }}" alt="DNA Lighting" style="height: 56px;">
                        <div>
                            <h5 class="mb-1 fw-bold">DNA LIGHTING</h5>
                            <p class="mb-1">Spesialis APILL, Rambu, dan Tiang</p>
                            <p class="mb-0">0852 1000 1116</p>
                        </div>
                    </div>
                </div>

                <div class="text-xl-end text-start">
                    <h5 class="mb-4">
                        {{ $invoice->invoice_number ? 'Invoice' : 'Proforma Invoice' }}
                    </h5>
                    <p class="mb-1 text-heading">
                        <span>No:</span>
                        <span class="fw-medium">{{ $invoice->invoice_number ?: $invoice->proforma_number }}</span>
                    </p>
                    <p class="mb-1 text-heading">
                        <span>Created Date:</span>
                        <span class="fw-medium">{{ $invoice->created_at->format('d F Y, H:i') }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="card-body px-0">
            <div class="row">
                <div class="col-xl-6 col-md-12 col-12 mb-xl-0 mb-6">
                    <h6>Customer:</h6>
                    <p class="mb-1">{{ $invoice->customer_name }}</p>
                    <p class="mb-1">{{ $invoice->customer_phone }}</p>
                    <p class="mb-0">{{ $invoice->customer_address }}</p>
                </div>

                <div class="col-xl-6 col-md-12 col-12 text-xl-end text-start">
                    <table class="ms-xl-auto">
                        <tbody>
                            <tr>
                                <td class="pe-4">Issued Date:</td>
                                <td class="fw-medium">{{ $invoice->issued_date?->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td class="pe-4">Due Date:</td>
                                <td class="fw-medium">{{ $invoice->valid_until?->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td class="pe-4">Payment Terms:</td>
                                <td class="fw-medium">{{ $invoice->reference ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="pe-4">Reference:</td>
                                <td class="fw-medium">{{ strtoupper($invoice->payment_terms) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="table-responsive border border-bottom-0 border-top-0 rounded">
            <table class="table m-0">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">UoM</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->items as $item)
                        <tr>
                            <td class="text-heading">
                                {{ $item->product->name ?? ($item->product->product_name ?? '-') }}
                            </td>
                            <td class="text-center">
                                {{ rtrim(rtrim(number_format($item->quantity, 2, '.', ''), '0'), '.') }}</td>
                            <td class="text-center">{{ $item->uom }}</td>
                            <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="text-end">{{ rtrim(rtrim(number_format($item->discount, 2, '.', ''), '0'), '.') }}%
                            </td>
                            <td class="text-end">Rp {{ number_format($item->line_total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="table-responsive">
            <table class="table m-0 table-borderless">
                <tbody>
                    <tr>
                        <td class="align-top pe-6 ps-0 py-6 text-body"></td>
                        <td class="px-0 py-6 w-px-100">
                            <p class="mb-2">Subtotal:</p>
                            <p class="mb-2 pb-2">Discount:</p>
                            <p class="mb-2 border-bottom pb-2">Amount Paid:</p>
                            <p class="mb-0">Balance Due:</p>
                        </td>
                        <td class="text-end px-0 py-6 w-px-150 fw-medium text-heading">
                            <p class="mb-2">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</p>
                            <p class="mb-2 pb-2">Rp
                                {{ number_format($invoice->discount_total, 0, ',', '.') }}</p>
                            <p id="paid-value" class="mb-2 border-bottom pb-2">Rp
                                {{ number_format($invoice->paid_amount, 0, ',', '.') }}</p>
                            <p id="balance-value" class="mb-0">Rp
                                {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <h4 class="pt-3">Payment History</h4>

    <div class="row g-4 mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-datatable table-responsive text-nowrap">
                    <table class="datatables-payments table table-hover border-top">
                        <thead>
                            <tr>
                                <th width="1px">#</th>
                                <th class="text-start">Date</th>
                                <th class="text-start">Amount</th>
                                <th class="text-start">Method</th>
                                <th class="text-start">File</th>
                                <th class="text-start">Created Date</th>
                                <th class="text-start">Updated Date</th>
                                <th width="1px">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPayment" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form id="formPayment" class="modal-content" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="payment_date">Payment Date <strong
                                class="text-danger">**</strong></label>
                        <input type="text" id="payment_date" name="payment_date" class="form-control"
                            placeholder="YYYY-MM-DD" autofocus />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="amount">Amount <strong class="text-danger">**</strong></label>
                        <input type="text" id="amount" name="amount" class="form-control"
                            placeholder="215000" />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="payment_method">Payment Method <strong
                                class="text-danger">**</strong></label>
                        <select id="payment_method" name="payment_method" class="select2 form-select"
                            data-allow-clear="true"></select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="file_upload">Upload File <strong
                                class="text-danger">**</strong></label>
                        <input id="file_upload" name="file_upload" class="form-control" type="file"
                            accept=".jpg,.jpeg,.png">
                    </div>
                    <div id="uploadPreviewWrapper" class="mt-3 d-none">
                        <a href="" id="uploadPreviewLink" data-fancybox="file-upload">
                            <img id="uploadPreview" src="" class="rounded border"
                                style="width: 90px; height: 90px; object-fit: contain;">
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="reset" class="btn btn-label-danger" data-bs-dismiss="modal"
                        aria-label="Close">Cancel</button>
                </div>
            </form>
        </div>
    </div>
@endsection
