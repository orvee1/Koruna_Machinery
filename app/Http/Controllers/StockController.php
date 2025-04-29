<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Stock;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Stock::query();

        // Worker বা Manager হলে নিজের ব্রাঞ্চের স্টক
        if (Auth::user()->role !== 'admin') {
            $query->where('branch_id', session('active_branch_id'));
        }

        $stocks = $query->latest()->paginate(20);

        return view('admin.stocks.index', compact('stocks'));
    }

    public function create()
    {
        $branches = Branch::all();
        $products = Product::where('branch_id', session('active_branch_id'))->get();
        return view('admin.stocks.create', compact('products', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'      => 'required|exists:products,id',
            'supplier_name'   => 'required|string|max:255',
            'buying_price'    => 'required|numeric|min:0',
            'quantity'        => 'required|integer|min:1',
            'deposit_amount'  => 'nullable|numeric|min:0',
            'purchase_date'   => 'required|date',
        ]);

        Stock::create([
            'branch_id'       => session('active_branch_id'),
            'product_id'      => $request->product_id,
            'supplier_name'   => $request->supplier_name,
            'buying_price'    => $request->buying_price,
            'quantity'        => $request->quantity,
            'deposit_amount'  => $request->deposit_amount,
            'purchase_date'   => $request->purchase_date,
        ]);

        return redirect()->route('admin.stocks.index')->with('success', 'Stock added successfully.');
    }

    public function edit(Stock $stock)
    {
        if (Auth::user()->role !== 'admin' && $stock->branch_id !== session('active_branch_id')) {
            abort(403);
        }

        $products = Product::where('branch_id', session('active_branch_id'))->get();
        return view('admin.stocks.edit', compact('stock', 'products'));
    }

    public function update(Request $request, Stock $stock)
    {
        if (Auth::user()->role !== 'admin' && $stock->branch_id !== session('active_branch_id')) {
            abort(403);
        }

        $request->validate([
            'product_id'      => 'required|exists:products,id',
            'supplier_name'   => 'required|string|max:255',
            'buying_price'    => 'required|numeric|min:0',
            'quantity'        => 'required|integer|min:1',
            'deposit_amount'  => 'nullable|numeric|min:0',
            'purchase_date'   => 'required|date',
        ]);

        $stock->update($request->only('product_id', 'supplier_name', 'buying_price', 'quantity', 'deposit_amount', 'purchase_date'));

        return redirect()->route('admin.stocks.index')->with('success', 'Stock updated successfully.');
    }

    public function show(Stock $stock)
    {
        if (Auth::user()->role !== 'admin' && $stock->branch_id !== session('active_branch_id')) {
            abort(403);
        }

        return view('admin.stocks.show', compact('stock'));
    }

    public function destroy(Stock $stock)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $stock->delete();

        return redirect()->route('admin.stocks.index')->with('success', 'Stock deleted successfully.');
    }
}
