@extends('layout.mobile-master')

@section('content')
    <style>
        .pkg-card {
            border-radius: 12px;
            border: 1px solid #e7eaec;
        }
        .btn-emerald {
            background-color: #059669 !important;
            border-color: #059669 !important;
            color: #ffffff !important;
        }
    </style>

    <div class="pt-3 pb-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="fs-18 fw-bold text-dark m-0 d-flex align-items-center">
                <i class="mdi mdi-trash-can text-danger me-2 fs-22"></i>
                <span>Trashed Domestic Packages</span>
            </h4>
            <p class="text-muted fs-13 mb-0">Soft-deleted items are safely preserved. Restore any package with a single click.</p>
        </div>
        <div>
            <a href="{{ route('mobile-ops.domestic-packages.index') }}" class="btn btn-outline-secondary btn-sm px-3 rounded-pill">
                <i class="mdi mdi-arrow-left me-1"></i> Back to Active Packages
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-2 shadow-sm" role="alert">
            <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-2 shadow-sm" role="alert">
            <i class="mdi mdi-alert-circle-outline me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mt-3 pkg-card">
        <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" action="{{ route('mobile-ops.domestic-packages.trash') }}" class="d-flex gap-2" style="max-width: 380px; width: 100%;">
                <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm rounded-pill"
                    placeholder="Search trashed packages...">
                <button type="submit" class="btn btn-sm btn-light border rounded-pill px-3"><i class="mdi mdi-magnify"></i></button>
                @if (!empty($search))
                    <a href="{{ route('mobile-ops.domestic-packages.trash') }}" class="btn btn-sm btn-outline-secondary rounded-pill">Clear</a>
                @endif
            </form>
            <span class="badge bg-danger-subtle text-danger border px-3 py-2 fs-12 rounded-pill">
                Total in Trash: {{ $trashCount }}
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 50px;">#</th>
                            <th>Package</th>
                            <th>Route</th>
                            <th>Deleted At</th>
                            <th class="text-end pe-3" style="width: 140px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $pkg)
                            <tr>
                                <td class="ps-3 text-muted fs-12">{{ $packages->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $pkg->package_title }}</div>
                                    <span class="badge bg-light text-secondary border fs-11">{{ $pkg->package_code ?? 'DOM-' . $pkg->id }}</span>
                                </td>
                                <td>
                                    <span class="text-dark fw-semibold">{{ $pkg->departure_city ?? 'Any' }}</span>
                                    <i class="mdi mdi-arrow-right text-muted mx-1"></i>
                                    <span class="text-success fw-semibold">{{ $pkg->destination_city ?? 'Pakistan' }}</span>
                                </td>
                                <td class="text-muted fs-13">
                                    {{ $pkg->deleted_at ? $pkg->deleted_at->format('M d, Y - h:i A') : 'N/A' }}
                                </td>
                                <td class="text-end pe-3">
                                    <form action="{{ route('mobile-ops.domestic-packages.restore', $pkg->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">
                                            <i class="mdi mdi-restore me-1"></i> Restore
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-delete-empty-outline fs-40 d-block mb-2 text-muted"></i>
                                    Trash is empty. No soft-deleted domestic packages found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($packages->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $packages->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
