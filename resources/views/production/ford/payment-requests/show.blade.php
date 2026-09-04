<x-app-layout :title="$title" :sidebar="$sidebar">
    <div class="container-fluid">
        <!-- Action Cards (For Approvers) -->
        @if($canApprove)
            <div class="card mb-4 border-left-success" style="border-left: 5px solid #28a745;">
                <div class="card-header">
                    <h4 class="card-title text-success"><i class="las la-shield-alt me-2"></i> Approval Action Required</h4>
                </div>
                <div class="card-body">
                    <p class="mb-3">This payment request is currently <strong>{{ str_replace('_', ' ', $paymentRequest->status) }}</strong> and awaits your review.</p>
                    
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @if($paymentRequest->status === 'pending_director_approval')
                            <form action="{{ route('payment-requests.approve', $paymentRequest->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="approval_type" value="director">
                                <button type="submit" class="btn btn-success"><i class="las la-check-circle"></i> Approve as Director</button>
                            </form>
                        @endif

                        @if($paymentRequest->status === 'pending_admin_finance_approval')
                            @if($canApproveAsAdmin && $canApproveAsFinance)
                                <form action="{{ route('payment-requests.approve', $paymentRequest->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="approval_type" value="both">
                                    <button type="submit" class="btn btn-success"><i class="las la-check-circle"></i> Approve Request</button>
                                </form>
                            @else
                                @if($canApproveAsAdmin)
                                    <form action="{{ route('payment-requests.approve', $paymentRequest->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="approval_type" value="admin">
                                        <button type="submit" class="btn btn-success"><i class="las la-check-circle"></i> Approve as Admin Manager</button>
                                    </form>
                                @endif

                                @if($canApproveAsFinance)
                                    <form action="{{ route('payment-requests.approve', $paymentRequest->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="approval_type" value="finance">
                                        <button type="submit" class="btn btn-success"><i class="las la-check-circle"></i> Approve as Finance Manager</button>
                                    </form>
                                @endif
                            @endif
                        @endif

                        <form action="{{ route('payment-requests.reject', $paymentRequest->id) }}" method="POST" class="d-inline" id="rejectPaymentRequestForm">
                            @csrf
                            <button type="button" class="btn btn-danger" onclick="handlePaymentRequestReject(this.form)">
                                <i class="las la-times-circle"></i> Reject Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Rejection Warning Banner -->
        @if($paymentRequest->status === 'rejected')
            <div class="card mb-4 border-left-danger bg-light" style="border-left: 5px solid #dc3545;">
                <div class="card-body">
                    <h5 class="text-danger fw-bold"><i class="las la-exclamation-triangle"></i> Rejected</h5>
                    <p class="mb-1">This request was rejected by <strong>{{ $paymentRequest->rejector->name ?? 'User' }}</strong> on {{ $paymentRequest->rejected_at->format('M. d, Y h:i A') }}</p>
                    <p class="mb-0 text-muted"><strong>Reason:</strong> "{{ $paymentRequest->rejection_reason }}"</p>
                </div>
            </div>
        @endif

        <!-- Payment Scheduled Banner -->
        @if($paymentRequest->status === 'scheduled' || $paymentRequest->status === 'paid')
            <div class="card mb-4 border-left-info" style="border-left: 5px solid #17a2b8; background-color: #f0fcfe;">
                <div class="card-body">
                    <h5 class="text-info fw-bold"><i class="las la-calendar-check"></i> Payment Information</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Status:</strong> 
                            <span class="badge bg-{{ $paymentRequest->status === 'paid' ? 'success' : 'primary' }}">
                                {{ strtoupper($paymentRequest->status) }}
                            </span>
                        </div>
                        <div class="col-md-3"><strong>Scheduled Date:</strong> {{ $paymentRequest->scheduled_payment_date ? $paymentRequest->scheduled_payment_date->format('M. d, Y') : 'N/A' }}</div>
                        <div class="col-md-3"><strong>Payment Method:</strong> {{ $paymentRequest->payment_method ?? 'N/A' }}</div>
                        <div class="col-md-3"><strong>Reference #:</strong> {{ $paymentRequest->payment_reference ?? 'N/A' }}</div>
                    </div>
                    @if($paymentRequest->remarks)
                        <div class="mt-2"><strong>Accounting Notes:</strong> "{{ $paymentRequest->remarks }}"</div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Main Document Details -->
        <div class="row">
            <div class="col-xl-9 col-lg-10 mx-auto">
                <div class="card shadow">
                    <div class="card-body p-5">
                        
                        <!-- Header Actions -->
                        <div class="d-flex justify-content-between align-items-center mb-4 report-actions">
                            <a href="javascript:history.back()" class="btn btn-secondary btn-sm"><i class="las la-arrow-left"></i> Back</a>
                            <div class="d-flex gap-2">
                                @if($paymentRequest->attachment_path)
                                    <a href="{{ asset('storage/' . $paymentRequest->attachment_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="las la-paperclip"></i> View Attachment
                                    </a>
                                @endif
                                <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="las la-print"></i> Print Letter</button>
                            </div>
                        </div>

                        <!-- Document Body -->
                        <div class="generated-letter" style="border: 1px solid #ddd; padding: 40px; min-height: 600px; font-family: 'Times New Roman', serif; color: #000; background: #fff;">
                            <!-- Claretian Header -->
                            <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-dark">
                                <div class="company-logo-circle" style="width: 50px; height: 50px; background: #ff0000; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.5rem; font-weight: bold;">C</div>
                                <div>
                                    <h4 class="font-weight-bold mb-0" style="font-family: inherit; font-size: 1.15rem; color: #000;">CLARETIAN COMMUNICATIONS FOUNDATION INC.</h4>
                                    <div style="font-size: 0.8rem; color: #333;">8 Mayumi St., UP Village, Diliman, Quezon City | Tel. No.: 921-3984</div>
                                </div>
                            </div>

                            <div class="text-center mb-4">
                                <h3 class="font-weight-bold" style="font-family: inherit; color: #000; font-size: 1.5rem;">PAYMENT REQUEST</h3>
                            </div>
                            
                            <!-- Header Info -->
                            <table class="w-100 mb-4" style="color: #000; font-size: 0.95rem; line-height: 1.6;">
                                <tr>
                                    <td style="width: 50%;"><strong>Date:</strong> <span class="blank-field-val">{{ $paymentRequest->date ? $paymentRequest->date->format('F d, Y') : '' }}</span></td>
                                    <td style="width: 50%; text-align: right;"><strong>Due Date:</strong> <span class="blank-field-val">{{ $paymentRequest->due_date ? $paymentRequest->due_date->format('F d, Y') : '____________' }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Payment to:</strong> <span class="blank-field-val">{{ $paymentRequest->payment_to }}</span></td>
                                    <td style="text-align: right;"><strong>PO#:</strong> <span class="blank-field-val">{{ $paymentRequest->po_number ?? '____________' }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Payment for:</strong> <span class="blank-field-val">{{ $paymentRequest->payment_for ?? '____________' }}</span></td>
                                    <td style="text-align: right;"><strong>Item Receipt#:</strong> <span class="blank-field-val">{{ $paymentRequest->item_receipt ?? '____________' }}</span></td>
                                </tr>
                            </table>

                            <!-- Particulars Table -->
                            <table class="payment-table w-100 mb-4" style="border-collapse: collapse; border: 1px solid #000; color: #000;">
                                <thead>
                                    <tr style="background-color: #f0f0f0;">
                                        <th style="border: 1px solid #000; padding: 8px; font-weight: bold; width: 120px;">Date</th>
                                        <th style="border: 1px solid #000; padding: 8px; font-weight: bold; width: 150px;">Ref. No.</th>
                                        <th style="border: 1px solid #000; padding: 8px; font-weight: bold;">Particulars</th>
                                        <th style="border: 1px solid #000; padding: 8px; font-weight: bold; width: 150px; text-align: right;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($paymentRequest->items as $item)
                                        <tr>
                                            <td style="border: 1px solid #000; padding: 8px;">{{ $item->item_date ? $item->item_date->format('Y-m-d') : '' }}</td>
                                            <td style="border: 1px solid #000; padding: 8px;">{{ $item->ref_no }}</td>
                                            <td style="border: 1px solid #000; padding: 8px;">{{ $item->particulars }}</td>
                                            <td style="border: 1px solid #000; padding: 8px; text-align: right;">{{ number_format($item->amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center" style="border: 1px solid #000; padding: 8px;">No line items added.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #FFD700; font-weight: bold;">
                                        <td colspan="3" style="border: 1px solid #000; padding: 8px; text-align: right;">TOTAL:</td>
                                        <td style="border: 1px solid #000; padding: 8px; text-align: right;">PhP {{ number_format($paymentRequest->total_amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                            
                            <!-- Signature Section -->
                            <div class="signature-section d-flex justify-content-between mt-5" style="font-size: 0.9rem; line-height: 1.4;">
                                <!-- Prepared By -->
                                <div class="signature-box text-center" style="width: 23%;">
                                    <div class="border-bottom border-dark mb-2 pb-1" style="height: 35px; display: flex; align-items: flex-end; justify-content: center; font-weight: bold;">
                                        {{ $paymentRequest->requester->name ?? 'User' }}
                                    </div>
                                    <strong>Prepared By</strong>
                                    <div class="small text-muted">{{ $paymentRequest->created_at->format('Y-m-d') }}</div>
                                </div>

                                <!-- Approved By Director -->
                                <div class="signature-box text-center" style="width: 23%;">
                                    <div class="border-bottom border-dark mb-2 pb-1" style="height: 35px; display: flex; align-items: flex-end; justify-content: center;">
                                        @if($paymentRequest->director_approved_by)
                                            <span style="font-weight: bold; color: #28a745;">
                                                <i class="las la-check"></i> {{ $paymentRequest->directorApprovedBy->name }}
                                            </span>
                                        @else
                                            <span class="text-muted small italic">Awaiting Approval</span>
                                        @endif
                                    </div>
                                    <strong>Approved By (Director)</strong>
                                    <div class="small text-muted">{{ $paymentRequest->director_approved_at ? $paymentRequest->director_approved_at->format('Y-m-d') : '' }}</div>
                                </div>

                                <!-- Checked By Admin Manager -->
                                <div class="signature-box text-center" style="width: 23%;">
                                    <div class="border-bottom border-dark mb-2 pb-1" style="height: 35px; display: flex; align-items: flex-end; justify-content: center;">
                                        @if($paymentRequest->admin_approved_by)
                                            <span style="font-weight: bold; color: #28a745;">
                                                <i class="las la-check"></i> {{ $paymentRequest->adminApprovedBy->name }}
                                            </span>
                                        @else
                                            <span class="text-muted small italic">Awaiting Approval</span>
                                        @endif
                                    </div>
                                    <strong>Checked By (Admin)</strong>
                                    <div class="small text-muted">{{ $paymentRequest->admin_approved_at ? $paymentRequest->admin_approved_at->format('Y-m-d') : '' }}</div>
                                </div>

                                <!-- Checked By Finance Manager -->
                                <div class="signature-box text-center" style="width: 23%;">
                                    <div class="border-bottom border-dark mb-2 pb-1" style="height: 35px; display: flex; align-items: flex-end; justify-content: center;">
                                        @if($paymentRequest->finance_approved_by)
                                            <span style="font-weight: bold; color: #28a745;">
                                                <i class="las la-check"></i> {{ $paymentRequest->financeApprovedBy->name }}
                                            </span>
                                        @else
                                            <span class="text-muted small italic">Awaiting Approval</span>
                                        @endif
                                    </div>
                                    <strong>Checked By (Finance)</strong>
                                    <div class="small text-muted">{{ $paymentRequest->finance_approved_at ? $paymentRequest->finance_approved_at->format('Y-m-d') : '' }}</div>
                                </div>
                            </div>

                        </div> <!-- End Letter -->

                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('styles')
    <style>
        .blank-field-val {
            border-bottom: 1px solid #000;
            padding: 0 5px;
            display: inline-block;
            min-width: 150px;
        }

        .border-left-success {
            border-left: 4px solid #28a745 !important;
        }

        .border-left-danger {
            border-left: 4px solid #dc3545 !important;
        }

        .border-left-info {
            border-left: 4px solid #17a2b8 !important;
        }

        @media print {
            .sidebar,
            .header,
            .report-actions,
            .card:not(.shadow) {
                display: none !important;
            }

            .card.shadow {
                box-shadow: none !important;
                border: none !important;
            }

            .card-body {
                padding: 0 !important;
            }

            .generated-letter {
                border: none !important;
                padding: 0 !important;
            }

            body {
                background: #fff !important;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function handlePaymentRequestReject(form) {
            if (typeof window.openTwoStepRejectionFlow === 'function') {
                window.openTwoStepRejectionFlow('', function(reason) {
                    let input = form.querySelector('input[name="rejection_reason"]');
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'rejection_reason';
                        form.appendChild(input);
                    }
                    input.value = reason;
                    form.submit();
                });
            } else {
                let reason = prompt('Please enter rejection reason:');
                if (reason !== null && reason.trim() !== '') {
                    let input = form.querySelector('input[name="rejection_reason"]');
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'rejection_reason';
                        form.appendChild(input);
                    }
                    input.value = reason.trim();
                    form.submit();
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
