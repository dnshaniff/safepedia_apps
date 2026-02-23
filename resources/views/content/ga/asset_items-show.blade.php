@extends('layouts/layoutMaster')

@section('title', 'Asset Detail')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/notyf/notyf.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss'])
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-profile.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/notyf/notyf.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/notiflix/notiflix.js', 'resources/assets/vendor/libs/fancybox/fancybox.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/ga/asset_items-show.js'])
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-4 col-lg-5 col-md-5">
            <div class="card mb-6">
                <div class="card-body">
                    <small class="card-text text-uppercase text-body-secondary small">Detail</small>
                    <ul class="list-unstyled my-3 py-1">
                        <li class="d-flex align-items-center mb-4">
                            <i class="icon-base bx bx-buildings"></i>
                            <span class="fw-medium mx-2">Company:</span>
                            <span>{{ $assetItem->company->company_name }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-4">
                            <i class="icon-base bx bx-category"></i>
                            <span class="fw-medium mx-2">Type:</span>
                            <span>{{ $assetItem->type->type_name }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-4">
                            <i class="icon-base bx bx-copyright"></i>
                            <span class="fw-medium mx-2">Brand:</span>
                            <span>{{ $assetItem->item_brand ?? '-' }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-4">
                            <i class="icon-base bx bx-chip"></i>
                            <span class="fw-medium mx-2">Model:</span>
                            <span>{{ $assetItem->item_model ?? '-' }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-4">
                            <i class="icon-base bx bx-detail"></i>
                            <span class="fw-medium mx-2">Specification:</span>
                            <span>{{ $assetItem->item_specification }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-4">
                            <i class="icon-base bx bx-barcode"></i>
                            <span class="fw-medium mx-2">Serial:</span>
                            <span>{{ $assetItem->serial_number ?? '-' }}</span>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="icon-base bx bx-check-circle"></i>
                            <span class="fw-medium mx-2">Status:</span>
                            <span>{{ $assetItem->item_status }}</span>
                        </li>

                    </ul>
                </div>
            </div>
            <div class="card mb-6">
                <div class="card-body">
                    <small class="card-text text-uppercase text-body-secondary small">Audit Information</small>
                    <ul class="list-unstyled mb-0 mt-3 pt-1">
                        <li class="d-flex align-items-center"><i class="icon-base bx bx-user-check"></i><span
                                class="fw-medium mx-2">Created By:</span> <span>
                                {{ $assetItem->creator?->display_name ?? '-' }}</span></li>
                    </ul>
                    <ul class="list-unstyled mb-0 mt-3 pt-1">
                        <li class="d-flex align-items-center"><i class="icon-base bx bx-calendar"></i><span
                                class="fw-medium mx-2">Created At:</span> <span>
                                {{ $assetItem->created_at->format('d F Y') }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-lg-7 col-md-7">
            <div class="card card-action mb-6">
                <div class="card-header align-items-center">
                    <h5 class="card-action-title mb-0">Picture & QR</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7 mb-4 mb-md-0 text-center">
                            <a data-fancybox="asset-media"
                                href="https://www.notebookcheck.net/fileadmin/_processed_/d/1/csm_mba_15_m4_case_06_d2412c23a3.jpg">
                                <img src="https://www.notebookcheck.net/fileadmin/_processed_/d/1/csm_mba_15_m4_case_06_d2412c23a3.jpg"
                                    class="img-fluid rounded border" style="max-height: 350px; object-fit: contain;">
                            </a>
                        </div>

                        <div class="col-md-5 text-center">
                            <a data-fancybox="asset-media" href="{{ $qrUrl }}">
                                <img src="{{ $qrUrl }}" class="img-fluid rounded border" style="max-height: 350px;">
                            </a>
                            <div class="mt-3">
                                <small class="text-muted d-block">
                                    Scan to view public asset page
                                </small>
                                <div class="fw-medium mt-1">
                                    {{ $assetItem->item_code }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
