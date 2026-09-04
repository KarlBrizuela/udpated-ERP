<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-plus me-2"></i>Create Sales Order from Freight Quotation</h5>
                    </div>

                    <div class="card-body">
                        <!-- Freight Quotation Summary -->
                        <div class="alert alert-info">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="mb-2"><strong>Freight Quotation Summary</strong></h6>
                                    <p class="mb-1"><strong>Quote #:</strong> {{ $quotation->quote_number }}</p>
                                    <p class="mb-1"><strong>Route:</strong> {{ $quotation->origin_province }} → {{ $quotation->destination_province }}</p>
                                    <p class="mb-0">
                                        <strong>Service:</strong> {{ $quotation->service_mode }} |
                                        <strong>Freight Option:</strong> {{ $quotation->freight_option ? ucwords(str_replace('_', ' ', $quotation->freight_option)) : 'N/A' }} |
                                        <strong>Forwarder:</strong> {{ $quotation->forwarder ?? $quotation->freight_mode ?? 'N/A' }} |
                                        <strong>Boxes:</strong> {{ $quotation->boxes_count }}
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div style="font-size: 1.5rem; font-weight: bold; color: #dc3545;">
                                        ₱ {{ number_format($quotation->total_amount, 2) }}
                                    </div>
                                    <small class="text-muted">Freight charges included in SO</small>
                                </div>
                            </div>
                        </div>

                        <form id="soForm" method="POST" action="{{ route('marketing.freight-quotations.create-so', $quotation->id) }}" enctype="multipart/form-data">
                            @csrf

                            <!-- Customer Selection -->
                            <h6 class="border-bottom pb-2 mb-3"><strong>Customer Information</strong></h6>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Customer:</label>
                                    <select class="form-control @error('customer_id') is-invalid @enderror" name="customer_id" id="customerSelect" required>
                                        <option value="" selected disabled>Select Customer...</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->customer_id }}" {{ old('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                                                {{ $customer->customer_name }} ({{ $customer->company_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Transaction Type:</label>
                                    <select class="form-control @error('type') is-invalid @enderror" name="type" required>
                                        <option value="" selected disabled>Select Type</option>
                                        <option value="paid" {{ old('type') === 'paid' ? 'selected' : '' }}>Paid Transaction</option>
                                        <option value="charge" {{ old('type') === 'charge' ? 'selected' : '' }}>Charge Transaction</option>
                                        <option value="area_consignment" {{ old('type') === 'area_consignment' ? 'selected' : '' }}>Area Consignment</option>
                                        <option value="direct_consignment" {{ old('type') === 'direct_consignment' ? 'selected' : '' }}>Direct Consignment</option>
                                        <option value="foreign" {{ old('type') === 'foreign' ? 'selected' : '' }}>Foreign Order</option>
                                        <option value="complimentary" {{ old('type') === 'complimentary' ? 'selected' : '' }}>Complimentary</option>
                                        <option value="cod" {{ old('type') === 'cod' ? 'selected' : '' }}>Due on Receipt (COD)</option>
                                <div class="col-md-4 text-end">
                                    <div style="font-size: 1.5rem; font-weight: bold; color: #dc3545;">
                                        {{ ($quotation->currency ?? 'PHP') === 'USD' ? '$' : '₱' }} {{ number_format($quotation->total_amount, 2) }}
                                    </div>
                                    <small class="text-muted">Freight charges included in SO</small>
                                </div>
                            </div>
                        </div>

                        <form id="soForm" method="POST" action="{{ route('marketing.freight-quotations.create-so', $quotation->id) }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="source" value="{{ $quotation->source }}">

                            <!-- Customer Selection -->
                            <h6 class="border-bottom pb-2 mb-3"><strong>Customer Information</strong></h6>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Customer:</label>
                                    <select class="form-control @error('customer_id') is-invalid @enderror" name="customer_id" id="customerSelect" required>
                                        <option value="" selected disabled>Select Customer...</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->customer_id }}" {{ old('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                                                {{ $customer->customer_name }} ({{ $customer->company_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Transaction Type:</label>
                                    <select class="form-control @error('type') is-invalid @enderror" name="type" required>
                                        <option value="" disabled>Select Type</option>
                                        <option value="paid" {{ old('type') === 'paid' ? 'selected' : '' }}>Paid Transaction</option>
                                        <option value="charge" {{ old('type') === 'charge' ? 'selected' : '' }}>Charge Transaction</option>
                                        <option value="area_consignment" {{ old('type') === 'area_consignment' ? 'selected' : '' }}>Area Consignment</option>
                                        <option value="direct_consignment" {{ old('type') === 'direct_consignment' ? 'selected' : '' }}>Direct Consignment</option>
                                        <option value="foreign" {{ (old('type') === 'foreign' || $quotation->source === 'ford' || ($quotation->currency ?? 'PHP') !== 'PHP') ? 'selected' : '' }}>Foreign Order</option>
                                        <option value="complimentary" {{ old('type') === 'complimentary' ? 'selected' : '' }}>Complimentary</option>
                                        <option value="cod" {{ old('type') === 'cod' ? 'selected' : '' }}>Due on Receipt (COD)</option>
                                        <option value="evaluation" {{ old('type') === 'evaluation' ? 'selected' : '' }}>Evaluation</option>
                                    </select>
                                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <!-- Sales Order Items -->
                            <h6 class="border-bottom pb-2 mb-3"><strong>Sales Order Items</strong></h6>

                            <button type="button" class="btn btn-sm btn-outline-danger mb-3" id="addItemBtn">
                                <i class="bi bi-plus me-1"></i>Add Item
                            </button>

                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-bordered" id="itemsTable">
                                    <thead class="table-danger">
                                        <tr>
                                            <th style="width: 100px;">QTY</th>
                                            <th>DESCRIPTION / PRODUCT</th>
                                            <th style="width: 130px;">UNIT PRICE</th>
                                            <th style="width: 130px;">DISCOUNT</th>
                                            <th style="width: 150px;">AMOUNT</th>
                                            <th style="width: 80px;">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsBody">
                                        <!-- Dynamic rows via JS -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Items Subtotal:</strong></td>
                                            <td class="text-end fw-bold" id="itemsSubtotal">₱ 0.00</td>
                                            <td></td>
                                        </tr>
                                        <tr class="table-warning">
                                            <td colspan="4" class="text-end"><strong>+ Freight Charges:</strong></td>
                                            <td class="text-end fw-bold text-danger">₱ {{ number_format($quotation->total_amount, 2) }}</td>
                                            <td></td>
                                        </tr>
                                        @if($quotation->freight_option === 'freight_collect')
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>+ Service Fee:</strong></td>
                                            <td class="text-end fw-bold text-danger">₱ 50.00</td>
                                            <td></td>
                                        </tr>
                                        @endif
                                        <tr class="table-danger">
                                            <td colspan="4" class="text-end"><strong>Total Amount:</strong></td>
                                            <td class="text-end fw-bold" id="grandTotal" style="font-size: 1.1rem;">₱ 0.00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            @error('items')<div class="alert alert-danger">{{ $message }}</div>@enderror

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 justify-content-between mt-4">
                                <a href="{{ route('marketing.freight-quotations.show', $quotation->id) }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Back to Quotation
                                </a>
                                <button type="submit" class="btn btn-primary" id="saveBtn">
                                    <i class="bi bi-check-circle me-1"></i>Create Sales Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden product template -->
    <select id="productSource" class="d-none">
        <option value="" disabled selected>Select Product...</option>
        @if(isset($products))
            @foreach($products as $product)
                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                    {{ $product->display_name ?? $product->name }}
                </option>
            @endforeach
        @endif
    </select>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addItemBtn = document.getElementById('addItemBtn');
            const itemsBody = document.getElementById('itemsBody');
            const grandTotalEl = document.getElementById('grandTotal');
            const itemsSubtotalEl = document.getElementById('itemsSubtotal');
            const freightTotal = parseFloat('{{ $quotation->total_amount }}');
            const serviceFee = '{{ $quotation->freight_option }}' === 'freight_collect' ? 50 : 0;
            const productSource = document.getElementById('productSource');

            function calculateRow(row) {
                const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
                const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
                const discountVal = parseFloat(row.querySelector('.discount-input')?.value) || 0;
                const discountType = row.querySelector('.discount-type-select')?.value || 'percentage';

                const gross = qty * price;
                let dAmt = discountType === 'percentage' ? gross * (discountVal / 100) : discountVal;
                const netSubtotal = Math.max(0, gross - dAmt);

                row.querySelector('.subtotal-display').textContent = '₱ ' + netSubtotal.toFixed(2);
                updateGrandTotal();
            }

            function updateGrandTotal() {
                let itemsTotal = 0;
                document.querySelectorAll('.subtotal-display').forEach(el => {
                    itemsTotal += parseFloat(el.textContent.replace('₱ ', '')) || 0;
                });
                itemsSubtotalEl.textContent = '₱ ' + itemsTotal.toFixed(2);
                const total = itemsTotal + freightTotal + serviceFee;
                grandTotalEl.textContent = '₱ ' + total.toFixed(2);
            }

            function addRow(data = null) {
                const tr = document.createElement('tr');
                const uniqueId = Date.now() + Math.random().toString(36).substring(7);
                
                const qtyVal = data ? data.quantity : 1;
                const productId = data ? data.product_id : '';
                const priceVal = data ? data.price : '';
                const discountVal = data ? (parseFloat(data.discount_value) || 0) : 0;
                const discountTypeVal = data ? (data.discount_type || 'percentage') : 'percentage';

                let subtotalVal = 0;
                if (data) {
                    const gross = (parseFloat(data.quantity) || 0) * (parseFloat(data.price) || 0);
                    const dAmt = discountTypeVal === 'percentage' ? gross * (discountVal / 100) : discountVal;
                    subtotalVal = Math.max(0, gross - dAmt);
                }

                tr.innerHTML = `
                    <td>
                        <input type="number" class="form-control form-control-sm qty-input" 
                               name="items[new_${uniqueId}][quantity]" min="1" value="${qtyVal}" required style="text-align: center;">
                    </td>
                    <td>
                        <select class="form-control form-control-sm product-select" name="items[new_${uniqueId}][product_id]" required>
                            ${productSource.innerHTML}
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm price-input" 
                               name="items[new_${uniqueId}][price]" step="0.01" value="${priceVal}" required style="text-align: right;">
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            <input type="number" step="any" min="0" class="form-control form-control-sm discount-input" 
                                   name="items[new_${uniqueId}][discount_value]" value="${discountVal > 0 ? discountVal : ''}" placeholder="0" style="text-align: right; width: 55%;">
                            <select class="form-select form-select-sm discount-type-select px-1" name="items[new_${uniqueId}][discount_type]" style="width: 45%; font-size: 0.8rem;">
                                <option value="percentage" ${discountTypeVal === 'percentage' ? 'selected' : ''}>%</option>
                                <option value="amount" ${discountTypeVal === 'amount' ? 'selected' : ''}>₱</option>
                            </select>
                        </div>
                    </td>
                    <td class="subtotal-display fw-bold text-end">₱ ${subtotalVal.toFixed(2)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-row" title="Remove Item" style="background: #ff0000; border: none;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                
                const qtyInput = tr.querySelector('.qty-input');
                const priceInput = tr.querySelector('.price-input');
                const discountInput = tr.querySelector('.discount-input');
                const discountTypeSelect = tr.querySelector('.discount-type-select');
                const productSelect = tr.querySelector('.product-select');
                const removeBtn = tr.querySelector('.remove-row');

                if (productId) {
                    productSelect.value = productId;
                }

                productSelect.addEventListener('change', function() {
                    const option = this.options[this.selectedIndex];
                    if (option && option.dataset.price) {
                        priceInput.value = option.dataset.price;
                    }
                    calculateRow(tr);
                });

                qtyInput.addEventListener('input', () => calculateRow(tr));
                priceInput.addEventListener('input', () => calculateRow(tr));
                discountInput.addEventListener('input', () => calculateRow(tr));
                discountTypeSelect.addEventListener('change', () => calculateRow(tr));
                
                removeBtn.addEventListener('click', function() {
                    tr.remove();
                    updateGrandTotal();
                });

                itemsBody.appendChild(tr);

                if (typeof $ !== 'undefined' && $.fn && $.fn.selectpicker) {
                    $(productSelect).selectpicker({
                        size: 8,
                        liveSearch: true,
                        liveSearchPlaceholder: 'Search product...'
                    });
                }

                updateGrandTotal();
            }

            addItemBtn.addEventListener('click', () => {
                if (itemsBody.querySelectorAll('tr').length >= 24) {
                    alert('Maximum of 24 products allowed per order.');
                    return;
                }
                addRow();
            });

            // Form submission
            const soForm = document.getElementById('soForm');
            const saveBtn = document.getElementById('saveBtn');

            soForm.addEventListener('submit', function(e) {
                if (itemsBody.querySelectorAll('tr').length === 0) {
                    e.preventDefault();
                    alert('Please add at least one item to the order.');
                    return;
                }

                let allSelected = true;
                $(itemsBody).find('.product-select').each(function() {
                    if (!$(this).val()) {
                        allSelected = false;
                    }
                });

                if (!allSelected) {
                    e.preventDefault();
                    alert('Please select a product for all rows.');
                    return;
                }

                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="las la-spinner la-spin me-2"></i> Creating...';
            });
        });
    </script>
    @endpush
</x-app-layout>
