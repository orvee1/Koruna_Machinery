@extends('layouts.app')

@section('title', 'Edit Stock')

@section('content')
<div class="container">
    <h1 class="mb-4">✏️ Edit Stock</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.stocks.update', $stock->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="product_name" class="form-label">Product Name</label>
            <input type="text" id="product_name" name="product_name" class="form-control" value="{{ old('product_name', $stock->product_name) }}" required>
        </div>

        <div class="mb-3">
            <label for="supplier_name" class="form-label">Supplier Name</label>
            <input type="text" id="supplier_name" name="supplier_name" class="form-control" value="{{ old('supplier_name', $stock->supplier_name) }}" required>
        </div>

        <div class="mb-3">
            <label for="buying_price" class="form-label">Buying Price (৳)</label>
            <input type="number" id="buying_price" name="buying_price" class="form-control" value="{{ old('buying_price', $stock->buying_price) }}" step="0.01" min="0" required>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" id="quantity" name="quantity" class="form-control" value="{{ old('quantity', $stock->quantity) }}" min="1" required>
        </div>

        <div class="mb-3">
            <label for="purchase_date" class="form-label">Purchase Date</label>
            <input type="date" id="purchase_date" name="purchase_date" class="form-control" value="{{ old('purchase_date', \Carbon\Carbon::parse($stock->purchase_date)->format('Y-m-d')) }}" required>
        </div>

        <div class="mb-3">
            <label for="deposit_amount" class="form-label">Deposit Amount (Optional)</label>
            <input type="number" id="deposit_amount" name="deposit_amount" class="form-control" value="{{ old('deposit_amount', $stock->deposit_amount) }}" step="0.01" min="0">
        </div>

        <button type="submit" class="btn btn-primary">✔️ Update Stock</button>
    </form>
</div>
@endsection
