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
        .voucher-table td { padding: 0.5rem; border: 1px solid #ddd; position: relative; }
        .voucher-table tfoot { background: #f8f9fa; font-weight: 600; }

        .btn-add-row {
            background: #ff0000; color: #fff; border: none;
            padding: 0.5rem 1rem; border-radius: 4px; margin-bottom: 1rem;
        }

        .acct-dropdown-list {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1050;
            background: #fff;
            border: 1px solid #ccc;
            border-top: none;
            max-height: 250px;
            overflow-y: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 0 0 4px 4px;
        }

        .acct-dropdown-item {
            padding: 8px 12px;
            cursor: pointer;
            font-size: 0.85rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f0f0f0;
        }

        .acct-dropdown-item:hover {
            background: #f5f5f5;
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

                <form action="{{ route('admin-finance.check-voucher.store') }}" method="POST">
                    @csrf
                    <div class="form-info-row">
                        <div class="form-info-item" style="flex: 2;">
                            <label>Payee:</label>
                            <div style="position: relative; flex: 1;">
                                <input type="text" class="form-control" id="payeeSearch"
                                       placeholder="Search vendor, supplier, or employee..."
                                       autocomplete="off">
                                <input type="hidden" name="payee" id="payeeHidden" required>
                                <div id="payeeDropdown" style="
                                    display: none;
                                    position: absolute;
                                    top: 100%;
                                    left: 0;
                                    right: 0;
                                    z-index: 1050;
                                    background: #fff;
                                    border: 1px solid #ccc;
                                    border-top: none;
                                    max-height: 260px;
                                    overflow-y: auto;
                                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                                "></div>
                            </div>
                        </div>
                        <div class="form-info-item">
                            <label>Check No.:</label>
                            <input type="text" class="form-control" name="check_no" id="checkNo" required>
                        </div>
                        <div class="form-info-item">
                            <label>Date:</label>
                            <input type="date" class="form-control" name="date" id="voucherDate" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary rounded shadow-sm p-0 px-5 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 22px;" onclick="addRow()">
                        <i class="las la-plus"></i>Add Row
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
                                <td style="position: relative;">
                                    <input type="text" class="form-control border-0 account-search-input"
                                           name="items[0][account_name]"
                                           placeholder="Search account (e.g. Office, Petty Cash, BDO)..."
                                           autocomplete="off" required>
                                    <div class="acct-dropdown-list"></div>
                                </td>
                                <td><input type="number" class="form-control border-0 text-end debit-input" name="items[0][debit]" step="0.01" value="0"></td>
                                <td><input type="number" class="form-control border-0 text-end credit-input" name="items[0][credit]" step="0.01" value="0"></td>
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

                    <input type="hidden" name="amount" id="totalAmountHidden" value="0">

                    <div class="mb-4">
                        <label class="fw-bold">MEMO:</label>
                        <textarea class="form-control" name="memo" rows="3"></textarea>
                    </div>

                    <div class="form-actions d-flex justify-content-between gap-2">
                        <a href="{{ route('admin-finance.check-voucher') }}" class="btn btn-primary rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                            <i class="las la-arrow-left me-1"></i>Back to List
                        </a>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="height: 40px !important; padding-top: 0 !important; padding-bottom: 0 !important;" onclick="window.print()"><i class="las la-print me-1"></i>Print</button>
                            <button type="submit" class="btn btn-primary rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;">Save & Post Voucher</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // ---- CHART OF ACCOUNTS DATA ----
        const accountsData = [
            @if(isset($accounts))
                @foreach($accounts as $acc)
                {
                    id: {{ $acc->id }},
                    code: '{{ addslashes($acc->code) }}',
                    name: '{{ addslashes($acc->name) }}',
                    type: '{{ addslashes($acc->type ?? "Account") }}',
                    category: '{{ addslashes($acc->category ?? "General") }}',
                    label: '{{ addslashes($acc->code . " · " . $acc->name) }}',
                    value: '{{ addslashes($acc->name) }}'
                },
                @endforeach
            @endif
        ];

        const accountGroupColors = {
            'Asset': '#1b5e20',
            'Expense': '#c62828',
            'Liability': '#e65100',
            'Income': '#1565c0',
            'Equity': '#4a148c'
        };

        // ---- TOTALS ----
        function calculateTotals() {
            let debit = 0, credit = 0;
            document.querySelectorAll('.debit-input').forEach(i => debit += parseFloat(i.value) || 0);
            document.querySelectorAll('.credit-input').forEach(i => credit += parseFloat(i.value) || 0);
            document.getElementById('totalDebit').textContent = debit.toLocaleString('en-US', {minimumFractionDigits: 2});
            document.getElementById('totalCredit').textContent = credit.toLocaleString('en-US', {minimumFractionDigits: 2});
            document.getElementById('totalAmountHidden').value = Math.max(debit, credit);
        }

        // ---- ACCOUNT DROPDOWN BINDING ----
        function bindAccountInputEvents(inputElem) {
            const container = inputElem.parentElement;
            const dropdown = container.querySelector('.acct-dropdown-list');

            function renderAccountDropdown(items) {
                if (!items.length) {
                    dropdown.innerHTML = '<div style="padding: 10px 12px; color: #999; font-size: 0.82rem;">No matching accounts found.</div>';
                    dropdown.style.display = 'block';
                    return;
                }

                const groups = {};
                items.forEach(item => {
                    const groupName = item.type || 'General Accounts';
                    if (!groups[groupName]) groups[groupName] = [];
                    groups[groupName].push(item);
                });

                let html = '';
                Object.keys(groups).forEach(group => {
                    const color = accountGroupColors[group] || '#444';
                    html += `<div style="padding: 4px 10px; font-size: 0.72rem; font-weight: 700; color: ${color}; background: #f8f9fa; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #eee;">${group}</div>`;
                    groups[group].forEach(item => {
                        const safeVal = item.value.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
                        html += `
                            <div class="acct-dropdown-item" data-value="${safeVal}">
                                <span class="fw-semibold text-dark" style="font-size: 0.85rem;">${item.name}</span>
                                <span class="badge bg-light text-muted border ms-2" style="font-size: 0.75rem; font-family: monospace;">${item.code}</span>
                            </div>`;
                    });
                });

                dropdown.innerHTML = html;
                dropdown.style.display = 'block';

                // Add click listener to options
                dropdown.querySelectorAll('.acct-dropdown-item').forEach(opt => {
                    opt.addEventListener('click', function() {
                        const selectedVal = this.getAttribute('data-value');
                        inputElem.value = selectedVal;
                        dropdown.style.display = 'none';
                    });
                });
            }

            function filterAccounts(query) {
                const q = query.toLowerCase().trim();
                if (!q) return accountsData;
                const terms = q.split(/\s+/).filter(Boolean);
                return accountsData.filter(a => {
                    const fullText = (a.name + ' ' + a.code + ' ' + a.category + ' ' + a.type).toLowerCase();
                    return terms.every(t => fullText.includes(t));
                });
            }

            inputElem.addEventListener('input', function() {
                const q = this.value.trim();
                if (!q) {
                    dropdown.style.display = 'none';
                    return;
                }
                renderAccountDropdown(filterAccounts(q));
            });

            inputElem.addEventListener('focus', function() {
                const q = this.value.trim();
                renderAccountDropdown(filterAccounts(q));
            });
        }

        // Initialize first row
        document.querySelectorAll('.account-search-input').forEach(bindAccountInputEvents);

        function addRow() {
            const index = document.querySelectorAll('#voucherTableBody tr').length;
            const row = document.createElement('tr');
            row.innerHTML =
                '<td style="position: relative;"><input type="text" class="form-control border-0 account-search-input" name="items[' + index + '][account_name]" placeholder="Search account (e.g. Office, Petty Cash, BDO)..." autocomplete="off" required><div class="acct-dropdown-list"></div></td>' +
                '<td><input type="number" class="form-control border-0 text-end debit-input" name="items[' + index + '][debit]" step="0.01" value="0"></td>' +
                '<td><input type="number" class="form-control border-0 text-end credit-input" name="items[' + index + '][credit]" step="0.01" value="0"></td>' +
                '<td class="text-center"><button type="button" class="btn btn-danger btn-xs" onclick="removeRow(this)"><i class="las la-trash"></i></button></td>';
            document.getElementById('voucherTableBody').appendChild(row);

            const newInput = row.querySelector('.account-search-input');
            bindAccountInputEvents(newInput);

            row.querySelectorAll('input').forEach(i => i.addEventListener('input', calculateTotals));
        }

        function removeRow(btn) {
            if (document.querySelectorAll('#voucherTableBody tr').length > 1) {
                btn.closest('tr').remove();
                calculateTotals();
            }
        }

        document.getElementById('voucherTableBody').addEventListener('input', calculateTotals);

        // ---- PAYEE SEARCH ----
        const payeeData = [
            @foreach($vendors as $v)
            { label: '{{ addslashes($v->vendor_name) }}', value: '{{ addslashes($v->vendor_name) }}', group: 'Vendor', code: '{{ $v->vendor_code }}' },
            @endforeach
            @foreach($suppliers as $s)
            { label: '{{ addslashes($s->company_name) }}', value: '{{ addslashes($s->company_name) }}', group: 'Supplier', code: '{{ $s->supplier_code }}' },
            @endforeach
            @foreach($employees as $e)
            { label: '{{ addslashes($e->full_name) }}', value: '{{ addslashes($e->full_name) }}', group: 'Employee', code: '{{ $e->employee_number ?? "" }}' },
            @endforeach
        ];

        const groupColors = { Vendor: '#1a73e8', Supplier: '#e65100', Employee: '#2e7d32' };
        const payeeSearch   = document.getElementById('payeeSearch');
        const payeeHidden   = document.getElementById('payeeHidden');
        const payeeDropdown = document.getElementById('payeeDropdown');

        function renderDropdown(items) {
            if (!items.length) {
                payeeDropdown.innerHTML = '<div style="padding:10px 14px;color:#999;font-size:0.85rem;">No results found.</div>';
                payeeDropdown.style.display = 'block';
                return;
            }
            const groups = {};
            items.forEach(item => {
                if (!groups[item.group]) groups[item.group] = [];
                groups[item.group].push(item);
            });
            let html = '';
            Object.keys(groups).forEach(function(group) {
                const color = groupColors[group] || '#666';
                html += '<div style="padding:4px 10px;font-size:0.72rem;font-weight:700;color:' + color + ';background:#f9f9f9;text-transform:uppercase;letter-spacing:0.5px;border-bottom:1px solid #eee;">' + group + '</div>';
                groups[group].forEach(function(item) {
                    const codeHtml = item.code ? '<span style="font-size:0.75rem;color:#999;margin-left:6px;">' + item.code + '</span>' : '';
                    const safeVal = item.value.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
                    html += '<div style="padding:8px 14px;cursor:pointer;font-size:0.875rem;display:flex;align-items:center;border-bottom:1px solid #f0f0f0;" onmouseenter="this.style.background=\'#f5f5f5\'" onmouseleave="this.style.background=\'\'" onclick="selectPayee(\'' + safeVal + '\')">' + item.label + codeHtml + '</div>';
                });
            });
            payeeDropdown.innerHTML = html;
            payeeDropdown.style.display = 'block';
        }

        payeeSearch.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            payeeHidden.value = this.value;
            if (!q) { payeeDropdown.style.display = 'none'; return; }
            renderDropdown(payeeData.filter(function(d) { return d.label.toLowerCase().includes(q); }));
        });

        payeeSearch.addEventListener('focus', function() {
            if (this.value.trim().length > 0) {
                const q = this.value.toLowerCase().trim();
                renderDropdown(payeeData.filter(function(d) { return d.label.toLowerCase().includes(q); }));
            }
        });

        function selectPayee(value) {
            payeeSearch.value = value;
            payeeHidden.value = value;
            payeeDropdown.style.display = 'none';
        }

        // Global click listener to close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!payeeSearch.contains(e.target) && !payeeDropdown.contains(e.target)) {
                payeeDropdown.style.display = 'none';
                if (!payeeHidden.value && payeeSearch.value.trim()) {
                    payeeHidden.value = payeeSearch.value.trim();
                }
            }

            document.querySelectorAll('.account-search-input').forEach(input => {
                const container = input.parentElement;
                const dropdown = container.querySelector('.acct-dropdown-list');
                if (dropdown && !input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
