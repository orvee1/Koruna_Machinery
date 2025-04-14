@extends('layouts.app')

@section('title', 'Deposit History Details')

@section('content')
<div class="container">
    <h1>Deposit History Details</h1>

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

    <!-- Deposit History Details Table -->
    <table class="table table-bordered">
        <tr>
            <th>Investor</th>
            <td>{{ $depositHistory->investor->name }}</td>
        </tr>
        <tr>
            <th>Amount</th>
            <td>{{ $depositHistory->amount }}</td>
        </tr>
        <tr>
            <th>Payment Method</th>
            <td>{{ ucfirst($depositHistory->payment_method) }}</td>
        </tr>
        <tr>
            <th>Payment Date</th>
            <td>{{ $depositHistory->payment_date }}</td>
        </tr>
    </table>

    <!-- Back Button -->
    <a href="{{ route('admin.depositHistories.index') }}" class="btn btn-primary">Back to Deposit Histories</a>
</div>
@endsection
