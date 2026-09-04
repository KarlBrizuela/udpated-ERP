<x-app-layout :title="'Purchase Order Details'" :sidebar="'production'">
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card order-form">
                <div class="form-header">
                    <div class="company-info">
                        <div class="company-logo">C</div>
                        <div class="company-details">
                            <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION, INC.</div>
                            <div class="company-address">8 Mayumi St., UP Village, Diliman, Quezon City 1128</div>
                            <div class="company-contact">Non-Vat Reg. TIN: 000-395-713-000</div>
                            <div class="company-contact">Telephone: (02) 921-3984 | Fax: (02) 921-6205</div>
                        </div>
                    </div>
                    <div class="document-title">PURCHASE ORDER</div>
                </div>

                <div class="customer-section">
                    <div class="customer-details">
                        <h5>Vendor Information</h5>
                        <p><strong>Vendor:</strong> {{ $po->supplier->company_name }}</p>
                        <p><strong>Contact Person:</strong> {{ $po->supplier->contact_person }}</p>
                        <p><strong>Address:</strong> {{ $po->supplier->address ?? 'N/A' }}</p>
                    </div>
                    <div class="order-details">
                        <h5>Order Information</h5>
                        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($po->date)->format('F d, Y') }}</p>
                        <p><strong>P.O. No.:</strong> {{ $po->po_number }}</p>
                        <p><strong>Terms:</strong> {{ $po->terms ?? 'N/A' }}</p>
                        <p><strong>Invoice No.:</strong> {{ $po->invoice_number ?? 'N/A' }}</p>
                        <p><strong>Status:</strong> <span class="badge badge-primary text-capitalize">{{ str_replace('_', ' ', $po->status) }}</span></p>
                        @if($po->source === 'ford')
                        <p><strong>Source:</strong> <span class="badge badge-warning">FORD Division</span></p>
                        @endif
                    </div>
                </div>

                @if($po->source === 'ford')
                <table class="table table-bordered mt-4">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th style="width: 80px;">LANG</th>
                            <th style="width: 60px;">FT</th>
                            <th>DESCRIPTION</th>
                            <th style="width: 80px;">QTY</th>
                            <th style="width: 80px;">REC'D</th>
                            <th style="width: 80px;">REM.</th>
                            <th style="width: 130px; text-align: right;">UNIT PRICE ({{ $po->currency_label }})</th>
                            <th style="width: 130px; text-align: right;">AMOUNT</th>
                            <th style="width: 100px;">BINDINGS</th>
                            <th style="width: 120px;">REMARKS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($po->items as $item)
                        @php $remaining = $item->quantity - $item->received_quantity; @endphp
                        <tr>
                            <td>{{ $item->language }}</td>
                            <td class="text-center">{{ $item->ft }}</td>
                            <td><strong>{{ $item->description }}</strong></td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-center text-success fw-bold">{{ $item->received_quantity }}</td>
                            <td class="text-center {{ $remaining > 0 ? 'text-danger fw-bold' : 'text-success' }}">{{ $remaining }}</td>
                            <td class="text-end">{{ $po->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end">{{ $po->currency_symbol }}{{ number_format($item->total_amount, 2) }}</td>
                            <td>{{ $item->bindings }}</td>
                            <td>{{ $item->item_remarks }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" style="text-align: right; font-weight: 600;">TOTAL:</td>
                            <td style="text-align: right; font-weight: 600;">{{ $po->currency_symbol }}{{ number_format($po->total_amount, 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
                @else
                <table class="table table-bordered mt-4">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th style="width: 80px;">ORD. QTY</th>
                            <th style="width: 80px;">REC'D</th>
                            <th style="width: 80px;">REM.</th>
                            <th>PRODUCT / DESCRIPTION</th>
                            <th style="width: 140px;">ISBN</th>
                            <th style="width: 140px; text-align: right;">UNIT PRICE</th>
                            <th style="width: 140px; text-align: right;">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($po->items as $item)
                        @php $remaining = max(0, $item->quantity - $item->received_quantity); @endphp
                        <tr>
                            <td class="text-center fw-bold">{{ $item->quantity }}</td>
                            <td class="text-center text-success fw-bold">{{ $item->received_quantity }}</td>
                            <td class="text-center {{ $remaining > 0 ? 'text-danger fw-bold' : 'text-success' }}">{{ $remaining }}</td>
                            <td>
                                <strong>{{ $item->product ? $item->product->name : $item->description }}</strong>
                                @if($item->description && $item->product && $item->description != $item->product->name)
                                    <br><small class="text-muted">{{ $item->description }}</small>
                                @endif
                            </td>
                            <td>{{ $item->isbn ?? 'N/A' }}</td>
                            <td class="text-end">{{ $po->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end">{{ $po->currency_symbol }}{{ number_format($item->total_amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" style="text-align: right; font-weight: 600;">TOTAL:</td>
                            <td style="text-align: right; font-weight: 600;">{{ $po->currency_symbol }}{{ number_format($po->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
                @endif

                <div class="signature-section mt-5">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Prepared by:</strong>
                            <div class="mt-4 pt-2 border-top w-75">
                                {{ $po->preparedBy->name ?? 'System' }}<br>
                                <small>Printed Name / Date</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <strong>Approved by:</strong>
                            <div class="mt-2">
                                <strong>Fr. Louie R. Guades III, CMF</strong><br>
                                Executive Director
                            </div>
                            <div class="mt-3 pt-2 border-top w-75">
                                <small>Signature over Printed Name / Date</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions mt-4 no-print">
                    <button type="button" class="btn btn-light" onclick="window.location.href='{{ route('production.logistic.purchase-order-list') }}'">
                        <i class="las la-arrow-left"></i> Back to List
                    </button>
                    <button type="button" class="btn btn-info" onclick="window.print()">
                        <i class="las la-print"></i> Print P.O.
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .order-form {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }
        .form-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }
        .form-header .company-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .form-header .company-logo {
            width: 60px;
            height: 60px;
            background: #ff0000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2rem;
            font-weight: bold;
            flex-shrink: 0;
        }
        .form-header .company-name {
            font-size: 1.25rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .document-title {
            text-align: center;
            font-size: 1.75rem;
            font-weight: 700;
            margin-top: 1rem;
        }
        .customer-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        .customer-details, .order-details {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
        }
        @media print {
            .no-print, .sidebar, .header {
                display: none !important;
            }
            .order-form {
                box-shadow: none !important;
                padding: 0 !important;
            }
        }
    </style>
    @endpush
</x-app-layout>
