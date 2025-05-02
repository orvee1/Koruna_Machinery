@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">

    {{-- 🔔 Welcome Note --}}
    <div class="alert alert-primary d-flex justify-content-between align-items-center">
        <div>
            👋 স্বাগতম, <strong>{{ auth()->user()->name }}</strong>!

            @if(auth()->user()->role === 'admin')
                — আপনি এখন <strong>
                    {{ session('selected_branch_name') ?? 'কোনো ব্রাঞ্চ সিলেক্ট করা হয়নি' }}
                </strong> ব্রাঞ্চে আছেন।
            @endif
        </div>
    </div>

    <h1 class="mb-4">Admin Dashboard</h1>

    <div class="row">
        <div class="col-md-3">
            <div class="dashboard-card">
                <h5>Total Customers</h5>
                <p>{{ $totalCustomers }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <h5>Total Products</h5>
                <p>{{ $totalProducts }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <h5>Total Sales</h5>
                <p>{{ number_format($totalSales, 2) }} ৳</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <h5>Total Investors</h5>
                <p>{{ $totalInvestors }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
