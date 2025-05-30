<?php

namespace App\Http\Controllers;

use App\Models\Bill;
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

        $partStocks = PartStock::where('branch_id', $branchId)
            ->select(
                'id',
                'product_name as name',
                'quantity',
                DB::raw('NULL as buying_price'),
                DB::raw('sell_value as selling_price'),
                DB::raw("'partstock' as type")
            );

        $results = $products->unionAll($partStocks)->get();

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
    $validated = $request->validate([
        'branch_id' => 'required|exists:branches,id',
        'paid_amount' => 'required|numeric|min:0|max:99999999.99',
        'product_details' => 'required|array|min:1',
        'product_details.*.type' => 'required|in:product,part',
        'product_details.*.id' => 'required|integer',
        'product_details.*.quantity' => 'required|integer|min:1',
        'product_details.*.unit_price' => 'required|numeric|min:0|max:99999999.99',
        'customer_id' => 'nullable|exists:customers,id',
        'customer_name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:20',
        'district' => 'nullable|string|max:255',
    ]);

    $branchId = $validated['branch_id'];
    $paidAmount = $validated['paid_amount'];
    $details = $validated['product_details'];
    $totalBillAmount = 0;
    $appliedTotalPaid = 0;

    // ✅ যদি পুরাতন গ্রাহক না থাকে, নতুন customer তৈরি করবো
    if (empty($validated['customer_id'])) {
        $newCustomer = Customer::create([
            'name' => $validated['customer_name'],
            'phone' => $validated['phone'] ?? null,
            'district' => $validated['district'] ?? null,
            'branch_id' => $branchId,
            'type' => 2, // সাধারণত 2 হলে সাধারণ customer বোঝায়, আপনি চাইলে পরিবর্তন করতে পারেন
            'status' => 1, // অ্যাক্টিভ
        ]);
        $customerId = $newCustomer->id;
    } else {
        $customerId = $validated['customer_id'];
    }

    // ✅ Bill তৈরি
    $bill = Bill::create([
        'customer_id' => $customerId,
        'branch_id' => $branchId,
        'seller_id' => auth()->id(),
        'total_amount' => 0,
        'paid_amount' => 0,
        'due_amount' => 0,
        'payment_status' => 'due',
    ]);

    foreach ($details as $item) {
        $type = $item['type'];
        $id = $item['id'];
        $quantity = $item['quantity'];
        $unitPrice = $item['unit_price'];
        $total = $quantity * $unitPrice;

        $availablePaid = $paidAmount - $appliedTotalPaid;
        $applyPay = min($availablePaid, $total);
        $due = max($total - $applyPay, 0);

        $appliedTotalPaid += $applyPay;
        $totalBillAmount += $total;

        if ($type === 'product') {
            ProductSale::create([
                'branch_id' => $branchId,
                'stock_id' => $id,
                'customer_id' => $customerId,
                'seller_id' => auth()->id(),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'paid_amount' => $applyPay,
                'due_amount' => $due,
                'payment_status' => $due <= 0 ? 'paid' : 'due',
                'bill_id' => $bill->id,
            ]);
        } elseif ($type === 'part') {
            PartStockSale::create([
                'branch_id' => $branchId,
                'part_stock_id' => $id,
                'customer_id' => $customerId,
                'seller_id' => auth()->id(),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'paid_amount' => $applyPay,
                'due_amount' => $due,
                'payment_status' => $due <= 0 ? 'paid' : 'due',
                'bill_id' => $bill->id,
            ]);
        }
    }

    // ✅ Final bill update
    $bill->total_amount = $totalBillAmount;
    $bill->paid_amount = $appliedTotalPaid;
    $bill->due_amount = max(0, $totalBillAmount - $appliedTotalPaid);
    $bill->payment_status = $bill->due_amount <= 0 ? 'paid' : 'due';
    $bill->save();

    return redirect()->back()
        ->with('success', 'Bill created successfully with ID: ' . $bill->id);
}



}
