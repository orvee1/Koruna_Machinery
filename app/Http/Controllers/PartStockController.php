<?php

namespace App\Http\Controllers;

use App\Models\PartStock;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Http\Request;

class PartStockController extends Controller
{
    /**
     * Display a listing of the part stocks.
     */
    public function index()
    {
        $partStocks = PartStock::with('product', 'branch')->paginate(10); // Fetch part stocks with product and branch relationships
        return view('admin.partstocks.index', compact('partStocks'));
    }

    /**
     * Show the form for creating a new part stock entry.
     */
    public function create()
    {
        $products = Product::all();
        $branches = Branch::all();
        return view('admin.partstocks.create', compact('products', 'branches'));
    }

    /**
     * Store a newly created part stock entry in the database.
     */
    public function store(Request $request)
    {
       $partStock = $request->validate([
            'product_name' => 'required|string|max:255',
            'buy_value' => 'required|numeric',
            'quantity' => 'required|integer|min:1',
            'sell_value' => 'required|numeric',
            'product_id' => 'required|exists:products,id',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $partStock = PartStock::create($request->all());

    
        // Calculate the total purchase amount after part stock creation
        $partStock->calculateTotalPurchaseAmount($request->buy_value, $request->quantity);
        
        // Redirect to the index with a success message
        return redirect()->route('admin.partstocks.index')->with('success', 'Part stock added successfully.');
    }
    
    
    /**
     * Show the form for editing the specified part stock entry.
     */
    public function edit(PartStock $partStock)
    {
        $products = Product::all();
        $branches = Branch::all();
        return view('admin.partstocks.edit', compact('partStock', 'products', 'branches'));
    }

    /**
     * Update the specified part stock entry in the database.
     */
    public function update(Request $request, PartStock $partStock)
{
    $request->validate([
        'product_name' => 'required|string|max:255',
        'buy_value' => 'required|numeric',
        'quantity' => 'required|integer|min:1',
        'sell_value' => 'required|numeric',
        'product_id' => 'required|exists:products,id',
        'branch_id' => 'required|exists:branches,id',
    ]);

    $partStock->update($request->all());

    $partStock->calculateTotalPurchaseAmount($request->buy_value, $request->quantity);

    return redirect()->route('admin.partstocks.index')->with('success', 'Part stock updated successfully.');
}

public function calculateTotalPurchase(Product $product, Request $request)
{
    $request->validate([
        'quantity' => 'required|integer|min:1',
        'buy_value' => 'required|numeric',
    ]);

    $product->buy_value = $request->buy_value;
    $product->quantity = $request->quantity;
    $product->save();
    $product->calculateTotalPurchaseAmount($request->buy_value, $request->quantity);

    return back()->with('success', 'Total purchase amount updated successfully.');
}

   
    /**
     * Show the details of a specific part stock entry.
     */
    public function show(PartStock $partStock)
    {
        $partStock->load('product', 'branch'); // Load related product and branch data
        return view('admin.partstocks.show', compact('partStock'));
    }
}
