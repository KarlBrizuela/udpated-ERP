<x-app-layout :title="'GSD Job Orders'" :role="'Finance Manager'" :sidebar="'admin-finance'">
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

        .document-title {
            text-align: center; font-size: 1.75rem; font-weight: 700;
            color: #333; margin-top: 1rem; text-transform: uppercase;
        }

        .nav-tabs { border-bottom: 2px solid #e0e0e0; margin-bottom: 2rem; }
        .nav-tabs .nav-link { font-weight: 600; color: #666; border: none; border-bottom: 3px solid transparent; }
        .nav-tabs .nav-link.active { color: #ff0000; border-bottom-color: #ff0000; background: transparent; }

        .section-title { font-size: 1.1rem; font-weight: 700; color: #333; text-transform: uppercase; margin: 1.5rem 0; text-align: center; }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card request-form">
                <div class="form-header d-flex justify-content-between align-items-center">
                    <div class="document-title mb-0">GSD JOB ORDERS</div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'all']) }}" class="btn btn-sm btn-{{ $currentStatus == 'all' ? 'primary' : 'outline-primary' }}">All</a>
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'approved']) }}" class="btn btn-sm btn-{{ $currentStatus == 'approved' ? 'success' : 'outline-success' }}">Approved</a>
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'ongoing']) }}" class="btn btn-sm btn-{{ $currentStatus == 'ongoing' ? 'info' : 'outline-info' }}">Ongoing</a>
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'on_hold']) }}" class="btn btn-sm btn-{{ $currentStatus == 'on_hold' ? 'warning' : 'outline-warning' }}">On Hold</a>
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'completed']) }}" class="btn btn-sm btn-{{ $currentStatus == 'completed' ? 'secondary' : 'outline-secondary' }}">Completed</a>
                    </div>
                </div>

                <ul class="nav nav-tabs" id="jobTabs" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#material">Material Request</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#service">Service Request</a></li>
                </ul>

                <div class="tab-content">
                        <!-- Material Request -->
                        <div class="tab-pane fade show active" id="material">
                        <div class="section-title mt-0 text-uppercase">Existing Material Requests</div>

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
                                                    data-status="{{ ucfirst($req->status) }}"
                                                    title="View Details">
                                                    <i class="las la-eye"></i>
                                                </button>

                                                @if($req->status === 'to submit')
                                                <button type="button" class="btn btn-warning sharp shadow edit-material" 
                                                    data-id="{{ $req->material_req_id }}"
                                                    data-requested_by="{{ $req->requested_by }}"
                                                    data-request_date="{{ $req->request_date }}"
                                                    data-request_details="{{ $req->request_details }}"
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
                                                    <input type="hidden" name="status" value="pending approval">
                                                    <button type="submit" class="btn btn-success sharp shadow px-2" title="Submit Request" style="font-size: 0.7rem;">Submit</button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No requests found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                        <!-- Service Request -->
                        <div class="tab-pane fade" id="service">
                        <div class="section-title mt-0 text-uppercase">Existing Service Requests</div>

                        <div class="table-responsive">
                            <table class="table table-hover" id="serviceTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date Requested</th>
                                        <th>Requestor</th>
                                        <th>Nature of Request</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($serviceRequests as $req)
                                    <tr>
                                        <td>{{ $req->created_at->format('m/d/Y') }}</td>
                                        <td>{{ $req->requestor_name }}</td>
                                        <td>{{ (isset($req->nature_of_request) && strlen($req->nature_of_request) > 40) ? substr($req->nature_of_request, 0, 37) . '...' : $req->nature_of_request }}</td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'approved' => 'success',
                                                    'ongoing' => 'info',
                                                    'on_hold' => 'warning',
                                                    'completed' => 'success',
                                                    'pending' => 'secondary'
                                                ][$req->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($req->status) }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <button type="button" class="btn btn-info sharp shadow view-service text-white" 
                                                    data-requestor_name="{{ $req->requestor_name }}"
                                                    data-date="{{ $req->created_at->format('m/d/Y') }}"
                                                    data-nature="{{ $req->nature_of_request }}"
                                                    data-status="{{ ucfirst($req->status) }}"
                                                    title="View Details">
                                                    <i class="las la-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No requests found</td>
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
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Requested By</label>
                            <p id="view_material_by" class="fs-5 fw-bold text-dark mb-0"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Date Requested</label>
                            <p id="view_material_date" class="fs-5 fw-bold text-dark mb-0"></p>
                        </div>
                        <div class="col-md-12">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Status</label>
                            <p><span id="view_material_status" class="badge"></span></p>
                        </div>
                        <div class="col-md-12">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Request Details</label>
                            <p id="view_material_details" class="text-dark mb-0 pre-wrap" style="white-space: pre-wrap;"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Service Modal -->
    <div class="modal fade" id="viewServiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light text-dark">
                    <h5 class="modal-title fw-bold"><i class="las la-tools me-2"></i>Service Request Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Requestor</label>
                            <p id="view_service_requestor" class="fs-5 fw-bold text-dark mb-0"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Date Requested</label>
                            <p id="view_service_date" class="fs-5 fw-bold text-dark mb-0"></p>
                        </div>
                        <div class="col-md-12">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Status</label>
                            <p><span id="view_service_status" class="badge"></span></p>
                        </div>
                        <div class="col-md-12">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Nature of Request</label>
                            <p id="view_service_nature" class="text-dark mb-0 pre-wrap" style="white-space: pre-wrap;"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#materialTable').DataTable({
                order: [[0, 'desc']]
            });
            $('#serviceTable').DataTable({
                order: [[0, 'desc']]
            });

            // View Material Modal
            const viewMaterialModalEl = document.getElementById('viewMaterialModal');
            if (viewMaterialModalEl) {
                const viewMaterialModal = new bootstrap.Modal(viewMaterialModalEl);
                document.querySelectorAll('.view-material').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const data = this.dataset;
                        document.getElementById('view_material_by').textContent = data.requested_by;
                        document.getElementById('view_material_date').textContent = data.request_date;
                        document.getElementById('view_material_details').textContent = data.request_details;
                        
                        const statusBadge = document.getElementById('view_material_status');
                        statusBadge.textContent = data.status;
                        statusBadge.className = 'badge bg-primary';
                        
                        viewMaterialModal.show();
                    });
                });
            }

            // View Service Modal
            const viewServiceModalEl = document.getElementById('viewServiceModal');
            if (viewServiceModalEl) {
                const viewServiceModal = new bootstrap.Modal(viewServiceModalEl);
                document.querySelectorAll('.view-service').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const data = this.dataset;
                        document.getElementById('view_service_requestor').textContent = data.requestor_name;
                        document.getElementById('view_service_date').textContent = data.date;
                        document.getElementById('view_service_nature').textContent = data.nature;
                        
                        const statusBadge = document.getElementById('view_service_status');
                        statusBadge.textContent = data.status;
                        statusBadge.className = 'badge bg-success';
                        
                        viewServiceModal.show();
                    });
                });
            }

            // Delete confirmation
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Are you sure you want to delete this request?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
