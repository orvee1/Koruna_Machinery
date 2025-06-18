<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $bill->id }}</title>
    <style>
        body { font-family: 'Times New Roman', serif; margin: 30px; color: #000; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        .no-border td { border: none; padding: 4px 8px; text-align: left; }
        .summary { margin-top: 30px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <h2>Customer Invoice</h2>

    <table class="no-border">
        <tr><td><strong>Invoice #:</strong> {{ $bill->id }}</td></tr>
        <tr><td><strong>Date:</strong> {{ $bill->created_at->format('d M, Y') }}</td></tr>
        <tr><td><strong>Customer:</strong> {{ $bill->customer->name ?? 'N/A' }}</td></tr>
        <tr><td><strong>Phone:</strong> {{ $bill->customer->phone ?? 'N/A' }}</td></tr>
        <tr><td><strong>District:</strong> {{ $bill->customer->district ?? 'N/A' }}</td></tr>
        <tr><td><strong>Seller:</strong> {{ $bill->seller->name ?? 'N/A' }}</td></tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>SL</th>
                <th>Product Name</th>
                <th>Type</th>
                <th>Qty</th>
                <th>Unit Price (৳)</th>
                <th>Total (৳)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bill->product_details ?? [] as $item)
                @php
                    $productName = 'N/A';
                    if ($item['type'] === 'product') {
                        $stock = \App\Models\Stock::find($item['id']);
                        $productName = $stock->product_name ?? 'Deleted Product';
                    } elseif ($item['type'] === 'partstock') {
                        $part = \App\Models\PartStock::find($item['id']);
                        $productName = $part->product_name ?? 'Deleted Part Stock';
                    }
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $productName }}</td>
                    <td>{{ ucfirst($item['type']) }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ number_format($item['unit_price'], 2) }}</td>
                    <td>{{ number_format($item['quantity'] * $item['unit_price'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Total Amount:</strong> ৳{{ number_format($bill->total_amount, 2) }}</p>
        <p><strong>Paid Amount:</strong> ৳{{ number_format($bill->paid_amount, 2) }}</p>
        <p><strong>Due Amount:</strong> ৳{{ number_format($bill->due_amount, 2) }}</p>
    </div>

    <div class="no-print" style="margin-top: 20px;">
        <button onclick="window.print()">🖨️ Print Invoice</button>
    </div>
</body>
</html>
