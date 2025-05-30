@extends('layouts.app')

@section('title', 'Create Part Stock')

@section('content')
<div class="container">
    <h1>Add New Part Stock</h1>

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

    <form action="{{ route('admin.partstocks.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="product_name">Product Name</label>
            <input type="text" id="product_name" name="product_name" class="form-control @error('product_name') is-invalid @enderror" value="{{ old('product_name') }}" required>
            @error('product_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="supplier_name">Supplier Name</label>
            <input type="text" id="supplier_name" name="supplier_name" class="form-control @error('supplier_name') is-invalid @enderror" value="{{ old('supplier_name') }}" required>
            @error('supplier_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="buying_price">Buying Price</label>
            <input type="number" step="0.01" id="buying_price" name="buying_price" class="form-control @error('buying_price') is-invalid @enderror" value="{{ old('buying_price') }}" required>
            @error('buying_price')
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
            <label for="deposit_amount">Deposit Amount</label>
            <input type="number" step="0.01" id="deposit_amount" name="deposit_amount" class="form-control @error('deposit_amount') is-invalid @enderror" value="{{ old('deposit_amount') }}" required>
            @error('deposit_amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="sell_value">Sell Value</label>
            <input type="number" step="0.01" id="sell_value" name="sell_value" class="form-control @error('sell_value') is-invalid @enderror" value="{{ old('sell_value') }}" required>
            @error('sell_value')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

         <div class="mb-3">
            <label for="purchase_date" class="form-label">Purchase Date</label>
            <input type="date" id="purchase_date" name="purchase_date" class="form-control" value="{{ now()->toDateString() }}" required>
        </div>

        <button type="submit" class="btn btn-success mt-3">✔️Save Part Stock</button>
    </form>
</div>
@endsection
