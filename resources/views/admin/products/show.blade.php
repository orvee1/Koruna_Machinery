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
                    <th style="width: 25%;">Product Name</th>
                    <td>{{ $stock->product_name }}</td>
                </tr>
                <tr>
                    <th>Supplier Name</th>
                    <td>{{ $stock->supplier_name }}</td>
                </tr>
                <tr>
                    <th>Buying Price (৳)</th>
                    <td>{{ number_format($stock->buying_price, 2) }}</td>
                </tr>
                <tr>
                    <th>Unit Price (৳)</th>
                    <td>{{ number_format($stock->unit_price, 2) }}</td>
                </tr>
                <tr>
                    <th>Stock Quantity</th>
                    <td>{{ $stock->quantity }}</td>
                </tr>
                <tr>
                    <th>Branch</th>
                    <td>{{ $stock->branch->name ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Last Purchase Date</th>
                    <td>{{ $stock->purchase_date ? \Illuminate\Support\Carbon::parse($stock->purchase_date)->format('d M, Y') : '—' }}</td>
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
                    <th>Total Profit (৳)</th>
                    <td>{{ number_format($stock->total_profit, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between">
        <a href="{{ route('admin.products.index') }}" class="btn btn-primary">← Back to Products</a>
        <div>
            <a href="{{ route('admin.products.edit', $stock->id) }}" class="btn btn-warning me-2">✏️ Edit Product</a>
        </div>
    </div>
</div>
@endsection
