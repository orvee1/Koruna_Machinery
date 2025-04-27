<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index()
    {
        $query = Customer::query();

        // Worker হলে শুধু নিজের branch এর কাস্টমার দেখবে
        if (Auth::user()->role === 'worker' || Auth::user()->role === 'manager') {
            $query->where('branch_id', session('active_branch_id'));
        }

        $customers = $query->latest()->paginate(20);
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
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
            'branch_id'  => session('active_branch_id'), // Worker/Manager নিজে লগিন করা ব্রাঞ্চ থেকে
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        if (Auth::user()->role !== 'admin' && $customer->branch_id !== session('active_branch_id')) {
            abort(403);
        }

        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        if (Auth::user()->role !== 'admin' && $customer->branch_id !== session('active_branch_id')) {
            abort(403);
        }

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
        if (Auth::user()->role !== 'admin' && $customer->branch_id !== session('active_branch_id')) {
            abort(403);
        }

        return view('admin.customers.show', compact('customer'));
    }

    public function destroy(Customer $customer)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
    }
}
