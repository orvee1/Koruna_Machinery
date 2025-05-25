<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\PartStock;
use App\Models\PartstockSale;
use App\Models\Product;
use App\Models\ProductSale;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;

class WorkerController extends Controller
{

    // Worker Dashboard (branch-specific)
    public function dashboard(Request $request)
    {
        $branchId = auth()->user()->branch_id;

        // Query Building
        $branch = Branch::find($branchId);
        // Sales
        $totalProductSales = ProductSale::where('branch_id', $branchId)->sum('paid_amount');
        $totalPartStockSales = PartStockSale::where('branch_id', $branchId)->sum('paid_amount');
        $totalSales = $totalProductSales + $totalPartStockSales;
    
        $totalPartStockValue = PartStock::where('branch_id', $branchId)->sum('buy_value');
        $totalValue = $totalProductValue + $totalPartStockValue;

        $productProfit = Stock::where('branch_id', $branchId)->sum('total_profit');
        $partStockProfit = PartStock::where('branch_id', $branchId)->sum('total_profit');
        $totalProfit = $productProfit + $partStockProfit;

        $productDue = Stock::where('branch_id', $branchId)->sum('due_amount');
        $partStockDue = PartStock::where('branch_id', $branchId)->sum('due_amount');
        $totalDue = $productDue + $partStockDue;

        $productDueToHave = ProductSale::where('branch_id', $branchId)->sum('due_amount');
        $partStockDueToHave = PartStockSale::where('branch_id', $branchId)->sum('due_amount');
        $totalDueToHave = $productDueToHave + $partStockDueToHave;

        // Optional: all users of this branch
        $users = User::where('branch_id', $branchId)->with('branch')->get();
    
        return view('admin.dashboard', compact(
            'totalSales',
            'totalValue',
            'users',
            'totalProfit',
            'totalDue',
            'totalDueToHave',
        ));
    }

    // Add stock to the branch (workers can add stock)
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

    // Show customer IDs for the worker's branch
    public function showCustomerIds()
    {
        $workerBranch = auth()->user()->branch_id;
        $customers = Customer::where('branch_id', $workerBranch)->get();
        return view('worker.customers.index', compact('customers'));
    }

    // Show part stock for the worker's branch
    // public function showPartStock()
    // {
    //     $workerBranch = auth()->user()->branch_id;
    //     $partStocks = PartStock::where('branch_id', $workerBranch)->get();
    //     return view('worker.part_stocks.index', compact('partStocks'));
    // }
}
