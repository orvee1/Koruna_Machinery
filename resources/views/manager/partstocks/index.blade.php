@extends('layouts.app')

@section('title', 'Part Stocks')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">📦 Part Stock List</h1>

    {{-- Inline Bootstrap Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filter/Search -->
    <form method="GET" action="{{ route('manager.partstocks.index') }}" class="row g-2 mb-3">
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
            <a href="{{ route('manager.partstocks.index') }}" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Clear</a>
        </div>
        <div class="col-md-auto ms-auto">
            <a href="{{ route('manager.partstocks.create') }}" class="btn btn-success shadow-sm">
                <i class="bi bi-plus-circle"></i>➕ Add New Stock

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
                        <td>{{ number_format($partstock->buying_price, 2) }}</td>
                        <td>{{ number_format($partstock->sell_value, 2) }}</td>
                        <td>{{ $partstock->quantity }}</td>
                        <td>{{ number_format($partstock->total_amount, 2) }}</td>
                        <td class="{{ $partstock->due_amount > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                        {{ number_format($partstock->due_amount, 2) }}
                        </td>
                        <td class="text-end text-success fw-semibold">{{ number_format($partstock->total_profit, 2) }}</td>
                        <td>
                            <a href="{{ route('manager.partstocks.show', $partstock->id) }}" class="btn btn-sm btn-info">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No data found.</td>
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
