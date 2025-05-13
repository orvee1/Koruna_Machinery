<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Stock;
use App\Models\ProductPayment;
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
        $branchId = session('active_branch_id');
        $stocks = Stock::where('branch_id', $branchId)
            ->with('branch')->latest()->paginate(20);

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
 
    // ✅ **স্টক এন্ট্রি সেভ এবং প্রোডাক্ট সিঙ্ক**
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_name' => 'required|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'buying_price' => 'required|numeric|min:0|max:99999999.99',
            'quantity' => 'required|integer|min:1',
            'deposit_amount' => 'nullable|numeric|min:0|max:99999999.99',
            'purchase_date' => 'required|date',
        ]);

        $branchId = session('active_branch_id');

        if (!$branchId) {
            return redirect()->route('admin.select-branch')->with('error', 'Please select a branch before adding stock.');
        }

        $data['branch_id'] = $branchId;

        // ✅ **স্টক তৈরি হচ্ছে** (Model Event Listener কাজ করবে এখানে)
        Stock::create($data);

        return redirect()->route('admin.stocks.index')->with('success', 'Stock entry created successfully.');
    }



    // স্টক এডিট ফর্ম
    public function edit(Stock $stock)
    {
        return view('admin.stocks.edit', compact('stock'));
    }

    // ✅ **স্টক এন্ট্রি আপডেট**
        public function update(Request $request, Stock $stock)
    {
        $data = $request->validate([
            'product_name' => 'required|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'buying_price' => 'required|numeric|min:0|max:99999999.99',
            'quantity' => 'required|integer|min:1',
            'deposit_amount' => 'nullable|numeric|min:0|max:99999999.99',
            'purchase_date' => 'required|date',
        ]);

        $branchId = session('active_branch_id');

        $previousQuantity = $stock->quantity;

        $stock->update($data);

        $stock = Stock::where('product_name', $data['product_name'])
            ->where('branch_id', $branchId)
            ->first();

        if ($stock) {
            $stock->quantity = ($stock->quantity - $previousQuantity) + $data['quantity'];
            if ($stock->quantity < 0) {
                $stock->quantity = 0;
            }
            $stock->save();
        }

        return redirect()->route('admin.stocks.index')->with('success', 'Stock entry updated successfully.');
    }


    public function updatePayment(Request $request, Stock $stock)
    {
        $request->validate([
            'paid_amount' => 'required|decimal:0,2',
            'payment_date' => 'required|date',
        ]);
    
        // Create new payment record for the part stock
        $payment = new ProductPayment([
            'paid_amount' => $request->paid_amount,
            'payment_date' => $request->payment_date,
            'stock_id' => $stock->id,
        ]);
    
        // Save payment related to the part stock
        $payment->save();

            $stock->due_amount -= $request->paid_amount;
        if ($stock->due_amount < 0) {
            $stock->due_amount = 0;
        }
        $stock->save();
    
        return back()->with('success', 'Payment updated successfully.');
    }

    public function show($id)
    {
        $stock = Stock::with('payments')->find($id);

        if (!$stock) {
            return redirect()->route('admin.stocks.index')->with('error', 'Stock not found.');
        }

        return view('admin.stocks.show', compact('stock'));
    }

}
