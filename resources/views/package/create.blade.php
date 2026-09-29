@extends('layout.master')

@section('title', 'Create Package')

@section('content')
    <style>
        .accommodation-row {
            position: relative;
        }
        .accommodation-row:not(:first-child) {
            margin-top: 35px !important;
        }
        .accommodation-row-divider {
            border: none;
            border-top: 4px solid #000 !important;
            opacity: 1 !important;
            position: absolute;
            top: -20px;
            left: 0;
            right: 0;
            margin: 0 !important;
        }
        .accommodation-row:first-child .accommodation-row-divider {
            display: none;
        }
    </style>
    <!-- Summernote Lite CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-center justify-content-between">
                    <h4 class="fs-18 fw-semibold m-0">Package / <strong>Create</strong></h4>
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

                <form action="{{ route('package.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <ul class="nav nav-pills mb-3">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill"
                                data-bs-target="#tab-package" type="button">PACKAGE</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill"
                                data-bs-target="#tab-accommodation" type="button">ACCOMMODATION</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-transport"
                                type="button">TRANSPORT</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-training"
                                type="button">TRAINING / GIFTS</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-terms"
                                type="button">TERMS & CONDITION</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-itinerary"
                                type="button">ITINERARY</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-maktab"
                                type="button">MAKTAB ADDRESS</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-notes"
                                type="button">Notes</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-feature-icons"
                                type="button">FEATURE ICONS BOX</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-services"
                                type="button">SERVICES (PAGE 2)</button></li>
                    </ul>

                    <div class="tab-content card">

                        {{-- ================= PACKAGE ================= --}}
                        <div class="tab-pane fade show active card" id="tab-package">
                            <div class="card-body">
                                <h5 class="mb-3">Package Details</h5>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Company</label>
                                        <select name="company_id" class="form-select">
                                            <option value="">-- Select Company --</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}"
                                                    {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                                    {{ $company->company_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Package #</label>
                                        <input type="text" name="package_number" class="form-control"
                                            placeholder="e.g. PKG-001" value="{{ old('package_number') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Category</label>
                                        <input type="text" name="category" class="form-control" placeholder="e.g. VIP"
                                            value="{{ old('category') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Zone</label>
                                        <input type="text" name="zone" class="form-control" placeholder="e.g. Zone A"
                                            value="{{ old('zone') }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Package Name</label>
                                        <input type="text" name="package_title" class="form-control"
                                            placeholder="e.g. Executive Platinum" value="{{ old('package_title') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Package Name</label>
                                        <input type="text" name="name" class="form-control"
                                            placeholder="e.g. INTERCON / FAIRMONT" value="{{ old('name') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Package Code</label>
                                        <input type="text" name="code" class="form-control"
                                            placeholder="e.g. HJ-2027-01" value="{{ old('code') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Days</label>
                                        <input type="number" min="0" name="days" class="form-control"
                                            placeholder="e.g. 21" value="{{ old('days') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Year</label>
                                        <input type="text" name="year" class="form-control"
                                            placeholder="e.g. 2027" value="{{ old('year', date('Y') + 1) }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Maktab</label>
                                        <input type="text" name="maktab" class="form-control"
                                            placeholder="e.g. Maktab 5" value="{{ old('maktab') }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Maktab Number</label>
                                        <input type="text" name="maktab_number" class="form-control"
                                            placeholder="e.g. 105" value="{{ old('maktab_number') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label d-block">Medina Arrival</label>
                                        @foreach (['before_hajj' => 'Before Hajj', 'after_hajj' => 'After Hajj'] as $val => $label)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="medina_arrival"
                                                    value="{{ $val }}"
                                                    {{ old('medina_arrival', 'before_hajj') == $val ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ $label }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label d-block">Hajj Duration</label>
                                        @foreach (['short' => 'Short', 'long' => 'Long'] as $val => $label)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="hajj_duration"
                                                    value="{{ $val }}"
                                                    {{ old('hajj_duration', 'short') == $val ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ $label }}</label>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Hijri Start Day</label>
                                        <input type="number" min="1" max="30" name="hijri_start_day"
                                            class="form-control" placeholder="e.g. 1"
                                            value="{{ old('hijri_start_day') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Hijri Start Month</label>
                                        <select name="hijri_start_month" class="form-select">
                                            <option value="">-- Select --</option>
                                            @foreach ([
            1 => 'Muharram',
            2 => 'Safar',
            3 => 'Rabi-ul-Awwal',
            4 => 'Rabi-ul-Thani',
            5 => 'Jumada-al-Awwal',
            6 => 'Jumada-al-Thani',
            7 => 'Rajab',
            8 => 'Shaban',
            9 => 'Ramadan',
            10 => 'Shawwal',
            11 => 'Zil Qadah',
            12 => 'Zil Hajj',
        ] as $val => $label)
                                                <option value="{{ $val }}"
                                                    {{ old('hijri_start_month', 12) == $val ? 'selected' : '' }}>
                                                    {{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <hr class="my-4">
                                <h5 class="mb-3">Package Category</h5>
                                <div class="row g-3 mt-1">
                                    {{-- <div class="col-md-3">
                                        <label class="form-label">Room Type</label>
                                        <input type="text" name="room_type" class="form-control"
                                            placeholder="e.g. Standard" value="{{ old('room_type') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Azizia Room Type</label>
                                        <input type="text" name="azizia_room_type" class="form-control"
                                            placeholder="e.g. Standard" value="{{ old('azizia_room_type') }}">
                                    </div> --}}
                                    <div class="col-md-3">
                                        <label class="form-label">Makkah Type</label>
                                        <select name="makkah_type" class="form-select">
                                            <option value="">-- Select --</option>
                                            @foreach ([1, 2, 3, 4, 5] as $val)
                                                <option value="{{ $val }}"
                                                    {{ old('makkah_type') == $val ? 'selected' : '' }}>{{ $val }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Medinah Type</label>
                                        <select name="medinah_type" class="form-select">
                                            <option value="">-- Select --</option>
                                            @foreach ([1, 2, 3, 4, 5] as $val)
                                                <option value="{{ $val }}"
                                                    {{ old('medinah_type') == $val ? 'selected' : '' }}>
                                                    {{ $val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Azizia Type</label>
                                        <select name="azizia_type" class="form-select">
                                            <option value="">-- Select --</option>
                                            @foreach ([1, 2, 3] as $val)
                                                <option value="{{ $val }}"
                                                    {{ old('azizia_type') == $val ? 'selected' : '' }}>{{ $val }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Mina Type</label>
                                        <input type="text" name="mina_type" class="form-control"
                                            placeholder="e.g. Camp A" value="{{ old('mina_type') }}">
                                    </div>
                                </div>

                                <hr class="my-4">
                                <h5 class="mb-3">Makkah / Madinah Sharing Breakdown</h5>
                                <div class="row g-3">
                                    @foreach ([
            'makkah_a' => 'Makkah A',
            'makkah_b' => 'Makkah B',
            'madinah_a' => 'Madinah A',
            'madinah_b' => 'Madinah B',
        ] as $prefix => $heading)
                                        <div class="col-12">
                                            <h6 class="mt-2 mb-1">{{ $heading }}</h6>
                                        </div>
                                        @foreach (['double' => 'Double', 'triple' => 'Triple', 'quad' => 'Quad', 'sharing' => 'Sharing'] as $key => $label)
                                            <div class="col-md-3">
                                                <label class="form-label">{{ $label }}</label>
                                                <input type="number" min="0"
                                                    name="{{ $prefix }}[{{ $key }}]" class="form-control"
                                                    placeholder="0" value="{{ old("$prefix.$key") }}">
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- ================= ACCOMMODATION ================= --}}
                        <div class="tab-pane fade card" id="tab-accommodation">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="m-0">Accommodation Details</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        id="addAccommodationBtn">Add <i class="mdi mdi-plus"></i></button>
                                </div>
                                <p class="text-muted mb-3">Check In / Check Out dono packages (A &amp; B) ke liye same
                                    rahenge. Sirf hotel A aur B alag select karein — agar dono packages ka hotel same
                                    ho to "Same Hotel for Package A &amp; B" tick kar dein.</p>

                                <div id="accommodationRows">
                                    <div class="accommodation-row border rounded p-3 mb-3 position-relative">
                                         <hr class="accommodation-row-divider" style="border-top: 3px solid #000; opacity: 1; margin-top: 0; margin-bottom: 15px;">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger remove-accommodation-row position-absolute top-0 end-0 m-2"><i
                                                class="mdi mdi-delete"></i></button>
                                        <div class="row g-3">
                                            <div class="col-md-3"><label class="form-label">Place</label>
                                                <select name="accommodations[0][place]" class="form-select">
                                                    <option value="">-- Select Place --</option>
                                                    @foreach (\App\Enums\Place::options() as $val => $label)
                                                        <option value="{{ $val }}">{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3"><label class="form-label">Check In</label><input
                                                    type="date" name="accommodations[0][check_in]"
                                                    class="form-control"></div>
                                            <div class="col-md-3"><label class="form-label">Check Out</label><input
                                                    type="date" name="accommodations[0][check_out]"
                                                    class="form-control"></div>
                                            <div class="col-md-3"><label class="form-label">Days</label><input
                                                    type="number" name="accommodations[0][days]" class="form-control">
                                            </div>
                                            <div class="col-md-3"><label class="form-label">Nights</label><input
                                                    type="number" name="accommodations[0][nights]" class="form-control">
                                            </div>
                                            <div class="col-md-3 d-flex align-items-end">
                                                <div class="form-check">
                                                    <input class="form-check-input same-for-both-toggle" type="checkbox"
                                                        name="accommodations[0][same_for_both]" value="1">
                                                    <label class="form-check-label">Same Hotel for Package A &
                                                        B</label>
                                                </div>
                                            </div>

                                             <div class="col-12">
                                                <hr class="my-2">
                                                <h6 class="text-primary mb-0">Package A</h6>
                                            </div>
                                            <div class="col-md-3"><label class="form-label">Accommodation
                                                    Type (A)</label>
                                                <select name="accommodations[0][package_a][accommodation_type]"
                                                    class="form-select">
                                                    <option value="">-- Select Type --</option>
                                                    @foreach (\App\Enums\AccommodationType::options() as $val => $label)
                                                        <option value="{{ $val }}">{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3"><label class="form-label">Saudi Star
                                                    Rating (A)</label>
                                                <select name="accommodations[0][package_a][saudi_star_rating]"
                                                    class="form-select">
                                                    <option value="">-- Select Rating --</option>
                                                    @foreach (\App\Enums\SaudiStarRating::options() as $val => $label)
                                                        <option value="{{ $val }}">{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3"><label class="form-label">Hotel (A)</label>
                                                <select name="accommodations[0][package_a][hotel]" class="form-select">
                                                    <option value="">-- Select Hotel --</option>
                                                    @foreach ($hotels as $hotelItem)
                                                        <option value="{{ $hotelItem->name }}">{{ $hotelItem->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3"><label class="form-label">Food Package (A)</label>
                                                <select name="accommodations[0][package_a][food_package]"
                                                    class="form-select">
                                                    <option value="">-- Select Food --</option>
                                                    @foreach (\App\Enums\FoodPackage::options() as $val => $label)
                                                        <option value="{{ $val }}">{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="package-b-block-wrap row g-3">
                                                <div class="col-12">
                                                    <hr class="my-2">
                                                    <h6 class="text-danger mb-0">Package B</h6>
                                                </div>
                                                <div class="col-md-3"><label class="form-label">Accommodation
                                                        Type (B)</label>
                                                    <select name="accommodations[0][package_b][accommodation_type]"
                                                        class="form-select">
                                                        <option value="">-- Select Type --</option>
                                                        @foreach (\App\Enums\AccommodationType::options() as $val => $label)
                                                            <option value="{{ $val }}">{{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3"><label class="form-label">Saudi Star
                                                        Rating (B)</label>
                                                    <select name="accommodations[0][package_b][saudi_star_rating]"
                                                        class="form-select">
                                                        <option value="">-- Select Rating --</option>
                                                        @foreach (\App\Enums\SaudiStarRating::options() as $val => $label)
                                                            <option value="{{ $val }}">{{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3"><label class="form-label">Hotel (B)</label>
                                                    <select name="accommodations[0][package_b][hotel]"
                                                        class="form-select">
                                                        <option value="">-- Select Hotel --</option>
                                                        @foreach ($hotels as $hotelItem)
                                                            <option value="{{ $hotelItem->name }}">{{ $hotelItem->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3"><label class="form-label">Food Package (B)</label>
                                                    <select name="accommodations[0][package_b][food_package]"
                                                        class="form-select">
                                                        <option value="">-- Select Food --</option>
                                                        @foreach (\App\Enums\FoodPackage::options() as $val => $label)
                                                            <option value="{{ $val }}">{{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <hr class="my-2">
                                            </div>

                                            <div class="col-md-3"><label class="form-label">Distance (meter)</label><input
                                                    type="number" name="accommodations[0][distance]"
                                                    class="form-control"></div>
                                            {{-- <div class="col-md-3"><label class="form-label">Azizia Date</label><input
                                                    type="date" name="accommodations[0][azizia_date]"
                                                    class="form-control"></div>
                                            <div class="col-md-3"><label class="form-label">Food Package</label>
                                                <select name="accommodations[0][food_package]" class="form-select">
                                                    <option value="">-- Select Package --</option>
                                                    @foreach (\App\Enums\FoodPackage::options() as $val => $label)
                                                        <option value="{{ $val }}">{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div> --}}

                                            <div class="col-md-4"><label class="form-label">Actual Check In
                                                    Time</label><input type="datetime-local"
                                                    name="accommodations[0][actual_check_in_time]" class="form-control">
                                            </div>
                                            <div class="col-md-4"><label class="form-label">Actual Check Out
                                                    Time</label><input type="datetime-local"
                                                    name="accommodations[0][actual_check_out_time]" class="form-control">
                                            </div>

                                            <div class="col-md-3"><label class="form-label">Makkah Ziarat</label>
                                                <select name="accommodations[0][makkah_ziarat]" class="form-select">
                                                    <option value="">-- Select --</option>
                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3"><label class="form-label">Madinah Ziarat</label>
                                                <select name="accommodations[0][madinah_ziarat]" class="form-select">
                                                    <option value="">-- Select --</option>
                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4"><label class="form-label">Distribution</label><input
                                                    type="text" name="accommodations[0][distribution]"
                                                    class="form-control"></div>
                                            <div class="col-md-4"><label class="form-label">Camp</label><input
                                                    type="text" name="accommodations[0][camp]" class="form-control">
                                            </div>
                                            <div class="col-md-4"><label class="form-label">Arafat</label><input
                                                    type="text" name="accommodations[0][arafat]" class="form-control">
                                            </div>

                                            <div class="col-md-4"><label class="form-label"> Azizia Shuttle</label><input
                                                    type="text" name="accommodations[0][shuttle]"
                                                    class="form-control"></div>
                                            <div class="col-md-4"><label class="form-label">Bedding (Sofa
                                                    Mattress)</label><input type="text"
                                                    name="accommodations[0][bedding]" class="form-control"></div>
                                            <div class="col-md-4"><label class="form-label">Sharing (Room / Tent /
                                                    Camp)</label><input type="text" name="accommodations[0][sharing]"
                                                    class="form-control"></div>

                                            <div class="col-md-4"><label class="form-label">Sharing Type</label><input
                                                    type="text" name="accommodations[0][sharing_type]"
                                                    class="form-control"></div>
                                            <div class="col-md-8"><label class="form-label">Note</label><input
                                                    type="text" name="accommodations[0][note]" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ================= TRANSPORT ================= --}}
                        <div class="tab-pane fade card" id="tab-transport">
                            <div class="card-body">

                                {{-- ---- General Route (repeatable) ---- --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="m-0">Transport Details</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        id="addTransportBtn">Add <i class="mdi mdi-plus"></i></button>
                                </div>

                                <div id="transportRows">
                                    <div class="transport-row border rounded p-3 mb-3 position-relative">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger remove-transport-row position-absolute top-0 end-0 m-2"><i
                                                class="mdi mdi-delete"></i></button>
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label">Route</label>
                                                <input type="text" name="transports[0][route]" class="form-control">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Arrival</label>
                                                <input type="text" name="transports[0][arrival]" class="form-control">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Departure</label>
                                                <input type="text" name="transports[0][departure]"
                                                    class="form-control">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Type</label>
                                                <input type="text" name="transports[0][type]" class="form-control">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Vehicle</label>
                                                <input type="text" name="transports[0][vehicle]" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                {{-- ---- Flight ---- --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="m-0">Flight</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="addFlightBtn">Add
                                        <i class="mdi mdi-plus"></i></button>
                                </div>

                                <div id="flightRows">
                                    <div class="flight-row border rounded p-3 mb-3 position-relative">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger remove-flight-row position-absolute top-0 end-0 m-2"><i
                                                class="mdi mdi-delete"></i></button>
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label">Airline</label>
                                                <select name="flights[0][airline]" class="form-select">
                                                    <option value="">-- Select Airline --</option>
                                                    @foreach ($airlines as $airlineItem)
                                                        <option value="{{ $airlineItem->name }}">{{ $airlineItem->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3"><label class="form-label">Flight No.</label><input
                                                    type="text" name="flights[0][flight_no]" class="form-control">
                                            </div>
                                            <div class="col-md-3"><label class="form-label">Flight Class</label><input
                                                    type="text" name="flights[0][flight_class]" class="form-control">
                                            </div>
                                            <div class="col-md-3"><label class="form-label">Origin</label><input
                                                    type="text" name="flights[0][origin]" class="form-control"></div>

                                            <div class="col-md-3"><label class="form-label">Destination</label><input
                                                    type="text" name="flights[0][destination]" class="form-control">
                                            </div>
                                            <div class="col-md-3"><label class="form-label">Departure Date</label><input
                                                    type="date" name="flights[0][departure_date]"
                                                    class="form-control"></div>
                                            <div class="col-md-3"><label class="form-label">Departure Time</label><input
                                                    type="time" name="flights[0][departure_time]"
                                                    class="form-control"></div>
                                            <div class="col-md-3"><label class="form-label">Arrival Date</label><input
                                                    type="date" name="flights[0][arrival_date]" class="form-control">
                                            </div>

                                            <div class="col-md-3"><label class="form-label">Arrival Time</label><input
                                                    type="time" name="flights[0][arrival_time]" class="form-control">
                                            </div>
                                            <div class="col-md-3"><label class="form-label">PNR No.</label><input
                                                    type="text" name="flights[0][pnr_no]" class="form-control"></div>
                                            <div class="col-md-3"><label class="form-label">Ticket Amount
                                                    (SAR)</label><input type="number" step="0.01"
                                                    name="flights[0][ticket_amount]" class="form-control"></div>
                                            <div class="col-md-3">
                                                <label class="form-label d-block">Is Preferred</label>
                                                @foreach (['1' => 'Yes', '0' => 'No'] as $val => $label)
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="flights[0][is_preferred]" value="{{ $val }}"
                                                            {{ $val == '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label">{{ $label }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                {{-- ---- Train ---- --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="m-0">Train</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="addTrainBtn">Add <i
                                            class="mdi mdi-plus"></i></button>
                                </div>

                                <div id="trainRows">
                                    <div class="train-row border rounded p-3 mb-3 position-relative">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger remove-train-row position-absolute top-0 end-0 m-2"><i
                                                class="mdi mdi-delete"></i></button>
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label">Railway</label>
                                                <select name="trains[0][railway]" class="form-select">
                                                    <option value="">-- Select Train / Railway --</option>
                                                    @foreach ($trains as $trainItem)
                                                        <option value="{{ $trainItem->train_name }}">
                                                            {{ $trainItem->train_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3"><label class="form-label">Train No.</label><input
                                                    type="text" name="trains[0][train_no]" class="form-control"></div>
                                            <div class="col-md-3"><label class="form-label">Train Class</label><input
                                                    type="text" name="trains[0][train_class]" class="form-control">
                                            </div>
                                            <div class="col-md-3"><label class="form-label">Origin</label><input
                                                    type="text" name="trains[0][origin]" class="form-control"></div>

                                            <div class="col-md-3"><label class="form-label">Destination</label><input
                                                    type="text" name="trains[0][destination]" class="form-control">
                                            </div>
                                            <div class="col-md-3"><label class="form-label">Departure Date</label><input
                                                    type="date" name="trains[0][departure_date]" class="form-control">
                                            </div>
                                            <div class="col-md-3"><label class="form-label">Departure Time</label><input
                                                    type="time" name="trains[0][departure_time]" class="form-control">
                                            </div>
                                            <div class="col-md-3"><label class="form-label">Arrival Date</label><input
                                                    type="date" name="trains[0][arrival_date]" class="form-control">
                                            </div>

                                            <div class="col-md-3"><label class="form-label">Arrival Time</label><input
                                                    type="time" name="trains[0][arrival_time]" class="form-control">
                                            </div>
                                            <div class="col-md-3"><label class="form-label">PNR No.</label><input
                                                    type="text" name="trains[0][pnr_no]" class="form-control"></div>
                                            <div class="col-md-3"><label class="form-label">Ticket Amount
                                                    (SAR)</label><input type="number" step="0.01"
                                                    name="trains[0][ticket_amount]" class="form-control"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- ================= TRAINING / GIFTS ================= --}}
                        <div class="tab-pane fade card" id="tab-training">
                            <div class="card-body">
                                <h5 class="mb-3">Training Session</h5>
                                @if ($trainingSessions->isEmpty())
                                    <p class="text-danger mb-3">No Training Sessions Exists! <a
                                            href="{{ route('training-session.create') }}" target="_blank">Please create
                                            training sessions first</a></p>
                                @else
                                    @php $selectedSessions = old('training_sessions', []); @endphp
                                    @foreach ($trainingSessions as $session)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="training_sessions[]"
                                                value="{{ $session->id }}" id="session-{{ $session->id }}"
                                                {{ in_array($session->id, $selectedSessions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="session-{{ $session->id }}">
                                                {{ $session->name }}
                                                @if ($session->session_date)
                                                    ({{ \Carbon\Carbon::parse($session->session_date)->format('d M, Y') }}
                                                    at
                                                    {{ $session->session_time ? \Carbon\Carbon::parse($session->session_time)->format('h:i A') : '' }})
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                @endif

                                <h5 class="mb-3">Giveaways</h5>
                                @php $selectedGiveaways = old('giveaways', []); @endphp
                                @foreach ($giveaways->where('code', 'GW-01') as $g)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="giveaways[]"
                                            value="{{ $g->id }}" id="giveaway-{{ $g->id }}"
                                            {{ in_array($g->id, $selectedGiveaways) ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="giveaway-{{ $g->id }}">{{ $g->code }} -
                                            {{ $g->name }}</label>
                                    </div>
                                @endforeach
                                <textarea name="giveaway_note" rows="3" class="form-control"
                                    placeholder="Write here what giveaway you are giving...">{{ old('giveaway_note') }}</textarea>
                            </div>
                        </div>

                        {{-- ================= TERMS & CONDITION ================= --}}
                        <div class="tab-pane fade card" id="tab-terms">
                            <div class="card-body">
                                <h5 class="mb-3">Terms & Condition</h5>
                                <textarea name="terms_content" rows="8" class="form-control" placeholder="Write your content here...">{{ old('terms_content') }}</textarea>
                            </div>
                        </div>

                        {{-- ================= ITINERARY ================= --}}
                        <div class="tab-pane fade card" id="tab-itinerary">
                            <div class="card-body">
                                <h5 class="mb-3">Description</h5>
                                <textarea name="itinerary_description" rows="8" class="form-control mb-4"
                                    placeholder="Write your content here...">{{ old('itinerary_description') }}</textarea>

                                <h5 class="mb-3">Images</h5>
                                <div class="row g-3">
                                    @foreach ([
            'mina_image' => 'MINA',
            'arafat_image' => 'ARAFAT',
            'muzdalifah_image' => 'MUZDALIFAH',
            'makkah_mina_rami_day_one_image' => 'MAKKAH / MINA RAMI - DAY ONE',
            'mina_rami_day_two_image' => 'MINA RAMI - DAY TWO',
            'mina_makkah_rami_day_three_image' => 'MINA / MAKKAH RAMI - DAY THREE',
        ] as $field => $label)
                                        <div class="col-md-4">
                                            <label class="form-label">{{ $label }}</label>
                                            <input type="file" name="{{ $field }}" class="form-control">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- ================= MAKTAB ADDRESS ================= --}}
                        <div class="tab-pane fade card" id="tab-maktab">
                            <div class="card-body">
                                <h5 class="mb-3">Maktab Address</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Maktab Address</label>
                                        <input type="text" name="maktab_address" class="form-control"
                                            value="{{ old('maktab_address') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Office Address</label>
                                        <input type="text" name="office_address" class="form-control"
                                            value="{{ old('office_address') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Notes --}}

                        <div class="tab-pane fade card" id="tab-notes">
                            <div class="card-body">
                                <h5 class="mb-3">Notes & Price Disclaimer</h5>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">Notes</label>
                                        <textarea name="notes" id="notes-editor" class="form-control">{{ old('notes') }}</textarea>
                                    </div>

                                    <div class="col-md-12 mt-4">
                                        <label class="form-label fw-semibold">Price Disclaimer</label>
                                        <textarea name="price_disclaimer" id="disclaimer-editor" class="form-control">{{ old('price_disclaimer', '"Book Early, Prices and Packages Subject to Change."') }}</textarea>
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <label class="form-label fw-semibold">Jeddah Airport Taxi Fare (SAR / Person)</label>
                                        <input type="text" name="jeddah_taxi_fare" class="form-control"
                                            placeholder="e.g. 600" value="{{ old('jeddah_taxi_fare', '600') }}">
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <label class="form-label fw-semibold">Madinah Airport Taxi Fare (SAR / Person)</label>
                                        <input type="text" name="madinah_taxi_fare" class="form-control"
                                            placeholder="e.g. 150" value="{{ old('madinah_taxi_fare', '150') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Feature Icons Box --}}
                        @php
                            $feat = old('feature_icons', []);

                            $defZoneBadge = $feat['item_1']['badge_zone'] ?? 'ZONE 1';
                            $defMaktabBadge = $feat['item_1']['badge_maktab'] ?? 'MAKTAB A-CATEGORY';
                            $defItem1Title = $feat['item_1']['title'] ?? 'BEST LOCATION IN MINA';
                            $defItem1Desc = $feat['item_1']['desc'] ?? "AVG16 PEOPLE TO A TENT\nSOFACUM BED SIZE 50-55 CM EACH\n(TENT MAY BE COMBINED)\nAS PER SAUDI TALIMAAT";

                            $defItem2Icon = $feat['item_2']['icon'] ?? 'assets/images/package_images/1.png';
                            $defItem2Text = $feat['item_2']['text'] ?? "MAKKAH AND\nMEDINAH HOTELS\nHALF BOARD BASIS";

                            $defItem3Icon = $feat['item_3']['icon'] ?? 'assets/images/package_images/2.png';
                            $defItem3Text = $feat['item_3']['text'] ?? "FULL BOARD BUFFET\nMEAL IN MINA & ARAFAT\nFor Group Maktab A Category Hujjaj";

                            $defItem4Icon = $feat['item_4']['icon'] ?? 'assets/images/package_images/2.png';
                            $defItem4Text = $feat['item_4']['text'] ?? "AZIZIYA ACCOMMODATION\nQUAD SHARING, FULL\nBOARD BUFFET";

                            $defItem5Icon = $feat['item_5']['icon'] ?? 'assets/images/package_images/3.png';
                            $defItem5Text = $feat['item_5']['text'] ?? "PRIVATE BATHROOM\nIN MINA & ARAFAT FOR\nUB GROUP";

                            $defItem6Icon = $feat['item_6']['icon'] ?? 'assets/images/package_images/4.png';
                            $defItem6Text = $feat['item_6']['text'] ?? "BULLET TRAIN MAK-MED OR MED-MAK\nPRIVATE LUXURY BUSSES\nMODEL 2025 FOR MASHAER\nDAYS WITH BATHROOM";

                            $presetIcons = [
                                'assets/images/package_images/1.png' => 'Icon 1 - Dining / Half Board (Cutlery)',
                                'assets/images/package_images/2.png' => 'Icon 2 - Buffet / Tent (Meals)',
                                'assets/images/package_images/3.png' => 'Icon 3 - Bathroom / Washroom',
                                'assets/images/package_images/4.png' => 'Icon 4 - Transport / Train & Luxury Bus',
                            ];
                        @endphp

                        <div class="tab-pane fade card" id="tab-feature-icons">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <h5 class="mb-1">Feature Icons & Box Details (Page 1 Left Box)</h5>
                                        <p class="text-muted fs-13 mb-0">Customize the 6 items, icons, and text displayed inside the gold-bordered box on the package brochure.</p>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <!-- Item 1: Zone & Maktab Info -->
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 h-100 bg-light bg-opacity-25">
                                            <h6 class="fw-bold text-primary mb-3">Block 1: Zone & Maktab Banner</h6>
                                            <div class="row g-2 mb-2">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold fs-12">Zone Badge Text</label>
                                                    <input type="text" name="feature_icons[item_1][badge_zone]" class="form-control form-control-sm"
                                                        value="{{ $defZoneBadge }}" placeholder="e.g. ZONE 1">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold fs-12">Maktab Sub-Badge Text</label>
                                                    <input type="text" name="feature_icons[item_1][badge_maktab]" class="form-control form-control-sm"
                                                        value="{{ $defMaktabBadge }}" placeholder="e.g. MAKTAB A-CATEGORY">
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label fw-semibold fs-12">Heading / Line 1</label>
                                                <input type="text" name="feature_icons[item_1][title]" class="form-control form-control-sm"
                                                    value="{{ $defItem1Title }}" placeholder="e.g. BEST LOCATION IN MINA">
                                            </div>
                                            <div>
                                                <label class="form-label fw-semibold fs-12">Details / Description</label>
                                                <textarea name="feature_icons[item_1][desc]" class="form-control form-control-sm" rows="3"
                                                    placeholder="Enter text (new lines supported)">{{ $defItem1Desc }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item 2: Hotels Meal -->
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 h-100 bg-light bg-opacity-25">
                                            <h6 class="fw-bold text-primary mb-3">Block 2: Hotels & Meal Plan</h6>
                                            <div class="row g-2 mb-2 align-items-center">
                                                <div class="col-md-7">
                                                    <label class="form-label fw-semibold fs-12">Preset Icon</label>
                                                    <select name="feature_icons[item_2][icon]" class="form-select form-select-sm">
                                                        @foreach($presetIcons as $path => $label)
                                                            <option value="{{ $path }}" {{ $defItem2Icon == $path ? 'selected' : '' }}>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label fw-semibold fs-12">Custom Icon (Optional)</label>
                                                    <input type="file" name="feature_icons_files[item_2]" class="form-control form-control-sm" accept="image/*">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="form-label fw-semibold fs-12">Description Text</label>
                                                <textarea name="feature_icons[item_2][text]" class="form-control form-control-sm" rows="3"
                                                    placeholder="Enter text (new lines supported)">{{ $defItem2Text }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item 3: Mina & Arafat Meal -->
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 h-100 bg-light bg-opacity-25">
                                            <h6 class="fw-bold text-primary mb-3">Block 3: Mina & Arafat Meal</h6>
                                            <div class="row g-2 mb-2 align-items-center">
                                                <div class="col-md-7">
                                                    <label class="form-label fw-semibold fs-12">Preset Icon</label>
                                                    <select name="feature_icons[item_3][icon]" class="form-select form-select-sm">
                                                        @foreach($presetIcons as $path => $label)
                                                            <option value="{{ $path }}" {{ $defItem3Icon == $path ? 'selected' : '' }}>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label fw-semibold fs-12">Custom Icon (Optional)</label>
                                                    <input type="file" name="feature_icons_files[item_3]" class="form-control form-control-sm" accept="image/*">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="form-label fw-semibold fs-12">Description Text</label>
                                                <textarea name="feature_icons[item_3][text]" class="form-control form-control-sm" rows="3"
                                                    placeholder="Enter text (new lines supported)">{{ $defItem3Text }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item 4: Aziziya Accommodation -->
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 h-100 bg-light bg-opacity-25">
                                            <h6 class="fw-bold text-primary mb-3">Block 4: Aziziya Accommodation</h6>
                                            <div class="row g-2 mb-2 align-items-center">
                                                <div class="col-md-7">
                                                    <label class="form-label fw-semibold fs-12">Preset Icon</label>
                                                    <select name="feature_icons[item_4][icon]" class="form-select form-select-sm">
                                                        @foreach($presetIcons as $path => $label)
                                                            <option value="{{ $path }}" {{ $defItem4Icon == $path ? 'selected' : '' }}>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label fw-semibold fs-12">Custom Icon (Optional)</label>
                                                    <input type="file" name="feature_icons_files[item_4]" class="form-control form-control-sm" accept="image/*">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="form-label fw-semibold fs-12">Description Text</label>
                                                <textarea name="feature_icons[item_4][text]" class="form-control form-control-sm" rows="3"
                                                    placeholder="Enter text (new lines supported)">{{ $defItem4Text }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item 5: Private Bathroom -->
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 h-100 bg-light bg-opacity-25">
                                            <h6 class="fw-bold text-primary mb-3">Block 5: Private Bathroom</h6>
                                            <div class="row g-2 mb-2 align-items-center">
                                                <div class="col-md-7">
                                                    <label class="form-label fw-semibold fs-12">Preset Icon</label>
                                                    <select name="feature_icons[item_5][icon]" class="form-select form-select-sm">
                                                        @foreach($presetIcons as $path => $label)
                                                            <option value="{{ $path }}" {{ $defItem5Icon == $path ? 'selected' : '' }}>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label fw-semibold fs-12">Custom Icon (Optional)</label>
                                                    <input type="file" name="feature_icons_files[item_5]" class="form-control form-control-sm" accept="image/*">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="form-label fw-semibold fs-12">Description Text</label>
                                                <textarea name="feature_icons[item_5][text]" class="form-control form-control-sm" rows="3"
                                                    placeholder="Enter text (new lines supported)">{{ $defItem5Text }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item 6: Transport / Luxury Bus -->
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 h-100 bg-light bg-opacity-25">
                                            <h6 class="fw-bold text-primary mb-3">Block 6: Transport & Luxury Bus</h6>
                                            <div class="row g-2 mb-2 align-items-center">
                                                <div class="col-md-7">
                                                    <label class="form-label fw-semibold fs-12">Preset Icon</label>
                                                    <select name="feature_icons[item_6][icon]" class="form-select form-select-sm">
                                                        @foreach($presetIcons as $path => $label)
                                                            <option value="{{ $path }}" {{ $defItem6Icon == $path ? 'selected' : '' }}>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label fw-semibold fs-12">Custom Icon (Optional)</label>
                                                    <input type="file" name="feature_icons_files[item_6]" class="form-control form-control-sm" accept="image/*">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="form-label fw-semibold fs-12">Description Text</label>
                                                <textarea name="feature_icons[item_6][text]" class="form-control form-control-sm" rows="3"
                                                    placeholder="Enter text (new lines supported)">{{ $defItem6Text }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Services (Page 2) --}}
                        @php
                            $defaultServicesContent = '<ul>
    <li>Meet & assist at the airport Jeddah/Medinah Hajj Terminal. (Sub to Approval Handling).</li>
    <li>Group arrival transfer by Bus from Airport to hotel is provided by NAQABA / SAUDI MOULLEM.</li>
    <li>Average 04 person sharing Accommodation in Aziziya with air condition A class building with proper beds (Pillow, Bed sheet, Blanket).</li>
    <li>Fullboard meal (Breakfast, Lunch & dinner) with hot & Coldrink to serve in Aziziya building except Hajj Days.</li>
    <li>Aziziya Services please reference to Page # 25 & 25A.</li>
    <li>Accommodation in Makkah hotels with Breakfast & Dinner (by Saudi Star Standard).</li>
    <li>Accommodation in Medinah hotels with Breakfast & Dinner (by Saudi Star Standard).</li>
    <li>During peak days from the 1st to the 14th of Zill Hajj, the check-in time at hotels in Makkah and Medinah is after Isha prayers, in accordance with hotel policies and due to the large number of check-ins and check-outs. The check-out time is 12 PM</li>
    <li>Fullboard meal to be serve in Mina from 08 Zil hajj to 12 Zil hajj.</li>
    <li>5 days Platinum Arrangment between 08 Zil hajj to 12 Zil hajj with retaining room in Aziziya.</li>
    <li>Private Special Luxury Busses with Bathroom (Mina - Arafat - Muzdalfa - Mina).</li>
    <li>Transfer Makkah to Medinah or Medinah to Makkah by Bus/Train.</li>
    <li>Best location MAKTAB (A) in mina very near to Jamarat, with Sofa Cum Bed (size 50 to 55cm) Private Toilet, for Group MAKTAB (A) Category Hujjaj. (Indian and western) (Services by Saudi Company)</li>
    <li>(Mashaer Hajj Services) Pillow, Bed sheet, blanket, Air conditioned tent, buffet meal & Hot & Coldrink. Avg 16 people to a tent (Tent may be combined). (Services by Saudi Company)</li>
    <li>Tent in Arafat with meals and Hot & Coldrink. Floor Mat & snack box in Muzdalfa. (Services by Saudi Company)</li>
    <li>Mic and Speaker are installed to the religious speeches for guidance.</li>
</ul>

<p><strong>Airline Ticket not included in this package<br>
(Approx PKR 335,000/- FROM KARACHI & PKR 345,000/- FROM NORTH PAKISTAN.)</strong></p>

<p>Different fares for Hajis coming from international destination.</p>
<ul>
    <li>Saudi Airline, Emirates, Oman, PIA, Qatar, SereneAir, FlyNas, Turkish Airlines, Fly Dubai etc Inclusive PSF.</li>
    <li>International ticket may be upgraded to Business class by paying suppliment. (Subject to Availability)</li>
    <li>Ziyarat in Medinah with guidance.</li>
    <li>Hajj training program and guidance in Pakistan / Saudia.</li>
    <li>Religious guide book etc.</li>
    <li>Assitance in doing Qurbani Approx Charges SAR 720/-</li>
    <li>Assitance in Tawaf - e - Ziyara.</li>
</ul>

<p><strong>IMPORTANT NOTES:</strong></p>
<ol>
    <li>No of days of stay in Makkah can be reduced but prices remain the same.</li>
    <li>Shuttle will be provided two times a day for drop to Haram till 07 Zil hajj, (Shuttle services subject to Saudi Laws and traffic).</li>
    <li>Abraaj Tower Means Swiss Maqam, Hajar tower, Swissotel, Safwa orchid, Al marwa etc & Project of Jabal e Omar, means Hayat Regency, Address Hotel, Jumeirah Hotel, Hilton Convention, Double Tree, Marriot Hotel etc.</li>
    <li>Kaba view Supplement SAR 3800/- per person.</li>
    <li>Rates & Hotels subject to change (Currency Difference) prices are subject to change. Even after booking /Saudi Talimaat changes.</li>
</ol>';
                        @endphp
                        <div class="tab-pane fade card" id="tab-services">
                            <div class="card-body">
                                <h5 class="mb-3">Package Services & Notes (Page 2 in Brochure / PDF)</h5>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">Services Header Title</label>
                                        <input type="text" name="services_title" class="form-control"
                                            placeholder="e.g. PLATINUM PACKAGES SERVICES (WITH AZIZIYA)"
                                            value="{{ old('services_title', 'PLATINUM PACKAGES SERVICES (WITH AZIZIYA)') }}">
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <label class="form-label fw-semibold">Services & Important Notes Content</label>
                                        <textarea name="services_content" id="services-editor" class="form-control">{{ old('services_content', $defaultServicesContent) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end" style="margin: 20px">
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <script>
        (function() {
            let accIndex = 1;

            function resetRowFields(clone) {
                clone.querySelectorAll('input, textarea, select').forEach(function(el) {
                    el.name = el.name.replace(/accommodations\[\d+\]/, 'accommodations[' + accIndex + ']');
                    if (el.tagName === 'SELECT') {
                        el.selectedIndex = 0;
                    } else if (el.type === 'checkbox' || el.type === 'radio') {
                        el.checked = false;
                    } else if (el.type !== 'button') {
                        el.value = '';
                    }
                });

                // Make sure Package B is visible again on new rows
                const bWrap = clone.querySelector('.package-b-block-wrap');
                if (bWrap) bWrap.style.display = '';
            }

            document.getElementById('addAccommodationBtn').addEventListener('click', function() {
                const firstRow = document.querySelector('.accommodation-row');
                const clone = firstRow.cloneNode(true);

                resetRowFields(clone);

                document.getElementById('accommodationRows').appendChild(clone);
                accIndex++;
            });

            document.getElementById('accommodationRows').addEventListener('click', function(e) {
                if (e.target.closest('.remove-accommodation-row')) {
                    const row = e.target.closest('.accommodation-row');
                    if (document.querySelectorAll('.accommodation-row').length > 1) {
                        row.remove();
                    } else {
                        row.querySelectorAll('input, textarea').forEach(el => {
                            if (el.type === 'checkbox' || el.type === 'radio') {
                                el.checked = false;
                            } else {
                                el.value = '';
                            }
                        });
                        row.querySelectorAll('select').forEach(el => el.selectedIndex = 0);
                        const bWrap = row.querySelector('.package-b-block-wrap');
                        if (bWrap) bWrap.style.display = '';
                    }
                }
            });

            // ---- Same Hotel for Package A & B toggle ----
            document.getElementById('accommodationRows').addEventListener('change', function(e) {
                if (e.target.classList.contains('same-for-both-toggle')) {
                    const row = e.target.closest('.accommodation-row');
                    const bWrap = row.querySelector('.package-b-block-wrap');
                    if (!bWrap) return;
                    bWrap.style.display = e.target.checked ? 'none' : '';
                }
            });

            // ---- Auto calculate Days & Nights from Check In / Check Out ----
            function calculateDaysNights(row) {
                const checkInEl = row.querySelector('input[name*="[check_in]"]');
                const checkOutEl = row.querySelector('input[name*="[check_out]"]');
                const daysEl = row.querySelector('input[name*="[days]"]');
                const nightsEl = row.querySelector('input[name*="[nights]"]');

                if (!checkInEl || !checkOutEl || !daysEl || !nightsEl) return;

                const checkInVal = checkInEl.value;
                const checkOutVal = checkOutEl.value;

                if (!checkInVal || !checkOutVal) return;

                const checkIn = new Date(checkInVal);
                const checkOut = new Date(checkOutVal);

                const diffTime = checkOut - checkIn;
                const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));

                if (diffDays > 0) {
                    nightsEl.value = diffDays;
                    daysEl.value = diffDays + 1;
                } else {
                    nightsEl.value = '';
                    daysEl.value = '';
                }
            }

            document.getElementById('accommodationRows').addEventListener('change', function(e) {
                if (e.target.matches('input[name*="[check_in]"], input[name*="[check_out]"]')) {
                    const row = e.target.closest('.accommodation-row');
                    if (row) calculateDaysNights(row);
                }
            });

            // ---- On submit: if "same for both" is checked, copy Package A values into Package B ----
            const formEl = document.querySelector('form');
            if (formEl) {
                formEl.addEventListener('submit', function() {
                    document.querySelectorAll('.accommodation-row').forEach(function(row) {
                        const same = row.querySelector('.same-for-both-toggle');
                        if (!same || !same.checked) return;

                        const aType = row.querySelector(
                            'select[name*="[package_a][accommodation_type]"]');
                        const aStar = row.querySelector(
                            'select[name*="[package_a][saudi_star_rating]"]');
                        const aHotel = row.querySelector('select[name*="[package_a][hotel]"]');

                        const bType = row.querySelector(
                            'select[name*="[package_b][accommodation_type]"]');
                        const bStar = row.querySelector(
                            'select[name*="[package_b][saudi_star_rating]"]');
                        const bHotel = row.querySelector('select[name*="[package_b][hotel]"]');

                        if (bType && aType) bType.value = aType.value;
                        if (bStar && aStar) bStar.value = aStar.value;
                        if (bHotel && aHotel) bHotel.value = aHotel.value;
                    });
                });
            }
        })();

        // ---- Transport repeatable rows ----
        (function() {
            let transportIndex = 1;

            document.getElementById('addTransportBtn').addEventListener('click', function() {
                const firstRow = document.querySelector('.transport-row');
                const clone = firstRow.cloneNode(true);

                clone.querySelectorAll('input').forEach(function(el) {
                    el.name = el.name.replace(/transports\[\d+\]/, 'transports[' + transportIndex +
                        ']');
                    el.value = '';
                });

                document.getElementById('transportRows').appendChild(clone);
                transportIndex++;
            });

            document.getElementById('transportRows').addEventListener('click', function(e) {
                if (e.target.closest('.remove-transport-row')) {
                    const row = e.target.closest('.transport-row');
                    if (document.querySelectorAll('.transport-row').length > 1) {
                        row.remove();
                    } else {
                        row.querySelectorAll('input').forEach(el => el.value = '');
                    }
                }
            });
        })();

        // ---- Flight repeatable rows ----
        (function() {
            let flightIndex = 1;

            document.getElementById('addFlightBtn').addEventListener('click', function() {
                const firstRow = document.querySelector('.flight-row');
                const clone = firstRow.cloneNode(true);

                clone.querySelectorAll('input, select').forEach(function(el) {
                    el.name = el.name.replace(/flights\[\d+\]/, 'flights[' + flightIndex + ']');
                    if (el.tagName.toLowerCase() === 'select') {
                        el.selectedIndex = 0;
                    } else if (el.type === 'radio') {
                        el.checked = el.value === '0';
                    } else {
                        el.value = '';
                    }
                });

                document.getElementById('flightRows').appendChild(clone);
                flightIndex++;
            });

            document.getElementById('flightRows').addEventListener('click', function(e) {
                if (e.target.closest('.remove-flight-row')) {
                    const row = e.target.closest('.flight-row');
                    if (document.querySelectorAll('.flight-row').length > 1) {
                        row.remove();
                        row.querySelectorAll(
                                'input[type="text"], input[type="date"], input[type="time"], input[type="number"], select'
                            )
                            .forEach(el => {
                                if (el.tagName.toLowerCase() === 'select') {
                                    el.selectedIndex = 0;
                                } else {
                                    el.value = '';
                                }
                            });
                        row.querySelectorAll('input[type="radio"]').forEach(el => el.checked = el.value ===
                            '0');
                    }
                }
            });
        })();

        // ---- Train repeatable rows ----
        (function() {
            let trainIndex = 1;

            document.getElementById('addTrainBtn').addEventListener('click', function() {
                const firstRow = document.querySelector('.train-row');
                const clone = firstRow.cloneNode(true);

                clone.querySelectorAll('input, select').forEach(function(el) {
                    el.name = el.name.replace(/trains\[\d+\]/, 'trains[' + trainIndex + ']');
                    if (el.tagName.toLowerCase() === 'select') {
                        el.selectedIndex = 0;
                    } else {
                        el.value = '';
                    }
                });

                document.getElementById('trainRows').appendChild(clone);
                trainIndex++;
            });

            document.getElementById('trainRows').addEventListener('click', function(e) {
                if (e.target.closest('.remove-train-row')) {
                    const row = e.target.closest('.train-row');
                    if (document.querySelectorAll('.train-row').length > 1) {
                        row.remove();
                    } else {
                        row.querySelectorAll('input').forEach(el => el.value = '');
                    }
                }
            });
        })();

        // Initialize Summernote Lite
        window.addEventListener('DOMContentLoaded', function() {
            var checkjQuery = setInterval(function () {
                if (typeof jQuery !== 'undefined') {
                    clearInterval(checkjQuery);
                    jQuery.getScript('https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js', function() {
                        jQuery('#notes-editor').summernote({
                            placeholder: 'Write package notes here...',
                            tabsize: 2,
                            height: 200,
                            toolbar: [
                                ['style', ['style']],
                                ['font', ['bold', 'underline', 'clear']],
                                ['color', ['color']],
                                ['para', ['ul', 'ol', 'paragraph']],
                                ['table', ['table']],
                                ['insert', ['link', 'picture', 'video']],
                                ['view', ['fullscreen', 'codeview', 'help']]
                            ]
                        });
                        jQuery('#disclaimer-editor').summernote({
                            placeholder: 'Write price disclaimer here...',
                            tabsize: 2,
                            height: 100,
                            toolbar: [
                                ['style', ['style']],
                                ['font', ['bold', 'underline', 'clear']],
                                ['color', ['color']],
                                ['para', ['ul', 'ol', 'paragraph']],
                                ['view', ['codeview']]
                            ]
                        });
                        jQuery('#services-editor').summernote({
                            placeholder: 'Write package services and terms here...',
                            tabsize: 2,
                            height: 350,
                            toolbar: [
                                ['style', ['style']],
                                ['font', ['bold', 'underline', 'clear']],
                                ['color', ['color']],
                                ['para', ['ul', 'ol', 'paragraph']],
                                ['table', ['table']],
                                ['insert', ['link', 'picture']],
                                ['view', ['fullscreen', 'codeview', 'help']]
                            ]
                        });
                    });
                }
            }, 100);
        });
    </script>
@endsection
