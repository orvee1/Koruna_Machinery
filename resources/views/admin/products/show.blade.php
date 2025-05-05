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
                    <td>{{ $product->paidAmount() }}</td>
                </tr>
                <tr>
                    <th>Remaining Balance</th>
                    <td>{{ $product->remainingBalance() }}</td>
                </tr>
            </tbody>
        </table>
    </div>


    <!-- Back Button -->
    <a href="{{ route('admin.products.index') }}" class="btn btn-primary">Back to Products</a>
</div>

@endsection
