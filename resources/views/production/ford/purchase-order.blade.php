<x-app-layout :title="'Purchase Order'" :sidebar="'production'">
    <div class="container-fluid">
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
                        <div class="document-title">PURCHASE ORDER</div>
                    </div>

                    <form id="purchaseOrderForm" class="form-section">
                        <!-- Order Details -->
                        <div class="customer-section">
                            <div class="order-details">
                                <h5>Order Information</h5>
                                <div class="form-group">
                                    <label>Date:</label>
                                    <input type="date" name="date" id="formDate">
                                </div>
                                <div class="form-group">
                                    <label>PO No.:</label>
                                    <input type="text" name="po_number" id="formPONumber" placeholder="Enter PO number">
                                </div>
                                <div class="form-group">
                                    <label>Terms:</label>
                                    <input type="text" name="terms" id="formTerms" placeholder="e.g., CHARGE" value="CHARGE">
                                </div>
                                <div class="form-group">
                                    <label>Payment Schedule:</label>
                                    <input type="text" name="payment_schedule" id="formPaymentSchedule" placeholder="e.g., BY TT, ONE YEAR TO PAY" value="BY TT, ONE YEAR TO PAY">
                                </div>
                                <div class="form-group">
                                    <label>Payment Schedule (Line 2):</label>
                                    <input type="text" name="payment_schedule2" id="formPaymentSchedule2" placeholder="e.g., IN 12 EQUAL MONTHLY INSTALLMENTS" value="IN 12 EQUAL MONTHLY INSTALLMENTS">
                                </div>
                                <div class="form-group">
                                    <label>Currency:</label>
                                    <select id="formCurrency" class="form-control" onchange="updateCurrencyDisplay()">
                                        <option value="PHP">₱ Philippine Peso (PHP)</option>
                                        <option value="USD" selected>$ US Dollar (USD)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Supplier <span class="text-danger">*</span> <small class="text-muted">(required to save to system)</small>:</label>
                                    <select id="formSupplierId" class="form-control">
                                        <option value="">-- Select Supplier --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="customer-details">
                                <h5>Vendor Information</h5>
                                <div class="form-group">
                                    <label>Vendor Name:</label>
                                    <input type="text" name="vendor_name" id="formVendorName" placeholder="Enter vendor name">
                                </div>
                                <div class="form-group">
                                    <label>Contact Persons:</label>
                                    <input type="text" name="contact_persons" id="formContactPersons" placeholder="e.g., Fr. Alberto Rossa, CMF / Ms. Divine de Leon">
                                </div>
                                <div class="form-group">
                                    <label>Address:</label>
                                    <textarea name="vendor_address" id="formVendorAddress" placeholder="Enter vendor address"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <button type="button" class="btn-add-row" onclick="addRow()">
                            <i class="las la-plus"></i> Add Row
                        </button>

                        <table class="form-table" id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">LANGUAGE</th>
                                    <th style="width: 60px;">FT</th>
                                    <th>DESCRIPTION</th>
                                    <th style="width: 80px;">QUANTITY</th>
                                    <th style="width: 100px;" id="formUnitPriceHeader">UNIT PRICE (USD)</th>
                                    <th style="width: 100px;">TOTAL AMOUNT</th>
                                    <th style="width: 100px;">BINDINGS</th>
                                    <th style="width: 150px;">REMARKS</th>
                                    <th style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <tr>
                                    <td><input type="text" name="language[]" placeholder="Language"></td>
                                    <td><input type="text" name="ft[]" placeholder="FT"></td>
                                    <td>
                                        <select class="form-control selectpicker select-product" name="product_id[]" data-live-search="true" onchange="onProductSelected(this)" required>
                                            <option value="" disabled selected>-- Select Book --</option>
                                            <option value="new_custom_book" style="font-weight: bold; color: #007bff;">+ New Book / Custom Title...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->book->cost ?? $product->price }}">
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="custom-title-container mt-1" style="display: none;">
                                            <input type="text" class="form-control custom-title-input" placeholder="Type custom book title..." oninput="onCustomTitleInput(this)">
                                        </div>
                                        <input type="hidden" name="description[]" class="product-name-input">
                                    </td>
                                    <td><input type="number" name="quantity[]" placeholder="Qty" min="0" oninput="calculateRowTotal(this)"></td>
                                    <td><input type="number" name="unit_price[]" placeholder="Unit Price" min="0" step="0.01" oninput="calculateRowTotal(this)"></td>
                                    <td><input type="number" name="total_amount[]" placeholder="Total" readonly></td>
                                    <td><input type="text" name="bindings[]" placeholder="Bindings"></td>
                                    <td><input type="text" name="remarks[]" placeholder="Remarks"></td>
                                    <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Hidden Book Options for JS -->
                        <select id="productSource" class="d-none">
                            <option value="" disabled selected>-- Select Book --</option>
                            <option value="new_custom_book" style="font-weight: bold; color: #007bff;">+ New Book / Custom Title...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->book->cost ?? $product->price }}">
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn btn-light" onclick="resetGeneratedPO()">
                                <i class="las la-undo"></i> Reset
                            </button>
                            <button type="button" class="btn btn-primary" onclick="updateGeneratedPO()">
                                <i class="las la-check"></i> Generate Purchase Order
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Hidden form that POSTs to the server to save the PO -->
                <form id="savePOForm" action="{{ route('production.ford.purchase-order.store') }}" method="POST" style="display:none">
                    @csrf
                    <input type="hidden" name="supplier_id" id="saveSupplier">
                    <input type="hidden" name="po_number" id="savePONumber">
                    <input type="hidden" name="date" id="saveDate">
                    <input type="hidden" name="terms" id="saveTerms">
                    <input type="hidden" name="vendor_name" id="saveVendorName">
                    <input type="hidden" name="contact_persons" id="saveContactPersons">
                    <input type="hidden" name="vendor_address" id="saveVendorAddress">
                    <input type="hidden" name="payment_schedule" id="savePaymentSchedule">
                    <input type="hidden" name="payment_schedule2" id="savePaymentSchedule2">
                    <input type="hidden" name="currency" id="saveCurrency">
                    <div id="saveItemsContainer"></div>
                </form>

                <!-- Generated PO Section -->
                <div class="generated-po-section" id="generatedPOSection" style="display: none;">
                    <div class="d-flex gap-2 mb-3 report-actions">
                        <button type="button" class="btn btn-secondary" onclick="backToForm()">
                            <i class="las la-arrow-left"></i> Back
                        </button>
                        <button type="button" class="btn btn-success" onclick="printReport()">
                            <i class="las la-print"></i> Print PO
                        </button>
                        <button type="button" class="btn btn-primary" onclick="savePOToSystem()" id="savePOBtn">
                            <i class="las la-save"></i> Save &amp; Send to Logistics
                        </button>
                    </div>
                    <div class="generated-po" id="generatedPO">
                        <div class="company-header">
                            <h2>CLARETIAN COMMUNICATIONS FOUNDATION INC.</h2>
                            <p>8 Mayumi St., U.P. Village, Diliman 1101 Quezon City, Philippines</p>
                            <p>C.P.O. Box 4 U.P. Diliman 1101 Quezon City, Philippines</p>
                            <p>Tel.: 921-3984 • Fax: 921-6205</p>
                        </div>

                        <div class="po-title">PURCHASE ORDER</div>

                        <div class="po-details">
                            <div class="po-details-left">
                                <div class="vendor-section">
                                    <div style="margin-bottom: 5px;">TO: <strong id="reportVendorName">____________________</strong></div>
                                    <div style="margin-left: 30px;">
                                        <div id="reportVendorAddress">_______________________</div>
                                        <div><strong id="reportContactPersons">____________________</strong></div>
                                    </div>
                                </div>
                            </div>
                            <div class="po-details-right">
                                <div style="margin-bottom: 5px;"><strong>DATE:</strong> <span id="reportDate">_____________</span></div>
                                <div style="margin-bottom: 5px;"><strong>PO NO.:</strong> <span id="reportPONumber">_____________</span></div>
                                <div style="margin-bottom: 5px;"><strong>TERMS:</strong> <span id="reportTerms">_____________</span></div>
                            </div>
                        </div>

                        <div style="margin-bottom: 10px;">
                            Please supply us with the following items, to be delivered to our office.
                        </div>

                        <table class="po-table">
                            <thead>
                                <tr>
                                    <th class="language-col">LANGUAGE</th>
                                    <th class="ft-col">FT</th>
                                    <th class="description-col">DESCRIPTION</th>
                                    <th class="quantity-col">QUANTITY</th>
                                    <th class="unit-price-col" id="reportUnitPriceHeader">UNIT PRICE (USD)</th>
                                    <th class="total-amount-col">TOTAL AMOUNT</th>
                                    <th class="bindings-col">BINDINGS</th>
                                    <th class="remarks-col">REMARKS</th>
                                </tr>
                            </thead>
                            <tbody id="reportItemsTableBody">
                                <!-- Rows injected here -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" style="text-align: right; border: 1px solid #000; padding: 5px; font-weight: bold;">TOTAL:</td>
                                    <td style="text-align: right; border: 1px solid #000; padding: 5px; font-weight: bold;" id="reportTotalAmount">0.00</td>
                                    <td colspan="2" style="border: 1px solid #000; padding: 5px;"></td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="po-footer">
                            <div class="po-footer-row">
                                <strong>PAYMENT SCHEDULE:</strong> <span id="reportPaymentSchedule">_______________________</span>
                            </div>
                            <div class="po-footer-row" style="padding-left: 155px;">
                                <span id="reportPaymentSchedule2">_______________________</span>
                            </div>
                            <div class="po-footer-row" style="margin-top: 15px;">
                                <strong>NOTE:</strong> KINDLY SECURE US THE NECESSARY COMMERCIAL INVOICE.
                            </div>
                        </div>

                        <div class="signature-section">
                            <div class="signature-row" style="display: flex; justify-content: space-between;">
                                <div class="signature-item" style="width: 40%;">
                                    <div style="color:red; font-weight:bold; font-style:italic;">Please sign & email / fax back</div>
                                    <div class="signature-line" style="border-top: 1px solid #000; margin-top: 40px; padding-top: 5px; text-align: center;">
                                        <strong>Signature over printed name</strong>
                                    </div>
                                </div>
                                <div class="signature-item text-right" style="width: 40%; text-align: right;">
                                    <div>Sincerely,</div>
                                    <div class="signature-line" style="border-top: 1px solid #000; margin-top: 40px; padding-top: 5px; text-align: center;">
                                        <strong>FR. LOUIE GUADES III,CMF</strong><br>
                                        Director Treasurer
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('styles')
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <style>
        /* Marketing Division Form Styles */
        .order-form {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .form-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }

        .form-header .company-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-header .company-logo {
            width: 60px;
            height: 60px;
            background: #ff0000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2rem;
            font-weight: bold;
            flex-shrink: 0;
        }

        .form-header .company-details {
            flex: 1;
        }

        .form-header .company-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
        }

        .form-header .company-address {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.1rem;
        }

        .form-header .company-contact {
            font-size: 0.9rem;
            color: #666;
        }

        .form-header .document-title {
            text-align: center;
            font-size: 1.75rem;
            font-weight: 700;
            color: #333;
            margin-top: 1rem;
            letter-spacing: 1px;
        }

        .customer-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 1.5rem;
        }

        .customer-details,
        .order-details {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
        }

        .customer-details h5,
        .order-details h5 {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 0.75rem;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
            display: block;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
            font-size: 0.9rem;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }

        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        .form-table thead {
            background: #ff0000;
            color: #fff;
        }

        .form-table th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid #ddd;
        }

        .form-table td {
            padding: 0.5rem;
            border: 1px solid #ddd;
        }

        .form-table input[type="text"],
        .form-table input[type="number"] {
            width: 100%;
            border: none;
            padding: 0.5rem;
            background: transparent;
        }

        .form-table input[type="number"] {
            text-align: right;
        }

        .form-table input:focus {
            outline: 2px solid #ff0000;
            outline-offset: -2px;
            background: #fff;
        }

        .form-table tfoot {
            background: #f8f9fa;
            font-weight: 600;
        }

        .form-table tfoot td {
            padding: 0.75rem;
            border-top: 2px solid #333;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e0e0e0;
        }

        .btn-add-row {
            background: #ff0000;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-add-row:hover {
            background: #ff6666;
        }

        .btn-remove-row {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
        }

        .btn-remove-row:hover {
            background: #c82333;
        }

        /* Generated PO Section Styles (unchanged) */
        .generated-po-section {
            max-width: 1200px;
            margin: 0;
        }
        .generated-po {
            background: #fff;
            border: 1px solid #ddd;
            padding: 40px;
            margin-top: 30px;
            min-height: 800px;
            font-family: 'Times New Roman', serif;
        }
        .po-header {
            margin-bottom: 30px;
        }
        .company-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .company-header h2 {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .company-header p {
            margin: 2px 0;
            font-size: 12px;
        }
        .po-title {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin: 20px 0;
        }
        .po-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .po-details-left {
            width: 60%;
        }
        .po-details-right {
            width: 35%;
            text-align: right;
        }
        .vendor-section {
            margin: 20px 0;
        }
        .vendor-section strong {
            display: inline-block;
            width: 120px;
        }
        .po-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }
        .po-table th,
        .po-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        .po-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .po-table input {
            width: 100%;
            border: none;
            background: transparent;
            padding: 3px;
            font-size: 11px;
        }
        .po-table .language-col {
            width: 100px;
        }
        .po-table .ft-col {
            width: 60px;
        }
        .po-table .description-col {
            width: 200px;
        }
        .po-table .quantity-col {
            width: 80px;
            text-align: center;
        }
        .po-table .unit-price-col {
            width: 100px;
            text-align: right;
        }
        .po-table .total-amount-col {
            width: 100px;
            text-align: right;
        }
        .po-table .bindings-col {
            width: 100px;
        }
        .po-table .remarks-col {
            width: 150px;
        }
        .po-footer {
            margin-top: 30px;
        }
        .po-footer-row {
            margin-bottom: 10px;
        }
        .po-footer-row strong {
            display: inline-block;
            width: 150px;
        }
        .signature-section {
            margin-top: 40px;
        }
        .signature-box {
            margin-bottom: 30px;
        }
        .signature-box strong {
            display: block;
            margin-bottom: 10px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
        }
        .signature-row {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .signature-item {
            width: 30%;
        }
        .blank-field {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 200px;
            padding: 0 5px;
            margin: 0 5px;
        }

        @media print {
            @page {
                size: landscape;
            }
            .sidebar,
            .header,
            .form-actions,
            .report-actions,
            .btn-add-row {
                display: none !important;
            }

            .order-form {
                display: none !important;
                box-shadow: none;
            }

            body {
                background-color: #fff;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        let rowCounter = 1;

        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            return months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
        }

        function calculateRowTotal(input) {
            const row = input.closest('tr');
            const qty = parseFloat(row.querySelector('input[name="quantity[]"]').value) || 0;
            const price = parseFloat(row.querySelector('input[name="unit_price[]"]').value) || 0;
            const total = qty * price;
            
            row.querySelector('input[name="total_amount[]"]').value = total > 0 ? total.toFixed(2) : '';
        }

        function onProductSelected(selectEl) {
            const option = selectEl.options[selectEl.selectedIndex];
            const row = selectEl.closest('tr');
            const customContainer = row.querySelector('.custom-title-container');
            const customInput = row.querySelector('.custom-title-input');
            const priceInput = row.querySelector('input[name="unit_price[]"]');
            const descInput = row.querySelector('.product-name-input');

            if (option) {
                const val = option.value;
                if (val === 'new_custom_book') {
                    customContainer.style.display = 'block';
                    customInput.required = true;
                    if (descInput) descInput.value = customInput.value;
                    if (priceInput) priceInput.value = '0.00';
                } else {
                    customContainer.style.display = 'none';
                    customInput.required = false;
                    customInput.value = '';
                    const price = option.getAttribute('data-price') || 0;
                    const name = option.getAttribute('data-name') || '';
                    if (priceInput) priceInput.value = price;
                    if (descInput) descInput.value = name;
                }
                
                if (priceInput) {
                    calculateRowTotal(priceInput);
                }
            }
        }

        function onCustomTitleInput(inputEl) {
            const row = inputEl.closest('tr');
            const descInput = row.querySelector('.product-name-input');
            if (descInput) {
                descInput.value = inputEl.value;
            }
        }

        function addRow() {
            rowCounter++;
            const tbody = document.getElementById('itemsTableBody');
            const newRow = document.createElement('tr');
            
            const productSource = document.getElementById('productSource');
            const optionsHtml = productSource ? productSource.innerHTML : '<option value="" disabled>No products available</option>';

            newRow.innerHTML = `
                <td><input type="text" name="language[]" placeholder="Language"></td>
                <td><input type="text" name="ft[]" placeholder="FT"></td>
                <td>
                    <select class="form-control selectpicker select-product" name="product_id[]" data-live-search="true" onchange="onProductSelected(this)" required style="width: 100%;">
                        ${optionsHtml}
                    </select>
                    <div class="custom-title-container mt-1" style="display: none;">
                        <input type="text" class="form-control custom-title-input" placeholder="Type custom book title..." oninput="onCustomTitleInput(this)">
                    </div>
                    <input type="hidden" name="description[]" class="product-name-input">
                </td>
                <td><input type="number" name="quantity[]" placeholder="Qty" min="0" oninput="calculateRowTotal(this)"></td>
                <td><input type="number" name="unit_price[]" placeholder="Unit Price" min="0" step="0.01" oninput="calculateRowTotal(this)"></td>
                <td><input type="number" name="total_amount[]" placeholder="Total" readonly></td>
                <td><input type="text" name="bindings[]" placeholder="Bindings"></td>
                <td><input type="text" name="remarks[]" placeholder="Remarks"></td>
                <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
            `;
            tbody.appendChild(newRow);

            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.selectpicker === 'function') {
                jQuery(newRow).find('.selectpicker').selectpicker('render');
            }
        }

        function removeRow(button) {
            const tbody = document.getElementById('itemsTableBody');
            if (tbody.rows.length > 1) {
                button.closest('tr').remove();
            } else {
                alert('At least one row is required.');
            }
        }

        function updateCurrencyDisplay() {
            const currency = document.getElementById('formCurrency').value;
            const label = currency === 'PHP' ? 'UNIT PRICE (PHP)' : 'UNIT PRICE (USD)';
            const formHeader = document.getElementById('formUnitPriceHeader');
            const reportHeader = document.getElementById('reportUnitPriceHeader');
            if (formHeader) formHeader.textContent = label;
            if (reportHeader) reportHeader.textContent = label;
        }

        function updateGeneratedPO() {
            const date = document.getElementById('formDate').value;
            const vendorName = document.getElementById('formVendorName').value;
            const poNumber = document.getElementById('formPONumber').value;

            if (!date || !vendorName || !poNumber) {
                if(window.showAlert) window.showAlert('Please enter the Date, PO Number, and Vendor Name.', 'warning');
                else alert('Please enter the Date, PO Number, and Vendor Name.');
                return;
            }

            const tbody = document.getElementById('itemsTableBody');
            const rows = tbody.querySelectorAll('tr');
            let hasValidRow = false;
            let missingRowData = false;
            
            rows.forEach(row => {
                const qty = row.querySelector('input[name="quantity[]"]').value;
                const prodSelect = row.querySelector('select[name="product_id[]"]');
                const prodId = prodSelect ? prodSelect.value : '';
                const unitPrice = row.querySelector('input[name="unit_price[]"]').value;
                
                if (qty || prodId || unitPrice) {
                    if (!qty || !prodId || !unitPrice) {
                        missingRowData = true;
                    } else {
                        hasValidRow = true;
                    }
                }
            });

            if (missingRowData) {
                if(window.showAlert) window.showAlert('Please complete Quantity, Book selection, and Unit Price for all entered rows.', 'warning');
                else alert('Please complete Quantity, Book selection, and Unit Price for all entered rows.');
                return;
            }

            if (!hasValidRow) {
                if(window.showAlert) window.showAlert('Please add at least one item entry.', 'warning');
                else alert('Please add at least one item entry.');
                return;
            }

            document.querySelector('.order-form').style.display = 'none';
            document.getElementById('generatedPOSection').style.display = 'block';
            
            // Generate header
            document.getElementById('reportVendorName').textContent = vendorName;
            document.getElementById('reportVendorAddress').textContent = document.getElementById('formVendorAddress').value || '_______________________';
            document.getElementById('reportContactPersons').textContent = document.getElementById('formContactPersons').value || '____________________';
            
            document.getElementById('reportDate').textContent = formatDate(date);
            document.getElementById('reportPONumber').textContent = document.getElementById('formPONumber').value || '_____________';
            document.getElementById('reportTerms').textContent = document.getElementById('formTerms').value || '_____________';
            
            document.getElementById('reportPaymentSchedule').textContent = document.getElementById('formPaymentSchedule').value || '_______________________';
            document.getElementById('reportPaymentSchedule2').textContent = document.getElementById('formPaymentSchedule2').value || '_______________________';

            // Update currency header in generated PO
            const currency = document.getElementById('formCurrency').value;
            const currencySymbol = currency === 'PHP' ? '₱' : '$';
            const currencyLabel = currency === 'PHP' ? 'UNIT PRICE (PHP)' : 'UNIT PRICE (USD)';
            document.getElementById('reportUnitPriceHeader').textContent = currencyLabel;

            // Generate table — reuse tbody/rows already declared above
            const reportTbody = document.getElementById('reportItemsTableBody');
            reportTbody.innerHTML = '';
            
            let totalAmount = 0;
            
            rows.forEach(row => {
                const language = row.querySelector('input[name="language[]"]').value || '';
                const ft = row.querySelector('input[name="ft[]"]').value || '';
                const descInput = row.querySelector('.product-name-input');
                const description = descInput ? descInput.value : '';
                const quantity = row.querySelector('input[name="quantity[]"]').value || '';
                const unitPrice = parseFloat(row.querySelector('input[name="unit_price[]"]').value) || 0;
                const totalAmountRow = parseFloat(row.querySelector('input[name="total_amount[]"]').value) || 0;
                const bindings = row.querySelector('input[name="bindings[]"]').value || '';
                const remarks = row.querySelector('input[name="remarks[]"]').value || '';
                
                totalAmount += totalAmountRow;
                
                if (language || ft || description || quantity || unitPrice || totalAmountRow || bindings || remarks) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${language}</td>
                        <td style="text-align: center;">${ft}</td>
                        <td>${description}</td>
                        <td style="text-align: center;">${quantity}</td>
                        <td style="text-align: right;">${unitPrice > 0 ? currencySymbol + unitPrice.toFixed(2) : ''}</td>
                        <td style="text-align: right;">${totalAmountRow > 0 ? currencySymbol + totalAmountRow.toFixed(2) : ''}</td>
                        <td>${bindings}</td>
                        <td>${remarks}</td>
                    `;
                    reportTbody.appendChild(tr);
                }
            });
            
            // Minimum 5 blank rows
            const rowsCount = reportTbody.querySelectorAll('tr').length;
            for (let i = rowsCount; i < 5; i++) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                `;
                reportTbody.appendChild(tr);
            }
            
            document.getElementById('reportTotalAmount').textContent = currencySymbol + totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

            // --- Populate the hidden save form ---
            document.getElementById('saveSupplier').value         = document.getElementById('formSupplierId').value;
            document.getElementById('savePONumber').value          = poNumber;
            document.getElementById('saveDate').value              = date;
            document.getElementById('saveTerms').value             = document.getElementById('formTerms').value;
            document.getElementById('saveVendorName').value        = vendorName;
            document.getElementById('saveContactPersons').value    = document.getElementById('formContactPersons').value;
            document.getElementById('saveVendorAddress').value     = document.getElementById('formVendorAddress').value;
            document.getElementById('savePaymentSchedule').value   = document.getElementById('formPaymentSchedule').value;
            document.getElementById('savePaymentSchedule2').value  = document.getElementById('formPaymentSchedule2').value;
            document.getElementById('saveCurrency').value           = currency;

            // Build hidden item inputs
            const container = document.getElementById('saveItemsContainer');
            container.innerHTML = '';
            rows.forEach((row, idx) => {
                const lang  = row.querySelector('input[name="language[]"]').value;
                const ft    = row.querySelector('input[name="ft[]"]').value;
                
                const prodSelect = row.querySelector('select[name="product_id[]"]');
                const prodId = prodSelect ? prodSelect.value : '';
                
                const descInput = row.querySelector('.product-name-input');
                const desc  = descInput ? descInput.value : '';
                
                const qty   = row.querySelector('input[name="quantity[]"]').value;
                const price = row.querySelector('input[name="unit_price[]"]').value;
                const bind  = row.querySelector('input[name="bindings[]"]').value;
                const rem   = row.querySelector('input[name="remarks[]"]').value;

                if (!desc || !qty || !price || !prodId) return; // skip incomplete rows

                const hidden = (name, val) => {
                    const i = document.createElement('input');
                    i.type = 'hidden'; i.name = name; i.value = val;
                    container.appendChild(i);
                };
                hidden(`language[${idx}]`,     lang);
                hidden(`ft[${idx}]`,           ft);
                hidden(`product_id[${idx}]`,   prodId);
                hidden(`description[${idx}]`,  desc);
                hidden(`quantity[${idx}]`,     qty);
                hidden(`unit_price[${idx}]`,   price);
                hidden(`bindings[${idx}]`,     bind);
                hidden(`item_remarks[${idx}]`, rem);
            });

            // Scroll to it
            document.getElementById('generatedPOSection').scrollIntoView({ behavior: 'smooth' });
        }

        function savePOToSystem() {
            const supplierId = document.getElementById('formSupplierId').value;
            if (!supplierId) {
                if(window.showAlert) window.showAlert('Please select a Supplier before saving to the system.', 'warning');
                else alert('Please select a Supplier before saving to the system.');
                return;
            }
            const btn = document.getElementById('savePOBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="las la-spinner la-spin"></i> Saving...';
            document.getElementById('savePOForm').submit();
        }

        function resetGeneratedPO() {
            document.getElementById('purchaseOrderForm').reset();
            document.getElementById('generatedPOSection').style.display = 'none';
            
            const tbody = document.getElementById('itemsTableBody');
            while (tbody.rows.length > 1) {
                tbody.deleteRow(1);
            }
        }

        function backToForm() {
            document.querySelector('.order-form').style.display = 'block';
            document.getElementById('generatedPOSection').style.display = 'none';
        }

        function printReport() {
            window.print();
        }
    </script>
    @endpush

</x-app-layout>
