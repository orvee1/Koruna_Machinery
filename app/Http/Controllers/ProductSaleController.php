<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductSale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Branch;

class ProductSaleController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = ProductSale::with(['product', 'customer', 'branch', 'seller', 'investor']);

        if ($user->role === 'worker') {
            $query->forToday()->where('seller_id', $user->id);
        } else {
            if ($request->filled('date')) {
                $query->whereDate('created_at', $request->input('date'));
            }
            if ($request->filled('month')) {
                $query->forMonth($request->input('month'));
            }
            if ($request->filled('year')) {
                $query->forYear($request->input('year'));
            }
        }

        $sales = $query->latest()->paginate(20);
        return view('product_sales.index', compact('sales'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role === 'worker' || $user->role === 'admin' || $user->role === 'manager') {
            $products = Product::all();
            $customers = Customer::all();
            return view('product_sales.create', compact('products', 'customers'));
        }

        abort(403);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['worker', 'admin', 'manager'])) {
            abort(403);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        ProductSale::create([
            'branch_id' => $user->branch_id,
            'product_id' => $validated['product_id'],
            'customer_id' => $validated['customer_id'],
            'seller_id' => $user->id,
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'paid_amount' => $validated['paid_amount'],
        ]);

        return redirect()->route('product-sales.index')->with('success', 'Sale added successfully');
    }

    public function edit(ProductSale $productSale)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403);
        }

        $products = Product::all();
        $customers = Customer::all();

        return view('product_sales.edit', compact('productSale', 'products', 'customers'));
    }

    public function update(Request $request, ProductSale $productSale)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $productSale->update($validated);

        return redirect()->route('product-sales.index')->with('success', 'Sale updated successfully');
    }

    public function destroy(ProductSale $productSale)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403);
        }

        $productSale->delete();
        return redirect()->route('product-sales.index')->with('success', 'Sale deleted successfully');
    }
}
