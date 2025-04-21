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
            <label for="buy_value">Buy Value</label>
            <input type="number" step="0.01" id="buy_value" name="buy_value" class="form-control @error('buy_value') is-invalid @enderror" value="{{ old('buy_value') }}" required>
            @error('buy_value')
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
            <label for="sell_value">Sell Value</label>
            <input type="number" step="0.01" id="sell_value" name="sell_value" class="form-control @error('sell_value') is-invalid @enderror" value="{{ old('sell_value') }}" required>
            @error('sell_value')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="branch_id">Branch</label>
            <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" @if(old('branch_id') == $branch->id) selected @endif>{{ $branch->name }}</option>
                @endforeach
            </select>
            @error('branch_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success mt-3">Save Part Stock</button>
    </form>
</div>
@endsection
@section('scripts')