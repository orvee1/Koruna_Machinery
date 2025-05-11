@extends('layouts.app')

@section('title', 'Stock List')

@section('content')
<div class="container">
    <h1 class="mb-4">Stock List</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <a href="{{ route('admin.stocks.create') }}" class="btn btn-primary mb-3">➕ Add New Stock</a>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>SL No</th>
                <th>Product</th>
                <th>Supplier</th>
                <th>Quantity</th>
                <th>Unit Price (৳)</th>
                <th>Total Amount (৳)</th>
                <th>Due (৳)</th>
                <th>Purchase Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $i => $stock)
                <tr>
                    <td>{{ $stocks->firstItem() + $i }}</td>
                    <td>{{ $stock->product_name }}</td>
                    <td>{{ $stock->supplier_name }}</td>
                    <td>{{ $stock->quantity }}</td>
                    <td>{{ number_format($stock->buying_price, 2) }}</td>
                    <td>{{ number_format($stock->total_amount, 2) }}</td>
                    <td>{{ number_format($stock->due_amount, 2) }}</td>
                    <td>{{ \Illuminate\Support\Carbon::parse($stock->purchase_date)->format('d M, Y') }}</td>
                    <td class="d-flex flex-wrap gap-1">
                        <a href="{{ route('admin.stocks.show', $stock) }}" class="btn btn-sm btn-info">🔎 View</a>
                        <a href="{{ route('admin.stocks.edit', $stock) }}" class="btn btn-sm btn-warning">✏️ Edit</a>
                        <form action="{{ route('admin.stocks.destroy', $stock) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">🗑️ Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No stock records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $stocks->withQueryString()->links() }}
    </div>
</div>
@endsection
