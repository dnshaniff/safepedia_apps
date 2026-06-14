@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Detail Construction')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/notyf/notyf.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss', 'resources/assets/vendor/libs/leaflet/leaflet.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/notyf/notyf.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/notiflix/notiflix.js', 'resources/assets/vendor/libs/leaflet/leaflet.js', 'resources/assets/vendor/libs/fancybox/fancybox.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/warehouse_constructions/show.js'])
@endsection

@section('content')
    <h4 class="py-3 mb-2">
        Warehouse Construction: {{ $warehouseConstruction->construction_number }}
    </h4>

    <div class="card p-sm-10">
        <div class="card-body px-0">
            <div class="row">
                <div class="col-12 mb-6">
                    <table>
                        <tbody>
                            <tr>
                                <td class="pe-4">Number:</td>
                                <td class="fw-medium">{{ $warehouseConstruction->construction_number }}</td>
                            </tr>
                            <tr>
                                <td class="pe-4">Name:</td>
                                <td class="fw-medium">{{ $warehouseConstruction->warehouse_name }}</td>
                            </tr>
                            <tr>
                                <td class="pe-4">Latitude:</td>
                                <td class="fw-medium">{{ $warehouseConstruction->latitude }}</td>
                            </tr>
                            <tr>
                                <td class="pe-4">Longitude:</td>
                                <td class="fw-medium">{{ $warehouseConstruction->longitude }}</td>
                            </tr>
                            <tr>
                                <td class="pe-4">Requestor:</td>
                                <td class="fw-medium">{{ $warehouseConstruction->creator?->name }}</td>
                            </tr>
                            <tr>
                                <td class="pe-4">Created At:</td>
                                <td class="fw-medium">
                                    {{ $warehouseConstruction->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-12 mb-6">
                    <h6 class="fw-normal">Location:</h6>

                    <div id="constructionMap" class="leaflet-map rounded border" style="height: 320px; width: 100%;"
                        data-latitude="{{ $warehouseConstruction->latitude }}"
                        data-longitude="{{ $warehouseConstruction->longitude }}"
                        data-name="{{ $warehouseConstruction->warehouse_name }}"></div>
                </div>

                <div class="col-12 mb-6">
                    <h6 class="fw-normal">Documents:</h6>

                    <div class="row g-3">
                        @forelse ($warehouseConstruction->documents as $document)
                            <div class="col-12 col-md-6 col-lg-4">
                                <a href="{{ asset('storage/' . $document->file_path) }}"
                                    class="d-flex align-items-center border rounded p-3 text-body"
                                    data-fancybox="construction-documents" data-type="iframe"
                                    data-caption="{{ $document->original_name }}">
                                    <i class="bx bxs-file-pdf fs-2 text-danger me-3"></i>

                                    <div class="overflow-hidden">
                                        <div class="fw-medium text-truncate">
                                            {{ $document->original_name }}
                                        </div>
                                        <small class="text-muted">
                                            {{ number_format($document->file_size / 1024, 0, ',', '.') }} KB
                                        </small>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="col-12">
                                <span class="text-muted">No documents available</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <h6 class="fw-normal">Items:</h6>
        <div class="table-responsive border border-bottom-0 border-top-0 rounded">
            <table class="table m-0">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($warehouseConstruction->items as $item)
                        <tr>
                            <td class="text-heading">
                                {{ $item->item_name }}
                            </td>
                            <td class="text-center">
                                {{ rtrim(rtrim(number_format($item->quantity, 2, '.', ''), '0'), '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->line_total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="table-responsive">
            <table class="table m-0 table-borderless">
                <tbody>
                    <tr>
                        <td class="align-top pe-6 ps-0 py-6 text-body"></td>
                        <td class="px-0 py-6 w-px-100">
                            <p class="mb-0">Total:</p>
                        </td>
                        <td class="text-end px-0 py-6 w-px-150 fw-medium text-heading">
                            <p class="mb-0">Rp
                                {{ number_format($warehouseConstruction->grand_total_budget, 0, ',', '.') }}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-6">
        <div class="card-header">
            <h5 class="mb-0">Approval Timeline</h5>
        </div>

        <div class="card-body">
            <ul class="timeline mb-0">
                @forelse ($warehouseConstruction->approvals->sortBy('created_at') as $approvalHistory)
                    @php
                        $pointClass = match ($approvalHistory->action) {
                            'submitted', 'resubmitted' => 'timeline-point-primary',
                            'approved' => 'timeline-point-success',
                            'returned' => 'timeline-point-warning',
                            'rejected', 'canceled' => 'timeline-point-danger',
                            'pending' => 'timeline-point-info',
                            default => 'timeline-point-secondary',
                        };

                        $employeeName = $approvalHistory->employee?->full_name ?? '-';
                        $roleName = $approvalHistory->approval?->approval_role;

                        $title = match ($approvalHistory->action) {
                            'submitted' => "{$employeeName} submitted request",
                            'resubmitted' => "{$employeeName} resubmitted request",
                            'approved' => "{$employeeName} approved request",
                            'returned' => "{$employeeName} returned request",
                            'rejected' => "{$employeeName} rejected request",
                            'canceled' => "{$employeeName} canceled request",
                            'pending' => "{$employeeName} waiting for approval",
                            default => "{$employeeName} {$approvalHistory->action}",
                        };
                    @endphp

                    <li class="timeline-item timeline-item-transparent">
                        <span class="timeline-point {{ $pointClass }}"></span>

                        <div class="timeline-event">
                            <div
                                class="timeline-header mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <h6 class="mb-0">{{ $title }}</h6>

                                <div class="d-flex flex-wrap align-items-center justify-content-end gap-3">
                                    @if (
                                        $approvalHistory->action === 'pending' &&
                                            $warehouseConstruction->status === 'pending' &&
                                            $approvalHistory->employee_id === auth()->user()->employee?->id)
                                        <button type="button" class="btn btn-sm btn-label-danger return-construction"
                                            data-id="{{ $warehouseConstruction->id }}"
                                            data-approval-id="{{ $approvalHistory->id }}">
                                            <i class="bx bx-undo me-1"></i>
                                            Return
                                        </button>

                                        <button type="button" class="btn btn-sm btn-primary approve-construction"
                                            data-id="{{ $warehouseConstruction->id }}"
                                            data-approval-id="{{ $approvalHistory->id }}">
                                            <i class="bx bx-check me-1"></i>
                                            Approve
                                        </button>
                                    @endif

                                    <small class="text-body-secondary text-nowrap">
                                        {{ $approvalHistory->created_at->format('d M Y, H:i') }}
                                    </small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                                <div class="d-flex flex-wrap align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <img src="{{ asset('assets/img/avatars/avatar.png') }}" alt="Avatar"
                                            class="rounded-circle" />
                                    </div>

                                    <div>
                                        <p class="mb-0 small fw-medium">{{ $employeeName }}</p>
                                        <small>{{ $roleName ?? 'Requestor' }}</small>
                                    </div>
                                </div>
                            </div>

                            @if ($approvalHistory->notes)
                                <p class="mb-0">{{ $approvalHistory->notes }}</p>
                            @endif
                        </div>
                    </li>

                @empty
                    <li class="timeline-item timeline-item-transparent">
                        <div class="timeline-event">
                            <p class="mb-0 text-body-secondary">No approval history yet.</p>
                        </div>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="modal fade" id="modalApproval" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formApproval" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Construction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="notes">Notes</label>
                        <textarea id="notes" name="notes" class="form-control autosize" rows="4"
                            placeholder="Write notes here..."></textarea>
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
