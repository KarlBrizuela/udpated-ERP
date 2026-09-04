<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .receipt-form { background: #fff; border-radius: 8px; padding: 2rem; box-shadow: 0 0 20px rgba(0, 0, 0, 0.05); }
        .form-header { margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #e0e0e0; }
        .form-header .company-info { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
        .form-header .company-logo { width: 60px; height: 60px; background: #ff0000; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; font-weight: bold; flex-shrink: 0; }
        .form-header .document-title { text-align: center; font-size: 1.75rem; font-weight: 700; color: #333; margin-top: 1rem; letter-spacing: 1px; }
        .form-info-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding: 0.75rem; background: #f8f9fa; border-radius: 6px; gap: 2rem; }
        .receipt-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        .receipt-table thead { background: #ff0000; color: #fff; }
        .receipt-table th, .receipt-table td { padding: 0.75rem; border: 1px solid #ddd; }
        .signature-section { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem; border-top: 2px solid #eee; padding-top: 2rem; }
        @media print { .sidebar, .header, .form-actions, .btn-add-row { display: none !important; } }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <form id="receiptForm" onsubmit="event.preventDefault(); alert('Acknowledgement Receipt Saved Successfully!');">
                <div class="card receipt-form">
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
                    <div class="document-title">ACKNOWLEDGMENT RECEIPT</div>
                </div>

                <!-- Info Row -->
                <div class="form-info-row">
                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0 font-w600">No.:</label>
                        <input type="text" class="form-control" value="AR-2026-000123" style="width: 200px;">
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0 font-w600">Date:</label>
                        <input type="date" class="form-control" value="{{ date('Y-m-d') }}" style="width: 200px;">
                    </div>
                </div>

                <!-- Recipient -->
                <div class="mb-4">
                    <label class="form-label font-w600">To:</label>
                    <select class="form-control" required>
                        <option value="">Select Customer</option>
                        <option value="National Book Store">National Book Store</option>
                    </select>
                </div>

                <!-- Table -->
                <button type="button" class="btn btn-sm btn-primary mb-3" onclick="addRow()" style="background:#ff0000; border:none;">
                    <i class="las la-plus me-2"></i>Add Row
                </button>
                <table class="receipt-table">
                    <thead>
                        <tr>
                            <th style="width: 100px;">QTY</th>
                            <th>DESCRIPTION</th>
                            <th style="width: 150px;">UNIT PRICE</th>
                            <th style="width: 150px;">AMOUNT</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody id="receiptTableBody">
                        <tr>
                            <td><input type="number" class="qty form-control border-0" value="1" min="1" required onchange="calculate()"></td>
                            <td><input type="text" class="form-control border-0" placeholder="Enter description" required></td>
                            <td><input type="number" class="price form-control border-0" value="0" step="0.01" min="0" required onchange="calculate()"></td>
                            <td><input type="number" class="amount form-control border-0" value="0" readonly></td>
                            <td><button type="button" class="btn btn-danger btn-xs sharp" onclick="removeRow(this)"><i class="fa fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>

                <!-- Summary -->
                <div class="text-end mb-4">
                    <h4 class="text-primary">Total Amount: <span id="totalAmount">₱0.00</span></h4>
                </div>

                <!-- Signatures -->
                <div class="signature-section">
                    <div class="text-center">
                        <div style="border-bottom: 1px solid #333; margin-bottom: 5px; height: 30px;"></div>
                        <label class="font-w600">Released by</label>
                    </div>
                    <div class="text-center">
                        <div style="border-bottom: 1px solid #333; margin-bottom: 5px; height: 30px;"></div>
                        <label class="font-w600">Received by</label>
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions mt-4 text-end">
                    <button type="button" class="btn btn-light" onclick="window.print()">Print</button>
                    <button type="submit" class="btn btn-primary" style="background:#ff0000; border:none;">Save Receipt</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function addRow() {
            const tbody = document.getElementById('receiptTableBody');
            const row = tbody.rows[0].cloneNode(true);
            row.querySelectorAll('input').forEach(i => i.value = i.classList.contains('qty') ? 1 : 0);
            tbody.appendChild(row);
        }
        function removeRow(btn) {
            if (document.getElementById('receiptTableBody').rows.length > 1) {
                btn.closest('tr').remove();
                calculate();
            }
        }
        function calculate() {
            let total = 0;
            document.querySelectorAll('#receiptTableBody tr').forEach(row => {
                const qty = parseFloat(row.querySelector('.qty').value) || 0;
                const price = parseFloat(row.querySelector('.price').value) || 0;
                const amount = qty * price;
                row.querySelector('.amount').value = amount.toFixed(2);
                total += amount;
            });
            document.getElementById('totalAmount').textContent = `₱${total.toFixed(2)}`;
        }
    </script>
    @endpush
</x-app-layout>
