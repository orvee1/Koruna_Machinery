<?php

namespace App\Http\Controllers\Manager;

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
        $this->middleware('checkRole:admin,manager');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = PartstockSale::with(['partStock', 'customer', 'branch', 'seller', 'investor']);

        // Worker: শুধু আজকের লিস্ট দেখবে
        if ($user->role === 'worker') {
            $query->forToday()->where('seller_id', $user->id);
        } else {
            // Admin/Manager: তারিখ/মাস/বছর অনুযায়ী ফিল্টার করতে পারবে
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

        // শুধু Active Branch-এর Data দেখাবে
        if (session('active_branch_id')) {
            $query->where('branch_id', session('active_branch_id'));
        }

        $sales = $query->latest()->paginate(20);
        return view('manager.partstock-sales.index', compact('sales'));
    }

    public function create()
    {
        $partStocks = PartStock::where('branch_id', session('active_branch_id'))->get();
        $customers = Customer::where('branch_id', session('active_branch_id'))->get();

        return view('manager.partstock-sales.create', compact('partStocks', 'customers'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'part_stock_id' => 'required|exists:part_stocks,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        PartstockSale::create([
            'branch_id' => session('active_branch_id'),
            'part_stock_id' => $validated['part_stock_id'],
            'customer_id' => $validated['customer_id'],
            'seller_id' => $user->id,
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'paid_amount' => $validated['paid_amount'],
        ]);

        return redirect()->route('manager.partstock-sales.index')->with('success', 'Partstock Sale added successfully.');
    }

    public function edit(PartstockSale $partstockSale)
    {
        $partStocks = PartStock::where('branch_id', session('active_branch_id'))->get();
        $customers = Customer::where('branch_id', session('active_branch_id'))->get();

        return view('manager.partstock-sales.edit', compact('partstockSale', 'partStocks', 'customers'));
    }

    public function update(Request $request, PartstockSale $partstockSale)
    {
        $validated = $request->validate([
            'part_stock_id' => 'required|exists:part_stocks,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $partstockSale->update($validated);

        return redirect()->route('manager.partstock-sales.index')->with('success', 'Partstock Sale updated successfully.');
    }

}
