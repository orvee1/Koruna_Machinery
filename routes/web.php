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
Route::middleware(['auth', 'checkRole:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    /*
    |-------------------------------------------------------------------------- 
    | Branch Selector (Admin-only)
    |-------------------------------------------------------------------------- 
    */
    Route::get('select-branch', [BranchSelectorController::class, 'show'])->name('select-branch');
    Route::post('select-branch', [BranchSelectorController::class, 'set'])->name('select-branch.set');

    /*
    |-------------------------------------------------------------------------- 
    | Branch & User Management
    |-------------------------------------------------------------------------- 
    */
    Route::resource('branches', BranchController::class);
    Route::resource('users', AdminController::class)->only([
        'index', 'create', 'store', 'show', 'edit', 'update'
    ]);

    /*
    |-------------------------------------------------------------------------- 
    | Customer Management
    |-------------------------------------------------------------------------- 
    */
    Route::resource('customers', CustomerController::class);

    /*
    |-------------------------------------------------------------------------- 
    | Inventory: Products, Stocks, Part Stocks
    |-------------------------------------------------------------------------- 
    */
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/update-payment', [ProductController::class, 'updatePayment'])->name('products.updatePayment');

    Route::resource('stocks', StockController::class);

    Route::resource('partstocks', PartStockController::class);
    Route::post('partstocks/{partStock}/update-payment', [PartStockController::class, 'updatePayment'])->name('partstocks.updatePayment');

    /*
    |-------------------------------------------------------------------------- 
    | Sales Management
    |-------------------------------------------------------------------------- 
    */
    Route::resource('product-sales', ProductSaleController::class);
    Route::resource('partstock-sales', PartstockSaleController::class);

    /*
    |-------------------------------------------------------------------------- 
    | Investment Management
    |-------------------------------------------------------------------------- 
    */
    Route::resource('investors', InvestorController::class);
    Route::post('investors/{investor}/add-investment-history', [InvestorController::class, 'addInvestmentHistory'])
        ->name('investors.addInvestmentHistory');

    Route::resource('investment-histories', InvestmentHistoryController::class);
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

/*
|--------------------------------------------------------------------------
| Temporary Admin Registration (Optional)
|--------------------------------------------------------------------------
*/
// Route::get('/register-admin', [RegisterAdminController::class, 'showForm'])->name('register-admin');
// Route::post('/register-admin', [RegisterAdminController::class, 'store'])->name('register-admin.store');
