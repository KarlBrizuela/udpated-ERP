<x-app-layout :title="$title" :sidebar="$sidebar">
    @push('styles')
    <style>
        .freight-action-card,
        .cargo-action-card {
            float: none;
            width: 100%;
            margin-right: 0;
        }

        .freight-action-card,
        .cargo-action-card {
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
        }

        .freight-action-card .card-body,
        .freight-action-card form,
        .cargo-action-card .card-body,
        .cargo-action-card form {
            height: auto !important;
            min-height: 0 !important;
        }

        .freight-action-card .compact-help {
            margin-bottom: 0.5rem !important;
            padding: 0.4rem 0.5rem !important;
        }

        .freight-action-card textarea {
            min-height: 34px !important;
            max-height: 64px !important;
        }

        .cargo-items-scroll {
            max-height: 150px;
            overflow-y: auto;
        }
    </style>
    @endpush

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Review Freight Quotation</h5>
                        <a href="{{ route('production.logistic.pending-freight-quotations') }}" class="btn btn-sm btn-light text-primary fw-bold">
                            <i class="bi bi-arrow-left me-1"></i>Back to List
                        </a>
                    </div>

                    <div class="card-body p-2">
                        <!-- Sales Order Header - Prominent Display -->
                        @if($quotation->sales_order_id && $quotation->salesOrder && $quotation->salesOrder->id)
                            @php
                                $ordCurr = $quotation->currency ?? ($quotation->salesOrder->currency ?? 'PHP');
                                $ordSym = ($ordCurr === 'USD' ? '$' : '₱');
                            @endphp
                            <div class="alert alert-info border-2 mb-4" style="background-color: #e3f2fd; border-color: #2196F3;">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="mb-2"><i class="bi bi-file-earmark-arrow-right me-2"></i>📦 Sales Order: <strong>{{ $quotation->salesOrder->so_number }}</strong></h5>
                                        <p class="mb-1"><strong>Company:</strong> {{ $quotation->salesOrder->customer?->customer_name ?? ($quotation->customer?->customer_name ?? 'N/A') }}</p>
                                        <p class="mb-1"><strong>Customer Name:</strong> {{ $quotation->salesOrder->customer_representative ?: ($quotation->customer_representative ?: 'N/A') }}</p>
                                        <p class="mb-1"><strong>Items:</strong> {{ $quotation->salesOrder->items ? $quotation->salesOrder->items->sum('quantity') : 0 }} units | <strong>Total:</strong> {{ $ordSym }} {{ number_format($quotation->salesOrder->items ? $quotation->salesOrder->items->sum(function($item) { return $item->quantity * $item->price; }) : 0, 2) }}</p>
                                        <p class="mb-0 text-muted"><strong>Delivery Address:</strong> {{ $quotation->salesOrder?->shipping_address ?: ($quotation->salesOrder?->billing_address ?: ($quotation->destination_address ?: 'N/A')) }}</p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="{{ route('marketing.sales-orders.show', $quotation->sales_order_id) }}" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="bi bi-eye me-1"></i>View Full SO
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Quotation Header Info -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <small class="text-muted">Quote Number</small>
                                        <h6 class="mb-0">{{ $quotation->quote_number }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <small class="text-muted">Requested By</small>
                                        <h6 class="mb-0">{{ $quotation->createdBy->name ?? 'System' }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <small class="text-muted">Created Date</small>
                                        <h6 class="mb-0">{{ $quotation->created_at->format('M d, Y') }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <small class="text-muted">Status</small>
                                        <h6 class="mb-0">
                                            @if($quotation->workflow_status === 'draft')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-success">Approved</span>
                                            @endif
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Shipment Details -->
                        <h6 class="border-bottom pb-2 mb-3"><strong>Shipment Details</strong></h6>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Origin (Pick-up)</h6>
                                        <p class="mb-1"><strong>Contact:</strong> {{ $quotation->origin_contact }}</p>
                                        <p class="mb-1"><strong>Province:</strong> {{ $quotation->origin_province }}</p>
                                        <p class="mb-0"><strong>Address:</strong><br> {{ $quotation->origin_address }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Destination (Delivery)</h6>
                                        <p class="mb-1"><strong>Contact:</strong> {{ $quotation->destination_contact }}</p>
                                        <p class="mb-1"><strong>Province:</strong> {{ $quotation->destination_province }}</p>
                                        <p class="mb-0"><strong>Address:</strong><br> {{ $quotation->destination_address }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p><strong>Service Mode:</strong> {{ $quotation->service_mode }}</p>
                        <p><strong>Forwarder:</strong> {{ $quotation->forwarder ?? $quotation->salesOrder?->forwarder ?? $quotation->freight_mode ?? 'N/A' }}</p>
                        <p><strong>Transaction Type:</strong> {{ $quotation->transaction_type ? ucwords(str_replace('_', ' ', $quotation->transaction_type)) : 'Paid' }}</p>
                        <p><strong>Terms:</strong> {{ $quotation->terms ?: 'N/A' }}</p>
                        <p><strong>Currency:</strong> <span class="badge bg-danger text-white fs-6">{{ $quotation->currency ?? 'PHP' }} ({{ ($quotation->currency ?? 'PHP') === 'USD' ? '$' : (($quotation->currency ?? 'PHP') === 'EUR' ? '€' : '₱') }})</span></p>
                        <p><strong>Freight Option:</strong> {{ $quotation->freight_option ? ucwords(str_replace('_', ' ', $quotation->freight_option)) : 'N/A' }}</p>
                        <p><strong>Service Fee:</strong> {{ $quotation->freight_option === 'freight_collect' ? 'Applies to Freight Collect' : 'No service fee for Freight Billing' }}</p>
                        @php
                            $logisticsPop = $quotation->proof_of_payment ?: ($quotation->salesOrder?->proof_of_payment ?? null);
                        @endphp
                        <p><strong>Proof of Payment:</strong>
                            @if($logisticsPop)
                                <a href="{{ asset('storage/' . $logisticsPop) }}" target="_blank" class="btn btn-xs btn-outline-success ms-1">
                                    <i class="bi bi-paperclip me-1"></i>View Attachment ({{ basename($logisticsPop) }})
                                </a>
                            @else
                                <span class="text-muted">Not attached (Optional)</span>
                            @endif
                        </p>

                        <hr>

                        <!-- CARGO ITEMS INPUT SECTION - ALWAYS VISIBLE -->
                        <div class="card border-info border-2 mb-3 cargo-action-card" style="background-color: #f0f8ff;">
                            <div class="card-header bg-info text-white py-2">
                                <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>📦 Cargo Items</h6>
                            </div>
                            <div class="card-body p-1" style="padding: 0.5rem !important;">
                                <form id="cargoItemsForm" method="POST" action="{{ route('production.logistic.update-cargo-items', $quotation->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="boxes_count" id="cargo_boxes_count_hidden">
                                        <input type="hidden" name="estimated_freight" id="cargo_estimated_freight_hidden">
                                        <input type="hidden" name="logistics_notes" id="cargo_logistics_notes_hidden">

                                        <div class="table-responsive cargo-items-scroll" style="margin-bottom: 0.5rem; border: 1px solid #dee2e6; border-radius: 4px;">
                                            <table class="table table-bordered table-sm" id="cargoItemsTable" style="margin-bottom: 0; font-size: 0.85rem;">
                                                <thead class="table-light" style="position: sticky; top: 0; z-index: 10; background: #fff;">
                                                    <tr style="background-color: #e8f4f8; height: 2rem;">
                                                        <th style="width: 5%; padding: 0.25rem;">#</th>
                                                        <th style="width: 8%; padding: 0.25rem;">Qty</th>
                                                        <th style="width: 16%; padding: 0.25rem;">Type</th>
                                                        <th style="width: 20%; padding: 0.25rem;">Dimensions</th>
                                                        <th style="width: 13%; padding: 0.25rem;">Gross Wt</th>
                                                        <th style="width: 13%; padding: 0.25rem;">Vol Wt</th>
                                                        <th style="width: 8%; text-align: center; padding: 0.25rem;">Del</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="cargoItemsBody">
                                                @php
                                                    $existingItems = [];
                                                    if ($quotation->cargo_items) {
                                                        $existingItems = is_string($quotation->cargo_items) ? json_decode($quotation->cargo_items, true) : $quotation->cargo_items;
                                                    }
                                                @endphp
                                                
                                                @if(count($existingItems) > 0)
                                                    @foreach($existingItems as $index => $item)
                                                        <tr class="cargo-row" data-row-num="{{ $index + 1 }}" style="height: 2rem;">
                                                            <td class="row-number" style="padding: 0.2rem; vertical-align: middle; font-size: 0.8rem;">{{ $index + 1 }}</td>
                                                            <td style="padding: 0.2rem;">
                                                                <input type="number" name="cargo_qty[]" class="form-control form-control-sm" style="height: 1.5rem; font-size: 0.75rem;"
                                                                       value="{{ $item['qty'] ?? '' }}" min="1" placeholder="0">
                                                            </td>
                                                            <td style="padding: 0.2rem;">
                                                                <select name="cargo_package_type[]" class="form-control form-control-sm" style="height: 1.5rem; font-size: 0.75rem;">
                                                                    <option value="">Select</option>
                                                                    <option value="Box, Bag, Pallet" {{ ($item['package_type'] ?? '') === 'Box, Bag, Pallet' ? 'selected' : '' }}>Box</option>
                                                                    <option value="Parcel" {{ ($item['package_type'] ?? '') === 'Parcel' ? 'selected' : '' }}>Parcel</option>
                                                                    <option value="Crate" {{ ($item['package_type'] ?? '') === 'Crate' ? 'selected' : '' }}>Crate</option>
                                                                    <option value="Carton" {{ ($item['package_type'] ?? '') === 'Carton' ? 'selected' : '' }}>Carton</option>
                                                                    <option value="LCL" {{ ($item['package_type'] ?? '') === 'LCL' ? 'selected' : '' }}>LCL</option>
                                                                    <option value="FCL" {{ ($item['package_type'] ?? '') === 'FCL' ? 'selected' : '' }}>FCL</option>
                                                                    <option value="Pallets" {{ ($item['package_type'] ?? '') === 'Pallets' ? 'selected' : '' }}>Pallets</option>
                                                                </select>
                                                            </td>
                                                            <td style="padding: 0.2rem;">
                                                                <input type="text" name="cargo_dimensions[]" class="form-control form-control-sm" style="height: 1.5rem; font-size: 0.75rem;"
                                                                       value="{{ $item['dimensions'] ?? '' }}" placeholder="L×W×H">
                                                            </td>
                                                            <td style="padding: 0.2rem;">
                                                                <input type="number" name="cargo_gross_weight[]" class="form-control form-control-sm" style="height: 1.5rem; font-size: 0.75rem;"
                                                                       value="{{ $item['gross_weight'] ?? '' }}" step="0.01" placeholder="kg">
                                                            </td>
                                                            <td style="padding: 0.2rem;">
                                                                <input type="number" name="cargo_vol_weight[]" class="form-control form-control-sm" style="height: 1.5rem; font-size: 0.75rem;"
                                                                       value="{{ $item['vol_weight'] ?? '' }}" step="0.01" placeholder="kg">
                                                            </td>
                                                            <td style="text-align: center; padding: 0.2rem; vertical-align: middle;">
                                                                <button type="button" class="btn btn-sm btn-danger" style="padding: 0.15rem 0.3rem; font-size: 0.7rem;" onclick="removeCargoRow(this)">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr class="cargo-row" data-row-num="1" style="height: 2rem;">
                                                        <td class="row-number" style="padding: 0.2rem; vertical-align: middle; font-size: 0.8rem;">1</td>
                                                        <td style="padding: 0.2rem;">
                                                            <input type="number" name="cargo_qty[]" class="form-control form-control-sm" style="height: 1.5rem; font-size: 0.75rem;" min="1" placeholder="0">
                                                        </td>
                                                        <td style="padding: 0.2rem;">
                                                            <select name="cargo_package_type[]" class="form-control form-control-sm" style="height: 1.5rem; font-size: 0.75rem;">
                                                                <option value="">Select</option>
                                                                <option value="Box, Bag, Pallet">Box</option>
                                                                <option value="Parcel">Parcel</option>
                                                                <option value="Crate">Crate</option>
                                                                <option value="Carton">Carton</option>
                                                                <option value="LCL">LCL</option>
                                                                <option value="FCL">FCL</option>
                                                                <option value="Pallets">Pallets</option>
                                                            </select>
                                                        </td>
                                                        <td style="padding: 0.2rem;">
                                                            <input type="text" name="cargo_dimensions[]" class="form-control form-control-sm" style="height: 1.5rem; font-size: 0.75rem;" placeholder="L×W×H">
                                                        </td>
                                                        <td style="padding: 0.2rem;">
                                                            <input type="number" name="cargo_gross_weight[]" class="form-control form-control-sm" style="height: 1.5rem; font-size: 0.75rem;" step="0.01" placeholder="kg">
                                                        </td>
                                                        <td style="padding: 0.2rem;">
                                                            <input type="number" name="cargo_vol_weight[]" class="form-control form-control-sm" style="height: 1.5rem; font-size: 0.75rem;" step="0.01" placeholder="kg">
                                                        </td>
                                                        <td style="text-align: center; padding: 0.2rem; vertical-align: middle;">
                                                            <button type="button" class="btn btn-sm btn-danger" style="padding: 0.15rem 0.3rem; font-size: 0.7rem;" onclick="removeCargoRow(this)">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>

                                    <div style="display: flex; gap: 0.5rem; justify-content: space-between; margin-top: 0.5rem;">
                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="addCargoItemRow()" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">
                                            <i class="bi bi-plus-circle me-1"></i>Add Row
                                        </button>

                                        <button type="submit" class="btn btn-sm btn-info" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">
                                            <i class="bi bi-check-circle me-1"></i>Save Cargo Items
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card border-success border-3 mb-3 freight-action-card" style="background-color: #f0fdf4;">
                            <div class="card-header bg-success text-white py-2 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="bi bi-calculator me-2"></i>Set Freight Charges & Boxes</h6>
                                @if($quotation->workflow_status === 'approved')
                                    <span class="badge bg-light text-success"><i class="bi bi-check-circle me-1"></i>Approved</span>
                                @endif
                            </div>
                            <div id="freightChargesCollapse">
                                <div class="card-body p-1" style="padding: 0.5rem !important;">
                                    <div class="compact-help" style="font-size: 0.85rem; padding: 0.5rem; background-color: #fef3cd; border-left: 3px solid #ff9800; margin-bottom: 0.75rem; border-radius: 0.25rem;">
                                        <strong>Fill in or edit the fields below to update the freight quotation charges</strong>
                                    </div>

                                    <form id="approveQuotationForm" method="POST" action="{{ route('production.logistic.approve-freight-quotation', $quotation->id) }}">
                                        @csrf

                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Forwarder (Carrier):</label>
                                                <input type="text" class="form-control form-control-sm @error('forwarder') is-invalid @enderror" 
                                                       name="forwarder" value="{{ old('forwarder', $quotation->forwarder ?? $quotation->salesOrder?->forwarder ?? '') }}" 
                                                       placeholder="e.g., LBC, 2GO, J&T, AP Cargo, FedEx" style="height: 1.75rem; font-size: 0.85rem;">
                                                @error('forwarder')<div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold" style="font-size: 0.85rem;"> Number of Boxes:</label>
                                                <input type="number" class="form-control form-control-sm @error('boxes_count') is-invalid @enderror" 
                                                       name="boxes_count" min="0" value="{{ old('boxes_count', $quotation->boxes_count ? $quotation->boxes_count : '') }}" 
                                                       placeholder="Enter number of boxes (optional, e.g., 5)" style="height: 1.75rem; font-size: 0.85rem;">
                                                @error('boxes_count')<div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                @php
                                                     $ordCurr = $quotation->currency ?? ($quotation->salesOrder->currency ?? 'PHP');
                                                     $ordSym = ($ordCurr === 'USD' ? '$' : '₱');
                                                 @endphp
                                                 <label class="form-label fw-bold" style="font-size: 0.85rem;"> Estimated Freight Charge ({{ $ordSym }}):</label>
                                                <input type="number" class="form-control form-control-sm @error('estimated_freight') is-invalid @enderror" 
                                                       name="estimated_freight" step="0.01" min="0" 
                                                       value="{{ old('estimated_freight', $quotation->estimated_freight > 0 ? $quotation->estimated_freight : ($quotation->workflow_status === 'approved' && $quotation->estimated_freight !== null ? $quotation->estimated_freight : '')) }}" 
                                                       placeholder="Enter freight charge (optional, e.g., 500.00)" style="height: 1.75rem; font-size: 0.85rem;">
                                                @error('estimated_freight')<div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                            </div>
                                        </div>

                                        <div style="margin-top: 0.5rem;">
                                            <label class="form-label" style="font-size: 0.85rem;">Logistics Notes:</label>
                                            <textarea class="form-control form-control-sm @error('logistics_notes') is-invalid @enderror" 
                                                      name="logistics_notes" rows="1" style="font-size: 0.85rem;"
                                                      placeholder="e.g., sea freight via Manila Port, delivery in 7-10 days...">{{ old('logistics_notes', $quotation->logistics_notes) }}</textarea>
                                            @error('logistics_notes')<div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                        </div>

                                        <div style="display: flex; gap: 0.5rem; justify-content: space-between; margin-top: 0.5rem;">
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">
                                                <i class="bi bi-x-circle me-1"></i>Reject
                                            </button>
                                            <button type="submit" class="btn btn-sm btn-success" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">
                                                <i class="bi bi-check-circle me-1"></i>{{ $quotation->workflow_status === 'approved' ? '✓ Save / Update Freight Charges' : '✓ Approve & Quote' }}
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Reject Quotation</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST" action="{{ route('production.logistic.reject-freight-quotation', $quotation->id) }}">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <label class="form-label">Reason for Rejection:</label>
                                                        <textarea class="form-control" name="rejection_reason" rows="4" required 
                                                                  placeholder="Explain why this quotation is being rejected..."></textarea>
                                                        <small class="text-muted d-block mt-2">This will be sent back to marketing</small>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Reject</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- CARGO ITEMS DISPLAY - ALWAYS VISIBLE -->
                        <h6 class="border-bottom pb-2 mb-3"><strong>📦 Cargo Items</strong></h6>
                        @if($quotation->cargo_items)
                            <div class="table-responsive mb-3" style="max-height: 220px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px;">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light" style="position: sticky; top: 0;">
                                        <tr>
                                            <th style="width: 15%;">Quantity</th>
                                            <th style="width: 25%;">Package Type</th>
                                            <th style="width: 30%;">Dimensions</th>
                                            <th style="width: 15%;">Gross Weight</th>
                                            <th style="width: 15%;">Vol. Weight</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $cargoItems = is_string($quotation->cargo_items) ? json_decode($quotation->cargo_items, true) : $quotation->cargo_items;
                                        @endphp
                                        @foreach($cargoItems as $item)
                                            <tr>
                                                <td>{{ $item['qty'] ?? '-' }}</td>
                                                <td>{{ $item['package_type'] ?? '-' }}</td>
                                                <td>{{ $item['dimensions'] ?? '-' }}</td>
                                                <td>{{ $item['gross_weight'] ?? '-' }}</td>
                                                <td>{{ $item['vol_weight'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-4">
                                <i class="bi bi-info-circle me-2"></i>No cargo items recorded yet.
                            </div>
                        @endif

                        <!-- BOOKS/ITEMS BREAKDOWN DISPLAY - ALWAYS VISIBLE -->
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                            <h6 class="mb-0"><strong>📚 Books/Items Breakdown</strong></h6>
                            <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" onclick="printItemsBreakdownOnly()">
                                <i class="fas fa-print me-1"></i> Print Breakdown
                            </button>
                        </div>
                        @if($quotation->sales_order_id && $quotation->salesOrder && $quotation->salesOrder->id && $quotation->salesOrder->items && $quotation->salesOrder->items->count() > 0)
                            <div class="table-responsive mb-4">
                                <table class="table table-hover table-bordered">
                                    <thead class="table-info">
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th>Book/Product Name</th>
                                            <th style="width: 80px;" class="text-center">QTY</th>
                                            <th style="width: 100px;" class="text-center">Weight (g)</th>
                                            <th style="width: 120px;" class="text-center">Total Weight</th>
                                            <th style="width: 110px;" class="text-end">Unit Price</th>
                                            <th style="width: 100px;" class="text-end">Discount</th>
                                            <th style="width: 120px;" class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalAmount = 0; $totalWeight = 0; $grossSubtotal = 0; $totalItemDiscount = 0; @endphp
                                        @foreach($quotation->salesOrder->items as $key => $item)
                                            @php
                                                $bookIndex = $item->bookIndex ?? ($item->book_index_id ? \App\Models\BookIndex::find($item->book_index_id) : null);
                                                $book = $item->book;
                                                $product = $item->product ?? $item->bundle;

                                                if ($bookIndex) {
                                                    $displayName = $bookIndex->display_name;
                                                    $sku = $bookIndex->barcode ?: ($bookIndex->nbs_barcode ?: ($bookIndex->article ?: ($book?->sku ?: $book?->isbn)));
                                                    $weight = (float)($bookIndex->weight ?: ($book?->weight ?: 0));
                                                } else {
                                                    $displayName = $item->product_name ?? ($item->item_name ?? ($product?->name ?? ($book?->name ?? 'Unknown Item')));
                                                    $sku = $product?->sku ?? ($book?->sku ?? ($book?->isbn ?? null));
                                                    $weight = (float)($product?->weight ?? ($book?->weight ?: 0));
                                                }

                                                $quantity = (int)($item->quantity ?? 0);
                                                $price = (float)($item->price ?? 0);
                                                $discVal = (float)($item->discount_value ?? 0);
                                                $discType = $item->discount_type ?? 'percentage';
                                                $itemGross = $quantity * $price;
                                                $discAmt = $item->discount_amount ?? ($discType === 'percentage' ? $itemGross * ($discVal / 100) : $discVal);
                                                $itemSubtotal = $item->subtotal ?? max(0, $itemGross - $discAmt);
                                                $itemTotalWeight = ($quantity * $weight);
                                                $totalAmount += $itemSubtotal;
                                                $totalWeight += $itemTotalWeight;
                                                $grossSubtotal += $itemGross;
                                                $totalItemDiscount += $discAmt;
                                            @endphp
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <strong>{{ $displayName }}</strong>
                                                    @if($sku)
                                                        <br><small class="text-muted">SKU: {{ $sku }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $quantity }}</td>
                                                <td class="text-center">
                                                    @if($weight > 0)
                                                        {{ number_format($weight, 2) }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center fw-bold">
                                                    @if($totalWeight > 0)
                                                        {{ number_format($itemTotalWeight, 2) }} g
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">{{ $ordSym }} {{ number_format($price, 2) }}</td>
                                                <td class="text-end text-danger">
                                                    @if($discVal > 0)
                                                        {{ $discType === 'percentage' ? $discVal . '%' : $ordSym . number_format($discVal, 2) }}
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td class="text-end fw-bold">{{ $ordSym }} {{ number_format($itemSubtotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                         @if($totalItemDiscount > 0)
                                         <tr>
                                             <td colspan="7" class="text-end"><strong>Gross Subtotal:</strong></td>
                                             <td class="text-end fw-bold">{{ $ordSym }} {{ number_format($grossSubtotal, 2) }}</td>
                                         </tr>
                                         <tr>
                                             <td colspan="7" class="text-end text-danger"><strong>Item Discounts:</strong></td>
                                             <td class="text-end fw-bold text-danger">- {{ $ordSym }} {{ number_format($totalItemDiscount, 2) }}</td>
                                         </tr>
                                         @endif
                                         <tr>
                                             <td colspan="4" class="text-end"><strong>Order Subtotal:</strong></td>
                                             <td class="text-center fw-bold">
                                                 @if($totalWeight > 0)
                                                     {{ number_format($totalWeight, 2) }} g
                                                 @else
                                                     <span class="text-muted">-</span>
                                                 @endif
                                             </td>
                                             <td colspan="2"></td>
                                             <td class="text-end fw-bold fs-5">{{ $ordSym }} {{ number_format($totalAmount, 2) }}</td>
                                         </tr>
                                         @php
                                             $topOrderDiscAmount = (float)($quotation->salesOrder->discount_amount ?? 0);
                                             $topOrderDiscVal = (float)($quotation->salesOrder->discount_percentage ?? 0);
                                             $topNetTotal = max(0, $totalAmount - $topOrderDiscAmount);
                                             $topServiceFee = 0;
                                             if ($quotation->freight_option === 'freight_collect') {
                                                 if ($ordCurr === 'USD') $topServiceFee = 50.00 / 56.0;
                                                 elseif ($ordCurr === 'EUR') $topServiceFee = 50.00 / 62.0;
                                                 else $topServiceFee = 50.00;
                                             }
                                             $topGrandTotal = $topNetTotal + $topServiceFee;
                                         @endphp
                                         @if($topOrderDiscAmount > 0)
                                         <tr>
                                             <td colspan="7" class="text-end text-danger">
                                                 <strong>Order Discount{{ $topOrderDiscVal > 0 ? ' (' . $topOrderDiscVal . '%)' : ' (' . $ordSym . number_format($topOrderDiscAmount, 2) . ')' }}:</strong>
                                             </td>
                                             <td class="text-end fw-bold text-danger">- {{ $ordSym }} {{ number_format($topOrderDiscAmount, 2) }}</td>
                                         </tr>
                                         @endif
                                         @if($topServiceFee > 0)
                                         <tr style="background-color: #fff3cd;">
                                             <td colspan="7" class="text-end text-success"><strong>Service Fee (Freight Collect):</strong></td>
                                             <td class="text-end fw-bold text-success">{{ $ordSym }} {{ number_format($topServiceFee, 2) }}</td>
                                         </tr>
                                         <tr style="background-color: #e8f5e9;">
                                             <td colspan="7" class="text-end"><strong>Grand Total:</strong></td>
                                             <td class="text-end fw-bold" style="font-size: 1.1rem; color: #2e7d32;">{{ $ordSym }} {{ number_format($topGrandTotal, 2) }}</td>
                                         </tr>
                                         @else
                                         <tr style="background-color: #e8f5e9;">
                                             <td colspan="7" class="text-end"><strong>Grand Total:</strong></td>
                                             <td class="text-end fw-bold" style="font-size: 1.1rem; color: #2e7d32;">{{ $ordSym }} {{ number_format($topGrandTotal, 2) }}</td>
                                         </tr>
                                         @endif
                                     </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>No items found in this sales order.
                            </div>
                        @endif

                        <hr>



                        <hr>

                    <!-- Linked Sales Order -->
                    @if($quotation->sales_order_id && $quotation->salesOrder)
                        <h6 class="border-bottom pb-2 mb-3"><strong>Linked Sales Order</strong></h6>
                        
                        <div class="card mb-4 border-success">
                            <div class="card-body p-2">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <p><strong>SO Number:</strong> {{ $quotation->salesOrder->so_number ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>Company:</strong> {{ $quotation->salesOrder->customer?->customer_name ?? ($quotation->customer?->customer_name ?? 'N/A') }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>Customer Name:</strong> {{ $quotation->salesOrder->customer_representative ?: ($quotation->customer_representative ?: 'N/A') }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>Status:</strong> 
                                            <span class="badge bg-info">{{ $quotation->salesOrder->status ?? 'DRAFT' }}</span>
                                        </p>
                                    </div>
                                </div>

                                <!-- SO Items Table -->
                                @if($quotation->salesOrder->items && $quotation->salesOrder->items->count() > 0)
                                    <h6 class="mt-3 mb-2"><small>Order Items:</small></h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-2">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 60px;">QTY</th>
                                                    <th>PRODUCT</th>
                                                    <th style="width: 110px;" class="text-end">UNIT PRICE</th>
                                                    <th style="width: 100px;" class="text-end">DISCOUNT</th>
                                                    <th style="width: 120px;" class="text-end">AMOUNT</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $soSubtotal = 0; @endphp
                                                @foreach($quotation->salesOrder->items as $item)
                                                    @php
                                                        $itemGross = $item->quantity * $item->price;
                                                        $discVal = (float)($item->discount_value ?? 0);
                                                        $discType = $item->discount_type ?? 'percentage';
                                                        $discAmt = $item->discount_amount ?? ($discType === 'percentage' ? $itemGross * ($discVal / 100) : $discVal);
                                                        $itemSubtotal = $item->subtotal ?? max(0, $itemGross - $discAmt);
                                                        $soSubtotal += $itemSubtotal;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $item->quantity }}</td>
                                                        @php
                                                            $soBookIndex = $item->bookIndex ?? ($item->book_index_id ? \App\Models\BookIndex::find($item->book_index_id) : null);
                                                            $soItemName = $soBookIndex ? $soBookIndex->display_name : ($item->product_name ?? ($item->item_name ?? ($item->product?->name ?? ($item->book?->name ?? ($item->bundle?->name ?? 'N/A')))));
                                                        @endphp
                                                        <td>{{ $soItemName }}</td>
                                                        <td class="text-end">{{ $ordSym }} {{ number_format($item->price, 2) }}</td>
                                                        <td class="text-end text-danger">
                                                            @if($discVal > 0)
                                                                {{ $discType === 'percentage' ? $discVal . '%' : $ordSym . number_format($discVal, 2) }}
                                                            @else
                                                                —
                                                            @endif
                                                        </td>
                                                        <td class="text-end fw-bold">{{ $ordSym }} {{ number_format($itemSubtotal, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light">
                                                @if($totalItemDiscount > 0)
                                                <tr>
                                                    <td colspan="4" class="text-end"><strong>Gross Subtotal:</strong></td>
                                                    <td class="text-end fw-bold">{{ $ordSym }} {{ number_format($grossSubtotal, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-end text-danger"><strong>Item Discounts:</strong></td>
                                                    <td class="text-end fw-bold text-danger">- {{ $ordSym }} {{ number_format($totalItemDiscount, 2) }}</td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td colspan="4" class="text-end"><strong>Order Subtotal:</strong></td>
                                                    <td class="text-end fw-bold">{{ $ordSym }} {{ number_format($soSubtotal, 2) }}</td>
                                                </tr>
                                                @php
                                                    $orderDiscAmount = (float)($quotation->salesOrder->discount_amount ?? 0);
                                                    $orderDiscVal = (float)($quotation->salesOrder->discount_percentage ?? 0);
                                                    $soNetTotal = max(0, $soSubtotal - $orderDiscAmount);
                                                    $serviceFee = 0;
                                                    if ($quotation->freight_option === 'freight_collect') {
                                                        if ($ordCurr === 'USD') $serviceFee = 50.00 / 56.0;
                                                        elseif ($ordCurr === 'EUR') $serviceFee = 50.00 / 62.0;
                                                        else $serviceFee = 50.00;
                                                    }
                                                    $grandTotal = $soNetTotal + $serviceFee;
                                                @endphp
                                                @if($orderDiscAmount > 0)
                                                <tr>
                                                    <td colspan="4" class="text-end text-danger"><strong>Order Discount{{ $orderDiscVal > 0 ? ' (' . $orderDiscVal . '%)' : ' (' . $ordSym . number_format($orderDiscAmount, 2) . ')' }}:</strong></td>
                                                    <td class="text-end fw-bold text-danger">- {{ $ordSym }} {{ number_format($orderDiscAmount, 2) }}</td>
                                                </tr>
                                                @endif
                                                @if($serviceFee > 0)
                                                <tr style="background-color: #fff3cd;">
                                                    <td colspan="4" class="text-end"><strong>Service Fee (Freight Collect):</strong></td>
                                                    <td class="text-end fw-bold text-success">{{ $ordSym }} {{ number_format($serviceFee, 2) }}</td>
                                                </tr>
                                                <tr style="background-color: #e8f5e9;">
                                                    <td colspan="4" class="text-end"><strong>Grand Total:</strong></td>
                                                    <td class="text-end fw-bold" style="font-size: 1.1rem; color: #2e7d32;">{{ $ordSym }} {{ number_format($grandTotal, 2) }}</td>
                                                </tr>
                                                @else
                                                <tr style="background-color: #e8f5e9;">
                                                    <td colspan="4" class="text-end"><strong>Grand Total:</strong></td>
                                                    <td class="text-end fw-bold" style="font-size: 1.1rem; color: #2e7d32;">{{ $ordSym }} {{ number_format($grandTotal, 2) }}</td>
                                                </tr>
                                                @endif
                                            </tfoot>
                                        </table>
                                    </div>
                                @endif

                                <a href="{{ route('marketing.sales-orders.show', $quotation->sales_order_id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="bi bi-eye me-1"></i>View Full Sales Order
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cargoItemsForm = document.getElementById('cargoItemsForm');
        if (cargoItemsForm) {
            cargoItemsForm.addEventListener('submit', function() {
                const boxesInput = document.querySelector('input[name="boxes_count"]');
                const freightInput = document.querySelector('input[name="estimated_freight"]');
                const notesInput = document.querySelector('textarea[name="logistics_notes"]');
                
                if (boxesInput && boxesInput.value !== '') {
                    document.getElementById('cargo_boxes_count_hidden').value = boxesInput.value;
                }
                if (freightInput && freightInput.value !== '') {
                    document.getElementById('cargo_estimated_freight_hidden').value = freightInput.value;
                }
                if (notesInput && notesInput.value) {
                    document.getElementById('cargo_logistics_notes_hidden').value = notesInput.value;
                }
            });
        }
    });

    // Add a new cargo item row
    function addCargoItemRow() {
        const tbody = document.getElementById('cargoItemsBody');
        const rowCount = tbody.querySelectorAll('tr').length + 1;
        
        const newRow = document.createElement('tr');
        newRow.className = 'cargo-row';
        newRow.setAttribute('data-row-num', rowCount);
        newRow.style.height = '2rem';
        newRow.innerHTML = `
            <td class="row-number" style="padding: 0.2rem; vertical-align: middle; font-size: 0.8rem;">${rowCount}</td>
            <td style="padding: 0.2rem;">
                <input type="number" name="cargo_qty[]" class="form-control form-control-sm" style="height: 1.5rem; font-size: 0.75rem;" min="1" placeholder="0">
            </td>
            <td style="padding: 0.2rem;">
                <select name="cargo_package_type[]" class="form-control form-control-sm" style="height: 1.5rem; font-size: 0.75rem;">
                    <option value="">Select</option>
                    <option value="Box, Bag, Pallet">Box</option>
                    <option value="Parcel">Parcel</option>
                    <option value="Crate">Crate</option>
                    <option value="Carton">Carton</option>
                    <option value="LCL">LCL</option>
                    <option value="FCL">FCL</option>
                    <option value="Pallets">Pallets</option>
                </select>
            </td>
            <td style="padding: 0.2rem;">
                <input type="text" name="cargo_dimensions[]" class="form-control form-control-sm" style="height: 1.5rem; font-size: 0.75rem;" placeholder="L×W×H">
            </td>
            <td style="padding: 0.2rem;">
                <input type="number" name="cargo_gross_weight[]" class="form-control form-control-sm" style="height: 1.5rem; font-size: 0.75rem;" step="0.01" placeholder="kg">
            </td>
            <td style="padding: 0.2rem;">
                <input type="number" name="cargo_vol_weight[]" class="form-control form-control-sm" style="height: 1.5rem; font-size: 0.75rem;" step="0.01" placeholder="kg">
            </td>
            <td style="text-align: center; padding: 0.2rem; vertical-align: middle;">
                <button type="button" class="btn btn-sm btn-danger" style="padding: 0.15rem 0.3rem; font-size: 0.7rem;" onclick="removeCargoRow(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(newRow);
        updateRowNumbers();
        
        // Scroll to the new row
        setTimeout(() => {
            newRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
    }

    // Remove a cargo item row
    function removeCargoRow(button) {
        const row = button.closest('tr');
        const tbody = document.getElementById('cargoItemsBody');

        if (tbody.querySelectorAll('tr').length === 1) {
            row.querySelectorAll('input, select').forEach((field) => {
                field.value = '';
            });
            return;
        }

        row.remove();
        updateRowNumbers();
    }

    // Update row numbers after adding/removing rows
    function updateRowNumbers() {
        const tbody = document.getElementById('cargoItemsBody');
        const rows = tbody.querySelectorAll('tr');
        rows.forEach((row, index) => {
            row.setAttribute('data-row-num', index + 1);
            row.querySelector('.row-number').textContent = index + 1;
        });
    }

    // Print ONLY Books/Items Breakdown (Item, Qty, Price, Amount)
    function printItemsBreakdownOnly() {
        var refNo = "{{ $quotation->quotation_number ?? ('FO-' . str_pad($quotation->id, 5, '0', STR_PAD_LEFT)) }}";
        var soNumber = "{{ $quotation->salesOrder->so_number ?? 'N/A' }}";
        var customerName = "{{ $quotation->salesOrder->customer->customer_name ?? ($quotation->salesOrder->company_name ?? 'N/A') }}";
        var dateStr = "{{ now()->format('M d, Y') }}";
        var logoUrl = window.location.origin + "{{ asset('images/claeritian_logo.png') }}";

        var rowsHtml = '';
        var itemsTable = document.querySelector('.table-responsive table.table-hover');
        if (!itemsTable) {
            alert('No items breakdown table found.');
            return;
        }

        var rows = itemsTable.querySelectorAll('tbody tr');
        var totalAmountVal = 0;
        var totalQtyCount = 0;

        rows.forEach(function(tr, idx) {
            var tds = tr.querySelectorAll('td');
            if (tds.length >= 8) {
                var itemName = tds[1].innerText.split('\n')[0].trim();
                var qtyStr = tds[2].innerText.trim();
                var unitPrice = tds[5].innerText.trim();
                var amount = tds[7].innerText.trim();
                var qtyNum = parseInt(qtyStr) || 0;
                totalQtyCount += qtyNum;

                var amtNum = parseFloat(amount.replace(/[^0-9.-]+/g, '')) || 0;
                totalAmountVal += amtNum;

                rowsHtml += `
                    <tr>
                        <td style="text-align: center;">${idx + 1}</td>
                        <td>${itemName}</td>
                        <td style="text-align: center; font-weight: bold;">${qtyStr}</td>
                        <td style="text-align: right;">${unitPrice}</td>
                        <td style="text-align: right; font-weight: bold;">${amount}</td>
                    </tr>
                `;
            }
        });

        var printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Books/Items Breakdown - ${refNo}</title>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; margin: 25px; color: #111; font-size: 12px; }
                .company-header { display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 12px; }
                .company-logo { height: 60px; width: auto; object-fit: contain; }
                .company-details { text-align: left; }
                .company-name { font-size: 17px; font-weight: 800; color: #D9251C; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
                .company-sub { font-size: 11px; color: #444; margin-bottom: 1px; }
                .document-banner { text-align: center; margin: 15px 0 18px 0; border-top: 2px solid #D9251C; border-bottom: 1px solid #ddd; padding: 8px 0; background: #fff8f8; }
                .document-title { font-size: 14px; font-weight: 800; color: #222; text-transform: uppercase; letter-spacing: 1px; margin: 0; }
                .meta-table { width: 100%; margin-bottom: 18px; font-size: 12px; border-collapse: collapse; background: #fafafa; border: 1px solid #e0e0e0; }
                .meta-table td { padding: 8px 12px; }
                .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px; }
                .items-table th { background-color: #D9251C; color: #ffffff; font-weight: bold; text-transform: uppercase; font-size: 11px; padding: 9px 10px; border: 1px solid #b71c1c; }
                .items-table td { border: 1px solid #e0e0e0; padding: 8px 10px; color: #222; }
                .items-table tr:nth-child(even) { background-color: #fcfcfc; }
                .tfoot-summary td { background-color: #f4f4f4; font-weight: bold; font-size: 13px; border-top: 2px solid #D9251C; padding: 10px; }
                @media print {
                    @page { margin: 12mm; }
                    body { margin: 0; }
                }
            </style>
        </head>
        <body>
            <div class="company-header">
                <img src="${logoUrl}" class="company-logo" alt="Claretian Logo" onerror="this.style.display='none'">
                <div class="company-details">
                    <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                    <div class="company-sub">8 Mayumi St., U.P. Village, Diliman, Quezon City 1101 Philippines</div>
                    <div class="company-sub">Tel. No.: (02) 921-3984 &nbsp;|&nbsp; Fax: 921-7429 &nbsp;|&nbsp; Email: info@claretianpublications.com</div>
                </div>
            </div>

            <div class="document-banner">
                <h3 class="document-title">FREIGHT QUOTATION — BOOKS & ITEMS BREAKDOWN</h3>
            </div>

            <table class="meta-table">
                <tr>
                    <td style="width: 50%;"><strong>Freight Quotation #:</strong> <span style="color: #D9251C; font-weight: bold;">${refNo}</span></td>
                    <td style="width: 50%; text-align: right;"><strong>Date Printed:</strong> ${dateStr}</td>
                </tr>
                <tr>
                    <td><strong>Sales Order #:</strong> ${soNumber}</td>
                    <td style="text-align: right;"><strong>Customer:</strong> ${customerName}</td>
                </tr>
            </table>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">#</th>
                        <th>Book / Product Name</th>
                        <th style="width: 80px; text-align: center;">QTY</th>
                        <th style="width: 120px; text-align: right;">Unit Price</th>
                        <th style="width: 130px; text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    ${rowsHtml}
                </tbody>
                <tfoot>
                    <tr class="tfoot-summary">
                        <td colspan="2" style="text-align: right;">Total Items Summary:</td>
                        <td style="text-align: center; color: #D9251C;">${totalQtyCount} pcs</td>
                        <td></td>
                        <td style="text-align: right; color: #D9251C;">₱ ${totalAmountVal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    </tr>
                </tfoot>
            </table>
        </body>
        </html>
        `;

        var printWindow = window.open('', '_blank', 'width=900,height=700');
        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.focus();
        setTimeout(function() {
            printWindow.print();
            printWindow.close();
        }, 400);
    }
</script>
</x-app-layout>
