@extends('layouts.app')

@section('title', 'Products List')

@section('content')
<div class="container">
    <h1 class="mb-4">Products List</h1>

    <!-- Messages -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Filter & Search Form -->
    <form method="GET" action="{{ route('worker.products.index') }}" class="mb-4 row g-2">
        <!-- Date Filter -->
        <div class="col-md-3">
            <input
                type="date"
                name="date"
                id="date"
                class="form-control @if(request('date')) border-success @endif"
                value="{{ request('date') }}"
            >
        </div>

        <!-- Product Name Filter -->
        <div class="col-md-3">
            <input
                type="text"
                name="product_name"
                id="product_name"
                class="form-control @if(request('product_name')) border-info @endif"
                placeholder="Product Name"
                value="{{ request('product_name') }}"
            >
        </div>

        <!-- Supplier Name Filter -->
        <div class="col-md-3">
            <input
                type="text"
                name="supplier_name"
                id="supplier_name"
                class="form-control @if(request('supplier_name')) border-warning @endif"
                placeholder="Supplier Name"
                value="{{ request('supplier_name') }}"
            >
        </div>

        <!-- Buttons -->
        <div class="col-auto align-self-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-filter-circle me-1"></i> Search
            </button>
        </div>
        <div class="col-auto align-self-end">
            <a href="{{ route('worker.products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i> Clear
            </a>
        </div>
    </form>

    <!-- Products Table -->
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th style="width:5%; text-align: center">SL No</th>
                <th style="width:20%; text-align: center">Product Name</th>
                <th style="width:15%; text-align: center">Supplier Name</th>
                <th style="width:10%; text-align: center">Stock Quantity</th>
                <th style="width:15%; text-align: center">Unit Price (৳)</th>
                <th style="width:15%; text-align: center">Total Amount (৳)</th>
                <th style="width:15%; text-align: center">Branch</th>
                <th style="width:15%; text-align: center">Purchase Date</th>
                {{-- <th style="width:10%; text-align: center">Actions</th> --}}
            </tr>
        </thead>
        <tbody>
             @forelse($products as $i => $product)
                <tr>
                    <td style="text-align: center">{{ $products->firstItem() + $i }}</td>
                    <td style="text-align: center">{{ $product->product_name }}</td>
                    <td style="text-align: center">{{ $product->supplier_name }}</td>
                    <td style="text-align: center">{{ $product->quantity }}</td>
                    <td style="text-align: center">{{ number_format($product->buying_price, 2) }}</td>
                    <td style="text-align: center">{{ number_format($product->total_amount, 2) }}</td>
                    <td style="text-align: center">{{ $product->branch->name ?? '—' }}</td>
                    <td style="text-align: center">{{ $product->purchase_date }}</td>
                    {{-- <td style="text-align: center">
                        <a href="{{ route('worker.products.show', $product->id) }}"
                           class="btn btn-sm btn-info">
                            🔎 View
                        </a>
                    </td> --}}
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">কোনো পণ্য পাওয়া যায়নি।</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $products->links() }}
    </div>
</div>
@endsection
