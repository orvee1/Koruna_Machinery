@extends('layouts.app')

@section('title', 'Add New Stock')

@section('content')
<div class="container">
    <h1 class="mb-4">Add New Stock</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.stocks.store') }}" method="POST">
        @csrf

        {{-- Product Name --}}
        <div class="mb-3">
            <label for="product_name" class="form-label">Product Name</label>
            <input 
                type="text" 
                id="product_name" 
                name="product_name" 
                class="form-control @error('product_name') is-invalid @enderror" 
                value="{{ old('product_name') }}" 
                required
            >
            @error('product_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Supplier Name --}}
        <div class="mb-3">
            <label for="supplier_name" class="form-label">Supplier Name</label>
            <input 
                type="text" 
                id="supplier_name" 
                name="supplier_name" 
                class="form-control @error('supplier_name') is-invalid @enderror" 
                value="{{ old('supplier_name') }}" 
                required
            >
            @error('supplier_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Buying & Selling Prices --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="buying_price" class="form-label">Buying Price (৳)</label>
                <input 
                    type="number" 
                    id="buying_price" 
                    name="buying_price" 
                    class="form-control @error('buying_price') is-invalid @enderror" 
                    value="{{ old('buying_price') }}" 
                    required 
                    step="0.01"
                    min="0"
                >
                @error('buying_price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="selling_price" class="form-label">Selling Price (৳)</label>
                <input 
                    type="number" 
                    id="selling_price" 
                    name="selling_price" 
                    class="form-control @error('selling_price') is-invalid @enderror" 
                    value="{{ old('selling_price') }}" 
                    required 
                    step="0.01"
                    min="0"
                >
                @error('selling_price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Quantity & Deposit --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input 
                    type="number" 
                    id="quantity" 
                    name="quantity" 
                    class="form-control @error('quantity') is-invalid @enderror" 
                    value="{{ old('quantity') }}" 
                    required
                    min="1"
                >
                @error('quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="deposit_amount" class="form-label">Deposit Amount (৳)</label>
                <input 
                    type="number" 
                    id="deposit_amount" 
                    name="deposit_amount" 
                    class="form-control @error('deposit_amount') is-invalid @enderror" 
                    value="{{ old('deposit_amount', 0) }}" 
                    step="0.01" 
                    min="0"
                >
                @error('deposit_amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Purchase Date --}}
        <div class="mb-3">
            <label for="purchase_date" class="form-label">Purchase Date</label>
            <input 
                type="date" 
                id="purchase_date" 
                name="purchase_date" 
                class="form-control @error('purchase_date') is-invalid @enderror"
                value="{{ old('purchase_date', now()->toDateString()) }}"
                required
            >
            @error('purchase_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Add Stock</button>
    </form>
</div>
@endsection
