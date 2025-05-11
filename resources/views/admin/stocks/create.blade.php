@extends('layouts.app')

@section('title', 'Add New Stock')

@section('content')
<div class="container">
    <h1 class="mb-4">➕ Add New Stock</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.stocks.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="product_name" class="form-label">Product Name</label>
            <input type="text" id="product_name" name="product_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="supplier_name" class="form-label">Supplier Name</label>
            <input type="text" id="supplier_name" name="supplier_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="buying_price" class="form-label">Buying Price (৳)</label>
            <input type="number" id="buying_price" name="buying_price" class="form-control" step="0.01" min="0" required>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" id="quantity" name="quantity" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label for="purchase_date" class="form-label">Purchase Date</label>
            <input type="date" id="purchase_date" name="purchase_date" class="form-control" value="{{ now()->toDateString() }}" required>
        </div>

        <button type="submit" class="btn btn-primary">✔️ Save Stock</button>
    </form>
</div>
@endsection
