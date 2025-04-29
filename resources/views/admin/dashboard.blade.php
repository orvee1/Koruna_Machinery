@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <h1 class="mb-4">Branch Dashboard</h1>

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
