<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\PartStock;
use App\Models\ProductSale;
use App\Models\PartStockSale;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
        public function getProducts(Request $request)
    {
        $branchId = session('active_branch_id');

        $products = Stock::where('branch_id', $branchId)
            ->select(
                'id',
                'product_name as name',
                'quantity',
                'buying_price',
                DB::raw('NULL as selling_price'),
                DB::raw("'product' as type")
            );

        $partstocks = PartStock::where('branch_id', $branchId)
            ->select(
                'id',
                'product_name as name',
                'quantity',
                DB::raw('NULL as buying_price'),
                DB::raw('sell_value as selling_price'),
                DB::raw("'partstock' as type")
            );

        $results = $products->unionAll($partstocks)->get();

        // 🛠️ Fix: force numeric conversion
        $results = $results->map(function ($item) {
            if ($item->type === 'product') {
                $item->buying_price = (float) $item->buying_price;
                $item->selling_price = null;
            } elseif ($item->type === 'partstock') {
                $item->buying_price = null;
                $item->selling_price = (float) $item->selling_price;
            }
            return $item;
        });

        return response()->json($results);
    }


        public function getCustomers(Request $request)
    {
        $branchId = session('active_branch_id');
        $query = $request->get('name');

        $customers = Customer::withSum([
            'productSales as product_due' => fn($q) => $q->where('payment_status', '!=', 'paid'),
            'partsStockSales as part_due' => fn($q) => $q->where('payment_status', '!=', 'paid'),
        ], 'due_amount')
            ->where('branch_id', $branchId)
            ->where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get();
        $customers = $customers->map(function ($c) {
        $c->total_due = round(($c->product_due ?? 0) + ($c->part_due ?? 0), 2);
        return $c;
        });

        return response()->json($customers);
    }

    public function store(Request $request)
{
    $request->validate([
        'customer_name'     => 'required|string',
        'product_details'   => 'required|array',
        'paid_amount'       => 'required|numeric',
        'phone'             => 'nullable|string|max:20',
        'district'          => 'nullable|string|max:255',
        'customer_id'       => 'nullable|exists:customers,id',
    ]);

    $branchId   = session('active_branch_id');
    $sellerId   = auth()->id();
    $paidAmount = $request->paid_amount;

    $customerId = $request->customer_id;
    if (!$customerId) {
        $customer = Customer::create([
            'name'      => $request->customer_name,
            'phone'     => $request->phone,
            'district'  => $request->district,
            'branch_id' => $branchId,
        ]);
        $customerId = $customer->id;
    }

    $totalSaleAmount = 0;
    foreach ($request->product_details as $item) {
        $totalSaleAmount += $item['quantity'] * $item['price'];
    }

    foreach ($request->product_details as $uid => $item) {
        $type       = $item['type'];
        $id         = $item['id'];
        $qty        = $item['quantity'];
        $unitPrice  = $item['price'];
        $lineTotal  = $qty * $unitPrice;

        $allocatedPaid = ($totalSaleAmount > 0) ? ($lineTotal / $totalSaleAmount) * $paidAmount : 0;
        $dueAmount     = max($lineTotal - $allocatedPaid, 0);
        $paymentStatus = $dueAmount <= 0 ? 'paid' : 'due';

        $data = [
            'branch_id'      => $branchId,
            'customer_id'    => $customerId,
            'seller_id'      => $sellerId,
            'quantity'       => $qty,
            'unit_price'     => $unitPrice,
            'paid_amount'    => round($allocatedPaid, 2),
            'due_amount'     => round($dueAmount, 2),
            'payment_status' => $paymentStatus,
        ];

        if ($type === 'partstock') {
            $data['part_stock_id'] = $id;
            PartStockSale::create($data);
        } elseif ($type === 'product') {
            $data['stock_id'] = $id;
            ProductSale::create($data);
        }
    }

    return back()->with('status', 'Bill created successfully!');
}



}
