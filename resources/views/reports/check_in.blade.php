@extends('layout.master')
@section('title', 'Hotel Check-In Report')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                {{-- Page Title & Actions --}}
                <div class="py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 no-print">
                    <div>
                        <h4 class="fs-20 fw-bold m-0 text-dark">
                            <i class="mdi mdi-hotel text-primary me-2"></i>Hotel Check-In Report & Manifest
                        </h4>
                        <p class="text-muted mb-0 small">Real-time rooming manifests, daily arrivals, pilgrim details, and stay schedules.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('report.check-in') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="mdi mdi-refresh me-1"></i> Reset Filters
                        </a>
                        <button onclick="window.print()" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-printer me-1"></i> Print Manifest
                        </button>
                    </div>
                </div>

                {{-- KPI Summary Cards --}}
                <div class="row g-3 mb-4 no-print">
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-white-50 small fw-semibold text-uppercase">Total Stays</span>
                                        <h3 class="fw-bold my-1 text-white">{{ number_format($totalStays) }}</h3>
                                        <span class="badge bg-white text-primary px-2 py-0" style="font-size:11px;">{{ $totalRooms }} Rooms</span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-white bg-opacity-25">
                                        <i class="mdi mdi-domain fs-24 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-white-50 small fw-semibold text-uppercase">Total Pilgrims</span>
                                        <h3 class="fw-bold my-1 text-white">{{ number_format($totalPilgrims) }}</h3>
                                        <span class="badge bg-white text-success px-2 py-0" style="font-size:11px;">Hajji / Guests</span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-white bg-opacity-25">
                                        <i class="mdi mdi-account-group fs-24 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); color: #fff;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-white-50 small fw-semibold text-uppercase">Today's Check-in</span>
                                        <h3 class="fw-bold my-1 text-white">{{ number_format($todayCheckins) }}</h3>
                                        <span class="badge bg-white text-danger px-2 py-0" style="font-size:11px;">{{ \Carbon\Carbon::today()->format('d M') }}</span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-white bg-opacity-25">
                                        <i class="mdi mdi-calendar-today fs-24 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #f09819 0%, #edde5d 100%); color: #333;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase">Next 7 Days</span>
                                        <h3 class="fw-bold my-1 text-dark">{{ number_format($upcoming7Days) }}</h3>
                                        <span class="badge bg-dark text-white px-2 py-0" style="font-size:11px;">Upcoming</span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-dark bg-opacity-10">
                                        <i class="mdi mdi-calendar-clock fs-24 text-dark"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #4776e6 0%, #8e54e9 100%); color: #fff;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-white-50 small fw-semibold text-uppercase">Active In-House</span>
                                        <h3 class="fw-bold my-1 text-white">{{ number_format($activeInHouse) }}</h3>
                                        <span class="badge bg-white text-primary px-2 py-0" style="font-size:11px;">Currently Staying</span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-white bg-opacity-25">
                                        <i class="mdi mdi-bed-king fs-24 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100 bg-light border">
                            <div class="card-body p-3 d-flex flex-column justify-content-center text-center">
                                <span class="text-muted small fw-semibold">Report Timestamp</span>
                                <strong class="fs-13 text-dark mt-1">{{ now()->format('d M Y, h:i A') }}</strong>
                                <span class="text-success small fw-bold mt-1"><i class="mdi mdi-check-decagram me-1"></i>Live Synchronized</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filter Card --}}
                <div class="card shadow-sm border-0 mb-4 no-print">
                    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h6 class="m-0 fw-bold text-dark">
                            <i class="mdi mdi-filter-variant me-1 text-primary"></i> Filter Check-In Criteria
                        </h6>
                        {{-- Quick Filter Pills --}}
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('report.check-in', array_merge(request()->query(), ['quick_filter' => 'all', 'from_date' => null, 'to_date' => null])) }}" 
                               class="btn {{ ($quickFilter == 'all' || empty($quickFilter)) && empty($fromDate) ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
                            <a href="{{ route('report.check-in', array_merge(request()->query(), ['quick_filter' => 'today', 'from_date' => null, 'to_date' => null])) }}" 
                               class="btn {{ $quickFilter == 'today' ? 'btn-primary' : 'btn-outline-secondary' }}">Today</a>
                            <a href="{{ route('report.check-in', array_merge(request()->query(), ['quick_filter' => 'tomorrow', 'from_date' => null, 'to_date' => null])) }}" 
                               class="btn {{ $quickFilter == 'tomorrow' ? 'btn-primary' : 'btn-outline-secondary' }}">Tomorrow</a>
                            <a href="{{ route('report.check-in', array_merge(request()->query(), ['quick_filter' => 'this_week', 'from_date' => null, 'to_date' => null])) }}" 
                               class="btn {{ $quickFilter == 'this_week' ? 'btn-primary' : 'btn-outline-secondary' }}">Next 7 Days</a>
                            <a href="{{ route('report.check-in', array_merge(request()->query(), ['quick_filter' => 'this_month', 'from_date' => null, 'to_date' => null])) }}" 
                               class="btn {{ $quickFilter == 'this_month' ? 'btn-primary' : 'btn-outline-secondary' }}">This Month</a>
                            <a href="{{ route('report.check-in', array_merge(request()->query(), ['quick_filter' => 'upcoming', 'from_date' => null, 'to_date' => null])) }}" 
                               class="btn {{ $quickFilter == 'upcoming' ? 'btn-primary' : 'btn-outline-secondary' }}">All Upcoming</a>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('report.check-in') }}" id="checkinFilterForm">
                            <div class="row g-3">
                                {{-- Hotel Name --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label small fw-bold">Hotel Name</label>
                                    <input type="text" name="hotel_name" class="form-control form-control-sm" list="hotelList" 
                                           placeholder="Type or select hotel..." value="{{ $hotelFilter }}">
                                    <datalist id="hotelList">
                                        @foreach ($registeredHotels as $rh)
                                            <option value="{{ $rh->name }}">{{ $rh->place ? ' (' . ucfirst($rh->place) . ')' : '' }}</option>
                                        @endforeach
                                        @foreach ($distinctHotelNames as $dName)
                                            <option value="{{ $dName }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>

                                {{-- City / Location --}}
                                <div class="col-lg-2 col-md-6">
                                    <label class="form-label small fw-bold">City / Location</label>
                                    <select name="location" class="form-select form-select-sm">
                                        <option value="all" {{ ($locationFilter == 'all' || empty($locationFilter)) ? 'selected' : '' }}>All Locations</option>
                                        <option value="makkah" {{ $locationFilter == 'makkah' ? 'selected' : '' }}>🕋 Makkah</option>
                                        <option value="madinah" {{ $locationFilter == 'madinah' ? 'selected' : '' }}>🕌 Madinah</option>
                                        <option value="azizia" {{ $locationFilter == 'azizia' ? 'selected' : '' }}>🏢 Azizia</option>
                                        <option value="jeddah" {{ $locationFilter == 'jeddah' ? 'selected' : '' }}>🌊 Jeddah</option>
                                        <option value="mina" {{ $locationFilter == 'mina' ? 'selected' : '' }}>⛺ Mina</option>
                                    </select>
                                </div>

                                {{-- Check-In Date From --}}
                                <div class="col-lg-2 col-md-3 col-6">
                                    <label class="form-label small fw-bold">Check-In From</label>
                                    <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate }}">
                                </div>

                                {{-- Check-In Date To --}}
                                <div class="col-lg-2 col-md-3 col-6">
                                    <label class="form-label small fw-bold">Check-In To</label>
                                    <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate }}">
                                </div>

                                {{-- Package --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label small fw-bold">Package</label>
                                    <select name="package_id" class="form-select form-select-sm">
                                        <option value="">-- All Packages --</option>
                                        @foreach ($packages as $pkg)
                                            <option value="{{ $pkg->id }}" {{ $packageFilter == $pkg->id ? 'selected' : '' }}>
                                                {{ $pkg->package_code ? '[' . $pkg->package_code . '] ' : '' }}{{ $pkg->package_title ?: ($pkg->name ?: 'Package #' . $pkg->id) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Search keyword --}}
                                <div class="col-lg-9 col-md-8">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light"><i class="mdi mdi-magnify"></i></span>
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Search by Pilgrim Name, Passport #, CNIC, Voucher Ref (UB...), or Client..." 
                                               value="{{ $search }}">
                                    </div>
                                </div>

                                {{-- Filter Action Buttons --}}
                                <div class="col-lg-3 col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                        <i class="mdi mdi-filter me-1"></i> Apply Filter
                                    </button>
                                    <a href="{{ route('report.check-in') }}" class="btn btn-light btn-sm border">
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Printable Header (Visible only in Print) --}}
                <div class="print-only mb-3" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-2">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ asset('assets/images/PIRWANI PNG FILE.png') }}" alt="Logo" style="height: 60px; object-fit: contain;">
                            <div>
                                <h3 class="m-0 fw-bold" style="color:#000; letter-spacing:0.5px;">PIRWANI HAJJ GROUP</h3>
                                <p class="m-0 small text-muted">HOTEL CHECK-IN & ROOMING MANIFEST REPORT</p>
                            </div>
                        </div>
                        <div class="text-end" style="font-size: 11px;">
                            <div><strong>Printed On:</strong> {{ now()->format('d M Y, h:i A') }}</div>
                            <div><strong>Date Range:</strong> {{ $fromDate ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'All Dates' }} - {{ $toDate ? \Carbon\Carbon::parse($toDate)->format('d M Y') : 'Present' }}</div>
                            <div><strong>Total Check-Ins:</strong> {{ $totalStays }} Stays ({{ $totalPilgrims }} Pilgrims)</div>
                        </div>
                    </div>
                </div>

                {{-- Report Results Table Card --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="m-0 fw-bold text-dark">
                                Check-In Records ({{ $hotelStays->count() }})
                            </h6>
                            @if (!empty($fromDate) || !empty($toDate) || !empty($hotelFilter) || !empty($locationFilter) || !empty($search) || !empty($packageFilter))
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Filtered</span>
                            @endif
                        </div>
                        <div class="text-muted small">
                            Showing all matched hotel rooming allocations
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0" id="checkInManifestTable" style="font-size: 13px;">
                                <thead class="table-light text-uppercase text-dark fw-semibold" style="font-size: 11.5px; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">#</th>
                                        <th style="min-width: 220px;">Pilgrim / Hajji Details</th>
                                        <th style="min-width: 180px;">Hotel & Location</th>
                                        <th style="min-width: 130px;">Room Details</th>
                                        <th style="min-width: 140px;">Check-In Date</th>
                                        <th style="min-width: 140px;">Check-Out Date</th>
                                        <th class="text-center" style="min-width: 80px;">Nights</th>
                                        <th style="min-width: 160px;">Package & Voucher</th>
                                        <th class="text-center" style="min-width: 110px;">Status</th>
                                        <th class="text-center no-print" style="width: 90px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($hotelStays as $idx => $stay)
                                        @php
                                            $booking = $stay->booking;
                                            $persons = $booking ? $booking->persons : collect([]);
                                            $mainPerson = $persons->first();
                                            $paxCount = $booking ? ($booking->no_of_pax ?: $persons->count()) : 1;
                                            
                                            $checkIn = $stay->check_in ? \Carbon\Carbon::parse($stay->check_in) : null;
                                            $checkOut = $stay->check_out ? \Carbon\Carbon::parse($stay->check_out) : null;
                                            $todayC = \Carbon\Carbon::today();

                                            // Determine Status
                                            $statusText = 'Scheduled';
                                            $statusBadge = 'bg-secondary';
                                            if ($checkIn) {
                                                if ($checkIn->isToday()) {
                                                    $statusText = 'Checking In Today';
                                                    $statusBadge = 'bg-danger text-white animate-pulse';
                                                } elseif ($checkIn->isTomorrow()) {
                                                    $statusText = 'Arriving Tomorrow';
                                                    $statusBadge = 'bg-warning text-dark';
                                                } elseif ($checkIn->isFuture()) {
                                                    $statusText = 'Upcoming (' . $checkIn->diffForHumans() . ')';
                                                    $statusBadge = 'bg-info text-white';
                                                } elseif ($checkOut && $checkOut->isPast()) {
                                                    $statusText = 'Checked Out';
                                                    $statusBadge = 'bg-light text-muted border';
                                                } elseif ($checkIn->isPast() && ($checkOut ? $checkOut->isFuture() || $checkOut->isToday() : true)) {
                                                    $statusText = 'In-House (Staying)';
                                                    $statusBadge = 'bg-success text-white';
                                                }
                                            }

                                            $locBadge = match(strtolower($stay->location ?? '')) {
                                                'makkah' => 'background:#e3f2fd; color:#0d47a1; border:1px solid #bbdefb;',
                                                'madinah' => 'background:#e8f5e9; color:#1b5e20; border:1px solid #c8e6c9;',
                                                'azizia' => 'background:#fff8e1; color:#f57f17; border:1px solid #ffe082;',
                                                'jeddah' => 'background:#e0f2f1; color:#004d40; border:1px solid #b2dfdb;',
                                                default => 'background:#f5f5f5; color:#424242; border:1px solid #e0e0e0;'
                                            };
                                        @endphp
                                        <tr>
                                            <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>

                                            {{-- Pilgrim / Hajji Details with Photo --}}
                                            <td>
                                                <div class="d-flex align-items-start gap-2">
                                                    {{-- Photo Thumbnail --}}
                                                    <div class="flex-shrink-0">
                                                        @if ($mainPerson && !empty($mainPerson->photo))
                                                            <a href="{{ asset($mainPerson->photo) }}" target="_blank" title="View Full Photo">
                                                                <img src="{{ asset($mainPerson->photo) }}" alt="{{ $mainPerson->full_name }}" 
                                                                     class="rounded shadow-sm border" 
                                                                     style="width: 42px; height: 42px; object-fit: cover; border-color: #c9a84c !important;">
                                                            </a>
                                                        @else
                                                            <div class="rounded d-flex align-items-center justify-content-center fw-bold text-primary border" 
                                                                 style="width: 42px; height: 42px; background: #eef2ff; font-size: 14px;">
                                                                {{ strtoupper(substr($mainPerson->full_name ?? ($booking->client->name ?? 'G'), 0, 1)) }}
                                                            </div>
                                                        @endif
                                                    </div>

                                                    {{-- Pilgrim Info --}}
                                                    <div class="flex-grow-1" style="line-height: 1.3;">
                                                        <strong class="text-dark d-block" style="font-size: 13.5px;">
                                                            {{ $mainPerson->full_name ?? ($booking?->client?->name ?? ($booking?->company?->name ?? 'N/A')) }}
                                                        </strong>
                                                        <div class="text-muted small mt-1">
                                                            @if ($mainPerson && $mainPerson->passport_number)
                                                                <span class="badge bg-light text-dark border px-1 py-0 me-1">
                                                                    <i class="mdi mdi-passport me-1"></i>{{ $mainPerson->passport_number }}
                                                                </span>
                                                            @elseif ($booking && $booking->passport_number)
                                                                <span class="badge bg-light text-dark border px-1 py-0 me-1">
                                                                    <i class="mdi mdi-passport me-1"></i>{{ $booking->passport_number }}
                                                                </span>
                                                            @endif

                                                            @if ($paxCount > 1)
                                                                <span class="badge bg-primary-subtle text-primary px-1 py-0" title="Total Pilgrims in Booking">
                                                                    +{{ $paxCount - 1 }} more (Total {{ $paxCount }})
                                                                </span>
                                                            @endif
                                                        </div>

                                                        {{-- Other Pilgrims in Booking tooltip / popup details --}}
                                                        @if ($persons->count() > 1)
                                                            <div class="mt-1" style="font-size: 11px;">
                                                                <span class="text-muted">Other Pax: </span>
                                                                @foreach ($persons->slice(1, 3) as $otherP)
                                                                    <span class="text-secondary fw-semibold">{{ $otherP->full_name }}</span>{{ !$loop->last ? ', ' : '' }}
                                                                @endforeach
                                                                @if ($persons->count() > 4)
                                                                    <span class="text-muted">+{{ $persons->count() - 4 }} more</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Hotel & Location --}}
                                            <td>
                                                <div class="fw-bold text-dark fs-13">
                                                    {{ strtoupper($stay->hotel_name ?: 'Hotel Not Specified') }}
                                                </div>
                                                <div class="mt-1">
                                                    <span class="badge px-2 py-1 rounded-pill" style="{{ $locBadge }}; font-size:11px; font-weight:600;">
                                                        <i class="mdi mdi-map-marker me-1"></i>{{ strtoupper($stay->location ?: 'Location') }}
                                                    </span>
                                                </div>
                                            </td>

                                            {{-- Room Details --}}
                                            <td>
                                                <div class="fw-semibold text-dark">
                                                    <span class="badge bg-dark text-white px-2 py-1" style="font-size:11px;">
                                                        {{ ucfirst($stay->room_type ?: 'Standard') }} Room
                                                    </span>
                                                </div>
                                                <div class="text-muted small mt-1">
                                                    <strong>{{ $stay->no_of_rooms ?: 1 }}</strong> Room(s)
                                                </div>
                                            </td>

                                            {{-- Check-In Date --}}
                                            <td>
                                                <div class="fw-bold text-dark">
                                                    <i class="mdi mdi-calendar-arrow-right text-success me-1"></i>
                                                    {{ $checkIn ? $checkIn->format('d M Y') : '—' }}
                                                </div>
                                                <small class="text-muted d-block ps-3">
                                                    {{ $checkIn ? $checkIn->format('l') : '' }} (04:00 PM)
                                                </small>
                                            </td>

                                            {{-- Check-Out Date --}}
                                            <td>
                                                <div class="fw-bold text-dark">
                                                    <i class="mdi mdi-calendar-arrow-left text-danger me-1"></i>
                                                    {{ $checkOut ? $checkOut->format('d M Y') : '—' }}
                                                </div>
                                                <small class="text-muted d-block ps-3">
                                                    {{ $checkOut ? $checkOut->format('l') : '' }} (12:00 PM)
                                                </small>
                                            </td>

                                            {{-- Nights --}}
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border px-2 py-1 fs-12 fw-bold">
                                                    {{ $stay->no_of_nights ?: ($checkIn && $checkOut ? $checkIn->diffInDays($checkOut) : '—') }} N
                                                </span>
                                            </td>

                                            {{-- Package & Voucher Ref --}}
                                            <td>
                                                <div class="fw-semibold text-primary">
                                                    @if ($booking && $booking->package)
                                                        @if ($booking->package->package_code)
                                                            <span class="badge bg-dark text-white me-1" style="font-size:10px;">{{ $booking->package->package_code }}</span>
                                                        @endif
                                                        {{ $booking->package->package_title ?: ($booking->package->name ?: 'Package') }}
                                                    @elseif ($booking)
                                                        {{ strtoupper($booking->package_type ?? 'Hajj / Umrah') }}
                                                    @else
                                                        —
                                                    @endif
                                                </div>
                                                <div class="small text-muted mt-1">
                                                    <span class="fw-bold text-dark">Voucher:</span> 
                                                    @if ($booking)
                                                        <a href="{{ route('booking.voucher', $booking->id) }}" target="_blank" class="text-decoration-none fw-semibold">
                                                            {{ $booking->voucher_number ?: ('UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT)) }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </div>
                                                @if ($booking && $booking->client)
                                                    <div class="text-muted" style="font-size: 11px;">
                                                        Client: {{ $booking->client->name }}
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Status --}}
                                            <td class="text-center">
                                                <span class="badge {{ $statusBadge }} px-2 py-1" style="font-size: 11px;">
                                                    {{ $statusText }}
                                                </span>
                                            </td>

                                            {{-- Action --}}
                                            <td class="text-center no-print">
                                                @if ($booking)
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('booking.show', $booking->id) }}" class="btn btn-light border text-primary" title="View Booking Details">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('booking.voucher', $booking->id) }}" target="_blank" class="btn btn-light border text-success" title="Print / Download Voucher">
                                                            <i class="mdi mdi-ticket-percent-outline"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5">
                                                <div class="py-4">
                                                    <i class="mdi mdi-hotel text-muted" style="font-size: 48px;"></i>
                                                    <h5 class="fw-bold text-muted mt-2">No Hotel Check-In Records Found</h5>
                                                    <p class="text-muted small mb-3">No bookings match the selected hotel, location, date range, or pilgrim search criteria.</p>
                                                    <a href="{{ route('report.check-in') }}" class="btn btn-primary btn-sm">
                                                        <i class="mdi mdi-refresh me-1"></i> Clear Filters
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Custom CSS for Printing and Animations --}}
    <style>
        .animate-pulse {
            animation: pulse-animation 2s infinite;
        }
        @keyframes pulse-animation {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.85; }
            100% { transform: scale(1); opacity: 1; }
        }

        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
            .content-page {
                margin: 0 !important;
                padding: 0 !important;
            }
            body {
                background: #fff !important;
                color: #000 !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            table {
                font-size: 10px !important;
                width: 100% !important;
            }
            th, td {
                padding: 4px 6px !important;
            }
            @page {
                size: A4 landscape;
                margin: 8mm 8mm;
            }
        }
    </style>
@endsection
