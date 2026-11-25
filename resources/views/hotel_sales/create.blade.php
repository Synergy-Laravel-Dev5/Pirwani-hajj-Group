@extends('layout.master')
@section('title', 'Create Hotel Sale Invoice / Reservation')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .item-row {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 10px;
        margin-bottom: 10px;
        transition: all 0.2s;
    }
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
                        <i class="mdi mdi-plus-circle me-1"></i> Create Hotel Sale Invoice & Reservation
                    </h4>
                    <p class="text-muted fs-13 mb-0">
                        Issue official Hajj/Umrah hotel reservation confirmation vouchers to Agents (B2B) or Direct Clients (B2C) on Room or Bed basis.
                    </p>
                </div>
                <div>
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

            <form action="{{ route('hotel-sales.store') }}" method="POST" id="hotelSaleForm">
                @csrf

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
                                        <input class="form-check-input" type="radio" name="sale_for" id="type_hotel_client" value="hotel_client" checked>
                                        <label class="form-check-label fw-bold text-dark" for="type_hotel_client">Hotel Client</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sale_for" id="type_company" value="company">
                                        <label class="form-check-label fw-bold text-dark" for="type_company">Supplier/Agent</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sale_for" id="type_client" value="client">
                                        <label class="form-check-label fw-bold text-dark" for="type_client">Hajj Client</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Hotel Client Selector (Default) -->
                            <div class="col-md-4" id="hotelClientSelectWrap">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fs-13 fw-semibold mb-0">Select Hotel Client <span class="text-danger">*</span></label>
                                    <button type="button" class="btn btn-link p-0 text-primary fs-11 text-decoration-none fw-semibold" data-bs-toggle="modal" data-bs-target="#quickAddHotelClientModal">
                                        <i class="mdi mdi-plus-circle me-1"></i>+ New Client
                                    </button>
                                </div>
                                <select name="hotel_client_id" id="hotelClientSelect" class="form-select form-select-sm">
                                    <option value="">-- Choose Hotel Client --</option>
                                    @foreach($hotelClients as $hc)
                                        <option value="{{ $hc->id }}" data-company="{{ $hc->company_name }}" data-name="{{ $hc->name }}" data-contact="{{ $hc->contact }}">
                                            {{ $hc->company_name ? $hc->company_name . ' (' . $hc->name . ')' : $hc->name }} @if($hc->contact) - {{ $hc->contact }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 d-none" id="companySelectWrap">
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
                                            <option value="{{ $sup->id }}" data-name="{{ $sup->name }}" data-contact="{{ $sup->phone ?: ($sup->mobile ?: '') }}">
                                                {{ $sup->name }} @if($sup->city) ({{ $sup->city }}) @endif
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    @if(count($companies) > 0)
                                        <optgroup label="Internal Companies / Branches">
                                            @foreach($companies as $comp)
                                                <option value="{{ $comp->id }}" data-name="{{ $comp->company_name }}" data-contact="{{ $comp->phone ?? ($comp->contactNumbers->first()->contact_number ?? '') }}">
                                                    {{ $comp->company_name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-4 d-none" id="clientSelectWrap">
                                <label class="form-label fs-13 fw-semibold">Select Hajj Client <span class="text-danger">*</span></label>
                                <select name="client_id" id="clientSelect" class="form-select form-select-sm">
                                    <option value="">-- Choose Client --</option>
                                    @foreach($clients as $cl)
                                        <option value="{{ $cl->id }}" data-name="{{ $cl->name }}" data-contact="{{ $cl->phone ?? '' }}">
                                            {{ $cl->name }} @if($cl->phone) ({{ $cl->phone }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fs-13 fw-semibold">Customer / Client Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" id="customerName" class="form-control form-control-sm"
                                    placeholder="Enter or auto-filled client/agent name" value="{{ old('customer_name') }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Invoice Ref / Voucher #</label>
                                <input type="text" name="invoice_number" class="form-control form-control-sm fw-bold"
                                    value="{{ old('invoice_number', $nextInvoiceNumber) }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Invoice Date <span class="text-danger">*</span></label>
                                <input type="text" name="invoice_date" class="form-control form-control-sm flatpickr-date"
                                    value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Reservation Title / Season</label>
                                <input type="text" name="reservation_title" class="form-control form-control-sm"
                                    value="{{ old('reservation_title', 'HAJJ RESERVATION ' . (date('Y') + 1)) }}"
                                    placeholder="e.g. HAJJ RESERVATION 1448/2027">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fs-13 fw-semibold">Currency</label>
                                <select name="currency" class="form-select form-select-sm">
                                    <option value="SAR" selected>SAR (Saudi Riyal)</option>
                                    <option value="PKR">PKR (Pak Rupee)</option>
                                    <option value="USD">USD (US Dollar)</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Attn. (Attention Person)</label>
                                <input type="text" name="attn" id="attnInput" class="form-control form-control-sm"
                                    placeholder="Contact Person / Manager Name" value="{{ old('attn') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Contact Details</label>
                                <input type="text" name="contact" id="customerContact" class="form-control form-control-sm"
                                    placeholder="Phone numbers / WhatsApp" value="{{ old('contact') }}">
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
                            <span class="fs-12 text-muted">
                                Add hotels with individual room types (Double, Triple, Quad, etc.) having separate quantities and rates.
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-info btn-sm" id="loadTemplateBtn">
                                <i class="mdi mdi-lightning-bolt me-1"></i> Quick Load Hajj Template
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" id="addItemBtn">
                                <i class="mdi mdi-plus-circle me-1"></i> + Add Hotel
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div id="itemsContainer">
                            <!-- Rows added dynamically via JS -->
                        </div>

                        <!-- Overall Summary Row -->
                        <div class="card bg-light border-0 mt-3 p-3">
                            <div class="row g-3 align-items-center justify-content-end">
                                <div class="col-md-3">
                                    <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">TOTAL AMOUNT</label>
                                    <input type="number" step="0.01" name="total_amount" id="grandTotalAmount"
                                        class="form-control form-control-sm fw-bold fs-15 text-primary text-end bg-white" readonly value="0.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fs-12 fw-bold text-uppercase text-success mb-1">TILL RECEIVED (Advance)</label>
                                    <input type="number" step="0.01" name="till_received" id="tillReceived"
                                        class="form-control form-control-sm fw-bold fs-15 text-success text-end" value="0.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fs-12 fw-bold text-uppercase text-danger mb-1">BALANCE REMAINING</label>
                                    <input type="number" step="0.01" name="balance_remaining" id="balanceRemaining"
                                        class="form-control form-control-sm fw-bold fs-15 text-danger text-end bg-white" readonly value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Terms & Cancellation & CEO Signature -->
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
                                <textarea name="terms_conditions" rows="5" class="form-control form-control-sm fs-12 font-monospace">{{ old('terms_conditions', $defaultTerms) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold">Cancellation Policy</label>
                                <textarea name="cancellation_policy" rows="5" class="form-control form-control-sm fs-12 font-monospace">{{ old('cancellation_policy', $defaultCancellation) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold">Payment Procedure</label>
                                <textarea name="payment_procedure" rows="3" class="form-control form-control-sm fs-12 font-monospace">{{ old('payment_procedure', $defaultPayment) }}</textarea>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">C.E.O / Signatory Name</label>
                                <input type="text" name="ceo_name" class="form-control form-control-sm"
                                    value="{{ old('ceo_name', 'MUHAMMAD YOUSUF PIRWANI') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Signatory Title</label>
                                <input type="text" name="ceo_title" class="form-control form-control-sm"
                                    value="{{ old('ceo_title', 'C.E.O,') }}">
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="fs-13 text-muted">
                                <i class="mdi mdi-check-decagram text-success me-1"></i>
                                Clicking save will generate the printable confirmation voucher.
                            </span>
                            <div class="d-flex gap-2">
                                <a href="{{ route('hotel-sales.index') }}" class="btn btn-secondary btn-sm px-3">Cancel</a>
                                <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold">
                                    <i class="mdi mdi-check-circle me-1"></i> Save & Generate Reservation Voucher
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

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    const hotelsList = @json($hotels);
    let hotelCardIndex = 0;
    let globalItemIndex = 0;

    const HIJRI_MONTH_NAMES = {
        1: 'MUHARRAM', 2: 'SAFAR', 3: 'RABI-I', 4: 'RABI-II',
        5: 'JAMADI-I', 6: 'JAMADI-II', 7: 'RAJAB', 8: 'SHABAN',
        9: 'RAMADAN', 10: 'SHAWWAL', 11: 'ZILQAIDA', 12: 'ZILHAJJ'
    };

    function kuwaitiHijri(year, month, day) {
        let jd;
        if ((year > 1582) || ((year === 1582) && (month > 10)) || ((year === 1582) && (month === 10) && (day > 14))) {
            jd = Math.floor((1461 * (year + 4800 + Math.floor((month - 14) / 12))) / 4) +
                 Math.floor((367 * (month - 2 - 12 * (Math.floor((month - 14) / 12)))) / 12) -
                 Math.floor((3 * Math.floor((year + 4900 + Math.floor((month - 14) / 12)) / 100)) / 4) +
                 day - 32075;
        } else {
            jd = 367 * year - Math.floor((7 * (year + 5001 + Math.floor((month - 9) / 7))) / 4) +
                 Math.floor((275 * month) / 9) + day + 1729777;
        }

        let l = jd - 1948440 + 10632;
        const n = Math.floor((l - 1) / 10631);
        l = l - 10631 * n + 354;
        const j = Math.floor((10985 - l) / 5316) * Math.floor((50 * l) / 17719) +
                  Math.floor(l / 5670) * Math.floor((43 * l) / 15238);
        l = l - Math.floor((30 - j) / 15) * Math.floor((17719 * j) / 50) -
            Math.floor(j / 16) * Math.floor((15238 * j) / 43) + 29;
        const m = Math.floor((24 * l) / 709);
        const d = l - Math.floor((709 * m) / 24);

        const dd = String(d).padStart(2, '0');
        const monthName = HIJRI_MONTH_NAMES[m] || ('M' + m);
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

            // 1. Try modern browser Intl API (Saudi Umm Al-Qura)
            if (typeof Intl !== 'undefined' && Intl.DateTimeFormat) {
                try {
                    const dateObj = new Date(Date.UTC(y, m - 1, d, 12, 0, 0));
                    const formatter = new Intl.DateTimeFormat('en-u-ca-islamic-umalqura-nu-latn', {
                        day: 'numeric',
                        month: 'numeric',
                        year: 'numeric'
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

            // 2. Fallback to Kuwaiti mathematical formula
            return kuwaitiHijri(y, m, d);
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
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Hotel Name <span class="text-danger">*</span></label>
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

        // Add Hotel Button
        document.getElementById('addItemBtn').addEventListener('click', () => renderHotelCard());

        // Quick Load Sample Hajj Template matching user contract
        document.getElementById('loadTemplateBtn').addEventListener('click', function () {
            if (confirm('Load sample contract with Pullman Zamzam, Swissôtel, Nusk Al Hijra and Nusk Al Madinah (545 Beds)?')) {
                document.getElementById('itemsContainer').innerHTML = '';
                hotelCardIndex = 0;
                globalItemIndex = 0;

                renderHotelCard({
                    hotel_name: 'PULLMAN ZAMZAM',
                    allotment_basis: 'room',
                    meal_plan: 'HALF BOARD',
                    check_in: '2027-05-28',
                    check_out: '2027-06-06',
                    hijri_check_in: '22-ZILQAIDA',
                    hijri_check_out: '01-ZILHAJJ',
                    no_of_nights: 9,
                    room_types: [
                        { room_type: 'DOUBLE', quantity: 30, rate_per_unit: 0 },
                        { room_type: 'TRIPLE', quantity: 10, rate_per_unit: 0 },
                        { room_type: 'QUAD', quantity: 20, rate_per_unit: 0 }
                    ]
                });

                renderHotelCard({
                    hotel_name: 'SWISOTEL',
                    allotment_basis: 'room',
                    meal_plan: 'HALF BOARD',
                    check_in: '2027-06-06',
                    check_out: '2027-06-09',
                    hijri_check_in: '01-ZILHAJJ',
                    hijri_check_out: '04-ZILHAJJ',
                    no_of_nights: 3,
                    room_types: [
                        { room_type: 'DOUBLE', quantity: 10, rate_per_unit: 0 },
                        { room_type: 'TRIPLE', quantity: 4, rate_per_unit: 0 },
                        { room_type: 'QUAD', quantity: 3, rate_per_unit: 0 }
                    ]
                });

                renderHotelCard({
                    hotel_name: 'NUSK AL HIJRA',
                    allotment_basis: 'room',
                    meal_plan: 'FULL BOARD',
                    check_in: '2027-06-19',
                    check_out: '2027-06-22',
                    hijri_check_in: '14-ZILHAJJ',
                    hijri_check_out: '17-ZILHAJJ',
                    no_of_nights: 3,
                    room_types: [
                        { room_type: 'DOUBLE', quantity: 30, rate_per_unit: 0 },
                        { room_type: 'TRIPLE', quantity: 10, rate_per_unit: 0 },
                        { room_type: 'QUAD', quantity: 20, rate_per_unit: 0 }
                    ]
                });

                renderHotelCard({
                    hotel_name: 'NUSK AL MADINAH',
                    allotment_basis: 'bed',
                    meal_plan: 'ROOM ONLY',
                    check_in: '2027-06-19',
                    check_out: '2027-06-28',
                    hijri_check_in: '14-ZILHAJJ',
                    hijri_check_out: '23-ZILHAJJ',
                    no_of_nights: 9,
                    room_types: [
                        { room_type: 'BED', quantity: 545, rate_per_unit: 0 }
                    ]
                });
            }
        });

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
        const mainForm = document.querySelector('form');
        if (mainForm) {
            mainForm.addEventListener('submit', function () {
                document.querySelectorAll('.room-qty-input').forEach(inp => {
                    const val = parseInt(inp.value, 10);
                    if (isNaN(val) || val < 1) {
                        inp.value = 1;
                    }
                });
            });
        }

        // Render from old input if validation failed, otherwise default card
        const oldItems = @json(old('items', []));
        if (oldItems && Object.keys(oldItems).length > 0) {
            const rawList = Array.isArray(oldItems) ? oldItems : Object.values(oldItems);
            const hotelGroups = [];
            rawList.forEach(item => {
                if (!item) return;
                const hName = item.hotel_name || '';
                let grp = hotelGroups.find(g => g.hotel_name === hName && g.check_in === item.check_in && g.check_out === item.check_out);
                if (!grp) {
                    grp = {
                        hotel_name: hName,
                        hotel_id: item.hotel_id,
                        allotment_basis: item.allotment_basis || 'room',
                        meal_plan: item.meal_plan || 'HALF BOARD',
                        check_in: item.check_in,
                        check_out: item.check_out,
                        hijri_check_in: item.hijri_check_in,
                        hijri_check_out: item.hijri_check_out,
                        no_of_nights: item.no_of_nights || 1,
                        room_types: []
                    };
                    hotelGroups.push(grp);
                }
                grp.room_types.push({
                    room_type: item.room_type || 'DOUBLE',
                    quantity: item.quantity || 1,
                    rate_per_unit: item.rate_per_unit || 0,
                    amount: item.amount
                });
            });
            hotelGroups.forEach(grp => renderHotelCard(grp));
        } else {
            renderHotelCard({
                hotel_name: 'SWISSOTEL MAKKAH',
                allotment_basis: 'room',
                meal_plan: 'HALF BOARD',
                no_of_nights: 5,
                room_types: [
                    { room_type: 'DOUBLE', quantity: 10, rate_per_unit: 0 }
                ]
            });
        }
    });

</script>
@endsection
