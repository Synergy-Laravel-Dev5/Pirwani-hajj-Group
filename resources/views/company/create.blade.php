@extends('layout.master')

@section('title', 'Add Company')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <h4 class="fs-18 fw-semibold py-3">Add Company</h4>

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('company.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>

                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="{{ route('company.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
