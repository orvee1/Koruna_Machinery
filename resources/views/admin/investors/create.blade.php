@extends('layouts.app')

@section('title', 'Add New Investor')

@section('content')
<div class="container">
    <h1>Add New Investor</h1>

    <!-- Display success or error messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.investors.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Investor Name</label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="total_investment">Total Investment</label>
            <input type="number" id="total_investment" name="total_investment" class="form-control @error('total_investment') is-invalid @enderror" value="{{ old('total_investment') }}" required>
            @error('total_investment')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="balance">Balance</label>
            <input type="number" id="balance" name="balance" class="form-control @error('balance') is-invalid @enderror" value="{{ old('balance') }}" required>
            @error('balance')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success mt-3">Save Investor</button>
    </form>
</div>
@endsection
