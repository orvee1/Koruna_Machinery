<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkRole:worker'); // Ensures only Workers can access
    }

    /**
     * Worker Dashboard
     */
    public function dashboard()
    {
        return view('worker.dashboard');
    }

    /**
     * View Sales History (Only for logged-in worker)
     */
    public function salesHistory()
    {
        $sales = Auth::user()->sales; // Get only sales from the logged-in worker
        return view('worker.sales.index', compact('sales'));
    }
}

