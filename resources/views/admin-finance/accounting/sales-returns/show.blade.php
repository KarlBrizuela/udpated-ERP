<x-app-layout :title="'Sales Return details'" :sidebar="$sidebar ?? 'admin-finance'" :role="$role ?? 'Finance Manager'">
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
                    <a href="{{ route('admin-finance.accounting.sales-returns.index') }}" class="btn btn-sm btn-light border fw-bold text-dark d-inline-flex align-items-center justify-content-center" style="height: 38px;">
                        <i class="las la-arrow-left me-1"></i> Back to Logs
                    </a>
                    <form action="{{ route('admin-finance.accounting.sales-returns.destroy', $return->id) }}" method="POST" class="m-0">
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
                                    <span class="text-muted small uppercase fw-bold d-block">Refunded Amount</span>
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
                                        <span class="detail-item-label">Original Order</span>
                                    </div>
                                    <span class="detail-item-val">
                                        <a href="{{ route('admin-finance.sales-order.detail', $return->sales_order_id) }}" class="text-info fw-bold">
                                            {{ $return->salesOrder->so_number }}
                                        </a>
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;"><i class="las la-user fs-18"></i></div>
                                        <span class="detail-item-label">Customer</span>
                                    </div>
                                    <span class="detail-item-val">{{ $return->salesOrder->customer->customer_name ?? 'Walk-in' }}</span>
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
                                        <span class="detail-item-label">Refund Method</span>
                                    </div>
                                    <span class="detail-item-val text-uppercase">{{ $return->refund_method ?? 'N/A' }}</span>
                                </div>
                                <div class="detail-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;"><i class="las la-warehouse fs-18"></i></div>
                                        <span class="detail-item-label">Stock Restored</span>
                                    </div>
                                    <span class="detail-item-val">
                                        @if($return->inventory_restored)
                                            <span class="badge bg-success-light text-success" style="background-color: rgba(40, 167, 69, 0.1);">Yes</span>
                                        @else
                                            <span class="badge bg-secondary-light text-secondary" style="background-color: rgba(108, 117, 125, 0.1);">No</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;"><i class="las la-user-edit fs-18"></i></div>
                                        <span class="detail-item-label">Recorded By</span>
                                    </div>
                                    <span class="detail-item-val">{{ $return->creator->name ?? 'System' }}</span>
                                </div>

                                @if($return->remarks)
                                    <div class="mt-4 p-3 bg-light rounded">
                                        <span class="text-muted small uppercase fw-bold d-block mb-1">Remarks:</span>
                                        <p class="text-dark small mb-0">{{ $return->remarks }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right: Items list and Journal Entries -->
                    <div class="col-lg-8 mb-4">
                        <!-- Items Table -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 8px; height: auto !important;">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="fw-bold text-dark mb-0">Returned Items Detail</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-custom mb-0">
                                        <thead>
                                            <tr>
                                                <th style="text-align: left !important;">Book / Item Title</th>
                                                <th class="text-center" style="width: 120px; text-align: center !important;">Returned Qty</th>
                                                <th class="text-end" style="width: 150px; text-align: right !important;">Unit Price</th>
                                                <th class="text-end" style="width: 160px; text-align: right !important;">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($return->items as $item)
                                                <tr>
                                                    <td class="fw-semibold text-dark">
                                                        {{ $item->book->name ?? 'Unknown Product' }}
                                                    </td>
                                                    <td class="text-center font-w600">{{ $item->returned_qty }}</td>
                                                    <td class="text-end">₱{{ number_format($item->price, 2) }}</td>
                                                    <td class="text-end fw-bold text-dark">₱{{ number_format($item->subtotal, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-end fw-bold text-dark pt-3" style="text-align: right !important;">Refund Total:</td>
                                                <td class="text-end fw-bold text-danger fs-15 pt-3" style="text-align: right !important;">₱{{ number_format($return->refund_amount, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Journal Entries Details -->
                        <div class="card border-0 shadow-sm" style="border-radius: 8px; height: auto !important;">
                            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <h5 class="fw-bold text-dark mb-0">Posted Journal Entry</h5>
                                @if($return->journalEntry)
                                    <a href="{{ route('accounting.journal.show', $return->journalEntry->id) }}" class="btn btn-xs btn-outline-primary border">
                                        View in Journal Logs ({{ $return->journalEntry->entry_no }})
                                    </a>
                                @endif
                            </div>
                            <div class="card-body p-0">
                                @if($return->journalEntry)
                                    <div class="table-responsive">
                                        <table class="table table-custom mb-0">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: left !important;">Account Code & Title</th>
                                                    <th class="text-end" style="width: 150px; text-align: right !important;">Debit</th>
                                                    <th class="text-end" style="width: 150px; text-align: right !important;">Credit</th>
                                                    <th style="text-align: left !important;">Memo / Details</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($return->journalEntry->items as $jeItem)
                                                    <tr>
                                                        <td>
                                                            <div class="{{ $jeItem->credit > 0 ? 'ps-4' : '' }}">
                                                                <span class="fw-bold text-dark small">[{{ $jeItem->account->code }}]</span> 
                                                                <span class="{{ $jeItem->credit > 0 ? 'text-muted' : 'text-dark fw-semibold' }}">{{ $jeItem->account->name }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="text-end text-dark font-w600">
                                                            {{ $jeItem->debit > 0 ? '₱' . number_format($jeItem->debit, 2) : '-' }}
                                                        </td>
                                                        <td class="text-end text-dark font-w600">
                                                            {{ $jeItem->credit > 0 ? '₱' . number_format($jeItem->credit, 2) : '-' }}
                                                        </td>
                                                        <td class="text-muted small">
                                                            {{ $jeItem->memo }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
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
                    title: 'Void Sales Return?',
                    text: "Are you sure you want to void and delete this sales return? This will reverse all ledger postings and deduct the returned quantities from inventory.",
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
