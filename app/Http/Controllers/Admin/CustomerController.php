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
        $branchId = session('active_branch_id');

        $customers = Customer::where('branch_id', $branchId)
            ->with('branch')
            ->latest()
            ->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }


        public function create()
    {
        $branchId = session('active_branch_id');

        $branch = Branch::findOrFail($branchId);

        return view('admin.customers.create', compact('branch'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'phone'     => 'required|string|max:20|unique:customers,phone',
            'district'  => 'nullable|string|max:255',
        ]);


        $branchId = session('active_branch_id');

    
        Customer::create([
            'name'       => $request->name,
            'phone'      => $request->phone,
            'district'   => $request->district,
            'branch_id'  => $branchId,
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        if ($customer->branch_id !== session('active_branch_id')) {
            abort(403, 'Unauthorized action.');
        }

        $branch = Branch::find(session('active_branch_id'));

        return view('admin.customers.edit', compact('customer', 'branch'));
    }


        public function update(Request $request, Customer $customer)
        {
        
            if ($customer->branch_id !== session('active_branch_id')) {
                abort(403, 'Unauthorized action.');
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
            try {
                $productSales = $customer->productSales()
                    ->with(['stock.product', 'seller'])
                    ->latest()
                    ->get();

                $grandTotal = $productSales->sum(function ($sale) {
                    return $sale->unit_price * $sale->quantity;
                });

                $grandPaid = $productSales->sum('paid_amount');
                $grandDue = $grandTotal - $grandPaid;

            } catch (\Exception $e) {
                Log::error("CustomerController@show: Invoice loading failed (Customer ID: {$customer->id}): " . $e->getMessage());

                return redirect()
                    ->route('admin.customers.index')
                    ->withErrors('দুঃখিত, এই মুহূর্তে কাস্টমার ইনভয়েস লোড করা যাচ্ছে না।');
            }

            return view('admin.customers.show', compact(
                'customer',
                'productSales',
                'grandTotal',
                'grandPaid',
                'grandDue'
            ));
        }
    

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
    }

}