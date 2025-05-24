@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">{{ $user->name }}'s Dashboard</h2>

    {{-- Totals --}}
    <div class="row mb-5 g-3">
        <div class="col-md-6">
            <div class="card shadow-sm text-white bg-primary">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-1">Total Revenue</h5>
                        <p class="h4 mb-0">{{ number_format($totalRevenue,2) }} ৳</p>
                    </div>
                    <i class="bi bi-currency-dollar display-4"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm text-white bg-success">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-1">Total Profit</h5>
                        <p class="h4 mb-0">{{ number_format($totalProfit,2) }} ৳</p>
                    </div>
                    <i class="bi bi-graph-up display-4"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Shared Filter Form --}}
    <form method="GET" class="row gx-3 gy-2 align-items-end mb-4">
        {{-- Preserve pagination params --}}
        <input type="hidden" name="product_page" value="{{ request('product_page') }}">
        <input type="hidden" name="partstock_page" value="{{ request('partstock_page') }}">

        {{-- From Date --}}
        <div class="col-md-3">
            <label class="form-label" for="from_date">From Date</label>
            <input type="date"
                   class="form-control"
                   id="from_date"
                   name="from_date"
                   value="{{ request('from_date') }}">
        </div>

        {{-- To Date --}}
        <div class="col-md-3">
            <label class="form-label" for="to_date">To Date</label>
            <input type="date"
                   class="form-control"
                   id="to_date"
                   name="to_date"
                   value="{{ request('to_date') }}">
        </div>

        {{-- Month --}}
        <div class="col-md-2">
            <label class="form-label" for="month">Month</label>
            <select class="form-select" id="month" name="month">
                <option value="">All Months</option>
                @foreach([
                    1=>'January',2=>'February',3=>'March',4=>'April',
                    5=>'May',6=>'June',7=>'July',8=>'August',
                    9=>'September',10=>'October',11=>'November',12=>'December'
                ] as $num=>$label)
                    <option value="{{ $num }}"
                        {{ request('month') == $num ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Year --}}
        <div class="col-md-2">
            <label class="form-label" for="year">Year</label>
            <select class="form-select" id="year" name="year">
                <option value="">All Years</option>
                @php
                    $current = now()->year;
                    $limit   = $current + 5;
                @endphp
                @for($y = $current; $y <= $limit; $y++)
                    <option value="{{ $y }}"
                        {{ request('year') == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </div>

        {{-- Buttons --}}
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.users.show', $user->id) }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Clear
            </a>
        </div>
    </form>

    {{-- Product Sales --}}
    <h4 class="mb-3">Product Sales</h4>
    <div class="table-responsive mb-4">
        <table class="table table-striped table-hover shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productSales as $sale)
                    <tr>
                        <td>{{ $sale->id }}</td>
                        <td>{{ $sale->customer->name }}</td>
                        <td>{{ optional($sale->stock)->product_name ?? 'N/A' }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>৳ {{ number_format($sale->unit_price,2) }}</td>
                        <td>৳ {{ number_format($sale->total_amount,2) }}</td>
                        <td>{{ $sale->created_at->format('d M, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No product sales found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mb-5">
        {{ $productSales->links() }}
    </div>

    {{-- Part Stock Sales --}}
    <h4 class="mb-3">Part Stock Sales</h4>
    <div class="table-responsive mb-4">
        <table class="table table-striped table-hover shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Part Name</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($partStockSales as $sale)
                    <tr>
                        <td>{{ $sale->id }}</td>
                        <td>{{ $sale->customer->name }}</td>
                        <td>{{ optional($sale->partStock)->product_name ?? 'N/A' }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>৳ {{ number_format($sale->unit_price,2) }}</td>
                        <td>৳ {{ number_format($sale->total_amount,2) }}</td>
                        <td>{{ $sale->created_at->format('d M, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No part-stock sales found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        {{ $partStockSales->links() }}
    </div>
</div>
@endsection
