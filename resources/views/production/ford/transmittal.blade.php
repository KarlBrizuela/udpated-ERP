<x-app-layout :title="'Transmittal'" :sidebar="'production'">
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
                        <div class="document-title">TRANSMITTAL SLIP</div>
                    </div>

                    <form id="transmittalForm" class="form-section">
                        <!-- Recipient and Order Details -->
                        <div class="customer-section">
                            <div class="customer-details">
                                <h5>Recipient Information</h5>
                                <div class="form-group">
                                    <label>To:</label>
                                    <input type="text" name="to" id="formTo" placeholder="Enter recipient name">
                                </div>
                                <div class="form-group">
                                    <label>Address:</label>
                                    <textarea name="address" id="formAddress" placeholder="Enter address"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Contact No:</label>
                                    <input type="text" name="contact_no" id="formContactNo" placeholder="Enter contact number">
                                </div>
                            </div>
                            <div class="order-details">
                                <h5>Transmittal Information</h5>
                                <div class="form-group">
                                    <label>Date:</label>
                                    <input type="date" name="date" id="formDate">
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
                                    <th style="width: 120px;">QUANTITY</th>
                                    <th>DESCRIPTION</th>
                                    <th style="width: 200px;">REMARKS</th>
                                    <th style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <tr>
                                    <td><input type="number" name="quantity[]" placeholder="Quantity" min="0"></td>
                                    <td><input type="text" name="description[]" placeholder="Description"></td>
                                    <td><input type="text" name="remarks[]" placeholder="Remarks"></td>
                                    <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn btn-light" onclick="resetGeneratedTransmittal()">
                                <i class="las la-undo"></i> Reset
                            </button>
                            <button type="button" class="btn btn-primary" onclick="updateGeneratedTransmittal()">
                                <i class="las la-check"></i> Generate Transmittal
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Generated Transmittal Section -->
                <div class="generated-transmittal-section" id="generatedTransmittalSection" style="display: none;">
                    <div class="d-flex gap-2 mb-3 report-actions">
                        <button type="button" class="btn btn-secondary" onclick="backToForm()">
                            <i class="las la-arrow-left"></i> Back
                        </button>
                        <button type="button" class="btn btn-success" onclick="printReport()">
                            <i class="las la-print"></i> Print Transmittal
                        </button>
                    </div>
                    <div class="generated-transmittal" id="generatedTransmittal">
                        <div class="company-header" style="text-align: center; margin-bottom: 20px;">
                            <h2 style="font-weight: bold; margin-bottom: 5px;">CLARETIAN COMMUNICATIONS FOUNDATION INC.</h2>
                            <p style="margin: 2px 0;">8 Mayumi St., U.P. Village, Diliman 1101 Quezon City, Philippines</p>
                            <p style="margin: 2px 0;">C.P.O. Box 4 U.P. Diliman 1101 Quezon City, Philippines</p>
                            <p style="margin: 2px 0;">Tel.: 921-3984 • Fax: 921-6205</p>
                        </div>

                        <div class="transmittal-title">TRANSMITTAL SLIP</div>

                        <div class="transmittal-header-row">
                            <div class="transmittal-header-left">
                                <div class="transmittal-field">
                                    <strong>TO:</strong> <span class="blank-line" id="reportTo">_________________________</span>
                                </div>
                                <div class="transmittal-field">
                                    <strong>ADDRESS:</strong> <span class="blank-line" id="reportAddress">_________________________</span>
                                </div>
                                <div class="transmittal-field">
                                    <strong>CONTACT NO:</strong> <span class="blank-line" id="reportContactNo">_________________________</span>
                                </div>
                            </div>
                            <div class="transmittal-header-right">
                                <div class="transmittal-field-right">
                                    <strong>DATE:</strong> <span class="blank-line" id="reportDate">_____________</span>
                                </div>
                            </div>
                        </div>

                        <div style="margin-bottom: 10px;">
                            We are sending you herewith the following:
                        </div>

                        <table class="transmittal-table">
                            <thead>
                                <tr>
                                    <th class="quantity-col">QUANTITY</th>
                                    <th class="description-col">DESCRIPTION</th>
                                    <th class="remarks-col">REMARKS</th>
                                </tr>
                            </thead>
                            <tbody id="reportItemsTableBody">
                                <!-- Rows injected here -->
                            </tbody>
                        </table>

                        <div class="transmittal-footer">
                            <div class="transmittal-footer-item">
                                <div class="transmittal-footer-field">
                                    <strong>Prepared by:</strong>
                                    <div class="blank-line"></div>
                                </div>
                            </div>
                            <div class="transmittal-footer-item">
                                <div class="transmittal-footer-field">
                                    <strong>Delivered by:</strong>
                                    <div class="blank-line"></div>
                                </div>
                            </div>
                            <div class="transmittal-footer-item">
                                <div class="transmittal-footer-field">
                                    <strong>Received by:</strong>
                                    <div class="blank-line"></div>
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

        /* Generated Transmittal Section Styles */
        .generated-transmittal-section {
            max-width: 1200px;
            margin: 0;
        }
        .generated-transmittal {
            background: #fff;
            border: 1px solid #ddd;
            padding: 40px;
            margin-top: 30px;
            min-height: 800px;
            font-family: 'Times New Roman', serif;
        }
        .transmittal-title {
            text-align: center;
            font-weight: bold;
            font-size: 20px;
            margin: 20px 0 30px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .transmittal-header {
            margin-bottom: 30px;
        }
        .transmittal-header-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .transmittal-header-left {
            width: 60%;
        }
        .transmittal-header-right {
            width: 35%;
        }
        .transmittal-field {
            margin-bottom: 15px;
        }
        .transmittal-field strong {
            display: inline-block;
            width: 100px;
        }
        .transmittal-field .blank-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 300px;
            padding: 0 5px;
        }
        .transmittal-field-right {
            margin-bottom: 10px;
        }
        .transmittal-field-right strong {
            display: inline-block;
            width: 80px;
        }
        .transmittal-field-right .blank-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 200px;
            padding: 0 5px;
        }
        .transmittal-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }
        .transmittal-table th,
        .transmittal-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .transmittal-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .transmittal-table input {
            width: 100%;
            border: none;
            background: transparent;
            padding: 3px;
            font-size: 11px;
        }
        .transmittal-table .quantity-col {
            width: 120px;
            text-align: center;
        }
        .transmittal-table .description-col {
            width: 400px;
        }
        .transmittal-table .remarks-col {
            width: 200px;
        }
        .transmittal-footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .transmittal-footer-item {
            width: 30%;
        }
        .transmittal-footer-field {
            margin-bottom: 20px;
        }
        .transmittal-footer-field strong {
            display: block;
            margin-bottom: 5px;
        }
        .transmittal-footer-field .blank-line {
            display: block;
            border-bottom: 1px solid #000;
            min-width: 100%;
            padding: 0 5px;
            margin-top: 40px;
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

        function addRow() {
            rowCounter++;
            const tbody = document.getElementById('itemsTableBody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="number" name="quantity[]" placeholder="Quantity" min="0"></td>
                <td><input type="text" name="description[]" placeholder="Description"></td>
                <td><input type="text" name="remarks[]" placeholder="Remarks"></td>
                <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
            `;
            tbody.appendChild(newRow);
        }

        function removeRow(button) {
            const tbody = document.getElementById('itemsTableBody');
            if (tbody.rows.length > 1) {
                button.closest('tr').remove();
            } else {
                alert('At least one row is required.');
            }
        }

        function updateGeneratedTransmittal() {
            const to = document.getElementById('formTo').value;
            const date = document.getElementById('formDate').value;

            if (!to || !date) {
                if(window.showAlert) window.showAlert('Please fill in To and Date.', 'warning');
                else alert('Please fill in To and Date.');
                return;
            }

            const tbody = document.getElementById('itemsTableBody');
            const rows = tbody.querySelectorAll('tr');
            let hasValidRow = false;
            let missingRowData = false;
            
            rows.forEach(row => {
                const quantity = row.querySelector('input[name="quantity[]"]').value;
                const description = row.querySelector('input[name="description[]"]').value;
                
                if (quantity || description) {
                    if (!quantity || !description) {
                        missingRowData = true;
                    } else {
                        hasValidRow = true;
                    }
                }
            });

            if (missingRowData) {
                if(window.showAlert) window.showAlert('Please complete Quantity and Description for all entered rows.', 'warning');
                else alert('Please complete Quantity and Description for all entered rows.');
                return;
            }

            if (!hasValidRow) {
                if(window.showAlert) window.showAlert('Please add at least one item entry.', 'warning');
                else alert('Please add at least one item entry.');
                return;
            }

            document.querySelector('.order-form').style.display = 'none';
            document.getElementById('generatedTransmittalSection').style.display = 'block';
            
            // Header
            document.getElementById('reportTo').textContent = to;
            document.getElementById('reportAddress').textContent = document.getElementById('formAddress').value || '_________________________';
            document.getElementById('reportContactNo').textContent = document.getElementById('formContactNo').value || '_________________________';
            
            document.getElementById('reportDate').textContent = formatDate(date);

            // Table
            const reportTbody = document.getElementById('reportItemsTableBody');
            reportTbody.innerHTML = '';
            
            rows.forEach(row => {
                const quantity = row.querySelector('input[name="quantity[]"]').value || '';
                const description = row.querySelector('input[name="description[]"]').value || '';
                const remarks = row.querySelector('input[name="remarks[]"]').value || '';
                
                if (quantity || description || remarks) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td style="text-align: center;">${quantity}</td>
                        <td>${description}</td>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                `;
                reportTbody.appendChild(tr);
            }
            
            // Scroll to it
            document.getElementById('generatedTransmittalSection').scrollIntoView({ behavior: 'smooth' });
        }

        function resetGeneratedTransmittal() {
            document.getElementById('transmittalForm').reset();
            document.getElementById('generatedTransmittalSection').style.display = 'none';
            
            const tbody = document.getElementById('itemsTableBody');
            while (tbody.rows.length > 1) {
                tbody.deleteRow(1);
            }
        }

        function backToForm() {
            document.querySelector('.order-form').style.display = 'block';
            document.getElementById('generatedTransmittalSection').style.display = 'none';
        }

        function printReport() {
            window.print();
        }
    </script>
    @endpush

</x-app-layout>
