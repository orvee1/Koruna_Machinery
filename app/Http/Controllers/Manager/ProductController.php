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
        $this->middleware('checkRole:manager');
    }

    public function index(Request $request)
    {
        $query = Product::query();

        // Worker বা Manager হলে নিজের ব্রাঞ্চ ফিল্টার
        if (Auth::user()->role == 'manager') {
            $query->where('branch_id', session('active_branch_id'));
        }

        $products = $query->latest()->paginate(20);
        return view('manager.products.index', compact('products'));
    }

    public function create()
    {
        $branches = Branch::all(); // Get all branches (only for manager)
        // if (Auth::user()->role === 'manager') {
        //     return view('manager.products.create', compact('branches'));
        // } else {
        //     return view('manager.products.create', compact('branches'));
        // }
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

        // if(Auth::user()->role === 'manager') {
        //     return redirect()->route('manager.products.index')->with('success', 'Product created successfully.');
        // }else {
        //     return redirect()->route('manager.products.index')->with('success', 'Product created successfully.');
        // }
        return redirect()->route('manager.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        // Only allow editing if the product belongs to the active branch
        // if (Auth::user()->role == 'manager' && $product->branch_id == session('active_branch_id')) {
        //     return view('manager.products.edit', compact('product'));
        // }   
        // else {
        //     abort(403);
        // }

        return view('manager.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        if (Auth::user()->role == 'manager' && $product->branch_id == session('active_branch_id')) {

        $request->validate([
            'name'            => 'required|string|max:255',
            'buying_price'    => 'required|numeric',
            'selling_price'   => 'nullable|numeric',
            'stock_quantity'  => 'required|integer|min:0',
        ]);

        $product->update($request->only('name', 'buying_price', 'selling_price', 'stock_quantity'));

        return redirect()->route('manager.products.index')->with('success', 'Product updated successfully.');
    }
        else {
            abort(403);
        }
    }

    public function show(Product $product)
    {
        // if (Auth::user()->role == 'manager' && $product->branch_id == session('active_branch_id')) {
        // return view('manager.products.show', compact('product'));
        //     } else {
        //     abort(403);
        // }   

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
