<div class="row g-3 mb-4">
    <!-- Total Complimentary Orders Card -->
    <div class="col-xl-3 col-md-4 col-sm-6">
        <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                            <i class="las la-gift fs-20"></i>
                        </div>
                        <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                    </div>
                    <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Complimentary Orders</h6>
                    <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Total complimentary/donation orders registered.</p>
                </div>
                <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                    <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Count</span>
                    <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">{{ $complimentaryOrders->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Expense Valuation Card -->
    <div class="col-xl-3 col-md-4 col-sm-6">
        <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                            <i class="las la-wallet fs-20"></i>
                        </div>
                        <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                    </div>
                    <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Expense Valuation</h6>
                    <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Accrued valuation of issued items (COA 5100).</p>
                </div>
                <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                    <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Total</span>
                    <span class="fw-bold fs-15 text-danger" style="font-weight: 800 !important; color: #ef4444 !important;">₱{{ number_format($complimentaryTotalValuation, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Posted Expense Entries Card -->
    <div class="col-xl-3 col-md-4 col-sm-6">
        <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                            <i class="las la-file-invoice-dollar fs-20"></i>
                        </div>
                        <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                    </div>
                    <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Posted Expense Entries</h6>
                    <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Orders posted to the CCFI journal ledger.</p>
                </div>
                <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                    <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Count</span>
                    <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">{{ $complimentaryOrders->whereNotIn('status', ['draft', 'pending_mkt_approval', 'pending_acct_approval'])->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Deliveries Card -->
    <div class="col-xl-3 col-md-4 col-sm-6">
        <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                            <i class="las la-check-circle fs-20"></i>
                        </div>
                        <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                    </div>
                    <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Completed Deliveries</h6>
                    <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Delivered complimentary distributions.</p>
                </div>
                <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                    <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Count</span>
                    <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">{{ $complimentaryOrders->where('status', 'completed')->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border: 1px solid #e2e8f0;">
    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h5 class="fw-bold mb-0 text-dark fs-18"><i class="las la-gift me-2 text-danger"></i>Complimentary & Donation Receipts Ledger</h5>
            <p class="text-muted small mb-0">List of complimentary orders. These orders generate Acknowledgement Receipts and are posted to Expense (COA 5100), not Sales Revenue.</p>
        </div>
        <div class="badge bg-danger-subtle text-danger px-3 py-2 fw-bold" style="border-radius: 20px; font-size: 0.72rem; letter-spacing: 0.3px;">
            <i class="las la-info-circle me-1"></i> Non-Sales / Expense Category
        </div>
    </div>
    <div class="card-body pt-2">
        <div class="table-responsive" style="min-height: 250px;">
            <table class="table table-modern align-middle mb-0" id="complimentaryLedgerTable">
                <thead>
                    <tr>
                        <th class="ps-3">SO # / Ref</th>
                        <th>Recipient / Customer</th>
                        <th>Items Summary</th>
                        <th>Valuation (Expense)</th>
                        <th>Created Date</th>
                        <th>Receipt Status</th>
                        <th class="pe-3 text-end">Action</th>
                    </tr>
                </thead>
                @php $grandTotalValuation = 0; @endphp
                <tbody>
                    @forelse($complimentaryOrders as $order)
                    @php
                        $itemValuation = 0;
                        foreach($order->items as $i) {
                            $c = ($i->book && $i->book->cost > 0) ? $i->book->cost : ($i->unit_price > 0 ? $i->unit_price : 0);
                            $itemValuation += ($c * $i->quantity);
                        }
                        if ($itemValuation <= 0) $itemValuation = $order->total_amount;
                        $grandTotalValuation += $itemValuation;
                    @endphp
                    <tr>
                        <td class="ps-3">
                            <span class="fw-bold text-dark font-monospace">#{{ $order->so_number }}</span>
                            @if($order->ref_number)
                                <br><small class="text-muted font-monospace">Ref: {{ $order->ref_number }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $order->customer->customer_name ?? 'Walk-in / Recipient' }}</div>
                            <small class="text-muted">{{ Str::limit($order->billing_address ?? ($order->customer->address ?? 'No address'), 35) }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                {{ $order->items->sum('quantity') }} items
                            </span>
                            <small class="text-muted d-block mt-1">
                                {{ Str::limit($order->items->map(fn($i) => ($i->product->name ?? $i->book->name ?? 'Item') . ' (' . $i->quantity . ')')->implode(', '), 40) }}
                            </small>
                        </td>
                        <td>
                            <span class="fw-bold text-danger">₱{{ number_format($itemValuation, 2) }}</span>
                            <small class="d-block text-muted" style="font-size: 0.72rem;">COA 5100 Expense</small>
                        </td>
                        <td>
                            <div class="text-dark small">{{ \Carbon\Carbon::parse($order->created_at)->timezone('Asia/Manila')->format('M d, Y') }}</div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($order->created_at)->timezone('Asia/Manila')->format('h:i A') }}</small>
                        </td>
                        <td>
                            @if($order->status === 'pending_ar_prep')
                                <span class="badge bg-warning-subtle text-warning"><i class="las la-clock me-1"></i> Pending Approval</span>
                                <small class="d-block text-muted" style="font-size: 0.72rem; margin-top: 2px;">Ready for AR &amp; Packing</small>
                            @elseif($order->status === 'ready_for_packing')
                                <span class="badge bg-info-subtle text-info"><i class="las la-box me-1"></i> In Packing</span>
                                <small class="d-block text-muted" style="font-size: 0.72rem; margin-top: 2px;">Logistics Packing Queue</small>
                            @elseif($order->status === 'ready_for_delivery')
                                <span class="badge bg-primary-subtle text-primary"><i class="las la-truck me-1"></i> Delivery Scheduling</span>
                                <small class="d-block text-muted" style="font-size: 0.72rem; margin-top: 2px;">Packed &amp; Awaiting Dispatch</small>
                            @elseif($order->status === 'completed')
                                <span class="badge bg-success-subtle text-success"><i class="las la-check-circle me-1"></i> Delivered / Completed</span>
                                <small class="d-block text-muted" style="font-size: 0.72rem; margin-top: 2px;">COA 5100 Posted</small>
                            @elseif($order->status === 'picking')
                                <span class="badge bg-secondary-subtle text-secondary"><i class="las la-dolly me-1"></i> Logistics Picking</span>
                                <small class="d-block text-muted" style="font-size: 0.72rem; margin-top: 2px;">Gathering Picklist</small>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span>
                            @endif
                        </td>
                        <td class="pe-3 text-end">
                            <div class="dropdown">
                                <button class="btn btn-link text-muted p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow: none;">
                                    <i class="las la-ellipsis-v" style="font-size: 1.25rem;"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="font-size: 12px; border-radius: 6px; min-width: 195px; z-index: 1050;">
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('admin-finance.sales-order.detail', $order->id) }}">
                                            <i class="las la-eye me-2 text-primary" style="font-size: 1rem;"></i> View Order Detail
                                        </a>
                                    </li>

                                    @if($order->status === 'pending_ar_prep')
                                        <li>
                                            <form action="{{ route('admin-finance.accounting.ar.store', $order->id) }}" method="POST" class="m-0" onsubmit="return confirm('Mark Acknowledgement Receipt for Order #{{ $order->so_number }} as complete and send to Packing?');">
                                                @csrf
                                                <button type="submit" class="dropdown-item py-2 text-success fw-semibold">
                                                    <i class="las la-check-circle me-2" style="font-size: 1rem;"></i> Mark as Complete
                                                </button>
                                            </form>
                                        </li>
                                    @endif

                                    <li>
                                        <a class="dropdown-item py-2 text-danger fw-semibold" href="{{ route('admin-finance.accounting.ar.print', $order->id) }}" target="_blank">
                                            <i class="las la-print me-2" style="font-size: 1rem;"></i> Print Complimentary Receipt
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item py-2 text-info" href="{{ route('admin-finance.accounting.delivery-receipt.print', $order->id) }}" target="_blank">
                                            <i class="las la-truck me-2" style="font-size: 1rem;"></i> Print DR
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="las la-gift fs-48 mb-2 d-block text-secondary"></i>
                            No complimentary receipts found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($complimentaryOrders->count() > 0)
                <tfoot>
                    <tr class="fw-bold bg-light border-top" style="border-top: 2px solid #cbd5e1 !important; background-color: #f8fafc !important;">
                        <td colspan="3" class="ps-3 py-3 text-end text-dark font-w700" style="font-size: 0.85rem; letter-spacing: 0.5px;">TOTAL VALUATION (EXPENSE):</td>
                        <td class="py-3 text-danger font-w800" style="font-size: 0.95rem; font-weight: 800 !important;">₱{{ number_format($grandTotalValuation, 2) }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
