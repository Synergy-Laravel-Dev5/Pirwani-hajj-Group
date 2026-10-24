@extends('layout.mobile-master')

@section('content')
    <div class="pt-3 pb-2 d-flex align-items-sm-center justify-content-between flex-column flex-sm-row">
        <div>
            <h4 class="fs-18 fw-bold text-dark m-0">Hajj Video Tutorials</h4>
            <p class="text-muted fs-13 mb-0">Manage training guides, tutorials, and video instructions for pilgrims.
            </p>
        </div>
        <div class="mt-2 mt-sm-0">
            <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createVideoModal">
                <i class="mdi mdi-plus me-1"></i> Add New Video
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
            <form method="GET" action="{{ route('mobile-ops.videos.index') }}" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Search videos by title..." value="{{ $search ?? '' }}">
                <button type="submit" class="btn btn-sm btn-dark px-3">Search</button>
                @if (!empty($search))
                    <a href="{{ route('mobile-ops.videos.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Videos Table -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table  align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 80px;">Thumbnail</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Preview / Source</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($videos as $video)
                            <tr>
                                <td class="ps-3">
                                    @if ($video->thumbnail_url)
                                        <a href="{{ $video->thumbnail_url }}" target="_blank">
                                            <img src="{{ $video->thumbnail_url }}" class="rounded shadow-sm"
                                                style="width: 65px; height: 45px; object-fit: cover;">
                                        </a>
                                    @else
                                        <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted"
                                            style="width: 65px; height: 45px;">
                                            <i class="mdi mdi-play-circle-outline fs-20"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-bold text-dark">{{ $video->title }}</td>
                                <td>
                                    @if ($video->video_type === 'file')
                                        <span
                                            class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-11">Uploaded
                                            File</span>
                                    @else
                                        <span
                                            class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-11">Online
                                            Link</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ $video->play_url }}" target="_blank"
                                        class="btn btn-xs btn-outline-secondary">
                                        <i class="mdi mdi-play me-1"></i> Watch Video
                                    </a>
                                </td>
                                <td class="text-muted fs-13" style="max-width: 200px;">
                                    {{ $video->description ? \Illuminate\Support\Str::limit($video->description, 45) : '—' }}
                                </td>
                                <td>
                                    @if ($video->is_active)
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-11">Active</span>
                                    @else
                                        <span
                                            class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 fs-11">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex gap-1">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#editVideoModal{{ $video->id }}" title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>

                                        <form action="{{ route('mobile-ops.videos.destroy', $video->id) }}" method="POST"
                                            onsubmit="return confirm('Delete this video?');">
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
                            <div class="modal fade" id="editVideoModal{{ $video->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <form action="{{ route('mobile-ops.videos.update', $video->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fs-15">Edit Video Tutorial</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body fs-13">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Title <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control"
                                                        value="{{ $video->title }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Thumbnail Image
                                                        (Optional)
                                                    </label>
                                                    <input type="file" name="thumbnail" class="form-control"
                                                        accept="image/*">
                                                    <small class="text-muted">Leave empty to keep existing
                                                        thumbnail.</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Video Source</label>
                                                    <div class="d-flex gap-3 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="source_type" id="editTypeFile{{ $video->id }}"
                                                                value="file"
                                                                {{ $video->video_type === 'file' ? 'checked' : '' }}
                                                                onchange="toggleEditSource({{ $video->id }}, 'file')">
                                                            <label class="form-check-label"
                                                                for="editTypeFile{{ $video->id }}">Upload Video
                                                                File</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="source_type" id="editTypeLink{{ $video->id }}"
                                                                value="link"
                                                                {{ $video->video_type === 'link' ? 'checked' : '' }}
                                                                onchange="toggleEditSource({{ $video->id }}, 'link')">
                                                            <label class="form-check-label"
                                                                for="editTypeLink{{ $video->id }}">External Video
                                                                URL</label>
                                                        </div>
                                                    </div>

                                                    <div id="editFileInputBox{{ $video->id }}"
                                                        class="{{ $video->video_type === 'file' ? '' : 'd-none' }}">
                                                        <input type="file" name="video_file" class="form-control"
                                                            accept="video/mp4,video/quicktime,video/webm">
                                                        <small class="text-muted">Leave empty to keep current file.</small>
                                                    </div>

                                                    <div id="editLinkInputBox{{ $video->id }}"
                                                        class="{{ $video->video_type === 'link' ? '' : 'd-none' }}">
                                                        <input type="url" name="video_link" class="form-control"
                                                            value="{{ $video->video_link }}"
                                                            placeholder="https://www.youtube.com/watch?v=...">
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Description (Optional)</label>
                                                    <textarea name="description" class="form-control" rows="3">{{ $video->description }}</textarea>
                                                </div>

                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_active"
                                                        id="activeVidSwitch{{ $video->id }}"
                                                        {{ $video->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-semibold"
                                                        for="activeVidSwitch{{ $video->id }}">Publish to App</label>
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
                                <td colspan="7" class="text-center py-4 text-muted">No video tutorials added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($videos->hasPages())
                <div class="p-3 border-top">
                    {{ $videos->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createVideoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('mobile-ops.videos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fs-15">Add New Video Tutorial</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body fs-13">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control"
                                placeholder="e.g. Step-by-Step Hajj Guide" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Thumbnail Image (Optional)</label>
                            <input type="file" name="thumbnail" class="form-control" accept="image/*">
                            <small class="text-muted">If omitted for YouTube links, the video's default preview is used
                                automatically.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Choose Video Source <span
                                    class="text-danger">*</span></label>
                            <div class="d-flex gap-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="source_type"
                                        id="createTypeLink" value="link" checked onchange="toggleCreateSource('link')">
                                    <label class="form-check-label fw-semibold" for="createTypeLink">External Video URL
                                        (YouTube / Cloud)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="source_type"
                                        id="createTypeFile" value="file" onchange="toggleCreateSource('file')">
                                    <label class="form-check-label fw-semibold" for="createTypeFile">Upload Video
                                        File</label>
                                </div>
                            </div>

                            <!-- Link Box -->
                            <div id="createLinkBox">
                                <input type="url" name="video_link" id="createLinkInput" class="form-control"
                                    placeholder="https://www.youtube.com/watch?v=...">
                            </div>

                            <!-- File Box -->
                            <div id="createFileBox" class="d-none">
                                <input type="file" name="video_file" id="createFileInput" class="form-control"
                                    accept="video/mp4,video/quicktime,video/webm">
                                <small class="text-muted">Supports MP4, MOV, WEBM (Max 100MB).</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description (Optional)</label>
                            <textarea name="description" class="form-control" rows="3"
                                placeholder="Brief summary of what this video demonstrates..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary">Upload Video</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleCreateSource(type) {
            const linkBox = document.getElementById('createLinkBox');
            const fileBox = document.getElementById('createFileBox');
            const linkInput = document.getElementById('createLinkInput');
            const fileInput = document.getElementById('createFileInput');

            if (type === 'file') {
                linkBox.classList.add('d-none');
                fileBox.classList.remove('d-none');
                linkInput.value = '';
            } else {
                fileBox.classList.add('d-none');
                linkBox.classList.remove('d-none');
                fileInput.value = '';
            }
        }

        function toggleEditSource(id, type) {
            const linkBox = document.getElementById('editLinkInputBox' + id);
            const fileBox = document.getElementById('editFileInputBox' + id);

            if (type === 'file') {
                linkBox.classList.add('d-none');
                fileBox.classList.remove('d-none');
            } else {
                fileBox.classList.add('d-none');
                linkBox.classList.remove('d-none');
            }
        }
    </script>
@endsection
