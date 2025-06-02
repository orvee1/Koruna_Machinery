@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="container">
    <h1>Edit Customer</h1>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Edit Form --}}
    <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Name</label>
                <input
                    type="text"
                    class="form-control @error('name') is-invalid @enderror"
                    id="name"
                    name="name"
                    value="{{ old('name', $customer->name) }}"
                    required
                >
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input
                    type="text"
                    class="form-control @error('phone') is-invalid @enderror"
                    id="phone"
                    name="phone"
                    value="{{ old('phone', $customer->phone) }}"
                    required
                >
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="district" class="form-label">District</label>
                <input
                    type="text"
                    class="form-control @error('district') is-invalid @enderror"
                    id="district"
                    name="district"
                    value="{{ old('district', $customer->district) }}"
                >
                @error('district') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="branch_id" class="form-label">Branch</label>
                <input
                    type="text"
                    class="form-control"
                    id="branch_name"
                    value="{{ $branch->name }}"
                    disabled
                >
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Update Customer</button>
        </div>
    </form>
</div>
@endsection
<script>
    document.querySelector('form').addEventListener('submit', function() {
        alert('Form Submitted');
    });
</script>

