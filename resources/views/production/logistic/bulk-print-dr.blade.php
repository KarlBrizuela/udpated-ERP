<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? ($orders->count() === 1 ? 'Delivery Receipt - ' . $orders->first()->so_number : 'Bulk Print Delivery Receipts') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background-color: #f4f6f9;
            color: #000;
            padding: 15px;
        }

        .dr-box {
            background: #fff;
            max-width: 8.5in;
            margin: 0 auto 25px auto;
            padding: 0.4in 0.5in;
            border: 1px solid #ccc;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            page-break-after: always;
            break-after: page;
        }

        .dr-box:last-child {
            page-break-after: avoid;
            break-after: avoid;
            margin-bottom: 0;
        }

        .form-header {
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #333;
        }

        .form-header .company-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }

        .form-header .company-logo-img {
            height: 55px;
            width: auto;
            object-fit: contain;
        }

        .form-header .company-name {
            font-size: 1.15rem;
            font-weight: 800;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 2px;
        }

        .form-header .company-address,
        .form-header .company-contact {
            font-size: 0.82rem;
            color: #333;
            line-height: 1.2;
        }

        .form-header .document-title {
            text-align: center;
            font-size: 1.6rem;
            font-weight: 900;
            color: #000;
            margin-top: 0.5rem;
            letter-spacing: 1.5px;
        }

        .form-info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
            font-size: 0.88rem;
        }

        .form-info-grid td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .form-info-grid .label-col {
            font-weight: bold;
            color: #000;
            white-space: nowrap;
            width: 110px;
        }

        .form-info-grid .val-col {
            border-bottom: 1px solid #777;
            font-weight: 600;
            color: #111;
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.25rem;
            font-size: 0.85rem;
        }

        .receipt-table thead th {
            background-color: #ff0000 !important;
            color: #ffffff !important;
            padding: 7px 10px;
            font-weight: 700;
            text-align: left;
            border: 1px solid #ff0000;
            font-size: 0.83rem;
            text-transform: uppercase;
        }

        .receipt-table tbody td {
            padding: 6px 10px;
            border: 1px solid #dee2e6;
            vertical-align: middle;
            color: #212529;
        }

        .receipt-table tfoot td {
            padding: 5px 10px;
            border: 1px solid #dee2e6;
            font-size: 0.85rem;
        }

        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1.5rem;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 2px solid #ccc;
        }

        .signature-box {
            display: flex;
            flex-direction: column;
        }

        .signature-box label {
            font-weight: 700;
            color: #333;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }

        .signature-line-box {
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 4px;
            font-size: 0.72rem;
            font-weight: bold;
            color: #000;
        }

        .signature-name {
            font-weight: bold;
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 4px;
            min-height: 1.2rem;
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
                font-size: 11px !important;
                font-family: Arial, Helvetica, sans-serif !important;
            }
            .dr-box {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                margin-bottom: 0 !important;
            }
            .actions-bar {
                display: none !important;
            }
            .receipt-table,
            .receipt-table td,
            .receipt-table tfoot td,
            .receipt-table span,
            .receipt-table div {
                color: #000000 !important;
                font-weight: 700 !important;
                background: #ffffff !important;
                background-color: #ffffff !important;
                border-color: #000000 !important;
            }
            .receipt-table thead th {
                font-weight: 800 !important;
                background-color: #ff0000 !important;
                color: #ffffff !important;
                border: 1px solid #ff0000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .receipt-table td {
                font-weight: 700 !important;
                font-size: 11px !important;
                line-height: 1.3 !important;
                border: 1px solid #000000 !important;
            }
            .form-info-grid td,
            .form-info-grid .label-col,
            .form-info-grid .val-col,
            .form-header .company-name,
            .form-header .company-address,
            .form-header .company-contact,
            .form-header .document-title,
            .form-header div,
            .signature-box label,
            .signature-name,
            .signature-line-box {
                color: #000000 !important;
                opacity: 1 !important;
                font-weight: 700 !important;
            }
            .form-header .company-name {
                font-size: 1.1rem !important;
                font-weight: 900 !important;
            }
            .form-header .company-address,
            .form-header .company-contact {
                font-size: 11px !important;
                font-weight: 700 !important;
            }
            .form-header div.text-muted,
            .form-header .extra-small {
                color: #000000 !important;
                font-weight: 800 !important;
                font-size: 10px !important;
            }
            .signature-section {
                display: table !important;
                table-layout: fixed !important;
                width: 100% !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                page-break-before: auto !important;
                margin-top: 1.5rem !important;
                padding-top: 0.5rem !important;
                border-top: none !important;
                clear: both !important;
            }
            .signature-box {
                display: table-cell !important;
                width: 33.33% !important;
                vertical-align: top !important;
                padding: 0 10px !important;
                text-align: center !important;
            }
            .signature-box label {
                text-align: left !important;
                display: block !important;
            }
            .signature-line-box {
                border-top: 1.5px solid #000000 !important;
                font-weight: 800 !important;
                font-size: 10px !important;
            }
            .cancellation-date-print {
                color: #000000 !important;
                -webkit-text-fill-color: #000000 !important;
                font-weight: 900 !important;
                opacity: 1 !important;
            }
            .badge,
            span.badge,
            span[style*="background"] {
                background: transparent !important;
                background-color: transparent !important;
                color: #000000 !important;
                border: 1px solid #000000 !important;
                font-weight: 800 !important;
                font-size: 10px !important;
                padding: 1px 5px !important;
                box-shadow: none !important;
                display: inline-block !important;
            }
            @page {
                size: letter portrait;
                margin: 0.35in 0.4in;
            }
        }
    </style>
