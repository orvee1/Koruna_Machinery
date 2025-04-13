@extends('layouts.app')

@section('title', 'Part Stocks')

@section('content')
<div class="container">
    <h1>Part Stocks</h1>

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

    <a href="{{ route('admin.partstocks.create') }}" class="btn btn-primary mb-3">Add New Part Stock</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>SL No</th>
                <th>Product Name</th>
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
                    <td>{{ $partStock->buy_value }}</td>
                    <td>{{ $partStock->sell_value }}</td>
                    <td>{{ $partStock->quantity }}</td>
                    <td>{{ $partStock->total_profit }}</td>
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
