<?php
namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Customer;
use App\Models\PartStock;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkRole:manager');
    }

    // Manager Dashboard (branch-specific)
    public function dashboard()
    {
        $managerBranch = auth()->user()->branch_id;
        $stocks = Stock::where('branch_id', $managerBranch)->get();
        $customers = Customer::where('branch_id', $managerBranch)->get();
        // $partStocks = PartStock::where('branch_id', $managerBranch)->get();

        return view('manager.dashboard', compact('stocks', 'customers', 'partStocks'));
    }

    // Add Stock to the branch
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

    // Show customers from the manager's branch
    public function showCustomers()
    {
        $managerBranch = auth()->user()->branch_id;
        $customers = Customer::where('branch_id', $managerBranch)->get();
        return view('manager.customers.index', compact('customers'));
    }

    // Show part stock from the manager's branch
    // public function showPartStock()
    // {
    //     $managerBranch = auth()->user()->branch_id;
    //     // $partStocks = PartStock::where('branch_id', $managerBranch)->get();
    //     return view('manager.part_stocks.index', compact('partStocks'));
    // }
}
