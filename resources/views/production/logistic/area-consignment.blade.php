<x-app-layout :title="'Area Consignment'" :sidebar="'production'">

    @push('styles')
    <style>
        .so-modal-form       { background:#fff; border-radius:8px; }
        .so-form-header      { margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:2px solid #e0e0e0; }
        .so-company-info     { display:flex; align-items:center; gap:1rem; margin-bottom:0.5rem; }
        .so-company-logo     { width:52px; height:52px; background:#ff0000; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.6rem; font-weight:bold; flex-shrink:0; }
        .so-company-name     { font-size:1rem; font-weight:700; color:#333; text-transform:uppercase; }
        .so-company-sub      { font-size:0.78rem; color:#666; }
        .so-document-title   { text-align:center; font-size:1.4rem; font-weight:700; color:#333; letter-spacing:1px; }
        .so-customer-section { display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.25rem; }
        .so-customer-details,
        .so-order-details    { background:#f8f9fa; padding:1.25rem; border-radius:6px; }
        .so-order-table      { width:100%; border-collapse:collapse; margin-bottom:1rem; }
        .so-order-table thead{ background:#1a5276; color:#fff; }
        .so-order-table th,
        .so-order-table td   { padding:0.6rem 0.75rem; border:1px solid #ddd; font-size:0.875rem; }
        .so-order-table tfoot{ background:#f8f9fa; font-weight:600; }
        .pick-qty-col        { background:#fff8f0 !important; }
        .pick-qty-col input  { border:2px solid #ffa500; background:#fff8f0; font-weight:700; color:#7b3f00; text-align:center; border-radius:4px; padding:3px 6px; width:75px; }
        .pick-qty-col input:focus { outline:none; border-color:#cc7000; box-shadow:0 0 0 3px rgba(255,165,0,.2); }
        .pick-qty-head       { background:#ffa500 !important; color:#7b3f00 !important; }
        .receipt-badge       { font-size: 11px; padding: 4px 8px; border-radius: 4px; font-weight: 600; }
        @media (max-width:576px) { .so-customer-section { grid-template-columns:1fr; } }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 d-block d-sm-flex justify-content-between align-items-center pt-4 pb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 48px; height: 48px; background: #eef2ff;">
                            <i class="las la-truck-loading fs-24 text-primary"></i>
                        </div>
                        <div>
                            <h4 class="fs-20 mb-0 text-black fw-bold">Consignment Delivery Receipt Management</h4>
                            <small class="text-muted">Workflow: Create SO &rarr; Approvals &rarr; Picklist &rarr; Delivery Receipt (AR / CR / SI Link)</small>
                        </div>
                    </div>

                    <!-- Search Form -->
                    <form action="{{ route('production.logistic.area-consignment') }}" method="GET" class="d-flex align-items-center gap-1">
                        <div style="width: 240px; height: 32px; display: flex; align-items: center; border: 1px solid #ced4da; border-radius: 4px; background-color: #fff; padding: 0 10px; box-sizing: border-box;">
                            <i class="fas fa-search text-muted me-2" style="font-size: 0.85rem;"></i>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search SO #, staff, customer..." value="{{ request('search') }}" 
                                   style="border: none !important; background: transparent !important; padding: 0 !important; height: 100%; font-size: 0.82rem; color: #333; outline: none !important; box-shadow: none !important;">
                            @if(request('search'))
                                <a href="{{ route('production.logistic.area-consignment') }}" class="text-muted d-inline-flex align-items-center ms-1" title="Clear search" style="text-decoration: none;">
                                    <i class="fas fa-times-circle" style="color: #999; font-size: 0.9rem; cursor: pointer;"></i>
                                </a>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-sm btn-danger text-white rounded d-inline-flex align-items-center justify-content-center gap-1" style="height: 32px; padding: 0 12px; font-size: 0.8rem; background-color: #D9251C; border: none;">
                            <i class="fas fa-search" style="font-size: 0.8rem;"></i>
                            <span>Search</span>
                        </button>
                    </form>
                </div>
                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="las la-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="las la-exclamation-triangle me-2"></i>{{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="las la-exclamation-circle me-2"></i>
                            @foreach($errors->all() as $e){{ $e }}@endforeach
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="areaConsignmentTable" class="display table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>SO Number</th>
                                    <th>Area Sales Staff</th>
                                    <th>Order Date</th>
                                    <th>Terms</th>
                                    <th>Items</th>
                                    <th>Total Amount</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <strong>{{ $order->so_number }}</strong>
                                        <br><span class="badge bg-light text-dark border">Area Consignment</span>
                                    </td>
                                    <td>{{ optional($order->areaSalesStaff)->name ?? '—' }}</td>
                                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $order->terms ?? '—' }}</td>
                                    <td><span class="badge bg-info">{{ $order->items->count() }} items</span></td>
                                    <td class="fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        @if($order->customer)
                                            <span class="badge bg-success">{{ $order->customer->customer_name }}</span>
                                        @else
                                            <span class="badge bg-secondary">No customer yet</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $smap = [
                                                'draft'                => ['Draft',                'bg-secondary'],
                                                'pending_mkt_approval' => ['Pending Mkt Approval', 'bg-warning text-dark'],
                                                'mkt_approved'         => ['Mkt Approved',         'bg-info text-white'],
                                                'picking'              => ['Picking (Picklist)',   'bg-primary'],
                                                'pending_dr_prep'      => ['DR Preparation',       'bg-info text-white'],
                                                'ready_for_delivery'   => ['DR Prepared / Ready',  'bg-success'],
                                                'si_created'           => ['SI Created',            'bg-success'],
                                                'completed'            => ['Completed',             'bg-dark'],
                                                'cancelled'            => ['Cancelled',             'bg-danger'],
                                            ];
                                            [$slabel, $sclass] = $smap[$order->status] ?? [ucwords(str_replace('_',' ',$order->status)), 'bg-secondary'];
                                        @endphp
                                        <span class="badge {{ $sclass }}">{{ $slabel }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 align-items-center">
                                            {{-- Enter / View Pick Qty --}}
                                            <button class="btn btn-primary btn-xs shadow" title="Enter Pick Qty" data-bs-toggle="modal" data-bs-target="#pickModal{{ $order->id }}">
                                                <i class="las la-edit"></i>
                                            </button>

                                            {{-- View CR Form --}}
                                            <a href="{{ route('production.logistic.view-delivery-form', $order->id) }}?back=consignment" class="btn btn-outline-success btn-xs shadow d-flex align-items-center gap-1" title="View CR Form">
                                                <i class="las la-eye"></i> View
                                            </a>

                                            {{-- Link CR to Sales Invoice --}}
                                            <button class="btn btn-success btn-xs shadow d-flex align-items-center gap-1 text-white" title="Link to SI" data-bs-toggle="modal" data-bs-target="#linkSiModal{{ $order->id }}">
                                                <i class="las la-link"></i> Link to SI
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No Area Consignment orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                        <div class="text-muted small">
                            Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} entries
                        </div>
                        <div>
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals for each Order --}}
    @foreach($orders as $order)

    {{-- Pick Qty Modal --}}
    <div class="modal fade" id="pickModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content so-modal-form">
                <form method="POST" action="{{ route('production.logistic.acknowledgement-receipt.import') }}">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">

                    <div class="modal-header" style="background:#fff; border-bottom:2px solid #e0e0e0;">
                        <div style="flex:1;">
                            <div class="so-company-info">
                                <div class="so-company-logo">C</div>
                                <div>
                                    <div class="so-company-name">Claretian Communications Foundation Inc.</div>
                                    <div class="so-company-sub">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                                </div>
                            </div>
                            <div class="so-document-title">
                                AREA CONSIGNMENT SALES ORDER &nbsp;<span class="text-danger">#{{ $order->so_number }}</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close align-self-start" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="so-customer-section">
                            <div class="so-customer-details">
                                <h5 class="text-black fw-bold">Customer Information</h5>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="fw-bold text-dark" style="width:130px;">Company:</td>
                                        <td class="fw-bold text-black">{{ $order->customer?->company_name ?: ($order->customer?->customer_name ?? '—') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-dark">Customer Name:</td>
                                        <td class="fw-bold text-black">{{ $order->customer_representative ?: ($order->customer?->customer_name ?? '—') }}</td>
                                    </tr>
                                    @php
                                        $bName = $order->customer_representative;
                                        if (!$bName && $order->remarks && str_contains($order->remarks, 'Branch:')) {
                                            preg_match('/Branch:\s*([^|\n\r]+)/', $order->remarks, $m);
                                            $bName = trim($m[1] ?? '');
                                        }
                                        $bCompany = $bName ? \App\Models\Company::where('company_name', $bName)->first() : null;
                                        $displayAccountNo = $bCompany?->account_number ?: ($order->customer?->account_number ?? 'N/A');
                                    @endphp
                                    <tr>
                                        <td class="fw-bold text-dark">Account No:</td>
                                        <td class="text-black">{{ $displayAccountNo }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-dark">Contact:</td>
                                        <td class="text-black">{{ $order->customer_contact ?: ($order->customer?->mobile ?: ($order->customer?->main_phone ?: 'N/A')) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-dark">Area Sales Staff:</td>
                                        <td><span class="badge bg-primary">{{ $order->areaSalesStaff?->name ?? '—' }}</span></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="so-order-details">
                                <h5 class="text-black fw-bold">Order Details</h5>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="fw-bold text-dark">Order Date:</td>
                                        <td class="text-black">{{ $order->created_at->format('F d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-dark">Terms:</td>
                                        <td class="text-black">{{ $order->terms ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-dark">Remarks:</td>
                                        <td class="text-black fw-bold text-primary">{{ $order->remarks ?: '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-dark">Total Amount:</td>
                                        <td class="text-danger fw-bold">₱{{ number_format($order->total_amount, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <table class="so-order-table">
                            <thead>
                                <tr>
                                    <th style="width:50px;">QTY</th>
                                    <th>DESCRIPTION</th>
                                    <th style="width:130px;">UNIT PRICE</th>
                                    <th style="width:130px;">AMOUNT</th>
                                    <th class="pick-qty-head" style="width:100px; text-align:center;">PICK QTY</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->items as $i => $item)
                                <tr>
                                    <td class="text-center">{{ (int) $item->quantity }}</td>
                                    <td><div class="fw-bold">{{ optional($item->book)->name ?? 'Unknown Product' }}</div></td>
                                    <td class="text-end">₱{{ number_format($item->price, 2) }}</td>
                                    <td class="text-end fw-bold">₱{{ number_format($item->subtotal, 2) }}</td>
                                    <td class="text-center pick-qty-col">
                                        <input type="hidden" name="pick_items[{{ $i }}][item_id]" value="{{ $item->id }}">
                                        <input type="number"
                                               name="pick_items[{{ $i }}][pick_qty]"
                                               value="{{ $item->customer_selected_qty ?? '' }}"
                                               min="0" max="{{ (int) $item->quantity }}"
                                               placeholder="0">
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">No items.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"><i class="las la-save me-1"></i> Save Pick Qty</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Link to SI Modal --}}
    <div class="modal fade" id="linkSiModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('production.logistic.link-consignment-to-si', $order->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white"><i class="las la-link me-2"></i> Link Consignment Receipt to Sales Invoice (SI)</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">Select the quantities to link and upload the required proof of payment to generate a Sales Invoice (SI) for SO #{{ $order->so_number }}:</p>
                        
                        <div class="table-responsive my-2">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th style="width: 100px;">Ordered Qty</th>
                                        <th style="width: 120px;">Price</th>
                                        <th style="width: 120px;">Select Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ $item->book?->name ?? 'Item' }}</td>
                                        <td class="text-center">{{ (int)$item->quantity }}</td>
                                        <td class="text-end">₱{{ number_format($item->price, 2) }}</td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm text-center" 
                                                   name="items[{{ $item->id }}][selected_qty]" 
                                                   min="0" max="{{ (int)$item->quantity }}" 
                                                   value="{{ $item->customer_selected_qty ?? (int)$item->quantity }}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Upload Proof of Payment <span class="text-danger">*</span></label>
                            <input type="file" name="proof_of_payment" class="form-control form-control-sm" required accept=".pdf,.png,.jpg,.jpeg">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success"><i class="las la-check-circle me-1"></i> Generate Sales Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @endforeach

    @push('scripts')
    <script>
        $(document).ready(function () {
            if ($('#areaConsignmentTable').length) {
                $('#areaConsignmentTable').DataTable({
                    order: [[2, 'desc']],
                    pageLength: 25
                });
            }
        });
    </script>
    @endpush

</x-app-layout>
