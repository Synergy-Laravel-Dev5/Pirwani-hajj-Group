@extends('layout.mobile-master')

@section('content')
    <div class="pt-3 pb-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="fs-18 fw-bold text-dark m-0 d-flex align-items-center">
                <i class="mdi mdi-trash-can text-danger me-2 fs-22"></i>
                <span>Trashed Video Tutorials</span>
            </h4>
            <p class="text-muted fs-13 mb-0">View all soft-deleted training videos and guides. Click restore to recover any video tutorial back to the active listings.</p>
        </div>
        <div>
            <a href="{{ route('mobile-ops.videos.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm rounded-pill">
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
            <form method="GET" action="{{ route('mobile-ops.videos.trash') }}" class="d-flex gap-2" style="max-width: 380px; width: 100%;">
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm"
                    placeholder="Search by title or description...">
                <button type="submit" class="btn btn-sm btn-dark px-3"><i class="mdi mdi-magnify"></i></button>
                @if (!empty($search))
                    <a href="{{ route('mobile-ops.videos.trash') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
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
                            <th class="ps-3" style="width: 70px;">Thumbnail</th>
                            <th>Title & Description</th>
                            <th>Type</th>
                            <th>Video Source / Play</th>
                            <th>Deleted Date</th>
                            <th class="text-end pe-3" style="width: 140px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($videos as $video)
                            <tr>
                                <td class="ps-3">
                                    @if ($video->thumbnail_url)
                                        <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}"
                                            class="rounded object-fit-cover opacity-75 border" width="56" height="42">
                                    @else
                                        <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted border"
                                            style="width: 56px; height: 42px;">
                                            <i class="mdi mdi-video-outline fs-20"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark text-decoration-line-through fs-14">{{ $video->title }}</div>
                                    @if ($video->description)
                                        <div class="text-muted fs-12 text-truncate" style="max-width: 320px;">
                                            {{ $video->description }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if ($video->video_type === 'file')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill">
                                            <i class="mdi mdi-file-video-outline me-1"></i> Uploaded File
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">
                                            <i class="mdi mdi-youtube me-1"></i> External Link
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($video->play_url)
                                        <a href="{{ $video->play_url }}" target="_blank" class="btn btn-sm btn-light border rounded-pill px-2 py-1 fs-12 text-muted">
                                            <i class="mdi mdi-play-circle-outline me-1 text-primary"></i> Preview Video
                                        </a>
                                    @else
                                        <span class="text-muted fs-12">No media</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted fs-13">
                                        <i class="mdi mdi-clock-outline me-1"></i>
                                        {{ $video->deleted_at ? $video->deleted_at->format('d M Y, h:i A') : 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <form action="{{ route('mobile-ops.videos.restore', $video->id) }}" method="POST"
                                        onsubmit="return confirm('Restore this video tutorial back to active listings?');">
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
                                    <span class="fw-bold">No trashed videos found.</span>
                                    <p class="fs-13 mb-0 text-muted">Video tutorials deleted from the active list will appear here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($videos->hasPages())
            <div class="card-footer bg-white border-top py-2">
                {{ $videos->links() }}
            </div>
        @endif
    </div>
@endsection
