@extends('layouts.app')

@section('title', 'Part Stock Details')

@section('content')
<div class="container">
    <h1>Part Stock Details</h1>

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

    <!-- Part Stock Details Table -->
    <table class="table table-bordered">
        <tr>
            <th>Product Name</th>
            <td>{{ $partStock->product_name }}</td>
        </tr>
        <tr>
            <th>Buy Value</th>
            <td>{{ $partStock->buy_value }}</td>
        </tr>
        <tr>
            <th>Sell Value</th>
            <td>{{ $partStock->sell_value }}</td>
        </tr>
        <tr>
            <th>Quantity</th>
            <td>{{ $partStock->quantity }}</td>
        </tr>
        <tr>
            <th>Amount</th>
            <td>{{ $partStock->amount }}</td>
        </tr>
        <tr>
            <th>Total Profit</th>
            <td>{{ $partStock->total_profit }}</td>
        </tr>
        <tr>
            <th>Branch</th>
            <td>{{ $partStock->branch->name ?? 'No branch assigned' }}</td>
        </tr>
    </table>

    <!-- Back Button -->
    <a href="{{ route('admin.partstocks.index') }}" class="btn btn-primary">Back to Part Stocks</a>
</div>
@endsection
