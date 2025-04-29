@extends('layouts.app')

@section('title', 'Add Part Stock Sale')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Add Part Stock Sale</h2>
        <a href="{{ route('admin.partstock-sales.index') }}" class="btn btn-secondary">Back to List</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.partstock-sale.store') }}" method="POST">
        @csrf
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="part_stock_id" class="form-label">Select Part Stock</label>
                <select name="part_stock_id" id="part_stock_id" class="form-select" required>
                    <option value="">-- Select Part Stock --</option>
                    @foreach($partStocks as $partStock)
                        <option value="{{ $partStock->id }}" {{ old('part_stock_id') == $partStock->id ? 'selected' : '' }}>
                            {{ $partStock->product_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="customer_id" class="form-label">Select Customer</label>
                <select name="customer_id" id="customer_id" class="form-select" required>
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="{{ old('quantity') }}" required>
            </div>

            <div class="col-md-4">
                <label for="unit_price" class="form-label">Unit Price</label>
                <input type="number" step="0.01" name="unit_price" id="unit_price" class="form-control" value="{{ old('unit_price') }}" required>
            </div>

            <div class="col-md-4">
                <label for="paid_amount" class="form-label">Paid Amount</label>
                <input type="number" step="0.01" name="paid_amount" id="paid_amount" class="form-control" value="{{ old('paid_amount') }}" required>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Save Sale</button>
        </div>
    </form>
</div>
@endsection
