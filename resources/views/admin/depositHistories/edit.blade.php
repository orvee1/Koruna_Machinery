@extends('layouts.app')

@section('title', 'Edit Deposit History')

@section('content')
<div class="container">
    <h1>Edit Deposit History</h1>

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

    <!-- Form for editing the deposit history -->
    <form action="{{ route('admin.depositHistories.update', $depositHistory->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="investor_id">Investor</label>
            <select name="investor_id" id="investor_id" class="form-control @error('investor_id') is-invalid @enderror">
                <option value="">Select Investor</option>
                @foreach($investors as $investor)
                    <option value="{{ $investor->id }}" @if($depositHistory->investor_id == $investor->id) selected @endif>{{ $investor->name }}</option>
                @endforeach
            </select>
            @error('investor_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $depositHistory->amount) }}" required min="1">
            @error('amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="payment_method">Payment Method</label>
            <select name="payment_method" id="payment_method" class="form-control @error('payment_method') is-invalid @enderror">
                <option value="cash" @if($depositHistory->payment_method == 'cash') selected @endif>Cash</option>
                <option value="bank" @if($depositHistory->payment_method == 'bank') selected @endif>Bank</option>
                <option value="card" @if($depositHistory->payment_method == 'card') selected @endif>Card</option>
            </select>
            @error('payment_method')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="payment_date">Payment Date</label>
            <input type="date" id="payment_date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', $depositHistory->payment_date) }}" required>
            @error('payment_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success mt-3">Update Deposit History</button>
    </form>
</div>
@endsection
