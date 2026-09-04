<div class="po-modal-container p-2">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center fw-bold fs-20" style="width: 50px; height: 50px; flex-shrink: 0;">
                C
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-dark fs-15 text-uppercase">Claretian Communications Foundation, Inc.</h6>
                <small class="text-muted d-block" style="font-size: 0.78rem;">8 Mayumi St., UP Village, Diliman, Quezon City 1128</small>
                <small class="text-muted d-block" style="font-size: 0.78rem;">Non-VAT Reg. TIN: 000-395-713-000 | Tel: (02) 921-3984</small>
            </div>
        </div>
        <div class="text-end">
            <span class="badge bg-danger text-white fs-13 px-3 py-2 text-uppercase fw-bold" style="letter-spacing: 1px;">PURCHASE ORDER</span>
            <div class="text-muted small fw-bold mt-1">PO #: {{ $po->po_number }}</div>
        </div>
    </div>

    <!-- Vendor & Order Info (Side by Side) -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="p-3 bg-light rounded border h-100">
                <h6 class="fw-bold text-dark mb-2 pb-1 border-bottom" style="font-size: 0.88rem;">
                    <i class="las la-building text-danger me-1"></i> Vendor Information
                </h6>
                <div class="small">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Vendor:</span>
                        <span class="fw-bold text-dark">{{ $po->supplier ? $po->supplier->company_name : ($po->vendor_name ?: 'N/A') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Contact:</span>
                        <span class="fw-medium text-dark">{{ $po->supplier ? ($po->supplier->contact_person ?: 'N/A') : 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Address:</span>
                        <span class="text-dark text-end ms-2">{{ $po->supplier ? ($po->supplier->address ?: 'N/A') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-3 bg-light rounded border h-100">
                <h6 class="fw-bold text-dark mb-2 pb-1 border-bottom" style="font-size: 0.88rem;">
                    <i class="las la-info-circle text-primary me-1"></i> Order Information
                </h6>
                <div class="small">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Order Date:</span>
                        <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($po->date)->format('F d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Payment Terms:</span>
                        <span class="badge bg-white text-dark border px-2 py-1">{{ $po->terms ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <span class="text-muted">Status:</span>
                        <span class="badge bg-success text-capitalize px-3 py-1 fs-12">{{ str_replace('_', ' ', $po->status) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="table-responsive rounded border mb-4">
        <table class="table table-striped table-hover mb-0 text-black align-middle" style="font-size: 0.88rem;">
            <thead style="background-color: #1a5276; color: #ffffff;">
                <tr>
                    <th style="width: 70px;" class="text-center text-white py-2">QTY</th>
                    <th class="text-white py-2">PRODUCT / DESCRIPTION</th>
                    <th style="width: 130px;" class="text-end text-white py-2">UNIT PRICE</th>
                    <th style="width: 140px;" class="text-end text-white py-2">TOTAL AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($po->items as $item)
                <tr>
                    <td class="text-center fw-bold">{{ number_format($item->quantity) }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ $item->product ? $item->product->name : $item->description }}</div>
                        @if($item->isbn)
                            <small class="text-muted">ISBN: {{ $item->isbn }}</small>
                        @endif
                    </td>
                    <td class="text-end fw-medium">{{ $po->currency_symbol ?: '₱' }}{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end fw-bold text-dark">{{ $po->currency_symbol ?: '₱' }}{{ number_format($item->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-light fw-bold border-top">
                <tr>
                    <td colspan="3" class="text-end py-2 text-dark fs-14">TOTAL COST:</td>
                    <td class="text-end py-2 text-danger fs-15">{{ $po->currency_symbol ?: '₱' }}{{ number_format($po->total_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Signatures -->
    <div class="row pt-2 mt-3 border-top">
        <div class="col-6">
            <small class="text-muted d-block fw-bold mb-3">Prepared by:</small>
            <div class="fw-bold text-dark">{{ $po->preparedBy->name ?? 'System' }}</div>
            <small class="text-muted">Purchasing / Admin</small>
        </div>
        <div class="col-6 text-end">
            <small class="text-muted d-block fw-bold mb-3">Approved by:</small>
            <div class="fw-bold text-dark">Fr. Louie R. Guades III, CMF</div>
            <small class="text-muted">Executive Director</small>
        </div>
    </div>
</div>
