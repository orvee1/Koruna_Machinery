<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin');
    }

    // শুধুমাত্র তালিকা দেখাবে
    public function index(Request $request)
    {
        $query = Product::with('branch')->latest();

        // ডেট ফিল্টার (last_purchase_date অনুযায়ী)
        if ($request->filled('date')) {
            $query->whereDate('last_purchase_date', $request->input('date'));
        }

        // সার্চ ফিল্টার (name বা branch name)
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        $products = Product::latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    // শুধু বিস্তারিত দেখাবে
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }
}
