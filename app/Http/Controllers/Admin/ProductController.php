<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
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
        $branchId = session('active_branch_id'); // ✅ বর্তমান ব্রাঞ্চ আইডি নেওয়া হচ্ছে

        // ✅ স্টক থেকে প্রোডাক্টের ইনফর্মেশন ফেচ করা হচ্ছে
        $query = Stock::with('branch')
            ->where('branch_id', $branchId)
            ->where('quantity', '>', 0) // ✅ স্টকে যেগুলো আছে সেগুলোই দেখানো হচ্ছে
            ->latest();

        // ✅ তারিখ অনুসারে ফিল্টার
        if ($request->filled('date')) {
            $query->whereDate('purchase_date', $request->input('date'));
        }

        // ✅ সার্চ ফিল্টার
        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%' . $request->input('search') . '%')
                ->orWhereHas('branch', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->input('search') . '%');
                });
        }

        // ✅ পেজিনেশন সহ ডেটা ফেচ করা হচ্ছে
        $stocks = $query->paginate(20);

        // ✅ ভিউতে ডেটা পাঠানো হচ্ছে
        return view('admin.products.index', compact('stocks'));
    }



    // শুধু বিস্তারিত দেখাবে
    public function show(Product $product)
    {
        $stock = Stock::where('product_name', $product->name)
                    ->where('branch_id', $product->branch_id)
                    ->first();

        return view('admin.products.show', compact('stock'));
    }

}
