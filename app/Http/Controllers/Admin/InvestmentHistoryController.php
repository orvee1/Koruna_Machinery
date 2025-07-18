<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvestmentHistory;
use App\Models\Investor;
use App\Models\Product;
use Illuminate\Http\Request;

class InvestmentHistoryController extends Controller
{
    public function __construct()
    {
        // Middleware to check if the user has the 'admin' role
        $this->middleware('checkRole:admin');
    }
    /**
     * Display a listing of the investment histories.
     */
    public function index()
    {
        // Fetch all investment histories along with their investor and product relationships
        $investmentHistories = InvestmentHistory::with('investor')->paginate(10);

        return view('admin.investment-histories.index', compact('investmentHistories'));
    }

    /**
     * Show the form for creating a new investment history entry.
     */
    public function create()
    {
        // Get all investors and products to be used in the form
        $investors = Investor::all();
       

        return view('admin.investment-histories.create', compact('investors'));
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
            'buying_price' => 'required|decimal:0,2',
            'total_cost' => 'required|decimal:0,2',
        ]);

        // Create the investment history record
        InvestmentHistory::create($request->all());

        // After creating, you can perform additional operations like updating the investor's balance, etc.

        return redirect()->route('admin.investment-histories.index')->with('success', 'Investment history created successfully.');
    }

    /**
     * Show the form for editing the specified investment history entry.
     */
    public function edit(InvestmentHistory $investmentHistory)
    {
        // Get all products and investors to populate the form
        $investors = Investor::all();
      

        return view('admin.investment-histories.edit', compact('investmentHistory', 'investors'));
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
            'buying_price' => 'required|decimal:0,2',
            'total_cost' => 'required|decimal:0,2',
        ]);

        // Update the investment history record
        $investmentHistory->update($request->all());

        return redirect()->route('admin.investment-histories.index')->with('success', 'Investment history updated successfully.');
    }

    /**
     * Remove the specified investment history entry from the database.
     */

    /**
     * Show the details of a specific investment history.
     */
    public function show(InvestmentHistory $investmentHistory)
    {
        return view('admin.investment-histories.show', compact('investmentHistory'));
    }
}