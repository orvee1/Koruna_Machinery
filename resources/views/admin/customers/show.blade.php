@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="container">
    <h1>Customer Details</h1>

    <div class="card mb-3">
        <div class="card-header">
            <strong>{{ $customer->name }}</strong>
        </div>
        <div class="card-body">
            <h5 class="card-title">Customer Information</h5>
            <p><strong>Phone:</strong> {{ $customer->phone }}</p>
            <p><strong>District:</strong> {{ $customer->district }}</p>
            <p><strong>Customer ID:</strong> {{ $customer->customer_id }}</p>

            <h5 class="mt-3">Branch Information</h5>
            <p><strong>Branch Name:</strong> {{ $customer->branch->name ?? 'No branch assigned' }}</p>
            <p><strong>Branch Code:</strong> {{ $customer->branch->code ?? 'No branch code' }}</p>
        </div>
    </div>

    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Back to Customer List</a>
</div>
@endsection
