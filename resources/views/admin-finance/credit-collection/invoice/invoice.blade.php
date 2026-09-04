<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link href="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <style>
        .billing-card {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .document-title {
            text-align: center; font-size: 1.75rem; font-weight: 700;
            color: #333; margin-top: 1rem; text-transform: uppercase;
        }

        .tab-content { padding-top: 1rem; }

        /* Selectpicker styles and overrides */
        .bootstrap-select .btn { 
            background: #fff !important;
            border: 1px solid #dee2e6 !important;
            color: #495057 !important;
            padding: 0.375rem 0.75rem !important;
            font-size: 0.875rem !important;
            height: calc(1.5em + 0.75rem + 2px) !important;
        }
        .bootstrap-select .dropdown-toggle:focus { outline: none !important; }
        .bootstrap-select .filter-option {
            display: flex;
            align-items: center;
        }
        .product-select-td { 
            position: relative;
        }
        .product-select-td .bootstrap-select {
            width: 100% !important;
        }
        .product-select-td .bootstrap-select .btn {
            width: 100% !important;
        }
        .product-select-td .bootstrap-select .filter-option-inner-inner {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 250px;
        }
        .bootstrap-select .dropdown-menu {
            max-height: 360px !important;
            overflow: hidden !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18) !important;
            border-radius: 6px !important;
        }
        .bootstrap-select .bs-searchbox {
            position: sticky !important;
            top: 0 !important;
            z-index: 1050 !important;
            background: #ffffff !important;
            padding: 8px 10px !important;
            border-bottom: 1px solid #e9ecef !important;
        }
        .bootstrap-select .bs-searchbox input {
            font-size: 0.9rem !important;
            padding: 0.4rem 0.75rem !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            background: #ffffff !important;
            color: #495057 !important;
        }
        .bootstrap-select .dropdown-menu .inner {
            max-height: 280px !important;
            overflow-y: auto !important;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card billing-card">
                <div class="document-title mb-4">ORDER FULFILLMENT</div>

                <div class="card-body p-0">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#to-process" role="tab">To Process</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#pending-invoicing" role="tab">Pending Invoicing</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#completed" role="tab">Completed</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#all-orders" role="tab">All</a>
                        </li>
                    </ul>

                    <div class="tab-content pt-4">
                        <!-- To Process Tab (Active) -->
                        <div class="tab-pane fade show active" id="to-process" role="tabpanel">
                            <div class="px-4">
                                <!-- Search and Filter Row -->
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="input-group shadow-sm">
                                            <span class="input-group-text bg-white border-end-0"><i class="las la-search text-muted"></i></span>
                                            <input type="text" class="form-control border-start-0 ps-0" placeholder="Search SO#, Customer...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-control form-select shadow-sm">
                                            <option value="">Filter: All Status</option>
                                            <option value="verified">Verified</option>
                                            <option value="draft">Draft</option>
                                            <option value="pending">Pending</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5 text-end">
                                        <button class="btn btn-primary shadow-sm btn-sm" data-bs-toggle="modal" data-bs-target="#createSOModal">
                                            <i class="las la-plus me-1"></i> Create SO
                                        </button>
                                    </div>
                                </div>

                                <!-- Table -->
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>SO#</th>
                                                <th>Estimate#</th>
                                                <th>Customer</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th class="ps-3">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Static Placeholder Data: Verified -->
                                            <tr>
                                                <td class="fw-bold text-primary">#SO-1001</td>
                                                <td>EST-001</td>
                                                <td>St. Anthony Parish</td>
                                                <td>Feb 10, 2026</td>
                                                <td>
                                                    <span class="badge badge-success light">
                                                        <i class="las la-check-circle me-1"></i> Verified
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="#" class="btn btn-primary shadow sharp me-1" 
                                                           title="Delivery receipt"
                                                           data-bs-toggle="modal" 
                                                           data-bs-target="#createDRModal"
                                                           data-so-number="SO-1001"
                                                           data-customer="St. Anthony Parish"
                                                           data-terms="Net 30"
                                                           data-memo="Assorted Books"
                                                           data-items='[{"quantity":5,"unit":"PCS","product":{"product_name":"Office Paper A4","unit_price":100}},{"quantity":2,"unit":"BOX","product":{"product_name":"Printer Ink","unit_price":3500}}]'
                                                        ><i class="las la-shipping-fast"></i></a>
                                                        <a href="#" class="btn btn-info shadow sharp" title="View"><i class="las la-eye"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- Static Placeholder Data: Draft -->
                                            <tr>
                                                <td class="fw-bold text-primary">#SO-1002</td>
                                                <td>EST-002</td>
                                                <td>San Lorenzo Ruiz</td>
                                                <td>Feb 12, 2026</td>
                                                <td>
                                                    <span class="badge badge-warning light">
                                                        <i class="las la-clock me-1"></i> Draft
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="#" class="btn btn-warning shadow sharp me-1" title="Edit"><i class="las la-edit"></i></a>
                                                        <a href="#" class="btn btn-success shadow sharp" title="Verify"><i class="las la-check"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @forelse($invoices as $invoice)
                                            <tr>
                                                <td class="fw-bold text-primary">#{{ $invoice->so_number }}</td>
                                                <td>{{ $invoice->ref_number ?? 'EST-' . str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</td>
                                                <td>{{ $invoice->customer->customer_name ?? $invoice->customer->company_name ?? 'Unknown Customer' }}</td>
                                                <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    @if($invoice->status == 'ready_for_delivery' || $invoice->status == 'verified' || $invoice->status == 'approved')
                                                        <span class="badge badge-success light">
                                                            <i class="las la-check-circle me-1"></i> Verified
                                                        </span>
                                                    @elseif($invoice->status == 'draft' || $invoice->status == 'pending')
                                                        <span class="badge badge-warning light">
                                                            <i class="las la-clock me-1"></i> Draft
                                                        </span>
                                                    @else
                                                        <span class="badge badge-info light">{{ ucfirst(str_replace('_', ' ', $invoice->status)) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @if($invoice->status == 'ready_for_delivery' || $invoice->status == 'verified' || $invoice->status == 'approved')
                                                            <a href="#" class="btn btn-primary shadow sharp me-1" 
                                                               title="Delivery receipt"
                                                               data-bs-toggle="modal" 
                                                               data-bs-target="#createDRModal"
                                                               data-so-number="{{ $invoice->so_number }}"
                                                               data-customer="{{ $invoice->customer->customer_name ?? $invoice->customer->company_name ?? 'Unknown Customer' }}"
                                                               data-terms="{{ $invoice->terms ?? 'Net 30' }}"
                                                               data-memo="{{ $invoice->memo ?? '' }}"
                                                               data-items='@json($invoice->items)'
                                                            ><i class="las la-shipping-fast"></i></a>
                                                            <a href="#" class="btn btn-info shadow sharp" title="View"><i class="las la-eye"></i></a>
                                                        @else
                                                            <a href="#" class="btn btn-warning shadow sharp me-1" title="Edit"><i class="las la-edit"></i></a>
                                                            <a href="#" class="btn btn-success shadow sharp" title="Verify"><i class="las la-check"></i></a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                                        <i class="las la-inbox display-4 text-muted mb-3"></i>
                                                        <p class="text-muted mb-0">No orders found.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                            <!-- Pending Invoicing Tab -->
                            <div class="tab-pane fade" id="pending-invoicing" role="tabpanel">
                                <div class="px-4">
                                    <!-- Search and Filter Row -->
                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <div class="input-group shadow-sm">
                                                <span class="input-group-text bg-white border-end-0"><i class="las la-search text-muted"></i></span>
                                                <input type="text" class="form-control border-start-0 ps-0" placeholder="Search DR#, SO#, Customer...">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control form-select shadow-sm">
                                                <option value="">Filter: All Status</option>
                                                <option value="pending">Pending</option>
                                                <option value="overdue">Overdue</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Table -->
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>DR#</th> <!-- Delivery Receipt Number -->
                                                    <th>SO#</th> <!-- Sales Order Number -->
                                                    <th>Customer</th>
                                                    <th>Delivery Date</th>
                                                    <th>Amount</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Placeholder Rows -->
                                                <tr>
                                                    <td class="fw-bold text-dark">DR-16330</td>
                                                    <td class="text-primary">#12345</td>
                                                    <td>ABC Corp</td>
                                                    <td>Jan 28, 2026</td>
                                                    <td class="fw-bold text-dark">₱ 8,200.00</td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <a href="#" class="btn btn-success sharp shadow me-1" 
                                                                title="Sales invoice"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#createSIModal"
                                                                data-dr-number="16330"
                                                                data-so-number="12345"
                                                                data-customer="ABC Corp"
                                                                data-terms="Net 30"
                                                                data-memo="Assorted Books; paid"
                                                                data-items='[{"quantity":5,"unit":"PCS","product":{"product_name":"Office Paper A4","unit_price":800}},{"quantity":2,"unit":"BOX","product":{"product_name":"Printer Ink","unit_price":2100}}]'
                                                            ><i class="las la-file-invoice-dollar"></i></a>
                                                            <a href="#" class="btn btn-info sharp shadow" title="View"><i class="las la-eye"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold text-dark">DR-16331</td>
                                                    <td class="text-primary">#12346</td>
                                                    <td>XYZ Trading</td>
                                                    <td>Jan 29, 2026</td>
                                                    <td class="fw-bold text-dark">₱ 12,450.00</td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <a href="#" class="btn btn-success sharp shadow me-1" 
                                                                title="Sales invoice"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#createSIModal"
                                                                data-dr-number="16331"
                                                                data-so-number="12346"
                                                                data-customer="XYZ Trading"
                                                                data-terms="Net 15"
                                                                data-memo="Supply delivery"
                                                                data-items='[{"quantity":10,"unit":"PCS","product":{"product_name":"Notebooks","unit_price":50}},{"quantity":5,"unit":"PCS","product":{"product_name":"Pens","unit_price":15}}]'
                                                            ><i class="las la-file-invoice-dollar"></i></a>
                                                            <a href="#" class="btn btn-info sharp shadow" title="View"><i class="las la-eye"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Mock Data Loop / Placeholder -->
                                                @forelse($invoices->where('status', 'ready_for_delivery') as $invoice)
                                                <tr>
                                                    <td class="fw-bold text-dark">DR-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</td>
                                                    <td class="text-primary">#{{ $invoice->so_number }}</td>
                                                    <td>{{ $invoice->customer->customer_name ?? $invoice->customer->company_name ?? 'Unknown Customer' }}</td>
                                                    <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                                                    <td class="fw-bold text-dark">₱ {{ number_format($invoice->total_amount ?? 0, 2) }}</td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <a href="#" class="btn btn-success sharp shadow me-1" 
                                                                title="Sales invoice"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#createSIModal"
                                                                data-so-id="{{ $invoice->id }}"
                                                                data-dr-number="{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}"
                                                                data-so-number="{{ $invoice->so_number }}"
                                                                data-customer="{{ $invoice->customer->customer_name ?? $invoice->customer->company_name ?? 'Unknown Customer' }}"
                                                                data-terms="{{ $invoice->terms ?? 'Net 30' }}"
                                                                data-memo="{{ $invoice->memo ?? '' }}"
                                                                data-items='@json($invoice->items)'
                                                            ><i class="las la-file-invoice-dollar"></i></a>
                                                            <a href="#" class="btn btn-info sharp shadow" title="View"><i class="las la-eye"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-5">
                                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                                            <i class="las la-inbox display-4 text-muted mb-3"></i>
                                                            <p class="text-muted mb-0">No pending invoices found.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Completed Tab -->
                            <div class="tab-pane fade" id="completed" role="tabpanel">
                                <div class="px-4">
                                    <!-- Search and Filter Row -->
                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <div class="input-group shadow-sm">
                                                <span class="input-group-text bg-white border-end-0"><i class="las la-search text-muted"></i></span>
                                                <input type="text" class="form-control border-start-0 ps-0" placeholder="Search SI#, DR#, SO#, Customer...">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control form-select shadow-sm">
                                                <option value="">Filter: All Status</option>
                                                <option value="paid">Paid</option>
                                                <option value="unpaid">Unpaid</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Table -->
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>SI#</th> <!-- Sales Invoice Number -->
                                                    <th>DR#</th> <!-- Delivery Receipt Number -->
                                                    <th>SO#</th> <!-- Sales Order Number -->
                                                    <th>Customer</th>
                                                    <th>Invoice Date</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Mock Data Loop / Placeholder -->
                                                @forelse($invoices->whereIn('status', ['completed', 'paid']) as $invoice)
                                                <tr>
                                                    <td class="fw-bold text-dark">SI-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</td>
                                                    <td class="text-muted">DR-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</td>
                                                    <td class="text-primary">#{{ $invoice->so_number }}</td>
                                                    <td>{{ $invoice->customer->customer_name ?? $invoice->customer->company_name ?? 'Unknown Customer' }}</td>
                                                    <td>{{ $invoice->updated_at->format('M d, Y') }}</td>
                                                    <td class="fw-bold text-dark">₱ {{ number_format($invoice->total_amount ?? 0, 2) }}</td>
                                                    <td>
                                                        <span class="badge badge-success light">Paid</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center">
                                                            <a href="#" class="btn btn-info sharp shadow" title="View"><i class="las la-eye"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="8" class="text-center py-5">
                                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                                            <i class="las la-inbox display-4 text-muted mb-3"></i>
                                                            <p class="text-muted mb-0">No completed invoices found.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- All Orders Tab -->
                            <div class="tab-pane fade" id="all-orders" role="tabpanel">
                                <div class="px-4">
                                    <!-- Search and Filter Row -->
                                    <div class="row mb-4 align-items-center">
                                        <div class="col-md-3">
                                            <div class="input-group shadow-sm">
                                                <span class="input-group-text bg-white border-end-0"><i class="las la-search text-muted"></i></span>
                                                <input type="text" class="form-control border-start-0 ps-0" placeholder="Search...">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group shadow-sm">
                                                <span class="input-group-text bg-white border-end-0"><i class="las la-calendar text-muted"></i></span>
                                                <input type="text" class="form-control border-start-0 ps-0 date-range-picker" placeholder="Select Date Range">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control form-select shadow-sm">
                                                <option value="">Filter: Status</option>
                                                <option value="verified">Verified</option>
                                                <option value="pending">Pending</option>
                                                <option value="completed">Completed</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <button id="exportCsvBtn" class="btn btn-outline-success shadow-sm btn-sm"><i class="las la-file-excel me-1"></i> Export CSV</button>
                                        </div>
                                    </div>

                                    <!-- Table -->
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Doc Type</th>
                                                    <th>Doc #</th>
                                                    <th>Customer</th>
                                                    <th>Date</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Mock Data Loop / Placeholder -->
                                                @forelse($invoices as $invoice)
                                                <tr>
                                                    <td>
                                                        @if($invoice->status == 'completed' || $invoice->status == 'paid')
                                                            <span class="badge badge-success light">Sales Invoice</span>
                                                        @elseif($invoice->status == 'ready_for_delivery')
                                                            <span class="badge badge-primary light">Delivery Receipt</span>
                                                        @else
                                                            <span class="badge badge-secondary light">Sales Order</span>
                                                        @endif
                                                    </td>
                                                    <td class="fw-bold text-dark">
                                                        @if($invoice->status == 'completed' || $invoice->status == 'paid')
                                                            SI-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}
                                                        @elseif($invoice->status == 'ready_for_delivery')
                                                            DR-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}
                                                        @else
                                                            #{{ $invoice->so_number }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $invoice->customer->customer_name ?? $invoice->customer->company_name ?? 'Unknown Customer' }}</td>
                                                    <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                                                    <td class="fw-bold text-dark">₱ {{ number_format($invoice->total_amount ?? 0, 2) }}</td>
                                                    <td>
                                                        @if($invoice->status == 'completed' || $invoice->status == 'paid')
                                                            <span class="badge badge-success light">Paid</span>
                                                        @elseif($invoice->status == 'ready_for_delivery')
                                                            <span class="badge badge-warning light">Pending Invoice</span>
                                                        @else
                                                            <span class="badge badge-info light">{{ ucfirst(str_replace('_', ' ', $invoice->status)) }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-2">
                                                            @if($invoice->status == 'ready_for_delivery')
                                                                <a href="#" class="btn btn-success sharp shadow me-1" 
                                                                    title="Sales invoice"
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#createSIModal"
                                                                    data-so-id="{{ $invoice->id }}"
                                                                    data-dr-number="{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}"
                                                                    data-so-number="{{ $invoice->so_number }}"
                                                                    data-customer="{{ $invoice->customer->customer_name ?? $invoice->customer->company_name ?? 'Unknown Customer' }}"
                                                                    data-terms="{{ $invoice->terms ?? 'Net 30' }}"
                                                                    data-memo="{{ $invoice->memo ?? '' }}"
                                                                    data-items='@json($invoice->items)'
                                                                ><i class="las la-file-invoice-dollar"></i></a>
                                                            @endif
                                                            <a href="#" class="btn btn-info sharp shadow" title="View"><i class="las la-eye"></i></a>
                                                            @if($invoice->status != 'completed' && $invoice->status != 'paid')
                                                                <a href="#" class="btn btn-warning sharp shadow" title="Edit"><i class="las la-edit"></i></a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center py-5">
                                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                                            <i class="las la-inbox display-4 text-muted mb-3"></i>
                                                            <p class="text-muted mb-0">No records found.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

    <!-- Create SO Modal -->
    <div class="modal fade" id="createSOModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold text-white"><i class="las la-file-invoice me-2"></i>Create Sales Order</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Header Info -->
                    <div class="row mb-4 border-bottom pb-4">
                        <div class="col-md-6">
                            <h4 class="fw-bold text-primary mb-1">CLARETIAN COMMUNICATIONS FOUNDATION INC.</h4>
                            <p class="text-muted small mb-0">8 Mayumi St., UP Village, Diliman, Quezon City</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h2 class="fw-bold text-primary">SALES ORDER</h2>
                        </div>
                    </div>

                    <!-- Step 1: Find Estimate -->
                    <div class="row mb-4 border-bottom pb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted text-uppercase text-primary"><i class="las la-search me-1"></i>Step 1 — Find Estimate from QuickBooks</label>
                            <div class="alert alert-info py-2 small border-0 shadow-none mb-3">
                                <i class="las la-info-circle me-1"></i> Start by locating the existing estimate in QuickBooks. Enter the estimate number below to auto-populate.
                            </div>
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Estimate Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="estimateInput" placeholder="e.g. 49163">
                                        <button class="btn btn-primary" type="button" onclick="simulateFindEstimate()"><i class="las la-search"></i> Find</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Order Details -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted text-uppercase text-primary">Step 2 — Order Details <span class="text-lowercase fw-normal">(auto-populated)</span></label>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">SO Number</label>
                            <input type="text" class="form-control bg-light fw-bold" value="SO-2026-{{ rand(10000, 99999) }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">QB Reference #</label>
                            <input type="text" class="form-control bg-light" value="49163" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Status</label>
                            <select class="form-select border-primary shadow-sm">
                                <option value="draft" selected>Draft</option>
                                <option value="pending">Pending</option>
                                <option value="verified">Verified</option>
                            </select>
                        </div>
                    </div>

                    <!-- Step 3: Buyer Information -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted text-uppercase text-primary">Step 3 — Buyer Information</label>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Buyer / Customer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="e.g. Sem. Lancelot T. Pineda">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Diocese / Organization</label>
                            <input type="text" class="form-control" placeholder="e.g. Diocese of Aparri">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Complete address">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Contact Number</label>
                            <input type="text" class="form-control" placeholder="e.g. 0966-XXX-XXXX">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Area / Region <span class="text-danger">*</span></label>
                            <select class="form-select">
                                <option value="">Select Area</option>
                                <option value="NCR">NCR – Metro Manila</option>
                                <option value="TUG">TUG – Tuguegarao / Cagayan</option>
                                <option value="VIS">VIS – Visayas</option>
                                <option value="MIN">MIN – Mindanao</option>
                                <option value="LUZ">LUZ – Luzon (Other)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4 border-bottom pb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Buyer Type</label>
                            <select class="form-select">
                                <option>Parish / Church</option>
                                <option>Seminary / Formation House</option>
                                <option>Individual Clergy</option>
                                <option>Bookstore / Reseller</option>
                                <option>Institution / School</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">TIN <span class="text-muted fw-normal">(if applicable)</span></label>
                            <input type="text" class="form-control" placeholder="e.g. 000-000-000-000">
                            <small class="text-muted extra-small">Leave blank if non-VAT institution</small>
                        </div>
                    </div>

                    <!-- Step 4: Payment Terms -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted text-uppercase text-primary">Step 4 — Payment Terms</label>
                            <div class="d-flex gap-3 pt-2">
                                <div class="form-check custom-radio">
                                    <input type="radio" class="form-check-input" name="paytype" id="payCash" value="cash" checked>
                                    <label class="form-check-label fw-bold" for="payCash">💵 CASH</label>
                                </div>
                                <div class="form-check custom-radio">
                                    <input type="radio" class="form-check-input" name="paytype" id="payCharge" value="charge">
                                    <label class="form-check-label fw-bold" for="payCharge">🗂 CHARGE (On Account)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Items Ordered -->
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted text-uppercase text-primary">Step 5 — Items Ordered</label>
                        </div>
                    </div>

                    <div class="row mb-4 bg-light p-3 rounded mx-0">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Apply Discount:</label>
                            <select class="form-select form-select-sm" id="discountType">
                                <option value="0">No Discount</option>
                                <option value="20">Clergy Discount – 20%</option>
                                <option value="15">Bulk Discount – 15%</option>
                                <option value="10">Reseller Discount – 10%</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Percent:</label>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control" id="discountPct" value="0" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <small class="text-muted mb-1"><i class="las la-info-circle me-1"></i> Discount will be applied to all items.</small>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive mb-4 border rounded">
                        <table class="table table-bordered mb-0" id="soItemsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 80px;">QTY</th>
                                    <th style="width: 80px;">UNIT</th>
                                    <th>DESCRIPTION</th>
                                    <th>ISBN</th>
                                    <th>AREA</th>
                                    <th style="width: 150px;">UNIT PRICE</th>
                                    <th style="width: 150px;">AMOUNT</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="number" class="form-control item-qty" value="1" min="1"></td>
                                    <td><input type="text" class="form-control item-unit" placeholder="pcs"></td>
                                    <td class="product-select-td" style="min-width: 250px;">
                                        <select class="form-control selectpicker product-select" data-live-search="true" data-size="8">
                                            <option value="" disabled selected>Select Product...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->name }}" 
                                                        data-isbn="{{ $product->sku ?? $product->barcode ?? '' }}" 
                                                        data-unit="{{ $product->unit ?? 'pcs' }}" 
                                                        data-price="{{ $product->price ?? 0 }}"
                                                        data-area="{{ $product->shelf_number ? ($product->shelf_number . ($product->rack_number ? ' - ' . $product->rack_number : '')) : '' }}">
                                                    {{ $product->name }} (ISBN: {{ $product->sku ?? $product->barcode ?? '-' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control item-isbn" placeholder="ISBN"></td>
                                    <td><input type="text" class="form-control item-area" placeholder="Area"></td>
                                    <td><input type="number" class="form-control item-price" value="0.00" min="0" step="0.01"></td>
                                    <td><input type="text" class="form-control bg-light item-amount fw-bold text-end" value="0.00" readonly></td>
                                    <td class="text-center"><button type="button" class="btn btn-danger btn-xs btn-remove-item shadow"><i class="las la-trash"></i></button></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="8" class="p-2">
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="btnAddItem"><i class="las la-plus me-1"></i> Add Item</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end fw-bold pt-3">TOTAL AMOUNT:</td>
                                    <td class="text-end fw-bold fs-5 pt-3" id="soTotalAmount">₱ 0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Bottom Details -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Terms</label>
                            <select class="form-select">
                                <option value="net30" selected>Net 30</option>
                                <option value="cod">COD</option>
                                <option value="prepaid">Prepaid</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-muted text-uppercase">Memo</label>
                            <input type="text" class="form-control" placeholder="Add a note...">
                        </div>
                    </div>

                    <!-- Verification Section -->
                    <div class="card bg-light border-warning border-start border-4 mb-0">
                        <div class="card-body py-3">
                            <h6 class="fw-bold text-warning mb-3"><i class="las la-check-circle me-2"></i>Verification (Before creating DR)</h6>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check custom-checkbox mb-2">
                                        <input type="checkbox" class="form-check-input" id="checkHardCopy">
                                        <label class="form-check-label" for="checkHardCopy">Hard copy matches QuickBooks entry</label>
                                    </div>
                                    <div class="form-check custom-checkbox">
                                        <input type="checkbox" class="form-check-input" id="checkRedStamp">
                                        <label class="form-check-label" for="checkRedStamp">Red stamp present</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Gathered by:</label>
                                    <input type="text" class="form-control form-control-sm" placeholder="Name">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted">Date:</label>
                                    <input type="date" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted">Time:</label>
                                    <input type="time" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-danger btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-outline-primary btn-sm px-4 shadow-sm"><i class="las la-save me-1"></i> Save Draft</button>
                    <button type="button" class="btn btn-success btn-sm px-4 shadow"><i class="las la-check-double me-1"></i> Mark as Verified</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create SI Modal -->
    <div class="modal fade" id="createSIModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title fw-bold text-white"><i class="las la-file-invoice-dollar me-2"></i>SALES INVOICE</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="si_so_id">
                    <!-- Document Trail -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted text-uppercase text-success"><i class="las la-link me-1"></i>📎 Document Trail</label>
                            <div class="d-flex align-items-center gap-2 p-3 bg-light rounded shadow-sm">
                                <div class="bg-white border rounded px-3 py-2">
                                    <small class="text-muted d-block extra-small">Sales Order</small>
                                    <span class="fw-bold text-success" id="si_trail_so">SO-2026-12345</span>
                                </div>
                                <i class="las la-arrow-right text-muted"></i>
                                <div class="bg-white border rounded px-3 py-2">
                                    <small class="text-muted d-block extra-small">Delivery Receipt</small>
                                    <span class="fw-bold text-success" id="si_trail_dr">DR-16329</span>
                                </div>
                                <i class="las la-arrow-right text-muted"></i>
                                <div class="bg-success text-white border rounded px-3 py-2 shadow-sm">
                                    <small class="text-white-50 d-block extra-small">Current</small>
                                    <span class="fw-bold" id="si_trail_si">SI-67891</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-success py-2 small border-0 shadow-none mb-4">
                        <i class="las la-info-circle me-1"></i> This SI is the final Step 3. All items and prices are locked from the DR and SO.
                    </div>

                    <!-- Meta Details -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted text-uppercase text-success">SI Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control fw-bold border-success shadow-sm" value="67891" id="si_number_input">
                            <small class="text-muted extra-small">Verify against hard copy SI#</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Date</label>
                            <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted text-uppercase text-muted">DR Reference <i class="las la-lock"></i></label>
                            <input type="text" class="form-control bg-light" id="si_dr_number" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Due Date</label>
                            <input type="date" class="form-control border-warning shadow-sm" id="si_due_date">
                            <small class="text-muted extra-small">Auto-set: +30 days from SO terms</small>
                        </div>
                    </div>

                    <!-- Buyer Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase text-muted">Sold To <i class="las la-lock"></i></label>
                            <input type="text" class="form-control bg-light" id="si_customer_name" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">TIN</label>
                            <input type="text" class="form-control" placeholder="000-000-000-000" id="si_tin">
                            <small class="text-muted extra-small">Verify for VATable entities</small>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-muted text-uppercase text-muted">Address <i class="las la-lock"></i></label>
                            <input type="text" class="form-control bg-light" value="Auto-filled Address" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase text-muted">Contact <i class="las la-lock"></i></label>
                            <input type="text" class="form-control bg-light" value="0966-XXX-XXXX" readonly>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive mb-4 border rounded shadow-sm">
                        <table class="table table-bordered mb-0" id="siItemsTable">
                            <thead class="bg-success text-white">
                                <tr>
                                    <th style="width: 80px;">QTY</th>
                                    <th style="width: 80px;">UNIT</th>
                                    <th>DESCRIPTION</th>
                                    <th style="width: 150px;">UNIT PRICE</th>
                                    <th style="width: 150px;">AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Populated via JS -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Items Total:</td>
                                    <td class="text-end fw-bold" id="siItemsTotal">₱ 0.00</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold align-middle">Withholding Tax (2307):</td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" class="form-control text-end" value="0.00" step="0.01">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold pt-3 fs-5 align-middle">GRAND TOTAL:</td>
                                    <td class="text-end fw-bold fs-5 pt-3 text-success" id="siTotalAmount">₱ 0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase text-muted">Terms <i class="las la-lock"></i></label>
                            <input type="text" class="form-control bg-light" id="si_terms" value="Net 30" readonly>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-muted text-uppercase text-muted">Memo <i class="las la-lock"></i></label>
                            <input type="text" class="form-control bg-light" id="si_memo" readonly>
                        </div>
                    </div>

                    <!-- Final Verification Checklist -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light border-0 h-100 shadow-sm">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold text-success mb-2 small text-uppercase"><i class="las la-clipboard-check me-1"></i>Verification Checklist</h6>
                                    <div class="form-check custom-checkbox mb-1">
                                        <input type="checkbox" class="form-check-input" id="si_v1">
                                        <label class="form-check-label extra-small" for="si_v1">Physical SI matches QuickBooks details</label>
                                    </div>
                                    <div class="form-check custom-checkbox mb-1">
                                        <input type="checkbox" class="form-check-input" id="si_v2">
                                        <label class="form-check-label extra-small" for="si_v2">Correct 2307 amount applied (if applicable)</label>
                                    </div>
                                    <div class="form-check custom-checkbox">
                                        <input type="checkbox" class="form-check-input" id="si_v3">
                                        <label class="form-check-label extra-small" for="si_v3">Signatories are present on hard copy</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light border-0 h-100 shadow-sm text-center">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold mb-3 small text-uppercase"><i class="las la-pen-alt me-1"></i>Signatories Required</h6>
                                    <div class="row g-2">
                                        <div class="col-4">
                                            <div class="border-bottom border-dark small fw-bold">Nicole</div>
                                            <div class="extra-small text-muted">Prepared by</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="border-bottom border-dark small">_______</div>
                                            <div class="extra-small text-muted">Verified by</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="border-bottom border-dark small">_______</div>
                                            <div class="extra-small text-muted">Noted by</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-danger btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-info btn-sm px-4 shadow text-white"><i class="las la-print me-1"></i> Print SI</button>
                    <button type="button" class="btn btn-success btn-sm px-4 shadow"><i class="las la-check-double me-1"></i> Finalize Invoice</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create DR Modal -->
    <div class="modal fade" id="createDRModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold text-white"><i class="las la-truck me-2"></i>DELIVERY RECEIPT</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Document Trail -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted text-uppercase text-primary"><i class="las la-link me-1"></i>📎 Document Trail</label>
                            <div class="d-flex align-items-center gap-2 p-3 bg-light rounded">
                                <div class="bg-white border rounded px-3 py-2 shadow-sm">
                                    <small class="text-muted d-block extra-small">Sales Order</small>
                                    <span class="fw-bold text-primary" id="dr_from_so_trail">SO-2026-12345</span>
                                </div>
                                <i class="las la-arrow-right text-muted"></i>
                                <div class="bg-primary text-white border rounded px-3 py-2 shadow-sm">
                                    <small class="text-white-50 d-block extra-small">Current</small>
                                    <span class="fw-bold" id="dr_number_trail">DR-16329</span>
                                </div>
                                <i class="las la-arrow-right text-muted"></i>
                                <div class="bg-white border border-dashed rounded px-3 py-2 text-muted">
                                    <small class="text-muted d-block extra-small">Pending</small>
                                    <span>Sales Invoice</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 small border-0 shadow-none mb-4">
                        <i class="las la-info-circle me-1"></i> This DR was created from <strong id="dr_from_so_msg">SO-2026-12345</strong>. Locked fields are auto-populated.
                    </div>

                    <!-- Meta -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">DR Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control fw-bold border-primary shadow-sm" value="16329">
                            <small class="text-muted extra-small">Verify against hard copy DR#</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Date</label>
                            <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted text-uppercase text-muted">SO Reference <i class="las la-lock"></i></label>
                            <input type="text" class="form-control bg-light" id="dr_so_number" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted text-uppercase text-muted">P.O. # <i class="las la-lock"></i></label>
                            <input type="text" class="form-control bg-light" id="dr_po_number" readonly>
                            <small class="text-muted extra-small">From SO memo</small>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase text-muted">Delivered To <i class="las la-lock"></i></label>
                            <input type="text" class="form-control bg-light" id="dr_customer_name" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase text-muted">Address <i class="las la-lock"></i></label>
                            <input type="text" class="form-control bg-light" value="Auto-filled Address" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase text-muted">Contact <i class="las la-lock"></i></label>
                            <input type="text" class="form-control bg-light" value="CP# 0966-XXX-XXXX" readonly>
                        </div>
                    </div>

                    <!-- Delivery Details -->
                    <div class="row mb-4 border-top pt-4">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase text-primary">Delivery Details</label>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Delivery Method <span class="text-danger">*</span></label>
                            <select class="form-select border-primary shadow-sm">
                                <option>LBC Express</option>
                                <option>Grab Express</option>
                                <option>2GO Express</option>
                                <option>JRS Business</option>
                                <option>J&T Express</option>
                                <option>Pickup</option>
                                <option>Others</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Shipping Type <span class="text-danger">*</span></label>
                            <select class="form-select border-primary shadow-sm">
                                <option>Local (Metro Manila)</option>
                                <option selected>Provincial</option>
                                <option>International</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tracking Number</label>
                            <input type="text" class="form-control" placeholder="e.g. SB#67891">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Additional Tracking #</label>
                            <input type="text" class="form-control" placeholder="e.g. SQ#49163">
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted text-uppercase text-primary">Items — Verify Delivered Quantities</label>
                        </div>
                    </div>
                    <div class="alert alert-warning py-2 small border-0 shadow-none mb-3">
                        <i class="las la-exclamation-triangle me-1"></i> Adjust <strong>QTY Delivered</strong> if any items are out of stock. The backorder will auto-calculate.
                    </div>

                    <div class="table-responsive mb-4 border rounded shadow-sm">
                        <table class="table table-bordered mb-0" id="drItemsTable">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>DESCRIPTION</th>
                                    <th style="width: 80px;">AREA</th>
                                    <th style="width: 100px;">ORDERED</th>
                                    <th style="width: 120px;">DELIVERED <span class="text-warning small">*</span></th>
                                    <th style="width: 100px;">BACKORDER</th>
                                    <th style="width: 150px;">UNIT PRICE</th>
                                    <th style="width: 150px;">AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Populated via JS -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-end fw-bold">Items Subtotal:</td>
                                    <td class="text-end fw-bold" id="drItemsSubtotal">₱ 0.00</td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end fw-bold align-middle">Delivery Charge:</td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" class="form-control text-end fw-bold" id="dr_delivery_charge" value="0.00" step="0.01">
                                        </div>
                                        <small class="text-muted extra-small">From logistics notation</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end fw-bold pt-3 fs-5 align-middle">TOTAL AMOUNT:</td>
                                    <td class="text-end fw-bold fs-5 pt-3 text-primary" id="drTotalAmount">₱ 0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase text-muted">Terms <i class="las la-lock"></i></label>
                            <input type="text" class="form-control bg-light" id="dr_terms" readonly>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-muted text-uppercase text-muted">Memo <i class="las la-lock"></i></label>
                            <input type="text" class="form-control bg-light" id="dr_memo" readonly>
                        </div>
                    </div>

                    <!-- Logistics Verification -->
                    <div class="card bg-light border-0 shadow-sm mb-0">
                        <div class="card-body p-3">
                            <h6 class="fw-bold text-success mb-3"><i class="las la-clipboard-check me-2"></i>Logistics Verification (Red Stamp)</h6>
                            
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-check custom-checkbox mb-1">
                                        <input type="checkbox" class="form-check-input" id="dr_v1">
                                        <label class="form-check-label small" for="dr_v1">Total amount, terms, template, and date have been checked and verified</label>
                                    </div>
                                    <div class="form-check custom-checkbox mb-1">
                                        <input type="checkbox" class="form-check-input" id="dr_v2">
                                        <label class="form-check-label small" for="dr_v2">SI# and SO# notation added at the bottom of transactions</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row align-items-end pt-2 border-top">
                                <div class="col-md-4">
                                    <label class="form-label extra-small text-muted text-uppercase">Gathered by (Logistics):</label>
                                    <select class="form-select form-select-sm" id="dr_gathered_by">
                                        <option value="">Select Staff ▼</option>
                                        @foreach(['Gabriel', 'John Doe', 'Jane Smith', 'Mark Wilson'] as $staff)
                                            <option value="{{ $staff }}">{{ $staff }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label extra-small text-muted text-uppercase">Date:</label>
                                    <input type="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label extra-small text-muted text-uppercase">Time:</label>
                                    <input type="time" class="form-control form-control-sm" value="{{ date('H:i') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-danger btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm px-4 shadow"><i class="las la-save me-1"></i> Save DR</button>
                    <button type="button" class="btn btn-success btn-sm px-4 shadow"><i class="las la-file-invoice-dollar me-1"></i> Save & Create SI</button>
                </div>
            </div>
        </div>
    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Date Range Picker
            $('.date-range-picker').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'MM/DD/YYYY'
                }
            });
    
            $('.date-range-picker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                // Trigger filtering
                const tabId = $(this).closest('.tab-pane').attr('id');
                if (tabId) {
                    const searchSel = `#${tabId} input[placeholder*="Search"]`;
                    const filterSel = `#${tabId} select.form-select`;
                    applyFilters(`#${tabId}`, searchSel, filterSel, '.table', this);
                }
            });
    
            $('.date-range-picker').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                // Trigger filtering
                const tabId = $(this).closest('.tab-pane').attr('id');
                if (tabId) {
                    const searchSel = `#${tabId} input[placeholder*="Search"]`;
                    const filterSel = `#${tabId} select.form-select`;
                    applyFilters(`#${tabId}`, searchSel, filterSel, '.table', this);
                }
            });
            // Sales Order Items Logic
            function calculateRowTotal(row) {
                const qty = parseFloat(row.find('.item-qty').val()) || 0;
                const price = parseFloat(row.find('.item-price').val()) || 0;
                const amount = qty * price;
                row.find('.item-amount').val(amount.toFixed(2));
                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                let total = 0;
                $('.item-amount').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#soTotalAmount').text('₱ ' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            }

            // Add Item
            $('#btnAddItem').click(function() {
                const newRow = $(`
                    <tr>
                        <td><input type="number" class="form-control item-qty" value="1" min="1"></td>
                        <td><input type="text" class="form-control item-unit" placeholder="pcs"></td>
                        <td class="product-select-td" style="min-width: 250px;">
                            <select class="form-control selectpicker product-select" data-live-search="true" data-size="8">
                                <option value="" disabled selected>Select Product...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->name }}" 
                                            data-isbn="{{ $product->sku ?? $product->barcode ?? '' }}" 
                                            data-unit="{{ $product->unit ?? 'pcs' }}" 
                                            data-price="{{ $product->price ?? 0 }}"
                                            data-area="{{ $product->shelf_number ? ($product->shelf_number . ($product->rack_number ? ' - ' . $product->rack_number : '')) : '' }}">
                                        {{ $product->name }} (ISBN: {{ $product->sku ?? $product->barcode ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" class="form-control item-isbn" placeholder="ISBN"></td>
                        <td><input type="text" class="form-control item-area" placeholder="Area"></td>
                        <td><input type="number" class="form-control item-price" value="0.00" min="0" step="0.01"></td>
                        <td><input type="text" class="form-control bg-light item-amount fw-bold text-end" value="0.00" readonly></td>
                        <td class="text-center"><button type="button" class="btn btn-danger btn-xs btn-remove-item shadow"><i class="las la-trash"></i></button></td>
                    </tr>
                `);
                
                $('#soItemsTable tbody').append(newRow);
                if (typeof $.fn.selectpicker === 'function') {
                    newRow.find('.selectpicker').selectpicker();
                }
            });

            // Remove Item
            $(document).on('click', '.btn-remove-item', function() {
                if ($('#soItemsTable tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                    calculateGrandTotal();
                } else {
                     // Clear the last row instead of removing it if it's the only one
                     const row = $(this).closest('tr');
                     row.find('input').val('');
                     row.find('.item-qty').val(1);
                     row.find('.item-price').val(0.00);
                     row.find('.item-amount').val(0.00);
                     if (typeof $.fn.selectpicker === 'function') {
                         row.find('.selectpicker').val('').selectpicker('refresh');
                     }
                     calculateGrandTotal();
                }
            });

            // Calculate on input change
            $(document).on('input', '.item-qty, .item-price', function() {
                calculateRowTotal($(this).closest('tr'));
            });

            // Auto-populate when selecting a product from selectpicker
            $(document).on('change', '.product-select', function() {
                const option = $(this).find('option:selected');
                if (option.length && option.val()) {
                    const row = $(this).closest('tr');
                    const isbn = option.data('isbn') || '';
                    const unit = option.data('unit') || 'pcs';
                    const price = parseFloat(option.data('price')) || 0;
                    const area = option.data('area') || '';
                    
                    row.find('.item-isbn').val(isbn);
                    row.find('.item-unit').val(unit);
                    row.find('.item-price').val(price.toFixed(2));
                    row.find('.item-area').val(area);
                    
                    calculateRowTotal(row);
                }
            });

            // Initialize selectpickers when Create SO Modal is shown
            $('#createSOModal').on('shown.bs.modal', function () {
                if (typeof $.fn.selectpicker === 'function') {
                    $('#createSOModal .selectpicker').selectpicker();
                }
            });

            // --- Create SO Modal Logic ---
            window.simulateFindEstimate = function() {
                const estNum = $('#estimateInput').val();
                if(!estNum) {
                    alert('Please enter an Estimate Number');
                    return;
                }
                
                // Show a brief loading state
                const btn = $('#createSOModal .btn-primary:contains("Find")');
                const originalText = btn.html();
                btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Searching...');
                btn.prop('disabled', true);

                setTimeout(() => {
                    btn.html(originalText);
                    btn.prop('disabled', false);

                    // Auto-populate based on "found" estimate
                    $('#createSOModal input[placeholder="e.g. Sem. Lancelot T. Pineda"]').val('Sem. Lancelot T. Pineda');
                    $('#createSOModal input[placeholder="e.g. Diocese of Aparri"]').val('Diocese of Aparri');
                    $('#createSOModal input[placeholder="Complete address"]').val('8 Mayumi St., UP Village, Diliman, Quezon City');
                    $('#createSOModal select').eq(1).val('NCR');
                    
                    // Add some sample items
                    const tbody = $('#soItemsTable tbody');
                    tbody.empty();
                    const sampleItems = [
                        { qty: 10, unit: 'PCS', desc: 'Preaching the Word (Year B)', isbn: '978-971-501-XXX', area: 'A1', price: 450.00 },
                        { qty: 5, unit: 'PCS', desc: 'Claretian Journal 2026', isbn: '978-971-501-YYY', area: 'B2', price: 350.00 }
                    ];

                    sampleItems.forEach(item => {
                        const amount = item.qty * item.price;
                        const newRow = $(`
                            <tr>
                                <td><input type="number" class="form-control item-qty" value="${item.qty}" min="1"></td>
                                <td><input type="text" class="form-control item-unit" value="${item.unit}"></td>
                                <td class="product-select-td" style="min-width: 250px;">
                                    <select class="form-control selectpicker product-select" data-live-search="true" data-size="8">
                                        <option value="" disabled selected>Select Product...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->name }}" 
                                                    data-isbn="{{ $product->sku ?? $product->barcode ?? '' }}" 
                                                    data-unit="{{ $product->unit ?? 'pcs' }}" 
                                                    data-price="{{ $product->price ?? 0 }}"
                                                    data-area="{{ $product->shelf_number ? ($product->shelf_number . ($product->rack_number ? ' - ' . $product->rack_number : '')) : '' }}">
                                                {{ $product->name }} (ISBN: {{ $product->sku ?? $product->barcode ?? '-' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="text" class="form-control item-isbn" value="${item.isbn}"></td>
                                <td><input type="text" class="form-control item-area" value="${item.area}"></td>
                                <td><input type="number" class="form-control item-price" value="${item.price.toFixed(2)}" min="0" step="0.01"></td>
                                <td><input type="text" class="form-control bg-light item-amount fw-bold text-end" value="${amount.toFixed(2)}" readonly></td>
                                <td class="text-center"><button type="button" class="btn btn-danger btn-xs btn-remove-item shadow"><i class="las la-trash"></i></button></td>
                            </tr>
                        `);
                        tbody.append(newRow);
                        
                        // Check if the item description exists as a product, if not, add it as a temporary option
                        let selectEl = newRow.find('.product-select');
                        let exists = selectEl.find(`option[value="${item.desc}"]`).length > 0;
                        if (!exists) {
                            selectEl.append(`<option value="${item.desc}" selected>${item.desc}</option>`);
                        } else {
                            selectEl.val(item.desc);
                        }
                        
                        if (typeof $.fn.selectpicker === 'function') {
                            selectEl.selectpicker();
                        }
                    });
                    calculateGrandTotal();
                    alert('Estimate #' + estNum + ' found and imported successfully!');
                }, 800);
            };

            // --- Create DR Modal Logic ---
            $('#createDRModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const soNumber = button.data('so-number');
                const customer = button.data('customer');
                const terms = button.data('terms');
                const memo = button.data('memo');
                const items = button.data('items');

                const modal = $(this);
                const generatedDR = 'DR-' + Math.floor(10000 + Math.random() * 90000);

                // Document Trail
                modal.find('#dr_from_so_trail').text('#' + soNumber);
                modal.find('#dr_number_trail').text(generatedDR);
                modal.find('#dr_from_so_msg').text('#' + soNumber);

                modal.find('input[value="16329"]').val(generatedDR.replace('DR-', ''));
                modal.find('#dr_so_number').val('#' + soNumber);
                modal.find('#dr_customer_name').val(customer);
                modal.find('#dr_terms').val(terms);
                modal.find('#dr_memo').val(memo);
                modal.find('#dr_po_number').val(memo); 

                const tbody = modal.find('#drItemsTable tbody');
                tbody.empty();

                if (items && Array.isArray(items)) {
                    items.forEach(item => {
                        const qty = item.quantity || 0;
                        const desc = item.product ? item.product.product_name : (item.description || 'Item Description');
                        const price = item.product ? item.product.unit_price : (item.unit_price || 0);
                        const amount = qty * price;

                        const row = `
                            <tr>
                                <td>${desc}</td>
                                <td>A1</td>
                                <td class="text-center">${qty}</td>
                                <td><input type="number" class="form-control form-control-sm dr-item-delivered" value="${qty}" min="0" max="${qty}"></td>
                                <td class="text-center text-muted">0</td>
                                <td class="dr-item-price" data-price="${price}">₱ ${parseFloat(price).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                                <td class="text-end fw-bold dr-item-amount">₱ ${amount.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                }
                
                modal.find('#dr_delivery_charge').val('0.00');
                updateDRGrandTotal();
            });

            function updateDRGrandTotal() {
                let itemsTotal = 0;
                $('#drItemsTable tbody tr').each(function() {
                    const row = $(this);
                    const qtyDelivered = parseFloat(row.find('.dr-item-delivered').val()) || 0;
                    const price = parseFloat(row.find('.dr-item-price').data('price')) || 0;
                    const amount = qtyDelivered * price;
                    itemsTotal += amount;
                    row.find('.dr-item-amount').text('₱ ' + amount.toLocaleString(undefined, {minimumFractionDigits: 2}));
                    
                    // Backorder calculation
                    const ordered = parseFloat(row.find('td:eq(2)').text()) || 0;
                    const backorder = Math.max(0, ordered - qtyDelivered);
                    row.find('td:eq(4)').text(backorder);
                    if(backorder > 0) row.find('td:eq(4)').addClass('text-danger fw-bold').removeClass('text-muted');
                    else row.find('td:eq(4)').addClass('text-muted').removeClass('text-danger fw-bold');
                });

                $('#drItemsSubtotal').text('₱ ' + itemsTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
                const deliveryCharge = parseFloat($('#dr_delivery_charge').val()) || 0;
                const grandTotal = itemsTotal + deliveryCharge;
                $('#drTotalAmount').text('₱ ' + grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
            }

            $(document).on('input', '.dr-item-delivered, #dr_delivery_charge', function() {
                updateDRGrandTotal();
            });

            // --- Create SI Modal Logic ---
            $('#createSIModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const soId = button.data('so-id');
                const drNumber = button.data('dr-number');
                const soNumber = button.data('so-number');
                const customer = button.data('customer');
                const terms = button.data('terms') || 'Net 30';
                const memo = button.data('memo');
                const items = button.data('items');

                const modal = $(this);
                modal.find('#si_so_id').val(soId || '');
                const generatedSI = 'SI-' + Math.floor(10000 + Math.random() * 90000);
                
                // Document Trail
                modal.find('#si_trail_so').text('#' + soNumber);
                modal.find('#si_trail_dr').text('#' + drNumber);
                modal.find('#si_trail_si').text(generatedSI);

                modal.find('#si_number_input').val(generatedSI.replace('SI-', ''));
                modal.find('#si_dr_number').val('#' + drNumber);
                modal.find('#si_customer_name').val(customer);
                modal.find('#si_terms').val(terms);
                modal.find('#si_memo').val(memo);

                // Due Date Calculation
                const days = parseInt(terms.replace(/\D/g, '')) || 30;
                const dueDate = moment().add(days, 'days').format('YYYY-MM-DD');
                modal.find('#si_due_date').val(dueDate);

                const tbody = modal.find('#siItemsTable tbody');
                tbody.empty();

                let total = 0;
                if (items && Array.isArray(items)) {
                    items.forEach(item => {
                        const qty = item.quantity || 0;
                        const unit = item.unit || 'PCS';
                        const desc = item.product ? item.product.product_name : (item.description || 'Item Description');
                        const price = item.product ? item.product.unit_price : (item.unit_price || 0);
                        const amount = qty * price;
                        total += amount;

                        const row = `
                            <tr>
                                <td class="text-center">${qty}</td>
                                <td>${unit}</td>
                                <td>${desc}</td>
                                <td class="text-end">₱ ${parseFloat(price).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                                <td class="text-end fw-bold">₱ ${amount.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                }
                
                modal.find('#siItemsTotal').text('₱ ' + total.toLocaleString(undefined, {minimumFractionDigits: 2}));
                modal.find('#siTotalAmount').text('₱ ' + total.toLocaleString(undefined, {minimumFractionDigits: 2}));
            });

            // --- Success Output Modal Helper ---
            function showSuccess(message) {
                $('.modal').not('#successModal').modal('hide');
                setTimeout(function() {
                    $('#successMessage').text(message);
                    $('#successModal').modal('show');
                }, 500);
            }

            // --- Generic Search & Filter Logic for All Tabs ---
            // Function to filter tables
            function applyFilters(tabId, searchSelector, filterSelector, tableSelector, dateRangeSelector) {
                const searchVal = $(searchSelector).not('.date-range-picker').val()?.toLowerCase().trim() || '';
                const filterVal = $(filterSelector).val()?.toLowerCase().trim() || '';
                const dateRangeVal = $(dateRangeSelector).val() || '';
                
                let startDate = null;
                let endDate = null;
                if (dateRangeVal) {
                    const parts = dateRangeVal.split(' - ');
                    startDate = moment(parts[0], 'MM/DD/YYYY').startOf('day');
                    endDate = moment(parts[1], 'MM/DD/YYYY').endOf('day');
                }

                // Map column indices for dates: #completed is 4, others are 3
                const dateColIndex = (tabId === '#completed') ? 4 : 3;

                $(`${tabId} ${tableSelector} tbody tr`).each(function() {
                    const row = $(this);
                    
                    // Skip empty state rows
                    if(row.find('td').length === 1 && row.text().includes('No ')) return;

                    const rowText = row.text().toLowerCase();
                    let rowStatus = '';
                    
                    // Extract status based on badge text
                    const badge = row.find('.badge');
                    if(badge.length > 0) {
                        rowStatus = badge.text().toLowerCase().trim();
                        // Map visual text to value in select dropdown where they differ
                        if(rowStatus === 'sales invoice') rowStatus = 'completed'; 
                        if(rowStatus === 'delivery receipt') rowStatus = 'pending'; 
                        if(rowStatus === 'sales order') rowStatus = 'verified'; 
                        if(rowStatus === 'pending invoice') rowStatus = 'pending';
                    }

                    const rowDateTxt = row.find(`td:eq(${dateColIndex})`).text().trim();
                    const rowDate = moment(rowDateTxt, 'MMM DD, YYYY');

                    const matchesSearch = searchVal === '' || rowText.includes(searchVal);
                    const matchesFilter = filterVal === '' || rowStatus.includes(filterVal) || rowStatus === filterVal;
                    const matchesDate = !startDate || (rowDate.isValid() && rowDate.isSameOrAfter(startDate) && rowDate.isSameOrBefore(endDate));

                    if (matchesSearch && matchesFilter && matchesDate) {
                        row.show();
                    } else {
                        row.hide();
                    }
                });
            }

            // To Process Tab
            $('#to-process input[placeholder*="Search"]').on('input', function() {
                applyFilters('#to-process', this, '#to-process select.form-select', '.table', '#to-process .date-range-picker');
            });
            $('#to-process select.form-select').on('change', function() {
                applyFilters('#to-process', '#to-process input[placeholder*="Search"]', this, '.table', '#to-process .date-range-picker');
            });

            // Pending Invoicing Tab
            $('#pending-invoicing input[placeholder*="Search"]').on('input', function() {
                applyFilters('#pending-invoicing', this, '#pending-invoicing select.form-select', '.table', '#pending-invoicing .date-range-picker');
            });
            $('#pending-invoicing select.form-select').on('change', function() {
                applyFilters('#pending-invoicing', '#pending-invoicing input[placeholder*="Search"]', this, '.table', '#pending-invoicing .date-range-picker');
            });

            // Completed Tab
            $('#completed input[placeholder*="Search"]').on('input', function() {
                applyFilters('#completed', this, '#completed select.form-select', '.table', '#completed .date-range-picker');
            });
            $('#completed select.form-select').on('change', function() {
                applyFilters('#completed', '#completed input[placeholder*="Search"]', this, '.table', '#completed .date-range-picker');
            });

            // All Orders Tab
            $('#all-orders input[placeholder*="Search"]').not('.date-range-picker').on('input', function() {
                applyFilters('#all-orders', this, '#all-orders select.form-select', '.table', '#all-orders .date-range-picker');
            });
            $('#all-orders select.form-select').on('change', function() {
                applyFilters('#all-orders', '#all-orders input[placeholder*="Search"]:not(.date-range-picker)', this, '.table', '#all-orders .date-range-picker');
            });

            // Export CSV Logic
            $('#exportCsvBtn').click(function() {
                const rows = [];
                const headers = [];
                
                // Get headers
                $('#all-orders table thead th').each(function() {
                    const text = $(this).text().trim();
                    if (text && text !== 'Actions') headers.push('"' + text + '"');
                });
                rows.push(headers.join(','));

                // Get visible data
                $('#all-orders table tbody tr:visible').each(function() {
                    const row = [];
                    $(this).find('td').each(function(index) {
                        // Skip actions column
                        if (index < headers.length) {
                            row.push('"' + $(this).text().trim().replace(/"/g, '""') + '"');
                        }
                    });
                    if (row.length > 0) rows.push(row.join(','));
                });

                if (rows.length <= 1) {
                    alert('No data to export.');
                    return;
                }

                const csvContent = "data:text/csv;charset=utf-8," + rows.join("\n");
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "invoices_export_" + moment().format('YYYYMMDD') + ".csv");
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
            
            // Hook up dummy View Action
            $(document).on('click', '.btn-info[title="View"]', function(e) {
                e.preventDefault();
                // We'll simulate viewing by opening the SO Modal for now
                $('#createSOModal').modal('show');
                $('#createSOModal .modal-title').html('<i class="las la-eye me-2"></i>View Record Details');
            });

            // Hook up dummy Edit Action
            $(document).on('click', '.btn-warning[title="Edit"]', function(e) {
                e.preventDefault();
                $('#createSOModal').modal('show');
                $('#createSOModal .modal-title').html('<i class="las la-edit me-2"></i>Edit Record');
            });

            // Hook up dummy Verify Action
            $(document).on('click', '.btn-success[title="Verify"]', function(e) {
                e.preventDefault();
                const btn = $(this);
                const row = btn.closest('tr');
                
                // Store the row reference in a data attribute on the confirm button
                $('#confirmVerifyBtn').data('row', row);
                // Set text for confirmation
                const soNumber = row.find('td:eq(0)').text().trim();
                $('#verify_record_name').text(soNumber);
                $('#verifyConfirmModal').modal('show');
            });
            
            // Handle verification confirm
            $('#confirmVerifyBtn').click(function() {
                const row = $(this).data('row');
                $('#verifyConfirmModal').modal('hide');
                
                // Change the badge status for demonstration
                row.find('.badge').removeClass('badge-warning').addClass('badge-success')
                   .html('<i class="las la-check-circle me-1"></i> Verified');
                
                // Replace actions: Remove Verify/Edit, Add DR Receipt
                row.find('td:last-child .d-flex').html(`
                    <a href="#" class="btn btn-primary shadow sharp me-1" title="Delivery receipt" data-bs-toggle="modal" data-bs-target="#createDRModal"><i class="las la-shipping-fast"></i></a>
                    <a href="#" class="btn btn-info shadow sharp" title="View"><i class="las la-eye"></i></a>
                `);
                
                showSuccess('Transaction has been verified.');
            });

            // --- Modal Confirm Actions ---
            // Create SO Modal Buttons
            $('#createSOModal .btn-outline-primary:contains("Save Draft")').click(function() {
                showSuccess('Sales Order saved as draft!');
            });
            $('#createSOModal .btn-success:contains("Mark as Verified")').click(function() {
                showSuccess('Sales Order verified successfully!');
            });

            // Create DR Modal Buttons
            $('#createDRModal .btn-primary:contains("Save DR")').click(function() {
                showSuccess('Delivery Receipt saved successfully!');
            });
            $('#createDRModal .btn-success:contains("Save & Create SI")').click(function() {
                $('#createDRModal').modal('hide');
                setTimeout(() => {
                    $('#createSIModal').modal('show'); // Open SI Modal immediately
                }, 500);
            });

            // Create SI Modal Buttons
            $('#createSIModal .btn-info:contains("Print SI")').click(function() {
                showSuccess('Initiating print dialog...');
                setTimeout(() => {
                    window.print();
                }, 1000);
            });
            
            $('#createSIModal .btn-success:contains("Finalize Invoice")').click(function() {
                const modal = $('#createSIModal');
                const soId = modal.find('#si_so_id').val();
                
                if (soId) {
                    // Disable button to prevent double-submit
                    const btn = $(this);
                    btn.prop('disabled', true).html('<i class="las la-spinner la-spin me-1"></i> Finalizing...');
                    
                    $.ajax({
                        url: `/credit-collection/invoice/${soId}/finalize`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#createSIModal').modal('hide');
                                showSuccess(response.message);
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            } else {
                                alert('Error: ' + response.message);
                                btn.prop('disabled', false).html('<i class="las la-check-double me-1"></i> Finalize Invoice');
                            }
                        },
                        error: function(xhr) {
                            alert('An error occurred while finalizing invoice.');
                            btn.prop('disabled', false).html('<i class="las la-check-double me-1"></i> Finalize Invoice');
                        }
                    });
                    return;
                }

                const drNumber = modal.find('#si_dr_number').val().replace('#', '');
                const soNumber = modal.find('#si_trail_so').text().replace('#', '');
                const customer = modal.find('#si_customer_name').val();
                const amount = modal.find('#siTotalAmount').text();
                const siNumber = 'SI-' + modal.find('#si_number_input').val();
                
                // --- Simulation Logic: Move from Pending to Completed ---
                // Find matching row in Pending Invoicing if it exists (by SO or DR number)
                $('#pending-invoicing tbody tr').each(function() {
                    const row = $(this);
                    if (row.text().includes(soNumber) || row.text().includes(drNumber)) {
                        row.remove();
                    }
                });
                
                // Add to Completed Tab
                const today = moment().format('MMM DD, YYYY');
                const completedTabRow = `
                    <tr>
                        <td class="fw-bold text-dark">${siNumber}</td>
                        <td class="text-muted">DR-${drNumber}</td>
                        <td class="text-primary">#${soNumber}</td>
                        <td>${customer}</td>
                        <td>${today}</td>
                        <td class="fw-bold text-dark">${amount}</td>
                        <td>
                            <span class="badge badge-success light">Paid</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center">
                                <a href="#" class="btn btn-info shadow sharp" title="View"><i class="las la-eye"></i></a>
                            </div>
                        </td>
                    </tr>
                `;
                
                const completedTable = $('#completed tbody');
                
                // Remove empty state if present
                if (completedTable.find('.las-inbox').length > 0) {
                    completedTable.empty();
                }
                completedTable.prepend(completedTabRow);
                
                showSuccess('Sales Invoice finalized and moved to Completed.');
            });

        });
    </script>
    
    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white"><i class="las la-check-circle me-2"></i>Success</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- ... -->
                <div class="modal-body text-center py-5">
                    <i class="las la-check-circle text-success mb-3" style="font-size: 4rem;"></i>
                    <h4 class="mb-2">Success!</h4>
                    <p class="mb-0" id="successMessage">Operation completed successfully.</p>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Verify Confirm Modal -->
    <div class="modal fade" id="verifyConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verify Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to verify <strong id="verify_record_name"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmVerifyBtn">Verify</button>
                </div>
            </div>
        </div>
    </div>

    @endpush
</x-app-layout>
