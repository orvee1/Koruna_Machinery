<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;

class WorkerUnifiedSaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin,worker');
    }

    public function index(Request $request)
    {
        $branchId = auth()->user()->branch_id;

        $bills = Bill::with(['customer', 'productSales.stock', 'partStockSales.partStock', 'seller'])
            ->where('branch_id', $branchId)
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->when($request->month, fn($q) => $q->whereMonth('created_at', $request->month))
            ->when($request->year, fn($q) => $q->whereYear('created_at', $request->year))
            ->when($request->status === 'paid', fn($q) => $q->where('due_amount', 0))
            ->when($request->status === 'due', fn($q) => $q->where('due_amount', '>', 0))
            ->latest()
            ->get();

        return view('worker.sales.index', compact('bills'));
    }

    public function show(Bill $bill)
    {
        $bill->load(['customer', 'productSales.stock', 'partStockSales.partStock', 'seller']);
        return view('worker.sales.show', compact('bill'));
    }

    public function destroy(Bill $bill)
    {
        $bill->productSales()->delete();
        $bill->partStockSales()->delete();
        $bill->delete();

        return redirect()->route('worker.sales.index')->with('success', 'Bill and all related sales deleted successfully.');
    }
}
