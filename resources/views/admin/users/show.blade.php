@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">{{ $user->name }}'s Dashboard</h2>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total Revenue</div>
                <div class="card-body">
                    <h5 class="card-title">{{ number_format($totalRevenue, 2) }} ৳</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Profit</div>
                <div class="card-body">
                    <h5 class="card-title">{{ number_format($totalProfit, 2) }} ৳</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Filter Form -->
   <form method="GET" class="mb-4">
    <div class="row">
        <!-- ✅ From Date -->
        <div class="col-md-3">
            <label for="from_date">From Date</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
        </div>

        <!-- ✅ To Date -->
        <div class="col-md-3">
            <label for="to_date">To Date</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
        </div>

        <!-- ✅ Month Dropdown -->
        <div class="col-md-2">
            <label for="month">Select Month</label>
            <select name="month" class="form-control">
                <option value="">-- Select Month --</option>
                @foreach ([
                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                ] as $key => $month)
                    <option value="{{ $key }}" {{ request('month') == $key ? 'selected' : '' }}>
                        {{ $month }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- ✅ Year Dropdown -->
        <div class="col-md-2">
            <label for="year">Select Year</label>
            <select name="year" class="form-control">
                <option value="">-- Select Year --</option>
                @for ($year = now()->year; $year >= 2000; $year--)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endfor
            </select>
        </div>

        <!-- ✅ Filter Button -->
        <div class="col-md-1">
            <button class="btn btn-primary w-100 mt-4">Filter</button>
        </div>

        <!-- ✅ Clear Button -->
        <div class="col-md-1">
            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-danger w-100 mt-4">Clear</a>
        </div>
    </div>
</form>


    <!-- ✅ Sales Table -->
    <table class="table table-bordered">
        <thead class="bg-dark text-white">
            <tr>
                <th>Sale ID</th>
                <th>Type</th>
                <th>Product/Part</th>
                <th>Customer</th>
                <th>Quantity</th>
                <th>Unit Price (৳)</th>
                <th>Total Amount (৳)</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
                <tr>
                    <td>{{ $sale->id }}</td>
                    <td>
                        @if($sale->sale_type === 'ProductSale')
                            Product Sale
                        @elseif($sale->sale_type === 'PartStockSale')
                            Part Stock Sale
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($sale->sale_type === 'ProductSale')
                            {{ optional($sale->product)->name ?? 'N/A' }}
                        @elseif($sale->sale_type === 'PartStockSale')
                            {{ optional($sale->partStock)->name ?? 'N/A' }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ optional($sale->customer)->name ?? 'N/A' }}</td>
                    <td>{{ $sale->quantity }}</td>
                    <td>{{ number_format($sale->unit_price, 2) }} ৳</td>
                    <td>{{ number_format($sale->total_amount, 2) }} ৳</td>
                    <td>{{ $sale->created_at->format('d M, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No Sales Found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- ✅ Pagination -->
    <div class="d-flex justify-content-center">
        {{ $sales->links() }}
    </div>
</div>
@endsection
