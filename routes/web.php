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
    RegisterAdminController
};

/*
|--------------------------------------------------------------------------
| Authentication Routes (Public)
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
| Authenticated User Routes (Must be logged in)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Branch Selection for Admins
    Route::get('/admin/select-branch', [BranchSelectorController::class, 'show'])->name('admin.select-branch');
    Route::post('/admin/select-branch', [BranchSelectorController::class, 'set'])->name('admin.select-branch.set');

    // Common Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (Admin role only)
    |--------------------------------------------------------------------------
    */
    Route::middleware('checkRole:admin')->prefix('admin')->name('admin.')->group(function () {

        // Admin Dashboard
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Branches
        Route::resource('branches', BranchController::class);

        // Users Management
        Route::get('users', [AdminController::class, 'indexUsers'])->name('users.index');
        Route::get('users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('users/{user}', [AdminController::class, 'showUser'])->name('users.show');
        Route::get('users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('users/{user}', [AdminController::class, 'updateUser'])->name('users.update');

        // Customers
        Route::resource('customers', CustomerController::class);

        // Products
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/update-payment', [ProductController::class, 'updatePayment'])->name('products.updatePayment');

        // Stocks
        Route::resource('stocks', StockController::class);

        // Part Stocks
        Route::resource('partstocks', PartStockController::class);
        Route::post('partstocks/{partStock}/update-payment', [PartStockController::class, 'updatePayment'])->name('partstocks.updatePayment');

        // Investors
        Route::resource('investors', InvestorController::class);
        Route::post('investors/{investor}/add-investment-history', [InvestorController::class, 'addInvestmentHistory'])->name('investors.addInvestmentHistory');

        // Investment Histories
        Route::resource('investment-histories', InvestmentHistoryController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Common Routes (Admin + Manager + Worker)
    |--------------------------------------------------------------------------
    */

    // Product Sales
    Route::resource('product-sales', ProductSaleController::class)->except('show');

    // Part Stock Sales
    Route::resource('partstock-sales', PartstockSaleController::class)->except('show');
});

/*
|--------------------------------------------------------------------------
| Temporary Admin Registration Routes (Commented)
|--------------------------------------------------------------------------
| Only enable during development if necessary!
*/

// Route::get('/register-admin', [RegisterAdminController::class, 'showForm'])->name('register-admin');
// Route::post('/register-admin', [RegisterAdminController::class, 'store'])->name('register-admin.store');
