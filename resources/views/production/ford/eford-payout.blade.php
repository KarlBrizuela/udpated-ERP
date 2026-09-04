<x-app-layout :title="'E-FORD Payout'" :sidebar="'production'">
    @push('styles')
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <style>
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
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .customer-details,
        .order-details,
        .attachment-details {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
        }

        .customer-details h5,
        .order-details h5,
        .attachment-details h5 {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
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
            background: #fff;
        }

        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        .form-table thead {
            background: #ff0000;
            color: #fff;
        }

        .form-table th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid #ddd;
        }

        .form-table td {
            padding: 0.5rem;
            border: 1px solid #ddd;
            background: #fff;
        }

        .form-table input[type="text"],
        .form-table input[type="number"],
        .form-table input[type="date"] {
            width: 100%;
            border: none;
            padding: 0.5rem;
            background: transparent;
        }

        .form-table input[type="number"] {
            text-align: right;
        }

        .form-table input:focus {
            outline: 2px solid #ff0000;
            outline-offset: -2px;
            background: #fff;
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

        .badge-attachment {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        @media (max-width: 992px) {
            .customer-section {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }
    </style>
    @endpush

    <div class="container-fluid">
        <!-- Session Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-4" id="efordPayoutTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="create-tab" data-bs-toggle="tab" data-bs-target="#create-pane" type="button" role="tab" aria-controls="create-pane" aria-selected="true">
                    <i class="las la-plus-circle"></i> Create Report
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-pane" type="button" role="tab" aria-controls="history-pane" aria-selected="false">
                    <i class="las la-history"></i> Report History
                </button>
            </li>
        </ul>

        <div class="tab-content" id="efordPayoutTabContent">
            <!-- Create pane -->
            <div class="tab-pane fade show active" id="create-pane" role="tabpanel" aria-labelledby="create-tab">
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
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
                                <div class="document-title">E-FORD SALES SUMMARY REPORT</div>
                            </div>

                            <form id="efordPayoutForm" class="form-section" method="POST" action="{{ route('production.ford.eford-payout.store') }}" enctype="multipart/form-data">
                                @csrf
                                <!-- Report Period & Customer -->
                                <div class="customer-section">
                                    <div class="order-details">
                                        <h5>Report Information</h5>
                                        <div class="form-group" style="position: relative;">
                                             <label>Period:</label>
                                             <input type="text" name="period" id="formPeriod" placeholder="Select Period Range" required readonly style="background-color: #fff; cursor: pointer; padding-right: 35px;">
                                             <i class="las la-calendar" style="position: absolute; right: 12px; bottom: 12px; font-size: 1.25rem; color: #888; cursor: pointer; pointer-events: none;"></i>
                                        </div>
                                    </div>
                                    <div class="customer-details">
                                        <h5>Customer Selection</h5>
                                        <div class="form-group">
                                            <label>Customer:</label>
                                            <select name="customer_id" id="selectCustomer" class="form-control selectpicker" data-live-search="true" required>
                                                <option value="">Select a Customer</option>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->customer_id }}">
                                                        {{ $customer->customer_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="attachment-details">
                                        <h5>Attachments</h5>
                                        <div class="form-group">
                                            <label>Upload Supporting Documents (Multiple):</label>
                                            <input type="file" name="attachments[]" id="attachmentInput" multiple class="form-control" style="padding: 6px;">
                                            <small class="text-muted d-block mt-1">Allowed types: pdf, word, excel, images. Max 5MB per file.</small>
                                            <div id="attachmentList" class="mt-2 d-flex flex-column gap-1" style="max-height: 150px; overflow-y: auto;"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sales Table -->
                                <button type="button" class="btn-add-row" onclick="addRow()">
                                    <i class="las la-plus"></i> Add Row
                                </button>

                                <table class="form-table" id="salesTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">NO.</th>
                                            <th>ORDER NO.</th>
                                            <th>DATE</th>
                                            <th>SI NO.</th>
                                            <th>CUSTOMER</th>
                                            <th>AMOUNT</th>
                                            <th>FREIGHT</th>
                                            <th>GROSS SALES</th>
                                            <th>PAYMENT METHOD</th>
                                            <th style="width: 80px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="salesTableBody">
                                        <tr>
                                            <td style="text-align: center;">1</td>
                                            <td><input type="text" name="order_no[]" placeholder="ORDER No."></td>
                                            <td><input type="date" name="date[]"></td>
                                            <td><input type="text" name="si_no[]" placeholder="SI No."></td>
                                            <td><input type="text" name="customer[]" placeholder="Customer"></td>
                                            <td><input type="number" name="amount[]" placeholder="Amount" min="0" step="0.01" oninput="calculateRowGrossSales(this)"></td>
                                            <td><input type="number" name="freight[]" placeholder="Freight" min="0" step="0.01" oninput="calculateRowGrossSales(this)"></td>
                                            <td><input type="number" name="gross_sales[]" placeholder="Gross Sales" readonly></td>
                                            <td><input type="text" name="payment_method[]" placeholder="Payment Method"></td>
                                            <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- Form Actions -->
                                <div class="form-actions">
                                    <button type="button" class="btn btn-light" onclick="resetGeneratedReport()">
                                        <i class="las la-undo"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-danger text-white fw-bold">
                                        <i class="las la-check-circle"></i> Generate & Submit Report
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- History pane -->
            <div class="tab-pane fade" id="history-pane" role="tabpanel" aria-labelledby="history-tab">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="efordHistoryTable" class="display table table-bordered" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Report ID</th>
                                        <th>Period</th>
                                        <th>Customer</th>
                                        <th>Total Amount</th>
                                        <th>Total Freight</th>
                                        <th>Total Gross Sales</th>
                                        <th>Date Submitted</th>
                                        <th>Attachments</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $report)
                                    <tr>
                                        <td><strong>#{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                        <td>{{ $report->period }}</td>
                                        <td>{{ $report->customer->customer_name ?? 'N/A' }}</td>
                                        <td>₱{{ number_format($report->total_amount, 2) }}</td>
                                        <td>₱{{ number_format($report->total_freight, 2) }}</td>
                                        <td>₱{{ number_format($report->total_gross_sales, 2) }}</td>
                                        <td>{{ $report->created_at->timezone('Asia/Manila')->format('Y-m-d h:i A') }}</td>
                                        <td>
                                            @if($report->attachments && count($report->attachments) > 0)
                                                <div class="d-flex flex-column gap-1">
                                                    @foreach($report->attachments as $idx => $path)
                                                        <a href="{{ route('admin-finance.accounting.eford-payouts.download', ['id' => $report->id, 'index' => $idx]) }}" target="_blank" class="text-primary small text-decoration-none">
                                                            <i class="las la-paperclip"></i> File {{ $idx + 1 }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted small">None</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin-finance.accounting.eford-payouts.show', $report->id) }}" class="btn btn-primary btn-xs" target="_blank">
                                                <i class="las la-eye"></i> View Payout
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script>
        let rowCounter = 1;
        let customerUnpaidSIs = [];

        function calculateGrossSales(amount, freight) {
            const amt = parseFloat(amount) || 0;
            const frt = parseFloat(freight) || 0;
            return (amt + frt).toFixed(2);
        }

        function addRow(initialData = null) {
            rowCounter++;
            const tbody = document.getElementById('salesTableBody');
            const newRow = document.createElement('tr');
            
            let siCellHTML = '';
            if (customerUnpaidSIs && customerUnpaidSIs.length > 0) {
                let optionsHTML = '<option value="">Select SI No.</option>';
                customerUnpaidSIs.forEach(item => {
                    const selected = (initialData && initialData.si_no === item.si_no) ? 'selected' : '';
                    optionsHTML += `<option value="${item.si_no}" ${selected}>${item.si_no}</option>`;
                });
                siCellHTML = `<select name="si_no[]" class="form-control select-si-no" style="border:none; background:transparent; width:100%;" onchange="onSIRowChange(this)">${optionsHTML}</select>`;
            } else {
                const val = initialData ? initialData.si_no : '';
                siCellHTML = `<input type="text" name="si_no[]" placeholder="SI No." value="${val}">`;
            }

            const orderVal = initialData ? initialData.order_no : '';
            const dateVal = initialData ? initialData.date : '';
            const customerVal = initialData ? initialData.customer : '';
            const amountVal = initialData ? initialData.amount : '';
            const freightVal = initialData ? initialData.freight : '';
            const grossVal = initialData ? initialData.gross_sales : '';
            const payVal = initialData ? initialData.payment_method : '';

            newRow.innerHTML = `
                <td style="text-align: center;">${rowCounter}</td>
                <td><input type="text" name="order_no[]" placeholder="ORDER No." value="${orderVal}"></td>
                <td><input type="date" name="date[]" value="${dateVal}"></td>
                <td>${siCellHTML}</td>
                <td><input type="text" name="customer[]" placeholder="Customer" value="${customerVal}"></td>
                <td><input type="number" name="amount[]" placeholder="Amount" min="0" step="0.01" value="${amountVal}" oninput="calculateRowGrossSales(this)"></td>
                <td><input type="number" name="freight[]" placeholder="Freight" min="0" step="0.01" value="${freightVal}" oninput="calculateRowGrossSales(this)"></td>
                <td><input type="number" name="gross_sales[]" placeholder="Gross Sales" value="${grossVal}" readonly></td>
                <td><input type="text" name="payment_method[]" placeholder="Payment Method" value="${payVal}"></td>
                <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
            `;
            tbody.appendChild(newRow);

            if (initialData) {
                calculateRowGrossSales(newRow.querySelector('input[name="amount[]"]'));
            }
        }

        function onSIRowChange(selectElement) {
            const selectedSiNo = selectElement.value;
            const row = selectElement.closest('tr');
            if (!selectedSiNo) {
                row.querySelector('input[name="order_no[]"]').value = '';
                row.querySelector('input[name="date[]"]').value = '';
                row.querySelector('input[name="customer[]"]').value = '';
                row.querySelector('input[name="amount[]"]').value = '';
                row.querySelector('input[name="freight[]"]').value = '';
                row.querySelector('input[name="gross_sales[]"]').value = '';
                row.querySelector('input[name="payment_method[]"]').value = '';
                return;
            }

            const siData = customerUnpaidSIs.find(item => item.si_no === selectedSiNo);
            if (siData) {
                row.querySelector('input[name="order_no[]"]').value = siData.order_no || '';
                row.querySelector('input[name="date[]"]').value = siData.date || '';
                row.querySelector('input[name="customer[]"]').value = siData.customer || '';
                row.querySelector('input[name="amount[]"]').value = siData.amount || '';
                row.querySelector('input[name="freight[]"]').value = siData.freight || '';
                row.querySelector('input[name="gross_sales[]"]').value = siData.gross_sales || '';
                row.querySelector('input[name="payment_method[]"]').value = siData.payment_method || '';
                
                calculateRowGrossSales(row.querySelector('input[name="amount[]"]'));
            }
        }

        function removeRow(button) {
            const tbody = document.getElementById('salesTableBody');
            if (tbody.rows.length > 1) {
                button.closest('tr').remove();
                renumberRows();
            } else {
                alert('At least one row is required.');
            }
        }

        function renumberRows() {
            const tbody = document.getElementById('salesTableBody');
            const rows = tbody.querySelectorAll('tr');
            rows.forEach((row, index) => {
                row.querySelector('td:first-child').textContent = index + 1;
            });
            rowCounter = rows.length;
        }

        function calculateRowGrossSales(input) {
            const row = input.closest('tr');
            const amount = row.querySelector('input[name="amount[]"]').value || 0;
            const freight = row.querySelector('input[name="freight[]"]').value || 0;
            const grossSales = calculateGrossSales(amount, freight);
            row.querySelector('input[name="gross_sales[]"]').value = grossSales;
        }

        let accumulatedFiles = [];

        function resetGeneratedReport() {
            document.getElementById('efordPayoutForm').reset();
            accumulatedFiles = [];
            const attachmentList = document.getElementById('attachmentList');
            if (attachmentList) {
                attachmentList.innerHTML = '';
            }
            const customerSelect = document.getElementById('selectCustomer');
            if (customerSelect) {
                customerSelect.value = '';
                if (typeof $.fn.selectpicker === 'function') {
                    $(customerSelect).selectpicker('refresh');
                }
            }
            customerUnpaidSIs = [];

            // Reset table to 1 row
            const tbody = document.getElementById('salesTableBody');
            tbody.innerHTML = '';
            rowCounter = 0;
            addRow();
        }

        function removeAttachedFile(index) {
            accumulatedFiles.splice(index, 1);
            updateAttachmentInputAndUI();
        }

        function updateAttachmentInputAndUI() {
            const attachmentInput = document.getElementById('attachmentInput');
            const attachmentList = document.getElementById('attachmentList');
            if (!attachmentInput || !attachmentList) return;

            // Clear visual list
            attachmentList.innerHTML = '';

            // Create new DataTransfer object to sync files back to input
            const dataTransfer = new DataTransfer();

            accumulatedFiles.forEach((file, index) => {
                dataTransfer.items.add(file);

                const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                const fileDiv = document.createElement('div');
                fileDiv.className = 'd-flex align-items-center text-dark bg-light border rounded p-2 mb-1';
                fileDiv.style.fontSize = '0.85rem';
                fileDiv.innerHTML = `
                    <i class="las la-file-alt text-danger me-2" style="font-size: 1.25rem;"></i>
                    <span class="text-truncate flex-grow-1" style="max-width: 250px;" title="${file.name}">${file.name}</span>
                    <span class="badge bg-white text-muted ms-2 me-2">${sizeInMB} MB</span>
                    <button type="button" class="btn btn-link text-danger p-0 ms-auto border-0 bg-transparent" onclick="removeAttachedFile(${index})" style="line-height:1; vertical-align:middle;">
                        <i class="las la-times-circle" style="font-size: 1.25rem;"></i>
                    </button>
                `;
                attachmentList.appendChild(fileDiv);
            });

            // Sync files back to input
            attachmentInput.files = dataTransfer.files;
        }

        $(document).ready(function() {
            $('#efordHistoryTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 10
            });

            // Period date range picker
            $('#formPeriod').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'MMMM D, YYYY'
                }
            });

            $('#formPeriod').on('apply.daterangepicker', function(ev, picker) {
                const start = picker.startDate;
                const end = picker.endDate;
                let formatted = '';
                
                if (start.format('YYYY') === end.format('YYYY')) {
                    if (start.format('MMMM') === end.format('MMMM')) {
                        formatted = start.format('MMMM D') + '-' + end.format('D, YYYY');
                    } else {
                        formatted = start.format('MMMM D') + ' - ' + end.format('MMMM D, YYYY');
                    }
                } else {
                    formatted = start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY');
                }
                $(this).val(formatted);
            });

            $('#formPeriod').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            // File input selection preview
            const attachmentInput = document.getElementById('attachmentInput');
            if (attachmentInput) {
                attachmentInput.addEventListener('change', function() {
                    const files = Array.from(this.files);
                    
                    // Add only new files
                    files.forEach(newFile => {
                        const duplicate = accumulatedFiles.some(f => f.name === newFile.name && f.size === newFile.size);
                        if (!duplicate) {
                            accumulatedFiles.push(newFile);
                        }
                    });

                    updateAttachmentInputAndUI();
                });
            }

            // Customer selection handler
            const customerSelect = document.getElementById('selectCustomer');
            if (customerSelect) {
                $(customerSelect).on('change', function() {
                    const customerId = this.value;
                    if (!customerId) {
                        customerUnpaidSIs = [];
                        const tbody = document.getElementById('salesTableBody');
                        tbody.innerHTML = '';
                        rowCounter = 0;
                        addRow();
                        return;
                    }

                    const url = "{{ route('production.ford.eford-payout.unpaid-invoices', ':id') }}".replace(':id', customerId);
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            customerUnpaidSIs = data;
                            const tbody = document.getElementById('salesTableBody');
                            tbody.innerHTML = '';
                            rowCounter = 0;

                            if (data.length === 0) {
                                addRow();
                            } else {
                                data.forEach(item => {
                                    addRow(item);
                                });
                            }
                        })
                        .catch(err => {
                            console.error('Error fetching unpaid SIs:', err);
                            alert('Failed to load customer unpaid SIs.');
                        });
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
