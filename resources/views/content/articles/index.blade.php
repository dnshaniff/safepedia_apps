@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Articles')

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.scss',
        'resources/assets/vendor/libs/select2/select2.scss',
        'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
        'resources/assets/vendor/libs/@form-validation/form-validation.scss',
        'resources/assets/vendor/libs/notyf/notyf.scss',
        'resources/assets/vendor/libs/animate-css/animate.scss',
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
        'resources/assets/vendor/libs/spinkit/spinkit.scss',
        'resources/assets/vendor/libs/dropzone/dropzone.scss',
        'resources/assets/vendor/libs/quill/typography.scss',
        'resources/assets/vendor/libs/highlight/highlight.scss',
        'resources/assets/vendor/libs/quill/katex.scss',
        'resources/assets/vendor/libs/quill/editor.scss',
    ])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/notyf/notyf.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/notiflix/notiflix.js', 'resources/assets/vendor/libs/fancybox/fancybox.js', 'resources/assets/vendor/libs/dropzone/dropzone.js', 'resources/assets/vendor/libs/quill/katex.js', 'resources/assets/vendor/libs/highlight/highlight.js', 'resources/assets/vendor/libs/quill/quill.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/articles/index.js'])
@endsection

@section('content')
    <h4 class="pt-3">Article List</h4>

    <div class="row g-4 mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-datatable table-responsive text-nowrap">
                    <table class="datatables-articles table table-hover border-top">
                        <thead>
                            <tr>
                                <th width="1px">#</th>
                                <th class="text-start" width="30%">Title</th>
                                <th class="text-start" width="20%">Project At</th>
                                <th class="text-start" width="10%">Status</th>
                                <th class="text-start">Created Date</th>
                                <th class="text-start">Updated Date</th>
                                <th width="1px">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalArticle" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <form id="formArticle" class="modal-content" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Article</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="title">Title <strong class="text-danger">**</strong></label>
                        <input type="text" id="title" name="title" class="form-control"
                            placeholder="Instalasi APIL" autofocus />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="content">Content <strong class="text-danger">**</strong></label>
                        <div id="content-editor"></div>
                        <input type="hidden" name="content" id="content" />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="location">Location <strong class="text-danger">**</strong></label>
                        <input type="text" id="location" name="location" class="form-control" placeholder="Surabaya" />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="project_at">Date</label>
                        <input type="text" id="project_at" name="project_at" class="form-control"
                            placeholder="YYYY-MM-DD" />
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="status">Status <strong class="text-danger">**</strong></label>
                        <select id="status" name="status" class="select2 form-select" data-allow-clear="true"></select>
                    </div>
                    <div class="col-12">
                        <label class="form-label d-block mb-2">Article Images <strong
                                class="text-danger">**</strong></label>
                        <div id="articleDropzone" class="dropzone border rounded p-4 text-center">
                            <div class="dz-message">
                                <div class="mt-2 fw-medium">Drop article images here</div>
                                <small class="text-muted">PNG, JPG, JPEG, WEBP up to 4MB</small>
                            </div>
                        </div>
                        <input type="hidden" name="article_images" id="article-images" />
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

    <div class="modal fade" id="modalShow" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Detail Article</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-danger" data-bs-dismiss="modal"
                        aria-label="Close">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
