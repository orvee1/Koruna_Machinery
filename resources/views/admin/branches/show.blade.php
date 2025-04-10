@extends('layouts.app')

@section('title', 'Branch Details')

@section('content')
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h4>{{ $branch->name }} Branch Details</h4>
        </div>
        <div class="card-body">
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

            <!-- Branch Info -->
            <div class="mb-4">
                <h5 class="text-primary">Branch Info</h5>
                <p><strong>Branch Name: <span class="text-muted">{{ $branch->name }}</span></strong></p>
                {{-- <p><strong>Branch Code:</strong> {{ $branch->code }}</p> --}}
            </div>

            <!-- Customers in this branch -->
            <div class="mb-4">
                <h5 class="text-primary">Customers in this Branch</h5>
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>SL No</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Customer ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $key => $customer)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>{{ $customer->customer_id }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Products in this branch -->
            <div class="mb-4">
                <h5 class="text-primary">Products in this Branch</h5>
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>SL No</th>
                            <th>Product Name</th>
                            <th>Buying Price</th>
                            <th>Selling Price</th>
                            <th>Stock Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $key => $product)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->buying_price }}</td>
                                <td>{{ $product->selling_price }}</td>
                                <td>{{ $product->stock_quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Total Income from Sales -->
            <div class="mb-4">
                <h5 class="text-primary">Total Income from Sales</h5>
                <p><strong>Total Income:</strong> <span class="badge bg-success">{{ $totalIncome }} ৳</span></p>
            </div>

            <!-- Total Expense -->
            <div class="mb-4">
                <h5 class="text-primary">Total Expense (Part Stocks)</h5>
                <p><strong>Total Expense:</strong> <span class="badge bg-danger">{{ $totalExpense }} ৳</span></p>
            </div>

            <!-- Profit -->
            <div class="mb-4">
                <h5 class="text-primary">Profit</h5>
                <p><strong>Profit:</strong> <span class="badge bg-warning text-dark">{{ $profit }} ৳</span></p>
            </div>

            <!-- Sales in this branch -->
            <div class="mb-4">
                <h5 class="text-primary">Sales in this Branch</h5>
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Quantity Sold</th>
                            <th>Total Amount</th>
                            <th>Payment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                            <tr>
                                <td>{{ $sale->id }}</td>
                                <td>{{ $sale->product->name }}</td>
                                <td>{{ $sale->quantity }}</td>
                                <td>{{ $sale->total_amount }}</td>
                                <td>
                                    @if($sale->payment_status == 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-danger">Due</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
