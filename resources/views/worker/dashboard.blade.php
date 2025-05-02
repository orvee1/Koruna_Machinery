@extends('layouts.app')

@section('title', 'Worker Dashboard')

@section('content')
<div class="container">
    <div class="alert alert-primary d-flex justify-content-between align-items-center">
        <div>
            👋 স্বাগতম, <strong>{{ auth()->user()->name }}</strong>!
        </div>
    </div>
    <h1 class="mb-4">Worker Dashboard</h1>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
                    <p class="card-text fs-4">{{ $totalProducts }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Stocks</h5>
                    <p class="card-text fs-4">{{ $totalStocks }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Customers</h5>
                    <p class="card-text fs-4">{{ $totalCustomers }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Sales</h5>
                    <p class="card-text fs-4">{{ number_format($totalSales, 2) }} TK</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
