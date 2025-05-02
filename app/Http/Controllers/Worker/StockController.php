<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin,worker');
    }
    public function index(Request $request)
    {
        $query = Stock::query();

        // Worker বা worker হলে নিজের ব্রাঞ্চের স্টক
        if (Auth::user()->role == 'worker') {
            $query->where('branch_id', session('active_branch_id'));
        }

        $stocks = $query->latest()->paginate(20);

        return view('worker.stocks.index', compact('stocks'));
    }

    public function create()
    {
        $branches = Branch::all();
        $products = Product::where('branch_id', session('active_branch_id'))->get();
        return view('worker.stocks.create', compact('products', 'branches'));
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

        return redirect()->route('worker.stocks.index')->with('success', 'Stock added successfully.');
    }

    public function edit(Stock $stock)
    {
        $products = Product::where('branch_id', session('active_branch_id'))->get();
        return view('worker.stocks.edit', compact('stock', 'products'));
    }

    public function update(Request $request, Stock $stock)
    {

        $request->validate([
            'product_id'      => 'required|exists:products,id',
            'supplier_name'   => 'required|string|max:255',
            'buying_price'    => 'required|numeric|min:0',
            'quantity'        => 'required|integer|min:1',
            'deposit_amount'  => 'nullable|numeric|min:0',
            'purchase_date'   => 'required|date',
        ]);

        $stock->update($request->all());

        return redirect()->route('worker.stocks.index')->with('success', 'Stock updated successfully.');
    }

    public function show(Stock $stock)
    {
        return view('worker.stocks.show', compact('stock'));
    }
}

