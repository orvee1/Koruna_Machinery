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
            <td>{{ $partStock->buying_price }}</td>
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
            <td>{{ $partStock->total_amount }}</td>
        </tr>
        <tr>
            <th>Paid Amount</th>
            <td>{{ $partStock->deposit_amount }}</td>
        </tr>
        <tr>
            <th>Due Amount</th>
            <td class="{{ $partStock->due_amount > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                    {{ number_format($partStock->due_amount, 2) }}
                </td>
        </tr>
        <tr>
            <th>Total Profit</th>
            <td>{{ $partStock->total_profit }}</td>
        </tr>   
    </table>

     <a href="{{ route('admin.partstocks.index') }}" class="btn btn-secondary mt-3">
            ← Back to List
     </a>
     
     <div class="card shadow-lg p-4 mb-5">
        <h5 class="mb-3">Update Supplier Payment</h5>
        <form action="{{ route('admin.partstocks.updatePayment', $partStock->id) }}" method="POST">
            @csrf
            <div class="row">
                <!-- Paid Amount -->
                <div class="col-md-6 mb-3">
                    <label for="paid_amount" class="form-label">Paid Amount (৳)</label>
                    <input type="number" name="paid_amount" id="paid_amount"
                        class="form-control @error('paid_amount') is-invalid @enderror"
                        value="{{ old('paid_amount') }}" min="1"
                        max="{{ $partStock->due_amount }}" step="0.01" required>
                    @error('paid_amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Payment Date -->
                <div class="col-md-6 mb-3">
                    <label for="payment_date" class="form-label">Payment Date</label>
                    <input type="date" name="payment_date" id="payment_date"
                        class="form-control @error('payment_date') is-invalid @enderror"
                        value="{{ old('payment_date', date('Y-m-d')) }}" required>
                    @error('payment_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-success">➕ Add Payment</button>
        </form>
    </div>

    <!-- Payment History -->
    <div class="card shadow-lg p-4 mb-5">
        <h5 class="mb-3">Supplier Payment History</h5>
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Paid Amount (৳)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($partStock->payments as $payment)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M, Y') }}</td>
                        <td>{{ number_format($payment->paid_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted">No payments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
