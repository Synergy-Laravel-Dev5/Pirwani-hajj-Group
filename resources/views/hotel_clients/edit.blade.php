@extends('layout.master')
@section('title', 'Edit Hotel Client')

@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                <div>
                    <h4 class="fs-18 fw-semibold mb-1 text-primary">
                        <i class="mdi mdi-pencil me-1"></i> Edit Hotel Client
                    </h4>
                    <p class="text-muted fs-13 mb-0">Update hotel client profile information.</p>
                </div>
                <a href="{{ route('hotel-clients.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="mdi mdi-arrow-left me-1"></i> Back to Clients
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white py-2 px-3">
                            <h5 class="fs-14 fw-bold text-white mb-0">Edit Client: {{ $client->name }}</h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('hotel-clients.update', $client->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label fs-13 fw-bold text-uppercase text-dark mb-1">Company Name</label>
                                    <input type="text" name="company_name" class="form-control" placeholder="e.g. AL MOAZZIN, AL-FAJR TRAVELS" value="{{ old('company_name', $client->company_name) }}">
                                    <small class="text-muted fs-11">Company / Agency name that appears on invoice header.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fs-13 fw-bold text-uppercase text-dark mb-1">Client Name / Contact Person <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required placeholder="e.g. MUHAMMAD SALEEM" value="{{ old('name', $client->name) }}">
                                    <small class="text-muted fs-11">Person's name that appears under Attn.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fs-13 fw-bold text-uppercase text-dark mb-1">Contact Number</label>
                                    <input type="text" name="contact" class="form-control" placeholder="e.g. 0300-1234567 / +966 50 123 4567" value="{{ old('contact', $client->contact) }}">
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fs-13 fw-bold text-uppercase text-dark mb-1">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="active" {{ old('status', $client->status) === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $client->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fs-13 fw-bold text-uppercase text-dark mb-1">Notes / Remarks</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes...">{{ old('notes', $client->notes) }}</textarea>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('hotel-clients.index') }}" class="btn btn-secondary px-3">Cancel</a>
                                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                                        <i class="mdi mdi-check me-1"></i> Update Client
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
