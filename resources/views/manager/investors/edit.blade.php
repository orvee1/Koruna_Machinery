@extends('layouts.app')

@section('title', 'Edit Investor')

@section('content')
<div class="container">
    <h1>Edit Investor</h1>

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

    <form action="{{ route('manager.investors.update', $investor->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Investor Name</label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $investor->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="total_investment">Total Investment</label>
            <input type="number" id="total_investment" name="total_investment" class="form-control @error('total_investment') is-invalid @enderror" value="{{ old('total_investment', $investor->total_investment) }}" required>
            @error('total_investment')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="balance">Balance</label>
            <input type="number" id="balance" name="balance" class="form-control @error('balance') is-invalid @enderror" value="{{ old('balance', $investor->balance) }}" required>
            @error('balance')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="active" {{ old('status', $investor->status) == 'active' ? 'selected' : '' }}>Active</option>
                <option value="closed" {{ old('status', $investor->status) == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success mt-3">Update Investor</button>
    </form>
</div>
@endsection
