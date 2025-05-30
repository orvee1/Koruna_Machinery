@extends('layouts.app')

@section('title', '📦 Stock List')

@section('content')
<div class="container mt-4">

    {{-- ✅ Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-primary m-0">📦 Stock List</h2>
            <small class="text-muted">Showing all stock entries for the selected branch</small>
        </div>
        <a href="{{ route('admin.stocks.create') }}" class="btn btn-success shadow-sm">
            ➕ Add New Stock
        </a>
    </div>

    {{-- ✅ Filters --}}
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" value="{{ $search }}"
                placeholder="🔍 Search by product or supplier...">
        </div>
        <div class="col-md-3">
            <input type="date" name="date" class="form-control" value="{{ $date }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                🔎 Filter
            </button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.stocks.index') }}" class="btn btn-outline-secondary w-100">
                ❌ Clear
            </a>
        </div>
    </form>

    {{-- ✅ Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ✅ Data Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-dark text-center">
                    <tr>
                        <th>SL</th>
                        <th>Product</th>
                        <th>Supplier</th>
                        <th>Qty</th>
                        <th>Unit Price (৳)</th>
                        <th>Total Amount(৳)</th>
                        <th>Profit (৳)</th>
                        <th>Due (৳)</th>
                        <th>Purchase Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $i => $stock)
                        <tr>
                            <td class="text-center">{{ $stocks->firstItem() + $i }}</td>
                            <td>{{ $stock->product_name }}</td>
                            <td>{{ $stock->supplier_name }}</td>
                            <td class="text-center">{{ $stock->quantity }}</td>
                            <td class="text-end">{{ number_format($stock->buying_price, 2) }}</td>
                            <td class="text-end">{{ number_format($stock->total_amount, 2) }}</td>
                            <td class="text-end text-success fw-semibold">{{ number_format($stock->total_profit, 2) }}</td>
                            <td class="text-end fw-bold {{ $stock->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($stock->due_amount, 2) }}
                            </td>
                            <td class="text-nowrap text-center">
                                {{ \Carbon\Carbon::parse($stock->purchase_date)->format('d M, Y') }}
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.stocks.show', $stock) }}"
                                       class="btn btn-sm btn-outline-primary" title="View Details">
                                        🔍
                                    </a>
                                    <a href="{{ route('admin.stocks.edit', $stock) }}"
                                       class="btn btn-sm btn-outline-warning" title="Edit Stock">
                                        ✏️
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                😕 No stock records found for this filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ✅ Pagination --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $stocks->withQueryString()->links() }}
    </div>

</div>
@endsection
