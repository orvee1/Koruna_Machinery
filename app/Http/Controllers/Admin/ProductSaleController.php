<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductSaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin');
    }

    public function index(Request $request)
    {
        $query = ProductSale::with(['product', 'customer', 'branch', 'seller', 'investor']);
            // Admin/Manager: তারিখ, মাস, বছর অনুযায়ী ফিল্টার করতে পারবে
            if ($request->filled('date')) {
                $query->whereDate('created_at', $request->input('date'));
            }
            if ($request->filled('month')) {
                $query->forMonth($request->input('month'));
            }
            if ($request->filled('year')) {
                $query->forYear($request->input('year'));
            }

        $sales = $query->latest()->paginate(20);
        return view('admin.product-sales.index', compact('sales'));
    }

    public function create()
    {
        $branchId = session('active_branch_id', Auth::user()->branch_id);
        $products = Product::where('branch_id', session('active_branch_id'))->get();
        $customers = Customer::where('branch_id', session('active_branch_id'))->get();

        return view('admin.product-sales.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
       
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        ProductSale::create([
            'branch_id' => session('active_branch_id'),
            'product_id' => $validated['product_id'],
            'customer_id' => $validated['customer_id'],
            'seller_id' => $user->id,
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'paid_amount' => $validated['paid_amount'],
            'due_amount' => ($validated['quantity'] * $validated['unit_price']) - $validated['paid_amount'],
        ]);

        return redirect()->route('admin.product-sales.index')->with('success', 'Product Sale added successfully.');
    }

    public function edit(ProductSale $productSale)
    {
        $products = Product::where('branch_id', session('active_branch_id'))->get();
        $customers = Customer::where('branch_id', session('active_branch_id'))->get();

        return view('admin.product-sales.edit', compact('productSale', 'products', 'customers'));
    }

    public function update(Request $request, ProductSale $productSale)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $productSale->update($validated);

        return redirect()->route('admin.product-sales.index')->with('success', 'Product Sale updated successfully.');
    }

        public function show(ProductSale $productSale)
    {
        $user = Auth::user();
        return view('admin.prodct-sales.show', compact('productSale'));
    }


    public function destroy(ProductSale $productSale)
    {
        $productSale->delete();

        return redirect()->route('admin.product-sales.index')->with('success', 'Product Sale deleted successfully.');
    }
}
