<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Receipt - {{ $order->so_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    @php
        $hideActions = request('hide_actions', false) || request('iframe', false);
        $format = request('format', 'whole');
        $halfPart = request('half', null);
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

        .dr-box {
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
        body.half-page-mode .dr-box {
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
        body.half-page-mode .particulars-block {
            padding: 4px 6px !important;
            font-size: 7pt !important;
            margin-top: 3px !important;
            margin-bottom: 3px !important;
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
        body.half-page-mode.preprinted-mode .dr-box {
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

        .doc-no span.doc-no-val {
            color: #d32f2f;
            font-size: 11.5pt;
            font-weight: 900;
            letter-spacing: 0.5px;
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

        .particulars-block {
            padding: 6px 10px;
            background: #fafafa;
            border: 1px dashed #bbb;
            border-radius: 4px;
            font-size: 8pt;
            margin-top: 6px;
            margin-bottom: 8px;
            line-height: 1.3;
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

        body.preprinted-mode .dr-box {
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

            .dr-box {
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
            body.preprinted-mode .dr-box {
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
                <label class="form-check-label fw-bold small text-dark" for="preprintedToggle">Print Data Only (For Pre-Printed DR Paper)</label>
            </div>
            <button onclick="window.print()" class="btn btn-danger btn-sm px-4 shadow-sm" style="background:#ff0000; border: none;">
                <i class="las la-print me-1"></i> Print / Save PDF
            </button>
        </div>
    </div>
    @endif

    @php
        $rawDrItems = ($deliveryReceipt && count($deliveryReceipt->items) > 0) ? $deliveryReceipt->items : ($order ? $order->items : collect());
        $allItems = collect($rawDrItems)->filter(function($i) {
            $qty = (float)($i->quantity ?? 0);
            $sent = (float)($i->sent_qty ?? 0);
            $req = (float)($i->requested_qty ?? 0);
            $cust = (float)($i->customer_selected_qty ?? 0);
            return ($qty > 0 || $sent > 0 || $req > 0 || $cust > 0);
        });

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

        $custName = $order->customer_representative ?: ($order->customer?->customer_name ?: 'Recipient / Customer');
        $companyName = $order->customer?->company_name && !in_array(strtolower($order->customer->company_name), ['intracode', 'individual']) ? $order->customer->company_name : '';
        if ($companyName && $companyName !== $custName) {
            $displayCustomerStr = $companyName . ' — ' . $custName;
        } else {
            $displayCustomerStr = $custName;
        }

        $rawAddr = $order->shipping_address ?: ($order->billing_address ?: ($order->customer?->shipping_address ?? ($order->customer?->billing_address ?? '')));
        $custAddress = ($rawAddr === 'N/A') ? '' : $rawAddr;
        
        $rawTin = $order->customer?->tin ?? '';
        $custTin = ($rawTin === 'N/A') ? '' : $rawTin;
        
        $termsVal = $order->terms ?: ($order->payment_method ? strtoupper($order->payment_method) : 'Net 90');
        $dateFormatted = $order->dr_prepared_at 
            ? \Carbon\Carbon::parse($order->dr_prepared_at)->format('m/d/Y') 
            : ($order->created_at ? $order->created_at->format('m/d/Y') : date('m/d/Y'));
        
        // Determine DR Number display (Display SI Number if available, as requested)
        $activeInvoice = \App\Models\SalesInvoice::where('so_id', $order->id)->where('status', '!=', 'cancelled')->latest()->first();
        $siNumber = $order->si_number ?: ($deliveryReceipt?->si_number ?: ($activeInvoice?->si_number ?? ($order->invoice?->si_number ?? ($order->invoices?->first()?->si_number ?? null))));
        $drNoDisplay = $siNumber ?: ($deliveryReceipt?->dr_number ?: ($order->dr_number ?: 'DR-' . $order->so_number));

        // Signatories
        $preparedByName = $order->drPreparedBy?->name ?? ($order->preparedBy?->name ?? 'System');
        $approvedByName = $order->drApprovedBy?->name ?? ($order->signedBy?->name ?? ($order->acctApprovedBy?->name ?? ($order->mktApprovedBy?->name ?? ($order->prodApprovedBy?->name ?? ''))));
        $receivedByName = $order->customer_representative ?: ($order->customer?->customer_name ?? '');

        // PO Number
        $poNumber = $order->ref_number ?? ($order->po_number ?? '');

        // Total quantity calculation
        $totalQuantitySum = 0;
    @endphp

    <!-- Standard Delivery Receipt Box -->
    <div class="dr-box">
        <div>
            <!-- Header Section -->
            <div class="header-section">
                <div class="header-logo-details">
                    <img src="{{ asset('images/claeritian_logo.png') }}" alt="Logo" class="header-logo" onerror="this.src='https://via.placeholder.com/65?text=C'">
                    <div>
                        <h1 class="company-name">Claretian Communications Foundation, Inc.</h1>
                        <div class="company-subtitle">Non-Vat Reg. TIN: 000-395-713-00000</div>
                        <div class="company-address">8 Mayumi Street, U.P. Village, Diliman 1101 Quezon City NCR, Second District Philippines</div>
                        <div class="company-contact">Tel: (02) 921-3984 Fax: (02) 921-6205</div>
                    </div>
                </div>
                <div class="header-right">
                    <div class="doc-no"><span class="text-muted">№ </span><span class="doc-no-val">{{ $drNoDisplay }}</span></div>
                    <div class="doc-title">Delivery Receipt</div>
                    <div class="text-muted small fw-bold" style="font-size: 7.5pt;">NON-VAT REGISTERED</div>
                    @if(isset($halfLabel) && $halfLabel)
                        <div style="font-size: 8pt; color: #666; font-weight: bold;">{{ $halfLabel }}</div>
                    @endif
                </div>
            </div>

            <!-- Customer & Transaction Details Grid -->
            <table class="info-grid">
                <tr>
                    <td class="info-label">Delivered To:</td>
                    <td class="info-value-line" style="width: 55%;">{{ $displayCustomerStr }}</td>
                    <td class="info-label" style="padding-left: 15px;">Date:</td>
                    <td class="info-value-line">{{ $dateFormatted }}</td>
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
                    <td class="info-label" style="padding-left: 15px;">P.O. No.:</td>
                    <td class="info-value-line">{{ $poNumber ?: '—' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Business Style:</td>
                    <td class="info-value-line" colspan="3">{{ $order->customer?->business_style ?? '' }}</td>
                </tr>
            </table>

            <!-- Items Table (NO AMOUNT, QTY LANG NG TITLES - AS IN PIC 3) -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 10%; text-align: center;">QTY</th>
                        <th style="width: 10%; text-align: center;">U/M</th>
                        <th style="width: 60%;">Items / Particulars</th>
                        <th style="width: 20%; text-align: center;">ISBN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($itemsToPrint as $item)
                        @php
                            $sQty = (float)($item->sent_qty ?? 0);
                            $rQty = (float)($item->requested_qty ?? 0);
                            $iQty = (float)($item->quantity ?? 0);
                            $cQty = (float)($item->customer_selected_qty ?? 0);

                            $isConsignment = in_array($order->type, ['area_consignment', 'area_sales_consignment']);

                            if ($isConsignment && $cQty > 0) {
                                $displayQty = (int)$cQty;
                            } elseif ($sQty > 0) {
                                $displayQty = (int)$sQty;
                            } elseif ($iQty > 0) {
                                $displayQty = (int)$iQty;
                            } elseif ($cQty > 0) {
                                $displayQty = (int)$cQty;
                            } elseif ($rQty > 0) {
                                $displayQty = (int)$rQty;
                            } else {
                                $displayQty = (int)$iQty;
                            }

                            $totalQuantitySum += $displayQty;

                            $unit = $item->unit ?: ($item->book?->unit ?: 'pc');

                            if ($item->bookIndex) {
                                $desc = $item->bookIndex->display_name ?? ($item->bookIndex->title ?? ($item->bookIndex->custom_name ?? ($item->bookIndex->book?->name ?? 'Item')));
                            } elseif ($item->bundle) {
                                $desc = '[BUNDLE] ' . $item->bundle->name;
                            } elseif (!empty($item->product_name)) {
                                $desc = $item->product_name;
                            } else {
                                $desc = $item->book?->name ?? ($item->product?->name ?? ($item->item_name ?? ($item->product_name ?? ($item->description ?? 'Item'))));
                            }

                            $isbn = $item->isbn 
                                ?? ($item->book?->isbn 
                                ?? ($item->bookIndex?->isbn 
                                ?? ($item->book?->sku 
                                ?? ($item->book?->barcode 
                                ?? ($item->article_number 
                                ?? ($item->book?->article 
                                ?? ($item->book?->item_code ?? '')))))));
                        @endphp
                        <tr>
                            <td style="text-align: center; font-weight: bold;">{{ $displayQty }}</td>
                            <td style="text-align: center;">{{ $unit }}</td>
                            <td style="font-weight: 600;">{{ $desc }}</td>
                            <td style="text-align: center;">{{ $isbn ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #777; padding: 15px;">No items found for this delivery receipt.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="border-top: 2px solid #000; border-bottom: 2px solid #000;">
                        <td style="text-align: center; font-weight: bold;">{{ $totalQuantitySum }}</td>
                        <td style="text-align: center; font-weight: bold;">pcs</td>
                        <td colspan="2" style="font-weight: 900; text-transform: uppercase; font-size: 8.5pt; padding-left: 8px;">
                            Total Quantity Delivered
                        </td>
                    </tr>
                </tfoot>
            </table>

            <!-- Particulars / Delivery Notes Block (Matching Pic 3) -->
            @php
                $notesList = [];
                if ($order->remarks) $notesList[] = $order->remarks;
                if ($order->notes && $order->notes !== $order->remarks) $notesList[] = $order->notes;
                if ($deliveryReceipt?->remarks && !in_array($deliveryReceipt->remarks, $notesList)) $notesList[] = $deliveryReceipt->remarks;
                if ($order->forwarder) $notesList[] = 'Forwarder / Carrier: ' . $order->forwarder;
            @endphp
            @if(count($notesList) > 0 || $order->shipping_address)
            <div class="particulars-block">
                @if(count($notesList) > 0)
                    <div class="fw-bold mb-1" style="font-size: 8pt; text-transform: uppercase; color: #555;">Delivery Instructions &amp; Remarks:</div>
                    @foreach($notesList as $note)
                        <div>• {{ $note }}</div>
                    @endforeach
                @endif
                @if($order->shipping_address && $order->shipping_address !== $order->billing_address)
                    <div class="mt-1"><strong>Deliver to:</strong> {{ $order->shipping_address }}</div>
                @endif
            </div>
            @endif
        </div>

        <div>
            <!-- Signatories Section (Matching Pic 3) -->
            <div class="signatories-row">
                <div class="sig-col">
                    <div class="sig-label">Prepared by:</div>
                    <div class="sig-line">{{ $preparedByName }}</div>
                    <div class="text-center text-muted" style="font-size: 7.5pt; margin-top: 2px;">{{ $dateFormatted }}</div>
                </div>
                <div class="sig-col">
                    <div class="sig-label">Approved by:</div>
                    <div class="sig-line">{{ $approvedByName ?: '________________' }}</div>
                    <div class="text-center text-muted" style="font-size: 7.5pt; margin-top: 2px;">{{ $dateFormatted }}</div>
                </div>
                <div class="sig-col">
                    <div class="sig-label" style="font-size: 7.5pt; line-height: 1.2;">Received the above goods and services in good order &amp; condition:</div>
                    <div class="sig-line" style="margin-top: 14px;"></div>
                    <div class="text-center fw-bold" style="font-size: 7pt; margin-top: 3px;">CUSTOMER SIGNATURE OVER PRINTED NAME</div>
                </div>
            </div>

            <!-- Footer Notice -->
            <div class="footer-notice">
                <div class="bir-details">
                    <div>30 Pads (50x4) 15501-17000 | BIR Authority to Print No. 000030HU20240000011234 (Date of ATP: October 17, 2024)</div>
                    <div>Looseleaf Permit No. LLAR-039-1022-00083 | Date Issued: 10-03-2022</div>
                    <div>TOPAZ PUBLISHING HAUS CO. | Cell No.: 0945-548-2022 | Tel: 822-3443 | 63-A Matahimik St., Teachers Village, Diliman Q.C.</div>
                    <div>NON-VAT Reg. TIN: 004-720-224-00000 | Printer's Accreditation No. 039MP20240000000003</div>
                </div>
                <div class="input-tax-notice" style="font-weight: bold; font-size: 7.5pt; color: #000; text-align: right; text-decoration: underline;">
                    *THIS DOCUMENT IS NOT VALID FOR CLAIM OF INPUT TAXES*
                </div>
            </div>
        </div>
    </div>

    <!-- PRE-PRINTED OVERLAY (For feeding Pic 3 physical carbon-copy paper into printer) -->
    <div class="preprinted-overlay preprinted-overlay-whole">
        <!-- Customer Info -->
        <div style="position: absolute; left: 1.55in; top: 1.65in; width: 4.3in; font-weight: bold; font-size: 10pt;">{{ $displayCustomerStr }}</div>
        <div style="position: absolute; left: 1.55in; top: 1.95in; width: 3.5in; font-weight: bold; font-size: 9pt; line-height: 1.25;">{{ $custAddress }}</div>
        <div style="position: absolute; left: 1.55in; top: 2.50in; width: 4.3in; font-weight: bold; font-size: 10pt;">{{ $custTin }}</div>

        <!-- Transaction Details -->
        <div style="position: absolute; right: 0.85in; top: 1.65in; font-weight: bold; font-size: 9.5pt;">{{ $dateFormatted }}</div>
        <div style="position: absolute; right: 0.85in; top: 1.95in; font-weight: bold; font-size: 9.5pt;">{{ $termsVal }}</div>
        <div style="position: absolute; right: 0.85in; top: 2.25in; font-weight: bold; font-size: 9.5pt;">{{ $poNumber }}</div>

        <!-- Items Rows -->
        <div style="position: absolute; left: 0.5in; top: 3.10in; width: 7.5in;">
            @foreach($itemsToPrint as $idx => $item)
                @php
                    $sQty = (float)($item->sent_qty ?? 0);
                    $rQty = (float)($item->requested_qty ?? 0);
                    $iQty = (float)($item->quantity ?? 0);
                    $cQty = (float)($item->customer_selected_qty ?? 0);

                    $isConsignment = in_array($order->type, ['area_consignment', 'area_sales_consignment']);

                    if ($isConsignment && $cQty > 0) {
                        $displayQty = (int)$cQty;
                    } elseif ($sQty > 0) {
                        $displayQty = (int)$sQty;
                    } elseif ($iQty > 0) {
                        $displayQty = (int)$iQty;
                    } elseif ($cQty > 0) {
                        $displayQty = (int)$cQty;
                    } elseif ($rQty > 0) {
                        $displayQty = (int)$rQty;
                    } else {
                        $displayQty = (int)$iQty;
                    }

                    $unit = $item->unit ?: ($item->book?->unit ?: 'pc');

                    if ($item->bookIndex) {
                        $desc = $item->bookIndex->display_name ?? ($item->bookIndex->title ?? ($item->bookIndex->custom_name ?? ($item->bookIndex->book?->name ?? 'Item')));
                    } elseif ($item->bundle) {
                        $desc = '[BUNDLE] ' . $item->bundle->name;
                    } elseif (!empty($item->product_name)) {
                        $desc = $item->product_name;
                    } else {
                        $desc = $item->book?->name ?? ($item->product?->name ?? ($item->item_name ?? ($item->product_name ?? ($item->description ?? 'Item'))));
                    }

                    $isbn = $item->isbn 
                        ?? ($item->book?->isbn 
                        ?? ($item->bookIndex?->isbn 
                        ?? ($item->book?->sku 
                        ?? ($item->book?->barcode 
                        ?? ($item->article_number 
                        ?? ($item->book?->article 
                        ?? ($item->book?->item_code ?? '')))))));
                @endphp
                <div style="display: flex; margin-bottom: 5px; font-size: 9pt; font-weight: bold;">
                    <div style="width: 0.8in; text-align: center;">{{ $displayQty }}</div>
                    <div style="width: 0.7in; text-align: center;">{{ $unit }}</div>
                    <div style="width: 4.4in; padding-left: 10px;">{{ $desc }}</div>
                    <div style="width: 1.6in; text-align: center;">{{ $isbn ?: '—' }}</div>
                </div>
            @endforeach
        </div>

        <!-- Signatures -->
        <div style="position: absolute; left: 0.8in; bottom: 1.5in; font-size: 9pt; font-weight: bold;">{{ $preparedByName }}</div>
        <div style="position: absolute; left: 3.2in; bottom: 1.5in; font-size: 9pt; font-weight: bold;">{{ $approvedByName }}</div>
    </div>

    <script>
        function changePaperSize(size) {
            if (size === 'half') {
                document.body.classList.add('half-page-mode');
            } else {
                document.body.classList.remove('half-page-mode');
            }
        }

        function togglePreprintedMode(checkbox) {
            if (checkbox.checked) {
                document.body.classList.add('preprinted-mode');
            } else {
                document.body.classList.remove('preprinted-mode');
            }
        }

        // Initialize from URL parameters
        @if(($format ?? 'whole') === 'half')
            document.body.classList.add('half-page-mode');
        @endif
        @if(request('preprinted') || request('data_only'))
            document.body.classList.add('preprinted-mode');
        @endif

        @if(request('autoprint'))
        window.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                window.print();
            }, 400);
        });
        @endif
    </script>
</body>
</html>
