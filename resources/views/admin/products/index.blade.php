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
    <form method="GET" action="{{ route('admin.products.index') }}" class="mb-4 row g-2">
        <!-- Date Filter -->
        <div class="col-md-4">
            <input
                type="date"
                name="date"
                id="date"
                class="form-control @if(request('date')) border-success @endif"
                value="{{ request('date') }}"
            >
        </div>
        <div class="col-auto align-self-end">
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i> Clear
            </a>
        </div>
        <!-- Search Input -->
        <div class="row-md-4">
            <div class="col-md-4">
                <input
                type="text"
                name="search"
                id="search"
                class="form-control @if(request('search')) border-info @endif"
                placeholder="Type product or branch name"
                value="{{ request('search') }}"
            >
            </div>
        </div>

        <!-- Buttons -->
        <div class="col-auto align-self-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-filter-circle me-1"></i> Search
            </button>
        </div>
        <div class="col-auto align-self-end">
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i> Clear
            </a>
        </div>
    </form>

    <!-- Products Table -->
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th style="width:5%; text-align: center">SL No</th>
                <th style="width:20%; text-align: center" >Name</th>
                <th style="width:15%; text-align: center">Buying Price (৳)</th>
                <th style="width:15%; text-align: center">Selling Price (৳)</th>
                <th style="width:10%; text-align: center">Stock Qty</th>
                <th style="width:15%; text-align: center">Branch</th>
                <th style="width:15%; text-align: center">Last Purchase</th>
                <th style="width:10%; text-align: center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $i => $product)
                <tr>
                    <td>{{ $products->firstItem() + $i }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ number_format($product->buying_price, 2) }}</td>
                    <td>{{ $product->selling_price !== null ? number_format($product->selling_price, 2) : '—' }}</td>
                    <td>{{ $product->stock_quantity }}</td>
                    <td>{{ $product->branch->name ?? '—' }}</td>
                    <td>{{ optional($product->last_purchase_date)->format('d M, Y') ?? '—' }}</td>
                    <td>
                        <a href="{{ route('admin.products.show', $product->id) }}"
                           class="btn btn-sm btn-info">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">কোনো পণ্য পাওয়া যায়নি।</td>
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
