@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Manpower Plans')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/notyf/notyf.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/notyf/notyf.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/notiflix/notiflix.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/hr/manpower_plans.js'])
@endsection

@section('content')
    <h4 class="pt-3">Manpower Plannings and Operational Needs</h4>

    <div class="row g-4 mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-datatable table-responsive text-nowrap">
                    <table class="datatables-manpowers table table-hover border-top">
                        <thead>
                            <tr>
                                <th width="1px">#</th>
                                <th class="text-start">Position</th>
                                <th class="text-start">Estimation Join Date</th>
                                <th class="text-start">Total</th>
                                <th class="text-start">Devices</th>
                                <th class="text-start">Status</th>
                                <th class="text-start">Created By</th>
                                <th width="1px">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalManPower" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form id="formManPower" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Manpower Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="org_unit_id">Organization Unit <strong
                                class="text-danger">**</strong></label>
                        <select id="org_unit_id" name="org_unit_id" class="select2 form-select"
                            data-allow-clear="true"></select>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="position_title">Position <strong
                                class="text-danger">**</strong></label>
                        <input type="text" id="position_title" name="position_title" class="form-control"
                            placeholder="KOL Officer" />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="planned_date">Planned Date <strong
                                class="text-danger">**</strong></label>
                        <input type="text" id="planned_date" name="planned_date" class="form-control"
                            placeholder="YYYY-MM-DD" />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="number_positions">Total Headcount</label>
                        <input type="number" id="number_positions" name="number_positions" class="form-control"
                            placeholder="5" min="0" step="1" />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="devices">Devices <strong class="text-danger">**</strong></label>
                        <select id="devices" name="devices[]" class="select2 form-select" multiple></select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="3" class="form-control autosize"></textarea>
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
