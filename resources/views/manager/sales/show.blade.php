@extends('layouts.app')
@section('title', 'Bill #' . $bill->id)

@section('content')
<div class="container">
    <h3 class="mb-3">🧾 Bill #{{ $bill->id }}</h3>

    <p><strong>Customer:</strong> {{ $bill->customer->name ?? 'N/A' }}</p>
    <p><strong>Seller:</strong> {{ $bill->seller->name ?? 'N/A' }}</p>
    <p><strong>Date:</strong> {{ $bill->created_at->format('Y-m-d') }}</p>

    <div class="row">
        <div class="col-md-6">
            <h5>🟢 Product Sales</h5>
            <ul class="list-group">
                @foreach($bill->productSales as $sale)
                    <li class="list-group-item">
                        {{ $sale->stock->product_name ?? 'N/A' }} — {{ $sale->quantity }} × {{ number_format($sale->unit_price, 2) }} = ৳{{ number_format($sale->total_amount, 2) }}
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="col-md-6">
            <h5>🔵 Part Stock Sales</h5>
            <ul class="list-group">
                @foreach($bill->partStockSales as $sale)
                    <li class="list-group-item">
                        {{ $sale->partStock->product_name ?? 'N/A' }} — {{ $sale->quantity }} × {{ number_format($sale->unit_price, 2) }} = ৳{{ number_format($sale->total_amount, 2) }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <hr>
    <h5>Total Summary</h5>
    <p><strong>Total:</strong> ৳{{ number_format($bill->total_amount, 2) }}</p>
    <p><strong>Paid:</strong> ৳{{ number_format($bill->paid_amount, 2) }}</p>
    <p><strong>Due:</strong> ৳{{ number_format($bill->due_amount, 2) }}</p>
</div>
@endsection
