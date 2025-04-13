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

    <!-- Button to create new product -->
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3">Add New Product</a>

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
                        <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-info btn-sm me-2">View</a>
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        {{-- <form action="{{ route('admin.products.adjustStock', $product->id) }}" method="POST">
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
