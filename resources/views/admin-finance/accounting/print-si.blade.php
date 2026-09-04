<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Invoice - {{ $order->so_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            padding: 10px;
        }

        .invoice-container {
            background: #fff;
            max-width: 8.5in;
            min-height: 10.5in;
            margin: 0 auto;
            padding: 0.4in;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            page-break-after: avoid;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .invoice-header {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 0.6rem;
            padding-bottom: 0.6rem;
            border-bottom: 2px solid #ff0000;
        }

        .company-logo {
            width: 50px;
            height: 50px;
            background: #ff0000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.8rem;
            font-weight: bold;
            flex-shrink: 0;
        }

        .company-info h1 {
            font-size: 0.85rem;
            font-weight: 700;
            color: #333;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .company-info p {
            margin: 0.1rem 0;
            color: #666;
            font-size: 0.7rem;
        }

        .invoice-title {
            text-align: center;
            margin: 0.4rem 0;
        }

        .invoice-title h2 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            letter-spacing: 1px;
            margin-bottom: 0.2rem;
        }

        .invoice-title p {
            color: #666;
            font-size: 0.7rem;
            margin: 0;
        }

        .invoice-number {
            text-align: center;
            font-size: 1rem;
            font-weight: 700;
            color: #ff0000;
            margin: 0.4rem 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 0.4rem;
        }

        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.8rem;
            margin-bottom: 0.8rem;
        }

        .detail-section {
            background: #f8f9fa;
            padding: 0.6rem;
            border-radius: 4px;
        }

        .detail-section h5 {
            font-weight: 700;
            color: #333;
            margin-bottom: 0.4rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .detail-item {
            margin-bottom: 0.35rem;
            line-height: 1.2;
        }

        .detail-item label {
            font-weight: 600;
            color: #333;
            display: block;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .detail-item p {
            color: #555;
            margin: 0.1rem 0 0 0;
            font-size: 0.75rem;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0.6rem 0;
            font-size: 0.75rem;
            flex-grow: 1;
        }

        .items-table thead {
            background: #ff0000;
            color: #fff;
        }

        .items-table th {
            padding: 0.4rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #ff0000;
        }

        .items-table td {
            padding: 0.3rem 0.4rem;
            border: 1px solid #ddd;
            color: #333;
            font-size: 0.73rem;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table tfoot tr {
            background-color: #f5f5f5;
            font-weight: 600;
        }

        .items-table tfoot th,
        .items-table tfoot td {
            padding: 0.4rem;
            border: 1px solid #ddd;
            text-align: right;
            font-size: 0.73rem;
        }

        .tax-notice {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 0.5rem;
            border-radius: 3px;
            margin: 0.4rem 0;
            font-size: 0.7rem;
            color: #333;
            text-align: center;
        }

        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 0.6rem;
            padding-top: 0.6rem;
            border-top: 1px solid #e0e0e0;
        }

        .signature-block {
            text-align: center;
        }

        .signature-block h6 {
            font-weight: 700;
            color: #333;
            margin-bottom: 1.2rem;
            font-size: 0.7rem;
            text-transform: uppercase;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-bottom: 0.3rem;
            height: 25px;
        }

        .signature-name {
            font-size: 0.65rem;
            color: #666;
            font-weight: 600;
        }

        .print-button {
            text-align: center;
            margin-top: 0.6rem;
            padding-top: 0.6rem;
            border-top: 1px solid #e0e0e0;
        }

        .print-button button {
            background: #ff0000;
            color: #fff;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 3px;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .print-button button:hover {
            background: #ff3333;
        }

        @media print {
            .no-print,
            .actions-bar,
            .btn,
            button,
            .print-button {
                display: none !important;
                visibility: hidden !important;
            }

            body {
                background-color: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .invoice-container {
                box-shadow: none !important;
                padding: 0.3in !important;
                margin: 0 auto !important;
                max-width: 100% !important;
                height: auto !important;
                page-break-after: avoid;
            }

            @page {
                margin: 0.3in;
                size: portrait;
            }
        }

        body.preprinted-mode .invoice-container {
            padding-top: 0.95in !important;
        }

        body.preprinted-mode .detail-section {
            background: transparent !important;
            border: none !important;
        }

        /* Adjust right column (Date, Terms, Due Date) vertical alignment on pre-printed form */
        body.preprinted-mode .info-grid tr td:nth-child(3),
        body.preprinted-mode .info-grid tr td:nth-child(4) {
            padding-top: 18px !important;
        }

        /* Expand hidden table header height so line items start below the pre-printed table header */
        body.preprinted-mode .items-table thead tr {
            height: 68px !important;
        }

        /* Signatories alignment */
        body.preprinted-mode .signature-line {
            border-color: transparent !important;
            margin-top: 26px !important;
        }

        body.preprinted-mode .invoice-header,
        body.preprinted-mode .invoice-title,
        body.preprinted-mode .invoice-number,
        body.preprinted-mode .detail-section h5,
        body.preprinted-mode .detail-item label,
        body.preprinted-mode .items-table thead,
        body.preprinted-mode .tax-notice,
        body.preprinted-mode .signature-block h6,
        body.preprinted-mode .signature-block small {
            visibility: hidden !important;
        }

        body.preprinted-mode .items-table th,
        body.preprinted-mode .items-table td,
        body.preprinted-mode .signature-section {
            border-color: transparent !important;
        }

        /* Half Page Mode for print-si */
        body.half-page-mode .invoice-container {
            max-width: 8.5in !important;
            height: auto !important;
            min-height: 4.8in !important;
            max-height: none !important;
            padding: 0.2in 0.25in !important;
            box-sizing: border-box !important;
        }
        body.half-page-mode .invoice-header {
            margin-bottom: 0.3rem !important;
            padding-bottom: 0.3rem !important;
        }
        body.half-page-mode .company-logo {
            width: 35px !important;
            height: 35px !important;
            font-size: 1.2rem !important;
        }
        body.half-page-mode .company-info h1 {
            font-size: 0.75rem !important;
        }
        body.half-page-mode .company-info p {
            font-size: 0.65rem !important;
        }
        body.half-page-mode .invoice-title {
            margin: 0.2rem 0 !important;
        }
        body.half-page-mode .invoice-title h2 {
            font-size: 1rem !important;
        }
        body.half-page-mode .invoice-number {
            font-size: 0.85rem !important;
            margin: 0.2rem 0 !important;
            padding-bottom: 0.2rem !important;
        }
        body.half-page-mode .invoice-details {
            gap: 0.4rem !important;
            margin-bottom: 0.4rem !important;
        }
        body.half-page-mode .detail-section {
            padding: 0.4rem !important;
        }
        body.half-page-mode .items-table th,
        body.half-page-mode .items-table td {
            padding: 0.2rem 0.4rem !important;
            font-size: 0.75rem !important;
        }
        body.half-page-mode .signature-section {
            margin-top: 0.4rem !important;
            padding-top: 0.4rem !important;
        }
    </style>
</head>
<body>
    <div class="actions-bar no-print p-3 bg-white mb-3 d-flex justify-content-between align-items-center shadow-sm rounded">
        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
            <i class="las la-arrow-left me-1"></i> Back
        </a>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <label class="form-label fw-bold small text-dark mb-0 text-nowrap"><i class="las la-file-alt me-1"></i>Paper Size:</label>
                <select class="form-select form-select-sm fw-bold border-secondary" id="paperSizeSelect" onchange="changePaperSize(this.value)" style="width: 150px; cursor: pointer; height: 32px; font-size: 0.8rem;">
                    <option value="whole" selected>1 Whole (Full Page)</option>
                    <option value="half">1/2 (Half Page)</option>
                </select>
            </div>
            <div class="form-check form-switch mb-0 ms-2">
                <input class="form-check-input" type="checkbox" id="preprintedToggle" onchange="document.body.classList.toggle('preprinted-mode', this.checked)">
                <label class="form-check-label fw-bold small text-dark" for="preprintedToggle">Overlay on Pre-printed Paper Form</label>
            </div>
            <button onclick="window.print()" class="btn btn-danger btn-sm px-4 shadow-sm" style="background:#ff0000; border: none;">
                <i class="las la-print me-1"></i> Print Sales Invoice
            </button>
        </div>
    </div>
    <div class="invoice-container single-page-invoice">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-logo">C</div>
            <div class="company-info">
                <h1>Claretian Communications Foundation Inc.</h1>
                <p>8 Mayumi St., UP Village, Diliman, Quezon City</p>
                <p>Tel. No.: 921-3984</p>
            </div>
        </div>

        <!-- Invoice Title -->
        <div class="invoice-title">
            <h2>SALES INVOICE</h2>
            <p>NON-VAT REGISTERED</p>
            <p style="font-size: 0.85rem; font-style: italic; color: #999;">
                "This document is not valid for claim of input taxes."
            </p>
        </div>

        <!-- Invoice Number -->
        <div class="invoice-number">
            SI-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
        </div>

        <!-- Details Section -->
        <div class="invoice-details">
            <div class="detail-section">
                <h5>Customer Information</h5>
                <div class="detail-item">
                    <label>Sold To:</label>
                    <p>{{ $order->customer->customer_name ?? 'N/A' }}</p>
                </div>
                <div class="detail-item">
                    <label>Address:</label>
                    <p>{{ $order->billing_address ?? ($order->customer->address ?? 'N/A') }}</p>
                </div>
                <div class="detail-item">
                    <label>TIN:</label>
                    <p>{{ $order->customer->tin ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="detail-section">
                <h5>Transaction Details</h5>
                <div class="detail-item">
                    <label>Date:</label>
                    <p>{{ now()->format('F d, Y') }}</p>
                </div>
                <div class="detail-item">
                    <label>Terms:</label>
                    <p>{{ $order->terms ?? 'N/A' }}</p>
                </div>
                <div class="detail-item">
                    <label>Reference:</label>
                    <p>SO #{{ $order->so_number }}</p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 80px;">QTY</th>
                    <th>DESCRIPTION</th>
                    <th style="width: 120px;">AREA</th>
                    <th style="width: 140px; text-align: right;">UNIT PRICE</th>
                    <th style="width: 140px; text-align: right;">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->items->filter(fn($i) => (float)($i->quantity ?? 0) > 0) as $item)
                <tr>
                    <td class="text-center">{{ $item->quantity }} {{ $item->unit ?? 'pcs' }}</td>
                    <td>{{ $item->item_name ?? ($item->product?->name ?? ($item->book?->name ?? ($item->bundle?->name ?? 'Unknown Product'))) }}</td>
                    <td>{{ $item->area ?? '-' }}</td>
                    <td class="text-right">₱{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">₱{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">No items</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                @php
                    $itemsSubtotal = $order->items->sum('subtotal');
                    $discountAmount = $order->discount_amount ?? 0;
                    $discountPercentage = $order->discount_percentage ?? 0;
                    $freightCharges = $order->freight_charges ?? 0;
                    $serviceFee = $order->freight_option === 'freight_collect' ? 50 : 0;
                @endphp
                <tr>
                    <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                    <td class="text-right">₱{{ number_format($itemsSubtotal, 2) }}</td>
                </tr>
                @if($discountAmount > 0)
                <tr>
                    <td colspan="4" class="text-right text-danger">
                        <strong>
                            Discount
                            @if($discountPercentage > 0)
                                ({{ (float)$discountPercentage }}%)
                            @endif:
                        </strong>
                    </td>
                    <td class="text-right text-danger">- ₱{{ number_format($discountAmount, 2) }}</td>
                </tr>
                @endif
                @if($freightCharges > 0)
                <tr>
                    <td colspan="4" class="text-right"><strong>Freight Charges:</strong></td>
                    <td class="text-right">₱{{ number_format($freightCharges, 2) }}</td>
                </tr>
                @endif
                @if($serviceFee > 0)
                <tr>
                    <td colspan="4" class="text-right"><strong>Service Fee:</strong></td>
                    <td class="text-right">₱{{ number_format($serviceFee, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <th colspan="4" class="text-right">TOTAL AMOUNT DUE</th>
                    <th class="text-right">₱{{ number_format($order->total_amount, 2) }}</th>
                </tr>
            </tfoot>
        </table>

        <!-- Tax Notice -->
        <div class="tax-notice">
            <strong>NON-VAT REGISTERED ESTABLISHMENT</strong><br>
            This document is not valid for claim of input taxes.
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-block">
                <h6>Prepared By</h6>
                <div class="signature-line"></div>
                <div class="signature-name">
                    {{ $order->siPreparedBy->name ?? 'Staff Name' }}
                </div>
                <small>Accounting Staff</small>
            </div>
            <div class="signature-block">
                <h6>Manager Signature</h6>
                <div class="signature-line"></div>
                <div class="signature-name">
                    {{ $order->signedBy->name ?? 'Manager Name' }}
                </div>
                <small>Admin & Finance Manager</small>
            </div>
        </div>

        <!-- Print Button -->
        <div class="print-button">
            <button type="button" onclick="window.print()">
                <i class="las la-print"></i> Print Sales Invoice
            </button>
        </div>
    </div>

    <script>
        function changePaperSize(size) {
            if (size === 'half') {
                document.body.classList.add('half-page-mode');
                document.body.classList.remove('whole-page-mode');
            } else {
                document.body.classList.remove('half-page-mode');
                document.body.classList.add('whole-page-mode');
            }
        }
    </script>
</body>
</html>
