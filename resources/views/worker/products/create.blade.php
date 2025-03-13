@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800">Add New Product</h2>

    <form action="{{ route('products.store') }}" method="POST" class="mt-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" name="name" class="p-3 border rounded" placeholder="Product Name" value="{{ old('name') }}" required>
            <input type="number" name="buying_price" class="p-3 border rounded" placeholder="Buying Price" value="{{ old('buying_price') }}" required>
            <input type="number" name="selling_price" class="p-3 border rounded" placeholder="Selling Price" value="{{ old('selling_price') }}" required>
            <input type="number" name="stock_quantity" class="p-3 border rounded" placeholder="Stock Quantity" value="{{ old('stock_quantity') }}" required>
            <select name="branch_id" class="p-3 border rounded" required>
                <option value="" disabled selected>Select Branch</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button class="bg-blue-600 text-white px-4 py-2 mt-4 rounded hover:bg-blue-700">Save Product</button>
    </form>
</div>
@endsection
