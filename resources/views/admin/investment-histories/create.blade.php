@extends('layouts.app')

@section('title', 'Create Investment History')

@section('content')
<div class="container">
    <h1>Add New Investment History</h1>

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

    <form action="{{ route('admin.investment-histories.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="investor_id">Investor</label>
            <select id="investor_id" name="investor_id" class="form-control @error('investor_id') is-invalid @enderror" required>
                @foreach($investors as $investor)
                    <option value="{{ $investor->id }}" @if(old('investor_id') == $investor->id) selected @endif>{{ $investor->name }}</option>
                @endforeach
            </select>
            @error('investor_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="product_id">Product</label>
            <select id="product_id" name="product_id" class="form-control @error('product_id') is-invalid @enderror" required>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" @if(old('product_id') == $product->id) selected @endif>{{ $product->name }}</option>
                @endforeach
            </select>
            @error('product_id')
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
            <label for="buying_price">Buying Price</label>
            <input type="number" id="buying_price" name="buying_price" class="form-control @error('buying_price') is-invalid @enderror" value="{{ old('buying_price') }}" required>
            @error('buying_price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="total_cost">Total Cost</label>
            <input type="number" id="total_cost" name="total_cost" class="form-control @error('total_cost') is-invalid @enderror" value="{{ old('total_cost') }}" required>
            @error('total_cost')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success mt-3">Save Investment History</button>
    </form>
</div>
@endsection
