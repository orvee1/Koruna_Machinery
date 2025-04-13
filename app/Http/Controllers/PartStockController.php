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
        $request->validate([
            'product_name' => 'required|string|max:255',
            'buy_value' => 'required|numeric',
            'quantity' => 'required|integer|min:1',
            'sell_value' => 'required|numeric',
            'product_id' => 'required|exists:products,id',
            'branch_id' => 'required|exists:branches,id',
        ]);
    
        // Calculate the amount and total profit automatically
        $amount = $request->buy_value * $request->quantity;
        $total_profit = ($request->sell_value - $request->buy_value) * $request->quantity;
    
        // Prepare data for PartStock creation
        $data = $request->all();
        $data['amount'] = $amount;
        $data['total_profit'] = $total_profit;
    
        // Create the part stock
        $partStock = PartStock::create($data);
    
        // Update the stock quantity of the product
        $product = $partStock->product;
        $product->stock_quantity += $request->quantity;
        $product->save();
    
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

    // Before updating, adjust the stock quantity of the product
    $product = $partStock->product;
    $product->stock_quantity -= $partStock->quantity; // Remove old quantity
    $product->stock_quantity += $request->quantity; // Add new quantity
    $product->save();

    // Recalculate amount and total profit for the updated data
    $amount = $request->buy_value * $request->quantity;
    $total_profit = ($request->sell_value - $request->buy_value) * $request->quantity;

    // Update part stock with the new values
    $partStock->update(array_merge($request->all(), ['amount' => $amount, 'total_profit' => $total_profit]));

    return redirect()->route('admin.partstocks.index')->with('success', 'Part stock updated successfully.');
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
