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
        .order-table input { width: 100%; border: none; background: transparent; padding: 0.5rem; }
        .order-table tfoot { background: #f8f9fa; font-weight: 600; }
        .btn-add-row { background: #ff0000; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 4px; margin-bottom: 1rem; cursor: pointer; }
        .status-step.active { background: #28a745 !important; color: white !important; }
        .print-only { display: none; }
        @media print { 
            @page { size: letter; margin: 0.5in; }
            body { background: #fff !important; }
            /* Hide UI elements */
            .nav-header, .header, .deznav, .sidebar, .footer, .screen-only { display: none !important; } 
            
            /* Reset Layout containers */
            #main-wrapper, .content-body, .container-fluid { margin: 0 !important; padding: 0 !important; padding-top: 0 !important; }
            
            .print-only { display: block !important; width: 100%; color: #000; font-family: 'Times New Roman', serif; }
            .card, .order-form { border: none !important; box-shadow: none !important; padding: 0 !important; }
            table { font-size: 11pt; }
        }

    </style>
    @endpush

    @php
        $activeInvoice = null;
        $itemsToRender = collect();
        $totalSalesAmount = 0;
        if ($order) {
            if (in_array($order->type ?? '', ['area_consignment', 'area_sales_consignment'])) {
                $activeInvoice = \App\Models\SalesInvoice::where('so_id', $order->id)->where('status', '!=', 'cancelled')->latest()->first();
            }

            // If activeInvoice exists but has no items, fall back to SO items
            if ($activeInvoice && $activeInvoice->items->count() > 0) {
                $itemsToRender = $activeInvoice->items ? $activeInvoice->items->filter(fn($i) => (float)($i->quantity ?? 0) > 0) : collect();
                $totalSalesAmount = (float) ($activeInvoice->total_amount ?? 0);
            } else {
                $itemsToRender = $order->items ? $order->items->filter(function($i) {
                    return (float)($i->quantity ?? 0) > 0 
                        || (float)($i->sent_qty ?? 0) > 0 
                        || (float)($i->customer_selected_qty ?? 0) > 0 
                        || (float)($i->pickListItems?->first()?->requested_qty ?? 0) > 0
                        || (float)($i->price ?? 0) > 0;
                }) : collect();
                $totalSalesAmount = (float) ($order->total_amount ?? 0);
                $activeInvoice = null; // reset so item display uses SO item fields
            }
        }
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card order-form">
                <!-- SCREEN VIEW -->
                <div class="screen-only">
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
                    @if($order)
                    <div class="document-title">SALES ORDER <span class="text-danger">#{{ $order->so_number }}</span></div>
                     @php
                        $typeDisplay = str_replace('_', ' ', $order->type);
                        if ($order->type == 'calculator_pos') $typeDisplay = 'direct POS';
                        if ($order->type == 'ecom_direct') $typeDisplay = 'ECOM POS';
                        if ($order->type == 'paid') $typeDisplay = 'paid transac';
                        $siNumberDisplay = $order->si_number ?: (\App\Models\SalesInvoice::where('so_id', $order->id)->value('si_number') ?? null);
                    @endphp
                    <div class="text-center text-uppercase fw-bold {{ $order->type === 'paid' ? 'text-success' : 'text-primary' }}">{{ $typeDisplay }}</div>
                    @if($siNumberDisplay)
                        <div class="text-center mt-1"><span class="badge bg-danger text-white px-3 py-1" style="font-size: 0.85rem; font-weight: 700; letter-spacing: 0.5px;"><i class="las la-file-invoice me-1"></i>SI #: {{ $siNumberDisplay }}</span></div>
                    @endif
                    @else
                    <div class="document-title">SALES ORDER</div>
                    @endif
                </div>

                @if($order)
                <!-- Customer and Order Details -->
                <div class="customer-section">
                    <div class="customer-details">
                        <h5 class="text-black fw-bold">Customer Information</h5>
                        <table class="table table-sm table-borderless">
                            @php
                                $bName = $order->customer_representative;
                                if (!$bName && $order->remarks && str_contains($order->remarks, 'Branch:')) {
                                    preg_match('/Branch:\s*([^|\n\r]+)/', $order->remarks, $m);
                                    $bName = trim($m[1] ?? '');
                                }
                                $bCompany = $bName ? \App\Models\Company::where('company_name', $bName)
                                    ->orWhere('company_name', str_replace('AB-', 'AB - ', $bName))
                                    ->orWhere('company_name', str_replace('AB - ', 'AB-', $bName))
                                    ->first() : null;
                                $accountNo = $bCompany?->account_number ?: ($order->customer?->account_number ?? null);
                                $acctCompany = $accountNo ? \App\Models\Company::where('account_number', $accountNo)->first() : null;

                                $displayCompanyName = $bCompany?->parent?->company_name 
                                    ?: ($bCompany?->company_name 
                                    ?: ($acctCompany?->parent?->company_name 
                                    ?: ($acctCompany?->company_name 
                                    ?: ($order->customer?->company_name && !in_array(strtolower($order->customer->company_name), ['intracode', 'individual']) ? $order->customer->company_name : ($order->customer?->customer_name ?? 'N/A')))));
                                $displayAccountNo = $bCompany?->account_number ?: ($acctCompany?->account_number ?: ($order->customer?->account_number ?? 'N/A'));
                            @endphp
                            <tr>
                                <td class="fw-bold text-dark" style="width: 140px;">Company:</td>
                                <td class="fw-bold text-black">
                                    {{ $displayCompanyName }}
                                    @if($order->customer)
                                        @if($order->customer->isBadClient)
                                            <span class="badge bg-danger ms-2">BAD CLIENT</span>
                                        @else
                                            <span class="badge bg-success ms-2">GOOD CLIENT</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Customer Name:</td>
                                <td class="fw-bold text-black">{{ $order->customer_representative ?: ($order->customer?->customer_name ?? 'N/A') }}</td>
                            </tr>
                            @if($order->type === 'area_sales_consignment')
                            <tr>
                                <td class="fw-bold text-dark" style="width: 140px;">Area Sales Staff:</td>
                                <td class="fw-bold text-black">
                                    <span class="badge" style="background:#1a7a3e; font-size:12px;">{{ $order->areaSalesStaff?->name ?? '—' }}</span>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td class="fw-bold text-dark">Account No:</td>
                                <td class="text-black">{{ $displayAccountNo }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Address:</td>
                                <td class="text-black">
                                    <textarea class="form-control form-control-sm" id="orderAddress" style="min-height: 60px;" placeholder="Address...">{{ $order->shipping_address ?: ($order->customer?->shipping_address ?: ($order->customer?->billing_address ?? '')) }}</textarea>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Contact:</td>
                                <td class="text-black">{{ $order->customer_contact ?: ($order->customer?->mobile ?: ($order->customer?->main_phone ?: 'N/A')) }}</td>
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
                                <td><span class="badge bg-info text-white">{{ strtoupper(str_replace('_', ' ', $order->status)) }}</span></td>
                            </tr>
                            @if($siNumberDisplay)
                            <tr>
                                <td class="fw-bold text-dark"><i class="las la-file-invoice me-1 text-danger"></i>SI Number:</td>
                                <td class="fw-bold text-danger">{{ $siNumberDisplay }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="fw-bold text-dark">Prepared By:</td>
                                <td class="text-black">{{ $order->preparedBy->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Remarks:</td>
                                <td class="text-black" style="white-space: pre-wrap;">{!! e($order->remarks ?: 'None') !!}</td>
                            </tr>
                            @if($order->cancellation_date)
                            <tr>
                                <td class="fw-bold text-dark">Cancellation Date:</td>
                                <td class="text-black fw-bold text-danger">{{ \Carbon\Carbon::parse($order->cancellation_date)->format('F d, Y') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="fw-bold text-dark">Terms:</td>
                                <td class="text-black">{{ $order->terms ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">REF #:</td>
                                <td class="text-black">{{ $order->ref_number ?? '-' }}</td>
                            </tr>
                            @if($order->proof_of_payment)
                            <tr>
                                <td class="fw-bold text-dark">Proof of Payment:</td>
                                <td class="text-black">
                                    <a href="{{ asset('storage/' . $order->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-success py-0 px-2" style="font-size: 0.75rem;">
                                        <i class="las la-paperclip me-1"></i>View Proof of Payment
                                    </a>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td class="fw-bold text-dark">Freight Option:</td>
                                <td class="text-black">
                                    @if($order->status !== 'completed' && $order->status !== 'cancelled')
                                        <select id="freightOptionSelect" class="form-control form-control-sm">
                                            <option value="">Select Freight Option</option>
                                            <option value="freight_collect" {{ $order->freight_option === 'freight_collect' ? 'selected' : '' }}>Freight Collect</option>
                                            <option value="freight_billing" {{ $order->freight_option === 'freight_billing' ? 'selected' : '' }}>Freight Billing</option>
                                            <option value="bill_client" {{ $order->freight_option === 'bill_client' ? 'selected' : '' }}>Bill Client</option>
                                        </select>
                                        <div id="forwarderContainer" class="mt-2" style="display: {{ $order->freight_option ? 'block' : 'none' }};">
                                            <label class="small fw-bold text-dark mb-1">Forwarder / Carrier:</label>
                                            <input type="text" id="forwarderInput" class="form-control form-control-sm" placeholder="e.g. LBC, J&T, 2GO, AP Cargo" value="{{ $order->forwarder }}">
                                        </div>
                                    @else
                                        @if($order->freight_option)
                                            <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $order->freight_option)) }}</span>
                                            @if($order->forwarder)
                                                <div class="small fw-bold text-dark mt-1"><i class="las la-shipping-fast me-1 text-primary"></i>Forwarder: {{ $order->forwarder }}</div>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr id="serviceFeeRow" style="display: none;">
                                <td class="fw-bold text-dark">Service Fee:</td>
                                <td class="text-black">₱50.00</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Items Table -->
                <table class="order-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">QTY</th>
                            <th style="width: 100px;">UNIT</th>
                            <th>DESCRIPTION</th>
                            <th style="width: 120px;">ISBN</th>
                            <th style="width: 120px;">AREA</th>
                            <th style="width: 130px;">UNIT PRICE</th>
                            <th style="width: 110px;">DISCOUNT</th>
                            <th style="width: 150px;">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($itemsToRender as $item)
                        @php
                            $displayTitle = null;
                            $displayImage = asset('images/no-book-cover.svg');
                            $displayIsbn = $item->isbn ?? '-';
                            $isIndex = false;
                            $isBundle = false;
                            $indexVal = '';

                            if ($item->bookIndex) {
                                $isIndex = true;
                                $indexVal = $item->bookIndex->index_value;
                                $parentBook = $item->bookIndex->book;
                                $displayTitle = $item->bookIndex->display_name;
                                $displayIsbn = $item->bookIndex->barcode ?? ($parentBook?->isbn ?? $displayIsbn);
                                if ($parentBook && $parentBook->image) {
                                    $displayImage = asset('storage/' . $parentBook->image);
                                }
                            } elseif ($item->bundle) {
                                $isBundle = true;
                                $displayTitle = $item->bundle->name;
                                $displayIsbn = $item->bundle->sku ?? $displayIsbn;
                            } elseif ($item->book) {
                                $displayTitle = $item->book->name;
                                $displayIsbn = $item->book->isbn ?? $displayIsbn;
                                if ($item->book->image) {
                                    $displayImage = asset('storage/' . $item->book->image);
                                }
                            } else {
                                $displayTitle = $item->product_name ?? null;
                            }
                        @endphp
                        @if($displayTitle)
                        @php
                            $soSym = ($order->currency === 'USD' ? '$' : ($order->currency === 'EUR' ? '€' : '₱'));
                            $itemQty = (float)($item->quantity ?? 0);
                            if ($itemQty <= 0) {
                                $itemQty = (float)($item->sent_qty ?? 0) > 0 
                                    ? (float)$item->sent_qty 
                                    : ((float)($item->customer_selected_qty ?? 0) > 0 
                                        ? (float)$item->customer_selected_qty 
                                        : ((float)($item->pickListItems?->first()?->requested_qty ?? 0) > 0 
                                            ? (float)$item->pickListItems->first()->requested_qty 
                                            : 0));
                            }
                            $itemPrice = (float)($item->unit_price ?? $item->price ?? 0);
                            $itemGross = $itemQty * $itemPrice;
                            $itemDiscVal = (float)($item->discount_value ?? 0);
                            $itemDiscType = $item->discount_type ?? 'percentage';
                            $itemDiscAmt = (float)($item->discount_amount ?? 0);
                            if ($itemDiscAmt <= 0 && $itemDiscVal > 0) {
                                $itemDiscAmt = $itemDiscType === 'percentage' ? $itemGross * ($itemDiscVal / 100) : $itemDiscVal;
                            }
                            $itemDiscAmt = min($itemGross, max(0, $itemDiscAmt));
                            $itemNetSubtotal = ($item->subtotal !== null && (float)$item->subtotal > 0 && (float)$item->subtotal <= $itemGross)
                                ? (float)$item->subtotal
                                : max(0, $itemGross - $itemDiscAmt);
                        @endphp
                        <tr>
                            <td class="text-center">{{ (float)$itemQty }}</td>
                            <td class="text-center text-uppercase">{{ $item->unit ?? $item->book?->unit ?? 'pcs' }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $displayImage }}" 
                                         style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"
                                         alt="Product Cover">
                                    <div>
                                        <div class="fw-bold">
                                            @if($isIndex)
                                                <span class="badge bg-warning text-dark me-1" style="font-size: 0.7rem;">INDEX ({{ $indexVal }})</span>
                                            @elseif($isBundle)
                                                <span class="badge text-white me-1" style="font-size: 0.7rem; background-color: #6f42c1;">BUNDLE</span>
                                            @endif
                                            {{ $displayTitle }}
                                        </div>
                                        @if($isIndex)
                                            <div class="small text-muted">Index Variant: {{ $indexVal }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $displayIsbn }}</td>
                            <td>{{ $item->area ?? '-' }}</td>
                            <td class="text-end">{{ $soSym }}{{ number_format($item->unit_price ?? $item->price, 2) }}</td>
                            <td class="text-center">
                                @if(($item->discount_value ?? 0) > 0 || ($item->discount_amount ?? 0) > 0)
                                    @if(($item->discount_type ?? 'percentage') === 'percentage' && ($item->discount_value ?? 0) > 0)
                                        {{ (float)$item->discount_value }}%
                                    @elseif(($item->discount_value ?? 0) > 0)
                                        {{ $soSym }}{{ number_format($item->discount_value, 2) }}
                                    @else
                                        {{ $soSym }}{{ number_format($item->discount_amount, 2) }}
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-end fw-bold">{{ $soSym }}{{ number_format($itemNetSubtotal, 2) }}</td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                    @php
                        $soSym = ($order->currency === 'USD' ? '$' : '₱');
                    @endphp
                    <tfoot>
                        @php
                            $serviceFee = $order->freight_option === 'freight_collect' ? 50 : 0;
                            $itemsSubtotal = $itemsToRender->sum(function($item) {
                                $itemQty = (float)($item->quantity ?? 0);
                                $itemPrice = (float)($item->unit_price ?? $item->price ?? 0);
                                $itemGross = $itemQty * $itemPrice;
                                $itemDiscVal = (float)($item->discount_value ?? 0);
                                $itemDiscType = $item->discount_type ?? 'percentage';
                                $itemDiscAmt = (float)($item->discount_amount ?? 0);
                                if ($itemDiscAmt <= 0 && $itemDiscVal > 0) {
                                    $itemDiscAmt = $itemDiscType === 'percentage' ? $itemGross * ($itemDiscVal / 100) : $itemDiscVal;
                                }
                                $itemDiscAmt = min($itemGross, max(0, $itemDiscAmt));
                                return ($item->subtotal !== null && (float)$item->subtotal > 0 && (float)$item->subtotal < $itemGross)
                                    ? (float)$item->subtotal
                                    : max(0, $itemGross - $itemDiscAmt);
                            });
                            $discountAmount = (float) ($order->discount_amount ?? 0);
                            $freightCharges = $order->freight_charges ?? 0;
                            $grandTotal = $activeInvoice ? (float)$activeInvoice->total_amount : max(0, $itemsSubtotal - $discountAmount + $freightCharges + $serviceFee);
                        @endphp
                        <tr>
                            <td colspan="7" class="text-end text-uppercase"><strong>Items Subtotal:</strong></td>
                            <td class="text-end fw-bold">{{ $soSym }}{{ number_format($itemsSubtotal, 2) }}</td>
                        </tr>
                        @if($discountAmount > 0)
                        <tr>
                            <td colspan="7" class="text-end text-uppercase">
                                <strong>
                                    Discount
                                    @if(($order->discount_percentage ?? 0) > 0)
                                        ({{ (float)$order->discount_percentage }}%)
                                    @endif:
                                </strong>
                            </td>
                            <td class="text-end fw-bold text-danger">- {{ $soSym }}{{ number_format($discountAmount, 2) }}</td>
                        </tr>
                        @endif
                        @if($order->freight_charges && $order->freight_charges > 0)
                        <tr>
                            <td colspan="7" class="text-end text-uppercase"><strong>Freight Charges:</strong></td>
                            <td class="text-end fw-bold">{{ $soSym }}{{ number_format($order->freight_charges, 2) }}</td>
                        </tr>
                        @endif
                        @if($serviceFee > 0)
                        <tr>
                            <td colspan="7" class="text-end text-uppercase"><strong>Service Fee:</strong></td>
                            <td class="text-end fw-bold">{{ $soSym }}{{ number_format($serviceFee, 2) }}</td>
                        </tr>
                        @endif
                        <tr style="background: #f8f9fa;">
                            <td colspan="7" class="text-end text-uppercase"><strong>Grand Total:</strong></td>
                            <td class="text-end fw-bold fs-5 text-primary">{{ $soSym }}{{ number_format($grandTotal, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Freight Information Section (for draft SOs) -->
                @if($order->status === 'draft')
                <div class="alert alert-info mt-3">
                    <h6 class="mb-2"><i class="fas fa-truck me-2"></i>Freight Quotation Status</h6>
                    @if($order->freight_charges && $order->freight_charges > 0)
                        <p class="mb-1"><strong>✓ Freight Charges Approved:</strong> {{ $soSym }}{{ number_format($order->freight_charges, 2) }}</p>
                    @endif
                </div>

                <table class="table table-bordered mt-3">
                    <thead>
                        <tr style="background: #e9ecef;">
                            <th colspan="2">Cost Breakdown</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-end" style="width: 50%;"><strong>Items Subtotal:</strong></td>
                            <td class="text-end">{{ $soSym }}{{ number_format($itemsSubtotal, 2) }}</td>
                        </tr>
                        @if($discountAmount > 0)
                        <tr>
                            <td class="text-end">
                                <strong>
                                    Discount
                                    @if(($order->discount_percentage ?? 0) > 0)
                                        ({{ (float)$order->discount_percentage }}%)
                                    @endif:
                                </strong>
                            </td>
                            <td class="text-end text-danger">- {{ $soSym }}{{ number_format($discountAmount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-end"><strong>Freight Charges:</strong></td>
                            <td class="text-end text-success"><strong>{{ $soSym }}{{ number_format($order->freight_charges, 2) }}</strong></td>
                        </tr>
                        @if($serviceFee > 0)
                        <tr>
                            <td class="text-end"><strong>Service Fee:</strong></td>
                            <td class="text-end text-success"><strong>{{ $soSym }}{{ number_format($serviceFee, 2) }}</strong></td>
                        </tr>
                        @endif
                        <tr style="background: #f0f0f0;">
                            <td class="text-end"><strong>Grand Total:</strong></td>
                            <td class="text-end"><strong>{{ $soSym }}{{ number_format($grandTotal, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
                @endif

                <!-- Actions -->
                <div class="d-flex justify-content-end gap-2 mt-4 form-actions">
                    @if($order->status !== 'completed' && $order->status !== 'cancelled')
                    <button type="button" class="btn btn-success" id="saveChangesBtn" onclick="saveOrderChanges('{{ $order->id }}')">
                        <i class="las la-save me-2"></i>Save Changes
                    </button>
                    @endif
                    <button type="button" class="btn btn-dark" onclick="window.history.back()">
                        <i class="las la-arrow-left me-2"></i>Back
                    </button>
                    <button type="button" class="btn btn-light border" onclick="window.print()">
                        <i class="las la-print me-2"></i>Print Order
                    </button>
                    <a href="{{ route('marketing.sales-orders.print-invoice', $order->id) }}" target="_blank" class="btn btn-primary">
                        <i class="las la-file-invoice me-1"></i>Print Sales Invoice
                    </a>
                    <button type="button" class="btn btn-info text-white" onclick="printShippingLabel('{{ route('marketing.sales-orders.shipping-label', $order->id) }}')">
                        <i class="las la-tag me-2"></i>Shipping Label
                    </button>
                    <iframe id="shippingLabelFrame" style="display:none;"></iframe>
                    @if($order->type === 'direct_consignment' || $order->type === 'evaluation')
                    <a href="{{ route('marketing.nbs-consignment-receipt', $order->id) }}" class="btn btn-primary" target="_blank">
                        <i class="las la-file-invoice me-2"></i>Print NBS Consignment DR
                    </a>
                    @endif
                    <!-- Workflow Actions -->
                    @php
                        $isMktManager = str_contains(auth()->user()->position, 'Manager') || str_contains(auth()->user()->position, 'Supervisor');
                    @endphp
                    
                    @if($order->status === 'pending_mkt_approval' && $isMktManager)
                    <form action="{{ route('marketing.sales-orders.reject', $order->id) }}" method="POST" id="rejectForm">
                        @csrf
                        <input type="hidden" name="remarks" id="rejectRemarks">
                        <button type="button" class="btn btn-outline-danger" onclick="confirmReject()">
                            <i class="las la-times-circle me-2"></i>Reject Order
                        </button>
                    </form>
                    <form action="{{ route('marketing.sales-orders.approve', $order->id) }}" method="POST" id="mktApproveForm">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="las la-check-circle me-2"></i>Approve Order
                        </button>
                    </form>
                    @endif

                    <!-- Proceed to Final SO Button (for draft with freight) -->
                    @php
                        $isFreightApproved = $order->freight_charges !== null || ($order->freightQuotation && in_array($order->freightQuotation->workflow_status, ['approved', 'linked_to_so']));
                    @endphp
                    @if($order->status === 'draft' && $isFreightApproved)
                    <form action="{{ route('marketing.sales-orders.proceed-to-final', $order->id) }}" method="POST" id="proceedForm">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="las la-arrow-right me-2"></i>Proceed to Final Sales Order
                        </button>
                    </form>
                    @endif
                </div>
                @else
                    <div class="alert alert-warning text-center">
                        Sales Order not found or ID missing. <a href="{{ route('marketing.sales-orders.list') }}">Return to List</a>
                    </div>
                @endif
                </div> <!-- End Screen Only -->

                <!-- PRINT VIEW -->
                @if($order)
                <div class="print-only" style="padding: 10px;">
                    <!-- Header with Logo & Official Details -->
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: #000 !important;">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ asset('images/claeritian_logo.png') }}" style="height: 65px;">
                            <div>
                                <h4 class="fw-black mb-0 text-uppercase" style="font-family: Arial, sans-serif; font-size: 14pt; color: #000; letter-spacing: 0.3px;">CLARETIAN COMMUNICATIONS FOUNDATION, INC.</h4>
                                <div style="font-size: 8.5pt; font-weight: bold; color: #333;">Non-Vat Reg. TIN: 000-395-713-00000</div>
                                <div style="font-size: 8pt; color: #222;">8 Mayumi Street, U.P. Village, Diliman, 1101 Quezon City NCR, Second District Philippines</div>
                                <div style="font-size: 8pt; color: #222;">Tel: (02) 8921-3984 Fax: (02) 8921-6205</div>
                            </div>
                        </div>
                        <div class="text-end">
                            @php
                                $printSiNumber = $order->si_number ?: (\App\Models\SalesInvoice::where('so_id', $order->id)->value('si_number') ?? $order->so_number);
                            @endphp
                            <div class="fw-bold" style="font-size: 11pt;">No. <span class="text-danger" style="font-size: 12pt;">{{ $printSiNumber }}</span></div>
                            <div class="fw-black text-uppercase" style="font-size: 15pt; letter-spacing: 1px;">Sales - Invoice</div>
                        </div>
                    </div>

                    <!-- Upper Info Grid -->
                    <div class="row mb-4">
                        <!-- Left: Customer -->
                        <div class="col-7">
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; height: 100%; border: 1px solid #eee;">
                                <table class="table-sm table-borderless w-100 m-0" style="font-size: 10pt;">
                                    <tr>
                                        <td class="fw-bold text-muted" style="width: 80px; vertical-align: top;">Customer:</td>
                                        <td class="fw-bold" style="font-size: 11pt; color: #333;">
                                            {{ $order->customer?->customer_name ?? '' }}
                                            @if($order->customer)
                                                @if($order->customer->isBadClient)
                                                    <span style="background: #dc3545; color: white; padding: 2px 6px; border-radius: 3px; font-size: 8pt; margin-left: 8px; display: inline-block;">BAD CLIENT</span>
                                                @else
                                                    <span style="background: #28a745; color: white; padding: 2px 6px; border-radius: 3px; font-size: 8pt; margin-left: 8px; display: inline-block;">GOOD CLIENT</span>
                                                @endif
                                            @endif
                                            @if($order->type === 'area_sales_consignment')
                                                <br><span style="font-size: 9pt; color: #555; font-weight: normal;">Area Sales Staff: <strong>{{ $order->areaSalesStaff?->name ?? '—' }}</strong></span>
                                            @endif
                                            <br>
                                            <span class="fw-normal">{{ $order->customer?->company_name ?? '' }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted" style="vertical-align: top; padding-top: 10px;">Address:</td>
                                        <td style="padding-top: 10px; color: #444;"><span id="printAddress">{{ $order->shipping_address ?: ($order->customer?->shipping_address ?: ($order->customer?->billing_address ?? '')) }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Right: Meta fields with borders -->
                        <div class="col-5">
                            <table class="table-sm w-100" style="font-size: 10pt; border-collapse: collapse; height: 100%;">
                                <tr>
                                    <td class="fw-bold text-muted bg-light" style="width: 70px; padding: 6px 10px; border: 1px solid #ddd;">Date:</td>
                                    <td class="text-center fw-bold text-dark" style="padding: 6px 10px; border: 1px solid #ddd;">{{ $order->created_at->format('m/d/Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted bg-light" style="padding: 6px 10px; border: 1px solid #ddd;">S.O. #:</td>
                                    <td class="text-center fw-bold text-danger" style="padding: 6px 10px; border: 1px solid #ddd; font-size: 11pt;">{{ $order->so_number }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted bg-light" style="padding: 6px 10px; border: 1px solid #ddd;">Terms:</td>
                                    <td class="text-center text-dark" style="padding: 6px 10px; border: 1px solid #ddd;">{{ $order->terms ?? 'Net 30' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted bg-light" style="padding: 6px 10px; border: 1px solid #ddd;">REF#:</td>
                                    <td class="text-center text-dark" style="padding: 6px 10px; border: 1px solid #ddd;">{{ $order->ref_number ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <table class="table table-sm w-100 mt-3" style="font-size: 10pt; border: 1px solid #ddd; border-top: 3px solid #cc0000;">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th class="text-center text-muted" style="width: 60px; font-weight: 700;">QTY</th>
                                <th class="text-muted" style="font-weight: 700;">DESCRIPTION</th>
                                <th class="text-muted" style="width: 120px; font-weight: 700;">ISBN</th>
                                <th class="text-center text-muted" style="width: 70px; font-weight: 700;">AREA</th>
                                <th class="text-end text-muted" style="width: 110px; font-weight: 700;">UNIT PRICE</th>
                                <th class="text-end text-muted" style="width: 110px; font-weight: 700;">AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($itemsToRender as $item)
                            @php $itemName = $item->book?->name ?? ($item->product_name ?? null); @endphp
                            @if($itemName)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td class="text-center fw-bold">{{ (float)$item->quantity }}</td>
                                <td class="fw-bold text-dark">{{ $itemName }}</td>
                                <td class="text-muted">{{ $item->isbn ?? '-' }}</td>
                                <td class="text-center">{{ $item->area ?? '-' }}</td>
                                <td class="text-end text-dark">{{ number_format($item->unit_price ?? $item->price, 2) }}</td>
                                <td class="text-end fw-bold text-dark">{{ number_format($item->amount ?? $item->subtotal, 2) }}</td>
                            </tr>
                            @endif
                            @endforeach
                            @if($order->remarks)
                            <tr>
                                <td colspan="6" class="p-3 text-muted fst-italic" style="background: #fafafa; border-bottom: 1px solid #eee;">
                                    <strong>Remarks:</strong> {{ $order->remarks }}
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>

                    <!-- Totals Block -->
                    <div class="row mt-4 mb-5">
                        <div class="col-7">
                            <div class="text-muted" style="font-size: 9pt;">
                                * This document serves as a sales order and is not valid for claiming input taxes.<br>
                                * Terms are strictly on a {{ $order->terms ?? 'Net 30' }} basis unless otherwise specified.
                            </div>
                        </div>
                        <div class="col-5 text-end">
                            <div style="background: #fff5f5; padding: 15px; border-radius: 6px; border: 1px solid #ffcccc;">
                                <h4 class="fw-bold mb-0 d-flex justify-content-between align-items-center" style="font-family: Arial, sans-serif; color: #cc0000;">
                                    <span style="font-size: 13pt;">TOTAL:</span>
                                    <span>PHP {{ number_format($totalSalesAmount, 2) }}</span>
                                </h4>
                            </div>
                        </div>
                    </div>

                    <!-- Signatories -->
                    @php
                        $siPreparer = $order->siPreparedBy ?: ($order->invoices->first()?->createdBy ?: $order->preparedBy);
                        $siApprover = $order->signedBy ?: ($order->acctApprovedBy ?: ($order->invoices->first()?->approvedBy ?: ($order->mktApprovedBy ?: $order->prodApprovedBy)));
                        $siPrepDate = $order->si_prepared_at ? \Carbon\Carbon::parse($order->si_prepared_at)->format('m/d/Y') : ($order->created_at ? $order->created_at->format('m/d/Y') : '-');
                        $siApprDate = $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('m/d/Y') : ($order->acct_approved_at ? \Carbon\Carbon::parse($order->acct_approved_at)->format('m/d/Y') : ($order->mkt_approved_at ? \Carbon\Carbon::parse($order->mkt_approved_at)->format('m/d/Y') : '-'));
                    @endphp
                    <div class="row mt-5 pt-3" style="font-size: 9pt;">
                        <div class="col-4">
                            <div class="mb-4 fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Prepared By:</div>
                            <div class="border-bottom border-dark mb-1" style="width: 90%;"></div>
                            <div class="fw-bold text-dark" style="font-size: 10pt;">{{ $siPreparer->name ?? '____________________' }}</div>
                            <div class="text-muted">{{ $siPreparer->position ?? ($order->siPreparedBy ? 'Accounting Staff' : 'Sales Representative') }}</div>
                            <div class="text-muted">Date: {{ $siPrepDate }}</div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="mb-4 fw-bold text-uppercase text-muted text-start" style="letter-spacing: 0.5px; padding-left: 5%;">Checked By:</div>
                            <div class="border-bottom border-dark mb-1 mx-auto" style="width: 90%;"></div>
                            <div class="text-muted mt-2">&nbsp;</div>
                            <div class="text-muted">&nbsp;</div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="mb-4 fw-bold text-uppercase text-muted text-start" style="letter-spacing: 0.5px; padding-left: 10%;">Approved By:</div>
                            <div class="border-bottom border-dark mb-1 ms-auto" style="width: 90%;"></div>
                            @if($siApprover)
                                <div class="fw-bold text-start text-dark" style="font-size: 10pt; padding-left: 10%;">{{ $siApprover->name }}</div>
                                <div class="text-muted text-start" style="padding-left: 10%;">{{ $siApprover->position ?? ($order->signedBy ? 'Finance Manager' : 'Authorized Signatory') }}</div>
                                <div class="text-muted text-start" style="padding-left: 10%;">Date: {{ $siApprDate }}</div>
                            @else
                                <div class="text-muted mt-2 text-start" style="padding-left: 10%;">&nbsp;</div>
                                <div class="text-muted text-start" style="padding-left: 10%;">&nbsp;</div>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- End Print View -->
                @endif
            </div>

        </div>
    </div>
    @push('scripts')
    <script>
        // Update print address preview when user edits address
        document.addEventListener('DOMContentLoaded', function() {
            const addressInput = document.getElementById('orderAddress');
            const printAddress = document.getElementById('printAddress');
            
            if (addressInput && printAddress) {
                addressInput.addEventListener('input', function() {
                    printAddress.textContent = this.value || 'Address...';
                });
            }

            // Freight Option Change Handler
            const freightOptionSelect = document.getElementById('freightOptionSelect');
            if (freightOptionSelect) {
                freightOptionSelect.addEventListener('change', function() {
                    const serviceFeeRow = document.getElementById('serviceFeeRow');
                    const forwarderContainer = document.getElementById('forwarderContainer');
                    if (this.value === 'freight_collect') {
                        serviceFeeRow.style.display = '';
                    } else {
                        serviceFeeRow.style.display = 'none';
                    }
                    if (forwarderContainer) {
                        forwarderContainer.style.display = this.value ? 'block' : 'none';
                    }
                });
                
                // Check on page load
                if (freightOptionSelect.value === 'freight_collect') {
                    document.getElementById('serviceFeeRow').style.display = '';
                }
                const forwarderContainer = document.getElementById('forwarderContainer');
                if (forwarderContainer) {
                    forwarderContainer.style.display = freightOptionSelect.value ? 'block' : 'none';
                }
            }
        });

        function saveOrderChanges(orderId) {
            const freightOption = document.getElementById('freightOptionSelect')?.value || '';
            const forwarderVal = document.getElementById('forwarderInput')?.value || '';

            const saveBtn = document.getElementById('saveChangesBtn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="las la-spinner la-spin me-2"></i>Saving...';

            fetch(`/marketing/sales-orders/${orderId}/quick-update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    freight_option: freightOption,
                    forwarder: forwarderVal
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Freight Option updated successfully!');
                    location.reload();
                } else {
                    alert('Error updating order: ' + (data.message || 'Unknown error'));
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="las la-save me-2"></i>Save Changes';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving changes: ' + error.message);
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="las la-save me-2"></i>Save Changes';
            });
        }

        function printShippingLabel(url) {
            const iframe = document.getElementById('shippingLabelFrame');
            const originalTitle = document.title;
            const editedAddress = document.getElementById('orderAddress')?.value || '';
            
            // Pass edited address to shipping label if user modified it
            const separator = url.includes('?') ? '&' : '?';
            const finalUrl = editedAddress ? `${url}${separator}address=${encodeURIComponent(editedAddress)}` : url;
            
            // Set parents title to a space to minimize browser headers
            document.title = ' ';
            iframe.src = finalUrl;
            
            iframe.onload = function() {
                setTimeout(function() {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                    
                    // Restore title after a delay
                    setTimeout(() => {
                        document.title = originalTitle;
                    }, 1000);
                }, 500);
            };
        }
        function confirmReject() {
            if (typeof showAppModal === 'function') {
                showAppModal('Reject Sales Order', 'Please provide a reason for rejection:', {
                    type: 'input',
                    placeholder: 'Enter rejection remarks...',
                    confirmText: 'Confirm Reject',
                    confirmClass: 'btn-danger',
                    onConfirm: function(remarks) {
                        if (remarks && remarks.trim() !== '') {
                            document.getElementById('rejectRemarks').value = remarks.trim();
                            document.getElementById('rejectForm').submit();
                        } else {
                            alert('Rejection reason is required.');
                        }
                    }
                });
            } else {
                let remarks = prompt('Please provide a reason for rejection:');
                if (remarks && remarks.trim() !== '') {
                    document.getElementById('rejectRemarks').value = remarks.trim();
                    document.getElementById('rejectForm').submit();
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
