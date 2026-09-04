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

    @php
        $activeInvoice = null;
        if (in_array($order->type, ['area_consignment', 'area_sales_consignment'])) {
            $activeInvoice = \App\Models\SalesInvoice::where('so_id', $order->id)->where('status', '!=', 'cancelled')->latest()->first();
        }

        // If activeInvoice has no items, fall back to SO items
        if ($activeInvoice && $activeInvoice->items->count() > 0) {
            $itemsToRender = $activeInvoice->items->filter(fn($i) => (float)($i->quantity ?? 0) > 0);
            $totalSalesAmount = (float) $activeInvoice->total_amount;
        } else {
            $itemsToRender = $order->items ? $order->items->filter(fn($i) => (float)($i->quantity ?? 0) > 0) : collect();
            $totalSalesAmount = (float) $order->total_amount;
            $activeInvoice = null; // reset so item fields resolve from SO items
        }
        $sym = ($order->currency === 'USD' ? '$' : ($order->currency === 'EUR' ? '€' : '₱'));
    @endphp

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
                    <div class="text-center text-uppercase fw-bold text-secondary mb-2">{{ str_replace('_', ' ', $order->type) }}</div>
                    @php
                        $isComplimentary = $order->type === 'complimentary';
                        $paidAmt = (float) $order->total_paid_amount;
                        $remBal = $isComplimentary ? 0 : (float) $order->remaining_balance;
                        $pmStatus = $order->computed_payment_status;
                        $pmBadgeColor = $pmStatus === 'paid' ? 'success' : ($pmStatus === 'partially_paid' ? 'warning' : 'danger');
                        $pmLabel = $pmStatus === 'partially_paid' ? 'PARTIALLY PAID' : strtoupper($pmStatus);
                    @endphp
                    <div class="text-center mb-3">
                        @if($isComplimentary)
                            <span class="badge fs-14 px-3 py-2 me-2" style="background-color: #6f42c1; color: #fff;"><i class="las la-gift me-1"></i>COMPLIMENTARY / DONATION (NO PAYMENT REQUIRED)</span>
                        @else
                            <span class="badge bg-{{ $pmBadgeColor }} fs-14 px-3 py-2 me-2">{{ $pmLabel }}</span>
                            @if($remBal > 0 && $order->customer_id)
                                <button type="button" class="btn btn-sm btn-success open-pay-modal-btn shadow-sm" data-so-id="{{ $order->id }}" data-customer-id="{{ $order->customer_id }}" data-so-number="{{ $order->so_number }}" data-total="{{ $order->total_amount }}" data-paid="{{ $paidAmt }}" data-remaining="{{ $remBal }}" data-terms="{{ $order->terms ?? 'COD' }}" data-due-date="{{ $order->due_date ? $order->due_date->format('M d, Y') : 'N/A' }}">
                                    <i class="las la-coins me-1"></i> Record Payment / Installment
                                </button>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Customer and Order Details -->
                <div class="customer-section">
                    <div class="customer-details">
                        <h5 class="text-black fw-bold">Customer Information</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold text-dark" style="width: 140px;">Company:</td>
                                <td class="fw-bold text-black">
                                    {{ $order->customer->company_name ?: ($order->customer->customer_name ?? 'N/A') }}
                                    @if($order->customer)
                                        @if($order->customer->is_bad_client)
                                            <span class="badge bg-danger ms-2">Bad Client</span>
                                        @else
                                            <span class="badge bg-success ms-2">Good Client</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Customer Name:</td>
                                <td class="fw-bold text-black">{{ $order->customer_representative ?: ($order->customer->customer_name ?? 'Unknown Customer') }}</td>
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
                            <tr>
                                <td class="fw-bold text-dark"><i class="las la-hashtag me-1 text-danger"></i>SI Number:</td>
                                <td>
                                    <div class="input-group input-group-sm" style="max-width: 260px;">
                                        <input type="text" id="reviewSiNumberInput" class="form-control fw-bold text-danger" 
                                               value="{{ $order->si_number ?: (\App\Models\SalesInvoice::where('so_id', $order->id)->value('si_number') ?? '') }}" 
                                               placeholder="e.g. 00123" style="font-size: 0.95rem; border: 2px solid #ced4da;">
                                        <button type="button" class="btn btn-outline-danger" id="saveReviewSiBtn" onclick="saveSiNumberQuick()" title="Save SI Number">
                                            <i class="las la-save"></i> Save
                                        </button>
                                    </div>
                                    <small id="siSaveStatus" class="d-block mt-1" style="font-size: 0.75rem;"></small>
                                </td>
                            </tr>
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
                            @if($order->freight_option === 'bill_client' || $order->forwarder)
                            <tr>
                                <td class="fw-bold text-dark">Forwarder:</td>
                                <td class="fw-bold text-primary"><i class="las la-shipping-fast me-1"></i>{{ $order->forwarder ?? 'N/A' }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="fw-bold text-dark">Terms:</td>
                                <td class="fw-bold text-primary">{{ $order->terms ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Remarks:</td>
                                <td class="text-black fw-bold text-primary" style="white-space: pre-wrap;">{!! e($order->remarks ?: '—') !!}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Payment Method:</td>
                                <td>
                                    @if($isComplimentary)
                                        <span class="badge" style="background-color: #6f42c1; color: #fff;"><i class="las la-gift me-1"></i>Complimentary / Donation</span>
                                    @else
                                        @php
                                            $pm = strtolower($order->payment_method ?? 'cash');
                                            $pmBadges = [
                                                'cash'          => ['Cash', 'bg-success'],
                                                'gcash'         => ['GCash', 'bg-primary'],
                                                'maya'          => ['Maya', 'bg-info text-white'],
                                                'bank_transfer' => ['Bank Transfer', 'bg-secondary'],
                                                'check'         => ['Check', 'bg-dark'],
                                                'card'          => ['Card', 'bg-warning text-dark']
                                            ];
                                            [$pmLabelText, $pmClass] = $pmBadges[$pm] ?? [ucfirst($pm), 'bg-secondary'];
                                        @endphp
                                        <span class="badge {{ $pmClass }}"><i class="las la-wallet me-1"></i>{{ $pmLabelText }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Already Paid:</td>
                                <td class="fw-bold">
                                    @if($isComplimentary)
                                        <span class="text-muted">N/A (Donation)</span>
                                    @else
                                        <span class="text-success">{{ $sym }}{{ number_format($paidAmt, 2) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Remaining Balance:</td>
                                <td class="fw-bold">
                                    @if($isComplimentary)
                                        <span class="text-success">{{ $sym }}0.00 (No Charge)</span>
                                    @else
                                        <span class="text-danger">{{ $sym }}{{ number_format($remBal, 2) }}</span>
                                    @endif
                                </td>
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
                                    @if($isComplimentary)
                                        <span class="badge p-2" style="background-color: #6f42c1; color: #fff;"><i class="las la-info-circle me-1"></i>Not Required (Complimentary Order)</span>
                                        @if($order->proof_of_payment)
                                            <a href="{{ asset('storage/' . $order->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-success fw-bold ms-1">
                                                <i class="las la-receipt me-1"></i> View POP
                                            </a>
                                        @else
                                            <form action="{{ route('admin-finance.sales-order.upload-attachment', $order->id) }}" method="POST" enctype="multipart/form-data" class="mt-2">
                                                @csrf
                                                <input type="hidden" name="attachment_type" value="proof_of_payment">
                                                <div class="input-group input-group-sm">
                                                    <input type="file" name="attachment_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                                    <button type="submit" class="btn btn-outline-secondary text-nowrap"><i class="las la-upload me-1"></i> Upload (Optional)</button>
                                                </div>
                                            </form>
                                        @endif
                                    @elseif(in_array($order->type, ['charge', 'area_consignment', 'area_sales_consignment', 'direct_consignment']))
                                        <span class="badge p-2 bg-info text-white"><i class="las la-info-circle me-1"></i>Not Required ({{ ucfirst(str_replace('_', ' ', $order->type)) }} Transaction)</span>
                                        @if($order->proof_of_payment)
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <a href="{{ asset('storage/' . $order->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-success fw-bold">
                                                    <i class="las la-receipt me-1"></i> View POP
                                                </a>
                                                <button class="btn btn-sm btn-light border text-muted" type="button" onclick="document.getElementById('reuploadPopFormExempt').classList.toggle('d-none')" title="Re-upload">
                                                    <i class="las la-edit"></i>
                                                </button>
                                            </div>
                                            <form id="reuploadPopFormExempt" action="{{ route('admin-finance.sales-order.upload-attachment', $order->id) }}" method="POST" enctype="multipart/form-data" class="d-none mt-2">
                                                @csrf
                                                <input type="hidden" name="attachment_type" value="proof_of_payment">
                                                <div class="input-group input-group-sm">
                                                    <input type="file" name="attachment_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                                    <button type="submit" class="btn btn-primary"><i class="las la-upload me-1"></i> Upload</button>
                                                </div>
                                            </form>
                                        @else
                                            <form action="{{ route('admin-finance.sales-order.upload-attachment', $order->id) }}" method="POST" enctype="multipart/form-data" class="mt-2">
                                                @csrf
                                                <input type="hidden" name="attachment_type" value="proof_of_payment">
                                                <div class="input-group input-group-sm">
                                                    <input type="file" name="attachment_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                                    <button type="submit" class="btn btn-outline-secondary text-nowrap"><i class="las la-upload me-1"></i> Upload (Optional)</button>
                                                </div>
                                            </form>
                                        @endif
                                    @elseif($order->proof_of_payment)
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ asset('storage/' . $order->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-success fw-bold">
                                                <i class="las la-receipt me-1"></i> View Proof of Payment
                                            </a>
                                            <button class="btn btn-sm btn-light border text-muted" type="button" onclick="document.getElementById('reuploadPopForm').classList.toggle('d-none')" title="Re-upload Proof of Payment">
                                                <i class="las la-edit"></i>
                                            </button>
                                        </div>
                                        <form id="reuploadPopForm" action="{{ route('admin-finance.sales-order.upload-attachment', $order->id) }}" method="POST" enctype="multipart/form-data" class="d-none mt-2">
                                            @csrf
                                            <input type="hidden" name="attachment_type" value="proof_of_payment">
                                            <div class="input-group input-group-sm">
                                                <input type="file" name="attachment_file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png">
                                                <button type="submit" class="btn btn-primary"><i class="las la-upload me-1"></i> Upload</button>
                                            </div>
                                        </form>
                                    @else
                                        <form action="{{ route('admin-finance.sales-order.upload-attachment', $order->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="attachment_type" value="proof_of_payment">
                                            <div class="input-group input-group-sm">
                                                <input type="file" name="attachment_file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png">
                                                <button type="submit" class="btn btn-primary fw-bold text-nowrap"><i class="las la-upload me-1"></i> Upload POP</button>
                                            </div>
                                        </form>
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
                                        <form action="{{ route('admin-finance.sales-order.upload-attachment', $order->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="attachment_type" value="pick_list_attachment">
                                            <div class="input-group input-group-sm">
                                                <input type="file" name="attachment_file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png">
                                                <button type="submit" class="btn btn-outline-primary text-nowrap"><i class="las la-upload me-1"></i> Upload</button>
                                            </div>
                                        </form>
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
                                        <form action="{{ route('admin-finance.sales-order.upload-attachment', $order->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="attachment_type" value="attachment">
                                            <div class="input-group input-group-sm">
                                                <input type="file" name="attachment_file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png">
                                                <button type="submit" class="btn btn-outline-info text-nowrap"><i class="las la-upload me-1"></i> Upload</button>
                                            </div>
                                        </form>
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
                    <tbody>
                        @foreach($itemsToRender as $item)
                        @php 
                            $itemName = $item->item_name ?? ($item->bookIndex?->display_name ?? ($item->bookIndex?->title ?? ($item->bookIndex?->custom_name ?? ($item->bookIndex?->book?->name ?? ($item->book?->name ?? ($item->product?->name ?? ($item->bundle?->name ?? ($item->bookBundle?->name ?? ($item->product_name ?? 'Unknown Item'))))))))); 
                            $sQty = (float)($item->sent_qty ?? 0);
                            $iQty = (float)($item->quantity ?? 0);
                            $rQty = (float)($item->requested_qty ?? 0);
                            $plReqQty = 0;
                            if (isset($item->pickListItems) && count($item->pickListItems) > 0) {
                                $plReqQty = (float)($item->pickListItems->first()->requested_qty ?? 0);
                            }
                            $itemQty = $sQty > 0 ? $sQty : ($iQty > 0 ? $iQty : ($rQty > 0 ? $rQty : ($plReqQty > 0 ? $plReqQty : 1)));
                            $itemPrice = (float)($item->unit_price ?? ($item->price ?? 0));
                            $itemSubtotal = ($item->amount ?? ($item->subtotal && (float)$item->subtotal > 0 ? (float)$item->subtotal : ($itemQty * $itemPrice)));
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
                                return $item->amount ?? ($item->subtotal && (float)$item->subtotal > 0 ? (float)$item->subtotal : ($q * $p));
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
                                    @if(($order->discount_percentage ?? 0) > 0)
                                        ({{ (float)$order->discount_percentage }}%)
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
                            <td class="text-end fw-bold">+ {{ $sym }}{{ number_format($serviceFee, 2) }}</td>
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
                        <a href="{{ route('marketing.sales-orders.print-invoice', $order->id) }}" target="_blank" class="btn btn-primary" onclick="return handlePrintSalesInvoice(event, this)">
                            <i class="las la-file-invoice me-1"></i>Print Sales Invoice
                        </a>
                        <a href="{{ route('marketing.sales-orders.shipping-label', $order->id) }}" target="_blank" class="btn btn-info text-white">
                            <i class="las la-tag me-1"></i>Shipping Label
                        </a>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        @if($order->type === 'complimentary')
                            @if(in_array($order->status, ['draft', 'pending_mkt_approval', 'pending_acct_approval', 'pending_ar_prep']))
                                <form action="{{ route('admin-finance.sales-order.reject', $order->id) }}" method="POST" id="rejectForm">
                                    @csrf
                                    <input type="hidden" name="remarks" id="rejectRemarks">
                                    <button type="button" class="btn btn-outline-danger" onclick="confirmReject()">
                                        <i class="las la-times-circle me-2"></i>Reject Order
                                    </button>
                                </form>
                                <form action="{{ route('admin-finance.sales-order.approve', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn fw-bold" style="background-color: #6f42c1; color: #fff;">
                                        <i class="las la-boxes me-2"></i>Approve & Send to Packing
                                    </button>
                                </form>
                            @else
                                <span class="badge fs-14 p-2" style="background-color: #6f42c1; color: #fff;"><i class="las la-check-circle me-1"></i>Complimentary Order (Sent to Packing / Logistics)</span>
                            @endif
                        @elseif($order->status === 'pending_acct_approval')
                            <div class="w-100 mb-3 text-start">
                                <label class="form-label fw-bold text-dark"><i class="las la-comment-alt text-primary me-1"></i> Add Approval / Action Remarks (Optional):</label>
                                <textarea name="remarks" id="financeApprovalRemarks" class="form-control" rows="2" placeholder="Enter optional notes before approving or rejecting..."></textarea>
                            </div>
                            <form action="{{ route('admin-finance.sales-order.reject', $order->id) }}" method="POST" id="rejectForm">
                                @csrf
                                <input type="hidden" name="remarks" id="rejectRemarks">
                                <button type="button" class="btn btn-outline-danger" onclick="confirmReject()">
                                    <i class="las la-times-circle me-2"></i>Reject Order
                                </button>
                            </form>
                            <form action="{{ route('admin-finance.sales-order.approve', $order->id) }}" method="POST" onsubmit="document.getElementById('approveRemarksInput').value = document.getElementById('financeApprovalRemarks').value;">
                                @csrf
                                <input type="hidden" name="remarks" id="approveRemarksInput">
                                <button type="submit" class="btn btn-success">
                                    <i class="las la-check-circle me-2"></i>Approve Order
                                </button>
                            </form>
                        @elseif($order->status === 'pending_si_prep')
                            @if($order->proof_of_payment || in_array($order->type, ['ecom_direct', 'charge', 'area_consignment', 'area_sales_consignment', 'direct_consignment', 'complimentary']))
                                <a href="{{ route('admin-finance.accounting.sales-invoice.prepare', $order->id) }}" class="btn btn-warning">
                                    <i class="las la-file-invoice me-2"></i>Prepare Sales Invoice
                                </a>
                            @else
                                <button class="btn btn-warning" disabled title="Proof of Payment is required to prepare SI">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Prepare Sales Invoice (Proof Required)
                                </button>
                            @endif
                        @elseif($order->status === 'pending_si_approval' || (!$order->signed_by_af_manager && $order->type !== 'complimentary'))
                            <form action="{{ route('admin-finance.accounting.sales-invoice.sign', $order->id) }}" method="POST" onsubmit="document.getElementById('signSiNumberHidden').value = document.getElementById('reviewSiNumberInput')?.value || '';">
                                @csrf
                                <input type="hidden" name="si_number" id="signSiNumberHidden">
                                <button type="submit" class="btn btn-primary">
                                    <i class="las la-file-signature me-2"></i>Sign & Approve Sales Invoice
                                </button>
                            </form>
                        @elseif($order->status === 'pending_dr_approval')
                            <form action="{{ route('production.logistic.approve-dr', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="las la-truck me-2"></i>Sign & Approve Delivery Receipt
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function saveSiNumberQuick() {
            const siVal = document.getElementById('reviewSiNumberInput')?.value?.trim() || '';
            const statusEl = document.getElementById('siSaveStatus');
            if (statusEl) {
                statusEl.innerHTML = '<span class="text-muted"><i class="las la-spinner la-spin"></i> Saving...</span>';
            }

            return fetch("{{ route('admin-finance.sales-order.update-si-number', $order->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ si_number: siVal })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (statusEl) statusEl.innerHTML = '<span class="text-success fw-bold"><i class="las la-check"></i> Saved</span>';
                    setTimeout(() => { if (statusEl) statusEl.innerHTML = ''; }, 3000);
                    return data;
                } else {
                    if (statusEl) statusEl.innerHTML = '<span class="text-danger">' + (data.message || 'Error saving') + '</span>';
                    throw new Error(data.message || 'Error saving');
                }
            })
            .catch(err => {
                if (statusEl) statusEl.innerHTML = '<span class="text-danger">Failed to save SI Number</span>';
                throw err;
            });
        }

        function handlePrintSalesInvoice(event, el) {
            const siInput = document.getElementById('reviewSiNumberInput');
            if (siInput) {
                event.preventDefault();
                saveSiNumberQuick().finally(() => {
                    window.open(el.href, '_blank');
                });
                return false;
            }
            return true;
        }

        function confirmReject() {
            if (typeof showAppModal === 'function') {
                showAppModal('Reject Sales Order', 'Please provide a reason for rejection:', {
                    type: 'input',
                    placeholder: 'Enter rejection remarks...',
                    confirmText: 'Confirm Reject',
                    confirmClass: 'btn-danger',
                    onConfirm: function(remarks) {
                        if (remarks) {
                            document.getElementById('rejectRemarks').value = remarks;
                            document.getElementById('rejectForm').submit();
                        } else {
                            alert('Rejection reason is required.');
                        }
                    }
                });
            } else {
                let remarks = prompt('Please provide a reason for rejection:');
                if (remarks) {
                    document.getElementById('rejectRemarks').value = remarks;
                    document.getElementById('rejectForm').submit();
                }
            }
        }
    </script>
    @endpush

    <!-- Record Payment Modal -->
    <div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white"><i class="las la-money-bill-wave me-2"></i>Payment History & Record Installment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="recordPaymentForm">
                    <div class="modal-body">
                        <input type="hidden" id="paySoId">
                        <input type="hidden" id="payCustomerId">
                        
                        <div class="alert alert-light border mb-3">
                            <div class="row g-2 text-center text-md-start">
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Transaction #:</span>
                                    <strong id="paySoNumber" class="text-dark">SO-0000</strong>
                                </div>
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Terms:</span>
                                    <span id="payTerms" class="badge bg-info text-white fw-semibold">COD</span>
                                </div>
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Due Date:</span>
                                    <strong id="payDueDate" class="text-dark">N/A</strong>
                                </div>
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Grand Total:</span>
                                    <strong id="payTotalAmount" class="text-dark">₱0.00</strong>
                                </div>
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Already Paid:</span>
                                    <span id="payAlreadyPaid" class="text-success fw-bold">₱0.00</span>
                                </div>
                                <div class="col-6 col-md-2">
                                    <span class="text-muted small d-block">Remaining:</span>
                                    <strong id="payRemainingBalance" class="text-danger fs-16">₱0.00</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Payment History Breakdown Table -->
                        <div class="card mb-3 border">
                            <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                                <span class="fw-bold small text-dark"><i class="las la-history me-1 text-primary"></i> Previous Installments Log</span>
                                <span class="badge bg-secondary" id="payHistoryBadge">0 payments</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 180px; overflow-y: auto;">
                                    <table class="table table-sm table-striped table-bordered mb-0 align-middle" style="font-size: 11px;">
                                        <thead class="bg-light sticky-top">
                                            <tr>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Method</th>
                                                <th>Ref # / Check #</th>
                                                <th>Notes</th>
                                                <th>Proof</th>
                                                <th>Recorded By</th>
                                            </tr>
                                        </thead>
                                        <tbody id="payHistoryTableBody">
                                            <tr><td colspan="7" class="text-center py-2 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Loading payment history...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- New Installment Entry Form -->
                        <div id="newPaymentFormFields">
                            <h6 class="fw-bold text-dark border-bottom pb-1 mb-3"><i class="las la-plus-circle me-1 text-success"></i> Add New Installment Payment</h6>

                            <div class="row g-2">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Payment Amount (<span class="pay-curr-symbol">₱</span>) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text pay-curr-symbol">₱</span>
                                        <input type="number" step="0.01" min="0.01" id="payAmountInput" class="form-control fw-bold fs-15 text-primary" required placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Payment Method <span class="text-danger">*</span></label>
                                    <select id="payMethodSelect" class="form-select form-select-sm" required>
                                        <option value="cash">Cash</option>
                                        <option value="gcash">GCash</option>
                                        <option value="maya">Maya</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="check">Check</option>
                                        <option value="card">Credit / Debit Card</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Reference / Check # <span class="text-muted fw-normal">(Optional)</span></label>
                                    <input type="text" id="payRefInput" class="form-control form-control-sm" placeholder="e.g. Ref #123456 or Check #">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Notes / Remarks <span class="text-muted fw-normal">(Optional)</span></label>
                                    <input type="text" id="payNotesInput" class="form-control form-control-sm" placeholder="e.g. 1st installment payment">
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label class="form-label fw-bold small text-dark">Proof of Payment <span class="text-muted fw-normal">(Optional - Image/PDF)</span></label>
                                    <input type="file" id="payProofInput" class="form-control form-control-sm" accept="image/*,.pdf">
                                </div>
                            </div>
                        </div>

                        <div id="fullyPaidNotice" class="alert alert-success d-none text-center py-2 mb-0">
                            <i class="las la-check-circle me-1 fs-16"></i> This order is fully paid. No further payments required.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" id="submitPaymentBtn">
                            <i class="las la-check-circle me-1"></i> Submit Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        async function fetchPaymentHistory(customerId, soId) {
            const tableBody = document.getElementById('payHistoryTableBody');
            const badge = document.getElementById('payHistoryBadge');

            if (!tableBody) return;

            tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-2 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Loading history...</td></tr>';
            if (badge) badge.textContent = 'Loading...';

            try {
                const response = await fetch(`/marketing/customers/${customerId}/transactions/${soId}/payments`);
                const data = await response.json();

                if (!data.payments || data.payments.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-2 text-muted">No previous installments recorded.</td></tr>';
                    if (badge) badge.textContent = '0 payments';
                } else {
                    if (badge) badge.textContent = data.payments.length + ' payment(s)';
                    let rows = '';
                    data.payments.forEach(p => {
                        const proofTag = p.has_proof ? `<a href="${p.proof_url}" target="_blank" class="badge badge-xs bg-light text-primary border"><i class="las la-paperclip me-1"></i>View Proof</a>` : '<span class="text-muted small">None</span>';
                        rows += `<tr>
                            <td class="fw-bold">${p.date}</td>
                            <td class="text-success fw-bold">₱${p.amount.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                            <td><span class="badge bg-light text-dark border">${p.method}</span></td>
                            <td>${p.reference_number}</td>
                            <td>${p.notes}</td>
                            <td>${proofTag}</td>
                            <td><small class="text-muted">${p.recorded_by}</small></td>
                        </tr>`;
                    });
                    tableBody.innerHTML = rows;
                }
            } catch (error) {
                console.error('Error loading payment history:', error);
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-2 text-danger">Failed to load payment history.</td></tr>';
                if (badge) badge.textContent = 'Error';
            }
        }

        document.body.addEventListener('click', function(e) {
            const payBtn = e.target.closest('.open-pay-modal-btn');
            if (payBtn) {
                const soId = payBtn.dataset.soId;
                const customerId = payBtn.dataset.customerId;
                const soNumber = payBtn.dataset.soNumber;
                const totalAmount = parseFloat(payBtn.dataset.total) || 0;
                const paidAmount = parseFloat(payBtn.dataset.paid) || 0;
                const remainingBalance = parseFloat(payBtn.dataset.remaining) || 0;

                const terms = payBtn.dataset.terms || 'COD';
                const dueDate = payBtn.dataset.dueDate || 'N/A';

                const currSymbol = payBtn.dataset.symbol || (payBtn.dataset.currency === 'USD' ? '$' : (payBtn.dataset.currency === 'EUR' ? '€' : '₱'));

                document.getElementById('paySoId').value = soId;
                document.getElementById('payCustomerId').value = customerId;
                document.getElementById('paySoNumber').textContent = soNumber;
                document.getElementById('payTerms').textContent = terms;
                document.getElementById('payDueDate').textContent = dueDate;
                document.getElementById('payTotalAmount').textContent = currSymbol + totalAmount.toLocaleString(undefined, {minimumFractionDigits: 2});
                document.getElementById('payAlreadyPaid').textContent = currSymbol + paidAmount.toLocaleString(undefined, {minimumFractionDigits: 2});
                document.getElementById('payRemainingBalance').textContent = currSymbol + remainingBalance.toLocaleString(undefined, {minimumFractionDigits: 2});
                
                document.querySelectorAll('.pay-curr-symbol').forEach(el => {
                    el.textContent = currSymbol;
                });
                
                const formFields = document.getElementById('newPaymentFormFields');
                const submitBtn = document.getElementById('submitPaymentBtn');
                const notice = document.getElementById('fullyPaidNotice');

                if (remainingBalance <= 0) {
                    if (formFields) formFields.classList.add('d-none');
                    if (submitBtn) submitBtn.classList.add('d-none');
                    if (notice) notice.classList.remove('d-none');
                } else {
                    if (formFields) formFields.classList.remove('d-none');
                    if (submitBtn) submitBtn.classList.remove('d-none');
                    if (notice) notice.classList.add('d-none');

                    const payAmountInput = document.getElementById('payAmountInput');
                    payAmountInput.value = remainingBalance.toFixed(2);
                    payAmountInput.max = remainingBalance;
                    document.getElementById('payRefInput').value = '';
                    document.getElementById('payNotesInput').value = '';
                    const proofInput = document.getElementById('payProofInput');
                    if (proofInput) proofInput.value = '';
                }

                // Fetch payment history breakdown
                fetchPaymentHistory(customerId, soId);

                const payModalElement = document.getElementById('recordPaymentModal');
                const payModal = bootstrap.Modal.getInstance(payModalElement) || new bootstrap.Modal(payModalElement);
                payModal.show();
            }
        });

        document.getElementById('recordPaymentForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const soId = document.getElementById('paySoId').value;
            const customerId = document.getElementById('payCustomerId').value;
            const amount = parseFloat(document.getElementById('payAmountInput').value);
            const paymentMethod = document.getElementById('payMethodSelect').value;
            const referenceNumber = document.getElementById('payRefInput').value;
            const notes = document.getElementById('payNotesInput').value;
            const proofInput = document.getElementById('payProofInput');

            if (!soId || !customerId) return;

            const submitBtn = document.getElementById('submitPaymentBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...';

            const formData = new FormData();
            formData.append('amount', amount);
            formData.append('payment_method', paymentMethod);
            if (referenceNumber) formData.append('reference_number', referenceNumber);
            if (notes) formData.append('notes', notes);
            if (proofInput && proofInput.files[0]) {
                formData.append('proof_of_payment', proofInput.files[0]);
            }

            try {
                const response = await fetch(`/marketing/customers/${customerId}/transactions/${soId}/pay`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    alert('Payment recorded successfully!');
                    window.location.reload();
                } else {
                    alert(data.message || 'Error recording payment.');
                }
            } catch (error) {
                console.error('Error submitting payment:', error);
                alert('An error occurred while submitting payment.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="las la-check-circle me-1"></i> Submit Payment';
            }
        });
    });
    </script>
    @endpush
</x-app-layout>
