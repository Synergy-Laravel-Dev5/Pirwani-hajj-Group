@extends('layout.master')
@section('title', 'Rooming List & Room Stock Management')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .metric-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .room-type-card {
        border-radius: 10px;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        padding: 16px;
        margin-bottom: 15px;
        transition: all 0.2s;
    }
    .room-type-card:hover {
        border-color: #001030;
        box-shadow: 0 3px 12px rgba(0,16,48,0.08);
    }
    .status-progress {
        height: 7px;
        border-radius: 4px;
        background-color: #e2e8f0;
        overflow: hidden;
    }
    .nav-inventory-tabs .nav-link {
        font-weight: 600;
        color: #64748b;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 12px 20px;
        font-size: 14px;
    }
    .nav-inventory-tabs .nav-link.active {
        color: #001030;
        border-bottom-color: #001030;
        background: transparent;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Page Title & Actions -->
            <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                <div>
                    <h4 class="fs-18 fw-semibold mb-1 text-primary">
                        <i class="mdi mdi-hotel me-1"></i> Rooming List & Room Stock Management
                    </h4>
                    <p class="text-muted fs-13 mb-0">
                        Hotel-wise room inventory control (Double, Triple, Quad, Sharing with Male/Female beds) and live rooming list.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('hotel-sales.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="mdi mdi-receipt me-1"></i> Hotel Sale Invoices
                    </a>
                    {{-- <a href="{{ route('report.rooming-list') }}" class="btn btn-outline-info btn-sm">
                        <i class="mdi mdi-bed-king me-1"></i> Room Allocation & Bed Report
                    </a> --}}
                    
                    <a href="{{ route('room-inventory.create') }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-plus-circle me-1"></i> + Add Room & Bed Stock Allotment
                    </a>
                </div>
            </div>

            <!-- Flash Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show fs-13" role="alert">
                    <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger fs-13">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Filter Card -->
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('room-inventory.index') }}">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fs-12 fw-semibold mb-1 text-muted">Filter by Hotel</label>
                                <select name="hotel_id" class="form-select form-select-sm">
                                    <option value="">-- All Hotels (Entire Stock) --</option>
                                    @foreach($hotels as $h)
                                        <option value="{{ $h->id }}" {{ ($hotelId == $h->id) ? 'selected' : '' }}>
                                            {{ $h->name }} @if($h->city || $h->place) ({{ $h->city ?? $h->place }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fs-12 fw-semibold mb-1 text-muted">City</label>
                                <select name="city" class="form-select form-select-sm">
                                    <option value="">-- All Cities --</option>
                                    <option value="Makkah" {{ ($city == 'Makkah') ? 'selected' : '' }}>Makkah</option>
                                    <option value="Madinah" {{ ($city == 'Madinah') ? 'selected' : '' }}>Madinah</option>
                                    <option value="Jeddah" {{ ($city == 'Jeddah') ? 'selected' : '' }}>Jeddah</option>
                                    <option value="Riyadh" {{ ($city == 'Riyadh') ? 'selected' : '' }}>Riyadh</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fs-12 fw-semibold mb-1 text-muted">Room Type</label>
                                <select name="room_type" class="form-select form-select-sm">
                                    <option value="">-- All Types --</option>
                                    @foreach(['Double', 'Triple', 'Quad', 'Quint', 'Single', 'Sharing', 'Suite'] as $rt)
                                        <option value="{{ $rt }}" {{ ($roomType == $rt) ? 'selected' : '' }}>{{ $rt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fs-12 fw-semibold mb-1 text-muted">Check In From</label>
                                <input type="text" name="check_in" class="form-control form-control-sm flatpickr-date"
                                    placeholder="Y-m-d" value="{{ $checkIn }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fs-12 fw-semibold mb-1 text-muted">Check Out To</label>
                                <input type="text" name="check_out" class="form-control form-control-sm flatpickr-date"
                                    placeholder="Y-m-d" value="{{ $checkOut }}">
                            </div>
                            <div class="col-md-1 d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm w-100" title="Search">
                                    <i class="mdi mdi-filter"></i>
                                </button>
                                <a href="{{ route('room-inventory.index') }}" class="btn btn-light btn-sm" title="Reset">
                                    <i class="mdi mdi-refresh"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Top Metric Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="card metric-card bg-white p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fs-12 text-muted fw-semibold text-uppercase">Total Stock (Rooms + Beds)</span>
                                <h3 class="fs-22 fw-bold text-dark mb-0 mt-1">{{ number_format($totalStock) }}</h3>
                                <small class="text-primary fw-semibold fs-11">
                                    {{ number_format($totalRoomStock ?? 0) }} Rooms • {{ number_format($totalBedStock ?? 0) }} Beds
                                </small>
                            </div>
                            <div class="avatar-sm bg-soft-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-warehouse fs-22 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card metric-card bg-white p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fs-12 text-muted fw-semibold text-uppercase">Booked (Rooms + Beds)</span>
                                <h3 class="fs-22 fw-bold text-danger mb-0 mt-1">{{ number_format($totalBooked) }}</h3>
                                <small class="text-danger fw-semibold fs-11">
                                    {{ number_format($totalRoomBooked ?? 0) }} Rooms • {{ number_format($totalBedBooked ?? 0) }} Beds
                                </small>
                            </div>
                            <div class="avatar-sm bg-soft-danger rounded-circle d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-calendar-check fs-22 text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card metric-card bg-white p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fs-12 text-muted fw-semibold text-uppercase">Available Remaining</span>
                                <h3 class="fs-22 fw-bold text-success mb-0 mt-1">{{ number_format($totalAvailable) }}</h3>
                                <small class="text-success fw-semibold fs-11">
                                    {{ number_format($totalRoomAvailable ?? 0) }} Rooms • {{ number_format($totalBedAvailable ?? 0) }} Beds
                                </small>
                            </div>
                            <div class="avatar-sm bg-soft-success rounded-circle d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-bed-empty fs-22 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card metric-card bg-white p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fs-12 text-muted fw-semibold text-uppercase">Overall Occupancy</span>
                                <h3 class="fs-22 fw-bold text-info mb-0 mt-1">{{ $overallOccupancy }}%</h3>
                                <small class="text-muted fs-11">Rooms & Beds utilization</small>
                            </div>
                            <div class="avatar-sm bg-soft-info rounded-circle d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-chart-donut fs-22 text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ROOM SUMMARY BREAKDOWN (Double, Triple, Quad, Sharing, etc.) -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fs-15 fw-semibold text-primary mb-0">
                            <i class="mdi mdi-chart-box-outline me-1"></i> Room Type Stock Summary
                        </h5>
                        <span class="fs-12 text-muted">Stock availability & booked count for Double, Triple, Quad, Sharing, and all room types</span>
                    </div>
                    @if($hotelId)
                        @php $currentH = $hotels->firstWhere('id', $hotelId); @endphp
                        <span class="badge bg-soft-primary text-primary fs-12">
                            <i class="mdi mdi-hotel me-1"></i> Showing: {{ $currentH->name ?? 'Selected Hotel' }}
                        </span>
                    @else
                        <span class="badge bg-soft-secondary text-secondary fs-12">
                            <i class="mdi mdi-earth me-1"></i> All Hotels Combined
                        </span>
                    @endif
                </div>
                <div class="card-body p-3">
                    <div class="row g-3">
                        @foreach($summary as $rType => $data)
                            @php
                                $badgeClass = 'bg-success';
                                if ($data['total_stock'] == 0) {
                                    $badgeClass = 'bg-secondary';
                                } elseif ($data['available'] == 0) {
                                    $badgeClass = 'bg-danger';
                                } elseif ($data['percentage'] >= 80) {
                                    $badgeClass = 'bg-warning text-dark';
                                }
                            @endphp
                            <div class="col-md-3 col-sm-6">
                                <div class="room-type-card {{ !empty($data['is_bed']) ? 'border-warning' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fs-14 fw-bold mb-0 text-dark">
                                            @if(!empty($data['is_bed']))
                                                <i class="mdi mdi-bed-empty me-1 text-warning"></i> BED (Beds)
                                            @else
                                                <i class="mdi mdi-bed me-1 text-primary"></i> {{ $rType }}
                                            @endif
                                        </h6>
                                        <span class="badge {{ $badgeClass }} fs-11">
                                            @if($data['total_stock'] == 0)
                                                No Stock
                                            @elseif($data['available'] == 0)
                                                Sold Out
                                            @else
                                                {{ $data['available'] }} Avail
                                            @endif
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between fs-12 mb-1">
                                        <span class="text-muted">Total Stock: <strong class="text-dark">{{ $data['total_stock'] }}</strong></span>
                                        <span class="text-muted">Sold (Inv): <strong class="text-danger">{{ $data['booked'] }}</strong></span>
                                    </div>
                                    <div class="status-progress mb-2">
                                        <div class="progress-bar {{ ($data['available'] == 0 && $data['total_stock'] > 0) ? 'bg-danger' : (!empty($data['is_bed']) ? 'bg-warning' : 'bg-primary') }}"
                                            style="width: {{ $data['percentage'] }}%;"></div>
                                    </div>
                                    <div class="d-flex justify-content-between fs-11 text-muted">
                                        <span>Remaining: <strong class="text-success">{{ $data['available'] }}</strong></span>
                                        <span>Sold: <strong>{{ $data['percentage'] }}%</strong></span>
                                    </div>
                                    @if((strtolower($rType) === 'sharing' || !empty($data['is_bed'])) && (!empty($data['male_stock']) || !empty($data['female_stock'])))
                                        <div class="fs-11 text-muted mt-1 pt-1 border-top d-flex justify-content-between">
                                            <span><i class="mdi mdi-gender-male text-primary"></i> M: <strong>{{ $data['male_available'] }}/{{ $data['male_stock'] }}</strong></span>
                                            <span><i class="mdi mdi-gender-female text-danger"></i> F: <strong>{{ $data['female_available'] }}/{{ $data['female_stock'] }}</strong></span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- MAIN TABS: HOTEL SALE CLIENTS & INVENTORIES -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white p-0 border-bottom">
                    <ul class="nav nav-inventory-tabs" id="roomInventoryTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="rooming-list-tab" data-bs-toggle="tab"
                                data-bs-target="#rooming-list-pane" type="button" role="tab">
                                <i class="mdi mdi-receipt me-1"></i> 1. Hotel Sale Clients & Agents (Sold Invoices)
                                <span class="badge bg-danger rounded-pill ms-1">{{ count($soldItems) }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="allotments-tab" data-bs-toggle="tab"
                                data-bs-target="#allotments-pane" type="button" role="tab">
                                <i class="mdi mdi-warehouse me-1"></i> 2. Purchased Stock Allotments & Inventory
                                <span class="badge bg-secondary rounded-pill ms-1">{{ count($inventories) }}</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-3">
                    <div class="tab-content" id="roomInventoryContent">

                        <!-- TAB 1: HOTEL SALE CLIENTS & AGENTS (SOLD STOCK) -->
                        <div class="tab-pane fade show active" id="rooming-list-pane" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="fs-14 fw-semibold text-dark mb-0">
                                        <i class="mdi mdi-file-document-check-outline text-primary me-1"></i> Hotel Reservations & Sale Invoices
                                    </h6>
                                    <span class="fs-12 text-muted">Stock sold to Agents (B2B) and Clients (B2C) through Hotel Sale Invoices</span>
                                </div>
                                <a href="{{ route('hotel-sales.create') }}" class="btn btn-primary btn-sm">
                                    <i class="mdi mdi-plus-circle me-1"></i> + New Hotel Sale Invoice
                                </a>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered table-sm align-middle fs-13 mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Date</th>
                                            <th>Agent / Client</th>
                                            <th>Hotel Reserved</th>
                                            <th>Type / Basis</th>
                                            <th class="text-center">Sold Qty</th>
                                            <th>Stay Dates (Hijri & Cal)</th>
                                            <th class="text-center">Nights</th>
                                            <th class="text-end">Rate</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($soldItems as $item)
                                            @php $sale = $item->hotelSale; @endphp
                                            <tr>
                                                <td>
                                                    <a href="{{ route('hotel-sales.show', $item->hotel_sale_id) }}" class="fw-bold text-primary">
                                                        {{ $sale->invoice_number ?? ('#HR-' . $item->hotel_sale_id) }}
                                                    </a>
                                                </td>
                                                <td>{{ $sale->invoice_date ? $sale->invoice_date->format('d-M-Y') : '-' }}</td>
                                                <td>
                                                    <strong>{{ $sale->customer_name ?? 'N/A' }}</strong>
                                                    <div class="fs-11 text-muted">
                                                        @if(($sale->sale_for ?? '') === 'company')
                                                            <span class="badge bg-soft-primary text-primary">Agent / B2B</span>
                                                        @else
                                                            <span class="badge bg-soft-info text-info">Client / B2C</span>
                                                        @endif
                                                        @if(!empty($sale->contact)) • {{ $sale->contact }} @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold text-dark">{{ $item->hotel_name }}</span>
                                                </td>
                                                <td>
                                                    @if($item->allotment_basis === 'bed' || strtoupper($item->room_type) === 'BED')
                                                        <span class="badge bg-warning text-dark border fs-11">BED BASIS</span>
                                                    @else
                                                        <span class="badge bg-soft-primary text-primary fs-11">{{ $item->room_type }}</span>
                                                    @endif
                                                    <div class="fs-10 text-muted">{{ $item->meal_plan ?: 'ROOM ONLY' }}</div>
                                                </td>
                                                <td class="text-center">
                                                    @if($item->allotment_basis === 'bed' || strtoupper($item->room_type) === 'BED')
                                                        <span class="badge bg-warning text-dark fs-12 px-2 py-1">
                                                            {{ $item->quantity }} Beds
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger fs-12 px-2 py-1">
                                                            {{ $item->quantity }} Room(s)
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->hijri_check_in || $item->hijri_check_out)
                                                        <div class="fw-bold text-primary fs-11">{{ $item->hijri_check_in }} → {{ $item->hijri_check_out }}</div>
                                                    @endif
                                                    @if($item->check_in || $item->check_out)
                                                        <small class="text-muted fs-11">
                                                            {{ $item->check_in ? $item->check_in->format('d-M') : 'Open' }} to {{ $item->check_out ? $item->check_out->format('d-M-Y') : 'Open' }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td class="text-center fw-semibold">{{ $item->no_of_nights ?: 1 }}</td>
                                                <td class="text-end">{{ $item->rate_per_unit > 0 ? number_format($item->rate_per_unit, 2) : '-' }}</td>
                                                <td class="text-end fw-bold text-primary">{{ number_format($item->amount, 2) }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('hotel-sales.show', $item->hotel_sale_id) }}" class="btn btn-xs btn-outline-primary" title="View / Print Invoice Voucher">
                                                        <i class="mdi mdi-printer"></i> Voucher
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center py-4 text-muted">
                                                    <i class="mdi mdi-information-outline fs-22 d-block mb-1"></i>
                                                    No hotel sale invoices found.
                                                    <div class="mt-2">
                                                        <a href="{{ route('hotel-sales.create') }}" class="btn btn-primary btn-sm">
                                                            <i class="mdi mdi-plus-circle me-1"></i> Create Hotel Sale Invoice
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 2: INVENTORY / ALLOTMENTS -->
                        <div class="tab-pane fade" id="allotments-pane" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fs-14 fw-semibold text-dark mb-0">
                                    <i class="mdi mdi-warehouse text-primary me-1"></i> Configured Room Stock Allotments
                                </h6>
                                <a href="{{ route('room-inventory.create') }}" class="btn btn-primary btn-sm">
                                    <i class="mdi mdi-plus-circle me-1"></i> + Add New Stock
                                </a>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered table-sm align-middle fs-13 mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Hotel Name</th>
                                            <th>Batch / Reference</th>
                                            <th>Room Type</th>
                                            <th>View / Meals</th>
                                            <th>Valid Period (Dates)</th>
                                            <th class="text-center">Total Stock</th>
                                            <th class="text-center">Booked</th>
                                            <th class="text-center">Remaining</th>
                                            <th>Supplier</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($inventories as $inv)
                                            @php
                                                $summaryType = $summary[$inv->room_type] ?? null;
                                                $availCount = $summaryType ? $summaryType['available'] : $inv->total_rooms;
                                                $bookedCount = $summaryType ? $summaryType['booked'] : 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $inv->hotel->name ?? 'Hotel #' . $inv->hotel_id }}</strong>
                                                    @if($inv->hotel && ($inv->hotel->city || $inv->hotel->place))
                                                        <br><small class="text-muted">{{ $inv->hotel->city ?? $inv->hotel->place }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $inv->batch_name ?: 'General Stock' }}</td>
                                                <td>
                                                    @if($inv->allotment_basis === 'bed' || strtoupper($inv->room_type) === 'BED')
                                                        <span class="badge bg-soft-warning text-dark border fs-11">
                                                            <i class="mdi mdi-bed-empty me-1"></i> BED BASIS
                                                        </span>
                                                    @else
                                                        <span class="badge bg-soft-primary text-primary fs-11">{{ $inv->room_type }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="fs-11">{{ $inv->room_view ?: 'City View' }}</span>
                                                    @if($inv->meal_plan)
                                                        <br><small class="text-primary fw-semibold">{{ $inv->meal_plan }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($inv->hijri_check_in || $inv->hijri_check_out)
                                                        <div class="fw-bold text-primary fs-11">{{ $inv->hijri_check_in }} → {{ $inv->hijri_check_out }}</div>
                                                    @endif
                                                    @if($inv->check_in && $inv->check_out)
                                                        <span class="fs-11 text-muted">{{ date('d M Y', strtotime($inv->check_in)) }} - {{ date('d M Y', strtotime($inv->check_out)) }}</span>
                                                    @elseif(!$inv->hijri_check_in)
                                                        <span class="badge bg-light text-dark fs-11">Open / Perpetual</span>
                                                    @endif
                                                </td>
                                                <td class="text-center fw-bold">
                                                    @if($inv->allotment_basis === 'bed' || strtoupper($inv->room_type) === 'BED')
                                                        <span class="text-warning">{{ $inv->total_beds ?: $inv->total_rooms }} Beds</span>
                                                    @else
                                                        <span>{{ $inv->total_rooms }} Rooms</span>
                                                    @endif
                                                </td>
                                                <td class="text-center text-danger fw-semibold">{{ $bookedCount }}</td>
                                                <td class="text-center">
                                                    <span class="badge {{ $availCount > 0 ? 'bg-success' : 'bg-danger' }} fs-11">
                                                        {{ $availCount }} Avail
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($inv->supplier)
                                                        <a href="{{ route('supplier.show', $inv->supplier->id) }}" class="fw-semibold text-dark">
                                                            {{ $inv->supplier->name }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge {{ $inv->status === 'active' ? 'bg-success' : 'bg-secondary' }} fs-10 text-uppercase">
                                                        {{ $inv->status }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('room-inventory.edit', $inv->id) }}" class="btn btn-outline-primary" title="Edit Allotment">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('room-inventory.delete', $inv->id) }}" method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this room allotment?');" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger" title="Delete Allotment">
                                                                <i class="mdi mdi-trash-can-outline"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center py-4 text-muted">
                                                    <i class="mdi mdi-warehouse fs-22 d-block mb-1"></i>
                                                    No room stock allotments added yet. Click "+ Add Room Stock Allotment" to add hotel room stocks.
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        flatpickr(".flatpickr-date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "m/d/Y",
            allowInput: true
        });
    });
</script>
@endsection
