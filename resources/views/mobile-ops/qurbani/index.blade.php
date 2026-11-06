@extends('layout.mobile-master')

@section('content')
    <div class="py-3 d-flex align-items-sm-center justify-content-between flex-column flex-sm-row gap-2">
        <div>
            <h4 class="fs-18 fw-semibold m-0 text-dark">
                <i class="mdi mdi-bullhorn text-warning me-1"></i> Qurbani Batches & Schedule
            </h4>
            <p class="text-muted fs-13 mb-0">
                Manage Qurbani date & time slots for Hajjis.
            </p>
        </div>

        <div>
            <a href="{{ route('mobile-ops.qurbani.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                <i class="mdi mdi-plus fs-16"></i> Create Qurbani Batch
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- DataTables Table -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body">
            <div class="table-responsive">
                <table id="qurbani-batches-datatable" class="table table-bordered dt-responsive nowrap align-middle mb-0 w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Batch Name</th>
                            <th>Qurbani Date</th>
                            <th>Time Window (Same Day)</th>
                            <th>Assigned Hajjis</th>
                            <th>Completed by Hajjis</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="fs-13">
                        @foreach ($batches as $batch)
                            <tr>
                                <td class="ps-3">
                                    <a href="{{ route('mobile-ops.qurbani.show', $batch->id) }}" class="fw-bold text-primary text-decoration-none">
                                        {{ $batch->batch_name }}
                                    </a>
                                    @if($batch->title)
                                        <div class="text-muted fs-11 text-truncate" style="max-width: 250px;">{{ $batch->title }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="mdi mdi-calendar me-1 text-primary"></i>
                                        {{ \Carbon\Carbon::parse($batch->qurbani_date)->format('d M, Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="mdi mdi-clock-outline me-1 text-info"></i>
                                        {{ substr((string)$batch->start_time, 0, 5) }} - {{ substr((string)$batch->end_time, 0, 5) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold px-2 py-1">
                                        <i class="mdi mdi-account-group me-1"></i>
                                        {{ $batch->hajjis_count }} Hajji{{ $batch->hajjis_count === 1 ? '' : 's' }}
                                    </span>
                                </td>
                                <td>
                                    @if($batch->completed_hajjis_count > 0)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold px-2 py-1">
                                            <i class="mdi mdi-check-circle me-1"></i>
                                            {{ $batch->completed_hajjis_count }} of {{ $batch->hajjis_count }} Done
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border px-2 py-1">
                                            0 Done
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('mobile-ops.qurbani.show', $batch->id) }}" class="btn btn-sm btn-outline-info" title="View & Manage Hajjis">
                                            <i class="mdi mdi-eye-outline me-1"></i> View
                                        </a>
                                        <a href="{{ route('mobile-ops.qurbani.edit', $batch->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Batch">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <form action="{{ route('mobile-ops.qurbani.destroy', $batch->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Qurbani batch?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .bg-primary-subtle {
            background-color: rgba(13, 110, 253, 0.1) !important;
        }
        .bg-success-subtle {
            background-color: rgba(25, 135, 84, 0.1) !important;
        }
    </style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#qurbani-batches-datatable').DataTable({
            paging: true,
            info: true,
            searching: true,
            lengthChange: true,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            pageLength: 10,
            responsive: true,
            order: [],
            columnDefs: [
                { orderable: false, targets: [5] }
            ],
            language: {
                lengthMenu: "Show _MENU_ entries",
                search: "Search batches:",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next: "<i class='mdi mdi-chevron-right'>"
                }
            }
        });
    });
</script>
@endpush
