<x-app-layout :title="'Client Payment for Posting'" :sidebar="'production'">
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
                        <div class="document-title">CLIENT PAYMENT FOR POSTING</div>
                    </div>

                    <form id="clientPaymentForm" class="form-section">
                        <!-- Payment Information -->
                        <div class="order-details" style="margin-bottom: 1.5rem;">
                            <h5>Payment Information</h5>
                            <div class="form-group">
                                <label>Date:</label>
                                <input type="date" name="date" id="formDate">
                            </div>
                        </div>

                        <!-- Payment Table -->
                        <button type="button" class="btn-add-row" onclick="addRow()">
                            <i class="las la-plus"></i> Add Row
                        </button>

                        <table class="payment-table" id="paymentTable">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">NO.</th>
                                    <th>CLIENT'S NAME</th>
                                    <th style="width: 150px;">BANK/DATE</th>
                                    <th style="width: 150px;">DOCUMENT NO.</th>
                                    <th style="width: 150px;">AMOUNT</th>
                                    <th style="width: 250px;">PROOF OF PAYMENT</th>
                                    <th style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="paymentTableBody">
                                <tr>
                                    <td style="text-align: center;">1</td>
                                    <td>
                                        <select name="customer_id[]" class="form-control selectpicker" data-live-search="true" required>
                                            <option value="">Select Client</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->customer_id }}">{{ $customer->customer_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="text" name="bank_date[]" placeholder="Bank/Date"></td>
                                    <td><input type="text" name="document_no[]" placeholder="Document No."></td>
                                    <td><input type="number" name="amount[]" placeholder="Amount" min="0" step="0.01" required></td>
                                    <td><input type="file" name="proof_file[0]" class="form-control" accept="image/*,application/pdf"></td>
                                    <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn btn-light" onclick="resetGeneratedLetter()">
                                <i class="las la-undo"></i> Reset
                            </button>
                            <button type="button" class="btn btn-primary" onclick="updateGeneratedLetter()">
                                <i class="las la-check"></i> Generate Letter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Generated Letter Section -->
                <div class="generated-letter-section" id="generatedLetterSection" style="display: none;">
                    <div class="d-flex gap-2 mb-3 report-actions">
                        <button type="button" class="btn btn-secondary" onclick="backToForm()">
                            <i class="las la-arrow-left"></i> Back
                        </button>
                        <button type="button" class="btn btn-success" onclick="printReport()">
                            <i class="las la-print"></i> Print Letter
                        </button>
                    </div>
                    <div class="generated-letter" id="generatedLetter">
                        <div class="memo-header">
                            <div class="memo-header-row">
                                <span class="memo-header-label">DATE </span>: <span class="memo-header-value" id="reportDate">_____________</span>
                            </div>
                            <div class="memo-header-row">
                                <span class="memo-header-label">TO </span>: <span class="memo-header-value">ACCOUNTING DEPT.</span>
                            </div>
                            <div class="memo-header-row">
                                <span class="memo-header-label">FROM </span>: <span class="memo-header-value">FOREIGN ORDER AND RIGHTS DEPT.</span>
                            </div>
                            <div class="memo-header-row">
                                <span class="memo-header-label">RE </span>: <span class="memo-header-value">CLIENT'S PAYMENT FOR POSTING</span>
                            </div>
                        </div>

                        <div class="memo-body">
                            <div class="memo-body-text">
                                Please see attached copy of the deposited check / remittance as payment of the following client(s):
                            </div>
                            <table class="report-table" style="width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 20px;">
                                <thead>
                                    <tr>
                                        <th style="border: 1px solid #000; padding: 5px; text-align: center; width: 50px;">NO.</th>
                                        <th style="border: 1px solid #000; padding: 5px;">CLIENT'S NAME</th>
                                        <th style="border: 1px solid #000; padding: 5px;">BANK/DATE</th>
                                        <th style="border: 1px solid #000; padding: 5px;">DOCUMENT NO.</th>
                                        <th style="border: 1px solid #000; padding: 5px; text-align: right;">AMOUNT</th>
                                    </tr>
                                </thead>
                                <tbody id="reportPaymentTableBody">
                                    <!-- Rows injected here -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" style="border: 1px solid #000; padding: 5px; text-align: right; font-weight: bold;">TOTAL:</td>
                                        <td style="border: 1px solid #000; padding: 5px; text-align: right; font-weight: bold;" id="reportTotalAmount">0.00</td>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="memo-body-text">
                                For your appropriate action.
                            </div>
                        </div>

                        <div class="memo-footer">
                            <div class="memo-signature" style="display: flex; gap: 50px; flex-wrap: wrap;">
                                <div style="flex: 1; min-width: 200px; text-align: center;">
                                    <div>Prepared by:</div>
                                    <div style="border-bottom: 1px solid #000; margin-top: 40px; margin-bottom: 5px;"></div>
                                    <strong>MICHELLE MACALABON</strong><br>
                                    FORD Clerk
                                </div>
                                <div style="flex: 1; min-width: 200px; text-align: center;">
                                    <div>Noted by:</div>
                                    <div style="border-bottom: 1px solid #000; margin-top: 40px; margin-bottom: 5px;"></div>
                                    <strong>CRISTINA J. GALANG</strong><br>
                                    FORD Head
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

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        .payment-table thead {
            background: #ff0000;
            color: #fff;
        }

        .payment-table th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid #ddd;
        }

        .payment-table td {
            padding: 0.5rem;
            border: 1px solid #ddd;
        }

        .payment-table input[type="text"],
        .payment-table input[type="number"] {
            width: 100%;
            border: none;
            padding: 0.5rem;
            background: transparent;
        }

        .payment-table input[type="number"] {
            text-align: right;
        }

        .payment-table input:focus {
            outline: 2px solid #ff0000;
            outline-offset: -2px;
            background: #fff;
        }

        .payment-table tfoot {
            background: #f8f9fa;
            font-weight: 600;
        }

        .payment-table tfoot td {
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

        /* Generated Letter Section Styles (unchanged) */
        .generated-letter-section {
            max-width: 1000px;
            margin: 0;
        }
        .generated-letter {
            background: #fff;
            border: 1px solid #ddd;
            padding: 40px;
            margin-top: 30px;
            min-height: 500px;
            font-family: 'Times New Roman', serif;
        }
        .memo-header {
            margin-bottom: 20px;
        }
        .memo-header-row {
            margin-bottom: 5px;
        }
        .memo-body {
            margin: 20px 0;
            line-height: 1.8;
        }
        .memo-body-text {
            margin-bottom: 15px;
        }
        .memo-footer {
            margin-top: 30px;
        }
        .memo-signature {
            margin-top: 20px;
        }
        .memo-signature div {
            margin-bottom: 5px;
        }
        .blank-field {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 200px;
            padding: 0 5px;
            margin: 0 5px;
        }
        .payment-table .no-col {
            width: 50px;
            text-align: center;
        }
        .payment-table .client-name-col {
            width: 200px;
        }
        .payment-table .bank-date-col {
            width: 150px;
        }
        .payment-table .doc-no-col {
            width: 150px;
        }
        .payment-table .amount-col {
            width: 150px;
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
        const customers = @json($customers);
        let rowCounter = 1;

        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            return months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
        }

        function addRow() {
            rowCounter++;
            const tbody = document.getElementById('paymentTableBody');
            const newRow = document.createElement('tr');
            
            let options = '<option value="">Select Client</option>';
            customers.forEach(c => {
                options += `<option value="${c.customer_id}">${c.customer_name}</option>`;
            });

            newRow.innerHTML = `
                <td style="text-align: center;">${rowCounter}</td>
                <td>
                    <select name="customer_id[]" class="form-control selectpicker" data-live-search="true" required>
                        ${options}
                    </select>
                </td>
                <td><input type="text" name="bank_date[]" placeholder="Bank/Date"></td>
                <td><input type="text" name="document_no[]" placeholder="Document No."></td>
                <td><input type="number" name="amount[]" placeholder="Amount" min="0" step="0.01" required></td>
                <td><input type="file" name="proof_file[${rowCounter - 1}]" class="form-control" accept="image/*,application/pdf"></td>
                <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
            `;
            tbody.appendChild(newRow);
            $('.selectpicker').selectpicker('refresh');
        }

        function removeRow(button) {
            const tbody = document.getElementById('paymentTableBody');
            if (tbody.rows.length > 1) {
                button.closest('tr').remove();
                renumberRows();
            } else {
                alert('At least one row is required.');
            }
        }

        function renumberRows() {
            const tbody = document.getElementById('paymentTableBody');
            const rows = tbody.querySelectorAll('tr');
            rows.forEach((row, index) => {
                row.querySelector('td:first-child').textContent = index + 1;
                const fileInput = row.querySelector('input[type="file"]');
                if (fileInput) {
                    fileInput.name = `proof_file[${index}]`;
                }
            });
            rowCounter = rows.length;
        }

        function updateGeneratedLetter() {
            const date = document.getElementById('formDate').value;
            if (!date) {
                alert('Please select a Date.');
                return;
            }

            const tbody = document.getElementById('paymentTableBody');
            const rows = tbody.querySelectorAll('tr');
            let hasValidRow = true;

            rows.forEach(row => {
                const customerSelect = row.querySelector('select[name="customer_id[]"]');
                const amountInput = row.querySelector('input[name="amount[]"]');
                if (!customerSelect.value || !amountInput.value) {
                    hasValidRow = false;
                }
            });

            if (!hasValidRow) {
                alert('Please complete Client Name and Amount for all rows.');
                return;
            }

            // Perform AJAX Submit
            const form = document.getElementById('clientPaymentForm');
            const formData = new FormData(form);
            formData.append('_token', '{{ csrf_token() }}');

            // Disable generate button
            const genBtn = document.querySelector('button[onclick="updateGeneratedLetter()"]');
            if (genBtn) {
                genBtn.disabled = true;
                genBtn.textContent = 'Generating...';
            }

            fetch("{{ route('production.ford.client-payment-posting.store') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Populate Report Letter
                    document.getElementById('reportDate').textContent = formatDate(date);
                    const reportTbody = document.getElementById('reportPaymentTableBody');
                    reportTbody.innerHTML = '';
                    
                    let totalAmount = 0;
                    rows.forEach((row, index) => {
                        const selectElement = row.querySelector('select[name="customer_id[]"]');
                        const clientName = selectElement.options[selectElement.selectedIndex].text || '_____________';
                        const bankDate = row.querySelector('input[name="bank_date[]"]').value || '_____________';
                        const documentNo = row.querySelector('input[name="document_no[]"]').value || '_____________';
                        const amount = parseFloat(row.querySelector('input[name="amount[]"]').value) || 0;
                        
                        totalAmount += amount;
                        
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td style="border: 1px solid #000; padding: 5px; text-align: center;">${index + 1}</td>
                            <td style="border: 1px solid #000; padding: 5px;">${clientName}</td>
                            <td style="border: 1px solid #000; padding: 5px;">${bankDate}</td>
                            <td style="border: 1px solid #000; padding: 5px;">${documentNo}</td>
                            <td style="border: 1px solid #000; padding: 5px; text-align: right;">${amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        `;
                        reportTbody.appendChild(tr);
                    });
                    
                    document.getElementById('reportTotalAmount').textContent = totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    
                    document.querySelector('.order-form').style.display = 'none';
                    document.getElementById('generatedLetterSection').style.display = 'block';

                    if (window.showAlert) window.showAlert('Request saved and sent to Accounting successfully.', 'success');
                    else alert('Request saved and sent to Accounting successfully.');
                } else {
                    alert(data.message || 'An error occurred.');
                    if (genBtn) {
                        genBtn.disabled = false;
                        genBtn.innerHTML = '<i class="las la-check"></i> Generate Letter';
                    }
                }
            })
            .catch(err => {
                console.error(err);
                alert('Connection error occurred.');
                if (genBtn) {
                    genBtn.disabled = false;
                    genBtn.innerHTML = '<i class="las la-check"></i> Generate Letter';
                }
            });
        }

        function resetGeneratedLetter() {
            document.getElementById('clientPaymentForm').reset();
            document.getElementById('generatedLetterSection').style.display = 'none';
            document.querySelector('.order-form').style.display = 'block';
            document.getElementById('reportDate').textContent = '_____________';
            
            const tbody = document.getElementById('paymentTableBody');
            while (tbody.rows.length > 1) {
                tbody.deleteRow(1);
            }
            renumberRows();
            $('.selectpicker').selectpicker('refresh');
            const genBtn = document.querySelector('button[onclick="updateGeneratedLetter()"]');
            if (genBtn) {
                genBtn.disabled = false;
                genBtn.innerHTML = '<i class="las la-check"></i> Generate Letter';
            }
        }

        function backToForm() {
            resetGeneratedLetter();
        }

        function printReport() {
            window.print();
        }
    </script>
    @endpush
</x-app-layout>
