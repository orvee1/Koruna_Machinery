<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\PartStock;
use App\Models\PartStockPayment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $validated = $request->validate([
            'product_name'    => 'required|string|max:255',
            'supplier_name'   => 'required|string|max:255',
            'buying_price'    => 'required|numeric|min:0|max:99999999.99',
            'quantity'        => 'required|integer|min:1',
            'sell_value'      => 'required|numeric|min:0|max:99999999.99',
            'deposit_amount'  => 'nullable|numeric|min:0|max:99999999.99',
            'purchase_date'   => 'required|date',
        ]);

        $validated['branch_id'] = session('active_branch_id');
        $validated['deposit_amount'] = $validated['deposit_amount'] ?? 0;

        PartStock::create($validated);

        return redirect()
            ->route('admin.partstocks.index')
            ->with('success', 'Part stock added successfully.');
    }


    public function edit(PartStock $partStock)
    {
        $branchId = session('active_branch_id');

        if (!$branchId || $partStock->branch_id !== $branchId) {
            return redirect()
                ->route('admin.partstocks.index')
                ->with('error', 'You are not authorized to edit this part stock.');
        }

        $branch = Branch::findOrFail($branchId);

        return view('admin.partstocks.edit', compact('partStock', 'branch'));
    }



    public function update(Request $request, PartStock $partStock)
    {

        $request->validate([
            'product_name'    => 'required|string|max:255',
            'supplier_name'   => 'required|string|max:255',
            'buying_price'       => 'required|numeric|min:0',
            'quantity'        => 'required|integer|min:1',
            'sell_value'      => 'required|numeric|min:0',
            'deposit_amount'  => 'nullable|numeric|min:0',
            'purchase_date'   => 'required|date',
        ]);

        $partStock->update([
            'product_name'    => $request->product_name,
            'supplier_name'   => $request->supplier_name,
            'buying_price'       => $request->buying_price,
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
            'paid_amount' => 'required|decimal:0,2|min:0.01|max:' . $partStock->due_amount,
            'payment_date' => 'required|date',
        ]);

        PartStockPayment::create([
            'paid_amount' => $request->paid_amount,
            'payment_date' => $request->payment_date,
            'part_stock_id' => $partStock->id,
        ]);

        $partStock->deposit_amount += $request->paid_amount;

        $partStock->due_amount = max($partStock->total_amount - $partStock->deposit_amount, 0);
        $partStock->save();

        return back()->with('success', 'Payment updated successfully.');
    }

    public function show($id)
    {
        $partStock = PartStock::with('payments')->find($id);

        if (!$partStock) {
            return redirect()->route('admin.partstocks.index')->with('error', 'Part Stock not found.');
        }

        return view('admin.partstocks.show', compact('partStock'));
    }


}
