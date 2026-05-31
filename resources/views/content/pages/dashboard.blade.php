@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Dashboard')

@section('content')
    <h4 class="mb-4">Dashboard</h4>

    <div class="row g-4">

        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-2">Total Proforma</h6>

                            <h4 class="mb-1">
                                {{ number_format($proformaCount) }}
                            </h4>

                            <small class="text-muted">
                                Active proforma documents
                            </small>
                        </div>

                        <div class="avatar">
                            <div class="avatar-initial bg-label-info rounded">
                                <i class="bx bx-file"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-2">Proforma Value</h6>

                            <h4 class="mb-1">
                                Rp {{ number_format($proformaValue, 0, ',', '.') }}
                            </h4>

                            <small class="text-muted">
                                Total value of open quotations
                            </small>
                        </div>

                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded">
                                <i class="bx bx-wallet"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-2">Total Invoice</h6>

                            <h4 class="mb-1">
                                {{ number_format($invoiceCount) }}
                            </h4>

                            <small class="text-muted">
                                Issued invoice documents
                            </small>
                        </div>

                        <div class="avatar">
                            <div class="avatar-initial bg-label-secondary rounded">
                                <i class="bx bx-receipt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-2">Invoice Value</h6>

                            <h4 class="mb-1">
                                Rp {{ number_format($invoiceValue, 0, ',', '.') }}
                            </h4>

                            <small class="text-muted">
                                Total value of issued invoices
                            </small>
                        </div>

                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded">
                                <i class="bx bx-money"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-2">Amount Paid</h6>

                            <h4 class="mb-1">
                                Rp {{ number_format($amountPaid, 0, ',', '.') }}
                            </h4>

                            <small class="text-muted">
                                Total payments received
                            </small>
                        </div>

                        <div class="avatar">
                            <div class="avatar-initial bg-label-success rounded">
                                <i class="bx bx-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-2">Outstanding Balance</h6>

                            <h4 class="mb-1">
                                Rp {{ number_format($outstandingBalance, 0, ',', '.') }}
                            </h4>

                            <small class="text-muted">
                                Remaining receivables
                            </small>
                        </div>

                        <div class="avatar">
                            <div class="avatar-initial bg-label-warning rounded">
                                <i class="bx bx-time-five"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
