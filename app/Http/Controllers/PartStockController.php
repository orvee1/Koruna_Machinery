<?php

namespace App\Http\Controllers;

use App\Models\PartStock;
use Illuminate\Http\Request;

class PartStockController extends Controller
{
    public function index()
    {
        $partStocks = PartStock::paginate(10);

        return view('admin.part-stocks.index', compact('partStocks'));
    }

    public function create()
    {
        $partStocks = PartStock::all();

        return view('admin.part-stocks.create', compact('partStocks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            
        ]);
    }
}
