@extends('layouts.app')

@section('title', 'Worker Dashboard')

@section('content')
<div class="card shadow">
    <div class="card-header bg-dark text-white">
        <h3>Worker Dashboard</h3>
    </div>
    <div class="card-body">
        <p>Welcome, <strong>{{ auth()->user()->name }}</strong>! You have limited access.</p>

        <a href="{{ route('worker.sales') }}" class="btn btn-success">View Sales History</a>
    </div>
</div>
@endsection
