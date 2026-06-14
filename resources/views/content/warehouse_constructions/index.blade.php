@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Warehouse Constructions')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/notyf/notyf.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss', 'resources/assets/vendor/libs/dropzone/dropzone.scss', 'resources/assets/vendor/libs/leaflet/leaflet.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/notyf/notyf.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/notiflix/notiflix.js', 'resources/assets/vendor/libs/dropzone/dropzone.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/jquery-repeater/jquery-repeater.js', 'resources/assets/vendor/libs/leaflet/leaflet.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/warehouse_constructions/index.js'])
@endsection

@section('content')
    <h4 class="py-3 mb-2">Approvals List</h4>

    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="datatables-constructions table table-hover border-top text-start">
                <thead>
                    <tr>
                        <th class="text-start">Number</th>
                        <th class="text-start">Warehouse Name</th>
                        <th class="text-start">Total Budget</th>
                        <th class="text-start">Approval</th>
                        <th class="text-start">Status</th>
                        <th class="text-start">Created At</th>
                        <th width="1px">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalConstruction" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <form id="formConstruction" class="modal-content" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Construction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="warehouse_name">Warehouse Name <strong
                                class="text-danger">**</strong></label>
                        <input type="text" id="warehouse_name" name="warehouse_name" class="form-control"
                            placeholder="Gudang Karplindo" autofocus />
                    </div>
                    <div class="col-12">
                        <div class="leaflet-map" id="dragMap"></div>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="latitude">Latitude <strong class="text-danger">**</strong></label>
                        <input type="text" id="latitude" name="latitude" class="form-control" placeholder="-6.200000"
                            readonly />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="longitude">Longitude <strong class="text-danger">**</strong></label>
                        <input type="text" id="longitude" name="longitude" class="form-control" placeholder="106.816666"
                            readonly />
                    </div>
                    <div class="col-12">
                        <label class="form-label d-block mb-2">Documents <strong class="text-danger">**</strong></label>
                        <div id="documentDropzone" class="dropzone border rounded p-4 text-center">
                            <div class="dz-message">
                                <div class="mt-2 fw-medium">Drop document here</div>
                                <small class="text-muted">PDF up to 2MB</small>
                            </div>
                        </div>
                        <input type="hidden" name="construction_document" id="construction_document" />
                    </div>
                    <div class="col-12 budget-repeater mt-3 mb-4">
                        <div class="mb-4" data-repeater-list="item-budget">
                            <div class="repeater-wrapper pt-0 pt-md-4 mb-3" data-repeater-item>
                                <div class="d-flex border rounded position-relative pe-0">
                                    <div class="row w-100 p-4 g-4">
                                        <div class="col-md-4 col-12 mb-md-0 mb-4">
                                            <p class="h6 repeater-title">Item</p>
                                            <input type="text" name="item_name" class="form-control item-item_name"
                                                placeholder="Pondasi" />
                                        </div>
                                        <div class="col-md-2 col-12 mb-md-0 mb-4">
                                            <p class="h6 repeater-title">Qty</p>
                                            <input type="number" min="1" name="quantity"
                                                class="form-control item-qty" placeholder="1" />
                                        </div>
                                        <div class="col-md-3 col-12 mb-md-0 mb-4">
                                            <p class="h6 repeater-title">Cost</p>
                                            <input type="text" name="unit_price" class="form-control unit-price"
                                                placeholder="215000" />
                                        </div>
                                        <div class="col-md-3 col-12 pe-0">
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
                    <div class="col-md-6 col-12 construction-calculations">
                        <hr class="my-2" />
                        <div class="d-flex justify-content-between">
                            <span class="w-px-100">Total:</span>
                            <span class="fw-medium text-heading construction-total">Rp 0</span>
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
