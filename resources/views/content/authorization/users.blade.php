@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Users')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/notyf/notyf.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/notyf/notyf.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/notiflix/notiflix.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/authorization/users.js'])
@endsection

@section('content')
    <h4 class="pt-3">User List</h4>

    <div class="row g-4 mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-datatable table-responsive text-nowrap">
                    <table class="datatables-users table table-hover border-top">
                        <thead>
                            <tr>
                                <th width="1px">#</th>
                                <th class="text-start" width="10%">Username</th>
                                <th class="text-start" width="30%">Full Name</th>
                                <th class="text-start" width="14%">Role</th>
                                <th class="text-start" width="8%">Status</th>
                                <th class="text-start" width="16%">Created Date</th>
                                <th class="text-start" width="16%">Updated Date</th>
                                <th width="1px">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalUser" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formUser" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="johndoe"
                            autofocus />
                    </div>
                    <div class="col-12 mb-3 form-password-toggle">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group input-group-merge">
                            <input type="password" id="password" class="form-control" name="password"
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                aria-describedby="password" />
                            <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
                        </div>
                    </div>
                    <div class="col-12 mb-3 form-password-toggle">
                        <label class="form-label" for="password_confirmation">Password Confirmation</label>
                        <div class="input-group input-group-merge">
                            <input type="password" id="password_confirmation" class="form-control"
                                name="password_confirmation"
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                aria-describedby="password_confirmation" />
                            <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="role">Role</label>
                        <select id="role" name="role" class="select2 form-select" data-allow-clear="true"></select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="status">Status Account</label>
                        <select id="status" name="status" class="select2 form-select" data-allow-clear="true"></select>
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
