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
        .signature-row {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e0e0e0;
        }
        .signature-box {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .signature-box label { font-weight: 600; color: #333; margin-bottom: 0.5rem; }
        .signature-box input {
            border: 0;
            border-bottom: 2px solid #ccc;
            border-radius: 0;
            padding: 0.5rem 0;
            background: transparent;
        }
        .signature-box input:focus {
            outline: none;
            border-bottom-color: #ff0000;
            box-shadow: none;
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
                    <div class="document-title">PETTY CASH VOUCHER</div>
                </div>

                <form action="{{ route('admin-finance.petty-cash.store') }}" method="POST">
                    @csrf

                    <div class="form-info-row">
                        <div class="form-info-item">
                            <label>PCV No.:</label>
                            <input type="text" class="form-control @error('pcv_number') is-invalid @enderror" name="pcv_number" value="{{ old('pcv_number', $pcvNumber) }}" readonly required>
                            @error('pcv_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-info-item">
                            <label>Voucher Type:</label>
                            <select class="form-control" name="type" required>
                                <option value="fund" {{ old('type') === 'fund' ? 'selected' : '' }}>Petty Cash for Fund</option>
                                <option value="freight" {{ old('type') === 'freight' ? 'selected' : '' }}>Petty Cash for Freight</option>
                            </select>
                        </div>
                        <div class="form-info-item">
                            <label>Pay To:</label>
                            <input type="text" class="form-control @error('pay_to') is-invalid @enderror" name="pay_to" value="{{ old('pay_to') }}" required>
                        </div>
                        <div class="form-info-item">
                            <label>Date:</label>
                            <input type="date" class="form-control" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary rounded shadow-sm p-0 px-4 mb-3 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 28px;" onclick="addRow()">
                        <i class="las la-plus me-1"></i>Add Item
                    </button>

                    <table class="voucher-table">
                        <thead>
                            <tr>
                                <th>PARTICULARS</th>
                                <th style="width: 160px;" class="text-end">AMOUNT</th>
                                <th style="width: 60px;"></th>
                            </tr>
                        </thead>
                        <tbody id="pcvTableBody">
                            <tr>
                                <td><input type="text" class="form-control border-0" name="items[0][particulars]" placeholder="Description of expense" required></td>
                                <td><input type="number" class="form-control border-0 text-end amount-input" name="items[0][amount]" step="0.01" value="0" oninput="calculateTotal()"></td>
                                <td class="text-center"><button type="button" class="btn btn-danger btn-xs" onclick="removeRow(this)"><i class="las la-trash"></i></button></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-end"><strong>TOTAL</strong></td>
                                <td class="text-end">
                                    <strong id="grandTotal">₱ 0.00</strong>
                                    <div id="limitWarning" class="text-danger mt-1 text-start" style="display: none; font-size: 0.85rem; font-weight: bold;">
                                        Voucher total cannot exceed ₱1,000. For amounts above ₱1,000, please create a Material Request or Cash Advance.
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>



                    <div class="form-actions d-flex justify-content-between gap-2 mt-4">
                        <a href="{{ route('admin-finance.petty-cash.index') }}" class="btn btn-primary rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                            <i class="las la-arrow-left me-1"></i>Back to List
                        </a>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="height: 40px !important; padding-top: 0 !important; padding-bottom: 0 !important;" onclick="window.print()"><i class="las la-print me-1"></i>Print</button>
                            <button type="submit" class="btn btn-primary rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;">Save Voucher</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.amount-input').forEach(i => total += parseFloat(i.value) || 0);
            document.getElementById('grandTotal').textContent = '₱ ' + total.toLocaleString('en-US', { minimumFractionDigits: 2 });

            const submitBtn = document.querySelector('button[type="submit"]');
            const warning = document.getElementById('limitWarning');
            if (total > 1000) {
                warning.style.display = 'block';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.5';
                }
            } else {
                warning.style.display = 'none';
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                }
            }
        }

        function addRow() {
            const index = document.querySelectorAll('#pcvTableBody tr').length;
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" class="form-control border-0" name="items[${index}][particulars]" placeholder="Description of expense" required></td>
                <td><input type="number" class="form-control border-0 text-end amount-input" name="items[${index}][amount]" step="0.01" value="0" oninput="calculateTotal()"></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-xs" onclick="removeRow(this)"><i class="las la-trash"></i></button></td>
            `;
            document.getElementById('pcvTableBody').appendChild(row);
        }

        function removeRow(btn) {
            if (document.querySelectorAll('#pcvTableBody tr').length > 1) {
                btn.closest('tr').remove();
                calculateTotal();
            }
        }
    </script>
    @endpush
</x-app-layout>
