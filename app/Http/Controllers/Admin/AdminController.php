<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Investor;
use App\Models\PartStock;
use App\Models\PartStockSale;
use App\Models\Product;
use App\Models\ProductList;
use App\Models\ProductSale;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin'); // ✅ Now using checkRole middleware cleanly
    }

    public function dashboard()
    {
        
        $branchId = session('active_branch_id');
        // Check if branchId is set in the session
        if (!$branchId) {
            return redirect()->route('admin.select-branch')->with('error', 'Please select a branch first.');
        }
        $branch = Branch::find($branchId);
        // Sales
        $totalProductSales = ProductSale::where('branch_id', $branchId)->sum('total_amount');
        $totalPartstockSales = PartStockSale::where('branch_id', $branchId)->sum('total_amount');
        $totalSales = $totalProductSales + $totalPartstockSales;
    
        // $totalProductValue = Product::where('branch_id', $branchId)->sum('buying_price');
        $totalProductValue = ProductList::where('branch_id', $branchId)->sum('total_amount');
        
        $productProfit = Stock::where('branch_id', $branchId)->sum('total_profit');
        $partStockProfit = PartStock::where('branch_id', $branchId)->sum('total_profit');
        $totalProfit = $productProfit + $partStockProfit;

        $productDue = Stock::where('branch_id', $branchId)->sum('due_amount');
        $partStockDue = PartStock::where('branch_id', $branchId)->sum('due_amount');
        $totalDue = $productDue + $partStockDue;

        $productDueToHave = ProductSale::where('branch_id', $branchId)->sum('due_amount');
        $partStockDueToHave = PartStockSale::where('branch_id', $branchId)->sum('due_amount');
        $totalDueToHave = $productDueToHave + $partStockDueToHave;

        // Optional: all users of this branch
        $users = User::where('branch_id', $branchId)->with('branch')->get();
    
        return view('admin.dashboard', compact(
            'totalSales',
            'totalProductValue',
            'users',
            'totalProfit',
            'totalDue',
            'totalDueToHave',
        ));
    }
    

    public function index()
    {
        $users = User::with('branch')->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $branches = Branch::all();
        return view('admin.users.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
            'role' => 'required|in:admin,worker,manager',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        if ($request->role === 'admin') {
            $request->merge(['branch_id' => null]);
        }

        User::create($request->all());

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    public function edit(User $user)
    {
        $branches = Branch::all();
        return view('admin.users.edit', compact('user', 'branches'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'password_confirmation' => 'nullable|string|min:6',
            'role' => 'required|in:admin,worker,manager',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        if ($request->has('password') && $request->password) {
            $user->password = bcrypt($request->password);
        }

        if ($request->role === 'admin') {
            $request->merge(['branch_id' => null]);
        }

        $user->update($request->all());

        return redirect()->route('admin.dashboard')->with('success', 'User updated successfully!');
    }


public function show(User $user, Request $request)
{
    // ✅ ProductSales ফিল্টারিং
    $productSales = ProductSale::with(['product', 'customer'])
        ->where('seller_id', $user->id);

    if ($request->has('from_date') && $request->has('to_date')) {
        $productSales = $productSales->whereBetween('created_at', [$request->from_date, $request->to_date]);
    }

    if ($request->has('month')) {
        $productSales = $productSales->whereMonth('created_at', $request->month);
    }

    if ($request->has('year')) {
        $productSales = $productSales->whereYear('created_at', $request->year);
    }

    $productSales = $productSales->get()->map(function ($sale) {
        $sale->sale_type = 'ProductSale'; // সেল টাইপ নির্ধারণ
        $sale->total_amount = $sale->unit_price * $sale->quantity; // ✅ টোটাল অ্যামাউন্ট সেট
        return $sale;
    });

    // ✅ PartStockSales ফিল্টারিং
    $partStockSales = PartstockSale::with(['partStock', 'customer'])
        ->where('seller_id', $user->id);

    if ($request->has('from_date') && $request->has('to_date')) {
        $partStockSales = $partStockSales->whereBetween('created_at', [$request->from_date, $request->to_date]);
    }

    if ($request->has('month')) {
        $partStockSales = $partStockSales->whereMonth('created_at', $request->month);
    }

    if ($request->has('year')) {
        $partStockSales = $partStockSales->whereYear('created_at', $request->year);
    }

    $partStockSales = $partStockSales->get()->map(function ($sale) {
        $sale->sale_type = 'PartStockSale'; // সেল টাইপ নির্ধারণ
        $sale->total_amount = $sale->unit_price * $sale->quantity; // ✅ টোটাল অ্যামাউন্ট সেট
        return $sale;
    });

    // ✅ ডেটা সংগ্রহ এবং মার্জ করা
    $allSales = $productSales->merge($partStockSales);

    // ✅ সেলেকশন অনুযায়ী সাজানো এবং Pagination তৈরি
    $sales = $allSales->sortByDesc('created_at')->values();
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $perPage = 20;
    $currentItems = $sales->slice(($currentPage - 1) * $perPage, $perPage)->all();
    $paginatedSales = new LengthAwarePaginator($currentItems, $sales->count(), $perPage);

    // ✅ টোটাল রেভিনিউ এবং প্রফিট
    $totalRevenue = $allSales->sum(function ($sale) {
        return $sale->total_amount;
    });

    $totalProfit = $allSales->sum(function ($sale) {
        if ($sale->sale_type === 'ProductSale') {
            return ($sale->unit_price - $sale->product->buying_price) * $sale->quantity;
        } elseif ($sale->sale_type === 'PartStockSale') {
            return ($sale->unit_price - $sale->partStock->buying_price) * $sale->quantity;
        }
        return 0;
    });

    return view('admin.users.show', [
        'user' => $user,
        'sales' => $paginatedSales,
        'totalRevenue' => $totalRevenue,
        'totalProfit' => $totalProfit
    ]);
}





}