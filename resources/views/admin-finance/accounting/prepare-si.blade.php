<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @php
        $activeInvoice = null;
        if (in_array($order->type, ['area_consignment', 'area_sales_consignment'])) {
            $activeInvoice = \App\Models\SalesInvoice::where('so_id', $order->id)->where('status', '!=', 'cancelled')->latest()->first();
        }

        // If activeInvoice has no items, fall back to SO items
        if ($activeInvoice && $activeInvoice->items->count() > 0) {
            $itemsToRender = $activeInvoice->items->filter(fn($i) => (float)($i->quantity ?? 0) > 0);
            $totalSalesAmount = (float) $activeInvoice->total_amount;
        } else {
            $itemsToRender = $order->items ? $order->items->filter(fn($i) => (float)($i->quantity ?? 0) > 0) : collect();
            $totalSalesAmount = (float) $order->total_amount;
            $activeInvoice = null; // reset so item fields resolve from SO items
        }
    @endphp
    @push('styles')
    <style>
        .invoice-form {
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
            width: 60px; height: 60px;
            background: #ff0000; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 2rem; font-weight: bold;
        }
        .form-header .company-name {
            font-size: 1.25rem; font-weight: 700; color: #333;
            text-transform: uppercase;
        }
        .form-header .document-title {
            text-align: center; font-size: 1.75rem; font-weight: 700;
            color: #333; margin-top: 1rem;
        }
        .invoice-number {
            text-align: center; font-size: 1.25rem; font-weight: 700;
            color: #ff0000; margin-top: 0.5rem;
        }
        .customer-section {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 2rem; margin-bottom: 1.5rem;
        }
        .customer-details, .transaction-details {
            background: #f8f9fa; padding: 1rem; border-radius: 6px;
        }
        .invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        .invoice-table thead { background: #ff0000; color: #fff; }
        .invoice-table th, .invoice-table td { padding: 0.75rem; border: 1px solid #ddd; }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <form action="{{ route('admin-finance.accounting.sales-invoice.store', $order->id) }}" method="POST">
                @csrf
                <div class="card invoice-form">
                    <div class="form-header">
                        <div class="company-info">
                            <div class="company-logo">C</div>
                            <div class="company-details">
                                <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                                <div class="company-address">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                                <div class="company-contact">Tel. No.: 921-3984</div>
                            </div>
                        </div>
                        <div class="document-title">PREPARE SALES INVOICE</div>
                        <div class="invoice-number">SO Ref: #{{ $order->so_number }}</div>
                    </div>

                    <div class="customer-section">
                        <div class="customer-details">
                            <h5 class="fw-bold mb-3">Customer Information</h5>
                            <div class="mb-2">
                                <label class="form-label fw-bold">Company:</label>
                                <input type="text" class="form-control" value="{{ $order->customer->customer_name ?? 'N/A' }}" readonly>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold">Customer Name:</label>
                                <input type="text" class="form-control" value="{{ $order->customer_representative ?: ($order->customer->customer_name ?? 'N/A') }}" readonly>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold">Contact:</label>
                                <input type="text" class="form-control" value="{{ $order->customer_contact ?: ($order->customer?->mobile ?: ($order->customer?->main_phone ?: 'N/A')) }}" readonly>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold">Address:</label>
                                <textarea class="form-control" rows="2" readonly>{{ $order->billing_address ?? ($order->customer->address ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="transaction-details">
                            <h5 class="fw-bold mb-3">Transaction Details</h5>
                            <div class="mb-2">
                                <label class="form-label fw-bold text-danger"><i class="las la-file-invoice me-1"></i> SI Number:</label>
                                <input type="text" class="form-control fw-bold" name="si_number" value="{{ $order->si_number ?: ($activeInvoice->si_number ?? \App\Http\Controllers\POSController::getNextSiNumber($order->type === 'ecom_direct' ? 'mibf' : ($order->type === 'calculator_pos' ? 'pos' : 'sales_invoice'))) }}" placeholder="e.g. 00123" style="border: 2px solid #dc3545; background-color: #fff8f8; color: #dc3545; font-size: 1rem;">
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold">Date:</label>
                                <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold"><i class="las la-wallet me-1 text-primary"></i> Payment Method:</label>
                                <select name="payment_method" class="form-select form-control" required style="border: 2px solid #0d6efd; background-color: #f0f7ff; font-weight: 600;">
                                    <option value="cash" {{ strtolower($order->payment_method ?? '') === 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="gcash" {{ strtolower($order->payment_method ?? '') === 'gcash' ? 'selected' : '' }}>GCash (E-Wallet)</option>
                                    <option value="maya" {{ strtolower($order->payment_method ?? '') === 'maya' ? 'selected' : '' }}>Maya (E-Wallet)</option>
                                    <option value="bank_transfer" {{ strtolower($order->payment_method ?? '') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="check" {{ strtolower($order->payment_method ?? '') === 'check' ? 'selected' : '' }}>Check</option>
                                    <option value="card" {{ strtolower($order->payment_method ?? '') === 'card' ? 'selected' : '' }}>Credit / Debit Card</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold">Terms:</label>
                                <input type="text" class="form-control" value="{{ $order->terms }}" readonly>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold">Remarks:</label>
                                <textarea class="form-control" rows="2" readonly style="background:#f8f9fa; font-weight:600;">{{ $order->remarks ?: '—' }}</textarea>
                            </div>
                            @php
                                $isComp = $order->type === 'complimentary';
                                $paidAmt = (float) $order->total_paid_amount;
                                $remBal = $isComp ? 0 : (float) $order->remaining_balance;
                                $pmStatus = $order->computed_payment_status;
                                $pmBadgeColor = $pmStatus === 'paid' ? 'success' : ($pmStatus === 'partially_paid' ? 'warning' : 'danger');
                                $pmLabel = $pmStatus === 'partially_paid' ? 'PARTIALLY PAID' : strtoupper($pmStatus);
                            @endphp
                            <div class="mb-2 p-2 rounded bg-white border">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold">Payment Status:</span>
                                    @if($isComp)
                                        <span class="badge fs-12 px-2 py-1" style="background-color: #6f42c1; color: #fff;">Complimentary / Donation</span>
                                    @else
                                        <span class="badge bg-{{ $pmBadgeColor }} fs-12 px-2 py-1">{{ $pmLabel }}</span>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-muted">Already Paid:</span>
                                    <span class="fw-bold text-success">{{ $isComp ? 'N/A' : '₱' . number_format($paidAmt, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted">Remaining Balance:</span>
                                    <span class="fw-bold {{ $isComp ? 'text-success' : 'text-danger' }}">{{ $isComp ? '₱0.00 (No Charge)' : '₱' . number_format($remBal, 2) }}</span>
                                </div>
                                @if(!$isComp && $remBal > 0 && $order->customer_id)
                                    <button type="button" class="btn btn-sm btn-success w-100 open-pay-modal-btn shadow-sm" data-so-id="{{ $order->id }}" data-customer-id="{{ $order->customer_id }}" data-so-number="{{ $order->so_number }}" data-total="{{ $order->total_amount }}" data-paid="{{ $paidAmt }}" data-remaining="{{ $remBal }}" data-terms="{{ $order->terms ?? 'COD' }}" data-due-date="{{ $order->due_date ? $order->due_date->format('M d, Y') : 'N/A' }}">
                                        <i class="las la-coins me-1"></i> Record Payment / Installment
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Attachments Display -->
                    <div class="card mb-4 border-light bg-light">
                        <div class="card-body">
                            <h5 class="fw-bold text-dark mb-3"><i class="las la-paperclip me-1"></i> Attachments</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted">Pick List:</label>
                                    <div>
                                        @if($order->pick_list_attachment)
                                            <a href="{{ asset('storage/' . $order->pick_list_attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="las la-file-alt me-1"></i> View Pick List
                                            </a>
                                        @else
                                            <span class="text-muted"><i class="las la-info-circle me-1"></i> No Pick List attached</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted">Proof of Payment:</label>
                                    <div>
                                        @if(in_array($order->type, ['charge', 'area_consignment', 'area_sales_consignment', 'direct_consignment', 'complimentary']))
                                            <span class="badge p-2 bg-info text-white"><i class="las la-info-circle me-1"></i> Not Required ({{ ucfirst(str_replace('_', ' ', $order->type)) }} Transaction)</span>
                                            @if($order->proof_of_payment)
                                                <a href="{{ asset('storage/' . $order->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-success fw-bold ms-1">
                                                    <i class="las la-receipt me-1"></i> View POP
                                                </a>
                                            @else
                                                <div class="text-muted small mt-1"><i class="las la-info-circle me-1"></i> You may optionally attach a Proof of Payment via the SO Review page.</div>
                                            @endif
                                        @elseif($order->proof_of_payment)
                                            <a href="{{ asset('storage/' . $order->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="las la-receipt me-1"></i> View Proof of Payment
                                            </a>
                                        @else
                                            <span class="text-muted"><i class="las la-info-circle me-1"></i> No Proof of Payment attached</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="invoice-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">QTY</th>
                                <th>DESCRIPTION</th>
                                <th style="width: 120px;">ISBN</th>
                                <th style="width: 120px;">AREA</th>
                                <th style="width: 150px;">UNIT PRICE</th>
                                <th style="width: 150px;">AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($itemsToRender as $item)
                            <tr>
                                <td class="text-center">
                                    {{ $item->quantity }} 
                                    {{ $activeInvoice ? ($item->book?->unit ?? 'pcs') : ($item->product?->unit ?? $item->book?->unit ?? 'pcs') }}
                                </td>
                                <td>
                                    <div class="fw-bold d-flex align-items-center gap-1 flex-wrap">
                                        <span>{{ $item->item_name ?? ($activeInvoice ? ($item->book?->name ?? 'Unknown Product') : ($item->product?->name ?? ($item->book?->name ?? ($item->bundle?->name ?? 'Unknown Product')))) }}</span>
                                        @if($item->bundle_id || $item->bundle)
                                            <span class="badge bg-purple text-white ms-1" style="background-color: #6f42c1; font-size: 10px; padding: 3px 6px;">Bundle</span>
                                        @elseif($item->book_index_id || $item->bookIndex)
                                            <span class="badge bg-info text-white ms-1" style="font-size: 10px; padding: 3px 6px;">Index</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    {{ $activeInvoice ? ($item->book?->sku ?? '-') : ($item->isbn ?? '-') }}
                                </td>
                                <td>
                                    {{ $activeInvoice ? ($item->book?->shelf_number ?? '-') : ($item->area ?? '-') }}
                                </td>
                                <td class="text-end">₱{{ number_format($activeInvoice ? $item->unit_price : $item->price, 2) }}</td>
                                <td class="text-end">₱{{ number_format($activeInvoice ? $item->amount : $item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            @php
                                $itemsSubtotal = $itemsToRender->sum(function($item) {
                                    return $item->amount ?? ($item->subtotal !== null ? $item->subtotal : ($item->quantity * $item->price));
                                });
                                $discountAmount = $order->discount_amount ?? 0;
                                $discountPercentage = $order->discount_percentage ?? 0;
                                $freightCharges = $order->freight_charges ?? 0;
                                $serviceFee = $order->freight_option === 'freight_collect' ? 50 : 0;
                            @endphp
                            <tr>
                                <td colspan="5" class="text-end text-uppercase"><strong>Items Subtotal:</strong></td>
                                <td class="text-end fw-bold">₱{{ number_format($itemsSubtotal, 2) }}</td>
                            </tr>
                            @if($discountAmount > 0)
                            <tr>
                                <td colspan="5" class="text-end text-uppercase">
                                    <strong>
                                        Discount
                                        @if($discountPercentage > 0)
                                            ({{ (float)$discountPercentage }}%)
                                        @endif:
                                    </strong>
                                </td>
                                <td class="text-end fw-bold text-danger">- ₱{{ number_format($discountAmount, 2) }}</td>
                            </tr>
                            @endif
                            @if($freightCharges > 0)
                            <tr>
                                <td colspan="5" class="text-end text-uppercase"><strong>Freight Charges:</strong></td>
                                <td class="text-end fw-bold">₱{{ number_format($freightCharges, 2) }}</td>
                            </tr>
                            @endif
                            @if($serviceFee > 0)
                            <tr>
                                <td colspan="5" class="text-end text-uppercase"><strong>Service Fee:</strong></td>
                                <td class="text-end fw-bold">₱{{ number_format($serviceFee, 2) }}</td>
                            </tr>
                            @endif
                            <tr style="background: #f8f9fa;">
                                <th colspan="5" class="text-end text-uppercase"><strong>Grand Total:</strong></th>
                                <th class="text-end fw-bold fs-5 text-primary">₱{{ number_format($totalSalesAmount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="las la-info-circle me-2"></i>
                                By submitting, you are marking this Sales Invoice as <strong>Prepared by {{ auth()->user()->name }}</strong>.
                            </div>
                        </div>
                    </div>

                    <div class="form-actions d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-light" onclick="window.history.back()">Cancel</button>
                        
                        {{-- Direct Invoice Ecom: Show "Linked to Picklist" button --}}
                        @if($order->type === 'ecom_direct')
                            <a href="{{ route('production.logistic.pick-list-management', ['so_id' => $order->id]) }}" class="btn btn-info">
                                <i class="las la-link me-1"></i>Linked to Picklist
                            </a>
                        @endif
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="las la-save me-2"></i>Finalize & Submit for Manager Approval
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Record Payment Modal -->
    <div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white"><i class="las la-money-bill-wave me-2"></i>Payment History & Record Installment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="recordPaymentForm">
                    <div class="modal-body">
                        <input type="hidden" id="paySoId">
                        <input type="hidden" id="payCustomerId">
                        
                        <div class="alert alert-light border mb-3">
                            <div class="row g-2 text-center text-md-start">
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Transaction #:</span>
                                    <strong id="paySoNumber" class="text-dark">SO-0000</strong>
                                </div>
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Terms:</span>
                                    <span id="payTerms" class="badge bg-info text-white fw-semibold">COD</span>
                                </div>
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Due Date:</span>
                                    <strong id="payDueDate" class="text-dark">N/A</strong>
                                </div>
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Grand Total:</span>
                                    <strong id="payTotalAmount" class="text-dark">₱0.00</strong>
                                </div>
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Already Paid:</span>
                                    <span id="payAlreadyPaid" class="text-success fw-bold">₱0.00</span>
                                </div>
                                <div class="col-6 col-md-2">
                                    <span class="text-muted small d-block">Remaining:</span>
                                    <strong id="payRemainingBalance" class="text-danger fs-16">₱0.00</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Payment History Breakdown Table -->
                        <div class="card mb-3 border">
                            <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                                <span class="fw-bold small text-dark"><i class="las la-history me-1 text-primary"></i> Previous Installments Log</span>
                                <span class="badge bg-secondary" id="payHistoryBadge">0 payments</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 180px; overflow-y: auto;">
                                    <table class="table table-sm table-striped table-bordered mb-0 align-middle" style="font-size: 11px;">
                                        <thead class="bg-light sticky-top">
                                            <tr>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Method</th>
                                                <th>Ref # / Check #</th>
                                                <th>Notes</th>
                                                <th>Proof</th>
                                                <th>Recorded By</th>
                                            </tr>
                                        </thead>
                                        <tbody id="payHistoryTableBody">
                                            <tr><td colspan="7" class="text-center py-2 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Loading payment history...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- New Installment Entry Form -->
                        <div id="newPaymentFormFields">
                            <h6 class="fw-bold text-dark border-bottom pb-1 mb-3"><i class="las la-plus-circle me-1 text-success"></i> Add New Installment Payment</h6>

                            <div class="row g-2">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Payment Amount (₱) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" step="0.01" min="0.01" id="payAmountInput" class="form-control fw-bold fs-15 text-primary" required placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Payment Method <span class="text-danger">*</span></label>
                                    <select id="payMethodSelect" class="form-select form-select-sm" required>
                                        <option value="cash">Cash</option>
                                        <option value="gcash">GCash</option>
                                        <option value="maya">Maya</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="check">Check</option>
                                        <option value="card">Credit / Debit Card</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Reference / Check # <span class="text-muted fw-normal">(Optional)</span></label>
                                    <input type="text" id="payRefInput" class="form-control form-control-sm" placeholder="e.g. Ref #123456 or Check #">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Notes / Remarks <span class="text-muted fw-normal">(Optional)</span></label>
                                    <input type="text" id="payNotesInput" class="form-control form-control-sm" placeholder="e.g. 1st installment payment">
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label class="form-label fw-bold small text-dark">Proof of Payment <span class="text-muted fw-normal">(Optional - Image/PDF)</span></label>
                                    <input type="file" id="payProofInput" class="form-control form-control-sm" accept="image/*,.pdf">
                                </div>
                            </div>
                        </div>

                        <div id="fullyPaidNotice" class="alert alert-success d-none text-center py-2 mb-0">
                            <i class="las la-check-circle me-1 fs-16"></i> This order is fully paid. No further payments required.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" id="submitPaymentBtn">
                            <i class="las la-check-circle me-1"></i> Submit Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        async function fetchPaymentHistory(customerId, soId) {
            const tableBody = document.getElementById('payHistoryTableBody');
            const badge = document.getElementById('payHistoryBadge');

            if (!tableBody) return;

            tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-2 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Loading history...</td></tr>';
            if (badge) badge.textContent = 'Loading...';

            try {
                const response = await fetch(`/marketing/customers/${customerId}/transactions/${soId}/payments`);
                const data = await response.json();

                if (!data.payments || data.payments.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-2 text-muted">No previous installments recorded.</td></tr>';
                    if (badge) badge.textContent = '0 payments';
                } else {
                    if (badge) badge.textContent = data.payments.length + ' payment(s)';
                    let rows = '';
                    data.payments.forEach(p => {
                        const proofTag = p.has_proof ? `<a href="${p.proof_url}" target="_blank" class="badge badge-xs bg-light text-primary border"><i class="las la-paperclip me-1"></i>View Proof</a>` : '<span class="text-muted small">None</span>';
                        rows += `<tr>
                            <td class="fw-bold">${p.date}</td>
                            <td class="text-success fw-bold">₱${p.amount.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                            <td><span class="badge bg-light text-dark border">${p.method}</span></td>
                            <td>${p.reference_number}</td>
                            <td>${p.notes}</td>
                            <td>${proofTag}</td>
                            <td><small class="text-muted">${p.recorded_by}</small></td>
                        </tr>`;
                    });
                    tableBody.innerHTML = rows;
                }
            } catch (error) {
                console.error('Error loading payment history:', error);
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-2 text-danger">Failed to load payment history.</td></tr>';
                if (badge) badge.textContent = 'Error';
            }
        }

        document.body.addEventListener('click', function(e) {
            const payBtn = e.target.closest('.open-pay-modal-btn');
            if (payBtn) {
                const soId = payBtn.dataset.soId;
                const customerId = payBtn.dataset.customerId;
                const soNumber = payBtn.dataset.soNumber;
                const totalAmount = parseFloat(payBtn.dataset.total) || 0;
                const paidAmount = parseFloat(payBtn.dataset.paid) || 0;
                const remainingBalance = parseFloat(payBtn.dataset.remaining) || 0;

                const terms = payBtn.dataset.terms || 'COD';
                const dueDate = payBtn.dataset.dueDate || 'N/A';

                document.getElementById('paySoId').value = soId;
                document.getElementById('payCustomerId').value = customerId;
                document.getElementById('paySoNumber').textContent = soNumber;
                document.getElementById('payTerms').textContent = terms;
                document.getElementById('payDueDate').textContent = dueDate;
                document.getElementById('payTotalAmount').textContent = '₱' + totalAmount.toLocaleString(undefined, {minimumFractionDigits: 2});
                document.getElementById('payAlreadyPaid').textContent = '₱' + paidAmount.toLocaleString(undefined, {minimumFractionDigits: 2});
                document.getElementById('payRemainingBalance').textContent = '₱' + remainingBalance.toLocaleString(undefined, {minimumFractionDigits: 2});
                
                const formFields = document.getElementById('newPaymentFormFields');
                const submitBtn = document.getElementById('submitPaymentBtn');
                const notice = document.getElementById('fullyPaidNotice');

                if (remainingBalance <= 0) {
                    if (formFields) formFields.classList.add('d-none');
                    if (submitBtn) submitBtn.classList.add('d-none');
                    if (notice) notice.classList.remove('d-none');
                } else {
                    if (formFields) formFields.classList.remove('d-none');
                    if (submitBtn) submitBtn.classList.remove('d-none');
                    if (notice) notice.classList.add('d-none');

                    const payAmountInput = document.getElementById('payAmountInput');
                    payAmountInput.value = remainingBalance.toFixed(2);
                    payAmountInput.max = remainingBalance;
                    document.getElementById('payRefInput').value = '';
                    document.getElementById('payNotesInput').value = '';
                    const proofInput = document.getElementById('payProofInput');
                    if (proofInput) proofInput.value = '';
                }

                // Fetch payment history breakdown
                fetchPaymentHistory(customerId, soId);

                const payModalElement = document.getElementById('recordPaymentModal');
                const payModal = bootstrap.Modal.getInstance(payModalElement) || new bootstrap.Modal(payModalElement);
                payModal.show();
            }
        });

        document.getElementById('recordPaymentForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const soId = document.getElementById('paySoId').value;
            const customerId = document.getElementById('payCustomerId').value;
            const amount = parseFloat(document.getElementById('payAmountInput').value);
            const paymentMethod = document.getElementById('payMethodSelect').value;
            const referenceNumber = document.getElementById('payRefInput').value;
            const notes = document.getElementById('payNotesInput').value;
            const proofInput = document.getElementById('payProofInput');

            if (!soId || !customerId) return;

            const submitBtn = document.getElementById('submitPaymentBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...';

            const formData = new FormData();
            formData.append('amount', amount);
            formData.append('payment_method', paymentMethod);
            if (referenceNumber) formData.append('reference_number', referenceNumber);
            if (notes) formData.append('notes', notes);
            if (proofInput && proofInput.files[0]) {
                formData.append('proof_of_payment', proofInput.files[0]);
            }

            try {
                const response = await fetch(`/marketing/customers/${customerId}/transactions/${soId}/pay`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    alert('Payment recorded successfully!');
                    window.location.reload();
                } else {
                    alert(data.message || 'Error recording payment.');
                }
            } catch (error) {
                console.error('Error submitting payment:', error);
                alert('An error occurred while submitting payment.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="las la-check-circle me-1"></i> Submit Payment';
            }
        });
    });
    </script>
    @endpush
</x-app-layout>
