<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Stock;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin');
    }

    // স্টক এন্ট্রির তালিকা দেখাবে
    public function index()
    {
        $stocks = Stock::with('branch')->latest()->paginate(20);
        return view('admin.stocks.index', compact('stocks'));
    }

    // নতুন স্টক এন্ট্রির ফর্ম
    public function create()
    {
        $branchId = session('active_branch_id');

        if (!$branchId) {
            return redirect()->route('admin.select-branch')->with('error', 'Please select a branch first.');
        }

        $branch = Branch::find($branchId);
        return view('admin.stocks.create', compact('branch'));
    }

    // স্টক এন্ট্রি সেভ এবং প্রোডাক্ট সিঙ্ক
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_name' => 'required|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'buying_price' => 'required|numeric|min:0|max:99999999.99',
            'selling_price' => 'required|numeric|min:0|max:99999999.99',
            'quantity' => 'required|integer|min:1',
            'deposit_amount' => 'nullable|numeric|min:0|max:99999999.99',
            'purchase_date' => 'required|date',
        ]);

        $branchId = session('active_branch_id');

        if (!$branchId) {
            return redirect()->route('admin.select-branch')->with('error', 'Please select a branch before adding stock.');
        }

        $data['branch_id'] = $branchId;
        $data['total_amount'] = $data['buying_price'] * $data['quantity'];
        $data['due_amount'] = $data['total_amount'] - ($data['deposit_amount'] ?? 0);

        // স্টক তৈরি
        $stock = Stock::create($data);

        // প্রোডাক্ট তৈরি বা আপডেট করুন
        $product = Product::firstOrNew(['name' => $data['product_name'], 'branch_id' => $branchId]);
        $product->stock_quantity += $data['quantity']; // স্টক বাড়ানোর পরিমাণ
        $product->buying_price = $data['buying_price'];
        $product->selling_price = $data['selling_price'];
        $product->last_purchase_date = $data['purchase_date'];
        $product->save();

        return redirect()->route('admin.stocks.index')->with('success', 'স্টক সফলভাবে তৈরি হয়েছে এবং পণ্য সিঙ্ক করা হয়েছে।');
    }

    // স্টক এডিট ফর্ম
    public function edit(Stock $stock)
    {
        return view('admin.stocks.edit', compact('stock'));
    }

    // স্টক এন্ট্রি আপডেট
    public function update(Request $request, Stock $stock)
    {
        $data = $request->validate([
            'product_name' => 'required|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'buying_price' => 'required|numeric|min:0|max:99999999.99',
            'selling_price' => 'required|numeric|min:0|max:99999999.99',
            'quantity' => 'required|integer|min:1',
            'deposit_amount' => 'nullable|numeric|min:0|max:99999999.99',
            'purchase_date' => 'required|date',
        ]);

        $data['total_amount'] = $data['buying_price'] * $data['quantity'];
        $data['due_amount'] = $data['total_amount'] - ($data['deposit_amount'] ?? 0);

        $stock->update($data);

        // প্রোডাক্ট আপডেট করুন
        $product = Product::where('name', $data['product_name'])->where('branch_id', $stock->branch_id)->first();
        $product->stock_quantity += $data['quantity']; // স্টক বাড়ানোর পরিমাণ
        $product->save();

        return redirect()->route('admin.stocks.index')->with('success', 'Stock entry updated successfully.');
    }

    // স্টক ডিলিট
    public function destroy(Stock $stock)
    {
        $stock->delete();

        // প্রোডাক্টের স্টক কমিয়ে দিন
        $product = Product::where('name', $stock->product_name)->where('branch_id', $stock->branch_id)->first();
        $product->stock_quantity -= $stock->quantity; // স্টক কমানোর পরিমাণ
        $product->save();

        return redirect()->route('admin.stocks.index')->with('success', 'Stock entry deleted successfully.');
    }
}
