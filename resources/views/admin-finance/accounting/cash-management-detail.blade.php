<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .content-body .container-fluid {
            padding-bottom: 80px !important;
        }

        .csh-card {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
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


        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin-finance.cash-management.index') }}" class="btn btn-sm btn-outline-danger px-3 mb-2" style="border-radius: 4px; border-color: #cbd5e1; color: #475569;">
                        <i class="las la-arrow-left me-1"></i> Back to Cash Management
                    </a>
                    <h4 class="fs-24 fw-bold text-dark mb-0">{{ $account->bank_name }} Statement</h4>
                    <p class="text-muted small mb-0">Code: <span class="font-monospace fw-bold text-dark">{{ $account->account_code }}</span> | Account No: <span class="font-monospace fw-bold text-dark">{{ $account->account_number }}</span> | Type: <span class="badge bg-light text-dark border">{{ $account->account_type }}</span></p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="color: #D9251C; border-color: #D9251C; height: 40px;" onclick="window.print()">
                        <i class="las la-print fs-18"></i> Print Statement
                    </button>
                </div>
            </div>
        </div>

        <!-- Row 1: Bank Account Summary (Full Width) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="csh-card">
                    <div class="row">
                        <!-- Left column: Account Information -->
                        <div class="col-md-6" style="border-right: 1px solid #f1f5f9;">
                            <h6 class="fw-bold text-uppercase text-muted small mb-3" style="letter-spacing: 0.5px; font-size: 0.72rem; color: #475569 !important;">Bank Account Profile</h6>
                            
                            <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                <span class="text-muted small">Account Code</span>
                                <span class="fw-bold text-dark small font-monospace">{{ $account->account_code }}</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                <span class="text-muted small">Account Number</span>
                                <span class="fw-bold text-dark small font-monospace">{{ $account->account_number }}</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                <span class="text-muted small">Account Type</span>
                                <span class="badge bg-light text-dark border small">{{ $account->account_type }}</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                <span class="text-muted small">Currency / Status</span>
                                <span class="fw-bold text-dark small">{{ $account->currency }} ({{ $account->status }})</span>
                            </div>

                            @if($account->notes)
                            <div class="mt-3 p-3 bg-light rounded border" style="border-radius: 8px;">
                                <span class="text-muted small fw-bold d-block text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Account Notes / Remarks</span>
                                <p class="small text-dark mb-0 mt-1">{{ $account->notes }}</p>
                            </div>
                            @endif
                        </div>

                        <!-- Right column: Financial Reconciliation Worksheet -->
                        <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                            <h6 class="fw-bold text-uppercase text-muted small mb-3" style="letter-spacing: 0.5px; font-size: 0.72rem; color: #475569 !important;">Financial Reconciliation Summary</h6>
                            
                            <div class="mb-3 text-center p-3 rounded" style="background-color: rgba(217, 37, 28, 0.05); border: 1px solid rgba(217, 37, 28, 0.12); border-radius: 8px;">
                                <span class="small text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Current Live Balance</span>
                                <h3 class="fw-bold mb-0 text-danger" style="color: #D9251C !important; font-size: 1.8rem; letter-spacing: -0.5px;">₱{{ number_format($account->current_balance, 2) }}</h3>
                            </div>

                            @php
                                $clearedInflows = $account->transactions->where('category', 'Inflow')->where('status', 'Cleared')->sum('amount');
                                $clearedOutflows = $account->transactions->where('category', 'Outflow')->where('status', 'Cleared')->sum('amount');
                                $pendingChecks = $account->transactions->where('transaction_type', 'Check Issuance')->where('status', 'Pending')->sum('amount');
                            @endphp

                            <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                <span class="text-muted small">Opening Balance</span>
                                <span class="fw-bold text-dark small">₱{{ number_format($account->opening_balance, 2) }}</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                <span class="text-muted small">Cleared Cash Inflows</span>
                                <span class="fw-bold text-success small">₱{{ number_format($clearedInflows, 2) }}</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                <span class="text-muted small">Cleared Outflows & Checks</span>
                                <span class="fw-bold text-dark small">₱{{ number_format($clearedOutflows, 2) }}</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                <span class="text-muted small">Outstanding Checks (Pending Clearing)</span>
                                <span class="fw-bold text-danger small">₱{{ number_format($pendingChecks, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Bank Account Statement Ledger (Full Width) -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="csh-card">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Bank Account Statement Ledger</h5>
                            <p class="text-muted small mb-0">Itemized deposits, check issuances, transfers, and reconciliations</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group input-group-sm" style="width: 320px;">
                                <span class="input-group-text bg-white" style="border-color: #cbd5e1; border-right: 0; padding: 0 10px; height: 38px; display: flex; align-items: center;">
                                    <i class="las la-search text-muted fs-16"></i>
                                </span>
                                <input type="text" id="table-search-input" class="form-control border-start-0" placeholder="Search Ref, Payee, Type..." style="border-color: #cbd5e1; outline: none; box-shadow: none; height: 38px; font-size: 0.82rem;">
                                <button type="button" id="btn-search-action" class="btn text-white px-3" style="background-color: #D9251C; border-color: #D9251C; height: 38px; font-size: 0.82rem; border-top-right-radius: 4px; border-bottom-right-radius: 4px;">Search</button>
                            </div>
                        </div>
                    </div>

                    <div id="cashTableContainer" style="border: none;">
                        <div class="table-responsive">
                            <table class="table table-modern align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Ref / Check #</th>
                                        <th>Payee / Payer</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $tx)
                                    <tr>
                                        <td><span class="fw-bold text-dark small">{{ $tx->transaction_date ? $tx->transaction_date->format('M d, Y') : 'N/A' }}</span></td>
                                        <td><span class="badge bg-light text-dark border">{{ $tx->transaction_type }}</span></td>
                                        <td><span class="font-monospace text-dark fw-bold small">{{ $tx->reference_no ?: 'N/A' }}</span></td>
                                        <td><span class="text-dark small">{{ $tx->payee_or_payer ?: 'N/A' }}</span></td>
                                        <td class="text-end fw-bold {{ $tx->category === 'Inflow' ? 'text-success' : 'text-dark' }}">
                                            ₱{{ number_format($tx->amount, 2) }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success-subtle text-success">{{ $tx->status }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No transactions logged for this bank account yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="paginationContainer" class="mt-4 d-flex justify-content-end pe-4">
                        {{ $transactions->onEachSide(0)->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function($) {
            'use strict';

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
