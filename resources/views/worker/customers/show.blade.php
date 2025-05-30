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
        $mergedSales = collect();

        // product sales
        foreach ($customer->productSales()->with(['stock', 'seller'])->get() as $sale) {
            $mergedSales->push([
                'name'       => $sale->stock->product_name ?? 'N/A',
                'quantity'   => $sale->quantity,
                'unit_price' => $sale->unit_price,
                'total'      => $sale->unit_price * $sale->quantity,
                'paid'       => $sale->paid_amount,
                'due'        => $sale->due_amount,
                'seller'     => $sale->seller->name ?? 'N/A',
            ]);
        }

        // part stock sales
        foreach ($customer->partsStockSales()->with(['partStock', 'seller'])->get() as $sale) {
            $mergedSales->push([
                'name'       => $sale->partStock->product_name ?? 'N/A',
                'quantity'   => $sale->quantity,
                'unit_price' => $sale->unit_price,
                'total'      => $sale->unit_price * $sale->quantity,
                'paid'       => $sale->paid_amount,
                'due'        => $sale->due_amount,
                'seller'     => $sale->seller->name ?? 'N/A',
            ]);
        }

        $grandTotal = $mergedSales->sum('total');
        $grandPaid = $mergedSales->sum('paid');
        $grandDue = $mergedSales->sum('due');
        $lastSeller = $mergedSales->isNotEmpty() ? $mergedSales->last()['seller'] : 'N/A';
    @endphp

    @if($mergedSales->isNotEmpty())
    <table class="table table-bordered text-center align-middle">
        <thead class="table-light">
            <tr>
                <th>ক্রম</th>
                <th>পণ্যের নাম</th>
                <th>পরিমাণ</th>
                <th>দর (৳)</th>
                <th>মোট (৳)</th>
                <th>পেইড (৳)</th>
                <th>বাকি (৳)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mergedSales as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ number_format($item['unit_price'], 2) }}</td>
                    <td>{{ number_format($item['total'], 2) }}</td>
                    <td>{{ number_format($item['paid'], 2) }}</td>
                    <td class="{{ $item['due'] > 0 ? 'text-danger' : 'text-success' }}">
                        {{ $item['due'] > 0 ? number_format($item['due'], 2) . ' (Due)' : 'Paid' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-light">
                <td colspan="4" class="text-end fw-bold">মোট</td>
                <td>{{ number_format($grandTotal, 2) }}</td>
                <td>{{ number_format($grandPaid, 2) }}</td>
                <td class="{{ $grandDue > 0 ? 'text-danger' : 'text-success' }}">
                    {{ $grandDue > 0 ? number_format($grandDue, 2) . ' (Due)' : 'Paid' }}
                </td>
            </tr>
        </tfoot>
    </table>
    @else
        <p class="text-center text-muted">কোন বিক্রয় রেকর্ড পাওয়া যায়নি।</p>
    @endif

    {{-- 🔷 Signature --}}
    <div class="text-end mt-5">
        <p><strong>পক্ষে - {{ $lastSeller }}</strong></p>
    </div>

    <div class="text-center no-print mt-4">
        <button class="btn btn-outline-primary" onclick="printInvoice()">🖨️ প্রিন্ট করুন</button>
    </div>
    <div class="mt-3 no-print">
        <a href="{{ route('worker.customers.index') }}" class="btn btn-secondary">← ফিরে যান</a>
    </div>
</div>

<script>
function printInvoice() {
    window.print();
}
</script>

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
        top: 0;
        left: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
    .text-danger { color: red !important; }
    .text-success { color: green !important; }
}
</style>
@endsection
