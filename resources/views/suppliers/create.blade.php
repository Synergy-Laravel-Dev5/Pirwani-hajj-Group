@extends('layout.master')
@section('title', 'Add New Supplier')

@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Title & Breadcrumb -->
            <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                <div>
                    <h4 class="fs-18 fw-semibold mb-1 text-primary">
                        <i class="mdi mdi-plus-circle me-1"></i> Add New Supplier / Vendor
                    </h4>
                    <p class="text-muted fs-13 mb-0">
                        Register hotel accommodation providers, bed allotment vendors, transport companies, and Maktab suppliers.
                    </p>
                </div>
                <div>
                    <a href="{{ route('supplier.index') }}" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left me-1"></i> Back to Suppliers
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

            <form action="{{ route('supplier.store') }}" method="POST">
                @csrf

                <!-- Basic Information Card -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="fs-15 fw-semibold text-primary mb-0">
                            <i class="mdi mdi-office-building me-1"></i> 1. Supplier / Vendor Profile
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold">Supplier / Vendor Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm"
                                    placeholder="e.g. Swissôtel Vendor / Elaf Group / Rawabi Hotels"
                                    value="{{ old('name') }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Supplier Code</label>
                                <input type="text" name="supplier_code" class="form-control form-control-sm"
                                    placeholder="e.g. SUP-006" value="{{ old('supplier_code', $nextCode) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Service Type</label>
                                <select name="service_type" class="form-select form-select-sm">
                                    <option value="Hotels & Accommodation" {{ old('service_type') == 'Hotels & Accommodation' ? 'selected' : '' }}>Hotels & Accommodation</option>
                                    <option value="Bed & Room Allotment" {{ old('service_type') == 'Bed & Room Allotment' ? 'selected' : '' }}>Bed & Room Allotment</option>
                                    <option value="Transport & Buses" {{ old('service_type') == 'Transport & Buses' ? 'selected' : '' }}>Transport & Buses</option>
                                    <option value="Visa & Maktab Services" {{ old('service_type') == 'Visa & Maktab Services' ? 'selected' : '' }}>Visa & Maktab Services</option>
                                    <option value="Flight & Ticketing" {{ old('service_type') == 'Flight & Ticketing' ? 'selected' : '' }}>Flight & Ticketing</option>
                                    <option value="Catering & Food" {{ old('service_type') == 'Catering & Food' ? 'selected' : '' }}>Catering & Food</option>
                                    <option value="General Supplier" {{ old('service_type') == 'General Supplier' ? 'selected' : '' }}>General Supplier</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Contact Person / Manager</label>
                                <input type="text" name="contact_person" class="form-control form-control-sm"
                                    placeholder="e.g. Sheikh Tariq / Sales Director" value="{{ old('contact_person') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Office Phone</label>
                                <input type="text" name="phone" class="form-control form-control-sm"
                                    placeholder="e.g. +966 12 555 1000" value="{{ old('phone') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Mobile / WhatsApp</label>
                                <input type="text" name="mobile" class="form-control form-control-sm"
                                    placeholder="e.g. +966 50 123 4567" value="{{ old('mobile') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-sm"
                                    placeholder="e.g. reservations@hotel-supplier.com" value="{{ old('email') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">City</label>
                                <input type="text" name="city" class="form-control form-control-sm"
                                    placeholder="e.g. Makkah / Madinah / Jeddah / Karachi"
                                    value="{{ old('city', 'Makkah') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Country</label>
                                <input type="text" name="country" class="form-control form-control-sm"
                                    placeholder="e.g. Saudi Arabia" value="{{ old('country', 'Saudi Arabia') }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fs-13 fw-semibold">Physical / Hotel Address</label>
                                <textarea name="address" class="form-control form-control-sm" rows="2"
                                    placeholder="e.g. Abraj Al Bait Complex / Ajyad / Central Area...">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial & Tax Details -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="fs-15 fw-semibold text-primary mb-0">
                            <i class="mdi mdi-cash-register me-1"></i> 2. Financial, Tax & Banking Information
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">VAT / Tax Number</label>
                                <input type="text" name="tax_number" class="form-control form-control-sm"
                                    placeholder="e.g. 300123456700003" value="{{ old('tax_number') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Commercial Reg. (CR) Number</label>
                                <input type="text" name="commercial_reg_no" class="form-control form-control-sm"
                                    placeholder="e.g. 4031098765" value="{{ old('commercial_reg_no') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fs-13 fw-semibold">Default Currency</label>
                                <select name="currency" class="form-select form-select-sm">
                                    <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>SAR (Saudi Riyal)</option>
                                    <option value="PKR" {{ old('currency') == 'PKR' ? 'selected' : '' }}>PKR (Pak Rupee)</option>
                                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD (US Dollar)</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fs-13 fw-semibold">Opening Balance</label>
                                <input type="number" step="0.01" name="opening_balance" class="form-control form-control-sm"
                                    placeholder="0.00" value="{{ old('opening_balance', '0.00') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fs-13 fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select form-select-sm" required>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control form-control-sm"
                                    placeholder="e.g. Al Rajhi Bank / SNB AlAhli" value="{{ old('bank_name') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Account Title</label>
                                <input type="text" name="bank_account_title" class="form-control form-control-sm"
                                    placeholder="e.g. Swissôtel Vendor Account" value="{{ old('bank_account_title') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Account Number</label>
                                <input type="text" name="bank_account_number" class="form-control form-control-sm"
                                    placeholder="Account #" value="{{ old('bank_account_number') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">IBAN</label>
                                <input type="text" name="bank_iban" class="form-control form-control-sm"
                                    placeholder="SA00 0000 0000 0000" value="{{ old('bank_iban') }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fs-13 fw-semibold">Notes / Contract Terms</label>
                                <textarea name="notes" class="form-control form-control-sm" rows="3"
                                    placeholder="Additional supplier terms, payment agreements, hotel contact notes...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-4">
                    <a href="{{ route('supplier.index') }}" class="btn btn-secondary btn-sm px-3">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="mdi mdi-check-circle me-1"></i> Save Supplier
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
