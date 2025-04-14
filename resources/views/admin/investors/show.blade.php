@extends('layouts.app')

@section('title', 'Investor Details')

@section('content')
<div class="container">
    <h1>Investor Details</h1>

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

    <!-- Investor Details Table -->
    <table class="table table-bordered">
        <tr>
            <th>Name</th>
            <td>{{ $investor->name }}</td>
        </tr>
        <tr>
            <th>Total Investment</th>
            <td>{{ $investor->total_investment }}</td>
        </tr>
        <tr>
            <th>Balance</th>
            <td>{{ $investor->balance }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ $investor->status }}</td>
        </tr>
    </table>

    <!-- Investment History -->
    <h3>Investment History</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Amount</th>
                <th>Description</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($investor->investmentHistories as $history)
                <tr>
                    <td>{{ $history->amount }}</td>
                    <td>{{ $history->description }}</td>
                    <td>{{ $history->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Deposit History -->
    <h3>Deposit History</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Amount</th>
                <th>Description</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($investor->depositHistories as $history)
                <tr>
                    <td>{{ $history->amount }}</td>
                    <td>{{ $history->description }}</td>
                    <td>{{ $history->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
