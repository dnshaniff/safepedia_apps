@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Dashboard')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.scss', 'resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/pages/dashboard.js'])
@endsection

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">
                                {{ number_format($cards['total_construction']) }}
                            </h2>
                            <span class="text-muted">
                                Total Construction
                            </span>
                        </div>

                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded">
                                <i class="bx bx-buildings fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">
                                {{ number_format($cards['progress']) }}
                            </h2>
                            <span class="text-muted">
                                In Progress
                            </span>
                        </div>

                        <div class="avatar">
                            <div class="avatar-initial bg-label-warning rounded">
                                <i class="bx bx-loader-circle fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">
                                {{ number_format($cards['approved']) }}
                            </h2>
                            <span class="text-muted">
                                Approved
                            </span>
                        </div>

                        <div class="avatar">
                            <div class="avatar-initial bg-label-success rounded">
                                <i class="bx bx-check-double fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Construction Statistics</h5>
                        <p class="card-subtitle mb-0">
                            Total construction this month
                        </p>
                    </div>

                    <div style="width: 180px;">
                        <input type="month" id="chartPeriod" class="form-control">
                    </div>
                </div>

                <div class="card-body">
                    <div id="constructionStatisticsChart"></div>
                </div>
            </div>
        </div>

        <h4 class="py-3 mb-2"></h4>

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
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
