@extends('layouts.app')

@section('title', 'Add Part Stock Sale')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">🛒 Add Part Stock Sale</h2>
        <a href="{{ route('admin.partstock-sales.index') }}" class="btn btn-outline-secondary">
            ← Back to Sales List
        </a>
    </div>

    {{-- Show Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>There were some problems with your input:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.partstock-sales.store') }}" method="POST" class="card shadow-sm p-4">
        @csrf

        <div class="row g-4">
            {{-- Part Stock Dropdown --}}
            <div class="col-md-6">
                <label for="part_stock_id" class="form-label">🧩 Select Part Stock <span class="text-danger">*</span></label>
                <select name="part_stock_id" id="part_stock_id" class="form-select" required>
                    <option value="">-- Choose a Part --</option>
                    @foreach($partStocks as $partStock)
                        <option value="{{ $partStock->id }}" {{ old('part_stock_id') == $partStock->id ? 'selected' : '' }}>
                            {{ $partStock->product_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Customer Dropdown --}}
            <div class="col-md-6">
                <label for="customer_id" class="form-label">👤 Select Customer <span class="text-danger">*</span></label>
                <select name="customer_id" id="customer_id" class="form-select" required>
                    <option value="">-- Choose a Customer --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }} — {{ $customer->phone }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Quantity --}}
            <div class="col-md-4">
                <label for="quantity" class="form-label">📦 Quantity <span class="text-danger">*</span></label>
                <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="{{ old('quantity') }}" required>
            </div>

            {{-- Unit Price --}}
            <div class="col-md-4">
                <label for="unit_price" class="form-label">💰 Unit Price <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="unit_price" id="unit_price" class="form-control" value="{{ old('unit_price') }}" required>
            </div>

            {{-- Paid Amount --}}
            <div class="col-md-4">
                <label for="paid_amount" class="form-label">💵 Paid Amount</label>
                <input type="number" step="0.01" name="paid_amount" id="paid_amount" class="form-control" value="{{ old('paid_amount') }}">
            </div>
        </div>

        {{-- Submit --}}
        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary">
                ✅ Save Sale
            </button>
        </div>
    </form>
</div>
@endsection
