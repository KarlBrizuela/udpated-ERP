<x-app-layout :title="'Inventory Overview'" :sidebar="$sidebar ?? 'production'" :role="$role ?? 'User Role'">
@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        /* Site Inventory Modal Extra Large & Single Scrollbar Enforcement */
        .site-inventory-modal .modal-dialog {
            max-width: 92vw !important;
            width: 1200px !important;
        }
        .site-inventory-modal .modal-body {
            max-height: 85vh !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }
        .site-inventory-modal .table-responsive,
        .site-inventory-modal .tab-content,
        .site-inventory-modal .tab-pane {
            max-height: none !important;
            height: auto !important;
            overflow: visible !important;
        }
        .site-inventory-modal .table {
            margin-bottom: 0 !important;
        }

        /* Select2 Bootstrap 5 & Modal Integration Styling */
        .select2-container--default .select2-selection--single {
            height: 42px !important;
            padding: 6px 12px !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
            display: flex !important;
            align-items: center !important;
            background-color: #fff !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            color: #495057 !important;
            padding-left: 0 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 8px !important;
        }
        .select2-dropdown {
            border-color: #ced4da !important;
            border-radius: 0.375rem !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            z-index: 10060 !important;
        }
        .select2-search--dropdown {
            padding: 8px !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        .select2-search--dropdown .select2-search__field {
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem !important;
            padding: 6px 10px !important;
            outline: none !important;
            width: 100% !important;
            font-size: 14px !important;
            display: block !important;
            visibility: visible !important;
            height: 34px !important;
            box-sizing: border-box !important;
            opacity: 1 !important;
        }
        .select2-container--open .select2-search--dropdown {
            display: block !important;
        }
    </style>
@endpush
    <div class="container-fluid">
        <!-- Add Project Modal -->
        <div class="modal fade" id="addProjectSidebar">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label class="text-black font-w500">Project Name</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="text-black font-w500">Deadline</label>
                                <div class="cal-icon">
                                    <input type="date" class="form-control">
                                    <i class="far fa-calendar-alt"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="text-black font-w500">Client Name</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary">CREATE</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Inventory Management Tabs -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-8">
                <ul class="nav nav-tabs" role="tablist" style="border-bottom: 2px solid #e3e6f0;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="stocks-tab" data-bs-toggle="tab" data-bs-target="#stocks-content" type="button" role="tab" aria-controls="stocks-content" aria-selected="true">
                            <i class="las la-cubes me-2"></i>Stock Overview
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sites-tab" data-bs-toggle="tab" data-bs-target="#sites-content" type="button" role="tab" aria-controls="sites-content" aria-selected="false">
                            <i class="las la-map-marker me-2"></i>Sites
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="transfer-workflow-tab" data-bs-toggle="tab" data-bs-target="#transfer-workflow-content" type="button" role="tab" aria-controls="transfer-workflow-content" aria-selected="false">
                            <i class="las la-random me-2"></i>Transfer Workflow
                        </button>
                    </li>
                </ul>
            </div>
            @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->position === 'Super Admin' || auth()->user()->id == 1))
            <div class="col-md-4 text-end">
                <button type="button" id="reconcileStockBtn" class="btn btn-sm btn-outline-danger fw-bold px-3 shadow-sm" onclick="reconcileStockUI()">
                    <i class="las la-sync me-1"></i>Recalculate & Sync Stock
                </button>
                <script>
                window.reconcileStockUI = function() {
                    if (!confirm('Recalculate and synchronize all Master Book Stock levels with Warehouse Inventory?')) {
                        return;
                    }
                    const token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                    fetch('{{ route("production.inventory.reconcile-stock") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('✓ ' + data.message);
                            window.location.reload();
                        } else {
                            alert('Error: ' + (data.message || 'Failed to reconcile stock'));
                        }
                    })
                    .catch(err => {
                        alert('Error: ' + err.message);
                    });
                };
                </script>
            </div>
            @endif
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Stock Overview Tab -->
            <div class="tab-pane fade show active" id="stocks-content" role="tabpanel" aria-labelledby="stocks-tab">
        
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
                        <div class="card card-bd">
                            <div class="bg-primary card-border"></div>
                            <div class="card-body box-style">
                                <div class="media align-items-center">
                                    <div class="media-body me-3">
                                        <h2 class="num-text text-black font-w700">{{ $totalBooks }}</h2>
                                        <span class="fs-14">Master Book Records</span>
                                    </div>
                                    <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-primary-light">
                                        <i class="las la-cube fs-36 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
                        <div class="card card-bd">
                            <div class="bg-warning card-border"></div>
                            <div class="card-body box-style">
                                <div class="media align-items-center">
                                    <div class="media-body me-3">
                                        <h2 class="num-text text-black font-w700">{{ $lowStock }}</h2>
                                        <span class="fs-14">Low Stock Items</span>
                                    </div>
                                    <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-warning-light">
                                        <i class="las la-exclamation-triangle fs-36 text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
                        <div class="card card-bd">
                            <div class="bg-danger card-border"></div>
                            <div class="card-body box-style">
                                <div class="media align-items-center">
                                    <div class="media-body me-3">
                                        <h2 class="num-text text-black font-w700">{{ $outOfStock }}</h2>
                                        <span class="fs-14">Out of Stock</span>
                                    </div>
                                    <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-danger-light">
                                        <i class="las la-times-circle fs-36 text-danger"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- 
                    <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
                        <div class="card card-bd">
                            <div class="bg-success card-border"></div>
                            <div class="card-body box-style">
                                <div class="media align-items-center">
                                    <div class="media-body me-3">
                                        <h2 class="num-text text-black font-w700">₱{{ number_format($inventoryValue, 2) }}</h2>
                                        <span class="fs-14">Inventory Value</span>
                                    </div>
                                    <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-success-light">
                                        <i class="las la-coins fs-36 text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    --}}
                </div>
        
                <!-- Product Inventory Table with Sub-Tabs -->
                <div class="row">
                    <div class="col-xl-12 col-xxl-12">
                        <div class="card">
                            <div class="card-header border-0 d-block d-sm-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div class="d-flex align-items-center flex-wrap gap-3">
                                    
                                    <ul class="nav nav-tabs card-header-tabs border-0" role="tablist" style="margin-bottom: -15px;">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active font-w600" id="registry-books-tab" data-bs-toggle="tab" data-bs-target="#registry-books-content" type="button" role="tab" aria-controls="registry-books-content" aria-selected="true">
                                                <i class="las la-book me-1"></i>Books (Main Warehouse)
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link font-w600" id="registry-allsites-tab" data-bs-toggle="tab" data-bs-target="#registry-allsites-content" type="button" role="tab" aria-controls="registry-allsites-content" aria-selected="false">
                                                <i class="las la-warehouse me-1"></i>Master Registry
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link font-w600" id="registry-consignment-tab" data-bs-toggle="tab" data-bs-target="#registry-consignment-content" type="button" role="tab" aria-controls="registry-consignment-content" aria-selected="false">
                                                <i class="las la-truck-loading me-1"></i>Consignment Inventory
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link font-w600" id="registry-nonbooks-tab" data-bs-toggle="tab" data-bs-target="#registry-nonbooks-content" type="button" role="tab" aria-controls="registry-nonbooks-content" aria-selected="false">
                                                <i class="las la-gift me-1"></i>Non-Books
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link font-w600" id="registry-indices-tab" data-bs-toggle="tab" data-bs-target="#registry-indices-content" type="button" role="tab" aria-controls="registry-indices-content" aria-selected="false">
                                                <i class="las la-list me-1"></i>Indices
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                             <button class="nav-link font-w600" id="registry-bundles-tab" data-bs-toggle="tab" data-bs-target="#registry-bundles-content" type="button" role="tab" aria-controls="registry-bundles-content" aria-selected="false">
                                                 <i class="las la-boxes me-1"></i>Bundles
                                             </button>
                                         </li>
                                         <li class="nav-item" role="presentation">
                                             <button class="nav-link font-w600" id="registry-lost-tab" data-bs-toggle="tab" data-bs-target="#registry-lost-content" type="button" role="tab" aria-controls="registry-lost-content" aria-selected="false">
                                                 <i class="las la-exclamation-triangle me-1"></i>Lost Inventory
                                                 @if(isset($totalLostQty) && $totalLostQty > 0)
                                                     <span class="badge bg-danger text-white ms-1" style="font-size: 0.72rem; padding: 2px 6px; border-radius: 10px;">{{ number_format($totalLostQty) }}</span>
                                                 @endif
                                             </button>
                                         </li>
                                    </ul>
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-2 mt-3 mt-sm-0">
                                    <!-- Search Form -->
                                    <form action="{{ route('production.inventory.overview') }}" method="GET" class="d-flex align-items-center gap-2">
                                        <div style="width: 250px; height: 38px; display: flex; align-items: center; border: 1px solid #ced4da; border-radius: 4px; background-color: #f8f9fa; padding: 0 12px; box-sizing: border-box;">
                                            <span class="las la-search text-muted me-2" style="font-size: 1.1rem; line-height: 1;"></span>
                                            <input type="text" name="search" class="form-control" 
                                                   placeholder="Search registry..." value="{{ request('search') }}" 
                                                   style="border: none !important; background: transparent !important; padding: 0 !important; height: 100%; font-size: 0.85rem; color: #333; outline: none !important; box-shadow: none !important;">
                                            @if(request('search'))
                                                <a href="{{ route('production.inventory.overview') }}" class="text-muted d-inline-flex align-items-center justify-content-center ms-2" title="Clear search" style="text-decoration: none;">
                                                    <span class="las la-times-circle" style="color: #999; font-size: 1.25rem; cursor: pointer;"></span>
                                                </a>
                                            @endif
                                        </div>
                                        <button type="submit" class="btn btn-primary text-white rounded d-inline-flex align-items-center justify-content-center gap-2" style="height: 38px; padding: 0 1.2rem; border: none; font-size: 0.85rem; font-weight: 500; background-color: #D9251C; border-color: #D9251C; box-shadow: 0 4px 6px rgba(217, 37, 28, 0.15);">
                                            <span class="las la-search" style="font-size: 1rem; color: #fff;"></span>
                                            <span>Search</span>
                                        </button>
                                    </form>

                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#transferStockModal" onclick="initTransferModalFromMaster()" style="height: 38px; display: inline-flex; align-items: center;">
                                        <i class="las la-exchange-alt me-1"></i>Transfer Stock
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    
                                    <!-- Books Registry Tab Pane -->
                                    <div class="tab-pane fade show active" id="registry-books-content" role="tabpanel" aria-labelledby="registry-books-tab">
                                        <div class="table-responsive">
                                            <table class="table table-responsive-md">
                                                <thead>
                                                    <tr>
                                                        <th><strong>BOOK ID</strong></th>
                                                        <th><strong>TITLE</strong></th>
                                                        <th><strong>CATEGORY</strong></th>
                                                        <th><strong>UNIT COST</strong></th>
                                                        <th><strong>STOCK</strong></th>
                                                        <th><strong>MAX STOCK</strong></th>
                                                        <th><strong>STATUS</strong></th>
                                                        <th><strong>ACTION</strong></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($books as $book)
                                                    @php
                                                        // Get Main Warehouse inventory for this book
                                                        $mainWarehouseQuantity = 0;
                                                        if($mainWarehouse) {
                                                            $mainWarehouseQuantity = $mainWarehouse->inventory()
                                                                ->where('book_id', $book->id)
                                                                ->sum('quantity');
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td><strong>#{{ $book->sku }}</strong></td>
                                                        <td>{{ $book->name }}</td>
                                                        <td>{{ $book->category }}</td>
                                                        <td>₱{{ number_format($book->cost, 2) }}</td>
                                                        <td><strong>{{ $mainWarehouseQuantity }}</strong></td>
                                                        <td><strong>{{ $book->max_stock ?? 'N/A' }}</strong></td>
                                                        <td>
                                                            @if($mainWarehouseQuantity == 0)
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa fa-circle text-danger me-1"></i> Out of Stock
                                                                </div>
                                                            @elseif($mainWarehouseQuantity <= ($book->reorder_point ?? 0))
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa fa-circle text-warning me-1"></i> Low Stock
                                                                </div>
                                                            @else
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa fa-circle text-success me-1"></i> In Stock
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-danger btn-open-stock-mgmt" data-book-id="{{ $book->id }}" data-book-name="{{ $book->name }}" data-stock="{{ $mainWarehouseQuantity }}" data-max="{{ $book->max_stock ?? 0 }}">
                                                                <i class="las la-pen"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center">No master books found.</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-4">
                                            <div class="pagination-info">
                                                Showing {{ $books->firstItem() ?? 0 }} to {{ $books->lastItem() ?? 0 }} of {{ $books->total() }} entries
                                            </div>
                                            <nav>
                                                {{ $books->appends(['search' => request('search')])->links() }}
                                            </nav>
                                        </div>
                                    </div>

                                    <!-- Consignment Inventory Tab Pane -->
                                    <div class="tab-pane fade" id="registry-consignment-content" role="tabpanel" aria-labelledby="registry-consignment-tab">
                                        <div class="mb-3 p-2 bg-light rounded d-flex gap-2">
                                            <button class="btn btn-sm btn-danger active fw-bold c-sub-btn" onclick="switchConsignmentSubTab(this, 'area-consignment-pane')" type="button">
                                                <i class="las la-users me-1"></i>Area Consignment
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger fw-bold c-sub-btn" onclick="switchConsignmentSubTab(this, 'direct-consignment-pane')" type="button">
                                                <i class="las la-store me-1"></i>Direct Consignment (NBS / Direct Accounts)
                                            </button>
                                        </div>

                                        <div class="tab-content" id="consignmentSubTabsContent">
                                            <!-- 1. Area Consignment Sub-Pane -->
                                            <div class="tab-pane fade show active c-sub-pane" id="area-consignment-pane" role="tabpanel" style="display: block;">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-responsive-md text-black align-middle">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th style="width: 50px;" class="text-center"><strong>#</strong></th>
                                                                <th style="width: 250px;"><strong>CUSTOMER / ACCOUNT</strong></th>
                                                                <th style="width: 140px;"><strong>BOOK ID / SKU</strong></th>
                                                                <th><strong>BOOK TITLE</strong></th>
                                                                <th class="text-center" style="width: 120px;"><strong>QTY</strong></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($consignmentStaff as $custId => $cData)
                                                                @php $bookCount = count($cData->books); $rowIdx = 0; @endphp
                                                                @if($bookCount > 0)
                                                                    @foreach($cData->books as $bookData)
                                                                    @php $rowIdx++; @endphp
                                                                    <tr>
                                                                        @if($rowIdx === 1)
                                                                        <td class="text-center text-muted align-middle" rowspan="{{ $bookCount + 1 }}">
                                                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white mx-auto" style="width: 34px; height: 34px; background: #1a5276;">
                                                                                <i class="las la-store fs-16"></i>
                                                                            </div>
                                                                        </td>
                                                                        <td class="fw-bold text-black align-middle" rowspan="{{ $bookCount + 1 }}">
                                                                            <div class="fs-14 text-primary fw-bold mb-1">{{ $cData->customer_name }}</div>
                                                                            @if(!empty($cData->company_name))
                                                                                <div class="fs-12 text-muted fw-normal mb-1"><i class="las la-building text-secondary me-1"></i>Company: {{ $cData->company_name }}</div>
                                                                            @endif
                                                                            <div class="d-flex flex-wrap gap-1 mb-2">
                                                                                <span class="badge bg-secondary px-2 py-1 fs-11"><i class="las la-user me-1"></i>Staff: {{ $cData->staff_name }}</span>
                                                                                <span class="badge bg-primary px-2 py-1 fs-11">{{ $cData->orders_count }} {{ Str::plural('Order', $cData->orders_count) }}</span>
                                                                            </div>
                                                                            @if(!empty($cData->dr_numbers))
                                                                            <div class="mt-2 text-muted fs-11 fw-normal">
                                                                                <strong><i class="las la-file-alt text-primary me-1"></i>DR Numbers:</strong>
                                                                                <div class="mt-1 d-flex flex-wrap gap-1">
                                                                                    @foreach($cData->dr_numbers as $drNum)
                                                                                        <span class="badge bg-light text-dark border font-monospace px-1 py-1 fs-11">{{ $drNum }}</span>
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                            @endif
                                                                            <div class="mt-3">
                                                                                <button type="button" class="btn btn-sm btn-outline-primary fw-bold px-2 py-1 fs-11 btn-print-cust-sheet"
                                                                                        data-cust-data="{{ base64_encode(json_encode($cData)) }}">
                                                                                    <i class="las la-print me-1"></i>Print Inventory Sheet
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                        @endif
                                                                        <td><strong>#{{ $bookData['sku'] ?? 'N/A' }}</strong></td>
                                                                        <td class="fw-bold text-black">
                                                                            {{ $bookData['name'] }}
                                                                            @if(!empty($bookData['drs']))
                                                                                <div class="mt-1">
                                                                                    <small class="text-muted fw-normal d-inline-block me-2">
                                                                                        <i class="las la-file-alt text-secondary me-1"></i>DRs: {{ implode(', ', $bookData['drs']) }}
                                                                                    </small>
                                                                                </div>
                                                                            @endif
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <span class="badge bg-light text-success border border-success fw-bold px-2 py-1 fs-13">
                                                                                {{ number_format($bookData['total_qty']) }}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                    {{-- Subtotal row --}}
                                                                    <tr class="bg-light">
                                                                        <td colspan="2" class="text-end fw-bold text-black">TOTAL CONSIGNED:</td>
                                                                        <td class="text-center">
                                                                            <span class="badge bg-success fs-14 fw-bold px-3 py-2">
                                                                                {{ number_format($cData->total_items) }}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                @else
                                                                    <tr>
                                                                        <td class="text-center text-muted align-middle">
                                                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white mx-auto" style="width: 34px; height: 34px; background: #1a5276;">
                                                                                <i class="las la-store fs-16"></i>
                                                                            </div>
                                                                        </td>
                                                                        <td class="fw-bold text-black align-middle">
                                                                            <div class="fs-14 text-primary fw-bold mb-1">{{ $cData->customer_name }}</div>
                                                                            @if(!empty($cData->company_name))
                                                                                <div class="fs-12 text-muted fw-normal mb-1"><i class="las la-building text-secondary me-1"></i>Company: {{ $cData->company_name }}</div>
                                                                            @endif
                                                                        </td>
                                                                        <td colspan="3" class="text-center text-muted py-3">No consigned books recorded.</td>
                                                                    </tr>
                                                                @endif
                                                            @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center py-4 text-muted">No Area Consignment inventory found.</td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-3 px-2">
                                                    <div class="text-muted fs-13">
                                                        Showing {{ $consignmentStaff->firstItem() ?? 0 }} to {{ $consignmentStaff->lastItem() ?? 0 }} of {{ $consignmentStaff->total() }} entries
                                                    </div>
                                                    <div>
                                                        {{ $consignmentStaff->appends(request()->except('area_consignment_page'))->links() }}
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 2. Direct Consignment Sub-Pane -->
                                            <div class="tab-pane fade c-sub-pane" id="direct-consignment-pane" role="tabpanel" style="display: none;">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-responsive-md text-black align-middle">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th style="width: 50px;" class="text-center"><strong>#</strong></th>
                                                                <th style="width: 250px;"><strong>CUSTOMER / ACCOUNT</strong></th>
                                                                <th style="width: 140px;"><strong>BOOK ID / SKU</strong></th>
                                                                <th><strong>BOOK TITLE</strong></th>
                                                                <th class="text-center" style="width: 120px;"><strong>QTY</strong></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($directConsignmentCustomers as $custId => $cData)
                                                                @php $bCount = count($cData->books); $rIdx = 0; @endphp
                                                                @if($bCount > 0)
                                                                    @foreach($cData->books as $bData)
                                                                    @php $rIdx++; @endphp
                                                                    <tr>
                                                                        @if($rIdx === 1)
                                                                        <td class="text-center text-muted align-middle" rowspan="{{ $bCount + 1 }}">
                                                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white mx-auto" style="width: 34px; height: 34px; background: #c0392b;">
                                                                                <i class="las la-store fs-16"></i>
                                                                            </div>
                                                                        </td>
                                                                        <td class="fw-bold text-black align-middle" rowspan="{{ $bCount + 1 }}">
                                                                            <div class="fs-14 text-danger fw-bold mb-1">{{ $cData->customer_name }}</div>
                                                                            @if(!empty($cData->company_name))
                                                                                <div class="fs-12 text-muted fw-normal mb-1"><i class="las la-building text-secondary me-1"></i>Company: {{ $cData->company_name }}</div>
                                                                            @endif
                                                                            <div class="d-flex flex-wrap gap-1 mb-2">
                                                                                <span class="badge bg-danger px-2 py-1 fs-11">{{ $cData->orders_count }} {{ Str::plural('Order', $cData->orders_count) }}</span>
                                                                            </div>
                                                                            @if(!empty($cData->dr_numbers))
                                                                            <div class="mt-2 text-muted fs-11 fw-normal">
                                                                                <strong><i class="las la-file-alt text-danger me-1"></i>DR Numbers:</strong>
                                                                                <div class="mt-1 d-flex flex-wrap gap-1">
                                                                                    @foreach($cData->dr_numbers as $drNum)
                                                                                        <span class="badge bg-light text-dark border font-monospace px-1 py-1 fs-11">{{ $drNum }}</span>
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                            @endif
                                                                            <div class="mt-3">
                                                                                <button type="button" class="btn btn-sm btn-outline-danger fw-bold px-2 py-1 fs-11 btn-print-cust-sheet"
                                                                                        data-cust-data="{{ base64_encode(json_encode($cData)) }}">
                                                                                    <i class="las la-print me-1"></i>Print Inventory Sheet
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                        @endif
                                                                        <td><strong>#{{ $bData['sku'] ?? 'N/A' }}</strong></td>
                                                                        <td class="fw-bold text-black">
                                                                            {{ $bData['name'] }}
                                                                            @if(!empty($bData['drs']))
                                                                                <div class="mt-1">
                                                                                    <small class="text-muted fw-normal d-inline-block me-2">
                                                                                        <i class="las la-file-alt text-secondary me-1"></i>DRs: {{ implode(', ', $bData['drs']) }}
                                                                                    </small>
                                                                                </div>
                                                                            @endif
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <span class="badge bg-light text-danger border border-danger fw-bold px-2 py-1 fs-13">
                                                                                {{ number_format($bData['total_qty']) }}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                    {{-- Subtotal row --}}
                                                                    <tr class="bg-light">
                                                                        <td colspan="2" class="text-end fw-bold text-black">TOTAL CONSIGNED:</td>
                                                                        <td class="text-center">
                                                                            <span class="badge bg-danger fs-14 fw-bold px-3 py-2">
                                                                                {{ number_format($cData->total_items) }}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                @else
                                                                    <tr>
                                                                        <td class="text-center text-muted align-middle">
                                                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white mx-auto" style="width: 34px; height: 34px; background: #c0392b;">
                                                                                <i class="las la-store fs-16"></i>
                                                                            </div>
                                                                        </td>
                                                                        <td class="fw-bold text-black align-middle">
                                                                            <div class="fs-14 text-danger fw-bold mb-1">{{ $cData->customer_name }}</div>
                                                                            @if(!empty($cData->company_name))
                                                                                <div class="fs-12 text-muted fw-normal mb-1"><i class="las la-building text-secondary me-1"></i>Company: {{ $cData->company_name }}</div>
                                                                            @endif
                                                                        </td>
                                                                        <td colspan="3" class="text-center text-muted py-3">No consigned books recorded.</td>
                                                                    </tr>
                                                                @endif
                                                            @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center py-4 text-muted">No Direct Consignment inventory found.</td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-3 px-2">
                                                    <div class="text-muted fs-13">
                                                        Showing {{ $directConsignmentCustomers->firstItem() ?? 0 }} to {{ $directConsignmentCustomers->lastItem() ?? 0 }} of {{ $directConsignmentCustomers->total() }} entries
                                                    </div>
                                                    <div>
                                                        {{ $directConsignmentCustomers->appends(request()->except('direct_consignment_page'))->links() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="registry-allsites-content" role="tabpanel" aria-labelledby="registry-allsites-tab">
                                          <div class="table-responsive">
                                              <table class="table table-bordered table-responsive-md text-black align-middle">
                                                  <thead class="bg-light">
                                                      <tr>
                                                          <th style="width: 80px;"><strong>TYPE</strong></th>
                                                          <th style="width: 140px;"><strong>ITEM ID / SKU</strong></th>
                                                          <th style="min-width: 200px;"><strong>ITEM TITLE</strong></th>
                                                          @foreach($allSites ?? $sites as $site)
                                                              <th class="text-center" style="min-width: 110px;">
                                                                  <span class="fs-11 text-uppercase text-muted d-block" style="letter-spacing: 0.5px;">SITE</span>
                                                                  <strong>{{ $site->name }}</strong>
                                                              </th>
                                                          @endforeach
                                                          <th class="text-center bg-light" style="width: 130px;"><strong>TOTAL STOCK</strong></th>
                                                          <th class="text-center" style="width: 110px;"><strong>ACTION</strong></th>
                                                      </tr>
                                                  </thead>
                                                  <tbody>
                                                      {{-- 1. Books --}}
                                                      @foreach($books as $book)
                                                      @php
                                                          $bookInventories = $book->inventory->keyBy('site_id');
                                                          $totalSiteStock = $book->inventory->sum('quantity');
                                                      @endphp
                                                      <tr>
                                                          <td><span class="badge bg-danger">BOOK</span></td>
                                                          <td><strong>#{{ $book->sku ?? $book->id }}</strong></td>
                                                          <td class="fw-bold text-black">{{ $book->name }}</td>
                                                          @foreach($allSites ?? $sites as $site)
                                                              @php
                                                                  $siteQty = isset($bookInventories[$site->id]) ? (float)$bookInventories[$site->id]->quantity : 0;
                                                              @endphp
                                                              <td class="text-center">
                                                                  @if($siteQty > 0)
                                                                      <span class="badge bg-light text-success border border-success fw-bold px-2 py-1 fs-13">
                                                                          {{ number_format($siteQty) }}
                                                                      </span>
                                                                  @else
                                                                      <span class="text-muted small" style="opacity: 0.4;">0</span>
                                                                  @endif
                                                              </td>
                                                          @endforeach
                                                          <td class="text-center bg-light">
                                                              <span class="badge {{ $totalSiteStock > 0 ? 'bg-success' : 'bg-danger' }} fs-14 fw-bold px-3 py-2">
                                                                  {{ number_format($totalSiteStock) }}
                                                              </span>
                                                          </td>
                                                          <td class="text-center">
                                                              <button type="button" class="btn btn-sm btn-danger shadow-sm px-2 py-1 fw-semibold btn-open-stock-mgmt" title="Edit Stock" data-book-id="{{ $book->id }}" data-book-name="{{ $book->name }}" data-stock="{{ $totalSiteStock }}" data-max="{{ $book->max_stock ?? 0 }}">
                                                                  <i class="las la-pen me-1"></i>Edit
                                                              </button>
                                                          </td>
                                                      </tr>
                                                      @endforeach

                                                      {{-- 2. Book Indices --}}
                                                      @foreach($allIndices ?? $indices ?? [] as $index)
                                                      @php
                                                          $indexInventories = $index->inventory ? $index->inventory->keyBy('site_id') : collect([]);
                                                          $totalIndexStock = $index->inventory ? $index->inventory->sum('quantity') : 0;
                                                          $bookTitle = $index->book ? ($index->book->name ?? $index->book->title ?? '') : '';
                                                          $idxVal = $index->index_value ?? $index->name ?? ('Index #' . $index->id);
                                                          $displayTitle = $bookTitle ? ($bookTitle . ' — ' . $idxVal) : $idxVal;
                                                      @endphp
                                                      <tr>
                                                          <td><span class="badge bg-info text-white">INDEX</span></td>
                                                          <td><strong>#{{ $index->barcode ?? $index->article ?? $index->id }}</strong></td>
                                                          <td class="fw-bold text-black">{{ $displayTitle }}</td>
                                                          @foreach($allSites ?? $sites as $site)
                                                              @php
                                                                  $siteQty = isset($indexInventories[$site->id]) ? (float)$indexInventories[$site->id]->quantity : 0;
                                                              @endphp
                                                              <td class="text-center">
                                                                  @if($siteQty > 0)
                                                                      <span class="badge bg-light text-success border border-success fw-bold px-2 py-1 fs-13">
                                                                          {{ number_format($siteQty) }}
                                                                      </span>
                                                                  @else
                                                                      <span class="text-muted small" style="opacity: 0.4;">0</span>
                                                                  @endif
                                                              </td>
                                                          @endforeach
                                                          <td class="text-center bg-light">
                                                              <span class="badge {{ $totalIndexStock > 0 ? 'bg-success' : 'bg-danger' }} fs-14 fw-bold px-3 py-2">
                                                                  {{ number_format($totalIndexStock) }}
                                                              </span>
                                                          </td>
                                                          <td class="text-center">
                                                              <button type="button" class="btn btn-sm btn-info text-white shadow-sm px-2 py-1 fw-semibold btn-open-index-mgmt" title="Edit Index Stock" data-index-id="{{ $index->id }}" data-book-title="{{ $bookTitle ?: $displayTitle }}" data-index-val="{{ $idxVal }}" data-stock="{{ $totalIndexStock }}">
                                                                  <i class="las la-pen me-1"></i>Edit
                                                              </button>
                                                          </td>
                                                      </tr>
                                                      @endforeach

                                                      {{-- 3. Book Bundles --}}
                                                      @foreach($allBundles ?? $bundles ?? [] as $bundle)
                                                      @php
                                                          $bundleInventories = $bundle->inventory ? $bundle->inventory->keyBy('site_id') : collect([]);
                                                          $totalBundleStock = $bundle->inventory ? $bundle->inventory->sum('quantity') : 0;
                                                      @endphp
                                                      <tr>
                                                          <td><span class="badge bg-warning text-dark">BUNDLE</span></td>
                                                          <td><strong>#{{ $bundle->sku ?? ('BND-' . $bundle->id) }}</strong></td>
                                                          <td class="fw-bold text-black">{{ $bundle->name }}</td>
                                                          @foreach($allSites ?? $sites as $site)
                                                              @php
                                                                  $siteQty = isset($bundleInventories[$site->id]) ? (float)$bundleInventories[$site->id]->quantity : 0;
                                                              @endphp
                                                              <td class="text-center">
                                                                  @if($siteQty > 0)
                                                                      <span class="badge bg-light text-success border border-success fw-bold px-2 py-1 fs-13">
                                                                          {{ number_format($siteQty) }}
                                                                      </span>
                                                                  @else
                                                                      <span class="text-muted small" style="opacity: 0.4;">0</span>
                                                                  @endif
                                                              </td>
                                                          @endforeach
                                                          <td class="text-center bg-light">
                                                              <span class="badge {{ $totalBundleStock > 0 ? 'bg-success' : 'bg-danger' }} fs-14 fw-bold px-3 py-2">
                                                                  {{ number_format($totalBundleStock) }}
                                                              </span>
                                                          </td>
                                                          <td class="text-center">
                                                              <button type="button" class="btn btn-sm btn-warning text-dark shadow-sm px-2 py-1 fw-semibold btn-open-bundle-mgmt" title="Edit Bundle Stock" data-bundle-id="{{ $bundle->id }}" data-bundle-name="{{ $bundle->name }}" data-stock="{{ $totalBundleStock }}">
                                                                  <i class="las la-pen me-1"></i>Edit
                                                              </button>
                                                          </td>
                                                      </tr>
                                                      @endforeach

                                                      @if(count($books) == 0 && count($allIndices ?? $indices ?? []) == 0 && count($allBundles ?? $bundles ?? []) == 0)
                                                      <tr>
                                                          <td colspan="{{ count($allSites ?? $sites) + 5 }}" class="text-center py-4 text-muted">No items found in master registry.</td>
                                                      </tr>
                                                      @endif
                                                  </tbody>
                                              </table>
                                          </div>
                                          <div class="d-flex justify-content-between align-items-center mt-4">
                                              <div class="pagination-info">
                                                  Showing {{ $books->firstItem() ?? 0 }} to {{ $books->lastItem() ?? 0 }} of {{ $books->total() }} entries
                                              </div>
                                              <nav>
                                                  {{ $books->appends(['search' => request('search')])->links() }}
                                              </nav>
                                          </div>
                                      </div>

                                    

                                    <!-- Non-Books Registry Tab Pane -->
                                    <div class="tab-pane fade" id="registry-nonbooks-content" role="tabpanel" aria-labelledby="registry-nonbooks-tab">
                                        <div class="table-responsive">
                                            <table class="table table-responsive-md">
                                                <thead>
                                                    <tr>
                                                        <th><strong>ITEM ID</strong></th>
                                                        <th><strong>NAME</strong></th>
                                                        <th><strong>CATEGORY</strong></th>
                                                        <th><strong>UNIT COST</strong></th>
                                                        <th><strong>STOCK</strong></th>
                                                        <th><strong>MAX STOCK</strong></th>
                                                        <th><strong>STATUS</strong></th>
                                                        <th><strong>ACTION</strong></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($nonBooks as $item)
                                                    @php
                                                        // Get Main Warehouse inventory for this non-book
                                                        $mainWarehouseQuantity = 0;
                                                        if($mainWarehouse) {
                                                            $mainWarehouseQuantity = $mainWarehouse->inventory()
                                                                ->where('book_id', $item->id)
                                                                ->sum('quantity');
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td><strong>#{{ $item->sku }}</strong></td>
                                                        <td>{{ $item->name }}</td>
                                                        <td>{{ $item->category }}</td>
                                                        <td>₱{{ number_format($item->cost, 2) }}</td>
                                                        <td><strong>{{ $mainWarehouseQuantity }}</strong></td>
                                                        <td><strong>{{ $item->max_stock ?? 'N/A' }}</strong></td>
                                                        <td>
                                                            @if($mainWarehouseQuantity == 0)
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa fa-circle text-danger me-1"></i> Out of Stock
                                                                </div>
                                                            @elseif($mainWarehouseQuantity <= ($item->reorder_point ?? 0))
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa fa-circle text-warning me-1"></i> Low Stock
                                                                </div>
                                                            @else
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa fa-circle text-success me-1"></i> In Stock
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-danger" onclick="openStockManagementModal({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->stock }}, {{ $item->max_stock ?? 0 }})">
                                                                <i class="las la-pen"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center">No non-books found.</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-4">
                                            <div class="pagination-info">
                                                Showing {{ $nonBooks->firstItem() ?? 0 }} to {{ $nonBooks->lastItem() ?? 0 }} of {{ $nonBooks->total() }} entries
                                            </div>
                                            <nav>
                                                {{ $nonBooks->appends(['search' => request('search')])->links() }}
                                            </nav>
                                        </div>
                                    </div>

                                    <!-- Indices Registry Tab Pane -->
                                    <div class="tab-pane fade" id="registry-indices-content" role="tabpanel" aria-labelledby="registry-indices-tab">
                                        <div class="table-responsive">
                                            <table class="table table-responsive-md">
                                                <thead>
                                                    <tr>
                                                        <th><strong>ID</strong></th>
                                                        <th><strong>BOOK TITLE</strong></th>
                                                        <th><strong>INDEX VALUE</strong></th>
                                                        <th><strong>STOCK</strong></th>
                                                        <th><strong>STATUS</strong></th>
                                                        <th><strong>ACTION</strong></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($indices as $index)
                                                    @php
                                                        $mainWarehouseQty = 0;
                                                        if($mainWarehouse) {
                                                            $mainWarehouseQty = $mainWarehouse->inventory()
                                                                ->where('book_index_id', $index->id)
                                                                ->sum('quantity');
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td><strong>#IDX-{{ str_pad($index->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                                        <td>{{ $index->book->name ?? 'N/A' }}</td>
                                                        <td><span class="badge badge-info light">{{ $index->index_value }}</span></td>
                                                        <td><strong>{{ $mainWarehouseQty }}</strong></td>
                                                        <td>
                                                            @if($mainWarehouseQty == 0)
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa fa-circle text-danger me-1"></i> Out of Stock
                                                                </div>
                                                            @else
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa fa-circle text-success me-1"></i> In Stock
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-danger" onclick="openIndexStockModal({{ $index->id }}, '{{ addslashes($index->book->name ?? 'N/A') }}', '{{ addslashes($index->index_value) }}', {{ $mainWarehouseQty }})">
                                                                <i class="las la-pen"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center">No book indices found.</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-4">
                                            <div class="pagination-info">
                                                Showing {{ $indices->firstItem() ?? 0 }} to {{ $indices->lastItem() ?? 0 }} of {{ $indices->total() }} entries
                                            </div>
                                            <nav>
                                                {{ $indices->appends(['search' => request('search')])->links() }}
                                            </nav>
                                        </div>
                                    </div>

                                    <!-- Bundles Registry Tab Pane -->
                                    <div class="tab-pane fade" id="registry-bundles-content" role="tabpanel" aria-labelledby="registry-bundles-tab">
                                        <div class="table-responsive">
                                            <table class="table table-responsive-md">
                                                <thead>
                                                    <tr>
                                                        <th><strong>BUNDLE SKU</strong></th>
                                                        <th><strong>NAME</strong></th>
                                                        <th><strong>CONSTITUENT BOOKS</strong></th>
                                                        <th><strong>PRICE</strong></th>
                                                        <th><strong>STOCK</strong></th>
                                                        <th><strong>STATUS</strong></th>
                                                        <th><strong>ACTION</strong></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($bundles as $bundle)
                                                    @php
                                                        $mainWarehouseQty = 0;
                                                        if($mainWarehouse) {
                                                            $mainWarehouseQty = $mainWarehouse->inventory()
                                                                ->where('book_bundle_id', $bundle->id)
                                                                ->sum('quantity');
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td><strong>#{{ $bundle->sku ?? 'N/A' }}</strong></td>
                                                        <td>{{ $bundle->name }}</td>
                                                        <td>
                                                            <ul class="mb-0 list-unstyled">
                                                                @foreach($bundle->books as $b)
                                                                    <li><small>• {{ $b->name }} (Qty: {{ $b->pivot->quantity }})</small></li>
                                                                @endforeach
                                                            </ul>
                                                        </td>
                                                        <td>₱{{ number_format($bundle->price, 2) }}</td>
                                                        <td><strong>{{ $mainWarehouseQty }}</strong></td>
                                                        <td>
                                                            @if($mainWarehouseQty == 0)
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa fa-circle text-danger me-1"></i> Out of Stock
                                                                </div>
                                                            @else
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa fa-circle text-success me-1"></i> In Stock
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-danger" onclick="openBundleStockModal({{ $bundle->id }}, '{{ addslashes($bundle->name) }}', {{ $mainWarehouseQty }})">
                                                                <i class="las la-pen"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">No book bundles found.</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-4">
                                            <div class="pagination-info">
                                                Showing {{ $bundles->firstItem() ?? 0 }} to {{ $bundles->lastItem() ?? 0 }} of {{ $bundles->total() }} entries
                                            </div>
                                            <nav>
                                                {{ $bundles->appends(['search' => request('search')])->links() }}
                                            </nav>
                                        </div>
                                    </div>

                                    <!-- Lost Inventory Registry Tab Pane -->
                                    <div class="tab-pane fade" id="registry-lost-content" role="tabpanel" aria-labelledby="registry-lost-tab">
                                        <div class="table-responsive">
                                            <table class="table table-responsive-md align-middle table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th><strong>ITEM DESCRIPTION</strong></th>
                                                        <th class="text-center"><strong>TYPE</strong></th>
                                                        <th class="text-center"><strong>LOST QTY</strong></th>
                                                        <th><strong>SITE / WAREHOUSE</strong></th>
                                                        <th><strong>TEAM</strong></th>
                                                        <th><strong>DATE LOST</strong></th>
                                                        <th><strong>REASON / REMARKS</strong></th>
                                                        <th><strong>RECORDED BY</strong></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($lostInventories ?? [] as $lost)
                                                    <tr>
                                                        <td>
                                                            <div class="fw-bold text-dark">{{ $lost->product_name }}</div>
                                                            <small class="text-muted"><i class="las la-barcode me-1"></i>{{ $lost->sku_isbn }}</small>
                                                        </td>
                                                        <td class="text-center">
                                                            @if($lost->product_type === 'book')
                                                                <span class="badge bg-primary text-white px-2 py-1" style="font-size: 0.78rem;">Book</span>
                                                            @elseif($lost->product_type === 'index')
                                                                <span class="badge bg-info text-white px-2 py-1" style="font-size: 0.78rem;">Index</span>
                                                            @elseif($lost->product_type === 'bundle')
                                                                <span class="badge bg-warning text-white px-2 py-1" style="font-size: 0.78rem;">Bundle</span>
                                                            @else
                                                                <span class="badge bg-secondary text-white px-2 py-1" style="font-size: 0.78rem;">Non-Book</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="fw-bold text-danger fs-14">{{ number_format($lost->quantity) }} pcs</span>
                                                        </td>
                                                        <td>
                                                            @if($lost->site)
                                                                <span class="fw-semibold text-dark"><i class="las la-warehouse me-1 text-primary"></i>{{ $lost->site->name }}</span>
                                                            @else
                                                                <span class="text-muted small">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($lost->team_name)
                                                                <span class="badge bg-danger text-white px-2 py-1" style="font-size: 0.78rem;">{{ $lost->team_name }}</span>
                                                            @else
                                                                <span class="text-muted small">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <small class="text-dark fw-medium">{{ optional($lost->lost_date ?: $lost->created_at)->format('M d, Y h:i A') }}</small>
                                                        </td>
                                                        <td>
                                                            <small class="text-secondary fw-medium">{{ $lost->reason ?: 'No remarks provided' }}</small>
                                                        </td>
                                                        <td>
                                                            <small class="fw-semibold text-dark">{{ $lost->user->name ?? 'System' }}</small>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center py-4 text-muted">
                                                            <i class="las la-check-circle fs-24 text-success d-block mb-1"></i>
                                                            No lost inventory records found.
                                                        </td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        @if(isset($lostInventories) && method_exists($lostInventories, 'links'))
                                        <div class="d-flex justify-content-between align-items-center mt-4">
                                            <div class="pagination-info">
                                                Showing {{ $lostInventories->firstItem() ?? 0 }} to {{ $lostInventories->lastItem() ?? 0 }} of {{ $lostInventories->total() }} entries
                                            </div>
                                            <nav>
                                                {{ $lostInventories->appends(['search' => request('search')])->links() }}
                                            </nav>
                                        </div>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Stock Movements -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header border-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <h4 class="fs-20 mb-0 text-black">Recent Stock Movements</h4>
                                </div>
                                <ul class="nav nav-pills" id="movementFilterTabs" style="gap: 5px;">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link active btn-sm py-1 px-3 movement-tab-btn" onclick="filterStockMovements('all', this)">All</button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link btn-sm py-1 px-3 movement-tab-btn" onclick="filterStockMovements('so', this)">Sales Order</button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link btn-sm py-1 px-3 movement-tab-btn" onclick="filterStockMovements('pos', this)">POS</button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link btn-sm py-1 px-3 movement-tab-btn" onclick="filterStockMovements('ecom', this)">E-Com</button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link btn-sm py-1 px-3 movement-tab-btn" onclick="filterStockMovements('transfer', this)">Stock Transfer</button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link btn-sm py-1 px-3 movement-tab-btn" onclick="filterStockMovements('stockin', this)">Stock In</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th style="width:80px;"><strong>ITEM ID</strong></th>
                                                <th><strong>BOOK TITLE</strong></th>
                                                <th><strong>TYPE</strong></th>
                                                <th><strong>QUANTITY</strong></th>
                                                <th><strong>DATE</strong></th>
                                                <th><strong>STATUS</strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentMovements as $transaction)
                                            @php
                                                $src = strtolower(($transaction->source ?? '') . ' ' . ($transaction->notes ?? '') . ' ' . ($transaction->reference_number ?? ''));
                                                $cat = 'so';
                                                if (str_contains($src, 'transfer') || str_contains($src, 'st-')) {
                                                    $cat = 'transfer';
                                                } elseif ($transaction->type == 'in') {
                                                    $cat = 'stockin';
                                                } elseif (str_contains($src, 'pos')) {
                                                    $cat = 'pos';
                                                } elseif (str_contains($src, 'e-com') || str_contains($src, 'ecom') || str_contains($src, 'shopee') || str_contains($src, 'lazada') || str_contains($src, 'tiktok') || str_contains($src, 'website')) {
                                                    $cat = 'ecom';
                                                } elseif ($transaction->sales_order_item_id || str_contains($src, 'so-') || str_contains($src, 'sales order') || $transaction->type == 'out') {
                                                    $cat = 'so';
                                                }
                                            @endphp
                                            <tr class="stock-movement-row" data-movement-type="{{ $cat }}">
                                                <td><strong>#{{ $transaction->book->sku ?? $transaction->book_id }}</strong></td>
                                                <td>{{ $transaction->book->name ?? 'Unknown' }}</td>
                                                <td>
                                                    @if($cat == 'transfer')
                                                        <span class="badge light" style="background-color: #cfe2ff; color: #084298; font-weight: 600;">Stock Transfer</span>
                                                    @elseif($cat == 'stockin')
                                                        <span class="badge light badge-success">Stock In</span>
                                                    @elseif($cat == 'pos')
                                                        <span class="badge light" style="background-color: #e0cffc; color: #5925dc; font-weight: 600;">POS</span>
                                                    @elseif($cat == 'ecom')
                                                        <span class="badge light" style="background-color: #cff4fc; color: #055160; font-weight: 600;">E-Com</span>
                                                    @elseif($cat == 'so')
                                                        <span class="badge light" style="background-color: #fff3cd; color: #664d03; font-weight: 600;">Sales Order</span>
                                                    @else
                                                        <span class="badge light badge-danger">Stock Out</span>
                                                    @endif
                                                </td>
                                                <td class="{{ $transaction->type == 'out' ? 'text-danger' : 'text-success' }}">
                                                    {{ $transaction->type == 'out' ? '-' : '+' }}{{ $transaction->quantity }}
                                                </td>
                                                <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                                                <td>
                                                    @if($transaction->status == 'completed')
                                                        <span class="badge light badge-success">Completed</span>
                                                    @elseif($transaction->status == 'pending')
                                                        <span class="badge light badge-warning">Pending</span>
                                                    @else
                                                        <span class="badge light badge-danger">Cancelled</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No recent movements.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 gap-2">
                                    <div class="pagination-info" id="movementPaginationInfo">
                                        Showing 0 to 0 of 0 entries
                                    </div>
                                    <nav>
                                        <ul class="pagination pagination-xs mb-0" id="movementPaginationControls">
                                            <!-- Dynamic Pagination Buttons -->
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                let currentMovementCategory = 'all';
                let currentMovementPage = 1;
                const movementsPerPage = 10;

                function filterStockMovements(category, btn) {
                    if (btn) {
                        document.querySelectorAll('#movementFilterTabs .nav-link').forEach(el => el.classList.remove('active'));
                        btn.classList.add('active');
                    }
                    currentMovementCategory = category;
                    currentMovementPage = 1;
                    updateMovementPagination();
                }

                function changeMovementPage(page) {
                    currentMovementPage = page;
                    updateMovementPagination();
                }

                function updateMovementPagination() {
                    const allRows = Array.from(document.querySelectorAll('.stock-movement-row'));
                    const filteredRows = allRows.filter(row => {
                        return currentMovementCategory === 'all' || row.getAttribute('data-movement-type') === currentMovementCategory;
                    });

                    allRows.forEach(row => row.style.display = 'none');

                    const totalFiltered = filteredRows.length;
                    const totalPages = Math.ceil(totalFiltered / movementsPerPage) || 1;

                    if (currentMovementPage > totalPages) currentMovementPage = totalPages;
                    if (currentMovementPage < 1) currentMovementPage = 1;

                    const startIndex = (currentMovementPage - 1) * movementsPerPage;
                    const endIndex = Math.min(startIndex + movementsPerPage, totalFiltered);

                    for (let i = startIndex; i < endIndex; i++) {
                        if (filteredRows[i]) {
                            filteredRows[i].style.display = '';
                        }
                    }

                    const infoEl = document.getElementById('movementPaginationInfo');
                    if (infoEl) {
                        const fromCount = totalFiltered > 0 ? startIndex + 1 : 0;
                        infoEl.textContent = `Showing ${fromCount} to ${endIndex} of ${totalFiltered} entries`;
                    }

                    const controlsEl = document.getElementById('movementPaginationControls');
                    if (controlsEl) {
                        let html = '';
                        const prevDisabled = currentMovementPage === 1 ? 'disabled' : '';
                        html += `<li class="page-item ${prevDisabled}"><button type="button" class="page-link" style="white-space: nowrap; min-width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" onclick="changeMovementPage(${currentMovementPage - 1})" ${prevDisabled}><i class="las la-angle-left"></i></button></li>`;

                        const maxButtons = 5;
                        let startPage = Math.max(1, currentMovementPage - 2);
                        let endPage = Math.min(totalPages, startPage + maxButtons - 1);

                        if (endPage - startPage < maxButtons - 1) {
                            startPage = Math.max(1, endPage - maxButtons + 1);
                        }

                        if (startPage > 1) {
                            html += `<li class="page-item"><button type="button" class="page-link" style="min-width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" onclick="changeMovementPage(1)">1</button></li>`;
                            if (startPage > 2) {
                                html += `<li class="page-item disabled"><span class="page-link" style="min-width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">...</span></li>`;
                            }
                        }

                        for (let p = startPage; p <= endPage; p++) {
                            const activeClass = p === currentMovementPage ? 'active' : '';
                            html += `<li class="page-item ${activeClass}"><button type="button" class="page-link" style="min-width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" onclick="changeMovementPage(${p})">${p}</button></li>`;
                        }

                        if (endPage < totalPages) {
                            if (endPage < totalPages - 1) {
                                html += `<li class="page-item disabled"><span class="page-link" style="min-width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">...</span></li>`;
                            }
                            html += `<li class="page-item"><button type="button" class="page-link" style="min-width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" onclick="changeMovementPage(${totalPages})">${totalPages}</button></li>`;
                        }

                        const nextDisabled = currentMovementPage === totalPages || totalPages === 0 ? 'disabled' : '';
                        html += `<li class="page-item ${nextDisabled}"><button type="button" class="page-link" style="white-space: nowrap; min-width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" onclick="changeMovementPage(${currentMovementPage + 1})" ${nextDisabled}><i class="las la-angle-right"></i></button></li>`;

                        controlsEl.innerHTML = html;
                    }
                }

                document.addEventListener('DOMContentLoaded', function() {
                    updateMovementPagination();
                });
                        function switchConsignmentSubTab(btn, targetPaneId) {
            $('.c-sub-btn').removeClass('active btn-danger').addClass('btn-outline-danger');
            $(btn).addClass('active btn-danger').removeClass('btn-outline-danger');
            $('#area-consignment-pane, #direct-consignment-pane').removeClass('show active').css('display', 'none');
            $('#' + targetPaneId).addClass('show active').css('display', 'block');
        }
    </script>
            </div>

            <!-- Sites Tab Content -->
            <div class="tab-pane fade" id="sites-content" role="tabpanel" aria-labelledby="sites-tab">
                <!-- Sites List -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                                <h4 class="fs-20 mb-0 text-black">Site Management</h4>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSiteModal">
                                    <i class="las la-plus me-2"></i>Add Site
                                </button>
                            </div>
                            <div class="card-body">
                                {{-- Search bar for sites --}}
                                <div class="mb-3">
                                    <form method="GET" action="{{ url()->current() }}" class="d-flex gap-2">
                                        {{-- Preserve other query params --}}
                                        @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                                        @if(request('page'))<input type="hidden" name="page" value="{{ request('page') }}">@endif
                                        <div class="input-group" style="max-width: 350px;">
                                            <span class="input-group-text bg-white border-end-0"><i class="las la-search text-muted"></i></span>
                                            <input type="text" name="site_search" class="form-control border-start-0 ps-0" placeholder="Search sites..." value="{{ request('site_search') }}">
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-primary px-3">Search</button>
                                        @if(request('site_search'))
                                            <a href="{{ url()->current() }}?{{ http_build_query(request()->except('site_search', 'sites_page')) }}" class="btn btn-sm btn-outline-secondary px-3">Clear</a>
                                        @endif
                                    </form>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th><strong>SITE NAME</strong></th>
                                                <th><strong>CODE</strong></th>
                                                <th><strong>LOCATION</strong></th>
                                                <th><strong>TOTAL INVENTORY</strong></th>
                                                <th><strong>TOTAL VALUE</strong></th>
                                                <th><strong>STATUS</strong></th>
                                                <th><strong>ACTION</strong></th>
                                            </tr>
                                        </thead>
                                        <tbody id="sitesTableBody">
                                            @forelse($sites ?? [] as $site)
                                            <tr>
                                                <td><strong>{{ $site->name }}</strong></td>
                                                <td>{{ $site->code ?? 'N/A' }}</td>
                                                <td>{{ $site->location ?? 'N/A' }}</td>
                                                <td>{{ $site->getTotalInventoryQuantity() }} items</td>
                                                <td>₱{{ number_format($site->getTotalInventoryValue(), 2) }}</td>
                                                <td>
                                                    @if($site->is_active)
                                                        <span class="badge light badge-success">Active</span>
                                                    @else
                                                        <span class="badge light badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewSiteInventory{{ $site->id }}" title="View Inventory">
                                                        <i class="las la-boxes"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editSiteModal{{ $site->id }}" title="Edit Site">
                                                        <i class="las la-pen"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteSite({{ $site->id }}, '{{ $site->name }}')" title="Delete Site">
                                                        <i class="las la-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    @if(request('site_search'))
                                                        No sites found matching "{{ request('site_search') }}". <a href="{{ url()->current() }}?{{ http_build_query(request()->except('site_search', 'sites_page')) }}">Clear search</a>
                                                    @else
                                                        No sites found. <a href="#" data-bs-toggle="modal" data-bs-target="#addSiteModal">Add a new site</a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Pagination --}}
                                @if($sites instanceof \Illuminate\Pagination\LengthAwarePaginator && $sites->hasPages())
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="text-muted small">
                                        Showing {{ $sites->firstItem() }} to {{ $sites->lastItem() }} of {{ $sites->total() }} sites
                                    </div>
                                    <nav>
                                        {{ $sites->appends(request()->except('sites_page'))->links() }}
                                    </nav>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Transfers Section -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header border-0">
                                <h4 class="fs-20 mb-0 text-black">Pending Stock Transfers</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th><strong>FROM</strong></th>
                                                <th><strong>TO</strong></th>
                                                <th><strong>ITEM</strong></th>
                                                <th><strong>QUANTITY</strong></th>
                                                <th><strong>APPROVER</strong></th>
                                                <th><strong>STATUS</strong></th>
                                                <th><strong>ACTION</strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($pendingTransfers ?? [] as $transfer)
                                            <tr>
                                                <td>{{ $transfer->fromSite->name }}</td>
                                                <td>{{ $transfer->toSite->name }}</td>
                                                <td>{{ $transfer->item_name }}</td>
                                                <td><strong>{{ $transfer->quantity }}</strong></td>
                                                <td>{{ $transfer->approval_division ?? 'Production' }} Manager/Supervisor</td>
                                                <td>
                                                    @if($transfer->status == 'pending')
                                                        <span class="badge light badge-warning">Pending</span>
                                                    @elseif($transfer->status == 'completed')
                                                        <span class="badge light badge-success">Completed</span>
                                                    @else
                                                        <span class="badge light badge-danger">Rejected</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($transfer->status == 'pending' && $transfer->canBeApprovedBy(auth()->user()))
                                                        <button class="btn btn-xs btn-success" onclick="approveTransfer({{ $transfer->id }})">
                                                            <i class="las la-check"></i> Approve
                                                        </button>
                                                        <button class="btn btn-xs btn-danger" onclick="rejectTransfer({{ $transfer->id }})">
                                                            <i class="las la-times"></i> Reject
                                                        </button>
                                                    @elseif($transfer->status == 'pending')
                                                        <span class="text-muted small">Waiting for approval</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No pending transfers</td>
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

            <!-- Transfer Workflow Tab Content -->
            <div class="tab-pane fade" id="transfer-workflow-content" role="tabpanel" aria-labelledby="transfer-workflow-tab">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header border-0">
                                <div>
                                    <h4 class="fs-20 mb-0 text-black">Stock Transfer Workflow</h4>
                                    <small class="text-muted">Approval  Accounting → Logistics Assignment → Completion</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-responsive-md align-middle" id="stockTransferWorkflowTable">
                                        <thead>
                                            <tr>
                                                <th><strong>REF</strong></th>
                                                <th><strong>FROM / TO</strong></th>
                                                <th><strong>ITEM</strong></th>
                                                <th><strong>QTY</strong></th>
                                                <th><strong>REQUESTED BY</strong></th>
                                                <th><strong>ASSIGNED LOGISTICS</strong></th>
                                                <th><strong>STATUS</strong></th>
                                                <th><strong>ACTION</strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($stockTransferWorkflow ?? [] as $transfer)
                                            @php
                                                $itemCount = $transfer->items_count ?? 1;
                                                $totQty = $transfer->total_quantity ?? $transfer->quantity;
                                            @endphp
                                            <tr>
                                                <td><strong>ST-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                                <td>
                                                    <div>{{ $transfer->fromSite->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">to {{ $transfer->toSite->name ?? 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    @if($itemCount > 1)
                                                        <strong>Multiple Books</strong> <small class="text-muted">({{ $itemCount }} titles)</small>
                                                    @else
                                                        {{ $transfer->item_name ?? 'N/A' }}
                                                    @endif
                                                </td>
                                                <td><strong>{{ number_format($totQty) }}</strong></td>
                                                <td>{{ $transfer->createdBy->name ?? 'N/A' }}</td>
                                                <td>
                                                    @if($transfer->logisticsAssignedTo && $transfer->logistics_assigned_to != $transfer->created_by)
                                                        {{ $transfer->logisticsAssignedTo->name }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($transfer->status === 'pending')
                                                        <span class="badge light badge-warning">Manager/Supervisor Approval</span>
                                                    @elseif($transfer->status === 'accounting_review')
                                                        <span class="badge light badge-info">Accounting Review</span>
                                                    @elseif($transfer->status === 'logistics_assignment')
                                                        <span class="badge light badge-primary">For Logistics Assignment</span>
                                                    @elseif($transfer->status === 'logistics_assigned')
                                                        <span class="badge light badge-secondary">Assigned to Logistics</span>
                                                    @elseif($transfer->status === 'completed')
                                                        <span class="badge light badge-success">Completed</span>
                                                    @else
                                                        <span class="badge light badge-danger">Rejected</span>
                                                    @endif
                                                </td>
                                                <td style="min-width: 220px;">
                                                    <div class="d-flex align-items-center gap-1">
                                                        <button type="button" 
                                                                class="btn btn-xs btn-outline-info" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#viewWorkflowTransferModal" 
                                                                data-id="{{ $transfer->id }}"
                                                                data-ref="ST-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}"
                                                                data-from="{{ $transfer->fromSite->name ?? 'N/A' }}"
                                                                data-to="{{ $transfer->toSite->name ?? 'N/A' }}"
                                                                data-requested="{{ $transfer->createdBy->name ?? 'N/A' }}"
                                                                data-assigned="{{ ($transfer->logisticsAssignedTo && $transfer->logistics_assigned_to != $transfer->created_by) ? $transfer->logisticsAssignedTo->name : 'Unassigned' }}"
                                                                data-date="{{ optional($transfer->created_at)->format('M. d, Y h:i A') }}"
                                                                data-status="{{ ucfirst(str_replace('_', ' ', $transfer->status)) }}"
                                                                data-notes="{{ $transfer->notes ?? '' }}">
                                                            <i class="las la-eye"></i> View
                                                        </button>

                                                        @if($transfer->status === 'logistics_assigned' && $transfer->canBeCompletedBy(auth()->user()))
                                                            <button class="btn btn-xs btn-success" onclick="completeLogisticsTransfer({{ $transfer->id }})">
                                                                <i class="las la-check-double"></i> Mark Completed
                                                            </button>
                                                        @elseif($transfer->status === 'logistics_assignment' && ($isLogisticsAssigner ?? false))
                                                            <div class="d-flex gap-1">
                                                                <select class="form-control form-control-sm" id="assignLogistics{{ $transfer->id }}">
                                                                    <option value="">Select staff</option>
                                                                    @foreach($logisticsUsers ?? [] as $logisticsUser)
                                                                        <option value="{{ $logisticsUser->id }}" {{ $transfer->logistics_assigned_to == $logisticsUser->id ? 'selected' : '' }}>
                                                                            {{ $logisticsUser->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <button class="btn btn-xs btn-primary" onclick="assignLogisticsTransfer({{ $transfer->id }})">
                                                                    Assign
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No stock transfers found</td>
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
        </div>
    </div>

    <!-- Per-Site Modals -->
    @forelse($sites ?? [] as $site)
        <!-- View Site Inventory Modal -->
        <div class="modal fade site-inventory-modal" id="viewSiteInventory{{ $site->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h6 class="modal-title text-white"><i class="las la-boxes me-2"></i>Inventory at {{ $site->name }}</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-3" style="max-height: 85vh !important; overflow-y: auto !important; overflow-x: hidden !important;">
                        @php
                            $booksInv = $site->inventory->filter(fn($inv) => !empty($inv->book_id) && empty($inv->book_index_id) && empty($inv->book_bundle_id));
                            $indicesInv = $site->inventory->filter(fn($inv) => !empty($inv->book_index_id));
                            $bundlesInv = $site->inventory->filter(fn($inv) => !empty($inv->book_bundle_id));
                        @endphp

                        @if($site->inventory->count() > 0)
                            <!-- Nav tabs & Search inside modal -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 pb-2 border-bottom">
                                <ul class="nav nav-tabs nav-tabs-primary mb-0 border-bottom-0" role="tablist">
                                    <li class="nav-item">
                                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#site-books-{{ $site->id }}" type="button">
                                            <i class="las la-book me-1"></i> Books <span class="badge bg-primary text-white ms-1">{{ $booksInv->count() }}</span>
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#site-indices-{{ $site->id }}" type="button">
                                            <i class="las la-bookmark me-1"></i> Indices <span class="badge bg-info text-white ms-1">{{ $indicesInv->count() }}</span>
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#site-bundles-{{ $site->id }}" type="button">
                                            <i class="las la-boxes me-1"></i> Bundles <span class="badge bg-warning text-white ms-1">{{ $bundlesInv->count() }}</span>
                                        </button>
                                    </li>
                                </ul>
                                <div class="input-group input-group-sm" style="width: 280px; max-width: 100%;">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="las la-search fs-14"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0 site-inv-search" data-site-id="{{ $site->id }}" placeholder="Search title or item..." autocomplete="off">
                                    <button class="btn btn-outline-secondary btn-clear-site-search d-none" type="button" data-site-id="{{ $site->id }}" title="Clear search"><i class="las la-times"></i></button>
                                </div>
                            </div>

                            <div class="tab-content">
                                <!-- Books Tab -->
                                <div class="tab-pane fade show active" id="site-books-{{ $site->id }}">
                                    @if($booksInv->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle" id="site-books-table-{{ $site->id }}">
                                                <thead>
                                                    <tr>
                                                        <th>Book Title</th>
                                                        <th>Quantity</th>
                                                        <th>Reorder Point</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($booksInv as $inv)
                                                    <tr class="paginate-row">
                                                        <td>{{ $inv->book->name ?? 'Unknown' }}</td>
                                                        <td><strong>{{ $inv->quantity }}</strong></td>
                                                        <td>{{ $inv->reorder_point ?? 'N/A' }}</td>
                                                        <td>
                                                            @if($inv->getStockStatus() == 'out_of_stock')
                                                                <span class="badge light badge-danger">Out of Stock</span>
                                                            @elseif($inv->getStockStatus() == 'low_stock')
                                                                <span class="badge light badge-warning">Low Stock</span>
                                                            @else
                                                                <span class="badge light badge-success">In Stock</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-light text-center my-3 text-muted">No books inventory at this site</div>
                                    @endif
                                </div>

                                <!-- Indices Tab -->
                                <div class="tab-pane fade" id="site-indices-{{ $site->id }}">
                                    @if($indicesInv->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle" id="site-indices-table-{{ $site->id }}">
                                                <thead>
                                                    <tr>
                                                        <th>Index Name</th>
                                                        <th>Quantity</th>
                                                        <th>Reorder Point</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($indicesInv as $inv)
                                                    <tr class="paginate-row">
                                                        <td>
                                                            {{ $inv->bookIndex->book->name ?? 'Unknown Book' }}
                                                            <span class="badge bg-info text-white ms-1">{{ $inv->bookIndex->index_value ?? 'Index' }}</span>
                                                        </td>
                                                        <td><strong>{{ $inv->quantity }}</strong></td>
                                                        <td>{{ $inv->reorder_point ?? 'N/A' }}</td>
                                                        <td>
                                                            @if($inv->getStockStatus() == 'out_of_stock')
                                                                <span class="badge light badge-danger">Out of Stock</span>
                                                            @elseif($inv->getStockStatus() == 'low_stock')
                                                                <span class="badge light badge-warning">Low Stock</span>
                                                            @else
                                                                <span class="badge light badge-success">In Stock</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-light text-center my-3 text-muted">No index inventory at this site</div>
                                    @endif
                                </div>

                                <!-- Bundles Tab -->
                                <div class="tab-pane fade" id="site-bundles-{{ $site->id }}">
                                    @if($bundlesInv->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle" id="site-bundles-table-{{ $site->id }}">
                                                <thead>
                                                    <tr>
                                                        <th>Bundle Name</th>
                                                        <th>Quantity</th>
                                                        <th>Reorder Point</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($bundlesInv as $inv)
                                                    <tr class="paginate-row">
                                                        <td>
                                                            {{ $inv->bookBundle->name ?? 'Unknown Bundle' }}
                                                            <span class="badge bg-warning text-white ms-1">Bundle</span>
                                                        </td>
                                                        <td><strong>{{ $inv->quantity }}</strong></td>
                                                        <td>{{ $inv->reorder_point ?? 'N/A' }}</td>
                                                        <td>
                                                            @if($inv->getStockStatus() == 'out_of_stock')
                                                                <span class="badge light badge-danger">Out of Stock</span>
                                                            @elseif($inv->getStockStatus() == 'low_stock')
                                                                <span class="badge light badge-warning">Low Stock</span>
                                                            @else
                                                                <span class="badge light badge-success">In Stock</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-light text-center my-3 text-muted">No bundle inventory at this site</div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info text-center my-3">No inventory at this site</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Site Modal -->
        <div class="modal fade" id="editSiteModal{{ $site->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                        <h6 class="modal-title text-white">
                            <i class="las la-edit me-2"></i>
                            Edit {{ $site->name }}
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form class="editSiteForm" data-site-id="{{ $site->id }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label font-w600">Site Name *</label>
                                <input type="text" name="name" class="form-control" value="{{ $site->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-w600">Site Code</label>
                                <input type="text" name="code" class="form-control" value="{{ $site->code ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-w600">Location</label>
                                <input type="text" name="location" class="form-control" value="{{ $site->location ?? '' }}">
                            </div>
                            <div class="mb-0">
                                <label class="form-label font-w600">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ $site->description ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-warning">Update Site</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
    @endforelse

    <!-- Stock Management Modal -->
    <div class="modal fade" id="stockManagementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h6 class="modal-title m-0 text-white"><i class="las la-pen me-2"></i>Stock Management</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-w600">Book Name</label>
                        <input type="text" class="form-control" id="mgmtBookName" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-w600">Site *</label>
                        <select class="form-control" id="mgmtSiteSelect" onchange="onStockMgmtSiteChange()">
                            @foreach($allSites ?? $sites ?? [] as $site)
                                <option value="{{ $site->id }}">{{ $site->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-w600">Current Stock</label>
                            <input type="number" class="form-control" id="mgmtCurrentStock" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-w600">Max Stock</label>
                            <input type="number" class="form-control" id="mgmtMaxStock" disabled>
                        </div>
                    </div>

                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="addTab" data-bs-toggle="tab" data-bs-target="#addTabContent" type="button" role="tab" aria-controls="addTabContent" aria-selected="true">
                                <i class="las la-plus me-1"></i>Add Stock
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="editTab" data-bs-toggle="tab" data-bs-target="#editTabContent" type="button" role="tab" aria-controls="editTabContent" aria-selected="false">
                                <i class="las la-edit me-1"></i>Edit Stock
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="addTabContent" role="tabpanel" aria-labelledby="addTab">
                            <div class="mb-3">
                                <label class="form-label font-w600">Quantity to Add *</label>
                                <input type="number" class="form-control" id="mgmtAddQuantity" placeholder="Enter quantity" min="1">
                                <small class="text-muted" id="mgmtAddWarning"></small>
                            </div>
                            <div class="alert alert-info" id="mgmtAddPreview" style="display:none;">
                                <small><strong>New Stock:</strong> <span id="mgmtAddNewStock">0</span></small>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="editTabContent" role="tabpanel" aria-labelledby="editTab">
                            <div class="mb-3">
                                <label class="form-label font-w600">Set Stock to *</label>
                                <input type="number" class="form-control" id="mgmtEditQuantity" placeholder="Enter new stock value" min="0">
                                <small class="text-muted" id="mgmtEditWarning"></small>
                            </div>
                            <div class="alert alert-info" id="mgmtEditPreview" style="display:none;">
                                <small><strong>Change from:</strong> <span id="mgmtEditOldStock">0</span> to <span id="mgmtEditNewStock">0</span></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary" id="mgmtSaveBtn" onclick="saveStockManagement()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Index Stock Management Modal -->
    <div class="modal fade" id="indexStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h6 class="modal-title m-0 text-white"><i class="las la-pen me-2"></i>Index Stock Management</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-w600">Book Name</label>
                        <input type="text" class="form-control" id="mgmtIndexBookName" disabled>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-w600">Index Value</label>
                            <input type="text" class="form-control" id="mgmtIndexValue" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-w600">Current Stock</label>
                            <input type="number" class="form-control" id="mgmtIndexCurrentStock" disabled>
                        </div>
                    </div>

                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="idxAddTab" data-bs-toggle="tab" data-bs-target="#idxAddTabContent" type="button" role="tab" aria-controls="idxAddTabContent" aria-selected="true">
                                <i class="las la-plus me-1"></i>Add Stock
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="idxEditTab" data-bs-toggle="tab" data-bs-target="#idxEditTabContent" type="button" role="tab" aria-controls="idxEditTabContent" aria-selected="false">
                                <i class="las la-edit me-1"></i>Edit Stock
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="idxAddTabContent" role="tabpanel" aria-labelledby="idxAddTab">
                            <div class="mb-3">
                                <label class="form-label font-w600">Quantity to Add *</label>
                                <input type="number" class="form-control" id="mgmtIndexAddQuantity" placeholder="Enter quantity" min="1">
                            </div>
                            <div class="alert alert-info" id="mgmtIndexAddPreview" style="display:none;">
                                <small><strong>New Stock:</strong> <span id="mgmtIndexAddNewStock">0</span></small>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="idxEditTabContent" role="tabpanel" aria-labelledby="idxEditTab">
                            <div class="mb-3">
                                <label class="form-label font-w600">Set Stock to *</label>
                                <input type="number" class="form-control" id="mgmtIndexEditQuantity" placeholder="Enter new stock value" min="0">
                            </div>
                            <div class="alert alert-info" id="mgmtIndexEditPreview" style="display:none;">
                                <small><strong>Change from:</strong> <span id="mgmtIndexEditOldStock">0</span> to <span id="mgmtIndexEditNewStock">0</span></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary" id="mgmtIndexSaveBtn" onclick="saveIndexStockManagement()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bundle Stock Management Modal -->
    <div class="modal fade" id="bundleStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h6 class="modal-title m-0 text-white"><i class="las la-pen me-2"></i>Bundle Stock Management</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-w600">Bundle Name</label>
                        <input type="text" class="form-control" id="mgmtBundleName" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-w600">Current Stock</label>
                        <input type="number" class="form-control" id="mgmtBundleCurrentStock" disabled>
                    </div>

                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="bndlAddTab" data-bs-toggle="tab" data-bs-target="#bndlAddTabContent" type="button" role="tab" aria-controls="bndlAddTabContent" aria-selected="true">
                                <i class="las la-plus me-1"></i>Add Stock
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="bndlEditTab" data-bs-toggle="tab" data-bs-target="#bndlEditTabContent" type="button" role="tab" aria-controls="bndlEditTabContent" aria-selected="false">
                                <i class="las la-edit me-1"></i>Edit Stock
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="bndlAddTabContent" role="tabpanel" aria-labelledby="bndlAddTab">
                            <div class="mb-3">
                                <label class="form-label font-w600">Quantity to Add *</label>
                                <input type="number" class="form-control" id="mgmtBundleAddQuantity" placeholder="Enter quantity" min="1">
                            </div>
                            <div class="alert alert-info" id="mgmtBundleAddPreview" style="display:none;">
                                <small><strong>New Stock:</strong> <span id="mgmtBundleAddNewStock">0</span></small>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="bndlEditTabContent" role="tabpanel" aria-labelledby="bndlEditTab">
                            <div class="mb-3">
                                <label class="form-label font-w600">Set Stock to *</label>
                                <input type="number" class="form-control" id="mgmtBundleEditQuantity" placeholder="Enter new stock value" min="0">
                            </div>
                            <div class="alert alert-info" id="mgmtBundleEditPreview" style="display:none;">
                                <small><strong>Change from:</strong> <span id="mgmtBundleEditOldStock">0</span> to <span id="mgmtBundleEditNewStock">0</span></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary" id="mgmtBundleSaveBtn" onclick="saveBundleStockManagement()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Site Modal -->
    <div class="modal fade" id="addSiteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h6 class="modal-title text-white"><i class="las la-plus me-2"></i>Add New Site</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addSiteForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label font-w600">Site Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g., Main Warehouse" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-w600">Site Code</label>
                            <input type="text" name="code" class="form-control" placeholder="e.g., WH-001">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-w600">Location</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g., Quezon City">
                        </div>
                        <div class="mb-0">
                            <label class="form-label font-w600">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Site description..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary">Add Site</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Transfer Stock Modal -->
    <div class="modal fade" id="transferStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header text-white d-flex justify-content-between align-items-center" style="background-color: #dc3545;">
                    <h6 class="modal-title text-white m-0"><i class="las la-exchange-alt me-2"></i>Transfer Stock (Multiple Books)</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="transferStockForm">
                    @csrf
                    <div class="modal-body">
                        <!-- From Site Selection -->
                        <div class="mb-3">
                            <label class="form-label font-w600">From Site *</label>
                            <select name="from_site_id" class="form-control select2-single" required id="fromSiteSelect" style="width: 100%;">
                                <option value="">-- Select Source Site --</option>
                                @foreach($allSites ?? $sites ?? [] as $site)
                                    <option value="{{ $site->id }}">{{ $site->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Multiple Books to Transfer Section -->
                        <div class="mb-4">
                            <label class="form-label font-w600">Items to Transfer</label>
                            <div class="table-responsive" style="max-height: 250px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 6px;">
                                <table class="table table-bordered mb-0" id="transferBooksTable" style="width:100%;">
                                    <thead class="table-light" style="position: sticky; top: 0; z-index: 2; background-color: #f8f9fa;">
                                        <tr>
                                            <th style="width: 40%;">Item</th>
                                            <th style="width: 25%;">Quantity</th>
                                            <th style="width: 25%;">Available</th>
                                            <th style="width: 10%;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="transferBooksBody">
                                        <tr id="emptyBooksRow">
                                            <td colspan="4" class="text-center text-muted py-3">Select a source site above, then click an Add button to start.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Add Item Buttons -->
                        <div class="mb-4 d-flex flex-wrap gap-2" style="position: relative; z-index: 1;">
                            <button type="button" class="btn btn-primary" id="showAddBookBtn" disabled style="pointer-events: auto;">
                                <i class="las la-plus me-1"></i>Add Book
                            </button>
                            <button type="button" class="btn btn-info text-white" id="showAddIndexBtn" disabled style="pointer-events: auto;">
                                <i class="las la-plus me-1"></i>Add Index
                            </button>
                            <button type="button" class="btn btn-warning text-white" id="showAddBundleBtn" disabled style="pointer-events: auto;">
                                <i class="las la-plus me-1"></i>Add Bundle
                            </button>
                        </div>

                        <!-- Dynamic Add Item Form (Hidden by default) -->
                        <div id="addBookForm" style="display: none; position: relative; z-index: 1;" class="mb-4 p-3 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0" id="addBookFormTitle"><i class="las la-plus-circle me-2"></i>Add New Book</h6>
                                <button type="button" class="btn-close" id="closeAddBookForm"></button>
                            </div>
                            <div class="row g-3">
                                <!-- Hidden type select (value set by Javascript buttons) -->
                                <select id="itemTypeSelect" style="display: none !important;">
                                    <option value="book">Book / Non-Book</option>
                                    <option value="index">Book Index</option>
                                    <option value="bundle">Book Bundle</option>
                                </select>
                                
                                <div class="col-md-8">
                                    <label class="form-label font-w600" id="itemSelectLabel">Select Item *</label>
                                    <select id="bookSelect" class="form-control select2-single" style="width: 100%;">
                                        <option value="">-- Select an Item --</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-w600">Quantity *</label>
                                    <input type="number" id="bookQuantity" class="form-control" placeholder="Qty" min="1" disabled>
                                </div>
                                <div class="col-12">
                                    <button type="button" id="confirmAddBookBtn" class="btn btn-success w-100" style="padding: 0.75rem 1rem; font-size: 0.95rem; background-color: #68CF29; border-color: #68CF29; color: #000; font-weight: 600;">
                                        ADD TO TRANSFER
                                    </button>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted">Available stock: <span id="selectedBookAvailable">0</span></small>
                                </div>
                            </div>
                        </div>

                        <!-- To Site -->
                        <div class="mb-3">
                            <label class="form-label font-w600">To Site *</label>
                            <select name="to_site_id" class="form-control select2-single" required id="toSiteSelect" style="width: 100%;">
                                <option value="">-- Select Destination Site --</option>
                                @foreach($allSites ?? $sites ?? [] as $site)
                                    <option value="{{ $site->id }}">{{ $site->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="mb-0">
                            <label class="form-label font-w600">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Transfer notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-danger" id="submitTransferBtn" disabled>
                            <i class="las la-check me-1"></i>Request Transfer (0 books)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Workflow Guide Modal -->
    <div class="modal fade" id="workflowGuideModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h6 class="modal-title text-white"><i class="las la-lightbulb me-2"></i>Multi-Site Inventory & Stock Transfer Workflow</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 class="mb-3">📍 Step-by-Step Guide</h6>
                    
                    <div class="timeline-item mb-4">
                        <div class="timeline-marker bg-primary"><strong>1</strong></div>
                        <div class="timeline-content">
                            <h6 class="font-w600">Create Site</h6>
                            <p class="text-muted mb-0">Click "Add Site" to create a new site location. Set the name, code, location, and description.</p>
                        </div>
                    </div>

                    <div class="timeline-item mb-4">
                        <div class="timeline-marker bg-success"><strong>2</strong></div>
                        <div class="timeline-content">
                            <h6 class="font-w600">Add Stock to Site</h6>
                            <p class="text-muted mb-0">Click the <i class="las la-plus"></i> button on any site to add inventory. Select the book, quantity, and optional reorder point.</p>
                        </div>
                    </div>

                    <div class="timeline-item mb-4">
                        <div class="timeline-marker bg-warning"><strong>3</strong></div>
                        <div class="timeline-content">
                            <h6 class="font-w600">View Site Inventory</h6>
                            <p class="text-muted mb-0">Click the <i class="las la-boxes"></i> button to see all items in that warehouse and their stock levels.</p>
                        </div>
                    </div>

                    <div class="timeline-item mb-4">
                        <div class="timeline-marker bg-danger"><strong>4</strong></div>
                        <div class="timeline-content">
                            <h6 class="font-w600">Request Stock Transfer</h6>
                            <p class="text-muted mb-0">Click the "Transfer Stock" button, select source site, add books to transfer, select destination site, and submit.</p>
                        </div>
                    </div>

                    <div class="timeline-item mb-4">
                        <div class="timeline-marker bg-success"><strong>5</strong></div>
                        <div class="timeline-content">
                            <h6 class="font-w600">Manager Approval</h6>
                            <p class="text-muted mb-0">Manager reviews pending transfers and clicks Approve or Reject.</p>
                        </div>
                    </div>

                    <div class="timeline-item mb-0">
                        <div class="timeline-marker bg-info"><strong>6</strong></div>
                        <div class="timeline-content">
                            <h6 class="font-w600">Automatic Stock Update</h6>
                            <p class="text-muted mb-0">Once approved, stock is automatically deducted from source and added to destination.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Workflow Stock Transfer Modal -->
    <div class="modal fade" id="viewWorkflowTransferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <div>
                        <h5 class="modal-title text-white fw-bold"><i class="las la-boxes me-2"></i>Stock Transfer Request Details</h5>
                        <small class="text-white-50" id="wf-modal-ref-sub">Ref: ST-00000</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
                    <!-- Info Cards Grid -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded border">
                                <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.75rem;">REQUESTED BY</small>
                                <span class="fw-bold text-dark fs-14" id="wf-modal-requested">N/A</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded border">
                                <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.75rem;">TRANSFER ROUTE</small>
                                <span class="fw-bold text-dark fs-14" id="wf-modal-route">N/A -> N/A</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded border">
                                <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.75rem;">ASSIGNED LOGISTICS</small>
                                <span class="fw-bold text-primary fs-14" id="wf-modal-assigned">Unassigned</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border">
                                <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.75rem;">DATE SUBMITTED</small>
                                <span class="fw-semibold text-dark" id="wf-modal-date">N/A</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border">
                                <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.75rem;">STATUS</small>
                                <div><span class="badge bg-secondary fs-13" id="wf-modal-status-badge">Pending</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Books Table -->
                    <div class="card border mb-3">
                        <div class="card-header bg-white py-2">
                            <h6 class="mb-0 fw-bold text-dark"><i class="las la-book me-1 text-danger"></i>Books / Items Included (<span id="wf-modal-total-summary">0 items</span>)</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                                        <tr>
                                            <th>Book Title / Code</th>
                                            <th>Type</th>
                                            <th class="text-center">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody id="wf-modal-items-body">
                                        <!-- Dynamic rows -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Row -->
                    <div id="wf-modal-notes-container" style="display: none;">
                        <h6 class="fw-bold fs-13 text-muted mb-1">NOTES / REMARKS</h6>
                        <div class="p-2 rounded bg-light border font-monospace fs-13" id="wf-modal-notes"></div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px;"></div>
@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        function switchConsignmentSubTab(btn, targetPaneId) {
            $('.c-sub-btn').removeClass('active btn-danger').addClass('btn-outline-danger');
            $(btn).addClass('active btn-danger').removeClass('btn-outline-danger');
            $('#area-consignment-pane, #direct-consignment-pane').removeClass('show active').css('display', 'none');
            $('#' + targetPaneId).addClass('show active').css('display', 'block');
        }
        var workflowBatchData = @json($batchData ?? []);

        $(document).on('show.bs.modal', '#viewWorkflowTransferModal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var ref = button.data('ref') || ('ST-' + String(id).padStart(5, '0'));
            var from = button.data('from') || 'N/A';
            var to = button.data('to') || 'N/A';
            var requested = button.data('requested') || 'N/A';
            var assigned = button.data('assigned') || 'Unassigned';
            var date = button.data('date') || 'N/A';
            var status = button.data('status') || 'Pending';
            var notes = button.data('notes') || '';

            var modal = $(this);
            modal.find('#wf-modal-ref-sub').text('Ref: ' + ref);
            modal.find('#wf-modal-requested').text(requested);
            modal.find('#wf-modal-route').text(from + ' → ' + to);
            modal.find('#wf-modal-assigned').text(assigned);
            modal.find('#wf-modal-date').text(date);
            modal.find('#wf-modal-status-badge').text(status);

            var itemsData = [];
            var batchInfo = workflowBatchData[id];
            if (batchInfo && Array.isArray(batchInfo.items) && batchInfo.items.length > 0) {
                itemsData = batchInfo.items;
            } else {
                itemsData = [{ name: 'Stock Item', type: 'Book', quantity: 1 }];
            }

            var rowsHtml = '';
            var totalQty = 0;
            itemsData.forEach(function(item) {
                var qty = parseInt(item.quantity) || 0;
                totalQty += qty;
                var typeColor = item.type === 'Book' ? 'success' : (item.type === 'Bundle' ? 'warning' : 'secondary');
                rowsHtml += `<tr>
                    <td class="fw-semibold text-dark">${item.name || 'Unknown Item'}</td>
                    <td><span class="badge bg-${typeColor}">${item.type || 'Item'}</span></td>
                    <td class="text-center fw-bold text-success">${qty} pcs</td>
                </tr>`;
            });
            if (itemsData.length > 1) {
                rowsHtml += `<tr class="table-light fw-bold">
                    <td colspan="2" class="text-end small">Total Batch Units:</td>
                    <td class="text-center text-success">${totalQty} pcs</td>
                </tr>`;
            }

            modal.find('#wf-modal-items-body').html(rowsHtml);
            modal.find('#wf-modal-total-summary').text(itemsData.length + ' title(s) · ' + totalQty + ' pcs total');

            if (notes && notes.trim() !== '') {
                modal.find('#wf-modal-notes').text(notes);
                modal.find('#wf-modal-notes-container').show();
            } else {
                modal.find('#wf-modal-notes-container').hide();
            }
        });
        function showNotification(message, type = 'success') {
            let toastContainer = document.getElementById('toastContainer');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toastContainer';
                toastContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px;';
                document.body.appendChild(toastContainer);
            }
            
            const toastId = 'toast-' + Date.now();
            const bgColor = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-warning';
            const icon = type === 'success' ? 'la-check-circle' : type === 'error' ? 'la-exclamation-circle' : 'la-info-circle';
            
            const toastHTML = `
                <div id="${toastId}" class="toast show text-white ${bgColor}" role="alert" style="min-width: 280px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border: none;">
                    <div class="toast-header ${bgColor} text-white border-0">
                        <i class="las ${icon} me-2"></i>
                        <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body pt-0">
                        ${message}
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            
            const toastElement = document.getElementById(toastId);
            if (toastElement && typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                try {
                    const bsToast = new bootstrap.Toast(toastElement);
                    bsToast.show();
                    toastElement.addEventListener('hidden.bs.toast', function() {
                        toastElement.remove();
                    });
                } catch(e) {}
            }
            
            setTimeout(() => {
                toastElement?.remove();
            }, 4000);
        }

        function closeModal(modalId) {
            const modalEl = document.getElementById(modalId);
            if (!modalEl) return;
            try {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const inst = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
                    if (inst) inst.hide();
                }
                if (window.jQuery && typeof jQuery.fn.modal === 'function') {
                    $(modalEl).modal('hide');
                }
            } catch (e) {}
            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
            document.body.classList.remove('modal-open');
            document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
        }

@php
    $sitesInventoryMap = [];
    foreach($allSites ?? $sites ?? [] as $s) {
        $sitesInventoryMap[$s->id] = $s->inventory ? $s->inventory->map(function($inv) {
            return [
                'book_id' => $inv->book_id,
                'book_index_id' => $inv->book_index_id,
                'book_bundle_id' => $inv->book_bundle_id,
                'quantity' => (float)$inv->quantity,
                'max_stock' => $inv->max_stock
            ];
        })->values()->toArray() : [];
    }
@endphp
        window.sitesInventoryData = window.sitesInventoryData || @json($sitesInventoryMap);

        let currentBookName = null;
        let currentStock = 0;
        let maxStock = null;
        let currentBookId = null;
        let globalBookMaxStock = null;
        let stockMgmtAddHandler = null;
        let stockMgmtEditHandler = null;

        function onStockMgmtSiteChange() {
            if (!currentBookId) return;
            const siteSelect = document.getElementById('mgmtSiteSelect');
            if (!siteSelect) return;
            const siteId = parseInt(siteSelect.value);
            if (!siteId) return;

            const inventory = (typeof window.sitesInventoryData !== 'undefined' && window.sitesInventoryData[siteId]) ? window.sitesInventoryData[siteId] : [];
            const item = inventory.find(i => i.book_id === currentBookId);
            
            const stockVal = item ? item.quantity : 0;
            const siteMaxStock = (item && item.max_stock !== null) ? item.max_stock : (globalBookMaxStock || null);

            currentStock = stockVal;
            maxStock = siteMaxStock;

            const currentStockInput = document.getElementById('mgmtCurrentStock');
            if (currentStockInput) currentStockInput.value = currentStock;

            const maxStockInput = document.getElementById('mgmtMaxStock');
            if (maxStockInput) maxStockInput.value = maxStock !== null ? maxStock : 'Not Set';

            // Trigger inputs update to refresh previews/warnings
            const addQtyInput = document.getElementById('mgmtAddQuantity');
            if (addQtyInput && addQtyInput.value) {
                addQtyInput.dispatchEvent(new Event('input'));
            }
            const editQtyInput = document.getElementById('mgmtEditQuantity');
            if (editQtyInput && editQtyInput.value) {
                editQtyInput.dispatchEvent(new Event('input'));
            }
        }

        function showModalSafely(modalId) {
            const modalEl = document.getElementById(modalId);
            if (!modalEl) return;
            try {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const inst = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
                    if (inst) {
                        inst.show();
                        return;
                    }
                }
            } catch (e) {
                console.warn('Bootstrap modal error, falling back to jQuery/DOM:', e);
            }
            if (window.jQuery && typeof jQuery.fn.modal === 'function') {
                $(modalEl).modal('show');
            } else {
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
                document.body.classList.add('modal-open');
            }
        }

        function openStockManagementModal(bookId, bookName, stock, max) {
            currentBookId = bookId;
            currentBookName = bookName;
            globalBookMaxStock = max;

            const nameEl = document.getElementById('mgmtBookName');
            if (nameEl) nameEl.value = bookName;
            
            // Default select site to "Main Warehouse" if it exists
            const siteSelect = document.getElementById('mgmtSiteSelect');
            if (siteSelect) {
                let mainWarehouseOption = [...siteSelect.options].find(opt => opt.text.trim() === 'Main Warehouse');
                if (mainWarehouseOption) {
                    siteSelect.value = mainWarehouseOption.value;
                } else if (siteSelect.options.length > 0) {
                    siteSelect.selectedIndex = 0;
                }
            }

            // Sync values for selected site
            onStockMgmtSiteChange();
            
            const addQtyInput = document.getElementById('mgmtAddQuantity');
            if (addQtyInput) addQtyInput.value = '';
            const addWarningEl = document.getElementById('mgmtAddWarning');
            if (addWarningEl) addWarningEl.innerHTML = '';
            const addPreviewEl = document.getElementById('mgmtAddPreview');
            if (addPreviewEl) addPreviewEl.style.display = 'none';

            const editQtyInput = document.getElementById('mgmtEditQuantity');
            if (editQtyInput) editQtyInput.value = '';
            const editWarningEl = document.getElementById('mgmtEditWarning');
            if (editWarningEl) editWarningEl.innerHTML = '';
            const editPreviewEl = document.getElementById('mgmtEditPreview');
            if (editPreviewEl) editPreviewEl.style.display = 'none';

            const saveBtn = document.getElementById('mgmtSaveBtn');
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = 'Save Changes';
            }

            // Force visual and functional reset of active tab to "Add Stock"
            const addTabBtn = document.getElementById('addTab');
            const editTabBtn = document.getElementById('editTab');
            const addTabPane = document.getElementById('addTabContent');
            const editTabPane = document.getElementById('editTabContent');

            if (addTabBtn && editTabBtn && addTabPane && editTabPane) {
                addTabBtn.classList.add('active');
                addTabBtn.setAttribute('aria-selected', 'true');
                editTabBtn.classList.remove('active');
                editTabBtn.setAttribute('aria-selected', 'false');

                addTabPane.classList.add('show', 'active');
                editTabPane.classList.remove('show', 'active');
            }

            if (stockMgmtAddHandler && addQtyInput) {
                addQtyInput.removeEventListener('input', stockMgmtAddHandler);
            }
            if (stockMgmtEditHandler && editQtyInput) {
                editQtyInput.removeEventListener('input', stockMgmtEditHandler);
            }

            stockMgmtAddHandler = function() {
                const quantity = parseInt(this.value) || 0;
                const newStock = currentStock + quantity;
                const warning = document.getElementById('mgmtAddWarning');
                const preview = document.getElementById('mgmtAddPreview');

                if (quantity > 0) {
                    if (preview) preview.style.display = 'block';
                    const newStockEl = document.getElementById('mgmtAddNewStock');
                    if (newStockEl) newStockEl.textContent = newStock;

                    if (warning) {
                        if (maxStock && newStock > maxStock) {
                            warning.innerHTML = `<span class="text-warning"><i class="las la-exclamation-triangle"></i> Notice: New stock (${newStock}) exceeds max stock limit (${maxStock})</span>`;
                        } else {
                            warning.innerHTML = '';
                        }
                    }
                    if (saveBtn) saveBtn.disabled = false;
                } else {
                    if (preview) preview.style.display = 'none';
                    if (warning) warning.innerHTML = '';
                    if (saveBtn) saveBtn.disabled = false;
                }
            };

            stockMgmtEditHandler = function() {
                const newStock = parseInt(this.value);
                const warning = document.getElementById('mgmtEditWarning');
                const preview = document.getElementById('mgmtEditPreview');

                if (!isNaN(newStock) && newStock >= 0) {
                    if (preview) preview.style.display = 'block';
                    const oldStockEl = document.getElementById('mgmtEditOldStock');
                    if (oldStockEl) oldStockEl.textContent = currentStock;
                    const editNewStockEl = document.getElementById('mgmtEditNewStock');
                    if (editNewStockEl) editNewStockEl.textContent = newStock;

                    if (warning) {
                        if (maxStock && newStock > maxStock) {
                            warning.innerHTML = `<span class="text-warning"><i class="las la-exclamation-triangle"></i> Notice: New stock (${newStock}) exceeds max stock limit (${maxStock})</span>`;
                        } else {
                            warning.innerHTML = '';
                        }
                    }
                    if (saveBtn) saveBtn.disabled = false;
                } else {
                    if (preview) preview.style.display = 'none';
                    if (warning) warning.innerHTML = '';
                    if (saveBtn) saveBtn.disabled = false;
                }
            };

            if (addQtyInput) addQtyInput.addEventListener('input', stockMgmtAddHandler);
            if (editQtyInput) editQtyInput.addEventListener('input', stockMgmtEditHandler);

            showModalSafely('stockManagementModal');
        }

        function saveStockManagement() {
            const addPane = document.getElementById('addTabContent');
            const editPane = document.getElementById('editTabContent');
            
            const isAddActive = addPane && (addPane.classList.contains('active') || addPane.classList.contains('show'));
            const isEditActive = editPane && (editPane.classList.contains('active') || editPane.classList.contains('show'));

            const addQtyVal = document.getElementById('mgmtAddQuantity')?.value;
            const editQtyVal = document.getElementById('mgmtEditQuantity')?.value;

            if (isAddActive || (addQtyVal && !editQtyVal)) {
                saveAddStock();
            } else if (isEditActive || editQtyVal) {
                saveEditStock();
            } else {
                saveAddStock();
            }
        }

        function saveAddStock() {
            const saveBtn = document.getElementById('mgmtSaveBtn');
            const originalText = saveBtn ? saveBtn.innerHTML : 'Save Changes';

            try {
                const quantityInput = document.getElementById('mgmtAddQuantity');
                const quantity = parseInt(quantityInput ? quantityInput.value : '');
                const siteId = document.getElementById('mgmtSiteSelect')?.value;

                if (!siteId) {
                    showNotification('Please select a site', 'warning');
                    return;
                }

                if (!quantity || isNaN(quantity) || quantity < 1) {
                    showNotification('Please enter a valid quantity to add', 'warning');
                    return;
                }

                const newStock = currentStock + quantity;



                if (!currentBookId) {
                    showNotification('No item selected', 'error');
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<i class="las la-spinner la-spin me-1"></i>Saving...';
                }

                fetch(`/production/inventory/update-stock/${currentBookId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'add',
                        site_id: siteId,
                        quantity: quantity,
                        new_stock: newStock
                    })
                })
                .then(async response => {
                    const data = await response.json();
                    if (response.ok && data.success) {
                        showNotification(data.message || 'Stock added successfully!', 'success');
                        closeModal('stockManagementModal');
                        setTimeout(() => location.reload(), 200);
                    } else {
                        showNotification('Error: ' + (data.message || 'Failed to update stock'), 'error');
                        if (saveBtn) {
                            saveBtn.disabled = false;
                            saveBtn.innerHTML = originalText;
                        }
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showNotification('An error occurred while adding stock', 'error');
                    if (saveBtn) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = originalText;
                    }
                });

            } catch (err) {
                console.error('saveAddStock exception:', err);
                showNotification('An unexpected error occurred', 'error');
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                }
            }
        }

        function saveEditStock() {
            const saveBtn = document.getElementById('mgmtSaveBtn');
            const originalText = saveBtn ? saveBtn.innerHTML : 'Save Changes';

            try {
                const editInput = document.getElementById('mgmtEditQuantity');
                const newStock = parseInt(editInput ? editInput.value : '');
                const siteId = document.getElementById('mgmtSiteSelect')?.value;

                if (!siteId) {
                    showNotification('Please select a site', 'warning');
                    return;
                }

                if (isNaN(newStock) || newStock < 0) {
                    showNotification('Please enter a valid stock value', 'warning');
                    return;
                }



                if (!currentBookId) {
                    showNotification('No item selected', 'error');
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<i class="las la-spinner la-spin me-1"></i>Saving...';
                }

                fetch(`/production/inventory/update-stock/${currentBookId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'set',
                        site_id: siteId,
                        new_stock: newStock
                    })
                })
                .then(async response => {
                    const data = await response.json();
                    if (response.ok && data.success) {
                        showNotification(data.message || 'Stock updated successfully!', 'success');
                        closeModal('stockManagementModal');
                        setTimeout(() => location.reload(), 200);
                    } else {
                        showNotification('Error: ' + (data.message || 'Failed to update stock'), 'error');
                        if (saveBtn) {
                            saveBtn.disabled = false;
                            saveBtn.innerHTML = originalText;
                        }
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showNotification('An error occurred while updating stock', 'error');
                    if (saveBtn) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = originalText;
                    }
                });

            } catch (err) {
                console.error('saveEditStock exception:', err);
                showNotification('An unexpected error occurred', 'error');
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                }
            }
        }

        // Site Management Functions
        document.getElementById('addSiteForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('/production/sites/store', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Site added successfully!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('addSiteModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        });

        document.querySelectorAll('.editSiteForm').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const siteId = this.getAttribute('data-site-id');
                const formData = new FormData(this);
                
                fetch(`/production/sites/update/${siteId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        bootstrap.Modal.getInstance(document.getElementById(`editSiteModal${siteId}`)).hide();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred', 'error');
                });
            });
        });
        
        // Delete Site Function
        function deleteSite(siteId, siteName) {
            if (confirm(`Are you sure you want to delete "${siteName}"? This action cannot be undone.`)) {
                const deleteBtn = event.target.closest('button');
                const originalHTML = deleteBtn.innerHTML;
                deleteBtn.innerHTML = '<i class="las la-spinner la-spin"></i>';
                deleteBtn.disabled = true;

                fetch(`/production/sites/delete/${siteId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Site deleted successfully!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                        deleteBtn.innerHTML = originalHTML;
                        deleteBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while deleting the site', 'error');
                    deleteBtn.innerHTML = originalHTML;
                    deleteBtn.disabled = false;
                });
            }
        }
        
        // Stock Transfer Variables
        let selectedBooksMap = {};
        let siteBooks = {};
        let nextRowId = 1;

        // Store master books and sites inventory data safely
        @php
            $formattedSitesInventory = [];
            foreach($allSites ?? $sites ?? [] as $site) {
                $formattedSitesInventory[$site->id] = collect($site->inventory ?? [])->map(function($inv) {
                    return [
                        'book_id' => $inv->book_id,
                        'book_index_id' => $inv->book_index_id,
                        'book_bundle_id' => $inv->book_bundle_id,
                        'book' => ['name' => $inv->item_name ?? 'Unknown'],
                        'quantity' => (int)($inv->quantity ?? 0),
                        'reorder_point' => $inv->reorder_point,
                        'max_stock' => $inv->max_stock
                    ];
                })->values()->toArray();
            }
            $formattedMasterBooks = collect($allBooks ?? $books ?? [])->map(function($book) {
                return [
                    'book_id' => $book->id,
                    'book' => ['name' => $book->name ?? 'Unknown'],
                    'quantity' => (int)($book->stock ?? 0)
                ];
            })->values()->toArray();
        @endphp
        const masterBooksData = @json($formattedMasterBooks);
        const sitesInventoryData = @json($formattedSitesInventory);

        // Initialize transfer modal from master inventory
        window.initTransferModalFromMaster = function() {
            selectedBooksMap = {};
            nextRowId = 1;
            siteBooks = {}; // Clear cache
            
            const fromSelect = document.getElementById('fromSiteSelect');
            const toSelect = document.getElementById('toSiteSelect');
            if (fromSelect) {
                fromSelect.value = '';
                $(fromSelect).trigger('change');
            }
            if (toSelect) {
                toSelect.value = '';
                $(toSelect).trigger('change');
            }
            const notesTextarea = document.querySelector('textarea[name="notes"]');
            if (notesTextarea) notesTextarea.value = '';
            
            const itemTypeSelect = document.getElementById('itemTypeSelect');
            if (itemTypeSelect) itemTypeSelect.value = 'book';

            const addBookForm = document.getElementById('addBookForm');
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            const showAddIndexBtn = document.getElementById('showAddIndexBtn');
            const showAddBundleBtn = document.getElementById('showAddBundleBtn');
            
            if (addBookForm) addBookForm.style.display = 'none';
            if (showAddBookBtn) {
                showAddBookBtn.disabled = true;
                showAddBookBtn.style.display = 'block';
            }
            if (showAddIndexBtn) {
                showAddIndexBtn.disabled = true;
                showAddIndexBtn.style.display = 'block';
            }
            if (showAddBundleBtn) {
                showAddBundleBtn.disabled = true;
                showAddBundleBtn.style.display = 'block';
            }
            
            const bookSelect = document.getElementById('bookSelect');
            const quantityInput = document.getElementById('bookQuantity');
            if (bookSelect) bookSelect.innerHTML = '<option value="">-- Select an Item --</option>';
            if (quantityInput) {
                quantityInput.value = '';
                quantityInput.disabled = true;
            }
            
            renderSelectedBooks();
            updateSubmitButton();
        };

        // Transfer Stock Functions — Batch Submit
        document.getElementById('transferStockForm')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const keys = Object.keys(selectedBooksMap);
            if (keys.length === 0) {
                showNotification('Please add at least one item', 'error');
                return;
            }

            const fromSiteId = document.getElementById('fromSiteSelect').value;
            const toSiteId   = document.getElementById('toSiteSelect').value;

            if (!fromSiteId) {
                showNotification('Please select source site', 'error');
                return;
            }
            if (!toSiteId) {
                showNotification('Please select destination site', 'error');
                return;
            }
            if (fromSiteId === toSiteId) {
                showNotification('Source and destination sites cannot be the same', 'error');
                return;
            }

            // Build items array — books, indices & bundles all together
            const items = keys.map(key => {
                const item = selectedBooksMap[key];
                return {
                    type:     item.type,      // 'book' | 'index' | 'bundle'
                    item_id:  item.itemId,
                    quantity: item.quantity
                };
            });

            const submitBtn   = document.getElementById('submitTransferBtn');
            const originalTxt = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="las la-spinner la-spin me-1"></i>Submitting…';
            submitBtn.disabled  = true;

            fetch('/production/sites/transfer-batch', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept':       'application/json'
                },
                body: JSON.stringify({
                    from_site_id: fromSiteId,
                    to_site_id:   toSiteId,
                    notes:        document.querySelector('textarea[name="notes"]')?.value || '',
                    items:        items
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const skippedMsg = data.skipped > 0 ? ` (${data.skipped} skipped due to insufficient stock)` : '';
                    showNotification(`${data.created} item(s) transfer submitted!${skippedMsg}`, data.skipped > 0 ? 'warning' : 'success');
                    bootstrap.Modal.getInstance(document.getElementById('transferStockModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message || 'Transfer failed', 'error');
                    submitBtn.innerHTML = originalTxt;
                    submitBtn.disabled  = false;
                }
            })
            .catch(err => {
                console.error('Batch transfer error:', err);
                showNotification('Network error. Please try again.', 'error');
                submitBtn.innerHTML = originalTxt;
                submitBtn.disabled  = false;
            });
        });

        // When source site is selected, load its inventory
        $('#transferStockModal').on('shown.bs.modal', function () {
            $('#fromSiteSelect, #toSiteSelect').select2({
                dropdownParent: $('#transferStockModal'),
                width: '100%'
            });
            const fromVal = $('#fromSiteSelect').val();
            if (fromVal) {
                $('#fromSiteSelect').trigger('change');
            }
        });

        $(document).on('change select2:select', '#fromSiteSelect', function() {
            const siteId = this.value;
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            const showAddIndexBtn = document.getElementById('showAddIndexBtn');
            const showAddBundleBtn = document.getElementById('showAddBundleBtn');
            
            if (siteId) {
                selectedBooksMap = {};
                nextRowId = 1;
                renderSelectedBooks();
                updateSubmitButton();
                
                loadBooksForSite(siteId);
                if (showAddBookBtn) showAddBookBtn.disabled = false;
                if (showAddIndexBtn) showAddIndexBtn.disabled = false;
                if (showAddBundleBtn) showAddBundleBtn.disabled = false;
            } else {
                if (showAddBookBtn) showAddBookBtn.disabled = true;
                if (showAddIndexBtn) showAddIndexBtn.disabled = true;
                if (showAddBundleBtn) showAddBundleBtn.disabled = true;
                const bookSelect = document.getElementById('bookSelect');
                if (bookSelect) bookSelect.innerHTML = '<option value="">-- Select an Item --</option>';
            }
        });

        function loadBooksForSite(siteId) {
            console.log('Loading books for site:', siteId);
            
            // Always fetch real-time data from server
            console.log('Fetching real-time books from server for site:', siteId);
            fetch(`/production/sites/${siteId}/inventory`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            })
            .then(data => {
                console.log('Real-time books loaded from server:', data);
                if (data.success && data.inventory && Array.isArray(data.inventory)) {
                    siteBooks[siteId] = data.inventory;
                    populateBookSelect(siteId);
                } else {
                    const select = document.getElementById('bookSelect');
                    if (select) select.innerHTML = '<option value="">No books available</option>';
                    showNotification('No inventory found for this site', 'warning');
                }
            })
            .catch(error => {
                console.error('Error loading books:', error);
                showNotification('Could not load books: ' + error.message, 'error');
                const select = document.getElementById('bookSelect');
                if (select) select.innerHTML = '<option value="">Error loading books</option>';
            });
        }

        document.getElementById('itemTypeSelect')?.addEventListener('change', function() {
            const siteId = document.getElementById('fromSiteSelect').value;
            if (siteId) {
                populateBookSelect(parseInt(siteId));
            }
        });

        function populateBookSelect(siteId) {
            const select = document.getElementById('bookSelect');
            if (!select) return;
            
            const itemType = document.getElementById('itemTypeSelect')?.value || 'book';
            const inventory = siteBooks[siteId] || siteBooks[String(siteId)] || [];
            console.log('Populating dropdown with inventory:', inventory, 'filtered by type:', itemType);
            
            // Filter by item type
            const typeFiltered = inventory.filter(item => item.type === itemType);
            
            // Filter out items already in the selectedBooksMap
            const availableItems = typeFiltered.filter(item => {
                const key = itemType + '_' + item.item_id;
                return !selectedBooksMap[key];
            });

            const placeholderText = `-- Select a ${itemType.charAt(0).toUpperCase() + itemType.slice(1)} --`;
            select.innerHTML = `<option value="">${placeholderText}</option>`;
            
            if (availableItems.length === 0) {
                if (typeFiltered.length === 0) {
                    select.innerHTML = `<option value="">-- No ${itemType}s available --</option>`;
                } else {
                    select.innerHTML = `<option value="">-- All ${itemType}s added --</option>`;
                }
            } else {
                availableItems.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.item_id;
                    option.textContent = `${item.name} (Available: ${item.quantity})`;
                    option.dataset.available = item.quantity;
                    option.dataset.name = item.name;
                    option.dataset.itemId = item.item_id;
                    option.dataset.type = item.type;
                    select.appendChild(option);
                });
            }

            // Initialize or Refresh Select2 (with integrated search)
            if (window.jQuery && typeof jQuery.fn.select2 === 'function') {
                const $select = $('#bookSelect');
                if ($select.data('select2')) {
                    $select.select2('destroy');
                }
                $select.select2({
                    dropdownParent: $('#transferStockModal'),
                    placeholder: placeholderText,
                    allowClear: true,
                    width: '100%',
                    minimumResultsForSearch: 0
                });

                $select.off('change.itemSelect').on('change.itemSelect', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const availableSpan = document.getElementById('selectedBookAvailable');
                    const quantityInput = document.getElementById('bookQuantity');
                    
                    if (selectedOption && selectedOption.value) {
                        if (availableSpan) availableSpan.textContent = selectedOption.dataset.available || '0';
                        if (quantityInput) {
                            quantityInput.max = selectedOption.dataset.available || 1;
                            quantityInput.value = '';
                            quantityInput.disabled = false;
                        }
                    } else {
                        if (availableSpan) availableSpan.textContent = '0';
                        if (quantityInput) {
                            quantityInput.max = 1;
                            quantityInput.value = '';
                            quantityInput.disabled = true;
                        }
                    }
                });
            } else {
                select.onchange = function() {
                    const selected = this.options[this.selectedIndex];
                    const availableSpan = document.getElementById('selectedBookAvailable');
                    const quantityInput = document.getElementById('bookQuantity');
                    
                    if (selected && selected.value) {
                        if (availableSpan) availableSpan.textContent = selected.dataset.available || '0';
                        if (quantityInput) {
                            quantityInput.max = selected.dataset.available || 1;
                            quantityInput.value = '';
                            quantityInput.disabled = false;
                        }
                    } else {
                        if (availableSpan) availableSpan.textContent = '0';
                        if (quantityInput) {
                            quantityInput.max = 1;
                            quantityInput.value = '';
                            quantityInput.disabled = true;
                        }
                    }
                };
                if (select.onchange) select.onchange();
            }
        }

        function openAddItemSubForm(type) {
            const addBookForm = document.getElementById('addBookForm');
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            const showAddIndexBtn = document.getElementById('showAddIndexBtn');
            const showAddBundleBtn = document.getElementById('showAddBundleBtn');
            
            if (addBookForm) addBookForm.style.display = 'block';
            if (showAddBookBtn) showAddBookBtn.style.display = 'none';
            if (showAddIndexBtn) showAddIndexBtn.style.display = 'none';
            if (showAddBundleBtn) showAddBundleBtn.style.display = 'none';
            
            // Set type select value
            const itemTypeSelect = document.getElementById('itemTypeSelect');
            if (itemTypeSelect) itemTypeSelect.value = type;
            
            // Update title & select label
            const titleEl = document.getElementById('addBookFormTitle');
            const labelEl = document.getElementById('itemSelectLabel');

            if (type === 'book') {
                if (titleEl) titleEl.innerHTML = '<i class="las la-book me-2"></i>Add New Book';
                if (labelEl) labelEl.textContent = 'Select Book *';
            } else if (type === 'index') {
                if (titleEl) titleEl.innerHTML = '<i class="las la-pen me-2"></i>Add New Index';
                if (labelEl) labelEl.textContent = 'Select Index *';
            } else if (type === 'bundle') {
                if (titleEl) titleEl.innerHTML = '<i class="las la-cubes me-2"></i>Add New Bundle';
                if (labelEl) labelEl.textContent = 'Select Bundle *';
            }

            const bookSelect = document.getElementById('bookSelect');
            const quantityInput = document.getElementById('bookQuantity');
            
            if (bookSelect) bookSelect.value = '';
            if (quantityInput) {
                quantityInput.value = '';
                quantityInput.disabled = true;
            }
            const availableSpan = document.getElementById('selectedBookAvailable');
            if (availableSpan) availableSpan.textContent = '0';
            
            const fromSiteId = document.getElementById('fromSiteSelect').value;
            if (fromSiteId) {
                populateBookSelect(parseInt(fromSiteId));
            }
        }

        document.getElementById('showAddBookBtn')?.addEventListener('click', function() {
            openAddItemSubForm('book');
        });
        document.getElementById('showAddIndexBtn')?.addEventListener('click', function() {
            openAddItemSubForm('index');
        });
        document.getElementById('showAddBundleBtn')?.addEventListener('click', function() {
            openAddItemSubForm('bundle');
        });

        document.getElementById('closeAddBookForm')?.addEventListener('click', function() {
            const addBookForm = document.getElementById('addBookForm');
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            const showAddIndexBtn = document.getElementById('showAddIndexBtn');
            const showAddBundleBtn = document.getElementById('showAddBundleBtn');
            
            if (addBookForm) addBookForm.style.display = 'none';
            if (showAddBookBtn) showAddBookBtn.style.display = 'block';
            if (showAddIndexBtn) showAddIndexBtn.style.display = 'block';
            if (showAddBundleBtn) showAddBundleBtn.style.display = 'block';
            
            const bookSelect = document.getElementById('bookSelect');
            const quantityInput = document.getElementById('bookQuantity');
            if (bookSelect) bookSelect.value = '';
            if (quantityInput) {
                quantityInput.value = '';
                quantityInput.disabled = true;
            }
        });

        document.getElementById('confirmAddBookBtn')?.addEventListener('click', function() {
            const bookSelect = document.getElementById('bookSelect');
            const quantityInput = document.getElementById('bookQuantity');

            if (!bookSelect || !bookSelect.value) {
                showNotification('Please select an item', 'error');
                return;
            }

            if (!quantityInput || !quantityInput.value || parseInt(quantityInput.value) < 1) {
                showNotification('Please enter a valid quantity', 'error');
                return;
            }

            const itemId = parseInt(bookSelect.value);
            const selectedOption = bookSelect.options[bookSelect.selectedIndex];
            const bookName = selectedOption.dataset.name;
            const available = parseInt(selectedOption.dataset.available);
            const type = selectedOption.dataset.type;
            const quantity = parseInt(quantityInput.value);

            if (quantity > available) {
                showNotification(`Insufficient stock. Available: ${available}`, 'error');
                return;
            }

            addBookToTransfer(itemId, type, bookName, quantity, available);

            bookSelect.value = '';
            quantityInput.value = '';
            quantityInput.disabled = true;
            const availableSpan = document.getElementById('selectedBookAvailable');
            if (availableSpan) availableSpan.textContent = '0';
            
            const addBookForm = document.getElementById('addBookForm');
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            const showAddIndexBtn = document.getElementById('showAddIndexBtn');
            const showAddBundleBtn = document.getElementById('showAddBundleBtn');
            if (addBookForm) addBookForm.style.display = 'none';
            if (showAddBookBtn) showAddBookBtn.style.display = 'block';
            if (showAddIndexBtn) showAddIndexBtn.style.display = 'block';
            if (showAddBundleBtn) showAddBundleBtn.style.display = 'block';
            
            const fromSiteId = document.getElementById('fromSiteSelect').value;
            if (fromSiteId) {
                populateBookSelect(parseInt(fromSiteId));
            }
        });

        function addBookToTransfer(itemId, type, bookName, quantity, available) {
            const key = type + '_' + itemId;
            if (selectedBooksMap[key]) {
                showNotification('This item is already added', 'error');
                return;
            }

            selectedBooksMap[key] = {
                itemId: itemId,
                type: type,
                name: bookName,
                quantity: quantity,
                available: available,
                rowId: nextRowId++
            };

            renderSelectedBooks();
            updateSubmitButton();
            showNotification(`${bookName} added to transfer list`, 'success');
        }

        window.removeBookFromTransfer = function(key) {
            delete selectedBooksMap[key];
            renderSelectedBooks();
            updateSubmitButton();
            
            const fromSiteId = document.getElementById('fromSiteSelect').value;
            if (fromSiteId) {
                populateBookSelect(parseInt(fromSiteId));
            }
        };
        
        window.updateBookQuantity = function(key, newQuantity) {
            const book = selectedBooksMap[key];
            if (!book) return;
            
            if (newQuantity < 1) {
                showNotification('Quantity must be at least 1', 'error');
                return;
            }
            
            if (newQuantity > book.available) {
                showNotification(`Maximum quantity available: ${book.available}`, 'error');
                return;
            }
            
            book.quantity = newQuantity;
            renderSelectedBooks();
            updateSubmitButton();
        };

        function renderSelectedBooks() {
            const tbody = document.getElementById('transferBooksBody');
            if (!tbody) return;

            if (Object.keys(selectedBooksMap).length === 0) {
                tbody.innerHTML = '<tr id="emptyBooksRow"><td colspan="4" class="text-center text-muted py-3">No items added. Click "Add Book" to start.</td></tr>';
                return;
            }

            tbody.innerHTML = Object.entries(selectedBooksMap).map(([key, book]) => `
                <tr data-key="${key}">
                    <td><strong>${escapeHtml(book.name)} <span class="badge badge-xs bg-secondary text-uppercase">${book.type}</span></strong></td>
                    <td>
                        <input type="number" 
                               class="form-control form-control-sm" 
                               value="${book.quantity}" 
                               min="1" 
                               max="${book.available}"
                               style="width: 100px;"
                               onchange="updateBookQuantity('${key}', parseInt(this.value))">
                    </td>
                    <td><span class="badge bg-info">${book.available} available</span></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeBookFromTransfer('${key}')" title="Remove">
                            <i class="las la-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function updateSubmitButton() {
            const submitBtn = document.getElementById('submitTransferBtn');
            const bookCount = Object.keys(selectedBooksMap).length;
            const fromSiteId = document.getElementById('fromSiteSelect').value;
            const toSiteId = document.getElementById('toSiteSelect').value;
            
            if (submitBtn) {
                submitBtn.disabled = bookCount === 0 || !fromSiteId || !toSiteId || fromSiteId === toSiteId;
                submitBtn.innerHTML = `<i class="las la-check me-1"></i>Request Transfer (${bookCount} item${bookCount !== 1 ? 's' : ''})`;
            }
        }
        
        document.getElementById('toSiteSelect')?.addEventListener('change', function() {
            updateSubmitButton();
        });

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        window.approveTransfer = function(transferId) {
            if (confirm('Approve this transfer?')) {
                fetch(`/stock-transfers/${transferId}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Transfer approved!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('An error occurred', 'error');
                });
            }
        };

        window.rejectTransfer = function(transferId) {
            if (typeof window.openTwoStepRejectionFlow === 'function') {
                window.openTwoStepRejectionFlow('', function(reason) {
                    const formData = new FormData();
                    formData.append('rejection_reason', reason);
                    formData.append('remarks', reason);

                    fetch(`/stock-transfers/${transferId}/reject`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('Transfer rejected!', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showNotification('Error: ' + data.message, 'error');
                        }
                    });
                });
            }
        };

        window.accountingApproveTransfer = function(transferId) {
            if (confirm('Approve this transfer from Accounting and forward to Logistics?')) {
                fetch(`/stock-transfers/${transferId}/accounting-approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message || 'Transfer forwarded to Logistics!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                })
                .catch(() => {
                    showNotification('An error occurred', 'error');
                });
            }
        };

        window.assignLogisticsTransfer = function(transferId) {
            const select = document.getElementById(`assignLogistics${transferId}`);
            const logisticsUserId = select ? select.value : '';

            if (!logisticsUserId) {
                showNotification('Please select a logistics staff.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('logistics_assigned_to', logisticsUserId);

            fetch(`/stock-transfers/${transferId}/assign-logistics`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message || 'Transfer assigned!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(() => {
                showNotification('An error occurred', 'error');
            });
        };

        window.completeLogisticsTransfer = function(transferId) {
            if (confirm('Mark this stock transfer as completed? This will move the stock now.')) {
                fetch(`/stock-transfers/${transferId}/complete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message || 'Transfer completed!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                })
                .catch(() => {
                    showNotification('An error occurred', 'error');
                });
            }
        };

        // --- BOOK INDEX STOCK MANAGEMENT ---
        let currentIndexId = null;
        let currentIndexStock = 0;

        window.openIndexStockModal = function(indexId, bookName, indexValue, stock) {
            console.log("openIndexStockModal called", {indexId, bookName, indexValue, stock});
            try {
                currentIndexId = indexId;
                currentIndexStock = stock;

                const bookNameInput = document.getElementById('mgmtIndexBookName');
                if (bookNameInput) bookNameInput.value = bookName;

                const indexValueInput = document.getElementById('mgmtIndexValue');
                if (indexValueInput) indexValueInput.value = indexValue;

                const currentStockInput = document.getElementById('mgmtIndexCurrentStock');
                if (currentStockInput) currentStockInput.value = stock;

                const addQtyInput = document.getElementById('mgmtIndexAddQuantity');
                if (addQtyInput) addQtyInput.value = '';

                const addPreview = document.getElementById('mgmtIndexAddPreview');
                if (addPreview) addPreview.style.display = 'none';

                const editQtyInput = document.getElementById('mgmtIndexEditQuantity');
                if (editQtyInput) editQtyInput.value = '';

                const editPreview = document.getElementById('mgmtIndexEditPreview');
                if (editPreview) editPreview.style.display = 'none';

                // Hook preview events
                if (addQtyInput) {
                    addQtyInput.oninput = function() {
                        const qty = parseInt(this.value) || 0;
                        const preview = document.getElementById('mgmtIndexAddPreview');
                        if (qty > 0) {
                            if (preview) preview.style.display = 'block';
                            const addNewStock = document.getElementById('mgmtIndexAddNewStock');
                            if (addNewStock) addNewStock.textContent = currentIndexStock + qty;
                        } else {
                            if (preview) preview.style.display = 'none';
                        }
                    };
                }

                if (editQtyInput) {
                    editQtyInput.oninput = function() {
                        const val = parseInt(this.value);
                        const preview = document.getElementById('mgmtIndexEditPreview');
                        if (!isNaN(val) && val >= 0) {
                            if (preview) preview.style.display = 'block';
                            const editOldStock = document.getElementById('mgmtIndexEditOldStock');
                            if (editOldStock) editOldStock.textContent = currentIndexStock;
                            const editNewStock = document.getElementById('mgmtIndexEditNewStock');
                            if (editNewStock) editNewStock.textContent = val;
                        } else {
                            if (preview) preview.style.display = 'none';
                        }
                    };
                }

                const modalEl = document.getElementById('indexStockModal');
                if (modalEl) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                    console.log("indexStockModal show called successfully");
                } else {
                    console.error("indexStockModal element not found in DOM");
                }
            } catch (err) {
                console.error("Error in openIndexStockModal:", err);
            }
        };

        window.saveIndexStockManagement = function() {
            const activeTab = document.querySelector('#indexStockModal .nav-link.active');
            let action = 'add';
            let qty = 0;
            let newStock = 0;

            if (activeTab && activeTab.id === 'idxAddTab') {
                action = 'add';
                qty = parseInt(document.getElementById('mgmtIndexAddQuantity').value);
                if (!qty || qty < 1) {
                    showNotification('Please enter a valid quantity to add', 'warning');
                    return;
                }
            } else {
                action = 'set';
                newStock = parseInt(document.getElementById('mgmtIndexEditQuantity').value);
                if (isNaN(newStock) || newStock < 0) {
                    showNotification('Please enter a valid new stock value', 'warning');
                    return;
                }
            }

            const saveBtn = document.getElementById('mgmtIndexSaveBtn');
            const originalText = saveBtn.innerHTML;
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="las la-spinner la-spin"></i> Saving...';

            fetch(`/production/inventory/update-index-stock/${currentIndexId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    action: action,
                    quantity: qty,
                    new_stock: newStock
                })
            })
            .then(async response => {
                const data = await response.json();
                if (response.ok && data.success) {
                    showNotification(data.message || 'Index stock updated successfully!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('indexStockModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    let msg = data.message || 'Failed to update index stock';
                    if (data.errors) {
                        msg = Object.values(data.errors).flat().join(' ');
                    }
                    showNotification('Error: ' + msg, 'error');
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while saving index stock', 'error');
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            });
        };

        // --- BOOK BUNDLE STOCK MANAGEMENT ---
        let currentBundleId = null;
        let currentBundleStock = 0;

        window.openBundleStockModal = function(bundleId, bundleName, stock) {
            console.log("openBundleStockModal called", {bundleId, bundleName, stock});
            try {
                currentBundleId = bundleId;
                currentBundleStock = stock;

                const bundleNameInput = document.getElementById('mgmtBundleName');
                if (bundleNameInput) bundleNameInput.value = bundleName;

                const currentStockInput = document.getElementById('mgmtBundleCurrentStock');
                if (currentStockInput) currentStockInput.value = stock;

                const addQtyInput = document.getElementById('mgmtBundleAddQuantity');
                if (addQtyInput) addQtyInput.value = '';

                const addPreview = document.getElementById('mgmtBundleAddPreview');
                if (addPreview) addPreview.style.display = 'none';

                const editQtyInput = document.getElementById('mgmtBundleEditQuantity');
                if (editQtyInput) editQtyInput.value = '';

                const editPreview = document.getElementById('mgmtBundleEditPreview');
                if (editPreview) editPreview.style.display = 'none';

                // Hook preview events
                if (addQtyInput) {
                    addQtyInput.oninput = function() {
                        const qty = parseInt(this.value) || 0;
                        const preview = document.getElementById('mgmtBundleAddPreview');
                        if (qty > 0) {
                            if (preview) preview.style.display = 'block';
                            const addNewStock = document.getElementById('mgmtBundleAddNewStock');
                            if (addNewStock) addNewStock.textContent = currentBundleStock + qty;
                        } else {
                            if (preview) preview.style.display = 'none';
                        }
                    };
                }

                if (editQtyInput) {
                    editQtyInput.oninput = function() {
                        const val = parseInt(this.value);
                        const preview = document.getElementById('mgmtBundleEditPreview');
                        if (!isNaN(val) && val >= 0) {
                            if (preview) preview.style.display = 'block';
                            const editOldStock = document.getElementById('mgmtBundleEditOldStock');
                            if (editOldStock) editOldStock.textContent = currentBundleStock;
                            const editNewStock = document.getElementById('mgmtBundleEditNewStock');
                            if (editNewStock) editNewStock.textContent = val;
                        } else {
                            if (preview) preview.style.display = 'none';
                        }
                    };
                }

                const modalEl = document.getElementById('bundleStockModal');
                if (modalEl) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                    console.log("bundleStockModal show called successfully");
                } else {
                    console.error("bundleStockModal element not found in DOM");
                }
            } catch (err) {
                console.error("Error in openBundleStockModal:", err);
            }
        };

        window.saveBundleStockManagement = function() {
            const activeTab = document.querySelector('#bundleStockModal .nav-link.active');
            let action = 'add';
            let qty = 0;
            let newStock = 0;

            if (activeTab && activeTab.id === 'bndlAddTab') {
                action = 'add';
                qty = parseInt(document.getElementById('mgmtBundleAddQuantity').value);
                if (!qty || qty < 1) {
                    showNotification('Please enter a valid quantity to add', 'warning');
                    return;
                }
            } else {
                action = 'set';
                newStock = parseInt(document.getElementById('mgmtBundleEditQuantity').value);
                if (isNaN(newStock) || newStock < 0) {
                    showNotification('Please enter a valid new stock value', 'warning');
                    return;
                }
            }

            const saveBtn = document.getElementById('mgmtBundleSaveBtn');
            const originalText = saveBtn.innerHTML;
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="las la-spinner la-spin"></i> Saving...';

            fetch(`/production/inventory/update-bundle-stock/${currentBundleId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    action: action,
                    quantity: qty,
                    new_stock: newStock
                })
            })
            .then(async response => {
                const data = await response.json();
                if (response.ok && data.success) {
                    showNotification(data.message || 'Bundle stock updated successfully!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('bundleStockModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    let msg = data.message || 'Failed to update bundle stock';
                    if (data.errors) {
                        msg = Object.values(data.errors).flat().join(' ');
                    }
                    showNotification('Error: ' + msg, 'error');
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while saving bundle stock', 'error');
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            });
        };

        // --- TAB STATE PERSISTENCE ---
        document.addEventListener('DOMContentLoaded', function() {
            // Page-level tabs configuration
            const pageTabs = ['stocks-tab', 'sites-tab', 'transfer-workflow-tab'];
            pageTabs.forEach(tabId => {
                const button = document.getElementById(tabId);
                button?.addEventListener('shown.bs.tab', function(event) {
                    localStorage.setItem('active_inventory_overview_tab', event.target.id);
                });
            });

            // Restore page-level tab
            const urlParams = new URLSearchParams(window.location.search);
            const hasSiteSearch = urlParams.has('site_search') || urlParams.has('sites_page');
            const savedPageTabId = hasSiteSearch ? 'sites-tab' : localStorage.getItem('active_inventory_overview_tab');
            if (savedPageTabId && pageTabs.includes(savedPageTabId)) {
                const tabButton = document.getElementById(savedPageTabId);
                if (tabButton) {
                    const tab = new bootstrap.Tab(tabButton);
                    tab.show();
                }
            }

            // Card-level nested registry tabs configuration
            const registryTabs = ['registry-books-tab', 'registry-nonbooks-tab', 'registry-indices-tab', 'registry-bundles-tab'];
            registryTabs.forEach(tabId => {
                const button = document.getElementById(tabId);
                button?.addEventListener('shown.bs.tab', function(event) {
                    localStorage.setItem('active_inventory_registry_tab', event.target.id);
                });
            });

            // Restore card-level nested registry tab
            const savedRegistryTabId = localStorage.getItem('active_inventory_registry_tab');
            if (savedRegistryTabId && registryTabs.includes(savedRegistryTabId)) {
                const tabButton = document.getElementById(savedRegistryTabId);
                if (tabButton) {
                    const tab = new bootstrap.Tab(tabButton);
                    tab.show();
                }
            }

            // Site Inventory Modal Client-side Pagination with Search
            function initSiteTablePagination(tableId, pageSize = 6, searchQuery = '') {
                const table = document.getElementById(tableId);
                if (!table) return;
                const tbody = table.querySelector('tbody');
                if (!tbody) return;
                const allRows = Array.from(tbody.querySelectorAll('tr.paginate-row'));
                if (allRows.length === 0) return;

                const query = (searchQuery || '').trim().toLowerCase();
                const filteredRows = allRows.filter(row => {
                    if (!query) return true;
                    return row.textContent.toLowerCase().includes(query);
                });

                // Remove existing no-match row if any
                const existingNoMatch = tbody.querySelector('.no-matching-row');
                if (existingNoMatch) existingNoMatch.remove();

                let container = document.getElementById(tableId + '_pagination');
                if (!container) {
                    container = document.createElement('div');
                    container.id = tableId + '_pagination';
                    container.className = 'd-flex flex-wrap justify-content-between align-items-center mt-3 pt-2 border-top gap-2';
                    table.parentNode.appendChild(container);
                }

                if (filteredRows.length === 0) {
                    allRows.forEach(r => r.style.display = 'none');
                    const colCount = table.querySelectorAll('thead th').length || 4;
                    const noMatchTr = document.createElement('tr');
                    noMatchTr.className = 'no-matching-row';
                    const safeQuery = query.replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    noMatchTr.innerHTML = `<td colspan="${colCount}" class="text-center py-4 text-muted"><i class="las la-search me-1"></i> No items found matching "<strong>${safeQuery}</strong>"</td>`;
                    tbody.appendChild(noMatchTr);
                    container.innerHTML = `<small class="text-muted fw-bold">0 matching entries (filtered from ${allRows.length} total)</small>`;
                    return;
                }

                let currentPage = 1;
                const totalPages = Math.ceil(filteredRows.length / pageSize);

                function render() {
                    const start = (currentPage - 1) * pageSize;
                    const end = start + pageSize;

                    allRows.forEach(r => r.style.display = 'none');
                    filteredRows.forEach((row, idx) => {
                        row.style.display = (idx >= start && idx < end) ? '' : 'none';
                    });

                    const showingStart = start + 1;
                    const showingEnd = Math.min(end, filteredRows.length);

                    let html = `<small class="text-muted fw-bold">Showing ${showingStart} to ${showingEnd} of ${filteredRows.length} entries` + 
                               (query ? ` (filtered from ${allRows.length} total)` : '') + `</small>`;
                    
                    if (totalPages > 1) {
                        html += `<ul class="pagination pagination-sm m-0 flex-wrap">`;
                        
                        // Previous button
                        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                            <button class="page-link py-1 px-2" type="button" data-page="${currentPage - 1}">Prev</button>
                        </li>`;

                        // Smart sliding window for page numbers
                        let pages = [];
                        if (totalPages <= 7) {
                            for (let i = 1; i <= totalPages; i++) pages.push(i);
                        } else {
                            pages.push(1);
                            if (currentPage > 3) {
                                pages.push('...');
                            }
                            
                            let startPage = Math.max(2, currentPage - 1);
                            let endPage = Math.min(totalPages - 1, currentPage + 1);

                            if (currentPage <= 3) {
                                endPage = 4;
                            }
                            if (currentPage >= totalPages - 2) {
                                startPage = totalPages - 3;
                            }

                            for (let i = startPage; i <= endPage; i++) {
                                pages.push(i);
                            }

                            if (currentPage < totalPages - 2) {
                                pages.push('...');
                            }
                            pages.push(totalPages);
                        }

                        pages.forEach(p => {
                            if (p === '...') {
                                html += `<li class="page-item disabled"><span class="page-link py-1 px-2">...</span></li>`;
                            } else {
                                html += `<li class="page-item ${currentPage === p ? 'active' : ''}">
                                    <button class="page-link py-1 px-2" type="button" data-page="${p}">${p}</button>
                                </li>`;
                            }
                        });

                        // Next button
                        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                            <button class="page-link py-1 px-2" type="button" data-page="${currentPage + 1}">Next</button>
                        </li>`;
                        
                        html += `</ul>`;
                    }

                    container.innerHTML = html;

                    container.querySelectorAll('button.page-link').forEach(btn => {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            const p = parseInt(this.getAttribute('data-page'));
                            if (p >= 1 && p <= totalPages) {
                                currentPage = p;
                                render();
                            }
                        });
                    });
                }

                render();
            }

            document.querySelectorAll('[id^="viewSiteInventory"]').forEach(modalEl => {
                const siteId = modalEl.id.replace('viewSiteInventory', '');
                const searchInput = modalEl.querySelector(`.site-inv-search[data-site-id="${siteId}"]`);
                const clearBtn = modalEl.querySelector(`.btn-clear-site-search[data-site-id="${siteId}"]`);
                
                function applySearchAndPagination() {
                    const q = searchInput ? searchInput.value : '';
                    if (clearBtn) {
                        if (q.trim().length > 0) {
                            clearBtn.classList.remove('d-none');
                        } else {
                            clearBtn.classList.add('d-none');
                        }
                    }
                    initSiteTablePagination(`site-books-table-${siteId}`, 6, q);
                    initSiteTablePagination(`site-indices-table-${siteId}`, 6, q);
                    initSiteTablePagination(`site-bundles-table-${siteId}`, 6, q);
                }

                if (searchInput) {
                    searchInput.addEventListener('input', applySearchAndPagination);
                }
                if (clearBtn) {
                    clearBtn.addEventListener('click', function() {
                        if (searchInput) {
                            searchInput.value = '';
                            searchInput.focus();
                        }
                        applySearchAndPagination();
                    });
                }

                modalEl.addEventListener('shown.bs.modal', function() {
                    applySearchAndPagination();
                });

                modalEl.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tabBtn => {
                    tabBtn.addEventListener('shown.bs.tab', function() {
                        applySearchAndPagination();
                    });
                });
            });

            // Active tab persistence
            const savedTabId = localStorage.getItem('overviewActiveTab');
           // const urlParams = new URLSearchParams(window.location.search);
           // if (urlParams.has('area_consignment_page') || urlParams.has('direct_consignment_page')) {
          const registryUrlParams = new URLSearchParams(window.location.search);
		  if (registryUrlParams.has('area_consignment_page') || registryUrlParams.has('direct_consignment_page')) {
                const cTab = document.getElementById('registry-consignment-tab');
                if (cTab) new bootstrap.Tab(cTab).show();
            } else if (savedTabId) {
                const savedTabBtn = document.getElementById(savedTabId);
                if (savedTabBtn) new bootstrap.Tab(savedTabBtn).show();
            }

            document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(e) {
                    if (e.target && e.target.id) {
                        localStorage.setItem('overviewActiveTab', e.target.id);
                    }
                    const titleEl = document.getElementById('registryHeaderTitle');
                    if (titleEl) {
                        const titleMap = {
                            'registry-allsites-tab': 'All Sites Breakdown',
                            'registry-consignment-tab': 'Consignment Inventory',
                        };
                        titleEl.textContent = titleMap[e.target.id] || 'Master Registry';
                    }
                });
            });

            // Initialize Stock Transfer Workflow DataTable (with search and pagination)
            if ($('#stockTransferWorkflowTable').length > 0 && typeof $.fn.DataTable !== 'undefined') {
                if (!$.fn.DataTable.isDataTable('#stockTransferWorkflowTable')) {
                    const stwTable = $('#stockTransferWorkflowTable').DataTable({
                        order: [[0, 'desc']],
                        pageLength: 10,
                        columnDefs: [{ orderable: false, targets: -1 }]
                    });

                    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
                        tab.addEventListener('shown.bs.tab', function(e) {
                            if (e.target.id === 'transfer-workflow-tab' || e.target.getAttribute('href') === '#transfer-workflow-content') {
                                stwTable.columns.adjust().draw();
                            }
                        });
                    });
                }
            }
        });
        window.switchConsignmentSubTab = function(btn, targetPaneId) {
            $('.c-sub-btn').removeClass('active btn-danger').addClass('btn-outline-danger');
            $(btn).addClass('active btn-danger').removeClass('btn-outline-danger');
            $('#area-consignment-pane, #direct-consignment-pane').removeClass('show active').css('display', 'none');
            $('#' + targetPaneId).addClass('show active').css('display', 'block');
        };

        window.reconcileStockUI = function() {
            if (!confirm('Recalculate and synchronize all Master Book Stock levels with Warehouse Inventory?')) {
                return;
            }
            fetch('{{ route("production.inventory.reconcile-stock") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('✓ ' + data.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to reconcile stock'));
                }
            })
            .catch(err => {
                alert('Error: ' + err.message);
            });
        };
    </script>

    <!-- Mark Item as Lost Modal -->
    <div class="modal fade" id="markAsLostModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white py-3">
                    <h5 class="modal-title text-white fw-bold"><i class="las la-exclamation-triangle me-2"></i>Mark Inventory as Lost</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('production.inventory.mark-as-lost') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-warning mb-4 py-2 px-3 small d-flex align-items-center gap-2">
                            <i class="las la-info-circle fs-18"></i>
                            <span>Marking stock as lost will deduct the specified quantity from available stock and log a permanent audit record. The product record will NOT be deleted.</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Product Type <span class="text-danger">*</span></label>
                                <select name="product_type" id="lostProductType" class="form-select" required onchange="onLostProductTypeChange()">
                                    <option value="book">Book</option>
                                    <option value="non_book">Non-Book</option>
                                    <option value="index">Index</option>
                                    <option value="bundle">Bundle</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Select Item / Product <span class="text-danger">*</span></label>
                                <select name="product_id" id="lostProductId" class="form-select" required>
                                    <option value="">Select product...</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Location Type <span class="text-danger">*</span></label>
                                <select name="location_type" id="lostLocationType" class="form-select" required onchange="onLostLocationTypeChange()">
                                    <option value="site">Site / Warehouse</option>
                                    <option value="team">Sales Team</option>
                                </select>
                            </div>

                            <div class="col-md-6" id="lostSiteGroup">
                                <label class="form-label fw-semibold text-dark">Select Site / Warehouse <span class="text-danger">*</span></label>
                                <select name="site_id" id="lostSiteId" class="form-select">
                                    @foreach($allSites ?? $sites ?? [] as $site)
                                        <option value="{{ $site->id }}" {{ $site->name === 'Main Warehouse' ? 'selected' : '' }}>{{ $site->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 d-none" id="lostTeamGroup">
                                <label class="form-label fw-semibold text-dark">Select Sales Team <span class="text-danger">*</span></label>
                                <select name="team_name" id="lostTeamName" class="form-select">
                                    <option value="Team A">Team A</option>
                                    <option value="Team B">Team B</option>
                                    <option value="Team C">Team C</option>
                                    <option value="Book Sales">Book Sales</option>
                                    <option value="MIBF">MIBF</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Lost Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control" min="1" required placeholder="Enter lost quantity (e.g. 1)">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold text-dark">Reason / Remarks</label>
                                <textarea name="reason" class="form-control" rows="3" placeholder="Reason for marking as lost (e.g. Damaged during transfer, missing during audit)..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-3">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">
                            <i class="las la-exclamation-triangle me-1"></i>Confirm & Mark as Lost
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden Printable Customer Consignment Inventory Sheet Container -->
    <div id="printConsignmentSheetArea" style="display: none;">
        <div style="font-family: Arial, sans-serif; padding: 20px; color: #000;">
            <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px;">
                <h2 style="margin: 0; font-size: 18px; font-weight: bold; text-transform: uppercase;">CLARETIAN COMMUNICATIONS FOUNDATION INC.</h2>
                <p style="margin: 2px 0 0 0; font-size: 12px; color: #333;">8 Mayumi St., UP Village, Diliman, Quezon City | Tel: 921-3984</p>
                <h3 style="margin: 10px 0 0 0; font-size: 15px; font-weight: bold; letter-spacing: 1px; color: #1a5276;">CUSTOMER CONSIGNMENT INVENTORY SHEET</h3>
            </div>

            <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 12px; line-height: 1.6;">
                <div>
                    <strong>Customer Name:</strong> <span id="pcsCustName"></span><br>
                    <strong>Area Sales Staff:</strong> <span id="pcsStaffName"></span>
                </div>
                <div style="text-align: right;">
                    <strong>Date Generated:</strong> <span id="pcsDate"></span><br>
                    <strong>Total Orders:</strong> <span id="pcsOrderCount"></span>
                </div>
            </div>

            <div style="margin-bottom: 15px; font-size: 11px; background: #f8f9fa; border: 1px solid #ddd; padding: 8px; border-radius: 4px;">
                <strong>Consigned DR Numbers:</strong> <span id="pcsDrList"></span>
            </div>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px;">
                <thead>
                    <tr style="background: #e9ecef; border: 1px solid #000;">
                        <th style="padding: 6px; text-align: center; border: 1px solid #000; width: 40px;">#</th>
                        <th style="padding: 6px; text-align: left; border: 1px solid #000; width: 140px;">SKU / BARCODE</th>
                        <th style="padding: 6px; text-align: left; border: 1px solid #000;">BOOK DESCRIPTION</th>
                        <th style="padding: 6px; text-align: center; border: 1px solid #000; width: 120px;">CONSIGNED QTY</th>
                    </tr>
                </thead>
                <tbody id="pcsItemsBody">
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; background: #f8f9fa; border: 1px solid #000;">
                        <td colspan="3" style="padding: 8px; text-align: right; border: 1px solid #000;">TOTAL CONSIGNED BOOKS:</td>
                        <td id="pcsTotalQty" style="padding: 8px; text-align: center; border: 1px solid #000; font-size: 14px; color: #1a5276;">0</td>
                    </tr>
                </tfoot>
            </table>

            <div style="margin-top: 50px; display: flex; justify-content: space-between; font-size: 12px;">
                <div style="width: 45%; text-align: center;">
                    <div style="border-top: 1px solid #000; padding-top: 5px; font-weight: bold;">
                        Prepared By (Claretian Staff)
                    </div>
                </div>
                <div style="width: 45%; text-align: center;">
                    <div style="border-top: 1px solid #000; padding-top: 5px; font-weight: bold;">
                        Received & Verified By (Customer Signature & Date)
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $getLostProductList = function($items) {
            $list = [];
            $collection = is_object($items) && method_exists($items, 'items') ? $items->items() : (is_array($items) ? $items : (is_object($items) && method_exists($items, 'all') ? $items->all() : []));
            foreach ($collection as $item) {
                if (is_object($item)) {
                    $id = $item->id ?? null;
                    $name = $item->name ?? ($item->title ?? ($item->bundle_name ?? ''));
                    if (isset($item->index_value) && $item->index_value) {
                        $name = ($item->book->name ?? 'Index') . ' - ' . $item->index_value;
                    }
                    $stock = $item->stock ?? 0;
                    if ($id) {
                        $list[] = ['id' => $id, 'name' => $name . ' (Stock: ' . $stock . ')'];
                    }
                }
            }
            return $list;
        };
    @endphp

    <script>
        const lostBooksData = @json($getLostProductList($allBooks ?? []));
        const lostNonBooksData = @json($getLostProductList($nonBooks ?? []));
        const lostIndicesData = @json($getLostProductList($allIndices ?? []));
        const lostBundlesData = @json($getLostProductList($allBundles ?? []));

        window.onLostProductTypeChange = function() {
            const type = document.getElementById('lostProductType').value;
            const selectEl = document.getElementById('lostProductId');
            let data = [];

            if (type === 'book') data = lostBooksData;
            else if (type === 'non_book') data = lostNonBooksData;
            else if (type === 'index') data = lostIndicesData;
            else if (type === 'bundle') data = lostBundlesData;

            let html = '<option value="">Select product...</option>';
            data.forEach(item => {
                html += `<option value="${item.id}">${item.name}</option>`;
            });
            selectEl.innerHTML = html;
        };

        window.onLostLocationTypeChange = function() {
            const locType = document.getElementById('lostLocationType').value;
            const siteGroup = document.getElementById('lostSiteGroup');
            const teamGroup = document.getElementById('lostTeamGroup');
            const siteSelect = document.getElementById('lostSiteId');
            const teamSelect = document.getElementById('lostTeamName');

            if (locType === 'site') {
                siteGroup.classList.remove('d-none');
                teamGroup.classList.add('d-none');
                if (siteSelect) siteSelect.required = true;
                if (teamSelect) teamSelect.required = false;
            } else {
                siteGroup.classList.add('d-none');
                teamGroup.classList.remove('d-none');
                if (siteSelect) siteSelect.required = false;
                if (teamSelect) teamSelect.required = true;
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            onLostProductTypeChange();
            onLostLocationTypeChange();
        });

        window.printCustomerInventorySheet = function(cData) {
            if (!cData) return;
            const today = new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            const logoUrl = @json(asset('images/claeritian_logo.png'));
            
            // 1. Flatten all items across all DRs
            let allItems = [];
            const drList = cData.dr_breakdown || [];
            
            drList.forEach(dr => {
                const drDate = dr.order_date || 'N/A';
                const drNum = dr.dr_number || 'N/A';
                (dr.items || []).forEach(b => {
                    allItems.push({
                        date: b.order_date || drDate,
                        raw_date: b.raw_date || '',
                        dr_number: b.dr_number || drNum,
                        sku: b.sku || '',
                        title: b.name || 'N/A',
                        qty: parseInt(b.qty) || 0,
                        price: parseFloat(b.price) || 0,
                        amount: parseFloat(b.amount) || ((parseInt(b.qty) || 0) * (parseFloat(b.price) || 0))
                    });
                });
            });

            // 2. Sort by Book Title first (so items with same name are consecutive), then by Date / DR#
            allItems.sort((a, b) => {
                const nameComp = a.title.localeCompare(b.title, undefined, { sensitivity: 'base' });
                if (nameComp !== 0) return nameComp;
                if (a.raw_date && b.raw_date) {
                    const dateComp = a.raw_date.localeCompare(b.raw_date);
                    if (dateComp !== 0) return dateComp;
                }
                return a.dr_number.localeCompare(b.dr_number);
            });

            let grandTotalQty = 0;
            let grandTotalAmount = 0;
            let rowsHtml = '';

            if (allItems.length > 0) {
                allItems.forEach((b, idx) => {
                    grandTotalQty += b.qty;
                    grandTotalAmount += b.amount;
                    rowsHtml += `
                        <tr style="border: 1px solid #000;">
                            <td style="padding: 5px 3px; text-align: center; border: 1px solid #000; font-size: 11px;">${idx + 1}</td>
                            <td style="padding: 5px 4px; text-align: center; border: 1px solid #000; font-size: 11px; white-space: nowrap;">${b.date}</td>
                            <td style="padding: 5px 4px; text-align: center; border: 1px solid #000; font-size: 11px; font-weight: bold; white-space: nowrap;">${b.dr_number}</td>
                            <td style="padding: 5px 6px; border: 1px solid #000; font-size: 11px;">
                                <div style="font-weight: bold; color: #111;">${b.title}</div>
                                ${b.sku ? `<div style="font-size: 9.5px; color: #555;">#${b.sku}</div>` : ''}
                            </td>
                            <td style="padding: 5px 4px; text-align: center; border: 1px solid #000; font-weight: bold; font-size: 11px;">${b.qty.toLocaleString()}</td>
                            <td style="padding: 5px 4px; text-align: center; border: 1px solid #000; min-width: 65px;"></td>
                            <td style="padding: 5px 4px; text-align: center; border: 1px solid #000; min-width: 55px;"></td>
                            <td style="padding: 5px 4px; text-align: center; border: 1px solid #000; min-width: 50px;"></td>
                            <td style="padding: 5px 6px; text-align: right; border: 1px solid #000; font-size: 11px; white-space: nowrap;">₱${b.price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            <td style="padding: 5px 4px; text-align: center; border: 1px solid #000; min-width: 65px;"></td>
                        </tr>
                    `;
                });
            } else {
                rowsHtml = `
                    <tr style="border: 1px solid #000;">
                        <td colspan="10" style="padding: 15px; text-align: center; color: #777; border: 1px solid #000;">No consignment items found for this customer.</td>
                    </tr>
                `;
            }

            const unifiedTableHtml = `
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px;">
                    <thead>
                        <tr style="background: #e9ecef; border: 1px solid #000;">
                            <th style="padding: 6px 3px; text-align: center; border: 1px solid #000; width: 30px;">#</th>
                            <th style="padding: 6px 4px; text-align: center; border: 1px solid #000; width: 75px;">DATE</th>
                            <th style="padding: 6px 4px; text-align: center; border: 1px solid #000; width: 110px;">DR #</th>
                            <th style="padding: 6px 6px; text-align: left; border: 1px solid #000;">BOOK TITLE</th>
                            <th style="padding: 6px 4px; text-align: center; border: 1px solid #000; width: 50px;">QTY</th>
                            <th style="padding: 6px 4px; text-align: center; border: 1px solid #000; width: 75px;">PHYSICAL INV</th>
                            <th style="padding: 6px 4px; text-align: center; border: 1px solid #000; width: 60px;">RETURN</th>
                            <th style="padding: 6px 4px; text-align: center; border: 1px solid #000; width: 55px;">SOLD</th>
                            <th style="padding: 6px 6px; text-align: right; border: 1px solid #000; width: 70px;">PRICE</th>
                            <th style="padding: 6px 6px; text-align: right; border: 1px solid #000; width: 85px;">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rowsHtml}
                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold; background: #f8f9fa; border: 1px solid #000;">
                            <td colspan="4" style="padding: 6px 8px; text-align: right; border: 1px solid #000; font-size: 12px;">TOTAL:</td>
                            <td style="padding: 6px 4px; text-align: center; border: 1px solid #000; font-size: 12px; color: #1a5276;">${grandTotalQty.toLocaleString()}</td>
                            <td colspan="3" style="border: 1px solid #000; background: #f8f9fa;"></td>
                            <td style="border: 1px solid #000; background: #f8f9fa;"></td>
                            <td style="border: 1px solid #000; background: #f8f9fa;"></td>
                        </tr>
                    </tfoot>
                </table>
            `;

            const printWindow = window.open('', '_blank', 'width=900,height=900');
            if (!printWindow) {
                alert('Please allow popups for this website to print the inventory sheet.');
                return;
            }
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Customer Consignment Inventory Sheet - ${cData.customer_name}</title>
                    <style>
                        @page { size: letter portrait; margin: 0.35in; }
                        body { margin: 0; padding: 0; font-family: Arial, sans-serif; color: #000; background: #fff; }
                        * { box-sizing: border-box; }
                    </style>
                </head>
                <body>
                    <div style="font-family: Arial, sans-serif; padding: 15px; color: #000;">
                        <div style="border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 12px;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 14px; margin-bottom: 4px;">
                                <img src="${logoUrl}" alt="Claretian Logo" style="height: 50px; width: auto;" onerror="this.style.display='none'">
                                <div style="text-align: left;">
                                    <h2 style="margin: 0; font-size: 16px; font-weight: bold; color: #000; text-transform: uppercase;">CLARETIAN COMMUNICATIONS FOUNDATION INC.</h2>
                                    <p style="margin: 2px 0 0 0; font-size: 11px; color: #333;">8 Mayumi St., UP Village, Diliman, Quezon City</p>
                                    <p style="margin: 1px 0 0 0; font-size: 11px; color: #333;">Tel. No.: 921-3984</p>
                                </div>
                            </div>
                            <h3 style="margin: 6px 0 0 0; text-align: center; font-size: 14px; font-weight: bold; letter-spacing: 1px; color: #1a5276;">CUSTOMER CONSIGNMENT INVENTORY SHEET</h3>
                        </div>

                        <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 11.5px; line-height: 1.5;">
                            <div>
                                <strong>Customer Name:</strong> ${cData.company_name ? `${cData.customer_name} (${cData.company_name})` : (cData.customer_name || 'N/A')}<br>
                                <strong>Area Sales Staff:</strong> ${cData.staff_name || 'Direct / Area Sales Team'}
                            </div>
                            <div style="text-align: right;">
                                <strong>Date Generated:</strong> ${today}<br>
                                <strong>Total Orders / DRs:</strong> ${(cData.orders_count || 0)} Order(s)
                            </div>
                        </div>

                        <div style="margin-bottom: 12px; font-size: 10.5px; background: #f8f9fa; border: 1px solid #ddd; padding: 6px 10px; border-radius: 4px;">
                            <strong>Consigned DR Numbers:</strong> ${(cData.dr_numbers || []).join(', ') || 'N/A'}
                        </div>

                        ${unifiedTableHtml}

                        <div style="margin-top: 15px; background: #f8f9fa; border: 2px solid #1a5276; padding: 8px 12px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 12px; font-weight: bold; color: #000;">OVERALL CONSIGNED BOOKS (${(cData.orders_count || 1)} DRs COMBINED):</span>
                            <span style="font-size: 14px; font-weight: bold; color: #1a5276;">${grandTotalQty.toLocaleString()} pcs</span>
                        </div>

                        <div style="margin-top: 40px; display: flex; justify-content: space-between; font-size: 11px; page-break-inside: avoid;">
                            <div style="width: 45%; text-align: center;">
                                <div style="border-top: 1px solid #000; padding-top: 5px; font-weight: bold;">
                                    Prepared By (Claretian Staff)
                                </div>
                            </div>
                            <div style="width: 45%; text-align: center;">
                                <div style="border-top: 1px solid #000; padding-top: 5px; font-weight: bold;">
                                    Received & Verified By (Customer Signature & Date)
                                </div>
                            </div>
                        </div>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 300);
        };

        document.addEventListener('click', function(e) {
            const btnStock = e.target.closest('.btn-open-stock-mgmt');
            if (btnStock) {
                e.preventDefault();
                const bookId = parseInt(btnStock.getAttribute('data-book-id'));
                const bookName = btnStock.getAttribute('data-book-name') || '';
                const stock = parseFloat(btnStock.getAttribute('data-stock') || 0);
                const maxAttr = btnStock.getAttribute('data-max');
                const max = (maxAttr && maxAttr !== 'N/A') ? parseFloat(maxAttr) : 0;
                openStockManagementModal(bookId, bookName, stock, max);
                return;
            }

            const btnIndex = e.target.closest('.btn-open-index-mgmt');
            if (btnIndex) {
                e.preventDefault();
                const id = parseInt(btnIndex.getAttribute('data-index-id'));
                const title = btnIndex.getAttribute('data-book-title') || '';
                const val = btnIndex.getAttribute('data-index-val') || '';
                const stock = parseFloat(btnIndex.getAttribute('data-stock') || 0);
                openIndexStockModal(id, title, val, stock);
                return;
            }

            const btnBundle = e.target.closest('.btn-open-bundle-mgmt');
            if (btnBundle) {
                e.preventDefault();
                const id = parseInt(btnBundle.getAttribute('data-bundle-id'));
                const name = btnBundle.getAttribute('data-bundle-name') || '';
                const stock = parseFloat(btnBundle.getAttribute('data-stock') || 0);
                openBundleStockModal(id, name, stock);
                return;
            }

            const btnPrintSheet = e.target.closest('.btn-print-cust-sheet');
            if (btnPrintSheet) {
                e.preventDefault();
                const raw = btnPrintSheet.getAttribute('data-cust-data');
                if (!raw) return;
                try {
                    const jsonStr = atob(raw);
                    const cData = JSON.parse(jsonStr);
                    printCustomerInventorySheet(cData);
                } catch (err) {
                    console.error('Error opening print window:', err);
                }
            }
        });
    </script>
@endpush
</x-app-layout>
