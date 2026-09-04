<x-app-layout :title="$title" :sidebar="$sidebar">
    @push('styles')
    <style>
        /* Consignment Receipt Blue Theme */
        .receipt-paper {
            background: #e3f2fd; /* Light blue paper color */
            padding: 3rem;
            border-radius: 4px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            max-width: 950px;
            margin: 2rem auto;
            color: #0d47a1; /* Deep blue text */
            font-family: 'Courier New', Courier, monospace; /* Typewriter feel */
            position: relative;
            border: 1px solid #bbdefb;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(13, 71, 161, 0.2);
            padding-bottom: 1rem;
        }

        .receipt-header h2 {
            font-weight: 800;
            margin-bottom: 0.25rem;
            font-size: 1.5rem;
            letter-spacing: 1px;
        }

        .receipt-header p {
            margin-bottom: 0;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .doc-title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
        }

        .doc-title {
            font-size: 1.4rem;
            font-weight: 700;
            text-decoration: underline;
        }

        .dr-number {
            font-size: 1.4rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dr-label { font-size: 1.1rem; }
        .dr-val { color: #d32f2f; } /* Red for the actual number as per many DRs, but can be blue */

        .info-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 3rem;
            margin-top: 2rem;
            font-size: 0.95rem;
        }

        .info-item {
            display: flex;
            margin-bottom: 0.5rem;
            border-bottom: 1px dotted rgba(13, 71, 161, 0.4);
        }

        .info-label {
            min-width: 100px;
            font-weight: 700;
        }

        .info-value {
            flex: 1;
            font-weight: 600;
        }

        .items-table {
            width: 100%;
            margin-top: 2rem;
            border-collapse: collapse;
        }

        .items-table th {
            border-top: 2px solid #0d47a1;
            border-bottom: 2px solid #0d47a1;
            padding: 0.75rem 0.5rem;
            text-align: left;
            font-size: 0.9rem;
            font-weight: 800;
        }

        .items-table td {
            padding: 0.75rem 0.5rem;
            border-bottom: 1px dotted rgba(13, 71, 161, 0.2);
            font-size: 0.9rem;
            vertical-align: top;
        }

        .totals-section {
            margin-top: 2rem;
            display: flex;
            justify-content: flex-end;
        }

        .totals-box {
            width: 300px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0;
            font-weight: 700;
        }

        .final-total {
            margin-top: 1rem;
            font-size: 1.2rem;
            border-top: 2px solid #0d47a1;
            padding-top: 0.5rem;
        }

        .footer-note {
            margin-top: 4rem;
            text-align: center;
            font-size: 0.8rem;
            font-style: italic;
            font-weight: 700;
            opacity: 0.8;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 2rem;
            margin-top: 4rem;
            text-align: center;
        }

        .sig-box {
            border-top: 1px solid #0d47a1;
            padding-top: 0.5rem;
            font-size: 0.85rem;
            font-weight: 700;
        }

        @media print {
            .nav-header, .sidebar, .btn-print, .modern-nav-menu, .header { display: none !important; }
            .content-body { margin: 0 !important; padding: 0 !important; }
            body { background: white; }
            .receipt-paper { box-shadow: none; border: none; margin: 0; width: 100%; max-width: 100%; background: #e3f2fd !important; -webkit-print-color-adjust: exact; }
        }
    </style>
    @endpush

    <div class="mb-4 text-end">
        <button class="btn btn-primary btn-print px-4" onclick="window.print()">
            <i class="las la-print me-2"></i>Print Receipt
        </button>
    </div>

    <div class="receipt-paper">
        <div class="receipt-header">
            <h2>CLARETIAN COMMUNICATIONS FOUNDATION, INC.</h2>
            <p>8 Mayumi St., U.P. P.O. Box 4 Quezon City 1101</p>
            <p>Tel. (02) 921-3984 Fax (02) 921-6205</p>
            
            <div class="doc-title-row">
                <div class="doc-title">Consignment Delivery Receipt</div>
                <div class="dr-number">
                    <span class="dr-label">D.R. No.</span>
                    <span class="dr-val">{{ $order->dr_number ?? str_replace('SO-NBS-', '', $order->so_number) }}</span>
                </div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-col">
                <div class="info-item">
                    <span class="info-label">Customer:</span>
                    <span class="info-value">{{ $order->customer->customer_name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Address:</span>
                    <span class="info-value">{{ $order->shipping_address ?? $order->customer->shipping_address }}</span>
                </div>
            </div>
            <div class="info-col">
                <div class="info-item">
                    <span class="info-label">Date:</span>
                    <span class="info-value">{{ $order->created_at->format('m/d/Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Terms:</span>
                    <span class="info-value">Consignment</span>
                </div>
                <div class="info-item">
                    <span class="info-label">S.O. No.</span>
                    <span class="info-value">{{ $order->so_number }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">P.O. No.</span>
                    <span class="info-value">{{ $order->ref_number }}</span>
                </div>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 80px;">QTY</th>
                    <th>DESCRIPTION</th>
                    <th style="width: 120px;">ARTICLE</th>
                    <th style="width: 120px;">UNIT PRICE</th>
                    <th style="width: 150px;">NBS BARCODE</th>
                    <th style="width: 120px;">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ (float)$item->quantity }}</td>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->bookIndex->article ?? $item->book->article ?? $item->book->sku ?? '---' }}</td>
                    <td>{{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->bookIndex->nbs_barcode ?? $item->book->nbs_barcode ?? '---' }}</td>
                    <td>{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section">
            <div class="totals-box">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span>{{ number_format($order->total_amount, 2) }}</span>
                </div>
                @if($order->discount_percentage > 0)
                <div class="total-row">
                    <span>Discount {{ (float)$order->discount_percentage }}%</span>
                    <span>-{{ number_format($order->total_amount * ($order->discount_percentage / 100), 2) }}</span>
                </div>
                @endif
                <div class="total-row final-total">
                    <span>TOTAL:</span>
                    <span>PHP {{ number_format($order->total_amount * (1 - ($order->discount_percentage / 100)), 2) }}</span>
                </div>
            </div>
        </div>

        <div class="footer-note">
            "THIS DOCUMENT IS NOT VALID FOR CLAIM OF INPUT TAXES"
            <br>
            This Delivery Receipt shall be valid for Five (5) years from the date of AR.
        </div>

        <div class="signature-grid">
            <div class="sig-box">Prepared by: {{ $order->preparedBy->name ?? '---' }}</div>
            <div class="sig-box">Approved by:</div>
            <div class="sig-box">Received by: Signature over Printed Name</div>
        </div>
    </div>
</x-app-layout>
