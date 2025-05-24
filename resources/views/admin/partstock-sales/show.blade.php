@extends('layouts.app')

@section('title', 'View Part Stock Sale')

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Part Stock Sale Details</h2>
        <a href="{{ route('admin.partstock-sales.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    {{-- Sale Summary --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-receipt"></i> Sale Summary</h5>
        </div>
        <div class="card-body">
            <div class="row gx-3 gy-2">
                <div class="col-md-4">
                    <strong>Customer:</strong><br>
                    {{ $partstockSale->customer->name ?? 'N/A' }}
                </div>
                <div class="col-md-4">
                    <strong>Product:</strong><br>
                    {{ $partstockSale->partStock->product_name ?? 'N/A' }}
                </div>
                <div class="col-md-4">
                    <strong>Seller:</strong><br>
                    {{ $partstockSale->seller->name ?? 'N/A' }}
                </div>

                <div class="col-md-4">
                    <strong>Unit Price:</strong><br>
                    ৳ {{ number_format($partstockSale->unit_price, 2) }}
                </div>
                <div class="col-md-4">
                    <strong>Quantity:</strong><br>
                    {{ $partstockSale->quantity }}
                </div>
                <div class="col-md-4">
                    <strong>Sale Date:</strong><br>
                    {{ $partstockSale->created_at->format('d M, Y') }}
                </div>

                <div class="col-md-4">
                    <strong>Total Amount:</strong><br>
                    ৳ {{ number_format($partstockSale->total_amount, 2) }}
                </div>
                <div class="col-md-4">
                    <strong>Paid Amount:</strong><br>
                    ৳ {{ number_format($partstockSale->paid_amount, 2) }}
                </div>
                <div class="col-md-4">
                    <strong>Due Amount:</strong><br>
                    <span class="{{ $partstockSale->due_amount > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                        ৳ {{ number_format($partstockSale->due_amount, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Form --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-credit-card"></i> Add Payment</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.partstockSales.updatePayment', $partstockSale->id) }}" method="POST">
                @csrf
                <div class="row gx-3">
                    <div class="col-md-6 mb-3">
                        <label for="paid_amount" class="form-label">Paid Amount (৳)</label>
                        <input type="number"
                               name="paid_amount"
                               id="paid_amount"
                               class="form-control @error('paid_amount') is-invalid @enderror"
                               value="{{ old('paid_amount') }}"
                               min="0.01"
                               max="{{ $partstockSale->due_amount }}"
                               step="0.01"
                               required>
                        @error('paid_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="payment_date" class="form-label">Payment Date</label>
                        <input type="date"
                               name="payment_date"
                               id="payment_date"
                               class="form-control @error('payment_date') is-invalid @enderror"
                               value="{{ old('payment_date', now()->toDateString()) }}"
                               required>
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-lg"></i> Add Payment
                </button>
            </form>
        </div>
    </div>

    {{-- Payment History --}}
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Payment History</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Paid Amount (৳)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($partstockSale->payments as $payment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M, Y') }}</td>
                            <td>৳ {{ number_format($payment->paid_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-3">
                                No payments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
