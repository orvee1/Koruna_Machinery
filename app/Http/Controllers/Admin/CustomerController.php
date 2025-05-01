<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Worker, Manager, Admin - সবারই অ্যাক্সেস লাগবে
    }

    public function index()
    {
        $query = Customer::query();

        if (auth()->user()->role !== 'admin') {
            $query->where('branch_id', session('active_branch_id'));
        }

        $customers = $query->latest()->paginate(20);
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        $branches = Branch::all();
        return view('admin.customers.create', compact('branches'));
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

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        $this->authorizeAccess($customer);

        return view('admin.customers.edit', compact('customer'));
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

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function show(Customer $customer)
    {
        $this->authorizeAccess($customer);

        return view('admin.customers.show', compact('customer'));
    }

    public function destroy(Customer $customer)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
    }

    private function authorizeAccess(Customer $customer)
    {
        if (auth()->user()->role !== 'admin' && $customer->branch_id !== session('active_branch_id')) {
            abort(403, 'Unauthorized.');
        }
    }
}