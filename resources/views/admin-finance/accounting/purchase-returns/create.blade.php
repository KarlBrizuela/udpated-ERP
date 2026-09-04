<x-app-layout :title="'Record Purchase Return'" :sidebar="$sidebar ?? 'admin-finance'" :role="$role ?? 'Finance Manager'">
    @push('styles')
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        /* Select2 Custom Styles to match Claretian UI Theme */
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
            color: #000000 !important;
            font-size: 0.85rem !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
        .select2-dropdown {
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            z-index: 9999 !important;
        }
        .select2-container {
            width: 100% !important;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            outline: none !important;
            padding: 4px 8px !important;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #D9251C !important;
        }

        /* Widescreen Spacing Override */
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
            padding-bottom: 80px !important;
        }

        /* Label Styling */
        .form-label-custom {
            color: #475569 !important;
            text-transform: uppercase !important;
            font-weight: 600 !important;
            font-size: 0.72rem !important;
            letter-spacing: 0.5px !important;
            margin-bottom: 6px !important;
            display: inline-block;
        }

        /* Input and Select Controls */
        .form-control-custom {
            border-color: #cbd5e1 !important;
            border-radius: 6px !important;
            color: #000000 !important;
            height: 38px;
            font-size: 0.85rem !important;
            box-shadow: none !important;
            transition: all 0.15s ease-in-out !important;
        }
        .form-control-custom:focus {
            border-color: #D9251C !important;
            box-shadow: 0 0 0 0.2rem rgba(217, 37, 28, 0.15) !important;
        }

        .form-textarea-custom {
            border-color: #cbd5e1 !important;
            border-radius: 6px !important;
            color: #000000 !important;
            font-size: 0.85rem !important;
            box-shadow: none !important;
            transition: all 0.15s ease-in-out !important;
        }
        .form-textarea-custom:focus {
            border-color: #D9251C !important;
            box-shadow: 0 0 0 0.2rem rgba(217, 37, 28, 0.15) !important;
        }

        /* Modern Items Table */
        .table-items {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        .table-items thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
            font-size: 0.72rem !important;
            padding: 12px 16px !important;
            border-bottom: 2px solid #e2e8f0 !important;
        }
        .table-items tbody td {
            padding: 12px 16px !important;
            font-size: 0.84rem !important;
            color: #475569 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            vertical-align: middle !important;
        }
    </style>
    @endpush

    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <a href="{{ route('admin-finance.accounting.purchase-returns.index') }}" class="btn btn-sm btn-light border mb-4 fw-bold text-dark">
                    <i class="las la-arrow-left me-1"></i> Back to Logs
                </a>

                <div class="card border-0 shadow-sm" style="border-radius: 8px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h4 class="fs-18 mb-0 fw-bold text-dark" style="letter-spacing: -0.5px;">Record Purchase Return</h4>
                        <p class="text-muted small mb-0">Record items returned to a supplier, adjust payable outstanding, and update warehouse stocks.</p>
                    </div>
                    <div class="card-body" style="padding-bottom: 40px !important;">
                        <form action="{{ route('admin-finance.accounting.purchase-returns.store') }}" method="POST" id="purchaseReturnForm">
                            @csrf
                            
                            <!-- Header Form Information -->
                            <div class="row mb-4">
                                <div class="col-md-6 col-lg-5 mb-3">
                                    <label for="supplier_invoice_id" class="form-label form-label-custom">Select Supplier Invoice <span class="text-danger">*</span></label>
                                    <select name="supplier_invoice_id" id="supplier_invoice_id" class="form-select form-control-custom" required>
                                        <option value="">-- Select Active Invoice --</option>
                                        @foreach($invoices as $invoice)
                                            <option value="{{ $invoice->id }}" {{ old('supplier_invoice_id') == $invoice->id ? 'selected' : '' }}>
                                                Invoice #: {{ $invoice->invoice_number }} - {{ $invoice->supplier->name ?? 'N/A' }} (₱{{ number_format($invoice->total_amount, 2) }} | Bal: ₱{{ number_format($invoice->balance, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <label for="return_date" class="form-label form-label-custom">Return Date <span class="text-danger">*</span></label>
                                    <input type="date" name="return_date" id="return_date" class="form-control form-control-custom" value="{{ old('return_date', date('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <label class="form-label form-label-custom d-block">Inventory Adjustment</label>
                                    <div class="form-check form-switch pt-2">
                                        <input class="form-check-input" type="checkbox" name="inventory_deducted" id="inventory_deducted" value="1" checked disabled style="cursor: not-allowed; width: 45px; height: 22px;">
                                        <label class="form-check-label text-muted small ms-2" for="inventory_deducted">Always auto-deduct returned stock from warehouse</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Supplier Info Preview -->
                            <div id="supplier_info_card" class="alert alert-light border d-none py-3 mb-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <span class="text-muted small uppercase fw-bold">Supplier:</span>
                                        <p id="preview_supplier_name" class="fw-bold text-dark mb-0"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted small uppercase fw-bold">Supplier Invoice Ref:</span>
                                        <p id="preview_invoice_number" class="fw-bold text-dark mb-0"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Items List Table -->
                            <div id="items_section" class="d-none mb-4">
                                <h5 class="fw-bold text-dark mb-3">Returned Items details</h5>
                                <div class="table-responsive border rounded">
                                    <table class="table table-items mb-0">
                                        <thead>
                                            <tr>
                                                <th>Book / Item Title</th>
                                                <th class="text-center" style="width: 120px;">Received Qty</th>
                                                <th class="text-center" style="width: 120px;">Already Returned</th>
                                                <th class="text-center" style="width: 120px;">Available Qty</th>
                                                <th class="text-end" style="width: 140px;">Unit Cost</th>
                                                <th class="text-center" style="width: 160px;">Return Qty <span class="text-danger">*</span></th>
                                                <th class="text-end" style="width: 150px;">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody id="items_table_body">
                                            <!-- Dynamically populated -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="6" class="text-end fw-bold text-dark pt-3">Total Return Refund:</td>
                                                <td class="text-end fw-bold fs-16 text-danger pt-3" id="total_refund_preview">₱0.00</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="mb-4">
                                <label for="notes" class="form-label form-label-custom">Notes / Reason for Return</label>
                                <textarea name="notes" id="notes" rows="3" class="form-control form-textarea-custom" placeholder="e.g. Defective stock, supplier delivery error, etc.">{{ old('notes') }}</textarea>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-end gap-2 border-top pt-4 mb-2">
                                <a href="{{ route('admin-finance.accounting.purchase-returns.index') }}" class="btn btn-light border px-4 d-inline-flex align-items-center justify-content-center" style="height: 38px; border-radius: 4px; font-weight: 600;">Cancel</a>
                                <button type="submit" class="btn text-white px-4 d-inline-flex align-items-center justify-content-center" id="submitBtn" disabled style="background-color: #D9251C; height: 38px; border-radius: 4px; font-weight: 600; box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15);">
                                    Post Purchase Return
                                </button>
                            </div>
                            <div style="height: 25px;"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectInvoice = document.getElementById('supplier_invoice_id');
            const supplierInfoCard = document.getElementById('supplier_info_card');
            const previewSupplier = document.getElementById('preview_supplier_name');
            const previewInvoice = document.getElementById('preview_invoice_number');
            const itemsSection = document.getElementById('items_section');
            const itemsTableBody = document.getElementById('items_table_body');
            const totalRefundPreview = document.getElementById('total_refund_preview');
            const submitBtn = document.getElementById('submitBtn');
            const form = document.getElementById('purchaseReturnForm');

            function handleInvoiceChange(invoiceId) {
                if (!invoiceId) {
                    supplierInfoCard.classList.add('d-none');
                    itemsSection.classList.add('d-none');
                    submitBtn.disabled = true;
                    return;
                }

                // Show loading state
                itemsTableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-danger spinner-border-sm" role="status"></div> Loading invoice items...</td></tr>`;
                supplierInfoCard.classList.remove('d-none');
                itemsSection.classList.remove('d-none');

                fetch(`{{ route('admin-finance.accounting.purchase-returns.create') }}?supplier_invoice_id=${invoiceId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    // Set headers
                    previewSupplier.textContent = data.supplier;
                    previewInvoice.textContent = data.invoice_number;

                    // Build items rows
                    let rowsHtml = '';
                    if (data.items.length === 0) {
                        rowsHtml = `<tr><td colspan="7" class="text-center py-4 text-muted">No returnable items found in this invoice's source orders/receipts.</td></tr>`;
                        submitBtn.disabled = true;
                    } else {
                        data.items.forEach((item, index) => {
                            const isAvailable = item.available_qty > 0;
                            rowsHtml += `
                                <tr>
                                    <td>
                                        <span class="fw-semibold text-dark">${item.title}</span>
                                        <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                                        <input type="hidden" name="items[${index}][unit_cost]" value="${item.unit_cost}">
                                    </td>
                                    <td class="text-center">${item.original_qty}</td>
                                    <td class="text-center text-muted">${item.previously_returned}</td>
                                    <td class="text-center font-w600 ${isAvailable ? 'text-dark' : 'text-danger'}">${item.available_qty}</td>
                                    <td class="text-end">₱${parseFloat(item.unit_cost).toFixed(2)}</td>
                                    <td class="text-center">
                                        <input type="number" 
                                               name="items[${index}][returned_qty]" 
                                               class="form-control form-control-custom text-center return-qty-input" 
                                               value="0" 
                                               min="0" 
                                               max="${item.available_qty}" 
                                               data-price="${item.unit_cost}" 
                                               style="width: 100px; margin: 0 auto;"
                                               ${!isAvailable ? 'disabled' : ''}>
                                    </td>
                                    <td class="text-end fw-bold text-dark subtotal-cell">₱0.00</td>
                                </tr>
                             `;
                        });
                        submitBtn.disabled = false;
                    }
                    itemsTableBody.innerHTML = rowsHtml;
                    attachInputListeners();
                    calculateTotalRefund();
                })
                .catch(error => {
                    console.error('Error fetching invoice details:', error);
                    itemsTableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger"><i class="las la-exclamation-triangle"></i> Failed to load items.</td></tr>`;
                });
            }

            // Initialize Select2
            if (window.jQuery && typeof jQuery.fn.select2 === 'function') {
                $(selectInvoice).select2({
                    placeholder: '-- Select Active Invoice --',
                    allowClear: true
                }).on('change', function () {
                    handleInvoiceChange(this.value);
                });
            } else {
                selectInvoice.addEventListener('change', function () {
                    handleInvoiceChange(this.value);
                });
            }

            function attachInputListeners() {
                const inputs = document.querySelectorAll('.return-qty-input');
                inputs.forEach(input => {
                    input.addEventListener('input', function () {
                        // Validate bounds
                        const val = parseInt(this.value) || 0;
                        const maxVal = parseInt(this.getAttribute('max')) || 0;
                        if (val < 0) this.value = 0;
                        if (val > maxVal) this.value = maxVal;

                        // Calculate row subtotal
                        const price = parseFloat(this.getAttribute('data-price')) || 0;
                        const qty = parseInt(this.value) || 0;
                        const subtotal = qty * price;

                        const row = this.closest('tr');
                        row.querySelector('.subtotal-cell').textContent = `₱${subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

                        calculateTotalRefund();
                    });
                });
            }

            function calculateTotalRefund() {
                const inputs = document.querySelectorAll('.return-qty-input');
                let grandTotal = 0;
                let hasReturnedItems = false;

                inputs.forEach(input => {
                    const qty = parseInt(input.value) || 0;
                    const price = parseFloat(input.getAttribute('data-price')) || 0;
                    grandTotal += (qty * price);
                    if (qty > 0) {
                        hasReturnedItems = true;
                    }
                });

                totalRefundPreview.textContent = `₱${grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                
                // Only enable submission if at least one item quantity is > 0
                submitBtn.disabled = !hasReturnedItems;
            }

            form.addEventListener('submit', function (e) {
                const inputs = document.querySelectorAll('.return-qty-input');
                let hasReturnedItems = false;
                inputs.forEach(input => {
                    if ((parseInt(input.value) || 0) > 0) {
                        hasReturnedItems = true;
                    }
                });

                if (!hasReturnedItems) {
                    e.preventDefault();
                    alert("Please enter a return quantity of at least 1 for one of the items.");
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
