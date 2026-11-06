@extends('layout.mobile-master')

@section('content')
    <div class="pt-3 pb-2 d-flex align-items-sm-center justify-content-between flex-column flex-sm-row">
        <div>
            <h4 class="fs-18 fw-bold text-dark m-0">Create Qurbani Batch</h4>
            <p class="text-muted fs-13 mb-0">Set up a scheduled date, start and end time window for Hajjis Qurbani announcement.</p>
        </div>
        <div class="mt-2 mt-sm-0">
            <a href="{{ route('mobile-ops.qurbani.index') }}" class="btn btn-sm btn-secondary rounded-pill px-3">
                <i class="mdi mdi-arrow-left me-1"></i> Back to Batches
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-4">
            <form action="{{ route('mobile-ops.qurbani.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Batch Name <span class="text-danger">*</span></label>
                        <input type="text" name="batch_name" class="form-control" placeholder="e.g. Batch 1 - Day of Nahr Morning" value="{{ old('batch_name') }}" required>
                        <div class="form-text">A descriptive label identifying this group's slot.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Qurbani Date <span class="text-danger">*</span></label>
                        <input type="date" name="qurbani_date" class="form-control" value="{{ old('qurbani_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Start Time (Announcement fires at this time) <span class="text-danger">*</span></label>
                        <input type="time" name="start_time" class="form-control" value="{{ old('start_time', '09:00') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">End Time (Same Day) <span class="text-danger">*</span></label>
                        <input type="time" name="end_time" class="form-control" value="{{ old('end_time', '11:00') }}" required>
                    </div>

                    <div class="col-12">
                        <hr class="my-2">
                        <h5 class="fs-14 fw-bold text-primary mb-2">
                            <i class="mdi mdi-cellphone-message me-1"></i> Mobile App Announcement Details
                        </h5>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Announcement Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Your Qurbani is Ready / In Progress" value="{{ old('title') }}">
                        <div class="form-text">Title shown on Hajji's mobile screen or push banner.</div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Announcement Message / Instructions</label>
                        <textarea name="message" class="form-control" rows="3" placeholder="e.g. Dear Pilgrim, your Qurbani sacrifice window has started. Please proceed accordingly.">{{ old('message') }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Internal Notes (Admin only)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Slaughterhouse location, coordinator phone, etc.">{{ old('notes') }}</textarea>
                    </div>

                    <div class="col-12 mt-4 text-end">
                        <a href="{{ route('mobile-ops.qurbani.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="mdi mdi-check me-1"></i> Create Batch & Assign Hajjis
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
