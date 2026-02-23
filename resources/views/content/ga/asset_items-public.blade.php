@php
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Asset Detail')

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
                        <li class="d-flex align-items-center">
                            <i class="icon-base bx bx-check-circle"></i>
                            <span class="fw-medium mx-2">Status:</span>
                            <span>{{ $assetItem->item_status }}</span>
                        </li>

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
                    <div class="col-md-7 mb-4 mb-md-0 text-center">
                        <a data-fancybox="asset-media"
                            href="https://www.notebookcheck.net/fileadmin/_processed_/d/1/csm_mba_15_m4_case_06_d2412c23a3.jpg">
                            <img src="https://www.notebookcheck.net/fileadmin/_processed_/d/1/csm_mba_15_m4_case_06_d2412c23a3.jpg"
                                class="img-fluid rounded border" style="max-height: 350px; object-fit: contain;">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
