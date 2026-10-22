@extends('layout.mobile-master')

@section('content')
    <div class="row pt-3 pb-2">
        <div class="col-12">
            <h4 class="fs-18 fw-bold text-dark m-0">Mobile Operations Dashboard</h4>
            <p class="text-muted fs-13">Overview of mobile application activities and guest inquiries.</p>
        </div>
    </div>

    <div class="row g-3">
        <!-- Inquiries Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fs-13 fw-semibold">Pending Guest Inquiries</span>
                    </div>
                    <h2 class="fw-bold mb-2 text-dark">{{ \App\Models\PackageInquiry::where('status', 'pending')->count() }}
                    </h2>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fs-12">Total: {{ \App\Models\PackageInquiry::count() }} inquiries</span>
                        <a href="{{ route('mobile-ops.inquiries.index') }}" class="btn btn-sm btn-outline-primary"
                            style="border-radius: 20px;">
                            Manage Inquiries &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
