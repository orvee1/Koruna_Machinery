<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\PartStock;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     $this->middleware('checkRole:admin'); // Admin role is required to access this functionality
    // }

    public function index()
    {
        $branches = Branch::all();

        return view('admin.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('admin.branches.create');
    }
    
    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            // 'code' => 'required|string|max:5|unique:branches,code', 
        ]);

      
        Branch::create([
            'name' => $request->name,
            // 'code' => $request->code,
        ]);

        return redirect()->route('admin.branches.index')->with('success', 'Branch created successfully!');
    }

    public function show(Branch $branch)
    {
        // Get all related data for the branch
        $customers = Customer::where('branch_id', $branch->id)->get();  
        $products = Product::where('branch_id', $branch->id)->get();    
        $sales = Sale::where('branch_id', $branch->id)->get();         
        $partStocks = PartStock::where('branch_id', $branch->id)->get(); 
        // Calculate total income and expense
        $totalIncome = $sales->sum('total_amount');
        $totalExpense = $partStocks->sum('amount'); // Sum of all part stocks (buying price)
        $profit = $totalIncome - $totalExpense;

        return view('admin.branches.show', compact('branch', 'customers', 'products', 'sales', 'partStocks', 'totalIncome', 'totalExpense', 'profit'));
    }

    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, $id)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            // 'code' => 'required|string|max:5|unique:branches,code,' . $id, // Ignore uniqueness for the current branch
        ]);

        $branch = Branch::findOrFail($id);
        $branch->update([
            'name' => $request->name,
            // 'code' => $request->code,
        ]);

        return redirect()->route('admin.branches.index')->with('success', 'Branch updated successfully!');
    }
}
