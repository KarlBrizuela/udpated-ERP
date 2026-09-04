<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
            padding-bottom: 80px !important;
        }

        .sup-header-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem 1.75rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            margin-bottom: 1.5rem;
        }

        .btn-sup-primary {
            background-color: #D9251C;
            border-color: #D9251C;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.85rem;
            border-radius: 6px;
            padding: 8px 16px;
            transition: all 0.2s ease;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-sup-primary:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
            color: #ffffff;
        }

        /* Modern Table overrides */
        .table-modern {
            margin-bottom: 0 !important;
            border: none !important;
        }
        .table-modern thead th {
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
        .table-modern tbody td {
            padding: 12px 16px !important;
            font-size: 0.84rem !important;
            color: #475569 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            vertical-align: middle !important;
        }
        .table-modern tbody tr {
            transition: all 0.15s ease-in-out !important;
        }
        .table-modern tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* Form Modal overrides */
        .modal-content {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }
        .modal-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 18px 24px !important;
        }
        .modal-header .modal-title {
            font-size: 0.95rem !important;
            font-weight: 700 !important;
            color: #000000 !important;
        }
        .form-label {
            color: #475569 !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            font-size: 0.72rem !important;
            letter-spacing: 0.5px !important;
        }
        .form-control, .form-select {
            border-color: #cbd5e1 !important;
            border-radius: 6px !important;
            color: #000000 !important;
            font-size: 0.85rem !important;
            padding: 8px 12px !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: #D9251C !important;
            box-shadow: 0 0 0 0.2rem rgba(217, 37, 28, 0.15) !important;
            outline: none !important;
        }

        .pagination {
            margin-bottom: 0;
        }
        .page-item.active .page-link {
            background-color: #D9251C !important;
            border-color: #D9251C !important;
            color: #ffffff !important;
        }
        .page-link {
            color: #D9251C;
        }
    </style>
    @endpush

    <div class="container-fluid">
        <!-- Master Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="sup-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(217, 37, 28, 0.08); color: #D9251C;">
                            <i class="las la-box fs-24"></i>
                        </div>
                        <div>
                            <h4 class="fs-20 mb-1 fw-bold text-dark" style="letter-spacing: -0.3px;">Office Supplies Inventory</h4>
                            <p class="text-muted small mb-0">Manage accounting division office supplies, pricing, and stock levels.</p>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-sup-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addSupplyModal">
                            <i class="las la-plus-circle fs-16"></i> Add New Item
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory List Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #e2e8f0 !important;">
                    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="las la-list me-2 fs-18"></i>Item Catalog</h6>
                        
                        <form method="GET" action="{{ route('admin-finance.accounting.office-supplies.index') }}" class="d-flex align-items-center">
                            @if($log_search)
                                <input type="hidden" name="log_search" value="{{ $log_search }}">
                            @endif
                            @if($log_start_date)
                                <input type="hidden" name="log_start_date" value="{{ $log_start_date }}">
                            @endif
                            @if($log_end_date)
                                <input type="hidden" name="log_end_date" value="{{ $log_end_date }}">
                            @endif
                            <div class="input-group" style="width: 280px;">
                                <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 10px; border-top-left-radius: 4px; border-bottom-left-radius: 4px;">
                                    <i class="las la-search text-muted fs-16"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0 border-end-0" placeholder="Search item..." value="{{ $search ?? '' }}" style="height: 38px; border-color: #cbd5e1; font-size: 0.82rem; padding-left: 0; outline: none; box-shadow: none;">
                                <button type="submit" class="btn text-white px-3 d-inline-flex align-items-center justify-content-center" style="height: 38px; background-color: #D9251C; border-color: #D9251C; border-top-right-radius: 4px; border-bottom-right-radius: 4px; font-weight: 600; font-size: 0.82rem; line-height: 1 !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                                    Search
                                </button>
                            </div>
                            @if(!empty($search))
                                <a href="{{ route('admin-finance.accounting.office-supplies.index') }}" class="btn btn-sm btn-light border ms-2 d-inline-flex align-items-center justify-content-center" style="height: 38px; padding: 0 12px; border-radius: 4px; font-weight: 600; color: #475569;">
                                    Clear
                                </a>
                            @endif
                        </form>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-modern align-middle">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Item Price</th>
                                        <th>Items Stock</th>
                                        <th>Total Valuation</th>
                                        <th class="text-center" style="width: 260px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($supplies as $supply)
                                    <tr>
                                        <td><span class="fw-bold text-dark">{{ $supply->item_name }}</span></td>
                                        <td class="fw-bold text-dark">₱{{ number_format($supply->item_price, 2) }}</td>
                                        <td>
                                            @if($supply->items_stock <= 5)
                                                <span class="badge bg-danger-subtle text-danger px-2.5 py-1 font-w600" style="font-size: 0.72rem;">
                                                    Low Stock ({{ $supply->items_stock }} {{ $supply->unit ?? 'pcs' }})
                                                </span>
                                            @else
                                                <span class="badge bg-success-subtle text-success px-2.5 py-1 font-w600" style="font-size: 0.72rem;">
                                                    {{ $supply->items_stock }} {{ $supply->unit ?? 'pcs' }} in stock
                                                </span>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-danger">₱{{ number_format($supply->item_price * $supply->items_stock, 2) }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1 align-items-center">
                                                <button class="btn btn-success shadow btn-xs sharp text-white add-stock-btn"
                                                        data-bs-toggle="modal" data-bs-target="#addStockModal"
                                                        data-id="{{ $supply->id }}"
                                                        data-name="{{ $supply->item_name }}"
                                                        data-stock="{{ $supply->items_stock }}"
                                                        title="Add Stock">
                                                    <i class="las la-plus"></i>
                                                </button>

                                                <button class="btn btn-warning shadow btn-xs sharp text-white edit-btn"
                                                        data-bs-toggle="modal" data-bs-target="#editSupplyModal"
                                                        data-id="{{ $supply->id }}"
                                                        data-name="{{ $supply->item_name }}"
                                                        data-price="{{ $supply->item_price }}"
                                                        data-stock="{{ $supply->items_stock }}"
                                                        data-unit="{{ $supply->unit ?? 'pcs' }}"
                                                        title="Edit">
                                                    <i class="las la-pen"></i>
                                                </button>
                                                
                                                <button class="btn btn-danger shadow btn-xs sharp text-white"
                                                        onclick="confirmDelete({{ $supply->id }})"
                                                        title="Delete">
                                                    <i class="las la-trash"></i>
                                                </button>
                                                
                                                <form id="delete-form-{{ $supply->id }}" action="{{ route('admin-finance.accounting.office-supplies.destroy', $supply->id) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="las la-box fs-48 mb-2 d-block text-secondary"></i>
                                            No office supply items found. Click "Add New Item" above to add one.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Links -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted small">
                                Showing {{ $supplies->firstItem() ?? 0 }} to {{ $supplies->lastItem() ?? 0 }} of {{ $supplies->total() }} items
                            </div>
                            <div>
                                {{ $supplies->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logs Card -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #e2e8f0 !important;">
                    <div class="card-header bg-white border-0 pt-4 pb-2">
                        <h6 class="fw-bold text-dark mb-1"><i class="las la-history me-2 fs-18"></i>Stock Transaction History</h6>
                        <p class="text-muted small mb-0">Logs of all office supply stock additions and replenishments.</p>
                    </div>
                    <div class="card-body">
                        <!-- Log Filters -->
                        <form action="{{ route('admin-finance.accounting.office-supplies.index') }}" method="GET" class="row mb-4 g-3 align-items-end">
                            @if($search)
                                <input type="hidden" name="search" value="{{ $search }}">
                            @endif

                            <div class="col-md-3">
                                <label for="log_search" class="form-label">Search Item</label>
                                <input type="text" id="log_search" name="log_search" class="form-control" placeholder="Item name..." value="{{ $log_search }}">
                            </div>

                            <div class="col-md-3">
                                <label for="log_start_date" class="form-label">Start Date</label>
                                <input type="date" id="log_start_date" name="log_start_date" class="form-control" value="{{ $log_start_date }}">
                            </div>

                            <div class="col-md-3">
                                <label for="log_end_date" class="form-label">End Date</label>
                                <input type="date" id="log_end_date" name="log_end_date" class="form-control" value="{{ $log_end_date }}">
                            </div>

                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-sup-primary w-100 fw-semibold text-white d-inline-flex align-items-center justify-content-center" style="height: 38px;">Filter Logs</button>
                                @if($log_search || $log_start_date || $log_end_date)
                                    <a href="{{ route('admin-finance.accounting.office-supplies.index', ['search' => $search]) }}" 
                                       class="btn btn-light border d-flex align-items-center justify-content-center" 
                                       style="height: 38px; width: 44px; border-radius: 6px; color: #475569;" title="Reset filters">
                                        <i class="las la-undo-alt fs-16"></i>
                                    </a>
                                @endif
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-modern align-middle">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Item Name</th>
                                        <th>Qty Added</th>
                                        <th>Unit Price</th>
                                        <th>Prev Stock</th>
                                        <th>New Stock</th>
                                        <th>Total Expense</th>
                                        <th>Supplier</th>
                                        <th>Added By</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                    @php
                                        $price = $log->unit_price > 0 ? $log->unit_price : ($log->officeSupply->item_price ?? 0);
                                        $totalCost = $log->quantity * $price;
                                    @endphp
                                    <tr>
                                        <td><span class="text-dark small">{{ $log->created_at->format('Y-m-d h:i A') }}</span></td>
                                        <td><span class="fw-bold text-dark">{{ $log->item_name ?? ($log->officeSupply->item_name ?? 'Deleted Item') }}</span></td>
                                        <td><span class="text-success fw-bold">+{{ $log->quantity }}</span></td>
                                        <td class="text-dark fw-bold">₱{{ number_format($price, 2) }}</td>
                                        <td class="text-muted">{{ $log->previous_stock }}</td>
                                        <td class="fw-bold text-dark">{{ $log->new_stock }}</td>
                                        <td class="fw-bold text-danger">₱{{ number_format($totalCost, 2) }}</td>
                                        <td><span class="text-dark">{{ $log->supplier->company_name ?? 'N/A' }}</span></td>
                                        <td><span class="text-muted small">{{ $log->addedBy->name ?? 'N/A' }}</span></td>
                                        <td><span class="text-muted small">{{ $log->notes ?: '—' }}</span></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5 text-muted">
                                            <i class="las la-history fs-48 mb-2 d-block text-secondary"></i>
                                            No stock transaction logs found.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Logs Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted small">
                                Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} logs
                            </div>
                            <div>
                                {{ $logs->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addSupplyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark"><i class="las la-plus-circle me-2 text-danger"></i> Add Office Supply Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin-finance.accounting.office-supplies.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="item_name" class="form-label">Item Name *</label>
                            <input type="text" class="form-control" id="item_name" name="item_name" placeholder="e.g. A4 Bond Paper" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="item_price" class="form-label">Item Price (₱) *</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="item_price" name="item_price" placeholder="0.00" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="items_stock" class="form-label">Items Stock *</label>
                                <input type="number" min="0" class="form-control" id="items_stock" name="items_stock" placeholder="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="unit" class="form-label">Unit *</label>
                                <select class="form-select" id="unit" name="unit" required>
                                    <option value="pcs">pcs (Pieces)</option>
                                    <option value="box">box (Box)</option>
                                    <option value="pack">pack (Pack)</option>
                                    <option value="ream">ream (Ream)</option>
                                    <option value="set">set (Set)</option>
                                    <option value="roll">roll (Roll)</option>
                                    <option value="pad">pad (Pad)</option>
                                    <option value="bundle">bundle (Bundle)</option>
                                    <option value="bottle">bottle (Bottle)</option>
                                    <option value="pair">pair (Pair)</option>
                                    <option value="cartridge">cartridge (Cartridge/Toner)</option>
                                    <option value="unit">unit (Unit)</option>
                                    <option value="kg">kg (Kilogram)</option>
                                    <option value="liter">liter (Liter)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 6px;">Cancel</button>
                        <button type="submit" class="btn btn-sup-primary px-4">Save Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editSupplyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark"><i class="las la-edit me-2 text-danger"></i> Edit Office Supply Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editSupplyForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="edit_item_name" class="form-label">Item Name *</label>
                            <input type="text" class="form-control" id="edit_item_name" name="item_name" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_item_price" class="form-label">Item Price (₱) *</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="edit_item_price" name="item_price" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_unit" class="form-label">Unit *</label>
                                <select class="form-select" id="edit_unit" name="unit" required>
                                    <option value="pcs">pcs (Pieces)</option>
                                    <option value="box">box (Box)</option>
                                    <option value="pack">pack (Pack)</option>
                                    <option value="ream">ream (Ream)</option>
                                    <option value="set">set (Set)</option>
                                    <option value="roll">roll (Roll)</option>
                                    <option value="pad">pad (Pad)</option>
                                    <option value="bundle">bundle (Bundle)</option>
                                    <option value="bottle">bottle (Bottle)</option>
                                    <option value="pair">pair (Pair)</option>
                                    <option value="cartridge">cartridge (Cartridge/Toner)</option>
                                    <option value="unit">unit (Unit)</option>
                                    <option value="kg">kg (Kilogram)</option>
                                    <option value="liter">liter (Liter)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 6px;">Cancel</button>
                        <button type="submit" class="btn btn-sup-primary px-4">Update Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Stock Modal -->
    <div class="modal fade" id="addStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark"><i class="las la-plus-circle me-2 text-danger"></i> Add Stock to Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addStockForm" action="" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label">Item Name</label>
                            <input type="text" class="form-control" id="stock_item_name" readonly style="background-color: #f1f5f9;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Stock</label>
                            <input type="text" class="form-control" id="stock_current_stock" readonly style="background-color: #f1f5f9;">
                        </div>
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Supplier *</label>
                            <select class="form-select" id="supplier_id" name="supplier_id" required>
                                <option value="">-- Select Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity to Add *</label>
                            <input type="number" min="1" class="form-control" id="quantity" name="quantity" placeholder="e.g. 50" required>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Remarks / Notes</label>
                            <input type="text" class="form-control" id="notes" name="notes" placeholder="e.g. Replenishment">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 6px;">Cancel</button>
                        <button type="submit" class="btn btn-sup-primary px-4">Add Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Edit modal data population
            $('.edit-btn').on('click', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const price = $(this).data('price');
                const stock = $(this).data('stock');
                const unit = $(this).data('unit') || 'pcs';
                
                $('#edit_item_name').val(name);
                $('#edit_item_price').val(price);
                $('#edit_items_stock').val(stock);
                $('#edit_unit').val(unit);
                
                // Update form action URL dynamically
                const route = "{{ route('admin-finance.accounting.office-supplies.update', ':id') }}";
                $('#editSupplyForm').attr('action', route.replace(':id', id));
            });

            // Add Stock modal data population
            $('.add-stock-btn').on('click', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const stock = $(this).data('stock');
                
                $('#stock_item_name').val(name);
                $('#stock_current_stock').val(stock + ' in stock');
                $('#quantity').val('');
                $('#notes').val('');
                $('#supplier_id').val('');
                
                // Update form action URL dynamically
                const route = "{{ route('admin-finance.accounting.office-supplies.add-stock', ':id') }}";
                $('#addStockForm').attr('action', route.replace(':id', id));
            });
        });

        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action will permanently delete this office supply item and all historical stock transaction logs associated with it.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#D9251C',
                cancelButtonColor: '#475569',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
