<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .request-form {
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

        .nav-tabs { border-bottom: 2px solid #e0e0e0; margin-bottom: 2rem; }
        .nav-tabs .nav-link { font-weight: 600; color: #666; border: none; border-bottom: 3px solid transparent; }
        .nav-tabs .nav-link.active { color: #ff0000; border-bottom-color: #ff0000; background: transparent; }

        .form-section { margin-bottom: 1.5rem; }
        .section-title { font-size: 1.1rem; font-weight: 700; color: #333; text-transform: uppercase; margin: 1.5rem 0; text-align: center; }
        .section-box { background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; padding: 1.5rem; margin-bottom: 1rem; }

        .from-to-table { width: 100%; border-collapse: collapse; }
        .from-to-table thead { background: #ff0000; color: #fff; }
        .from-to-table th, .from-to-table td { padding: 0.75rem; border: 1px solid #ddd; }

        @media print {
            .sidebar-wrapper, .header, .nav-tabs, .form-actions { display: none !important; }
            .content-body { margin-left: 0 !important; padding: 0 !important; }
            .request-form { box-shadow: none; }
            .tab-pane { display: block !important; opacity: 1 !important; }
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card request-form">
                <div class="form-header d-flex justify-content-between align-items-center">
                    <div class="document-title mb-0">JOB ORDERS</div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin-finance.mis.job-orders', ['status' => 'all']) }}" class="btn btn-sm btn-{{ $currentStatus == 'all' ? 'primary' : 'outline-primary' }}">All</a>
                        <a href="{{ route('admin-finance.mis.job-orders', ['status' => 'ongoing']) }}" class="btn btn-sm btn-{{ $currentStatus == 'ongoing' ? 'info' : 'outline-info' }}">Ongoing</a>
                        <a href="{{ route('admin-finance.mis.job-orders', ['status' => 'on_hold']) }}" class="btn btn-sm btn-{{ $currentStatus == 'on_hold' ? 'warning' : 'outline-warning' }}">On Hold</a>
                        <a href="{{ route('admin-finance.mis.job-orders', ['status' => 'completed']) }}" class="btn btn-sm btn-{{ $currentStatus == 'completed' ? 'secondary' : 'outline-secondary' }}">Completed</a>
                    </div>
                </div>

                <ul class="nav nav-tabs" id="jobTabs" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#cctv">CCTV Review</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#material">Material Request</a></li>
                    <!-- <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#qb">QB Change</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#undertime">Undertime</a></li> -->
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#service">Service Request</a></li>
                </ul>

                <div class="tab-content">
                    <!-- CCTV Review -->
                    <div class="tab-pane fade show active" id="cctv">
                        <div class="section-title mt-0 text-uppercase">Existing CCTV Requests</div>


                
                        <div class="table-responsive">
                            <table class="table table-hover" id="cctvTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date Requested</th>
                                        <th>Requested By</th>
                                        <th>Department</th>
                                        <th>Incident Details</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cctvRequests as $req)
                                    <tr>
                                        <td>{{ $req->created_at->format('m/d/Y') }}</td>
                                        <td>{{ $req->requested_by }}</td>
                                        <td>{{ $req->department }}</td>
                                        <td>{{ \Carbon\Carbon::parse($req->date_of_incident)->format('m/d/Y') }} {{ \Carbon\Carbon::parse($req->time_of_incident)->format('h:i A') }}</td>
                                        <td>
                                            @php
                                                $jobStatus = $req->status === 'approved' ? 'on_hold' : $req->status;
                                                $statusClass = [
                                                    'Pending HR approval' => 'warning',
                                                    'Pending Final Approval' => 'primary',
                                                    'ongoing' => 'info',
                                                    'on_hold' => 'warning',
                                                    'rejected' => 'danger',
                                                    'pending approval' => 'warning',
                                                    'completed' => 'success',
                                                    'to submit' => 'secondary'
                                                ][$jobStatus] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ ucwords(str_replace('_', ' ', $jobStatus)) }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <button type="button" class="btn btn-info sharp shadow view-cctv text-white" 
                                                    data-id="{{ $req->cctv_req_id }}"
                                                    data-requested_by="{{ $req->requested_by }}"
                                                    data-time_of_incident="{{ $req->time_of_incident }}"
                                                    data-department="{{ $req->department }}"
                                                    data-date_of_incident="{{ $req->date_of_incident }}"
                                                    data-purpose="{{ $req->purpose }}"
                                                    data-hardcopy="{{ $req->hardcopy }}"
                                                    data-viewing="{{ $req->viewing }}"
                                                    data-status="{{ ucwords(str_replace('_', ' ', $jobStatus)) }}"
                                                    title="View Details">
                                                    <i class="las la-eye"></i>
                                                </button>

                                                <button type="button" class="btn btn-warning sharp shadow edit-cctv" 
                                                    {{ $req->status !== 'to submit' ? 'disabled' : '' }}
                                                    data-id="{{ $req->cctv_req_id }}"
                                                    data-requested_by="{{ $req->requested_by }}"
                                                    data-time_of_incident="{{ $req->time_of_incident }}"
                                                    data-department="{{ $req->department }}"
                                                    data-date_of_incident="{{ $req->date_of_incident }}"
                                                    data-purpose="{{ $req->purpose }}"
                                                    data-hardcopy="{{ $req->hardcopy }}"
                                                    data-viewing="{{ $req->viewing }}"
                                                    data-status="{{ $req->status }}"
                                                    title="Edit Request">
                                                    <i class="las la-edit"></i>
                                                </button>

                                                <form action="{{ route('admin-finance.mis.cctv-requests.destroy', $req->cctv_req_id) }}" method="POST" class="delete-form mb-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger sharp shadow delete-btn" 
                                                        {{ $req->status !== 'to submit' ? 'disabled' : '' }}
                                                        title="Delete Request">
                                                        <i class="las la-trash"></i>
                                                    </button>
                                                </form>

                                                @if($req->status === 'to submit')
                                                <form action="{{ route('admin-finance.mis.cctv-requests.update', $req->cctv_req_id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="pending approval">
                                                    <button type="submit" class="btn btn-success sharp shadow px-2" title="Submit Request" style="font-size: 0.7rem;">Submit</button>
                                                </form>
                                                @endif

                                                @if(in_array($jobStatus, ['on_hold', 'ongoing', 'completed']))
                                                <form action="{{ route('admin-finance.mis.job-orders.update-status', ['type' => 'cctv', 'id' => $req->cctv_req_id]) }}" method="POST" class="d-flex align-items-center gap-1 mb-0">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="status" class="form-select form-select-sm" style="width: 120px;">
                                                        <option value="on_hold" {{ $jobStatus === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                                        <option value="ongoing" {{ $jobStatus === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                                        <option value="completed" {{ $jobStatus === 'completed' ? 'selected' : '' }}>Completed</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-primary sharp shadow px-2" title="Update Status" style="font-size: 0.7rem;">Update</button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No requests found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Material Request -->
                    <div class="tab-pane fade" id="material">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="section-title mt-0 text-uppercase mb-0">Existing Material Requests</div>
                            <button type="button" class="btn btn-primary btn-sm text-white" data-bs-toggle="modal" data-bs-target="#createMaterialModal">
                                <i class="las la-plus me-1"></i> New Request
                            </button>
                        </div>


                        
                        <div class="table-responsive">
                            <table class="table table-hover" id="materialTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date Requested</th>
                                        <th>Requested By</th>
                                        <th>Request Details</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($materialRequests as $req)
                                    <tr>
                                        <td>{{ $req->created_at->format('m/d/Y') }}</td>
                                        <td>{{ $req->requested_by }}</td>
                                        <td>{{ (isset($req->request_details) && strlen($req->request_details) > 40) ? substr($req->request_details, 0, 37) . '...' : $req->request_details }}</td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'Pending Final Approval' => 'primary',
                                                    'forwarded to accounting' => 'info',
                                                    'received' => 'success',
                                                    'rejected' => 'danger',
                                                    'pending approval' => 'warning',
                                                    'to submit' => 'secondary'
                                                ][$req->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($req->status) }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <button type="button" class="btn btn-info sharp shadow view-material text-white" 
                                                    data-requested_by="{{ $req->requested_by }}"
                                                    data-request_date="{{ $req->created_at->format('m/d/Y') }}"
                                                    data-request_details="{{ $req->request_details }}"
                                                    data-amount="{{ $req->amount ? 'PhP ' . number_format($req->amount, 2) : '—' }}"
                                                    data-status="{{ ucwords(str_replace('_', ' ', $jobStatus)) }}"
                                                    title="View Details">
                                                    <i class="las la-eye"></i>
                                                </button>

                                                @if($req->status === 'to submit')
                                                <button type="button" class="btn btn-warning sharp shadow edit-material text-white" 
                                                    data-id="{{ $req->material_req_id }}"
                                                    data-requested_by="{{ $req->requested_by }}"
                                                    data-request_date="{{ $req->request_date ?? $req->created_at->format('Y-m-d') }}"
                                                    data-request_details="{{ $req->request_details }}"
                                                    data-amount="{{ $req->amount }}"
                                                    data-status="{{ $req->status }}"
                                                    title="Edit Request">
                                                    <i class="las la-edit"></i>
                                                </button>

                                                <form action="{{ route('admin-finance.mis.material-requests.destroy', $req->material_req_id) }}" method="POST" class="delete-form mb-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger sharp shadow delete-btn" 
                                                        title="Delete Request">
                                                        <i class="las la-trash"></i>
                                                    </button>
                                                </form>
                                                @endif


                                                @if($req->status === 'to submit')
                                                <form action="{{ route('admin-finance.mis.material-requests.update', $req->material_req_id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="forwarded to accounting">
                                                    <button type="submit" class="btn btn-success sharp shadow px-2 text-white" title="Submit Request" style="font-size: 0.7rem;">Submit</button>
                                                </form>
                                                @endif

                                                @if($req->status === 'forwarded to accounting')
                                                <form action="{{ route('admin-finance.mis.material-requests.update', $req->material_req_id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="received">
                                                    <button type="submit" class="btn btn-secondary sharp shadow text-white" title="Mark as Received"><i class="las la-check-double"></i></button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No requests found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- QB Change -->
                    <div class="tab-pane fade" id="qb">
                        <div class="section-title mt-0 text-uppercase">QB Requests</div>


                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Customer/Item</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($qbRequests as $req)
                                    <tr>
                                        <td>{{ $req->created_at->format('m/d/Y') }}</td>
                                        <td>{{ $req->customer_item_name }}</td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'approved' => 'success',
                                                    'on_hold' => 'warning',
                                                    'ongoing' => 'info',
                                                    'rejected' => 'danger',
                                                    'pending' => 'warning',
                                                    'completed' => 'success',
                                                ][$req->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ ucwords(str_replace('_', ' ', $req->status)) }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <button type="button" class="btn btn-info sharp shadow view-qb text-white" 
                                                    data-customer_item_name="{{ $req->customer_item_name }}"
                                                    data-items="{{ json_encode($req->items) }}"
                                                    data-status="{{ ucfirst($req->status) }}"
                                                    title="View Details">
                                                    <i class="las la-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-warning sharp shadow edit-qb"
                                                    {{ in_array($req->status, ['approved', 'rejected']) ? 'disabled' : '' }}
                                                    data-id="{{ $req->qb_req_id }}"
                                                    data-customer_item_name="{{ $req->customer_item_name }}"
                                                    data-items="{{ json_encode($req->items) }}"
                                                    data-status="{{ $req->status }}"
                                                    title="Edit Request">
                                                    <i class="las la-edit"></i>
                                                </button>
                                                <form action="{{ route('admin-finance.mis.qb-requests.destroy', $req->qb_req_id) }}" method="POST" class="delete-form mb-0 d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger sharp shadow delete-btn"
                                                        {{ in_array($req->status, ['approved', 'rejected']) ? 'disabled' : '' }}
                                                        title="Delete Request">
                                                        <i class="las la-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No requests found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Undertime -->
                    <div class="tab-pane fade" id="undertime">
                        <div class="section-title mt-0 text-uppercase">Undertime Requests</div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Employee</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($undertimeRequests as $req)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($req->date)->format('m/d/Y') }}</td>
                                        <td>{{ $req->employee_name }}</td>
                                        <td>{{ Str::limit($req->reason, 30) }}</td>
                                        <td>
                                            @php
                                                $jobStatus = $req->status === 'approved' ? 'on_hold' : $req->status;
                                                $statusClass = [
                                                    'Pending Final Approval' => 'primary',
                                                    'approved' => 'success',
                                                    'on_hold' => 'warning',
                                                    'ongoing' => 'info',
                                                    'rejected' => 'danger',
                                                    'pending' => 'warning',
                                                    'pending approval' => 'warning',
                                                    'completed' => 'success',
                                                ][$jobStatus] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ ucwords(str_replace('_', ' ', $jobStatus)) }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <button type="button" class="btn btn-info sharp shadow view-undertime text-white" 
                                                    data-employee_name="{{ $req->employee_name }}"
                                                    data-date="{{ $req->date }}"
                                                    data-time_from="{{ $req->time_from }}"
                                                    data-time_to="{{ $req->time_to }}"
                                                    data-reason="{{ $req->reason }}"
                                                    data-status="{{ ucfirst($req->status) }}"
                                                    title="View Details">
                                                    <i class="las la-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-warning sharp shadow edit-undertime" 
                                                    {{ in_array($req->status, ['approved', 'rejected']) ? 'disabled' : '' }}
                                                    data-id="{{ $req->undertime_req_id }}"
                                                    data-employee_name="{{ $req->employee_name }}"
                                                    data-date="{{ $req->date }}"
                                                    data-time_from="{{ $req->time_from }}"
                                                    data-time_to="{{ $req->time_to }}"
                                                    data-reason="{{ $req->reason }}"
                                                    data-status="{{ $req->status }}"
                                                    title="Edit Request">
                                                    <i class="las la-edit"></i>
                                                </button>
                                                <form action="{{ route('admin-finance.mis.undertime-requests.destroy', $req->undertime_req_id) }}" method="POST" class="delete-form mb-0">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="btn btn-danger sharp shadow delete-btn"
                                                        {{ in_array($req->status, ['approved', 'rejected']) ? 'disabled' : '' }}
                                                        title="Delete Request">
                                                        <i class="las la-trash"></i>
                                                    </button>
                                                </form>
                                                
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center">No requests found</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Service Request -->
                    <div class="tab-pane fade" id="service">
                        <div class="section-title mt-0 text-uppercase">Existing Service Requests</div>

                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Requestor</th>
                                        <th>Nature of Request</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($serviceRequests as $req)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($req->date)->format('m/d/Y') }}</td>
                                        <td>{{ $req->requestor_name }}</td>
                                        <td>{{ Str::limit($req->nature_of_request, 40) }}</td>
                                        <td>
                                            @php
                                                $jobStatus = $req->status === 'approved' ? 'on_hold' : $req->status;
                                                $statusClass = [
                                                    'Pending Final Approval' => 'primary',
                                                    'on_hold' => 'warning',
                                                    'ongoing' => 'info',
                                                    'rejected' => 'danger',
                                                    'pending' => 'warning',
                                                    'pending approval' => 'warning',
                                                    'completed' => 'success',
                                                ][$jobStatus] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ ucwords(str_replace('_', ' ', $jobStatus)) }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <button type="button" class="btn btn-info sharp shadow view-service text-white" 
                                                    data-requestor_name="{{ $req->requestor_name }}"
                                                    data-date="{{ $req->date }}"
                                                    data-nature_of_request="{{ $req->nature_of_request }}"
                                                    data-status="{{ ucwords(str_replace('_', ' ', $jobStatus)) }}"
                                                    title="View Details">
                                                    <i class="las la-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-warning sharp shadow edit-service" 
                                                    {{ in_array($req->status, ['approved', 'rejected']) ? 'disabled' : '' }}
                                                    data-id="{{ $req->service_req_id }}"
                                                    data-requestor_name="{{ $req->requestor_name }}"
                                                    data-date="{{ $req->date }}"
                                                    data-nature_of_request="{{ $req->nature_of_request }}"
                                                    data-status="{{ $req->status }}"
                                                    title="Edit Request">
                                                    <i class="las la-edit"></i>
                                                </button>
                                                <form action="{{ route('admin-finance.mis.service-requests.destroy', $req->service_req_id) }}" method="POST" class="delete-form mb-0">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="btn btn-danger sharp shadow delete-btn"
                                                        {{ in_array($req->status, ['approved', 'rejected']) ? 'disabled' : '' }}
                                                        title="Delete Request">
                                                        <i class="las la-trash"></i>
                                                    </button>
                                                </form>
                                                @if(in_array($jobStatus, ['on_hold', 'ongoing', 'completed']))
                                                <form action="{{ route('admin-finance.mis.job-orders.update-status', ['type' => 'service', 'id' => $req->service_req_id]) }}" method="POST" class="d-flex align-items-center gap-1 mb-0">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="status" class="form-select form-select-sm" style="width: 120px;">
                                                        <option value="on_hold" {{ $jobStatus === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                                        <option value="ongoing" {{ $jobStatus === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                                        <option value="completed" {{ $jobStatus === 'completed' ? 'selected' : '' }}>Completed</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-primary sharp shadow px-2" title="Update Status" style="font-size: 0.7rem;">Update</button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center">No requests found</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Edit CCTV Modal -->
    <div class="modal fade" id="editCctvModal" tabindex="-1" aria-labelledby="editCctvModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="editCctvForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCctvModalLabel">Edit CCTV Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Requested by (Name):</label>
                                <input type="text" name="requested_by" id="edit_requested_by" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Time of Incident:</label>
                                <input type="time" name="time_of_incident" id="edit_time_of_incident" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Department:</label>
                                <select name="department" id="edit_department" class="form-control" required>
                                    <option value="Admin">Admin</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Production">Production</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date of Incident:</label>
                                <input type="date" name="date_of_incident" id="edit_date_of_incident" class="form-control" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Status:</label>
                                <select name="status" id="edit_status" class="form-control" required>
                                    <option value="to submit">To Submit</option>
                                    <option value="pending approval">Pending Approval</option>
                                    <option value="Pending HR approval">Pending HR approval</option>
                                    <option value="Pending Final Approval">Pending Final Approval</option>
                                    <option value="completed">Completed</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold d-block">Request Type:</label>
                                <div class="d-flex gap-4 p-2 bg-light rounded border">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="hardcopy" id="edit_hardcopy" value="1">
                                        <label class="form-check-label" for="edit_hardcopy">Hardcopy (CD/USB)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="viewing" id="edit_viewing" value="1">
                                        <label class="form-check-label" for="edit_viewing">Viewing Only</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Purpose:</label>
                                <textarea name="purpose" id="edit_purpose" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Attachment:</label>
                                <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <small class="text-muted">Accepted files: PDF, JPG, PNG, DOC, DOCX. Max 5MB.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Material Request Modal -->
    <div class="modal fade" id="editMaterialModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="editMaterialForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title text-white fw-bold"><i class="las la-edit me-2"></i>Edit Material Request</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Requestor's Name:</label>
                                <input type="text" name="requested_by" id="edit_material_requested_by" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date:</label>
                                <input type="date" name="request_date" id="edit_material_request_date" class="form-control" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Amount (PhP):</label>
                                <input type="number" name="amount" id="edit_material_amount" class="form-control" step="0.01" min="0">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Status:</label>
                                <select name="status" id="edit_material_status" class="form-control" required>
                                    <option value="to submit">To Submit</option>
                                    <option value="pending approval">Pending Approval</option>
                                    <option value="Pending Final Approval">Pending Final Approval</option>
                                    <option value="forwarded to accounting">Forwarded to Accounting</option>
                                    <option value="received">Received</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Request Details:</label>
                                <textarea name="request_details" id="edit_material_request_details" class="form-control" rows="5" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Update Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit QB Modal -->
    <div class="modal fade" id="editQbModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="editQbForm" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit QB Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Customer/Item Name:</label>
                            <input type="text" name="customer_item_name" id="edit_qb_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status:</label>
                            <select name="status" id="edit_qb_status" class="form-control" required>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="table-responsive mb-3">
                            <table class="from-to-table" id="edit_qb_table">
                                <thead><tr><th>FROM</th><th>TO</th></tr></thead>
                                <tbody>
                                    @for($i=0; $i<4; $i++)
                                    <tr>
                                        <input type="hidden" name="items[{{$i}}][id]" class="qb-item-id">
                                        <td><input type="text" name="items[{{$i}}][from]" class="form-control border-0 qb-item-from"></td>
                                        <td><input type="text" name="items[{{$i}}][to]" class="form-control border-0 qb-item-to"></td>
                                    </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- View QB Modal -->
    <div class="modal fade" id="viewQbModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light text-dark">
                    <h5 class="modal-title fw-bold">QB Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="fw-bold">Customer/Item Name:</label>
                        <span id="view_qb_name"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Status:</label>
                        <span id="view_qb_status" class="badge"></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr><th>From</th><th>To</th></tr>
                            </thead>
                            <tbody id="view_qb_items_body"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Undertime Modal -->
    <div class="modal fade" id="editUndertimeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="editUndertimeForm" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Undertime Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Employee Name:</label>
                                <input type="text" name="employee_name" id="edit_undertime_employee_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date:</label>
                                <input type="date" name="date" id="edit_undertime_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Time From:</label>
                                <input type="time" name="time_from" id="edit_undertime_time_from" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Time To:</label>
                                <input type="time" name="time_to" id="edit_undertime_time_to" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Status:</label>
                                <select name="status" id="edit_undertime_status" class="form-control" required>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Reason:</label>
                                <textarea name="reason" id="edit_undertime_reason" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Undertime Modal -->
    <div class="modal fade" id="viewUndertimeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light text-dark">
                    <h5 class="modal-title fw-bold">Undertime Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="fw-bold text-muted small">Employee Name</label><p id="view_ut_name" class="fw-bold mb-0"></p></div>
                        <div class="col-md-6"><label class="fw-bold text-muted small">Date</label><p id="view_ut_date" class="fw-bold mb-0"></p></div>
                        <div class="col-md-6"><label class="fw-bold text-muted small">From</label><p id="view_ut_from" class="fw-bold mb-0"></p></div>
                        <div class="col-md-6"><label class="fw-bold text-muted small">To</label><p id="view_ut_to" class="fw-bold mb-0"></p></div>
                        <div class="col-md-12"><label class="fw-bold text-muted small">Status</label><div><span id="view_ut_status" class="badge"></span></div></div>
                        <div class="col-12"><div class="p-3 bg-light rounded border"><label class="fw-bold text-muted small mb-2">Reason</label><p id="view_ut_reason" class="mb-0" style="white-space: pre-wrap;"></p></div></div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div class="modal fade" id="editServiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="editServiceForm" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Service Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">Requestor's Name:</label>
                                <input type="text" name="requestor_name" id="edit_service_requestor_name" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Date:</label>
                                <input type="date" name="date" id="edit_service_date" class="form-control" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Status:</label>
                                <select name="status" id="edit_service_status" class="form-control" required>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Nature of Request:</label>
                                <textarea name="nature_of_request" id="edit_service_nature" class="form-control" rows="5" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Update Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- View CCTV Modal -->
    <div class="modal fade" id="viewCctvModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light text-dark">
                    <h5 class="modal-title fw-bold"><i class="las la-video me-2"></i>CCTV Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Requested By</label>
                            <p id="view_cctv_name" class="fs-5 fw-bold text-dark mb-0"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Department</label>
                            <p id="view_cctv_dept" class="fs-6 text-dark mb-0"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Date of Incident</label>
                            <p id="view_cctv_date" class="fs-6 text-dark mb-0"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Time of Incident</label>
                            <p id="view_cctv_time" class="fs-6 text-dark mb-0"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Request Type</label>
                            <div id="view_cctv_type" class="d-flex gap-2 mt-1"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Status</label>
                            <div><span id="view_cctv_status" class="badge fs-6"></span></div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3 border">
                                <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Purpose</label>
                                <p id="view_cctv_purpose" class="mb-0 text-dark" style="white-space: pre-wrap;"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Service Modal -->
    <div class="modal fade" id="viewServiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light text-dark">
                    <h5 class="modal-title fw-bold"><i class="las la-tools me-2"></i>Service Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Requestor</label>
                            <p id="view_svc_name" class="fs-5 fw-bold text-dark mb-0"></p>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Date</label>
                            <p id="view_svc_date" class="fs-5 fw-bold text-dark mb-0"></p>
                        </div>
                        <div class="col-md-12">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Status</label>
                            <div><span id="view_svc_status" class="badge fs-6"></span></div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3 border">
                                <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Nature of Request</label>
                                <p id="view_svc_nature" class="mb-0 text-dark" style="white-space: pre-wrap;"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <div class="mb-3">
                        <i class="las la-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="mb-2">Success!</h4>
                    <p class="text-muted mb-0" id="successMessage">Operation completed successfully!</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-0">
                    <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal" id="successOkBtn">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <div class="mb-3">
                        <i class="las la-times-circle text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="mb-2">Error</h4>
                    <p class="text-muted mb-0" id="errorMessage">An error occurred.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <div class="mb-3">
                        <i class="las la-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="mb-2">Are you sure?</h4>
                    <p class="text-muted mb-0">You are about to delete this request. This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-3">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Trigger Modals for Session Flashes
            @if(session('success'))
                document.getElementById('successMessage').textContent = "{{ session('success') }}";
                new bootstrap.Modal(document.getElementById('successModal')).show();
            @endif

            @if(session('error'))
                document.getElementById('errorMessage').textContent = "{{ session('error') }}";
                new bootstrap.Modal(document.getElementById('errorModal')).show();
            @endif

            @if($errors->any())
                document.getElementById('errorMessage').textContent = "{{ $errors->first() }}";
                new bootstrap.Modal(document.getElementById('errorModal')).show();
            @endif

            // Delete Confirmation Logic
            let formToSubmit = null;
            const deleteConfirmModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));

            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    formToSubmit = this.closest('.delete-form');
                    deleteConfirmModal.show();
                });
            });

            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                if (formToSubmit) {
                    formToSubmit.submit();
                }
            });

            // View CCTV Logic
            const viewCctvModalEl = document.getElementById('viewCctvModal');
            if (viewCctvModalEl) {
                const viewModal = new bootstrap.Modal(viewCctvModalEl);
                document.querySelectorAll('.view-cctv').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const data = this.dataset;
                        document.getElementById('view_cctv_name').textContent = data.requested_by;
                        document.getElementById('view_cctv_dept').textContent = data.department;
                        document.getElementById('view_cctv_date').textContent = data.date_of_incident;
                        document.getElementById('view_cctv_time').textContent = data.time_of_incident;
                        document.getElementById('view_cctv_purpose').textContent = data.purpose;
                        
                        const statusBadge = document.getElementById('view_cctv_status');
                        statusBadge.textContent = data.status;
                        statusBadge.className = 'badge fs-6 ' + (data.status.toLowerCase() === 'completed' ? 'bg-success' : 'bg-warning text-white');

                        const typeContainer = document.getElementById('view_cctv_type');
                        typeContainer.innerHTML = '';
                        if (data.hardcopy === "1") {
                            typeContainer.innerHTML += '<span class="badge bg-primary">Hardcopy</span> ';
                        }
                        if (data.viewing === "1") {
                            typeContainer.innerHTML += '<span class="badge bg-secondary">Viewing Only</span>';
                        }
                        if (typeContainer.innerHTML === '') {
                            typeContainer.innerHTML = '<span class="text-muted small italic">None specified</span>';
                        }
                        
                        viewModal.show();
                    });
                });
            }

            // Edit CCTV Logic
            const editModalEl = document.getElementById('editCctvModal');
            const editForm = document.getElementById('editCctvForm');

            if (editModalEl && editForm) {
                const editModal = new bootstrap.Modal(editModalEl);

                document.querySelectorAll('.edit-cctv').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const data = this.dataset;
                        console.log('Editing CCTV Request:', data);
                        
                        // Set Form Action - using relative path to avoid root issues
                        editForm.action = `cctv-requests/${data.id}`;
                        
                        // Populate Fields
                        if(document.getElementById('edit_requested_by')) document.getElementById('edit_requested_by').value = data.requested_by;
                        if(document.getElementById('edit_time_of_incident')) document.getElementById('edit_time_of_incident').value = data.time_of_incident;
                        if(document.getElementById('edit_department')) document.getElementById('edit_department').value = data.department;
                        if(document.getElementById('edit_date_of_incident')) document.getElementById('edit_date_of_incident').value = data.date_of_incident;
                        if(document.getElementById('edit_status')) document.getElementById('edit_status').value = data.status;
                        if(document.getElementById('edit_purpose')) document.getElementById('edit_purpose').value = data.purpose;
                        
                        // Populate Checkboxes
                        if(document.getElementById('edit_hardcopy')) document.getElementById('edit_hardcopy').checked = (data.hardcopy === "1");
                        if(document.getElementById('edit_viewing')) document.getElementById('edit_viewing').checked = (data.viewing === "1");
                        
                        editModal.show();
                    });
                });
            } else {
                console.error('Edit Modal or Form not found');
            }

            // Edit Material Logic
            const editMaterialModalEl = document.getElementById('editMaterialModal');
            const editMaterialForm = document.getElementById('editMaterialForm');

            if (editMaterialModalEl && editMaterialForm) {
                const editMaterialModal = new bootstrap.Modal(editMaterialModalEl);

                document.querySelectorAll('.edit-material').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const data = this.dataset;
                        console.log('Editing Material Request:', data);
                        
                        editMaterialForm.action = `{{ route('admin-finance.mis.material-requests.update', '') }}/${data.id}`;
                        
                        if(document.getElementById('edit_material_requested_by')) document.getElementById('edit_material_requested_by').value = data.requested_by;
                        if(document.getElementById('edit_material_request_date')) document.getElementById('edit_material_request_date').value = data.request_date;
                        if(document.getElementById('edit_material_status')) document.getElementById('edit_material_status').value = data.status;
                        if(document.getElementById('edit_material_amount')) document.getElementById('edit_material_amount').value = data.amount;
                        if(document.getElementById('edit_material_request_details')) document.getElementById('edit_material_request_details').value = data.request_details;
                        
                        editMaterialModal.show();
                    });
                });
            }
            
            // View QB Logic
            const viewQbModalEl = document.getElementById('viewQbModal');
            if (viewQbModalEl) {
                const viewQbModal = new bootstrap.Modal(viewQbModalEl);
                document.querySelectorAll('.view-qb').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const data = this.dataset;
                        document.getElementById('view_qb_name').textContent = data.customer_item_name;
                        
                        const statusBadge = document.getElementById('view_qb_status');
                        statusBadge.textContent = data.status;
                        statusBadge.className = 'badge ' + (data.status.toLowerCase() === 'approved' ? 'bg-success' : 'bg-secondary');

                        const tbody = document.getElementById('view_qb_items_body');
                        tbody.innerHTML = '';
                        try {
                            const items = JSON.parse(data.items);
                            items.forEach(item => {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `<td>${item.from_value || '-'}</td><td>${item.to_value || '-'}</td>`;
                                tbody.appendChild(tr);
                            });
                        } catch(e) { console.error(e); }
                        
                        viewQbModal.show();
                    });
                });
            }

            // Edit QB Logic
            const editQbModalEl = document.getElementById('editQbModal');
            const editQbForm = document.getElementById('editQbForm');
            if (editQbModalEl && editQbForm) {
                const editModal = new bootstrap.Modal(editQbModalEl);
                document.querySelectorAll('.edit-qb').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const data = this.dataset;
                        console.log('Editing QB Request:', data);
                        editQbForm.action = `qb-requests/${data.id}`;
                        document.getElementById('edit_qb_name').value = data.customer_item_name;
                        document.getElementById('edit_qb_status').value = data.status;

                        // Clear inputs first
                        const table = document.getElementById('edit_qb_table');
                        table.querySelectorAll('input').forEach(i => i.value = '');

                        try {
                            const items = JSON.parse(data.items);
                            items.forEach((item, index) => {
                                if (index < 4) {
                                  const row = table.rows[index + 1]; // +1 for thead
                                  if (row) {
                                      row.querySelector('.qb-item-id').value = item.id;
                                      row.querySelector('.qb-item-from').value = item.from_value;
                                      row.querySelector('.qb-item-to').value = item.to_value;
                                  }
                                }
                            });
                        } catch(e) { console.error(e); }
                        editModal.show();
                    });
                });
            }
            
            // Edit Undertime Logic
            const editUndertimeModalEl = document.getElementById('editUndertimeModal');
            const editUndertimeForm = document.getElementById('editUndertimeForm');
            if (editUndertimeModalEl) {
                const editModal = new bootstrap.Modal(editUndertimeModalEl);
                document.querySelectorAll('.edit-undertime').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const data = this.dataset;
                        editUndertimeForm.action = `undertime-requests/${data.id}`;
                        document.getElementById('edit_undertime_employee_name').value = data.employee_name;
                        document.getElementById('edit_undertime_date').value = data.date;
                        document.getElementById('edit_undertime_time_from').value = data.time_from;
                        document.getElementById('edit_undertime_time_to').value = data.time_to;
                        document.getElementById('edit_undertime_reason').value = data.reason;
                        document.getElementById('edit_undertime_status').value = data.status;
                        editModal.show();
                    });
                });
            }

            // View Undertime Logic
            const viewUndertimeModalEl = document.getElementById('viewUndertimeModal');
            if (viewUndertimeModalEl) {
                const viewModal = new bootstrap.Modal(viewUndertimeModalEl);
                document.querySelectorAll('.view-undertime').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const data = this.dataset;
                        document.getElementById('view_ut_name').textContent = data.employee_name;
                        document.getElementById('view_ut_date').textContent = data.date;
                        document.getElementById('view_ut_from').textContent = data.time_from;
                        document.getElementById('view_ut_to').textContent = data.time_to;
                        document.getElementById('view_ut_reason').textContent = data.reason;
                        
                        const statusBadge = document.getElementById('view_ut_status');
                        statusBadge.textContent = data.status;
                        statusBadge.className = 'badge ' + (data.status.toLowerCase() == 'approved' ? 'bg-success' : 'bg-secondary');
                        
                        viewModal.show();
                    });
                });
            }

            // Edit Service Logic
            const editServiceModalEl = document.getElementById('editServiceModal');
            const editServiceForm = document.getElementById('editServiceForm');
            if (editServiceModalEl) {
                const editModal = new bootstrap.Modal(editServiceModalEl);
                document.querySelectorAll('.edit-service').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const data = this.dataset;
                        editServiceForm.action = `service-requests/${data.id}`;
                        document.getElementById('edit_service_requestor_name').value = data.requestor_name;
                        document.getElementById('edit_service_date').value = data.date;
                        document.getElementById('edit_service_nature').value = data.nature_of_request;
                        document.getElementById('edit_service_status').value = data.status;
                        editModal.show();
                    });
                });
            }

            // View Service Logic
            const viewServiceModalEl = document.getElementById('viewServiceModal');
            if (viewServiceModalEl) {
                const viewModal = new bootstrap.Modal(viewServiceModalEl);
                document.querySelectorAll('.view-service').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const data = this.dataset;
                        document.getElementById('view_svc_name').textContent = data.requestor_name;
                        document.getElementById('view_svc_date').textContent = data.date;
                        document.getElementById('view_svc_nature').textContent = data.nature_of_request;
                        
                        const statusBadge = document.getElementById('view_svc_status');
                        statusBadge.textContent = data.status;
                        statusBadge.className = 'badge ' + (data.status.toLowerCase() == 'approved' ? 'bg-success' : 'bg-secondary');
                        
                        viewModal.show();
                    });
                });
            }


            // View Material Logic
            const viewMaterialModalEl = document.getElementById('viewMaterialModal');
            if (viewMaterialModalEl) {
                const viewMaterialModal = new bootstrap.Modal(viewMaterialModalEl);

                document.querySelectorAll('.view-material').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const data = this.dataset;
                        
                        document.getElementById('view_material_by').textContent = data.requested_by;
                        document.getElementById('view_material_date').textContent = data.request_date;
                        document.getElementById('view_material_details').textContent = data.request_details;
                        if (document.getElementById('view_material_amount')) {
                            document.getElementById('view_material_amount').textContent = data.amount || '—';
                        }
                        
                        const statusBadge = document.getElementById('view_material_status');
                        statusBadge.textContent = data.status;
                        statusBadge.className = 'badge fs-6 ' + (data.status.toLowerCase() === 'received' ? 'bg-success' : 'bg-warning text-white');
                        
                        viewMaterialModal.show();
                    });
                });
            }
        });
    </script>
    @endpush
    <!-- View Material Modal -->
    <div class="modal fade" id="viewMaterialModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light text-dark">
                    <h5 class="modal-title fw-bold"><i class="las la-receipt me-2"></i>Material Request Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Requested By</label>
                            <p id="view_material_by" class="fs-5 fw-bold text-dark mb-0"></p>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Date Requested</label>
                            <p id="view_material_date" class="fs-5 fw-bold text-dark mb-0"></p>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Amount Requested</label>
                            <p id="view_material_amount" class="fs-5 fw-bold text-primary mb-0"></p>
                        </div>
                        <div class="col-md-12">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Status</label>
                            <div>
                                <span id="view_material_status" class="badge bg-secondary fs-6"></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3 border">
                                <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Request Details</label>
                                <div id="view_material_details" class="white-space-pre-wrap text-dark" style="white-space: pre-wrap;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Create Material Request Modal -->
    <div class="modal fade" id="createMaterialModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin-finance.mis.material-requests.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white fw-bold"><i class="las la-plus me-2"></i>New MIS Material Request</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Requestor's Name:</label>
                                <input type="text" name="requested_by" class="form-control" value="{{ auth()->user()->name }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date:</label>
                                <input type="date" name="request_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Amount (PhP):</label>
                                <input type="number" name="amount" class="form-control" placeholder="0.00" step="0.01" min="0">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Request Details:</label>
                                <textarea name="request_details" class="form-control" rows="5" placeholder="Enter materials needed..." required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
