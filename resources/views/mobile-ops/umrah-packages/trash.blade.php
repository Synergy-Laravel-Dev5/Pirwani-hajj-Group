@extends('layout.mobile-master')

@section('content')
    <div class="pt-3 pb-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="fs-18 fw-bold text-dark m-0 d-flex align-items-center">
                <i class="mdi mdi-trash-can text-danger me-2 fs-22"></i>
                <span>Trashed Umrah Packages</span>
            </h4>
            <p class="text-muted fs-13 mb-0">View all soft-deleted packages. Click restore to recover any package to active
                listing.</p>
        </div>
        <div>
            <a href="{{ route('mobile-ops.umrah-packages.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                <i class="mdi mdi-arrow-left me-1"></i> Back to Active Packages
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Trash Table Card -->
    <div class="card border-0 shadow-sm mt-3" style="border-radius: 12px;">
        <div
            class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" action="{{ route('mobile-ops.umrah-packages.trash') }}" class="d-flex gap-2"
                style="max-width: 350px; width: 100%;">
                <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm"
                    placeholder="Search deleted packages...">
                <button type="submit" class="btn btn-sm btn-light border"><i class="mdi mdi-magnify"></i></button>
                @if (!empty($search))
                    <a href="{{ route('mobile-ops.umrah-packages.trash') }}"
                        class="btn btn-sm btn-outline-secondary">Clear</a>
                @endif
            </form>
            <span class="badge bg-danger-subtle text-danger border px-3 py-2 fs-12">
                Total in Trash: {{ $trashCount }}
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 70px;">Thumbnail</th>
                            <th>Package Info</th>
                            <th>Hotels Summary</th>
                            <th>Sharing Price</th>
                            <th>Deleted At</th>
                            <th class="text-end pe-3" style="width: 140px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $pkg)
                            @php
                                $makkah = $pkg->accommodations->firstWhere('place', 'makkah');
                                $madinah = $pkg->accommodations->firstWhere('place', 'madinah');
                                $rate = $pkg->price_sharing ?: ($pkg->price_quad ?: 0);
                            @endphp
                            <tr>
                                <td class="ps-3">
                                    @if ($pkg->package_image)
                                        <img src="{{ asset('storage/' . $pkg->package_image) }}" alt=""
                                            class="rounded object-fit-cover opacity-75 border" width="46"
                                            height="46">
                                    @else
                                        <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted border"
                                            style="width: 46px; height: 46px;">
                                            <i class="mdi mdi-image-off-outline fs-20"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark text-decoration-line-through">
                                        {{ $pkg->package_title ?? $pkg->name }}</div>
                                    <div class="fs-12 text-muted">
                                        <span
                                            class="badge bg-light text-dark border">{{ $pkg->package_code ?? 'NO-CODE' }}</span>
                                        @if ($pkg->is_group_package)
                                            <span class="badge bg-primary-subtle text-primary border ms-1">Group</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="fs-12"><strong>Makkah:</strong> {{ $makkah ? $makkah->hotel : 'N/A' }}
                                    </div>
                                    <div class="fs-12 text-muted"><strong>Madinah:</strong>
                                        {{ $madinah ? $madinah->hotel : 'N/A' }}</div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-secondary">PKR {{ number_format($rate, 2) }}</span>
                                </td>
                                <td>
                                    <span class="text-muted fs-13">
                                        <i class="mdi mdi-clock-outline me-1"></i>
                                        {{ $pkg->deleted_at ? $pkg->deleted_at->format('d M Y, h:i A') : 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <form action="{{ route('mobile-ops.umrah-packages.restore', $pkg->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Restore this package back to active packages?');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-sm px-3 shadow-sm">
                                            <i class="mdi mdi-restore me-1"></i> Restore
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-delete-empty-outline fs-36 d-block mb-2 text-secondary"></i>
                                    <span class="fw-bold">No trashed packages found.</span>
                                    <p class="fs-13 mb-0 text-muted">Items deleted from the active list will appear here.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($packages->hasPages())
            <div class="card-footer bg-white border-top py-2">
                {{ $packages->links() }}
            </div>
        @endif
    </div>
@endsection
