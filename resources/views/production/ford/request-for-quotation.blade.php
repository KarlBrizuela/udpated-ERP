<x-app-layout :title="'Request for Quotation'" :sidebar="'production'">
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
                        <div class="document-title">REQUEST FOR QUOTATION</div>
                    </div>

                    <form id="requestForQuotationForm" class="form-section">
                        <!-- Account and Order Details -->
                        <div class="customer-section">
                            <div class="customer-details">
                                <h5>Account Information</h5>
                                <div class="form-group">
                                    <label>Account Name:</label>
                                    <input type="text" name="account_name" id="formAccountName" placeholder="Enter account name">
                                </div>
                                <div class="form-group">
                                    <label>SEND via:</label>
                                    <input type="text" name="send_via" id="formSendVia" placeholder="Enter send method">
                                </div>
                            </div>
                            <div class="order-details">
                                <h5>Quotation Information</h5>
                                <div class="form-group">
                                    <label>Terms:</label>
                                    <input type="text" name="terms" id="formTerms" placeholder="Enter terms">
                                </div>
                                <div class="form-group">
                                    <label>Declared Value:</label>
                                    <input type="text" name="declared_value" id="formDeclaredValue" placeholder="Enter declared value">
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="d-flex gap-2 mb-3">
                            <button type="button" class="btn-add-row mb-0" onclick="addRow()">
                                <i class="las la-plus"></i> Add Row
                            </button>
                            <button type="button" class="btn btn-primary" id="addBookItemBtn" style="height: 38px; min-height: 38px; border: none; background: #007bff; color: #fff; font-weight: 600; border-radius: 4px; padding: 0 1rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem;">
                                <i class="las la-book"></i> Add Item
                            </button>
                        </div>

                        <table class="form-table" id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">QTY</th>
                                    <th>DESCRIPTION</th>
                                    <th style="width: 120px;">UNIT PRICE</th>
                                    <th style="width: 120px;">AMOUNT</th>
                                    <th style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <tr>
                                    <td><input type="number" name="quantity[]" placeholder="Qty" min="0" oninput="calculateRowTotal(this)"></td>
                                    <td><input type="text" name="description[]" placeholder="Description"></td>
                                    <td><input type="number" name="unit_price[]" placeholder="Unit Price" min="0" step="0.01" oninput="calculateRowTotal(this)"></td>
                                    <td><input type="number" name="amount[]" placeholder="Amount" readonly></td>
                                    <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Hidden Book Options for JS -->
                        <select id="bookSource" class="d-none">
                            <option value="" disabled selected>Select Book...</option>
                            @if(isset($books))
                                @foreach($books as $book)
                                    <option value="{{ $book->id }}" 
                                            data-price="{{ $book->price }}" 
                                            data-name="{{ $book->name }}">
                                            {{ $book->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn btn-light" onclick="resetGeneratedRFQ()">
                                <i class="las la-undo"></i> Reset
                            </button>
                            <button type="button" class="btn btn-primary" onclick="updateGeneratedRFQ()">
                                <i class="las la-check"></i> Generate Request for Quotation
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Generated RFQ Section -->
                <div class="generated-rfq-section" id="generatedRFQSection" style="display: none;">
                    <div class="d-flex gap-2 mb-3 report-actions">
                        <button type="button" class="btn btn-secondary" onclick="backToForm()">
                            <i class="las la-arrow-left"></i> Back
                        </button>
                        <button type="button" class="btn btn-success" onclick="printReport()">
                            <i class="las la-print"></i> Print RFQ
                        </button>
                    </div>
                    <div class="generated-rfq" id="generatedRFQ">
                        <div class="company-header" style="text-align: center; margin-bottom: 20px;">
                            <h2>CLARETIAN COMMUNICATIONS FOUNDATION INC.</h2>
                            <p>8 Mayumi St., U.P. Village, Diliman 1101 Quezon City, Philippines</p>
                            <p>C.P.O. Box 4 U.P. Diliman 1101 Quezon City, Philippines</p>
                            <p>Tel.: 921-3984 • Fax: 921-6205</p>
                        </div>

                        <div class="rfq-title" style="text-align: center; font-weight: bold; font-size: 20px; margin: 30px 0; text-transform: uppercase;">
                            REQUEST FOR QUOTATION
                        </div>

                        <div class="rfq-details">
                            <div class="rfq-details-row" style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                                <div class="rfq-details-left" style="width: 60%;">
                                    <div class="rfq-field">
                                        <strong>ACCOUNT NAME:</strong> <span class="blank-line" id="reportAccountName">_________________________</span>
                                    </div>
                                    <div class="rfq-field">
                                        <strong>TERMS:</strong> <span class="blank-line" id="reportTerms">_________________________</span>
                                    </div>
                                </div>
                                <div class="rfq-details-right" style="width: 35%;">
                                    <div class="rfq-field-right">
                                        <strong>DATE:</strong> <span class="blank-line" id="reportDate">_____________</span>
                                    </div>
                                    <div class="rfq-field-right">
                                        <strong>SEND via:</strong> <span class="blank-line" id="reportSendVia">_____________</span>
                                    </div>
                                    <div class="rfq-field-right">
                                        <strong>DECLARED VALUE:</strong> <span class="blank-line" id="reportDeclaredValue">_____________</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="rfq-table" style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                            <thead>
                                <tr>
                                    <th class="qty-col" style="border: 1px solid #000; padding: 8px; text-align: center;">QTY / WT</th>
                                    <th class="description-col" style="border: 1px solid #000; padding: 8px;">DESCRIPTION</th>
                                    <th class="unit-price-col" style="border: 1px solid #000; padding: 8px; text-align: right;">UNIT PRICE</th>
                                    <th class="amount-col" style="border: 1px solid #000; padding: 8px; text-align: right;">AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody id="reportItemsTableBody">
                                <!-- Rows injected here -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" style="text-align: right; border: 1px solid #000; padding: 5px; font-weight: bold;">TOTAL:</td>
                                    <td style="text-align: right; border: 1px solid #000; padding: 5px; font-weight: bold;" id="reportTotalAmount">0.00</td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="rfq-footer" style="margin-top: 50px;">
                            <div style="font-style: italic; margin-bottom: 30px;">Note: Any discrepancy in this quotation must be reported immediately.</div>
                            
                            <div style="display: flex; justify-content: space-between; margin-top: 50px;">
                                <div style="width: 45%; text-align: center;">
                                    <div style="border-bottom: 1px solid #000; margin-bottom: 5px;"></div>
                                    <strong>Requested by</strong>
                                </div>
                                <div style="width: 45%; text-align: center;">
                                    <div style="border-bottom: 1px solid #000; margin-bottom: 5px;"></div>
                                    <strong>Approved by</strong>
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

        /* Generated RFQ Section Styles (unchanged) */
        .generated-rfq-section {
            max-width: 1200px;
            margin: 0;
        }
        .generated-rfq {
            background: #fff;
            border: 1px solid #ddd;
            padding: 40px;
            margin-top: 30px;
            min-height: 800px;
            font-family: 'Times New Roman', serif;
        }
        .rfq-header {
            margin-bottom: 30px;
        }
        .company-header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        .company-header .company-logo {
            width: 80px;
            height: 80px;
            margin-right: 20px;
            background: linear-gradient(135deg, #ff0000 0%, #ffd700 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 36px;
            font-weight: bold;
        }
        .company-header .company-info {
            flex: 1;
        }
        .company-header .company-info h2 {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 18px;
        }
        .company-header .company-info p {
            margin: 2px 0;
            font-size: 12px;
        }
        .rfq-title {
            text-align: center;
            font-weight: bold;
            font-size: 20px;
            margin: 30px 0;
            text-transform: uppercase;
        }
        .rfq-details {
            margin-bottom: 20px;
        }
        .rfq-details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .rfq-details-left {
            width: 60%;
        }
        .rfq-details-right {
            width: 35%;
        }
        .rfq-field {
            margin-bottom: 10px;
        }
        .rfq-field strong {
            display: inline-block;
            width: 120px;
        }
        .rfq-field .blank-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 300px;
            padding: 0 5px;
        }
        .rfq-field-right {
            margin-bottom: 10px;
        }
        .rfq-field-right strong {
            display: inline-block;
            width: 100px;
        }
        .rfq-field-right .blank-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 200px;
            padding: 0 5px;
        }
        .rfq-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }
        .rfq-table th,
        .rfq-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .rfq-table th {
            background-color: #4a90e2;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        .rfq-table tbody tr {
            background-color: white;
        }
        .rfq-table tbody tr:last-child {
            background-color: #4a90e2;
            color: white;
        }
        .rfq-table tbody tr:last-child td:first-child {
            font-weight: bold;
            color: #ff0000;
            text-align: left;
        }
        .rfq-table tbody tr:last-child td:last-child {
            font-weight: bold;
            color: white;
            text-align: right;
        }
        .rfq-table input {
            width: 100%;
            border: none;
            background: transparent;
            padding: 3px;
            font-size: 11px;
        }
        .rfq-table .qty-col {
            width: 80px;
            text-align: center;
        }
        .rfq-table .description-col {
            width: 400px;
        }
        .rfq-table .unit-price-col {
            width: 120px;
            text-align: right;
        }
        .rfq-table .amount-col {
            width: 120px;
            text-align: right;
        }
        .rfq-footer {
            margin-top: 30px;
        }
        .rfq-footer strong {
            display: inline-block;
            width: 120px;
        }
        .rfq-footer .blank-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 300px;
            padding: 0 5px;
        }

        @media print {
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
            const amount = qty * price;
            
            row.querySelector('input[name="amount[]"]').value = amount > 0 ? amount.toFixed(2) : '';
        }

        function addRow() {
            rowCounter++;
            const tbody = document.getElementById('itemsTableBody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="number" name="quantity[]" placeholder="Qty" min="0" oninput="calculateRowTotal(this)"></td>
                <td><input type="text" name="description[]" placeholder="Description"></td>
                <td><input type="number" name="unit_price[]" placeholder="Unit Price" min="0" step="0.01" oninput="calculateRowTotal(this)"></td>
                <td><input type="number" name="amount[]" placeholder="Amount" readonly></td>
                <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
            `;
            tbody.appendChild(newRow);
        }

        function addBookItemRow() {
            rowCounter++;
            const tbody = document.getElementById('itemsTableBody');
            const newRow = document.createElement('tr');
            
            // Get book options
            const bookSource = document.getElementById('bookSource');
            const optionsHtml = bookSource ? bookSource.innerHTML : '<option value="" disabled>No books available</option>';
            
            newRow.innerHTML = `
                <td><input type="number" name="quantity[]" placeholder="Qty" min="0" oninput="calculateRowTotal(this)" value="1"></td>
                <td>
                    <select class="form-control selectpicker select-book-item" name="book_id[]" data-live-search="true" onchange="onBookSelected(this)" required style="width: 100%;">
                        ${optionsHtml}
                    </select>
                    <input type="hidden" name="description[]" class="book-description-input">
                </td>
                <td><input type="number" name="unit_price[]" placeholder="Unit Price" min="0" step="0.01" oninput="calculateRowTotal(this)"></td>
                <td><input type="number" name="amount[]" placeholder="Amount" readonly></td>
                <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
            `;
            tbody.appendChild(newRow);
            
            // Initialize selectpicker if jQuery and bootstrap-select are available
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.selectpicker === 'function') {
                jQuery(newRow).find('.selectpicker').selectpicker('render');
            }
        }

        function onBookSelected(selectEl) {
            const option = selectEl.options[selectEl.selectedIndex];
            if (option) {
                const price = option.getAttribute('data-price') || 0;
                const name = option.getAttribute('data-name') || '';
                
                const row = selectEl.closest('tr');
                const priceInput = row.querySelector('input[name="unit_price[]"]');
                const descInput = row.querySelector('.book-description-input');
                
                if (priceInput) priceInput.value = price;
                if (descInput) descInput.value = name;
                
                if (priceInput) {
                    calculateRowTotal(priceInput);
                }
            }
        }

        function setupAddBookBtn() {
            const btn = document.getElementById('addBookItemBtn');
            if (btn) {
                btn.addEventListener('click', addBookItemRow);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupAddBookBtn);
        } else {
            setupAddBookBtn();
        }

        function removeRow(button) {
            const tbody = document.getElementById('itemsTableBody');
            if (tbody.rows.length > 1) {
                button.closest('tr').remove();
            } else {
                alert('At least one row is required.');
            }
        }

        function updateGeneratedRFQ() {
            const accountName = document.getElementById('formAccountName').value;
            const terms = document.getElementById('formTerms').value;
            const date = document.getElementById('formDate').value;
            const sendVia = document.getElementById('formSendVia').value;
            const declaredValue = document.getElementById('formDeclaredValue').value;

            if (!accountName || !terms || !date || !sendVia || !declaredValue) {
                if(window.showAlert) window.showAlert('Please fill in all Account and Quotation Information.', 'warning');
                else alert('Please fill in all Account and Quotation Information.');
                return;
            }

            const tbody = document.getElementById('itemsTableBody');
            const rows = tbody.querySelectorAll('tr');
            let hasValidRow = false;
            let missingRowData = false;
            
            rows.forEach(row => {
                const quantity = row.querySelector('input[name="quantity[]"]').value;
                const description = row.querySelector('input[name="description[]"]').value;
                const unitPrice = row.querySelector('input[name="unit_price[]"]').value;
                
                if (quantity || description || unitPrice) {
                    if (!quantity || !description || !unitPrice) {
                        missingRowData = true;
                    } else {
                        hasValidRow = true;
                    }
                }
            });

            if (missingRowData) {
                if(window.showAlert) window.showAlert('Please complete Quantity, Description, and Unit Price for all entered rows.', 'warning');
                else alert('Please complete Quantity, Description, and Unit Price for all entered rows.');
                return;
            }

            if (!hasValidRow) {
                if(window.showAlert) window.showAlert('Please add at least one item entry.', 'warning');
                else alert('Please add at least one item entry.');
                return;
            }

            document.querySelector('.order-form').style.display = 'none';
            document.getElementById('generatedRFQSection').style.display = 'block';
            
            // Header
            document.getElementById('reportAccountName').textContent = accountName;
            document.getElementById('reportTerms').textContent = terms;
            document.getElementById('reportDate').textContent = formatDate(date);
            document.getElementById('reportSendVia').textContent = sendVia;
            document.getElementById('reportDeclaredValue').textContent = declaredValue;

            // Table
            const tbody = document.getElementById('itemsTableBody');
            const reportTbody = document.getElementById('reportItemsTableBody');
            reportTbody.innerHTML = '';
            
            let totalAmount = 0;
            const rows = tbody.querySelectorAll('tr');
            
            rows.forEach(row => {
                const quantity = row.querySelector('input[name="quantity[]"]').value || '';
                const description = row.querySelector('input[name="description[]"]').value || '';
                const unitPrice = parseFloat(row.querySelector('input[name="unit_price[]"]').value) || 0;
                const amount = parseFloat(row.querySelector('input[name="amount[]"]').value) || 0;
                
                totalAmount += amount;
                
                if (quantity || description || unitPrice || amount) {
                    const tr = document.createElement('tr');
                    tr.style.backgroundColor = 'white';
                    tr.innerHTML = `
                        <td style="text-align: center; border: 1px solid #000; padding: 8px;">${quantity}</td>
                        <td style="border: 1px solid #000; padding: 8px;">${description}</td>
                        <td style="text-align: right; border: 1px solid #000; padding: 8px;">${unitPrice > 0 ? unitPrice.toFixed(2) : ''}</td>
                        <td style="text-align: right; border: 1px solid #000; padding: 8px;">${amount > 0 ? amount.toFixed(2) : ''}</td>
                    `;
                    reportTbody.appendChild(tr);
                }
            });

            // Minimum 5 blank rows
            const rowsCount = reportTbody.querySelectorAll('tr').length;
            for (let i = rowsCount; i < 5; i++) {
                const tr = document.createElement('tr');
                tr.style.backgroundColor = 'white';
                tr.innerHTML = `
                    <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
                    <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
                    <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
                    <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
                `;
                reportTbody.appendChild(tr);
            }
            
            document.getElementById('reportTotalAmount').textContent = totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            // Scroll to it
            document.getElementById('generatedRFQSection').scrollIntoView({ behavior: 'smooth' });
        }

        function resetGeneratedRFQ() {
            document.getElementById('requestForQuotationForm').reset();
            document.getElementById('generatedRFQSection').style.display = 'none';
            
            const tbody = document.getElementById('itemsTableBody');
            while (tbody.rows.length > 1) {
                tbody.deleteRow(1);
            }
        }

        function backToForm() {
            document.querySelector('.order-form').style.display = 'block';
            document.getElementById('generatedRFQSection').style.display = 'none';
        }

        function printReport() {
            window.print();
        }
    </script>
    @endpush

</x-app-layout>
