@extends('layouts.app')

@section('title', 'Sale Details')

@section('content')
<div class="container">
    <h2 class="mb-4">📝 Sale Details</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">🔹 Product: {{ $productSale->product->name ?? 'N/A' }}</h5>
            <p class="card-text"><strong>Customer:</strong> {{ $productSale->customer->name ?? 'N/A' }}</p>
            <p class="card-text"><strong>Quantity:</strong> {{ $productSale->quantity }}</p>
            <p class="card-text"><strong>Unit Price:</strong> {{ number_format($productSale->unit_price, 2) }} ৳</p>
            <p class="card-text"><strong>Paid Amount:</strong> {{ number_format($productSale->paid_amount, 2) }} ৳</p>
            <p class="card-text"><strong>Due Amount:</strong> 
                <span class="{{ $productSale->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($productSale->due_amount, 2) }} ৳
                </span>
            </p>
            <p class="card-text"><strong>Profit:</strong> 
                <span class="{{ $productSale->profit > 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($productSale->profit, 2) }} ৳
                </span>
            </p>
            <p class="card-text"><strong>Branch:</strong> {{ $productSale->branch->name ?? 'N/A' }}</p>
            <p class="card-text"><strong>Sold By:</strong> {{ $productSale->seller->name ?? 'N/A' }}</p>
            <p class="card-text"><strong>Date:</strong> {{ $productSale->created_at->format('d M Y, h:i A') }}</p>

            <div class="mt-4 d-flex justify-content-end">
                <a href="{{ route('admin.product-sales.index') }}" class="btn btn-secondary">⬅️ Back to Sales</a>
            </div>
        </div>
    </div>
</div>
@endsection
