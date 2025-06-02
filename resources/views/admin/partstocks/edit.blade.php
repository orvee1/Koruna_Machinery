@extends('layouts.app')

@section('title', 'Edit Part Stock')

@section('content')
<div class="container">
    <h1>Edit Part Stock</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.partstocks.update', $partStock) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="product_name">Product Name</label>
            <input type="text" id="product_name" name="product_name" class="form-control @error('product_name') is-invalid @enderror" value="{{ old('product_name', $partStock->product_name) }}" required>
            @error('product_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="supplier_name">Supplier Name</label>
            <input type="text" id="supplier_name" name="supplier_name" class="form-control @error('supplier_name') is-invalid @enderror" value="{{ old('supplier_name', $partStock->supplier_name) }}" required>
            @error('supplier_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="buying_price">Buying Price</label>
            <input type="number" step="0.01" id="buying_price" name="buying_price" class="form-control @error('buying_price') is-invalid @enderror" value="{{ old('buying_price', $partStock->buying_price) }}" required>
            @error('buying_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $partStock->quantity) }}" required>
            @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="sell_value">Sell Value</label>
            <input type="number" step="0.01" id="sell_value" name="sell_value" class="form-control @error('sell_value') is-invalid @enderror" value="{{ old('sell_value', $partStock->sell_value) }}" required>
            @error('sell_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="total_amount">Total Amount</label>
            <input type="number" id="total_amount" name="total_amount" class="form-control" value="{{ old('total_amount', $partStock->total_amount) }}" readonly>
        </div>

        <div class="form-group mb-3">
            <label for="total_profit">Total Profit</label>
            <input type="number" id="total_profit" name="total_profit" class="form-control" value="{{ old('total_profit', $partStock->total_profit) }}" readonly>
        </div>

        <div class="form-group mb-3">
            <label for="purchase_date">Purchase Date</label>
            <input type="date" id="purchase_date" name="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" value="{{ old('purchase_date', \Carbon\Carbon::parse($partStock->purchase_date)->format('Y-m-d')) }}" required>
            @error('purchase_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="deposit_amount">Deposit Amount</label>
            <input type="number" step="0.01" id="deposit_amount" name="deposit_amount" class="form-control @error('deposit_amount') is-invalid @enderror" value="{{ old('deposit_amount', $partStock->deposit_amount) }}">
            @error('deposit_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-group mb-3">
            <label>Branch</label>
            <input type="text" class="form-control" value="{{ $branch->name }}" readonly>
            <small class="text-muted">Branch cannot be changed</small>
        </div>

        <button type="submit" class="btn btn-success mt-3">Update Part Stock</button>
    </form>
</div>

<script>
    function calculateValues() {
        const buyingPrice = parseFloat(document.getElementById('buying_price').value) || 0;
        const quantity = parseInt(document.getElementById('quantity').value) || 0;
        const sellValue = parseFloat(document.getElementById('sell_value').value) || 0;

        const totalAmount = buyingPrice * quantity;
        const totalProfit = (sellValue - buyingPrice) * quantity;

        document.getElementById('total_amount').value = totalAmount.toFixed(2);
        document.getElementById('total_profit').value = totalProfit.toFixed(2);
    }

    document.getElementById('buying_price').addEventListener('input', calculateValues);
    document.getElementById('quantity').addEventListener('input', calculateValues);
    document.getElementById('sell_value').addEventListener('input', calculateValues);

    calculateValues();
</script>
@endsection
