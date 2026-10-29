@extends('layout.mobile-master')

@section('content')
    <div class="pt-3 pb-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="fs-18 fw-bold text-dark m-0 d-flex align-items-center">
                <i class="mdi mdi-trash-can text-danger me-2 fs-22"></i>
                <span>Trashed Hujjaj Guidelines</span>
            </h4>
            <p class="text-muted fs-13 mb-0">View all soft-deleted guidelines and notices. Click restore to recover any guideline back to active status.</p>
        </div>
        <div>
            <a href="{{ route('mobile-ops.guidelines.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm rounded-pill">
                <i class="mdi mdi-arrow-left me-1"></i> Back to Active
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
            <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Trash Table Card -->
    <div class="card border-0 shadow-sm mt-3" style="border-radius: 12px;">
        <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" action="{{ route('mobile-ops.guidelines.trash') }}" class="d-flex gap-2" style="max-width: 380px; width: 100%;">
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm"
                    placeholder="Search by title, sub-heading, content...">
                <button type="submit" class="btn btn-sm btn-dark px-3"><i class="mdi mdi-magnify"></i></button>
                @if (!empty($search))
                    <a href="{{ route('mobile-ops.guidelines.trash') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                @endif
            </form>
            <span class="badge bg-danger-subtle text-danger border px-3 py-2 fs-12 rounded-pill">
                Total in Trash: {{ $trashCount }}
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 50px;">#</th>
                            <th>Title</th>
                            <th>Sub Heading</th>
                            <th>Original Status</th>
                            <th>Deleted Date</th>
                            <th class="text-end pe-3" style="width: 140px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guidelines as $guideline)
                            <tr>
                                <td class="ps-3 text-muted fs-13">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-bold text-dark text-decoration-line-through fs-14">{{ $guideline->title }}</div>
                                    <div class="text-muted fs-12 text-truncate" style="max-width: 380px;">
                                        {{ Str::limit(strip_tags($guideline->content), 80) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted fs-13">{{ $guideline->sub_heading ?? '—' }}</span>
                                </td>
                                <td>
                                    @if ($guideline->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                            Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-pill">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted fs-13">
                                        <i class="mdi mdi-clock-outline me-1"></i>
                                        {{ $guideline->deleted_at ? $guideline->deleted_at->format('d M Y, h:i A') : 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <form action="{{ route('mobile-ops.guidelines.restore', $guideline->id) }}" method="POST"
                                        onsubmit="return confirm('Restore this guideline back to active listings?');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-sm px-3 shadow-sm rounded-pill">
                                            <i class="mdi mdi-restore me-1"></i> Restore
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-delete-empty-outline fs-36 d-block mb-2 text-secondary"></i>
                                    <span class="fw-bold">No trashed guidelines found.</span>
                                    <p class="fs-13 mb-0 text-muted">Items deleted from the active list will appear here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($guidelines->hasPages())
            <div class="card-footer bg-white border-top py-2">
                {{ $guidelines->links() }}
            </div>
        @endif
    </div>
@endsection
