<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterAdminController;
use App\Http\Controllers\BillController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminController as AdminAdminController;
use App\Http\Controllers\Admin\BranchController as AdminBranchController;
use App\Http\Controllers\Admin\BranchSelectorController as AdminBranchSelectorController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\InvestorController as AdminInvestorController;
use App\Http\Controllers\Admin\InvestmentHistoryController as AdminInvestmentHistoryController;
use App\Http\Controllers\Admin\ProductSaleController as AdminProductSaleController;
use App\Http\Controllers\Admin\StockController as AdminStockController;
use App\Http\Controllers\Admin\PartStockController as AdminPartStockController;
use App\Http\Controllers\Admin\PartStockSaleController as AdminPartStockSaleController;
use App\Http\Controllers\Admin\ProductListController as AdminProductListController;
use App\Http\Controllers\Admin\UnifiedSaleController;
// use App\Http\Controllers\Admin\ProductListController;
// Manager Controllers
use App\Http\Controllers\Manager\ManagerController as ManagerManagerController;
use App\Http\Controllers\Manager\CustomerController as ManagerCustomerController;
use App\Http\Controllers\Manager\InvestorController as ManagerInvestorController;
use App\Http\Controllers\Manager\ManagerUnifiedSaleController;
use App\Http\Controllers\Manager\ProductSaleController as ManagerProductSaleController;
use App\Http\Controllers\Manager\StockController as ManagerStockController;
use App\Http\Controllers\Manager\PartStockController as ManagerPartStockController;
use App\Http\Controllers\Manager\PartStockSaleController as ManagerPartStockSaleController;
use App\Http\Controllers\Manager\ProductListController as ManagerProductListController;
// Worker Controllers
use App\Http\Controllers\Worker\WorkerController as WorkerWorkerController;
use App\Http\Controllers\Worker\CustomerController as WorkerCustomerController;
use App\Http\Controllers\Worker\ProductController as WorkerProductController;
use App\Http\Controllers\Worker\ProductSaleController as WorkerProductSaleController;
use App\Http\Controllers\Worker\StockController as WorkerStockController;
use App\Http\Controllers\Worker\PartStockController as WorkerPartStockController;
use App\Http\Controllers\Worker\PartStockSaleController as WorkerPartStockSaleController;
use App\Http\Controllers\Worker\ProductListController as WorkerProductListController;
use App\Http\Controllers\Worker\WorkerUnifiedSaleController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Registration for Admin
Route::get('/register-admin', [RegisterAdminController::class, 'showForm'])->name('register-admin');
Route::post('/register-admin', [RegisterAdminController::class, 'store'])->name('register-admin.store');

