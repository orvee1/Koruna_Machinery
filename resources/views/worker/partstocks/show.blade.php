@extends('layouts.app')

@section('title', 'Part Stock Details')

@section('content')
<div class="container">
    <h1>Part Stock Details</h1>

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

    <!-- Part Stock Details Table -->
    <table class="table table-bordered">
        <tr>
            <th>Product Name</th>
            <td>{{ $partStock->product_name }}</td>
        </tr>
        <tr>
            <th>Supplier Name</th>
            <td>{{ $partStock->supplier_name }}</td>
        </tr>
        <tr>
            <th>Buy Value</th>
            <td>{{ $partStock->buy_value }}</td>
        </tr>
        <tr>
            <th>Sell Value</th>
            <td>{{ $partStock->sell_value }}</td>
        </tr>
        <tr>
            <th>Quantity</th>
            <td>{{ $partStock->quantity }}</td>
        </tr>
        <tr>
            <th>Total Amount</th>
            <td>{{ $partStock->amount }}</td>
        </tr>
        <tr>
            <th>Paid Amount</th>
            <td>{{ $partStock->paidAmount() }}</td>
        </tr>
        <tr>
            <th>Due Amount</th>
            <td>{{ $partStock->remainingBalance() }}</td>
        </tr>
        <tr>
            <th>Total Profit</th>
            <td>{{ $partStock->total_profit }}</td>
        </tr>   
    </table>

    <div class="card shadow-lg p-4 mb-4">
        <h3 class="mb-3">Update Payment</h3>
        <form action="{{ route('worker.partstocks.updatePayment', $partStock->id) }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label for="paid_amount" class="form-label">Paid Amount</label>
                <input type="number" id="paid_amount" name="paid_amount" max="{{ $partStock->remainingBalance() }}" class="form-control @error('paid_amount') is-invalid @enderror" value="{{ old('paid_amount') }}" required min="1">
                @error('paid_amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="payment_date" class="form-label">Payment Date</label>
                <input type="date" id="payment_date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date') }}" required>
                @error('payment_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success mt-3">Update Payment</button>
        </form>
    </div>

    <!-- Payment History Section -->
    <div class="card shadow-lg p-4 mb-4">
        <h3 class="mb-3">Payment History</h3>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Paid Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($partStock->payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date }}</td>
                        <td>{{ $payment->paid_amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Back Button -->
    <a href="{{ route('worker.partstocks.index') }}" class="btn btn-primary">Back to Part Stocks</a>
</div>
@endsection
