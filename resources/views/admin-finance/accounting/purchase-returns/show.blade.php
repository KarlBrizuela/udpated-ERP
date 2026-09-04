<x-app-layout :title="'Purchase Return details'" :sidebar="$sidebar ?? 'admin-finance'" :role="$role ?? 'Finance Manager'">
    @push('styles')
    <style>
        /* Widescreen Spacing Override */
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
            padding-bottom: 80px !important;
        }

        /* Detail List Item */
        .detail-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #e2e8f0 !important;
        }
        .detail-item:last-child {
            border-bottom: none !important;
        }
        .detail-item-label {
            color: #475569 !important;
            font-size: 0.82rem !important;
        }
        .detail-item-val {
            color: #0f172a !important;
            font-weight: 700;
            font-size: 0.85rem !important;
            text-align: right !important;
        }

        /* Modern Tables styling */
        .table-custom {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        .table-custom thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
            font-size: 0.72rem !important;
            padding: 12px 16px !important;
            border-bottom: 2px solid #e2e8f0 !important;
        }
        .table-custom tbody td {
            padding: 12px 16px !important;
            font-size: 0.84rem !important;
            color: #475569 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            vertical-align: middle !important;
        }
        .table-custom tfoot td {
            padding: 12px 16px !important;
            font-size: 0.85rem !important;
            vertical-align: middle !important;
        }
    </style>
    @endpush

    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('admin-finance.accounting.purchase-returns.index') }}" class="btn btn-sm btn-light border fw-bold text-dark d-inline-flex align-items-center justify-content-center" style="height: 38px;">
                        <i class="las la-arrow-left me-1"></i> Back to Logs
                    </a>
                    <form action="{{ route('admin-finance.accounting.purchase-returns.destroy', $return->id) }}" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger fw-bold d-inline-flex align-items-center justify-content-center btn-void-confirm" style="background-color: #D9251C; border-color: #D9251C; height: 38px;">
                            <i class="las la-trash-alt me-1"></i> Void / Delete Return
                        </button>
                    </form>
                </div>

                <div class="row" style="align-items: flex-start;">
                    <!-- Left: Metadata Summary -->
                    <div class="col-lg-4 mb-4">
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 8px; height: auto !important;">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="fw-bold text-dark mb-0">Return Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center py-3 mb-4 rounded" style="background-color: rgba(217, 37, 28, 0.06); border: 1px solid rgba(217, 37, 28, 0.12);">
                                    <span class="text-muted small uppercase fw-bold d-block">Return Refund Value</span>
                                    <h3 class="fw-bold text-danger mb-0 mt-1">₱{{ number_format($return->refund_amount, 2) }}</h3>
                                </div>

                                <div class="detail-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;"><i class="las la-id-badge fs-18"></i></div>
                                        <span class="detail-item-label">Return Number</span>
                                    </div>
                                    <span class="detail-item-val text-primary">{{ $return->return_no }}</span>
                                </div>
                                <div class="detail-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;"><i class="las la-file-alt fs-18"></i></div>
                                        <span class="detail-item-label">Supplier Invoice Reference</span>
                                    </div>
                                    <span class="detail-item-val fw-semibold text-dark">
                                        {{ $return->supplierInvoice->invoice_number ?? $return->supplier_invoice_no ?? 'N/A' }}
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;"><i class="las la-user fs-18"></i></div>
                                        <span class="detail-item-label">Supplier / Vendor</span>
                                    </div>
                                    <span class="detail-item-val">{{ $return->supplier->name ?? 'N/A' }}</span>
                                </div>
                                <div class="detail-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;"><i class="las la-calendar fs-18"></i></div>
                                        <span class="detail-item-label">Return Date</span>
                                    </div>
                                    <span class="detail-item-val">{{ $return->return_date->format('F d, Y') }}</span>
                                </div>
                                <div class="detail-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;"><i class="las la-wallet fs-18"></i></div>
                                        <span class="detail-item-label">Refund Status</span>
                                    </div>
                                    <span class="detail-item-val">
                                        @if($return->refund_status === 'applied_to_payable')
                                            <span class="badge bg-light text-dark border">AP APPLIED</span>
                                        @else
                                            <span class="badge bg-danger-light text-danger" style="background-color: rgba(217, 37, 28, 0.06); padding: 4px 8px; border-radius: 4px;">RECEIVABLE</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;"><i class="las la-warehouse fs-18"></i></div>
                                        <span class="detail-item-label">Stock Deducted</span>
                                    </div>
                                    <span class="detail-item-val">
                                        @if($return->inventory_deducted)
                                            <span class="text-success fw-bold"><i class="las la-check-circle fs-16 align-middle me-1"></i> Yes, restocked</span>
                                        @else
                                            <span class="text-muted">No</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;"><i class="las la-user-tag fs-18"></i></div>
                                        <span class="detail-item-label">Recorded By</span>
                                    </div>
                                    <span class="detail-item-val">{{ $return->creator->name ?? 'System' }}</span>
                                </div>
                                <div class="mt-3">
                                    <span class="text-muted small uppercase fw-bold">Notes / Reason for Return:</span>
                                    <div class="p-3 bg-light rounded mt-2 border text-dark" style="font-size: 0.84rem; line-height: 1.5;">
                                        {{ $return->notes ?: 'No notes provided.' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-8 mb-4">
                        <!-- Returned Items Detail Card -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 8px; height: auto !important;">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="fw-bold text-dark mb-0">Returned Items Detail</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-custom mb-0">
                                        <thead>
                                            <tr>
                                                <th>Book / Item Title</th>
                                                <th class="text-center" style="width: 140px;">Returned Qty</th>
                                                <th class="text-end" style="width: 160px;">Unit Cost</th>
                                                <th class="text-end" style="width: 180px;">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($return->items as $item)
                                                <tr>
                                                    <td>
                                                        <span class="fw-bold text-dark">
                                                            {{ $item->product->name ?? ($item->product->book->name ?? ($item->product->item->name ?? 'Unknown Product')) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center fw-semibold text-dark">
                                                        {{ $item->returned_qty }}
                                                    </td>
                                                    <td class="text-end">
                                                        ₱{{ number_format($item->unit_cost, 2) }}
                                                    </td>
                                                    <td class="text-end fw-bold text-dark">
                                                        ₱{{ number_format($item->subtotal, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-light border-top">
                                                <td colspan="3" class="text-end fw-bold text-dark pt-3">Total Refund Amount:</td>
                                                <td class="text-end fw-bold fs-16 text-danger pt-3">₱{{ number_format($return->refund_amount, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Posted Journal Entry Card -->
                        <div class="card border-0 shadow-sm" style="border-radius: 8px; height: auto !important;">
                            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold text-dark mb-0">Posted Journal Entry</h5>
                                @if($return->journalEntry)
                                    <span class="badge bg-success-light text-success" style="background-color: rgba(40, 167, 69, 0.1); font-weight: 600;">{{ $return->journalEntry->entry_no }}</span>
                                @endif
                            </div>
                            <div class="card-body p-0">
                                @if($return->journalEntry)
                                    <div class="table-responsive">
                                        <table class="table table-custom mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Account Code & Title</th>
                                                    <th class="text-end" style="width: 150px;">Debit</th>
                                                    <th class="text-end" style="width: 150px;">Credit</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $totalDebits = 0;
                                                    $totalCredits = 0;
                                                @endphp
                                                @foreach($return->journalEntry->items as $jItem)
                                                    @php
                                                        $totalDebits += $jItem->debit;
                                                        $totalCredits += $jItem->credit;
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <div class="{{ $jItem->credit > 0 ? 'ps-4' : 'fw-semibold text-dark' }}">
                                                                {{ $jItem->account->code ?? 'N/A' }} - {{ $jItem->account->name ?? 'N/A' }}
                                                                @if($jItem->memo)
                                                                    <span class="d-block text-muted small fw-normal">{{ $jItem->memo }}</span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td class="text-end text-dark">
                                                            {{ $jItem->debit > 0 ? '₱' . number_format($jItem->debit, 2) : '-' }}
                                                        </td>
                                                        <td class="text-end text-dark">
                                                            {{ $jItem->credit > 0 ? '₱' . number_format($jItem->credit, 2) : '-' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-light border-top">
                                                    <td class="text-end fw-bold text-dark">Total:</td>
                                                    <td class="text-end fw-bold text-dark">₱{{ number_format($totalDebits, 2) }}</td>
                                                    <td class="text-end fw-bold text-dark">₱{{ number_format($totalCredits, 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4 text-muted">
                                        <i class="las la-exclamation-circle fs-30 mb-1 text-warning"></i>
                                        <p class="mb-0 font-w600">No journal entry associated with this return.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $(document).on('click', '.btn-void-confirm', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Void Purchase Return?',
                    text: "Are you sure you want to void and delete this purchase return? This will reverse all ledger postings, re-add the returned cost back to the invoice payable amount, and restore/re-add the returned quantities back to inventory.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#D9251C',
                    cancelButtonColor: '#475569',
                    confirmButtonText: 'Yes, void it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
