<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        /* Detailed Customer Form Styles */
        .customer-modal-header-info {
            padding: 1rem 1.5rem;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .customer-tab-container {
            display: flex;
            min-height: 500px;
        }

        .customer-nav-tabs {
            width: 180px;
            border-right: 1px solid #dee2e6;
            background: #f1f1f1;
            padding-top: 1rem;
        }

        .customer-nav-tabs .nav-link {
            border: none;
            border-radius: 0;
            text-align: left;
            padding: 10px 15px;
            color: #444;
            font-weight: 500;
            font-size: 0.9rem;
            border-left: 4px solid transparent;
        }

        .customer-nav-tabs .nav-link:hover {
            background: #e9e9e9;
        }

        .customer-nav-tabs .nav-link.active {
            background: #fff;
            color: #000;
            border-left-color: #ff0000;
            margin-right: -1px;
            border-bottom: 1px solid #dee2e6;
            border-top: 1px solid #dee2e6;
        }

        .customer-tab-content {
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

        .address-box-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .address-box {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fafafa;
        }

        .address-box textarea {
            width: 100%;
            height: 80px;
            border: 1px solid #eee;
            padding: 5px;
            font-size: 0.85rem;
        }

        .credit-card-section {
            border: 1px solid #dee2e6;
            padding: 1rem;
            border-radius: 4px;
            margin-top: 1rem;
        }

        .credit-card-label {
            position: relative;
            top: -22px;
            left: 10px;
            background: #fff;
            padding: 0 5px;
            font-weight: 700;
            font-size: 0.8rem;
            color: #666;
            display: inline-block;
        }

        /* Fix header action buttons icon styles */
        .customer-header-actions .btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.4rem !important;
            height: 38px !important;
            padding: 0.5rem 0.9rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            border-radius: 4px !important;
            box-shadow: none !important;
        }

        .customer-header-actions .btn i {
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            width: auto !important;
            height: auto !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
            display: inline-block !important;
            font-size: 1.15rem !important;
            line-height: 1 !important;
            color: inherit !important;
        }

        /* Customer Row Selection & Checkbox Styles */
        .customer-row {
            transition: background-color 0.2s ease;
        }
        .customer-row:hover {
            background-color: rgba(220, 53, 69, 0.04) !important;
        }
        .customer-row.selected-row {
            background-color: rgba(220, 53, 69, 0.08) !important;
        }
        .customer-select-col {
            width: 45px;
            text-align: center;
            vertical-align: middle !important;
        }
        .customer-select-cb {
            width: 18px;
            height: 18px;
            cursor: pointer;
            vertical-align: middle;
        }

        /* Floating Sticky Bulk Action Bar at Bottom of Screen */
        .bulk-floating-bar {
            position: fixed;
            bottom: 25px;
            left: 50%;
            transform: translateX(-50%);
            background: #1e1e2d;
            color: #ffffff;
            padding: 10px 22px;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            z-index: 1060;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .bulk-floating-bar.hidden {
            opacity: 0;
            visibility: hidden;
            transform: translate(-50%, 35px);
            pointer-events: none;
        }
    </style>
    @endpush

    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('marketing.customers'))
    <!-- Floating Sticky Bulk Action Bar -->
    <div id="bulkFloatingBar" class="bulk-floating-bar hidden">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger rounded-pill px-3 py-2 fs-13">
                <span id="floatingSelectedCount">0</span> selected
            </span>
            <span class="fw-medium text-white d-none d-sm-inline">customer(s) selected</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3" id="clearSelectionBtn">
                <i class="fas fa-times me-1"></i> Deselect
            </button>
            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold shadow-sm" id="floatingBulkDeleteBtn" style="background:#ff0000; border-color:#ff0000;">
                <i class="fa fa-trash me-1"></i> Delete Selected
            </button>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex">
                    <div>
                        <h4 class="fs-20 mb-0 text-black">Customer List</h4>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-3 mt-sm-0 customer-header-actions">
                        <input type="text" class="form-control me-2" placeholder="Search customers (press Enter)..."
                            id="customerSearch" value="{{ request('search') }}" style="max-width: 250px; height: 38px;">

                        <a href="javascript:void(0);"
                            class="btn btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#importCustomerModal"
                            title="Import Customers from Excel">
                            <i class="las la-file-upload"></i>
                            <span>Import Excel</span>
                        </a>
                        <a href="javascript:void(0);"
                            class="btn btn-danger text-white" data-bs-toggle="modal"
                            data-bs-target="#addCustomerModal"
                            style="background: #ff0000; border-color: #ff0000;">
                            <i class="las la-plus"></i>
                            <span>Add New Customer</span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('marketing.customers'))
                                    <th class="customer-select-col">
                                        <input type="checkbox" id="selectAllCustomers" class="form-check-input customer-select-cb" title="Select All">
                                    </th>
                                    @endif
                                    <th><strong>CUSTOMER ID</strong></th>
                                    <th><strong>CUSTOMER NAME</strong></th>
                                    <th><strong>PHONE NUMBER</strong></th>
                                    <th><strong>EMAIL</strong></th>
                                    <th><strong>ADDRESS</strong></th>
                                    <th><strong>STATUS</strong></th>
                                    <th><strong>ACTION</strong></th>
                                </tr>
                            </thead>
                            <tbody id="customerTableBody">
                              @forelse($customers as $customer)
                               <tr class="customer-row" data-customer-id="{{$customer->customer_id}}">
                                  @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('marketing.customers'))
                                  <td class="customer-select-col">
                                      <input type="checkbox" class="form-check-input customer-select-cb customer-row-cb" value="{{$customer->customer_id}}">
                                  </td>
                                  @endif
                                  <td><strong>{{$customer->customer_id}}</strong></td>
                                  <td>
                                    <div class="d-flex align-items-center">
                                      <span class="w-space-no" style="font-size: 13px;">{{$customer->customer_name}}</span>
                                    </div>
                                  </td>
                                  <td>{{$customer->mobile}}</td>
                                  <td>{{$customer->main_email}}</td>
                                  <td>{{$customer->shipping_address}}</td>
                                  <td>
                                      <div class="d-flex flex-column">
                                        @if($customer->is_bad_client)
                                          <span class="badge light badge-danger"><i class="fa fa-circle text-danger me-1"></i> Bad Client</span>
                                        @else
                                          <span class="badge light badge-success"><i class="fa fa-circle text-success me-1"></i> Good Client</span>
                                        @endif
                                        <small class="text-muted mt-1">Balance: ₱{{ number_format($customer->balance, 2) }}</small>
                                      </div>
                                  </td>
                                  <td>
                                      <div class="d-flex">
                                          @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('marketing.customers'))
                                          <a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1 edit-customer-btn"
                                              data-customer-id="{{$customer->customer_id}}">
                                              <i class="fas fa-pencil-alt"></i></a>
                                          @endif
                                          <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp me-1 view-history-btn"
                                              data-customer-id="{{$customer->customer_id}}"
                                              title="Transaction History"><i class="las la-history"></i></a>
                                          @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('marketing.customers'))
                                          <a href="javascript:void(0);" class="btn btn-danger shadow btn-xs sharp delete-customer-btn"
                                              data-customer-id="{{$customer->customer_id}}">
                                              <i class="fa fa-trash"></i></a>
                                          @endif
                                      </div>
                                  </td>
                              </tr>
                              @empty
                              <tr>
                                  <td colspan="8" class="text-center">No customers found.</td>  
                              </tr>
                              @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($customers->hasPages())
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 pt-3 border-top gap-2">
                        <div class="small text-muted">
                            Showing <strong>{{ $customers->firstItem() ?? 0 }}</strong> to <strong>{{ $customers->lastItem() ?? 0 }}</strong> of <strong>{{ $customers->total() }}</strong> customers
                        </div>
                        <div>
                            {{ $customers->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('modals')
    <!-- Import Customer Modal -->
    <div class="modal fade" id="importCustomerModal" tabindex="-1" aria-labelledby="importCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="importCustomerModalLabel"><i class="las la-file-excel me-2"></i>Import Customers from Excel</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="importCustomerForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info py-2 px-3 small">
                            <i class="fas fa-info-circle me-1"></i> Upload an Excel (<strong>.xlsx</strong>, <strong>.xls</strong>) or <strong>.csv</strong> file containing multiple customer records.
                        </div>

                        <div class="mb-3 text-end">
                            <a href="{{ route('marketing.customers.template') }}" class="btn btn-sm btn-outline-success">
                                <i class="las la-download me-1"></i> Download Sample Excel Template
                            </a>
                        </div>

                        <div class="mb-3">
                            <label for="customerExcelFile" class="form-label fw-bold small">Select Excel/CSV File <span class="text-danger">*</span></label>
                            <input class="form-control" type="file" id="customerExcelFile" name="file" accept=".xlsx,.xls,.csv" required>
                        </div>

                        <div id="importFeedback" class="d-none"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm" id="btnSubmitImport">
                            <i class="las la-file-upload me-1"></i> Upload & Import Customers
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerModalLabel">New Customer Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Global Header Info -->
                <div class="customer-modal-header-info">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="form-row-custom">
                                <label>COMPANY NAME <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="custNameInput" required>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-row-custom mb-0">
                                    <label style="width: auto;">OPENING BALANCE</label>
                                    <input type="number" class="form-control form-control-sm" style="width: 100px;"
                                        id="openingBalance">
                                </div>
                                <div class="form-row-custom mb-0">
                                    <label style="width: auto;">AS OF</label>
                                    <input type="date" class="form-control form-control-sm" id="asOfDate">
                                </div>
                                <div class="form-row-custom mb-0">
                                    <label style="width: auto;">CURRENCY</label>
                                    <select class="form-select form-select-sm" style="width: 150px;"
                                        id="currencySelect">
                                        <option value="PHP">Philippine peso</option>
                                        <option value="USD">US Dollar</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-body p-0">
                    <div class="customer-tab-container">
                        <!-- Vertical Tabs -->
                        <div class="nav flex-column nav-pills customer-nav-tabs" id="customer-tabs" role="tablist"
                            aria-orientation="vertical">
                            <button class="nav-link active" id="tab-address-link" data-bs-toggle="pill"
                                data-bs-target="#tab-address" type="button" role="tab">Address Info</button>
                            <button class="nav-link" id="tab-payment-link" data-bs-toggle="pill"
                                data-bs-target="#tab-payment" type="button" role="tab">Payment Settings</button>
                            <button class="nav-link" id="tab-additional-link" data-bs-toggle="pill"
                                data-bs-target="#tab-additional" type="button" role="tab">Additional Info</button>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content customer-tab-content" id="customer-tabs-content">
                            <!-- Address Info Tab -->
                            <div class="tab-pane fade show active" id="tab-address" role="tabpanel">
                                <form id="addressInfoForm">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="form-row-custom">
                                                <label>CUSTOMER NAME</label>
                                                <input type="text" class="form-control form-control-sm" id="companyName">
                                            </div>
                                            <div class="form-row-custom">
                                                <label>FULL NAME</label>
                                                <div class="d-flex gap-1 flex-1">
                                                    <input type="text" class="form-control form-control-sm"
                                                        style="width: 60px;" placeholder="Mr/Ms" id="titleName">
                                                    <input type="text" class="form-control form-control-sm"
                                                        placeholder="First" id="firstName">
                                                    <input type="text" class="form-control form-control-sm"
                                                        style="width: 40px;" placeholder="M.I" id="middleName">
                                                    <input type="text" class="form-control form-control-sm"
                                                        placeholder="Last" id="lastName">
                                                </div>
                                            </div>
                                            <div class="form-row-custom mt-3">
                                                 <label>JOB TITLE</label>
                                                 <input type="text" class="form-control form-control-sm" id="jobTitle">
                                             </div>
                                         </div>
                                     </div>

                                     <!-- Representatives Section -->
                                     <div class="row mt-3">
                                         <div class="col-12">
                                             <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                                                 <span class="fw-bold text-dark d-flex align-items-center" style="font-size: 0.85rem; letter-spacing: 0.3px;">
                                                     <i class="las la-user-tie text-danger me-1 fs-5"></i> REPRESENTATIVES
                                                 </span>
                                                 <button type="button" class="btn btn-outline-danger btn-xs px-3 rounded-pill fw-bold" id="addRepBtn" style="font-size: 0.75rem;">
                                                     <i class="las la-plus me-1"></i> Add Representative
                                                 </button>
                                             </div>
                                             
                                             <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                                                 <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.82rem; background: #fff;">
                                                     <thead style="background: #f8f9fa; color: #495057;">
                                                         <tr>
                                                             <th style="width: 28%; font-weight: 600;">Representative Name</th>
                                                             <th style="width: 25%; font-weight: 600;">Position / Role</th>
                                                             <th style="width: 22%; font-weight: 600;">Phone / Mobile</th>
                                                             <th style="width: 20%; font-weight: 600;">Email Address</th>
                                                             <th style="width: 5%; text-align: center;"></th>
                                                         </tr>
                                                     </thead>
                                                     <tbody id="representativesContainer">
                                                         <tr class="empty-rep-row">
                                                             <td colspan="5" class="text-center py-2 text-muted small">No representatives added yet. Click "+ Add Representative" above.</td>
                                                         </tr>
                                                     </tbody>
                                                 </table>
                                             </div>
                                         </div>
                                     </div>

                                    <div class="row mt-4">
                                        <!-- Contact Methods Column 1 -->
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Main Phone</option>
                                                    <option>Home Phone</option>
                                                </select>
                                                <input type="tel" class="form-control form-control-sm" id="mainPhone">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Work Phone</option>
                                                </select>
                                                <input type="tel" class="form-control form-control-sm" id="workPhone">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Mobile <span class="text-danger">*</span></option>
                                                </select>
                                                <input type="tel" class="form-control form-control-sm" id="mobilePhone" placeholder="Required" required>
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Fax</option>
                                                </select>
                                                <input type="tel" class="form-control form-control-sm" id="faxPhone">
                                            </div>
                                        </div>
                                        <!-- Contact Methods Column 2 -->
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Main Email</option>
                                                </select>
                                                <input type="email" class="form-control form-control-sm" id="mainEmail">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>CC Email</option>
                                                </select>
                                                <input type="email" class="form-control form-control-sm" id="ccEmail">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Website</option>
                                                </select>
                                                <input type="text" class="form-control form-control-sm" id="website">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Other 1</option>
                                                </select>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="otherContact">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="section-divider">Address Details</div>
                                    <div class="row g-3">
                                        <!-- Invoice / Bill To Column -->
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded border h-100">
                                                <label class="small fw-bold text-primary mb-2 d-block"><i class="las la-file-invoice me-1"></i>INVOICE / BILL TO</label>
                                                <div class="mb-2">
                                                    <input type="text" class="form-control form-control-sm mb-1" id="billAddr1" placeholder="Address Line 1 (Street / Barangay)">
                                                    <input type="text" class="form-control form-control-sm" id="billAddr2" placeholder="Address Line 2 (Building / Suite - Optional)">
                                                </div>
                                                <div class="row g-2">
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-sm" id="billCity" placeholder="Town / City">
                                                    </div>
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-sm" id="billProvince" placeholder="Province / Region">
                                                    </div>
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-sm" id="billCountry" placeholder="Country" value="Philippines">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Ship To Column -->
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded border h-100">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="small fw-bold text-danger mb-0"><i class="las la-truck me-1"></i>SHIP TO <span class="text-danger">*</span></label>
                                                    <button type="button" class="btn btn-link btn-xs text-decoration-none p-0 fw-bold" id="copyBillingToShippingBtn"><i class="las la-copy me-1"></i>Copy Billing</button>
                                                </div>
                                                <div class="mb-2">
                                                    <input type="text" class="form-control form-control-sm mb-1" id="shipAddr1" placeholder="Address Line 1 (Street / Barangay)" required>
                                                    <input type="text" class="form-control form-control-sm" id="shipAddr2" placeholder="Address Line 2 (Building / Suite - Optional)">
                                                </div>
                                                <div class="row g-2">
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-sm" id="shipCity" placeholder="Town / City" required>
                                                    </div>
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-sm" id="shipProvince" placeholder="Province / Region">
                                                    </div>
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-sm" id="shipCountry" placeholder="Country" value="Philippines">
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <input type="checkbox" id="defaultShipping" checked> <label for="defaultShipping" class="small mb-0">Default shipping address</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Payment Settings Tab -->
                            <div class="tab-pane fade" id="tab-payment" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-row-custom">
                                            <label>ACCOUNT NO.</label>
                                            <input type="text" class="form-control form-control-sm" id="accountNo">
                                        </div>
                                        <div class="form-row-custom">
                                            <label>PAYMENT TERMS</label>
                                            <select class="form-select form-select-sm" id="paymentTerms">
                                                <option>Net 15</option>
                                                <option>Net 30</option>
                                                <option>Net 60</option>
                                                <option>Due on receipt</option>
                                            </select>
                                        </div>
                                        <div class="form-row-custom">
                                            <label>PREFERRED DELIVERY METHOD</label>
                                            <select class="form-select form-select-sm" id="deliveryMethod">
                                                <option>Lazada</option>
                                                <option>Shopee</option>
                                                <option>Main Warehouse</option>
                                            </select>
                                        </div>
                                        <div class="form-row-custom">
                                            <label>PREFERRED PAYMENT METHOD</label>
                                            <select class="form-select form-select-sm" id="paymentMethod">
                                                <option>Check</option>
                                                <option>Cash</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-row-custom">
                                            <label>CREDIT LIMIT</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">PHP</span>
                                                <input type="number" class="form-control" id="creditLimitInput">
                                            </div>
                                        </div>
                                        <div class="form-row-custom">
                                            <label>PRICE LEVEL</label>
                                            <select class="form-select form-select-sm" id="priceLevel">
                                                <option>Standard</option>
                                                <option>Wholesale</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="credit-card-section">
                                    <div class="credit-card-label">CREDIT CARD INFORMATION</div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <label>CREDIT CARD NO.</label>
                                                <input type="text" class="form-control form-control-sm" id="ccNo">
                                            </div>
                                            <div class="form-row-custom">
                                                <label>EXP. DATE</label>
                                                <div class="d-flex gap-1 align-items-center flex-1">
                                                    <input type="text" class="form-control form-control-sm"
                                                        style="width: 50px;" id="ccExpMonth">
                                                    <span>/</span>
                                                    <input type="text" class="form-control form-control-sm"
                                                        style="width: 70px;" id="ccExpYear">
                                                </div>
                                            </div>
                                            <div class="form-row-custom">
                                                <label>NAME ON CARD</label>
                                                <input type="text" class="form-control form-control-sm" id="ccName">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <label>ADDRESS</label>
                                                <input type="text" class="form-control form-control-sm" id="ccAddress">
                                            </div>
                                            <div class="form-row-custom">
                                                <label>ZIP/POSTAL CODE</label>
                                                <input type="text" class="form-control form-control-sm" id="ccZip">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Info Tab -->
                            <div class="tab-pane fade" id="tab-additional" role="tabpanel">
                                <form id="additionalInfoForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <label>CUSTOMER TYPE</label>
                                                <div class="d-flex align-items-center gap-1 flex-1">
                                                    <select class="form-select form-select-sm" id="custType">
                                                        <option value="TEAM A">TEAM A</option>
                                                        <option value="TEAM B">TEAM B</option>
                                                        <option value="TEAM C">TEAM C</option>
                                                    </select>
                                                    <button type="button" class="btn btn-outline-primary btn-xs py-1 px-2 text-nowrap" onclick="addNewDropdownOption('custType', 'Customer Type')" title="Add New Customer Type"><i class="fas fa-plus"></i></button>
                                                </div>
                                            </div>
                                            <div class="form-row-custom">
                                                <label>REP</label>
                                                <div class="d-flex align-items-center gap-1 flex-1">
                                                    <select class="form-select form-select-sm" id="custRep">
                                                        <option value="CLE">CLE</option>
                                                        <option value="MKT">MKT</option>
                                                    </select>
                                                    <button type="button" class="btn btn-outline-primary btn-xs py-1 px-2 text-nowrap" onclick="addNewDropdownOption('custRep', 'Rep')" title="Add New Rep"><i class="fas fa-plus"></i></button>
                                                </div>
                                            </div>
                                            <div class="form-row-custom">
                                                <label>CLASS</label>
                                                <div class="d-flex align-items-center gap-1 flex-1">
                                                    <select class="form-select form-select-sm" id="custClass">
                                                        <option value="LAG">LAG</option>
                                                        <option value="MNL">MNL</option>
                                                    </select>
                                                    <button type="button" class="btn btn-outline-primary btn-xs py-1 px-2 text-nowrap" onclick="addNewDropdownOption('custClass', 'Class')" title="Add New Class"><i class="fas fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="section-divider mt-0">CUSTOM FIELDS</div>
                                            <div class="form-row-custom">
                                                <label>CONTACT PERSON</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="customContactPerson">
                                            </div>
                                            <div class="form-row-custom">
                                                <label>CUSTOMER</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="customCustField">
                                            </div>
                                            <div class="text-end mt-4">
                                                <button type="button" class="btn btn-sm btn-light border">Define
                                                    Fields</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <div>
                        <input type="checkbox" id="customerInactive"> <label for="customerInactive"
                            class="small mb-0">Customer is inactive</label>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary px-4" id="saveCustomerBtn">OK</button>
                        <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History Modal -->
    <div class="modal fade" id="transactionHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white"><i class="las la-history me-2"></i>Transaction History - <span id="historyCustomerName"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="mb-0">Current Balance: <span class="text-primary" id="historyBalance">₱0.00</span></h4>
                            <div id="historyStatusBadge" class="mt-1"></div>
                        </div>
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('marketing.customers'))
                        <div class="d-flex align-items-center gap-2">
                            <label class="small fw-bold mb-0">Override Status:</label>
                            <select class="form-select form-select-sm" id="manualStatusOverride" style="width: 150px;">
                                <option value="">Auto (System)</option>
                                <option value="good">Force Good</option>
                                <option value="bad">Force Bad</option>
                            </select>
                            <button class="btn btn-primary btn-xs" id="updateManualStatusBtn">Apply</button>
                        </div>
                        @endif
                    </div>

                    <!-- Search and Filtering Controls -->
                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-md-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light"><i class="las la-search"></i></span>
                                <input type="text" id="historySearchInput" class="form-control form-control-sm" placeholder="Search SO or SI number...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select id="historyStatusFilter" class="form-select form-select-sm">
                                <option value="">All Statuses (Paid & Unpaid)</option>
                                <option value="paid">Paid</option>
                                <option value="unpaid">Unpaid</option>
                                <option value="completed">Completed Orders</option>
                                <option value="overdue">Overdue</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3 text-end">
                            <select id="historyPerPage" class="form-select form-select-sm d-inline-block w-auto">
                                <option value="5">5 / page</option>
                                <option value="10" selected>10 / page</option>
                                <option value="25">25 / page</option>
                                <option value="50">50 / page</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr class="bg-light">
                                    <th>Date</th>
                                    <th>Transaction #</th>
                                    <th>Total Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Remaining</th>
                                    <th>Due Date</th>
                                    <th>Order Status</th>
                                    <th>Payment Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody">
                                <!-- Populated by AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3" id="historyPaginationContainer">
                        <div class="small text-muted" id="historyPaginationInfo">
                            Showing 0 entries
                        </div>
                        <nav aria-label="Transaction pagination">
                            <ul class="pagination pagination-sm mb-0" id="historyPaginationList">
                                <!-- Populated by AJAX -->
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Record Payment Modal -->
    <div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white"><i class="las la-money-bill-wave me-2"></i>Payment History & Record Installment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="recordPaymentForm">
                    <div class="modal-body">
                        <input type="hidden" id="paySoId">
                        
                        <div class="alert alert-light border mb-3">
                            <div class="row g-2 text-center text-md-start">
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Transaction #:</span>
                                    <strong id="paySoNumber" class="text-dark">SO-0000</strong>
                                </div>
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Terms:</span>
                                    <span id="payTerms" class="badge bg-info text-white fw-semibold">COD</span>
                                </div>
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Due Date:</span>
                                    <strong id="payDueDate" class="text-dark">N/A</strong>
                                </div>
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Grand Total:</span>
                                    <strong id="payTotalAmount" class="text-dark">₱0.00</strong>
                                </div>
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Already Paid:</span>
                                    <span id="payAlreadyPaid" class="text-success fw-bold">₱0.00</span>
                                </div>
                                <div class="col-6 col-md-2">
                                    <span class="text-muted small d-block">Remaining:</span>
                                    <strong id="payRemainingBalance" class="text-danger fs-16">₱0.00</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Payment History Breakdown Table -->
                        <div class="card mb-3 border">
                            <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                                <span class="fw-bold small text-dark"><i class="las la-history me-1 text-primary"></i> Previous Installments Log</span>
                                <span class="badge bg-secondary" id="payHistoryBadge">0 payments</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 180px; overflow-y: auto;">
                                    <table class="table table-sm table-striped table-bordered mb-0 align-middle" style="font-size: 11px;">
                                        <thead class="bg-light sticky-top">
                                            <tr>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Method</th>
                                                <th>Ref # / Check #</th>
                                                <th>Notes</th>
                                                <th>Proof</th>
                                                <th>Recorded By</th>
                                            </tr>
                                        </thead>
                                        <tbody id="payHistoryTableBody">
                                            <tr><td colspan="7" class="text-center py-2 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Loading payment history...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- New Installment Entry Form -->
                        <div id="newPaymentFormFields">
                            <h6 class="fw-bold text-dark border-bottom pb-1 mb-3"><i class="las la-plus-circle me-1 text-success"></i> Add New Installment Payment</h6>

                            <div class="row g-2">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Payment Amount (₱) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" step="0.01" min="0.01" id="payAmountInput" class="form-control fw-bold fs-15 text-primary" required placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Payment Method <span class="text-danger">*</span></label>
                                    <select id="payMethodSelect" class="form-select form-select-sm" required>
                                        <option value="cash">Cash</option>
                                        <option value="gcash">GCash</option>
                                        <option value="maya">Maya</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="check">Check</option>
                                        <option value="card">Credit / Debit Card</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Reference / Check # <span class="text-muted fw-normal">(Optional)</span></label>
                                    <input type="text" id="payRefInput" class="form-control form-control-sm" placeholder="e.g. Ref #123456 or Check #">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Notes / Remarks <span class="text-muted fw-normal">(Optional)</span></label>
                                    <input type="text" id="payNotesInput" class="form-control form-control-sm" placeholder="e.g. 1st installment payment">
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label class="form-label fw-bold small text-dark">Proof of Payment <span class="text-muted fw-normal">(Optional - Image/PDF)</span></label>
                                    <input type="file" id="payProofInput" class="form-control form-control-sm" accept="image/*,.pdf">
                                </div>
                            </div>
                        </div>

                        <div id="fullyPaidNotice" class="alert alert-success d-none text-center py-2 mb-0">
                            <i class="las la-check-circle me-1 fs-16"></i> This order is fully paid. No further payments required.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" id="submitPaymentBtn">
                            <i class="las la-check-circle me-1"></i> Submit Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Global Header Info -->
                <div class="customer-modal-header-info">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="form-row-custom">
                                <label>COMPANY NAME <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="editCustNameInput" required>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-row-custom mb-0">
                                    <label style="width: auto;">OPENING BALANCE</label>
                                    <input type="number" class="form-control form-control-sm" style="width: 100px;"
                                        id="editOpeningBalance">
                                </div>
                                <div class="form-row-custom mb-0">
                                    <label style="width: auto;">AS OF</label>
                                    <input type="date" class="form-control form-control-sm" id="editAsOfDate">
                                </div>
                                <div class="form-row-custom mb-0">
                                    <label style="width: auto;">CURRENCY</label>
                                    <select class="form-select form-select-sm" style="width: 150px;"
                                        id="editCurrencySelect">
                                        <option value="PHP">Philippine peso</option>
                                        <option value="USD">US Dollar</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-body p-0">
                    <div class="customer-tab-container">
                        <!-- Vertical Tabs -->
                        <div class="nav flex-column nav-pills customer-nav-tabs" id="edit-customer-tabs" role="tablist"
                            aria-orientation="vertical">
                            <button class="nav-link active" id="edit-tab-address-link" data-bs-toggle="pill"
                                data-bs-target="#edit-tab-address" type="button" role="tab">Address Info</button>
                            <button class="nav-link" id="edit-tab-payment-link" data-bs-toggle="pill"
                                data-bs-target="#edit-tab-payment" type="button" role="tab">Payment Settings</button>
                            <button class="nav-link" id="edit-tab-additional-link" data-bs-toggle="pill"
                                data-bs-target="#edit-tab-additional" type="button" role="tab">Additional Info</button>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content customer-tab-content" id="edit-customer-tabs-content">
                            <!-- Address Info Tab -->
                            <div class="tab-pane fade show active" id="edit-tab-address" role="tabpanel">
                                <form id="editAddressInfoForm">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="form-row-custom">
                                                <label>CUSTOMER NAME</label>
                                                <input type="text" class="form-control form-control-sm" id="editCompanyName">
                                            </div>
                                            <div class="form-row-custom">
                                                <label>FULL NAME</label>
                                                <div class="d-flex gap-1 flex-1">
                                                    <input type="text" class="form-control form-control-sm"
                                                        style="width: 60px;" placeholder="Mr/Ms" id="editTitleName">
                                                    <input type="text" class="form-control form-control-sm"
                                                        placeholder="First" id="editFirstName">
                                                    <input type="text" class="form-control form-control-sm"
                                                        style="width: 40px;" placeholder="M.I" id="editMiddleName">
                                                    <input type="text" class="form-control form-control-sm"
                                                        placeholder="Last" id="editLastName">
                                                </div>
                                            </div>
                                            <div class="form-row-custom mt-3">
                                                 <label>JOB TITLE</label>
                                                 <input type="text" class="form-control form-control-sm" id="editJobTitle">
                                             </div>
                                         </div>
                                     </div>

                                     <!-- Representatives Section -->
                                     <div class="row mt-3">
                                         <div class="col-12">
                                             <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                                                 <span class="fw-bold text-dark d-flex align-items-center" style="font-size: 0.85rem; letter-spacing: 0.3px;">
                                                     <i class="las la-user-tie text-danger me-1 fs-5"></i> REPRESENTATIVES
                                                 </span>
                                                 <button type="button" class="btn btn-outline-danger btn-xs px-3 rounded-pill fw-bold" id="editAddRepBtn" style="font-size: 0.75rem;">
                                                     <i class="las la-plus me-1"></i> Add Representative
                                                 </button>
                                             </div>
                                             
                                             <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                                                 <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.82rem; background: #fff;">
                                                     <thead style="background: #f8f9fa; color: #495057;">
                                                         <tr>
                                                             <th style="width: 28%; font-weight: 600;">Representative Name</th>
                                                             <th style="width: 25%; font-weight: 600;">Position / Role</th>
                                                             <th style="width: 22%; font-weight: 600;">Phone / Mobile</th>
                                                             <th style="width: 20%; font-weight: 600;">Email Address</th>
                                                             <th style="width: 5%; text-align: center;"></th>
                                                         </tr>
                                                     </thead>
                                                     <tbody id="editRepresentativesContainer">
                                                         <tr class="empty-rep-row">
                                                             <td colspan="5" class="text-center py-2 text-muted small">No representatives added yet. Click "+ Add Representative" above.</td>
                                                         </tr>
                                                     </tbody>
                                                 </table>
                                             </div>
                                         </div>
                                     </div>

                                    <div class="row mt-4">
                                        <!-- Contact Methods Column 1 -->
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Main Phone</option>
                                                    <option>Home Phone</option>
                                                </select>
                                                <input type="tel" class="form-control form-control-sm" id="editMainPhone">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Work Phone</option>
                                                </select>
                                                <input type="tel" class="form-control form-control-sm" id="editWorkPhone">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Mobile <span class="text-danger">*</span></option>
                                                </select>
                                                <input type="tel" class="form-control form-control-sm" id="editMobilePhone" placeholder="Required" required>
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Fax</option>
                                                </select>
                                                <input type="tel" class="form-control form-control-sm" id="editFaxPhone">
                                            </div>
                                        </div>
                                        <!-- Contact Methods Column 2 -->
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Main Email</option>
                                                </select>
                                                <input type="email" class="form-control form-control-sm" id="editMainEmail">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>CC Email</option>
                                                </select>
                                                <input type="email" class="form-control form-control-sm" id="editCcEmail">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Website</option>
                                                </select>
                                                <input type="text" class="form-control form-control-sm" id="editWebsite">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Other 1</option>
                                                </select>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="editOtherContact">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="section-divider">Address Details</div>
                                    <div class="row g-3">
                                        <!-- Invoice / Bill To Column -->
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded border h-100">
                                                <label class="small fw-bold text-primary mb-2 d-block"><i class="las la-file-invoice me-1"></i>INVOICE / BILL TO</label>
                                                <div class="mb-2">
                                                    <input type="text" class="form-control form-control-sm mb-1" id="editBillAddr1" placeholder="Address Line 1 (Street / Barangay)">
                                                    <input type="text" class="form-control form-control-sm" id="editBillAddr2" placeholder="Address Line 2 (Building / Suite - Optional)">
                                                </div>
                                                <div class="row g-2">
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-sm" id="editBillCity" placeholder="Town / City">
                                                    </div>
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-sm" id="editBillProvince" placeholder="Province / Region">
                                                    </div>
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-sm" id="editBillCountry" placeholder="Country" value="Philippines">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Ship To Column -->
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded border h-100">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="small fw-bold text-danger mb-0"><i class="las la-truck me-1"></i>SHIP TO <span class="text-danger">*</span></label>
                                                    <button type="button" class="btn btn-link btn-xs text-decoration-none p-0 fw-bold" id="editCopyBillingToShippingBtn"><i class="las la-copy me-1"></i>Copy Billing</button>
                                                </div>
                                                <div class="mb-2">
                                                    <input type="text" class="form-control form-control-sm mb-1" id="editShipAddr1" placeholder="Address Line 1 (Street / Barangay)" required>
                                                    <input type="text" class="form-control form-control-sm" id="editShipAddr2" placeholder="Address Line 2 (Building / Suite - Optional)">
                                                </div>
                                                <div class="row g-2">
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-sm" id="editShipCity" placeholder="Town / City" required>
                                                    </div>
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-sm" id="editShipProvince" placeholder="Province / Region">
                                                    </div>
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-sm" id="editShipCountry" placeholder="Country" value="Philippines">
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <input type="checkbox" id="editDefaultShipping" checked> <label for="editDefaultShipping" class="small mb-0">Default shipping address</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Payment Settings Tab -->
                            <div class="tab-pane fade" id="edit-tab-payment" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-row-custom">
                                            <label>ACCOUNT NO.</label>
                                            <input type="text" class="form-control form-control-sm" id="editAccountNo">
                                        </div>
                                        <div class="form-row-custom">
                                            <label>PAYMENT TERMS</label>
                                            <select class="form-select form-select-sm" id="editPaymentTerms">
                                                <option>Net 15</option>
                                                <option>Net 30</option>
                                                <option>Net 60</option>
                                                <option>Due on receipt</option>
                                            </select>
                                        </div>
                                        <div class="form-row-custom">
                                            <label>PREFERRED DELIVERY METHOD</label>
                                            <select class="form-select form-select-sm" id="editDeliveryMethod">
                                                <option>Lazada</option>
                                                <option>Shopee</option>
                                                <option>Main Warehouse</option>
                                            </select>
                                        </div>
                                        <div class="form-row-custom">
                                            <label>PREFERRED PAYMENT METHOD</label>
                                            <select class="form-select form-select-sm" id="editPaymentMethod">
                                                <option>Check</option>
                                                <option>Cash</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-row-custom">
                                            <label>CREDIT LIMIT</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">PHP</span>
                                                <input type="number" class="form-control" id="editCreditLimitInput">
                                            </div>
                                        </div>
                                        <div class="form-row-custom">
                                            <label>PRICE LEVEL</label>
                                            <select class="form-select form-select-sm" id="editPriceLevel">
                                                <option>Standard</option>
                                                <option>Wholesale</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="credit-card-section">
                                    <div class="credit-card-label">CREDIT CARD INFORMATION</div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <label>CREDIT CARD NO.</label>
                                                <input type="text" class="form-control form-control-sm" id="editCcNo">
                                            </div>
                                            <div class="form-row-custom">
                                                <label>EXP. DATE</label>
                                                <div class="d-flex gap-1 align-items-center flex-1">
                                                    <input type="text" class="form-control form-control-sm"
                                                        style="width: 50px;" id="editCcExpMonth">
                                                    <span>/</span>
                                                    <input type="text" class="form-control form-control-sm"
                                                        style="width: 70px;" id="editCcExpYear">
                                                </div>
                                            </div>
                                            <div class="form-row-custom">
                                                <label>NAME ON CARD</label>
                                                <input type="text" class="form-control form-control-sm" id="editCcName">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <label>ADDRESS</label>
                                                <input type="text" class="form-control form-control-sm" id="editCcAddress">
                                            </div>
                                            <div class="form-row-custom">
                                                <label>ZIP/POSTAL CODE</label>
                                                <input type="text" class="form-control form-control-sm" id="editCcZip">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Info Tab -->
                            <div class="tab-pane fade" id="edit-tab-additional" role="tabpanel">
                                <form id="editAdditionalInfoForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <label>CUSTOMER TYPE</label>
                                                <div class="d-flex align-items-center gap-1 flex-1">
                                                    <select class="form-select form-select-sm" id="editCustType">
                                                        <option value="TEAM A">TEAM A</option>
                                                        <option value="TEAM B">TEAM B</option>
                                                        <option value="TEAM C">TEAM C</option>
                                                    </select>
                                                    <button type="button" class="btn btn-outline-primary btn-xs py-1 px-2 text-nowrap" onclick="addNewDropdownOption('editCustType', 'Customer Type')" title="Add New Customer Type"><i class="fas fa-plus"></i></button>
                                                </div>
                                            </div>
                                            <div class="form-row-custom">
                                                <label>REP</label>
                                                <div class="d-flex align-items-center gap-1 flex-1">
                                                    <select class="form-select form-select-sm" id="editCustRep">
                                                        <option value="CLE">CLE</option>
                                                        <option value="MKT">MKT</option>
                                                    </select>
                                                    <button type="button" class="btn btn-outline-primary btn-xs py-1 px-2 text-nowrap" onclick="addNewDropdownOption('editCustRep', 'Rep')" title="Add New Rep"><i class="fas fa-plus"></i></button>
                                                </div>
                                            </div>
                                            <div class="form-row-custom">
                                                <label>CLASS</label>
                                                <div class="d-flex align-items-center gap-1 flex-1">
                                                    <select class="form-select form-select-sm" id="editCustClass">
                                                        <option value="LAG">LAG</option>
                                                        <option value="MNL">MNL</option>
                                                    </select>
                                                    <button type="button" class="btn btn-outline-primary btn-xs py-1 px-2 text-nowrap" onclick="addNewDropdownOption('editCustClass', 'Class')" title="Add New Class"><i class="fas fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="section-divider mt-0">CUSTOM FIELDS</div>
                                            <div class="form-row-custom">
                                                <label>CONTACT PERSON</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="editCustomContactPerson">
                                            </div>
                                            <div class="form-row-custom">
                                                <label>CUSTOMER</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="editCustomCustField">
                                            </div>
                                            <div class="text-end mt-4">
                                                <button type="button" class="btn btn-sm btn-light border">Define
                                                    Fields</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <input type="checkbox" id="editCustomerInactive"> <label for="editCustomerInactive"
                                class="small mb-0">Customer is inactive</label>
                        </div>
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('marketing.customers'))
                        <div class="d-flex align-items-center gap-2 border-start ps-3">
                            <label class="small fw-bold mb-0">Manual Status:</label>
                            <select class="form-select form-select-sm" id="editManualStatus" style="width: 120px;">
                                <option value="">Auto</option>
                                <option value="good">Good</option>
                                <option value="bad">Bad</option>
                            </select>
                        </div>
                        @endif
                    </div>
                    <div>
                        <input type="hidden" id="editCustomerId">
                        <button type="button" class="btn btn-primary px-4" id="updateCustomerBtn">Save Changes</button>
                        <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-light px-4 border">Help</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete this customer? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Save Changes Modal -->
    <div class="modal fade" id="confirmSaveModal" tabindex="-1" aria-labelledby="confirmSaveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmSaveModalLabel">
                        <i class="fas fa-question-circle text-primary me-2"></i>Confirm Changes
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to save the changes to this customer?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmSaveBtn">
                        <i class="fas fa-check me-1"></i>Yes
                    </button>
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
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="mb-2">Success!</h4>
                    <p class="text-muted mb-0" id="successMessage">Customer updated successfully!</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-0">
                    <button type="button" class="btn btn-primary px-4" id="successOkBtn">OK</button>
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
                        <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
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
    @endpush

    @push('scripts')
    <script>
        window.addNewDropdownOption = function(selectId, labelName) {
            const val = prompt('Enter new ' + labelName + ' name:');
            if (val && val.trim()) {
                const cleanVal = val.trim();
                const select = document.getElementById(selectId);
                if (select) {
                    let exists = false;
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].value.toLowerCase() === cleanVal.toLowerCase()) {
                            select.selectedIndex = i;
                            exists = true;
                            break;
                        }
                    }
                    if (!exists) {
                        const opt = new Option(cleanVal, cleanVal, true, true);
                        select.add(opt);
                    }
                }
            }
        };

        window.setSelectOrAddOption = function(selectId, value, defaultVal) {
            const select = document.getElementById(selectId);
            if (!select) return;
            const targetVal = value || defaultVal || '';
            if (!targetVal) return;
            let found = false;
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].value.toLowerCase() === targetVal.toLowerCase()) {
                    select.selectedIndex = i;
                    found = true;
                    break;
                }
            }
            if (!found) {
                const newOpt = new Option(targetVal, targetVal, true, true);
                select.add(newOpt);
            }
        };

        window.renderRepRow = function(containerId, rep = {}) {
            const container = document.getElementById(containerId);
            if (!container) return;
            
            const emptyRow = container.querySelector('.empty-rep-row');
            if (emptyRow) {
                emptyRow.remove();
            }

            const rowId = 'rep_row_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            const tr = document.createElement('tr');
            tr.className = 'rep-row';
            tr.id = rowId;
            tr.innerHTML = `
                <td class="p-1">
                    <input type="text" class="form-control form-control-sm rep-name border-light-gray" placeholder="e.g. John Doe" value="${rep.name || rep.rep_name || ''}">
                </td>
                <td class="p-1">
                    <input type="text" class="form-control form-control-sm rep-position border-light-gray" placeholder="e.g. Account Manager" value="${rep.position || rep.rep_position || ''}">
                </td>
                <td class="p-1">
                    <input type="tel" class="form-control form-control-sm rep-phone border-light-gray" placeholder="e.g. 09171234567" value="${rep.phone || rep.rep_phone || ''}">
                </td>
                <td class="p-1">
                    <input type="email" class="form-control form-control-sm rep-email border-light-gray" placeholder="e.g. rep@company.com" value="${rep.email || rep.rep_email || ''}">
                </td>
                <td class="p-1 text-center align-middle">
                    <button type="button" class="btn btn-link text-danger p-0 m-0 remove-rep-btn" title="Remove Representative" onclick="removeRepRow('${containerId}', '${rowId}')">
                        <i class="las la-trash-alt fs-5"></i>
                    </button>
                </td>
            `;
            container.appendChild(tr);
        };

        window.removeRepRow = function(containerId, rowId) {
            const row = document.getElementById(rowId);
            if (row) row.remove();
            
            const container = document.getElementById(containerId);
            if (container && container.querySelectorAll('.rep-row').length === 0) {
                container.innerHTML = `<tr class="empty-rep-row"><td colspan="5" class="text-center py-2 text-muted small">No representatives added yet. Click "+ Add Representative" above.</td></tr>`;
            }
        };

        window.getRepresentativesFromContainer = function(containerId) {
            const container = document.getElementById(containerId);
            if (!container) return [];
            const rows = container.querySelectorAll('.rep-row');
            const reps = [];
            rows.forEach(row => {
                const name = row.querySelector('.rep-name')?.value?.trim() || '';
                const position = row.querySelector('.rep-position')?.value?.trim() || '';
                const phone = row.querySelector('.rep-phone')?.value?.trim() || '';
                const email = row.querySelector('.rep-email')?.value?.trim() || '';
                if (name || position || phone || email) {
                    reps.push({ name, position, phone, email });
                }
            });
            return reps;
        };

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('addRepBtn')?.addEventListener('click', function() {
                renderRepRow('representativesContainer');
            });
            document.getElementById('editAddRepBtn')?.addEventListener('click', function() {
                renderRepRow('editRepresentativesContainer');
            });
        });

        // Authorization check
        const userPosition = '{{ auth()->user()->position ?? "" }}';
        const canEditCustomers = {{ auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('marketing.customers') ? 'true' : 'false' }};
        const isAdmin = userPosition === 'Super Admin' || canEditCustomers;

        // Search Functionality
        document.getElementById('customerSearch')?.addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#customerTableBody tr:not(#noResultsRow)');
            
            let visibleCount = 0;
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const isMatch = text.includes(query);
                row.style.display = isMatch ? '' : 'none';
                if (isMatch) visibleCount++;
            });
            
            // Handle "No results" message
            let noResultsRow = document.getElementById('noResultsRow');
            if (visibleCount === 0 && query !== '') {
                if (!noResultsRow) {
                    const tbody = document.getElementById('customerTableBody');
                    noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'noResultsRow';
                    noResultsRow.innerHTML = `<td colspan="7" class="text-center text-muted py-4">No customers matching "${this.value}" found.</td>`;
                    tbody.appendChild(noResultsRow);
                } else {
                    noResultsRow.style.display = '';
                    noResultsRow.querySelector('td').textContent = `No customers matching "${this.value}" found.`;
                }
            } else if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
        });

        // Copy Billing to Shipping button handlers
        document.getElementById('copyBillingToShippingBtn')?.addEventListener('click', function() {
            document.getElementById('shipAddr1').value = document.getElementById('billAddr1')?.value || '';
            document.getElementById('shipAddr2').value = document.getElementById('billAddr2')?.value || '';
            document.getElementById('shipCity').value = document.getElementById('billCity')?.value || '';
            document.getElementById('shipProvince').value = document.getElementById('billProvince')?.value || '';
            document.getElementById('shipCountry').value = document.getElementById('billCountry')?.value || 'Philippines';
        });

        document.getElementById('editCopyBillingToShippingBtn')?.addEventListener('click', function() {
            document.getElementById('editShipAddr1').value = document.getElementById('editBillAddr1')?.value || '';
            document.getElementById('editShipAddr2').value = document.getElementById('editBillAddr2')?.value || '';
            document.getElementById('editShipCity').value = document.getElementById('editBillCity')?.value || '';
            document.getElementById('editShipProvince').value = document.getElementById('editBillProvince')?.value || '';
            document.getElementById('editShipCountry').value = document.getElementById('editBillCountry')?.value || 'Philippines';
        });

        // Save Customer via AJAX
        document.getElementById('saveCustomerBtn')?.addEventListener('click', async function() {
            const btn = this;
            btn.disabled = true;
            btn.textContent = 'Saving...';

            const billParts = [
                document.getElementById('billAddr1')?.value,
                document.getElementById('billAddr2')?.value,
                document.getElementById('billCity')?.value,
                document.getElementById('billProvince')?.value,
                document.getElementById('billCountry')?.value
            ].filter(Boolean);
            const billingAddressVal = billParts.join(', ') || 'N/A';

            const shipParts = [
                document.getElementById('shipAddr1')?.value,
                document.getElementById('shipAddr2')?.value,
                document.getElementById('shipCity')?.value,
                document.getElementById('shipProvince')?.value,
                document.getElementById('shipCountry')?.value
            ].filter(Boolean);
            const shippingAddressVal = shipParts.join(', ') || billingAddressVal || 'N/A';

            const data = {
                customer_name: document.getElementById('custNameInput')?.value || '',
                company_name: document.getElementById('companyName')?.value || '',
                opening_balance: document.getElementById('openingBalance')?.value || null,
                opening_balance_date: document.getElementById('asOfDate')?.value || null,
                currency_code: document.getElementById('currencySelect')?.value || null,
                title: document.getElementById('titleName')?.value || null,
                first_name: document.getElementById('firstName')?.value || null,
                middle_initial: document.getElementById('middleName')?.value || null,
                last_name: document.getElementById('lastName')?.value || null,
                job_title: document.getElementById('jobTitle')?.value || null,
                main_phone: document.getElementById('mainPhone')?.value || null,
                work_phone: document.getElementById('workPhone')?.value || null,
                mobile: document.getElementById('mobilePhone')?.value || null,
                fax: document.getElementById('faxPhone')?.value || null,
                main_email: document.getElementById('mainEmail')?.value || null,
                cc_email: document.getElementById('ccEmail')?.value || null,
                website: document.getElementById('website')?.value || null,
                other_contact: document.getElementById('otherContact')?.value || null,
                billing_address: billingAddressVal,
                billing_address_line1: document.getElementById('billAddr1')?.value || null,
                billing_address_line2: document.getElementById('billAddr2')?.value || null,
                billing_city: document.getElementById('billCity')?.value || null,
                billing_province: document.getElementById('billProvince')?.value || null,
                billing_country: document.getElementById('billCountry')?.value || 'Philippines',
                shipping_address: shippingAddressVal,
                shipping_address_line1: document.getElementById('shipAddr1')?.value || null,
                shipping_address_line2: document.getElementById('shipAddr2')?.value || null,
                shipping_city: document.getElementById('shipCity')?.value || null,
                shipping_province: document.getElementById('shipProvince')?.value || null,
                shipping_country: document.getElementById('shipCountry')?.value || 'Philippines',
                is_default_shipping: document.getElementById('defaultShipping')?.checked ? 1 : 0,
                account_number: document.getElementById('accountNo')?.value || null,
                payment_terms: document.getElementById('paymentTerms')?.value || null,
                preferred_delivery_method: document.getElementById('deliveryMethod')?.value || null,
                preferred_payment_method: document.getElementById('paymentMethod')?.value?.toLowerCase() || null,
                credit_limit: document.getElementById('creditLimitInput')?.value || null,
                price_level: document.getElementById('priceLevel')?.value?.toLowerCase() || null,
                card_number_last4: document.getElementById('ccNo')?.value?.slice(-4) || null,
                card_exp_month: document.getElementById('ccExpMonth')?.value || null,
                card_exp_year: document.getElementById('ccExpYear')?.value || null,
                card_name: document.getElementById('ccName')?.value || null,
                card_billing_address: document.getElementById('ccAddress')?.value || null,
                card_zip: document.getElementById('ccZip')?.value || null,
                customer_type: document.getElementById('custType')?.value || null,
                rep: document.getElementById('custRep')?.value || null,
                class: document.getElementById('custClass')?.value || null,
                custom_contact_person: document.getElementById('customContactPerson')?.value || null,
                custom_customer_field: document.getElementById('customCustField')?.value || null,
                representatives: getRepresentativesFromContainer('representativesContainer'),
                is_inactive: document.getElementById('customerInactive')?.checked ? 1 : 0,
            };

            try {
                const response = await fetch('{{ route("marketing.customers.store") }}', {
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
                    bootstrap.Modal.getInstance(document.getElementById('addCustomerModal')).hide();
                    
                    // Show success modal
                    document.getElementById('successMessage').textContent = 'Customer saved successfully!';
                    const successModalElement = document.getElementById('successModal');
                    const successModal = new bootstrap.Modal(successModalElement);
                    successModal.show();
                    
                    const reloadWithNewCustomer = function() {
                        window.location.href = '{{ route("marketing.customers") }}';
                    };
                    
                    document.getElementById('successOkBtn').onclick = reloadWithNewCustomer;
                    successModalElement.addEventListener('hidden.bs.modal', reloadWithNewCustomer, { once: true });
                } else {
                    let errorMsg = result.message || 'Failed to save customer.';
                    if (result.errors) {
                        errorMsg = Object.values(result.errors).flat().join('\n');
                    }
                    
                    // Show error modal
                    document.getElementById('errorMessage').textContent = errorMsg;
                    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                }
            } catch (error) {
                // Show error modal
                document.getElementById('errorMessage').textContent = 'An error occurred: ' + error.message;
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            } finally {
                btn.disabled = false;
                btn.textContent = 'Save Customer';
            }
        });



        // Open Edit Modal and fetch customer data
        document.querySelectorAll('.edit-customer-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (!canEditCustomers) {
                    alert('You do not have permission to edit customers. Only Marketing users can edit.');
                    return;
                }
                const customerId = this.dataset.customerId;
                
                try {
                    const response = await fetch(`/marketing/customers/${customerId}/edit`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to fetch customer data');
                    }

                    const customer = await response.json();

                    // Populate the edit modal with customer data
                    document.getElementById('editCustomerId').value = customer.customer_id;
                    document.getElementById('editCustNameInput').value = customer.customer_name || '';
                    document.getElementById('editOpeningBalance').value = customer.opening_balance || '';
                    
                    // Format opening_balance_date to YYYY-MM-DD for HTML5 date input
                    let formattedAsOfDate = '';
                    if (customer.opening_balance_date) {
                        const dateStr = String(customer.opening_balance_date);
                        if (dateStr.includes('T')) {
                            formattedAsOfDate = dateStr.split('T')[0];
                        } else if (dateStr.includes(' ')) {
                            formattedAsOfDate = dateStr.split(' ')[0];
                        } else if (dateStr.length >= 10) {
                            formattedAsOfDate = dateStr.substring(0, 10);
                        }
                    }
                    document.getElementById('editAsOfDate').value = formattedAsOfDate;
                    document.getElementById('editCurrencySelect').value = customer.currency_code || 'PHP';
                    document.getElementById('editCompanyName').value = customer.company_name || '';
                    document.getElementById('editTitleName').value = customer.title || '';
                    document.getElementById('editFirstName').value = customer.first_name || '';
                    document.getElementById('editMiddleName').value = customer.middle_initial || '';
                    document.getElementById('editLastName').value = customer.last_name || '';
                    document.getElementById('editJobTitle').value = customer.job_title || '';
                    document.getElementById('editMainPhone').value = customer.main_phone || '';
                    document.getElementById('editWorkPhone').value = customer.work_phone || '';
                    document.getElementById('editMobilePhone').value = customer.mobile || '';
                    document.getElementById('editFaxPhone').value = customer.fax || '';
                    document.getElementById('editMainEmail').value = customer.main_email || '';
                    document.getElementById('editCcEmail').value = customer.cc_email || '';
                    document.getElementById('editWebsite').value = customer.website || '';
                    document.getElementById('editOtherContact').value = customer.other_contact || '';
                    // Helper to parse address strings cleanly
                    const parseAddressString = (addrStr) => {
                        if (!addrStr || addrStr === 'N/A') {
                            return { line1: '', line2: '', city: '', province: '', country: 'Philippines' };
                        }
                        if (addrStr.includes('|')) {
                            const p = addrStr.split('|').map(s => s.trim());
                            return { line1: p[0] || '', line2: p[1] || '', city: p[2] || '', province: p[3] || '', country: p[4] || 'Philippines' };
                        }
                        const parts = addrStr.split(',').map(s => s.trim()).filter(Boolean);
                        if (parts.length >= 5) {
                            return { line1: parts[0] || '', line2: parts[1] || '', city: parts[2] || '', province: parts[3] || '', country: parts[4] || 'Philippines' };
                        } else if (parts.length === 4) {
                            return { line1: parts[0] || '', line2: '', city: parts[1] || '', province: parts[2] || '', country: parts[3] || 'Philippines' };
                        } else if (parts.length === 3) {
                            return { line1: parts[0] || '', line2: '', city: parts[1] || '', province: '', country: parts[2] || 'Philippines' };
                        } else if (parts.length === 2) {
                            return { line1: parts[0] || '', line2: '', city: parts[1] || '', province: '', country: 'Philippines' };
                        }
                        return { line1: parts[0] || '', line2: '', city: '', province: '', country: 'Philippines' };
                    };

                    // Populate Form-Style Address Fields using direct DB columns with smart parser fallback
                    const parsedBill = parseAddressString(customer.billing_address);
                    document.getElementById('editBillAddr1').value = customer.billing_address_line1 || parsedBill.line1;
                    document.getElementById('editBillAddr2').value = customer.billing_address_line2 || parsedBill.line2;
                    document.getElementById('editBillCity').value = customer.billing_city || parsedBill.city;
                    document.getElementById('editBillProvince').value = customer.billing_province || parsedBill.province;
                    document.getElementById('editBillCountry').value = customer.billing_country || parsedBill.country;

                    const parsedShip = parseAddressString(customer.shipping_address);
                    document.getElementById('editShipAddr1').value = customer.shipping_address_line1 || parsedShip.line1;
                    document.getElementById('editShipAddr2').value = customer.shipping_address_line2 || parsedShip.line2;
                    document.getElementById('editShipCity').value = customer.shipping_city || parsedShip.city;
                    document.getElementById('editShipProvince').value = customer.shipping_province || parsedShip.province;
                    document.getElementById('editShipCountry').value = customer.shipping_country || parsedShip.country;

                    document.getElementById('editDefaultShipping').checked = customer.is_default_shipping == 1;
                    document.getElementById('editAccountNo').value = customer.account_number || '';
                    document.getElementById('editPaymentTerms').value = customer.payment_terms || 'Net 15';
                    document.getElementById('editDeliveryMethod').value = customer.preferred_delivery_method || 'Lazada';
                    
                    // Handle payment method (capitalize first letter for select option)
                    const paymentMethod = customer.preferred_payment_method || 'check';
                    document.getElementById('editPaymentMethod').value = paymentMethod.charAt(0).toUpperCase() + paymentMethod.slice(1);
                    
                    document.getElementById('editCreditLimitInput').value = customer.credit_limit || '';
                    
                    // Handle price level (capitalize first letter for select option)
                    const priceLevel = customer.price_level || 'standard';
                    document.getElementById('editPriceLevel').value = priceLevel.charAt(0).toUpperCase() + priceLevel.slice(1);
                    
                    document.getElementById('editCcNo').value = customer.card_number_last4 ? '****' + customer.card_number_last4 : '';
                    document.getElementById('editCcExpMonth').value = customer.card_exp_month || '';
                    document.getElementById('editCcExpYear').value = customer.card_exp_year || '';
                    document.getElementById('editCcName').value = customer.card_name || '';
                    document.getElementById('editCcAddress').value = customer.card_billing_address || '';
                    setSelectOrAddOption('editCustType', customer.customer_type, 'TEAM A');
                    setSelectOrAddOption('editCustRep', customer.rep, 'CLE');
                    setSelectOrAddOption('editCustClass', customer.class, 'LAG');
                    document.getElementById('editCustomContactPerson').value = customer.custom_contact_person || '';
                    document.getElementById('editCustomCustField').value = customer.custom_customer_field || '';
                    
                    // Populate representatives
                    const editRepContainer = document.getElementById('editRepresentativesContainer');
                    if (editRepContainer) {
                        editRepContainer.innerHTML = '';
                        let reps = customer.representatives;
                        if (typeof reps === 'string') {
                            try { reps = JSON.parse(reps); } catch(e) { reps = []; }
                        }
                        if (Array.isArray(reps) && reps.length > 0) {
                            reps.forEach(rep => renderRepRow('editRepresentativesContainer', rep));
                        } else {
                            editRepContainer.innerHTML = `<tr class="empty-rep-row"><td colspan="5" class="text-center py-2 text-muted small">No representatives added yet. Click "+ Add Representative" above.</td></tr>`;
                        }
                    }

                    document.getElementById('editCustomerInactive').checked = customer.is_inactive == 1;
                    if (document.getElementById('editManualStatus')) {
                        document.getElementById('editManualStatus').value = customer.manual_status || '';
                    }

                    // Show the edit modal
                    const editModal = new bootstrap.Modal(document.getElementById('editCustomerModal'));
                    editModal.show();

                } catch (error) {
                    // Show error modal
                    document.getElementById('errorMessage').textContent = 'Error fetching customer data: ' + error.message;
                    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                }
            });
        });

        // Show confirm modal before saving
        document.getElementById('updateCustomerBtn')?.addEventListener('click', function() {
            // Show confirm modal
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmSaveModal'));
            confirmModal.show();
        });

        // Update Customer via AJAX after confirmation
        document.getElementById('confirmSaveBtn')?.addEventListener('click', async function() {
            const btn = this;
            const updateBtn = document.getElementById('updateCustomerBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';

            // Hide confirm modal
            bootstrap.Modal.getInstance(document.getElementById('confirmSaveModal')).hide();

            const customerId = document.getElementById('editCustomerId').value;

            const editBillParts = [
                document.getElementById('editBillAddr1')?.value,
                document.getElementById('editBillAddr2')?.value,
                document.getElementById('editBillCity')?.value,
                document.getElementById('editBillProvince')?.value,
                document.getElementById('editBillCountry')?.value
            ].filter(Boolean);
            const editBillingAddressVal = editBillParts.join(', ') || 'N/A';

            const editShipParts = [
                document.getElementById('editShipAddr1')?.value,
                document.getElementById('editShipAddr2')?.value,
                document.getElementById('editShipCity')?.value,
                document.getElementById('editShipProvince')?.value,
                document.getElementById('editShipCountry')?.value
            ].filter(Boolean);
            const editShippingAddressVal = editShipParts.join(', ') || editBillingAddressVal || 'N/A';

            const data = {
                customer_name: document.getElementById('editCustNameInput')?.value || '',
                company_name: document.getElementById('editCompanyName')?.value || '',
                opening_balance: document.getElementById('editOpeningBalance')?.value || null,
                opening_balance_date: document.getElementById('editAsOfDate')?.value || null,
                currency_code: document.getElementById('editCurrencySelect')?.value || null,
                title: document.getElementById('editTitleName')?.value || null,
                first_name: document.getElementById('editFirstName')?.value || null,
                middle_initial: document.getElementById('editMiddleName')?.value || null,
                last_name: document.getElementById('editLastName')?.value || null,
                job_title: document.getElementById('editJobTitle')?.value || null,
                main_phone: document.getElementById('editMainPhone')?.value || null,
                work_phone: document.getElementById('editWorkPhone')?.value || null,
                mobile: document.getElementById('editMobilePhone')?.value || null,
                fax: document.getElementById('editFaxPhone')?.value || null,
                main_email: document.getElementById('editMainEmail')?.value || null,
                cc_email: document.getElementById('editCcEmail')?.value || null,
                website: document.getElementById('editWebsite')?.value || null,
                other_contact: document.getElementById('editOtherContact')?.value || null,
                billing_address: editBillingAddressVal,
                billing_address_line1: document.getElementById('editBillAddr1')?.value || null,
                billing_address_line2: document.getElementById('editBillAddr2')?.value || null,
                billing_city: document.getElementById('editBillCity')?.value || null,
                billing_province: document.getElementById('editBillProvince')?.value || null,
                billing_country: document.getElementById('editBillCountry')?.value || 'Philippines',
                shipping_address: editShippingAddressVal,
                shipping_address_line1: document.getElementById('editShipAddr1')?.value || null,
                shipping_address_line2: document.getElementById('editShipAddr2')?.value || null,
                shipping_city: document.getElementById('editShipCity')?.value || null,
                shipping_province: document.getElementById('editShipProvince')?.value || null,
                shipping_country: document.getElementById('editShipCountry')?.value || 'Philippines',
                is_default_shipping: document.getElementById('editDefaultShipping')?.checked ? 1 : 0,
                account_number: document.getElementById('editAccountNo')?.value || null,
                payment_terms: document.getElementById('editPaymentTerms')?.value || null,
                preferred_delivery_method: document.getElementById('editDeliveryMethod')?.value || null,
                preferred_payment_method: document.getElementById('editPaymentMethod')?.value?.toLowerCase() || null,
                credit_limit: document.getElementById('editCreditLimitInput')?.value || null,
                price_level: document.getElementById('editPriceLevel')?.value?.toLowerCase() || null,
                card_number_last4: document.getElementById('editCcNo')?.value?.replace('****', '')?.slice(-4) || null,
                card_exp_month: document.getElementById('editCcExpMonth')?.value || null,
                card_exp_year: document.getElementById('editCcExpYear')?.value || null,
                card_name: document.getElementById('editCcName')?.value || null,
                card_billing_address: document.getElementById('editCcAddress')?.value || null,
                card_zip: document.getElementById('editCcZip')?.value || null,
                customer_type: document.getElementById('editCustType')?.value || null,
                rep: document.getElementById('editCustRep')?.value || null,
                class: document.getElementById('editCustClass')?.value || null,
                custom_contact_person: document.getElementById('editCustomContactPerson')?.value || null,
                custom_customer_field: document.getElementById('editCustomCustField')?.value || null,
                representatives: getRepresentativesFromContainer('editRepresentativesContainer'),
                is_inactive: document.getElementById('editCustomerInactive')?.checked ? 1 : 0,
                manual_status: document.getElementById('editManualStatus')?.value || null,
            };

            try {
                const response = await fetch(`/marketing/customers/${customerId}`, {
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
                    // Hide edit modal
                    bootstrap.Modal.getInstance(document.getElementById('editCustomerModal')).hide();
                    
                    // Show success modal
                    document.getElementById('successMessage').textContent = 'Customer updated successfully!';
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();

                    // Optional: Refresh page on OK button click
                    document.getElementById('successOkBtn').onclick = function() {
                        window.location.reload();
                    };
                } else {
                    let errorMsg = result.message || 'Failed to update customer.';
                    if (result.errors) {
                        errorMsg = Object.values(result.errors).flat().join('\n');
                    }
                    // Show error modal
                    document.getElementById('errorMessage').textContent = errorMsg;
                    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                }
            } catch (error) {
                // Show error modal
                document.getElementById('errorMessage').textContent = 'An error occurred: ' + error.message;
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check me-1"></i>Yes';
            }
        });

        // View Transaction History Logic with Filtering & Pagination
        let currentHistoryCustomerId = null;
        let currentHistoryPage = 1;
        let historySearchTimeout = null;

        async function fetchTransactionHistory(customerId, page = 1) {
            currentHistoryCustomerId = customerId;
            currentHistoryPage = page;

            const tableBody = document.getElementById('historyTableBody');
            const paginationList = document.getElementById('historyPaginationList');
            const paginationInfo = document.getElementById('historyPaginationInfo');
            
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-3"><i class="fas fa-spinner fa-spin me-2"></i>Loading history...</td></tr>';

            const search = document.getElementById('historySearchInput')?.value || '';
            const status = document.getElementById('historyStatusFilter')?.value || '';
            const perPage = document.getElementById('historyPerPage')?.value || 10;

            const url = new URL(`/marketing/customers/${customerId}/history`, window.location.origin);
            url.searchParams.append('page', page);
            url.searchParams.append('per_page', perPage);
            if (search) url.searchParams.append('search', search);
            if (status) url.searchParams.append('status', status);

            try {
                const response = await fetch(url);
                const data = await response.json();

                document.getElementById('historyCustomerName').textContent = data.customer_name;
                document.getElementById('historyBalance').textContent = '₱' + data.balance.toLocaleString(undefined, {minimumFractionDigits: 2});
                
                const statusBadge = document.getElementById('historyStatusBadge');
                if (data.is_bad_client) {
                    statusBadge.innerHTML = '<span class="badge light badge-danger">Bad Client</span>';
                } else {
                    statusBadge.innerHTML = '<span class="badge light badge-success">Good Client</span>';
                }

                if (document.getElementById('manualStatusOverride')) {
                    document.getElementById('manualStatusOverride').value = data.manual_status || '';
                }

                let rows = '';
                if (!data.history || data.history.length === 0) {
                    rows = '<tr><td colspan="9" class="text-center text-muted py-3">No transactions found.</td></tr>';
                } else {
                    data.history.forEach(order => {
                        const paymentColor = order.payment_status === 'paid' ? 'success' : (order.payment_status === 'partially_paid' ? 'warning' : 'danger');
                        const paymentLabel = order.payment_status === 'partially_paid' ? 'PARTIALLY PAID' : order.payment_status.toUpperCase();
                        
                        let orderBadgeColor = 'secondary';
                        if (order.status === 'completed') orderBadgeColor = 'success';
                        else if (order.status === 'ready_for_delivery') orderBadgeColor = 'primary';
                        else if (order.status === 'cancelled') orderBadgeColor = 'dark';
                        else if (order.status && order.status.includes('pending')) orderBadgeColor = 'warning';

                        const overdueTag = order.is_overdue ? '<br><span class="badge badge-xs light badge-danger">OVERDUE</span>' : '';
                        const siBadge = order.si_number ? `<br><span class="badge badge-xs light badge-info mt-1"><i class="las la-file-invoice me-1"></i>${order.si_number}</span>` : '';
                        const proofTag = order.has_proof_of_payment ? `<br><a href="${order.proof_of_payment_url}" target="_blank" class="badge badge-xs bg-light text-primary border mt-1" title="View Proof of Payment"><i class="las la-paperclip me-1"></i>Proof Attached</a>` : '';
                        
                        let actionCol = '';
                        if (order.remaining_balance > 0) {
                            actionCol = `<button type="button" class="btn btn-xs btn-success open-pay-modal-btn shadow-sm" data-so-id="${order.id}" data-so-number="${order.so_number}" data-total="${order.total_amount}" data-paid="${order.paid_amount}" data-remaining="${order.remaining_balance}" data-terms="${order.terms || 'COD'}" data-due-date="${order.due_date || 'N/A'}"><i class="las la-coins me-1"></i>Pay</button>`;
                        } else {
                            actionCol = `<button type="button" class="btn btn-xs btn-outline-success open-pay-modal-btn shadow-sm" data-so-id="${order.id}" data-so-number="${order.so_number}" data-total="${order.total_amount}" data-paid="${order.paid_amount}" data-remaining="${order.remaining_balance}" data-terms="${order.terms || 'COD'}" data-due-date="${order.due_date || 'N/A'}"><i class="las la-history me-1"></i>History</button>`;
                        }

                        rows += `
                            <tr>
                                <td>${order.date}</td>
                                <td>
                                    <div class="fw-bold">${order.so_number}</div>
                                    ${siBadge}
                                </td>
                                <td>₱${order.total_amount.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                                <td class="text-success fw-bold">₱${order.paid_amount.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                                <td class="text-danger fw-bold">₱${order.remaining_balance.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                                <td>${order.due_date}${overdueTag}</td>
                                <td><span class="badge badge-xs light badge-${orderBadgeColor}">${order.status_label.toUpperCase()}</span></td>
                                <td>
                                    <span class="badge badge-xs light badge-${paymentColor}">${paymentLabel}</span>
                                    ${proofTag}
                                </td>
                                <td>${actionCol}</td>
                            </tr>
                        `;
                    });
                }
                tableBody.innerHTML = rows;

                // Render Pagination
                const pag = data.pagination;
                if (pag && pag.total > 0) {
                    paginationInfo.textContent = `Showing ${pag.from} to ${pag.to} of ${pag.total} transactions`;
                    
                    let pagHtml = '';
                    // Previous button
                    pagHtml += `<li class="page-item ${pag.current_page === 1 ? 'disabled' : ''}">
                        <button type="button" class="page-link" data-page="${pag.current_page - 1}">Prev</button>
                    </li>`;

                    for (let i = 1; i <= pag.last_page; i++) {
                        if (i === 1 || i === pag.last_page || (i >= pag.current_page - 2 && i <= pag.current_page + 2)) {
                            pagHtml += `<li class="page-item ${i === pag.current_page ? 'active' : ''}">
                                <button type="button" class="page-link" data-page="${i}">${i}</button>
                            </li>`;
                        } else if (i === pag.current_page - 3 || i === pag.current_page + 3) {
                            pagHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                        }
                    }

                    // Next button
                    pagHtml += `<li class="page-item ${pag.current_page === pag.last_page ? 'disabled' : ''}">
                        <button type="button" class="page-link" data-page="${pag.current_page + 1}">Next</button>
                    </li>`;

                    paginationList.innerHTML = pagHtml;
                } else {
                    paginationInfo.textContent = 'Showing 0 transactions';
                    paginationList.innerHTML = '';
                }
                
                // Store current customer ID for override button
                document.getElementById('updateManualStatusBtn')?.setAttribute('data-customer-id', customerId);

            } catch (error) {
                console.error('Error fetching history:', error);
                tableBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-3">Error loading transaction history.</td></tr>';
            }
        }

        async function fetchPaymentHistory(customerId, soId) {
            const tableBody = document.getElementById('payHistoryTableBody');
            const badge = document.getElementById('payHistoryBadge');

            if (!tableBody) return;

            tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-2 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Loading history...</td></tr>';
            if (badge) badge.textContent = 'Loading...';

            try {
                const response = await fetch(`/marketing/customers/${customerId}/transactions/${soId}/payments`);
                const data = await response.json();

                if (!data.payments || data.payments.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-2 text-muted">No previous installments recorded.</td></tr>';
                    if (badge) badge.textContent = '0 payments';
                } else {
                    if (badge) badge.textContent = data.payments.length + ' payment(s)';
                    let rows = '';
                    data.payments.forEach(p => {
                        const proofTag = p.has_proof ? `<a href="${p.proof_url}" target="_blank" class="badge badge-xs bg-light text-primary border"><i class="las la-paperclip me-1"></i>View Proof</a>` : '<span class="text-muted small">None</span>';
                        rows += `<tr>
                            <td class="fw-bold">${p.date}</td>
                            <td class="text-success fw-bold">₱${p.amount.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                            <td><span class="badge bg-light text-dark border">${p.method}</span></td>
                            <td>${p.reference_number}</td>
                            <td>${p.notes}</td>
                            <td>${proofTag}</td>
                            <td><small class="text-muted">${p.recorded_by}</small></td>
                        </tr>`;
                    });
                    tableBody.innerHTML = rows;
                }
            } catch (error) {
                console.error('Error loading payment history:', error);
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-2 text-danger">Failed to load payment history.</td></tr>';
                if (badge) badge.textContent = 'Error';
            }
        }

        // Open Pay Installment Sub-Modal
        document.getElementById('historyTableBody')?.addEventListener('click', function(e) {
            const payBtn = e.target.closest('.open-pay-modal-btn');
            if (payBtn) {
                const soId = payBtn.dataset.soId;
                const soNumber = payBtn.dataset.soNumber;
                const totalAmount = parseFloat(payBtn.dataset.total) || 0;
                const paidAmount = parseFloat(payBtn.dataset.paid) || 0;
                const remainingBalance = parseFloat(payBtn.dataset.remaining) || 0;

                const terms = payBtn.dataset.terms || 'COD';
                const dueDate = payBtn.dataset.dueDate || 'N/A';

                document.getElementById('paySoId').value = soId;
                document.getElementById('paySoNumber').textContent = soNumber;
                document.getElementById('payTerms').textContent = terms;
                document.getElementById('payDueDate').textContent = dueDate;
                document.getElementById('payTotalAmount').textContent = '₱' + totalAmount.toLocaleString(undefined, {minimumFractionDigits: 2});
                document.getElementById('payAlreadyPaid').textContent = '₱' + paidAmount.toLocaleString(undefined, {minimumFractionDigits: 2});
                document.getElementById('payRemainingBalance').textContent = '₱' + remainingBalance.toLocaleString(undefined, {minimumFractionDigits: 2});
                
                const formFields = document.getElementById('newPaymentFormFields');
                const submitBtn = document.getElementById('submitPaymentBtn');
                const notice = document.getElementById('fullyPaidNotice');

                if (remainingBalance <= 0) {
                    if (formFields) formFields.classList.add('d-none');
                    if (submitBtn) submitBtn.classList.add('d-none');
                    if (notice) notice.classList.remove('d-none');
                } else {
                    if (formFields) formFields.classList.remove('d-none');
                    if (submitBtn) submitBtn.classList.remove('d-none');
                    if (notice) notice.classList.add('d-none');

                    const payAmountInput = document.getElementById('payAmountInput');
                    payAmountInput.value = remainingBalance.toFixed(2);
                    payAmountInput.max = remainingBalance;
                    document.getElementById('payRefInput').value = '';
                    document.getElementById('payNotesInput').value = '';
                    const proofInput = document.getElementById('payProofInput');
                    if (proofInput) proofInput.value = '';
                }

                // Load payment history table via API
                fetchPaymentHistory(currentHistoryCustomerId, soId);

                const payModalElement = document.getElementById('recordPaymentModal');
                const payModal = bootstrap.Modal.getInstance(payModalElement) || new bootstrap.Modal(payModalElement);
                payModal.show();
            }
        });

        // Submit Payment Form
        document.getElementById('recordPaymentForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const soId = document.getElementById('paySoId').value;
            const amount = parseFloat(document.getElementById('payAmountInput').value);
            const paymentMethod = document.getElementById('payMethodSelect').value;
            const referenceNumber = document.getElementById('payRefInput').value;
            const notes = document.getElementById('payNotesInput').value;
            const proofInput = document.getElementById('payProofInput');

            if (!soId || !currentHistoryCustomerId) return;

            const submitBtn = document.getElementById('submitPaymentBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...';

            const formData = new FormData();
            formData.append('amount', amount);
            formData.append('payment_method', paymentMethod);
            if (referenceNumber) formData.append('reference_number', referenceNumber);
            if (notes) formData.append('notes', notes);
            if (proofInput && proofInput.files[0]) {
                formData.append('proof_of_payment', proofInput.files[0]);
            }

            try {
                const response = await fetch(`/marketing/customers/${currentHistoryCustomerId}/transactions/${soId}/pay`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    const payModalElement = document.getElementById('recordPaymentModal');
                    const payModal = bootstrap.Modal.getInstance(payModalElement);
                    if (payModal) payModal.hide();

                    // Refresh history & customer list balance
                    fetchTransactionHistory(currentHistoryCustomerId, currentHistoryPage);
                } else {
                    alert(data.message || 'Error recording payment.');
                }
            } catch (error) {
                console.error('Error submitting payment:', error);
                alert('An error occurred while submitting payment.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="las la-check-circle me-1"></i> Submit Payment';
            }
        });

        // View Transaction History Button Click
        document.querySelectorAll('.view-history-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const customerId = this.dataset.customerId;
                const historyModalElement = document.getElementById('transactionHistoryModal');
                const historyModal = bootstrap.Modal.getInstance(historyModalElement) || new bootstrap.Modal(historyModalElement);
                
                // Reset search and filters
                if (document.getElementById('historySearchInput')) document.getElementById('historySearchInput').value = '';
                if (document.getElementById('historyStatusFilter')) document.getElementById('historyStatusFilter').value = '';
                if (document.getElementById('historyPerPage')) document.getElementById('historyPerPage').value = '10';

                historyModal.show();
                fetchTransactionHistory(customerId, 1);
            });
        });

        // Filter / Search event listeners
        document.getElementById('historySearchInput')?.addEventListener('input', function() {
            clearTimeout(historySearchTimeout);
            historySearchTimeout = setTimeout(() => {
                if (currentHistoryCustomerId) {
                    fetchTransactionHistory(currentHistoryCustomerId, 1);
                }
            }, 300);
        });

        document.getElementById('historyStatusFilter')?.addEventListener('change', function() {
            if (currentHistoryCustomerId) {
                fetchTransactionHistory(currentHistoryCustomerId, 1);
            }
        });

        document.getElementById('historyPerPage')?.addEventListener('change', function() {
            if (currentHistoryCustomerId) {
                fetchTransactionHistory(currentHistoryCustomerId, 1);
            }
        });

        // Pagination click listener
        document.getElementById('historyPaginationList')?.addEventListener('click', function(e) {
            e.preventDefault();
            const pageBtn = e.target.closest('.page-link');
            if (pageBtn && !pageBtn.parentElement.classList.contains('disabled') && !pageBtn.parentElement.classList.contains('active')) {
                const targetPage = parseInt(pageBtn.dataset.page);
                if (targetPage && currentHistoryCustomerId) {
                    fetchTransactionHistory(currentHistoryCustomerId, targetPage);
                }
            }
        });

        // Update Manual Status from History Modal
        document.getElementById('updateManualStatusBtn')?.addEventListener('click', async function() {
            const customerId = this.getAttribute('data-customer-id');
            const manualStatus = document.getElementById('manualStatusOverride').value;
            const btn = this;

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            try {
                const response = await fetch(`/marketing/customers/${customerId}/manual-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ manual_status: manualStatus })
                });

                if (response.ok) {
                    location.reload();
                } else {
                    alert('Failed to update status.');
                }
            } catch (error) {
                alert('An error occurred.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Apply';
            }
        });

        let customerIdToDelete = null;

        // Open Confirm Delete Modal
        document.querySelectorAll('.delete-customer-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!canEditCustomers) {
                    alert('You do not have permission to delete customers. Only Marketing users can delete.');
                    return;
                }
                customerIdToDelete = this.dataset.customerId;
                const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                confirmDeleteModal.show();
            });
        });

        // Delete Customer via AJAX
        document.getElementById('confirmDeleteBtn')?.addEventListener('click', async function() {
            if (!customerIdToDelete) return;

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Deleting...';

            try {
                const response = await fetch(`/marketing/customers/${customerIdToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                // Hide confirm modal
                bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal')).hide();

                if (response.ok) {
                    // Show success modal
                    document.getElementById('successMessage').textContent = 'Customer deleted successfully!';
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                    
                    document.getElementById('successOkBtn').onclick = function() {
                        window.location.reload();
                    };
                } else {
                    // Show error modal
                    document.getElementById('errorMessage').textContent = result.message || 'Failed to delete customer.';
                    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                }
            } catch (error) {
                // Hide confirm modal if still open
                const modalEl = document.getElementById('confirmDeleteModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();

                // Show error modal
                document.getElementById('errorMessage').textContent = 'An error occurred: ' + error.message;
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-trash me-1"></i>Delete';
                customerIdToDelete = null;
            }
        });

        // Bulk Select & Delete functionality with Cross-Page Persistence via sessionStorage
        const selectAllCb = document.getElementById('selectAllCustomers');
        const floatingBulkDeleteBtn = document.getElementById('floatingBulkDeleteBtn');
        const bulkFloatingBar = document.getElementById('bulkFloatingBar');
        const clearSelectionBtn = document.getElementById('clearSelectionBtn');
        const floatingSelectedCountSpan = document.getElementById('floatingSelectedCount');

        function getSelectedCustomerIds() {
            try {
                const stored = sessionStorage.getItem('selectedCustomerIds');
                return stored ? JSON.parse(stored) : [];
            } catch (e) {
                return [];
            }
        }

        function saveSelectedCustomerIds(ids) {
            sessionStorage.setItem('selectedCustomerIds', JSON.stringify(Array.from(new Set(ids))));
        }

        function updateBulkDeleteState() {
            const currentSelected = new Set(getSelectedCustomerIds());

            // Synchronize current page checkboxes with session storage
            document.querySelectorAll('.customer-row-cb').forEach(cb => {
                if (cb.checked) {
                    currentSelected.add(cb.value);
                } else {
                    currentSelected.delete(cb.value);
                }
            });

            const allSelectedIds = Array.from(currentSelected);
            saveSelectedCustomerIds(allSelectedIds);

            const count = allSelectedIds.length;
            if (floatingSelectedCountSpan) floatingSelectedCountSpan.textContent = count;

            if (bulkFloatingBar) {
                if (count > 0) {
                    bulkFloatingBar.classList.remove('hidden');
                } else {
                    bulkFloatingBar.classList.add('hidden');
                }
            }

            const pageCbs = document.querySelectorAll('.customer-row-cb');
            const pageCheckedCbs = document.querySelectorAll('.customer-row-cb:checked');
            if (selectAllCb) {
                selectAllCb.checked = (pageCbs.length > 0 && pageCheckedCbs.length === pageCbs.length);
                selectAllCb.indeterminate = (pageCheckedCbs.length > 0 && pageCheckedCbs.length < pageCbs.length);
            }

            // Update row background styling
            document.querySelectorAll('.customer-row').forEach(row => {
                const cb = row.querySelector('.customer-row-cb');
                if (cb && cb.checked) {
                    row.classList.add('selected-row');
                } else {
                    row.classList.remove('selected-row');
                }
            });
        }

        function restoreCheckboxStates() {
            const selectedIds = new Set(getSelectedCustomerIds());
            document.querySelectorAll('.customer-row-cb').forEach(cb => {
                if (selectedIds.has(cb.value)) {
                    cb.checked = true;
                }
            });
            updateBulkDeleteState();
        }

        // Restore checkboxes when page loads
        restoreCheckboxStates();

        if (clearSelectionBtn) {
            clearSelectionBtn.addEventListener('click', function() {
                sessionStorage.removeItem('selectedCustomerIds');
                document.querySelectorAll('.customer-row-cb').forEach(cb => cb.checked = false);
                if (selectAllCb) {
                    selectAllCb.checked = false;
                    selectAllCb.indeterminate = false;
                }
                updateBulkDeleteState();
            });
        }

        if (selectAllCb) {
            selectAllCb.addEventListener('change', function() {
                const isChecked = this.checked;
                document.querySelectorAll('.customer-row-cb').forEach(cb => {
                    cb.checked = isChecked;
                });
                updateBulkDeleteState();
            });
        }

        document.querySelectorAll('.customer-row-cb').forEach(cb => {
            cb.addEventListener('change', updateBulkDeleteState);
        });

        // Mouse Hold / Click on Row Selection feature
        document.querySelectorAll('.customer-row').forEach(row => {
            let holdTimer = null;
            
            row.addEventListener('mousedown', function(e) {
                // Don't trigger if user clicked on action buttons or inputs directly
                if (e.target.closest('a, button, input, select, textarea')) return;

                holdTimer = setTimeout(() => {
                    const cb = row.querySelector('.customer-row-cb');
                    if (cb) {
                        cb.checked = !cb.checked;
                        updateBulkDeleteState();
                    }
                }, 350); // Hold mouse for 350ms
            });

            row.addEventListener('mouseup', function() {
                if (holdTimer) clearTimeout(holdTimer);
            });

            row.addEventListener('mouseleave', function() {
                if (holdTimer) clearTimeout(holdTimer);
            });
        });

        // Shared Execute Bulk Delete Function
        async function executeBulkDelete(targetBtn) {
            const selectedIds = getSelectedCustomerIds();

            if (!selectedIds.length) return;

            if (!confirm(`Are you sure you want to delete ${selectedIds.length} selected customer(s) across pages? This action cannot be undone.`)) {
                return;
            }

            if (targetBtn) {
                targetBtn.disabled = true;
                targetBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Deleting...';
            }

            try {
                const response = await fetch('{{ route("marketing.customers.bulk-delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ customer_ids: selectedIds })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    sessionStorage.removeItem('selectedCustomerIds');
                    alert(data.message || 'Customers deleted successfully!');
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to delete selected customers.');
                }
            } catch (err) {
                alert('An error occurred during bulk deletion: ' + err.message);
            } finally {
                if (floatingBulkDeleteBtn) {
                    floatingBulkDeleteBtn.disabled = false;
                    floatingBulkDeleteBtn.innerHTML = '<i class="fa fa-trash me-1"></i> Delete Selected';
                }
                updateBulkDeleteState();
            }
        }

        if (floatingBulkDeleteBtn) {
            floatingBulkDeleteBtn.addEventListener('click', function() {
                executeBulkDelete(this);
            });
        }

        // Customer Search Functionality (Debounced Server Search across all DB records)
        const customerSearchInput = document.getElementById('customerSearch');
        if (customerSearchInput) {
            let searchDebounceTimeout = null;

            const executeServerSearch = function(val) {
                const searchTerm = (val || '').trim();
                const url = new URL(window.location.href);
                const currentSearch = url.searchParams.get('search') || '';

                if (searchTerm === currentSearch) return;

                if (searchTerm) {
                    url.searchParams.set('search', searchTerm);
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.delete('page');
                window.location.href = url.toString();
            };

            customerSearchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchDebounceTimeout);
                    executeServerSearch(this.value);
                }
            });

            customerSearchInput.addEventListener('input', function() {
                const searchTerm = this.value;
                clearTimeout(searchDebounceTimeout);

                if (searchTerm.trim() === '') {
                    executeServerSearch('');
                    return;
                }

                searchDebounceTimeout = setTimeout(() => {
                    executeServerSearch(searchTerm);
                }, 500);
            });
        }

        // Import Customer Form Submission
        const importForm = document.getElementById('importCustomerForm');
        if (importForm) {
            importForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const fileInput = document.getElementById('customerExcelFile');
                if (!fileInput.files.length) {
                    alert('Please select an Excel file to import.');
                    return;
                }

                const btn = document.getElementById('btnSubmitImport');
                const feedback = document.getElementById('importFeedback');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Importing...';
                feedback.classList.add('d-none');

                const formData = new FormData(this);

                try {
                    const response = await fetch('{{ route("marketing.customers.import") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        feedback.className = 'alert alert-success mt-3 small';
                        feedback.innerHTML = '<strong><i class="fas fa-check-circle me-1"></i> Success:</strong> ' + data.message;
                        feedback.classList.remove('d-none');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1200);
                    } else {
                        feedback.className = 'alert alert-danger mt-3 small';
                        let errHtml = '<strong><i class="fas fa-exclamation-triangle me-1"></i> Import Error:</strong> ' + (data.message || 'Failed to import customers.');
                        if (data.errors && data.errors.length) {
                            errHtml += '<ul class="mb-0 mt-2 ps-3">';
                            data.errors.forEach(err => {
                                errHtml += '<li>' + err + '</li>';
                            });
                            errHtml += '</ul>';
                        }
                        feedback.innerHTML = errHtml;
                        feedback.classList.remove('d-none');
                    }
                } catch (err) {
                    feedback.className = 'alert alert-danger mt-3 small';
                    feedback.innerHTML = '<strong>Error:</strong> ' + err.message;
                    feedback.classList.remove('d-none');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="las la-file-upload me-1"></i> Upload & Import Customers';
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
