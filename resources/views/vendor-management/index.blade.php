<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .vendor-modal-header-info {
            padding: 1rem 1.5rem;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .form-row-custom {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            gap: 1rem;
        }
        .form-row-custom label {
            width: 135px;
            text-align: right;
            font-size: 0.85rem;
            font-weight: 600;
            color: #555;
            margin-bottom: 0;
            flex-shrink: 0;
        }
        .form-row-custom .form-control-sm,
        .form-row-custom .form-select-sm {
            flex: 1;
            border-radius: 2px;
            border: 1px solid #ccc;
        }
        .section-divider {
            border-bottom: 1px solid #eee;
            margin: 1.5rem 0 1rem;
            font-weight: 700;
            font-size: 0.85rem;
            color: #333;
            text-transform: uppercase;
            padding-bottom: 5px;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex">
                    <div>
                        <h4 class="fs-20 mb-0 text-black">Vendor List</h4>
                    </div>
                    <div class="d-flex align-items-center mt-3 mt-sm-0">
                        <input type="text" class="form-control me-3" placeholder="Search vendors..."
                            id="vendorSearch" style="max-width: 300px;" oninput="filterVendors()">
                        <a href="javascript:void(0);"
                            class="btn btn-primary rounded d-flex align-items-center"
                            data-bs-toggle="modal"
                            data-bs-target="#addVendorModal"
                            onclick="openAddVendor()"
                            style="gap: 0.5rem; padding: 0.5rem 1rem; height: 38px; min-height: 38px; line-height: 1.5; box-sizing: border-box; border: none; background: #ff0000; color: #ffffff; font-weight: 500; white-space: nowrap;">
                            <i class="las la-plus" style="font-size: 1rem; line-height: 1; margin: 0; padding: 0;"></i>
                            <span style="font-size: 0.875rem;">Add New Vendor</span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th><strong>VENDOR CODE</strong></th>
                                    <th><strong>VENDOR NAME</strong></th>
                                    <th><strong>CONTACT PERSON</strong></th>
                                    <th><strong>CONTACT NUMBER</strong></th>
                                    <th><strong>EMAIL</strong></th>
                                    <th><strong>ADDRESS</strong></th>
                                    <th><strong>STATUS</strong></th>
                                    <th><strong>ACTION</strong></th>
                                </tr>
                            </thead>
                            <tbody id="vendorTableBody">
                                @forelse($vendors as $vendor)
                                <tr data-vendor-name="{{ strtolower($vendor->vendor_name) }}"
                                    data-vendor-code="{{ strtolower($vendor->vendor_code) }}"
                                    data-contact="{{ strtolower($vendor->contact_person ?? '') }}">
                                    <td><strong>{{ $vendor->vendor_code }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="w-space-no" style="font-size: 13px;">{{ $vendor->vendor_name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $vendor->contact_person ?: 'N/A' }}</td>
                                    <td>{{ $vendor->contact_number ?: 'N/A' }}</td>
                                    <td>{{ $vendor->email ?: 'N/A' }}</td>
                                    <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                        title="{{ $vendor->address }}">
                                        {{ $vendor->address ?: 'N/A' }}
                                    </td>
                                    <td>
                                        @if($vendor->status === 'active')
                                            <span class="badge light badge-success"><i class="fa fa-circle text-success me-1"></i> Active</span>
                                        @else
                                            <span class="badge light badge-danger"><i class="fa fa-circle text-danger me-1"></i> Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1"
                                               title="Edit Vendor"
                                               onclick="openEditVendor({{ $vendor->id }})">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-danger shadow btn-xs sharp"
                                               title="Delete Vendor"
                                               onclick="confirmDeleteVendor({{ $vendor->id }}, '{{ addslashes($vendor->vendor_name) }}')">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">No vendors found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ADD VENDOR MODAL --}}
    <div class="modal fade" id="addVendorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="vendor-modal-header-info d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="modal-title fw-bold text-black mb-0">New Vendor</h5>
                        <p class="text-muted mb-0 small">Enter vendor details below</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <form id="addVendorForm">
                        <div class="section-divider mt-0">Vendor Profile</div>
                        <div class="form-row-custom">
                            <label>Vendor Code</label>
                            <input type="text" class="form-control form-control-sm" id="addVendorCode"
                                   placeholder="Auto-generated" readonly style="background: #f8f9fa; font-family: monospace; font-weight: 700; color: #555;">
                        </div>
                        <div class="form-row-custom">
                            <label>Vendor Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="addVendorName" required>
                        </div>
                        <div class="section-divider">Contact Details</div>
                        <div class="form-row-custom">
                            <label>Contact Person</label>
                            <input type="text" class="form-control form-control-sm" id="addContactPerson">
                        </div>
                        <div class="form-row-custom">
                            <label>Contact Number</label>
                            <input type="text" class="form-control form-control-sm" id="addContactNumber">
                        </div>
                        <div class="form-row-custom">
                            <label>Email Address</label>
                            <input type="email" class="form-control form-control-sm" id="addVendorEmail">
                        </div>
                        <div class="section-divider">Address & Status</div>
                        <div class="form-row-custom align-items-start">
                            <label class="mt-1">Address</label>
                            <textarea class="form-control form-control-sm" id="addVendorAddress" rows="3"></textarea>
                        </div>
                        <div class="form-row-custom">
                            <label>Status</label>
                            <select class="form-select form-select-sm" id="addVendorStatus">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top p-3 bg-light d-flex justify-content-end" style="gap: 0.5rem;">
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary" id="addVendorSaveBtn"
                            onclick="saveNewVendor()"
                            style="background: #ff0000; border: none; font-weight: 500;">Save Vendor</button>
                </div>
            </div>
        </div>
    </div>

    {{-- EDIT VENDOR MODAL --}}
    <div class="modal fade" id="editVendorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="vendor-modal-header-info d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="modal-title fw-bold text-black mb-0">Edit Vendor</h5>
                        <p class="text-muted mb-0 small" id="editVendorSubtitle">Modify vendor details</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <form id="editVendorForm">
                        <input type="hidden" id="editVendorId">
                        <div class="section-divider mt-0">Vendor Profile</div>
                        <div class="form-row-custom">
                            <label>Vendor Code</label>
                            <input type="text" class="form-control form-control-sm" id="editVendorCode"
                                   readonly style="background: #f8f9fa; font-family: monospace; font-weight: 700; color: #555;">
                        </div>
                        <div class="form-row-custom">
                            <label>Vendor Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="editVendorName" required>
                        </div>
                        <div class="section-divider">Contact Details</div>
                        <div class="form-row-custom">
                            <label>Contact Person</label>
                            <input type="text" class="form-control form-control-sm" id="editContactPerson">
                        </div>
                        <div class="form-row-custom">
                            <label>Contact Number</label>
                            <input type="text" class="form-control form-control-sm" id="editContactNumber">
                        </div>
                        <div class="form-row-custom">
                            <label>Email Address</label>
                            <input type="email" class="form-control form-control-sm" id="editVendorEmail">
                        </div>
                        <div class="section-divider">Address & Status</div>
                        <div class="form-row-custom align-items-start">
                            <label class="mt-1">Address</label>
                            <textarea class="form-control form-control-sm" id="editVendorAddress" rows="3"></textarea>
                        </div>
                        <div class="form-row-custom">
                            <label>Status</label>
                            <select class="form-select form-select-sm" id="editVendorStatus">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top p-3 bg-light d-flex justify-content-end" style="gap: 0.5rem;">
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary" id="editVendorSaveBtn"
                            onclick="saveEditVendor()"
                            style="background: #ff0000; border: none; font-weight: 500;">Update Vendor</button>
                </div>
            </div>
        </div>
    </div>

    {{-- DELETE CONFIRM MODAL --}}
    <div class="modal fade" id="deleteVendorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="mb-0">Are you sure you want to delete <strong id="deleteVendorNameLabel"></strong>?</p>
                    <p class="text-danger small mt-1 mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const CSRF           = '{{ csrf_token() }}';
        const VENDOR_STORE   = '{{ route("vendor-management.store") }}';
        const VENDOR_BASE    = '{{ url("vendor-management") }}';

        let pendingDeleteId = null;

        // --- ADD ---
        function openAddVendor() {
            document.getElementById('addVendorForm').reset();
        }

        function saveNewVendor() {
            const name = document.getElementById('addVendorName').value.trim();
            if (!name) {
                alert('Vendor name is required.');
                document.getElementById('addVendorName').focus();
                return;
            }

            const btn = document.getElementById('addVendorSaveBtn');
            btn.disabled = true;
            btn.textContent = 'Saving...';

            fetch(VENDOR_STORE, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({
                    vendor_name:    name,
                    contact_person: document.getElementById('addContactPerson').value.trim(),
                    contact_number: document.getElementById('addContactNumber').value.trim(),
                    email:          document.getElementById('addVendorEmail').value.trim(),
                    address:        document.getElementById('addVendorAddress').value.trim(),
                    status:         document.getElementById('addVendorStatus').value,
                })
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.textContent = 'Save Vendor';
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addVendorModal'))?.hide();
                    location.reload();
                } else {
                    alert(data.message ?? 'Something went wrong.');
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.textContent = 'Save Vendor';
                alert('Request failed. Please try again.');
            });
        }

        // --- EDIT ---
        function openEditVendor(id) {
            fetch(`${VENDOR_BASE}/${id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(v => {
                document.getElementById('editVendorId').value       = v.id;
                document.getElementById('editVendorCode').value     = v.vendor_code;
                document.getElementById('editVendorName').value     = v.vendor_name;
                document.getElementById('editContactPerson').value  = v.contact_person ?? '';
                document.getElementById('editContactNumber').value  = v.contact_number ?? '';
                document.getElementById('editVendorEmail').value    = v.email ?? '';
                document.getElementById('editVendorAddress').value  = v.address ?? '';
                document.getElementById('editVendorStatus').value   = v.status;
                document.getElementById('editVendorSubtitle').textContent = 'Editing: ' + v.vendor_name;
                new bootstrap.Modal(document.getElementById('editVendorModal')).show();
            })
            .catch(() => alert('Failed to load vendor data.'));
        }

        function saveEditVendor() {
            const id   = document.getElementById('editVendorId').value;
            const name = document.getElementById('editVendorName').value.trim();
            if (!name) {
                alert('Vendor name is required.');
                document.getElementById('editVendorName').focus();
                return;
            }

            const btn = document.getElementById('editVendorSaveBtn');
            btn.disabled = true;
            btn.textContent = 'Saving...';

            fetch(`${VENDOR_BASE}/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({
                    vendor_name:    name,
                    contact_person: document.getElementById('editContactPerson').value.trim(),
                    contact_number: document.getElementById('editContactNumber').value.trim(),
                    email:          document.getElementById('editVendorEmail').value.trim(),
                    address:        document.getElementById('editVendorAddress').value.trim(),
                    status:         document.getElementById('editVendorStatus').value,
                })
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.textContent = 'Update Vendor';
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editVendorModal'))?.hide();
                    location.reload();
                } else {
                    alert(data.message ?? 'Something went wrong.');
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.textContent = 'Update Vendor';
                alert('Request failed. Please try again.');
            });
        }

        // --- DELETE ---
        function confirmDeleteVendor(id, name) {
            pendingDeleteId = id;
            document.getElementById('deleteVendorNameLabel').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteVendorModal')).show();
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            if (!pendingDeleteId) return;
            const btn = this;
            btn.disabled = true;
            btn.textContent = 'Deleting...';

            fetch(`${VENDOR_BASE}/${pendingDeleteId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.textContent = 'Yes, Delete';
                bootstrap.Modal.getInstance(document.getElementById('deleteVendorModal'))?.hide();
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message ?? 'Could not delete vendor.');
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.textContent = 'Yes, Delete';
                alert('Request failed. Please try again.');
            });
        });

        // --- SEARCH ---
        function filterVendors() {
            const q = document.getElementById('vendorSearch').value.toLowerCase();
            document.querySelectorAll('#vendorTableBody tr[data-vendor-name]').forEach(row => {
                const match = row.dataset.vendorName.includes(q)
                           || row.dataset.vendorCode.includes(q)
                           || row.dataset.contact.includes(q);
                row.style.display = match ? '' : 'none';
            });
        }
    </script>
    @endpush
</x-app-layout>
