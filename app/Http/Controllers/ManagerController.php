<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Customer;
use App\Models\PartStock;
use App\Models\ProductSale;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function dashboard(Request $request)
    {
        $branchId = auth()->user()->branch_id;

        $stocks = Stock::where('branch_id', $branchId)->get();
        $customers = Customer::where('branch_id', $branchId)->get();
        $partStocks = PartStock::where('branch_id', $branchId)->get();

        $salesQuery = ProductSale::with(['product', 'customer'])
            ->where('branch_id', $branchId);

        if ($request->filled('date')) {
            $salesQuery->whereDate('created_at', $request->input('date'));
        }

        if ($request->filled('month')) {
            $salesQuery->whereMonth('created_at', $request->input('month'));
        }

        if ($request->filled('year')) {
            $salesQuery->whereYear('created_at', $request->input('year'));
        }

        $sales = $salesQuery->latest()->get();
        $totalIncome = $sales->sum('paid_amount');

        return view('manager.dashboard', compact('stocks', 'customers', 'partStocks', 'sales', 'totalIncome'));
    }

    public function addStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'buying_price' => 'required|numeric',
        ]);

        // Logic for adding stock goes here

        return redirect()->back()->with('success', 'Stock added successfully!');
    }

    public function showCustomers()
    {
        $managerBranch = auth()->user()->branch_id;
        $customers = Customer::where('branch_id', $managerBranch)->get();
        return view('manager.customers.index', compact('customers'));
    }
}
