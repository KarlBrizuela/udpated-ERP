<x-app-layout :title="'Delivery Receipt'" :sidebar="$sidebar ?? 'production'">
    <div class="row">
        <div class="col-xl-12">
            <div class="card receipt-form">


                <!-- Form Header -->
                <div class="form-header">
                    <div class="company-info">
                        <img src="{{ asset('images/claeritian_logo.png') }}" alt="Claretian Logo" class="company-logo-img me-2" style="height: 50px; width: auto; object-fit: contain;">
                        <div class="company-details">
                            <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                            <div class="company-address">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                            <div class="company-contact">Tel. No.: 921-3984</div>
                        </div>
                    </div>
                    <div class="document-title">DELIVERY RECEIPT</div>
                    <div class="text-center text-muted small fw-bold mb-1">NON-VAT REGISTERED</div>
                    <div class="text-center extra-small text-muted italic mb-2">"This document is not valid for claim of input taxes."</div>
                </div>

                @php
                    $soNumStr = strtolower($order?->so_number ?? '');
                    $drNumStr = strtolower($deliveryReceipt?->dr_number ?? '');
                    $cNameStr = strtolower($order?->customer?->customer_name ?? '');
                    $cRepStr = strtolower($order?->customer_representative ?? '');
                    $isNBS = str_contains($soNumStr, 'nbs') || 
                             str_contains($drNumStr, 'nbs') || 
                             str_contains($cNameStr, 'national book store') || 
                             str_contains($cNameStr, 'nbs') || 
                             str_contains($cRepStr, 'national book store') || 
                             str_contains($cRepStr, 'nbs');

                    $poNumber = null;
                    if ($isNBS) {
                        $poNumber = $order?->ref_number ?? ($order?->po_number ?? null);
                        if (empty($poNumber) && $order) {
                            if (preg_match('/(?:DR-)?SO-NBS-([^-]+)/i', $order->so_number ?? '', $m)) {
                                $poNumber = $m[1];
                            }
                        }
                        if (empty($poNumber) && isset($deliveryReceipt) && $deliveryReceipt?->dr_number) {
                            if (preg_match('/(?:DR-)?SO-NBS-([^-]+)/i', $deliveryReceipt->dr_number, $m)) {
                                $poNumber = $m[1];
                            }
                        }
                    }
                @endphp
                <!-- Receipt Details -->
                <div class="form-info-row">
                    <div class="form-info-item">
                        <label>DR No.:</label>
                        <input type="text" class="form-control" id="receiptNumber" placeholder="Enter DR number" value="{{ $order ? 'DR-' . $order->so_number : '' }}" {{ $order ? 'readonly' : '' }}>
                    </div>
                    <div class="form-info-item">
                        <label>Date:</label>
                        <input type="date" class="form-control" id="receiptDate" value="{{ $order ? ($order->dr_prepared_at ? \Carbon\Carbon::parse($order->dr_prepared_at)->format('Y-m-d') : date('Y-m-d')) : date('Y-m-d') }}">
                    </div>
                    <div class="form-info-item">
                        <label>Sales Order:</label>
                        <input type="text" class="form-control" placeholder="Sales Order" value="{{ $order ? $order->so_number : '' }}" readonly>
                    </div>
                    @if($isNBS && !empty($poNumber))
                    <div class="form-info-item">
                        <label>PO Number:</label>
                        <input type="text" class="form-control" placeholder="PO Number" value="{{ $poNumber }}" readonly>
                    </div>
                    @endif
                    @if($order?->cancellation_date)
                    <div class="form-info-item cancellation-date-item">
                        <label class="cancellation-date-label">Cancel Date:</label>
                        <input type="text" class="form-control cancellation-date-input" value="{{ \Carbon\Carbon::parse($order->cancellation_date)->format('M d, Y') }}" readonly>
                    </div>
                    @endif
                </div>


                @if($order)
                    @php
                        $bName = $order->customer_representative;
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

                        $accountNo = $bCompany?->account_number ?: ($order->customer?->account_number ?? null);
                        $acctCompany = $accountNo ? \App\Models\Company::where('account_number', $accountNo)->first() : null;

                        $displayCompanyName = $bCompany?->parent?->company_name 
                            ?: ($bCompany?->company_name 
                            ?: ($acctCompany?->parent?->company_name 
                            ?: ($acctCompany?->company_name 
                            ?: ($order->customer?->company_name && !in_array(strtolower($order->customer->company_name), ['intracode', 'individual']) ? $order->customer->company_name : ($order->customer?->customer_name ?? 'N/A')))));

                        $displayCustomerName = $bCompany?->company_name 
                            ?: ($order->customer_representative ?: ($order->customer?->customer_name ?? 'Unknown'));
                    @endphp
                    <!-- Delivered To Section -->
                    <div class="form-group">
                        <label class="fw-bold">Company:</label>
                        <input type="text" class="form-control" value="{{ $displayCompanyName }}" readonly>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold">Customer Name:</label>
                        <input type="text" class="form-control" value="{{ $displayCustomerName }}" readonly>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold">Contact:</label>
                        <input type="text" class="form-control" value="{{ $order->customer_contact ?: ($order->customer?->mobile ?: ($order->customer?->main_phone ?: 'N/A')) }}" readonly>
                    </div>

                    <!-- Delivery Address -->
                    <div class="form-group">
                        <label class="fw-bold">Delivery Address:</label>
                        <textarea class="form-control screen-only-remarks" readonly style="background:#fff; min-height: 50px;">{{ $order->shipping_address ?: ($order->customer->shipping_address ?? $order->customer->billing_address ?? '') }}</textarea>
                        <div class="print-only-remarks">{{ $order->shipping_address ?: ($order->customer->shipping_address ?? $order->customer->billing_address ?? 'N/A') }}</div>
                    </div>

                    <!-- Terms -->
                    @if($order->terms)
                        <div class="form-group">
                            <label class="fw-bold">Terms:</label>
                            <textarea class="form-control screen-only-remarks" readonly style="min-height: 60px; background:#fff;">{{ $order->terms }}</textarea>
                            <div class="print-only-remarks">{{ $order->terms }}</div>
                        </div>
                    @endif

                    <!-- Remarks / Notes -->
                    <div class="form-group">
                        <label class="fw-bold">Remarks / Notes:</label>
                        <div class="d-flex flex-column gap-2">
                            <textarea id="drRemarksTextarea" name="remarks" class="form-control screen-only-remarks" style="background:#fff; font-weight:600;" rows="2" placeholder="Enter remarks or special instructions...">{{ $order->remarks ?: ($order->notes ?: ($deliveryReceipt->remarks ?? '')) }}</textarea>
                            <div class="print-only-remarks" id="drPrintRemarksContent">{{ $order->remarks ?: ($order->notes ?: ($deliveryReceipt->remarks ?? 'None')) }}</div>
                            <button type="button" class="btn btn-sm btn-primary align-self-start no-print" onclick="saveDrRemarks()" style="background:#0d6efd; border:none; border-radius:6px; font-weight:600; padding: 0.4rem 1.25rem;">
                                <i class="las la-save me-1"></i>Save Remarks
                            </button>
                        </div>
                    </div>

                    <!-- Delivery Receipt Items Table -->
                    @php
                        $isConsignment = $order && in_array($order->type, ['area_consignment', 'area_sales_consignment', 'direct_consignment']);
                        $rawItems = ($deliveryReceipt && count($deliveryReceipt->items) > 0) ? $deliveryReceipt->items : ($order && count($order->items) > 0 ? $order->items : ($deliveryReceipt ? $deliveryReceipt->items : []));
                        $displayItems = collect($rawItems);
                        
                        $grossSubtotal = 0;
                        $totalItemDiscounts = 0;
                    @endphp

                    <form action="{{ route('production.logistic.delivery-receipt.update-pick-qty', $order->id) }}" method="POST" id="drPickQtyForm">
                        @csrf
                        <div class="my-3" style="width: 100%;">
                            <table class="receipt-table table border">
                                <thead>
                                    <tr>
                                        <th style="width: 110px; text-align: center;">{{ $isConsignment ? 'SENT QTY' : 'QUANTITY' }}</th>
                                        @if($isConsignment)
                                            <th class="pick-qty-col" style="width: 130px; text-align: center; background-color: #0d6efd !important; color: #fff;">PICK QTY</th>
                                        @endif
                                        <th>DESCRIPTION</th>
                                        <th style="width: 140px; text-align: right;">UNIT PRICE</th>
                                        <th style="width: 110px; text-align: center;">DISCOUNT</th>
                                        <th style="width: 140px; text-align: right;">AMOUNT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($displayItems as $item)
                                        @php
                                            $sQty = (float)($item->sent_qty ?? 0);
                                            $rQty = (float)($item->requested_qty ?? 0);
                                            $iQty = (float)($item->quantity ?? 0);
                                            $plReqQty = 0;
                                            if (isset($item->pickListItems) && count($item->pickListItems) > 0) {
                                                $plReqQty = (float)($item->pickListItems->first()->requested_qty ?? 0);
                                            } elseif (isset($item->pick_list_items) && count($item->pick_list_items) > 0) {
                                                $plReqQty = (float)($item->pick_list_items->first()->requested_qty ?? 0);
                                            }

                                            if ($sQty > 0) {
                                                $qty = (int)$sQty;
                                            } elseif ($iQty > 0) {
                                                $qty = (int)$iQty;
                                            } elseif ($rQty > 0) {
                                                $qty = (int)$rQty;
                                            } elseif ($plReqQty > 0) {
                                                $qty = (int)$plReqQty;
                                            } else {
                                                $qty = (int)$iQty;
                                            }

                                            $pickQty = (!empty($item->customer_selected_qty) && (float)$item->customer_selected_qty > 0) 
                                                ? (int)$item->customer_selected_qty 
                                                : ($qty > 0 ? $qty : (int)($item->quantity ?? 0));
                                            $unitPrice = (float)($item->unit_price ?? ($item->price ?? 0));
                                            $itemSubtotal = ($isConsignment ? $pickQty : $qty) * $unitPrice;
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
                                            $rowAmount = max(0, $itemSubtotal - $itemDiscountAmt);
                                        @endphp
                                        <tr>
                                            <td style="text-align: center;">{{ $qty }}</td>
                                            @if($isConsignment)
                                                <td class="pick-qty-col" style="text-align: center; background-color: #f0f7ff; padding: 4px;">
                                                    <input type="number" 
                                                           class="form-control form-control-sm text-center fw-bold pick-qty-input" 
                                                           name="pick_qty[{{ $item->id }}]" 
                                                           value="{{ $pickQty }}" 
                                                           min="0" 
                                                           max="{{ $qty }}" 
                                                           data-price="{{ $unitPrice }}"
                                                           data-qty="{{ $qty }}"
                                                           style="width: 90px; margin: 0 auto; color: #0d6efd; border-color: #0d6efd; background-color: #fff;">
                                                    <span class="d-none print-pick-qty fw-bold text-center">{{ $pickQty }}</span>
                                                </td>
                                            @endif
                                            <td>
                                                @php
                                                    $dItemName = $item->bookIndex?->display_name ?? ($item->bookIndex?->title ?? ($item->bookIndex?->custom_name ?? ($item->bookIndex?->book?->name ?? ($item->book?->name ?? ($item->bundle?->name ?? ($item->product?->name ?? ($item->item_name ?? ($item->product_name ?? 'Unknown Item'))))))));
                                                    $dType = $item->item_type ?? ($item->bookIndex || !empty($item->book_index_id) ? 'Index' : (!empty($item->bundle_id) || !empty($item->bundle) ? 'Bundle' : 'Book'));

                                                    $soNum = strtolower($order->so_number ?? '');
                                                    $drNum = strtolower($deliveryReceipt->dr_number ?? '');
                                                    $cName = strtolower($order->customer?->customer_name ?? '');
                                                    $cRep = strtolower($order->customer_representative ?? '');
                                                    $isNBS = str_contains($soNum, 'nbs') || 
                                                             str_contains($drNum, 'nbs') || 
                                                             str_contains($cName, 'national book store') || 
                                                             str_contains($cName, 'nbs') || 
                                                             str_contains($cRep, 'national book store') || 
                                                             str_contains($cRep, 'nbs');

                                                    $articleNo = $item->article_number 
                                                        ?? ($item->article 
                                                        ?? ($item->bookIndex->article_number 
                                                        ?? ($item->bookIndex->article 
                                                        ?? ($item->book->article_number 
                                                        ?? ($item->book->article 
                                                        ?? ($item->bookIndex->barcode 
                                                        ?? ($item->bookIndex->item_code 
                                                        ?? ($item->book->sku 
                                                        ?? ($item->book->item_code ?? null)))))))));
                                                @endphp
                                                <div class="d-flex align-items-center flex-wrap gap-1">
                                                    <span class="fw-semibold text-dark">{{ $dItemName }}</span>
                                                    @if($dType === 'Bundle')
                                                        <span class="badge ms-1" style="background:#6f42c1; color:#fff; font-size: 11px; padding: 3px 7px;">Bundle</span>
                                                    @elseif($dType === 'Index')
                                                        <span class="badge bg-info text-dark ms-1" style="font-size: 11px; padding: 3px 7px;">Index</span>
                                                    @else
                                                        <span class="badge bg-primary ms-1" style="font-size: 11px; padding: 3px 7px;">Book</span>
                                                    @endif
                                                </div>
                                                @if($isNBS && !empty($articleNo))
                                                    <div class="print-only-article" style="font-size: 11px; font-weight: bold; color: #000; margin-top: 2px;">
                                                        Article #: {{ $articleNo }}
                                                    </div>
                                                @endif
                                            </td>
                                            @php
                                                $drSym = (($order->currency ?? ($dr->salesOrder->currency ?? 'PHP')) === 'USD' ? '$' : '₱');
                                            @endphp
                                            <td style="text-align: right;">{{ $drSym }}{{ number_format($unitPrice, 2) }}</td>
                                            <td style="text-align: center;">
                                                @if(($item->discount_value ?? 0) > 0 || ($item->discount_amount ?? 0) > 0)
                                                    @if(($item->discount_type ?? 'percentage') === 'percentage' && ($item->discount_value ?? 0) > 0)
                                                        {{ (float)$item->discount_value }}%
                                                    @elseif(($item->discount_value ?? 0) > 0)
                                                        {{ $drSym }}{{ number_format($item->discount_value, 2) }}
                                                    @else
                                                        {{ $drSym }}{{ number_format($item->discount_amount, 2) }}
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td style="text-align: right; font-weight: 600;" class="row-amount-td">{{ $drSym }}{{ number_format($rowAmount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $isConsignment ? 6 : 5 }}" class="text-center py-3 text-muted">No items found for this delivery receipt</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @php
                                     $drSym = (($order->currency ?? ($dr->salesOrder->currency ?? 'PHP')) === 'USD' ? '$' : '₱');
                                     $orderDiscountAmt = (float)($order->discount_amount ?? 0);
                                     if ($orderDiscountAmt == 0 && ($order->discount_percentage ?? 0) > 0) {
                                         $orderDiscountAmt = max(0, $grossSubtotal - $totalItemDiscounts) * ((float)$order->discount_percentage / 100);
                                     }
                                     $allDiscountsCombined = $totalItemDiscounts + $orderDiscountAmt;
                                     $freightChargesAmt = (float)($order->freight_charges ?? 0);
                                     $finalTotalAmt = max(0, $grossSubtotal - $allDiscountsCombined + $freightChargesAmt);
                                 @endphp
                                 <tfoot>
                                     <tr>
                                         <td colspan="4" class="text-end text-uppercase"><strong>Gross Subtotal:</strong></td>
                                         @if($isConsignment)
                                             <td class="pick-qty-col d-print-none"></td>
                                         @endif
                                         <td class="text-end fw-bold">{{ $drSym }}{{ number_format($grossSubtotal, 2) }}</td>
                                     </tr>
                                     @if($totalItemDiscounts > 0)
                                     <tr>
                                         <td colspan="4" class="text-end text-uppercase"><strong>Items Discount Subtotal:</strong></td>
                                         @if($isConsignment)
                                             <td class="pick-qty-col d-print-none"></td>
                                         @endif
                                         <td class="text-end fw-bold text-danger">- {{ $drSym }}{{ number_format($totalItemDiscounts, 2) }}</td>
                                     </tr>
                                     @endif
                                     @if($orderDiscountAmt > 0)
                                     <tr>
                                         <td colspan="4" class="text-end text-uppercase"><strong>Order Discount @if(($order->discount_percentage ?? 0) > 0)({{ (float)$order->discount_percentage }}%)@endif:</strong></td>
                                         @if($isConsignment)
                                             <td class="pick-qty-col d-print-none"></td>
                                         @endif
                                         <td class="text-end fw-bold text-danger">- {{ $drSym }}{{ number_format($orderDiscountAmt, 2) }}</td>
                                     </tr>
                                     @endif
                                     @if($allDiscountsCombined > 0)
                                     <tr style="background-color: #fff3cd;">
                                         <td colspan="4" class="text-end text-uppercase fw-bold text-dark">Total Discount:</td>
                                         @if($isConsignment)
                                             <td class="pick-qty-col d-print-none"></td>
                                         @endif
                                         <td class="text-end fw-bold text-danger" style="font-size: 15px;">- {{ $drSym }}{{ number_format($allDiscountsCombined, 2) }}</td>
                                     </tr>
                                     @endif
                                     @if($freightChargesAmt > 0)
                                     <tr>
                                         <td colspan="4" class="text-end text-uppercase"><strong>Freight Charges:</strong></td>
                                         @if($isConsignment)
                                             <td class="pick-qty-col d-print-none"></td>
                                         @endif
                                         <td class="text-end fw-bold">{{ $drSym }}{{ number_format($freightChargesAmt, 2) }}</td>
                                     </tr>
                                     @endif
                                     <tr style="background-color: #f8f9fa;">
                                         <td colspan="4" class="text-end text-uppercase fw-bold text-dark" style="font-size: 1rem;">Total Amount:</td>
                                         @if($isConsignment)
                                             <td class="pick-qty-col d-print-none"></td>
                                         @endif
                                         <td class="text-end fw-bold text-primary" style="font-size: 1.1rem;"><span id="drTotalAmountDisplayTable">{{ $drSym }}{{ number_format($finalTotalAmt > 0 ? $finalTotalAmt : ($order->total_amount ?? 0), 2) }}</span></td>
                                     </tr>
                                 </tfoot>
                            </table>
                        </div>

                        @if($isConsignment && count($displayItems) > 0)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <small class="text-muted"><i class="las la-info-circle me-1"></i> You can edit Pick Qty directly here and click Save Pick Qty to update the record.</small>
                                <button type="submit" class="btn btn-sm btn-primary px-3 shadow-sm">
                                    <i class="las la-save me-1"></i> Save Pick Qty
                                </button>
                            </div>
                        @endif
                    </form>

                    <!-- Total Amount (Screen Only) -->
                    <div class="d-print-none" style="text-align: right; margin-bottom: 1.5rem; font-size: 1.1rem; font-weight: 600;">
                        <div class="small text-muted mb-1">Gross Subtotal: {{ $drSym }}{{ number_format($grossSubtotal, 2) }}</div>
                        @if($allDiscountsCombined > 0)
                            <div class="small text-danger mb-1">Total Discount (Books + Order): - {{ $drSym }}{{ number_format($allDiscountsCombined, 2) }}</div>
                        @endif
                        @if($freightChargesAmt > 0)
                            <div class="small text-secondary mb-1">Freight Charges: + {{ $drSym }}{{ number_format($freightChargesAmt, 2) }}</div>
                        @endif
                        <strong>Total Amount: <span id="drTotalAmountDisplay">{{ $drSym }}{{ number_format($finalTotalAmt > 0 ? $finalTotalAmt : ($order->total_amount ?? 0), 2) }}</span></strong>
                    </div>
                @else
                    <!-- Empty Form for Creating New Receipt -->
                    <div class="form-group">
                        <label class="fw-bold">Delivered To:</label>
                        <select class="form-control" id="recipient">
                            <option value="">Select Customer</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="fw-bold">Delivery Address:</label>
                        <textarea class="form-control" rows="2" placeholder="Enter delivery address"></textarea>
                    </div>

                    <button type="button" class="btn btn-danger btn-sm mb-3" onclick="addRow()">+ Add Row</button>
                    
                    <div class="table-responsive">
                        <table class="receipt-table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">QUANTITY</th>
                                    <th>DESCRIPTION</th>
                                    <th style="width: 120px; text-align: right;">UNIT PRICE</th>
                                    <th style="width: 120px; text-align: right;">AMOUNT</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="receiptTableBody">
                                <tr>
                                    <td><input type="number" class="qty-input" value="0"></td>
                                    <td><input type="text" placeholder="Product description"></td>
                                    <td><input type="number" class="price-input" value="0.00" step="0.01" style="text-align: right;"></td>
                                    <td><input type="number" class="amount-input" value="0.00" readonly style="text-align: right;"></td>
                                    <td class="text-center"><button type="button" class="btn btn-xs sharp btn-danger" onclick="removeRow(this)"><i class="fa fa-times"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif

                @php
                    $preparedByName = 'System';
                    $approvedByName = 'Pending Approval';
                    if ($order) {
                        if ($order->drPreparedBy) {
                            $preparedByName = $order->drPreparedBy->name;
                        } elseif ($order->preparedBy) {
                            $preparedByName = $order->preparedBy->name;
                        }

                        if ($order->drApprovedBy) {
                            $approvedByName = $order->drApprovedBy->name;
                        } elseif ($order->signedBy) {
                            $approvedByName = $order->signedBy->name;
                        } elseif ($order->acctApprovedBy) {
                            $approvedByName = $order->acctApprovedBy->name;
                        } elseif ($order->mktApprovedBy) {
                            $approvedByName = $order->mktApprovedBy->name;
                        } else {
                            $approvedByName = 'Pending Approval';
                        }
                    } elseif (auth()->check()) {
                        $preparedByName = auth()->user()->name;
                        $approvedByName = 'Pending Approval';
                    }

                    $receivedByName = $order ? ($order->customer_representative ?: ($order->customer->customer_name ?? '')) : '';
                @endphp

                <!-- Signature Section -->
                <div class="signature-section">
                    <div class="signature-box">
                        <label class="fw-bold">Prepared by:</label>
                        <div class="signature-input-wrapper">
                            <input type="text" id="preparedBy" class="form-control" placeholder="Enter name" value="{{ $preparedByName }}">
                            <span class="signature-value-display" id="preparedByDisplay">{{ $preparedByName }}</span>
                        </div>
                        <div style="text-align: center; margin-top: 0.5rem;">
                            <div style="border-top: 1px solid #000; width: 85%; margin: 0 auto; padding-top: 0.2rem; font-size: 0.75rem; font-weight: bold;">SIGNATURE OVER PRINTED NAME</div>
                        </div>
                    </div>
                    <div class="signature-box">
                        <label class="fw-bold">Approved by:</label>
                        <div class="signature-input-wrapper">
                            <input type="text" id="approvedBy" class="form-control" placeholder="Enter name" value="{{ $approvedByName }}">
                            <span class="signature-value-display" id="approvedByDisplay">{{ $approvedByName }}</span>
                        </div>
                        <div style="text-align: center; margin-top: 0.5rem;">
                            <div style="border-top: 1px solid #000; width: 85%; margin: 0 auto; padding-top: 0.2rem; font-size: 0.75rem; font-weight: bold;">SIGNATURE OVER PRINTED NAME</div>
                        </div>
                    </div>
                    <div class="signature-box">
                        <label class="fw-bold">Received by:</label>
                        <div class="signature-input-wrapper">
                            <input type="text" id="receivedBy" class="form-control received-by-input" placeholder="Enter name" value="{{ $receivedByName }}">
                            <span class="signature-value-display" id="receivedByDisplay">{{ $receivedByName }}</span>
                        </div>
                        <div style="text-align: center; margin-top: 0.5rem;">
                            <div style="border-top: 1px solid #000; width: 85%; margin: 0 auto; padding-top: 0.2rem; font-size: 0.75rem; font-weight: bold;">SIGNATURE OVER PRINTED NAME</div>
                        </div>
                    </div>
                </div>

                @if($order && in_array($order->type, ['area_consignment', 'area_sales_consignment', 'direct_consignment']))
                <div class="card p-3 my-3 border bg-light">
                    <h6 class="fw-bold text-dark mb-2"><i class="las la-exchange-alt me-1 text-primary"></i> Move Order & Proof of Payment Options</h6>
                    <p class="small text-muted mb-3">Uploading Proof of Payment or moving the order will make it visible on the Acknowledgement Receipt or Consignment Receipt page.</p>

                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <form action="{{ route('production.logistic.upload-dr-pop', $order->id) }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                                @csrf
                                <input type="file" name="proof_of_payment" class="form-control form-control-sm" required accept=".pdf,.png,.jpg,.jpeg">
                                <button type="submit" class="btn btn-outline-info btn-sm text-nowrap"><i class="las la-upload me-1"></i> Upload POP</button>
                            </form>
                            @if($order->proof_of_payment)
                                <small class="text-success fw-bold mt-1 d-block"><i class="las la-check-circle me-1"></i> Proof of payment attached (Visible in Acknowledgement Receipt)</small>
                            @endif
                        </div>
                        <div class="col-md-6 d-flex gap-2 justify-content-md-end flex-wrap">
                            @php
                                $isMovedToAR = $order->status === 'ar_created' || $order->ar_prepared_at !== null;
                                $isMovedToCR = $order->status === 'cr_created' || $order->cr_prepared_at !== null;
                            @endphp

                            @if($order->type === 'area_consignment')
                                <form action="{{ route('production.logistic.move-to-ar', $order->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-info text-white btn-sm" {{ $isMovedToAR ? 'disabled' : '' }}>
                                        <i class="las la-file-signature me-1"></i> {{ $isMovedToAR ? 'Moved to AR' : 'Move to AR' }}
                                    </button>
                                </form>
                            @elseif($order->type === 'area_sales_consignment')
                                <form action="{{ route('production.logistic.move-to-cr', $order->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" {{ $isMovedToCR ? 'disabled' : '' }}>
                                        <i class="las la-file-contract me-1"></i> {{ $isMovedToCR ? 'Moved to CR' : 'Move to CR' }}
                                    </button>
                                </form>
                            @endif

                             <form action="{{ route('production.logistic.move-to-si', $order->id) }}" method="POST" style="display:inline-block;">
                                 @csrf
                                 <button type="submit" class="btn btn-danger btn-sm text-white fw-bold">
                                     <i class="las la-file-invoice me-1"></i> Move to SI
                                 </button>
                             </form>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-light" onclick="window.print()">
                        <i class="las la-print"></i> Print
                    </button>
                    @if(!$order)
                        <button type="button" class="btn btn-primary">
                            <i class="las la-save"></i> Save Receipt
                        </button>
                        <button type="button" class="btn btn-success">
                            <i class="las la-paper-plane"></i> Submit
                        </button>
                    @else
                        @php
                            $drListRoute = ($sidebar ?? (request()->is('admin-finance*') ? 'admin-finance' : 'production')) === 'admin-finance' ? 'admin-finance.accounting.delivery-receipt-list' : 'production.logistic.delivery-receipt-list';
                            $user = auth()->user();
                            $canApproveDR = $user && ($user->isSuperAdmin() || 
                                str_contains($user->position ?? '', 'Manager') || 
                                str_contains($user->position ?? '', 'Supervisor') || 
                                str_contains($user->position ?? '', 'Head') || 
                                str_contains($user->position ?? '', 'Senior Logistics Staff') ||
                                str_contains($user->position ?? '', 'Accounting') ||
                                str_contains($user->position ?? '', 'Finance') ||
                                str_contains($user->division ?? '', 'Admin') ||
                                str_contains($user->division ?? '', 'Finance') ||
                                str_contains($user->department ?? '', 'Accounting') ||
                                $user->hasPermission('admin_finance.accounting'));
                        @endphp
                        <a href="{{ route($drListRoute) }}" class="btn btn-secondary">
                            <i class="las la-arrow-left"></i> Back to List
                        </a>
                        @if(!empty($order->dr_approved_at) || !empty($order->dr_approved_by))
                            <div class="d-inline-block ms-2">
                                <span class="badge bg-success text-white me-2 fs-13 py-2 px-3">
                                    <i class="las la-check-circle me-1"></i> DR Signed & Approved
                                </span>
                            </div>
                        @elseif(in_array($order->status, ['pending_dr_prep', 'pending_dr_approval']) && $canApproveDR)
                            <div class="d-inline-block ms-2">
                                @if($order->status === 'pending_dr_approval')
                                    <span class="badge bg-warning text-dark me-2 fs-13 py-2 px-3"><i class="las la-clock me-1"></i> Pending DR Approval</span>
                                @endif
                                <form action="{{ route('production.logistic.approve-dr', $order->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-success shadow-sm">
                                        <i class="las la-check-double me-1"></i> Approve & Sign DR
                                    </button>
                                </form>
                            </div>
                        @elseif(in_array($order->status, ['pending_dr_prep']))
                            <form action="{{ route('production.logistic.complete-dr', $order->id) }}" method="POST" style="display:inline-block; margin-left: 0.5rem;">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="las la-check-circle me-1"></i> Submit for DR Approval
                                </button>
                            </form>
                        @endif
                        @if(in_array($order->type, ['area_consignment', 'area_sales_consignment', 'direct_consignment']))
                            <form action="{{ route('production.logistic.request-reconsignment', $order->id) }}" method="POST" style="display:inline-block; margin-left: 0.5rem;">
                                @csrf
                                <button type="submit" class="btn btn-warning" {{ !in_array($order->status, ['pending_dr_prep', 'ready_for_packing', 'ready_for_delivery', 'ar_created', 'cr_created', 'si_created', 'pending_si_approval', 'pending_si_prep', 'completed']) ? 'disabled' : '' }}>
                                    <i class="las la-retweet"></i> Reconsignment
                                </button>
                            </form>
                            <form action="{{ route('production.logistic.return-consignment', $order->id) }}" method="POST" style="display:inline-block; margin-left: 0.5rem;">
                                @csrf
                                <button type="submit" class="btn btn-danger" {{ !in_array($order->status, ['pending_dr_prep', 'ready_for_packing', 'ready_for_delivery', 'ar_created', 'cr_created', 'si_created', 'pending_si_approval', 'pending_si_prep', 'completed']) ? 'disabled' : '' }}>
                                    <i class="las la-undo-alt"></i> Return
                                </button>
                            </form>
                        @endif
                    @endif
                </div>

    <!-- Modal System -->
    <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="alertModalHeader">
                    <h5 class="modal-title" id="alertModalLabel">Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="alertModalBody">
                    <p id="alertModalMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmModalMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmModalOkBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <style>
        .receipt-form {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .form-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }

        .form-header .company-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-header .company-logo {
            width: 60px;
            height: 60px;
            background: #ff0000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2rem;
            font-weight: bold;
            flex-shrink: 0;
            position: absolute;
            left: -9999px;
            top: -9999px;
            opacity: 0;
            visibility: hidden;
        }

        .form-header .company-details {
            flex: 1;
        }

        .form-header .company-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
        }

        .form-header .company-address {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.1rem;
        }

        .form-header .company-contact {
            font-size: 0.9rem;
            color: #666;
        }

        .form-header .document-title {
            text-align: center;
            font-size: 1.75rem;
            font-weight: 700;
            color: #333;
            margin-top: 1rem;
            letter-spacing: 1px;
        }

        .form-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 6px;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .form-info-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1;
            min-width: 200px;
        }

        .form-info-item label {
            font-weight: 600;
            color: #333;
            margin: 0;
            min-width: 80px;
        }

        .form-info-item input,
        .form-info-item .form-control {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
            flex: 1;
        }

        .cancellation-date-label,
        .cancellation-date-input {
            color: #dc3545 !important;
            font-weight: 700 !important;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
            font-size: 0.9rem;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }

        .receipt-table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-bottom: 1.5rem !important;
            display: table !important;
        }

        .receipt-table thead {
            background: #ff0000 !important;
            color: #ffffff !important;
            display: table-header-group !important;
        }

        .receipt-table tbody {
            display: table-row-group !important;
        }

        .receipt-table tr {
            display: table-row !important;
        }

        .receipt-table th {
            padding: 0.75rem 1rem !important;
            text-align: left;
            font-weight: 700 !important;
            font-size: 0.9rem !important;
            border: 1px solid #dee2e6 !important;
            background-color: #ff0000 !important;
            color: #ffffff !important;
            display: table-cell !important;
        }

        .receipt-table td {
            padding: 0.75rem 1rem !important;
            border: 1px solid #dee2e6 !important;
            vertical-align: middle !important;
            color: #212529 !important;
            background-color: #ffffff !important;
            display: table-cell !important;
            font-size: 0.9rem !important;
        }

        .receipt-table input[type="text"],
        .receipt-table input[type="number"] {
            width: 100%;
            border: none;
            padding: 0.5rem;
            background: transparent;
        }

        .receipt-table input[type="number"] {
            text-align: right;
        }

        .receipt-table input:focus {
            outline: 2px solid #ff0000;
            outline-offset: -2px;
            background: #fff;
        }

        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e0e0e0;
        }

        .signature-box {
            display: flex;
            flex-direction: column;
        }

        .signature-box label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            display: block;
        }

        .signature-box input {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
            margin-bottom: 2rem;
        }

        .signature-input-wrapper {
            display: block;
            position: relative;
            min-height: 40px;
        }

        .signature-value-display {
            display: none;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e0e0e0;
        }

        .btn-add-row {
            background: #ff0000;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-add-row:hover {
            background: #ff6666;
        }

        .btn-remove-row {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
        }

        .btn-remove-row:hover {
            background: #c82333;
        }

        .notes-section {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }

        .notes-section label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            display: block;
        }

        .notes-section textarea {
            min-height: 80px;
        }

        .print-only-remarks,
        .print-only-article {
            display: none;
        }

        @page {
            size: letter portrait; /* Short bond paper (8.5in x 11in) */
            margin: 0.25in 0.35in;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body, html {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 10.5px !important;
                line-height: 1.2 !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Hide UI elements, navigation, buttons, and helper bars */
            .sidebar,
            .header,
            .nav-header,
            .form-actions,
            .btn-add-row,
            .btn-remove-row,
            #saveSelectionsBtn,
            #linkToSIBtn,
            .modal,
            button,
            .btn,
            div[style*="background: #e7f3ff"],
            .card.p-3.my-3.border.bg-light,
            .d-flex.justify-content-between.align-items-center.mb-3,
            .alert,
            .toast,
            no-print {
                display: none !important;
            }

            /* Make textareas and inputs look clean and transparent without borders or resize handles */
            .form-info-item input,
            .form-group input,
            .form-group textarea,
            textarea,
            input {
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                background: transparent !important;
                outline: none !important;
                box-shadow: none !important;
                color: #000000 !important;
                font-size: 10.5px !important;
                resize: none !important;
                min-height: auto !important;
                height: auto !important;
                overflow: hidden !important;
                -webkit-appearance: none !important;
                appearance: none !important;
            }

            /* Show the table cleanly */
            .receipt-table {
                width: 100% !important;
                page-break-inside: auto;
                display: table !important;
                margin-bottom: 0.5rem !important;
                font-size: 10.5px !important;
                border-collapse: collapse !important;
            }

            .receipt-table thead {
                display: table-header-group !important;
                page-break-inside: avoid;
            }

            .receipt-table tbody {
                display: table-row-group !important;
            }

            .receipt-table tfoot {
                display: table-row-group !important;
                page-break-inside: avoid !important;
            }

            .receipt-table tr {
                display: table-row !important;
                page-break-inside: avoid !important;
            }

            .receipt-table th,
            .receipt-table td {
                display: table-cell !important;
                border: 1px solid #000000 !important;
                padding: 3px 5px !important;
                font-size: 10.5px !important;
            }

            .receipt-table th {
                background: #e9ecef !important;
                color: #000000 !important;
                font-weight: bold !important;
                text-transform: uppercase !important;
            }

            .receipt-table td {
                background: #ffffff !important;
                color: #000000 !important;
                font-weight: 700 !important;
            }

            .receipt-table span,
            .receipt-table div {
                color: #000000 !important;
            }

            .badge,
            span.badge {
                background: transparent !important;
                background-color: transparent !important;
                color: #000000 !important;
                border: 1px solid #000000 !important;
                font-weight: bold !important;
                font-size: 10px !important;
                padding: 1px 4px !important;
                box-shadow: none !important;
            }

            /* Show table inputs as text */
            .receipt-table input[type="number"],
            .receipt-table input[type="text"] {
                border: none !important;
                padding: 0 !important;
                background: transparent !important;
                outline: none !important;
                color: #000000 !important;
                font-family: inherit;
                width: auto;
                font-size: 10.5px !important;
                text-align: inherit;
            }

            .pick-qty-col,
            th.pick-qty-col,
            td.pick-qty-col {
                display: none !important;
            }

            /* Clean up receipt form container */
            .receipt-form,
            .card {
                box-shadow: none !important;
                padding: 0 !important;
                max-width: 100% !important;
                margin: 0 !important;
                border: none !important;
                background: transparent !important;
            }

            .form-header {
                margin-bottom: 0.5rem !important;
                padding-bottom: 0.35rem !important;
                border-bottom: 2px solid #000000 !important;
                text-align: center !important;
            }

            .form-header .company-info {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 0.75rem !important;
                margin-bottom: 0.25rem !important;
                text-align: left !important;
            }

            .form-header .company-logo-img {
                display: block !important;
                height: 42px !important;
                width: auto !important;
                object-fit: contain !important;
            }

            .form-header .company-details {
                flex: none !important;
            }

            .form-header .company-name {
                font-size: 1rem !important;
                font-weight: bold !important;
                margin-bottom: 2px !important;
                color: #000000 !important;
            }

            .form-header .company-address,
            .form-header .company-contact {
                font-size: 0.75rem !important;
                margin: 0 !important;
                color: #000000 !important;
            }

            .form-header div.text-muted,
            .form-header .extra-small {
                color: #000000 !important;
                font-weight: 700 !important;
            }

            .document-title {
                font-size: 1.15rem !important;
                font-weight: bold !important;
                margin-top: 0.25rem !important;
                margin-bottom: 0.15rem !important;
                letter-spacing: 1px !important;
                color: #000000 !important;
            }

            .form-info-row {
                background: transparent !important;
                border: none !important;
                padding: 0.15rem 0 !important;
                margin-bottom: 0.35rem !important;
                display: flex !important;
                flex-wrap: wrap !important;
                gap: 0.5rem 1rem !important;
                justify-content: space-between !important;
            }

            .form-info-item {
                flex: 1 1 auto !important;
                min-width: 130px !important;
            }

            .cancellation-date-label,
            .cancellation-date-input,
            .form-info-item.cancellation-date-item input,
            .form-info-item.cancellation-date-item label {
                color: #000000 !important;
                -webkit-text-fill-color: #000000 !important;
                font-weight: 900 !important;
                font-size: 11px !important;
                opacity: 1 !important;
            }

            .form-info-item label,
            .form-group label {
                font-size: 0.75rem !important;
                font-weight: bold !important;
                margin-bottom: 0px !important;
                display: block !important;
                color: #000000 !important;
            }

            .form-group {
                background: transparent !important;
                border: none !important;
                padding: 0.15rem 0 !important;
                margin-bottom: 0.25rem !important;
            }

            .screen-only-remarks {
                display: none !important;
            }

            .print-only-remarks {
                display: block !important;
                border: 1px solid #ccc !important;
                padding: 0.35rem 0.5rem !important;
                font-size: 0.75rem !important;
                font-weight: 600 !important;
                background: #ffffff !important;
                color: #000000 !important;
                white-space: pre-wrap !important;
                word-break: break-word !important;
                min-height: 30px !important;
            }

            .print-only-article {
                display: block !important;
                font-size: 11px !important;
                font-weight: bold !important;
                color: #000000 !important;
                margin-top: 2px !important;
            }

            /* Signature section */
            .signature-section {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                page-break-before: auto !important;
                margin-top: 1rem !important;
                border-top: none !important;
                padding-top: 0.5rem !important;
                display: table !important;
                table-layout: fixed !important;
                width: 100% !important;
                clear: both !important;
            }

            .signature-box {
                display: table-cell !important;
                width: 33.33% !important;
                vertical-align: top !important;
                text-align: center !important;
                padding: 0 8px !important;
            }

            .signature-box label {
                font-weight: bold !important;
                display: block !important;
                font-size: 0.75rem !important;
                margin-bottom: 0.15rem !important;
                color: #000000 !important;
                text-align: left !important;
            }

            .signature-box input {
                display: none !important;
            }

            .signature-input-wrapper {
                display: block !important;
                min-height: 22px !important;
                margin-bottom: 0.25rem !important;
            }

            .signature-value-display {
                display: block !important;
                color: #000000 !important;
                font-weight: bold !important;
                font-size: 0.85rem !important;
                min-height: 1.2rem !important;
                text-align: center !important;
            }

            .signature-line-box,
            .signature-box div[style*="border-top"] {
                border-top: 1px solid #000000 !important;
                text-align: center !important;
                padding-top: 0.15rem !important;
                font-size: 0.75rem !important;
                font-weight: bold !important;
                color: #000000 !important;
            }

            body,
            html,
            #main-wrapper,
            .content-body,
            .container-fluid,
            .receipt-form,
            .table-responsive,
            .card,
            .row,
            .col-xl-12 {
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
                display: block !important;
                float: none !important;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function syncSignatureDisplays() {
                const prep = document.getElementById('preparedBy');
                const prepDisp = document.getElementById('preparedByDisplay');
                if (prep && prepDisp) prepDisp.textContent = prep.value || ' ';

                const app = document.getElementById('approvedBy');
                const appDisp = document.getElementById('approvedByDisplay');
                if (app && appDisp) appDisp.textContent = app.value || ' ';

                const rec = document.getElementById('receivedBy');
                const recDisp = document.getElementById('receivedByDisplay');
                if (rec && recDisp) recDisp.textContent = rec.value || ' ';
            }

            syncSignatureDisplays();
            window.addEventListener('beforeprint', syncSignatureDisplays);

            // Live sync signature inputs to print display spans
            const preparedInput = document.getElementById('preparedBy');
            if (preparedInput) {
                preparedInput.addEventListener('input', syncSignatureDisplays);
            }

            const approvedInput = document.getElementById('approvedBy');
            if (approvedInput) {
                approvedInput.addEventListener('input', syncSignatureDisplays);
            }

            const receivedInput = document.getElementById('receivedBy');
            if (receivedInput) {
                receivedInput.addEventListener('input', syncSignatureDisplays);
            }

            document.querySelectorAll('.pick-qty-input').forEach(input => {
                input.addEventListener('input', function() {
                    const row = this.closest('tr');
                    const price = parseFloat(this.dataset.price) || 0;
                    const sentQty = parseInt(this.dataset.qty) || 0;
                    let val = parseInt(this.value);
                    if (isNaN(val)) val = 0;

                    if (val > sentQty) {
                        val = sentQty;
                        this.value = sentQty;
                    } else if (val < 0) {
                        val = 0;
                        this.value = 0;
                    }

                    this.setAttribute('value', this.value);
                    const printSpan = row.querySelector('.print-pick-qty');
                    if (printSpan) {
                        printSpan.textContent = this.value;
                    }

                    const effectiveQty = val;
                    const rowAmount = effectiveQty * price;

                    const amountTd = row.querySelector('.row-amount-td');
                    if (amountTd) {
                        amountTd.textContent = '₱' + rowAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }

                    let total = 0;
                    document.querySelectorAll('.pick-qty-input').forEach(i => {
                        const p = parseFloat(i.dataset.price) || 0;
                        let v = parseInt(i.value);
                        if (isNaN(v)) v = 0;
                        total += v * p;
                    });

                    const formattedTotal = '₱' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    const totalElem = document.getElementById('drTotalAmountDisplay');
                    if (totalElem) {
                        totalElem.textContent = formattedTotal;
                    }
                    const totalTableElem = document.getElementById('drTotalAmountDisplayTable');
                    if (totalTableElem) {
                        totalTableElem.textContent = formattedTotal;
                    }
                });
            });
        });

        function saveDrRemarks() {
            const remarks = document.getElementById('drRemarksTextarea').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const printRemarksContent = document.getElementById('drPrintRemarksContent');
            if (printRemarksContent) {
                printRemarksContent.innerText = remarks || 'None';
            }

            fetch('/production/logistic/delivery-receipt/{{ $order->id ?? 0 }}/update-pick-qty', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken 
                },
                body: JSON.stringify({ remarks: remarks })
            })
            .then(res => res.json())
            .then(data => {
                showNotification ? showNotification('Remarks saved successfully!', 'success') : alert('Remarks saved successfully!');
            })
            .catch(err => {
                alert('Remarks saved!');
            });
        }
    </script>
    @endpush
</x-app-layout>
