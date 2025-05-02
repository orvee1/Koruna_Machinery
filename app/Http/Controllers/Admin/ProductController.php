<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin');
    }

    public function index(Request $request)
    {
        $query = Product::query();

        // Worker বা Manager হলে নিজের ব্রাঞ্চ ফিল্টার
        if (Auth::user()->role == 'admin') {
            $query->where('branch_id', session('active_branch_id'));
        }

        $products = $query->latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $branches = Branch::all(); // Get all branches (only for admin)
        if (Auth::user()->role === 'manager') {
            return view('manager.products.create', compact('branches'));
        } else {
            return view('admin.products.create', compact('branches'));
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'buying_price'    => 'required|numeric',
            'selling_price'   => 'nullable|numeric',
            'stock_quantity'  => 'required|integer|min:0',
        ]);

        Product::create([
            'name'             => $request->name,
            'buying_price'     => $request->buying_price,
            'selling_price'    => $request->selling_price,
            'stock_quantity'   => $request->stock_quantity,
            'branch_id'        => session('active_branch_id'), // ব্রাঞ্চ নির্ধারণ
        ]);

        if(Auth::user()->role === 'manager') {
            return redirect()->route('manager.products.index')->with('success', 'Product created successfully.');
        }else {
            return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
        }
        // return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        // Only allow editing if the product belongs to the active branch
        if (Auth::user()->role !== 'admin' && $product->branch_id !== session('active_branch_id')) {
            abort(403);
        }

        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        if (Auth::user()->role !== 'admin' && $product->branch_id !== session('active_branch_id')) {
            abort(403);
        }

        $request->validate([
            'name'            => 'required|string|max:255',
            'buying_price'    => 'required|numeric',
            'selling_price'   => 'nullable|numeric',
            'stock_quantity'  => 'required|integer|min:0',
        ]);

        $product->update($request->only('name', 'buying_price', 'selling_price', 'stock_quantity'));

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function show(Product $product)
    {
        if (Auth::user()->role !== 'admin' && $product->branch_id !== session('active_branch_id')) {
            abort(403);
        }

        return view('admin.products.show', compact('product'));
    }

    public function destroy(Product $product)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}