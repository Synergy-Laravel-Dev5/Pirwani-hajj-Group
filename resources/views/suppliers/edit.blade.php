@extends('layout.master')
@section('title', 'Edit Supplier')

@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Title & Breadcrumb -->
            <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                <div>
                    <h4 class="fs-18 fw-semibold mb-1 text-primary">
                        <i class="mdi mdi-pencil me-1"></i> Edit Supplier: {{ $supplier->name }}
                    </h4>
                    <p class="text-muted fs-13 mb-0">
                        Update supplier information, contact details, and financial settings.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('supplier.show', $supplier->id) }}" class="btn btn-outline-info btn-sm">
                        <i class="mdi mdi-eye me-1"></i> View Profile
                    </a>
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

            <form action="{{ route('supplier.update', $supplier->id) }}" method="POST">
                @csrf
                @method('PUT')

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
                                    value="{{ old('name', $supplier->name) }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Supplier Code</label>
                                <input type="text" name="supplier_code" class="form-control form-control-sm"
                                    value="{{ old('supplier_code', $supplier->supplier_code) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Service Type</label>
                                <select name="service_type" class="form-select form-select-sm">
                                    <option value="Hotels & Accommodation" {{ old('service_type', $supplier->service_type) == 'Hotels & Accommodation' ? 'selected' : '' }}>Hotels & Accommodation</option>
                                    <option value="Bed & Room Allotment" {{ old('service_type', $supplier->service_type) == 'Bed & Room Allotment' ? 'selected' : '' }}>Bed & Room Allotment</option>
                                    <option value="Transport & Buses" {{ old('service_type', $supplier->service_type) == 'Transport & Buses' ? 'selected' : '' }}>Transport & Buses</option>
                                    <option value="Visa & Maktab Services" {{ old('service_type', $supplier->service_type) == 'Visa & Maktab Services' ? 'selected' : '' }}>Visa & Maktab Services</option>
                                    <option value="Flight & Ticketing" {{ old('service_type', $supplier->service_type) == 'Flight & Ticketing' ? 'selected' : '' }}>Flight & Ticketing</option>
                                    <option value="Catering & Food" {{ old('service_type', $supplier->service_type) == 'Catering & Food' ? 'selected' : '' }}>Catering & Food</option>
                                    <option value="General Supplier" {{ old('service_type', $supplier->service_type) == 'General Supplier' ? 'selected' : '' }}>General Supplier</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Contact Person / Manager</label>
                                <input type="text" name="contact_person" class="form-control form-control-sm"
                                    value="{{ old('contact_person', $supplier->contact_person) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Office Phone</label>
                                <input type="text" name="phone" class="form-control form-control-sm"
                                    value="{{ old('phone', $supplier->phone) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Mobile / WhatsApp</label>
                                <input type="text" name="mobile" class="form-control form-control-sm"
                                    value="{{ old('mobile', $supplier->mobile) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-sm"
                                    value="{{ old('email', $supplier->email) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">City</label>
                                <input type="text" name="city" class="form-control form-control-sm"
                                    value="{{ old('city', $supplier->city) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Country</label>
                                <input type="text" name="country" class="form-control form-control-sm"
                                    value="{{ old('country', $supplier->country) }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fs-13 fw-semibold">Physical / Hotel Address</label>
                                <textarea name="address" class="form-control form-control-sm" rows="2">{{ old('address', $supplier->address) }}</textarea>
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
                                    value="{{ old('tax_number', $supplier->tax_number) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Commercial Reg. (CR) Number</label>
                                <input type="text" name="commercial_reg_no" class="form-control form-control-sm"
                                    value="{{ old('commercial_reg_no', $supplier->commercial_reg_no) }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fs-13 fw-semibold">Default Currency</label>
                                <select name="currency" class="form-select form-select-sm">
                                    <option value="SAR" {{ old('currency', $supplier->currency) == 'SAR' ? 'selected' : '' }}>SAR (Saudi Riyal)</option>
                                    <option value="PKR" {{ old('currency', $supplier->currency) == 'PKR' ? 'selected' : '' }}>PKR (Pak Rupee)</option>
                                    <option value="USD" {{ old('currency', $supplier->currency) == 'USD' ? 'selected' : '' }}>USD (US Dollar)</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fs-13 fw-semibold">Opening Balance</label>
                                <input type="number" step="0.01" name="opening_balance" class="form-control form-control-sm"
                                    value="{{ old('opening_balance', $supplier->opening_balance) }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fs-13 fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select form-select-sm" required>
                                    <option value="active" {{ old('status', $supplier->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $supplier->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control form-control-sm"
                                    value="{{ old('bank_name', $supplier->bank_name) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Account Title</label>
                                <input type="text" name="bank_account_title" class="form-control form-control-sm"
                                    value="{{ old('bank_account_title', $supplier->bank_account_title) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">Account Number</label>
                                <input type="text" name="bank_account_number" class="form-control form-control-sm"
                                    value="{{ old('bank_account_number', $supplier->bank_account_number) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-13 fw-semibold">IBAN</label>
                                <input type="text" name="bank_iban" class="form-control form-control-sm"
                                    value="{{ old('bank_iban', $supplier->bank_iban) }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fs-13 fw-semibold">Notes / Contract Terms</label>
                                <textarea name="notes" class="form-control form-control-sm" rows="3">{{ old('notes', $supplier->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-4">
                    <a href="{{ route('supplier.index') }}" class="btn btn-secondary btn-sm px-3">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="mdi mdi-check-circle me-1"></i> Update Supplier
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
