@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="container">
    <h1>Product Details</h1>

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
    <table class="table table-bordered">
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
        {{-- <tr>
            <th>Branch</th>
            <td>{{ $product->branch->name ?? 'No branch assigned' }}</td>
        </tr> --}}
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
    </table>

    <!-- Form to adjust stock -->
    {{-- <h3>Adjust Stock</h3>
    <form action="{{ route('admin.products.adjustStock', $product->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="quantity">Quantity to Adjust</label>
            <input type="number" id="quantity" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" required min="1">
            @error('quantity')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary mt-3">Adjust Stock</button>
    </form> --}}

    <!-- Form to update payment -->
    <h3>Update Payment</h3>
    <form action="{{ route('admin.products.updatePayment', $product->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="payment_amount">Payment Amount</label>
            <input type="number" id="payment_amount" name="payment_amount" class="form-control @error('payment_amount') is-invalid @enderror" value="{{ old('payment_amount') }}" required min="1">
            @error('payment_amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success mt-3">Update Payment</button>
    </form>
</div>
@endsection
