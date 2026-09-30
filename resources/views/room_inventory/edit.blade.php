@extends('layout.master')
@section('title', 'Edit Room Stock Allotment')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                <div>
                    <h4 class="fs-18 fw-semibold mb-1 text-primary">
                        <i class="mdi mdi-pencil me-1"></i> Edit Room Stock Allotment
                    </h4>
                    <p class="text-muted fs-13 mb-0">
                        Update existing inventory stock count, pricing, or stay dates.
                    </p>
                </div>
                <div>
                    <a href="{{ route('room-inventory.index') }}" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left me-1"></i> Back to Rooming List & Stock
                    </a>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger fs-13">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('room-inventory.update', $inventory->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="fs-15 fw-semibold text-primary mb-0">
                            <i class="mdi mdi-hotel me-1"></i> Hotel Stock Allotment Details
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-primary">Hotel Name <span class="text-danger">*</span></label>
                                <select name="hotel_id" class="form-select form-select-sm" required>
                                    @foreach($hotels as $hotel)
                                        <option value="{{ $hotel->id }}" {{ old('hotel_id', $inventory->hotel_id) == $hotel->id ? 'selected' : '' }}>
                                            {{ $hotel->name }} @if($hotel->city || $hotel->place) ({{ $hotel->city ?? $hotel->place }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Allotment Batch Name</label>
                                <input type="text" name="batch_name" class="form-control form-control-sm"
                                    value="{{ old('batch_name', $inventory->batch_name) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Supplier / Vendor</label>
                                <select name="supplier_id" class="form-select form-select-sm">
                                    <option value="">-- Direct Hotel / No Supplier --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('supplier_id', $inventory->supplier_id) == $company->id ? 'selected' : '' }}>
                                            {{ $company->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Room Type <span class="text-danger">*</span></label>
                                <select name="room_type" class="form-select form-select-sm" required>
                                    @foreach($roomTypes as $rt)
                                        <option value="{{ $rt }}" {{ old('room_type', $inventory->room_type) == $rt ? 'selected' : '' }}>
                                            {{ $rt }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold text-primary">Total Rooms / Beds Stock <span class="text-danger">*</span></label>
                                <input type="number" name="total_rooms" class="form-control form-control-sm fw-bold border-primary"
                                    min="0" required value="{{ old('total_rooms', $inventory->total_rooms) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Room View</label>
                                <select name="room_view" class="form-select form-select-sm">
                                    @foreach(['City View', 'Haram View', 'Kaaba View', 'Partial Haram View', 'Courtyard View'] as $v)
                                        <option value="{{ $v }}" {{ old('room_view', $inventory->room_view) == $v ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Meal Plan</label>
                                <select name="meal_plan" class="form-select form-select-sm">
                                    @foreach(['Room Only', 'Bed & Breakfast', 'Half Board', 'Full Board', 'Suhoor', 'Iftar Dinner'] as $m)
                                        <option value="{{ $m }}" {{ old('meal_plan', $inventory->meal_plan) == $m ? 'selected' : '' }}>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @if(strtolower($inventory->room_type) === 'sharing')
                                <div class="col-md-4">
                                    <label class="form-label fs-13 fw-semibold text-primary">Male Beds Stock</label>
                                    <input type="number" name="male_beds" class="form-control form-control-sm text-primary fw-bold"
                                        min="0" value="{{ old('male_beds', $inventory->male_beds) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-13 fw-semibold text-danger">Female Beds Stock</label>
                                    <input type="number" name="female_beds" class="form-control form-control-sm text-danger fw-bold"
                                        min="0" value="{{ old('female_beds', $inventory->female_beds) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-13 fw-semibold">Total Beds</label>
                                    <input type="number" name="total_beds" class="form-control form-control-sm fw-bold"
                                        min="0" value="{{ old('total_beds', $inventory->total_beds) }}">
                                </div>
                            @endif

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Check-In Date</label>
                                <input type="text" name="check_in" class="form-control form-control-sm flatpickr-date"
                                    value="{{ old('check_in', $inventory->check_in ? $inventory->check_in->format('Y-m-d') : '') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Check-Out Date</label>
                                <input type="text" name="check_out" class="form-control form-control-sm flatpickr-date"
                                    value="{{ old('check_out', $inventory->check_out ? $inventory->check_out->format('Y-m-d') : '') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="active" {{ old('status', $inventory->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $inventory->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Notes</label>
                                <input type="text" name="notes" class="form-control form-control-sm"
                                    value="{{ old('notes', $inventory->notes) }}">
                            </div>
                        </div>

                        <div class="mt-4 pt-2 border-top d-flex justify-content-between align-items-center">
                            <a href="{{ route('room-inventory.index') }}" class="btn btn-secondary btn-sm px-3">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                                <i class="mdi mdi-check-circle me-1"></i> Update Room Allotment
                            </button>
                        </div>
                    </div>
                </div>
            </form>

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
