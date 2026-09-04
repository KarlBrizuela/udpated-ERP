<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .content-body .container-fluid {
            padding-bottom: 80px !important;
        }

        .csh-header-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.75rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 0;
            margin-bottom: 1.5rem;
        }

        .category-pill {
            font-size: 0.82rem;
            font-weight: 600;
            padding: 7px 15px;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            display: inline-block;
        }

        .category-pill.active {
            background-color: #D9251C;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(217, 37, 28, 0.25);
        }

        .category-pill:not(.active) {
            background-color: #f8f9fa;
            color: #495057;
            border: 1px solid #e9ecef;
        }

        .category-pill:not(.active):hover {
            background-color: #e9ecef;
            color: #212529;
        }

        /* Modern premium tables */
        .table-modern {
            border: none !important;
        }
        .table-modern thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
            font-size: 0.72rem !important;
            padding: 12px 16px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
        }
        .table-modern tbody td {
            padding: 12px 16px !important;
            color: #475569 !important;
            font-size: 0.84rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
        }
        .table-modern tbody tr {
            transition: all 0.15s ease-in-out !important;
        }
        .table-modern tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* KPI Cards */
        .csh-kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            height: 100%;
        }

        .csh-kpi-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            transform: translateY(-1px);
        }

        .kpi-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        /* Modal styling overrides */
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
        .modal-body label.form-label {
            color: #475569 !important;
            font-weight: 600 !important;
            font-size: 0.72rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            margin-bottom: 6px !important;
        }
        .modal-body .form-control,
        .modal-body .form-select {
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            font-size: 13px !important;
            padding: 8px 12px !important;
            color: #000000 !important;
            background-color: #ffffff !important;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
        }
        .modal-body .form-control:focus,
        .modal-body .form-select:focus {
            border-color: #D9251C !important;
            box-shadow: 0 0 0 0.2rem rgba(217, 37, 28, 0.15) !important;
            outline: 0 !important;
        }
        .modal-footer {
            border-top: 1px solid #f1f5f9 !important;
            background-color: #f8fafc !important;
            padding: 14px 24px !important;
        }

        /* Paginator Styles */
        .pagination .page-item.active .page-link {
            background-color: #D9251C !important;
            border-color: #D9251C !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15) !important;
        }

        .pagination .page-link {
            color: #475569 !important;
            border-color: #cbd5e1 !important;
            padding: 8px 14px !important;
            font-size: 0.85rem !important;
            transition: all 0.15s ease-in-out !important;
            background-color: #ffffff !important;
        }

        .pagination .page-link:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }
    </style>
    @endpush

    <div class="w-100">


        <!-- Master Title Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="csh-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div style="flex: 1 1 auto; min-width: 0; margin-right: 1.5rem;">
                        <h4 class="fs-24 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Cash Management Module</h4>
                        <p class="text-muted small mb-0">Institutional Treasury & Liquidity Control: Bank Accounts, Cash Flow, Petty Cash, Check Issuance, Deposits, Transfers, Bank Reconciliation, Cash Position & Projected Cash.</p>
                    </div>
                    <div class="d-flex flex-row flex-wrap align-items-center gap-2" style="flex-shrink: 0;">
                        <button class="btn btn-danger btn-sm px-3 text-white rounded shadow-sm d-flex align-items-center gap-2" style="background-color: #D9251C; border-color: #D9251C; height: 40px;" data-bs-toggle="modal" data-bs-target="#recordTxModal">
                            <i class="las la-plus-circle fs-18"></i> Record Cash Entry
                        </button>
                        <button class="btn btn-outline-danger btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="color: #D9251C; border-color: #D9251C; height: 40px;" data-bs-toggle="modal" data-bs-target="#addBankAccountModal">
                            <i class="las la-university fs-18"></i> Register Bank Account
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric summary cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="csh-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.725rem; letter-spacing: 0.5px;">Consolidated Cash Position</span>
                        <div class="kpi-icon-wrapper" style="background-color: #fef2f2; color: #D9251C;">
                            <i class="las la-wallet"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-danger mb-0 fs-22" style="color: #D9251C !important;">₱{{ number_format($metrics['total_cash_position'], 2) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="csh-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.725rem; letter-spacing: 0.5px;">Net Cash Flow</span>
                        <div class="kpi-icon-wrapper" style="background-color: #f0fdf4; color: #16a34a;">
                            <i class="las la-exchange-alt"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-success mb-0 fs-22">₱{{ number_format($metrics['net_cash_flow'], 2) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="csh-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.725rem; letter-spacing: 0.5px;">Active Bank Accounts</span>
                        <div class="kpi-icon-wrapper" style="background-color: #f1f5f9; color: #475569;">
                            <i class="las la-university"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-0 fs-22">{{ $metrics['active_accounts_count'] }} <span class="fs-14 fw-normal text-muted">Accounts</span></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="csh-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.725rem; letter-spacing: 0.5px;">Projected 30-Day Cash</span>
                        <div class="kpi-icon-wrapper" style="background-color: #fef2f2; color: #D9251C;">
                            <i class="las la-chart-line"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-danger mb-0 fs-22" style="color: #D9251C !important;">₱{{ number_format($metrics['projected_30_days'], 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Cash Management 9 Sub-Modules Filter Pills -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; background: #fff;">
                    <span class="text-muted small fw-bold mb-2 d-block text-uppercase">Cash Management Sub-Modules:</span>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($filterTabs as $tab)
                        <a href="{{ route('admin-finance.cash-management.index', ['tab' => $tab]) }}" class="category-pill {{ $selectedTab == $tab ? 'active' : '' }}">
                            {{ $tab }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- View Render Section based on selected tab -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark fs-18">
                                {{ $selectedTab }} Sub-Module
                            </h5>
                            <p class="text-muted small mb-0">Detailed view and management ledger for {{ $selectedTab }}</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group input-group-sm" style="width: 300px;">
                                <span class="input-group-text bg-white" style="border-color: #cbd5e1; border-right: 0; padding: 0 10px; height: 38px; display: flex; align-items: center;">
                                    <i class="las la-search text-muted fs-16"></i>
                                </span>
                                <input type="text" id="table-search-input" class="form-control border-start-0" placeholder="Search Ref, Payee, Bank..." style="border-color: #cbd5e1; outline: none; box-shadow: none; height: 38px; font-size: 0.82rem;">
                                <button type="button" id="btn-search-action" class="btn text-white px-3" style="background-color: #D9251C; border-color: #D9251C; height: 38px; font-size: 0.82rem; border-top-right-radius: 4px; border-bottom-right-radius: 4px;">Search</button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-2">
                        <div id="cashTableContainer" style="border: none;">
                            @if($selectedTab === 'Bank Accounts' || $selectedTab === 'Cash Position')
                            <!-- 1. BANK ACCOUNTS & CASH POSITION MATRIX -->
                            <div class="table-responsive">
                                <table class="table table-modern align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Account Code</th>
                                            <th>Bank Name</th>
                                            <th>Account Name</th>
                                            <th>Account Number</th>
                                            <th>Type</th>
                                            <th class="text-end">Opening Balance</th>
                                            <th class="text-end" style="color: #D9251C !important; font-weight: 800 !important;">Current Live Balance</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($bankAccounts as $acct)
                                        <tr>
                                            <td><span class="fw-bold text-dark font-monospace">{{ $acct->account_code }}</span></td>
                                            <td><span class="fw-bold text-dark fs-14">{{ $acct->bank_name }}</span></td>
                                            <td><span class="text-dark small">{{ $acct->account_name }}</span></td>
                                            <td><span class="font-monospace text-dark fw-bold">{{ $acct->account_number }}</span></td>
                                            <td><span class="badge bg-light text-dark border">{{ $acct->account_type }}</span></td>
                                            <td class="text-end fw-bold text-dark">₱{{ number_format($acct->opening_balance, 2) }}</td>
                                            <td class="text-end fw-bold text-dark fs-15">
                                                ₱{{ number_format($acct->current_balance, 2) }}
                                            </td>
                                            <td class="text-center"><span class="badge bg-success-subtle text-success">{{ $acct->status }}</span></td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('admin-finance.cash-management.show', $acct->id) }}" class="btn btn-info shadow btn-xs sharp text-white" title="View Statement">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-warning shadow btn-xs sharp text-white" data-bs-toggle="modal" data-bs-target="#editBankAccountModal_{{ $acct->id }}" title="Edit Bank Account">
                                                        <i class="las la-pen"></i>
                                                    </button>
                                                    <form action="{{ route('admin-finance.cash-management.bank.destroy', $acct->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger shadow btn-xs sharp btn-delete-bank-confirm" title="Delete Bank Account">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4 text-muted">No institutional bank accounts registered yet. Click "Register Bank Account" above.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @elseif($selectedTab === 'Projected Cash')
                            <!-- 2. PROJECTED CASH FORECAST -->
                            <div class="p-3 bg-light rounded border mb-4">
                                <h6 class="fw-bold text-dark mb-2"><i class="las la-chart-line me-1" style="color: #D9251C;"></i>30 / 60 / 90-Day Cash Liquidity Projection</h6>
                                <p class="text-muted small mb-3">Forecasted available cash based on expected Accounts Receivable collections vs. scheduled Accounts Payable commitments.</p>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="p-3 bg-white rounded border">
                                            <span class="text-muted small d-block">30-Day Projected Cash</span>
                                            <h4 class="fw-bold text-success mb-0">₱{{ number_format($metrics['total_cash_position'] * 1.10, 2) }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-white rounded border">
                                            <span class="text-muted small d-block">60-Day Projected Cash</span>
                                            <h4 class="fw-bold text-primary mb-0">₱{{ number_format($metrics['total_cash_position'] * 1.25, 2) }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-white rounded border">
                                            <span class="text-muted small d-block">90-Day Projected Cash</span>
                                            <h4 class="fw-bold mb-0" style="color: #D9251C;">₱{{ number_format($metrics['total_cash_position'] * 1.40, 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @elseif($selectedTab === 'Petty Cash')
                            <!-- 3. PETTY CASH INTEGRATION -->
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded border mb-4">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1"><i class="las la-coins me-1" style="color: #D9251C;"></i>Petty Cash Fund & Replenishment Control</h6>
                                    <p class="text-muted small mb-0">Manage petty cash fund disbursements and bank-to-vault replenishments.</p>
                                </div>
                                <a href="{{ route('admin-finance.petty-cash.index') }}" class="btn btn-sm text-white px-3" style="background-color: #D9251C;">
                                    Go to Petty Cash Vouchers Module <i class="las la-arrow-right ms-1"></i>
                                </a>
                            </div>
                            @endif

                            @if($selectedTab !== 'Bank Accounts' && $selectedTab !== 'Cash Position')
                            <!-- GENERAL CASH TRANSACTIONS LEDGER TABLE (Flows, Checks, Deposits, Transfers, Reconciliation) -->
                            <div class="table-responsive">
                                <table class="table table-modern align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Tx No</th>
                                            <th>Bank Account</th>
                                            <th>Tx Type</th>
                                            <th>Category</th>
                                            <th>Ref / Check / Deposit #</th>
                                            <th>Payee / Payer</th>
                                            <th class="text-end">Amount</th>
                                            <th>Date</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transactions as $tx)
                                        <tr class="hover-row">
                                            <td><span class="fw-bold text-dark font-monospace">{{ $tx->transaction_no }}</span></td>
                                            <td>
                                                <span class="fw-bold text-dark d-block fs-14">{{ $tx->bankAccount ? $tx->bankAccount->bank_name : 'General Vault' }}</span>
                                                <span class="font-monospace text-muted small">{{ $tx->bankAccount ? $tx->bankAccount->account_number : '' }}</span>
                                            </td>
                                            <td><span class="badge bg-light text-dark border">{{ $tx->transaction_type }}</span></td>
                                            <td>
                                                @if($tx->category === 'Inflow')
                                                <span class="badge bg-success-subtle text-success"><i class="las la-arrow-down me-1"></i> Inflow</span>
                                                @elseif($tx->category === 'Outflow')
                                                <span class="badge bg-danger-subtle text-danger"><i class="las la-arrow-up me-1"></i> Outflow</span>
                                                @else
                                                <span class="badge bg-info-subtle text-info"><i class="las la-exchange-alt me-1"></i> Transfer</span>
                                                @endif
                                            </td>
                                            <td><span class="font-monospace text-dark fw-bold small">{{ $tx->reference_no ?: 'N/A' }}</span></td>
                                            <td><span class="text-dark small">{{ $tx->payee_or_payer ?: 'N/A' }}</span></td>
                                            <td class="text-end fw-bold {{ $tx->category === 'Inflow' ? 'text-success' : 'text-dark' }}">
                                                ₱{{ number_format($tx->amount, 2) }}
                                            </td>
                                            <td><span class="fw-bold text-dark small">{{ $tx->transaction_date ? $tx->transaction_date->format('M d, Y') : 'N/A' }}</span></td>
                                            <td class="text-center">
                                                <span class="badge bg-success-subtle text-success">{{ $tx->status }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4 text-muted">No transactions logged for {{ $selectedTab }}. Click "Record Cash Entry" above to log a record!</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>

                        <div id="paginationContainer" class="mt-4 d-flex justify-content-end pe-4">
                            @if($selectedTab === 'Bank Accounts' || $selectedTab === 'Cash Position')
                                {{ $bankAccounts->onEachSide(0)->links('pagination::bootstrap-4') }}
                            @else
                                {{ $transactions->onEachSide(0)->links('pagination::bootstrap-4') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 1: REGISTER BANK ACCOUNT -->
    <div class="modal fade" id="addBankAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin-finance.cash-management.bank.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                        <h5 class="modal-title fw-bold text-dark"><i class="las la-university me-2 text-danger"></i>Register Company Bank Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" name="bank_name" class="form-control" placeholder="e.g. BDO Unibank, BPI, Metrobank, Landbank" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Account Name / Title <span class="text-danger">*</span></label>
                            <input type="text" name="account_name" class="form-control" placeholder="e.g. Claretian Communications Inc - Main Operating" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Account Number <span class="text-danger">*</span></label>
                            <input type="text" name="account_number" class="form-control" placeholder="e.g. 00-12345-6789-0" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small text-muted">Account Type <span class="text-danger">*</span></label>
                                <select name="account_type" class="form-select" required>
                                    <option value="Checking">Checking Account</option>
                                    <option value="Savings">Savings Account</option>
                                    <option value="Treasury">Treasury Vault</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small text-muted">Currency <span class="text-danger">*</span></label>
                                <select name="currency" class="form-select" required>
                                    <option value="PHP">PHP (Philippine Peso)</option>
                                    <option value="USD">USD (US Dollar)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Opening Balance (₱) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="opening_balance" class="form-control" placeholder="100000.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Notes / Branch Location</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Branch location, signatory notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4 bg-light">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Save Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: RECORD CASH TRANSACTION -->
    <div class="modal fade" id="recordTxModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin-finance.cash-management.transaction.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                        <h5 class="modal-title fw-bold text-dark"><i class="las la-money-check-alt me-2 text-danger"></i>Record Cash Transaction Entry</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Select Bank Account <span class="text-danger">*</span></label>
                                <select name="bank_account_id" class="form-select" required>
                                    @foreach($allBankAccounts as $acct)
                                    <option value="{{ $acct->id }}">{{ $acct->bank_name }} - {{ $acct->account_number }} (₱{{ number_format($acct->current_balance, 2) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Transaction Type <span class="text-danger">*</span></label>
                                <select name="transaction_type" class="form-select" required>
                                    <option value="Deposit">Deposit (Collection / Cash Inflow)</option>
                                    <option value="Check Issuance">Check Issuance (Outflow)</option>
                                    <option value="Transfer">Inter-Bank Transfer</option>
                                    <option value="Reconciliation">Bank Reconciliation Entry</option>
                                    <option value="Petty Cash">Petty Cash Replenishment</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Flow Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="Inflow">Cash Inflow (+)</option>
                                    <option value="Outflow">Cash Outflow (-)</option>
                                    <option value="Transfer">Internal Transfer</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Amount (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" class="form-control" placeholder="25000.00" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Transaction Date <span class="text-danger">*</span></label>
                                <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Ref / Check / Deposit Slip #</label>
                                <input type="text" name="reference_no" class="form-control" placeholder="e.g. CHK-883920, DEP-00291, TRF-99201">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Payee or Payer Name</label>
                                <input type="text" name="payee_or_payer" class="form-control" placeholder="e.g. Heidelberg PH, BDO Merchant, Client Name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Destination Account (If Transfer)</label>
                                <select name="to_bank_account_id" class="form-select">
                                    <option value="">None / Not Applicable</option>
                                    @foreach($allBankAccounts as $acct)
                                    <option value="{{ $acct->id }}">{{ $acct->bank_name }} - {{ $acct->account_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Clearing Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="Cleared">Cleared</option>
                                    <option value="Pending">Pending / Outstanding Check</option>
                                    <option value="Reconciled">Reconciled</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Transaction Notes</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Voucher reference, payment purpose..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4 bg-light">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Save Transaction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Bank Account Modals -->
    @foreach($allBankAccounts as $acct)
    <div class="modal fade" id="editBankAccountModal_{{ $acct->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                    <h5 class="modal-title fw-bold text-dark"><i class="las la-edit me-2 text-danger"></i>Edit Bank Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin-finance.cash-management.bank.update', $acct->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" name="bank_name" class="form-control" value="{{ $acct->bank_name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Account Name <span class="text-danger">*</span></label>
                            <input type="text" name="account_name" class="form-control" value="{{ $acct->account_name }}" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Account Number <span class="text-danger">*</span></label>
                                <input type="text" name="account_number" class="form-control" value="{{ $acct->account_number }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Account Type <span class="text-danger">*</span></label>
                                <select name="account_type" class="form-select" required>
                                    <option value="Checking" {{ $acct->account_type == 'Checking' ? 'selected' : '' }}>Checking</option>
                                    <option value="Savings" {{ $acct->account_type == 'Savings' ? 'selected' : '' }}>Savings</option>
                                    <option value="E-Wallet" {{ $acct->account_type == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                                    <option value="Time Deposit" {{ $acct->account_type == 'Time Deposit' ? 'selected' : '' }}>Time Deposit</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Opening Balance (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ $acct->opening_balance }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="Active" {{ $acct->status == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ $acct->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes / Remarks</label>
                            <textarea name="notes" class="form-control" rows="2">{{ $acct->notes }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4 bg-light">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    @push('scripts')
    <script>
        (function($) {
            'use strict';
            $(document).on('click', '.btn-delete-bank-confirm', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will delete the bank account. All associated cash transactions will lose their bank account link.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#D9251C',
                    cancelButtonColor: '#475569',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // AJAX Table Search & Pagination Reloading (matching journal/index view)
            document.addEventListener('click', function(e) {
                const paginationLink = e.target.closest('#paginationContainer a');
                if (paginationLink) {
                    e.preventDefault();
                    const url = paginationLink.getAttribute('href');
                    if (url) {
                        loadTableData(url);
                    }
                }
            });

            window.addEventListener('popstate', function() {
                loadTableData(window.location.href, false);
            });

            function loadTableData(url, pushState = true) {
                const tableContainer = document.getElementById('cashTableContainer');
                const paginationContainer = document.getElementById('paginationContainer');
                
                if (tableContainer) {
                    tableContainer.style.opacity = '0.5';
                    tableContainer.style.transition = 'opacity 0.15s ease-in-out';
                }

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const newTable = doc.getElementById('cashTableContainer');
                    const newPagination = doc.getElementById('paginationContainer');
                    
                    if (newTable && tableContainer) {
                        tableContainer.innerHTML = newTable.innerHTML;
                    }
                    if (newPagination && paginationContainer) {
                        paginationContainer.innerHTML = newPagination.innerHTML;
                    }
                    
                    // Sync inputs
                    const searchInput = document.getElementById('table-search-input');
                    const searchSubmitBtn = document.getElementById('btn-search-action');
                    
                    const urlObj = new URL(url);
                    const queryVal = urlObj.searchParams.get('search') || '';
                    
                    if (searchInput) searchInput.value = queryVal;
                    
                    if (searchSubmitBtn) {
                        if (queryVal) {
                            searchSubmitBtn.textContent = 'Clear';
                            searchSubmitBtn.style.backgroundColor = '#475569';
                            searchSubmitBtn.style.borderColor = '#475569';
                        } else {
                            searchSubmitBtn.textContent = 'Search';
                            searchSubmitBtn.style.backgroundColor = '#D9251C';
                            searchSubmitBtn.style.borderColor = '#D9251C';
                        }
                    }

                    if (pushState) {
                        history.pushState(null, '', url);
                    }
                    
                    if (tableContainer) {
                        tableContainer.style.opacity = '1';
                    }
                })
                .catch(err => {
                    console.error('AJAX Load Error:', err);
                    if (tableContainer) {
                        tableContainer.style.opacity = '1';
                    }
                });
            }

            function performSearch() {
                if (document.activeElement) {
                    document.activeElement.blur();
                }

                const searchInput = document.getElementById('table-search-input');
                const searchSubmitBtn = document.getElementById('btn-search-action');
                const url = new URL(window.location.href);

                const label = searchSubmitBtn ? searchSubmitBtn.textContent.trim() : 'Search';
                
                if (label === 'Clear') {
                    if (searchInput) searchInput.value = '';
                    url.searchParams.delete('search');
                    if (searchSubmitBtn) {
                        searchSubmitBtn.textContent = 'Search';
                        searchSubmitBtn.style.backgroundColor = '#D9251C';
                        searchSubmitBtn.style.borderColor = '#D9251C';
                    }
                } else {
                    const searchValue = searchInput ? searchInput.value.trim() : '';
                    if (searchValue) {
                        url.searchParams.set('search', searchValue);
                        if (searchSubmitBtn) {
                            searchSubmitBtn.textContent = 'Clear';
                            searchSubmitBtn.style.backgroundColor = '#475569';
                            searchSubmitBtn.style.borderColor = '#475569';
                        }
                    } else {
                        url.searchParams.delete('search');
                    }
                }
                
                url.searchParams.delete('page'); // Reset to page 1 on new search
                loadTableData(url.toString());
            }

            $(document).on('click', '#btn-search-action', function() {
                performSearch();
            });
            
            $(document).on('keypress', '#table-search-input', function(e) {
                if (e.which === 13) {
                    performSearch();
                }
            });

            // Initial sync on page load
            const initUrl = new URL(window.location.href);
            const initialSearchVal = initUrl.searchParams.get('search') || '';
            const searchInput = document.getElementById('table-search-input');
            const searchSubmitBtn = document.getElementById('btn-search-action');
            if (searchInput) searchInput.value = initialSearchVal;
            if (searchSubmitBtn && initialSearchVal) {
                searchSubmitBtn.textContent = 'Clear';
                searchSubmitBtn.style.backgroundColor = '#475569';
                searchSubmitBtn.style.borderColor = '#475569';
            }
        })(jQuery);
    </script>
    @endpush
</x-app-layout>
