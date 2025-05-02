@extends('layouts.app')

@section('title', 'Edit Product Sale')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Product Sale</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('manager.product-sales.update', $productSale->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="product_id" class="form-label">Product</label>
                <select name="product_id" id="product_id" class="form-select" required>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ $productSale->product_id == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="customer_id" class="form-label">Customer</label>
                <select name="customer_id" id="customer_id" class="form-select" required>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $productSale->customer_id == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" name="quantity" id="quantity" class="form-control" min="1"
                       value="{{ old('quantity', $productSale->quantity) }}" required>
            </div>

            <div class="col-md-4">
                <label for="unit_price" class="form-label">Unit Price</label>
                <input type="number" name="unit_price" id="unit_price" class="form-control" step="0.01" min="0"
                       value="{{ old('unit_price', $productSale->unit_price) }}" required>
            </div>

            <div class="col-md-4">
                <label for="paid_amount" class="form-label">Paid Amount</label>
                <input type="number" name="paid_amount" id="paid_amount" class="form-control" step="0.01" min="0"
                       value="{{ old('paid_amount', $productSale->paid_amount) }}" required>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success">Update Sale</button>
        </div>
    </form>
</div>
@endsection
