<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\PartStock;
use App\Models\PartStockPayment;
use Illuminate\Http\Request;

class PartStockController extends Controller
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

        $query = PartStock::where('branch_id', $branchId)
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

        $partstocks = $query->paginate(20);

        return view('admin.partstocks.index', compact('partstocks', 'date', 'search'));
    }

    public function create()
    {
        $branchId = session('active_branch_id');

        if (!$branchId) {
            return redirect()
                ->route('admin.select-branch')
                ->with('error', 'Please select a branch first.');
        }

        $branch = Branch::findOrFail($branchId);
        return view('admin.partstocks.create', compact('branch'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name'    => 'required|string|max:255',
            'supplier_name'   => 'required|string|max:255',
            'buy_value'       => 'required|numeric|min:0',
            'quantity'        => 'required|integer|min:1',
            'sell_value'      => 'required|numeric|min:0',
            'deposit_amount'  => 'nullable|numeric|min:0',
            'purchase_date'   => 'required|date',

        ]);

        $branchId = session('active_branch_id');

        PartStock::create([
            'branch_id'       => $branchId,
            'product_name'    => $request->product_name,
            'supplier_name'   => $request->supplier_name,
            'buy_value'       => $request->buy_value,
            'quantity'        => $request->quantity,
            'sell_value'      => $request->sell_value,
            'deposit_amount'  => $request->deposit_amount ?? 0,
            'purchase_date'   => $request->purchase_date,
        ]);

        return redirect()
            ->route('admin.partstocks.index')
            ->with('success', 'Part stock added successfully.');
    }

    public function edit(PartStock $partStock)
    {
        if ($partStock->branch_id !== session('active_branch_id')) {
            abort(403, 'Unauthorized action.');
        }

        $branch = Branch::find(session('active_branch_id'));
        return view('admin.partstocks.edit', compact('partStock', 'branch'));
    }

    public function update(Request $request, PartStock $partStock)
    {
        if ($partStock->branch_id !== session('active_branch_id')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'product_name'    => 'required|string|max:255',
            'supplier_name'   => 'required|string|max:255',
            'buy_value'       => 'required|numeric|min:0',
            'quantity'        => 'required|integer|min:1',
            'sell_value'      => 'required|numeric|min:0',
            'deposit_amount'  => 'nullable|numeric|min:0',
            'purchase_date'   => 'required|date',
        ]);

        $partStock->update([
            'product_name'    => $request->product_name,
            'supplier_name'   => $request->supplier_name,
            'buy_value'       => $request->buy_value,
            'quantity'        => $request->quantity,
            'sell_value'      => $request->sell_value,
            'deposit_amount'  => $request->deposit_amount ?? 0,
            'purchase_date'   => $request->purchase_date,
        ]);

        return redirect()
            ->route('admin.partstocks.index')
            ->with('success', 'Part stock updated successfully.');
    }

    public function updatePayment(Request $request, PartStock $partStock)
    {
        $request->validate([
            'paid_amount'  => 'required|numeric|min:0.01|max:' . $partStock->remainingBalance(),
            'payment_date' => 'required|date',
        ]);

        PartStockPayment::create([
            'part_stock_id' => $partStock->id,
            'paid_amount'   => $request->paid_amount,
            'payment_date'  => $request->payment_date,
        ]);

        return back()->with('success', 'Payment added successfully.');
    }

    public function show(PartStock $partStock)
    {
        if ($partStock->branch_id !== session('active_branch_id')) {
            abort(403, 'Unauthorized action.');
        }

        $partStock->load(['branch', 'payments', 'partSales']);

        return view('admin.partstocks.show', compact('partStock'));
    }
}
