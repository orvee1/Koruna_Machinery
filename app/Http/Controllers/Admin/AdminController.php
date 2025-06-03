<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Stock;
use App\Models\PartStock;
use App\Models\User;
use Illuminate\Http\Request;
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

        $from = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : null;
        $to = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : null;
        $month = $request->month;
        $year = $request->year;

        $stockQuery = Stock::where('branch_id', $branchId);
        $partStockQuery = PartStock::where('branch_id', $branchId);
        $billQuery = Bill::with('customer')->where('branch_id', $branchId);

        if ($from && $to) {
            $billQuery->whereBetween('created_at', [$from, $to]);
        } elseif ($month) {
            $billQuery->whereMonth('created_at', $month);
        } elseif ($year) {
            $billQuery->whereYear('created_at', $year);
        }

        $bills = $billQuery->get();

        $totalSales = $bills->sum('total_amount');
        $totalDueToHave = $bills->sum('due_amount');
        $stockDue = $stockQuery->sum('due_amount');
        $partStockDue = $partStockQuery->sum('due_amount');
        $totalDue = $stockDue + $partStockDue;
        $stockValue = $stockQuery->sum('total_amount');
        $partStockValue = $partStockQuery->sum('total_amount');
        $totalProductValue = $stockValue + $partStockValue;

        $profitBills = clone $billQuery;

        if (!$from && !$month && !$year) {
            $today = Carbon::today();
            $tomorrow = Carbon::tomorrow();
            $profitBills->whereBetween('created_at', [$today, $tomorrow]);
        }

        $productProfit = 0;
        $partStockProfit = 0;

        foreach ($profitBills->get() as $bill) {
            foreach ($bill->product_details ?? [] as $item) {
                $type = $item['type'];
                $id = $item['id'];
                $quantity = $item['quantity'];
                $unitPrice = $item['unit_price'];
                $profit = 0;

                if ($type === 'product') {
                    $stock = \App\Models\Stock::find($id);
                    if ($stock) {
                        $profit = ($unitPrice - $stock->buying_price) * $quantity;
                        $productProfit += $profit > 0 ? $profit : 0;
                    }
                } elseif ($type === 'partstock') {
                    $part = \App\Models\PartStock::find($id);
                    if ($part) {
                        $profit = ($unitPrice - $part->buying_price) * $quantity;
                        $partStockProfit += $profit > 0 ? $profit : 0;
                    }
                }
            }
        }

        $totalProfit = $productProfit + $partStockProfit;

        $users = User::where('branch_id', $branchId)->get();

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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'password_confirmation' => 'nullable|string|min:6',
            'role' => 'required|in:admin,worker,manager',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        if ($validated['role'] === 'admin') {
            $validated['branch_id'] = null;
        }

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

        public function show(User $user, Request $request)
    {

        $bills = Bill::with(['customer'])
            ->where('seller_id', $user->id)
            ->when($request->from_date && $request->to_date, fn($q) =>
                $q->whereBetween('created_at', [$request->from_date, $request->to_date])
            )
            ->when($request->month, fn($q) => $q->whereMonth('created_at', $request->month))
            ->when($request->year, fn($q) => $q->whereYear('created_at', $request->year))
            ->latest()
            ->paginate(20);

        $totalRevenue = 0;
        $totalProfit = 0;
        $salesList = [];

        foreach ($bills as $bill) {
            foreach ($bill->product_details ?? [] as $item) {
                $type = $item['type'] ?? null;
                $id = $item['id'] ?? null;
                $qty = $item['quantity'] ?? 0;
                $unitPrice = $item['unit_price'] ?? 0;
                $total = $qty * $unitPrice;

                $productName = 'Unknown';
                $buyingPrice = 0;

                if ($type === 'product') {
                    $stock = \App\Models\Stock::find($id);
                    if ($stock) {
                        $productName = $stock->product_name;
                        $buyingPrice = $stock->buying_price;
                    }
                } elseif ($type === 'partstock') {
                    $part = \App\Models\PartStock::find($id);
                    if ($part) {
                        $productName = $part->product_name;
                        $buyingPrice = $part->buying_price;
                    }
                }

                $profit = ($unitPrice - $buyingPrice) * $qty;
                $totalRevenue += $total;
                $totalProfit += $profit;

                $salesList[] = [
                    'bill_id' => $bill->id,
                    'customer' => $bill->customer?->name ?? 'N/A',
                    'product_name' => $productName,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'total_amount' => $total,
                    'date' => $bill->created_at->format('d M, Y'),
                    'type' => $type,
                ];
            }
        }

        return view('admin.users.show', [
            'user' => $user,
            'bills' => $bills,
            'salesList' => collect($salesList),
            'totalRevenue' => $totalRevenue,
            'totalProfit' => $totalProfit,
        ]);
    }

}
