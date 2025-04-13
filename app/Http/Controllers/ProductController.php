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
    public function index()
    {
        $products = Product::with('branch')->paginate(10); // Fetch products with branch relationship
        return view('admin.products.index', compact('products'));
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
       $product = $request->validate([
            'name' => 'required|string|max:255',
            'buying_price' => 'required|numeric',
            'selling_price' => 'nullable|numeric',
            'stock_quantity' => 'required|integer',
            'branch_id' => 'required|exists:branches,id',
        ]);
    
        // Create the product
        $product = Product::create($request->all());
    
        // Calculate the total purchase amount after product creation
        $product->calculateTotalPurchaseAmount($request->buying_price, $request->stock_quantity);
    
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
        'buying_price' => 'required|numeric',
        'selling_price' => 'required|numeric',
        'stock_quantity' => 'required|integer',
        'branch_id' => 'required|exists:branches,id',
    ]);

    // Update the product
    $product->update($request->all());

    // Recalculate the total purchase amount after product update
    $product->calculateTotalPurchaseAmount($request->buying_price, $request->stock_quantity);

    return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
}




    public function calculateTotalPurchase(Product $product, Request $request)
    {
        $request->validate([
            'stock_quantity' => 'required|integer|min:1',
            'buying_price' => 'required|numeric',
        ]);

        $product->buying_price = $request->buying_price;
        $product->stock_quantity = $request->stock_quantity;
        $product->save();
        $product->calculateTotalPurchaseAmount($request->buying_price, $request->stock_quantity);

        return back()->with('success', 'Total purchase amount updated successfully.');
    }

    public function updatePayment(Product $product, Request $request)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
        ]);
    
       
        $product_payment = new ProductPayment([
            'product_id' => $product->id,
            'paid_amount' => $request->paid_amount,
            'payment_date' => $request->payment_date,
        ]);
    
        $product_payment->save();
        
        $product->paid_amount += $request->paid_amount;
        $product->save();
    
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
