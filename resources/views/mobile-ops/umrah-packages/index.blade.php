@extends('layout.mobile-master')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
            <i class="mdi mdi-alert-circle-outline me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="pt-3 pb-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="fs-18 fw-bold text-dark m-0">Umrah Packages</h4>
            <p class="text-muted fs-13 mb-0">Manage mobile app Umrah packages and pricing.</p>
        </div>
        <div class="d-flex gap-2">
            @php
                $trashTotal = \App\Models\Package::onlyTrashed()->where('package_type', 'umrah')->count();
            @endphp
            <a href="{{ route('mobile-ops.umrah-packages.trash') }}" class="btn btn-outline-danger btn-sm px-3">
                <i class="mdi mdi-trash-can-outline me-1"></i> View Trash
                @if ($trashTotal > 0)
                    <span class="badge bg-danger text-white ms-1">{{ $trashTotal }}</span>
                @endif
            </a>
            <a href="{{ route('mobile-ops.umrah-packages.create') }}" class="btn btn-success btn-sm px-3"
                style="background-color: #1e7456; border-color: #1e7456;">
                <i class="mdi mdi-plus-circle me-1"></i> Add Umrah Package
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card border-0 shadow-sm mt-3" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('mobile-ops.umrah-packages.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i
                                class="mdi mdi-magnify text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0"
                            placeholder="Search by package title, code..." value="{{ $search ?? '' }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
                    @if (!empty($search))
                        <a href="{{ route('mobile-ops.umrah-packages.index') }}"
                            class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
                    @endif
                </div>
                <div class="col-md-4 text-md-end text-muted fs-13">
                    Showing <strong>{{ $packages->total() }}</strong> Umrah Packages
                </div>
            </form>
        </div>
    </div>

    <!-- Simplified Packages Table -->
    <div class="card border-0 shadow-sm mt-3" style="border-radius: 12px;">
        <div class="card-body">
            <div class="table-responsive">
                <table id="umrah-packages-datatable" class="table table-bordered dt-responsive nowrap align-middle mb-0 w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 50px;">#</th>
                            <th>Package</th>
                            <th style="width: 140px;">Code</th>
                            <th style="width: 180px;">Validity Dates</th>
                            <th style="width: 150px;">Starting Price</th>
                            <th style="width: 110px;">Status</th>
                            <th class="text-end pe-3" style="width: 130px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $pkg)
                            @php
                                $makkah = $pkg->accommodations->firstWhere('place', 'makkah');
                                $madinah = $pkg->accommodations->firstWhere('place', 'madinah');
                                $minPrice =
                                    $pkg->price_sharing > 0
                                        ? $pkg->price_sharing
                                        : ($pkg->price_quad > 0
                                            ? $pkg->price_quad
                                            : ($pkg->price_triple > 0
                                                ? $pkg->price_triple
                                                : ($pkg->price_double > 0
                                                    ? $pkg->price_double
                                                    : 0)));
                            @endphp
                            <tr>
                                <td class="ps-3 text-muted fs-12">
                                    {{ $packages->firstItem() + $loop->index }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($pkg->package_image_url)
                                            <img src="{{ $pkg->package_image_url }}"
                                                class="rounded shadow-sm flex-shrink-0"
                                                style="width: 44px; height: 34px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted border flex-shrink-0"
                                                style="width: 44px; height: 34px; font-size: 13px;">
                                                <i class="mdi mdi-kaaba"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <a href="javascript:void(0);" data-bs-toggle="modal"
                                                data-bs-target="#packageDetailsModal{{ $pkg->id }}"
                                                class="fw-bold text-dark text-decoration-none hover-primary">
                                                {{ $pkg->package_title ?? $pkg->name }}
                                            </a>
                                            @if ($pkg->subtitle)
                                                <div class="fs-11 text-muted text-truncate" style="max-width: 280px;">
                                                    {{ Str::limit($pkg->subtitle, 45) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($pkg->package_code ?? $pkg->code)
                                        {{ $pkg->package_code ?? $pkg->code }}
                                    @else
                                        <span class="text-muted fs-12">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($pkg->valid_from && $pkg->valid_until)
                                        <div class="fs-12 text-dark fw-medium">
                                            <i class="mdi mdi-calendar-range text-muted me-1"></i>
                                            {{ \Carbon\Carbon::parse($pkg->valid_from)->format('d M') }} -
                                            {{ \Carbon\Carbon::parse($pkg->valid_until)->format('d M, Y') }}
                                        </div>
                                    @elseif($pkg->valid_from)
                                        <div class="fs-12 text-dark">
                                            <i class="mdi mdi-calendar-start text-muted me-1"></i>
                                            From {{ \Carbon\Carbon::parse($pkg->valid_from)->format('d M, Y') }}
                                        </div>
                                    @elseif($pkg->valid_until)
                                        <div class="fs-12 text-dark">
                                            <i class="mdi mdi-calendar-end text-muted me-1"></i>
                                            Till {{ \Carbon\Carbon::parse($pkg->valid_until)->format('d M, Y') }}
                                        </div>
                                    @else
                                        <span class="text-muted fs-12">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($minPrice > 0)
                                        <div class="fw-bold text-success fs-14">PKR {{ number_format($minPrice, 0) }}</div>
                                        <span class="text-muted fs-11">Starting from</span>
                                    @else
                                        <span class="badge bg-light text-muted border">Call for Price</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($pkg->status === 'active')
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                                    @elseif($pkg->status === 'draft')
                                        <span
                                            class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Draft</span>
                                    @else
                                        <span
                                            class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    @if ($tab === 'trash')
                                        <form action="{{ route('mobile-ops.umrah-packages.restore', $pkg->id) }}"
                                            method="POST" onsubmit="return confirm('Restore this package?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success btn-sm px-3"
                                                title="Restore Package">
                                                <i class="mdi mdi-restore me-1"></i> Restore
                                            </button>
                                        </form>
                                    @else
                                        <div class="d-flex justify-content-end gap-1">
                                            <!-- Details Action Icon -->
                                            <button type="button" class="btn btn-outline-info btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#packageDetailsModal{{ $pkg->id }}"
                                                title="View Package Details">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </button>
                                            <!-- Edit Action Icon -->
                                            <a href="{{ route('mobile-ops.umrah-packages.edit', $pkg->id) }}"
                                                class="btn btn-outline-primary btn-sm" title="Edit">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <!-- Trash Action Icon -->
                                            <form action="{{ route('mobile-ops.umrah-packages.destroy', $pkg->id) }}"
                                                method="POST" onsubmit="return confirm('Move package to trash?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    title="Move to Trash">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Package Details Modals (Rendered outside table) -->
    @foreach ($packages as $pkg)
        @php
            $makkah = $pkg->accommodations->firstWhere('place', 'makkah');
            $madinah = $pkg->accommodations->firstWhere('place', 'madinah');
            $otherStays = $pkg->accommodations->whereNotIn('place', ['makkah', 'madinah']);
            $outbound = $pkg->transportFlights->firstWhere('is_preferred', 1) ?? $pkg->transportFlights->first();
            $inbound = $pkg->transportFlights->where('id', '!=', optional($outbound)->id)->first();
            $terms = $pkg->terms;
        @endphp
        <div class="modal fade" id="packageDetailsModal{{ $pkg->id }}" tabindex="-1"
            aria-labelledby="packageDetailsLabel{{ $pkg->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light py-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success p-2 rounded-circle">
                                <i class="mdi mdi-kaaba fs-18"></i>
                            </span>
                            <div>
                                <h5 class="modal-title fs-16 fw-bold mb-0 text-dark"
                                    id="packageDetailsLabel{{ $pkg->id }}">
                                    {{ $pkg->package_title ?? $pkg->name }}
                                </h5>
                                <div class="d-flex flex-wrap align-items-center gap-1 mt-1">
                                    @if ($pkg->package_code ?? $pkg->code)
                                        <span class="badge bg-dark text-white px-2 py-1 font-monospace fs-11">Code: {{ $pkg->package_code ?? $pkg->code }}</span>
                                    @endif
                                    <span class="badge {{ $pkg->status === 'active' ? 'bg-success text-white' : 'bg-secondary text-white' }} px-2 py-1 fs-11">
                                        {{ ucfirst($pkg->status) }}
                                    </span>
                                    <span class="badge {{ $pkg->visa_status === 'included' ? 'bg-info text-white' : 'bg-warning text-dark' }} px-2 py-1 fs-11">
                                        Visa {{ ucfirst($pkg->visa_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 fs-13">
                        @if ($pkg->package_image_url)
                            <div class="mb-3 text-center">
                                <img src="{{ $pkg->package_image_url }}" class="img-fluid rounded shadow-sm"
                                    style="max-height: 220px; width: 100%; object-fit: cover;">
                            </div>
                        @endif

                        @if ($pkg->subtitle)
                            <div class="alert alert-light border py-2 px-3 mb-3 text-secondary fst-italic">
                                {{ $pkg->subtitle }}
                            </div>
                        @endif

                        <!-- Standard Room Rates -->
                        <div class="card border shadow-none mb-3">
                            <div class="card-header bg-light py-2">
                                <h6 class="fs-13 fw-bold text-dark m-0"><i
                                        class="mdi mdi-currency-usd text-success me-1"></i> Standard Room Rates (Per
                                    Person)</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-2 text-center">
                                    <div class="col-6 col-md-3">
                                        <div class="p-2 border rounded bg-light-subtle">
                                            <div class="text-muted fs-11">Sharing</div>
                                            <div class="fw-bold text-dark fs-14">PKR
                                                {{ number_format($pkg->price_sharing ?? 0, 0) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="p-2 border rounded bg-light-subtle">
                                            <div class="text-muted fs-11">Quad</div>
                                            <div class="fw-bold text-dark fs-14">PKR
                                                {{ number_format($pkg->price_quad ?? 0, 0) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="p-2 border rounded bg-light-subtle">
                                            <div class="text-muted fs-11">Triple</div>
                                            <div class="fw-bold text-dark fs-14">PKR
                                                {{ number_format($pkg->price_triple ?? 0, 0) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="p-2 border rounded bg-light-subtle">
                                            <div class="text-muted fs-11">Double</div>
                                            <div class="fw-bold text-dark fs-14">PKR
                                                {{ number_format($pkg->price_double ?? 0, 0) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hotel Details -->
                        <div class="card border shadow-none mb-3">
                            <div class="card-header bg-light py-2">
                                <h6 class="fs-13 fw-bold text-dark m-0"><i
                                        class="mdi mdi-office-building text-primary me-1"></i> Hotel Accommodations</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-md-6 border-end">
                                        <div class="fw-bold text-primary mb-1"><i class="mdi mdi-mosque me-1"></i> Makkah
                                            Hotel</div>
                                        @if ($makkah)
                                            <div><strong>Hotel:</strong> {{ $makkah->hotel ?? 'N/A' }}</div>
                                            <div><strong>Distance:</strong>
                                                {{ $makkah->distance ? $makkah->distance . 'm' : $makkah->note ?? 'N/A' }}
                                            </div>
                                            <div><strong>Stay / Meal:</strong>
                                                {{ $makkah->days ? $makkah->days . ' Days' : '—' }} |
                                                {{ $makkah->food_package ?? 'N/A' }}</div>
                                            <div><strong>Category:</strong> {{ $makkah->accommodation_type ?? 'Standard' }}
                                            </div>
                                        @else
                                            <div class="text-muted">Not specified</div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <div class="fw-bold text-success mb-1"><i class="mdi mdi-mosque me-1"></i> Madinah
                                            Hotel</div>
                                        @if ($madinah)
                                            <div><strong>Hotel:</strong> {{ $madinah->hotel ?? 'N/A' }}</div>
                                            <div><strong>Distance:</strong>
                                                {{ $madinah->distance ? $madinah->distance . 'm' : $madinah->note ?? 'N/A' }}
                                            </div>
                                            <div><strong>Stay / Meal:</strong>
                                                {{ $madinah->days ? $madinah->days . ' Days' : '—' }} |
                                                {{ $madinah->food_package ?? 'N/A' }}</div>
                                            <div><strong>Category:</strong>
                                                {{ $madinah->accommodation_type ?? 'Standard' }}</div>
                                        @else
                                            <div class="text-muted">Not specified</div>
                                        @endif
                                    </div>
                                </div>

                                @if (!empty($pkg->additional_destinations) && count($pkg->additional_destinations) > 0)
                                    <div class="mt-3 pt-3 border-top">
                                        <div class="fw-bold text-purple mb-2"><i class="mdi mdi-earth me-1"></i>
                                            Additional Multi-City Stays</div>
                                        <div class="row g-2">
                                            @foreach ($pkg->additional_destinations as $dest)
                                                <div class="col-md-6">
                                                    <div class="p-2 border rounded bg-light-subtle">
                                                        <div class="fw-bold text-dark">
                                                            {{ $dest['country'] ?? 'Destination' }}</div>
                                                        <div class="fs-12 text-muted"><strong>Hotel:</strong>
                                                            {{ $dest['hotel_name'] ?? '—' }}</div>
                                                        <div class="fs-12 text-muted"><strong>Period:</strong>
                                                            {{ $dest['hotel_period'] ?? '—' }} | <strong>Meal:</strong>
                                                            {{ $dest['hotel_meal_plan'] ?? '—' }}</div>
                                                        @if (!empty($dest['hotel_category']))
                                                            <div class="fs-12 text-muted"><strong>Category:</strong>
                                                                {{ $dest['hotel_category'] }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Flight Details -->
                        @if ($outbound || $inbound)
                            <div class="card border shadow-none mb-3">
                                <div class="card-header bg-light py-2">
                                    <h6 class="fs-13 fw-bold text-dark m-0"><i
                                            class="mdi mdi-airplane text-info me-1"></i> Flight Details</h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        @if ($outbound)
                                            <div class="col-md-6">
                                                <div class="fw-bold text-dark"><i
                                                        class="mdi mdi-airplane-takeoff text-primary me-1"></i> Outbound
                                                </div>
                                                <div class="text-muted">{{ $outbound->airline }}
                                                    ({{ $outbound->flight_class ?? 'Direct' }})</div>
                                                <div><strong>Route:</strong> {{ $outbound->origin }} &rarr;
                                                    {{ $outbound->destination }}</div>
                                                @if ($outbound->flight_no)
                                                    <div class="fs-12 text-muted"><strong>Baggage:</strong>
                                                        {{ $outbound->flight_no }}</div>
                                                @endif
                                            </div>
                                        @endif
                                        @if ($inbound)
                                            <div class="col-md-6">
                                                <div class="fw-bold text-dark"><i
                                                        class="mdi mdi-airplane-landing text-success me-1"></i> Inbound
                                                </div>
                                                <div class="text-muted">{{ $inbound->airline }}
                                                    ({{ $inbound->flight_class ?? 'Direct' }})</div>
                                                <div><strong>Route:</strong> {{ $inbound->origin }} &rarr;
                                                    {{ $inbound->destination }}</div>
                                                @if ($inbound->flight_no)
                                                    <div class="fs-12 text-muted"><strong>Baggage:</strong>
                                                        {{ $inbound->flight_no }}</div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Multiple Duration Matrix (if exists) -->
                        @if (!empty($pkg->duration_pricing_options) && count($pkg->duration_pricing_options) > 0)
                            <div class="card border shadow-none mb-3">
                                <div class="card-header bg-light py-2">
                                    <h6 class="fs-13 fw-bold text-dark m-0"><i
                                            class="mdi mdi-table-clock text-secondary me-1"></i> Multiple Duration Pricing
                                        Matrix</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered align-middle mb-0 text-center fs-12">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Duration</th>
                                                    <th>Sharing (PKR)</th>
                                                    <th>Quad (PKR)</th>
                                                    <th>Triple (PKR)</th>
                                                    <th>Double (PKR)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pkg->duration_pricing_options as $matrix)
                                                    <tr>
                                                        <td class="fw-bold text-dark">{{ $matrix['duration'] ?? '—' }}
                                                        </td>
                                                        <td>{{ number_format($matrix['sharing'] ?? 0, 0) }}</td>
                                                        <td>{{ number_format($matrix['quad'] ?? 0, 0) }}</td>
                                                        <td>{{ number_format($matrix['triple'] ?? 0, 0) }}</td>
                                                        <td>{{ number_format($matrix['double'] ?? 0, 0) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Inclusions & Exclusions -->
                        @if ($pkg->inclusions || $pkg->exclusions || $pkg->key_features)
                            <div class="card border shadow-none mb-3">
                                <div class="card-header bg-light py-2">
                                    <h6 class="fs-13 fw-bold text-dark m-0"><i
                                            class="mdi mdi-text-box-outline text-muted me-1"></i> Package Highlights &
                                        Inclusions</h6>
                                </div>
                                <div class="card-body p-3">
                                    @if ($pkg->inclusions)
                                        <div class="mb-2">
                                            <strong class="text-success"><i class="mdi mdi-check-circle me-1"></i>
                                                Inclusions:</strong>
                                            <div class="text-muted ps-3 whitespace-pre-line"
                                                style="white-space: pre-line;">{{ $pkg->inclusions }}</div>
                                        </div>
                                    @endif
                                    @if ($pkg->exclusions)
                                        <div>
                                            <strong class="text-danger"><i class="mdi mdi-close-circle me-1"></i>
                                                Exclusions:</strong>
                                            <div class="text-muted ps-3 whitespace-pre-line"
                                                style="white-space: pre-line;">{{ $pkg->exclusions }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer bg-light py-2">
                        <a href="{{ route('mobile-ops.umrah-packages.edit', $pkg->id) }}"
                            class="btn btn-primary btn-sm px-3">
                            <i class="mdi mdi-pencil me-1"></i> Edit Package
                        </a>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#umrah-packages-datatable').DataTable({
            paging: true,
            info: true,
            searching: false,
            lengthChange: true,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            pageLength: 10,
            responsive: true,
            order: [],
            columnDefs: [
                { orderable: false, targets: [0, 6] }
            ],
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
