<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        #mrTable thead th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; padding: 12px 15px; font-weight: 600; }
        #mrTable tbody td { padding: 12px 15px; border-bottom: 1px solid #dee2e6; vertical-align: middle; }

        /* Modal form styles */
        .req-form-header { border-bottom: 2px solid #dee2e6; padding-bottom: 1rem; margin-bottom: 1.25rem; }
        .company-logo-circle {
            width: 52px; height: 52px; background: #ff0000; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.4rem; font-weight: bold; flex-shrink: 0;
        }
        .form-document-title { font-size: 1.25rem; font-weight: 700; text-transform: uppercase; text-align: center; }
        .requisition-table { width: 100%; border-collapse: collapse; }
        .requisition-table thead { background: #ff0000; color: #fff; }
        .requisition-table th, .requisition-table td { padding: 0.5rem 0.6rem; border: 1px solid #dee2e6; font-size: 13px; }
        .requisition-table .form-control { font-size: 13px; padding: 0.3rem 0.5rem; border: none; background: transparent; }
        .form-instructions { background: #f8f9fa; padding: 0.75rem 1rem; border-radius: 4px; font-size: 0.8rem; color: #666; border-left: 3px solid #ff0000; margin-top: 1rem; }
        .badge-pending    { background-color: #ff9800; color: #fff; }
        .badge-processing { background-color: #2196f3; color: #fff; }
        .badge-completed  { background-color: #4caf50; color: #fff; }
        .badge-rejected   { background-color: #f44336; color: #fff; }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h4 class="fs-20 mb-0 text-black">Materials / Supplies Requisition</h4>
                    <button type="button" class="btn btn-primary rounded d-flex align-items-center gap-1"
                            style="background:#ff0000;border:none;"
                            data-bs-toggle="modal" data-bs-target="#addRequisitionModal">
                        <i class="las la-plus"></i> Add New Material Requisition Form
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="mrTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Req #</th>
                                    <th>Date</th>
                                    <th>Requested By</th>
                                    <th>Department</th>
                                    <th>P.O. #</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requisitions as $req)
                                <tr id="row-{{ $req->id }}">
                                    <td class="fw-bold"><strong>{{ $req->requisition_no }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($req->date)->format('Y-m-d') }}</td>
                                    <td>{{ $req->user->name ?? 'N/A' }}</td>
                                    <td>{{ $req->department }}</td>
                                    <td>{{ $req->po_number ?: '—' }}</td>
                                    <td><span class="badge badge-{{ $req->status === 'pending' ? 'warning' : 'info' }}">{{ ucfirst($req->status) }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <button class="btn btn-primary shadow btn-sm mb-0 px-2 py-1" onclick="viewRequisition({{ $req->id }})" title="View Details"><i class="las la-eye"></i></button>
                                            <button class="btn btn-danger shadow btn-sm mb-0 px-2 py-1" onclick="confirmDeleteRequisition({{ $req->id }})" title="Delete Requisition"><i class="las la-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                {{-- Populated from DB once backend is wired up --}}
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
    {{-- Add New Requisition Modal --}}
    <div class="modal fade" id="addRequisitionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">New Materials / Supplies Requisition Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Header --}}
                    <div class="req-form-header">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="company-logo-circle">C</div>
                            <div>
                                <div class="fw-bold text-uppercase" style="font-size:1rem;">Claretian Communications Foundation Inc.</div>
                                <div class="text-muted small">8 Mayumi St., UP Village, Diliman, Quezon City &nbsp;|&nbsp; Tel: 921-3984</div>
                            </div>
                        </div>
                        <div class="form-document-title">Materials / Supplies Requisition</div>
                        <div class="text-center text-muted" style="font-size:0.78rem;">CCFI-AD Form # (January 01, 2015)</div>
                    </div>

                    {{-- Info Row --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Requisition No.</label>
                            <input type="text" class="form-control" id="reqNo" placeholder="Auto-generated" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="reqDate" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Department <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reqDepartment" placeholder="e.g. Finance" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Supplier</label>
                            <input type="text" class="form-control" id="reqSupplier" placeholder="Supplier name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">P.O. #</label>
                            <input type="text" class="form-control" id="reqPO" placeholder="Purchase Order No.">
                        </div>
                    </div>

                    {{-- Add Row Button --}}
                    <button type="button" class="btn btn-sm mb-2 d-flex align-items-center gap-1" style="background:#ff0000;color:#fff;border:none;" onclick="addReqRow()">
                        <i class="las la-plus"></i> Add Row
                    </button>

                    {{-- Items Table --}}
                    <div class="table-responsive">
                        <table class="requisition-table">
                            <thead>
                                <tr>
                                    <th style="width:80px;">QTY.</th>
                                    <th style="width:100px;">UNIT</th>
                                    <th>DESCRIPTION / ITEM</th>
                                    <th style="width:160px;">SUPPLIER 1 PRICE</th>
                                    <th style="width:160px;">SUPPLIER 2 PRICE</th>
                                    <th style="width:160px;">SUPPLIER 3 PRICE</th>
                                    <th style="width:50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="reqTableBody">
                                <tr>
                                    <td><input type="number" class="form-control" step="0.01" min="0"></td>
                                    <td><input type="text" class="form-control" placeholder="pcs, box..."></td>
                                    <td><input type="text" class="form-control" placeholder="Item description"></td>
                                    <td><input type="text" class="form-control" placeholder="0.00"></td>
                                    <td><input type="text" class="form-control" placeholder="0.00"></td>
                                    <td><input type="text" class="form-control" placeholder="0.00"></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-xs" onclick="removeReqRow(this)"><i class="las la-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-instructions mt-3">
                        <strong>Instructions:</strong> Fill up in triplicate — Original: Acctg. Dept. &nbsp;|&nbsp; Duplicate: General Services &nbsp;|&nbsp; Triplicate: Division/Employee
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning text-white" onclick="printRequisition()"><i class="las la-print me-1"></i>Print</button>
                    <button type="button" class="btn btn-primary" style="background:#ff0000;border:none;" id="saveRequisitionBtn">
                        <i class="las la-save me-1"></i>Save Requisition
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- View Requisition Modal --}}
    <div class="modal fade" id="viewRequisitionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">View Requisition Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Header --}}
                    <div class="req-form-header">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="company-logo-circle">C</div>
                            <div>
                                <div class="fw-bold text-uppercase" style="font-size:1rem;">Claretian Communications Foundation Inc.</div>
                                <div class="text-muted small">8 Mayumi St., UP Village, Diliman, Quezon City &nbsp;|&nbsp; Tel: 921-3984</div>
                            </div>
                        </div>
                        <div class="form-document-title">Materials / Supplies Requisition</div>
                        <div class="text-center text-muted" style="font-size:0.78rem;">CCFI-AD Form # (January 01, 2015)</div>
                    </div>

                    {{-- Info Row --}}
                    <div class="row border p-3 rounded mb-4" style="background-color: #f8f9fa;">
                        <div class="col-md-4 mb-2"><strong>Requisition No:</strong> <span id="viewReqNo" class="text-primary fw-bold"></span></div>
                        <div class="col-md-4 mb-2"><strong>Date:</strong> <span id="viewReqDate"></span></div>
                        <div class="col-md-4 mb-2"><strong>Requested By:</strong> <span id="viewReqUser"></span></div>
                        <div class="col-md-4"><strong>Department:</strong> <span id="viewReqDept"></span></div>
                        <div class="col-md-4"><strong>Supplier:</strong> <span id="viewReqSupplier"></span></div>
                        <div class="col-md-4"><strong>P.O. #:</strong> <span id="viewReqPO"></span></div>
                    </div>

                    {{-- Items Table --}}
                    <div class="table-responsive">
                        <table class="requisition-table">
                            <thead>
                                <tr>
                                    <th style="width:80px;">QTY.</th>
                                    <th style="width:100px;">UNIT</th>
                                    <th>DESCRIPTION / ITEM</th>
                                    <th style="width:160px;">SUPPLIER 1 PRICE</th>
                                    <th style="width:160px;">SUPPLIER 2 PRICE</th>
                                    <th style="width:160px;">SUPPLIER 3 PRICE</th>
                                </tr>
                            </thead>
                            <tbody id="viewReqItemsTable"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning text-white" onclick="printRequisition()"><i class="las la-print me-1"></i>Print</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteRequisitionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div style="font-size:3rem;color:#f44336;"><i class="las la-exclamation-circle"></i></div>
                    <h5 class="fw-bold mt-2">Delete Requisition?</h5>
                    <p class="text-muted mb-0">Are you sure you want to delete this requisition permanently?</p>
                    <input type="hidden" id="deleteReqId">
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-4" onclick="deleteRequisition()">Delete</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Success Confirmation Modal --}}
    <div class="modal fade" id="requisitionSuccessModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div style="font-size:3rem;color:#4caf50;"><i class="las la-check-circle"></i></div>
                    <h5 class="fw-bold mt-2">Requisition Saved!</h5>
                    <p class="text-muted mb-0" id="successModalMsg">The material requisition has been successfully recorded.</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Validation Error Modal --}}
    <div class="modal fade" id="requisitionValidationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div style="font-size:3rem;color:#f44336;"><i class="las la-exclamation-circle"></i></div>
                    <h5 class="fw-bold mt-2">Missing Required Fields</h5>
                    <p class="text-muted mb-0">Please fill in at least the <strong>Date</strong> and <strong>Department</strong> fields before saving.</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            window.mrTable = $('#mrTable').DataTable({
                pageLength: 25,
                responsive: true,
                language: { emptyTable: "No requisitions found." }
            });
        });

        let reqCounter = 1;

        function addReqRow() {
            const row = `
                <tr>
                    <td><input type="number" class="form-control" step="0.01" min="0"></td>
                    <td><input type="text" class="form-control" placeholder="pcs, box..."></td>
                    <td><input type="text" class="form-control" placeholder="Item description"></td>
                    <td><input type="text" class="form-control" placeholder="0.00"></td>
                    <td><input type="text" class="form-control" placeholder="0.00"></td>
                    <td><input type="text" class="form-control" placeholder="0.00"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-xs" onclick="removeReqRow(this)"><i class="las la-trash"></i></button>
                    </td>
                </tr>`;
            document.getElementById('reqTableBody').insertAdjacentHTML('beforeend', row);
        }

        function removeReqRow(btn) {
            if (document.querySelectorAll('#reqTableBody tr').length > 1) {
                btn.closest('tr').remove();
            }
        }

        function printRequisition() {
            window.print();
        }

        document.getElementById('saveRequisitionBtn').addEventListener('click', function() {
            const date      = document.getElementById('reqDate').value;
            const dept      = document.getElementById('reqDepartment').value.trim();
            const supplier  = document.getElementById('reqSupplier').value.trim();
            const po        = document.getElementById('reqPO').value.trim();

            if (!date || !dept) {
                new bootstrap.Modal(document.getElementById('requisitionValidationModal')).show();
                return;
            }

            // Gather items
            const items = [];
            document.querySelectorAll('#reqTableBody tr').forEach(row => {
                const inputs = row.querySelectorAll('input');
                const qty = inputs[0].value;
                const unit = inputs[1].value;
                const desc = inputs[2].value;
                
                if (qty && desc) {
                    items.push({
                        qty: qty,
                        unit: unit,
                        description: desc,
                        supplier1_price: inputs[3].value || null,
                        supplier2_price: inputs[4].value || null,
                        supplier3_price: inputs[5].value || null
                    });
                }
            });

            if (items.length === 0) {
                alert('Please add at least one item with a quantity and description.');
                return;
            }

            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="las la-spinner la-spin"></i> Saving...';
            btn.disabled = true;

            fetch('{{ route("admin-finance.accounting.materials-requisition.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    date: date,
                    department: dept,
                    supplier: supplier,
                    po_number: po,
                    items: items
                })
            })
            .then(response => response.json())
            .then(data => {
                btn.innerHTML = originalText;
                btn.disabled = false;

                if (data.success) {
                    // Add row to DataTable
                    const newRow = window.mrTable.row.add([
                        '<strong>' + data.requisition.requisition_no + '</strong>',
                        data.requisition.date,
                        data.requisition.requested_by,
                        data.requisition.department,
                        data.requisition.po_number || '—',
                        '<span class="badge badge-warning">Pending</span>',
                        `<div class="d-flex align-items-center gap-1">
                             <button class="btn btn-primary shadow btn-sm mb-0 px-2 py-1" onclick="viewRequisition(${data.requisition.id})" title="View Details"><i class="las la-eye"></i></button>
                             <button class="btn btn-danger shadow btn-sm mb-0 px-2 py-1" onclick="confirmDeleteRequisition(${data.requisition.id})" title="Delete Requisition"><i class="las la-trash"></i></button>
                         </div>`
                    ]).draw(false).node();
                    $(newRow).attr('id', 'row-' + data.requisition.id);

                    // Close form modal, show success modal
                    bootstrap.Modal.getInstance(document.getElementById('addRequisitionModal')).hide();
                    document.getElementById('successModalMsg').textContent = data.message;
                    new bootstrap.Modal(document.getElementById('requisitionSuccessModal')).show();
                    
                    // Reset fields
                    document.getElementById('reqDepartment').value = '';
                    document.getElementById('reqSupplier').value = '';
                    document.getElementById('reqPO').value = '';
                    document.getElementById('reqDate').value = '{{ date("Y-m-d") }}';
                    // keep 1 blank row
                    document.getElementById('reqTableBody').innerHTML = `
                        <tr>
                            <td><input type="number" class="form-control" step="0.01" min="0"></td>
                            <td><input type="text" class="form-control" placeholder="pcs, box..."></td>
                            <td><input type="text" class="form-control" placeholder="Item description"></td>
                            <td><input type="text" class="form-control" placeholder="0.00"></td>
                            <td><input type="text" class="form-control" placeholder="0.00"></td>
                            <td><input type="text" class="form-control" placeholder="0.00"></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-xs" onclick="removeReqRow(this)"><i class="las la-trash"></i></button>
                            </td>
                        </tr>`;
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                console.error(err);
                alert('An error occurred while saving.');
            });
        });

        function viewRequisition(id) {
            fetch(`/admin-finance/accounting/materials-requisition/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const req = data.requisition;
                        document.getElementById('viewReqNo').textContent = req.requisition_no;
                        document.getElementById('viewReqDate').textContent = req.date;
                        document.getElementById('viewReqDept').textContent = req.department;
                        document.getElementById('viewReqUser').textContent = req.user ? req.user.name : 'N/A';
                        document.getElementById('viewReqSupplier').textContent = req.supplier || 'N/A';
                        document.getElementById('viewReqPO').textContent = req.po_number || 'N/A';
                        
                        let itemsHtml = '';
                        req.items.forEach(item => {
                            itemsHtml += `<tr>
                                <td>${item.qty}</td>
                                <td>${item.unit || ''}</td>
                                <td>${item.description}</td>
                                <td>${item.supplier1_price || '-'}</td>
                                <td>${item.supplier2_price || '-'}</td>
                                <td>${item.supplier3_price || '-'}</td>
                            </tr>`;
                        });
                        document.getElementById('viewReqItemsTable').innerHTML = itemsHtml;
                        new bootstrap.Modal(document.getElementById('viewRequisitionModal')).show();
                    } else {
                        alert('Could not fetch requisition details.');
                    }
                })
                .catch(err => {
                    console.error('Error fetching requisition details:', err);
                    alert('An error occurred.');
                });
        }

        function confirmDeleteRequisition(id) {
            document.getElementById('deleteReqId').value = id;
            new bootstrap.Modal(document.getElementById('deleteRequisitionModal')).show();
        }

        function deleteRequisition() {
            const id = document.getElementById('deleteReqId').value;
            fetch(`/admin-finance/accounting/materials-requisition/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                bootstrap.Modal.getInstance(document.getElementById('deleteRequisitionModal')).hide();
                if (data.success) {
                    const rowNode = document.getElementById(`row-${id}`);
                    if (rowNode) {
                        window.mrTable.row(rowNode).remove().draw(false);
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred.');
            });
        }
    </script>
    @endpush
</x-app-layout>