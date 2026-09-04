<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .hover-row {
            transition: background-color 0.2s ease;
        }

        .hover-row:hover {
            background-color: #fafafa !important;
        }

        .transition-icon {
            transition: transform 0.2s ease;
        }

        .cursor-pointer[aria-expanded="true"] .transition-icon {
            transform: rotate(180deg);
        }

        .bg-soft-success {
            background-color: rgba(40, 167, 69, 0.1);
        }

        .text-success {
            color: #28a745 !important;
        }

        /* Premium Dashboard KPI Cards Styling */
        .hover-card {
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .hover-card:hover {
            transform: translateY(-4px);
            background-color: #ffffff !important;
        }
        
        .assets-card:hover {
            border-color: #3b82f6 !important;
            box-shadow: 0 12px 24px -5px rgba(59, 130, 246, 0.12), 0 4px 12px -2px rgba(59, 130, 246, 0.08) !important;
        }
        .liabilities-card:hover {
            border-color: #f59e0b !important;
            box-shadow: 0 12px 24px -5px rgba(245, 158, 11, 0.12), 0 4px 12px -2px rgba(245, 158, 11, 0.08) !important;
        }
        .equity-card:hover {
            border-color: #8b5cf6 !important;
            box-shadow: 0 12px 24px -5px rgba(139, 92, 246, 0.12), 0 4px 12px -2px rgba(139, 92, 246, 0.08) !important;
        }
        .income-card:hover {
            border-color: #10b981 !important;
            box-shadow: 0 12px 24px -5px rgba(16, 185, 129, 0.12), 0 4px 12px -2px rgba(16, 185, 129, 0.08) !important;
        }
        .expenses-card:hover {
            border-color: #ef4444 !important;
            box-shadow: 0 12px 24px -5px rgba(239, 68, 68, 0.12), 0 4px 12px -2px rgba(239, 68, 68, 0.08) !important;
        }

        /* Maximize page width by overriding layout container padding */
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
        }

        /* Modal Table Styling (using General Journal as reference) */
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

        .modal-body {
            padding: 20px 24px !important;
        }

        .modal-body table {
            margin-bottom: 0 !important;
            border: none !important;
        }

        .modal-body table thead th {
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

        .modal-body table tbody td {
            padding: 12px 16px !important;
            font-size: 0.82rem !important;
            color: #475569 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            vertical-align: middle !important;
        }

        .modal-body table tbody tr {
            transition: all 0.15s ease-in-out !important;
        }

        .modal-body table tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* Modal Pagination Style Matching */
        .modal nav {
            padding: 12px 16px 0 16px !important;
            border-top: 1px solid #f1f5f9 !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
        }

        .modal .pagination {
            display: flex !important;
            gap: 5px !important;
            margin: 0 !important;
            padding: 0 !important;
            list-style: none !important;
            align-items: center !important;
        }

        .modal .pagination .page-item {
            margin: 0 !important;
            padding: 0 !important;
        }

        .modal .pagination .page-link {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 28px !important;
            height: 28px !important;
            padding: 0 8px !important;
            border-radius: 6px !important;
            font-size: 0.76rem !important;
            font-weight: 600 !important;
            color: #475569 !important;
            border: 1px solid #cbd5e1 !important;
            background-color: #ffffff !important;
            transition: all 0.15s ease-in-out !important;
            text-decoration: none !important;
            box-shadow: none !important;
            outline: none !important;
        }

        .modal .pagination .page-link:hover {
            background-color: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }

        .modal .pagination .page-item.active .page-link {
            background-color: #D9251C !important;
            border-color: #D9251C !important;
            color: #ffffff !important;
            box-shadow: none !important;
        }

        .modal .pagination .page-item.disabled .page-link {
            color: #94a3b8 !important;
            border-color: #e2e8f0 !important;
            background-color: #f8fafc !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }
    </style>
    @endpush

    <div class="container-fluid p-0">
        <!-- Top Title & Sleek Segmented Main Tab Switcher -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-2" style="border-radius: 8px; background-color: #ffffff; border: 1px solid #e2e8f0;">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <!-- Main Tabs: CRUD vs Cards vs Account Groups -->
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <a href="?main_tab=crud" class="btn btn-sm fw-bold px-3 py-2 d-flex align-items-center gap-2" style="border-radius: 6px; transition: all 0.2s; {{ $mainTab === 'crud' ? 'background-color: #D9251C; color: #ffffff;' : 'background-color: transparent; color: #475569; border: 1px solid #cbd5e1;' }}">
                                <i class="las la-list-alt fs-16"></i> Account Management (CRUD)
                            </a>
                            <a href="?main_tab=cards&tab=assets" class="btn btn-sm fw-bold px-3 py-2 d-flex align-items-center gap-2" style="border-radius: 6px; transition: all 0.2s; {{ $mainTab === 'cards' ? 'background-color: #D9251C; color: #ffffff;' : 'background-color: transparent; color: #475569; border: 1px solid #cbd5e1;' }}">
                                <i class="las la-th-large fs-16"></i> Accounts Cards Overview
                            </a>
                            <a href="?main_tab=account_groups" class="btn btn-sm fw-bold px-3 py-2 d-flex align-items-center gap-2" style="border-radius: 6px; transition: all 0.2s; {{ $mainTab === 'account_groups' ? 'background-color: #D9251C; color: #ffffff;' : 'background-color: transparent; color: #475569; border: 1px solid #cbd5e1;' }}">
                                <i class="las la-layer-group fs-16"></i> Account Groups Management
                            </a>
                        </div>

                        <!-- Sub-Tabs for Category Cards (Visible only when Cards tab is active) -->
                        @if($mainTab === 'cards')
                        <div class="d-flex flex-wrap gap-1">
                            <a href="?main_tab=cards&tab=assets" class="btn btn-xs fw-bold px-2.5 py-1.5 d-flex align-items-center gap-1" style="border-radius: 4px; transition: all 0.2s; {{ $tab === 'assets' ? 'background-color: #3b82f6; color: #ffffff;' : 'background-color: #f8fafc; color: #475569; border: 1px solid #cbd5e1;' }}">
                                <i class="las la-wallet"></i> Assets
                            </a>
                            <a href="?main_tab=cards&tab=liabilities" class="btn btn-xs fw-bold px-2.5 py-1.5 d-flex align-items-center gap-1" style="border-radius: 4px; transition: all 0.2s; {{ $tab === 'liabilities' ? 'background-color: #f59e0b; color: #ffffff;' : 'background-color: #f8fafc; color: #475569; border: 1px solid #cbd5e1;' }}">
                                <i class="las la-credit-card"></i> Liabilities
                            </a>
                            <a href="?main_tab=cards&tab=equity" class="btn btn-xs fw-bold px-2.5 py-1.5 d-flex align-items-center gap-1" style="border-radius: 4px; transition: all 0.2s; {{ $tab === 'equity' ? 'background-color: #8b5cf6; color: #ffffff;' : 'background-color: #f8fafc; color: #475569; border: 1px solid #cbd5e1;' }}">
                                <i class="las la-coins"></i> Equity
                            </a>
                            <a href="?main_tab=cards&tab=income" class="btn btn-xs fw-bold px-2.5 py-1.5 d-flex align-items-center gap-1" style="border-radius: 4px; transition: all 0.2s; {{ $tab === 'income' ? 'background-color: #10b981; color: #ffffff;' : 'background-color: #f8fafc; color: #475569; border: 1px solid #cbd5e1;' }}">
                                <i class="las la-chart-line"></i> Income
                            </a>
                            <a href="?main_tab=cards&tab=expenses" class="btn btn-xs fw-bold px-2.5 py-1.5 d-flex align-items-center gap-1" style="border-radius: 4px; transition: all 0.2s; {{ $tab === 'expenses' ? 'background-color: #ef4444; color: #ffffff;' : 'background-color: #f8fafc; color: #475569; border: 1px solid #cbd5e1;' }}">
                                <i class="las la-file-invoice-dollar"></i> Expenses
                            </a>
                        </div>
                        @endif

                        <div>
                            <button class="btn btn-sm px-3 d-flex align-items-center gap-2 fw-bold" style="background-color: #ffffff; border: 1px solid #cbd5e1; color: #475569; height: 36px; border-radius: 6px;" onclick="window.print()">
                                <i class="las la-print fs-16"></i> Print Chart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 1: Account Management (CRUD Datatable) -->
        @if($mainTab === 'crud')
            @include('admin-finance.accounting.chart-of-accounts.crud-table')
        @elseif($mainTab === 'account_groups')
            @include('admin-finance.accounting.chart-of-accounts.account-groups-table')
        @else
            <!-- Tab 2: Accounts Cards Overview -->
            <div class="row">
                <div class="col-12">
                    @if($tab === 'assets')
                        @include('admin-finance.accounting.chart-of-accounts.assets')
                    @elseif($tab === 'liabilities')
                        @include('admin-finance.accounting.chart-of-accounts.liabilities')
                    @elseif($tab === 'equity')
                        @include('admin-finance.accounting.chart-of-accounts.equity')
                    @elseif($tab === 'income')
                        @include('admin-finance.accounting.chart-of-accounts.income')
                    @elseif($tab === 'expenses')
                        @include('admin-finance.accounting.chart-of-accounts.expenses')
                    @endif
                </div>
            </div>

            @if(isset($uncategorizedAccounts) && count($uncategorizedAccounts) > 0)
            @php
                $themeColor = '#3b82f6';
                $bgSoft = 'rgba(59, 130, 246, 0.08)';
                if ($tab === 'liabilities') {
                    $themeColor = '#f59e0b';
                    $bgSoft = 'rgba(245, 158, 11, 0.08)';
                } elseif ($tab === 'equity') {
                    $themeColor = '#8b5cf6';
                    $bgSoft = 'rgba(139, 92, 246, 0.08)';
                } elseif ($tab === 'income') {
                    $themeColor = '#10b981';
                    $bgSoft = 'rgba(16, 185, 129, 0.08)';
                } elseif ($tab === 'expenses') {
                    $themeColor = '#ef4444';
                    $bgSoft = 'rgba(239, 68, 68, 0.08)';
                }
            @endphp
            <!-- Uncategorized Database Accounts -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px; border: 1px solid #e2e8f0; background: #ffffff;">
                        <div class="card-header bg-white border-0 pt-3 pb-2 px-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0 fw-bold fs-16" style="color: #000000;">Additional {{ ucfirst($tab) }} Accounts</h5>
                                <p class="text-muted small mb-0">Other accounts registered in the database system.</p>
                            </div>
                        </div>
                        <div class="card-body p-3 pt-1">
                            <div class="row g-2">
                                @foreach($uncategorizedAccounts as $acc)
                                <div class="col-xl-3 col-md-4 col-sm-6">
                                    <div class="card h-100 shadow-sm hover-card {{ $tab }}-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease;">
                                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                                            <div>
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: {{ $bgSoft }}; color: {{ $themeColor }};">
                                                        <i class="las la-file-invoice-dollar fs-20"></i>
                                                    </div>
                                                    <span class="badge status-badge {{ $acc->is_active ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ $acc->is_active ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $acc->id }}">
                                                        {{ $acc->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </div>
                                                <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">{{ $acc->name }}</h6>
                                                <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Code: {{ $acc->code }}</p>
                                            </div>
                                            <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                                                <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                                                <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($acc->balance, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endif
    </div>

    <!-- Modals for Card Details -->
    
    <!-- Cash on Hand Modal -->
    <div class="modal fade" id="cashOnHandModal" tabindex="-1" aria-labelledby="cashOnHandModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="cashOnHandModalLabel"><i class="las la-coins text-primary me-2 fs-20"></i>Cash on Hand - Sales Invoices Ledger</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Invoice No.</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salesInvoices as $si)
                                <tr>
                                    <td><span class="fw-bold text-dark">#{{ $si->si_number }}</span></td>
                                    <td>{{ $si->customer_name ?? 'N/A' }}</td>
                                    <td>{{ $si->created_at->format('M d, Y') }}</td>
                                    <td><span class="badge bg-success text-white">{{ ucfirst($si->status) }}</span></td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($si->total_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No sales invoice transactions recorded in the database.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Petty Cash Modal -->
    <div class="modal fade" id="pettyCashModal" tabindex="-1" aria-labelledby="pettyCashModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="pettyCashModalLabel"><i class="las la-cash-register text-primary me-2 fs-20"></i>Petty Cash Vouchers Ledger</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>PCV No.</th>
                                    <th>Pay To</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pettyCashVouchers as $pcv)
                                <tr>
                                    <td><span class="fw-bold text-dark">#{{ $pcv->pcv_number }}</span></td>
                                    <td>{{ $pcv->pay_to }}</td>
                                    <td>{{ date('M d, Y', strtotime($pcv->date)) }}</td>
                                    <td>
                                        <span class="badge {{ $pcv->status === 'completed' ? 'bg-success text-white' : 'bg-warning text-dark' }}">
                                            {{ ucfirst($pcv->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($pcv->items_sum_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No petty cash vouchers recorded in the database.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplies Expense Modal -->
    <div class="modal fade" id="suppliesExpenseModal" tabindex="-1" aria-labelledby="suppliesExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="suppliesExpenseModalLabel"><i class="las la-archive text-danger me-2 fs-20"></i>Office Supplies Expenses Breakdown</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Item Name</th>
                                    <th>Item Price</th>
                                    <th>Stock Qty</th>
                                    <th class="text-end">Total Valuation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($officeSuppliesList as $supply)
                                <tr>
                                    <td><span class="fw-bold text-dark">{{ $supply->item_name }}</span></td>
                                    <td>₱{{ number_format($supply->item_price, 2) }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $supply->items_stock }} {{ $supply->unit ?? 'pcs' }}</span></td>
                                    <td class="text-end fw-bold text-danger">₱{{ number_format($supply->item_price * $supply->items_stock, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No office supply items recorded.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fixed Assets Expense Modal -->
    <div class="modal fade" id="fixedAssetsExpenseModal" tabindex="-1" aria-labelledby="fixedAssetsExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="fixedAssetsExpenseModalLabel"><i class="las la-tools text-danger me-2 fs-20"></i>Fixed Assets Expense Breakdown</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Asset Code</th>
                                    <th>Machine / Asset Name</th>
                                    <th>Category</th>
                                    <th>Purchase Date</th>
                                    <th class="text-end">Purchase Price</th>
                                    <th class="text-end">Current Book Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($fixedAssetsRecordsList ?? [] as $ast)
                                <tr>
                                    <td><span class="fw-bold font-monospace text-dark">{{ $ast->asset_code }}</span></td>
                                    <td>
                                        <span class="fw-bold text-dark d-block">{{ $ast->name }}</span>
                                        <span class="text-muted small">{{ $ast->location ?: 'Main Production Facility' }}</span>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $ast->category }}</span></td>
                                    <td class="small">{{ $ast->purchase_date ? $ast->purchase_date->format('M d, Y') : 'N/A' }}</td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($ast->purchase_price, 2) }}</td>
                                    <td class="text-end fw-bold text-danger">₱{{ number_format($ast->current_value, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No fixed asset records logged.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Operational Expenses Modal -->
    <div class="modal fade" id="operationalExpensesModal" tabindex="-1" aria-labelledby="operationalExpensesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="operationalExpensesModalLabel"><i class="las la-file-invoice-dollar text-danger me-2 fs-20"></i>Operational Expenses Breakdown</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Title / Item</th>
                                    <th>Department</th>
                                    <th>Expense Date</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expensesRecordsList as $exp)
                                <tr>
                                    <td><span class="fw-bold text-dark">{{ $exp->expense_title ?? ($exp->title ?? 'Operational Expense') }}</span></td>
                                    <td><span class="badge bg-light text-dark border">{{ $exp->department->name ?? 'General' }}</span></td>
                                    <td>{{ date('M d, Y', strtotime($exp->expense_date ?? $exp->created_at)) }}</td>
                                    <td class="text-end fw-bold text-danger">₱{{ number_format($exp->amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No operational expenses recorded.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Accounts Modal -->
    <div class="modal fade" id="bankAccountsModal" tabindex="-1" aria-labelledby="bankAccountsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="bankAccountsModalLabel"><i class="las la-university text-primary me-2 fs-20"></i>Bank Accounts Ledger</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Account Code</th>
                                    <th>Bank & Account Name</th>
                                    <th>Account Number</th>
                                    <th>Type</th>
                                    <th class="text-end">Current Balance</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($companyBankAccounts as $acct)
                                <tr>
                                    <td><span class="fw-bold text-dark">{{ $acct->account_code }}</span></td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $acct->bank_name }}</span>
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $acct->account_name }}</small>
                                    </td>
                                    <td><span class="text-muted small fw-medium">{{ $acct->account_number }}</span></td>
                                    <td><span class="badge bg-light text-dark border">{{ $acct->account_type }}</span></td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($acct->current_balance, 2) }}</td>
                                    <td><span class="badge bg-success text-white">Active</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No bank accounts registered in the database.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receivables Modal -->
    <div class="modal fade" id="receivablesModal" tabindex="-1" aria-labelledby="receivablesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="receivablesModalLabel"><i class="las la-file-invoice-dollar text-primary me-2 fs-20"></i>Accounts Receivable - Statements of Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Reference No.</th>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Created Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($statementOfAccounts as $soa)
                                <tr>
                                    <td><span class="fw-bold text-dark">#{{ $soa->soa_number }}</span></td>
                                    <td>{{ $soa->customer_name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $soa->type ?? 'Invoice' }}</span></td>
                                    <td>{{ $soa->created_at ? $soa->created_at->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        @php
                                            $st = strtolower($soa->status ?? '');
                                            $isPaid = ($st === 'paid');
                                        @endphp
                                        <span class="badge {{ $isPaid ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                            {{ $isPaid ? 'Paid' : 'Unpaid' }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($soa->total_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No accounts receivable transactions recorded in the database.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Modal -->
    <div class="modal fade" id="inventoryModal" tabindex="-1" aria-labelledby="inventoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="inventoryModalLabel"><i class="las la-boxes text-primary me-2 fs-20"></i>Inventory Valuation (Finished Goods)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Book Title</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-end">Cost</th>
                                    <th class="text-end">Total Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($books as $book)
                                <tr>
                                    <td><span class="fw-bold text-dark">{{ $book->name }}</span></td>
                                    <td class="text-center">{{ $book->stock }}</td>
                                    <td class="text-end">₱{{ number_format($book->cost, 2) }}</td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($book->stock * $book->cost, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No books in inventory found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Suppliers Modal -->
    <div class="modal fade" id="suppliersModal" tabindex="-1" aria-labelledby="suppliersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="suppliersModalLabel"><i class="las la-truck text-primary me-2 fs-20"></i>Suppliers Payable - Purchase Orders</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>PO Number</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseOrders as $po)
                                <tr>
                                    <td><span class="fw-bold text-dark">#{{ $po->po_number }}</span></td>
                                    <td>{{ $po->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-warning text-dark">{{ ucfirst($po->status) }}</span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($po->total_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No purchase orders recorded in the database.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Generic Empty Account Modal -->
    <div class="modal fade" id="genericCoaModal" tabindex="-1" aria-labelledby="genericCoaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="genericModalTitle">Account Ledger</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="genericModalBody">
                    <!-- Dynamic -->
                </div>
            </div>
        </div>
    </div>

    <!-- Account Group Accounts Detail Modal -->
    <div class="modal fade" id="accountGroupDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="las la-layer-group text-primary fs-20"></i> <span id="accountGroupDetailName">Account Group Ledger</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="accountGroupDetailBody">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add Account Group Modal -->
    <div class="modal fade" id="addAccountGroupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white border-bottom pb-3">
                    <h5 class="modal-title fw-bold" style="color: #000000; font-size: 0.95rem;">
                        <i class="las la-plus-circle me-1" style="color: #D9251C;"></i> Add New Account Group
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin-finance.accounting.account-groups.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-uppercase fw-bold" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">Account Group Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Bank" required style="border-color: #cbd5e1; border-radius: 6px; color: #000000; font-size: 0.85rem;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-uppercase fw-bold" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">Base Category <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required style="border-color: #cbd5e1; border-radius: 6px; color: #000000; font-size: 0.85rem;">
                                <option value="">Select Base Category</option>
                                <option value="Asset">Asset</option>
                                <option value="Liability">Liability</option>
                                <option value="Equity">Equity</option>
                                <option value="Income">Income</option>
                                <option value="Expense">Expense</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-uppercase fw-bold" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">Description (Optional)</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="e.g. Bank accounts, savings, and deposits" style="border-color: #cbd5e1; border-radius: 6px; color: #000000; font-size: 0.85rem;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top px-4 py-3">
                        <button type="button" class="btn btn-light border btn-sm px-3 fw-bold" data-bs-dismiss="modal" style="color: #475569;">Cancel</button>
                        <button type="submit" class="btn btn-sm text-white px-4 fw-bold" style="background-color: #D9251C; border: none; box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15);">Save Account Group</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Account Group Modal -->
    <div class="modal fade" id="editAccountGroupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white border-bottom pb-3">
                    <h5 class="modal-title fw-bold" style="color: #000000; font-size: 0.95rem;">
                        <i class="las la-pen me-1" style="color: #f59e0b;"></i> Edit Account Group
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="editAccountGroupForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-uppercase fw-bold" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">Account Group Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editGroupName" class="form-control" required style="border-color: #cbd5e1; border-radius: 6px; color: #000000; font-size: 0.85rem;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-uppercase fw-bold" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">Base Category <span class="text-danger">*</span></label>
                            <select name="type" id="editGroupType" class="form-select" required style="border-color: #cbd5e1; border-radius: 6px; color: #000000; font-size: 0.85rem;">
                                <option value="Asset">Asset</option>
                                <option value="Liability">Liability</option>
                                <option value="Equity">Equity</option>
                                <option value="Income">Income</option>
                                <option value="Expense">Expense</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-uppercase fw-bold" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">Description (Optional)</label>
                            <textarea name="description" id="editGroupDescription" class="form-control" rows="2" style="border-color: #cbd5e1; border-radius: 6px; color: #000000; font-size: 0.85rem;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top px-4 py-3">
                        <button type="button" class="btn btn-light border btn-sm px-3 fw-bold" data-bs-dismiss="modal" style="color: #475569;">Cancel</button>
                        <button type="submit" class="btn btn-sm text-white px-4 fw-bold" style="background-color: #D9251C; border: none; box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15);">Update Group</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Account Modal -->
    <div class="modal fade" id="addAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white border-bottom pb-3">
                    <h5 class="modal-title fw-bold" style="color: #000000; font-size: 0.95rem;">
                        <i class="las la-plus-circle me-1" style="color: #D9251C;"></i> Add New Chart of Account
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin-finance.accounting.chart-of-accounts.store') }}" method="POST" id="addAccountForm">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-uppercase fw-bold" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">Account Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" placeholder="e.g. 1090" required style="border-color: #cbd5e1; border-radius: 6px; color: #000000; font-size: 0.85rem;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-uppercase fw-bold" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">Account Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Special Operations Fund" required style="border-color: #cbd5e1; border-radius: 6px; color: #000000; font-size: 0.85rem;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-uppercase fw-bold" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">Category / Type <span class="text-danger">*</span></label>
                            <select name="type" id="addAccountType" class="form-select" required style="border-color: #cbd5e1; border-radius: 6px; color: #000000; font-size: 0.85rem;">
                                <option value="">Select Category</option>
                                <option value="Asset">Asset</option>
                                <option value="Liability">Liability</option>
                                <option value="Equity">Equity</option>
                                <option value="Income">Income</option>
                                <option value="Expense">Expense</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-uppercase fw-bold" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">Account Group (Card Grouping)</label>
                            <select name="account_group_id" id="addAccountGroupId" class="form-select" style="border-color: #cbd5e1; border-radius: 6px; color: #000000; font-size: 0.85rem;">
                                <option value="">None (Standalone Account Card)</option>
                                @foreach($allAccountGroups as $ag)
                                    <option value="{{ $ag->id }}" data-type="{{ $ag->type }}">{{ $ag->name }} ({{ $ag->type }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-uppercase fw-bold" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">Sub-Category / Classification</label>
                            <input type="text" name="category" class="form-control" placeholder="e.g. Current Asset, Operating Expense" style="border-color: #cbd5e1; border-radius: 6px; color: #000000; font-size: 0.85rem;">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="addIsActive" checked style="border-color: #cbd5e1;">
                            <label class="form-check-label small fw-bold" for="addIsActive" style="color: #475569;">
                                Active Account Status
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top px-4 py-3">
                        <button type="button" class="btn btn-light border btn-sm px-3 fw-bold" data-bs-dismiss="modal" style="color: #475569;">Cancel</button>
                        <button type="submit" class="btn btn-sm text-white px-4 fw-bold" style="background-color: #D9251C; border: none; box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15);">Save Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Account Modal -->
    <div class="modal fade" id="editAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white border-bottom pb-3">
                    <h5 class="modal-title fw-bold" style="color: #000000; font-size: 0.95rem;">
                        <i class="las la-pen me-1" style="color: #f59e0b;"></i> Edit Chart of Account
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="editAccountForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-uppercase fw-bold" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">Account Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="editCode" class="form-control" required style="border-color: #cbd5e1; border-radius: 6px; color: #000000; font-size: 0.85rem;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-uppercase fw-bold" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">Account Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editName" class="form-control" required style="border-color: #cbd5e1; border-radius: 6px; color: #000000; font-size: 0.85rem;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-uppercase fw-bold" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">Category / Type <span class="text-danger">*</span></label>
                            <select name="type" id="editType" class="form-select" required style="border-color: #cbd5e1; border-radius: 6px; color: #000000; font-size: 0.85rem;">
                                <option value="Asset">Asset</option>
                                <option value="Liability">Liability</option>
                                <option value="Equity">Equity</option>
                                <option value="Income">Income</option>
                                <option value="Expense">Expense</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-uppercase fw-bold" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">Account Group (Card Grouping)</label>
                            <select name="account_group_id" id="editAccountGroupId" class="form-select" style="border-color: #cbd5e1; border-radius: 6px; color: #000000; font-size: 0.85rem;">
                                <option value="">None (Standalone Account Card)</option>
                                @foreach($allAccountGroups as $ag)
                                    <option value="{{ $ag->id }}" data-type="{{ $ag->type }}">{{ $ag->name }} ({{ $ag->type }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-uppercase fw-bold" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">Sub-Category / Classification</label>
                            <input type="text" name="category" id="editCategory" class="form-control" style="border-color: #cbd5e1; border-radius: 6px; color: #000000; font-size: 0.85rem;">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editIsActive" style="border-color: #cbd5e1;">
                            <label class="form-check-label small fw-bold" for="editIsActive" style="color: #475569;">
                                Active Account Status
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top px-4 py-3">
                        <button type="button" class="btn btn-light border btn-sm px-3 fw-bold" data-bs-dismiss="modal" style="color: #475569;">Cancel</button>
                        <button type="submit" class="btn btn-sm text-white px-4 fw-bold" style="background-color: #D9251C; border: none; box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15);">Update Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white border-bottom pb-3">
                    <h5 class="modal-title fw-bold" style="color: #000000; font-size: 0.95rem;">
                        <i class="las la-trash me-1 text-danger"></i> Delete Chart of Account
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="deleteAccountForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body p-4 text-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 54px; height: 54px; background-color: rgba(217, 37, 28, 0.1); color: #D9251C;">
                            <i class="las la-exclamation-triangle fs-30"></i>
                        </div>
                        <h6 class="fw-bold mb-2" style="color: #000000;" id="deleteAccountTargetName">Delete Account</h6>
                        <p class="text-muted small mb-0">Are you sure you want to delete this account? It will be soft-deleted and can be restored if needed.</p>
                    </div>
                    <div class="modal-footer bg-light border-top px-4 py-3 justify-content-center">
                        <button type="button" class="btn btn-light border btn-sm px-3 fw-bold" data-bs-dismiss="modal" style="color: #475569;">Cancel</button>
                        <button type="submit" class="btn btn-sm text-white px-4 fw-bold" style="background-color: #D9251C; border: none; box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15);">Yes, Delete Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Universal Dynamic Account Ledger Modal per SKILL.md Section 6 -->
    <div class="modal fade" id="universalAccountLedgerModal" tabindex="-1" aria-labelledby="universalAccountLedgerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 8px; overflow: hidden;">
                <div class="modal-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="las la-book fs-20 text-dark"></i>
                        <h5 class="modal-title fw-bold text-dark mb-0 fs-16" id="ledgerModalTitle">Account Ledger Breakdown</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    {{-- Header info and Search bar per SKILL.md Section 5 --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold" style="letter-spacing: 0.5px; font-size: 0.7rem;">ACCOUNT CODE & NAME:</span>
                            <div class="fw-bold fs-16 text-dark" id="ledgerAccountCodeName">—</div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group input-group-sm" style="width: 240px;">
                                <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; height: 36px; display: flex; align-items: center; justify-content: center; padding: 0 10px; border-top-left-radius: 4px; border-bottom-left-radius: 4px;">
                                    <i class="las la-search text-muted fs-14"></i>
                                </span>
                                <input type="text" id="modalLedgerSearchInput" class="form-control border-start-0" placeholder="Search ref, memo, date..." style="height: 36px; border-color: #cbd5e1; border-top-right-radius: 4px; border-bottom-right-radius: 4px; font-size: 0.82rem; padding-left: 0; outline: none; box-shadow: none;">
                            </div>
                            <button type="button" id="modalLedgerSearchBtn" class="btn btn-danger btn-sm text-white fw-bold d-inline-flex align-items-center justify-content-center" style="height: 36px; padding: 0 14px; background-color: #D9251C; border-color: #D9251C; border-radius: 4px; font-size: 0.82rem;">
                                Search
                            </button>
                            <button type="button" id="modalLedgerClearBtn" class="btn btn-light border btn-sm d-inline-flex align-items-center justify-content-center" style="height: 36px; padding: 0 12px; border-radius: 4px; font-size: 0.82rem; color: #475569;">
                                Clear
                            </button>
                        </div>
                    </div>

                    <div id="ledgerLoadingSpinner" class="text-center py-5">
                        <div class="spinner-border text-danger" role="status"></div>
                        <div class="text-muted small mt-2">Loading transactions from General Ledger...</div>
                    </div>

                    <div id="ledgerTableWrapper" style="display: none;">
                        <div class="table-responsive">
                            <table class="table claretian-table align-middle">
                                <thead>
                                    <tr>
                                        <th>DATE</th>
                                        <th>REFERENCE / JV NO.</th>
                                        <th>MEMO / PARTICULARS</th>
                                        <th class="text-end">DEBIT (₱)</th>
                                        <th class="text-end">CREDIT (₱)</th>
                                    </tr>
                                </thead>
                                <tbody id="ledgerTableBody">
                                    {{-- JS Populated --}}
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination Controls per SKILL.md Section 4 --}}
                        <div id="modalPaginationContainer" class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2 pt-3 border-top">
                            <span class="text-muted small" id="modalPaginationInfo" style="font-size: 0.8rem;">Showing 0 entries</span>
                            <div id="modalPaginationButtons" class="d-flex gap-1"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top py-3 pe-4">
                    <button type="button" class="btn btn-light border fw-semibold" data-bs-dismiss="modal" style="color: #475569; border-color: #cbd5e1; font-size: 0.85rem; padding: 8px 20px; border-radius: 6px;">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let modalRawTransactions = [];
        let modalCurrentPage = 1;
        const modalPageSize = 10;

        function renderModalTable() {
            const searchQuery = (document.getElementById('modalLedgerSearchInput').value || '').toLowerCase().trim();
            const filtered = modalRawTransactions.filter(t => {
                if (!searchQuery) return true;
                return (t.date || '').toLowerCase().includes(searchQuery) ||
                       (t.ref_no || '').toLowerCase().includes(searchQuery) ||
                       (t.memo || '').toLowerCase().includes(searchQuery);
            });

            const totalEntries = filtered.length;
            const totalPages = Math.ceil(totalEntries / modalPageSize) || 1;
            if (modalCurrentPage > totalPages) modalCurrentPage = totalPages;
            if (modalCurrentPage < 1) modalCurrentPage = 1;

            const start = (modalCurrentPage - 1) * modalPageSize;
            const pageItems = filtered.slice(start, start + modalPageSize);

            const tbody = document.getElementById('ledgerTableBody');
            tbody.innerHTML = '';

            if (pageItems.length > 0) {
                pageItems.forEach(t => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${t.date}</td>
                        <td><strong style="color: #0f172a;">${t.ref_no}</strong></td>
                        <td>${t.memo}</td>
                        <td class="text-end fw-bold" style="color: #0f5132;">${t.debit > 0 ? '₱' + t.debit.toLocaleString('en-US', {minimumFractionDigits: 2}) : '—'}</td>
                        <td class="text-end fw-bold" style="color: #842029;">${t.credit > 0 ? '₱' + t.credit.toLocaleString('en-US', {minimumFractionDigits: 2}) : '—'}</td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="las la-folder-open fs-32 d-block mb-2 text-slate-400"></i>
                            No ledger transaction records found.
                        </td>
                    </tr>
                `;
            }

            // Update Pagination Info
            const endIdx = Math.min(start + modalPageSize, totalEntries);
            document.getElementById('modalPaginationInfo').innerText = totalEntries > 0 
                ? `Showing ${start + 1} to ${endIdx} of ${totalEntries} entries` 
                : 'Showing 0 entries';

            // Render Pagination Buttons
            const btnContainer = document.getElementById('modalPaginationButtons');
            btnContainer.innerHTML = '';

            if (totalPages > 1) {
                // Prev button
                const prevBtn = document.createElement('button');
                prevBtn.className = 'btn btn-xs btn-light border me-1';
                prevBtn.innerText = '«';
                prevBtn.disabled = modalCurrentPage === 1;
                prevBtn.onclick = () => { modalCurrentPage--; renderModalTable(); };
                btnContainer.appendChild(prevBtn);

                for (let i = 1; i <= totalPages; i++) {
                    if (i === 1 || i === totalPages || (i >= modalCurrentPage - 1 && i <= modalCurrentPage + 1)) {
                        const pageBtn = document.createElement('button');
                        pageBtn.className = `btn btn-xs ${i === modalCurrentPage ? 'btn-danger text-white' : 'btn-light border'} me-1`;
                        if (i === modalCurrentPage) pageBtn.style.backgroundColor = '#D9251C';
                        pageBtn.innerText = i;
                        pageBtn.onclick = () => { modalCurrentPage = i; renderModalTable(); };
                        btnContainer.appendChild(pageBtn);
                    }
                }

                // Next button
                const nextBtn = document.createElement('button');
                nextBtn.className = 'btn btn-xs btn-light border';
                nextBtn.innerText = '»';
                nextBtn.disabled = modalCurrentPage === totalPages;
                nextBtn.onclick = () => { modalCurrentPage++; renderModalTable(); };
                btnContainer.appendChild(nextBtn);
            }
        }

        function openAccountLedgerModal(id, code, name) {
            const modalEl = document.getElementById('universalAccountLedgerModal');
            const modal = new bootstrap.Modal(modalEl);
            
            document.getElementById('ledgerModalTitle').innerText = name + ' (' + code + ') Ledger Breakdown';
            document.getElementById('ledgerAccountCodeName').innerText = code + ' - ' + name;
            document.getElementById('modalLedgerSearchInput').value = '';
            document.getElementById('ledgerLoadingSpinner').style.display = 'block';
            document.getElementById('ledgerTableWrapper').style.display = 'none';
            
            modalRawTransactions = [];
            modalCurrentPage = 1;
            modal.show();

            const ledgerUrl = "{{ route('admin-finance.accounting.chart-of-accounts.ledger', ':id') }}".replace(':id', id);
            fetch(ledgerUrl)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('ledgerLoadingSpinner').style.display = 'none';
                    document.getElementById('ledgerTableWrapper').style.display = 'block';

                    if (data.success) {
                        modalRawTransactions = data.transactions || [];
                        modalCurrentPage = 1;
                        renderModalTable();
                    }
                })
                .catch(err => {
                    document.getElementById('ledgerLoadingSpinner').style.display = 'none';
                    alert('Failed to load account ledger details.');
                });
        }

        document.getElementById('modalLedgerSearchBtn').addEventListener('click', function() {
            modalCurrentPage = 1;
            renderModalTable();
        });

        document.getElementById('modalLedgerSearchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                modalCurrentPage = 1;
                renderModalTable();
            }
        });

        document.getElementById('modalLedgerClearBtn').addEventListener('click', function() {
            document.getElementById('modalLedgerSearchInput').value = '';
            modalCurrentPage = 1;
            renderModalTable();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showGenericModal(title, description, balance = 0) {
            document.getElementById('genericModalTitle').innerText = title + ' Ledger';
            const numBalance = parseFloat(balance || 0);
            const formattedBalance = numBalance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const badgeClass = numBalance > 0 ? 'bg-soft-success text-success' : 'bg-light text-muted';
            const infoText = numBalance > 0 ? `Current Ledger Balance: ₱${formattedBalance}` : `Balance: ₱0.00 (No logged transactions)`;
            
            document.getElementById('genericModalBody').innerHTML = `
                <div class="text-center py-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-light mx-auto mb-3" style="width: 60px; height: 60px; color: #ff0000;">
                        <i class="las la-folder-open fs-32"></i>
                    </div>
                    <h6 class="fw-bold text-dark">${title}</h6>
                    <p class="text-muted small px-3 mb-4">${description}</p>
                    <div class="alert ${badgeClass} border-0 small d-inline-block px-3 py-2 rounded-pill fw-bold">
                        <i class="las la-info-circle me-1"></i> ${infoText}
                    </div>
                </div>
            `;
            const modal = new bootstrap.Modal(document.getElementById('genericCoaModal'));
            modal.show();
        }

        // --- CLIENT-SIDE TABLE PAGINATION FOR CARD LEDGER MODALS ---
        function initTablePagination(tableElement, itemsPerPage = 10) {
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
            ul.className = 'pagination pagination-xs mb-0';
            
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

        document.addEventListener('DOMContentLoaded', function() {
            const modalTables = document.querySelectorAll('.modal .modal-body table');
            modalTables.forEach(table => {
                if (table.closest('#genericCoaModal')) return;
                initTablePagination(table, 10);
            });

            // Edit Account Modal Populator
            $(document).on('click', '.btn-edit-account', function() {
                const id = $(this).data('id');
                const code = $(this).data('code');
                const name = $(this).data('name');
                const type = $(this).data('type');
                const category = $(this).data('category');
                const accountGroupId = $(this).data('account-group-id');
                const active = $(this).data('active');

                $('#editCode').val(code);
                $('#editName').val(name);
                $('#editType').val(type);
                $('#editCategory').val(category);
                $('#editIsActive').prop('checked', active == 1);

                filterAccountGroupsByType('#editType', '#editAccountGroupId', accountGroupId);

                const updateUrl = "{{ route('admin-finance.accounting.chart-of-accounts.update', ':id') }}".replace(':id', id);
                $('#editAccountForm').attr('action', updateUrl);

                $('#editAccountModal').modal('show');
            });

            // Dynamic Account Group dropdown filtering by Base Category Type
            function filterAccountGroupsByType(typeSelect, groupSelect, selectedVal) {
                const selectedType = $(typeSelect).val();
                $(groupSelect).find('option').each(function() {
                    const optType = $(this).data('type');
                    if (!optType || optType === selectedType) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
                $(groupSelect).val(selectedVal || '');
            }

            $('#addAccountType').on('change', function() {
                filterAccountGroupsByType('#addAccountType', '#addAccountGroupId', '');
            });

            $('#editType').on('change', function() {
                filterAccountGroupsByType('#editType', '#editAccountGroupId', $('#editAccountGroupId').val());
            });

            // Account Group Detail Modal Handler
            window.openAccountGroupDetailModal = function(groupId) {
                const modal = new bootstrap.Modal(document.getElementById('accountGroupDetailModal'));
                $('#accountGroupDetailName').text('Loading Account Group Details...');
                $('#accountGroupDetailBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Loading assigned accounts...</p></div>');
                modal.show();

                $.ajax({
                    url: "{{ url('admin-finance/account-groups') }}/" + groupId + "/accounts",
                    method: 'GET',
                    success: function(res) {
                        if (res.success) {
                            $('#accountGroupDetailName').text(res.group.name + ' Group Accounts (' + res.group.type + ')');
                            let html = `
                                <div class="d-flex align-items-center justify-content-between mb-3 p-3 bg-light rounded border">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark fs-16">${res.group.name}</h6>
                                        <small class="text-muted">${res.group.description || 'Grouped Chart of Accounts Card'}</small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Combined Group Balance</small>
                                        <span class="fw-bold fs-18 text-primary">₱${res.group.total_balance}</span>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light text-muted small text-uppercase">
                                            <tr>
                                                <th style="width: 120px;">Code</th>
                                                <th>Account Name</th>
                                                <th>Sub-Category</th>
                                                <th style="width: 120px; text-align: center;">Status</th>
                                                <th style="width: 160px;" class="text-end">Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;
                            if (!res.accounts || res.accounts.length === 0) {
                                html += `<tr><td colspan="5" class="text-center py-4 text-muted">No Chart of Accounts assigned to this group yet.</td></tr>`;
                            } else {
                                res.accounts.forEach(acc => {
                                    html += `
                                        <tr style="cursor: pointer;" onclick="bootstrap.Modal.getInstance(document.getElementById('accountGroupDetailModal')).hide(); openAccountLedgerModal(${acc.id}, '${acc.code}', '${acc.name.replace(/'/g, "\\'")}')">
                                            <td><span class="fw-bold text-dark">${acc.code}</span></td>
                                            <td><span class="fw-bold text-primary">${acc.name}</span></td>
                                            <td><span class="badge bg-light text-dark border">${acc.category || 'General'}</span></td>
                                            <td style="text-align: center;"><span class="badge ${acc.is_active ? 'bg-success text-white' : 'bg-secondary text-white'}">${acc.is_active ? 'Active' : 'Inactive'}</span></td>
                                            <td class="text-end fw-bold text-dark">₱${acc.balance}</td>
                                        </tr>
                                    `;
                                });
                            }
                            html += `</tbody></table></div>`;
                            $('#accountGroupDetailBody').html(html);
                        }
                    },
                    error: function() {
                        $('#accountGroupDetailBody').html('<div class="alert alert-danger mb-0">Failed to load account group details.</div>');
                    }
                });
            };

            // Edit Account Group Modal Trigger
            window.editAccountGroup = function(id, name, type, description) {
                $('#editGroupName').val(name);
                $('#editGroupType').val(type);
                $('#editGroupDescription').val(description);
                $('#editAccountGroupForm').attr('action', "{{ url('admin-finance/account-groups') }}/" + id);
                const modal = new bootstrap.Modal(document.getElementById('editAccountGroupModal'));
                modal.show();
            };

            // Delete Account Group Confirmation
            window.deleteAccountGroup = function(id, name) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Delete Account Group?',
                        text: 'Are you sure you want to delete "' + name + '"? Linked accounts will not be deleted, they will simply become standalone cards.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#D9251C',
                        cancelButtonColor: '#475569',
                        confirmButtonText: 'Yes, delete group'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('admin-finance/account-groups') }}/" + id,
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    _method: 'DELETE'
                                },
                                success: function(res) {
                                    Swal.fire('Deleted!', res.message || 'Account Group deleted successfully.', 'success').then(() => {
                                        window.location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    Swal.fire('Error', xhr.responseJSON ? xhr.responseJSON.error : 'Failed to delete Account Group.', 'error');
                                }
                            });
                        }
                    });
                } else if (confirm('Are you sure you want to delete account group "' + name + '"?')) {
                    $.ajax({
                        url: "{{ url('admin-finance/account-groups') }}/" + id,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function() {
                            window.location.reload();
                        }
                    });
                }
            };

            // Delete Account confirmation modal trigger
            $(document).on('click', '.btn-delete-account', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const name = $(this).data('name');
                const deleteUrl = "{{ route('admin-finance.accounting.chart-of-accounts.destroy', ':id') }}".replace(':id', id);

                $('#deleteAccountTargetName').text(`Delete "${name}"?`);
                $('#deleteAccountForm').attr('action', deleteUrl);

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: `Soft delete account "${name}"? It will be safely archived and can be restored if needed.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#D9251C',
                        cancelButtonColor: '#475569',
                        confirmButtonText: 'Yes, delete account!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: deleteUrl,
                                type: 'POST',
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    _method: 'DELETE'
                                },
                                success: function(res) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: res.message || 'Account soft-deleted successfully.',
                                        icon: 'success',
                                        confirmButtonColor: '#D9251C'
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    const err = xhr.responseJSON ? xhr.responseJSON.error : 'Failed to delete account.';
                                    Swal.fire({
                                        title: 'Cannot Delete Account',
                                        text: err,
                                        icon: 'error',
                                        confirmButtonColor: '#D9251C'
                                    });
                                }
                            });
                        }
                    });
                } else {
                    const deleteModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
                    deleteModal.show();
                }
            });

            // AJAX status toggle handler
            $(document).on('click', '.status-badge', function(e) {
                e.stopPropagation();
                const $badge = $(this);
                const type = $badge.data('type');
                const id = $badge.data('id');

                if (!id) return;

                $badge.css('opacity', '0.5');

                $.ajax({
                    url: "{{ route('admin-finance.accounting.chart-of-accounts.toggle') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        type: type,
                        id: id
                    },
                    success: function(response) {
                        $badge.css('opacity', '1');
                        if (response.success) {
                            const isActive = response.is_active;
                            if (isActive) {
                                $badge.removeClass('bg-light text-secondary')
                                      .addClass('bg-soft-success text-success')
                                      .css({'background-color': 'rgba(16, 185, 129, 0.1)', 'color': '#10b981'})
                                      .text('Active');
                            } else {
                                $badge.removeClass('bg-soft-success text-success')
                                      .addClass('bg-light text-secondary')
                                      .css({'background-color': '', 'color': ''})
                                      .text('Inactive');
                            }
                            if (typeof showNotification === 'function') {
                                showNotification(response.message, 'success');
                            }
                        }
                    },
                    error: function(xhr) {
                        $badge.css('opacity', '1');
                        const err = xhr.responseJSON ? xhr.responseJSON.error : 'Failed to update status.';
                        if (typeof showNotification === 'function') {
                            showNotification(err, 'error');
                        }
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
