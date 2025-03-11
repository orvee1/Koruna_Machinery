@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="card shadow">
    <div class="card-header bg-dark text-white">
        <h3>Admin Dashboard</h3>
    </div>
    <div class="card-body">
        <p>Welcome, <strong>{{ auth()->user()->name }}</strong>! You have full access.</p>
        
        <a href="{{ route('admin.users') }}" class="btn btn-primary">Manage Workers</a>
    </div>
</div>
@endsection
