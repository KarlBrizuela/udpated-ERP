<x-app-layout :title="$title ?? 'Delivery Scheduling'" :sidebar="$sidebar ?? 'production'">
    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .nav-tabs .nav-link {
            color: #495057;
            border: none;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 1.5rem;
            padding: 0.75rem 0.5rem;
        }
        .nav-tabs .nav-link:hover {
            border-bottom-color: #ff0000;
            color: #ff0000;
        }
        .nav-tabs .nav-link.active {
            background: transparent;
            color: #ff0000;
            border-bottom-color: #ff0000;
        }

        .scheduling-table th {
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #eee;
            padding: 12px 15px;
            font-size: 13px;
        }
        .scheduling-table td {
            padding: 12px 15px;
            vertical-align: middle;
            font-size: 13px;
        }
        .status-badge {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: 600;
        }
        .status-ready { background-color: #e6f8f0; color: #00a65a; }
        .status-in_transit { background-color: #e6f0fa; color: #0066cc; }
        .status-delivered { background-color: #e6f4ea; color: #137333; }
        .status-cod { background-color: #fff3cd; color: #856404; }
        .status-paid { background-color: #d4edda; color: #155724; }
        .status-charge { background-color: #e2e3e5; color: #383d41; }

        /* Floating Sticky Bulk Action Bar at Bottom of Screen */
        .pickup-bulk-floating-bar {
            position: fixed;
            bottom: 25px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
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
        .pickup-bulk-floating-bar.hidden {
            bottom: -100px;
            opacity: 0;
            pointer-events: none;
        }
    </style>
    @endpush

    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4 align-items-center">
            <div class="col-sm-6">
                <div class="d-flex align-items-center">
                    <div class="me-3 p-3 bg-primary text-white rounded-3 shadow-sm">
                        <i class="las la-truck fs-24"></i>
                    </div>
                    <div>
                        <h2 class="font-w600 mb-0">Delivery Scheduling</h2>
                        <p class="mb-0 text-muted">Manifest management, driver assignments, and office pickups</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                <button class="btn btn-secondary shadow-sm" onclick="window.print()">
                    <i class="las la-print me-2"></i>Print Manifest
                </button>
            </div>
        </div>

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="las la-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="las la-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Scheduling Tabs & Tables -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <ul class="nav nav-tabs border-bottom-0" id="deliveryTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="landtrip-tab" data-bs-toggle="tab" data-bs-target="#landtrip-pane" type="button" role="tab" aria-controls="landtrip-pane" aria-selected="true">
                                    <i class="las la-truck me-2"></i>Landtrip Manifest (Driver Delivery)
                                    <span class="badge bg-danger ms-2">{{ $orders->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pickup-tab" data-bs-toggle="tab" data-bs-target="#pickup-pane" type="button" role="tab" aria-controls="pickup-pane" aria-selected="false">
                                    <i class="las la-store-alt me-2"></i>For Pickup (Office Pickup)
                                    <span class="badge bg-primary ms-2">{{ $pickupOrders->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests-pane" type="button" role="tab" aria-controls="requests-pane" aria-selected="false">
                                    <i class="las la-clipboard-list me-2"></i>Logistics Service Orders
                                    <span class="badge bg-danger ms-2" style="background-color: #dc3545 !important;">{{ $approvedRequests->count() }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="card-body px-4 pb-4 pt-3">
                        <div class="tab-content" id="deliveryTabContent">
                            
                            <!-- TAB 1: LANDTRIP MANIFEST -->
                            <div class="tab-pane fade show active" id="landtrip-pane" role="tabpanel" aria-labelledby="landtrip-tab">
                                
                                <!-- Filters Section for Delivery Scheduling -->
                                <div class="p-3 mb-3 border rounded shadow-sm bg-light" style="height: auto !important; min-height: 0 !important;">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-3 col-sm-6">
                                            <label class="form-label small fw-bold text-muted mb-1"><i class="las la-user-tag me-1 text-danger"></i>Filter by Driver</label>
                                            <select id="driverFilter" class="form-control form-control-sm">
                                                <option value="all">All Drivers</option>
                                                <option value="unassigned">Unassigned (Needs Driver)</option>
                                                @php
                                                    $driverList = collect($drivers ?? [])->map(function($d) {
                                                        return trim(($d->first_name ?? '') . ' ' . ($d->last_name ?? ''));
                                                    })->concat(collect($orders ?? [])->pluck('driver'))->filter()->unique()->sort();
                                                @endphp
                                                @foreach($driverList as $drvName)
                                                    <option value="{{ strtolower($drvName) }}">{{ $drvName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <label class="form-label small fw-bold text-muted mb-1"><i class="las la-users me-1 text-danger"></i>Filter by Team</label>
                                            <select id="teamFilter" class="form-control form-control-sm">
                                                <option value="all">All Teams</option>
                                                @php
                                                    $teamList = collect($orders ?? [])->map(function($o) {
                                                        return $o->sales_team ?? ($o->team ?? ($o->preparedBy?->sales_team ?? ($o->customer?->customer_type ?? null)));
                                                    })->concat(['Team A', 'Team B', 'Team C', 'Book Sales', 'MIBF'])->filter()->unique()->sort();
                                                @endphp
                                                @foreach($teamList as $tmName)
                                                    <option value="{{ strtolower($tmName) }}">{{ $tmName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <label class="form-label small fw-bold text-muted mb-1"><i class="las la-tasks me-1 text-danger"></i>Filter by Status</label>
                                            <select id="statusFilter" class="form-control form-control-sm">
                                                <option value="all">All Statuses</option>
                                                <option value="ready">Ready</option>
                                                <option value="in_transit">In Transit</option>
                                                <option value="delivered">Delivered</option>
                                                <option value="scheduled">Scheduled</option>
                                                <option value="not_scheduled">Not Scheduled</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-sm-6 d-flex align-items-end">
                                            <button type="button" id="resetSchedulingFilters" class="btn btn-outline-secondary btn-sm w-100 mt-md-4" title="Reset Filters">
                                                <i class="fas fa-undo me-1"></i>Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>


                                <!-- Bulk Set as Pickup Form -->
                                <form action="{{ route('production.logistic.set-as-pickup') }}" method="POST" id="bulkPickupForm">
                                    @csrf
                                    <div class="table-responsive">
                                        <table id="deliveryTable" class="display table scheduling-table mb-0" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 40px;" class="text-center">
                                                        <input type="checkbox" id="selectAllLandtrip" class="form-check-input" style="cursor: pointer; width: 18px; height: 18px;">
                                                    </th>
                                                    <th>SI / DR Reference</th>
                                                    <th>Reference Number</th>
                                                    <th>Client</th>
                                                    <th>Address</th>
                                                    <th>Forwarder</th>
                                                    <th>Doc Status</th>
                                                    <th>Assignment</th>
                                                    <th>Delivery Date</th>
                                                    <th>Remarks</th>
                                                    <th>Status</th>
                                                    <th class="text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($orders as $order)
                                                @php
                                                    $drvName = $order->driver ? strtolower(trim($order->driver)) : 'unassigned';
                                                    $hasDriver = $order->driver ? 'yes' : 'no';
                                                    $isScheduled = $order->delivery_date ? 'yes' : 'no';
                                                    $orderStatus = strtolower($order->status ?? 'ready');

                                                    // Team resolution
                                                    $orderTeam = $order->sales_team ?? ($order->team ?? ($order->preparedBy?->sales_team ?? ($order->customer?->customer_type ?? '')));

                                                    // SI resolution
                                                    $siObj = $order->invoice ?: \App\Models\SalesInvoice::where('so_id', $order->id)->orWhere('so_number', $order->so_number)->first();
                                                    $siNum = $siObj?->si_number ?: ($order->si_number ?: ($order->si_prepared_at ? ('SI-' . $order->so_number) : null));

                                                    // DR resolution
                                                    $drObj = $order->deliveryReceipt ?: \App\Models\DeliveryReceipt::where('so_id', $order->id)->orWhere('so_number', $order->so_number)->first();
                                                    $drNum = $drObj?->dr_number ?: ($order->dr_number ?: ($order->dr_prepared_at ? ('DR-' . $order->so_number) : ('DR-' . $order->so_number)));

                                                    $hasSI = !empty($siNum);
                                                    $hasDR = !empty($drNum);

                                                    $refDisplay = $hasSI ? $siNum : ($hasDR ? $drNum : $order->so_number);
                                                @endphp
                                                <tr data-driver="{{ $drvName }}"
                                                    data-has-driver="{{ $hasDriver }}"
                                                    data-scheduled="{{ $isScheduled }}"
                                                    data-status="{{ $orderStatus }}"
                                                    data-team="{{ strtolower(trim($orderTeam)) }}">
                                                    <td class="text-center align-middle">
                                                        <input type="checkbox" value="{{ $order->id }}" class="form-check-input landtrip-checkbox" style="cursor: pointer; width: 18px; height: 18px;">
                                                    </td>
                                                    <td class="align-middle">
                                                        <span class="text-black font-w600 d-block">{{ $refDisplay }}</span>
                                                        <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                                                    </td>
                                                     <td class="align-middle">
                                                         @php
                                                             $refVal = !empty($order->ref_number) ? $order->ref_number : (!empty($order->reference_number) ? $order->reference_number : null);
                                                         @endphp
                                                         @if($refVal)
                                                             <span class="badge bg-dark text-white font-monospace fs-13 px-2 py-1 fw-bold shadow-sm" title="Reference Number">
                                                                 <i class="las la-hashtag me-1 text-warning"></i>{{ $refVal }}
                                                             </span>
                                                         @else
                                                             <span class="text-muted font-w500 fs-13">—</span>
                                                         @endif
                                                     </td>
                                                    <td class="align-middle">
                                                        <span class="text-black d-block">{{ $order->customer->customer_name ?? 'N/A' }}</span>
                                                        @if(!empty($orderTeam))
                                                            <span class="badge bg-light text-primary border font-w500 fs-11 mt-1"><i class="fas fa-users me-1"></i>{{ $orderTeam }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle" style="max-width: 200px;">
                                                        <div class="text-truncate small text-muted" title="{{ $order->shipping_address ?? $order->billing_address ?? 'N/A' }}">
                                                            {{ $order->shipping_address ?? $order->billing_address ?? 'Same as Billing' }}
                                                        </div>
                                                    </td>
                                                    <td class="align-middle">
                                                        @php
                                                            $fwdName = !empty($order->forwarder) ? trim($order->forwarder) : (!empty($order->freightQuotation?->forwarder) ? trim($order->freightQuotation->forwarder) : null);
                                                        @endphp
                                                        @if($fwdName)
                                                            <span class="badge bg-light text-dark border font-w500"><i class="las la-truck me-1 text-danger"></i>{{ $fwdName }}</span>
                                                        @else
                                                            <span class="text-muted small">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle">
                                                        @if($hasSI)
                                                            <span class="badge bg-success font-monospace fs-12 px-2 py-1" title="Sales Invoice Number">
                                                                <i class="las la-file-invoice me-1"></i>{{ $siNum }}
                                                            </span>
                                                        @elseif($hasDR)
                                                            <span class="badge bg-info text-white font-monospace fs-12 px-2 py-1" title="Delivery Receipt Number">
                                                                <i class="las la-shipping-fast me-1"></i>{{ $drNum }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning text-dark fs-12 px-2 py-1">
                                                                <i class="las la-clock me-1"></i>Pending
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle">
                                                        @if($order->driver)
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-2 p-2 bg-light rounded text-black">
                                                                    <i class="las la-user-tag"></i>
                                                                </div>
                                                                <div>
                                                                    <span class="text-black font-w500 d-block small">{{ $order->driver }}</span>
                                                                    <span class="text-muted extra-small d-block">{{ $order->plate_number }}</span>
                                                                    @if(!empty($order->helper))
                                                                        <span class="text-muted extra-small d-block"><i class="las la-user-friends me-1"></i>Helper: {{ $order->helper }}</span>
                                                                    @endif
                                                                    @if($order->driver_approval_status === 'pending_approval')
                                                                        <div class="mt-1">
                                                                            <span class="badge bg-warning text-dark font-w500 fs-11 mb-1 d-inline-block"><i class="las la-clock me-1"></i>Pending Approval</span>
                                                                            <div class="d-flex gap-1 mt-1">
                                                                                <form action="{{ route('production.logistic.approve-driver', $order->id) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    <button type="submit" class="btn btn-success btn-xxs px-2 shadow-sm" title="Approve Driver Assignment">
                                                                                        <i class="las la-check me-1"></i>Approve
                                                                                    </button>
                                                                                </form>
                                                                                <form action="{{ route('production.logistic.reject-driver', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Reject this driver assignment?');">
                                                                                    @csrf
                                                                                    <button type="submit" class="btn btn-danger btn-xxs px-2 shadow-sm" title="Reject Driver Assignment">
                                                                                        <i class="las la-times me-1"></i>Reject
                                                                                    </button>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    @elseif($order->driver_approval_status === 'approved')
                                                                        <span class="badge bg-success text-white font-w500 fs-11 mt-1 d-inline-block"><i class="las la-check-circle me-1"></i>Approved</span>
                                                                    @endif
                                                                    <button type="button" class="btn btn-link btn-xs p-0 text-primary border-0 ms-1" data-bs-toggle="modal" data-bs-target="#assignDriverModal{{ $order->id }}">Change</button>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <button type="button" class="btn btn-warning btn-xxs shadow-sm px-2" data-bs-toggle="modal" data-bs-target="#assignDriverModal{{ $order->id }}">
                                                                Assign Driver
                                                            </button>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle">
                                                        @if($order->delivery_date)
                                                            <span class="text-black font-w500 d-block small">{{ \Carbon\Carbon::parse($order->delivery_date)->format('M d, Y') }}</span>
                                                        @else
                                                            <span class="text-muted small">Not scheduled</span>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle">
                                                        @if($order->type === 'paid')
                                                            <span class="status-badge status-paid">PAID</span>
                                                        @elseif($order->transaction_type === 'COD')
                                                            @php
                                                                $collection = \App\Models\RiderCollection::where('sales_order_id', $order->id)->first();
                                                            @endphp
                                                            <span class="status-badge status-cod">
                                                                COD: ₱{{ number_format($order->total_amount, 2) }}
                                                            </span>
                                                            <br>
                                                            @if($collection)
                                                                <small class="d-block mt-1">
                                                                    Collection: 
                                                                    <span class="badge bg-{{ $collection->status == 'verified' ? 'success' : ($collection->status == 'handed_over' ? 'warning' : 'secondary') }}">
                                                                        {{ ucfirst($collection->status) }}
                                                                    </span>
                                                                </small>
                                                            @else
                                                                <small class="d-block mt-1 text-danger">No collection created</small>
                                                            @endif
                                                        @else
                                                            <span class="status-badge status-charge">{{ strtoupper($order->type ?? $order->transaction_type ?? 'CHARGE') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle">
                                                        <span class="status-badge status-ready">Ready</span>
                                                    </td>
                                                    <td class="align-middle text-end">
                                                        <div class="d-flex justify-content-end gap-2">
                                                            @php
                                                                $canDeliver = true;
                                                                $disableReason = '';
                                                                if ($order->driver_approval_status === 'pending_approval') {
                                                                    $canDeliver = false;
                                                                    $disableReason = 'Driver assignment pending approval';
                                                                } elseif ($order->type === 'paid') {
                                                                    $canDeliver = true;
                                                                } elseif ($order->transaction_type === 'COD') {
                                                                    $collection = \App\Models\RiderCollection::where('sales_order_id', $order->id)->first();
                                                                    if (!$collection) {
                                                                        $canDeliver = false;
                                                                        $disableReason = 'No collection created';
                                                                    } elseif ($collection->status !== 'verified') {
                                                                        $canDeliver = false;
                                                                        $disableReason = 'Collection not verified by accounting';
                                                                    }
                                                                } else {
                                                                    $canDeliver = true;
                                                                }
                                                            @endphp
                                                            <button type="button" 
                                                                    class="btn btn-primary shadow btn-xs sharp" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#viewOrderModal{{ $order->id }}" 
                                                                    title="View Order Details & Remarks">
                                                                <i class="fas fa-eye"></i>
                                                            </button>

                                                            <button type="submit" 
                                                                    formaction="{{ route('production.logistic.mark-as-delivered', $order->id) }}" 
                                                                    class="btn btn-{{ $canDeliver ? 'success' : 'secondary disabled' }} shadow btn-xs sharp" 
                                                                    {{ !$canDeliver ? 'disabled' : '' }}
                                                                    title="{{ !$canDeliver ? $disableReason : 'Mark Complete' }}"
                                                                    onclick="return confirm('Confirm delivery completion?');">
                                                                <i class="fas fa-check"></i>
                                                            </button>

                                                            <a href="{{ route('production.logistic.print-transmittal', $order->id) }}" target="_blank" class="btn btn-info shadow btn-xs sharp" title="Print Transmittal">
                                                                <i class="las la-file-alt"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr class="empty-row">
                                                    <td colspan="12" class="text-center py-5">
                                                        <div class="text-muted">
                                                            <i class="las la-clipboard-list fs-50 mb-3 d-block opacity-25"></i>
                                                            No orders currently ready for Landtrip delivery schedule.
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </form>

                                <!-- Render Modals for Landtrip Orders outside table -->
                                @foreach($orders as $order)
                                    @php
                                        $orderTeam = $order->sales_team ?? ($order->team ?? ($order->preparedBy?->sales_team ?? ($order->customer?->customer_type ?? '')));
                                        $siObj = $order->invoice ?: \App\Models\SalesInvoice::where('so_id', $order->id)->orWhere('so_number', $order->so_number)->first();
                                        $siNum = $siObj?->si_number ?: ($order->si_number ?: ($order->si_prepared_at ? ('SI-' . $order->so_number) : null));
                                        $drObj = $order->deliveryReceipt ?: \App\Models\DeliveryReceipt::where('so_id', $order->id)->orWhere('so_number', $order->so_number)->first();
                                        $drNum = $drObj?->dr_number ?: ($order->dr_number ?: ($order->dr_prepared_at ? ('DR-' . $order->so_number) : ('DR-' . $order->so_number)));
                                        $hasSI = !empty($siNum);
                                        $hasDR = !empty($drNum);
                                        $refDisplay = $hasSI ? $siNum : ($hasDR ? $drNum : $order->so_number);
                                        $fwdName = !empty($order->forwarder) ? trim($order->forwarder) : (!empty($order->freightQuotation?->forwarder) ? trim($order->freightQuotation->forwarder) : null);
                                    @endphp

                                    <!-- Assign Driver Modal -->
                                    <div class="modal fade" id="assignDriverModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0">
                                                <div class="modal-header bg-secondary text-white border-0">
                                                    <h5 class="modal-title text-white">Assign Driver: {{ $order->so_number }}</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('production.logistic.assign-driver', $order->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body p-4 text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label font-w500 text-black">Select Driver</label>
                                                            <select name="driver_id" class="form-control default-select shadow-sm" required>
                                                                <option value="">-- Choose Driver --</option>
                                                                @foreach($drivers as $driver)
                                                                    <option value="{{ $driver->id }}" {{ (isset($order->driver_id) && $order->driver_id == $driver->id) ? 'selected' : '' }}>
                                                                        {{ $driver->first_name }} {{ $driver->last_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label font-w500 text-black">Vehicle Plate Number</label>
                                                            <input type="text" name="plate_number" class="form-control shadow-sm" value="{{ $order->plate_number ?? '' }}" placeholder="Ex: ABC 1234" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label font-w500 text-black">Helper Name</label>
                                                            <input type="text" name="helper" class="form-control shadow-sm" value="{{ $order->helper ?? '' }}" placeholder="Ex: Juan Dela Cruz">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label font-w500 text-black">Reference Number</label>
                                                            <input type="text" name="ref_number" class="form-control shadow-sm" value="{{ $order->ref_number ?? '' }}" placeholder="Ex: REF-1234">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label font-w500 text-black">Delivery Date</label>
                                                            <input type="date" name="delivery_date" class="form-control shadow-sm" value="{{ $order->delivery_date ?? '' }}" required>
                                                        </div>
                                                        <div class="mb-0">
                                                            <label class="form-label font-w500 text-black">Remarks</label>
                                                            <textarea name="remarks" class="form-control shadow-sm" rows="3" placeholder="Optional remarks or instructions...">{{ $order->remarks ?? '' }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-secondary shadow">Update Assignment</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- View Order Details Modal -->
                                    <div class="modal fade" id="viewOrderModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-primary text-white border-0 py-3">
                                                    <h5 class="modal-title text-white fw-bold">
                                                        <i class="las la-truck me-2"></i>Order Details: {{ $refDisplay }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4 text-start">
                                                    <!-- Status & Reference Badges -->
                                                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                                        <div>
                                                            @php
                                                                $refVal = !empty($order->ref_number) ? $order->ref_number : (!empty($order->reference_number) ? $order->reference_number : null);
                                                            @endphp
                                                            <span class="fw-bold text-dark fs-15 me-2">SO #: {{ $order->so_number }}</span>
                                                            @if($refVal)
                                                                <span class="badge bg-dark text-white font-monospace fs-13 px-2 py-1 me-1 shadow-sm fw-bold"><i class="las la-hashtag me-1 text-warning"></i>Ref #: {{ $refVal }}</span>
                                                            @endif
                                                            @if($hasSI)
                                                                <span class="badge bg-success font-monospace me-1">SI #: {{ $siNum }}</span>
                                                            @endif
                                                            @if($hasDR)
                                                                <span class="badge bg-info text-white font-monospace me-1">DR #: {{ $drNum }}</span>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <span class="badge bg-secondary px-3 py-2 fs-12">{{ strtoupper(str_replace('_', ' ', $order->status)) }}</span>
                                                        </div>
                                                    </div>

                                                    <!-- Info Grid -->
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded border h-100">
                                                                <h6 class="fw-bold text-primary mb-2"><i class="las la-user-circle me-1"></i>Customer Details</h6>
                                                                @if($refVal)
                                                                    <div class="mb-2 fs-13"><strong class="text-dark">Reference No.:</strong> <span class="badge bg-warning text-dark font-monospace fs-13 px-2 py-1 ms-1 fw-bold border border-warning"><i class="las la-hashtag me-1"></i>{{ $refVal }}</span></div>
                                                                @endif
                                                                <div class="small mb-1"><strong>Client:</strong> {{ $order->customer->customer_name ?? 'N/A' }}</div>
                                                                <div class="small mb-1"><strong>Contact:</strong> {{ $order->customer_contact ?? ($order->customer->phone ?? 'N/A') }}</div>
                                                                <div class="small mb-1"><strong>Delivery Address:</strong> {{ $order->shipping_address ?? ($order->billing_address ?? 'Same as Billing') }}</div>
                                                                @if(!empty($orderTeam))
                                                                    <div class="small mt-1"><strong>Team:</strong> <span class="badge bg-white text-primary border"><i class="fas fa-users me-1"></i>{{ $orderTeam }}</span></div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded border h-100">
                                                                <h6 class="fw-bold text-primary mb-2"><i class="las la-route me-1"></i>Dispatch & Assignment</h6>
                                                                <div class="small mb-1"><strong>Driver:</strong> {{ $order->driver ?: 'Unassigned' }}</div>
                                                                <div class="small mb-1"><strong>Plate Number:</strong> {{ $order->plate_number ?: 'N/A' }}</div>
                                                                <div class="small mb-1"><strong>Helper:</strong> {{ $order->helper ?: 'None' }}</div>
                                                                <div class="small mb-1"><strong>Delivery Date:</strong> {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('M d, Y') : 'Not Scheduled' }}</div>
                                                                <div class="small mb-1"><strong>Forwarder:</strong> {{ $fwdName ?: 'In-House / Landtrip' }}</div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Remarks / Notes Box -->
                                                    <div class="mb-3">
                                                        <div class="p-3 rounded border border-warning" style="background-color: #fff9e6;">
                                                            <h6 class="fw-bold text-warning mb-1" style="color: #b7791f !important;"><i class="las la-comment-alt me-1"></i>Remarks & Special Instructions</h6>
                                                            <p class="mb-0 small text-dark font-w500" style="white-space: pre-line;">{{ $order->remarks ?: ($order->freight_notes ?: 'No special remarks or instructions provided.') }}</p>
                                                        </div>
                                                    </div>

                                                    <!-- Items Table -->
                                                    <div class="mb-2">
                                                        <h6 class="fw-bold text-dark mb-2"><i class="las la-boxes me-1 text-primary"></i>Items Breakdown</h6>
                                                        <div class="table-responsive border rounded">
                                                            <table class="table table-sm table-striped mb-0 text-start align-middle" style="font-size: 13px;">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Item Description</th>
                                                                        <th class="text-center">Barcode</th>
                                                                        <th class="text-center">Qty</th>
                                                                        <th class="text-end">Unit Price</th>
                                                                        <th class="text-end">Total</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse($order->items->filter(fn($i) => (float)($i->quantity ?? 0) > 0) as $index => $item)
                                                                    @php
                                                                        $itemName = $item->bookIndex?->display_name ?? ($item->book?->name ?? ($item->bundle?->name ?? ($item->product_name ?? 'N/A')));
                                                                        $barcode = $item->bookIndex?->barcode ?? ($item->book?->barcode ?? ($item->bundle?->barcode ?? '—'));
                                                                        $unitPrice = $item->unit_price ?? ($item->price ?? 0);
                                                                        $subtotal = $unitPrice * $item->quantity;
                                                                        $ordCurr = $order->currency ?? 'PHP';
                                                                        $ordSym = ($ordCurr === 'USD' ? '$' : '₱');
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $index + 1 }}</td>
                                                                        <td class="fw-bold text-dark">{{ $itemName }}</td>
                                                                        <td class="text-center font-monospace small"><i class="las la-barcode me-1 opacity-50"></i>{{ $barcode }}</td>
                                                                        <td class="text-center fw-bold text-primary">{{ $item->quantity }}</td>
                                                                        <td class="text-end">{{ $ordSym }}{{ number_format($unitPrice, 2) }}</td>
                                                                        <td class="text-end fw-bold">{{ $ordSym }}{{ number_format($subtotal, 2) }}</td>
                                                                    </tr>
                                                                    @empty
                                                                    <tr>
                                                                        <td colspan="6" class="text-center text-muted py-3">No items listed.</td>
                                                                    </tr>
                                                                    @endforelse
                                                                </tbody>
                                                                <tfoot class="table-light">
                                                                    <tr>
                                                                        <th colspan="5" class="text-end fw-bold">Total Amount:</th>
                                                                        <th class="text-end fw-bold text-danger">{{ $ordSym }}{{ number_format($order->total_amount, 2) }}</th>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                                    <button type="button" class="btn btn-secondary shadow" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- TAB 2: FOR PICKUP -->
                            <div class="tab-pane fade" id="pickup-pane" role="tabpanel" aria-labelledby="pickup-tab">
                                <div class="table-responsive">
                                    <table id="pickupTable" class="display table scheduling-table mb-0" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>SI / DR Reference</th>
                                                <th>Reference Number</th>
                                                <th>Client & Team</th>
                                                <th>Pickup Location / Address</th>
                                                <th>Doc Status</th>
                                                <th>Pickup Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($pickupOrders as $order)
                                            @php
                                                $orderTeam = $order->sales_team ?? ($order->team ?? ($order->preparedBy?->sales_team ?? ($order->customer?->customer_type ?? '')));

                                                $siObj = $order->invoice ?: \App\Models\SalesInvoice::where('so_id', $order->id)->orWhere('so_number', $order->so_number)->first();
                                                $siNum = $siObj?->si_number ?: ($order->si_number ?: ($order->si_prepared_at ? ('SI-' . $order->so_number) : null));

                                                $drObj = $order->deliveryReceipt ?: \App\Models\DeliveryReceipt::where('so_id', $order->id)->orWhere('so_number', $order->so_number)->first();
                                                $drNum = $drObj?->dr_number ?: ($order->dr_number ?: ($order->dr_prepared_at ? ('DR-' . $order->so_number) : ('DR-' . $order->so_number)));

                                                $hasSI = !empty($siNum);
                                                $hasDR = !empty($drNum);

                                                $refDisplay = $hasSI ? $siNum : ($hasDR ? $drNum : $order->so_number);
                                                $isCompleted = ($order->status === 'completed');
                                            @endphp
                                            <tr>
                                                <td class="align-middle">
                                                    <span class="text-black font-w600 d-block">{{ $refDisplay }}</span>
                                                    <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                                                </td>
                                                <td class="align-middle">
                                                    @php
                                                        $refVal = !empty($order->ref_number) ? $order->ref_number : (!empty($order->reference_number) ? $order->reference_number : null);
                                                    @endphp
                                                    @if($refVal)
                                                        <span class="badge bg-dark text-white font-monospace fs-13 px-2 py-1 fw-bold shadow-sm" title="Reference Number">
                                                            <i class="las la-hashtag me-1 text-warning"></i>{{ $refVal }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted font-w500 fs-13">—</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    <span class="text-black d-block">{{ $order->customer->customer_name ?? 'N/A' }}</span>
                                                    @if(!empty($orderTeam))
                                                        <span class="badge bg-light text-primary border font-w500 fs-11 mt-1"><i class="fas fa-users me-1"></i>{{ $orderTeam }}</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge bg-light text-dark border"><i class="las la-store-alt me-1 text-primary"></i>Office Pickup</span>
                                                    <div class="small text-muted mt-1" title="{{ $order->shipping_address ?? $order->billing_address ?? '' }}">
                                                        {{ $order->shipping_address ?? $order->billing_address ?? 'Pickup at Main Office' }}
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    @if($hasSI)
                                                        <span class="badge bg-success font-monospace fs-12 px-2 py-1" title="Sales Invoice Number">
                                                            <i class="las la-file-invoice me-1"></i>{{ $siNum }}
                                                        </span>
                                                    @elseif($hasDR)
                                                        <span class="badge bg-info text-white font-monospace fs-12 px-2 py-1" title="Delivery Receipt Number">
                                                            <i class="las la-shipping-fast me-1"></i>{{ $drNum }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning text-dark fs-12 px-2 py-1">
                                                            <i class="las la-clock me-1"></i>Pending
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    @if($isCompleted)
                                                        <span class="badge bg-success px-3 py-2 fs-12"><i class="fas fa-check-circle me-1"></i>Picked Up & Completed</span>
                                                        @if($order->picked_up_at)
                                                            <small class="d-block text-muted mt-1">{{ \Carbon\Carbon::parse($order->picked_up_at)->format('M d, Y h:i A') }}</small>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-primary px-3 py-2 fs-12"><i class="las la-store-alt me-1"></i>Ready for Pickup</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle text-end">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <button type="button" 
                                                                class="btn btn-primary shadow btn-xs sharp" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#viewOrderModalPickup{{ $order->id }}" 
                                                                title="View Order Details & Remarks">
                                                            <i class="fas fa-eye"></i>
                                                        </button>

                                                        @if(!$isCompleted)
                                                            <form action="{{ route('production.logistic.mark-as-picked-up', $order->id) }}" method="POST" onsubmit="return confirm('Confirm that customer has picked up this order?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success shadow btn-xs sharp" title="Mark Completed (Picked Up)">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <form action="{{ route('production.logistic.move-back-to-delivery', $order->id) }}" method="POST" onsubmit="return confirm('Move this order back to Landtrip Manifest delivery?');">
                                                            @csrf
                                                            <button type="submit" class="btn btn-warning shadow btn-xs sharp" title="Move back to Landtrip Manifest">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr class="empty-row">
                                                <td colspan="7" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="las la-store-alt fs-50 mb-3 d-block opacity-25"></i>
                                                        No orders currently marked for office pickup.
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Render Modals for Pickup Orders outside table -->
                                @foreach($pickupOrders as $order)
                                    @php
                                        $orderTeam = $order->sales_team ?? ($order->team ?? ($order->preparedBy?->sales_team ?? ($order->customer?->customer_type ?? '')));
                                        $siObj = $order->invoice ?: \App\Models\SalesInvoice::where('so_id', $order->id)->orWhere('so_number', $order->so_number)->first();
                                        $siNum = $siObj?->si_number ?: ($order->si_number ?: ($order->si_prepared_at ? ('SI-' . $order->so_number) : null));
                                        $drObj = $order->deliveryReceipt ?: \App\Models\DeliveryReceipt::where('so_id', $order->id)->orWhere('so_number', $order->so_number)->first();
                                        $drNum = $drObj?->dr_number ?: ($order->dr_number ?: ($order->dr_prepared_at ? ('DR-' . $order->so_number) : ('DR-' . $order->so_number)));
                                        $hasSI = !empty($siNum);
                                        $hasDR = !empty($drNum);
                                        $refDisplay = $hasSI ? $siNum : ($hasDR ? $drNum : $order->so_number);
                                        $isCompleted = ($order->status === 'completed');
                                    @endphp
                                    <div class="modal fade" id="viewOrderModalPickup{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-primary text-white border-0 py-3">
                                                    <h5 class="modal-title text-white fw-bold">
                                                        <i class="las la-store-alt me-2"></i>Pickup Order Details: {{ $refDisplay }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4 text-start">
                                                    <!-- Status & Reference Badges -->
                                                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                                        <div>
                                                            @php
                                                                $refVal = !empty($order->ref_number) ? $order->ref_number : (!empty($order->reference_number) ? $order->reference_number : null);
                                                            @endphp
                                                            <span class="fw-bold text-dark fs-15 me-2">SO #: {{ $order->so_number }}</span>
                                                            @if($refVal)
                                                                <span class="badge bg-dark text-white font-monospace fs-13 px-2 py-1 me-1 shadow-sm fw-bold"><i class="las la-hashtag me-1 text-warning"></i>Ref #: {{ $refVal }}</span>
                                                            @endif
                                                            @if($hasSI)
                                                                <span class="badge bg-success font-monospace me-1">SI #: {{ $siNum }}</span>
                                                            @endif
                                                            @if($hasDR)
                                                                <span class="badge bg-info text-white font-monospace me-1">DR #: {{ $drNum }}</span>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <span class="badge bg-{{ $isCompleted ? 'success' : 'primary' }} px-3 py-2 fs-12">
                                                                {{ $isCompleted ? 'PICKED UP & COMPLETED' : 'READY FOR PICKUP' }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Info Grid -->
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded border h-100">
                                                                <h6 class="fw-bold text-primary mb-2"><i class="las la-user-circle me-1"></i>Customer Details</h6>
                                                                @if($refVal)
                                                                    <div class="mb-2 fs-13"><strong class="text-dark">Reference No.:</strong> <span class="badge bg-warning text-dark font-monospace fs-13 px-2 py-1 ms-1 fw-bold border border-warning"><i class="las la-hashtag me-1"></i>{{ $refVal }}</span></div>
                                                                @endif
                                                                <div class="small mb-1"><strong>Client:</strong> {{ $order->customer->customer_name ?? 'N/A' }}</div>
                                                                <div class="small mb-1"><strong>Contact:</strong> {{ $order->customer_contact ?? ($order->customer->phone ?? 'N/A') }}</div>
                                                                <div class="small mb-1"><strong>Address:</strong> {{ $order->shipping_address ?? ($order->billing_address ?? 'Same as Billing') }}</div>
                                                                @if(!empty($orderTeam))
                                                                    <div class="small mt-1"><strong>Team:</strong> <span class="badge bg-white text-primary border"><i class="fas fa-users me-1"></i>{{ $orderTeam }}</span></div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded border h-100">
                                                                <h6 class="fw-bold text-primary mb-2"><i class="las la-store-alt me-1"></i>Pickup Details</h6>
                                                                <div class="small mb-1"><strong>Delivery Mode:</strong> Office Pickup</div>
                                                                <div class="small mb-1"><strong>Status:</strong> {{ $isCompleted ? 'Completed' : 'Pending Pickup' }}</div>
                                                                @if($order->picked_up_at)
                                                                    <div class="small mb-1"><strong>Picked Up At:</strong> {{ \Carbon\Carbon::parse($order->picked_up_at)->format('M d, Y h:i A') }}</div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Remarks / Notes Box -->
                                                    <div class="mb-3">
                                                        <div class="p-3 rounded border border-warning" style="background-color: #fff9e6;">
                                                            <h6 class="fw-bold text-warning mb-1" style="color: #b7791f !important;"><i class="las la-comment-alt me-1"></i>Remarks & Special Instructions</h6>
                                                            <p class="mb-0 small text-dark font-w500" style="white-space: pre-line;">{{ $order->remarks ?: ($order->freight_notes ?: 'No special remarks or instructions provided.') }}</p>
                                                        </div>
                                                    </div>

                                                    <!-- Items Table -->
                                                    <div class="mb-2">
                                                        <h6 class="fw-bold text-dark mb-2"><i class="las la-boxes me-1 text-primary"></i>Items Breakdown</h6>
                                                        <div class="table-responsive border rounded">
                                                            <table class="table table-sm table-striped mb-0 text-start align-middle" style="font-size: 13px;">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Item Description</th>
                                                                        <th class="text-center">Barcode</th>
                                                                        <th class="text-center">Qty</th>
                                                                        <th class="text-end">Unit Price</th>
                                                                        <th class="text-end">Total</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse($order->items->filter(fn($i) => (float)($i->quantity ?? 0) > 0) as $index => $item)
                                                                    @php
                                                                        $itemName = $item->bookIndex?->display_name ?? ($item->book?->name ?? ($item->bundle?->name ?? ($item->product_name ?? 'N/A')));
                                                                        $barcode = $item->bookIndex?->barcode ?? ($item->book?->barcode ?? ($item->bundle?->barcode ?? '—'));
                                                                        $unitPrice = $item->unit_price ?? ($item->price ?? 0);
                                                                        $subtotal = $unitPrice * $item->quantity;
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $index + 1 }}</td>
                                                                        <td class="fw-bold text-dark">{{ $itemName }}</td>
                                                                        <td class="text-center font-monospace small"><i class="las la-barcode me-1 opacity-50"></i>{{ $barcode }}</td>
                                                                        <td class="text-center fw-bold text-primary">{{ $item->quantity }}</td>
                                                                        <td class="text-end">₱{{ number_format($unitPrice, 2) }}</td>
                                                                        <td class="text-end fw-bold">₱{{ number_format($subtotal, 2) }}</td>
                                                                    </tr>
                                                                    @empty
                                                                    <tr>
                                                                        <td colspan="6" class="text-center text-muted py-3">No items listed.</td>
                                                                    </tr>
                                                                    @endforelse
                                                                </tbody>
                                                                <tfoot class="table-light">
                                                                    <tr>
                                                                        <th colspan="5" class="text-end fw-bold">Total Amount:</th>
                                                                        <th class="text-end fw-bold text-danger">{{ $ordSym }}{{ number_format($order->total_amount, 2) }}</th>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                                    <button type="button" class="btn btn-secondary shadow" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- TAB 3: APPROVED PICK-UP / DELIVERY REQUESTS -->
                            <div class="tab-pane fade" id="requests-pane" role="tabpanel" aria-labelledby="requests-tab">
                                <div class="table-responsive">
                                    <table id="approvedRequestsTable" class="display table scheduling-table mb-0" style="width: 100%">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Ref #</th>
                                                <th>Type</th>
                                                <th>Client / Receiver</th>
                                                <th>Address / Location</th>
                                                <th>Requested Date</th>
                                                <th>Driver & Vehicle</th>
                                                <th>Items Details</th>
                                                <th>Remarks</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($approvedRequests as $req)
                                            <tr>
                                                <td class="align-middle">
                                                    <span class="text-black font-w600">REQ-{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    @if($req->type === 'delivery')
                                                        <span class="badge bg-light text-primary border"><i class="las la-truck me-1"></i>Delivery</span>
                                                    @elseif($req->type === 'pickup')
                                                        <span class="badge bg-light text-purple border" style="color: #8e24aa;"><i class="las la-store-alt me-1"></i>Pickup</span>
                                                    @else
                                                        <span class="badge bg-light text-warning border" style="color: #e65100; border-color: #ffe0b2 !important;"><i class="las la-arrow-circle-down me-1"></i>Pull Out</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    <span class="text-black d-block font-w600">{{ $req->client_name }}</span>
                                                    <small class="text-muted">By: {{ $req->createdByUser->name ?? 'N/A' }}</small>
                                                </td>
                                                <td class="align-middle" title="{{ $req->address }}">
                                                    {{ \Illuminate\Support\Str::limit($req->address, 50) }}
                                                </td>
                                                <td class="align-middle">
                                                    <i class="las la-calendar me-1 text-danger"></i>
                                                    {{ $req->requested_date->format('M d, Y') }}
                                                </td>
                                                <td class="align-middle">
                                                    @php
                                                        $dName = $req->driver_name ?: ($req->driver ? ($req->driver->first_name . ' ' . $req->driver->last_name) : '');
                                                    @endphp
                                                    @if($dName)
                                                        <span class="text-black font-w600 d-block"><i class="las la-user-tag me-1 text-primary"></i>{{ $dName }}</span>
                                                    @endif
                                                    @if($req->vehicle)
                                                        <small class="text-muted d-block"><i class="las la-truck me-1 text-success"></i>{{ $req->vehicle }}</small>
                                                    @elseif(!$dName)
                                                        <span class="badge bg-light text-secondary border">Unassigned</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle" title="{{ $req->items_details }}">
                                                    {{ \Illuminate\Support\Str::limit($req->items_details, 40) }}
                                                </td>
                                                <td class="align-middle">
                                                    {{ $req->remarks ?: '—' }}
                                                </td>
                                                <td class="align-middle text-end">
                                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#assignDriverRequestModal{{ $req->id }}" title="Assign Driver & Vehicle">
                                                            <i class="las la-user-plus me-1"></i> Assign Driver
                                                        </button>
                                                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#viewRequestModal{{ $req->id }}">
                                                            <i class="las la-eye me-1"></i> View
                                                        </button>

                                                        @if($req->status === 'completed')
                                                            <span class="badge bg-success text-white px-3 py-2 rounded-pill"><i class="las la-check-double me-1"></i> Completed</span>
                                                        @else
                                                            <form action="{{ route('production.logistic.pickup-requests.complete', $req->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Mark this request as completed?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm" style="background-color: #198754; border-color: #198754;">
                                                                    <i class="las la-check-double me-1 fs-14"></i> Set Completed
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>

                                                    <!-- Assign Driver Modal -->
                                                    <div class="modal fade" id="assignDriverRequestModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content text-start">
                                                                <form action="{{ route('production.logistic.pickup-requests.assign-driver', $req->id) }}" method="POST">
                                                                    @csrf
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Assign Driver & Vehicle — REQ-{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body text-start">
                                                                        <div class="mb-3 text-start">
                                                                            <label class="form-label fw-bold text-dark">Select Driver</label>
                                                                            <select class="form-select" name="driver_id">
                                                                                <option value="">-- Select Driver (Optional) --</option>
                                                                                @foreach($drivers as $d)
                                                                                    <option value="{{ $d->id }}" {{ $req->driver_id == $d->id ? 'selected' : '' }}>
                                                                                        {{ $d->first_name }} {{ $d->last_name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-3 text-start">
                                                                            <label class="form-label fw-bold text-dark">Vehicle / Plate No.</label>
                                                                            <input type="text" class="form-control" name="vehicle" value="{{ old('vehicle', $req->vehicle) }}" placeholder="e.g., L300 / NBO 1234, Motorcycle">
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary shadow-sm rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn btn-primary shadow-sm rounded-pill px-4">Save Assignment</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Details View Modal -->
                                                    <div class="modal fade" id="viewRequestModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content text-start">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Logistics Service Order Details</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body text-start">
                                                                    <div class="mb-3 text-start">
                                                                        <strong>Type:</strong> <span class="badge {{ $req->type === 'delivery' ? 'bg-primary text-white' : 'text-white' }}" style="{{ $req->type === 'pickup' ? 'background-color: #6f42c1 !important;' : ($req->type === 'pull_out' ? 'background-color: #f57c00 !important;' : '') }}">{{ str_replace('_', ' ', ucfirst($req->type)) }}</span>
                                                                    </div>
                                                                    <div class="mb-3 text-start">
                                                                        <strong>Client / Receiver Name:</strong>
                                                                        <div>{{ $req->client_name }}</div>
                                                                    </div>
                                                                    <div class="mb-3 text-start">
                                                                        <strong>Address / Location:</strong>
                                                                        <div>{{ $req->address }}</div>
                                                                    </div>
                                                                    <div class="mb-3 text-start">
                                                                        <strong>Requested Date:</strong>
                                                                        <div>{{ $req->requested_date->format('M d, Y') }}</div>
                                                                    </div>
                                                                    @if($req->driver_name || $req->vehicle)
                                                                    <div class="mb-3 text-start">
                                                                        <strong>Assigned Driver & Vehicle:</strong>
                                                                        <div>
                                                                            @if($req->driver_name)
                                                                                <span class="fw-semibold text-dark"><i class="las la-user-tag me-1 text-primary"></i>{{ $req->driver_name }}</span>
                                                                            @endif
                                                                            @if($req->vehicle)
                                                                                <span class="ms-2 text-muted"><i class="las la-truck me-1 text-success"></i>{{ $req->vehicle }}</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    @endif
                                                                    <div class="mb-3 text-start">
                                                                        <strong>Items Details:</strong>
                                                                        <div class="bg-light p-2 rounded" style="white-space: pre-wrap;">{{ $req->items_details }}</div>
                                                                    </div>
                                                                    @if($req->remarks)
                                                                    <div class="mb-3 text-start">
                                                                        <strong>Remarks:</strong>
                                                                        <div>{{ $req->remarks }}</div>
                                                                    </div>
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary shadow-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr class="empty-row">
                                                <td colspan="9" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="las la-clipboard-list fs-50 mb-3 d-block opacity-25"></i>
                                                        No approved pickup or delivery requests.
                                                    </div>
                                                </td>
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

    <!-- Floating Sticky Bulk Action Bar for Setting as For Pickup -->
    <div id="bulkPickupFloatingBar" class="pickup-bulk-floating-bar hidden">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger rounded-pill px-3 py-2 fs-13 fw-bold" id="selectedPickupCountBadge">0</span>
            <span class="fw-bold text-dark fs-14">Sales Order(s) selected</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" id="btnSubmitBulkPickupFloating" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold shadow-sm" style="background:#ff0000; border-color:#ff0000; height: 38px;">
                <i class="las la-store-alt me-1 fs-16"></i> Set Selected as For Pickup
            </button>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Remove empty row with colspan before DataTable initialization to prevent _DT_CellIndex crash
            $('#deliveryTable tbody tr.empty-row').remove();
            $('#pickupTable tbody tr.empty-row').remove();
            $('#approvedRequestsTable tbody tr.empty-row').remove();

            const deliveryTable = $('#deliveryTable').DataTable({
                order: [[1, 'desc']],
                pageLength: 25,
                columnDefs: [{ orderable: false, targets: [0, 11] }],
                language: {
                    emptyTable: "No orders currently ready for Landtrip delivery schedule.",
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                }
            });

            const pickupTable = $('#pickupTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 25,
                columnDefs: [{ orderable: false, targets: 6 }],
                language: {
                    emptyTable: "No orders currently marked for office pickup.",
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                }
            });

            const approvedRequestsTable = $('#approvedRequestsTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 25,
                columnDefs: [{ orderable: false, targets: 7 }],
                language: {
                    emptyTable: "No approved pickup or delivery requests.",
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                }
            });

            // State tracking for selected Sales Orders for Pickup across pagination & filters
            const selectedPickupOrderIds = new Set();

            function updatePickupToolbarState() {
                const checkedCount = selectedPickupOrderIds.size;
                $('#selectedPickupCountBadge').text(checkedCount);

                if (checkedCount > 0) {
                    $('#bulkPickupFloatingBar').removeClass('hidden');
                } else {
                    $('#bulkPickupFloatingBar').addClass('hidden');
                }

                // Update selectAll header checkbox status for currently visible page
                const visibleCheckboxes = $('#deliveryTable tbody .landtrip-checkbox');
                if (visibleCheckboxes.length > 0) {
                    const visibleChecked = visibleCheckboxes.filter(':checked').length;
                    if (visibleChecked === visibleCheckboxes.length) {
                        $('#selectAllLandtrip').prop('checked', true).prop('indeterminate', false);
                    } else if (visibleChecked > 0) {
                        $('#selectAllLandtrip').prop('checked', false).prop('indeterminate', true);
                    } else {
                        $('#selectAllLandtrip').prop('checked', false).prop('indeterminate', false);
                    }
                } else {
                    $('#selectAllLandtrip').prop('checked', false).prop('indeterminate', false);
                }
            }

            // Restore checkbox states when DataTables redraws (page change, search, sort)
            function syncPickupCheckboxesOnDraw() {
                $('#deliveryTable tbody .landtrip-checkbox').each(function() {
                    const id = $(this).val();
                    $(this).prop('checked', selectedPickupOrderIds.has(id));
                });
                updatePickupToolbarState();
            }

            deliveryTable.on('draw', function() {
                syncPickupCheckboxesOnDraw();
            });

            // Tab switch listener
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                updatePickupToolbarState();
            });

            // Header select all checkbox
            $('#selectAllLandtrip').on('change', function() {
                const isChecked = $(this).is(':checked');

                deliveryTable.rows({ filter: 'applied' }).nodes().to$().find('.landtrip-checkbox').each(function() {
                    const id = $(this).val();
                    $(this).prop('checked', isChecked);
                    if (isChecked) {
                        selectedPickupOrderIds.add(id);
                    } else {
                        selectedPickupOrderIds.delete(id);
                    }
                });

                updatePickupToolbarState();
            });

            // Individual checkbox change
            $(document).on('change', '.landtrip-checkbox', function() {
                const id = $(this).val();
                if ($(this).is(':checked')) {
                    selectedPickupOrderIds.add(id);
                } else {
                    selectedPickupOrderIds.delete(id);
                }
                updatePickupToolbarState();
            });


            // Bulk order pickup button click from floating toolbar
            $('#btnSubmitBulkPickupFloating').on('click', function(e) {
                e.preventDefault();

                if (selectedPickupOrderIds.size === 0) {
                    alert('Please select at least one Sales Order using the checkboxes.');
                    return false;
                }

                if (!confirm('Move ' + selectedPickupOrderIds.size + ' selected order(s) to For Pickup tab?')) {
                    return false;
                }

                const $form = $('#bulkPickupForm');
                $form.find('input[name="order_ids[]"]').remove();

                selectedPickupOrderIds.forEach(function(id) {
                    $form.append(
                        $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', 'order_ids[]')
                            .val(id)
                    );
                });

                $form.submit();
            });

            // Custom DataTables filter for Delivery Scheduling
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'deliveryTable') return true;

                const rowNode = settings.aoData[dataIndex] ? settings.aoData[dataIndex].nTr : null;
                if (!rowNode) return true;
                const selectedDriver = ($('#driverFilter').val() || 'all').toLowerCase().trim();
                const selectedTeam = ($('#teamFilter').val() || 'all').toLowerCase().trim();
                const selectedStatus = ($('#statusFilter').val() || 'all').toLowerCase().trim();

                const rowDriver = ($(rowNode).data('driver') || '').toString().toLowerCase();
                const hasDriver = ($(rowNode).data('has-driver') || 'no').toString();
                const isScheduled = ($(rowNode).data('scheduled') || 'no').toString();
                const rowStatus = ($(rowNode).data('status') || 'ready').toString().toLowerCase();
                const rowTeam = ($(rowNode).data('team') || '').toString().toLowerCase();

                // 1. Driver match
                let driverMatch = true;
                if (selectedDriver !== 'all') {
                    if (selectedDriver === 'unassigned') {
                        driverMatch = (hasDriver === 'no');
                    } else {
                        driverMatch = rowDriver.includes(selectedDriver);
                    }
                }

                // 2. Team match
                let teamMatch = true;
                if (selectedTeam !== 'all') {
                    teamMatch = rowTeam.includes(selectedTeam);
                }

                // 3. Status match
                let statusMatch = true;
                if (selectedStatus !== 'all') {
                    if (selectedStatus === 'scheduled') {
                        statusMatch = (isScheduled === 'yes');
                    } else if (selectedStatus === 'not_scheduled') {
                        statusMatch = (isScheduled === 'no');
                    } else {
                        statusMatch = (rowStatus === selectedStatus);
                    }
                }

                return driverMatch && teamMatch && statusMatch;
            });

            $('#driverFilter, #teamFilter, #statusFilter').on('change', function() {
                deliveryTable.draw();
            });

            $('#resetSchedulingFilters').on('click', function() {
                $('#driverFilter').val('all');
                $('#teamFilter').val('all');
                $('#statusFilter').val('all');
                deliveryTable.draw();
            });
        });
    </script>
    @endpush
</x-app-layout>
