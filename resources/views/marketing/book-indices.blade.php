<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
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
                        <h4 class="card-title mb-0">Book Index Mapping</h4>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-3 mt-sm-0">
                        <!-- Search Form -->
                        <form action="{{ route('marketing.indices') }}" method="GET" class="d-flex align-items-center gap-2">
                            <div style="width: 250px; height: 38px; display: flex; align-items: center; border: 1px solid #ced4da; border-radius: 4px; background-color: #f8f9fa; padding: 0 12px; box-sizing: border-box;">
                                <span class="las la-search text-muted me-2" style="font-size: 1.1rem; line-height: 1;"></span>
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search indices or books..." value="{{ request('search') }}" 
                                       style="border: none !important; background: transparent !important; padding: 0 !important; height: 100%; font-size: 0.85rem; color: #333; outline: none !important; box-shadow: none !important;">
                                @if(request('search'))
                                    <a href="{{ route('marketing.indices') }}" class="text-muted d-inline-flex align-items-center justify-content-center ms-2" title="Clear search" style="text-decoration: none;">
                                        <span class="las la-times-circle" style="color: #999; font-size: 1.25rem; cursor: pointer;"></span>
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-danger text-white rounded d-inline-flex align-items-center justify-content-center gap-2" style="height: 38px; padding: 0 1.2rem; border: none; font-size: 0.85rem; font-weight: 500; background-color: #D9251C; box-shadow: 0 4px 6px rgba(217, 37, 28, 0.15);">
                                <span class="las la-search" style="font-size: 1rem; color: #fff;"></span>
                                <span>Search</span>
                            </button>
                        </form>

                        <a href="{{ route('marketing.indices.export', ['search' => request('search')]) }}" class="btn btn-export-excel rounded" title="Export all book indices to Excel">
                            <i class="las la-file-excel"></i>
                            <span>Export Excel</span>
                        </a>
                        <a href="javascript:void(0);" id="addNewIndexBtn"
                            class="btn btn-primary rounded d-flex align-items-center"
                            style="gap: 0.5rem; padding: 0.5rem 1rem; height: 38px; min-height: 38px; line-height: 1.5; box-sizing: border-box; border: none; background: #ff0000; color: #ffffff; font-weight: 500;">
                            <i class="las la-plus" style="font-size: 1rem;"></i>
                            <span>Add Book Index</span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Article</th>
                                    <th>Barcode</th>
                                    <th>NBS Barcode</th>
                                    <th>Original Book Name</th>
                                    <th>Index Value</th>
                                    <th>Resulting Book Name</th>
                                    <th>Stock</th>
                                    <th>Price (₱)</th>
                                    <th>MIBF Price (₱)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($indices as $idx)
                                <tr>
                                    <td><strong>#{{ $idx->book->sku ?? 'N/A' }}</strong></td>
                                    <td>{{ $idx->article ?? $idx->book->article ?? 'N/A' }}</td>
                                    <td>{{ $idx->barcode ?? $idx->book->barcode ?? 'N/A' }}</td>
                                    <td>{{ $idx->nbs_barcode ?? $idx->book->nbs_barcode ?? 'N/A' }}</td>
                                    <td>{{ $idx->book->name ?? 'N/A' }}</td>
                                    <td><span class="badge badge-outline-primary">{{ $idx->index_value }}</span></td>
                                    <td><strong class="text-success">{{ $idx->display_name }}</strong></td>
                                    <td><span class="badge badge-light">{{ $idx->main_stock }}</span></td>
                                    <td><strong class="text-dark">₱{{ number_format(($idx->price && $idx->price > 0) ? $idx->price : ($idx->book?->price ?? 0), 2) }}</strong></td>
                                    <td>{{ $idx->mibf_price !== null ? '₱' . number_format($idx->mibf_price, 2) : '-' }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1 edit-index-btn" 
                                               data-id="{{ $idx->id }}"><i class="fas fa-pencil-alt"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-danger shadow btn-xs sharp delete-index-btn"
                                               data-id="{{ $idx->id }}"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center">No book indices mapped.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                        <div class="text-muted small">
                            Showing {{ $indices->firstItem() ?? 0 }} to {{ $indices->lastItem() ?? 0 }} of {{ $indices->total() }} entries
                        </div>
                        <div>
                            {{ $indices->appends(['search' => request('search')])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
    <!-- Add/Edit Index Modal -->
    <div class="modal" id="addIndexModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <form id="indexForm">
                    @csrf
                    <input type="hidden" name="index_id" id="modal_index_id">
                    <div class="modal-header" style="background: #D9251C; color: #fff;">
                        <h5 class="modal-title text-white" id="addIndexModalTitle">Add Book Index Mapping</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">SELECT BOOK <span class="text-danger">*</span></label>
                            <select name="book_id" id="index_book_id" class="form-select form-control" required style="width: 100%;">
                                <option value="">Select Book...</option>
                                @foreach($allBooks as $book)
                                    <option value="{{ $book->id }}" 
                                            data-article="{{ $book->article }}"
                                            data-barcode="{{ $book->barcode }}"
                                            data-nbs_barcode="{{ $book->nbs_barcode }}">
                                        {{ $book->name }} (SKU: #{{ $book->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">INDEX VALUE / SUFFIX <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="index_value" id="index_value_field" required placeholder="e.g. Index 1, Vol 2, Part A">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">CUSTOM RESULTING NAME (Optional)</label>
                            <input type="text" class="form-control" name="custom_name" id="index_custom_name_field" placeholder="e.g. Mang Inasal Index 2">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">ARTICLE</label>
                            <input type="text" class="form-control" name="article" id="index_article_field" placeholder="e.g. ART-001">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">BARCODE</label>
                            <input type="text" class="form-control" name="barcode" id="index_barcode_field" placeholder="e.g. 9780764814754">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">NBS BARCODE</label>
                            <input type="text" class="form-control" name="nbs_barcode" id="index_nbs_barcode_field" placeholder="e.g. NBS-12345">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">STOCK <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="stock" id="index_stock_field" required min="0" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">PRICE (₱)</label>
                            <input type="number" step="0.01" class="form-control" name="price" id="index_price_field" min="0" value="0.00" placeholder="0.00">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">MIBF PRICE (₱)</label>
                            <input type="number" step="0.01" class="form-control" name="mibf_price" id="index_mibf_price_field" min="0" placeholder="Leave empty for default price">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="saveIndexBtn" style="background: #D9251C; border-color: #D9251C;">Save Mapping</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Index Confirmation Modal -->
    <div class="modal" id="deleteIndexModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white"><i class="fas fa-trash me-2"></i>Confirm Delete Mapping</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this book index mapping? Original book entry name will remain unmodified.</p>
                    <input type="hidden" id="delete_index_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteIndexBtn">Delete Mapping</button>
                </div>
            </div>
        </div>
    </div>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        function showCustomModal(id) {
            ['addIndexModal', 'deleteIndexModal'].forEach(function(mid) {
                var m = document.getElementById(mid);
                if (m) { m.style.display = 'none'; m.removeAttribute('aria-modal'); m.setAttribute('aria-hidden', 'true'); }
            });
            document.querySelectorAll('.modal-backdrop').forEach(function(b) { b.remove(); });
            document.body.classList.remove('modal-open');

            var el = document.getElementById(id);
            if (!el) return;

            if (el.parentNode !== document.body) {
                document.body.appendChild(el);
            }

            var backdrop = document.createElement('div');
            backdrop.id = 'custom-backdrop';
            backdrop.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1050;';
            document.body.appendChild(backdrop);
            document.body.classList.add('modal-open');

            el.style.cssText = 'display:block !important; position:fixed !important; top:0; left:0; width:100%; height:100%; overflow-x:hidden; overflow-y:auto; z-index:1060;';
            el.removeAttribute('aria-hidden');
            el.setAttribute('aria-modal', 'true');
        }

        function hideCustomModal(id) {
            var el = document.getElementById(id);
            if (el) {
                el.style.display = 'none';
                el.setAttribute('aria-hidden', 'true');
                el.removeAttribute('aria-modal');
            }
            var bd = document.getElementById('custom-backdrop');
            if (bd) bd.remove();
            document.body.classList.remove('modal-open');
        }

        let isEditingIndexMode = false;

        $(document).ready(function() {
            if (window.jQuery && typeof jQuery.fn.select2 === 'function') {
                $('#index_book_id').select2({
                    dropdownParent: $('#addIndexModal'),
                    placeholder: 'Select Book...',
                    allowClear: true,
                    width: '100%'
                });

                $('#index_book_id').on('change', function() {
                    const selectedOpt = $(this).find('option:selected');
                    if (selectedOpt.val() && !isEditingIndexMode) {
                        const article = selectedOpt.data('article') || '';
                        const barcode = selectedOpt.data('barcode') || '';
                        const nbsBarcode = selectedOpt.data('nbs_barcode') || '';

                        document.getElementById('index_article_field').value = article;
                        document.getElementById('index_barcode_field').value = barcode;
                        document.getElementById('index_nbs_barcode_field').value = nbsBarcode;
                    }
                });
            }
        });

        const addNewIndexBtn = document.getElementById('addNewIndexBtn');
        if (addNewIndexBtn) {
            addNewIndexBtn.addEventListener('click', function(e) {
                e.preventDefault();
                isEditingIndexMode = false;
                document.getElementById('modal_index_id').value = '';
                document.getElementById('indexForm').reset();
                $('#index_book_id').val('').trigger('change');
                document.getElementById('addIndexModalTitle').innerText = 'Add Book Index Mapping';
                showCustomModal('addIndexModal');
            });
        }

        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var modal = this.closest('.modal');
                if (modal) hideCustomModal(modal.id);
            });
        });

        const indexForm = document.getElementById('indexForm');
        if (indexForm) {
            indexForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const saveBtn = document.getElementById('saveIndexBtn');
                saveBtn.disabled = true;

                const formData = new FormData(this);
                const payload = {
                    book_id: formData.get('book_id'),
                    index_value: formData.get('index_value'),
                    custom_name: formData.get('custom_name'),
                    article: formData.get('article'),
                    barcode: formData.get('barcode'),
                    nbs_barcode: formData.get('nbs_barcode'),
                    stock: formData.get('stock'),
                    price: formData.get('price') || 0,
                    mibf_price: formData.get('mibf_price') || null
                };

                const indexId = document.getElementById('modal_index_id').value;
                const url = indexId ? `/marketing/book-indices/${indexId}/update` : "{{ route('marketing.indices.store') }}";

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
                    hideCustomModal('addIndexModal');
                    setTimeout(() => location.reload(), 1500);
                })
                .catch(err => {
                    console.error('Save Mapping Error:', err);
                    let msg = 'Failed to save mapping.';
                    if (err.errors) msg = Object.values(err.errors).flat().join(' ');
                    else if (err.message) msg = err.message;
                    window.showAlert(msg, 'danger');
                    saveBtn.disabled = false;
                });
            });
        }

        document.querySelectorAll('.edit-index-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch(`/marketing/book-indices/${id}/edit`)
                    .then(response => response.json())
                    .then(data => {
                        isEditingIndexMode = true;
                        document.getElementById('modal_index_id').value = data.id;
                        $('#index_book_id').val(data.book_id).trigger('change');
                        document.getElementById('index_value_field').value = data.index_value;
                        document.getElementById('index_custom_name_field').value = data.custom_name || '';
                        document.getElementById('index_article_field').value = data.article || '';
                        document.getElementById('index_barcode_field').value = data.barcode || '';
                        document.getElementById('index_nbs_barcode_field').value = data.nbs_barcode || '';
                        document.getElementById('index_stock_field').value = data.stock ?? 0;
                        document.getElementById('index_price_field').value = data.price ?? '0.00';
                        document.getElementById('index_mibf_price_field').value = data.mibf_price !== null ? data.mibf_price : '';
                        document.getElementById('addIndexModalTitle').innerText = 'Edit Book Index Mapping';
                        showCustomModal('addIndexModal');
                    });
            });
        });

        document.querySelectorAll('.delete-index-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('delete_index_id').value = this.dataset.id;
                showCustomModal('deleteIndexModal');
            });
        });

        // Confirm Delete Mapping
        const confirmDeleteIndexBtn = document.getElementById('confirmDeleteIndexBtn');
        if (confirmDeleteIndexBtn) {
            confirmDeleteIndexBtn.addEventListener('click', function() {
                const id = document.getElementById('delete_index_id').value;
                this.disabled = true;

                fetch(`/marketing/book-indices/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideCustomModal('deleteIndexModal');
                    window.showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                })
                .catch(err => {
                    console.error('Delete Index Error:', err);
                    window.showAlert('Failed to delete mapping.', 'danger');
                    this.disabled = false;
                });
            });
        }
    </script>
    @endpush
</x-app-layout>
