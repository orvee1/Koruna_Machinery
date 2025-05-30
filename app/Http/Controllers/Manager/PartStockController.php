<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\PartStock;
use App\Models\PartStockPayment;
use Illuminate\Http\Request;

class PartStockController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:manager');
    }

    public function index(Request $request)
    {
        $branchId = auth()->user()->branch_id;
        $search = $request->get('search');
        $date = $request->get('date');

        $query = PartStock::with('branch')
            ->where('branch_id', $branchId)
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

        return view('manager.partstocks.index', compact('partstocks', 'search', 'date'));
    }

    public function create()
    {
        return view('manager.partstocks.create');
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

        $validated['branch_id'] = auth()->user()->branch_id;
        $validated['deposit_amount'] = $validated['deposit_amount'] ?? 0;

        PartStock::create($validated);

        return redirect()->route('manager.partstocks.index')
            ->with('success', 'Part stock added successfully.');
    }

    public function edit(PartStock $partStock)
    {
        if ($partStock->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized access to part stock.');
        }

        return view('manager.partstocks.edit', compact('partStock'));
    }

    public function update(Request $request, PartStock $partStock)
    {
        if ($partStock->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized update attempt.');
        }

        $validated = $request->validate([
            'product_name'    => 'required|string|max:255',
            'supplier_name'   => 'required|string|max:255',
            'buying_price'    => 'required|numeric|min:0|max:99999999.99',
            'quantity'        => 'required|integer|min:1',
            'sell_value'      => 'required|numeric|min:0|max:99999999.99',
            'deposit_amount'  => 'nullable|numeric|min:0|max:99999999.99',
            'purchase_date'   => 'required|date',
        ]);

        $validated['deposit_amount'] = $validated['deposit_amount'] ?? 0;

        $partStock->update($validated);

        return redirect()->route('manager.partstocks.index')
            ->with('success', 'Part stock updated successfully.');
    }

    public function updatePayment(Request $request, PartStock $partStock)
    {
        if ($partStock->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized payment update.');
        }

        $rules = [
            'paid_amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
        ];

        if (!is_null($partStock->due_amount)) {
            $rules['paid_amount'][] = 'max:' . $partStock->due_amount;
        }

        $request->validate($rules);

        $payment = new PartStockPayment([
            'paid_amount' => $request->paid_amount,
            'payment_date' => $request->payment_date,
            'part_stock_id' => $partStock->id,
        ]);
        $payment->save();

        $partStock->deposit_amount += $request->paid_amount;
        $partStock->due_amount = max($partStock->total_amount - $partStock->deposit_amount, 0);
        $partStock->save();

        return redirect()->route('manager.partstocks.show', $partStock->id)
            ->with('success', 'Payment updated successfully.');
    }

    public function show(PartStock $partStock)
    {
        if ($partStock->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized view access.');
        }

        $partStock->load('branch', 'payments');

        return view('manager.partstocks.show', compact('partStock'));
    }
}
