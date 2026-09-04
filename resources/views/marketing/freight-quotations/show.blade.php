<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card" style="border-radius: 8px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);">
                    <!-- Header -->
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-file-earmark me-2"></i>Freight Quotation: {{ $quotation->quote_number }}</h5>
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

                    <div class="card-body">
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

                        <!-- Action Buttons -->
                        @php
                            $isFordSource = !empty($isFord) || $quotation->source === 'ford' || (isset($quotation->salesOrder) && ($quotation->salesOrder->type === 'foreign' || str_contains($quotation->salesOrder->so_number ?? '', 'FORD')));
                            $backRoute = $isFordSource ? route($indexRoute ?? 'production.ford.freight-quotation.index') : route('marketing.freight-quotations.list');
                        @endphp
                        <div class="d-flex gap-2 justify-content-between flex-wrap" style="padding-top: 1.5rem;">
                            <a href="{{ $backRoute }}" class="btn btn-light border">
                                <i class="bi bi-arrow-left me-1"></i>Back
                            </a>

                            <div>
                                @if(in_array($quotation->workflow_status, ['approved', 'linked_to_so']))
                                    @if(!$quotation->sales_order_id)
                                        <form method="POST" action="{{ route('marketing.freight-quotations.create-so-directly', $quotation->id) }}" style="display: inline;">
                                            @csrf
                                            @if($isFordSource)
                                                <input type="hidden" name="source" value="ford">
                                            @endif
                                            <button type="submit" class="btn btn-success btn-lg">
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
                                                <button type="submit" class="btn btn-success btn-lg">
                                                    <i class="bi bi-arrow-right-circle me-1"></i>Proceed Sales Order (SO #{{ $quotation->salesOrder->so_number }})
                                                </button>
                                            </form>
                                        @else
                                            @php
                                                $targetSoRoute = $isFordSource
                                                    ? (Route::has('production.ford.sales-order.review') && $quotation->sales_order_id ? route('production.ford.sales-order.review', $quotation->sales_order_id) : (Route::has('production.ford.sales-order') ? route('production.ford.sales-order') : route('marketing.sales-orders.detail', $quotation->sales_order_id)))
                                                    : route('marketing.sales-orders.detail', $quotation->sales_order_id);
                                            @endphp
                                            <a href="{{ $targetSoRoute }}" class="btn btn-info btn-lg">
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
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            if ($('#allBooksTable').length) {
                $('#allBooksTable').DataTable({
                    pageLength: 5,
                    lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                    order: [[0, 'asc']],
                    responsive: true
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
