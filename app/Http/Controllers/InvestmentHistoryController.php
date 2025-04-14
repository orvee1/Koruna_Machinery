<?php

namespace App\Http\Controllers;

use App\Models\InvestmentHistory;
use App\Models\Investor;
use App\Models\Product;
use Illuminate\Http\Request;

class InvestmentHistoryController extends Controller
{
    /**
     * Display a listing of the investment histories.
     */
    public function index()
    {
        // Fetch all investment histories along with their investor and product relationships
        $investmentHistories = InvestmentHistory::with('investor', 'product')->paginate(10);

        return view('admin.investmentHistories.index', compact('investmentHistories'));
    }

    /**
     * Show the form for creating a new investment history entry.
     */
    public function create()
    {
        // Get all investors and products to be used in the form
        $investors = Investor::all();
        $products = Product::all();

        return view('admin.investmentHistories.create', compact('investors', 'products'));
    }

    /**
     * Store a newly created investment history entry in the database.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'buying_price' => 'required|numeric',
            'total_cost' => 'required|numeric',
        ]);

        // Create the investment history record
        InvestmentHistory::create($request->all());

        // After creating, you can perform additional operations like updating the investor's balance, etc.

        return redirect()->route('admin.investmentHistories.index')->with('success', 'Investment history created successfully.');
    }

    /**
     * Show the form for editing the specified investment history entry.
     */
    public function edit(InvestmentHistory $investmentHistory)
    {
        // Get all products and investors to populate the form
        $investors = Investor::all();
        $products = Product::all();

        return view('admin.investmentHistories.edit', compact('investmentHistory', 'investors', 'products'));
    }

    /**
     * Update the specified investment history entry in the database.
     */
    public function update(Request $request, InvestmentHistory $investmentHistory)
    {
        // Validate the incoming request data
        $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'buying_price' => 'required|numeric',
            'total_cost' => 'required|numeric',
        ]);

        // Update the investment history record
        $investmentHistory->update($request->all());

        return redirect()->route('admin.investmentHistories.index')->with('success', 'Investment history updated successfully.');
    }

    /**
     * Remove the specified investment history entry from the database.
     */

    /**
     * Show the details of a specific investment history.
     */
    public function show(InvestmentHistory $investmentHistory)
    {
        return view('admin.investmentHistories.show', compact('investmentHistory'));
    }
}
