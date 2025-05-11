@extends('layouts.app')

@section('title', 'Stock Details')

@section('content')
<div class="container">
    <h1 class="mb-4">🔎 Stock Details</h1>

    <table class="table table-bordered">
        <tr><th>Product Name</th><td>{{ $stock->product_name }}</td></tr>
        <tr><th>Supplier Name</th><td>{{ $stock->supplier_name }}</td></tr>
        <tr><th>Quantity</th><td>{{ $stock->quantity }}</td></tr>
        <tr><th>Unit Price (৳)</th><td>{{ number_format($stock->buying_price, 2) }}</td></tr>
        <tr><th>Total Amount (৳)</th><td>{{ number_format($stock->total_amount, 2) }}</td></tr>
        <tr><th>Due Amount (৳)</th><td>{{ number_format($stock->due_amount, 2) }}</td></tr>
        <tr><th>Purchase Date</th><td>{{ \Illuminate\Support\Carbon::parse($stock->purchase_date)->format('d M, Y') }}</td></tr>
    </table>

    <a href="{{ route('admin.stocks.index') }}" class="btn btn-secondary">← Back to List</a>
</div>
@endsection
