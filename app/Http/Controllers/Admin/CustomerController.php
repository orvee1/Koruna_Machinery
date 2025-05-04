<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin');
    }

    public function index()
    {
        $query = Customer::query();
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
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'phone'     => 'required|string|max:20|unique:customers,phone,' . $customer->id,
            'district'  => 'nullable|string|max:255',
            'branch_id'  => session('active_branch_id'),
        ]);

        $customer->update($request->only('name', 'phone', 'district'));

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function show(Customer $customer)
    {
        try {
            // ১) eager–load করে N+1 সমস্যা এড়ানো
            $sales = $customer
                ->productsales()
                ->with(['product', 'seller'])
                ->latest()
                ->get();

            // ২) যদি ইনভয়েস তৈরির বিশেষ লজিক থাকে, তখন service বা view helper এ পাঠিয়ে দিতে পারেন
        } catch (\Exception $e) {
            // ৩) ব্যর্থ হলে লগ লিখে redirect ও friendly error দেখানো
            Log::error("CustomerController@show: বিক্রয় লোডিংয়ে সমস্যা (Customer ID: {$customer->id}): " . $e->getMessage());

            return redirect()
                ->route('admin.customers.index')
                ->withErrors('দুঃখিত, এই মুহূর্তে কাস্টমার ইনভয়েস লোড করা যাচ্ছে না।');
        }

        // ৪) সফল হলে view–এ ডেটা পাঠানো
        return view('admin.customers.show', compact('customer', 'sales'));
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
    }

}