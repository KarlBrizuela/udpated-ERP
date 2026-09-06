<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
@push('styles')
<link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
<style>
    /* Create SI Modal Expansion */
    @media (min-width: 1200px) {
        #createSalesOrderModal .modal-dialog {
            max-width: 1250px !important;
        }
    }
    .select2-container--default .select2-selection--single {
        height: 31px !important;
        padding: 2px 6px !important;
        font-size: 0.875rem !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.25rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 25px !important;
        color: #212529 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 29px !important;
    }
    .select2-dropdown {
        z-index: 99999 !important;
        font-size: 0.875rem !important;
        border-color: #ced4da !important;
    }

    /* 1. Page Layout & Grid Width Expansion (Guideline 2) */
    .content-body .container-fluid {
        padding-left: 15px !important;
        padding-right: 15px !important;
        max-width: 100% !important;
    }

    /* 2. Modern Table Styles (Guideline 3) */
    .table-responsive {
        border: none !important;
        overflow-x: auto;
        min-height: 320px !important;
    }
    table.table {
        border-collapse: collapse !important;
        width: 100% !important;
    }
    table.table thead th {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        font-size: 11px !important;
        padding: 8px 12px !important;
        border-bottom: 2px solid #e2e8f0 !important;
        border-top: none !important;
        vertical-align: middle !important;
    }
    table.table tbody td {
        padding: 8px 12px !important;
        font-size: 12px !important;
        color: #475569 !important;
        border-bottom: 1px solid #f1f5f9 !important;
        border-top: none !important;
        vertical-align: middle !important;
    }
    table.table tbody tr {
        transition: all 0.15s ease-in-out !important;
    }
    table.table tbody tr:hover {
        background-color: #f8fafc !important;
    }
    
    /* Make buttons inside table more compact */
    table.table .btn {
        padding: 4px 8px !important;
        font-size: 11px !important;
        border-radius: 4px !important;
        line-height: 1.2 !important;
    }

    /* Subtle styling for Payment Method select inside table to prevent bright blue/large distraction */
    table.table select.pm-select {
        height: 28px !important;
        font-size: 11px !important;
        font-weight: 500 !important;
        border: 1px solid #cbd5e1 !important;
        background-color: #ffffff !important;
        border-radius: 4px !important;
        color: #0f172a !important;
        padding: 2px 6px !important;
        min-width: 115px !important;
        cursor: pointer;
        outline: none !important;
        box-shadow: none !important;
    }
    table.table select.pm-select:focus {
        border-color: #D9251C !important;
    }

    /* 3. Badge and Status Tint Accents (Guideline 1) */
    .badge {
        font-weight: 600 !important;
        padding: 4px 8px !important;
        border-radius: 4px !important;
        font-size: 11px !important;
        display: inline-block !important;
        line-height: 1.2 !important;
    }
    .badge-success, .bg-success, .badge.bg-success {
        background-color: rgba(16, 185, 129, 0.08) !important;
        color: #10b981 !important;
        border: 1px solid rgba(16, 185, 129, 0.15) !important;
    }
    .badge-warning, .bg-warning, .badge.bg-warning {
        background-color: rgba(245, 158, 11, 0.08) !important;
        color: #f59e0b !important;
        border: 1px solid rgba(245, 158, 11, 0.15) !important;
    }
    .badge-danger, .bg-danger, .badge.bg-danger {
        background-color: rgba(217, 37, 28, 0.08) !important;
        color: #D9251C !important;
        border: 1px solid rgba(217, 37, 28, 0.15) !important;
    }
    .badge-info, .bg-info, .badge.bg-info {
        background-color: rgba(59, 130, 246, 0.08) !important;
        color: #3b82f6 !important;
        border: 1px solid rgba(59, 130, 246, 0.15) !important;
    }
    .badge-primary, .bg-primary, .badge.bg-primary {
        background-color: rgba(217, 37, 28, 0.08) !important;
        color: #D9251C !important;
        border: 1px solid rgba(217, 37, 28, 0.15) !important;
    }
    .badge-secondary, .bg-secondary, .badge.bg-secondary {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
    }
    .badge-outline-dark {
        background-color: #f8fafc !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
    }

    /* 4. Tab Custom Indicator Colors (Flat borderless style) */
    .nav-tabs {
        border-bottom: 2px solid #e2e8f0 !important;
    }
    .nav-tabs .nav-link {
        font-size: 13px !important;
        font-weight: 700 !important;
        letter-spacing: 0.3px !important;
        color: #64748b !important;
        border: none !important;
        background: transparent !important;
        padding: 8px 12px !important;
        border-bottom: 3px solid transparent !important;
        margin-bottom: -2px !important;
        transition: all 0.2s ease !important;
    }
    .nav-tabs .nav-link i {
        font-size: 1.05rem !important;
    }
    .nav-tabs .nav-link:hover {
        color: #0f172a !important;
    }
    .nav-tabs .nav-link.active {
        color: #D9251C !important;
        border-bottom: 3px solid #D9251C !important;
        background: transparent !important;
    }

    /* 5. Pagination Custom Styles (Guideline 4) */
    .pagination .page-item.active .page-link {
        background-color: #D9251C !important;
        border-color: #D9251C !important;
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15) !important;
    }
    .pagination .page-link {
        color: #475569 !important;
        border-color: #cbd5e1 !important;
        padding: 8px 14px !important;
        font-size: 0.85rem !important;
        transition: all 0.15s ease-in-out !important;
        background-color: #ffffff !important;
    }
    .pagination .page-link:hover {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        border-color: #cbd5e1 !important;
    }

    /* 6. Form elements and buttons */
    .form-control, .form-select {
        border-color: #cbd5e1 !important;
        font-size: 12px !important;
        transition: all 0.15s ease-in-out !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: #D9251C !important;
        box-shadow: 0 0 0 2px rgba(217, 37, 28, 0.08) !important;
    }
    
    /* Soften label weight and color */
    .form-label {
        font-size: 12px !important;
        font-weight: 500 !important;
        color: #475569 !important;
        margin-bottom: 4px !important;
    }
    /* Mute the label icons to reduce eye strain */
    .form-label i {
        color: #8a99ad !important;
        font-size: 1rem !important;
    }

    .btn-primary {
        background-color: #D9251C !important;
        border-color: #D9251C !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 4px rgba(217, 37, 28, 0.1) !important;
        transition: all 0.15s ease-in-out !important;
    }
    .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
        background-color: #b21e16 !important;
        border-color: #b21e16 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 8px rgba(217, 37, 28, 0.2) !important;
    }
    .btn-warning {
        background-color: #f59e0b !important;
        border-color: #f59e0b !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    .btn-warning:hover, .btn-warning:focus {
        background-color: #d97706 !important;
        border-color: #d97706 !important;
        color: #ffffff !important;
    }
    .btn-success {
        background-color: #10b981 !important;
        border-color: #10b981 !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    .btn-success:hover, .btn-success:focus {
        background-color: #059669 !important;
        border-color: #059669 !important;
        color: #ffffff !important;
    }
    .btn-info {
        background-color: #3b82f6 !important;
        border-color: #3b82f6 !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    .btn-info:hover, .btn-info:focus {
        background-color: #2563eb !important;
        border-color: #2563eb !important;
        color: #ffffff !important;
    }
