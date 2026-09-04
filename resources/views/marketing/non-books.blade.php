<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
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

        /* Modern Tabbed Form Styles */
        .book-modal-header-info {
            padding: 1rem 1.5rem;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .book-tab-container {
            display: flex;
            min-height: 520px;
        }

        .book-nav-tabs {
            width: 200px;
            border-right: 1px solid #dee2e6;
            background: #f1f1f1;
            padding-top: 1rem;
        }

        .book-nav-tabs .nav-link {
            border: none;
            border-radius: 0;
            text-align: left;
            padding: 12px 15px;
            color: #444;
            font-weight: 500;
            font-size: 0.85rem;
            border-left: 4px solid transparent;
            transition: all 0.2s;
        }

        .book-nav-tabs .nav-link:hover {
            background: #e9e9e9;
        }

        .book-nav-tabs .nav-link.active {
            background: #fff;
            color: #ff0000;
            border-left-color: #ff0000;
            margin-right: -1px;
            font-weight: 700;
        }

        .book-tab-content {
            flex: 1;
            padding: 1.5rem;
            background: #fff;
            overflow-y: auto;
            max-height: 600px;
        }

        .form-row-custom {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            gap: 1rem;
        }

        .form-row-custom>label {
            width: 140px;
            text-align: right;
            font-size: 0.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0;
            text-transform: uppercase;
        }

        .form-row-custom .form-control-sm,
        .form-row-custom .form-select-sm {
            flex: 1;
            border-radius: 2px;
            border: 1px solid #ced4da;
        }

        .section-divider {
            border-bottom: 2px solid #f1f1f1;
            margin: 1.5rem 0 1rem;
            font-weight: 800;
            font-size: 0.8rem;
            color: #2c3e50;
            text-transform: uppercase;
            padding-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-divider::after {
            content: "";
            height: 1px;
            background: #eee;
            flex: 1;
        }

        /* Import Excel Button Styling */
        .btn-import-excel {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
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

        .btn-import-excel:hover {
            background-color: #218838;
            box-shadow: 0 6px 8px rgba(40, 167, 69, 0.3);
            transform: translateY(-1px);
            color: #fff;
        }

        .btn-import-excel i {
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
        }

        /* Manage Categories Button & Modal */
        .btn-manage-cat {
            background-color: #ff0000;
            border-color: #ff0000;
            color: #fff;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(255, 0, 0, 0.2);
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

        .btn-manage-cat:hover {
            background-color: #e60000;
            box-shadow: 0 6px 8px rgba(255, 0, 0, 0.3);
            transform: translateY(-1px);
            color: #fff;
        }

        .btn-manage-cat i {
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
        }

        #manageCategoriesModal .nav-tabs {
            border-bottom: 2px solid #eee;
        }

        #manageCategoriesModal .nav-link {
            font-size: 0.85rem;
            font-weight: 600;
            color: #666;
            border: none;
            padding: 10px 20px;
        }

        #manageCategoriesModal .nav-link.active {
            color: #ff0000;
            border-bottom: 2px solid #ff0000;
            background: transparent;
        }

        .accordion-danger-solid .accordion-header {
            background-color: #f8f9fa;
            border: 1px solid #eee;
        }

        /* Fix for red border cutoff on invalid fields */
        .form-row-custom .is-invalid {
            border-right: 1px solid #dc3545 !important;
            margin-right: 2px;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <!-- Navigation Tabs -->
            <ul class="nav page-tabs" id="bookMgmtTabs">
                <li class="nav-item">
                    <a class="nav-link" id="book-list-tab" href="{{ route('marketing.products') }}">
                        <i class="las la-book" style="font-size: 1.25rem;"></i>
                        <span>Book List</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" id="non-books-tab" href="{{ route('marketing.non-books') }}">
                        <i class="las la-list" style="font-size: 1.25rem;"></i>
                        <span>Non-Books</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="book-index-tab" href="{{ route('marketing.indices') }}">
                        <i class="las la-tag" style="font-size: 1.25rem;"></i>
                        <span>Book Index</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="book-bundle-tab" href="{{ route('marketing.bundles') }}">
                        <i class="las la-boxes" style="font-size: 1.25rem;"></i>
                        <span>Book Bundle</span>
                    </a>
                </li>
            </ul>

            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h4 class="card-title mb-0">Non-Books List (Master)</h4>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-3 mt-sm-0">
                        <!-- Search Form -->
                        <form action="{{ route('marketing.non-books') }}" method="GET" class="d-flex align-items-center gap-2">
                            <div style="width: 250px; height: 38px; display: flex; align-items: center; border: 1px solid #ced4da; border-radius: 4px; background-color: #f8f9fa; padding: 0 12px; box-sizing: border-box;">
                                <span class="las la-search text-muted me-2" style="font-size: 1.1rem; line-height: 1;"></span>
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search items..." value="{{ request('search') }}" 
                                       style="border: none !important; background: transparent !important; padding: 0 !important; height: 100%; font-size: 0.85rem; color: #333; outline: none !important; box-shadow: none !important;">
                                @if(request('search'))
                                    <a href="{{ route('marketing.non-books') }}" class="text-muted d-inline-flex align-items-center justify-content-center ms-2" title="Clear search" style="text-decoration: none;">
                                        <span class="las la-times-circle" style="color: #999; font-size: 1.25rem; cursor: pointer;"></span>
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-danger text-white rounded d-inline-flex align-items-center justify-content-center gap-2" style="height: 38px; padding: 0 1.2rem; border: none; font-size: 0.85rem; font-weight: 500; background-color: #D9251C; box-shadow: 0 4px 6px rgba(217, 37, 28, 0.15);">
                                <span class="las la-search" style="font-size: 1rem; color: #fff;"></span>
                                <span>Search</span>
                            </button>
                        </form>

                        <a href="javascript:void(0);" class="btn btn-import-excel rounded"
                            data-bs-toggle="modal" data-bs-target="#importNonBooksModal">
                            <i class="las la-file-excel"></i>
                            <span>Import Excel</span>
                        </a>
                        <a href="javascript:void(0);" class="btn btn-manage-cat rounded"
                            data-bs-toggle="modal" data-bs-target="#manageCategoriesModal">
                            <i class="las la-cog"></i>
                            <span>Manage Categories</span>
                        </a>
                        <a href="javascript:void(0);"
                            class="btn btn-primary rounded d-flex align-items-center" data-bs-toggle="modal"
                            data-bs-target="#addNonBookModal"
                            style="gap: 0.5rem; padding: 0.5rem 1rem; height: 38px; min-height: 38px; line-height: 1.5; box-sizing: border-box; border: none; background: #ff0000; color: #ffffff; font-weight: 500;">
                            <i class="las la-plus" style="font-size: 1rem;"></i>
                            <span>Add New Non-Book</span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th>Cover</th>
                                    <th>SKU</th>
                                    <th>Item Name</th>
                                    <th>Selling Price</th>
                                    <th>MIBF Price</th>
                                    <th>Cost</th>
                                    <th>Stock</th>
                                    <th>Classification</th>
                                    <th>POS Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($books as $book)
                                <tr>
                                    <td>
                                        <img src="{{ $book->image ? '/storage/' . $book->image : asset('images/no-book-cover.svg') }}" 
                                             class="rounded-circle" width="35" height="35" style="object-fit: cover; border: 1px solid #eee;">
                                    </td>
                                    <td><strong>#{{ $book->sku }}</strong></td>
                                    <td>{{ $book->name }}</td>
                                    <td>₱{{ number_format($book->price, 2) }}</td>
                                    <td>{{ $book->mibf_price !== null ? '₱' . number_format($book->mibf_price, 2) : '-' }}</td>
                                    <td>₱{{ number_format($book->cost, 2) }}</td>
                                    <td>
                                        @if($book->stock > 0)
                                            <span class="badge badge-success">{{ $book->stock }} {{ $book->unit ?? 'pcs' }}</span>
                                        @elseif($book->stock == 0)
                                            <span class="badge badge-warning">0 {{ $book->unit ?? 'pcs' }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ $book->stock }} {{ $book->unit ?? 'pcs' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-outline-primary">{{ $book->book_type ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        @if($book->is_active)
                                            <span class="badge badge-success">Active on POS</span>
                                        @else
                                            <span class="badge badge-light">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="javascript:void(0);" class="btn btn-secondary shadow btn-xs sharp me-1 view-book-btn" 
                                               data-id="{{ $book->id }}"><i class="far fa-eye"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1 edit-book-btn" 
                                               data-id="{{ $book->id }}"><i class="fas fa-pencil-alt"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-danger shadow btn-xs sharp delete-book-btn"
                                               data-id="{{ $book->id }}"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">No non-books in the list.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                        <div class="text-muted small">
                            Showing {{ $books->firstItem() ?? 0 }} to {{ $books->lastItem() ?? 0 }} of {{ $books->total() }} entries
                        </div>
                        <div>
                            {{ $books->appends(['search' => request('search')])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')

    <!-- Import Non-Books Modal -->
    <div class="modal fade" id="importNonBooksModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="importNonBooksForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header" style="background: #28a745; color: #fff;">
                        <h5 class="modal-title text-white"><i class="las la-file-excel me-2" style="font-size: 1.25rem;"></i>Import Non-Books from Excel</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Instructions:</strong>
                            <ul class="mb-0 ps-3 small">
                                <li>`SKU` and `Item Name` columns are required for every row.</li>
                                <li>Duplicate SKUs are not allowed. If a SKU already exists, the import will be blocked and none of the changes will be saved.</li>
                                <li>If a Category or Sub-category does not exist, it will be automatically created.</li>
                            </ul>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4 p-2 bg-light rounded border">
                            <span class="small text-muted fw-bold">Need a template?</span>
                            <a href="{{ route('marketing.non-books.import-template') }}" class="btn btn-sm btn-outline-success">
                                <i class="las la-download"></i> Download Template
                            </a>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">SELECT EXCEL/CSV FILE</label>
                            <input type="file" class="form-control form-control-sm" name="excel_file" accept=".xlsx,.xls,.csv" required>
                        </div>
                        <div id="importErrorsContainer" class="d-none mt-3">
                            <div class="alert alert-danger p-2 mb-0">
                                <strong class="small">Import failed due to the following errors:</strong>
                                <ul id="importErrorList" class="mb-0 ps-3 small text-danger" style="max-height: 150px; overflow-y: auto;"></ul>
                            </div>
                        </div>
                        <div id="importLoading" class="d-none text-center my-3">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="small text-muted mt-2 mb-0">Processing import... Please wait as this can take a moment for large files.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success btn-sm" id="importSubmitBtn">
                            <i class="las la-upload"></i> Start Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Manage Categories Modal -->
    <div class="modal fade" id="manageCategoriesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Categories</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs px-3 pt-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#create-cat-tab">Add Category</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#create-sub-tab">Add Sub-category</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#view-cats-tab">View All</a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content p-4">
                        <div id="create-cat-tab" class="tab-pane active">
                            <h6 class="fw-bold mb-3">Create New Master Category</h6>
                            <form id="addCategoryFormOnly">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">CATEGORY NAME</label>
                                    <input type="text" class="form-control form-control-sm" name="name" required placeholder="e.g., Stationery, Gifts">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Create Category</button>
                            </form>
                        </div>
                        <div id="create-sub-tab" class="tab-pane fade">
                            <h6 class="fw-bold mb-3">Create New Sub-category</h6>
                            <form id="addSubCategoryForm">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">PARENT CATEGORY</label>
                                    <select class="form-select form-select-sm" name="parent_id" required>
                                        <option value="" disabled selected>Select Parent Category...</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">SUB-CATEGORY NAME</label>
                                    <input type="text" class="form-control form-control-sm" name="name" required placeholder="e.g., Notebooks, Pens">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Create Sub-category</button>
                            </form>
                        </div>
                        <div id="view-cats-tab" class="tab-pane fade">
                            <h6 class="fw-bold mb-3">Current Hierarchy</h6>
                            <div id="categoriesList" style="max-height: 400px; overflow-y: auto;">
                                <div class="accordion accordion-sm accordion-danger-solid" id="accordionCategories">
                                    @foreach($categories as $cat)
                                        <div class="accordion-item">
                                            <div class="accordion-header d-flex align-items-center justify-content-between pe-3">
                                                <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCat{{ $cat->id }}">
                                                    {{ $cat->name }}
                                                </button>
                                                <a href="javascript:void(0);" class="text-danger delete-category-btn" data-id="{{ $cat->id }}" title="Delete Category">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                            <div id="collapseCat{{ $cat->id }}" class="accordion-collapse collapse" data-bs-parent="#accordionCategories">
                                                <div class="accordion-body py-2">
                                                    <ul class="list-group list-group-flush mb-0">
                                                        @forelse($cat->children as $sub)
                                                            <li class="list-group-item d-flex justify-content-between align-items-center py-1 border-0 ps-3">
                                                                <span class="small text-muted">— {{ $sub->name }}</span>
                                                                <a href="javascript:void(0);" class="text-danger delete-category-btn" data-id="{{ $sub->id }}" title="Delete Sub-category">
                                                                    <i class="fas fa-times-circle"></i>
                                                                </a>
                                                            </li>
                                                        @empty
                                                            <li class="list-group-item py-1 border-0 ps-3 text-muted small">No sub-categories</li>
                                                        @endforelse
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Book Confirmation Modal -->
    <div class="modal fade" id="deleteBookModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to permanently delete this item from the Master Registry?</p>
                    <p class="text-danger small fw-bold">This action cannot be undone and will free up the SKU.</p>
                    <input type="hidden" id="delete_book_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete Permanently</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Category Modal -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Confirm Category Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this category? This will also delete all associated sub-categories.</p>
                    <p class="text-danger small fw-bold">This action cannot be undone.</p>
                    <input type="hidden" id="delete_cat_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteCategoryBtn">Delete Permanently</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Master Non-Book Modal -->
    <div class="modal fade" id="addNonBookModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form id="addNonBookForm" class="modal-content" novalidate>
                @csrf
                <input type="hidden" name="book_id" id="modal_book_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNonBookModalTitle">Non-Book Details</h5>
                    <button type="submit" class="btn btn-primary btn-sm mx-3" id="saveNonBookBtn">Save Non-Book</button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="book-modal-header-info">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-row-custom">
                                <label>ITEM NAME</label>
                                <input type="text" class="form-control form-control-sm" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-row-custom">
                                <label>SKU / CATALOG #</label>
                                <div style="flex: 1;">
                                    <input type="text" class="form-control form-control-sm w-100" name="sku" id="book_sku_input" required>
                                    <div id="sku-validation-msg" class="text-danger small fw-bold mt-1" style="display: none; font-size: 0.75rem;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-body p-0">
                    <div class="book-tab-container">
                        <div class="nav flex-column nav-pills book-nav-tabs" role="tablist">
                            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#book-general" type="button" role="tab">General</button>
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#book-metadata" type="button" role="tab">Physical Specs</button>
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#book-inventory" type="button" role="tab">Inventory Settings</button>
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#book-extended" type="button" role="tab">Extended Info</button>
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#book-cover" type="button" role="tab">Cover Image</button>
                        </div>

                        <div class="tab-content book-tab-content">
                            <div class="tab-pane fade" id="book-cover" role="tabpanel">
                                <div class="section-divider mt-0">COVER PREVIEW</div>
                                <div class="text-center p-4 border rounded bg-light">
                                    <img id="book_image_preview" src="" class="img-fluid rounded shadow-sm mb-3 d-none" style="max-height: 300px; max-width: 100%; object-fit: contain;">
                                    <div class="mt-2">
                                        <label for="cover_image_input" id="choose_image_btn" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-camera me-2"></i>Choose Image
                                        </label>
                                        <input type="file" id="cover_image_input" class="d-none" name="image" accept="image/*" onchange="previewBookImage(this)">
                                        <p class="text-muted small mt-2">Recommended size: 600x900px. Max 2MB.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade show active" id="book-general" role="tabpanel">
                                <div class="section-divider mt-0">IDENTIFICATION</div>
                                <div class="form-row-custom">
                                    <label>ITEM CODE (Auto)</label>
                                    <input type="text" class="form-control form-control-sm" name="item_code" readonly placeholder="Auto-generated">
                                </div>
                                <div class="form-row-custom">
                                    <label>BARCODE</label>
                                    <input type="text" class="form-control form-control-sm" name="barcode">
                                </div>
                                <div class="form-row-custom">
                                    <label>SELLING PRICE</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" name="price" step="0.01">
                                    </div>
                                </div>
                                <div class="form-row-custom">
                                    <label>MIBF PRICE</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" name="mibf_price" step="0.01" min="0" placeholder="Optional">
                                    </div>
                                </div>
                                <div class="form-row-custom">
                                    <label>DOLLAR PRICE</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">$</span>
                                        <input type="text" class="form-control" id="formDollarPrice" readonly placeholder="Auto-calculated">
                                    </div>
                                </div>
                                <div class="form-row-custom">
                                    <label>POS STATUS</label>
                                    <div class="form-check custom-checkbox check-xs mb-0">
                                        <input type="checkbox" class="form-check-input" name="is_active" id="bookActive" value="1" checked>
                                        <label class="form-check-label small" for="bookActive">Active on POS</label>
                                    </div>
                                </div>
                                
                                <div class="section-divider">MANUFACTURER / BRAND</div>
                                <div class="form-row-custom">
                                    <label>BRAND/AUTHOR</label>
                                    <input type="text" class="form-control form-control-sm" name="author">
                                </div>
                                <div class="form-row-custom">
                                    <label>PUBLISHER/SUPPLIER</label>
                                    <input type="text" class="form-control form-control-sm" name="publisher">
                                </div>
                            </div>

                            <div class="tab-pane fade" id="book-metadata" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-row-custom">
                                            <label>SIZE (LXW)</label>
                                            <input type="text" class="form-control form-control-sm" name="size">
                                        </div>
                                        <div class="form-row-custom">
                                            <label>WEIGHT</label>
                                            <input type="text" class="form-control form-control-sm" name="weight">
                                        </div>
                                        <div class="form-row-custom">
                                            <label>PAGES / QTY IN SET</label>
                                            <input type="number" class="form-control form-control-sm" name="pages">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-row-custom">
                                            <label>MATERIAL TYPE</label>
                                            <input type="text" class="form-control form-control-sm" name="cover_type">
                                        </div>
                                        <div class="form-row-custom">
                                            <label>CLASSIFICATION</label>
                                            <select class="form-select form-select-sm" name="book_type">
                                                <option value="">Select Classification</option>
                                                <option value="Local">Local Item</option>
                                                <option value="Foreign">Foreign Item</option>
                                                <option value="Consignment">Consignment</option>
                                            </select>
                                        </div>
                                        <div class="form-row-custom">
                                            <label>COPYRIGHT / MODEL YEAR</label>
                                            <input type="text" class="form-control form-control-sm" name="copyright">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="book-inventory" role="tabpanel">
                                <div class="section-divider mt-0">COSTING</div>
                                <div class="form-row-custom">
                                    <label>UNIT COST</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" name="cost" step="0.01">
                                    </div>
                                </div>
                                <div class="form-row-custom">
                                    <label>COGS ACCOUNT</label>
                                    <select class="form-select form-select-sm" name="cogs_account">
                                        <option value="Cost of Sales">Cost of Sales</option>
                                        <option value="Inventory Asset">Inventory Asset</option>
                                    </select>
                                </div>
                                <div class="section-divider">THRESHOLDS</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-row-custom">
                                            <label>MIN STOCK</label>
                                            <input type="number" class="form-control form-control-sm" name="reorder_point">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-row-custom">
                                            <label>MAX STOCK</label>
                                            <input type="number" class="form-control form-control-sm" name="max_stock">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="section-divider">LOCATION (WAREHOUSE)</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-row-custom">
                                            <label>SHELF NUMBER</label>
                                            <input type="text" class="form-control form-control-sm" name="shelf_number">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-row-custom">
                                            <label>RACK NUMBER</label>
                                            <input type="text" class="form-control form-control-sm" name="rack_number">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="book-extended" role="tabpanel">
                                <div class="section-divider mt-0">TAXONOMY & ATTRIBUTES</div>
                                <div class="form-row-custom">
                                    <label>CATEGORY</label>
                                    <select class="form-select form-select-sm" name="category_id" id="book_category_id">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-row-custom">
                                    <label>SUB-CATEGORY</label>
                                    <select class="form-select form-select-sm" name="sub_category_id" id="book_sub_category_id">
                                        <option value="">Select Sub-category</option>
                                    </select>
                                </div>
                                <div class="form-row-custom">
                                    <label>ARTICLE CODE</label>
                                    <input type="text" class="form-control form-control-sm" name="article">
                                </div>
                                <div class="form-row-custom">
                                    <label>ROYALTY</label>
                                    <input type="text" class="form-control form-control-sm" name="royalty">
                                </div>
                                <div class="form-row-custom">
                                    <label>EMAIL</label>
                                    <input type="email" class="form-control form-control-sm" name="email">
                                </div>
                                <div class="form-row-custom">
                                    <label>CONTACT #</label>
                                    <input type="text" class="form-control form-control-sm" name="contact_number">
                                </div>
                                <div class="form-row-custom">
                                    <label>PURCHASE DESC</label>
                                    <textarea class="form-control form-control-sm" name="purchase_description" rows="3"></textarea>
                                </div>
                                <div class="form-row-custom">
                                    <label>NBS BARCODE</label>
                                    <input type="text" class="form-control form-control-sm" name="nbs_barcode">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Validation Errors Modal -->
    <div class="modal fade" id="validationErrorsModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: #D9251C; color: #fff;">
                    <h5 class="modal-title text-white"><i class="fas fa-exclamation-triangle me-2"></i>Validation Errors</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="fw-bold">The following errors were found:</p>
                    <ul id="modalErrorList" class="text-danger mb-0"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="goToFirstError()">Fix Errors</button>
                </div>
            </div>
        </div>
    </div>

    @endpush

    @push('scripts')
    <script>
        let nonBookModal, deleteModal, validationErrorsModal;
        try {
            if (window.bootstrap && bootstrap.Modal) {
                nonBookModal = new bootstrap.Modal(document.getElementById('addNonBookModal'));
                deleteModal = new bootstrap.Modal(document.getElementById('deleteBookModal'));
                validationErrorsModal = new bootstrap.Modal(document.getElementById('validationErrorsModal'));
            }
        } catch (e) {
            console.warn("Bootstrap Modal JS not available, falling back to manual/jQuery methods", e);
        }

        function safeModal(modalObj, elementId, action = 'show') {
            try {
                if (modalObj && typeof modalObj[action] === 'function') {
                    modalObj[action]();
                    return true;
                }
                if (window.jQuery && typeof $(elementId).modal === 'function') {
                    $(elementId).modal(action);
                    return true;
                }
                const el = document.getElementById(elementId);
                if (el) {
                    if (action === 'show') el.classList.add('show'), el.style.display = 'block';
                    else el.classList.remove('show'), el.style.display = 'none';
                    return true;
                }
            } catch (e) {
                console.error(`Failed to ${action} modal ${elementId}`, e);
            }
            return false;
        }

        const nonBookForm = document.getElementById('addNonBookForm');
        let isFixingErrors = false;

        const skuInput = document.getElementById('book_sku_input');
        const skuMsg = document.getElementById('sku-validation-msg');
        const saveBookBtn = document.getElementById('saveNonBookBtn');
        let skuTimeout = null;

        if (skuInput) {
            skuInput.addEventListener('input', function() {
                clearTimeout(skuTimeout);
                const skuVal = this.value.trim();
                const bookId = document.getElementById('modal_book_id').value;

                if (skuVal === '') {
                    skuInput.classList.remove('is-invalid');
                    if (skuMsg) {
                        skuMsg.style.display = 'none';
                        skuMsg.innerText = '';
                    }
                    if (skuInput) skuInput.dataset.skuExists = "false";
                    checkSaveButtonState();
                    return;
                }

                skuTimeout = setTimeout(() => {
                    let url = `/marketing/book-list/check-sku?sku=${encodeURIComponent(skuVal)}`;
                    if (bookId) {
                        url += `&exclude_id=${bookId}`;
                    }

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                skuInput.classList.add('is-invalid');
                                if (skuMsg) {
                                    skuMsg.innerText = 'This SKU is already taken by another item.';
                                    skuMsg.style.display = 'block';
                                }
                                skuInput.dataset.skuExists = "true";
                            } else {
                                skuInput.classList.remove('is-invalid');
                                if (skuMsg) {
                                    skuMsg.style.display = 'none';
                                    skuMsg.innerText = '';
                                }
                                skuInput.dataset.skuExists = "false";
                            }
                            checkSaveButtonState();
                        })
                        .catch(err => {
                            console.error('SKU validation error:', err);
                        });
                }, 300);
            });
        }

        function checkSaveButtonState() {
            if (skuInput && skuInput.dataset.skuExists === "true") {
                if (saveBookBtn) saveBookBtn.disabled = true;
            } else {
                if (saveBookBtn) saveBookBtn.disabled = false;
            }
        }

        function calculateDollarPrice() {
            const pesoInput = nonBookForm.querySelector('input[name="price"]');
            const dollarInput = document.getElementById('formDollarPrice');
            if (pesoInput && dollarInput) {
                const pesoValue = parseFloat(pesoInput.value) || 0;
                if (pesoValue <= 0) {
                    dollarInput.value = '';
                    return;
                }
                const dollarBase = pesoValue / 40;
                const dollarSRP = dollarBase * 1.10;
                const roundedDollar = Math.ceil(dollarSRP * 4) / 4;
                dollarInput.value = roundedDollar.toFixed(2);
            }
        }

        const pesoInputEl = nonBookForm.querySelector('input[name="price"]');
        if (pesoInputEl) {
            pesoInputEl.addEventListener('input', calculateDollarPrice);
        }

        nonBookForm.addEventListener('reset', function() {
            const dollarInput = document.getElementById('formDollarPrice');
            if (dollarInput) dollarInput.value = '';
        });

        function previewBookImage(input) {
            const preview = document.getElementById('book_image_preview');
            if (input.files && input.files[0]) {
                preview.src = window.URL.createObjectURL(input.files[0]);
                preview.classList.remove('d-none');
            } else {
                preview.src = "{{ asset('images/no-book-cover.svg') }}";
            }
        }

        function populateBookModal(data, isReadOnly = false) {
            document.getElementById('modal_book_id').value = data.id;
            nonBookForm.querySelector('[name="name"]').value = data.name;
            nonBookForm.querySelector('[name="sku"]').value = data.sku;
            nonBookForm.querySelector('[name="item_code"]').value = data.item_code || '';
            nonBookForm.querySelector('[name="category_id"]').value = data.category_id || '';
            
            const subCatSelect = document.getElementById('book_sub_category_id');
            subCatSelect.innerHTML = '<option value="">Select Sub-category</option>';
            
            if (data.category_id) {
                fetch(`/marketing/book-categories/${data.category_id}/subcategories`)
                    .then(response => response.json())
                    .then(subcats => {
                        subcats.forEach(sub => {
                            const opt = document.createElement('option');
                            opt.value = sub.id;
                            opt.textContent = sub.name;
                            if (sub.id == data.sub_category_id) opt.selected = true;
                            subCatSelect.appendChild(opt);
                        });
                    });
            }
            
            nonBookForm.querySelector('[name="barcode"]').value = data.barcode || '';
            nonBookForm.querySelector('[name="nbs_barcode"]').value = data.nbs_barcode || '';
            nonBookForm.querySelector('[name="article"]').value = data.article || '';
            nonBookForm.querySelector('[name="author"]').value = data.author || '';
            nonBookForm.querySelector('[name="publisher"]').value = data.publisher || '';
            nonBookForm.querySelector('[name="size"]').value = data.size || '';
            nonBookForm.querySelector('[name="pages"]').value = data.pages || '';
            nonBookForm.querySelector('[name="cover_type"]').value = data.cover_type || '';
            nonBookForm.querySelector('[name="book_type"]').value = data.book_type || '';
            nonBookForm.querySelector('[name="copyright"]').value = data.copyright || '';
            nonBookForm.querySelector('[name="weight"]').value = data.weight || '';
            nonBookForm.querySelector('[name="cost"]').value = data.cost;
            nonBookForm.querySelector('[name="price"]').value = data.price;
            const mibfEl = nonBookForm.querySelector('[name="mibf_price"]');
            if (mibfEl) mibfEl.value = (data.mibf_price !== null && data.mibf_price !== undefined) ? data.mibf_price : '';
            calculateDollarPrice();
            nonBookForm.querySelector('[name="cogs_account"]').value = data.cogs_account || '';
            nonBookForm.querySelector('[name="reorder_point"]').value = data.reorder_point;
            nonBookForm.querySelector('[name="max_stock"]').value = data.max_stock;
            nonBookForm.querySelector('[name="shelf_number"]').value = data.shelf_number || '';
            nonBookForm.querySelector('[name="rack_number"]').value = data.rack_number || '';
            nonBookForm.querySelector('[name="royalty"]').value = data.royalty || '';
            nonBookForm.querySelector('[name="email"]').value = data.email || '';
            nonBookForm.querySelector('[name="contact_number"]').value = data.contact_number || '';
            nonBookForm.querySelector('[name="purchase_description"]').value = data.purchase_description || '';
            nonBookForm.querySelector('[name="is_active"]').checked = data.is_active ? true : false;

            const preview = document.getElementById('book_image_preview');
            preview.classList.remove('d-none');
            if (data.image) {
                preview.src = "/storage/" + data.image;
            } else {
                preview.src = "{{ asset('images/no-book-cover.svg') }}";
            }

            // Mode Logic
            const titleEl = document.getElementById('addNonBookModalTitle');
            const saveBtn = document.getElementById('saveNonBookBtn');
            const inputs = nonBookForm.querySelectorAll('input, select, textarea');

            if (isReadOnly) {
                titleEl.innerText = "View Non-Book Details";
                saveBtn.classList.add('d-none');
                document.getElementById('choose_image_btn').classList.add('d-none');
                inputs.forEach(input => {
                    if (input.type !== 'hidden') {
                        input.disabled = true;
                    }
                });
            } else {
                titleEl.innerText = data.id ? "Edit Non-Book Entry" : "Add New Non-Book to Master Registry";
                saveBtn.classList.remove('d-none');
                document.getElementById('choose_image_btn').classList.remove('d-none');
                inputs.forEach(input => {
                    if (input.name !== 'item_code') {
                        input.disabled = false;
                    }
                });
            }
            
            if (skuInput) {
                skuInput.dataset.skuExists = "false";
                skuInput.classList.remove('is-invalid');
            }
            if (skuMsg) {
                skuMsg.style.display = 'none';
                skuMsg.innerText = '';
            }
            if (saveBookBtn) {
                saveBookBtn.disabled = false;
            }

            safeModal(nonBookModal, 'addNonBookModal', 'show');
        }

        document.querySelectorAll('.edit-book-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch(`/marketing/non-books/${id}/edit`)
                    .then(response => response.json())
                    .then(data => populateBookModal(data, false));
            });
        });

        document.querySelectorAll('.view-book-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch(`/marketing/non-books/${id}/edit`)
                    .then(response => response.json())
                    .then(data => populateBookModal(data, true));
            });
        });

        const addNonBookModalEl = document.getElementById('addNonBookModal');
        if (addNonBookModalEl) {
            addNonBookModalEl.addEventListener('hidden.bs.modal', function () {
                if (isFixingErrors) {
                    isFixingErrors = false;
                    return;
                }
                nonBookForm.reset();
                clearBookFormErrors();
                document.getElementById('modal_book_id').value = '';
                
                if (skuInput) {
                    skuInput.dataset.skuExists = "false";
                    skuInput.classList.remove('is-invalid');
                }
                if (skuMsg) {
                    skuMsg.style.display = 'none';
                    skuMsg.innerText = '';
                }
                if (saveBookBtn) {
                    saveBookBtn.disabled = false;
                }
                
                document.getElementById('addNonBookModalTitle').innerText = "Add New Non-Book to Master Registry";
                document.getElementById('saveNonBookBtn').classList.remove('d-none');
                document.getElementById('choose_image_btn').classList.remove('d-none');
                nonBookForm.querySelectorAll('input, select, textarea').forEach(input => {
                    if (input.name !== 'item_code') {
                        input.disabled = false;
                    }
                });

                const preview = document.getElementById('book_image_preview');
                preview.src = "{{ asset('images/no-book-cover.svg') }}";
                preview.classList.remove('d-none');
            });
        }

        const validationErrorsModalEl = document.getElementById('validationErrorsModal');
        if (validationErrorsModalEl) {
            validationErrorsModalEl.addEventListener('show.bs.modal', function () {
                const content = addNonBookModalEl.querySelector('.modal-content');
                if (content) content.style.filter = 'brightness(0.5)';
            });
            validationErrorsModalEl.addEventListener('hidden.bs.modal', function () {
                const content = addNonBookModalEl.querySelector('.modal-content');
                if (content) content.style.filter = 'brightness(1)';
            });
        }
        let firstErrorElement = null;

        function clearBookFormErrors() {
            if (!nonBookForm) return;
            nonBookForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            nonBookForm.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            const errorList = document.getElementById('modalErrorList');
            if (errorList) errorList.innerHTML = '';
            firstErrorElement = null;

            if (skuMsg) {
                skuMsg.style.display = 'none';
                skuMsg.innerText = '';
            }
            if (skuInput) {
                skuInput.dataset.skuExists = "false";
            }
            if (saveBookBtn) {
                saveBookBtn.disabled = false;
            }
        }

        function goToFirstError() {
            isFixingErrors = true;
            safeModal(validationErrorsModal, 'validationErrorsModal', 'hide');
            if (firstErrorElement) {
                const tabPane = firstErrorElement.closest('.tab-pane');
                let tabTrigger = null;
                
                if (tabPane) {
                    tabTrigger = document.querySelector(`.book-nav-tabs [data-bs-target="#${tabPane.id}"], .book-nav-tabs [href="#${tabPane.id}"]`);
                } else {
                    tabTrigger = document.querySelector('.book-nav-tabs .nav-link:first-child');
                }

                if (tabTrigger) {
                    try {
                        if (window.bootstrap && bootstrap.Tab) {
                            bootstrap.Tab.getOrCreateInstance(tabTrigger).show();
                        } else if (window.jQuery && typeof $(tabTrigger).tab === 'function') {
                            $(tabTrigger).tab('show');
                        } else {
                            tabTrigger.click();
                        }
                    } catch (e) {
                        tabTrigger.click();
                    }
                }
                
                setTimeout(() => {
                    firstErrorElement.focus();
                    firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 250);
            }
        }

        nonBookForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clearBookFormErrors();

            const id = document.getElementById('modal_book_id').value;
            const url = id ? `/marketing/non-books/${id}/update` : "{{ route('marketing.non-books.store') }}";
            
            const formData = new FormData(this);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                const data = await response.json();
                
                if (response.status === 422) {
                    const errorList = document.getElementById('modalErrorList');
                    let errorMessages = [];
                    firstErrorElement = null;
                    
                    for (const [key, messages] of Object.entries(data.errors)) {
                        const input = nonBookForm.querySelector(`[name="${key}"]`);
                        if (input) {
                            if (!firstErrorElement) firstErrorElement = input;
                            input.classList.add('is-invalid');
                        }
                        messages.forEach(msg => {
                            errorMessages.push(msg);
                            if (errorList) {
                                const li = document.createElement('li');
                                li.innerText = msg;
                                errorList.appendChild(li);
                            }
                        });
                    }

                    const shown = safeModal(validationErrorsModal, 'validationErrorsModal', 'show');
                    
                    if (!shown || !document.getElementById('validationErrorsModal').classList.contains('show')) {
                        setTimeout(() => {
                            const modal = document.getElementById('validationErrorsModal');
                            if (!modal || window.getComputedStyle(modal).display === 'none') {
                                window.alert("VALIDATION ERRORS:\n\n" + errorMessages.join("\n"));
                            }
                        }, 200);
                    }
                } else if (!response.ok) {
                    const msg = data.message || 'An unexpected error occurred.';
                    if (window.showAlert) window.showAlert(msg, 'danger');
                    else window.alert(msg);
                } else {
                    const msg = data.message || 'Success';
                    if (window.showAlert) window.showAlert(msg, 'success');
                    else window.alert(msg);
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.alert('An unexpected error occurred during submission.');
            });
        });

        // Delete button opens modal
        document.querySelectorAll('.delete-book-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('delete_book_id').value = this.dataset.id;
                deleteModal.show();
            });
        });

        // Confirm Delete Button Action
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            const id = document.getElementById('delete_book_id').value;
            
            fetch(`/marketing/non-books/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                deleteModal.hide();
                if (data.error) {
                    window.showAlert(data.error, 'danger');
                } else {
                    window.showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                }
            });
        });

        // Category Management JS
        const addCategoryFormOnly = document.getElementById('addCategoryFormOnly');
        const addSubCategoryForm = document.getElementById('addSubCategoryForm');
        const accordionContainer = document.getElementById('categoriesList');
        const parentCatSelect = document.querySelector('#addSubCategoryForm select[name="parent_id"]');
        const bookCatSelect = document.getElementById('book_category_id');

        function refreshCategoryUI() {
            fetch("{{ route('marketing.categories.index') }}")
                .then(response => response.json())
                .then(data => {
                    let parentOpts = '<option value="" disabled selected>Select Parent Category...</option>';
                    let bookOpts = '<option value="">Select Category</option>';
                    
                    data.forEach(cat => {
                        parentOpts += `<option value="${cat.id}">${cat.name}</option>`;
                        bookOpts += `<option value="${cat.id}">${cat.name}</option>`;
                    });
                    
                    if(parentCatSelect) parentCatSelect.innerHTML = parentOpts;
                    if(bookCatSelect) bookCatSelect.innerHTML = bookOpts;

                    let html = '<div class="accordion accordion-sm accordion-danger-solid" id="accordionCategories">';
                    data.forEach(cat => {
                        let subHtml = '';
                        if(cat.children && cat.children.length > 0) {
                            cat.children.forEach(sub => {
                                subHtml += `
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-1 border-0 ps-3">
                                        <span class="small text-muted">— ${sub.name}</span>
                                        <a href="javascript:void(0);" class="text-danger delete-category-btn" data-id="${sub.id}" title="Delete Sub-category">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </li>`;
                            });
                        } else {
                            subHtml = '<li class="list-group-item py-1 border-0 ps-3 text-muted small">No sub-categories</li>';
                        }
                        
                        html += `
                            <div class="accordion-item">
                                <div class="accordion-header d-flex align-items-center justify-content-between pe-3">
                                    <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCat${cat.id}">
                                        ${cat.name}
                                    </button>
                                    <a href="javascript:void(0);" class="text-danger delete-category-btn" data-id="${cat.id}" title="Delete Category">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                                <div id="collapseCat${cat.id}" class="accordion-collapse collapse" data-bs-parent="#accordionCategories">
                                    <div class="accordion-body py-2">
                                        <ul class="list-group list-group-flush mb-0">
                                            ${subHtml}
                                        </ul>
                                    </div>
                                </div>
                            </div>`;
                    });
                    html += '</div>';
                    accordionContainer.innerHTML = html;
                    bindDeleteEvents();
                });
        }

        function handleCategorySubmit(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);
            fetch("{{ route('marketing.categories.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                window.showAlert(data.message, 'success');
                form.reset();
                refreshCategoryUI();
            })
            .catch(err => {
                console.error('Save Error:', err);
                let msg = 'Failed to save.';
                if(err.errors) msg = Object.values(err.errors).flat().join(' ');
                window.showAlert(msg, 'danger');
            });
        }

        const deleteCategoryModal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));

        function bindDeleteEvents() {
            document.querySelectorAll('.delete-category-btn').forEach(btn => {
                btn.onclick = function() {
                    document.getElementById('delete_cat_id').value = this.dataset.id;
                    deleteCategoryModal.show();
                };
            });
        }

        document.getElementById('confirmDeleteCategoryBtn').addEventListener('click', function() {
            const id = document.getElementById('delete_cat_id').value;
            fetch(`/marketing/book-categories/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                deleteCategoryModal.hide();
                window.showAlert(data.message, 'success');
                refreshCategoryUI();
            });
        });

        if(addCategoryFormOnly) addCategoryFormOnly.addEventListener('submit', handleCategorySubmit);
        if(addSubCategoryForm) addSubCategoryForm.addEventListener('submit', handleCategorySubmit);
        bindDeleteEvents();

        // Dependent Dropdown Logic
        document.getElementById('book_category_id').addEventListener('change', function() {
            const id = this.value;
            const subCatSelect = document.getElementById('book_sub_category_id');
            subCatSelect.innerHTML = '<option value="">Select Sub-category</option>';
            
            if (!id) return;

            fetch(`/marketing/book-categories/${id}/subcategories`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(sub => {
                        const opt = document.createElement('option');
                        opt.value = sub.id;
                        opt.textContent = sub.name;
                        subCatSelect.appendChild(opt);
                    });
                });
        });

        // Excel Import Form Submission for Non-Books
        const importNonBooksModalEl = document.getElementById('importNonBooksModal');
        const importNonBooksModal = importNonBooksModalEl ? bootstrap.Modal.getOrCreateInstance(importNonBooksModalEl) : null;
        const importForm = document.getElementById('importNonBooksForm');
        const importLoading = document.getElementById('importLoading');
        const importSubmitBtn = document.getElementById('importSubmitBtn');
        const importErrorsContainer = document.getElementById('importErrorsContainer');
        const importErrorList = document.getElementById('importErrorList');

        if (importForm) {
            importForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Hide previous errors
                importErrorsContainer.classList.add('d-none');
                importErrorList.innerHTML = '';
                
                // Show loading, disable buttons
                importLoading.classList.remove('d-none');
                importSubmitBtn.disabled = true;
                
                const formData = new FormData(this);
                
                fetch("{{ route('marketing.non-books.import') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async response => {
                    const data = await response.json();
                    
                    // Hide loading & enable submit
                    importLoading.classList.add('d-none');
                    importSubmitBtn.disabled = false;
                    
                    if (response.status === 422) {
                        importErrorsContainer.classList.remove('d-none');
                        if (data.details && data.details.length > 0) {
                            data.details.forEach(msg => {
                                const li = document.createElement('li');
                                li.innerText = msg;
                                importErrorList.appendChild(li);
                            });
                        } else {
                            const li = document.createElement('li');
                            li.innerText = data.error || 'Validation failed.';
                            importErrorList.appendChild(li);
                        }
                    } else if (!response.ok) {
                        importErrorsContainer.classList.remove('d-none');
                        const li = document.createElement('li');
                        li.innerText = data.error || data.message || 'An unexpected error occurred.';
                        importErrorList.appendChild(li);
                    } else {
                        // Success!
                        importForm.reset();
                        if (importNonBooksModal) {
                            importNonBooksModal.hide();
                        }
                        
                        const successMsg = `${data.message} Created: ${data.created} non-books. Updated: ${data.updated} non-books.`;
                        if (window.showAlert) {
                            window.showAlert(successMsg, 'success');
                        } else {
                            alert(successMsg);
                        }
                        
                        setTimeout(() => location.reload(), 1500);
                    }
                })
                .catch(error => {
                    importLoading.classList.add('d-none');
                    importSubmitBtn.disabled = false;
                    importErrorsContainer.classList.remove('d-none');
                    
                    const li = document.createElement('li');
                    li.innerText = 'Network error or connection lost. Please try again.';
                    importErrorList.appendChild(li);
                    console.error('Import Error:', error);
                });
            });

            // Clean errors when closing modal
            if (importNonBooksModalEl) {
                importNonBooksModalEl.addEventListener('hidden.bs.modal', function () {
                    importForm.reset();
                    importErrorsContainer.classList.add('d-none');
                    importErrorList.innerHTML = '';
                });
            }
        }
    </script>
    @endpush
</x-app-layout>
