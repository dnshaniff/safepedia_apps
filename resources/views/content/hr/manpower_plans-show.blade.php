@extends('layouts/layoutMaster')

@section('title', 'MPP Detail')

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
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/hr/manpower_plans-show.js'])
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-6">
                <div class="d-flex flex-column flex-lg-row text-sm-start text-center mb-8">
                    <div class="flex-grow-1 mt-3 mt-lg-5">
                        <div
                            class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
                            <div class="user-profile-info">
                                <h4 class="mb-2">{{ $manpowerPlan->position_title }}</h4>
                                <ul
                                    class="list-inline mb-3 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4">
                                    <li class="list-inline-item"><i class="icon-base bx bx-sitemap me-2 align-top"></i><span
                                            class="fw-medium">{{ $manpowerPlan->orgUnit->unit_name }}</span></li>
                                    <li class="list-inline-item"><i class="icon-base bx bx-user me-2 align-top"></i><span
                                            class="fw-medium">Total Headcount: {{ $filled }} /
                                            {{ $manpowerPlan->number_positions }}</span>
                                    </li>
                                    <li class="list-inline-item"><i
                                            class="icon-base bx bx-calendar me-2 align-top"></i><span
                                            class="fw-medium">{{ $manpowerPlan->planned_date->format('d F Y') }}</span></li>
                                </ul>
                                <ul
                                    class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4">
                                    <li class="list-inline-item">Devices:
                                        {{ $manpowerPlan->devices->pluck('type_name')->join(', ') }}</li>
                                </ul>
                                <ul
                                    class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4">
                                    <li class="list-inline-item">Notes:
                                        {{ $manpowerPlan->notes }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card card-action mb-6">
                <div class="card-header align-items-center">
                    <h5 class="card-action-title mb-0"><i class="icon-base bx bx-user icon-lg text-body me-4"></i>Candidates
                    </h5>
                </div>
                <div class="card-body pt-3">
                    <div class="card-datatable table-responsive text-nowrap">
                        <table class="datatables-candidates table table-hover border-top">
                            <thead>
                                <tr>
                                    <th width="1px">#</th>
                                    <th class="text-start">Name</th>
                                    <th class="text-start">Contact</th>
                                    <th class="text-start">Status</th>
                                    <th class="text-start">Expected Date</th>
                                    <th class="text-start">Notes</th>
                                    <th class="text-start">Created By</th>
                                    <th width="1px">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCandidate" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form id="formCandidate" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Candidate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="agreement_type">Agreement Type <strong
                                class="text-danger">**</strong></label>
                        <select id="agreement_type" name="agreement_type" class="select2 form-select"
                            data-allow-clear="true"></select>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="start_date">Start Date</label>
                        <input type="text" id="start_date" name="start_date" class="form-control"
                            placeholder="YYYY-MM-DD" />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="end_date">End Date</label>
                        <input type="text" id="end_date" name="end_date" class="form-control"
                            placeholder="YYYY-MM-DD" />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="effective_date">Effective Date</label>
                        <input type="text" id="effective_date" name="effective_date" class="form-control"
                            placeholder="YYYY-MM-DD" />
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
