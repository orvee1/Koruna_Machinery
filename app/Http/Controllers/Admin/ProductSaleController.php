<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ProductSale;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductSaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin');
    }

    public function index(Request $request)
    {
        $branchId = session('active_branch_id');

        $query = ProductSale::where('branch_id', $branchId)
            ->with(['stock.product', 'customer', 'branch', 'seller', 'investor'])
            ->latest();

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }
        if ($request->filled('month')) {
            $query->forMonth($request->input('month'));
        }
        if ($request->filled('year')) {
            $query->forYear($request->input('year'));
        }

        $sales = $query->paginate(20);
        return view('admin.product-sales.index', compact('sales'));
    }

    public function create()
    {
        $branchId = session('active_branch_id', Auth::user()->branch_id);
        $stocks = Stock::where('branch_id', $branchId)->get();
        $customers = Customer::where('branch_id', $branchId)->get();

        return view('admin.product-sales.create', compact('stocks', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        // ✅ **স্টক খুঁজে বের করুন**
        $stock = Stock::findOrFail($validated['stock_id']);

        // ✅ **পর্যাপ্ত পরিমাণ স্টকে আছে কিনা চেক করুন**
        if ($stock->quantity < $validated['quantity']) {
            return redirect()->route('admin.product-sales.create')
                ->with('error', 'Insufficient stock available for this product.');
        }

        // ✅ **স্টক থেকে কোয়ান্টিটি কমানো হচ্ছে**
        $stock->quantity -= $validated['quantity'];

        // ✅ **প্রফিট হিসাব করে যোগ করা হচ্ছে**
        $profitPerUnit = $validated['unit_price'] - $stock->buying_price;
        $totalProfit = $profitPerUnit * $validated['quantity'];
        $stock->total_profit += $totalProfit;

        // ✅ **স্টক সেভ করা হচ্ছে**
        $stock->save();

        // ✅ **সেল রেকর্ড তৈরি করা হচ্ছে**
        $user = Auth::user();
        ProductSale::create([
            'branch_id' => session('active_branch_id'),
            'stock_id' => $validated['stock_id'],
            'customer_id' => $validated['customer_id'],
            'seller_id' => $user->id,
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'paid_amount' => $validated['paid_amount'],
            'due_amount' => ($validated['quantity'] * $validated['unit_price']) - $validated['paid_amount'],
            'profit' => $totalProfit,
        ]);

        return redirect()->route('admin.product-sales.index')->with('success', 'Product Sale added successfully.');
    }

    public function show(ProductSale $productSale)
    {
        $user = Auth::user();
        return view('admin.product-sales.show', compact('productSale'));
    }

    public function destroy(ProductSale $productSale)
    {
        // ✅ **স্টক ফেরত আসবে**
        $stock = Stock::find($productSale->stock_id);

        if ($stock) {
            $stock->quantity += $productSale->quantity;

            // ✅ **প্রফিট অ্যাডজাস্ট হচ্ছে**
            $profitPerUnit = $productSale->unit_price - $stock->buying_price;
            $totalProfit = $profitPerUnit * $productSale->quantity;

            $stock->total_profit -= $totalProfit;
            $stock->save();
        }

        $productSale->delete();
        return redirect()->route('admin.product-sales.index')->with('success', 'Product Sale deleted successfully and Stock restored.');
    }
}
