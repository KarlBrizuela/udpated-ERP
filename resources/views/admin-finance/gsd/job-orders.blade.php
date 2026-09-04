<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .nav-tabs { border-bottom: 2px solid #e0e0e0; margin-bottom: 2rem; }
        .nav-tabs .nav-link { font-weight: 600; color: #666; border: none; border-bottom: 3px solid transparent; }
        .nav-tabs .nav-link.active { color: #ff0000; border-bottom-color: #ff0000; background: transparent; }

        .section-title { font-size: 1.1rem; font-weight: 700; color: #333; text-transform: uppercase; margin: 1.5rem 0; text-align: center; }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                    <div class="document-title mb-0" style="font-size: 1.75rem; font-weight: 700; color: #333;">GSD JOB ORDERS</div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'all']) }}" class="btn btn-sm btn-{{ $currentStatus == 'all' ? 'primary' : 'outline-primary' }}">All</a>
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'approved']) }}" class="btn btn-sm btn-{{ $currentStatus == 'approved' ? 'success' : 'outline-success' }}">Approved</a>
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'ongoing']) }}" class="btn btn-sm btn-{{ $currentStatus == 'ongoing' ? 'info' : 'outline-info' }}">Ongoing</a>
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'on_hold']) }}" class="btn btn-sm btn-{{ $currentStatus == 'on_hold' ? 'warning' : 'outline-warning' }}">On Hold</a>
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'completed']) }}" class="btn btn-sm btn-{{ $currentStatus == 'completed' ? 'secondary' : 'outline-secondary' }}">Completed</a>
                    </div>
                </div>

                <div class="card-body">
                    <ul class="nav nav-tabs" id="jobTabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#material">Material Request</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#service">Service Request</a></li>
                    </ul>

                    <div class="tab-content">
                        <!-- Material Request Tab -->
                        <div class="tab-pane fade show active" id="material">
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
                                            <th>Reference</th>
                                            <th>Requested By</th>
                                            <th>Date Requested</th>
                                            <th>Request Details</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($materialRequests as $req)
                                        <tr>
                                            <td><strong>MAT-{{ str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                            <td>{{ $req->requested_by }}</td>
                                            <td>{{ $req->created_at->format('m/d/Y') }}</td>
                                            <td>{{ (isset($req->request_details) && strlen($req->request_details) > 40) ? substr($req->request_details, 0, 37) . '...' : $req->request_details }}</td>
                                            <td>
                                                @php
                                                    $statusClass = [
                                                        'Pending Final Approval' => 'primary',
                                                        'forwarded to accounting' => 'info',
                                                        'received' => 'success',
                                                        'rejected' => 'danger',
                                                        'pending approval' => 'warning',
                                                        'to submit' => 'secondary',
                                                        'ongoing' => 'info',
                                                        'on_hold' => 'warning',
                                                        'completed' => 'success'
                                                    ][$req->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $req->status)) }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-1">
                                                    <button type="button" class="btn btn-info sharp shadow view-material text-white" 
                                                        data-requested_by="{{ $req->requested_by }}"
                                                        data-request_date="{{ $req->created_at->format('m/d/Y') }}"
                                                        data-request_details="{{ $req->request_details }}"
                                                        data-amount="{{ $req->amount ? 'PhP ' . number_format($req->amount, 2) : '—' }}"
                                                        data-status="{{ ucfirst(str_replace('_', ' ', $req->status)) }}"
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

                                                    <form action="{{ route('admin-finance.gsd.material-requests.destroy', $req->material_req_id) }}" method="POST" class="delete-form mb-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger sharp shadow delete-btn" 
                                                            title="Delete Request">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('admin-finance.gsd.material-requests.submit', $req->material_req_id) }}" method="POST" class="d-inline mb-0">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success sharp shadow px-2 text-white" title="Submit Request" style="font-size: 0.7rem;">Submit</button>
                                                    </form>
                                                    @endif

                                                    @if($req->status === 'forwarded to accounting')
                                                    <form action="{{ route('admin-finance.gsd.material-requests.update', $req->material_req_id) }}" method="POST" class="d-inline mb-0">
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
                                            <td colspan="6" class="text-center">No requests found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Service Request Tab -->
                        <div class="tab-pane fade" id="service">
                            <div class="section-title mt-0 text-uppercase">Existing Service Requests</div>

                            <div class="table-responsive">
                                <table class="table table-hover" id="serviceTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Reference</th>
                                            <th>Requestor</th>
                                            <th>Date</th>
                                            <th>Nature of Request</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($serviceRequests as $req)
                                        <tr>
                                            <td><strong>SRV-{{ str_pad($req->service_req_id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                            <td>{{ $req->requestor_name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($req->date)->format('m/d/Y') }}</td>
                                            <td>{{ Str::limit($req->nature_of_request, 40) }}</td>
                                            <td>
                                                @php
                                                    $statusClass = [
                                                        'approved' => 'success',
                                                        'rejected' => 'danger',
                                                        'pending' => 'warning',
                                                        'completed' => 'info',
                                                        'ongoing' => 'info',
                                                        'on_hold' => 'warning'
                                                    ][$req->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $req->status)) }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-1">
                                                    <button type="button" class="btn btn-info sharp shadow view-service text-white" 
                                                        data-requestor_name="{{ $req->requestor_name }}"
                                                        data-date="{{ $req->date }}"
                                                        data-nature_of_request="{{ $req->nature_of_request }}"
                                                        data-status="{{ ucfirst(str_replace('_', ' ', $req->status)) }}"
                                                        title="View Details">
                                                        <i class="las la-eye"></i>
                                                    </button>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <div id="view_material_details" class="text-dark" style="white-space: pre-wrap;"></div>
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

    <!-- Create Material Request Modal -->
    <div class="modal fade" id="createMaterialModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin-finance.gsd.material-requests.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white fw-bold"><i class="las la-plus me-2"></i>New GSD Material Request</h5>
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
                    <h4 class="mb-2 fw-bold text-dark">Success!</h4>
                    <p class="text-muted mb-0" id="successMessage">Operation completed successfully!</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
                    <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">OK</button>
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
                    <h4 class="mb-2 fw-bold text-dark">Error</h4>
                    <p class="text-muted mb-0" id="errorMessage">An error occurred.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
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
                    <h4 class="mb-2 fw-bold text-dark">Are you sure?</h4>
                    <p class="text-muted mb-0">You are about to delete this request. This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-3 pb-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-4 text-white" id="confirmDeleteBtn">Yes, Delete</button>
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
                        statusBadge.className = 'badge fs-6 ' + (data.status.toLowerCase() === 'received' ? 'bg-success' : (data.status.toLowerCase() === 'completed' ? 'bg-success' : 'bg-warning text-white'));
                        
                        viewMaterialModal.show();
                    });
                });
            }

            // Edit Material Logic
            const editMaterialModalEl = document.getElementById('editMaterialModal');
            const editMaterialForm = document.getElementById('editMaterialForm');

            if (editMaterialModalEl && editMaterialForm) {
                const editMaterialModal = new bootstrap.Modal(editMaterialModalEl);

                document.querySelectorAll('.edit-material').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const data = this.dataset;
                        
                        editMaterialForm.action = `{{ route('admin-finance.gsd.material-requests.update', '') }}/${data.id}`;
                        
                        if(document.getElementById('edit_material_requested_by')) document.getElementById('edit_material_requested_by').value = data.requested_by;
                        if(document.getElementById('edit_material_request_date')) document.getElementById('edit_material_request_date').value = data.request_date;
                        if(document.getElementById('edit_material_amount')) document.getElementById('edit_material_amount').value = data.amount;
                        if(document.getElementById('edit_material_request_details')) document.getElementById('edit_material_request_details').value = data.request_details;
                        
                        editMaterialModal.show();
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
        });
    </script>
    @endpush
</x-app-layout>
