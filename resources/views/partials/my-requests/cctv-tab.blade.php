@php
    $cctvDepartment = $cctvDepartment ?? auth()->user()->division ?? '';
    $allowedDepartments = ['Admin', 'Marketing', 'Production'];
    if (!in_array($cctvDepartment, $allowedDepartments, true)) {
        $position = auth()->user()->position ?? '';
        $cctvDepartment = str_contains($position, 'Marketing') ? 'Marketing' : (str_contains($position, 'Production') ? 'Production' : 'Admin');
    }
@endphp

<div class="tab-pane fade" id="cctv-requests" role="tabpanel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">CCTV Requests</h5>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createCctvModal">
            <i class="las la-plus me-1"></i> New CCTV Request
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Reference</th>
                    <th>Date Requested</th>
                    <th>Incident Date</th>
                    <th>Request Type</th>
                    <th>Attachment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cctvRequests as $req)
                    <tr>
                        <td>CCTV-{{ str_pad($req->cctv_req_id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $req->created_at->format('M d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($req->date_of_incident)->format('M d, Y') }}</td>
                        <td>
                            @if($req->hardcopy)
                                <span class="badge badge-primary">Hardcopy</span>
                            @endif
                            @if($req->viewing)
                                <span class="badge badge-info">Viewing Only</span>
                            @endif
                            @if(!$req->hardcopy && !$req->viewing)
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($req->attachment)
                                <a href="{{ asset('storage/' . $req->attachment) }}" class="btn btn-light btn-xs" target="_blank">
                                    <i class="las la-paperclip"></i> View
                                </a>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusColors = [
                                    'to submit' => 'secondary',
                                    'pending approval' => 'warning',
                                    'Pending HR approval' => 'info',
                                    'Pending Final Approval' => 'primary',
                                    'completed' => 'success',
                                    'rejected' => 'danger',
                                ];
                                $statusColor = $statusColors[$req->status] ?? 'secondary';
                            @endphp
                            <span class="badge badge-{{ $statusColor }}">{{ ucwords(str_replace('_', ' ', $req->status)) }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <button type="button"
                                        class="btn btn-info btn-xs sharp shadow view-details-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#requestDetailsModal"
                                        data-id="{{ $req->cctv_req_id }}"
                                        data-type="CCTV"
                                        data-reference="CCTV-{{ str_pad($req->cctv_req_id, 4, '0', STR_PAD_LEFT) }}"
                                        data-date="{{ $req->created_at->format('M d, Y') }}"
                                        data-status="{{ $req->status }}"
                                        data-description="{{ e($req->purpose) }}"
                                        data-original='@json($req)'
                                        title="View Details">
                                    <i class="las la-eye"></i>
                                </button>

                                @if($req->status === 'to submit')
                                    <form action="{{ route('user.cctv-requests.update', $req->cctv_req_id) }}" method="POST" class="mb-0">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="pending approval">
                                        <button type="submit" class="btn btn-success btn-xs sharp shadow" title="Submit for Approval">
                                            <i class="las la-paper-plane"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('user.cctv-requests.destroy', $req->cctv_req_id) }}" method="POST" class="mb-0" onsubmit="return confirm('Delete this CCTV request?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs sharp shadow" title="Delete">
                                            <i class="las la-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No CCTV requests found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="createCctvModal" tabindex="-1" aria-labelledby="createCctvModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('user.cctv-requests.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createCctvModalLabel">Create New CCTV Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Requested by (Name):</label>
                            <input type="text" name="requested_by" class="form-control" value="{{ old('requested_by', auth()->user()->name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Department:</label>
                            <select name="department" class="form-control" required>
                                @foreach($allowedDepartments as $department)
                                    <option value="{{ $department }}" {{ old('department', $cctvDepartment) === $department ? 'selected' : '' }}>{{ $department }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Date of Incident:</label>
                            <input type="date" name="date_of_incident" class="form-control" value="{{ old('date_of_incident') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Time of Incident:</label>
                            <input type="time" name="time_of_incident" class="form-control" value="{{ old('time_of_incident') }}" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold d-block">Request Type:</label>
                            <div class="d-flex gap-4 p-2 bg-light rounded border">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="hardcopy" id="my_cctv_hardcopy" value="1" {{ old('hardcopy') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="my_cctv_hardcopy">Hardcopy (CD/USB)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="viewing" id="my_cctv_viewing" value="1" {{ old('viewing') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="my_cctv_viewing">Viewing Only</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Purpose:</label>
                            <textarea name="purpose" class="form-control" rows="3" required>{{ old('purpose') }}</textarea>
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
                    <button type="submit" class="btn btn-success">Save CCTV Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
