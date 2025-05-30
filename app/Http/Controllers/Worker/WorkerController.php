<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\PartStock;
use App\Models\PartstockSale;
use App\Models\ProductList;
use App\Models\ProductSale;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WorkerController extends Controller
{

    // Worker Dashboard (branch-specific)
     public function dashboard(Request $request)
    {
        $branchId = auth()->user()->branch_id;

        // Query Building
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

        return view('worker.dashboard', compact(
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


    // Show customer IDs for the worker's branch
    public function showCustomerIds()
    {
        $workerBranch = auth()->user()->branch_id;
        $customers = Customer::where('branch_id', $workerBranch)->get();
        return view('worker.customers.index', compact('customers'));
    }

}
