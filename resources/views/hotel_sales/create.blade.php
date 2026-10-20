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
    .item-row:hover {
        background: #ffffff;
        border-color: #001030;
        box-shadow: 0 2px 8px rgba(0,16,48,0.06);
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
                                <div class="d-flex gap-3 mt-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sale_for" id="type_company" value="company" checked>
                                        <label class="form-check-label fw-bold text-dark" for="type_company">Supplier / Agent</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sale_for" id="type_client" value="client">
                                        <label class="form-check-label fw-bold text-dark" for="type_client">Direct Client</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4" id="companySelectWrap">
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
                                <label class="form-label fs-13 fw-semibold">Select Client <span class="text-danger">*</span></label>
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
                                <input type="text" name="attn" class="form-control form-control-sm"
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
                                Add hotels sold to Agent/Client on <strong>Room Basis</strong> (Double, Triple, Quad) or <strong>Bed Basis</strong> (e.g. 545 Beds).
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-info btn-sm" id="loadTemplateBtn">
                                <i class="mdi mdi-lightning-bolt me-1"></i> Quick Load Hajj Template
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" id="addItemBtn">
                                <i class="mdi mdi-plus"></i> Add Hotel Row
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

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    const hotelsList = @json($hotels);
    let itemIndex = 0;

    function renderItemRow(data = {}) {
        const idx = itemIndex++;
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
        const rate = data.rate_per_unit || 0;
        const amount = data.amount || (qty * nights * rate);

        let hotelOptions = '<option value="">-- Select Hotel or Type Custom --</option>';
        hotelsList.forEach(h => {
            const sel = (h.name === defaultHotel) ? 'selected' : '';
            hotelOptions += `<option value="${h.name}" data-id="${h.id}" ${sel}>${h.name} (${h.city || h.place || 'Hotel'})</option>`;
        });

        const html = `
            <div class="item-row" id="row_${idx}">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                    <span class="badge ${basis === 'bed' ? 'bg-warning text-dark' : 'bg-primary'} fs-11">
                        Row #${idx + 1} • <span class="row-basis-badge">${basis.toUpperCase()} BASIS</span>
                    </span>
                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 fs-12 remove-row-btn" data-row="${idx}">
                        <i class="mdi mdi-close"></i> Remove
                    </button>
                </div>
                <div class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Hotel Name <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm hotel-select mb-1" data-row="${idx}">
                            ${hotelOptions}
                        </select>
                        <input type="text" name="items[${idx}][hotel_name]" class="form-control form-control-sm hotel-input"
                            placeholder="Type or select hotel name" value="${defaultHotel}" required id="hotel_input_${idx}">
                        <input type="hidden" name="items[${idx}][hotel_id]" id="hotel_id_${idx}" value="${data.hotel_id || ''}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Allotment Basis</label>
                        <select name="items[${idx}][allotment_basis]" class="form-select form-select-sm basis-select" data-row="${idx}">
                            <option value="room" ${basis === 'room' ? 'selected' : ''}>Room Basis</option>
                            <option value="bed" ${basis === 'bed' ? 'selected' : ''}>Bed Basis</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Room / Bed Type</label>
                        <select name="items[${idx}][room_type]" class="form-select form-select-sm room-type-select" id="room_type_${idx}">
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
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold" id="qty_lbl_${idx}">${basis === 'bed' ? 'Beds Qty' : 'Room Qty'}</label>
                        <input type="number" min="1" name="items[${idx}][quantity]" class="form-control form-control-sm text-center fw-bold qty-input calc-trigger"
                            value="${qty}" required data-row="${idx}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Meal Plan (MP)</label>
                        <select name="items[${idx}][meal_plan]" class="form-select form-select-sm">
                            <option value="HALF BOARD" ${mp === 'HALF BOARD' ? 'selected' : ''}>HALF BOARD</option>
                            <option value="FULL BOARD" ${mp === 'FULL BOARD' ? 'selected' : ''}>FULL BOARD</option>
                            <option value="ROOM ONLY" ${mp === 'ROOM ONLY' ? 'selected' : ''}>ROOM ONLY</option>
                            <option value="BED & BREAKFAST" ${mp === 'BED & BREAKFAST' ? 'selected' : ''}>BED & BREAKFAST</option>
                            <option value="SUHOOR" ${mp === 'SUHOOR' ? 'selected' : ''}>SUHOOR</option>
                            <option value="IFTAR" ${mp === 'IFTAR' ? 'selected' : ''}>IFTAR</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Check-In</label>
                        <input type="text" name="items[${idx}][hijri_check_in]" class="form-control form-control-sm mb-1 text-primary fw-semibold"
                            placeholder="Hijri (e.g. 22-ZILQAIDA)" value="${hijriIn}">
                        <input type="text" name="items[${idx}][check_in]" class="form-control form-control-sm flatpickr-date"
                            placeholder="Calendar Date" value="${cIn}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Check-Out</label>
                        <input type="text" name="items[${idx}][hijri_check_out]" class="form-control form-control-sm mb-1 text-primary fw-semibold"
                            placeholder="Hijri (e.g. 01-ZILHAJJ)" value="${hijriOut}">
                        <input type="text" name="items[${idx}][check_out]" class="form-control form-control-sm flatpickr-date"
                            placeholder="Calendar Date" value="${cOut}">
                    </div>

                    <div class="col-md-1">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Nights</label>
                        <input type="number" min="1" name="items[${idx}][no_of_nights]" class="form-control form-control-sm text-center nights-input calc-trigger"
                            value="${nights}" required data-row="${idx}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Rate Per ${basis === 'bed' ? 'Bed' : 'Room'}</label>
                        <input type="number" step="0.01" min="0" name="items[${idx}][rate_per_unit]" class="form-control form-control-sm text-end rate-input calc-trigger"
                            value="${rate}" placeholder="0.00" data-row="${idx}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fs-11 text-muted mb-0 fw-semibold">Amount (SAR)</label>
                        <input type="number" step="0.01" min="0" name="items[${idx}][amount]" class="form-control form-control-sm text-end fw-bold amount-input"
                            value="${amount}" id="amount_${idx}">
                    </div>
                </div>
            </div>
        `;

        document.getElementById('itemsContainer').insertAdjacentHTML('beforeend', html);
        initFlatpickr();
        recalcTotals();
    }

    function initFlatpickr() {
        flatpickr(".flatpickr-date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "m/d/Y",
            allowInput: true
        });
    }

    function recalcTotals() {
        let total = 0;
        document.querySelectorAll('.amount-input').forEach(inp => {
            total += parseFloat(inp.value) || 0;
        });
        document.getElementById('grandTotalAmount').value = total.toFixed(2);

        const rec = parseFloat(document.getElementById('tillReceived').value) || 0;
        const bal = Math.max(0, total - rec);
        document.getElementById('balanceRemaining').value = bal.toFixed(2);
    }

    document.addEventListener("DOMContentLoaded", function () {
        initFlatpickr();

        // Customer switch
        const typeCompany = document.getElementById('type_company');
        const typeClient = document.getElementById('type_client');
        const compWrap = document.getElementById('companySelectWrap');
        const clientWrap = document.getElementById('clientSelectWrap');
        const customerName = document.getElementById('customerName');
        const customerContact = document.getElementById('customerContact');
        const companySelect = document.getElementById('companySelect');
        const clientSelect = document.getElementById('clientSelect');

        function toggleCustomerType() {
            if (typeCompany.checked) {
                compWrap.classList.remove('d-none');
                clientWrap.classList.add('d-none');
            } else {
                compWrap.classList.add('d-none');
                clientWrap.classList.remove('d-none');
            }
        }
        typeCompany.addEventListener('change', toggleCustomerType);
        typeClient.addEventListener('change', toggleCustomerType);

        companySelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.value) {
                customerName.value = opt.getAttribute('data-name') || opt.text;
                customerContact.value = opt.getAttribute('data-contact') || '';
            }
        });

        clientSelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.value) {
                customerName.value = opt.getAttribute('data-name') || opt.text;
                customerContact.value = opt.getAttribute('data-contact') || '';
            }
        });

        // Add Item
        document.getElementById('addItemBtn').addEventListener('click', () => renderItemRow());

        // Quick Load Sample Hajj Template matching user contract
        document.getElementById('loadTemplateBtn').addEventListener('click', function () {
            if (confirm('Load sample contract with Pullman Zamzam, Swissôtel, Nusk Al Hijra and Nusk Al Madinah (545 Beds)?')) {
                document.getElementById('itemsContainer').innerHTML = '';
                itemIndex = 0;

                const sampleRows = [
                    { hotel_name: 'PULLMAN ZAMZAM', allotment_basis: 'room', room_type: 'DOUBLE', quantity: 30, meal_plan: 'HALF BOARD', hijri_check_in: '22-ZILQAIDA', hijri_check_out: '01-ZILHAJJ', no_of_nights: 9, rate_per_unit: 0 },
                    { hotel_name: 'PULLMAN ZAMZAM', allotment_basis: 'room', room_type: 'TRIPLE', quantity: 10, meal_plan: 'HALF BOARD', hijri_check_in: '22-ZILQAIDA', hijri_check_out: '01-ZILHAJJ', no_of_nights: 9, rate_per_unit: 0 },
                    { hotel_name: 'PULLMAN ZAMZAM', allotment_basis: 'room', room_type: 'QUAD', quantity: 20, meal_plan: 'HALF BOARD', hijri_check_in: '22-ZILQAIDA', hijri_check_out: '01-ZILHAJJ', no_of_nights: 9, rate_per_unit: 0 },
                    { hotel_name: 'SWISOTEL', allotment_basis: 'room', room_type: 'DOUBLE', quantity: 10, meal_plan: 'HALF BOARD', hijri_check_in: '01-ZILHAJJ', hijri_check_out: '04-ZILHAJJ', no_of_nights: 3, rate_per_unit: 0 },
                    { hotel_name: 'SWISOTEL', allotment_basis: 'room', room_type: 'TRIPLE', quantity: 4, meal_plan: 'HALF BOARD', hijri_check_in: '01-ZILHAJJ', hijri_check_out: '04-ZILHAJJ', no_of_nights: 3, rate_per_unit: 0 },
                    { hotel_name: 'SWISOTEL', allotment_basis: 'room', room_type: 'QUAD', quantity: 3, meal_plan: 'HALF BOARD', hijri_check_in: '01-ZILHAJJ', hijri_check_out: '04-ZILHAJJ', no_of_nights: 3, rate_per_unit: 0 },
                    { hotel_name: 'NUSK AL HIJRA', allotment_basis: 'room', room_type: 'DOUBLE', quantity: 30, meal_plan: 'FULL BOARD', hijri_check_in: '14-ZILHAJJ', hijri_check_out: '17-ZILHAJJ', no_of_nights: 3, rate_per_unit: 0 },
                    { hotel_name: 'NUSK AL HIJRA', allotment_basis: 'room', room_type: 'TRIPLE', quantity: 10, meal_plan: 'FULL BOARD', hijri_check_in: '14-ZILHAJJ', hijri_check_out: '17-ZILHAJJ', no_of_nights: 3, rate_per_unit: 0 },
                    { hotel_name: 'NUSK AL HIJRA', allotment_basis: 'room', room_type: 'QUAD', quantity: 20, meal_plan: 'FULL BOARD', hijri_check_in: '14-ZILHAJJ', hijri_check_out: '17-ZILHAJJ', no_of_nights: 3, rate_per_unit: 0 },
                    { hotel_name: 'NUSK AL MADINAH', allotment_basis: 'bed', room_type: 'BED', quantity: 545, meal_plan: 'ROOM ONLY', hijri_check_in: '14-ZILHAJJ', hijri_check_out: '23-ZILHAJJ', no_of_nights: 9, rate_per_unit: 0 },
                ];

                sampleRows.forEach(r => renderItemRow(r));
            }
        });

        // Event delegation for items container
        const container = document.getElementById('itemsContainer');

        container.addEventListener('click', function (e) {
            const btn = e.target.closest('.remove-row-btn');
            if (btn) {
                const rIdx = btn.getAttribute('data-row');
                const rowElem = document.getElementById(`row_${rIdx}`);
                if (rowElem) {
                    rowElem.remove();
                    recalcTotals();
                }
            }
        });

        container.addEventListener('change', function (e) {
            // Hotel select
            if (e.target.classList.contains('hotel-select')) {
                const rIdx = e.target.getAttribute('data-row');
                const input = document.getElementById(`hotel_input_${rIdx}`);
                const hiddenId = document.getElementById(`hotel_id_${rIdx}`);
                const opt = e.target.options[e.target.selectedIndex];
                if (opt && opt.value) {
                    input.value = opt.value;
                    hiddenId.value = opt.getAttribute('data-id') || '';
                }
            }

            // Basis select
            if (e.target.classList.contains('basis-select')) {
                const rIdx = e.target.getAttribute('data-row');
                const basis = e.target.value;
                const roomTypeSelect = document.getElementById(`room_type_${rIdx}`);
                const qtyLbl = document.getElementById(`qty_lbl_${rIdx}`);
                const badge = document.querySelector(`#row_${rIdx} .row-basis-badge`);

                if (basis === 'bed') {
                    roomTypeSelect.value = 'BED';
                    qtyLbl.textContent = 'Beds Qty';
                    if (badge) badge.textContent = 'BED BASIS';
                } else {
                    if (roomTypeSelect.value === 'BED') roomTypeSelect.value = 'DOUBLE';
                    qtyLbl.textContent = 'Room Qty';
                    if (badge) badge.textContent = 'ROOM BASIS';
                }
            }
        });

        container.addEventListener('input', function (e) {
            if (e.target.classList.contains('calc-trigger')) {
                const rIdx = e.target.getAttribute('data-row');
                const row = document.getElementById(`row_${rIdx}`);
                if (row) {
                    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                    const nights = parseFloat(row.querySelector('.nights-input').value) || 0;
                    const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
                    const amt = qty * nights * rate;
                    row.querySelector('.amount-input').value = amt.toFixed(2);
                    recalcTotals();
                }
            } else if (e.target.classList.contains('amount-input')) {
                recalcTotals();
            }
        });

        document.getElementById('tillReceived').addEventListener('input', recalcTotals);

        // Render at least one row by default
        renderItemRow();
    });
</script>
@endsection
