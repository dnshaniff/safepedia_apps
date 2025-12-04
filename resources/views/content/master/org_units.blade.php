@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Organization Units')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/notyf/notyf.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/sortablejs/sortable.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/notyf/notyf.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/notiflix/notiflix.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/master/org_units.js'])
@endsection

@section('content')
    <h4 class="pt-3">Organization Units</h4>

    <button class="btn btn-primary add-new" data-bs-toggle="modal" data-bs-target="#modalOrgUnit">Create Unit</button>

    <div class="pt-4" id="org-breadcrumbs"></div>

    <div class="row">
        <div class="col-12 mb-md-0 mb-6">
            <ul id="org-unit" class="list-unstyled mb-0"></ul>
        </div>
    </div>

    <div class="modal fade" id="modalOrgUnit" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formOrgUnit" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Organization Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="unit_name">Unit Name</label>
                        <input type="text" id="unit_name" name="unit_name" class="form-control"
                            placeholder="General Affairs" autofocus />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="unit_code">Unit Code</label>
                        <input type="text" id="unit_code" name="unit_code" class="form-control" placeholder="GA" />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="unit_type">Type</label>
                        <select id="unit_type" name="unit_type" class="select2 form-select"
                            data-allow-clear="true"></select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="parent_id">Parent</label>
                        <select id="parent_id" name="parent_id" class="select2 form-select"
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
