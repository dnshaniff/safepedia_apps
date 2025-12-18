@extends('layouts/layoutMaster')

@section('title', 'Employee Detail')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/notyf/notyf.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss'])
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-profile.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/notyf/notyf.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/notiflix/notiflix.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/hr/employees-show.js'])
@endsection

@section('content')
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card my-6">
                <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-8">
                    <div class="flex-shrink-0 mt-1 mx-sm-0 mx-auto">
                        <img src="{{ asset('assets/img/avatars/avatar.png') }}" alt="user image"
                            class="d-block h-auto ms-0 ms-sm-6 rounded-3 user-profile-img" />
                    </div>
                    <div class="flex-grow-1 mt-3 mt-lg-5">
                        <div
                            class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
                            <div class="user-profile-info">
                                <h4 class="mb-2 mt-lg-7">{{ $employee->full_name }}</h4>
                                <ul
                                    class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4 mt-4">
                                    <li class="list-inline-item"><i class="icon-base bx bx-sitemap me-2 align-top"></i><span
                                            class="fw-medium">{{ $employee->orgUnit->unit_name }}</span></li>
                                    <li class="list-inline-item"><i
                                            class="icon-base bx bx-briefcase me-2 align-top"></i><span
                                            class="fw-medium">{{ $employee->jobTitle->title_name }}</span></li>
                                    <li class="list-inline-item"><i
                                            class="icon-base bx bx-calendar me-2 align-top"></i><span
                                            class="fw-medium">Joined {{ $employee->join_date->format('d F Y') }}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Header -->

    <!-- User Profile Content -->
    <div class="row">
        <div class="col-xl-4 col-lg-5 col-md-5">
            <!-- About User -->
            <div class="card mb-6">
                <div class="card-body">
                    <small class="card-text text-uppercase text-body-secondary small">Employment</small>
                    <ul class="list-unstyled my-3 py-1">
                        <li class="d-flex align-items-center mb-4"><i class="icon-base bx bx-id-card"></i><span
                                class="fw-medium mx-2">ID:</span> <span>{{ $employee->employee_code }}</span></li>
                        <li class="d-flex align-items-center mb-4"><i class="icon-base bx bx-badge-check"></i><span
                                class="fw-medium mx-2">Status:</span> <span>{{ $employee->employment_type }}</span></li>
                        <li class="d-flex align-items-center mb-4"><i class="icon-base bx bx-buildings"></i><span
                                class="fw-medium mx-2">Company:</span>
                            <span>{{ $employee->company->company_name ?? '-' }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-4"><i class="icon-base bx bx-user-voice"></i><span
                                class="fw-medium mx-2">Direct Manager:</span>
                            <span>{{ $employee->manager ? $employee->manager->full_name : '-' }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-2"><i class="icon-base bx bx-user-pin"></i><span
                                class="fw-medium mx-2">HRBP:</span>
                            <span>{{ $employee->hrbp ? $employee->hrbp->full_name : '-' }}</span>
                        </li>
                    </ul>
                    <small class="card-text text-uppercase text-body-secondary small">Contacts</small>
                    <ul class="list-unstyled my-3 py-1">
                        <li class="d-flex align-items-center mb-4"><i class="icon-base bx bx-phone"></i><span
                                class="fw-medium mx-2">Contact:</span> <span>{{ $employee->phone_number ?? '-' }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-4"><i class="icon-base bx bx-envelope"></i><span
                                class="fw-medium mx-2">Office Email:</span>
                            <span>{{ $employee->office_email ?? '-' }}</span>
                        </li>
                        <li class="d-flex align-items-center"><i class="icon-base bx bx-mail-send"></i><span
                                class="fw-medium mx-2">Personal Email:</span>
                            <span>{{ $employee->personal_email ?? '-' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <!--/ About User -->
            <!-- Profile Overview -->
            <div class="card">
                <div class="card-body">
                    <small class="card-text text-uppercase text-body-secondary small">Audit Information</small>
                    <ul class="list-unstyled mb-0 mt-3 pt-1">
                        <li class="d-flex align-items-center"><i class="icon-base bx bx-user-check"></i><span
                                class="fw-medium mx-2">Created By:</span> <span>
                                {{ $employee->creator?->display_name ?? '-' }}</span></li>
                    </ul>
                    <ul class="list-unstyled mb-0 mt-3 pt-1">
                        <li class="d-flex align-items-center"><i class="icon-base bx bx-calendar"></i><span
                                class="fw-medium mx-2">Created At:</span> <span>
                                {{ $employee->created_at->format('d F Y') }}</span></li>
                    </ul>
                </div>
            </div>
            <!--/ Profile Overview -->
        </div>
        <div class="col-xl-8 col-lg-7 col-md-7">
            <!-- Agreements -->
            <div class="card card-action mb-6">
                <div class="card-header align-items-center">
                    <h5 class="card-action-title mb-0"><i
                            class="icon-base bx bx-bar-chart-alt-2 icon-lg text-body me-4"></i>Agreements</h5>
                </div>
                <div class="card-body pt-3">
                    <div class="card-datatable table-responsive text-nowrap">
                        <table class="datatables-employees table table-hover border-top">
                            <thead>
                                <tr>
                                    <th width="1px">#</th>
                                    <th class="text-start">Type</th>
                                    <th class="text-start">Effective Date</th>
                                    <th class="text-start">Period</th>
                                    <th class="text-start">Created By</th>
                                    <th width="1px">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!--/ Agreements -->
        </div>
    </div>
    <!--/ User Profile Content -->
@endsection
