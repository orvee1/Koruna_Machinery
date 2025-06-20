<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8">
  <title>Invoice #{{ $bill->id }}</title>
  <style>
    body {
      font-family: 'Siyam Rupali', 'Times New Roman', serif;
      margin: 20px;
      color: #000;
    }
    .header {
      text-align: center;
      border-bottom: 2px solid #000;
      padding-bottom: 10px;
    }
    .info-table, .product-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    .info-table td {
      padding: 5px;
      vertical-align: top;
    }
    .product-table th, .product-table td {
      border: 1px solid #000;
      padding: 5px;
      text-align: center;
    }
    .summary td {
      border: none;
      padding: 5px;
      text-align: right;
    }
    .footer-signature {
      margin-top: 40px;
      display: flex;
      justify-content: space-between;
    }
    .signature-box {
      text-align: right;
    }
    .no-print {
      margin-top: 20px;
      text-align: center;
    }
  </style>
</head>
<body>

  <div class="header">
    <h2 style="margin: 0;">করুণা মেশিনারী</h2>
    <h3 style="margin: 0;">KARUNA MACHINERY</h3>
    <p style="margin: 5px 0;">হাতাই ডিজেল ইঞ্জিন, পাওয়ার টিলার, পানির পাম্প, জেনারেটর, ইত্যাদি বিক্রয় কেন্দ্র</p>
    {{-- <p>২৫২, জুবলী রোড, চট্টগ্রাম | ফোন: ০১৮১৮-৫৮৩৬৩৬, ০১৮১৮-৫৮৩৬৩৭</p> --}}
  </div>

  <table class="info-table">
    <tr>
      <td><strong>নং:</strong> {{ $bill->id }}</td>
      <td><strong>তারিখ:</strong> {{ $bill->created_at->format('d/m/Y') }}</td>
    </tr>
    <tr>
      <td><strong>নাম:</strong> {{ $bill->customer->name ?? 'N/A' }}</td>
      <td><strong>ঠিকানা:</strong> {{ $bill->customer->district ?? 'N/A' }}</td>
    </tr>
  </table>

  <table class="product-table">
    <thead>
      <tr>
        <th>ক্রম</th>
        <th>বিবরণ</th>
        <th>পরিমাণ</th>
        <th>দর</th>
        <th>টাকা</th>
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
          <td>{{ $item['quantity'] }}</td>
          <td>{{ number_format($item['unit_price'], 2) }}</td>
          <td>{{ number_format($item['quantity'] * $item['unit_price'], 2) }}</td>
        </tr>
      @endforeach
      @for ($i = count($bill->product_details ?? []); $i < 10; $i++)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>&nbsp;</td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      @endfor
      <tr>
        <td colspan="4" style="text-align: right;"><strong>মোট</strong></td>
        <td><strong>{{ number_format($bill->total_amount, 2) }}</strong></td>
      </tr>
      <tr>
        <td colspan="4" style="text-align: right;">অগ্রিম</td>
        <td>{{ number_format($bill->paid_amount, 2) }}</td>
      </tr>
      <tr>
        <td colspan="4" style="text-align: right;">বাকি</td>
        <td>{{ number_format($bill->due_amount, 2) }}</td>
      </tr>
    </tbody>
  </table>

  <p style="margin-top: 15px;"><strong>টাকা (কথায়):</strong> ...............................................................................................</p>

  <div class="footer-signature">
    <div></div>
    <div class="signature-box">
      <p>পক্ষে - করুনা মেশিনারী</p>
      <p style="margin-top: 50px;">বিক্রেতা: {{ $bill->seller->name ?? 'N/A' }}</p>
    </div>
  </div>

  <div class="no-print">
    <button onclick="window.print()">🖨️ প্রিন্ট করুন</button>
  </div>

</body>
</html>
