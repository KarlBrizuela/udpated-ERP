<x-app-layout :title="'List of Sponsors'" :role="'Marketing Manager'" :sidebar="'marketing'">

    @push('styles')
    <style>
        .sponsor-header-card {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            border-radius: 16px;
            padding: 28px 32px;
            color: #fff;
            position: relative;
            overflow: hidden;
            margin-bottom: 24px;
        }
        .sponsor-header-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
            border-radius: 50%;
        }
        .sponsor-header-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: rgba(228,80,73,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: #e45049;
            flex-shrink: 0;
        }
        .sponsor-table-container {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
            overflow: hidden;
        }
        .sponsor-table thead th {
            background: #f8f9fb;
            color: #495057;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 15px 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .sponsor-table tbody td {
            padding: 15px 20px;
            vertical-align: middle;
            border-bottom: 1px solid #f0f2f5;
        }
        .btn-add-sponsor {
            background: linear-gradient(135deg, #e45049, #c0392b);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(228,80,73,0.3);
            transition: all 0.3s ease;
        }
        .btn-add-sponsor:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(228,80,73,0.4);
            color: #fff;
        }
    </style>
    @endpush

    <div class="sponsor-header-card">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="sponsor-header-icon">
                    <i class="las la-handshake"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-white">Sponsor Management</h3>
                    <p class="mb-0 opacity-70">Manage your organization's sponsors and contact information.</p>
                </div>
            </div>
            <button class="btn btn-add-sponsor" data-bs-toggle="modal" data-bs-target="#addSponsorModal">
                <i class="las la-plus me-1"></i> Add New Sponsor
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
            <i class="las la-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
    @endif

    <div class="sponsor-table-container">
        <div class="table-responsive">
            <table class="table sponsor-table mb-0">
                <thead>
                    <tr>
                        <th>Sponsor Name</th>
                        <th>Address</th>
                        <th>Remarks</th>
                        <th>Contact File</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sponsors as $sponsor)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $sponsor->name }}</div>
                            </td>
                            <td><span class="text-muted small">{{ $sponsor->address ?? 'N/A' }}</span></td>
                            <td><span class="text-muted small">{{ $sponsor->remarks ?? 'N/A' }}</span></td>
                            <td>
                                @if($sponsor->file_path)
                                    <a href="/storage/{{ $sponsor->file_path }}" target="_blank" class="btn btn-xs btn-outline-info">
                                        <i class="las la-file-download me-1"></i> View File
                                    </a>
                                @else
                                    <span class="text-muted italic small">No file</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-warning btn-xs shadow-sm" 
                                            onclick="editSponsor({{ json_encode($sponsor) }})"
                                            title="Edit">
                                        <i class="las la-edit"></i>
                                    </button>
                                    <form action="{{ route('marketing.ads-promo.sponsors.destroy', $sponsor->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs shadow-sm" title="Delete">
                                            <i class="las la-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="las la-folder-open fs-1 text-muted mb-3 d-block"></i>
                                <p class="text-muted">No sponsors found. Start by adding a new one!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Sponsor Modal -->
    <div class="modal fade" id="addSponsorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <form action="{{ route('marketing.ads-promo.sponsors.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-dark text-white p-4">
                        <h5 class="modal-title fw-bold"><i class="las la-plus-circle me-2"></i>Add New Sponsor</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Sponsor Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter sponsor name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Enter physical address"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2" placeholder="Additional notes..."></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small text-uppercase">Contact File (PDF/Word)</label>
                            <input type="file" name="contact_file" class="form-control" accept=".pdf,.doc,.docx">
                            <div class="form-text small">Maximum file size: 5MB</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 bg-dark border-0">Save Sponsor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Sponsor Modal -->
    <div class="modal fade" id="editSponsorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <form id="editSponsorForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-warning p-4">
                        <h5 class="modal-title fw-bold"><i class="las la-edit me-2"></i>Edit Sponsor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Sponsor Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Address</label>
                            <textarea name="address" id="edit_address" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Remarks</label>
                            <textarea name="remarks" id="edit_remarks" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small text-uppercase">Update Contact File (PDF/Word)</label>
                            <input type="file" name="contact_file" class="form-control" accept=".pdf,.doc,.docx">
                            <div class="form-text small" id="current_file_msg">Leave blank to keep current file</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning px-4 fw-bold">Update Sponsor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function editSponsor(sponsor) {
            $('#edit_name').val(sponsor.name);
            $('#edit_address').val(sponsor.address);
            $('#edit_remarks').val(sponsor.remarks);
            
            if(sponsor.file_path) {
                $('#current_file_msg').text('Current file: ' + sponsor.file_path.split('/').pop());
            } else {
                $('#current_file_msg').text('No file currently attached');
            }
            
            const url = "{{ route('marketing.ads-promo.sponsors.update', ':id') }}".replace(':id', sponsor.id);
            $('#editSponsorForm').attr('action', url);
            
            const modal = new bootstrap.Modal(document.getElementById('editSponsorModal'));
            modal.show();
        }
    </script>
    @endpush

</x-app-layout>
