<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .voucher-form {
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
            width: 60px; height: 60px;
            background: #ff0000; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 2rem; font-weight: bold;
        }

        .form-header .company-name {
            font-size: 1.25rem; font-weight: 700; color: #333;
            text-transform: uppercase;
        }

        .form-header .document-title {
            text-align: center; font-size: 1.75rem; font-weight: 700;
            color: #333; margin-top: 1rem;
        }

        .form-info-row {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 1.5rem; padding: 0.75rem;
            background: #f8f9fa; border-radius: 6px; gap: 2rem;
        }

        .form-info-item { display: flex; align-items: center; gap: 0.75rem; flex: 1; }
        .form-info-item label { font-weight: 600; color: #333; min-width: 100px; }

        .voucher-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        .voucher-table thead { background: #ff0000; color: #fff; }
        .voucher-table th { padding: 0.75rem; border: 1px solid #ddd; }
        .voucher-table td { padding: 0.5rem; border: 1px solid #ddd; }
        .voucher-table tfoot { background: #f8f9fa; font-weight: 600; }

        .btn-add-row {
            background: #ff0000; color: #fff; border: none;
            padding: 0.5rem 1rem; border-radius: 4px; margin-bottom: 1rem;
        }

        @media print {
            .sidebar-wrapper, .header, .form-actions, .btn-add-row { display: none !important; }
            .content-body { margin-left: 0 !important; padding: 0 !important; }
            .voucher-form { box-shadow: none; }
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card voucher-form">
                <div class="form-header">
                    <div class="company-info">
                        <div class="company-logo">C</div>
                        <div class="company-details">
                            <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                            <div class="company-address">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                            <div class="company-contact">Tel. No.: 921-3984</div>
                        </div>
                    </div>
                    <div class="document-title">CHECK VOUCHER</div>
                </div>

                <div class="form-info-row">
                    <div class="form-info-item">
                        <label>Payee:</label>
                        <input type="text" class="form-control" id="payee">
                    </div>
                    <div class="form-info-item">
                        <label>Check No.:</label>
                        <input type="text" class="form-control" id="checkNo">
                    </div>
                    <div class="form-info-item">
                        <label>Date:</label>
                        <input type="date" class="form-control" id="voucherDate" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <button type="button" class="btn-add-row" onclick="addRow()">
                    <i class="las la-plus"></i> Add Row
                </button>

                <table class="voucher-table">
                    <thead>
                        <tr>
                            <th>ACCOUNT</th>
                            <th style="width: 200px;">DEBIT</th>
                            <th style="width: 200px;">CREDIT</th>
                            <th style="width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="voucherTableBody">
                        <tr>
                            <td><input type="text" class="form-control border-0" placeholder="Enter account name"></td>
                            <td><input type="number" class="form-control border-0 text-end debit-input" step="0.01"></td>
                            <td><input type="number" class="form-control border-0 text-end credit-input" step="0.01"></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-xs" onclick="removeRow(this)"><i class="las la-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-end"><strong>TOTAL</strong></td>
                            <td class="text-end"><strong id="totalDebit">0.00</strong></td>
                            <td class="text-end"><strong id="totalCredit">0.00</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                <div class="mb-4">
                    <label class="fw-bold">MEMO:</label>
                    <textarea class="form-control" rows="3"></textarea>
                </div>

                <div class="form-actions d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light" onclick="window.print()"><i class="las la-print"></i> Print</button>
                    <button type="button" class="btn btn-primary">Save Voucher</button>
                    <button type="button" class="btn btn-success">Submit</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function calculateTotals() {
            let debit = 0;
            let credit = 0;
            document.querySelectorAll('.debit-input').forEach(i => debit += parseFloat(i.value) || 0);
            document.querySelectorAll('.credit-input').forEach(i => credit += parseFloat(i.value) || 0);
            document.getElementById('totalDebit').textContent = debit.toLocaleString('en-US', {minimumFractionDigits: 2});
            document.getElementById('totalCredit').textContent = credit.toLocaleString('en-US', {minimumFractionDigits: 2});
        }

        function addRow() {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" class="form-control border-0"></td>
                <td><input type="number" class="form-control border-0 text-end debit-input" step="0.01"></td>
                <td><input type="number" class="form-control border-0 text-end credit-input" step="0.01"></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-xs" onclick="removeRow(this)"><i class="las la-trash"></i></button></td>
            `;
            document.getElementById('voucherTableBody').appendChild(row);
            row.querySelectorAll('input').forEach(i => i.addEventListener('input', calculateTotals));
        }

        function removeRow(btn) {
            if (document.querySelectorAll('#voucherTableBody tr').length > 1) {
                btn.closest('tr').remove();
                calculateTotals();
            }
        }

        document.getElementById('voucherTableBody').addEventListener('input', calculateTotals);
    </script>
    @endpush
</x-app-layout>
