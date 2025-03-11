@extends('layouts.app')

@section('title', 'Homepage')

@section('content')
<div class="container mt-4">
    <h1 class="text-center">Welcome to the POS Management System</h1>

    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total Products</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $totalProducts }}</h5>
                    <p class="card-text">Total products available in the system.</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Sales</div>
                <div class="card-body">
                    <h5 class="card-title">${{ number_format($totalSales, 2) }}</h5>
                    <p class="card-text">Total sales amount generated.</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Total Customers</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $totalCustomers }}</h5>
                    <p class="card-text">Total registered customers.</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Total Stock</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $totalStock }}</h5>
                    <p class="card-text">Total quantity of stock available.</p>
                </div>
            </div>
        </div>
    </div>

    <h3>Branches</h3>
    <ul class="list-group mt-4">
        @foreach($branches as $branch)
            <li class="list-group-item">
                {{ $branch->name }} - Code: {{ $branch->code }}
            </li>
        @endforeach
    </ul>
</div>
@endsection
