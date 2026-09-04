<x-app-layout :title="'Purchase Order'" :sidebar="'production'">
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <!-- Form Section -->
            <div class="card order-form">
                <!-- Form Header -->
                <div class="form-header">
                    <div class="company-info">
                        <div class="company-logo">C</div>
                        <div class="company-details">
                            <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION, INC.</div>
                            <div class="company-address">8 Mayumi St., UP Village, Diliman, Quezon City 1128</div>
                            <div class="company-contact">Non-Vat Reg. TIN: 000-395-713-000</div>
                            <div class="company-contact">Telephone: (02) 921-3984 | Fax: (02) 921-6205</div>
                        </div>
                    </div>
                    <div class="document-title">PURCHASE ORDER</div>
                </div>

                <form id="purchaseOrderForm" class="form-section" action="{{ route('production.logistic.purchase-order.store') }}" method="POST">
                    @csrf
                    <!-- Order Details -->
                    <div class="customer-section">
                        <div class="customer-details">
                            <h5>Vendor Information</h5>
                            <div class="form-group">
                                <label>Vendor:</label>
                                <select name="supplier_id" id="formSupplierId" class="form-control default-select" onchange="updateVendorInfo(this)" required>
                                    <option value="">-- Select Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" 
                                            data-name="{{ $supplier->company_name }}"
                                            data-person="{{ $supplier->contact_person }}"
                                            data-address="">{{ $supplier->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Vendor Contact Person:</label>
                                <input type="text" id="formVendorContactPerson" readonly placeholder="">
                            </div>
                            <div class="form-group">
                                <label>Vendor Address:</label>
                                <textarea id="formVendorAddress" readonly placeholder=""></textarea>
                            </div>
                        </div>
                        <div class="order-details">
                            <h5>Order Information</h5>
                            <div class="form-group">
                                <label>Date:</label>
                                <input type="date" name="date" id="formDate" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="form-group">
                                <label>P.O. No.:</label>
                                <input type="text" name="po_number" id="formPONumber" value="PO-{{ date('YmdHis') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Terms:</label>
                                <input type="text" name="terms" id="formTerms" placeholder="">
                            </div>
                            <div class="form-group">
                                <label>Invoice No. (Optional):</label>
                                <input type="text" name="invoice_number" id="formInvoiceNumber" placeholder="">
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <button type="button" class="btn-add-row" onclick="addRow()">
                        <i class="las la-plus"></i> Add Item
                    </button>

                    <table class="form-table" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width: 80px;">QTY</th>
                                <th>PRODUCT / DESCRIPTION</th>
                                <th style="width: 150px;">ISBN</th>
                                <th style="width: 150px;">UNIT PRICE</th>
                                <th style="width: 150px;">AMOUNT</th>
                                <th style="width: 80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                                <tr class="item-row">
                                    <td><input type="number" name="items[0][quantity]" class="qty-input" placeholder="0" min="1" step="1" required oninput="calculateRowTotal(this)"></td>
                                    <td>
                                        <select name="items[0][product_id]" class="form-control product-select" onchange="updateRowFromProduct(this)">
                                            <option value="">-- Select Product (or Custom) --</option>
                                            @foreach($products as $product)
                                            <option value="{{ $product->id }}" 
                                                    data-isbn="{{ $product->isbn ?? $product->sku }}" 
                                                    data-price="{{ $product->cost }}"
                                                    data-name="{{ $product->name }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="items[0][description]" class="mt-2 desc-input" placeholder="Custom Description" required>
                                    </td>
                                    <td><input type="text" name="items[0][isbn]" class="isbn-input" placeholder=""></td>
                                    <td><input type="number" name="items[0][unit_price]" class="price-input" placeholder="0.00" min="0" step="0.01" required oninput="calculateRowTotal(this)"></td>
                                    <td><input type="number" name="items[0][total_amount]" class="total-input" placeholder="0.00" readonly></td>
                                    <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" style="text-align: right; font-weight: 600;">TOTAL:</td>
                                    <td><input type="number" name="total_amount" id="grandTotal" value="0.00" readonly style="font-weight: 600; text-align: right; border: none; background: transparent;"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                    </table>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="button" class="btn btn-light" onclick="window.location.href='{{ route('production.logistic.purchase-order-list') }}'">
                            <i class="las la-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="las la-check"></i> Save Purchase Order
                        </button>
                    </div>
                </form>
                </div>

                <!-- Generated Purchase Order Section -->
                <div class="card mt-4">
                    <div class="card-header border-0">
                        <h4 class="fs-20 mb-0 text-black">Generated Purchase Order</h4>
                    </div>
                    <div class="card-body">
                        <div class="generated-po-section">
                            <div id="generatedPO" class="generated-po">
                                <!-- Company Header -->
                                <div class="company-header">
                                    <h2>CLARETIAN COMMUNICATIONS FOUNDATION, INC.</h2>
                                    <p>8 Mayumi St., UP Village, Diliman, Quezon City 1128</p>
                                    <p>Non-Vat Reg. TIN: 000-395-713-000</p>
                                    <p>Telephone: (02) 921-3984</p>
                                    <p>Fax: (02) 921-6205</p>
                                </div>

                                <!-- PO Title -->
                                <div class="po-title">
                                    PURCHASE ORDER
                                </div>

                                <!-- PO Details -->
                                <div class="po-details">
                                    <div class="po-details-left">
                                        <div class="vendor-section">
                                            <div><strong>Vendor:</strong> <span id="poVendorName">_____________</span></div>
                                            <div style="margin-top: 5px;"><strong>Vendor Contact Person:</strong> <span id="poVendorContactPerson">_____________</span></div>
                                            <div style="margin-top: 5px;"><strong>Vendor Address:</strong> <span id="poVendorAddress">_____________</span></div>
                                        </div>
                                    </div>
                                    <div class="po-details-right">
                                        <div><strong>Date:</strong> <span id="poDate">_____________</span></div>
                                        <div style="margin-top: 5px;"><strong>P.O. No.:</strong> <span id="poPONumber">_____________</span></div>
                                        <div style="margin-top: 5px;"><strong>Terms:</strong> <span id="poTerms">_____________</span></div>
                                        <div style="margin-top: 5px;"><strong>Invoice No.:</strong> <span id="poInvoiceNumber">_____________</span></div>
                                    </div>
                                </div>

                                <!-- Items Table -->
                                <table class="po-table" id="poItemsTable">
                                    <thead>
                                        <tr>
                                            <th class="quantity-col">QTY</th>
                                            <th class="description-col">DESCRIPTION</th>
                                            <th class="isbn-col">ISBN</th>
                                            <th class="unit-price-col">UNIT PRICE</th>
                                            <th class="total-amount-col">AMOUNT</th>
                                        </tr>
                                    </thead>
                                    <tbody id="poItemsTableBody">
                                        <tr>
                                            <td style="text-align: center;">_____________</td>
                                            <td>_____________</td>
                                            <td>_____________</td>
                                            <td style="text-align: right;">_____________</td>
                                            <td style="text-align: right;">_____________</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" style="text-align: right; font-weight: 600;">TOTAL:</td>
                                            <td style="text-align: right; font-weight: 600;" id="poGrandTotal">_____________</td>
                                        </tr>
                                    </tfoot>
                                </table>


                                <!-- Signature Section -->
                                <div class="signature-section">
                                    <div class="signature-box">
                                        <strong>Prepared by:</strong>
                                        <div class="signature-line">
                                            <div style="margin-top: 50px;">_______________________</div>
                                            <div>Printed Name / Date</div>
                                        </div>
                                    </div>
                                    <div class="signature-box">
                                        <strong>Approved by:</strong>
                                        <div class="signature-line">
                                            <div><strong>Fr. Louie R. Guades III, CMF</strong></div>
                                            <div>Executive Director</div>
                                            <div style="margin-top: 40px;">_______________________</div>
                                            <div>Signature over Printed Name / Date</div>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align: center; margin-top: 20px;">
                                    Page 1 / 1
                                </div>
                            </div>
                            <div class="text-end mt-3">
                                <button type="button" class="btn btn-success" onclick="printPO()">Print Purchase Order</button>
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

        .order-table, .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        .order-table thead, .form-table thead {
            background: #ff0000;
            color: #fff;
        }

        .order-table th, .form-table th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid #ddd;
        }

        .order-table td, .form-table td {
            padding: 0.5rem;
            border: 1px solid #ddd;
        }

        .order-table input[type="text"],
        .order-table input[type="number"],
        .order-table textarea,
        .form-table input[type="text"],
        .form-table input[type="number"],
        .form-table textarea {
            width: 100%;
            border: none;
            padding: 0.5rem;
            background: transparent;
        }

        .order-table input[type="number"],
        .form-table input[type="number"] {
            text-align: right;
        }

        .order-table textarea,
        .form-table textarea {
            resize: vertical;
            height: 40px !important; padding-top: 0 !important; padding-bottom: 0 !important;
        }

        .order-table input:focus,
        .order-table textarea:focus,
        .form-table input:focus,
        .form-table textarea:focus {
            outline: 2px solid #ff0000;
            outline-offset: -2px;
            background: #fff;
        }

        .order-table tfoot, .form-table tfoot {
            background: #f8f9fa;
            font-weight: 600;
        }

        .order-table tfoot td, .form-table tfoot td {
            padding: 0.75rem;
            border-top: 2px solid #333;
        }

        .terms-section {
            margin-bottom: 1.5rem;
        }

        .terms-section label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            display: block;
        }

        .terms-section textarea {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
            min-height: 80px;
            resize: vertical;
        }

        .terms-section textarea:focus {
            outline: 2px solid #ff0000;
            outline-offset: -2px;
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
        .po-table .quantity-col {
            width: 80px;
            text-align: center;
        }
        .po-table .description-col {
            width: 300px;
        }
        .po-table .isbn-col {
            width: 150px;
        }
        .po-table .unit-price-col {
            width: 120px;
            text-align: right;
        }
        .po-table .total-amount-col {
            width: 120px;
            text-align: right;
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
            .sidebar,
            .header,
            .form-actions,
            .btn-add-row {
                display: none;
            }

            .order-form {
                box-shadow: none;
            }
        }
    </style>
    @endpush
    @push('scripts')
    <script>
        let rowCount = 1;

        function addRow() {
            const tbody = document.getElementById('itemsTableBody');
            const newRow = document.createElement('tr');
            newRow.className = 'item-row';
            newRow.innerHTML = `
                <td><input type="number" name="items[${rowCount}][quantity]" class="qty-input" placeholder="0" min="1" step="1" required oninput="calculateRowTotal(this)"></td>
                <td>
                    <select name="items[${rowCount}][product_id]" class="form-control product-select" onchange="updateRowFromProduct(this)">
                        <option value="">-- Select Product (or Custom) --</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" 
                                data-isbn="{{ $product->isbn ?? $product->sku }}" 
                                data-price="{{ $product->cost }}"
                                data-name="{{ $product->name }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="items[${rowCount}][description]" class="mt-2 desc-input" placeholder="Custom Description" required>
                </td>
                <td><input type="text" name="items[${rowCount}][isbn]" class="isbn-input" placeholder=""></td>
                <td><input type="number" name="items[${rowCount}][unit_price]" class="price-input" placeholder="0.00" min="0" step="0.01" required oninput="calculateRowTotal(this)"></td>
                <td><input type="number" name="items[${rowCount}][total_amount]" class="total-input" placeholder="0.00" readonly></td>
                <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
            `;
            tbody.appendChild(newRow);
            rowCount++;
        }

        function removeRow(button) {
            const row = button.closest('tr');
            if (document.querySelectorAll('.item-row').length > 1) {
                row.remove();
                updateGrandTotal();
            } else {
                alert('At least one item is required.');
            }
        }

        function updateVendorInfo(select) {
            const option = select.selectedOptions[0];
            document.getElementById('formVendorContactPerson').value = option.dataset.person || '';
            document.getElementById('formVendorAddress').value = option.dataset.address || '';
            
            // Update preview
            document.getElementById('poVendorName').innerText = option.dataset.name || '_____________';
            document.getElementById('poVendorContactPerson').innerText = option.dataset.person || '_____________';
            document.getElementById('poVendorAddress').innerText = option.dataset.address || '_____________';
        }

        function updateRowFromProduct(select) {
            const row = select.closest('tr');
            const option = select.selectedOptions[0];
            
            if (option.value) {
                row.querySelector('.desc-input').value = option.dataset.name;
                row.querySelector('.isbn-input').value = option.dataset.isbn;
                row.querySelector('.price-input').value = option.dataset.price;
            }
            
            calculateRowTotal(row.querySelector('.qty-input'));
        }

        function calculateRowTotal(input) {
            const row = input.closest('tr');
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const total = qty * price;
            
            row.querySelector('.total-input').value = total.toFixed(2);
            updateGrandTotal();
            updatePreviewTable();
        }

        function updateGrandTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.total-input').forEach(input => {
                grandTotal += parseFloat(input.value) || 0;
            });
            document.getElementById('grandTotal').value = grandTotal.toFixed(2);
            document.getElementById('poGrandTotal').innerText = '₱' + grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2});
        }

        function updatePreviewTable() {
            const previewTbody = document.getElementById('poItemsTableBody');
            previewTbody.innerHTML = '';
            
            document.querySelectorAll('.item-row').forEach(row => {
                const qty = row.querySelector('.qty-input').value || '___';
                const desc = row.querySelector('.desc-input').value || '___';
                const isbn = row.querySelector('.isbn-input').value || '___';
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                const total = parseFloat(row.querySelector('.total-input').value) || 0;
                
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td style="text-align: center;">${qty}</td>
                    <td>${desc}</td>
                    <td>${isbn}</td>
                    <td style="text-align: right;">₱${price.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                    <td style="text-align: right;">₱${total.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                `;
                previewTbody.appendChild(tr);
            });
        }

        // Sync basic info with preview
        document.getElementById('formPONumber').addEventListener('input', function() {
            document.getElementById('poPONumber').innerText = this.value;
        });
        document.getElementById('formDate').addEventListener('input', function() {
            document.getElementById('poDate').innerText = this.value;
        });
        document.getElementById('formTerms').addEventListener('input', function() {
            document.getElementById('poTerms').innerText = this.value;
        });
        document.getElementById('formInvoiceNumber').addEventListener('input', function() {
            document.getElementById('poInvoiceNumber').innerText = this.value;
        });

        function printPO() {
            window.print();
        }
    </script>
    @endpush
</x-app-layout>
