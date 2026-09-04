<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .coa-header-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.75rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }

        /* Premium Dashboard KPI Cards Styling matching COA */
        .hover-card {
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .hover-card:hover {
            transform: translateY(-4px) !important;
            background-color: #ffffff !important;
            border-color: #D9251C !important;
            box-shadow: 0 12px 24px -5px rgba(217, 37, 28, 0.12), 0 4px 12px -2px rgba(217, 37, 28, 0.08) !important;
        }

        .bg-soft-primary {
            background-color: rgba(217, 37, 28, 0.05) !important;
        }

        .text-primary {
            color: #D9251C !important;
        }

        /* Borderless flat sub-navigation tabs */
        .sales-nav-tabs {
            border-bottom: 1px solid #e2e8f0 !important;
            padding-left: 10px !important;
        }
        
        .sales-nav-tabs .nav-link {
            border: none !important;
            color: #475569 !important;
            font-weight: 600 !important;
            padding: 12px 24px !important;
            font-size: 0.88rem !important;
            border-bottom: 3px solid transparent !important;
            border-radius: 0 !important;
            transition: all 0.15s ease-in-out !important;
            background: transparent !important;
        }
        
        .sales-nav-tabs .nav-link.active {
            color: #D9251C !important;
            border-bottom: 3px solid #D9251C !important;
        }
        
        .sales-nav-tabs .nav-link:hover:not(.active) {
            color: #0f172a !important;
            border-bottom: 3px solid #cbd5e1 !important;
        }

        /* Modal tabs borderless styling */
        .modal-tabs {
            border-bottom: 1px solid #e2e8f0 !important;
        }
        .modal-tabs .nav-link {
            border: none !important;
            color: #475569 !important;
            font-weight: 600 !important;
            padding: 10px 16px !important;
            font-size: 0.85rem !important;
            border-bottom: 3px solid transparent !important;
            border-radius: 0 !important;
            transition: all 0.15s ease-in-out !important;
            background: transparent !important;
        }
        .modal-tabs .nav-link.active {
            color: #D9251C !important;
            border-bottom: 3px solid #D9251C !important;
        }
        .modal-tabs .nav-link:hover:not(.active) {
            color: #0f172a !important;
            border-bottom: 3px solid #cbd5e1 !important;
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
        
        /* Modern table overrides inside modals */
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
            padding: 10px 14px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
        }
        .table-modern tbody td {
            padding: 10px 14px !important;
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

        /* Paginator Link Styles inside Modal */
        .pagination .page-item.active .page-link {
            background-color: #D9251C !important;
            border-color: #D9251C !important;
            color: #ffffff !important;
        }
        .pagination .page-link {
            color: #475569 !important;
            border-color: #cbd5e1 !important;
            background-color: #ffffff !important;
        }
        .pagination .page-link:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }
    </style>
    @endpush

    <div class="container-fluid">
        <!-- Top Title & Overview Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="coa-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fs-24 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Sales & Receipts Ledger - 
                            @if($tab === 'bookstore') Bookstore 
                            @elseif($tab === 'areasales') Area Sales 
                            @elseif($tab === 'ecom') E-Commerce 
                            @elseif($tab === 'wholesale') Wholesale 
                            @elseif($tab === 'mibf') MIBF POS
                            @elseif($tab === 'complimentary') Complimentary Receipt 
                            @else {{ strtoupper($tab) }} @endif
                        </h4>
                        <p class="text-muted small mb-0">CCFI Sales Management ledger containing {{ $tab === 'mibf' ? 'MIBF POS' : ucfirst($tab) }} accounts, receipts tracking, and performance logs.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs Bar -->
        @if($tab !== 'complimentary')
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div class="card-body p-0">
                        <ul class="nav sales-nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ $tab === 'bookstore' ? 'active' : '' }}" href="{{ route('admin-finance.accounting.sales-management', ['tab' => 'bookstore']) }}">
                                    <i class="las la-store me-1"></i> Bookstore
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $tab === 'areasales' ? 'active' : '' }}" href="{{ route('admin-finance.accounting.sales-management', ['tab' => 'areasales']) }}">
                                    <i class="las la-map-marked-alt me-1"></i> Area Sales
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $tab === 'ecom' ? 'active' : '' }}" href="{{ route('admin-finance.accounting.sales-management', ['tab' => 'ecom']) }}">
                                    <i class="las la-shopping-cart me-1"></i> E-Commerce
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $tab === 'wholesale' ? 'active' : '' }}" href="{{ route('admin-finance.accounting.sales-management', ['tab' => 'wholesale']) }}">
                                    <i class="las la-boxes me-1"></i> Wholesale
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $tab === 'mibf' ? 'active' : '' }}" href="{{ route('admin-finance.accounting.sales-management', ['tab' => 'mibf']) }}">
                                    <i class="las la-tags me-1"></i> MIBF
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Render the selected component only -->
        <div class="row">
            <div class="col-12">
                @if($tab === 'bookstore')
                    @include('admin-finance.accounting.sales-management.bookstore')
                @elseif($tab === 'areasales')
                    @include('admin-finance.accounting.sales-management.areasales')
                @elseif($tab === 'ecom')
                    @include('admin-finance.accounting.sales-management.ecom')
                @elseif($tab === 'wholesale')
                    @include('admin-finance.accounting.sales-management.wholesale')
                @elseif($tab === 'mibf')
                    @include('admin-finance.accounting.sales-management.mibf')
                @elseif($tab === 'complimentary')
                    @include('admin-finance.accounting.sales-management.complimentary')
                @endif
            </div>
        </div>
    </div>

    <!-- Generic Ledger Detail Modal -->
    <div class="modal fade" id="salesLedgerModal" tabindex="-1" aria-labelledby="salesLedgerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                    <h5 class="modal-title fw-bold text-dark" id="salesLedgerModalLabel">Account Ledger</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="salesLedgerModalBody">
                    <!-- Loaded dynamically via Javascript -->
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // --- CLIENT-SIDE TABLE FILTERING & PAGINATION FOR CARD LEDGER MODALS ---
        function applyModalTableFilters(tableElement) {
            const tbody = tableElement.querySelector('tbody');
            if (!tbody) return;

            const customerInput = document.getElementById('modalFilterCustomer');
            const dateFromInput = document.getElementById('modalFilterDateFrom');
            const dateToInput = document.getElementById('modalFilterDateTo');

            const customerVal = customerInput ? customerInput.value.trim().toLowerCase() : '';
            const dateFromVal = dateFromInput ? dateFromInput.value : '';
            const dateToVal = dateToInput ? dateToInput.value : '';

            const rows = Array.from(tbody.querySelectorAll('tr:not(.empty-filter-row)'));
            let matchedRows = [];

            rows.forEach(row => {
                if (row.querySelector('td[colspan]')) {
                    row.style.display = 'none';
                    return;
                }

                let rowCustomer = row.getAttribute('data-customer');
                let rowDate = row.getAttribute('data-date');
                let rowSi = row.getAttribute('data-si') || '';
                let rowSo = row.getAttribute('data-so') || '';
                let rowPrepared = row.getAttribute('data-prepared') || '';

                if (!rowCustomer || !rowDate) {
                    const text = row.innerText.toLowerCase();
                    rowCustomer = rowCustomer || text;
                    rowDate = rowDate || '';
                }

                let matchCustomer = true;
                if (customerVal) {
                    const combinedText = (rowCustomer + ' ' + rowSi + ' ' + rowSo + ' ' + rowPrepared + ' ' + row.innerText).toLowerCase();
                    matchCustomer = combinedText.includes(customerVal);
                }

                let matchDate = true;
                if (dateFromVal && rowDate) {
                    matchDate = matchDate && (rowDate >= dateFromVal);
                }
                if (dateToVal && rowDate) {
                    matchDate = matchDate && (rowDate <= dateToVal);
                }

                if (matchCustomer && matchDate) {
                    matchedRows.push(row);
                } else {
                    row.style.display = 'none';
                }
            });

            // Handle empty state
            let emptyRow = tbody.querySelector('.empty-filter-row');
            if (matchedRows.length === 0) {
                if (!emptyRow) {
                    emptyRow = document.createElement('tr');
                    emptyRow.className = 'empty-filter-row';
                    const colCount = tableElement.querySelectorAll('thead th').length || 6;
                    emptyRow.innerHTML = `<td colspan="${colCount}" class="text-center py-4 text-muted"><i class="las la-filter me-1 text-danger"></i>No transactions match the selected filters.</td>`;
                    tbody.appendChild(emptyRow);
                }
                emptyRow.style.display = '';
            } else if (emptyRow) {
                emptyRow.style.display = 'none';
            }

            // Calculate and update TOTAL row in tfoot
            updateModalTableTotal(tableElement, matchedRows);

            // Paginate matched rows
            initFilteredTablePagination(tableElement, matchedRows, 6);
        }

        function updateModalTableTotal(tableElement, matchedRows) {
            const ths = Array.from(tableElement.querySelectorAll('thead th'));
            if (!ths.length) return;

            let amountColIdx = -1;
            ths.forEach((th, idx) => {
                const text = th.innerText.toLowerCase().trim();
                if (text.includes('amount') || text.includes('balance') || text.includes('total') || text.includes('valuation') || text.includes('price')) {
                    amountColIdx = idx;
                }
            });

            if (amountColIdx === -1) {
                amountColIdx = ths.length - 1;
            }

            let totalSum = 0;
            matchedRows.forEach(row => {
                const cell = row.cells[amountColIdx] || row.querySelector('td:last-child');
                if (cell) {
                    const numText = cell.innerText.replace(/[^0-9.-]/g, '');
                    const val = parseFloat(numText);
                    if (!isNaN(val)) {
                        totalSum += val;
                    }
                }
            });

            let tfoot = tableElement.querySelector('tfoot');
            if (!tfoot) {
                tfoot = document.createElement('tfoot');
                tableElement.appendChild(tfoot);
            }

            const colCount = ths.length;
            const formattedTotal = '₱' + totalSum.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const beforeColspan = amountColIdx;
            const afterColspan = colCount - 1 - amountColIdx;

            tfoot.innerHTML = `
                <tr class="fw-bold bg-light border-top" style="border-top: 2px solid #cbd5e1 !important; background-color: #f8fafc !important;">
                    ${beforeColspan > 0 ? `<td colspan="${beforeColspan}" class="text-end py-3 text-dark font-w700" style="font-size: 0.85rem; letter-spacing: 0.5px;">TOTAL AMOUNT:</td>` : ''}
                    <td class="text-end py-3 text-danger font-w800" style="font-size: 0.95rem; font-weight: 800 !important;">${formattedTotal}</td>
                    ${afterColspan > 0 ? `<td colspan="${afterColspan}"></td>` : ''}
                </tr>
            `;
        }

        function initFilteredTablePagination(tableElement, rows, itemsPerPage = 6) {
            const wrapper = tableElement.closest('.table-responsive') || tableElement;
            
            // Remove old pagination navigation if present
            const oldNav = wrapper.parentNode.querySelector('.modal-pagination-nav');
            if (oldNav) oldNav.remove();

            if (rows.length === 0) return;

            if (rows.length <= itemsPerPage) {
                rows.forEach(r => r.style.display = '');
                return;
            }

            const totalItems = rows.length;
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            let currentPage = 1;

            const nav = document.createElement('nav');
            nav.className = 'modal-pagination-nav d-flex justify-content-between align-items-center mt-3 pt-2 border-top';

            const info = document.createElement('div');
            info.className = 'small text-muted';

            const ul = document.createElement('ul');
            ul.className = 'pagination pagination-xs mb-0';

            nav.appendChild(info);
            nav.appendChild(ul);
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
                prevLi.innerHTML = `<a class="page-link" href="#" style="border-radius: 4px; margin-right: 4px; padding: 4px 8px; font-size: 0.75rem;">&laquo;</a>`;
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
                                dotsLi.innerHTML = '<span class="page-link" style="border: none; padding: 4px 8px; font-size: 0.75rem;">...</span>';
                                ul.appendChild(dotsLi);
                            }
                            continue;
                        }
                    }

                    const li = document.createElement('li');
                    li.className = `page-item ${currentPage === i ? 'active' : ''}`;
                    let activeStyles = currentPage === i ? 'background-color: #D9251C; border-color: #D9251C; color: #fff;' : '';
                    li.innerHTML = `<a class="page-link" href="#" style="border-radius: 4px; margin-right: 4px; padding: 4px 8px; font-size: 0.75rem; ${activeStyles}">${i}</a>`;
                    li.querySelector('a').onclick = (e) => {
                        e.preventDefault();
                        showPage(i);
                    };
                    ul.appendChild(li);
                }

                // Next
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                nextLi.innerHTML = `<a class="page-link" href="#" style="border-radius: 4px; padding: 4px 8px; font-size: 0.75rem;">&raquo;</a>`;
                nextLi.querySelector('a').onclick = (e) => {
                    e.preventDefault();
                    if (currentPage < totalPages) showPage(currentPage + 1);
                };
                ul.appendChild(nextLi);
            }

            showPage(1);
        }

        // Dynamically instantiate a modal from JavaScript to display custom details with filters
        function showSalesLedgerModal(title, contentHtml) {
            document.getElementById('salesLedgerModalLabel').innerText = title;
            const body = document.getElementById('salesLedgerModalBody');

            const filterToolbarHtml = `
                <div class="modal-filter-toolbar mb-3 p-3 bg-light rounded-3 border" style="border-color: #e2e8f0 !important; background-color: #f8fafc !important;">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-dark mb-1"><i class="las la-user me-1 text-primary"></i>Customer / Staff / Order</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="las la-search"></i></span>
                                <input type="text" class="form-control form-control-sm border-start-0 ps-0" id="modalFilterCustomer" placeholder="Filter by customer, SI #, SO #, or staff...">
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label small fw-bold text-dark mb-1"><i class="las la-calendar me-1 text-primary"></i>Date From</label>
                            <input type="date" class="form-control form-control-sm" id="modalFilterDateFrom">
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label small fw-bold text-dark mb-1"><i class="las la-calendar me-1 text-primary"></i>Date To</label>
                            <input type="date" class="form-control form-control-sm" id="modalFilterDateTo">
                        </div>
                        <div class="col-md-1 col-12 d-flex">
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="btnResetModalFilter" title="Reset Filters" style="height: 31px;">
                                <i class="las la-undo"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;

            body.innerHTML = filterToolbarHtml + '<div class="modal-table-content">' + contentHtml + '</div>';

            const table = body.querySelector('table');
            if (table) {
                const customerInput = document.getElementById('modalFilterCustomer');
                const dateFromInput = document.getElementById('modalFilterDateFrom');
                const dateToInput = document.getElementById('modalFilterDateTo');
                const resetBtn = document.getElementById('btnResetModalFilter');

                const triggerFilter = () => applyModalTableFilters(table);

                if (customerInput) customerInput.addEventListener('input', triggerFilter);
                if (dateFromInput) dateFromInput.addEventListener('change', triggerFilter);
                if (dateToInput) dateToInput.addEventListener('change', triggerFilter);
                if (resetBtn) {
                    resetBtn.addEventListener('click', () => {
                        if (customerInput) customerInput.value = '';
                        if (dateFromInput) dateFromInput.value = '';
                        if (dateToInput) dateToInput.value = '';
                        triggerFilter();
                    });
                }

                triggerFilter();
            }

            const modal = new bootstrap.Modal(document.getElementById('salesLedgerModal'));
            modal.show();
        }
    </script>
    @endpush
</x-app-layout>
