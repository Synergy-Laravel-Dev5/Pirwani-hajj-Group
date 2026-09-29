@extends('layout.master')
@section('title', 'Edit Airline')
@section('header-title', 'Edit Airline')
@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container">
                <div class="py-3 d-flex justify-content-between align-items-center">
                    <h4 class="fs-18 fw-semibold m-0">Edit Airline</h4>
                    <a href="{{ route('airline.index') }}" class="btn btn-secondary btn-sm">Back</a>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Airline Information</h5>
                            </div>
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $e)
                                                <li>{{ $e }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <form action="{{ route('airline.update', $airline->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Airline Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" value="{{ old('name', $airline->name) }}" class="form-control" placeholder="Enter airline name" required>
                                                @error('name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Code</label>
                                                <input type="text" name="code" value="{{ old('code', $airline->code) }}" class="form-control" placeholder="Enter airline code">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">IATA Code</label>
                                                <input type="text" name="iata_code" value="{{ old('iata_code', $airline->iata_code) }}" class="form-control" placeholder="e.g. PK">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">ICAO Code</label>
                                                <input type="text" name="icao_code" value="{{ old('icao_code', $airline->icao_code) }}" class="form-control" placeholder="e.g. PIA">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Country</label>
                                                <input type="text" name="country" value="{{ old('country', $airline->country) }}" class="form-control" placeholder="Enter country name">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Call Sign</label>
                                                <input type="text" name="call_sign" value="{{ old('call_sign', $airline->call_sign) }}" class="form-control" placeholder="e.g. PAKISTAN">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">FF Number</label>
                                                <input type="text" name="ffnumber" value="{{ old('ffnumber', $airline->ffnumber) }}" class="form-control" placeholder="Frequent Flyer Number">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Logo</label>
                                                <input type="file" name="logo" class="form-control mb-2">
                                                @if($airline->logo)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <img src="{{ asset($airline->logo) }}" alt="Current Logo" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                                        <small class="text-muted">Current Logo</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                                <select name="status" class="form-select" required>
                                                    <option value="active" {{ old('status', $airline->status) == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ old('status', $airline->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                                @error('status')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-primary px-4">Update Airline</button>
                                        </div>
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
