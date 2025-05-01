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
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = ProductSale::with(['product', 'customer', 'branch', 'seller', 'investor']);

        if ($user->role === 'worker') {
            // Worker: শুধু আজকের সেলস দেখতে পারবে
            $query->forToday()->where('seller_id', $user->id);
        } else {
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
        }

        // Active Branch Filtering
        if (session('active_branch_id')) {
            $query->where('branch_id', session('active_branch_id'));
        }

        $sales = $query->latest()->paginate(20);
        return view('admin.product-sales.index', compact('sales'));
    }

    public function create()
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'manager', 'worker'])) {
            abort(403);
        }

        $products = Product::where('branch_id', session('active_branch_id'))->get();
        $customers = Customer::where('branch_id', session('active_branch_id'))->get();

        return view('admin.product-sales.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'manager', 'worker'])) {
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
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403);
        }

        $products = Product::where('branch_id', session('active_branch_id'))->get();
        $customers = Customer::where('branch_id', session('active_branch_id'))->get();

        return view('admin.product-sales.edit', compact('productSale', 'products', 'customers'));
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

        return redirect()->route('admin.product-sales.index')->with('success', 'Product Sale updated successfully.');
    }

        public function show(ProductSale $productSale)
    {
        $user = Auth::user();

        // Worker রা তাদের ব্রাঞ্চের ডেটা ছাড়া অন্য কিছু দেখতে পারবে না
        if ($user->role !== 'admin' && $productSale->branch_id !== session('active_branch_id')) {
            abort(403, 'Unauthorized Access');
        }

        return view('admin.product-sales.show', compact('productSale'));
    }


    public function destroy(ProductSale $productSale)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403);
        }

        $productSale->delete();

        return redirect()->route('admin.product-sales.index')->with('success', 'Product Sale deleted successfully.');
    }
}
