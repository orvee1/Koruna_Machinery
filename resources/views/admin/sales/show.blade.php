@extends('layouts.app')
@section('title', 'Bill #' . $bill->id)

@section('content')
<div class="container">
    <h3 class="mb-3">🧾 Bill #{{ $bill->id }}</h3>

    {{-- Customer and Bill Info --}}
    <p><strong>Customer:</strong> {{ $bill->customer->name ?? 'N/A' }}</p>
    <p><strong>Phone:</strong> {{ $bill->customer->phone ?? 'N/A' }}</p>
    <p><strong>District:</strong> {{ $bill->customer->district ?? 'N/A' }}</p>
    <p><strong>Seller:</strong> {{ $bill->seller->name ?? 'N/A' }}</p>
    <p><strong>Date:</strong> {{ $bill->created_at->format('Y-m-d') }}</p>

    <hr>
    {{-- Unified Sales List --}}
    <h5>🛒 All Sold Items</h5>
    <ul class="list-group mb-4">
        @foreach($bill->productSales as $sale)
            <li class="list-group-item">
                {{ $sale->stock->product_name ?? 'N/A' }} — Qty: {{ $sale->quantity }} × {{ number_format($sale->unit_price, 2) }} = 
                ৳{{ number_format($sale->total_amount, 2) }}
            </li>
        @endforeach

        @foreach($bill->partStockSales as $sale)
            <li class="list-group-item">
                {{ $sale->partStock->product_name ?? 'N/A' }} — Qty: {{ $sale->quantity }} × {{ number_format($sale->unit_price, 2) }} = 
                ৳{{ number_format($sale->total_amount, 2) }}
            </li>
        @endforeach
    </ul>

    {{-- Bill Summary --}}
    <h5>Total Summary</h5>
    <p><strong>Total Amount:</strong> ৳{{ number_format($bill->total_amount, 2) }}</p>
    <p><strong>Paid Amount:</strong> ৳{{ number_format($bill->paid_amount, 2) }}</p>
    <p class="{{ $bill->due_amount > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}"><strong>Due Amount:</strong> ৳{{ number_format($bill->due_amount, 2) }}</p>

    <hr>
     {{-- New Payment Form --}}
      <div class="card shadow-lg p-4 mb-5">
        <h5 class="mb-3">Update Customer Payment</h5>
        <form action="{{ route('admin.sales.updatePayment', $bill->id) }}" method="POST">
            @csrf
            <div class="row">
                <!-- Paid Amount -->
                <div class="col-md-6 mb-3">
                    <label for="paid_amount" class="form-label">Paid Amount (৳)</label>
                    <input type="number" name="paid_amount" id="paid_amount"
                        class="form-control @error('paid_amount') is-invalid @enderror"
                        value="{{ old('paid_amount') }}" min="1"
                        max="{{ $bill->due_amount }}" step="0.01" required>
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

    {{-- Payment History --}}
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
                @forelse($bill->payments as $payment)
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
