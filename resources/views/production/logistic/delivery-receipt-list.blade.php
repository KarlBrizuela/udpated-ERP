<x-app-layout :title="'Delivery Receipts'" :sidebar="$sidebar ?? 'production'">
    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <style>
        .nav-tabs .nav-link {
            color: #333;
            border: none;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 1rem;
        }
        .nav-tabs .nav-link:hover {
            border-bottom-color: #ff0000;
        }
        .nav-tabs .nav-link.active {
            background: transparent;
            color: #ff0000;
            border-bottom-color: #ff0000;
        }

        .table-status-badge {
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-in-transit { background: #cce5ff; color: #004085; }

        .dataTables_wrapper {
            font-size: 13px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #ff0000 !important;
            color: #fff !important;
            border-color: #ff0000 !important;
        }

        /* Floating Sticky Bulk Action Bar at Bottom of Screen */
        .dr-bulk-floating-bar {
            position: fixed;
            bottom: 25px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            background: #ffffff;
            padding: 10px 24px;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            gap: 18px;
            border: 2px solid #ff0000;
            transition: all 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .dr-bulk-floating-bar.hidden {
            bottom: -100px;
            opacity: 0;
            pointer-events: none;
        }
    </style>
    @endpush

    @php
        $sidebar = $sidebar ?? (request()->is('admin-finance*') ? 'admin-finance' : 'production');
        $drCreateRoute = $sidebar === 'admin-finance' ? 'admin-finance.accounting.delivery-receipt' : 'production.logistic.delivery-receipt';

        $user = auth()->user();
        $canPrep = $user && ($user->isSuperAdmin() || 
            str_contains($user->position, 'Manager') || 
            str_contains($user->position, 'Supervisor') || 
            str_contains($user->position, 'Head') || 
            str_contains($user->position, 'Senior Logistics Staff') || 
            str_contains($user->position, 'Logistics Staff') ||
            str_contains($user->position, 'Accounting') ||
            str_contains($user->position, 'Finance') ||
            $user->hasPermission('admin_finance.accounting'));
            
        $canApprove = $user && ($user->isSuperAdmin() || 
            str_contains($user->position, 'Manager') || 
            str_contains($user->position, 'Supervisor') || 
            str_contains($user->position, 'Head') || 
            str_contains($user->position, 'Senior Logistics Staff') ||
            str_contains($user->position, 'Accounting') ||
            str_contains($user->position, 'Finance') ||
            $user->hasPermission('admin_finance.accounting'));
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card">
                <ul class="nav nav-tabs border-bottom px-4 pt-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-pane" type="button" role="tab" aria-controls="pending-pane" aria-selected="true">
                            <i class="fas fa-hourglass-half me-2"></i>Pending DR Prep ({{ count($orders) }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-pane" type="button" role="tab" aria-controls="completed-pane" aria-selected="false">
                            <i class="fas fa-check-circle me-2"></i>Completed DRs ({{ count($completedOrders ?? []) }})
                        </button>
                    </li>
                </ul>

                <div class="card-header border-0 d-block d-sm-flex px-4 pt-3 pb-0">
                    <div>
                        <h4 class="fs-20 mb-0 text-black">Delivery Receipts Management</h4>
                    </div>
                    @if($canPrep)
                    <a href="{{ route($drCreateRoute) }}" class="btn btn-primary rounded d-flex align-items-center ms-auto" style="gap: 0.5rem; background: #ff0000; border: none;">
                        <i class="las la-plus"></i>
                        <span>Create New Receipt</span>
                    </a>
                    @endif
                </div>

                <div class="tab-content p-4">
                    <!-- Pending DR Prep Tab -->
                    <div class="tab-pane fade show active" id="pending-pane" role="tabpanel" aria-labelledby="pending-tab">
                        <!-- Single Line Filter Section -->
                        <div class="row g-2 align-items-center mb-4">
                            <div class="col-md-5 col-sm-12">
                                <input type="text" id="searchInput" class="form-control" placeholder="Search by SO # or Customer...">
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <select id="customerFilter" class="form-control">
                                    <option value="all">All Customers</option>
                                     @php
                                         $getResolvedCustomer = function($order) {
                                             $cName = $order->customer?->customer_name;
                                             $cComp = $order->customer?->company_name;
                                             $cRep = $order->customer_representative;
                                             $bName = $cRep;
                                             if (!$bName && $order->remarks && str_contains($order->remarks, 'Branch:')) {
                                                 preg_match('/Branch:\s*([^|\n\r]+)/', $order->remarks, $m);
                                                 $bName = trim($m[1] ?? '');
                                             }

                                             $bCompany = null;
                                             if ($bName) {
                                                 $bCompany = \App\Models\Company::where('company_name', $bName)
                                                     ->orWhere('company_name', str_replace('AB-', 'AB - ', $bName))
                                                     ->orWhere('company_name', str_replace('AB - ', 'AB-', $bName))
                                                     ->first();

                                                 if (!$bCompany && preg_match('/(\d{3,})/', $bName, $codeM)) {
                                                     $bCode = $codeM[1];
                                                     $bCompany = \App\Models\Company::where('company_name', 'like', "%{$bCode}%")
                                                         ->orWhere('account_number', 'like', "%{$bCode}%")
                                                         ->first();
                                                 }

                                                 if (!$bCompany) {
                                                     $cleanBName = trim(str_replace(['AB-', 'AB -', 'AB'], '', $bName));
                                                     if (!empty($cleanBName)) {
                                                         $bCompany = \App\Models\Company::where('company_name', 'like', "%{$cleanBName}%")->first();
                                                     }
                                                 }
                                             }

                                             if ($bCompany) {
                                                 return $bCompany->company_name;
                                             }

                                             if (strtolower($cComp ?? '') === 'national book store' || strtolower($cName ?? '') === 'national book store' || str_contains(strtolower($order->so_number ?? ''), 'nbs')) {
                                                 return !empty($cRep) ? $cRep : (!empty($cComp) && strtolower($cComp) !== 'abacus' ? $cComp : (!empty($cName) && strtolower($cName) !== 'abacus' ? $cName : 'National Book Store'));
                                             }

                                             return !empty($cName) ? $cName : (!empty($cComp) ? $cComp : (!empty($cRep) ? $cRep : 'Unknown'));
                                         };

                                         $uniqueCustomers = $orders->merge($completedOrders ?? [])->map(function($order) use ($getResolvedCustomer) {
                                             $disp = $getResolvedCustomer($order);
                                             return [
                                                 'id' => $order->customer_id ?? $order->id,
                                                 'name' => $disp
                                             ];
                                         })->unique('name')->sortBy('name');
                                     @endphp
                                    @foreach($uniqueCustomers as $cItem)
                                        <option value="{{ $cItem['id'] }}">{{ $cItem['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <select id="statusFilter" class="form-control">
                                    <option value="all">All Status</option>
                                    <option value="pending_dr_prep">Pending Prep</option>
                                    <option value="pending_dr_approval">Pending Approval</option>
                                    <option value="ready_for_delivery">Ready for Delivery</option>
                                    <option value="si_created">Closed</option>
                                    <option value="reconsignment_pending">Reconsignment Pending</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="drTable" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>SO Number</th>
                                        <th style="min-width: 180px;">Customer</th>
                                        <th>Total Amount</th>
                                        <th>Payment Terms</th>
                                        <th>Remaining Date</th>
                                        <th>Status</th>
                                        <th>Prepared By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    @php
                                        $rawTerms = $order->terms ?: ($order->customer?->payment_terms ?: ($order->customer?->terms ?? null));
                                        if (empty($rawTerms) && (str_contains(strtolower($order->so_number ?? ''), 'nbs') || str_contains(strtolower($order->customer?->customer_name ?? ''), 'national book store') || str_contains(strtolower($order->customer?->customer_name ?? ''), 'nbs'))) {
                                            $rawTerms = '90 days';
                                        }

                                        $termsMap = [
                                            'cash' => 0, 
                                            'cod' => 0, 
                                            '7_days' => 7, '7 days' => 7, '7days' => 7, '7' => 7,
                                            '15_days' => 15, '15 days' => 15, '15days' => 15, '15' => 15,
                                            '30_days' => 30, '30 days' => 30, '30days' => 30, '30' => 30,
                                            '60_days' => 60, '60 days' => 60, '60days' => 60, '60' => 60,
                                            '90_days' => 90, '90 days' => 90, '90days' => 90, '90' => 90,
                                        ];
                                        
                                        $termValue = strtolower(trim($rawTerms ?? ''));
                                        $daysFromTerms = $termsMap[$termValue] ?? 0;
                                        if ($daysFromTerms === 0 && preg_match('/(\d+)/', $termValue, $matches)) {
                                            $daysFromTerms = (int)$matches[1];
                                        }
                                        
                                        if ($daysFromTerms > 0) {
                                            $baseDateTime = $order->dr_prepared_at ?? $order->created_at;
                                            $baseDate = \Carbon\Carbon::parse($baseDateTime);
                                            $dueDate = $baseDate->copy()->addDays($daysFromTerms);
                                            $today = \Carbon\Carbon::today();
                                            $interval = $today->diff($dueDate);
                                            $daysRemaining = (int)$interval->format('%r%a');
                                        } else {
                                            $daysRemaining = null;
                                            $dueDate = null;
                                        }

                                        $termsDisplay = match($termValue) {
                                            'cash' => 'Cash',
                                            'cod' => 'COD',
                                            '7_days', '7 days' => '7 Days',
                                            '15_days', '15 days' => '15 Days',
                                            '30_days', '30 days' => '30 Days',
                                            '60_days', '60 days' => '60 Days',
                                            '90_days', '90 days' => '90 Days',
                                            '' => 'N/A',
                                            default => ucwords(str_replace('_', ' ', $rawTerms))
                                        };

                                        $effectiveTotal = (float)($order->total_amount ?? 0);
                                        if ($effectiveTotal <= 0) {
                                            $itemsToUse = ($order->items && count($order->items) > 0) ? $order->items : \App\Models\SalesOrderItem::where('sales_order_id', $order->id)->get();
                                            if (count($itemsToUse) > 0) {
                                                $grossSubtotal = 0;
                                                $totalItemDiscounts = 0;
                                                foreach ($itemsToUse as $item) {
                                                    $qty = (float)(!empty($item->customer_selected_qty) ? $item->customer_selected_qty : ($item->quantity ?? 0));
                                                    $unitPrice = (float)($item->price ?? ($item->unit_price ?? 0));
                                                    $itemSubtotal = $qty * $unitPrice;
                                                    $grossSubtotal += $itemSubtotal;

                                                    $itemDiscountAmt = 0;
                                                    if (($item->discount_amount ?? 0) > 0) {
                                                        $itemDiscountAmt = (float)$item->discount_amount;
                                                    } elseif (($item->discount_value ?? 0) > 0) {
                                                        if (($item->discount_type ?? 'percentage') === 'percentage') {
                                                            $itemDiscountAmt = $itemSubtotal * ((float)$item->discount_value / 100);
                                                        } else {
                                                            $itemDiscountAmt = (float)$item->discount_value;
                                                        }
                                                    }
                                                    $totalItemDiscounts += $itemDiscountAmt;
                                                }
                                                $orderDiscountAmt = (float)($order->discount_amount ?? 0);
                                                if ($orderDiscountAmt == 0 && ($order->discount_percentage ?? 0) > 0) {
                                                    $orderDiscountAmt = max(0, $grossSubtotal - $totalItemDiscounts) * ((float)$order->discount_percentage / 100);
                                                }
                                                $allDiscountsCombined = $totalItemDiscounts + $orderDiscountAmt;
                                                $freightChargesAmt = (float)($order->freight_charges ?? 0);
                                                $effectiveTotal = max(0, $grossSubtotal - $allDiscountsCombined + $freightChargesAmt);
                                            }
                                        }
                                    @endphp
                                     @php
                                          $displayCustomer = $getResolvedCustomer($order);
                                     @endphp
                                     <tr data-so-number="{{ $order->so_number }}" data-customer="{{ strtolower($displayCustomer) }}" data-customer-id="{{ $order->customer_id ?? '' }}" data-status="{{ $order->status }}" data-days-remaining="{{ $daysRemaining !== null ? $daysRemaining : '' }}">
                                         <td>
                                             <strong>{{ $order->so_number }}</strong>
                                             @if($order->cancellation_date)
                                                 <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="fas fa-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($order->cancellation_date)->format('M d, Y') }}</span>
                                             @endif
                                         </td>
                                         <td>
                                             <div style="min-width: 180px; max-width: 280px; word-break: break-word; white-space: normal;">
                                                 <span class="fw-bold text-dark">{{ $displayCustomer }}</span>
                                             </div>
                                         </td>
                                        @php
                                            $drListSym = (($order->currency ?? 'PHP') === 'USD' ? '$' : '₱');
                                        @endphp
                                        <td>{{ $drListSym }}{{ number_format($effectiveTotal, 2) }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $termsDisplay }}</span>
                                        </td>
                                        <td>
                                            @if($daysRemaining !== null)
                                                <span class="@if($daysRemaining < 0) text-danger fw-bold @elseif($daysRemaining < 7) text-warning @else text-success @endif">
                                                    {{ $dueDate->format('M d, Y') }}
                                                    <br><small>{{ $daysRemaining < 0 ? abs($daysRemaining) . ' days overdue' : $daysRemaining . ' days' }}</small>
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->status === 'pending_dr_prep')
                                                <span class="table-status-badge status-pending">Pending Prep</span>
                                            @elseif($order->status === 'pending_dr_approval')
                                                <span class="table-status-badge status-in-transit">Pending Approval</span>
                                            @elseif($order->status === 'ready_for_delivery')
                                                <span class="table-status-badge status-completed">Ready for Delivery</span>
                                            @elseif($order->status === 'ar_created')
                                                <span class="table-status-badge bg-info text-white text-nowrap">Moved to AR</span>
                                            @elseif($order->status === 'cr_created')
                                                <span class="table-status-badge bg-success text-white text-nowrap">Moved to CR</span>
                                            @elseif($order->status === 'si_created')
                                                <span class="table-status-badge bg-secondary text-white">Closed</span>
                                            @elseif($order->status === 'reconsignment_pending')
                                                <span class="table-status-badge bg-warning text-dark text-nowrap">Reconsignment Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->drPreparedBy)
                                                <span class="fw-bold text-dark">{{ $order->drPreparedBy->name }}</span>
                                                <br><small class="text-muted">DR Approver</small>
                                            @else
                                                {{ $order->preparedBy->name ?? 'System' }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route($drCreateRoute, $order->id) }}" class="btn btn-primary shadow btn-xs sharp" title="View/Create DR">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if(in_array($order->type, ['area_consignment', 'area_sales_consignment']))
                                                     <button type="button" class="btn btn-success shadow btn-xs sharp" title="Import Excel (Customer Name + Pick Qty)" data-bs-toggle="modal" data-bs-target="#importExcelModalDr{{ $order->id }}">
                                                         <i class="las la-file-excel"></i>
                                                     </button>
                                                     @php
                                                         $isMovedToAR = $order->status === 'ar_created' || $order->ar_prepared_at !== null;
                                                         $isMovedToCR = $order->status === 'cr_created' || $order->cr_prepared_at !== null;
                                                     @endphp
                                                     @if($order->type === 'area_sales_consignment')
                                                          <form action="{{ route('production.logistic.move-to-ar', $order->id) }}" method="POST" style="display:inline;">
                                                              @csrf
                                                              <button type="submit" class="btn btn-info shadow btn-xs sharp text-white" title="{{ $isMovedToAR ? 'Already Moved to AR' : 'Move to Acknowledgement Receipt (AR)' }}" {{ $isMovedToAR ? 'disabled' : '' }}>
                                                                  <i class="las la-file-signature"></i>
                                                              </button>
                                                          </form>
                                                      @elseif($order->type === 'area_consignment')
                                                          <form action="{{ route('production.logistic.move-to-cr', $order->id) }}" method="POST" style="display:inline;">
                                                              @csrf
                                                              <button type="submit" class="btn btn-success shadow btn-xs sharp" title="{{ $isMovedToCR ? 'Already Moved to CR' : 'Move to Consignment Receipt (CR)' }}" {{ $isMovedToCR ? 'disabled' : '' }}>
                                                                  <i class="las la-file-contract"></i>
                                                              </button>
                                                          </form>
                                                      @endif
                                                @endif
                                                 @if($order->status === 'pending_dr_prep' && $canPrep)
                                                    <form action="{{ route('production.logistic.mark-as-dr-prepared', $order->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-warning shadow btn-xs sharp" title="Mark as DR Prepared">
                                                            <i class="fas fa-file-invoice"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if(in_array($order->status, ['pending_dr_prep', 'pending_dr_approval']) && $canApprove)
                                                    <form action="{{ route('production.logistic.approve-dr', $order->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success shadow btn-xs sharp" title="Approve & Sign DR">
                                                            <i class="fas fa-signature"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @php
                                                    $isTeamOrder = !empty($order->preparedBy->sales_team ?? $order->areaSalesStaff->sales_team ?? null);
                                                @endphp
                                                @if($isTeamOrder || ($sidebar ?? '') === 'admin-finance')
                                                    <form action="{{ route('production.logistic.move-to-si', $order->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger shadow btn-xs sharp text-white" title="Move to Sales Invoice (SI)">
                                                            <i class="las la-file-invoice"></i>
                                                        </button>
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

                    <!-- Completed DRs Tab -->
                    <div class="tab-pane fade" id="completed-pane" role="tabpanel" aria-labelledby="completed-tab">
                        <!-- Filters Section for Completed DRs -->
                        <div class="p-3 mb-3 border rounded shadow-sm bg-light" style="height: auto !important; min-height: 0 !important;">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-4 col-sm-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Status</label>
                                    <select id="completedStatusFilter" class="form-control form-control-sm">
                                        <option value="all">All Statuses</option>
                                        <option value="ready_for_packing">In Packing</option>
                                        <option value="ready_for_delivery">Ready for Delivery</option>
                                        <option value="si_created">Moved to SI</option>
                                        <option value="ar_created">Moved to AR</option>
                                        <option value="cr_created">Moved to CR</option>
                                        <option value="completed">Completed</option>
                                        <option value="reconsignment_pending">Reconsignment Pending</option>
                                    </select>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Prepared By</label>
                                    <select id="completedPreparedByFilter" class="form-control form-control-sm">
                                        <option value="all">All Prepared By</option>
                                        @php
                                            $uniquePreparers = collect($completedOrders ?? [])->map(function($o) {
                                                return $o->drPreparedBy->name ?? ($o->preparedBy->name ?? 'System');
                                            })->filter()->unique()->sort();
                                        @endphp
                                        @foreach($uniquePreparers as $prepName)
                                            <option value="{{ strtolower($prepName) }}">{{ $prepName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <label class="form-label small fw-bold text-muted mb-1">DR Date Range</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0"><i class="las la-calendar text-danger fs-16"></i></span>
                                        <input type="text" id="completedDateRange" class="form-control form-control-sm border-start-0" placeholder="Date Range" readonly style="background:#fff; cursor:pointer;">
                                    </div>
                                </div>
                                <div class="col-md-1 col-sm-6 d-flex align-items-end">
                                    <button type="button" id="resetCompletedFilters" class="btn btn-outline-secondary btn-sm w-100 mt-md-4" title="Reset Filters">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Sticky Bulk Action Bar for Completed DRs -->
                        <div id="bulkCompletedDrToolbar" class="dr-bulk-floating-bar hidden">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-danger rounded-pill px-3 py-2 fs-13 fw-bold" id="selectedCompletedDrCount">0</span>
                                <span class="fw-bold text-dark fs-14">Delivery Receipt(s) selected</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" id="btnPrintSelectedDr" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold shadow-sm" style="background:#ff0000; border-color:#ff0000; height: 38px;">
                                    <i class="las la-print me-1 fs-16"></i> Print Selected DRs
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover" id="completedDrTable" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">
                                            <input type="checkbox" id="selectAllCompletedDr" class="form-check-input" style="cursor: pointer; width: 18px; height: 18px;">
                                        </th>
                                        <th>SO Number</th>
                                        <th>DR Date</th>
                                        <th style="min-width: 180px;">Customer</th>
                                        <th>Total Amount</th>
                                        <th>Payment Terms</th>
                                        <th>Remaining Date</th>
                                        <th>Status</th>
                                        <th>Prepared By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($completedOrders ?? [] as $order)
                                    @php
                                        $rawTerms = $order->terms ?: ($order->customer?->payment_terms ?: ($order->customer?->terms ?? null));
                                        if (empty($rawTerms) && (str_contains(strtolower($order->so_number ?? ''), 'nbs') || str_contains(strtolower($order->customer?->customer_name ?? ''), 'national book store') || str_contains(strtolower($order->customer?->customer_name ?? ''), 'nbs'))) {
                                            $rawTerms = '90 days';
                                        }

                                        $termsMap = [
                                            'cash' => 0, 
                                            'cod' => 0, 
                                            '7_days' => 7, '7 days' => 7, '7days' => 7, '7' => 7,
                                            '15_days' => 15, '15 days' => 15, '15days' => 15, '15' => 15,
                                            '30_days' => 30, '30 days' => 30, '30days' => 30, '30' => 30,
                                            '60_days' => 60, '60 days' => 60, '60days' => 60, '60' => 60,
                                            '90_days' => 90, '90 days' => 90, '90days' => 90, '90' => 90,
                                        ];
                                        
                                        $termValue = strtolower(trim($rawTerms ?? ''));
                                        $daysFromTerms = $termsMap[$termValue] ?? 0;
                                        if ($daysFromTerms === 0 && preg_match('/(\d+)/', $termValue, $matches)) {
                                            $daysFromTerms = (int)$matches[1];
                                        }
                                        
                                        if ($daysFromTerms > 0) {
                                            $baseDateTime = $order->dr_prepared_at ?? $order->created_at;
                                            $baseDate = \Carbon\Carbon::parse($baseDateTime);
                                            $dueDate = $baseDate->copy()->addDays($daysFromTerms);
                                            $today = \Carbon\Carbon::today();
                                            $interval = $today->diff($dueDate);
                                            $daysRemaining = (int)$interval->format('%r%a');
                                        } else {
                                            $daysRemaining = null;
                                            $dueDate = null;
                                        }

                                        $termsDisplay = match($termValue) {
                                            'cash' => 'Cash',
                                            'cod' => 'COD',
                                            '7_days', '7 days' => '7 Days',
                                            '15_days', '15 days' => '15 Days',
                                            '30_days', '30 days' => '30 Days',
                                            '60_days', '60 days' => '60 Days',
                                            '90_days', '90 days' => '90 Days',
                                            '' => 'N/A',
                                            default => ucwords(str_replace('_', ' ', $rawTerms))
                                        };

                                        $drDateObj = $order->dr_prepared_at ? \Carbon\Carbon::parse($order->dr_prepared_at) : ($order->updated_at ? \Carbon\Carbon::parse($order->updated_at) : null);
                                        $drDateFormatted = $drDateObj ? $drDateObj->format('M d, Y') : '-';
                                        $drDateIso = $drDateObj ? $drDateObj->format('Y-m-d') : '';
                                        $prepByName = $order->drPreparedBy->name ?? ($order->preparedBy->name ?? 'System');

                                        $effectiveTotal = (float)($order->total_amount ?? 0);
                                        if ($effectiveTotal <= 0) {
                                            $itemsToUse = ($order->items && count($order->items) > 0) ? $order->items : \App\Models\SalesOrderItem::where('sales_order_id', $order->id)->get();
                                            if (count($itemsToUse) > 0) {
                                                $grossSubtotal = 0;
                                                $totalItemDiscounts = 0;
                                                foreach ($itemsToUse as $item) {
                                                    $qty = (float)(!empty($item->customer_selected_qty) ? $item->customer_selected_qty : ($item->quantity ?? 0));
                                                    $unitPrice = (float)($item->price ?? ($item->unit_price ?? 0));
                                                    $itemSubtotal = $qty * $unitPrice;
                                                    $grossSubtotal += $itemSubtotal;

                                                    $itemDiscountAmt = 0;
                                                    if (($item->discount_amount ?? 0) > 0) {
                                                        $itemDiscountAmt = (float)$item->discount_amount;
                                                    } elseif (($item->discount_value ?? 0) > 0) {
                                                        if (($item->discount_type ?? 'percentage') === 'percentage') {
                                                            $itemDiscountAmt = $itemSubtotal * ((float)$item->discount_value / 100);
                                                        } else {
                                                            $itemDiscountAmt = (float)$item->discount_value;
                                                        }
                                                    }
                                                    $totalItemDiscounts += $itemDiscountAmt;
                                                }
                                                $orderDiscountAmt = (float)($order->discount_amount ?? 0);
                                                if ($orderDiscountAmt == 0 && ($order->discount_percentage ?? 0) > 0) {
                                                    $orderDiscountAmt = max(0, $grossSubtotal - $totalItemDiscounts) * ((float)$order->discount_percentage / 100);
                                                }
                                                $allDiscountsCombined = $totalItemDiscounts + $orderDiscountAmt;
                                                $freightChargesAmt = (float)($order->freight_charges ?? 0);
                                                $effectiveTotal = max(0, $grossSubtotal - $allDiscountsCombined + $freightChargesAmt);
                                            }
                                        }

                                        $displayCustomer = $getResolvedCustomer($order);
                                    @endphp
                                    <tr data-so-number="{{ $order->so_number }}" 
                                        data-customer="{{ strtolower($displayCustomer) }}" 
                                        data-status="{{ $order->status }}" 
                                        data-prepared-by="{{ strtolower($prepByName) }}" 
                                        data-dr-date="{{ $drDateIso }}">
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input completed-dr-cb" value="{{ $order->id }}" style="cursor: pointer; width: 18px; height: 18px;">
                                        </td>
                                        <td>
                                            <strong>{{ $order->so_number }}</strong>
                                            @if($order->cancellation_date)
                                                <br><span class="badge bg-danger text-white mt-1" style="font-size: 0.72rem;"><i class="fas fa-calendar-times me-1"></i>Cancel: {{ \Carbon\Carbon::parse($order->cancellation_date)->format('M d, Y') }}</span>
                                            @endif
                                        </td>
                                        <td data-order="{{ $drDateObj ? $drDateObj->timestamp : ($order->id ?? 0) }}"><span class="badge bg-light text-dark border text-nowrap"><i class="las la-calendar me-1"></i>{{ $drDateFormatted }}</span></td>
                                        <td>
                                            <div style="min-width: 180px; max-width: 280px; word-break: break-word; white-space: normal;">
                                                <span class="fw-bold text-dark">{{ $displayCustomer }}</span>
                                            </div>
                                        </td>
                                        @php
                                            $drListSym = (($order->currency ?? 'PHP') === 'USD' ? '$' : '₱');
                                        @endphp
                                        <td>{{ $drListSym }}{{ number_format($effectiveTotal, 2) }}</td>
                                        <td><span class="badge bg-info">{{ $termsDisplay }}</span></td>
                                        <td>
                                            @if($daysRemaining !== null)
                                                <span class="@if($daysRemaining < 0) text-danger fw-bold @elseif($daysRemaining < 7) text-warning @else text-success @endif">
                                                    {{ $dueDate->format('M d, Y') }}
                                                    <br><small>{{ $daysRemaining < 0 ? abs($daysRemaining) . ' days overdue' : $daysRemaining . ' days' }}</small>
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->status === 'ready_for_packing')
                                                <span class="table-status-badge bg-primary text-white">In Packing</span>
                                            @elseif($order->status === 'ready_for_delivery')
                                                <span class="table-status-badge status-completed">Ready for Delivery</span>
                                            @elseif($order->status === 'ar_created')
                                                <span class="table-status-badge bg-info text-white">Moved to AR</span>
                                            @elseif($order->status === 'cr_created')
                                                <span class="table-status-badge bg-success text-white">Moved to CR</span>
                                            @elseif($order->status === 'si_created')
                                                <span class="table-status-badge bg-warning text-dark">Moved to SI</span>
                                            @elseif($order->status === 'completed')
                                                <span class="table-status-badge bg-success text-white">Completed</span>
                                            @else
                                                <span class="table-status-badge bg-secondary text-white">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->preparedBy->name ?? 'System' }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route($drCreateRoute, $order->id) }}" class="btn btn-primary shadow btn-xs sharp" title="View DR">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if(in_array($order->type, ['area_consignment', 'area_sales_consignment']))
                                                     <button type="button" class="btn btn-success shadow btn-xs sharp" title="Import Excel (Customer Name + Pick Qty)" data-bs-toggle="modal" data-bs-target="#importExcelModalDr{{ $order->id }}">
                                                         <i class="las la-file-excel"></i>
                                                     </button>
                                                     @php
                                                         $isMovedToAR = $order->status === 'ar_created' || $order->ar_prepared_at !== null;
                                                         $isMovedToCR = $order->status === 'cr_created' || $order->cr_prepared_at !== null;
                                                     @endphp
                                                     @if($order->type === 'area_sales_consignment')
                                                         <form action="{{ route('production.logistic.move-to-ar', $order->id) }}" method="POST" style="display:inline;">
                                                             @csrf
                                                             <button type="submit" class="btn btn-info shadow btn-xs sharp text-white" title="{{ $isMovedToAR ? 'Already Moved to AR' : 'Move to Acknowledgement Receipt (AR)' }}" {{ $isMovedToAR ? 'disabled' : '' }}>
                                                                 <i class="las la-file-signature"></i>
                                                             </button>
                                                         </form>
                                                     @elseif($order->type === 'area_consignment')
                                                         <form action="{{ route('production.logistic.move-to-cr', $order->id) }}" method="POST" style="display:inline;">
                                                             @csrf
                                                             <button type="submit" class="btn btn-success shadow btn-xs sharp" title="{{ $isMovedToCR ? 'Already Moved to CR' : 'Move to Consignment Receipt (CR)' }}" {{ $isMovedToCR ? 'disabled' : '' }}>
                                                                 <i class="las la-file-contract"></i>
                                                             </button>
                                                         </form>
                                                     @endif
                                                     <form action="{{ route('production.logistic.request-reconsignment', $order->id) }}" method="POST" style="display:inline;">
                                                         @csrf
                                                         <button type="submit" class="btn btn-warning shadow btn-xs sharp" title="Request Reconsignment">
                                                             <i class="las la-retweet"></i>
                                                         </button>
                                                     </form>
                                                     <form action="{{ route('production.logistic.return-consignment', $order->id) }}" method="POST" style="display:inline;">
                                                         @csrf
                                                         <button type="submit" class="btn btn-danger shadow btn-xs sharp" title="Return Consignment Stock">
                                                             <i class="las la-undo-alt"></i>
                                                         </button>
                                                     </form>
                                                 @endif

                                                <form action="{{ route('production.logistic.move-to-si', $order->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger shadow btn-xs sharp text-white" title="Move to Sales Invoice (SI)">
                                                        <i class="las la-file-invoice"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Import Excel Modals for DR --}}
    @foreach($orders->concat($completedOrders ?? [])->unique('id') as $order)
    @if(in_array($order->type, ['area_consignment', 'area_sales_consignment']))
    <div class="modal fade" id="importExcelModalDr{{ $order->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('production.logistic.delivery-receipt.import-excel') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white">
                            <i class="las la-file-excel me-2"></i>
                            Import Excel into DR — <strong>{{ $order->so_number }}</strong>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info mb-3" style="font-size:0.85rem;">
                            <strong><i class="las la-info-circle me-1"></i> Steps:</strong>
                            <ol class="mb-0 mt-1 ps-3">
                                <li>Export <strong>{{ $order->so_number }}</strong> from Sales Orders.</li>
                                <li>Row 7 Col B: Fill in <strong>Customer Name</strong>.</li>
                                <li>Column G (Row 10+): Fill in <strong>Pick Qty</strong> per item.</li>
                                <li>Upload the updated Excel file below.</li>
                            </ol>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Upload Excel File <span class="text-danger">*</span></label>
                            <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls" required>
                            <small class="text-muted">Only .xlsx / .xls — must match SO <strong>{{ $order->so_number }}</strong>.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="las la-upload me-1"></i> Import to DR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endforeach

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script>
        $(document).ready(function() {
            const drTable = $('#drTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 10,
                dom: 'rtip',
                columnDefs: [
                    { orderable: false, targets: -1 }
                ],
                language: {
                    zeroRecords: "No matching delivery receipts found"
                }
            });

            const completedDrTable = $('#completedDrTable').DataTable({
                order: [[2, 'desc'], [1, 'desc']],
                pageLength: 10,
                columnDefs: [
                    { orderable: false, targets: [0, -1] }
                ],
                language: {
                    zeroRecords: "No completed delivery receipts found"
                }
            });

            let completedDateFrom = null;
            let completedDateTo = null;

            // Initialize bootstrap daterangepicker for single calendar date-range selection
            $('#completedDateRange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            });

            $('#completedDateRange').on('apply.daterangepicker', function(ev, picker) {
                completedDateFrom = picker.startDate.format('YYYY-MM-DD');
                completedDateTo = picker.endDate.format('YYYY-MM-DD');
                $(this).val(picker.startDate.format('MMM DD, YYYY') + ' - ' + picker.endDate.format('MMM DD, YYYY'));
                completedDrTable.draw();
            });

            $('#completedDateRange').on('cancel.daterangepicker', function(ev, picker) {
                completedDateFrom = null;
                completedDateTo = null;
                $(this).val('');
                completedDrTable.draw();
            });

            // Persistent state for selected Completed DR IDs across pagination
            const selectedCompletedDrIds = new Set();

            function updateCompletedDrToolbarState() {
                const checkedCount = selectedCompletedDrIds.size;

                $('#selectedCompletedDrCount').text(checkedCount);

                const isCompletedTabActive = $('#completed-pane').hasClass('active');

                if (checkedCount > 0 && isCompletedTabActive) {
                    $('#bulkCompletedDrToolbar').removeClass('hidden');
                } else {
                    $('#bulkCompletedDrToolbar').addClass('hidden');
                }

                // Update selectAll header checkbox status for currently visible page
                const visibleCheckboxes = $('#completedDrTable tbody .completed-dr-cb');
                if (visibleCheckboxes.length > 0) {
                    const visibleChecked = visibleCheckboxes.filter(':checked').length;
                    if (visibleChecked === visibleCheckboxes.length) {
                        $('#selectAllCompletedDr').prop('checked', true).prop('indeterminate', false);
                    } else if (visibleChecked > 0) {
                        $('#selectAllCompletedDr').prop('checked', false).prop('indeterminate', true);
                    } else {
                        $('#selectAllCompletedDr').prop('checked', false).prop('indeterminate', false);
                    }
                } else {
                    $('#selectAllCompletedDr').prop('checked', false).prop('indeterminate', false);
                }
            }

            // Restore checkbox states when DataTables redraws (page change, search, sort)
            function syncCompletedDrCheckboxesOnDraw() {
                $('#completedDrTable tbody .completed-dr-cb').each(function() {
                    const id = $(this).val();
                    $(this).prop('checked', selectedCompletedDrIds.has(id));
                });
                updateCompletedDrToolbarState();
            }

            $('#selectAllCompletedDr').on('change', function() {
                const isChecked = $(this).is(':checked');
                
                completedDrTable.rows({ filter: 'applied' }).nodes().to$().find('.completed-dr-cb').each(function() {
                    const id = $(this).val();
                    $(this).prop('checked', isChecked);
                    if (isChecked) {
                        selectedCompletedDrIds.add(id);
                    } else {
                        selectedCompletedDrIds.delete(id);
                    }
                });

                updateCompletedDrToolbarState();
            });

            $(document).on('change', '.completed-dr-cb', function() {
                const id = $(this).val();
                if ($(this).is(':checked')) {
                    selectedCompletedDrIds.add(id);
                } else {
                    selectedCompletedDrIds.delete(id);
                }
                updateCompletedDrToolbarState();
            });

            completedDrTable.on('draw', function() {
                syncCompletedDrCheckboxesOnDraw();
            });

            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                updateCompletedDrToolbarState();
            });

            $('#btnPrintSelectedDr').on('click', function() {
                const selectedIds = Array.from(selectedCompletedDrIds);

                if (selectedIds.length === 0) {
                    alert('Please select at least one Delivery Receipt to print.');
                    return;
                }

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('production.logistic.delivery-receipt.bulk-print') }}";
                form.target = '_blank';

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);

                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            });

            // Custom DataTables filter for Completed DRs
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'completedDrTable') return true;

                const rowNode = completedDrTable.row(dataIndex).node();
                const statusFilter = ($('#completedStatusFilter').val() || 'all').toLowerCase().trim();
                const preparedByFilter = ($('#completedPreparedByFilter').val() || 'all').toLowerCase().trim();
                const dateFrom = completedDateFrom;
                const dateTo = completedDateTo;

                const status = ($(rowNode).data('status') || '').toString().toLowerCase();
                const preparedBy = ($(rowNode).data('prepared-by') || '').toString().toLowerCase();
                const drDate = ($(rowNode).data('dr-date') || '').toString(); // YYYY-MM-DD

                // 1. Status match
                const statusMatch = (statusFilter === 'all') || (status === statusFilter);

                // 2. Prepared By match
                const preparedByMatch = (preparedByFilter === 'all') || (preparedBy.includes(preparedByFilter));

                // 3. Date Range match
                let dateMatch = true;
                if (dateFrom || dateTo) {
                    if (!drDate) {
                        dateMatch = false;
                    } else {
                        if (dateFrom && drDate < dateFrom) dateMatch = false;
                        if (dateTo && drDate > dateTo) dateMatch = false;
                    }
                }

                return statusMatch && preparedByMatch && dateMatch;
            });

            // Trigger redraw on filter changes
            $('#completedStatusFilter, #completedPreparedByFilter').on('change', function() {
                completedDrTable.draw();
            });

            $('#resetCompletedFilters').on('click', function() {
                $('#completedStatusFilter').val('all');
                $('#completedPreparedByFilter').val('all');
                $('#completedDateRange').val('');
                completedDateFrom = null;
                completedDateTo = null;
                completedDrTable.draw();
            });

            // Custom DataTables filter matching Search, Customer, and Status for Pending DRs
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'drTable') return true;

                const rowNode = drTable.row(dataIndex).node();
                const searchTerm = $('#searchInput').val().toLowerCase().trim();
                const currentCustomerId = $('#customerFilter').val();
                const currentStatusFilter = $('#statusFilter').val();

                const soNumber = ($(rowNode).data('so-number') || '').toString().toLowerCase();
                const customer = ($(rowNode).data('customer') || '').toString().toLowerCase();
                const customerId = ($(rowNode).data('customer-id') || '').toString();
                const status = ($(rowNode).data('status') || '').toString();
                const daysRemaining = ($(rowNode).data('days-remaining') || '').toString();

                // Search match
                const searchMatch = !searchTerm || soNumber.includes(searchTerm) || customer.includes(searchTerm);

                // Customer match
                const customerMatch = currentCustomerId === 'all' || customerId === currentCustomerId;

                // Status match
                let statusMatch = false;
                if (currentStatusFilter === 'all') {
                    statusMatch = true;
                } else if (currentStatusFilter === 'overdue') {
                    statusMatch = daysRemaining !== '' && parseInt(daysRemaining) < 0;
                } else {
                    statusMatch = status === currentStatusFilter;
                }

                return searchMatch && customerMatch && statusMatch;
            });

            // Trigger redraw when filter inputs change
            $('#searchInput').on('keyup change', function() {
                drTable.draw();
            });

            $('#customerFilter, #statusFilter').on('change', function() {
                drTable.draw();
            });
        });
    </script>
    @endpush
</x-app-layout>
