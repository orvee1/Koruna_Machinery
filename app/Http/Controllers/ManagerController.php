<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Customer;
use App\Models\Investor;
use App\Models\PartStock;
use App\Models\PartstockSale;
use App\Models\Product;
use App\Models\ProductSale;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function dashboard(Request $request)
    {
        $branchId = auth()->user()->branch_id;

        // Query Building
        $salesQuery = ProductSale::where('branch_id', $branchId);

        if ($request->filled('date')) {
            $salesQuery->whereDate('created_at', $request->input('date'));
        }
        if ($request->filled('month')) {
            $salesQuery->whereMonth('created_at', $request->input('month'));
        }
        if ($request->filled('year')) {
            $salesQuery->whereYear('created_at', $request->input('year'));
        }

        $partstockSalesQuery = PartstockSale::where('branch_id', $branchId);

        if ($request->filled('date')) {
            $partstockSalesQuery->whereDate('created_at', $request->input('date'));
        }
        if ($request->filled('month')) {
            $partstockSalesQuery->whereMonth('created_at', $request->input('month'));
        }
        if ($request->filled('year')) {
            $partstockSalesQuery->whereYear('created_at', $request->input('year'));
        }

        // Calculations
        $totalProducts = Product::where('branch_id', $branchId)->count();
        $totalStocks = Stock::where('branch_id', $branchId)->count();
        $totalCustomers = Customer::where('branch_id', $branchId)->count();
        $totalInvestors = Investor::where('branch_id', $branchId)->count();
        $totalProductSales = $salesQuery->sum('paid_amount');
        $totalPartstockSales = $partstockSalesQuery->sum('paid_amount');
        $totalSales = $totalProductSales + $totalPartstockSales;

        return view('manager.dashboard', compact(
            'totalProducts', 'totalStocks', 'totalCustomers', 'totalInvestors', 'totalSales'
        ));
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
