<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\InvestmentHistory;
use App\Models\DepositHistory;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
    /**
     * Display a listing of the investors.
     */
    public function index()
    {
        // Get all investors with their investment histories and deposit histories
        $investors = Investor::with('investmentHistories', 'depositHistories')->paginate(10);
        return view('admin.investors.index', compact('investors'));
    }

    /**
     * Show the form for creating a new investor.
     */
    public function create()
    {
        return view('admin.investors.create');
    }

    /**
     * Store a newly created investor in the database.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'total_investment' => 'required|numeric',
            'balance' => 'required|numeric',
            'status' => 'required|in:active,closed',
        ]);

        // Create the new investor
        $investor = Investor::create($request->all());

        // Check if the balance meets or exceeds the total investment, and close the panel if necessary
        $investor->closePanel();

        return redirect()->route('admin.investors.index')->with('success', 'Investor created successfully.');
    }

    /**
     * Show the form for editing the specified investor.
     */
    public function edit(Investor $investor)
    {
        return view('admin.investors.edit', compact('investor'));
    }

    /**
     * Update the specified investor in the database.
     */
    public function update(Request $request, Investor $investor)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'total_investment' => 'required|numeric',
            'balance' => 'required|numeric',
            'status' => 'required|in:active,closed',
        ]);

        // Update the investor's information
        $investor->update($request->all());

        // Check if the balance meets or exceeds the total investment, and close the panel if necessary
        $investor->closePanel();

        return redirect()->route('admin.investors.index')->with('success', 'Investor updated successfully.');
    }

    /**
     * Remove the specified investor from the database.
     */

    /**
     * Show the details of a specific investor.
     */
    public function show(Investor $investor)
    {
        // Load investment histories and deposit histories of the investor
        $investor->load('investmentHistories', 'depositHistories');

        return view('admin.investors.show', compact('investor'));
    }

    /**
     * Add an investment history for the investor.
     */
    public function addInvestmentHistory(Request $request, Investor $investor)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:255',
        ]);

        // Create a new investment history record
        $investmentHistory = new InvestmentHistory([
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        // Associate the investment history with the investor
        $investor->investmentHistories()->save($investmentHistory);

        // Update the balance and check if the panel should be closed
        $investor->balance += $request->amount;
        $investor->closePanel(); // Check if the panel should be closed

        return back()->with('success', 'Investment history added and balance updated.');
    }

    /**
     * Add a deposit history for the investor.
     */
    public function addDepositHistory(Request $request, Investor $investor)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:255',
        ]);

        // Create a new deposit history record
        $depositHistory = new DepositHistory([
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        // Associate the deposit history with the investor
        $investor->depositHistories()->save($depositHistory);

        // Update the balance and check if the panel should be closed
        $investor->balance += $request->amount;
        $investor->closePanel(); // Check if the panel should be closed

        return back()->with('success', 'Deposit history added and balance updated.');
    }
}
