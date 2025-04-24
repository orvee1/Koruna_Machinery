<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PartstockSale;
use App\Models\PartStock;
use App\Models\Customer;

class PartstockSaleController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = PartstockSale::with(['partStock', 'customer', 'branch', 'seller', 'investor']);

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
        }

        $sales = $query->latest()->paginate(20);
        return view('admin.partstock_sales.index', compact('sales'));
    }

    public function create()
    {
        $user = Auth::user();
        if (in_array($user->role, ['worker', 'admin', 'manager'])) {
            $partStocks = PartStock::all();
            $customers = Customer::all();
            return view('admin.partstock_sales.create', compact('partStocks', 'customers'));
        }

        abort(403);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['worker', 'admin', 'manager'])) {
            abort(403);
        }

        $validated = $request->validate([
            'part_stock_id' => 'required|exists:part_stocks,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        PartstockSale::create([
            'branch_id' => $user->branch_id,
            'part_stock_id' => $validated['part_stock_id'],
            'customer_id' => $validated['customer_id'],
            'seller_id' => $user->id,
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'paid_amount' => $validated['paid_amount'],
        ]);

        return redirect()->route('admin.partstock_sales.index')->with('success', 'Partstock Sale added successfully');
    }

    public function edit(PartstockSale $partstockSale)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403);
        }

        $partStocks = PartStock::all();
        $customers = Customer::all();

        return view('admin.partstock_sales.index', compact('partstockSale', 'partStocks', 'customers'));
    }

    public function update(Request $request, PartstockSale $partstockSale)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403);
        }

        $validated = $request->validate([
            'part_stock_id' => 'required|exists:part_stocks,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $partstockSale->update($validated);

        return redirect()->route('admin.partstock_sales.index')->with('success', 'Partstock Sale updated successfully');
    }

    public function destroy(PartstockSale $partstockSale)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403);
        }

        $partstockSale->delete();
        return redirect()->route('admin.partstock_sales.index')->with('success', 'Partstock Sale deleted successfully');
    }
}
