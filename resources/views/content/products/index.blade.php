@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Products')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/notyf/notyf.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss', 'resources/assets/vendor/libs/dropzone/dropzone.scss'])
@endsection

@section('page-style')
    <style>
        .dz-thumbnail {
            position: relative;
            overflow: hidden;
            border-radius: 12px 12px 0 0;
        }

        .thumbnail-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .35);

            display: flex;
            align-items: center;
            justify-content: center;

            opacity: 0;
            transition: .2s ease;
        }

        .dz-thumbnail:hover .thumbnail-overlay {
            opacity: 1;
        }

        .thumbnail-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 20;
        }
    </style>
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/notyf/notyf.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/notiflix/notiflix.js', 'resources/assets/vendor/libs/fancybox/fancybox.js', 'resources/assets/vendor/libs/dropzone/dropzone.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/products/index.js'])
@endsection

@section('content')
    <h4 class="pt-3">Product List</h4>

    <div class="row g-4 mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-datatable table-responsive text-nowrap">
                    <table class="datatables-products table table-hover border-top">
                        <thead>
                            <tr>
                                <th width="1px">#</th>
                                <th class="text-start" width="40%">Name</th>
                                <th class="text-start" width="10%">Brand</th>
                                <th class="text-start" width="10%">Status</th>
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

    <div class="modal fade" id="modalProduct" tabindex="-1" data-bs-backdrop="static" enctype="multipart/form-data">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form id="formProduct" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="name">Name <strong class="text-danger">**</strong></label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Ballast Trafo"
                            autofocus />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="description">Description <strong
                                class="text-danger">**</strong></label>
                        <textarea id="description" name="description" rows="2" class="form-control autosize"></textarea>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="brand_id">Brand <strong class="text-danger">**</strong></label>
                        <select id="brand_id" name="brand_id" class="select2 form-select" data-allow-clear="true"></select>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="status">Status <strong class="text-danger">**</strong></label>
                        <select id="status" name="status" class="select2 form-select" data-allow-clear="true"></select>
                    </div>
                    <div class="col-12">
                        <label class="form-label d-block mb-2">Product Images <strong
                                class="text-danger">**</strong></label>
                        <div id="productDropzone" class="dropzone border rounded p-4 text-center">
                            <div class="dz-message">
                                <div class="mt-2 fw-medium">Drop product images here</div>
                                <small class="text-muted">PNG, JPG, JPEG, WEBP up to 4MB</small>
                            </div>
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
