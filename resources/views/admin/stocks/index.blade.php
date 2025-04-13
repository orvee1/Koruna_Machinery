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

    <!-- Button to create new stock -->
    <a href="{{ route('admin.stocks.create') }}" class="btn btn-primary mb-3">Add New Stock</a>

    <!-- Stocks Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Supplier Name</th>
                <th>Quantity</th>
                <th>Total Amount</th>
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
                    <td>{{ $stock->purchase_date }}</td>
                    <td>
                        <a href="{{ route('admin.stocks.show', $stock->id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('admin.stocks.edit', $stock->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.stocks.destroy', $stock->id) }}" method="POST" style="display:inline;">
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
