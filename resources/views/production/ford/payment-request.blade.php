 <x-app-layout :title="'Payment Request'" :sidebar="'production'">
    <div class="container-fluid">
        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="las la-check-circle me-2"></i>
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="las la-exclamation-triangle me-2"></i>
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-4" id="paymentRequestTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="create-tab" data-bs-toggle="tab" data-bs-target="#create-pane" type="button" role="tab" aria-controls="create-pane" aria-selected="true">
                    <i class="las la-plus-circle"></i> Create Request
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-pane" type="button" role="tab" aria-controls="history-pane" aria-selected="false">
                    <i class="las la-history"></i> Request History
                </button>
            </li>
        </ul>

        <div class="tab-content" id="paymentRequestTabContent">
            <!-- Create Pane -->
            <div class="tab-pane fade show active" id="create-pane" role="tabpanel" aria-labelledby="create-tab">
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
                                <div class="document-title">PAYMENT REQUEST</div>
                            </div>

                            <form id="paymentRequestForm" class="form-section" method="POST" action="{{ route('production.ford.payment-request.store') }}" enctype="multipart/form-data">
                                @csrf
                                <!-- Payment and Order Details -->
                                <div class="customer-section">
                                    <div class="customer-details">
                                        <h5>Payment Information</h5>
                                        <div class="form-group">
                                            <label>Date:</label>
                                            <input type="date" name="date" id="formDate" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Request for Payment to:</label>
                                            <input type="text" name="payment_to" id="formPaymentTo" placeholder="Enter recipient name" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Payment for:</label>
                                            <input type="text" name="payment_for" id="formPaymentFor" placeholder="Enter payment purpose">
                                        </div>
                                    </div>
                                    <div class="order-details">
                                        <h5>Order Information</h5>
                                        <div class="form-group">
                                            <label>Due Date:</label>
                                            <input type="date" name="due_date" id="formDueDate">
                                        </div>
                                        <div class="form-group">
                                            <label>P.O. #:</label>
                                            <input type="text" name="po_number" id="formPONumber" placeholder="Enter P.O. number">
                                        </div>
                                        <div class="form-group">
                                            <label>Item Receipt #:</label>
                                            <input type="text" name="item_receipt" id="formItemReceipt" placeholder="Enter item receipt number">
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Details Table -->
                                <button type="button" class="btn-add-row" onclick="addRow()">
                                    <i class="las la-plus"></i> Add Row
                                </button>

                                <table class="form-table" id="paymentTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 120px;">DATE</th>
                                            <th style="width: 150px;">REF. NO.</th>
                                            <th>PARTICULARS</th>
                                            <th style="width: 150px;">AMOUNT</th>
                                            <th style="width: 80px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paymentTableBody">
                                        <tr>
                                            <td><input type="date" name="payment_date[]"></td>
                                            <td><input type="text" name="ref_no[]" placeholder="Ref. No."></td>
                                            <td><input type="text" name="particulars[]" placeholder="Particulars" required></td>
                                            <td><input type="number" name="amount[]" placeholder="Amount" min="0" step="0.01" required></td>
                                            <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- File Attachment Section -->
                                <div class="form-group mt-4" style="padding: 1rem; background-color: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                                    <label for="attachmentFile" style="font-weight: 600; margin-bottom: 0.5rem; display: block;">
                                        <i class="las la-paperclip"></i> Attach Supporting Document
                                    </label>
                                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                                        <input type="file" name="attachment_file" id="attachmentFile" style="flex: 1; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                                        <button type="button" class="btn btn-secondary" id="clearAttachmentBtn" onclick="clearAttachment()" style="padding: 0.5rem 1rem;" title="Clear file">
                                            <i class="las la-times"></i> Clear
                                        </button>
                                    </div>
                                    <small style="color: #666; margin-top: 0.5rem; display: block;">
                                        Supported formats: PDF, Word (DOC, DOCX), Excel (XLS, XLSX), Images (JPG, PNG) | Max size: 5MB
                                    </small>
                                    <div id="attachmentPreview" style="margin-top: 0.5rem; display: none;">
                                        <span style="color: #28a745; font-weight: 500;">
                                            <i class="las la-check-circle"></i> File selected: <span id="attachmentFileName"></span>
                                        </span>
                                    </div>
                                </div>

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
                        
                        <!-- Letter Preview -->
                        <div class="generated-letter-section mt-4" id="generatedLetterWrapper" style="display: none;">
                            <div class="card">
                                <div class="card-body">
                                    <div class="generated-letter" id="generatedLetter">
                                        <div class="memo-header text-center mb-4">
                                            <h3 class="font-weight-bold">PAYMENT REQUEST</h3>
                                        </div>
                                        
                                        <div class="row mb-4">
                                            <div class="col-sm-6">
                                                <div class="mb-2"><strong>Date:</strong> <span id="lblDate" class="blank-field"></span></div>
                                                <div class="mb-2"><strong>Payment to:</strong> <span id="lblPaymentTo" class="blank-field"></span></div>
                                                <div class="mb-2"><strong>Payment for:</strong> <span id="lblPaymentFor" class="blank-field"></span></div>
                                            </div>
                                            <div class="col-sm-6 text-right">
                                                <div class="mb-2"><strong>Due Date:</strong> <span id="lblDueDate" class="blank-field"></span></div>
                                                <div class="mb-2"><strong>PO#:</strong> <span id="lblPONumber" class="blank-field"></span></div>
                                                <div class="mb-2"><strong>Item Receipt#:</strong> <span id="lblItemReceipt" class="blank-field"></span></div>
                                            </div>
                                        </div>

                                        <table class="payment-table w-100 mb-4">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Ref. No.</th>
                                                    <th>Particulars</th>
                                                    <th class="text-right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="generatedTableBody">
                                                <!-- Populated via JS -->
                                            </tbody>
                                            <tfoot>
                                                <tr class="total-row">
                                                    <td colspan="3" class="text-right"><strong>TOTAL:</strong></td>
                                                    <td class="text-right"><strong id="lblTotalAmount">0.00</strong></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        
                                        <div class="signature-section d-flex justify-content-between mt-5">
                                            <div class="signature-box text-center">
                                                <div class="blank-line w-100 border-bottom border-dark mb-2" style="height:20px;"></div>
                                                <strong>Prepared By</strong>
                                                <div class="small text-muted">{{ auth()->user()->name }}</div>
                                            </div>
                                            <div class="signature-box text-center">
                                                <div class="blank-line w-100 border-bottom border-dark mb-2" style="height:20px;"></div>
                                                <strong>Checked By (Admin Manager)</strong>
                                                <div class="small text-muted">Awaiting Approval</div>
                                            </div>
                                            <div class="signature-box text-center">
                                                <div class="blank-line w-100 border-bottom border-dark mb-2" style="height:20px;"></div>
                                                <strong>Approved By (Production Manager)</strong>
                                                <div class="small text-muted">Awaiting Approval</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 text-right report-actions d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-secondary" onclick="backToForm()"><i class="las la-arrow-left"></i> Back</button>
                                        <button type="button" class="btn btn-info" onclick="printLetter()"><i class="las la-print"></i> Print Letter</button>
                                        <button type="button" class="btn btn-success" onclick="submitForm()"><i class="las la-check-circle"></i> Submit for Approval</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div> <!-- End Create Pane -->

            <!-- History Pane -->
            <div class="tab-pane fade" id="history-pane" role="tabpanel" aria-labelledby="history-tab">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="fs-20 mb-0 text-black">My Payment Requests History</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="paymentRequestsHistoryTable" class="display table table-bordered" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Ref #</th>
                                        <th>Date Created</th>
                                        <th>Pay To</th>
                                        <th>PO #</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests ?? [] as $req)
                                    <tr>
                                        <td><strong>PR-{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                        <td>{{ $req->date ? $req->date->format('Y-m-d') : $req->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $req->payment_to }}</td>
                                        <td>{{ $req->po_number ?? 'N/A' }}</td>
                                        <td>PhP {{ number_format($req->total_amount, 2) }}</td>
                                        <td>
                                            @php
                                                $status = $req->status;
                                                $badge = 'warning';
                                                $statusText = 'Pending Director Approval';
                                                
                                                if ($status === 'pending_admin_finance_approval') {
                                                    $badge = 'info';
                                                    $statusText = 'Pending Admin & Finance';
                                                } elseif ($status === 'approved') {
                                                    $badge = 'success';
                                                    $statusText = 'Approved';
                                                } elseif ($status === 'scheduled') {
                                                    $badge = 'primary';
                                                    $statusText = 'Scheduled';
                                                } elseif ($status === 'paid') {
                                                    $badge = 'success';
                                                    $statusText = 'Paid';
                                                } elseif ($status === 'rejected') {
                                                    $badge = 'danger';
                                                    $statusText = 'Rejected';
                                                }
                                            @endphp
                                            <span class="badge badge-{{ $badge }}">{{ $statusText }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('payment-requests.show', $req->id) }}" class="btn btn-primary btn-xs"><i class="las la-eye"></i> View Letter</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> <!-- End History Pane -->
        </div>

    </div>

    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
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

        .generated-letter-section {
            max-width: 1000px;
            margin: 0;
        }

        .generated-letter {
            background: #fff;
            border: 1px solid #ddd;
            padding: 40px;
            margin-top: 30px;
            min-height: 600px;
            font-family: 'Times New Roman', serif;
            color: #000;
        }

        .memo-header {
            margin-bottom: 20px;
        }

        .blank-field {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 200px;
            padding: 0 5px;
            margin: 0 5px;
            color: #000;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .payment-table th,
        .payment-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            color: #000;
        }

        .payment-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .payment-table .total-row {
            background-color: #FFD700;
            font-weight: bold;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }

        .signature-box {
            width: 30%;
        }

        .signature-box div {
            margin-bottom: 5px;
        }

        @media print {
            .sidebar,
            .header,
            .form-actions,
            .report-actions,
            .btn-add-row,
            .nav-tabs {
                display: none !important;
            }

            .order-form,
            .tab-content {
                display: none !important;
                box-shadow: none;
            }

            .generated-letter-section,
            #create-pane {
                display: block !important;
            }

            body {
                background-color: #fff;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#paymentRequestsHistoryTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 10
            });
        });

        // Function to format date nicely
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            return months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
        }

        // File attachment handling
        const attachmentFile = document.getElementById('attachmentFile');
        if (attachmentFile) {
            attachmentFile.addEventListener('change', function(e) {
                const file = e.target.files[0];
                const preview = document.getElementById('attachmentPreview');
                const fileName = document.getElementById('attachmentFileName');
                
                if (file) {
                    const maxSize = 5 * 1024 * 1024; // 5MB in bytes
                    if (file.size > maxSize) {
                        alert('File size exceeds 5MB limit. Please choose a smaller file.');
                        this.value = '';
                        preview.style.display = 'none';
                        return;
                    }
                    
                    const allowedTypes = [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'image/jpeg',
                        'image/png'
                    ];
                    
                    if (!allowedTypes.includes(file.type)) {
                        alert('Invalid file type. Allowed types: PDF, Word (DOC, DOCX), Excel (XLS, XLSX), Images (JPG, PNG)');
                        this.value = '';
                        preview.style.display = 'none';
                        return;
                    }
                    
                    fileName.textContent = file.name;
                    preview.style.display = 'block';
                } else {
                    preview.style.display = 'none';
                }
            });
        }

        function clearAttachment() {
            const attachmentFile = document.getElementById('attachmentFile');
            const preview = document.getElementById('attachmentPreview');
            
            if (attachmentFile) {
                attachmentFile.value = '';
                preview.style.display = 'none';
            }
        }

        function addRow() {
            const tbody = document.getElementById('paymentTableBody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="date" name="payment_date[]"></td>
                <td><input type="text" name="ref_no[]" placeholder="Ref. No."></td>
                <td><input type="text" name="particulars[]" placeholder="Particulars" required></td>
                <td><input type="number" name="amount[]" placeholder="Amount" min="0" step="0.01" required></td>
                <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
            `;
            tbody.appendChild(newRow);
        }

        function removeRow(button) {
            const tbody = document.getElementById('paymentTableBody');
            if (tbody.rows.length > 1) {
                button.closest('tr').remove();
            } else {
                alert('At least one row is required.');
            }
        }

        function updateGeneratedLetter() {
            const dateVal = document.getElementById('formDate').value;
            const dueDateVal = document.getElementById('formDueDate').value;
            const paymentToVal = document.getElementById('formPaymentTo').value;

            if (!dateVal || !paymentToVal) {
                alert('Please fill in Date and Payment to.');
                return;
            }

            if (dateVal && dueDateVal) {
                const currentDate = new Date(dateVal);
                const dueDate = new Date(dueDateVal);
                currentDate.setHours(0,0,0,0);
                dueDate.setHours(0,0,0,0);

                if (currentDate > dueDate) {
                    alert('Error: Cannot process. The Date is past the Due Date.');
                    return;
                }
            }

            const tbody = document.getElementById('paymentTableBody');
            const rows = tbody.querySelectorAll('tr');
            let hasValidRow = false;
            let missingRowData = false;
            
            rows.forEach(row => {
                const particulars = row.querySelector('input[name="particulars[]"]').value;
                const amount = row.querySelector('input[name="amount[]"]').value;
                
                if (particulars || amount) {
                    if (!particulars || !amount) {
                        missingRowData = true;
                    } else {
                        hasValidRow = true;
                    }
                }
            });

            if (missingRowData) {
                alert('Please complete Particulars and Amount for all entered rows.');
                return;
            }

            if (!hasValidRow) {
                alert('Please add at least one item entry.');
                return;
            }

            // Populate labels
            document.getElementById('lblDate').textContent = dateVal ? formatDate(dateVal) : '_____________';
            document.getElementById('lblPaymentTo').textContent = document.getElementById('formPaymentTo').value || '_____________';
            document.getElementById('lblPaymentFor').textContent = document.getElementById('formPaymentFor').value || '_____________';
            
            document.getElementById('lblDueDate').textContent = dueDateVal ? formatDate(dueDateVal) : '_____________';
            document.getElementById('lblPONumber').textContent = document.getElementById('formPONumber').value || '_____________';
            document.getElementById('lblItemReceipt').textContent = document.getElementById('formItemReceipt').value || '_____________';

            // Populate table
            const generatedTbody = document.getElementById('generatedTableBody');
            generatedTbody.innerHTML = '';
            
            let totalAmount = 0;

            rows.forEach(row => {
                const date = row.querySelector('input[name="payment_date[]"]').value;
                const refNo = row.querySelector('input[name="ref_no[]"]').value;
                const particulars = row.querySelector('input[name="particulars[]"]').value;
                const amount = parseFloat(row.querySelector('input[name="amount[]"]').value) || 0;

                totalAmount += amount;

                if(date || refNo || particulars || amount > 0) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${date ? formatDate(date) : ''}</td>
                        <td>${refNo}</td>
                        <td>${particulars}</td>
                        <td class="text-right">${amount.toFixed(2)}</td>
                    `;
                    generatedTbody.appendChild(tr);
                }
            });

            if(generatedTbody.innerHTML === '') {
                 generatedTbody.innerHTML = `<tr><td colspan="4" class="text-center">No particulars added</td></tr>`;
            }

            document.getElementById('lblTotalAmount').textContent = totalAmount.toFixed(2);
            document.querySelector('.order-form').style.display = 'none';
            document.getElementById('generatedLetterWrapper').style.display = 'block';
            
            document.getElementById('generatedLetterWrapper').scrollIntoView({ behavior: 'smooth' });
        }

        function resetGeneratedLetter() {
            document.getElementById('paymentRequestForm').reset();
            document.getElementById('generatedLetterWrapper').style.display = 'none';
            
            const preview = document.getElementById('attachmentPreview');
            if (preview) {
                preview.style.display = 'none';
            }
            
            const tbody = document.getElementById('paymentTableBody');
            while (tbody.rows.length > 1) {
                tbody.deleteRow(1);
            }
        }

        function backToForm() {
            document.querySelector('.order-form').style.display = 'block';
            document.getElementById('generatedLetterWrapper').style.display = 'none';
        }

        function printLetter() {
            window.print();
        }

        function submitForm() {
            const form = document.getElementById('paymentRequestForm');
            if(form.reportValidity()) {
                form.submit();
            }
        }
    </script>
    @endpush

</x-app-layout>
