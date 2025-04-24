<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    public function index()
    {
        $customers = Customer::with('branch')->paginate(10);
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
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:customers',
            'district' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
        ]);

        Customer::create($request->all());

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully.');
    }

        public function show(Customer $customer)
    {
      
        $customer->load('branch'); 
        

        return view('admin.customers.show', compact('customer'));
    }


    public function edit(Customer $customer)
    {
        $branches = Branch::all();
        return view('admin.customers.edit', compact('customer', 'branches'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:customers,phone,' . $customer->id,
            'district' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $customer->update($request->all());

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully.');
    }
}
