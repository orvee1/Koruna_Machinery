<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the stocks.
     */
    public function index()
    {
        $stocks = Stock::with('product', 'branch')->paginate(10); // Fetch stocks with product and branch relationships
        return view('admin.stocks.index', compact('stocks'));
    }

    /**
     * Show the form for creating a new stock entry.
     */
    public function create()
    {
        $products = Product::all();
        $branches = Branch::all();
        return view('admin.stocks.create', compact('products', 'branches'));
    }

    /**
     * Store a newly created stock entry in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_name' => 'required|string|max:255',
            'buying_price' => 'required|numeric',
            'quantity' => 'required|integer|min:1',
            'total_amount' => 'required|numeric',
            'deposit_amount' => 'nullable|numeric',
            'due_amount' => 'nullable|numeric',
            'purchase_date' => 'required|date',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $stock = Stock::create($request->all());

        // After creating the stock, update the stock quantity for the product
        $product = $stock->product;
        $product->stock_quantity += $request->quantity;
        $product->save();

        return redirect()->route('admin.stocks.index')->with('success', 'Stock added successfully.');
    }

    /**
     * Show the form for editing the specified stock entry.
     */
    public function edit(Stock $stock)
    {
        $products = Product::all();
        $branches = Branch::all();
        return view('admin.stocks.edit', compact('stock', 'products', 'branches'));
    }

    /**
     * Update the specified stock entry in the database.
     */
    public function update(Request $request, Stock $stock)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_name' => 'required|string|max:255',
            'buying_price' => 'required|numeric',
            'quantity' => 'required|integer|min:1',
            'total_amount' => 'required|numeric',
            'deposit_amount' => 'nullable|numeric',
            'due_amount' => 'nullable|numeric',
            'purchase_date' => 'required|date',
            'branch_id' => 'required|exists:branches,id',
        ]);

        // Before updating, update the stock quantity for the product
        $product = $stock->product;
        $product->stock_quantity -= $stock->quantity; // Remove old stock quantity
        $product->stock_quantity += $request->quantity; // Add the new stock quantity
        $product->save();

        $stock->update($request->all());

        return redirect()->route('admin.stocks.index')->with('success', 'Stock updated successfully.');
    }

    /**
     * Remove the specified stock entry from the database.
     */
    public function destroy(Stock $stock)
    {
        // Before deleting, update the stock quantity for the product
        $product = $stock->product;
        $product->stock_quantity -= $stock->quantity;
        $product->save();

        $stock->delete();

        return redirect()->route('admin.stocks.index')->with('success', 'Stock deleted successfully.');
    }

    public function show(Stock $stock)
    {
        // Load related product and branch data
        $stock->load('product', 'branch');

        // Pass stock data to the view
        return view('admin.stocks.show', compact('stock'));
    }
}
