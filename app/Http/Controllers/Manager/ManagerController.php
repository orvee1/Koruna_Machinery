<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Branch;
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

        $branch = Branch::find($branchId);

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
