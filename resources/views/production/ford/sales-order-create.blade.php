<x-app-layout :title="'Create Foreign Sales Order'" :sidebar="'production'">
    @push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .order-form { background: #fff; border-radius: 8px; padding: 2rem; box-shadow: 0 0 20px rgba(0, 0, 0, 0.05); }
        .form-header { margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #e0e0e0; }
        .form-header .company-info { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
        .form-header .company-logo { width: 60px; height: 60px; background: #ff0000; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; font-weight: bold; flex-shrink: 0; }
        .form-header .company-details { flex: 1; }
        .form-header .company-name { font-size: 1.25rem; font-weight: 700; color: #333; margin-bottom: 0.25rem; text-transform: uppercase; }
        .form-header .document-title { text-align: center; font-size: 1.75rem; font-weight: 700; color: #333; margin-top: 1rem; letter-spacing: 1px; }
        .customer-section { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem; }
        .customer-details, .order-details { background: #f8f9fa; padding: 1.5rem; border-radius: 6px; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #333; font-size: 0.875rem; }
        .form-control, .form-select { border: 1px solid #ced4da; border-radius: 4px; padding: 0.5rem 0.75rem; font-size: 0.875rem; }
        .form-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; font-size: 0.85rem; }
        .form-table th { background: #ff0000; color: #fff; padding: 0.5rem; border: 1px solid #dc3545; text-align: center; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; }
        .form-table td { padding: 0.4rem; border: 1px solid #e0e0e0; vertical-align: middle; }
        .form-table tfoot td { background: #f8f9fa; font-weight: 600; }
        .btn-add-row { background: #ff0000; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 4px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-add-row:hover { background: #cc0000; color: #fff; }
        .btn-remove-row { background: #ff0000; color: #fff; border: none; padding: 0.25rem 0.5rem; border-radius: 4px; cursor: pointer; font-size: 0.75rem; }
        .btn-remove-row:hover { background: #cc0000; }
        .form-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1rem; border-top: 2px solid #e0e0e0; }
        .bootstrap-select .dropdown-toggle { border: 1px solid #ced4da !important; background-color: #fff !important; padding: 0.375rem 0.75rem !important; }
        .bootstrap-select .dropdown-toggle:focus { outline: none !important; box-shadow: 0 0 0 0.2rem rgba(255, 0, 0, 0.25) !important; }
        .select2-container .select2-selection--single { height: 31px !important; line-height: 31px !important; font-size: 0.8rem; border: 1px solid #ced4da; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 31px !important; font-size: 0.8rem; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 30px !important; }
        .select2-dropdown { font-size: 0.8rem; z-index: 1060; }
    </style>
    @endpush

    <div class="container-fluid">
        <div class="mb-3 d-flex align-items-center justify-content-between">
            <a href="{{ route('production.ford.sales-order') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" style="border-radius: 6px; font-weight: 600;">
                <i class="las la-arrow-left fs-18"></i> Back to Foreign Sales Orders List
            </a>
        </div>

        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <!-- Form Section -->
                <div class="card order-form">
                    <!-- Form Header -->
                    <div class="form-header">
                        <div class="company-info">
                            <div class="company-logo">C</div>
                            <div class="company-details">
                                <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                                <div class="company-address">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                                <div class="company-contact">Tel. No.: 921-3984</div>
                            </div>
                        </div>
                        <div class="document-title">CREATE FOREIGN SALES ORDER</div>
                    </div>

                    <form id="salesOrderForm" action="{{ route('production.ford.sales-order.store') }}" method="POST" enctype="multipart/form-data" class="form-section">
                        @csrf
                        <!-- Customer and Order Details -->
                        <div class="customer-section">
                            <div class="customer-details">
                                <h5>Customer Information</h5>
                                <div class="form-group">
                                    <label>Company / Customer:</label>
                                    <select class="form-control selectpicker" data-live-search="true" data-size="8" data-live-search-placeholder="Search company..." name="customer_id" id="customerSelect" required>
                                        <option value="" selected disabled>Select Company...</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->customer_id }}" 
                                                data-address="{{ $customer->shipping_address ?? $customer->billing_address ?? '' }}"
                                                data-customer-name="{{ $customer->customer_name ?? '' }}"
                                                data-phone="{{ $customer->mobile ?: ($customer->main_phone ?: ($customer->work_phone ?: '')) }}"
                                                data-representatives='@json($customer->representatives ?? [])'>
                                                {{ $customer->customer_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="customer" id="customerNameHidden">
                                </div>
                                <div class="form-group">
                                    <label>Customer Representative / Name:</label>
                                    <select class="form-control" name="customer_representative" id="customerRepresentativeSelect">
                                        <option value="">Select Representative...</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Contact:</label>
                                    <input type="text" class="form-control" name="customer_contact" id="customerContactInput" placeholder="Contact number of representative...">
                                </div>
                                <div class="form-group">
                                    <label>Address:</label>
                                    <textarea class="form-control" name="billing_address" id="billingAddress" rows="2" placeholder="Address..."></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Transaction Type:</label>
                                    <select class="form-select" name="type" id="transactionType">
                                        <option value="foreign" selected>Foreign Order</option>
                                        <option value="paid">Paid Transaction</option>
                                        <option value="charge">Charge Transaction</option>
                                        <option value="area_consignment">Area Consignment</option>
                                        <option value="area_sales_consignment">Area Sales Consignment</option>
                                        <option value="direct_consignment">Direct Consignment</option>
                                        <option value="complimentary">Complimentary</option>
                                        <option value="cod">COD (Cash on Delivery)</option>
                                        <option value="evaluation">Evaluation</option>
                                    </select>
                                </div>
                                <div class="form-group" id="areaSalesStaffGroup" style="display: none;">
                                    <label>Area Sales Staff:</label>
                                    <select class="form-select" name="area_sales_staff_id">
                                        <option value="">Select Area Sales Staff...</option>
                                        @foreach($areaSalesStaff as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Remarks:</label>
                                    <textarea class="form-control" name="remarks" rows="2" placeholder="Additional notes..."></textarea>
                                </div>
                            </div>
                            <div class="order-details">
                                <h5>Order Information</h5>
                                <div class="form-group">
                                    <label>Date:</label>
                                    <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="form-group">
                                    <label>S.O. #:</label>
                                    <input type="text" class="form-control" name="so_number" value="FORD-SO-{{ date('Ymd') }}-{{ rand(1000, 9999) }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Terms:</label>
                                    <input type="text" class="form-control" name="terms" placeholder="e.g. 30 Days">
                                </div>
                                <div class="form-group">
                                    <label>REF #:</label>
                                    <input type="text" class="form-control" name="ref_number" placeholder="PO Reference...">
                                </div>
                                <div class="form-group">
                                    <label>Currency:</label>
                                    <select class="form-select" name="currency" id="formCurrency" onchange="onCurrencyChanged()">
                                        <option value="USD" selected>Dollar ($)</option>
                                        <option value="PHP">Peso (₱)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Freight Option:</label>
                                    <select class="form-select" name="freight_option" id="freightOptionSelect">
                                        <option value="">Select Freight Option</option>
                                        <option value="freight_collect">Freight Collect</option>
                                        <option value="freight_billing">Freight Billing</option>
                                        <option value="bill_client">Bill Client</option>
                                    </select>
                                </div>

                                <div class="form-group" id="forwarderGroup" style="display: none;">
                                    <label>Forwarder / Carrier:</label>
                                    <input type="text" class="form-control" name="forwarder" id="forwarderInput" placeholder="Enter Forwarder (e.g. LBC, J&T, 2GO, AP Cargo)">
                                </div>

                                <div class="form-group" id="serviceFeeGroup" style="display: none;">
                                    <label>Service Fee:</label>
                                    <input type="number" step="0.01" class="form-control" name="service_fee" id="serviceFeeInput" value="1.00" readonly>
                                </div>

                                <div class="form-group">
                                    <label>PO Attachment:</label>
                                    <input type="file" class="form-control" name="attachment" accept=".pdf,.jpg,.jpeg,.png">
                                </div>

                                <div class="form-group mt-2">
                                    <label>Proof of Payment Attachment:</label>
                                    <input type="file" class="form-control" name="proof_of_payment" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                            </div>
                        </div>

                        <!-- Items Table Actions -->
                        <div class="d-flex gap-2 mb-3 mt-4">
                            <button type="button" class="btn-add-row mb-0" id="addItemBtn" onclick="addProductRow()">
                                <i class="las la-plus"></i> Add Row
                            </button>
                            <button type="button" class="btn btn-primary" id="addBookItemBtn" onclick="addProductRow()" style="height: 38px; min-height: 38px; border: none; background: #007bff; color: #fff; font-weight: 600; border-radius: 4px; padding: 0 1rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem;">
                                <i class="las la-book"></i> Add Book
                            </button>
                        </div>

                        <div class="table-responsive border-0">
                            <table class="form-table" id="itemsTable" style="table-layout: fixed; width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;">QTY</th>
                                        <th style="width: 45px;">UNIT</th>
                                        <th style="width: 200px;">DESCRIPTION / PRODUCT</th>
                                        <th style="width: 80px;">ISBN</th>
                                        <th style="width: 60px;">AREA</th>
                                        <th style="width: 75px;">PRICE</th>
                                        <th style="width: 90px;">DISCOUNT</th>
                                        <th style="width: 80px;">AMOUNT</th>
                                        <th style="width: 60px;">ACTION</th>
                                    </tr>
                                </thead>
                            <tbody id="itemsBody">
                                <!-- Dynamic rows via JS -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="7" class="text-end text-uppercase fw-bold">Items Subtotal:</td>
                                    <td class="text-end fw-bold fs-6" id="subtotalAmount">$ 0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-end text-uppercase fw-bold">
                                        <div class="d-inline-flex align-items-center justify-content-end gap-2">
                                            <strong>Overall Discount:</strong>
                                            <input type="number" step="any" min="0" name="discount_value" id="discountValue" class="form-control form-control-sm text-end" style="width: 100px; display: inline-block;" value="0">
                                            <select name="discount_type" id="discountType" class="form-select form-select-sm" style="width: 90px; display: inline-block;">
                                                <option value="amount">Amt (<span class="currency-symbol">$</span>)</option>
                                                <option value="percentage">% (Pct)</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-danger fs-6" id="discountAmountDisplay">- $ 0.00</td>
                                    <td></td>
                                </tr>
                                <tr id="serviceFeeTotalRow" style="display: none;">
                                    <td colspan="7" class="text-end text-uppercase fw-bold">Service Fee:</td>
                                    <td class="text-end fw-bold text-primary fs-6" id="serviceFeeDisplay">+ $ 1.00</td>
                                    <td></td>
                                </tr>
                                <tr class="table-active">
                                    <td colspan="7" class="text-end text-uppercase fs-5 fw-bold">Net Total Amount:</td>
                                    <td class="text-end fw-bold fs-5 text-success" id="finalTotalAmount">$ 0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="reset" class="btn btn-secondary me-2">Reset</button>
                            <button type="button" class="btn btn-danger me-2" onclick="previewSO()">
                                <i class="las la-eye"></i> Preview Sales Order
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="las la-paper-plane"></i> Save & Submit Sales Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        let rowIndex = 0;

        function getCurrencySymbol() {
            const currency = document.getElementById('formCurrency').value;
            if (currency === 'USD') return '$';
            return '₱';
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Customer selection change listener
            const customerSelect = document.getElementById('customerSelect');
            const repSelect = document.getElementById('customerRepresentativeSelect');
            const contactInput = document.getElementById('customerContactInput');

            if (customerSelect) {
                customerSelect.addEventListener('change', function () {
                    const selected = this.options[this.selectedIndex];
                    const address = selected.getAttribute('data-address') || '';
                    const companyPhone = selected.getAttribute('data-phone') || '';
                    const customerName = selected.getAttribute('data-customer-name') || '';
                    let reps = [];
                    try {
                        reps = JSON.parse(selected.getAttribute('data-representatives') || '[]');
                    } catch(e) { reps = []; }

                    document.getElementById('billingAddress').value = address;
                    document.getElementById('customerNameHidden').value = customerName;

                    if (repSelect) {
                        repSelect.innerHTML = '<option value="">Select Representative...</option>';
                        let selectedRepPhone = '';

                        if (Array.isArray(reps) && reps.length > 0) {
                            reps.forEach((rep, idx) => {
                                const repName = typeof rep === 'object' ? (rep.name || rep.rep_name || '') : rep;
                                const repPhone = typeof rep === 'object' ? (rep.phone || rep.mobile || rep.contact || rep.contact_number || rep.phone_number || '') : '';
                                
                                if (repName) {
                                    const opt = document.createElement('option');
                                    opt.value = repName;
                                    opt.textContent = repName + (repPhone ? ` (${repPhone})` : '');
                                    opt.setAttribute('data-phone', repPhone);
                                    if (idx === 0) {
                                        opt.selected = true;
                                        selectedRepPhone = repPhone;
                                    }
                                    repSelect.appendChild(opt);
                                }
                            });
                        }

                        if (contactInput) {
                            contactInput.value = selectedRepPhone || companyPhone || '';
                        }
                    } else if (contactInput) {
                        contactInput.value = companyPhone || '';
                    }
                });
            }

            if (repSelect) {
                repSelect.addEventListener('change', function() {
                    const selectedOpt = this.options[this.selectedIndex];
                    const companyOpt = customerSelect ? customerSelect.options[customerSelect.selectedIndex] : null;
                    const companyPhone = companyOpt ? (companyOpt.getAttribute('data-phone') || '') : '';

                    if (selectedOpt) {
                        const repPhone = selectedOpt.getAttribute('data-phone');
                        if (contactInput) {
                            contactInput.value = repPhone || companyPhone || '';
                        }
                    }
                });
            }

            // Transaction Type change listener
            const transType = document.getElementById('transactionType');
            if (transType) {
                transType.addEventListener('change', function () {
                    const areaGroup = document.getElementById('areaSalesStaffGroup');
                    if (areaGroup) {
                        areaGroup.style.display = this.value === 'area_sales_consignment' ? 'block' : 'none';
                    }
                });
            }

            // Freight Option change listener
            const freightOpt = document.getElementById('freightOptionSelect');
            if (freightOpt) {
                freightOpt.addEventListener('change', function () {
                    const forwarderGroup = document.getElementById('forwarderGroup');
                    if (forwarderGroup) {
                        forwarderGroup.style.display = this.value ? 'block' : 'none';
                    }
                    calculateTotals();
                });
            }

            // Discount Input listeners
            document.getElementById('discountValue').addEventListener('input', calculateTotals);
            document.getElementById('discountType').addEventListener('change', calculateTotals);

            // Add Item & Add Book buttons both add searchable book/product dropdown row
            document.getElementById('addItemBtn')?.addEventListener('click', addProductRow);
            document.getElementById('addBookItemBtn')?.addEventListener('click', addProductRow);

            // Add initial row
            addProductRow();
        });

        function initProductSelect2(selectEl, rowIdx) {
            if (!window.jQuery || typeof jQuery.fn.select2 !== 'function') return;

            $(selectEl).select2({
                placeholder: 'Search Book / Product...',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('production.ford.sales-order.products-search') }}",
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            q: params.term || ''
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results || []
                        };
                    },
                    cache: true
                }
            }).on('select2:select', function(e) {
                const item = e.params.data;
                const row = document.getElementById(`row-${rowIdx}`);
                if (row && item) {
                    const basePrice = parseFloat(item.price) || 0;
                    const isbn = item.isbn || '';
                    row.dataset.basePrice = basePrice;

                    const curr = document.getElementById('formCurrency').value;
                    const finalPrice = (curr === 'USD') ? convertPesoToDollar(basePrice) : basePrice;

                    const priceInput = row.querySelector('.item-price');
                    const isbnInput = row.querySelector('.item-isbn');
                    if (priceInput) priceInput.value = finalPrice.toFixed(2);
                    if (isbnInput) isbnInput.value = isbn;

                    calculateRow(rowIdx);
                }
            }).on('select2:clear', function(e) {
                const row = document.getElementById(`row-${rowIdx}`);
                if (row) {
                    row.dataset.basePrice = 0;
                    const priceInput = row.querySelector('.item-price');
                    const isbnInput = row.querySelector('.item-isbn');
                    if (priceInput) priceInput.value = '0.00';
                    if (isbnInput) isbnInput.value = '';

                    calculateRow(rowIdx);
                }
            });
        }

        function addProductRow() {
            const tbody = document.getElementById('itemsBody');
            if (tbody && tbody.children.length >= 24) {
                alert('Maximum of 24 products allowed per order.');
                return;
            }
            rowIndex++;
            const tr = document.createElement('tr');
            tr.setAttribute('id', `row-${rowIndex}`);
            const sym = getCurrencySymbol();

            tr.innerHTML = `
                <td><input type="number" min="1" class="form-control form-control-sm text-center item-qty" name="items[${rowIndex}][quantity]" value="1" oninput="calculateRow(${rowIndex})" style="padding: 2px 4px; font-size: 0.8rem;"></td>
                <td><input type="text" class="form-control form-control-sm text-center" name="items[${rowIndex}][unit]" value="pcs" style="padding: 2px 4px; font-size: 0.8rem;"></td>
                <td style="min-width: 220px;">
                    <select class="form-control form-control-sm item-product-select" name="items[${rowIndex}][product_id]" required style="width: 100%;">
                        <option value="" selected disabled>Search Book / Product...</option>
                    </select>
                </td>
                <td><input type="text" class="form-control form-control-sm item-isbn" name="items[${rowIndex}][isbn]" placeholder="ISBN" readonly style="padding: 2px 4px; font-size: 0.8rem;"></td>
                <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][area]" placeholder="Area" style="padding: 2px 4px; font-size: 0.8rem;"></td>
                <td><input type="number" step="0.01" min="0" class="form-control form-control-sm text-end item-price" name="items[${rowIndex}][price]" placeholder="0.00" oninput="calculateRow(${rowIndex})" style="padding: 2px 4px; font-size: 0.8rem;"></td>
                <td>
                    <div class="d-flex gap-1 align-items-center">
                        <input type="number" step="any" min="0" class="form-control form-control-sm text-end item-disc-val" name="items[${rowIndex}][discount_value]" value="0" oninput="calculateRow(${rowIndex})" style="width: 45px; padding: 2px 4px; font-size: 0.75rem;">
                        <select class="form-select form-select-sm item-disc-type" name="items[${rowIndex}][discount_type]" onchange="calculateRow(${rowIndex})" style="width: 42px; padding: 2px 2px; font-size: 0.75rem;">
                            <option value="percentage">%</option>
                            <option value="amount">Amt</option>
                        </select>
                    </div>
                </td>
                <td class="text-end fw-bold item-subtotal-display" id="subtotal-${rowIndex}">${sym} 0.00</td>
                <td class="text-center">
                    <button type="button" class="btn-remove-row" onclick="removeProductRow(${rowIndex})">Remove</button>
                </td>
            `;

            tbody.appendChild(tr);

            const selectEl = tr.querySelector('.item-product-select');
            initProductSelect2(selectEl, rowIndex);
        }

        window.addProductRow = addProductRow;

        function removeProductRow(idx) {
            const tbody = document.getElementById('itemsBody');
            if (tbody.children.length > 1) {
                const row = document.getElementById(`row-${idx}`);
                if (row) row.remove();
                calculateTotals();
            } else {
                alert('At least one item is required.');
            }
        }

        function convertPesoToDollar(pesoPrice) {
            const p = parseFloat(pesoPrice) || 0;
            if (p <= 0) return 0;
            const dollarBase = p / 40;
            const dollarSRP = dollarBase * 1.10;
            const roundedDollar = Math.ceil(dollarSRP * 4) / 4;
            return roundedDollar;
        }

        function onProductSelect(idx, selectEl) {
            const row = document.getElementById(`row-${idx}`);
            const selected = selectEl.options[selectEl.selectedIndex];
            if (selected) {
                let basePrice = parseFloat(selected.getAttribute('data-price')) || 0;
                const isbn = selected.getAttribute('data-isbn') || '';

                row.dataset.basePrice = basePrice;
                const curr = document.getElementById('formCurrency').value;
                const finalPrice = (curr === 'USD') ? convertPesoToDollar(basePrice) : basePrice;

                row.querySelector('.item-price').value = finalPrice.toFixed(2);
                row.querySelector('.item-isbn').value = isbn;
                calculateRow(idx);
            }
        }

        function onCurrencyChanged() {
            const sym = getCurrencySymbol();
            const curr = document.getElementById('formCurrency').value;
            
            document.querySelectorAll('.currency-symbol').forEach(el => {
                el.textContent = sym;
            });

            document.querySelectorAll('#itemsBody tr').forEach(row => {
                const idAttr = row.getAttribute('id');
                if (idAttr) {
                    const idx = idAttr.replace('row-', '');
                    const selectEl = row.querySelector('.item-product-select');
                    if (selectEl && selectEl.selectedIndex > 0) {
                        const selected = selectEl.options[selectEl.selectedIndex];
                        const basePrice = parseFloat(row.dataset.basePrice || selected.getAttribute('data-price')) || 0;
                        const finalPrice = (curr === 'USD') ? convertPesoToDollar(basePrice) : basePrice;
                        row.querySelector('.item-price').value = finalPrice.toFixed(2);
                    }
                    calculateRow(idx);
                }
            });
            calculateTotals();
        }

        function calculateRow(idx) {
            const row = document.getElementById(`row-${idx}`);
            if (!row) return;

            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const discVal = parseFloat(row.querySelector('.item-disc-val').value) || 0;
            const discType = row.querySelector('.item-disc-type').value;

            const gross = qty * price;
            let discAmt = 0;
            if (discType === 'percentage') {
                discAmt = gross * (discVal / 100);
            } else {
                discAmt = discVal;
            }

            const subtotal = Math.max(0, gross - discAmt);
            const sym = getCurrencySymbol();
            const subtotalDisplay = row.querySelector('.item-subtotal-display');
            if (subtotalDisplay) {
                subtotalDisplay.textContent = `${sym} ${subtotal.toFixed(2)}`;
            }
            
            calculateTotals();
        }

        function calculateTotals() {
            const sym = getCurrencySymbol();
            let itemsSubtotal = 0;

            document.querySelectorAll('#itemsBody tr').forEach(row => {
                const idAttr = row.getAttribute('id');
                const qty = parseFloat(row.querySelector('.item-qty')?.value) || 0;
                const price = parseFloat(row.querySelector('.item-price')?.value) || 0;
                const discVal = parseFloat(row.querySelector('.item-disc-val')?.value) || 0;
                const discType = row.querySelector('.item-disc-type')?.value;

                const gross = qty * price;
                let discAmt = discType === 'percentage' ? (gross * (discVal / 100)) : discVal;
                const subtotal = Math.max(0, gross - discAmt);
                itemsSubtotal += subtotal;

                const subtotalDisplay = row.querySelector('.item-subtotal-display');
                if (subtotalDisplay) {
                    subtotalDisplay.textContent = `${sym} ${subtotal.toFixed(2)}`;
                }
            });

            document.getElementById('subtotalAmount').textContent = `${sym} ${itemsSubtotal.toFixed(2)}`;

            const discVal = parseFloat(document.getElementById('discountValue').value) || 0;
            const discType = document.getElementById('discountType').value;
            let headerDiscAmt = 0;

            if (discType === 'percentage') {
                headerDiscAmt = itemsSubtotal * (discVal / 100);
            } else {
                headerDiscAmt = discVal;
            }

            document.getElementById('discountAmountDisplay').textContent = `- ${sym} ${headerDiscAmt.toFixed(2)}`;

            const freightOpt = document.getElementById('freightOptionSelect').value;
            const serviceFeeGroup = document.getElementById('serviceFeeGroup');
            const serviceFeeTotalRow = document.getElementById('serviceFeeTotalRow');
            const curr = document.getElementById('formCurrency').value;
            
            let serviceFeeVal = 0;
            if (freightOpt) {
                if (curr === 'USD') serviceFeeVal = 1.00;
                else if (curr === 'EUR') serviceFeeVal = 1.00;
                else serviceFeeVal = 50.00;

                if (serviceFeeGroup) serviceFeeGroup.style.display = 'block';
                if (serviceFeeTotalRow) serviceFeeTotalRow.style.display = '';

                document.getElementById('serviceFeeInput').value = serviceFeeVal.toFixed(2);
                document.getElementById('serviceFeeDisplay').textContent = `+ ${sym} ${serviceFeeVal.toFixed(2)}`;
            } else {
                if (serviceFeeGroup) serviceFeeGroup.style.display = 'none';
                if (serviceFeeTotalRow) serviceFeeTotalRow.style.display = 'none';
            }

            let netTotal = itemsSubtotal - headerDiscAmt + serviceFeeVal;
            netTotal = Math.max(0, netTotal);
            document.getElementById('finalTotalAmount').textContent = `${sym} ${netTotal.toFixed(2)}`;
        }

        function previewSO() {
            alert('Sales Order Preview modal ready for review before submission.');
        }
    </script>
    @endpush
</x-app-layout>
