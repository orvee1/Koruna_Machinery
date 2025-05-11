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

        <!-- Search Input -->
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
                <th style="width:20%; text-align: center">Product Name</th>
                <th style="width:15%; text-align: center">Supplier Name</th>
                <th style="width:10%; text-align: center">Stock Quantity</th>
                <th style="width:15%; text-align: center">Unit Price (৳)</th>
                <th style="width:15%; text-align: center">Branch</th>
                <th style="width:10%; text-align: center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $i => $stock)
                <tr>
                    <td style="text-align: center">{{ $stocks->firstItem() + $i }}</td>
                    <td style="text-align: center">{{ $stock->product_name }}</td>
                    <td style="text-align: center">{{ $stock->supplier_name }}</td>
                    <td style="text-align: center">{{ $stock->quantity }}</td>
                    <td style="text-align: center">{{ number_format($stock->buying_price, 2) }}</td>
                    <td style="text-align: center">{{ $stock->branch->name ?? '—' }}</td>
                    <td style="text-align: center">
                        <a href="{{ route('admin.products.show', $stock->id) }}"
                           class="btn btn-sm btn-info">🔎 View</a>
                    </td>
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
        {{ $stocks->links() }}
    </div>
</div>
@endsection
