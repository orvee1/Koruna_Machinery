@extends('layouts.app')
@section('title', 'Customer View')

@section('content')
<div class="container">
    <h3 class="mb-4">🧾 Customer History: {{ $customer->name }}</h3>

    <p><strong>Phone:</strong> {{ $customer->phone }}</p>
    <p><strong>District:</strong> {{ $customer->district ?? 'N/A' }}</p>

    @php
        $grandTotal = 0;
        $grandPaid = 0;
        $grandDue = 0;
    @endphp

    @forelse($bills as $bill)
        @php
            $billTotal = $bill->total_amount;
            $billPaid = $bill->paid_amount;
            $billDue = $bill->due_amount;

            $grandTotal += $billTotal;
            $grandPaid += $billPaid;
            $grandDue += $billDue;
        @endphp

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <span>📄 <strong>Bill #{{ $bill->id }}</strong> | {{ $bill->created_at->format('d M Y') }}</span>
                <span><strong>Seller:</strong> {{ $bill->seller->name ?? 'N/A' }}</span>
            </div>

            <div class="card-body p-0">
                <table class="table table-bordered text-center m-0">
                    <thead class="table-light">
                        <tr>
                            <th>SL No</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                   @php $serial = 1; @endphp
                    <tbody>
                        @foreach($bill->productSales as $sale)
                            <tr>
                                <td>{{ $serial++ }}</td>
                                <td>{{ $sale->stock->product_name ?? 'N/A' }}</td>
                                <td>{{ $sale->quantity }}</td>
                                <td>{{ number_format($sale->unit_price, 2) }}</td>
                                <td>{{ number_format($sale->quantity * $sale->unit_price, 2) }}</td>
                            </tr>
                        @endforeach

                        @foreach($bill->partStockSales as $sale)
                            <tr>
                                <td>{{ $serial++ }}</td>
                                <td>{{ $sale->partStock->product_name ?? 'N/A' }}</td>
                                <td>{{ $sale->quantity }}</td>
                                <td>{{ number_format($sale->unit_price, 2) }}</td>
                                <td>{{ number_format($sale->quantity * $sale->unit_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold">Subtotal</td>
                            <td>{{ number_format($billPaid, 2) }} Paid</td>
                            <td class="{{ $billDue > 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($billDue, 2) }} {{ $billDue > 0 ? '(Due)' : '(Paid)' }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @empty
        <p class="text-muted">No sales records available for this customer.</p>
    @endforelse

    <hr>
    <h5 class="mt-4">🔢 Grand Summary</h5>
    <ul>
        <li><strong>Total Amount:</strong> ৳{{ number_format($grandTotal, 2) }}</li>
        <li><strong>Total Paid:</strong> ৳{{ number_format($grandPaid, 2) }}</li>
        <li><strong>Total Due:</strong> <span class="{{ $grandDue > 0 ? 'text-danger' : 'text-success' }}">
            ৳{{ number_format($grandDue, 2) }}
        </span></li>
    </ul>
</div>
@endsection
