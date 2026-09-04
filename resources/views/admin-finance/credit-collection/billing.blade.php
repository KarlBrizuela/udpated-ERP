<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .billing-card {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .form-header {
            margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #e0e0e0;
        }

        .form-header .company-info { display: flex; align-items: center; gap: 1rem; }
        .form-header .company-logo {
            width: 60px; height: 60px; background: #ff0000; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 2rem; font-weight: bold;
        }

        .document-title {
            text-align: center; font-size: 1.75rem; font-weight: 700;
            color: #333; margin-top: 1rem; text-transform: uppercase;
        }

        /* Custom nav-tabs styles removed to match Order Fulfillment page */

        .tab-content { padding-top: 1rem; }
        .no-result-msg td { font-style: italic; }

        /* Summary Report Modal Styles */
        .report-selection-list {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 1rem;
            background: #fdfdfd;
        }
        
        .report-item {
            padding: 0.75rem;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .report-item:last-child {
            border-bottom: none;
        }
        
        .report-item-info .id {
            font-weight: 700;
            color: #333;
            display: block;
        }
        
        .report-item-info .customer {
            font-size: 0.8125rem;
            color: #666;
        }
        
        .report-item-info .date {
            font-size: 0.75rem;
            color: #999;
        }
        
        .report-item-amount {
            font-weight: 700;
            color: #2c3e50;
        }

        .report-total-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.25rem;
            border: 1px solid #dee2e6;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card billing-card">
                <div class="document-title mb-4 d-flex justify-content-between align-items-center">
                    <span>BILLING</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin-finance.credit-collection.billing.manual-create') }}" class="btn btn-success btn-sm shadow">
                            <i class="las la-plus me-1"></i> Add SOA
                        </a>
                        <a href="{{ route('admin-finance.credit-collection.jv-requests.create') }}" class="btn btn-primary btn-sm shadow">
                            <i class="las la-file-invoice me-1"></i> New Summary / JV Request
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                        <ul class="nav nav-tabs" id="billingTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="account-statement-tab" data-bs-toggle="tab" href="#account-statement" role="tab">Account Statement</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="freight-billing-tab" data-bs-toggle="tab" href="#freight-billing" role="tab">Freight Billing</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="jv-summary-tab" data-bs-toggle="tab" href="#jv-summary" role="tab">JV Summary Requests</a>
                            </li>
                        </ul>
                        <div class="tab-content pt-4" id="billingTabsContent">
                            <!-- Account Statement Tab -->
                            <div class="tab-pane fade show active" id="account-statement" role="tabpanel">

                                <!-- Sub-tabs for Account Statement -->
                                <div class="px-4">

                                    <div class="custom-tab-1">
                                        <ul class="nav nav-tabs mb-4">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#as-to-prepare">
                                                    <i class="las la-clock me-2"></i>To Prepare
                                                    <span class="badge badge-primary light ms-2 counter-received">{{ $unpaidOrders->count() }}</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#as-drafts">
                                                    <i class="las la-pencil-alt me-2"></i>Drafts
                                                    <span class="badge badge-secondary light ms-2">{{ $statements->where('status', 'draft')->count() }}</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#as-pending">
                                                    <i class="las la-hourglass-half me-2"></i>Pending
                                                    <span class="badge badge-warning light ms-2">{{ $statements->where('status', 'pending')->count() + $jvRequests->where('status', 'pending_accounting')->count() }}</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#as-approved">
                                                    <i class="las la-check-circle me-2"></i>Approved
                                                    <span class="badge badge-success light ms-2">{{ $statements->where('status', 'approved')->count() + $jvRequests->where('status', 'approved')->count() }}</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#as-compiled">
                                                    <i class="las la-archive me-2"></i>Compiled
                                                    <span class="badge badge-warning light ms-2">{{ $statements->where('status', 'compiled')->count() }}</span>
                                                </a>
                                            </li>
                                        </ul>

                                        <!-- Search Bar and Actions Relocated Under Tabs -->
                                        <div class="row mb-4 align-items-center">
                                            <div class="col-md-12">
                                                <div class="input-group shadow-sm">
                                                    <span class="input-group-text bg-white border-end-0"><i class="las la-search text-muted"></i></span>
                                                    <input type="text" id="tableSearch" class="form-control border-start-0" placeholder="Search by statement number, customer name, department, or status...">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <!-- To Prepare Tab -->
                                            <div class="tab-pane fade show active" id="as-to-prepare">
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Order No.</th>
                                                                <th>Customer Name</th>
                                                                <th>Date</th>
                                                                <th>Amount</th>
                                                                <th class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($unpaidOrders ?? [] as $order)
                                                            <tr>
                                                                <td>{{ $order->so_number }}</td>
                                                                <td>{{ $order->customer->customer_name ?? $order->customer->company_name ?? 'Unknown' }}</td>
                                                                <td>{{ Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</td>
                                                                <td class="fw-bold">
                                                                    @if($order->remaining_balance <= 0)
                                                                        <span class="text-success">₱ 0.00</span> <span class="badge bg-success text-white ms-1" style="font-size: 0.7rem;">Paid</span>
                                                                    @elseif($order->total_paid_amount > 0)
                                                                        <span class="text-dark">₱ {{ number_format($order->remaining_balance, 2) }}</span> <small class="text-muted d-block" style="font-size: 0.75rem;">(Paid: ₱{{ number_format($order->total_paid_amount, 2) }})</small>
                                                                    @else
                                                                        ₱ {{ number_format($order->remaining_balance, 2) }}
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="d-flex justify-content-center">
                                                                        <a href="{{ route('admin-finance.credit-collection.billing.create', ['id' => $order->id]) }}" class="btn btn-primary btn-sm shadow" title="Prepare Statement"><i class="las la-file-invoice me-1"></i> Prepare SOA</a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center py-4 text-muted">No pending orders ready for statement preparation.</td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Drafts Tab -->
                                            <div class="tab-pane fade" id="as-drafts">
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>SOA No.</th>
                                                                <th>Customer</th>
                                                                <th>Total</th>
                                                                <th>Date</th>
                                                                <th class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($statements->where('status', 'draft') as $soa)
                                                            <tr>
                                                                <td class="fw-bold">{{ $soa->soa_number }}</td>
                                                                <td>{{ $soa->customer ? ($soa->customer->customer_name ?? $soa->customer->company_name) : 'Unknown' }}</td>
                                                                <td class="fw-bold">₱ {{ number_format($soa->total_amount, 2) }}</td>
                                                                <td>{{ $soa->created_at->format('M d, Y') }}</td>
                                                                <td class="text-center">
                                                                    <div class="d-flex justify-content-center">
                                                                        <a href="{{ route('admin-finance.credit-collection.billing.edit', $soa->id) }}" class="btn btn-primary btn-sm me-1 shadow"><i class="las la-edit"></i> Edit</a>
                                                                        <button class="btn btn-info btn-sm shadow text-white btn-update-status" data-id="{{ $soa->id }}" data-status="pending" data-type="soa"><i class="las la-paper-plane"></i> Submit</button>
                                                                    </div>
                                                                </td>
                                                            </tr>@empty<tr><td colspan="5" class="text-center py-4 text-muted">No draft statements found.</td></tr>@endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Pending Approval Tab -->
                                            <div class="tab-pane fade" id="as-pending">
                                                <h6 class="fw-bold mb-3"><i class="las la-file-invoice me-1"></i> Account Statements</h6>
                                                <div class="table-responsive mb-4">
                                                    <table class="table table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>SOA No.</th>
                                                                <th>Customer</th>
                                                                <th>Total</th>
                                                                <th>Date</th>
                                                                <th class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($statements->where('status', 'pending') as $soa)
                                                            <tr
                                                                data-soa-id="{{ $soa->id }}"
                                                                data-contact="{{ $soa->contact_person ?? $soa->customer->contact_person ?? '' }}"
                                                                data-address="{{ $soa->billing_address ?? $soa->customer->billing_address ?? '' }}"
                                                                data-total="₱ {{ number_format($soa->total_amount, 2) }}"
                                                                data-period-start="{{ $soa->billing_period_start ? \Carbon\Carbon::parse($soa->billing_period_start)->format('M d, Y') : '' }}"
                                                                data-period-end="{{ $soa->billing_period_end ? \Carbon\Carbon::parse($soa->billing_period_end)->format('M d, Y') : '' }}"
                                                            >
                                                                <td class="fw-bold">{{ $soa->soa_number }}</td>
                                                                <td>{{ $soa->customer ? ($soa->customer->customer_name ?? $soa->customer->company_name) : 'Unknown' }}</td>
                                                                <td class="fw-bold">₱ {{ number_format($soa->total_amount, 2) }}</td>
                                                                <td>{{ $soa->created_at->format('M d, Y') }}</td>
                                                                <td class="text-center">
                                                                    <div class="d-flex justify-content-center">
                                                                        <a href="{{ route('admin-finance.credit-collection.billing.show', $soa->id) }}" class="btn btn-info btn-sm me-1 shadow text-white"><i class="las la-eye"></i> View</a>
                                                                        <button class="btn btn-success btn-sm shadow btn-update-status" data-id="{{ $soa->id }}" data-status="approved" data-type="soa"><i class="las la-check"></i> Approve</button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr><td colspan="5" class="text-center py-4 text-muted">No statements pending approval.</td></tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <h6 class="fw-bold mb-3 mt-4"><i class="las la-file-alt me-1"></i> Summary / JV Requests</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>JV No.</th>
                                                                <th>Requested By</th>
                                                                <th>Amount</th>
                                                                <th>Date</th>
                                                                <th class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($jvRequests->where('status', 'pending_accounting') as $jv)
                                                            <tr class="jv-row">
                                                                <td class="fw-bold text-primary">#{{ $jv->jv_number }}</td>
                                                                <td>{{ $jv->requestor->name ?? 'Unknown' }}</td>
                                                                <td class="fw-bold">₱ {{ number_format($jv->total_amount, 2) }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($jv->date)->format('M d, Y') }}</td>
                                                                <td class="text-center">
                                                                    <div class="d-flex justify-content-center">
                                                                        <a href="{{ route('admin-finance.credit-collection.jv-requests.show', $jv->id) }}" class="btn btn-info btn-sm me-1 shadow text-white"><i class="las la-eye"></i> View</a>
                                                                        <form action="{{ route('admin-finance.credit-collection.jv-requests.approve', $jv->id) }}" method="POST">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <button type="submit" class="btn btn-success btn-sm shadow"><i class="las la-check"></i> Approve</button>
                                                                        </form>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr><td colspan="5" class="text-center py-4 text-muted">No JV requests pending approval.</td></tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Approved Tab -->
                                            <div class="tab-pane fade" id="as-approved">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="fw-bold mb-0"><i class="las la-file-invoice me-1"></i> Approved Statements</h6>
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-muted small me-3">
                                                            <i class="las la-info-circle me-1"></i> Select to compile.
                                                        </div>
                                                        <button type="button" id="btnCompileReport" class="btn btn-dark shadow btn-sm" disabled>
                                                            <i class="las la-file-alt me-1"></i> Compile Summary Report
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="table-responsive mb-4">
                                                    <table class="table table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th style="width: 50px;">
                                                                    <div class="form-check custom-checkbox">
                                                                        <input type="checkbox" class="form-check-input" id="checkAll">
                                                                        <label class="form-check-label" for="checkAll"></label>
                                                                    </div>
                                                                </th>
                                                                <th>Statement No.</th>
                                                                <th>Customer Name</th>
                                                                <th>Department</th>
                                                                <th>Total Amount</th>
                                                                <th>Status</th>
                                                                <th class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse(($statements ?? collect())->where('status', 'approved') as $soa)
                                                            <tr
                                                                data-soa-id="{{ $soa->id }}"
                                                            data-contact="{{ $soa->contact_person ?? $soa->customer->contact_person ?? '' }}"
                                                                data-address="{{ $soa->billing_address ?? $soa->customer->billing_address ?? '' }}"
                                                                data-total="₱ {{ number_format($soa->total_amount, 2) }}"
                                                                data-period-start="{{ $soa->billing_period_start ? \Carbon\Carbon::parse($soa->billing_period_start)->format('M d, Y') : '' }}"
                                                                data-period-end="{{ $soa->billing_period_end ? \Carbon\Carbon::parse($soa->billing_period_end)->format('M d, Y') : '' }}"
                                                            >
                                                                <td>
                                                                    <div class="form-check custom-checkbox">
                                                                        <input type="checkbox" class="form-check-input check-item" value="{{ $soa->id }}">
                                                                        <label class="form-check-label"></label>
                                                                    </div>
                                                                </td>
                                                                <td>{{ $soa->soa_number }}</td>
                                                                <td>{{ $soa->customer ? ($soa->customer->customer_name ?? $soa->customer->company_name) : 'Unknown' ?? 'Unknown' }}</td>
                                                                <td>General</td>
                                                                <td class="fw-bold">₱ {{ number_format($soa->total_amount, 2) }}</td>
                                                                <td><span class="badge bg-success">Approved</span></td>
                                                                <td class="text-center">
                                                                    <div class="d-flex justify-content-center">
                                                                        <a href="{{ route('admin-finance.credit-collection.billing.show', ['id' => $soa->id]) }}" class="btn btn-info shadow sharp" title="View Details"><i class="las la-eye"></i></a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="7" class="text-center py-4 text-muted">No approved statements waiting for compilation.</td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                                
                                                <h6 class="fw-bold mb-3 mt-4"><i class="las la-check-double me-1"></i> Approved Summary / JV Requests</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>JV No.</th>
                                                                <th>Client's Name</th>
                                                                <th>Total Amount</th>
                                                                <th>Status</th>
                                                                <th class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($jvRequests->where('status', 'approved') as $jv)
                                                            <tr class="jv-row">
                                                                <td class="fw-bold text-primary">#{{ $jv->jv_number }}</td>
                                                                <td>{{ $jv->client_name ?? ($jv->items->first()->customer_name ?? 'Multiple Clients') }}</td>
                                                                <td class="fw-bold text-dark">₱ {{ number_format($jv->total_amount, 2) }}</td>
                                                                <td><span class="badge badge-success light">Approved</span></td>
                                                                <td class="text-center">
                                                                    <div class="d-flex justify-content-center gap-1">
                                                                        <a href="{{ route('admin-finance.credit-collection.jv-requests.show', $jv->id) }}" class="btn btn-info btn-xs shadow text-white" title="View Details"><i class="las la-eye me-1"></i> View</a>
                                                                        <a href="{{ route('admin-finance.credit-collection.jv-requests.prepare-adjustment', $jv->id) }}" class="btn btn-warning btn-xs shadow text-dark" title="Request for Adjustments/Revision"><i class="las la-edit me-1"></i> Request Adjustment</a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr><td colspan="5" class="text-center py-4 text-muted">No approved JV requests found.</td></tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="mt-3 d-flex align-items-center">
                                                    <div class="form-check custom-checkbox">
                                                        <input type="checkbox" class="form-check-input" id="checkAllBottom">
                                                        <label class="form-check-label fw-light" for="checkAllBottom">Select All Statements</label>
                                                    </div>
                                                    <span class="text-muted small ms-3 selection-count d-none">(0 selected)</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Compiled Tab -->
                                            <div class="tab-pane fade" id="as-compiled" role="tabpanel">
                                                <div class="px-0">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <div class="text-muted small">
                                                            <i class="las la-info-circle me-1"></i> Compiled reports ready for Journal Voucher (JV) request.
                                                        </div>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Report ID</th>
                                                                    <th>Report Date</th>
                                                                    <th>Statements</th>
                                                                    <th>Total Amount</th>
                                                                    <th>Status</th>
                                                                    <th class="text-center">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="compiledReportsBody">
                                                                @forelse(($statements ?? collect())->where('status', 'compiled') as $soa)
                                                                <tr>
                                                                    <td class="fw-bold text-primary">{{ $soa->soa_number }}</td>
                                                                    <td>{{ Carbon\Carbon::parse($soa->created_at)->format('M d, Y') }}</td>
                                                                    <td>1 statement</td>
                                                                    <td class="fw-bold">₱ {{ number_format($soa->total_amount, 2) }}</td>
                                                                    <td><span class="badge badge-warning light">Pending JV</span></td>
                                                                    <td class="text-center">
                                                                        <button class="btn btn-primary px-3 shadow btnCreateJV" 
                                                                            data-report-id="{{ $soa->soa_number }}"
                                                                            data-customer-name="{{ $soa->customer ? ($soa->customer ? ($soa->customer->customer_name ?? $soa->customer->company_name) : 'Unknown') : 'Unknown' }}"
                                                                            data-customer-id="{{ $soa->customer_id }}">
                                                                            <i class="las la-file-invoice me-1"></i> Create JV Request
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                                @empty
                                                                <tr>
                                                                    <td colspan="6" class="text-center py-4 text-muted">No compiled statements found.</td>
                                                                </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- All Tab -->
                                            <div class="tab-pane fade" id="all">
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Statement No.</th>
                                                                <th>Customer Name</th>
                                                                <th>Department</th>
                                                                <th>Billing Period</th>
                                                                <th>Status</th>
                                                                <th class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($statements ?? [] as $soa)
                                                            <tr>
                                                                <td>{{ $soa->soa_number }}</td>
                                                                <td>{{ $soa->customer ? ($soa->customer->customer_name ?? $soa->customer->company_name) : 'Unknown' ?? 'Unknown' }}</td>
                                                                <td>General</td>
                                                                <td>{{ Carbon\Carbon::parse($soa->billing_period_start)->format('M d') }} - {{ Carbon\Carbon::parse($soa->billing_period_end)->format('M d, Y') }}</td>
                                                                <td>
                                                                    @if($soa->status == 'approved')
                                                                        <span class="badge bg-success">Approved</span>
                                                                    @elseif($soa->status == 'compiled')
                                                                        <span class="badge bg-primary">Compiled</span>
                                                                    @else
                                                                        <span class="badge bg-warning text-dark">{{ ucfirst($soa->status) }}</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="d-flex justify-content-center">
                                                                        <a href="{{ route('admin-finance.credit-collection.billing.show', ['id' => $soa->id]) }}" class="btn btn-info shadow sharp me-1" title="View Details"><i class="las la-eye"></i></a>
                                                                        @if($soa->status == 'draft')
                                                                        <a href="{{ route('admin-finance.credit-collection.billing.edit', ['id' => $soa->id]) }}" class="btn btn-primary shadow sharp" title="Edit Statement"><i class="las la-edit"></i></a>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center py-4 text-muted">No statements found in the database.</td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Freight Billing Tab -->
                            <div class="tab-pane fade" id="freight-billing" role="tabpanel">
                                <div class="px-4">
                                    <div class="custom-tab-1">
                                        <div class="d-flex justify-content-end mb-3">
                                            <button type="button" id="btnNewBill" class="btn btn-primary btn-sm shadow px-4">
                                                <i class="las la-plus me-1"></i> New Bill
                                            </button>
                                        </div>
                                        <ul class="nav nav-tabs custom-tab-1 mb-4">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-bs-toggle="tab" href="#fb-drafts">
                                                        <i class="las la-clock me-2"></i>Drafts
                                                        <span class="badge badge-primary light ms-2">{{ ($freightBills ?? collect())->where('status', 'draft')->count() }}</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#fb-pending">
                                                        <i class="las la-hourglass-half me-2"></i>Pending Approval
                                                        <span class="badge badge-warning light ms-2">{{ ($freightBills ?? collect())->where('status', 'pending')->count() }}</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#fb-approved">
                                                        <i class="las la-check-circle me-2"></i>Approved
                                                        <span class="badge badge-success light ms-2">{{ ($freightBills ?? collect())->where('status', 'approved')->count() }}</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#fb-compiled">
                                                        <i class="las la-archive me-2"></i>Compiled
                                                        <span class="badge badge-warning light ms-2">{{ ($freightBills ?? collect())->where('status', 'compiled')->count() }}</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#fb-all">
                                                        <i class="las la-list me-2"></i>All
                                                    </a>
                                                </li>
                                        </ul>

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-md-12">
                                                <div class="input-group shadow-sm">
                                                    <span class="input-group-text bg-white border-end-0"><i class="las la-search text-muted"></i></span>
                                                    <input type="text" id="fb-tableSearch" class="form-control border-start-0" placeholder="Search by Invoice, carrier, or status...">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <!-- FB Drafts -->
                                            <div class="tab-pane fade show active" id="fb-drafts">
                                                <div class="alert alert-info py-2 d-flex align-items-center mb-3">
                                                    <i class="las la-info-circle fs-4 me-2"></i>
                                                    <span class="small fw-bold">{{ ($freightBills ?? collect())->where('status', 'draft')->count() }} draft bills</span>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Bill No.</th>
                                                                <th>Customer</th>
                                                                <th>Amount</th>
                                                                <th>Created</th>
                                                                <th class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse(($freightBills ?? collect())->where('status', 'draft') as $fb)
                                                            <tr>
                                                                <td class="fw-bold">{{ $fb->bill_number }}</td>
                                                                <td>{{ $fb->customer->customer_name ?? $fb->customer->company_name ?? 'Unknown' }}</td>
                                                                <td class="fw-bold">₱ {{ number_format($fb->amount, 2) }}</td>
                                                                <td><span class="text-muted">{{ Carbon\Carbon::parse($fb->bill_date)->format('M d, Y') }}</span></td>
                                                                <td class="text-center">
                                                                    <div class="d-flex justify-content-center">
                                                                        <button class="btn btn-danger btn-sm px-2 shadow me-1 btn-delete-fb" data-id="{{ $fb->id }}"><i class="las la-trash me-1"></i> Delete</button>
                                                                        <button class="btn btn-info btn-sm px-2 shadow text-white btn-update-status" data-id="{{ $fb->id }}" data-status="pending" data-type="fb"><i class="las la-paper-plane me-1"></i> Submit</button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center py-4 bg-light text-muted">No draft freight bills</td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- FB Pending Approval -->
                                            <div class="tab-pane fade" id="fb-pending">
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Bill No.</th>
                                                                <th>Customer</th>
                                                                <th>Amount</th>
                                                                <th>Submitted Date</th>
                                                                <th class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse(($freightBills ?? collect())->where('status', 'pending') as $fb)
                                                            <tr>
                                                                <td class="fw-bold">{{ $fb->bill_number }}</td>
                                                                <td>{{ $fb->customer->customer_name ?? $fb->customer->company_name ?? 'Unknown' }}</td>
                                                                <td class="fw-bold">₱ {{ number_format($fb->amount, 2) }}</td>
                                                                <td>{{ Carbon\Carbon::parse($fb->bill_date)->format('M d, Y') }}</td>
                                                                <td class="text-center">
                                                                    <div class="d-flex justify-content-center">
                                                                        <button class="btn btn-info btn-sm px-2 shadow text-white me-1"><i class="las la-eye me-1"></i> View</button>
                                                                        <button class="btn btn-success btn-sm px-2 shadow btn-update-status" data-id="{{ $fb->id }}" data-status="approved" data-type="fb"><i class="las la-check me-1"></i> Approve</button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center py-4 bg-light text-muted">No pending freight bills</td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- FB Approved -->
                                            <div class="tab-pane fade" id="fb-approved">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div class="text-muted small">
                                                        <i class="las la-info-circle me-1"></i> Select approved bills to compile into a report.
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge badge-info light selection-count d-none me-3">(0 selected)</span>
                                                        <button type="button" id="fb-btnCompileReport" class="btn btn-dark shadow px-4" disabled>
                                                            <i class="las la-file-alt me-1"></i> Compile Freight Summary
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th style="width: 50px;">
                                                                    <div class="form-check custom-checkbox">
                                                                        <input type="checkbox" class="form-check-input" id="fb-checkAll">
                                                                        <label class="form-check-label" for="fb-checkAll"></label>
                                                                    </div>
                                                                </th>
                                                                <th>Invoice No.</th>
                                                                <th>Carrier</th>
                                                                <th>Date</th>
                                                                <th>Amount</th>
                                                                <th>Status</th>
                                                                <th class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse(($freightBills ?? collect())->where('status', 'approved') as $fb)
                                                            <tr>
                                                                <td>
                                                                    <div class="form-check custom-checkbox">
                                                                        <input type="checkbox" class="form-check-input fb-check-item" value="{{ $fb->id }}">
                                                                        <label class="form-check-label"></label>
                                                                    </div>
                                                                </td>
                                                                <td>{{ $fb->bill_number }}</td>
                                                                <td>{{ $fb->customer->customer_name ?? $fb->customer->company_name ?? 'Unknown' }}</td>
                                                                <td>{{ Carbon\Carbon::parse($fb->bill_date)->format('M d, Y') }}</td>
                                                                <td class="fw-bold">₱ {{ number_format($fb->amount, 2) }}</td>
                                                                <td><span class="badge bg-success">Approved</span></td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-info shadow sharp" title="View Details"><i class="las la-eye"></i></button>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="7" class="text-center py-4 bg-light text-muted">No approved freight bills ready for compilation.</td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- FB Compiled -->
                                            <div class="tab-pane fade" id="fb-compiled">
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Report ID</th>
                                                                <th>Date Compiled</th>
                                                                <th>Billings</th>
                                                                <th>Total Amount</th>
                                                                <th>Status</th>
                                                                <th class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                                @forelse(($freightBills ?? collect())->where('status', 'compiled') as $fb)
                                                                <tr>
                                                                    <td class="fw-bold text-primary">{{ $fb->bill_number }}</td>
                                                                    <td>{{ Carbon\Carbon::parse($fb->bill_date)->format('M d, Y') }}</td>
                                                                    <td>1 bill</td>
                                                                    <td class="fw-bold">₱ {{ number_format($fb->amount, 2) }}</td>
                                                                    <td><span class="badge badge-warning light">Pending JV</span></td>
                                                                    <td class="text-center">
                                                                        <button class="btn btn-primary px-3 shadow btnCreateJV" 
                                                                            data-report-id="{{ $fb->bill_number }}"
                                                                            data-customer-name="{{ $fb->customer->customer_name ?? $fb->customer->company_name ?? 'Unknown' }}"
                                                                            data-customer-id="{{ $fb->customer_id }}">
                                                                            <i class="las la-file-invoice me-1"></i> Create JV Request
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                                @empty
                                                                <tr>
                                                                    <td colspan="6" class="text-center py-4 bg-light text-muted">No compiled freight bills found.</td>
                                                                </tr>
                                                                @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- FB All -->
                                            <div class="tab-pane fade" id="fb-all">
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Invoice No.</th>
                                                                <th>Carrier</th>
                                                                <th>Date</th>
                                                                <th>Amount</th>
                                                                <th>Status</th>
                                                                <th class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($freightBills ?? [] as $fb)
                                                            <tr>
                                                                <td>{{ $fb->bill_number }}</td>
                                                                <td>{{ $fb->customer->customer_name ?? $fb->customer->company_name ?? 'Unknown' }}</td>
                                                                <td>{{ Carbon\Carbon::parse($fb->bill_date)->format('M d, Y') }}</td>
                                                                <td class="fw-bold">₱ {{ number_format($fb->amount, 2) }}</td>
                                                                <td>
                                                                    @if($fb->status == 'approved')
                                                                        <span class="badge bg-success">Approved</span>
                                                                    @elseif($fb->status == 'compiled')
                                                                        <span class="badge bg-primary">Compiled</span>
                                                                    @else
                                                                        <span class="badge bg-warning text-dark">{{ ucfirst($fb->status) }}</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="d-flex justify-content-center">
                                                                        <button class="btn btn-info shadow sharp me-1" title="View Details"><i class="las la-eye"></i></button>
                                                                        <button class="btn btn-primary shadow sharp" title="Edit Bill"><i class="las la-edit"></i></button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center py-4 bg-light text-muted">No freight bills found.</td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- JV Summary Tab -->
                            <div class="tab-pane fade" id="jv-summary" role="tabpanel">
                                <div class="px-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="fw-bold mb-0 text-dark">Compiled Summary Reports & JV Requests</h5>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin-finance.credit-collection.jv-requests.index') }}" class="btn btn-outline-primary btn-xs px-3 shadow">
                                                View Batch History <i class="las la-history ms-1"></i>
                                            </a>
                                            <a href="{{ route('admin-finance.credit-collection.jv-requests.create') }}" class="btn btn-primary btn-xs px-3 shadow">
                                                <i class="las la-plus me-1"></i> New Compilation
                                            </a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>JV #</th>
                                                    <th>Date</th>
                                                    <th>Category</th>
                                                    <th class="text-end">Total Amount</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php 
                                                    $recentJvs = \App\Models\JournalVoucherRequest::with('items')->latest()->take(10)->get();
                                                @endphp
                                                @forelse($recentJvs as $jv)
                                                <tr>
                                                    <td class="fw-bold text-primary">#{{ $jv->jv_number }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($jv->date)->format('M d, Y') }}</td>
                                                    <td><span class="badge badge-outline-info badge-xs">{{ $jv->category }}</span></td>
                                                    <td class="text-end fw-bold text-dark">₱ {{ number_format($jv->total_amount, 2) }}</td>
                                                    <td>
                                                            @if($jv->status == 'accounting_verified')
                                                                <span class="badge badge-success light">Verified by Accounting</span>
                                                            @elseif($jv->status == 'pending_manager')
                                                                <span class="badge badge-warning light text-dark">Pending Manager</span>
                                                            @elseif($jv->status == 'pending_accounting')
                                                                <span class="badge badge-warning light text-dark">Pending Accounting</span>
                                                            @else
                                                                <span class="badge badge-primary">{{ ucwords(str_replace('_', ' ', $jv->status)) }}</span>
                                                            @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('admin-finance.credit-collection.jv-requests.show', $jv->id) }}" class="btn btn-info shadow sharp me-1" title="View Compilation"><i class="las la-eye text-white"></i></a>
                                                        <a href="{{ route('admin-finance.credit-collection.jv-requests.print', $jv->id) }}" target="_blank" class="btn btn-dark shadow sharp" title="Print Blue Form"><i class="las la-print"></i></a>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-5 text-muted italic">
                                                        <i class="las la-folder-open fs-2 d-block mb-2"></i>
                                                        No recent JV summaries found. Use "New Compilation" to get started.
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
        </div>
    </div>

    <!-- Create Summary Report Modal -->
    <div class="modal fade" id="summaryReportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title fw-bold text-white"><i class="las la-file-alt me-2"></i>Create Summary Report - Review Selection</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-3 fw-bold">You've selected <span id="modalSelectedCount" class="text-primary">0</span> statements to compile:</p>
                    
                    <div class="report-selection-list mb-4" id="modalSelectionList">
                        <!-- Populated by JS -->
                    </div>

                    <div class="report-total-box mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small fw-bold text-uppercase">Total Statements</span>
                            <span class="fw-bold fs-5" id="modalTotalCountText">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center font-w700">
                            <span class="text-dark fs-5">TOTAL AMOUNT</span>
                            <span class="text-primary fs-4" id="modalTotalAmount">₱ 0.00</span>
                        </div>
                    </div>

                    <div class="row align-items-center mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1">Report Date</label>
                            <input type="date" class="form-control" id="reportDate" value="{{ now()->format('Y-m-d') }}">
                            <div class="text-muted extra-small mt-1">Automatically set to the latest date.</div>
                        </div>
                    </div>

                    <div class="alert alert-warning border-0 small mb-0 py-2 d-flex align-items-center">
                        <i class="las la-exclamation-triangle fs-4 me-2"></i>
                        <div>
                            <strong>Note:</strong> These statements will be marked as <strong>"Compiled"</strong> once the report is generated.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="modal">Back to Selection</button>
                    <button type="button" class="btn btn-primary btn-sm px-4" id="btnConfirmGenerateReport"><i class="las la-check-circle me-1"></i> Generate Report</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Journal Voucher (JV) Modal -->
    <div class="modal fade" id="jvRequestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold text-white"><i class="las la-file-invoice me-2"></i>Create JV Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">JV Number</label>
                            <input type="text" class="form-control bg-light fw-bold" id="jvNumber" readonly value="JV-11083">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Date</label>
                            <input type="text" class="form-control bg-light" id="jvDate" readonly value="{{ now()->format('M d, Y') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-muted text-uppercase">Description</label>
                            <input type="text" class="form-control bg-light" id="jvDescription" readonly value="Summary Report SR-2026-003">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Customer</label>
                            <input type="text" class="form-control bg-light fw-bold text-dark" id="jvCustomerName" readonly value="">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Total Amount</label>
                            <input type="text" class="form-control bg-light fw-bold text-primary" id="jvAmount" readonly value="₱ 0.00">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Reason</label>
                        <textarea class="form-control" id="jvReason" rows="3" placeholder="Explain why this entry should be made..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Remarks (Optional)</label>
                        <textarea class="form-control" id="jvRemarks" rows="2" placeholder="Any special notes..."></textarea>
                    </div>

                    <div class="attachment-checklist p-3 bg-light rounded-3">
                        <h6 class="fw-bold mb-3 small text-uppercase text-primary"><i class="las la-paperclip me-2"></i>Supporting Documents Included:</h6>
                        <div class="form-check custom-checkbox mb-2 text-dark">
                            <input type="checkbox" class="form-check-input" id="attachSummary" checked disabled>
                            <label class="form-check-label small" for="attachSummary">Summary Report (Auto-attached)</label>
                        </div>
                        <div class="form-check custom-checkbox mb-2 text-dark">
                            <input type="checkbox" class="form-check-input" id="attachStatements" checked disabled>
                            <label class="form-check-label small" for="attachStatements">Individual Account Statements</label>
                        </div>
                        <div class="form-check custom-checkbox text-dark">
                            <input type="checkbox" class="form-check-input" id="attachContracts" checked disabled>
                            <label class="form-check-label small" for="attachContracts">Original Customer Contracts</label>
                        </div>
                    </div>

                    <div class="mt-4 p-2 border-start border-primary border-4 bg-light shadow-sm">
                        <span class="small text-muted"><i class="las la-info-circle me-1"></i> This request will be sent to <strong>Accounting</strong> for QuickBooks entry.</span>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm px-4" id="btnSubmitJV"><i class="las la-paper-plane me-1"></i> Submit to Accounting</button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Success Output Modal Helper ---
            function showSuccess(message) {
                $('.modal').not('#successModal').modal('hide');
                setTimeout(function() {
                    $('#successMessage').text(message);
                    $('#successModal').modal('show');
                }, 500);
            }

            // Handle Tab Selection via URL Parameter
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab');
            if (activeTab === 'freight') {
                const freightTabLink = document.querySelector('a[href="#freight-billing"]');
                if (freightTabLink) {
                    const tab = new bootstrap.Tab(freightTabLink);
                    tab.show();
                }
            }

            if (activeTab === 'jv') {
                const jvTabLink = document.querySelector('a[href="#jv-summary"]');
                if (jvTabLink) {
                    const tab = new bootstrap.Tab(jvTabLink);
                    tab.show();
                }
            }

            if (activeTab === 'reconsignment') {
                const reconsignmentTabLink = document.querySelector('a[href="#reconsignment-requests"]');
                if (reconsignmentTabLink) {
                    const tab = new bootstrap.Tab(reconsignmentTabLink);
                    tab.show();
                }
            }

            // Generic Search Functionality
            function setupSearch(inputId, containerSelector) {
                const searchInput = document.getElementById(inputId);
                if (!searchInput) return;

                searchInput.addEventListener('keyup', function() {
                    const query = this.value.toLowerCase();
                    const container = containerSelector ? document.querySelector(containerSelector) : document;
                    const rows = container.querySelectorAll('.tab-pane .table tbody tr');

                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (text.includes(query)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Handle 'No records found'
                    container.querySelectorAll('.tab-pane').forEach(pane => {
                        const visibleRows = pane.querySelectorAll('tbody tr:not([style*="display: none"]):not(.no-result-msg)').length;
                        const noResultMsg = pane.querySelector('.no-result-msg');
                        
                        if (visibleRows === 0) {
                            if (!noResultMsg) {
                                const tr = document.createElement('tr');
                                tr.className = 'no-result-msg';
                                tr.innerHTML = `<td colspan="10" class="text-center py-4 bg-light text-muted">No records found matching "${query}"</td>`;
                                pane.querySelector('tbody').appendChild(tr);
                            }
                        } else if (noResultMsg) {
                            noResultMsg.remove();
                        }
                    });
                });
            }

            setupSearch('tableSearch', '#account-statement');
            setupSearch('fb-tableSearch', '#freight-billing');

            // Initial search check logic would go here if needed

            // Handle Bulk Checkboxes
            const checkAll = document.getElementById('checkAll');
            const fbCheckAll = document.getElementById('fb-checkAll');
            const checkAllBottom = document.getElementById('checkAllBottom');

            function updateSelectionUI(section) {
                const isFB = section === 'fb';
                const container = isFB ? document.getElementById('freight-billing') : document.getElementById('account-statement');
                const itemSelector = isFB ? '.fb-check-item' : '.check-item';
                const mainCheck = isFB ? fbCheckAll : checkAll;
                const compileBtn = isFB ? document.getElementById('fb-btnCompileReport') : document.getElementById('btnCompileReport');
                const selectionLabels = container.querySelectorAll('.selection-count');
                
                const checkboxes = document.querySelectorAll(itemSelector);
                const checkedCount = document.querySelectorAll(`${itemSelector}:checked`).length;
                const isAllChecked = (checkedCount === checkboxes.length && checkboxes.length > 0);
                
                // Update Compile Button state
                if (compileBtn) {
                    compileBtn.disabled = checkedCount === 0;
                }

                // Update all selection labels in this section
                selectionLabels.forEach(label => {
                    if (checkedCount > 0) {
                        label.textContent = `(${checkedCount} selected)`;
                        label.classList.remove('d-none');
                    } else {
                        label.classList.add('d-none');
                    }
                });

                if (mainCheck) {
                    mainCheck.checked = isAllChecked;
                }
                
                if (!isFB && checkAllBottom) {
                    checkAllBottom.checked = isAllChecked;
                }
            }

            if(checkAll) {
                checkAll.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.check-item');
                    checkboxes.forEach(cb => {
                        cb.checked = this.checked;
                    });
                    updateSelectionUI('as');
                });
            }

            if(fbCheckAll) {
                fbCheckAll.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.fb-check-item');
                    checkboxes.forEach(cb => {
                        cb.checked = this.checked;
                    });
                    updateSelectionUI('fb');
                });
            }

            if(checkAllBottom) {
                checkAllBottom.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.check-item');
                    checkboxes.forEach(cb => {
                        cb.checked = this.checked;
                    });
                    updateSelectionUI('as');
                });
            }

            // Use delegation for items
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('check-item')) {
                    updateSelectionUI('as');
                }
                if (e.target.classList.contains('fb-check-item')) {
                    updateSelectionUI('fb');
                }
            });

            // Handle Summary Report Modal Population (Combined for both if targets are the same)
            function populationModal(btnId, itemSelector, reportPrefix) {
                const btn = document.getElementById(btnId);
                if(!btn) return;

                btn.addEventListener('click', function() {
                    const selectedCheckboxes = document.querySelectorAll(`${itemSelector}:checked`);
                    const modalSelectionList = document.getElementById('modalSelectionList');
                    const modalSelectedCount = document.getElementById('modalSelectedCount');
                    const modalTotalCountText = document.getElementById('modalTotalCountText');
                    const modalTotalAmount = document.getElementById('modalTotalAmount');
                    const modalTitle = document.querySelector('#summaryReportModal .modal-title');
                    
                    let html = '';
                    let totalAmount = 0;
                    const count = selectedCheckboxes.length;

                    selectedCheckboxes.forEach(cb => {
                        const row = cb.closest('tr');
                        const id = row.cells[1].textContent.trim();
                        const customer = row.cells[2].textContent.trim();
                        const amountText = row.cells[4].textContent.trim();
                        const amount = parseFloat(amountText.replace(/[₱,\s]/g, '')) || 0;
                        
                        totalAmount += amount;

                        html += `
                            <div class="report-item">
                                <div class="report-item-info">
                                    <span class="id">${id}</span>
                                    <span class="customer">${customer}</span>
                                    <div class="date mt-1 text-muted small"><i class="las la-calendar me-1"></i>Approved: Feb 6, 2026</div>
                                </div>
                                <div class="report-item-amount text-end">
                                    ₱ ${amount.toLocaleString(undefined, {minimumFractionDigits: 2})}
                                </div>
                            </div>
                        `;
                    });

                    if(modalTitle) modalTitle.innerHTML = `<i class="las la-file-alt me-2"></i>Create ${reportPrefix} Summary Report`;
                    modalSelectionList.innerHTML = html;
                    modalSelectedCount.textContent = count;
                    modalTotalCountText.textContent = `${count} items`;
                    modalTotalAmount.textContent = `₱ ${totalAmount.toLocaleString(undefined, {minimumFractionDigits: 2})}`;

                    // Set target section for confirmation button
                    document.getElementById('btnConfirmGenerateReport').setAttribute('data-target-section', btnId === 'fb-btnCompileReport' ? 'fb' : 'as');

                    const modal = new bootstrap.Modal(document.getElementById('summaryReportModal'));
                    modal.show();
                });
            }

            populationModal('btnCompileReport', '.check-item', 'Account Statement');
            populationModal('fb-btnCompileReport', '.fb-check-item', 'Freight');

            // Handle "Generate Report" Confirmation
            const btnConfirmGenerateReport = document.getElementById('btnConfirmGenerateReport');

            // Handle "Create JV Request" button click (delegated)
            const btnSubmitJV = document.getElementById('btnSubmitJV');

            document.addEventListener('click', function(e) {
                if (e.target.closest('.btnCreateJV')) {
                    const btn = e.target.closest('.btnCreateJV');
                    const row = btn.closest('tr');
                    const reportId = btn.getAttribute('data-report-id');
                    const customerName = btn.getAttribute('data-customer-name');
                    const customerId = btn.getAttribute('data-customer-id');
                    const amount = row.cells[3].textContent.trim();
                    const date = row.cells[1].textContent.trim();
                    const isFB = reportId.startsWith('F') || reportId.startsWith('FR');
                    
                    // Auto-populate JV Modal
                    document.getElementById('jvNumber').value = 'JV-' + (11000 + Math.floor(Math.random() * 999));
                    document.getElementById('jvDescription').value = `${isFB ? 'Freight' : 'Summary'} Report ${reportId}`;
                    document.getElementById('jvCustomerName').value = customerName;
                    document.getElementById('jvAmount').value = amount;
                    document.getElementById('jvReason').value = `To record ${isFB ? 'freight revenue' : 'accounts receivable'} for period ending ${date}. Total: ${amount}`;
                    
                    if (btnSubmitJV) {
                        btnSubmitJV.setAttribute('data-target-report', reportId);
                        btnSubmitJV.setAttribute('data-customer-name', customerName);
                        btnSubmitJV.setAttribute('data-customer-id', customerId);
                        btnSubmitJV.setAttribute('data-amount', amount.replace(/[₱,\s]/g, ''));
                    }
                    
                    const jvModal = new bootstrap.Modal(document.getElementById('jvRequestModal'));
                    jvModal.show();
                }
            });

            if (btnSubmitJV) {
                btnSubmitJV.addEventListener('click', function() {
                    const reportId = this.getAttribute('data-target-report');
                    const customerName = this.getAttribute('data-customer-name') || 'Unknown Customer';
                    const customerId = this.getAttribute('data-customer-id');
                    const amount = parseFloat(this.getAttribute('data-amount')) || 0;
                    
                    const reason = document.getElementById('jvReason').value;
                    const remarks = document.getElementById('jvRemarks').value;
                    const isFB = reportId.startsWith('F') || reportId.startsWith('FR');

                    const payload = {
                        reason: reason,
                        remarks: remarks,
                        category: isFB ? 'Freight Bill' : 'Account Statement',
                        items: [
                            {
                                type: isFB ? 'FB' : 'SOA',
                                reference_no: reportId,
                                customer_name: customerName,
                                customer_id: customerId,
                                amount: amount,
                                remarks: remarks || 'QB Entry'
                            }
                        ]
                    };

                    fetch('{{ route("admin-finance.credit-collection.jv-requests.store") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(response => {
                        if (response.redirected) {
                            window.location.href = response.url;
                            return;
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data && data.success) {
                            window.location.href = '{{ route("admin-finance.credit-collection.billing", ["tab" => "jv"]) }}';
                        } else if (data && data.error) {
                            alert('Error: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.location.href = '{{ route("admin-finance.credit-collection.billing", ["tab" => "jv"]) }}';
                    });
                });
            }

            // --- AJAX Common Handler ---
            function updateStatus(id, status, type) {
                const url = type === 'soa' 
                    ? `{{ url('/admin-finance/credit-collection/billing') }}/${id}/update-status`
                    : `{{ url('/admin-finance/credit-collection/freight-billing') }}/${id}/update-status`;
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Simple reload to refresh all tabs/badges
                    } else {
                        alert('Error updating status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred');
                });
            }

            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-update-status')) {
                    const btn = e.target.closest('.btn-update-status');
                    const id = btn.getAttribute('data-id');
                    const status = btn.getAttribute('data-status');
                    const type = btn.getAttribute('data-type');
                    updateStatus(id, status, type);
                }
                
                if (e.target.closest('.btn-delete-fb')) {
                    if (!confirm('Are you sure you want to delete this freight bill?')) return;
                    const btn = e.target.closest('.btn-delete-fb');
                    const id = btn.getAttribute('data-id');
                    
                    fetch(`{{ url('/admin-finance/credit-collection/freight-billing') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
                }
            });

            // --- Compile Logic ---
            function compileItems(ids, type, date) {
                const url = type === 'soa' 
                    ? `{{ route('admin-finance.credit-collection.billing.compile') }}`
                    : `{{ route('admin-finance.credit-collection.freight-billing.compile') }}`;
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ids: ids, date: date })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }

            if (btnConfirmGenerateReport) {
                btnConfirmGenerateReport.addEventListener('click', function() {
                    const section = this.getAttribute('data-target-section');
                    const isFB = section === 'fb';
                    const itemSelector = isFB ? '.fb-check-item' : '.check-item';
                    
                    const checkedIds = Array.from(document.querySelectorAll(`${itemSelector}:checked`)).map(cb => cb.value);
                    if (checkedIds.length > 0) {
                        const date = document.getElementById('reportDate').value;
                        compileItems(checkedIds, isFB ? 'fb' : 'soa', date);
                    }
                });
            }

            // --- Account Statement Handlers ---

            // "View" / "Edit" buttons (NOT "Prepare" - that uses a real form page)
            document.addEventListener('click', function(e) {
                const asBtn = e.target.closest('#account-statement button, #account-statement a');
                if (!asBtn || asBtn.closest('#compiled') || asBtn.closest('.jv-row')) return; // Compiled and JV have their own logic

                const isView = asBtn.title === 'View Details' || asBtn.querySelector('.la-eye');
                const isPrepare = asBtn.title === 'Prepare Statement' || asBtn.querySelector('.la-file-invoice');
                const isEdit = asBtn.title === 'Edit Statement' || asBtn.querySelector('.la-edit');

                // Skip Prepare - let it navigate to the real form page
                if (isPrepare) return;

                if (isView || isEdit) {
                    e.preventDefault();
                    const row = asBtn.closest('tr');
                    const id = row.cells[0].textContent.trim();
                    const mode = isView ? 'view' : 'edit';
                    
                    populateStatementModal(id, row, mode);
                }
            });

            function populateStatementModal(id, row, mode) {
                const modal = $('#accountStatementModal');
                const isView = mode === 'view';
                const isPrepare = mode === 'prepare';
                
                // Set Title & Mode
                let title = isView ? 'View' : (isPrepare ? 'Prepare' : 'Edit');
                modal.find('.modal-title').html(`<i class="las la-file-invoice me-2"></i> ${title} Account Statement - ${id}`);
                
                // Toggle static vs input fields
                if (isView) {
                    modal.find('.view-mode-section').show();
                    modal.find('.edit-mode-section').hide();
                    modal.find('.modal-footer .btn-primary').hide();
                    modal.find('.modal-footer .btn-outline-primary').hide();
                } else {
                    modal.find('.view-mode-section').hide();
                    modal.find('.edit-mode-section').show();
                    modal.find('.modal-footer .btn-primary').show();
                    modal.find('.modal-footer .btn-outline-primary').show();
                    
                    // Reset particulars table
                    const tbody = modal.find('#statement_particulars_body');
                    tbody.empty();
                    if (isPrepare) {
                        addParticularRow(); // Start with one empty row
                    } else {
                        // Simulate pre-filled data for "Edit"
                        addParticularRow('Ad Placement', 'Inside Back (Full Color)', '1 insertion', 25000);
                    }
                }

                // Populate shared fields from row cells
                const customer = row.cells[1]?.textContent.trim() ?? '';
                const department = row.cells[2]?.textContent.trim() ?? '';
                const period = row.cells[3]?.textContent.trim() ?? '';

                // Populate from data attributes
                const contact = row.dataset.contact || '—';
                const address = row.dataset.address || '—';
                const total = row.dataset.total || '—';
                const periodStart = row.dataset.periodStart || '—';
                const periodEnd = row.dataset.periodEnd || '—';

                modal.find('.val-customer-name').text(customer).val(customer);
                modal.find('.val-dept').text(department).val(department);
                modal.find('.val-period').text(period).val(period);
                modal.find('.val-request-id').text(id).val(id);
                modal.find('.val-contact-person').text(contact);
                modal.find('.val-address').text(address);
                modal.find('.val-total-amount').text(total);
                modal.find('.val-period-start').text(periodStart);
                modal.find('.val-period-end').text(periodEnd);

                modal.modal('show');
            }

            // Logic to add rows to particulates table
            window.addParticularRow = function(item = '', desc = '', qty = '', price = 0) {
                const tbody = $('#statement_particulars_body');
                const rowCount = tbody.find('tr').length;
                const newRow = `
                    <tr>
                        <td><input type="text" class="form-control form-control-sm" value="${item}"></td>
                        <td><input type="text" class="form-control form-control-sm" value="${desc}"></td>
                        <td><input type="text" class="form-control form-control-sm" value="${qty}"></td>
                        <td><input type="number" class="form-control form-control-sm text-end part-price" value="${price}"></td>
                        <td class="text-end fw-bold">₱ ${(price).toLocaleString()}</td>
                        <td class="text-center">
                            ${rowCount > 0 ? '<button class="btn btn-danger btn-xs light btn-remove-row"><i class="las la-times"></i></button>' : ''}
                        </td>
                    </tr>
                `;
                tbody.append(newRow);
                updateStatementTotal();
            };

            $(document).on('click', '.btn-remove-row', function() {
                $(this).closest('tr').remove();
                updateStatementTotal();
            });

            $(document).on('input', '.part-price', function() {
                const row = $(this).closest('tr');
                const price = parseFloat($(this).val()) || 0;
                row.find('td:eq(4)').text(`₱ ${price.toLocaleString()}`);
                updateStatementTotal();
            });

            function updateStatementTotal() {
                let total = 0;
                $('.part-price').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#modal_statement_total').text(`₱ ${total.toLocaleString()}`);
            }

            // Modal Submit Statement
            $('#btn_stmt_submit_approval').click(function() {
                $('#accountStatementModal').modal('hide');
                showSuccess('Account Statement has been submitted for approval.');
            });

            // Modal Save Draft
            $('#btn_stmt_save_draft').click(function() {
                $('#accountStatementModal').modal('hide');
                showSuccess('Statement draft saved successfully.');
            });

            // --- Freight Billing Specific Handlers ---
            
            // Draft Actions: Edit, Delete, Submit
            document.addEventListener('click', function(e) {
                const draftBtn = e.target.closest('#fb-drafts button');
                if (!draftBtn) return;

                const row = draftBtn.closest('tr');
                const billNo = row.cells[0].textContent.trim();

                if (draftBtn.innerHTML.includes('Edit')) {
                    populateFreightModal(billNo, row, 'edit');
                } else if (draftBtn.innerHTML.includes('Delete')) {
                    if (confirm(`Are you sure you want to delete draft ${billNo}?`)) {
                        row.remove();
                        showSuccess(`Draft ${billNo} has been deleted.`);
                        // Update badge
                        const draftBadge = document.querySelector('a[href="#fb-drafts"] .badge');
                        if (draftBadge) {
                            const count = parseInt(draftBadge.textContent) || 0;
                            draftBadge.textContent = Math.max(0, count - 1);
                        }
                    }
                } else if (draftBtn.innerHTML.includes('Submit for Approval')) {
                    // Move from Draft to Pending
                    row.remove();
                    
                    // Add to Pending table
                    const pendingTable = document.querySelector('#fb-pending tbody');
                    const customer = row.cells[1].textContent.trim();
                    const amount = row.cells[2].textContent.trim();
                    const date = row.cells[3].textContent.trim().split('\n')[0];
                    
                    const newPendingRow = `
                        <tr>
                            <td class="fw-bold">${billNo}</td>
                            <td>${customer}</td>
                            <td class="fw-bold">${amount}</td>
                            <td>${date}</td>
                            <td class="text-center">
                                <button class="btn btn-info px-3 shadow btn-view-fb"><i class="las la-eye me-1"></i> View Details</button>
                            </td>
                        </tr>
                    `;
                    
                    if (pendingTable.querySelector('.no-result-msg')) {
                        pendingTable.innerHTML = newPendingRow;
                    } else {
                        pendingTable.innerHTML = newPendingRow + pendingTable.innerHTML;
                    }

                    // Update Badges
                    const draftBadge = document.querySelector('a[href="#fb-drafts"] .badge');
                    const pendingBadge = document.querySelector('a[href="#fb-pending"] .badge');
                    if (draftBadge) draftBadge.textContent = Math.max(0, (parseInt(draftBadge.textContent) || 0) - 1);
                    if (pendingBadge) pendingBadge.textContent = (parseInt(pendingBadge.textContent) || 0) + 1;

                    showSuccess(`Bill ${billNo} submitted for approval.`);
                }
            });

            // View Details / Edit handler for Pending/Approved/All Freight Billing
            document.addEventListener('click', function(e) {
                const actionBtn = e.target.closest('.btn-info, .btn-primary:not(#btnNewBill), .btn-view-fb');
                // Exclude Drafts because Drafts handles its own actions above
                if (!actionBtn || !actionBtn.closest('#freight-billing') || actionBtn.closest('#fb-drafts')) return;

                const row = actionBtn.closest('tr');
                const isApprovedTable = actionBtn.closest('.table').querySelectorAll('th').length === 7;
                const id = row.cells[isApprovedTable ? 1 : 0].textContent.trim();
                
                const isEdit = actionBtn.classList.contains('btn-primary') || (actionBtn.title && actionBtn.title.includes('Edit'));
                
                populateFreightModal(id, row, isEdit ? 'edit' : 'view');
            });

            function populateFreightModal(billNo, row, mode) {
                const modal = $('#freightBillingModal');
                const isView = mode === 'view';
                
                // Set Title & Mode
                modal.find('.modal-title').html(`<i class="las la-file-invoice me-2"></i> ${isView ? 'View' : 'Edit'} Freight Bill - ${billNo}`);
                modal.find('input, textarea, select').prop('readonly', isView).prop('disabled', isView);
                
                // Populate basic info from row
                modal.find('#modal_bill_no').val(billNo);
                
                // Get data from row safely handling DOM/jQuery
                const $row = $(row);
                const cellsLen = row.cells ? row.cells.length : $row.find('td').length;
                let customerIdx = 1;
                let amountIdx = 2;
                
                if (cellsLen === 7) { // Approved table
                    customerIdx = 2;
                    amountIdx = 4;
                } else if (cellsLen === 6) { // All table
                    customerIdx = 1;
                    amountIdx = 3;
                }
                
                const customer = $row.find(`td:eq(${customerIdx})`).text().trim();
                const amount = $row.find(`td:eq(${amountIdx})`).text().trim().replace(/[₱,\s]/g, '');
                
                // Find matching option by text and select it
                const option = modal.find('#modal_customer_name option').filter(function() {
                    return $(this).text().trim() === customer;
                });
                if (option.length) {
                    modal.find('#modal_customer_name').val(option.val());
                } else {
                    modal.find('#modal_customer_name').val('');
                }
                if (typeof $.fn.selectpicker === 'function') {
                    modal.find('#modal_customer_name').selectpicker('refresh');
                }
                
                modal.find('#modal_amount').val(amount);
                
                // Hardcoded simulations for details not in the table
                modal.find('#modal_bill_date').val('2025-11-18');
                modal.find('#modal_address').val('123 Sample St, Business District, Metro Manila');
                modal.find('#modal_delivery_ref').val('JRMT-8821');
                modal.find('#modal_courier').val('jrmt');
                modal.find('#modal_destination').val('Kidapawan City');
                modal.find('#modal_description').val('Standard Delivery Charge');
                modal.find('#modal_tracking').val('TRK-9901-22');
                modal.find('#modal_terms').val('15');
                modal.find('#modal_due_date').val('Dec 03, 2025');
                modal.find('#modal_notes').val('Internal reference only.');

                // Show/Hide Footer buttons
                if (isView) {
                    modal.find('.modal-footer .btn-primary').hide();
                    modal.find('.modal-footer .btn-outline-primary').hide();
                } else {
                    modal.find('.modal-footer .btn-primary').show();
                    modal.find('.modal-footer .btn-outline-primary').show();
                }

                modal.modal('show');
            }

            // Modal Submit for Approval
            $('#btn_modal_submit_approval').click(function() {
                const billNo = $('#modal_bill_no').val();
                $('#freightBillingModal').modal('hide');
                
                // Trigger the existing submit logic by finding the button in the table row
                $('#fb-drafts tbody tr').each(function() {
                    const row = $(this);
                    if (row.find('td:eq(0)').text().includes(billNo)) {
                        row.find('button:contains("Submit")').click();
                        return false;
                    }
                });
            });

            // Modal Save Changes (Just simulate)
            $('#btn_modal_save_draft').click(function() {
                $('#freightBillingModal').modal('hide');
                showSuccess('Changes saved successfully as draft.');
            });

            // Handle "New Bill" button click — open freightBillingModal in create mode
            $('#btnNewBill').click(function() {
                const modal = $('#freightBillingModal');
                modal.find('.modal-title').html('<i class="las la-file-invoice me-2"></i>New Freight Bill');
                // Clear all inputs
                modal.find('input, textarea').val('');
                modal.find('#modal_customer_name').val('');
                if (typeof $.fn.selectpicker === 'function') {
                    modal.find('#modal_customer_name').selectpicker('refresh');
                }
                modal.find('select').not('#modal_customer_name').prop('selectedIndex', 0);
                // Make all fields editable
                modal.find('input, textarea, select').prop('readonly', false).prop('disabled', false);
                // Auto-generate bill number and set today's date
                modal.find('#modal_bill_no').val('F' + new Date().getFullYear() + '-' + Math.floor(100 + Math.random() * 900)).prop('readonly', true);
                modal.find('#modal_bill_date').val(new Date().toISOString().split('T')[0]);
                // Show save/submit buttons
                modal.find('.modal-footer .btn-primary').show();
                modal.find('.modal-footer .btn-outline-primary').show();
                modal.modal('show');
            });

            // Auto-populate address when customer is selected
            $(document).on('change', '#modal_customer_name', function() {
                const selectedOption = $(this).find('option:selected');
                const address = selectedOption.data('address') || '';
                $('#modal_address').val(address);
            });

            // Initialize selectpicker on modal show with custom styling for modal
            $('#freightBillingModal').on('shown.bs.modal', function () {
                if (typeof $.fn.selectpicker === 'function') {
                    $('#modal_customer_name').selectpicker('destroy');
                    $('#modal_customer_name').selectpicker({
                        size: 8,
                        liveSearch: true,
                        liveSearchPlaceholder: 'Search customer...'
                    });
                }
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

    <!-- Freight Billing View/Edit Modal -->
    <div class="modal fade" id="freightBillingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-w600"><i class="las la-file-invoice me-2"></i>Freight Bill Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted text-uppercase mb-1">Bill Number</label>
                            <input type="text" id="modal_bill_no" class="form-control bg-light" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted text-uppercase mb-1">Bill Date</label>
                            <input type="date" id="modal_bill_date" class="form-control">
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label small text-muted text-uppercase mb-1">Customer Name</label>
                            <select id="modal_customer_name" class="form-control selectpicker" data-live-search="true" data-size="8">
                                <option value="">Select Customer ▼</option>
                                @foreach(\App\Models\Customer::orderBy('customer_name')->get() as $cust)
                                    <option value="{{ $cust->customer_id }}" data-address="{{ $cust->shipping_address ?? $cust->billing_address ?? '' }}">{{ $cust->customer_name ?? $cust->company_name ?? 'Unknown' }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label small text-muted text-uppercase mb-1">Address</label>
                            <textarea id="modal_address" class="form-control" rows="2" placeholder="Full address details..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small text-muted text-uppercase mb-1">Delivery Reference</label>
                            <input type="text" id="modal_delivery_ref" class="form-control" placeholder="e.g. JRMT-2469">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted text-uppercase mb-1">Courier Service</label>
                            <select id="modal_courier" class="form-select">
                                <option value="jrmt">JRMT Resources</option>
                                <option value="lbc">LBC</option>
                                <option value="jt">J&T Express</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small text-muted text-uppercase mb-1">Destination</label>
                            <input type="text" id="modal_destination" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted text-uppercase mb-1">Description</label>
                            <input type="text" id="modal_description" class="form-control">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small text-muted text-uppercase mb-1">Tracking Number</label>
                            <input type="text" id="modal_tracking" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small text-muted text-uppercase mb-1">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" id="modal_amount" class="form-control fw-bold">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted text-uppercase mb-1">Terms</label>
                            <select id="modal_terms" class="form-select">
                                <option value="7">7 Days</option>
                                <option value="15">15 Days</option>
                                <option value="30">30 Days</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted text-uppercase mb-1">Due Date</label>
                            <input type="text" id="modal_due_date" class="form-control bg-light" readonly>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small text-muted text-uppercase mb-1">Internal Notes</label>
                            <textarea id="modal_notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btn_modal_save_draft">Save Changes</button>
                    <button type="button" class="btn btn-primary btn-sm" id="btn_modal_submit_approval">Submit for Approval <i class="las la-paper-plane ms-1"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Statement View/Edit Modal -->
    <div class="modal fade" id="accountStatementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-w600"><i class="las la-file-invoice me-2"></i>Account Statement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Section 1: Header/Contract Info -->
                    <div class="row mb-4 border-bottom pb-3">
                        <div class="col-md-3">
                            <label class="small text-muted text-uppercase mb-1">Request ID</label>
                            <div class="fw-bold val-request-id text-primary"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted text-uppercase mb-1">Customer</label>
                            <div class="fw-bold val-customer-name"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted text-uppercase mb-1">Dept</label>
                            <div class="val-dept"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted text-uppercase mb-1">Period</label>
                            <div class="val-period"></div>
                        </div>
                    </div>

                    <!-- View Mode Section -->
                    <div class="view-mode-section">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="fw-bold mb-2">Statement Details</label>
                                <ul class="list-group list-group-flush small">
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Contact Person:</span>
                                        <span class="fw-bold val-contact-person">—</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Billing Address:</span>
                                        <span class="text-end val-address" style="width: 60%;">—</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Total Amount:</span>
                                        <span class="fw-bold val-total-amount">—</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold mb-2">Billing Period</label>
                                <ul class="list-group list-group-flush small">
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">From:</span>
                                        <span class="val-period-start">—</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">To:</span>
                                        <span class="val-period-end">—</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Edit/Prepare Mode Section -->
                    <div class="edit-mode-section">
                        <div class="row g-3 mb-4 bg-light p-3 rounded">
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Statement Number</label>
                                <input type="text" class="form-control form-control-sm" value="AS-2026-042">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Statement Date</label>
                                <input type="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Prepared By</label>
                                <input type="text" class="form-control form-control-sm" placeholder="Billing Staff">
                            </div>
                        </div>

                        <label class="fw-bold mb-2">Particulars</label>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Item/Service</th>
                                        <th>Description</th>
                                        <th>Qty/Size</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end">Amount</th>
                                        <th style="width: 40px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="statement_particulars_body">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">TOTAL AMOUNT</td>
                                        <td class="text-end fw-bold text-primary" id="modal_statement_total">₱ 0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <button type="button" class="btn btn-link btn-sm p-0" onclick="addParticularRow()"><i class="las la-plus me-1"></i> Add Item Row</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btn_stmt_save_draft">Save Draft</button>
                    <button type="button" class="btn btn-primary btn-sm" id="btn_stmt_submit_approval">Submit for Approval <i class="las la-check-circle ms-1"></i></button>
                </div>
            </div>
        </div>
    </div>
    @endpush
</x-app-layout>
