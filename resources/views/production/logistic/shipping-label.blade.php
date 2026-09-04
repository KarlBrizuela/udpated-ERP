<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Label - SO #{{ $order->so_number }}</title>
    <style>
        @page {
            size: letter portrait;
            margin: 0 !important;
        }
        * {
            box-sizing: border-box;
        }
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            height: 100% !important;
            overflow: hidden !important;
            font-family: 'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #fff;
            color: #1a1a1a;
        }
        .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            width: 100vw;
            height: calc(100vh - 58px);
            max-width: 8.5in;
            max-height: 11in;
            margin: 0 auto;
            padding: 0.2in;
            gap: 0.15in;
            page-break-inside: avoid !important;
            page-break-after: avoid !important;
        }
        .label {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 0.2in;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: #fff;
            position: relative;
            overflow: hidden;
            height: 100%;
            page-break-inside: avoid !important;
        }
        .label::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #cc0000, #ff0000);
        }
        .header {
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .logo-img {
            width: 42px;
            height: auto;
            flex-shrink: 0;
        }
        .company-info-block {
            flex-grow: 1;
        }
        .company-name {
            font-size: 9.5pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin: 0;
            color: #cc0000;
            line-height: 1.15;
        }
        .company-sub {
            font-size: 7.5pt;
            margin: 1px 0;
            color: #666;
            line-height: 1.2;
        }
        .body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .section-title {
            font-size: 7.5pt;
            font-weight: 700;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .section-title::after {
            content: "";
            flex-grow: 1;
            height: 1px;
            background: #f0f0f0;
        }
        .buyer-name {
            font-size: 14pt;
            font-weight: 900;
            margin-bottom: 6px;
            text-transform: uppercase;
            line-height: 1.1;
            color: #000;
        }
        .buyer-address {
            font-weight: 700;
            margin-bottom: 8px;
            color: #111;
            flex-grow: 1;
            display: flex;
            align-items: center;
            word-break: break-word;
            overflow: hidden;
        }
        .contact-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            background: #f8f9fa;
            padding: 6px 10px;
            border-radius: 6px;
        }
        .buyer-contact {
            font-size: 10pt;
            font-weight: 700;
            color: #000;
        }
        .website-label {
            font-size: 7.5pt;
            font-weight: 600;
            color: #cc0000;
            text-transform: lowercase;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px dashed #eee;
        }
        .order-ref {
            font-size: 8.5pt;
            font-weight: 600;
            color: #666;
        }
        .no-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .no-text {
            font-size: 11pt;
            font-weight: 800;
            color: #333;
        }
        .no-box {
            border: 2px solid #333;
            border-radius: 4px;
            width: 55px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10pt;
            font-weight: 800;
        }
        
        @media print {
            @page {
                size: letter portrait;
                margin: 0 !important;
            }
            html, body {
                width: 215.9mm !important;
                height: 279.4mm !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
                background: none !important;
            }
            .no-print {
                display: none !important;
            }
            .container {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 215.9mm !important;
                height: 279.4mm !important;
                max-width: 215.9mm !important;
                max-height: 279.4mm !important;
                padding: 6mm !important;
                gap: 4mm !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
                margin: 0 !important;
            }
            .label {
                border-color: #000 !important;
                page-break-inside: avoid !important;
            }
        }
        
        .no-print-header {
            background: #cc0000;
            color: white;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: sans-serif;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .btn-print {
            background: white;
            color: #cc0000;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 12px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover {
            background: #f0f0f0;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="no-print-header no-print">
        <div style="display:flex; align-items:center; gap:10px;">
            <img src="{{ asset('images/claeritian_logo.png') }}" style="height:30px; background:white; padding:2px; border-radius:4px;">
            <strong>Shipping Label Preview - SO #{{ $order->so_number }}</strong>
        </div>
        <div style="display:flex; gap:10px;">
            @if($order->shipping_label_attachment)
                <a href="/storage/{{ $order->shipping_label_attachment }}" target="_blank" class="btn-print" style="background:#e3f2fd; color:#0d47a1;">View Uploaded Label File</a>
            @endif
            <button class="btn-print" onclick="window.print()">Print Labels Now</button>
        </div>
    </div>

        @php
            $finalAddr = trim($address ?? ($order->shipping_address ?: ($order->customer->shipping_address ?? ($order->customer->billing_address ?? ''))));
            $addrLen = strlen($finalAddr);
            if ($addrLen > 140) {
                $addrFontSize = '11.5pt';
                $addrLineHeight = '1.2';
            } elseif ($addrLen > 90) {
                $addrFontSize = '13.5pt';
                $addrLineHeight = '1.25';
            } else {
                $addrFontSize = '16pt';
                $addrLineHeight = '1.3';
            }
        @endphp
    <div class="container">
        @for($i = 0; $i < 4; $i++)
        <div class="label">
            <div class="header">
                <img src="{{ asset('images/claeritian_logo.png') }}" class="logo-img">
                <div class="company-info-block">
                    <p class="company-name">Claretian Communications Foundation, Inc.</p>
                    <p class="company-sub">8 Mayumi St., UP Village, Diliman, QC</p>
                    <p class="company-sub">Mobile: 0908 886 1897</p>
                </div>
            </div>
            <div class="body">
                <div class="section-title">Ship To</div>
                <div class="buyer-name">{{ $order->customer->customer_name ?? ($order->customer_representative ?: 'N/A') }}</div>
                <div class="buyer-address" style="font-size: {{ $addrFontSize }}; line-height: {{ $addrLineHeight }};">
                    {{ $finalAddr ?: 'N/A' }}
                </div>
                
                <div class="contact-row">
                    <div class="buyer-contact">
                        {{ $order->customer_contact ?: ($order->customer->mobile ?? ($order->customer->main_phone ?? 'N/A')) }}
                    </div>
                    <div class="website-label">https://claretianpublications.com/</div>
                </div>
            </div>
            <div class="footer">
                <div class="order-ref">
                    <strong>Ref:</strong> {{ $order->so_number }}
                    @if($order->cancellation_date)
                        <br><span style="color: #cc0000; font-weight: 700;">Cancel: {{ \Carbon\Carbon::parse($order->cancellation_date)->format('M d, Y') }}</span>
                    @endif
                    @if($order->remarks)
                        <br><small style="color:#555;">Remarks: {{ \Illuminate\Support\Str::limit($order->remarks, 30) }}</small>
                    @endif
                </div>
                <div class="no-group">
                    <div class="no-text">BOX</div>
                    <div class="no-box"></div>
                </div>
            </div>
        </div>
        @endfor
    </div>
</body>
</html>
