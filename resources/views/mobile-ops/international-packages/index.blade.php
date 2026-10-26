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
            <h4 class="fs-18 fw-bold text-dark m-0">International Packages</h4>
            <p class="text-muted fs-13 mb-0">Manage worldwide holiday packages, international tours, multi-country itineraries, and ground logistics.</p>
        </div>
        <div class="d-flex gap-2">
            @php
                $trashTotal = \App\Models\Package::onlyTrashed()->where('package_type', 'international')->count();
            @endphp
            <a href="{{ route('mobile-ops.international-packages.trash') }}" class="btn btn-outline-danger btn-sm px-3">
                <i class="mdi mdi-trash-can-outline me-1"></i> View Trash
                @if ($trashTotal > 0)
                    <span class="badge bg-danger text-white ms-1">{{ $trashTotal }}</span>
                @endif
            </a>
            <a href="{{ route('mobile-ops.international-packages.create') }}" class="btn btn-success btn-sm px-3"
                style="background-color: #1e7456; border-color: #1e7456;">
                <i class="mdi mdi-plus-circle me-1"></i> Add International Package
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card border-0 shadow-sm mt-3" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('mobile-ops.international-packages.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i
                                class="mdi mdi-magnify text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0"
                            placeholder="Search by package title, code, destination country, or city..." value="{{ $search ?? '' }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
                    @if (!empty($search))
                        <a href="{{ route('mobile-ops.international-packages.index') }}"
                            class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
                    @endif
                </div>
                <div class="col-md-4 text-md-end text-muted fs-13">
                    Showing <strong>{{ $packages->total() }}</strong> International Packages
                </div>
            </form>
        </div>
    </div>

    <!-- Simplified Packages Table -->
    <div class="card border-0 shadow-sm mt-3" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
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
                                $startingPrice = $pkg->price_per_person > 0 
                                    ? $pkg->price_per_person 
                                    : ($pkg->price_sharing > 0 
                                        ? $pkg->price_sharing 
                                        : ($pkg->price_quad > 0 
                                            ? $pkg->price_quad 
                                            : ($pkg->price_triple > 0 
                                                ? $pkg->price_triple 
                                                : ($pkg->price_double > 0 ? $pkg->price_double : 0))));
                                $acc = $pkg->accommodations->first();
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
                                                <i class="mdi mdi-earth"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <a href="javascript:void(0);" data-bs-toggle="modal"
                                                data-bs-target="#packageDetailsModal{{ $pkg->id }}"
                                                class="fw-bold text-dark text-decoration-none hover-primary">
                                                {{ $pkg->package_title ?? $pkg->name }}
                                            </a>
                                            <div class="fs-11 text-muted text-truncate" style="max-width: 280px;">
                                                @if ($pkg->subtitle)
                                                    {{ Str::limit($pkg->subtitle, 35) }} &bull;
                                                @endif
                                                <span class="text-dark">{{ $pkg->destination_country ?? 'Global' }}
                                                    @if($pkg->destination_city) ({{ $pkg->destination_city }}) @endif
                                                </span>
                                            </div>
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
                                    @if ($startingPrice > 0)
                                        <div class="fw-bold text-success fs-14">PKR {{ number_format($startingPrice, 0) }}</div>
                                        <span class="text-muted fs-11">{{ $pkg->price_per_person > 0 ? 'Per Person' : 'Starting from' }}</span>
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
                                    <div class="d-flex justify-content-end gap-1">
                                        <!-- Details Action Icon -->
                                        <button type="button" class="btn btn-outline-info btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#packageDetailsModal{{ $pkg->id }}"
                                            title="View Package Details">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </button>
                                        <!-- Edit Action Icon -->
                                        <a href="{{ route('mobile-ops.international-packages.edit', $pkg->id) }}"
                                            class="btn btn-outline-primary btn-sm" title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <!-- Trash Action Icon -->
                                        <form action="{{ route('mobile-ops.international-packages.destroy', $pkg->id) }}"
                                            method="POST" onsubmit="return confirm('Move package to trash?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                title="Move to Trash">
                                                <i class="mdi mdi-trash-can-outline"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-earth fs-36 text-muted mb-2 d-block"></i>
                                    <h5>No International packages found</h5>
                                    <p class="fs-13 text-muted mb-3">Get started by creating your first international holiday package with hotels and rates.</p>
                                    <a href="{{ route('mobile-ops.international-packages.create') }}"
                                        class="btn btn-primary btn-sm rounded-pill px-3">
                                        <i class="mdi mdi-plus me-1"></i> Add International Package
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($packages->hasPages())
                <div class="p-3 border-top">
                    {{ $packages->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Package Details Modals (Consistent with Umrah Package Detail Modal) -->
    @foreach ($packages as $pkg)
        @php
            $acc = $pkg->accommodations->first();
            $outbound = $pkg->transportFlights->firstWhere('is_preferred', 1) ?? $pkg->transportFlights->first();
            $inbound = $pkg->transportFlights->where('id', '!=', optional($outbound)->id)->first();
            $transport = $pkg->transports->first();
            $terms = $pkg->terms;

            // Inclusions decoding
            $incList = [];
            if (!empty($pkg->inclusions)) {
                $decoded = json_decode($pkg->inclusions, true);
                if (is_array($decoded)) {
                    $incList = $decoded;
                } else {
                    $incList = array_filter(array_map('trim', explode("\n", $pkg->inclusions)));
                }
            }
        @endphp
        <div class="modal fade" id="packageDetailsModal{{ $pkg->id }}" tabindex="-1"
            aria-labelledby="packageDetailsLabel{{ $pkg->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light py-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success p-2 rounded-circle">
                                <i class="mdi mdi-earth fs-18"></i>
                            </span>
                            <div>
                                <h5 class="modal-title fs-16 fw-bold mb-0 text-dark"
                                    id="packageDetailsLabel{{ $pkg->id }}">
                                    {{ $pkg->package_title ?? $pkg->name }}
                                </h5>
                                <div class="fs-12 text-muted">
                                    @if ($pkg->package_code ?? $pkg->code)
                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-0">Code:
                                            {{ $pkg->package_code ?? $pkg->code }}</span>
                                    @endif
                                    <span
                                        class="badge {{ $pkg->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-2 py-0">
                                        {{ ucfirst($pkg->status) }}
                                    </span>
                                    <span
                                        class="badge {{ $pkg->visa_status === 'included' ? 'bg-info-subtle text-info' : 'bg-warning-subtle text-warning' }} px-2 py-0">
                                        Visa {{ ucfirst($pkg->visa_status) }}
                                    </span>
                                    <span class="badge bg-primary-subtle text-primary px-2 py-0">
                                        {{ $pkg->destination_country ?? 'Global' }}
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
                                <h6 class="fs-13 fw-bold m-0 text-dark">
                                    <i class="mdi mdi-currency-usd text-success me-1"></i> Rates & Pricing Matrix
                                </h6>
                            </div>
                            <div class="card-body p-2">
                                <div class="row g-2 text-center">
                                    <div class="col-md">
                                        <div class="p-2 border rounded bg-light">
                                            <div class="text-muted fs-11">Per Person</div>
                                            <div class="fw-bold fs-13 text-success">
                                                {{ $pkg->price_per_person > 0 ? 'PKR ' . number_format($pkg->price_per_person, 0) : '—' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="p-2 border rounded bg-light">
                                            <div class="text-muted fs-11">Sharing</div>
                                            <div class="fw-bold fs-13 text-dark">
                                                {{ $pkg->price_sharing > 0 ? 'PKR ' . number_format($pkg->price_sharing, 0) : '—' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="p-2 border rounded bg-light">
                                            <div class="text-muted fs-11">Quad Room</div>
                                            <div class="fw-bold fs-13 text-dark">
                                                {{ $pkg->price_quad > 0 ? 'PKR ' . number_format($pkg->price_quad, 0) : '—' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="p-2 border rounded bg-light">
                                            <div class="text-muted fs-11">Triple Room</div>
                                            <div class="fw-bold fs-13 text-dark">
                                                {{ $pkg->price_triple > 0 ? 'PKR ' . number_format($pkg->price_triple, 0) : '—' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="p-2 border rounded bg-light">
                                            <div class="text-muted fs-11">Double Room</div>
                                            <div class="fw-bold fs-13 text-dark">
                                                {{ $pkg->price_double > 0 ? 'PKR ' . number_format($pkg->price_double, 0) : '—' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hotel Details -->
                        <div class="card border shadow-none mb-3">
                            <div class="card-header bg-light py-2">
                                <h6 class="fs-13 fw-bold m-0 text-dark">
                                    <i class="mdi mdi-office-building text-primary me-1"></i> Hotel Accommodations
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-md-6 border-end">
                                        <div class="fw-bold text-success mb-1">
                                            <i class="mdi mdi-map-marker me-1"></i> {{ $pkg->destination_country ?? 'Primary Destination' }} Stay
                                            @if($pkg->destination_city) ({{ $pkg->destination_city }}) @endif
                                        </div>
                                        <div class="text-dark fw-medium">{{ $pkg->primary_hotel ?? optional($acc)->hotel ?? 'Not Specified' }}</div>
                                        <div class="text-muted fs-11">
                                            Rating: {{ $pkg->hotel_star_rating ?? optional($acc)->accommodation_type ?? 'Standard' }} | 
                                            Duration: {{ $pkg->stay_nights ? $pkg->stay_nights . ' Nights' : 'Flexible' }}
                                        </div>
                                        <div class="text-muted fs-11">Meal Plan: {{ $pkg->meal_plan ?? optional($acc)->food_package ?? 'RO' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="fw-bold text-dark mb-1">
                                            <i class="mdi mdi-car text-muted me-1"></i> Ground Logistics
                                        </div>
                                        <div class="text-dark">{{ $pkg->ground_transfer_fleet ?? 'Standard Vehicle Transfer' }}</div>
                                        @if(is_array($pkg->additional_destinations) && count($pkg->additional_destinations) > 0)
                                            <div class="mt-2 text-primary fs-12 fw-medium">
                                                <i class="mdi mdi-earth me-1"></i> {{ count($pkg->additional_destinations) }} Additional Countries / Destinations Included
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Flight Details -->
                        @if ($outbound || $inbound)
                            <div class="card border shadow-none mb-3">
                                <div class="card-header bg-light py-2">
                                    <h6 class="fs-13 fw-bold m-0 text-dark">
                                        <i class="mdi mdi-airplane text-info me-1"></i> Flight Details
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-2">
                                        @if ($outbound)
                                            <div class="col-md-6">
                                                <div class="p-2 border rounded bg-light">
                                                    <div class="fw-semibold text-primary fs-12"><i class="mdi mdi-airplane-takeoff me-1"></i> Outbound Flight</div>
                                                    <div><strong>Airline:</strong> {{ $outbound->airline ?? '—' }}</div>
                                                    <div><strong>Route:</strong> {{ $outbound->origin ?? 'Departure' }} &rarr; {{ $outbound->destination ?? 'Arrival' }}</div>
                                                    <div><strong>Flight Type:</strong> {{ $outbound->flight_class ?? 'Economy Class' }}</div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($inbound)
                                            <div class="col-md-6">
                                                <div class="p-2 border rounded bg-light">
                                                    <div class="fw-semibold text-success fs-12"><i class="mdi mdi-airplane-landing me-1"></i> Inbound Flight</div>
                                                    <div><strong>Airline:</strong> {{ $inbound->airline ?? '—' }}</div>
                                                    <div><strong>Route:</strong> {{ $inbound->origin ?? 'Departure' }} &rarr; {{ $inbound->destination ?? 'Arrival' }}</div>
                                                    <div><strong>Return Flight Type:</strong> {{ $inbound->flight_class ?? 'Economy Class' }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Inclusions & Features -->
                        <div class="row g-3">
                            @if (!empty($incList))
                                <div class="col-md-6">
                                    <div class="card border shadow-none h-100">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="fs-13 fw-bold m-0 text-success"><i class="mdi mdi-check-circle me-1"></i> Package Inclusions</h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <ul class="ps-3 mb-0">
                                                @foreach ($incList as $item)
                                                    <li>{{ $item }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (!empty($pkg->key_features))
                                <div class="col-md-6">
                                    <div class="card border shadow-none h-100">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="fs-13 fw-bold m-0 text-dark"><i class="mdi mdi-star-circle me-1"></i> Key Features</h6>
                                        </div>
                                        <div class="card-body p-3 text-muted">
                                            {!! nl2br(e($pkg->key_features)) !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('mobile-ops.international-packages.edit', $pkg->id) }}" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-pencil me-1"></i> Edit Package
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
