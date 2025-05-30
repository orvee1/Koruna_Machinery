<?php

namespace App\Http\Controllers\Manager;

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
        $this->middleware('checkRole:manager');
    }

    public function index(Request $request)
    {
        $branchId = auth()->user()->branch_id;
        $user = Auth::user();

        $query = ProductSale::with(['stock', 'customer', 'branch', 'seller', 'investor'])
            ->where('branch_id', $branchId);

        if ($user->role === 'worker') {
            $query->forToday()->where('seller_id', $user->id);
        } else {
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
        }

        $sales = $query->latest()->paginate(20);
        return view('manager.product-sales.index', compact('sales'));
    }

    public function create()
    {
        $branchId = auth()->user()->branch_id;

        $stocks = Stock::where('branch_id', $branchId)->get();
        $customers = Customer::where('branch_id', $branchId)->get();

        return view('manager.product-sales.create', compact('stocks', 'customers'));
    }

    public function store(Request $request)
    {
        $branchId = auth()->user()->branch_id;
        $userId = Auth::id();

        $validated = $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0|max:99999999.99',
            'paid_amount' => 'nullable|numeric|min:0|max:99999999.99',
        ]);

        $stock = Stock::where('branch_id', $branchId)->findOrFail($validated['stock_id']);

        if ($stock->quantity < $validated['quantity']) {
            return redirect()->route('manager.product-sales.create')
                ->with('error', 'Insufficient stock available for this product.');
        }

        $totalAmount = $validated['quantity'] * $validated['unit_price'];
        $paid = $validated['paid_amount'] ?? 0;
        $due = max($totalAmount - $paid, 0);

        ProductSale::create([
            'branch_id' => $branchId,
            'stock_id' => $validated['stock_id'],
            'customer_id' => $validated['customer_id'],
            'seller_id' => $userId,
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'paid_amount' => $paid,
            'total_amount' => $totalAmount,
            'due_amount' => $due,
            'payment_status' => $due <= 0 ? 'paid' : 'due',
        ]);

        return redirect()->route('manager.product-sales.index')->with('success', 'Product Sale added successfully.');
    }

    public function edit(ProductSale $productSale)
    {
        $branchId = auth()->user()->branch_id;

        if ($productSale->branch_id != $branchId) {
            abort(403);
        }

        $stocks = Stock::where('branch_id', $branchId)->get();
        $customers = Customer::where('branch_id', $branchId)->get();

        return view('manager.product-sales.edit', compact('productSale', 'stocks', 'customers'));
    }

    public function update(Request $request, ProductSale $productSale)
    {
        $branchId = auth()->user()->branch_id;

        if ($productSale->branch_id != $branchId) {
            abort(403);
        }

        $validated = $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $totalAmount = $validated['quantity'] * $validated['unit_price'];
        $dueAmount = max($totalAmount - $validated['paid_amount'], 0);

        $productSale->update([
            'stock_id' => $validated['stock_id'],
            'customer_id' => $validated['customer_id'],
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'paid_amount' => $validated['paid_amount'],
            'total_amount' => $totalAmount,
            'due_amount' => $dueAmount,
            'payment_status' => $dueAmount <= 0 ? 'paid' : 'due',
        ]);

        return redirect()->route('manager.product-sales.index')->with('success', 'Product Sale updated successfully.');
    }

    public function updatePayment(Request $request, ProductSale $productSale)
    {
        if ($productSale->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        $request->validate([
            'paid_amount' => 'required|decimal:0,2|min:0.01|max:' . $productSale->due_amount,
            'payment_date' => 'required|date',
        ]);

        ProductSalePayment::create([
            'paid_amount' => $request->paid_amount,
            'payment_date' => $request->payment_date,
            'product_sale_id' => $productSale->id,
        ]);

        $productSale->paid_amount += $request->paid_amount;
        $productSale->due_amount = max($productSale->total_amount - $productSale->paid_amount, 0);
        $productSale->payment_status = $productSale->due_amount <= 0 ? 'paid' : 'due';
        $productSale->save();

        return back()->with('success', 'Payment updated successfully.');
    }

    public function show(ProductSale $productSale)
    {
        if ($productSale->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        $productSale->load('payments');
        return view('manager.product-sales.show', compact('productSale'));
    }
}
