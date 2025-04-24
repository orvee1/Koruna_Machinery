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
                @if(auth()->user()->role === 'admin')
                    <span class="fw-bold text-secondary">User & Branch</span>
                    <a href="{{ route('admin.branches.index') }}" class="nav-link">Branches</a>
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">Users</a>
                    <a href="{{ route('admin.customers.index') }}" class="nav-link">Customers</a>
                @endif

                @if(in_array(auth()->user()->role, ['admin', 'manager']))
                    <span class="fw-bold text-secondary mt-3">Inventory</span>
                    <a href="{{ route('admin.products.index') }}" class="nav-link">Products</a>
                    <a href="{{ route('admin.stocks.index') }}" class="nav-link">Stocks</a>
                    <a href="{{ route('admin.partstocks.index') }}" class="nav-link">Part Stocks</a>
                @endif

                @if(in_array(auth()->user()->role, ['admin', 'manager', 'worker']))
                    <span class="fw-bold text-secondary mt-3">Sales</span>
                    <a href="{{ route('admin.product-sales.index') }}" class="nav-link">Product Sales</a>
                    <a href="{{ route('admin.partstock-sales.index') }}" class="nav-link">Partstock Sales</a>
                @endif

                @if(auth()->user()->role === 'admin')
                    <span class="fw-bold text-secondary mt-3">Investors</span>
                    <a href="{{ route('admin.investors.index') }}" class="nav-link">Investor List</a>
                    <a href="{{ route('admin.investmentHistories.index') }}" class="nav-link">Investment Histories</a>
                    {{-- <a href="#" class="nav-link">Deposit Histories</a> --}} <!-- handled via investor details -->
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
