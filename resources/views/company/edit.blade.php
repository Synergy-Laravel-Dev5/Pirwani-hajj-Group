@extends('layout.master')

@section('title', 'Edit Company')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <h4 class="fs-18 fw-semibold py-3">Edit Company</h4>

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('company.update', $company->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="name" value="{{ old('name', $company->name) }}"
                                    class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active"
                                        {{ old('status', $company->status) == 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>

                                    <option value="inactive"
                                        {{ old('status', $company->status) == 'inactive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>

                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('company.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
