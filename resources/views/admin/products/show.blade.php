@extends('layouts.app')

@section('title', 'Sales History for ' . $product->name)

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800">Sales History for {{ $product->name }}</h2>

    <!-- Product Details -->
    <div class="mt-4">
        <p><strong>Product Name:</strong> {{ $product->name }}</p>
        <p><strong>Buying Price:</strong> ${{ number_format($product->buying_price, 2) }}</p>
        <p><strong>Selling Price:</strong> ${{ number_format($product->selling_price, 2) }}</p>
        <p><strong>Stock Quantity:</strong> {{ $product->stock_quantity }}</p>
        <p><strong>Total Revenue from Sales:</strong> ${{ number_format($product->total_revenue, 2) }}</p>
    </div>

    <!-- Sales History Table -->
    <table class="w-full border mt-4">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-3">Sale ID</th>
                <th class="p-3">Customer</th>
                <th class="p-3">Quantity</th>
                <th class="p-3">Total Amount</th>
                <th class="p-3">Payment Status</th>
                <th class="p-3">Sale Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
                <tr class="border-t">
                    <td class="p-3">{{ $sale->id }}</td>
                    <td class="p-3">{{ $sale->customer->name }}</td>
                    <td class="p-3">{{ $sale->saleDetails->sum('quantity') }}</td> <!-- Assuming you have a SaleDetails model to track quantities -->
                    <td class="p-3">${{ number_format($sale->total_amount, 2) }}</td>
                    <td class="p-3">{{ ucfirst($sale->payment_status) }}</td>
                    <td class="p-3">{{ $sale->created_at->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center p-3">No sales history available for this product.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination Links for Sales -->
    <div class="mt-4">
        {{ $sales->links() }} <!-- Pagination controls for sales if necessary -->
    </div>

</div>
@endsection
