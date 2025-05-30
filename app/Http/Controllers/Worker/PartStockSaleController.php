<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PartStock;
use App\Models\PartStockSale;
use App\Models\PartStockSalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartStockSaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin,worker');
    }

      public function index(Request $request)
    {
        $branchId = auth()->user()->branch_id;

        $query = PartStockSale::where('branch_id', $branchId)
            ->with(['partStock', 'customer', 'branch', 'seller', 'investor']);

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

        return view('worker.partstock-sales.index', compact('salesGrouped'));
    }

    public function create()
    {
        $branchId = auth()->user()->branch_id;
        $partStocks = PartStock::where('branch_id', $branchId)->get();
        $customers = Customer::where('branch_id', $branchId)->get();

        return view('worker.partstock-sales.create', compact('partStocks', 'customers'));
    }

    public function store(Request $request)
    {
        $branchId = auth()->user()->branch_id;

        $validated = $request->validate([
            'part_stock_id' => 'required|exists:part_stocks,id',
            'customer_id'   => 'required|exists:customers,id',
            'quantity'      => 'required|integer|min:1',
            'unit_price'    => 'required|numeric|min:0|max:99999999.99',
            'paid_amount'   => 'nullable|numeric|min:0|max:99999999.99',
        ]);

        $partStock = PartStock::where('id', $validated['part_stock_id'])
            ->where('branch_id', $branchId)
            ->firstOrFail();

        $customer = Customer::where('id', $validated['customer_id'])
            ->where('branch_id', $branchId)
            ->firstOrFail();

        if ($partStock->quantity < $validated['quantity']) {
            return redirect()->route('worker.partstock-sales.create')
                ->with('error', 'Insufficient stock available for this item.');
        }

        $totalAmount = $validated['quantity'] * $validated['unit_price'];
        $paid = $validated['paid_amount'] ?? 0;
        $due = max($totalAmount - $paid, 0);

        PartStockSale::create([
            'branch_id'      => $branchId,
            'part_stock_id'  => $validated['part_stock_id'],
            'customer_id'    => $validated['customer_id'],
            'seller_id'      => Auth::id(),
            'quantity'       => $validated['quantity'],
            'unit_price'     => $validated['unit_price'],
            'total_amount'   => $totalAmount,
            'paid_amount'    => $paid,
            'due_amount'     => $due,
            'payment_status' => $due <= 0 ? 'paid' : 'due',
        ]);

        return redirect()->route('worker.partstock-sales.index')
            ->with('success', 'Part stock sale added successfully.');
    }

    public function edit(PartStockSale $partStockSale)
    {
        $branchId = auth()->user()->branch_id;

        if ($partStockSale->branch_id !== $branchId) {
            abort(403, 'Unauthorized access.');
        }

        $partStocks = PartStock::where('branch_id', $branchId)->get();
        $customers = Customer::where('branch_id', $branchId)->get();

        return view('worker.partstock-sales.edit', compact('partStockSale', 'partStocks', 'customers'));
    }

    public function update(Request $request, PartStockSale $partStockSale)
    {
        $branchId = auth()->user()->branch_id;

        if ($partStockSale->branch_id !== $branchId) {
            abort(403, 'Unauthorized update.');
        }

        $validated = $request->validate([
            'part_stock_id' => 'required|exists:part_stocks,id',
            'customer_id'   => 'required|exists:customers,id',
            'quantity'      => 'required|integer|min:1',
            'unit_price'    => 'required|numeric|min:0|max:99999999.99',
            'paid_amount'   => 'nullable|numeric|min:0|max:99999999.99',
        ]);

        $totalAmount = $validated['quantity'] * $validated['unit_price'];
        $paid = $validated['paid_amount'] ?? 0;
        $due = max($totalAmount - $paid, 0);

        $partStockSale->update([
            'part_stock_id' => $validated['part_stock_id'],
            'customer_id'   => $validated['customer_id'],
            'quantity'      => $validated['quantity'],
            'unit_price'    => $validated['unit_price'],
            'total_amount'  => $totalAmount,
            'paid_amount'   => $paid,
            'due_amount'    => $due,
            'payment_status' => $due <= 0 ? 'paid' : 'due',
        ]);

        return redirect()->route('worker.partstock-sales.index')
            ->with('success', 'Part stock sale updated successfully.');
    }

    public function updatePayment(Request $request, PartStockSale $partStockSale)
    {
        $branchId = auth()->user()->branch_id;

        if ($partStockSale->branch_id !== $branchId) {
            abort(403, 'Unauthorized payment update.');
        }

        $request->validate([
            'paid_amount' => 'required|decimal:0,2|min:0.01|max:' . $partStockSale->due_amount,
            'payment_date' => 'required|date',
        ]);

        PartStockSalePayment::create([
            'paid_amount' => $request->paid_amount,
            'payment_date' => $request->payment_date,
            'partstock_sale_id' => $partStockSale->id,
        ]);

        $partStockSale->paid_amount += $request->paid_amount;
        $partStockSale->due_amount = max($partStockSale->total_amount - $partStockSale->paid_amount, 0);
        $partStockSale->payment_status = $partStockSale->due_amount <= 0 ? 'paid' : 'due';
        $partStockSale->save();

        return back()->with('success', 'Payment updated successfully.');
    }

    public function show($id)
    {
        $branchId = auth()->user()->branch_id;

        $partStockSale = PartStockSale::with('payments')->findOrFail($id);

        if ($partStockSale->branch_id !== $branchId) {
            abort(403, 'Unauthorized view access.');
        }

        return view('worker.partstock-sales.show', compact('partStockSale'));
    }

}

