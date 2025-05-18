@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">

    {{-- 🔔 Welcome Note --}}
    <div class="alert alert-primary d-flex justify-content-between align-items-center mb-4">
        <div>
            👋 স্বাগতম, <strong>{{ auth()->user()->name }}</strong>!
            @if(auth()->user()->role === 'admin')
                — আপনি এখন <strong>{{ session('active_branch_name') ?? 'কোনো ব্রাঞ্চ সিলেক্ট করা হয়নি' }}</strong> ব্রাঞ্চে আছেন।
            @endif
        </div>
    </div>

    <h1 class="mb-4 text-center">Admin Dashboard</h1>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm rounded-lg p-4 text-center h-100">
                <div class="card-body">
                    <h5 class="card-title text-primary">Revenue</h5>
                    <p class="card-text fs-4 text-success">{{ number_format($totalSales, 2) }} ৳</p>
                    <div class="card-footer text-muted">Total sales revenue</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card shadow-sm rounded-lg p-4 text-center h-100">
                <div class="card-body">
                    <h5 class="card-title text-primary">Total Value</h5>
                    <p class="card-text fs-4 text-info">{{ number_format($totalProductValue, 2) }} ৳</p>
                    <div class="card-footer text-muted">Total product value</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card shadow-sm rounded-lg p-4 text-center h-100">
                <div class="card-body">
                    <h5 class="card-title text-primary">Total Profit</h5>
                    <p class="card-text fs-4 text-warning">{{ number_format($totalProfit, 2) }} ৳</p>
                    <div class="card-footer text-muted">Profit from sales</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card shadow-sm rounded-lg p-4 text-center h-100">
                <div class="card-body">
                    <h5 class="card-title text-primary">Total Due</h5>
                    <p class="card-text fs-4 text-danger">{{ number_format($totalDue, 2) }} ৳</p>
                    <div class="card-footer text-muted">Dues to be paid</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card shadow-sm rounded-lg p-4 text-center h-100">
                <div class="card-body">
                    <h5 class="card-title text-primary">Total Due To Have</h5>
                    <p class="card-text fs-4 text-danger">{{ number_format($totalDueToHave, 2) }} ৳</p>
                    <div class="card-footer text-muted">Dues to be paid By Customer</div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
