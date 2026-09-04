<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acknowledgement Receipt (Complimentary) - {{ $order->so_number }}</title>
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
        body.half-page-mode .doc-no-val {
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

        .doc-no-val {
            color: #d9251c;
            font-size: 11.5pt;
            font-weight: 900;
        }

        .doc-title {
            font-size: 13pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
            color: #000;
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
            width: 80px;
            white-space: nowrap;
        }

        .info-value-line {
            border-bottom: 1px solid #000;
            padding-left: 5px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9pt;
        }

        .items-table th {
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
            padding: 5px 6px;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            font-size: 8.5pt;
        }

        .items-table td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 12px;
            margin-top: 8px;
            font-size: 8.5pt;
        }

        .signatories-row {
            display: flex;
            justify-content: space-between;
            margin-top: 12px;
            margin-bottom: 8px;
            font-size: 8.5pt;
            gap: 20px;
        }

        .sig-box {
            flex: 1;
            text-align: center;
        }

        .sig-line {
            border-bottom: 1.5px solid #000;
            height: 30px;
            margin-bottom: 4px;
        }

        .sig-label {
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
        }

        .sig-name {
            font-weight: bold;
            font-size: 8.5pt;
        }

        .footer-notice {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-top: 1px solid #ccc;
            padding-top: 4px;
            margin-top: 6px;
            font-size: 7pt;
            color: #555;
        }

        .actions-bar {
            max-width: 8.5in;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 10px 18px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        /* Pre-printed Overlay Mode */
        body.preprinted-mode .header-section,
        body.preprinted-mode .info-label,
        body.preprinted-mode .items-table th,
        body.preprinted-mode .sig-label,
        body.preprinted-mode .sig-line,
        body.preprinted-mode .footer-notice,
        body.preprinted-mode .summary-box {
            visibility: hidden !important;
        }

        body.preprinted-mode .info-value-line {
            border-bottom: none !important;
        }

        body.preprinted-mode .items-table td {
            border: none !important;
        }

        body.preprinted-mode .invoice-box {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }

        @media print {
            body {
                background-color: #fff !important;
                padding: 0 !important;
            }
            .actions-bar {
                display: none !important;
            }
            .invoice-box {
                border: none !important;
                box-shadow: none !important;
                padding: 0.15in 0.25in !important;
                max-width: 100% !important;
                width: 100% !important;
                min-height: auto !important;
            }
            @page {
                size: letter portrait;
                margin: 0.3in 0.4in;
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
                <label class="form-check-label fw-bold small text-dark" for="preprintedToggle">Print Data Only (For Pre-Printed Paper)</label>
            </div>
            <button onclick="window.print()" class="btn btn-danger btn-sm px-4 shadow-sm" style="background:#ff0000; border: none;">
                <i class="las la-print me-1"></i> Print / Save PDF
            </button>
        </div>
    </div>
    @endif

    @php
        $allItems = collect($order->items ?? [])->filter(function($i) {
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

        $custName = ($order->customer?->customer_name && $order->customer->customer_name !== 'N/A') 
            ? $order->customer->customer_name 
            : ($order->customer_representative ?: 'Recipient / Beneficiary');
        
        $rawAddr = $order->billing_address ?: ($order->shipping_address ?: ($order->customer?->billing_address ?? ($order->customer?->shipping_address ?? '')));
        $custAddress = ($rawAddr === 'N/A') ? '' : $rawAddr;
        
        $rawTin = $order->customer?->tin ?? '';
        $custTin = ($rawTin === 'N/A') ? '' : $rawTin;
        
        $termsVal = 'COMPLIMENTARY / DONATION';
        $orderDate = $order->ar_prepared_at 
            ? \Carbon\Carbon::parse($order->ar_prepared_at)->format('m/d/Y') 
            : ($order->created_at ? $order->created_at->format('m/d/Y') : date('m/d/Y'));

        $arNoDisplay = 'AR-' . $order->so_number;
        $totalQuantitySum = 0;

        $preparedByName = $order->arPreparedBy?->name ?? ($order->preparedBy?->name ?? 'Accounting Staff');
        $approvedByName = $order->signedBy?->name ?? ($order->acctApprovedBy?->name ?? ($order->mktApprovedBy?->name ?? 'Admin & Finance'));
    @endphp

    <div class="invoice-box">
        <div>
            <!-- Header Section (Matches Sales Invoice) -->
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
                    <div class="doc-no"><span class="text-muted" style="font-size: 11pt;">№ </span><span class="doc-no-val">{{ $arNoDisplay }}</span></div>
                    <div class="doc-title">Acknowledgement Receipt</div>
                    <div class="text-muted small fw-bold" style="font-size: 7.5pt;">COMPLIMENTARY / NON-VAT REGISTERED</div>
                    @if(isset($halfLabel) && $halfLabel)
                        <div style="font-size: 8pt; color: #666; font-weight: bold;">{{ $halfLabel }}</div>
                    @endif
                </div>
            </div>

            <!-- Customer & Transaction Details Grid (Matches Sales Invoice) -->
            <table class="info-grid">
                <tr>
                    <td class="info-label">Issued to:</td>
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
                    <td class="info-label" style="padding-left: 15px;">SO Ref. No.:</td>
                    <td class="info-value-line font-monospace fw-bold">#{{ $order->so_number }}</td>
                </tr>
                <tr>
                    <td class="info-label">Business Style:</td>
                    <td class="info-value-line" colspan="3">{{ $order->customer?->business_style ?: 'Complimentary / Donation Distribution (COA 5100)' }}</td>
                </tr>
            </table>

            <!-- Items Table (Matches Sales Invoice Typography & Design) -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 10%; text-align: center;">QTY</th>
                        <th style="width: 10%; text-align: center;">U/M</th>
                        <th style="width: 60%;">ITEMS / PARTICULARS</th>
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

                            if ($cQty > 0) {
                                $displayQty = (int)$cQty;
                            } elseif ($sQty > 0) {
                                $displayQty = (int)$sQty;
                            } elseif ($iQty > 0) {
                                $displayQty = (int)$iQty;
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
                            <td style="text-align: center; font-weight: bold; font-size: 10pt;">{{ $displayQty }}</td>
                            <td style="text-align: center; color: #333;">{{ $unit }}</td>
                            <td style="font-weight: 600; font-size: 9.5pt;">{{ $desc }}</td>
                            <td style="text-align: center; font-size: 8.5pt; color: #444;">{{ $isbn ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #777; padding: 15px;">No items found for this receipt.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="border-top: 1.5px solid #000;">
                        <td style="text-align: center; font-weight: bold; font-size: 10pt;">{{ $totalQuantitySum }}</td>
                        <td style="text-align: center; font-weight: bold;">pcs</td>
                        <td colspan="2" style="font-weight: bold; text-transform: uppercase; font-size: 8.5pt; padding-left: 8px;">
                            Total Quantity Delivered / Distributed
                        </td>
                    </tr>
                </tfoot>
            </table>

            <!-- Particulars / Purpose Box -->
            <div class="summary-box">
                <strong>Particulars / Purpose:</strong>
                <div class="text-muted mt-1" style="font-size: 8.5pt;">
                    {{ $order->remarks ?: 'Official complimentary copy distribution (COA 5100). No collection or payment is expected.' }}
                </div>
            </div>
        </div>

        <div>
            <!-- Signatories Section (Matches Sales Invoice Format) -->
            <div class="signatories-row">
                <div class="sig-box">
                    <div class="sig-name">{{ $preparedByName }}</div>
                    <div class="sig-line"></div>
                    <div class="sig-label">Prepared by</div>
                    <div class="text-muted" style="font-size: 7pt;">Accounting Staff</div>
                </div>
                <div class="sig-box">
                    <div class="sig-name">{{ $approvedByName }}</div>
                    <div class="sig-line"></div>
                    <div class="sig-label">Approved by</div>
                    <div class="text-muted" style="font-size: 7pt;">Admin & Finance</div>
                </div>
                <div class="sig-box">
                    <div class="sig-name">{{ $custName }}</div>
                    <div class="sig-line"></div>
                    <div class="sig-label">Received in Good Order</div>
                    <div class="text-muted" style="font-size: 7pt;">Customer / Recipient Signature</div>
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
    </script>

</body>
</html>
