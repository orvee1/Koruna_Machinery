@extends('layouts.app')

@section('title', 'Create Sale')

@section('content')
<div class="container">
    <h1>Create New Sale</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.sales.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="product_id">Product</label>
            <select name="product_id" id="product_id" class="form-control @error('product_id') is-invalid @enderror">
                @foreach($products as $product)
                    <option value="{{ $product->id }}" @if(old('product_id') == $product->id) selected @endif>{{ $product->name }}</option>
                @endforeach
            </select>
            @error('product_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="customer_id">Customer</label>
            <select name="customer_id" id="customer_id" class="form-control @error('customer_id') is-invalid @enderror">
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" @if(old('customer_id') == $customer->id) selected @endif>{{ $customer->name }}</option>
                @endforeach
            </select>
            @error('customer_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" required>
            @error('quantity')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="total_amount">Total Amount</label>
            <input type="number" id="total_amount" name="total_amount" class="form-control @error('total_amount') is-invalid @enderror" value="{{ old('total_amount') }}" required>
            @error('total_amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="paid_amount">Paid Amount</label>
            <input type="number" id="paid_amount" name="paid_amount" class="form-control @error('paid_amount') is-invalid @enderror" value="{{ old('paid_amount') }}" required>
            @error('paid_amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="due_amount">Due Amount</label>
            <input type="number" id="due_amount" name="due_amount" class="form-control @error('due_amount') is-invalid @enderror" value="{{ old('due_amount') }}" required>
            @error('due_amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="payment_status">Payment Status</label>
            <select name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror">
                <option value="paid" @if(old('payment_status') == 'paid') selected @endif>Paid</option>
                <option value="pending" @if(old('payment_status') == 'pending') selected @endif>Pending</option>
            </select>
            @error('payment_status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="investor_id">Investor (Optional)</label>
            <select name="investor_id" id="investor_id" class="form-control @error('investor_id') is-invalid @enderror">
                <option value="">Select Investor</option>
                @foreach($investors as $investor)
                    <option value="{{ $investor->id }}" @if(old('investor_id') == $investor->id) selected @endif>{{ $investor->name }}</option>
                @endforeach
            </select>
            @error('investor_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="branch_id">Branch</label>
            <select name="branch_id" id="branch_id" class="form-control @error('branch_id') is-invalid @enderror">
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" @if(old('branch_id') == $branch->id) selected @endif>{{ $branch->name }}</option>
                @endforeach
            </select>
            @error('branch_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="part_stock_id">Part Stock</label>
            <select name="part_stock_id" id="part_stock_id" class="form-control @error('part_stock_id') is-invalid @enderror">
                @foreach($products as $product)
                    <option value="{{ $product->partStock->id }}" @if(old('part_stock_id') == $product->partStock->id) selected @endif>{{ $product->name }}</option>
                @endforeach
            </select>
            @error('part_stock_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success mt-3">Save Sale</button>
    </form>
</div>
@endsection
