<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PartStock;
use App\Models\ProductSale;
use App\Models\PartStockSale;
use App\Models\Stock;

class BillController extends Controller
{
    public function getProducts(Request $request)
    {
        if ($request->type === 'stock') {
            return Stock::select('id', 'name')->get();
        } elseif ($request->type === 'partstock') {
            return PartStock::select('id', 'name')->get();
        }
        return response()->json([]);
    }

    public function store(Request $request)
    {
        $type = $request->input('product_type'); // Optional, or detect from ID if needed
        $details = $request->input('product_details', []);

        foreach ($details as $id => $data) {
            $data['product_id'] = $id;
            $data['customer_name'] = $request->customer_name;
            $data['paid_amount'] = $request->paid_amount;

            if ($request->input('productType') === 'partstock') {
                PartStockSale::create($data);
            } else {
                ProductSale::create($data);
            }
        }

        return back()->with('status', 'Bill created successfully!');
    }
}
