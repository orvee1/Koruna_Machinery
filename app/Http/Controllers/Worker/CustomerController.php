<?php

namespace App\Http\Controllers\worker;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin,worker');
    }

    public function index()
    {
        $query = Customer::query();

        $branchId = auth()->user()->branch_id;

        $customers = Customer::where('branch_id', $branchId)
            ->with('branch')
            ->latest()
            ->paginate(20);

        return view('worker.customers.index', compact('customers'));
    }

    public function create()
    {
       $branchId = auth()->user()->branch_id;

       $branch = Branch::findOrFail($branchId);

        return view('worker.customers.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'phone'     => 'required|string|max:20|unique:customers,phone',
            'district'  => 'nullable|string|max:255',
        ]);

        Customer::create([
            'name'       => $request->name,
            'phone'      => $request->phone,
            'district'   => $request->district,
            'branch_id'  => auth()->user()->branch_id,
        ]);

        return redirect()->route('worker.customers.index')->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('worker.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'phone'     => 'required|string|max:20|unique:customers,phone,' . $customer->id,
            'district'  => 'nullable|string|max:255',
            'branch_id'  => auth()->user()->branch_id, 
        ]);

        $customer->update($request->only('name', 'phone', 'district'));

        return redirect()->route('worker.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function show(Customer $customer)
    {
        $branchId = auth()->user()->branch_id;

        $branch = Branch::findOrFail($branchId);

        $bills = $customer->bills()
        ->with(['payments', 'seller'])
        ->orderBy('created_at')
        ->get();

        return view('worker.customers.show', compact('customer','bills'));
    }
}

