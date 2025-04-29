@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="container">
    <h1>Edit Product</h1>

    <!-- Display success or error messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('worker.products.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name', $product->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="buying_price" class="form-label">Buying Price</label>
            <input type="number" step="0.01" class="form-control @error('buying_price') is-invalid @enderror" name="buying_price" id="buying_price" value="{{ old('buying_price', $product->buying_price) }}" required>
            @error('buying_price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="selling_price" class="form-label">Selling Price</label>
            <input type="number" step="0.01" class="form-control @error('selling_price') is-invalid @enderror" name="selling_price" id="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required>
            @error('selling_price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="stock_quantity" class="form-label">Stock Quantity</label>
            <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
            @error('stock_quantity')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="branch_id" class="form-label">Branch</label>
            <select class="form-select @error('branch_id') is-invalid @enderror" name="branch_id" id="branch_id" required>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" @if($product->branch_id == $branch->id) selected @endif>{{ $branch->name }}</option>
                @endforeach
            </select>
            @error('branch_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>

    <!-- Adjust Stock Form -->
    {{-- <h3>Adjust Stock</h3>
    <form action="{{ route('worker.products.adjustStock', $product->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="quantity">Quantity to Adjust</label>
            <input type="number" id="quantity" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" required min="1">
            @error('quantity')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary mt-3">Adjust Stock</button>
    </form> --}}

</div>
@endsection
