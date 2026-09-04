<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .custom-tab-container {
            background: #fff;
            border-radius: 8px;
            padding: 0;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }
        .nav-tabs {
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 1rem;
        }
        .nav-tabs .nav-link {
            font-weight: 600;
            color: #666;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 0.75rem 1.25rem;
        }
        .nav-tabs .nav-link.active {
            color: #ff0000;
            border-bottom-color: #ff0000;
            background: transparent;
        }
        .tab-pane {
            padding: 0;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-tab-container">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">My Requests</h4>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myRequestsTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#cash-advance" role="tab">Material Request / Cash Advance</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#cctv-requests" role="tab">CCTV Request</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Cash Advance Tab -->
                        <div class="tab-pane fade show active" id="cash-advance" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0"> </h5>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createCashAdvanceModal">
                                    <i class="las la-plus me-1"></i> New Request
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date Requested</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Purpose</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($cashAdvances as $advance)
                                        @php
                                            $isMaterial = isset($advance->material_req_id);
                                            $refId = $isMaterial ? $advance->material_req_id : $advance->id;
                                            $refPrefix = $isMaterial ? 'MAT' : 'CA';
                                            $amountVal = $advance->amount ? 'PhP ' . number_format($advance->amount, 2) : '—';
                                            $purposeVal = $advance->purpose ?? $advance->request_details;
                                            
                                            $statusColors = [
                                                'pending_supervisor_approval' => 'warning',
                                                'pending_admin_approval' => 'info',
                                                'pending_director_approval' => 'primary',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'to submit' => 'secondary',
                                                'pending approval' => 'warning',
                                                'Pending Final Approval' => 'primary',
                                                'forwarded to accounting' => 'info',
                                                'received' => 'success',
                                            ];
                                            $color = $statusColors[$advance->status] ?? 'secondary';
                                            $deptVal = $isMaterial ? $advance->module : $advance->department;
                                            $displayStatus = $advance->status;
                                            if ($advance->status === 'forwarded to accounting') {
                                                if ($deptVal === 'MIS') {
                                                    $displayStatus = 'Forwarded To MIS';
                                                } elseif ($deptVal === 'GSD') {
                                                    $displayStatus = 'Forwarded To GSD';
                                                } else {
                                                    $displayStatus = 'Forwarded To Accounting';
                                                }
                                            } else {
                                                $displayStatus = ucwords(str_replace('_', ' ', $advance->status));
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $advance->created_at->format('M d, Y') }}</td>
                                            <td><span class="badge badge-info">{{ $isMaterial ? 'Material' : ($advance->request_type ?? 'Cash Advance') }}</span></td>
                                            <td>{{ $amountVal }}</td>
                                            <td>{{ Str::limit($purposeVal, 50) }}</td>
                                            <td>
                                                <span class="badge badge-sm light badge-{{ $color }}">
                                                    {{ $displayStatus }}
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" 
                                                        class="btn btn-info btn-xs sharp shadow view-details-btn" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#requestDetailsModal"
                                                        data-id="{{ $refId }}"
                                                        data-type="{{ $isMaterial ? 'Material' : 'Cash Advance' }}"
                                                        data-reference="{{ $refPrefix }}-{{ str_pad($refId, 4, '0', STR_PAD_LEFT) }}"
                                                        data-date="{{ $advance->created_at->format('M d, Y') }}"
                                                        data-status="{{ $advance->status }}"
                                                        data-amount="{{ $amountVal }}"
                                                        data-description="{{ $purposeVal }}"
                                                        data-original="{{ json_encode($advance) }}"
                                                        title="View Details">
                                                    <i class="las la-eye"></i>
                                                </button>
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

                        @include('partials.my-requests.cctv-tab', ['cctvDepartment' => 'Production'])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Request Details Modal -->
    <div class="modal fade" id="requestDetailsModal" tabindex="-1" aria-labelledby="requestDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 text-white position-relative" style="background: #dc3545; padding: 1.5rem 2rem;">
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-1" id="requestDetailsModalLabel">
                            <i class="las la-file-alt me-2"></i>Request Details
                        </h5>
                        <p class="mb-0 opacity-75 small" id="modalReferenceHeader"></p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-0">
                    <div class="p-3" style="background: #f8f9fa;">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #f8f9fa; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="las la-hashtag" style="font-size: 1.1rem; color: #6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size: 0.75rem; font-weight: 600;">Reference Number</label>
                                            <p id="modalReference" class="fw-bold mb-0 small" style="color: #212529;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #f8f9fa; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="las la-info-circle" style="font-size: 1.1rem; color: #6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size: 0.75rem; font-weight: 600;">Status</label>
                                            <div id="modalStatus"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #f8f9fa; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="las la-tag" style="font-size: 1.1rem; color: #6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size: 0.75rem; font-weight: 600;">Request Type</label>
                                            <p id="modalType" class="mb-0 small fw-semibold" style="color: #212529;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #f8f9fa; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="las la-calendar" style="font-size: 1.1rem; color: #6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size: 0.75rem; font-weight: 600;">Date Submitted</label>
                                            <p id="modalDate" class="mb-0 small fw-semibold" style="color: #212529;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 pb-4" style="background: #f8f9fa;">
                        <div class="details-section p-4 rounded-3 bg-white border">
                            <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #dc3545; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                    <i class="las la-clipboard-list text-white" style="font-size: 1.1rem;"></i>
                                </div>
                                <h6 class="fw-bold mb-0" style="color: #212529;">Request Details</h6>
                            </div>
                            <div id="modalDescription" class="p-3 rounded-2" style="background: #f8f9fa; min-height: 80px;"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top bg-white" style="padding: 1.25rem 2rem;">
                    <button type="button" class="btn btn-light px-4 py-2 fw-semibold border" data-bs-dismiss="modal">
                        <i class="las la-times me-1"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('partials.modals.cash-advance-request')

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#requestDetailsModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const type = button.attr('data-type');
                const reference = button.attr('data-reference');
                const date = button.attr('data-date');
                const description = button.attr('data-description');
                const status = button.attr('data-status') || '';
                const amount = button.attr('data-amount');
                
                let original = {};
                try {
                    original = JSON.parse(button.attr('data-original'));
                } catch(e) {}

                $('#modalReference').text(reference);
                $('#modalReferenceHeader').text(reference);
                $('#modalType').text(type + ' Request');
                $('#modalDate').text(date);
                
                if(status) {
                    const statusConfig = {
                        'pending approval': { badge: 'warning', text: 'Manager Review' },
                        'pending_supervisor_approval': { badge: 'warning', text: 'Supervisor Review' },
                        'pending_admin_approval': { badge: 'info', text: 'Admin Review' },
                        'pending_director_approval': { badge: 'primary', text: 'Director Review' },
                        'Pending HR approval': { badge: 'info', text: 'HR Review' },
                        'Pending Final Approval': { badge: 'primary', text: 'Final Review' },
                        'forwarded to accounting': { badge: 'info', text: 'Forwarded to Accounting' },
                        'received': { badge: 'success', text: 'Received' },
                        'completed': { badge: 'success', text: 'Completed' },
                        'rejected': { badge: 'danger', text: 'Rejected' },
                        'approved': { badge: 'success', text: 'Approved' },
                        'to submit': { badge: 'secondary', text: 'To Submit' }
                    };
                    const config = statusConfig[status] || { badge: 'secondary', text: status };
                    $('#modalStatus').html(`<span class="badge badge-${config.badge}">${config.text}</span>`);
                }

                let descriptionHtml = ``;
                
                if (type === 'Cash Advance') {
                    descriptionHtml = `
                        <table class="table table-sm mb-2">
                            <tbody>
                                <tr>
                                    <th class="bg-light py-1" style="width: 30%;">Requested Amount</th>
                                    <td class="fw-bold text-primary py-1">${amount}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="mt-2">
                            <label class="fw-bold mb-1 d-block small">Purpose / Justification:</label>
                            <div class="p-2 border rounded bg-white">${description}</div>
                        </div>`;
                } else if (type === 'Material') {
                    descriptionHtml = `
                        <table class="table table-sm mb-2">
                            <tbody>
                                <tr>
                                    <th class="bg-light py-1" style="width: 30%;">Department</th>
                                    <td class="py-1">${original.module || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light py-1" style="width: 30%;">Requested Amount</th>
                                    <td class="fw-bold text-primary py-1">${amount}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="mt-2">
                            <label class="fw-bold mb-1 d-block small">Request Details:</label>
                            <div class="p-2 border rounded bg-white">${description}</div>
                        </div>`;
                } else if (type === 'CCTV') {
                    const requestTypes = [];
                    if (original.hardcopy) requestTypes.push('Hardcopy (CD/USB)');
                    if (original.viewing) requestTypes.push('Viewing Only');
                    const attachmentHtml = original.attachment
                        ? `<a href="/storage/${original.attachment}" target="_blank" class="btn btn-light btn-sm"><i class="las la-paperclip me-1"></i>View Attachment</a>`
                        : '<span class="text-muted">No attachment uploaded</span>';

                    descriptionHtml = `
                        <table class="table table-sm mb-2">
                            <tbody>
                                <tr>
                                    <th class="bg-light py-1" style="width: 30%;">Requested By</th>
                                    <td class="py-1">${original.requested_by || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light py-1">Department</th>
                                    <td class="py-1">${original.department || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light py-1">Incident Date / Time</th>
                                    <td class="py-1">${original.date_of_incident || 'N/A'} ${original.time_of_incident || ''}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light py-1">Request Type</th>
                                    <td class="py-1">${requestTypes.length ? requestTypes.join(', ') : 'N/A'}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light py-1">Attachment</th>
                                    <td class="py-1">${attachmentHtml}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="mt-2">
                            <label class="fw-bold mb-1 d-block small">Purpose:</label>
                            <div class="p-2 border rounded bg-white">${description}</div>
                        </div>`;
                }
                
                $('#modalDescription').html(descriptionHtml);
            });
        });
    </script>
    @endpush
</x-app-layout>
