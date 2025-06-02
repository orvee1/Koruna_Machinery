@extends('layouts.app')
@section('title', 'All Sales Panel')

@section('content')
<div class="container">
    <h2 class="mb-4">🧾 Unified Sales Panel (Bill Based)</h2>

    {{-- 🔍 Filter --}}
    <form method="GET" action="{{ route('manager.sales.index') }}" class="row gy-2 gx-3 align-items-end mb-4">
        <div class="col-auto">
            <label>Date</label>
            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-auto">
            <label>Month</label>
            <select name="month" class="form-select">
                <option value="">All</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-auto">
            <label>Year</label>
            <select name="year" class="form-select">
                @for($y = now()->year; $y <= now()->year + 5; $y++)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-auto">
            <label>Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="due" {{ request('status') == 'due' ? 'selected' : '' }}>Due</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    {{-- 📊 Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>SL No</th>
                    <th>Customer</th>
                    {{-- <th>Items</th> --}}
                    <th>Total (৳)</th>
                    <th>Paid (৳)</th>
                    <th>Due (৳)</th>
                    <th>Seller</th>
                    <th>Date</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bills as $bill)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $bill->customer->name ?? 'N/A' }}</td>
                        {{-- <td>
                            @foreach($bill->productSales as $sale)
                                🟢 {{ $sale->stock->product_name ?? 'N/A' }} ({{ $sale->quantity }}x{{ number_format($sale->unit_price, 2) }})<br>
                            @endforeach
                            @foreach($bill->partStockSales as $sale)
                                🔵 {{ $sale->partStock->product_name ?? 'N/A' }} ({{ $sale->quantity }}x{{ number_format($sale->unit_price, 2) }})<br>
                            @endforeach
                        </td> --}}
                        <td>{{ number_format($bill->total_amount, 2) }}</td>
                        <td>{{ number_format($bill->paid_amount, 2) }}</td>
                        <td class="{{ $bill->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($bill->due_amount, 2) }}
                        </td>
                        <td>{{ $bill->seller->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($bill->created_at)->format('Y-m-d') }}</td>
                        <td class="text-center">
                        <a href="{{ route('manager.sales.show', $bill->id) }}" class="btn btn-sm btn-outline-info">View</a>
                        <form action="{{ route('manager.sales.destroy', $bill->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this bill and all related sales?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">🗑️ Delete</button>
                        </form>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
