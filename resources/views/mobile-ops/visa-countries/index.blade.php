@extends('layout.mobile-master')

@section('content')
    <div class="pt-3 pb-2 d-flex align-items-sm-center justify-content-between flex-column flex-sm-row">
        <div>
            <h4 class="fs-18 fw-bold text-dark m-0">Visa Countries Management</h4>
            <p class="text-muted fs-13 mb-0">Manage destination countries, visa categories, B2B prices, and requirements.</p>
        </div>
        <div class="mt-2 mt-sm-0 d-flex gap-2">
            @php
                $trashTotal = \App\Models\VisaCountry::onlyTrashed()->count();
            @endphp
            <a href="{{ route('mobile-ops.visa-countries.trash') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1">
                <i class="mdi mdi-trash-can-outline"></i> View Trash
                @if ($trashTotal > 0)
                    <span class="badge bg-danger text-white rounded-pill ms-1">{{ $trashTotal }}</span>
                @endif
            </a>
            <a href="{{ route('mobile-ops.visa-countries.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                <i class="mdi mdi-plus me-1"></i> Add Visa Country
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mt-3" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table  align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 70px;">Flag</th>
                            <th>Country Name</th>
                            <th>Code</th>
                            <th>Visa Types</th>
                            <th>Monument</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($countries as $c)
                            <tr>
                                <td class="ps-3">
                                    @if ($c->country_flag_url)
                                        <img src="{{ $c->country_flag_url }}" class="rounded shadow-sm"
                                            style="width: 38px; height: 26px; object-fit: cover;">
                                    @else
                                        <span class="badge bg-light text-muted border">No Flag</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-dark">{{ $c->country_name }}</td>
                                <td><span
                                        class="badge bg-secondary-subtle text-secondary px-2 py-1">{{ $c->country_code }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                        {{ $c->visaTypes->count() }} Types
                                    </span>
                                </td>
                                <td>
                                    @if ($c->monument_picture_url)
                                        <a href="{{ $c->monument_picture_url }}" target="_blank">
                                            <img src="{{ $c->monument_picture_url }}" class="rounded shadow-sm"
                                                style="width: 45px; height: 35px; object-fit: cover;">
                                        </a>
                                    @else
                                        <span class="text-muted fs-12">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($c->is_active)
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                                    @else
                                        <span
                                            class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('mobile-ops.visa-countries.edit', $c->id) }}"
                                            class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <form action="{{ route('mobile-ops.visa-countries.destroy', $c->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Delete this country and all its visa types?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No visa countries added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($countries->hasPages())
                <div class="p-3 border-top">
                    {{ $countries->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
