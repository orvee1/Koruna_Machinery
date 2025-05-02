<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin,manager');
        // Worker, Manager, Admin - সবারই অ্যাক্সেস লাগবে
    }

    public function index()
    {
        $query = Customer::query();

        if (auth()->user()->role == 'manager') {
            $query->where('branch_id', session('active_branch_id'));
        }

        $customers = $query->latest()->paginate(20);
        return view('manager.customers.index', compact('customers'));
    }

    public function create()
    {
        $branches = Branch::all();
        return view('manager.customers.create', compact('branches'));
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
            'branch_id'  => session('active_branch_id'), 
        ]);

        return redirect()->rote('manager.customers.index')->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('manager.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorizeAccess($customer);

        $request->validate([
            'name'      => 'required|string|max:255',
            'phone'     => 'required|string|max:20|unique:customers,phone,' . $customer->id,
            'district'  => 'nullable|string|max:255',
        ]);

        $customer->update($request->only('name', 'phone', 'district'));

        return redirect()->rote('manager.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function show(Customer $customer)
    {

        $sales = $customer->productsales()
        ->with(['product', 'seller']) // eager load
        ->latest()
        ->get();

        return view('manager.customers.show', compact('customer','sales'));
    }

}
