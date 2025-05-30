<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\ProductList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductListController extends Controller
{
    public function index(Request $request)
    {
        $branchId = auth()->user()->branch_id;

        $query = ProductList::where('branch_id', $branchId);

        if ($request->filled('product_name')) {
            $query->where('product_name', 'like', '%' . $request->input('product_name') . '%');
        }

        if ($request->filled('supplier_name')) {
            $query->where('supplier_name', 'like', '%' . $request->input('supplier_name') . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('purchase_date', $request->input('date'));
        }

        $products = $query->latest()->paginate(20);

        return view('worker.products.index', compact('products'));
    }
}