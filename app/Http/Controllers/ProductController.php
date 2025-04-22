<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Branch;
use App\Models\ProductPayment;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Display a list of all products.
     */
    public function index(Request $request)
    {
        // Get the selected date from the request or use today's date if not provided
        $date = $request->get('date', null);  // Allow null for the first load, so we get all records initially
        $search = $request->get('search', '');
    
        $query = Product::with('branch');
    
        // Apply the search query for product name and supplier name
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', '%' . $search . '%');
                //   ->orWhere('supplier_name', 'like', '%' . $search . '%');
            });
        }
    
        // If a date is provided, filter by that date
        if ($date) {
            $query->whereDate('created_at', $date);
        }
    
        // Fetch part stocks with the necessary relationships and pagination
        $products = $query->paginate(10);
    
        // Return the view with the filtered part stocks
        return view('admin.products.index', compact('products', 'date', 'search'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $branches = Branch::all();
        return view('admin.products.create', compact('branches'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'buying_price' => 'required|decimal:0,2',
            'selling_price' => 'nullable|decimal:0,2',
            'stock_quantity' => 'required|integer',
            'branch_id' => 'required|exists:branches,id',
        ]);
    
        // Create the product
        $product = Product::create($request->all());
    
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }
    

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $branches = Branch::all();
        return view('admin.products.edit', compact('product', 'branches'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'buying_price' => 'required|decimal:0,2',
        'selling_price' => 'nullable|decimal:0,2',
        'stock_quantity' => 'required|integer',
        'branch_id' => 'required|exists:branches,id',
    ]);

    // Update the product
    $product->update($request->all());

    // Recalculate the total purchase amount after product update
    // $product->calculateTotalPurchaseAmount($request->buying_price, $request->stock_quantity);

    return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
}


    public function updatePayment(Product $product, Request $request)
    {
        $request->validate([
            'paid_amount' => 'required|decimal:0,2',
            'payment_date' => 'required|date',
        ]);
    
       
        $product_payment = new ProductPayment([
            'product_id' => $product->id,
            'paid_amount' => $request->paid_amount,
            'payment_date' => $request->payment_date,
        ]);
    
        $product_payment->save();
        
        return back()->with('success', 'Payment updated successfully.');
    }
    

    /**
     * Show the details of a specific product.
     */
    public function show(Product $product)
    {
        $payments = $product->payments()->orderBy('payment_date', 'desc')->get();
        return view('admin.products.show', compact('product','payments'));
    }
}
