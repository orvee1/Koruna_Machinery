@extends('layouts.app')

@section('title', 'Edit Stock')

@section('content')
<div class="container">
    <h1 class="mb-4">✏️ Edit Stock</h1>

    <form action="{{ route('worker.stocks.update', $stock) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="product_name" class="form-label">Product Name</label>
            <input type="text" id="product_name" name="product_name" class="form-control" value="{{ $stock->product_name }}" required>
        </div>

        <div class="mb-3">
            <label for="supplier_name" class="form-label">Supplier Name</label>
            <input type="text" id="supplier_name" name="supplier_name" class="form-control" value="{{ $stock->supplier_name }}" required>
        </div>

        <div class="mb-3">
            <label for="buying_price" class="form-label">Buying Price (৳)</label>
            <input type="number" id="buying_price" name="buying_price" class="form-control" value="{{ $stock->buying_price }}" step="0.01" min="0" required>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" id="quantity" name="quantity" class="form-control" value="{{ $stock->quantity }}" min="1" required>
        </div>

        <button type="submit" class="btn btn-primary">✔️ Update Stock</button>
    </form>
</div>
@endsection
