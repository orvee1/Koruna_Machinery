@extends('layouts.app')

@section('title', 'Product Sales List')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>🛒 Product Sales List</h2>
        <a href="{{ route('admin.product-sales.create') }}" class="btn btn-success">
            ➕ Add New Sale
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 🔎 Filter --}}
    <form method="GET" class="row row-cols-lg-auto g-3 mb-4">
        <div class="col">
            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col">
            <input type="number" name="month" class="form-control" placeholder="Month" value="{{ request('month') }}">
        </div>
        <div class="col">
            <input type="number" name="year" class="form-control" placeholder="Year" value="{{ request('year') }}">
        </div>
        <div class="col">
            <button type="submit" class="btn btn-secondary">🔎 Filter</button>
        </div>
    </form>

    {{-- 📋 Product Sales Table --}}
    <div class="table-responsive">
        <table class="table table-bordered align-middle table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price (৳)</th>
                    <th>Paid (৳)</th>
                    <th>Due (৳)</th>
                    <th>Profit (৳)</th>
                    <th>Seller</th>
                    <th>Date</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                        <td>
    @if($sale->stock)
        {{ $sale->stock->product_name }}
    @else
        <span class="text-danger">No Stock Found</span>
    @endif
</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>{{ number_format($sale->unit_price, 2) }}</td>
                        <td>{{ number_format($sale->paid_amount, 2) }}</td>
                        <td class="{{ $sale->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($sale->due_amount, 2) }}
                        </td>
                        <td class="{{ $sale->profit > 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($sale->profit, 2) }}
                        </td>
                        <td>{{ $sale->seller->name ?? 'N/A' }}</td>
                        <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.product-sales.show', $sale->id) }}" class="btn btn-sm btn-info">
                                🔎 View
                            </a>
                            <form action="{{ route('admin.product-sales.destroy', $sale->id) }}" method="POST" class="d-inline-block"
                                  onsubmit="return confirm('Are you sure you want to delete this sale?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">🗑️ Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center text-danger">No sales found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center">
        {{ $sales->links() }}
    </div>
</div>
@endsection