// Authentication
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::prefix('bills')->middleware('auth')->group(function () {
    Route::get('/products', [BillController::class, 'getProducts']);
    Route::get('/customers', [BillController::class, 'getCustomers']);
    Route::post('/', [BillController::class, 'store'])->name('bills.store');
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Branch Selection for Admins
    Route::get('/admin/select-branch', [AdminBranchSelectorController::class, 'show'])->name('admin.select-branch');
    Route::post('/admin/select-branch', [AdminBranchSelectorController::class, 'set'])->name('admin.select-branch.set');
    Route::get('/admin/switch-branch', [AdminBranchSelectorController::class, 'switchBranch'])->name('admin.switch-branch');
    Route::post('/admin/switch-branch', [AdminBranchSelectorController::class, 'set'])->name('admin.switch-branch.set');
   


    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')
        ->middleware(['checkRole:admin', 'ensureAdminBranchSelected'])
        ->name('admin.')
        ->group(function () {
            // Dashboard
            Route::get('dashboard', [AdminAdminController::class, 'dashboard'])->name('dashboard');

            // Branch & User Management
            Route::resource('branches', AdminBranchController::class);
            // Assuming a dedicated UserController exists; adjust if needed.
            Route::resource('users', AdminAdminController::class);
            Route::get('admin/users/{user}', [AdminAdminController::class, 'show'])->name('admin.users.show');


            // Customer Management
            Route::resource('customers', AdminCustomerController::class);

            Route::get('products', [AdminProductListController::class, 'index'])->name('products.index');
            Route::get('sales',[UnifiedSaleController::class, 'index'])->name('sales.index');
            Route::get('sales/{bill}', [UnifiedSaleController::class, 'show'])->name('sales.show');
            Route::delete('sales/{bill}', [UnifiedSaleController::class, 'destroy'])->name('sales.destroy');
            Route::post('/sales/{bill}/update-payment', [UnifiedSaleController::class, 'updatePayment'])->name('sales.updatePayment');
            Route::get('/sales/{bill}/print', [UnifiedSaleController::class, 'print'])->name('sales.print');


            // Inventory: Stocks
            Route::resource('stocks', AdminStockController::class);
            Route::post('stocks/{stock}/update-payment', [AdminStockController::class, 'updatePayment'])
                ->name('stocks.updatePayment');

            // Inventory: Part Stocks
            // Route::resource('partstocks', AdminPartStockController::class);
            Route::get('partstocks', [AdminPartStockController::class, 'index'])->name('partstocks.index');
            Route::get('partstocks/create', [AdminPartStockController::class, 'create'])->name('partstocks.create');
            Route::post('partstocks', [AdminPartStockController::class, 'store'])->name('partstocks.store');
            Route::get('partstocks/{partStock}', [AdminPartStockController::class, 'show'])->name('partstocks.show');
            Route::get('partstocks/{partStock}/edit', [AdminPartStockController::class, 'edit'])->name('partstocks.edit');
            Route::put('partstocks/{partStock}', [AdminPartStockController::class, 'update'])->name('partstocks.update');
            Route::post('partstocks/{partStock}/update-payment', [AdminPartStockController::class, 'updatePayment'])
                ->name('partstocks.updatePayment');

            // Investment Management
            Route::resource('investors', AdminInvestorController::class);
            Route::resource('investment-histories', AdminInvestmentHistoryController::class);
        });

    /*
    |--------------------------------------------------------------------------
    | Manager Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('manager')
        ->middleware('checkRole:manager')
        ->name('manager.')
        ->group(function () {
            Route::get('dashboard', [ManagerManagerController::class, 'dashboard'])->name('dashboard');

            // Branch Inventory
            Route::resource('products', ManagerProductListController::class);

            Route::resource('stocks', ManagerStockController::class);
            Route::post('stocks/{stock}/update-payment', [ManagerStockController::class, 'updatePayment'])
                ->name('stocks.updatePayment');

            Route::resource('partstocks', ManagerPartStockController::class);
            Route::post('partstocks/{partstock}/update-payment', [ManagerPartStockController::class, 'updatePayment'])
                ->name('partstocks.updatePayment');

            // Sales Management
            // Route::resource('product-sales', ManagerProductSaleController::class);
            // Route::resource('partstock-sales', ManagerPartStockSaleController::class);

            // Customer & Investor Management
            Route::resource('customers', ManagerCustomerController::class);
            Route::get('products', [ManagerProductListController::class, 'index'])->name('products.index');
            Route::get('sales',[ManagerUnifiedSaleController::class, 'index'])->name('sales.index');
            Route::get('sales/{bill}', [ManagerUnifiedSaleController::class, 'show'])->name('sales.show');
            Route::delete('sales/{bill}', [ManagerUnifiedSaleController::class, 'destroy'])->name('sales.destroy');
            Route::post('/sales/{bill}/update-payment', [ManagerUnifiedSaleController::class, 'updatePayment'])->name('sales.updatePayment');
            Route::get('/sales/{bill}/print', [ManagerUnifiedSaleController::class, 'print'])->name('sales.print');
            Route::resource('investors', ManagerInvestorController::class);
        });

    /*
    |--------------------------------------------------------------------------
    | Worker Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('worker')
        ->middleware('checkRole:worker')
        ->name('worker.')
        ->group(function () {
            // Route::get('dashboard', [WorkerWorkerController::class, 'dashboard'])->name('dashboard');

            // Branch Inventory
            Route::resource('products', WorkerProductListController::class);

            Route::resource('stocks', WorkerStockController::class);
             Route::post('stocks/{stock}/update-payment', [WorkerStockController::class, 'updatePayment'])
                ->name('stocks.updatePayment');

            Route::resource('partstocks', WorkerPartStockController::class);
            Route::post('partstocks/{partstock}/update-payment', [WorkerPartStockController::class, 'updatePayment'])
                ->name('partstocks.updatePayment');

            // Sales Management
            // Route::resource('product-sales', WorkerProductSaleController::class);
            // Route::resource('partstock-sales', WorkerPartStockSaleController::class);

            // Customer Management
            Route::resource('customers', WorkerCustomerController::class);
            Route::get('products', [WorkerProductListController::class, 'index'])->name('products.index');
            Route::get('sales',[WorkerUnifiedSaleController::class, 'index'])->name('sales.index');
            Route::get('sales/{bill}', [WorkerUnifiedSaleController::class, 'show'])->name('sales.show');
            Route::delete('sales/{bill}', [WorkerUnifiedSaleController::class, 'destroy'])->name('sales.destroy');
            Route::post('/sales/{bill}/update-payment', [WorkerUnifiedSaleController::class, 'updatePayment'])->name('sales.updatePayment');
            Route::get('/sales/{bill}/print', [WorkerUnifiedSaleController::class, 'print'])->name('sales.print');


        });
});
