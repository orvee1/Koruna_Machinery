<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $branches = Branch::all();

        $customerQuery = Customer::query();

        if($request->filled('branch_id')) {
            $customerQuery->where('branch_id', $request->branch_id);
        }

        if(!auth()->user()->isAdmin()) {
            $customerQuery->where('branch_id', auth()->user()->branch_id);
        }

        $customers = $customerQuery->paginate(10)
                                    ->get();
    

    return view('admin.customers.index', compact('customers', 'branches'));
    }
    public function create()
    {
        $branches = Branch::all();

        return view('worker.customers.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $customerId = Customer::generateCustomerId($request->branch_id);

        // Create the customer
        Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'district' => $request->district,
            'branch_id' => $request->branch_id,
            'customer_id' => $customerId,
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Customer added successfully.');
    }
}
