@extends('layout.mobile-master')

@section('content')
    <div class="pt-3 pb-2">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fs-18 fw-bold text-dark m-0">Create Umrah Package</h4>
                <p class="text-muted fs-13 mb-0">Build an Umrah package with hotel options, flights, multi-city destinations,
                    and dates.</p>
            </div>
            <a href="{{ route('mobile-ops.umrah-packages.index') }}"
                class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="mdi mdi-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('mobile-ops.umrah-packages.store') }}" method="POST" enctype="multipart/form-data"
        class="mt-3">
        @csrf

        <!-- 1. Basic Package Information & Dual Country / Multi-City Section -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fs-15 fw-bold text-dark m-0">
                    Basic Package Information</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="package_title" class="form-control"
                            placeholder="e.g. 14 Days Premium Umrah Package" value="{{ old('package_title') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Package Code</label>
                        <input type="text" name="package_code" class="form-control" placeholder="e.g. UMR-2026-01"
                            value="{{ old('package_code') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Visa</label>
                        <select name="visa_status" class="form-select">
                            <option value="included" {{ old('visa_status', 'included') === 'included' ? 'selected' : '' }}>
                                Included</option>
                            <option value="excluded" {{ old('visa_status') === 'excluded' ? 'selected' : '' }}>Excluded
                            </option>
                        </select>
                    </div>

                    {{-- <div class="col-md-3 d-flex align-items-center pt-md-3">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_group_package" id="isGroupPkg"
                                value="1" {{ old('is_group_package') ? 'checked' : '' }}
                                style="cursor: pointer; width: 45px; height: 22px;">
                            <label class="form-check-label fw-semibold ms-2" for="isGroupPkg">Mark as Group Package</label>
                        </div>
                    </div> --}}

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Subtitle</label>
                        <input type="text" name="subtitle" class="form-control"
                            placeholder="e.g. Economy & 5 Star Options with Ziyarat" value="{{ old('subtitle') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active
                            </option>
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Dual Country Toggle -->
                    <div class="col-12 mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center mb-3">
                            <div class="form-check form-switch me-3">
                                <input class="form-check-input" type="checkbox" name="is_dual_country"
                                    id="dualCountrySwitch" value="1" {{ old('is_dual_country') ? 'checked' : '' }}
                                    style="cursor: pointer; width: 48px; height: 24px;">
                            </div>
                            <label class="form-check-label fw-bold text-dark fs-14 cursor-pointer" for="dualCountrySwitch">
                                Dual Country / Multi-City Package
                            </label>
                        </div>

                        <!-- Dual Country Dynamic Destination Container -->
                        <div id="dualCountrySection" style="{{ old('is_dual_country') ? '' : 'display: none;' }}">
                            <label class="form-label fw-semibold text-secondary mb-2">Additional Destination Countries /
                                Cities</label>

                            <div id="destinationsContainer" class="d-flex flex-column gap-2 mb-3">
                                <!-- Template / Items injected by JS -->
                            </div>

                            <button type="button" id="addDestinationBtn" class="btn btn-sm btn-dark px-3 py-1 rounded"
                                style="background-color: #0d4b43; border-color: #0d4b43;">
                                <i class="mdi mdi-plus me-1"></i> Add More Destination
                            </button>

                            <!-- Dynamic Hotel Cards for each Additional Destination -->
                            <div id="destinationHotelCardsContainer" class="mt-4 d-flex flex-column gap-3">
                                <!-- Dynamic Hotel Details for Selected Countries will be rendered here -->
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-3">
                        <label class="form-label fw-semibold">Package Banner / Image</label>
                        <input type="file" name="package_image" class="form-control" accept="image/*">
                        <small class="text-muted fs-11">Recommended: 1200x600 JPG, PNG, WEBP. Max 5MB.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Hotel Accommodations Section -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fs-15 fw-bold text-dark m-0 d-flex align-items-center">
                    <i class="mdi mdi-office-building text-success me-2 fs-18"></i>
                    <span>Hotel Accommodations</span>
                </h5>
            </div>

            <div class="card-body p-4">
                <!-- Makkah Hotel Card -->
                <div class="border rounded p-3 mb-4 bg-light-subtle" style="border-color: #e9ecef !important;">
                    <div class="d-flex align-items-center mb-3">

                        <h6 class="fs-14 fw-bold text-dark m-0">Makkah Hotel Details</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold fs-13">Makkah Hotel Name</label>
                            <select name="makkah_hotel" class="form-select hotel-dropdown-select" data-city="makkah">
                                <option value="">Select Makkah Hotel</option>
                                @foreach ($makkahHotels as $hotel)
                                    <option value="{{ $hotel->name }}" data-distance="{{ $hotel->distance ?? '' }}"
                                        data-category="{{ $hotel->accommodation_category ?? ($hotel->category ?? '') }}"
                                        {{ old('makkah_hotel') === $hotel->name ? 'selected' : '' }}>
                                        {{ $hotel->name }} @if (!empty($hotel->accommodation_category ?? $hotel->category))
                                            ({{ $hotel->accommodation_category ?? $hotel->category }})
                                        @endif
                                    </option>
                                @endforeach
                                @if (old('makkah_hotel') && !$makkahHotels->contains('name', old('makkah_hotel')))
                                    <option value="{{ old('makkah_hotel') }}" selected>{{ old('makkah_hotel') }}</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold fs-13">Makkah Hotel Distance</label>
                            <input type="text" name="makkah_distance" id="makkah_distance" class="form-control"
                                placeholder="e.g. 550 Meters" value="{{ old('makkah_distance') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold fs-13">Makkah Hotel Period</label>
                            <input type="text" name="makkah_period" class="form-control"
                                placeholder="e.g. 15 Zil Qadda to 25 Zil Qadda" value="{{ old('makkah_period') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold fs-13">Makkah Hotel Meal Plan</label>
                            <select name="makkah_meal_plan" class="form-select">
                                <option value="">Select Meal Plan</option>
                                @foreach ($mealPlans as $plan)
                                    <option value="{{ $plan }}"
                                        {{ old('makkah_meal_plan') === $plan ? 'selected' : '' }}>{{ $plan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold fs-13">Makkah Hotel Category</label>
                            <select name="makkah_room_category" id="makkah_room_category" class="form-select">
                                <option value="">Select Category</option>
                                @foreach ($hotelCategories as $cat)
                                    <option value="{{ $cat }}"
                                        {{ old('makkah_room_category') === $cat ? 'selected' : '' }}>{{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Madinah Hotel Card -->
                <div class="border rounded p-3 bg-light-subtle" style="border-color: #e9ecef !important;">
                    <div class="d-flex align-items-center mb-3">

                        <h6 class="fs-14 fw-bold text-dark m-0">Medina Hotel Details</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold fs-13">Madinah Hotel Name</label>
                            <select name="madinah_hotel" class="form-select hotel-dropdown-select" data-city="madinah">
                                <option value="">Select Madinah Hotel</option>
                                @foreach ($madinahHotels as $hotel)
                                    <option value="{{ $hotel->name }}" data-distance="{{ $hotel->distance ?? '' }}"
                                        data-category="{{ $hotel->accommodation_category ?? ($hotel->category ?? '') }}"
                                        {{ old('madinah_hotel') === $hotel->name ? 'selected' : '' }}>
                                        {{ $hotel->name }} @if (!empty($hotel->accommodation_category ?? $hotel->category))
                                            ({{ $hotel->accommodation_category ?? $hotel->category }})
                                        @endif
                                    </option>
                                @endforeach
                                @if (old('madinah_hotel') && !$madinahHotels->contains('name', old('madinah_hotel')))
                                    <option value="{{ old('madinah_hotel') }}" selected>{{ old('madinah_hotel') }}
                                    </option>
                                @endif
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold fs-13">Madinah Hotel Distance</label>
                            <input type="text" name="madinah_distance" id="madinah_distance" class="form-control"
                                placeholder="e.g. 350 Meters" value="{{ old('madinah_distance') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold fs-13">Madinah Hotel Period</label>
                            <input type="text" name="madinah_period" class="form-control"
                                placeholder="e.g. 25 Zil Qadda to 04 Zil Hujja" value="{{ old('madinah_period') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold fs-13">Madinah Hotel Meal Plan</label>
                            <select name="madinah_meal_plan" class="form-select">
                                <option value="">Select Meal Plan</option>
                                @foreach ($mealPlans as $plan)
                                    <option value="{{ $plan }}"
                                        {{ old('madinah_meal_plan') === $plan ? 'selected' : '' }}>{{ $plan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold fs-13">Madinah Hotel Category</label>
                            <select name="madinah_room_category" id="madinah_room_category" class="form-select">
                                <option value="">Select Category</option>
                                @foreach ($hotelCategories as $cat)
                                    <option value="{{ $cat }}"
                                        {{ old('madinah_room_category') === $cat ? 'selected' : '' }}>{{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Flights & Itinerary Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fs-15 fw-bold text-dark m-0"><i class="mdi mdi-airplane text-primary me-2"></i>Flight
                    Details & Itinerary</h5>
            </div>
            <div class="card-body p-4">
                <!-- Departure Flight (Outbound) -->
                <div class="border rounded p-3 mb-4 bg-light-subtle">
                    <h6 class="fs-14 fw-bold text-dark mb-3"><i class="mdi mdi-airplane-takeoff text-primary me-1"></i>
                        Departure Flight (Outbound)</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Airline Name</label>
                            <input type="text" name="outbound_airline" class="form-control"
                                placeholder="e.g. Saudia / PIA / AirBlue" value="{{ old('outbound_airline') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Flight Type</label>
                            <select name="outbound_flight_type" class="form-select">
                                <option value="">Select Flight Type</option>
                                @foreach ($flightTypes as $ftype)
                                    <option value="{{ $ftype }}"
                                        {{ old('outbound_flight_type') === $ftype ? 'selected' : '' }}>{{ $ftype }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Departure Airport / City</label>
                            <input type="text" name="outbound_origin" class="form-control"
                                placeholder="e.g. KHI / LHE / ISB" value="{{ old('outbound_origin') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Arrival Airport / City</label>
                            <input type="text" name="outbound_destination" class="form-control"
                                placeholder="e.g. JED / MED" value="{{ old('outbound_destination') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Flight Notes & Baggage Info</label>
                            <input type="text" name="outbound_notes" class="form-control"
                                placeholder="e.g. 30kg Baggage + 7kg Hand carry, Zamzam allowed"
                                value="{{ old('outbound_notes') }}">
                        </div>
                    </div>
                </div>

                <!-- Return Flight Details (Inbound) -->
                <div class="border rounded p-3 bg-light-subtle">
                    <h6 class="fs-14 fw-bold text-dark mb-3"><i class="mdi mdi-airplane-landing text-success me-1"></i>
                        Return Flight Details (Inbound)</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Return Airline Name</label>
                            <input type="text" name="inbound_airline" class="form-control"
                                placeholder="e.g. Saudia / PIA / AirBlue" value="{{ old('inbound_airline') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Return Flight Type</label>
                            <select name="inbound_flight_type" class="form-select">
                                <option value="">Select Flight Type</option>
                                @foreach ($flightTypes as $ftype)
                                    <option value="{{ $ftype }}"
                                        {{ old('inbound_flight_type') === $ftype ? 'selected' : '' }}>{{ $ftype }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Return Departure Airport / City</label>
                            <input type="text" name="inbound_origin" class="form-control"
                                placeholder="e.g. MED / JED" value="{{ old('inbound_origin') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Return Arrival Airport / City</label>
                            <input type="text" name="inbound_destination" class="form-control"
                                placeholder="e.g. KHI / LHE / ISB" value="{{ old('inbound_destination') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Return Flight Notes & Baggage Info</label>
                            <input type="text" name="inbound_notes" class="form-control"
                                placeholder="e.g. 30kg Baggage + 7kg Hand carry, Zamzam allowed"
                                value="{{ old('inbound_notes') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Dates & Pricing Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fs-15 fw-bold text-success m-0"><i class="mdi mdi-calendar-clock text-success me-2"></i>Dates &
                    Pricing</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Travel Date From (Gregorian Date)</label>
                        <input type="date" name="valid_from" class="form-control" value="{{ old('valid_from') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Travel Date To (Gregorian Date)</label>
                        <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Price (Sharing)</label>
                        <input type="number" step="0.01" name="price_sharing" class="form-control"
                            placeholder="0.00" value="{{ old('price_sharing') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Price (Quad)</label>
                        <input type="number" step="0.01" name="price_quad" class="form-control" placeholder="0.00"
                            value="{{ old('price_quad') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Price (Triple)</label>
                        <input type="number" step="0.01" name="price_triple" class="form-control"
                            placeholder="0.00" value="{{ old('price_triple') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Price (Double)</label>
                        <input type="number" step="0.01" name="price_double" class="form-control"
                            placeholder="0.00" value="{{ old('price_double') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Multiple Duration Options Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fs-15 fw-bold text-success m-0">
                    <i class="mdi mdi-clock-time-four-outline text-success me-2"></i>Multiple Duration Options
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" style="border-color: #e9edf1;">
                        <thead style="background-color: #f1f4f7;">
                            <tr class="text-dark fw-bold fs-13">
                                <th class="ps-3 py-3" style="width: 14%;">Duration</th>
                                <th class="py-3 text-center" style="width: 13%;">Enable</th>
                                <th class="py-3" style="width: 18%;">Price (Sharing)</th>
                                <th class="py-3" style="width: 18%;">Price (Quad)</th>
                                <th class="py-3" style="width: 18%;">Price (Triple)</th>
                                <th class="py-3" style="width: 18%;">Price (Double)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($durationOptions as $duration)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark fs-13">{{ $duration }}</td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center justify-content-center">
                                            <div class="form-check form-switch m-0 d-flex align-items-center">
                                                <input class="form-check-input" type="checkbox"
                                                    name="duration_pricing[{{ $duration }}][enabled]" value="1"
                                                    id="dur_{{ Str::slug($duration) }}"
                                                    {{ old("duration_pricing.{$duration}.enabled") ? 'checked' : '' }}
                                                    style="cursor: pointer; width: 38px; height: 20px;">
                                                <label class="form-check-label fw-semibold ms-2 fs-13 text-dark"
                                                    for="dur_{{ Str::slug($duration) }}"
                                                    style="cursor: pointer;">Active</label>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="0.01"
                                            name="duration_pricing[{{ $duration }}][sharing]"
                                            class="form-control form-control-sm" placeholder="Sharing Price"
                                            value="{{ old("duration_pricing.{$duration}.sharing") }}">
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="0.01"
                                            name="duration_pricing[{{ $duration }}][quad]"
                                            class="form-control form-control-sm" placeholder="Quad Price"
                                            value="{{ old("duration_pricing.{$duration}.quad") }}">
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="0.01"
                                            name="duration_pricing[{{ $duration }}][triple]"
                                            class="form-control form-control-sm" placeholder="Triple Price"
                                            value="{{ old("duration_pricing.{$duration}.triple") }}">
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="0.01"
                                            name="duration_pricing[{{ $duration }}][double]"
                                            class="form-control form-control-sm" placeholder="Double Price"
                                            value="{{ old("duration_pricing.{$duration}.double") }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 5. Transportation Plan & Content Details -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fs-15 fw-bold text-dark m-0"><i
                        class="mdi mdi-file-document-edit-outline text-primary me-2"></i>Transportation Plan & Package
                    Details</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Transportation Plan & Routes</label>
                        <input type="text" name="transport_plan" class="form-control"
                            placeholder="e.g. Jeddah Airport to Makkah Hotel -> Madinah Hotel -> Madinah Airport by Luxury VIP Bus / Private Car"
                            value="{{ old('transport_plan') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Inclusions (What's Included)</label>
                        <textarea name="inclusions" rows="5" class="form-control"
                            placeholder="• Saudi Visa Processing with Insurance&#10;• Complete Transportation in Private AC Car&#10;• Hotel Accommodation near Haram&#10;• 24/7 Ground Assistance">{{ old('inclusions') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Key Features</label>
                        <textarea name="key_features" rows="5" class="form-control"
                            placeholder="• 5-Star Luxury Accommodations&#10;• Walking distance to Holy Mosques&#10;• Direct Flights with Saudia/PIA&#10;• Daily Buffet Breakfast">{{ old('key_features') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Exclusions (What's Not Included)</label>
                        <textarea name="exclusions" rows="4" class="form-control"
                            placeholder="• Personal laundry and phone calls&#10;• Any extra sightseeing trips not mentioned&#10;• Meals other than specified">{{ old('exclusions') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Document Requirements</label>
                        <textarea name="requirements_text" rows="4" class="form-control"
                            placeholder="• Original Passport valid for minimum 6 months&#10;• 2 Passport size photographs with blue/white background&#10;• Copy of valid CNIC">{{ old('requirements_text') }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Terms & Conditions</label>
                        <textarea name="terms_conditions" rows="4" class="form-control"
                            placeholder="Package rates are subject to currency fluctuations and hotel availability at the time of final booking confirmation. Non-refundable cancellation policy applies.">{{ old('terms_conditions') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Toolbar -->
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('mobile-ops.umrah-packages.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                <i class="mdi mdi-content-save me-1"></i> Save Umrah Package
            </button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dualSwitch = document.getElementById('dualCountrySwitch');
            const dualSection = document.getElementById('dualCountrySection');
            const addDestBtn = document.getElementById('addDestinationBtn');
            const destContainer = document.getElementById('destinationsContainer');
            const hotelCardsContainer = document.getElementById('destinationHotelCardsContainer');

            const countries = @json($countriesList);
            const mealPlans = @json($mealPlans);
            const hotelCategories = @json($hotelCategories);

            let destIndex = 0;

            dualSwitch.addEventListener('change', function() {
                if (this.checked) {
                    dualSection.style.display = 'block';
                    if (destContainer.children.length === 0) {
                        addDestinationRow();
                    }
                } else {
                    dualSection.style.display = 'none';
                }
            });

            addDestBtn.addEventListener('click', function() {
                addDestinationRow();
            });

            function addDestinationRow(preSelectedCountry = '', preData = {}) {
                const currentIndex = destIndex++;

                // 1. Destination Country Select Row
                const row = document.createElement('div');
                row.className = 'd-flex align-items-center gap-2 dest-row';
                row.id = `dest_row_${currentIndex}`;

                let optionsHtml = '<option value="">Select Destination Country</option>';
                countries.forEach(c => {
                    const isSelected = (preSelectedCountry === c) ? 'selected' : '';
                    optionsHtml += `<option value="${c}" ${isSelected}>${c}</option>`;
                });

                row.innerHTML = `
            <div class="flex-grow-1">
                <select name="additional_destinations[${currentIndex}][country]" class="form-select dest-country-select" data-index="${currentIndex}">
                    ${optionsHtml}
                </select>
            </div>
            <button type="button" class="btn btn-outline-danger btn-remove-dest" data-index="${currentIndex}" title="Remove Destination">
                <i class="mdi mdi-trash-can-outline"></i>
            </button>
        `;

                destContainer.appendChild(row);

                // 2. Hotel Details Card for this destination
                createHotelCardForDestination(currentIndex, preSelectedCountry, preData);

                // Bind events
                const selectElem = row.querySelector('.dest-country-select');
                selectElem.addEventListener('change', function() {
                    updateHotelCardHeader(currentIndex, this.value);
                });

                row.querySelector('.btn-remove-dest').addEventListener('click', function() {
                    removeDestination(currentIndex);
                });
            }

            function createHotelCardForDestination(index, countryName = '', preData = {}) {
                const card = document.createElement('div');
                card.className = 'card border border-success-subtle shadow-none dest-hotel-card';
                card.id = `dest_hotel_card_${index}`;
                card.style.borderRadius = '10px';

                const displayName = countryName || 'Destination';

                let mealOptions = '<option value="">Select Meal Plan</option>';
                mealPlans.forEach(m => {
                    const isSelected = (preData.hotel_meal_plan === m) ? 'selected' : '';
                    mealOptions += `<option value="${m}" ${isSelected}>${m}</option>`;
                });

                let catOptions = '<option value="">Select Category</option>';
                hotelCategories.forEach(cat => {
                    const isSelected = (preData.hotel_category === cat) ? 'selected' : '';
                    catOptions += `<option value="${cat}" ${isSelected}>${cat}</option>`;
                });

                card.innerHTML = `
            <div class="card-body p-3 bg-white" style="border-radius: 10px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fs-14 fw-bold text-success m-0 dest-card-title">
                        <i class="mdi mdi-office-building me-1"></i> <span class="country-label">${displayName}</span> Hotel Details
                    </h6>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-dest-card-btn" data-index="${index}">
                        <i class="mdi mdi-trash-can-outline me-1"></i> Remove Destination
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold fs-13"><span class="country-label">${displayName}</span> Hotel Name</label>
                        <input type="text" name="additional_destinations[${index}][hotel_name]" class="form-control" placeholder="e.g. B&B Hotel Milano Ornato (3★)" value="${preData.hotel_name || ''}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold fs-13"><span class="country-label">${displayName}</span> Hotel Distance / Address</label>
                        <input type="text" name="additional_destinations[${index}][hotel_distance]" class="form-control" placeholder="e.g. Via Luigi Ornato or 100m" value="${preData.hotel_distance || ''}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold fs-13"><span class="country-label">${displayName}</span> Hotel Period</label>
                        <input type="text" name="additional_destinations[${index}][hotel_period]" class="form-control" placeholder="e.g. 3 Nights or Dates" value="${preData.hotel_period || ''}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold fs-13"><span class="country-label">${displayName}</span> Hotel Meal Plan</label>
                        <select name="additional_destinations[${index}][hotel_meal_plan]" class="form-select">
                            ${mealOptions}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold fs-13"><span class="country-label">${displayName}</span> Hotel Category</label>
                        <select name="additional_destinations[${index}][hotel_category]" class="form-select">
                            ${catOptions}
                        </select>
                    </div>
                </div>
            </div>
        `;

                hotelCardsContainer.appendChild(card);

                card.querySelector('.remove-dest-card-btn').addEventListener('click', function() {
                    removeDestination(index);
                });
            }

            function updateHotelCardHeader(index, countryName) {
                const card = document.getElementById(`dest_hotel_card_${index}`);
                if (card) {
                    const labels = card.querySelectorAll('.country-label');
                    const nameToDisplay = countryName || 'Destination';
                    labels.forEach(l => l.textContent = nameToDisplay);
                }
            }

            function removeDestination(index) {
                const row = document.getElementById(`dest_row_${index}`);
                if (row) row.remove();
                const card = document.getElementById(`dest_hotel_card_${index}`);
                if (card) card.remove();
            }

            // Auto populate category on hotel change for Makkah / Madinah
            document.querySelectorAll('.hotel-dropdown-select').forEach(select => {
                select.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const category = selectedOption.getAttribute('data-category');
                    const type = this.getAttribute('data-type');
                    if (category) {
                        const targetSelect = document.querySelector(`.${type}-room-cat`);
                        if (targetSelect) {
                            Array.from(targetSelect.options).forEach(opt => {
                                if (opt.value.toLowerCase().includes(category
                                        .toLowerCase())) {
                                    opt.selected = true;
                                }
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
