<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    LoginController,
    BranchSelectorController,
    AdminController,
    CustomerController,
    ProductController,
    StockController,
    PartStockController,
    ProductSaleController,
    PartstockSaleController,
    InvestorController,
    InvestmentHistoryController,
    BranchController,
    ManagerController,
    WorkerController,
    RegisterAdminController
};
use App\Models\Customer;
use App\Models\Investor;
use App\Models\PartstockSale;
use App\Models\Product;
use App\Models\ProductSale;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Show Login Form
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

// Submit Login
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Logged-in users only)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Admins: Branch Selection
    Route::get('/admin/select-branch', [BranchSelectorController::class, 'show'])->name('admin.select-branch');
    Route::post('/admin/select-branch', [BranchSelectorController::class, 'set'])->name('admin.select-branch.set');

    // Common Dashboard (after branch selection)
    Route::get('/dashboard', function () {
        $branchId = session('active_branch_id');

        $totalCustomers = Customer::where('branch_id', $branchId)->count();
        $totalProducts = Product::where('branch_id', $branchId)->count();
        $totalProductSales = ProductSale::where('branch_id', $branchId)->sum('paid_amount');
        $totalPartStockSales = PartstockSale::where('branch_id', $branchId)->sum('paid_amount');
        $totalSales = $totalProductSales + $totalPartStockSales;
        $totalInvestors = Investor::where('branch_id', $branchId)->count();

        return view('admin.dashboard', compact('totalCustomers', 'totalProducts', 'totalSales', 'totalInvestors'));
    })->name('dashboard');
    
    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('checkRole:admin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::resource('branches', BranchController::class);
        Route::resource('users', AdminController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
        Route::resource('customers', CustomerController::class);
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/update-payment', [ProductController::class, 'updatePayment'])->name('products.updatePayment');

        Route::resource('stocks', StockController::class);
        Route::resource('partstocks', PartStockController::class);
        Route::post('partstocks/{partStock}/update-payment', [PartStockController::class, 'updatePayment'])->name('partstocks.updatePayment');

        Route::resource('investors', InvestorController::class);
        Route::post('investors/{investor}/add-investment-history', [InvestorController::class, 'addInvestmentHistory'])->name('investors.addInvestmentHistory');

        Route::resource('investment-histories', InvestmentHistoryController::class);
        Route::resource('product-sales', ProductSaleController::class);
        Route::resource('partstock-sales', PartstockSaleController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Manager Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('checkRole:manager')->prefix('manager')->name('manager.')->group(function () {
        Route::get('dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');
        Route::resource('customers', CustomerController::class);
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/update-payment', [ProductController::class, 'updatePayment'])->name('products.updatePayment');
        Route::resource('stocks', StockController::class);
        Route::resource('partstocks', PartStockController::class);
        Route::post('partstocks/{partStock}/update-payment', [PartStockController::class, 'updatePayment'])->name('partstocks.updatePayment');
        Route::resource('investors', InvestorController::class);
        Route::post('investors/{investor}/add-investment-history', [InvestorController::class, 'addInvestmentHistory'])->name('investors.addInvestmentHistory');
        Route::resource('product-sales', ProductSaleController::class);
        Route::resource('partstock-sales', PartstockSaleController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Worker Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('checkRole:worker')->prefix('worker')->name('worker.')->group(function () {
        Route::get('dashboard', [WorkerController::class, 'dashboard'])->name('dashboard');
        Route::resource('customers', CustomerController::class);
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/update-payment', [ProductController::class, 'updatePayment'])->name('products.updatePayment');
        Route::resource('stocks', StockController::class);
        Route::resource('partstocks', PartStockController::class);
        Route::post('partstocks/{partStock}/update-payment', [PartStockController::class, 'updatePayment'])->name('partstocks.updatePayment');
        Route::resource('product-sales', ProductSaleController::class);
        Route::resource('partstock-sales', PartstockSaleController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Common for Admin, Manager, Worker
    |--------------------------------------------------------------------------
    */
});

/*
|--------------------------------------------------------------------------
| Temporary Admin Registration (Optional)
|--------------------------------------------------------------------------
*/
// Route::get('/register-admin', [RegisterAdminController::class, 'showForm'])->name('register-admin');
// Route::post('/register-admin', [RegisterAdminController::class, 'store'])->name('register-admin.store');
