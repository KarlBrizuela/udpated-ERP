<x-app-layout :title="'General Ledger'" :sidebar="$sidebar ?? 'admin-finance'" :role="$role ?? 'Finance Manager'">
    @push('styles')
    <style>
        /* Widescreen Spacing Override */
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
        }

        /* Modern Table Style (Clean, borderless outside) */
        .table-responsive {
            border: none !important;
        }
        .table-modern {
            border-collapse: collapse !important;
        }
        .table-modern thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
            padding: 12px 16px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
        }
        .table-modern tbody td {
            padding: 12px 16px !important;
            color: #475569 !important;
            font-size: 0.84rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
        }
        .table-modern tbody tr {
            transition: all 0.15s ease-in-out !important;
        }
        .table-modern tbody tr:hover {
            background-color: #f8fafc !important;
        }
        .text-deep-black {
            color: #0f172a !important;
            font-weight: 600 !important;
        }

        /* KPI Metric Cards styling */
        .kpi-card {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.04);
        }
        .kpi-value {
            font-size: 1.6rem;
            font-weight: 700;
            color: #0f172a;
        }
        .kpi-label {
            font-size: 0.72rem;
            color: #475569;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* Custom Capsule Pagination styling */
        .pagination .page-item.active .page-link {
            background-color: #D9251C !important;
            border-color: #D9251C !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15) !important;
            border-radius: 4px !important;
        }
        .pagination .page-link {
            color: #475569 !important;
            border-color: #cbd5e1 !important;
            padding: 8px 14px !important;
            font-size: 0.85rem !important;
            transition: all 0.15s ease-in-out !important;
            background-color: #ffffff !important;
            border-radius: 4px !important;
            margin: 0 2px !important;
        }
        .pagination .page-link:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        @media print {
            .sidebar-wrapper, .header, .btn-print, #paginationContainer, .card-header p.text-muted, .form-label { display: none !important; }
            .content-body { margin-left: 0 !important; padding: 0 !important; }
            .card { border: none !important; box-shadow: none !important; }
            .table-modern thead th { background-color: #f1f5f9 !important; }
        }
    </style>
    @endpush

    <div class="container-fluid p-0">
        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fs-22 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">General Ledger</h4>
                <p class="text-muted small mb-0">Central accounting ledger for all posted transaction journals.</p>
            </div>
        </div>

        @if($selectedAccount)
        <!-- Ledger Balances Summary Cards (Total Debits, Total Credits, Net Activity) -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-label">Total Debits (Period)</div>
                            <div class="kpi-value mt-1 text-success">₱{{ number_format($totalDebits, 2) }}</div>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(16, 185, 129, 0.08);">
                            <i class="las la-arrow-down fs-24" style="color: #10b929;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-label">Total Credits (Period)</div>
                            <div class="kpi-value mt-1 text-danger">₱{{ number_format($totalCredits, 2) }}</div>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(219, 37, 28, 0.08);">
                            <i class="las la-arrow-up fs-24" style="color: #D9251C;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                        @php
                            $netActivity = $totalDebits - $totalCredits;
                            $isNetDebit = $netActivity >= 0;
                        @endphp
                        <div>
                            <div class="kpi-label">Net Period Activity</div>
                            <div class="kpi-value mt-1 {{ $isNetDebit ? 'text-primary' : 'text-warning' }}">
                                ₱{{ number_format(abs($netActivity), 2) }} {{ $isNetDebit ? '(DR)' : '(CR)' }}
                            </div>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(59, 130, 246, 0.08);">
                            <i class="las la-balance-scale fs-24" style="color: #3b82f6;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ledger Table Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;">
            <!-- Card Header -->
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fs-18 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Account: {{ $selectedAccount->code }} - {{ $selectedAccount->name }}</h4>
                        <p class="text-muted small mb-0">Classification: <strong>{{ $selectedAccount->type }}</strong> ({{ $selectedAccount->category }})</p>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Filters Inside Card (Left/Right Layout) -->
                <form action="{{ route('admin-finance.accounting.general-ledger') }}" method="GET" class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3 btn-print">
                    <!-- Left side: Select & Dates -->
                    <div class="d-flex flex-wrap align-items-end gap-3 flex-grow-1">
                        <div style="min-width: 280px; flex: 2;">
                            <label for="account_id" class="form-label fw-bold text-muted small" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Select Account</label>
                            <select name="account_id" id="account_id" class="form-select" onchange="this.form.submit()" style="height: 38px; border-color: #cbd5e1; border-radius: 4px; font-size: 0.85rem; color: #000000; background-color: #ffffff;">
                                @foreach($accounts->groupBy('type') as $type => $group)
                                    <optgroup label="{{ strtoupper($type) }}">
                                        @foreach($group as $acc)
                                            <option value="{{ $acc->id }}" {{ $accountId == $acc->id ? 'selected' : '' }}>
                                                {{ $acc->code }} - {{ $acc->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div style="min-width: 140px; flex: 1;">
                            <label for="start_date" class="form-label fw-bold text-muted small" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}" style="height: 38px; border-color: #cbd5e1; border-radius: 4px; font-size: 0.85rem; color: #000000; background-color: #ffffff;">
                        </div>
                        <div style="min-width: 140px; flex: 1;">
                            <label for="end_date" class="form-label fw-bold text-muted small" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}" style="height: 38px; border-color: #cbd5e1; border-radius: 4px; font-size: 0.85rem; color: #000000; background-color: #ffffff;">
                        </div>
                    </div>

                    <!-- Right side: Actions -->
                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" class="btn text-white font-w600" style="background-color: #D9251C; height: 38px; border-radius: 4px; border: none; padding: 0 20px; font-size: 0.85rem;">Apply Filters</button>
                        <button type="button" onclick="window.print()" class="btn btn-light border d-flex align-items-center justify-content-center btn-print" style="height: 38px; width: 44px; border-radius: 4px;" title="Print Ledger Card">
                            <i class="las la-print fs-18"></i>
                        </button>
                    </div>
                </form>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-modern align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 140px;">Posting Date</th>
                                <th style="width: 160px;">Voucher Ref</th>
                                <th style="width: 200px;">Payee / Entity</th>
                                <th>Memo / Description</th>
                                <th class="text-end" style="width: 150px;">Debit</th>
                                <th class="text-end" style="width: 150px;">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Period Transactions -->
                            @forelse($items as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item->journalEntry->date ?? $item->created_at)->format('Y-m-d') }}</td>
                                <td class="text-deep-black">
                                    @if($item->journalEntry)
                                        <a href="{{ route('accounting.journal.show', $item->journal_entry_id) }}" class="text-danger fw-bold" style="text-decoration: none;">
                                            #{{ $item->journalEntry->entry_no ?? ('JV-' . $item->journal_entry_id) }}
                                        </a>
                                    @else
                                        #JV-{{ $item->journal_entry_id }}
                                    @endif
                                </td>
                                <td>{{ $item->name ?: '—' }}</td>
                                <td>{{ $item->memo ?: ($item->journalEntry->memo ?? 'Accounting Entry Item') }}</td>
                                <td class="text-end text-deep-black text-success">
                                    @if($item->debit > 0)
                                        ₱{{ number_format($item->debit, 2) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-end text-deep-black text-danger">
                                    @if($item->credit > 0)
                                        ₱{{ number_format($item->credit, 2) }}
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="mb-3"><i class="las la-history fs-40 text-light"></i></div>
                                    <span class="fs-15">No transactions posted to this account during the specified range.</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="d-flex justify-content-between align-items-center mt-4 btn-print">
                    <div class="text-muted small">
                        Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() }} postings
                    </div>
                    <div id="paginationContainer" class="pe-0">
                        {{ $items->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-warning text-center py-4">
            <i class="las la-exclamation-circle me-2 fs-20"></i>No accounts found in the Chart of Accounts. Please set up accounts first.
        </div>
        @endif
    </div>
</x-app-layout>
