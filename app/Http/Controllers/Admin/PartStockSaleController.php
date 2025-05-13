<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PartStock;
use App\Models\PartstockSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartstockSaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin');
    }

    /**
     * Display all partstock sales with optional filters
     */
    public function index(Request $request)
    {
        $branchId = session('active_branch_id');
        $query = PartstockSale::where('branch_id', $branchId)
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

        $sales = $query->latest()->paginate(20);
        return view('admin.partstock-sales.index', compact('sales'));
    }

    /**
     * Show form to create a new partstock sale
     */
    public function create()
    {
        $branchId = session('active_branch_id');
        $partStocks = PartStock::where('branch_id', $branchId)->get();
        $customers = Customer::where('branch_id', $branchId)->get();

        return view('admin.partstock-sales.create', compact('partStocks', 'customers'));
    }

    /**
     * Store a newly created partstock sale
     */
    public function store(Request $request)
    {
        $request->validate([
            'part_stock_id' => 'required|exists:part_stocks,id',
            'customer_id'   => 'required|exists:customers,id',
            'quantity'      => 'required|integer|min:1',
            'unit_price'    => 'required|numeric|min:0',
            'paid_amount'   => 'required|numeric|min:0',
        ]);

        PartstockSale::create([
            'branch_id'      => session('active_branch_id'),
            'part_stock_id'  => $request->part_stock_id,
            'customer_id'    => $request->customer_id,
            'seller_id'      => Auth::id(),
            'quantity'       => $request->quantity,
            'unit_price'     => $request->unit_price,
            'paid_amount'    => $request->paid_amount,
        ]);

        return redirect()->route('admin.partstock-sales.index')
            ->with('success', 'Partstock sale added successfully.');
    }

    /**
     * Show form to edit an existing sale
     */
    public function edit(PartstockSale $partstockSale)
    {
        $branchId = session('active_branch_id');
        $partStocks = PartStock::where('branch_id', $branchId)->get();
        $customers = Customer::where('branch_id', $branchId)->get();

        return view('admin.partstock-sales.edit', compact('partstockSale', 'partStocks', 'customers'));
    }

    /**
     * Update an existing sale
     */
    public function update(Request $request, PartstockSale $partstockSale)
    {
        $request->validate([
            'part_stock_id' => 'required|exists:part_stocks,id',
            'customer_id'   => 'required|exists:customers,id',
            'quantity'      => 'required|integer|min:1',
            'unit_price'    => 'required|numeric|min:0',
            'paid_amount'   => 'required|numeric|min:0',
        ]);

        $partstockSale->update([
            'part_stock_id' => $request->part_stock_id,
            'customer_id'   => $request->customer_id,
            'quantity'      => $request->quantity,
            'unit_price'    => $request->unit_price,
            'paid_amount'   => $request->paid_amount,
        ]);

        return redirect()->route('admin.partstock-sales.index')
            ->with('success', 'Partstock sale updated successfully.');
    }

    /**
     * Delete a partstock sale
     */
    public function destroy(PartstockSale $partstockSale)
    {
        $partstockSale->delete();

        return redirect()->route('admin.partstock-sales.index')
            ->with('success', 'Partstock sale deleted successfully.');
    }
}
