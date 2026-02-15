@extends('layouts/layoutMaster')

@section('title', 'Employee Detail')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/notyf/notyf.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss'])
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-profile.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/notyf/notyf.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/notiflix/notiflix.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/pages/profile.js'])
@endsection

@section('content')
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

    <div class="row">
        <div class="col-xl-4 col-lg-5 col-md-5">
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
            <div class="card mb-6">
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
        </div>
        <div class="col-xl-8 col-lg-7 col-md-7">
            <div class="card card-action mb-6">
                <div class="card-header align-items-center">
                    <h5 class="card-action-title mb-0">Update Account</h5>
                </div>
                <div class="card-body">
                    <form id="formProfile" class="modal-content">
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="username">Username</label>
                                <input type="text" id="username" name="username" class="form-control"
                                    placeholder="johndoe" value="{{ old('username', $user->username) }}" autofocus />
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="personal_email">Personal Email</label>
                                <input type="text" id="personal_email" name="personal_email" class="form-control"
                                    placeholder="johndoe@example.com"
                                    value="{{ old('personal_email', optional($employee)->personal_email) }}" />
                            </div>
                            <div class="col-12 col-md-6 mb-3 form-password-toggle">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i
                                            class="icon-base bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3 form-password-toggle">
                                <label class="form-label" for="password_confirmation">Password Confirmation</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password_confirmation" class="form-control"
                                        name="password_confirmation"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password_confirmation" />
                                    <span class="input-group-text cursor-pointer"><i
                                            class="icon-base bx bx-hide"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary me-4">Submit</button>
                            <button type="reset" class="btn btn-label-danger" data-bs-dismiss="modal"
                                aria-label="Close">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
