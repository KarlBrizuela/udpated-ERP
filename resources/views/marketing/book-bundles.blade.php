<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        /* Fix Table Layout & Select Inputs in Bundle Modal */
        #bundleItemsTable {
            table-layout: fixed;
            width: 100%;
        }
        #bundleItemsTable td, #bundleItemsTable th {
            vertical-align: middle;
        }
        .select2-container {
            width: 100% !important;
        }
        .select2-container .select2-selection--single {
            height: 38px !important;
            padding: 4px 8px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
        }
        .select2-container .select2-selection--single .select2-selection__rendered {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            line-height: 1.5 !important;
            padding-left: 0 !important;
            padding-right: 20px !important;
            color: #333;
        }
        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
        .select2-dropdown {
            max-width: 100% !important;
            z-index: 1070 !important;
        }
        .select2-results__option {
            font-size: 0.85rem !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            padding: 6px 10px !important;
        }

        /* Custom Page Tabs Styling */
        .page-tabs {
            border-bottom: 2px solid #eee;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1.5rem;
            padding-left: 1rem;
        }
        .page-tabs .nav-link {
            font-size: 0.95rem;
            font-weight: 700;
            color: #666;
            border: none;
            background: transparent;
            padding: 12px 24px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.2s ease-in-out;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .page-tabs .nav-link:hover {
            color: #D9251C;
            background: transparent !important;
        }
        .page-tabs .nav-link.active {
            color: #D9251C;
            border-bottom-color: #D9251C;
            background: transparent !important;
        }

        /* Export Excel Button Styling */
        .btn-export-excel {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff !important;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2);
            height: 38px;
            min-height: 38px;
            box-sizing: border-box;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            padding: 0 1rem;
            font-size: 0.85rem;
            gap: 0.5rem;
        }

        .btn-export-excel:hover {
            background-color: #218838;
            box-shadow: 0 6px 8px rgba(40, 167, 69, 0.3);
            transform: translateY(-1px);
            color: #fff !important;
        }

        .btn-export-excel i {
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            font-size: 1.1rem !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: auto !important;
            height: 1.1rem !important;
            line-height: 1 !important;
            color: #fff !important;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <!-- Navigation Tabs -->
            <ul class="nav page-tabs" id="bookMgmtTabs">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('marketing.products') ? 'active' : '' }}" id="book-list-tab" href="{{ route('marketing.products') }}">
                        <i class="las la-book" style="font-size: 1.25rem;"></i>
                        <span>Book List</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('marketing.non-books') ? 'active' : '' }}" id="non-books-tab" href="{{ route('marketing.non-books') }}">
                        <i class="las la-list" style="font-size: 1.25rem;"></i>
                        <span>Non-Books</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('marketing.indices') ? 'active' : '' }}" id="book-index-tab" href="{{ route('marketing.indices') }}">
                        <i class="las la-tag" style="font-size: 1.25rem;"></i>
                        <span>Book Index</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('marketing.bundles') ? 'active' : '' }}" id="book-bundle-tab" href="{{ route('marketing.bundles') }}">
                        <i class="las la-boxes" style="font-size: 1.25rem;"></i>
                        <span>Book Bundle</span>
                    </a>
                </li>
            </ul>

            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h4 class="card-title mb-0">Book Bundle List</h4>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-3 mt-sm-0">
                        <!-- Bundle Search Form -->
                        <form action="{{ route('marketing.bundles') }}" method="GET" class="d-flex align-items-center gap-2">
                            <div style="width: 250px; height: 38px; display: flex; align-items: center; border: 1px solid #ced4da; border-radius: 4px; background-color: #f8f9fa; padding: 0 12px; box-sizing: border-box;">
                                <span class="las la-search text-muted me-2" style="font-size: 1.1rem; line-height: 1;"></span>
                                <input type="text" name="bundle_search" class="form-control" 
                                       placeholder="Search bundles..." value="{{ request('bundle_search') }}" 
                                       style="border: none !important; background: transparent !important; padding: 0 !important; height: 100%; font-size: 0.85rem; color: #333; outline: none !important; box-shadow: none !important;">
                                @if(request('bundle_search'))
                                    <a href="{{ route('marketing.bundles') }}" class="text-muted d-inline-flex align-items-center justify-content-center ms-2" title="Clear search" style="text-decoration: none;">
                                        <span class="las la-times-circle" style="color: #999; font-size: 1.25rem; cursor: pointer;"></span>
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-danger text-white rounded d-inline-flex align-items-center justify-content-center gap-2" style="height: 38px; padding: 0 1.2rem; border: none; font-size: 0.85rem; font-weight: 500; background-color: #D9251C; box-shadow: 0 4px 6px rgba(217, 37, 28, 0.15);">
                                <span class="las la-search" style="font-size: 1rem; color: #fff;"></span>
                                <span>Search</span>
                            </button>
                        </form>

                        <a href="{{ route('marketing.bundles.export', ['bundle_search' => request('bundle_search')]) }}" class="btn btn-export-excel rounded" title="Export all book bundles to Excel">
                            <i class="las la-file-excel"></i>
                            <span>Export Excel</span>
                        </a>
                        <a href="javascript:void(0);" id="addNewBundleBtn"
                            class="btn btn-primary rounded d-flex align-items-center"
                            style="gap: 0.5rem; padding: 0.5rem 1rem; height: 38px; min-height: 38px; line-height: 1.5; box-sizing: border-box; border: none; background: #ff0000; color: #ffffff; font-weight: 500;">
                            <i class="las la-plus" style="font-size: 1rem;"></i>
                            <span>Add New Bundle</span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Bundle Name</th>
                                    <th>Description</th>
                                    <th>Included Books</th>
                                    <th>Price</th>
                                    <th>MIBF Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bundles as $bundle)
                                <tr>
                                    <td><strong>#{{ $bundle->sku }}</strong></td>
                                    <td>{{ $bundle->name }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($bundle->description ?? 'N/A', 50) }}</td>
                                    <td>
                                         @php
                                             $totalBooksCount = $bundle->books->count();
                                             $displayedBooks = $bundle->books->take(3);
                                             $remainingCount = $totalBooksCount - 3;
                                         @endphp
                                         @foreach($displayedBooks as $b)
                                             <span class="badge badge-outline-danger mb-1" style="display:inline-block; font-size:0.75rem;">
                                                 {{ \Illuminate\Support\Str::limit($b->name, 30) }} <strong class="text-dark">x{{ $b->pivot->quantity }}</strong>
                                             </span>
                                         @endforeach
                                         @if($remainingCount > 0)
                                             <a href="javascript:void(0);" class="badge bg-secondary text-white mb-1 view-bundle-btn" data-id="{{ $bundle->id }}" style="text-decoration:none; font-size:0.75rem;" title="Click to view all included books">
                                                 + {{ $remainingCount }} more...
                                             </a>
                                         @endif
                                     </td>
                                    <td>₱{{ number_format($bundle->price, 2) }}</td>
                                    <td>{{ $bundle->mibf_price !== null ? '₱' . number_format($bundle->mibf_price, 2) : '-' }}</td>
                                    <td>
                                        @if($bundle->stock > 0)
                                            <span class="badge badge-success">{{ $bundle->stock }} pcs</span>
                                        @else
                                            <span class="badge badge-danger">0 pcs</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($bundle->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-light">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="javascript:void(0);" class="btn btn-secondary shadow btn-xs sharp me-1 view-bundle-btn" 
                                               data-id="{{ $bundle->id }}"><i class="far fa-eye"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1 edit-bundle-btn" 
                                               data-id="{{ $bundle->id }}"><i class="fas fa-pencil-alt"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-danger shadow btn-xs sharp delete-bundle-btn"
                                               data-id="{{ $bundle->id }}"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No book bundles found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                        <div class="text-muted small">
                            Showing {{ $bundles->firstItem() ?? 0 }} to {{ $bundles->lastItem() ?? 0 }} of {{ $bundles->total() }} entries
                        </div>
                        <div>
                            {{ $bundles->appends(['bundle_search' => request('bundle_search')])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
    <!-- Add/Edit Bundle Modal -->
    <div class="modal" id="addBundleModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form id="addBundleForm" class="modal-content">
                @csrf
                <input type="hidden" name="bundle_id" id="modal_bundle_id">
                <div class="modal-header" style="background: #D9251C; color: #fff;">
                    <h5 class="modal-title text-white" id="addBundleModalTitle">Add New Book Bundle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">BUNDLE NAME <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="name" id="bundle_name" required placeholder="e.g. Theological Starter Pack">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">SKU <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="sku" id="bundle_sku" required placeholder="e.g. BNDL-THEO-01">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">PRICE (₱) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="price" id="bundle_price" required placeholder="e.g. 1500.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">MIBF PRICE (₱)</label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="mibf_price" id="bundle_mibf_price" placeholder="Optional">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">STOCK <span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control form-control-sm" name="stock" id="bundle_stock" required placeholder="e.g. 10">
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-center">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" name="is_active" id="bundle_is_active" value="1" checked>
                                <label class="form-check-label small fw-bold" for="bundle_is_active">ACTIVE ON POS</label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label small fw-bold">DESCRIPTION</label>
                            <textarea class="form-control form-control-sm" name="description" id="bundle_description" rows="3" placeholder="Optional description..."></textarea>
                        </div>
                    </div>
                    
                    <hr>
                    <div class="mb-3">
                        <h6 class="fw-bold mb-0"><i class="las la-book me-2"></i>Bundle Books List</h6>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="bundleItemsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 70%">Book <span class="text-danger">*</span></th>
                                    <th style="width: 20%">Quantity <span class="text-danger">*</span></th>
                                    <th style="width: 10%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="bundleItemsContainer">
                                <!-- Dynamic rows go here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-sm btn-success text-white" id="addBundleItemRowBtn">
                        <i class="las la-plus me-1"></i> Add Book
                    </button>
                    <div>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="saveBundleBtn" style="background: #D9251C; border-color: #D9251C;">Save Bundle</button>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </div>

    <!-- View Bundle Modal -->
    <div class="modal" id="viewBundleModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-secondary">
                    <h5 class="modal-title text-white"><i class="las la-boxes me-2"></i>View Bundle Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase d-block">Stock</label>
                        <span id="view_bundle_stock" class="fw-bold text-dark fs-5"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase d-block">Status</label>
                        <span id="view_bundle_status"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase d-block">Description</label>
                        <p id="view_bundle_description" class="bg-light p-2 rounded small"></p>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-3"><i class="las la-book me-2"></i>Included Books</h6>
                    <ul class="list-group" id="view_bundle_books_list">
                        <!-- Dynamic list items -->
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Bundle Confirmation Modal -->
    <div class="modal" id="deleteBundleModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white"><i class="fas fa-trash me-2"></i>Confirm Delete Bundle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this book bundle? This action cannot be undone.</p>
                    <input type="hidden" id="delete_bundle_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBundleBtn">Delete Bundle</button>
                </div>
            </div>
        </div>
    </div>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        // Bundle modals: use direct DOM show/hide to avoid Bootstrap JS conflicts
        function showBundleModal(id) {
            // Hide any open bundle modals first
            ['addBundleModal', 'viewBundleModal', 'deleteBundleModal'].forEach(function(mid) {
                var m = document.getElementById(mid);
                if (m) { m.style.display = 'none'; m.removeAttribute('aria-modal'); m.setAttribute('aria-hidden', 'true'); }
            });
            // Remove stale backdrops
            document.querySelectorAll('#bundle-backdrop, .modal-backdrop').forEach(function(b) { b.remove(); });
            document.body.classList.remove('modal-open');

            var el = document.getElementById(id);
            if (!el) return;

            // CRITICAL: move modal to <body> to escape CSS transform stacking context
            if (el.parentNode !== document.body) {
                document.body.appendChild(el);
            }

            // Add backdrop
            var backdrop = document.createElement('div');
            backdrop.id = 'bundle-backdrop';
            backdrop.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1050;';
            backdrop.addEventListener('click', function() {
                hideBundleModal(id);
            });
            document.body.appendChild(backdrop);
            document.body.classList.add('modal-open');

            el.style.cssText = 'display:block !important; position:fixed !important; top:0; left:0; width:100%; height:100%; overflow-x:hidden; overflow-y:auto; z-index:1060;';
            el.removeAttribute('aria-hidden');
            el.setAttribute('aria-modal', 'true');
        }

        function hideBundleModal(id) {
            ['addBundleModal', 'viewBundleModal', 'deleteBundleModal'].forEach(function(mid) {
                var m = document.getElementById(mid);
                if (m) {
                    m.style.display = 'none';
                    m.setAttribute('aria-hidden', 'true');
                    m.removeAttribute('aria-modal');
                }
            });
            document.querySelectorAll('#bundle-backdrop, .modal-backdrop').forEach(function(b) {
                b.remove();
            });
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }

        let bundleItemIndex = 0;

        // Helper to add bundle item row
        function addBundleItemRow(bookId = '', bookText = '', quantity = 1) {
            const container = document.getElementById('bundleItemsContainer');
            
            let optionsHtml = '<option value="">Select Book...</option>';
            if (bookId && bookText) {
                optionsHtml += `<option value="${bookId}" selected>${bookText}</option>`;
            }

            const rowId = `bundle-item-row-${bundleItemIndex}`;
            const tr = document.createElement('tr');
            tr.id = rowId;
            tr.innerHTML = `
                <td>
                    <select name="items[${bundleItemIndex}][book_id]" class="form-select form-select-sm select-book-item" required style="width: 100%;">
                        ${optionsHtml}
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${bundleItemIndex}][quantity]" class="form-control form-control-sm" value="${quantity}" min="1" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-danger text-white remove-item-row">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            container.appendChild(tr);

            const selectEl = tr.querySelector('.select-book-item');

            // Bind remove handler
            tr.querySelector('.remove-item-row').addEventListener('click', function() {
                tr.remove();
            });

            // Initialize Select2 with Server-Side AJAX Search for 4000+ books
            if (window.jQuery && typeof jQuery.fn.select2 === 'function') {
                const $select = jQuery(selectEl);
                $select.select2({
                    dropdownParent: jQuery('#addBundleModal'),
                    width: '100%',
                    placeholder: 'Type to search book by title or SKU...',
                    allowClear: true,
                    ajax: {
                        url: "{{ route('marketing.bundles.search-books') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            const currentSelect = this[0];
                            const excludeIds = Array.from(document.querySelectorAll('#bundleItemsContainer .select-book-item'))
                                .filter(s => s !== currentSelect && s.value)
                                .map(s => s.value);

                            return {
                                q: params.term,
                                exclude_ids: excludeIds
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.results
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0
                });
            }

            bundleItemIndex++;
        }

        // Add item row event (single pinned button in footer)
        const addRowBtn = document.getElementById('addBundleItemRowBtn');
        if (addRowBtn) {
            addRowBtn.addEventListener('click', function(e) {
                e.preventDefault();
                addBundleItemRow();
                // Auto-scroll modal body down to newly added row
                const modalBody = document.querySelector('#addBundleModal .modal-body');
                if (modalBody) {
                    setTimeout(() => {
                        modalBody.scrollTop = modalBody.scrollHeight;
                    }, 50);
                }
            });
        }

        // Open Add Bundle modal via button click
        const addNewBundleBtn = document.getElementById('addNewBundleBtn');
        if (addNewBundleBtn) {
            addNewBundleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('modal_bundle_id').value = '';
                document.getElementById('addBundleForm').reset();
                document.getElementById('bundleItemsContainer').innerHTML = '';
                document.getElementById('addBundleModalTitle').innerText = 'Add New Book Bundle';
                bundleItemIndex = 0;
                showBundleModal('addBundleModal');
            });
        }

        // Wire up close buttons on bundle modals
        document.querySelectorAll('#addBundleModal [data-bs-dismiss="modal"], #viewBundleModal [data-bs-dismiss="modal"], #deleteBundleModal [data-bs-dismiss="modal"]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var modal = this.closest('.modal');
                if (modal) hideBundleModal(modal.id);
            });
        });

        // Clear dynamic rows and reset form when add bundle modal is dismissed
        const addBundleModalEl = document.getElementById('addBundleModal');
        const bundleForm = document.getElementById('addBundleForm');

        // Add/Edit Bundle form submit
        if (bundleForm) {
            bundleForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const saveBtn = document.getElementById('saveBundleBtn');
                saveBtn.disabled = true;

                const formData = new FormData(this);
                const payload = {
                    name: formData.get('name'),
                    sku: formData.get('sku'),
                    price: formData.get('price'),
                    stock: formData.get('stock'),
                    description: formData.get('description'),
                    is_active: formData.get('is_active') ? 1 : 0,
                    items: []
                };

                const rows = document.querySelectorAll('#bundleItemsContainer tr');
                let hasItems = false;
                rows.forEach(row => {
                    const bookSelect = row.querySelector('.select-book-item');
                    const qtyInput = row.querySelector('input[type="number"]');
                    if (bookSelect && qtyInput) {
                        const bookId = bookSelect.value;
                        const qty = qtyInput.value;
                        if (bookId && qty) {
                            hasItems = true;
                            payload.items.push({
                                book_id: bookId,
                                quantity: qty
                            });
                        }
                    }
                });

                if (!hasItems) {
                    window.showAlert('Please add at least one book to the bundle.', 'danger');
                    saveBtn.disabled = false;
                    return;
                }

                const bundleId = document.getElementById('modal_bundle_id').value;
                const url = bundleId ? `/marketing/book-bundles/${bundleId}/update` : "{{ route('marketing.bundles.store') }}";

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    window.showAlert(data.message, 'success');
                    hideBundleModal('addBundleModal');
                    setTimeout(() => location.reload(), 1500);
                })
                .catch(err => {
                    console.error('Save Bundle Error:', err);
                    let msg = 'Failed to save bundle.';
                    if(err.errors) msg = Object.values(err.errors).flat().join(' ');
                    else if (err.message) msg = err.message;
                    window.showAlert(msg, 'danger');
                    saveBtn.disabled = false;
                });
            });
        }

        // View Bundle Click Handlers
        document.querySelectorAll('.view-bundle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch(`/marketing/book-bundles/${id}/edit`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('view_bundle_sku').innerText = '#' + data.sku;
                        document.getElementById('view_bundle_name').innerText = data.name;
                        document.getElementById('view_bundle_price').innerText = '₱' + parseFloat(data.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        document.getElementById('view_bundle_stock').innerText = data.stock + ' pcs';
                        
                        const statusEl = document.getElementById('view_bundle_status');
                        if (data.is_active) {
                            statusEl.className = 'badge badge-success';
                            statusEl.innerText = 'Active';
                        } else {
                            statusEl.className = 'badge badge-light';
                            statusEl.innerText = 'Inactive';
                        }

                        document.getElementById('view_bundle_description').innerText = data.description || 'No description provided.';
                        
                        const listContainer = document.getElementById('view_bundle_books_list');
                        listContainer.innerHTML = '';
                        data.books.forEach(b => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item d-flex justify-content-between align-items-center py-2';
                            li.innerHTML = `
                                <span>${b.name}</span>
                                <span class="badge bg-danger rounded-pill">x${b.pivot.quantity}</span>
                            `;
                            listContainer.appendChild(li);
                        });

                        showBundleModal('viewBundleModal');
                    });
            });
        });

        // Edit Bundle Click Handlers
        document.querySelectorAll('.edit-bundle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch(`/marketing/book-bundles/${id}/edit`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('modal_bundle_id').value = data.id;
                        document.getElementById('bundle_name').value = data.name;
                        document.getElementById('bundle_sku').value = data.sku;
                        document.getElementById('bundle_price').value = data.price;
                        document.getElementById('bundle_stock').value = data.stock;
                        document.getElementById('bundle_is_active').checked = !!data.is_active;
                        document.getElementById('bundle_description').value = data.description ?? '';
                        
                        const container = document.getElementById('bundleItemsContainer');
                        container.innerHTML = '';
                        bundleItemIndex = 0;
                        
                        data.books.forEach(b => {
                            const text = `${b.name} (₱${parseFloat(b.price).toFixed(2)})`;
                            addBundleItemRow(b.id, text, b.pivot.quantity);
                        });

                        document.getElementById('addBundleModalTitle').innerText = "Edit Book Bundle";
                        showBundleModal('addBundleModal');
                    });
            });
        });

        // Delete Bundle Click Handlers
        document.querySelectorAll('.delete-bundle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('delete_bundle_id').value = this.dataset.id;
                showBundleModal('deleteBundleModal');
            });
        });

        const confirmDeleteBundleBtn = document.getElementById('confirmDeleteBundleBtn');
        if (confirmDeleteBundleBtn) {
            confirmDeleteBundleBtn.addEventListener('click', function() {
                const id = document.getElementById('delete_bundle_id').value;
                this.disabled = true;

                fetch(`/marketing/book-bundles/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideBundleModal('deleteBundleModal');
                    window.showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                })
                .catch(err => {
                    console.error('Delete Bundle Error:', err);
                    window.showAlert('Failed to delete bundle.', 'danger');
                    this.disabled = false;
                });
            });
        }
    </script>
    @endpush
</x-app-layout>
