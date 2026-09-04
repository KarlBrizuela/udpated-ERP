<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Print Sales Invoices</title>
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

        .invoice-box {
            background: #fff;
            max-width: 8.5in;
            min-height: auto;
            margin: 0 auto;
            padding: 0.35in 0.45in;
            border: 1px solid #ccc;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-after: always;
            margin-bottom: 20px;
        }

        .invoice-box:last-child {
            page-break-after: avoid;
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
            color: #333;
            margin-top: 2px;
        }

        .company-address, .company-contact {
            font-size: 8pt;
            color: #222;
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
            color: #cc0000;
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
            border-bottom: 2px solid #000;
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
            border-bottom: 2px solid #000;
            padding: 0 12px;
            font-size: 12pt;
            color: #cc0000;
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

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .invoice-box {
                border: none;
                box-shadow: none;
                padding: 0;
                width: 100%;
                max-width: 100%;
                margin-bottom: 0;
            }
            .actions-bar {
                display: none !important;
            }
            @page {
                size: letter portrait;
                margin: 0.4in;
            }
        }
    </style>
</head>
<body>

    <div class="actions-bar">
        <div class="d-flex align-items-center gap-3">
            <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
                <i class="las la-arrow-left me-1"></i> Back
            </a>
            <span class="fw-bold text-muted">{{ $orders->count() }} Invoice(s)</span>
        </div>
        <button onclick="window.print()" class="btn btn-danger btn-sm px-4 shadow-sm" style="background:#ff0000;">
            <i class="las la-print me-1"></i> Print All Invoices
        </button>
    </div>

    @foreach($orders as $order)
    <div class="invoice-box">
        <div>
            @php
                $activeInvoice = null;
                if (in_array($order->type, ['area_consignment', 'area_sales_consignment'])) {
                    $activeInvoice = \App\Models\SalesInvoice::where('so_id', $order->id)->where('status', '!=', 'cancelled')->latest()->first();
                }
                $siNoDisplay = $order->si_number ?: ($activeInvoice->si_number ?? $order->so_number);
            @endphp
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
                    <div class="doc-no">No. <span>{{ $siNoDisplay }}</span></div>
                    <div class="doc-title">Sales - Invoice</div>
                </div>
            </div>

            <!-- Customer & Transaction Details -->
            @php

                if ($activeInvoice) {
                    $itemsToPrint = $activeInvoice->items->filter(fn($i) => (float)($i->quantity ?? 0) > 0);
                    $totalSalesAmount = (float) $activeInvoice->total_amount;
                } else {
                    $itemsToPrint = $order->items ? $order->items->filter(fn($i) => (float)($i->quantity ?? 0) > 0) : collect();
                    $totalSalesAmount = (float) $order->total_amount;
                }

                $isCash = in_array($order->payment_method, ['cash', 'gcash', 'paymaya', 'card', 'bank', 'check']) 
                          || in_array($order->type, ['calculator_pos', 'ecom_direct', 'paid']);
                $custName = $order->customer?->customer_name ?: 'Cash Customer';
                $custAddress = $order->billing_address ?: ($order->shipping_address ?: ($order->customer?->billing_address ?? 'N/A'));
                $custTin = $order->customer?->tin ?: 'N/A';
                $termsVal = $order->terms ?: ($order->payment_method ? strtoupper($order->payment_method) : 'CASH');
                $orderDate = $order->created_at ? $order->created_at->format('m/d/Y') : date('m/d/Y');
                $dueDate = $order->due_date ? \Carbon\Carbon::parse($order->due_date)->format('m/d/Y') : '-';

                $itemsSubtotal = 0;
                foreach ($itemsToPrint as $item) {
                    $qty = (float) $item->quantity;
                    $price = (float) ($item->unit_price ?? $item->price);
                    $itemsSubtotal += (float) ($item->amount ?? ($item->subtotal !== null ? $item->subtotal : ($qty * $price)));
                }

                $discount = (float) ($order->discount_amount ?? 0);
                if ($discount == 0 && (float) ($order->discount_percentage ?? 0) > 0) {
                    $discount = $itemsSubtotal * ((float) $order->discount_percentage / 100);
                }

                $freight = (float) ($order->freight_charges ?? 0);
                $wht = (float) ($order->withholding_tax_amount ?? 0);

                if ($discount > 0 || $freight > 0) {
                    $calculatedTotalSales = max(0, $itemsSubtotal - $discount + $freight);
                } else {
                    $calculatedTotalSales = $totalSalesAmount > 0 ? $totalSalesAmount : $itemsSubtotal;
                }

                $totalAmountDue = max(0, $calculatedTotalSales - $wht);
            @endphp

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
                            $desc = $item->book?->name ?? ($item->product_name ?? 'Product Item');
                            $qty = (float) $item->quantity;
                            $price = (float) ($item->unit_price ?? $item->price);
                            $subtotal = (float) ($item->amount ?? ($item->subtotal !== null ? $item->subtotal : ($qty * $price)));
                        @endphp
                        <tr>
                            <td style="text-align: center; font-weight: bold;">{{ $qty }}</td>
                            <td style="font-weight: 600;">{{ $desc }}</td>
                            <td style="text-align: center;">{{ $item->area ?? '-' }}</td>
                            <td style="text-align: right;">₱{{ number_format($price, 2) }}</td>
                            <td style="text-align: right; font-weight: bold;">₱{{ number_format($subtotal, 2) }}</td>
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
                        <span>CASH</span>
                    </div>
                    <div class="custom-cb">
                        <span class="cb-box">{{ !$isCash ? '✓' : '' }}</span>
                        <span>CHARGE</span>
                    </div>
                </div>
                <div class="totals-block text-end" style="display: flex; flex-direction: column; align-items: flex-end;">
                    <div class="subtotal-line" style="font-size: 8.5pt; font-weight: bold; margin-bottom: 3px;">
                        <span class="total-label">SUBTOTAL: </span><span style="padding: 0 8px; min-width: 115px; display: inline-block;">₱{{ number_format($itemsSubtotal, 2) }}</span>
                    </div>
                    @if($freight > 0)
                    <div class="freight-line" style="font-size: 8.5pt; font-weight: bold; margin-bottom: 3px;">
                        <span class="total-label">FREIGHT: </span><span style="padding: 0 8px; min-width: 115px; display: inline-block;">₱{{ number_format($freight, 2) }}</span>
                    </div>
                    @endif
                    @if($discount > 0)
                    <div class="discount-line" style="font-size: 8.5pt; font-weight: bold; margin-bottom: 3px;">
                        <span class="total-label">DISCOUNT: </span><span style="padding: 0 8px; min-width: 115px; display: inline-block;">-₱{{ number_format($discount, 2) }}</span>
                    </div>
                    @endif
                    <div class="withholding-tax-line" style="font-size: 8.5pt; font-weight: bold; margin-bottom: 3px;">
                        <span class="total-label">LESS: WITHHOLDING TAX: </span><span style="padding: 0 8px; min-width: 115px; display: inline-block;">{{ $wht > 0 ? '-₱' . number_format($wht, 2) : '' }}</span>
                    </div>
                    <div class="total-sales-line" style="font-size: 8.5pt; font-weight: bold; margin-bottom: 3px;">
                        <span class="total-label">TOTAL SALES: </span><span style="padding: 0 8px; min-width: 115px; display: inline-block;">₱{{ number_format($calculatedTotalSales, 2) }}</span>
                    </div>
                    <div class="total-amount-due-line" style="font-size: 8.5pt; font-weight: bold; margin-bottom: 3px;">
                        <span class="total-label">TOTAL AMOUNT DUE: </span><span style="padding: 0 8px; min-width: 115px; display: inline-block;">{{ $wht > 0 ? '₱' . number_format($totalAmountDue, 2) : '' }}</span>
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
                    <span>RCBC - SA# 1-191-48135-6</span> &nbsp;&nbsp;&nbsp; <span>Metrobank SA# 186-3-18617805-0</span><br>
                    <span>BDO - SA# 3640009449</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>BPI SA# 1993-0734-11</span>
                </div>
            </div>

            <!-- Signatories -->
            <div class="signatories-row">
                <div class="sig-col">
                    <div>Prepared by:</div>
                    <div class="sig-line">{{ $order->preparedBy?->name ?? '' }}</div>
                </div>
                <div class="sig-col">
                    <div>Approved by:</div>
                    <div class="sig-line">{{ $order->mktApprovedBy?->name ?? ($order->prodApprovedBy?->name ?? '') }}</div>
                </div>
                <div class="sig-col">
                    <div>Received by:</div>
                    <div class="sig-line"></div>
                </div>
            </div>

            <!-- Footer Notice -->
            <div class="footer-notice">
                <div>
                    100 Pads (50x4) 02251-57250<br>
                    Looseleaf Permit No. LLAR-099-1022-00083
                </div>
                <div style="font-weight: bold; font-size: 8pt; color: #000;">
                    *THIS DOCUMENT IS NOT VALID FOR CLAIM OF INPUT TAXES*
                </div>
            </div>
        </div>
    </div>
    @endforeach

</body>
</html>
