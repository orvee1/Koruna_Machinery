<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\PartStock;
use App\Models\PartStockPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartStockController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin,manager');
    }
    /**
     * Display a listing of the part stocks.
     */
    public function index(Request $request)
{
    // Get the selected date from the request or use today's date if not provided
    $date = $request->get('date', null);  // Allow null for the first load, so we get all records initially
    $search = $request->get('search', '');

    $query = PartStock::with('branch');

    // If the user is a manager, restrict results to the active branch
    if (Auth::check() && Auth::user()->role == 'manager') {
        $query->where('branch_id', session('active_branch_id'));
    }

    // Apply the search query for product name and supplier name
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('product_name', 'like', '%' . $search . '%')
              ->orWhere('supplier_name', 'like', '%' . $search . '%');
        });
    }

    // If a date is provided, filter by that date
    if ($date) {
        $query->whereDate('created_at', $date);
    }

    // Fetch part stocks with the necessary relationships and pagination
    $partStocks = $query->paginate(10);

    // Return the view with the filtered part stocks
    return view('manager.partstocks.index', compact('partStocks', 'date', 'search'));
}


    /**
     * Show the form for creating a new part stock entry.
     */
    public function create()
    {
        $branches = Branch::all();
        return view('manager.partstocks.create', compact('branches'));
    }

    /**
     * Store a newly created part stock entry in the database.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'product_name' => 'required|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'buy_value' => 'required|decimal:0,2',
            'quantity' => 'required|integer|min:1',
            'sell_value' => 'required|decimal:0,2',
            'branch_id' => 'required|exists:branches,id',
        ]);

        // Create the part stock using the validated data
        $partStock = PartStock::create($request->all());

        // Automatically calculate the amount and total profit
        // $partStock->calculateAmountAndProfit();  // This will calculate the amount and profit

        // Redirect to the index with a success message
        return redirect()->route('manager.partstocks.index')->with('success', 'Part stock added successfully.');
    }

    /**
     * Show the form for editing the specified part stock entry.
     */
    public function edit(PartStock $partStock)
    {
        $branches = Branch::all();
        return view('manager.partstocks.edit', compact('partStock', 'branches'));
    }

    /**
     * Update the specified part stock entry in the database.
     */
    public function update(Request $request, PartStock $partStock)
    {
        // Validate the incoming request data
        $request->validate([
            'product_name' => 'required|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'buy_value' => 'required|decimal:0,2',
            'quantity' => 'required|integer|min:1',
            'sell_value' => 'required|decimal:0,2',
            'branch_id' => 'required|exists:branches,id',
        ]);

        // Update the part stock with the validated data
        $partStock->update($request->all());

        // Recalculate the amount and total profit
        // $partStock->calculateAmountAndProfit();  // This will calculate the amount and profit

        // Redirect to the index with a success message
        return redirect()->route('manager.partstocks.index')->with('success', 'Part stock updated successfully.');
    }

    public function updatePayment(Request $request, PartStock $partStock)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
        ]);
    
        // Create new payment record for the part stock
        $payment = new PartStockPayment([
            'paid_amount' => $request->paid_amount,
            'payment_date' => $request->payment_date,
            'part_stock_id' => $partStock->id,
        ]);
    
        // Save payment related to the part stock
        $payment->save();
    
        // return back()->with('success', 'Payment updated successfully.');
        return redirect()->route('manager.partstocks.show', $partStock->id)->with('success', 'Payment updated successfully.');

    }
    

    /**
     * Show the details of a specific part stock entry.
     */
    public function show(PartStock $partStock)
    {
        // Load the related product and branch data
        $partStock->load('branch');

        // Return the view for showing the part stock details
        return view('manager.partstocks.show', compact('partStock'));
    }
}