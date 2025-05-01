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
use App\Http\Controllers\Manager\ProductController as ManagerProductController;
use App\Models\Customer;
use App\Models\Investor;
use App\Models\PartstockSale;
use App\Models\Product;
use App\Models\ProductSale;

/*
|---------------------------------------------------------------------- 
| Public Routes (For login and logout) 
|---------------------------------------------------------------------- 
*/

// Show Login Form
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

// Submit Login
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|---------------------------------------------------------------------- 
| Authenticated Routes (Only for logged-in users) 
|---------------------------------------------------------------------- 
*/
Route::middleware(['auth'])->group(function () {

    // Branch Selection for Admins
    Route::get('/admin/select-branch', [BranchSelectorController::class, 'show'])->name('admin.select-branch');
    Route::post('/admin/select-branch', [BranchSelectorController::class, 'set'])->name('admin.select-branch.set');

    /*
    |---------------------------------------------------------------------- 
    | Admin Routes 
    |---------------------------------------------------------------------- 
    */
    Route::middleware('checkRole:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Branch & User Management
        Route::resource('branches', BranchController::class);
        Route::resource('users', AdminController::class);

        // Customer Management
        Route::resource('customers', CustomerController::class);

        // Inventory: Products, Stocks, Part Stocks
        Route::resource('products', ProductController::class);
        Route::resource('stocks', StockController::class);
        Route::resource('partstocks', PartStockController::class);
        
        // Sales Management
        Route::resource('product-sales', ProductSaleController::class);
        Route::resource('partstock-sales', PartstockSaleController::class);

        // Investment Management
        Route::resource('investors', InvestorController::class);
        Route::resource('investment-histories', InvestmentHistoryController::class);
    });

    /*
    |---------------------------------------------------------------------- 
    | Manager Routes 
    |---------------------------------------------------------------------- 
    */
    Route::middleware('checkRole:manager')->prefix('manager')->name('manager.')->group(function () {
        Route::get('dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');
        
        // Branch Inventory
        Route::resource('products', ManagerProductController::class);
        Route::post('products/{product}/update-payment', [ManagerProductController::class, 'updatePayment'])->name('products.updatePayment');

        // Route::get('products', [ManagerProductController::class, 'index'])->name('products.index');
        // Route::get('products/create', [ManagerProductController::class, 'create'])->name('products.create');
        // Route::post('products', [ManagerProductController::class, 'store'])->name('products.store');
        // Route::get('products/{product}/edit', [ManagerProductController::class, 'edit'])->name('products.edit');
        // Route::put('products/{product}', [ManagerProductController::class, 'update'])->name('products.update');

        Route::resource('stocks', StockController::class);
        Route::resource('partstocks', PartStockController::class);

        // Sales Management
        Route::resource('product-sales', ProductSaleController::class);
        Route::resource('partstock-sales', PartstockSaleController::class);

        // Customer & Investor Management
        Route::resource('customers', CustomerController::class);
        Route::resource('investors', InvestorController::class);
    });

    /*
    |---------------------------------------------------------------------- 
    | Worker Routes 
    |---------------------------------------------------------------------- 
    */
    Route::middleware('checkRole:worker')->prefix('worker')->name('worker.')->group(function () {
        Route::get('dashboard', [WorkerController::class, 'dashboard'])->name('dashboard');
        
        // Branch Inventory
        Route::resource('products', ProductController::class);
        Route::resource('stocks', StockController::class);
        Route::resource('partstocks', PartStockController::class);

        // Sales Management
        Route::resource('product-sales', ProductSaleController::class);
        Route::resource('partstock-sales', PartstockSaleController::class);

        // Customer Management
        Route::resource('customers', CustomerController::class);
    });

    /*
    |---------------------------------------------------------------------- 
    | Common Routes (For Admin, Manager, and Worker) 
    |---------------------------------------------------------------------- 
    */
    // Can be accessed by all roles: Admin, Manager, Worker
    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    // Other routes for Admin, Manager, and Worker here
});
