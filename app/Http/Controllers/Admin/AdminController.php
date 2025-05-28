<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Investor;
use App\Models\PartStock;
use App\Models\PartStockSale;
use App\Models\ProductList;
use App\Models\ProductSale;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
     public function __construct()
    {
        $this->middleware('checkRole:admin'); 
    }

     public function dashboard(Request $request)
    {
        $branchId = session('active_branch_id');

        if (!$branchId) {
            return redirect()->route('admin.select-branch')->with('error', 'Please select a branch first.');
        }

        $branch = Branch::find($branchId);

        $from = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : null;
        $to = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : null;
        $month = $request->month;
        $year = $request->year;

        $productSaleQuery = ProductSale::where('branch_id', $branchId);
        $partSaleQuery = PartStockSale::where('branch_id', $branchId);
        $productListQuery = ProductList::where('branch_id', $branchId);
        $stockQuery = Stock::where('branch_id', $branchId);
        $partStockQuery = PartStock::where('branch_id', $branchId);

        $productProfitQuery = ProductSale::with('stock')->where('branch_id', $branchId);
        $partProfitQuery = PartStockSale::with('partStock')->where('branch_id', $branchId);

        if ($from && $to) {
            $productSaleQuery->whereBetween('created_at', [$from, $to]);
            $partSaleQuery->whereBetween('created_at', [$from, $to]);
            $productListQuery->whereBetween('created_at', [$from, $to]);
            $stockQuery->whereBetween('created_at', [$from, $to]);
            $partStockQuery->whereBetween('created_at', [$from, $to]);

            $productProfitQuery->whereBetween('created_at', [$from, $to]);
            $partProfitQuery->whereBetween('created_at', [$from, $to]);
        } elseif ($month) {
            $productSaleQuery->whereMonth('created_at', $month);
            $partSaleQuery->whereMonth('created_at', $month);
            $productListQuery->whereMonth('created_at', $month);
            $stockQuery->whereMonth('created_at', $month);
            $partStockQuery->whereMonth('created_at', $month);

            $productProfitQuery->whereMonth('created_at', $month);
            $partProfitQuery->whereMonth('created_at', $month);
        } elseif ($year) {
            $productSaleQuery->whereYear('created_at', $year);
            $partSaleQuery->whereYear('created_at', $year);
            $productListQuery->whereYear('created_at', $year);
            $stockQuery->whereYear('created_at', $year);
            $partStockQuery->whereYear('created_at', $year);

            $productProfitQuery->whereYear('created_at', $year);
            $partProfitQuery->whereYear('created_at', $year);
        } else {
          
            $today = Carbon::today();
            $tomorrow = Carbon::tomorrow();

            $productProfitQuery->whereBetween('created_at', [$today, $tomorrow]);
            $partProfitQuery->whereBetween('created_at', [$today, $tomorrow]);
        }

        $totalProductSales = $productSaleQuery->sum('total_amount');
        $totalPartStockSales = $partSaleQuery->sum('total_amount');
        $totalSales = $totalProductSales + $totalPartStockSales;

        // $totalProductValue = $productListQuery->sum('total_amount');
        $stockValue = $stockQuery->sum('total_amount');
        $partStockValue = $partStockQuery->sum('total_amount');
        $totalProductValue = $stockValue + $partStockValue;
        
        $productDue = $stockQuery->sum('due_amount');
        $partStockDue = $partStockQuery->sum('due_amount');
        $totalDue = $productDue + $partStockDue;

        $productDueToHave = $productSaleQuery->sum('due_amount');
        $partStockDueToHave = $partSaleQuery->sum('due_amount');
        $totalDueToHave = $productDueToHave + $partStockDueToHave;

        $productProfit = $productProfitQuery->get()->sum(function ($s) {
            return ($s->unit_price - optional($s->stock)->buying_price) * $s->quantity;
        });

        $partStockProfit = $partProfitQuery->get()->sum(function ($s) {
            return ($s->unit_price - optional($s->partStock)->buying_price) * $s->quantity;
        });

        $totalProfit = $productProfit + $partStockProfit;

        $users = User::where('branch_id', $branchId)->with('branch')->get();

        return view('admin.dashboard', compact(
            'totalSales',
            'totalProductValue',
            'users',
            'totalProfit',
            'totalDue',
            'totalDueToHave',
            'from',
            'to',
            'month',
            'year'
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
        $productQuery = ProductSale::with(['stock', 'customer'])->where('seller_id', $user->id);
        $partQuery = PartStockSale::with(['partStock', 'customer'])->where('seller_id', $user->id);

        // Filter by date
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $productQuery->whereBetween('created_at', [$request->from_date, $request->to_date]);
            $partQuery->whereBetween('created_at', [$request->from_date, $request->to_date]);
        } elseif ($request->filled('month')) {
            $productQuery->whereMonth('created_at', $request->month);
            $partQuery->whereMonth('created_at', $request->month);
        } elseif ($request->filled('year')) {
            $productQuery->whereYear('created_at', $request->year);
            $partQuery->whereYear('created_at', $request->year);
        }

        $productSales = (clone $productQuery)
            ->orderBy('created_at', 'desc')
            ->paginate(20, ['*'], 'product_page')
            ->appends($request->except('partstock_page'));

        $partStockSales = (clone $partQuery)
            ->orderBy('created_at', 'desc')
            ->paginate(20, ['*'], 'partstock_page')
            ->appends($request->except('product_page'));
    
        $profitProductQuery = clone $productQuery;
        $profitPartQuery = clone $partQuery;

        if (!$request->filled('from_date') && !$request->filled('month') && !$request->filled('year')) {
            $today = Carbon::today();
            $tomorrow = Carbon::tomorrow();
            $profitProductQuery->whereBetween('created_at', [$today, $tomorrow]);
            $profitPartQuery->whereBetween('created_at', [$today, $tomorrow]);
        }

        $totalRevenue = (
            $productQuery->sum(DB::raw('quantity * unit_price')) +
            $partQuery->sum(DB::raw('quantity * unit_price'))
        );

        $totalProfit = (
            $profitProductQuery->get()->sum(function ($s) {
                return ($s->unit_price - $s->stock->buying_price) * $s->quantity;
            }) +
            $profitPartQuery->get()->sum(function ($s) {
                return ($s->unit_price - $s->partStock->buying_price) * $s->quantity;
            })
        );

        return view('admin.users.show', [
            'user'           => $user,
            'productSales'   => $productSales,
            'partStockSales' => $partStockSales,
            'totalRevenue'   => $totalRevenue,
            'totalProfit'    => $totalProfit,
        ]);
    }


}