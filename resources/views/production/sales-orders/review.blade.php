<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .order-form { background: #fff; border-radius: 8px; padding: 2rem; box-shadow: 0 0 20px rgba(0, 0, 0, 0.05); }
        .form-header { margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #e0e0e0; }
        .form-header .company-info { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
        .form-header .company-logo { width: 60px; height: 60px; background: #ff0000; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; font-weight: bold; flex-shrink: 0; }
        .form-header .company-details { flex: 1; }
        .form-header .company-name { font-size: 1.25rem; font-weight: 700; color: #333; margin-bottom: 0.25rem; text-transform: uppercase; }
        .form-header .document-title { text-align: center; font-size: 1.75rem; font-weight: 700; color: #333; margin-top: 1rem; letter-spacing: 1px; }
        .customer-section { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem; }
        .customer-details, .order-details { background: #f8f9fa; padding: 1.5rem; border-radius: 6px; }
        .order-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        .order-table thead { background: #ff0000; color: #fff; }
        .order-table th, .order-table td { padding: 0.75rem; border: 1px solid #ddd; }
        .order-table tfoot { background: #f8f9fa; font-weight: 600; }
        @media print { .sidebar, .header, .form-actions, .btn { display: none !important; } .content-body { margin-left: 0 !important; } }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="card order-form">
                <!-- Form Header -->
                <div class="form-header">
                    <div class="company-info">
                        <div class="company-logo">C</div>
                        <div class="company-details">
                            <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                            <div class="company-address">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                            <div class="company-contact">Tel. No.: 921-3984</div>
                        </div>
                    </div>
                    <div class="document-title">SALES ORDER <span class="text-danger">#{{ $order->so_number }}</span></div>
                    <div class="text-center text-uppercase fw-bold text-secondary">{{ str_replace('_', ' ', $order->type) }}</div>
                </div>

                @php
                    $sym = ($order->currency === 'USD' ? '$' : '₱');
                @endphp

                <!-- Customer and Order Details -->
                <div class="customer-section">
                    <div class="customer-details">
                        <h5 class="text-black fw-bold">Customer Information</h5>
                        <table class="table table-sm table-borderless">
                            @php
                                $companyRaw = $order->customer?->company_name;
                                $companyDisplayName = ($companyRaw && strtolower($companyRaw) !== 'individual') 
                                    ? $companyRaw 
                                    : ($order->customer?->customer_name ?: ($order->customer_representative ?: 'Individual'));
                            @endphp
                            <tr>
                                <td class="fw-bold text-dark" style="width: 140px;">Company:</td>
                                <td class="fw-bold text-black">{{ $companyDisplayName }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Customer Name:</td>
                                <td class="fw-bold text-black">{{ $order->customer_representative ?: ($order->customer?->customer_name ?? 'Unknown Customer') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Contact:</td>
                                <td class="text-black">{{ $order->customer_contact ?: ($order->customer?->mobile ?: ($order->customer?->main_phone ?: 'N/A')) }}</td>
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
                                <td class="fw-bold text-dark">Address:</td>
                                <td class="text-black">{{ $order->customer->shipping_address ?? $order->customer->billing_address ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="order-details">
                        <h5 class="text-black fw-bold">Order Information</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold text-dark">Order Date:</td>
                                <td class="text-black">{{ $order->created_at->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Status:</td>
                                <td><span class="badge bg-warning text-white">{{ strtoupper(str_replace('_', ' ', $order->status)) }}</span></td>
                            </tr>
                            @php
                                $prodSiNumber = $order->si_number ?: (\App\Models\SalesInvoice::where('so_id', $order->id)->value('si_number') ?? null);
                            @endphp
                            @if($prodSiNumber)
                            <tr>
                                <td class="fw-bold text-dark"><i class="las la-file-invoice me-1 text-danger"></i>SI Number:</td>
                                <td class="fw-bold text-danger">{{ $prodSiNumber }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="fw-bold text-dark">Currency:</td>
                                <td class="fw-bold text-primary">{{ $order->currency ?: 'USD' }} ({{ $sym }})</td>
                            </tr>
                            @if($order->terms)
                            <tr>
                                <td class="fw-bold text-dark">Terms:</td>
                                <td class="fw-bold text-black">{{ $order->terms }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="fw-bold text-dark">Prepared By:</td>
                                <td class="text-black">{{ $order->preparedBy->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Freight Option:</td>
                                <td class="text-black">
                                    @if($order->freight_option)
                                        <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $order->freight_option)) }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            @if($order->freight_option || $order->forwarder)
                            <tr>
                                <td class="fw-bold text-dark">Forwarder:</td>
                                <td class="fw-bold text-primary"><i class="las la-shipping-fast me-1"></i>{{ $order->forwarder ?: 'N/A' }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="fw-bold text-dark">Remarks:</td>
                                <td class="text-black fw-bold text-primary" style="white-space: pre-wrap;">{!! e($order->remarks ?: '—') !!}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Attachments Section -->
                <div class="card mb-4 border-0 bg-light">
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-dark mb-3"><i class="las la-paperclip me-1 text-primary"></i> Attachments</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted mb-1">Proof of Payment:</label>
                                <div>
                                    @if($order->proof_of_payment)
                                        <a href="{{ asset('storage/' . $order->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-success fw-bold">
                                            <i class="las la-receipt me-1"></i> View Proof of Payment
                                        </a>
                                    @else
                                        <span class="badge bg-warning text-dark p-2"><i class="fas fa-exclamation-triangle me-1"></i> No Proof of Payment attached</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted mb-1">Pick List Attachment:</label>
                                <div>
                                    @if($order->pick_list_attachment)
                                        <a href="{{ asset('storage/' . $order->pick_list_attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="las la-file-alt me-1"></i> View Pick List
                                        </a>
                                    @else
                                        <span class="text-muted small"><i class="las la-info-circle me-1"></i> None</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted mb-1">Other Attachments:</label>
                                <div>
                                    @if($order->attachment)
                                        <a href="{{ asset('storage/' . $order->attachment) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="las la-paperclip me-1"></i> View Document
                                        </a>
                                    @elseif($order->order_list_attachment)
                                        <a href="{{ asset('storage/' . $order->order_list_attachment) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="las la-list me-1"></i> View Order List
                                        </a>
                                    @else
                                        <span class="text-muted small"><i class="las la-info-circle me-1"></i> None</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <table class="order-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">QTY</th>
                            <th style="width: 100px;">UNIT</th>
                            <th>DESCRIPTION</th>
                            <th style="width: 130px;">UNIT PRICE</th>
                            <th style="width: 110px;">DISCOUNT</th>
                            <th style="width: 150px;">AMOUNT</th>
                        </tr>
                    </thead>
                    @php
                        $sym = ($order->currency === 'USD' ? '$' : '₱');
                    @endphp
                    <tbody>
                        @foreach($order->items as $item)
                        @php 
                            $itemName = $item->item_name ?? ($item->bookIndex?->display_name ?? ($item->bookIndex?->title ?? ($item->bookIndex?->custom_name ?? ($item->bookIndex?->book?->name ?? ($item->book?->name ?? ($item->product?->name ?? ($item->bundle?->name ?? ($item->bookBundle?->name ?? 'Unknown Item')))))))); 
                            $sQty = (float)($item->sent_qty ?? 0);
                            $iQty = (float)($item->quantity ?? 0);
                            $rQty = (float)($item->requested_qty ?? 0);
                            $plReqQty = 0;
                            if (isset($item->pickListItems) && count($item->pickListItems) > 0) {
                                $plReqQty = (float)($item->pickListItems->first()->requested_qty ?? 0);
                            }
                            $itemQty = $sQty > 0 ? $sQty : ($iQty > 0 ? $iQty : ($rQty > 0 ? $rQty : ($plReqQty > 0 ? $plReqQty : 1)));
                            $itemPrice = (float)($item->price ?? ($item->unit_price ?? 0));
                            $itemSubtotal = ($item->subtotal && (float)$item->subtotal > 0) ? (float)$item->subtotal : ($itemQty * $itemPrice);
                        @endphp
                        @if($itemName)
                        <tr>
                            <td class="text-center">{{ (float)$itemQty }}</td>
                            <td class="text-center text-uppercase">{{ $item->product?->unit ?? $item->book?->unit ?? 'pcs' }}</td>
                            <td>
                                <div class="fw-bold d-flex align-items-center gap-1 flex-wrap">
                                    <span>{{ $itemName }}</span>
                                    @if($item->bundle_id || $item->bundle)
                                        <span class="badge bg-purple text-white ms-1" style="background-color: #6f42c1; font-size: 10px; padding: 3px 6px;">Bundle</span>
                                    @elseif($item->book_index_id || $item->bookIndex)
                                        <span class="badge bg-info text-white ms-1" style="font-size: 10px; padding: 3px 6px;">Index</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $item->product?->sku ?? $item->book?->sku ?? $item->bundle?->sku ?? '-' }}</small>
                            </td>
                            <td class="text-end">{{ $sym }}{{ number_format($itemPrice, 2) }}</td>
                            <td class="text-center">
                                @if(($item->discount_value ?? 0) > 0 || ($item->discount_amount ?? 0) > 0)
                                    @if(($item->discount_type ?? 'percentage') === 'percentage' && ($item->discount_value ?? 0) > 0)
                                        {{ (float)$item->discount_value }}%
                                    @elseif(($item->discount_value ?? 0) > 0)
                                        {{ $sym }}{{ number_format($item->discount_value, 2) }}
                                    @else
                                        {{ $sym }}{{ number_format($item->discount_amount, 2) }}
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-end fw-bold">{{ $sym }}{{ number_format($itemSubtotal, 2) }}</td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php
                            $itemsToRender = $order->items;
                            $totalSalesAmount = (float) $order->total_amount;
                            $itemsSubtotal = $itemsToRender->sum(function($item) {
                                $sQty = (float)($item->sent_qty ?? 0);
                                $iQty = (float)($item->quantity ?? 0);
                                $rQty = (float)($item->requested_qty ?? 0);
                                $plReqQty = 0;
                                if (isset($item->pickListItems) && count($item->pickListItems) > 0) {
                                    $plReqQty = (float)($item->pickListItems->first()->requested_qty ?? 0);
                                }
                                $q = $sQty > 0 ? $sQty : ($iQty > 0 ? $iQty : ($rQty > 0 ? $rQty : ($plReqQty > 0 ? $plReqQty : 1)));
                                $p = (float)($item->price ?? ($item->unit_price ?? 0));
                                return ($item->subtotal && (float)$item->subtotal > 0) ? (float)$item->subtotal : ($q * $p);
                            });
                            $discountAmount = (float) ($order->discount_amount ?? 0);
                            $discountPercentage = $order->discount_percentage ?? 0;
                            $freightCharges = $order->freight_charges ?? 0;
                            $serviceFee = $order->freight_option ? (($order->currency === 'USD' || $order->currency === 'EUR') ? 1.00 : 50.00) : 0;
                        @endphp
                        <tr>
                            <td colspan="5" class="text-end text-uppercase"><strong>Items Subtotal:</strong></td>
                            <td class="text-end fw-bold">{{ $sym }}{{ number_format($itemsSubtotal, 2) }}</td>
                        </tr>
                        @if($discountAmount > 0)
                        <tr>
                            <td colspan="5" class="text-end text-uppercase">
                                <strong>
                                    Discount
                                    @if($discountPercentage > 0)
                                        ({{ (float)$discountPercentage }}%)
                                    @endif:
                                </strong>
                            </td>
                            <td class="text-end fw-bold text-danger">- {{ $sym }}{{ number_format($discountAmount, 2) }}</td>
                        </tr>
                        @endif
                        @if($freightCharges > 0)
                        <tr>
                            <td colspan="5" class="text-end text-uppercase"><strong>Freight Charges:</strong></td>
                            <td class="text-end fw-bold">{{ $sym }}{{ number_format($freightCharges, 2) }}</td>
                        </tr>
                        @endif
                        @if($serviceFee > 0)
                        <tr>
                            <td colspan="5" class="text-end text-uppercase"><strong>Service Fee:</strong></td>
                            <td class="text-end fw-bold text-primary">+ {{ $sym }}{{ number_format($serviceFee, 2) }}</td>
                        </tr>
                        @endif
                        <tr style="background: #f8f9fa;">
                            <td colspan="5" class="text-end text-uppercase"><strong>Grand Total:</strong></td>
                            <td class="text-end fw-bold fs-5 text-primary">{{ $sym }}{{ number_format($totalSalesAmount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Actions -->
                <div class="d-flex justify-content-between align-items-center mt-4 form-actions flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button type="button" class="btn btn-dark" onclick="window.history.back()">
                            <i class="las la-arrow-left me-2"></i>Back to Queue
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="las la-print me-1"></i>Print Order
                        </button>
                        <a href="{{ route('marketing.sales-orders.print-invoice', $order->id) }}" target="_blank" class="btn btn-primary">
                            <i class="las la-file-invoice me-1"></i>Print Sales Invoice
                        </a>
                        <a href="{{ route('marketing.sales-orders.shipping-label', $order->id) }}" target="_blank" class="btn btn-info text-white">
                            <i class="las la-tag me-1"></i>Shipping Label
                        </a>
                    </div>
                    @if(in_array($order->status, ['draft', 'pending_mkt_approval', 'pending_acct_approval', 'pending_prod_approval']))
                    <div class="d-flex flex-column align-items-end gap-2 w-100 ms-auto" style="max-width: 420px;">
                        <div class="w-100 text-start">
                            <label class="form-label fw-bold text-dark small mb-1"><i class="las la-comment-alt text-primary me-1"></i> Action / Approval Remarks (Optional):</label>
                            <textarea id="prodApprovalRemarks" class="form-control form-control-sm" rows="2" placeholder="Enter optional notes before approving or rejecting..."></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <form action="{{ route('production.sales-order.reject', $order->id) }}" method="POST" id="rejectForm">
                                @csrf
                                <input type="hidden" name="remarks" id="rejectRemarks">
                                <button type="button" class="btn btn-outline-danger" onclick="confirmReject()">
                                    <i class="las la-times-circle me-2"></i>Reject Order
                                </button>
                            </form>
                            <form action="{{ route('production.sales-order.approve', $order->id) }}" method="POST" onsubmit="document.getElementById('approveProdRemarksInput').value = document.getElementById('prodApprovalRemarks').value;">
                                @csrf
                                <input type="hidden" name="remarks" id="approveProdRemarksInput">
                                <button type="submit" class="btn btn-success">
                                    <i class="las la-check-circle me-2"></i>Approve Order
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <div>
                        <span class="badge bg-success fs-14 p-2 fw-bold">
                            <i class="las la-check-circle me-1"></i> ORDER STATUS: {{ strtoupper(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmReject() {
            let initialReason = document.getElementById('prodApprovalRemarks') ? document.getElementById('prodApprovalRemarks').value : '';
            if (typeof window.openTwoStepRejectionFlow === 'function') {
                window.openTwoStepRejectionFlow(initialReason, function(reason) {
                    document.getElementById('rejectRemarks').value = reason;
                    document.getElementById('rejectForm').submit();
                });
            }
        }
    </script>
    @endpush
</x-app-layout>
