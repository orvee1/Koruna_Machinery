<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Investor;
use App\Models\PartStock;
use App\Models\PartstockSale;
use App\Models\Product;
use App\Models\ProductSale;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;

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
        $totalProductSales = ProductSale::where('branch_id', $branchId)->sum('paid_amount');
        $totalPartstockSales = PartstockSale::where('branch_id', $branchId)->sum('paid_amount');
        $totalSales = $totalProductSales + $totalPartstockSales;
    
        $totalProductValue = Product::where('branch_id', $branchId)->sum('buying_price');
        $totalPartStockValue = PartStock::where('branch_id', $branchId)->sum('buy_value');
        $totalValue = $totalProductValue + $totalPartStockValue;

        $productProfit = Stock::where('branch_id', $branchId)->sum('total_profit');
        $partStockProfit = PartStock::where('branch_id', $branchId)->sum('total_profit');
        $totalProfit = $productProfit + $partStockProfit;

        $productDue = Stock::where('branch_id', $branchId)->sum('due_amount');
        $partStockDue = PartStock::where('branch_id', $branchId)->sum('due_amount');
        $totalDue = $productDue + $partStockDue;

        $productDueToHave = ProductSale::where('branch_id', $branchId)->sum('due_amount');
        $partStockDueToHave = PartstockSale::where('branch_id', $branchId)->sum('due_amount');
        $totalDueToHave = $productDueToHave + $partStockDueToHave;

        // Optional: all users of this branch
        $users = User::where('branch_id', $branchId)->with('branch')->get();
    
        return view('admin.dashboard', compact(
            'totalSales',
            'totalValue',
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
            'email' => 'required|email|unique:users',
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
}