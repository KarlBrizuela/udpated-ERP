@php
    $totalEntries = \App\Models\JournalEntry::count();
    $totalVolume = \App\Models\JournalEntryItem::sum('debit');
@endphp

<x-app-layout :title="'General Journal Entries'" :sidebar="'admin-finance'">
    <div class="container-fluid p-0">
        
        <!-- Summarization Cards Row -->
        <div class="row mb-4">
            <div class="col-md-6 col-sm-6">
                <div class="card border-0 shadow-sm p-3 mb-3 mb-md-0" style="border-radius: 8px; border-left: 4px solid #3b82f6; background-color: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem; color: #000000;">Total Entries</span>
                            <h3 class="fw-bold text-dark mt-1 mb-0" style="letter-spacing: -0.5px;">{{ number_format($totalEntries) }}</h3>
                        </div>
                        <div class="text-primary opacity-75">
                            <i class="las la-book fs-32"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6">
                <div class="card border-0 shadow-sm p-3" style="border-radius: 8px; border-left: 4px solid #f59e0b; background-color: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem; color: #000000;">Total Debit Volume</span>
                            <h3 class="fw-bold text-dark mt-1 mb-0" style="letter-spacing: -0.5px;">₱{{ number_format($totalVolume, 2) }}</h3>
                        </div>
                        <div class="text-warning opacity-75">
                            <i class="las la-money-bill-wave fs-32"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card shadow-sm border-0" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                    <div class="card-header border-0 bg-white pt-3 pb-2 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
                            <form id="searchForm" method="GET" action="{{ route('accounting.journal.index') }}" class="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
                                <!-- Date From -->
                                <div class="d-flex align-items-center gap-1">
                                    <span class="text-dark small fw-bold" style="font-size: 0.75rem;">From:</span>
                                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm" style="height: 38px; border-color: #cbd5e1; border-radius: 4px; font-size: 0.82rem; width: 170px; box-shadow: none;">
                                </div>
                                <!-- Date To -->
                                <div class="d-flex align-items-center gap-1 me-1">
                                    <span class="text-dark small fw-bold" style="font-size: 0.75rem;">To:</span>
                                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm" style="height: 38px; border-color: #cbd5e1; border-radius: 4px; font-size: 0.82rem; width: 170px; box-shadow: none;">
                                </div>
                                
                                <button id="filterSubmitBtn" type="submit" class="btn text-white fw-bold px-3 d-flex align-items-center justify-content-center me-3" style="background: #D9251C; border-color: #D9251C; height: 38px; border-radius: 4px; font-size: 0.82rem;">
                                    {{ (request('date_from') || request('date_to')) ? 'Clear Filter' : 'Apply Filter' }}
                                </button>

                                <!-- Search Box -->
                                <div class="input-group input-group-sm flex-grow-1" style="max-width: 280px; min-width: 180px;">
                                    <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; border-radius: 4px 0 0 4px; height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 10px;">
                                        <i class="las la-search text-muted fs-16"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search entries..." value="{{ request('search') }}" style="height: 38px; border-color: #cbd5e1; border-radius: 0 4px 4px 0; font-size: 0.82rem; padding-left: 0; outline: none; box-shadow: none;">
                                </div>
                                
                                <button id="searchSubmitBtn" type="submit" class="btn text-white fw-bold px-3 d-flex align-items-center justify-content-center" style="background: #D9251C; border-color: #D9251C; height: 38px; border-radius: 4px; font-size: 0.82rem;">
                                    {{ request('search') ? 'Clear' : 'Search' }}
                                </button>
                            </form>
                        </div>
                        <div class="d-flex align-items-center justify-content-end">
                            <a href="{{ route('accounting.journal.create') }}" class="btn btn-primary rounded shadow-sm px-3 d-flex align-items-center justify-content-center fw-bold" style="background: #D9251C; color: #ffffff; border: none; height: 38px !important; padding: 0 1.25rem; font-size: 0.82rem;">
                                <i class="las la-plus me-1"></i>New Entry
                            </a>
                        </div>
                    </div>
                    <div class="card-body pt-3 px-0">
                        <div id="journalTableContainer" class="table-responsive" style="border: none;">
                            <table class="journal-table table align-middle" style="margin-bottom: 0;">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Entry No.</th>
                                        <th>Date</th>
                                        <th>Reference</th>
                                        <th>Memo</th>
                                        <th>Created By</th>
                                        <th class="text-center pe-4" style="width: 120px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($entries as $entry)
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark">{{ $entry->entry_no }}</td>
                                        <td>{{ date('M d, Y', strtotime($entry->date)) }}</td>
                                        <td><span class="text-muted small">{{ $entry->reference ?? '---' }}</span></td>
                                        <td><span class="text-limit" title="{{ $entry->memo }}">{{ $entry->memo ?? '---' }}</span></td>
                                        <td><span class="text-muted">{{ $entry->creator->name ?? 'Unknown' }}</span></td>
                                        <td class="pe-4">
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route('accounting.journal.show', $entry->id) }}" class="btn btn-info shadow btn-xs sharp me-1 text-white" title="View">
                                                    <i class="las la-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger shadow btn-xs sharp"
                                                    data-toggle="modal"
                                                    data-bs-toggle="modal" 
                                                    data-target="#deleteModal"
                                                    data-bs-target="#deleteModal" 
                                                    data-url="{{ route('accounting.journal.destroy', $entry->id) }}"
                                                    data-entry-no="{{ $entry->entry_no }}"
                                                    title="Delete">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No journal entries found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div id="paginationContainer" class="mt-4 d-flex justify-content-end pe-4">
                            {{ $entries->onEachSide(0)->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 8px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    Are you sure you want to delete journal entry <strong id="modalEntryNo" style="color: #D9251C;"></strong>? This action will reverse all associated ledger postings and cannot be undone.
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal" data-dismiss="modal" style="border-radius: 4px;">Cancel</button>
                    <form id="deleteForm" action="" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn text-white btn-sm px-3" style="background-color: #D9251C; border-color: #D9251C; border-radius: 4px;">Yes, Delete Entry</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete modal trigger
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                const handleModalShow = function(event) {
                    const button = event.relatedTarget;
                    const url = button.getAttribute('data-url');
                    const entryNo = button.getAttribute('data-entry-no');
                    const form = deleteModal.querySelector('#deleteForm');
                    const entrySpan = deleteModal.querySelector('#modalEntryNo');
                    
                    form.setAttribute('action', url);
                    entrySpan.textContent = entryNo;
                };

                deleteModal.addEventListener('show.bs.modal', handleModalShow);
                
                if (window.jQuery) {
                    $('#deleteModal').on('show.bs.modal', handleModalShow);
                }
            }

            // AJAX Search Form Handler
            const searchForm = document.getElementById('searchForm');
            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Blur the button/input to remove the focus outline ring
                    if (document.activeElement) {
                        document.activeElement.blur();
                    }
                    
                    const searchInput = searchForm.querySelector('input[name="search"]');
                    const dateFromInput = searchForm.querySelector('input[name="date_from"]');
                    const dateToInput = searchForm.querySelector('input[name="date_to"]');
                    
                    const filterSubmitBtn = document.getElementById('filterSubmitBtn');
                    const searchSubmitBtn = document.getElementById('searchSubmitBtn');
                    const url = new URL(window.location.href);
                    
                    const submitterId = e.submitter ? e.submitter.id : '';
                    
                    if (submitterId === 'filterSubmitBtn') {
                        if (filterSubmitBtn && filterSubmitBtn.textContent.trim() === 'Clear Filter') {
                            if (dateFromInput) dateFromInput.value = '';
                            if (dateToInput) dateToInput.value = '';
                            url.searchParams.delete('date_from');
                            url.searchParams.delete('date_to');
                            if (filterSubmitBtn) filterSubmitBtn.textContent = 'Apply Filter';
                        } else {
                            const dateFromValue = dateFromInput ? dateFromInput.value : '';
                            const dateToValue = dateToInput ? dateToInput.value : '';
                            
                            if (dateFromValue) url.searchParams.set('date_from', dateFromValue);
                            else url.searchParams.delete('date_from');
                            
                            if (dateToValue) url.searchParams.set('date_to', dateToValue);
                            else url.searchParams.delete('date_to');
                            
                            // Clear search filter when applying date filter explicitly as requested
                            if (searchInput) searchInput.value = '';
                            url.searchParams.delete('search');
                            if (searchSubmitBtn) searchSubmitBtn.textContent = 'Search';
                            
                            if (dateFromValue || dateToValue) {
                                if (filterSubmitBtn) filterSubmitBtn.textContent = 'Clear Filter';
                            }
                        }
                        url.searchParams.delete('page');
                        loadTableData(url.toString());
                        
                    } else if (submitterId === 'searchSubmitBtn') {
                        if (searchSubmitBtn && searchSubmitBtn.textContent.trim() === 'Clear') {
                            if (searchInput) searchInput.value = '';
                            url.searchParams.delete('search');
                            searchSubmitBtn.textContent = 'Search';
                        } else {
                            const searchValue = searchInput ? searchInput.value.trim() : '';
                            if (searchValue) {
                                url.searchParams.set('search', searchValue);
                                searchSubmitBtn.textContent = 'Clear';
                            } else {
                                url.searchParams.delete('search');
                            }
                            
                            // Clear date filters when searching explicitly
                            if (dateFromInput) dateFromInput.value = '';
                            if (dateToInput) dateToInput.value = '';
                            url.searchParams.delete('date_from');
                            url.searchParams.delete('date_to');
                            if (filterSubmitBtn) filterSubmitBtn.textContent = 'Apply Filter';
                        }
                        url.searchParams.delete('page');
                        loadTableData(url.toString());
                    }
                });
            }

            // Intercept Pagination Link Clicks inside paginationContainer
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

            // Browser Back/Forward navigation support
            window.addEventListener('popstate', function() {
                loadTableData(window.location.href, false);
            });

            function loadTableData(url, pushState = true) {
                const tableContainer = document.getElementById('journalTableContainer');
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
                    
                    const newTable = doc.getElementById('journalTableContainer');
                    const newPagination = doc.getElementById('paginationContainer');
                    
                    if (newTable && tableContainer) {
                        tableContainer.innerHTML = newTable.innerHTML;
                    }
                    if (newPagination && paginationContainer) {
                        paginationContainer.innerHTML = newPagination.innerHTML;
                    }
                    
                    // Sync current input value and button text with the URL search param
                    const searchInput = document.querySelector('#searchForm input[name="search"]');
                    const dateFromInput = document.querySelector('#searchForm input[name="date_from"]');
                    const dateToInput = document.querySelector('#searchForm input[name="date_to"]');
                    const searchSubmitBtn = document.getElementById('searchSubmitBtn');
                    
                    const urlObj = new URL(url);
                    const queryVal = urlObj.searchParams.get('search') || '';
                    const dateFromVal = urlObj.searchParams.get('date_from') || '';
                    const dateToVal = urlObj.searchParams.get('date_to') || '';
                    
                    if (searchInput) searchInput.value = queryVal;
                    if (dateFromInput) dateFromInput.value = dateFromVal;
                    if (dateToInput) dateToInput.value = dateToVal;
                    
                    if (searchSubmitBtn) {
                        searchSubmitBtn.textContent = queryVal ? 'Clear' : 'Search';
                    }
                    const filterSubmitBtn = document.getElementById('filterSubmitBtn');
                    if (filterSubmitBtn) {
                        filterSubmitBtn.textContent = (dateFromVal || dateToVal) ? 'Clear Filter' : 'Apply Filter';
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
        });
    </script>
    @endpush

    <style>
        .journal-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.8px;
            padding: 16px 12px;
            border-bottom: 2px solid #e2e8f0;
        }

        .journal-table td {
            padding: 16px 12px;
            font-size: 0.88rem;
            color: #0f172a;
            border-bottom: 1px solid #f1f5f9;
        }

        .journal-table tr {
            transition: all 0.15s ease-in-out;
        }

        .journal-table tr:hover {
            background-color: #f8fafc;
        }

        .text-limit {
            display: inline-block;
            max-width: 280px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 0.88rem;
            color: #64748b;
        }

        .status-badge-posted {
            background-color: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
            font-weight: 600;
            font-size: 0.78rem;
            padding: 4px 12px;
            border-radius: 4px;
            display: inline-block;
        }

        .status-badge-draft {
            background-color: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
            font-weight: 600;
            font-size: 0.78rem;
            padding: 4px 12px;
            border-radius: 4px;
            display: inline-block;
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
</x-app-layout>
