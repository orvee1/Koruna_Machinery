@extends('layouts.app')

@section('title', 'Add New Product Sale')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>🛒 Add New Product Sale</h2>
        <a href="{{ route('manager.product-sales.index') }}" class="btn btn-secondary">
            ← Back to Sales List
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('manager.product-sales.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="stock_id" class="form-label">Select Product</label>
            <select class="form-select" name="stock_id" id="stock_id" required>
                <option value="" selected disabled>-- Select Product --</option>
                @foreach($stocks as $stock)
                    <option value="{{ $stock->id }}" data-price="{{ $stock->buying_price }}">
                        {{ $stock->product_name }} - [Stock: {{ $stock->quantity }} pcs]
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="customer_id" class="form-label">Select Customer</label>
            <select class="form-select" name="customer_id" required>
                <option value="" selected disabled>-- Select Customer --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" name="quantity" id="quantity" min="1" required>
        </div>

        <div class="mb-3">
            <label for="unit_price" class="form-label">Unit Price</label>
            <input type="number" class="form-control" name="unit_price" id="unit_price" required>
        </div>

        <div class="mb-3">
            <label for="paid_amount" class="form-label">Paid Amount</label>
            <input type="number" class="form-control" name="paid_amount" required>
        </div>

        <button type="submit" class="btn btn-primary">✔️ Add Sale</button>
    </form>
</div>
@endsection
