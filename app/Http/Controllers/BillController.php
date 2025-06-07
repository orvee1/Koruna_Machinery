<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\PartStock;
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

        $customers = Customer::where('branch_id', $branchId)
            ->where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($c) {
                $c->total_due = $c->bills()->where('payment_status', '!=', 'paid')->sum('due_amount');
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
        'product_details.*.type' => 'required|in:product,partstock',
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

    foreach ($details as $item) {
        $type = $item['type'];
        $productId = $item['id'];
        $unitPrice = $item['unit_price'];
        $quantity = $item['quantity'];

        if ($type === 'product') {
            $product = \App\Models\Stock::where('branch_id', $branchId)->findOrFail($productId);

            if ($unitPrice < $product->buying_price) {
                return back()->withErrors([
                    "product_details.{$productId}.unit_price" => 
                        "Selling price for '{$product->product_name}' cannot be below buying price (৳{$product->buying_price})."
                ])->withInput();
            }
        } elseif ($type === 'partstock') {
            $part = \App\Models\PartStock::where('branch_id', $branchId)->findOrFail($productId);

            if ($unitPrice < $part->sell_value) {
                return back()->withErrors([
                    "product_details.{$productId}.unit_price" => 
                        "Selling price for '{$part->product_name}' cannot be below minimum (৳{$part->sell_value})."
                ])->withInput();
            }
        }

        $totalBillAmount += $unitPrice * $quantity;
    }

    $customerId = $validated['customer_id'] ?? Customer::create([
        'name' => $validated['customer_name'],
        'phone' => $validated['phone'] ?? null,
        'district' => $validated['district'] ?? null,
        'branch_id' => $branchId,
        'type' => 2,
        'status' => 1,
    ])->id;

    $bill = Bill::create([
        'customer_id'     => $customerId,
        'branch_id'       => $branchId,
        'seller_id'       => auth()->id(),
        'total_amount'    => $totalBillAmount,
        'paid_amount'     => $paidAmount,
        'due_amount'      => max(0, $totalBillAmount - $paidAmount),
        'payment_status'  => $totalBillAmount <= $paidAmount ? 'paid' : 'due',
        'product_details' => $details,
    ]);

    return redirect()->back()->with('success', 'Bill created successfully with ID: ' . $bill->id);
}


}
