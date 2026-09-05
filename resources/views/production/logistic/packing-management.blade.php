<x-app-layout :title="'Packing Management'" :sidebar="'production'">
@php
$txnTypeLabels = [
    'paid'                  => 'Paid Transaction',
    'charge'                => 'Charge Transaction',
    'area_consignment'      => 'Area Consignment',
    'area_sales_consignment'=> 'Area Sales Consignment',
    'direct_consignment'    => 'Direct Consignment',
    'foreign'               => 'Foreign Order',
    'complimentary'         => 'Complimentary',
    'cod'                   => 'Due on Receipt (COD)',
    'COD'                   => 'Due on Receipt (COD)',
    'evaluation'            => 'Evaluation',
    'Evaluation'            => 'Evaluation',
    'ecom_direct'           => 'E-Commerce Direct',
    'calculator_pos'        => 'Direct POS',
    'Credit'                => 'Credit',
    'Prepaid'               => 'Prepaid',
];
$isAdmin = auth()->check() && auth()->user()->isSuperAdmin();

$cachedBookIndexOptions = '';
foreach ($allBookIndexes ?? [] as $bIndex) {
    $cachedBookIndexOptions .= '<option value="' . $bIndex->id . '">' . e($bIndex->display_name) . '</option>';
}
@endphp
    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        .status-ready { background-color: #fff3cd; color: #856404; }
        .status-in-progress { background-color: #cce5ff; color: #004085; }
        .status-packed { background-color: #d4edda; color: #155724; }
        .status-partial { background-color: #e2e3e5; color: #383d41; }

        /* Floating Sticky Bulk Action Bar at Bottom of Screen */
        .ready-bulk-floating-bar {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: #1e1e2d;
            color: #ffffff;
            padding: 10px 24px;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            z-index: 1060;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .ready-bulk-floating-bar.hidden {
            opacity: 0;
            visibility: hidden;
            transform: translate(-50%, 35px);
            pointer-events: none;
        }

        /* Platform badges for Completed Drop-off */
        .platform-badge { padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .platform-lazada { background: #0f146d; color: #fff; }
        .platform-shopee { background: #ee4d2d; color: #fff; }
        .platform-tiktok { background: #010101; color: #fff; }
        .platform-cob { background: #6f42c1; color: #fff; }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex">
                    <div>
                        <h4 class="fs-24 mb-0 text-black">Packing Management</h4>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs" id="packingTabs" role="tablist" style="border-bottom: 2px solid #ddd; margin-bottom: 2rem;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="packing-queue-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#packing-queue-content" data-target="#packing-queue-content" type="button" role="tab" aria-controls="packing-queue-content" aria-selected="true" style="font-weight: 600; color: #333;">
                                <i class="fas fa-boxes" style="margin-right: 0.5rem;"></i>Packing Queue
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ecom-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#ecom-direct-content" data-target="#ecom-direct-content" type="button" role="tab" aria-controls="ecom-direct-content" aria-selected="false" style="font-weight: 600; color: #666;">
                                <i class="fas fa-shopping-bag" style="margin-right: 0.5rem;"></i>E-Commerce Direct <span class="badge bg-info" style="margin-left: 0.5rem;">{{ $ecomByPlatform['lazada']->count() + $ecomByPlatform['shopee']->count() + $ecomByPlatform['tiktok']->count() + ($ecomByPlatform['cob']?->count() ?? 0) }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ready-pickup-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#ready-pickup-content" data-target="#ready-pickup-content" type="button" role="tab" aria-controls="ready-pickup-content" aria-selected="false" style="font-weight: 600; color: #666;">
                                <i class="fas fa-truck" style="margin-right: 0.5rem;"></i>Ready for Pickup/Drop-off <span class="badge bg-success" style="margin-left: 0.5rem;">{{ count($readyForPickupOrders) }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="complimentary-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#complimentary-content" data-target="#complimentary-content" type="button" role="tab" aria-controls="complimentary-content" aria-selected="false" style="font-weight: 600; color: #666;">
                                <i class="fas fa-gift" style="margin-right: 0.5rem; color: #6f42c1;"></i>Complimentary <span class="badge" style="margin-left: 0.5rem; background-color: #6f42c1; color: #fff;">{{ $complimentaryPackingOrders->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="team-stocks-packing-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#team-stocks-packing-content" data-target="#team-stocks-packing-content" type="button" role="tab" aria-controls="team-stocks-packing-content" aria-selected="false" style="font-weight: 600; color: #666;">
                                <i class="fas fa-boxes text-danger" style="margin-right: 0.5rem;"></i>Team Stock Transfers <span class="badge bg-danger" style="margin-left: 0.5rem;">{{ isset($teamStockPackingTransfers) ? $teamStockPackingTransfers->count() : 0 }}</span>
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Contents -->
                    <div class="tab-content" id="packingTabContent">
                        <!-- Packing Queue Tab -->
                        <div class="tab-pane fade show active" id="packing-queue-content" role="tabpanel" aria-labelledby="packing-queue-tab">
                            <!-- Bulk Action Toolbar -->
                            <div id="bulkActionToolbar" style="display: none; background-color: #e8f4f8; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; border-left: 4px solid #007bff;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <span style="color: #333; font-weight: 500;">
                                        <span id="selectedCount">0</span> order(s) selected
                                    </span>
                                    <div class="d-flex gap-2">
                                        <button type="button" id="setReadyPickupBtn" class="btn btn-primary" style="background-color: #28a745; border: none; padding: 0.5rem 1.5rem;">
                                            <i class="fas fa-check" style="margin-right: 0.5rem;"></i>Mark Selected as Packed
                                        </button>
                                        <button type="button" id="clearSelectionBtn" class="btn btn-secondary" style="padding: 0.5rem 1.5rem;">
                                            Clear Selection
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="packingTable" class="display" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 30px;"><input type="checkbox" id="selectAllCheckbox" style="cursor: pointer;"></th>
                                            <th>SO #</th>
                                            <th>Transaction Type</th>
                                            <th>Company</th>
                                            <th>SI Signed</th>
                                            <th>Total Items</th>
                                            <th>Packed Items</th>
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                @foreach($packingOrders as $order)
                                @php
                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                    $totalItems = $order->items->count();
                                    
                                    if($packedCount === 0) {
                                        $statusClass = 'status-ready';
                                        $statusText = 'Ready for Packing';
                                    } elseif($packedCount === $totalItems && $totalItems > 0) {
                                        $statusClass = 'status-packed';
                                        $statusText = 'Fully Packed';
                                    } else {
                                        $statusClass = 'status-partial';
                                        $statusText = 'Partially Packed';
                                    }
                                    $isFullyPacked = ($packedCount === $totalItems && $totalItems > 0);
                                @endphp
                                <tr class="packing-row" data-order-id="{{ $order->id }}">
                                    <td><input type="checkbox" class="order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;" {{ !$isFullyPacked ? 'disabled' : '' }}></td>
                                    <td>
                                        <strong>{{ $order->so_number }}</strong>
                                        @php
                                            $isEcom = ($order->type === 'ecom_direct' || !empty($order->ecom_platform) || str_contains(strtolower($order->so_number ?? ''), 'ecom') || !empty($order->platform_order_id));
                                            $ecomId = $order->platform_order_id ?: ($order->ref_number ?: ($order->po_number ?? null));
                                        @endphp
                                        @if($isEcom || !empty($ecomId))
                                            <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                        @endif
                                        @if($order->cancellation_date)
                                            <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="fas fa-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($order->cancellation_date)->format('M d, Y') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php $ordType = $order->type ?: ($order->transaction_type ?? ''); @endphp
                                        <span class="badge bg-light text-dark border">{{ $txnTypeLabels[$ordType] ?? (ucwords(str_replace('_', ' ', $ordType)) ?: 'N/A') }}</span>
                                    </td>
                                    <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ $totalItems }}</td>
                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                     @php
                                         $pkgCurr = $order->currency ?? 'PHP';
                                         $pkgSym = ($pkgCurr === 'USD' ? '$' : '₱');
                                         $pkgTotalAmt = ($order->total_amount !== null && (float)$order->total_amount > 0)
                                             ? (float)$order->total_amount
                                             : $order->items->sum(function($i) {
                                                 $q = (float)($i->quantity && (float)$i->quantity > 0 ? $i->quantity : ($i->customer_selected_qty ?: ($i->selected_qty ?: 0)));
                                                 $p = (float)($i->price ?: 0);
                                                 return (float)($i->subtotal && (float)$i->subtotal > 0 ? $i->subtotal : $q * $p);
                                             });
                                     @endphp
                                     <td class="fw-bold">{{ $pkgSym }}{{ number_format($pkgTotalAmt, 2) }}</td>
                                     <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                     <td>
                                         <div class="d-flex gap-2">
                                             <button type="button" class="btn btn-danger shadow view-order-btn"
                                                     data-order-id="{{ $order->id }}"
                                                     data-so-number="{{ $order->so_number }}"
                                                     data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                     data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                     data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                     title="View Details"
                                                     style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                 <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                             </button>
                                             <button type="button" class="btn btn-success shadow mark-packed-btn"
                                                     onclick="markOrderAsPackedAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                     data-order-id="{{ $order->id }}"
                                                     data-so-number="{{ $order->so_number }}"
                                                     title="Mark as Packed"
                                                     style="background: {{ $isFullyPacked ? '#28a745' : '#ffc107' }}; color: {{ $isFullyPacked ? '#fff' : '#000' }}; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                 <i class="fas fa-check" style="font-size: 0.9rem; pointer-events: none;"></i>
                                             </button>
                                             @if($isAdmin)
                                                 <a href="{{ route('marketing.sales-orders.edit', $order->id) }}" class="btn btn-warning shadow btn-xs sharp" title="Edit Order" style="padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-pencil-alt"></i></a>
                                                 <form action="{{ route('marketing.sales-orders.destroy', $order->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete Order {{ $order->so_number }}?');">
                                                     @csrf
                                                     @method('DELETE')
                                                     <button type="submit" class="btn btn-danger shadow btn-xs sharp" title="Delete Order" style="padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-trash"></i></button>
                                                 </form>
                                             @endif
                                         </div>
                                     </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                            </div>
                        </div>

                        <!-- E-Commerce Direct Tab -->
                        <div class="tab-pane fade" id="ecom-direct-content" role="tabpanel" aria-labelledby="ecom-tab">
                            <!-- Platform Sub-tabs -->
                            <ul class="nav nav-tabs mb-3" id="ecomTabs" role="tablist" style="border-bottom: 2px solid #dee2e6;">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="lazada-ecom-tab" data-bs-toggle="tab" data-bs-target="#lazada-ecom-content" type="button" role="tab" aria-controls="lazada-ecom-content" aria-selected="true">
                                        <i class="las la-shopping-bag me-2"></i>Lazada <span class="badge bg-primary ms-2">{{ $ecomByPlatform['lazada']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="shopee-ecom-tab" data-bs-toggle="tab" data-bs-target="#shopee-ecom-content" type="button" role="tab" aria-controls="shopee-ecom-content" aria-selected="false">
                                        <i class="las la-shopping-bag me-2"></i>Shopee <span class="badge bg-danger ms-2">{{ $ecomByPlatform['shopee']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tiktok-ecom-tab" data-bs-toggle="tab" data-bs-target="#tiktok-ecom-content" type="button" role="tab" aria-controls="tiktok-ecom-content" aria-selected="false">
                                        <i class="las la-music me-2"></i>TikTok <span class="badge bg-dark ms-2">{{ $ecomByPlatform['tiktok']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="cob-ecom-tab" data-bs-toggle="tab" data-bs-target="#cob-ecom-content" type="button" role="tab" aria-controls="cob-ecom-content" aria-selected="false">
                                        <i class="las la-building me-2" style="color: #6f42c1;"></i>COB <span class="badge ms-2" style="background-color: #6f42c1; color: #fff;">{{ $ecomByPlatform['cob']->count() }}</span>
                                    </button>
                                </li>
                            </ul>

                            <!-- Platform Sub-tabs Content -->
                            <div class="tab-content" id="ecomTabsContent">
                                <!-- Lazada Tab -->
                                <div class="tab-pane fade show active" id="lazada-ecom-content" role="tabpanel" aria-labelledby="lazada-ecom-tab">
                                    <div class="table-responsive">
                                        <table id="lazadaPackingTable" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30px;"><input type="checkbox" class="ecom-select-all-checkbox" data-platform="lazada" style="cursor: pointer;"></th>
                                                    <th>SO #</th>
                                                    <th>Transaction Type</th>
                                                    <th>Customer</th>
                                                    <th>SI Signed</th>
                                                    <th>Total Items</th>
                                                    <th>Packed Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($ecomByPlatform['lazada'] as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                                    $totalItems = $order->items->count();
                                                    
                                                    if($packedCount === 0) {
                                                        $statusClass = 'status-ready';
                                                        $statusText = 'Ready for Packing';
                                                    } elseif($packedCount === $totalItems && $totalItems > 0) {
                                                        $statusClass = 'status-packed';
                                                        $statusText = 'Fully Packed';
                                                    } else {
                                                        $statusClass = 'status-partial';
                                                        $statusText = 'Partially Packed';
                                                    }
                                                    $isFullyPacked = ($packedCount === $totalItems && $totalItems > 0);
                                                @endphp
                                                <tr class="packing-row" data-order-id="{{ $order->id }}">
                                                    <td><input type="checkbox" class="ecom-order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;" {{ !$isFullyPacked ? 'disabled' : '' }}></td>
                                                    <td>
                                        <strong>{{ $order->so_number }}</strong>
                                        @php
                                            $isEcom = ($order->type === 'ecom_direct' || !empty($order->ecom_platform) || str_contains(strtolower($order->so_number ?? ''), 'ecom') || !empty($order->platform_order_id));
                                            $ecomId = $order->platform_order_id ?: ($order->ref_number ?: ($order->po_number ?? null));
                                        @endphp
                                        @if($isEcom || !empty($ecomId))
                                            <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                        @endif
                                        @if($order->cancellation_date)
                                            <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="fas fa-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($order->cancellation_date)->format('M d, Y') }}</span>
                                        @endif
                                    </td>
                                                    <td>
                                                        @php $ordType = $order->type ?: ($order->transaction_type ?? ''); @endphp
                                        <span class="badge bg-light text-dark border">{{ $txnTypeLabels[$ordType] ?? (ucwords(str_replace('_', ' ', $ordType)) ?: 'N/A') }}</span>
                                                    </td>
                                                    <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                                    @php
                                                        $pkgCurr = $order->currency ?? 'PHP';
                                                        $pkgSym = ($pkgCurr === 'USD' ? '$' : '₱');
                                                    @endphp
                                                    <td class="fw-bold">{{ $pkgSym }}{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                    
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                    title="View Details"
                                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-success shadow mark-packed-btn"
                                                                    onclick="markOrderAsPackedAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Mark as Packed"
                                                                    style="background: #28a745; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-check" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-danger shadow delete-ecom-order-btn"
                                                                    onclick="deleteEcomOrderAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Delete Order"
                                                                    style="border: 1px solid #dc3545; color: #dc3545; background: #fff; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-trash-alt" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                        </div>
                                                @empty
                                                <tr>
                                                    <td colspan="10" class="text-center">No Lazada orders found</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Shopee Tab -->
                                <div class="tab-pane fade" id="shopee-ecom-content" role="tabpanel" aria-labelledby="shopee-ecom-tab">
                                    <div class="table-responsive">
                                        <table id="shopeePackingTable" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30px;"><input type="checkbox" class="ecom-select-all-checkbox" data-platform="shopee" style="cursor: pointer;"></th>
                                                    <th>SO #</th>
                                                    <th>Transaction Type</th>
                                                    <th>Customer</th>
                                                    <th>SI Signed</th>
                                                    <th>Total Items</th>
                                                    <th>Packed Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($ecomByPlatform['shopee'] as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                                    $totalItems = $order->items->count();
                                                    
                                                    if($packedCount === 0) {
                                                        $statusClass = 'status-ready';
                                                        $statusText = 'Ready for Packing';
                                                    } elseif($packedCount === $totalItems && $totalItems > 0) {
                                                        $statusClass = 'status-packed';
                                                        $statusText = 'Fully Packed';
                                                    } else {
                                                        $statusClass = 'status-partial';
                                                        $statusText = 'Partially Packed';
                                                    }
                                                    $isFullyPacked = ($packedCount === $totalItems && $totalItems > 0);
                                                @endphp
                                                <tr class="packing-row" data-order-id="{{ $order->id }}">
                                                    <td><input type="checkbox" class="ecom-order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;" {{ !$isFullyPacked ? 'disabled' : '' }}></td>
                                                    <td>
                                        <strong>{{ $order->so_number }}</strong>
                                        @php
                                            $isEcom = ($order->type === 'ecom_direct' || !empty($order->ecom_platform) || str_contains(strtolower($order->so_number ?? ''), 'ecom') || !empty($order->platform_order_id));
                                            $ecomId = $order->platform_order_id ?: ($order->ref_number ?: ($order->po_number ?? null));
                                        @endphp
                                        @if($isEcom || !empty($ecomId))
                                            <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                        @endif
                                        @if($order->cancellation_date)
                                            <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="fas fa-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($order->cancellation_date)->format('M d, Y') }}</span>
                                        @endif
                                    </td>
                                                    <td>
                                                        @php $ordType = $order->type ?: ($order->transaction_type ?? ''); @endphp
                                        <span class="badge bg-light text-dark border">{{ $txnTypeLabels[$ordType] ?? (ucwords(str_replace('_', ' ', $ordType)) ?: 'N/A') }}</span>
                                                    </td>
                                                    <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                                    @php
                                                        $pkgCurr = $order->currency ?? 'PHP';
                                                        $pkgSym = ($pkgCurr === 'USD' ? '$' : '₱');
                                                    @endphp
                                                    <td class="fw-bold">{{ $pkgSym }}{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                    
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                    title="View Details"
                                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-success shadow mark-packed-btn"
                                                                    onclick="markOrderAsPackedAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Mark as Packed"
                                                                    style="background: #28a745; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-check" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-danger shadow delete-ecom-order-btn"
                                                                    onclick="deleteEcomOrderAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Delete Order"
                                                                    style="border: 1px solid #dc3545; color: #dc3545; background: #fff; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-trash-alt" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="10" class="text-center">No Shopee orders found</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- TikTok Tab -->
                                <div class="tab-pane fade" id="tiktok-ecom-content" role="tabpanel" aria-labelledby="tiktok-ecom-tab">
                                    <div class="table-responsive">
                                        <table id="tiktokPackingTable" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30px;"><input type="checkbox" class="ecom-select-all-checkbox" data-platform="tiktok" style="cursor: pointer;"></th>
                                                    <th>SO #</th>
                                                    <th>Transaction Type</th>
                                                    <th>Customer</th>
                                                    <th>SI Signed</th>
                                                    <th>Total Items</th>
                                                    <th>Packed Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($ecomByPlatform['tiktok'] as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                                    $totalItems = $order->items->count();
                                                    
                                                    if($packedCount === 0) {
                                                        $statusClass = 'status-ready';
                                                        $statusText = 'Ready for Packing';
                                                    } elseif($packedCount === $totalItems && $totalItems > 0) {
                                                        $statusClass = 'status-packed';
                                                        $statusText = 'Fully Packed';
                                                    } else {
                                                        $statusClass = 'status-partial';
                                                        $statusText = 'Partially Packed';
                                                    }
                                                    $isFullyPacked = ($packedCount === $totalItems && $totalItems > 0);
                                                @endphp
                                                <tr class="packing-row" data-order-id="{{ $order->id }}">
                                                    <td><input type="checkbox" class="ecom-order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;" {{ !$isFullyPacked ? 'disabled' : '' }}></td>
                                                    <td>
                                        <strong>{{ $order->so_number }}</strong>
                                        @php
                                            $isEcom = ($order->type === 'ecom_direct' || !empty($order->ecom_platform) || str_contains(strtolower($order->so_number ?? ''), 'ecom') || !empty($order->platform_order_id));
                                            $ecomId = $order->platform_order_id ?: ($order->ref_number ?: ($order->po_number ?? null));
                                        @endphp
                                        @if($isEcom || !empty($ecomId))
                                            <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                        @endif
                                        @if($order->cancellation_date)
                                            <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="fas fa-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($order->cancellation_date)->format('M d, Y') }}</span>
                                        @endif
                                    </td>
                                                    <td>
                                                        @php $ordType = $order->type ?: ($order->transaction_type ?? ''); @endphp
                                        <span class="badge bg-light text-dark border">{{ $txnTypeLabels[$ordType] ?? (ucwords(str_replace('_', ' ', $ordType)) ?: 'N/A') }}</span>
                                                    </td>
                                                    <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                                    @php
                                                        $pkgCurr = $order->currency ?? 'PHP';
                                                        $pkgSym = ($pkgCurr === 'USD' ? '$' : '₱');
                                                    @endphp
                                                    <td class="fw-bold">{{ $pkgSym }}{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                    
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                    title="View Details"
                                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-success shadow mark-packed-btn"
                                                                    onclick="markOrderAsPackedAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Mark as Packed"
                                                                    style="background: #28a745; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-check" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-danger shadow delete-ecom-order-btn"
                                                                    onclick="deleteEcomOrderAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Delete Order"
                                                                    style="border: 1px solid #dc3545; color: #dc3545; background: #fff; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-trash-alt" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="10" class="text-center">No TikTok orders found</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- COB Tab -->
                                <div class="tab-pane fade" id="cob-ecom-content" role="tabpanel" aria-labelledby="cob-ecom-tab">
                                    <div class="table-responsive">
                                        <table id="cobPackingTable" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30px;"><input type="checkbox" class="ecom-select-all-checkbox" data-platform="cob" style="cursor: pointer;"></th>
                                                    <th>SO #</th>
                                                    <th>Transaction Type</th>
                                                    <th>Customer</th>
                                                    <th>SI Signed</th>
                                                    <th>Total Items</th>
                                                    <th>Packed Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($ecomByPlatform['cob'] as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                                    $totalItems = $order->items->count();
                                                    
                                                    if($packedCount === 0) {
                                                        $statusClass = 'status-ready';
                                                        $statusText = 'Ready for Packing';
                                                    } elseif($packedCount === $totalItems && $totalItems > 0) {
                                                        $statusClass = 'status-packed';
                                                        $statusText = 'Fully Packed';
                                                    } else {
                                                        $statusClass = 'status-partial';
                                                        $statusText = 'Partially Packed';
                                                    }
                                                    $isFullyPacked = ($packedCount === $totalItems && $totalItems > 0);
                                                @endphp
                                                <tr class="packing-row" data-order-id="{{ $order->id }}">
                                                    <td><input type="checkbox" class="ecom-order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;" {{ !$isFullyPacked ? 'disabled' : '' }}></td>
                                                    <td>
                                        <strong>{{ $order->so_number }}</strong>
                                        @php
                                            $isEcom = ($order->type === 'ecom_direct' || !empty($order->ecom_platform) || str_contains(strtolower($order->so_number ?? ''), 'ecom') || !empty($order->platform_order_id));
                                            $ecomId = $order->platform_order_id ?: ($order->ref_number ?: ($order->po_number ?? null));
                                        @endphp
                                        @if($isEcom || !empty($ecomId))
                                            <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                        @endif
                                        @if($order->cancellation_date)
                                            <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="fas fa-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($order->cancellation_date)->format('M d, Y') }}</span>
                                        @endif
                                    </td>
                                                    <td>
                                                        @php $ordType = $order->type ?: ($order->transaction_type ?? ''); @endphp
                                        <span class="badge bg-light text-dark border">{{ $txnTypeLabels[$ordType] ?? (ucwords(str_replace('_', ' ', $ordType)) ?: 'N/A') }}</span>
                                                    </td>
                                                    <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                                    @php
                                                        $pkgCurr = $order->currency ?? 'PHP';
                                                        $pkgSym = ($pkgCurr === 'USD' ? '$' : '₱');
                                                    @endphp
                                                    <td class="fw-bold">{{ $pkgSym }}{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                   
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                    title="View Details"
                                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-success shadow mark-packed-btn"
                                                                    onclick="markOrderAsPackedAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Mark as Packed"
                                                                    style="background: #28a745; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-check" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-danger shadow delete-ecom-order-btn"
                                                                    onclick="deleteEcomOrderAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Delete Order"
                                                                    style="border: 1px solid #dc3545; color: #dc3545; background: #fff; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-trash-alt" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="10" class="text-center">No COB orders found</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ready for Pickup Tab -->
                        <div class="tab-pane fade" id="ready-pickup-content" role="tabpanel" aria-labelledby="ready-pickup-tab">
                            <!-- Platform Sub-tabs -->
                            <ul class="nav nav-tabs mb-3" id="readyPickupPlatformTabs" role="tablist" style="border-bottom: 2px solid #dee2e6;">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="ready-all-tab" data-bs-toggle="tab" data-bs-target="#ready-all-content" type="button" role="tab" aria-controls="ready-all-content" aria-selected="true">
                                        <i class="fas fa-boxes me-2"></i>All Orders <span class="badge bg-secondary ms-2">{{ $readyForPickupOrders->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ready-shopee-tab" data-bs-toggle="tab" data-bs-target="#ready-shopee-content" type="button" role="tab" aria-controls="ready-shopee-content" aria-selected="false">
                                        <i class="las la-shopping-bag me-2" style="color: #ee4d2d;"></i>Shopee <span class="badge bg-danger ms-2">{{ $readyByPlatform['shopee']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ready-tiktok-tab" data-bs-toggle="tab" data-bs-target="#ready-tiktok-content" type="button" role="tab" aria-controls="ready-tiktok-content" aria-selected="false">
                                        <i class="las la-music me-2"></i>TikTok <span class="badge bg-dark ms-2">{{ $readyByPlatform['tiktok']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ready-lazada-tab" data-bs-toggle="tab" data-bs-target="#ready-lazada-content" type="button" role="tab" aria-controls="ready-lazada-content" aria-selected="false">
                                        <i class="las la-shopping-bag me-2" style="color: #0f146d;"></i>Lazada <span class="badge bg-primary ms-2">{{ $readyByPlatform['lazada']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ready-cob-tab" data-bs-toggle="tab" data-bs-target="#ready-cob-content" type="button" role="tab" aria-controls="ready-cob-content" aria-selected="false">
                                        <i class="las la-building me-2" style="color: #6f42c1;"></i>COB <span class="badge ms-2" style="background-color: #6f42c1; color: #fff;">{{ $readyByPlatform['cob']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ready-completed-tab" data-bs-toggle="tab" data-bs-target="#ready-completed-content" type="button" role="tab" aria-controls="ready-completed-content" aria-selected="false">
                                        <i class="fas fa-check-double me-2" style="color: #17a2b8;"></i>Completed Drop-off <span class="badge ms-2" style="background-color: #17a2b8; color: #fff;">{{ count($completedDropoffOrders) }}</span>
                                    </button>
                                </li>
                            </ul>

                            <!-- Platform Sub-tabs Content -->
                            <div class="tab-content" id="readyPickupPlatformTabContent">
                                <!-- All Orders Tab -->
                                <div class="tab-pane fade show active" id="ready-all-content" role="tabpanel" aria-labelledby="ready-all-tab">
                                    <div class="table-responsive">
                                        <table id="readyForPickupTableAll" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30px;"><input type="checkbox" class="ready-select-all-table-checkbox" style="cursor: pointer;"></th>
                                                    <th>SO #</th>
                                                    <th>Transaction Type</th>
                                                    <th>Customer</th>
                                                    <th>SI Signed</th>
                                                    <th>Total Items</th>
                                                    <th>Packed Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($readyForPickupOrders as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                                    $totalItems = $order->items->count();
                                                    $statusClass = 'status-packed';
                                                    $statusText = 'Ready for Pickup/Drop-off';
                                                @endphp
                                                <tr>
                                                    <td><input type="checkbox" class="ready-order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;"></td>
                                                    <td>
                                        <strong>{{ $order->so_number }}</strong>
                                        @php
                                            $isEcom = ($order->type === 'ecom_direct' || !empty($order->ecom_platform) || str_contains(strtolower($order->so_number ?? ''), 'ecom') || !empty($order->platform_order_id));
                                            $ecomId = $order->platform_order_id ?: ($order->ref_number ?: ($order->po_number ?? null));
                                        @endphp
                                        @if($isEcom || !empty($ecomId))
                                            <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                        @endif
                                        @if($order->cancellation_date)
                                            <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="fas fa-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($order->cancellation_date)->format('M d, Y') }}</span>
                                        @endif
                                    </td>
                                                    <td>
                                                        @php $ordType = $order->type ?: ($order->transaction_type ?? ''); @endphp
                                        <span class="badge bg-light text-dark border">{{ $txnTypeLabels[$ordType] ?? (ucwords(str_replace('_', ' ', $ordType)) ?: 'N/A') }}</span>
                                                    </td>
                                                    <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                                    @php
                                                        $pkgCurr = $order->currency ?? 'PHP';
                                                        $pkgSym = ($pkgCurr === 'USD' ? '$' : '₱');
                                                    @endphp
                                                    <td class="fw-bold">{{ $pkgSym }}{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td><span class="status-badge {{ $statusClass }}" style="background-color: #d4edda; color: #155724;">{{ $statusText }}</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                    
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                    title="View Details"
                                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-success shadow mark-gathered-btn"
                                                                    onclick="markOrderAsGatheredAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Mark as Gathered (Ready for Delivery)"
                                                                    style="background: #007bff; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-box-open" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="10" class="text-center" style="padding: 2rem;">
                                                        <p style="color: #999;">No orders ready for pickup yet</p>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Shopee Tab -->
                                <div class="tab-pane fade" id="ready-shopee-content" role="tabpanel" aria-labelledby="ready-shopee-tab">
                                    <div class="table-responsive">
                                        <table id="readyForPickupTableShopee" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30px;"><input type="checkbox" class="ready-select-all-table-checkbox" style="cursor: pointer;"></th>
                                                    <th>SO #</th>
                                                    <th>Transaction Type</th>
                                                    <th>Customer</th>
                                                    <th>SI Signed</th>
                                                    <th>Total Items</th>
                                                    <th>Packed Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($readyByPlatform['shopee'] as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                                    $totalItems = $order->items->count();
                                                    $statusClass = 'status-packed';
                                                    $statusText = 'Ready for Pickup/Drop-off';
                                                @endphp
                                                <tr>
                                                    <td><input type="checkbox" class="ready-order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;"></td>
                                                    <td>
                                        <strong>{{ $order->so_number }}</strong>
                                        @php
                                            $isEcom = ($order->type === 'ecom_direct' || !empty($order->ecom_platform) || str_contains(strtolower($order->so_number ?? ''), 'ecom') || !empty($order->platform_order_id));
                                            $ecomId = $order->platform_order_id ?: ($order->ref_number ?: ($order->po_number ?? null));
                                        @endphp
                                        @if($isEcom || !empty($ecomId))
                                            <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                        @endif
                                        @if($order->cancellation_date)
                                            <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="fas fa-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($order->cancellation_date)->format('M d, Y') }}</span>
                                        @endif
                                    </td>
                                                    <td>
                                                        @php $ordType = $order->type ?: ($order->transaction_type ?? ''); @endphp
                                        <span class="badge bg-light text-dark border">{{ $txnTypeLabels[$ordType] ?? (ucwords(str_replace('_', ' ', $ordType)) ?: 'N/A') }}</span>
                                                    </td>
                                                    <td>{{ $order->customer->customer_name ?? 'Shopee Customer' }}</td>
                                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                                    @php
                                                        $pkgCurr = $order->currency ?? 'PHP';
                                                        $pkgSym = ($pkgCurr === 'USD' ? '$' : '₱');
                                                    @endphp
                                                    <td class="fw-bold">{{ $pkgSym }}{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td><span class="status-badge {{ $statusClass }}" style="background-color: #d4edda; color: #155724;">{{ $statusText }}</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                    
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                    title="View Details"
                                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-success shadow mark-gathered-btn"
                                                                    onclick="markOrderAsGatheredAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Mark as Gathered (Ready for Delivery)"
                                                                    style="background: #007bff; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-box-open" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="10" class="text-center" style="padding: 2rem;">
                                                        <p style="color: #999;">No Shopee orders ready for pickup</p>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- TikTok Tab -->
                                <div class="tab-pane fade" id="ready-tiktok-content" role="tabpanel" aria-labelledby="ready-tiktok-tab">
                                    <div class="table-responsive">
                                        <table id="readyForPickupTableTiktok" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30px;"><input type="checkbox" class="ready-select-all-table-checkbox" style="cursor: pointer;"></th>
                                                    <th>SO #</th>
                                                    <th>Transaction Type</th>
                                                    <th>Customer</th>
                                                    <th>SI Signed</th>
                                                    <th>Total Items</th>
                                                    <th>Packed Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($readyByPlatform['tiktok'] as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                                    $totalItems = $order->items->count();
                                                    $statusClass = 'status-packed';
                                                    $statusText = 'Ready for Pickup/Drop-off';
                                                @endphp
                                                <tr>
                                                    <td><input type="checkbox" class="ready-order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;"></td>
                                                    <td>
                                        <strong>{{ $order->so_number }}</strong>
                                        @php
                                            $isEcom = ($order->type === 'ecom_direct' || !empty($order->ecom_platform) || str_contains(strtolower($order->so_number ?? ''), 'ecom') || !empty($order->platform_order_id));
                                            $ecomId = $order->platform_order_id ?: ($order->ref_number ?: ($order->po_number ?? null));
                                        @endphp
                                        @if($isEcom || !empty($ecomId))
                                            <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                        @endif
                                        @if($order->cancellation_date)
                                            <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="fas fa-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($order->cancellation_date)->format('M d, Y') }}</span>
                                        @endif
                                    </td>
                                                    <td>
                                                        @php $ordType = $order->type ?: ($order->transaction_type ?? ''); @endphp
                                        <span class="badge bg-light text-dark border">{{ $txnTypeLabels[$ordType] ?? (ucwords(str_replace('_', ' ', $ordType)) ?: 'N/A') }}</span>
                                                    </td>
                                                    <td>{{ $order->customer->customer_name ?? 'TikTok Customer' }}</td>
                                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                                    @php
                                                        $pkgCurr = $order->currency ?? 'PHP';
                                                        $pkgSym = ($pkgCurr === 'USD' ? '$' : '₱');
                                                    @endphp
                                                    <td class="fw-bold">{{ $pkgSym }}{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td><span class="status-badge {{ $statusClass }}" style="background-color: #d4edda; color: #155724;">{{ $statusText }}</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                    
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                    title="View Details"
                                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-success shadow mark-gathered-btn"
                                                                    onclick="markOrderAsGatheredAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Mark as Gathered (Ready for Delivery)"
                                                                    style="background: #007bff; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-box-open" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="10" class="text-center" style="padding: 2rem;">
                                                        <p style="color: #999;">No TikTok orders ready for pickup</p>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Lazada Tab -->
                                <div class="tab-pane fade" id="ready-lazada-content" role="tabpanel" aria-labelledby="ready-lazada-tab">
                                    <div class="table-responsive">
                                        <table id="readyForPickupTableLazada" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30px;"><input type="checkbox" class="ready-select-all-table-checkbox" style="cursor: pointer;"></th>
                                                    <th>SO #</th>
                                                    <th>Transaction Type</th>
                                                    <th>Customer</th>
                                                    <th>SI Signed</th>
                                                    <th>Total Items</th>
                                                    <th>Packed Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($readyByPlatform['lazada'] as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                                    $totalItems = $order->items->count();
                                                    $statusClass = 'status-packed';
                                                    $statusText = 'Ready for Pickup/Drop-off';
                                                @endphp
                                                <tr>
                                                    <td><input type="checkbox" class="ready-order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;"></td>
                                                    <td>
                                        <strong>{{ $order->so_number }}</strong>
                                        @php
                                            $isEcom = ($order->type === 'ecom_direct' || !empty($order->ecom_platform) || str_contains(strtolower($order->so_number ?? ''), 'ecom') || !empty($order->platform_order_id));
                                            $ecomId = $order->platform_order_id ?: ($order->ref_number ?: ($order->po_number ?? null));
                                        @endphp
                                        @if($isEcom || !empty($ecomId))
                                            <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                        @endif
                                        @if($order->cancellation_date)
                                            <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="fas fa-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($order->cancellation_date)->format('M d, Y') }}</span>
                                        @endif
                                    </td>
                                                    <td>
                                                        @php $ordType = $order->type ?: ($order->transaction_type ?? ''); @endphp
                                        <span class="badge bg-light text-dark border">{{ $txnTypeLabels[$ordType] ?? (ucwords(str_replace('_', ' ', $ordType)) ?: 'N/A') }}</span>
                                                    </td>
                                                    <td>{{ $order->customer->customer_name ?? 'Lazada Customer' }}</td>
                                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                                    @php
                                                        $pkgCurr = $order->currency ?? 'PHP';
                                                        $pkgSym = ($pkgCurr === 'USD' ? '$' : '₱');
                                                    @endphp
                                                    <td class="fw-bold">{{ $pkgSym }}{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                    
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                    title="View Details"
                                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-primary shadow mark-gathered-btn"
                                                                    onclick="markOrderAsGatheredAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Mark as Gathered (Ready for Delivery)"
                                                                    style="background: #007bff; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-box-open" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="10" class="text-center" style="padding: 2rem;">
                                                        <p style="color: #999;">No Lazada orders ready for pickup</p>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- COB Tab -->
                                <div class="tab-pane fade" id="ready-cob-content" role="tabpanel" aria-labelledby="ready-cob-tab">
                                    <div class="table-responsive">
                                        <table id="readyForPickupTableCob" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30px;"><input type="checkbox" class="ready-select-all-table-checkbox" style="cursor: pointer;"></th>
                                                    <th>SO #</th>
                                                    <th>Transaction Type</th>
                                                    <th>Customer</th>
                                                    <th>SI Signed</th>
                                                    <th>Total Items</th>
                                                    <th>Packed Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($readyByPlatform['cob'] as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                                    $totalItems = $order->items->count();
                                                    $statusClass = 'status-packed';
                                                    $statusText = 'Ready for Pickup/Drop-off';
                                                @endphp
                                                <tr>
                                                    <td><input type="checkbox" class="ready-order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;"></td>
                                                    <td>
                                        <strong>{{ $order->so_number }}</strong>
                                        @php
                                            $isEcom = ($order->type === 'ecom_direct' || !empty($order->ecom_platform) || str_contains(strtolower($order->so_number ?? ''), 'ecom') || !empty($order->platform_order_id));
                                            $ecomId = $order->platform_order_id ?: ($order->ref_number ?: ($order->po_number ?? null));
                                        @endphp
                                        @if($isEcom || !empty($ecomId))
                                            <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                        @endif
                                        @if($order->cancellation_date)
                                            <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="fas fa-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($order->cancellation_date)->format('M d, Y') }}</span>
                                        @endif
                                    </td>
                                                    <td>
                                                        @php $ordType = $order->type ?: ($order->transaction_type ?? ''); @endphp
                                        <span class="badge bg-light text-dark border">{{ $txnTypeLabels[$ordType] ?? (ucwords(str_replace('_', ' ', $ordType)) ?: 'N/A') }}</span>
                                                    </td>
                                                    <td>{{ $order->customer->customer_name ?? 'COB Customer' }}</td>
                                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                                    @php
                                                        $pkgCurr = $order->currency ?? 'PHP';
                                                        $pkgSym = ($pkgCurr === 'USD' ? '$' : '₱');
                                                    @endphp
                                                    <td class="fw-bold">{{ $pkgSym }}{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                    
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                    title="View Details"
                                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-primary shadow mark-gathered-btn"
                                                                    onclick="markOrderAsGatheredAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Mark as Gathered (Ready for Delivery)"
                                                                    style="background: #007bff; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-box-open" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="10" class="text-center" style="padding: 2rem;">
                                                        <p style="color: #999;">No COB orders ready for pickup</p>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Completed Drop-off Sub-tab Content -->
                                <div class="tab-pane fade" id="ready-completed-content" role="tabpanel" aria-labelledby="ready-completed-tab">
                                    <div class="table-responsive">
                                        <table id="completedDropoffAllTable" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>SO #</th>
                                                    <th>Transaction Type</th>
                                                    <th>Platform</th>
                                                    <th>Customer</th>
                                                    <th>Total Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Gathered By</th>
                                                    <th>Gathered At</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($completedDropoffOrders as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $totalItems = $order->items->count();
                                                    $gatheredBy = $packingData['gathered_by'] ?? 'N/A';
                                                    $gatheredAt = isset($packingData['gathered_at']) ? \Carbon\Carbon::parse($packingData['gathered_at'])->format('M d, Y h:i A') : 'N/A';
                                                    $platformLabel = ucfirst($order->ecom_platform ?? 'N/A');
                                                    $platformClass = 'platform-' . strtolower($order->ecom_platform ?? 'default');
                                                @endphp
                                                <tr>
                                                    <td>
                                        <strong>{{ $order->so_number }}</strong>
                                        @php
                                            $isEcom = ($order->type === 'ecom_direct' || !empty($order->ecom_platform) || str_contains(strtolower($order->so_number ?? ''), 'ecom') || !empty($order->platform_order_id));
                                            $ecomId = $order->platform_order_id ?: ($order->ref_number ?: ($order->po_number ?? null));
                                        @endphp
                                        @if($isEcom || !empty($ecomId))
                                            <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                        @endif
                                        @if($order->cancellation_date)
                                            <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="fas fa-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($order->cancellation_date)->format('M d, Y') }}</span>
                                        @endif
                                    </td>
                                                    <td>
                                                        @php $ordType = $order->type ?: ($order->transaction_type ?? ''); @endphp
                                        <span class="badge bg-light text-dark border">{{ $txnTypeLabels[$ordType] ?? (ucwords(str_replace('_', ' ', $ordType)) ?: 'N/A') }}</span>
                                                    </td>
                                                    <td><span class="platform-badge {{ $platformClass }}">{{ $platformLabel }}</span></td>
                                                    <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    @php
                                                        $pkgCurr = $order->currency ?? 'PHP';
                                                        $pkgSym = ($pkgCurr === 'USD' ? '$' : '₱');
                                                    @endphp
                                                    <td class="fw-bold">{{ $pkgSym }}{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td>{{ $gatheredBy }}</td>
                                                    <td>{{ $gatheredAt }}</td>
                                                    <td><span class="badge" style="background-color: #17a2b8; color: #fff;">Completed</span></td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                
                                                                data-order-id="{{ $order->id }}"
                                                                data-so-number="{{ $order->so_number }}"
                                                                data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                title="View Details"
                                                                style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                            <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="10" class="text-center" style="padding: 2rem;">
                                                        <p style="color: #999;">No completed drop-off orders</p>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Complimentary Tab -->
                        <div class="tab-pane fade" id="complimentary-content" role="tabpanel" aria-labelledby="complimentary-tab">
                            <div class="table-responsive">
                                <table id="complimentaryPackingTable" class="display" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>SO Number</th>
                                            <th>Transaction Type</th>
                                            <th>Recipient / Customer</th>
                                            <th>Date</th>
                                            <th>Total Qty</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($complimentaryPackingOrders as $order)
                                        @php
                                            $totalQty = $order->items->sum('quantity');
                                            $pData = json_decode($order->packing_data ?? '{}', true) ?: [];
                                            $pStatus = $pData['status'] ?? 'ready';
                                        @endphp
                                        <tr>
                                            <td>
                                        <strong>{{ $order->so_number }}</strong>
                                        @php $ecomId = $order->platform_order_id ?: ($order->ref_number ?? null); @endphp
                                        @if(!empty($ecomId))
                                            <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>{{ $ecomId }}</span>
                                        @endif
                                        @if($order->cancellation_date)
                                            <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="fas fa-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($order->cancellation_date)->format('M d, Y') }}</span>
                                        @endif
                                    </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ $order->transaction_type ?? 'N/A' }}</span>
                                            </td>
                                            <td>{{ $order->customer->customer_name ?? 'Recipient' }}</td>
                                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                            <td>{{ $totalQty }} pcs</td>
                                            <td>
                                                <span class="badge" style="background-color: #6f42c1; color: #fff;">Complimentary (Ready to Pack)</span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-danger shadow view-order-btn"
                                                            data-order-id="{{ $order->id }}"
                                                            data-so-number="{{ $order->so_number }}"
                                                            data-customer="{{ $order->customer->customer_name ?? 'Recipient' }}"
                                                            data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                            data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                            title="View Details"
                                                            style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; color: #fff;">
                                                        <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-success shadow mark-packed-btn"
                                                            onclick="markOrderAsPackedAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                            title="Mark as Packed (Send to Delivery Scheduling)"
                                                            style="background: #28a745; border: none; padding: 0.4rem 0.8rem; height: 36px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 500;">
                                                        <i class="fas fa-check-circle me-1" style="font-size: 0.9rem;"></i> Mark as Packed
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center" style="padding: 2rem;">
                                                <p style="color: #999;">No complimentary orders to pack yet</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Team Stock Transfers Packing Tab -->
                        <div class="tab-pane fade" id="team-stocks-packing-content" role="tabpanel" aria-labelledby="team-stocks-packing-tab">
                            <div class="table-responsive">
                                <table id="teamStockPackingTable" class="display table table-bordered table-hover align-middle mb-0" style="width: 100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Transfer #</th>
                                            <th>Target Team</th>
                                            <th>Transferred By</th>
                                            <th class="text-center">Items</th>
                                            <th class="text-center">Total Pcs</th>
                                            <th class="text-end">Total Amount</th>
                                            <th>Date Created</th>
                                            <th>Remarks</th>
                                            <th>Status</th>
                                            <th class="text-end" style="min-width: 150px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($teamStockPackingTransfers ?? [] as $tt)
                                        @php
                                           $getItemQty = function($item) {
    // Total Pcs in Packing must be based on the actual quantity
    // that came from Picking. Explicit 0 must remain 0.
    if ($item->picked_qty !== null) {
        return (float) $item->picked_qty;
    }

    return 0;
};

$totalPcs = $tt->items->sum(function($item) use ($getItemQty) {
    return $getItemQty($item);
});
                                             $totalTransferAmount = $tt->items->sum(function($item) use ($getItemQty) {
                                                 $price = 0;
                                                 if ($item->bookIndex) {
                                                     $price = (float)($item->bookIndex->price ?: ($item->bookIndex->book?->price ?: 0));
                                                 } elseif ($item->book) {
                                                     $price = (float)($item->book->price ?: 0);
                                                 } elseif ($item->bookBundle) {
                                                     $price = (float)($item->bookBundle->price ?: 0);
                                                 }
                                                 return $price * $getItemQty($item);
                                             });
                                         @endphp
                                        <tr>
                                            <td class="fw-bold">{{ $tt->transfer_number }}</td>
                                            <td><span class="badge bg-danger">{{ $tt->team_name }}</span></td>
                                            <td>{{ $tt->transferredByUser->name ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $tt->items->count() }} item(s)</td>
                                            <td class="text-center fw-bold {{ $totalPcs > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($totalPcs) }} pcs
                                            </td>
                                            <td class="text-end fw-bold text-dark">₱{{ number_format($totalTransferAmount, 2) }}</td>
                                            <td>{{ $tt->created_at->format('M d, Y h:i A') }}</td>
                                            <td style="max-width: 110px;" class="text-truncate" title="{{ $tt->notes }}"><span class="text-dark">{{ $tt->notes ?: '' }}</span></td>
                                            <td>
                                                @if($tt->status === 'completed')
                                                    <span class="badge bg-success text-white"><i class="fas fa-check me-1"></i>Completed</span>
                                                @else
                                                    <span id="team_stock_transfer_status_badge_{{ $tt->id }}" class="badge bg-info text-white">Ready for Packing</span>
                                                @endif
                                            </td>
                                            <td class="text-end" style="white-space: nowrap;">
                                                <div class="d-flex align-items-center justify-content-end gap-1">
                                                    <button type="button" class="btn btn-xs btn-outline-danger fw-bold" data-bs-toggle="modal" data-bs-target="#teamStockPackModal{{ $tt->id }}">
                                                        <i class="fas fa-barcode me-1"></i>View & Pack Items
                                                    </button>
                                                    @if($tt->status === 'completed')
                                                        <span class="badge bg-success text-white"><i class="fas fa-check me-1"></i>Completed</span>
                                                    @else
                                                        <form action="{{ route('production.logistic.team-stock-transfer.complete-pack', $tt->id) }}" method="POST" class="d-inline m-0">
                                                            @csrf
                                                            <button type="submit" id="team_stock_complete_btn_main_{{ $tt->id }}" class="btn btn-xs btn-success fw-bold" style="background-color: #28a745; border: none;">
                                                                <i class="fas fa-check me-1"></i>Mark Packed
                                                            </button>
                                                        </form>
                                                        @if(auth()->check() && auth()->user()->isSuperAdmin())
                                                         <button type="button" class="btn btn-xs btn-warning text-white fw-bold" style="background-color: #f59e0b; border: none;" data-bs-toggle="modal" data-bs-target="#editTeamStockModal{{ $tt->id }}" title="Edit Transfer">
                                                             <i class="fas fa-edit me-1"></i>Edit
                                                         </button>
                                                         <form action="{{ route('production.logistic.team-stock-transfer.delete', $tt->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Are you sure you want to delete Team Stock Transfer {{ $tt->transfer_number }}?');">
                                                             @csrf
                                                             @method('DELETE')
                                                             <button type="submit" class="btn btn-xs btn-danger fw-bold" style="background-color: #dc3545; border: none;" title="Delete Transfer">
                                                                 <i class="fas fa-trash me-1"></i>Delete
                                                             </button>
                                                         </form>
                                                     @endif
                                                @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4 text-muted">No team stock transfers found.</td>
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

    @foreach($teamStockPackingTransfers ?? [] as $tt)
    @php
        $totalTransferAmount = $tt->items->sum(function($item) {
            $price = 0;
            if ($item->bookIndex) {
                $price = (float)($item->bookIndex->price ?: ($item->bookIndex->book?->price ?: 0));
            } elseif ($item->book) {
                $price = (float)($item->book->price ?: 0);
            } elseif ($item->bookBundle) {
                $price = (float)($item->bookBundle->price ?: 0);
            }
            $qty = (float)($item->packed_qty !== null ? $item->packed_qty : ($item->picked_qty !== null ? $item->picked_qty : $item->quantity));
            return $price * $qty;
        });
    @endphp
    <div class="modal fade team-stock-modal" id="teamStockPackModal{{ $tt->id }}" tabindex="-1" aria-hidden="true" data-transfer-id="{{ $tt->id }}">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header px-4 py-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fw-bold text-dark mb-0">Packing Details - {{ $tt->transfer_number }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="max-height: 80vh; overflow-y: auto;">
                    <form id="ts_pack_form_{{ $tt->id }}" action="{{ route('production.logistic.team-stock-transfer.save-pack-items', $tt->id) }}" method="POST">
                        @csrf
                    
                    <!-- Top Information Grid (2 Columns) -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border">
                                <h6 class="fw-bold text-dark mb-3">Order Information</h6>
                                <div class="mb-2">
                                    <label class="fw-semibold small text-muted d-block mb-1">Transfer Number:</label>
                                    <input type="text" class="form-control form-control-sm fw-bold bg-white" value="{{ $tt->transfer_number }}" readonly>
                                </div>
                                <div class="mb-2">
                                    <label class="fw-semibold small text-muted d-block mb-1">Date Created:</label>
                                    <input type="text" class="form-control form-control-sm bg-white" value="{{ $tt->created_at->format('M d, Y h:i A') }}" readonly>
                                </div>
                                <div class="mb-2">
                                    <label class="fw-semibold small text-muted d-block mb-1">Target Sales Team:</label>
                                    <input type="text" class="form-control form-control-sm bg-white fw-bold text-danger" value="{{ $tt->team_name }}" readonly>
                                </div>
                                <div class="mb-2">
                                    <label class="fw-semibold small text-muted d-block mb-1">Requested By:</label>
                                    <input type="text" class="form-control form-control-sm bg-white" value="{{ $tt->transferredByUser->name ?? 'N/A' }}" readonly>
                                </div>
                                <div class="mb-2">
                                    <label class="fw-semibold small text-muted d-block mb-1">Remarks / Special Instructions:</label>
                                    <textarea name="notes" id="ts_remarks_{{ $tt->id }}" class="form-control form-control-sm bg-white fw-semibold mb-1" rows="2" placeholder="Enter remarks or special instructions...">{{ $tt->notes }}</textarea>
                                    <button type="submit" class="btn btn-sm btn-primary fw-bold"><i class="fas fa-save me-1"></i>Save Remarks</button>
                                </div>
                                <div class="mb-2">
                                    <label class="fw-semibold small text-muted d-block mb-1">Packing Status:</label>
                                    <select name="status" id="ts_packingStatus_{{ $tt->id }}" class="form-select form-select-sm fw-bold">
                                         <option value="not_started" {{ $tt->status === 'not_started' ? 'selected' : '' }}>Not Started</option>
                                         <option value="in_progress" {{ in_array($tt->status, ['packing', 'in_progress']) ? 'selected' : '' }}>In Progress</option>
                                         <option value="completed" {{ $tt->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                     </select>
                                </div>
                                <div class="mb-0">
                                    <label class="fw-semibold small text-muted d-block mb-1">Number of Boxes:</label>
                                    <input type="number" id="ts_boxes_{{ $tt->id }}" placeholder="Enter number of boxes" min="0" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold text-dark mb-3">Barcode Scanning & Quick Action</h6>
                                    <label class="form-label fw-bold text-dark mb-1"><i class="fas fa-barcode text-danger me-1"></i>Scan Book Barcode / ISBN:</label>
                                    <div class="input-group input-group-sm mb-3">
                                        <span class="input-group-text bg-danger text-white"><i class="fas fa-search"></i></span>
                                        <input type="text" id="ts_barcode_input_{{ $tt->id }}" class="form-control form-control-sm fw-bold ts-barcode-input-field" placeholder="Scan or type ISBN/barcode and press Enter..." data-transfer-id="{{ $tt->id }}">
                                        <button type="button" class="btn btn-danger btn-sm fw-bold" onclick="onTSManualScanClick({{ $tt->id }})">Scan</button>
                                    </div>
                                    <div id="ts_scan_feedback_{{ $tt->id }}" class="p-2 rounded text-center small fw-bold bg-success text-white border mb-3">
                                        Ready to scan
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-success btn-sm w-100 fw-bold py-2" onclick="markAllTeamStockItemsPacked({{ $tt->id }})">
                                        <i class="fas fa-check-double me-1"></i>Pack All Items Quickly
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items to Pack Section -->
                    <h6 class="fw-bold text-dark mb-2">Items to Pack</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle mb-0" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>PRODUCT</th>
                                    <th style="width: 110px;" class="text-center">QTY TO PACK</th>
                                    <th style="width: 110px;" class="text-end">UNIT PRICE</th>
                                    <th style="width: 110px;" class="text-end">SUBTOTAL</th>
                                    <th style="width: 100px;" class="text-center">PACKED QTY</th>
                                    <th style="width: 120px;" class="text-center">STATUS</th>
                                    <th style="width: 150px;">NOTES</th>
                                    <th style="width: 130px;" class="text-center">PACKED DATE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tt->items as $idx => $tItem)
                                @php
                                    $itemName = $tItem->bookIndex ? $tItem->bookIndex->display_name : ($tItem->book ? $tItem->book->name : ($tItem->bookBundle ? $tItem->bookBundle->name : 'N/A'));
                                    $unitPrice = 0;
                                    $barcodes = [];
                                    if ($tItem->bookIndex) {
                                        if (!empty($tItem->bookIndex->barcode)) $barcodes[] = (string)$tItem->bookIndex->barcode;
                                        if (!empty($tItem->bookIndex->nbs_barcode)) $barcodes[] = (string)$tItem->bookIndex->nbs_barcode;
                                        if (!empty($tItem->bookIndex->article)) $barcodes[] = (string)$tItem->bookIndex->article;
                                        if ($tItem->bookIndex->book) {
                                            if (!empty($tItem->bookIndex->book->barcode)) $barcodes[] = (string)$tItem->bookIndex->book->barcode;
                                            if (!empty($tItem->bookIndex->book->nbs_barcode)) $barcodes[] = (string)$tItem->bookIndex->book->nbs_barcode;
                                            if (!empty($tItem->bookIndex->book->sku)) $barcodes[] = (string)$tItem->bookIndex->book->sku;
                                            if (!empty($tItem->bookIndex->book->item_code)) $barcodes[] = (string)$tItem->bookIndex->book->item_code;
                                        }
                                    }
                                    if ($tItem->book) {
                                        if (!empty($tItem->book->barcode)) $barcodes[] = (string)$tItem->book->barcode;
                                        if (!empty($tItem->book->nbs_barcode)) $barcodes[] = (string)$tItem->book->nbs_barcode;
                                        if (!empty($tItem->book->sku)) $barcodes[] = (string)$tItem->book->sku;
                                        if (!empty($tItem->book->item_code)) $barcodes[] = (string)$tItem->book->item_code;
                                    }
                                    if ($tItem->bookBundle) {
                                        if (!empty($tItem->bookBundle->sku)) $barcodes[] = (string)$tItem->bookBundle->sku;
                                        if (!empty($tItem->bookBundle->name)) $barcodes[] = (string)$tItem->bookBundle->name;
                                    }
                                    if (!empty($tItem->barcode)) $barcodes[] = (string)$tItem->barcode;
                                    if (!empty($tItem->nbs_barcode)) $barcodes[] = (string)$tItem->nbs_barcode;
                                    if (!empty($tItem->sku)) $barcodes[] = (string)$tItem->sku;
                                    if (!empty($tItem->item_code)) $barcodes[] = (string)$tItem->item_code;
                                    if (!empty($tItem->isbn)) $barcodes[] = (string)$tItem->isbn;

                                    if ($tItem->bookIndex) {
                                        $unitPrice = (float)($tItem->bookIndex->price ?: ($tItem->bookIndex->book?->price ?: 0));
                                    } elseif ($tItem->book) {
                                        $unitPrice = (float)($tItem->book->price ?: 0);
                                    } elseif ($tItem->bookBundle) {
                                        $unitPrice = (float)($tItem->bookBundle->price ?: 0);
                                    } else {
                                        $unitPrice = 0;
                                    }

                                    $uniqueBarcodes = array_values(array_unique(array_filter($barcodes)));
                                    $barcodesJson = json_encode($uniqueBarcodes);
                                    $tItemType = $tItem->item_type ?? ($tItem->bookIndex ? 'Index' : ($tItem->bookBundle ? 'Bundle' : 'Book'));
                                    $tSym = (($tt->currency ?? ($order->currency ?? 'PHP')) === 'USD' ? '$' : '₱');

                              
                            
                              		$displayQty = (float)($tItem->picked_qty ?? 0);
                                   
                                                                $effectiveQty = $tItem->packed_qty !== null
    ? (float) $tItem->packed_qty
    : 0;
                                    $itemSubtotal = $unitPrice * ($effectiveQty > 0 ? $effectiveQty : $displayQty);
                                    $isItemPacked = ($tItem->status === 'Packed' || ($tItem->packed_qty !== null && (float)$tItem->packed_qty > 0 && $displayQty > 0 && $tItem->packed_qty >= $displayQty));
                              

                              
                              
                                @endphp
                                <tr id="ts_row_{{ $tt->id }}_{{ $idx }}" class="ts-item-row" data-transfer-id="{{ $tt->id }}" data-index="{{ $idx }}" data-barcodes="{{ $barcodesJson }}" data-title="{{ e($itemName) }}" style="background: {{ $isItemPacked ? '#d4edda' : ($tItem->status === 'In Progress' ? '#fff3cd' : '#f8d7da') }};">
                                    <td>{{ $idx + 1 }}</td>
                                    <td class="fw-bold text-dark">
                                        <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $tItem->id }}">
                                        <div>
                                            {{ $itemName }}
                                            @if($tItemType === 'Bundle')
                                                <span class="badge ms-1" style="background:#6f42c1; color:#fff;">Bundle</span>
                                            @elseif($tItemType === 'Index')
                                                <span class="badge bg-info text-dark ms-1">Index</span>
                                            @else
                                                <span class="badge bg-primary ms-1">Book</span>
                                            @endif
                                        </div>
                                        @if(!empty($uniqueBarcodes))
                                            <small class="text-muted d-block"><i class="fas fa-barcode me-1"></i>{{ implode(', ', $uniqueBarcodes) }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold {{ $displayQty <= 0 ? 'text-danger' : 'text-primary' }}">
                                        <span id="ts_qty_to_pack_{{ $tt->id }}_{{ $idx }}">{{ number_format($displayQty, 2) }}</span>
                                    </td>
                                    <td class="text-end">{{ $tSym }}{{ number_format($unitPrice, 2) }}</td>
                                    <td class="text-end fw-bold">
                                        <span id="ts_subtotal_{{ $tt->id }}_{{ $idx }}">{{ $tSym }}{{ number_format($itemSubtotal, 2) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" name="items[{{ $idx }}][packed_qty]" id="ts_packed_qty_{{ $tt->id }}_{{ $idx }}" min="0" max="{{ $displayQty }}" value="{{ $effectiveQty }}" placeholder="0" oninput="onTSPackedQtyInput({{ $tt->id }}, {{ $idx }}, {{ $unitPrice }})" onchange="updateTeamStockPackingProgress({{ $tt->id }})" style="width: 70px; padding: 2px 4px; text-align: center; border: 1px solid #ccc; border-radius: 4px; font-weight: 600;">
                                    </td>
                                    <td class="text-center">
                                        <select name="items[{{ $idx }}][status]" id="ts_packed_status_{{ $tt->id }}_{{ $idx }}" class="ts-status-select" onchange="onTSStatusSelectChange({{ $tt->id }}, {{ $idx }})" style="padding: 2px 4px; border: 1px solid #ccc; border-radius: 4px; font-weight: 600;">
                                            <option value="Not Packed" {{ ($tItem->status ?? 'Not Packed') === 'Not Packed' || $displayQty <= 0 ? 'selected' : '' }}>Not Packed</option>
                                            <option value="In Progress" {{ ($tItem->status ?? '') === 'In Progress' && $displayQty > 0 ? 'selected' : '' }}>In Progress</option>
                                            <option value="Packed" {{ ($tItem->status ?? '') === 'Packed' && $displayQty > 0 ? 'selected' : '' }}>Packed</option>
                                        </select>
                                        <span id="ts_status_badge_{{ $tt->id }}_{{ $idx }}" class="d-none {{ $isItemPacked ? 'bg-success' : 'bg-warning' }}"></span>
                                    </td>
                                    <td>
                                        <input type="text" name="items[{{ $idx }}][notes]" id="ts_notes_{{ $tt->id }}_{{ $idx }}" value="{{ $tItem->notes ?? '' }}" placeholder="Add notes..." style="width: 100%; padding: 2px 4px; border: 1px solid #ccc; border-radius: 4px; font-size: 0.82rem; {{ !empty($tItem->notes) ? 'font-weight: 600; color: #d9251c;' : '' }}">
                                    </td>
                                    <td class="text-center">
                                        <input type="date" name="items[{{ $idx }}][packed_date]" id="ts_date_{{ $tt->id }}_{{ $idx }}" value="{{ $tItem->packed_date ? \Carbon\Carbon::parse($tItem->packed_date)->format('Y-m-d') : date('Y-m-d') }}" style="padding: 2px 4px; border: 1px solid #ccc; border-radius: 4px; font-size: 0.82rem;">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Bottom Summary & Actions (2 Columns) -->
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border">
                                <h6 class="fw-bold text-dark mb-3">Packing Summary</h6>
                                <div class="mb-2">
                                    <label class="fw-semibold small text-muted d-block mb-1">Total Items:</label>
                                    <input type="text" class="form-control form-control-sm bg-white" value="{{ $tt->items->count() }}" readonly>
                                </div>
                                <div class="mb-2">
                                    <label class="fw-semibold small text-muted d-block mb-1">Items Packed:</label>
                                    <input type="text" id="ts_items_packed_{{ $tt->id }}" class="form-control form-control-sm bg-white fw-bold text-success" value="{{ $tt->items->filter(fn($i) => $i->status === 'Packed' || $i->packed_qty >= $i->quantity)->count() }}" readonly>
                                </div>
                                <div class="mb-0">
                                    <label class="fw-semibold small text-muted d-block mb-1">Packing Progress:</label>
                                    @php
                                        $packedCount = $tt->items->filter(fn($i) => $i->status === 'Packed' || $i->packed_qty >= $i->quantity)->count();
                                        $totalCount = $tt->items->count();
                                        $pct = $totalCount > 0 ? round(($packedCount / $totalCount) * 100) : 0;
                                    @endphp
                                    <div class="progress" style="height: 18px; border-radius: 9px; background: #e9ecef;">
                                        <div id="ts_progress_bar_{{ $tt->id }}" class="progress-bar {{ $pct == 100 ? 'bg-success' : 'bg-warning text-dark' }}" style="width: {{ $pct }}%; font-size: 11px; font-weight: bold;">
                                            {{ $pct }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border h-100 d-flex flex-column justify-content-between">
                                <h6 class="fw-bold text-dark mb-3">Actions</h6>
                                <div class="d-flex flex-column gap-2">
                                    <button type="submit" class="btn btn-warning w-100 fw-bold py-2 shadow-sm text-dark" style="background-color: #ffc107; border: none;">
                                        <i class="fas fa-save me-1"></i>Save Packing
                                    </button>

                                    @if($tt->status !== 'completed')
                                    <button type="submit" id="ts_modal_complete_btn_{{ $tt->id }}" formaction="{{ route('production.logistic.team-stock-transfer.complete-pack', $tt->id) }}" formmethod="POST" class="btn btn-primary w-100 fw-bold py-2 shadow-sm" style="background-color: #0d6efd; border: none;">
                                        <i class="fas fa-check-circle me-1"></i>Mark Packed & Complete
                                    </button>
                                    @endif

                                    <button type="button" class="btn btn-secondary w-100 fw-bold py-2" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-1"></i>Close Details
                                    </button>
                                </div>
                    </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @if(auth()->check() && auth()->user()->isSuperAdmin())
    <div class="modal fade team-stock-modal" id="editTeamStockModal{{ $tt->id }}" tabindex="-1" aria-hidden="true" data-transfer-id="{{ $tt->id }}">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header px-4 py-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fw-bold text-dark mb-0">Edit Team Stock Transfer - {{ $tt->transfer_number }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="max-height: 80vh; overflow-y: auto;">
                    <form action="{{ route('production.logistic.team-stock-transfer.update', $tt->id) }}" method="POST">
                        @csrf

                        <!-- Top Information Grid (2 Columns) -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded border">
                                    <h6 class="fw-bold text-dark mb-3">Order Information (Super Admin Edit)</h6>
                                    <div class="mb-2">
                                        <label class="fw-semibold small text-muted d-block mb-1">Transfer Number:</label>
                                        <input type="text" name="transfer_number" class="form-control form-control-sm fw-bold bg-white" value="{{ $tt->transfer_number }}" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="fw-semibold small text-muted d-block mb-1">Date Created:</label>
                                        <input type="text" class="form-control form-control-sm bg-white" value="{{ $tt->created_at->format('M d, Y h:i A') }}" readonly>
                                    </div>
                                    <div class="mb-2">
                                        <label class="fw-semibold small text-muted d-block mb-1">Target Sales Team:</label>
                                        <input type="text" name="team_name" list="teamOptionsList_{{ $tt->id }}" class="form-control form-control-sm bg-white fw-bold text-danger" value="{{ $tt->team_name }}" required>
                                        <datalist id="teamOptionsList_{{ $tt->id }}">
                                            <option value="Team A">
                                            <option value="Team B">
                                            <option value="Team C">
                                            <option value="Book Sales">
                                            <option value="MIBF">
                                        </datalist>
                                    </div>
                                    <div class="mb-2">
                                        <label class="fw-semibold small text-muted d-block mb-1">Transferred / Requested By:</label>
                                        <select name="transferred_by" class="form-select form-select-sm bg-white fw-semibold">
                                            @foreach($allUsers ?? [] as $u)
                                                <option value="{{ $u->id }}" {{ $tt->transferred_by == $u->id ? 'selected' : '' }}>
                                                    {{ $u->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="fw-semibold small text-muted d-block mb-1">Remarks / Special Instructions:</label>
                                        <textarea name="notes" class="form-control form-control-sm bg-white fw-semibold mb-1" rows="2" placeholder="Enter remarks or special instructions...">{{ $tt->notes }}</textarea>
                                    </div>
                                    <div class="mb-0">
                                        <label class="fw-semibold small text-muted d-block mb-1">Packing Status:</label>
                                        <select name="status" class="form-select form-select-sm fw-bold bg-white">
                                            <option value="not_started" {{ $tt->status === 'not_started' ? 'selected' : '' }}>Not Started</option>
                                            <option value="in_progress" {{ in_array($tt->status, ['packing', 'in_progress']) ? 'selected' : '' }}>In Progress</option>
                                            <option value="completed" {{ $tt->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded border h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-3">Add Item to Transfer (Optional)</h6>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-dark mb-1">Select Product / Item:</label>
                                            <select name="new_item_book_index_id" class="form-select form-select-sm bg-white">
                                                <option value="">-- Choose item to add --</option>
                                                {!! $cachedBookIndexOptions !!}
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-dark mb-1">Quantity to Add:</label>
                                            <input type="number" step="any" min="1" name="new_item_qty" class="form-control form-control-sm bg-white" placeholder="e.g. 5">
                                        </div>
                                    </div>
                                    <div class="p-2 bg-white rounded border small text-muted">
                                        <i class="fas fa-info-circle me-1 text-primary"></i> As Super Admin, you can edit header details, requested & packed quantities, or delete/add items.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items to Pack Section -->
                        <h6 class="fw-bold text-dark mb-2">Items to Pack (Super Admin Edit)</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle mb-0" style="font-size: 13px;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>PRODUCT</th>
                                      
                                        <th style="width: 110px;" class="text-center">PICKED QTY</th>
                                        <th style="width: 110px;" class="text-center">PACKED QTY</th>
                                        <th style="width: 130px;" class="text-center">STATUS</th>
                                        <th>NOTES</th>
                                        <th style="width: 70px;" class="text-center">REMOVE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tt->items as $idx => $tItem)
                                    @php
                                        $itemName = $tItem->bookIndex ? $tItem->bookIndex->display_name : ($tItem->book ? $tItem->book->name : ($tItem->bookBundle ? $tItem->bookBundle->name : 'N/A'));
                                        $tItemType = $tItem->item_type ?? ($tItem->bookIndex ? 'Index' : ($tItem->bookBundle ? 'Bundle' : 'Book'));
                                    @endphp
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="fw-bold text-dark">
                                            <div>
                                                {{ $itemName }}
                                                @if($tItemType === 'Bundle')
                                                    <span class="badge ms-1" style="background:#6f42c1; color:#fff;">Bundle</span>
                                                @elseif($tItemType === 'Index')
                                                    <span class="badge bg-info text-dark ms-1">Index</span>
                                                @else
                                                    <span class="badge bg-primary ms-1">Book</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                          <!--  <input type="number" step="any" min="0" name="items[{{ $tItem->id }}][quantity]" value="{{ $tItem->quantity }}" style="width: 70px; padding: 2px 4px; text-align: center; border: 1px solid #ccc; border-radius: 4px; font-weight: 600;" required>-->
                                       
                                   
<input type="hidden"
       name="items[{{ $tItem->id }}][quantity]"
       value="{{ $tItem->quantity }}">


<input type="number"
       step="any"
       min="0"
       name="items[{{ $tItem->id }}][picked_qty]"
       value="{{ $tItem->picked_qty !== null ? $tItem->picked_qty : 0 }}">
</td>
                                      
                                        
                                        <td class="text-center">
                                            <input type="number" step="any" min="0" name="items[{{ $tItem->id }}][packed_qty]" value="{{ $tItem->packed_qty }}" style="width: 70px; padding: 2px 4px; text-align: center; border: 1px solid #ccc; border-radius: 4px; font-weight: 600; color: #28a745;" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <select name="items[{{ $tItem->id }}][status]" style="padding: 2px 4px; border: 1px solid #ccc; border-radius: 4px; font-weight: 600;">
                                                <option value="Not Packed" {{ ($tItem->status ?? 'Not Packed') === 'Not Packed' ? 'selected' : '' }}>Not Packed</option>
                                                <option value="In Progress" {{ ($tItem->status ?? '') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="Packed" {{ ($tItem->status ?? '') === 'Packed' ? 'selected' : '' }}>Packed</option>
                                                <option value="Unpicked" {{ ($tItem->status ?? '') === 'Unpicked' ? 'selected' : '' }}>Unpicked</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="items[{{ $tItem->id }}][notes]" value="{{ $tItem->notes ?? '' }}" placeholder="Add notes..." style="width: 100%; padding: 2px 4px; border: 1px solid #ccc; border-radius: 4px; font-size: 0.82rem;">
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="items[{{ $tItem->id }}][_delete]" value="1" title="Check to remove item" style="cursor: pointer; width: 18px; height: 18px;">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Bottom Summary & Actions (2 Columns) -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded border">
                                    <h6 class="fw-bold text-dark mb-3">Packing Summary</h6>
                                    <div class="mb-2">
                                        <label class="fw-semibold small text-muted d-block mb-1">Total Items:</label>
                                        <input type="text" class="form-control form-control-sm bg-white" value="{{ $tt->items->count() }}" readonly>
                                    </div>
                                    <div class="mb-0">
                                        <label class="fw-semibold small text-muted d-block mb-1">Items Packed:</label>
                                        <input type="text" class="form-control form-control-sm bg-white fw-bold text-success" value="{{ $tt->items->filter(fn($i) => $i->status === 'Packed' || $i->packed_qty >= $i->quantity)->count() }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded border h-100 d-flex flex-column justify-content-between">
                                    <h6 class="fw-bold text-dark mb-3">Actions</h6>
                                    <div class="d-flex flex-column gap-2">
                                        <button type="submit" class="btn btn-warning w-100 fw-bold py-2 shadow-sm text-dark" style="background-color: #ffc107; border: none;">
                                            <i class="fas fa-save me-1"></i>Save Changes (Super Admin)
                                        </button>
                                        <button type="button" class="btn btn-secondary w-100 fw-bold py-2" data-bs-dismiss="modal">
                                            <i class="fas fa-times me-1"></i>Close Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach
    <!-- Floating Sticky Bulk Action Bar for Ready for Pickup -->
    <div id="readyBulkFloatingBar" class="ready-bulk-floating-bar hidden">
        <div class="d-flex align-items-center gap-2">
            <span class="badge rounded-pill px-3 py-2 fs-13" style="background:#007bff!important; color:#fff;">
                <span id="readyFloatingSelectedCount">0</span> selected
            </span>
            <span class="fw-medium text-white d-none d-sm-inline">order(s) selected</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3" onclick="clearReadySelections()">
                <i class="fas fa-times me-1"></i> Deselect
            </button>
            <button type="button" class="btn btn-sm btn-success rounded-pill px-4 fw-bold shadow-sm" id="btnMarkSelectedReadyDelivery" onclick="markSelectedReadyForDelivery()" style="background:#28a745; border-color:#28a745; font-size:0.875rem;">
                <i class="fas fa-truck me-1"></i> Mark Selected as Ready for Delivery
            </button>
        </div>
    </div>

    @push('modals')
    <!-- Order Detail Modal (rendered at body level to avoid stacking context issues) -->
    <div id="orderDetailModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center; backdrop-filter: blur(3px);">
        <div style="background: #fff; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); max-width: 95vw; width: 1200px; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column; animation: slideInPacking 0.3s ease-out;">
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem 2rem; border-bottom: 1px solid #e9ecef; background: #f8f9fa;">
                <h3 id="modalTitle" style="margin: 0; color: #000;">Packing Details</h3>
                <button type="button" onclick="closePackingDetailsModal()" style="background: none; border: none; font-size: 1.8rem; cursor: pointer; color: #999; padding: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; line-height: 1;">&times;</button>
            </div>
            <div style="padding: 2rem; overflow-y: auto; flex: 1;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; border: 1px solid #e9ecef;">
                        <h5 style="margin-bottom: 1rem; font-weight: 600;">Order Information</h5>
                        <div style="margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-bottom: 0.25rem;">Sales Order Number:</label>
                            <input type="text" id="detailSONumber" readonly style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #ddd; border-radius: 4px; background: #fff; font-weight: 700;">
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-bottom: 0.25rem;">Order Date:</label>
                            <input type="text" id="detailOrderDate" readonly style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #ddd; border-radius: 4px; background: #fff;">
                        </div>
                        <div id="detailCancellationDateContainer" style="display: none; margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #dc3545; display: block; margin-bottom: 0.25rem;">Cancellation Date:</label>
                            <input type="text" id="detailCancellationDate" readonly style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #dc3545; border-radius: 4px; background: #fff; color: #dc3545; font-weight: 700;">
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-bottom: 0.25rem;">Company:</label>
                            <input type="text" id="detailCustomerName" readonly style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #ddd; border-radius: 4px; background: #fff;">
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-bottom: 0.25rem;">Customer Name:</label>
                            <input type="text" id="detailRepresentative" readonly style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #ddd; border-radius: 4px; background: #fff;">
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-bottom: 0.25rem;">Account Number:</label>
                            <input type="text" id="detailAccountNumber" readonly style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #ddd; border-radius: 4px; background: #fff; font-weight: 700; font-family: monospace;">
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-bottom: 0.25rem;">Contact:</label>
                            <input type="text" id="detailContact" readonly style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #ddd; border-radius: 4px; background: #fff;">
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-bottom: 0.25rem;">Address:</label>
                            <textarea id="detailAddress" readonly rows="2" style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #ddd; border-radius: 4px; background: #fff; font-weight: 600;"></textarea>
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-bottom: 0.25rem;">Remarks / Special Instructions:</label>
                            <textarea id="detailRemarks" class="form-control" style="background:#fff; font-weight:600;" placeholder="Enter remarks or special instructions..." rows="2"></textarea>
                            <button type="button" onclick="savePackingRemarksOnly()" class="btn btn-sm btn-primary mt-1" style="background:#0d6efd; border:none; border-radius:6px; font-weight:600; padding: 0.4rem 1rem;">
                                <i class="fas fa-save me-1"></i>Save Remarks
                            </button>
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-bottom: 0.25rem;">SI Signed Date:</label>
                            <input type="text" id="siSignedDate" readonly style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #ddd; border-radius: 4px; background: #fff;">
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-bottom: 0.25rem;">Freight Option:</label>
                            <input type="text" id="detailFreightOptionPack" readonly style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #ddd; border-radius: 4px; background: #fff;">
                        </div>
                        <div id="detailForwarderPackContainer" style="display: none; margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-bottom: 0.25rem;">Forwarder:</label>
                            <input type="text" id="detailForwarderPack" readonly style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #ddd; border-radius: 4px; background: #fff; color: #0d6efd; font-weight: 700;">
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-bottom: 0.25rem;">Packing Status:</label>
                            <select id="packingStatus" style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="not_started">Not Started</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="ready_for_pickup">Ready for Pickup</option>
                            </select>
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-bottom: 0.25rem;">Number of Boxes:</label>
                            <input type="number" id="packingBoxesCount" placeholder="Enter number of boxes" min="0" style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>
                    <div id="attachmentsSection" style="display: none; background: #f8f9fa; padding: 1.5rem; border-radius: 8px; border: 1px solid #e9ecef;">
                        <h5 style="margin-bottom: 1rem; font-weight: 600;">Attachments - Packing Photos</h5>
                        <div style="margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-bottom: 0.25rem;">Upload Photo 1:</label>
                            <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                                <input type="file" id="packingPhoto1" accept="image/*" style="flex: 1; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                                <button type="button" id="cameraPhoto1Btn" class="btn" style="background: #007bff; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer;"><i class="fas fa-camera"></i> Camera</button>
                            </div>
                            <div id="photo1Preview" style="display: none; margin-top: 0.5rem;">
                                <img id="photo1Img" src="" alt="Photo 1" style="max-width: 100%; max-height: 200px; border-radius: 4px; border: 1px solid #ddd;">
                            </div>
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-bottom: 0.25rem;">Upload Photo 2:</label>
                            <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                                <input type="file" id="packingPhoto2" accept="image/*" style="flex: 1; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                                <button type="button" id="cameraPhoto2Btn" class="btn" style="background: #007bff; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer;"><i class="fas fa-camera"></i> Camera</button>
                            </div>
                            <div id="photo2Preview" style="display: none; margin-top: 0.5rem;">
                                <img id="photo2Img" src="" alt="Photo 2" style="max-width: 100%; max-height: 200px; border-radius: 4px; border: 1px solid #ddd;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Barcode / Items section -->
                <div id="barcodeScanMessage" style="display: none; padding: 0.5rem 1rem; background: #e8f5e9; border-radius: 6px; margin-bottom: 1rem; font-weight: 600; color: #2e7d32;" aria-live="polite">Ready to scan</div>

                <h5 style="margin-bottom: 1rem; margin-top: 0.5rem; font-weight: 600;">Items to Pack</h5>
                <div style="overflow-x: auto; margin-bottom: 1rem;">
                    <table class="packing-table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                <th style="padding: 0.75rem; text-align: left; font-weight: 600; width: 40px;">#</th>
                                <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Product</th>
                                <th style="padding: 0.75rem; text-align: left; font-weight: 600; width: 100px;">Qty to Pack</th>
                                <th style="padding: 0.75rem; text-align: left; font-weight: 600; width: 100px;">Unit Price</th>
                                <th style="padding: 0.75rem; text-align: left; font-weight: 600; width: 100px;">Subtotal</th>
                                <th style="padding: 0.75rem; text-align: left; font-weight: 600; width: 100px;">Packed Qty</th>
                                <th style="padding: 0.75rem; text-align: left; font-weight: 600; width: 110px;">Status</th>
                                <th style="padding: 0.75rem; text-align: left; font-weight: 600; width: 140px;">Notes</th>
                                <th style="padding: 0.75rem; text-align: left; font-weight: 600; width: 120px;">Packed Date</th>
                            </tr>
                        </thead>
                        <tbody id="packingTableBody">
                            <!-- Filled by JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Summary & Actions -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 1.5rem;">
                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; border: 1px solid #e9ecef;">
                        <h5 style="margin-bottom: 1rem; font-weight: 600;">Packing Summary</h5>
                        <div style="margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-bottom: 0.25rem;">Total Items:</label>
                            <input type="text" id="totalItems" value="0" readonly style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #ddd; border-radius: 4px; background: #fff;">
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-bottom: 0.25rem;">Items Packed:</label>
                            <input type="text" id="itemsPacked" value="0" readonly style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #ddd; border-radius: 4px; background: #fff;">
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #666; display: block; margin-bottom: 0.25rem;">Packing Progress:</label>
                            <div class="progress" style="height: 25px;">
                                <div id="packingProgressBar" class="progress-bar bg-warning" role="progressbar" style="width: 0%">
                                    <span id="packingPercent">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; border: 1px solid #e9ecef;">
                        <h5 style="margin-bottom: 1rem; font-weight: 600;">Actions</h5>
                        <button type="button" id="savePackingBtn" onclick="savePackingData()" style="width: 100%; margin-bottom: 0.5rem; background: #ffc107; color: #000; border: none; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            <i class="las la-save"></i> Save Packing
                        </button>
                        <button type="button" onclick="openPackingShippingLabel()" style="width: 100%; margin-bottom: 0.5rem; border: 1px solid #0d6efd; color: #0d6efd; background: #fff; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-tag me-1"></i> Print / View Shipping Label
                        </button>
                        <button type="button" onclick="closePackingDetailsModal()" style="width: 100%; background: #6c757d; color: #fff; border: none; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            <i class="las la-times"></i> Close Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endpush

    @push('styles')
    <style>
        .tab-pane {
            display: none;
        }
        .tab-pane.active, .tab-pane.show.active {
            display: block !important;
        }
        /* Modal Styles */
        .modal-backdrop-packing {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999 !important;
            backdrop-filter: blur(3px);
        }

        .modal-content-packing {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 95vw;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header-packing {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
        }

        .modal-close-btn {
            background: none;
            border: none;
            font-size: 1.8rem;
            cursor: pointer;
            color: #999;
            padding: 0;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close-btn:hover {
            color: #ff0000;
        }

        .modal-body-packing {
            padding: 2rem;
            overflow-y: auto;
            flex: 1;
        }

        .table-wrapper-packing {
            overflow-x: auto;
            margin-bottom: 1rem;
        }

        .order-info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .order-info-box {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #ff0000;
        }

        .order-info-box h5 {
            color: #333;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #555;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.95rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #ff0000;
            box-shadow: 0 0 0 3px rgba(255,0,0,0.1);
        }

        .form-group input:readonly {
            background: #e9ecef;
            cursor: not-allowed;
        }

        .packing-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            background: #fff;
        }

        .packing-table th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            border: 1px solid #ddd;
            text-transform: uppercase;
        }

        .packing-table td {
            padding: 0.5rem;
            border: 1px solid #ddd;
        }

        .packing-table input[type="number"],
        .packing-table input[type="text"],
        .packing-table input[type="date"],
        .packing-table select {
            width: 100%;
            border: none;
            padding: 0.5rem;
            background: transparent;
            font-size: 0.9rem;
        }

        .packing-table input:focus,
        .packing-table select:focus {
            outline: 2px solid #ffc107;
            outline-offset: -2px;
            background: #fff;
        }

        .barcode-scan-message {
            color: #555;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .visually-hidden {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }

        .barcode-scan-message.success {
            color: #155724;
        }

        .barcode-scan-message.error {
            color: #b00020;
        }

        .packing-table tr.item-packed {
            background: #e8f5e9;
        }

        .packing-table tr.item-not-packed {
            background: #ffe5e5;
        }

        .packing-table tr.item-scanned {
            animation: scannedPulse 0.7s ease-out;
        }

        @keyframes scannedPulse {
            0% { box-shadow: inset 0 0 0 3px rgba(40, 167, 69, 0.8); }
            100% { box-shadow: inset 0 0 0 0 rgba(40, 167, 69, 0); }
        }

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

        .progress {
            background-color: #e9ecef;
            border-radius: 4px;
        }

        @media print {
            .sidebar, .header, .alert, .card-header { display: none !important; }
            .card { border: none !important; box-shadow: none !important; }
            .modal-backdrop-packing { background: rgba(0, 0, 0, 0) !important; z-index: 1 !important; }
            .modal-content-packing { max-width: 100% !important; box-shadow: none !important; animation: none !important; }
            body { background: #fff !important; }
        }

        .btn-xs {
            padding: 0.35rem 0.6rem !important;
            font-size: 0.8rem !important;
        }

        .btn-xs i {
            font-size: 1rem !important;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        @media (max-width: 1200px) {
            .modal-content-packing {
                max-width: 98vw;
                max-height: 95vh;
            }
            .order-info-section {
                grid-template-columns: 1fr;
            }
            .packing-table {
                font-size: 0.85rem;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        let currentOrderId = null;
        let currentOrderItems = [];
        let barcodeScanTimer = null;
        let scannerBuffer = '';
        
        // Check if we have a preload order from picklist
        const preloadOrderId = {{ $preloadOrderId ? $preloadOrderId : 'null' }};
        console.log('Packing Management - Preload Order ID:', preloadOrderId);

        window.openPackingDetailsModal = function(orderId, isCompleted = false) {
            console.log('Opening details for order ID:', orderId);
            if (!orderId) return;
            currentOrderId = orderId;
            loadPackingOrder(orderId, isCompleted);
        };

        window.markOrderAsPackedAction = function(orderId, soNumber) {
            if (!orderId) return;
            if (confirm(`Mark ${soNumber} as packed and send directly to Delivery Scheduling?`)) {
                fetch('/production/logistic/packing/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        packing_status: 'completed',
                        boxes_count: 1,
                        items: []
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`${soNumber} marked as packed and moved directly to Delivery Scheduling!`);
                        window.location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to mark as packed'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error marking order as packed: ' + error.message);
                });
            }
        };

        window.markOrderAsGatheredAction = function(orderId, soNumber) {
            if (!orderId) return;
            if (confirm(`Mark ${soNumber} as gathered?`)) {
                markOrderAsGathered(orderId, soNumber);
            }
        };

        window.setReadyForPickupSingle = function(orderId, soNumber) {
            if (!orderId) return;
            if (confirm(`Set ${soNumber} as ready for pickup/drop-off?`)) {
                fetch('{{ route("production.logistic.packing.set-ready-for-pickup") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ order_ids: [orderId] })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while setting order as ready for pickup');
                });
            }
        };

        window.markSelectedReadyForDelivery = function() {
            const selectedCheckboxes = document.querySelectorAll('.ready-order-checkbox:checked');
            const orderIds = Array.from(selectedCheckboxes).map(cb => parseInt(cb.dataset.orderId));

            if (orderIds.length === 0) {
                alert('Please select at least one order using the checkboxes.');
                return;
            }

            if (confirm(`Mark ${orderIds.length} selected e-commerce order(s) as ready for delivery / gathered?`)) {
                fetch('{{ route("production.logistic.packing.mark-as-gathered") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ order_ids: orderIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to process selected orders'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error processing orders: ' + error.message);
                });
            }
        };

        window.clearReadySelections = function() {
            $('.ready-order-checkbox, .ready-select-all-table-checkbox').prop('checked', false);
            updateReadySelectedCount();
        };

        // Checkbox sync event handlers
        $(document).on('change', '.ready-select-all-table-checkbox', function() {
            const table = $(this).closest('table');
            table.find('.ready-order-checkbox').prop('checked', this.checked);
            updateReadySelectedCount();
        });

        $(document).on('change', '.ready-order-checkbox', function() {
            updateReadySelectedCount();
        });

        function updateReadySelectedCount() {
            const count = $('.ready-order-checkbox:checked').length;
            const floatingCountEl = document.getElementById('readyFloatingSelectedCount');
            if (floatingCountEl) {
                floatingCountEl.textContent = count;
            }

            const floatingBar = document.getElementById('readyBulkFloatingBar');
            if (floatingBar) {
                if (count > 0) {
                    floatingBar.classList.remove('hidden');
                } else {
                    floatingBar.classList.add('hidden');
                }
            }
        }

        // Initialize DataTable and Event Listeners
        $(document).ready(function() {
            // Safely initialize DataTables on all tables with class .display
            $('table.display').each(function() {
                if ($.fn.DataTable.isDataTable(this)) return;
                try {
                    $(this).DataTable({
                        order: [],
                        pageLength: 25,
                        responsive: true,
                        autoWidth: false,
                        deferRender: true
                    });
                } catch(e) {
                    console.warn('DataTable init warning for:', this.id, e);
                }
            });

            // Fail-safe manual tab switching for packingTabs
            $(document).on('click', '#packingTabs .nav-link', function(e) {
                e.preventDefault();
                $('#packingTabs .nav-link').removeClass('active');
                $(this).addClass('active');
                const target = $(this).attr('data-bs-target') || $(this).attr('data-target');
                if (target) {
                    $('#packingTabContent > .tab-pane').removeClass('show active').css('display', 'none');
                    $(target).addClass('show active').css('display', 'block');
                    setTimeout(function() {
                        if ($.fn.DataTable) {
                            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
                        }
                    }, 50);
                }
            });

            // Sub-tabs switching for Ecom Tabs
            $(document).on('click', '#ecomTabs .nav-link', function(e) {
                e.preventDefault();
                $('#ecomTabs .nav-link').removeClass('active');
                $(this).addClass('active');
                const target = $(this).attr('data-bs-target') || $(this).attr('data-target');
                if (target) {
                    $('#ecomTabsContent > .tab-pane').removeClass('show active').css('display', 'none');
                    $(target).addClass('show active').css('display', 'block');
                    setTimeout(function() {
                        if ($.fn.DataTable) {
                            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
                        }
                    }, 50);
                }
            });

            // Sub-tabs switching for Ready Pickup Tabs
            $(document).on('click', '#readyPickupPlatformTabs .nav-link', function(e) {
                e.preventDefault();
                $('#readyPickupPlatformTabs .nav-link').removeClass('active');
                $(this).addClass('active');
                const target = $(this).attr('data-bs-target') || $(this).attr('data-target');
                if (target) {
                    $('#ready-pickup-content .tab-pane').removeClass('show active').css('display', 'none');
                    $(target).addClass('show active').css('display', 'block');
                    setTimeout(function() {
                        if ($.fn.DataTable) {
                            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
                        }
                    }, 50);
                }
            });

            // If preloadOrderId is set, auto-load that order
            if (preloadOrderId) {
                console.log('Auto-loading preload order:', preloadOrderId);
                currentOrderId = preloadOrderId;
                loadPackingOrder(preloadOrderId);
            }

            // Mark as Packed Button Click using event delegation
            $(document).on('click', '.mark-packed-btn', function(e) {
                e.stopPropagation();
                const btn = $(this).closest('.mark-packed-btn');
                const orderId = btn.attr('data-order-id') || btn.data('order-id');
                const soNumber = btn.attr('data-so-number') || btn.data('so-number');
                if (orderId && confirm(`Mark all items in ${soNumber} as packed?`)) {
                    markOrderAsPacked(orderId, soNumber);
                }
            });

            // Mark as Gathered Button Click
            $(document).on('click', '.mark-gathered-btn', function(e) {
                e.stopPropagation();
                const btn = $(this).closest('.mark-gathered-btn');
                const orderId = btn.attr('data-order-id') || btn.data('order-id');
                const soNumber = btn.attr('data-so-number') || btn.data('so-number');
                if (orderId && confirm(`Mark ${soNumber} as gathered?`)) {
                    markOrderAsGathered(orderId, soNumber);
                }
            });

            // View Order Button Click
           document.addEventListener('click', function(e) {
    const btn = e.target.closest('.view-order-btn');

    if (!btn) {
        return;
    }

    e.preventDefault();
    e.stopPropagation();

    const orderId = btn.getAttribute('data-order-id');

    console.log('Clicked view details for order:', orderId);

    if (orderId) {
        currentOrderId = orderId;
        loadPackingOrder(orderId);
    }
});

            // Close Detail Modal
            const closeDetailBtn = document.getElementById('closeDetailBtn');
            if (closeDetailBtn) {
                closeDetailBtn.addEventListener('click', function() {
                    closePackingDetailsModal();
                });
            }

            // Close modal button inside the modal
            document.querySelectorAll('.close-modal-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    closePackingDetailsModal();
                });
            });

            // Close modal when clicking outside
            const orderDetailModal = document.getElementById('orderDetailModal');
            if (orderDetailModal) {
                orderDetailModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closePackingDetailsModal();
                    }
                });
            }

            // Photo upload handlers
            const packingPhoto1 = document.getElementById('packingPhoto1');
            if (packingPhoto1) {
                packingPhoto1.addEventListener('change', function(e) {
                    handlePhotoUpload(e, 1);
                });
            }

            const packingPhoto2 = document.getElementById('packingPhoto2');
            if (packingPhoto2) {
                packingPhoto2.addEventListener('change', function(e) {
                    handlePhotoUpload(e, 2);
                });
            }

            // Camera buttons
            const cameraPhoto1Btn = document.getElementById('cameraPhoto1Btn');
            if (cameraPhoto1Btn) {
                cameraPhoto1Btn.addEventListener('click', function() {
                    openCamera(1);
                });
            }

            const cameraPhoto2Btn = document.getElementById('cameraPhoto2Btn');
            if (cameraPhoto2Btn) {
                cameraPhoto2Btn.addEventListener('click', function() {
                    openCamera(2);
                });
            }

            // Initialize bulk actions
            initializeBulkActions();
        });

        window.openPackingDetailsModal = function(orderId, isCompleted = false) {
            if (orderId) {
                currentOrderId = orderId;
                console.log('Opening packing details modal for order:', currentOrderId);
                loadPackingOrder(currentOrderId, isCompleted);
            }
        };

        function openPackingDetailsModal(orderId, isCompleted = false) {
            window.openPackingDetailsModal(orderId, isCompleted);
        }

        window.openPackingShippingLabel = function() {
            if (currentOrderId) {
                window.open('/marketing/sales-orders/' + currentOrderId + '/shipping-label', '_blank');
            } else {
                alert('No order selected');
            }
        };

        function openPackingShippingLabel() {
            window.openPackingShippingLabel();
        }

        window.savePackingRemarksOnly = function() {
            if (!currentOrderId) {
                alert('No order selected');
                return;
            }
            const remarks = document.getElementById('detailRemarks') ? document.getElementById('detailRemarks').value : '';
            fetch('/production/logistic/packing/save-remarks', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ order_id: currentOrderId, remarks: remarks })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Remarks saved successfully');
                } else {
                    alert('Error: ' + (data.message || 'Failed to save remarks'));
                }
            })
            .catch(error => {
                alert('Error saving remarks: ' + error.message);
            });
        };

        function savePackingRemarksOnly() {
            window.savePackingRemarksOnly();
        }

        window.markOrderAsPackedAction = function(orderId, soNumber) {
            markOrderAsPacked(orderId, soNumber);
        };

        function markOrderAsPackedAction(orderId, soNumber) {
            window.markOrderAsPackedAction(orderId, soNumber);
        }

        window.markOrderAsGatheredAction = function(orderId, soNumber) {
            markOrderAsGathered(orderId, soNumber);
        };

        function markOrderAsGatheredAction(orderId, soNumber) {
            window.markOrderAsGatheredAction(orderId, soNumber);
        }

        window.closePackingDetailsModal = function() {
            const modal = document.getElementById('orderDetailModal');
            if (modal) {
                modal.style.display = 'none';
            }
            currentOrderId = null;
            currentOrderItems = [];
            scannerBuffer = '';
        };

        function closePackingDetailsModal() {
            window.closePackingDetailsModal();
        }

        document.addEventListener('keydown', function(e) {
            if (document.getElementById('orderDetailModal').style.display === 'none' || !currentOrderItems.length) {
                return;
            }

            const tagName = (e.target.tagName || '').toLowerCase();
            const isEditable = tagName === 'input' || tagName === 'select' || tagName === 'textarea' || e.target.isContentEditable;

            if (isEditable) {
                return;
            }

            if (e.key === 'Enter') {
                if (scannerBuffer.trim()) {
                    e.preventDefault();
                    processPackingBarcode(scannerBuffer);
                    scannerBuffer = '';
                }
                return;
            }

            if (e.key.length !== 1) {
                return;
            }

            scannerBuffer += e.key;
            clearTimeout(barcodeScanTimer);
            barcodeScanTimer = setTimeout(() => {
                if (scannerBuffer.trim().length >= 6) {
                    processPackingBarcode(scannerBuffer);
                    scannerBuffer = '';
                }
            }, 250);
        });

        function markOrderAsPacked(orderId, soNumber) {
            // Fetch order data first
            fetch(`/production/logistic/packing/${orderId}/data`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP Error: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        console.error('Failed to load order:', data);
                        alert('Error: ' + (data.message || 'Could not load order data'));
                        return;
                    }

                    const order = data.order;
                    const packingItems = [];
                    const today = new Date().toISOString().split('T')[0];

                    // Create packing items with all qty marked as packed
                    order.items.forEach((item, index) => {
                        packingItems.push({
                            index: index,
                            packed_qty: item.quantity,
                            status: 'Packed',
                            notes: 'Auto-marked as packed',
                            packed_date: today,
                        });
                    });

                    const payload = {
                        order_id: orderId,
                        packing_status: 'completed',
                        boxes_count: null,
                        items: packingItems,
                    };

                    // Save packing data
                    fetch('/production/logistic/packing/save', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(`✓ All items in ${soNumber} marked as packed!`);
                            window.location.reload();
                        } else {
                            alert('Error: ' + (data.message || 'Failed to mark as packed'));
                        }
                    })
                    .catch(error => {
                        console.error('Error saving packing:', error);
                        alert('Error marking as packed: ' + error.message);
                    });
                })
                .catch(error => {
                    console.error('Error loading order:', error);
                    alert('Error loading order: ' + (error.message || 'Unknown error'));
                });
        }

        function markOrderAsGathered(orderId, soNumber) {
            fetch('/production/logistic/packing/mark-as-gathered', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    order_id: orderId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || `✓ ${soNumber} marked as gathered!`);
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to mark as gathered'));
                }
            })
            .catch(error => {
                console.error('Error marking as gathered:', error);
                alert('Error: ' + error.message);
            });
        }

        function loadPackingOrder(orderId, isCompleted = false) {
            // Fetch order data
            fetch(`/production/logistic/packing/${orderId}/data`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP Error: ${response.status} - ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        console.error('API returned error:', data);
                        alert('Error: ' + (data.message || 'Failed to load order data'));
                        return;
                    }

                    const order = data.order;
                    currentOrderItems = order.items;
                    
                    // Helper function to safely set input value
                    const setInputValue = (id, value) => {
                        const element = document.getElementById(id);
                        if (element) {
                            element.value = value || '';
                        } else {
                            console.warn(`Element with id "${id}" not found in DOM`);
                        }
                    };

                    // Populate order info
                    setInputValue('detailSONumber', order.so_number);
                    setInputValue('detailOrderDate', new Date(order.created_at).toLocaleDateString());
                    const cancelContainer = document.getElementById('detailCancellationDateContainer');
                    if (cancelContainer) {
                        if (order.cancellation_date) {
                            cancelContainer.style.display = 'block';
                            setInputValue('detailCancellationDate', new Date(order.cancellation_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }));
                        } else {
                            cancelContainer.style.display = 'none';
                        }
                    }
                    setInputValue('detailCustomerName', order.display_company_name || order.customer?.company_name || order.customer?.customer_name || 'N/A');
                    setInputValue('detailRepresentative', order.customer_representative || order.customer?.customer_name || 'N/A');
                    setInputValue('detailAccountNumber', order.display_account_number || order.customer?.account_number || 'N/A');
                    setInputValue('detailContact', order.customer_contact || order.customer?.mobile || order.customer?.main_phone || 'N/A');
                    setInputValue('detailAddress', order.display_address || order.shipping_address || order.customer?.address || order.customer?.business_address || 'N/A');
                    const packingData = order.packing_data ? (typeof order.packing_data === 'string' ? JSON.parse(order.packing_data) : order.packing_data) : {};
                    setInputValue('detailRemarks', order.remarks || (packingData.remarks || ''));
                    const signedDate = order.signed_at ? new Date(order.signed_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : (order.acct_approved_at ? new Date(order.acct_approved_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'Not Signed Yet');
                    setInputValue('siSignedDate', signedDate);
                    const freightOptStr = order.freight_option ? (order.freight_option.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())) : 'N/A';
                    setInputValue('detailFreightOptionPack', freightOptStr);
                    const forwarderPackContainer = document.getElementById('detailForwarderPackContainer');
                    if (forwarderPackContainer) {
                        if (order.freight_option === 'bill_client' || order.forwarder) {
                            forwarderPackContainer.style.display = 'block';
                            setInputValue('detailForwarderPack', order.forwarder || 'N/A');
                        } else {
                            forwarderPackContainer.style.display = 'none';
                        }
                    }
                    setInputValue('packingStatus', packingData.status || 'not_started');
                    setInputValue('packingBoxesCount', packingData.boxes_count || '');

                    // Show/hide "Ready for Pickup" status based on order type
                    const packingStatusSelect = document.getElementById('packingStatus');
                    if (packingStatusSelect) {
                        const readyPickupOption = packingStatusSelect.querySelector('option[value="ready_for_pickup"]');
                        if (readyPickupOption) {
                            if (order.type === 'ecom_direct') {
                                readyPickupOption.style.display = 'block';
                            } else {
                                readyPickupOption.style.display = 'none';
                            }
                        }
                    }

                    // Show/hide attachments section based on order type
                    const attachmentsSection = document.getElementById('attachmentsSection');
                    if (attachmentsSection) {
                        if (order.type === 'ecom_direct') {
                            attachmentsSection.style.display = 'block';
                        } else {
                            attachmentsSection.style.display = 'none';
                        }
                    }

                    // Display saved attachments if available
                    if (packingData.attachments) {
                        if (packingData.attachments.photo_1) {
                            const photo1Preview = document.getElementById('photo1Preview');
                            const photo1Img = document.getElementById('photo1Img');
                            if (photo1Preview && photo1Img) {
                                photo1Img.src = '/storage/' + packingData.attachments.photo_1;
                                photo1Preview.style.display = 'block';
                            }
                        }
                        if (packingData.attachments.photo_2) {
                            const photo2Preview = document.getElementById('photo2Preview');
                            const photo2Img = document.getElementById('photo2Img');
                            if (photo2Preview && photo2Img) {
                                photo2Img.src = '/storage/' + packingData.attachments.photo_2;
                                photo2Preview.style.display = 'block';
                            }
                        }
                    }

                    // Populate items table
                    let html = '';
                    let totalItems = 0;
                    let packedItems = 0;

                    (order.items || []).forEach((item, index) => {
                        const itemKey = `item_${index}`;
                        const itemData = packingData[itemKey] || {};
                        
                        totalItems++;
                        if (itemData.status === 'Packed') packedItems++;

                       // const prodName = item.book_index ? (item.book_index.display_name || item.book_index.title) : (item.book ? item.book.name : (item.bundle ? item.bundle.name : 'N/A'));

                       const prodName =
    item.book_index?.display_name ||
    item.book_index?.title ||
    item.book?.name ||
    item.book_bundle?.name ||
    item.bookBundle?.name ||
    item.bundle?.name ||
    item.name ||
    'N/A';

    
                        const itemType = item.item_type || (item.bundle_id || item.bundle || item.book_bundle || item.bookBundle ? 'Bundle' : (item.book_index_id || item.book_index || item.bookIndex ? 'Index' : 'Book'));
                        let typeBadgeHtml = '<span class="badge bg-primary ms-1">Book</span>';
                        if (itemType === 'Bundle') {
                            typeBadgeHtml = '<span class="badge ms-1" style="background:#6f42c1; color:#fff;">Bundle</span>';
                        } else if (itemType === 'Index') {
                            typeBadgeHtml = '<span class="badge bg-info text-dark ms-1">Index</span>';
                        }

                        const currSym = (order && order.currency === 'USD') ? '$' : '₱';
                        let itemQty = parseFloat((item.quantity && parseFloat(item.quantity) > 0) 
                            ? item.quantity 
                            : ((item.customer_selected_qty && parseFloat(item.customer_selected_qty) > 0) 
                                ? item.customer_selected_qty 
                                : ((item.selected_qty && parseFloat(item.selected_qty) > 0) 
                                    ? item.selected_qty 
                                    : (item.qty || 0))));
                        if (isNaN(itemQty) || itemQty <= 0) {
                            itemQty = 1;
                        }
                        const unitPrice = parseFloat(item.price || 0).toFixed(2);
                        const subtotalPrice = (item.subtotal && parseFloat(item.subtotal) > 0) 
                            ? parseFloat(item.subtotal).toFixed(2) 
                            : (itemQty * parseFloat(item.price || 0)).toFixed(2);

                        html += `
                            <tr id="packing_item_row_${index}">
                                <td>${index + 1}</td>
                                <td class="fw-bold">${prodName} ${typeBadgeHtml}</td>
                                <td><input type="number" value="${itemQty}" readonly style="width: 100%; border: none; background: transparent; font-weight: 600;"></td>
                                <td>${currSym}${unitPrice}</td>
                                <td>${currSym}${subtotalPrice}</td>
                                <td><input type="number" id="packed_qty_${index}" min="0" max="${itemQty}" value="${itemData.packed_qty || 0}" onchange="updatePackingCount()"></td>
                                <td>
                                    <select id="packed_status_${index}" onchange="handlePackingStatusChange()">
                                        <option value="Not Packed" ${itemData.status === 'Not Packed' ? 'selected' : ''}>Not Packed</option>
                                        <option value="In Progress" ${itemData.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
                                        <option value="Packed" ${itemData.status === 'Packed' ? 'selected' : ''}>Packed</option>
                                    </select>
                                </td>
                                <td><input type="text" id="packed_notes_${index}" value="${itemData.notes || ''}" placeholder="Add notes..."></td>
                                <td><input type="date" id="packed_date_${index}" value="${itemData.packed_date || new Date().toISOString().split('T')[0]}"></td>
                            </tr>
                        `;
                    });

                    const packingTableBody = document.getElementById('packingTableBody');
                    if (packingTableBody) {
                        packingTableBody.innerHTML = html;
                    }

                    setInputValue('totalItems', totalItems);
                    updatePackingCount();

                    // Show detail modal
                    const orderDetailModal = document.getElementById('orderDetailModal');
                    if (orderDetailModal) {
                        orderDetailModal.style.display = 'flex';
                    }
                    
                    const modalTitle = document.getElementById('modalTitle');
                    if (modalTitle) {
                        modalTitle.textContent = `Packing Details - ${order.so_number}`;
                    }

                    scannerBuffer = '';
                    setBarcodeScanMessage('Ready to scan', 'neutral');
                    refreshPackingRowColors();
                    
                    // Disable inputs if completed
                    if (isCompleted) {
                        const packingStatusEl = document.getElementById('packingStatus');
                        const packingBoxesCountEl = document.getElementById('packingBoxesCount');
                        
                        if (packingStatusEl) packingStatusEl.disabled = true;
                        if (packingBoxesCountEl) packingBoxesCountEl.disabled = true;
                        
                        for (let i = 0; i < totalItems; i++) {
                            const qtyEl = document.getElementById(`packed_qty_${i}`);
                            const statusEl = document.getElementById(`packed_status_${i}`);
                            const notesEl = document.getElementById(`packed_notes_${i}`);
                            const dateEl = document.getElementById(`packed_date_${i}`);
                            
                            if (qtyEl) qtyEl.disabled = true;
                            if (statusEl) statusEl.disabled = true;
                            if (notesEl) notesEl.disabled = true;
                            if (dateEl) dateEl.disabled = true;
                        }
                        
                        const saveBtn = document.getElementById('savePackingBtn');
                        if (saveBtn) saveBtn.style.display = 'none';
                    } else {
                        const packingStatusEl = document.getElementById('packingStatus');
                        const packingBoxesCountEl = document.getElementById('packingBoxesCount');
                        
                        if (packingStatusEl) packingStatusEl.disabled = false;
                        if (packingBoxesCountEl) packingBoxesCountEl.disabled = false;
                        
                        for (let i = 0; i < totalItems; i++) {
                            const qtyEl = document.getElementById(`packed_qty_${i}`);
                            const statusEl = document.getElementById(`packed_status_${i}`);
                            const notesEl = document.getElementById(`packed_notes_${i}`);
                            const dateEl = document.getElementById(`packed_date_${i}`);
                            
                            if (qtyEl) qtyEl.disabled = false;
                            if (statusEl) statusEl.disabled = false;
                            if (notesEl) notesEl.disabled = false;
                            if (dateEl) dateEl.disabled = false;
                        }
                        
                        const saveBtn = document.getElementById('savePackingBtn');
                        if (saveBtn) saveBtn.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error loading order:', error);
                    alert('Error loading order data: ' + (error.message || 'Unknown error. Please check browser console and server logs.'));
                });
        }

        function updatePackingCount() {
            const totalItems = parseInt(document.getElementById('totalItems').value) || 0;
            let packedCount = 0;

            for (let i = 0; i < totalItems; i++) {
                const status = document.getElementById(`packed_status_${i}`).value;
                if (status === 'Packed') {
                    packedCount++;
                }
            }

            document.getElementById('itemsPacked').value = packedCount;
            const percent = totalItems > 0 ? Math.round((packedCount / totalItems) * 100) : 0;
            document.getElementById('packingProgressBar').style.width = percent + '%';
            document.getElementById('packingPercent').textContent = percent + '%';
            document.getElementById('packingStatus').value = packedCount === totalItems && totalItems > 0
                ? 'completed'
                : (packedCount > 0 ? 'in_progress' : 'not_started');
            refreshPackingRowColors();
        }

        function handlePackingStatusChange() {
            updatePackingCount();
        }

        function normalizeBarcode(value) {
            return (value || '').toString().trim().toLowerCase();
        }

        function getItemBarcodes(item) {
            const book = item.book || {};
            return [
                book.barcode,
                book.nbs_barcode,
                book.sku,
                book.item_code,
                item.isbn,
            ].map(normalizeBarcode).filter(Boolean);
        }

        function processPackingBarcode(rawBarcode) {
            clearTimeout(barcodeScanTimer);
            const barcode = normalizeBarcode(rawBarcode);

            if (!barcode || !currentOrderItems.length) {
                return;
            }

            const matchedIndex = currentOrderItems.findIndex(item => getItemBarcodes(item).includes(barcode));

            if (matchedIndex === -1) {
                setBarcodeScanMessage(`Barcode not found in this order: ${rawBarcode.trim()}`, 'error');
                return;
            }

            const item = currentOrderItems[matchedIndex];
            const qtyInput = document.getElementById(`packed_qty_${matchedIndex}`);
            const statusSelect = document.getElementById(`packed_status_${matchedIndex}`);
            const notesInput = document.getElementById(`packed_notes_${matchedIndex}`);
            const dateInput = document.getElementById(`packed_date_${matchedIndex}`);
            const row = document.getElementById(`packing_item_row_${matchedIndex}`);

            qtyInput.value = item.quantity;
            statusSelect.value = 'Packed';
            dateInput.value = new Date().toISOString().split('T')[0];
            if (!notesInput.value.trim()) {
                notesInput.value = 'Scanned by barcode';
            }

            updatePackingCount();
            row.classList.remove('item-scanned');
            void row.offsetWidth;
            row.classList.add('item-scanned');
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setBarcodeScanMessage(`${item.book.name} marked as packed`, 'success');
        }

        function refreshPackingRowColors() {
            const totalItems = parseInt(document.getElementById('totalItems').value) || 0;

            for (let i = 0; i < totalItems; i++) {
                const row = document.getElementById(`packing_item_row_${i}`);
                const status = document.getElementById(`packed_status_${i}`)?.value;

                if (!row) continue;

                row.classList.toggle('item-packed', status === 'Packed');
                row.classList.toggle('item-not-packed', status !== 'Packed');
            }
        }

        function setBarcodeScanMessage(message, type) {
            const messageBox = document.getElementById('barcodeScanMessage');
            if (!messageBox) return;
            messageBox.textContent = message;
            messageBox.style.display = message ? 'block' : 'none';
            messageBox.style.background = type === 'success' ? '#e8f5e9' : (type === 'error' ? '#ffebee' : '#e8f5e9');
            messageBox.style.color = type === 'success' ? '#2e7d32' : (type === 'error' ? '#c62828' : '#2e7d32');
        }

        function savePackingData() {
            if (!currentOrderId) {
                alert('No order selected');
                return;
            }

            const totalItems = parseInt(document.getElementById('totalItems').value) || 0;
            const packingItems = [];

            for (let i = 0; i < totalItems; i++) {
                packingItems.push({
                    index: i,
                    packed_qty: parseInt(document.getElementById(`packed_qty_${i}`).value) || 0,
                    status: document.getElementById(`packed_status_${i}`).value,
                    notes: document.getElementById(`packed_notes_${i}`).value,
                    packed_date: document.getElementById(`packed_date_${i}`).value,
                });
            }

            // Get photo attachments
            const photo1Input = document.getElementById('packingPhoto1');
            const photo2Input = document.getElementById('packingPhoto2');

            // Function to compress image before base64 encoding to speed up upload loading time
            const compressAndGetBase64 = (file) => {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            let width = img.width;
                            let height = img.height;
                            const max_size = 1200; // max resolution

                            if (width > height) {
                                if (width > max_size) {
                                    height *= max_size / width;
                                    width = max_size;
                                }
                            } else {
                                if (height > max_size) {
                                    width *= max_size / height;
                                    height = max_size;
                                }
                            }

                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);
                            
                            // Compress to JPEG with 0.75 quality to drastically reduce upload size
                            const dataUrl = canvas.toDataURL('image/jpeg', 0.75);
                            resolve(dataUrl);
                        };
                        img.onerror = error => reject(error);
                        img.src = e.target.result;
                    };
                    reader.onerror = error => reject(error);
                    reader.readAsDataURL(file);
                });
            };

            // Process attachments with compression to prevent long loading delays
            Promise.all([
                photo1Input && photo1Input.files.length > 0 ? compressAndGetBase64(photo1Input.files[0]) : Promise.resolve(null),
                photo2Input && photo2Input.files.length > 0 ? compressAndGetBase64(photo2Input.files[0]) : Promise.resolve(null)
            ])
            .then(([photo1Base64, photo2Base64]) => {
                const payload = {
                    order_id: currentOrderId,
                    packing_status: document.getElementById('packingStatus').value,
                    boxes_count: document.getElementById('packingBoxesCount').value,
                    remarks: document.getElementById('detailRemarks') ? document.getElementById('detailRemarks').value : '',
                    items: packingItems,
                    attachments: {
                        photo_1: photo1Base64,
                        photo_2: photo2Base64
                    }
                };

                fetch('/production/logistic/packing/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Packing data saved successfully');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to save packing data'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error saving packing data: ' + error.message);
                });
            })
            .catch(error => {
                console.error('Error converting files:', error);
                alert('Error processing photo attachments: ' + error.message);
            });
        }

        // Bulk Action Handling
        let selectedOrderIds = new Set();

        function initializeBulkActions() {
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const setReadyPickupBtn = document.getElementById('setReadyPickupBtn');
            const clearSelectionBtn = document.getElementById('clearSelectionBtn');

            // Select All functionality using event delegation
            $(document).on('change', '#selectAllCheckbox', function() {
                const isChecked = this.checked;
                $('.order-checkbox').each(function() {
                    if (!this.disabled) {
                        this.checked = isChecked;
                        const orderId = this.dataset.orderId;
                        if (isChecked) {
                            selectedOrderIds.add(orderId);
                        } else {
                            selectedOrderIds.delete(orderId);
                        }
                    }
                });
                updateBulkActionToolbar();
            });

            // Individual checkbox handling using event delegation
            $(document).on('change', '.order-checkbox', function() {
                const orderId = this.dataset.orderId;
                if (this.checked) {
                    selectedOrderIds.add(orderId);
                } else {
                    selectedOrderIds.delete(orderId);
                }
                updateSelectAllCheckbox();
                updateBulkActionToolbar();
            });

            // Set as Ready for Pickup button
            if (setReadyPickupBtn) {
                setReadyPickupBtn.addEventListener('click', function() {
                    if (selectedOrderIds.size === 0) {
                        alert('Please select at least one fully packed order');
                        return;
                    }
                    
                    if (confirm(`Mark ${selectedOrderIds.size} order(s) as packed?`)) {
                        setOrdersAsReadyForPickup();
                    }
                });
            }

            // Clear Selection button
            if (clearSelectionBtn) {
                clearSelectionBtn.addEventListener('click', function() {
                    selectedOrderIds.clear();
                    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
                    if (selectAllCheckbox) selectAllCheckbox.checked = false;
                    $('.order-checkbox').prop('checked', false);
                    updateBulkActionToolbar();
                });
            }
        }

        function updateBulkActionToolbar() {
            const toolbar = document.getElementById('bulkActionToolbar');
            const selectedCount = document.getElementById('selectedCount');
            
            selectedCount.textContent = selectedOrderIds.size;
            
            if (selectedOrderIds.size > 0) {
                toolbar.style.display = 'flex';
            } else {
                toolbar.style.display = 'none';
            }
        }

        function updateSelectAllCheckbox() {
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            if (!selectAllCheckbox) return;

            const orderCheckboxes = $('.order-checkbox:not(:disabled)');
            const checkedCheckboxes = $('.order-checkbox:not(:disabled):checked');

            if (orderCheckboxes.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedCheckboxes.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedCheckboxes.length === orderCheckboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
            }
        }

        function setOrdersAsReadyForPickup() {
            const orderIds = Array.from(selectedOrderIds);
            
            fetch('{{ route("production.logistic.packing.set-ready-for-pickup") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ order_ids: orderIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while setting orders as ready for pickup');
            });
        }

        function handlePhotoUpload(e, photoNumber) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please select a valid image file');
                return;
            }

            // Create FileReader to display preview
            const reader = new FileReader();
            reader.onload = function(event) {
                displayPhotoPreview(event.target.result, photoNumber);
            };
            reader.readAsDataURL(file);
        }

        function displayPhotoPreview(imageSrc, photoNumber) {
            const previewDiv = document.getElementById(`photo${photoNumber}Preview`);
            const previewImg = document.getElementById(`photo${photoNumber}Img`);
            
            previewImg.src = imageSrc;
            previewDiv.style.display = 'block';
        }

        function openCamera(photoNumber) {
            // Check if getUserMedia is available
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Camera access is not supported on this device or browser');
                return;
            }

            // Create a temporary video element for camera capture
            const videoId = `tempVideoCamera${photoNumber}`;
            const existingVideo = document.getElementById(videoId);
            if (existingVideo) {
                existingVideo.remove();
            }

            const video = document.createElement('video');
            video.id = videoId;
            video.autoplay = true;
            video.playsinline = true;
            video.style.cssText = 'display:none;';
            document.body.appendChild(video);

            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                .then(stream => {
                    video.srcObject = stream;
                    
                    // Create a modal for camera capture
                    createCameraModal(video, stream, photoNumber);
                })
                .catch(error => {
                    console.error('Error accessing camera:', error);
                    alert('Unable to access camera: ' + error.message);
                    video.remove();
                });
        }

        function createCameraModal(video, stream, photoNumber) {
            const modal = document.createElement('div');
            modal.id = `cameraModal${photoNumber}`;
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.9);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                z-index: 10000;
            `;

            const canvas = document.createElement('canvas');
            canvas.id = `cameraCanvas${photoNumber}`;
            canvas.style.cssText = 'display:none;';
            
            const videoContainer = document.createElement('div');
            videoContainer.style.cssText = `
                width: 90vw;
                max-width: 600px;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            `;

            const cameraVideo = document.createElement('video');
            cameraVideo.srcObject = stream;
            cameraVideo.autoplay = true;
            cameraVideo.playsinline = true;
            cameraVideo.style.cssText = `
                width: 100%;
                height: auto;
                display: block;
                transform: scaleX(-1);
            `;

            videoContainer.appendChild(cameraVideo);

            const buttonContainer = document.createElement('div');
            buttonContainer.style.cssText = `
                display: flex;
                gap: 1rem;
                justify-content: center;
                margin-top: 2rem;
            `;

            const captureBtn = document.createElement('button');
            captureBtn.textContent = '📷 Capture Photo';
            captureBtn.style.cssText = `
                background: #28a745;
                color: white;
                border: none;
                padding: 0.75rem 2rem;
                border-radius: 6px;
                cursor: pointer;
                font-weight: 600;
                font-size: 1rem;
            `;

            const cancelBtn = document.createElement('button');
            cancelBtn.textContent = ' Cancel';
            cancelBtn.style.cssText = `
                background: #6c757d;
                color: white;
                border: none;
                padding: 0.75rem 2rem;
                border-radius: 6px;
                cursor: pointer;
                font-weight: 600;
                font-size: 1rem;
            `;

            captureBtn.addEventListener('click', function() {
                capturePhoto(cameraVideo, canvas, photoNumber, modal, stream);
            });

            cancelBtn.addEventListener('click', function() {
                closeCameraModal(modal, stream);
            });

            buttonContainer.appendChild(captureBtn);
            buttonContainer.appendChild(cancelBtn);

            modal.appendChild(videoContainer);
            modal.appendChild(buttonContainer);
            modal.appendChild(canvas);

            document.body.appendChild(modal);

            // Handle escape key to close modal
            const escapeHandler = (e) => {
                if (e.key === 'Escape') {
                    closeCameraModal(modal, stream);
                    document.removeEventListener('keydown', escapeHandler);
                }
            };
            document.addEventListener('keydown', escapeHandler);
        }

        function capturePhoto(video, canvas, photoNumber, modal, stream) {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            // Flip the image horizontally (mirror effect)
            context.scale(-1, 1);
            context.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);

            // Convert to blob and set to file input
            canvas.toBlob(blob => {
                const file = new File([blob], `packing_photo_${photoNumber}_${Date.now()}.jpg`, { type: 'image/jpeg' });
                
                // Set the file to the input
                const fileInput = document.getElementById(`packingPhoto${photoNumber}`);
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;

                // Trigger change event to show preview
                const event = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(event);

                // Close modal and stop stream
                closeCameraModal(modal, stream);
            }, 'image/jpeg', 0.9);
        }

        function closeCameraModal(modal, stream) {
            // Stop all tracks in the stream
            stream.getTracks().forEach(track => track.stop());

            // Remove modal and video element
            modal.remove();
            const videoId = modal.querySelector('video') ? `tempVideoCamera${modal.id.replace('cameraModal', '')}` : null;
            if (videoId) {
                const tempVideo = document.getElementById(videoId);
                if (tempVideo) tempVideo.remove();
            }
        }

        function openPackingShippingLabel() {
            if (!currentOrderId) {
                alert('Please select an order first.');
                return;
            }
            window.open('/production/logistic/shipping-label/' + currentOrderId, '_blank');
        }

        function savePackingRemarksOnly() {
            if (!currentOrderId) {
                alert('No order selected');
                return;
            }
            const remarks = document.getElementById('detailRemarks').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            fetch('/production/logistic/packing/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    order_id: currentOrderId,
                    remarks: remarks,
                    packing_status: document.getElementById('packingStatus')?.value || 'not_started'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Packing remarks saved successfully!');
                } else {
                    alert('Error saving remarks: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(err => alert('Error saving remarks: ' + err.message));
        // --- Team Stock Transfer Packing & Barcode Scanning JS ---
  }
        let tsScannerBuffer = '';
        let tsScannerTimer = null;

        function normalizeTeamStockBarcode(bc) {
            if (!bc) return '';
            return String(bc).trim().toLowerCase().replace(/[^a-z0-9]/g, '');
        }

        function processTeamStockBarcodeScan(transferId, rawBarcode) {
            const rawTrimmed = (rawBarcode || '').trim();
            const normalized = normalizeTeamStockBarcode(rawTrimmed);
            const feedbackEl = document.getElementById(`ts_scan_feedback_${transferId}`);

            if (!normalized && !rawTrimmed) return false;

            const rows = document.querySelectorAll(`#teamStockPackModal${transferId} .ts-item-row`);
            let matched = false;
            let matchedTitle = '';

            rows.forEach(row => {
                if (matched) return; // match one item per scan
                const index = row.getAttribute('data-index');
                const title = (row.getAttribute('data-title') || '').trim();
                let barcodes = [];
                try {
                    let rawAttr = row.getAttribute('data-barcodes') || '[]';
                    const txt = document.createElement('textarea');
                    txt.innerHTML = rawAttr;
                    rawAttr = txt.value;
                    barcodes = JSON.parse(rawAttr);
                } catch (e) {
                    const rawAttr = row.getAttribute('data-barcodes') || '';
                    barcodes = rawAttr.replace(/[\[\]"'\&quot\;]/g, '').split(',').map(s => s.trim()).filter(Boolean);
                }

                if (!Array.isArray(barcodes)) {
                    barcodes = [String(barcodes)];
                }

                const normalizedBarcodes = barcodes.map(normalizeTeamStockBarcode);
                const normalizedTitle = normalizeTeamStockBarcode(title);

                const isMatch = (normalized && normalizedBarcodes.includes(normalized)) ||
                                (normalized && normalizedTitle.includes(normalized)) ||
                                (rawTrimmed && barcodes.some(b => String(b).trim().toLowerCase() === rawTrimmed.toLowerCase())) ||
                                (rawTrimmed && title.toLowerCase().includes(rawTrimmed.toLowerCase()));

                if (isMatch) {
                    matched = true;
                    matchedTitle = title;
                    markTeamStockItemAsPacked(transferId, index, title);
                }
            });

            if (matched && feedbackEl) {
                feedbackEl.className = 'p-2 rounded text-center small fw-bold bg-success text-white border';
                feedbackEl.innerHTML = `<i class="fas fa-check-circle me-1"></i>SCANNED: "${matchedTitle}" - Marked as Packed!`;
            } else if (!matched && feedbackEl) {
                feedbackEl.className = 'p-2 rounded text-center small fw-bold bg-danger text-white border';
                feedbackEl.innerHTML = `<i class="fas fa-times-circle me-1"></i>Barcode "${rawTrimmed}" not found in this transfer!`;
            }

            return matched;
        }

        function onTSManualScanClick(transferId) {
            const input = document.getElementById(`ts_barcode_input_${transferId}`);
            if (input && input.value.trim()) {
                processTeamStockBarcodeScan(transferId, input.value.trim());
                input.value = '';
                input.focus();
            }
        }

        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && e.target.classList.contains('ts-barcode-input-field')) {
                e.preventDefault();
                const transferId = e.target.getAttribute('data-transfer-id');
                if (transferId) {
                    onTSManualScanClick(transferId);
                }
            }
        });

        function onTSStatusSelectChange(transferId, index) {
            const select = document.getElementById(`ts_packed_status_${transferId}_${index}`);
            const badge = document.getElementById(`ts_status_badge_${transferId}_${index}`);
            const row = document.getElementById(`ts_row_${transferId}_${index}`);
            if (!select) return;

            const val = select.value;
            if (val === 'Packed') {
                if (row) row.style.backgroundColor = '#d4edda';
                if (badge) badge.className = 'd-none bg-success';
            } else if (val === 'In Progress') {
                if (row) row.style.backgroundColor = '#fff3cd';
                if (badge) badge.className = 'd-none bg-warning';
            } else {
                if (row) row.style.backgroundColor = '#f8d7da';
                if (badge) badge.className = 'd-none bg-danger';
            }
            updateTeamStockPackingProgress(transferId);
        }

        function onTSPackedQtyInput(transferId, index, unitPrice) {
            const qtyInput = document.getElementById(`ts_packed_qty_${transferId}_${index}`);
            const qtyToPackEl = document.getElementById(`ts_qty_to_pack_${transferId}_${index}`);
            const subtotalEl = document.getElementById(`ts_subtotal_${transferId}_${index}`);
            const select = document.getElementById(`ts_packed_status_${transferId}_${index}`);
            const row = document.getElementById(`ts_row_${transferId}_${index}`);

            if (!qtyInput) return;
            const qty = parseFloat(qtyInput.value) || 0;

            if (qtyToPackEl && (parseFloat(qtyToPackEl.textContent) <= 0 || qtyToPackEl.textContent.trim() === '0.00')) {
                qtyToPackEl.textContent = qty.toFixed(2);
            }
            if (subtotalEl) {
                subtotalEl.textContent = '₱' + (qty * unitPrice).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            if (qty > 0) {
                if (select) select.value = 'Packed';
                if (row) row.style.backgroundColor = '#d4edda';
            }
            updateTeamStockPackingProgress(transferId);
        }

        function markTeamStockItemAsPacked(transferId, index, title) {
            const select = document.getElementById(`ts_packed_status_${transferId}_${index}`);
            const badge = document.getElementById(`ts_status_badge_${transferId}_${index}`);
            const row = document.getElementById(`ts_row_${transferId}_${index}`);
            const qtyInput = document.getElementById(`ts_packed_qty_${transferId}_${index}`);
            const qtyToPackEl = document.getElementById(`ts_qty_to_pack_${transferId}_${index}`);

            const maxQty = parseFloat(qtyInput && qtyInput.max !== '' ? qtyInput.max : (qtyToPackEl ? qtyToPackEl.textContent : 0)) || 0;
            if (maxQty <= 0) {
                if (qtyInput) qtyInput.value = 0;
                if (select) select.value = 'Not Packed';
                if (row) row.style.backgroundColor = '#f8d7da';
                updateTeamStockPackingProgress(transferId);
                return;
            }

            if (select) select.value = 'Packed';
            if (badge) badge.className = 'd-none bg-success';
            if (row) row.style.backgroundColor = '#d4edda';

            if (qtyInput) {
                qtyInput.value = maxQty;
            }

            updateTeamStockPackingProgress(transferId);
        }

        function toggleTeamStockItemPack(transferId, index) {
            markTeamStockItemAsPacked(transferId, index);
        }

        function markAllTeamStockItemsPacked(transferId) {
            const rows = document.querySelectorAll(`#teamStockPackModal${transferId} .ts-item-row`);
            rows.forEach(row => {
                const index = row.getAttribute('data-index');
                markTeamStockItemAsPacked(transferId, index);
            });
        }

        function updateTeamStockPackingProgress(transferId) {
            const rows = document.querySelectorAll(`#teamStockPackModal${transferId} .ts-item-row`);
            let packedCount = 0;
            const totalCount = rows.length;

            rows.forEach(row => {
                const index = row.getAttribute('data-index');
                const select = document.getElementById(`ts_packed_status_${transferId}_${index}`);
                const badge = document.getElementById(`ts_status_badge_${transferId}_${index}`);
                if ((select && select.value === 'Packed') || (badge && badge.classList.contains('bg-success'))) {
                    packedCount++;
                }
            });

            const itemsPackedInput = document.getElementById(`ts_items_packed_${transferId}`);
            if (itemsPackedInput) {
                itemsPackedInput.value = packedCount;
            }

            const progressBar = document.getElementById(`ts_progress_bar_${transferId}`);
            if (progressBar) {
                const pct = totalCount > 0 ? Math.round((packedCount / totalCount) * 100) : 0;
                progressBar.style.width = pct + '%';
                progressBar.textContent = pct + '%';
                if (pct === 100) {
                    progressBar.className = 'progress-bar bg-success';
                } else {
                    progressBar.className = 'progress-bar bg-warning text-dark';
                }
            }

            const packingStatusSelect = document.getElementById(`ts_packingStatus_${transferId}`);
            if (packingStatusSelect) {
                if (packedCount === totalCount && totalCount > 0) {
                    packingStatusSelect.value = 'completed';
                } else if (packedCount > 0) {
                    packingStatusSelect.value = 'in_progress';
                }
            }
        }

        // Global keydown listener for hardware barcode scanning when Team Stock Transfer modal is open
        document.addEventListener('keydown', function(e) {
            const activeModal = document.querySelector('.modal.team-stock-modal.show');
            if (!activeModal) return;

            if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
                return;
            }

            if (e.key === 'Enter') {
                if (tsScannerBuffer.trim().length >= 3) {
                    const transferId = activeModal.getAttribute('data-transfer-id');
                    processTeamStockBarcodeScan(transferId, tsScannerBuffer.trim());
                }
                tsScannerBuffer = '';
                clearTimeout(tsScannerTimer);
                return;
            }

            if (e.key.length === 1) {
                tsScannerBuffer += e.key;
                clearTimeout(tsScannerTimer);
                tsScannerTimer = setTimeout(() => {
                    if (tsScannerBuffer.trim().length >= 6) {
                        const transferId = activeModal.getAttribute('data-transfer-id');
                        processTeamStockBarcodeScan(transferId, tsScannerBuffer.trim());
                    }
                    tsScannerBuffer = '';
                }, 300);
            }
        });

        window.deleteEcomOrderAction = function(orderId, soNumber) {
            if (!confirm(`Are you sure you want to delete order ${soNumber} from Packing?\n\nThis will remove the order from the packing queue and return any deducted stock.`)) {
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

            fetch(`{{ url('/production/inventory/packing/delete-order') }}/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    _method: 'DELETE'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('✓ ' + data.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete order.'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Connection error occurred while deleting order.');
            });
        };
    </script>
    @endpush

</x-app-layout>
