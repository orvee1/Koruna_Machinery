<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function index()
    {
        // Get the necessary data for the homepage
        $totalProducts = Product::count();
        $totalSales = Sale::sum('total_amount');
        $totalCustomers = Customer::count();
        $totalStock = Stock::sum('quantity');
        $branches = Branch::all();

        // Pass data to the view
        return view('homepage.index', compact('totalProducts', 'totalSales', 'totalCustomers', 'totalStock', 'branches'));
    }
}
