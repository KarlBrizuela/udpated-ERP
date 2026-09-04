<x-app-layout :title="$title" :sidebar="$sidebar">
    @push('styles')
    <style>
        .order-form { background: #fff; border-radius: 8px; padding: 2rem; box-shadow: 0 0 20px rgba(0, 0, 0, 0.05); max-width: 1000px; margin: 0 auto; }
        .form-header { margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #e0e0e0; position: relative; }
        .form-header .company-info { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
        .form-header .company-logo { width: 60px; height: 60px; background: #ff0000; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; font-weight: bold; flex-shrink: 0; }
        .form-header .company-details { flex: 1; }
        .form-header .company-name { font-size: 1.25rem; font-weight: 700; color: #333; margin-bottom: 0.25rem; text-transform: uppercase; }
        .form-header .document-title { text-align: center; font-size: 1.75rem; font-weight: 700; color: #333; margin-top: 1rem; letter-spacing: 1px; }
        .doc-type-badge { position: absolute; top: 0; right: 0; padding: 5px 15px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; font-size: 0.8rem; font-weight: 700; color: #666; }
        
        .customer-section { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem; }
        .details-box { background: #f8f9fa; padding: 1.5rem; border-radius: 6px; }
        .details-box h5 { border-bottom: 1px solid #dee2e6; padding-bottom: 0.5rem; margin-bottom: 1rem; font-weight: 700; color: #333; }
        
        .order-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; border: 1px solid #ddd; }
        .order-table thead { background: #ff0000; color: #fff; }
        .order-table th, .order-table td { padding: 0.75rem; border: 1px solid #ddd; }
        .order-table tfoot { background: #f8f9fa; font-weight: 600; }
        
        .signature-section { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 2rem; margin-top: 2rem; page-break-inside: avoid; }
        .signature-box { text-align: center; }
        .signature-line { border-top: 1px solid #333; margin-top: 2rem; padding-top: 0.5rem; font-weight: 600; color: #333; }
        
        @page {
            size: letter portrait; /* Short bond paper (8.5in x 11in) */
            margin: 0.35in 0.4in;
        }
        
        @media print { 
            * { -webkit-print-color-adjust: exact !important; color-adjust: exact !important; print-color-adjust: exact !important; }
            body, html { background: #fff !important; color: #000 !important; font-size: 11px !important; line-height: 1.2 !important; margin: 0 !important; padding: 0 !important; }
            .sidebar, .header, .form-actions, .btn, .nav-header, .doc-type-badge { display: none !important; } 
            .content-body { margin-left: 0 !important; padding: 0 !important; } 
            .order-form { box-shadow: none !important; padding: 0 !important; max-width: 100% !important; margin: 0 !important; border: none !important; }
            .form-header { margin-bottom: 0.75rem !important; padding-bottom: 0.5rem !important; border-bottom: 2px solid #000 !important; text-align: center !important; }
            .form-header .company-logo { display: none !important; }
            .form-header .company-name { font-size: 1rem !important; font-weight: 700 !important; margin-bottom: 2px !important; }
            .form-header .company-address, .form-header .company-contact { font-size: 0.75rem !important; margin: 0 !important; }
            .document-title { font-size: 1.25rem !important; font-weight: 700 !important; margin-top: 0.35rem !important; margin-bottom: 0.2rem !important; }
            .customer-section { display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 0.75rem !important; margin-bottom: 0.75rem !important; }
            .details-box { background: transparent !important; padding: 0.5rem !important; border: 1px solid #ddd !important; border-radius: 4px !important; }
            .details-box h5 { font-size: 0.85rem !important; margin-bottom: 0.35rem !important; padding-bottom: 0.2rem !important; border-bottom: 1px solid #000 !important; }
            .details-box td { font-size: 0.75rem !important; padding: 2px 4px !important; }
            .order-table { width: 100% !important; border-collapse: collapse !important; margin-bottom: 0.75rem !important; font-size: 11px !important; }
            .order-table th, .order-table td { padding: 4px 6px !important; border: 1px solid #000 !important; font-size: 11px !important; }
            .order-table thead { background: #e9ecef !important; color: #000 !important; font-weight: 700 !important; }
            .signature-section { margin-top: 0.75rem !important; gap: 1.5rem !important; display: grid !important; grid-template-columns: 1fr 1fr 1fr !important; page-break-inside: avoid !important; break-inside: avoid !important; page-break-before: auto !important; }
            .signature-line { border-top: 1px solid #000 !important; margin-top: 1.25rem !important; padding-top: 0.25rem !important; font-size: 0.75rem !important; }
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="mb-4 form-actions">
                @if(request('back') === 'consignment')
                    <a href="{{ route('production.logistic.area-consignment') }}" class="btn btn-dark btn-sm">
                        <i class="las la-arrow-left me-2"></i>Back to Consignment Receipt
                    </a>
                @else
                    <a href="{{ route('production.logistic.driver-dashboard') }}" class="btn btn-dark btn-sm">
                        <i class="las la-arrow-left me-2"></i>Back to Dashboard
                    </a>
                @endif
                <button type="button" class="btn btn-primary btn-sm ms-2" onclick="window.print()">
                    <i class="las la-print me-2"></i>Print Form
                </button>
            </div>

            <div class="card order-form">
                <div class="doc-type-badge">{{ $order->so_number }}</div>
                
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
                    <div class="document-title">{{ $documentType }}</div>
                    @if($documentType === 'SALES INVOICE' || $documentType === 'DELIVERY RECEIPT')
                        <div class="text-center text-muted small fw-bold mb-1">NON-VAT REGISTERED</div>
                        <div class="text-center extra-small text-muted italic">"This document is not valid for claim of input taxes."</div>
                    @endif
                </div>

                <!-- Info Grid -->
                <div class="customer-section">
                    <div class="details-box">
                        <h5>Customer Information</h5>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="fw-bold text-dark" style="width: 130px;">Company:</td>
                                <td class="text-black fw-bold">{{ $order->customer?->customer_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Customer Name:</td>
                                <td class="text-black fw-bold">{{ $order->customer_representative ?: ($order->customer?->customer_name ?? 'N/A') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Address:</td>
                                <td class="text-black">{{ ($addr = $order->shipping_address ?? $order->customer?->shipping_address ?? $order->customer?->billing_address) && $addr !== 'N/A' ? $addr : '' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Contact:</td>
                                <td class="text-black">{{ ($phone = $order->customer?->phone) && $phone !== 'N/A' ? $phone : '' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="details-box">
                        <h5>Document Details</h5>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="fw-bold text-dark" style="width: 120px;">Reference No:</td>
                                <td class="text-black fw-bold">{{ $order->so_number }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Date:</td>
                                <td class="text-black">{{ now()->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Order Type:</td>
                                <td class="text-black text-uppercase">{{ str_replace('_', ' ', $order->type) }}</td>
                            </tr>
                            @if($order->cancellation_date)
                            <tr>
                                <td class="fw-bold text-danger">Cancellation Date:</td>
                                <td class="text-danger fw-bold">{{ \Carbon\Carbon::parse($order->cancellation_date)->format('F d, Y') }}</td>
                            </tr>
                            @endif
                            @if($order->plate_number)
                            <tr>
                                <td class="fw-bold text-dark">Vehicle:</td>
                                <td class="text-black">{{ $order->plate_number }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Items Table -->
                <table class="order-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;" class="text-center">QTY</th>
                            <th style="width: 80px;" class="text-center">UNIT</th>
                            <th>DESCRIPTION</th>
                            <th style="width: 120px;" class="text-center">UNIT PRICE</th>
                            <th style="width: 100px;" class="text-center">DISCOUNT</th>
                            <th style="width: 130px;" class="text-center">AMOUNT</th>
                        </tr>
                    </thead>
                    @php
                        $grossSubtotal = 0;
                        $totalItemDiscounts = 0;
                    @endphp
                    <tbody>
                        @foreach($order->items->filter(fn($i) => (float)($i->quantity ?? 0) > 0) as $item)
                        @php
                            $qty = (float)($item->quantity ?? 0);
                            $unitPrice = (float)($item->price ?? $item->unit_price ?? 0);
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
                            $rowAmount = max(0, $itemSubtotal - $itemDiscountAmt);
                        @endphp
                        <tr>
                            <td class="text-center text-black fw-bold">{{ $qty }}</td>
                            <td class="text-center text-uppercase text-muted">{{ $item->book->unit ?? 'pcs' }}</td>
                            <td>
                                @php
                                    $vItemName = $item->bookIndex ? $item->bookIndex->display_name : ($item->book ? $item->book->name : ($item->bundle ? $item->bundle->name : ($item->product ? $item->product->name : ($item->item_name ?? ($item->description ?? 'Unknown Item')))));
                                    $vType = $item->item_type ?? ($item->bookIndex || !empty($item->book_index_id) ? 'Index' : (!empty($item->bundle_id) || !empty($item->bundle) ? 'Bundle' : 'Book'));
                                @endphp
                                <div class="d-flex align-items-center flex-wrap gap-1">
                                    <span class="text-black fw-bold">{{ $vItemName }}</span>
                                    @if($vType === 'Bundle')
                                        <span class="badge ms-1" style="background:#6f42c1; color:#fff; font-size: 10px; padding: 2px 6px;">Bundle</span>
                                    @elseif($vType === 'Index')
                                        <span class="badge bg-info text-dark ms-1" style="font-size: 10px; padding: 2px 6px;">Index</span>
                                    @else
                                        <span class="badge bg-primary ms-1" style="font-size: 10px; padding: 2px 6px;">Book</span>
                                    @endif
                                 </div>
                                <small class="text-muted d-block">{{ $item->bookIndex->barcode ?? ($item->book->sku ?? ($item->bundle->sku ?? 'N/A')) }}</small>
                            </td>
                            @php
                                $drSym = ($order->currency === 'USD' ? '$' : '₱');
                            @endphp
                            <td class="text-end">{{ $drSym }}{{ number_format($unitPrice, 2) }}</td>
                            <td class="text-center">
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
                            <td class="text-end fw-bold">{{ $drSym }}{{ number_format($rowAmount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    @php
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
                            <td colspan="5" class="text-end text-uppercase"><strong>Gross Subtotal:</strong></td>
                            <td class="text-end fw-bold">{{ $drSym }}{{ number_format($grossSubtotal, 2) }}</td>
                        </tr>
                        @if($totalItemDiscounts > 0)
                        <tr>
                            <td colspan="5" class="text-end text-uppercase"><strong>Items Discount Subtotal:</strong></td>
                            <td class="text-end fw-bold text-danger">- {{ $drSym }}{{ number_format($totalItemDiscounts, 2) }}</td>
                        </tr>
                        @endif
                        @if($orderDiscountAmt > 0)
                        <tr>
                            <td colspan="5" class="text-end text-uppercase"><strong>Order Discount @if(($order->discount_percentage ?? 0) > 0)({{ (float)$order->discount_percentage }}%)@endif:</strong></td>
                            <td class="text-end fw-bold text-danger">- {{ $drSym }}{{ number_format($orderDiscountAmt, 2) }}</td>
                        </tr>
                        @endif
                        @if($allDiscountsCombined > 0)
                        <tr style="background-color: #fff3cd;">
                            <td colspan="5" class="text-end text-uppercase fw-bold text-dark">TOTAL DISCOUNT:</td>
                            <td class="text-end fw-bold text-danger" style="font-size: 15px;">- {{ $drSym }}{{ number_format($allDiscountsCombined, 2) }}</td>
                        </tr>
                        @endif
                        @if($freightChargesAmt > 0)
                        <tr>
                            <td colspan="5" class="text-end text-uppercase"><strong>Freight Charges:</strong></td>
                            <td class="text-end fw-bold">{{ $drSym }}{{ number_format($freightChargesAmt, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="table-light">
                            <td colspan="5" class="text-end text-uppercase fs-6"><strong>GRAND TOTAL:</strong></td>
                            <td class="text-end fw-bold fs-5 text-primary">{{ $drSym }}{{ number_format($finalTotalAmt > 0 ? $finalTotalAmt : ($order->total_amount ?? 0), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Remarks -->
                @if($order->remarks)
                <div class="mb-4 p-3 bg-light rounded italic">
                    <strong class="text-dark">Remarks:</strong> {{ $order->remarks }}
                </div>
                @endif

                <!-- Signatures -->
                @php
                    $preparedName = $order->drPreparedBy->name ?? ($order->preparedBy->name ?? 'System');
                    $approvedName = $order->drPreparedBy->name ?? ($order->signedBy->name ?? ($order->acctApprovedBy->name ?? ($order->mktApprovedBy->name ?? 'Authorized Signatory')));
                    $receivedName = $order->customer_representative ?: ($order->customer->customer_name ?? 'Customer');
                @endphp
                <div class="signature-section">
                    <div class="signature-box">
                        <div class="signature-line">PREPARED BY</div>
                        <div class="small text-muted fw-bold">{{ $preparedName }}</div>
                    </div>
                    <div class="signature-box">
                        <div class="signature-line">APPROVED BY</div>
                        <div class="small text-muted fw-bold">{{ $approvedName }}</div>
                    </div>
                    <div class="signature-box">
                        <div class="signature-line">RECEIVED BY / CUSTOMER</div>
                        <div class="small text-muted fw-bold">{{ $receivedName }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
