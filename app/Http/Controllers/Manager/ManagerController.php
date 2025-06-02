<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Investor;
use App\Models\PartStock;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin,manager');
    }
    
     public function dashboard(Request $request)
    {
        $branchId = auth()->user()->branch_id;

        // Query Building
        $branch = Branch::find($branchId);

        $from = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : null;
        $to = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : null;
        $month = $request->month;
        $year = $request->year;

       $billQuery = Bill::with('customer')->where('branch_id', $branchId);
        $stockQuery = Stock::where('branch_id', $branchId);
        $partStockQuery = PartStock::where('branch_id', $branchId);

        if ($from && $to) {
            $billQuery->whereBetween('created_at', [$from, $to]);
        } elseif ($month) {
            $billQuery->whereMonth('created_at', $month);
        } elseif ($year) {
            $billQuery->whereYear('created_at', $year);
        } else {
            $today = Carbon::today();
            $tomorrow = Carbon::tomorrow();
            $billQuery->whereBetween('created_at', [$today, $tomorrow]);
        }

        $bills = $billQuery->get();

        $totalSales = $bills->sum('total_amount');
        $totalDueToHave = $bills->sum('due_amount');
        $stockDue = $stockQuery->sum('due_amount');
        $partStockDue = $partStockQuery->sum('due_amount');
        $totalDue = $stockDue + $partStockDue;

        $productProfit = Stock::where('branch_id', $branchId)->sum('total_profit');
        $partStockProfit = PartStock::where('branch_id', $branchId)->sum('total_profit');
        $totalProfit = $productProfit + $partStockProfit;


        $stockValue = $stockQuery->sum('total_amount');
        $partStockValue = $partStockQuery->sum('total_amount');
        $totalProductValue = $stockValue + $partStockValue;

        $users = User::where('branch_id', $branchId)->get();

        return view('manager.dashboard', compact(
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
}
