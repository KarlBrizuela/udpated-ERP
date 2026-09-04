<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
            padding-bottom: 80px !important;
        }

        .bdg-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.75rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }

        .btn-bdg-accent {
            background-color: #D9251C;
            border-color: #D9251C;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.85rem;
            border-radius: 6px;
            padding: 8px 16px;
            transition: all 0.2s ease;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-bdg-accent:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
            color: #ffffff;
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
        .form-control {
            border-color: #cbd5e1 !important;
            border-radius: 6px !important;
            color: #000000 !important;
            font-size: 0.85rem !important;
            padding: 8px 12px !important;
        }
        .form-control:focus {
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
                    <a href="{{ route('admin-finance.budgeting.index') }}" class="btn btn-sm btn-light border mb-2 px-3 fw-semibold text-secondary" style="border-radius: 6px;">
                        <i class="las la-arrow-left me-1"></i> Back to Budgeting Matrix
                    </a>
                    <h4 class="fs-22 fw-bold text-dark mb-0">{{ $budget->department }}</h4>
                    <p class="text-muted small mb-0">
                        Code: <span class="font-monospace fw-bold fs-13" style="color: #0f172a !important;">{{ $budget->budget_code }}</span> | 
                        Division: <span class="badge px-2.5 py-1 rounded" style="background-color: rgba(71, 85, 105, 0.08); color: #475569; font-weight: 600; font-size: 0.72rem;">{{ $budget->division }}</span> | 
                        Fiscal Year: <span class="fw-bold" style="color: #0f172a !important;">FY {{ $budget->fiscal_year }}</span>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-bdg-accent d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addLineItemModal">
                        <i class="las la-plus-circle fs-16"></i> Add Line-Item Category
                    </button>
                    <button class="btn btn-sm btn-light border px-3 d-flex align-items-center gap-2 fw-semibold text-secondary" style="height: 38px; border-radius: 6px;" onclick="window.print()">
                        <i class="las la-print fs-16"></i> Print Budget Sheet
                    </button>
                </div>
            </div>
        </div>

        <div class="row" style="align-items: flex-start;">
            <!-- Left: Financial Summary & Forecast -->
            <div class="col-md-5 mb-4">
                <div class="bdg-card mb-4">
                    <h6 class="fw-bold text-uppercase small mb-3" style="color: #475569; letter-spacing: 0.5px; font-size: 0.72rem;">Department Financial Performance</h6>
                    
                    <div class="p-3 rounded mb-3" style="background-color: #D9251C; box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15);">
                        <span class="small d-block text-uppercase fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px; color: rgba(255, 255, 255, 0.85) !important;">Annual Allocated Budget</span>
                        <h3 class="fw-bold mb-0" style="font-size: 1.8rem; color: #ffffff !important;">₱{{ number_format($budget->allocated_budget, 2) }}</h3>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="p-3 border rounded bg-light" style="border-color: #cbd5e1 !important;">
                                <span class="d-block mb-1" style="font-size: 0.72rem; color: #475569; font-weight: 600;">Actual Spend</span>
                                <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 700 !important;">₱{{ number_format($budget->actual_spend, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded bg-light" style="border-color: #cbd5e1 !important;">
                                <span class="d-block mb-1" style="font-size: 0.72rem; color: #475569; font-weight: 600;">Variance (Remaining)</span>
                                <span class="fw-bold fs-15" style="font-weight: 700 !important; color: {{ $budget->variance >= 0 ? '#10b981' : '#ef4444' }} !important;">₱{{ number_format($budget->variance, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded bg-light" style="border-color: #cbd5e1 !important;">
                                <span class="d-block mb-1" style="font-size: 0.72rem; color: #475569; font-weight: 600;">Percentage Used</span>
                                <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 700 !important;">{{ $budget->percentage_used }}%</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded bg-light" style="border-color: #cbd5e1 !important;">
                                <span class="d-block mb-1" style="font-size: 0.72rem; color: #475569; font-weight: 600;">Year-End Forecast</span>
                                <span class="fw-bold fs-15" style="color: #D9251C !important; font-weight: 700 !important;">₱{{ number_format($budget->forecasted_spend, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <hr style="border-color: #cbd5e1 !important;">

                    <h6 class="fw-bold text-dark small mb-3" style="font-size: 0.8rem;">Burn Rate Gauge</h6>
                    <div class="progress mb-2" style="height: 12px; border-radius: 6px;">
                        <div class="progress-bar {{ $budget->percentage_used >= 90 ? 'bg-danger' : ($budget->percentage_used >= 75 ? 'bg-warning' : 'bg-success') }}" style="width: {{ min(100, $budget->percentage_used) }}%; border-radius: 6px;"></div>
                    </div>
                    <span class="small text-muted d-block text-center" style="font-size: 0.72rem;">{{ $budget->percentage_used }}% of total department budget consumed</span>

                    @if($budget->notes)
                    <div class="mt-3 p-3 bg-light rounded border" style="border-color: #cbd5e1 !important;">
                        <span class="small fw-bold d-block mb-1" style="color: #475569;">Justification Notes:</span>
                        <span class="small text-dark" style="font-size: 0.78rem; line-height: 1.4;">{{ $budget->notes }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right: Line-Item Expenditure Categories -->
            <div class="col-md-7 mb-4">
                <div class="bdg-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Line-Item Expenditure Categories</h5>
                            <p class="text-muted small mb-0">Category-level breakdown of allocated vs actual spending</p>
                        </div>
                        <span class="badge bg-danger-subtle text-danger px-3 py-2 fw-bold" style="border-radius: 20px; font-size: 0.72rem; letter-spacing: 0.3px;">
                            Categories: {{ $budget->lineItems->count() }}
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-modern align-middle">
                            <thead>
                                <tr>
                                    <th>Expense Category</th>
                                    <th class="text-end">Allocated</th>
                                    <th class="text-end">Actual Spend</th>
                                    <th class="text-end">Variance</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($budget->lineItems as $item)
                                @php
                                    $var = $item->allocated_amount - $item->actual_amount;
                                @endphp
                                <tr>
                                    <td><span class="fw-bold text-dark">{{ $item->account_category }}</span></td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($item->allocated_amount, 2) }}</td>
                                    <td class="text-end fw-bold text-danger">₱{{ number_format($item->actual_amount, 2) }}</td>
                                    <td class="text-end fw-bold {{ $var >= 0 ? 'text-success' : 'text-danger' }}">₱{{ number_format($var, 2) }}</td>
                                    <td><span class="text-muted small">{{ $item->notes ?: 'N/A' }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="las la-list fs-48 mb-2 d-block text-secondary"></i>
                                        No line-item expense categories recorded yet. Click "Add Line-Item Category" above.
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

    <!-- MODAL: ADD LINE-ITEM CATEGORY -->
    <div class="modal fade" id="addLineItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin-finance.budgeting.line-item.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="department_budget_id" value="{{ $budget->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-dark"><i class="las la-plus-circle me-2 text-danger"></i> Add Line-Item Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label">Account Expense Category <span class="text-danger">*</span></label>
                            <input type="text" name="account_category" class="form-control" placeholder="e.g. Raw Materials Paper, Machine Repair & Parts, Staff Salaries, Electricity" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Allocated Category Budget (₱) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="allocated_amount" class="form-control" placeholder="500000.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Actual Realized Spend (₱) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="actual_amount" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes / Details</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Category specifications or notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 6px;">Cancel</button>
                        <button type="submit" class="btn btn-bdg-accent px-4">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
