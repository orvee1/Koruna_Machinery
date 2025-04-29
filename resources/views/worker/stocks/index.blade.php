@extends('layouts.app')

@section('title', 'Stock List')

@section('content')
<div class="container">
    <h1>Stock List</h1>

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

    <form method="GET" action="{{ route('worker.stocks.index') }}" class="mb-4">
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
                <a href="{{ route('worker.stocks.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i> Clear
                </a>
            </div>
        </div>
    </form>
    

    <!-- Search Form -->
    <form action="{{ route('worker.stocks.index') }}" method="GET" class="mb-4">
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
                <a href="{{ route('worker.stocks.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i> Clear
                </a>
            </div>
        </div>
    </form>

    <!-- Button to create new stock -->
    <a href="{{ route('worker.stocks.create') }}" class="btn btn-primary mb-3">Add New Stock</a>

    <!-- Stocks Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Supplier Name</th>
                <th>Quantity</th>
                <th>Total Amount</th>
                <th>Due Amount</th>
                <th>Purchase Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stocks as $key => $stock)
                <tr>
                    <td>{{ $key +1 }}</td>
                    <td>{{ $stock->product->name }}</td>
                    <td>{{ $stock->supplier_name }}</td>
                    <td>{{ $stock->quantity }}</td>
                    <td>{{ $stock->total_amount }}</td>
                    <td>{{ $stock->due_amount }}</td>
                    <td>{{ $stock->purchase_date }}</td>
                    <td>
                        <a href="{{ route('worker.stocks.show', $stock->id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('worker.stocks.edit', $stock->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('worker.stocks.destroy', $stock->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    {{ $stocks->links() }}
</div>
@endsection
