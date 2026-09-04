<x-app-layout :title="'Pick List Management'" :sidebar="'production'">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card pick-list-form">
                    <div class="form-header">
                        <h2 class="document-title">PICK LIST MANAGEMENT</h2>
                    </div>

                    {{-- Success/Error Messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="las la-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Completed Pick Lists (For Recreation) -->
                    <div class="completed-picklists-section mb-4">
                        <h5 style="font-weight: 700; color: #333; margin-bottom: 1rem;">
                            <i class="las la-check-square me-2"></i>Completed Pick Lists (Click to Recreate)
                            <span class="badge bg-success rounded-pill ms-2">{{ $completedPickLists->total() }}</span>
                        </h5>

                        @if($completedPickLists->total() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" style="border: 1px solid #dee2e6;">
                                <thead style="background: linear-gradient(135deg, #28a745, #20c997); color: #fff;">
                                    <tr>
                                        <th style="padding: 0.75rem;">Pick List #</th>
                                        <th>SO #</th>
                                        <th>Customer</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Completed</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($completedPickLists as $pickList)
                                    @php
                                        $pickListItemsJson = json_encode($pickList->pickListItems->map(function($item) {
                                            return [
                                                'id'           => $item->id,
                                                'so_item_id'   => $item->sales_order_item_id,
                                                'product'      => $item->salesOrderItem->item_name ?? ($item->salesOrderItem->book->name ?? 'Unknown'),
                                                'item_type'    => $item->salesOrderItem?->item_type ?? ($item->salesOrderItem?->bookIndex ? 'Index' : ($item->salesOrderItem?->bundle ? 'Bundle' : 'Book')),
                                                'quantity'     => $item->requested_qty,
                                                'picked_qty'   => $item->picked_qty,
                                                'price'        => $item->salesOrderItem->price ?? 0,
                                                'subtotal'     => $item->salesOrderItem->subtotal ?? 0,
                                                'unit'         => $item->salesOrderItem->unit ?? 'pcs',
                                                'status'       => $item->status,
                                                'notes'        => $item->notes,
                                            ];
                                        })->values()->all());
                                    @endphp
                                    <tr>
                                        <td class="fw-bold">{{ $pickList->pick_list_number }}</td>
                                        <td class="fw-bold">
                                            {{ $pickList->salesOrder?->so_number ?? 'N/A' }}
                                            @php
                                                $isEcom = ($pickList->salesOrder?->type === 'ecom_direct' || !empty($pickList->salesOrder?->ecom_platform) || str_contains(strtolower($pickList->salesOrder?->so_number ?? ''), 'ecom') || !empty($pickList->salesOrder?->platform_order_id));
                                                $ecomId = $pickList->salesOrder?->platform_order_id ?: ($pickList->salesOrder?->ref_number ?: ($pickList->salesOrder?->po_number ?? null));
                                            @endphp
                                            @if($isEcom || !empty($ecomId))
                                                <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $pickList->salesOrder?->customer?->customer_name ?? 'N/A' }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $pickList->pickListItems->count() }} items</span></td>
                                        <td class="fw-bold">₱{{ number_format($pickList->pickListItems->sum(fn($i) => $i->salesOrderItem->subtotal ?? 0), 2) }}</td>
                                        <td>{{ $pickList->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                                        <td>
                                            @if(auth()->check() && auth()->user()->isSuperAdmin())
                                                <button type="button" class="btn btn-sm btn-warning recreate-picklist-btn"
                                                        data-order-id="{{ $pickList->salesOrder?->id ?? '' }}"
                                                        data-pick-list-id="{{ $pickList->id }}"
                                                        data-so-number="{{ $pickList->salesOrder?->so_number ?? 'N/A' }}"
                                                        data-customer="{{ $pickList->salesOrder?->customer?->customer_name ?? 'N/A' }}"
                                                        data-date="{{ $pickList->created_at?->format('Y-m-d') ?? '' }}"
                                                        data-freight="{{ $pickList->salesOrder?->freight_option ?? 'N/A' }}"
                                                        data-forwarder="{{ $pickList->salesOrder?->freight_forwarder ?? '' }}"
                                                        data-remarks="{{ $pickList->remarks ?: ($pickList->salesOrder?->remarks ?? '') }}"
                                                        data-items='{{ $pickListItemsJson }}'
                                                        style="background: #ffc107; border: none; color: #000;">
                                                    <i class="las la-redo me-1"></i> Recreate
                                                </button>
                                            @endif
                                            <a href="{{ route('production.logistic.shipping-label', $pickList->salesOrder?->id ?? $pickList->id) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-1" title="Print Shipping Label">
                                                <i class="las la-tag me-1"></i> Label
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex justify-content-center">
                            {{ $completedPickLists->appends(request()->except('page'))->links() }}
                        </div>
                        @else
                        <div class="text-center py-4" style="background: #f8f9fa; border-radius: 8px; border: 1px dashed #dee2e6;">
                            <i class="las la-check-circle" style="font-size: 2rem; color: #28a745;"></i>
                            <p class="text-muted mt-2 mb-0">No completed pick lists available for recreation.</p>
                        </div>
                        @endif
                    </div>

                    <hr style="margin: 2rem 0;">

                    <!-- Team Stock Transfers Ready for Picking Queue -->
                    <div class="team-transfers-queue-section mb-4">
                        <h5 style="font-weight: 700; color: #333; margin-bottom: 1rem;">
                            <i class="las la-boxes me-2 text-danger"></i>Team Stock Transfers Ready for Picking
                            <span class="badge bg-danger rounded-pill ms-2">{{ isset($teamStockPickLists) ? $teamStockPickLists->count() : 0 }}</span>
                        </h5>

                        @if(isset($teamStockPickLists) && $teamStockPickLists->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" style="border: 1px solid #dee2e6;">
                                <thead style="background: linear-gradient(135deg, #d9251c, #ff4d4d); color: #fff;">
                                    <tr>
                                        <th style="padding: 0.75rem;">Transfer #</th>
                                        <th>Target Team</th>
                                        <th>Requested By</th>
                                        <th>Total Items</th>
                                        <th>Total Pcs</th>
                                        <th>Date</th>
                                        <th>Remarks</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($teamStockPickLists as $tList)
                                    <tr>
                                        <td class="fw-bold">{{ $tList->transfer_number }}</td>
                                        <td><span class="badge bg-danger">{{ $tList->team_name }}</span></td>
                                        <td>{{ $tList->transferredByUser->name ?? 'N/A' }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $tList->items->count() }} item(s)</span></td>
                                        <td class="fw-bold text-success">{{ number_format($tList->items->sum('quantity')) }} pcs</td>
                                        <td>{{ $tList->created_at->format('M d, Y') }}</td>
                                        <td class="small text-muted">{{ $tList->notes ?: '—' }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('production.logistic.team-stock-transfer.complete-pick', $tList->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success fw-bold">
                                                    <i class="las la-check me-1"></i>Complete Pick & Transfer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-light text-muted small border mb-4">
                            No team stock transfers awaiting picking.
                        </div>
                        @endif
                    </div>

                    <!-- Orders Ready for Picking Queue -->
                    <div class="picking-queue-section mb-4">
                        <h5 style="font-weight: 700; color: #333; margin-bottom: 1rem;">
                            <i class="las la-clipboard-list me-2"></i>Orders Ready for Picking
                            <span class="badge bg-danger rounded-pill ms-2">{{ $pickLists->count() }}</span>
                        </h5>

                        @if($pickLists->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" style="border: 1px solid #dee2e6;">
                                <thead style="background: linear-gradient(135deg, #cc0000, #ff0000); color: #fff;">
                                    <tr>
                                        <th style="padding: 0.75rem;">SO / Invoice #</th>
                                        <th>Customer</th>
                                        <th>Pick List #</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Prepared By</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pickLists as $pickList)
                                    @php
                                        // Build items data from PickListItems (which have picked_qty)
                                        $pickListItemsJson = json_encode($pickList->pickListItems->map(function($item) {
                                            return [
                                                'id'           => $item->id,
                                                'so_item_id'   => $item->sales_order_item_id,
                                                'product'      => $item->salesOrderItem->item_name ?? ($item->salesOrderItem->book->name ?? 'Unknown'),
                                                'item_type'    => $item->salesOrderItem?->item_type ?? ($item->salesOrderItem?->bookIndex ? 'Index' : ($item->salesOrderItem?->bundle ? 'Bundle' : 'Book')),
                                                'quantity'     => $item->requested_qty,
                                                'picked_qty'   => $item->picked_qty,
                                                'price'        => $item->salesOrderItem->price ?? 0,
                                                'subtotal'     => $item->salesOrderItem->subtotal ?? 0,
                                                'unit'         => $item->salesOrderItem->unit ?? 'pcs',
                                                'status'       => $item->status,
                                                'notes'        => $item->notes,
                                            ];
                                        })->values()->all());
                                    @endphp
                                    <tr>
                                        <td class="fw-bold">
                                            {{ $pickList->salesOrder?->so_number ?? 'N/A' }}
                                            @php
                                                $isEcom = ($pickList->salesOrder?->type === 'ecom_direct' || !empty($pickList->salesOrder?->ecom_platform) || str_contains(strtolower($pickList->salesOrder?->so_number ?? ''), 'ecom') || !empty($pickList->salesOrder?->platform_order_id));
                                                $ecomId = $pickList->salesOrder?->platform_order_id ?: ($pickList->salesOrder?->ref_number ?: ($pickList->salesOrder?->po_number ?? null));
                                            @endphp
                                            @if($isEcom || !empty($ecomId))
                                                <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $pickList->salesOrder?->customer?->customer_name ?? 'N/A' }}</td>
                                        <td>{{ $pickList->pick_list_number }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $pickList->pickListItems->count() }} items</span>
                                        </td>
                                        <td class="fw-bold">₱{{ number_format($pickList->pickListItems->sum(fn($i) => $i->salesOrderItem->subtotal ?? 0), 2) }}</td>
                                        <td>{{ $pickList->preparedByUser?->name ?? 'N/A' }}</td>
                                        <td>{{ $pickList->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary view-order-btn"
                                                    data-order-id="{{ $pickList->salesOrder?->id ?? '' }}"
                                                    data-pick-list-id="{{ $pickList->id }}"
                                                    data-so-number="{{ $pickList->salesOrder?->so_number ?? 'N/A' }}"
                                                    data-customer="{{ $pickList->salesOrder?->customer?->customer_name ?? 'N/A' }}"
                                                    data-date="{{ $pickList->created_at?->format('Y-m-d') ?? '' }}"
                                                    data-freight-option="{{ $pickList->salesOrder->freight_option ?? '' }}"
                                                    data-forwarder="{{ $pickList->salesOrder->forwarder ?? '' }}"
                                                    data-items='{{ $pickListItemsJson }}'
                                                    data-ecom-platform="{{ $pickList->salesOrder->ecom_platform ?? '' }}"
                                                    style="background: #ff0000; border: none;">
                                                <i class="las la-eye me-1"></i> View Items
                                            </button>
                                            <a href="{{ route('production.logistic.shipping-label', $pickList->salesOrder?->id ?? $pickList->id) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-1" title="Print Shipping Label">
                                                <i class="las la-tag me-1"></i> Label
                                            </a>
                                            @if(optional($pickList->salesOrder)->ecom_platform)
                                            <button type="button" class="btn btn-sm link-to-pack-btn"
                                                    data-order-id="{{ $pickList->salesOrder->id ?? '' }}"
                                                    data-so-number="{{ $pickList->salesOrder->so_number ?? 'N/A' }}"
                                                    title="Link to Pack Management"
                                                    style="background: #0d6efd; color: #fff; border: none; margin-left: 0.25rem;">
                                                <i class="las la-dolly"></i>
                                            </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5" style="background: #f8f9fa; border-radius: 8px;">
                            <i class="las la-inbox" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-2 mb-0">No pick lists ready for picking at this time.</p>
                        </div>
                        @endif
                    </div>

                    <hr style="margin: 2rem 0;">

                    <!-- Order Details Panel (shown when clicking View Items) -->
                    <div id="orderDetailPanel" style="display: none;">
                        <hr>
                        <div class="order-info-section">
                            <div class="order-info-box">
                                <h5>Order Information</h5>
                                <div class="form-group">
                                    <label>Sales Order Number:</label>
                                    <input type="text" id="detailSONumber" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Order Date:</label>
                                    <input type="text" id="detailOrderDate" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Customer:</label>
                                    <input type="text" id="detailCustomerName" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Freight Option:</label>
                                    <input type="text" id="detailFreightOption" readonly>
                                </div>
                                <div class="form-group" id="detailForwarderGroup" style="display: none;">
                                    <label>Forwarder:</label>
                                    <input type="text" id="detailForwarder" class="fw-bold text-primary" readonly>
                                </div>
                            </div>
                            <div class="order-info-box">
                                <h5>Pick List Information</h5>
                                <div class="form-group">
                                    <label>Pick List Number:</label>
                                    <input type="text" id="pickListNumber" placeholder="Auto-generated" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Status:</label>
                                    <select id="pickListStatus" class="form-control">
                                        <option value="draft">Draft</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Prepared By:</label>
                                    <input type="text" id="preparedBy" value="{{ auth()->user()->name ?? 'N/A' }}" readonly>
                                </div>
                                <div class="form-group">
                                     <label>Remarks / Notes:</label>
                                     <textarea id="pickListRemarks" class="form-control" rows="2" placeholder="Enter remarks or special instructions..."></textarea>
                                 </div>
                            </div>
                        </div>

                        <!-- Hidden fields for pack management link -->
                        <input type="hidden" id="ecomPlatform">
                        <input type="hidden" id="pickListIdHidden">

                        <!-- Pick List Items Table -->
                        <h5 style="margin-bottom: 1rem; font-weight: 600;">Pick List Items</h5>
                        <table class="pick-list-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Product</th>
                                    <th style="width: 120px;">Requested Qty</th>
                                    <th style="width: 120px;">Unit Price</th>
                                    <th style="width: 120px;">Subtotal</th>
                                    <th style="width: 120px;">Picked Qty</th>
                                    <th style="width: 120px;">Status</th>
                                    <th style="width: 150px;">Notes</th>
                                </tr>
                            </thead>
                            <tbody id="pickListTableBody">
                                <!-- Filled by JS -->
                            </tbody>
                        </table>

                        <!-- Summary Section -->
                        <div class="order-info-section" style="margin-top: 1.5rem;">
                            <div class="order-info-box">
                                <h5>Summary</h5>
                                <div class="form-group">
                                    <label>Total Items:</label>
                                    <input type="text" id="totalItems" value="0" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Items Picked:</label>
                                    <input type="text" id="itemsPicked" value="0" readonly>
                                </div>
                            </div>
                            <div class="order-info-box">
                                <h5>Actions</h5>
                                <div class="form-group">
                                    <button type="button" class="btn btn-success" style="width: 100%; margin-bottom: 0.5rem; background: #28a745; color: #fff; border: none; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;" id="savePickedBtn" onclick="savePickedItems()">
                                        <i class="las la-save"></i> Save Picked Items
                                    </button>
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-secondary-custom" style="width: 100%; margin-bottom: 0.5rem;" onclick="printPickList()">
                                        <i class="las la-print"></i> Print Pick List
                                    </button>
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-outline-primary" style="width: 100%; margin-bottom: 0.5rem; border: 1px solid #0d6efd; color: #0d6efd; background: #fff; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;" onclick="openPickListShippingLabel()">
                                        <i class="las la-tag me-1"></i> Print / View Shipping Label
                                    </button>
                                </div>
                                <div class="form-group" id="packManagementLinkGroup" style="display: none;">
                                    <button type="button" class="btn btn-info" style="width: 100%; margin-bottom: 0.5rem; background: #0d6efd; color: #fff; border: none; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;" id="linkToPackBtn" onclick="linkToPackManagement()">
                                        <i class="las la-dolly me-1"></i> Link to Pack Management
                                    </button>
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary-custom" style="width: 100%;" id="closeDetailBtn">
                                        <i class="las la-times"></i> Close Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .pick-list-form {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }

        .form-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }

        .form-header .document-title {
            text-align: center;
            font-size: 1.75rem;
            font-weight: 700;
            color: #333;
            margin-top: 1rem;
            letter-spacing: 1px;
        }

        .order-info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 1.5rem;
        }

        .order-info-box {
            background: #f8f9fa;
            padding: 1.25rem;
            border-radius: 8px;
        }

        .order-info-box h5 {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 0.75rem;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
            display: block;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 0.5rem;
            font-size: 0.9rem;
        }

        .pick-list-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        .pick-list-table thead {
            background: linear-gradient(135deg, #cc0000, #ff0000);
            color: #fff;
        }

        .pick-list-table th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            border: 1px solid #ddd;
            text-transform: uppercase;
        }

        .pick-list-table td {
            padding: 0.5rem;
            border: 1px solid #ddd;
        }

        .pick-list-table input[type="number"],
        .pick-list-table input[type="text"],
        .pick-list-table select {
            width: 100%;
            border: none;
            padding: 0.5rem;
            background: transparent;
        }

        .pick-list-table input:focus,
        .pick-list-table select:focus {
            outline: 2px solid #ff0000;
            outline-offset: -2px;
            background: #fff;
        }

        .platform-badge { padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .platform-lazada { background: #0f146d; color: #fff; }
        .platform-shopee { background: #ee4d2d; color: #fff; }
        .platform-tiktok { background: #010101; color: #fff; }

        .btn-primary-custom {
            background: #ff0000;
            color: #fff;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary-custom:hover {
            background: #ff3333;
            box-shadow: 0 4px 12px rgba(255,0,0,0.2);
        }

        .btn-secondary-custom {
            background: #6c757d;
            color: #fff;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-secondary-custom:hover {
            background: #5a6268;
        }

        @media print {
            /* HIDE EVERYTHING BY DEFAULT */
            html, body { width: 100%; height: 100%; }
            body { margin: 0; padding: 0; }
            
            .sidebar { display: none !important; }
            .header { display: none !important; }
            .alert { display: none !important; }
            .form-header { display: none !important; }
            
            /* Hide the pick lists queue section entirely */
            .picking-queue-section { display: none !important; }
            .table-responsive { display: none !important; }
            .container-fluid { margin: 0; padding: 0.5in; }
            .row { margin: 0; padding: 0; }
            .col-12 { padding: 0; }
            
            .card { 
                border: none !important;
                box-shadow: none !important; 
                padding: 0 !important;
                margin: 0 !important;
            }
            
            /* SHOW ONLY THE DETAIL PANEL */
            #orderDetailPanel {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            /* Detail panel styling */
            #orderDetailPanel hr { display: none !important; }
            
            /* Body and container */
            body { 
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
            }
            
            .container-fluid { 
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .row { margin: 0 !important; }
            .col-12 { padding: 0 !important; }
            
            /* Detail panel content */
            #orderDetailPanel hr {
                visibility: hidden !important;
            }
            
            .order-info-section { 
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 1.5rem !important;
                margin-bottom: 1.5rem !important;
                page-break-inside: avoid !important;
            }
            
            .order-info-box { 
                background: #f5f5f5 !important;
                padding: 1rem !important;
                border: 1px solid #ddd !important;
                border-radius: 4px !important;
                page-break-inside: avoid !important;
            }
            
            .order-info-box h5 {
                font-size: 1rem !important;
                font-weight: bold !important;
                margin: 0 0 1rem 0 !important;
                padding-bottom: 0.5rem !important;
                border-bottom: 2px solid #cc0000 !important;
                color: #333 !important;
            }
            
            .form-group {
                margin-bottom: 0.75rem !important;
                display: block !important;
            }
            
            .form-group label {
                font-weight: 600 !important;
                font-size: 0.9rem !important;
                margin-bottom: 0.25rem !important;
                display: block !important;
                color: #333 !important;
            }
            
            .form-group input[readonly],
            .form-group input[type="text"],
            .form-group select {
                width: 100% !important;
                border: none !important;
                border-bottom: 1px solid #999 !important;
                background: transparent !important;
                color: #000 !important;
                padding: 0.25rem 0 !important;
                font-size: 0.9rem !important;
            }
            
            /* Pick list table */
            #orderDetailPanel h5 {
                font-size: 1.1rem !important;
                font-weight: 600 !important;
                margin-top: 1.5rem !important;
                margin-bottom: 1rem !important;
                color: #333 !important;
            }
            
            .pick-list-table {
                width: 100% !important;
                border-collapse: collapse !important;
                page-break-inside: avoid !important;
                font-size: 0.85rem !important;
            }
            
            .pick-list-table thead {
                display: table-header-group !important;
            }
            
            .pick-list-table tbody {
                display: table-row-group !important;
            }
            
            .pick-list-table tr {
                display: table-row !important;
                page-break-inside: avoid !important;
            }
            
            .pick-list-table th {
                display: table-cell !important;
                padding: 10px !important;
                background: #cc0000 !important;
                color: #fff !important;
                font-weight: bold !important;
                border: 1px solid #999 !important;
                text-align: left !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .pick-list-table td {
                display: table-cell !important;
                padding: 8px !important;
                border: 1px solid #ddd !important;
                background: #fff !important;
                color: #000 !important;
            }
            
            .pick-list-table input,
            .pick-list-table select {
                width: 100% !important;
                border: none !important;
                background: transparent !important;
                color: #000 !important;
                padding: 0 !important;
                font-size: 0.85rem !important;
            }
            
            /* Buttons - hide in print */
            .btn, button {
                display: none !important;
            }
        }
        
        @page {
            margin: 0.5in;
            size: A4;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Prepare preloadOrder data if available from query parameter
        @if($preloadOrder)
        @php
            $preloadOrderItems = $preloadOrder->items->map(function($item) {
                return [
                    'product'   => $item->item_name ?? ($item->bookIndex?->display_name ?? ($item->book?->name ?? ($item->bundle?->name ?? 'Unknown'))),
                    'item_type' => $item->item_type ?? ($item->bookIndex ? 'Index' : ($item->bundle ? 'Bundle' : 'Book')),
                    'quantity'  => $item->quantity,
                    'price'     => $item->price,
                    'subtotal'  => $item->subtotal,
                    'unit'      => $item->unit,
                    'picked_qty' => 0,
                    'status'    => 'pending',
                    'notes'     => '',
                ];
            })->toArray();
        @endphp
        const preloadOrderData = {
            orderId: {{ $preloadOrder->id }},
            soNumber: '{{ $preloadOrder->so_number }}',
            customer: '{{ $preloadOrder->customer->customer_name ?? "N/A" }}',
            date: '{{ $preloadOrder->created_at->format("Y-m-d") }}',
            items: @json($preloadOrderItems)
        };
        console.log('Preload order data available:', preloadOrderData);
        @else
        const preloadOrderData = null;
        @endif
        
        console.log('Pick List Management page loaded');
        console.log('Pending orders count: {{ isset($pendingOrders) ? $pendingOrders->count() : 0 }}');
        console.log('Completed pick lists count: {{ isset($completedPickLists) ? $completedPickLists->count() : 0 }}');
        console.log('Active pick lists count: {{ isset($pickLists) ? $pickLists->count() : 0 }}');
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded - Initializing Pick List Management');
            
            const detailPanel = document.getElementById('orderDetailPanel');
            const pickListBody = document.getElementById('pickListTableBody');
            const closeDetailBtn = document.getElementById('closeDetailBtn');
            
            console.log('Detail panel found:', !!detailPanel);
            console.log('Pick list body found:', !!pickListBody);
            console.log('Close detail button found:', !!closeDetailBtn);

            // View Items buttons
            document.querySelectorAll('.view-order-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('View order button clicked:', this.dataset.soNumber);
                    
                    try {
                        const soNumber = this.dataset.soNumber;
                        const orderId = this.dataset.orderId;
                        const customer = this.dataset.customer;
                        const date = this.dataset.date;
                        const itemsJson = this.dataset.items;
                        const ecomPlatform = this.dataset.ecomPlatform || '';
                        const pickListId = this.dataset.pickListId || '';
                        
                        console.log('Processing order:', { soNumber, orderId, customer, date, ecomPlatform });
                        
                        // Parse items from data attribute
                        const items = JSON.parse(itemsJson);
                        console.log('Parsed items:', items);

                        // Fill details
                        document.getElementById('detailSONumber').value = soNumber;
                        document.getElementById('detailOrderDate').value = date;
                        document.getElementById('detailCustomerName').value = customer;
                        const freightOpt = this.dataset.freightOption || '';
                        const fwdVal = this.dataset.forwarder || '';
                        document.getElementById('detailFreightOption').value = freightOpt ? (freightOpt.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())) : 'N/A';
                        const fwdGroup = document.getElementById('detailForwarderGroup');
                        if (fwdGroup) {
                            if (freightOpt === 'bill_client' || fwdVal) {
                                fwdGroup.style.display = 'block';
                                document.getElementById('detailForwarder').value = fwdVal || 'N/A';
                            } else {
                                fwdGroup.style.display = 'none';
                            }
                        }
                        document.getElementById('pickListNumber').value = 'PL-' + soNumber;

                        // Store order data for saving
                        detailPanel.dataset.orderId = orderId;
                        detailPanel.dataset.soNumber = soNumber;
                        
                        // Store ecom platform and pick list ID
                        document.getElementById('ecomPlatform').value = ecomPlatform;
                        document.getElementById('pickListIdHidden').value = pickListId;
                        
                        // Show/hide pack management link button based on ecom_platform
                        const packMgmtGroup = document.getElementById('packManagementLinkGroup');
                        if (ecomPlatform) {
                            packMgmtGroup.style.display = 'block';
                            console.log('Pack management link enabled for ecom platform:', ecomPlatform);
                        } else {
                            packMgmtGroup.style.display = 'none';
                            console.log('Pack management link hidden - not an ecom order');
                        }

                        // Fill items table
                        pickListBody.innerHTML = '';
                        items.forEach((item, idx) => {
                            const itemType = item.item_type || 'Book';
                            let typeBadge = '<span class="badge bg-primary ms-1">Book</span>';
                            if (itemType === 'Bundle') {
                                typeBadge = '<span class="badge ms-1" style="background:#6f42c1; color:#fff;">Bundle</span>';
                            } else if (itemType === 'Index') {
                                typeBadge = '<span class="badge bg-info text-dark ms-1">Index</span>';
                            }
                            const tr = document.createElement('tr');
                            if (item.id) tr.dataset.itemId = item.id;
                            if (item.so_item_id) tr.dataset.soItemId = item.so_item_id;
                            tr.innerHTML = `
                                <td>${idx + 1}</td>
                                <td class="fw-bold">${item.product} ${typeBadge}</td>
                                <td style="text-align:center;">${item.quantity} ${item.unit || 'pcs'}</td>
                                <td style="text-align:right;">₱${parseFloat(item.price).toFixed(2)}</td>
                                <td style="text-align:right;">₱${parseFloat(item.subtotal).toFixed(2)}</td>
                                <td><input type="number" class="picked-qty" value="${item.picked_qty || 0}" min="0" max="${item.quantity}" style="text-align:center;"></td>
                                <td>
                                    <select class="status-select form-control" style="border:none;">
                                        <option value="pending" ${(item.status === 'pending') ? 'selected' : ''}>Pending</option>
                                        <option value="picked" ${(item.status === 'picked') ? 'selected' : ''}>Picked</option>
                                        <option value="short" ${(item.status === 'short') ? 'selected' : ''}>Short</option>
                                    </select>
                                </td>
                                <td><input type="text" class="notes-input" placeholder="Notes" value="${item.notes || ''}" style="border:none;"></td>
                            `;
                            pickListBody.appendChild(tr);
                        });

                        document.getElementById('totalItems').value = items.length;
                        document.getElementById('itemsPicked').value = items.reduce((sum, item) => sum + (parseFloat(item.picked_qty) || 0), 0);

                        // Add real-time update handler for picked quantity changes
                        const updateItemsPicked = () => {
                            const rows = document.querySelectorAll('#pickListTableBody tr');
                            let total = 0;
                            rows.forEach(row => {
                                const pickedQtyInput = row.querySelector('.picked-qty');
                                total += parseFloat(pickedQtyInput.value) || 0;
                            });
                            document.getElementById('itemsPicked').value = total;
                        };

                        // Attach event listeners to all picked quantity inputs
                        document.querySelectorAll('#pickListTableBody .picked-qty').forEach(input => {
                            input.addEventListener('input', updateItemsPicked);
                        });

                        // Show panel and scroll to it
                        detailPanel.style.display = 'block';
                        setTimeout(() => {
                            detailPanel.scrollIntoView({ behavior: 'smooth' });
                        }, 100);
                        
                    } catch (error) {
                        console.error('Error:', error);
                        console.error('Items data:', this.dataset.items);
                        alert('Error loading order items: ' + error.message);
                    }
                });
            });

            // Close detail panel
            if (closeDetailBtn) {
                closeDetailBtn.addEventListener('click', function() {
                    detailPanel.style.display = 'none';
                });
            }

            // Link to Pack Management - Quick action button in main table
            document.querySelectorAll('.link-to-pack-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const orderId = this.dataset.orderId;
                    const soNumber = this.dataset.soNumber;
                    
                    console.log('Quick link to pack management clicked for:', { orderId, soNumber });
                    
                    // Redirect directly to packing management
                    window.location.href = `/production/logistic/packing-management?order_id=${orderId}`;
                });
            });

            // Auto-load preloadOrder if available
            if (preloadOrderData) {
                console.log('Auto-loading preload order:', preloadOrderData.soNumber);
                try {
                    const orderData = preloadOrderData;
                    
                    // Fill details
                    document.getElementById('detailSONumber').value = orderData.soNumber;
                    document.getElementById('detailOrderDate').value = orderData.date;
                    document.getElementById('detailCustomerName').value = orderData.customer;
                    document.getElementById('pickListNumber').value = 'PL-' + orderData.soNumber;

                    // Store order data for saving
                    detailPanel.dataset.orderId = orderData.orderId;
                    detailPanel.dataset.soNumber = orderData.soNumber;

                    // Fill items table
                    pickListBody.innerHTML = '';
                    orderData.items.forEach((item, idx) => {
                        const itemType = item.item_type || 'Book';
                        let typeBadge = '<span class="badge bg-primary ms-1">Book</span>';
                        if (itemType === 'Bundle') {
                            typeBadge = '<span class="badge ms-1" style="background:#6f42c1; color:#fff;">Bundle</span>';
                        } else if (itemType === 'Index') {
                            typeBadge = '<span class="badge bg-info text-dark ms-1">Index</span>';
                        }
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${idx + 1}</td>
                            <td class="fw-bold">${item.product} ${typeBadge}</td>
                            <td style="text-align:center;">${item.quantity} ${item.unit || 'pcs'}</td>
                            <td style="text-align:right;">₱${parseFloat(item.price).toFixed(2)}</td>
                            <td style="text-align:right;">₱${parseFloat(item.subtotal).toFixed(2)}</td>
                            <td><input type="number" class="picked-qty" value="${item.picked_qty || 0}" min="0" max="${item.quantity}" style="text-align:center;"></td>
                            <td>
                                <select class="status-select form-control" style="border:none;">
                                    <option value="pending" ${(item.status === 'pending') ? 'selected' : ''}>Pending</option>
                                    <option value="picked" ${(item.status === 'picked') ? 'selected' : ''}>Picked</option>
                                    <option value="short" ${(item.status === 'short') ? 'selected' : ''}>Short</option>
                                </select>
                            </td>
                            <td><input type="text" class="notes-input" placeholder="Notes" value="${item.notes || ''}" style="border:none;"></td>
                        `;
                        pickListBody.appendChild(tr);
                    });

                    document.getElementById('totalItems').value = orderData.items.length;
                    document.getElementById('itemsPicked').value = 0;

                    // Add real-time update handler for picked quantity changes
                    const updateItemsPicked = () => {
                        const rows = document.querySelectorAll('#pickListTableBody tr');
                        let total = 0;
                        rows.forEach(row => {
                            const pickedQtyInput = row.querySelector('.picked-qty');
                            total += parseFloat(pickedQtyInput.value) || 0;
                        });
                        document.getElementById('itemsPicked').value = total;
                    };

                    // Attach event listeners to all picked quantity inputs
                    document.querySelectorAll('#pickListTableBody .picked-qty').forEach(input => {
                        input.addEventListener('input', updateItemsPicked);
                    });

                    // Show panel and scroll to it
                    detailPanel.style.display = 'block';
                    setTimeout(() => {
                        detailPanel.scrollIntoView({ behavior: 'smooth' });
                    }, 100);

                    console.log('Preload order auto-loaded successfully');
                } catch (error) {
                    console.error('Error auto-loading preload order:', error);
                }
            }
        });

        // Function to save picked items
        function savePickedItems() {
            console.log('savePickedItems called');
            
            const detailPanel = document.getElementById('orderDetailPanel');
            const orderId = detailPanel.dataset.orderId;
            const soNumber = detailPanel.dataset.soNumber;
            const rows = document.querySelectorAll('#pickListTableBody tr');
            
            console.log('Saving picked items for:', { orderId, soNumber, rowCount: rows.length });
            
            const pickedItems = [];
            let totalPicked = 0;
            let hasInvalidData = false;

            rows.forEach((row, idx) => {
                const pickedQtyInput = row.querySelector('.picked-qty');
                const statusSelect = row.querySelector('.status-select');
                const notesInput = row.querySelector('.notes-input');
                
                const pickedQty = parseFloat(pickedQtyInput.value) || 0;
                const status = statusSelect.value;
                const notes = notesInput.value;
                const product = row.cells[1].innerText;

                console.log(`Item ${idx}:`, { product, pickedQty, status, notes });
                
                // Validate: if status is 'picked', picked_qty should be > 0
                if (status === 'picked' && pickedQty === 0) {
                    alert(`Item "${product}" is marked as "Picked" but has 0 quantity. Please enter a quantity or change status.`);
                    hasInvalidData = true;
                    return;
                }

                pickedItems.push({
                    id: row.dataset.itemId || null,
                    so_item_id: row.dataset.soItemId || null,
                    product: product,
                    picked_qty: pickedQty,
                    status: status,
                    notes: notes,
                    item_index: idx
                });

                totalPicked += pickedQty;
            });

            if (hasInvalidData) {
                console.log('Validation failed - invalid data found');
                return;
            }

            // Confirmation
            const confirmMsg = `Save picked items for SO ${soNumber}?\n\nTotal items picked: ${totalPicked}`;
            if (!confirm(confirmMsg)) {
                console.log('User cancelled save operation');
                return;
            }

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                            document.querySelector('input[name="_token"]')?.value;
            
            console.log('CSRF token found:', !!csrfToken);
            
            if (!csrfToken) {
                alert('Security token not found. Please refresh the page.');
                console.error('CSRF token not found!');
                return;
            }

            // Show loading
            const saveBtn = document.getElementById('savePickedBtn');
            const originalText = saveBtn.innerHTML;
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="las la-spinner la-spin"></i> Saving...';

            const remarksValue = document.getElementById('pickListRemarks')?.value || '';

            // Send to backend
            console.log('Sending request to /production/logistic/pick-list/save with data:', {
                order_id: orderId,
                so_number: soNumber,
                picked_items: pickedItems,
                remarks: remarksValue
            });
            
            fetch('/production/logistic/pick-list/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    order_id: orderId,
                    so_number: soNumber,
                    picked_items: pickedItems,
                    remarks: remarksValue
                })
            })
            .then(response => {
                console.log('Response received:', { status: response.status, ok: response.ok });
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
                
                if (data.success) {
                    // Show brief success feedback
                    saveBtn.style.background = '#28a745';
                    saveBtn.innerHTML = '<i class="las la-check-circle"></i> Saved!';
                    setTimeout(() => {
                        saveBtn.style.background = '#28a745';
                        saveBtn.innerHTML = originalText;
                        // Optionally refresh the page or hide the panel
                        window.location.reload();
                    }, 2000);
                } else {
                    console.error('Save failed:', data.message);
                    alert('Error: ' + (data.message || 'Failed to save picked items'));
                }
            })
            .catch(error => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
                console.error('Fetch error:', error);
                console.error('Error message:', error.message);
                console.error('Error stack:', error.stack);
                alert('Error saving picked items: ' + error.message);
            });
        }

        // Function to print pick list
        function printPickList() {
            const detailPanel = document.getElementById('orderDetailPanel');
            
            // Check if detail panel has content
            if (!detailPanel.querySelector('#detailSONumber').value) {
                alert('Please select a pick list to print first.');
                return;
            }
            
            // Force show the detail panel for printing
            detailPanel.style.display = 'block';
            detailPanel.style.visibility = 'visible';
            
            // Small delay to ensure rendering
            setTimeout(() => {
                window.print();
            }, 50);
        }

        // Function to link to pack management
        function linkToPackManagement() {
            console.log('Link to pack management clicked');
            
            const detailPanel = document.getElementById('orderDetailPanel');
            const orderId = detailPanel.dataset.orderId;
            const soNumber = detailPanel.dataset.soNumber;
            const ecomPlatform = document.getElementById('ecomPlatform').value;
            
            console.log('Pack management link data:', { orderId, soNumber, ecomPlatform });
            
            // Validate that we have an ecom order
            if (!ecomPlatform) {
                alert('This feature is only available for E-Com orders (Direct Invoice).');
                return;
            }
            
            // First, save the picked items
            const rows = document.querySelectorAll('#pickListTableBody tr');
            const pickedItems = [];
            let totalPicked = 0;
            let hasInvalidData = false;

            rows.forEach((row, idx) => {
                const pickedQtyInput = row.querySelector('.picked-qty');
                const statusSelect = row.querySelector('.status-select');
                const notesInput = row.querySelector('.notes-input');
                
                const pickedQty = parseFloat(pickedQtyInput.value) || 0;
                const status = statusSelect.value;
                const notes = notesInput.value;
                const product = row.cells[1].innerText;

                // Validate: if status is 'picked', picked_qty should be > 0
                if (status === 'picked' && pickedQty === 0) {
                    alert(`Item "${product}" is marked as "Picked" but has 0 quantity. Please enter a quantity or change status.`);
                    hasInvalidData = true;
                    return;
                }

                pickedItems.push({
                    product: product,
                    picked_qty: pickedQty,
                    status: status,
                    notes: notes,
                    item_index: idx
                });

                totalPicked += pickedQty;
            });

            if (hasInvalidData) {
                return;
            }

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                            document.querySelector('input[name="_token"]')?.value;
            
            if (!csrfToken) {
                alert('Security token not found. Please refresh the page.');
                return;
            }

            // Show loading state
            const linkBtn = document.getElementById('linkToPackBtn');
            const originalText = linkBtn.innerHTML;
            linkBtn.disabled = true;
            linkBtn.innerHTML = '<i class="las la-spinner la-spin"></i> Saving & Linking...';

            // Save picked items first
            console.log('Saving picked items before linking to pack management...');
            
            fetch('/production/logistic/pick-list/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    order_id: orderId,
                    so_number: soNumber,
                    picked_items: pickedItems
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Items saved successfully:', data);
                
                if (data.success) {
                    // Now navigate to pack management
                    console.log('Navigating to pack management for order:', orderId);
                    
                    // Redirect to pack management with the order ID
                    // This will show the order in the packing queue ready for packing
                    const packMgmtUrl = `/production/logistic/packing-management?order_id=${orderId}&platform=${ecomPlatform}`;
                    console.log('Redirect URL:', packMgmtUrl);
                    
                    // Show success feedback
                    linkBtn.style.background = '#28a745';
                    linkBtn.innerHTML = '<i class="las la-check-circle"></i> Linked!';
                    
                    // Redirect after brief delay
                    setTimeout(() => {
                        window.location.href = packMgmtUrl;
                    }, 1500);
                } else {
                    linkBtn.disabled = false;
                    linkBtn.innerHTML = originalText;
                    alert('Error: ' + (data.message || 'Failed to save picked items'));
                }
            })
            .catch(error => {
                linkBtn.disabled = false;
                linkBtn.innerHTML = originalText;
                console.error('Error linking to pack management:', error);
                alert('Error: ' + error.message);
            });
        }

        // Handle Recreate Pick List button click
        document.querySelectorAll('.recreate-picklist-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                console.log('Recreate pick list button clicked');
                
                const orderId = this.dataset.orderId;
                const soNumber = this.dataset.soNumber;
                const customer = this.dataset.customer;
                const freight = this.dataset.freight || 'N/A';
                const forwarder = this.dataset.forwarder || '';
                const remarks = this.dataset.remarks || '';
                
                console.log('Recreating pick list for:', { orderId, soNumber, customer });
                
                try {
                    const items = JSON.parse(this.dataset.items);
                    console.log('Parsed items for recreation:', items);
                
                // Show the order detail panel
                const detailPanel = document.getElementById('orderDetailPanel');
                detailPanel.style.display = 'block';
                
                // Set data attributes on panel for saving
                detailPanel.dataset.orderId = orderId;
                detailPanel.dataset.soNumber = soNumber;
                
                // Populate the form
                document.getElementById('detailSONumber').value = soNumber;
                document.getElementById('detailOrderDate').value = this.dataset.date || new Date().toISOString().split('T')[0];
                document.getElementById('detailCustomerName').value = customer;
                document.getElementById('detailFreightOption').value = freight;
                const fwdGroup = document.getElementById('detailForwarderGroup');
                const fwdInput = document.getElementById('detailForwarder');
                if (fwdGroup && fwdInput) {
                    if (forwarder) {
                        fwdInput.value = forwarder;
                        fwdGroup.style.display = 'block';
                    } else {
                        fwdGroup.style.display = 'none';
                    }
                }
                const remarksInput = document.getElementById('pickListRemarks');
                if (remarksInput) {
                    remarksInput.value = remarks;
                }
                document.getElementById('pickListNumber').value = 'PL-' + soNumber + '-' + Date.now();
                document.getElementById('pickListStatus').value = 'in_progress';
                
                // Populate items table with previous pick quantities, status and notes
                const pickListBody = document.getElementById('pickListTableBody');
                pickListBody.innerHTML = '';
                
                items.forEach((item, index) => {
                    const row = document.createElement('tr');
                    const itemStatus = item.status || 'pending';
                    const itemNotes = item.notes || '';
                   // const itemPickedQty = (item.picked_qty !== undefined && item.picked_qty !== null && parseFloat(item.picked_qty) > 0) ? parseFloat(item.picked_qty) : (itemStatus !== 'unpicked' ? item.quantity : 0);
                  const itemPickedQty =
    item.picked_qty !== undefined &&
    item.picked_qty !== null &&
    item.picked_qty !== ''
        ? parseFloat(item.picked_qty)
        : 0; 
                  
                  const itemType = item.item_type || '';

                    let typeBadge = '';
                    if (itemType === 'Bundle') {
                        typeBadge = '<span class="badge ms-1" style="background:#6f42c1; color:#fff; font-size:10px; padding:2px 5px;">Bundle</span>';
                    } else if (itemType === 'Index') {
                        typeBadge = '<span class="badge bg-info text-white ms-1" style="font-size:10px; padding:2px 5px;">Index</span>';
                    }

                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td class="fw-bold">${item.product} ${typeBadge}</td>
                        <td class="text-center">${item.quantity} ${item.unit || 'pcs'}</td>
                        <td class="text-right">₱${parseFloat(item.price || 0).toFixed(2)}</td>
                        <td class="text-right">₱${(parseFloat(item.quantity || 0) * parseFloat(item.price || 0)).toFixed(2)}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm picked-qty" 
                                   value="${itemPickedQty}" min="0" max="${item.quantity}" style="width: 80px; text-align: center;">
                        </td>
                        <td>
                            <select class="form-control form-control-sm status-select" style="width: 100px;">
                                <option value="pending" ${itemStatus === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="picked" ${itemStatus === 'picked' ? 'selected' : ''}>Picked</option>
                                <option value="short" ${itemStatus === 'short' ? 'selected' : ''}>Short</option>
                                <option value="not_available" ${itemStatus === 'not_available' ? 'selected' : ''}>Not Available</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm notes-input" 
                                   value="${itemNotes.replace(/"/g, '&quot;')}" placeholder="Notes" style="width: 150px;">
                        </td>
                    `;
                    pickListBody.appendChild(row);
                });
                
                // Update summary
                document.getElementById('totalItems').value = items.length;
                document.getElementById('itemsPicked').value = items.reduce((sum, item) => sum + (parseFloat(item.picked_qty) || 0), 0);

                // Add real-time update handler for picked quantity changes
                const updateItemsPickedRecreate = () => {
                    const rows = document.querySelectorAll('#pickListTableBody tr');
                    let total = 0;
                    rows.forEach(row => {
                        const pickedQtyInput = row.querySelector('.picked-qty');
                        total += parseFloat(pickedQtyInput.value) || 0;
                    });
                    document.getElementById('itemsPicked').value = total;
                };

                const onRecreatePickedQtyChange = (input) => {
                    const row = input.closest('tr');
                    if (row) {
                        const pickedQty = parseFloat(input.value) || 0;
                        const reqQtyMatch = row.cells[2]?.innerText.match(/[\d.]+/);
                        const requestedQty = reqQtyMatch ? parseFloat(reqQtyMatch[0]) : 0;
                        const statusSelect = row.querySelector('.status-select');
                        if (statusSelect) {
                            if (pickedQty >= requestedQty && requestedQty > 0) {
                                statusSelect.value = 'picked';
                            } else if (pickedQty > 0 && pickedQty < requestedQty) {
                                statusSelect.value = 'short';
                            } else if (pickedQty === 0) {
                                statusSelect.value = 'pending';
                            }
                        }
                    }
                    updateItemsPickedRecreate();
                };

                // Attach event listeners to all picked quantity inputs
                document.querySelectorAll('#pickListTableBody .picked-qty').forEach(input => {
                    input.addEventListener('input', () => onRecreatePickedQtyChange(input));
                });
                
                // Add info message about recreation
                let infoMsg = document.querySelector('.recreation-info');
                if (!infoMsg) {
                    infoMsg = document.createElement('div');
                    infoMsg.className = 'alert alert-info recreation-info';
                    detailPanel.insertBefore(infoMsg, detailPanel.firstChild);
                }
                infoMsg.innerHTML = '<i class="las la-info-circle"></i> <strong>Recreation Mode:</strong> This will create a new pick list with the same items.';
                
                // Scroll to form
                detailPanel.scrollIntoView({ behavior: 'smooth' });
                
                } catch (error) {
                    console.error('Error:', error);
                    console.error('Items data:', this.dataset.items);
                    alert('Error loading order items: ' + error.message);
                }
            });
        });

        function openPickListShippingLabel() {
            const detailPanel = document.getElementById('orderDetailPanel');
            const orderId = detailPanel?.dataset?.orderId || document.getElementById('pickListIdHidden')?.value;
            if (!orderId) {
                alert('Please select a pick list to print first.');
                return;
            }
            window.open(`/production/logistic/shipping-label/${orderId}`, '_blank');
        }
    </script>
    @endpush
</x-app-layout>
