@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Invoices')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/notyf/notyf.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/notyf/notyf.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/notiflix/notiflix.js', 'resources/assets/vendor/libs/fancybox/fancybox.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/jquery-repeater/jquery-repeater.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/invoices/index.js'])
@endsection

@section('content')
    <h4 class="pt-3">Invoice List</h4>

    <div class="row g-4 mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-datatable table-responsive text-nowrap">
                    <table class="datatables-invoices table table-hover border-top">
                        <thead>
                            <tr>
                                <th width="1px">Number</th>
                                <th class="text-start">Status</th>
                                <th class="text-start">Customer</th>
                                <th class="text-start">Total</th>
                                <th class="text-start">Issued Date</th>
                                <th class="text-start">Balance</th>
                                <th width="1px">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalInvoice" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <form id="formInvoice" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="customer_name">Customer Name <strong
                                class="text-danger">**</strong></label>
                        <input type="text" id="customer_name" name="customer_name" class="form-control"
                            placeholder="John Doe" autofocus />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="customer_phone">Customer Phone <strong
                                class="text-danger">**</strong></label>
                        <input type="text" id="customer_phone" name="customer_phone" class="form-control"
                            placeholder="0813 xxxx xxxx xxxx" />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="customer_address">Customer Address <strong
                                class="text-danger">**</strong></label>
                        <input type="text" id="customer_address" name="customer_address" class="form-control"
                            placeholder="Perumahan Shoji Land, Cluster Estu" />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="payment_terms">Payment Terms <strong
                                class="text-danger">**</strong></label>
                        <select id="payment_terms" name="payment_terms" class="select2 form-select"
                            data-allow-clear="true"></select>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="reference">Reference <strong class="text-danger">**</strong></label>
                        <input type="text" id="reference" name="reference" class="form-control" placeholder="Whatsapp" />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="issued_date">Issued Date <strong
                                class="text-danger">**</strong></label>
                        <input type="text" id="issued_date" name="issued_date" class="form-control"
                            placeholder="YYYY-MM-DD" />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="valid_until">Due Date <strong class="text-danger">**</strong></label>
                        <input type="text" id="valid_until" name="valid_until" class="form-control"
                            placeholder="YYYY-MM-DD" />
                    </div>
                    <div class="col-12 invoice-repeater mb-4">
                        <div class="mb-4" data-repeater-list="item-invoice">
                            <div class="repeater-wrapper pt-0 pt-md-9" data-repeater-item>
                                <div class="d-flex border rounded position-relative pe-0">
                                    <div class="row w-100 p-6 g-6">
                                        <div class="col-md-5 col-12 mb-md-0 mb-4">
                                            <p class="h6 repeater-title">Item</p>
                                            <select name="product_id" class="select2 select-product form-select"
                                                data-allow-clear="true"></select>
                                        </div>
                                        <div class="col-md-1 col-12 mb-md-0 mb-4">
                                            <p class="h6 repeater-title">Qty</p>
                                            <input type="text" name="quantity" class="form-control item-qty"
                                                placeholder="1" />
                                        </div>
                                        <div class="col-md-1 col-12 mb-md-0 mb-4">
                                            <div class="h6 repeater-title">UoM</div>
                                            <input type="text" name="uom" class="form-control item-uom"
                                                placeholder="pcs" />
                                        </div>
                                        <div class="col-md-2 col-12 mb-md-0 mb-4">
                                            <p class="h6 repeater-title">Cost</p>
                                            <input type="text" name="unit_price" class="form-control mb-4 unit-price"
                                                placeholder="215000" />
                                        </div>
                                        <div class="col-md-1 col-12 mb-md-0 mb-4">
                                            <div class="h6 repeater-title">Discount</div>
                                            <input type="text" name="discount" class="form-control item-discount"
                                                placeholder="5" min="0" max="100" />
                                        </div>
                                        <div class="col-md-2 col-12 pe-0">
                                            <p class="h6 repeater-title">Price</p>
                                            <p class="mb-0 mt-6 line-total-text">Rp 0</p>
                                            <input type="hidden" name="line_total" class="line-total" />
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex flex-column align-items-center justify-content-between border-start p-2">
                                        <i class="icon-base bx bx-x icon-lg cursor-pointer" data-repeater-delete></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-sm btn-primary" data-repeater-create><i
                                        class="icon-base bx bx-plus icon-xs me-1_5"></i>Add Item</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-12 invoice-calculations">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="w-px-100">Subtotal:</span>
                            <span class="fw-medium text-heading invoice-subtotal">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="w-px-100">Discount:</span>
                            <span class="fw-medium text-heading invoice-discount">Rp 0</span>
                        </div>
                        <hr class="my-2" />
                        <div class="d-flex justify-content-between">
                            <span class="w-px-100">Total:</span>
                            <span class="fw-medium text-heading invoice-total">Rp 0</span>
                        </div>
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
