@extends('layout.mobile-master')

@section('content')
    <!-- Dashboard Header with Greeting & Quick Action -->
    <div class="py-3 d-flex align-items-sm-center justify-content-between flex-column flex-sm-row gap-2">
        <div>
            <h4 class="fs-18 fw-semibold text-dark m-0 d-flex align-items-center gap-2">
                <span>Mobile Operations Overview</span>
                <span class="badge bg-success-subtle text-success border border-success-subtle fs-11 px-2 py-0 fw-medium">Live</span>
            </h4>
            <p class="text-muted fs-13 mb-0">Monitor pilgrim inquiries, Islamic content feeds, and app resource health.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('mobile-ops.inquiries.index') }}" class="btn btn-sm btn-white border shadow-sm px-3 d-flex align-items-center gap-1">
                <i class="mdi mdi-email-fast-outline text-primary"></i>
                <span>View Inquiries</span>
                @if($stats['inquiries_pending'] > 0)
                    <span class="badge bg-danger rounded-pill ms-1">{{ $stats['inquiries_pending'] }}</span>
                @endif
            </a>
            <a href="{{ route('mobile-ops.gallery.index') }}" class="btn btn-sm btn-primary px-3 shadow-sm d-flex align-items-center gap-1">
                <i class="mdi mdi-plus fs-15"></i>
                <span>Manage Ziarat</span>
            </a>
        </div>
    </div>

    <!-- Top Key Metrics Grid -->
    <div class="row g-3 mb-4">
        <!-- Pending Inquiries Card -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fs-12 fw-semibold text-uppercase">Pending Inquiries</span>
                        <div class="avatar-xs rounded bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="mdi mdi-clock-alert-outline fs-18"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <h3 class="fw-bold mb-0 text-dark fs-24">{{ $stats['inquiries_pending'] }}</h3>
                        <span class="fs-12 text-muted">awaiting reply</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light fs-12">
                        <span class="text-muted">Total: {{ $stats['inquiries_total'] }}</span>
                        <a href="{{ route('mobile-ops.inquiries.index', ['status' => 'pending']) }}" class="text-primary text-decoration-none fw-semibold">
                            Review &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ziarat Places Card -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fs-12 fw-semibold text-uppercase">Ziarat Places</span>
                        <div class="avatar-xs rounded bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="mdi mdi-image-multiple-outline fs-18"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <h3 class="fw-bold mb-0 text-dark fs-24">{{ $stats['ziarat_total'] }}</h3>
                        <span class="fs-12 text-muted">locations</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light fs-12">
                        <span class="text-muted">Makkah & Madina</span>
                        <a href="{{ route('mobile-ops.gallery.index') }}" class="text-primary text-decoration-none fw-semibold">
                            View All &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hajj Books Card -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fs-12 fw-semibold text-uppercase">Hajj Books</span>
                        <div class="avatar-xs rounded bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="mdi mdi-book-open-page-variant-outline fs-18"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <h3 class="fw-bold mb-0 text-dark fs-24">{{ $stats['books_total'] }}</h3>
                        <span class="fs-12 text-muted">guides available</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light fs-12">
                        <span class="text-muted">Digital library</span>
                        <a href="{{ route('mobile-ops.books.index') }}" class="text-primary text-decoration-none fw-semibold">
                            Library &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guidelines & Videos Card -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fs-12 fw-semibold text-uppercase">App Guidelines & Media</span>
                        <div class="avatar-xs rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="mdi mdi-play-circle-outline fs-18"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <h3 class="fw-bold mb-0 text-dark fs-24">{{ $stats['guidelines_total'] + $stats['videos_total'] }}</h3>
                        <span class="fs-12 text-muted">resources</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light fs-12">
                        <span class="text-muted">{{ $stats['guidelines_total'] }} articles &bull; {{ $stats['videos_total'] }} videos</span>
                        <a href="{{ route('mobile-ops.guidelines.index') }}" class="text-primary text-decoration-none fw-semibold">
                            Explore &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area: Recent Inquiries + Quick Operational Launchpad -->
    <div class="row g-3">
        <!-- Recent Inquiries Section -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fs-15 fw-bold text-dark m-0">Recent Guest Package Inquiries</h5>
                        <p class="text-muted fs-12 mb-0">Latest package inquiries submitted through the guest mobile application.</p>
                    </div>
                    <a href="{{ route('mobile-ops.inquiries.index') }}" class="btn btn-sm btn-light border px-2 py-1 fs-12">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 fs-12">Guest</th>
                                    <th class="fs-12">Target Package</th>
                                    <th class="fs-12">Pax</th>
                                    <th class="fs-12">Status</th>
                                    <th class="fs-12">Submitted</th>
                                    <th class="text-end pe-3 fs-12">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentInquiries as $inq)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-semibold text-dark fs-13">{{ $inq->full_name }}</div>
                                            <div class="fs-11 text-muted">{{ $inq->contact }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1 fs-11 text-truncate" style="max-width: 160px; display: inline-block;">
                                                {{ $inq->package?->package_name ?? 'Custom Package' }}
                                            </span>
                                        </td>
                                        <td class="fs-12 fw-semibold">
                                            {{ $inq->pax }} <span class="text-muted fw-normal">({{ ucfirst($inq->room_type) }})</span>
                                        </td>
                                        <td>
                                            @if ($inq->status === 'pending')
                                                <span class="badge bg-warning text-dark px-2 py-1 fs-11">Pending</span>
                                            @elseif($inq->status === 'contacted')
                                                <span class="badge bg-info text-white px-2 py-1 fs-11">Contacted</span>
                                            @else
                                                <span class="badge bg-success text-white px-2 py-1 fs-11">Closed</span>
                                            @endif
                                        </td>
                                        <td class="fs-12 text-muted">
                                            {{ $inq->created_at->diffForHumans() }}
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('mobile-ops.inquiries.index') }}" class="btn btn-xs btn-outline-primary py-1 px-2 fs-11">
                                                <i class="mdi mdi-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted fs-13">
                                            <i class="mdi mdi-inbox-outline fs-24 d-block mb-1"></i>
                                            No inquiries recorded yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Content Hub & Operational Health -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-3 h-100">
                <!-- Inquiry Pipeline Progress Card -->
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body p-3">
                        <h6 class="fs-13 fw-bold text-dark text-uppercase mb-3">Inquiry Pipeline Status</h6>
                        @php
                            $totalInq = max(1, $stats['inquiries_total']);
                            $contactedPct = round(($stats['inquiries_contacted'] / $totalInq) * 100);
                            $closedPct = round(($stats['inquiries_closed'] / $totalInq) * 100);
                            $pendingPct = round(($stats['inquiries_pending'] / $totalInq) * 100);
                        @endphp

                        <div class="d-flex justify-content-between fs-12 mb-1">
                            <span class="text-muted">Response Progress</span>
                            <span class="fw-semibold text-dark">{{ $stats['inquiries_contacted'] + $stats['inquiries_closed'] }} of {{ $stats['inquiries_total'] }} addressed</span>
                        </div>

                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $closedPct }}%" title="Closed: {{ $stats['inquiries_closed'] }}"></div>
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $contactedPct }}%" title="Contacted: {{ $stats['inquiries_contacted'] }}"></div>
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $pendingPct }}%" title="Pending: {{ $stats['inquiries_pending'] }}"></div>
                        </div>

                        <div class="row g-2 text-center pt-2 border-top border-light">
                            <div class="col-4">
                                <div class="fs-11 text-muted text-uppercase fw-semibold">Pending</div>
                                <div class="fs-14 fw-bold text-warning">{{ $stats['inquiries_pending'] }}</div>
                            </div>
                            <div class="col-4">
                                <div class="fs-11 text-muted text-uppercase fw-semibold">Contacted</div>
                                <div class="fs-14 fw-bold text-info">{{ $stats['inquiries_contacted'] }}</div>
                            </div>
                            <div class="col-4">
                                <div class="fs-11 text-muted text-uppercase fw-semibold">Closed</div>
                                <div class="fs-14 fw-bold text-success">{{ $stats['inquiries_closed'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Content Management Links -->
                <div class="card border-0 shadow-sm flex-grow-1" style="border-radius: 12px;">
                    <div class="card-body p-3">
                        <h6 class="fs-13 fw-bold text-dark text-uppercase mb-3">App Content Modules</h6>
                        <div class="d-flex flex-column gap-2">
                            <a href="{{ route('mobile-ops.gallery.index') }}" class="d-flex align-items-center justify-content-between p-2 rounded border bg-light-subtle text-decoration-none hover-shadow">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-xs rounded bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="mdi mdi-image-multiple-outline fs-16"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark fs-13">Ziarat Places</div>
                                        <div class="text-muted fs-11">{{ $stats['ziarat_total'] }} sacred locations</div>
                                    </div>
                                </div>
                                <i class="mdi mdi-chevron-right text-muted fs-18"></i>
                            </a>

                            <a href="{{ route('mobile-ops.books.index') }}" class="d-flex align-items-center justify-content-between p-2 rounded border bg-light-subtle text-decoration-none hover-shadow">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-xs rounded bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="mdi mdi-book-open-page-variant-outline fs-16"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark fs-13">Hajj Information Books</div>
                                        <div class="text-muted fs-11">{{ $stats['books_total'] }} published guides</div>
                                    </div>
                                </div>
                                <i class="mdi mdi-chevron-right text-muted fs-18"></i>
                            </a>

                            <a href="{{ route('mobile-ops.guidelines.index') }}" class="d-flex align-items-center justify-content-between p-2 rounded border bg-light-subtle text-decoration-none hover-shadow">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-xs rounded bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="mdi mdi-information-outline fs-16"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark fs-13">Hujjaj Guidelines</div>
                                        <div class="text-muted fs-11">{{ $stats['guidelines_total'] }} published guidelines</div>
                                    </div>
                                </div>
                                <i class="mdi mdi-chevron-right text-muted fs-18"></i>
                            </a>

                            <a href="{{ route('mobile-ops.videos.index') }}" class="d-flex align-items-center justify-content-between p-2 rounded border bg-light-subtle text-decoration-none hover-shadow">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-xs rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="mdi mdi-video-vintage fs-16"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark fs-13">Tutorial Videos</div>
                                        <div class="text-muted fs-11">{{ $stats['videos_total'] }} instructional videos</div>
                                    </div>
                                </div>
                                <i class="mdi mdi-chevron-right text-muted fs-18"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-shadow {
            transition: all 0.15s ease-in-out;
        }
        .hover-shadow:hover {
            background-color: #f8f9fa !important;
            transform: translateX(2px);
        }
        .bg-primary-subtle {
            background-color: rgba(13, 110, 253, 0.1) !important;
        }
        .bg-success-subtle {
            background-color: rgba(25, 135, 84, 0.1) !important;
        }
        .bg-warning-subtle {
            background-color: rgba(255, 193, 7, 0.12) !important;
        }
        .bg-info-subtle {
            background-color: rgba(13, 202, 240, 0.1) !important;
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
