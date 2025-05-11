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

    /**
     * Display a listing of the part stocks.
     */
    public function index(Request $request)
    {
        $date = $request->get('date', null); 
        $search = $request->get('search', '');
        $branchId = session('active_branch_id');

        if (!$branchId) {
            return redirect()->route('admin.select-branch')->with('error', 'Please select a branch first.');
        }

        // Query with branch filter
        $query = PartStock::where('branch_id', $branchId)
            ->with('branch')
            ->latest();

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', '%' . $search . '%')
                  ->orWhere('supplier_name', 'like', '%' . $search . '%');
            });
        }

        // Apply date filter
        if ($date) {
            $query->whereDate('created_at', $date);
        }

        // Pagination
        $partstocks = $query->paginate(20);

        return view('admin.partstocks.index', compact('partstocks', 'date', 'search'));
    }

    /**
     * Show the form for creating a new part stock entry.
     */
    public function create()
    {
        $branchId = session('active_branch_id');

        if (!$branchId) {
            return redirect()->route('admin.select-branch')->with('error', 'Please select a branch first.');
        }

        $branch = Branch::findOrFail($branchId);
        return view('admin.partstocks.create', compact('branch'));
    }

    /**
     * Store a newly created part stock entry in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'buy_value' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'sell_value' => 'required|numeric|min:0',
        ]);

        // Ensure the PartStock is only created for the active branch
        $branchId = session('active_branch_id');

        PartStock::create([
            'product_name' => $request->product_name,
            'supplier_name' => $request->supplier_name,
            'buy_value' => $request->buy_value,
            'quantity' => $request->quantity,
            'sell_value' => $request->sell_value,
            'branch_id' => $branchId,  // Only active branch
        ]);

        return redirect()->route('admin.partstocks.index')->with('success', 'Part stock added successfully.');
    }

    /**
     * Show the form for editing the specified part stock entry.
     */
    public function edit(PartStock $partStock)
    {
        // Check if the PartStock belongs to the active branch
        if ($partStock->branch_id !== session('active_branch_id')) {
            abort(403, 'Unauthorized action.');
        }

        $branch = Branch::find(session('active_branch_id'));
        return view('admin.partstocks.edit', compact('partStock', 'branch'));
    }

    /**
     * Update the specified part stock entry in the database.
     */
    public function update(Request $request, PartStock $partStock)
    {
        if ($partStock->branch_id !== session('active_branch_id')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'product_name' => 'required|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'buy_value' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'sell_value' => 'required|numeric|min:0',
        ]);

        $partStock->update([
            'product_name' => $request->product_name,
            'supplier_name' => $request->supplier_name,
            'buy_value' => $request->buy_value,
            'quantity' => $request->quantity,
            'sell_value' => $request->sell_value,
            'branch_id' => session('active_branch_id'), // Only active branch
        ]);

        return redirect()->route('admin.partstocks.index')->with('success', 'Part stock updated successfully.');
    }

    public function updatePayment(Request $request, PartStock $partStock)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
        ]);

        PartStockPayment::create([
            'paid_amount' => $request->paid_amount,
            'payment_date' => $request->payment_date,
            'part_stock_id' => $partStock->id,
        ]);

        return back()->with('success', 'Payment updated successfully.');
    }

    /**
     * Show the details of a specific part stock entry.
     */
    public function show(PartStock $partStock)
    {
        if ($partStock->branch_id !== session('active_branch_id')) {
            abort(403, 'Unauthorized action.');
        }

        $partStock->load('branch');
        return view('admin.partstocks.show', compact('partStock'));
    }
}
