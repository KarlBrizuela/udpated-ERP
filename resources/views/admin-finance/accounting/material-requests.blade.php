<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link href="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <style>
        .material-card {
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
        
        .badge-pending { background-color: #ff9800; color: #fff; }
        .badge-processing { background-color: #2196f3; color: #fff; }
        .badge-completed { background-color: #4caf50; color: #fff; }
        .badge-info { background-color: #17a2b8; color: #fff; }
        
        .action-link {
            text-decoration: none;
            font-weight: 600;
        }
        .action-link:hover {
            text-decoration: underline;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card material-card">
                <div class="document-title mb-4">MATERIAL REQUESTS (INCOMING)</div>

                <div class="card-body p-0">
                    <!-- STATUS-BASED TABS -->
                    <div class="mb-3">
                        <h6 class="text-muted ms-3 mt-3" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">By Status</h6>
                        <ul class="nav nav-tabs" role="tablist" id="statusTabs">
                            <li class="nav-item">
                                <a class="nav-link {{ !request()->hasAny(['date_range', 'department', 'status', 'requested_by']) ? 'active' : '' }}" data-bs-toggle="tab" href="#pending-action" role="tab">Pending Action</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#processing" role="tab">Processing</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#completed" role="tab">Completed</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->hasAny(['date_range', 'department', 'status', 'requested_by', 'min_amount', 'max_amount']) ? 'active' : '' }}" data-bs-toggle="tab" href="#all-requests" role="tab">All Requests</a>
                            </li>
                        </ul>
                    </div>

                    <!-- DEPARTMENT-BASED TABS -->
                    <div class="mb-3">
                        <h6 class="text-muted ms-3 mt-3" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">By Department</h6>
                        <ul class="nav nav-tabs" role="tablist" id="departmentTabs">
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#direct" role="tab">Direct <span class="badge bg-info ms-2">{{ count($directRequests) }}</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#gsd" role="tab">GSD <span class="badge bg-info ms-2">{{ count($gsdRequests) }}</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#mis" role="tab">MIS <span class="badge bg-info ms-2">{{ count($misRequests) }}</span></a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content pt-4">
                        <!-- Pending Action Tab -->
                        <div class="tab-pane fade {{ !request()->hasAny(['date_range', 'department', 'status', 'requested_by']) ? 'show active' : '' }}" id="pending-action" role="tabpanel">
                            <div class="px-2">
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="input-group shadow-sm border-0">
                                            <span class="input-group-text bg-white border-0"><i class="las la-search text-muted"></i></span>
                                            <input type="text" class="form-control border-0 ps-0" placeholder="Search Req#, Department...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select border-0 shadow-sm">
                                            <option value="">Filter: Priority</option>
                                            <option value="high">High</option>
                                            <option value="medium">Medium</option>
                                            <option value="low">Low</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Request #</th>
                                                <th>Requested By</th>
                                                <th>Department</th>
                                                <th>Date Approved</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($pendingRequests as $req)
                                            <tr>
                                                <td class="fw-bold text-primary">#MAT-{{ str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT) }}</td>
                                                <td>{{ $req->user->name ?? $req->requested_by }}</td>
                                                <td>{{ $req->user->department ?? 'N/A' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($req->director_approved_at)->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="#" class="btn btn-primary shadow sharp btn-view-mr" title="View"
                                                           data-bs-toggle="modal" 
                                                           data-bs-target="#viewMaterialRequestModal"
                                                           data-id="{{ $req->material_req_id }}"
                                                           data-reference="#MAT-{{ str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT) }}"
                                                           data-requested-by="{{ $req->user->name ?? $req->requested_by }}"
                                                           data-department="{{ $req->user->department ?? 'N/A' }}"
                                                           data-date="{{ $req->created_at->format('M d, Y') }}"
                                                           data-details="{{ $req->request_details }}"
                                                           data-status="Forwarded to Accounting"
                                                           data-status-badge="info"
                                                           data-manager="{{ $req->manager->name ?? 'N/A' }}"
                                                           data-manager-date="{{ $req->manager_approved_at ? \Carbon\Carbon::parse($req->manager_approved_at)->format('M d, Y') : 'N/A' }}"
                                                           data-director="{{ $req->director->name ?? 'N/A' }}"
                                                           data-director-date="{{ $req->director_approved_at ? \Carbon\Carbon::parse($req->director_approved_at)->format('M d, Y') : 'N/A' }}" data-amount="{{ $req->amount ? 'PhP ' . number_format($req->amount, 2) : '—' }}"
                                                        ><i class="las la-eye"></i></a>
                                                        <a href="#" class="btn btn-success shadow sharp" title="Process"><i class="las la-file-invoice"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">No pending requests found.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Processing Tab -->
                        <div class="tab-pane fade" id="processing" role="tabpanel">
                            <div class="px-2">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Request #</th>
                                                <th>Requested By</th>
                                                <th>Department</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($processingRequests as $req)
                                            <tr>
                                                <td class="fw-bold text-primary">#MAT-{{ str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT) }}</td>
                                                <td>{{ $req->user->name ?? $req->requested_by }}</td>
                                                <td>{{ $req->user->department ?? 'N/A' }}</td>
                                                <td><span class="text-muted small fw-bold text-uppercase">Material</span></td>
                                                <td><span class="badge light badge-processing">Processing</span></td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="#" class="btn btn-primary shadow sharp btn-view-mr" title="View"
                                                           data-bs-toggle="modal" 
                                                           data-bs-target="#viewMaterialRequestModal"
                                                           data-id="{{ $req->material_req_id }}"
                                                           data-reference="#MAT-{{ str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT) }}"
                                                           data-requested-by="{{ $req->user->name ?? $req->requested_by }}"
                                                           data-department="{{ $req->user->department ?? 'N/A' }}"
                                                           data-date="{{ $req->created_at->format('M d, Y') }}"
                                                           data-details="{{ $req->request_details }}"
                                                           data-status="Processing"
                                                           data-status-badge="processing"
                                                           data-manager="{{ $req->manager->name ?? 'N/A' }}"
                                                           data-manager-date="{{ $req->manager_approved_at ? \Carbon\Carbon::parse($req->manager_approved_at)->format('M d, Y') : 'N/A' }}"
                                                           data-director="{{ $req->director->name ?? 'N/A' }}"
                                                           data-director-date="{{ $req->director_approved_at ? \Carbon\Carbon::parse($req->director_approved_at)->format('M d, Y') : 'N/A' }}" data-amount="{{ $req->amount ? 'PhP ' . number_format($req->amount, 2) : '—' }}"
                                                        ><i class="las la-eye"></i></a>
                                                        <a href="#" class="btn btn-info shadow sharp" title="Create CV"><i class="las la-file-invoice-dollar"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">No processing requests found.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Completed Tab -->
                        <div class="tab-pane fade" id="completed" role="tabpanel">
                            <div class="px-2">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Request #</th>
                                                <th>Requested By</th>
                                                <th>Department</th>
                                                <th>Processed By</th>
                                                <th>Date Closed</th>
                                                <th>Reference Doc</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($completedRequests as $req)
                                            <tr>
                                                <td class="fw-bold text-primary">#MAT-{{ str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT) }}</td>
                                                <td>{{ $req->user->name ?? $req->requested_by }}</td>
                                                <td>{{ $req->user->department ?? 'N/A' }}</td>
                                                <td>N/A</td>
                                                <td>{{ $req->updated_at->format('M d, Y') }}</td>
                                                <td><span class="text-muted small fw-bold">N/A</span></td>
                                                <td>
                                                    <a href="#" class="btn btn-primary shadow sharp btn-view-mr" title="View"
                                                       data-bs-toggle="modal" 
                                                       data-bs-target="#viewMaterialRequestModal"
                                                       data-id="{{ $req->material_req_id }}"
                                                       data-reference="#MAT-{{ str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT) }}"
                                                       data-requested-by="{{ $req->user->name ?? $req->requested_by }}"
                                                       data-department="{{ $req->user->department ?? 'N/A' }}"
                                                       data-date="{{ $req->created_at->format('M d, Y') }}"
                                                       data-details="{{ $req->request_details }}"
                                                       data-status="Received"
                                                       data-status-badge="success"
                                                       data-manager="{{ $req->manager->name ?? 'N/A' }}"
                                                       data-manager-date="{{ $req->manager_approved_at ? \Carbon\Carbon::parse($req->manager_approved_at)->format('M d, Y') : 'N/A' }}"
                                                       data-director="{{ $req->director->name ?? 'N/A' }}"
                                                       data-director-date="{{ $req->director_approved_at ? \Carbon\Carbon::parse($req->director_approved_at)->format('M d, Y') : 'N/A' }}" data-amount="{{ $req->amount ? 'PhP ' . number_format($req->amount, 2) : '—' }}"
                                                    ><i class="las la-eye"></i></a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">No completed requests found.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Direct Tab -->
                        <div class="tab-pane fade" id="direct" role="tabpanel">
                            <div class="px-2">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Request #</th>
                                                <th>Requested By</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($directRequests as $req)
                                            <tr>
                                                <td class="fw-bold text-primary">#MAT-{{ str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT) }}</td>
                                                <td>{{ $req->user->name ?? $req->requested_by }}</td>
                                                <td>{{ $req->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    @if($req->status === 'forwarded to accounting')
                                                        <span class="badge light badge-pending">Pending</span>
                                                    @elseif($req->status === 'received')
                                                        <span class="badge light badge-completed">Completed</span>
                                                    @elseif($req->status === 'processing')
                                                        <span class="badge light badge-processing">Processing</span>
                                                    @else
                                                        <span class="badge light bg-secondary">{{ $req->status }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-primary shadow sharp btn-view-mr" title="View"
                                                       data-bs-toggle="modal" 
                                                       data-bs-target="#viewMaterialRequestModal"
                                                       data-id="{{ $req->material_req_id }}"
                                                       data-reference="#MAT-{{ str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT) }}"
                                                       data-requested-by="{{ $req->user->name ?? $req->requested_by }}"
                                                       data-department="{{ $req->user->department ?? 'N/A' }}"
                                                       data-date="{{ $req->created_at->format('M d, Y') }}"
                                                       data-details="{{ $req->request_details }}"
                                                       data-status="{{ ucfirst($req->status) }}"
                                                       data-status-badge="{{ $req->status === 'received' ? 'success' : ($req->status === 'forwarded to accounting' ? 'info' : 'processing') }}"
                                                       data-manager="{{ $req->manager->name ?? 'N/A' }}"
                                                       data-manager-date="{{ $req->manager_approved_at ? \Carbon\Carbon::parse($req->manager_approved_at)->format('M d, Y') : 'N/A' }}"
                                                       data-director="{{ $req->director->name ?? 'N/A' }}"
                                                       data-director-date="{{ $req->director_approved_at ? \Carbon\Carbon::parse($req->director_approved_at)->format('M d, Y') : 'N/A' }}" data-amount="{{ $req->amount ? 'PhP ' . number_format($req->amount, 2) : '—' }}"
                                                    ><i class="las la-eye"></i></a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">No Direct requests found.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- GSD Tab -->
                        <div class="tab-pane fade" id="gsd" role="tabpanel">
                            <div class="px-2">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Request #</th>
                                                <th>Requested By</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($gsdRequests as $req)
                                            <tr>
                                                <td class="fw-bold text-primary">#MAT-{{ str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT) }}</td>
                                                <td>{{ $req->user->name ?? $req->requested_by }}</td>
                                                <td>{{ $req->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    @if($req->status === 'forwarded to accounting')
                                                        <span class="badge light badge-pending">Pending</span>
                                                    @elseif($req->status === 'received')
                                                        <span class="badge light badge-completed">Completed</span>
                                                    @elseif($req->status === 'processing')
                                                        <span class="badge light badge-processing">Processing</span>
                                                    @else
                                                        <span class="badge light bg-secondary">{{ $req->status }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-primary shadow sharp btn-view-mr" title="View"
                                                       data-bs-toggle="modal" 
                                                       data-bs-target="#viewMaterialRequestModal"
                                                       data-id="{{ $req->material_req_id }}"
                                                       data-reference="#MAT-{{ str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT) }}"
                                                       data-requested-by="{{ $req->user->name ?? $req->requested_by }}"
                                                       data-department="{{ $req->user->department ?? 'N/A' }}"
                                                       data-date="{{ $req->created_at->format('M d, Y') }}"
                                                       data-details="{{ $req->request_details }}"
                                                       data-status="{{ ucfirst($req->status) }}"
                                                       data-status-badge="{{ $req->status === 'received' ? 'success' : ($req->status === 'forwarded to accounting' ? 'info' : 'processing') }}"
                                                       data-manager="{{ $req->manager->name ?? 'N/A' }}"
                                                       data-manager-date="{{ $req->manager_approved_at ? \Carbon\Carbon::parse($req->manager_approved_at)->format('M d, Y') : 'N/A' }}"
                                                       data-director="{{ $req->director->name ?? 'N/A' }}"
                                                       data-director-date="{{ $req->director_approved_at ? \Carbon\Carbon::parse($req->director_approved_at)->format('M d, Y') : 'N/A' }}" data-amount="{{ $req->amount ? 'PhP ' . number_format($req->amount, 2) : '—' }}"
                                                    ><i class="las la-eye"></i></a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">No GSD requests found.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- MIS Tab -->
                        <div class="tab-pane fade" id="mis" role="tabpanel">
                            <div class="px-2">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Request #</th>
                                                <th>Requested By</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($misRequests as $req)
                                            <tr>
                                                <td class="fw-bold text-primary">#MAT-{{ str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT) }}</td>
                                                <td>{{ $req->user->name ?? $req->requested_by }}</td>
                                                <td>{{ $req->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    @if($req->status === 'forwarded to accounting')
                                                        <span class="badge light badge-pending">Pending</span>
                                                    @elseif($req->status === 'received')
                                                        <span class="badge light badge-completed">Completed</span>
                                                    @elseif($req->status === 'processing')
                                                        <span class="badge light badge-processing">Processing</span>
                                                    @else
                                                        <span class="badge light bg-secondary">{{ $req->status }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-primary shadow sharp btn-view-mr" title="View"
                                                       data-bs-toggle="modal" 
                                                       data-bs-target="#viewMaterialRequestModal"
                                                       data-id="{{ $req->material_req_id }}"
                                                       data-reference="#MAT-{{ str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT) }}"
                                                       data-requested-by="{{ $req->user->name ?? $req->requested_by }}"
                                                       data-department="{{ $req->user->department ?? 'N/A' }}"
                                                       data-date="{{ $req->created_at->format('M d, Y') }}"
                                                       data-details="{{ $req->request_details }}"
                                                       data-status="{{ ucfirst($req->status) }}"
                                                       data-status-badge="{{ $req->status === 'received' ? 'success' : ($req->status === 'forwarded to accounting' ? 'info' : 'processing') }}"
                                                       data-manager="{{ $req->manager->name ?? 'N/A' }}"
                                                       data-manager-date="{{ $req->manager_approved_at ? \Carbon\Carbon::parse($req->manager_approved_at)->format('M d, Y') : 'N/A' }}"
                                                       data-director="{{ $req->director->name ?? 'N/A' }}"
                                                       data-director-date="{{ $req->director_approved_at ? \Carbon\Carbon::parse($req->director_approved_at)->format('M d, Y') : 'N/A' }}" data-amount="{{ $req->amount ? 'PhP ' . number_format($req->amount, 2) : '—' }}"
                                                    ><i class="las la-eye"></i></a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">No MIS requests found.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- All Requests Tab -->
                        <div class="tab-pane fade {{ request()->hasAny(['date_range', 'department', 'status', 'requested_by', 'min_amount', 'max_amount']) ? 'show active' : '' }}" id="all-requests" role="tabpanel">
                            <div class="px-2">
                                <!-- Advanced Filters -->
                                <form action="{{ route('admin-finance.accounting.material-requests.incoming') }}" method="GET">
                                    <div class="row mb-4 g-3">
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold">Date Range</label>
                                            <div class="input-group shadow-sm">
                                                <span class="input-group-text bg-white border-end-0"><i class="las la-calendar text-muted"></i></span>
                                                <input type="text" name="date_range" class="form-control border-start-0 ps-0 daterange" placeholder="Select dates..." value="{{ request('date_range') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold">Department</label>
                                            <select name="department" class="form-select shadow-sm border-0">
                                                <option value="">All Departments</option>
                                                <option value="editorial" {{ request('department') == 'editorial' ? 'selected' : '' }}>Editorial</option>
                                                <option value="sales" {{ request('department') == 'sales' ? 'selected' : '' }}>Sales</option>
                                                <option value="gsd" {{ request('department') == 'gsd' ? 'selected' : '' }}>GSD</option>
                                                <option value="mis" {{ request('department') == 'mis' ? 'selected' : '' }}>MIS</option>
                                                <option value="hr" {{ request('department') == 'hr' ? 'selected' : '' }}>HR</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold">Status</label>
                                            <select name="status" class="form-select shadow-sm border-0">
                                                <option value="">All Status</option>
                                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold">Type</label>
                                            <select name="type" class="form-select shadow-sm border-0">
                                                <option value="">All Types</option>
                                                <option value="material" {{ request('type') == 'material' ? 'selected' : '' }}>Material</option>
                                                <option value="cash_advance" {{ request('type') == 'cash_advance' ? 'selected' : '' }}>Cash Advance</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold">Requested By</label>
                                            <input type="text" name="requested_by" class="form-control shadow-sm border-0" placeholder="Name..." value="{{ request('requested_by') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold">Amount Range</label>
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="number" name="min_amount" class="form-control shadow-sm border-0" placeholder="Min" value="{{ request('min_amount') }}">
                                                <span class="text-muted">to</span>
                                                <input type="number" name="max_amount" class="form-control shadow-sm border-0" placeholder="Max" value="{{ request('max_amount') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end gap-2">
                                            <button type="submit" class="btn btn-primary shadow-sm flex-grow-1"><i class="las la-filter me-1"></i>Apply Filters</button>
                                            <a href="{{ route('admin-finance.accounting.material-requests.incoming') }}" class="btn btn-light shadow-sm"><i class="las la-times me-1"></i>Clear</a>
                                        </div>
                                    </div>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Request #</th>
                                                <th>Date</th>
                                                <th>Requested By</th>
                                                <th>Department</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($allRequests as $req)
                                            <tr>
                                                <td class="fw-bold text-primary">#MAT-{{ str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT) }}</td>
                                                <td>{{ $req->created_at->format('M d, Y') }}</td>
                                                <td>{{ $req->user->name ?? $req->requested_by }}</td>
                                                <td>{{ $req->user->department ?? 'N/A' }}</td>
                                                <td>Material</td>
                                                <td>
                                                    @if($req->status === 'forwarded to accounting')
                                                        <span class="badge light badge-pending">Accounting Pending</span>
                                                    @elseif($req->status === 'received')
                                                        <span class="badge light badge-completed">Received</span>
                                                    @elseif($req->status === 'processing')
                                                        <span class="badge light badge-processing">Processing</span>
                                                    @else
                                                        <span class="badge light bg-secondary">{{ $req->status }}</span>
                                                    @endif
                                                </td>
                                                <td><a href="#" class="btn btn-primary shadow sharp btn-view-mr" title="View"
                                                       data-bs-toggle="modal" 
                                                       data-bs-target="#viewMaterialRequestModal"
                                                       data-id="{{ $req->material_req_id }}"
                                                       data-reference="#MAT-{{ str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT) }}"
                                                       data-requested-by="{{ $req->user->name ?? $req->requested_by }}"
                                                       data-department="{{ $req->user->department ?? 'N/A' }}"
                                                       data-date="{{ $req->created_at->format('M d, Y') }}"
                                                       data-details="{{ $req->request_details }}"
                                                       data-status="{{ ucfirst($req->status) }}"
                                                       data-status-badge="{{ $req->status === 'received' ? 'success' : ($req->status === 'forwarded to accounting' ? 'info' : 'processing') }}"
                                                       data-manager="{{ $req->manager->name ?? 'N/A' }}"
                                                       data-manager-date="{{ $req->manager_approved_at ? \Carbon\Carbon::parse($req->manager_approved_at)->format('M d, Y') : 'N/A' }}"
                                                       data-director="{{ $req->director->name ?? 'N/A' }}"
                                                       data-director-date="{{ $req->director_approved_at ? \Carbon\Carbon::parse($req->director_approved_at)->format('M d, Y') : 'N/A' }}" data-amount="{{ $req->amount ? 'PhP ' . number_format($req->amount, 2) : '—' }}"
                                                ><i class="las la-eye"></i></a></td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">No requests found.</td>
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

    <!-- View Material Request Modal -->
    <div class="modal fade" id="viewMaterialRequestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-black">
                        <i class="las la-file-alt me-2 text-primary fs-24"></i>
                        Material Request Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1">Request Number</label>
                            <h5 id="modalRefNo" class="fw-bold text-primary mb-0">#MAT-0000</h5>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Status</label>
                            <div id="modalStatusBadge"></div>
                        </div>
                    </div>

                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Requested By</label>
                                    <h6 id="modalRequestedBy" class="fw-bold mb-0">N/A</h6>
                                    <span id="modalDepartment" class="text-muted small">N/A</span>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Date Requested</label>
                                    <h6 id="modalDate" class="fw-bold mb-0">N/A</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="text-muted small text-uppercase fw-bold mb-2 d-block">Request Details</label>
                        <div class="p-3 bg-white border rounded shadow-sm" style="min-height: 100px;">
                            <p id="modalDetails" class="mb-0 text-black" style="white-space: pre-wrap;"></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="text-muted small text-uppercase fw-bold mb-2 d-block">Approved By (Manager)</label>
                            <div class="d-flex align-items-center">
                                <i class="las la-check-circle text-success fs-20 me-2"></i>
                                <div>
                                    <h6 id="modalManager" class="fw-bold mb-0">N/A</h6>
                                    <span id="modalManagerDate" class="text-muted small">N/A</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold mb-2 d-block">Approved By (Director)</label>
                            <div class="d-flex align-items-center">
                                <i class="las la-check-circle text-success fs-20 me-2"></i>
                                <div>
                                    <h6 id="modalDirector" class="fw-bold mb-0">N/A</h6>
                                    <span id="modalDirectorDate" class="text-muted small">N/A</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary shadow-sm" id="modalPrintBtn" onclick="window.print()">
                        <i class="las la-print me-1"></i> Print Details
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.daterange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'MMM DD, YYYY'
                }
            });

            $('.daterange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MMM DD, YYYY') + ' - ' + picker.endDate.format('MMM DD, YYYY'));
            });

            $('.daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            $('#viewMaterialRequestModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                console.log('Modal opening for button:', button);
                
                // Extract info from data attributes
                const reference = button.data('reference');
                const requestedBy = button.data('requested-by');
                const department = button.data('department');
                const date = button.data('date');
                const details = button.data('details');
                const status = button.data('status');
                const statusBadge = button.data('status-badge');
                const manager = button.data('manager');
                const managerDate = button.data('manager-date');
                const director = button.data('director');
                const directorDate = button.data('director-date');
                const amount = button.data('amount');

                // Update modal content
                $('#modalRefNo').text(reference);
                $('#modalRequestedBy').text(requestedBy);
                $('#modalDepartment').text(department);
                $('#modalDate').text(date);
                $('#modalDetails').text(details);
                $('#modalManager').text(manager);
                $('#modalManagerDate').text(managerDate);
                $('#modalDirector').text(director);
                $('#modalDirectorDate').text(directorDate);
                $('#modalAmount').text(amount);
                
                // Update badge
                $('#modalStatusBadge').html(`<span class="badge light badge-${statusBadge}">${status}</span>`);
            });
        });
    </script>
    @endpush
</x-app-layout>
