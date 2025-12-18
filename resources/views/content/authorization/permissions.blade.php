@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Permissions')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/notyf/notyf.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/notyf/notyf.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/notiflix/notiflix.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/authorization/permissions.js'])
@endsection

@section('content')
    <h4 class="py-3 mb-2">Permissions List</h4>

    <p class="mb-4">Permissions allow you to specify what actions or features different roles can access. <br> By defining
        permissions, you can control and customize the level of access for each role, ensuring that users have appropriate
        access to the features they need.</p>

    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="datatables-permissions table table-hover border-top text-start">
                <thead>
                    <tr>
                        <th width="1px">#</th>
                        <th class="text-start">Name</th>
                        <th class="text-start">Route</th>
                        <th class="text-start">Group</th>
                        <th class="text-start" width="16%">Created Date</th>
                        <th class="text-start" width="16%">Updated Date</th>
                        <th width="1px">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalPermission" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formPermission" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <p>Permissions you may use and assign to your users.</p>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="display_name">Permission Name</label>
                        <input type="text" id="display_name" name="display_name" class="form-control"
                            placeholder="Permission Name" autofocus />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="name">Route Name</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Route Name" />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="group_name">Group Name</label>
                        <input type="text" id="group_name" name="group_name" class="form-control"
                            placeholder="Group Name" />
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
