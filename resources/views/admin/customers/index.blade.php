@extends('layouts.app')

@section('title', 'Customer List')

@section('content')
<div class="container">
    <h1>Customer List</h1>

    <!-- Display success message -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Button to create new customer -->
    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary mb-3">Add New Customer</a>

    <!-- Customers table -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>SL No</th>
                <th>Name</th>
                <th>Phone</th>
                <th>District</th>
                <th>Branch</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->district }}</td>
                    <td>{{ $customer->branch->name ?? 'No branch assigned' }}</td>
                    <td>
                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    {{ $customers->links() }}
</div>
@endsection
