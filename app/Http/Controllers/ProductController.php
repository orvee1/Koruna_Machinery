<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::all();

        $productsQuery = Product::query();
        if (request()->filled('branch_id')) {
            $productsQuery->where('branch_id', request('branch_id'));
        }

        if(!auth()->user()->isAdmin()) {
            $productsQuery->where('branch_id', auth()->user()->branch_id);
        }

        $products = $productsQuery->paginate(10);

        foreach ($products as $product) {
            $product->total_revenue = $product->sales->sum('total_amount');
        }

        return view('admin.products.index', compact('products', 'branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::all();
        return view('worker.products.create', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       
        $request->validate([
            'name' => 'required|string|max:255',
            'buying_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'branch_id' => 'required|exists:branches,id',  
        ]);

        
        Product::create([
            'name' => $request->name,
            'buying_price' => $request->buying_price,
            'selling_price' => $request->selling_price,
            'stock_quantity' => $request->stock_quantity,
            'branch_id' => $request->branch_id,  
        ]);

        return redirect()->route('products.index')->with('success', 'Product added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        
        $sales = $product->sales()->get();

        return view('admin.products.show', compact('product', 'sales'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $branches = Branch::all();
        return view('admin.products.edit', compact('product', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
      
        $request->validate([
            'name' => 'required|string|max:255',
            'buying_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'branch_id' => 'required|exists:branches,id',  
        ]);

        
        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
