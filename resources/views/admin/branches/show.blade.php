@extends('layouts.app')

@section('title', 'Branch Details')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Branch: {{ $branch->name }}</h2>
        <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">← Back to Branches</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h5>Total Income (from Bills)</h5>
                    <p class="fs-4 text-success">{{ number_format($totalIncome, 2) }} ৳</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h5>Total Expenses</h5>
                    <p class="fs-4 text-danger">{{ number_format($totalExpense, 2) }} ৳</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h6>Product Purchase Expenses</h6>
                    <p class="fs-5 text-danger">{{ number_format($productExpense, 2) }} ৳</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h6>Part Stock Purchase Expenses</h6>
                    <p class="fs-5 text-danger">{{ number_format($partstockExpense, 2) }} ৳</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-12">
            <div class="card text-center bg-light">
                <div class="card-body">
                    <h4 class="text-dark">Net Profit</h4>
                    <h2 class="text-success">{{ number_format($profit, 2) }} ৳</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- List of Customers -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Branch Customers</h5>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>SL No</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>District</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branch->customers as $customer)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ $customer->district ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
