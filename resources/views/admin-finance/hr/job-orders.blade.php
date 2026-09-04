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

        .document-title {
            text-align: center; font-size: 1.75rem; font-weight: 700;
            color: #333; margin-top: 1rem; text-transform: uppercase;
        }

        .nav-tabs { border-bottom: 2px solid #e0e0e0; margin-bottom: 2rem; }
        .nav-tabs .nav-link { font-weight: 600; color: #666; border: none; border-bottom: 3px solid transparent; }
        .nav-tabs .nav-link.active { color: #ff0000; border-bottom-color: #ff0000; background: transparent; }

        .section-title { font-size: 1.1rem; font-weight: 700; color: #333; text-transform: uppercase; margin: 1.5rem 0; text-align: center; }
        
        /* Modal Styles */
        .modal-header { background-color: #f8f9fa; border-bottom: 1px solid #dee2e6; }
        .modal-footer { background-color: #f8f9fa; border-top: 1px solid #dee2e6; }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card request-form">
                <div class="form-header d-flex justify-content-between align-items-center">
                    <div class="document-title mb-0">HR JOB ORDER</div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin-finance.hr.job-orders', ['status' => 'all']) }}" class="btn btn-sm btn-{{ $currentStatus == 'all' ? 'primary' : 'outline-primary' }}">All</a>
                        <a href="{{ route('admin-finance.hr.job-orders', ['status' => 'approved']) }}" class="btn btn-sm btn-{{ $currentStatus == 'approved' ? 'success' : 'outline-success' }}">Approved</a>
                        <a href="{{ route('admin-finance.hr.job-orders', ['status' => 'ongoing']) }}" class="btn btn-sm btn-{{ $currentStatus == 'ongoing' ? 'info' : 'outline-info' }}">Ongoing</a>
                        <a href="{{ route('admin-finance.hr.job-orders', ['status' => 'on_hold']) }}" class="btn btn-sm btn-{{ $currentStatus == 'on_hold' ? 'warning' : 'outline-warning' }}">On Hold</a>
                        <a href="{{ route('admin-finance.hr.job-orders', ['status' => 'completed']) }}" class="btn btn-sm btn-{{ $currentStatus == 'completed' ? 'secondary' : 'outline-secondary' }}">Completed</a>
                    </div>
                </div>

                <ul class="nav nav-tabs" id="jobTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#cctv">CCTV Review</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#service">Service Request</a>
                    </li>
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
                                        <td><span class="badge bg-warning text-dark">{{ ucfirst($req->status) }}</span></td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm shadow review-btn" 
                                                data-id="{{ $req->cctv_req_id }}"
                                                data-requested_by="{{ $req->requested_by }}"
                                                data-time_of_incident="{{ $req->time_of_incident }}"
                                                data-department="{{ $req->department }}"
                                                data-date_of_incident="{{ $req->date_of_incident }}"
                                                data-purpose="{{ $req->purpose }}"
                                                data-hardcopy="{{ $req->hardcopy }}"
                                                data-viewing="{{ $req->viewing }}"
                                                data-status="{{ $req->status }}"
                                                title="Review Request">
                                                <i class="las la-clipboard-check me-1"></i> Review
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="las la-check-circle text-success mb-2" style="font-size: 3rem;"></i>
                                                <h5 class="text-muted">No pending approvals</h5>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Service Request -->
                    <div class="tab-pane fade" id="service">
                        <div class="section-title mt-0 text-uppercase">HR Service Requests</div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Reference</th>
                                        <th>Requestor</th>
                                        <th>Date</th>
                                        <th>Nature of Request</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($serviceRequests as $req)
                                    <tr>
                                        <td><strong>SRV-{{ str_pad($req->service_req_id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                        <td>{{ $req->requestor_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($req->date)->format('m/d/Y') }}</td>
                                        <td>{{ Str::limit($req->nature_of_request, 60) }}</td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'to submit' => 'secondary',
                                                    'pending approval' => 'warning',
                                                    'Pending HR approval' => 'info',
                                                    'Pending Final Approval' => 'primary',
                                                    'approved' => 'success',
                                                    'ongoing' => 'info',
                                                    'on_hold' => 'warning',
                                                    'completed' => 'success',
                                                    'rejected' => 'danger',
                                                ][$req->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $req->status)) }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">No HR service requests found.</td>
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

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="las la-clipboard-check me-2"></i>Review CCTV Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Requested By</label>
                            <p id="review_requested_by" class="fs-5 fw-bold text-dark mb-0"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Department</label>
                            <p id="review_department" class="fs-6 text-dark mb-0"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Date of Incident</label>
                            <p id="review_date" class="fs-6 text-dark mb-0"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Time of Incident</label>
                            <p id="review_time" class="fs-6 text-dark mb-0"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Request Type</label>
                            <div id="review_type" class="d-flex gap-2 mt-1"></div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3 border">
                                <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Purpose</label>
                                <p id="review_purpose" class="mb-0 text-dark" style="white-space: pre-wrap;"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <div class="d-flex gap-2">
                        <form id="rejectForm" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-danger">
                                <i class="las la-times-circle me-1"></i> Reject
                            </button>
                        </form>
                        
                        <form id="approveForm" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="Pending Final Approval">
                            <button type="submit" class="btn btn-success">
                                <i class="las la-check-circle me-1"></i> Approve Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center pt-4">
                    <div class="mb-3">
                        <i class="las la-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="mb-2">Success!</h4>
                    <p class="text-muted mb-4" id="successMessage">Operation completed successfully!</p>
                    <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Success Message Logic
            @if(session('success'))
                document.getElementById('successMessage').textContent = "{{ session('success') }}";
                new bootstrap.Modal(document.getElementById('successModal')).show();
            @endif

            // Review Modal Logic
            const reviewModalEl = document.getElementById('reviewModal');
            if (reviewModalEl) {
                const reviewModal = new bootstrap.Modal(reviewModalEl);
                
                document.querySelectorAll('.review-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const data = this.dataset;
                        
                        // Populate Data
                        document.getElementById('review_requested_by').textContent = data.requested_by;
                        document.getElementById('review_department').textContent = data.department;
                        document.getElementById('review_date').textContent = data.date_of_incident;
                        document.getElementById('review_time').textContent = data.time_of_incident;
                        document.getElementById('review_purpose').textContent = data.purpose;

                        const typeContainer = document.getElementById('review_type');
                        typeContainer.innerHTML = '';
                        if (data.hardcopy === "1") typeContainer.innerHTML += '<span class="badge bg-primary">Hardcopy</span> ';
                        if (data.viewing === "1") typeContainer.innerHTML += '<span class="badge bg-secondary">Viewing Only</span>';
                        if (typeContainer.innerHTML === '') typeContainer.innerHTML = '<span class="text-muted small italic">None specified</span>';

                        // Set Form Actions - Use absolute paths to MIS controller
                        const baseUrl = `/admin-finance/mis/cctv-requests/${data.id}`;
                        document.getElementById('approveForm').action = baseUrl;
                        document.getElementById('rejectForm').action = baseUrl;

                        reviewModal.show();
                    });
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
