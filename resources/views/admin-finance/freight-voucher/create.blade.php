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
            flex-wrap: wrap;
        }
        .form-info-item { display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 250px; }
        .form-info-item label { font-weight: 600; color: #333; min-width: 120px; }
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
            * { margin: 0; padding: 0; }
            body, html { height: 100%; }
            .sidebar-wrapper, .header, .form-actions, .btn-add-row { display: none !important; }
            .content-body { margin-left: 0 !important; padding: 0 !important; }
            .voucher-form { 
                box-shadow: none; 
                padding: 0.5rem;
                background: white;
                border-radius: 0;
            }
            .form-header {
                margin-bottom: 0.5rem;
                padding-bottom: 0.5rem;
                border-bottom: 1px solid #e0e0e0;
            }
            .form-header .company-info {
                gap: 0.5rem;
                margin-bottom: 0.25rem;
            }
            .form-header .company-logo {
                width: 40px; 
                height: 40px;
                font-size: 1.5rem;
            }
            .form-header .company-name {
                font-size: 0.9rem;
            }
            .form-header .document-title {
                font-size: 1.25rem;
                margin-top: 0.25rem;
            }
            .form-info-row {
                margin-bottom: 0.5rem; 
                padding: 0.25rem;
                gap: 0.5rem;
            }
            .form-info-item { 
                gap: 0.25rem; 
                min-width: auto;
                font-size: 0.85rem;
            }
            .form-info-item label { 
                min-width: 80px; 
                font-size: 0.85rem;
            }
            .form-info-item input {
                font-size: 0.85rem;
                padding: 0.2rem 0;
            }
            .voucher-table { margin-bottom: 0.75rem; }
            .voucher-table th, .voucher-table td { 
                padding: 0.25rem; 
                font-size: 0.8rem;
            }
            .voucher-table th { font-size: 0.75rem; }
            .signature-row {
                gap: 1rem;
                margin-top: 0.5rem;
                padding-top: 0.5rem;
            }
            .signature-box label { 
                font-size: 0.75rem;
                margin-bottom: 0.25rem;
            }
            .signature-box input {
                font-size: 0.75rem;
                padding: 0.2rem 0;
            }
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
                    <div class="document-title">FREIGHT VOUCHER</div>
                </div>

                <form action="{{ route('admin-finance.freight-voucher.store') }}" method="POST">
                    @csrf

                    <div class="form-info-row">
                        <div class="form-info-item">
                            <label>FV No.:</label>
                            <input type="text" class="form-control @error('fv_number') is-invalid @enderror" name="fv_number" value="{{ old('fv_number') }}" placeholder="e.g., FV-2026-001" required>
                            @error('fv_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-info-item">
                            <label>Pay To:</label>
                            <input type="text" class="form-control @error('pay_to') is-invalid @enderror" name="pay_to" value="{{ old('pay_to') }}" placeholder="" required>
                            @error('pay_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                        <tbody id="fvTableBody">
                            <tr>
                                <td><input type="text" class="form-control border-0" name="items[0][particulars]" placeholder="Description of freight expense" required></td>
                                <td><input type="number" class="form-control border-0 text-end amount-input" name="items[0][amount]" step="0.01" value="0" oninput="calculateTotal()"></td>
                                <td class="text-center"><button type="button" class="btn btn-danger btn-xs" onclick="removeRow(this)"><i class="las la-trash"></i></button></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-end"><strong>TOTAL ADVANCE PAYMENT</strong></td>
                                <td class="text-end"><strong id="grandTotal">₱ 0.00</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="form-actions d-flex justify-content-between gap-2 mt-4">
                        <a href="{{ route('admin-finance.freight-voucher.index') }}" class="btn btn-primary rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                            <i class="las la-arrow-left me-1"></i>Back to List
                        </a>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;" onclick="window.print()">
                                <i class="las la-print me-1"></i>Print
                            </button>
                            <button type="submit" class="btn btn-primary rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="background: #28a745; color: #ffffff; border: none; height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                                <i class="las la-save me-1"></i>Create Voucher
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let rowCount = 1;

        function addRow() {
            const tableBody = document.getElementById('fvTableBody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="text" class="form-control border-0" name="items[${rowCount}][particulars]" placeholder="Description of freight expense" required></td>
                <td><input type="number" class="form-control border-0 text-end amount-input" name="items[${rowCount}][amount]" step="0.01" value="0" oninput="calculateTotal()"></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-xs" onclick="removeRow(this)"><i class="las la-trash"></i></button></td>
            `;
            tableBody.appendChild(newRow);
            rowCount++;
        }

        function removeRow(button) {
            const tableBody = document.getElementById('fvTableBody');
            if (tableBody.children.length > 1) {
                button.closest('tr').remove();
                calculateTotal();
            } else {
                alert('At least one item is required.');
            }
        }

        function calculateTotal() {
            const inputs = document.querySelectorAll('.amount-input');
            let total = 0;
            inputs.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('grandTotal').textContent = '₱ ' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        // Calculate total on page load
        document.addEventListener('DOMContentLoaded', calculateTotal);
    </script>
    @endpush
</x-app-layout>
