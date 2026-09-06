<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Create Freight Quotation Request</h5>
                    </div>
                    <div class="card-body">
                        <form id="freightQuotationForm" method="POST" action="{{ $storeRoute ?? route('marketing.freight-quotations.store') }}" enctype="multipart/form-data">
                            @csrf
                            @if(!empty($isFord))
                                <input type="hidden" name="source" value="ford">
                            @endif

                            <!-- Customer Selection Section -->
                            <h6 class="border-bottom pb-2 mb-3"><strong>Customer Information</strong></h6>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Company:</label>
                                    <select class="form-control selectpicker @error('customer_id') is-invalid @enderror" 
                                            data-live-search="true" data-size="8" data-live-search-placeholder="Search company..."
                                            name="customer_id" id="fqCustomerSelect" required>
                                        <option value="">Select Company...</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->customer_id }}"
                                                    data-customer-name="{{ $customer->customer_name ?? '' }}"
                                                    data-representatives='@json($customer->representatives ?? [])'
                                                    data-address="{{ $customer->shipping_address ?? $customer->billing_address ?? '' }}"
                                                    data-province="{{ $customer->province ?? $customer->city_municipality ?? '' }}"
                                                    data-phone="{{ $customer->main_phone ?? $customer->mobile ?? '' }}"
                                                    data-terms="{{ $customer->payment_terms ?? $customer->terms ?? '' }}"
                                                    {{ old('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                                                {{ $customer->customer_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Customer Name:</label>
                                    <select class="form-control" name="customer_representative" id="fqRepresentativeSelect">
                                        <option value="">Select Representative...</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Transaction Type:</label>
                                    <select class="form-control @error('transaction_type') is-invalid @enderror" name="transaction_type" id="fqTransactionType" required>
                                        <option value="paid" {{ old('transaction_type', 'paid') === 'paid' ? 'selected' : '' }}>Paid Transaction</option>
                                        <option value="charge" {{ old('transaction_type') === 'charge' ? 'selected' : '' }}>Charge Transaction</option>
                                        <option value="area_consignment" {{ old('transaction_type') === 'area_consignment' ? 'selected' : '' }}>Area Consignment</option>
                                        <option value="area_sales_consignment" {{ old('transaction_type') === 'area_sales_consignment' ? 'selected' : '' }}>Area Sales Consignment</option>
                                        <option value="direct_consignment" {{ old('transaction_type') === 'direct_consignment' ? 'selected' : '' }}>Direct Consignment</option>
                                        <option value="foreign" {{ (old('transaction_type') === 'foreign' || !empty($isFord)) ? 'selected' : '' }}>Foreign Order</option>
                                        <option value="complimentary" {{ old('transaction_type') === 'complimentary' ? 'selected' : '' }}>Complimentary</option>
                                        <option value="cod" {{ old('transaction_type') === 'cod' ? 'selected' : '' }}>Due on Receipt (COD)</option>
                                        <option value="evaluation" {{ old('transaction_type') === 'evaluation' ? 'selected' : '' }}>Evaluation</option>
                                    </select>
                                    @error('transaction_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Terms:</label>
                                    <input type="text" class="form-control @error('terms') is-invalid @enderror" 
                                           name="terms" id="fqTermsInput" placeholder="e.g. 30 Days, COD" 
                                           value="{{ old('terms') }}">
                                    @error('terms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Currency:</label>
                                    <select class="form-control @error('currency') is-invalid @enderror" name="currency" id="fqCurrencySelect" required>
                                         <option value="PHP" {{ old('currency', !empty($isFord) ? 'USD' : 'PHP') === 'PHP' ? 'selected' : '' }}>PHP (₱)</option>
                                         <option value="USD" {{ old('currency', !empty($isFord) ? 'USD' : 'PHP') === 'USD' ? 'selected' : '' }}>USD ($)</option>
                                     </select>
                                    @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Proof of Payment (Optional):</label>
                                    <input type="file" class="form-control @error('proof_of_payment') is-invalid @enderror" 
                                           name="proof_of_payment" id="fqProofOfPayment" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">Attach deposit slip, bank transfer screenshot, or receipt (PDF, JPG, PNG up to 10MB)</small>
                                    @error('proof_of_payment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <hr>

                            <!-- Shipment Details Section -->
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                <h6 class="mb-0"><strong>Shipment Details</strong></h6>
                                <button type="button" class="btn btn-sm btn-outline-secondary shadow-sm" id="toggleAutofillBtn">
                                    <i class="fas fa-edit me-1"></i> <span id="autofillBtnText">Manual Input Mode</span>
                                </button>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Origin Contact:</label>
                                    <input type="text" class="form-control @error('origin_contact') is-invalid @enderror" 
                                           name="origin_contact" placeholder="Contact person name" 
                                           value="{{ old('origin_contact') }}" required>
                                    @error('origin_contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Origin Province:</label>
                                    <input type="text" class="form-control @error('origin_province') is-invalid @enderror" 
                                           name="origin_province" placeholder="Province name" 
                                           value="{{ old('origin_province') }}" required>
                                    @error('origin_province')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Origin Address:</label>
                                <textarea class="form-control @error('origin_address') is-invalid @enderror" 
                                          name="origin_address" rows="2" placeholder="Full pickup address" 
                                          required>{{ old('origin_address') }}</textarea>
                                @error('origin_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Destination Contact:</label>
                                    <input type="text" class="form-control @error('destination_contact') is-invalid @enderror" 
                                           name="destination_contact" placeholder="Contact person name" 
                                           value="{{ old('destination_contact') }}" required>
                                    @error('destination_contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Destination Province:</label>
                                    <input type="text" class="form-control @error('destination_province') is-invalid @enderror" 
                                           name="destination_province" placeholder="Province name" 
                                           value="{{ old('destination_province') }}" required>
                                    @error('destination_province')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Destination Address:</label>
                                <textarea class="form-control @error('destination_address') is-invalid @enderror" 
                                          name="destination_address" rows="2" placeholder="Full delivery address" 
                                          required>{{ old('destination_address') }}</textarea>
                                @error('destination_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label class="form-label">Service Mode:</label>
                                <select class="form-control @error('service_mode') is-invalid @enderror" 
                                        name="service_mode" required>
                                    <option value="">Select Service Mode</option>
                                    <option value="Sea Freight" {{ old('service_mode') === 'Sea Freight' ? 'selected' : '' }}>Sea Freight</option>
                                    <option value="Air Freight" {{ old('service_mode') === 'Air Freight' ? 'selected' : '' }}>Air Freight</option>
                                    <option value="Land Freight" {{ old('service_mode') === 'Land Freight' ? 'selected' : '' }}>Land Freight</option>
                                    <option value="Mixed" {{ old('service_mode') === 'Mixed' ? 'selected' : '' }}>Mixed</option>
                                </select>
                                @error('service_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                 <label class="form-label">Forwarder:</label>
                                 <input type="text" class="form-control @error('forwarder') is-invalid @enderror" 
                                        name="forwarder" placeholder="Enter forwarder name (e.g., LBC, 2GO, J&T, AP Cargo, FedEx, DHL, etc.)..." 
                                        value="{{ old('forwarder') }}">
                                 @error('forwarder')<div class="invalid-feedback">{{ $message }}</div>@enderror
                             </div>

                            <div class="mb-3">
                                <label class="form-label">Freight Option:</label>
                                <select class="form-control @error('freight_option') is-invalid @enderror" 
                                        name="freight_option" id="freightOption">
                                    <option value="">Select Freight Option</option>
                                    <option value="freight_collect" {{ old('freight_option') === 'freight_collect' ? 'selected' : '' }}>Freight Collect</option>
                                    <option value="freight_billing" {{ old('freight_option') === 'freight_billing' ? 'selected' : '' }}>Freight Billing</option>
                                    <option value="bill_client" {{ old('freight_option') === 'bill_client' ? 'selected' : '' }}>Bill Client</option>
                                </select>
                                @error('freight_option')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="alert alert-info py-2 mb-3" id="serviceFeeNotice" style="display: none;">
                                <strong>Service Fee:</strong> <span id="serviceFeeNoticeText">₱ 50.00</span>
                            </div>

                            <hr>

                            <!-- Sales Order Items Section -->
                            <h6 class="border-bottom pb-2 mb-3"><strong>Sales Order Items (Optional)</strong></h6>
                            <p class="text-muted small">Add items that will be included in the Sales Order created from this quotation.</p>

                            <button type="button" class="btn btn-sm btn-danger mb-3" id="addSOItem" style="background: #ff0000; border: none; padding: 0.5rem 1rem;">
                                <i class="fas fa-plus me-1"></i>Add Item
                            </button>

                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-bordered" id="soItemsTable">
                                    <thead class="table-primary">
                                        <tr>
                                            <th style="width: 100px;">QTY</th>
                                            <th>DESCRIPTION / PRODUCT</th>
                                            <th style="width: 120px;">UNIT PRICE</th>
                                            <th style="width: 140px;">DISCOUNT</th>
                                            <th style="width: 120px;">AMOUNT</th>
                                            <th style="width: 80px;">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody id="soItemsBody">
                                        <!-- Dynamic rows via JS -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                            <td class="text-end fw-bold" id="soSubtotal">₱ 0.00</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-end align-middle">
                                                <div class="d-inline-flex align-items-center justify-content-end gap-2">
                                                    <strong>Discount:</strong>
                                                    <input type="number" step="any" min="0" name="discount_value" id="discountValue" class="form-control form-control-sm text-end" style="width: 90px;" value="{{ old('discount_value', 0) }}" placeholder="0">
                                                    <select name="discount_type" id="discountType" class="form-select form-select-sm" style="width: 80px;">
                                                        <option value="percentage" {{ old('discount_type', 'percentage') === 'percentage' ? 'selected' : '' }}>%</option>
                                                        <option value="amount" {{ old('discount_type') === 'amount' ? 'selected' : '' }} class="fq-disc-amount-opt">₱</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td class="text-end fw-bold text-danger align-middle" id="soTotalDiscount">- ₱ 0.00</td>
                                            <td></td>
                                        </tr>
                                        <tr id="serviceFeeRow" style="display: none;">
                                            <td colspan="4" class="text-end"><strong>Service Fee:</strong></td>
                                            <td class="text-end fw-bold" id="soServiceFee">₱ 50.00</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                            <td class="text-end fw-bold" id="soTotal">₱ 0.00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Hidden Product Options for JS -->
                            <select id="productSource" class="d-none">
                                <option value="" disabled selected>Select Product...</option>
                                @if(isset($products))
                                    @foreach($products as $product)
                                        @php
                                            $dispName = $product->display_name ?? $product->name;
                                        @endphp
                                        <option value="{{ $product->id }}" 
                                                data-price="{{ $product->price }}" 
                                                data-name="{{ $dispName }}">
                                                {{ $dispName }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>

                            <!-- Action Buttons -->
                            <div class="form-actions mt-4 pt-3 border-top">
                                @php
                                    $cancelUrl = !empty($isFord) ? route('production.ford.freight-quotation.index') : route('marketing.freight-quotations.list');
                                @endphp
                                <a href="{{ $cancelUrl }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-check me-1"></i>Submit for Logistics Review
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        /* Prevent bootstrap-select dropdown from overflowing viewport and hiding search box */
        .bootstrap-select .dropdown-menu {
            max-height: 360px !important;
            overflow: hidden !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18) !important;
            border-radius: 6px !important;
        }
        .bootstrap-select .bs-searchbox {
            position: sticky !important;
            top: 0 !important;
            z-index: 1050 !important;
            background: #ffffff !important;
            padding: 8px 10px !important;
            border-bottom: 1px solid #e9ecef !important;
        }
        .bootstrap-select .bs-searchbox input {
            font-size: 0.9rem !important;
            padding: 0.4rem 0.75rem !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
        }
        .bootstrap-select .dropdown-menu .inner {
            max-height: 280px !important;
            overflow-y: auto !important;
        }
        /* Remove spinner arrows for discount value input for clear visibility */
        input[type=number].so-discount-val::-webkit-inner-spin-button, 
        input[type=number].so-discount-val::-webkit-outer-spin-button { 
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number].so-discount-val {
            -moz-appearance: textfield;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof $ !== 'undefined' && $.fn && $.fn.selectpicker) {
                $('select[name="customer_id"]').selectpicker({ size: 8, liveSearch: true });
            }

            // Sales Order Items Logic
            const addSOBtn = document.getElementById('addSOItem');
            const soItemsBody = document.getElementById('soItemsBody');
            const productSource = document.getElementById('productSource');
            const freightOption = document.getElementById('freightOption');
            const serviceFeeNotice = document.getElementById('serviceFeeNotice');
            const serviceFeeRow = document.getElementById('serviceFeeRow');
            const soTotal = document.getElementById('soTotal');

            // Freight Option Change Handler
            if (freightOption) {
                freightOption.addEventListener('change', function() {
                    const isFreightCollect = this.value === 'freight_collect';
                    if (serviceFeeRow) {
                        serviceFeeRow.style.display = isFreightCollect ? 'table-row' : 'none';
                    }
                    calculateSOSubtotal();
                });
                
                // Trigger on page load in case freight option has a value
                const event = new Event('change');
                freightOption.dispatchEvent(event);
            }

            function convertPesoToDollar(pesoPrice) {
                const p = parseFloat(pesoPrice) || 0;
                if (p <= 0) return 0;
                const dollarBase = p / 40;
                const dollarSRP = dollarBase * 1.10;
                const roundedDollar = Math.ceil(dollarSRP * 4) / 4;
                return roundedDollar;
            }

            function getProductPriceForSelectedCurrency(basePrice) {
                const fqCurrencySelect = document.getElementById('fqCurrencySelect');
                const curr = fqCurrencySelect ? fqCurrencySelect.value : 'PHP';
                const p = parseFloat(basePrice) || 0;
                if (curr === 'USD') {
                    return convertPesoToDollar(p);
                }
                return p;
            }

            function getFQCurrencySymbol() {
                const fqCurrencySelect = document.getElementById('fqCurrencySelect');
                const curr = fqCurrencySelect ? fqCurrencySelect.value : 'PHP';
                if (curr === 'USD') return '$';
                return '₱';
            }

            function updateCurrencySymbols() {
                const sym = getFQCurrencySymbol();
                const fqCurrencySelect = document.getElementById('fqCurrencySelect');
                const curr = fqCurrencySelect ? fqCurrencySelect.value : 'PHP';
                
                document.querySelectorAll('#discountType option[value="amount"], .so-discount-type option[value="amount"]').forEach(opt => {
                    opt.textContent = sym;
                });

                let feeVal = 50.00;

                const serviceFeeEl = document.getElementById('soServiceFee');
                if (serviceFeeEl) serviceFeeEl.textContent = sym + ' ' + feeVal.toFixed(2);

                const serviceFeeNoticeText = document.getElementById('serviceFeeNoticeText');
                if (serviceFeeNoticeText) serviceFeeNoticeText.textContent = sym + ' ' + feeVal.toFixed(2);

                document.querySelectorAll('#soItemsBody tr').forEach(row => {
                    const productSelect = row.querySelector('.so-product');
                    const priceInput = row.querySelector('.so-price');
                    if (productSelect && productSelect.selectedIndex > 0) {
                        const option = productSelect.options[productSelect.selectedIndex];
                        const basePrice = parseFloat(row.dataset.basePrice || option.dataset.price) || 0;
                        priceInput.value = getProductPriceForSelectedCurrency(basePrice).toFixed(2);
                    }
                    calculateRow(row);
                });
                calculateSOSubtotal();
            }

            const fqCurrencySelectEl = document.getElementById('fqCurrencySelect');
            if (fqCurrencySelectEl) {
                fqCurrencySelectEl.addEventListener('change', updateCurrencySymbols);
            }

            const discountValueInput = document.getElementById('discountValue');
            const discountTypeSelect = document.getElementById('discountType');

            if (discountValueInput) discountValueInput.addEventListener('input', calculateSOSubtotal);
            if (discountTypeSelect) discountTypeSelect.addEventListener('change', calculateSOSubtotal);

            function calculateSOSubtotal() {
                let itemsNetTotal = 0;

                document.querySelectorAll('#soItemsBody tr').forEach(row => {
                    const qty = parseFloat(row.querySelector('.so-qty')?.value) || 0;
                    const price = parseFloat(row.querySelector('.so-price')?.value) || 0;
                    const discVal = parseFloat(row.querySelector('.so-discount-val')?.value) || 0;
                    const discType = row.querySelector('.so-discount-type')?.value || 'percentage';

                    const rowGross = qty * price;
                    let rowDisc = discType === 'percentage' ? rowGross * (discVal / 100) : discVal;
                    rowDisc = Math.min(rowGross, Math.max(0, rowDisc));
                    const rowNet = Math.max(0, rowGross - rowDisc);

                    itemsNetTotal += rowNet;
                });

                // Calculate Overall Discount from summary input
                const overallDiscVal = parseFloat(discountValueInput?.value) || 0;
                const overallDiscType = discountTypeSelect?.value || 'percentage';

                let overallDiscountAmount = 0;
                if (overallDiscType === 'percentage') {
                    overallDiscountAmount = itemsNetTotal * (overallDiscVal / 100);
                } else {
                    overallDiscountAmount = overallDiscVal;
                }
                overallDiscountAmount = Math.min(itemsNetTotal, Math.max(0, overallDiscountAmount));

                const netAfterOverallDiscount = Math.max(0, itemsNetTotal - overallDiscountAmount);

                const isFreightCollect = freightOption && freightOption.value === 'freight_collect';
                const fqCurrencySelect = document.getElementById('fqCurrencySelect');
                const curr = fqCurrencySelect ? fqCurrencySelect.value : 'PHP';

                let serviceFeeAmount = 0;
                if (isFreightCollect) {
                    serviceFeeAmount = 50.00;
                }

                const finalTotal = netAfterOverallDiscount + serviceFeeAmount;

                const subtotalEl = document.getElementById('soSubtotal');
                const discountEl = document.getElementById('soTotalDiscount');
                const totalEl = document.getElementById('soTotal');
                const serviceFeeEl = document.getElementById('soServiceFee');
                const sym = getFQCurrencySymbol();

                let currentFeeVal = 50.00;

                if (serviceFeeEl) serviceFeeEl.textContent = sym + ' ' + currentFeeVal.toFixed(2);
                if (subtotalEl) subtotalEl.textContent = sym + ' ' + itemsNetTotal.toFixed(2);
                if (discountEl) discountEl.textContent = '- ' + sym + ' ' + overallDiscountAmount.toFixed(2);
                if (totalEl) totalEl.textContent = sym + ' ' + finalTotal.toFixed(2);
            }

            function calculateRow(row) {
                const qty = parseFloat(row.querySelector('.so-qty').value) || 0;
                const price = parseFloat(row.querySelector('.so-price').value) || 0;
                const discVal = parseFloat(row.querySelector('.so-discount-val')?.value) || 0;
                const discType = row.querySelector('.so-discount-type')?.value || 'percentage';

                const gross = qty * price;
                let dAmt = discType === 'percentage' ? gross * (discVal / 100) : discVal;
                const netSubtotal = Math.max(0, gross - dAmt);

                const sym = getFQCurrencySymbol();
                row.querySelector('.so-item-amount').textContent = sym + ' ' + netSubtotal.toFixed(2);
                calculateSOSubtotal();
            }

            if (addSOBtn && soItemsBody) {
                addSOBtn.addEventListener('click', function() {
                    if (soItemsBody.querySelectorAll('tr').length >= 24) {
                        alert('Maximum of 24 products allowed per order.');
                        return;
                    }
                    const row = document.createElement('tr');
                    const uniqueId = Date.now() + Math.random().toString(36).substring(7);
                    const sym = getFQCurrencySymbol();
                    
                    row.innerHTML = `
                        <td>
                            <input type="number" class="form-control form-control-sm so-qty" name="so_items[new_${uniqueId}][quantity]" min="1" value="1" required style="text-align: center;">
                        </td>
                        <td>
                            <select class="form-control form-control-sm so-product selectpicker" data-live-search="true" data-size="8" data-live-search-placeholder="Search product..." name="so_items[new_${uniqueId}][product_id]" required>
                                ${productSource.innerHTML}
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm so-price" name="so_items[new_${uniqueId}][price]" step="0.01" min="0" required style="text-align: right;">
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1" style="min-width: 110px;">
                                <input type="number" step="any" min="0" class="form-control form-control-sm so-discount-val text-center px-1" name="so_items[new_${uniqueId}][discount_value]" placeholder="0" style="width: 60%; font-size: 0.85rem;">
                                <select class="form-select form-select-sm so-discount-type px-1" name="so_items[new_${uniqueId}][discount_type]" style="width: 40%; font-size: 0.8rem;">
                                    <option value="percentage">%</option>
                                    <option value="amount">${sym}</option>
                                </select>
                            </div>
                        </td>
                        <td class="so-item-amount text-end fw-bold">${sym} 0.00</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger so-remove-row" title="Remove Item" style="background: #ff0000; border: none; padding: 0.35rem 0.6rem;"><i class="fas fa-trash"></i></button>
                        </td>
                    `;

                    const qtyInput = row.querySelector('.so-qty');
                    const priceInput = row.querySelector('.so-price');
                    const discValInput = row.querySelector('.so-discount-val');
                    const discTypeSelect = row.querySelector('.so-discount-type');
                    const productSelect = row.querySelector('.so-product');
                    const removeBtn = row.querySelector('.so-remove-row');

                    qtyInput.addEventListener('input', () => calculateRow(row));
                    priceInput.addEventListener('input', () => calculateRow(row));
                    discValInput.addEventListener('input', () => calculateRow(row));
                    discTypeSelect.addEventListener('change', () => calculateRow(row));
                    
                    productSelect.addEventListener('change', function() {
                        const option = this.options[this.selectedIndex];
                        const basePrice = parseFloat(option.dataset.price) || 0;
                        row.dataset.basePrice = basePrice;
                        priceInput.value = getProductPriceForSelectedCurrency(basePrice).toFixed(2);
                        calculateRow(row);
                    });

                    removeBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        row.remove();
                        calculateSOSubtotal();
                    });

                    soItemsBody.appendChild(row);

                    if (typeof $ !== 'undefined' && $.fn && $.fn.selectpicker) {
                        $(productSelect).selectpicker({
                            size: 8,
                            liveSearch: true,
                            liveSearchPlaceholder: 'Search product...',
                            dropupAuto: false
                        });
                    }

                    calculateSOSubtotal();
                });
            }

            // Customer Select & Auto-fill Logic
            const fqCustomerSelect = document.getElementById('fqCustomerSelect');
            const fqRepresentativeSelect = document.getElementById('fqRepresentativeSelect');
            const fqTermsInput = document.getElementById('fqTermsInput');
            const toggleAutofillBtn = document.getElementById('toggleAutofillBtn');

            const originContact = document.querySelector('input[name="origin_contact"]');
            const originProvince = document.querySelector('input[name="origin_province"]');
            const originAddress = document.querySelector('textarea[name="origin_address"]');
            const destinationContact = document.querySelector('input[name="destination_contact"]');
            const destinationProvince = document.querySelector('input[name="destination_province"]');
            const destinationAddress = document.querySelector('textarea[name="destination_address"]');

            let isAutofillEnabled = false;

            function populateRepresentatives(option) {
                if (!fqRepresentativeSelect) return;
                fqRepresentativeSelect.innerHTML = '<option value="">Select Representative...</option>';

                const repsData = option.getAttribute('data-representatives');
                let repsArray = [];
                if (repsData) {
                    try { repsArray = JSON.parse(repsData); } catch(e) { repsArray = []; }
                }

                if (Array.isArray(repsArray) && repsArray.length > 0) {
                    repsArray.forEach((rep, index) => {
                        const repName = rep.name || rep.rep_name;
                        if (repName) {
                            const opt = document.createElement('option');
                            opt.value = repName;
                            opt.textContent = repName;
                            if (index === 0) opt.selected = true;
                            fqRepresentativeSelect.appendChild(opt);
                        }
                    });
                }
            }

            function performAutofill() {
                if (!fqCustomerSelect) return;
                const opt = fqCustomerSelect.options[fqCustomerSelect.selectedIndex];
                if (!opt || !opt.value) return;

                if (originContact && !originContact.value) originContact.value = 'Claretian Communications Foundation Inc.';
                if (originProvince && !originProvince.value) originProvince.value = 'Metro Manila';
                if (originAddress && !originAddress.value) originAddress.value = '8 Mayumi St, UP Village, Diliman, Quezon City';

                const custName = opt.getAttribute('data-customer-name') || '';
                const repVal = fqRepresentativeSelect ? fqRepresentativeSelect.value : '';
                const phone = opt.getAttribute('data-phone') || '';
                const address = opt.getAttribute('data-address') || '';
                const province = opt.getAttribute('data-province') || '';

                let destContactStr = repVal || custName;
                if (phone) destContactStr += ' (' + phone + ')';

                if (destinationContact) destinationContact.value = destContactStr;
                if (destinationProvince) destinationProvince.value = province;
                if (destinationAddress) destinationAddress.value = address;

                const custTerms = opt.getAttribute('data-terms') || '';
                if (fqTermsInput && custTerms && !fqTermsInput.value) {
                    fqTermsInput.value = custTerms;
                    fqTermsInput.dataset.autofilled = 'true';
                }
            }

            if (fqCustomerSelect) {
                const handleCustomerChange = function() {
                    const option = fqCustomerSelect.options[fqCustomerSelect.selectedIndex];
                    if (!option) return;
                    populateRepresentatives(option);
                    const custTerms = option.getAttribute('data-terms');
                    if (custTerms && fqTermsInput && (!fqTermsInput.value || fqTermsInput.dataset.autofilled === 'true')) {
                        fqTermsInput.value = custTerms;
                        fqTermsInput.dataset.autofilled = 'true';
                    }
                    if (isAutofillEnabled) {
                        performAutofill();
                    }
                };

                fqCustomerSelect.addEventListener('change', handleCustomerChange);
                if (window.jQuery) {
                    $(fqCustomerSelect).on('changed.bs.select', handleCustomerChange);
                }
            }

            if (fqTermsInput) {
                fqTermsInput.addEventListener('input', function() {
                    delete this.dataset.autofilled;
                });
            }

            const fqTransactionType = document.getElementById('fqTransactionType');
            const fqCurrencySelect = document.getElementById('fqCurrencySelect');
            if (fqTransactionType && fqCurrencySelect) {
                fqTransactionType.addEventListener('change', function() {
                    if (this.value === 'foreign') {
                        fqCurrencySelect.value = 'USD';
                    }
                    if (this.value === 'cod' && fqTermsInput && !fqTermsInput.value) {
                        fqTermsInput.value = 'COD';
                    }
                });
            }

            if (fqRepresentativeSelect) {
                fqRepresentativeSelect.addEventListener('change', function() {
                    if (isAutofillEnabled) {
                        performAutofill();
                    }
                });
            }

            if (toggleAutofillBtn) {
                toggleAutofillBtn.addEventListener('click', function() {
                    isAutofillEnabled = !isAutofillEnabled;
                    if (isAutofillEnabled) {
                        this.className = 'btn btn-sm btn-outline-danger shadow-sm';
                        this.innerHTML = '<i class="fas fa-magic me-1"></i> <span id="autofillBtnText">Auto-fill Customer Details</span>';
                        performAutofill();
                    } else {
                        this.className = 'btn btn-sm btn-outline-secondary shadow-sm';
                        this.innerHTML = '<i class="fas fa-edit me-1"></i> <span id="autofillBtnText">Manual Input Mode</span>';
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
