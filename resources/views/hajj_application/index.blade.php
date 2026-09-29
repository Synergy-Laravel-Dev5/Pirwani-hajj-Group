@extends('layout.master')

@section('title', 'Hajj Applications')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="fs-18 fw-semibold m-0">Hajj Applications</h4>
                        <p class="text-muted fs-13 mb-0">Online Hajj Contracts &amp; Applications submitted by clients</p>
                    </div>
                    <div>
                        <button class="btn btn-primary btn-sm" onclick="copyPublicLink()">
                            <i data-feather="link" class="me-1"></i> Copy Public Form Link
                        </button>
                        <a href="{{ route('hajj-application.form') }}" target="_blank" class="btn btn-outline-dark btn-sm ms-1">
                            <i data-feather="external-link" class="me-1"></i> Open Form
                        </a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <!-- Filters -->
                        <form action="{{ route('hajj-application.index') }}" method="GET" class="row g-2 mb-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Search by Name, CNIC, Passport, Phone..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="package_id" class="form-select form-select-sm">
                                    <option value="">-- Filter by Package --</option>
                                    @foreach ($packages as $pkg)
                                        <option value="{{ $pkg->id }}" {{ request('package_id') == $pkg->id ? 'selected' : '' }}>
                                            {{ $pkg->package_title ?? ($pkg->name ?? $pkg->code) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">-- Status --</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-dark btn-sm flex-fill">Filter</button>
                                <a href="{{ route('hajj-application.index') }}" class="btn btn-light btn-sm">Reset</a>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th style="width: 60px;">Photo</th>
                                        <th>Applicant Name</th>
                                        <th>CNIC / NIC</th>
                                        <th>Mobile No</th>
                                        <th>Selected Package</th>
                                        <th>Status</th>
                                        <th>Submitted At</th>
                                        <th style="width: 140px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($applications as $app)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="text-center">
                                                @if ($app->photo && file_exists(public_path($app->photo)))
                                                    <img src="{{ asset($app->photo) }}" alt="Photo" class="rounded" width="40" height="45" style="object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded text-muted d-flex align-items-center justify-content-center" style="width: 40px; height: 45px; font-size: 0.65rem;">No Pic</div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $app->given_name }} {{ $app->surname }}</div>
                                                <small class="text-muted">{{ $app->gender ?? 'N/A' }} | {{ $app->dob ? $app->dob->format('d M Y') : '' }}</small>
                                            </td>
                                            <td>{{ $app->cnic_no }}</td>
                                            <td>{{ $app->mobile_no }}</td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $app->package_name ?? ($app->package->package_title ?? 'General') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($app->status == 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($app->status == 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @endif
                                            </td>
                                            <td>{{ $app->created_at->format('d M Y, h:i A') }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('hajj-application.show', $app->id) }}" class="btn btn-primary btn-sm px-2 py-1" title="View & Print Contract">
                                                    <i data-feather="eye"></i> View
                                                </a>
                                                <form action="{{ route('hajj-application.delete', $app->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this application?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm px-2 py-1" title="Delete">
                                                        <i data-feather="trash-2"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">No Hajj applications found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $applications->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function copyPublicLink() {
            const link = "{{ route('hajj-application.form') }}";
            navigator.clipboard.writeText(link).then(function() {
                alert('Public Hajj Application Link copied to clipboard:\n' + link);
            }, function(err) {
                prompt('Copy this link:', link);
            });
        }
    </script>
@endsection
