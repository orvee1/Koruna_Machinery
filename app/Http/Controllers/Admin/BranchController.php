<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct()
    {
        // Only Admin can access this controller
        $this->middleware('checkRole:admin');
    }

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
        ]);

        Branch::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.branches.index')->with('success', 'Branch created successfully!');
    }

    public function show(Branch $branch)
    {
        $branch->load([
            'customers',
            'user',
            'stock',
            'partStocks',
            'investors',
            'bills',
        ]);

        $totalIncome = $branch->bills->sum('total_amount');

        $productExpense = $branch->stock->sum(function ($stock) {
            return $stock->buying_price * $stock->quantity;
        });

        $partstockExpense = $branch->partStocks->sum('amount');
        $totalExpense = $productExpense + $partstockExpense;

        $profit = $totalIncome - $totalExpense;

        return view('admin.branches.show', compact(
            'branch',
            'totalIncome',
            'productExpense',
            'partstockExpense',
            'totalExpense',
            'profit'
        ));
    }

    public function edit(Branch $branch)
    {
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $branch->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.branches.index')->with('success', 'Branch updated successfully!');
    }
}
