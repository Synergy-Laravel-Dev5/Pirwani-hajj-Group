@extends('layout.mobile-master')

@section('content')
    <div class="py-3 d-flex align-items-sm-center justify-content-between flex-column flex-sm-row gap-2">
        <div>
            <h4 class="fs-18 fw-bold text-dark m-0">Edit Qurbani Batch</h4>
            <p class="text-muted fs-13 mb-0">Modify batch schedule or announcement message.</p>
        </div>
        <div>
            <a href="{{ route('mobile-ops.qurbani.show', $batch->id) }}" class="btn btn-sm btn-secondary rounded-pill px-3">
                <i class="mdi mdi-arrow-left me-1"></i> Back to Batch Details
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
            <form action="{{ route('mobile-ops.qurbani.update', $batch->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Batch Name <span class="text-danger">*</span></label>
                        <input type="text" name="batch_name" class="form-control" value="{{ old('batch_name', $batch->batch_name) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Qurbani Date <span class="text-danger">*</span></label>
                        <input type="date" name="qurbani_date" class="form-control" value="{{ old('qurbani_date', \Carbon\Carbon::parse($batch->qurbani_date)->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Start Time <span class="text-danger">*</span></label>
                        <input type="time" name="start_time" class="form-control" value="{{ old('start_time', substr((string)$batch->start_time, 0, 5)) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">End Time (Same Day) <span class="text-danger">*</span></label>
                        <input type="time" name="end_time" class="form-control" value="{{ old('end_time', substr((string)$batch->end_time, 0, 5)) }}" required>
                    </div>

                    <div class="col-12">
                        <hr class="my-2">
                        <h5 class="fs-14 fw-bold text-primary mb-2">
                            <i class="mdi mdi-cellphone-message me-1"></i> Mobile App Announcement Details
                        </h5>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Announcement Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $batch->title) }}">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Announcement Message / Instructions</label>
                        <textarea name="message" class="form-control" rows="3">{{ old('message', $batch->message) }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Internal Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $batch->notes) }}</textarea>
                    </div>

                    <div class="col-12 mt-4 text-end">
                        <a href="{{ route('mobile-ops.qurbani.show', $batch->id) }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="mdi mdi-check me-1"></i> Update Batch
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
