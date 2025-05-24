@extends('layouts.app')

@section('title', 'Stock List')

@section('content')
<div class="container mt-4">

    {{-- ✅ Page Title --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">📦 Stock List</h2>
        <a href="{{ route('admin.stocks.create') }}" class="btn btn-success shadow-sm">
            ➕ Add New Stock
        </a>
    </div>

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

    {{-- ✅ Table --}}
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
                        <th>Total (৳)</th>
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
                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($stock->purchase_date)->format('d M, Y') }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.stocks.show', $stock) }}" class="btn btn-sm btn-outline-primary">
                                        🔍
                                    </a>
                                    <a href="{{ route('admin.stocks.edit', $stock) }}" class="btn btn-sm btn-outline-warning">
                                        ✏️
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                😕 No stock records found.
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
