<?php

namespace App\Http\Controllers\Admin;

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
        $this->middleware('checkRole:admin');
    }

   public function index(Request $request)
{
    $branchId = session('active_branch_id');

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

    return view('admin.partstock-sales.index', compact('salesGrouped'));
}

    public function create()
    {
        $branchId = session('active_branch_id');
        $partStocks = PartStock::where('branch_id', $branchId)->get();
        $customers = Customer::where('branch_id', $branchId)->get();

        return view('admin.partstock-sales.create', compact('partStocks', 'customers'));
    }

        public function store(Request $request)
    {
        $validated = $request->validate([
            'part_stock_id' => 'required|exists:part_stocks,id',
            'customer_id'   => 'required|exists:customers,id',
            'quantity'      => 'required|integer|min:1',
            'unit_price'    => 'required|numeric|min:0|max:99999999.99',
            'paid_amount'   => 'nullable|numeric|min:0|max:99999999.99',
        ]);

        $partStock = PartStock::findOrFail($validated['part_stock_id']);

        if ($partStock->quantity < $validated['quantity']) {
            return redirect()->route('admin.partstock-sales.create')
                ->with('error', 'Insufficient stock available for this item.');
        }

        $totalAmount = $validated['quantity'] * $validated['unit_price'];
        $paid = $validated['paid_amount'] ?? 0;
        $due = max($totalAmount - $paid, 0);

        PartStockSale::create([
            'branch_id'      => session('active_branch_id'),
            'part_stock_id'  => $validated['part_stock_id'],
            'customer_id'    => $validated['customer_id'],
            'seller_id'      => Auth::id(),
            'quantity'       => $validated['quantity'],
            'unit_price'     => $validated['unit_price'],
            'paid_amount'    => $paid,
            'total_amount'   => $totalAmount,
            'due_amount'     => $due,
            'payment_status' => $due <= 0 ? 'paid' : 'due',
        ]);

        return redirect()->route('admin.partstock-sales.index')
            ->with('success', 'Part stock sale added successfully.');
    }


    public function updatePayment(Request $request, PartStockSale $partStockSale)
    {
        $request->validate([
            'paid_amount' => 'required|decimal:0,2|min:0.01|max:' . $partStockSale->due_amount,
            'payment_date' => 'required|date',
        ]);

        $payment = new PartStockSalePayment([
            'paid_amount' => $request->paid_amount,
            'payment_date' => $request->payment_date,
            'partstock_sale_id' => $partStockSale->id,
        ]);
        $payment->save();

        $partStockSale->paid_amount += $request->paid_amount;
        $partStockSale->due_amount = max($partStockSale->total_amount - $partStockSale->paid_amount, 0);

        $partStockSale->save();

        return back()->with('success', 'Payment updated successfully.');
    }

    public function show($id)
    {
        $partStockSale = PartStockSale::with('payments')->findOrFail($id);
        return view('admin.partstock-sales.show', compact('partStockSale'));
    }

    public function destroy(PartStockSale $partStockSale)
    {
        $partStockSale->delete();

        return redirect()->route('admin.partstock-sales.index')
            ->with('success', 'Partstock sale deleted successfully.');
    }
}
