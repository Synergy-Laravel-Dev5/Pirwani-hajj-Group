@extends('layout.mobile-master')

@section('content')
    <div class="py-3 d-flex align-items-sm-center justify-content-between flex-column flex-sm-row gap-2">
        <div>
            <h4 class="fs-18 fw-semibold m-0 text-dark">Ziarat Management</h4>
            <p class="text-muted fs-13 mb-0">Upload and manage Ziarat places and pictures (Max 10 images per Ziarat).</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Category Filter Dropdown -->
            <label class="fs-13 fw-semibold text-muted mb-0 text-nowrap">Filter Category:</label>
            <div class="dropdown">
                <button class="btn btn-sm btn-white border shadow-sm dropdown-toggle px-3 d-flex align-items-center gap-2"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false" style="min-width: 170px;">
                    <span class="d-flex align-items-center gap-1 flex-grow-1 text-start">
                        @if (empty($category))
                            <span class="badge bg-dark rounded-circle p-1 me-1"></span> All Categories
                            ({{ $counts['all'] ?? 0 }})
                        @elseif($category === 'makkah')
                            <span class="badge bg-success rounded-circle p-1 me-1"></span> Makkah
                            ({{ $counts['makkah'] ?? 0 }})
                        @elseif($category === 'madina')
                            <span class="badge bg-info rounded-circle p-1 me-1"></span> Madina
                            ({{ $counts['madina'] ?? 0 }})
                        @elseif($category === 'other')
                            <span class="badge bg-secondary rounded-circle p-1 me-1"></span> Other
                            ({{ $counts['other'] ?? 0 }})
                        @endif
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 190px;">
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between fs-13 {{ empty($category) ? 'active' : '' }}"
                            href="{{ route('mobile-ops.gallery.index') }}">
                            <span>All Categories</span>
                            <span class="badge bg-dark rounded-pill">{{ $counts['all'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between fs-13 {{ $category === 'makkah' ? 'active' : '' }}"
                            href="{{ route('mobile-ops.gallery.index', ['category' => 'makkah']) }}">
                            <span class="text-success"><i class="mdi mdi-mosque me-1"></i> Makkah</span>
                            <span class="badge bg-success text-white rounded-pill">{{ $counts['makkah'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between fs-13 {{ $category === 'madina' ? 'active' : '' }}"
                            href="{{ route('mobile-ops.gallery.index', ['category' => 'madina']) }}">
                            <span class="text-info"><i class="mdi mdi-moon-waning-crescent me-1"></i> Madina</span>
                            <span class="badge bg-info text-white rounded-pill">{{ $counts['madina'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between fs-13 {{ $category === 'other' ? 'active' : '' }}"
                            href="{{ route('mobile-ops.gallery.index', ['category' => 'other']) }}">
                            <span class="text-secondary"><i class="mdi mdi-image-multiple-outline me-1"></i> Other</span>
                            <span class="badge bg-secondary text-white rounded-pill">{{ $counts['other'] ?? 0 }}</span>
                        </a>
                    </li>
                </ul>
            </div>

            @php
                $trashTotal = \App\Models\Gallery::onlyTrashed()->count();
            @endphp
            <a href="{{ route('mobile-ops.ziarat.trash') }}"
                class="btn btn-outline-danger btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1">
                <i class="mdi mdi-trash-can-outline"></i> View Trash
                @if ($trashTotal > 0)
                    <span class="badge bg-danger text-white rounded-pill ms-1">{{ $trashTotal }}</span>
                @endif
            </a>

            <!-- Add New Ziarat Button -->
            <button class="btn btn-sm btn-primary px-3 d-inline-flex align-items-center gap-1 shadow-sm"
                data-bs-toggle="modal" data-bs-target="#createGalleryModal">
                <i class="mdi mdi-plus fs-16"></i> Add New Ziarat
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
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

    <!-- Ziarat Table Card -->
    <div class="card border-0 shadow-sm" style="overflow: visible;">
        <div class="card-body" style="overflow: visible;">
            <div class="table-responsive">
                <table id="ziarat-datatable" class="table table-bordered dt-responsive align-middle mb-0 w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-nowrap" style="width: 75px;">Cover</th>
                            <th class="text-nowrap" style="min-width: 160px;">Title</th>
                            <th class="text-nowrap" style="width: 110px;">Category</th>
                            <th class="text-nowrap" style="width: 100px;">Photos</th>
                            <th style="min-width: 250px;">Description</th>
                            <th class="text-end pe-3 text-nowrap" style="width: 160px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($galleries as $item)
                            <tr>
                                <td class="ps-3 text-nowrap">
                                    <div role="button" data-bs-toggle="modal"
                                        data-bs-target="#ziaratDetailsModal{{ $item->id }}"
                                        class="position-relative d-inline-block rounded overflow-hidden shadow-sm"
                                        style="cursor: pointer; width: 52px; height: 52px;" title="Click to view details">
                                        @if ($item->image_url)
                                            <img src="{{ $item->image_url }}" class="w-100 h-100"
                                                style="object-fit: cover;">
                                        @else
                                            <div
                                                class="w-100 h-100 bg-light d-flex align-items-center justify-content-center text-muted">
                                                <i class="mdi mdi-image-off fs-20"></i>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->title }}</div>
                                    {{-- <div class="fs-12 text-muted">ID: #{{ $item->id }}</div> --}}
                                </td>
                                <td class="text-nowrap">
                                    @if ($item->category === 'makkah')
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 text-uppercase fs-11">
                                            Makkah
                                        </span>
                                    @elseif($item->category === 'madina')
                                        <span
                                            class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 text-uppercase fs-11">
                                            Madina
                                        </span>
                                    @else
                                        <span
                                            class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 text-uppercase fs-11">
                                            Other
                                        </span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <i class="mdi mdi-image-multiple text-primary me-1"></i>
                                        {{ $item->images->count() }} / 10
                                    </span>
                                </td>
                                <td class="text-muted fs-13" style="max-width: 340px; white-space: normal; word-break: break-word;">
                                    {{ $item->description ? \Illuminate\Support\Str::limit($item->description, 100) : '—' }}
                                </td>
                                <td class="text-end pe-3 text-nowrap">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <!-- View Details Button (aligned with inquiries style) -->
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#ziaratDetailsModal{{ $item->id }}"
                                            title="View Details & All Images">
                                            <i class="mdi mdi-eye-outline me-1"></i> Details
                                        </button>

                                        <!-- Edit / Manage Photos Button -->
                                        <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="modal"
                                            data-bs-target="#editGalleryModal{{ $item->id }}"
                                            title="Edit Ziarat & Manage Photos">
                                            <i class="mdi mdi-pencil text-muted"></i>
                                        </button>

                                        <!-- Delete Button -->
                                        <form action="{{ route('mobile-ops.gallery.destroy', $item->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Delete this Ziarat along with all its photos?');"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger"
                                                title="Delete Ziarat">
                                                <i class="mdi mdi-trash-can-outline"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- View Details Modal (Showing full details and all 1 to 10 images) -->
                            <div class="modal fade" id="ziaratDetailsModal{{ $item->id }}" tabindex="-1"
                                aria-labelledby="ziaratModalLabel{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light py-3">
                                            <div>
                                                <h5 class="modal-title fs-16 fw-bold mb-0"
                                                    id="ziaratModalLabel{{ $item->id }}">
                                                    Ziarat Details: {{ $item->title }}
                                                </h5>
                                                <span class="fs-12 text-muted">ID #{{ $item->id }} &bull; Added
                                                    {{ $item->created_at->format('d M Y, h:i A') }}</span>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <!-- Top Info Summary Cards -->
                                            <div class="row g-3 mb-4">
                                                <div class="col-sm-6">
                                                    <div class="p-3 rounded bg-light">
                                                        <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">
                                                            Ziarat Title</div>
                                                        <div class="fw-bold text-dark fs-15">{{ $item->title }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="p-3 rounded bg-light">
                                                        <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">
                                                            Category</div>
                                                        <div>
                                                            @if ($item->category === 'makkah')
                                                                <span
                                                                    class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 text-uppercase fs-11">Makkah</span>
                                                            @elseif($item->category === 'madina')
                                                                <span
                                                                    class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 text-uppercase fs-11">Madina</span>
                                                            @else
                                                                <span
                                                                    class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 text-uppercase fs-11">Other</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="p-3 rounded bg-light">
                                                        <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Total
                                                            Photos</div>
                                                        <div class="fw-bold text-dark fs-14">
                                                            <i
                                                                class="mdi mdi-image-multiple text-primary me-1"></i>{{ $item->images->count() }}
                                                            of 10
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Historical / Ziarat Description -->
                                            <div class="mb-4">
                                                <h6 class="fs-13 fw-bold text-uppercase text-muted mb-2">Historical &amp;
                                                    Ziarat Description</h6>
                                                <div class="p-3 border rounded bg-light-subtle text-dark fs-13 lh-base">
                                                    {{ $item->description ?: 'No detailed description provided.' }}
                                                </div>
                                            </div>

                                            <!-- Images Gallery Section -->
                                            <div>
                                                <h6
                                                    class="fs-13 fw-bold text-uppercase text-muted mb-3 d-flex align-items-center justify-content-between">
                                                    <span><i class="mdi mdi-image-album text-primary me-1 fs-16"></i>
                                                        Attached Images ({{ $item->images->count() }})</span>
                                                    <span class="text-muted fs-12 fw-normal">Click any photo to view full
                                                        size</span>
                                                </h6>

                                                <div class="row g-2">
                                                    @forelse($item->images as $img)
                                                        <div class="col-6 col-sm-4 col-md-3">
                                                            <div
                                                                class="border rounded p-1 bg-white position-relative shadow-none h-100 d-flex flex-column">
                                                                <a href="{{ $img->image_url }}" target="_blank"
                                                                    class="d-block overflow-hidden rounded mb-1"
                                                                    style="height: 120px;">
                                                                    <img src="{{ $img->image_url }}" alt="Ziarat photo"
                                                                        class="w-100 h-100"
                                                                        style="object-fit: cover; transition: transform 0.2s;"
                                                                        onmouseover="this.style.transform='scale(1.05)'"
                                                                        onmouseout="this.style.transform='scale(1)'">
                                                                </a>
                                                                <div
                                                                    class="mt-auto pt-1 d-flex align-items-center justify-content-between">
                                                                    @if ($img->is_primary)
                                                                        <span class="badge bg-success fs-10 px-2 py-1"><i
                                                                                class="mdi mdi-star me-1"></i>Cover</span>
                                                                    @else
                                                                        <span
                                                                            class="badge bg-light text-muted border fs-10">Photo</span>
                                                                    @endif
                                                                    <a href="{{ $img->image_url }}" target="_blank"
                                                                        class="btn btn-xs btn-outline-primary py-0 px-1"
                                                                        title="Open full size">
                                                                        <i class="mdi mdi-open-in-new"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="col-12">
                                                            <div
                                                                class="text-center py-4 bg-light rounded text-muted fs-13">
                                                                <i
                                                                    class="mdi mdi-image-off-outline fs-24 d-block mb-1"></i>
                                                                No images uploaded for this Ziarat.
                                                            </div>
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light py-2">
                                            <button type="button" class="btn btn-light border btn-sm"
                                                data-bs-dismiss="modal" data-bs-toggle="modal"
                                                data-bs-target="#editGalleryModal{{ $item->id }}">
                                                <i class="mdi mdi-pencil me-1"></i> Edit / Manage Photos
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit & Photo Gallery Modal -->
                            <div class="modal fade" id="editGalleryModal{{ $item->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light py-3">
                                            <h5 class="modal-title fs-16 fw-bold mb-0">Edit Ziarat: {{ $item->title }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 fs-13">
                                            <!-- Photo Strip -->
                                            <label class="form-label fw-semibold">Uploaded Photos
                                                ({{ $item->images->count() }} of 10)
                                            </label>
                                            <div class="d-flex flex-wrap gap-2 p-2 bg-light rounded mb-3 border">
                                                @forelse($item->images as $img)
                                                    <div class="position-relative text-center border rounded bg-white p-1"
                                                        style="width: 110px;">
                                                        <img src="{{ $img->image_url }}" class="rounded mb-1"
                                                            style="width: 100px; height: 75px; object-fit: cover;">
                                                        @if ($img->is_primary)
                                                            <span class="badge bg-success w-100 fs-10 mb-1">Cover</span>
                                                        @else
                                                            <form
                                                                action="{{ route('mobile-ops.gallery.image.primary', $img->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="btn btn-xs btn-outline-secondary w-100 py-0 fs-10 mb-1">Set
                                                                    Cover</button>
                                                            </form>
                                                        @endif
                                                        <form
                                                            action="{{ route('mobile-ops.gallery.image.delete', $img->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Remove this photo?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-xs btn-outline-danger w-100 py-0 fs-10">Delete</button>
                                                        </form>
                                                    </div>
                                                @empty
                                                    <div class="text-muted p-2">No individual photos found.</div>
                                                @endforelse
                                            </div>

                                            <!-- Update Form -->
                                            <form action="{{ route('mobile-ops.gallery.update', $item->id) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row g-2 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Category <span
                                                                class="text-danger">*</span></label>
                                                        <select name="category" class="form-select" required>
                                                            <option value="makkah"
                                                                {{ $item->category === 'makkah' ? 'selected' : '' }}>Makkah
                                                            </option>
                                                            <option value="madina"
                                                                {{ $item->category === 'madina' ? 'selected' : '' }}>Madina
                                                            </option>
                                                            <option value="other"
                                                                {{ $item->category === 'other' ? 'selected' : '' }}>Other
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Title <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="title" class="form-control"
                                                            value="{{ $item->title }}" required>
                                                    </div>
                                                </div>

                                                @php
                                                    $remainingSlots = max(0, 10 - $item->images->count());
                                                @endphp

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        Add More Photos
                                                        @if ($remainingSlots > 0)
                                                            <span class="text-muted fs-12">(You can upload up to
                                                                {{ $remainingSlots }} more)</span>
                                                        @else
                                                            <span class="text-danger fs-12">(Maximum 10 photos reached.
                                                                Delete one above to upload new ones.)</span>
                                                        @endif
                                                    </label>
                                                    <input type="file" name="images[]" class="form-control" multiple
                                                        accept="image/*" {{ $remainingSlots === 0 ? 'disabled' : '' }}>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Historical / Ziarat
                                                        Description</label>
                                                    <textarea name="description" class="form-control" rows="3">{{ $item->description }}</textarea>
                                                </div>

                                                <div class="text-end">
                                                    <button type="button" class="btn btn-sm btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-sm btn-primary">Save
                                                        Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createGalleryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('mobile-ops.gallery.store') }}" method="POST" enctype="multipart/form-data"
                    id="createGalleryForm">
                    @csrf
                    <div class="modal-header bg-light py-3">
                        <h5 class="modal-title fs-16 fw-bold mb-0">Add New Ziarat Place</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 fs-13">
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="makkah">Makkah</option>
                                    <option value="madina">Madina</option>
                                    <option value="other" selected>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control"
                                    placeholder="e.g. Masjid Al Haram / Kaaba Sharif" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Upload Photos <span class="text-danger">* (Select 1 to
                                    10 images)</span></label>
                            <input type="file" name="images[]" id="createImagesInput" class="form-control" multiple
                                accept="image/*" required>
                            <small class="text-muted d-block mt-1">The first photo selected will automatically serve as the
                                card cover.</small>
                            <div id="imageLimitError" class="text-danger fs-12 mt-1 d-none">You cannot select more than 10
                                images.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Historical / Ziarat Description</label>
                            <textarea name="description" class="form-control" rows="4"
                                placeholder="Brief history and significance of this holy place..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="submitCreateBtn">Upload Ziarat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const input = document.getElementById("createImagesInput");
            const errorDiv = document.getElementById("imageLimitError");
            const submitBtn = document.getElementById("submitCreateBtn");

            if (input) {
                input.addEventListener("change", function() {
                    if (this.files.length > 10) {
                        errorDiv.classList.remove("d-none");
                        submitBtn.disabled = true;
                    } else {
                        errorDiv.classList.add("d-none");
                        submitBtn.disabled = false;
                    }
                });
            }
        });
    </script>

    <style>
        .bg-primary-subtle {
            background-color: rgba(13, 110, 253, 0.1) !important;
        }

        .bg-info-subtle {
            background-color: rgba(13, 202, 240, 0.1) !important;
        }

        .bg-success-subtle {
            background-color: rgba(25, 135, 84, 0.1) !important;
        }

        .bg-secondary-subtle {
            background-color: rgba(108, 117, 125, 0.1) !important;
        }

        .btn-white {
            background-color: #fff;
            color: #333;
        }

        .btn-white:hover {
            background-color: #f8f9fa;
        }
    </style>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#ziarat-datatable').DataTable({
                paging: true,
                info: true,
                searching: false,
                lengthChange: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                pageLength: 10,
                responsive: true,
                order: [],
                columnDefs: [{
                    orderable: false,
                    targets: [0, 5]
                }],
                language: {
                    lengthMenu: "Show _MENU_ entries",
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
