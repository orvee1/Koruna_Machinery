<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koruna Machinery — @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .topbar {
            background-color: #004080;
            color: #fff;
            padding: 12px 20px;
            font-weight: 600;
            font-size: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
                .sidebar {
            width: 250px;
            background-color: #fff;
            border-right: 1px solid #dee2e6;
            position: fixed;
            top: 56px; /* Topbar height */
            bottom: 0;
            left: 0;
            overflow-y: auto;
            padding-bottom: 20px;
        }

        .sidebar .nav-link {
            padding: 10px 20px;
            color: #004080;
            background-color: #e8f0fe;
            margin-bottom: 5px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: linear-gradient(to right, #d0e9ff, #b3e5fc);
            color: #002f5e;
            transform: translateX(4px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
        }
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px 20px;
            padding-top: 80px;
        }
        .dashboard-card {
            background: #e1f5fe;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .dashboard-card:hover { background-color: #b3e5fc; }
    </style>
</head>
<body>
    <!-- Topbar -->
    <div class="topbar fixed-top shadow-sm">
        <div>Koruna Machinery — Empowering Industry</div>
        <div class="d-flex align-items-center gap-2">
            @if(auth()->check() && auth()->user()->role === 'admin')
                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#switchBranchModal">
                    🔁 Switch Branch ({{ session('selected_branch_name') ?? 'None' }})
                </button>
            @endif
            @if(auth()->check())
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
                </form>
            @endif
        </div>
    </div>

    <div class="d-flex">
        @if(auth()->check())
        <!-- Sidebar -->
        <div class="sidebar p-3">
            <nav class="nav flex-column">
                @if(auth()->user()->role === 'admin')
                    <h5 class="text-secondary">Admin Panel</h5>
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
                    <span class="fw-bold text-muted mt-3">User & Branch</span>
                    <a href="{{ route('admin.branches.index') }}" class="nav-link">Branches</a>
                    <a href="{{ route('admin.users.index') }}" class="nav-link">Users</a>
                    <a href="{{ route('admin.customers.index') }}" class="nav-link">Customers</a>
                    <span class="fw-bold text-muted mt-3">Inventory</span>
                    <a href="{{ route('admin.products.index') }}" class="nav-link">Products</a>
                    <a href="{{ route('admin.stocks.index') }}" class="nav-link">Stocks</a>
                    <a href="{{ route('admin.partstocks.index') }}" class="nav-link">Part Stocks</a>
                    <span class="fw-bold text-muted mt-3">Sales</span>
                    <a href="{{ route('admin.product-sales.index') }}" class="nav-link">Product Sales</a>
                    <a href="{{ route('admin.partstock-sales.index') }}" class="nav-link">Part Stock Sales</a>
                    <span class="fw-bold text-muted mt-3">Investors</span>
                    <a href="{{ route('admin.investors.index') }}" class="nav-link">Investor List</a>
                    <a href="{{ route('admin.investment-histories.index') }}" class="nav-link">Investment Histories</a>
                @endif
                @if(auth()->user()->role === 'manager')
                    <h5 class="text-secondary">Manager Panel</h5>
                    <a href="{{ route('manager.dashboard') }}" class="nav-link">Dashboard</a>
                    <span class="fw-bold text-muted mt-3">Inventory</span>
                    <a href="{{ route('manager.products.index') }}" class="nav-link">Products</a>
                    <a href="{{ route('manager.stocks.index') }}" class="nav-link">Stocks</a>
                    <a href="{{ route('manager.partstocks.index') }}" class="nav-link">Part Stocks</a>
                    <span class="fw-bold text-muted mt-3">Sales</span>
                    <a href="{{ route('manager.product-sales.index') }}" class="nav-link">Product Sales</a>
                    <a href="{{ route('manager.partstock-sales.index') }}" class="nav-link">Part Stock Sales</a>
                    <span class="fw-bold text-muted mt-3">Customers & Investors</span>
                    <a href="{{ route('manager.customers.index') }}" class="nav-link">Customers</a>
                    <a href="{{ route('manager.investors.index') }}" class="nav-link">Investors</a>
                @endif
                @if(auth()->user()->role === 'worker')
                    <h5 class="text-secondary">Worker Panel</h5>
                    <a href="{{ route('worker.dashboard') }}" class="nav-link">Dashboard</a>
                    <span class="fw-bold text-muted mt-3">Access</span>
                    <a href="{{ route('worker.products.index') }}" class="nav-link">Products</a>
                    <a href="{{ route('worker.stocks.index') }}" class="nav-link">Stocks</a>
                    <a href="{{ route('worker.partstocks.index') }}" class="nav-link">Part Stocks</a>
                    <a href="{{ route('worker.customers.index') }}" class="nav-link">Customers</a>
                    <a href="{{ route('worker.product-sales.index') }}" class="nav-link">Product Sales</a>
                    <a href="{{ route('worker.partstock-sales.index') }}" class="nav-link">Part Stock Sales</a>
                @endif
            </nav>
        </div>
        @endif

        <!-- Main Content -->
        <div class="main-content flex-fill">
            @yield('content')
        </div>
    </div>

    <!-- Modal: Switch Branch (Only for Admin) -->
    @if(auth()->check() && auth()->user()->role === 'admin')
        <div class="modal fade" id="switchBranchModal" tabindex="-1" aria-labelledby="switchBranchModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.switch-branch.set') }}">
              @csrf
              <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                  <h5 class="modal-title" id="switchBranchModalLabel">Switch Branch</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                    <label for="branch_id" class="form-label">Select Branch</label>
                    <select name="branch_id" id="branch_id" class="form-select" required>
                        @foreach(\App\Models\Branch::all() as $branch)
                            <option value="{{ $branch->id }}" {{ session('active_branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-success">Switch</button>
                </div>
              </div>
            </form>
          </div>
        </div>
    @endif

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
