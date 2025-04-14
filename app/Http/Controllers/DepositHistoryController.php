<?php

namespace App\Http\Controllers;

use App\Models\DepositHistory;
use App\Models\Investor;
use Illuminate\Http\Request;

class DepositHistoryController extends Controller
{
    /**
     * Display a listing of the deposit histories.
     */
    public function index()
    {
        // Get all deposit histories with related investor details and paginate results
        $depositHistories = DepositHistory::with('investor')->paginate(10);

        // Return the view with the deposit histories
        return view('admin.depositHistories.index', compact('depositHistories'));
    }

    /**
     * Show the form for creating a new deposit history entry.
     */
    public function create()
    {
        // Get all investors for the dropdown list
        $investors = Investor::all();

        // Return the view for creating a deposit history record
        return view('admin.depositHistories.create', compact('investors'));
    }

    /**
     * Store a newly created deposit history entry in the database.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'investor_id' => 'required|exists:investors,id',  // Ensure the investor exists
            'amount' => 'required|numeric|min:1',  // Amount must be a positive number
            'payment_method' => 'required|in:cash,bank,card',  // Payment method must be one of the specified types
            'payment_date' => 'required|date',  // Ensure payment date is valid
        ]);

        // Create the deposit history record
        DepositHistory::create($request->all());

        // Redirect back with a success message
        return redirect()->route('admin.depositHistories.index')->with('success', 'Deposit history added successfully.');
    }

    /**
     * Show the form for editing the specified deposit history entry.
     */
    public function edit(DepositHistory $depositHistory)
    {
        // Get all investors for the dropdown list
        $investors = Investor::all();

        // Return the view for editing the deposit history
        return view('admin.depositHistories.edit', compact('depositHistory', 'investors'));
    }

    /**
     * Update the specified deposit history entry in the database.
     */
    public function update(Request $request, DepositHistory $depositHistory)
    {
        // Validate the incoming request data
        $request->validate([
            'investor_id' => 'required|exists:investors,id',  // Ensure the investor exists
            'amount' => 'required|numeric|min:1',  // Amount must be a positive number
            'payment_method' => 'required|in:cash,bank,card',  // Payment method must be one of the specified types
            'payment_date' => 'required|date',  // Ensure payment date is valid
        ]);

        // Update the deposit history record
        $depositHistory->update($request->all());

        // Redirect back with a success message
        return redirect()->route('admin.depositHistories.index')->with('success', 'Deposit history updated successfully.');
    }

    /**
     * Remove the specified deposit history entry from the database.
     */

    /**
     * Show the details of a specific deposit history.
     */
    public function show(DepositHistory $depositHistory)
    {
        // Load related investor data
        $depositHistory->load('investor');

        // Return the view for showing the deposit history details
        return view('admin.depositHistories.show', compact('depositHistory'));
    }
}
