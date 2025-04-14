@extends('layouts.app')

@section('title', 'Deposit Histories')

@section('content')
<div class="container">
    <h1>Deposit Histories</h1>

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

    <!-- Add New Deposit History Button -->
    <a href="{{ route('admin.depositHistories.create') }}" class="btn btn-primary mb-3">Add New Deposit History</a>

    <!-- Table to display deposit histories -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>SL No</th>
                <th>Investor</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Payment Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($depositHistories as $key => $depositHistory)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $depositHistory->investor->name }}</td>
                    <td>{{ $depositHistory->amount }}</td>
                    <td>{{ ucfirst($depositHistory->payment_method) }}</td>
                    <td>{{ $depositHistory->payment_date }}</td>
                    <td>
                        <a href="{{ route('admin.depositHistories.show', $depositHistory->id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('admin.depositHistories.edit', $depositHistory->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination Links -->
    {{ $depositHistories->links() }}
</div>
@endsection
