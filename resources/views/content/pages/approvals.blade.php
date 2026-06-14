@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Master Approval')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/notyf/notyf.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/notyf/notyf.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/notiflix/notiflix.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/pages/approvals.js'])
@endsection

@section('content')
    <h4 class="py-3 mb-2">Approvals List</h4>

    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="datatables-approvals table table-hover border-top text-start">
                <thead>
                    <tr>
                        <th width="1px">#</th>
                        <th class="text-start">Role</th>
                        <th class="text-start">Sequence</th>
                        <th class="text-start">Employee</th>
                        <th class="text-start" width="16%">Created Date</th>
                        <th class="text-start" width="16%">Updated Date</th>
                        <th width="1px">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalApproval" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formApproval" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Approval</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="approval_role">Role</label>
                        <input type="text" id="approval_role" name="approval_role" class="form-control"
                            placeholder="SPV Gudang" />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="sequence">Sequence</label>
                        <input type="number" min="1" id="sequence" name="sequence" class="form-control"
                            placeholder="3" />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="employee_id">Employee</label>
                        <select id="employee_id" name="employee_id" class="select2 form-select"
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
@endsection
