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

    public function index(Request $request)
    {
        $date = $request->get('date');
        $search = $request->get('search', '');
        $branchId = session('active_branch_id');

        if (!$branchId) {
            return redirect()
                ->route('admin.select-branch')
                ->with('error', 'Please select a branch first.');
        }

        $query = Stock::where('branch_id', $branchId)
            ->with('branch')
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('supplier_name', 'like', "%{$search}%");
            });
        }

        if ($date) {
            $query->whereDate('purchase_date', $date);
        }

        $stocks = $query->paginate(20);

        return view('admin.stocks.index', compact('stocks', 'date', 'search'));
    }

    public function create()
    {
        $branchId = session('active_branch_id');

        if (!$branchId) {
            return redirect()->route('admin.select-branch')->with('error', 'Please select a branch first.');
        }

        $branch = Branch::find($branchId);
        return view('admin.stocks.create', compact('branch'));
    }

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

        Stock::create($data);

        return redirect()->route('admin.stocks.index')->with('success', 'Stock entry created successfully.');
    }

    public function edit(Stock $stock)
    {
        return view('admin.stocks.edit', compact('stock'));
    }

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
            'paid_amount' => 'required|decimal:0,2|min:0.01|max:' . $stock->due_amount,
            'payment_date' => 'required|date',
        ]);

        ProductPayment::create([
            'paid_amount' => $request->paid_amount,
            'payment_date' => $request->payment_date,
            'stock_id' => $stock->id,
        ]);

        $stock->deposit_amount += $request->paid_amount;

        $stock->due_amount = max($stock->total_amount - $stock->deposit_amount, 0);
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
