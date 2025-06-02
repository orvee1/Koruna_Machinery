<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductPayment;
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
        $branchId = auth()->user()->branch_id;
        $search = $request->get('search');
        $date = $request->get('date');

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

        return view('worker.stocks.index', compact('stocks', 'search', 'date'));
    }

    public function create()
    {
        $branchId = auth()->user()->branch_id;
        $branch = Branch::find($branchId);

        return view('worker.stocks.create', compact('branch'));
    }

    public function store(Request $request)
    {
        $branchId = auth()->user()->branch_id;

        $data = $request->validate([
            'product_name' => 'required|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'buying_price' => 'required|numeric|min:0|max:99999999.99',
            'quantity' => 'required|integer|min:1',
            'deposit_amount' => 'nullable|numeric|min:0|max:99999999.99',
            'purchase_date' => 'required|date',
        ]);

        $data['branch_id'] = $branchId;

        Stock::create($data);

        return redirect()->route('worker.stocks.index')->with('success', 'Stock added successfully.');
    }

    public function edit(Stock $stock)
    {

        return view('worker.stocks.edit', compact('stock'));
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

        $previousQuantity = $stock->quantity;

        $stock->update($data);

        $stock->quantity = ($stock->quantity - $previousQuantity) + $data['quantity'];
        if ($stock->quantity < 0) {
            $stock->quantity = 0;
        }
        $stock->save();

        return redirect()->route('worker.stocks.index')->with('success', 'Stock updated successfully.');
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
        $stock = Stock::with('payments')->findOrFail($id);
        
        return view('worker.stocks.show', compact('stock'));
    }
}

