@extends('layouts.app')

@section('title', 'Investment Histories')

@section('content')
<div class="container">
    <h1>Investment Histories</h1>

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

    <a href="{{ route('admin.investmentHistories.create') }}" class="btn btn-primary mb-3">Add New Investment History</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>SL No</th>
                <th>Investor Name</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Buying Price</th>
                <th>Total Cost</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($investmentHistories as $key => $investmentHistory)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $investmentHistory->investor->name }}</td>
                    <td>{{ $investmentHistory->product->name }}</td>
                    <td>{{ $investmentHistory->quantity }}</td>
                    <td>{{ $investmentHistory->buying_price }}</td>
                    <td>{{ $investmentHistory->total_cost }}</td>
                    <td>
                        <a href="{{ route('admin.investmentHistories.edit', $investmentHistory->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <a href="{{ route('admin.investmentHistories.show', $investmentHistory->id) }}" class="btn btn-info btn-sm">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $investmentHistories->links() }}
</div>
@endsection
