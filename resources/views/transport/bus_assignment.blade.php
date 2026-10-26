@extends('layout.master')
@section('title', 'Bus Assignment & Route Schedule')

@section('content')
<style>
    .bus-seat-card {
        border-radius: 8px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .bus-seat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    }
    .bus-card-active {
        border: 2px solid #0d6efd !important;
        background-color: #f0f7ff !important;
    }
    .manifest-table {
        border-collapse: collapse;
        width: 100%;
        background-color: #ffffff;
        font-family: Arial, sans-serif;
    }
    .manifest-table th {
        background-color: #0f172a;
        color: #fff;
        font-weight: 700;
        font-size: 12.5px;
        text-align: center;
        vertical-align: middle;
        padding: 8px 6px;
        border: 1px solid #334155 !important;
        text-transform: uppercase;
    }
    .manifest-table td {
        padding: 7px 6px;
        vertical-align: middle;
        font-size: 13px;
        border: 1px solid #cbd5e1 !important;
    }
    .seat-badge {
        font-size: 13px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 6px;
        background-color: #0f172a;
        color: #fff;
    }
    .gender-male { color: #1e3a8a; font-weight: 700; }
    .gender-female { color: #9d174d; font-weight: 700; }
    .progress-bar-bus {
        height: 8px;
        border-radius: 4px;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            {{-- Page Header --}}
            <div class="py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h4 class="fs-20 fw-bold m-0 text-dark">
                        <i class="mdi mdi-bus-clock text-primary me-2"></i>Bus Assignment & Route Schedule
                    </h4>
                    <p class="text-muted mb-0 small">Assign pilgrims to sector buses, manage seat numbers, and print official Bus Passenger Manifests.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddBus">
                        <i class="mdi mdi-plus-circle me-1"></i> Add / Register New Bus
                    </button>
                    @if($activeBus)
                        <a href="{{ route('transport.bus-assignment.export-pdf', ['bus_id' => $activeBus->id]) }}" 
                           class="btn btn-danger btn-sm" title="Download Official Passenger Manifest PDF">
                            <i class="mdi mdi-file-pdf-box me-1"></i> Download Bus Manifest (PDF)
                        </a>
                        <a href="{{ route('transport.bus-assignment.export-excel', ['bus_id' => $activeBus->id]) }}" 
                           class="btn btn-success btn-sm" title="Download Manifest Excel">
                            <i class="mdi mdi-file-excel me-1"></i> Download Excel
                        </a>
                    @endif
                </div>
            </div>

            {{-- Sector Selector Bar --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('transport.bus-assignment') }}" id="sectorForm">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-5">
                                <label class="form-label small fw-bold text-muted mb-1">
                                    <i class="mdi mdi-map-marker-path text-primary me-1"></i>Select Route / Sector:
                                </label>
                                <select name="sector" class="form-select form-select-sm fw-bold text-dark" onchange="document.getElementById('sectorForm').submit()">
                                    @foreach($sectors as $secKey => $secLabel)
                                        <option value="{{ $secKey }}" {{ $selectedSector === $secKey ? 'selected' : '' }}>
                                            🚌 {{ $secLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-7 d-flex flex-wrap gap-2 justify-content-md-end align-items-center pt-md-3">
                                <span class="badge bg-light text-dark border p-2 fs-12">
                                    <i class="mdi mdi-account-group text-primary me-1"></i>Total Pilgrims: <strong>{{ number_format($totalPilgrimsInSystem) }}</strong>
                                </span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle p-2 fs-12">
                                    <i class="mdi mdi-check-circle me-1"></i>Assigned in Sector: <strong>{{ number_format($totalAssignedInSector) }}</strong>
                                </span>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle p-2 fs-12">
                                    <i class="mdi mdi-account-clock me-1"></i>Unassigned: <strong>{{ number_format($totalUnassignedInSector) }}</strong>
                                </span>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle p-2 fs-12">
                                    <i class="mdi mdi-bus me-1"></i>Buses: <strong>{{ $totalBusesCount }}</strong> ({{ $totalBusCapacity }} Seats)
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show p-2 px-3 small" role="alert">
                    <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close p-2" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show p-2 px-3 small" role="alert">
                    <i class="mdi mdi-alert-circle-outline me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close p-2" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Buses Ribbon for Active Sector --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark fs-14">
                        <i class="mdi mdi-bus me-1 text-primary"></i> Buses for Sector: <span class="text-primary">{{ $selectedSector }}</span>
                    </h6>
                    <button type="button" class="btn btn-outline-primary btn-xs py-1 px-2" data-bs-toggle="modal" data-bs-target="#modalAddBus">
                        <i class="mdi mdi-plus"></i> Add Another Bus
                    </button>
                </div>
                <div class="card-body p-3">
                    @if($buses->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="mdi mdi-bus-stop fs-36 d-block text-secondary mb-2"></i>
                            <h6 class="fw-bold">No Buses Registered Yet for this Sector</h6>
                            <p class="small mb-3">Register Bus #1 or Coasters to begin assigning pilgrims and printing manifests.</p>
                            <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalAddBus">
                                <i class="mdi mdi-plus-circle me-1"></i> Add First Bus (e.g. Bus #1)
                            </button>
                        </div>
                    @else
                        <div class="row g-2">
                            @foreach($buses as $b)
                                @php
                                    $paxCount = $b->groupPersons->count();
                                    $cap = intval($b->bus_capacity ?: 45);
                                    $pct = $cap > 0 ? min(100, round(($paxCount / $cap) * 100)) : 0;
                                    $isCurrent = ($activeBus && $activeBus->id === $b->id);
                                    $freeSeats = max(0, $cap - $paxCount);
                                    $barColor = $pct >= 100 ? 'bg-danger' : ($pct >= 80 ? 'bg-warning' : 'bg-success');
                                @endphp
                                <div class="col-xl-3 col-md-6">
                                    <a href="{{ route('transport.bus-assignment', ['sector' => $selectedSector, 'bus_id' => $b->id]) }}" 
                                       class="text-decoration-none d-block">
                                        <div class="p-3 rounded border bus-seat-card {{ $isCurrent ? 'bus-card-active shadow' : 'bg-white shadow-sm' }} h-100">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <span class="fw-bold fs-15 text-dark d-block">
                                                        <i class="mdi mdi-bus text-primary me-1"></i>{{ $b->group_name }}
                                                    </span>
                                                    <small class="text-muted d-block" style="font-size: 11px;">
                                                        Driver: {{ $b->driver_name ?: 'Not Assigned' }} {{ $b->driver_phone ? '(' . $b->driver_phone . ')' : '' }}
                                                    </small>
                                                </div>
                                                <span class="badge {{ $pct >= 100 ? 'bg-danger' : 'bg-primary' }} px-2 py-1" style="font-size: 11px;">
                                                    {{ $paxCount }} / {{ $cap }} Seats
                                                </span>
                                            </div>

                                            <div class="progress progress-bar-bus bg-light mb-2">
                                                <div class="progress-bar {{ $barColor }}" style="width: {{ $pct }}%"></div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center" style="font-size: 11px;">
                                                <span class="text-success fw-bold">{{ $freeSeats }} Available Seats</span>
                                                @if(!empty($b->plate_number))
                                                    <span class="badge bg-secondary-subtle text-secondary px-1.5 py-0.5">Plate: {{ $b->plate_number }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Main Assignment Workspace --}}
            @if($activeBus)
                <div class="row g-3">
                    {{-- Left Column: Active Bus Passengers Manifest --}}
                    <div class="col-xl-7 col-lg-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white py-2 px-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <h6 class="m-0 fw-bold text-dark fs-14">
                                        <i class="mdi mdi-account-group me-1 text-primary"></i> {{ $activeBus->group_name }} — Passenger Manifest
                                    </h6>
                                    <small class="text-muted">
                                        {{ $activeBus->groupPersons->count() }} passengers assigned &bull; Capacity: {{ $activeBus->bus_capacity }} seats &bull; Available: {{ max(0, $activeBus->bus_capacity - $activeBus->groupPersons->count()) }}
                                    </small>
                                </div>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('transport.bus-assignment.export-pdf', ['bus_id' => $activeBus->id]) }}" 
                                       class="btn btn-danger btn-xs py-1 px-2" title="Download PDF Manifest">
                                        <i class="mdi mdi-file-pdf-box me-0.5"></i> PDF
                                    </a>
                                    <a href="{{ route('transport.bus-assignment.export-excel', ['bus_id' => $activeBus->id]) }}" 
                                       class="btn btn-success btn-xs py-1 px-2" title="Download Excel Manifest">
                                        <i class="mdi mdi-file-excel me-0.5"></i> Excel
                                    </a>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                @php
                                    $assignedPassengers = $activeBus->groupPersons->sortBy(function($gp) {
                                        return is_numeric($gp->seat_number) ? intval($gp->seat_number) : 9999;
                                    })->values();
                                @endphp

                                @if($assignedPassengers->isEmpty())
                                    <div class="text-center py-5 text-muted">
                                        <i class="mdi mdi-account-off fs-36 text-secondary d-block mb-2"></i>
                                        <h6 class="fw-bold">No Pilgrims Assigned to this Bus Yet</h6>
                                        <p class="small text-muted mb-0">Select pilgrims from the unassigned list on the right and click "Assign to Bus" to allocate seats.</p>
                                    </div>
                                @else
                                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                                        <table class="manifest-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 60px;">SEAT #</th>
                                                    <th style="width: 80px;">HAJJ ID</th>
                                                    <th style="width: 75px;">HB #</th>
                                                    <th style="min-width: 150px; text-align: left; padding-left: 8px;">PILGRIM FULL NAME</th>
                                                    <th style="width: 65px;">GENDER</th>
                                                    <th style="width: 95px;">PASSPORT</th>
                                                    <th style="min-width: 110px;">PACKAGE</th>
                                                    <th style="width: 80px;">CAMP</th>
                                                    <th style="width: 50px;">ACTION</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($assignedPassengers as $idx => $gp)
                                                    @php
                                                        $p = $gp->person;
                                                        $b = $gp->booking;
                                                        $campStr = $b?->camp ?: ($b?->package?->camp_category ? 'Maktab ' . $b->package->camp_category : '—');
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="seat-badge">{{ $gp->seat_number ?: ($idx + 1) }}</span>
                                                        </td>
                                                        <td class="text-center fw-bold">{{ $p?->hajj_id ?: '—' }}</td>
                                                        <td class="text-center fw-bold">{{ $p?->hb_number ?: '—' }}</td>
                                                        <td style="text-align: left; padding-left: 8px;">
                                                            <strong class="text-dark d-block">{{ $p?->full_name ?? ($b?->client?->name ?? 'Guest') }}</strong>
                                                            <small class="text-muted" style="font-size: 11px;">
                                                                Voucher: {{ $b?->voucher_number ?? '—' }}
                                                            </small>
                                                        </td>
                                                        <td class="text-center {{ strtoupper($p?->gender ?? '') === 'FEMALE' ? 'gender-female' : 'gender-male' }}">
                                                            {{ strtoupper($p?->gender ?? '—') }}
                                                        </td>
                                                        <td class="text-center fw-bold">{{ $p?->passport_number ?? ($b?->passport_number ?? '—') }}</td>
                                                        <td class="small">{{ $b?->package?->package_title ?: ($b?->package?->name ?: '—') }}</td>
                                                        <td class="text-center small">{{ $campStr }}</td>
                                                        <td class="text-center">
                                                            <form method="POST" action="{{ route('transport.bus-assignment.unassign') }}" onsubmit="return confirm('Remove pilgrim from this bus?')" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="haji_group_person_ids[]" value="{{ $gp->id }}">
                                                                <button type="submit" class="btn btn-xs btn-outline-danger p-1" title="Unassign from bus">
                                                                    <i class="mdi mdi-close"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Unassigned Pilgrims Selector --}}
                    <div class="col-xl-5 col-lg-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="m-0 fw-bold text-dark fs-14">
                                        <i class="mdi mdi-account-plus me-1 text-success"></i> Unassigned Pilgrims Pool
                                    </h6>
                                    <small class="text-muted">{{ $unassignedPilgrims->count() }} pilgrims waiting for bus allocation</small>
                                </div>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                    {{ $unassignedPilgrims->count() }} Available
                                </span>
                            </div>

                            <div class="card-body p-3">
                                {{-- Quick Filter Controls --}}
                                <form method="GET" action="{{ route('transport.bus-assignment') }}" class="mb-2">
                                    <input type="hidden" name="sector" value="{{ $selectedSector }}">
                                    <input type="hidden" name="bus_id" value="{{ $activeBus->id }}">
                                    <div class="row g-1">
                                        <div class="col-7">
                                            <div class="input-group input-group-sm">
                                                <input type="text" name="search" class="form-control" placeholder="Search name, passport, HB#..." value="{{ request('search') }}">
                                                <button class="btn btn-outline-secondary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <select name="gender" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="all">All Genders</option>
                                                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male Only</option>
                                                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female Only</option>
                                            </select>
                                        </div>
                                    </div>
                                </form>

                                {{-- Allocation Form --}}
                                <form method="POST" action="{{ route('transport.bus-assignment.assign') }}" id="assignPilgrimsForm">
                                    @csrf
                                    <input type="hidden" name="bus_id" value="{{ $activeBus->id }}">

                                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded border">
                                        <div class="form-check form-check-inline m-0">
                                            <input class="form-check-input" type="checkbox" id="selectAllPilgrims">
                                            <label class="form-check-label fw-bold small text-dark" for="selectAllPilgrims">Select All</label>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <label class="small text-muted mb-0">Start Seat:</label>
                                            @php
                                                $nextSeat = ($assignedPassengers->count() > 0) ? ($assignedPassengers->count() + 1) : 1;
                                            @endphp
                                            <input type="number" name="starting_seat" class="form-control form-control-sm text-center fw-bold" style="width: 60px;" value="{{ $nextSeat }}" min="1">
                                        </div>
                                    </div>

                                    {{-- Unassigned Pilgrims List --}}
                                    <div class="overflow-auto border rounded p-1 mb-3" style="max-height: 440px;">
                                        @forelse($unassignedPilgrims as $up)
                                            @php
                                                $b = $up->booking;
                                            @endphp
                                            <label class="d-flex align-items-center gap-2 p-2 border-bottom hover-bg-light cursor-pointer mb-0">
                                                <input type="checkbox" name="person_ids[]" value="{{ $up->id }}" class="form-check-input pilgrim-checkbox mt-0">
                                                <div class="flex-grow-1" style="line-height: 1.3;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <strong class="text-dark fs-13">{{ $up->full_name }}</strong>
                                                        <span class="badge {{ strtoupper($up->gender ?? '') === 'FEMALE' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }}" style="font-size: 10px;">
                                                            {{ strtoupper($up->gender ?: 'M') }}
                                                        </span>
                                                    </div>
                                                    <div class="d-flex flex-wrap gap-2 text-muted small" style="font-size: 11px;">
                                                        @if(!empty($up->hb_number)) <span>HB: <strong>{{ $up->hb_number }}</strong></span> @endif
                                                        @if(!empty($up->hajj_id)) <span>Hajj ID: <strong>{{ $up->hajj_id }}</strong></span> @endif
                                                        <span>Pass: <strong>{{ $up->passport_number ?: '—' }}</strong></span>
                                                        <span>Pkg: {{ $b?->package?->package_title ?: ($b?->package?->name ?: '—') }}</span>
                                                    </div>
                                                </div>
                                            </label>
                                        @empty
                                            <div class="text-center py-4 text-muted small">
                                                <i class="mdi mdi-check-all fs-24 text-success d-block mb-1"></i>
                                                All pilgrims are assigned to buses for this sector!
                                            </div>
                                        @endforelse
                                    </div>

                                    @php
                                        $freeSeats = max(0, intval($activeBus->bus_capacity) - $activeBus->groupPersons->count());
                                    @endphp

                                    @if($unassignedPilgrims->isNotEmpty())
                                        <button type="submit" class="btn btn-primary w-100 fw-bold" id="btnAssignSubmit" {{ $freeSeats <= 0 ? 'disabled' : '' }}>
                                            <i class="mdi mdi-bus-side me-1"></i> Assign Selected Pilgrims to {{ $activeBus->group_name }}
                                        </button>
                                        @if($freeSeats <= 0)
                                            <small class="text-danger d-block text-center mt-1">Bus capacity full! Register another bus or increase capacity.</small>
                                        @endif
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

{{-- Modal: Add / Register New Bus --}}
<div class="modal fade" id="modalAddBus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2 px-3">
                <h6 class="modal-title fw-bold">
                    <i class="mdi mdi-bus me-1"></i> Register New Bus / Vehicle
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('transport.bus-assignment.create-bus') }}">
                @csrf
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted mb-1">Route / Sector</label>
                        <select name="route_name" class="form-select form-select-sm fw-bold">
                            @foreach($sectors as $secKey => $secLabel)
                                <option value="{{ $secKey }}" {{ $selectedSector === $secKey ? 'selected' : '' }}>
                                    {{ $secLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-8">
                            <label class="form-label small fw-bold text-muted mb-1">Bus / Vehicle Name <span class="text-danger">*</span></label>
                            @php
                                $nextBusNumber = ($totalBusesCount > 0) ? 'Bus #' . ($totalBusesCount + 1) : 'Bus #1';
                            @endphp
                            <input type="text" name="bus_name" class="form-control form-control-sm" placeholder="e.g. Bus #1, VIP Coaster" value="{{ $nextBusNumber }}" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-bold text-muted mb-1">Capacity (Seats) <span class="text-danger">*</span></label>
                            <input type="number" name="bus_capacity" class="form-control form-control-sm fw-bold text-center" value="45" min="1" max="100" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted mb-1">Driver Name</label>
                            <input type="text" name="driver_name" class="form-control form-control-sm" placeholder="Driver Full Name">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted mb-1">Driver Mobile / WhatsApp</label>
                            <input type="text" name="driver_phone" class="form-control form-control-sm" placeholder="+966-50-xxx">
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted mb-1">Vehicle Plate Number</label>
                            <input type="text" name="plate_number" class="form-control form-control-sm" placeholder="e.g. 1234 KSA">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted mb-1">Registered Vehicle (Optional)</label>
                            <select name="vehicle_id" class="form-select form-select-sm">
                                <option value="">-- Choose from Fleet --</option>
                                @foreach($vehicles as $veh)
                                    <option value="{{ $veh->id }}">{{ $veh->brand_name }} ({{ $veh->plate_number }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted mb-1">Departure Date</label>
                            <input type="date" name="departure_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted mb-1">Departure Time</label>
                            <input type="time" name="departure_time" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted mb-1">Notes / Instructions</label>
                        <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="e.g. Meeting point, luggage instructions..."></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">
                        <i class="mdi mdi-check me-1"></i> Create Bus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCb = document.getElementById('selectAllPilgrims');
        const pilgrimCbs = document.querySelectorAll('.pilgrim-checkbox');

        if (selectAllCb) {
            selectAllCb.addEventListener('change', function () {
                pilgrimCbs.forEach(cb => {
                    cb.checked = selectAllCb.checked;
                });
            });
        }
    });
</script>
@endsection
