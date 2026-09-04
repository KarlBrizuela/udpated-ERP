<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
            padding-bottom: 80px !important;
        }

        .inv-header-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem 1.75rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            margin-bottom: 1.5rem;
        }

        .btn-inv-primary {
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

        .btn-inv-primary:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
            color: #ffffff;
        }

        /* KPI Cards Style Matching COA */
        .inv-kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .inv-kpi-card:hover {
            transform: translateY(-4px) !important;
            border-color: #D9251C !important;
            box-shadow: 0 12px 24px -5px rgba(217, 37, 28, 0.12), 0 4px 12px -2px rgba(217, 37, 28, 0.08) !important;
        }

        .kpi-icon-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
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
        <!-- Master Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="inv-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(217, 37, 28, 0.08); color: #D9251C;">
                            <i class="las la-chart-pie fs-24"></i>
                        </div>
                        <div>
                            <h4 class="fs-20 mb-1 fw-bold text-dark" style="letter-spacing: -0.3px;">Investments Ledger</h4>
                            <p class="text-muted small mb-0">Record and track institutional investment assets, time deposits, stocks, and funds.</p>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-inv-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addInvestmentModal">
                            <i class="las la-plus-circle fs-16"></i> Add Investment Asset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric summary cards -->
        <div class="row g-3 mb-4">
            <!-- Current Valuation Card -->
            <div class="col-md-4">
                <div class="inv-kpi-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="kpi-icon-wrapper" style="background-color: rgba(16, 185, 129, 0.08); color: #16a34a;">
                            <i class="las la-coins fs-20"></i>
                        </div>
                        <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                    </div>
                    <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Portfolio Valuation</h6>
                    <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Current aggregate market value of all holdings.</p>
                    <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                        <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Current Value</span>
                        <span class="fw-bold fs-15 text-dark" style="font-weight: 800 !important;">₱{{ number_format($metrics['total_current_val'] ?? $metrics['total_current_value'] ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Total Invested Card -->
            <div class="col-md-4">
                <div class="inv-kpi-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="kpi-icon-wrapper" style="background-color: rgba(37, 99, 235, 0.08); color: #2563eb;">
                            <i class="las la-piggy-bank fs-20"></i>
                        </div>
                        <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                    </div>
                    <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Principal Invested</h6>
                    <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Total cash principal allocated inside accounts.</p>
                    <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                        <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Principal</span>
                        <span class="fw-bold fs-15 text-dark" style="font-weight: 800 !important;">₱{{ number_format($metrics['total_principal'], 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Total Assets Card -->
            <div class="col-md-4">
                <div class="inv-kpi-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="kpi-icon-wrapper" style="background-color: rgba(217, 37, 28, 0.08); color: #D9251C;">
                            <i class="las la-briefcase fs-20"></i>
                        </div>
                        <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                    </div>
                    <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Investment Assets</h6>
                    <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Total registered individual investment records.</p>
                    <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                        <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Assets Count</span>
                        <span class="fw-bold fs-15 text-dark" style="font-weight: 800 !important;">{{ $metrics['total_items_count'] }} Assets</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Investment Ledger Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #e2e8f0 !important;">
                    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="las la-list me-2 fs-18"></i>Investment Records</h6>
                        
                        <form method="GET" action="{{ route('admin-finance.donations.index') }}" class="d-flex align-items-center">
                            <div class="input-group" style="width: 280px;">
                                <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 10px; border-top-left-radius: 4px; border-bottom-left-radius: 4px;">
                                    <i class="las la-search text-muted fs-16"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0 border-end-0" placeholder="Search investment..." value="{{ $search ?? '' }}" style="height: 38px; border-color: #cbd5e1; font-size: 0.82rem; padding-left: 0; outline: none; box-shadow: none;">
                                <button type="submit" class="btn text-white px-3 d-inline-flex align-items-center justify-content-center" style="height: 38px; background-color: #D9251C; border-color: #D9251C; border-top-right-radius: 4px; border-bottom-right-radius: 4px; font-weight: 600; font-size: 0.82rem; line-height: 1 !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                                    Search
                                </button>
                            </div>
                            @if(!empty($search))
                                <a href="{{ route('admin-finance.investments.index') }}" class="btn btn-sm btn-light border ms-2 d-inline-flex align-items-center justify-content-center" style="height: 38px; padding: 0 12px; border-radius: 4px; font-weight: 600; color: #475569;">
                                    Clear
                                </a>
                            @endif
                        </form>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-modern align-middle">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Asset Name</th>
                                        <th>Category</th>
                                        <th>Institution / Bank</th>
                                        <th class="text-end">Principal</th>
                                        <th class="text-end">Current Value</th>
                                        <th>Acquisition Date</th>
                                        <th class="text-center" style="width: 120px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($investments as $inv)
                                    <tr>
                                        <td><span class="fw-bold text-dark font-monospace">{{ $inv->portfolio_code ?: 'INV-'.$inv->id }}</span></td>
                                        <td><span class="fw-bold text-dark">{{ $inv->name }}</span></td>
                                        <td><span class="badge bg-secondary-subtle text-secondary px-2.5 py-1">{{ $inv->type }}</span></td>
                                        <td><span class="text-muted small fw-medium">{{ $inv->institution }}</span></td>
                                        <td class="text-end fw-bold" style="color: #475569 !important;">₱{{ number_format($inv->principal_amount, 2) }}</td>
                                        <td class="text-end fw-bold text-dark">₱{{ number_format($inv->current_value, 2) }}</td>
                                        <td><span class="text-muted small">{{ $inv->acquisition_date ? \Carbon\Carbon::parse($inv->acquisition_date)->format('M d, Y') : 'N/A' }}</span></td>
                                        <td class="text-center">
                                            <a href="{{ route('admin-finance.investments.show', $inv->id) }}" class="btn btn-info shadow btn-xs sharp text-white me-1" title="View Details">
                                                <i class="las la-eye"></i>
                                            </a>
                                            <form action="{{ route('admin-finance.investments.destroy', $inv->id) }}" method="POST" class="d-inline delete-investment-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger shadow btn-xs sharp btn-delete-investment-confirm" title="Delete Investment">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="las la-chart-pie fs-48 mb-2 d-block text-secondary"></i>
                                            No investment records found. Click "Add Investment Asset" above to add a record.
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
    </div>

    <!-- Simple Add Investment Modal -->
    <div class="modal fade" id="addInvestmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark"><i class="las la-plus-circle me-2 text-danger"></i> Add Investment Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin-finance.investments.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label">Asset Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g., BDO Time Deposit 5-Year" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Category / Type *</label>
                                <select name="type" class="form-select" required>
                                    <option value="Time Deposits">Time Deposits</option>
                                    <option value="Stocks">Stocks</option>
                                    <option value="Mutual Funds">Mutual Funds</option>
                                    <option value="Bonds">Bonds</option>
                                    <option value="Money Market">Money Market</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Institution / Bank *</label>
                                <input type="text" name="institution" class="form-control" placeholder="e.g., BDO Unibank" required>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Principal Amount (₱) *</label>
                                <input type="number" step="0.01" name="principal_amount" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Current Value (₱) *</label>
                                <input type="number" step="0.01" name="current_value" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Acquisition Date *</label>
                            <input type="date" name="acquisition_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes / Remarks</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes or details..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 6px;">Cancel</button>
                        <button type="submit" class="btn btn-inv-primary px-4">Save Investment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-delete-investment-confirm');
                if (btn) {
                    e.preventDefault();
                    const form = btn.closest('form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This action will permanently delete this investment asset record and all historically logged dividend payouts.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#D9251C',
                        cancelButtonColor: '#475569',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
