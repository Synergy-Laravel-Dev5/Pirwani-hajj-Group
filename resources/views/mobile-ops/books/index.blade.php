@extends('layout.mobile-master')

@section('content')
    <div class="pt-3 pb-2 d-flex align-items-sm-center justify-content-between flex-column flex-sm-row">
        <div>
            <h4 class="fs-18 fw-bold text-dark m-0">Hajj Information Books</h4>
            <p class="text-muted fs-13 mb-0">Upload and manage guides, booklets, and reference manuals for the mobile app.
            </p>
        </div>
        <div class="mt-2 mt-sm-0">
            <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createBookModal">
                <i class="mdi mdi-plus me-1"></i> Add New Book
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
            <form method="GET" action="{{ route('mobile-ops.books.index') }}" class="d-flex gap-2">

                <div class="d-flex align-items-center me-auto">
                    <label for="per_page" class="me-2 mb-0 text-nowrap">Show:</label>
                    <select name="per_page" id="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 rows</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 rows</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 rows</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 rows</option>
                    </select>
                </div>
                <input type="text" name="search" class="form-control form-control-sm w-25"
                    placeholder="Search books by title..." value="{{ $search ?? '' }}">
                <button type="submit" class="btn btn-sm btn-dark px-3">Search</button>
                @if (!empty($search))
                    <a href="{{ route('mobile-ops.books.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Books Table -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table  align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 70px;">Thumbnail</th>
                            <th>Title</th>
                            <th>Format</th>
                            <th>Size</th>
                            <th>File Link</th>
                            <th>Uploaded Date</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                            <tr>
                                <td class="ps-3">
                                    <a href="{{ $book->thumbnail_url }}" target="_blank">
                                        <img src="{{ $book->thumbnail_url }}" class="rounded shadow-sm"
                                            style="width: 50px; height: 65px; object-fit: cover;">
                                    </a>
                                </td>
                                <td class="fw-bold text-dark">{{ $book->title }}</td>
                                <td>
                                    <span
                                        class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 text-uppercase fs-11">
                                        {{ $book->file_type ?? 'FILE' }}
                                    </span>
                                </td>
                                <td class="fs-12 text-muted">{{ $book->file_size ?? '—' }}</td>
                                <td>
                                    <a href="{{ $book->file_url }}" target="_blank"
                                        class="btn btn-xs btn-outline-secondary">
                                        <i class="mdi mdi-download me-1"></i> View / Download
                                    </a>
                                </td>
                                <td class="fs-12 text-muted">{{ $book->created_at->format('d M Y, h:i A') }}</td>
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex gap-1">
                                        <!-- Edit Button -->
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#editBookModal{{ $book->id }}" title="Edit Book">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>

                                        <!-- Delete Button -->
                                        <form action="{{ route('mobile-ops.books.destroy', $book->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this book?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Book Modal -->
                            <div class="modal fade" id="editBookModal{{ $book->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <form action="{{ route('mobile-ops.books.update', $book->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fs-15">Edit Book</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body fs-13">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Book Title <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control"
                                                        value="{{ $book->title }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Replace Thumbnail Image</label>
                                                    <input type="file" name="thumbnail" class="form-control"
                                                        accept="image/*">
                                                    <small class="text-muted">Leave empty to keep existing
                                                        thumbnail.</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Replace Document / File</label>
                                                    <input type="file" name="file" class="form-control">
                                                    <small class="text-muted">Current: {{ $book->file_type }}
                                                        ({{ $book->file_size }})
                                                        . Leave empty to keep.</small>
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
                                <td colspan="7" class="text-center py-4 text-muted">No Hajj books uploaded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($books->hasPages())
                <div class="p-3 border-top">
                    {{-- {{ $books->links('pagination::bootstrap-5') }} --}}
                    {{ $books->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create Book Modal -->
    <div class="modal fade" id="createBookModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('mobile-ops.books.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fs-15">Add New Hajj Information Book</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body fs-13">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Book Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control"
                                placeholder="e.g. Complete Hajj & Umrah Guide" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Thumbnail Image <span
                                    class="text-danger">*</span></label>
                            <input type="file" name="thumbnail" class="form-control" accept="image/*" required>
                            <small class="text-muted">Used as the book cover on the mobile screen.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">File / Document <span
                                    class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control" required>
                            <small class="text-muted">Supports PDF, DOCX, EPUB, etc. (Max 50MB).</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary">Upload Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
