@extends('layout.mobile-master')

@section('content')
    <div class="pt-3 pb-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="fs-18 fw-bold text-dark m-0 d-flex align-items-center">
                <i class="mdi mdi-trash-can text-danger me-2 fs-22"></i>
                <span>Trashed Visa Countries</span>
            </h4>
            <p class="text-muted fs-13 mb-0">View all soft-deleted destination countries. Click restore to recover any country and its visa categories back to the active listings.</p>
        </div>
        <div>
            <a href="{{ route('mobile-ops.visa-countries.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm rounded-pill">
                <i class="mdi mdi-arrow-left me-1"></i> Back to Active Countries
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
            <form method="GET" action="{{ route('mobile-ops.visa-countries.trash') }}" class="d-flex gap-2" style="max-width: 380px; width: 100%;">
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm"
                    placeholder="Search country name or code...">
                <button type="submit" class="btn btn-sm btn-dark px-3"><i class="mdi mdi-magnify"></i></button>
                @if (!empty($search))
                    <a href="{{ route('mobile-ops.visa-countries.trash') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
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
                            <th class="ps-3" style="width: 70px;">Flag</th>
                            <th style="width: 80px;">Monument</th>
                            <th>Country Name</th>
                            <th>Country Code</th>
                            <th>Total Visa Types</th>
                            <th>Deleted Date</th>
                            <th class="text-end pe-3" style="width: 140px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($countries as $c)
                            <tr>
                                <td class="ps-3">
                                    @php
                                        $flagSrc = $c->country_flag_url;
                                        $isoCode = strtolower(trim($c->country_code ?? ''));
                                        $fallbackFlag = (strlen($isoCode) === 2 && ctype_alpha($isoCode)) ? "https://flagcdn.com/w160/{$isoCode}.png" : null;
                                    @endphp
                                    @if ($flagSrc || $fallbackFlag)
                                        <img src="{{ $flagSrc ?: $fallbackFlag }}" alt="{{ $c->country_name }}"
                                            class="rounded object-fit-cover opacity-75 border" style="width: 38px; height: 26px;"
                                            @if($fallbackFlag) onerror="if(this.src!=='{{ $fallbackFlag }}'){ this.src='{{ $fallbackFlag }}'; } else { this.onerror=null; this.parentElement.innerHTML='<span class=\'badge bg-light text-muted border\'>No Flag</span>'; }" @endif>
                                    @else
                                        <span class="badge bg-light text-muted border">No Flag</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($c->monument_picture_url)
                                        <img src="{{ $c->monument_picture_url }}" alt="{{ $c->country_name }}"
                                            class="rounded object-fit-cover opacity-75 border" style="width: 45px; height: 35px;"
                                            onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-muted fs-12\'>—</span>';">
                                    @else
                                        <span class="text-muted fs-12">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark text-decoration-line-through fs-14">{{ $c->country_name }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">
                                        {{ $c->country_code }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded-pill fs-12">
                                        <i class="mdi mdi-passport me-1"></i>
                                        {{ $c->visa_types_count ?? 0 }} Types
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted fs-13">
                                        <i class="mdi mdi-clock-outline me-1"></i>
                                        {{ $c->deleted_at ? $c->deleted_at->format('d M Y, h:i A') : 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <form action="{{ route('mobile-ops.visa-countries.restore', $c->id) }}" method="POST"
                                        onsubmit="return confirm('Restore this visa country and all its categories?');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-sm px-3 shadow-sm rounded-pill">
                                            <i class="mdi mdi-restore me-1"></i> Restore
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-delete-empty-outline fs-36 d-block mb-2 text-secondary"></i>
                                    <span class="fw-bold">No trashed visa countries found.</span>
                                    <p class="fs-13 mb-0 text-muted">Countries deleted from the active list will appear here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($countries->hasPages())
            <div class="card-footer bg-white border-top py-2">
                {{ $countries->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
