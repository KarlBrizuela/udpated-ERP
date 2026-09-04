<div class="order-form p-0 shadow-none border-0">
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
            <h5 class="fs-16">Report Information</h5>
            <p class="mb-1"><strong>RR Number:</strong> {{ $rr->rr_number }}</p>
            <p class="mb-1"><strong>Date Received:</strong> {{ \Carbon\Carbon::parse($rr->received_date)->format('F d, Y') }}</p>
            <p class="mb-1"><strong>Received By:</strong> {{ $rr->receivedBy->name ?? 'System' }}</p>
            <p class="mb-0"><strong>Status:</strong> <span class="badge badge-success text-capitalize">{{ $rr->status }}</span></p>
        </div>
        <div class="order-details">
            <h5 class="fs-16">Reference Information</h5>
            <p class="mb-1"><strong>Purchase Order:</strong> PO #{{ $rr->purchaseOrder->po_number }}</p>
            <p class="mb-1"><strong>Supplier:</strong> {{ $rr->supplier->company_name }}</p>
            <p class="mb-0"><strong>Contact:</strong> {{ $rr->supplier->contact_person }}</p>
        </div>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-bordered">
            <thead class="bg-primary text-white">
                <tr>
                    <th style="width: 60px;">QTY</th>
                    <th>PRODUCT / DESCRIPTION</th>
                    <th style="width: 120px; text-align: right;">UNIT COST</th>
                    <th style="width: 120px; text-align: right;">TOTAL COST</th>
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
                    <td class="text-end">{{ $rr->currency_symbol }}{{ number_format($item->unit_cost, 2) }}</td>
                    <td class="text-end">{{ $rr->currency_symbol }}{{ number_format($item->total_cost, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-light">
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: 600;">TOTAL:</td>
                    <td style="text-align: right; font-weight: 600;">{{ $rr->currency_symbol }}{{ number_format($rr->items->sum('total_cost'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($rr->notes)
    <div class="mt-3">
        <h5 class="fs-16">Notes / Remarks:</h5>
        <div class="p-2 bg-light rounded border small">
            {{ $rr->notes }}
        </div>
    </div>
    @endif

    <div class="signature-section mt-4">
        <div class="row">
            <div class="col-6 text-center">
                <strong>Received by:</strong>
                <div class="mt-4 pt-1 border-top w-75 mx-auto">
                    {{ $rr->receivedBy->name ?? 'System' }}<br>
                    <small>Printed Name / Date</small>
                </div>
            </div>
            <div class="col-6 text-center">
                <strong>Supplier Representative:</strong>
                <div class="mt-4 pt-1 border-top w-75 mx-auto">
                    <small>Signature over Printed Name / Date</small>
                </div>
            </div>
        </div>
    </div>
</div>
