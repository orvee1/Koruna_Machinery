<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillPayment;
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

       $bills = Bill::with(['customer','seller'])
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
        $bill->load(['customer', 'seller']);
        return view('worker.sales.show', compact('bill'));
    }

    public function updatePayment(Request $request, Bill $bill)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
        ]);

        if ($request->paid_amount > $bill->due_amount) {
            return back()->withErrors(['paid_amount' => 'Amount exceeds total due.']);
        }

        BillPayment::create([
            'bill_id' => $bill->id,
            'paid_amount' => $request->paid_amount,
            'payment_date' => $request->payment_date,
        ]);

        $bill->paid_amount += $request->paid_amount;
        $bill->due_amount = max($bill->total_amount - $bill->paid_amount, 0);
        $bill->save();

        return back()->with('success', 'Payment updated and logged successfully.');
    }


    public function destroy(Bill $bill)
    {
        $bill->delete();

        return redirect()->route('worker.sales.index')->with('success', 'Bill and all related sales deleted successfully.');
    }
}
