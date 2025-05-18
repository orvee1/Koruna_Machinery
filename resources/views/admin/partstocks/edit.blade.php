@extends('layouts.app')

@section('title', 'Edit Part Stock')

@section('content')
<div class="container">
    <h1>Edit Part Stock</h1>

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

    <form action="{{ route('admin.partstocks.update', $partStock->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="product_name">Product Name</label>
            <input type="text" id="product_name" name="product_name" class="form-control @error('product_name') is-invalid @enderror" value="{{ old('product_name', $partStock->product_name) }}" required>
            @error('product_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="supplier_name">Supplier Name</label>
            <input type="text" id="supplier_name" name="supplier_name" class="form-control @error('supplier_name') is-invalid @enderror" value="{{ old('supplier_name', $partStock->supplier_name) }}" required>
            @error('supplier_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="buying_price">Buying Price</label>
            <input type="number" step="0.01" id="buying_price" name="buying_price" class="form-control @error('buying_price') is-invalid @enderror" value="{{ old('buying_price', $partStock->buying_price) }}" required>
            @error('buying_price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $partStock->quantity) }}" required>
            @error('quantity')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="sell_value">Sell Value</label>
            <input type="number" step="0.01" id="sell_value" name="sell_value" class="form-control @error('sell_value') is-invalid @enderror" value="{{ old('sell_value', $partStock->sell_value) }}" required>
            @error('sell_value')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="total_amount">Total Amount</label>
            <input type="number" id="total_amount" name="total_amount" class="form-control @error('total_amount') is-invalid @enderror" value="{{ old('total_amount', $partStock->total_amount) }}" readonly>
            @error('total_amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="total_profit">Total Profit</label>
            <input type="number" id="total_profit" name="total_profit" class="form-control @error('total_profit') is-invalid @enderror" value="{{ old('total_profit', $partStock->total_profit) }}" readonly>
            @error('total_profit')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="branch_id">Branch</label>
            <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" @if($partStock->branch_id == $branch->id) selected @endif>{{ $branch->name }}</option>
                @endforeach
            </select>
            @error('branch_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success mt-3">Update Part Stock</button>
    </form>
</div>

<script>
    // Calculate Amount and Total Profit on the fly when 'buying_price', 'quantity', or 'sell_value' changes
    document.getElementById('buying_price').addEventListener('input', calculateValues);
    document.getElementById('quantity').addEventListener('input', calculateValues);
    document.getElementById('sell_value').addEventListener('input', calculateValues);

    function calculateValues() {
        let buyValue = parseFloat(document.getElementById('buying_price').value) || 0;
        let quantity = parseInt(document.getElementById('quantity').value) || 0;
        let sellValue = parseFloat(document.getElementById('sell_value').value) || 0;

        let amount = buyValue * quantity;
        let totalProfit = (sellValue - buyValue) * quantity;

        // Set values in the readonly fields
        document.getElementById('amount').value = amount.toFixed(2);
        document.getElementById('total_profit').value = totalProfit.toFixed(2);
    }

    // Initial calculation when page loads
    calculateValues();
</script>

@endsection
