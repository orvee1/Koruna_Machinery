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
    <form method="GET" action="{{ route('admin.product-sales.index') }}" class="row gy-2 gx-3 align-items-end mb-4">
    {{-- Date --}}
    <div class="col-auto">
        <label for="date" class="form-label">Date</label>
        <input
            type="date"
            name="date"
            id="date"
            class="form-control"
            value="{{ request('date') }}"
        >
    </div>

    {{-- Month --}}
    <div class="col-auto">
        <label for="month" class="form-label">Month</label>
        <select name="month" id="month" class="form-select">
            <option value="">All</option>
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                </option>
            @endfor
        </select>
    </div>

    {{-- Year --}}
    <div class="col-auto">
        <label for="year" class="form-label">Year</label>
        <select name="year" id="year" class="form-select">
            <option value="">All</option>
            @php
                $current = date('Y');
                $start = $current + 5;
            @endphp
            @for($y = $current; $y <= $start; $y++)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                    {{ $y }}
                </option>
            @endfor
        </select>
    </div>

    {{-- Payment Status --}}
    <div class="col-auto">
    <label for="status" class="form-label">Payment Status</label>
    <select name="status"
            id="status"
            class="form-select"
            onchange="this.form.submit()">
        <option value="" {{ request('status') === null || request('status') === '' ? 'selected' : '' }}>
            All
        </option>
        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>
            Paid
        </option>
        <option value="due" {{ request('status') === 'due' ? 'selected' : '' }}>
            Due
        </option>
    </select>
    </div>

    {{-- Buttons --}}
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-funnel"></i> Filter
        </button>
        <a href="{{ route('admin.partstock-sales.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-circle"></i> Clear
        </a>
    </div>
</form>


    {{-- 📋 Product Sales Table --}}
    <div class="table-responsive">
        <table class="table table-bordered align-middle table-hover">
            <thead class="table-dark">
                <tr>
                    <th>SL No</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Total Amount (৳)</th>
                    <th>Paid (৳)</th>
                    <th>Due (৳)</th>
                    <th>Seller</th>
                    <th>Date</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
           <tbody>
    @foreach($salesGrouped as $group)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $group['customer']->name ?? 'N/A' }}</td>
            <td>
                @foreach($group['sales'] as $sale)
                    {{ $sale->stock->product_name ?? 'N/A' }} ({{ $sale->quantity }}x{{ number_format($sale->unit_price, 2) }})<br>
                @endforeach
            </td>
            <td>{{ number_format($group['total'], 2) }}</td>
            <td>{{ number_format($group['paid'], 2) }}</td>
            <td class="{{ $group['due'] > 0 ? 'text-danger' : 'text-success' }}">
                {{ number_format($group['due'], 2) }}
            </td>
            <td>{{ $group['sales'][0]->seller->name ?? 'N/A' }}</td>
            <td>{{ \Carbon\Carbon::parse($group['sales'][0]->created_at)->format('Y-m-d') }}</td>
            <td class="text-center">
                <a href="{{ route('admin.product-sales.show', $group['sales'][0]->id) }}" class="btn btn-sm btn-info">🔎 View</a>
                 <form action="{{ route('admin.product-sales.destroy', $group['sales'][0]->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this group sale?');">
                @csrf
                 @method('DELETE')
                <button class="btn btn-sm btn-danger">🗑️ Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
</tbody>

        </table>
    </div>

    {{-- Pagination --}}
    {{-- <div class="d-flex justify-content-center">
        {{ $sales->links() }}
    </div> --}}
</div>
@endsection
