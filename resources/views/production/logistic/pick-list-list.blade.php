<x-app-layout :title="'Pick Lists'" :sidebar="'production'">
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
$isAdmin = auth()->check() && (
    auth()->user()->isSuperAdmin() || 
    str_contains(strtolower(auth()->user()->position ?? ''), 'admin') || 
    str_contains(strtolower(auth()->user()->department ?? ''), 'admin')
);
@endphp
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex">
                    <div>
                        <h4 class="fs-24 mb-0 text-black">Pick Lists</h4>
                    </div>
                    <a href="{{ route('production.logistic.pick-list-management') }}" class="btn btn-primary rounded d-flex align-items-center" style="gap: 0.5rem; padding: 0.5rem 1rem; height: 38px; min-height: 38px; line-height: 1.5; box-sizing: border-box; border: none; background: #ff0000; color: #ffffff; font-weight: 500;">
                        <i class="las la-plus" style="font-size: 1rem; line-height: 1; margin: 0; padding: 0; background: transparent; border: none; box-shadow: none;"></i>
                        <span style="font-size: 0.875rem; white-space: nowrap;">Create New Pick List</span>
                    </a>
                </div>
                <div class="card-body">
                    <!-- Main Tabs Navigation -->
                    <ul class="nav nav-tabs mb-3" id="mainTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-pick-lists-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#all-pick-lists" data-target="#all-pick-lists" type="button" role="tab" aria-controls="all-pick-lists" aria-selected="true">
                                All Pick Lists
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ecom-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#ecom-direct" data-target="#ecom-direct" type="button" role="tab" aria-controls="ecom-direct" aria-selected="false">
                                E-Commerce Direct <span class="badge bg-info ms-2">{{ $ecomByPlatform['lazada']->count() + $ecomByPlatform['shopee']->count() + $ecomByPlatform['tiktok']->count() + ($ecomByPlatform['cob']?->count() ?? 0) }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="complimentary-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#complimentary-pick-lists" data-target="#complimentary-pick-lists" type="button" role="tab" aria-controls="complimentary-pick-lists" aria-selected="false">
                                Complimentary <span class="badge ms-2" style="background-color: #6f42c1; color: #fff;">{{ $complimentaryPickLists->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="team-stocks-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#team-stocks-pick-lists" data-target="#team-stocks-pick-lists" type="button" role="tab" aria-controls="team-stocks-pick-lists" aria-selected="false">
                                Team Stock Transfers <span class="badge bg-danger ms-2">{{ isset($teamStockPickLists) ? $teamStockPickLists->count() : 0 }}</span>
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="mainTabsContent">
                        <!-- All Pick Lists Tab -->
                        <div class="tab-pane fade show active" id="all-pick-lists" role="tabpanel" aria-labelledby="all-pick-lists-tab">
                            <div class="table-responsive">
                                <table id="pickListsTable" class="display" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>Pick List Number</th>
                                            <th>Sales Order</th>
                                            <th>Transaction Type</th>
                                            <th>Customer</th>
                                            <th>Date Created</th>
                                            <th>Total Items</th>
                                            <th>Items Picked</th>
                                            <th>Status</th>
                                            <th>Prepared By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pickLists as $pickList)
                                        <tr>
                                            <td><strong>{{ $pickList->pick_list_number }}</strong></td>
                                            <td>
                                                {{ $pickList->salesOrder->so_number ?? 'N/A' }}
                                                @php
                                                    $isEcom = ($pickList->salesOrder?->type === 'ecom_direct' || !empty($pickList->salesOrder?->ecom_platform) || str_contains(strtolower($pickList->salesOrder?->so_number ?? ''), 'ecom') || !empty($pickList->salesOrder?->platform_order_id));
                                                    $ecomId = $pickList->salesOrder?->platform_order_id ?: ($pickList->salesOrder?->ref_number ?: ($pickList->salesOrder?->po_number ?? null));
                                                @endphp
                                                @if($isEcom || !empty($ecomId))
                                                    <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                                @endif
                                                @if($pickList->salesOrder?->cancellation_date)
                                                    <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="las la-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($pickList->salesOrder->cancellation_date)->format('M d, Y') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php $soType = $pickList->salesOrder?->type ?: ($pickList->salesOrder?->transaction_type ?? ''); @endphp
                                                <span class="badge bg-light text-dark border">{{ $txnTypeLabels[$soType] ?? (ucwords(str_replace('_', ' ', $soType)) ?: 'N/A') }}</span>
                                            </td>
                                            <td>{{ $pickList->salesOrder->customer->customer_name ?? 'Unknown' }}</td>
                                            <td>{{ $pickList->created_at->format('Y-m-d') }}</td>
                                            <td>{{ $pickList->pickListItems->sum('requested_qty') }}</td>
                                            <td>{{ $pickList->pickListItems->sum('picked_qty') }}</td>
                                            <td>
                                                @if($pickList->status === 'draft')
                                                    <span class="badge bg-secondary">Draft</span>
                                                @elseif($pickList->status === 'in_progress')
                                                    <span class="badge" style="background-color: #0dcaf0;">Picking</span>
                                                @elseif($pickList->status === 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @endif
                                            </td>
                                            <td>{{ $pickList->preparedByUser->name ?? 'System' }}</td>
                                            <td>
                                                <div class="workflow-actions">
                                                    <a href="{{ route('production.logistic.pick-list-details', $pickList->id) }}" class="btn btn-danger shadow btn-xs sharp me-1" title="View Details">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                    @if(auth()->check() && auth()->user()->isSuperAdmin())
                                                         @php
                                                             $isFordSO = $pickList->salesOrder && ($pickList->salesOrder->type === 'foreign' || str_starts_with($pickList->salesOrder->so_number, 'FORD-SO-'));
                                                             $editUrl = $isFordSO 
                                                                 ? route('production.sales-order.review', $pickList->salesOrder->id) 
                                                                 : ($pickList->salesOrder ? route('marketing.sales-orders.edit', $pickList->salesOrder->id) : route('production.logistic.pick-list-management') . '?id=' . $pickList->id);
                                                         @endphp
                                                         <a href="{{ $editUrl }}" class="btn btn-warning shadow btn-xs sharp me-1" title="Edit / Review Order">
                                                             <i class="fas fa-pencil-alt"></i>
                                                         </a>
                                                         <a href="{{ route('production.logistic.pick-list-delete', $pickList->id) }}" class="btn btn-danger shadow btn-xs sharp me-1" title="Delete Pick List" onclick="return confirm('Are you sure you want to delete this Pick List?');">
                                                             <i class="fas fa-trash"></i>
                                                         </a>
                                                     @endif
                                                    <a href="{{ route('production.logistic.shipping-label', $pickList->salesOrder?->id ?? $pickList->id) }}" target="_blank" class="btn btn-primary shadow btn-xs sharp me-1" title="Shipping Label">
                                                        <i class="las la-tag"></i>
                                                    </a>
                                                    <form action="{{ route('production.logistic.mark-as-gathered', $pickList->salesOrder->id ?? 0) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark as gathered?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Mark as Gathered">
                                                            <i class="las la-check"></i>
                                                        </button>
                                                    </form>
                                                    <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp" title="Print" onclick="window.print();">
                                                        <i class="las la-print"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="10" class="text-center">No active pick lists.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- E-Commerce Direct Tab -->
                        <div class="tab-pane fade" id="ecom-direct" role="tabpanel" aria-labelledby="ecom-tab">
                            <!-- Sub-tabs for Platforms -->
                            <ul class="nav nav-tabs mb-3" id="ecomTabs" role="tablist" style="border-bottom: 2px solid #dee2e6;">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="lazada-tab" data-bs-toggle="tab" data-bs-target="#lazada-content" type="button" role="tab" aria-controls="lazada-content" aria-selected="true">
                                        <i class="las la-shopping-bag me-2"></i>Lazada <span class="badge bg-primary ms-2">{{ $ecomByPlatform['lazada']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="shopee-tab" data-bs-toggle="tab" data-bs-target="#shopee-content" type="button" role="tab" aria-controls="shopee-content" aria-selected="false">
                                        <i class="las la-shopping-bag me-2"></i>Shopee <span class="badge bg-danger ms-2">{{ $ecomByPlatform['shopee']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tiktok-tab" data-bs-toggle="tab" data-bs-target="#tiktok-content" type="button" role="tab" aria-controls="tiktok-content" aria-selected="false">
                                        <i class="las la-music me-2"></i>TikTok <span class="badge bg-dark ms-2">{{ $ecomByPlatform['tiktok']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="cob-tab" data-bs-toggle="tab" data-bs-target="#cob-content" type="button" role="tab" aria-controls="cob-content" aria-selected="false">
                                        <i class="las la-building me-2"></i>COB <span class="badge ms-2" style="background-color: #6f42c1; color: #fff;">{{ $ecomByPlatform['cob']->count() }}</span>
                                    </button>
                                </li>
                            </ul>

                            <!-- Sub-tabs Content -->
                            <div class="tab-content" id="ecomTabsContent">
                                <!-- Lazada Tab -->
                                <div class="tab-pane fade show active" id="lazada-content" role="tabpanel" aria-labelledby="lazada-tab">
                                    <div class="table-responsive">
                                        <table id="lazadaTable" class="display ecom-table" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>Pick List Number</th>
                                                    <th>Sales Order</th>
                                                    <th>Transaction Type</th>
                                                    <th>Customer</th>
                                                    <th>Date Created</th>
                                                    <th>Total Items</th>
                                                    <th>Items Picked</th>
                                                    <th>Status</th>
                                                    <th>Prepared By</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($ecomByPlatform['lazada'] as $pickList)
                                                <tr>
                                                    <td><strong>{{ $pickList->pick_list_number }}</strong></td>
                                                    <td>
                                                {{ $pickList->salesOrder->so_number ?? 'N/A' }}
                                                @php
                                                    $isEcom = ($pickList->salesOrder?->type === 'ecom_direct' || !empty($pickList->salesOrder?->ecom_platform) || str_contains(strtolower($pickList->salesOrder?->so_number ?? ''), 'ecom') || !empty($pickList->salesOrder?->platform_order_id));
                                                    $ecomId = $pickList->salesOrder?->platform_order_id ?: ($pickList->salesOrder?->ref_number ?: ($pickList->salesOrder?->po_number ?? null));
                                                @endphp
                                                @if($isEcom || !empty($ecomId))
                                                    <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                                @endif
                                                @if($pickList->salesOrder?->cancellation_date)
                                                    <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="las la-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($pickList->salesOrder->cancellation_date)->format('M d, Y') }}</span>
                                                @endif
                                            </td>
                                                    <td>
                                                        @php $soType = $pickList->salesOrder?->type ?: ($pickList->salesOrder?->transaction_type ?? ''); @endphp
                                                <span class="badge bg-light text-dark border">{{ $txnTypeLabels[$soType] ?? (ucwords(str_replace('_', ' ', $soType)) ?: 'N/A') }}</span>
                                                    </td>
                                                    <td>{{ $pickList->salesOrder->customer->customer_name ?? 'Unknown' }}</td>
                                                    <td>{{ $pickList->created_at->format('Y-m-d') }}</td>
                                                    <td>{{ $pickList->pickListItems->sum('requested_qty') }}</td>
                                                    <td>{{ $pickList->pickListItems->sum('picked_qty') }}</td>
                                                    <td>
                                                        @if($pickList->status === 'draft')
                                                            <span class="badge bg-secondary">Draft</span>
                                                        @elseif($pickList->status === 'in_progress')
                                                            <span class="badge" style="background-color: #0dcaf0;">Picking</span>
                                                        @elseif($pickList->status === 'completed')
                                                            <span class="badge bg-success">Completed</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $pickList->preparedByUser->name ?? 'System' }}</td>
                                                    <td>
                                                        <div class="workflow-actions">
                                                            <a href="{{ route('production.logistic.pick-list-details', $pickList->id) }}" class="btn btn-danger shadow btn-xs sharp me-1" title="View Details">
                                                                <i class="las la-eye"></i>
                                                            </a>
                                                            <a href="{{ route('production.logistic.shipping-label', $pickList->salesOrder?->id ?? $pickList->id) }}" target="_blank" class="btn btn-primary shadow btn-xs sharp me-1" title="Shipping Label">
                                                                <i class="las la-tag"></i>
                                                            </a>
                                                            <form action="{{ route('production.logistic.mark-as-gathered', $pickList->salesOrder->id ?? 0) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark as gathered?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Mark as Gathered">
                                                                    <i class="las la-check"></i>
                                                                </button>
                                                            </form>
                                                            <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp" title="Print" onclick="window.print();">
                                                                <i class="las la-print"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="10" class="text-center">No Lazada pick lists.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Shopee Tab -->
                                <div class="tab-pane fade" id="shopee-content" role="tabpanel" aria-labelledby="shopee-tab">
                                    <div class="table-responsive">
                                        <table id="shopeeTable" class="display ecom-table" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>Pick List Number</th>
                                                    <th>Sales Order</th>
                                                    <th>Transaction Type</th>
                                                    <th>Customer</th>
                                                    <th>Date Created</th>
                                                    <th>Total Items</th>
                                                    <th>Items Picked</th>
                                                    <th>Status</th>
                                                    <th>Prepared By</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($ecomByPlatform['shopee'] as $pickList)
                                                <tr>
                                                    <td><strong>{{ $pickList->pick_list_number }}</strong></td>
                                                    <td>
                                                {{ $pickList->salesOrder->so_number ?? 'N/A' }}
                                                @php
                                                    $isEcom = ($pickList->salesOrder?->type === 'ecom_direct' || !empty($pickList->salesOrder?->ecom_platform) || str_contains(strtolower($pickList->salesOrder?->so_number ?? ''), 'ecom') || !empty($pickList->salesOrder?->platform_order_id));
                                                    $ecomId = $pickList->salesOrder?->platform_order_id ?: ($pickList->salesOrder?->ref_number ?: ($pickList->salesOrder?->po_number ?? null));
                                                @endphp
                                                @if($isEcom || !empty($ecomId))
                                                    <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                                @endif
                                                @if($pickList->salesOrder?->cancellation_date)
                                                    <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="las la-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($pickList->salesOrder->cancellation_date)->format('M d, Y') }}</span>
                                                @endif
                                            </td>
                                                    <td>
                                                        @php $soType = $pickList->salesOrder?->type ?: ($pickList->salesOrder?->transaction_type ?? ''); @endphp
                                                <span class="badge bg-light text-dark border">{{ $txnTypeLabels[$soType] ?? (ucwords(str_replace('_', ' ', $soType)) ?: 'N/A') }}</span>
                                                    </td>
                                                    <td>{{ $pickList->salesOrder->customer->customer_name ?? 'Unknown' }}</td>
                                                    <td>{{ $pickList->created_at->format('Y-m-d') }}</td>
                                                    <td>{{ $pickList->pickListItems->sum('requested_qty') }}</td>
                                                    <td>{{ $pickList->pickListItems->sum('picked_qty') }}</td>
                                                    <td>
                                                        @if($pickList->status === 'draft')
                                                            <span class="badge bg-secondary">Draft</span>
                                                        @elseif($pickList->status === 'in_progress')
                                                            <span class="badge" style="background-color: #0dcaf0;">Picking</span>
                                                        @elseif($pickList->status === 'completed')
                                                            <span class="badge bg-success">Completed</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $pickList->preparedByUser->name ?? 'System' }}</td>
                                                    <td>
                                                        <div class="workflow-actions">
                                                            <a href="{{ route('production.logistic.pick-list-details', $pickList->id) }}" class="btn btn-danger shadow btn-xs sharp me-1" title="View Details">
                                                                <i class="las la-eye"></i>
                                                            </a>
                                                            <a href="{{ route('production.logistic.shipping-label', $pickList->salesOrder?->id ?? $pickList->id) }}" target="_blank" class="btn btn-primary shadow btn-xs sharp me-1" title="Shipping Label">
                                                                <i class="las la-tag"></i>
                                                            </a>
                                                            <form action="{{ route('production.logistic.mark-as-gathered', $pickList->salesOrder->id ?? 0) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark as gathered?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Mark as Gathered">
                                                                    <i class="las la-check"></i>
                                                                </button>
                                                            </form>
                                                            <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp" title="Print" onclick="window.print();">
                                                                <i class="las la-print"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="10" class="text-center">No Shopee pick lists.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                 <!-- TikTok Tab -->
                                <div class="tab-pane fade" id="tiktok-content" role="tabpanel" aria-labelledby="tiktok-tab">
                                    <div class="table-responsive">
                                        <table id="tiktokTable" class="display ecom-table" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>Pick List Number</th>
                                                    <th>Sales Order</th>
                                                    <th>Transaction Type</th>
                                                    <th>Customer</th>
                                                    <th>Date Created</th>
                                                    <th>Total Items</th>
                                                    <th>Items Picked</th>
                                                    <th>Status</th>
                                                    <th>Prepared By</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($ecomByPlatform['tiktok'] as $pickList)
                                                <tr>
                                                    <td><strong>{{ $pickList->pick_list_number }}</strong></td>
                                                    <td>
                                                {{ $pickList->salesOrder->so_number ?? 'N/A' }}
                                                @php
                                                    $isEcom = ($pickList->salesOrder?->type === 'ecom_direct' || !empty($pickList->salesOrder?->ecom_platform) || str_contains(strtolower($pickList->salesOrder?->so_number ?? ''), 'ecom') || !empty($pickList->salesOrder?->platform_order_id));
                                                    $ecomId = $pickList->salesOrder?->platform_order_id ?: ($pickList->salesOrder?->ref_number ?: ($pickList->salesOrder?->po_number ?? null));
                                                @endphp
                                                @if($isEcom || !empty($ecomId))
                                                    <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                                @endif
                                                @if($pickList->salesOrder?->cancellation_date)
                                                    <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="las la-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($pickList->salesOrder->cancellation_date)->format('M d, Y') }}</span>
                                                @endif
                                            </td>
                                                    <td>
                                                        @php $soType = $pickList->salesOrder?->type ?: ($pickList->salesOrder?->transaction_type ?? ''); @endphp
                                                <span class="badge bg-light text-dark border">{{ $txnTypeLabels[$soType] ?? (ucwords(str_replace('_', ' ', $soType)) ?: 'N/A') }}</span>
                                                    </td>
                                                    <td>{{ $pickList->salesOrder->customer->customer_name ?? 'Unknown' }}</td>
                                                    <td>{{ $pickList->created_at->format('Y-m-d') }}</td>
                                                    <td>{{ $pickList->pickListItems->sum('requested_qty') }}</td>
                                                    <td>{{ $pickList->pickListItems->sum('picked_qty') }}</td>
                                                    <td>
                                                        @if($pickList->status === 'draft')
                                                            <span class="badge bg-secondary">Draft</span>
                                                        @elseif($pickList->status === 'in_progress')
                                                            <span class="badge" style="background-color: #0dcaf0;">Picking</span>
                                                        @elseif($pickList->status === 'completed')
                                                            <span class="badge bg-success">Completed</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $pickList->preparedByUser->name ?? 'System' }}</td>
                                                    <td>
                                                        <div class="workflow-actions">
                                                            <a href="{{ route('production.logistic.pick-list-details', $pickList->id) }}" class="btn btn-danger shadow btn-xs sharp me-1" title="View Details">
                                                                <i class="las la-eye"></i>
                                                            </a>
                                                            <a href="{{ route('production.logistic.shipping-label', $pickList->salesOrder?->id ?? $pickList->id) }}" target="_blank" class="btn btn-primary shadow btn-xs sharp me-1" title="Shipping Label">
                                                                <i class="las la-tag"></i>
                                                            </a>
                                                            <form action="{{ route('production.logistic.mark-as-gathered', $pickList->salesOrder->id ?? 0) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark as gathered?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Mark as Gathered">
                                                                    <i class="las la-check"></i>
                                                                </button>
                                                            </form>
                                                            <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp" title="Print" onclick="window.print();">
                                                                <i class="las la-print"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="10" class="text-center">No TikTok pick lists.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- COB Tab -->
                                <div class="tab-pane fade" id="cob-content" role="tabpanel" aria-labelledby="cob-tab">
                                    <div class="table-responsive">
                                        <table id="cobTable" class="display ecom-table" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>Pick List Number</th>
                                                    <th>Sales Order</th>
                                                    <th>Transaction Type</th>
                                                    <th>Customer</th>
                                                    <th>Date Created</th>
                                                    <th>Total Items</th>
                                                    <th>Items Picked</th>
                                                    <th>Status</th>
                                                    <th>Prepared By</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($ecomByPlatform['cob'] as $pickList)
                                                <tr>
                                                    <td><strong>{{ $pickList->pick_list_number }}</strong></td>
                                                    <td>
                                                {{ $pickList->salesOrder->so_number ?? 'N/A' }}
                                                @php
                                                    $isEcom = ($pickList->salesOrder?->type === 'ecom_direct' || !empty($pickList->salesOrder?->ecom_platform) || str_contains(strtolower($pickList->salesOrder?->so_number ?? ''), 'ecom') || !empty($pickList->salesOrder?->platform_order_id));
                                                    $ecomId = $pickList->salesOrder?->platform_order_id ?: ($pickList->salesOrder?->ref_number ?: ($pickList->salesOrder?->po_number ?? null));
                                                @endphp
                                                @if($isEcom || !empty($ecomId))
                                                    <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                                @endif
                                                @if($pickList->salesOrder?->cancellation_date)
                                                    <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="las la-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($pickList->salesOrder->cancellation_date)->format('M d, Y') }}</span>
                                                @endif
                                            </td>
                                                    <td>
                                                        @php $soType = $pickList->salesOrder?->type ?: ($pickList->salesOrder?->transaction_type ?? ''); @endphp
                                                <span class="badge bg-light text-dark border">{{ $txnTypeLabels[$soType] ?? (ucwords(str_replace('_', ' ', $soType)) ?: 'N/A') }}</span>
                                                    </td>
                                                    <td>{{ $pickList->salesOrder->customer->customer_name ?? 'Unknown' }}</td>
                                                    <td>{{ $pickList->created_at->format('Y-m-d') }}</td>
                                                    <td>{{ $pickList->pickListItems->sum('requested_qty') }}</td>
                                                    <td>{{ $pickList->pickListItems->sum('picked_qty') }}</td>
                                                    <td>
                                                        @if($pickList->status === 'draft')
                                                            <span class="badge bg-secondary">Draft</span>
                                                        @elseif($pickList->status === 'in_progress')
                                                            <span class="badge" style="background-color: #0dcaf0;">Picking</span>
                                                        @elseif($pickList->status === 'completed')
                                                            <span class="badge bg-success">Completed</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $pickList->preparedByUser->name ?? 'System' }}</td>
                                                    <td>
                                                        <div class="workflow-actions">
                                                            <a href="{{ route('production.logistic.pick-list-details', $pickList->id) }}" class="btn btn-danger shadow btn-xs sharp me-1" title="View Details">
                                                                <i class="las la-eye"></i>
                                                            </a>
                                                            <a href="{{ route('production.logistic.shipping-label', $pickList->salesOrder?->id ?? $pickList->id) }}" target="_blank" class="btn btn-primary shadow btn-xs sharp me-1" title="Shipping Label">
                                                                <i class="las la-tag"></i>
                                                            </a>
                                                            <form action="{{ route('production.logistic.mark-as-gathered', $pickList->salesOrder->id ?? 0) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark as gathered?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Mark as Gathered">
                                                                    <i class="las la-check"></i>
                                                                </button>
                                                            </form>
                                                            <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp" title="Print" onclick="window.print();">
                                                                <i class="las la-print"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="10" class="text-center">No COB pick lists.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Complimentary Pick Lists Tab -->
                        <div class="tab-pane fade" id="complimentary-pick-lists" role="tabpanel" aria-labelledby="complimentary-tab">
                            <div class="table-responsive">
                                <table id="complimentaryPickListsTable" class="display" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>Pick List Number</th>
                                            <th>Sales Order</th>
                                            <th>Transaction Type</th>
                                            <th>Recipient / Customer</th>
                                            <th>Date Created</th>
                                            <th>Total Items</th>
                                            <th>Items Picked</th>
                                            <th>Status</th>
                                            <th>Prepared By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($complimentaryPickLists as $pickList)
                                        <tr>
                                            <td><strong>{{ $pickList->pick_list_number }}</strong></td>
                                            <td>
                                                {{ $pickList->salesOrder->so_number ?? 'N/A' }}
                                                @php
                                                    $isEcom = ($pickList->salesOrder?->type === 'ecom_direct' || !empty($pickList->salesOrder?->ecom_platform) || str_contains(strtolower($pickList->salesOrder?->so_number ?? ''), 'ecom') || !empty($pickList->salesOrder?->platform_order_id));
                                                    $ecomId = $pickList->salesOrder?->platform_order_id ?: ($pickList->salesOrder?->ref_number ?: ($pickList->salesOrder?->po_number ?? null));
                                                @endphp
                                                @if($isEcom || !empty($ecomId))
                                                    <br><span class="badge bg-info text-white mt-1" style="font-size: 0.75rem;"><i class="las la-shopping-bag me-1"></i>Platform ID: {{ $ecomId ?: 'N/A' }}</span>
                                                @endif
                                                @if($pickList->salesOrder?->cancellation_date)
                                                    <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="las la-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($pickList->salesOrder->cancellation_date)->format('M d, Y') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php $soType = $pickList->salesOrder?->type ?: ($pickList->salesOrder?->transaction_type ?? ''); @endphp
                                                <span class="badge bg-light text-dark border">{{ $txnTypeLabels[$soType] ?? (ucwords(str_replace('_', ' ', $soType)) ?: 'N/A') }}</span>
                                            </td>
                                            <td>{{ $pickList->salesOrder->customer->customer_name ?? 'Unknown' }}</td>
                                            <td>{{ $pickList->created_at->format('Y-m-d') }}</td>
                                            <td>{{ $pickList->pickListItems->sum('requested_qty') }}</td>
                                            <td>{{ $pickList->pickListItems->sum('picked_qty') }}</td>
                                            <td>
                                                @if($pickList->status === 'draft')
                                                    <span class="badge bg-secondary">Draft</span>
                                                @elseif($pickList->status === 'in_progress')
                                                    <span class="badge" style="background-color: #6f42c1; color: #fff;">Picking (Complimentary)</span>
                                                @elseif($pickList->status === 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @endif
                                            </td>
                                            <td>{{ $pickList->preparedByUser->name ?? 'System' }}</td>
                                            <td>
                                                <div class="workflow-actions">
                                                    <a href="{{ route('production.logistic.pick-list-details', $pickList->id) }}" class="btn btn-danger shadow btn-xs sharp me-1" title="View Details">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                    <a href="{{ route('production.logistic.shipping-label', $pickList->salesOrder?->id ?? $pickList->id) }}" target="_blank" class="btn btn-primary shadow btn-xs sharp me-1" title="Shipping Label">
                                                        <i class="las la-tag"></i>
                                                    </a>
                                                    <form action="{{ route('production.logistic.mark-as-gathered', $pickList->salesOrder->id ?? 0) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark as gathered and send to Acknowledgement Receipt preparation?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Mark as Gathered">
                                                            <i class="las la-check"></i>
                                                        </button>
                                                    </form>
                                                    <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp" title="Print" onclick="window.print();">
                                                        <i class="las la-print"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4 text-muted">No active complimentary pick lists.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Team Stock Transfers Tab -->
                        <div class="tab-pane fade" id="team-stocks-pick-lists" role="tabpanel" aria-labelledby="team-stocks-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0" style="width: 100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Transfer #</th>
                                            <th>Target Team</th>
                                            <th>Transferred By</th>
                                            <th class="text-center">Items Count</th>
                                            <th class="text-center">Total Pcs</th>
                                            <th>Date Created</th>
                                            <th>Remarks</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($teamStockPickLists ?? [] as $tt)
                                        <tr>
                                            <td class="fw-bold">{{ $tt->transfer_number }}</td>
                                            <td><span class="badge bg-danger">{{ $tt->team_name }}</span></td>
                                                    <td>{{ $tt->transferredByUser->name ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $tt->items->count() }} item(s)</td>
                                            <td class="text-center fw-bold text-success">{{ number_format($tt->items->sum('quantity')) }} pcs</td>
                                            <td>{{ $tt->created_at->format('M d, Y h:i A') }}</td>
                                            <td><span class="text-dark fw-semibold">{{ $tt->notes ?: '—' }}</span></td>
                                            <td><span class="badge bg-warning text-dark">{{ ucwords(str_replace('_', ' ', $tt->status)) }}</span></td>
                                            <td class="text-end" style="white-space: nowrap;">
                                                <div class="d-flex align-items-center justify-content-end gap-1">
                                                    <button type="button" class="btn btn-xs btn-outline-danger" data-bs-toggle="modal" data-bs-target="#teamStockPickModal{{ $tt->id }}">
                                                        <i class="las la-eye me-1"></i>View Items
                                                    </button>
                                                  <!--  <form action="{{ route('production.logistic.team-stock-transfer.complete-pick', $tt->id) }}" method="POST" class="d-inline m-0">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs btn-success fw-bold" style="background-color: #28a745; border: none;">
                                                            <i class="las la-check me-1"></i>Complete Pick
                                                        </button>
                                                    </form>-->
                                                  
                                                  <button type="submit"
        form="tsp_form_{{ $tt->id }}"
        formaction="{{ route('production.logistic.team-stock-transfer.complete-pick', $tt->id) }}"
        formmethod="POST"
        class="btn btn-xs btn-success fw-bold"
        style="background-color: #28a745; border: none;"
        onclick="return confirm('Complete this pick and transfer the picked quantities to Packing?');">
    <i class="las la-check me-1"></i>Complete Pick
</button>
                                                  
                                                  
                                                    @if(auth()->check() && auth()->user()->isSuperAdmin())
                                                        <form action="{{ route('production.logistic.team-stock-transfer.delete', $tt->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Are you sure you want to delete Team Stock Transfer {{ $tt->transfer_number }}?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-xs btn-danger fw-bold" style="background-color: #dc3545; border: none;" title="Delete Transfer">
                                                                <i class="fas fa-trash me-1"></i>Delete
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4 text-muted">No pending team stock transfers ready for picking.</td>
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
    @foreach($teamStockPickLists ?? [] as $tt)
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
            return $price * $item->quantity;
        });
    @endphp
    <div class="modal fade team-stock-pick-modal" id="teamStockPickModal{{ $tt->id }}" tabindex="-1" aria-hidden="true" data-transfer-id="{{ $tt->id }}">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header px-4 py-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fw-bold text-dark mb-0">Pick List Details - {{ $tt->transfer_number }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="max-height: 80vh; overflow-y: auto;">
                    <form id="tsp_form_{{ $tt->id }}" action="{{ route('production.logistic.team-stock-transfer.save-pick-items', $tt->id) }}" method="POST">
                        @csrf
                    
                    <!-- Top Information Grid (2 Columns) -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border">
                                <h6 class="fw-bold text-dark mb-3">Order / Transfer Information</h6>
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
                                    <textarea name="notes" id="tsp_remarks_{{ $tt->id }}" class="form-control form-control-sm bg-white fw-semibold mb-1" rows="2" placeholder="Enter remarks or special instructions...">{{ $tt->notes }}</textarea>
                                    <button type="submit" class="btn btn-sm btn-primary fw-bold"><i class="las la-save me-1"></i>Save Remarks</button>
                                </div>
                                <div class="mb-0">
                                    <label class="fw-semibold small text-muted d-block mb-1">Picking Status:</label>
                                    <select name="status" id="tsp_status_{{ $tt->id }}" class="form-select form-select-sm fw-bold">
                                        <option value="pending_picklist" {{ $tt->status === 'pending_picklist' ? 'selected' : '' }}>Pending Picklist</option>
                                        <option value="picking" {{ $tt->status === 'picking' ? 'selected' : '' }}>Picking</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold text-dark mb-3">Barcode Scanning & Quick Action</h6>
                                    <label class="form-label fw-bold text-dark mb-1"><i class="las la-barcode text-danger me-1"></i>Scan Book Barcode / ISBN:</label>
                                    <div class="input-group input-group-sm mb-3">
                                        <span class="input-group-text bg-danger text-white"><i class="las la-search"></i></span>
                                        <input type="text" id="tsp_barcode_input_{{ $tt->id }}" class="form-control form-control-sm fw-bold tsp-barcode-input-field" placeholder="Scan or type ISBN/barcode and press Enter..." data-transfer-id="{{ $tt->id }}">
                                        <button type="button" class="btn btn-danger btn-sm fw-bold" onclick="onTSPManualScanClick({{ $tt->id }})">Scan</button>
                                    </div>
                                    <div id="tsp_scan_feedback_{{ $tt->id }}" class="p-2 rounded text-center small fw-bold bg-success text-white border mb-3">
                                        Ready to scan
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-success btn-sm w-100 fw-bold py-2" onclick="markAllTSPItemsPicked({{ $tt->id }})">
                                        <i class="las la-check-double me-1"></i>Pick All Items Quickly
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items to Pick Section -->
                    <h6 class="fw-bold text-dark mb-2">Items to Pick from Main Warehouse</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle mb-0" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>PRODUCT / ITEM TITLE</th>
                                    <th style="width: 110px;" class="text-center">QTY TO PICK</th>
                                    <th style="width: 110px;" class="text-end">UNIT PRICE</th>
                                    <th style="width: 110px;" class="text-end">SUBTOTAL</th>
                                    <th style="width: 100px;" class="text-center">PICKED QTY</th>
                                    <th style="width: 120px;" class="text-center">STATUS</th>
                                    <th style="width: 150px;">NOTES</th>
                                    <th style="width: 130px;" class="text-center">PICKED DATE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tt->items as $idx => $tItem)
                                @php
                                    $itemName = $tItem->bookIndex ? $tItem->bookIndex->display_name : ($tItem->book ? $tItem->book->name : ($tItem->bookBundle ? $tItem->bookBundle->name : 'N/A'));
                                    $tItemType = $tItem->item_type ?? ($tItem->bookIndex ? 'Index' : ($tItem->bookBundle ? 'Bundle' : 'Book'));
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

                                    $itemSubtotal = $unitPrice * $tItem->quantity;
                                    $uniqueBarcodes = array_values(array_unique(array_filter($barcodes)));
                                    $barcodesJson = json_encode($uniqueBarcodes);
                                    $isItemPicked = ($tItem->status === 'Picked' || ($tItem->picked_qty !== null && (float)$tItem->picked_qty >= (float)$tItem->quantity && (float)$tItem->quantity > 0));
                                  	
                              
            		$itemPickedQty = $tItem->picked_qty !== null ? (float) $tItem->picked_qty : 0;
                                
 
                              
                              @endphp
                                <tr id="tsp_row_{{ $tt->id }}_{{ $idx }}" class="tsp-item-row" data-transfer-id="{{ $tt->id }}" data-index="{{ $idx }}" data-barcodes="{{ $barcodesJson }}" data-title="{{ e($itemName) }}" style="background: {{ $isItemPicked ? '#d4edda' : ($tItem->status === 'Picking' ? '#fff3cd' : '#f8d7da') }};">
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
                                            <small class="text-muted d-block"><i class="las la-barcode me-1"></i>{{ implode(', ', $uniqueBarcodes) }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold text-primary">{{ number_format($tItem->quantity, 2) }}</td>
                                    <td class="text-end">₱{{ number_format($unitPrice, 2) }}</td>
                                    <td class="text-end fw-bold">₱{{ number_format($itemSubtotal, 2) }}</td>
                                    <td class="text-center">
                                        <input type="number" name="items[{{ $idx }}][picked_qty]" id="tsp_picked_qty_{{ $tt->id }}_{{ $idx }}" min="0" max="{{ $tItem->quantity }}" value="{{ $itemPickedQty }}" onchange="onTSPPickedQtyChange({{ $tt->id }}, {{ $idx }})" style="width: 60px; padding: 2px 4px; text-align: center; border: 1px solid #ccc; border-radius: 4px; font-weight: 600;">
                                    </td>
                                    <td class="text-center">
                                        <select name="items[{{ $idx }}][status]" id="tsp_item_status_{{ $tt->id }}_{{ $idx }}" class="tsp-status-select" onchange="onTSPStatusSelectChange({{ $tt->id }}, {{ $idx }})" style="padding: 2px 4px; border: 1px solid #ccc; border-radius: 4px; font-weight: 600;">
                                            <option value="Pending" {{ ($tItem->status ?? 'Pending') === 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Picking" {{ ($tItem->status ?? '') === 'Picking' ? 'selected' : '' }}>Picking</option>
                                            <option value="Picked" {{ ($tItem->status ?? '') === 'Picked' ? 'selected' : '' }}>Picked</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="items[{{ $idx }}][notes]" id="tsp_notes_{{ $tt->id }}_{{ $idx }}" value="{{ $tItem->notes ?? '' }}" placeholder="Add notes..." style="width: 100%; padding: 2px 4px; border: 1px solid #ccc; border-radius: 4px; font-size: 0.82rem;">
                                    </td>
                                    <td class="text-center">
                                        <input type="date" name="items[{{ $idx }}][picked_date]" id="tsp_date_{{ $tt->id }}_{{ $idx }}" value="{{ $tItem->picked_date ? \Carbon\Carbon::parse($tItem->picked_date)->format('Y-m-d') : date('Y-m-d') }}" style="padding: 2px 4px; border: 1px solid #ccc; border-radius: 4px; font-size: 0.82rem;">
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
                                <h6 class="fw-bold text-dark mb-3">Picking Summary</h6>
                                <div class="mb-2">
                                    <label class="fw-semibold small text-muted d-block mb-1">Total Items:</label>
                                    <input type="text" class="form-control form-control-sm bg-white" value="{{ $tt->items->count() }}" readonly>
                                </div>
                                <div class="mb-2">
                                    <label class="fw-semibold small text-muted d-block mb-1">Items Picked:</label>
                                    <input type="text" id="tsp_items_picked_{{ $tt->id }}" class="form-control form-control-sm bg-white fw-bold text-success" value="{{ $tt->items->filter(fn($i) => $i->status === 'Picked' || $i->picked_qty >= $i->quantity)->count() }}" readonly>
                                </div>
                                <div class="mb-0">
                                    <label class="fw-semibold small text-muted d-block mb-1">Picking Progress:</label>
                                    @php
                                        $pickedCount = $tt->items->filter(fn($i) => $i->status === 'Picked' || $i->picked_qty >= $i->quantity)->count();
                                        $totalCount = $tt->items->count();
                                        $pct = $totalCount > 0 ? round(($pickedCount / $totalCount) * 100) : 0;
                                    @endphp
                                    <div class="progress" style="height: 18px; border-radius: 9px; background: #e9ecef;">
                                        <div id="tsp_progress_bar_{{ $tt->id }}" class="progress-bar {{ $pct == 100 ? 'bg-success' : 'bg-warning text-dark' }}" style="width: {{ $pct }}%; font-size: 11px; font-weight: bold;">
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
                                        <i class="las la-save me-1"></i>Save Picked Items
                                    </button>

                                    @if($tt->status !== 'completed')
                                    <button type="submit" id="tsp_complete_btn_{{ $tt->id }}" formaction="{{ route('production.logistic.team-stock-transfer.complete-pick', $tt->id) }}" formmethod="POST" class="btn btn-success w-100 fw-bold py-2 shadow-sm" style="background-color: #28a745; border: none;">
                                        <i class="las la-check-circle me-1"></i>Complete Pick & Transfer
                                    </button>
                                    @endif

                                    <button type="button" class="btn btn-secondary w-100 fw-bold py-2" data-bs-dismiss="modal">
                                        <i class="las la-times me-1"></i>Close Details
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
    @endforeach

    <script>
        function normalizeTSPBarcode(bc) {
            if (!bc) return '';
            return String(bc).trim().toLowerCase().replace(/[^a-z0-9]/g, '');
        }

        function processTSPBarcodeScan(transferId, rawBarcode) {
            const rawTrimmed = (rawBarcode || '').trim();
            const normalized = normalizeTSPBarcode(rawTrimmed);
            const feedbackEl = document.getElementById(`tsp_scan_feedback_${transferId}`);

            if (!normalized && !rawTrimmed) return false;

            const rows = document.querySelectorAll(`#teamStockPickModal${transferId} .tsp-item-row`);
            let matched = false;
            let matchedTitle = '';

            rows.forEach(row => {
                if (matched) return;
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

                const normalizedBarcodes = barcodes.map(normalizeTSPBarcode);
                const normalizedTitle = normalizeTSPBarcode(title);

                const isMatch = (normalized && normalizedBarcodes.includes(normalized)) ||
                                (normalized && normalizedTitle.includes(normalized)) ||
                                (rawTrimmed && barcodes.some(b => String(b).trim().toLowerCase() === rawTrimmed.toLowerCase())) ||
                                (rawTrimmed && title.toLowerCase().includes(rawTrimmed.toLowerCase()));

                if (isMatch) {
                    matched = true;
                    matchedTitle = title;
                    markTSPItemAsPicked(transferId, index, title);
                }
            });

            if (matched && feedbackEl) {
                feedbackEl.className = 'p-2 rounded text-center small fw-bold bg-success text-white border mb-3';
                feedbackEl.innerHTML = `<i class="las la-check-circle me-1"></i>SCANNED: "${matchedTitle}" - Marked as Picked!`;
            } else if (!matched && feedbackEl) {
                feedbackEl.className = 'p-2 rounded text-center small fw-bold bg-danger text-white border mb-3';
                feedbackEl.innerHTML = `<i class="las la-times-circle me-1"></i>Barcode "${rawTrimmed}" not found in this transfer!`;
            }

            return matched;
        }

        function onTSPManualScanClick(transferId) {
            const input = document.getElementById(`tsp_barcode_input_${transferId}`);
            if (input && input.value.trim()) {
                processTSPBarcodeScan(transferId, input.value.trim());
                input.value = '';
                input.focus();
            }
        }

        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && e.target.classList.contains('tsp-barcode-input-field')) {
                e.preventDefault();
                const transferId = e.target.getAttribute('data-transfer-id');
                if (transferId) {
                    onTSPManualScanClick(transferId);
                }
            }
        });

 function onTSPPickedQtyChange(transferId, index) {
    const qtyInput = document.getElementById(`tsp_picked_qty_${transferId}_${index}`);
    const statusSelect = document.getElementById(`tsp_item_status_${transferId}_${index}`);
    const row = document.getElementById(`tsp_row_${transferId}_${index}`);

    if (!qtyInput || !statusSelect) return;

    const pickedQty = parseFloat(qtyInput.value) || 0;
    const maxQty = parseFloat(qtyInput.max) || 0;

    if (pickedQty <= 0) {
        qtyInput.value = 0;
        statusSelect.value = 'Pending';

        if (row) {
            row.style.backgroundColor = '#f8d7da';
        }
    } else if (pickedQty >= maxQty) {
        statusSelect.value = 'Picked';

        if (row) {
            row.style.backgroundColor = '#d4edda';
        }
    } else {
        statusSelect.value = 'Picking';

        if (row) {
            row.style.backgroundColor = '#fff3cd';
        }
    }

    updateTSPProgress(transferId);
}

