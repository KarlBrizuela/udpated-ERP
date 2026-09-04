<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .content-body .container-fluid {
            padding-bottom: 80px !important;
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

        /* Property tag preview box */
        .property-tag-preview {
            border: 1px dashed rgba(217, 37, 28, 0.25);
            border-radius: 10px;
            padding: 20px;
            width: 100%;
            max-width: 280px;
            background: rgba(217, 37, 28, 0.02);
            font-family: 'Courier New', Courier, monospace;
            margin-bottom: 20px;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.01);
        }
        .property-tag-preview div {
            margin-bottom: 8px;
            border-bottom: 1px dashed #cbd5e1;
            display: flex;
            justify-content: space-between;
            padding-bottom: 4px;
        }
        .property-tag-preview label {
            font-weight: bold;
            color: #475569;
            margin-right: 10px;
            font-size: 0.8rem;
            margin-bottom: 0;
        }
        .property-tag-preview span {
            font-size: 0.8rem;
            color: #0f172a;
            font-weight: 600;
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
            box-shadow: 0 0 0 3px rgba(217, 37, 28, 0.1) !important;
            outline: none !important;
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


        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark fs-18">Assets Ledger</h5>
                            <p class="text-muted small mb-0">Itemized ledger of recorded fixed assets, IT equipment, and institutional items</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group input-group-sm" style="width: 280px;">
                                <span class="input-group-text bg-white" style="border-color: #cbd5e1; border-right: 0; padding: 0 10px; height: 38px; display: flex; align-items: center;">
                                    <i class="las la-search text-muted fs-16"></i>
                                </span>
                                <input type="text" id="table-search-input" class="form-control border-start-0" placeholder="Search Property No, Cat, Dept..." style="border-color: #cbd5e1; outline: none; box-shadow: none; height: 38px; font-size: 0.82rem;">
                                <button type="button" id="btn-search-action" class="btn text-white px-3" style="background-color: #D9251C; border-color: #D9251C; height: 38px; font-size: 0.82rem; border-top-right-radius: 4px; border-bottom-right-radius: 4px;">Search</button>
                            </div>
                            <button type="button" class="btn text-white px-3 d-flex align-items-center gap-2" style="background-color: #D9251C; border-color: #D9251C; height: 38px; font-weight: 600; font-size: 0.82rem; border-radius: 4px;" data-bs-toggle="modal" data-bs-target="#addAssetModal">
                                <i class="las la-plus fs-16"></i> Record Asset
                            </button>
                        </div>
                    </div>

                    <div class="card-body pt-2">
                        <div id="cashTableContainer" style="border: none;">
                            <div class="table-responsive">
                                <table class="table table-modern align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Property No.</th>
                                            <th>Category</th>
                                            <th>Description</th>
                                            <th>Acquired Date</th>
                                            <th>Department</th>
                                            <th>Checked By</th>
                                            <th>Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($assets as $asset)
                                        <tr>
                                            <td><span class="fw-bold text-dark font-monospace">{{ $asset->property_code }}</span></td>
                                            <td><span class="text-dark small">{{ $asset->category }}</span></td>
                                            <td><span class="text-dark small">{{ $asset->description }}</span></td>
                                            <td><span class="fw-bold text-dark small">{{ \Carbon\Carbon::parse($asset->acquisition_date)->format('M d, Y') }}</span></td>
                                            <td><span class="text-dark small">{{ $asset->department }}</span></td>
                                            <td><span class="text-dark small">{{ $asset->checked_by }}</span></td>
                                            <td>
                                                @if($asset->status === 'Active')
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                                @elseif($asset->status === 'Inactive')
                                                <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                                @elseif($asset->status === 'Under Maintenance')
                                                <span class="badge bg-warning-subtle text-warning">Under Maintenance</span>
                                                @else
                                                <span class="badge bg-danger-subtle text-danger">{{ $asset->status }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="javascript:void(0);" 
                                                       class="btn btn-warning shadow btn-xs sharp text-white edit-asset-btn"
                                                       data-id="{{ $asset->asset_id }}"
                                                       data-property-code="{{ $asset->property_code }}"
                                                       data-category="{{ $asset->category }}"
                                                       data-description="{{ $asset->description }}"
                                                       data-acquisition-date="{{ $asset->acquisition_date }}"
                                                       data-department="{{ $asset->department }}"
                                                       data-checked-by="{{ $asset->checked_by }}"
                                                       data-status="{{ $asset->status }}"
                                                       title="Edit Asset">
                                                        <i class="las la-pen"></i>
                                                    </a>
                                                    
                                                    <form action="{{ route('admin-finance.gsd.asset-requests.destroy', $asset->asset_id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger shadow btn-xs sharp btn-delete-asset-confirm" data-property-code="{{ $asset->property_code }}" title="Delete Asset">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">No asset records registered yet. Click "Record New Asset" above to start.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="paginationContainer" class="mt-4 d-flex justify-content-end pe-4">
                            {{ $assets->onEachSide(0)->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
    <!-- MODAL 1: RECORD NEW ASSET -->
    <div class="modal fade" id="addAssetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                    <h5 class="modal-title fw-bold text-dark"><i class="las la-box me-2 text-danger"></i>Record Asset / Supply</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assetRecordForm" action="{{ route('admin-finance.gsd.asset-requests.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select" name="category" id="assetCategory" required>
                                        <option value="">Select Category</option>
                                        <option>Office Supplies</option>
                                        <option>Office Equipment</option>
                                        <option>Furniture & Fixtures</option>
                                        <option>Building/Real Estate</option>
                                        <option>Vehicles</option>
                                        <option>Computers/IT Assets</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Acquired Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="acquisition_date" id="acquiredDate" required>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex flex-column align-items-center justify-content-center">
                                <span class="text-muted small fw-bold d-block mb-2 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Property Tag Preview</span>
                                <div class="property-tag-preview">
                                    <div><label>PROP NO.</label><span id="preview-no">---</span></div>
                                    <div><label>CATEGORY</label><span id="preview-cat">---</span></div>
                                    <div><label>DESC</label><span id="preview-desc">---</span></div>
                                    <div><label>DATE</label><span id="preview-date">---</span></div>
                                    <div><label>DEPT</label><span id="preview-dept">---</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" id="assetDescription" rows="2" placeholder="Detailed description..." required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Department <span class="text-danger">*</span></label>
                                    <select class="form-select" name="department" id="department" required>
                                        <option value="">Select Department</option>
                                        <option>Accounting</option>
                                        <option>DTO</option>
                                        <option>Marketing</option>
                                        <option>MIS</option>
                                        <option>Production</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Checked By <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="checked_by" id="checkedBy" placeholder="Authorized Staff Name" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4 bg-light">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Save Asset Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: EDIT ASSET -->
    <div class="modal fade" id="editAssetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                    <h5 class="modal-title fw-bold text-dark"><i class="las la-edit me-2 text-danger"></i>Edit Asset Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editAssetForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Property / Tag No.</label>
                                    <input type="text" class="form-control" name="property_code" id="edit_property_code" placeholder="Auto-generated if empty">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select" name="category" id="edit_category" required>
                                        <option value="">Select Category</option>
                                        <option>Office Supplies</option>
                                        <option>Office Equipment</option>
                                        <option>Furniture & Fixtures</option>
                                        <option>Building/Real Estate</option>
                                        <option>Vehicles</option>
                                        <option>Computers/IT Assets</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Acquired Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="acquisition_date" id="edit_acquisition_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" name="status" id="edit_status" required>
                                        <option>Active</option>
                                        <option>Inactive</option>
                                        <option>Under Maintenance</option>
                                        <option>Disposed</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Department <span class="text-danger">*</span></label>
                                    <select class="form-select" name="department" id="edit_department" required>
                                        <option value="">Select Department</option>
                                        <option>Accounting</option>
                                        <option>DTO</option>
                                        <option>Marketing</option>
                                        <option>MIS</option>
                                        <option>Production</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Checked By <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="checked_by" id="edit_checked_by" placeholder="Authorized Staff Name" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" id="edit_description" rows="2" placeholder="Detailed description..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4 bg-light">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Update Asset Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endpush

    @push('scripts')
    <script>
        (function($) {
            'use strict';

            // Preview logic for Add Modal
            document.getElementById('assetCategory').addEventListener('change', e => document.getElementById('preview-cat').textContent = e.target.value || '---');
            document.getElementById('assetDescription').addEventListener('input', e => document.getElementById('preview-desc').textContent = e.target.value || '---');
            document.getElementById('acquiredDate').addEventListener('change', e => document.getElementById('preview-date').textContent = e.target.value || '---');
            document.getElementById('department').addEventListener('change', e => document.getElementById('preview-dept').textContent = e.target.value || '---');

            // Edit Modal Data Binding
            $(document).on('click', '.edit-asset-btn', function() {
                const id = $(this).attr('data-id');
                const form = document.getElementById('editAssetForm');
                form.action = `/admin-finance/gsd/asset-requests/${id}`;
                
                document.getElementById('edit_property_code').value = $(this).attr('data-property-code');
                document.getElementById('edit_category').value = $(this).attr('data-category');
                document.getElementById('edit_description').value = $(this).attr('data-description');
                document.getElementById('edit_acquisition_date').value = $(this).attr('data-acquisition-date');
                document.getElementById('edit_department').value = $(this).attr('data-department');
                document.getElementById('edit_checked_by').value = $(this).attr('data-checked-by');
                document.getElementById('edit_status').value = $(this).attr('data-status');
                
                // Show modal
                $('#editAssetModal').modal('show');
            });

            // SweetAlert2 Delete Handler
            $(document).on('click', '.btn-delete-asset-confirm', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                const propertyNo = $(this).attr('data-property-code') || '';
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: `This will permanently delete the record for asset ${propertyNo}.`,
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
