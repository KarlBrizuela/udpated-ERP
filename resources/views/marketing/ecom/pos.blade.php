<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        /* Select2 POS Theme Fixes */
        .select2-container--default .select2-selection--single {
            height: 48px;
            border: 2px solid #eef0f2;
            border-radius: 10px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #ff0000;
            outline: none;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 48px;
            padding-left: 15px;
            font-weight: 600;
            color: #333;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px;
        }
        .select2-dropdown {
            border: 2px solid #ff0000;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .select2-search--dropdown {
            padding: 10px;
        }
        .select2-search--dropdown .select2-search__field {
            border: 1px solid #eef0f2;
            border-radius: 5px;
            padding: 8px;
        }
        .select2-results__option--highlighted[aria-selected] {
            background-color: #ff0000;
        }

        .pos-container { display: flex; gap: 1rem; height: auto; min-height: calc(100vh - 200px); align-items: flex-start; }
        .pos-products-panel { flex: 1; display: flex; flex-direction: column; background: #fff; border-radius: 10px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        /* Category Tabs */
        .pos-category-tabs { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 2px solid #e0e0e0; }
        .pos-category-tab { padding: 0.75rem 1.25rem; background: transparent; border: none; border-bottom: 3px solid transparent; cursor: pointer; font-size: 14px; font-weight: 600; color: #666; transition: all 0.3s ease; }
        .pos-category-tab:hover { color: #ff0000; background: #fff5f5; border-radius: 6px 6px 0 0; }
        .pos-category-tab.active { color: #ff0000; border-bottom-color: #ff0000; }

        .pos-product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1.5rem; overflow-y: auto; padding: 0.5rem; }
        
        .pos-product-card { 
            background: #fff; 
            border: 2px solid #eef0f2; 
            border-radius: 12px; 
            padding: 1.25rem; 
            cursor: pointer; 
            text-align: center; 
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            position: relative;
        }
        .pos-product-card:hover { 
            border-color: #ff0000; 
            box-shadow: 0 8px 25px rgba(255, 0, 0, 0.15); 
            transform: translateY(-5px); 
        }
        .pos-product-card .stock-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.72rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: 700;
        }
        .pos-product-card img { 
            width: 100%; 
            height: 120px; 
            object-fit: cover; 
            border-radius: 8px; 
            margin-bottom: 1rem; 
        }
        .pos-product-card h6 { 
            font-size: 14px; 
            font-weight: 600; 
            margin: 0.5rem 0; 
            color: #333;
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .pos-product-card .price { 
            font-size: 18px; 
            font-weight: 700; 
            color: #ff0000; 
            margin-top: 0.5rem;
        }

        .pos-cart-panel { width: 450px; background: #f8f9fa; border-radius: 12px; padding: 1.5rem; border: 1px solid #dee2e6; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .pos-form-group { margin-bottom: 1.25rem; }
        .pos-form-group label { font-weight: 600; color: #333; margin-bottom: 0.5rem; display: block; }
        .pos-cart-items { flex: 1; overflow-y: auto; margin-bottom: 1.5rem; min-height: 200px; max-height: 400px; }
        
        .cart-item-card {
            background: #fff;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            border: 1px solid #eef0f2;
            transition: all 0.2s ease;
        }
        .cart-item-card:hover { border-color: #ff0000; }
        
        .pos-total-section {
            background: #fff;
            border-radius: 10px;
            padding: 1.5rem;
            border: 2px solid #dee2e6;
            margin-bottom: 1.5rem;
        }

        .pos-payment-btn-primary { background: #ff0000; color: white; border: none; padding: 18px; border-radius: 10px; font-weight: bold; width: 100%; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(255,0,0,0.3); }
        .pos-payment-btn-primary:hover { background: #cc0000; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255,0,0,0.4); }

        /* Checkout Modal Styles */
        .checkout-summary { background: #f8f9fa; padding: 1rem; border-radius: 10px; margin-bottom: 1rem; border: 1px solid #eef0f2; }
        .checkout-summary-row { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #eef0f2; font-size: 0.9rem; }
        .checkout-summary-row:last-child { border-bottom: none; font-size: 1.1rem; font-weight: 800; color: #ff0000; padding-top: 0.5rem; }
        
        .payment-method-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 1rem; }
        .payment-method-card { 
            padding: 1rem; border: 2px solid #eef0f2; border-radius: 10px; cursor: pointer; text-align: center; transition: all 0.3s ease; background: #fff;
        }
        .payment-method-card:hover { border-color: #ff0000; transform: translateY(-2px); box-shadow: 0 3px 10px rgba(255,0,0,0.1); }
        .payment-method-card.active { border-color: #ff0000; background: #fff5f5; }
        .payment-method-card i { font-size: 1.75rem; color: #ff0000; margin-bottom: 0.25rem; display: block; }
        .payment-method-card span { font-weight: 700; color: #333; font-size: 0.85rem; }
        
        .payment-details-section { 
            background: #fff; border-radius: 10px; padding: 1rem; border: 2px solid #ff0000; margin-top: 0.5rem; animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
    @endpush

    <!-- Hidden Barcode Scanner Input -->
    <input type="text" id="barcodeScanner" autofocus style="position: absolute; left: -9999px; top: -9999px; width: 1px; height: 1px; opacity: 0;">

    <div class="pos-container">
        <!-- Left Panel: Product Selection -->
        <div class="pos-products-panel">
            <div class="pos-category-tabs">
                <button class="pos-category-tab active" onclick="switchCategory('books', event)">Books</button>
                <button class="pos-category-tab" onclick="switchCategory('indices', event)">Book Indices</button>
                <button class="pos-category-tab" onclick="switchCategory('non-books', event)">Non-Books</button>
                <button class="pos-category-tab" onclick="switchCategory('bundle', event)">📦 Bundles</button>
            </div>
            <div class="mb-4">
                <input type="text" class="form-control form-control-lg" placeholder="Search products or scan barcode..." id="productSearch" onkeyup="filterProducts()">
            </div>
            <div class="pos-product-grid" id="productGrid">
                <!-- Products will be loaded dynamically -->
            </div>
        </div>

        <!-- Right Panel: Cart & Checkout -->
        <div class="pos-cart-panel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">MIBF Order Cart</h4>
                <button class="btn btn-sm btn-outline-danger" onclick="clearCart()">Clear</button>
            </div>
            
            <div class="pos-form-group">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="mb-0 font-w600">Customer *</label>
                    <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size: 0.75rem; border-radius: 4px;" data-bs-toggle="modal" data-bs-target="#quickCustomerModal">
                        <i class="las la-user-plus me-1"></i>+ Add Customer
                    </button>
                </div>
                <select class="form-control" id="customerSelect">
                    <option value="">Select Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->customer_id }}">{{ $customer->customer_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pos-form-group">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="mb-0 font-w600">SI Number *</label>
                    <span class="badge bg-light text-muted" style="font-size: 0.7rem; font-weight: 500;">Auto-incremented</span>
                </div>
                <input type="text" id="siNumber" class="form-control" value="{{ $nextSiNumber ?? '00001' }}" placeholder="e.g. 00123" style="height: 48px; border: 2px solid #eef0f2; border-radius: 10px; font-weight: 700; color: #ff0000; font-size: 1.05rem; padding-left: 15px;">
            </div>

            <div class="pos-cart-items" id="cartItems">
                <div class="text-center text-muted p-5">
                    <i class="las la-shopping-cart" style="font-size: 4rem; opacity: 0.2;"></i>
                    <p class="mt-2">Cart is empty</p>
                </div>
            </div>

            <div class="pos-total-section">
                <div class="d-flex justify-content-between mb-2 text-muted"><span>Subtotal</span><span id="subtotal">₱0.00</span></div>
                <div class="d-flex justify-content-between mb-2 align-items-center text-muted">
                    <span>Discount</span>
                    <div class="d-flex align-items-center gap-1">
                        <input type="number" step="any" min="0" id="discountValue" class="form-control form-control-sm text-end" style="width: 70px;" value="0" oninput="updateTotals()">
                        <select id="discountType" class="form-select form-select-sm" style="width: 60px; padding: 2px;" onchange="updateTotals()">
                            <option value="amount">₱</option>
                            <option value="percentage">%</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-2 text-muted"><span>Discount Amt</span><span id="discountDisplay" class="text-danger">-₱0.00</span></div>
                <div class="d-flex justify-content-between mt-3 pt-3 border-top"><h4 class="mb-0">Total</h4><h4 id="total" class="text-primary mb-0">₱0.00</h4></div>
            </div>

            <button class="pos-payment-btn-primary" onclick="openCheckoutModal()">
                PROCESS ORDER
            </button>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title m-0 text-white"><i class="las la-shopping-basket me-2"></i>Payment Details</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="checkout-summary">
                        <div class="checkout-summary-row"><span>Subtotal</span><span id="modalSubtotal">₱0.00</span></div>
                        <div class="checkout-summary-row"><span>Discount</span><span id="modalDiscount" class="text-danger">-₱0.00</span></div>
                        <div class="checkout-summary-row"><span>Grand Total</span><span id="modalTotal">₱0.00</span></div>
                    </div>

                    <h6 class="mb-2 font-w700 mt-2" style="font-size: 0.85rem;">Payment Channel</h6>
                    <div class="payment-method-grid">
                        <div class="payment-method-card active" onclick="selectMethod(this, 'cash')">
                            <i class="las la-money-bill-wave"></i>
                            <span>Cash</span>
                        </div>
                        <div class="payment-method-card" onclick="selectMethod(this, 'gcash')">
                            <i class="las la-mobile"></i>
                            <span>GCash</span>
                        </div>
                        <div class="payment-method-card" onclick="selectMethod(this, 'maya')">
                            <i class="las la-wallet"></i>
                            <span>Maya</span>
                        </div>
                        <div class="payment-method-card" onclick="selectMethod(this, 'card')">
                            <i class="las la-credit-card"></i>
                            <span>Card</span>
                        </div>
                        <div class="payment-method-card" onclick="selectMethod(this, 'check')">
                            <i class="las la-money-check"></i>
                            <span>Check</span>
                        </div>
                        <div class="payment-method-card" onclick="selectMethod(this, 'bank_transfer')">
                            <i class="las la-university"></i>
                            <span>Bank Transfer</span>
                        </div>
                    </div>

                    <div id="methodDetails">
                        <div id="cashDetails" class="payment-details-section" style="display:none;">
                            <label class="form-label font-w600">Cash Received</label>
                            <input type="number" class="form-control form-control-lg" id="cashReceived" placeholder="Enter amount..." min="0" step="0.01" oninput="calculateChange()">
                            <span id="cashChange" class="text-muted mt-2 d-block font-w600">Change: ₱0.00</span>
                        </div>
                        <div id="refDetails" class="payment-details-section">
                            <label class="form-label font-w600" id="refLabel">Order Notes</label>
                            <input type="text" class="form-control form-control-lg" id="refNumber" placeholder="Notes (Optional)">
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm px-4 font-w700" onclick="confirmOrder()">PLACE ORDER</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Success / Print Invoice Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white py-2">
                    <h6 class="modal-title text-white m-0"><i class="las la-check-circle me-2"></i>Order Placed - Sales Invoice Form</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" style="background: #f4f6f9;">
                    <iframe id="ecomOrderInvoiceIframe" src="about:blank" style="width: 100%; height: 78vh; min-height: 680px; border: none;"></iframe>
                </div>
                <div class="modal-footer py-2">
                    <a id="ecomPrintInvoiceNewTabBtn" href="#" target="_blank" class="btn btn-sm btn-outline-danger me-auto"><i class="las la-external-link-alt me-1"></i> Open In New Tab</a>
                    
                    <div class="form-check form-switch mb-0 ms-2 me-3">
                        <input class="form-check-input" type="checkbox" id="posPreprintedToggle" onchange="togglePosPreprintedMode(this)">
                        <label class="form-check-label fw-bold small text-dark" for="posPreprintedToggle">Print Data Only (For Official BIR Paper)</label>
                    </div>

                    <div class="btn-group btn-group-sm me-2" role="group">
                        <button type="button" class="btn btn-danger active" id="btnFormatWhole" onclick="switchPrintFormat('whole')">Whole Page</button>
                        <button type="button" class="btn btn-outline-danger" id="btnFormatHalf" onclick="switchPrintFormat('half')">1/2</button>
                    </div>

                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger btn-sm px-4 font-w700" onclick="document.getElementById('ecomOrderInvoiceIframe').contentWindow.print()" style="background:#ff0000;">
                        <i class="las la-print me-1"></i> PRINT INVOICE
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Customer Registration Modal -->
    <div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title m-0 text-white"><i class="las la-user-plus me-2"></i>Quick Customer Registration</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <form id="quickCustomerForm">
                        <div class="mb-3">
                            <label class="form-label font-w600">Customer Name *</label>
                            <input type="text" class="form-control" name="customer_name" required placeholder="Full Name">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-w600">Mobile Number</label>
                                <input type="tel" class="form-control" name="mobile" placeholder="09xxxxxxxxx">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-w600">Email Address</label>
                                <input type="email" class="form-control" name="main_email" placeholder="email@example.com">
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label font-w600">Billing Address</label>
                            <textarea class="form-control" name="billing_address" rows="2" placeholder="Full Address"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm px-4 font-w700" id="saveQuickCustomerBtn">REGISTER & SELECT</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Validation Errors Modal -->
    <div class="modal fade" id="validationErrorsModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title m-0 text-white"><i class="fas fa-exclamation-triangle me-2"></i>Registration Errors</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <p class="fw-bold">Please correct the following errors:</p>
                    <ul id="modalErrorList" class="text-danger mb-0"></ul>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Fix Errors</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        const products = @json($products);
        let cart = [];
        let activeCategory = 'books';

        function calculateNextSiNumber(currentSi) {
            if (!currentSi || !currentSi.trim()) return '00001';
            const trimmed = currentSi.trim();
            const match = trimmed.match(/^(.*?)(\d+)$/);
            if (match) {
                const prefix = match[1];
                const digits = match[2];
                const nextNum = parseInt(digits, 10) + 1;
                const nextDigits = String(nextNum).padStart(digits.length, '0');
                return prefix + nextDigits;
            }
            return trimmed + '1';
        }

        function switchCategory(category, evt) {
            activeCategory = category;
            document.querySelectorAll('.pos-category-tab').forEach(tab => tab.classList.remove('active'));
            if (evt && evt.target) {
                evt.target.classList.add('active');
            }
            renderProducts();
        }

        function renderProducts() {
            const grid = document.getElementById('productGrid');
            const searchTerm = document.getElementById('productSearch').value.toLowerCase().trim();
            
            const filtered = products.filter(p => {
                const matchesCategory = p.category === activeCategory;
                const matchesSearch = !searchTerm || 
                    p.name.toLowerCase().includes(searchTerm) || 
                    (p.barcode && p.barcode.toLowerCase().includes(searchTerm));
                return matchesCategory && matchesSearch;
            });
            
            if (filtered.length === 0) {
                grid.innerHTML = `
                    <div class="text-center text-muted p-4" style="grid-column: 1 / -1;">
                        <i class="las la-box-open" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="mt-2 mb-0 font-w600">No products found in this category.</p>
                    </div>`;
                return;
            }

            grid.innerHTML = filtered.map(p => {
                const stockBadgeClass = p.stock > 10 ? 'bg-success' : (p.stock > 0 ? 'bg-warning text-dark' : 'bg-danger');
                const stockText = p.stock > 0 ? `${p.stock} in MIBF` : 'Out of Stock';
                return `
                <div class="pos-product-card ${p.stock <= 0 ? 'opacity-75' : ''}" onclick="addToCart('${p.id}')">
                    <span class="badge ${stockBadgeClass} stock-badge">${stockText}</span>
                    <img src="${p.image}" alt="${p.name}">
                    <h6>${p.name}</h6>
                    <div class="price">₱${p.price.toLocaleString(undefined, {minimumFractionDigits: 2})}</div>
                </div>
            `}).join('');
        }
        
        function filterProducts() {
            renderProducts();
        }

        function addToCart(id) {
            const product = products.find(p => p.id === id || p.id == id);
            if (!product) return;

            if (product.stock <= 0) {
                return alert(`"${product.name}" has 0 stock in MIBF.`);
            }

            const existing = cart.find(item => item.id === id || item.id == id);
            
            if (existing) {
                if (existing.qty + 1 > product.stock) {
                    return alert(`Cannot add more. MIBF stock for "${product.name}" is only ${product.stock} pcs.`);
                }
                existing.qty++;
            } else {
                if (cart.length >= 24) {
                    return alert('Maximum of 24 products allowed per order.');
                }
                cart.push({ ...product, qty: 1, discount_value: 0, discount_type: 'percentage' });
            }
            renderCart();
        }

        function updateItemDiscount(index, val, type) {
            const item = cart[index];
            if (!item) return;

            if (val !== null && val !== undefined) {
                item.discount_value = val === '' ? '' : (parseFloat(val) || 0);
            }
            if (type !== null && type !== undefined) {
                item.discount_type = type;
            }
            updateTotals();
        }

        let currentSubtotal = 0;
        let currentTax = 0;
        let currentTotal = 0;
        let selectedMethodName = 'cash';

        function openCheckoutModal() {
            if (cart.length === 0) return alert('Your cart is empty');
            
            const customerId = document.getElementById('customerSelect').value;
            if (!customerId) {
                const quickModalEl = document.getElementById('quickCustomerModal');
                if (quickModalEl) {
                    const quickModal = bootstrap.Modal.getInstance(quickModalEl) || new bootstrap.Modal(quickModalEl);
                    quickModal.show();
                } else {
                    alert('Please select a customer');
                }
                return;
            }
            
            document.getElementById('modalSubtotal').textContent = `₱${currentSubtotal.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            
            const discountValueInput = document.getElementById('discountValue');
            const discountTypeSelect = document.getElementById('discountType');
            const discountVal = parseFloat(discountValueInput?.value) || 0;
            const discountType = discountTypeSelect?.value || 'amount';

            let discountAmount = 0;
            if (discountType === 'percentage') {
                discountAmount = currentSubtotal * (discountVal / 100);
            } else {
                discountAmount = discountVal;
            }
            document.getElementById('modalDiscount').textContent = `-₱${discountAmount.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            document.getElementById('modalTotal').textContent = `₱${currentTotal.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            
            selectMethod(document.querySelector('.payment-method-card.active') || document.querySelector('.payment-method-card'), selectedMethodName);
            
            new bootstrap.Modal(document.getElementById('checkoutModal')).show();
        }

        function selectMethod(el, method) {
            selectedMethodName = method;
            document.querySelectorAll('.payment-method-card').forEach(c => c.classList.remove('active'));
            if (el) el.classList.add('active');
            
            const refLabel = document.getElementById('refLabel');
            const refInput = document.getElementById('refNumber');
            const cashDetails = document.getElementById('cashDetails');
            const refDetails = document.getElementById('refDetails');
            
            if (method === 'cash') {
                if (cashDetails) cashDetails.style.display = 'block';
                if (refDetails) refDetails.style.display = 'block';
                if (refLabel) refLabel.textContent = 'Order Notes (Optional)';
                if (refInput) refInput.placeholder = 'Notes (Optional)';
                calculateChange();
            } else if (method === 'gcash') {
                if (cashDetails) cashDetails.style.display = 'none';
                if (refDetails) refDetails.style.display = 'block';
                if (refLabel) refLabel.textContent = 'GCash Reference Number';
                if (refInput) refInput.placeholder = 'Enter GCash Reference Number';
            } else if (method === 'maya' || method === 'paymaya') {
                if (cashDetails) cashDetails.style.display = 'none';
                if (refDetails) refDetails.style.display = 'block';
                if (refLabel) refLabel.textContent = 'Maya Reference Number';
                if (refInput) refInput.placeholder = 'Enter Maya Reference Number';
            } else if (method === 'bank_transfer' || method === 'bank') {
                if (cashDetails) cashDetails.style.display = 'none';
                if (refDetails) refDetails.style.display = 'block';
                if (refLabel) refLabel.textContent = 'Bank Reference Number';
                if (refInput) refInput.placeholder = 'Enter Bank Reference Number';
            } else if (method === 'card') {
                if (cashDetails) cashDetails.style.display = 'none';
                if (refDetails) refDetails.style.display = 'block';
                if (refLabel) refLabel.textContent = 'Card Reference Number';
                if (refInput) refInput.placeholder = 'Enter Card Reference Number';
            } else if (method === 'check') {
                if (cashDetails) cashDetails.style.display = 'none';
                if (refDetails) refDetails.style.display = 'block';
                if (refLabel) refLabel.textContent = 'Check Number';
                if (refInput) refInput.placeholder = 'Enter Check Number';
            } else {
                if (cashDetails) cashDetails.style.display = 'none';
                if (refDetails) refDetails.style.display = 'block';
                if (refLabel) refLabel.textContent = method.toUpperCase() + ' Reference #';
                if (refInput) refInput.placeholder = 'Enter Reference Number';
            }
        }

        function calculateChange() {
            const cashVal = parseFloat(document.getElementById('cashReceived').value) || 0;
            const change = Math.max(0, cashVal - currentTotal);
            document.getElementById('cashChange').textContent = 'Change: ₱' + change.toLocaleString(undefined, {minimumFractionDigits: 2});
        }

        function confirmOrder() {
            const customerId = document.getElementById('customerSelect').value;
            const refNumber = document.getElementById('refNumber').value;
            const cashReceived = parseFloat(document.getElementById('cashReceived').value) || 0;
            const currentSiVal = document.getElementById('siNumber')?.value?.trim() || '';

            if (!customerId) return alert('Please select a customer');

            if (selectedMethodName === 'cash' && cashReceived < currentTotal) {
                return alert('Insufficient cash received. Order Total is ₱' + currentTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
            }
            
            if (selectedMethodName !== 'cash' && !refNumber) {
                return alert('Please enter a payment reference / check number');
            }
            
            const orderData = {
                customer_id: customerId,
                si_number: currentSiVal,
                platform: 'MIBF',
                payment_method: selectedMethodName,
                cash_received: selectedMethodName === 'cash' ? cashReceived : null,
                items: cart.map(item => ({
                    product_id: item.book_id || item.real_id || item.id,
                    type: item.type || 'book',
                    book_id: item.book_id || null,
                    book_index_id: item.book_index_id || null,
                    book_bundle_id: item.book_bundle_id || null,
                    quantity: item.qty,
                    price: item.price,
                    discount_value: parseFloat(item.discount_value) || 0,
                    discount_type: item.discount_type || 'percentage',
                    discount_amount: item.itemDiscAmount || 0,
                    subtotal: item.itemSubtotal || (item.qty * item.price)
                })),
                subtotal: currentSubtotal,
                tax: 0,
                total: currentTotal,
                discount_value: parseFloat(document.getElementById('discountValue').value) || 0,
                discount_type: document.getElementById('discountType').value,
                notes: selectedMethodName === 'cash' ? refNumber : null,
                payment_reference: selectedMethodName !== 'cash' ? refNumber : null
            };

            fetch("{{ route('marketing.pos.process-ecom-order') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('checkoutModal')).hide();
                    
                    // Update SI Number field to next incremented value
                    if (data.next_si_number) {
                        const siInput = document.getElementById('siNumber');
                        if (siInput) siInput.value = data.next_si_number;
                    } else if (currentSiVal) {
                        const siInput = document.getElementById('siNumber');
                        if (siInput) siInput.value = calculateNextSiNumber(currentSiVal);
                    }

                    if (data.order && data.order.print_url) {
                        currentOrderPrintUrl = data.order.print_url;
                        switchPrintFormat('whole');
                    }
                    
                    new bootstrap.Modal(document.getElementById('successModal')).show();
                    
                    clearCart();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing the order');
            });
        }

        let currentOrderPrintUrl = '';

        function switchPrintFormat(format) {
            if (!currentOrderPrintUrl) return;
            
            const btnWhole = document.getElementById('btnFormatWhole');
            const btnHalf = document.getElementById('btnFormatHalf');
            
            // Reset all buttons
            [btnWhole, btnHalf].forEach(btn => {
                if (btn) {
                    btn.classList.remove('btn-danger', 'active');
                    btn.classList.add('btn-outline-danger');
                }
            });
            
            // Highlight the active button
            if (format === 'half') {
                btnHalf?.classList.remove('btn-outline-danger');
                btnHalf?.classList.add('btn-danger', 'active');
            } else {
                btnWhole?.classList.remove('btn-outline-danger');
                btnWhole?.classList.add('btn-danger', 'active');
            }
            
            let targetUrl = currentOrderPrintUrl + (currentOrderPrintUrl.includes('?') ? '&' : '?') + 'format=' + format + '&hide_actions=1';
            document.getElementById('ecomOrderInvoiceIframe').src = targetUrl;
            document.getElementById('ecomPrintInvoiceNewTabBtn').href = targetUrl;
        }

        function togglePosPreprintedMode(checkbox) {
            const iframe = document.getElementById('ecomOrderInvoiceIframe');
            if (iframe && iframe.contentWindow && iframe.contentWindow.document && iframe.contentWindow.document.body) {
                if (checkbox.checked) {
                    iframe.contentWindow.document.body.classList.add('preprinted-mode');
                } else {
                    iframe.contentWindow.document.body.classList.remove('preprinted-mode');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const iframe = document.getElementById('ecomOrderInvoiceIframe');
            if (iframe) {
                iframe.onload = function() {
                    const toggle = document.getElementById('posPreprintedToggle');
                    if (toggle && toggle.checked && this.contentWindow && this.contentWindow.document && this.contentWindow.document.body) {
                        this.contentWindow.document.body.classList.add('preprinted-mode');
                    }
                };
            }
        });

        function renderCart() {
            const container = document.getElementById('cartItems');
            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted p-5">
                        <i class="las la-shopping-cart" style="font-size: 4rem; opacity: 0.2;"></i>
                        <p class="mt-2">Cart is empty</p>
                    </div>`;
            } else {
                container.innerHTML = cart.map((item, index) => {
                    const discVal = item.discount_value !== undefined && item.discount_value !== '' ? item.discount_value : '';
                    const discType = item.discount_type || 'percentage';

                    return `
                    <div class="cart-item-card mb-2 p-2" style="background:#fff; border:1px solid #e9ecef; border-radius:8px;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div style="flex:1; padding-right:8px;">
                                <h6 class="mb-1 fw-bold" style="font-size: 0.85rem; line-height:1.2;">${item.name}</h6>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="text-primary font-w600" id="cart-item-subtotal-${index}" style="font-size: 0.85rem;">
                                        ₱${(item.itemSubtotal || (item.qty * item.price)).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-end">
                                <button class="btn btn-xs btn-outline-danger mb-1 py-0 px-1" onclick="removeItem(${index})" title="Remove item" style="line-height:1;">×</button>
                                <div class="input-group input-group-sm mb-1" style="width: 100px;">
                                    <button class="btn btn-outline-secondary py-0 px-2" type="button" onclick="updateQty(${index}, -1)">-</button>
                                    <input type="number" step="any" class="form-control text-center px-0 qty-input" value="${item.qty}" min="0.1" oninput="updateQtyDirect(${index}, this.value)">
                                    <button class="btn btn-outline-secondary py-0 px-2" type="button" onclick="updateQty(${index}, 1)">+</button>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top" style="border-color:#f1f3f5!important;">
                            <span class="text-muted" style="font-size: 0.75rem;"><i class="las la-tag me-1"></i>Item Disc:</span>
                            <div class="d-flex align-items-center gap-1">
                                <input type="number" step="any" min="0" class="form-control form-control-sm text-end p-1" 
                                       style="width: 65px; font-size: 0.75rem; height: 24px;" 
                                       value="${discVal}" placeholder="0" 
                                       oninput="updateItemDiscount(${index}, this.value, null)">
                                <select class="form-select form-select-sm px-1 py-0" 
                                        style="width: 52px; font-size: 0.75rem; height: 24px;" 
                                        onchange="updateItemDiscount(${index}, null, this.value)">
                                    <option value="percentage" ${discType === 'percentage' ? 'selected' : ''}>%</option>
                                    <option value="amount" ${discType === 'amount' ? 'selected' : ''}>₱</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `;
                }).join('');
            }
            
            updateTotals();
        }
        
        function updateQty(index, change) {
            const item = cart[index];
            if (!item) return;

            const newQty = item.qty + change;
            if (newQty > 0) {
                if (newQty > item.stock) {
                    return alert(`Cannot exceed available MIBF stock (${item.stock} pcs).`);
                }
                item.qty = newQty;
            } else {
                cart.splice(index, 1);
            }
            renderCart();
        }

        function updateQtyDirect(index, value) {
            const qtyVal = parseFloat(value);
            if (qtyVal && qtyVal > 0) {
                const item = cart[index];
                if (qtyVal > item.stock) {
                    alert(`Requested quantity exceeds available MIBF stock (${item.stock} pcs).`);
                    renderCart();
                    return;
                }
                item.qty = qtyVal;
                updateTotals();
            }
        }

        function updateTotals() {
            currentSubtotal = 0;
            cart.forEach((item, index) => {
                const qty = parseFloat(item.qty) || 0;
                const price = parseFloat(item.price) || 0;
                const gross = qty * price;
                const discVal = parseFloat(item.discount_value) || 0;
                const discType = item.discount_type || 'percentage';

                let dAmt = discType === 'percentage' ? gross * (discVal / 100) : discVal;
                dAmt = Math.min(gross, Math.max(0, dAmt));
                const netSub = Math.max(0, gross - dAmt);

                item.itemDiscAmount = dAmt;
                item.itemSubtotal = netSub;

                currentSubtotal += netSub;

                const cardSubtotalEl = document.getElementById(`cart-item-subtotal-${index}`);
                if (cardSubtotalEl) {
                    let discTag = '';
                    if (discVal > 0) {
                        discTag = discType === 'percentage' 
                            ? `<span class="badge bg-danger-subtle text-danger ms-1" style="font-size:0.65rem; padding: 2px 4px;">-${discVal}%</span>`
                            : `<span class="badge bg-danger-subtle text-danger ms-1" style="font-size:0.65rem; padding: 2px 4px;">-₱${dAmt.toFixed(2)}</span>`;
                    }
                    cardSubtotalEl.innerHTML = `₱${netSub.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}` + discTag;
                }
            });
            
            const discountValueInput = document.getElementById('discountValue');
            const discountTypeSelect = document.getElementById('discountType');
            const discountVal = parseFloat(discountValueInput?.value) || 0;
            const discountType = discountTypeSelect?.value || 'amount';

            let discountAmount = 0;
            if (discountType === 'percentage') {
                discountAmount = currentSubtotal * (discountVal / 100);
            } else {
                discountAmount = discountVal;
            }

            const discountDisplay = document.getElementById('discountDisplay');
            if (discountDisplay) {
                discountDisplay.textContent = `-₱${discountAmount.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            }

            currentTax = 0; // Tax 12% removed
            currentTotal = Math.max(0, currentSubtotal - discountAmount);

            document.getElementById('subtotal').textContent = `₱${currentSubtotal.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            document.getElementById('total').textContent = `₱${currentTotal.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function clearCart() {
            cart = [];
            if (window.jQuery && $('#customerSelect').length) {
                $('#customerSelect').val('').trigger('change');
            }
            renderCart();
            updateTotals();
        }

        // Barcode scanner listener
        let barcodeBuffer = '';
        let barcodeTimeout;
        document.addEventListener('keydown', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
                if (e.target.id !== 'barcodeScanner') return;
            }
            if (e.key === 'Enter') {
                if (barcodeBuffer.length > 2) {
                    processBarcode(barcodeBuffer);
                    barcodeBuffer = '';
                }
            } else if (e.key.length === 1) {
                barcodeBuffer += e.key;
                clearTimeout(barcodeTimeout);
                barcodeTimeout = setTimeout(() => { barcodeBuffer = ''; }, 200);
            }
        });

        function processBarcode(code) {
            const found = products.find(p => 
                (p.barcode && p.barcode.toLowerCase() === code.toLowerCase())
            );
            if (found) {
                addToCart(found.id);
            } else {
                alert('Barcode not found in MIBF inventory: ' + code);
            }
        }

        // Initialize grid, Select2 & Quick Customer Registration
        document.addEventListener('DOMContentLoaded', function() {
            if (window.jQuery && $('#customerSelect').length) {
                $('#customerSelect').select2({
                    placeholder: "Search for a customer...",
                    allowClear: true,
                    width: '100%'
                });
            }

            const quickCustForm = document.getElementById('quickCustomerForm');
            const saveQuickBtn = document.getElementById('saveQuickCustomerBtn');
            const quickModalEl = document.getElementById('quickCustomerModal');

            saveQuickBtn?.addEventListener('click', async function() {
                saveQuickBtn.disabled = true;
                saveQuickBtn.textContent = 'Registering...';

                const formData = new FormData(quickCustForm);
                const data = {};
                formData.forEach((value, key) => data[key] = value);

                try {
                    const response = await fetch('/marketing/customers', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (response.ok) {
                        const quickModalInstance = bootstrap.Modal.getInstance(quickModalEl) || new bootstrap.Modal(quickModalEl);
                        quickModalInstance.hide();
                        quickCustForm.reset();

                        const custObj = result.customer || result;
                        const custId = custObj.customer_id || custObj.id;
                        const custName = custObj.customer_name || data.customer_name;

                        if (custId) {
                            const newOption = new Option(custName, custId, true, true);
                            if (window.jQuery && $('#customerSelect').length) {
                                $('#customerSelect').append(newOption).trigger('change');
                            } else {
                                const sel = document.getElementById('customerSelect');
                                sel.add(newOption);
                                sel.value = custId;
                            }

                            if (cart.length > 0) {
                                setTimeout(() => openCheckoutModal(), 400);
                            }
                        } else {
                            location.reload();
                        }
                    } else if (response.status === 422) {
                        const errorList = document.getElementById('modalErrorList');
                        if (errorList) {
                            errorList.innerHTML = '';
                            Object.values(result.errors || {}).flat().forEach(err => {
                                const li = document.createElement('li');
                                li.textContent = err;
                                errorList.appendChild(li);
                            });
                        }
                        const errModalEl = document.getElementById('validationErrorsModal');
                        if (errModalEl) {
                            const errModal = bootstrap.Modal.getInstance(errModalEl) || new bootstrap.Modal(errModalEl);
                            errModal.show();
                        }
                    } else {
                        alert(result.message || 'Error registering customer');
                    }
                } catch (err) {
                    console.error('Error registering customer:', err);
                    alert('An error occurred while registering customer.');
                } finally {
                    saveQuickBtn.disabled = false;
                    saveQuickBtn.textContent = 'REGISTER & SELECT';
                }
            });

            renderProducts();
        });
    </script>
    @endpush
</x-app-layout>
