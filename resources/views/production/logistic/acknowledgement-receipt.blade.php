<x-app-layout :title="'Acknowledgement Receipt'" :sidebar="'production'">

    @push('styles')
    <style>
        /* ── SO detail styles (mirrored from marketing/sales-orders/detail) ── */
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
        .so-order-table thead{ background:#ff0000; color:#fff; }
        .so-order-table th,
        .so-order-table td   { padding:0.6rem 0.75rem; border:1px solid #ddd; font-size:0.875rem; }
        .so-order-table tfoot{ background:#f8f9fa; font-weight:600; }
        .pick-qty-col        { background:#fff8f0 !important; }
        .pick-qty-col input  { border:2px solid #ffa500; background:#fff8f0; font-weight:700; color:#7b3f00; text-align:center; border-radius:4px; padding:3px 6px; width:75px; }
        .pick-qty-col input:focus { outline:none; border-color:#cc7000; box-shadow:0 0 0 3px rgba(255,165,0,.2); }
        .pick-qty-head       { background:#ffa500 !important; color:#7b3f00 !important; }
        /* Responsive for smaller modals */
        @media (max-width:576px) { .so-customer-section { grid-template-columns:1fr; } }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex">
                    <div>
                        <h4 class="fs-20 mb-0 text-black">Acknowledgement Receipt</h4>
                        <small class="text-muted">Area Sales Consignment — Import Pick Quantities &amp; Customer Name</small>
                    </div>
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
                        <table id="ackTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>SO Number</th>
                                    <th>Area Sales Staff</th>
                                    <th>Order Date</th>
                                    <th>Terms</th>
                                    <th>Items</th>
                                    <th>Total Amount</th>
                                    <th>Customer</th>
                                    <th>Proof of Payment</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td><strong>{{ $order->so_number }}</strong></td>
                                    <td>{{ optional($order->areaSalesStaff)->name ?? '—' }}</td>
                                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $order->terms ?? '—' }}</td>
                                    <td>{{ $order->items->count() }}</td>
                                    <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        @if($order->customer)
                                            <span class="badge bg-success">{{ $order->customer->customer_name }}</span>
                                        @else
                                            <span class="badge bg-secondary">No customer yet</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->proof_of_payment)
                                            <a href="{{ asset('storage/' . $order->proof_of_payment) }}" target="_blank" class="badge bg-info text-white text-decoration-none" title="View Proof of Payment">
                                                <i class="las la-file-alt me-1"></i> View POP
                                            </a>
                                        @else
                                            <span class="badge bg-secondary">No POP</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $smap = [
                                                'draft'                => ['Draft',                'bg-secondary'],
                                                'pending_mkt_approval' => ['Pending Mkt Approval', 'bg-warning text-dark'],
                                                'mkt_approved'         => ['Mkt Approved',         'bg-info'],
                                                'picking'              => ['Picking',               'bg-primary'],
                                                'ready_for_delivery'   => ['Ready For Delivery',   'bg-success'],
                                                'ar_created'           => ['AR Created',           'bg-info text-white'],
                                                'cr_created'           => ['CR Created',           'bg-success text-white'],
                                                'completed'            => ['Completed',             'bg-success'],
                                                'cancelled'            => ['Cancelled',             'bg-danger'],
                                            ];
                                            [$slabel, $sclass] = $smap[$order->status] ?? [ucwords(str_replace('_',' ',$order->status)), 'bg-secondary'];
                                        @endphp
                                        <span class="badge {{ $sclass }}">{{ $slabel }}</span>
                                    </td>
                                    <td>
                                        <div class="workflow-actions">
                                            <a href="#" class="btn btn-primary shadow btn-xs sharp me-1"
                                               title="View / Enter Pick Quantities"
                                               data-bs-toggle="modal" data-bs-target="#pickModal{{ $order->id }}">
                                                <i class="las la-edit"></i>
                                            </a>
                                            <form action="{{ route('production.logistic.move-to-si', $order->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-success shadow btn-xs sharp" title="Move to SI (Prepare Sales Invoice)">
                                                    <i class="las la-file-invoice"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No Area Sales Consignment orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         Per-SO Modals
    ══════════════════════════════════════════════════════════════ --}}
    @foreach($orders as $order)

    {{-- ── Pick Qty Modal (looks like actual SO detail) ── --}}
    <div class="modal fade" id="pickModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content so-modal-form">
                <form method="POST" action="{{ route('production.logistic.acknowledgement-receipt.import') }}">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">

                    <div class="modal-header" style="background:#fff; border-bottom:2px solid #e0e0e0;">
                        <div style="flex:1;">
                            {{-- Company header --}}
                            <div class="so-company-info">
                                <div class="so-company-logo">C</div>
                                <div>
                                    <div class="so-company-name">Claretian Communications Foundation Inc.</div>
                                    <div class="so-company-sub">8 Mayumi St., UP Village, Diliman, Quezon City &nbsp;|&nbsp; Tel. No.: 921-3984</div>
                                </div>
                            </div>
                            <div class="so-document-title">
                                SALES ORDER &nbsp;<span class="text-danger">#{{ $order->so_number }}</span>
                            </div>
                            <div class="text-center text-uppercase fw-bold text-primary" style="font-size:0.8rem;">
                                Area Sales Consignment
                            </div>
                        </div>
                        <button type="button" class="btn-close align-self-start" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        {{-- Customer + Order Info section --}}
                        <div class="so-customer-section">
                            <div class="so-customer-details">
                                <h5 class="text-black fw-bold">Customer Information</h5>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="fw-bold text-dark" style="width:130px;">Customer Name:</td>
                                        <td class="fw-bold text-black">
                                            {{ $order->customer_representative ?: ($order->customer?->customer_name ?? '—') }}
                                            @if($order->customer)
                                                @if($order->customer->isBadClient)
                                                    <span class="badge bg-danger ms-1">BAD CLIENT</span>
                                                @else
                                                    <span class="badge bg-success ms-1">GOOD CLIENT</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary ms-1">Not yet set</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-dark">Contact:</td>
                                        <td class="text-black">{{ $order->customer_contact ?: ($order->customer?->mobile ?: ($order->customer?->main_phone ?: 'N/A')) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-dark">Area Sales Staff:</td>
                                        <td>
                                            <span class="badge" style="background:#1a7a3e; font-size:11px;">
                                                {{ $order->areaSalesStaff?->name ?? '—' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-dark">Company:</td>
                                        <td class="text-black">{{ $order->customer?->company_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-dark">Address:</td>
                                        <td class="text-black">{{ $order->customer?->shipping_address ?? $order->customer?->billing_address ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="so-order-details">
                                <h5 class="text-black fw-bold">Order Information</h5>
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
                                        <td class="fw-bold text-dark">Status:</td>
                                        <td><span class="badge bg-info text-white">{{ strtoupper(str_replace('_',' ',$order->status)) }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-dark">Prepared By:</td>
                                        <td class="text-black">{{ $order->preparedBy?->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-dark">Total Amount:</td>
                                        <td class="text-black fw-bold text-danger">₱{{ number_format($order->total_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-dark">Remarks:</td>
                                        <td class="text-black">{{ $order->remarks ?? 'None' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-dark">Proof of Payment:</td>
                                        <td>
                                            @if($order->proof_of_payment)
                                                <a href="{{ asset('storage/' . $order->proof_of_payment) }}" target="_blank" class="btn btn-xs btn-outline-info">
                                                    <i class="las la-external-link-alt me-1"></i> View Attached POP
                                                </a>
                                            @else
                                                <span class="text-muted small">Not attached</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- Items Table (same style as SO detail, + Pick Qty column) --}}
                        <table class="so-order-table">
                            <thead>
                                <tr>
                                    <th style="width:50px;">QTY</th>
                                    <th style="width:80px;">UNIT</th>
                                    <th>DESCRIPTION</th>
                                    <th style="width:110px;">ISBN</th>
                                    <th style="width:130px;">UNIT PRICE</th>
                                    <th style="width:130px;">AMOUNT</th>
                                    <th class="pick-qty-head" style="width:100px; text-align:center;">PICK QTY</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->items as $i => $item)
                                <tr>
                                    <td class="text-center">{{ (int) $item->quantity }}</td>
                                    <td class="text-center text-uppercase">{{ optional($item->book)->unit ?? 'pcs' }}</td>
                                    <td><div class="fw-bold">{{ optional($item->book)->name ?? 'Unknown Product' }}</div></td>
                                    <td>{{ $item->isbn ?? optional($item->book)->isbn ?? '—' }}</td>
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
                                <tr><td colspan="7" class="text-center text-muted py-3">No items.</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-end text-uppercase"><strong>Grand Total:</strong></td>
                                    <td class="text-end fw-bold" style="font-size:1rem;">₱{{ number_format($order->total_amount, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="modal-footer d-flex justify-content-between" style="border-top:2px solid #e0e0e0;">
                        <div>
                            <form action="{{ route('production.logistic.move-to-si', $order->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="las la-file-invoice me-1"></i> Move to SI
                                </button>
                            </form>
                        </div>
                        <div>
                            <button type="button" class="btn btn-danger light me-1" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="las la-save me-1"></i> Save Pick Quantities
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @endforeach

    @push('styles-end')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    @endpush

    @push('scripts')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <style>
        .dataTables_wrapper { font-size: 13px; }
        #ackTable { font-size: 13px; }
        #ackTable thead th { padding: 8px 10px; font-weight: 600; font-size: 13px; }
        #ackTable tbody td { padding: 6px 10px; vertical-align: middle; }
        .workflow-actions { display: flex; gap: 3px; align-items: center; }
        .workflow-actions .btn {
            padding: 2px 4px !important; font-size: 10px !important;
            min-width: 24px !important; width: 24px !important; height: 24px !important;
            display: flex !important; align-items: center !important; justify-content: center !important;
        }
        .workflow-actions .btn i { margin: 0 !important; font-size: 12px !important; }
    </style>
    <script>
        $(document).ready(function () {
            $('#ackTable').DataTable({
                order: [[2, 'desc']],
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
            });
        });
    </script>
    @endpush

</x-app-layout>
