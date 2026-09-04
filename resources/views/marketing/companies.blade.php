<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .company-modal-header-info {
            padding: 1rem 1.5rem;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .company-tab-container {
            display: flex;
            min-height: 350px;
        }

        .company-nav-tabs {
            width: 180px;
            border-right: 1px solid #dee2e6;
            background: #f1f1f1;
            padding-top: 1rem;
        }

        .company-nav-tabs .nav-link {
            border: none;
            border-radius: 0;
            text-align: left;
            padding: 10px 15px;
            color: #444;
            font-weight: 500;
            font-size: 0.9rem;
            border-left: 4px solid transparent;
        }

        .company-nav-tabs .nav-link:hover {
            background: #e9e9e9;
        }

        .company-nav-tabs .nav-link.active {
            background: #fff;
            color: #000;
            border-left-color: #ff0000;
            margin-right: -1px;
            border-bottom: 1px solid #dee2e6;
            border-top: 1px solid #dee2e6;
        }

        .company-tab-content {
            flex: 1;
            padding: 1.5rem;
            background: #fff;
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
                        <h4 class="fs-20 mb-0 text-black">Company List</h4>
                    </div>
                    <div class="d-flex align-items-center mt-3 mt-sm-0">
                        <input type="text" class="form-control me-3" placeholder="Search companies..."
                            id="companySearch" style="max-width: 300px;">
                        <a href="javascript:void(0);"
                            class="btn btn-primary rounded d-flex align-items-center" data-bs-toggle="modal"
                            data-bs-target="#addCompanyModal"
                            style="gap: 0.5rem; padding: 0.5rem 1rem; height: 38px; min-height: 38px; line-height: 1.5; box-sizing: border-box; border: none; background: #ff0000; color: #ffffff; font-weight: 500;">
                            <i class="las la-plus"
                                style="font-size: 1rem; line-height: 1; margin: 0; padding: 0; background: transparent; border: none; box-shadow: none;"></i>
                            <span style="font-size: 0.875rem; white-space: nowrap;">Add New Company</span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th><strong>COMPANY ID</strong></th>
                                    <th><strong>COMPANY NAME</strong></th>
                                    <th><strong>ACCOUNT NUMBER</strong></th>
                                    <th><strong>PHONE NUMBER</strong></th>
                                    <th><strong>EMAIL</strong></th>
                                    <th><strong>ADDRESS</strong></th>
                                    <th><strong>STATUS</strong></th>
                                    <th><strong>ACTION</strong></th>
                                </tr>
                            </thead>
                            <tbody id="companyTableBody">
                              @forelse($companies as $company)
                               <tr>
                                  <td><strong>{{$company->company_id}}</strong></td>
                                  <td>
                                    <div class="d-flex align-items-center">
                                      <span class="w-space-no" style="font-size: 13px;">{{$company->company_name}}</span>
                                    </div>
                                  </td>
                                  <td>{{$company->account_number}}</td>
                                  <td>{{$company->mobile}}</td>
                                  <td>{{$company->main_email ?: 'N/A'}}</td>
                                  <td>{{$company->shipping_address}}</td>
                                  <td>
                                      @if($company->is_inactive)
                                        <span class="badge light badge-danger"><i class="fa fa-circle text-danger me-1"></i> Inactive</span>
                                      @else
                                        <span class="badge light badge-success"><i class="fa fa-circle text-success me-1"></i> Active</span>
                                      @endif
                                  </td>
                                  <td>
                                       <div class="d-flex">
                                           @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('marketing.customers'))
                                           <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp me-1 add-branch-btn"
                                               data-company-id="{{$company->company_id}}" data-company-name="{{$company->company_name}}" title="Add Branch">
                                               <i class="fas fa-code-branch"></i></a>
                                           <a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1 edit-company-btn"
                                               data-company-id="{{$company->company_id}}">
                                               <i class="fas fa-pencil-alt"></i></a>
                                           <a href="javascript:void(0);" class="btn btn-danger shadow btn-xs sharp delete-company-btn"
                                               data-company-id="{{$company->company_id}}">
                                               <i class="fa fa-trash"></i></a>
                                           @endif
                                       </div>
                                  </td>
                               </tr>
                              @empty
                               <tr>
                                  <td colspan="8" class="text-center py-4">No companies found.</td>
                               </tr>
                              @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD COMPANY MODAL -->
    <div class="modal fade" id="addCompanyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="company-modal-header-info d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="modal-title fw-bold text-black mb-0">New Company</h5>
                        <p class="text-muted mb-0 small">Enter company details below</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <form id="addCompanyForm">
                        <div class="company-tab-container">
                            <div class="company-nav-tabs nav flex-column nav-pills me-3" role="tablist">
                                <button class="nav-link active" id="add-company-gen-tab" data-bs-toggle="pill" data-bs-target="#add-company-gen" type="button" role="tab">General Info</button>
                                <button class="nav-link" id="add-company-contact-tab" data-bs-toggle="pill" data-bs-target="#add-company-contact" type="button" role="tab">Address Details</button>
                            </div>
                            <div class="company-tab-content tab-content">
                                <div class="tab-pane fade show active" id="add-company-gen" role="tabpanel">
                                    <div class="section-divider mt-0">Company Profile</div>
                                    <div class="form-row-custom">
                                        <label for="addCompName">Company Name *</label>
                                        <input type="text" class="form-control form-control-sm" id="addCompName" required>
                                    </div>
                                    <div class="form-row-custom">
                                        <label for="addCompAccount">Account Number</label>
                                        <input type="text" class="form-control form-control-sm" id="addCompAccount" placeholder="N/A">
                                    </div>
                                    <div class="section-divider">Contact Details</div>
                                    <div class="form-row-custom">
                                        <label for="addCompPhone">Phone Number</label>
                                        <input type="text" class="form-control form-control-sm" id="addCompPhone" placeholder="N/A">
                                    </div>
                                    <div class="form-row-custom">
                                        <label for="addCompEmail">Email Address</label>
                                        <input type="email" class="form-control form-control-sm" id="addCompEmail">
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="add-company-contact" role="tabpanel">
                                    <div class="section-divider mt-0">Main Address</div>
                                    <div class="form-row-custom align-items-start">
                                        <label for="addCompAddress" class="mt-1">Shipping Address</label>
                                        <textarea class="form-control form-control-sm" id="addCompAddress" rows="5" placeholder="N/A"></textarea>
                                    </div>
                                    <div class="section-divider">Hierarchy Settings</div>
                                    <div class="form-row-custom">
                                        <label for="addCompParent">Branch Of (Parent)</label>
                                        <select class="form-select form-select-sm" id="addCompParent">
                                            <option value="">None (Top-Level Company)</option>
                                            @foreach($parentCompanies as $pComp)
                                                <option value="{{ $pComp->company_id }}">{{ $pComp->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top p-3 bg-light d-flex justify-content-end" style="gap: 0.5rem;">
                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-primary" style="background: #ff0000; border: none; font-weight: 500;">Save Company</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT COMPANY MODAL -->
    <div class="modal fade" id="editCompanyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="company-modal-header-info d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="modal-title fw-bold text-black mb-0" id="editCompanyModalTitle">Edit Company</h5>
                        <p class="text-muted mb-0 small">Modify company details</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <form id="editCompanyForm">
                        <input type="hidden" id="editCompanyId">
                        <div class="company-tab-container">
                            <div class="company-nav-tabs nav flex-column nav-pills me-3" role="tablist">
                                <button class="nav-link active" id="edit-company-gen-tab" data-bs-toggle="pill" data-bs-target="#edit-company-gen" type="button" role="tab">General Info</button>
                                <button class="nav-link" id="edit-company-contact-tab" data-bs-toggle="pill" data-bs-target="#edit-company-contact" type="button" role="tab">Address Details</button>
                                <button class="nav-link" id="edit-company-branches-tab" data-bs-toggle="pill" data-bs-target="#edit-company-branches" type="button" role="tab">Branch List</button>
                            </div>
                            <div class="company-tab-content tab-content">
                                <div class="tab-pane fade show active" id="edit-company-gen" role="tabpanel">
                                    <div class="section-divider mt-0">Company Profile</div>
                                    <div class="form-row-custom">
                                        <label for="editCompName">Company Name *</label>
                                        <input type="text" class="form-control form-control-sm" id="editCompName" required>
                                    </div>
                                    <div class="form-row-custom">
                                        <label for="editCompAccount">Account Number</label>
                                        <input type="text" class="form-control form-control-sm" id="editCompAccount" placeholder="N/A">
                                    </div>
                                    <div class="section-divider">Contact Details</div>
                                    <div class="form-row-custom">
                                        <label for="editCompPhone">Phone Number</label>
                                        <input type="text" class="form-control form-control-sm" id="editCompPhone">
                                    </div>
                                    <div class="form-row-custom">
                                        <label for="editCompEmail">Email Address</label>
                                        <input type="email" class="form-control form-control-sm" id="editCompEmail">
                                    </div>
                                    <div class="section-divider">Status Settings</div>
                                    <div class="form-row-custom">
                                        <label for="editCompanyInactive">Mark Inactive</label>
                                        <div class="form-check form-switch ps-0">
                                            <input class="form-check-input ms-0" type="checkbox" id="editCompanyInactive">
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="edit-company-contact" role="tabpanel">
                                    <div class="section-divider mt-0">Main Address</div>
                                    <div class="form-row-custom align-items-start">
                                        <label for="editCompAddress" class="mt-1">Shipping Address</label>
                                        <textarea class="form-control form-control-sm" id="editCompAddress" rows="5"></textarea>
                                    </div>
                                    <div class="section-divider">Hierarchy Settings</div>
                                    <div class="form-row-custom">
                                        <label for="editCompParent">Branch Of (Parent)</label>
                                        <select class="form-select form-select-sm" id="editCompParent">
                                            <option value="">None (Top-Level Company)</option>
                                            @foreach($parentCompanies as $pComp)
                                                <option value="{{ $pComp->company_id }}">{{ $pComp->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="edit-company-branches" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center section-divider mt-0" style="padding-bottom: 5px;">
                                        <span class="fw-bold">Branch List</span>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-xs btn-outline-success py-1 px-2" id="importBranchFromEditModalBtn" style="font-size: 0.75rem;">
                                                <i class="fas fa-file-excel me-1"></i>Import Excel
                                            </button>
                                            <button type="button" class="btn btn-xs btn-primary py-1 px-2" id="addBranchFromEditModalBtn" style="background: #ff0000; border: none; font-size: 0.75rem;">
                                                <i class="fas fa-plus me-1"></i>Add Branch
                                            </button>
                                        </div>
                                    </div>
                                    <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                                        <table class="table table-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Branch Name</th>
                                                    <th>Account Number</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="editCompanyBranchesList">
                                                <!-- Dynamically populated via JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top p-3 bg-light d-flex justify-content-end" style="gap: 0.5rem;">
                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-primary" style="background: #ff0000; border: none; font-weight: 500;">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- CONFIRM DELETE MODAL -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content border-0 shadow">
                <div class="modal-body text-center p-4">
                    <i class="fas fa-exclamation-triangle text-danger mb-3" style="font-size: 3rem;"></i>
                    <h5 class="fw-bold mb-2">Delete Company?</h5>
                    <p class="text-muted mb-4 small">Are you sure you want to delete this company? This action cannot be undone.</p>
                    <div class="d-flex justify-content-center" style="gap: 0.5rem;">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-sm btn-danger px-3" id="confirmDeleteBtn" style="background: #ff0000; border: none;">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SUCCESS MODAL -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content border-0 shadow">
                <div class="modal-body text-center p-4">
                    <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                    <h5 class="fw-bold mb-2" id="successTitle">Success!</h5>
                    <p class="text-muted mb-4 small" id="successMessage">Action completed successfully.</p>
                    <button type="button" class="btn btn-sm btn-success px-4" id="successOkBtn">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ERROR MODAL -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content border-0 shadow">
                <div class="modal-body text-center p-4">
                    <i class="fas fa-times-circle text-danger mb-3" style="font-size: 3rem;"></i>
                    <h5 class="fw-bold mb-2 text-danger">Error</h5>
                    <p class="text-muted mb-4 small" id="errorMessage">An error occurred while processing your request.</p>
                    <button type="button" class="btn btn-sm btn-outline-danger px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD BRANCH MODAL -->
    <div class="modal fade" id="addBranchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
            <div class="modal-content border-0 shadow">
                <div class="company-modal-header-info d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="modal-title fw-bold text-black mb-0">Add Branch</h5>
                        <p class="text-muted mb-0 small" id="addBranchParentText">Add branch details</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="addBranchForm">
                        <input type="hidden" id="addBranchParentId">
                        <div class="form-row-custom mb-3">
                            <label for="addBranchName" style="width: 120px; text-align: left;">Branch Name *</label>
                            <input type="text" class="form-control form-control-sm" id="addBranchName" required>
                        </div>
                        <div class="form-row-custom mb-3">
                            <label for="addBranchAccount" style="width: 120px; text-align: left;">Account Number</label>
                            <input type="text" class="form-control form-control-sm" id="addBranchAccount" placeholder="N/A">
                        </div>
                        <div class="d-flex justify-content-end" style="gap: 0.5rem; margin-top: 1.5rem;">
                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-primary" style="background: #ff0000; border: none; font-weight: 500;">Save Branch</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- IMPORT BRANCH MODAL -->
    <div class="modal fade" id="importBranchModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
            <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
                <div class="company-modal-header-info d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="modal-title fw-bold text-black mb-0">Import Branches from Excel</h5>
                        <p class="text-muted mb-0 small" id="importBranchParentText">Upload Excel file to add multiple branches</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="importBranchForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="importBranchCompanyId" name="company_id">
                    <div class="modal-body p-4">
                        <div class="mb-3 text-end">
                            <a href="{{ route('marketing.companies.branches.download-template') }}" class="btn btn-sm btn-outline-primary fw-semibold" style="border-radius: 6px;">
                                <i class="fas fa-download me-1"></i>Download Excel Template
                            </a>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label font-w600 text-black">Select Excel File (.xlsx, .xls, .csv) *</label>
                            <input type="file" class="form-control form-control-sm" id="branchExcelFile" name="excel_file" accept=".xlsx,.xls,.csv" required>
                        </div>

                        <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 0.8rem; border-radius: 6px;">
                            <i class="fas fa-info-circle me-1"></i><strong>Template Headers:</strong> <code>Branch Name*</code>, <code>Account Number</code>, <code>Phone / Mobile</code>, <code>Email</code>, <code>Address</code>, <code>Status</code>.
                        </div>
                    </div>
                    <div class="modal-footer border-top p-3 bg-light d-flex justify-content-end" style="gap: 0.5rem;">
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-success px-3" id="importBranchSubmitBtn" style="background: #28a745; border: none; font-weight: 500;">
                            <i class="fas fa-upload me-1"></i>Upload & Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const canEditCompanies = {{ (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('marketing.customers')) ? 'true' : 'false' }};

        // Global handler for native HTML5 validation on hidden tabs
        document.addEventListener('invalid', function(e) {
            const target = e.target;
            if (!target) return;

            const pane = target.closest('.tab-pane');
            if (pane && !pane.classList.contains('active') && !pane.classList.contains('show')) {
                const tabId = pane.id;
                const form = target.closest('form');
                const tabBtn = form ? form.querySelector(`button[data-bs-target="#${tabId}"], button[href="#${tabId}"]`)
                                    : document.querySelector(`button[data-bs-target="#${tabId}"], button[href="#${tabId}"]`);
                if (tabBtn && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                    const tabInstance = bootstrap.Tab.getOrCreateInstance(tabBtn);
                    tabInstance.show();
                    setTimeout(() => {
                        target.focus();
                    }, 150);
                }
            }
        }, true);

        // Reset Add Company Modal active tab when opening
        const addCompModalEl = document.getElementById('addCompanyModal');
        if (addCompModalEl) {
            addCompModalEl.addEventListener('show.bs.modal', function() {
                const genTabBtn = document.getElementById('add-company-gen-tab');
                if (genTabBtn && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                    bootstrap.Tab.getOrCreateInstance(genTabBtn).show();
                }
                const nameInput = document.getElementById('addCompName');
                if (nameInput) nameInput.classList.remove('is-invalid');
            });
        }

        // Add Branch Modal Trigger
        document.querySelectorAll('.add-branch-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const parentId = this.dataset.companyId;
                const parentName = this.dataset.companyName;

                document.getElementById('addBranchParentId').value = parentId;
                document.getElementById('addBranchParentText').textContent = `Add branch details under ${parentName}`;
                document.getElementById('addBranchName').value = '';
                document.getElementById('addBranchAccount').value = '';

                const addBranchModal = new bootstrap.Modal(document.getElementById('addBranchModal'));
                addBranchModal.show();
            });
        });

        // Create Branch Form Submit
        document.getElementById('addBranchForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';

            const parentId = document.getElementById('addBranchParentId').value;
            const data = {
                company_name: document.getElementById('addBranchName').value,
                parent_id: parentId,
                account_number: document.getElementById('addBranchAccount').value || null,
                mobile: null,
                main_email: null,
                shipping_address: null
            };

            try {
                const response = await fetch('/marketing/companies', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    bootstrap.Modal.getInstance(document.getElementById('addBranchModal')).hide();
                    form.reset();
                    
                    document.getElementById('successMessage').textContent = 'Branch added successfully!';
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();

                    document.getElementById('successOkBtn').onclick = function() {
                        window.location.reload();
                    };
                } else {
                    let errorMsg = result.message || 'Failed to add branch.';
                    if (result.errors) {
                        errorMsg = Object.values(result.errors).flat().join('\n');
                    }
                    document.getElementById('errorMessage').textContent = errorMsg;
                    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                }
            } catch (error) {
                document.getElementById('errorMessage').textContent = 'An error occurred: ' + error.message;
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Branch';
            }
        });

        // Create Company Form Submit
        document.getElementById('addCompanyForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = this;

            const compNameInput = document.getElementById('addCompName');
            if (!compNameInput.value.trim()) {
                const genTabBtn = document.getElementById('add-company-gen-tab');
                if (genTabBtn && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                    bootstrap.Tab.getOrCreateInstance(genTabBtn).show();
                }
                compNameInput.classList.add('is-invalid');
                compNameInput.focus();
                return;
            } else {
                compNameInput.classList.remove('is-invalid');
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';

            const data = {
                company_name: compNameInput.value.trim(),
                parent_id: document.getElementById('addCompParent').value || null,
                account_number: document.getElementById('addCompAccount').value || null,
                mobile: document.getElementById('addCompPhone').value,
                main_email: document.getElementById('addCompEmail').value || null,
                shipping_address: document.getElementById('addCompAddress').value
            };

            try {
                const response = await fetch('/marketing/companies', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    bootstrap.Modal.getInstance(document.getElementById('addCompanyModal')).hide();
                    form.reset();
                    
                    document.getElementById('successMessage').textContent = 'Company created successfully!';
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();

                    document.getElementById('successOkBtn').onclick = function() {
                        window.location.reload();
                    };
                } else {
                    let errorMsg = result.message || 'Failed to create company.';
                    if (result.errors) {
                        errorMsg = Object.values(result.errors).flat().join('\n');
                    }
                    document.getElementById('errorMessage').textContent = errorMsg;
                    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                }
            } catch (error) {
                document.getElementById('errorMessage').textContent = 'An error occurred: ' + error.message;
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Company';
            }
        });

        async function loadEditCompanyDetails(companyId) {
            try {
                const response = await fetch(`/marketing/companies/${companyId}/edit`);
                const company = await response.json();

                document.getElementById('editCompanyId').value = company.company_id;
                document.getElementById('editCompName').value = company.company_name;
                document.getElementById('editCompAccount').value = company.account_number || '';
                document.getElementById('editCompPhone').value = company.mobile || '';
                document.getElementById('editCompEmail').value = company.main_email || '';
                document.getElementById('editCompAddress').value = company.shipping_address || '';
                document.getElementById('editCompanyInactive').checked = company.is_inactive ? true : false;

                // Populate parent dropdown and prevent self-referencing
                const parentSelect = document.getElementById('editCompParent');
                parentSelect.value = company.parent_id || '';
                Array.from(parentSelect.options).forEach(option => {
                    if (option.value == companyId) {
                        option.disabled = true;
                    } else {
                        option.disabled = false;
                    }
                });

                // Populate branches table list
                const branchesTableBody = document.getElementById('editCompanyBranchesList');
                branchesTableBody.innerHTML = '';
                
                if (company.branches && company.branches.length > 0) {
                    company.branches.forEach(branch => {
                        const statusBadge = branch.is_inactive 
                            ? '<span class="badge light badge-danger py-1 px-2">Inactive</span>' 
                            : '<span class="badge light badge-success py-1 px-2">Active</span>';
                        
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td><span style="font-size: 13px;">${branch.company_name}</span></td>
                            <td>${branch.account_number || 'N/A'}</td>
                            <td>${statusBadge}</td>
                            <td>
                                <div class="d-flex">
                                    <a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1 edit-branch-from-list-btn"
                                        data-branch-id="${branch.company_id}">
                                        <i class="fas fa-pencil-alt"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-danger shadow btn-xs sharp delete-branch-from-list-btn"
                                        data-branch-id="${branch.company_id}">
                                        <i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        `;
                        branchesTableBody.appendChild(row);
                    });

                    // Bind click listeners for branch actions inside list
                    branchesTableBody.querySelectorAll('.edit-branch-from-list-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const bId = this.dataset.branchId;
                            const editModalInst = bootstrap.Modal.getOrCreateInstance(document.getElementById('editCompanyModal'));
                            if (editModalInst) editModalInst.hide();
                            loadEditCompanyDetails(bId);
                        });
                    });

                    branchesTableBody.querySelectorAll('.delete-branch-from-list-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const bId = this.dataset.branchId;
                            const editModalInst = bootstrap.Modal.getOrCreateInstance(document.getElementById('editCompanyModal'));
                            if (editModalInst) editModalInst.hide();
                            companyIdToDelete = bId;
                            const confirmDeleteModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('confirmDeleteModal'));
                            confirmDeleteModal.show();
                        });
                    });
                } else {
                    const row = document.createElement('tr');
                    row.innerHTML = `<td colspan="4" class="text-center text-muted py-3">No branches registered.</td>`;
                    branchesTableBody.appendChild(row);
                }

                // Wire Add Branch button inside edit modal
                document.getElementById('addBranchFromEditModalBtn').onclick = function() {
                    const editModalInst = bootstrap.Modal.getOrCreateInstance(document.getElementById('editCompanyModal'));
                    if (editModalInst) editModalInst.hide();
                    
                    document.getElementById('addBranchParentId').value = company.company_id;
                    document.getElementById('addBranchParentText').textContent = `Add branch details under ${company.company_name}`;
                    document.getElementById('addBranchName').value = '';
                    document.getElementById('addBranchAccount').value = '';

                    const addBranchModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('addBranchModal'));
                    addBranchModal.show();
                };

                // Wire Import Branch button inside edit modal
                document.getElementById('importBranchFromEditModalBtn').onclick = function() {
                    const editModalInst = bootstrap.Modal.getOrCreateInstance(document.getElementById('editCompanyModal'));
                    if (editModalInst) editModalInst.hide();
                    
                    document.getElementById('importBranchCompanyId').value = company.company_id;
                    document.getElementById('importBranchParentText').textContent = `Upload Excel file to add branches under ${company.company_name}`;
                    document.getElementById('branchExcelFile').value = '';

                    const importBranchModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('importBranchModal'));
                    importBranchModal.show();
                };

                // Switch back to "General Info" tab as default when modal opens
                const genTabBtn = document.getElementById('edit-company-gen-tab');
                if (genTabBtn && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                    const firstTab = bootstrap.Tab.getOrCreateInstance(genTabBtn);
                    firstTab.show();
                }

                const editModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editCompanyModal'));
                editModal.show();
            } catch (error) {
                console.error('Error loading company details:', error);
                alert('Error loading company details: ' + error.message);
            }
        }

        // Edit Company details loader trigger
        document.querySelectorAll('.edit-company-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                loadEditCompanyDetails(this.dataset.companyId);
            });
        });

        // Edit Company Form Submit
        document.getElementById('editCompanyForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = this;
            const companyId = document.getElementById('editCompanyId').value;
            const submitBtn = form.querySelector('button[type="submit"]');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';

            const data = {
                company_name: document.getElementById('editCompName').value,
                parent_id: document.getElementById('editCompParent').value || null,
                account_number: document.getElementById('editCompAccount').value || null,
                mobile: document.getElementById('editCompPhone').value,
                main_email: document.getElementById('editCompEmail').value || null,
                shipping_address: document.getElementById('editCompAddress').value,
                is_inactive: document.getElementById('editCompanyInactive').checked ? 1 : 0
            };

            try {
                const response = await fetch(`/marketing/companies/${companyId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    bootstrap.Modal.getInstance(document.getElementById('editCompanyModal')).hide();
                    
                    document.getElementById('successMessage').textContent = 'Company updated successfully!';
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();

                    document.getElementById('successOkBtn').onclick = function() {
                        window.location.reload();
                    };
                } else {
                    let errorMsg = result.message || 'Failed to update company.';
                    if (result.errors) {
                        errorMsg = Object.values(result.errors).flat().join('\n');
                    }
                    document.getElementById('errorMessage').textContent = errorMsg;
                    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                }
            } catch (error) {
                document.getElementById('errorMessage').textContent = 'An error occurred: ' + error.message;
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Changes';
            }
        });

        let companyIdToDelete = null;

        // Open Confirm Delete Modal
        document.querySelectorAll('.delete-company-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!canEditCompanies) {
                    alert('You do not have permission to delete companies.');
                    return;
                }
                companyIdToDelete = this.dataset.companyId;
                const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                confirmDeleteModal.show();
            });
        });

        // Delete Company via AJAX
        document.getElementById('confirmDeleteBtn')?.addEventListener('click', async function() {
            if (!companyIdToDelete) return;

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Deleting...';

            try {
                const response = await fetch(`/marketing/companies/${companyIdToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal')).hide();

                if (response.ok) {
                    document.getElementById('successMessage').textContent = 'Company deleted successfully!';
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                    
                    document.getElementById('successOkBtn').onclick = function() {
                        window.location.reload();
                    };
                } else {
                    document.getElementById('errorMessage').textContent = result.message || 'Failed to delete company.';
                    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                }
            } catch (error) {
                const modalEl = document.getElementById('confirmDeleteModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();

                document.getElementById('errorMessage').textContent = 'An error occurred: ' + error.message;
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Delete';
                companyIdToDelete = null;
            }
        });

        // Company Search Functionality
        document.getElementById('companySearch')?.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#companyTableBody tr:not(#noResultsRow)');
            let hasVisibleRows = false;
            let totalActualRows = 0;
            
            rows.forEach(row => {
                if (row.cells.length === 1 && row.textContent.includes('No companies found')) {
                    return; 
                }
                
                totalActualRows++;
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                    hasVisibleRows = true;
                } else {
                    row.style.display = 'none';
                }
            });
            
            if (totalActualRows > 0) {
                let noResultsRow = document.getElementById('noResultsRow');
                if (!hasVisibleRows) {
                    if (!noResultsRow) {
                        noResultsRow = document.createElement('tr');
                        noResultsRow.id = 'noResultsRow';
                        noResultsRow.innerHTML = '<td colspan="8" class="text-center py-4">No companies match your search.</td>';
                        document.getElementById('companyTableBody').appendChild(noResultsRow);
                    }
                    noResultsRow.style.display = '';
                } else if (noResultsRow) {
                    noResultsRow.style.display = 'none';
                }
            }
        });

        // Import Branch Form Submit Handler
        document.getElementById('importBranchForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const companyId = document.getElementById('importBranchCompanyId').value;
            const submitBtn = document.getElementById('importBranchSubmitBtn');
            const fileInput = document.getElementById('branchExcelFile');

            if (!fileInput.files || fileInput.files.length === 0) {
                alert('Please select an Excel file to upload.');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Importing...';

            const formData = new FormData();
            formData.append('excel_file', fileInput.files[0]);

            try {
                const response = await fetch(`/marketing/companies/${companyId}/branches/import-excel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.imported_count) {
                    alert('✓ ' + result.message);
                    const importModal = bootstrap.Modal.getInstance(document.getElementById('importBranchModal'));
                    if (importModal) importModal.hide();
                    
                    // Re-open edit company modal and load updated branches list
                    loadEditCompanyDetails(companyId);
                } else {
                    let errorMsg = result.message || 'Failed to import branches.';
                    alert('Import Error: ' + errorMsg);
                }
            } catch (error) {
                alert('An error occurred during import: ' + error.message);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-upload me-1"></i>Upload & Import';
            }
        });
    </script>
    @endpush
</x-app-layout>
