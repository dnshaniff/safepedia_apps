@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Inventory')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/notyf/notyf.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/notyf/notyf.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/notiflix/notiflix.js', 'resources/assets/vendor/libs/fancybox/fancybox.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/ga/asset_items.js'])
@endsection

@section('content')
    <h4 class="pt-3">Asset Inventory</h4>

    <div class="row g-4 mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-datatable table-responsive text-nowrap">
                    <table class="datatables-items table table-hover border-top">
                        <thead>
                            <tr>
                                <th width="1px">#</th>
                                <th class="text-start">Code</th>
                                <th class="text-start">PT</th>
                                <th class="text-start">Type</th>
                                <th class="text-start">Specification</th>
                                <th class="text-start">Status</th>
                                <th width="1px">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalItem" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <form id="formItem" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="asset_type_id">Type <strong class="text-danger">**</strong></label>
                        <select id="asset_type_id" name="asset_type_id" class="select2 form-select"
                            data-allow-clear="true"></select>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="item_brand">Brand <strong class="text-danger">**</strong></label>
                        <input type="text" class="form-control" id="item_brand" name="item_brand" placeholder="Lenovo" />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="serial_number">Serial Number</label>
                        <input type="text" class="form-control" id="serial_number" name="serial_number"
                            placeholder="GWHVV99R0Y" />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="item_model">Model</label>
                        <input type="text" class="form-control" id="item_model" name="item_model"
                            placeholder="Ideapad Slim 3" />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="item_specification">Specification <strong
                                class="text-danger">**</strong></label>
                        <input type="text" class="form-control" id="item_specification" name="item_specification"
                            placeholder="i7 16GB/512GB" />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="company_id">Company <strong class="text-danger">**</strong></label>
                        <select id="company_id" name="company_id" class="select2 form-select"
                            data-allow-clear="true"></select>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="item_status">Status <strong class="text-danger">**</strong></label>
                        <select id="item_status" name="item_status" class="select2 form-select"
                            data-allow-clear="true"></select>
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
