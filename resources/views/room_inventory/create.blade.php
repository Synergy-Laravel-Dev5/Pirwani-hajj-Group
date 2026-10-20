@extends('layout.master')
@section('title', 'Add Room & Bed Stock Allotment')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .room-input-row, .contract-row {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        transition: all 0.2s ease;
    }
    .room-input-row:hover, .contract-row:hover {
        background: #ffffff;
        border-color: #001030;
        box-shadow: 0 2px 8px rgba(0,16,48,0.06);
    }
    .nav-pills .nav-link.active {
        background-color: #001030;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                <div>
                    <h4 class="fs-18 fw-semibold mb-1 text-primary">
                        <i class="mdi mdi-plus-circle me-1"></i> Add Room & Bed Stock Allotment
                    </h4>
                    <p class="text-muted fs-13 mb-0">
                        Record purchased hotel inventory on <strong>Room Basis</strong> (Double, Triple, Quad) or <strong>Bed Basis</strong> (e.g. 545 Beds in Madinah) with Islamic (Zilqaida / Zilhajj) dates.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('hotel-sales.index') }}" class="btn btn-outline-info btn-sm">
                        <i class="mdi mdi-receipt me-1"></i> Hotel Sale Invoices
                    </a>
                    <a href="{{ route('room-inventory.index') }}" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left me-1"></i> Back to Rooming List & Stock
                    </a>
                </div>
            </div>

            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger fs-13">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Mode Selector Tabs -->
            <ul class="nav nav-pills mb-3" id="allotmentTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fs-13 fw-semibold" id="tab-contract-mode" data-bs-toggle="pill" data-bs-target="#contractModePane" type="button" role="tab">
                        <i class="mdi mdi-file-table-box-multiple-outline me-1"></i> Mode A: Multi-Hotel Contract Batch (Sheet Style)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fs-13 fw-semibold" id="tab-single-mode" data-bs-toggle="pill" data-bs-target="#singleModePane" type="button" role="tab">
                        <i class="mdi mdi-hotel me-1"></i> Mode B: Single Hotel Stock Entry
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="allotmentTabsContent">

                <!-- ══════════════════════════════════════════════════════════════════ -->
                <!-- PANE 1: MULTI-HOTEL CONTRACT BATCH (MATCHES SHEET EXACTLY)        -->
                <!-- ══════════════════════════════════════════════════════════════════ -->
                <div class="tab-pane fade show active" id="contractModePane" role="tabpanel">
                    <form action="{{ route('room-inventory.store') }}" method="POST" id="contractStockForm">
                        @csrf

                        <!-- Batch Meta Card -->
                        <div class="card mb-3 border-0 shadow-sm">
                            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                <h5 class="fs-15 fw-semibold text-primary mb-0">
                                    <i class="mdi mdi-folder-outline me-1"></i> Contract & Batch Information
                                </h5>
                                <button type="button" class="btn btn-outline-info btn-sm" id="loadSampleContractBtn">
                                    <i class="mdi mdi-lightning-bolt me-1"></i> Load Sample Hajj Contract (Pullman, Swissôtel, Nusk 545 Beds)
                                </button>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label class="form-label fs-13 fw-semibold">Contract / Allotment Batch Name</label>
                                        <input type="text" name="batch_name" class="form-control form-control-sm"
                                            placeholder="e.g. HAJJ ALLOTMENT 1448/2027 (HOTEL PURCHASE)"
                                            value="{{ old('batch_name', 'Hajj Allotment ' . (date('Y') + 1) . ' Purchase') }}">
                                    </div>

                                    <div class="col-md-4">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="form-label fs-13 fw-semibold mb-0">Supplier / Vendor</label>
                                            <a href="{{ route('supplier.create') }}" target="_blank" class="text-primary fs-11 text-decoration-none">
                                                <i class="mdi mdi-plus-circle me-1"></i>+ Add Supplier
                                            </a>
                                        </div>
                                        <select name="supplier_id" class="form-select form-select-sm">
                                            <option value="">-- Direct Hotel / Vendor --</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ old('supplier_id', request('supplier_id')) == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->name }} @if($supplier->city) ({{ $supplier->city }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fs-13 fw-semibold">Currency</label>
                                        <select name="currency" class="form-select form-select-sm">
                                            <option value="SAR" selected>SAR (Saudi Riyal)</option>
                                            <option value="PKR">PKR (Pak Rupee)</option>
                                            <option value="USD">USD (US Dollar)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Multi-Hotel Allotment Rows -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="fs-15 fw-semibold text-primary mb-0">
                                        <i class="mdi mdi-format-list-checks me-1"></i> Purchased Hotel Stock Items (Room & Bed Basis)
                                    </h5>
                                    <span class="fs-12 text-muted">Enter hotel stock lines. Supports both Room types (Double, Triple, Quad) and <strong>BED basis</strong> (e.g. 545 Beds).</span>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" id="addContractRowBtn">
                                    <i class="mdi mdi-plus"></i> Add Allotment Line
                                </button>
                            </div>

                            <div class="card-body p-3">
                                <div id="contractRowsContainer">
                                    <!-- Dynamic rows injected via JS -->
                                </div>

                                <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                                    <span class="fs-13 text-muted">
                                        <i class="mdi mdi-information-outline text-info"></i>
                                        All entries will be added to your hotel room & bed stock inventory.
                                    </span>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('room-inventory.index') }}" class="btn btn-secondary btn-sm px-3">Cancel</a>
                                        <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold">
                                            <i class="mdi mdi-check-circle me-1"></i> Save All Allotments to Stock
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>


                <!-- ══════════════════════════════════════════════════════════════════ -->
                <!-- PANE 2: SINGLE HOTEL QUICK STOCK ENTRY                           -->
                <!-- ══════════════════════════════════════════════════════════════════ -->
                <div class="tab-pane fade" id="singleModePane" role="tabpanel">
                    <form action="{{ route('room-inventory.store') }}" method="POST" id="singleHotelForm">
                        @csrf

                        <!-- Card 1: Hotel & Stay Period -->
                        <div class="card mb-3 border-0 shadow-sm">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h5 class="fs-15 fw-semibold text-primary mb-0">
                                    <i class="mdi mdi-hotel me-1"></i> 1. Hotel & Allotment Information
                                </h5>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fs-13 fw-semibold text-primary">
                                            Hotel Name <span class="text-danger">*</span>
                                        </label>
                                        <select name="hotel_id" class="form-select form-select-sm" required>
                                            <option value="">-- Select Hotel --</option>
                                            @foreach($hotels as $hotel)
                                                <option value="{{ $hotel->id }}" {{ old('hotel_id') == $hotel->id ? 'selected' : '' }}>
                                                    {{ $hotel->name }} @if($hotel->city || $hotel->place) ({{ $hotel->city ?? $hotel->place }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fs-13 fw-semibold">Allotment / Batch Name</label>
                                        <input type="text" name="batch_name" class="form-control form-control-sm"
                                            value="{{ old('batch_name', 'Stock Allotment ' . date('M Y')) }}">
                                    </div>

                                    <div class="col-md-4">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="form-label fs-13 fw-semibold mb-0">Supplier / Vendor</label>
                                            <a href="{{ route('supplier.create') }}" target="_blank" class="text-primary fs-11 text-decoration-none">
                                                <i class="mdi mdi-plus-circle me-1"></i>+ Add Supplier
                                            </a>
                                        </div>
                                        <select name="supplier_id" class="form-select form-select-sm">
                                            <option value="">-- Direct Hotel / No Supplier --</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ old('supplier_id', request('supplier_id')) == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->name }} @if($supplier->city) ({{ $supplier->city }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fs-13 fw-semibold">Check-In (Gregorian & Hijri)</label>
                                        <input type="text" name="hijri_check_in" class="form-control form-control-sm mb-1 text-primary fw-semibold"
                                            placeholder="Hijri (e.g. 22-ZILQAIDA)" value="{{ old('hijri_check_in') }}">
                                        <input type="text" name="check_in" class="form-control form-control-sm flatpickr-date"
                                            placeholder="Calendar Date" value="{{ old('check_in') }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fs-13 fw-semibold">Check-Out (Gregorian & Hijri)</label>
                                        <input type="text" name="hijri_check_out" class="form-control form-control-sm mb-1 text-primary fw-semibold"
                                            placeholder="Hijri (e.g. 01-ZILHAJJ)" value="{{ old('hijri_check_out') }}">
                                        <input type="text" name="check_out" class="form-control form-control-sm flatpickr-date"
                                            placeholder="Calendar Date" value="{{ old('check_out') }}">
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label fs-13 fw-semibold">Nights</label>
                                        <input type="number" min="1" name="no_of_nights" class="form-control form-control-sm text-center"
                                            value="{{ old('no_of_nights', 1) }}">
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label fs-13 fw-semibold">Currency</label>
                                        <select name="currency" class="form-select form-select-sm">
                                            <option value="SAR" selected>SAR</option>
                                            <option value="PKR">PKR</option>
                                            <option value="USD">USD</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label fs-13 fw-semibold">Notes</label>
                                        <input type="text" name="notes" class="form-control form-control-sm"
                                            placeholder="Optional notes" value="{{ old('notes') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Room & Bed Stock Quantities -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h5 class="fs-15 fw-semibold text-primary mb-0">
                                    <i class="mdi mdi-bed-double-outline me-1"></i> 2. Room & Bed Stock Quantities
                                </h5>
                            </div>

                            <div class="card-body p-3">
                                <!-- Dedicated Bed-Basis Stock Row -->
                                <div class="p-3 mb-3 border rounded border-warning bg-soft-warning">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-3">
                                            <span class="fw-bold fs-14 text-dark d-flex align-items-center gap-1">
                                                <i class="mdi mdi-bed-empty me-1 text-warning fs-18"></i> BED BASIS (Total Beds Stock)
                                            </span>
                                            <small class="text-muted d-block fs-11">e.g. 545 Beds in Madinah hotel</small>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fs-11 text-muted mb-0 fw-semibold">Total Beds Quantity</label>
                                            <input type="number" name="bed_stock_qty" class="form-control form-control-sm border-warning fw-bold text-center fs-14"
                                                min="0" placeholder="e.g. 545" value="{{ old('bed_stock_qty', 0) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fs-11 text-muted mb-0 fw-semibold">Cost Rate Per Bed (SAR)</label>
                                            <input type="number" step="0.01" min="0" name="cost_rate_Bed" class="form-control form-control-sm text-end"
                                                placeholder="0.00" value="{{ old('cost_rate_Bed', 0) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fs-11 text-muted mb-0 fw-semibold">Meal Plan</label>
                                            <select name="meal_plan_Bed" class="form-select form-select-sm">
                                                <option value="Room Only">Room Only (RO)</option>
                                                <option value="Bed & Breakfast">Bed & Breakfast (BB)</option>
                                                <option value="Half Board">Half Board (HB)</option>
                                                <option value="Full Board">Full Board (FB)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fs-13 fw-bold text-muted text-uppercase mb-2">Or / And Room-Wise Allotments:</h6>

                                @php
                                    $roomTypes = [
                                        'Double'  => ['label' => 'Double (2 Bed)', 'default_qty' => '0'],
                                        'Triple'  => ['label' => 'Triple (3 Bed)', 'default_qty' => '0'],
                                        'Quad'    => ['label' => 'Quad (4 Bed)',   'default_qty' => '0'],
                                        'Quint'   => ['label' => 'Quint (5 Bed)',  'default_qty' => '0'],
                                        'Single'  => ['label' => 'Single (1 Bed)', 'default_qty' => '0'],
                                        'Sharing' => ['label' => 'Sharing (Beds)', 'default_qty' => '0'],
                                        'Suite'   => ['label' => 'Suite / Family', 'default_qty' => '0'],
                                    ];
                                @endphp

                                @foreach($roomTypes as $type => $info)
                                    <div class="room-input-row border-bottom pb-2 mb-2">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-md-3">
                                                <span class="fw-bold fs-13 text-dark">
                                                    <i class="mdi mdi-bed me-1 text-primary"></i> {{ $info['label'] }}
                                                </span>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-primary text-white"><i class="mdi mdi-numeric"></i></span>
                                                    <input type="number" name="rooms[{{ $type }}]"
                                                        class="form-control form-control-sm border-primary fw-bold text-center"
                                                        min="0" placeholder="0" value="{{ old('rooms.' . $type, $info['default_qty']) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" step="0.01" min="0" name="cost_rate_{{ $type }}"
                                                    class="form-control form-control-sm text-end" placeholder="Cost Rate / Room" value="0.00">
                                            </div>
                                            <div class="col-md-3">
                                                <select name="meal_plan_{{ $type }}" class="form-select form-select-sm">
                                                    <option value="Room Only">Room Only (RO)</option>
                                                    <option value="Bed & Breakfast">Bed & Breakfast (BB)</option>
                                                    <option value="Half Board" selected>Half Board (HB)</option>
                                                    <option value="Full Board">Full Board (FB)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="mt-4 pt-2 border-top d-flex justify-content-between align-items-center">
                                    <span class="fs-13 text-muted">
                                        <i class="mdi mdi-information-outline text-info"></i>
                                        Room and Bed stock will be recorded immediately into inventory.
                                    </span>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('room-inventory.index') }}" class="btn btn-secondary btn-sm px-3">Cancel</a>
                                        <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold">
                                            <i class="mdi mdi-check-circle me-1"></i> Save Room & Bed Stock
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    const hotelsList = @json($hotels);
    let contractRowIndex = 0;

    function renderContractRow(data = {}) {
        const idx = contractRowIndex++;
        const defaultHotel = data.hotel_name || '';
        const basis = data.allotment_basis || 'room';
        const roomType = data.room_type || 'DOUBLE';
        const qty = data.quantity || 10;
        const mp = data.meal_plan || 'HALF BOARD';
        const hijriIn = data.hijri_check_in || '';
        const hijriOut = data.hijri_check_out || '';
        const cIn = data.check_in || '';
        const cOut = data.check_out || '';
        const nights = data.no_of_nights || 1;
        const rate = data.cost_rate || 0;
        const total = data.total_cost || (qty * nights * rate);

        let hotelOptions = '<option value="">-- Select Hotel --</option>';
        hotelsList.forEach(h => {
            const isSel = (h.name === defaultHotel) ? 'selected' : '';
            hotelOptions += `<option value="${h.id}" data-name="${h.name}" ${isSel}>${h.name} (${h.city || h.place || 'Hotel'})</option>`;
        });

        const html = `
            <div class="contract-row" id="c_row_${idx}">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                    <span class="badge ${basis === 'bed' ? 'bg-warning text-dark' : 'bg-primary'} fs-11">
                        Line #${idx + 1} • <span class="c-basis-badge">${basis.toUpperCase()} BASIS</span>
                    </span>
                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 fs-12 remove-c-row-btn" data-row="${idx}">
                        <i class="mdi mdi-close"></i> Remove
                    </button>
                </div>

                <div class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Hotel <span class="text-danger">*</span></label>
                        <select name="allotments[${idx}][hotel_id]" class="form-select form-select-sm" required>
                            ${hotelOptions}
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Basis</label>
                        <select name="allotments[${idx}][allotment_basis]" class="form-select form-select-sm c-basis-select" data-row="${idx}">
                            <option value="room" ${basis === 'room' ? 'selected' : ''}>Room Basis</option>
                            <option value="bed" ${basis === 'bed' ? 'selected' : ''}>Bed Basis</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Room / Bed Type</label>
                        <select name="allotments[${idx}][room_type]" class="form-select form-select-sm c-type-select" id="c_type_${idx}">
                            <option value="DOUBLE" ${roomType.toUpperCase() === 'DOUBLE' ? 'selected' : ''}>DOUBLE</option>
                            <option value="TRIPLE" ${roomType.toUpperCase() === 'TRIPLE' ? 'selected' : ''}>TRIPLE</option>
                            <option value="QUAD" ${roomType.toUpperCase() === 'QUAD' ? 'selected' : ''}>QUAD</option>
                            <option value="QUINT" ${roomType.toUpperCase() === 'QUINT' ? 'selected' : ''}>QUINT</option>
                            <option value="SINGLE" ${roomType.toUpperCase() === 'SINGLE' ? 'selected' : ''}>SINGLE</option>
                            <option value="BED" ${roomType.toUpperCase() === 'BED' ? 'selected' : ''}>BED (Bed Basis)</option>
                            <option value="SHARING" ${roomType.toUpperCase() === 'SHARING' ? 'selected' : ''}>SHARING</option>
                            <option value="SUITE" ${roomType.toUpperCase() === 'SUITE' ? 'selected' : ''}>SUITE</option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold" id="c_qty_lbl_${idx}">${basis === 'bed' ? 'Beds Qty' : 'Room Qty'}</label>
                        <input type="number" min="1" name="allotments[${idx}][quantity]" class="form-control form-control-sm text-center fw-bold c-qty-input c-calc"
                            value="${qty}" required data-row="${idx}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Meal Plan (MP)</label>
                        <select name="allotments[${idx}][meal_plan]" class="form-select form-select-sm">
                            <option value="HALF BOARD" ${mp === 'HALF BOARD' ? 'selected' : ''}>HALF BOARD</option>
                            <option value="FULL BOARD" ${mp === 'FULL BOARD' ? 'selected' : ''}>FULL BOARD</option>
                            <option value="ROOM ONLY" ${mp === 'ROOM ONLY' ? 'selected' : ''}>ROOM ONLY</option>
                            <option value="BED & BREAKFAST" ${mp === 'BED & BREAKFAST' ? 'selected' : ''}>BED & BREAKFAST</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Check-In</label>
                        <input type="text" name="allotments[${idx}][hijri_check_in]" class="form-control form-control-sm mb-1 text-primary fw-semibold"
                            placeholder="Hijri (e.g. 22-ZILQAIDA)" value="${hijriIn}">
                        <input type="text" name="allotments[${idx}][check_in]" class="form-control form-control-sm flatpickr-date"
                            placeholder="Calendar Date" value="${cIn}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Check-Out</label>
                        <input type="text" name="allotments[${idx}][hijri_check_out]" class="form-control form-control-sm mb-1 text-primary fw-semibold"
                            placeholder="Hijri (e.g. 01-ZILHAJJ)" value="${hijriOut}">
                        <input type="text" name="allotments[${idx}][check_out]" class="form-control form-control-sm flatpickr-date"
                            placeholder="Calendar Date" value="${cOut}">
                    </div>

                    <div class="col-md-1">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Nights</label>
                        <input type="number" min="1" name="allotments[${idx}][no_of_nights]" class="form-control form-control-sm text-center c-nights-input c-calc"
                            value="${nights}" required data-row="${idx}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Cost Rate / ${basis === 'bed' ? 'Bed' : 'Room'}</label>
                        <input type="number" step="0.01" min="0" name="allotments[${idx}][cost_rate]" class="form-control form-control-sm text-end c-rate-input c-calc"
                            value="${rate}" placeholder="0.00" data-row="${idx}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Total Cost (SAR)</label>
                        <input type="number" step="0.01" min="0" name="allotments[${idx}][total_cost]" class="form-control form-control-sm text-end fw-bold c-total-input"
                            value="${total}" id="c_total_${idx}">
                    </div>
                </div>
            </div>
        `;

        document.getElementById('contractRowsContainer').insertAdjacentHTML('beforeend', html);
        initFlatpickr();
    }

    function initFlatpickr() {
        flatpickr(".flatpickr-date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "m/d/Y",
            allowInput: true
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        initFlatpickr();

        document.getElementById('addContractRowBtn').addEventListener('click', () => renderContractRow());

        // Quick Load Sample Hajj Contract
        document.getElementById('loadSampleContractBtn').addEventListener('click', function () {
            if (confirm('Load sample contract with Pullman Zamzam, Swissôtel, Nusk Al Hijra and Nusk Al Madinah (545 Beds)?')) {
                document.getElementById('contractRowsContainer').innerHTML = '';
                contractRowIndex = 0;

                const sampleData = [
                    { hotel_name: 'Pullman ZamZam Makkah', allotment_basis: 'room', room_type: 'DOUBLE', quantity: 30, meal_plan: 'HALF BOARD', hijri_check_in: '22-ZILQAIDA', hijri_check_out: '01-ZILHAJJ', no_of_nights: 9 },
                    { hotel_name: 'Pullman ZamZam Makkah', allotment_basis: 'room', room_type: 'TRIPLE', quantity: 10, meal_plan: 'HALF BOARD', hijri_check_in: '22-ZILQAIDA', hijri_check_out: '01-ZILHAJJ', no_of_nights: 9 },
                    { hotel_name: 'Pullman ZamZam Makkah', allotment_basis: 'room', room_type: 'QUAD', quantity: 20, meal_plan: 'HALF BOARD', hijri_check_in: '22-ZILQAIDA', hijri_check_out: '01-ZILHAJJ', no_of_nights: 9 },
                    { hotel_name: 'Swissôtel Makkah', allotment_basis: 'room', room_type: 'DOUBLE', quantity: 10, meal_plan: 'HALF BOARD', hijri_check_in: '01-ZILHAJJ', hijri_check_out: '04-ZILHAJJ', no_of_nights: 3 },
                    { hotel_name: 'Swissôtel Makkah', allotment_basis: 'room', room_type: 'TRIPLE', quantity: 4, meal_plan: 'HALF BOARD', hijri_check_in: '01-ZILHAJJ', hijri_check_out: '04-ZILHAJJ', no_of_nights: 3 },
                    { hotel_name: 'Swissôtel Makkah', allotment_basis: 'room', room_type: 'QUAD', quantity: 3, meal_plan: 'HALF BOARD', hijri_check_in: '01-ZILHAJJ', hijri_check_out: '04-ZILHAJJ', no_of_nights: 3 },
                    { hotel_name: 'NUSK AL HIJRA', allotment_basis: 'room', room_type: 'DOUBLE', quantity: 30, meal_plan: 'FULL BOARD', hijri_check_in: '14-ZILHAJJ', hijri_check_out: '17-ZILHAJJ', no_of_nights: 3 },
                    { hotel_name: 'NUSK AL HIJRA', allotment_basis: 'room', room_type: 'TRIPLE', quantity: 10, meal_plan: 'FULL BOARD', hijri_check_in: '14-ZILHAJJ', hijri_check_out: '17-ZILHAJJ', no_of_nights: 3 },
                    { hotel_name: 'NUSK AL HIJRA', allotment_basis: 'room', room_type: 'QUAD', quantity: 20, meal_plan: 'FULL BOARD', hijri_check_in: '14-ZILHAJJ', hijri_check_out: '17-ZILHAJJ', no_of_nights: 3 },
                    { hotel_name: 'NUSK AL MADINAH', allotment_basis: 'bed', room_type: 'BED', quantity: 545, meal_plan: 'ROOM ONLY', hijri_check_in: '14-ZILHAJJ', hijri_check_out: '23-ZILHAJJ', no_of_nights: 9 },
                ];

                sampleData.forEach(item => renderContractRow(item));
            }
        });

        const container = document.getElementById('contractRowsContainer');

        container.addEventListener('click', function (e) {
            const btn = e.target.closest('.remove-c-row-btn');
            if (btn) {
                const rIdx = btn.getAttribute('data-row');
                const rowElem = document.getElementById(`c_row_${rIdx}`);
                if (rowElem) rowElem.remove();
            }
        });

        container.addEventListener('change', function (e) {
            if (e.target.classList.contains('c-basis-select')) {
                const rIdx = e.target.getAttribute('data-row');
                const basis = e.target.value;
                const typeSelect = document.getElementById(`c_type_${rIdx}`);
                const qtyLbl = document.getElementById(`c_qty_lbl_${rIdx}`);
                const badge = document.querySelector(`#c_row_${rIdx} .c-basis-badge`);

                if (basis === 'bed') {
                    typeSelect.value = 'BED';
                    qtyLbl.textContent = 'Beds Qty';
                    if (badge) badge.textContent = 'BED BASIS';
                } else {
                    if (typeSelect.value === 'BED') typeSelect.value = 'DOUBLE';
                    qtyLbl.textContent = 'Room Qty';
                    if (badge) badge.textContent = 'ROOM BASIS';
                }
            }
        });

        container.addEventListener('input', function (e) {
            if (e.target.classList.contains('c-calc')) {
                const rIdx = e.target.getAttribute('data-row');
                const row = document.getElementById(`c_row_${rIdx}`);
                if (row) {
                    const qty = parseFloat(row.querySelector('.c-qty-input').value) || 0;
                    const nights = parseFloat(row.querySelector('.c-nights-input').value) || 0;
                    const rate = parseFloat(row.querySelector('.c-rate-input').value) || 0;
                    row.querySelector('.c-total-input').value = (qty * nights * rate).toFixed(2);
                }
            }
        });

        // Initialize with 1 default row
        renderContractRow();
    });
</script>
@endsection
