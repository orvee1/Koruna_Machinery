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
        @if(auth()->check())
        <!-- Sidebar -->
        <div class="sidebar p-2 d-flex flex-column justify-content-between">
            <div>
                {{-- <h5><a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a></h5> --}}
                <nav class="nav flex-column">
                    {{-- Admin Menu --}}
                    @if(auth()->user()->role === 'admin')
                        <h5><a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a></h5>

                        <span class="fw-bold text-secondary">User & Branch</span>
                        <a href="{{ route('admin.branches.index') }}" class="nav-link">Branches</a>
                        <a href="{{ route('admin.users.index') }}" class="nav-link">Users</a>
                        <a href="{{ route('admin.customers.index') }}" class="nav-link">Customers</a>

                        <span class="fw-bold text-secondary mt-3">Inventory</span>
                        <a href="{{ route('admin.products.index') }}" class="nav-link">Products</a>
                        <a href="{{ route('admin.stocks.index') }}" class="nav-link">Stocks</a>
                        <a href="{{ route('admin.partstocks.index') }}" class="nav-link">Part Stocks</a>

                        <span class="fw-bold text-secondary mt-3">Sales</span>
                        <a href="{{ route('admin.product-sales.index') }}" class="nav-link">Product Sales</a>
                        <a href="{{ route('admin.partstock-sales.index') }}" class="nav-link">Part Stock Sales</a>

                        <span class="fw-bold text-secondary mt-3">Investors</span>
                        <a href="{{ route('admin.investors.index') }}" class="nav-link">Investor List</a>
                        <a href="{{ route('admin.investment-histories.index') }}" class="nav-link">Investment Histories</a>
                    @endif

                    {{-- Manager Menu --}}
                    @if(auth()->user()->role === 'manager')
                        <h5><a href="{{ route('manager.dashboard') }}" class="nav-link">Dashboard</a></h5>
                        <span class="fw-bold text-secondary mt-3">Branch Inventory</span>
                        <a href="{{ route('manager.products.index') }}" class="nav-link">Products</a>
                        <a href="{{ route('manager.stocks.index') }}" class="nav-link">Stocks</a>
                        <a href="{{ route('manager.partstocks.index') }}" class="nav-link">Part Stocks</a>

                        <span class="fw-bold text-secondary mt-3">Branch Sales</span>
                        <a href="{{ route('manager.product-sales.index') }}" class="nav-link">Product Sales</a>
                        <a href="{{ route('manager.partstock-sales.index') }}" class="nav-link">Part Stock Sales</a>

                        <span class="fw-bold text-secondary mt-3">Branch Customers</span>
                        <a href="{{ route('manager.customers.index') }}" class="nav-link">Customers</a>

                        <span class="fw-bold text-secondary mt-3">Branch Investors</span>
                        <a href="{{ route('manager.investors.index') }}" class="nav-link">Investor List</a>
                    @endif

                    {{-- Worker Menu --}}
                    @if(auth()->user()->role === 'worker')
                        <h5><a href="{{ route('worker.dashboard') }}" class="nav-link">Dashboard</a></h5>
                        <span class="fw-bold text-secondary mt-3">Work Access</span>
                        <a href="{{ route('worker.products.index') }}" class="nav-link">Products</a>
                        <a href="{{ route('worker.stocks.index') }}" class="nav-link">Stocks</a>
                        <a href="{{ route('worker.partstocks.index') }}" class="nav-link">Part Stocks</a>
                        <a href="{{ route('worker.customers.index') }}" class="nav-link">Customers</a>
                        <a href="{{ route('worker.product-sales.index') }}" class="nav-link">Product Sales</a>
                        <a href="{{ route('worker.partstock-sales.index') }}" class="nav-link">Part Stock Sales</a>
                    @endif
                </nav>
            </div>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-danger w-100">Logout</button>
            </form>
        </div>
        @endif

        <!-- Main Content -->
        <div class="flex-fill main-content">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Toast Messages -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        @if(session('status'))
        <div class="toast align-items-center text-white bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('status') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'))
            var toastList = toastElList.map(function (toastEl) {
                return new bootstrap.Toast(toastEl, { delay: 3000 });
            });
            toastList.forEach(toast => toast.show());
        });
    </script>

</body>
</html>
