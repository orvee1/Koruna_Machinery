<?php
namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Customer;
use App\Models\PartStock;
use Illuminate\Http\Request;

class WorkerController extends Controller
{


    // Worker Dashboard (branch-specific)
    public function dashboard()
    {
        $workerBranch = auth()->user()->branch_id;
        $stocks = Stock::where('branch_id', $workerBranch)->get();
        $customers = Customer::where('branch_id', $workerBranch)->get();
        // $partStocks = PartStock::where('branch_id', $workerBranch)->get();

        return view('worker.dashboard', compact('stocks', 'customers', 'partStocks'));
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

