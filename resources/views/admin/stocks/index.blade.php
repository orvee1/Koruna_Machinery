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

    {{-- Filter by Purchase Date --}}
    <form method="GET" action="{{ route('admin.stocks.index') }}" class="mb-4 row g-2 align-items-center">
        <div class="col-md-4">
            <label for="date" class="form-label">Purchase Date</label>
            <input 
                type="date" 
                name="date" 
                id="date"
                class="form-control @if(request('date')) border-success @endif"
                value="{{ request('date') }}"
            >
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary mt-4">
                <i class="bi bi-filter-circle me-1"></i> Filter
            </button>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.stocks.index') }}" class="btn btn-outline-secondary mt-4">
                <i class="bi bi-x-circle me-1"></i> Clear
            </a>
        </div>
    </form>

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.stocks.index') }}" class="mb-4 row g-2 align-items-center">
        <div class="col-md-6">
            <label for="search" class="form-label">Search Product / Supplier</label>
            <input 
                type="text" 
                name="search" 
                id="search"
                class="form-control @if(request('search')) border-info @endif"
                placeholder="Type product or supplier"
                value="{{ request('search') }}"
            >
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary mt-4">
                <i class="bi bi-search me-1"></i> Search
            </button>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.stocks.index') }}" class="btn btn-outline-secondary mt-4">
                <i class="bi bi-x-circle me-1"></i> Clear
            </a>
        </div>
    </form>

    <a href="{{ route('admin.stocks.create') }}" class="btn btn-primary mb-3">Add New Stock</a>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th style="width:5%;">SL</th>
                <th style="width:10%;">Product</th>
                <th style="width:10%;">Supplier</th>
                <th style="width:5%;">Qty</th>
                <th style="width:10%;">Unit Price</th>
                <th style="width:10%;">Per Unit Sell Price</th>
                <th style="width:10%;">Total (৳)</th>
                <th style="width:10%;">Profit (৳)</th>
                <th style="width:10%;">Due (৳)</th>
                <th style="width:10%;">Date</th>
                <th style="width:15%;">Actions</th>
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
                    <td>{{ number_format($stock->selling_price, 2) }}</td>
                    <td>{{ number_format($stock->total_amount, 2) }}</td>
                    <td>{{ number_format($stock->total_profit, 2) }}</td>
                    <td>{{ number_format($stock->due_amount, 2) }}</td>
                    <td>{{ \Illuminate\Support\Carbon::parse($stock->purchase_date)->format('d M, Y') }}</td>
                    <td class="d-flex flex-wrap gap-1">
                        <a href="{{ route('admin.stocks.show', $stock) }}" class="btn btn-sm btn-info">View</a>
                        <a href="{{ route('admin.stocks.edit', $stock) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.stocks.destroy', $stock) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No stock records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $stocks->withQueryString()->links() }}
    </div>
</div>
@endsection
