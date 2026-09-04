<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .approval-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 2rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            border-left: 5px solid;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .stat-card.pending { border-left-color: #ffc107; }
        .stat-card.urgent { border-left-color: #dc3545; }
        .stat-card.recent { border-left-color: #17a2b8; }
        .stat-card.total { border-left-color: #6c757d; }

        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            color: #333;
        }

        .stat-card p {
            margin: 0;
            color: #666;
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Unified Queue Navigation (Used for both/all tabs) */
        .queue-nav {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0;
        }

        .queue-btn {
            padding: 1rem 1.75rem;
            cursor: pointer;
            border: none;
            background: transparent;
            color: #6c757d;
            font-weight: 600;
            font-size: 0.95rem;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            position: relative;
            bottom: -2px;
        }

        .queue-btn.active {
            color: #ff0000;
            border-bottom-color: #ff0000;
            background: rgba(255, 0, 0, 0.05);
        }

        .queue-btn:hover { 
            color: #ff0000; 
            background: rgba(255, 0, 0, 0.03);
        }

        .table-responsive {
            margin-top: 1.5rem;
        }

        #approvalQueueTable {
            font-size: 0.9rem;
        }

        #approvalQueueTable thead th,
        #myApprovalsTable thead th,
        #mySubmissionsTable thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 0.8rem;
            color: #495057;
            padding: 0.75rem 0.5rem;
            border: none;
        }

        #approvalQueueTable tbody td,
        #myApprovalsTable tbody td,
        #mySubmissionsTable tbody td {
            padding: 0.75rem 0.5rem;
            vertical-align: middle;
        }

        .document-type-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            white-space: nowrap;
        }

        .type-sales-order { background-color: #cfe2ff; color: #084298; }
        .type-direct-invoice { background-color: #ceffbcff; color: #00991fff; }
        .type-expense { background-color: #fff3cd; color: #856404; }
        .type-job-order { background-color: #f8d7da; color: #721c24; }
        .type-payment-request { background-color: #e0cffc; color: #5a3791; }

        .btn-xs {
            padding: 0.35rem 0.65rem;
            font-size: 0.875rem;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            padding: 1.5rem 2rem;
            background: #fff;
            border-bottom: 1px solid #e9ecef;
        }

        .card-body {
            padding: 2rem;
        }

        /* Status badges matching admin-finance */
        .status-badge {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            display: inline-block;
            white-space: nowrap;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-success { background-color: #d1e7dd; color: #0f5132; }
        .status-danger { background-color: #f8d7da; color: #721c24; }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .approval-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .stat-card {
                padding: 1.5rem;
            }

            .stat-card h3 {
                font-size: 2rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            .queue-nav {
                flex-wrap: wrap;
            }

            .queue-btn {
                padding: 0.75rem 1.25rem;
                font-size: 0.875rem;
            }
        }

        @media (max-width: 768px) {
            .approval-stats {
                grid-template-columns: 1fr;
                gap: 1rem;
                margin-bottom: 1.5rem;
            }

            .stat-card {
                padding: 1.25rem;
            }

            .stat-card h3 {
                font-size: 1.75rem;
            }

            .card-header {
                padding: 1rem 1.5rem;
            }

            .card-body {
                padding: 1rem;
            }

            .queue-nav {
                gap: 0.25rem;
                margin-bottom: 1rem;
            }

            .queue-btn {
                padding: 0.65rem 1rem;
                font-size: 0.8rem;
                flex: 1;
                text-align: center;
            }

            .table-responsive {
                margin-top: 1rem;
            }

            #approvalQueueTable,
            #myApprovalsTable,
            #mySubmissionsTable {
                font-size: 0.8rem;
            }

            #approvalQueueTable thead th,
            #myApprovalsTable thead th,
            #mySubmissionsTable thead th {
                font-size: 0.7rem;
                padding: 0.75rem 0.5rem;
            }

            #approvalQueueTable tbody td,
            #myApprovalsTable tbody td,
            #mySubmissionsTable tbody td {
                padding: 0.75rem 0.5rem;
            }

            .document-type-badge {
                padding: 4px 10px;
                font-size: 0.7rem;
            }

            .btn-xs {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }

            .btn-sm {
                padding: 0.35rem 0.6rem;
                font-size: 0.8rem;
            }
        }
    </style>
    @endpush

    <div class="approval-stats">
        <div class="stat-card pending">
            <h3 id="pendingCount">{{ $salesOrders->count() + $pendingCashAdvances->count() + $pendingTransfers->count() + $pendingCctvRequests->count() }}</h3>
            <p>Pending Approvals</p>
        </div>
        <div class="stat-card urgent">
            <h3 id="urgentCount">0</h3>
            <p>Urgent (Overdue)</p>
        </div>
        <div class="stat-card recent">
            @php
                $recentSales = $salesOrders->where('created_at', '>=', now()->startOfDay())->count();
                $recentCash = $pendingCashAdvances->where('created_at', '>=', now()->startOfDay())->count();
                $recentTransfers = $pendingTransfers->where('created_at', '>=', now()->startOfDay())->count();
                $recentCctv = $pendingCctvRequests->where('created_at', '>=', now()->startOfDay())->count();
                $recentTotal = $recentSales + $recentCash + $recentTransfers + $recentCctv;
            @endphp
            <h3 id="recentCount">{{ $recentTotal }}</h3>
            <p>Received Today</p>
        </div>
        <div class="stat-card total">
            <h3 id="totalCount">{{ $salesOrders->count() + $pendingCashAdvances->count() + $pendingTransfers->count() + $pendingCctvRequests->count() }}</h3>
            <p>Total Pending</p>
        </div>
    </div>

    <!-- Personal Activity Card (My Approvals & My Submissions) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">My Activity</h4>
                </div>
                <div class="card-body">
                    <div class="queue-nav">
                        <button class="queue-btn tab-trigger active" onclick="switchTab(this, 'my-approvals')">For Approval</button>
                        <button class="queue-btn tab-trigger" onclick="switchTab(this, 'my-submissions')">My Submissions</button>
                        <button class="queue-btn tab-trigger" onclick="switchTab(this, 'my-approved')">Approved</button>
                    </div>

                    <!-- My Approvals Tab Content -->
                    <div id="my-approvals-content" class="tab-section">
                        <div class="table-responsive">
                            <table id="myApprovalsTable" class="display table table-bordered" style="width: 100%">
                                <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Ref #</th>
                                    <th>Customer Name</th>
                                    <th>User</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                                <tbody>
                                    @foreach($myApprovals as $approval)
                                    <tr>
                                            <td>
                                                @php
                                                     $typeClass = match($approval['type']) {
                                                         'Sales Order' => 'type-sales-order',
                                                         'Delivery Receipt' => 'bg-warning text-dark border border-warning',
                                                         'CCTV' => 'type-job-order',
                                                         'Payment Request' => 'type-payment-request',
                                                         'Auto Debit Letter', 'Auto Debit' => 'type-expense',
                                                         default => 'badge-info'
                                                     };
                                                 @endphp
                                                 <span class="document-type-badge {{ $typeClass }}" @if($approval['type'] === 'Cash Advance') style="background-color: #e3f2fd; color: #0d47a1;" @elseif($approval['type'] === 'Stock Transfer') style="background-color: #d4edda; color: #155724;" @endif>{{ $approval['type'] }}</span>
                                                 
                                         </td>
                                        <td><strong>{{ $approval['reference_no'] }}</strong></td>
                                        <td>{{ $approval['original']->customer?->customer_name ?? ($approval['original']->customer_representative ?? 'N/A') }}</td>
                                        <td>{{ $approval['submitted_by'] }}</td>
                                        @php
                                            $submittedDate = $approval['submitted_date'] ?? null;
                                            if ($submittedDate instanceof \Carbon\Carbon) {
                                                $dateDisplay = $submittedDate->format('Y-m-d h:i A');
                                            } elseif (is_string($submittedDate) && $submittedDate) {
                                                $dateDisplay = $submittedDate;
                                            } elseif (!empty($approval['original']->created_at)) {
                                                $dateDisplay = \Carbon\Carbon::parse($approval['original']->created_at)->format('Y-m-d h:i A');
                                            } else {
                                                $dateDisplay = '';
                                            }
                                        @endphp
                                        <td>{{ $dateDisplay }}</td>
                                        <td>{{ $approval['amount'] }}</td>
                                        <td>
                                            @php
                                                $status = $approval['status'];
                                                $badgeClass = 'status-pending';
                                            @endphp
                                            @if(in_array($status, ['approved', 'completed', 'Approved']))
                                                @php $badgeClass = 'status-success'; @endphp
                                            @elseif(in_array($status, ['rejected', 'cancelled', 'Rejected']))
                                                @php $badgeClass = 'status-danger'; @endphp
                                            @endif
                                            <span class="status-badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                        </td>
                                        <td>
                                            @if($approval['type'] === 'Delivery Receipt')
                                                <a href="{{ $approval['url'] }}" class="btn btn-danger btn-sm text-white me-1"><i class="las la-eye me-1"></i> Review</a>
                                                <form action="{{ route('production.logistic.approve-dr', $approval['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm"><i class="las la-check me-1"></i> Approve</button>
                                                </form>
                                            @elseif($approval['type'] === 'Payment Request')
                                                <a href="{{ route('payment-requests.show', $approval['id']) }}" class="btn btn-primary btn-sm me-1"><i class="las la-eye me-1"></i> Review</a>
                                                @if(isset($approval['original']->status) && $approval['original']->status === 'pending_director_approval' && (auth()->user()->isSuperAdmin() || str_contains(strtolower(auth()->user()->position ?? ''), 'director')))
                                                    <form action="{{ route('payment-requests.approve', $approval['id']) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="approval_type" value="director">
                                                        <button type="submit" class="btn btn-success btn-sm"><i class="las la-check me-1"></i> Approve</button>
                                                    </form>
                                                @endif
                                            @elseif($approval['type'] === 'Sales Order')
                                                <a href="{{ $approval['url'] }}" class="btn btn-danger btn-sm text-white me-1"><i class="las la-eye me-1"></i> Review</a>
                                                @if(isset($approval['status']) && $approval['status'] === 'pending_dr_approval')
                                                    <form action="{{ route('production.logistic.approve-dr', $approval['id']) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm"><i class="las la-check me-1"></i> Approve</button>
                                                    </form>
                                                @endif
                                            @elseif($approval['type'] === 'Logistics Service Order')
                                                <button type="button" class="btn btn-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#pickupRequestModal{{ $approval['id'] }}">
                                                    <i class="las la-eye"></i> Review
                                                </button>
                                                <form action="{{ route('production.logistic.pickup-requests.approve', $approval['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm"><i class="las la-check me-1"></i> Approve</button>
                                                </form>
                                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectPickupModal{{ $approval['id'] }}">
                                                    <i class="las la-times me-1"></i> Reject
                                                </button>

                                                <!-- Review Modal -->
                                                <div class="modal fade" id="pickupRequestModal{{ $approval['id'] }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content text-start">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Review Logistics Service Order</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body text-start">
                                                                <div class="mb-3 text-start">
                                                                    <strong>Type:</strong> <span class="badge {{ $approval['original']->type === 'delivery' ? 'bg-primary text-white' : 'text-white' }}" style="{{ $approval['original']->type === 'pickup' ? 'background-color: #6f42c1 !important;' : ($approval['original']->type === 'pull_out' ? 'background-color: #f57c00 !important;' : '') }}">{{ str_replace('_', ' ', ucfirst($approval['original']->type)) }}</span>
                                                                </div>
                                                                <div class="mb-3 text-start">
                                                                    <strong>Client / Receiver Name:</strong>
                                                                    <div>{{ $approval['original']->client_name }}</div>
                                                                </div>
                                                                <div class="mb-3 text-start">
                                                                    <strong>Address / Location:</strong>
                                                                    <div>{{ $approval['original']->address }}</div>
                                                                </div>
                                                                <div class="mb-3 text-start">
                                                                    <strong>Requested Date:</strong>
                                                                    <div>{{ $approval['original']->requested_date->format('M d, Y') }}</div>
                                                                </div>
                                                                @if($approval['original']->driver_name || $approval['original']->vehicle)
                                                                <div class="mb-3 text-start">
                                                                    <strong>Assigned Driver & Vehicle:</strong>
                                                                    <div>
                                                                        @if($approval['original']->driver_name)
                                                                            <span class="fw-semibold text-dark"><i class="las la-user-tag me-1 text-primary"></i>{{ $approval['original']->driver_name }}</span>
                                                                        @endif
                                                                        @if($approval['original']->vehicle)
                                                                            <span class="ms-2 text-muted"><i class="las la-truck me-1 text-success"></i>{{ $approval['original']->vehicle }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                @endif
                                                                <div class="mb-3 text-start">
                                                                    <strong>Items Details:</strong>
                                                                    <div class="bg-light p-2 rounded" style="white-space: pre-wrap;">{{ $approval['original']->items_details }}</div>
                                                                </div>
                                                                @if($approval['original']->remarks)
                                                                <div class="mb-3 text-start">
                                                                    <strong>Remarks:</strong>
                                                                    <div>{{ $approval['original']->remarks }}</div>
                                                                </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <form action="{{ route('production.logistic.pickup-requests.approve', $approval['id']) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success"><i class="las la-check me-1"></i> Approve</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Reject Modal -->
                                                <div class="modal fade" id="rejectPickupModal{{ $approval['id'] }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content text-start">
                                                            <form action="{{ route('production.logistic.pickup-requests.reject', $approval['id']) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Reject Request</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body text-start">
                                                                    <div class="mb-3 text-start">
                                                                        <label class="form-label fw-bold">Reason for Rejection</label>
                                                                        <textarea class="form-control" name="rejection_reason" rows="3" required placeholder="Specify why this request is being rejected..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-danger">Submit Reject</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                             @elseif($approval['type'] === 'Team Stock Transfer')
                                                 <button type="button" class="btn btn-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#prodTeamStockTransferModal{{ $approval['id'] }}">
                                                     <i class="las la-eye"></i> Review
                                                 </button>
                                                 <form action="{{ route('production.team-stock-transfer.approve', $approval['id']) }}" method="POST" class="d-inline">
                                                     @csrf
                                                     <button type="submit" class="btn btn-success btn-sm"><i class="las la-check me-1"></i> Approve</button>
                                                 </form>
                                            @elseif($approval['type'] === 'CCTV')
                                                <form action="{{ route('user.cctv-requests.update', $approval['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="Pending HR approval">
                                                    <button type="submit" class="btn btn-success btn-sm"><i class="las la-check"></i> Approve</button>
                                                </form>
                                                <form action="{{ route('user.cctv-requests.update', $approval['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-danger btn-sm"><i class="las la-times"></i> Reject</button>
                                                </form>
                                            @elseif($approval['type'] === 'Stock Transfer')
                                                <div class="d-flex gap-1 align-items-center flex-wrap">
                                                    <button type="button" 
                                                            class="btn btn-primary btn-sm view-transfer-btn"
                                                            data-id="{{ $approval['id'] }}"
                                                            data-ref="ST-{{ str_pad($approval['id'], 5, '0', STR_PAD_LEFT) }}"
                                                            data-from-site="{{ $approval['original']->fromSite->name ?? 'N/A' }}"
                                                            data-to-site="{{ $approval['original']->toSite->name ?? 'N/A' }}"
                                                            data-submitted-by="{{ $approval['submitted_by'] ?? 'N/A' }}"
                                                            data-date="{{ $approval['original']->created_at ? $approval['original']->created_at->format('M. d, Y h:i A') : '' }}"
                                                            data-notes="{{ $approval['original']->notes ?? '' }}"
                                                            data-remarks="{{ $approval['original']->remarks ?? '' }}"
                                                            data-status="{{ $approval['original']->status }}"
                                                            data-can-approve="{{ $approval['original']->canBeApprovedBy(auth()->user()) ? 'true' : 'false' }}"
                                                            data-items="{{ json_encode($approval['original']->batch_items ?? [['name' => $approval['original']->item_name, 'type' => $approval['original']->item_type, 'quantity' => $approval['original']->quantity]]) }}">
                                                        <i class="las la-eye"></i> View
                                                    </button>

                                                    @if($approval['original']->status === 'pending' && $approval['original']->canBeApprovedBy(auth()->user()))
                                                        <button type="button" class="btn btn-success btn-sm" onclick="approveTransfer({{ $approval['id'] }})">
                                                            <i class="las la-check"></i> Approve
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="rejectTransfer({{ $approval['id'] }})">
                                                            <i class="las la-times"></i> Reject
                                                        </button>
                                                    @elseif($approval['original']->status === 'logistics_assignment' && ($isLogisticsAssigner ?? false))
                                                        <div class="d-flex gap-1" style="max-width: 250px;">
                                                            <select class="form-control form-control-sm" id="assignLogistics{{ $approval['id'] }}">
                                                                <option value="">Select staff</option>
                                                                @foreach($logisticsUsers ?? [] as $logisticsUser)
                                                                    <option value="{{ $logisticsUser->id }}" {{ $approval['original']->logistics_assigned_to == $logisticsUser->id ? 'selected' : '' }}>
                                                                        {{ $logisticsUser->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <button class="btn btn-primary btn-sm" onclick="assignLogisticsTransfer({{ $approval['id'] }})">
                                                                Assign
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                             @elseif($approval['type'] === 'Auto Debit Letter' || $approval['type'] === 'Auto Debit')
                                                 <a href="{{ route('production.ford.auto-debit.show', $approval['id']) }}" class="btn btn-danger btn-sm text-white me-1"><i class="las la-eye me-1"></i> Review</a>
                                                 @if(isset($approval['original']->status) && ($approval['original']->status === 'pending_director' || $approval['original']->status === 'Pending Director Approval'))
                                                     <form action="{{ route('production.ford.auto-debit.approve-director', $approval['id']) }}" method="POST" class="d-inline">
                                                         @csrf
                                                         <button type="submit" class="btn btn-success btn-sm"><i class="las la-check me-1"></i> Approve</button>
                                                     </form>
                                                 @elseif(isset($approval['original']->status) && ($approval['original']->status === 'pending_finance' || $approval['original']->status === 'Pending Finance Approval'))
                                                     <form action="{{ route('production.ford.auto-debit.approve-finance', $approval['id']) }}" method="POST" class="d-inline">
                                                         @csrf
                                                         <button type="submit" class="btn btn-success btn-sm"><i class="las la-check me-1"></i> Approve</button>
                                                     </form>
                                                 @endif
                                            @else
                                                <button type="button" 
                                                        class="btn btn-primary btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#cashAdvanceModal"
                                                        data-type="{{ $approval['type'] }}"
                                                        data-module="{{ isset($approval['original']->module) ? $approval['original']->module : '' }}"
                                                        data-id="{{ $approval['id'] }}"
                                                        data-name="{{ $approval['submitted_by'] }}"
                                                        data-amount="{{ $approval['amount'] }}"
                                                        data-purpose="{{ $approval['original']->purpose }}"
                                                        data-date="{{ isset($approval['original']->date_needed) && $approval['original']->date_needed ? \Carbon\Carbon::parse($approval['original']->date_needed)->format('M d, Y') : '' }}"
                                                        data-original="{{ json_encode($approval['original']) }}">
                                                    <i class="las la-eye"></i> Review
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- My Submissions Tab Content -->
                    <div id="my-submissions-content" class="tab-section" style="display: none;">
                        <div class="table-responsive">
                            <table id="mySubmissionsTable" class="display table table-bordered" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Ref #</th>
                                        <th>Customer Name</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mySubmissions as $submission)
                                    <tr>
                                            <td><span class="document-type-badge {{ $submission->type === 'Sales Order' ? 'type-sales-order' : 'badge-info' }}" @if($submission->type === 'Cash Advance') style="background-color: #e3f2fd; color: #0d47a1;" @endif>{{ $submission->type }}</span>
                                                
                                            </td>
                                        <td><strong>{{ $submission->reference_no }}</strong></td>
                                        <td>{{ $submission->original->customer?->customer_name ?? ($submission->original->customer_representative ?? 'N/A') }}</td>
                                        <td>{{ (isset($submission->submitted_date) && $submission->submitted_date instanceof \Carbon\Carbon) ? $submission->submitted_date->format('Y-m-d h:i A') : (is_string($submission->submitted_date) ? $submission->submitted_date : '') }}</td>
                                        <td>{{ $submission->amount }}</td>
                                        <td>
                                            @php
                                                $status = $submission->status;
                                                $badgeClass = 'status-pending';
                                                if (in_array($status, ['approved', 'completed', 'picking', 'delivered'])) $badgeClass = 'status-success';
                                                elseif (in_array($status, ['rejected', 'cancelled'])) $badgeClass = 'status-danger';
                                                elseif (in_array($status, ['pending_admin_approval', 'pending_director_approval'])) $badgeClass = 'status-info';
                                            @endphp
                                            <span class="status-badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                        </td>
                                        <td>
                                            @if($submission->type === 'Sales Order')
                                                <a href="{{ $submission->url }}" class="btn btn-primary btn-sm"><i class="las la-eye"></i> View</a>
                                            @elseif($submission->type === 'Logistics Service Order')
                                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewSubmissionPickupModal{{ $submission->id }}">
                                                    <i class="las la-eye"></i> View
                                                </button>

                                                <!-- Submission Detail Modal -->
                                                <div class="modal fade" id="viewSubmissionPickupModal{{ $submission->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content text-start">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Logistics Service Order Details</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body text-start">
                                                                <div class="mb-3 text-start">
                                                                    <strong>Type:</strong> <span class="badge {{ $submission->original->type === 'delivery' ? 'bg-primary text-white' : 'text-white' }}" style="{{ $submission->original->type === 'pickup' ? 'background-color: #6f42c1 !important;' : ($submission->original->type === 'pull_out' ? 'background-color: #f57c00 !important;' : '') }}">{{ str_replace('_', ' ', ucfirst($submission->original->type)) }}</span>
                                                                </div>
                                                                <div class="mb-3 text-start">
                                                                    <strong>Client / Receiver Name:</strong>
                                                                    <div>{{ $submission->original->client_name }}</div>
                                                                </div>
                                                                <div class="mb-3 text-start">
                                                                    <strong>Address / Location:</strong>
                                                                    <div>{{ $submission->original->address }}</div>
                                                                </div>
                                                                <div class="mb-3 text-start">
                                                                    <strong>Requested Date:</strong>
                                                                    <div>{{ $submission->original->requested_date->format('M d, Y') }}</div>
                                                                </div>
                                                                <div class="mb-3 text-start">
                                                                    <strong>Items Details:</strong>
                                                                    <div class="bg-light p-2 rounded" style="white-space: pre-wrap;">{{ $submission->original->items_details }}</div>
                                                                </div>
                                                                @if($submission->original->remarks)
                                                                <div class="mb-3 text-start">
                                                                    <strong>Remarks:</strong>
                                                                    <div>{{ $submission->original->remarks }}</div>
                                                                </div>
                                                                @endif
                                                                @if($submission->original->status === 'rejected')
                                                                <div class="alert alert-danger mb-0 mt-3 text-start">
                                                                    <strong>Rejection Reason:</strong> {{ $submission->original->rejection_reason ?? 'N/A' }}
                                                                </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    data-bs-toggle="modal" data-bs-target="#cashAdvanceModal"
                                                    data-id="{{ $submission->id }}"
                                                    data-name="{{ $submission->prep_name }}"
                                                    data-amount="{{ $submission->amount }}"
                                                    data-purpose="{{ $submission->original->purpose ?? '' }}"
                                                    data-date="{{ isset($submission->original->date_needed) && $submission->original->date_needed ? \Carbon\Carbon::parse($submission->original->date_needed)->format('M d, Y') : '' }}"
                                                    data-status="{{ $submission->status }}"
                                                    data-original="{{ json_encode($submission->original) }}"
                                                    data-view-only="true">
                                                    <i class="las la-eye"></i> View
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- My Approved Tab Content -->
                    <div id="my-approved-content" class="tab-section" style="display: none;">
                        <div class="table-responsive">
                            <table id="myApprovedTable" class="display table table-bordered" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Ref #</th>
                                        <th>Customer Name</th>
                                        <th>Submitted By</th>
                                        <th>Date</th>
                                        <th>Amount / Details</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($myApprovedRequests as $approved)
                                    <tr>
                                            <td><span class="document-type-badge badge-info" style="background-color: #e3f2fd; color: #0d47a1;">{{ $approved->type }}</span>
                                            
                                            </td>
                                        <td><strong>{{ $approved->reference_no }}</strong></td>
                                        <td>{{ $approved->original->customer?->customer_name ?? ($approved->original->customer_representative ?? 'N/A') }}</td>
                                        <td>{{ $approved->submitted_by }}</td>
                                        <td>{{ (isset($approved->submitted_date) && $approved->submitted_date instanceof \Carbon\Carbon) ? $approved->submitted_date->format('Y-m-d h:i A') : (is_string($approved->submitted_date) ? $approved->submitted_date : '') }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark">{{ $approved->amount }}</span>
                                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($approved->original->purpose ?? '', 30) }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $status = $approved->status;
                                                $badgeClass = 'status-pending';
                                                if (in_array($status, ['approved', 'completed', 'picking', 'delivered'])) $badgeClass = 'status-success';
                                                elseif (in_array($status, ['rejected', 'cancelled'])) $badgeClass = 'status-danger';
                                                elseif (in_array($status, ['pending_admin_approval', 'pending_director_approval'])) $badgeClass = 'status-info';
                                            @endphp
                                            <span class="status-badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                        </td>
                                        <td>
                                            @if($approved->type === 'Logistics Service Order')
                                                <button type="button" class="btn btn-primary btn-xs" data-bs-toggle="modal" data-bs-target="#viewApprovedPickupModal{{ $approved->id }}">
                                                    <i class="las la-eye"></i> View
                                                </button>

                                                <!-- Approved Detail Modal -->
                                                <div class="modal fade" id="viewApprovedPickupModal{{ $approved->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content text-start">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Logistics Service Order Details</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body text-start">
                                                                <div class="mb-3 text-start">
                                                                    <strong>Type:</strong> <span class="badge {{ $approved->original->type === 'delivery' ? 'bg-primary text-white' : 'text-white' }}" style="{{ $approved->original->type === 'pickup' ? 'background-color: #6f42c1 !important;' : ($approved->original->type === 'pull_out' ? 'background-color: #f57c00 !important;' : '') }}">{{ str_replace('_', ' ', ucfirst($approved->original->type)) }}</span>
                                                                </div>
                                                                <div class="mb-3 text-start">
                                                                    <strong>Client / Receiver Name:</strong>
                                                                    <div>{{ $approved->original->client_name }}</div>
                                                                </div>
                                                                <div class="mb-3 text-start">
                                                                    <strong>Address / Location:</strong>
                                                                    <div>{{ $approved->original->address }}</div>
                                                                </div>
                                                                <div class="mb-3 text-start">
                                                                    <strong>Requested Date:</strong>
                                                                    <div>{{ $approved->original->requested_date->format('M d, Y') }}</div>
                                                                </div>
                                                                <div class="mb-3 text-start">
                                                                    <strong>Items Details:</strong>
                                                                    <div class="bg-light p-2 rounded" style="white-space: pre-wrap;">{{ $approved->original->items_details }}</div>
                                                                </div>
                                                                @if($approved->original->remarks)
                                                                <div class="mb-3 text-start">
                                                                    <strong>Remarks:</strong>
                                                                    <div>{{ $approved->original->remarks }}</div>
                                                                </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <button type="button" 
                                                        class="btn btn-primary btn-xs"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#cashAdvanceModal"
                                                    data-id="{{ $approved->id }}"
                                                    data-name="{{ $approved->submitted_by }}"
                                                    data-amount="{{ $approved->amount }}"
                                                    data-purpose="{{ $approved->original->purpose }}"
                                                    data-date="{{ isset($approved->original->date_needed) && $approved->original->date_needed ? \Carbon\Carbon::parse($approved->original->date_needed)->format('M d, Y') : '' }}"
                                                    data-status="{{ $approved->status }}">
                                                <i class="las la-eye"></i> View
                                            </button>
                                            @endif
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

    <!-- Department Queue Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">Departmental Queue</h4>
                </div>
                <div class="card-body">
                    <div class="queue-nav">
                        <button class="queue-btn filter-trigger active" onclick="filterQueue(this, '')">All Records</button>
                        <button class="queue-btn filter-trigger" onclick="filterQueue(this, 'Sales Order')">Sales Orders</button>
                        <button class="queue-btn filter-trigger" onclick="filterQueue(this, 'Cash Advance')">Cash Advances</button>
                        <button class="queue-btn filter-trigger" onclick="filterQueue(this, 'Stock Transfer')">Stock Transfers</button>
                    </div>

                    <div class="table-responsive">
                        <table id="approvalQueueTable" class="display table table-bordered" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Ref #</th>
                                    <th>Customer Name</th>
                                    <th>User</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesOrders as $order)
                                <tr data-type="Sales Order">
                                    <td><span class="document-type-badge type-sales-order">Sales Order</span></td>
                                    <td><strong>{{ $order->so_number }}</strong></td>
                                    <td>{{ $order->customer?->customer_name ?? ($order->customer_representative ?: 'N/A') }}</td>
                                    <td>{{ $order->preparedBy->name ?? 'N/A' }}</td>
                                    <td>{{ $order->created_at->format('Y-m-d h:i A') }}</td>
                                    <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                    <td><span class="status-badge status-pending">Pending Approval</span></td>
                                    <td>
                                        <a href="{{ route('production.sales-order.detail', $order->id) }}" class="btn btn-primary btn-sm"><i class="las la-eye"></i> Review</a>
                                    </td>
                                </tr>
                                @endforeach

                                @foreach($pendingCashAdvances as $advance)
                                <tr data-type="Cash Advance">
                                    <td><span class="document-type-badge badge-info" style="background-color: #e3f2fd; color: #0d47a1;">Cash Advance</span></td>
                                    <td><strong>CA-{{ str_pad($advance->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                    <td><span class="text-muted">N/A</span></td>
                                    <td>{{ $advance->user->name ?? $advance->employee_name }}</td>
                                    <td>{{ $advance->created_at->format('Y-m-d h:i A') }}</td>
                                    <td>₱{{ number_format($advance->amount, 2) }}</td>
                                    <td><span class="status-badge status-pending">Pending Manager</span></td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-primary btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#cashAdvanceModal"
                                                data-id="{{ $advance->id }}"
                                                data-name="{{ $advance->user->name ?? $advance->employee_name }}"
                                                data-amount="₱{{ number_format($advance->amount, 2) }}"
                                                data-purpose="{{ $advance->purpose }}"
                                                data-date="{{ $advance->date_needed->format('M d, Y') }}"
                                                data-original="{{ json_encode($advance) }}">
                                            <i class="las la-eye"></i> Review
                                        </button>
                                    </td>
                                </tr>
                                @endforeach

                                @foreach($pendingTransfers as $transfer)
                                <tr data-type="Stock Transfer">
                                    <td><span class="document-type-badge" style="background-color: #d4edda; color: #155724;">Stock Transfer</span></td>
                                    <td><strong>ST-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                    <td><span class="text-muted">N/A</span></td>
                                    <td>{{ $transfer->createdBy->name ?? 'N/A' }}</td>
                                    <td>{{ $transfer->created_at->format('Y-m-d h:i A') }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span>{{ $transfer->quantity }} units</span>
                                            @if($transfer->logisticsAssignedTo && $transfer->logistics_assigned_to != $transfer->created_by)
                                                <small class="text-muted">Logistics: {{ $transfer->logisticsAssignedTo->name }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($transfer->status === 'pending')
                                            <span class="status-badge status-pending">Pending Approval</span>
                                        @elseif($transfer->status === 'accounting_review')
                                            <span class="status-badge status-info">Accounting Review</span>
                                        @elseif($transfer->status === 'logistics_assignment')
                                            <span class="status-badge status-info">For Logistics Assignment</span>
                                        @elseif($transfer->status === 'logistics_assigned')
                                            <span class="status-badge status-success">Assigned to Logistics</span>
                                        @elseif($transfer->status === 'completed')
                                            <span class="status-badge status-success">Completed</span>
                                        @else
                                            <span class="status-badge status-danger">{{ ucfirst($transfer->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 align-items-center flex-wrap">
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm view-transfer-btn"
                                                    data-id="{{ $transfer->id }}"
                                                    data-ref="ST-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}"
                                                    data-from-site="{{ $transfer->fromSite->name ?? 'N/A' }}"
                                                    data-to-site="{{ $transfer->toSite->name ?? 'N/A' }}"
                                                    data-submitted-by="{{ $transfer->createdBy->name ?? 'N/A' }}"
                                                    data-date="{{ $transfer->created_at->format('M. d, Y h:i A') }}"
                                                    data-notes="{{ $transfer->notes ?? '' }}"
                                                    data-remarks="{{ $transfer->remarks ?? '' }}"
                                                    data-status="{{ $transfer->status }}"
                                                    data-can-approve="{{ $transfer->canBeApprovedBy(auth()->user()) ? 'true' : 'false' }}"
                                                    data-items="{{ json_encode($transfer->batch_items ?? [['name' => $transfer->item_name, 'type' => $transfer->item_type, 'quantity' => $transfer->quantity]]) }}">
                                                <i class="las la-eye"></i> View
                                            </button>

                                            @if($transfer->status === 'pending' && $transfer->canBeApprovedBy(auth()->user()))
                                                <button type="button" class="btn btn-success btn-sm" onclick="approveTransfer({{ $transfer->id }})">
                                                    <i class="las la-check"></i> Approve
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="rejectTransfer({{ $transfer->id }})">
                                                    <i class="las la-times"></i> Reject
                                                </button>
                                            @elseif($transfer->status === 'accounting_review' && ($isAccountingReviewer ?? false))
                                                <button class="btn btn-info btn-sm" onclick="accountingApproveTransfer({{ $transfer->id }})">
                                                    <i class="las la-file-invoice"></i> Accounting Approve
                                                </button>
                                            @elseif($transfer->status === 'logistics_assigned')
                                                <div class="d-flex flex-column gap-1">
                                                    @if($transfer->canBeCompletedBy(auth()->user()))
                                                        <button class="btn btn-success btn-sm" onclick="completeLogisticsTransfer({{ $transfer->id }})">
                                                            <i class="las la-check-double"></i> Mark Completed
                                                        </button>
                                                    @endif
                                                    @if($isLogisticsAssigner ?? false)
                                                        <div class="d-flex gap-1 mt-1">
                                                            <select class="form-control form-control-sm" id="assignLogistics{{ $transfer->id }}">
                                                                <option value="">Re-assign staff</option>
                                                                @foreach($logisticsUsers ?? [] as $logisticsUser)
                                                                    <option value="{{ $logisticsUser->id }}" {{ $transfer->logistics_assigned_to == $logisticsUser->id ? 'selected' : '' }}>
                                                                        {{ $logisticsUser->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <button class="btn btn-outline-primary btn-sm" onclick="assignLogisticsTransfer({{ $transfer->id }})">
                                                                Re-assign
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif($transfer->status === 'logistics_assignment' && ($isLogisticsAssigner ?? false))
                                                <div class="d-flex gap-1">
                                                    <select class="form-control form-control-sm" id="assignLogistics{{ $transfer->id }}">
                                                        <option value="">Select staff</option>
                                                        @foreach($logisticsUsers ?? [] as $logisticsUser)
                                                            <option value="{{ $logisticsUser->id }}" {{ $transfer->logistics_assigned_to == $logisticsUser->id ? 'selected' : '' }}>
                                                                {{ $logisticsUser->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button class="btn btn-primary btn-sm" onclick="assignLogisticsTransfer({{ $transfer->id }})">
                                                        Assign
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
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

    <!-- Cash Advance Approval Modal -->
    <div class="modal fade" id="cashAdvanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 1200px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 text-white position-relative" style="background: #dc3545; padding: 1.5rem 2rem;">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-3" style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="las la-money-bill-wave fs-24"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-white" id="ca-modal-reference-header">CA-00000</h5>
                            <p class="mb-0 opacity-75 small">Cash Advance Review</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 1.5rem; right: 1.5rem;"></button>
                </div>
                <div class="modal-body p-4" style="background: #f8f9fa;">
                    <div class="row g-4">
                        <!-- Employee Info -->
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 shadow-sm h-100 border-start border-4 border-primary">
                                <label class="text-uppercase small fw-bold text-muted mb-2 d-block">Employee Name</label>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light p-2 me-3">
                                        <i class="las la-user-tie text-primary fs-20"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold" id="ca-modal-name">---</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Amount Requested -->
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 shadow-sm h-100 border-start border-4 border-success">
                                <label class="text-uppercase small fw-bold text-muted mb-2 d-block">Amount Requested</label>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light p-2 me-3">
                                        <i class="las la-wallet text-success fs-20"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold text-success fs-18" id="ca-modal-amount">₱0.00</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Date Needed -->
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 shadow-sm h-100 border-start border-4 border-info">
                                <label class="text-uppercase small fw-bold text-muted mb-2 d-block">Date Needed</label>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light p-2 me-3">
                                        <i class="las la-calendar-check text-info fs-20"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold" id="ca-modal-date">---</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 shadow-sm h-100 border-start border-4 border-warning">
                                <label class="text-uppercase small fw-bold text-muted mb-2 d-block">Current Status</label>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light p-2 me-3">
                                        <i class="las la-info-circle text-warning fs-20"></i>
                                    </div>
                                    <div id="ca-modal-status">
                                        <span class="status-badge status-pending">Manager Review</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Details Section -->
                        <div class="col-12">
                            <div class="p-3 bg-white rounded-3 shadow-sm border">
                                <label class="text-uppercase small fw-bold text-muted mb-2 d-block">Request Details</label>
                                <div id="ca-modal-details" class="bg-light p-3 rounded-2" style="min-height: 80px;">
                                    <!-- Dynamic content here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rejection Reason Container (Floating Style) -->
                    <div id="rejection-reason-container" class="mt-4 p-3 rounded-3 shadow-sm border border-danger bg-white" style="display: none;">
                        <label class="fw-bold text-danger mb-2 d-block"><i class="las la-exclamation-circle me-1"></i> Reason for Rejection</label>
                        <textarea id="rejection_reason_input" class="form-control border-danger mb-3" rows="3" placeholder="Explain why this request is being rejected..."></textarea>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light btn-sm" onclick="toggleRejection()">Cancel</button>
                            <button type="button" class="btn btn-danger btn-sm px-3" onclick="submitRejection()">Confirm Rejection</button>
                        </div>
                    </div>
                </div>
                
                <!-- View-Only Footer (My Submissions) -->
                <div class="modal-footer border-0 p-4" id="footer-view-only" style="display:none;">
                    <button type="button" class="btn btn-secondary px-4 h-45" data-bs-dismiss="modal">Close</button>
                </div>

                <!-- Action Footer (For Approvers) -->
                <div class="modal-footer border-0 p-4" id="footer-actions">
                    <button type="button" class="btn btn-secondary px-4 h-45" data-bs-dismiss="modal">Close</button>
                    <div class="d-flex gap-2 ms-auto">
                        <button type="button" class="btn btn-outline-danger px-4 h-45" id="btn-show-reject" onclick="toggleRejection()">Reject</button>
                        <form action="" method="POST" id="ca-approve-form" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="pending_admin_approval">
                            <button type="submit" class="btn btn-success px-5 h-45 fw-bold shadow-sm">Approve Request</button>
                        </form>
                    </div>

                    <!-- Hidden Rejection Form -->
                    <form action="" method="POST" id="ca-reject-form" style="display: none;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="rejected">
                        <input type="hidden" name="rejection_reason" id="hidden_rejection_reason">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Transfer Details Modal -->
    <div class="modal fade" id="stockTransferModal" tabindex="-1" aria-labelledby="stockTransferModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 1200px;">
            <div class="modal-content border-0 shadow-lg">
                <!-- Header -->
                <div class="modal-header border-0 text-white position-relative" style="background: #dc3545; padding: 1.5rem 2rem;">
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-1" id="stockTransferModalLabel">
                            <i class="las la-exchange-alt me-2"></i>Stock Transfer Request Details
                        </h5>
                        <p class="mb-0 opacity-75 small" id="st-modal-reference-header"></p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-0" style="background: #f8f9fa; overflow-y: auto;">
                    <!-- Info Cards -->
                    <div class="p-3" style="background: #f8f9fa;">
                        <div class="row g-2">
                            <!-- Submitted By -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width:32px;height:32px;background:#f8f9fa;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                            <i class="las la-user" style="font-size:1.1rem;color:#6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size:0.75rem;font-weight:600;">Submitted By</label>
                                            <p id="st-submitted-by" class="mb-0 small fw-semibold" style="color:#212529;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Request Type -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width:32px;height:32px;background:#f8f9fa;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                            <i class="las la-tag" style="font-size:1.1rem;color:#6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size:0.75rem;font-weight:600;">Request Type</label>
                                            <p class="mb-0 small fw-semibold" style="color:#212529;">Stock Transfer Request</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- From Site -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width:32px;height:32px;background:#f8f9fa;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                            <i class="las la-warehouse" style="font-size:1.1rem;color:#6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size:0.75rem;font-weight:600;">From Site</label>
                                            <p id="st-from-site" class="mb-0 small fw-semibold" style="color:#212529;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- To Site -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width:32px;height:32px;background:#f8f9fa;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                            <i class="las la-map-marker" style="font-size:1.1rem;color:#6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size:0.75rem;font-weight:600;">To Site</label>
                                            <p id="st-to-site" class="mb-0 small fw-semibold" style="color:#212529;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Date Submitted -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width:32px;height:32px;background:#f8f9fa;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                            <i class="las la-calendar" style="font-size:1.1rem;color:#6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size:0.75rem;font-weight:600;">Date Submitted</label>
                                            <p id="st-modal-date" class="mb-0 small fw-semibold" style="color:#212529;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width:32px;height:32px;background:#f8f9fa;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                            <i class="las la-info-circle" style="font-size:1.1rem;color:#6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size:0.75rem;font-weight:600;">Status</label>
                                            <p id="st-modal-status" class="mb-0 small fw-semibold" style="color:#212529;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table Section -->
                    <div class="px-3 pb-3" style="background:#f8f9fa;">
                        <div class="details-section p-3 rounded-3 bg-white border">
                            <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                <div class="icon-wrapper me-2" style="width:32px;height:32px;background:#dc3545;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                    <i class="las la-books text-white" style="font-size:1.1rem;"></i>
                                </div>
                                <h6 class="fw-bold mb-0" style="color:#212529;">Books Included in Transfer</h6>
                            </div>
                            <div class="table-responsive" style="max-height:280px;overflow-y:auto;">
                                <table class="table table-bordered table-hover align-middle mb-0 small">
                                    <thead class="table-light" style="position:sticky;top:0;z-index:2;">
                                        <tr>
                                            <th style="width:40%">Book Title / Code</th>
                                            <th style="width:15%">Type</th>
                                            <th class="text-end" style="width:15%">Unit Price</th>
                                            <th class="text-center" style="width:15%">Quantity</th>
                                            <th class="text-end" style="width:15%">Total Price</th>
                                        </tr>
                                    </thead>
                                    <tbody id="st-items-body"></tbody>
                                </table>
                            </div>
                            <!-- Notes -->
                            <div id="st-notes-row" class="mt-3" style="display:none;">
                                <label class="form-label fw-bold small text-dark mb-1"><i class="las la-sticky-note text-warning me-1"></i>Notes:</label>
                                <div id="st-notes-text" class="p-2 rounded" style="background:#fffbe6;border:1px solid #ffe58f;font-size:0.875rem;color:#555;"></div>
                            </div>
                            <!-- Remarks -->
                            <div id="st-remarks-row" class="mt-3" style="display:none;">
                                <label class="form-label fw-bold small text-dark mb-1"><i class="las la-comment-alt text-primary me-1"></i>Approval Remarks / History:</label>
                                <div id="st-remarks-text" class="p-2 rounded" style="background:#e6f7ff;border:1px solid #91d5ff;font-size:0.875rem;color:#555;white-space:pre-wrap;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer border-top bg-white" style="padding:1.25rem 2rem;">
                    <div class="d-flex justify-content-between w-100 align-items-center">
                        <button type="button" class="btn btn-light px-4 py-2 fw-semibold border" data-bs-dismiss="modal">
                            <i class="las la-times me-1"></i>Close
                        </button>
                        <div class="d-flex gap-2" id="st-modal-actions" style="display:none !important;">
                            <button type="button" class="btn btn-danger px-4 py-2 fw-semibold" id="st-modal-reject-btn">
                                <i class="las la-times-circle me-1"></i>Reject
                            </button>
                            <button type="button" class="btn btn-success px-4 py-2 fw-semibold" id="st-modal-approve-btn">
                                <i class="las la-check-circle me-1"></i>Approve
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach($pendingTeamTransfers as $teamTransfer)
    <div class="modal fade" id="prodTeamStockTransferModal{{ $teamTransfer->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 1100px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white"><i class="las la-boxes me-2"></i>Review Team Stock Transfer ({{ $teamTransfer->transfer_number }})</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-1">Target Sales Team:</small>
                            <span class="badge bg-danger fs-6">{{ $teamTransfer->team_name }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-1">Requested By:</small>
                            <strong>{{ $teamTransfer->transferredByUser->name ?? 'N/A' }}</strong>
                            <small class="d-block text-muted">{{ $teamTransfer->created_at->format('M d, Y h:i A') }}</small>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-1">Remarks / Notes:</small>
                            <span class="fw-semibold text-dark">{{ $teamTransfer->notes ?: 'None' }}</span>
                        </div>
                    </div>

                    @if($teamTransfer->notes)
                    <div class="alert alert-warning border border-warning mb-3 py-2">
                        <strong class="text-dark"><i class="las la-comment-alt me-1"></i>Requester Notes:</strong> {{ $teamTransfer->notes }}
                    </div>
                    @else
                    <div class="alert alert-light border mb-3 py-2 text-muted">
                        <i class="las la-info-circle me-1"></i>No notes specified by requester.
                    </div>
                    @endif

                    @if($teamTransfer->remarks)
                    <div class="alert alert-info border border-info mb-3 py-2">
                        <strong class="text-dark"><i class="las la-history me-1"></i>Approval Remarks / History:</strong>
                        <div style="white-space: pre-wrap;" class="small text-dark mt-1">{{ $teamTransfer->remarks }}</div>
                    </div>
                    @endif

                    <h6 class="fw-bold mb-2">Requested Items (Main Warehouse Transfer):</h6>
                    <div class="table-responsive mb-3" style="max-height: 380px; overflow-y: auto;">
                        <table class="table table-bordered table-sm align-middle mb-0" style="position: relative;">
                            <thead class="table-light" style="position: sticky; top: 0; z-index: 2;">
                                <tr>
                                    <th>Item Title</th>
                                    <th>Type</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-center">Quantity to Transfer</th>
                                    <th class="text-end">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $pGrandTotal = 0; $pTotalQty = 0; @endphp
                                @foreach($teamTransfer->items as $tItem)
                                @php
                                    $itemName = $tItem->bookIndex ? $tItem->bookIndex->display_name : ($tItem->book ? $tItem->book->name : ($tItem->bookBundle ? $tItem->bookBundle->name : 'N/A'));
                                    $itemType = $tItem->bookIndex ? 'Book Index' : ($tItem->bookBundle ? 'Book Bundle' : 'Book');
                                    $uPrice = (float) ($tItem->bookIndex ? ($tItem->bookIndex->price ?: ($tItem->bookIndex->book?->price ?? 0)) : ($tItem->book ? $tItem->book->price : ($tItem->bookBundle ? $tItem->bookBundle->price : 0)));
                                    $barcodeVal = $tItem->bookIndex ? ($tItem->bookIndex->barcode ?: ($tItem->bookIndex->nbs_barcode ?: $tItem->bookIndex->article)) : ($tItem->book ? ($tItem->book->barcode ?: ($tItem->book->isbn ?: $tItem->book->item_code)) : ($tItem->bookBundle ? $tItem->bookBundle->sku : ''));
                                    $subT = $tItem->quantity * $uPrice;
                                    $pGrandTotal += $subT;
                                    $pTotalQty += $tItem->quantity;
                                @endphp
                                <tr>
                                    <td class="fw-bold text-dark">
                                        {{ $itemName }}
                                        @if($barcodeVal)
                                            <br><small class="text-muted"><i class="las la-barcode me-1"></i>Barcode: <code>{{ $barcodeVal }}</code></small>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $itemType }}</span></td>
                                    <td class="text-end font-monospace text-muted">₱{{ number_format($uPrice, 2) }}</td>
                                    <td class="text-center fw-bold text-success">{{ number_format($tItem->quantity) }} pcs</td>
                                    <td class="text-end font-monospace fw-bold text-dark">₱{{ number_format($subT, 2) }}</td>
                                </tr>
                                @endforeach
                                @if(count($teamTransfer->items) > 0)
                                <tr class="table-light fw-bold">
                                    <td colspan="3" class="text-end small">Total Estimated Value:</td>
                                    <td class="text-center text-success">{{ number_format($pTotalQty) }} pcs</td>
                                    <td class="text-end font-monospace text-danger">₱{{ number_format($pGrandTotal, 2) }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <form action="{{ route('production.team-stock-transfer.approve', $teamTransfer->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success fw-bold">
                            <i class="las la-check me-1"></i> Approve & Execute Stock Transfer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        // Global variables to hold table instances
        var queueTable;
        var myApprovalsTable;
        var mySubmissionsTable;
        var myApprovedTable;

        // Global function for bottom card tab switching
        function switchTab(btn, tabId) {
            $('.tab-section').hide();
            $('#' + tabId + '-content').show();
            
            $('.tab-trigger').removeClass('active');
            $(btn).addClass('active');

            // Re-draw tables to fix alignment in hidden tabs
            if (tabId === 'my-submissions' && mySubmissionsTable) mySubmissionsTable.columns.adjust().draw();
            if (tabId === 'my-approvals' && myApprovalsTable) myApprovalsTable.columns.adjust().draw();
            if (tabId === 'my-approved' && myApprovedTable) myApprovedTable.columns.adjust().draw();
        }

        // Global function for top card filtering
        function filterQueue(btn, filterValue) {
            // Update active state
            $('.filter-trigger').removeClass('active');
            $(btn).addClass('active');

            // Apply filter using DataTables
            if (queueTable) {
                if (filterValue === '') {
                    queueTable.search('').columns().search('').draw();
                } else {
                    queueTable.column(0).search(filterValue).draw();
                }
            }
        }

        $(document).ready(function() {
            // Initialize Tables
            queueTable = $('#approvalQueueTable').DataTable({
                order: [[4, 'desc']],
                pageLength: 10,
                columnDefs: [{ orderable: false, targets: -1 }]
            });

            myApprovalsTable = $('#myApprovalsTable').DataTable({
                order: [[4, 'desc']],
                pageLength: 10,
                columnDefs: [{ orderable: false, targets: -1 }]
            });

            mySubmissionsTable = $('#mySubmissionsTable').DataTable({
                order: [[3, 'desc']],
                pageLength: 10,
                columnDefs: [{ orderable: false, targets: -1 }]
            });

            myApprovedTable = $('#myApprovedTable').DataTable({
                order: [[4, 'desc']],
                pageLength: 10,
                columnDefs: [{ orderable: false, targets: -1 }]
            });

            // Cash Advance Modal Population
            $('#cashAdvanceModal').on('show.bs.modal', function (event) {
                var modal = $(this);
                var button = $(event.relatedTarget);
                var id = button.attr('data-id');
                var name = button.attr('data-name');
                var amount = button.attr('data-amount');
                var purpose = button.attr('data-purpose');
                var date = button.attr('data-date');
                var status = button.attr('data-status') || 'pending_supervisor_approval';
                var reference = 'CA-' + String(id).padStart(5, '0');

                modal.find('#ca-modal-name').text(name);
                modal.find('#ca-modal-amount').text(amount);
                modal.find('#ca-modal-date').text(date);
                modal.find('#ca-modal-reference-header').text(reference);

                let original = {};
                try {
                    original = JSON.parse(button.attr('data-original') || '{}');
                } catch(e) {}

                // Dynamic Details Logic
                const fieldLabels = {
                    'purpose': 'Purpose / Justification',
                    'date_needed': 'Date Needed',
                    'amount': 'Principal Amount',
                    'request_type': 'Classification',
                    'repayment_method': 'Repayment Method',
                    'is_liquidation_required': 'Liquidation Required',
                    'employee_name': 'Staff Name',
                    'department': 'Department'
                };
                const excludedFields = [
                    'id', 'user_id', 'created_at', 'updated_at', 'status', 'department_source',
                    'approved_by_manager', 'manager_approved_at',
                    'approved_by_admin', 'admin_approved_at',
                    'approved_by_director', 'director_approved_at',
                    'rejected_by', 'rejected_at', 'rejection_reason', 'amount'
                ];

                let detailsHtml = `<div class="table-responsive"><table class="table table-sm table-borderless mb-0"><tbody>`;
                Object.keys(original).forEach(key => {
                    const val = original[key];
                    if (!excludedFields.includes(key) && val !== null && val !== undefined) {
                        let displayVal = val;
                        if (typeof val === 'boolean' || val === 1 || val === 0) {
                            if (key.includes('is_')) displayVal = val ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-light text-dark">No</span>';
                        }
                        detailsHtml += `<tr><th class="py-1 text-muted small px-0" style="width: 40%;">${fieldLabels[key] || key.replace(/_/g, ' ').toUpperCase()}</th><td class="py-1 text-dark fs-13 px-0">${displayVal}</td></tr>`;
                    }
                });
                detailsHtml += `</tbody></table></div>`;
                modal.find('#ca-modal-details').html(detailsHtml);

                // Status Configuration matching admin-finance logic
                const statusConfig = {
                    'pending_supervisor_approval': { badge: 'warning', text: 'Manager Review' },
                    'pending_admin_approval': { badge: 'info', text: 'Finance Review (2nd Approval)' },
                    'pending_director_approval': { badge: 'primary', text: 'Final Review (Director)' },
                    'forwarded to accounting': { badge: 'info', text: 'Forwarded to Accounting' },
                    'approved': { badge: 'success', text: 'Approved' },
                    'rejected': { badge: 'danger', text: 'Rejected' },
                };
                const config = statusConfig[status] || { badge: 'secondary', text: status.replace('_', ' ') };
                modal.find('#ca-modal-status').html(`<span class="status-badge status-${config.badge === 'warning' ? 'pending' : (config.badge === 'danger' ? 'danger' : 'success')}">${config.text}</span>`);

                // Update Form Actions based on type/module
                var triggerType = button.attr('data-type') || '';
                var triggerModule = button.attr('data-module') || (original.module || '');
                var approveActionUrl = actionUrl;
                var rejectActionUrl = actionUrl;

                if (triggerType === 'Material') {
                    // Material requests live under admin-finance MIS or GSD controllers depending on module
                    if (triggerModule === 'GSD') {
                        approveActionUrl = rejectActionUrl = '/admin-finance/gsd/material-requests/' + id;
                    } else {
                        approveActionUrl = rejectActionUrl = '/admin-finance/mis/material-requests/' + id;
                    }
                } else if (triggerType === 'CCTV') {
                    approveActionUrl = rejectActionUrl = '/my-requests/cctv-requests/' + id;
                } else if (triggerType === 'Auto Debit Letter' || triggerType === 'Auto Debit') {
                    if (status === 'pending_director' || status === 'Pending Director Approval') {
                        approveActionUrl = '/production/ford/auto-debit/' + id + '/approve-director';
                    } else {
                        approveActionUrl = '/production/ford/auto-debit/' + id + '/approve-finance';
                    }
                    rejectActionUrl = '/production/ford/auto-debit/' + id + '/reject';
                } else if (triggerType === 'Cash Advance') {
                    approveActionUrl = rejectActionUrl = '/employee/cash-advance/' + id;
                }

                modal.find('#ca-approve-form').attr('action', approveActionUrl);
                modal.find('#ca-reject-form').attr('action', rejectActionUrl);
                
                // Toggle footers: view-only shows only Close, actionable shows Reject + Approve
                var viewOnly = button.attr('data-view-only') === 'true';
                if (viewOnly || status === 'approved' || status === 'rejected') {
                    modal.find('#footer-view-only').show();
                    modal.find('#footer-actions').hide();
                } else {
                    modal.find('#footer-view-only').hide();
                    modal.find('#footer-actions').show();
                }

                // Reset rejection container
                $('#rejection-reason-container').hide();
                $('#rejection_reason_input').val('');
            });
        });

        function toggleRejection() {
            $('#rejection-reason-container').toggle();
        }

        function submitRejection() {
            const reason = $('#rejection_reason_input').val() ? $('#rejection_reason_input').val().trim() : '';
            if (!reason) {
                alert('Please provide a reason for rejection.');
                return;
            }
            if (!confirm('Are you sure you want to reject this request with reason: "' + reason + '"?')) {
                return;
            }
            $('#hidden_rejection_reason').val(reason);
            $('#ca-reject-form').submit();
        }

        function approveTransfer(transferId) {
            const remarks = prompt('Remarks (Optional):');
            if (remarks === null) {
                return;
            }

            const formData = new FormData();
            formData.append('remarks', remarks);

            fetch(`/stock-transfers/${transferId}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Stock transfer approved.');
                    location.reload();
                    return;
                }

                alert(data.message || 'Unable to approve stock transfer.');
            })
            .catch(() => alert('Unable to approve stock transfer.'));
        }

        function rejectTransfer(transferId) {
            var initialReason = $('#rejection_reason_input').val() ? $('#rejection_reason_input').val().trim() : '';

            $('#stockTransferModal').modal('hide');
            $('#cashAdvanceModal').modal('hide');

            setTimeout(function() {
                window.openTwoStepRejectionFlow(initialReason, function(confirmedReason) {
                    const formData = new FormData();
                    formData.append('rejection_reason', confirmedReason);
                    formData.append('remarks', confirmedReason);
                    formData.append('approval_remarks', confirmedReason);

                    fetch(`/stock-transfers/${transferId}/reject`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Stock transfer rejected successfully.');
                            location.reload();
                            return;
                        }
                        alert(data.message || 'Unable to reject stock transfer.');
                    })
                    .catch(() => alert('Unable to reject stock transfer.'));
                });
            }, 300);
        }
        
        function assignLogisticsTransfer(transferId) {
            const select = document.getElementById(`assignLogistics${transferId}`);
            const logisticsUserId = select ? select.value : '';

            if (!logisticsUserId) {
                alert('Please select a logistics staff.');
                return;
            }

            const formData = new FormData();
            formData.append('logistics_assigned_to', logisticsUserId);

            fetch(`/stock-transfers/${transferId}/assign-logistics`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Transfer assigned!');
                    location.reload();
                    return;
                }

                alert(data.message || 'Error assigning transfer.');
            })
            .catch(() => alert('Error assigning transfer.'));
        }

        function completeLogisticsTransfer(transferId) {
            if (!confirm('Mark this stock transfer as completed? This will move the stock now.')) {
                return;
            }

            fetch(`/stock-transfers/${transferId}/complete`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Transfer completed!');
                    location.reload();
                    return;
                }

                alert(data.message || 'Error completing transfer.');
            })
            .catch(() => alert('Error completing transfer.'));
        }

        $(document).on('click', '.view-transfer-btn', function() {
            var btn = $(this);
            var id = btn.data('id');
            var ref = btn.data('ref');
            var fromSite = btn.data('from-site');
            var toSite = btn.data('to-site');
            var submittedBy = btn.data('submitted-by');
            var date = btn.data('date');
            var notes = btn.data('notes');
            var remarks = btn.data('remarks');
            var status = btn.data('status');
            var items = btn.data('items'); // JSON parsed array

            var modal = $('#stockTransferModal');
            modal.find('#st-modal-reference-header').text(ref);
            modal.find('#st-submitted-by').text(submittedBy);
            modal.find('#st-from-site').text(fromSite);
            modal.find('#st-to-site').text(toSite);
            modal.find('#st-modal-date').text(date);
            modal.find('#st-modal-status').text(status.replace(/_/g, ' ').toUpperCase());

            // Populate items table
            var itemsHtml = '';
            var totalQty = 0;
            var totalAmt = 0;
            if (Array.isArray(items)) {
                items.forEach(function(item) {
                    var qty = parseInt(item.quantity) || 0;
                    var unitPrice = parseFloat(item.unit_price || item.price) || 0;
                    var barcode = item.barcode || '';
                    var subtotal = qty * unitPrice;
                    totalQty += qty;
                    totalAmt += subtotal;
                    itemsHtml += `<tr>
                        <td class="fw-semibold text-dark">
                            ${item.name || 'Unknown Item'}
                            ${barcode ? `<br><small class="text-muted"><i class="las la-barcode me-1"></i>Barcode: <code>${barcode}</code></small>` : ''}
                        </td>
                        <td><span class="badge bg-secondary">${item.type || 'Book'}</span></td>
                        <td class="text-end font-monospace text-muted">₱${unitPrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        <td class="text-center fw-bold text-success">${qty} pcs</td>
                        <td class="text-end font-monospace fw-bold text-dark">₱${subtotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    </tr>`;
                });
                if (items.length > 0) {
                    itemsHtml += `<tr class="table-light fw-bold">
                        <td colspan="3" class="text-end small">Total Estimated Value:</td>
                        <td class="text-center text-success">${totalQty} pcs</td>
                        <td class="text-end font-monospace text-danger">₱${totalAmt.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    </tr>`;
                }
            }
            modal.find('#st-items-body').html(itemsHtml);
            modal.find('#st-modal-total').text((items ? items.length : 0) + ' titles · ' + totalQty + ' pcs total');

            // Populate notes
            if (notes && notes.trim() !== '') {
                modal.find('#st-notes-text').text(notes);
                modal.find('#st-notes-row').show();
            } else {
                modal.find('#st-notes-row').hide();
            }

            // Populate remarks
            if (remarks && remarks.trim() !== '') {
                modal.find('#st-remarks-text').text(remarks);
                modal.find('#st-remarks-row').show();
            } else {
                modal.find('#st-remarks-row').hide();
            }

            // Toggle Approve/Reject actions inside modal if pending
            var canApprove = btn.data('can-approve') === true || btn.data('can-approve') === 'true';
            if (status === 'pending' && canApprove) {
                modal.find('#st-modal-actions').show().css('display', 'flex');
                // Set click listeners for modal approve/reject buttons
                modal.find('#st-modal-approve-btn').off('click').on('click', function() {
                    modal.modal('hide');
                    approveTransfer(id);
                });
                modal.find('#st-modal-reject-btn').off('click').on('click', function() {
                    modal.modal('hide');
                    rejectTransfer(id);
                });
            } else {
                modal.find('#st-modal-actions').hide().attr('style', 'display: none !important;');
            }

            modal.modal('show');
        });
    </script>
    @endpush
</x-app-layout>