</head>
<body>

    <div class="actions-bar">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('production.logistic.delivery-receipt-list') }}" class="btn btn-outline-secondary btn-sm">
                <i class="las la-arrow-left me-1"></i> Back to Delivery Receipts
            </a>
            <span class="fw-bold text-muted">{{ $orders->count() }} Delivery Receipt{{ $orders->count() > 1 ? 's' : '' }} Selected</span>
        </div>
        <button onclick="window.print()" class="btn btn-danger btn-sm px-4 shadow-sm" style="background:#ff0000; border-color:#ff0000;">
            <i class="las la-print me-1"></i> {{ $orders->count() > 1 ? 'Print All Receipts' : 'Print Receipt' }}
        </button>
    </div>

    @foreach($orders as $order)
    @php
        $deliveryReceipt = $deliveryReceiptsMap->get($order->id);

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

        $isConsignment = in_array($order->type, ['area_consignment', 'area_sales_consignment']);
        $rawDrItems = ($deliveryReceipt && count($deliveryReceipt->items) > 0) ? $deliveryReceipt->items : ($order ? $order->items : []);
        $displayItems = collect($rawDrItems);

        $grossSubtotal = 0;
        $totalItemDiscounts = 0;

        $preparedByName = 'System';
        $approvedByName = 'Pending Approval';
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
        }

        $receivedByName = $bCompany?->company_name ?: ($order->customer_representative ?: ($order->customer->customer_name ?? ''));
        $dateFormatted = $order->dr_prepared_at ? \Carbon\Carbon::parse($order->dr_prepared_at)->format('M d, Y') : ($order->created_at ? $order->created_at->format('M d, Y') : date('M d, Y'));

        $soNumStr = strtolower($order->so_number ?? '');
        $cNameStr = strtolower($order->customer?->customer_name ?? '');
        $cRepStr = strtolower($order->customer_representative ?? '');
        $isNBS = str_contains($soNumStr, 'nbs') || 
                 str_contains($cNameStr, 'national book store') || 
                 str_contains($cNameStr, 'nbs') || 
                 str_contains($cRepStr, 'national book store') || 
                 str_contains($cRepStr, 'nbs');

        $poNumber = null;
        if ($isNBS) {
            $poNumber = $order->ref_number ?? ($order->po_number ?? null);
            if (empty($poNumber)) {
                if (preg_match('/(?:DR-)?SO-NBS-([^-]+)/i', $order->so_number ?? '', $m)) {
                    $poNumber = $m[1];
                }
            }
        }
    @endphp

    <div class="dr-box">
        <!-- Form Header -->
        <div class="form-header">
            <div class="company-info">
                <img src="{{ asset('images/claeritian_logo.png') }}" alt="Claretian Logo" class="company-logo-img" onerror="this.style.display='none'">
                <div>
                    <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                    <div class="company-address">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                    <div class="company-contact">Tel. No.: 921-3984</div>
                </div>
            </div>
            <div class="document-title">DELIVERY RECEIPT</div>
            <div class="text-center text-muted small fw-bold mt-1">NON-VAT REGISTERED</div>
            <div class="text-center extra-small text-muted fst-italic">"This document is not valid for claim of input taxes."</div>
        </div>

        <!-- Receipt Details Grid -->
        <table class="form-info-grid">
            <tr>
                <td class="label-col">DR No.:</td>
                <td class="val-col" style="width: 35%;">{{ $deliveryReceipt?->dr_number ?: 'DR-' . $order->so_number }}</td>
                <td class="label-col" style="padding-left: 15px;">Date:</td>
                <td class="val-col">{{ $dateFormatted }}</td>
            </tr>
            @if($isNBS && !empty($poNumber))
            <tr>
                <td class="label-col">Sales Order:</td>
                <td class="val-col">{{ $order->so_number }}</td>
                <td class="label-col" style="padding-left: 15px;">PO Number:</td>
                <td class="val-col">{{ $poNumber }}</td>
            </tr>
            <tr>
                <td class="label-col">Company:</td>
                <td class="val-col">{{ $displayCompanyName }}</td>
                <td class="label-col" style="padding-left: 15px;">Terms:</td>
                <td class="val-col">{{ $order->terms ?: 'Standard' }}</td>
            </tr>
            @else
            <tr>
                <td class="label-col">Sales Order:</td>
                <td class="val-col">{{ $order->so_number }}</td>
                <td class="label-col" style="padding-left: 15px;">Terms:</td>
                <td class="val-col">{{ $order->terms ?: 'Standard' }}</td>
            </tr>
            <tr>
                <td class="label-col">Company:</td>
                <td class="val-col" colspan="3">{{ $displayCompanyName }}</td>
            </tr>
            @endif
            <tr>
                <td class="label-col">Customer:</td>
                <td class="val-col">{{ $order->customer_representative ?: ($order->customer->customer_name ?? 'Unknown') }}</td>
                <td class="label-col" style="padding-left: 15px;">Contact:</td>
                <td class="val-col">{{ $order->customer_contact ?: ($order->customer?->mobile ?: ($order->customer?->main_phone ?: 'N/A')) }}</td>
            </tr>
            <tr>
                <td class="label-col">Address:</td>
                <td class="val-col" colspan="3">{{ $order->shipping_address ?: ($order->customer->shipping_address ?? $order->customer->billing_address ?? 'N/A') }}</td>
            </tr>
            @if($order->cancellation_date)
            <tr>
                <td class="label-col cancellation-date-print">Cancel Date:</td>
                <td class="val-col cancellation-date-print" colspan="3">{{ \Carbon\Carbon::parse($order->cancellation_date)->format('M d, Y') }}</td>
            </tr>
            @endif
            @if($order->remarks || $order->notes || ($deliveryReceipt->remarks ?? null))
            <tr>
                <td class="label-col">Remarks:</td>
                <td class="val-col" colspan="3">{{ $order->remarks ?: ($order->notes ?: ($deliveryReceipt->remarks ?? '')) }}</td>
            </tr>
            @endif
        </table>

        <!-- Delivery Receipt Items Table (NO AMOUNTS - QTY LANG NG TITLES AS IN PIC 3) -->
        <table class="receipt-table">
            <thead>
                <tr>
                    <th style="width: 12%; text-align: center;">QTY</th>
                    <th style="width: 12%; text-align: center;">U/M</th>
                    <th style="width: 56%;">Items / Particulars</th>
                    <th style="width: 20%; text-align: center;">ISBN</th>
                </tr>
            </thead>
            <tbody>
                @php $totalOrderQty = 0; @endphp
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
                        $displayQty = $isConsignment ? $pickQty : $qty;
                        if ($displayQty <= 0) $displayQty = (int)$iQty;
                        $totalOrderQty += $displayQty;

                        $unit = $item->unit ?: 'pc';
                    @endphp
                    @php
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

                        $isbn = $item->isbn ?? ($item->book?->isbn ?? ($item->bookIndex?->isbn ?? ($articleNo ?? '')));
                    @endphp
                    <tr>
                        <td style="text-align: center; font-weight: bold; font-size: 10pt;">{{ $displayQty }}</td>
                        <td style="text-align: center;">{{ $unit }}</td>
                        <td style="font-weight: 600;">
                            {{ $item->bookIndex?->display_name ?? ($item->bookIndex?->title ?? ($item->bookIndex?->custom_name ?? ($item->bookIndex?->book?->name ?? ($item->book?->name ?? ($item->bundle?->name ?? ($item->product?->name ?? ($item->item_name ?? ($item->product_name ?? 'Unknown Item')))))))) }}
                            @if($item->bookIndex || !empty($item->book_index_id))
                                <span class="badge border border-dark text-dark ms-1" style="font-size: 9px; padding: 1px 5px; vertical-align: middle; border-radius: 4px;">Index</span>
                            @endif
                            @if($isNBS && !empty($articleNo))
                                <div style="font-size: 11px; font-weight: bold; color: #000; margin-top: 2px;">
                                    Article #: {{ $articleNo }}
                                </div>
                            @endif
                        </td>
                        <td style="text-align: center; font-size: 9pt; color: #333;">{{ $isbn ?: '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-2 text-muted">No items found for this delivery receipt</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f8f9fa;">
                    <td style="text-align: center; font-weight: bold; font-size: 10pt;">{{ $totalOrderQty }}</td>
                    <td style="text-align: center; font-weight: bold;">pcs</td>
                    <td colspan="2" style="font-weight: bold; text-transform: uppercase; font-size: 0.85rem; padding-left: 8px;">Total Quantity Delivered</td>
                </tr>
            </tfoot>
        </table>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <label>Prepared by:</label>
                <div class="signature-name">{{ $preparedByName }}</div>
                <div class="signature-line-box">SIGNATURE OVER PRINTED NAME</div>
            </div>
            <div class="signature-box">
                <label>Approved by:</label>
                <div class="signature-name">{{ $approvedByName }}</div>
                <div class="signature-line-box">SIGNATURE OVER PRINTED NAME</div>
            </div>
            <div class="signature-box">
                <label>Received by:</label>
                <div class="signature-name">{{ $receivedByName }}</div>
                <div class="signature-line-box">SIGNATURE OVER PRINTED NAME</div>
            </div>
        </div>
    </div>
    @endforeach
    @if(request()->has('autoprint'))
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                window.print();
            }, 300);
        });
    </script>
    @endif
</body>
</html>
