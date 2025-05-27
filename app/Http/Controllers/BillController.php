<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\PartStock;
use App\Models\ProductSale;
use App\Models\PartStockSale;

class BillController extends Controller
{
    public function getProducts(Request $request)
    {
        $branchId = session('active_branch_id');

        if ($request->type === 'product') {
            return Stock::where('branch_id', $branchId)
                ->select('id', 'product_name as name','quantity','buying_price')
                ->get();
        } elseif ($request->type === 'partstock') {
            return PartStock::where('branch_id', $branchId)
                ->select('id', 'product_name as name','quantity','sell_value as selling_price')
                ->get();
        }

        return response()->json([]);
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
            // ->select('id', 'name', 'phone', 'district')
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

    if ($request->filled('customer_id')) {
        $customerId = $request->customer_id;
    } else {
        $customer = \App\Models\Customer::create([
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

    foreach ($request->product_details as $item) {
        $type       = $item['type'];
        $id         = $item['id'];
        $qty        = $item['quantity'];
        $unitPrice  = $item['price'];
        $lineTotal  = $qty * $unitPrice;

        $allocatedPaid   = ($totalSaleAmount > 0) ? ($lineTotal / $totalSaleAmount) * $paidAmount : 0;
        $dueAmount       = $lineTotal - $allocatedPaid;
        $paymentStatus   = $dueAmount <= 0 ? 'paid' : 'due';

        $data = [
            'branch_id'      => $branchId,
            'customer_id'    => $customerId,
            'seller_id'      => $sellerId,
            'quantity'       => $qty,
            'unit_price'     => $unitPrice,
            'paid_amount'    => round($allocatedPaid, 2),
            'payment_status' => $paymentStatus,
        ];

        if ($type === 'partstock') {
            $data['part_stock_id'] = $id;
            PartStockSale::create($data);
        } else {
            $data['stock_id'] = $id;
            ProductSale::create($data);
        }
    }

    return back()->with('status', 'Bill created successfully!');
}


}
