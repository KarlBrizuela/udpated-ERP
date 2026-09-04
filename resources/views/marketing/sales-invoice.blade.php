<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .invoice-form {
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

        .form-header .invoice-number {
            text-align: center;
            font-size: 1.25rem;
            font-weight: 700;
            color: #ff0000;
            margin-top: 0.5rem;
        }

        .customer-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 1.5rem;
        }

        .customer-details,
        .transaction-details {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
        }

        .customer-details h5,
        .transaction-details h5 {
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

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        .invoice-table thead {
            background: #ff0000;
            color: #fff;
        }

        .invoice-table th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid #ddd;
        }

        .invoice-table td {
            padding: 0.5rem;
            border: 1px solid #ddd;
        }

        .invoice-table input[type="text"],
        .invoice-table input[type="number"],
        .invoice-table textarea {
            width: 100%;
            border: none;
            padding: 0.5rem;
            background: transparent;
        }

        .invoice-table input[type="number"].unit-price-input,
        .invoice-table input[type="number"].amount-input {
            text-align: right;
        }

        .invoice-table input:focus,
        .invoice-table textarea:focus {
            outline: 2px solid #ff0000;
            outline-offset: -2px;
            background: #fff;
        }

        .summary-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 1.5rem;
        }

        .payment-methods,
        .financial-summary {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #ddd;
        }

        .summary-row:last-child {
            border-bottom: 2px solid #333;
            padding-top: 0.5rem;
            margin-top: 0.5rem;
        }

        .summary-row label {
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .summary-row input {
            width: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
            text-align: right;
            font-weight: 600;
        }

        .summary-row:last-child input {
            font-size: 1.1rem;
            color: #ff0000;
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
            background: #ff3333;
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

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        @media print {
            .sidebar-container, .header, .form-actions, .btn-add-row, .btn-remove-row {
                display: none !important;
            }
            .content-body { margin-left: 0 !important; padding: 0 !important; }
            .invoice-form { box-shadow: none !important; }
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card invoice-form">
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
                    <div class="document-title">SALES INVOICE</div>
                    <div class="text-center text-muted small fw-bold mb-1">NON-VAT REGISTERED</div>
                    <div class="text-center extra-small text-muted italic mb-2">"This document is not valid for claim of input taxes."</div>
                    <div class="invoice-number" id="invoiceNumber">No. SI-XXXXXX</div>
                </div>

                <!-- Customer and Transaction Details -->
                <div class="customer-section">
                    <div class="customer-details">
                        <h5>Customer Information</h5>
                        <div class="form-group">
                            <label>Sold to:</label>
                            <input type="text" id="soldTo" placeholder="Enter customer name">
                        </div>
                        <div class="form-group">
                            <label>Address:</label>
                            <textarea id="customerAddress" placeholder="Enter customer address" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label>TIN:</label>
                            <input type="text" id="customerTIN" placeholder="Enter Tax Identification Number">
                        </div>
                    </div>
                    <div class="transaction-details">
                        <h5>Transaction Details</h5>
                        <div class="form-group">
                            <label>Date:</label>
                            <input type="date" id="invoiceDate">
                        </div>
                        <div class="form-group">
                            <label>Terms:</label>
                            <input type="text" id="terms" placeholder="e.g., Net 30, COD">
                        </div>
                        <div class="form-group">
                            <label>Due Date:</label>
                            <input type="date" id="dueDate">
                        </div>
                    </div>
                </div>

                <!-- Invoice Table -->
                <button type="button" class="btn-add-row" onclick="addRow()">
                    <i class="las la-plus"></i> Add Row
                </button>

                <div class="table-responsive">
                    <table class="invoice-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">QTY</th>
                                <th>DESCRIPTION</th>
                                <th style="width: 150px;">AREA</th>
                                <th style="width: 150px; text-align: right;">UNIT PRICE</th>
                                <th style="width: 150px; text-align: right;">AMOUNT</th>
                                <th style="width: 80px; text-align: center;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceTableBody">
                            <tr>
                                <td><input type="number" class="qty-input" placeholder="Qty" min="0" step="1"></td>
                                <td><textarea class="description-input" rows="2" placeholder="Item description"></textarea></td>
                                <td><input type="text" class="area-input" placeholder="Area"></td>
                                <td><input type="number" class="unit-price-input" placeholder="0.00" min="0" step="0.01"></td>
                                <td><input type="number" class="amount-input" placeholder="0.00" readonly></td>
                                <td class="text-center">
                                    <button type="button" class="btn-remove-row" onclick="removeRow(this)">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Summary Section -->
                <div class="summary-section">
                    <div class="payment-methods">
                        <h5>Payment Method</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="paymentCash" name="paymentMethod" value="cash">
                            <label class="form-check-label" for="paymentCash">CASH</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="paymentCharge" name="paymentMethod" value="charge">
                            <label class="form-check-label" for="paymentCharge">CHARGE</label>
                        </div>
                    </div>
                    <div class="financial-summary">
                        <h5>Financial Summary</h5>
                        <div class="summary-row">
                            <label>LESS: WITHHOLDING TAX</label>
                            <input type="number" id="withholdingTax" placeholder="0.00" min="0" step="0.01" value="0.00">
                        </div>
                        <div class="summary-row">
                            <label>TOTAL SALES</label>
                            <input type="number" id="totalSales" placeholder="0.00" readonly value="0.00">
                        </div>
                        <div class="summary-row">
                            <label>TOTAL AMOUNT DUE</label>
                            <input type="number" id="totalAmountDue" placeholder="0.00" readonly value="0.00">
                        </div>
                    </div>
                </div>

                <!-- Footer Section -->
                <div class="signature-section mt-4 p-4 bg-light rounded">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3">Prepared By</h5>
                            <div class="form-group">
                                <label>Staff Name:</label>
                                <select id="preparedBy" class="form-control" onchange="updatePreparedBy()">
                                    <option value="">Select Staff</option>
                                    <option value="1" selected>Juan Dela Cruz - Accounting Staff</option>
                                    <option value="2">Maria Santos - Accounting Staff</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Date Prepared:</label>
                                <input type="date" id="preparedDate" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3">Manager Signature</h5>
                            <div class="form-group">
                                <label>Status:</label>
                                <span id="signatureStatus" class="status-badge bg-warning text-dark">Pending Signature</span>
                            </div>
                            <div class="form-group">
                                <label>Signed By:</label>
                                <input type="text" id="signedBy" class="form-control" readonly placeholder="Manager name will appear here">
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-info btn-sm w-100" onclick="requestSignature()">
                                    <i class="las la-signature"></i> Request Manager Signature
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-light" onclick="window.print()">
                        <i class="las la-print"></i> Print
                    </button>
                    <button type="button" class="btn btn-primary" onclick="saveInvoice()">
                        <i class="las la-save"></i> Save Invoice
                    </button>
                    <button type="button" class="btn btn-success" onclick="submitInvoice()">
                        <i class="las la-paper-plane"></i> Submit
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('invoiceDate').value = today;
            document.getElementById('preparedDate').value = today;

            const invoiceNo = 'SI-' + Date.now().toString().slice(-6);
            document.getElementById('invoiceNumber').textContent = 'No. ' + invoiceNo;

            document.getElementById('invoiceTableBody').addEventListener('input', function(e) {
                if (e.target.classList.contains('qty-input') || e.target.classList.contains('unit-price-input')) {
                    calculateRowAmount(e.target.closest('tr'));
                }
            });

            document.getElementById('withholdingTax').addEventListener('input', calculateTotals);
            document.getElementById('terms').addEventListener('change', calculateDueDate);
            document.getElementById('invoiceDate').addEventListener('change', calculateDueDate);
        });

        function calculateRowAmount(row) {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
            const amountInput = row.querySelector('.amount-input');
            amountInput.value = (qty * unitPrice).toFixed(2);
            calculateTotals();
        }

        function calculateTotals() {
            let totalSales = 0;
            document.querySelectorAll('.amount-input').forEach(input => {
                totalSales += parseFloat(input.value) || 0;
            });

            const withholdingTax = parseFloat(document.getElementById('withholdingTax').value) || 0;
            const totalAmountDue = totalSales - withholdingTax;

            document.getElementById('totalSales').value = totalSales.toFixed(2);
            document.getElementById('totalAmountDue').value = totalAmountDue.toFixed(2);
        }

        function calculateDueDate() {
            const invoiceDate = document.getElementById('invoiceDate').value;
            const terms = document.getElementById('terms').value;
            if (!invoiceDate) return;

            const date = new Date(invoiceDate);
            const daysMatch = terms.match(/\d+/);
            if (daysMatch) {
                date.setDate(date.getDate() + parseInt(daysMatch[0]));
                document.getElementById('dueDate').value = date.toISOString().split('T')[0];
            } else if (terms.toLowerCase().includes('cod')) {
                document.getElementById('dueDate').value = invoiceDate;
            }
        }

        function addRow() {
            const tbody = document.getElementById('invoiceTableBody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="number" class="qty-input" placeholder="Qty" min="0" step="1"></td>
                <td><textarea class="description-input" rows="2" placeholder="Item description"></textarea></td>
                <td><input type="text" class="area-input" placeholder="Area"></td>
                <td><input type="number" class="unit-price-input" placeholder="0.00" min="0" step="0.01"></td>
                <td><input type="number" class="amount-input" placeholder="0.00" readonly></td>
                <td class="text-center">
                    <button type="button" class="btn-remove-row" onclick="removeRow(this)">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
        }

        function removeRow(button) {
            const tbody = document.getElementById('invoiceTableBody');
            if (tbody.children.length > 1) {
                button.closest('tr').remove();
                calculateTotals();
            } else {
                alert('At least one row must remain.');
            }
        }

        function saveInvoice() {
            alert('Sales invoice saved successfully!');
        }

        function submitInvoice() {
            if (!document.getElementById('preparedBy').value) {
                alert('Please select staff name.');
                return;
            }
            if (confirm('Submit this sales invoice?')) {
                alert('Sales invoice submitted successfully!');
            }
        }

        function requestSignature() {
            alert('Signature request sent to manager.');
        }

        function updatePreparedBy() {
            // Logic to update display if needed
        }
    </script>
    @endpush
</x-app-layout>
