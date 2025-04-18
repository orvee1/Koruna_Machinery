@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="container">
    <div class="card shadow-lg p-4 mb-4">
        <h1 class="mb-4 text-center">Product Details</h1>

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

        <!-- Product Details Table -->
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <th>Name</th>
                    <td>{{ $product->name }}</td>
                </tr>
                <tr>
                    <th>Buying Price</th>
                    <td>{{ $product->buying_price }}</td>
                </tr>
                <tr>
                    <th>Selling Price</th>
                    <td>{{ $product->selling_price }}</td>
                </tr>
                <tr>
                    <th>Stock Quantity</th>
                    <td>{{ $product->stock_quantity }}</td>
                </tr>
                <tr>
                    <th>Total Purchase Amount</th>
                    <td>{{ $product->total_purchase_amount }}</td>
                </tr>
                <tr>
                    <th>Paid Amount</th>
                    <td>{{ $product->paid_amount }}</td>
                </tr>
                <tr>
                    <th>Remaining Balance</th>
                    <td>{{ $product->remainingBalance() }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Form to update payment -->
    <div class="card shadow-lg p-4 mb-4">
        <h3 class="mb-3">Update Payment</h3>
        <form action="{{ route('admin.products.updatePayment', $product->id) }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label for="paid_amount" class="form-label">Paid Amount</label>
                <input type="number" id="paid_amount" name="paid_amount" class="form-control @error('paid_amount') is-invalid @enderror" value="{{ old('paid_amount') }}" required min="1">
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
                @foreach($product->payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date }}</td>
                        <td>{{ $payment->paid_amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Back Button -->
    <a href="{{ route('admin.products.index') }}" class="btn btn-primary">Back to Products</a>
</div>

@endsection
