@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Employees Data')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/notyf/notyf.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/notyf/notyf.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/notiflix/notiflix.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/hr/employees.js'])
@endsection

@section('content')
    <h4 class="pt-3">Employee Data</h4>

    <div class="row g-4 mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-datatable table-responsive text-nowrap">
                    <table class="datatables-employees table table-hover border-top">
                        <thead>
                            <tr>
                                <th width="1px">#</th>
                                <th class="text-start">Full Name</th>
                                <th class="text-start" width="10px">PT</th>
                                <th class="text-start">Position</th>
                                <th class="text-start">Join Date</th>
                                <th class="text-start">Status</th>
                                <th class="text-start">HRBP</th>
                                <th width="1px">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEmployee" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <form id="formEmployee" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="employee_code">Employee ID <strong
                                class="text-danger">**</strong></label>
                        <input type="text" id="employee_code" name="employee_code" class="form-control"
                            placeholder="0001" autofocus />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="full_name">Full Name <strong class="text-danger">**</strong></label>
                        <input type="text" id="full_name" name="full_name" class="form-control" placeholder="John Doe" />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="hrbp_id">HRBP</label>
                        <select id="hrbp_id" name="hrbp_id" class="select2 form-select" data-allow-clear="true"></select>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="manager_id">Direct Manager</label>
                        <select id="manager_id" name="manager_id" class="select2 form-select"
                            data-allow-clear="true"></select>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="join_date">Join Date <strong class="text-danger">**</strong></label>
                        <input type="text" id="join_date" name="join_date" class="form-control"
                            placeholder="YYYY-MM-DD" />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="company_id">Company <strong class="text-danger">**</strong></label>
                        <select id="company_id" name="company_id" class="select2 form-select"
                            data-allow-clear="true"></select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="org_unit_id">Organization Unit <strong
                                class="text-danger">**</strong></label>
                        <select id="org_unit_id" name="org_unit_id" class="select2 form-select"
                            data-allow-clear="true"></select>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="job_title_id">Job Title <strong
                                class="text-danger">**</strong></label>
                        <select id="job_title_id" name="job_title_id" class="select2 form-select"
                            data-allow-clear="true"></select>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="employment_status">Status <strong
                                class="text-danger">**</strong></label>
                        <select id="employment_status" name="employment_status" class="select2 form-select"
                            data-allow-clear="true"></select>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="office_email">Office Email</label>
                        <input type="email" class="form-control" id="office_email" name="office_email"
                            placeholder="johndoe@example.com" />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="personal_email">Personal Email <strong
                                class="text-danger">**</strong></label>
                        <input type="email" class="form-control" id="personal_email" name="personal_email"
                            placeholder="johndoe@example.com" />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="phone_number">Phone Number <strong
                                class="text-danger">**</strong></label>
                        <input type="text" id="phone_number" name="phone_number"
                            class="form-control phone-number-mask" placeholder="0813 1234 1234" />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="gender">Gender <strong class="text-danger">**</strong></label>
                        <select id="gender" name="gender" class="select2 form-select"
                            data-allow-clear="true"></select>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="date_of_birth">Date of Birth <strong
                                class="text-danger">**</strong></label>
                        <input type="text" id="date_of_birth" name="date_of_birth" class="form-control"
                            placeholder="YYYY-MM-DD" />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="ktp_number">KTP Number <strong
                                class="text-danger">**</strong></label>
                        <input type="text" id="ktp_number" name="ktp_number" class="form-control phone-number-mask"
                            placeholder="3152 0610 1102 0001" />
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

    <div class="modal fade" id="modalUser" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formUser" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Create User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="johndoe"
                            autofocus />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="role">Role</label>
                        <select id="role" name="role" class="select2 form-select"
                            data-allow-clear="true"></select>
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

    <div class="modal fade" id="modalImport" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formImport" class="modal-content" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Import Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="file">Upload File</label>
                        <input id="file" name="file" class="form-control" type="file"accept=".xls,.xlsx">
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
