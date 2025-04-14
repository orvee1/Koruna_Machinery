@extends('layouts.app')

@section('title', 'Investors')

@section('content')
<div class="container">
    <h1>Investors</h1>

    <!-- Success or error messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <a href="{{ route('admin.investors.create') }}" class="btn btn-primary mb-3">Add New Investor</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>SL No</th>
                <th>Name</th>
                <th>Total Investment</th>
                <th>Balance</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($investors as $key => $investor)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $investor->name }}</td>
                    <td>{{ $investor->total_investment }}</td>
                    <td>{{ $investor->balance }}</td>
                    <td>{{ $investor->status }}</td>
                    <td>
                        <a href="{{ route('admin.investors.show', $investor->id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('admin.investors.edit', $investor->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $investors->links() }}
</div>
@endsection
