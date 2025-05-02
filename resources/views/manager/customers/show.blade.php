@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="container">
    <h1>Customer Details</h1>

    {{-- 🧾 Full Invoice Section (Customer Info + Purchase Table) --}}
    <div id="invoice-section">
        {{-- ✅ Customer Info --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <strong>{{ $customer->name }}</strong>
            </div>
            <div class="card-body">
                <h5 class="card-title">Customer Information</h5>
                <p><strong>Phone:</strong> {{ $customer->phone }}</p>
                <p><strong>District:</strong> {{ $customer->district ?? 'N/A' }}</p>
                <p><strong>Customer ID:</strong> {{ $customer->customer_id ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- ✅ Purchase History --}}
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <strong>Purchase History / Invoice</strong>
            </div>
            <div class="card-body">
                @if($sales->isEmpty())
                    <p class="text-muted">No sales found for this customer.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Amount (৳)</th>
                                    <th>Seller</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $total = 0; @endphp
                                @foreach($sales as $sale)
                                    @php $total += $sale->paid_amount; @endphp
                                    <tr>
                                        <td>{{ $sale->created_at->format('d M, Y') }}</td>
                                        <td>{{ $sale->product->name ?? 'N/A' }}</td>
                                        <td>{{ $sale->quantity ?? 'N/A' }}</td>
                                        <td>{{ number_format($sale->paid_amount, 2) }}</td>
                                        <td>{{ $sale->seller->name ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-info fw-bold">
                                    <td colspan="2">Total Purchase</td>
                                    <td colspan="2">{{ number_format($total, 2) }} ৳</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <button onclick="printInvoice()" class="btn btn-outline-primary mt-3 no-print">🖨️ Print Invoice</button>
                @endif
            </div>
        </div>
    </div>

    {{-- ✅ Normal Navigation --}}
    <a href="{{ route('manager.customers.index') }}" class="btn btn-secondary">← Back to Customer List</a>
</div>

{{-- ✅ CSS for Print Only --}}
<style>
@media print {
    body * {
        visibility: hidden;
    }
    #invoice-section, #invoice-section * {
        visibility: visible;
    }
    #invoice-section {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
}
</style>

{{-- ✅ Print Function --}}
<script>
function printInvoice() {
    window.print();
}
</script>
@endsection
