@extends('layout.master')
@section('title', 'Official Rooming List & Manifest Report')

@section('content')
<style>
    /* Official Manifest Table Styling matching PDF */
    .manifest-table {
        border-collapse: collapse;
        width: 100%;
        background-color: #ffffff;
        font-family: Arial, sans-serif;
    }
    .manifest-table th {
        background-color: #f8fafc;
        color: #000;
        font-weight: 800;
        font-size: 13px;
        text-align: center;
        vertical-align: middle;
        padding: 10px 6px;
        border: 2px solid #000000 !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .manifest-table td {
        padding: 8px 6px;
        vertical-align: middle;
        font-size: 13px;
        border: 1.5px solid #000000 !important;
    }
    .room-typ-cell {
        font-weight: 800;
        font-size: 14px;
        text-align: center;
        text-transform: uppercase;
        background-color: #ffffff;
        border-left: 2px solid #000 !important;
        border-right: 1.5px solid #000 !important;
    }
    .room-no-cell {
        font-weight: 900;
        font-size: 22px;
        color: #e51c23;
        text-align: center;
        background-color: #ffffff;
        border-left: 1.5px solid #000 !important;
        border-right: 2px solid #000 !important;
        letter-spacing: 1px;
    }
    .room-divider-row td {
        border-top: 3px solid #000000 !important;
    }
    .haji-photo {
        width: 55px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #999;
        display: block;
        margin: 0 auto;
    }
    .haji-photo-placeholder {
        width: 55px;
        height: 60px;
        background-color: #f1f5f9;
        border: 1px dashed #cbd5e1;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    .gender-male {
        color: #1e3a8a;
        font-weight: 700;
    }
    .gender-female {
        color: #9d174d;
        font-weight: 700;
    }
    @media print {
        .no-print, .left-side-menu, .navbar-custom, .footer {
            display: none !important;
        }
        .content-page {
            margin: 0 !important;
            padding: 0 !important;
        }
        body {
            background-color: #fff !important;
            font-size: 12px;
        }
        .manifest-table th, .manifest-table td {
            border: 1.5px solid #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .room-no-cell {
            color: #e51c23 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        tr {
            page-break-inside: avoid;
        }
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            {{-- Page Header & Actions --}}
            <div class="py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 no-print">
                <div>
                    <h4 class="fs-20 fw-bold m-0 text-dark">
                        <i class="mdi mdi-clipboard-text-multiple text-primary me-2"></i>Official Rooming List & Manifest Report
                    </h4>
                    <p class="text-muted mb-0 small">Official multi-pilgrim room allocation manifest matching airline/hotel printout format.</p>
                </div>
                    <div class="d-flex flex-wrap gap-2">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('report.rooming-list', array_merge(request()->query(), ['view' => 'manifest'])) }}" 
                               class="btn {{ ($viewMode ?? 'manifest') === 'manifest' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="mdi mdi-table me-1"></i> Official Manifest View
                            </a>
                            <a href="{{ route('report.rooming-list', array_merge(request()->query(), ['view' => 'cards'])) }}" 
                               class="btn {{ ($viewMode ?? '') === 'cards' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="mdi mdi-view-grid-outline me-1"></i> Room Cards View
                            </a>
                        </div>
                        <a href="{{ route('report.rooming-list.export-excel', request()->query()) }}" 
                           class="btn btn-success btn-sm"
                           title="Download official room manifest as Excel spreadsheet">
                            <i class="mdi mdi-file-excel me-1"></i> Download Excel
                        </a>
                        <a href="{{ route('report.rooming-list.export-pdf', request()->query()) }}" 
                           class="btn btn-danger btn-sm"
                           title="Download official room manifest as PDF document">
                            <i class="mdi mdi-file-pdf-box me-1"></i> Download PDF
                        </a>
                        <button onclick="window.print()" class="btn btn-dark btn-sm">
                            <i class="mdi mdi-printer me-1"></i> Print Manifest
                        </button>
                        <a href="{{ route('report.rooming-list') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
                            <i class="mdi mdi-refresh"></i>
                        </a>
                    </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
                    <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Filter Bar --}}
            <div class="card shadow-sm border-0 mb-3 no-print">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('report.rooming-list') }}" id="filterForm">
                        <input type="hidden" name="view" value="{{ $viewMode ?? 'manifest' }}">
                        <div class="row g-2 align-items-end">

                            {{-- Hotel Filter --}}
                            <div class="col-xl-3 col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Hotel Name</label>
                                <select name="hotel_name" class="form-select form-select-sm">
                                    <option value="">All Hotels</option>
                                    @foreach($registeredHotels as $h)
                                        <option value="{{ $h->name }}" {{ $hotelFilter == $h->name ? 'selected' : '' }}>
                                            {{ $h->name }} ({{ $h->city ?? $h->place ?? 'Hotel' }})
                                        </option>
                                    @endforeach
                                    @foreach($distinctHotelNames as $dhn)
                                        @if(!$registeredHotels->contains('name', $dhn))
                                            <option value="{{ $dhn }}" {{ $hotelFilter == $dhn ? 'selected' : '' }}>{{ $dhn }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            {{-- Location Filter --}}
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Location</label>
                                <select name="location" class="form-select form-select-sm">
                                    <option value="all">All Locations</option>
                                    <option value="makkah" {{ $locationFilter === 'makkah' ? 'selected' : '' }}>Makkah</option>
                                    <option value="madinah" {{ $locationFilter === 'madinah' ? 'selected' : '' }}>Madinah</option>
                                    <option value="azizia" {{ $locationFilter === 'azizia' ? 'selected' : '' }}>Azizia</option>
                                    <option value="mina" {{ $locationFilter === 'mina' ? 'selected' : '' }}>Mina / Arafat</option>
                                    <option value="other" {{ $locationFilter === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            {{-- Room Type Filter --}}
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Room Type</label>
                                <select name="room_type" class="form-select form-select-sm">
                                    <option value="all">All Room Types</option>
                                    <option value="double" {{ strtolower($roomTypeFilter ?? '') === 'double' ? 'selected' : '' }}>DOUBLE (2 Beds)</option>
                                    <option value="triple" {{ strtolower($roomTypeFilter ?? '') === 'triple' ? 'selected' : '' }}>TRIPLE (3 Beds)</option>
                                    <option value="quad" {{ strtolower($roomTypeFilter ?? '') === 'quad' ? 'selected' : '' }}>QUAD (4 Beds)</option>
                                    <option value="quint" {{ strtolower($roomTypeFilter ?? '') === 'quint' ? 'selected' : '' }}>QUINT (5 Beds)</option>
                                    <option value="six" {{ in_array(strtolower($roomTypeFilter ?? ''), ['six', 'sharing']) ? 'selected' : '' }}>SIX (6 Beds)</option>
                                    <option value="single" {{ strtolower($roomTypeFilter ?? '') === 'single' ? 'selected' : '' }}>SINGLE (1 Bed)</option>
                                    <option value="suite" {{ strtolower($roomTypeFilter ?? '') === 'suite' ? 'selected' : '' }}>SUITE</option>
                                </select>
                            </div>

                            {{-- Status Filter --}}
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Occupancy</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="all">All Rooms</option>
                                    <option value="available" {{ $statusFilter === 'available' ? 'selected' : '' }}>🟢 Free Beds Available</option>
                                    <option value="full" {{ $statusFilter === 'full' ? 'selected' : '' }}>🔵 Room Full (100%)</option>
                                    <option value="overbooked" {{ $statusFilter === 'overbooked' ? 'selected' : '' }}>🔴 Overbooked</option>
                                </select>
                            </div>

                            {{-- Date Range --}}
                            <div class="col-xl-3 col-md-5 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Stay Dates (Check In / Out)</label>
                                <div class="input-group input-group-sm">
                                    <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}" placeholder="From">
                                    <span class="input-group-text bg-light text-muted">to</span>
                                    <input type="date" name="to_date" class="form-control" value="{{ $toDate }}" placeholder="To">
                                </div>
                            </div>

                            {{-- Search Bar --}}
                            <div class="col-xl-9 col-md-7 col-sm-8">
                                <label class="form-label small fw-semibold text-muted mb-1">Search Room / Pilgrim / Passport / HB</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-magnify"></i></span>
                                    <input type="text" name="search" class="form-control" placeholder="Search by Room Number (e.g. 101, R102, 204), Pilgrim Name, Passport #, Hajj ID (PW...), HB #..." value="{{ $search }}">
                                    @if(!empty($search))
                                        <a href="{{ route('report.rooming-list', request()->except('search')) }}" class="btn btn-outline-secondary">× Clear</a>
                                    @endif
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="col-xl-3 col-md-5 col-sm-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="mdi mdi-filter me-1"></i> Apply Filter
                                </button>
                                <a href="{{ route('report.rooming-list') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            {{-- Summary Ribbon --}}
            <div class="row g-2 mb-3 no-print">
                <div class="col-md-3 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between">
                        <span class="text-muted small fw-semibold">Total Rooms:</span>
                        <strong class="fs-16 text-dark">{{ number_format($totalRoomsInUse) }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between">
                        <span class="text-muted small fw-semibold">Total Pilgrims:</span>
                        <strong class="fs-16 text-primary">{{ number_format($totalBedsOccupied) }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between">
                        <span class="text-muted small fw-semibold">Total Bed Capacity:</span>
                        <strong class="fs-16 text-info">{{ number_format($totalBedsCapacity) }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between">
                        <span class="text-muted small fw-semibold">Available Beds:</span>
                        <strong class="fs-16 text-success">{{ number_format($totalBedsAvailable) }}</strong>
                    </div>
                </div>
            </div>

            {{-- OFFICIAL MANIFEST TABLE VIEW (EXACT MATCH TO PDF) --}}
            @if(($viewMode ?? 'manifest') === 'manifest')
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="manifest-table align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">SR</th>
                                        <th style="width: 100px;">HAJJ ID</th>
                                        <th style="width: 90px;">HB</th>
                                        <th style="width: 120px;">PASSPORT</th>
                                        <th style="width: 250px; text-align: left; padding-left: 10px;">FULL NAME</th>
                                        <th style="width: 110px;">ROOM TYP</th>
                                        <th style="width: 130px;">ROOM NO</th>
                                        <th style="width: 90px;">Gender</th>
                                        <th style="width: 90px;">Haji Picture</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $globalSr = 1; @endphp
                                    @forelse($groupedRooms as $room)
                                        @php
                                            $occupants = $room['occupants'];
                                            $totalOccupants = count($occupants);
                                            $rowspan = max(1, $totalOccupants);
                                            
                                            // Format room number with 'R' prefix if not present (e.g. 101 -> R101)
                                            $rawNum = trim($room['room_number']);
                                            $roomDisplayNo = str_starts_with(strtoupper($rawNum), 'R') ? strtoupper($rawNum) : ('R' . $rawNum);
                                            
                                            $roomTypeDisplay = strtoupper($room['room_type']);
                                            if (in_array(strtolower($roomTypeDisplay), ['sharing', '6', 'six-bed', '6-bed'])) {
                                                $roomTypeDisplay = 'SIX';
                                            }

                                            $loc = strtolower(trim($room['location'] ?? 'makkah'));
                                            $locLabel = match($loc) {
                                                'makkah'  => 'MAKKAH HOTEL',
                                                'azizia'  => 'AZIZIA BUILDING',
                                                'mina'    => 'MINA / ARAFAT',
                                                'arafat'  => 'ARAFAT CAMP',
                                                'madinah' => 'MADINAH HOTEL',
                                                default   => strtoupper($loc),
                                            };
                                            $locBadgeClass = match($loc) {
                                                'makkah'  => 'bg-success text-white',
                                                'azizia'  => 'bg-warning text-dark',
                                                'mina', 'arafat' => 'bg-dark text-white',
                                                'madinah' => 'bg-info text-dark',
                                                default   => 'bg-secondary text-white',
                                            };
                                        @endphp

                                        @if($totalOccupants === 0)
                                            {{-- Empty Room Row --}}
                                            <tr class="room-divider-row">
                                                <td class="text-center text-muted fw-bold">{{ $globalSr++ }}</td>
                                                <td class="text-center text-muted">—</td>
                                                <td class="text-center text-muted">—</td>
                                                <td class="text-center text-muted">—</td>
                                                <td class="text-muted fst-italic"><em>(Empty Room - {{ $room['total_capacity'] }} Beds Available)</em></td>
                                                <td class="room-typ-cell">
                                                    <div class="mb-1">
                                                        <span class="badge {{ $locBadgeClass }} px-2 py-1" style="font-size: 10px; font-weight: 700;">
                                                            {{ $locLabel }}
                                                        </span>
                                                    </div>
                                                    <div class="fw-bold text-dark mb-1" style="font-size: 11px;">{{ $room['hotel_name'] }}</div>
                                                    <div class="fw-bold text-primary">{{ $roomTypeDisplay }} ({{ $room['total_capacity'] }} Beds)</div>
                                                </td>
                                                <td class="room-no-cell">
                                                    <span class="fs-14 fw-bold text-danger">{{ $roomDisplayNo }}</span>
                                                    <small class="d-block text-muted" style="font-size: 10px;">0/{{ $room['total_capacity'] }} Beds</small>
                                                </td>
                                                <td class="text-center text-muted">—</td>
                                                <td class="text-center text-muted">—</td>
                                            </tr>
                                        @else
                                            @foreach($occupants as $oIdx => $occ)
                                                <tr class="{{ $oIdx === 0 ? 'room-divider-row' : '' }}">
                                                    <td class="text-center fw-bold">{{ $globalSr++ }}</td>
                                                    <td class="text-center fw-semibold text-uppercase">{{ !empty($occ['hajj_id']) ? $occ['hajj_id'] : '—' }}</td>
                                                    <td class="text-center fw-semibold text-uppercase">{{ !empty($occ['hb_number']) ? $occ['hb_number'] : '—' }}</td>
                                                    <td class="text-center fw-bold text-uppercase">{{ $occ['passport'] }}</td>
                                                    <td style="text-align: left; padding-left: 10px;" class="fw-bold text-uppercase text-dark">
                                                        {{ $occ['name'] }}
                                                    </td>
                                                    
                                                    @if($oIdx === 0)
                                                        <td rowspan="{{ $rowspan }}" class="room-typ-cell">
                                                            <div class="mb-1">
                                                                <span class="badge {{ $locBadgeClass }} px-2 py-1" style="font-size: 10px; font-weight: 700; letter-spacing: 0.5px;">
                                                                    <i class="mdi mdi-map-marker me-1"></i>{{ $locLabel }}
                                                                </span>
                                                            </div>
                                                            <div class="fw-bold text-dark mb-1" style="font-size: 11px; line-height: 1.2;">
                                                                {{ $room['hotel_name'] }}
                                                            </div>
                                                            <div class="fw-bold text-primary" style="font-size: 12px;">
                                                                {{ $roomTypeDisplay }} <span class="text-muted" style="font-size: 10px;">({{ $room['total_capacity'] }} Beds)</span>
                                                            </div>
                                                            @if(!empty($room['room_gender']) && $room['room_gender'] !== 'Any')
                                                                <div class="mt-1">
                                                                    <span class="badge {{ strtolower($room['room_gender']) === 'female' ? 'bg-danger text-white' : (strtolower($room['room_gender']) === 'male' ? 'bg-primary text-white' : 'bg-success text-white') }}" style="font-size: 9px; letter-spacing: 0.5px;">
                                                                        {{ strtoupper($room['room_gender']) }} ROOM
                                                                    </span>
                                                                </div>
                                                            @endif
                                                            <div class="no-print mt-1">
                                                                <button type="button" 
                                                                        class="btn btn-xs btn-outline-secondary btn-adjust-bed py-0 px-1"
                                                                        style="font-size: 10px;"
                                                                        data-hotel="{{ $room['hotel_name'] }}"
                                                                        data-room="{{ $room['room_number'] }}"
                                                                        data-type="{{ $room['room_type'] }}"
                                                                        data-gender="{{ $room['room_gender'] ?? 'Any' }}"
                                                                        data-location="{{ $room['location'] }}"
                                                                        data-capacity="{{ $room['total_capacity'] }}"
                                                                        data-extra="{{ $room['extra_beds'] }}"
                                                                        data-notes="{{ $room['notes'] }}"
                                                                        title="Adjust/Increase Bed Capacity & Gender">
                                                                    <i class="mdi mdi-pencil"></i> Adjust Beds
                                                                </button>
                                                            </div>
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}" class="room-no-cell">
                                                            <span class="fs-14 fw-bold text-danger">{{ $roomDisplayNo }}</span>
                                                            <small class="d-block text-muted fw-semibold" style="font-size: 10px;">{{ $room['occupied_beds'] }}/{{ $room['total_capacity'] }} Booked</small>
                                                        </td>
                                                    @endif

                                                    <td class="text-center {{ strtoupper($occ['gender']) === 'FEMALE' ? 'gender-female' : 'gender-male' }} text-uppercase">
                                                        {{ strtoupper($occ['gender']) }}
                                                    </td>
                                                    <td class="text-center p-1">
                                                        @if(!empty($occ['photo']) && file_exists(public_path($occ['photo'])))
                                                            <img src="{{ asset($occ['photo']) }}" alt="{{ $occ['name'] }}" class="haji-photo">
                                                        @else
                                                            <div class="haji-photo-placeholder">
                                                                <i class="mdi {{ strtoupper($occ['gender']) === 'FEMALE' ? 'mdi-account-female text-danger' : 'mdi-account text-primary' }} fs-24"></i>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5 text-muted">
                                                <i class="mdi mdi-bed-empty fs-48 d-block mb-2 text-muted"></i>
                                                <h5>No Room Allocations Found</h5>
                                                <p>Try changing your hotel, room type, or date filters above.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                {{-- ROOM CARDS VIEW --}}
                <div class="row g-3">
                    @forelse($groupedRooms as $room)
                        @php
                            $rawNum = trim($room['room_number']);
                            $roomDisplayNo = str_starts_with(strtoupper($rawNum), 'R') ? strtoupper($rawNum) : ('R' . $rawNum);
                            $pct = $room['total_capacity'] > 0 ? min(100, round(($room['occupied_beds'] / $room['total_capacity']) * 100)) : 0;
                            
                            $badgeColor = 'bg-success';
                            if ($room['is_overbooked']) $badgeColor = 'bg-danger';
                            elseif ($room['is_full']) $badgeColor = 'bg-primary';
                        @endphp
                        <div class="col-xl-6 col-12">
                            <div class="card shadow-sm border h-100">
                                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-danger fs-16 px-2 py-1 fw-bold">{{ $roomDisplayNo }}</span>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">{{ $room['hotel_name'] }}</h6>
                                            <small class="text-muted text-uppercase fw-semibold">{{ $room['room_type'] }} &bull; {{ $room['location'] }}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge {{ $badgeColor }} fs-11 px-2 py-1">
                                            {{ $room['occupied_beds'] }} / {{ $room['total_capacity'] }} Beds
                                        </span>
                                        <button type="button" 
                                                class="btn btn-outline-primary btn-xs ms-2 btn-adjust-bed py-0 px-2"
                                                data-hotel="{{ $room['hotel_name'] }}"
                                                data-room="{{ $room['room_number'] }}"
                                                data-type="{{ $room['room_type'] }}"
                                                data-location="{{ $room['location'] }}"
                                                data-capacity="{{ $room['total_capacity'] }}"
                                                data-extra="{{ $room['extra_beds'] }}"
                                                data-notes="{{ $room['notes'] }}">
                                            + Adjust Beds
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped align-middle mb-0" style="font-size: 12px;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Photo</th>
                                                    <th>Hajj ID</th>
                                                    <th>Passport</th>
                                                    <th>Name</th>
                                                    <th>Gender</th>
                                                    <th>Booking</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($room['occupants'] as $occ)
                                                    <tr>
                                                        <td style="width: 40px;">
                                                            @if(!empty($occ['photo']) && file_exists(public_path($occ['photo'])))
                                                                <img src="{{ asset($occ['photo']) }}" class="rounded" style="width: 32px; height: 36px; object-fit: cover;">
                                                            @else
                                                                <i class="mdi {{ strtoupper($occ['gender']) === 'FEMALE' ? 'mdi-account-female text-danger' : 'mdi-account text-primary' }} fs-18"></i>
                                                            @endif
                                                        </td>
                                                        <td class="fw-semibold">{{ !empty($occ['hajj_id']) ? $occ['hajj_id'] : '—' }}</td>
                                                        <td class="fw-bold">{{ $occ['passport'] }}</td>
                                                        <td class="fw-bold text-dark">{{ $occ['name'] }}</td>
                                                        <td class="{{ strtoupper($occ['gender']) === 'FEMALE' ? 'text-danger' : 'text-primary' }} fw-semibold">{{ strtoupper($occ['gender']) }}</td>
                                                        <td>
                                                            <a href="{{ route('booking.show', $occ['booking_id']) }}" class="text-primary fw-bold" target="_blank">{{ $occ['booking_number'] }}</a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-2"><em>No pilgrims assigned yet.</em></td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <p class="text-muted">No room allocations found.</p>
                        </div>
                    @endforelse
                </div>
            @endif

        </div>
    </div>
</div>

{{-- Adjust Bed Capacity Modal --}}
<div class="modal fade" id="adjustBedModal" tabindex="-1" aria-labelledby="adjustBedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('report.rooming-list.adjust-bed') }}" id="adjustBedForm">
                @csrf
                <input type="hidden" name="hotel_name" id="modalHotelName">
                <input type="hidden" name="room_number" id="modalRoomNumber">
                <input type="hidden" name="room_type" id="modalRoomType">
                <input type="hidden" name="location" id="modalLocation">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="adjustBedModalLabel">
                        <i class="mdi mdi-bed-king me-2"></i>Adjust Bed Capacity for Room <span id="modalRoomDisplay" class="badge bg-white text-primary"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 mb-3 small">
                        <i class="mdi mdi-information-outline me-1"></i>
                        Hotel: <strong id="modalHotelDisplay"></strong> | Type: <strong id="modalTypeDisplay"></strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Total Bed Capacity for this Room <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-bed"></i></span>
                            <input type="number" name="bed_capacity" id="modalBedCapacity" class="form-control form-control-lg fw-bold" min="1" max="50" required>
                        </div>
                        <small class="text-muted">Set how many pilgrims can stay in this room (e.g., 2 for Double, 3 for Triple, 5 for Quint, 6 for Six/Sharing, or increase for extra beds).</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Designated Room Gender</label>
                        <select name="gender" id="modalGender" class="form-select">
                            <option value="Any">Any / Mixed</option>
                            <option value="Male">Male Room (Men)</option>
                            <option value="Female">Female Room (Women)</option>
                            <option value="Family">Family / Couple</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Extra Beds Added (Optional Note)</label>
                        <input type="number" name="extra_beds" id="modalExtraBeds" class="form-control" min="0" max="20" value="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Notes / Reason for bed adjustment</label>
                        <textarea name="notes" id="modalNotes" class="form-control" rows="2" placeholder="e.g. Extra mattress added for family"></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="mdi mdi-check me-1"></i> Save Room Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const adjustModalEl = document.getElementById('adjustBedModal');
        if (!adjustModalEl) return;
        const adjustModal = new bootstrap.Modal(adjustModalEl);

        document.querySelectorAll('.btn-adjust-bed').forEach(btn => {
            btn.addEventListener('click', function() {
                const hotel = this.getAttribute('data-hotel');
                const room = this.getAttribute('data-room');
                const type = this.getAttribute('data-type');
                const gender = this.getAttribute('data-gender') || 'Any';
                const loc = this.getAttribute('data-location');
                const cap = this.getAttribute('data-capacity');
                const extra = this.getAttribute('data-extra') || '0';
                const notes = this.getAttribute('data-notes') || '';

                document.getElementById('modalHotelName').value = hotel;
                document.getElementById('modalRoomNumber').value = room;
                document.getElementById('modalRoomType').value = type;
                document.getElementById('modalLocation').value = loc;

                document.getElementById('modalRoomDisplay').innerText = room;
                document.getElementById('modalHotelDisplay').innerText = hotel;
                document.getElementById('modalTypeDisplay').innerText = type || 'Standard';

                document.getElementById('modalBedCapacity').value = cap;
                document.getElementById('modalGender').value = gender;
                document.getElementById('modalExtraBeds').value = extra;
                document.getElementById('modalNotes').value = notes;

                adjustModal.show();
            });
        });
    });
</script>
@endsection
