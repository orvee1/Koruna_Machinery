<?php

namespace App\Http\Controllers;

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
            'products',
            'productSales',
            'partstockSales',
            'partStocks',
        ]);

        $productIncome = $branch->productSales->sum('total_amount');
        $partstockIncome = $branch->partstockSales->sum('total_amount');
        $totalIncome = $productIncome + $partstockIncome;

        $productExpense = $branch->products->sum(function ($product) {
            return $product->buying_price * $product->stock_quantity;
        });

        $partstockExpense = $branch->partStocks->sum('amount');
        $totalExpense = $productExpense + $partstockExpense;

        $profit = $totalIncome - $totalExpense;

        return view('admin.branches.show', compact(
            'branch',
            'productIncome',
            'partstockIncome',
            'totalIncome',
            'productExpense',
            'partstockExpense',
            'totalExpense',
            'profit'
        ));
    }

    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $branch = Branch::findOrFail($id);
        $branch->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.branches.index')->with('success', 'Branch updated successfully!');
    }
}