function markTSPItemAsPicked(transferId, index, title) {
    const select = document.getElementById(`tsp_item_status_${transferId}_${index}`);
    const row = document.getElementById(`tsp_row_${transferId}_${index}`);
    const qtyInput = document.getElementById(`tsp_picked_qty_${transferId}_${index}`);

    if (!qtyInput) return;

    const maxQty = parseFloat(qtyInput.max) || 0;

    // Never create artificial stock.
    // If requested quantity is 0, picked quantity must remain 0.
    if (maxQty <= 0) {
        qtyInput.value = 0;

        if (select) {
            select.value = 'Pending';
        }

        if (row) {
            row.style.backgroundColor = '#f8d7da';
        }

        updateTSPProgress(transferId);
        return;
    }

    // Pick the actual requested quantity.
    qtyInput.value = maxQty;

    if (select) {
        select.value = 'Picked';
    }

    if (row) {
        row.style.backgroundColor = '#d4edda';
    }

    updateTSPProgress(transferId);
}

        function markAllTSPItemsPicked(transferId) {
            const rows = document.querySelectorAll(`#teamStockPickModal${transferId} .tsp-item-row`);
            rows.forEach(row => {
                const index = row.getAttribute('data-index');
                markTSPItemAsPicked(transferId, index);
            });
        }

        function updateTSPProgress(transferId) {
            const rows = document.querySelectorAll(`#teamStockPickModal${transferId} .tsp-item-row`);
            let pickedCount = 0;
            const totalCount = rows.length;

            rows.forEach(row => {
                const index = row.getAttribute('data-index');
                const select = document.getElementById(`tsp_item_status_${transferId}_${index}`);
                if (select && select.value === 'Picked') {
                    pickedCount++;
                }
            });

            const itemsPickedInput = document.getElementById(`tsp_items_picked_${transferId}`);
            if (itemsPickedInput) {
                itemsPickedInput.value = pickedCount;
            }

            const progressBar = document.getElementById(`tsp_progress_bar_${transferId}`);
            if (progressBar) {
                const pct = totalCount > 0 ? Math.round((pickedCount / totalCount) * 100) : 0;
                progressBar.style.width = pct + '%';
                progressBar.textContent = pct + '%';
                if (pct === 100) {
                    progressBar.className = 'progress-bar bg-success';
                } else {
                    progressBar.className = 'progress-bar bg-warning text-dark';
                }
            }

            const tspStatusSelect = document.getElementById(`tsp_status_${transferId}`);
            if (tspStatusSelect) {
                if (pickedCount > 0) {
                    tspStatusSelect.value = 'picking';
                } else {
                    tspStatusSelect.value = 'pending_picklist';
                }
            }
        }
    </script>

    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <style>
        .tab-pane {
            display: none;
        }
        .tab-pane.active, .tab-pane.show.active {
            display: block !important;
        }
        .dataTables_wrapper {
            padding: 1rem 0;
            font-size: 13px;
        }
        .dataTables_wrapper .dataTables_length {
            float: left;
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .dataTables_length select {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            border: 1px solid #ced4da;
            font-size: 0.875rem;
            background-color: #fff;
            cursor: pointer;
        }
        .dataTables_wrapper .dataTables_filter {
            float: right;
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .dataTables_filter input {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            border: 1px solid #ced4da;
            font-size: 0.875rem;
            margin-left: 0.5rem;
            outline: none;
            transition: border-color 0.2s;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #ff0000;
            box-shadow: 0 0 0 2px rgba(255, 0, 0, 0.1);
        }
        .dataTables_wrapper .dataTables_info {
            float: left;
            padding-top: 0.75rem;
            font-weight: 500;
            color: #6c757d;
        }
        .dataTables_wrapper .dataTables_paginate {
            float: right;
            padding-top: 0.75rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.35rem 0.75rem !important;
            margin-left: 4px !important;
            border-radius: 6px !important;
            border: 1px solid #dee2e6 !important;
            background: #fff !important;
            color: #333 !important;
            font-weight: 500 !important;
            cursor: pointer !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #ff0000 !important;
            color: #fff !important;
            border-color: #ff0000 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed !important;
            background: #f8f9fa !important;
            color: #6c757d !important;
        }
        
        #pickListsTable, #lazadaTable, #shopeeTable, #tiktokTable, #complimentaryPickListsTable {
            font-size: 13px;
        }
        
        #pickListsTable thead th, #lazadaTable thead th, #shopeeTable thead th, #tiktokTable thead th, #complimentaryPickListsTable thead th {
            padding: 8px 10px;
            font-weight: 600;
            font-size: 13px;
        }
        
        #pickListsTable tbody td, #lazadaTable tbody td, #shopeeTable tbody td, #tiktokTable tbody td, #complimentaryPickListsTable tbody td {
            padding: 6px 10px;
            vertical-align: middle;
        }
        
        .workflow-actions {
            display: flex;
            gap: 3px;
            align-items: center;
        }
        
        .workflow-actions .btn {
            padding: 2px 4px !important;
            font-size: 10px !important;
            min-width: 24px !important;
            width: 24px !important;
            height: 24px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        .workflow-actions .btn i {
            margin: 0 !important;
            font-size: 12px !important;
        }

        /* Tab styling improvements */
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
        }

        .nav-tabs .nav-link {
            color: #495057;
            border: none;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            border-bottom-color: #0dcaf0;
            color: #0dcaf0;
        }

        .nav-tabs .nav-link.active {
            color: #fff;
            background-color: #0dcaf0;
            border-bottom-color: #0dcaf0;
            border-radius: 4px 4px 0 0;
        }

        .nav-tabs .nav-link .badge {
            font-size: 11px;
            padding: 3px 6px;
        }

        #ecomTabs .nav-link {
            font-size: 14px;
            padding: 0.5rem 1rem;
        }

        #ecomTabs .nav-link.active {
            background-color: #0dcaf0;
            color: white;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        console.log('Pick Lists page loaded');
        
        $(document).ready(function() {
            console.log('Document ready - initializing DataTables');
            
            // Safely initialize DataTables on all tables with class .display
            $('table.display').each(function() {
                if ($.fn.DataTable.isDataTable(this)) return;
                try {
                    $(this).DataTable({
                        order: [],
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                        searching: true,
                        paging: true,
                        info: true,
                        responsive: true,
                        autoWidth: false,
                        language: {
                            search: "Search:",
                            searchPlaceholder: "Search pick lists...",
                            lengthMenu: "Show _MENU_ entries",
                            info: "Showing _START_ to _END_ of _TOTAL_ entries",
                            paginate: {
                                first: "First",
                                last: "Last",
                                next: "Next",
                                previous: "Previous"
                            }
                        }
                    });
                } catch(e) {
                    console.warn('DataTable init warning in pick-list-list for:', this.id, e);
                }
            });

            // Fail-safe manual tab switching for mainTabs
            $(document).on('click', '#mainTabs .nav-link', function(e) {
                e.preventDefault();
                $('#mainTabs .nav-link').removeClass('active');
                $(this).addClass('active');
                const target = $(this).attr('data-bs-target') || $(this).attr('data-target');
                if (target) {
                    $('#mainTabsContent > .tab-pane').removeClass('show active').css('display', 'none');
                    $(target).addClass('show active').css('display', 'block');
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
