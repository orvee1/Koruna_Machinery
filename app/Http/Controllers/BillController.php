<?php

namespace App\Http\Controllers;

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

        $branchId  = session('active_branch_id');
        $sellerId  = auth()->id();
        $paidAmount = $request->paid_amount;

        if ($request->filled('customer_id')) {
            $customerId = $request->customer_id;
        } else {
            $customer = \App\Models\Customer::create([
                'name'       => $request->customer_name,
                'phone'      => $request->phone,
                'district'   => $request->district,
                'branch_id'  => $branchId,
            ]);
            $customerId = $customer->id;
        }

        foreach ($request->product_details as $item) {
            $type       = $item['type'];
            $id         = $item['id'];
            $qty        = $item['quantity'];
            $unitPrice  = $item['price'];

            $totalAmount = $qty * $unitPrice;
            $paymentStatus = match (true) {
                $paidAmount >= $totalAmount => 'paid',
                // $paidAmount > 0             => 'partial',
                default                     => 'due',
            };

            $data = [
                'branch_id'      => $branchId,
                'customer_id'    => $customerId,
                'seller_id'      => $sellerId,
                'quantity'       => $qty,
                'unit_price'     => $unitPrice,
                'paid_amount'    => $paidAmount,
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
