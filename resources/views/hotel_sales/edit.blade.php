@extends('layout.master')
@section('title', 'Edit Hotel Sale Invoice - ' . $sale->invoice_number)

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .hotel-card {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        transition: all 0.2s ease;
    }
    .hotel-card:hover {
        border-color: #94a3b8;
        box-shadow: 0 4px 14px rgba(0,0,0,0.07);
    }
    .btn-xs {
        padding: 2px 9px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 4px;
        line-height: 1.5;
    }
    .room-type-table th {
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        background-color: #f8fafc;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Title -->
            <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                <div>
                    <h4 class="fs-18 fw-semibold mb-1 text-primary">
                        <i class="mdi mdi-pencil me-1"></i> Edit Hotel Sale Invoice: {{ $sale->invoice_number }}
                    </h4>
                    <p class="text-muted fs-13 mb-0">
                        Update hotel reservation details, rooms/beds allotment, or payment balances.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('hotel-sales.show', $sale->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="mdi mdi-printer me-1"></i> View Voucher
                    </a>
                    <a href="{{ route('hotel-sales.index') }}" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left me-1"></i> Back to Invoices
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

            <form action="{{ route('hotel-sales.update', $sale->id) }}" method="POST" id="hotelSaleForm">
                @csrf
                @method('PUT')

                <!-- Section 1: Customer & Invoice Details -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="fs-15 fw-semibold text-primary mb-0">
                            <i class="mdi mdi-account-tie me-1"></i> 1. Reservation & Customer Details
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold text-primary">
                                    Customer Type <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex gap-2 flex-wrap mt-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sale_for" id="type_hotel_client" value="hotel_client" {{ ($sale->sale_for === 'hotel_client' || (!empty($sale->hotel_client_id))) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold text-dark" for="type_hotel_client">Hotel Client</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sale_for" id="type_company" value="company" {{ in_array($sale->sale_for, ['company', 'supplier']) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold text-dark" for="type_company">Supplier/Agent</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sale_for" id="type_client" value="client" {{ $sale->sale_for === 'client' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold text-dark" for="type_client">Hajj Client</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Hotel Client Selector -->
                            <div class="col-md-4 {{ ($sale->sale_for === 'hotel_client' || !empty($sale->hotel_client_id)) ? '' : 'd-none' }}" id="hotelClientSelectWrap">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fs-13 fw-semibold mb-0">Select Hotel Client <span class="text-danger">*</span></label>
                                    <button type="button" class="btn btn-link p-0 text-primary fs-11 text-decoration-none fw-semibold" data-bs-toggle="modal" data-bs-target="#quickAddHotelClientModal">
                                        <i class="mdi mdi-plus-circle me-1"></i>+ New Client
                                    </button>
                                </div>
                                <select name="hotel_client_id" id="hotelClientSelect" class="form-select form-select-sm">
                                    <option value="">-- Choose Hotel Client --</option>
                                    @foreach($hotelClients as $hc)
                                        <option value="{{ $hc->id }}" data-company="{{ $hc->company_name }}" data-name="{{ $hc->name }}" data-contact="{{ $hc->contact }}" {{ $sale->hotel_client_id == $hc->id ? 'selected' : '' }}>
                                            {{ $hc->company_name ? $hc->company_name . ' (' . $hc->name . ')' : $hc->name }} @if($hc->contact) - {{ $hc->contact }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 {{ in_array($sale->sale_for, ['company', 'supplier']) ? '' : 'd-none' }}" id="companySelectWrap">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fs-13 fw-semibold mb-0">Select Supplier / Agent <span class="text-danger">*</span></label>
                                    <a href="{{ route('supplier.create') }}" target="_blank" class="text-primary fs-11 text-decoration-none">
                                        <i class="mdi mdi-plus-circle me-1"></i>+ Add Supplier
                                    </a>
                                </div>
                                <select name="supplier_id" id="companySelect" class="form-select form-select-sm">
                                    <option value="">-- Choose Supplier / Agent --</option>
                                    <optgroup label="Suppliers / Vendors">
                                        @foreach($suppliers as $sup)
                                            <option value="{{ $sup->id }}" data-name="{{ $sup->name }}" data-contact="{{ $sup->phone ?: ($sup->mobile ?: '') }}" {{ ($sale->supplier_id == $sup->id || (empty($sale->supplier_id) && $sale->company_id == $sup->id)) ? 'selected' : '' }}>
                                                {{ $sup->name }} @if($sup->city) ({{ $sup->city }}) @endif
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    @if(count($companies) > 0)
                                        <optgroup label="Internal Companies / Branches">
                                            @foreach($companies as $comp)
                                                <option value="comp_{{ $comp->id }}" data-name="{{ $comp->company_name }}" data-contact="{{ $comp->phone ?? ($comp->contactNumbers->first()->contact_number ?? '') }}" {{ $sale->company_id == $comp->id ? 'selected' : '' }}>
                                                    {{ $comp->company_name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-4 {{ $sale->sale_for === 'client' ? '' : 'd-none' }}" id="clientSelectWrap">
                                <label class="form-label fs-13 fw-semibold">Select Hajj Client <span class="text-danger">*</span></label>
                                <select name="client_id" id="clientSelect" class="form-select form-select-sm">
                                    <option value="">-- Choose Client --</option>
                                    @foreach($clients as $cl)
                                        <option value="{{ $cl->id }}" data-name="{{ $cl->name }}" data-contact="{{ $cl->phone ?? '' }}" {{ $sale->client_id == $cl->id ? 'selected' : '' }}>
                                            {{ $cl->name }} @if($cl->phone) ({{ $cl->phone }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fs-13 fw-semibold">Customer / Client Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" id="customerName" class="form-control form-control-sm"
                                    value="{{ old('customer_name', $sale->customer_name) }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Invoice Ref / Voucher #</label>
                                <input type="text" name="invoice_number" class="form-control form-control-sm fw-bold bg-light"
                                    value="{{ $sale->invoice_number }}" readonly>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Invoice Date <span class="text-danger">*</span></label>
                                <input type="text" name="invoice_date" class="form-control form-control-sm flatpickr-date"
                                    value="{{ old('invoice_date', $sale->invoice_date ? $sale->invoice_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Reservation Title / Season</label>
                                <input type="text" name="reservation_title" class="form-control form-control-sm"
                                    value="{{ old('reservation_title', $sale->reservation_title) }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fs-13 fw-semibold">Currency</label>
                                <select name="currency" class="form-select form-select-sm">
                                    <option value="SAR" {{ $sale->currency === 'SAR' ? 'selected' : '' }}>SAR (Saudi Riyal)</option>
                                    <option value="PKR" {{ $sale->currency === 'PKR' ? 'selected' : '' }}>PKR (Pak Rupee)</option>
                                    <option value="USD" {{ $sale->currency === 'USD' ? 'selected' : '' }}>USD (US Dollar)</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Attn. (Attention Person)</label>
                                <input type="text" name="attn" id="attnInput" class="form-control form-control-sm"
                                    value="{{ old('attn', $sale->attn) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Contact Details</label>
                                <input type="text" name="contact" id="customerContact" class="form-control form-control-sm"
                                    value="{{ old('contact', $sale->contact) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Hotel Reservation Items (Room Basis & Bed Basis) -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fs-15 fw-semibold text-primary mb-0">
                                <i class="mdi mdi-hotel me-1"></i> 2. Reserved Hotels (Room & Bed Allotment)
                            </h5>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" id="addItemBtn">
                            <i class="mdi mdi-plus-circle me-1"></i> + Add Hotel
                        </button>
                    </div>
                    <div class="card-body p-3">
                        <div id="itemsContainer">
                            <!-- Populated with existing items via JS -->
                        </div>

                        <!-- Overall Summary Row -->
                        <div class="card bg-light border-0 mt-3 p-3">
                            <div class="row g-3 align-items-center justify-content-end">
                                <div class="col-md-3">
                                    <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">TOTAL AMOUNT</label>
                                    <input type="number" step="0.01" name="total_amount" id="grandTotalAmount"
                                        class="form-control form-control-sm fw-bold fs-15 text-primary text-end bg-white" readonly
                                        value="{{ number_format($sale->total_amount, 2, '.', '') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fs-12 fw-bold text-uppercase text-success mb-1">TILL RECEIVED (Advance)</label>
                                    <input type="number" step="0.01" name="till_received" id="tillReceived"
                                        class="form-control form-control-sm fw-bold fs-15 text-success text-end"
                                        value="{{ number_format($sale->till_received, 2, '.', '') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fs-12 fw-bold text-uppercase text-danger mb-1">BALANCE REMAINING</label>
                                    <input type="number" step="0.01" name="balance_remaining" id="balanceRemaining"
                                        class="form-control form-control-sm fw-bold fs-15 text-danger text-end bg-white" readonly
                                        value="{{ number_format($sale->balance_remaining, 2, '.', '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Policy, Payment Procedure & Signatures -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="fs-15 fw-semibold text-primary mb-0">
                            <i class="mdi mdi-text-box-check-outline me-1"></i> 3. Policy, Payment Procedure & Signatures
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold">Terms & Conditions</label>
                                <textarea name="terms_conditions" rows="5" class="form-control form-control-sm fs-12 font-monospace">{{ old('terms_conditions', $sale->terms_conditions) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold">Cancellation Policy</label>
                                <textarea name="cancellation_policy" rows="5" class="form-control form-control-sm fs-12 font-monospace">{{ old('cancellation_policy', $sale->cancellation_policy) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold">Payment Procedure</label>
                                <textarea name="payment_procedure" rows="3" class="form-control form-control-sm fs-12 font-monospace">{{ old('payment_procedure', $sale->payment_procedure) }}</textarea>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">C.E.O / Signatory Name</label>
                                <input type="text" name="ceo_name" class="form-control form-control-sm"
                                    value="{{ old('ceo_name', $sale->ceo_name) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Signatory Title</label>
                                <input type="text" name="ceo_title" class="form-control form-control-sm"
                                    value="{{ old('ceo_title', $sale->ceo_title) }}">
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="fs-13 text-muted">
                                <i class="mdi mdi-check-decagram text-success me-1"></i>
                                Saved changes will reflect immediately on the confirmation voucher.
                            </span>
                            <div class="d-flex gap-2">
                                <a href="{{ route('hotel-sales.index') }}" class="btn btn-secondary btn-sm px-3">Cancel</a>
                                <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold">
                                    <i class="mdi mdi-check-circle me-1"></i> Update Reservation Invoice
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- Mini Modal: Quick Add Hotel Client -->
<div class="modal fade" id="quickAddHotelClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="quickAddHotelClientForm">
                @csrf
                <div class="modal-header bg-primary text-white py-2 px-3">
                    <h5 class="modal-title fs-15 fw-bold text-white">
                        <i class="mdi mdi-account-plus me-1"></i> Quick Add Hotel Client
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">Company Name</label>
                        <input type="text" id="quick_company_name" name="company_name" class="form-control form-control-sm" placeholder="e.g. AL MOAZZIN">
                        <small class="text-muted fs-11">Company or agency name that appears on invoice header.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">Contact Person (Name) <span class="text-danger">*</span></label>
                        <input type="text" id="quick_name" name="name" class="form-control form-control-sm" required placeholder="e.g. MUHAMMAD SALEEM">
                        <small class="text-muted fs-11">Person's name that appears under Attn.</small>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">Contact / Phone</label>
                        <input type="text" id="quick_contact" name="contact" class="form-control form-control-sm" placeholder="e.g. 0300-1234567 / +966 50 123 4567">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="quickAddClientSubmitBtn" class="btn btn-primary btn-sm px-3 fw-bold">
                        <i class="mdi mdi-check me-1"></i> Save &amp; Select
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Mini Modal: Quick Add New Hotel -->
<div class="modal fade" id="quickAddHotelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="quickAddHotelForm">
                @csrf
                <div class="modal-header bg-dark text-white py-2 px-3">
                    <h5 class="modal-title fs-15 fw-bold text-white">
                        <i class="mdi mdi-office-building-plus me-1 text-warning"></i> Quick Add New Hotel
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">Hotel Name <span class="text-danger">*</span></label>
                        <input type="text" id="quick_hotel_name" name="name" class="form-control form-control-sm" required placeholder="e.g. MAKKAH CLOCK ROYAL TOWER">
                        <small class="text-muted fs-11">Official name of the hotel property.</small>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">City / Place <span class="text-danger">*</span></label>
                            <select id="quick_hotel_place" name="place" class="form-select form-select-sm" required>
                                <option value="MAKKAH" selected>MAKKAH</option>
                                <option value="MADINAH">MADINAH</option>
                                <option value="JEDDAH">JEDDAH</option>
                                <option value="AZIZIA">AZIZIA</option>
                                <option value="MINA">MINA</option>
                                <option value="OTHER">OTHER</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">Category / Stars</label>
                            <select id="quick_hotel_category" name="accommodation_category" class="form-select form-select-sm">
                                <option value="5 Star" selected>5 Star Luxury</option>
                                <option value="4 Star">4 Star</option>
                                <option value="3 Star">3 Star</option>
                                <option value="Standard / Economy">Standard / Economy</option>
                                <option value="Building">Building / Apartment</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">Contact / Phone</label>
                        <input type="text" id="quick_hotel_contact" name="contact" class="form-control form-control-sm" placeholder="e.g. +966 12 571 7777">
                    </div>

                    <div class="mb-1">
                        <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">Address / Location</label>
                        <input type="text" id="quick_hotel_address" name="address" class="form-control form-control-sm" placeholder="e.g. Ibrahim Al Khalil Road, Makkah">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="saveQuickHotelBtn" class="btn btn-primary btn-sm px-3 fw-bold">
                        <i class="mdi mdi-check me-1"></i> Save &amp; Select Hotel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    const hotelsList = @json($hotels);
    const existingItems = @json($sale->items);
    let hotelCardIndex = 0;
    let globalItemIndex = 0;

    const HIJRI_MONTH_NAMES = {
        1: 'MUHARRAM', 2: 'SAFAR', 3: 'RABI-I', 4: 'RABI-II',
        5: 'JAMADI-I', 6: 'JAMADI-II', 7: 'RAJAB', 8: 'SHABAN',
        9: 'RAMADAN', 10: 'SHAWWAL', 11: 'ZILQAIDA', 12: 'ZILHAJJ'
    };

    // Exact Umm Al-Qura month start dates for Hajj / Umrah seasons (1446-1450 AH)
    // Synchronized with official Saudi calendar and Maghrib date transition
    const UMM_AL_QURA_MONTH_STARTS = {
        '1447-09': '2026-02-18', '1447-10': '2026-03-20', '1447-11': '2026-04-18', '1447-12': '2026-05-18',
        '1448-01': '2026-06-16', '1448-02': '2026-07-16', '1448-03': '2026-08-14', '1448-04': '2026-09-13',
        '1448-05': '2026-10-12', '1448-06': '2026-11-11', '1448-07': '2026-12-10', '1448-08': '2027-01-09',
        '1448-09': '2027-02-08', '1448-10': '2027-03-10',
        '1448-11': '2027-04-08', // 21 Zilqaida = 2027-04-28!
        '1448-12': '2027-05-07', // 01 Zilhajj = 2027-05-07, 08 Zilhajj = 2027-05-14
        '1449-01': '2027-06-06', '1449-11': '2028-03-28', '1449-12': '2028-04-26',
        '1450-01': '2028-05-26', '1450-11': '2029-04-16', '1450-12': '2029-05-15'
    };

    function kuwaitiHijri(year, month, day) {
        let jd;
        const y = year, m = month, d = day;
        if ((year > 1582) || ((year === 1582) && (month > 10)) || ((year === 1582) && (month === 10) && (day > 14))) {
            jd = Math.trunc((1461 * (y + 4800 + Math.trunc((m - 14) / 12))) / 4) +
                 Math.trunc((367 * (m - 2 - 12 * Math.trunc((m - 14) / 12))) / 12) -
                 Math.trunc((3 * Math.trunc((y + 4900 + Math.trunc((m - 14) / 12)) / 100)) / 4) +
                 d - 32075;
        } else {
            jd = 367 * y - Math.trunc((7 * (y + 5001 + Math.trunc((m - 9) / 7))) / 4) +
                 Math.trunc((275 * m) / 9) + d + 1729777;
        }

        let l = jd - 1948440 + 10632;
        const n = Math.trunc((l - 1) / 10631);
        l = l - 10631 * n + 354;
        const j = Math.trunc((10985 - l) / 5316) * Math.trunc((50 * l) / 17719) +
                  Math.trunc(l / 5670) * Math.trunc((43 * l) / 15238);
        l = l - Math.trunc((30 - j) / 15) * Math.trunc((17719 * j) / 50) -
            Math.trunc(j / 16) * Math.trunc((15238 * j) / 43) + 29;
        const hm = Math.trunc((24 * l) / 709);
        const hd = l - Math.trunc((709 * hm) / 24);

        const dd = String(hd).padStart(2, '0');
        const monthName = HIJRI_MONTH_NAMES[hm] || ('M' + hm);
        return `${dd}-${monthName}`;
    }

    function gregorianToHijri(dateStr) {
        if (!dateStr) return '';
        try {
            const parts = dateStr.trim().split('-');
            if (parts.length !== 3) return '';
            const y = parseInt(parts[0], 10);
            const m = parseInt(parts[1], 10);
            const d = parseInt(parts[2], 10);
            if (isNaN(y) || isNaN(m) || isNaN(d)) return '';

            const targetDate = `${String(y).padStart(4, '0')}-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')}`;

            // 1. Check Umm Al-Qura month start table
            const keys = Object.keys(UMM_AL_QURA_MONTH_STARTS).sort();
            for (let i = 0; i < keys.length; i++) {
                const currKey = keys[i];
                const currDate = UMM_AL_QURA_MONTH_STARTS[currKey];
                const nextDate = keys[i + 1] ? UMM_AL_QURA_MONTH_STARTS[keys[i + 1]] : null;

                if (targetDate >= currDate && (!nextDate || targetDate < nextDate)) {
                    const d1 = new Date(currDate + 'T00:00:00Z');
                    const d2 = new Date(targetDate + 'T00:00:00Z');
                    const diffDays = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
                    const hDay = diffDays + 1;
                    const hMonth = parseInt(currKey.split('-')[1], 10);
                    const dd = String(hDay).padStart(2, '0');
                    return `${dd}-${HIJRI_MONTH_NAMES[hMonth] || ('M' + hMonth)}`;
                }
            }

            // 2. Try modern browser Intl API (Saudi Umm Al-Qura) with explicit UTC timezone
            if (typeof Intl !== 'undefined' && Intl.DateTimeFormat) {
                try {
                    const dateObj = new Date(Date.UTC(y, m - 1, d, 12, 0, 0));
                    const formatter = new Intl.DateTimeFormat('en-u-ca-islamic-umalqura-nu-latn', {
                        day: 'numeric',
                        month: 'numeric',
                        year: 'numeric',
                        timeZone: 'UTC'
                    });
                    const fParts = formatter.formatToParts(dateObj);
                    let hDay = null, hMonth = null;
                    for (const p of fParts) {
                        if (p.type === 'day') hDay = parseInt(p.value, 10);
                        if (p.type === 'month') hMonth = parseInt(p.value, 10);
                    }
                    if (hDay && hMonth && HIJRI_MONTH_NAMES[hMonth]) {
                        const dd = String(hDay).padStart(2, '0');
                        return `${dd}-${HIJRI_MONTH_NAMES[hMonth]}`;
                    }
                } catch (err) {}
            }

            // 3. Fallback to Kuwaiti mathematical formula
            return kuwaitiHijri(y, m, d);
        } catch (e) {
            return '';
        }
    }

    function hijriToGregorian(hijriStr, defaultYear = 1448) {
        if (!hijriStr) return '';
        try {
            let clean = hijriStr.toUpperCase().trim();
            clean = clean.replace(/HIJRI|\/|\\/g, ' ');

            let hy = defaultYear;
            const ym = clean.match(/\b(14\d{2})\b/);
            if (ym) {
                hy = parseInt(ym[1], 10);
                clean = clean.replace(ym[0], ' ');
            }

            const dm = clean.match(/(\d{1,2})/);
            if (!dm) return '';
            const hd = parseInt(dm[1], 10);

            let hm = 0;
            if (clean.includes('MUHAR')) hm = 1;
            else if (clean.includes('SAF')) hm = 2;
            else if (clean.includes('RABI-I') || clean.includes('RABI 1') || clean.includes('RABI-1') || clean.includes('RABI-UL-AWWAL')) hm = 3;
            else if (clean.includes('RABI-II') || clean.includes('RABI 2') || clean.includes('RABI-2') || clean.includes('RABI-US-SANI')) hm = 4;
            else if (clean.includes('JAMADI-I') || clean.includes('JAMADI 1') || clean.includes('JAMADI-1')) hm = 5;
            else if (clean.includes('JAMADI-II') || clean.includes('JAMADI 2') || clean.includes('JAMADI-2')) hm = 6;
            else if (clean.includes('RAJ')) hm = 7;
            else if (clean.includes('SHA') && !clean.includes('SHAW')) hm = 8;
            else if (clean.includes('RAM')) hm = 9;
            else if (clean.includes('SHAW')) hm = 10;
            else if (clean.includes('ZILQ') || clean.includes('QAIDA') || clean.includes('QADH')) hm = 11;
            else if (clean.includes('ZILH') || clean.includes('HAJJ') || clean.includes('HIJJ')) hm = 12;

            if (hm === 0) return '';

            // 1. Check Umm Al-Qura table
            const key = `${hy}-${String(hm).padStart(2, '0')}`;
            if (UMM_AL_QURA_MONTH_STARTS[key]) {
                const startDate = UMM_AL_QURA_MONTH_STARTS[key];
                const d = new Date(startDate + 'T00:00:00Z');
                d.setUTCDate(d.getUTCDate() + (hd - 1));
                return d.toISOString().substring(0, 10);
            }

            // 2. Fallback to Julian Day conversion
            const jd = Math.trunc((11 * hy + 3) / 30) + 354 * hy + 30 * hm - Math.trunc((hm - 1) / 2) + hd + 1948440 - 385;
            let l = jd + 68569;
            const n = Math.trunc((4 * l) / 146097);
            l = l - Math.trunc((146097 * n + 3) / 4);
            const i = Math.trunc((4000 * (l + 1)) / 1461001);
            l = l - Math.trunc((1461 * i) / 4) + 31;
            const j = Math.trunc((80 * l) / 2447);
            const day = l - Math.trunc((2447 * j) / 80);
            l = Math.trunc(j / 11);
            const month = j + 2 - 12 * l;
            const year = 100 * (n - 49) + i + l;

            return `${String(year).padStart(4, '0')}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        } catch (e) {
            return '';
        }
    }

    function dateDiffInDays(checkInStr, checkOutStr) {
        if (!checkInStr || !checkOutStr) return 0;
        const pIn = checkInStr.trim().split('-');
        const pOut = checkOutStr.trim().split('-');
        if (pIn.length !== 3 || pOut.length !== 3) return 0;

        const dIn = new Date(parseInt(pIn[0], 10), parseInt(pIn[1], 10) - 1, parseInt(pIn[2], 10));
        const dOut = new Date(parseInt(pOut[0], 10), parseInt(pOut[1], 10) - 1, parseInt(pOut[2], 10));

        const diffMs = dOut.getTime() - dIn.getTime();
        return Math.round(diffMs / (1000 * 60 * 60 * 24));
    }

    function addDaysToDate(dateStr, days) {
        if (!dateStr || isNaN(days)) return '';
        const parts = dateStr.trim().split('-');
        if (parts.length !== 3) return '';
        const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        d.setDate(d.getDate() + parseInt(days, 10));
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${day}`;
    }

    function renderHotelCard(data = {}) {
        const hIdx = hotelCardIndex++;
        const defaultHotel = data.hotel_name || '';
        const hotelId = data.hotel_id || '';
        const basis = data.allotment_basis || 'room';
        const mp = data.meal_plan || 'HALF BOARD';
        const cIn = data.check_in ? (typeof data.check_in === 'string' ? data.check_in.substring(0, 10) : data.check_in) : '';
        const cOut = data.check_out ? (typeof data.check_out === 'string' ? data.check_out.substring(0, 10) : data.check_out) : '';
        const hijriIn = data.hijri_check_in || (cIn ? gregorianToHijri(cIn) : '');
        const hijriOut = data.hijri_check_out || (cOut ? gregorianToHijri(cOut) : '');

        let nights = data.no_of_nights;
        if (!nights || nights < 1) {
            if (cIn && cOut) {
                const d = dateDiffInDays(cIn, cOut);
                nights = d > 0 ? d : 1;
            } else {
                nights = 1;
            }
        }

        let badgeClass = 'bg-primary';
        let badgeLabel = 'ROOM BASIS';
        let qtyLabel = 'Room Qty';
        let rateLabel = 'Rate Per Room';

        if (basis === 'bed') {
            badgeClass = 'bg-warning text-dark';
            badgeLabel = 'BED BASIS';
            qtyLabel = 'Beds Qty';
            rateLabel = 'Rate Per Bed';
        } else if (basis === 'per_night') {
            badgeClass = 'bg-info text-dark';
            badgeLabel = 'PER NIGHT BASIS';
            qtyLabel = 'Qty';
            rateLabel = 'Rate Per Night';
        }

        let hotelOptions = '<option value="">-- Choose Hotel or Type Below --</option>';
        hotelsList.forEach(h => {
            const sel = (h.name === defaultHotel) ? 'selected' : '';
            hotelOptions += `<option value="${h.name}" data-id="${h.id}" ${sel}>${h.name} (${h.city || h.place || 'Hotel'})</option>`;
        });

        const cardHtml = `
        <div class="card mb-3 hotel-card" id="hotel_card_${hIdx}" data-hotel-idx="${hIdx}">
            <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-dark fs-12">
                        <i class="mdi mdi-office-building me-1"></i> Hotel #${hIdx + 1}
                    </span>
                    <span class="badge ${badgeClass} fs-11 basis-badge">
                        ${badgeLabel}
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="fs-12 fw-bold text-dark">
                        Hotel Subtotal: <span class="text-primary hotel-subtotal" id="hotel_subtotal_${hIdx}">SAR 0.00</span>
                    </span>
                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 fs-11 remove-hotel-btn" data-hotel-idx="${hIdx}">
                        <i class="mdi mdi-close"></i> Remove Hotel
                    </button>
                </div>
            </div>

            <div class="card-body p-3">
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between align-items-center mb-0">
                            <label class="form-label fs-11 text-muted mb-0 fw-semibold">Hotel Name <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-link p-0 text-primary fs-11 text-decoration-none fw-semibold open-quick-hotel-btn" data-hotel-idx="${hIdx}" data-bs-toggle="modal" data-bs-target="#quickAddHotelModal">
                                <i class="mdi mdi-plus-circle me-1"></i>+ New Hotel
                            </button>
                        </div>
                        <select class="form-select form-select-sm hotel-select mb-1" data-hotel-idx="${hIdx}">
                            ${hotelOptions}
                        </select>
                        <input type="text" class="form-control form-control-sm hotel-name-input" placeholder="Type or select hotel name" value="${defaultHotel}" required data-hotel-idx="${hIdx}">
                        <input type="hidden" class="hotel-id-input" value="${hotelId}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Allotment Basis</label>
                        <select class="form-select form-select-sm hotel-basis-select" data-hotel-idx="${hIdx}">
                            <option value="room" ${basis === 'room' ? 'selected' : ''}>Room Basis</option>
                            <option value="bed" ${basis === 'bed' ? 'selected' : ''}>Bed Basis</option>
                            <option value="per_night" ${basis === 'per_night' ? 'selected' : ''}>Per Night Basis</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Meal Plan (MP)</label>
                        <select class="form-select form-select-sm hotel-meal-plan-select" data-hotel-idx="${hIdx}">
                            <option value="HALF BOARD" ${mp === 'HALF BOARD' ? 'selected' : ''}>HALF BOARD</option>
                            <option value="FULL BOARD" ${mp === 'FULL BOARD' ? 'selected' : ''}>FULL BOARD</option>
                            <option value="ROOM ONLY" ${mp === 'ROOM ONLY' ? 'selected' : ''}>ROOM ONLY</option>
                            <option value="BED & BREAKFAST" ${mp === 'BED & BREAKFAST' ? 'selected' : ''}>BED & BREAKFAST</option>
                            <option value="SUHOOR" ${mp === 'SUHOOR' ? 'selected' : ''}>SUHOOR</option>
                            <option value="IFTAR" ${mp === 'IFTAR' ? 'selected' : ''}>IFTAR</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex justify-content-between align-items-center mb-0">
                            <label class="form-label fs-11 text-muted mb-0 fw-semibold">Check-In</label>
                            <span class="badge bg-light text-primary border fs-10 py-0 px-1">Hijri &amp; Date</span>
                        </div>
                        <input type="text" class="form-control form-control-sm mb-1 text-primary fw-bold hotel-hijri-in" placeholder="e.g. 01-ZILHAJJ" value="${hijriIn}" data-hotel-idx="${hIdx}">
                        <input type="text" class="form-control form-control-sm hotel-check-in flatpickr-date" placeholder="Check-In Date" value="${cIn}" data-hotel-idx="${hIdx}">
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex justify-content-between align-items-center mb-0">
                            <label class="form-label fs-11 text-muted mb-0 fw-semibold">Check-Out</label>
                            <span class="badge bg-light text-primary border fs-10 py-0 px-1">Hijri &amp; Date</span>
                        </div>
                        <input type="text" class="form-control form-control-sm mb-1 text-primary fw-bold hotel-hijri-out" placeholder="e.g. 06-ZILHAJJ" value="${hijriOut}" data-hotel-idx="${hIdx}">
                        <input type="text" class="form-control form-control-sm hotel-check-out flatpickr-date" placeholder="Check-Out Date" value="${cOut}" data-hotel-idx="${hIdx}">
                    </div>
                </div>

                <!-- Nights info and Quick Add Room Toolbar -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3 mb-2 bg-light rounded border">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-12 fw-semibold text-muted">Stay Duration:</span>
                        <input type="number" min="1" class="form-control form-control-sm text-center fw-bold hotel-nights-input" style="width: 70px;" value="${nights}" data-hotel-idx="${hIdx}">
                        <span class="fs-12 fw-bold text-dark">Nights</span>
                    </div>

                    <div class="d-flex align-items-center flex-wrap gap-1">
                        <span class="fs-11 fw-semibold text-muted me-1"><i class="mdi mdi-plus-box-multiple text-primary me-1"></i>Quick Add Room Type:</span>
                        <button type="button" class="btn btn-xs btn-outline-primary quick-add-btn" data-hotel-idx="${hIdx}" data-type="DOUBLE">+ DOUBLE</button>
                        <button type="button" class="btn btn-xs btn-outline-primary quick-add-btn" data-hotel-idx="${hIdx}" data-type="TRIPLE">+ TRIPLE</button>
                        <button type="button" class="btn btn-xs btn-outline-primary quick-add-btn" data-hotel-idx="${hIdx}" data-type="QUAD">+ QUAD</button>
                        <button type="button" class="btn btn-xs btn-outline-primary quick-add-btn" data-hotel-idx="${hIdx}" data-type="QUINT">+ QUINT</button>
                        <button type="button" class="btn btn-xs btn-outline-primary quick-add-btn" data-hotel-idx="${hIdx}" data-type="SINGLE">+ SINGLE</button>
                        <button type="button" class="btn btn-xs btn-outline-warning text-dark quick-add-btn" data-hotel-idx="${hIdx}" data-type="BED">+ BED</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary quick-add-btn" data-hotel-idx="${hIdx}" data-type="SHARING">+ SHARING</button>
                    </div>
                </div>

                <!-- Room Types Breakdown Table -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-1 bg-white room-type-table">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Room / Bed Type</th>
                                <th style="width: 15%;" class="text-center th-qty-label">${qtyLabel}</th>
                                <th style="width: 25%;" class="text-end th-rate-label">${rateLabel} (SAR)</th>
                                <th style="width: 25%;" class="text-end">Total Amount (SAR)</th>
                                <th style="width: 10%;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="room-types-tbody" id="room_types_tbody_${hIdx}">
                        </tbody>
                    </table>
                </div>

                <div class="mt-2 text-start">
                    <button type="button" class="btn btn-xs btn-outline-success add-custom-room-btn" data-hotel-idx="${hIdx}">
                        <i class="mdi mdi-plus-circle me-1"></i> + Add Another Room Type
                    </button>
                </div>
            </div>
        </div>
        `;

        document.getElementById('itemsContainer').insertAdjacentHTML('beforeend', cardHtml);
        const card = document.getElementById(`hotel_card_${hIdx}`);
        initFlatpickr(card);

        // Add room types
        if (data.room_types && Array.isArray(data.room_types) && data.room_types.length > 0) {
            data.room_types.forEach(rt => addRoomTypeRow(hIdx, rt));
        } else if (data.room_type) {
            addRoomTypeRow(hIdx, {
                room_type: data.room_type,
                quantity: data.quantity || 1,
                rate_per_unit: data.rate_per_unit || 0,
                amount: data.amount
            });
        } else {
            addRoomTypeRow(hIdx, {
                room_type: (basis === 'bed' ? 'BED' : 'DOUBLE'),
                quantity: (data.quantity || (basis === 'bed' ? 100 : 10)),
                rate_per_unit: (data.rate_per_unit || 0),
                amount: data.amount
            });
        }

        syncHotelToRooms(hIdx);
        recalcTotals();
    }

    function addRoomTypeRow(hIdx, roomData = {}) {
        const card = document.getElementById(`hotel_card_${hIdx}`);
        const tbody = document.getElementById(`room_types_tbody_${hIdx}`);
        if (!card || !tbody) return;

        const rIdx = globalItemIndex++;
        const rType = (roomData.room_type || 'DOUBLE').toUpperCase().trim();
        const qty = roomData.quantity || 1;
        const rate = parseFloat(roomData.rate_per_unit) || 0;

        const hotelName = card.querySelector('.hotel-name-input')?.value || '';
        const hotelId = card.querySelector('.hotel-id-input')?.value || '';
        const basis = card.querySelector('.hotel-basis-select')?.value || 'room';
        const mp = card.querySelector('.hotel-meal-plan-select')?.value || 'HALF BOARD';
        const cIn = card.querySelector('.hotel-check-in')?.value || '';
        const cOut = card.querySelector('.hotel-check-out')?.value || '';
        const hijriIn = card.querySelector('.hotel-hijri-in')?.value || '';
        const hijriOut = card.querySelector('.hotel-hijri-out')?.value || '';
        const nights = parseInt(card.querySelector('.hotel-nights-input')?.value, 10) || 1;

        let amt = roomData.amount;
        if (amt === undefined || amt === null) {
            amt = (basis === 'per_night') ? (qty * nights * rate) : (qty * rate);
        }

        const standardTypes = ['DOUBLE', 'TRIPLE', 'QUAD', 'QUINT', 'SINGLE', 'BED', 'SHARING', 'SUITE'];
        const isCustom = !standardTypes.includes(rType);

        const rowHtml = `
        <tr class="room-type-row" id="room_row_${rIdx}" data-hotel-idx="${hIdx}" data-row-idx="${rIdx}">
            <td>
                <select name="items[${rIdx}][room_type]" class="form-select form-select-sm fw-semibold room-type-select">
                    <option value="DOUBLE" ${rType === 'DOUBLE' ? 'selected' : ''}>DOUBLE</option>
                    <option value="TRIPLE" ${rType === 'TRIPLE' ? 'selected' : ''}>TRIPLE</option>
                    <option value="QUAD" ${rType === 'QUAD' ? 'selected' : ''}>QUAD</option>
                    <option value="QUINT" ${rType === 'QUINT' ? 'selected' : ''}>QUINT</option>
                    <option value="SINGLE" ${rType === 'SINGLE' ? 'selected' : ''}>SINGLE</option>
                    <option value="BED" ${rType === 'BED' ? 'selected' : ''}>BED</option>
                    <option value="SHARING" ${rType === 'SHARING' ? 'selected' : ''}>SHARING</option>
                    <option value="SUITE" ${rType === 'SUITE' ? 'selected' : ''}>SUITE</option>
                    ${isCustom ? `<option value="${rType}" selected>${rType}</option>` : ''}
                </select>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" min="1" step="1" name="items[${rIdx}][quantity]" class="form-control form-control-sm text-center fw-bold room-qty-input" value="${qty || 1}" required>
                    <span class="input-group-text qty-unit-text fs-11">${basis === 'bed' ? 'Beds' : 'Rooms'}</span>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text fs-11">SAR</span>
                    <input type="number" step="0.01" min="0" name="items[${rIdx}][rate_per_unit]" class="form-control form-control-sm text-end room-rate-input" value="${rate > 0 ? rate : ''}" placeholder="0.00">
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text fs-11">SAR</span>
                    <input type="number" step="0.01" min="0" name="items[${rIdx}][amount]" class="form-control form-control-sm text-end fw-bold room-amount-input amount-input" value="${parseFloat(amt).toFixed(2)}" id="amount_${rIdx}">
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm p-1 remove-room-btn" title="Remove Room Type">
                    <i class="mdi mdi-trash-can-outline fs-14"></i>
                </button>
            </td>

            <!-- Synced Hidden Fields from Parent Hotel -->
            <input type="hidden" name="items[${rIdx}][hotel_name]" class="synced-hotel-name" value="${hotelName}">
            <input type="hidden" name="items[${rIdx}][hotel_id]" class="synced-hotel-id" value="${hotelId}">
            <input type="hidden" name="items[${rIdx}][allotment_basis]" class="synced-basis" value="${basis}">
            <input type="hidden" name="items[${rIdx}][meal_plan]" class="synced-meal-plan" value="${mp}">
            <input type="hidden" name="items[${rIdx}][check_in]" class="synced-check-in" value="${cIn}">
            <input type="hidden" name="items[${rIdx}][check_out]" class="synced-check-out" value="${cOut}">
            <input type="hidden" name="items[${rIdx}][hijri_check_in]" class="synced-hijri-in" value="${hijriIn}">
            <input type="hidden" name="items[${rIdx}][hijri_check_out]" class="synced-hijri-out" value="${hijriOut}">
            <input type="hidden" name="items[${rIdx}][no_of_nights]" class="synced-nights" value="${nights}">
        </tr>
        `;

        tbody.insertAdjacentHTML('beforeend', rowHtml);
        recalcRowAmount(rIdx);
    }

    function syncHotelToRooms(hIdx) {
        const card = document.getElementById(`hotel_card_${hIdx}`);
        if (!card) return;

        const hotelName = card.querySelector('.hotel-name-input')?.value || '';
        const hotelId = card.querySelector('.hotel-id-input')?.value || '';
        const basis = card.querySelector('.hotel-basis-select')?.value || 'room';
        const mp = card.querySelector('.hotel-meal-plan-select')?.value || 'HALF BOARD';
        const cIn = card.querySelector('.hotel-check-in')?.value || '';
        const cOut = card.querySelector('.hotel-check-out')?.value || '';
        const hijriIn = card.querySelector('.hotel-hijri-in')?.value || '';
        const hijriOut = card.querySelector('.hotel-hijri-out')?.value || '';
        const nights = parseInt(card.querySelector('.hotel-nights-input')?.value, 10) || 1;

        const badge = card.querySelector('.basis-badge');
        const thQty = card.querySelector('.th-qty-label');
        const thRate = card.querySelector('.th-rate-label');

        if (basis === 'bed') {
            if (badge) {
                badge.className = 'badge bg-warning text-dark fs-11 basis-badge';
                badge.textContent = 'BED BASIS';
            }
            if (thQty) thQty.textContent = 'Beds Qty';
            if (thRate) thRate.textContent = 'Rate Per Bed (SAR)';
        } else if (basis === 'per_night') {
            if (badge) {
                badge.className = 'badge bg-info text-dark fs-11 basis-badge';
                badge.textContent = 'PER NIGHT BASIS';
            }
            if (thQty) thQty.textContent = 'Qty';
            if (thRate) thRate.textContent = 'Rate Per Night (SAR)';
        } else {
            if (badge) {
                badge.className = 'badge bg-primary fs-11 basis-badge';
                badge.textContent = 'ROOM BASIS';
            }
            if (thQty) thQty.textContent = 'Room Qty';
            if (thRate) thRate.textContent = 'Rate Per Room (SAR)';
        }

        const rows = card.querySelectorAll('.room-type-row');
        rows.forEach(row => {
            row.querySelector('.synced-hotel-name').value = hotelName;
            row.querySelector('.synced-hotel-id').value = hotelId;
            row.querySelector('.synced-basis').value = basis;
            row.querySelector('.synced-meal-plan').value = mp;
            row.querySelector('.synced-check-in').value = cIn;
            row.querySelector('.synced-check-out').value = cOut;
            row.querySelector('.synced-hijri-in').value = hijriIn;
            row.querySelector('.synced-hijri-out').value = hijriOut;
            row.querySelector('.synced-nights').value = nights;

            const unitText = row.querySelector('.qty-unit-text');
            if (unitText) {
                unitText.textContent = (basis === 'bed') ? 'Beds' : 'Rooms';
            }

            const rIdx = row.getAttribute('data-row-idx');
            recalcRowAmount(rIdx);
        });

        recalcHotelSubtotal(hIdx);
    }

    function recalcRowAmount(rIdx) {
        const row = document.getElementById(`room_row_${rIdx}`);
        if (!row) return;

        const basis = row.querySelector('.synced-basis')?.value || 'room';
        const qty = parseFloat(row.querySelector('.room-qty-input')?.value) || 0;
        const nights = parseInt(row.querySelector('.synced-nights')?.value, 10) || 1;
        const rate = parseFloat(row.querySelector('.room-rate-input')?.value) || 0;

        let amt = 0;
        if (basis === 'per_night') {
            amt = qty * (nights > 0 ? nights : 1) * rate;
        } else {
            amt = qty * rate;
        }

        const amtInp = row.querySelector('.room-amount-input');
        if (amtInp) {
            amtInp.value = amt.toFixed(2);
        }

        const hIdx = row.getAttribute('data-hotel-idx');
        recalcHotelSubtotal(hIdx);
    }

    function recalcHotelSubtotal(hIdx) {
        const card = document.getElementById(`hotel_card_${hIdx}`);
        if (!card) return;

        let subtotal = 0;
        card.querySelectorAll('.room-amount-input').forEach(inp => {
            subtotal += parseFloat(inp.value) || 0;
        });

        const subElem = document.getElementById(`hotel_subtotal_${hIdx}`);
        if (subElem) {
            subElem.textContent = 'SAR ' + subtotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        recalcTotals();
    }

    function recalcTotals() {
        let total = 0;
        document.querySelectorAll('.amount-input').forEach(inp => {
            total += parseFloat(inp.value) || 0;
        });
        const totalElem = document.getElementById('grandTotalAmount');
        if (totalElem) totalElem.value = total.toFixed(2);

        const recElem = document.getElementById('tillReceived');
        const rec = parseFloat(recElem ? recElem.value : 0) || 0;
        const bal = Math.max(0, total - rec);
        const balElem = document.getElementById('balanceRemaining');
        if (balElem) balElem.value = bal.toFixed(2);
    }

    function handleDateChange(inputElem, dateStr) {
        const card = inputElem.closest('.hotel-card');
        if (!card) return;
        const hIdx = card.getAttribute('data-hotel-idx');

        const checkInInp = card.querySelector('.hotel-check-in');
        const checkOutInp = card.querySelector('.hotel-check-out');
        const hijriInInp = card.querySelector('.hotel-hijri-in');
        const hijriOutInp = card.querySelector('.hotel-hijri-out');
        const nightsInp = card.querySelector('.hotel-nights-input');

        const isCheckIn = inputElem.classList.contains('hotel-check-in');
        const isCheckOut = inputElem.classList.contains('hotel-check-out');

        if (isCheckIn) {
            if (dateStr) {
                hijriInInp.value = gregorianToHijri(dateStr);
            }
            if (checkOutInp && checkOutInp.value) {
                const diff = dateDiffInDays(dateStr, checkOutInp.value);
                if (diff > 0) {
                    nightsInp.value = diff;
                } else {
                    const nights = parseInt(nightsInp.value, 10) || 1;
                    const newOut = addDaysToDate(dateStr, nights);
                    if (newOut) {
                        if (checkOutInp._flatpickr) {
                            checkOutInp._flatpickr.setDate(newOut, false);
                        } else {
                            checkOutInp.value = newOut;
                        }
                        hijriOutInp.value = gregorianToHijri(newOut);
                    }
                }
            } else if (dateStr) {
                const nights = parseInt(nightsInp.value, 10) || 1;
                const newOut = addDaysToDate(dateStr, nights);
                if (newOut) {
                    if (checkOutInp._flatpickr) {
                        checkOutInp._flatpickr.setDate(newOut, false);
                    } else {
                        checkOutInp.value = newOut;
                    }
                    hijriOutInp.value = gregorianToHijri(newOut);
                }
            }
        } else if (isCheckOut) {
            if (dateStr) {
                hijriOutInp.value = gregorianToHijri(dateStr);
            }
            if (checkInInp && checkInInp.value && dateStr) {
                const diff = dateDiffInDays(checkInInp.value, dateStr);
                if (diff > 0) {
                    nightsInp.value = diff;
                } else if (diff <= 0) {
                    alert('Check-Out date must be after Check-In date (' + checkInInp.value + ')');
                    nightsInp.value = 1;
                }
            }
        }

        syncHotelToRooms(hIdx);
    }

    function handleHijriDateChange(inputElem) {
        const card = inputElem.closest('.hotel-card');
        if (!card) return;
        const hIdx = card.getAttribute('data-hotel-idx');

        const checkInInp = card.querySelector('.hotel-check-in');
        const checkOutInp = card.querySelector('.hotel-check-out');
        const hijriInInp = card.querySelector('.hotel-hijri-in');
        const hijriOutInp = card.querySelector('.hotel-hijri-out');
        const nightsInp = card.querySelector('.hotel-nights-input');

        const isHijriIn = inputElem.classList.contains('hotel-hijri-in');
        const isHijriOut = inputElem.classList.contains('hotel-hijri-out');

        if (isHijriIn) {
            const hVal = (hijriInInp.value || '').trim();
            const gDate = hijriToGregorian(hVal);
            if (gDate) {
                if (checkInInp._flatpickr) {
                    checkInInp._flatpickr.setDate(gDate, false);
                } else {
                    checkInInp.value = gDate;
                }
                const nights = parseInt(nightsInp.value, 10) || 1;
                if (!checkOutInp.value || dateDiffInDays(gDate, checkOutInp.value) <= 0) {
                    const newOut = addDaysToDate(gDate, nights);
                    if (newOut) {
                        if (checkOutInp._flatpickr) {
                            checkOutInp._flatpickr.setDate(newOut, false);
                        } else {
                            checkOutInp.value = newOut;
                        }
                        hijriOutInp.value = gregorianToHijri(newOut);
                    }
                } else {
                    nightsInp.value = dateDiffInDays(gDate, checkOutInp.value);
                }
                syncHotelToRooms(hIdx);
            }
        } else if (isHijriOut) {
            const hVal = (hijriOutInp.value || '').trim();
            const gDate = hijriToGregorian(hVal);
            if (gDate) {
                if (checkOutInp._flatpickr) {
                    checkOutInp._flatpickr.setDate(gDate, false);
                } else {
                    checkOutInp.value = gDate;
                }
                if (checkInInp && checkInInp.value) {
                    const diff = dateDiffInDays(checkInInp.value, gDate);
                    if (diff > 0) {
                        nightsInp.value = diff;
                    }
                }
                syncHotelToRooms(hIdx);
            }
        }
    }

    function initFlatpickr(scope = document) {
        const inputs = scope.querySelectorAll('.flatpickr-date');
        inputs.forEach(inp => {
            if (typeof flatpickr !== 'undefined') {
                if (inp._flatpickr) inp._flatpickr.destroy();
                flatpickr(inp, {
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    onChange: function (selectedDates, dateStr) {
                        handleDateChange(inp, dateStr);
                    }
                });
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        initFlatpickr();

        const typeHotelClient = document.getElementById('type_hotel_client');
        const typeCompany = document.getElementById('type_company');
        const typeClient = document.getElementById('type_client');
        const hotelClientWrap = document.getElementById('hotelClientSelectWrap');
        const compWrap = document.getElementById('companySelectWrap');
        const clientWrap = document.getElementById('clientSelectWrap');
        const customerName = document.getElementById('customerName');
        const attnInput = document.getElementById('attnInput');
        const customerContact = document.getElementById('customerContact');
        const hotelClientSelect = document.getElementById('hotelClientSelect');
        const companySelect = document.getElementById('companySelect');
        const clientSelect = document.getElementById('clientSelect');

        function toggleCustomerType() {
            if (typeHotelClient && typeHotelClient.checked) {
                hotelClientWrap?.classList.remove('d-none');
                compWrap?.classList.add('d-none');
                clientWrap?.classList.add('d-none');
            } else if (typeCompany && typeCompany.checked) {
                hotelClientWrap?.classList.add('d-none');
                compWrap?.classList.remove('d-none');
                clientWrap?.classList.add('d-none');
            } else {
                hotelClientWrap?.classList.add('d-none');
                compWrap?.classList.add('d-none');
                clientWrap?.classList.remove('d-none');
            }
        }
        typeHotelClient?.addEventListener('change', toggleCustomerType);
        typeCompany?.addEventListener('change', toggleCustomerType);
        typeClient?.addEventListener('change', toggleCustomerType);

        hotelClientSelect?.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.value) {
                const company = opt.getAttribute('data-company') || '';
                const name = opt.getAttribute('data-name') || '';
                const contact = opt.getAttribute('data-contact') || '';
                customerName.value = company || name;
                if (attnInput) attnInput.value = name;
                if (customerContact) customerContact.value = contact;
            }
        });

        companySelect?.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.value) {
                customerName.value = opt.getAttribute('data-name') || opt.text;
                if (customerContact) customerContact.value = opt.getAttribute('data-contact') || '';
            }
        });

        clientSelect?.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.value) {
                customerName.value = opt.getAttribute('data-name') || opt.text;
                if (customerContact) customerContact.value = opt.getAttribute('data-contact') || '';
            }
        });

        // Quick Add Hotel Client AJAX Handler
        const quickClientForm = document.getElementById('quickAddHotelClientForm');
        if (quickClientForm) {
            quickClientForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const btn = document.getElementById('quickAddClientSubmitBtn');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

                const formData = new FormData(this);
                fetch('{{ route("hotel-clients.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="mdi mdi-check me-1"></i> Save &amp; Select';
                    if (data.success && data.client) {
                        const cl = data.client;
                        const label = cl.company_name ? `${cl.company_name} (${cl.name})` : cl.name;
                        const opt = document.createElement('option');
                        opt.value = cl.id;
                        opt.setAttribute('data-company', cl.company_name || '');
                        opt.setAttribute('data-name', cl.name);
                        opt.setAttribute('data-contact', cl.contact || '');
                        opt.textContent = label + (cl.contact ? ` - ${cl.contact}` : '');
                        hotelClientSelect.appendChild(opt);
                        hotelClientSelect.value = cl.id;
                        hotelClientSelect.dispatchEvent(new Event('change'));

                        bootstrap.Modal.getInstance(document.getElementById('quickAddHotelClientModal'))?.hide();
                        quickClientForm.reset();
                    } else {
                        alert(data.message || 'Error creating client');
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="mdi mdi-check me-1"></i> Save &amp; Select';
                    alert('Network error while creating hotel client.');
                });
            });
        }

        // Quick Add Hotel AJAX Handler
        let activeHotelTargetIdx = null;
        document.getElementById('itemsContainer').addEventListener('click', function (e) {
            const hotelBtn = e.target.closest('.open-quick-hotel-btn');
            if (hotelBtn) {
                activeHotelTargetIdx = hotelBtn.getAttribute('data-hotel-idx');
            }
        });

        const quickHotelForm = document.getElementById('quickAddHotelForm');
        if (quickHotelForm) {
            quickHotelForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const saveHotelBtn = document.getElementById('saveQuickHotelBtn');
                saveHotelBtn.disabled = true;
                saveHotelBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

                const formData = new FormData(this);

                fetch("{{ route('hotel.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    saveHotelBtn.disabled = false;
                    saveHotelBtn.innerHTML = '<i class="mdi mdi-check me-1"></i> Save &amp; Select Hotel';

                    if (data.success && data.hotel) {
                        const h = data.hotel;
                        hotelsList.push({ id: h.id, name: h.name, city: h.place });

                        // Update all hotel select dropdowns across all cards
                        document.querySelectorAll('.hotel-select').forEach(sel => {
                            const opt = document.createElement('option');
                            opt.value = h.name;
                            opt.setAttribute('data-id', h.id);
                            opt.textContent = `${h.name} (${h.place || 'Hotel'})`;
                            sel.appendChild(opt);
                        });

                        // Select in the card that initiated the modal
                        if (activeHotelTargetIdx !== null) {
                            const card = document.getElementById(`hotel_card_${activeHotelTargetIdx}`);
                            if (card) {
                                const sel = card.querySelector('.hotel-select');
                                const nameInp = card.querySelector('.hotel-name-input');
                                const idInp = card.querySelector('.hotel-id-input');
                                if (sel) sel.value = h.name;
                                if (nameInp) nameInp.value = h.name;
                                if (idInp) idInp.value = h.id;
                                syncHotelToRooms(activeHotelTargetIdx);
                            }
                        }

                        bootstrap.Modal.getInstance(document.getElementById('quickAddHotelModal'))?.hide();
                        quickHotelForm.reset();
                    } else {
                        alert(data.message || 'Error creating hotel');
                    }
                })
                .catch(err => {
                    saveHotelBtn.disabled = false;
                    saveHotelBtn.innerHTML = '<i class="mdi mdi-check me-1"></i> Save &amp; Select Hotel';
                    alert('Network error while creating hotel.');
                });
            });
        }

        // Add Hotel Button
        document.getElementById('addItemBtn').addEventListener('click', () => renderHotelCard());

        // Event delegation for items container
        const container = document.getElementById('itemsContainer');

        container.addEventListener('click', function (e) {
            // Quick Add Room Type button
            const quickBtn = e.target.closest('.quick-add-btn');
            if (quickBtn) {
                const hIdx = quickBtn.getAttribute('data-hotel-idx');
                const rType = quickBtn.getAttribute('data-type');
                addRoomTypeRow(hIdx, { room_type: rType, quantity: 1, rate_per_unit: 0 });
                syncHotelToRooms(hIdx);
                return;
            }

            // Custom Add Room Type button
            const customBtn = e.target.closest('.add-custom-room-btn');
            if (customBtn) {
                const hIdx = customBtn.getAttribute('data-hotel-idx');
                addRoomTypeRow(hIdx, { room_type: 'DOUBLE', quantity: 1, rate_per_unit: 0 });
                syncHotelToRooms(hIdx);
                return;
            }

            // Remove single room row button
            const remRoomBtn = e.target.closest('.remove-room-btn');
            if (remRoomBtn) {
                const row = remRoomBtn.closest('.room-type-row');
                const hIdx = row.getAttribute('data-hotel-idx');
                row.remove();
                recalcHotelSubtotal(hIdx);
                return;
            }

            // Remove entire hotel card button
            const remHotelBtn = e.target.closest('.remove-hotel-btn');
            if (remHotelBtn) {
                const card = remHotelBtn.closest('.hotel-card');
                if (card) {
                    card.remove();
                    recalcTotals();
                }
                return;
            }
        });

        container.addEventListener('change', function (e) {
            // Check-in or Check-out date input changed directly
            if (e.target.classList.contains('hotel-check-in') || e.target.classList.contains('hotel-check-out')) {
                handleDateChange(e.target, e.target.value);
            }

            // Hotel dropdown select
            if (e.target.classList.contains('hotel-select')) {
                const hIdx = e.target.getAttribute('data-hotel-idx');
                const card = document.getElementById(`hotel_card_${hIdx}`);
                if (card) {
                    const input = card.querySelector('.hotel-name-input');
                    const hiddenId = card.querySelector('.hotel-id-input');
                    const opt = e.target.options[e.target.selectedIndex];
                    if (opt && opt.value) {
                        input.value = opt.value;
                        hiddenId.value = opt.getAttribute('data-id') || '';
                        syncHotelToRooms(hIdx);
                    }
                }
            }

            // Basis select or Meal plan select
            if (e.target.classList.contains('hotel-basis-select') || e.target.classList.contains('hotel-meal-plan-select')) {
                const card = e.target.closest('.hotel-card');
                if (card) {
                    syncHotelToRooms(card.getAttribute('data-hotel-idx'));
                }
            }

            // Hijri Date input changed
            if (e.target.classList.contains('hotel-hijri-in') || e.target.classList.contains('hotel-hijri-out')) {
                handleHijriDateChange(e.target);
            }
        });

        container.addEventListener('focusout', function (e) {
            if (e.target.classList.contains('hotel-hijri-in') || e.target.classList.contains('hotel-hijri-out')) {
                handleHijriDateChange(e.target);
            }
        });

        container.addEventListener('input', function (e) {
            // Nights input changed directly
            if (e.target.classList.contains('hotel-nights-input')) {
                const card = e.target.closest('.hotel-card');
                if (card) {
                    const checkInInp = card.querySelector('.hotel-check-in');
                    const checkOutInp = card.querySelector('.hotel-check-out');
                    const hijriOutInp = card.querySelector('.hotel-hijri-out');
                    const nights = parseInt(e.target.value, 10);

                    if (checkInInp && checkInInp.value && nights > 0) {
                        const newOut = addDaysToDate(checkInInp.value, nights);
                        if (newOut) {
                            if (checkOutInp._flatpickr) {
                                checkOutInp._flatpickr.setDate(newOut, false);
                            } else {
                                checkOutInp.value = newOut;
                            }
                            if (hijriOutInp) {
                                hijriOutInp.value = gregorianToHijri(newOut);
                            }
                        }
                    }
                    syncHotelToRooms(card.getAttribute('data-hotel-idx'));
                }
            }

            // Hotel custom name typed
            if (e.target.classList.contains('hotel-name-input')) {
                const card = e.target.closest('.hotel-card');
                if (card) {
                    syncHotelToRooms(card.getAttribute('data-hotel-idx'));
                }
            }

            // Room quantity or rate changed
            if (e.target.classList.contains('room-qty-input') || e.target.classList.contains('room-rate-input')) {
                const row = e.target.closest('.room-type-row');
                if (row) {
                    recalcRowAmount(row.getAttribute('data-row-idx'));
                }
            } else if (e.target.classList.contains('room-amount-input')) {
                const row = e.target.closest('.room-type-row');
                if (row) {
                    recalcHotelSubtotal(row.getAttribute('data-hotel-idx'));
                }
            }
        });

        document.getElementById('tillReceived').addEventListener('input', recalcTotals);

        // Auto-fix empty quantity on blur
        container.addEventListener('blur', function (e) {
            if (e.target && e.target.classList.contains('room-qty-input')) {
                const val = parseInt(e.target.value, 10);
                if (isNaN(val) || val < 1) {
                    e.target.value = 1;
                    const row = e.target.closest('.room-type-row');
                    if (row) recalcRowAmount(row.getAttribute('data-row-idx'));
                }
            }
        }, true);

        // Ensure all quantities are valid integers on form submit
        const editForm = document.querySelector('form');
        if (editForm) {
            editForm.addEventListener('submit', function () {
                document.querySelectorAll('.room-qty-input').forEach(inp => {
                    const val = parseInt(inp.value, 10);
                    if (isNaN(val) || val < 1) {
                        inp.value = 1;
                    }
                });
            });
        }

        // Preload existing items grouped by hotel
        if (existingItems && existingItems.length > 0) {
            const hotelGroups = [];
            existingItems.forEach(item => {
                const key = `${(item.hotel_name || '').toUpperCase().trim()}_${item.check_in || ''}_${item.check_out || ''}`;
                let grp = hotelGroups.find(g => g.key === key);
                if (!grp) {
                    grp = {
                        key: key,
                        hotel_id: item.hotel_id,
                        hotel_name: item.hotel_name,
                        allotment_basis: item.allotment_basis,
                        meal_plan: item.meal_plan,
                        check_in: item.check_in,
                        check_out: item.check_out,
                        hijri_check_in: item.hijri_check_in,
                        hijri_check_out: item.hijri_check_out,
                        no_of_nights: item.no_of_nights,
                        room_types: []
                    };
                    hotelGroups.push(grp);
                }

                if (item.room_type && item.room_type.includes(',')) {
                    const parts = item.room_type.split(',');
                    parts.forEach(p => {
                        grp.room_types.push({
                            room_type: p.trim(),
                            quantity: item.quantity,
                            rate_per_unit: item.rate_per_unit,
                            amount: item.amount
                        });
                    });
                } else {
                    grp.room_types.push({
                        room_type: item.room_type,
                        quantity: item.quantity,
                        rate_per_unit: item.rate_per_unit,
                        amount: item.amount
                    });
                }
            });

            hotelGroups.forEach(grp => renderHotelCard(grp));
        } else {
            renderHotelCard();
        }
    });

</script>
@endsection
