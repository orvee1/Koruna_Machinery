@extends('layouts.app')

@section('title', 'Stock Details')

@section('content')
<div class="container">
    <h1>Stock Details</h1>

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

    <!-- Stock Details Table -->
    <table class="table table-bordered">
        <tr>
            <th>Product</th>
            <td>{{ $stock->product->name }}</td>
        </tr>
        <tr>
            <th>Supplier</th>
            <td>{{ $stock->supplier_name }}</td>
        </tr>
        <tr>
            <th>Quantity</th>
            <td>{{ $stock->quantity }}</td>
        </tr>
        <tr>
            <th>Total Amount</th>
            <td>{{ $stock->total_amount }}</td>
        </tr>
        <tr>
            <th>Purchase Date</th>
            <td>{{ $stock->purchase_date }}</td>
        </tr>
        <tr>
            <th>Branch</th>
            <td>{{ $stock->branch->name ?? 'No branch assigned' }}</td>
        </tr>
    </table>

    <a href="{{ route('manager.stocks.index') }}" class="btn btn-primary">Back to Stocks</a>
</div>
@endsection
