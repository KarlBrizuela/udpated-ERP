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
                        <!-- SO Transaction Type Filter (Dropdown) -->
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; padding: 0.65rem 1rem; background: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef;">
                            <i class="las la-filter" style="color: #cc0000; font-size: 1.1rem;"></i>
                            <label for="soTypeDropdown" style="font-weight: 600; color: #555; font-size: 0.9rem; margin: 0; white-space: nowrap;">Transaction Type:</label>
                            <select id="soTypeDropdown" onchange="filterBySOType(this.value)"
                                style="padding: 0.4rem 0.85rem; border-radius: 6px; border: 2px solid #cc0000; color: #333; font-weight: 600; font-size: 0.875rem; background: #fff; cursor: pointer; min-width: 200px;">
                                <option value="">— All Types —</option>
                                <option value="paid">Paid</option>
                                <option value="area_sales_consignment">Area Sales</option>
                                <option value="area_consignment">Area Consignment</option>
                                <option value="direct_consignment">Direct Consignment</option>
                                <option value="charge">Charge</option>
                                <option value="complimentary">Complimentary</option>
                                <option value="ecom_direct">E-Com Direct</option>
                                 <option value="calculator_pos">Calculator / POS</option>
                                <option value="team_stock_transfer">Team Stock Transfer</option>
                            </select>
                        </div>
                        <div class="table-responsive">
                            <table id="myApprovalsTable" class="display table table-bordered" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Reference #</th>
                                        <th>Customer Name</th>
                                        <th>Submitted By</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Attachment</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salesOrders as $order)
                                    @php
                                        $soTypeLabels = [
                                            'paid' => 'Paid',
                                            'area_sales_consignment' => 'Area Sales',
                                            'area_consignment' => 'Area Consignment',
                                            'direct_consignment' => 'Direct Consignment',
                                            'charge' => 'Charge',
                                            'complimentary' => 'Complimentary',
                                            'ecom_direct' => 'E-Com Direct',
                                            'calculator_pos' => 'Calculator / POS',
                                        ];
                                        $soTypeLabel = $soTypeLabels[$order->type] ?? ucwords(str_replace('_', ' ', $order->type));
                                        $soTypeColors = [
                                            'paid' => ['bg' => '#d1e7dd', 'color' => '#0a3622'],
                                            'area_sales_consignment' => ['bg' => '#cfe2ff', 'color' => '#084298'],
                                            'area_consignment' => ['bg' => '#e0d7ff', 'color' => '#3d0a91'],
                                            'direct_consignment' => ['bg' => '#ffe5d0', 'color' => '#7d3807'],
                                            'charge' => ['bg' => '#f8d7da', 'color' => '#58151c'],
                                            'complimentary' => ['bg' => '#e2e3e5', 'color' => '#41464b'],
                                            'ecom_direct' => ['bg' => '#cff4fc', 'color' => '#055160'],
                                            'calculator_pos' => ['bg' => '#fff3cd', 'color' => '#664d03'],
                                        ];
                                        $soColor = $soTypeColors[$order->type] ?? ['bg' => '#e9ecef', 'color' => '#495057'];
                                    @endphp
                                    <tr data-so-type="{{ $order->type }}">
                                        <td>
                                            <span class="document-type-badge type-sales-order">Sales Order</span><br>
                                            <span style="display: inline-block; margin-top: 3px; padding: 2px 7px; border-radius: 10px; font-size: 0.72rem; font-weight: 700; background: {{ $soColor['bg'] }}; color: {{ $soColor['color'] }};">{{ $soTypeLabel }}</span>
                                        </td>
                                        <td><strong>{{ $order->so_number }}</strong></td>
                                        <td>{{ $order->customer?->customer_name ?? ($order->customer_representative ?: 'N/A') }}</td>
                                        <td>{{ $order->preparedBy->name ?? 'N/A' }}</td>
                                        <td data-order="{{ optional($order->created_at)->timestamp }}">{{ optional($order->created_at)->format('Y-m-d h:i A') }}</td>
                                        <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            @if($order->attachment)
                                                <a href="/storage/{{ $order->attachment }}" target="_blank" class="text-primary mb-1 d-block"><i class="las la-paperclip"></i> PO</a>
                                            @endif
                                            @if($order->proof_of_payment)
                                                <a href="/storage/{{ $order->proof_of_payment }}" target="_blank" class="text-success d-block"><i class="las la-paperclip"></i> Payment</a>
                                            @endif
                                            @if(!$order->attachment && !$order->proof_of_payment)
                                                <span class="text-muted">None</span>
                                            @endif
                                        </td>
                                        <td><span class="status-badge status-pending">Pending Approval</span></td>
                                        <td>
                                            <a href="{{ route('marketing.sales-orders.detail', $order->id) }}" class="btn btn-primary btn-sm"><i class="las la-eye"></i> Review</a>
                                        </td>
                                    </tr>
                                    @endforeach

                                    @foreach($pendingCashAdvances as $advance)
                                    <tr>
                                        <td><span class="document-type-badge badge-info" style="background-color: #e3f2fd; color: #0d47a1;">Cash Advance</span></td>
                                        <td><strong>CA-{{ str_pad($advance->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                        <td><span class="text-muted">N/A</span></td>
                                        <td>{{ $advance->user->name ?? $advance->employee_name }}</td>
                                        <td data-order="{{ optional($advance->created_at)->timestamp }}">{{ optional($advance->created_at)->format('Y-m-d h:i A') }}</td>
                                        <td>₱{{ number_format($advance->amount, 2) }}</td>
                                        <td><span class="text-muted">None</span></td>
                                        <td><span class="status-badge status-pending">Pending Manager</span></td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#cashAdvanceApprovalModal"
                                                    data-id="{{ $advance->id }}"
                                                    data-name="{{ $advance->user->name ?? $advance->employee_name }}"
                                                    data-amount="₱{{ number_format($advance->amount, 2) }}"
                                                    data-purpose="{{ $advance->purpose }}"
                                                    data-date-needed="{{ optional($advance->date_needed)->format('M d, Y') }}">
                                                <i class="las la-eye"></i> Review
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach

                                    @foreach($pendingTransfers as $transfer)
                                    @php
                                        $__bd = $batchData[$transfer->id] ?? null;
                                        $bItems = $__bd ? $__bd['items'] : [[
                                            'id' => $transfer->id,
                                            'name' => $transfer->item_name,
                                            'type' => $transfer->item_type,
                                            'quantity' => (int)$transfer->quantity
                                        ]];
                                        $totQty = $__bd ? $__bd['total_quantity'] : (int)$transfer->quantity;
                                        $itemCount = $__bd ? $__bd['items_count'] : 1;
                                    @endphp
                                    <tr>
                                        <td><span class="document-type-badge" style="background-color: #d4edda; color: #155724;">Stock Transfer</span></td>
                                        <td>
                                            <strong>ST-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}</strong>
                                            @if($itemCount > 1)
                                                <span class="badge bg-secondary ms-1">{{ $itemCount }} items</span>
                                            @endif
                                        </td>
                                        <td><span class="text-muted">N/A</span></td>
                                        <td>{{ $transfer->fromSite->name ?? 'N/A' }}</td>
                                        <td data-order="{{ optional($transfer->created_at)->timestamp }}">{{ optional($transfer->created_at)->format('Y-m-d h:i A') }}</td>
                                        <td>{{ number_format($totQty) }} units @if($itemCount > 1)<small class="text-muted">({{ $itemCount }} titles)</small>@endif</td>
                                        <td><span class="text-muted">None</span></td>
                                        <td><span class="status-badge status-pending">Pending Approval</span></td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#stockTransferApprovalModal"
                                                    data-id="{{ $transfer->id }}"
                                                    data-from-site="{{ $transfer->fromSite->name ?? 'N/A' }}"
                                                    data-to-site="{{ $transfer->toSite->name ?? 'N/A' }}"
                                                    data-submitted-by="{{ $transfer->createdBy->name ?? 'N/A' }}"
                                                    data-date="{{ optional($transfer->created_at)->format('M. d, Y h:i A') }}"
                                                    data-notes="{{ $transfer->notes ?? '' }}"
                                                    data-items="{{ e(json_encode($bItems)) }}">
                                                <i class="las la-eye"></i> Review
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach

                                     @foreach($pendingTeamStockTransfers as $teamTransfer)
                                     <tr data-so-type="team_stock_transfer">
                                         <td><span class="document-type-badge" style="background-color: #ffe5d0; color: #7d3807;">Team Stock Transfer</span></td>
                                         <td><strong>{{ $teamTransfer->transfer_number }}</strong></td>
                                         <td><span class="badge bg-danger">{{ $teamTransfer->team_name }}</span></td>
                                         <td>{{ $teamTransfer->transferredByUser->name ?? 'N/A' }}</td>
                                         <td data-order="{{ optional($teamTransfer->created_at)->timestamp }}">{{ optional($teamTransfer->created_at)->format('Y-m-d h:i A') }}</td>
                                         <td>{{ number_format($teamTransfer->items->sum('quantity')) }} pcs ({{ $teamTransfer->items->count() }} items)</td>
                                         <td><span class="text-muted">None</span></td>
                                         <td><span class="status-badge status-pending">Pending Marketing Approval</span></td>
                                         <td>
                                             <button type="button" class="btn btn-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#teamStockTransferModal{{ $teamTransfer->id }}">
                                                 <i class="las la-eye"></i> Review
                                             </button>
                                         </td>
                                     </tr>
                                     @endforeach

                                     @foreach($pendingCctvRequests as $req)
                                    <tr>
                                        <td><span class="document-type-badge type-job-order">CCTV</span></td>
                                        <td><strong>CCTV-{{ str_pad($req->cctv_req_id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                        <td><span class="text-muted">N/A</span></td>
                                        <td>{{ $req->user->name ?? $req->requested_by }}</td>
                                        <td data-order="{{ optional($req->created_at)->timestamp }}">{{ optional($req->created_at)->format('Y-m-d h:i A') }}</td>
                                        <td>N/A</td>
                                        <td>
                                            @if($req->attachment)
                                                <a href="/storage/{{ $req->attachment }}" target="_blank" class="text-primary"><i class="las la-paperclip"></i> View</a>
                                            @else
                                                <span class="text-muted">None</span>
                                            @endif
                                        </td>
                                        <td><span class="status-badge status-pending">Pending Manager</span></td>
                                        <td>
                                            <form action="{{ route('user.cctv-requests.update', $req->cctv_req_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="Pending HR approval">
                                                <button type="submit" class="btn btn-success btn-sm"><i class="las la-check"></i> Approve</button>
                                            </form>
                                            <form action="{{ route('user.cctv-requests.update', $req->cctv_req_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="las la-times"></i> Reject</button>
                                            </form>
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
                                        <td>
                                            @php
                                                $typeClass = $submission->type === 'Sales Order' ? 'type-sales-order' : 'badge-info';
                                            @endphp
                                            <span class="document-type-badge {{ $typeClass }}">{{ $submission->type }}</span>
                                        </td>
                                        <td><strong>{{ $submission->reference_no }}</strong></td>
                                        <td>{{ $submission->original->customer?->customer_name ?? ($submission->original->customer_representative ?? 'N/A') }}</td>
                                        <td>
                                            @php
                                                if (isset($submission->submitted_date) && $submission->submitted_date instanceof \Carbon\Carbon) {
                                                    echo $submission->submitted_date->format('Y-m-d h:i A');
                                                } elseif (is_string($submission->submitted_date) && $submission->submitted_date) {
                                                    echo $submission->submitted_date;
                                                } else {
                                                    echo '';
                                                }
                                            @endphp
                                        </td>
                                        <td>₱{{ number_format($submission->amount, 2) }}</td>
                                        <td>
                                            @php
                                                $status = $submission->status;
                                                $badgeClass = 'badge-warning';
                                                if (in_array($status, ['approved', 'completed', 'picking', 'delivered'])) $badgeClass = 'badge-success';
                                                elseif (in_array($status, ['rejected', 'cancelled'])) $badgeClass = 'badge-danger';
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                        </td>
                                        <td>
                                            @if($submission->type === 'Sales Order')
                                                <a href="{{ $submission->url }}" class="btn btn-primary btn-sm"><i class="las la-eye"></i> View</a>
                                            @else
                                                <button type="button" 
                                                    class="btn btn-primary btn-xs"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#cashAdvanceApprovalModal"
                                                data-id="{{ $submission->id }}"
                                                data-name="{{ auth()->user()->name }}"
                                                data-amount="₱{{ number_format($submission->amount, 2) }}"
                                                data-purpose="{{ $submission->original->purpose }}"
                                                data-date-needed="{{ optional($submission->original->date_needed)->format('M d, Y') }}"
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
                                        <td><span class="document-type-badge badge-info" style="background-color: #e3f2fd; color: #0d47a1;">{{ $approved->type }}</span></td>
                                        <td><strong>{{ $approved->reference_no }}</strong></td>
                                        <td>{{ $approved->original->customer?->customer_name ?? ($approved->original->customer_representative ?? 'N/A') }}</td>
                                        <td>{{ $approved->submitted_by }}</td>
                                        <td>
                                            @php
                                                if (isset($approved->submitted_date) && $approved->submitted_date instanceof \Carbon\Carbon) {
                                                    echo $approved->submitted_date->format('Y-m-d h:i A');
                                                } elseif (is_string($approved->submitted_date) && $approved->submitted_date) {
                                                    echo $approved->submitted_date;
                                                } else {
                                                    echo '';
                                                }
                                            @endphp
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark">₱{{ number_format($approved->amount, 2) }}</span>
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
                                            <button type="button" class="btn btn-primary btn-xs" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#cashAdvanceApprovalModal" 
                                                data-id="{{ $approved->id }}" 
                                                data-name="{{ $approved->submitted_by }}" 
                                                data-amount="₱{{ number_format($approved->amount, 2) }}" 
                                                data-purpose="{{ $approved->original->purpose }}" 
                                                data-date-needed="{{ optional($approved->original->date_needed)->format('M d, Y') }}"
                                                data-status="{{ $approved->status }}" 
                                                data-reference="{{ $approved->reference_no }}">
                                                <i class="las la-eye"></i> View
                                            </button>
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
                                        <a href="{{ route('marketing.sales-orders.detail', $order->id) }}" class="btn btn-primary btn-sm"><i class="las la-eye"></i> Review</a>
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
                                                data-bs-target="#cashAdvanceApprovalModal"
                                                data-id="{{ $advance->id }}"
                                                data-name="{{ $advance->user->name ?? $advance->employee_name }}"
                                                data-amount="₱{{ number_format($advance->amount, 2) }}"
                                                data-purpose="{{ $advance->purpose }}"
                                                data-date-needed="{{ $advance->date_needed->format('M d, Y') }}"
                                                data-original="{{ json_encode($advance) }}">
                                            <i class="las la-eye"></i> Review
                                        </button>
                                    </td>
                                </tr>
                                @endforeach

                                @foreach($pendingTransfers as $transfer)
                                @php
                                    $__bdDQ = $batchData[$transfer->id] ?? null;
                                    $bItemsDQ = $__bdDQ ? $__bdDQ['items'] : [[
                                        'id' => $transfer->id,
                                        'name' => $transfer->item_name,
                                        'type' => $transfer->item_type,
                                        'quantity' => (int)$transfer->quantity
                                    ]];
                                    $totQtyDQ = $__bdDQ ? $__bdDQ['total_quantity'] : (int)$transfer->quantity;
                                    $itemCountDQ = $__bdDQ ? $__bdDQ['items_count'] : 1;
                                @endphp
                                <tr data-type="Stock Transfer">
                                    <td><span class="document-type-badge" style="background-color: #d4edda; color: #155724;">Stock Transfer</span></td>
                                    <td>
                                        <strong>ST-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}</strong>
                                        @if($itemCountDQ > 1)
                                            <span class="badge bg-secondary ms-1">{{ $itemCountDQ }} items</span>
                                        @endif
                                    </td>
                                    <td><span class="text-muted">N/A</span></td>
                                    <td>{{ $transfer->fromSite->name ?? 'N/A' }}</td>
                                    <td>{{ $transfer->created_at->format('Y-m-d h:i A') }}</td>
                                    <td>{{ number_format($totQtyDQ) }} units @if($itemCountDQ > 1)<small class="text-muted">({{ $itemCountDQ }} titles)</small>@endif</td>
                                    <td><span class="status-badge status-pending">Pending Approval</span></td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-primary btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#stockTransferApprovalModal"
                                                data-id="{{ $transfer->id }}"
                                                data-from-site="{{ $transfer->fromSite->name ?? 'N/A' }}"
                                                data-to-site="{{ $transfer->toSite->name ?? 'N/A' }}"
                                                data-submitted-by="{{ $transfer->createdBy->name ?? 'N/A' }}"
                                                data-date="{{ optional($transfer->created_at)->format('M. d, Y h:i A') }}"
                                                data-notes="{{ $transfer->notes ?? '' }}"
                                                data-items="{{ e(json_encode($bItemsDQ)) }}">
                                            <i class="las la-eye"></i> Review
                                        </button>
                                    </td>
                                </tr>
                                @endforeach

                                @foreach($pendingTeamStockTransfers as $teamTransfer)
                                <tr data-type="Stock Transfer">
                                    <td><span class="document-type-badge" style="background-color: #ffe5d0; color: #7d3807;">Team Stock Transfer</span></td>
                                    <td><strong>{{ $teamTransfer->transfer_number }}</strong></td>
                                    <td><span class="badge bg-danger">{{ $teamTransfer->team_name }}</span></td>
                                    <td>{{ $teamTransfer->transferredByUser->name ?? 'N/A' }}</td>
                                    <td>{{ optional($teamTransfer->created_at)->format('Y-m-d h:i A') }}</td>
                                    <td>{{ number_format($teamTransfer->items->sum('quantity')) }} pcs</td>
                                    <td><span class="status-badge status-pending">Pending Marketing Approval</span></td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#teamStockTransferModal{{ $teamTransfer->id }}">
                                            <i class="las la-eye"></i> Review
                                        </button>
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

    <!-- Cash Advance Approval Modal -->
    <div class="modal fade" id="cashAdvanceApprovalModal" tabindex="-1" aria-labelledby="cashAdvanceApprovalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 1200px;">
            <div class="modal-content border-0 shadow-lg">
                <!-- Header -->
                <div class="modal-header border-0 text-white position-relative" style="background: #dc3545; padding: 1.5rem 2rem;">
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-1" id="cashAdvanceApprovalModalLabel">
                            <i class="las la-file-invoice-dollar me-2"></i>Cash Advance Details
                        </h5>
                        <p class="mb-0 opacity-75 small" id="ca-modal-reference-header"></p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-0">
                    <!-- Key Information Cards -->
                    <div class="p-3" style="background: #f8f9fa;">
                        <div class="row g-2">
                            <!-- Employee Card -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #f8f9fa; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="las la-user" style="font-size: 1.1rem; color: #6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size: 0.75rem; font-weight: 600;">Employee Name</label>
                                            <p id="ca-modal-name" class="fw-bold mb-0 small" style="color: #212529;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Amount Card -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #f8f9fa; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="las la-money-bill-wave" style="font-size: 1.1rem; color: #6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size: 0.75rem; font-weight: 600;">Amount Requested</label>
                                            <p id="ca-modal-amount" class="fw-bold mb-0 small text-primary"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Date Card -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #f8f9fa; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="las la-calendar" style="font-size: 1.1rem; color: #6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size: 0.75rem; font-weight: 600;">Date Needed</label>
                                            <p id="ca-modal-date" class="fw-bold mb-0 small" style="color: #212529;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Card -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #f8f9fa; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="las la-info-circle" style="font-size: 1.1rem; color: #6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size: 0.75rem; font-weight: 600;">Current Status</label>
                                            <div id="ca-modal-status"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="px-4 pb-4" style="background: #f8f9fa;">
                        <div class="details-section p-4 rounded-3 bg-white border">
                            <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #dc3545; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                    <i class="las la-clipboard-list text-white" style="font-size: 1.1rem;"></i>
                                </div>
                                <h6 class="fw-bold mb-0" style="color: #212529;">Request Details</h6>
                            </div>
                            <div id="ca-modal-details" class="p-3 rounded-2" style="background: #f8f9fa; min-height: 80px;"></div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer border-top bg-white" style="padding: 1.25rem 2rem;">
                    <div class="d-flex justify-content-between w-100 align-items-center">
                        <button type="button" class="btn btn-light px-4 py-2 fw-semibold border" data-bs-dismiss="modal">
                            <i class="las la-times me-1"></i>Close
                        </button>
                        <div class="d-flex gap-2">
                            <form action="" method="POST" id="ca-reject-form" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="rejected">
                                <button type="button" class="btn btn-danger px-4 py-2 fw-semibold" onclick="toggleRejection()">
                                    <i class="las la-times-circle me-1"></i>Reject
                                </button>
                                <div id="rejection-reason-container" class="mt-2" style="display:none; position: absolute; background: white; border: 1px solid #ccc; padding: 15px; z-index: 1000; width: 300px; bottom: 80px; right: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                    <label class="fw-bold mb-2 small">Reason for Rejection:</label>
                                    <textarea name="rejection_reason" class="form-control mb-3" rows="3" placeholder="Enter reason..."></textarea>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-danger btn-sm flex-grow-1">Confirm Reject</button>
                                        <button type="button" class="btn btn-light btn-sm flex-grow-1 border" onclick="toggleRejection()">Cancel</button>
                                    </div>
                                </div>
                            </form>
                            
                            <form action="" method="POST" id="ca-approve-form" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="pending_admin_approval">
                                <button type="submit" class="btn btn-success px-4 py-2 fw-semibold">
                                    <i class="las la-check-circle me-1"></i>Approve Request
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Transfer Approval Modal -->
    <div class="modal fade" id="stockTransferApprovalModal" tabindex="-1" aria-labelledby="stockTransferApprovalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 1200px;">
            <div class="modal-content border-0 shadow-lg">
                <!-- Header -->
                <div class="modal-header border-0 text-white position-relative" style="background: #dc3545; padding: 1.5rem 2rem;">
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-1" id="stockTransferApprovalModalLabel">
                            <i class="las la-exchange-alt me-2"></i>Request Details
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
                            <!-- Total Items -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width:32px;height:32px;background:#f8f9fa;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                            <i class="las la-box" style="font-size:1.1rem;color:#6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size:0.75rem;font-weight:600;">Total Items / Qty</label>
                                            <p id="st-modal-total" class="mb-0 small fw-semibold" style="color:#212529;"></p>
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
                            <!-- Approval Remarks -->
                            <div class="mt-3">
                                <label class="form-label fw-bold small text-dark mb-1"><i class="las la-comment-alt text-primary me-1"></i>Add Action / Approval Remarks (Optional):</label>
                                <textarea id="st-approval-remarks" class="form-control form-control-sm" rows="2" placeholder="Type optional remarks before approving or rejecting..."></textarea>
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
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-danger px-4 py-2 fw-semibold" id="st-reject-btn">
                                <i class="las la-times-circle me-1"></i>Reject
                            </button>
                            <button type="button" class="btn btn-success px-4 py-2 fw-semibold" id="st-approve-btn">
                                <i class="las la-check-circle me-1"></i>Approve Request
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    @foreach($pendingTeamStockTransfers as $teamTransfer)
    <div class="modal fade" id="teamStockTransferModal{{ $teamTransfer->id }}" tabindex="-1" aria-hidden="true">
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
                            <small class="d-block text-muted">{{ optional($teamTransfer->created_at)->format('M d, Y h:i A') }}</small>
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
                    <form action="{{ route('marketing.area-sales.team-stocks.approve', $teamTransfer->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success fw-bold">
                            <i class="las la-check me-1"></i> Approve Request
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
        // PHP batch data injected directly — 100% reliable, no HTML encoding issues
        var stockTransferBatchData = @json($batchData);
        var queueTable;
        var myApprovalsTable;
        var mySubmissionsTable;
        var myApprovedTable;
        function switchTab(btn, tabId) {
            $('.tab-section').hide();
            $('#' + tabId + '-content').show();
            $('.tab-trigger').removeClass('active');
            $(btn).addClass('active');
            if (tabId === 'my-submissions' && mySubmissionsTable) mySubmissionsTable.columns.adjust().draw();
            if (tabId === 'my-approved' && myApprovedTable) myApprovedTable.columns.adjust().draw();
            if (tabId === 'my-approvals' && myApprovalsTable) myApprovalsTable.columns.adjust().draw();
        }
        function filterQueue(btn, filterValue) {
            $('.filter-trigger').removeClass('active');
            $(btn).addClass('active');
            if (queueTable) {
                if (!filterValue) {
                    queueTable.column(0).search('').draw();
                } else {
                    queueTable.column(0).search(filterValue, false, true).draw();
                }
            }
        }
        let currentSOTypeFilter = '';
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex, row, counter) {
            if (settings.nTable.id !== 'myApprovalsTable') return true;
            if (!currentSOTypeFilter) return true;
            var nTr = settings.aoData[dataIndex] ? settings.aoData[dataIndex].nTr : null;
            var soType = nTr ? ($(nTr).attr('data-so-type') || '') : '';
            return soType === currentSOTypeFilter;
        });
        function filterBySOType(typeValue) {
            currentSOTypeFilter = typeValue;
            if (myApprovalsTable) {
                myApprovalsTable.draw();
            }
        }
        $(document).ready(function() {
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
            $('#cashAdvanceApprovalModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var name = button.data('name');
                var amount = button.data('amount');
                var dateNeeded = button.data('date-needed');
                var status = button.data('status') || 'pending_supervisor_approval';
                var reference = 'CA-' + String(id).padStart(5, '0');
                var modal = $(this);
                modal.find('#ca-modal-name').text(name);
                modal.find('#ca-modal-amount').text(amount);
                modal.find('#ca-modal-date').text(dateNeeded);
                modal.find('#ca-modal-reference-header').text(reference);
                let original = {};
                try {
                    original = typeof button.data('original') === 'string' ? JSON.parse(button.data('original')) : button.data('original');
                } catch(e) {}
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
                var triggerType = button.attr('data-type') || '';
                var approveActionUrl = '/employee/cash-advance/' + id;
                var rejectActionUrl = '/employee/cash-advance/' + id;
                if (triggerType === 'Auto Debit Letter' || triggerType === 'Auto Debit') {
                    if (status === 'pending_director' || status === 'Pending Director Approval') {
                        approveActionUrl = '/production/ford/auto-debit/' + id + '/approve-director';
                    } else {
                        approveActionUrl = '/production/ford/auto-debit/' + id + '/approve-finance';
                    }
                    rejectActionUrl = '/production/ford/auto-debit/' + id + '/reject';
                }
                modal.find('#ca-approve-form').attr('action', approveActionUrl);
                modal.find('#ca-reject-form').attr('action', rejectActionUrl);
                var viewOnly = button.data('view-only') === true || button.data('view-only') === 'true';
                if (viewOnly || status === 'approved' || status === 'rejected') {
                    modal.find('#ca-approve-form, #ca-reject-form').hide();
                } else {
                    modal.find('#ca-approve-form, #ca-reject-form').show();
                }
            });
            $('#stockTransferApprovalModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                if (button.length && !button.data('id')) {
                    button = button.closest('[data-id]');
                }
                var id = button.data('id') || button.attr('data-id');
                var fromSite = button.data('from-site') || button.attr('data-from-site');
                var toSite   = button.data('to-site') || button.attr('data-to-site');
                var submittedBy = button.data('submitted-by') || button.attr('data-submitted-by') || 'N/A';
                var dateSubmitted = button.data('date') || button.attr('data-date') || 'N/A';
                var notes = button.data('notes') || button.attr('data-notes') || '';
                var reference = 'ST-' + String(id || 0).padStart(5, '0');
                var itemsData = [];
                var batchInfo = (typeof stockTransferBatchData !== 'undefined') ? stockTransferBatchData[id] : null;
                if (batchInfo && Array.isArray(batchInfo.items) && batchInfo.items.length > 0) {
                    itemsData = batchInfo.items;
                } else {
                    var itemsRaw = button.attr('data-items');
                    if (itemsRaw) {
                        try { itemsData = JSON.parse(itemsRaw); } catch(e) { itemsData = []; }
                    }
                    if (!Array.isArray(itemsData) || itemsData.length === 0) {
                        itemsData = [{ name: button.attr('data-book-name') || 'Unknown Item', type: 'Book', quantity: 1 }];
                    }
                }
                var modal = $(this);
                modal.find('#st-submitted-by').text(submittedBy);
                modal.find('#st-from-site').text(fromSite || 'N/A');
                modal.find('#st-to-site').text(toSite || 'N/A');
                modal.find('#st-modal-date').text(dateSubmitted);
                modal.find('#st-modal-reference-header').text(reference + (itemsData.length > 1 ? ' (' + itemsData.length + ' items)' : ''));
                var rowsHtml = '';
                var totalQty = 0;
                var grandTotalVal = 0;
                itemsData.forEach(function(item) {
                    var qty = parseInt(item.quantity) || 0;
                    totalQty += qty;
                    var unitPrice = parseFloat(item.unit_price) || 0;
                    var barcode = item.barcode || '';
                    var lineTotal = qty * unitPrice;
                    grandTotalVal += lineTotal;
                    var typeColor = item.type === 'Book' ? 'success' : (item.type === 'Bundle' ? 'warning' : 'secondary');
                    rowsHtml += `<tr>
                        <td class="fw-semibold text-dark">
                            ${item.name || 'Unknown Item'}
                            ${barcode ? `<br><small class="text-muted"><i class="las la-barcode me-1"></i>Barcode: <code>${barcode}</code></small>` : ''}
                        </td>
                        <td><span class="badge bg-${typeColor}">${item.type || 'Item'}</span></td>
                        <td class="text-end font-monospace text-muted">₱${unitPrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        <td class="text-center fw-bold text-success">${qty} pcs</td>
                        <td class="text-end font-monospace fw-bold text-dark">₱${lineTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    </tr>`;
                });
                if (itemsData.length > 0) {
                    rowsHtml += `<tr class="table-light fw-bold">
                        <td colspan="3" class="text-end small">Total Estimated Value:</td>
                        <td class="text-center text-success">${totalQty} pcs</td>
                        <td class="text-end font-monospace text-danger">₱${grandTotalVal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    </tr>`;
                }
                modal.find('#st-items-body').html(rowsHtml);
                modal.find('#st-modal-total').text(itemsData.length + ' title(s) · ' + totalQty + ' pcs total');
                if (notes && notes.trim() !== '') {
                    modal.find('#st-notes-text').text(notes);
                    modal.find('#st-notes-row').show();
                } else {
                    modal.find('#st-notes-row').hide();
                }
                modal.data('transfer-id', id);
                modal.attr('data-transfer-id', id);
                window.currentStockTransferId = id;
                $('#st-rejection-reason-container').hide();
                $('#st-rejection-reason-text').val('');
                $('#st-approval-remarks').val('');
            });
            $(document).on('click', '#st-approve-btn', function(e) {
                e.preventDefault();
                var modal = $('#stockTransferApprovalModal');
                var transferId = modal.data('transfer-id') || modal.attr('data-transfer-id') || window.currentStockTransferId;
                if (!transferId) {
                    var refText = modal.find('#st-modal-reference-header').text();
                    var match = refText.match(/ST-(\d+)/i);
                    if (match) {
                        transferId = parseInt(match[1], 10);
                    }
                }
                if (transferId) {
                    approveStockTransfer(transferId);
                } else {
                    alert('Unable to identify stock transfer ID. Please refresh and try again.');
                }
            });
            $(document).on('click', '#st-reject-btn, #st-confirm-reject-btn', function(e) {
                e.preventDefault();
                var modal = $('#stockTransferApprovalModal');
                var transferId = modal.data('transfer-id') || modal.attr('data-transfer-id') || window.currentStockTransferId;
                if (!transferId) {
                    var refText = modal.find('#st-modal-reference-header').text();
                    var match = refText.match(/ST-(\d+)/i);
                    if (match) {
                        transferId = parseInt(match[1], 10);
                    }
                }
                if (transferId) {
                    rejectStockTransfer(transferId);
                } else {
                    alert('Unable to identify stock transfer ID. Please refresh and try again.');
                }
            });
            $(document).on('submit', '#ca-reject-form', function(e) {
                const form = this;
                if ($(form).data('rejection-confirmed') === true) {
                    return true;
                }
                e.preventDefault();
                let initialReason = ($(form).find('textarea[name="rejection_reason"]').val() || '').trim();
                $('#cashAdvanceModal').modal('hide');
                setTimeout(function() {
                    window.openTwoStepRejectionFlow(initialReason, function(confirmedReason) {
                        $(form).find('textarea[name="rejection_reason"]').val(confirmedReason);
                        $(form).data('rejection-confirmed', true);
                        form.submit();
                    });
                }, 300);
            });
        });
        function approveStockTransfer(transferId) {
            var remarksVal = $('#st-approval-remarks').val();
            $.ajax({
                url: '/stock-transfers/' + transferId + '/approve',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    remarks: remarksVal
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#stockTransferApprovalModal').modal('hide');
                        location.reload();
                    }
                },
                error: function(xhr) {
                    let message = 'Error approving transfer';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                }
            });
        }
        function rejectStockTransfer(transferId) {
            var initialReason = ($('#st-approval-remarks').val() || $('textarea[name="rejection_reason"]').val() || '').trim();
            $('#stockTransferApprovalModal').modal('hide');
            setTimeout(function() {
                window.openTwoStepRejectionFlow(initialReason, function(confirmedReason) {
                    $.ajax({
                        url: '/stock-transfers/' + transferId + '/reject',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            'rejection_reason': confirmedReason,
                            'remarks': confirmedReason,
                            'approval_remarks': confirmedReason
                        },
                        success: function(response) {
                            alert(response.message || 'Stock transfer rejected.');
                            location.reload();
                        },
                        error: function(xhr) {
                            let message = 'Error rejecting transfer';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            alert(message);
                        }
                    });
                });
            }, 300);
        }
    </script>
    @endpush
</x-app-layout>
