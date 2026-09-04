<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transmittal Slip - {{ $order->so_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 30px;
            height: 90vh; /* Approximate half sheet or full sheet? Image looks landscape-ish card */
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-text {
            font-weight: bold;
            font-size: 20px;
            text-transform: uppercase;
        }
        .address-text {
            font-size: 12px;
            margin-top: 5px;
        }
        .doc-title {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .details-label {
            width: 80px;
            display: inline-block;
        }
        .details-value {
            border-bottom: 1px solid #000;
            flex-grow: 1;
            padding-left: 5px;
        }
        .table-container {
            margin-top: 20px;
            border: 2px solid #000;
            height: 300px; /* Fixed height to mimic the card */
            display: flex;
        }
        .col-item {
            width: 25%;
            border-right: 2px solid #000;
            display: flex;
            flex-direction: column;
        }
        .col-desc {
            width: 50%;
            border-right: 2px solid #000;
            display: flex;
            flex-direction: column;
        }
        .col-due {
            width: 25%;
            display: flex;
            flex-direction: column;
        }
        .th {
            border-bottom: 2px solid #000;
            text-align: center;
            font-weight: bold;
            padding: 5px;
            text-transform: uppercase;
        }
        .td {
            padding: 5px;
            flex-grow: 1;
        }
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .sign-area {
            width: 45%;
        }
        .sign-line {
            border-bottom: 1px solid #000;
            margin-top: 30px;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
        }
        @media print {
            .print-btn { display: none; }
            .container { border: none; padding: 0; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print</button>

    <div class="container">
        <!-- Header with Logo Placeholder -->
        <div class="header">
            <!-- Ideally would use an image here if available, matching the logo in the image -->
            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                 <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 50px;"> 
                <div>
                    <div class="logo-text">Claretian Communications Foundation, Inc.</div>
                    <div class="address-text">#8 Mayumi Street, U.P. P.O. Box 4, Quezon City 1101 • Tel. 921-3984 • Fax: 921-7429</div>
                </div>
            </div>
        </div>

        <div class="doc-title">Transmittal Slip</div>

        <div class="details-row">
            <span class="details-label">To:</span>
            <span class="details-value">{{ $order->customer->customer_name ?? 'N/A' }}</span>
            <span class="details-label" style="text-align: right; width: 50px; margin-right: 10px;">Date:</span>
            <span class="details-value" style="width: 150px; flex-grow: 0;">{{ now()->format('M d, Y') }}</span>
        </div>
        <div class="details-row">
            <span class="details-label">Address:</span>
            <span class="details-value">{{ $order->shipping_address ?? ($order->billing_address ?? ($order->customer->address ?? 'N/A')) }}</span>
        </div>
        <div class="details-row">
            <span class="details-label">&nbsp;</span>
            <span class="details-value"></span>
        </div>

        <div class="table-container">
            <div class="col-item">
                <div class="th">Item</div>
                <div class="td">
                    @foreach($order->items->filter(fn($i) => (float)($i->quantity ?? 0) > 0) as $item)
                    {{ $item->book?->item_code ?? $item->book?->sku ?? 'Item' }}<br>
                    @endforeach
                </div>
            </div>
            <div class="col-desc">
                <div class="th">Description</div>
                <div class="td">
                    @foreach($order->items->filter(fn($i) => (float)($i->quantity ?? 0) > 0) as $item)
                    {{ $item->book?->name ?? $item->bundle?->name ?? 'Unknown Item' }} ({{ $item->quantity }} pcs)<br>
                    @endforeach
                </div>
            </div>
            <div class="col-due">
                <div class="th">Due Date</div>
                <div class="td">
                    <!-- Placeholder or specific due date logic -->
                    {{ $order->payment_due_date ?? 'N/A' }}
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="sign-area">
                <div>Released by:</div>
                <div class="sign-line"></div>
            </div>
            <div class="sign-area" style="display: flex; gap: 10px;">
                <div style="flex-grow: 1;">
                    <div>Received by:</div>
                    <div class="sign-line"></div>
                </div>
                <div style="width: 80px;">
                    <div>Date:</div>
                    <div class="sign-line"></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
