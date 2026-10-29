@extends('layout.mobile-master')

@section('content')
    <div class="pt-3 pb-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="fs-18 fw-bold text-dark m-0 d-flex align-items-center">
                <i class="mdi mdi-trash-can text-danger me-2 fs-22"></i>
                <span>Trashed Hajj Books</span>
            </h4>
            <p class="text-muted fs-13 mb-0">View all soft-deleted reference books and guides. Click restore to recover any book back to the active listing.</p>
        </div>
        <div>
            <a href="{{ route('mobile-ops.books.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm rounded-pill">
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
            <form method="GET" action="{{ route('mobile-ops.books.trash') }}" class="d-flex gap-2" style="max-width: 380px; width: 100%;">
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm"
                    placeholder="Search by title, file type, or year...">
                <button type="submit" class="btn btn-sm btn-dark px-3"><i class="mdi mdi-magnify"></i></button>
                @if (!empty($search))
                    <a href="{{ route('mobile-ops.books.trash') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
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
                            <th class="ps-3" style="width: 70px;">Cover</th>
                            <th>Book Title</th>
                            <th>File Format</th>
                            <th>File Size</th>
                            <th>Uploaded Year</th>
                            <th>Deleted Date</th>
                            <th class="text-end pe-3" style="width: 140px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                            <tr>
                                <td class="ps-3">
                                    @if ($book->thumbnail_url)
                                        <img src="{{ $book->thumbnail_url }}" alt="{{ $book->title }}"
                                            class="rounded object-fit-cover opacity-75 border" width="46" height="58">
                                    @else
                                        <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted border"
                                            style="width: 46px; height: 58px;">
                                            <i class="mdi mdi-book-open-outline fs-20"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark text-decoration-line-through fs-14">{{ $book->title }}</div>
                                    @if ($book->file_url)
                                        <a href="{{ $book->file_url }}" target="_blank" class="text-muted fs-12 d-inline-flex align-items-center gap-1">
                                            <i class="mdi mdi-open-in-new"></i> Preview File
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border rounded-pill px-2 py-1 fs-12">
                                        <i class="mdi mdi-file-document-outline me-1 text-primary"></i>
                                        {{ $book->file_type ?? 'PDF' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-secondary fs-13">{{ $book->file_size ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-muted border rounded-pill px-2 py-1 fs-12">
                                        {{ $book->created_at ? $book->created_at->format('Y') : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted fs-13">
                                        <i class="mdi mdi-clock-outline me-1"></i>
                                        {{ $book->deleted_at ? $book->deleted_at->format('d M Y, h:i A') : 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <form action="{{ route('mobile-ops.books.restore', $book->id) }}" method="POST"
                                        onsubmit="return confirm('Restore this book back to active listing?');">
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
                                    <span class="fw-bold">No trashed books found.</span>
                                    <p class="fs-13 mb-0 text-muted">Books moved to trash will appear here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($books->hasPages())
            <div class="card-footer bg-white border-top py-2">
                {{ $books->links() }}
            </div>
        @endif
    </div>
@endsection
