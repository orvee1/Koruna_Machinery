@extends('layouts.app')

@section('title', 'Products List')

@section('content')
<div class="container">
    <h1>Products List</h1>

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

    <form method="GET" action="{{ route('worker.products.index') }}" class="mb-4">
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
                <a href="{{ route('worker.products.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i> Clear
                </a>
            </div>
        </div>
    </form>
    

    <!-- Search Form -->
    <form action="{{ route('worker.products.index') }}" method="GET" class="mb-4">
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
                <a href="{{ route('worker.products.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i> Clear
                </a>
            </div>
        </div>
    </form>

    <!-- Button to create new product -->
    <a href="{{ route('worker.products.create') }}" class="btn btn-primary mb-3">Add New Product</a>

    <!-- Products table -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Buying Price</th>
                <th>Selling Price</th>
                <th>Stock Quantity</th>
                <th>Branch</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $key => $product)
                <tr>
                    <td>{{ $key +1  }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->buying_price }}</td>
                    <td>{{ $product->selling_price }}</td>
                    <td>{{ $product->stock_quantity }}</td>
                    <td>{{ $product->branch->name ?? 'No branch assigned' }}</td>
                    <td class="d-flex">
                        <a href="{{ route('worker.products.show', $product->id) }}" class="btn btn-info btn-sm me-2">View</a>
                        <a href="{{ route('worker.products.edit', $product->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        {{-- <form action="{{ route('worker.products.adjustStock', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm me-2">Adjust Stock</button>
                        </form> --}}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    {{ $products->links() }}
</div>
@endsection
