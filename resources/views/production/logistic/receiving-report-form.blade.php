<x-app-layout :title="'Create Receiving Report'" :sidebar="'production'">
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-header border-0">
                    <h4 class="fs-24 mb-0 text-black">Create Receiving Report</h4>
                </div>
                <div class="card-body">
                    @if(!$purchaseOrder)
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Select Purchase Order to Receive</label>
                            <select class="form-control default-select" id="po_selector" onchange="window.location.href='{{ route('production.logistic.receiving-report.create') }}/' + this.value">
                                <option value="">-- Choose P.O. --</option>
                                @foreach($openPOs as $po)
                                <option value="{{ $po->id }}">PO #{{ $po->po_number }} - {{ $po->supplier->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @else
                    <form action="{{ route('production.logistic.receiving-report.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="purchase_order_id" value="{{ $purchaseOrder->id }}">
                        
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label">RR Number</label>
                                <input type="text" name="rr_number" class="form-control" value="RR-{{ date('YmdHis') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date Received</label>
                                <input type="date" name="received_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Supplier</label>
                                <input type="text" class="form-control" value="{{ $purchaseOrder->supplier->company_name }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">PO Number</label>
                                <input type="text" class="form-control" value="{{ $purchaseOrder->po_number }}" readonly>
                            </div>
                        </div>

                        <div class="table-responsive mb-4">
                            <div class="alert alert-warning d-flex align-items-center gap-2 mb-3" role="alert">
                                <i class="las la-info-circle fs-5"></i>
                                <div>
                                    <strong>Partial Receipt Allowed.</strong>
                                    This Purchase Order will remain <span class="badge badge-warning">Open</span> until all items' received quantities match the ordered quantities.
                                </div>
                            </div>
                            <table class="table table-bordered">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>Description</th>
                                        <th style="width:110px;">Ordered Qty</th>
                                        <th style="width:130px;">Previously Received</th>
                                        <th style="width:110px;">Remaining</th>
                                        <th style="width:180px;">Receipt Progress</th>
                                        <th style="width: 150px;">Today's Receipt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseOrder->items as $item)
                                    @php
                                        $remaining = $item->quantity - $item->received_quantity;
                                        $pct = $item->quantity > 0 ? round(($item->received_quantity / $item->quantity) * 100) : 0;
                                        $barColor = $pct >= 100 ? 'bg-success' : ($pct > 0 ? 'bg-warning' : 'bg-danger');
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $item->product ? $item->product->name : $item->description }}</strong>
                                            @if($item->language) <br><small class="text-muted">Lang: {{ $item->language }}{{ $item->ft ? ' | FT: '.$item->ft : '' }}</small> @endif
                                            @if($item->isbn) <br><small class="text-muted">ISBN: {{ $item->isbn }}</small> @endif
                                        </td>
                                        <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                        <td class="text-center text-success fw-bold">{{ $item->received_quantity }}</td>
                                        <td class="text-center {{ $remaining > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">{{ $remaining }}</td>
                                        <td>
                                            <div class="progress" style="height: 18px; border-radius: 4px;">
                                                <div class="progress-bar {{ $barColor }}" role="progressbar"
                                                     style="width: {{ $pct }}%"
                                                     aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                                                    {{ $pct }}%
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $item->received_quantity }} / {{ $item->quantity }} received</small>
                                        </td>
                                        <td>
                                            @if($remaining > 0)
                                            <input type="number" name="items[{{ $item->id }}][quantity_received]"
                                                   class="form-control text-center qty-input"
                                                   max="{{ $remaining }}" min="0"
                                                   value="{{ $remaining }}"
                                                   oninput="validateQty(this, {{ $remaining }})">
                                            @else
                                            <div class="text-center">
                                                <span class="badge badge-success"><i class="fas fa-check"></i> Fully Received</span>
                                                <input type="hidden" name="items[{{ $item->id }}][quantity_received]" value="0">
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Notes / Remarks</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Enter any discrepancies or delivery notes..."></textarea>
                        </div>

                        <div class="card p-3 mb-4 border" style="background-color: #f8f9fa; border-radius: 8px;">
                            <div class="form-check form-switch m-0 d-flex align-items-center">
                                <input class="form-check-input me-2" type="checkbox" name="mark_as_completed" id="markAsCompletedCheck" value="1" style="cursor: pointer; width: 2.25em; height: 1.25em;">
                                <label class="form-check-label fw-bold text-dark mb-0" for="markAsCompletedCheck" style="cursor: pointer; font-size: 0.95rem;">
                                    <i class="fas fa-check-circle text-success me-1"></i> Mark Purchase Order as Completed / Closed
                                </label>
                            </div>
                            <small class="text-muted mt-1 ms-4 d-block">
                                Check this if no further deliveries will be received for this PO. The Purchase Order status will be set to <strong>Received / Completed</strong> even if ordered quantities are not fully received.
                            </small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('production.logistic.receiving-report-list') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">Post Receiving Report</button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function validateQty(input, max) {
            if (parseInt(input.value) > max) {
                input.value = max;
                alert('Cannot receive more than remaining quantity (' + max + ')');
            }
        }
    </script>
    @endpush
</x-app-layout>
