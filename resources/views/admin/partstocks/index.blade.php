@extends('layouts.app')

@section('title', 'Part Stocks')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">📦 Part Stock List</h1>

    @include('components.alert')

    <!-- Filter/Search -->
    <form method="GET" action="{{ route('admin.partstocks.index') }}" class="row g-2 mb-3">
        <div class="col-md-3">
            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by product/supplier" value="{{ request('search') }}">
        </div>
        <div class="col-md-auto">
            <button class="btn btn-primary"><i class="bi bi-search"></i> Filter</button>
        </div>
        <div class="col-md-auto">
            <a href="{{ route('admin.partstocks.index') }}" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Clear</a>
        </div>
        <div class="col-md-auto ms-auto">
            <a href="{{ route('admin.partstocks.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Add New
            </a>
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>SL</th>
                    <th>Product</th>
                    <th>Supplier</th>
                    <th>Buying Price</th>
                    <th>Selling Price</th>
                    <th>Qty</th>
                    <th>Total Amount</th>
                    <th>Due Amount</th>
                    <th>Total Profit</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($partstocks as $key => $partstock)
                    <tr>
                        <td>{{ $partstocks->firstItem() + $key }}</td>
                        <td>{{ $partstock->product_name }}</td>
                        <td>{{ $partstock->supplier_name }}</td>
                        <td>৳{{ number_format($partstock->buy_value, 2) }}</td>
                        <td>৳{{ number_format($partstock->sell_value, 2) }}</td>
                        <td>{{ $partstock->quantity }}</td>
                        <td>৳{{ number_format($partstock->amount, 2) }}</td>
                        <td>৳{{ number_format($partstock->sell_value, 2) }}</td>
                        <td>৳{{ number_format($partstock->total_profit, 2) }}</td>
                        <td>
                            <a href="{{ route('admin.partstocks.edit', $partstock->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <a href="{{ route('admin.partstocks.show', $partstock->id) }}" class="btn btn-sm btn-info">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No data found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-3">
        {{ $partstocks->links() }}
    </div>
</div>
@endsection
