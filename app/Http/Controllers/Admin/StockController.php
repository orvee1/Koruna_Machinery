<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Stock;
use App\Models\Product;
use App\Models\ProductPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin');
    }

    /**
     * স্টক এন্ট্রির তালিকা দেখাবে
     */
    public function index()
    {
        $stocks = Stock::with('branch')->latest()->paginate(20);
        return view('admin.stocks.index', compact('stocks'));
    }

    /**
     * নতুন স্টক এন্ট্রি ফর্ম
     */
    public function create()
    {
        // সেশন থেকে ব্রাঞ্চ আইডি নিয়ে আসুন
        $branchId = session('active_branch_id');
    
        // যদি ব্রাঞ্চ সিলেক্ট না থাকে, তখন ব্রাঞ্চ সিলেক্ট পেইজে রিডাইরেক্ট করুন
        if (!$branchId) {
            return redirect()->route('admin.select-branch')
                             ->with('error', 'Please select a branch first.');
        }
    
        // এক্সিকিউটেড ব্রাঞ্চের তথ্য পাঠানো হবে
        $branch = Branch::find($branchId);
        
        return view('admin.stocks.create', compact('branch'));
    }
    
    

    /**
     * নতুন স্টক এন্ট্রি সেভ এবং প্রোডাক্ট সিঙ্ক
     */
    public function store(Request $request)
    {
        // ১) ইনপুট ভ্যালিডেশন (branch_id সরিয়ে দেওয়া হয়েছে)
        $data = $request->validate([
            'product_name'   => 'required|string|max:255',
            'supplier_name'  => 'required|string|max:255',
            'buying_price'   => 'required|numeric|min:0|max:99999999.99',
            'selling_price'  => 'required|numeric|min:0|max:99999999.99',
            'quantity'       => 'required|integer|min:1',
            'deposit_amount' => 'nullable|numeric|min:0|max:99999999.99',
            'purchase_date'  => 'required|date',
        ]);
    
        // ২) সেশন থেকে ব্রাঞ্চ আইডি নিন
        $branchId = session('active_branch_id');
    
        // ৩) যদি সেশন না থাকে, প্রথমে ব্রাঞ্চ সিলেক্ট পেজে পাঠিয়ে দিন
        if (! $branchId) {
            return redirect()
                ->route('admin.select-branch')
                ->with('error', 'Please select a branch before adding stock.');
        }
    
        // ৪) প্রয়োজনীয় ফিল্ডগুলো তৈরি
        $data['branch_id']     = $branchId;
        $data['total_amount']  = $data['buying_price'] * $data['quantity'];
        $data['due_amount']    = $data['total_amount'] - ($data['deposit_amount'] ?? 0);
    
        // ৫) স্টক ক্রিয়েট করুন
        Stock::create($data);
    
        return redirect()
            ->route('admin.stocks.index')
            ->with('success', 'স্টক সফলভাবে তৈরি হয়েছে এবং পণ্য সিঙ্ক করা হয়েছে।');
    }
    

    /**
     * স্টক এডিট ফর্ম
     */
    public function edit(Stock $stock)
    {
        return view('admin.stocks.edit', compact('stock'));
    }

    /**
     * স্টক এন্ট্রি আপডেট
     */
    public function update(Request $request, Stock $stock)
    {
        $data = $request->validate([
            'product_name'   => 'required|string|max:255',
            'supplier_name'  => 'required|string|max:255',
            'buying_price'   => 'required|numeric|min:0|max:99999999.99',
            'selling_price'  => 'required|numeric|min:0|max:99999999.99',
            'quantity'       => 'required|integer|min:1',
            'deposit_amount' => 'nullable|numeric|min:0|max:99999999.99',
            'purchase_date'  => 'required|date',
        ]);

        $data['total_amount'] = $data['buying_price'] * $data['quantity'];
        $data['due_amount']   = $data['total_amount'] - ($data['deposit_amount'] ?? 0);

        $stock->update($data);

        return redirect()
            ->route('admin.stocks.index')
            ->with('success', 'Stock entry updated successfully.');
    }

    /**
     * এক স্টক এন্ট্রি দেখার পেজ
     */
    public function show(Stock $stock)
    {
        return view('admin.stocks.show', compact('stock'));
    }

    /**
     * স্টক এন্ট্রি ডিলিট
     */
    public function destroy(Stock $stock)
    {
        $stock->delete();

        return redirect()
            ->route('admin.stocks.index')
            ->with('success', 'Stock entry deleted successfully.');
    }

    /**
     * স্টকের সাথে সম্পর্কিত প্রোডাক্টে পেমেন্ট অ্যাড/আপডেট
     */
    public function updatePayment(Request $request, Stock $stock)
    {
        // Validate
        $data = $request->validate([
            'paid_amount'  => 'required|numeric|min:0|max:99999999.99',
            'payment_date' => 'required|date',
        ]);
    
        // Create new StockPayment
        $stock->payments()->create([
            'paid_amount'  => $data['paid_amount'],
            'payment_date' => $data['payment_date'],
        ]);
    
        return redirect()
            ->route('admin.stocks.show', $stock)
            ->with('success', "Payment of {$data['paid_amount']} ৳ recorded on {$data['payment_date']}.");
    }
    
}
