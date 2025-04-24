<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Investor;
use App\Models\Branch;
use App\Models\PartStock;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * Display a listing of the sales.
     */
    public function index()
    {
        $sales = Sale::with('product', 'customer', 'investor', 'branch')->paginate(10);
        return view('admin.sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new sale.
     */
    public function create()
    {
        $products = Product::all();
        $customers = Customer::all();
        $investors = Investor::all();
        $branches = Branch::all();

        return view('admin.sales.create', compact('products', 'customers', 'investors', 'branches'));
    }

    /**
     * Store a newly created sale in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'total_amount' => 'required|decimal:0,2',
            'paid_amount' => 'required|decimal:0,2',
            'due_amount' => 'required|decimal:0,2',
            'payment_status' => 'required|in:paid,pending',
            'investor_id' => 'nullable|exists:investors,id',
            'branch_id' => 'required|exists:branches,id',
            // 'part_stock_id' => 'required|exists:part_stocks,id',  // Assuming part stock id is passed to manage part stock
        ]);

        // Store the sale
        $sale = Sale::create($request->all());

        // Adjust stock based on the sale (Using PartStock for the sale)
        $this->adjustStockOnSale($sale, $request->quantity, $request->part_stock_id);

        return redirect()->route('admin.sales.index')->with('success', 'Sale added successfully.');
    }

    /**
     * Show the details of a specific sale.
     */
    public function show(Sale $sale)
    {
        $sale->load('product', 'customer', 'investor', 'branch');  // Load related data
        return view('admin.sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified sale.
     */
    public function edit(Sale $sale)
    {
        $products = Product::all();
        $customers = Customer::all();
        $investors = Investor::all();
        $branches = Branch::all();
        return view('admin.sales.edit', compact('sale', 'products', 'customers', 'investors', 'branches'));
    }

    /**
     * Update the specified sale in the database.
     */
    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'total_amount' => 'required|decimal:0,2',
            'paid_amount' => 'required|decimal:0,2',
            'due_amount' => 'required|decimal:0,2',
            'payment_status' => 'required|in:paid,pending',
            'investor_id' => 'nullable|exists:investors,id',
            'branch_id' => 'required|exists:branches,id',
            // 'part_stock_id' => 'required|exists:part_stocks,id',  // Assuming part stock id is passed to manage part stock
        ]);

        // Adjust stock based on the sale (Revert previous stock change and adjust new stock)
        $this->adjustStockOnReturn($sale, $sale->quantity, $sale->part_stock_id);  // Revert previous stock adjustment
        $sale->update($request->all());  // Update the sale data
        $this->adjustStockOnSale($sale, $request->quantity, $request->part_stock_id);  // Adjust new stock based on updated quantity

        return redirect()->route('admin.sales.index')->with('success', 'Sale updated successfully.');
    }

    /**
     * Adjust the stock when a sale is made (using part stock).
     */
    public function adjustStockOnSale(Sale $sale, $quantity, $partStockId)
    {
        // Decrease part stock
        // $partStock = PartStock::find($partStockId);
        // if ($partStock && $partStock->quantity >= $quantity) {
        //     $partStock->quantity -= $quantity;
        //     $partStock->save();
        // }

        // Adjust product stock
        $product = $sale->product;
        if ($product) {
            $product->stock_quantity -= $quantity;
            $product->save();
        }
    }

    /**
     * Adjust the stock when a sale is returned.
     */
    public function adjustStockOnReturn(Sale $sale, $quantity, $partStockId)
    {
        // Increase part stock
        // $partStock = PartStock::find($partStockId);
        // if ($partStock) {
        //     $partStock->quantity += $quantity;
        //     $partStock->save();
        // }

        // Adjust product stock
        $product = $sale->product;
        if ($product) {
            $product->stock_quantity += $quantity;
            $product->save();
        }
    }

    /**
     * Remove the specified sale from the database.
     */
}
