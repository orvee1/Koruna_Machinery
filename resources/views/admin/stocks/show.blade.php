@extends('layouts.app')

@section('title', 'Stock Details')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">🔍 Stock Details</h2>

    <!-- Stock Info Card -->
    <div class="card shadow-lg p-4 mb-5">
        <h5 class="mb-3">Product Information</h5>
        <table class="table table-bordered">
            <tr>
                <th width="30%">Product Name</th>
                <td>{{ $stock->product_name }}</td>
            </tr>
            <tr>
                <th>Supplier Name</th>
                <td>{{ $stock->supplier_name }}</td>
            </tr>
            {{-- <tr>
                <th>Branch</th>
                <td>{{ $stock->branch->name ?? '—' }}</td>
            </tr> --}}
            <tr>
                <th>Quantity</th>
                <td>{{ $stock->quantity }}</td>
            </tr>
            <tr>
                <th>Buying Price (৳)</th>
                <td>{{ number_format($stock->buying_price, 2) }}</td>
            </tr>
            <tr>
                <th>Total Amount (৳)</th>
                <td>{{ number_format($stock->total_amount, 2) }}</td>
            </tr>
            <tr>
                <th>Deposit Amount (৳)</th>
                <td>{{ number_format($stock->deposit_amount, 2) }}</td>
            </tr>
            <tr>
                <th>Due Amount (৳)</th>
                <td class="{{ $stock->due_amount > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                    {{ number_format($stock->due_amount, 2) }}
                </td>
            </tr>
            <tr>
                <th>Purchase Date</th>
                <td>{{ \Carbon\Carbon::parse($stock->purchase_date)->format('d M, Y') }}</td>
            </tr>
        </table>

        <a href="{{ route('admin.stocks.index') }}" class="btn btn-secondary mt-3">
            ← Back to List
        </a>
    </div>

    <!-- Payment Form Card -->
    <div class="card shadow-lg p-4 mb-5">
        <h5 class="mb-3">Update Supplier Payment</h5>
        <form action="{{ route('admin.stocks.updatePayment', $stock->id) }}" method="POST">
            @csrf
            <div class="row">
                <!-- Paid Amount -->
                <div class="col-md-6 mb-3">
                    <label for="paid_amount" class="form-label">Paid Amount (৳)</label>
                    <input type="number" name="paid_amount" id="paid_amount"
                        class="form-control @error('paid_amount') is-invalid @enderror"
                        value="{{ old('paid_amount') }}" min="1"
                        max="{{ $stock->due_amount }}" step="0.01" required>
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
                @forelse($stock->payments as $payment)
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
