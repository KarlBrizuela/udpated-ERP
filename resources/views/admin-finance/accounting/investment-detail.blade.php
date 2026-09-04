<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
            padding-bottom: 80px !important;
        }

        .inv-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }

        /* Modern Table overrides */
        .table-modern {
            margin-bottom: 0 !important;
            border: none !important;
        }
        .table-modern thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            font-size: 0.72rem !important;
            letter-spacing: 0.8px !important;
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

        /* Form Modal overrides */
        .modal-content {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }
        .modal-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 18px 24px !important;
        }
        .modal-header .modal-title {
            font-size: 0.95rem !important;
            font-weight: 700 !important;
            color: #000000 !important;
        }
        .form-label {
            color: #475569 !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            font-size: 0.72rem !important;
            letter-spacing: 0.5px !important;
        }
        .form-control, .form-select {
            border-color: #cbd5e1 !important;
            border-radius: 6px !important;
            color: #000000 !important;
            font-size: 0.85rem !important;
            padding: 8px 12px !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: #D9251C !important;
            box-shadow: 0 0 0 0.2rem rgba(217, 37, 28, 0.15) !important;
            outline: none !important;
        }
    </style>
    @endpush

    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <a href="{{ route('admin-finance.investments.index') }}" class="btn btn-sm btn-light border mb-2 px-3 fw-semibold text-secondary" style="border-radius: 6px;">
                        <i class="las la-arrow-left me-1"></i> Back to Investments Ledger
                    </a>
                    <h4 class="fs-22 fw-bold text-dark mb-0">{{ $investment->name }}</h4>
                    <p class="text-muted small mb-0">
                        Code: <span class="font-monospace fw-bold fs-13" style="color: #0f172a !important;">{{ $investment->portfolio_code }}</span> | 
                        Class: <span class="badge px-2.5 py-1 rounded" style="background-color: rgba(71, 85, 105, 0.08); color: #475569; font-weight: 600; font-size: 0.72rem;">{{ $investment->type }}</span>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-danger px-3 text-white rounded shadow-sm d-flex align-items-center gap-2 fw-semibold" style="background-color: #D9251C; border-color: #D9251C; height: 38px; border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#recordPayoutModal">
                        <i class="las la-plus-circle fs-16"></i> Record Dividend / Interest
                    </button>
                    <button class="btn btn-sm btn-light border px-3 d-flex align-items-center gap-2 fw-semibold text-secondary" style="height: 38px; border-radius: 6px;" onclick="window.print()">
                        <i class="las la-print fs-16"></i> Print Profile
                    </button>
                </div>
            </div>
        </div>

        <div class="row" style="align-items: flex-start;">
            <!-- Left: Financial Performance & Details -->
            <div class="col-md-5 mb-4">
                <div class="inv-card mb-4">
                    <h6 class="fw-bold text-uppercase small mb-3" style="color: #475569; letter-spacing: 0.5px; font-size: 0.72rem;">Portfolio Financial Summary</h6>
                    
                    <div class="p-3 rounded mb-3" style="background-color: #D9251C; box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15);">
                        <span class="small d-block text-uppercase fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px; color: rgba(255, 255, 255, 0.85) !important;">Current Market Valuation</span>
                        <h3 class="fw-bold mb-0" style="font-size: 1.8rem; color: #ffffff !important;">₱{{ number_format($investment->current_value, 2) }}</h3>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="p-3 border rounded bg-light" style="border-color: #cbd5e1 !important;">
                                <span class="d-block mb-1" style="font-size: 0.72rem; color: #475569; font-weight: 600;">Principal Invested</span>
                                <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 700 !important;">₱{{ number_format($investment->principal_amount, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded bg-light" style="border-color: #cbd5e1 !important;">
                                <span class="d-block mb-1" style="font-size: 0.72rem; color: #475569; font-weight: 600;">Overall ROI (%)</span>
                                <span class="fw-bold fs-15" style="color: #D9251C !important; font-weight: 700 !important;">{{ $investment->roi_percentage }}%</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded bg-light" style="border-color: #cbd5e1 !important;">
                                <span class="d-block mb-1" style="font-size: 0.72rem; color: #475569; font-weight: 600;">Earned Dividends</span>
                                <span class="fw-bold fs-15 text-success" style="font-weight: 700 !important; color: #10b981 !important;">₱{{ number_format($investment->total_dividends, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded bg-light" style="border-color: #cbd5e1 !important;">
                                <span class="d-block mb-1" style="font-size: 0.72rem; color: #475569; font-weight: 600;">Earned Interest</span>
                                <span class="fw-bold fs-15 text-success" style="font-weight: 700 !important; color: #10b981 !important;">₱{{ number_format($investment->total_interest, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <hr style="border-color: #cbd5e1 !important;">

                    <h6 class="fw-bold text-dark small mb-3" style="font-size: 0.8rem;">Institution & Maturity Specs</h6>
                    <div class="mb-3">
                        <span class="text-muted small d-block">Institution / Fund Manager:</span>
                        <span class="fw-bold text-dark fs-14" style="color: #0f172a !important;">{{ $investment->institution }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted small d-block">Stated Interest / Yield Rate:</span>
                        <span class="fw-bold text-dark fs-14" style="color: #0f172a !important;">{{ $investment->interest_rate }}% p.a.</span>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted small d-block">Acquisition / Start Date:</span>
                        <span class="fw-bold text-dark fs-14" style="color: #0f172a !important;">{{ $investment->acquisition_date ? $investment->acquisition_date->format('F d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted small d-block mb-1">Maturity Date & Status:</span>
                        @if($investment->maturity_date)
                            <span class="fw-bold text-dark d-block mb-1" style="color: #0f172a !important;">{{ $investment->maturity_date->format('F d, Y') }}</span>
                            @if($investment->maturity_date->isPast())
                                <span class="badge bg-info-subtle text-info px-2.5 py-1" style="font-size: 0.72rem; font-weight: 600;">Matured on {{ $investment->maturity_date->format('M d, Y') }}</span>
                            @else
                                <span class="badge bg-success-subtle text-success px-2.5 py-1" style="font-size: 0.72rem; font-weight: 600;">Active (Matures in {{ $investment->maturity_date->diffInDays(now()) }} days)</span>
                            @endif
                        @else
                            <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1" style="font-size: 0.72rem; font-weight: 600;">Open-ended / No Maturity</span>
                        @endif
                    </div>
                    @if($investment->notes)
                    <div class="mt-3 p-3 bg-light rounded border" style="border-color: #cbd5e1 !important;">
                        <span class="small fw-bold d-block mb-1" style="color: #475569;">Notes:</span>
                        <span class="small text-dark" style="font-size: 0.78rem; line-height: 1.4;">{{ $investment->notes }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right: Dividend & Interest Transaction Log -->
            <div class="col-md-7 mb-4">
                <div class="inv-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Dividend & Interest Payout History</h5>
                            <p class="text-muted small mb-0">Recorded earnings, distributions, and valuation updates</p>
                        </div>
                        <span class="badge bg-danger-subtle text-danger px-3 py-2 fw-bold" style="border-radius: 20px; font-size: 0.72rem; letter-spacing: 0.3px;">
                            Total Gains: ₱{{ number_format($investment->total_return, 2) }}
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-modern align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Transaction Type</th>
                                    <th>Ref / Check No</th>
                                    <th class="text-end">Payout Amount</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($investment->transactions as $tx)
                                <tr>
                                    <td><span class="fw-bold text-dark small">{{ $tx->transaction_date ? $tx->transaction_date->format('M d, Y') : 'N/A' }}</span></td>
                                    <td>
                                        @if($tx->transaction_type === 'Dividend')
                                            <span class="badge bg-success-subtle text-success px-2.5 py-1" style="font-size: 0.72rem; font-weight: 600;">Dividend Payout</span>
                                        @elseif($tx->transaction_type === 'Interest')
                                            <span class="badge bg-info-subtle text-info px-2.5 py-1" style="font-size: 0.72rem; font-weight: 600;">Interest Payment</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1" style="font-size: 0.72rem; font-weight: 600;">{{ $tx->transaction_type }}</span>
                                        @endif
                                    </td>
                                    <td><span class="font-monospace text-muted small">{{ $tx->reference_no ?: 'N/A' }}</span></td>
                                    <td class="text-end fw-bold text-success">₱{{ number_format($tx->amount, 2) }}</td>
                                    <td><span class="text-muted small">{{ $tx->notes ?: 'N/A' }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="las la-history fs-48 mb-2 d-block text-secondary"></i>
                                        No payouts or earnings recorded yet. Click "Record Dividend / Interest" above to log a transaction.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: RECORD DIVIDEND / INTEREST -->
    <div class="modal fade" id="recordPayoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin-finance.investments.transaction.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="investment_id" value="{{ $investment->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-dark"><i class="las la-hand-holding-usd me-2 text-danger"></i> Record Earnings or Valuation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label">Transaction Type <span class="text-danger">*</span></label>
                            <select name="transaction_type" class="form-select" required>
                                <option value="Dividend">Dividend Payout</option>
                                <option value="Interest">Interest Payment</option>
                                <option value="Valuation Update">Market Valuation Update</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Transaction / Payout Date <span class="text-danger">*</span></label>
                            <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount (₱) *</label>
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reference / Check Number</label>
                            <input type="text" name="reference_no" class="form-control" placeholder="e.g. DIV-884920, CHK-99201">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Quarterly dividend distribution, quarterly interest..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 6px;">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-danger px-4" style="background-color: #D9251C; border-color: #D9251C; height: 38px; border-radius: 6px;">Save Transaction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
