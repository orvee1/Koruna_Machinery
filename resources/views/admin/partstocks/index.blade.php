@extends('layouts.app')

@section('title', 'Part Stocks')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Part Stocks</h1>

    <!-- Display success or error messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Date filter form -->
    <form method="GET" action="{{ route('admin.partstocks.index') }}" class="mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="date" 
                       name="date" 
                       class="form-control @if(request('date')) border-success @endif"
                       value="{{ old('date', request('date')) }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-filter-circle me-1"></i> Filter
                </button>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.partstocks.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i> Clear
                </a>
            </div>
        </div>
    </form>
    

    <!-- Search Form -->
    <form action="{{ route('admin.partstocks.index') }}" method="GET" class="mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">
                <input type="text" 
                       name="search" 
                       class="form-control @if(request('search')) border-info @endif" 
                       placeholder="Search by Product or Supplier" 
                       value="{{ old('search', request('search')) }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i> Search
                </button>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.partstocks.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i> Clear
                </a>
            </div>
        </div>
    </form>
    

    <!-- Add New Part Stock Button -->
    <a href="{{ route('admin.partstocks.create') }}" class="btn btn-success mb-3">Add New Part Stock</a>

    <!-- Part Stocks Table -->
    <table class="table table-hover table-bordered">
        <thead class="table-dark">
            <tr>
                <th>SL No</th>
                <th>Product Name</th>
                <th>Supplier Name</th>
                <th>Buy Value</th>
                <th>Sell Value</th>
                <th>Quantity</th>
                <th>Total Profit</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($partStocks as $key => $partStock)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $partStock->product_name }}</td>
                    <td>{{ $partStock->supplier_name }}</td>
                    <td>{{ number_format($partStock->buy_value, 2) }}</td>
                    <td>{{ number_format($partStock->sell_value, 2) }}</td>
                    <td>{{ $partStock->quantity }}</td>
                    <td>{{ number_format($partStock->total_profit, 2) }}</td>
                    <td>
                        <a href="{{ route('admin.partstocks.edit', $partStock->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <a href="{{ route('admin.partstocks.show', $partStock->id) }}" class="btn btn-info btn-sm">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $partStocks->links() }}
</div>
@endsection
