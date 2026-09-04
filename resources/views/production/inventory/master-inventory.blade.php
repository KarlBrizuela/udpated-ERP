<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .inv-header-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.75rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 0;
            margin-bottom: 1.5rem;
        }

        .hover-row {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-row:hover {
            background-color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
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

        .sub-pill {
            font-size: 0.78rem;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 15px;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            display: inline-block;
        }

        .sub-pill.active {
            background-color: #e53935;
            color: #fff !important;
        }

        .sub-pill:not(.active) {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .stock-badge {
            font-family: monospace;
            font-size: 0.85rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .stock-positive { background-color: rgba(217, 37, 28, 0.1); color: #D9251C; }
        .stock-zero { background-color: rgba(107, 114, 128, 0.1); color: #9ca3af; }
        .stock-damaged { background-color: rgba(229, 57, 53, 0.15); color: #e53935; }
    </style>
    @endpush

    <div class="container-fluid">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="las la-check-circle me-2 fs-18"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="las la-exclamation-triangle me-2 fs-18"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Master Title Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="inv-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fs-24 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Production Master Inventory</h4>
                        <p class="text-muted small mb-0">Manage 11 separate inventory categories and track independent stock across 10 warehouse locations.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-danger btn-sm px-3 text-white rounded shadow-sm d-flex align-items-center gap-2" style="background-color: #D9251C; border-color: #D9251C; height: 40px;" data-bs-toggle="modal" data-bs-target="#addItemModal">
                            <i class="las la-plus-circle fs-18"></i> Add Inventory Item
                        </button>
                        <button class="btn btn-outline-danger btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="color: #D9251C; border-color: #D9251C; height: 40px;" data-bs-toggle="modal" data-bs-target="#transferStockModal">
                            <i class="las la-exchange-alt fs-18"></i> Transfer Stock
                        </button>
                        <button class="btn btn-outline-secondary btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="height: 40px;" onclick="window.print()">
                            <i class="las la-print fs-18"></i> Print Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric summary cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; color: #D9251C;">
                            <i class="las la-coins fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Inventory Value</span>
                            <h4 class="fw-bold text-dark mb-0">₱{{ number_format($metrics['total_valuation'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; color: #D9251C;">
                            <i class="las la-boxes fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Stock Units</span>
                            <h4 class="fw-bold mb-0" style="color: #D9251C;">{{ number_format($metrics['total_stock_units']) }} Units</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; color: #e53935;">
                            <i class="las la-warehouse fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Active Warehouses</span>
                            <h4 class="fw-bold text-dark mb-0">{{ $metrics['active_warehouses_count'] }} Locations</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; color: #D9251C;">
                            <i class="las la-exclamation-circle fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Damaged & Low Stock</span>
                            <h4 class="fw-bold mb-0" style="color: #D9251C;">{{ $metrics['low_stock_count'] }} Low / {{ $metrics['damaged_stock_count'] }} Dmg</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Categories Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; background: #fff;">
                    <span class="text-muted small fw-bold mb-2 d-block text-uppercase">Separate Inventories (Categories):</span>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <a href="{{ route('production.inventory.master', ['category' => 'All']) }}" class="category-pill {{ $selectedCategory == 'All' ? 'active' : '' }}">
                            All Categories
                        </a>
                        @foreach($categories as $cat)
                        <a href="{{ route('production.inventory.master', ['category' => $cat]) }}" class="category-pill {{ $selectedCategory == $cat ? 'active' : '' }}">
                            {{ $cat }}
                        </a>
                        @endforeach
                    </div>

                    @if($selectedCategory === 'Raw Materials')
                    <div class="pt-2 border-top">
                        <span class="text-muted small fw-bold me-2">Raw Materials Subcategories:</span>
                        <a href="{{ route('production.inventory.master', ['category' => 'Raw Materials', 'subcategory' => 'All']) }}" class="sub-pill {{ $selectedSubcategory == 'All' ? 'active' : '' }}">All Raw Materials</a>
                        @foreach($rawMaterialSubcategories as $sub)
                        <a href="{{ route('production.inventory.master', ['category' => 'Raw Materials', 'subcategory' => $sub]) }}" class="sub-pill {{ $selectedSubcategory == $sub ? 'active' : '' }} me-1">
                            {{ $sub }}
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Master Warehouse Stock Breakdown Matrix -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark fs-18">Warehouse Stock Allocation Matrix</h5>
                            <p class="text-muted small mb-0">Detailed inventory breakdown across all 10 independent warehouse locations</p>
                        </div>
                        <form action="{{ route('production.inventory.master') }}" method="GET" class="d-flex gap-2">
                            <input type="hidden" name="category" value="{{ $selectedCategory }}">
                            <input type="hidden" name="subcategory" value="{{ $selectedSubcategory }}">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search SKU or Item..." value="{{ $search }}">
                            <button type="submit" class="btn btn-sm text-white px-3" style="background-color: #D9251C; border-color: #D9251C;">Filter</button>
                        </form>
                    </div>

                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle" id="masterInventoryTable">
                                <thead class="table-dark text-white small text-uppercase align-middle">
                                    <tr>
                                        <th style="min-width: 140px;">SKU / Code</th>
                                        <th style="min-width: 180px;">Item Name</th>
                                        <th>Category</th>
                                        <th class="text-end">Unit Cost</th>
                                        <th class="text-center" style="background-color: #D9251C;">Total Stock</th>
                                        <!-- 10 Warehouse Columns -->
                                        <th class="text-center" title="Main Warehouse">Main WH</th>
                                        <th class="text-center" title="Bookstore Warehouse">Bookstore</th>
                                        <th class="text-center" title="Area Sales Warehouse">Area Sales</th>
                                        <th class="text-center" title="Consignment Warehouse">Consignment</th>
                                        <th class="text-center" title="Reserved Warehouse">Reserved</th>
                                        <th class="text-center" title="Book Sale Warehouse">Book Sale</th>
                                        <th class="text-center" title="E-commerce Warehouse">E-commerce</th>
                                        <th class="text-center" title="Damaged Stock">Damaged</th>
                                        <th class="text-center" title="Returned Stock">Returned</th>
                                        <th class="text-center" title="In Transit">In Transit</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($items as $item)
                                    @php
                                        $tot = $item->warehouseStocks->sum('quantity');
                                    @endphp
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark font-monospace">{{ $item->sku }}</span></td>
                                        <td>
                                            <span class="fw-bold text-dark d-block fs-14">{{ $item->name }}</span>
                                            <span class="text-muted small">{{ $item->unit_of_measure }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $item->category }}</span>
                                            @if($item->subcategory)
                                            <span class="badge bg-secondary-subtle text-secondary">{{ $item->subcategory }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold text-dark">₱{{ number_format($item->unit_cost, 2) }}</td>
                                        <td class="text-center fw-bold fs-15 text-white" style="background-color: #D9251C;">
                                            {{ number_format($tot) }}
                                        </td>

                                        <!-- Loop through all 10 warehouses -->
                                        @foreach($warehouses as $wh)
                                        @php
                                            $stk = $item->warehouseStocks->where('site_id', $wh->id)->first();
                                            $qty = $stk ? $stk->quantity : 0;
                                            $isDamagedOrReturned = in_array($wh->name, ['Damaged Stock Warehouse', 'Returned Stock Warehouse']);
                                        @endphp
                                        <td class="text-center">
                                            <span class="stock-badge {{ $qty > 0 ? ($isDamagedOrReturned ? 'stock-damaged' : 'stock-positive') : 'stock-zero' }}">
                                                {{ $qty }}
                                            </span>
                                        </td>
                                        @endforeach

                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-danger py-1 px-2" style="color: #D9251C; border-color: #D9251C;" data-bs-toggle="modal" data-bs-target="#adjustStockModal{{ $item->id }}">
                                                <i class="las la-edit"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Adjust Stock Modal per item -->
                                    <div class="modal fade" id="adjustStockModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <form action="{{ route('production.inventory.stock.update') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="inventory_category_item_id" value="{{ $item->id }}">
                                                    <div class="modal-header text-white" style="background-color: #D9251C;">
                                                        <h6 class="modal-title fw-bold"><i class="las la-sliders-h me-2"></i>Adjust Stock: {{ $item->name }}</h6>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted">Select Warehouse Location</label>
                                                            <select name="site_id" class="form-select" required>
                                                                @foreach($warehouses as $wh)
                                                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted">New Stock Quantity</label>
                                                            <input type="number" name="quantity" class="form-control" placeholder="0" min="0" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn text-white px-4" style="background-color: #D9251C; border-color: #D9251C;">Update Balance</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <tr>
                                        <td colspan="16" class="text-center py-4 text-muted">No inventory items found matching your filters.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 1: ADD INVENTORY ITEM -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('production.inventory.items.store') }}" method="POST">
                    @csrf
                    <div class="modal-header text-white" style="background-color: #D9251C;">
                        <h5 class="modal-title fw-bold"><i class="las la-plus-circle me-2"></i>Add Production Inventory Item</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold small text-muted">Item / Raw Material Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Book Paper 70gsm 25x38, Cyan Ink, Office Paper" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Inventory Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" id="itemCategorySelect" required>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4" id="subcategoryWrapper">
                                <label class="form-label fw-bold small text-muted">Raw Material Subcategory</label>
                                <select name="subcategory" class="form-select">
                                    @foreach($rawMaterialSubcategories as $sub)
                                    <option value="{{ $sub }}">{{ $sub }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Unit of Measure <span class="text-danger">*</span></label>
                                <input type="text" name="unit_of_measure" class="form-control" placeholder="e.g. reams, kg, pcs, liters, boxes" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Unit Cost (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="unit_cost" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Reorder Point <span class="text-danger">*</span></label>
                                <input type="number" name="reorder_point" class="form-control" value="10" min="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Initial Warehouse Site <span class="text-danger">*</span></label>
                                <select name="initial_warehouse_id" class="form-select" required>
                                    @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Initial Stock Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="initial_stock" class="form-control" value="0" min="0" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Description / Specifications</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Material grade, dimensions, or usage notes..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C;">Save Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: TRANSFER STOCK -->
    <div class="modal fade" id="transferStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('production.inventory.stock.transfer') }}" method="POST">
                    @csrf
                    <div class="modal-header text-white" style="background-color: #D9251C;">
                        <h5 class="modal-title fw-bold"><i class="las la-exchange-alt me-2"></i>Transfer Warehouse Stock</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Select Item <span class="text-danger">*</span></label>
                                <select name="inventory_category_item_id" class="form-select" required>
                                    <option value="">Choose Inventory Item...</option>
                                    @foreach($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->sku }} - {{ $item->name }} (Category: {{ $item->category }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">From Warehouse (Source) <span class="text-danger">*</span></label>
                                <select name="from_site_id" class="form-select" required>
                                    @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">To Warehouse (Destination) <span class="text-danger">*</span></label>
                                <select name="to_site_id" class="form-select" required>
                                    @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted">Quantity to Transfer <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control" min="1" placeholder="1" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Notes / Reason for Transfer</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Allocation for Bookstore sales, Damage report transfer, Event setup..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Execute Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
