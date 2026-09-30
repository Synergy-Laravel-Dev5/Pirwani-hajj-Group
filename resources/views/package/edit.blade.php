@extends('layout.master')

@section('title', 'Edit Package')

@section('content')
    <style>
        .accommodation-row {
            position: relative;
        }
        .accommodation-row:not(:first-child) {
            margin-top: 25px !important;
        }
        .section-subhead {
            font-size: 14px;
            font-weight: 700;
            color: #071527;
            border-bottom: 2px solid #C59A27;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
    </style>

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-center justify-content-between">
                    <h4 class="fs-18 fw-semibold m-0">Package / <strong>Edit #{{ $package->id }}</strong></h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('package.show', $package->id) }}" class="btn btn-info btn-sm text-white">
                            <i class="mdi mdi-eye me-1"></i> View Brochure
                        </a>
                        <a href="{{ route('package.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="mdi mdi-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>

                @if (isset($errors) && $errors->any())
                    <div class="alert alert-danger">
                        <ul class="m-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('package.update', $package->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <ul class="nav nav-pills mb-3" id="packageTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active fw-bold" data-bs-toggle="pill" data-bs-target="#tab-package" type="button">
                                📋 PACKAGE & DURATION
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#tab-camp" type="button">
                                ⛺ CAMP & PRICING
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#tab-qurbani" type="button">
                                🐑 QURBANI
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#tab-accommodation" type="button">
                                🏨 ACCOMMODATION & ITINERARY
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#tab-terms" type="button">
                                📜 INCLUSIONS & INSTRUCTIONS
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#tab-transport" type="button">
                                🚌 TRANSPORT & FLIGHTS
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#tab-training" type="button">
                                🎁 TRAINING & GIFTS
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content card">

                        {{-- ═════════════════════════════════════════
                             TAB 1: PACKAGE & DURATION
                        ═════════════════════════════════════════ --}}
                        <div class="tab-pane fade show active card-body" id="tab-package">
                            <h5 class="section-subhead">📋 Package Basic Info & Duration</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Company</label>
                                    <select name="company_id" class="form-select">
                                        <option value="">-- Select Company --</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}" {{ old('company_id', $package->company_id) == $company->id ? 'selected' : '' }}>
                                                {{ $company->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Package #</label>
                                    <input type="text" name="package_number" class="form-control" placeholder="e.g. PKG-2027-01" value="{{ old('package_number', $package->package_number) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Package Title</label>
                                    <input type="text" name="package_title" class="form-control" placeholder="e.g. HAJJ PACKAGE 1448 - 2027" value="{{ old('package_title', $package->package_title ?? 'HAJJ PACKAGE 1448 - 2027') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Package Code</label>
                                    <input type="text" name="code" class="form-control" placeholder="e.g. HJ-27-LS" value="{{ old('code', $package->code) }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Stay Type</label>
                                    <select name="stay_type" class="form-select">
                                        <option value="LONG STAY" {{ old('stay_type', $package->stay_type) == 'LONG STAY' ? 'selected' : '' }}>LONG STAY</option>
                                        <option value="SHORT STAY" {{ old('stay_type', $package->stay_type) == 'SHORT STAY' ? 'selected' : '' }}>SHORT STAY</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Stay Duration</label>
                                    <input type="text" name="stay_duration" class="form-control" placeholder="e.g. 29 - 30 DAYS" value="{{ old('stay_duration', $package->stay_duration ?? ($package->days ? $package->days . ' DAYS' : '29 - 30 DAYS')) }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">Total Days</label>
                                    <input type="number" min="1" name="days" class="form-control" placeholder="30" value="{{ old('days', $package->days ?? 30) }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">Hijri Year</label>
                                    <input type="text" name="hijri_year" class="form-control" placeholder="1448" value="{{ old('hijri_year', $package->hijri_year ?? '1448') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">Gregorian Year</label>
                                    <input type="text" name="gregorian_year" class="form-control" placeholder="2027" value="{{ old('gregorian_year', $package->gregorian_year ?? ($package->year ?? '2027')) }}">
                                </div>
                            </div>

                            <h5 class="section-subhead">✈️ Flight Route & Dates (For Brochure Header)</h5>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Departure Date & Hijri</label>
                                    <input type="text" name="departure_date_str" class="form-control" placeholder="e.g. 28-29 Apr / 21-22 Zil Qadh" value="{{ old('departure_date_str', $package->departure_date_str ?? '28-29 Apr / 21-22 Zil Qadh') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Departure Sector</label>
                                    <input type="text" name="departure_sector" class="form-control" placeholder="e.g. Karachi to Jeddah" value="{{ old('departure_sector', $package->departure_sector ?? 'Karachi to Jeddah') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Arrival Date & Hijri</label>
                                    <input type="text" name="arrival_date_str" class="form-control" placeholder="e.g. 28 May / 22 Zil Hajj" value="{{ old('arrival_date_str', $package->arrival_date_str ?? '28 May / 22 Zil Hajj') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Arrival Sector</label>
                                    <input type="text" name="arrival_sector" class="form-control" placeholder="e.g. Jeddah/Madinah to Karachi" value="{{ old('arrival_sector', $package->arrival_sector ?? 'Jeddah/Madinah to Karachi') }}">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-primary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-camp\']')).show()">
                                    Next: Camp & Pricing <i class="mdi mdi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        {{-- ═════════════════════════════════════════
                             TAB 2: CAMP & MAKTAB PRICING
                        ═════════════════════════════════════════ --}}
                        <div class="tab-pane fade card-body" id="tab-camp">
                            <h5 class="section-subhead">⛺ Maktab / Camp Category & Zone</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Maktab Category</label>
                                    <input type="text" name="camp_category" class="form-control" placeholder="e.g. C, A, A & C" value="{{ old('camp_category', $package->camp_category ?? ($package->maktab ?? 'C')) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Camp Zone</label>
                                    <input type="text" name="camp_zone" class="form-control" placeholder="e.g. ZONE 5 or ZONE 1 OR 2" value="{{ old('camp_zone', $package->camp_zone ?? ($package->zone ?? 'ZONE 5')) }}">
                                </div>
                            </div>

                            <div class="row g-4 mb-4">
                                {{-- Maktab C Rates --}}
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light">
                                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">📍 MAKTAB C (ZONE 5) RATES</h6>
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Quad / Sharing (PKR)</label>
                                                <input type="number" name="maktab_c_quad_pkr" class="form-control" placeholder="0" value="{{ old('maktab_c_quad_pkr', $package->maktab_c_quad_pkr ?? ($package->adult_pkr ?? '')) }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Quad / Sharing (USD $)</label>
                                                <input type="number" name="maktab_c_quad_usd" class="form-control" placeholder="0" value="{{ old('maktab_c_quad_usd', $package->maktab_c_quad_usd ?? ($package->adult_usd ?? '')) }}">
                                            </div>
                                        </div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Triple (PKR)</label>
                                                <input type="number" name="maktab_c_triple_pkr" class="form-control" placeholder="0" value="{{ old('maktab_c_triple_pkr', $package->maktab_c_triple_pkr ?? '') }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Triple (USD $)</label>
                                                <input type="number" name="maktab_c_triple_usd" class="form-control" placeholder="0" value="{{ old('maktab_c_triple_usd', $package->maktab_c_triple_usd ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Double (PKR)</label>
                                                <input type="number" name="maktab_c_double_pkr" class="form-control" placeholder="0" value="{{ old('maktab_c_double_pkr', $package->maktab_c_double_pkr ?? '') }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Double (USD $)</label>
                                                <input type="number" name="maktab_c_double_usd" class="form-control" placeholder="0" value="{{ old('maktab_c_double_usd', $package->maktab_c_double_usd ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Maktab A Rates (Optional Comparison) --}}
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light">
                                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">📍 MAKTAB A (ZONE 1 OR 2) RATES (Optional)</h6>
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Quad / Sharing (PKR)</label>
                                                <input type="number" name="maktab_a_quad_pkr" class="form-control" placeholder="0" value="{{ old('maktab_a_quad_pkr', $package->maktab_a_quad_pkr ?? '') }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Quad / Sharing (USD $)</label>
                                                <input type="number" name="maktab_a_quad_usd" class="form-control" placeholder="0" value="{{ old('maktab_a_quad_usd', $package->maktab_a_quad_usd ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Triple (PKR)</label>
                                                <input type="number" name="maktab_a_triple_pkr" class="form-control" placeholder="0" value="{{ old('maktab_a_triple_pkr', $package->maktab_a_triple_pkr ?? '') }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Triple (USD $)</label>
                                                <input type="number" name="maktab_a_triple_usd" class="form-control" placeholder="0" value="{{ old('maktab_a_triple_usd', $package->maktab_a_triple_usd ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Double (PKR)</label>
                                                <input type="number" name="maktab_a_double_pkr" class="form-control" placeholder="0" value="{{ old('maktab_a_double_pkr', $package->maktab_a_double_pkr ?? '') }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Double (USD $)</label>
                                                <input type="number" name="maktab_a_double_usd" class="form-control" placeholder="0" value="{{ old('maktab_a_double_usd', $package->maktab_a_double_usd ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Azizia Separate Room Addon --}}
                            <h5 class="section-subhead">🏢 Azizia Separate Room Add-On Rates</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Quad / Sharing (PKR / USD)</label>
                                    <div class="input-group">
                                        <input type="number" name="azizia_quad_pkr" class="form-control" placeholder="PKR" value="{{ old('azizia_quad_pkr', $package->azizia_quad_pkr ?? '') }}">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="azizia_quad_usd" class="form-control" placeholder="USD" value="{{ old('azizia_quad_usd', $package->azizia_quad_usd ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Triple (PKR / USD)</label>
                                    <div class="input-group">
                                        <input type="number" name="azizia_triple_pkr" class="form-control" placeholder="PKR" value="{{ old('azizia_triple_pkr', $package->azizia_triple_pkr ?? '') }}">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="azizia_triple_usd" class="form-control" placeholder="USD" value="{{ old('azizia_triple_usd', $package->azizia_triple_usd ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Double (PKR / USD)</label>
                                    <div class="input-group">
                                        <input type="number" name="azizia_double_pkr" class="form-control" placeholder="PKR" value="{{ old('azizia_double_pkr', $package->azizia_double_pkr ?? '') }}">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="azizia_double_usd" class="form-control" placeholder="USD" value="{{ old('azizia_double_usd', $package->azizia_double_usd ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-package\']')).show()">
                                    <i class="mdi mdi-arrow-left me-1"></i> Prev
                                </button>
                                <button type="button" class="btn btn-primary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-qurbani\']')).show()">
                                    Next: Qurbani <i class="mdi mdi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        {{-- ═════════════════════════════════════════
                             TAB 3: QURBANI
                        ═════════════════════════════════════════ --}}
                        <div class="tab-pane fade card-body" id="tab-qurbani">
                            <h5 class="section-subhead">🐑 Qurbani Policy & Charges</h5>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Qurbani Status in Package</label>
                                    <select name="qurbani_status" class="form-select">
                                        <option value="Not Included (Nusuk Masar System)" {{ old('qurbani_status', $package->qurbani_status) == 'Not Included (Nusuk Masar System)' ? 'selected' : '' }}>
                                            Not Included (Nusuk Masar System)
                                        </option>
                                        <option value="Included in Package" {{ old('qurbani_status', $package->qurbani_status) == 'Included in Package' ? 'selected' : '' }}>
                                            Included in Package
                                        </option>
                                        <option value="Separate Optional Add-on" {{ old('qurbani_status', $package->qurbani_status) == 'Separate Optional Add-on' ? 'selected' : '' }}>
                                            Separate Optional Add-on
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Estimated Qurbani Charges (Per Person SAR / PKR)</label>
                                    <input type="number" name="qurbani_charges" class="form-control" placeholder="e.g. 750" value="{{ old('qurbani_charges', $package->qurbani_charges ?? 0) }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Qurbani Notice Text (Appears in Brochure)</label>
                                    <textarea name="qurbani_note" class="form-control" rows="3">{{ old('qurbani_note', $package->qurbani_note ?? 'Qurbani is not included in the package. Qurbani charges will be added separately as per the applicable charges in the Nusuk Masar system.') }}</textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-camp\']')).show()">
                                    <i class="mdi mdi-arrow-left me-1"></i> Prev
                                </button>
                                <button type="button" class="btn btn-primary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-accommodation\']')).show()">
                                    Next: Accommodation <i class="mdi mdi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        {{-- ═════════════════════════════════════════
                             TAB 4: ACCOMMODATION & ITINERARY
                        ═════════════════════════════════════════ --}}
                        <div class="tab-pane fade card-body" id="tab-accommodation">
                            <h5 class="section-subhead">🏨 Stay Schedule (Makkah, Azizia, Hajj Days, Madinah)</h5>

                            <div id="accommodations-container">
                                @php
                                    $accs = $package->accommodations;
                                    if(!$accs || $accs->count() == 0) {
                                        $accs = collect([
                                            (object)['place' => 'MAKKAH - ANJUM MAKKAH HOTEL', 'hotel' => 'Anjum Makkah Hotel', 'check_in' => '2027-04-28', 'check_out' => '2027-05-06', 'note' => '21 - 22 Zil Qadh 1448', 'sharing' => '29 Zil Qadh 01 Zil Hajj 1448', 'food_package' => 'FULL BOARD', 'sharing_type' => 'Breakfast, Lunch, Dinner Asian Meal'],
                                            (object)['place' => 'AZIZIA - BUILDING', 'hotel' => 'Azizia Building', 'check_in' => '2027-05-06', 'check_out' => '2027-05-13', 'note' => '29 Zil Qadh - 01 Zil Hajj 1448', 'sharing' => '07 Zil Hajj 1448', 'food_package' => 'FULL BOARD', 'sharing_type' => 'Breakfast, Lunch, Dinner Asian Meal'],
                                            (object)['place' => 'HAJJ - DAYS (MINA / ARAFAT)', 'hotel' => 'Maktab Camp', 'check_in' => '2027-05-14', 'check_out' => '2027-05-18', 'note' => '08 Zil Hajj 1448', 'sharing' => '12 Zil Hajj 1448', 'food_package' => 'FULL BOARD', 'sharing_type' => 'Provided by Maktab'],
                                            (object)['place' => 'AZIZIA - BUILDING', 'hotel' => 'Azizia Building', 'check_in' => '2027-05-18', 'check_out' => '2027-05-23', 'note' => '12 Zil Hajj 1448', 'sharing' => '17 Zil Hajj 1448', 'food_package' => 'FULL BOARD', 'sharing_type' => 'Breakfast, Lunch, Dinner Asian Meal'],
                                            (object)['place' => 'MADINAH - NUSK AL HIJRA', 'hotel' => 'Nusk Al Hijra', 'check_in' => '2027-05-23', 'check_out' => '2027-05-28', 'note' => '17 Zil Hajj 1448', 'sharing' => '22 Zil Hajj 1448', 'food_package' => 'FULL BOARD', 'sharing_type' => 'Breakfast, Lunch, Dinner Asian Meal'],
                                        ]);
                                    }
                                @endphp

                                @foreach ($accs as $idx => $acc)
                                    <div class="accommodation-row border rounded p-3 mb-3 bg-light">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Segment / Place</label>
                                                <input type="text" name="accommodations[{{ $idx }}][place]" class="form-control" value="{{ old('accommodations.'.$idx.'.place', $acc->place) }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Hotel / Building</label>
                                                <input type="text" name="accommodations[{{ $idx }}][package_a][hotel]" class="form-control" value="{{ old('accommodations.'.$idx.'.package_a.hotel', $acc->package_a_hotel ?? ($acc->hotel ?? '')) }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Arrival Date & Hijri</label>
                                                <input type="date" name="accommodations[{{ $idx }}][check_in]" class="form-control mb-1" value="{{ old('accommodations.'.$idx.'.check_in', $acc->check_in ? \Carbon\Carbon::parse($acc->check_in)->format('Y-m-d') : '') }}">
                                                <input type="text" name="accommodations[{{ $idx }}][note]" class="form-control form-control-sm" placeholder="Hijri e.g. 21-22 Zil Qadh 1448" value="{{ old('accommodations.'.$idx.'.note', $acc->note ?? '') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Departure Date & Hijri</label>
                                                <input type="date" name="accommodations[{{ $idx }}][check_out]" class="form-control mb-1" value="{{ old('accommodations.'.$idx.'.check_out', $acc->check_out ? \Carbon\Carbon::parse($acc->check_out)->format('Y-m-d') : '') }}">
                                                <input type="text" name="accommodations[{{ $idx }}][sharing]" class="form-control form-control-sm" placeholder="Hijri e.g. 29 Zil Qadh 1448" value="{{ old('accommodations.'.$idx.'.sharing', $acc->sharing ?? '') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Meal Plan</label>
                                                <input type="text" name="accommodations[{{ $idx }}][food_package]" class="form-control" placeholder="e.g. FULL BOARD" value="{{ old('accommodations.'.$idx.'.food_package', $acc->food_package ?? ($acc->package_a_food_package ?? 'FULL BOARD')) }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Meal Detail Note</label>
                                                <input type="text" name="accommodations[{{ $idx }}][sharing_type]" class="form-control" placeholder="Breakfast, Lunch, Dinner Asian Meal" value="{{ old('accommodations.'.$idx.'.sharing_type', $acc->sharing_type ?? 'Breakfast, Lunch, Dinner Asian Meal') }}">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-qurbani\']')).show()">
                                    <i class="mdi mdi-arrow-left me-1"></i> Prev
                                </button>
                                <button type="button" class="btn btn-primary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-terms\']')).show()">
                                    Next: Inclusions & Terms <i class="mdi mdi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        {{-- ═════════════════════════════════════════
                             TAB 5: INCLUSIONS & INSTRUCTIONS
                        ═════════════════════════════════════════ --}}
                        <div class="tab-pane fade card-body" id="tab-terms">
                            <h5 class="section-subhead">📜 Package Inclusions, Instructions & Notes</h5>

                            @php
                                $inclContent = $package->terms->content ?? (is_array($package->package_included_points) ? implode("\n", $package->package_included_points) : ($package->package_included_points ?? ''));
                                $instContent = is_array($package->instructions_points) ? implode("\n", $package->instructions_points) : ($package->instructions_points ?? '');
                                $docContent = $package->documents_required ?? '';
                                $noteContent = $package->notes ?? ($package->important_note ?? '');
                            @endphp

                            <div class="row g-4 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Package Included (One per line)</label>
                                    <textarea name="terms_content" class="form-control" rows="8">{{ old('terms_content', $inclContent) }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Instructions & Guidelines (One per line)</label>
                                    <textarea name="instructions_content" class="form-control" rows="8">{{ old('instructions_content', $instContent) }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Documents Required (One per line)</label>
                                    <textarea name="documents_required" class="form-control" rows="8">{{ old('documents_required', $docContent) }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Important Notes & Disclaimer</label>
                                <textarea name="notes" class="form-control" rows="4">{{ old('notes', $noteContent) }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-accommodation\']')).show()">
                                    <i class="mdi mdi-arrow-left me-1"></i> Prev
                                </button>
                                <button type="button" class="btn btn-primary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-transport\']')).show()">
                                    Next: Transport <i class="mdi mdi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        {{-- ═════════════════════════════════════════
                             TAB 6: TRANSPORT & FLIGHTS
                        ═════════════════════════════════════════ --}}
                        <div class="tab-pane fade card-body" id="tab-transport">
                            <h5 class="section-subhead">🚌 Transport & Flight Routes</h5>
                            @php
                                $firstTr = $package->transports->first();
                            @endphp
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Transport Sector</label>
                                    <input type="text" name="transports[0][route]" class="form-control" placeholder="Karachi -> Jeddah -> Makkah -> Madinah" value="{{ old('transports.0.route', $firstTr->route ?? 'Karachi -> Jeddah -> Makkah -> Madinah -> Karachi') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Vehicle Type</label>
                                    <input type="text" name="transports[0][vehicle]" class="form-control" placeholder="Air Conditioned Private Buses" value="{{ old('transports.0.vehicle', $firstTr->vehicle ?? 'Air Conditioned Private Buses') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Transport Service</label>
                                    <input type="text" name="transports[0][type]" class="form-control" placeholder="Full Complete Hajj Transport" value="{{ old('transports.0.type', $firstTr->type ?? 'Full Complete Hajj Transport') }}">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-terms\']')).show()">
                                    <i class="mdi mdi-arrow-left me-1"></i> Prev
                                </button>
                                <button type="button" class="btn btn-primary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-training\']')).show()">
                                    Next: Training & Gifts <i class="mdi mdi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        {{-- ═════════════════════════════════════════
                             TAB 7: TRAINING & GIFTS
                        ═════════════════════════════════════════ --}}
                        <div class="tab-pane fade card-body" id="tab-training">
                            <h5 class="section-subhead">🎁 Hajj Training Sessions & Gifts</h5>

                            @php
                                $selectedSessions = $package->trainingSessions->pluck('id')->toArray();
                                $selectedGiveaways = $package->giveaways->pluck('id')->toArray();
                            @endphp

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Select Training Sessions</label>
                                    <select name="training_sessions[]" class="form-select" multiple size="4">
                                        @foreach ($trainingSessions as $session)
                                            <option value="{{ $session->id }}" {{ in_array($session->id, $selectedSessions) ? 'selected' : '' }}>
                                                {{ $session->title ?? $session->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Hold Ctrl to select multiple sessions</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Select Giveaways & Gifts</label>
                                    <select name="giveaways[]" class="form-select" multiple size="4">
                                        @foreach ($giveaways as $giveaway)
                                            <option value="{{ $giveaway->id }}" {{ in_array($giveaway->id, $selectedGiveaways) ? 'selected' : '' }}>
                                                {{ $giveaway->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Hold Ctrl to select multiple gifts</small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-transport\']')).show()">
                                    <i class="mdi mdi-arrow-left me-1"></i> Prev
                                </button>
                                <button type="submit" class="btn btn-success px-5 fw-bold">
                                    <i class="mdi mdi-check-circle me-1"></i> UPDATE PACKAGE
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
