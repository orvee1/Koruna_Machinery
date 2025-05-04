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
        $products = $query->latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $branches = Branch::all(); // Get all branches (only for admin)
      
        return view('admin.products.create', compact('branches'));   
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
 
          return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
        
        // return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
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
        return view('admin.products.show', compact('product'));
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}