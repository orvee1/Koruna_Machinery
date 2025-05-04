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
            <p><strong>তারিখ:</strong> {{ now()->format('d/m/Y') }}</p>
            <p><strong>মেমো নং:</strong> {{ $customer->customer_id ?? 'N/A' }}</p>
        </div>
    </div>

    @php
        // mergedSales তৈরি: পণ্য ও পার্টসেলস উভয়েই seller সহ
        $mergedSales = collect();

        foreach ($customer->productSales()->with(['product','seller'])->get() as $sale) {
            $mergedSales->push([
                'name'       => $sale->product->name ?? 'N/A',
                'quantity'   => $sale->quantity,
                'unit_price' => $sale->unit_price,
                'total'      => $sale->total_amount,
                'seller'     => $sale->seller->name ?? 'N/A',
            ]);
        }

        foreach ($customer->partsStockSales()->with(['partStock','seller'])->get() as $sale) {
            $mergedSales->push([
                'name'       => $sale->partStock->name ?? 'N/A',
                'quantity'   => $sale->quantity,
                'unit_price' => $sale->unit_price,
                'total'      => $sale->total_amount,
                'seller'     => $sale->seller->name ?? 'N/A',
            ]);
        }

        $discount      = session('invoice_discount', 0);
        $subtotal      = $mergedSales->sum('total');
        $afterDiscount = $subtotal - $discount;

        // শেষ বিক্রেতার নাম
        $lastSeller = $mergedSales->isEmpty() ? 'N/A' : $mergedSales->last()['seller'];
    @endphp

    <table class="table table-bordered text-center align-middle">
        <thead class="table-light">
            <tr>
                <th style="width:5%;">ক্রম</th>
                <th style="width:45%;">বিবরণ</th>
                <th style="width:15%;">পরিমাণ</th>
                <th style="width:15%;">দর (৳)</th>
                <th style="width:20%;">টাকা (৳)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mergedSales as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="text-start">{{ $item['name'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ number_format($item['unit_price'], 2) }}</td>
                    <td>{{ number_format($item['total'], 2) }}</td>
                </tr>
            @endforeach

            {{-- ডিসকাউন্ট রো --}}
            <tr>
                <td colspan="4" class="text-end"><strong>Discount</strong></td>
                <td>{{ number_format($discount, 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="fw-bold table-light">
                <td colspan="4" class="text-end">মোট</td>
                <td>{{ number_format($afterDiscount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Signature with dynamic seller --}}
    <div class="text-end mt-5">
        <p><strong>পক্ষে - {{ $lastSeller }}</strong></p>
    </div>

    {{-- Print & Back --}}
    <div class="text-center no-print mt-4">
        <button class="btn btn-outline-primary" onclick="printInvoice()">🖨️ প্রিন্ট করুন</button>
    </div>
    <div class="mt-3 no-print">
        <a href="{{ route('worker.customers.index') }}" class="btn btn-secondary">← ফিরে যান</a>
    </div>
</div>

{{-- Improved Print Styles --}}
<style>
@media print {
    /* পুরো বডির অন্যান্য এলিমেন্ট হাইড করে শুধু invoice-section দেখাবে */
    body * {
        visibility: hidden;
    }
    #invoice-section, #invoice-section * {
        visibility: visible;
    }
    #invoice-section {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        /* পেজ ব্রেক অবয়েড করে */
        page-break-after: auto;
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
