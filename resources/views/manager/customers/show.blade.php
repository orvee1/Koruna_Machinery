@extends('layouts.app')

@section('title', 'Customer Invoice')

@section('content')
<div class="container" id="invoice-section">
    {{-- 🔷 Header --}}
    <div class="text-center border-bottom pb-3 mb-3">
        <h2 class="fw-bold">KARUNA MACHINERY</h2>
        <p>২৫, জুবলি রোড, চট্টগ্রাম। ফোন: ০১৮৮১-০৮৭৭১৬</p>
        <h5 class="mt-3 border-top pt-2">ক্যাশ মেমো</h5>
    </div>

    {{-- 🔶 Customer Info --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <p><strong>গ্রাহকের নাম:</strong> {{ $customer->name }}</p>
            <p><strong>ফোন:</strong> {{ $customer->phone }}</p>
            <p><strong>জেলা:</strong> {{ $customer->district ?? 'N/A' }}</p>
        </div>
        <div class="col-md-6 text-end">
            <p><strong>তারিখ:</strong> {{ now()->format('d M, Y') }}</p>
            <p><strong>Customer ID:</strong> {{ $customer->customer_id ?? 'N/A' }}</p>
        </div>
    </div>

    @php
        $mergedSales = collect();

        $productSales = $customer->productSales()->with(['product', 'seller'])->get();
        foreach ($productSales as $sale) {
            $mergedSales->push([
                'type' => 'product',
                'name' => $sale->product->name ?? 'N/A',
                'quantity' => $sale->quantity,
                'unit_price' => $sale->unit_price,
                'total' => $sale->total_amount,
                'paid' => $sale->paid_amount,
                'due' => $sale->due_amount,
                'status' => $sale->payment_status,
                'seller' => $sale->seller->name ?? 'N/A',
                'date' => $sale->created_at->format('d M, Y'),
            ]);
        }

        $partSales = $customer->partsStockSales()->with(['partStock', 'seller'])->get();
        foreach ($partSales as $sale) {
            $mergedSales->push([
                'type' => 'part',
                'name' => $sale->partStock->name ?? 'N/A',
                'quantity' => $sale->quantity,
                'unit_price' => $sale->unit_price,
                'total' => $sale->total_amount,
                'paid' => $sale->paid_amount,
                'due' => $sale->due_amount,
                'status' => $sale->payment_status,
                'seller' => $sale->seller->name ?? 'N/A',
                'date' => $sale->created_at->format('d M, Y'),
            ]);
        }

        $totalAmount = $mergedSales->sum('total');
        $totalPaid = $mergedSales->sum('paid');
        $totalDue = $mergedSales->sum('due');
    @endphp

    @if($mergedSales->isEmpty())
        <div class="alert alert-warning">এই গ্রাহকের কোনো বিক্রয় রেকর্ড নেই।</div>
    @else
        <table class="table table-bordered text-center">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>তারিখ</th>
                    <th>পণ্যের নাম</th>
                    <th>পরিমাণ</th>
                    <th>দর (৳)</th>
                    <th>মোট (৳)</th>
                    <th>পরিশোধ (৳)</th>
                    <th>বাকি (৳)</th>
                    <th>স্ট্যাটাস</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mergedSales as $index => $sale)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $sale['date'] }}</td>
                        <td>{{ $sale['name'] }}</td>
                        <td>{{ $sale['quantity'] }}</td>
                        <td>{{ number_format($sale['unit_price'], 2) }}</td>
                        <td>{{ number_format($sale['total'], 2) }}</td>
                        <td>{{ number_format($sale['paid'], 2) }}</td>
                        <td>{{ number_format($sale['due'], 2) }}</td>
                        <td>
                            @if($sale['status'] === 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($sale['status'] === 'partial')
                                <span class="badge bg-warning text-dark">Partial</span>
                            @else
                                <span class="badge bg-danger">Due</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="fw-bold table-light">
                    <td colspan="5" class="text-end">সর্বমোট</td>
                    <td>{{ number_format($totalAmount, 2) }} ৳</td>
                    <td>{{ number_format($totalPaid, 2) }} ৳</td>
                    <td>{{ number_format($totalDue, 2) }} ৳</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <p class="text-end"><strong>সর্বশেষ বিক্রেতা:</strong> {{ $mergedSales->last()['seller'] }}</p>

        {{-- Signature Line --}}
        <div class="text-end mt-5">
            <p><strong>পক্ষে - করুনা মেশিনারী</strong></p>
        </div>

        <div class="text-center no-print mt-4">
            <button class="btn btn-outline-primary" onclick="printInvoice()">🖨️ প্রিন্ট করুন</button>
        </div>
    @endif

    {{-- Back Button - No Print --}}
    <div class="mt-3 no-print">
        <a href="{{ route('worker.customers.index') }}" class="btn btn-secondary">← ফিরে যান</a>
    </div>
</div>

{{-- ✅ Print Style --}}
<style>
@media print {
    @page {
        size: A4;
        margin: 20mm;
    }

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
        page-break-after: always;
    }

    .no-print {
        display: none !important;
    }

    table {
        page-break-inside: auto;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
}
</style>

<script>
function printInvoice() {
    window.print();
}
</script>
@endsection
