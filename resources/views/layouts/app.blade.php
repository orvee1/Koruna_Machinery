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
        <div>Koruna Machinery  <strong>({{ session('active_branch_name') ?? 'None' }})</strong></div>
        <div class="d-flex align-items-center gap-2">
            @if(auth()->check())
            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#createBillModal">
            ➕ Create Bill
            </button>
            @endif
            @if(auth()->check() && auth()->user()->role === 'admin')
                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#switchBranchModal">
                    🔁 Switch Branch
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
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-link {{ request()->routeIs('admin.dashboard') ? 'text-primary fw-bold' : '' }}">
                   Dashboard
                </a>
            
                <span class="fw-bold text-muted mt-3">User & Branch</span>
                <a href="{{ route('admin.branches.index') }}"
                   class="nav-link {{ request()->routeIs('admin.branches.index') ? 'text-primary fw-bold' : '' }}">
                   Branches
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="nav-link {{ request()->routeIs('admin.users.index') ? 'text-primary fw-bold' : '' }}">
                   Users
                </a>
                <a href="{{ route('admin.customers.index') }}"
                   class="nav-link {{ request()->routeIs('admin.customers.index') ? 'text-primary fw-bold' : '' }}">
                   Customers
                </a>
            
                <span class="fw-bold text-muted mt-3">Inventory</span>
                <a href="{{ route('admin.products.index') }}"
                   class="nav-link {{ request()->routeIs('admin.products.index') ? 'text-primary fw-bold' : '' }}">
                   Products
                </a>
                <a href="{{ route('admin.stocks.index') }}"
                   class="nav-link {{ request()->routeIs('admin.stocks.index') ? 'text-primary fw-bold' : '' }}">
                   Stocks
                </a>
                <a href="{{ route('admin.partstocks.index') }}"
                   class="nav-link {{ request()->routeIs('admin.partstocks.index') ? 'text-primary fw-bold' : '' }}">
                   Part Stocks
                </a>
            
                <span class="fw-bold text-muted mt-3">Sales</span>
                <a href="{{ route('admin.product-sales.index') }}"
                   class="nav-link {{ request()->routeIs('admin.product-sales.index') ? 'text-primary fw-bold' : '' }}">
                   Product Sales
                </a>
                <a href="{{ route('admin.partstock-sales.index') }}"
                   class="nav-link {{ request()->routeIs('admin.partstock-sales.index') ? 'text-primary fw-bold' : '' }}">
                   Part Stock Sales
                </a>
            
                <span class="fw-bold text-muted mt-3">Investors</span>
                <a href="{{ route('admin.investors.index') }}"
                   class="nav-link {{ request()->routeIs('admin.investors.index') ? 'text-primary fw-bold' : '' }}">
                   Investor List
                </a>
                <a href="{{ route('admin.investment-histories.index') }}"
                   class="nav-link {{ request()->routeIs('admin.investment-histories.index') ? 'text-primary fw-bold' : '' }}">
                   Investment Histories
                </a>
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

    <!-- Toast Notifications -->
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

