@extends('layouts.app')

@section('title', 'View Part Stock Sale')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>View Part Stock Sale</h2>
        <a href="{{ route('worker.partstock-sales.index') }}" class="btn btn-secondary">Back to List</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Sale Details</h5>

            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Customer:</strong> {{ $partStockSale->customer->name ?? 'N/A' }}
                </div>
                <div class="col-md-6">
                    <strong>Part Stock Product:</strong> {{ $partStockSale->partStock->product_name ?? 'N/A' }}
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4">
                    <strong>Quantity:</strong> {{ $partStockSale->quantity }}
                </div>
                <div class="col-md-4">
                    <strong>Unit Price:</strong> {{ number_format($partStockSale->unit_price, 2) }}
                </div>
                <div class="col-md-4">
                    <strong>Paid Amount:</strong> {{ number_format($partStockSale->paid_amount, 2) }}
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Seller:</strong> {{ $partStockSale->seller->name ?? 'N/A' }}
                </div>
                <div class="col-md-6">
                    <strong>Sale Date:</strong> {{ $partStockSale->created_at->format('Y-m-d') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
