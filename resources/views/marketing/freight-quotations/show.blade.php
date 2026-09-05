<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card" style="border-radius: 8px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);">
                    <!-- Header -->
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0"><i class="bi bi-file-earmark me-2"></i>Freight Quotation: {{ $quotation->quote_number }}</h5>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-light text-dark fw-bold shadow-sm" id="btnToggleEdit" onclick="toggleEditMode(true)">
                                <i class="bi bi-pencil-square me-1 text-primary"></i>Edit Quotation & Freight
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-light text-white d-none" id="btnCancelEditHeader" onclick="toggleEditMode(false)">
                                <i class="bi bi-x-circle me-1"></i>Cancel Edit
                            </button>
                            <span class="badge bg-light text-dark">
                                @php
                                    $statusClass = [
                                        'draft' => 'primary',
                                        'pending_logistics' => 'warning',
                                        'approved' => 'success',
                                        'linked_to_so' => 'info',
                                    ];
                                @endphp
                                {{ ucfirst(str_replace('_', ' ', $quotation->workflow_status)) }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- View Mode Container -->
                        <div id="freightViewMode" class="{{ request('edit') ? 'd-none' : '' }}">
                            <!-- Status Timeline -->
                        <div class="mb-4">
                            <div class="row text-center">
                                <div class="col-3">
                                    <div class="step-indicator {{ in_array($quotation->workflow_status, ['draft', 'pending_logistics', 'approved', 'linked_to_so']) ? 'completed' : '' }}">
                                        <div class="step-circle">1</div>
                                        <small>Draft</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="step-indicator {{ in_array($quotation->workflow_status, ['approved', 'linked_to_so']) ? 'completed' : ($quotation->workflow_status === 'pending_logistics' ? 'active' : '') }}">
                                        <div class="step-circle">2</div>
                                        <small>Logistics Review</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="step-indicator {{ $quotation->workflow_status === 'linked_to_so' ? 'completed' : ($quotation->workflow_status === 'approved' ? 'active' : '') }}">
                                        <div class="step-circle">3</div>
                                        <small>Approved</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="step-indicator {{ $quotation->workflow_status === 'linked_to_so' ? 'active' : '' }}">
                                        <div class="step-circle">4</div>
                                        <small>Sales Order</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Quotation Information Section -->
                        <div style="background: #f8f9fa; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
                            <h5 class="mb-3"><i class="bi bi-info-circle me-2"></i>Quotation Information</h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <small class="text-muted d-block mb-1"><strong>Quote Number</strong></small>
                                        <p class="mb-0">{{ $quotation->quote_number }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <small class="text-muted d-block mb-1"><strong>Created By</strong></small>
                                        <p class="mb-0">{{ $quotation->createdBy->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <small class="text-muted d-block mb-1"><strong>Created Date</strong></small>
                                        <p class="mb-0">{{ $quotation->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <small class="text-muted d-block mb-1"><strong>Service Mode</strong></small>
                                        <p class="mb-0">{{ $quotation->service_mode }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <small class="text-muted d-block mb-1"><strong>Forwarder</strong></small>
                                        <p class="mb-0">{{ $quotation->forwarder ?? $quotation->salesOrder?->forwarder ?? $quotation->freight_mode ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <small class="text-muted d-block mb-1"><strong>Transaction Type</strong></small>
                                        <p class="mb-0">{{ $quotation->transaction_type ? ucwords(str_replace('_', ' ', $quotation->transaction_type)) : 'Paid' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <small class="text-muted d-block mb-1"><strong>Freight Option</strong></small>
                                        <p class="mb-0">{{ $quotation->freight_option ? ucwords(str_replace('_', ' ', $quotation->freight_option)) : 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <small class="text-muted d-block mb-1"><strong>Currency</strong></small>
                                        <p class="mb-0 fw-bold text-danger">{{ $quotation->currency ?? 'PHP' }} ({{ ($quotation->currency ?? 'PHP') === 'USD' ? '$' : '₱' }})</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shipment Details -->
                        <div style="background: #f8f9fa; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
                            <h5 class="mb-3"><i class="bi bi-geo-alt me-2"></i>Shipment Details</h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6 class="mb-2">📍 Origin (Pick-up)</h6>
                                    <p class="mb-1"><strong>Contact:</strong> {{ $quotation->origin_contact }}</p>
                                    <p class="mb-1"><strong>Province:</strong> {{ $quotation->origin_province }}</p>
                                    <p class="mb-0"><strong>Address:</strong><br> {{ $quotation->origin_address }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-2">📍 Destination (Delivery)</h6>
                                    <p class="mb-1"><strong>Contact:</strong> {{ $quotation->destination_contact }}</p>
                                    <p class="mb-1"><strong>Province:</strong> {{ $quotation->destination_province }}</p>
                                    <p class="mb-0"><strong>Address:</strong><br> {{ $quotation->destination_address }}</p>
                                </div>
                            </div>

                            @if($quotation->respondedBy)
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Reviewed By:</strong> {{ $quotation->respondedBy->name }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-0"><strong>Reviewed Date:</strong> {{ $quotation->responded_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Cargo Items -->
                        <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-box me-2"></i><strong>Cargo Items</strong></h6>

                        @if($quotation->cargo_items)
                            <div class="table-responsive mb-4">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Quantity</th>
                                            <th>Package Type</th>
                                            <th>Dimensions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $cargoItems = is_string($quotation->cargo_items) ? json_decode($quotation->cargo_items, true) : $quotation->cargo_items;
                                        @endphp
                                        @foreach($cargoItems as $item)
                                            <tr>
                                                <td>{{ $item['qty'] }}</td>
                                                <td>{{ $item['package_type'] ?? '-' }}</td>
                                                <td>{{ $item['dimensions'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning mb-4">No cargo items available.</div>
                        @endif

                        <!-- All Available Books List -->
                        <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-book me-2"></i><strong>All Available Books</strong></h6>

                        @if(isset($allBooks) && $allBooks && $allBooks->count() > 0)
                            <div class="table-responsive mb-4">
                                <table class="table table-hover table-bordered display" id="allBooksTable" style="width: 100%;">
                                    <thead class="table-success">
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th>Book Name</th>
                                            <th style="width: 100px;" class="text-center">SKU</th>
                                            <th style="width: 100px;" class="text-center">Weight (g)</th>
                                            <th style="width: 100px;" class="text-center">Stock</th>
                                            <th style="width: 120px;" class="text-end">Price</th>
                                            <th style="width: 150px;">Author</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allBooks as $key => $book)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <strong>{{ $book->name }}</strong>
                                                    @if($book->category)
                                                        <br><small class="text-muted">Category: {{ $book->category }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $book->sku ?? '-' }}</td>
                                                <td class="text-center">
                                                    @if((float)$book->weight > 0)
                                                        {{ number_format((float)$book->weight, 2) }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge {{ $book->stock > 10 ? 'bg-success' : ($book->stock > 0 ? 'bg-warning' : 'bg-danger') }}">
                                                        {{ $book->stock ?? 0 }}
                                                    </span>
                                                </td>
                                                <td class="text-end">₱ {{ number_format((float)$book->price, 2) }}</td>
                                                <td>{{ $book->author ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-4">
                                <i class="bi bi-info-circle me-2"></i>No books available in the system.
                            </div>
                        @endif

                        <!-- Logistics Response (if approved or linked) -->
                        @if(in_array($quotation->workflow_status, ['approved', 'linked_to_so']) || $quotation->status === 'approved')
                            @php
                                $fqCurr = $quotation->currency ?? ($quotation->salesOrder->currency ?? 'PHP');
                                $fqSym = ($fqCurr === 'USD' ? '$' : '₱');
                                $displayTotal = $quotation->total_amount ?? ($quotation->estimated_freight + ($quotation->handling_fee ?? 0));
                            @endphp
                            <div style="background: #f0fdf4; padding: 1rem; border-radius: 6px; border: 2px solid #10b981; margin-bottom: 1.5rem;">
                                <h5 class="mb-3"><i class="bi bi-check-circle me-2"></i><strong>Logistics Quotation</strong></h5>
                                
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <small class="text-muted d-block mb-1"><strong>Boxes Count</strong></small>
                                            <p class="mb-0 fs-5"><strong>{{ $quotation->boxes_count ?? '-' }}</strong></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <small class="text-muted d-block mb-1"><strong>Estimated Freight</strong></small>
                                            <p class="mb-0 fs-5"><strong class="text-danger">{{ $fqSym }} {{ number_format($quotation->estimated_freight, 2) }}</strong></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <small class="text-muted d-block mb-1"><strong>Handling Fee</strong></small>
                                            <p class="mb-0 fs-5"><strong class="text-danger">{{ $fqSym }} {{ number_format($quotation->handling_fee ?? 0, 2) }}</strong></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <small class="text-muted d-block mb-1"><strong>Total Amount</strong></small>
                                        <p class="mb-0 fs-4"><strong class="text-danger">{{ $fqSym }} {{ number_format($displayTotal, 2) }}</strong></p>
                                    </div>
                                </div>

                                @if($quotation->logistics_notes)
                                    <div class="alert alert-info mt-3 mb-0">
                                        <strong>📝 Logistics Notes:</strong><br>
                                        {{ $quotation->logistics_notes }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        <hr>

                        <!-- Sales Order Section (if linked) -->
                        @if($quotation->sales_order_id && $quotation->salesOrder)
                            <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-file-earmark-arrow-right me-2"></i><strong>Sales Order Details</strong></h5>

                            <div style="background: #f8f9fa; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <small class="text-muted d-block mb-1"><strong>SO Number</strong></small>
                                        <p class="mb-0"><strong>{{ $quotation->salesOrder->so_number }}</strong></p>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block mb-1"><strong>Company</strong></small>
                                        <p class="mb-0">{{ $quotation->salesOrder->customer?->customer_name ?? ($quotation->customer?->customer_name ?? 'N/A') }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block mb-1"><strong>Customer Name</strong></small>
                                        <p class="mb-0">{{ $quotation->salesOrder->customer_representative ?: ($quotation->customer_representative ?: 'N/A') }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block mb-1"><strong>Status</strong></small>
                                        <p class="mb-0">
                                            <span class="badge bg-info">{{ ucfirst($quotation->salesOrder->status) }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block mb-1"><strong>Date</strong></small>
                                        <p class="mb-0">{{ $quotation->salesOrder->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block mb-1"><strong>Delivery Address</strong></small>
                                        <p class="mb-0">{{ $quotation->salesOrder->billing_address ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block mb-1"><strong>Transaction Type</strong></small>
                                        <p class="mb-0">{{ ucfirst(str_replace('_', ' ', $quotation->salesOrder->type)) }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- SO Items Table -->
                            @if($quotation->salesOrder->items && $quotation->salesOrder->items->count() > 0)
                                <h6 class="border-bottom pb-2 mb-3"><strong> Sales Order Items</strong></h6>
                                <div class="table-responsive mb-4">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-primary">
                                            <tr>
                                                <th style="width: 60px;">QTY</th>
                                                <th>PRODUCT</th>
                                                <th style="width: 80px;" class="text-center">WEIGHT (g)</th>
                                                <th style="width: 100px;" class="text-center">TOTAL WEIGHT</th>
                                                <th style="width: 100px;" class="text-end">UNIT PRICE</th>
                                                <th style="width: 100px;" class="text-end">DISCOUNT</th>
                                                <th style="width: 100px;" class="text-end">AMOUNT</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $soSubtotal = 0; $totalWeight = 0; $grossSubtotal = 0; $totalItemDiscount = 0; @endphp
                                            @foreach($quotation->salesOrder->items as $item)
                                                @php
                                                    $product = $item->product ?? $item->book ?? $item->bundle ?? $item->bookIndex;
                                                    $weight = (float)($product?->weight ?? 0);
                                                    $quantity = (int)($item->quantity ?? 0);
                                                    $price = (float)($item->price ?? 0);
                                                    $discVal = (float)($item->discount_value ?? 0);
                                                    $discType = $item->discount_type ?? 'percentage';
                                                    $itemGross = $quantity * $price;
                                                    $discAmt = $item->discount_amount ?? ($discType === 'percentage' ? $itemGross * ($discVal / 100) : $discVal);
                                                    $itemSubtotal = $item->subtotal ?? max(0, $itemGross - $discAmt);
                                                    $itemTotalWeight = $quantity * $weight;
                                                    $totalWeight += $itemTotalWeight;
                                                    $soSubtotal += $itemSubtotal;
                                                    $grossSubtotal += $itemGross;
                                                    $totalItemDiscount += $discAmt;
                                                @endphp
                                                <tr>
                                                    <td>{{ $quantity }}</td>
                                                    <td>{{ $item->bookIndex ? $item->bookIndex->display_name : ($product?->name ?? $item->product_name ?? 'N/A') }}</td>
                                                    <td class="text-center">
                                                        @if($weight > 0)
                                                            {{ number_format($weight, 2) }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center fw-bold">
                                                        @if($weight > 0)
                                                            {{ number_format($itemTotalWeight, 2) }} g
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">{{ $fqSym }} {{ number_format($price, 2) }}</td>
                                                    <td class="text-end text-danger">
                                                        @if($discVal > 0)
                                                            {{ $discType === 'percentage' ? $discVal . '%' : $fqSym . number_format($discVal, 2) }}
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td class="text-end fw-bold">{{ $fqSym }} {{ number_format($itemSubtotal, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            @php
                                                $orderDiscAmount = (float)($quotation->salesOrder->discount_amount ?? 0);
                                                $orderDiscVal = (float)($quotation->salesOrder->discount_percentage ?? 0);
                                                $soNetTotal = max(0, $soSubtotal - $orderDiscAmount);
                                                $serviceFee = $quotation->freight_option === 'freight_collect' ? 50 : 0;
                                                $grandTotal = $soNetTotal + $serviceFee;
                                            @endphp
                                            @if($totalItemDiscount > 0)
                                            <tr>
                                                <td colspan="6" class="text-end"><strong>Gross Subtotal:</strong></td>
                                                <td class="text-end fw-bold">{{ $fqSym }} {{ number_format($grossSubtotal, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="text-end text-danger"><strong>Item Discounts:</strong></td>
                                                <td class="text-end fw-bold text-danger">- {{ $fqSym }} {{ number_format($totalItemDiscount, 2) }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>Items Subtotal:</strong></td>
                                                <td class="text-center fw-bold">
                                                    @if($totalWeight > 0)
                                                        {{ number_format($totalWeight, 2) }} g
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td colspan="2"></td>
                                                <td class="text-end fw-bold">{{ $fqSym }} {{ number_format($soSubtotal, 2) }}</td>
                                            </tr>
                                            @if($orderDiscAmount > 0)
                                            <tr>
                                                <td colspan="6" class="text-end text-danger"><strong>Order Discount{{ $orderDiscVal > 0 ? ' (' . $orderDiscVal . '%)' : ' (' . $fqSym . number_format($orderDiscAmount, 2) . ')' }}:</strong></td>
                                                <td class="text-end fw-bold text-danger">- {{ $fqSym }} {{ number_format($orderDiscAmount, 2) }}</td>
                                            </tr>
                                            @endif
                                            @if($serviceFee > 0)
                                            <tr style="background-color: #fff3cd;">
                                                <td colspan="6" class="text-end text-success"><strong>Service Fee (Freight Collect):</strong></td>
                                                <td class="text-end fw-bold text-success">{{ $fqSym }} {{ number_format($serviceFee, 2) }}</td>
                                            </tr>
                                            @endif
                                            <tr style="background-color: #e8f5e9;">
                                                <td colspan="6" class="text-end"><strong>Grand Total:</strong></td>
                                                <td class="text-end fw-bold fs-6" style="color: #2e7d32;">{{ $fqSym }} {{ number_format($grandTotal, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif

                            <hr>
                        @endif

                        @php
                            $popPath = $quotation->proof_of_payment ?: ($quotation->salesOrder?->proof_of_payment ?? null);
                        @endphp
                        @if(in_array($quotation->workflow_status, ['approved', 'linked_to_so']) || $quotation->status === 'approved')
                            <div class="proof-of-payment-box border rounded p-2 px-3 mb-3 bg-white shadow-sm" style="height: auto !important; min-height: 0 !important; max-height: max-content !important;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <!-- Title & Info -->
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-primary border" style="width: 32px; height: 32px;">
                                            <i class="bi bi-receipt"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <strong style="font-size: 0.85rem;">Proof of Payment</strong>
                                                <span class="badge {{ $popPath ? 'bg-success text-white' : 'bg-light text-muted border' }}" style="font-size: 0.7rem;">
                                                    {{ $popPath ? 'Attached' : 'Optional' }}
                                                </span>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                @if($popPath)
                                                    <span class="text-dark fw-medium">{{ basename($popPath) }}</span>
                                                @else
                                                    Attach deposit slip, bank screenshot, or receipt
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions / Upload form -->
                                    <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                                        @if($popPath)
                                            <a href="{{ asset('storage/' . $popPath) }}" target="_blank" class="btn btn-sm btn-outline-success py-1 px-2" style="font-size: 0.78rem;">
                                                <i class="bi bi-eye me-1"></i>View
                                            </a>
                                            <a href="{{ asset('storage/' . $popPath) }}" download class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 0.78rem;">
                                                <i class="bi bi-download me-1"></i>Download
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 0.78rem;" onclick="document.getElementById('compactPopForm').classList.toggle('d-none')">
                                                <i class="bi bi-arrow-repeat me-1"></i>Replace
                                            </button>
                                        @endif

                                        <form id="compactPopForm" action="{{ route('marketing.freight-quotations.upload-proof-of-payment', $quotation->id) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-1 m-0 {{ $popPath ? 'd-none' : '' }}">
                                            @csrf
                                            <div class="input-group input-group-sm" style="max-width: 320px;">
                                                <input type="file" name="proof_of_payment" class="form-control form-control-sm @error('proof_of_payment') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required style="font-size: 0.75rem;">
                                                <button type="submit" class="btn btn-sm btn-danger px-2" style="font-size: 0.78rem;">
                                                    <i class="bi bi-cloud-upload me-1"></i>Upload
                                                </button>
                                            </div>
                                            @if($popPath)
                                                <button type="button" class="btn btn-sm btn-light border py-1 px-2" style="font-size: 0.75rem;" onclick="document.getElementById('compactPopForm').classList.add('d-none')">Cancel</button>
                                            @endif
                                        </form>
                                    </div>
                                </div>
                                @error('proof_of_payment')
                                    <div class="text-danger small mt-1" style="font-size: 0.75rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        @php
                            $isFordSource = !empty($isFord) || $quotation->source === 'ford' || (isset($quotation->salesOrder) && ($quotation->salesOrder->type === 'foreign' || str_contains($quotation->salesOrder->so_number ?? '', 'FORD')));
                            $backRoute = $isFordSource ? route($indexRoute ?? 'production.ford.freight-quotation.index') : route('marketing.freight-quotations.list');
                        @endphp
                        <div class="d-flex gap-2 justify-content-between flex-wrap" style="padding-top: 1.5rem;">
                            <div class="d-flex gap-2">
                                <a href="{{ $backRoute }}" class="btn btn-light border">
                                    <i class="bi bi-arrow-left me-1"></i>Back
                                </a>
                                <button type="button" class="btn btn-outline-primary" onclick="toggleEditMode(true)">
                                    <i class="bi bi-pencil-square me-1"></i>Edit Freight & Quotation
                                </button>
                            </div>

                            <div>
                                @if(in_array($quotation->workflow_status, ['approved', 'linked_to_so']))
                                    @if(!$quotation->sales_order_id)
                                        <form method="POST" action="{{ route('marketing.freight-quotations.create-so-directly', $quotation->id) }}" style="display: inline;">
                                            @csrf
                                            @if($isFordSource)
                                                <input type="hidden" name="source" value="ford">
                                            @endif
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-plus-circle me-1"></i>Create Sales Order
                                            </button>
                                        </form>
                                    @else
                                        @if($quotation->salesOrder && $quotation->salesOrder->status === 'draft')
                                            <form method="POST" action="{{ route('marketing.sales-orders.proceed-to-final', $quotation->sales_order_id) }}" style="display: inline;">
                                                @csrf
                                                @if($isFordSource)
                                                    <input type="hidden" name="source" value="ford">
                                                @endif
                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-arrow-right-circle me-1"></i>Proceed Sales Order (SO #{{ $quotation->salesOrder->so_number }})
                                                </button>
                                            </form>
                                        @else
                                            @php
                                                $targetSoRoute = $isFordSource
                                                    ? (Route::has('production.ford.sales-order.review') && $quotation->sales_order_id ? route('production.ford.sales-order.review', $quotation->sales_order_id) : (Route::has('production.ford.sales-order') ? route('production.ford.sales-order') : route('marketing.sales-orders.detail', $quotation->sales_order_id)))
                                                    : route('marketing.sales-orders.detail', $quotation->sales_order_id);
                                            @endphp
                                            <a href="{{ $targetSoRoute }}" class="btn btn-info">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>View Sales Order (SO #{{ $quotation->salesOrder->so_number ?? '' }})
                                            </a>
                                        @endif
                                    @endif
                                @elseif($quotation->workflow_status === 'draft')
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Waiting for logistics to review and quote the freight charges...
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div> <!-- End #freightViewMode -->

                        <!-- Full Unified On-Page Edit Form ("Buo") -->
                        @php
                            $updateAction = $isFordSource
                                ? route('production.ford.freight-quotation.update', $quotation->id)
                                : route('marketing.freight-quotations.update', $quotation->id);
                            $cargoItemsArray = is_string($quotation->cargo_items) ? json_decode($quotation->cargo_items, true) : ($quotation->cargo_items ?? []);
                            if (!is_array($cargoItemsArray)) { $cargoItemsArray = []; }
                        @endphp

                        <div id="freightEditMode" class="{{ request('edit') ? '' : 'd-none' }}">
                            <form action="{{ $updateAction }}" method="POST" enctype="multipart/form-data" id="editFreightFullForm">
                                @csrf
                                @method('PUT')

                                <!-- Form Top Action Bar -->
                                <div class="p-3 mb-4 rounded border bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h5 class="mb-0 text-dark fw-bold">
                                            <i class="bi bi-pencil-square text-primary me-2"></i>Edit Full Quotation & Freight Details
                                        </h5>
                                        <small class="text-muted">You can edit quotation settings, shipment addresses, cargo packages, logistics charges, and sales order items below.</small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-secondary" onclick="toggleEditMode(false)">
                                            <i class="bi bi-x-circle me-1"></i>Cancel
                                        </button>
                                        <button type="submit" class="btn btn-danger px-3 fw-bold">
                                            <i class="bi bi-check-circle me-1"></i>Save Changes
                                        </button>
                                    </div>
                                </div>

                                <!-- 1. Quotation & Shipment Information -->
                                <div class="border rounded mb-4 shadow-sm bg-white" style="height: auto !important; min-height: 0 !important;">
                                    <div class="bg-light border-bottom p-2 px-3">
                                        <strong class="text-uppercase text-secondary" style="font-size: 0.85rem;">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>1. Quotation & Shipment Information
                                        </strong>
                                    </div>
                                    <div class="p-3">
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">Service Mode</label>
                                                <select name="service_mode" class="form-select form-select-sm">
                                                    @foreach(['Door to Door', 'Pier to Pier', 'Door to Pier', 'Pier to Door', 'Air Freight', 'Sea Freight'] as $sm)
                                                        <option value="{{ $sm }}" {{ ($quotation->service_mode ?? '') === $sm ? 'selected' : '' }}>{{ $sm }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">Forwarder / Carrier</label>
                                                <input type="text" name="forwarder" class="form-control form-control-sm" value="{{ $quotation->forwarder ?? $quotation->freight_mode ?? '' }}" placeholder="e.g. Fedex, DHL, 2GO">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">Freight Option</label>
                                                <select name="freight_option" class="form-select form-select-sm" id="edit_freight_option" onchange="calculateFreightTotal()">
                                                    <option value="freight_collect" {{ ($quotation->freight_option ?? '') === 'freight_collect' ? 'selected' : '' }}>Freight Collect (+Service Fee)</option>
                                                    <option value="freight_billing" {{ ($quotation->freight_option ?? '') === 'freight_billing' ? 'selected' : '' }}>Freight Billing</option>
                                                    <option value="bill_client" {{ ($quotation->freight_option ?? '') === 'bill_client' ? 'selected' : '' }}>Bill Client</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">Currency</label>
                                                <select name="currency" class="form-select form-select-sm" id="edit_currency" onchange="calculateFreightTotal()">
                                                    <option value="PHP" {{ ($quotation->currency ?? 'PHP') === 'PHP' ? 'selected' : '' }}>PHP (₱)</option>
                                                    <option value="USD" {{ ($quotation->currency ?? '') === 'USD' ? 'selected' : '' }}>USD ($)</option>
                                                    <option value="EUR" {{ ($quotation->currency ?? '') === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Customer Representative</label>
                                                <input type="text" name="customer_representative" class="form-control form-control-sm" value="{{ $quotation->customer_representative ?? '' }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Terms</label>
                                                <input type="text" name="terms" class="form-control form-control-sm" value="{{ $quotation->terms ?? '' }}" placeholder="e.g. 30 Days, COD">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Quote Date</label>
                                                <input type="date" name="quote_date" class="form-control form-control-sm" value="{{ $quotation->quote_date ? \Carbon\Carbon::parse($quotation->quote_date)->format('Y-m-d') : date('Y-m-d') }}">
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <!-- Origin -->
                                            <div class="col-md-6 border-end pe-md-3">
                                                <h6 class="small fw-bold text-primary mb-2">📍 Origin (Pick-up)</h6>
                                                <div class="mb-2">
                                                    <label class="form-label small">Origin Contact</label>
                                                    <input type="text" name="origin_contact" class="form-control form-control-sm" value="{{ $quotation->origin_contact ?? '' }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small">Origin Province</label>
                                                    <input type="text" name="origin_province" class="form-control form-control-sm" value="{{ $quotation->origin_province ?? '' }}">
                                                </div>
                                                <div>
                                                    <label class="form-label small">Origin Address</label>
                                                    <textarea name="origin_address" rows="2" class="form-control form-control-sm">{{ $quotation->origin_address ?? '' }}</textarea>
                                                </div>
                                            </div>
                                            <!-- Destination -->
                                            <div class="col-md-6 ps-md-3">
                                                <h6 class="small fw-bold text-success mb-2">📍 Destination (Delivery)</h6>
                                                <div class="mb-2">
                                                    <label class="form-label small">Destination Contact</label>
                                                    <input type="text" name="destination_contact" class="form-control form-control-sm" value="{{ $quotation->destination_contact ?? '' }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small">Destination Province</label>
                                                    <input type="text" name="destination_province" class="form-control form-control-sm" value="{{ $quotation->destination_province ?? '' }}">
                                                </div>
                                                <div>
                                                    <label class="form-label small">Destination Address</label>
                                                    <textarea name="destination_address" rows="2" class="form-control form-control-sm">{{ $quotation->destination_address ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2. Cargo Packages -->
                                <div class="border rounded mb-4 shadow-sm bg-white" style="height: auto !important; min-height: 0 !important;">
                                    <div class="bg-light border-bottom p-2 px-3 d-flex justify-content-between align-items-center">
                                        <strong class="text-uppercase text-secondary" style="font-size: 0.85rem;">
                                            <i class="bi bi-box-seam text-warning me-1"></i>2. Cargo Packages
                                        </strong>
                                        <button type="button" class="btn btn-sm btn-primary py-0 px-2" style="font-size: 0.8rem;" onclick="addCargoRow()">
                                            <i class="bi bi-plus-circle me-1"></i>Add Package Row
                                        </button>
                                    </div>
                                    <div class="p-2">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered align-middle mb-0" id="cargoItemsTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 120px;">Quantity</th>
                                                        <th>Package Type</th>
                                                        <th>Dimensions (L x W x H)</th>
                                                        <th style="width: 60px;" class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="cargoItemsTableBody">
                                                    @forelse($cargoItemsArray as $item)
                                                        <tr>
                                                            <td>
                                                                <input type="number" name="cargo_qty[]" class="form-control form-control-sm" min="1" value="{{ $item['qty'] ?? 1 }}" required>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="cargo_package_type[]" class="form-control form-control-sm" placeholder="e.g. Box, Crate" value="{{ $item['package_type'] ?? 'Box' }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="cargo_dimensions[]" class="form-control form-control-sm" placeholder="e.g. 30x20x15 cm" value="{{ $item['dimensions'] ?? '' }}">
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="removeCargoRow(this)">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td>
                                                                <input type="number" name="cargo_qty[]" class="form-control form-control-sm" min="1" value="1" required>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="cargo_package_type[]" class="form-control form-control-sm" placeholder="e.g. Box, Crate" value="Box">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="cargo_dimensions[]" class="form-control form-control-sm" placeholder="e.g. 30x20x15 cm" value="">
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="removeCargoRow(this)">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3. Logistics & Freight Charges -->
                                <div class="border rounded mb-4 shadow-sm bg-white" style="height: auto !important; min-height: 0 !important;">
                                    <div class="bg-light border-bottom p-2 px-3">
                                        <strong class="text-uppercase text-secondary" style="font-size: 0.85rem;">
                                            <i class="bi bi-calculator text-success me-1"></i>3. Logistics & Freight Charges
                                        </strong>
                                    </div>
                                    <div class="p-3">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">Boxes Count</label>
                                                <input type="number" name="boxes_count" class="form-control form-control-sm" min="0" value="{{ $quotation->boxes_count ?? 1 }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">Estimated Freight (<span class="currency-symbol">{{ $quotation->currency ?? 'PHP' }}</span>)</label>
                                                <input type="number" step="0.01" min="0" name="estimated_freight" id="edit_estimated_freight" class="form-control form-control-sm" value="{{ $quotation->estimated_freight ?? 0 }}" oninput="calculateFreightTotal()">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">Handling Fee (<span class="currency-symbol">{{ $quotation->currency ?? 'PHP' }}</span>)</label>
                                                <input type="number" step="0.01" min="0" name="handling_fee" id="edit_handling_fee" class="form-control form-control-sm" value="{{ $quotation->handling_fee ?? 0 }}" oninput="calculateFreightTotal()">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold text-success">Total Freight Amount</label>
                                                <input type="number" step="0.01" min="0" name="total_amount" id="edit_total_amount" class="form-control form-control-sm fw-bold border-success text-success" value="{{ $quotation->total_amount ?? (($quotation->estimated_freight ?? 0) + ($quotation->handling_fee ?? 0)) }}">
                                                <div class="form-text" style="font-size: 0.7rem;">Auto-sums Est. Freight + Handling Fee (+ Service Fee if Freight Collect)</div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-bold">Logistics Notes / Instructions</label>
                                                <textarea name="logistics_notes" rows="2" class="form-control form-control-sm" placeholder="e.g. Via Fedex 4Kilos, special handling...">{{ $quotation->logistics_notes }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 4. Sales Order Items (with Add Item feature) -->
                                <div class="border rounded mb-4 shadow-sm bg-white" style="height: auto !important; min-height: 0 !important;">
                                    <div class="bg-light border-bottom p-2 px-3 d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong class="text-uppercase text-secondary" style="font-size: 0.85rem;">
                                                <i class="bi bi-cart-check text-primary me-1"></i>4. Sales Order Items
                                            </strong>
                                            @if($quotation->salesOrder)
                                                <span class="badge bg-primary ms-1" style="font-size: 0.75rem;">SO #{{ $quotation->salesOrder->so_number }}</span>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-sm btn-success py-0 px-2 fw-bold" style="font-size: 0.8rem;" onclick="addSOItemOnEdit()">
                                            <i class="bi bi-plus-circle me-1"></i>Add Item
                                        </button>
                                    </div>
                                    <div class="p-2">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered align-middle mb-0" id="editSoItemsTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="min-width: 250px;">Product / Item</th>
                                                        <th style="width: 100px;">Quantity</th>
                                                        <th style="width: 120px;">Unit Price</th>
                                                        <th style="width: 110px;">Discount Val</th>
                                                        <th style="width: 100px;">Disc Type</th>
                                                        <th style="width: 120px;" class="text-end">Subtotal</th>
                                                        <th style="width: 50px;" class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="editSoItemsTableBody">
                                                    @php
                                                        $soItems = $quotation->salesOrder?->items ?? collect();
                                                    @endphp
                                                    @forelse($soItems as $index => $soItem)
                                                        @php
                                                            $prodName = $soItem->bookIndex ? $soItem->bookIndex->display_name : ($soItem->product?->name ?? $soItem->book?->name ?? $soItem->bundle?->name ?? 'Product');
                                                            $itemGross = (float)$soItem->quantity * (float)$soItem->price;
                                                            $itemDiscAmount = ($soItem->discount_type ?? 'percentage') === 'percentage'
                                                                ? $itemGross * ((float)($soItem->discount_value ?? 0) / 100)
                                                                : (float)($soItem->discount_value ?? 0);
                                                            $itemSubtotal = max(0, $itemGross - $itemDiscAmount);
                                                        @endphp
                                                        <tr class="so-item-row" data-index="{{ $index }}">
                                                            <td>
                                                                <input type="hidden" name="so_items[{{ $index }}][id]" value="{{ $soItem->id }}">
                                                                <strong>{{ $prodName }}</strong>
                                                            </td>
                                                            <td>
                                                                <input type="number" min="1" name="so_items[{{ $index }}][quantity]" class="form-control form-control-sm edit-item-qty" value="{{ $soItem->quantity }}" oninput="recalcEditRow(this)">
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" min="0" name="so_items[{{ $index }}][price]" class="form-control form-control-sm edit-item-price" value="{{ $soItem->price }}" oninput="recalcEditRow(this)">
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" min="0" name="so_items[{{ $index }}][discount_value]" class="form-control form-control-sm edit-item-discount" value="{{ $soItem->discount_value ?? 0 }}" oninput="recalcEditRow(this)">
                                                            </td>
                                                            <td>
                                                                <select name="so_items[{{ $index }}][discount_type]" class="form-select form-select-sm edit-item-discount-type" onchange="recalcEditRow(this)">
                                                                    <option value="percentage" {{ ($soItem->discount_type ?? 'percentage') === 'percentage' ? 'selected' : '' }}>%</option>
                                                                    <option value="amount" {{ ($soItem->discount_type ?? '') === 'amount' ? 'selected' : '' }}>Amount</option>
                                                                </select>
                                                            </td>
                                                            <td class="text-end fw-bold edit-item-subtotal">
                                                                {{ number_format($itemSubtotal, 2) }}
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="removeSOItemOnEdit(this)">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr id="noSoItemsPlaceholder">
                                                            <td colspan="7" class="text-center text-muted py-3">
                                                                No items added yet. Click <strong><i class="bi bi-plus-circle text-success"></i> Add Item</strong> to add products.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <th colspan="5" class="text-end">Items Subtotal:</th>
                                                        <th class="text-end text-primary fw-bold" id="editSoTotalDisplay">0.00</th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- 5. Proof of Payment (Compact Box) -->
                                <div class="proof-of-payment-box border rounded p-3 mb-4 bg-white shadow-sm" style="height: auto !important; min-height: 0 !important; max-height: max-content !important;">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-primary border" style="width: 32px; height: 32px;">
                                                <i class="bi bi-receipt"></i>
                                            </div>
                                            <div>
                                                <strong style="font-size: 0.85rem;">5. Proof of Payment</strong>
                                                <span class="badge {{ $popPath ? 'bg-success text-white' : 'bg-light text-muted border' }} ms-1" style="font-size: 0.7rem;">
                                                    {{ $popPath ? 'Attached' : 'Optional' }}
                                                </span>
                                            </div>
                                        </div>
                                        @if($popPath)
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="small text-muted"><i class="bi bi-file-earmark-check me-1"></i>{{ basename($popPath) }}</span>
                                                <a href="{{ asset('storage/' . $popPath) }}" target="_blank" class="btn btn-sm btn-outline-success py-1 px-2" style="font-size: 0.78rem;">
                                                    <i class="bi bi-eye me-1"></i>View Current
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row align-items-center g-2">
                                        <div class="col-md-6">
                                            <input type="file" name="proof_of_payment" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                        <div class="col-md-6">
                                            <span class="text-muted" style="font-size: 0.75rem;">
                                                {{ $popPath ? 'Upload a file here if you wish to replace the current attachment.' : 'Upload deposit slip, bank transfer screenshot, or receipt (PDF, JPG, PNG).' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Bottom Action Bar -->
                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <button type="button" class="btn btn-light border px-3" onclick="toggleEditMode(false)">
                                        <i class="bi bi-arrow-left me-1"></i>Cancel & Back to View
                                    </button>
                                    <button type="submit" class="btn btn-danger px-4 fw-bold shadow-sm">
                                        <i class="bi bi-check-circle me-1"></i>Save All Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Product Source Select for Add Item Template -->
    <select id="editProductSource" class="d-none">
        <option value="">-- Select Product --</option>
        @if(isset($products) && count($products) > 0)
            @foreach($products as $prod)
                <option value="{{ $prod->id }}" data-price="{{ $prod->price ?? 0 }}" data-name="{{ $prod->name ?? $prod->display_name }}">
                    {{ $prod->display_name ?? $prod->name }} - {{ number_format($prod->price ?? 0, 2) }}
                </option>
            @endforeach
        @endif
    </select>

    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .step-indicator {
            position: relative;
            padding: 10px 0;
        }
        .step-indicator.completed .step-circle {
            background: #28a745;
            color: white;
        }
        .step-indicator.active .step-circle {
            background: #ff9900;
            color: white;
            box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.3);
        }
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 5px;
            background: #e0e0e0;
            font-weight: bold;
        }
        .proof-of-payment-box {
            height: auto !important;
            min-height: 0 !important;
            max-height: max-content !important;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        let soItemRowCounter = {{ isset($soItems) ? $soItems->count() + 10 : 100 }};

        $(document).ready(function() {
            if ($('#allBooksTable').length) {
                $('#allBooksTable').DataTable({
                    pageLength: 5,
                    lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                    order: [[0, 'asc']],
                    responsive: true
                });
            }

            recalcAllEditTotals();

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('edit') === '1' || urlParams.get('edit') === 'true') {
                toggleEditMode(true);
            }
        });

        function toggleEditMode(isEdit) {
            const viewDiv = document.getElementById('freightViewMode');
            const editDiv = document.getElementById('freightEditMode');
            const btnToggle = document.getElementById('btnToggleEdit');
            const btnCancel = document.getElementById('btnCancelEditHeader');

            if (isEdit) {
                if (viewDiv) viewDiv.classList.add('d-none');
                if (editDiv) editDiv.classList.remove('d-none');
                if (btnToggle) btnToggle.classList.add('d-none');
                if (btnCancel) btnCancel.classList.remove('d-none');
                recalcAllEditTotals();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                if (viewDiv) viewDiv.classList.remove('d-none');
                if (editDiv) editDiv.classList.add('d-none');
                if (btnToggle) btnToggle.classList.remove('d-none');
                if (btnCancel) btnCancel.classList.add('d-none');
            }
        }

        function addCargoRow() {
            const tbody = document.getElementById('cargoItemsTableBody');
            if (!tbody) return;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="number" name="cargo_qty[]" class="form-control form-control-sm" min="1" value="1" required></td>
                <td><input type="text" name="cargo_package_type[]" class="form-control form-control-sm" placeholder="e.g. Box, Crate" value="Box"></td>
                <td><input type="text" name="cargo_dimensions[]" class="form-control form-control-sm" placeholder="e.g. 30x20x15 cm" value=""></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="removeCargoRow(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        }

        function removeCargoRow(btn) {
            const row = btn.closest('tr');
            if (row) {
                const tbody = row.parentElement;
                if (tbody.querySelectorAll('tr').length > 1) {
                    row.remove();
                } else {
                    row.querySelectorAll('input').forEach(input => input.value = '');
                }
            }
        }

        function calculateFreightTotal() {
            const estInput = document.getElementById('edit_estimated_freight');
            const hndInput = document.getElementById('edit_handling_fee');
            const totInput = document.getElementById('edit_total_amount');
            const optionSelect = document.getElementById('edit_freight_option');
            const currencySelect = document.getElementById('edit_currency');
            if (!estInput || !hndInput || !totInput) return;

            const est = parseFloat(estInput.value) || 0;
            const hnd = parseFloat(hndInput.value) || 0;
            const opt = optionSelect ? optionSelect.value : '';
            const curr = currencySelect ? currencySelect.value : 'PHP';

            let serviceFee = 0;
            if (opt === 'freight_collect') {
                serviceFee = curr === 'USD' ? (50.0 / 56.0) : 50.0;
            }

            totInput.value = (est + hnd + serviceFee).toFixed(2);
        }

        function addSOItemOnEdit() {
            const tbody = document.getElementById('editSoItemsTableBody');
            if (!tbody) return;

            const placeholder = document.getElementById('noSoItemsPlaceholder');
            if (placeholder) {
                placeholder.remove();
            }

            const productSource = document.getElementById('editProductSource');
            const productOptions = productSource ? productSource.innerHTML : '<option value="">-- No Products Available --</option>';

            soItemRowCounter++;
            const idx = soItemRowCounter;

            const tr = document.createElement('tr');
            tr.className = 'so-item-row';
            tr.setAttribute('data-index', idx);
            tr.innerHTML = `
                <td>
                    <select name="so_items[${idx}][product_id]" class="form-select form-select-sm edit-item-prod" onchange="onProductSelectChanged(this)" required>
                        ${productOptions}
                    </select>
                </td>
                <td>
                    <input type="number" min="1" name="so_items[${idx}][quantity]" class="form-control form-control-sm edit-item-qty" value="1" oninput="recalcEditRow(this)">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="so_items[${idx}][price]" class="form-control form-control-sm edit-item-price" value="0.00" oninput="recalcEditRow(this)">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="so_items[${idx}][discount_value]" class="form-control form-control-sm edit-item-discount" value="0" oninput="recalcEditRow(this)">
                </td>
                <td>
                    <select name="so_items[${idx}][discount_type]" class="form-select form-select-sm edit-item-discount-type" onchange="recalcEditRow(this)">
                        <option value="percentage">%</option>
                        <option value="amount">Amount</option>
                    </select>
                </td>
                <td class="text-end fw-bold edit-item-subtotal">
                    0.00
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="removeSOItemOnEdit(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
            recalcAllEditTotals();
        }

        function onProductSelectChanged(selectElem) {
            const selectedOpt = selectElem.options[selectElem.selectedIndex];
            if (!selectedOpt) return;

            const price = selectedOpt.getAttribute('data-price') || 0;
            const row = selectElem.closest('tr');
            if (row) {
                const priceInput = row.querySelector('.edit-item-price');
                if (priceInput) {
                    priceInput.value = parseFloat(price).toFixed(2);
                }
                recalcEditRow(selectElem);
            }
        }

        function recalcEditRow(elementInRow) {
            const row = elementInRow.closest('tr');
            if (!row) return;

            const qtyInput = row.querySelector('.edit-item-qty');
            const priceInput = row.querySelector('.edit-item-price');
            const discValInput = row.querySelector('.edit-item-discount');
            const discTypeSelect = row.querySelector('.edit-item-discount-type');
            const subtotalCell = row.querySelector('.edit-item-subtotal');

            const qty = parseFloat(qtyInput?.value) || 0;
            const price = parseFloat(priceInput?.value) || 0;
            const discVal = parseFloat(discValInput?.value) || 0;
            const discType = discTypeSelect ? discTypeSelect.value : 'percentage';

            const gross = qty * price;
            let discAmount = 0;
            if (discType === 'percentage') {
                discAmount = gross * (discVal / 100);
            } else {
                discAmount = discVal;
            }

            const subtotal = Math.max(0, gross - discAmount);
            if (subtotalCell) {
                subtotalCell.textContent = subtotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            recalcAllEditTotals();
        }

        function removeSOItemOnEdit(btn) {
            const row = btn.closest('tr');
            if (!row) return;

            const tbody = row.parentElement;
            row.remove();

            if (tbody && tbody.querySelectorAll('tr.so-item-row').length === 0) {
                tbody.innerHTML = `
                    <tr id="noSoItemsPlaceholder">
                        <td colspan="7" class="text-center text-muted py-3">
                            No items added yet. Click <strong><i class="bi bi-plus-circle text-success"></i> Add Item</strong> to add products.
                        </td>
                    </tr>
                `;
            }

            recalcAllEditTotals();
        }

        function recalcAllEditTotals() {
            const subtotalCells = document.querySelectorAll('#editSoItemsTableBody .edit-item-subtotal');
            let sum = 0;
            subtotalCells.forEach(cell => {
                const val = parseFloat(cell.textContent.replace(/,/g, '')) || 0;
                sum += val;
            });

            const totalDisplay = document.getElementById('editSoTotalDisplay');
            if (totalDisplay) {
                totalDisplay.textContent = sum.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }
    </script>
    @endpush
</x-app-layout>
