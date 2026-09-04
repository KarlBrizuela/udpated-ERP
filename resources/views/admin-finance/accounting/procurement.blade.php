<x-app-layout :title="'Purchasing & Procurement'" :sidebar="$sidebar ?? 'admin-finance'" :role="$role ?? 'Finance Manager'">
    @push('styles')
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        /* Select2 theme overrides for consistent form height and border */
        .select2-container--default .select2-selection--single {
            border: 1px solid #cbd5e1 !important;
            height: 38px !important;
            border-radius: 4px !important;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
            padding-left: 12px !important;
            font-size: 0.85rem !important;
            color: #0f172a !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default .select2-selection--single:focus {
            border-color: #D9251C !important;
            box-shadow: 0 0 0 2px rgba(217, 37, 28, 0.1) !important;
        }
        .select2-dropdown {
            border-color: #cbd5e1 !important;
            border-radius: 4px !important;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border-color: #cbd5e1 !important;
            border-radius: 4px !important;
            height: 32px !important;
            font-size: 0.82rem !important;
        }
        /* Widescreen Spacing Override */
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
        }

        /* Segmented Pills Switcher */
        .custom-ap-pills {
            gap: 8px;
            display: flex;
            flex-wrap: wrap;
        }
        .custom-ap-pills .nav-item {
            margin-bottom: 4px;
        }
        .custom-ap-pills .nav-link {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #475569 !important;
            font-weight: 600 !important;
            font-size: 0.82rem !important;
            border-radius: 6px !important;
            padding: 8px 16px !important;
            transition: all 0.15s ease-in-out !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;
            display: flex;
            align-items: center;
        }
        .custom-ap-pills .nav-link:hover {
            background-color: #f8fafc !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }
        .custom-ap-pills .nav-link.active {
            background-color: #D9251C !important;
            border-color: #D9251C !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15) !important;
        }
        .custom-ap-pills .nav-link .badge {
            font-size: 0.72rem !important;
            padding: 3px 6px !important;
            background-color: #f1f5f9 !important;
            color: #475569 !important;
        }
        .custom-ap-pills .nav-link.active .badge {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: #ffffff !important;
        }

        /* Modern Table Style (Clean, borderless outside) */
        .table-responsive {
            border: none !important;
        }
        .table-modern {
            border-collapse: collapse !important;
        }
        .table-modern thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
            padding: 12px 16px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
        }
        .table-modern tbody td {
            padding: 12px 16px !important;
            color: #475569 !important;
            font-size: 0.84rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
        }
        .table-modern tbody tr {
            transition: all 0.15s ease-in-out !important;
        }
        .table-modern tbody tr:hover {
            background-color: #f8fafc !important;
        }
        .text-deep-black {
            color: #0f172a !important;
            font-weight: 600 !important;
        }

        /* Custom Capsule Pagination styling */
        .pagination .page-item.active .page-link {
            background-color: #D9251C !important;
            border-color: #D9251C !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15) !important;
            border-radius: 4px !important;
        }
        .pagination .page-link {
            color: #475569 !important;
            border-color: #cbd5e1 !important;
            padding: 8px 14px !important;
            font-size: 0.85rem !important;
            transition: all 0.15s ease-in-out !important;
            background-color: #ffffff !important;
            border-radius: 4px !important;
            margin: 0 2px !important;
        }
        .pagination .page-link:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        /* Branded Action Buttons */
        .btn-brand-red {
            background-color: #D9251C !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border: none !important;
            transition: opacity 0.2s ease;
        }
        .btn-brand-red:hover {
            opacity: 0.9 !important;
            color: #ffffff !important;
        }

        /* Modal Reference Overrides (Accounts Payable Style) */
        .modal-content {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }
        .modal-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 16px 24px !important;
        }
        .modal-header .modal-title {
            font-size: 0.95rem !important;
            font-weight: 700 !important;
            color: #000000 !important;
        }
        .modal-body label.form-label {
            color: #475569 !important;
            font-weight: 600 !important;
            font-size: 0.72rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            margin-bottom: 6px !important;
            display: inline-block;
        }
        .modal-body .form-control,
        .modal-body .form-select {
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            font-size: 13px !important;
            padding: 8px 12px !important;
            color: #000000 !important;
            background-color: #ffffff !important;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
        }
        .modal-body .form-control:focus,
        .modal-body .form-select:focus {
            border-color: #D9251C !important;
            box-shadow: 0 0 0 0.2rem rgba(217, 37, 28, 0.15) !important;
            outline: 0 !important;
        }
        .modal-footer {
            border-top: 1px solid #f1f5f9 !important;
            background-color: #f8fafc !important;
            padding: 14px 24px !important;
        }

        /* Material Requisition Custom PDF/Form visual styling */
        .req-form-header {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 1rem;
            margin-bottom: 1.25rem;
        }
        .form-document-title {
            font-size: 1.25rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #0f172a;
            border-left: 4px solid #D9251C;
            padding-left: 12px;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        .requisition-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #cbd5e1;
        }
        .requisition-table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 12px;
            border-bottom: 2px solid #cbd5e1;
            border-right: 1px solid #cbd5e1;
        }
        .requisition-table thead th:last-child {
            border-right: none;
        }
        .requisition-table tbody td {
            padding: 6px 10px;
            border-bottom: 1px solid #cbd5e1;
            border-right: 1px solid #cbd5e1;
            background-color: #ffffff;
            vertical-align: middle;
        }
        .requisition-table tbody td:last-child {
            border-right: none;
        }
        .requisition-table tbody tr:last-child td {
            border-bottom: none;
        }
        .requisition-table .form-control {
            font-size: 13px !important;
            padding: 6px 10px !important;
            border: 1px solid transparent !important;
            background-color: #f8fafc !important;
            border-radius: 4px !important;
            color: #0f172a !important;
            transition: all 0.15s ease-in-out;
        }
        .requisition-table .form-control:hover {
            border-color: #cbd5e1 !important;
            background-color: #ffffff !important;
        }
        .requisition-table .form-control:focus {
            border-color: #D9251C !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 0.2rem rgba(217, 37, 28, 0.1) !important;
        }
        .form-instructions {
            background-color: #f8fafc;
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 0.8rem;
            color: #475569;
            border-left: 3px solid #D9251C;
            margin-top: 1rem;
            border: 1px solid #cbd5e1;
            border-left-width: 3px;
        }
        
        .badge-category { font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 6px; }
    </style>
    @endpush

    <div class="container-fluid p-0">
        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fs-22 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Purchasing & Procurement</h4>
                <p class="text-muted small mb-0">Track material requisitions, purchase orders, and receiving reports across departments.</p>
            </div>
        </div>

        <!-- Master Tabs (Pills) Switcher -->
        <ul class="nav nav-pills custom-ap-pills mb-4" id="procurementTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'requisitions' ? 'active' : '' }}" id="requisitions-tab" data-bs-toggle="tab" data-bs-target="#requisitions-pane" type="button" role="tab">
                    <i class="las la-clipboard-list me-1 fs-18"></i> Requisitions (Requests) <span class="badge bg-light text-muted border rounded-pill ms-1">{{ $requisitions->total() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'purchase-orders' ? 'active' : '' }}" id="purchase-orders-tab" data-bs-toggle="tab" data-bs-target="#purchase-orders-pane" type="button" role="tab">
                    <i class="las la-file-invoice me-1 fs-18"></i> Purchase Orders (PO) <span class="badge bg-light text-muted border rounded-pill ms-1">{{ $purchaseOrders->total() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'receiving-reports' ? 'active' : '' }}" id="receiving-reports-tab" data-bs-toggle="tab" data-bs-target="#receiving-reports-pane" type="button" role="tab">
                    <i class="las la-truck-loading me-1 fs-18"></i> Receiving Reports (RR) <span class="badge bg-light text-muted border rounded-pill ms-1">{{ $receivingReports->total() }}</span>
                </button>
            </li>
        </ul>

        <!-- Tab Panes Content -->
        <div class="tab-content" id="procurementTabContent">
            
            <!-- TAB 1: REQUISITIONS PANE -->
            <div class="tab-pane fade {{ $activeTab === 'requisitions' ? 'show active' : '' }}" id="requisitions-pane" role="tabpanel" aria-labelledby="requisitions-tab">
                <div class="card border-0 shadow-sm" style="border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fs-18 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Material & Supplies Requisitions</h4>
                            <p class="text-muted small mb-0">List of purchase request orders routed from departments.</p>
                        </div>
                        <button class="btn btn-brand-red btn-sm px-4 py-2 d-flex align-items-center gap-2 shadow-sm" 
                                style="border-radius: 4px; font-size: 0.88rem; height: 38px;"
                                data-bs-toggle="modal" data-bs-target="#addRequisitionModal">
                            <i class="las la-plus-circle fs-16"></i> Add Requisition Form
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-modern align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Req #</th>
                                        <th>Date</th>
                                        <th>Requested By</th>
                                        <th>Department</th>
                                        <th>P.O. # Ref</th>
                                        <th>Status</th>
                                        <th class="text-end" style="width: 140px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($requisitions as $req)
                                    <tr id="row-{{ $req->id }}">
                                        <td class="text-deep-black"><strong>{{ $req->requisition_no }}</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($req->date)->format('Y-m-d') }}</td>
                                        <td>{{ $req->user->name ?? 'N/A' }}</td>
                                        <td>{{ $req->department }}</td>
                                        <td>
                                            @if($req->po_number)
                                                <span class="badge bg-light text-dark border px-2.5 py-1 fw-bold fs-12" style="border-radius: 4px;">{{ $req->po_number }}</span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = 'bg-warning text-dark border';
                                                if($req->status === 'approved' || $req->status === 'completed') $statusClass = 'bg-success text-white';
                                                if($req->status === 'rejected' || $req->status === 'cancelled') $statusClass = 'bg-danger text-white';
                                            @endphp
                                            <span class="badge badge-category {{ $statusClass }}">{{ ucfirst($req->status) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-info shadow btn-xs sharp me-1 text-white" onclick="viewRequisition({{ $req->id }})" title="View Details">
                                                    <i class="las la-eye"></i>
                                                </button>
                                                <button class="btn btn-danger shadow btn-xs sharp" onclick="confirmDeleteRequisition({{ $req->id }})" title="Delete">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">No material requisitions found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted small">
                                Showing {{ $requisitions->firstItem() ?? 0 }} to {{ $requisitions->lastItem() ?? 0 }} of {{ $requisitions->total() }} requisitions
                            </div>
                            <div id="paginationContainer" class="pe-0">
                                {{ $requisitions->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: PURCHASE ORDERS PANE -->
            <div class="tab-pane fade {{ $activeTab === 'purchase-orders' ? 'show active' : '' }}" id="purchase-orders-pane" role="tabpanel" aria-labelledby="purchase-orders-tab">
                <div class="card border-0 shadow-sm" style="border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h4 class="fs-18 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Purchase Orders (PO) Registry</h4>
                        <p class="text-muted small mb-0">Authorized purchase orders issued to suppliers.</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-modern align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>PO #</th>
                                        <th>Date</th>
                                        <th>Supplier Name</th>
                                        <th>Terms</th>
                                        <th class="text-end">Total Amount</th>
                                        <th>Prepared By</th>
                                        <th>Status</th>
                                        <th class="text-end" style="width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchaseOrders as $po)
                                    <tr>
                                        <td class="text-deep-black"><strong>{{ $po->po_number }}</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($po->date)->format('Y-m-d') }}</td>
                                        <td>{{ $po->supplier->company_name ?? ($po->vendor_name ?: '—') }}</td>
                                        <td>{{ $po->terms ?: '30 Days' }}</td>
                                        <td class="text-end text-deep-black">
                                            {{ $po->currency_symbol }}{{ number_format($po->total_amount, 2) }}
                                        </td>
                                        <td>{{ $po->preparedBy->name ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $poClass = 'bg-warning text-dark border';
                                                if($po->status === 'approved' || $po->status === 'received' || $po->status === 'completed') $poClass = 'bg-success text-white';
                                                if($po->status === 'cancelled') $poClass = 'bg-danger text-white';
                                            @endphp
                                            <span class="badge badge-category {{ $poClass }}">{{ ucfirst($po->status) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end">
                                                <a href="{{ route('production.logistic.purchase-order.show', $po->id) }}" class="btn btn-info shadow btn-xs sharp text-white" target="_blank" title="View PO Details">
                                                    <i class="las la-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">No purchase orders found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted small">
                                Showing {{ $purchaseOrders->firstItem() ?? 0 }} to {{ $purchaseOrders->lastItem() ?? 0 }} of {{ $purchaseOrders->total() }} purchase orders
                            </div>
                            <div id="paginationContainer" class="pe-0">
                                {{ $purchaseOrders->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: RECEIVING REPORTS PANE -->
            <div class="tab-pane fade {{ $activeTab === 'receiving-reports' ? 'show active' : '' }}" id="receiving-reports-pane" role="tabpanel" aria-labelledby="receiving-reports-tab">
                <div class="card border-0 shadow-sm" style="border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h4 class="fs-18 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Receiving Reports (RR) Log</h4>
                        <p class="text-muted small mb-0">Record of items and quantities received at the warehouse.</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-modern align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>RR #</th>
                                        <th>Received Date</th>
                                        <th>PO # Ref</th>
                                        <th>Supplier Name</th>
                                        <th>Received By</th>
                                        <th>Status</th>
                                        <th class="text-end" style="width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($receivingReports as $rr)
                                    <tr>
                                        <td class="text-deep-black"><strong>{{ $rr->rr_number }}</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($rr->received_date)->format('Y-m-d') }}</td>
                                        <td>
                                            @if($rr->purchaseOrder)
                                                <span class="badge bg-light text-dark border px-2.5 py-1 fw-bold fs-12" style="border-radius: 4px;">{{ $rr->purchaseOrder->po_number }}</span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $rr->supplier->company_name ?? '—' }}</td>
                                        <td>{{ $rr->receivedBy->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge badge-category bg-success text-white">{{ ucfirst($rr->status) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end">
                                                <a href="{{ route('production.logistic.receiving-report.show', $rr->id) }}" class="btn btn-info shadow btn-xs sharp text-white" target="_blank" title="View RR Details">
                                                    <i class="las la-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">No receiving reports found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted small">
                                Showing {{ $receivingReports->firstItem() ?? 0 }} to {{ $receivingReports->lastItem() ?? 0 }} of {{ $receivingReports->total() }} receiving reports
                            </div>
                            <div id="paginationContainer" class="pe-0">
                                {{ $receivingReports->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modals -->
    @push('modals')
    <!-- Add Material Requisition Modal -->
    <div class="modal fade" id="addRequisitionModal" aria-labelledby="addRequisitionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRequisitionModalLabel"><i class="las la-clipboard-list me-2 text-danger"></i>New Material Requisition Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Form Header Header Visual -->
                    <div class="req-form-header">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img src="{{ asset('images/claeritian_logo.png') }}" alt="Claretian Logo" style="height: 52px; width: auto; object-fit: contain; flex-shrink: 0;">
                            <div>
                                <div class="fw-bold text-uppercase text-dark" style="font-size: 1rem; letter-spacing: -0.2px;">Claretian Communications Foundation Inc.</div>
                                <div class="text-muted small">8 Mayumi St., UP Village, Diliman, Quezon City &nbsp;|&nbsp; Tel: 921-3984</div>
                            </div>
                        </div>
                        <div class="form-document-title mt-3">Materials / Supplies Requisition</div>
                    </div>

                    <!-- Input Fields -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="reqNo" class="form-label">Requisition No</label>
                            <input type="text" class="form-control" id="reqNo" placeholder="Auto-generated" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="reqDate" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="reqDate" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="reqDepartment" class="form-label">Department <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reqDepartment" placeholder="e.g. Accounting" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="reqSupplier" class="form-label">Supplier Reference</label>
                            <select class="form-control select2-supplier" id="reqSupplier" style="width: 100%;">
                                <option value="">Select Supplier...</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->company_name }}">{{ $supplier->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="reqPO" class="form-label">PO # Reference</label>
                            <input type="text" class="form-control" id="reqPO" placeholder="e.g. PO-2026-0001">
                        </div>
                    </div>

                    <!-- Add Row Trigger -->
                    <button type="button" class="btn btn-outline-danger btn-sm mb-3 d-flex align-items-center gap-1" style="height: 32px; border-radius: 4px; font-size: 0.82rem; border-color: #D9251C; color: #D9251C;" onclick="addReqRow()">
                        <i class="las la-plus"></i> Add Row
                    </button>

                    <!-- Requisition Items Grid -->
                    <div class="table-responsive">
                        <table class="requisition-table">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">Qty</th>
                                    <th style="width: 120px;">Unit</th>
                                    <th>Description / Item</th>
                                    <th style="width: 170px;">Supplier 1 Price</th>
                                    <th style="width: 170px;">Supplier 2 Price</th>
                                    <th style="width: 170px;">Supplier 3 Price</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="reqTableBody">
                                <tr>
                                    <td><input type="number" class="form-control" step="0.01" min="0" placeholder="0"></td>
                                    <td><input type="text" class="form-control" placeholder="pcs, box, rim..."></td>
                                    <td><input type="text" class="form-control" placeholder="Item description..."></td>
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
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning text-white px-3" onclick="printRequisition()"><i class="las la-print me-1"></i>Print</button>
                    <button type="button" class="btn btn-danger px-4" id="saveRequisitionBtn" style="background-color: #D9251C !important; border-color: #D9251C !important; color: #ffffff !important; font-weight: 600;">
                        <i class="las la-save me-1"></i>Save Requisition
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Requisition Modal -->
    <div class="modal fade" id="viewRequisitionModal" tabindex="-1" aria-labelledby="viewRequisitionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewRequisitionModalLabel"><i class="las la-clipboard-list me-2 text-danger"></i>View Requisition Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Form Header -->
                    <div class="req-form-header">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img src="{{ asset('images/claeritian_logo.png') }}" alt="Claretian Logo" style="height: 52px; width: auto; object-fit: contain; flex-shrink: 0;">
                            <div>
                                <div class="fw-bold text-uppercase text-dark" style="font-size: 1rem; letter-spacing: -0.2px;">Claretian Communications Foundation Inc.</div>
                                <div class="text-muted small">8 Mayumi St., UP Village, Diliman, Quezon City &nbsp;|&nbsp; Tel: 921-3984</div>
                            </div>
                        </div>
                        <div class="form-document-title mt-3">Materials / Supplies Requisition</div>
                    </div>

                    <!-- Details Row -->
                    <div class="row border p-3 rounded mb-4" style="background-color: #f8fafc;">
                        <div class="col-md-4 mb-2"><strong>Requisition No:</strong> <span id="viewReqNo" class="text-danger fw-bold ms-1"></span></div>
                        <div class="col-md-4 mb-2"><strong>Date:</strong> <span id="viewReqDate" class="ms-1"></span></div>
                        <div class="col-md-4 mb-2"><strong>Requested By:</strong> <span id="viewReqUser" class="ms-1"></span></div>
                        <div class="col-md-4"><strong>Department:</strong> <span id="viewReqDept" class="ms-1"></span></div>
                        <div class="col-md-4"><strong>Supplier:</strong> <span id="viewReqSupplier" class="ms-1"></span></div>
                        <div class="col-md-4"><strong>P.O. #:</strong> <span id="viewReqPO" class="ms-1"></span></div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive">
                        <table class="requisition-table">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">Qty</th>
                                    <th style="width: 120px;">Unit</th>
                                    <th>Description / Item</th>
                                    <th style="width: 170px;">Supplier 1 Price</th>
                                    <th style="width: 170px;">Supplier 2 Price</th>
                                    <th style="width: 170px;">Supplier 3 Price</th>
                                </tr>
                            </thead>
                            <tbody id="viewReqItemsTable"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning text-white px-3" onclick="printRequisition()"><i class="las la-print me-1"></i>Print</button>
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        jQuery(document).ready(function($) {
            $('.select2-supplier').select2({
                placeholder: "Select Supplier...",
                allowClear: true,
                dropdownParent: $('#addRequisitionModal')
            });
        });

        function addReqRow() {
            const row = `
                <tr>
                    <td><input type="number" class="form-control" step="0.01" min="0" placeholder="0"></td>
                    <td><input type="text" class="form-control" placeholder="pcs, box, rim..."></td>
                    <td><input type="text" class="form-control" placeholder="Item description..."></td>
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
                alert('Please fill in at least the Date and Department fields before saving.');
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
                    // Close modal and reload page to reflect newly added requisition
                    bootstrap.Modal.getInstance(document.getElementById('addRequisitionModal')).hide();
                    alert(data.message);
                    window.location.reload();
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
            window.showConfirm("Are you sure you want to delete this requisition permanently?", function() {
                deleteRequisition(id);
            });
        }

        function deleteRequisition(id) {
            fetch(`/admin-finance/accounting/materials-requisition/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
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
