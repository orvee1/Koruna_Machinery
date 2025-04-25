<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #fff;
            border-right: 1px solid #dee2e6;
        }
        .sidebar h4 {
            font-size: 18px;
            padding: 15px;
            margin-bottom: 0;
        }
        .sidebar .nav-link {
            padding: 10px 20px;
            color: #333;
        }
        .sidebar .nav-link:hover {
            background-color: #e9f5ff;
        }
        .main-content {
            padding: 20px;
        }
        .dashboard-card {
            background: #cce5ff;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            font-weight: 500;
            transition: 0.2s;
        }
        .dashboard-card:hover {
            background-color: #99d0ff;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-2">
            <h4>Dashboard</h4>
            <nav class="nav flex-column">
                {{-- Admin Menu (No branch restriction) --}}
                @if(auth()->user()->role === 'admin')
                    <span class="fw-bold text-secondary">User & Branch</span>
                    <a href="{{ route('admin.branches.index') }}" class="nav-link">Branches</a>
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">Users</a>
                    <a href="{{ route('admin.customers.index') }}" class="nav-link">Customers</a>
            
                    <span class="fw-bold text-secondary mt-3">Inventory</span>
                    <a href="{{ route('admin.products.index') }}" class="nav-link">Products</a>
                    <a href="{{ route('admin.stocks.index') }}" class="nav-link">Stocks</a>
                    <a href="{{ route('admin.partstocks.index') }}" class="nav-link">Part Stocks</a>
            
                    <span class="fw-bold text-secondary mt-3">Sales</span>
                    <a href="{{ route('product-sales.index') }}" class="nav-link">Product Sales</a>
                    <a href="{{ route('partstock-sales.index') }}" class="nav-link">Part Stock Sales</a>
            
                    <span class="fw-bold text-secondary mt-3">Investors</span>
                    <a href="{{ route('admin.investors.index') }}" class="nav-link">Investor List</a>
                    <a href="{{ route('admin.investmentHistories.index') }}" class="nav-link">Investment Histories</a>
                @endif
            
                {{-- Manager Menu (Branch-bound Access) --}}
                @if(auth()->user()->role === 'manager')
                    <span class="fw-bold text-secondary mt-3">Branch Inventory</span>
                    <a href="{{ route('admin.products.index') }}" class="nav-link">Products</a>
                    <a href="{{ route('admin.stocks.index') }}" class="nav-link">Stocks</a>
                    <a href="{{ route('admin.partstocks.index') }}" class="nav-link">Part Stocks</a>
            
                    <span class="fw-bold text-secondary mt-3">Branch Sales</span>
                    <a href="{{ route('product-sales.index') }}" class="nav-link">Product Sales</a>
                    <a href="{{ route('partstock-sales.index') }}" class="nav-link">Part Stock Sales</a>
            
                    <span class="fw-bold text-secondary mt-3">Branch Customers</span>
                    <a href="{{ route('admin.customers.index') }}" class="nav-link">Customers</a>
            
                    <span class="fw-bold text-secondary mt-3">Branch Investors</span>
                    <a href="{{ route('admin.investors.index') }}" class="nav-link">Investor List</a>
                @endif
            
                {{-- Worker Menu (Branch-bound, Limited Access) --}}
                @if(auth()->user()->role === 'worker')
                    <span class="fw-bold text-secondary mt-3">Branch Work Access</span>
                    <a href="{{ route('admin.products.index') }}" class="nav-link">Products</a>
                    <a href="{{ route('admin.stocks.index') }}" class="nav-link">Stocks</a>
                    <a href="{{ route('admin.partstocks.index') }}" class="nav-link">Part Stocks</a>
                    <a href="{{ route('admin.customers.index') }}" class="nav-link">Customers</a>
                    <a href="{{ route('product-sales.index') }}" class="nav-link">Product Sales</a>
                    <a href="{{ route('partstock-sales.index') }}" class="nav-link">Part Stock Sales</a>
                @endif
            </nav>
            
        </div>

        <!-- Main Content -->
        <div class="flex-fill main-content">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
