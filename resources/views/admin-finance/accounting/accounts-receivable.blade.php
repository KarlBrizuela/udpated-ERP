<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        /* Maximize page width by overriding layout container padding */
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
        }

        .hover-row {
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-row:hover {
            background-color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
        }

        .badge-rating {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .rating-aaa { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
        .rating-aa { background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .rating-a { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }

        .modal-tabs .nav-link {
            border: none;
            color: #475569;
            font-weight: 600;
            padding: 10px 16px;
            font-size: 0.8rem;
            border-bottom: 2px solid transparent;
            border-radius: 0;
            transition: all 0.15s ease-in-out;
        }

        .modal-tabs .nav-link:hover {
            color: #0f172a;
            background-color: #f8fafc;
        }

        .modal-tabs .nav-link.active {
            color: #D9251C;
            border-bottom: 2px solid #D9251C;
            background: transparent;
        }

        /* Accounts Receivable Table Styles (Reference: General Journal) */
        #arAccountsTable th, .modal-body table.table-hover thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            font-size: 0.72rem !important;
            letter-spacing: 0.8px !important;
            padding: 12px 16px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            border-top: none !important;
        }

        #arAccountsTable td, .modal-body table.table-hover tbody td {
            padding: 12px 16px !important;
            font-size: 0.84rem !important;
            color: #475569 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            vertical-align: middle !important;
        }

        #arAccountsTable tbody tr, .modal-body table.table-hover tbody tr {
            transition: all 0.15s ease-in-out !important;
        }

        #arAccountsTable tbody tr:hover, .modal-body table.table-hover tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* Modal Profile table-borderless details styling */
        .modal-body table.table-borderless td {
            padding: 8px 0 !important;
            border: none !important;
            background: transparent !important;
        }

        /* Modal Dialog & Layout */
        .modal-content {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }

        .modal-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 16px 24px !important;
        }

        .modal-header .modal-title {
            font-size: 0.95rem !important;
            font-weight: 700 !important;
            color: #000000 !important;
        }

        /* Modal Pagination Style Matching */
        .modal nav {
            padding: 12px 16px 0 16px !important;
            border-top: 1px solid #f1f5f9 !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
        }

        /* Custom pagination overrides */
        .pagination .page-item.active .page-link {
            background-color: #D9251C !important;
            border-color: #D9251C !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15) !important;
        }

        .pagination .page-link {
            color: #475569;
            border-color: #cbd5e1;
            padding: 8px 14px;
            font-size: 0.85rem;
            transition: all 0.15s ease-in-out;
        }

        .pagination .page-link:hover {
            background-color: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
        }
    </style>
    @endpush

    <div class="container-fluid p-0">
        <!-- Master Title Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-3" style="border-radius: 8px; background-color: #ffffff; border: 1px solid #e2e8f0;">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h4 class="fs-20 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px; color: #000000;">Accounts Receivable Ledger</h4>
                            <p class="text-muted small mb-0">Manage customer credit limits, payment terms, aging accounts, collections, and disputes.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2 fw-bold" style="background-color: #ffffff; border: 1px solid #cbd5e1; color: #475569; height: 38px;" onclick="window.print()">
                                <i class="las la-print fs-18"></i> Print Overview
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric summary cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 mb-3 mb-md-0" style="border-radius: 8px; border-left: 4px solid #3b82f6; background-color: #ffffff; border: 1px solid #e2e8f0;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem; color: #000000;">Total A/R Balance</span>
                            <h3 class="fw-bold text-dark mt-1 mb-0" style="letter-spacing: -0.5px;">₱{{ number_format($customers->sum('outstanding_balance'), 2) }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: rgba(59, 130, 246, 0.08); color: #3b82f6;">
                            <i class="las la-file-invoice-dollar fs-24"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 mb-3 mb-md-0" style="border-radius: 8px; border-left: 4px solid #ef4444; background-color: #ffffff; border: 1px solid #e2e8f0;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem; color: #ef4444;">Total Overdue A/R</span>
                            <h3 class="fw-bold text-danger mt-1 mb-0" style="letter-spacing: -0.5px;">₱{{ number_format($customers->sum('overdue_amount'), 2) }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: rgba(239, 68, 68, 0.08); color: #ef4444;">
                            <i class="las la-exclamation-circle fs-24"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3" style="border-radius: 8px; border-left: 4px solid #10b981; background-color: #ffffff; border: 1px solid #e2e8f0;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem; color: #000000;">Active Credit Clients</span>
                            <h3 class="fw-bold text-success mt-1 mb-0" style="letter-spacing: -0.5px;">{{ $customers->count() }} Accounts</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                            <i class="las la-users fs-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Master Accounts Receivable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px; border: 1px solid #e2e8f0; background: #ffffff;">
                    <div class="card-header bg-white border-0 pt-3 pb-2 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <!-- Left: Dropdown Filters -->
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <form method="GET" action="{{ route('admin-finance.accounting.accounts-receivable') }}" class="d-flex flex-wrap align-items-center gap-2">
                                @if(request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif

                                <!-- Credit Rating Filter -->
                                <select name="credit_rating" class="form-select form-select-sm" style="height: 38px; border-color: #cbd5e1; font-size: 0.82rem; width: 130px; box-shadow: none; outline: none; border-radius: 4px;">
                                    <option value="">All Ratings</option>
                                    <option value="AAA" {{ request('credit_rating') === 'AAA' ? 'selected' : '' }}>Rating AAA</option>
                                    <option value="AA" {{ request('credit_rating') === 'AA' ? 'selected' : '' }}>Rating AA</option>
                                    <option value="A" {{ request('credit_rating') === 'A' ? 'selected' : '' }}>Rating A</option>
                                </select>

                                <!-- Payment Terms Filter -->
                                <select name="payment_terms" class="form-select form-select-sm" style="height: 38px; border-color: #cbd5e1; font-size: 0.82rem; width: 150px; box-shadow: none; outline: none; border-radius: 4px;">
                                    <option value="">All Terms</option>
                                    @foreach($paymentTermsList as $term)
                                        <option value="{{ $term }}" {{ request('payment_terms') === $term ? 'selected' : '' }}>{{ $term }}</option>
                                    @endforeach
                                </select>

                                <button type="submit" class="btn text-white fw-bold px-3 d-flex align-items-center justify-content-center btn-sm" style="background: #D9251C; border-color: #D9251C; height: 38px; border-radius: 4px; font-size: 0.82rem;">
                                    Filter
                                </button>

                                @if(request('credit_rating') || request('payment_terms'))
                                    <a href="{{ route('admin-finance.accounting.accounts-receivable', request()->only('search')) }}" class="btn btn-light btn-sm d-flex align-items-center justify-content-center border" style="height: 38px; padding: 0 12px; font-size: 0.82rem; border-color: #cbd5e1; color: #475569; border-radius: 4px;">
                                        Clear Filter
                                    </a>
                                @endif
                            </form>
                        </div>

                        <!-- Right: Search Form -->
                        <div class="d-flex align-items-center">
                            <form method="GET" action="{{ route('admin-finance.accounting.accounts-receivable') }}" class="d-flex align-items-center gap-2">
                                @if(request('credit_rating'))
                                    <input type="hidden" name="credit_rating" value="{{ request('credit_rating') }}">
                                @endif
                                @if(request('payment_terms'))
                                    <input type="hidden" name="payment_terms" value="{{ request('payment_terms') }}">
                                @endif

                                <!-- Search Input Box with Magnifying Glass on Left -->
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 10px; border-top-left-radius: 4px; border-bottom-left-radius: 4px;">
                                        <i class="las la-search text-muted fs-16"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search client name..." value="{{ request('search') }}" style="height: 38px; border-color: #cbd5e1; border-top-right-radius: 4px; border-bottom-right-radius: 4px; font-size: 0.82rem; padding-left: 0; outline: none; box-shadow: none;">
                                </div>

                                <!-- Separate Search/Clear Button -->
                                @if(request('search'))
                                    <a href="{{ route('admin-finance.accounting.accounts-receivable', request()->except('search')) }}" class="btn btn-light d-flex align-items-center justify-content-center border fw-bold" style="height: 38px; padding: 0 16px; font-size: 0.82rem; border-color: #cbd5e1; color: #475569; border-radius: 4px;">
                                        Clear
                                    </a>
                                @else
                                    <button type="submit" class="btn text-white fw-bold px-3 d-flex align-items-center justify-content-center" style="background: #D9251C; border-color: #D9251C; height: 38px; border-radius: 4px; font-size: 0.82rem;">
                                        Search
                                    </button>
                                @endif
                            </form>
                        </div>
                    </div>
                    <div class="card-body pt-1">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="arAccountsTable" style="margin-bottom: 0;">
                                <thead class="table-light text-muted small text-uppercase">
                                    <tr>
                                        <th>Account No</th>
                                        <th>Client Name</th>
                                        <th>Credit Rating</th>
                                        <th class="text-end">Credit Limit</th>
                                        <th>Terms</th>
                                        <th class="text-end">Outstanding Balance</th>
                                        <th class="text-end">Overdue Amount</th>
                                        <th class="text-center" style="width: 130px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customers as $cust)
                                    <tr>
                                        <td><span class="fw-bold text-dark">{{ $cust->account_number }}</span></td>
                                        <td>
                                            <span class="fw-bold text-dark d-block fs-14">{{ $cust->customer_name }}</span>
                                            <span class="text-muted small">{{ $cust->company_name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge-rating {{ $cust->credit_rating === 'AAA' ? 'rating-aaa' : ($cust->credit_rating === 'AA' ? 'rating-aa' : 'rating-a') }}">
                                                {{ $cust->credit_rating }}
                                            </span>
                                        </td>
                                        <td class="text-end text-dark">₱{{ number_format($cust->credit_limit, 2) }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $cust->payment_terms }}</span></td>
                                        <td class="text-end fw-bold text-dark">₱{{ number_format($cust->outstanding_balance, 2) }}</td>
                                        <td class="text-end fw-bold {{ $cust->overdue_amount > 0 ? 'text-danger' : 'text-muted' }}">
                                            ₱{{ number_format($cust->overdue_amount, 2) }}
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-info shadow btn-xs sharp mx-auto text-white d-flex align-items-center justify-content-center" title="View Ledger" onclick="showSalesLedgerModal('Key Account AR Ledger: {{ $cust->customer_name }}', document.getElementById('template-cust-{{ $cust->customer_id }}').innerHTML)">
                                                <i class="las la-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">No accounts receivable ledger records found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div id="paginationContainer" class="mt-4 d-flex justify-content-end pe-4">
                            {{ $customers->onEachSide(0)->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Generic Ledger Detail Modal (Loaded Master View) -->
    <div class="modal fade" id="salesLedgerModal" tabindex="-1" aria-labelledby="salesLedgerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="salesLedgerModalLabel">Account Ledger</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="salesLedgerModalBody">
                    <!-- Dynamically populated -->
                </div>
            </div>
        </div>
    </div>

    <!-- HIDDEN TEMPLATES FOR CLIENT DETAILS -->
    @foreach($customers as $cust)
    <div id="template-cust-{{ $cust->customer_id }}" style="display: none;">
        <!-- Ledger Header details -->
        <div class="row mb-4 pb-3 border-bottom align-items-center g-3">
            <div class="col-md-7">
                <span class="text-muted small d-block mb-1 text-uppercase fw-bold" style="letter-spacing: 0.5px; font-size: 0.7rem;">Account Profile</span>
                <h4 class="text-dark fw-bold mb-1" style="letter-spacing: -0.5px;">{{ $cust->customer_name }}</h4>
                @if($cust->company_name && $cust->company_name !== $cust->customer_name)
                    <span class="badge bg-light text-muted border px-2 py-1 fs-12">{{ $cust->company_name }}</span>
                @endif
            </div>
            <div class="col-md-5 text-md-end">
                <span class="text-muted small d-block mb-2 text-uppercase fw-bold" style="letter-spacing: 0.5px; font-size: 0.7rem;">Outstanding Balance</span>
                <span class="px-3 py-2 rounded fw-bold text-danger d-inline-block" style="font-size: 1.15rem; background-color: rgba(217, 37, 28, 0.08); border: 1px solid rgba(217, 37, 28, 0.15);">
                    ₱{{ number_format($cust->outstanding_balance, 2) }}
                </span>
            </div>
        </div>

        <!-- Sub Tabs inside modal -->
        <ul class="nav nav-tabs modal-tabs mb-3" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#cust-profile-{{ $cust->customer_id }}" type="button" role="tab">Profile & Credit</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cust-billing-{{ $cust->customer_id }}" type="button" role="tab">Invoices & History</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cust-collection-{{ $cust->customer_id }}" type="button" role="tab">Collections & Reminders</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cust-disputes-{{ $cust->customer_id }}" type="button" role="tab">Claims & Returns</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cust-notes-{{ $cust->customer_id }}" type="button" role="tab">Notes & Docs</button>
            </li>
        </ul>

        <div class="tab-content pt-2">
            
            <!-- PROFILE & CREDIT TAB -->
            <div class="tab-pane fade show active" id="cust-profile-{{ $cust->customer_id }}" role="tabpanel">
                <div class="row g-4">
                    <!-- Left: Customer Profile -->
                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="las la-user me-2 text-primary"></i>Customer Profile</h6>
                        <div class="d-flex flex-column">
                            <!-- Contact Person -->
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;">
                                        <i class="las la-id-badge fs-18"></i>
                                    </div>
                                    <span class="text-muted small">Contact Person</span>
                                </div>
                                <span class="fw-bold text-dark small text-end">{{ $cust->customer_name }}</span>
                            </div>

                            <!-- Account Code -->
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;">
                                        <i class="las la-barcode fs-18"></i>
                                    </div>
                                    <span class="text-muted small">Account Code</span>
                                </div>
                                <span class="fw-bold text-dark small text-end">{{ $cust->account_number }}</span>
                            </div>

                            <!-- Phone Number -->
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;">
                                        <i class="las la-phone fs-18"></i>
                                    </div>
                                    <span class="text-muted small">Phone Number</span>
                                </div>
                                <span class="fw-semibold text-dark small text-end">{{ $cust->main_phone }}</span>
                            </div>

                            <!-- Email Address -->
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;">
                                        <i class="las la-envelope fs-18"></i>
                                    </div>
                                    <span class="text-muted small">Email Address</span>
                                </div>
                                <span class="fw-semibold text-dark small text-end">{{ $cust->main_email }}</span>
                            </div>

                            <!-- Billing Address -->
                            <div class="d-flex align-items-start justify-content-between py-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;">
                                        <i class="las la-map-marker fs-18"></i>
                                    </div>
                                    <span class="text-muted small">Billing Address</span>
                                </div>
                                <span class="text-dark small text-end fw-semibold" style="max-width: 280px; display: inline-block; line-height: 1.3;">{{ $cust->billing_address }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Credit Profile -->
                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="las la-shield-alt me-2 text-primary"></i>Credit Profile</h6>
                        <div class="d-flex flex-column">
                            <!-- Credit Limit -->
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;">
                                        <i class="las la-wallet fs-18"></i>
                                    </div>
                                    <span class="text-muted small">Credit Limit</span>
                                </div>
                                <span class="fw-bold text-dark small">₱{{ number_format($cust->credit_limit, 2) }}</span>
                            </div>

                            <!-- Credit Rating -->
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;">
                                        <i class="las la-star fs-18"></i>
                                    </div>
                                    <span class="text-muted small">Credit Rating</span>
                                </div>
                                <span class="badge {{ $cust->credit_rating === 'AAA' ? 'rating-aaa' : ($cust->credit_rating === 'AA' ? 'rating-aa' : 'rating-a') }} badge-rating">
                                    {{ $cust->credit_rating }}
                                </span>
                            </div>

                            <!-- Payment Terms -->
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;">
                                        <i class="las la-calendar-check fs-18"></i>
                                    </div>
                                    <span class="text-muted small">Payment Terms</span>
                                </div>
                                <span class="badge bg-light text-dark border font-weight-600" style="font-size: 0.72rem; padding: 4px 8px;">{{ $cust->payment_terms }}</span>
                            </div>

                            <!-- Overdue Interest -->
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;">
                                        <i class="las la-percent fs-18"></i>
                                    </div>
                                    <span class="text-muted small">Overdue Interest</span>
                                </div>
                                <span class="fw-semibold text-dark small">{{ $cust->interest_rate }}% monthly</span>
                            </div>

                            <!-- Sales Representative -->
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;">
                                        <i class="las la-user-tie fs-18"></i>
                                    </div>
                                    <span class="text-muted small">Sales Representative</span>
                                </div>
                                <select class="form-select form-select-sm py-0 px-2" style="font-size: 0.8rem; height: 28px; width: 160px; border-color: #cbd5e1; border-radius: 4px; box-shadow: none;" onchange="updateSalesRepresentative({{ $cust->customer_id }}, this.value)">
                                    <option value="" {{ is_null($cust->rep) ? 'selected' : '' }}>N/A</option>
                                    <option value="CLE" {{ $cust->rep === 'CLE' ? 'selected' : '' }}>Xavier Almocera</option>
                                    <option value="MKT" {{ $cust->rep === 'MKT' ? 'selected' : '' }}>Kerwin Morfe</option>
                                </select>
                            </div>

                            <!-- Bad Debts -->
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;">
                                        <i class="las la-exclamation-triangle fs-18"></i>
                                    </div>
                                    <span class="text-muted small">Bad Debts</span>
                                </div>
                                <span class="fw-bold text-danger small">₱{{ number_format($cust->bad_debts, 2) }}</span>
                            </div>

                            <!-- Accrued Interest -->
                            <div class="d-flex align-items-center justify-content-between py-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;">
                                        <i class="las la-coins fs-18"></i>
                                    </div>
                                    <span class="text-muted small">Accrued Interest</span>
                                </div>
                                <span class="fw-bold text-dark small">₱{{ number_format($cust->accrued_interest, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BILLING HISTORY & LEGDERS TAB -->
            <div class="tab-pane fade" id="cust-billing-{{ $cust->customer_id }}" role="tabpanel">
                <div class="row mb-3 g-2">
                    <div class="col-md-6">
                        <div class="border rounded p-2 text-center" style="background-color: #fafafa;">
                            <span class="text-muted small d-block">Overdue Balance</span>
                            <strong class="text-danger fs-15">₱{{ number_format($cust->overdue_amount, 2) }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-2 text-center" style="background-color: #fafafa;">
                            <span class="text-muted small d-block">Bad Debts (Written-off)</span>
                            <strong class="text-dark fs-15">₱{{ number_format($cust->bad_debts, 2) }}</strong>
                        </div>
                    </div>
                </div>
                
                <h6 class="fw-bold text-dark mb-2">Billing Invoices Ledger</h6>
                <div class="table-responsive">
                    <table class="table table-hover table-sm small align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice No</th>
                                <th>Invoice Date</th>
                                <th>Sales Representative</th>
                                <th class="text-end">Total Amount</th>
                                <th class="text-end">Paid Amount</th>
                                <th class="text-end">Remaining Balance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cust->invoices as $inv)
                            <tr>
                                <td><span class="fw-bold text-dark">#{{ $inv->so_number }}</span></td>
                                <td>{{ $inv->date }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $inv->sales_rep }}</span></td>
                                <td class="text-end">₱{{ number_format($inv->total_amount, 2) }}</td>
                                <td class="text-end">₱{{ number_format($inv->paid_amount, 2) }}</td>
                                <td class="text-end fw-bold text-dark">₱{{ number_format($inv->remaining_balance, 2) }}</td>
                                <td>
                                    <span class="badge {{ $inv->status === 'Paid' ? 'bg-success text-white' : ($inv->status === 'Partially Paid' ? 'bg-warning text-dark' : 'bg-danger text-white') }}">
                                        {{ $inv->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-3 text-muted">No invoices logged for this customer.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Aging grid summary -->
                <h6 class="fw-bold text-dark mt-3 mb-2">A/R Aging Schedule Buckets</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm small text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Current (0-30 Days)</th>
                                <th>1 - 30 Days Overdue</th>
                                <th>31 - 60 Days Overdue</th>
                                <th>61 - 90 Days Overdue</th>
                                <th>Over 90 Days Overdue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-semibold text-dark">₱{{ number_format($cust->outstanding_balance - $cust->overdue_amount, 2) }}</td>
                                <td class="fw-semibold {{ $cust->overdue_amount > 0 ? 'text-danger' : 'text-muted' }}">₱{{ number_format($cust->overdue_amount * 0.6, 2) }}</td>
                                <td class="fw-semibold {{ $cust->overdue_amount > 0 ? 'text-danger' : 'text-muted' }}">₱{{ number_format($cust->overdue_amount * 0.4, 2) }}</td>
                                <td class="fw-semibold text-muted">₱0.00</td>
                                <td class="fw-semibold text-muted">₱0.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- COLLECTIONS & REMINDERS TAB -->
            <div class="tab-pane fade" id="cust-collection-{{ $cust->customer_id }}" role="tabpanel">
                <h6 class="fw-bold text-dark mb-2">Collection History & Payments Received</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-hover table-sm small align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Receipt No</th>
                                <th>Payment Date</th>
                                <th>Payment Method</th>
                                <th class="text-end">Amount Collected</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">No collection receipts logged in the database.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="fw-bold text-dark mb-2">Automatic Reminders Schedule</h6>
                <div class="table-responsive">
                    <table class="table table-hover table-sm small align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Reminder Level</th>
                                <th>Next Scheduled Date</th>
                                <th>Medium Type</th>
                                <th>Assigned Action</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">No reminders scheduled.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CLAIMS & RETURNS TAB -->
            <div class="tab-pane fade" id="cust-disputes-{{ $cust->customer_id }}" role="tabpanel">
                <h6 class="fw-bold text-dark mb-2">Chargebacks, Returns & Customer Claims</h6>
                <div class="table-responsive">
                    <table class="table table-hover table-sm small align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Claim Reference</th>
                                <th>Claim Date</th>
                                <th>Type</th>
                                <th>Item Description / Remarks</th>
                                <th class="text-end">Disputed Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">No returns, chargebacks, or active claims logged for this customer.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- NOTES & DOCUMENTS TAB -->
            <div class="tab-pane fade" id="cust-notes-{{ $cust->customer_id }}" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-2"><i class="las la-comments me-2"></i>Collection Follow-up Notes</h6>
                        <div class="small p-3 rounded border bg-light mb-3" style="max-height: 250px; overflow-y: auto;">
                            <p class="text-muted italic mb-0">No collection remarks logged for this customer.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-2"><i class="las la-paperclip me-2"></i>Supporting Documents</h6>
                        <div class="list-group list-group-flush small">
                            <p class="text-muted italic mb-0">No supporting documents uploaded.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endforeach

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // --- CLIENT-SIDE TABLE PAGINATION FOR CARD LEDGER MODALS ---
        function initTablePagination(tableElement, itemsPerPage = 5) {
            const tbody = tableElement.querySelector('tbody');
            if (!tbody) return;
            
            const rows = Array.from(tbody.querySelectorAll('tr'));
            if (rows.length === 1 && rows[0].querySelector('td[colspan]')) return;
            if (rows.length <= itemsPerPage) return;
            
            const totalItems = rows.length;
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            let currentPage = 1;
            
            const nav = document.createElement('nav');
            nav.className = 'd-flex justify-content-between align-items-center mt-3';
            
            const info = document.createElement('div');
            info.className = 'small text-muted';
            
            const ul = document.createElement('ul');
            ul.className = 'pagination mb-0';
            
            nav.appendChild(info);
            nav.appendChild(ul);
            
            const wrapper = tableElement.closest('.table-responsive') || tableElement;
            wrapper.parentNode.appendChild(nav);
            
            function showPage(page) {
                currentPage = page;
                const start = (page - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                
                rows.forEach((row, idx) => {
                    if (idx >= start && idx < end) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                info.textContent = `Showing ${start + 1} to ${Math.min(end, totalItems)} of ${totalItems} entries`;
                ul.innerHTML = '';
                
                // Prev
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<a class="page-link" href="#">&laquo;</a>`;
                prevLi.querySelector('a').onclick = (e) => {
                    e.preventDefault();
                    if (currentPage > 1) showPage(currentPage - 1);
                };
                ul.appendChild(prevLi);
                
                // Numbers
                for (let i = 1; i <= totalPages; i++) {
                    if (totalPages > 5) {
                        if (i !== 1 && i !== totalPages && Math.abs(i - currentPage) > 1) {
                            if (i === 2 || i === totalPages - 1) {
                                const dotsLi = document.createElement('li');
                                dotsLi.className = 'page-item disabled';
                                dotsLi.innerHTML = '<span class="page-link">...</span>';
                                ul.appendChild(dotsLi);
                            }
                            continue;
                        }
                    }
                    
                    const li = document.createElement('li');
                    li.className = `page-item ${currentPage === i ? 'active' : ''}`;
                    li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    li.querySelector('a').onclick = (e) => {
                        e.preventDefault();
                        showPage(i);
                    };
                    ul.appendChild(li);
                }
                
                // Next
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                nextLi.innerHTML = `<a class="page-link" href="#">&raquo;</a>`;
                nextLi.querySelector('a').onclick = (e) => {
                    e.preventDefault();
                    if (currentPage < totalPages) showPage(currentPage + 1);
                };
                ul.appendChild(nextLi);
            }
            
            showPage(1);
        }

        function showSalesLedgerModal(title, contentHtml) {
            document.getElementById('salesLedgerModalLabel').innerText = title;
            const body = document.getElementById('salesLedgerModalBody');
            body.innerHTML = contentHtml;

            // Re-instantiate pagination on any tables inside loaded tab panes
            const tables = body.querySelectorAll('table.table-hover');
            tables.forEach(table => {
                initTablePagination(table, 5);
            });

            const modal = new bootstrap.Modal(document.getElementById('salesLedgerModal'));
            modal.show();
        }

        function updateSalesRepresentative(customerId, val) {
            const url = `/admin-finance/customers/${customerId}/update-rep`;
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ rep: val })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update header text in modal dynamically
                    const header = document.getElementById(`modal-rep-header-${customerId}`);
                    if (header) {
                        const selectEl = document.querySelector(`#salesLedgerModal select[onchange*="${customerId}"]`);
                        if (selectEl) {
                            const text = selectEl.options[selectEl.selectedIndex].text;
                            const termsText = header.innerText.split('|')[0].trim();
                            header.innerText = `${termsText} | Assigned: ${text}`;
                        }
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Sales Representative updated successfully!',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('JS Error: ' + error.message + '\nStack: ' + error.stack);
            });
        }

    </script>
    @endpush
</x-app-layout>