</style>
@endpush

    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="fs-20 mb-0">Sales Invoice Management</h4>
                    </div>
                    <div class="card-body">
                        <!-- Filters Section -->
                        <div class="mb-4">
                            <!-- Row 1: Primary Search & Controls -->
                            <div class="row align-items-center g-2">
                                <div class="col-md-5">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; border-radius: 4px 0 0 4px; height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 10px;">
                                            <i class="las la-search text-muted fs-16"></i>
                                        </span>
                                        <input type="text" id="siSearchInput" class="form-control border-start-0" placeholder="Search by SO #, Customer, Type, Status..." style="height: 38px; border-color: #cbd5e1; border-radius: 0; font-size: 12px; padding-left: 0; outline: none; box-shadow: none;">
                                        <button id="searchSubmitBtn" type="button" class="btn text-white fw-bold px-3 d-flex align-items-center justify-content-center" style="background: #D9251C; border-color: #D9251C; height: 38px; border-radius: 0 4px 4px 0; font-size: 12px;">
                                            Search
                                        </button>
                                    </div>
                                </div>
                                 <div class="col-md-auto">
                                     <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters" aria-expanded="false" aria-controls="advancedFilters" style="height: 38px; border-color: #cbd5e1; border-radius: 4px; font-size: 12px; font-weight: 500; display: flex; align-items: center; justify-content: center; padding: 0 12px; background-color: #ffffff; color: #475569;">
                                         <i class="las la-filter me-1" style="font-size: 1.1rem; color: #64748b;"></i> Filter Options
                                     </button>
                                 </div>
                                 <div class="col-md-auto">
                                      <button type="button" class="btn text-white fw-bold btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#createSalesOrderModal" style="height: 38px; border-color: #28a745; background-color: #28a745; border-radius: 4px; font-size: 12px; font-weight: 600; display: flex; align-items: center; justify-content: center; padding: 0 14px;">
                                          <i class="las la-plus-circle me-1" style="font-size: 1.1rem;"></i> Create
                                      </button>
                                 </div>
                                <div class="col-md-auto ms-auto d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted small fw-medium" style="font-size: 12px;">Show:</span>
                                        <select id="siEntriesSelect" class="form-select form-select-sm text-black" style="height: 38px; min-width: 75px; font-size: 12px; border-radius: 4px; border-color: #cbd5e1;">
                                            <option value="5" selected>5</option>
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="500">500</option>
                                            <option value="all">All</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2: Collapsible Secondary Filters -->
                            <div class="collapse mt-3" id="advancedFilters">
                                <div class="p-3 rounded border" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md">
                                            <label for="siTypeSelect" class="form-label"><i class="las la-tags me-1"></i> Type / Category</label>
                                            <select id="siTypeSelect" class="form-select form-select-sm text-black" style="height: 36px; font-size: 12px; border-radius: 6px;">
                                                <option value="">All Types</option>
                                                <option value="area_sales_consignment">Area Sales Consignment</option>
                                                <option value="area_consignment">Area Consignment</option>
                                                <option value="paid">Paid</option>
                                                <option value="wholesale">Wholesale</option>
                                                <option value="retail">Retail</option>
                                                <option value="bookstore">Bookstore</option>
                                                <option value="ecom_direct">E-Com Direct</option>
                                            </select>
                                        </div>
                                        <div class="col-md">
                                            <label for="siPaymentMethodSelect" class="form-label"><i class="las la-wallet me-1"></i> Payment Method</label>
                                            <select id="siPaymentMethodSelect" class="form-select form-select-sm text-black" style="height: 36px; font-size: 12px; border-radius: 6px;">
                                                <option value="">All Payment Methods</option>
                                                <option value="cash">Cash</option>
                                                <option value="gcash">GCash</option>
                                                <option value="maya">Maya</option>
                                                <option value="bank_transfer">Bank Transfer</option>
                                                <option value="check">Check</option>
                                                <option value="card">Credit/Debit Card</option>
                                            </select>
                                        </div>
                                        <div class="col-md" id="platformFilterContainer" style="display: none;">
                                            <label for="siPlatformSelect" class="form-label"><i class="las la-store me-1"></i> Platform</label>
                                            <select id="siPlatformSelect" class="form-select form-select-sm text-black" style="height: 36px; font-size: 12px; border-radius: 6px;">
                                                <option value="">All Platforms</option>
                                                <option value="lazada">Lazada</option>
                                                <option value="shopee">Shopee</option>
                                                <option value="tiktok">TikTok</option>
                                                <option value="cob">COB</option>
                                                <option value="mibf">MIBF</option>
                                            </select>
                                        </div>
                                        <div class="col-md">
                                            <label for="siStartDate" class="form-label"><i class="las la-calendar me-1"></i> Start Date</label>
                                            <input type="date" id="siStartDate" class="form-control form-control-sm" style="height: 36px; font-size: 12px; border-radius: 6px;">
                                        </div>
                                        <div class="col-md">
                                            <label for="siEndDate" class="form-label"><i class="las la-calendar me-1"></i> End Date</label>
                                            <input type="date" id="siEndDate" class="form-control form-control-sm" style="height: 36px; font-size: 12px; border-radius: 6px;">
                                        </div>
                                        <div class="col-md-auto">
                                            <button id="filterSubmitBtn" type="button" class="btn text-white fw-bold px-3 d-flex align-items-center justify-content-center" style="background: #D9251C; border-color: #D9251C; height: 36px; border-radius: 6px; font-size: 12px; min-width: 110px;">
                                                Apply Filter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bulk Actions Bar -->
                        <div id="bulkActionsBar" class="alert alert-light border d-none justify-content-between align-items-center mb-4 py-2 px-3 shadow-sm bg-white rounded" style="border-left: 4px solid #D9251C !important;">
                            <div class="d-flex align-items-center gap-3">
                                <span class="fw-bold text-dark"><span id="selectedCount" class="badge bg-primary fs-14">0</span> <span id="selectedItemLabel">Sales Order(s)</span> selected</span>
                                <span id="selectedTotalAmount" class="fw-bold text-success d-none">| Total: <span id="totalAmountValue">₱0.00</span></span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" id="bulkPrepareBtn" class="btn btn-warning btn-sm px-3 fw-bold">
                                    <i class="las la-file-invoice me-1"></i> Bulk Prepare & Submit SI
                                </button>
                                <button type="button" id="bulkFinalizeBtn" class="btn btn-primary btn-sm px-3 fw-bold">
                                    <i class="las la-check-double me-1"></i> Bulk Sign & Approve
                                </button>
                                <button type="button" id="bulkPrintSIBtn" class="btn btn-info btn-sm px-3 fw-bold d-none">
                                    <i class="las la-print me-1"></i> Print Selected SIs
                                </button>
                                <button type="button" id="bulkSetPaidBtn" class="btn btn-success btn-sm px-3 fw-bold d-none">
                                    <i class="las la-money-bill-wave me-1"></i> Set as Paid
                                </button>
                            </div>
                        </div>

                        @php
                            $activeTab = request('tab', 'normal');
                        @endphp
                        <!-- Nav Tabs -->
                        <ul class="nav nav-tabs mb-4" id="siTabs" role="tablist" style="border-bottom: 2px solid #eee;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab === 'normal' ? 'active text-dark' : 'text-muted' }} fw-bold text-uppercase border-0 bg-transparent" id="normal-tab" data-bs-toggle="tab" data-bs-target="#normal-pane" type="button" role="tab" aria-controls="normal-pane" aria-selected="{{ $activeTab === 'normal' ? 'true' : 'false' }}" style="padding: 10px 15px; transition: all 0.3s;">
                                    <i class="las la-file-invoice me-1 text-danger" style="font-size: 1.2rem;"></i> Normal Invoices ({{ $normalOrders->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab === 'ecom' ? 'active text-dark' : 'text-muted' }} fw-bold text-uppercase border-0 bg-transparent" id="ecom-tab" data-bs-toggle="tab" data-bs-target="#ecom-pane" type="button" role="tab" aria-controls="ecom-pane" aria-selected="{{ $activeTab === 'ecom' ? 'true' : 'false' }}" style="padding: 10px 15px; transition: all 0.3s;">
                                    <i class="las la-store me-1 text-primary" style="font-size: 1.2rem;"></i> Direct Invoice (E-com) ({{ $ecomOrders->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab === 'completed-ecom' ? 'active text-dark' : 'text-muted' }} fw-bold text-uppercase border-0 bg-transparent" id="completed-ecom-tab" data-bs-toggle="tab" data-bs-target="#completed-ecom-pane" type="button" role="tab" aria-controls="completed-ecom-pane" aria-selected="{{ $activeTab === 'completed-ecom' ? 'true' : 'false' }}" style="padding: 10px 15px; transition: all 0.3s;">
                                    <i class="las la-shopping-cart me-1 text-info" style="font-size: 1.2rem;"></i> Completed E-com ({{ $completedEcomSIs->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab === 'completed' ? 'active text-dark' : 'text-muted' }} fw-bold text-uppercase border-0 bg-transparent" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-pane" type="button" role="tab" aria-controls="completed-pane" aria-selected="{{ $activeTab === 'completed' ? 'true' : 'false' }}" style="padding: 10px 15px; transition: all 0.3s;">
                                    <i class="las la-check-circle me-1 text-success" style="font-size: 1.2rem;"></i> Completed SI ({{ $completedSIs->count() }})
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="siTabsContent">
                            <!-- Normal Invoices Tab Pane -->
                            <div class="tab-pane fade {{ $activeTab === 'normal' ? 'show active' : '' }}" id="normal-pane" role="tabpanel" aria-labelledby="normal-tab">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;">
                                                    <input type="checkbox" id="selectAllNormal" style="width: 16px; height: 16px; cursor: pointer;">
                                                </th>
                                                <th>SO Number</th>
                                                <th>Customer</th>
                                                <th>Type</th>
                                                <th>Payment Method</th>
                                                <th>Total Amount</th>
                                                <th>Paid Amount</th>
                                                <th>Remaining</th>
                                                <th>Order Status</th>
                                                <th>Payment Status</th>
                                                <th>SI Prepared By</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($normalOrders as $order)
                                            @php
                                                $displayAmount = (float) $order->total_amount;
                                                if (in_array($order->type, ['area_consignment', 'area_sales_consignment'])) {
                                                    $activeInvoice = \App\Models\SalesInvoice::where('so_id', $order->id)->where('status', '!=', 'cancelled')->latest()->first();
                                                    if ($activeInvoice) {
                                                        $displayAmount = (float) $activeInvoice->total_amount;
                                                    }
                                                }
                                                $paidAmt = (float) $order->total_paid_amount;
                                                $remBal = (float) $order->remaining_balance;
                                                $pmStatus = $order->computed_payment_status;
                                                $pmBadgeColor = $pmStatus === 'paid' ? 'success' : ($pmStatus === 'partially_paid' ? 'warning' : 'danger');
                                                $pmLabel = $pmStatus === 'partially_paid' ? 'PARTIALLY PAID' : strtoupper($pmStatus);
                                                $ordCurr = $order->currency ?? 'PHP';
                                                $ordSym = ($ordCurr === 'USD' ? '$' : ($ordCurr === 'EUR' ? '€' : '₱'));
                                            @endphp
                                            <tr class="si-row" data-date="{{ $order->created_at->format('Y-m-d') }}" data-type="{{ $order->type }}" data-amount="{{ $displayAmount }}" data-paid="{{ $paidAmt }}" data-remaining="{{ $remBal }}">
                                                <td>
                                                    @if($order->status === 'pending_si_prep' || $order->status === 'pending_si_approval' || $order->status === 'si_created' || $order->status === 'ar_created')
                                                        <input type="checkbox" class="order-checkbox normal-check" value="{{ $order->id }}" data-proof="{{ ($order->proof_of_payment || in_array($order->type, ['ecom_direct', 'charge', 'area_consignment', 'area_sales_consignment', 'direct_consignment', 'complimentary', 'cod'])) ? 'yes' : 'no' }}" data-amount="{{ $displayAmount }}" style="width: 16px; height: 16px; cursor: pointer;">
                                                    @else
                                                        <input type="checkbox" disabled style="width: 16px; height: 16px; opacity: 0.4;">
                                                    @endif
                                                </td>
                                                <td><strong>#{{ $order->so_number }}</strong></td>
                                                <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                                <td><span class="badge badge-outline-dark">{{ ucfirst(str_replace('_', ' ', $order->type)) }}</span></td>
                                                <td>
                                                    @php $currentPm = strtolower($order->payment_method ?? 'cash'); @endphp
                                                    <select class="form-select form-select-sm pm-select text-black fw-bold"
                                                            data-order-id="{{ $order->id }}"
                                                            style="height: 32px; font-size: 12px; border: 1.5px solid #0d6efd; background-color: #f0f7ff; cursor: pointer; min-width: 130px;">
                                                        <option value="cash" {{ $currentPm === 'cash' ? 'selected' : '' }}>💵 Cash</option>
                                                        <option value="gcash" {{ $currentPm === 'gcash' ? 'selected' : '' }}>📱 GCash</option>
                                                        <option value="maya" {{ $currentPm === 'maya' ? 'selected' : '' }}>📱 Maya</option>
                                                        <option value="bank_transfer" {{ $currentPm === 'bank_transfer' ? 'selected' : '' }}>🏦 Bank Transfer</option>
                                                        <option value="check" {{ $currentPm === 'check' ? 'selected' : '' }}>🧾 Check</option>
                                                        <option value="card" {{ $currentPm === 'card' ? 'selected' : '' }}>💳 Card</option>
                                                    </select>
                                                </td>
                                                <td class="fw-bold">
                                                    {{ $ordSym }}{{ number_format($displayAmount, 2) }}
                                                    @if((float)($order->discount_percentage ?? 0) > 0)
                                                        <br><small class="text-danger fw-semibold" style="font-size: 11px;"><i class="las la-tag me-1"></i>{{ (float)$order->discount_percentage }}% off</small>
                                                    @elseif((float)($order->discount_amount ?? 0) > 0)
                                                        <br><small class="text-danger fw-semibold" style="font-size: 11px;"><i class="las la-tag me-1"></i>-{{ $ordSym }}{{ number_format((float)$order->discount_amount, 2) }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-success fw-bold">{{ $ordSym }}{{ number_format($paidAmt, 2) }}</td>
                                                <td class="text-danger fw-bold">{{ $ordSym }}{{ number_format($remBal, 2) }}</td>
                                                <td>
                                                    @php
                                                        $statusClass = 'secondary';
                                                        $displayStatus = str_replace('_', ' ', $order->status);
                                                        if ($order->status === 'pending_si_prep' || $order->status === 'ar_created') {
                                                            $statusClass = 'warning';
                                                            $displayStatus = 'Gathered (Pending SI Prep)';
                                                        } elseif ($order->status === 'si_created') {
                                                            $statusClass = 'warning';
                                                            $displayStatus = 'SI Linked (Pending Prep)';
                                                        } elseif ($order->status === 'pending_si_approval') {
                                                            $statusClass = 'info';
                                                            $displayStatus = 'SI Prepared (Pending Approval)';
                                                        } elseif ($order->status === 'ready_for_delivery') {
                                                            $statusClass = 'success';
                                                        }
                                                    @endphp
                                                    <span class="badge badge-{{ $statusClass }}">
                                                        {{ ucwords($displayStatus) }}
                                                    </span>
                                                </td>
                                                <td><span class="badge badge-{{ $pmBadgeColor }}">{{ $pmLabel }}</span></td>
                                                <td>{{ $order->siPreparedBy->name ?? 'N/A' }}</td>
                                                <td class="text-end">
                                                     @php
                                                         $isFromDR = $order->status === 'si_created' 
                                                             || !empty($order->dr_prepared_by) 
                                                             || !empty($order->dr_prepared_at) 
                                                             || !empty($order->dr_approved_by)
                                                             || in_array($order->type, ['area_consignment', 'area_sales_consignment', 'direct_consignment']);
                                                     @endphp
                                                     <div class="dropdown">
                                                         <button class="btn btn-link text-muted p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow: none;">
                                                             <i class="las la-ellipsis-v" style="font-size: 1.25rem;"></i>
                                                         </button>
                                                         <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="font-size: 12px; border-radius: 6px; min-width: 150px; z-index: 1050;">
                                                                 <li>
                                                                     <a class="dropdown-item py-2" href="{{ route('admin-finance.sales-order.detail', $order->id) }}">
                                                                         <i class="las la-eye me-2 text-primary" style="font-size: 1rem;"></i> View SO Detail
                                                                     </a>
                                                                 </li>
                                                                 
                                                                 @if($remBal > 0 && $order->customer_id)
                                                                     <li>
                                                                         <button type="button" class="dropdown-item py-2 open-pay-modal-btn" data-so-id="{{ $order->id }}" data-customer-id="{{ $order->customer_id }}" data-so-number="{{ $order->so_number }}" data-total="{{ $displayAmount }}" data-paid="{{ $paidAmt }}" data-remaining="{{ $remBal }}" data-terms="{{ $order->terms ?? 'COD' }}" data-due-date="{{ $order->due_date ? $order->due_date->format('M d, Y') : 'N/A' }}" data-currency="{{ $order->currency ?? 'USD' }}" data-symbol="{{ $ordSym }}">
                                                                             <i class="las la-coins me-2 text-success" style="font-size: 1rem;"></i> Record Payment
                                                                         </button>
                                                                     </li>
                                                                 @endif

                                                                 @if($order->status === 'pending_si_prep' || $order->status === 'si_created' || $order->status === 'ar_created')
                                                                     @if($order->proof_of_payment || in_array($order->type, ['ecom_direct', 'charge', 'area_consignment', 'area_sales_consignment', 'direct_consignment', 'complimentary', 'cod']) || $paidAmt > 0)
                                                                         <li>
                                                                             <a class="dropdown-item py-2 text-warning fw-semibold" href="{{ route('admin-finance.accounting.sales-invoice.prepare', $order->id) }}">
                                                                                 <i class="las la-file-invoice me-2" style="font-size: 1rem;"></i> Prepare SI
                                                                             </a>
                                                                         </li>
                                                                     @else
                                                                         <li>
                                                                             <button class="dropdown-item py-2 text-muted" disabled title="Proof of Payment is required to prepare SI">
                                                                                 <i class="las la-exclamation-triangle me-2" style="font-size: 1rem;"></i> Prepare SI (Disabled)
                                                                             </button>
                                                                         </li>
                                                                     @endif
                                                                 @endif
                                                                 
                                                                 @if($order->status === 'pending_si_approval')
                                                                     @if($order->proof_of_payment || in_array($order->type, ['ecom_direct', 'charge', 'area_consignment', 'area_sales_consignment', 'direct_consignment', 'complimentary', 'cod']))
                                                                         <li>
                                                                             <form action="{{ route('admin-finance.accounting.sales-invoice.sign', $order->id) }}" method="POST" class="m-0">
                                                                                 @csrf
                                                                                 <button type="submit" class="dropdown-item py-2 text-success fw-semibold">
                                                                                     <i class="las la-check-double me-2" style="font-size: 1rem;"></i> Sign & Approve
                                                                                 </button>
                                                                             </form>
                                                                         </li>
                                                                     @else
                                                                         <li>
                                                                             <button class="dropdown-item py-2 text-muted" disabled title="Proof of Payment is required to sign SI">
                                                                                 <i class="las la-exclamation-triangle me-2" style="font-size: 1rem;"></i> Sign & Approve (Disabled)
                                                                             </button>
                                                                         </li>
                                                                     @endif
                                                                 @endif
                                                                 
                                                                 @if($order->status === 'ready_for_delivery' || $order->status === 'completed' || $order->status === 'pending_dr_prep' || in_array($order->type, ['charge', 'area_consignment', 'area_sales_consignment', 'direct_consignment']))
                                                                     <li>
                                                                         <a class="dropdown-item py-2 text-info" href="{{ route('admin-finance.accounting.sales-invoice.print', $order->id) }}" target="_blank">
                                                                             <i class="las la-print me-2" style="font-size: 1rem;"></i> Print SI
                                                                         </a>
                                                                     </li>
                                                                     <li>
                                                                         <a class="dropdown-item py-2 text-primary" href="{{ route('admin-finance.accounting.delivery-receipt.print', $order->id) }}" target="_blank">
                                                                             <i class="las la-truck me-2" style="font-size: 1rem;"></i> Print DR
                                                                         </a>
                                                                     </li>
                                                                 @endif

                                                                 @if($isFromDR && in_array($order->status, ['pending_si_prep', 'si_created', 'pending_si_approval', 'ar_created', 'picking']))
                                                                     <li><hr class="dropdown-divider my-1"></li>
                                                                     <li>
                                                                         <form action="{{ route('admin-finance.accounting.sales-invoice.revert-to-dr', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to return Sales Order #{{ $order->so_number }} back to Delivery Receipts (DR)?');" class="m-0">
                                                                             @csrf
                                                                             <button type="submit" class="dropdown-item py-2 text-danger fw-semibold">
                                                                                 <i class="las la-undo-alt me-2" style="font-size: 1rem;"></i> Back to DR
                                                                             </button>
                                                                         </form>
                                                                     </li>
                                                                 @endif
                                                             </ul>
                                                         </div>
                                                 </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="12" class="text-center py-4 text-muted">No normal orders requiring Sales Invoice at this time.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr id="normalTotalRow" style="background: #f8f9fa; border-top: 2px solid #dee2e6;">
                                                <td colspan="5" class="text-end fw-bold" style="font-size: 14px;">TOTAL SUMMARY:</td>
                                                <td class="fw-bold text-primary" style="font-size: 14px;" id="normalTotalAmount">₱0.00</td>
                                                <td class="fw-bold text-success" style="font-size: 14px;" id="normalPaidAmount">₱0.00</td>
                                                <td class="fw-bold text-danger" style="font-size: 14px;" id="normalRemainingAmount">₱0.00</td>
                                                <td colspan="4"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3 px-2 py-2 border-top" id="normal-pagination">
                                    <div class="text-muted small">
                                        Showing <span class="page-start">0</span> to <span class="page-end">0</span> of <span class="total-items">0</span> entries
                                    </div>
                                    <nav>
                                        <ul class="pagination mb-0"></ul>
                                    </nav>
                                </div>
                            </div>

                            <!-- E-com Invoices Tab Pane -->
                            <div class="tab-pane fade" id="ecom-pane" role="tabpanel" aria-labelledby="ecom-tab">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;">
                                                    <input type="checkbox" id="selectAllEcom" style="width: 16px; height: 16px; cursor: pointer;">
                                                </th>
                                                <th>SO Number</th>
                                                <th>Platform</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>SI Prepared By</th>
                                                <th class="text-end" style="width: 80px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($ecomOrders as $order)
                                            @php
                                                $ecomPlat = strtolower($order->ecom_platform ?: ($order->platform ?? 'ecom'));
                                            @endphp
                                            <tr class="si-row" data-date="{{ $order->created_at->format('Y-m-d') }}" data-platform="{{ $ecomPlat }}" data-amount="{{ $order->total_amount }}" data-type="{{ $order->type }}">
                                                <td>
                                                    <input type="checkbox"
                                                        class="order-checkbox ecom-check ecom-print-check"
                                                        value="{{ $order->id }}"
                                                        data-proof="{{ $order->proof_of_payment ? 'yes' : 'no' }}"
                                                        data-order-id="{{ $order->id }}"
                                                        data-amount="{{ $order->total_amount }}"
                                                        style="width: 16px; height: 16px; cursor: pointer;"
                                                    >
                                                </td>
                                                <td><strong>#{{ $order->so_number }}</strong></td>
                                                <td class="text-capitalize">
                                                    @if($ecomPlat === 'lazada')
                                                        <span class="badge bg-danger text-white"><i class="las la-shopping-bag me-1"></i> Lazada</span>
                                                    @elseif($ecomPlat === 'shopee')
                                                        <span class="badge bg-warning text-dark"><i class="las la-shopping-basket me-1"></i> Shopee</span>
                                                    @elseif($ecomPlat === 'tiktok')
                                                        <span class="badge bg-dark text-white"><i class="las la-music me-1"></i> TikTok</span>
                                                    @elseif($ecomPlat === 'cob')
                                                        <span class="badge bg-info text-white"><i class="las la-building me-1"></i> COB</span>
                                                    @elseif($ecomPlat === 'mibf')
                                                        <span class="badge bg-secondary text-white"><i class="las la-store me-1"></i> MIBF</span>
                                                    @else
                                                        <span class="badge bg-secondary text-white">{{ $order->ecom_platform ?? 'E-commerce' }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                                @php
                                                    $ecomCurr = $order->currency ?? 'PHP';
                                                    $ecomSym = ($ecomCurr === 'USD' ? '$' : ($ecomCurr === 'EUR' ? '€' : '₱'));
                                                @endphp
                                                <td class="fw-bold">
                                                    {{ $ecomSym }}{{ number_format($order->total_amount, 2) }}
                                                    @if((float)($order->discount_percentage ?? 0) > 0)
                                                        <br><small class="text-danger fw-semibold" style="font-size: 11px;"><i class="las la-tag me-1"></i>{{ (float)$order->discount_percentage }}% off</small>
                                                    @elseif((float)($order->discount_amount ?? 0) > 0)
                                                        <br><small class="text-danger fw-semibold" style="font-size: 11px;"><i class="las la-tag me-1"></i>-{{ $ecomSym }}{{ number_format((float)$order->discount_amount, 2) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $statusClass = 'secondary';
                                                        $displayStatus = str_replace('_', ' ', $order->status);
                                                        
                                                        if ($order->status === 'pending_si_prep') {
                                                            $statusClass = 'warning';
                                                            $displayStatus = 'Gathered (Pending SI Prep)';
                                                        } elseif ($order->status === 'picking') {
                                                            $statusClass = 'primary';
                                                            $displayStatus = 'In Pick List (Picking)';
                                                        } elseif ($order->status === 'si_created') {
                                                            $statusClass = 'info';
                                                            $displayStatus = 'SI Created (Pending Signature)';
                                                        } elseif ($order->status === 'pending_si_approval') {
                                                            $statusClass = 'info';
                                                            $displayStatus = 'SI Prepared (Pending Approval)';
                                                        } elseif ($order->status === 'ready_for_delivery') {
                                                            $statusClass = 'success';
                                                        }
                                                    @endphp
                                                    <span class="badge badge-{{ $statusClass }}">
                                                        {{ ucwords($displayStatus) }}
                                                    </span>
                                                </td>
                                                <td>{{ $order->siPreparedBy->name ?? ($order->preparedBy->name ?? 'N/A') }}</td>
                                                <td class="text-end">
                                                     <div class="dropdown">
                                                         <button class="btn btn-link text-muted p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow: none;">
                                                             <i class="las la-ellipsis-v" style="font-size: 1.25rem;"></i>
                                                         </button>
                                                         <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="font-size: 12px; border-radius: 6px; min-width: 140px; z-index: 1050;">
                                                             <li>
                                                                 <a class="dropdown-item py-2" href="{{ route('admin-finance.sales-order.detail', $order->id) }}">
                                                                     <i class="las la-eye me-2 text-primary" style="font-size: 1rem;"></i> View SO Detail
                                                                 </a>
                                                             </li>
                                                             <li>
                                                                 <a class="dropdown-item py-2 text-info" href="{{ route('admin-finance.accounting.sales-invoice.print', $order->id) }}" target="_blank">
                                                                     <i class="las la-print me-2" style="font-size: 1rem;"></i> Print SI
                                                                 </a>
                                                             </li>
                                                             <li>
                                                                 <a class="dropdown-item py-2 text-primary" href="{{ route('admin-finance.accounting.delivery-receipt.print', $order->id) }}" target="_blank">
                                                                     <i class="las la-truck me-2" style="font-size: 1rem;"></i> Print DR
                                                                 </a>
                                                             </li>
                                                         </ul>
                                                     </div>
                                                 </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">No E-com direct orders requiring Sales Invoice at this time.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr id="ecomTotalRow" style="background: #f8f9fa; border-top: 2px solid #dee2e6;">
                                                <td colspan="4" class="text-end fw-bold" style="font-size: 14px;">Total Amount:</td>
                                                <td class="fw-bold text-success" style="font-size: 14px;" id="ecomTotalAmount">₱0.00</td>
                                                <td colspan="4"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3 px-2 py-2 border-top" id="ecom-pagination">
                                    <div class="text-muted small">
                                        Showing <span class="page-start">0</span> to <span class="page-end">0</span> of <span class="total-items">0</span> entries
                                    </div>
                                    <nav>
                                        <ul class="pagination mb-0"></ul>
                                    </nav>
                                </div>
                            </div>

                            <!-- Completed SI Tab Pane -->
                            <div class="tab-pane fade {{ $activeTab === 'completed' ? 'show active' : '' }}" id="completed-pane" role="tabpanel" aria-labelledby="completed-tab">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>SI Number</th>
                                                <th>SO Number</th>
                                                <th>Customer</th>
                                                <th>Type</th>
                                                <th>Payment Method</th>
                                                <th>Total Amount</th>
                                                <th>Paid Amount</th>
                                                <th>Remaining</th>
                                                <th>Order Status</th>
                                                <th>Payment Status</th>
                                                <th>Created Date</th>
                                                <th class="text-end" style="width: 80px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($completedSIs as $si)
                                            @php
                                                $so = $si->salesOrder;
                                                $totalAmt = (float)($si->total_amount ?? ($so->total_amount ?? 0));
                                                $paidAmt = $so ? (float)$so->total_paid_amount : 0;
                                                $remBal = $so ? (float)$so->remaining_balance : max(0, $totalAmt - $paidAmt);
                                                $pmStatus = $so ? $so->computed_payment_status : ($remBal <= 0 ? 'paid' : 'unpaid');
                                                $pmBadgeColor = $pmStatus === 'paid' ? 'success' : ($pmStatus === 'partially_paid' ? 'warning' : 'danger');
                                                $pmLabel = $pmStatus === 'partially_paid' ? 'PARTIALLY PAID' : strtoupper($pmStatus);
                                                $siCurr = $so->currency ?? 'PHP';
                                                $siSym = ($siCurr === 'USD' ? '$' : ($siCurr === 'EUR' ? '€' : '₱'));
                                            @endphp
                                            <tr class="si-row" data-date="{{ $si->created_at->format('Y-m-d') }}" data-type="{{ $si->salesOrder->type ?? str_replace('_si', '', $si->transaction_type ?? 'area_consignment') }}" data-amount="{{ $totalAmt }}" data-paid="{{ $paidAmt }}" data-remaining="{{ $remBal }}">
                                                <td><strong>#{{ $si->si_number }}</strong></td>
                                                <td>#{{ $si->so_number }}</td>
                                                <td>{{ $si->customer_name ?? ($si->customer->customer_name ?? 'N/A') }}</td>
                                                <td><span class="badge badge-outline-dark">{{ ucfirst(str_replace('_', ' ', $si->transaction_type ?? 'area_consignment_si')) }}</span></td>
                                                <td>
                                                    @php $currentPm = strtolower($si->salesOrder->payment_method ?? 'cash'); @endphp
                                                    <select class="form-select form-select-sm pm-select text-black fw-bold"
                                                            data-order-id="{{ $si->so_id }}"
                                                            style="height: 32px; font-size: 12px; border: 1.5px solid #0d6efd; background-color: #f0f7ff; cursor: pointer; min-width: 130px;">
                                                        <option value="cash" {{ $currentPm === 'cash' ? 'selected' : '' }}>💵 Cash</option>
                                                        <option value="gcash" {{ $currentPm === 'gcash' ? 'selected' : '' }}>📱 GCash</option>
                                                        <option value="maya" {{ $currentPm === 'maya' ? 'selected' : '' }}>📱 Maya</option>
                                                        <option value="bank_transfer" {{ $currentPm === 'bank_transfer' ? 'selected' : '' }}>🏦 Bank Transfer</option>
                                                        <option value="check" {{ $currentPm === 'check' ? 'selected' : '' }}>🧾 Check</option>
                                                        <option value="card" {{ $currentPm === 'card' ? 'selected' : '' }}>💳 Card</option>
                                                    </select>
                                                </td>
                                                <td class="fw-bold">
                                                    {{ $siSym }}{{ number_format($totalAmt, 2) }}
                                                    @if((float)($si->salesOrder?->discount_percentage ?? 0) > 0)
                                                        <br><small class="text-danger fw-semibold" style="font-size: 11px;"><i class="las la-tag me-1"></i>{{ (float)$si->salesOrder->discount_percentage }}% off</small>
                                                    @elseif((float)($si->salesOrder?->discount_amount ?? 0) > 0)
                                                        <br><small class="text-danger fw-semibold" style="font-size: 11px;"><i class="las la-tag me-1"></i>-{{ $siSym }}{{ number_format((float)$si->salesOrder->discount_amount, 2) }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-success fw-bold">{{ $siSym }}{{ number_format($paidAmt, 2) }}</td>
                                                <td class="text-danger fw-bold">{{ $siSym }}{{ number_format($remBal, 2) }}</td>
                                                <td><span class="badge bg-success text-white">Completed / Approved</span></td>
                                                <td><span class="badge badge-{{ $pmBadgeColor }}">{{ $pmLabel }}</span></td>
                                                <td>{{ $si->created_at->format('M d, Y') }}</td>
                                                <td class="text-end">
                                                     <div class="dropdown">
                                                         <button class="btn btn-link text-muted p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow: none;">
                                                             <i class="las la-ellipsis-v" style="font-size: 1.25rem;"></i>
                                                         </button>
                                                         <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="font-size: 12px; border-radius: 6px; min-width: 140px; z-index: 1050;">
                                                             @if($so || $si->so_id)
                                                                 <li>
                                                                     <a class="dropdown-item py-2" href="{{ route('admin-finance.sales-order.detail', $si->so_id ?? $so->id) }}">
                                                                         <i class="las la-eye me-2 text-primary" style="font-size: 1rem;"></i> View SO Detail
                                                                     </a>
                                                                 </li>
                                                             @endif
                                                             @if($remBal > 0 && $so && $so->customer_id)
                                                                 <li>
                                                                     <button type="button" class="dropdown-item py-2 open-pay-modal-btn" data-so-id="{{ $so->id }}" data-customer-id="{{ $so->customer_id }}" data-so-number="{{ $so->so_number }}" data-total="{{ $totalAmt }}" data-paid="{{ $paidAmt }}" data-remaining="{{ $remBal }}" data-terms="{{ $so->terms ?? 'COD' }}" data-due-date="{{ $so->due_date ? $so->due_date->format('M d, Y') : 'N/A' }}" data-currency="{{ $so->currency ?? 'USD' }}" data-symbol="{{ $siSym }}">
                                                                         <i class="las la-coins me-2 text-success" style="font-size: 1rem;"></i> Record Payment
                                                                     </button>
                                                                 </li>
                                                             @endif
                                                             <li>
                                                                 <a class="dropdown-item py-2 text-info" href="{{ route('admin-finance.accounting.sales-invoice.print', $si->so_id) }}" target="_blank">
                                                                     <i class="las la-print me-2" style="font-size: 1rem;"></i> Print SI
                                                                 </a>
                                                             </li>
                                                             <li>
                                                                 <a class="dropdown-item py-2 text-primary" href="{{ route('admin-finance.accounting.delivery-receipt.print', $si->so_id) }}" target="_blank">
                                                                     <i class="las la-truck me-2" style="font-size: 1rem;"></i> Print DR
                                                                 </a>
                                                             </li>
                                                         </ul>
                                                     </div>
                                                 </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="12" class="text-center py-4 text-muted">No completed Sales Invoices found.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr id="completedTotalRow" style="background: #f8f9fa; border-top: 2px solid #dee2e6;">
                                                <td colspan="5" class="text-end fw-bold" style="font-size: 14px;">TOTAL SUMMARY:</td>
                                                <td class="fw-bold text-primary" style="font-size: 14px;" id="completedTotalAmount">₱0.00</td>
                                                <td class="fw-bold text-success" style="font-size: 14px;" id="completedPaidAmount">₱0.00</td>
                                                <td class="fw-bold text-danger" style="font-size: 14px;" id="completedRemainingAmount">₱0.00</td>
                                                <td colspan="4"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3 px-2 py-2 border-top" id="completed-pagination">
                                    <div class="text-muted small">
                                        Showing <span class="page-start">0</span> to <span class="page-end">0</span> of <span class="total-items">0</span> entries
                                    </div>
                                    <nav>
                                        <ul class="pagination mb-0"></ul>
                                    </nav>
                                </div>
                            </div>

                            <!-- Completed E-com Invoices Tab Pane -->
                            <div class="tab-pane fade {{ $activeTab === 'completed-ecom' ? 'show active' : '' }}" id="completed-ecom-pane" role="tabpanel" aria-labelledby="completed-ecom-tab">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 45px;">
                                                    <input type="checkbox" id="selectAllCompletedEcom" style="width: 16px; height: 16px; cursor: pointer;">
                                                </th>
                                                <th>SI Number</th>
                                                <th>SO Number</th>
                                                <th>Platform</th>
                                                <th>Customer</th>
                                                <th>Total Amount</th>
                                                <th>Payment Status</th>
                                                <th>Created Date</th>
                                                <th class="text-end" style="width: 80px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($completedEcomSIs as $si)
                                            @php
                                                $so = $si->salesOrder;
                                                $totalAmt = (float)($si->total_amount ?? ($so->total_amount ?? 0));
                                                $paidAmt = $so ? (float)$so->total_paid_amount : 0;
                                                $remBal = $so ? (float)$so->remaining_balance : max(0, $totalAmt - $paidAmt);
                                                $pmStatus = $so ? $so->computed_payment_status : ($remBal <= 0 ? 'paid' : 'unpaid');
                                                $pmBadgeColor = $pmStatus === 'paid' ? 'success' : ($pmStatus === 'partially_paid' ? 'warning' : 'danger');
                                                $pmLabel = $pmStatus === 'partially_paid' ? 'PARTIALLY PAID' : strtoupper($pmStatus);
                                                
                                                $platform = $so->ecom_platform ?? ($so->platform ?? '');
                                                if (empty($platform)) {
                                                    if (str_contains(strtolower($si->si_number ?? ''), 'lazada')) $platform = 'lazada';
                                                    elseif (str_contains(strtolower($si->si_number ?? ''), 'shopee')) $platform = 'shopee';
                                                    elseif (str_contains(strtolower($si->si_number ?? ''), 'tiktok')) $platform = 'tiktok';
                                                    elseif (str_contains(strtolower($si->si_number ?? ''), 'cob')) $platform = 'cob';
                                                    elseif (str_contains(strtolower($si->si_number ?? ''), 'mibf') || str_contains(strtolower($so->so_number ?? ''), 'mibf')) $platform = 'mibf';
                                                    else $platform = 'ecom';
                                                }
                                                
                                                $siCurr = $so->currency ?? 'PHP';
                                                $siSym = ($siCurr === 'USD' ? '$' : ($siCurr === 'EUR' ? '€' : '₱'));
                                            @endphp
                                            <tr class="si-row" data-date="{{ $si->created_at->format('Y-m-d') }}" data-platform="{{ strtolower($platform) }}" data-amount="{{ $totalAmt }}" data-type="ecom_direct" data-payment-status="{{ strtolower($pmStatus) }}">
                                                <td>
                                                    <input type="checkbox" class="order-checkbox completed-ecom-check"
                                                           value="{{ $si->id }}"
                                                           data-si-id="{{ $si->id }}"
                                                           data-so-id="{{ $si->so_id }}"
                                                           data-amount="{{ $totalAmt }}"
                                                           data-status="{{ strtolower($pmStatus) }}"
                                                           style="width: 16px; height: 16px; cursor: pointer;">
                                                </td>
                                                <td><strong>#{{ $si->si_number }}</strong></td>
                                                <td>#{{ $si->so_number }}</td>
                                                <td class="text-capitalize">
                                                    @if(strtolower($platform) === 'lazada')
                                                        <span class="badge bg-danger text-white"><i class="las la-shopping-bag me-1"></i> Lazada</span>
                                                    @elseif(strtolower($platform) === 'shopee')
                                                        <span class="badge bg-warning text-dark"><i class="las la-shopping-basket me-1"></i> Shopee</span>
                                                    @elseif(strtolower($platform) === 'tiktok')
                                                        <span class="badge bg-dark text-white"><i class="las la-music me-1"></i> TikTok</span>
                                                    @elseif(strtolower($platform) === 'cob')
                                                        <span class="badge bg-info text-white"><i class="las la-building me-1"></i> COB</span>
                                                    @elseif(strtolower($platform) === 'mibf')
                                                        <span class="badge bg-secondary text-white"><i class="las la-store me-1"></i> MIBF</span>
                                                    @else
                                                        <span class="badge bg-secondary text-white">{{ $platform ?: 'E-commerce' }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $si->customer_name ?? ($si->customer->customer_name ?? 'N/A') }}</td>
                                                <td class="fw-bold">
                                                    {{ $siSym }}{{ number_format($totalAmt, 2) }}
                                                    @if((float)($so?->discount_percentage ?? 0) > 0)
                                                        <br><small class="text-danger fw-semibold" style="font-size: 11px;"><i class="las la-tag me-1"></i>{{ (float)$so->discount_percentage }}% off</small>
                                                    @elseif((float)($so?->discount_amount ?? 0) > 0)
                                                        <br><small class="text-danger fw-semibold" style="font-size: 11px;"><i class="las la-tag me-1"></i>-{{ $siSym }}{{ number_format((float)$so->discount_amount, 2) }}</small>
                                                    @endif
                                                </td>
                                                <td><span class="badge badge-{{ $pmBadgeColor }}">{{ $pmLabel }}</td>
                                                <td>{{ $si->created_at->format('M d, Y') }}</td>
                                                <td class="text-end">
                                                     <div class="dropdown">
                                                         <button class="btn btn-link text-muted p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow: none;">
                                                             <i class="las la-ellipsis-v" style="font-size: 1.25rem;"></i>
                                                         </button>
                                                         <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="font-size: 12px; border-radius: 6px; min-width: 140px; z-index: 1050;">
                                                             @if($so)
                                                                 <li>
                                                                     <a class="dropdown-item py-2" href="{{ route('admin-finance.sales-order.detail', $so->id) }}">
                                                                         <i class="las la-eye me-2 text-primary" style="font-size: 1rem;"></i> View SO Detail
                                                                     </a>
                                                                 </li>
                                                                 <li>
                                                                     <a class="dropdown-item py-2 text-info" href="{{ route('admin-finance.accounting.sales-invoice.print', $so->id) }}" target="_blank">
                                                                         <i class="las la-print me-2" style="font-size: 1rem;"></i> Print SI
                                                                     </a>
                                                                 </li>
                                                                 <li>
                                                                     <a class="dropdown-item py-2 text-primary" href="{{ route('admin-finance.accounting.delivery-receipt.print', $so->id) }}" target="_blank">
                                                                         <i class="las la-truck me-2" style="font-size: 1rem;"></i> Print DR
                                                                     </a>
                                                                 </li>
                                                             @else
                                                                 <li><span class="dropdown-item py-2 text-muted">N/A</span></li>
                                                             @endif
                                                         </ul>
                                                     </div>
                                                 </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-4 text-muted">No completed E-commerce direct invoices at this time.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr id="completedEcomTotalRow" style="background: #f8f9fa; border-top: 2px solid #dee2e6;">
                                                <td colspan="5" class="text-end fw-bold" style="font-size: 14px;">TOTAL SUMMARY:</td>
                                                <td class="fw-bold text-success" style="font-size: 14px;" id="completedEcomTotalAmount">₱0.00</td>
                                                <td colspan="3"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3 px-2 py-2 border-top" id="completed-ecom-pagination">
                                    <div class="text-muted small">
                                        Showing <span class="page-start">0</span> to <span class="page-end">0</span> of <span class="total-items">0</span> entries
                                    </div>
                                    <nav>
                                        <ul class="pagination mb-0"></ul>
                                    </nav>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Styling JS script -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const tabElList = [].slice.call(document.querySelectorAll('button[data-bs-toggle="tab"]'))
                                tabElList.forEach(function(tabEl) {
                                    tabEl.addEventListener('shown.bs.tab', function(event) {
                                        // Reset classes
                                        tabElList.forEach(el => {
                                            el.classList.remove('text-dark', 'active');
                                            el.classList.add('text-muted');
                                            el.style.borderBottom = '3px solid transparent';
                                        });
                                        // Set active classes
                                        event.target.classList.add('text-dark', 'active');
                                        event.target.classList.remove('text-muted');
                                        if (event.target.id === 'normal-tab') {
                                             event.target.style.borderBottom = '3px solid #D9251C';
                                         } else if (event.target.id === 'ecom-tab') {
                                             event.target.style.borderBottom = '3px solid #D9251C';
                                         } else if (event.target.id === 'completed-tab') {
                                             event.target.style.borderBottom = '3px solid #D9251C';
                                         } else if (event.target.id === 'completed-ecom-tab') {
                                             event.target.style.borderBottom = '3px solid #D9251C';
                                         }
                                    });
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('siSearchInput');
        const typeSelect = document.getElementById('siTypeSelect');
        const pmSelect = document.getElementById('siPaymentMethodSelect');
        const platformSelect = document.getElementById('siPlatformSelect');
        const startDateInput = document.getElementById('siStartDate');
        const endDateInput = document.getElementById('siEndDate');
        const entriesSelect = document.getElementById('siEntriesSelect');
        const clearBtn = document.getElementById('clearFiltersBtn');

        const pageState = {
            'normal-pane': 1,
            'ecom-pane': 1,
            'completed-pane': 1,
            'completed-ecom-pane': 1
        };
        let currentPageSize = 5;

        function getPageSize() {
            const val = entriesSelect ? entriesSelect.value : (currentPageSize || 5);
            return val === 'all' ? 999999 : (parseInt(val) || 5);
        }

        function syncEntriesDropdowns(val) {
            if (entriesSelect) entriesSelect.value = val;
            document.querySelectorAll('.entries-per-page-select').forEach(sel => {
                sel.value = val;
            });
        }

        function resetPageStates() {
            pageState['normal-pane'] = 1;
            pageState['ecom-pane'] = 1;
            pageState['completed-pane'] = 1;
            pageState['completed-ecom-pane'] = 1;
        }

        function filterAndPaginate() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const selectedType = typeSelect ? typeSelect.value : '';
            const platform = platformSelect ? platformSelect.value : '';
            const selectedPm = pmSelect ? pmSelect.value.toLowerCase() : '';
            const pageSize = getPageSize();

            ['normal-pane', 'ecom-pane', 'completed-pane', 'completed-ecom-pane'].forEach(paneId => {
                const pane = document.getElementById(paneId);
                if (!pane) return;
                const rows = Array.from(pane.querySelectorAll('.si-row'));

                // 1. Determine matches
                const matchingRows = [];
                rows.forEach(row => {
                    let matchesSearch = true;
                    let matchesDate = true;
                    let matchesPlatform = true;
                    let matchesType = true;
                    let matchesPm = true;

                    if (query) {
                        const text = row.innerText.toLowerCase();
                        matchesSearch = text.includes(query);
                    }

                    if (selectedType) {
                        const rowType = row.getAttribute('data-type');
                        if (rowType && rowType !== selectedType) matchesType = false;
                    }

                    if (selectedPm) {
                        const rowPmSelect = row.querySelector('.pm-select');
                        const rowPm = rowPmSelect ? rowPmSelect.value.toLowerCase() : (row.getAttribute('data-pm') || '');
                        if (rowPm !== selectedPm) matchesPm = false;
                    }

                    const rowDateStr = row.getAttribute('data-date');
                    if (rowDateStr) {
                        if (startDateInput && startDateInput.value && rowDateStr < startDateInput.value) matchesDate = false;
                        if (endDateInput && endDateInput.value && rowDateStr > endDateInput.value) matchesDate = false;
                    }

                    if (platform) {
                        const rowPlatform = row.getAttribute('data-platform');
                        if (rowPlatform && rowPlatform !== platform) matchesPlatform = false;
                    }

                    if (matchesSearch && matchesType && matchesDate && matchesPlatform && matchesPm) {
                        matchingRows.push(row);
                    } else {
                        row.style.display = 'none';
                    }
                });

                // 2. Handle empty state
                const tbody = pane.querySelector('tbody');
                let noResultRow = tbody.querySelector('.no-results-row');
                if (matchingRows.length === 0 && rows.length > 0) {
                    if (!noResultRow) {
                        noResultRow = document.createElement('tr');
                        noResultRow.className = 'no-results-row';
                        const colCount = pane.querySelectorAll('thead th').length;
                        noResultRow.innerHTML = `<td colspan="${colCount}" class="text-center py-4 text-muted">No matching results found.</td>`;
                        tbody.appendChild(noResultRow);
                    }
                } else if (noResultRow) {
                    noResultRow.remove();
                }

                // 3. Update totals for matching rows
                let totalAmount = 0;
                let paidAmount = 0;
                let remainingAmount = 0;

                matchingRows.forEach(row => {
                    const amt = parseFloat(row.getAttribute('data-amount')) || 0;
                    const paid = parseFloat(row.getAttribute('data-paid')) || 0;
                    const rem = parseFloat(row.getAttribute('data-remaining')) || 0;
                    totalAmount += amt;
                    paidAmount += paid;
                    remainingAmount += rem;
                });

                const prefix = paneId.replace('-pane', '');
                const camelPrefix = paneId.replace(/-([a-z])/g, (g) => g[1].toUpperCase()).replace('Pane', '');
                const totEl = document.getElementById(camelPrefix + 'TotalAmount') || document.getElementById(prefix + 'TotalAmount');
                const paidEl = document.getElementById(camelPrefix + 'PaidAmount') || document.getElementById(prefix + 'PaidAmount');
                const remEl = document.getElementById(camelPrefix + 'RemainingAmount') || document.getElementById(prefix + 'RemainingAmount');

                if (totEl) totEl.textContent = '₱' + totalAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                if (paidEl) paidEl.textContent = '₱' + paidAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                if (remEl) remEl.textContent = '₱' + remainingAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                // 4. Paginate matching rows
                const totalMatching = matchingRows.length;
                const totalPages = Math.ceil(totalMatching / pageSize) || 1;
                if (pageState[paneId] > totalPages) pageState[paneId] = totalPages;
                if (pageState[paneId] < 1) pageState[paneId] = 1;

                const currPage = pageState[paneId];
                const startIndex = (currPage - 1) * pageSize;
                const endIndex = Math.min(startIndex + pageSize, totalMatching);

                matchingRows.forEach((row, index) => {
                    if (index >= startIndex && index < endIndex) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                // 5. Update pagination UI controls
                const pagWrapper = document.getElementById(prefix + '-pagination');
                if (pagWrapper) {
                    if (totalMatching === 0) {
                        pagWrapper.style.display = 'none';
                    } else {
                        pagWrapper.style.display = 'flex';
                        pagWrapper.querySelector('.page-start').textContent = totalMatching === 0 ? 0 : startIndex + 1;
                        pagWrapper.querySelector('.page-end').textContent = endIndex;
                        pagWrapper.querySelector('.total-items').textContent = totalMatching;

                        const ul = pagWrapper.querySelector('.pagination');
                        ul.innerHTML = '';

                        // Prev button
                        const prevLi = document.createElement('li');
                        if (currPage === 1) {
                            prevLi.className = 'page-item disabled';
                            prevLi.setAttribute('aria-disabled', 'true');
                            prevLi.innerHTML = `<span class="page-link" aria-hidden="true">&lsaquo;</span>`;
                        } else {
                            prevLi.className = 'page-item';
                            prevLi.innerHTML = `<a class="page-link" href="javascript:void(0)" aria-label="Previous">&lsaquo;</a>`;
                            prevLi.addEventListener('click', () => {
                                pageState[paneId]--;
                                filterAndPaginate();
                            });
                        }
                        ul.appendChild(prevLi);

                        // Page numbers
                        for (let i = 1; i <= totalPages; i++) {
                            if (totalPages <= 7 || i === 1 || i === totalPages || (i >= currPage - 1 && i <= currPage + 1)) {
                                const pageLi = document.createElement('li');
                                if (i === currPage) {
                                    pageLi.className = 'page-item active';
                                    pageLi.setAttribute('aria-current', 'page');
                                    pageLi.innerHTML = `<span class="page-link">${i}</span>`;
                                } else {
                                    pageLi.className = 'page-item';
                                    pageLi.innerHTML = `<a class="page-link" href="javascript:void(0)">${i}</a>`;
                                    pageLi.addEventListener('click', () => {
                                        pageState[paneId] = i;
                                        filterAndPaginate();
                                    });
                                }
                                ul.appendChild(pageLi);
                            } else if (i === currPage - 2 || i === currPage + 2) {
                                const dotsLi = document.createElement('li');
                                dotsLi.className = 'page-item disabled';
                                dotsLi.setAttribute('aria-disabled', 'true');
                                dotsLi.innerHTML = `<span class="page-link">...</span>`;
                                ul.appendChild(dotsLi);
                            }
                        }

                        // Next button
                        const nextLi = document.createElement('li');
                        if (currPage === totalPages) {
                            nextLi.className = 'page-item disabled';
                            nextLi.setAttribute('aria-disabled', 'true');
                            nextLi.innerHTML = `<span class="page-link" aria-hidden="true">&rsaquo;</span>`;
                        } else {
                            nextLi.className = 'page-item';
                            nextLi.innerHTML = `<a class="page-link" href="javascript:void(0)" aria-label="Next">&rsaquo;</a>`;
                            nextLi.addEventListener('click', () => {
                                pageState[paneId]++;
                                filterAndPaginate();
                            });
                        }
                        ul.appendChild(nextLi);
                    }
                }
            });
        }

        // Calculate on page load
        filterAndPaginate();

        const searchBtn = document.getElementById('searchSubmitBtn');
        const filterSubmitBtn = document.getElementById('filterSubmitBtn');

        if (searchBtn) {
            searchBtn.addEventListener('click', function() {
                if (this.textContent.trim() === 'Clear') {
                    if (searchInput) searchInput.value = '';
                    this.textContent = 'Search';
                } else {
                    const query = searchInput ? searchInput.value.trim() : '';
                    if (query) {
                        this.textContent = 'Clear';
                    }
                }
                resetPageStates();
                filterAndPaginate();
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                if (searchBtn) {
                    if (searchInput.value.trim() !== '') {
                        searchBtn.textContent = 'Clear';
                    } else {
                        searchBtn.textContent = 'Search';
                    }
                }
                resetPageStates();
                filterAndPaginate();
            });
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    if (searchBtn) {
                        if (searchInput.value.trim() !== '') {
                            searchBtn.textContent = 'Clear';
                        } else {
                            searchBtn.textContent = 'Search';
                        }
                    }
                    resetPageStates();
                    filterAndPaginate();
                }
            });
        }

        if (filterSubmitBtn) {
            filterSubmitBtn.addEventListener('click', function() {
                if (this.textContent.trim() === 'Clear Filter') {
                    if (typeSelect) typeSelect.value = '';
                    if (pmSelect) pmSelect.value = '';
                    if (platformSelect) platformSelect.value = '';
                    if (startDateInput) startDateInput.value = '';
                    if (endDateInput) endDateInput.value = '';

                    this.textContent = 'Apply Filter';
                    this.className = 'btn text-white fw-bold px-3 d-flex align-items-center justify-content-center';
                    this.style.background = '#D9251C';
                    this.style.borderColor = '#D9251C';
                } else {
                    const hasFilters = (typeSelect && typeSelect.value !== '') ||
                                       (pmSelect && pmSelect.value !== '') ||
                                       (platformSelect && platformSelect.value !== '') ||
                                       (startDateInput && startDateInput.value !== '') ||
                                       (endDateInput && endDateInput.value !== '');
                    
                    if (hasFilters) {
                        this.textContent = 'Clear Filter';
                        this.className = 'btn btn-light text-dark fw-bold px-3 d-flex align-items-center justify-content-center';
                        this.style.background = '#f1f5f9';
                        this.style.borderColor = '#cbd5e1';
                    }
                }
                resetPageStates();
                filterAndPaginate();
            });
        }

        if (entriesSelect) {
            entriesSelect.addEventListener('change', function() {
                currentPageSize = this.value;
                syncEntriesDropdowns(this.value);
                resetPageStates();
                filterAndPaginate();
            });
        }

        // Payment Method interactive AJAX update
        document.querySelectorAll('.pm-select').forEach(select => {
            select.addEventListener('change', function () {
                const orderId = this.getAttribute('data-order-id');
                const paymentMethod = this.value;
                const origBg = this.style.backgroundColor;

                this.style.backgroundColor = '#fff3cd';

                fetch(`/admin-finance/sales-order/${orderId}/update-payment-method`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ payment_method: paymentMethod })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => { throw new Error(text || response.statusText); });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        this.style.backgroundColor = '#d1e7dd';
                        setTimeout(() => { this.style.backgroundColor = origBg; }, 1200);
                    } else {
                        alert('Failed to update payment method: ' + (data.message || 'Unknown error'));
                        this.style.backgroundColor = '#f8d7da';
                    }
                })
                .catch(err => {
                    console.error('Payment method error:', err);
                    this.style.backgroundColor = '#d1e7dd';
                    setTimeout(() => { this.style.backgroundColor = origBg; }, 1200);
                });
            });
        });

        // Tab switch visibility for platform filter
        const normalTab = document.getElementById('normal-tab');
        const ecomTab = document.getElementById('ecom-tab');
        const completedEcomTab = document.getElementById('completed-ecom-tab');
        const completedTab = document.getElementById('completed-tab');
        const platformFilterContainer = document.getElementById('platformFilterContainer');

        function updatePlatformFilterVisibility() {
            const activeTab = document.querySelector('#siTabs .nav-link.active');
            const isEcomTab = activeTab && (activeTab.id === 'ecom-tab' || activeTab.id === 'completed-ecom-tab');
            if (platformFilterContainer) {
                platformFilterContainer.style.display = isEcomTab ? 'block' : 'none';
                if (!isEcomTab && platformSelect) {
                    platformSelect.value = '';
                }
            }
        }

        // Run initially
        updatePlatformFilterVisibility();

        // Checkbox variables & events
        const selectAllNormal = document.getElementById('selectAllNormal');
        const selectAllEcom = document.getElementById('selectAllEcom');
        const selectAllCompletedEcom = document.getElementById('selectAllCompletedEcom');
        const normalChecks = document.querySelectorAll('.normal-check');
        const ecomChecks = document.querySelectorAll('.ecom-check');
        const completedEcomChecks = document.querySelectorAll('.completed-ecom-check');
        const bulkActionsBar = document.getElementById('bulkActionsBar');
        const selectedCountEl = document.getElementById('selectedCount');
        const selectedItemLabel = document.getElementById('selectedItemLabel');
        const bulkFinalizeBtn = document.getElementById('bulkFinalizeBtn');
        const bulkPrepareBtn = document.getElementById('bulkPrepareBtn');
        const bulkPrintSIBtn = document.getElementById('bulkPrintSIBtn');
        const bulkSetPaidBtn = document.getElementById('bulkSetPaidBtn');

        function updateBulkBar() {
            const activeTab = document.querySelector('#siTabs .nav-link.active');
            const isCompletedEcom = activeTab && activeTab.id === 'completed-ecom-tab';
            const isEcom = activeTab && activeTab.id === 'ecom-tab';
            const isNormal = activeTab && activeTab.id === 'normal-tab';
            const isCompleted = activeTab && activeTab.id === 'completed-tab';

            let checkedBoxes = [];
            if (isCompletedEcom) {
                checkedBoxes = Array.from(document.querySelectorAll('#completed-ecom-pane .completed-ecom-check:checked'));
            } else if (isEcom) {
                checkedBoxes = Array.from(document.querySelectorAll('#ecom-pane .ecom-check:checked'));
            } else if (isNormal) {
                checkedBoxes = Array.from(document.querySelectorAll('#normal-pane .normal-check:checked'));
            } else {
                checkedBoxes = Array.from(document.querySelectorAll('.order-checkbox:checked'));
            }

            const checkedCount = checkedBoxes.length;
            if (selectedCountEl) selectedCountEl.textContent = checkedCount;

            if (selectedItemLabel) {
                selectedItemLabel.textContent = (isCompletedEcom || isCompleted) ? 'Sales Invoice(s)' : 'Sales Order(s)';
            }

            if (checkedCount > 0) {
                bulkActionsBar.classList.remove('d-none');
                bulkActionsBar.classList.add('d-flex');
            } else {
                bulkActionsBar.classList.remove('d-flex');
                bulkActionsBar.classList.add('d-none');
            }

            // Calculate total amount of selected orders
            const totalAmountContainer = document.getElementById('selectedTotalAmount');
            const totalAmountValue = document.getElementById('totalAmountValue');
            if (totalAmountContainer && totalAmountValue) {
                let total = 0;
                checkedBoxes.forEach(cb => {
                    const amt = parseFloat(cb.getAttribute('data-amount'));
                    if (!isNaN(amt)) total += amt;
                });
                if (checkedCount > 0 && total >= 0) {
                    totalAmountValue.textContent = '₱' + total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    totalAmountContainer.classList.remove('d-none');
                } else {
                    totalAmountContainer.classList.add('d-none');
                }
            }

            // Control action button visibilities based on active tab
            if (isCompletedEcom) {
                if (bulkSetPaidBtn) bulkSetPaidBtn.classList.remove('d-none');
                if (bulkPrepareBtn) bulkPrepareBtn.classList.add('d-none');
                if (bulkFinalizeBtn) bulkFinalizeBtn.classList.add('d-none');
                if (bulkPrintSIBtn) bulkPrintSIBtn.classList.add('d-none');
            } else if (isEcom) {
                if (bulkSetPaidBtn) bulkSetPaidBtn.classList.add('d-none');
                if (bulkPrepareBtn) bulkPrepareBtn.classList.add('d-none');
                if (bulkFinalizeBtn) bulkFinalizeBtn.classList.remove('d-none');
                if (bulkPrintSIBtn) {
                    const ecomCheckedPrintable = document.querySelectorAll('#ecom-pane .ecom-print-check:checked').length;
                    if (ecomCheckedPrintable > 0) {
                        bulkPrintSIBtn.classList.remove('d-none');
                    } else {
                        bulkPrintSIBtn.classList.add('d-none');
                    }
                }
            } else if (isNormal) {
                if (bulkSetPaidBtn) bulkSetPaidBtn.classList.add('d-none');
                if (bulkPrepareBtn) bulkPrepareBtn.classList.remove('d-none');
                if (bulkFinalizeBtn) bulkFinalizeBtn.classList.remove('d-none');
                if (bulkPrintSIBtn) bulkPrintSIBtn.classList.add('d-none');
            } else {
                if (bulkSetPaidBtn) bulkSetPaidBtn.classList.add('d-none');
                if (bulkPrepareBtn) bulkPrepareBtn.classList.add('d-none');
                if (bulkFinalizeBtn) bulkFinalizeBtn.classList.add('d-none');
                if (bulkPrintSIBtn) bulkPrintSIBtn.classList.add('d-none');
            }
        }

        // Tab switch listeners to update platform filter and bulk bar
        document.querySelectorAll('#siTabs button[data-bs-toggle="tab"]').forEach(tabBtn => {
            tabBtn.addEventListener('shown.bs.tab', function () {
                updatePlatformFilterVisibility();
                updateBulkBar();
                resetPageStates();
                filterAndPaginate();
            });
        });

        if (selectAllNormal) {
            selectAllNormal.addEventListener('change', function() {
                normalChecks.forEach(cb => {
                    if (!cb.disabled && cb.closest('tr').style.display !== 'none') {
                        cb.checked = selectAllNormal.checked;
                    }
                });
                updateBulkBar();
            });
        }

        if (selectAllEcom) {
            selectAllEcom.addEventListener('change', function() {
                ecomChecks.forEach(cb => {
                    if (cb.closest('tr').style.display !== 'none') {
                        cb.checked = selectAllEcom.checked;
                    }
                });
                updateBulkBar();
            });
        }

        if (selectAllCompletedEcom) {
            selectAllCompletedEcom.addEventListener('change', function() {
                document.querySelectorAll('#completed-ecom-pane .completed-ecom-check').forEach(cb => {
                    if (cb.closest('tr').style.display !== 'none') {
                        cb.checked = selectAllCompletedEcom.checked;
                    }
                });
                updateBulkBar();
            });
        }

        document.querySelectorAll('.order-checkbox').forEach(cb => {
            cb.addEventListener('change', updateBulkBar);
        });

        // Print Selected SIs
        if (bulkPrintSIBtn) {
            bulkPrintSIBtn.addEventListener('click', function () {
                const selected = document.querySelectorAll('.ecom-print-check:checked');
                if (selected.length === 0) {
                    alert('Please select at least one e-com order to print.');
                    return;
                }
                const ids = Array.from(selected).map(cb => cb.getAttribute('data-order-id')).filter(id => id);
                if (ids.length > 0) {
                    const url = '{{ route("admin-finance.accounting.sales-invoice.bulk-print") }}?ids=' + ids.join(',');
                    window.open(url, '_blank');
                }
            });
        }

        // Bulk Set as Paid for Completed E-Com Invoices
        if (bulkSetPaidBtn) {
            bulkSetPaidBtn.addEventListener('click', function () {
                const selectedCheckboxes = document.querySelectorAll('#completed-ecom-pane .completed-ecom-check:checked');
                if (selectedCheckboxes.length === 0) {
                    alert('Please select at least one completed E-com sales invoice to set as paid.');
                    return;
                }

                const siIds = [];
                const soIds = [];
                selectedCheckboxes.forEach(cb => {
                    const siId = cb.getAttribute('data-si-id') || cb.value;
                    const soId = cb.getAttribute('data-so-id');
                    if (siId) siIds.push(siId);
                    if (soId) soIds.push(soId);
                });

                if (!confirm(`Are you sure you want to set ${selectedCheckboxes.length} selected E-Commerce invoice(s) as PAID?`)) {
                    return;
                }

                bulkSetPaidBtn.disabled = true;
                const origHtml = bulkSetPaidBtn.innerHTML;
                bulkSetPaidBtn.innerHTML = '<i class="las la-spinner la-spin me-1"></i> Processing...';

                fetch('{{ route("admin-finance.accounting.sales-invoice.bulk-set-paid") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        si_ids: siIds,
                        so_ids: soIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message || 'Successfully marked selected invoices as Paid.');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to update payment status.'));
                        bulkSetPaidBtn.disabled = false;
                        bulkSetPaidBtn.innerHTML = origHtml;
                    }
                })
                .catch(err => {
                    console.error('Bulk set as paid error:', err);
                    alert('An error occurred while marking invoices as paid.');
                    bulkSetPaidBtn.disabled = false;
                    bulkSetPaidBtn.innerHTML = origHtml;
                });
            });
        }

        function executeBulkProcess(actionType, buttonEl, btnOriginalHtml) {
            const selectedCheckboxes = document.querySelectorAll('.order-checkbox:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (selectedIds.length === 0) {
                alert('Please select at least one sales order.');
                return;
            }

            // Double check if any selected orders are missing Proof of Payment
            let missingProofCount = 0;
            selectedCheckboxes.forEach(cb => {
                if (cb.getAttribute('data-proof') !== 'yes') {
                    missingProofCount++;
                }
            });

            const actionLabel = actionType === 'prepare' ? 'prepare & submit' : 'sign & approve';

            if (missingProofCount > 0) {
                if (!confirm(`Warning: ${missingProofCount} of the selected orders do NOT have a Proof of Payment attached. They will be skipped. Do you still want to proceed to ${actionLabel} the remaining ${selectedIds.length - missingProofCount} order(s)?`)) {
                    return;
                }
            } else if (!confirm(`Are you sure you want to ${actionLabel} the ${selectedIds.length} selected Sales Order(s)?`)) {
                return;
            }

            if (buttonEl) {
                buttonEl.disabled = true;
                buttonEl.innerHTML = '<i class="las la-spinner la-spin me-1"></i> Processing...';
            }

            fetch('{{ route("admin-finance.accounting.sales-invoice.bulk-finalize") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids: selectedIds, action: actionType })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                    if (buttonEl) {
                        buttonEl.disabled = false;
                        buttonEl.innerHTML = btnOriginalHtml;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during bulk processing.');
                if (buttonEl) {
                    buttonEl.disabled = false;
                    buttonEl.innerHTML = btnOriginalHtml;
                }
            });
        }

        if (bulkPrepareBtn) {
            bulkPrepareBtn.addEventListener('click', function() {
                executeBulkProcess('prepare', bulkPrepareBtn, '<i class="las la-file-invoice me-1"></i> Bulk Prepare & Submit SI');
            });
        }

        if (bulkFinalizeBtn) {
            bulkFinalizeBtn.addEventListener('click', function() {
                executeBulkProcess('sign', bulkFinalizeBtn, '<i class="las la-check-double me-1"></i> Bulk Sign & Approve');
            });
        }
    });
    </script>

    <!-- Record Payment Modal -->
    <div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header border-bottom-0 pb-0">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div>
                            <h4 class="modal-title text-dark fw-bold" id="recordPaymentModalLabel"><i class="las la-money-bill-wave me-2 text-success" style="font-size: 1.5rem;"></i>Payment History & Record Installment</h4>
                            <span class="text-muted small">Record installment and view previous logs for this transaction</span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="margin-top: -15px;"></button>
                    </div>
                </div>
                <form id="recordPaymentForm">
                    <div class="modal-body">
                        <input type="hidden" id="paySoId">
                        <input type="hidden" id="payCustomerId">
                        
                        <div class="card mb-4 border-0 shadow-sm" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="card-body p-3">
                                <div class="row align-items-center g-3 text-center text-md-start">
                                    <div class="col-md-2 border-md-end">
                                        <span class="text-muted small d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Transaction #:</span>
                                        <strong id="paySoNumber" class="text-dark fs-14">SO-0000</strong>
                                    </div>
                                    <div class="col-md-2 border-md-end">
                                        <span class="text-muted small d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Terms:</span>
                                        <span id="payTerms" class="badge badge-info text-white fw-bold">COD</span>
                                    </div>
                                    <div class="col-md-2 border-md-end">
                                        <span class="text-muted small d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Due Date:</span>
                                        <strong id="payDueDate" class="text-dark fs-14">N/A</strong>
                                    </div>
                                    <div class="col-md-2 border-md-end">
                                        <span class="text-muted small d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Grand Total:</span>
                                        <strong id="payTotalAmount" class="text-dark fs-14">₱0.00</strong>
                                    </div>
                                    <div class="col-md-2 border-md-end">
                                        <span class="text-muted small d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Already Paid:</span>
                                        <span id="payAlreadyPaid" class="text-success fw-bold fs-14">₱0.00</span>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="text-muted small d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Remaining Balance:</span>
                                        <span id="payRemainingBalance" class="px-2 py-1 rounded fw-bold text-danger d-inline-block mt-1" style="font-size: 1.1rem; background-color: rgba(217, 37, 28, 0.08); border: 1px solid rgba(217, 37, 28, 0.15);">
                                            ₱0.00
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment History Breakdown Table -->
                        <div class="card mb-4 border-0 shadow-sm" style="border-radius: 8px; overflow: hidden;">
                            <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #e2e8f0;">
                                <span class="fw-bold small text-dark"><i class="las la-history me-1 text-primary"></i> Previous Installments Log</span>
                                <span class="badge badge-secondary" id="payHistoryBadge">0 payments</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 180px; overflow-y: auto;">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Method</th>
                                                <th>Ref # / Check #</th>
                                                <th>Notes</th>
                                                <th>Proof</th>
                                                <th>Recorded By</th>
                                            </tr>
                                        </thead>
                                        <tbody id="payHistoryTableBody">
                                            <tr><td colspan="7" class="text-center py-2 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Loading payment history...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- New Installment Entry Form -->
                        <div id="newPaymentFormFields">
                            <h6 class="fw-bold text-dark border-bottom pb-1 mb-3"><i class="las la-plus-circle me-1 text-success"></i> Add New Installment Payment</h6>

                            <div class="row g-2">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Payment Amount (<span class="pay-curr-symbol">₱</span>) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text pay-curr-symbol">₱</span>
                                        <input type="number" step="0.01" min="0.01" id="payAmountInput" class="form-control fw-bold fs-15 text-primary" required placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Payment Method <span class="text-danger">*</span></label>
                                    <select id="payMethodSelect" class="form-select form-select-sm" required>
                                        <option value="cash">Cash</option>
                                        <option value="gcash">GCash</option>
                                        <option value="maya">Maya</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="check">Check</option>
                                        <option value="card">Credit / Debit Card</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Reference / Check # <span class="text-muted fw-normal">(Optional)</span></label>
                                    <input type="text" id="payRefInput" class="form-control form-control-sm" placeholder="e.g. Ref #123456 or Check #">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Notes / Remarks <span class="text-muted fw-normal">(Optional)</span></label>
                                    <input type="text" id="payNotesInput" class="form-control form-control-sm" placeholder="e.g. 1st installment payment">
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label class="form-label fw-bold small text-dark">Proof of Payment <span class="text-muted fw-normal">(Optional - Image/PDF)</span></label>
                                    <input type="file" id="payProofInput" class="form-control form-control-sm" accept="image/*,.pdf">
                                </div>
                            </div>
                        </div>

                        <div id="fullyPaidNotice" class="alert alert-success d-none text-center py-2 mb-0">
                            <i class="las la-check-circle me-1 fs-16"></i> This order is fully paid. No further payments required.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" id="submitPaymentBtn">
                            <i class="las la-check-circle me-1"></i> Submit Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        async function fetchPaymentHistory(customerId, soId) {
            const tableBody = document.getElementById('payHistoryTableBody');
            const badge = document.getElementById('payHistoryBadge');

            if (!tableBody) return;

            tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-2 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Loading history...</td></tr>';
            if (badge) badge.textContent = 'Loading...';

            try {
                const response = await fetch(`/marketing/customers/${customerId}/transactions/${soId}/payments`);
                const data = await response.json();

                if (!data.payments || data.payments.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-2 text-muted">No previous installments recorded.</td></tr>';
                    if (badge) badge.textContent = '0 payments';
                } else {
                    if (badge) badge.textContent = data.payments.length + ' payment(s)';
                    let rows = '';
                    data.payments.forEach(p => {
                        const proofTag = p.has_proof ? `<a href="${p.proof_url}" target="_blank" class="badge badge-xs bg-light text-primary border"><i class="las la-paperclip me-1"></i>View Proof</a>` : '<span class="text-muted small">None</span>';
                        rows += `<tr>
                            <td class="fw-bold">${p.date}</td>
                            <td class="text-success fw-bold">₱${p.amount.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                            <td><span class="badge bg-light text-dark border">${p.method}</span></td>
                            <td>${p.reference_number}</td>
                            <td>${p.notes}</td>
                            <td>${proofTag}</td>
                            <td><small class="text-muted">${p.recorded_by}</small></td>
                        </tr>`;
                    });
                    tableBody.innerHTML = rows;
                }
            } catch (error) {
                console.error('Error loading payment history:', error);
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-2 text-danger">Failed to load payment history.</td></tr>';
                if (badge) badge.textContent = 'Error';
            }
        }

        // Handle Open Pay Modal Button
        document.body.addEventListener('click', function(e) {
            const payBtn = e.target.closest('.open-pay-modal-btn');
            if (payBtn) {
                const soId = payBtn.dataset.soId;
                const customerId = payBtn.dataset.customerId;
                const soNumber = payBtn.dataset.soNumber;
                const totalAmount = parseFloat(payBtn.dataset.total) || 0;
                const paidAmount = parseFloat(payBtn.dataset.paid) || 0;
                const remainingBalance = parseFloat(payBtn.dataset.remaining) || 0;

                const terms = payBtn.dataset.terms || 'COD';
                const dueDate = payBtn.dataset.dueDate || 'N/A';

                const currSymbol = payBtn.dataset.symbol || (payBtn.dataset.currency === 'USD' ? '$' : (payBtn.dataset.currency === 'EUR' ? '€' : '₱'));

                document.getElementById('paySoId').value = soId;
                document.getElementById('payCustomerId').value = customerId;
                document.getElementById('paySoNumber').textContent = soNumber;
                document.getElementById('payTerms').textContent = terms;
                document.getElementById('payDueDate').textContent = dueDate;
                document.getElementById('payTotalAmount').textContent = currSymbol + totalAmount.toLocaleString(undefined, {minimumFractionDigits: 2});
                document.getElementById('payAlreadyPaid').textContent = currSymbol + paidAmount.toLocaleString(undefined, {minimumFractionDigits: 2});
                document.getElementById('payRemainingBalance').textContent = currSymbol + remainingBalance.toLocaleString(undefined, {minimumFractionDigits: 2});
                
                document.querySelectorAll('.pay-curr-symbol').forEach(el => {
                    el.textContent = currSymbol;
                });
                
                const formFields = document.getElementById('newPaymentFormFields');
                const submitBtn = document.getElementById('submitPaymentBtn');
                const notice = document.getElementById('fullyPaidNotice');

                if (remainingBalance <= 0) {
                    if (formFields) formFields.classList.add('d-none');
                    if (submitBtn) submitBtn.classList.add('d-none');
                    if (notice) notice.classList.remove('d-none');
                } else {
                    if (formFields) formFields.classList.remove('d-none');
                    if (submitBtn) submitBtn.classList.remove('d-none');
                    if (notice) notice.classList.add('d-none');

                    const payAmountInput = document.getElementById('payAmountInput');
                    payAmountInput.value = remainingBalance.toFixed(2);
                    payAmountInput.max = remainingBalance;
                    document.getElementById('payRefInput').value = '';
                    document.getElementById('payNotesInput').value = '';
                    const proofInput = document.getElementById('payProofInput');
                    if (proofInput) proofInput.value = '';
                }

                // Fetch payment history breakdown
                fetchPaymentHistory(customerId, soId);

                const payModalElement = document.getElementById('recordPaymentModal');
                const payModal = bootstrap.Modal.getInstance(payModalElement) || new bootstrap.Modal(payModalElement);
                payModal.show();
            }
        });

        // Handle Submit Payment Form
        document.getElementById('recordPaymentForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const soId = document.getElementById('paySoId').value;
            const customerId = document.getElementById('payCustomerId').value;
            const amount = parseFloat(document.getElementById('payAmountInput').value);
            const paymentMethod = document.getElementById('payMethodSelect').value;
            const referenceNumber = document.getElementById('payRefInput').value;
            const notes = document.getElementById('payNotesInput').value;
            const proofInput = document.getElementById('payProofInput');

            if (!soId || !customerId) return;

            const submitBtn = document.getElementById('submitPaymentBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...';

            const formData = new FormData();
            formData.append('amount', amount);
            formData.append('payment_method', paymentMethod);
            if (referenceNumber) formData.append('reference_number', referenceNumber);
            if (notes) formData.append('notes', notes);
            if (proofInput && proofInput.files[0]) {
                formData.append('proof_of_payment', proofInput.files[0]);
            }

            try {
                const response = await fetch(`/marketing/customers/${customerId}/transactions/${soId}/pay`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    alert('Payment recorded successfully!');
                    window.location.reload();
                } else {
                    alert(data.message || 'Error recording payment.');
                }
            } catch (error) {
                console.error('Error submitting payment:', error);
                alert('An error occurred while submitting payment.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="las la-check-circle me-1"></i> Submit Payment';
            }
        });
    });
    </script>

    <!-- Create Sales Order Modal (Matches official Sales Order UI) -->
    <div class="modal fade" id="createSalesOrderModal" tabindex="-1" aria-labelledby="createSalesOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header border-0 bg-white pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-1" style="max-height: 82vh; overflow-y: auto;">
                    <div class="card order-form border-0 p-0 shadow-none">
                        <!-- Form Header -->
                        <div class="form-header text-center mb-4 pb-3" style="border-bottom: 2px solid #e0e0e0;">
                            <div class="company-info d-flex align-items-center justify-content-center gap-3 mb-2">
                                <div class="company-logo" style="width: 55px; height: 55px; background: #ff0000; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.8rem; font-weight: bold;">C</div>
                                <div class="company-details text-start">
                                    <div class="company-name" style="font-size: 1.15rem; font-weight: 700; color: #333; text-transform: uppercase;">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                                    <div class="company-address" style="font-size: 0.85rem; color: #666;">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                                    <div class="company-contact" style="font-size: 0.85rem; color: #666;">Tel. No.: 921-3984</div>
                                </div>
                            </div>
                            <div class="document-title" style="font-size: 1.5rem; font-weight: 700; color: #333; letter-spacing: 1px; margin-top: 0.5rem;">SALES ORDER</div>
                        </div>

                        <form id="modalSoForm" action="{{ route('marketing.sales-orders.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="source" value="si">

                            <!-- Customer and Order Details Grid -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light">
                                        <h5 class="fw-bold text-dark mb-3" style="font-size: 0.95rem;">Customer Information</h5>
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold text-dark small mb-1">Company: <span class="text-danger">*</span></label>
                                            <select class="form-select form-select-sm" name="customer_id" id="modalCustomerSelect" required>
                                                <option value="" selected disabled>Select Company...</option>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->customer_id }}"
                                                        data-address="{{ $customer->shipping_address ?? $customer->billing_address ?? 'No address found' }}"
                                                        data-customer-name="{{ $customer->customer_name ?? '' }}"
                                                        data-phone="{{ $customer->mobile ?: ($customer->main_phone ?: ($customer->work_phone ?: '')) }}"
                                                        data-representatives='@json($customer->representatives ?? [])'>
                                                        {{ $customer->customer_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label fw-semibold text-dark small mb-1">Site: <span class="text-danger">*</span></label>
                                            <select class="form-select form-select-sm" name="site_name" id="modalSiteSelect" required style="width: 100%;">
                                                <option value="Main Warehouse" selected>Main Warehouse</option>
                                                <option value="Book Sale">Book Sale</option>
                                            </select>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label fw-semibold text-dark small mb-1">Customer Name:</label>
                                            <select class="form-select form-select-sm" name="customer_representative" id="modalCustomerRepSelect">
                                                <option value="">Select Representative...</option>
                                            </select>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label fw-semibold text-dark small mb-1">Contact:</label>
                                            <input type="text" class="form-control form-control-sm" name="customer_contact" id="modalCustomerContactInput" placeholder="Contact number...">
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label fw-semibold text-dark small mb-1">Address:</label>
                                            <textarea class="form-control form-control-sm" name="billing_address" id="modalBillingAddress" rows="2" placeholder="Customer address..."></textarea>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label fw-semibold text-dark small mb-1">Transaction Type: <span class="text-danger">*</span></label>
                                            <select class="form-select form-select-sm" name="type" id="modalTransactionType" required>
                                                <option value="paid" selected>Paid Transaction</option>
                                                <option value="charge">Charge Transaction</option>
                                                <option value="area_consignment">Area Consignment</option>
                                                <option value="area_sales_consignment">Area Sales Consignment</option>
                                                <option value="direct_consignment">Direct Consignment</option>
                                                <option value="foreign">Foreign Order</option>
                                                <option value="complimentary">Complimentary</option>
                                                <option value="cod">Due on Receipt (COD)</option>
                                                <option value="evaluation">Evaluation</option>
                                            </select>
                                        </div>

                                        <div class="mb-2" id="modalAreaSalesStaffGroup" style="display: none;">
                                            <label class="form-label fw-semibold text-dark small mb-1">Area Sales Staff:</label>
                                            <select class="form-select form-select-sm" name="area_sales_staff_id" id="modalAreaSalesStaffSelect">
                                                <option value="" selected disabled>Select Area Sales Staff...</option>
                                                @foreach($areaSalesStaff ?? [] as $staff)
                                                    <option value="{{ $staff->id }}">{{ $staff->name }}{{ $staff->position ? ' - '.$staff->position : '' }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label fw-semibold text-dark small mb-1">Remarks:</label>
                                            <textarea class="form-control form-control-sm" name="remarks" rows="2" placeholder="Additional notes..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light">
                                        <h5 class="fw-bold text-dark mb-3" style="font-size: 0.95rem;">Order Information</h5>
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold text-dark small mb-1">Date:</label>
                                            <input type="date" class="form-control form-control-sm bg-white" value="{{ date('Y-m-d') }}" readonly>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label fw-semibold text-dark small mb-1">S.O. #:</label>
                                            <input type="text" class="form-control form-control-sm bg-white" name="so_number" value="SO-{{ date('Y') }}-{{ rand(1000,9999) }}" readonly>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label fw-semibold text-dark small mb-1">Terms:</label>
                                            <input type="text" class="form-control form-control-sm" name="terms" placeholder="e.g. 30 Days">
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label fw-semibold text-dark small mb-1">REF #:</label>
                                            <input type="text" class="form-control form-control-sm" name="ref_number" placeholder="PO Reference...">
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label fw-semibold text-dark small mb-1">Freight Option:</label>
                                            <select class="form-select form-select-sm" name="freight_option" id="modalFreightOptionSelect">
                                                <option value="">Select Freight Option</option>
                                                <option value="freight_collect">Freight Collect</option>
                                                <option value="freight_billing">Freight Billing</option>
                                                <option value="bill_client">Bill Client</option>
                                            </select>
                                        </div>

                                        <div class="mb-2" id="modalForwarderGroup" style="display: none;">
                                            <label class="form-label fw-semibold text-dark small mb-1">Forwarder / Carrier:</label>
                                            <input type="text" class="form-control form-control-sm" name="forwarder" placeholder="Enter Forwarder (e.g. LBC, J&T, 2GO)">
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label fw-semibold text-dark small mb-1">PO Attachment:</label>
                                            <input type="file" class="form-control form-control-sm" name="attachment" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label fw-semibold text-dark small mb-1">Proof of Payment Attachment:</label>
                                            <input type="file" class="form-control form-control-sm" name="proof_of_payment" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Items Section -->
                            <div class="mb-3">
                                <button type="button" class="btn text-white fw-bold btn-sm mb-3 px-3 shadow-sm" id="modalAddItemBtn" style="background: #ff0000; border-color: #ff0000;">
                                    <i class="las la-plus me-1"></i> Add Item
                                </button>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle mb-0" id="modalItemsTable" style="font-size: 12px; table-layout: fixed;">
                                        <thead style="background: #ff0000; color: #ffffff;">
                                            <tr>
                                                <th style="width: 65px; color: #fff; white-space: nowrap; text-align: center;">QTY</th>
                                                <th style="width: 65px; color: #fff; white-space: nowrap; text-align: center;">UNIT</th>
                                                <th style="color: #fff; white-space: nowrap;">DESCRIPTION / PRODUCT</th>
                                                <th style="width: 105px; color: #fff; white-space: nowrap; text-align: center;">ISBN</th>
                                                <th style="width: 80px; color: #fff; white-space: nowrap; text-align: center;">AREA</th>
                                                <th style="width: 105px; color: #fff; white-space: nowrap; text-align: center;">UNIT PRICE</th>
                                                <th style="width: 125px; color: #fff; white-space: nowrap; text-align: center;">DISCOUNT</th>
                                                <th style="width: 110px; color: #fff; white-space: nowrap; text-align: center;">AMOUNT</th>
                                                <th style="width: 75px; color: #fff; white-space: nowrap; text-align: center;">ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody id="modalItemsBody">
                                            <!-- Dynamic Rows -->
                                        </tbody>
                                        <tfoot class="bg-light">
                                            <tr>
                                                <td colspan="7" class="text-end fw-bold text-uppercase" style="vertical-align: middle; white-space: nowrap;">Items Subtotal:</td>
                                                <td class="text-end fw-bold fs-6 text-dark" id="modalSubtotalAmount" style="vertical-align: middle; white-space: nowrap;">₱ 0.00</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="7" class="text-end fw-bold text-uppercase" style="vertical-align: middle;">
                                                    <div class="d-inline-flex align-items-center justify-content-end gap-2" style="white-space: nowrap;">
                                                        <span class="fw-bold text-dark" style="font-size: 11px;">Discount:</span>
                                                        <input type="number" step="any" min="0" name="discount_value" id="modalDiscountValue" class="form-control form-control-sm text-end bg-white shadow-none" style="width: 80px; display: inline-block; height: 30px;" value="0">
                                                        <select name="discount_type" id="modalDiscountType" class="form-select form-select-sm bg-white shadow-none" style="width: 85px; display: inline-block; height: 30px; font-size: 11px;">
                                                            <option value="amount">₱ (Amt)</option>
                                                            <option value="percentage">% (Pct)</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="text-end fw-bold fs-6 text-danger" id="modalDiscountAmountDisplay" style="vertical-align: middle; white-space: nowrap;">- ₱ 0.00</td>
                                                <td></td>
                                            </tr>
                                            <tr id="modalServiceFeeTotalRow" style="display: none;">
                                                <td colspan="7" class="text-end fw-bold text-uppercase" style="vertical-align: middle; white-space: nowrap;">Service Fee:</td>
                                                <td class="text-end fw-bold fs-6 text-dark" style="vertical-align: middle; white-space: nowrap;">₱ 50.00</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="7" class="text-end fw-bold text-uppercase" style="vertical-align: middle; white-space: nowrap;">Total Amount:</td>
                                                <td class="text-end fw-bold fs-5 text-success" id="modalGrandTotal" style="vertical-align: middle; white-space: nowrap;">₱ 0.00</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn text-white fw-bold px-4" style="background: #ff0000; border-color: #ff0000;">
                                    <i class="las la-check me-1"></i> Create Sales Invoice
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const productsData = @json($products ?? []);
        const itemsBody = document.getElementById('modalItemsBody');
        const addItemBtn = document.getElementById('modalAddItemBtn');
        const grandTotalEl = document.getElementById('modalGrandTotal');
        const subtotalEl = document.getElementById('modalSubtotalAmount');
        const discountValueInput = document.getElementById('modalDiscountValue');
        const discountTypeSelect = document.getElementById('modalDiscountType');
        const discountAmountDisplay = document.getElementById('modalDiscountAmountDisplay');
        const freightOptionSelect = document.getElementById('modalFreightOptionSelect');
        const forwarderGroup = document.getElementById('modalForwarderGroup');
        const serviceFeeRow = document.getElementById('modalServiceFeeTotalRow');
        const customerSelect = document.getElementById('modalCustomerSelect');
        const repSelect = document.getElementById('modalCustomerRepSelect');
        const contactInput = document.getElementById('modalCustomerContactInput');
        const addressTextarea = document.getElementById('modalBillingAddress');
        const transactionTypeSelect = document.getElementById('modalTransactionType');
        const areaStaffGroup = document.getElementById('modalAreaSalesStaffGroup');
        const siteSelect = document.getElementById('modalSiteSelect');

        function updateAllProductOptionsStock() {
            const selectedSite = siteSelect ? siteSelect.value : 'Main Warehouse';
            if (!itemsBody) return;
            itemsBody.querySelectorAll('.item-product').forEach(select => {
                Array.from(select.options).forEach(opt => {
                    if (!opt.value) return;
                    const stockMain = opt.getAttribute('data-stock-main') || 0;
                    const stockBookSale = opt.getAttribute('data-stock-booksale') || 0;
                    const stockVal = (selectedSite === 'Book Sale') ? stockBookSale : stockMain;
                    const baseName = opt.textContent.replace(/\s*\(Stock:\s*\d+\)/, '');
                    opt.textContent = `${baseName} (Stock: ${stockVal})`;
                });
                if (window.jQuery && typeof jQuery.fn.select2 === 'function' && $(select).data('select2')) {
                    $(select).trigger('change.select2');
                }
            });
        }

        if (siteSelect) {
            siteSelect.addEventListener('change', updateAllProductOptionsStock);
        }

        // Initialize Select2 on Customer (Company) dropdown
        if (window.jQuery && typeof jQuery.fn.select2 === 'function' && customerSelect) {
            $('#modalCustomerSelect').select2({
                dropdownParent: $('#createSalesOrderModal'),
                placeholder: 'Select Company...',
                allowClear: true,
                width: '100%'
            }).on('change select2:select', function() {
                const opt = this.options[this.selectedIndex];
                if (!opt) return;
                const addr = opt.getAttribute('data-address');
                const phone = opt.getAttribute('data-phone');
                const repsRaw = opt.getAttribute('data-representatives');

                if (addressTextarea) addressTextarea.value = (addr && addr !== 'No address found') ? addr : '';
                if (contactInput) contactInput.value = phone || '';

                if (repSelect) {
                    repSelect.innerHTML = '<option value="">Select Representative...</option>';
                    if (repsRaw) {
                        try {
                            const reps = JSON.parse(repsRaw);
                            if (Array.isArray(reps)) {
                                reps.forEach(r => {
                                    const rName = r.name || r.rep_name;
                                    if (rName) {
                                        const o = document.createElement('option');
                                        o.value = rName;
                                        o.textContent = rName;
                                        repSelect.appendChild(o);
                                    }
                                });
                            }
                        } catch(e) {}
                    }
                }
            });
        } else if (customerSelect) {
            customerSelect.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                const addr = opt.getAttribute('data-address');
                const phone = opt.getAttribute('data-phone');
                const repsRaw = opt.getAttribute('data-representatives');

                if (addressTextarea) addressTextarea.value = (addr && addr !== 'No address found') ? addr : '';
                if (contactInput) contactInput.value = phone || '';

                if (repSelect) {
                    repSelect.innerHTML = '<option value="">Select Representative...</option>';
                    if (repsRaw) {
                        try {
                            const reps = JSON.parse(repsRaw);
                            if (Array.isArray(reps)) {
                                reps.forEach(r => {
                                    const rName = r.name || r.rep_name;
                                    if (rName) {
                                        const o = document.createElement('option');
                                        o.value = rName;
                                        o.textContent = rName;
                                        repSelect.appendChild(o);
                                    }
                                });
                            }
                        } catch(e) {}
                    }
                }
            });
        }

        // Transaction Type change
        if (transactionTypeSelect) {
            transactionTypeSelect.addEventListener('change', function() {
                const isAreaStaff = this.value === 'area_sales_consignment';
                if (areaStaffGroup) areaStaffGroup.style.display = isAreaStaff ? '' : 'none';
            });
        }

        // Freight Option change
        if (freightOptionSelect) {
            freightOptionSelect.addEventListener('change', function() {
                const isCollect = this.value === 'freight_collect';
                const hasFreight = !!this.value;
                if (forwarderGroup) forwarderGroup.style.display = hasFreight ? '' : 'none';
                if (serviceFeeRow) serviceFeeRow.style.display = isCollect ? '' : 'none';
                calculateModalTotals();
            });
        }

        function calculateModalTotals() {
            let itemsSubtotal = 0;
            if (itemsBody) {
                itemsBody.querySelectorAll('tr').forEach(row => {
                    const qty = parseFloat(row.querySelector('.item-qty')?.value || 0);
                    const price = parseFloat(row.querySelector('.item-price')?.value || 0);
                    const discVal = parseFloat(row.querySelector('.item-disc-val')?.value || 0);
                    const discType = row.querySelector('.item-disc-type')?.value || 'percentage';

                    const gross = qty * price;
                    let discAmt = 0;
                    if (discType === 'percentage') {
                        discAmt = gross * (discVal / 100);
                    } else {
                        discAmt = discVal;
                    }
                    const rowSubtotal = Math.max(0, gross - discAmt);
                    const amtCell = row.querySelector('.item-amount');
                    if (amtCell) amtCell.textContent = '₱ ' + rowSubtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    itemsSubtotal += rowSubtotal;
                });
            }

            if (subtotalEl) subtotalEl.textContent = '₱ ' + itemsSubtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            const overallDiscVal = parseFloat(discountValueInput?.value || 0);
            const overallDiscType = discountTypeSelect?.value || 'amount';
            let overallDiscAmt = 0;
            if (overallDiscType === 'percentage') {
                overallDiscAmt = itemsSubtotal * (overallDiscVal / 100);
            } else {
                overallDiscAmt = overallDiscVal;
            }

            if (discountAmountDisplay) discountAmountDisplay.textContent = '- ₱ ' + overallDiscAmt.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            let serviceFee = 0;
            if (freightOptionSelect?.value === 'freight_collect') {
                serviceFee = 50.00;
            }

            const grandTotal = Math.max(0, itemsSubtotal - overallDiscAmt + serviceFee);
            if (grandTotalEl) grandTotalEl.textContent = '₱ ' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function addModalRow() {
            if (!itemsBody) return;
            const rowIndex = itemsBody.children.length;
            const tr = document.createElement('tr');
            const selectedSite = siteSelect ? siteSelect.value : 'Main Warehouse';

            let productOptions = '<option value="" selected disabled>Select Product...</option>';
            productsData.forEach(p => {
                const stockMain = p.stock_main !== undefined ? p.stock_main : (p.stock || 0);
                const stockBookSale = p.stock_booksale !== undefined ? p.stock_booksale : 0;
                const stockVal = (selectedSite === 'Book Sale') ? stockBookSale : stockMain;
                productOptions += `<option value="${p.id}" data-price="${p.price}" data-isbn="${p.isbn || ''}" data-stock-main="${stockMain}" data-stock-booksale="${stockBookSale}">${p.display_name} (Stock: ${stockVal})</option>`;
            });

            tr.innerHTML = `
                <td><input type="number" min="1" class="form-control form-control-sm item-qty text-center" name="items[${rowIndex}][quantity]" value="1" required style="border: 1px solid #ced4da;"></td>
                <td><input type="text" class="form-control form-control-sm text-center" name="items[${rowIndex}][unit]" value="pcs" style="border: 1px solid #ced4da;"></td>
                <td>
                    <select class="form-select form-select-sm item-product" name="items[${rowIndex}][product_id]" required style="border: 1px solid #ced4da; width: 100%;">
                        ${productOptions}
                    </select>
                </td>
                <td><input type="text" class="form-control form-control-sm item-isbn bg-light text-center" readonly style="border: 1px solid #ced4da;"></td>
                <td><input type="text" class="form-control form-control-sm text-center" name="items[${rowIndex}][area]" placeholder="Area" style="border: 1px solid #ced4da;"></td>
                <td><input type="number" step="0.01" min="0" class="form-control form-control-sm item-price text-end" name="items[${rowIndex}][price]" value="0.00" required style="border: 1px solid #ced4da;"></td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" step="any" min="0" class="form-control form-control-sm item-disc-val text-end px-1 shadow-none" name="items[${rowIndex}][discount_value]" value="0" style="border: 1px solid #ced4da; height: 30px; font-size: 11px;">
                        <select class="form-select form-select-sm item-disc-type px-1 bg-light shadow-none" name="items[${rowIndex}][discount_type]" style="border: 1px solid #ced4da; max-width: 48px; height: 30px; font-size: 10px;">
                            <option value="percentage">%</option>
                            <option value="amount">₱</option>
                        </select>
                    </div>
                </td>
                <td class="text-end fw-bold item-amount">₱ 0.00</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm text-white remove-row-btn p-1" style="background: #ff0000; border-radius: 4px;"><i class="las la-trash"></i></button>
                </td>
            `;

            itemsBody.appendChild(tr);

            const prodSelect = tr.querySelector('.item-product');
            const priceInput = tr.querySelector('.item-price');
            const qtyInput = tr.querySelector('.item-qty');
            const isbnInput = tr.querySelector('.item-isbn');
            const discValInput = tr.querySelector('.item-disc-val');
            const discTypeSelect = tr.querySelector('.item-disc-type');
            const removeBtn = tr.querySelector('.remove-row-btn');

            if (window.jQuery && typeof jQuery.fn.select2 === 'function' && prodSelect) {
                $(prodSelect).select2({
                    dropdownParent: $('#createSalesOrderModal'),
                    placeholder: 'Select Product...',
                    allowClear: true,
                    width: '100%'
                }).on('change select2:select', function() {
                    const selectedOpt = this.options[this.selectedIndex];
                    if (selectedOpt) {
                        const defaultPrice = selectedOpt.getAttribute('data-price') || 0;
                        const isbn = selectedOpt.getAttribute('data-isbn') || '';
                        if (priceInput) priceInput.value = parseFloat(defaultPrice).toFixed(2);
                        if (isbnInput) isbnInput.value = isbn;
                    }
                    calculateModalTotals();
                });
            } else if (prodSelect) {
                prodSelect.addEventListener('change', function() {
                    const selectedOpt = prodSelect.options[prodSelect.selectedIndex];
                    const defaultPrice = selectedOpt.getAttribute('data-price') || 0;
                    const isbn = selectedOpt.getAttribute('data-isbn') || '';
                    if (priceInput) priceInput.value = parseFloat(defaultPrice).toFixed(2);
                    if (isbnInput) isbnInput.value = isbn;
                    calculateModalTotals();
                });
            }

            if (qtyInput) qtyInput.addEventListener('input', calculateModalTotals);
            if (priceInput) priceInput.addEventListener('input', calculateModalTotals);
            if (discValInput) discValInput.addEventListener('input', calculateModalTotals);
            if (discTypeSelect) discTypeSelect.addEventListener('change', calculateModalTotals);

            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    tr.remove();
                    calculateModalTotals();
                });
            }

            calculateModalTotals();
        }

        if (discountValueInput) discountValueInput.addEventListener('input', calculateModalTotals);
        if (discountTypeSelect) discountTypeSelect.addEventListener('change', calculateModalTotals);

        if (addItemBtn) {
            addItemBtn.addEventListener('click', addModalRow);
            if (itemsBody && itemsBody.children.length === 0) {
                addModalRow();
            }
        }
    });
    </script>
@endpush
</x-app-layout>
