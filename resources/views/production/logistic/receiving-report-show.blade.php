<x-app-layout :title="'Receiving Report Details'" :sidebar="'production'">
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
                    <div class="document-title">RECEIVING REPORT</div>
                </div>

                <div class="customer-section">
                    <div class="customer-details">
                        <h5>Report Information</h5>
                        <p><strong>RR Number:</strong> {{ $rr->rr_number }}</p>
                        <p><strong>Date Received:</strong> {{ \Carbon\Carbon::parse($rr->received_date)->format('F d, Y') }}</p>
                        <p><strong>Received By:</strong> {{ $rr->receivedBy->name ?? 'System' }}</p>
                        <p><strong>Status:</strong> <span class="badge badge-success text-capitalize">{{ $rr->status }}</span></p>
                    </div>
                    <div class="order-details">
                        <h5>Reference Information</h5>
                        <p><strong>Purchase Order:</strong> PO #{{ $rr->purchaseOrder->po_number }}</p>
                        <p><strong>Supplier:</strong> {{ $rr->supplier->company_name }}</p>
                        <p><strong>Supplier Contact:</strong> {{ $rr->supplier->contact_person }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <h5>Received Items</h5>
                    <table class="table table-bordered">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th style="width: 80px;">QTY</th>
                                <th>PRODUCT / DESCRIPTION</th>
                                <th style="width: 150px;">ISBN</th>
                                <th style="width: 150px; text-align: right;">UNIT COST</th>
                                <th style="width: 150px; text-align: right;">TOTAL COST</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rr->items as $item)
                            <tr>
                                <td class="text-center">{{ $item->quantity_received }}</td>
                                <td>
                                    <strong>{{ $item->product ? $item->product->name : $item->purchaseOrderItem->description }}</strong>
                                    @if($item->product && $item->product->isbn) <br><small class="text-muted">ISBN: {{ $item->product->isbn }}</small> @endif
                                </td>
                                <td>{{ $item->product->isbn ?? $item->purchaseOrderItem->isbn ?? 'N/A' }}</td>
                                <td class="text-end">{{ $rr->currency_symbol }}{{ number_format($item->unit_cost, 2) }}</td>
                                <td class="text-end">{{ $rr->currency_symbol }}{{ number_format($item->total_cost, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="text-align: right; font-weight: 600;">TOTAL:</td>
                                <td style="text-align: right; font-weight: 600;">{{ $rr->currency_symbol }}{{ number_format($rr->items->sum('total_cost'), 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($rr->notes)
                <div class="mt-4">
                    <h5>Notes / Remarks:</h5>
                    <div class="p-3 bg-light rounded border">
                        {{ $rr->notes }}
                    </div>
                </div>
                @endif

                <div class="signature-section mt-5">
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <strong>Received by:</strong>
                            <div class="mt-5 pt-2 border-top w-75 mx-auto">
                                {{ $rr->receivedBy->name ?? 'System' }}<br>
                                <small>Printed Name / Date</small>
                            </div>
                        </div>
                        <div class="col-md-6 text-center">
                            <strong>Supplier Delivery Representative:</strong>
                            <div class="mt-5 pt-2 border-top w-75 mx-auto">
                                <small>Signature over Printed Name / Date</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions mt-4 no-print">
                    <button type="button" class="btn btn-light" onclick="window.location.href='{{ route('production.logistic.receiving-report-list') }}'">
                        <i class="las la-arrow-left"></i> Back to List
                    </button>
                    <button type="button" class="btn btn-info" onclick="window.print()">
                        <i class="las la-print"></i> Print RR
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
