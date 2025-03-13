@extends('layouts.app')

@section('title', 'Edit Product - ' . $product->name)

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800">Edit Product: {{ $product->name }}</h2>

    <form action="{{ route('products.update', $product->id) }}" method="POST" class="mt-4">
        @csrf
        @method('PUT') <!-- This method is required to update an existing resource -->

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Product Name -->
            <input type="text" name="name" class="p-3 border rounded" value="{{ old('name', $product->name) }}" placeholder="Product Name" required>
            
            <!-- Buying Price -->
            <input type="number" name="buying_price" class="p-3 border rounded" value="{{ old('buying_price', $product->buying_price) }}" placeholder="Buying Price" required>
            
            <!-- Selling Price -->
            <input type="number" name="selling_price" class="p-3 border rounded" value="{{ old('selling_price', $product->selling_price) }}" placeholder="Selling Price" required>
            
            <!-- Stock Quantity -->
            <input type="number" name="stock_quantity" class="p-3 border rounded" value="{{ old('stock_quantity', $product->stock_quantity) }}" placeholder="Stock Quantity" required>
            
            <!-- Branch Selection -->
            <select name="branch_id" class="p-3 border rounded" required>
                <option value="" disabled selected>Select Branch</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ old('branch_id', $product->branch_id) == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 mt-4 rounded hover:bg-blue-700">Update Product</button>
    </form>
</div>
@endsection
