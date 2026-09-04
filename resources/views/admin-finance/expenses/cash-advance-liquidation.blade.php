<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .liquidation-form {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .form-header {
            margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #e0e0e0;
        }

        .document-title {
            text-align: center; font-size: 1.75rem; font-weight: 700;
            color: #333; margin: 1.5rem 0; text-transform: uppercase;
        }

        .info-grid {
            display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 1.5rem;
        }

        .form-group-row { display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem; }
        .form-group-row label { font-weight: 700; color: #333; min-width: 100px; margin-bottom: 0; }
        .form-group-row input { flex: 1; border: none; border-bottom: 1px solid #000; padding: 0.25rem 0.5rem; background: transparent; }

        .advanced-amount-box {
            background: #f0f0f0; padding: 1rem; border-radius: 4px; margin-bottom: 2rem;
            display: flex; align-items: center; gap: 1rem;
        }

        .expense-category-header { font-weight: 800; color: #000; margin-bottom: 1rem; text-transform: uppercase; }
        .expense-category-title { font-weight: 700; text-decoration: underline; font-style: italic; margin-top: 10px; margin-bottom: 5px; color: #333; }
        .expense-row { display: flex; justify-content: space-between; align-items: center; padding: 0.4rem 0; border-bottom: 1px dotted #ccc; }
        .expense-label { font-size: 0.9rem; color: #333; padding-left: 1.5rem; flex: 1; }

        .expense-input-group { display: flex; align-items: center; gap: 0.5rem; width: 180px; }
        .expense-input-group input { width: 100%; border: none; border-bottom: 1px solid #999; text-align: right; padding: 2px 5px; }

        .totals-footer { margin-top: 3rem; padding-top: 1rem; border-top: 2px solid #333; }
        .totals-row.final { background: #f0f0f0; padding: 1rem; border-radius: 4px; font-weight: 800; font-size: 1.25rem; }

        .signature-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem; margin-top: 4rem; }
        .signature-item { text-align: center; }
        .signature-line { border-top: 1px solid #000; margin-bottom: 5px; padding-top: 5px; }

        @media print {
            .sidebar-wrapper, .header, .btn-group-container { display: none !important; }
            .content-body { margin-left: 0 !important; padding: 0 !important; }
            .liquidation-form { box-shadow: none; padding: 0; }
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin-finance.expenses.cash-advance-liquidation.store') }}" method="POST">
                        @csrf
                        <div class="liquidation-form" id="printArea">
                        <!-- Form Header -->
                        <div class="form-header">
                            <div class="text-center mb-3">
                                <img src="{{ asset('images/claeritian_logo.png') }}" width="50" alt="">
                            </div>
                            <div class="text-center h4 fw-bold mb-1 text-uppercase">CLARETIAN COMMUNICATIONS FOUNDATION, INC.</div>
                            <div class="text-center text-muted small">#8 Mayumi Street, U.P. Village, Diliman, Quezon City</div>
                        </div>

                        <div class="document-title">CASH ADVANCE LIQUIDATION FORM</div>

                        <!-- Top Info -->
                        <div class="info-grid mt-4">
                            <div>
                                <div class="form-group-row">
                                    <label>NAME :</label>
                                    <input type="text" name="employee_name" id="employeeName" required>
                                </div>
                                <div class="form-group-row">
                                    <label>Purpose :</label>
                                    <input type="text" name="purpose" id="purpose" required>
                                </div>
                            </div>
                            <div>
                                <div class="form-group-row">
                                    <label>Date :</label>
                                    <input type="date" id="formDate" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Amount Advanced -->
                        <div class="advanced-amount-box mt-4">
                            <label class="mb-0">AMOUNT ADVANCED (CV # <input type="text" name="reference" style="width: 150px; border-bottom: 1px solid #000; padding: 0;"> )</label>
                            <div class="ms-auto d-flex align-items-center">
                                <span class="me-2 fw-bold">PhP</span>
                                <input type="number" name="amount_advanced" id="amountAdvanced" placeholder="0.00" class="fw-bold border-0 border-bottom border-dark" step="0.01" style="width: 180px; text-align: right;" required>
                            </div>
                        </div>

                        <div class="expense-section mt-4">
                            <div class="expense-category-header">LESS: BREAKDOWN OF EXPENSES:</div>

                            @php
                                $categories = [
                                    'TRAVEL EXPENSES' => ['Gasoline: Provincial Trips', 'Metro Manila', 'Transportation', 'Airfare Ticket', 'Fine & Taxes', 'Parking Fees and Toll Fees'],
                                    'ALLOWANCES' => ['Meal Allowance', 'Lodging Expenses'],
                                    'OFFICE SUPPLIES' => ['General', 'Accounting', 'DTO', 'Marketing', 'FORD', 'Bookstore', 'Warehouse and General Services', 'Corp. Planning', 'Executive Office'],
                                    'REPAIRS AND MAINTENANCE' => ['Vehicle', 'Building', 'Computers'],
                                    'REPRESENTATION' => ['Executive Office', 'Department'],
                                    'COMMUNICATIONS' => ['Ordinary Mail', 'Special Mail'],
                                    'OTHERS' => ['Copying Cost', 'Bank Charges', 'Delivery Local', 'PBF', 'OTHERS']
                                ];
                            @endphp

                            @foreach($categories as $category => $items)
                                <div class="expense-category-title">{{ $category }}</div>
                                <div class="ps-4">
                                    @foreach($items as $item)
                                        <div class="expense-row">
                                            <div class="expense-label">{{ $item }}</div>
                                            <div class="expense-input-group">
                                                <input type="number" name="expenses[{{ $item }}]" class="expense-input" step="0.01" placeholder="0.00">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>

                        <!-- Totals -->
                        <div class="totals-footer">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold text-uppercase">TOTAL EXPENSES</span>
                                <div class="d-flex align-items-center">
                                    <span class="me-2 fw-bold">PhP</span>
                                    <span id="totalExpenses" class="fw-bold border-bottom border-dark border-2 px-3">0.00</span>
                                    <input type="hidden" name="total_expenses" id="totalExpensesHidden" value="0">
                                </div>
                            </div>
                            <div class="totals-row final d-flex justify-content-between align-items-center">
                                <span class="text-uppercase">BALANCE FOR REFUND/REIMBURSMENT</span>
                                <div class="d-flex align-items-center">
                                    <span class="me-2">PhP</span>
                                    <span id="finalBalance">0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Signatures -->
                        <div class="signature-grid">
                            <div class="signature-item">
                                <div class="fw-bold mb-5">Prepared by:</div>
                                <div class="signature-line"></div>
                                <div class="small italic text-muted">Employee</div>
                            </div>
                            <div class="signature-item">
                                <div class="fw-bold mb-5">Checked by:</div>
                                <div class="signature-line"></div>
                                <div class="small italic text-muted">Accounting</div>
                            </div>
                            <div class="signature-item">
                                <div class="fw-bold mb-5">Approved by:</div>
                                <div class="signature-line"></div>
                                <div class="small italic text-muted">Manager</div>
                            </div>
                            <div class="signature-item">
                                <div class="fw-bold mb-5">Received by:</div>
                                <div class="signature-line"></div>
                                <div class="small italic text-muted">Cashier/Finance</div>
                            </div>
                        </div>
                    </div>

                    <div class="btn-group-container text-end mt-4">
                        <button type="button" class="btn btn-light rounded shadow-sm p-0 px-4 me-2 d-inline-flex align-items-center justify-content-center" style="height: 40px !important; padding-top: 0 !important; padding-bottom: 0 !important;" onclick="resetForm()">
                            <i class="las la-undo"></i>Reset
                        </button>
                        <button type="button" class="btn btn-primary rounded shadow-sm p-0 px-4 me-2 d-inline-flex align-items-center justify-content-center" style="height: 40px !important; padding-top: 0 !important; padding-bottom: 0 !important;" onclick="window.print()">
                            <i class="las la-print"></i>Print
                        </button>
                        <button type="submit" class="btn btn-primary rounded shadow-sm p-0 px-5 d-inline-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 40px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                            <i class="las la-save"></i>Finalize
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function calculateLiquidation() {
            const advance = parseFloat(document.getElementById('amountAdvanced').value) || 0;
            let totalExpenses = 0;
            document.querySelectorAll('.expense-input').forEach(input => {
                totalExpenses += parseFloat(input.value) || 0;
            });
            const balance = advance - totalExpenses;
            document.getElementById('totalExpenses').textContent = totalExpenses.toLocaleString('en-US', { minimumFractionDigits: 2 });
            document.getElementById('totalExpensesHidden').value = totalExpenses;
            document.getElementById('finalBalance').textContent = balance.toLocaleString('en-US', { minimumFractionDigits: 2 });
        }

        function resetForm() {
            if (confirm('Are you sure you want to clear all fields?')) {
                document.querySelectorAll('input').forEach(input => {
                    if (input.type !== 'hidden' && input.type !== 'date') input.value = '';
                });
                calculateLiquidation();
            }
        }

        document.querySelectorAll('input').forEach(i => i.addEventListener('input', calculateLiquidation));
    </script>
    @endpush
</x-app-layout>
