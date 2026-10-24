@extends('layout.mobile-master')

@section('content')
    <!-- Dashboard Header with Greeting & Quick Action -->
    <div class="py-3 d-flex align-items-sm-center justify-content-between flex-column flex-sm-row gap-2">
        <div>
            <h4 class="fs-18 fw-semibold text-dark m-0 d-flex align-items-center gap-2">
                <span>Mobile Operations Overview</span>
                <span
                    class="badge bg-success-subtle text-success border border-success-subtle fs-11 px-2 py-0 fw-medium">Live</span>
            </h4>
            <p class="text-muted fs-13 mb-0">Monitor pilgrim inquiries, visa applications, Islamic content feeds, and app
                resource health.</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('mobile-ops.visa-inquiries.index') }}"
                class="btn btn-sm btn-white border shadow-sm px-3 d-flex align-items-center gap-1">
                <i class="mdi mdi-passport text-primary"></i>
                <span>Visa Inquiries</span>
                @if ($stats['visa_inquiries_pending'] > 0)
                    <span class="badge bg-warning text-dark rounded-pill ms-1">{{ $stats['visa_inquiries_pending'] }}</span>
                @endif
            </a>
            <a href="{{ route('mobile-ops.inquiries.index') }}"
                class="btn btn-sm btn-white border shadow-sm px-3 d-flex align-items-center gap-1">
                <i class="mdi mdi-email-fast-outline text-primary"></i>
                <span>Package Inquiries</span>
                @if ($stats['inquiries_pending'] > 0)
                    <span class="badge bg-danger rounded-pill ms-1">{{ $stats['inquiries_pending'] }}</span>
                @endif
            </a>
            <a href="{{ route('mobile-ops.gallery.index') }}"
                class="btn btn-sm btn-primary px-3 shadow-sm d-flex align-items-center gap-1">
                <i class="mdi mdi-plus fs-15"></i>
                <span>Manage Ziarat</span>
            </a>
        </div>
    </div>

    <!-- Top Key Metrics Grid -->
    <div class="row g-3 mb-4">
        <!-- Package Inquiries Pending Card -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fs-12 fw-semibold text-uppercase">Package Inquiries</span>
                        <div class="avatar-xs rounded bg-warning-subtle text-warning d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;">
                            <i class="mdi mdi-clock-alert-outline fs-18"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <h3 class="fw-bold mb-0 text-dark fs-24">{{ $stats['inquiries_pending'] }}</h3>
                        <span class="fs-12 text-muted">pending review</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light fs-12">
                        <span class="text-muted">Total: {{ $stats['inquiries_total'] }}</span>
                        <a href="{{ route('mobile-ops.inquiries.index', ['status' => 'pending']) }}"
                            class="text-primary text-decoration-none fw-semibold">
                            Review &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visa Inquiries Pending Card -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fs-12 fw-semibold text-uppercase">Visa Applications</span>
                        <div class="avatar-xs rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;">
                            <i class="mdi mdi-passport fs-18"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <h3 class="fw-bold mb-0 text-dark fs-24">{{ $stats['visa_inquiries_pending'] }}</h3>
                        <span class="fs-12 text-muted">pending action</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light fs-12">
                        <span class="text-muted">Total: {{ $stats['visa_inquiries_total'] }}</span>
                        <a href="{{ route('mobile-ops.visa-inquiries.index', ['status' => 'pending']) }}"
                            class="text-primary text-decoration-none fw-semibold">
                            Manage &rarr;
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
                        <div class="avatar-xs rounded bg-success-subtle text-success d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;">
                            <i class="mdi mdi-image-multiple-outline fs-18"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <h3 class="fw-bold mb-0 text-dark fs-24">{{ $stats['ziarat_total'] }}</h3>
                        <span class="fs-12 text-muted">locations</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light fs-12">
                        <span class="text-muted">Makkah & Madina</span>
                        <a href="{{ route('mobile-ops.gallery.index') }}"
                            class="text-primary text-decoration-none fw-semibold">
                            View All &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hajj Digital Library & Guidelines Card -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fs-12 fw-semibold text-uppercase">Library & Media</span>
                        <div class="avatar-xs rounded bg-info-subtle text-info d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;">
                            <i class="mdi mdi-book-open-page-variant-outline fs-18"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <h3 class="fw-bold mb-0 text-dark fs-24">
                            {{ $stats['books_total'] + $stats['guidelines_total'] + $stats['videos_total'] }}</h3>
                        <span class="fs-12 text-muted">resources</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light fs-12">
                        <span class="text-muted">{{ $stats['books_total'] }} Books &bull; {{ $stats['videos_total'] }}
                            Videos</span>
                        <a href="{{ route('mobile-ops.books.index') }}"
                            class="text-primary text-decoration-none fw-semibold">
                            Explore &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area: Recent Visa & Package Inquiries + Quick Operational Launchpad -->
    <div class="row g-3">
        <!-- Recent Visa Inquiries Section -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; overflow: visible;">
                <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fs-15 fw-bold text-dark m-0 d-flex align-items-center gap-2">
                            <i class="mdi mdi-passport text-primary"></i>
                            <span>Recent Visa Inquiries</span>
                        </h5>
                        <p class="text-muted fs-12 mb-0">Latest guest visa applications submitted through the mobile
                            application.</p>
                    </div>
                    <a href="{{ route('mobile-ops.visa-inquiries.index') }}"
                        class="btn btn-sm btn-light border px-2 py-1 fs-12">
                        View All
                    </a>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 fs-12">Applicant</th>
                                    <th class="fs-12">Destination / Visa</th>
                                    <th class="fs-12">Documents</th>
                                    <th class="fs-12">Status</th>
                                    <th class="fs-12">Submitted</th>
                                    <th class="text-end pe-3 fs-12">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentVisaInquiries as $vInq)
                                    @php
                                        $docs = is_array($vInq->documents)
                                            ? $vInq->documents
                                            : (is_string($vInq->documents)
                                                ? json_decode($vInq->documents, true)
                                                : []);
                                        if (!is_array($docs)) {
                                            $docs = [];
                                        }
                                    @endphp
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-semibold text-dark fs-13">{{ $vInq->full_name }}</div>
                                            <div class="fs-11 text-muted">{{ $vInq->phone_number ?? $vInq->contact }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-primary border px-2 py-1 fs-11">
                                                <i
                                                    class="mdi mdi-earth me-1"></i>{{ $vInq->country->country_name ?? '—' }}
                                            </span>
                                            <div class="fs-11 text-muted mt-1">
                                                {{ $vInq->visaType->visa_name ?? 'General' }}</div>
                                        </td>
                                        <td>
                                            @if (!empty($docs) && count($docs) > 0)
                                                <span class="badge bg-light text-dark border px-2 py-1 fs-11">
                                                    <i
                                                        class="mdi mdi-file-document-outline me-1 text-primary"></i>{{ count($docs) }}
                                                    Doc(s)
                                                </span>
                                            @else
                                                <span class="text-muted fs-11">None</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($vInq->status === 'pending')
                                                <span class="badge bg-warning text-dark px-2 py-1 fs-11">Pending</span>
                                            @elseif($vInq->status === 'contacted')
                                                <span class="badge bg-info text-white px-2 py-1 fs-11">Contacted</span>
                                            @elseif($vInq->status === 'in_progress')
                                                <span class="badge bg-primary text-white px-2 py-1 fs-11">In
                                                    Progress</span>
                                            @elseif($vInq->status === 'completed')
                                                <span class="badge bg-success text-white px-2 py-1 fs-11">Completed</span>
                                            @else
                                                <span class="badge bg-danger text-white px-2 py-1 fs-11">Cancelled</span>
                                            @endif
                                        </td>
                                        <td class="fs-12 text-muted">
                                            {{ $vInq->created_at ? $vInq->created_at->diffForHumans() : '—' }}
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('mobile-ops.visa-inquiries.index') }}"
                                                class="btn btn-xs btn-outline-primary py-1 px-2 fs-11">
                                                <i class="mdi mdi-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted fs-13">
                                            <i class="mdi mdi-passport fs-24 d-block mb-1 text-secondary"></i>
                                            No visa inquiries recorded yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Package Inquiries Section -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: visible;">
                <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fs-15 fw-bold text-dark m-0 d-flex align-items-center gap-2">
                            <i class="mdi mdi-email-fast-outline text-primary"></i>
                            <span>Recent Package Inquiries</span>
                        </h5>
                        <p class="text-muted fs-12 mb-0">Latest package inquiries submitted through the guest mobile
                            application.</p>
                    </div>
                    <a href="{{ route('mobile-ops.inquiries.index') }}"
                        class="btn btn-sm btn-light border px-2 py-1 fs-12">
                        View All
                    </a>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
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
                                            <span class="badge bg-light text-dark border px-2 py-1 fs-11 text-truncate"
                                                style="max-width: 160px; display: inline-block;">
                                                {{ $inq->package?->package_name ?? ($inq->package?->package_title ?? 'Custom Package') }}
                                            </span>
                                        </td>
                                        <td class="fs-12 fw-semibold">
                                            {{ $inq->pax }} <span
                                                class="text-muted fw-normal">({{ ucfirst($inq->room_type) }})</span>
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
                                            <a href="{{ route('mobile-ops.inquiries.index') }}"
                                                class="btn btn-xs btn-outline-primary py-1 px-2 fs-11">
                                                <i class="mdi mdi-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted fs-13">
                                            <i class="mdi mdi-inbox-outline fs-24 d-block mb-1"></i>
                                            No package inquiries recorded yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Pipelines & Quick Operational Modules -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-3 h-100">
                <!-- Visa Applications Status Pipeline Card -->
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body p-3">
                        <h6
                            class="fs-13 fw-bold text-dark text-uppercase mb-3 d-flex align-items-center justify-content-between">
                            <span>Visa Applications Pipeline</span>
                            <span class="badge bg-light text-muted border">{{ $stats['visa_inquiries_total'] }}
                                Total</span>
                        </h6>
                        @php
                            $totalVisa = max(1, $stats['visa_inquiries_total']);
                            $vCompletedPct = round(($stats['visa_inquiries_completed'] / $totalVisa) * 100);
                            $vProgressPct = round(($stats['visa_inquiries_progress'] / $totalVisa) * 100);
                            $vContactedPct = round(($stats['visa_inquiries_contacted'] / $totalVisa) * 100);
                            $vPendingPct = round(($stats['visa_inquiries_pending'] / $totalVisa) * 100);
                        @endphp

                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $vCompletedPct }}%"
                                title="Completed: {{ $stats['visa_inquiries_completed'] }}"></div>
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $vProgressPct }}%"
                                title="In Progress: {{ $stats['visa_inquiries_progress'] }}"></div>
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $vContactedPct }}%"
                                title="Contacted: {{ $stats['visa_inquiries_contacted'] }}"></div>
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $vPendingPct }}%"
                                title="Pending: {{ $stats['visa_inquiries_pending'] }}"></div>
                        </div>

                        <div class="row g-2 text-center pt-2 border-top border-light">
                            <div class="col-3">
                                <div class="fs-11 text-muted text-uppercase fw-semibold">Pending</div>
                                <div class="fs-14 fw-bold text-warning">{{ $stats['visa_inquiries_pending'] }}</div>
                            </div>
                            <div class="col-3">
                                <div class="fs-11 text-muted text-uppercase fw-semibold">Contacted</div>
                                <div class="fs-14 fw-bold text-info">{{ $stats['visa_inquiries_contacted'] }}</div>
                            </div>
                            <div class="col-3">
                                <div class="fs-11 text-muted text-uppercase fw-semibold">Active</div>
                                <div class="fs-14 fw-bold text-primary">{{ $stats['visa_inquiries_progress'] }}</div>
                            </div>
                            <div class="col-3">
                                <div class="fs-11 text-muted text-uppercase fw-semibold">Done</div>
                                <div class="fs-14 fw-bold text-success">{{ $stats['visa_inquiries_completed'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Package Inquiry Pipeline Card -->
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body p-3">
                        <h6
                            class="fs-13 fw-bold text-dark text-uppercase mb-3 d-flex align-items-center justify-content-between">
                            <span>Package Inquiries Pipeline</span>
                            <span class="badge bg-light text-muted border">{{ $stats['inquiries_total'] }} Total</span>
                        </h6>
                        @php
                            $totalInq = max(1, $stats['inquiries_total']);
                            $contactedPct = round(($stats['inquiries_contacted'] / $totalInq) * 100);
                            $closedPct = round(($stats['inquiries_closed'] / $totalInq) * 100);
                            $pendingPct = round(($stats['inquiries_pending'] / $totalInq) * 100);
                        @endphp

                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $closedPct }}%"
                                title="Closed: {{ $stats['inquiries_closed'] }}"></div>
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $contactedPct }}%"
                                title="Contacted: {{ $stats['inquiries_contacted'] }}"></div>
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $pendingPct }}%"
                                title="Pending: {{ $stats['inquiries_pending'] }}"></div>
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
                            <a href="{{ route('mobile-ops.visa-countries.index') }}"
                                class="d-flex align-items-center justify-content-between p-2 rounded border bg-light-subtle text-decoration-none hover-shadow">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-xs rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                                        style="width: 32px; height: 32px;">
                                        <i class="mdi mdi-earth fs-16"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark fs-13">Visa Countries & Types</div>
                                        <div class="text-muted fs-11">Manage countries & requirements</div>
                                    </div>
                                </div>
                                <i class="mdi mdi-chevron-right text-muted fs-18"></i>
                            </a>

                            <a href="{{ route('mobile-ops.gallery.index') }}"
                                class="d-flex align-items-center justify-content-between p-2 rounded border bg-light-subtle text-decoration-none hover-shadow">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-xs rounded bg-success-subtle text-success d-flex align-items-center justify-content-center"
                                        style="width: 32px; height: 32px;">
                                        <i class="mdi mdi-image-multiple-outline fs-16"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark fs-13">Ziarat Places</div>
                                        <div class="text-muted fs-11">{{ $stats['ziarat_total'] }} sacred locations</div>
                                    </div>
                                </div>
                                <i class="mdi mdi-chevron-right text-muted fs-18"></i>
                            </a>

                            <a href="{{ route('mobile-ops.books.index') }}"
                                class="d-flex align-items-center justify-content-between p-2 rounded border bg-light-subtle text-decoration-none hover-shadow">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-xs rounded bg-info-subtle text-info d-flex align-items-center justify-content-center"
                                        style="width: 32px; height: 32px;">
                                        <i class="mdi mdi-book-open-page-variant-outline fs-16"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark fs-13">Hajj Information Books</div>
                                        <div class="text-muted fs-11">{{ $stats['books_total'] }} published guides</div>
                                    </div>
                                </div>
                                <i class="mdi mdi-chevron-right text-muted fs-18"></i>
                            </a>

                            <a href="{{ route('mobile-ops.guidelines.index') }}"
                                class="d-flex align-items-center justify-content-between p-2 rounded border bg-light-subtle text-decoration-none hover-shadow">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-xs rounded bg-warning-subtle text-warning d-flex align-items-center justify-content-center"
                                        style="width: 32px; height: 32px;">
                                        <i class="mdi mdi-information-outline fs-16"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark fs-13">Hujjaj Guidelines</div>
                                        <div class="text-muted fs-11">{{ $stats['guidelines_total'] }} published
                                            guidelines</div>
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

        .btn-xs {
            padding: 0.15rem 0.45rem;
            font-size: 0.75rem;
            border-radius: 4px;
        }
    </style>
@endsection
