@extends('layout.mobile-master')

@section('content')
    <div class="py-3 d-flex align-items-sm-center justify-content-between flex-column flex-sm-row gap-2">
        <div>
            <h4 class="fs-18 fw-semibold m-0 text-dark">Visa Inquiries</h4>
            <p class="text-muted fs-13 mb-0">Review and manage guest visa applications submitted via mobile app.</p>
        </div>

        <!-- Status Filter Dropdown -->
        <div class="d-flex align-items-center gap-2">
            <label class="fs-13 fw-semibold text-muted mb-0 text-nowrap">Filter Status:</label>
            <div class="dropdown">
                <button class="btn btn-sm btn-white border shadow-sm dropdown-toggle px-3 d-flex align-items-center gap-2"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false" style="min-width: 180px;">
                    <span class="d-flex align-items-center gap-1 flex-grow-1 text-start">
                        @if (empty($status))
                            <span class="badge bg-dark rounded-circle p-1 me-1"></span> All Statuses
                            ({{ $counts['all'] ?? 0 }})
                        @elseif($status === 'pending')
                            <span class="badge bg-warning rounded-circle p-1 me-1"></span> Pending
                            ({{ $counts['pending'] ?? 0 }})
                        @elseif($status === 'contacted')
                            <span class="badge bg-info rounded-circle p-1 me-1"></span> Contacted
                            ({{ $counts['contacted'] ?? 0 }})
                        @elseif($status === 'in_progress')
                            <span class="badge bg-primary rounded-circle p-1 me-1"></span> In Progress
                            ({{ $counts['in_progress'] ?? 0 }})
                        @elseif($status === 'completed')
                            <span class="badge bg-success rounded-circle p-1 me-1"></span> Completed
                            ({{ $counts['completed'] ?? 0 }})
                        @endif
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 200px;">
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between fs-13 {{ empty($status) ? 'active' : '' }}"
                            href="{{ route('mobile-ops.visa-inquiries.index') }}">
                            <span>All Inquiries</span>
                            <span class="badge bg-dark rounded-pill">{{ $counts['all'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between fs-13 {{ $status === 'pending' ? 'active' : '' }}"
                            href="{{ route('mobile-ops.visa-inquiries.index', ['status' => 'pending']) }}">
                            <span class="text-warning-emphasis"><i class="mdi mdi-clock-outline me-1"></i> Pending</span>
                            <span class="badge bg-warning text-dark rounded-pill">{{ $counts['pending'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between fs-13 {{ $status === 'contacted' ? 'active' : '' }}"
                            href="{{ route('mobile-ops.visa-inquiries.index', ['status' => 'contacted']) }}">
                            <span class="text-info"><i class="mdi mdi-phone-in-talk-outline me-1"></i> Contacted</span>
                            <span class="badge bg-info text-white rounded-pill">{{ $counts['contacted'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between fs-13 {{ $status === 'in_progress' ? 'active' : '' }}"
                            href="{{ route('mobile-ops.visa-inquiries.index', ['status' => 'in_progress']) }}">
                            <span class="text-primary"><i class="mdi mdi-progress-clock me-1"></i> In Progress</span>
                            <span class="badge bg-primary text-white rounded-pill">{{ $counts['in_progress'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between fs-13 {{ $status === 'completed' ? 'active' : '' }}"
                            href="{{ route('mobile-ops.visa-inquiries.index', ['status' => 'completed']) }}">
                            <span class="text-success"><i class="mdi mdi-check-circle-outline me-1"></i> Completed</span>
                            <span class="badge bg-success text-white rounded-pill">{{ $counts['completed'] ?? 0 }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Inquiries Table Card -->
    <div class="card border-0 shadow-sm" style="overflow: visible;">
        <div class="card-body p-0" style="overflow: visible;">
            <div>
                <table class="table  align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Applicant</th>
                            <th>Contact</th>
                            <th>Destination / Visa</th>
                            {{-- <th>Documents</th> --}}
                            <th>Date</th>
                            <th class="text-end pe-3" style="min-width: 200px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inquiries as $item)
                            @php
                                $docs = is_array($item->documents)
                                    ? $item->documents
                                    : (is_string($item->documents)
                                        ? json_decode($item->documents, true)
                                        : []);
                                if (!is_array($docs)) {
                                    $docs = [];
                                }
                            @endphp
                            <tr>
                                <!-- 1. Applicant -->
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">{{ $item->full_name }}</div>
                                    <div class="fs-12 text-muted mt-1">
                                        <i class="mdi mdi-card-account-details-outline me-1 text-primary"></i>
                                        <span>{{ $item->cnic ?? ($item->passport_number ?? 'No CNIC/Passport') }}</span>
                                    </div>
                                </td>

                                <!-- 2. Contact -->
                                <td>
                                    <div class="fs-13">
                                        <i class="mdi mdi-phone me-1 text-primary"></i>
                                        <a href="tel:{{ $item->phone_number ?? $item->contact }}"
                                            class="text-secondary text-decoration-none">
                                            {{ $item->phone_number ?? $item->contact }}
                                        </a>
                                    </div>
                                    <div class="fs-12 text-muted mt-1">
                                        <i class="mdi mdi-email me-1 text-danger"></i>
                                        @if ($item->email)
                                            <a href="mailto:{{ $item->email }}"
                                                class="text-secondary text-decoration-none">{{ $item->email }}</a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- 3. Destination / Visa -->
                                <td>
                                    <span
                                        class="badge bg-light text-primary border px-2 py-1 text-wrap text-start fs-12 fw-semibold">
                                        <i
                                            class="mdi mdi-map-marker-outline me-1"></i>{{ $item->country->country_name ?? '—' }}
                                    </span>
                                    <div class="fs-12 text-muted mt-1">
                                        <i
                                            class="mdi mdi-card-bulleted-settings-outline me-1"></i>{{ $item->visaType->visa_name ?? 'General / Any' }}
                                    </div>
                                </td>

                                <!-- 4. Documents -->
                                {{-- <td>
                                    @if (!empty($docs) && count($docs) > 0)
                                        <div class="d-flex flex-wrap gap-1 align-items-center">
                                            @foreach ($docs as $doc)
                                                @php
                                                    $docPath = $doc['file_path'] ?? '';
                                                    $docTitle = $doc['title'] ?? 'Document';
                                                    $ext = strtolower(pathinfo($docPath, PATHINFO_EXTENSION));
                                                    $isPdf = $ext === 'pdf';
                                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                                    $docUrl = !empty($docPath) ? asset('storage/' . $docPath) : '#';
                                                @endphp
                                                <a href="{{ $docUrl }}" target="_blank"
                                                    class="badge {{ $isPdf ? 'bg-danger-subtle text-danger border border-danger-subtle' : ($isImage ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle') }} text-decoration-none d-inline-flex align-items-center gap-1"
                                                    title="{{ $docTitle }}">
                                                    @if ($isPdf)
                                                        <i class="mdi mdi-file-pdf-box fs-13"></i>
                                                    @elseif ($isImage)
                                                        <i class="mdi mdi-file-image-outline fs-13"></i>
                                                    @else
                                                        <i class="mdi mdi-file-document-outline fs-13"></i>
                                                    @endif
                                                    <span class="text-truncate"
                                                        style="max-width: 90px;">{{ $docTitle }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted fs-12">None</span>
                                    @endif
                                </td> --}}

                                <!-- 5. Date -->
                                <td class="fs-12 text-muted">
                                    <div>
                                        <i
                                            class="mdi mdi-calendar-blank-outline me-1"></i>{{ $item->created_at ? $item->created_at->format('d M Y') : '—' }}
                                    </div>
                                    <div class="text-muted">
                                        <i
                                            class="mdi mdi-clock-outline me-1"></i>{{ $item->created_at ? $item->created_at->format('h:i A') : '' }}
                                    </div>
                                </td>

                                <!-- 6. Action -->
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <!-- Details Button opens Popup -->
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#inquiryModal{{ $item->id }}">
                                            <i class="mdi mdi-eye-outline me-1"></i> Details
                                        </button>

                                        <!-- Status Change Dropdown -->
                                        <div class="dropdown">
                                            <button
                                                class="btn btn-sm btn-light border dropdown-toggle d-inline-flex align-items-center gap-1"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                @if ($item->status === 'pending')
                                                    <span class="badge bg-warning rounded-circle p-1"></span> Pending
                                                @elseif($item->status === 'contacted')
                                                    <span class="badge bg-info rounded-circle p-1"></span> Contacted
                                                @elseif($item->status === 'in_progress')
                                                    <span class="badge bg-primary rounded-circle p-1"></span> In Progress
                                                @elseif($item->status === 'completed')
                                                    <span class="badge bg-success rounded-circle p-1"></span> Completed
                                                @elseif($item->status === 'cancelled')
                                                    <span class="badge bg-danger rounded-circle p-1"></span> Cancelled
                                                @else
                                                    Status
                                                @endif
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0"
                                                style="min-width: 175px;">
                                                <li class="dropdown-header text-uppercase fs-11 fw-bold text-muted">Change
                                                    Status</li>
                                                <li>
                                                    <form
                                                        action="{{ route('mobile-ops.visa-inquiries.status', $item->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="pending">
                                                        <button type="submit"
                                                            class="dropdown-item fs-13 d-flex align-items-center {{ $item->status === 'pending' ? 'active' : '' }}">
                                                            <i class="mdi mdi-clock-outline me-2 text-warning"></i> Mark as
                                                            Pending
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form
                                                        action="{{ route('mobile-ops.visa-inquiries.status', $item->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="contacted">
                                                        <button type="submit"
                                                            class="dropdown-item fs-13 d-flex align-items-center {{ $item->status === 'contacted' ? 'active' : '' }}">
                                                            <i class="mdi mdi-phone-in-talk-outline me-2 text-info"></i>
                                                            Mark as Contacted
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form
                                                        action="{{ route('mobile-ops.visa-inquiries.status', $item->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="in_progress">
                                                        <button type="submit"
                                                            class="dropdown-item fs-13 d-flex align-items-center {{ $item->status === 'in_progress' ? 'active' : '' }}">
                                                            <i class="mdi mdi-progress-clock me-2 text-primary"></i> Mark
                                                            In Progress
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form
                                                        action="{{ route('mobile-ops.visa-inquiries.status', $item->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="submit"
                                                            class="dropdown-item fs-13 d-flex align-items-center {{ $item->status === 'completed' ? 'active' : '' }}">
                                                            <i class="mdi mdi-check-circle-outline me-2 text-success"></i>
                                                            Mark Completed
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider my-1">
                                                </li>
                                                <li>
                                                    <form
                                                        action="{{ route('mobile-ops.visa-inquiries.status', $item->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit"
                                                            class="dropdown-item fs-13 d-flex align-items-center text-danger {{ $item->status === 'cancelled' ? 'active' : '' }}">
                                                            <i class="mdi mdi-close-circle-outline me-2 text-danger"></i>
                                                            Mark Cancelled
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Delete Inquiry Form -->
                                        {{-- <form action="{{ route('mobile-ops.visa-inquiries.destroy', $item->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this visa inquiry?');"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="Delete Inquiry">
                                                <i class="mdi mdi-delete-outline"></i>
                                            </button>
                                        </form> --}}
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-passport fs-36 d-block mb-2 text-secondary"></i>
                                    <h6 class="text-muted fw-semibold mb-1">No Visa Inquiries Found</h6>
                                    <p class="fs-12 mb-0">There are no guest visa inquiries matching this filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($inquiries->hasPages())
                <div class="p-3 border-top">
                    {{ $inquiries->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modals rendered OUTSIDE the table to prevent layout distortion -->
    @foreach ($inquiries as $item)
        @php
            $docs = is_array($item->documents)
                ? $item->documents
                : (is_string($item->documents)
                    ? json_decode($item->documents, true)
                    : []);
            if (!is_array($docs)) {
                $docs = [];
            }
        @endphp
        <div class="modal fade" id="inquiryModal{{ $item->id }}" tabindex="-1"
            aria-labelledby="modalLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light py-3">
                        <div>
                            <h5 class="modal-title fs-16 fw-bold mb-0" id="modalLabel{{ $item->id }}">
                                Visa Inquiry Details #{{ $item->id }}
                            </h5>
                            <span class="fs-12 text-muted">Submitted on
                                {{ $item->created_at ? $item->created_at->format('d M Y, h:i A') : '—' }}
                            </span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        <!-- Basic Information Grid (identical to Package Inquiries layout) -->
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="p-3 rounded bg-light">
                                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">
                                        Applicant Name
                                    </div>
                                    <div class="fw-bold text-dark fs-14">{{ $item->full_name }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded bg-light">
                                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">
                                        Destination & Visa Type
                                    </div>
                                    <div class="fw-bold text-dark fs-14">
                                        {{ $item->country->country_name ?? '—' }}
                                        ({{ $item->visaType->visa_name ?? 'General / Any' }})
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded bg-light">
                                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">
                                        Phone / WhatsApp
                                    </div>
                                    <div class="fw-semibold text-dark fs-14">
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->phone_number ?? $item->contact) }}"
                                            target="_blank" class="text-success text-decoration-none">
                                            <i class="mdi mdi-whatsapp me-1"></i>
                                            {{ $item->phone_number ?? $item->contact }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded bg-light">
                                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">
                                        Email Address
                                    </div>
                                    <div class="fw-semibold text-dark fs-14">
                                        @if ($item->email)
                                            <a href="mailto:{{ $item->email }}"
                                                class="text-primary text-decoration-none">
                                                <i class="mdi mdi-email-outline me-1"></i> {{ $item->email }}
                                            </a>
                                        @else
                                            <span class="text-muted">Not Provided</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded bg-light">
                                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">
                                        CNIC / Identification
                                    </div>
                                    <div class="fw-semibold text-dark fs-14">
                                        {{ $item->cnic ?? ($item->passport_number ?? 'Not Provided') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded bg-light">
                                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">
                                        Submission Date
                                    </div>
                                    <div class="fw-semibold text-dark fs-14">
                                        {{ $item->created_at ? $item->created_at->format('d M Y, h:i A') : '—' }}
                                        <small
                                            class="text-muted fw-normal">({{ $item->created_at ? $item->created_at->diffForHumans() : '' }})</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Documents View Section -->
                        <div class="mb-2">
                            <h6
                                class="fs-13 fw-bold text-uppercase text-muted mb-3 d-flex align-items-center justify-content-between">
                                <span>
                                    <i class="mdi mdi-file-document-multiple-outline me-1 text-primary fs-16"></i>
                                    Attached Documents
                                </span>
                                <span class="badge bg-light text-muted border">{{ count($docs) }} Document(s)</span>
                            </h6>

                            @if (!empty($docs) && count($docs) > 0)
                                <div class="row g-3">
                                    @foreach ($docs as $doc)
                                        @php
                                            $docPath = $doc['file_path'] ?? '';
                                            $docTitle = $doc['title'] ?? 'Document';
                                            $ext = strtolower(pathinfo($docPath, PATHINFO_EXTENSION));
                                            $isPdf = $ext === 'pdf';
                                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                            $docUrl = !empty($docPath) ? asset('storage/' . $docPath) : '#';
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 bg-light">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="fw-semibold text-dark fs-13 text-truncate"
                                                        title="{{ $docTitle }}">
                                                        <i
                                                            class="mdi {{ $isPdf ? 'mdi-file-pdf-box text-danger' : ($isImage ? 'mdi-file-image-outline text-success' : 'mdi-file-document-outline text-primary') }} me-1 fs-16"></i>
                                                        {{ $docTitle }}
                                                    </span>
                                                    <span
                                                        class="badge bg-white border text-uppercase fs-10 text-muted">{{ $ext ?: 'file' }}</span>
                                                </div>

                                                @if ($isImage && !empty($docPath))
                                                    <div class="mb-3 text-center bg-white rounded p-2 border">
                                                        <a href="{{ $docUrl }}" target="_blank">
                                                            <img src="{{ $docUrl }}" alt="{{ $docTitle }}"
                                                                class="img-fluid rounded"
                                                                style="max-height: 140px; object-fit: contain;">
                                                        </a>
                                                    </div>
                                                @endif

                                                <div class="d-flex gap-2">
                                                    <a href="{{ $docUrl }}" target="_blank"
                                                        class="btn btn-sm btn-outline-info flex-fill">
                                                        <i class="mdi mdi-eye me-1"></i> View Full
                                                    </a>
                                                    <a href="{{ $docUrl }}" download
                                                        class="btn btn-sm btn-light border flex-fill">
                                                        <i class="mdi mdi-download me-1"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 bg-light rounded text-muted fs-12">
                                    <i class="mdi mdi-file-hidden fs-24 d-block mb-1 text-secondary"></i>
                                    No documents attached with this inquiry.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer bg-light py-2">
                        <div class="me-auto d-flex align-items-center gap-2">
                            <span class="fs-12 fw-semibold text-muted">Current Status:</span>
                            @if ($item->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($item->status === 'contacted')
                                <span class="badge bg-info text-white">Contacted</span>
                            @elseif($item->status === 'in_progress')
                                <span class="badge bg-primary text-white">In Progress</span>
                            @elseif($item->status === 'completed')
                                <span class="badge bg-success text-white">Completed</span>
                            @elseif($item->status === 'cancelled')
                                <span class="badge bg-danger text-white">Cancelled</span>
                            @else
                                <span class="badge bg-secondary text-white">{{ ucfirst($item->status) }}</span>
                            @endif
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

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

        .bg-danger-subtle {
            background-color: rgba(220, 53, 69, 0.1) !important;
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
