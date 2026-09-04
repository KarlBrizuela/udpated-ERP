<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .payment-form { background: #fff; border-radius: 8px; padding: 2rem; box-shadow: 0 0 20px rgba(0, 0, 0, 0.05); }
        .form-header { margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #e0e0e0; }
        .form-header .company-info { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
        .form-header .company-logo { width: 60px; height: 60px; background: #ff0000; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; font-weight: bold; flex-shrink: 0; }
        .payment-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        .payment-table thead { background: #ff0000; color: #fff; }
        .payment-table th, .payment-table td { padding: 0.75rem; border: 1px solid #ddd; }
        .upload-section { margin: 1.5rem 0; padding: 2rem; background: #f8f9fa; border: 2px dashed #ddd; border-radius: 6px; text-align: center; }
        @media print { .sidebar, .header, .form-actions, .btn-add-row, .upload-section { display: none !important; } }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <form id="paymentForm" onsubmit="event.preventDefault(); alert('Proof of Payment Saved Successfully!');">
                <div class="card payment-form">
                <!-- Form Header -->
                <div class="form-header">
                    <div class="company-info">
                        <div class="company-logo">C</div>
                        <div class="company-details">
                            <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION, INC.</div>
                            <div class="company-address">8 Mayumi Street, Diliman, Quezon City</div>
                        </div>
                    </div>
                    <h3 class="text-center font-w700 mt-4">PROOF OF PAYMENT</h3>
                </div>

                <!-- Info Row -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label font-w600">Date:</label>
                        <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <!-- Table -->
                <button type="button" class="btn btn-sm btn-primary mb-3" onclick="addRow()" style="background:#ff0000; border:none;">
                    <i class="las la-plus me-2"></i>Add Row
                </button>
                <div class="table-responsive">
                    <table class="payment-table">
                        <thead>
                            <tr>
                                <th>Client's Name</th>
                                <th>Bank/Date</th>
                                <th>Document No.</th>
                                <th>Amount</th>
                                <th>Remarks</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="paymentTableBody">
                            <tr>
                                <td><input type="text" class="form-control border-0" placeholder="Client Name" required></td>
                                <td><input type="text" class="form-control border-0" placeholder="e.g. GCash 12.22.25" required></td>
                                <td><input type="text" class="form-control border-0" placeholder="SI# 12345"></td>
                                <td><input type="number" class="form-control border-0 amount" value="0" step="0.01" min="0" required onchange="calculate()"></td>
                                <td><input type="text" class="form-control border-0" placeholder="Remarks"></td>
                                <td><button type="button" class="btn btn-danger btn-xs sharp" onclick="removeRow(this)"><i class="fa fa-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Upload -->
                <div class="upload-section">
                    <i class="las la-cloud-upload-alt fs-50 text-muted"></i>
                    <h5 class="mt-3">Upload Proof of Payment</h5>
                    <p class="text-muted">Drag and drop file or click to browse</p>
                    <input type="file" class="form-control mt-3 py-2" accept="image/*">
                </div>

                <!-- Footer -->
                <div class="row mt-4 pt-4 border-top">
                    <div class="col-md-6">
                        <label class="form-label font-w600">Prepared By:</label>
                        <input type="text" class="form-control" placeholder="Enter name" required>
                        <input type="text" class="form-control mt-2" placeholder="Position" required>
                    </div>
                    <div class="col-md-6 text-end">
                        <h4 class="text-primary mt-4">Total: <span id="totalAmount">₱0.00</span></h4>
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions mt-4 text-end">
                    <button type="button" class="btn btn-light" onclick="window.print()">Print</button>
                    <button type="submit" class="btn btn-primary" style="background:#ff0000; border:none;">Save & Submit</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function addRow() {
            const tbody = document.getElementById('paymentTableBody');
            const row = tbody.rows[0].cloneNode(true);
            row.querySelectorAll('input').forEach(i => i.value = i.classList.contains('amount') ? 0 : '');
            tbody.appendChild(row);
        }
        function removeRow(btn) {
            if (document.getElementById('paymentTableBody').rows.length > 1) {
                btn.closest('tr').remove();
                calculate();
            }
        }
        function calculate() {
            let total = 0;
            document.querySelectorAll('.amount').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('totalAmount').textContent = `₱${total.toFixed(2)}`;
        }
    </script>
    @endpush
</x-app-layout>
