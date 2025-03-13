@extends('layouts.app')

@section('title', 'Product List')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800">Product List</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mt-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter Form for Branch -->
    <form action="{{ route('products.index') }}" method="GET" class="mb-4">
        <div class="flex items-center gap-4">
            <select name="branch_id" class="p-3 border rounded">
                <option value="" disabled selected>Select Branch</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filter</button>
        </div>
    </form>

    <!-- Product Table -->
    <table class="w-full border mt-4">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-3">Name</th>
                <th class="p-3">Buying Price</th>
                <th class="p-3">Selling Price</th>
                <th class="p-3">Stock Quantity</th>
                <th class="p-3">Branch</th>
                <th class="p-3">Total Revenue</th>
                <th class="p-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr class="border-t">
                <td class="p-3">{{ $product->name }}</td>
                <td class="p-3">${{ number_format($product->buying_price, 2) }}</td>
                <td class="p-3">${{ number_format($product->selling_price, 2) }}</td>
                <td class="p-3">{{ $product->stock_quantity }}</td>
                <td class="p-3">{{ $product->branch->name }}</td>
                <td class="p-3">${{ number_format($product->total_revenue, 2) }}</td> <!-- Total Revenue -->
                <td class="p-3">
                    <a href="{{ route('products.edit', $product->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Edit</a>
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $products->links() }} <!-- This will display the pagination controls -->
    </div>
</div>
@endsection
