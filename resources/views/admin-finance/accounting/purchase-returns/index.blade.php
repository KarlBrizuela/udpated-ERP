<x-app-layout :title="'Purchase Returns'" :sidebar="$sidebar ?? 'admin-finance'" :role="$role ?? 'Finance Manager'">
    @push('styles')
    <style>
        /* Widescreen Spacing Override */
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
            padding-bottom: 80px !important;
        }

        /* Modern Table Design */
        .table-responsive {
            border: none !important;
        }
        .table-modern {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        .table-modern thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
            font-size: 0.72rem !important;
            padding: 12px 16px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            border-top: none !important;
        }
        .table-modern tbody td {
            padding: 12px 16px !important;
            font-size: 0.84rem !important;
            color: #475569 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            vertical-align: middle !important;
        }
        .table-modern tbody tr {
            transition: all 0.15s ease-in-out !important;
        }
        .table-modern tbody tr:hover {
            background-color: #f8fafc !important;
        }
        .table-modern tbody tr td .highlight-text {
            color: #0f172a !important;
            font-weight: 600;
        }

        /* Search input group styling */
        .search-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .custom-search-input {
            height: 38px;
            border-color: #cbd5e1;
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
            font-size: 0.82rem;
            padding-left: 0;
            outline: none;
            box-shadow: none;
        }
        .custom-search-input:focus {
            border-color: #D9251C !important;
            box-shadow: 0 0 0 0.2rem rgba(217, 37, 28, 0.15) !important;
        }
    </style>
    @endpush

    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">

                <div class="card border-0 shadow-sm" style="border-radius: 8px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h4 class="fs-18 mb-0 fw-bold text-dark" style="letter-spacing: -0.5px;">Purchase Returns Log</h4>
                            <p class="text-muted small mb-0">Record and track goods returned to suppliers and balance/receivable adjustments.</p>
                        </div>
                        <div class="d-flex align-items-center gap-3 ms-auto">
                            <form action="{{ route('admin-finance.accounting.purchase-returns.index') }}" method="GET" class="search-container">
                                <div class="input-group input-group-sm" style="width: 320px;">
                                    <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 10px; border-top-left-radius: 4px; border-bottom-left-radius: 4px;">
                                        <i class="las la-search text-muted fs-16"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0 custom-search-input" placeholder="Search Return, Supplier or Invoice..." value="{{ request('search') }}" style="border-top-right-radius: 0 !important; border-bottom-right-radius: 0 !important;">
                                    @if(request('search'))
                                        <a href="{{ route('admin-finance.accounting.purchase-returns.index') }}" class="btn btn-sm text-white d-inline-flex align-items-center justify-content-center" style="background-color: #64748b; height: 38px; border-top-right-radius: 4px; border-bottom-right-radius: 4px; padding: 0 16px; font-weight: 600; border: 1px solid #64748b;">
                                            Clear
                                        </a>
                                    @else
                                        <button type="submit" class="btn btn-sm text-white" style="background-color: #D9251C; height: 38px; border-top-right-radius: 4px; border-bottom-right-radius: 4px; padding: 0 16px; font-weight: 600; border: 1px solid #D9251C;">
                                            Search
                                        </button>
                                    @endif
                                </div>
                            </form>
                            <a href="{{ route('admin-finance.accounting.purchase-returns.create') }}" class="btn btn-primary rounded d-flex align-items-center" style="gap: 0.5rem; padding: 0.5rem 1rem; height: 38px; min-height: 38px; line-height: 1.5; box-sizing: border-box; border: none; background: #D9251C; color: #ffffff; font-weight: 600; white-space: nowrap; box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15);">
                                <i class="las la-plus" style="font-size: 1rem; line-height: 1; margin: 0; padding: 0; background: transparent; border: none; box-shadow: none;"></i>
                                <span>Record Return</span>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-modern mb-0">
                                <thead>
                                    <tr>
                                        <th>Return No</th>
                                        <th>Supplier Invoice</th>
                                        <th>Supplier</th>
                                        <th>Return Date</th>
                                        <th>Refund Status</th>
                                        <th class="text-center">Deducted</th>
                                        <th class="text-end">Refund Amount</th>
                                        <th class="text-center">Ledger Entry</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($returns as $return)
                                        <tr>
                                            <td>
                                                <span class="highlight-text">{{ $return->return_no }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-dark">
                                                    {{ $return->supplierInvoice->invoice_number ?? $return->supplier_invoice_no ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $return->supplier->name ?? 'Unknown Supplier' }}
                                            </td>
                                            <td>
                                                {{ $return->return_date->format('M d, Y') }}
                                            </td>
                                            <td>
                                                @if($return->refund_status === 'applied_to_payable')
                                                    <span class="badge bg-light text-dark border">AP APPLIED</span>
                                                @else
                                                    <span class="badge bg-danger-light text-danger" style="background-color: rgba(217, 37, 28, 0.06);">RECEIVABLE</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($return->inventory_deducted)
                                                    <span class="badge bg-success-light text-success" style="background-color: rgba(40, 167, 69, 0.1); font-weight: 600;">Yes</span>
                                                @else
                                                    <span class="badge bg-secondary-light text-secondary" style="background-color: rgba(108, 117, 125, 0.1); font-weight: 600;">No</span>
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold text-dark">
                                                ₱{{ number_format($return->refund_amount, 2) }}
                                            </td>
                                            <td class="text-center">
                                                @if($return->journalEntry)
                                                    <a href="{{ route('accounting.journal.show', $return->journalEntry->id) }}" class="text-info fw-semibold">
                                                        {{ $return->journalEntry->entry_no }}
                                                    </a>
                                                @else
                                                    <span class="text-danger small">Unposted</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center align-items-center gap-1">
                                                    <a href="{{ route('admin-finance.accounting.purchase-returns.show', $return->id) }}" class="btn btn-info shadow btn-xs sharp text-white" title="View details">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                    <form action="{{ route('admin-finance.accounting.purchase-returns.destroy', $return->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger shadow btn-xs sharp text-white border-0 btn-void-confirm" style="background-color: #D9251C;" title="Void and Delete">
                                                            <i class="las la-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="las la-receipt fs-40 mb-2 text-secondary"></i>
                                                    <p class="mb-0 font-w600">No purchase return transactions logged yet.</p>
                                                    <p class="small mb-0">Record a new purchase return using the "Record Return" button above.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($returns->hasPages())
                        <div class="card-footer bg-white border-top py-3 d-flex justify-content-end pe-4" id="paginationContainer">
                            {{ $returns->onEachSide(0)->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
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
