<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ProductSale;
use App\Models\ProductSalePayment;
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
        ->with(['stock', 'customer', 'branch', 'seller', 'investor']);

    if ($request->filled('date')) {
        $query->whereDate('created_at', $request->input('date'));
    }
    if ($request->filled('month')) {
        $query->forMonth($request->input('month'));
    }
    if ($request->filled('year')) {
        $query->forYear($request->input('year'));
    }
    if ($request->filled('status')) {
        if ($request->status === 'due') {
            $query->where('due_amount', '>', 0);
        } elseif ($request->status === 'paid') {
            $query->where('due_amount', 0);
        }
    }

    $sales = $query->get();

    $salesGrouped = $sales->groupBy(fn($s) => $s->customer_id . '_' . $s->created_at->toDateString())
        ->map(function ($group) {
            $first = $group->first();
            return [
                'customer' => $first->customer,
                'sales' => $group,
                'total' => $group->sum('total_amount'),
                'paid' => $group->sum('paid_amount'),
                'due' => $group->sum('due_amount'),
            ];
        })->values();

    return view('admin.product-sales.index', compact('salesGrouped'));
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

        $stock = Stock::findOrFail($validated['stock_id']);

        if ($stock->quantity < $validated['quantity']) {
            return redirect()->route('admin.product-sales.create')
                ->with('error', 'Insufficient stock available for this product.');
        }

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

        public function updatePayment(Request $request, ProductSale $productSale)
    {
        $request->validate([
            'paid_amount' => 'required|decimal:0,2|min:0.01|max:' . $productSale->due_amount,
            'payment_date' => 'required|date',
        ]);

        $payment = new ProductSalePayment([
            'paid_amount' => $request->paid_amount,
            'payment_date' => $request->payment_date,
            'product_sale_id' => $productSale->id,
        ]);
        $payment->save();

        $productSale->paid_amount += $request->paid_amount;
        $productSale->due_amount = max($productSale->total_amount - $productSale->paid_amount, 0);

        $productSale->save();

        return back()->with('success', 'Payment updated successfully.');
    }


     public function show($id)
    {
        $productSale = ProductSale::with('payments')->findOrFail($id);
        return view('admin.product-sales.show', compact('productSale'));
    }

    public function destroy(ProductSale $productSale)
    {
        $productSale->delete();

        return redirect()->route('admin.product-sales.index')
            ->with('success', 'Product Sale deleted successfully and Stock restored.');
    }

}
