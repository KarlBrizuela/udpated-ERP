<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Invoice - {{ $order->so_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    @php
        $hideActions = request('hide_actions', false) || request('iframe', false);
        $format = request('format', 'whole');
        $halfPart = request('half', null); // 1 = first half of items, 2 = second half
    @endphp
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background-color: {{ $hideActions ? '#fff' : '#f4f6f9' }};
            color: #000;
            padding: {{ $hideActions ? '0' : '15px' }};
        }

        .invoice-box {
            background: #fff;
            max-width: 8.5in;
            width: 100%;
            min-height: 9.2in;
            margin: 0 auto;
            padding: 0.25in 0.35in;
            border: {{ $hideActions ? 'none' : '1px solid #ccc' }};
            box-shadow: {{ $hideActions ? 'none' : '0 4px 15px rgba(0, 0, 0, 0.1)' }};
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Half-Page (1/2 Paper Size) Layout Rules */
        body.half-page-mode {
            padding: 10px 0 !important;
        }
        body.half-page-mode .invoice-box {
            max-width: 8.5in !important;
            min-height: 4.8in !important;
            height: auto !important;
            max-height: none !important;
            margin: 0 auto !important;
            padding: 0.15in 0.25in !important;
            box-sizing: border-box !important;
        }
        body.half-page-mode .header-section {
            padding-bottom: 2px !important;
            margin-bottom: 4px !important;
            border-bottom-width: 1.5px !important;
        }
        body.half-page-mode .header-logo {
            width: 40px !important;
            height: 40px !important;
        }
        body.half-page-mode .company-name {
            font-size: 9.5pt !important;
        }
        body.half-page-mode .company-subtitle,
        body.half-page-mode .company-address,
        body.half-page-mode .company-contact {
            font-size: 6.5pt !important;
            margin-top: 0px !important;
            line-height: 1.1 !important;
        }
        body.half-page-mode .doc-no {
            font-size: 8pt !important;
        }
        body.half-page-mode .doc-no span {
            font-size: 9pt !important;
        }
        body.half-page-mode .doc-title {
            font-size: 9.5pt !important;
        }
        body.half-page-mode .info-grid {
            margin-bottom: 4px !important;
            font-size: 7.5pt !important;
        }
        body.half-page-mode .info-grid td {
            padding: 1px 0 !important;
        }
        body.half-page-mode .items-table {
            margin-bottom: 4px !important;
            font-size: 7.5pt !important;
        }
        body.half-page-mode .items-table th {
            padding: 2px 4px !important;
            font-size: 7pt !important;
        }
        body.half-page-mode .items-table td {
            padding: 2px 4px !important;
            line-height: 1.1 !important;
        }
        body.half-page-mode .payment-sales-row {
            margin-bottom: 3px !important;
            margin-top: 4px !important;
            font-size: 7.5pt !important;
        }
        body.half-page-mode .cb-box {
            width: 12px !important;
            height: 12px !important;
            line-height: 10px !important;
            font-size: 9px !important;
        }
        body.half-page-mode .total-sales-box {
            font-size: 8pt !important;
            padding: 2px 6px !important;
        }
        body.half-page-mode .total-sales-box span {
            font-size: 9pt !important;
        }
        body.half-page-mode .conditions-bank-container {
            margin-bottom: 3px !important;
            font-size: 6pt !important;
            gap: 6px !important;
            padding: 3px !important;
        }
        body.half-page-mode .signatories-row {
            margin-top: 4px !important;
            margin-bottom: 2px !important;
            font-size: 7pt !important;
        }
        body.half-page-mode .sig-line {
            margin-top: 14px !important;
            border-bottom-width: 1px !important;
        }
        body.half-page-mode .footer-notice {
            font-size: 5.5pt !important;
            margin-top: 2px !important;
        }
        .totals-block {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        .freight-line { order: 1; }
        .subtotal-line { order: 2; }
        .service-fee-line { order: 3; }
        .discount-line { order: 4; }
        .withholding-tax-line { order: 5; }
        .total-sales-line { order: 6; }
        .total-amount-due-line { order: 7; }

        body.half-page-mode .freight-line { order: 1; }
        body.half-page-mode .subtotal-line { order: 2; }
        body.half-page-mode .service-fee-line { order: 3; }
        body.half-page-mode .discount-line { order: 4; }
        body.half-page-mode .total-sales-line { order: 5; }
        body.half-page-mode .withholding-tax-line,
        body.half-page-mode .total-amount-due-line {
            display: none !important;
        }
        body:not(.half-page-mode) .business-style-row {
            display: none !important;
        }
        body.half-page-mode .business-style-row {
            display: table-row !important;
        }
        body.half-page-mode.preprinted-mode .invoice-box {
            padding-top: 0.45in !important;
        }
        body.half-page-mode.preprinted-mode .sig-line {
            margin-top: 14px !important;
        }

        .header-section {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        .header-logo-details {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-logo {
            width: 65px;
            height: 65px;
            object-fit: contain;
            filter: grayscale(100%) contrast(150%);
        }

        .company-name {
            font-size: 12.5pt;
            font-weight: 900;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin: 0;
            line-height: 1.1;
        }

        .company-subtitle {
            font-size: 8.5pt;
            font-weight: bold;
            color: #000;
            margin-top: 2px;
        }

        .company-address, .company-contact {
            font-size: 8pt;
            color: #000;
            margin-top: 1px;
            line-height: 1.2;
        }

        .header-right {
            text-align: right;
        }

        .doc-no {
            font-size: 10.5pt;
            font-weight: 800;
            margin-bottom: 2px;
        }

        .doc-no span {
            color: #000;
            font-size: 11.5pt;
        }

        .doc-title {
            font-size: 14pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9.5pt;
        }

        .info-grid td {
            padding: 3px 4px;
            vertical-align: bottom;
        }

        .info-label {
            font-weight: bold;
            width: 70px;
            white-space: nowrap;
        }

        .info-value-line {
            border-bottom: 1.5px solid #000;
            padding-left: 5px;
            font-weight: 600;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9.5pt;
        }

        .items-table th {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 5px 8px;
            text-transform: uppercase;
            font-weight: 900;
            font-size: 8.5pt;
        }

        .items-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: top;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .payment-sales-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 10pt;
            font-weight: bold;
        }

        .checkbox-group {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .custom-cb {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .cb-box {
            width: 16px;
            height: 16px;
            border: 2px solid #000;
            display: inline-block;
            text-align: center;
            line-height: 12px;
            font-size: 11pt;
            font-weight: 900;
        }

        .total-sales-box {
            font-size: 10.5pt;
            font-weight: 900;
        }

        .total-sales-box span {
            border-bottom: none;
            padding: 0 12px;
            font-size: 12pt;
            color: #000;
        }

        .conditions-bank-container {
            display: flex;
            gap: 15px;
            font-size: 7.5pt;
            margin-bottom: 12px;
            line-height: 1.35;
        }

        .conditions-block {
            flex: 1.2;
        }

        .bank-block {
            flex: 1;
        }

        .signatories-row {
            display: flex;
            justify-content: space-between;
            margin-top: 12px;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }

        .sig-col {
            flex: 1;
            padding-right: 15px;
        }

        .sig-col:last-child {
            padding-right: 0;
        }

        .sig-line {
            border-bottom: 1.5px solid #000;
            margin-top: 18px;
            min-height: 16px;
            font-weight: bold;
            text-align: center;
        }

        .footer-notice {
            font-size: 7pt;
            color: #444;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        .actions-bar {
            max-width: 8.5in;
            margin: 0 auto 12px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        /* Pre-Printed Paper Form Overlay Absolute Positioning Styles */
        .preprinted-overlay {
            display: none;
            position: relative;
            width: 8.5in;
            min-height: 11in;
            height: 11in;
            margin: 0 auto;
            background: transparent;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
        }

        body.half-page-mode .preprinted-overlay {
            min-height: 5.5in;
            height: 5.5in;
        }

        body:not(.half-page-mode) .preprinted-overlay-half {
            display: none !important;
        }
        body.half-page-mode .preprinted-overlay-whole {
            display: none !important;
        }

        body.preprinted-mode .invoice-box {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            max-height: 0 !important;
            overflow: hidden !important;
            opacity: 0 !important;
        }

        body.preprinted-mode:not(.half-page-mode) .preprinted-overlay-whole {
            display: block !important;
            visibility: visible !important;
        }
        body.preprinted-mode.half-page-mode .preprinted-overlay-half {
            display: block !important;
            visibility: visible !important;
        }

        @media print {
            .no-print,
            .actions-bar,
            .btn,
            button {
                display: none !important;
                visibility: hidden !important;
            }

            body {
                background: #fff !important;
                background-color: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .invoice-box {
                border: none !important;
                box-shadow: none !important;
                margin: 0 auto !important;
                padding: 0.2in 0.3in !important;
            }

            body.preprinted-mode {
                background: #fff !important;
                padding: 0 !important;
            }
            body.preprinted-mode .actions-bar {
                display: none !important;
            }
            body.preprinted-mode .invoice-box {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                max-height: 0 !important;
                overflow: hidden !important;
                opacity: 0 !important;
            }
            body.preprinted-mode:not(.half-page-mode) .preprinted-overlay-whole {
                display: block !important;
                visibility: visible !important;
                border: none !important;
                box-shadow: none !important;
            }
            body.preprinted-mode.half-page-mode .preprinted-overlay-half {
                display: block !important;
                visibility: visible !important;
                border: none !important;
                box-shadow: none !important;
            }
            @page {
                size: Letter portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    @if(!$hideActions)
    <div class="actions-bar no-print">
        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
            <i class="las la-arrow-left me-1"></i> Back
        </a>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <label class="form-label fw-bold small text-dark mb-0 text-nowrap"><i class="las la-file-alt me-1"></i>Paper Size:</label>
                <select class="form-select form-select-sm fw-bold border-secondary" id="paperSizeSelect" onchange="changePaperSize(this.value)" style="width: 150px; cursor: pointer; height: 32px; font-size: 0.8rem;">
                    <option value="whole" {{ ($format ?? 'whole') === 'whole' ? 'selected' : '' }}>1 Whole (Full Page)</option>
                    <option value="half" {{ ($format ?? 'whole') === 'half' ? 'selected' : '' }}>1/2 (Half Page)</option>
                </select>
            </div>
            <div class="form-check form-switch mb-0 ms-2">
                <input class="form-check-input" type="checkbox" id="preprintedToggle" onchange="togglePreprintedMode(this)" {{ (request('preprinted') || request('data_only')) ? 'checked' : '' }}>
                <label class="form-check-label fw-bold small text-dark" for="preprintedToggle">Print Data Only (For Official BIR Paper)</label>
            </div>
            <button onclick="window.print()" class="btn btn-danger btn-sm px-4 shadow-sm" style="background:#ff0000; border: none;">
                <i class="las la-print me-1"></i> Print / Save PDF
            </button>
        </div>
    </div>
    @endif

    @php
        $activeInvoice = null;
        if (in_array($order->type, ['area_consignment', 'area_sales_consignment'])) {
            $activeInvoice = \App\Models\SalesInvoice::where('so_id', $order->id)->where('status', '!=', 'cancelled')->latest()->first();
        }

        if ($activeInvoice) {
            $allItems = $activeInvoice->items->filter(fn($i) => (float)($i->quantity ?? 0) > 0);
            $totalSalesAmount = (float) $activeInvoice->total_amount;
        } else {
            $allItems = $order->items ? $order->items->filter(fn($i) => (float)($i->quantity ?? 0) > 0) : collect();
            $totalSalesAmount = (float) $order->total_amount;
        }

        // Split items if half parameter is set
        if ($halfPart) {
            $itemsArray = $allItems->values();
            $totalCount = $itemsArray->count();
            $midpoint = (int) ceil($totalCount / 2);
            if ($halfPart == '1') {
                $itemsToPrint = $itemsArray->slice(0, $midpoint)->values();
            } else {
                $itemsToPrint = $itemsArray->slice($midpoint)->values();
            }
            $halfLabel = $halfPart == '1' ? 'Part 1 of 2' : 'Part 2 of 2';
        } else {
            $itemsToPrint = $allItems;
            $halfLabel = null;
        }

        $paymentMethodLower = strtolower($order->payment_method ?? '');
        $termsValLower = strtolower($order->terms ?? '');
        $transactionTypeLower = strtolower($order->transaction_type ?? '');

        $isCash = in_array($paymentMethodLower, ['cash', 'gcash', 'paymaya', 'card', 'bank', 'check']) 
                  || in_array($transactionTypeLower, ['cash', 'cod'])
                  || in_array($order->type, ['calculator_pos', 'ecom_direct', 'paid'])
                  || $termsValLower === 'cash' || $termsValLower === 'cod' || $termsValLower === 'c a s h';
        $custName = ($order->customer?->customer_name && $order->customer->customer_name !== 'N/A') ? $order->customer->customer_name : 'Cash Customer';
        
        $rawAddr = $order->billing_address ?: ($order->shipping_address ?: ($order->customer?->billing_address ?? ''));
        $custAddress = ($rawAddr === 'N/A') ? '' : $rawAddr;
        
        $rawTin = $order->customer?->tin ?? '';
        $custTin = ($rawTin === 'N/A') ? '' : $rawTin;
        
        $termsVal = $order->terms ?: ($order->payment_method ? strtoupper($order->payment_method) : 'CASH');
        $orderDate = $order->created_at ? $order->created_at->format('m/d/Y') : date('m/d/Y');
        $dueDate = ($order->due_date && $order->due_date !== 'N/A') ? \Carbon\Carbon::parse($order->due_date)->format('m/d/Y') : '';
        $wht = (float) ($order->withholding_tax_amount ?? 0);
        $siNoDisplay = $order->si_number ?: ($activeInvoice->si_number ?? $order->so_number);
        $soSym = ($order->currency === 'USD' ? '$' : '₱');

        $itemsSubtotal = 0;
        foreach ($itemsToPrint as $item) {
            $qty = (float) $item->quantity;
            $price = (float) ($item->unit_price ?? $item->price);
            $gross = $qty * $price;
            $discVal = (float)($item->discount_value ?? 0);
            $discType = $item->discount_type ?? 'percentage';
            $discAmt = (float)($item->discount_amount ?? 0);
            if ($discAmt <= 0 && $discVal > 0) {
                $discAmt = $discType === 'percentage' ? $gross * ($discVal / 100) : $discVal;
            }
            $discAmt = min($gross, max(0, $discAmt));
            $netSub = ($item->subtotal !== null && (float)$item->subtotal > 0 && (float)$item->subtotal < $gross)
                ? (float)$item->subtotal
                : max(0, $gross - $discAmt);
            $itemsSubtotal += $netSub;
        }

        $discount = (float) ($order->discount_amount ?? 0);
        if ($discount == 0 && (float) ($order->discount_percentage ?? 0) > 0) {
            $discount = $itemsSubtotal * ((float) $order->discount_percentage / 100);
        }

        $freight = (float) ($order->freight_charges ?? 0);
        $serviceFee = (float) ($order->service_fee ?? ($order->freight_option === 'freight_collect' ? 50 : 0));

        if ($discount > 0 || $freight > 0 || $serviceFee > 0) {
            $calculatedTotalSales = max(0, $itemsSubtotal - $discount + $freight + $serviceFee);
        } else {
            $calculatedTotalSales = $totalSalesAmount > 0 ? $totalSalesAmount : $itemsSubtotal;
        }

        $totalAmountDue = max(0, $calculatedTotalSales - $wht);
    @endphp

    <div class="invoice-box">
        <div>
            <!-- Header -->
            <div class="header-section">
                <div class="header-logo-details">
                    <img src="{{ asset('images/claeritian_logo.png') }}" alt="Logo" class="header-logo" onerror="this.src='https://via.placeholder.com/65?text=C'">
                    <div>
                        <h1 class="company-name">Claretian Communications Foundation, Inc.</h1>
                        <div class="company-subtitle">Non-Vat Reg. TIN: 000-395-713-00000</div>
                        <div class="company-address">8 Mayumi Street, U.P. Village, Diliman, 1101 Quezon City NCR, Second District Philippines</div>
                        <div class="company-contact">Tel: (02) 8921-3984 Fax: (02) 8921-6205</div>
                    </div>
                </div>
                <div class="header-right">
                    <div class="doc-no"><span class="doc-no-label">No. </span><span>{{ $siNoDisplay }}</span></div>
                    <div class="doc-title">Sales - Invoice</div>
                    @if(isset($halfLabel) && $halfLabel)
                        <div style="font-size: 8pt; color: #666; font-weight: bold;">{{ $halfLabel }}</div>
                    @endif
                </div>
            </div>

            <!-- Customer & Transaction Details -->
            <table class="info-grid">
                <tr>
                    <td class="info-label">Sold to:</td>
                    <td class="info-value-line" style="width: 55%;">{{ $custName }}</td>
                    <td class="info-label" style="padding-left: 15px;">Date:</td>
                    <td class="info-value-line">{{ $orderDate }}</td>
                </tr>
                <tr>
                    <td class="info-label">Address:</td>
                    <td class="info-value-line">{{ $custAddress }}</td>
                    <td class="info-label" style="padding-left: 15px;">Terms:</td>
                    <td class="info-value-line">{{ $termsVal }}</td>
                </tr>
                <tr>
                    <td class="info-label">TIN:</td>
                    <td class="info-value-line">{{ $custTin }}</td>
                    <td class="info-label" style="padding-left: 15px;">Due Date:</td>
                    <td class="info-value-line">{{ $dueDate }}</td>
                </tr>
                <tr class="business-style-row">
                    <td class="info-label">Business Style:</td>
                    <td class="info-value-line" colspan="3">{{ $order->customer?->business_style ?? '' }}</td>
                </tr>
            </table>

            <!-- Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 10%; text-align: center;">QTY</th>
                        <th style="width: 50%;">DESCRIPTION</th>
                        <th style="width: 12%; text-align: center;">AREA</th>
                        <th style="width: 14%; text-align: right;">UNIT PRICE</th>
                        <th style="width: 14%; text-align: right;">AMOUNT</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($itemsToPrint as $item)
                        @php
                            if ($item->bookIndex) {
                                $desc = $item->bookIndex->display_name;
                            } elseif ($item->bundle) {
                                $desc = '[BUNDLE] ' . $item->bundle->name;
                            } else {
                                $desc = $item->book?->name ?? ($item->product_name ?? 'Product Item');
                            }
                            $qty = (float) $item->quantity;
                            $price = (float) ($item->unit_price ?? $item->price);
                            $gross = $qty * $price;
                            $discVal = (float)($item->discount_value ?? 0);
                            $discType = $item->discount_type ?? 'percentage';
                            $discAmt = (float)($item->discount_amount ?? 0);
                            if ($discAmt <= 0 && $discVal > 0) {
                                $discAmt = $discType === 'percentage' ? $gross * ($discVal / 100) : $discVal;
                            }
                            $discAmt = min($gross, max(0, $discAmt));
                            $subtotal = ($item->subtotal !== null && (float)$item->subtotal > 0 && (float)$item->subtotal < $gross)
                                ? (float)$item->subtotal
                                : max(0, $gross - $discAmt);
                        @endphp
                        <tr>
                            <td style="text-align: center; font-weight: bold;">{{ $qty }}</td>
                            <td style="font-weight: 600;">{{ $desc }}</td>
                            <td style="text-align: center;">{{ ($item->area && $item->area !== 'N/A' && $item->area !== '-') ? $item->area : '' }}</td>
                            @php
                                $soSym = ($order->currency === 'USD' ? '$' : '₱');
                            @endphp
                            <td style="text-align: right;">{{ $soSym }}{{ number_format($price, 2) }}</td>
                            <td style="text-align: right; font-weight: bold;">{{ $soSym }}{{ number_format($subtotal, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #777;">No line items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            <!-- Payment Type Checkbox & Total Sales -->
            <div class="payment-sales-row">
                <div class="checkbox-group">
                    <div class="custom-cb">
                        <span class="cb-box">{{ $isCash ? '✓' : '' }}</span>
                        <span class="cb-text">CASH</span>
                    </div>
                    <div class="custom-cb">
                        <span class="cb-box">{{ !$isCash ? '✓' : '' }}</span>
                        <span class="cb-text">CHARGE</span>
                    </div>
                </div>
                <div class="totals-block text-end">
                    <div class="freight-line" style="font-size: 8.5pt; font-weight: bold; margin-bottom: 3px; min-height: 16px;">
                        @if($freight > 0)
                            <span class="total-label">FREIGHT: </span><span style="padding: 0 8px; min-width: 115px; display: inline-block;">{{ $soSym }}{{ number_format($freight, 2) }}</span>
                        @else
                            &nbsp;
                        @endif
                    </div>
                    <div class="subtotal-line" style="font-size: 8.5pt; font-weight: bold; margin-bottom: 3px; min-height: 16px;">
                        <span class="total-label">SUBTOTAL: </span><span style="padding: 0 8px; min-width: 115px; display: inline-block;">{{ $soSym }}{{ number_format($itemsSubtotal, 2) }}</span>
                    </div>
                    <div class="service-fee-line" style="font-size: 8.5pt; font-weight: bold; margin-bottom: 3px; min-height: 16px;">
                        @if($serviceFee > 0)
                            <span class="total-label">SERVICE FEE: </span><span style="padding: 0 8px; min-width: 115px; display: inline-block;">{{ $soSym }}{{ number_format($serviceFee, 2) }}</span>
                        @else
                            &nbsp;
                        @endif
                    </div>
                    <div class="discount-line" style="font-size: 8.5pt; font-weight: bold; margin-bottom: 3px; min-height: 16px;">
                        @if($discount > 0)
                            <span class="total-label">DISCOUNT: </span><span style="padding: 0 8px; min-width: 115px; display: inline-block;">-{{ $soSym }}{{ number_format($discount, 2) }}</span>
                        @else
                            &nbsp;
                        @endif
                    </div>
                    <div class="withholding-tax-line" style="font-size: 8.5pt; font-weight: bold; margin-bottom: 3px; min-height: 16px;">
                        <span class="total-label">LESS: WITHHOLDING TAX: </span><span style="padding: 0 8px; min-width: 115px; display: inline-block;">{{ $wht > 0 ? '-' . $soSym . number_format($wht, 2) : '' }}</span>
                    </div>
                    <div class="total-sales-line" style="font-size: 8.5pt; font-weight: bold; margin-bottom: 3px; min-height: 16px;">
                        <span class="total-label">TOTAL SALES: </span><span style="padding: 0 8px; min-width: 115px; display: inline-block;">{{ number_format($calculatedTotalSales, 2) }}</span>
                    </div>
                    <div class="total-amount-due-line" style="font-size: 8.5pt; font-weight: bold; margin-bottom: 3px; min-height: 16px;">
                        <span class="total-label">TOTAL AMOUNT DUE: </span><span style="padding: 0 8px; min-width: 115px; display: inline-block;">{{ $wht > 0 ? $soSym . number_format($totalAmountDue, 2) : '' }}</span>
                    </div>
                </div>
            </div>

            <!-- Conditions & Bank Accounts -->
            <div class="conditions-bank-container">
                <div class="conditions-block">
                    <strong>CONDITIONS:</strong> Parties submit themselves to the jurisdiction of the courts of Quezon City in any legal action arising from this transaction. Interest of 12% per annum is charged on all overdue accounts plus cost of collection and attorney's fees. Received merchandise in good order and condition.
                </div>
                <div class="bank-block">
                    <strong>Payments may be deposit thru the following bank accounts:</strong><br>
                    <span>RCBC - SA# 1-191-46138-6</span> &nbsp;&nbsp;&nbsp; <span>Metrobank SA# 186-3-18617805-0</span><br>
                    <span>BDO - SA# 3640009449</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>BPI SA# 1993-0734-11</span>
                </div>
            </div>

            <!-- Signatories -->
            <div class="signatories-row">
                <div class="sig-col">
                    <div class="sig-label">Prepared by:</div>
                    <div class="sig-line">{{ $order->preparedBy?->name ?? '' }}</div>
                </div>
                <div class="sig-col">
                    <div class="sig-label">Approved by:</div>
                    <div class="sig-line">{{ $order->mktApprovedBy?->name ?? ($order->prodApprovedBy?->name ?? '') }}</div>
                </div>
                <div class="sig-col">
                    <div class="sig-label">Received by:</div>
                    <div class="sig-line">{{ $custName }}</div>
                </div>
            </div>

            <!-- Footer Notice -->
            <div class="footer-notice">
                <div class="bir-details">
                    <div>200 Pads (50x4) 60751-70750 | BIR Authority to Print No. OCN033AU20250000000745 (Date of ATP: 01-28-2026)</div>
                    <div>Looseleaf Permit No. LLAR-039-1022-00083 | Date Issued: October 03, 2022</div>
                    <div>TOPAZ PUBLISHING HAUS CO. | Tel: 822-3443 | Cell: 0945-548-2022 | 63-A Matahimik St., Teachers Village, Diliman Q.C.</div>
                    <div>NON VAT Reg. TIN: 004-720-224-00000 | Printer's Accreditation No. 039MP20240000000003 (Valid: Feb 05, 2024 - Feb 05, 2029)</div>
                </div>
                <div class="input-tax-notice" style="font-weight: bold; font-size: 8pt; color: #000; text-align: center; text-decoration: underline; margin-top: 4px;">
                    *THIS DOCUMENT IS NOT VALID FOR CLAIM OF INPUT TAXES*
                </div>
            </div>
        </div>
    </div>

    <!-- 1 WHOLE PRE-PRINTED BIR OVERLAY (Exact Fit to whole.pdf) -->
    <div class="preprinted-overlay preprinted-overlay-whole">
        <!-- Customer Info -->
        <div style="position: absolute; left: 1.55in; top: 1.71in; width: 4.3in; font-weight: bold; font-size: 10pt;">{{ $custName }}</div>
        <div style="position: absolute; left: 1.55in; top: 2.02in; width: 3.5in; font-weight: bold; font-size: 9.5pt; line-height: 1.25; white-space: normal; overflow-wrap: break-word;">{{ $custAddress }}</div>
      
        
      
      
        <div style="position: absolute; left: 1.45in; top: 2.84in; width: 4.3in; font-weight: bold; font-size: 10pt;">{{ $custTin }}</div>

        <!-- Transaction Details -->
        <div style="position: absolute; left: 6.35in; top: 1.85in; width: 1.8in; font-weight: bold; font-size: 10pt;">{{ $orderDate }}</div>
        <div style="position: absolute; left: 6.35in; top: 2.10in; width: 1.8in; font-weight: bold; font-size: 10pt;">{{ $termsVal }}</div>
        <div style="position: absolute; left: 6.35in; top: 2.36in; width: 1.8in; font-weight: bold; font-size: 10pt;">{{ $dueDate }}</div>

        <!-- Line Items (Starts at Y = 3.54in) -->
        <div style="position: absolute; left: 0.4in; top: 3.36in; width: 7.7in;">
            @foreach($itemsToPrint as $idx => $item)
                @php
                    if ($item->bookIndex) {
                        $desc = $item->bookIndex->display_name;
                    } elseif ($item->bundle) {
                        $desc = '[BUNDLE] ' . $item->bundle->name;
                    } else {
                        $desc = $item->book?->name ?? ($item->product_name ?? 'Product Item');
                    }
                    $qty = (float) $item->quantity;
                    $price = (float) ($item->unit_price ?? $item->price);
                    $subtotal = (float) ($item->amount ?? ($item->subtotal !== null ? $item->subtotal : ($qty * $price)));
                    $topOffset = $idx * 0.15;
                @endphp
                <div style="position: absolute; top: {{ $topOffset }}in; left: 0.45in; width: 0.6in; text-align: center;  font-size: 6pt;">{{ $qty }}</div>
                <div style="position: absolute; top: {{ $topOffset }}in; left: 1.15in; width: 3.9in;  font-size: 6pt; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $desc }}</div>
                <div style="position: absolute; top: {{ $topOffset }}in; left: 5.15in; width: 0.6in; text-align: center; font-size: 6pt;">{{ ($item->area && $item->area !== 'N/A' && $item->area !== '-') ? $item->area : '' }}</div>
                <div style="position: absolute; top: {{ $topOffset }}in; left: 5.55in; width: 0.9in; text-align: right; font-size: 6pt;">{{ $soSym }}{{ number_format($price, 2) }}</div>
                <div style="position: absolute; top: {{ $topOffset }}in; left: 6.52in; width: 0.9in; text-align: right;  font-size: 6pt;">{{ $soSym }}{{ number_format($subtotal, 2) }}</div>
            @endforeach
        </div>

        <!-- Payment Checkmark -->
        @if($isCash)
            <div style="position: absolute; left: 0.48in; top: 8.35in; font-size: 11pt; font-weight: bold;">✓</div>
        @else
            <div style="position: absolute; left: 1.47in; top: 8.35in; font-size: 11pt; font-weight: bold;">✓</div>
        @endif

        <!-- Totals -->

        @if($freight > 0)
            <div style="position: absolute; left: 4.60in; top: 6.75in; width: 3.4in; text-align: right; font-weight: bold; font-size: 10pt;">
            	<span style="font-size: 8.5pt; font-weight: bold; margin-right: 6px;">SUBTOTAL:</span>{{ $soSym }}{{ number_format($itemsSubtotal, 2) }}
        	</div>
            <div style="position: absolute; left: 4.60in; top: 6.95in; width: 3.4in; text-align: right; font-weight: bold; font-size: 10pt;">
                <span style="font-size: 8.5pt; font-weight: bold; margin-right: 6px;">FREIGHT:</span>{{ $soSym }}{{ number_format($freight, 2) }}
            </div>
        @endif
        @if($serviceFee > 0)
      		<div style="position: absolute; left: 4.60in; top: 6.75in; width: 3.4in; text-align: right; font-weight: bold; font-size: 10pt;">
            	<span style="font-size: 8.5pt; font-weight: bold; margin-right: 6px;">SUBTOTAL:</span>{{ $soSym }}{{ number_format($itemsSubtotal, 2) }}
        	</div>
            <div style="position: absolute; left: 4.60in; top: 6.95in; width: 3.4in; text-align: right; font-weight: bold; font-size: 10pt;">
                <span style="font-size: 8.5pt; font-weight: bold; margin-right: 6px;">SERVICE FEE:</span>{{ $soSym }}{{ number_format($serviceFee, 2) }}
            </div>
        @endif
        @if($discount > 0)
            <div style="position: absolute; left: 4.60in; top: 6.75in; width: 3.4in; text-align: right; font-weight: bold; font-size: 10pt;">
            	<span style="font-size: 8.5pt; font-weight: bold; margin-right: 6px;">SUBTOTAL:</span>{{ $soSym }}{{ number_format($itemsSubtotal, 2) }}
        	</div>
            <div style="position: absolute; left: 4.60in; top: 7.15in; width: 3.4in; text-align: right; font-weight: bold; font-size: 10pt;">
                <span style="font-size: 8.5pt; font-weight: bold; margin-right: 6px;">DISCOUNT:</span>-{{ $soSym }}{{ number_format($discount, 2) }}
            </div>
        @endif
        @if($wht > 0)
            <div style="position: absolute; left: 4.60in; top: 7.34in; width: 3.4in; text-align: right; font-weight: bold; font-size: 10pt;">
                <span style="font-size: 8.5pt; font-weight: bold; margin-right: 6px;">LESS WHT:</span>-{{ number_format($wht, 2) }}
            </div>
        @endif
        <div style="position: absolute; left: 4.60in; top: 8.10in; width: 3.4in; text-align: right; font-weight: bold; font-size: 11pt;">
            <span style="font-size: 9pt; font-weight: bold; margin-right: 6px;"></span>{{ $soSym }}{{ number_format($calculatedTotalSales, 2) }}
        </div>
        @if($wht > 0)
            <div style="position: absolute; left: 4.60in; top: 7.90in; width: 3.4in; text-align: right; font-weight: bold; font-size: 11pt;">
                <span style="font-size: 9pt; font-weight: bold; margin-right: 6px;">TOTAL DUE:</span>{{ $soSym }}{{ number_format($totalAmountDue, 2) }}
            </div>
        @endif

        <!-- Signatories -->
        <div style="position: absolute; left: 3.02in; top: 9.54in; width: 1.8in; text-align: center; font-weight: bold; font-size: 8pt;">{{ $order->preparedBy?->name ?? '' }}</div>
        <div style="position: absolute; left: 4.92in; top: 9.54in; width: 1.8in; text-align: center; font-weight: bold; font-size: 8pt;">{{ $order->mktApprovedBy?->name ?? ($order->prodApprovedBy?->name ?? '') }}</div>
        <div style="position: absolute; left: 6.82in; top: 9.54in; width: 1.4in; text-align: center; font-weight: bold; font-size: 8pt;">{{ $custName }}</div>
    </div>

    <!-- 1/2 HALF PAGE PRE-PRINTED BIR OVERLAY (Exact Fit to half.pdf) -->
    <div class="preprinted-overlay preprinted-overlay-half">
        <!-- Customer Info -->
        <div style="position: absolute; left: 1.28in; top: 1.34in; width: 4.2in; font-weight: bold; font-size: 9.5pt;">{{ $custName }}</div>
        <div style="position: absolute; left: 1.28in; top: 1.65in; width: 4.2in; font-weight: bold; font-size: 9pt; line-height: 1.2; max-height: 0.45in; overflow: hidden;">{{ $custAddress }}</div>
        <div style="position: absolute; left: 1.40in; top: 2.22in; width: 4.2in; font-weight: bold; font-size: 9.5pt;">{{ $custTin }}</div>
        <div style="position: absolute; left: 1.75in; top: 2.48in; width: 3.8in; font-weight: bold; font-size: 9.5pt;">{{ $order->customer?->business_style ?? '' }}</div>

        <!-- Transaction Details -->
        <div style="position: absolute; left: 6.2in; top: 1.50in; width: 1.8in; font-weight: bold; font-size: 9.5pt;">{{ $orderDate }}</div>
        <div style="position: absolute; left: 6.2in; top: 1.75in; width: 1.8in; font-weight: bold; font-size: 9.5pt;">{{ $termsVal }}</div>
        <div style="position: absolute; left: 6.2in; top: 1.98in; width: 1.8in; font-weight: bold; font-size: 9.5pt;">{{ $dueDate }}</div>

        <!-- Line Items (Starts at Y = 3.12in) -->
        <div style="position: absolute; left: 0.4in; top: 3.12in; width: 7.7in;">
            @foreach($itemsToPrint as $idx => $item)
                @php
                    if ($item->bookIndex) {
                        $desc = $item->bookIndex->display_name;
                    } elseif ($item->bundle) {
                        $desc = '[BUNDLE] ' . $item->bundle->name;
                    } else {
                        $desc = $item->book?->name ?? ($item->product_name ?? 'Product Item');
                    }
                    $qty = (float) $item->quantity;
                    $price = (float) ($item->unit_price ?? $item->price);
                    $subtotal = (float) ($item->amount ?? ($item->subtotal !== null ? $item->subtotal : ($qty * $price)));
                    $topOffset = $idx * 0.28;
                @endphp
                <div style="position: absolute; top: {{ $topOffset }}in; left: 0.45in; width: 0.6in; text-align: center; font-weight: bold; font-size: 9.5pt;">{{ $qty }}</div>
                <div style="position: absolute; top: {{ $topOffset }}in; left: 1.15in; width: 3.9in; font-weight: bold; font-size: 9.5pt; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $desc }}</div>
                <div style="position: absolute; top: {{ $topOffset }}in; left: 5.15in; width: 0.6in; text-align: center; font-size: 9.5pt;">{{ ($item->area && $item->area !== 'N/A' && $item->area !== '-') ? $item->area : '' }}</div>
                <div style="position: absolute; top: {{ $topOffset }}in; left: 5.85in; width: 0.9in; text-align: right; font-size: 9.5pt;">{{ $soSym }}{{ number_format($price, 2) }}</div>
                <div style="position: absolute; top: {{ $topOffset }}in; left: 6.75in; width: 0.9in; text-align: right; font-weight: bold; font-size: 9.5pt;">{{ $soSym }}{{ number_format($subtotal, 2) }}</div>
            @endforeach
        </div>

        <!-- Payment Checkmark -->
        @if($isCash)
            <div style="position: absolute; left: 0.68in; top: 4.72in; font-size: 10pt; font-weight: bold;">✓</div>
        @else
            <div style="position: absolute; left: 1.77in; top: 4.62in; font-size: 10pt; font-weight: bold;">✓</div>
        @endif

        <!-- Totals -->

        @if($freight > 0)
      
         <div style="position: absolute; left: 4.60in; top: 4.10in; width: 3.4in; text-align: right; font-weight: bold; font-size: 9.5pt;">
            <span style="font-size: 8pt; font-weight: bold; margin-right: 6px;">SUBTOTAL:</span>{{ number_format($itemsSubtotal, 2) }}
        </div>
            <div style="position: absolute; left: 4.60in; top: 4.25in; width: 3.4in; text-align: right; font-weight: bold; font-size: 9.5pt;">
                <span style="font-size: 8pt; font-weight: bold; margin-right: 6px;">FREIGHT:</span>{{ $soSym }}{{ number_format($freight, 2) }}
            </div>
        @endif
        @if($serviceFee > 0)
               <div style="position: absolute; left: 4.60in; top: 4.10in; width: 3.4in; text-align: right; font-weight: bold; font-size: 9.5pt;">
            <span style="font-size: 8pt; font-weight: bold; margin-right: 6px;">SUBTOTAL:</span>{{ number_format($itemsSubtotal, 2) }}
        </div>
            <div style="position: absolute; left: 4.60in; top: 4.25in; width: 3.4in; text-align: right; font-weight: bold; font-size: 9.5pt;">
                <span style="font-size: 8pt; font-weight: bold; margin-right: 6px;">SERVICE FEE:</span>{{ $soSym }}{{ number_format($serviceFee, 2) }}
            </div>
        @endif
        @if($discount > 0)
               <div style="position: absolute; left: 4.60in; top: 4.10in; width: 3.4in; text-align: right; font-weight: bold; font-size: 9.5pt;">
            <span style="font-size: 8pt; font-weight: bold; margin-right: 6px;">SUBTOTAL:</span>{{ number_format($itemsSubtotal, 2) }}
        </div>
            <div style="position: absolute; left: 4.60in; top: 4.40in; width: 3.4in; text-align: right; font-weight: bold; font-size: 9.5pt;">
                <span style="font-size: 8pt; font-weight: bold; margin-right: 6px;">DISCOUNT:</span>-{{ $soSym }}{{ number_format($discount, 2) }}
            </div>
        @endif
        <div style="position: absolute; left: 4.60in; top: 4.70in; width: 3.4in; text-align: right; font-weight: bold; font-size: 10.5pt;">
            <span style="font-size: 8.5pt; font-weight: bold; margin-right: 6px;"></span>{{ $soSym }}{{ number_format($calculatedTotalSales, 2) }}
        </div>

        <!-- Signatories -->
        <div style="position: absolute; left: 3.20in; top: 5.80in; width: 1.8in; text-align: center; font-weight: bold; font-size: 9.5pt;">{{ $order->preparedBy?->name ?? '' }}</div>
        <div style="position: absolute; left: 5.15in; top: 5.80in; width: 1.8in; text-align: center; font-weight: bold; font-size: 9.5pt;">{{ $order->mktApprovedBy?->name ?? ($order->prodApprovedBy?->name ?? '') }}</div>
        <div style="position: absolute; left: 6.95in; top: 5.80in; width: 1.5in; text-align: center; font-weight: bold; font-size: 9.5pt;">{{ $custName }}</div>
    </div>

    <script>
        function togglePreprintedMode(checkbox) {
            if (checkbox.checked) {
                document.body.classList.add('preprinted-mode');
            } else {
                document.body.classList.remove('preprinted-mode');
            }
        }

        function changePaperSize(size) {
            if (size === 'half') {
                document.body.classList.add('half-page-mode');
                document.body.classList.remove('whole-page-mode');
            } else {
                document.body.classList.remove('half-page-mode');
                document.body.classList.add('whole-page-mode');
            }
        }

        // Initialize paper size and preprinted mode on load
        document.addEventListener('DOMContentLoaded', function() {
            var select = document.getElementById('paperSizeSelect');
            if (select) {
                changePaperSize(select.value);
            }
            var isDataOnly = {{ (request('preprinted') || request('data_only')) ? 'true' : 'false' }};
            if (isDataOnly) {
                var toggle = document.getElementById('preprintedToggle');
                if (toggle) {
                    toggle.checked = true;
                    togglePreprintedMode(toggle);
                }
            }
        });
    </script>

</body>
</html>
