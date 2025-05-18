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
            ->with(['stock', 'customer', 'branch', 'seller', 'investor'])
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
            'unit_price' => 'required|numeric|min:0|max:99999999.99',
            'paid_amount' => 'nullable|numeric|min:0|max:99999999.99',
        ]);

        // ✅ **স্টক খুঁজে বের করা**
        $stock = Stock::findOrFail($validated['stock_id']);

        // ✅ **পর্যাপ্ত পরিমাণ স্টকে আছে কিনা চেক করুন**
        if ($stock->quantity < $validated['quantity']) {
            return redirect()->route('admin.product-sales.create')
                ->with('error', 'Insufficient stock available for this product.');
        }

        // ✅ **সেল রেকর্ড তৈরি করা হচ্ছে**
        $productSale = ProductSale::create([
            'branch_id' => session('active_branch_id'),
            'stock_id' => $validated['stock_id'],
            'customer_id' => $validated['customer_id'],
            'seller_id' => Auth::id(),
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'paid_amount' => $validated['paid_amount'],
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

        $productSale->delete();

        return redirect()->route('admin.product-sales.index')
            ->with('success', 'Product Sale deleted successfully and Stock restored.');
    }

}
