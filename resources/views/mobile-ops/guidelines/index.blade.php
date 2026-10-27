@extends('layout.mobile-master')

@section('content')
    <div class="pt-3 pb-2 d-flex align-items-sm-center justify-content-between flex-column flex-sm-row">
        <div>
            <h4 class="fs-18 fw-bold text-dark m-0">Hujjaj Information & Guidelines</h4>
            <p class="text-muted fs-13 mb-0">Publish operational notices and advisory guidelines for
                pilgrims.</p>
        </div>
        <div class="mt-2 mt-sm-0 d-flex gap-2">
            @php
                $trashTotal = \App\Models\HujjajGuideline::onlyTrashed()->count();
            @endphp
            <a href="{{ route('mobile-ops.guidelines.trash') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1">
                <i class="mdi mdi-trash-can-outline"></i> View Trash
                @if ($trashTotal > 0)
                    <span class="badge bg-danger text-white rounded-pill ms-1">{{ $trashTotal }}</span>
                @endif
            </a>
            <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal"
                data-bs-target="#createGuidelineModal">
                <i class="mdi mdi-plus me-1"></i> Add New Guideline
            </button>
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

    <!-- Search bar -->
    <div class="card mb-3 border-0 shadow-sm" style="border-radius: 10px;">
        <div class="card-body p-2">
            <form method="GET" action="{{ route('mobile-ops.guidelines.index') }}" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Search by title or subheading..." value="{{ $search ?? '' }}">
                <button type="submit" class="btn btn-sm btn-dark px-3">Search</button>
                @if (!empty($search))
                    <a href="{{ route('mobile-ops.guidelines.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table  align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 25%;">Title</th>
                            <th style="width: 20%;">Sub Heading</th>
                            <th>Content Preview</th>
                            <th style="width: 90px;">Status</th>
                            <th style="width: 140px;">Published Date</th>
                            <th class="text-end pe-3" style="width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guidelines as $item)
                            <tr>
                                <td class="ps-3 fw-bold text-dark">{{ $item->title }}</td>
                                <td class="text-muted fs-13">{{ $item->sub_heading ?? '—' }}</td>
                                <td class="text-muted fs-13" style="max-width: 320px;">
                                    {{ \Illuminate\Support\Str::limit($item->content, 90) }}
                                </td>
                                <td>
                                    @if ($item->is_active)
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-11">Active</span>
                                    @else
                                        <span
                                            class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 fs-11">Draft</span>
                                    @endif
                                </td>
                                <td class="fs-12 text-muted">{{ $item->created_at->format('d M Y, h:i A') }}</td>
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex gap-1">
                                        <!-- Edit Button -->
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#editGuidelineModal{{ $item->id }}" title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>

                                        <!-- Delete Button -->
                                        <form action="{{ route('mobile-ops.guidelines.destroy', $item->id) }}"
                                            method="POST" onsubmit="return confirm('Delete this guideline?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editGuidelineModal{{ $item->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <form action="{{ route('mobile-ops.guidelines.update', $item->id) }}"
                                            method="POST">
                                            @csrf
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fs-15">Edit Guideline</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body fs-13">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Title <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control"
                                                        value="{{ $item->title }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Sub Heading (Optional)</label>
                                                    <input type="text" name="sub_heading" class="form-control"
                                                        value="{{ $item->sub_heading }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Content / Instructions <span
                                                            class="text-danger">*</span></label>
                                                    <textarea name="content" class="form-control" rows="8" required>{{ $item->content }}</textarea>
                                                </div>

                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_active"
                                                        id="activeSwitch{{ $item->id }}"
                                                        {{ $item->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-semibold"
                                                        for="activeSwitch{{ $item->id }}">Publish to App</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-sm btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-sm btn-primary">Save
                                                    Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No guidelines published yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($guidelines->hasPages())
                <div class="p-3 border-top">
                    {{ $guidelines->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createGuidelineModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('mobile-ops.guidelines.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fs-15">Add New Hujjaj Guideline</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body fs-13">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control"
                                placeholder="e.g. Important Luggage & Baggage Rules" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sub Heading (Optional)</label>
                            <input type="text" name="sub_heading" class="form-control"
                                placeholder="e.g. Saudi Civil Aviation & Airline Guidelines">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Content / Instructions <span
                                    class="text-danger">*</span></label>
                            <textarea name="content" class="form-control" rows="8"
                                placeholder="Write full step-by-step instructions or notice details..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary">Publish Guideline</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