<!-- Create Bill Modal -->
<div class="modal fade" id="createBillModal" tabindex="-1" aria-labelledby="createBillModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('bills.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Create Bill</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <!-- Customer Info -->
                    <div class="mb-3 position-relative">
                        <label>Customer Name</label>
                        <input type="text" id="customerNameInput" name="customer_name" class="form-control" autocomplete="off" required>
                        <input type="hidden" name="customer_id" id="customerId">
                        <div id="customerSuggestions" class="list-group position-absolute w-100" style="z-index: 999;"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label>Phone</label>
                            <input type="text" name="phone" id="phoneInput" class="form-control">
                        </div>
                        <div class="col">
                            <label>District</label>
                            <input type="text" name="district" id="districtInput" class="form-control">
                        </div>
                    </div>

                    <!-- Product Selection -->
                    <div class="mb-3">
                        <label>Select Product Type</label>
                        <select id="productType" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="product">🛒 Product</option>
                            <option value="partstock">🔩 Part Stock</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Select Products</label>
                        <select name="products[]" id="productSelect" class="form-select" multiple required>
                            <!-- Filled dynamically via JS -->
                        </select>
                    </div>

                    <!-- Product Inputs -->
                    <div id="productDetailsContainer"></div>

                    <!-- Payment Summary -->
                    <div class="mt-3 p-3 border rounded bg-light">
                        <h5>Total Summary</h5>
                        <p>Total Amount: ৳<span id="totalAmount">0</span></p>
                        <p>Previous Due: ৳<span id="previousDue">0</span></p>
                        <p>Total Due: ৳<span id="totalDue">0</span></p>
                    </div>

                    <!-- Payment -->
                    <div class="mb-3 mt-2">
                        <label>Paid Amount</label>
                        <input type="number" name="paid_amount" step="0.01" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Create</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Toasts
    document.querySelectorAll('.toast').forEach(el => new bootstrap.Toast(el, { delay: 3000 }).show());

    const customerInput = document.getElementById('customerNameInput');
    const suggestionBox = document.getElementById('customerSuggestions');
    const customerIdInput = document.getElementById('customerId');
    const phoneInput = document.getElementById('phoneInput');
    const districtInput = document.getElementById('districtInput');

    const productType = document.getElementById('productType');
    const productSelect = document.getElementById('productSelect');
    const container = document.getElementById('productDetailsContainer');

    const totalAmount = document.getElementById('totalAmount');
    const previousDue = document.getElementById('previousDue');
    const totalDue = document.getElementById('totalDue');
    const paidInput = document.querySelector('[name="paid_amount"]');

    // 🔍 Autocomplete Customer
    customerInput.addEventListener('input', function () {
        const query = this.value;
        if (query.length < 2) return;
        fetch(`/admin/customers/search?name=${query}`)
            .then(res => res.json())
            .then(data => {
                suggestionBox.innerHTML = '';
                data.forEach(c => {
                    const item = document.createElement('button');
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = `${c.name} (${c.phone})`;
                    item.onclick = () => {
                        customerInput.value = c.name;
                        customerIdInput.value = c.id;
                        phoneInput.value = c.phone || '';
                        districtInput.value = c.district || '';
                        previousDue.textContent = c.total_due ?? 0; // optional if added to response
                        suggestionBox.innerHTML = '';
                        calculateTotals();
                    };
                    suggestionBox.appendChild(item);
                });
            });
    });

    // 🔁 Load products by type
    productType.addEventListener('change', function () {
        const type = this.value;
        if (!type) {
            productSelect.innerHTML = '';
            container.innerHTML = '';
            return;
        }

        fetch(`/bills/products?type=${type}`)
            .then(res => res.json())
            .then(data => {
                productSelect.innerHTML = '';
                container.innerHTML = '';
                data.forEach(p => {
                    const option = document.createElement('option');
                    const isOut = p.quantity === 0;
                    option.value = `${type}_${p.id}`;
                    let label = `${p.name} — ${p.quantity} available`;
                    if (type === 'product' && p.buying_price !== undefined) {
                    label += ` — ৳${p.buying_price}`;
                    }
                    if (type === 'partstock' && p.selling_price !== undefined) {
                        label += ` — ৳${p.selling_price}`;
                    }
                    if (isOut) {
                        option.disabled = true;
                        label += ' (Out of Stock)';
                    }
                    option.text = label;
                    productSelect.appendChild(option);
                });
            });
    });

    // 🧮 Generate inputs for selected products
    productSelect.addEventListener('change', function () {
        container.innerHTML = '';
        Array.from(this.selectedOptions).forEach(opt => {
            const [type, id] = opt.value.split('_');
            const name = opt.text;
            const uid = `${type}_${id}`;

            container.innerHTML += `
                <div class="border p-2 mb-2 rounded bg-light">
                    <h6>${name}</h6>
                    <input type="hidden" name="product_details[${uid}][id]" value="${id}">
                    <input type="hidden" name="product_details[${uid}][type]" value="${type}">
                    <div class="mb-2">
                        <label>Quantity</label>
                        <input type="number" name="product_details[${uid}][quantity]" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Selling Price</label>
                        <input type="number" name="product_details[${uid}][price]" class="form-control" step="0.01" required>
                    </div>
                </div>
            `;
        });
        calculateTotals();
    });

    // 🧮 Auto calculate totals
    document.addEventListener('input', function (e) {
        if (
            e.target.name?.includes('[quantity]') ||
            e.target.name?.includes('[price]') ||
            e.target.name === 'paid_amount'
        ) {
            calculateTotals();
        }
    });

    function calculateTotals() {
        let total = 0;
        const previous = parseFloat(previousDue.textContent) || 0;
        const paid = parseFloat(paidInput.value || 0);

        document.querySelectorAll('[name^="product_details"]').forEach(input => {
            if (input.name.includes('[quantity]')) {
                const id = input.name.match(/\[(.*?)\]/)[1];
                const qty = parseFloat(input.value || 0);
                const price = parseFloat(document.querySelector(`[name="product_details[${id}][price]"]`)?.value || 0);
                total += qty * price;
            }
        });

        totalAmount.textContent = total.toFixed(2);
        totalDue.textContent = Math.max(0, total - paid + previous).toFixed(2);
    }
});
</script>



</body>
</html>
