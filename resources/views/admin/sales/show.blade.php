@extends('layouts.app')

@section('title', 'Sale Details')

@section('content')
<div class="container">
    <h1>Sale Details</h1>

    <table class="table table-bordered">
        <tr>
            <th>Product</th>
            <td>{{ $sale->product->name }}</td>
        </tr>
        <tr>
            <th>Customer</th>
            <td>{{ $sale->customer->name }}</td>
        </tr>
        <tr>
            <th>Quantity</th>
            <td>{{ $sale->quantity }}</td>
        </tr>
        <tr>
            <th>Total Amount</th>
            <td>{{ $sale->total_amount }}</td>
        </tr>
        <tr>
            <th>Paid Amount</th>
            <td>{{ $sale->paid_amount }}</td>
        </tr>
        <tr>
            <th>Due Amount</th>
            <td>{{ $sale->due_amount }}</td>
        </tr>
        <tr>
            <th>Payment Status</th>
            <td>{{ $sale->payment_status }}</td>
        </tr>
    </table>

    <a href="{{ route('admin.sales.index') }}" class="btn btn-primary">Back to Sales List</a>
</div>
@endsection
