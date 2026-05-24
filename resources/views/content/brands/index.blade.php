@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Brands')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/notyf/notyf.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/notyf/notyf.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/notiflix/notiflix.js', 'resources/assets/vendor/libs/fancybox/fancybox.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/brands/index.js'])
@endsection

@section('content')
    <h4 class="pt-3">Brand List</h4>

    <div class="row g-4 mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-datatable table-responsive text-nowrap">
                    <table class="datatables-brands table table-hover border-top">
                        <thead>
                            <tr>
                                <th width="1px">#</th>
                                <th class="text-start" width="60%">Name</th>
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

    <div class="modal fade" id="modalBrand" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formBrand" class="modal-content" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="name">Name <strong class="text-danger">**</strong></label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Philips"
                            autofocus />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="file_upload">Logo <strong class="text-danger">**</strong></label>
                        <input id="file_upload" name="file_upload" class="form-control" type="file" accept=".png">
                    </div>
                    <div id="logoPreviewWrapper" class="mt-3 d-none">
                        <a href="" id="logoPreviewLink" data-fancybox="brand-logo">
                            <img id="logoPreview" src="" class="rounded border"
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
