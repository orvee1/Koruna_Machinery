@extends('layouts.app')

@section('title', 'Investment History Details')

@section('content')
<div class="container">
    <h1>Investment History Details</h1>

    <table class="table table-bordered">
        <tr>
            <th>Investor</th>
            <td>{{ $investmentHistory->investor->name }}</td>
        </tr>
        <tr>
            <th>Product</th>
            <td>{{ $investmentHistory->product->name }}</td>
        </tr>
        <tr>
            <th>Quantity</th>
            <td>{{ $investmentHistory->quantity }}</td>
        </tr>
        <tr>
            <th>Buying Price</th>
            <td>{{ $investmentHistory->buying_price }}</td>
        </tr>
        <tr>
            <th>Total Cost</th>
            <td>{{ $investmentHistory->total_cost }}</td>
        </tr>
    </table>

    <a href="{{ route('admin.investment-histories.index') }}" class="btn btn-primary">Back to Investment Histories</a>
</div>
@endsection
