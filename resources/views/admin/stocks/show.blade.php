@extends('layouts.app')

@section('title', 'Stock Details')

@section('content')
<div class="container">
    <h1 class="mb-4">Stock Details</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Stock Info -->
    <div class="card mb-4">
        <div class="card-body">
            <table class="table table-bordered mb-0">
                <tr>
                    <th style="width:30%;">Product Name</th>
                    <td>{{ $stock->product_name }}</td>
                </tr>
                <tr>
                    <th>Supplier Name</th>
                    <td>{{ $stock->supplier_name }}</td>
                </tr>
                <tr>
                    <th>Quantity</th>
                    <td>{{ $stock->quantity }}</td>
                </tr>
                <tr>
                    <th>Total Amount (৳)</th>
                    <td>{{ number_format($stock->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <th>Due Amount (৳)</th>
                    <td>{{ number_format($stock->due_amount, 2) }}</td>
                </tr>
                <tr>
                    <th>Purchase Date</th>
                    <td>{{ \Illuminate\Support\Carbon::parse($stock->purchase_date)->format('d M, Y') }}</td>
                </tr>
                <tr>
                    <th>Branch</th>
                    <td>{{ session('active_branch_name', '—') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Update Payment -->
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="mb-3">Update Payment</h3>
            <form action="{{ route('admin.stocks.updatePayment', $stock) }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="paid_amount" class="form-label">Paid Amount (৳)</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            id="paid_amount" 
                            name="paid_amount"
                            class="form-control @error('paid_amount') is-invalid @enderror"
                            value="{{ old('paid_amount') }}"
                            required
                        >
                        @error('paid_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="payment_date" class="form-label">Payment Date</label>
                        <input 
                            type="date" 
                            id="payment_date" 
                            name="payment_date"
                            class="form-control @error('payment_date') is-invalid @enderror"
                            value="{{ old('payment_date', now()->toDateString()) }}"
                            required
                        >
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-success mt-3">Record Payment</button>
            </form>
        </div>
    </div>

    <!-- Payment History -->
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="mb-3">Payment History</h3>
            <table class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Paid Amount (৳)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stock->payments as $payment)
                        <tr>
                            <td>{{ \Illuminate\Support\Carbon::parse($payment->payment_date)->format('d M, Y') }}</td>
                            <td>{{ number_format($payment->paid_amount, 2) }} ৳</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">No payment records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('admin.stocks.index') }}" class="btn btn-secondary">← Back to List</a>
</div>
@endsection
