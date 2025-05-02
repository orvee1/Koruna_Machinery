<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin,manager');
    }

    public function index(Request $request)
    {
        // Get the selected date from the request or use today's date if not provided
        $date = $request->get('date', null);  // Allow null for the first load, so we get all records initially
        $search = $request->get('search', '');
    
        $query = Product::with('branch');
    
        // If the user is a manager, restrict results to the active branch
        if (Auth::check() && Auth::user()->role == 'manager') {
            $query->where('branch_id', session('active_branch_id'));
        }
    
        // Apply the search query for product name and supplier name
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
    
        // If a date is provided, filter by that date
        if ($date) {
            $query->whereDate('created_at', $date);
        }
    
        // Fetch part stocks with the necessary relationships and pagination
        $products = $query->paginate(10);
    
        // Return the view with the filtered part stocks
        return view('manager.products.index', compact('products', 'date', 'search'));
    }
    

    public function create()
    {
        $branches = Branch::all(); // Get all branches (only for manager)
        return view('manager.products.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'buying_price'    => 'required|numeric',
            'selling_price'   => 'nullable|numeric',
            'stock_quantity'  => 'required|integer|min:0',
        ]);

        Product::create([
            'name'             => $request->name,
            'buying_price'     => $request->buying_price,
            'selling_price'    => $request->selling_price,
            'stock_quantity'   => $request->stock_quantity,
            'branch_id'        => session('active_branch_id'), // ব্রাঞ্চ নির্ধারণ
        ]);

        return redirect()->route('manager.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        return view('manager.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'buying_price'    => 'required|numeric',
            'selling_price'   => 'nullable|numeric',
            'stock_quantity'  => 'required|integer|min:0',
        ]);

        $product->update($request->all());

        return redirect()->route('manager.products.index')->with('success', 'Product updated successfully.');

    }

    public function show(Product $product)
    {  
        return view('manager.products.show', compact('product'));
    }

        public function updatePayment(Request $request, Product $product)
    {
        // Validate input fields
        $request->validate([
            'paid_amount' => 'required|decimal:0,2',
            'payment_date' => 'required|date',
        ]);

        // Update payment details for the product
        $payment = new ProductPayment([
            'paid_amount' => $request->paid_amount,
            'payment_date' => $request->payment_date,
            'product_id' => $product->id,
        ]);

        $payment->save();

        // Redirect back with success message
        return redirect()->route('manager.products.show', $product->id)->with('success', 'Payment updated successfully.');
    }


}
