@extends('layouts.app')

@section('title', 'Sales List')

@section('content')
<div class="container">
    <h1>Sales List</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <a href="{{ route('admin.sales.create') }}" class="btn btn-primary mb-3">Add New Sale</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>SL No</th>
                <th>Product Name</th>
                <th>Customer Name</th>
                <th>Total Amount</th>
                <th>Paid Amount</th>
                <th>Due Amount</th>
                <th>Payment Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $key => $sale)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $sale->product->name }}</td>
                    <td>{{ $sale->customer->name }}</td>
                    <td>{{ $sale->total_amount }}</td>
                    <td>{{ $sale->paid_amount }}</td>
                    <td>{{ $sale->due_amount }}</td>
                    <td>{{ $sale->payment_status }}</td>
                    <td>
                        <a href="{{ route('admin.sales.edit', $sale->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <a href="{{ route('admin.sales.show', $sale->id) }}" class="btn btn-info btn-sm">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $sales->links() }}
</div>
@endsection
