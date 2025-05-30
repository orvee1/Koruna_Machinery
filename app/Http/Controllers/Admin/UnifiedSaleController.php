<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;

class UnifiedSaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin');
    }

    public function index(Request $request)
    {
        $branchId = session('active_branch_id');

        $bills = Bill::with(['customer', 'productSales.stock', 'partStockSales.partStock', 'seller'])
            ->where('branch_id', $branchId)
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->when($request->month, fn($q) => $q->whereMonth('created_at', $request->month))
            ->when($request->year, fn($q) => $q->whereYear('created_at', $request->year))
            ->when($request->status === 'paid', fn($q) => $q->where('due_amount', 0))
            ->when($request->status === 'due', fn($q) => $q->where('due_amount', '>', 0))
            ->latest()
            ->get();

        return view('admin.sales.index', compact('bills'));
    }

    public function show(Bill $bill)
    {
        $bill->load(['customer', 'productSales.stock', 'partStockSales.partStock', 'seller']);

        return view('admin.sales.show', compact('bill'));
    }

    public function destroy(Bill $bill)
    {
        foreach ($bill->productSales as $sale) {
            $sale->delete();
        }

        foreach ($bill->partStockSales as $sale) {
            $sale->delete();
        }

        $bill->delete();

        return redirect()->route('admin.sales.index')->with('success', 'Bill and all related sales deleted successfully.');
    }
}


