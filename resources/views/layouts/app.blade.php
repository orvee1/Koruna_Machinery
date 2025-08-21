<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koruna Machinery — @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

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
            top: 56px;
            /* Topbar height */
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: linear-gradient(to right, #d0e9ff, #b3e5fc);
            color: #002f5e;
            transform: translateX(4px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

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
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .dashboard-card:hover {
            background-color: #b3e5fc;
        }
    </style>
</head>

<body>
    <!-- Topbar -->
    <div class="topbar fixed-top shadow-sm">
        <div>Koruna Machinery <strong>({{ session('active_branch_name') ?? 'None' }})</strong></div>
        <div class="d-flex align-items-center gap-2">
            @if (auth()->check())
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#createBillModal">
                    ➕ Create Bill
                </button>
            @endif
            @if (auth()->check() && auth()->user()->role === 'admin')
                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#switchBranchModal">
                    🔁 Switch Branch
                </button>
            @endif
            @if (auth()->check())
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
                </form>
            @endif
        </div>
    </div>

    <div class="d-flex">
        @if (auth()->check())
            <!-- Sidebar -->
            <div class="sidebar p-3">
                <nav class="nav flex-column">
                    @if (auth()->user()->role === 'admin')
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
                        <a href="{{ route('admin.sales.index') }}"
                        class="nav-link {{ request()->routeIs('admin.sales.index') ? 'text-primary fw-bold' : '' }}">
                        📑 All Sales Panel
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

                    @if (auth()->user()->role === 'manager')
                        <h5 class="text-secondary">Manager Panel</h5>
                        <a href="{{ route('manager.dashboard') }}" class="nav-link">Dashboard</a>
                        <span class="fw-bold text-muted mt-3">Inventory</span>
                        <a href="{{ route('manager.products.index') }}" class="nav-link">Products</a>
                        <a href="{{ route('manager.stocks.index') }}" class="nav-link">Stocks</a>
                        <a href="{{ route('manager.partstocks.index') }}" class="nav-link">Part Stocks</a>
                        <span class="fw-bold text-muted mt-3">Sales</span>
                        <a href="{{ route('manager.sales.index') }}"
                        class="nav-link {{ request()->routeIs('manager.sales.index') ? 'text-primary fw-bold' : '' }}">
                        📑 All Sales Panel
                        </a>
                        <span class="fw-bold text-muted mt-3">Customers & Investors</span>
                        <a href="{{ route('manager.customers.index') }}" class="nav-link">Customers</a>
                        <a href="{{ route('manager.investors.index') }}" class="nav-link">Investors</a>
                    @endif

                    @if (auth()->user()->role === 'worker')
                        <h5 class="text-secondary">Worker Panel</h5>
                        {{-- <a href="{{ route('worker.dashboard') }}" class="nav-link">Dashboard</a> --}}
                        <span class="fw-bold text-muted mt-3">Access</span>
                        <a href="{{ route('worker.products.index') }}" class="nav-link">Products</a>
                        <a href="{{ route('worker.stocks.index') }}" class="nav-link">Stocks</a>
                        <a href="{{ route('worker.partstocks.index') }}" class="nav-link">Part Stocks</a>
                        <a href="{{ route('worker.customers.index') }}" class="nav-link">Customers</a>
                        <a href="{{ route('worker.sales.index') }}"
                        class="nav-link {{ request()->routeIs('worker.sales.index') ? 'text-primary fw-bold' : '' }}">
                        📑 All Sales Panel
                        </a>
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
    @if (auth()->check() && auth()->user()->role === 'admin')
        <div class="modal fade" id="switchBranchModal" tabindex="-1" aria-labelledby="switchBranchModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.switch-branch.set') }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="switchBranchModalLabel">Switch Branch</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="branch_id" class="form-label">Select Branch</label>
                                <select name="branch_id" id="branch_id" class="form-select" required>
                                    @foreach (\App\Models\Branch::all() as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ session('active_branch_id') == $branch->id ? 'selected' : '' }}>
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
        @if (session('status'))
            <div class="toast align-items-center text-white bg-success border-0 show" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('status') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>

    <!-- Create Bill Modal -->
   <!-- ✅ MODAL -->
<div class="modal fade" id="createBillModal" tabindex="-1" aria-labelledby="createBillModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="billCreateForm" method="POST" action="{{ route('bills.store') }}">
      @csrf
      <input type="hidden" name="branch_id" value="{{ session('active_branch_id') }}">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Create Bill</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body" style="overflow: visible;">
          @if(session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if($errors->any())
              <div class="alert alert-danger">
                  <ul class="mb-0">
                      @foreach($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
              </div>
          @endif

          <!-- Customer Info -->
          <div class="mb-3 position-relative">
            <label>Customer Name</label>
            <input list="customerList" id="customerNameInput" name="customer_name" class="form-control" autocomplete="off" required>
            <datalist id="customerList"></datalist>
            <input type="hidden" name="customer_id" id="customerId">
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

          <!-- Products -->
          <div class="mb-3">
            <label>Search Product</label>
            <input type="text" id="productSearchInput" class="form-control mb-2" placeholder="Search product name...">
            <div id="productSelect" class="form-group"></div>
          </div>

          <!-- Selected Product Inputs -->
          <div id="productDetailsContainer"></div>

          <!-- Summary -->
          <div class="mt-3 p-3 border rounded bg-light">
            <h5>Total Summary</h5>
            <p>Total Amount: ৳<span id="totalAmount">0</span></p>
            <p>Previous Due: ৳<span id="previousDue">0</span></p>
            <p>Total Due: ৳<span id="totalDue">0</span></p>
          </div>

          <div class="mb-3 mt-2">
            <label>Paid Amount</label>
            <input type="number" name="paid_amount" step="0.01" class="form-control" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Create</button>
        </div>
      </div>
    </form>
  </div>
</div>




<!-- ✅ SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  // ==== DOM refs ====
  const customerInput   = document.getElementById('customerNameInput');
  const customerIdInput = document.getElementById('customerId');
  const datalist        = document.getElementById('customerList');
  const phoneInput      = document.getElementById('phoneInput');
  const districtInput   = document.getElementById('districtInput');
  const productSearchInput = document.getElementById('productSearchInput');
  const productSelect      = document.getElementById('productSelect'); // container for checkboxes
  const container          = document.getElementById('productDetailsContainer');
  const totalAmount  = document.getElementById('totalAmount');
  const previousDue  = document.getElementById('previousDue');
  const totalDue     = document.getElementById('totalDue');
  const paidInput    = document.querySelector('[name="paid_amount"]');
  // ==== State ====
  const selectedProducts = new Set(); // keeps type_id (e.g. "product_5")
  let typingTimer;
  const DEBOUNCE_MS = 350;
  // =========================
  // Product list rendering
  // =========================
  function renderProductList(list) {
    productSelect.innerHTML = '';
    list.forEach(p => {
      const id = `${p.type}_${p.id}`;
      const labelParts = [`${p.name}`, `${p.quantity} available`];

      if (p.type === 'product' && p.buying_price != null) {
        labelParts.push(`৳${p.buying_price} (Buying Price)`);
      }
      if (p.type === 'partstock' && p.selling_price != null) {
        labelParts.push(`৳${p.selling_price} (Selling Price)`);
      }
      if (p.quantity === 0) labelParts.push('Out of Stock');
      const wrapper = document.createElement('div');
      wrapper.classList.add('form-check');
      // local filter no longer used; but keep dataset for any future UI
      wrapper.dataset.name = p.name.toLowerCase();
      wrapper.innerHTML = `
        <input class="form-check-input" type="checkbox" value="${id}" id="${id}"
          data-quantity="${p.quantity}"
          ${p.type === 'product' ? `data-buying-price="${p.buying_price}"` : ''}
          ${p.type === 'partstock' ? `data-selling-price="${p.selling_price}"` : ''}
          ${p.quantity === 0 ? 'disabled' : ''}>
        <label class="form-check-label" for="${id}">${labelParts.join(' — ')}</label>
      `;
      productSelect.appendChild(wrapper);
      const input = wrapper.querySelector('input');
      // যদি আগেই সিলেক্টেড ছিল, চেক করা থাকবে
      if (selectedProducts.has(id)) {
        input.checked = true;
      }
      input.addEventListener('change', function () {
        if (this.checked) {
          selectedProducts.add(id);
          addProductInput(id, p.type, p.name, p.quantity);
        } else {
          selectedProducts.delete(id);
          document.getElementById(`product_block_${id}`)?.remove();
          calculateTotals();
        }
      });
    });
  }
  // =========================
  // Initial: latest 10 items
  // =========================
  function loadLatest10() {
    fetch(`/bills/products?limit=10`)
      .then(res => res.json())
      .then(renderProductList)
      .catch(() => { productSelect.innerHTML = '<div class="text-danger">Failed to load products.</div>'; });
  }
  loadLatest10();
  // =========================
  // Server-side search (debounced)
  // =========================
  productSearchInput.addEventListener('input', function () {
    clearTimeout(typingTimer);
    const query = this.value.trim();

    typingTimer = setTimeout(() => {
      if (query.length >= 2) {
        fetch(`/bills/products?q=${encodeURIComponent(query)}`)
          .then(res => res.json())
          .then(renderProductList)
          .catch(() => { productSelect.innerHTML = '<div class="text-danger">Search failed.</div>'; });
      } else {
        // সার্চ ছোট/খালি হলে আবার লেটেস্ট ১০টা
        loadLatest10();
      }
    }, DEBOUNCE_MS);
  });
  // =========================
  // Customer autocomplete
  // =========================
  customerInput.addEventListener('input', function () {
    const query = this.value.trim();
    customerIdInput.value = '';
    phoneInput.value = '';
    districtInput.value = '';
    previousDue.textContent = '0';

    if (query.length < 2) {
      datalist.innerHTML = '';
      return;
    }
    fetch(`/bills/customers?name=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        datalist.innerHTML = '';
        data.forEach(c => {
          const option = document.createElement('option');
          option.value = c.name;
          option.dataset.id        = c.id;
          option.dataset.phone     = c.phone || '';
          option.dataset.district  = c.district || '';
          option.dataset.total_due = c.total_due ?? 0;
          datalist.appendChild(option);
        });
      });
  });
  // selected customer resolve (match option)
  customerInput.addEventListener('input', function () {
    const options = Array.from(datalist.options);
    const match = options.find(opt => opt.value === customerInput.value);
    if (match) {
      customerIdInput.value = match.dataset.id;
      phoneInput.value      = match.dataset.phone;
      districtInput.value   = match.dataset.district;
      previousDue.textContent = match.dataset.total_due;
    } else {
      customerIdInput.value = '';
      phoneInput.value = '';
      districtInput.value = '';
      previousDue.textContent = '0';
    }
    calculateTotals();
  });
  // =========================
  // Product detail blocks
  // =========================
  function addProductInput(uid, type, name, availableQty) {
    if (document.getElementById(`product_block_${uid}`)) return;
    const checkbox = document.getElementById(uid);
    const minPrice = type === 'product'
      ? parseFloat(checkbox?.dataset.buyingPrice || 0)
      : parseFloat(checkbox?.dataset.sellingPrice || 0);
    container.insertAdjacentHTML('beforeend', `
      <div id="product_block_${uid}" class="border p-2 mb-2 rounded bg-light">
        <h6>${name}</h6>
        <input type="hidden" name="product_details[${uid}][id]" value="${uid.split('_')[1]}">
        <input type="hidden" name="product_details[${uid}][type]" value="${type}">
        <div class="mb-2">
          <label>Quantity (Max: ${availableQty})</label>
          <input type="number" name="product_details[${uid}][quantity]" class="form-control"
                 required max="${availableQty}" data-max="${availableQty}">
        </div>
        <div class="mb-2">
          <label>Selling Price (Min: ৳${minPrice})</label>
          <input type="number" name="product_details[${uid}][unit_price]" class="form-control"
                 step="0.01" required data-min="${minPrice}">
        </div>
      </div>
    `);
    // newly added block — recalc
    setTimeout(() => calculateTotals(), 50);
  }
  // =========================
  // Instant total updates
  // =========================
  document.addEventListener('input', function (e) {
    const name = e.target.name || '';
    if (name.includes('[quantity]')) {
      const max = parseInt(e.target.dataset.max || '0');
      const val = parseInt(e.target.value || '0');
      if (val > max) {
        e.target.value = max;
        alert(`You cannot sell more than ${max} units for this product.`);
      }
    }
    if (
      name.includes('[quantity]') ||
      name.includes('[unit_price]') ||
      name === 'paid_amount'
    ) {
      calculateTotals();
    }
  });
  // Validate price on blur (not on every keystroke)
  document.addEventListener('blur', function (e) {
    if (e.target.name?.includes('[unit_price]')) {
      const input = e.target;
      const min   = parseFloat(input.dataset.min || 0);
      const val   = parseFloat(input.value || 0);
      if (!isNaN(val) && val < min) {
        alert(`❌ Selling price cannot be below ৳${min}`);
        input.value = min.toFixed(2);
        calculateTotals();
      }
    }
  }, true);
  // =========================
  // Total calculation
  // =========================
  function calculateTotals() {
    let total = 0;
    const previous = parseFloat(previousDue.textContent) || 0;
    const paid     = parseFloat(paidInput.value || 0);
    const blocks = document.querySelectorAll('[id^="product_block_"]');
    blocks.forEach(block => {
      const qtyInput   = block.querySelector('[name$="[quantity]"]');
      const priceInput = block.querySelector('[name$="[unit_price]"]');
      const qty   = parseFloat(qtyInput?.value || 0);
      const price = parseFloat(priceInput?.value || 0);
      total += qty * price;
    });
    totalAmount.textContent = total.toFixed(2);
    totalDue.textContent    = Math.max(0, total - paid + previous).toFixed(2);
  }
});
</script>
</body>
</html>
