<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        /* Viewport spacing & gutter override */
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
            padding-bottom: 80px !important;
        }

        /* Paginator Styles (Claretian ERP Brand Guidelines) */
        .pagination .page-item.active .page-link {
            background-color: #D9251C !important;
            border-color: #D9251C !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15) !important;
        }

        .pagination .page-link {
            color: #475569 !important;
            border-color: #cbd5e1 !important;
            padding: 8px 14px !important;
            font-size: 0.85rem !important;
            transition: all 0.15s ease-in-out !important;
            background-color: #ffffff !important;
        }

        .pagination .page-link:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        .rpt-header-card {
            background: #fff;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }

        .hover-row {
            transition: all 0.15s ease-in-out;
        }

        .hover-row:hover {
            background-color: #f8fafc !important;
        }

        .rpt-sidebar-item {
            font-size: 0.86rem;
            font-weight: 500;
            color: #475569 !important;
            transition: all 0.15s ease-in-out;
            padding: 10px 16px !important;
            border-radius: 6px !important;
            margin: 2px 10px !important;
            display: flex;
            align-items: center;
            border: none !important;
        }

        .rpt-sidebar-item i {
            font-size: 1.1rem;
            color: #64748b;
            transition: all 0.15s ease-in-out;
        }

        .rpt-sidebar-item:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }

        .rpt-sidebar-item:hover i {
            color: #0f172a;
        }

        .rpt-sidebar-item.active {
            background-color: rgba(217, 37, 28, 0.08) !important;
            color: #D9251C !important;
            font-weight: 600;
        }

        .rpt-sidebar-item.active i {
            color: #D9251C;
        }

        .rpt-sidebar-header {
            padding: 14px 20px 8px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            font-size: 0.68rem !important;
            letter-spacing: 0.8px !important;
            color: #64748b !important;
            background-color: transparent !important;
            border: none !important;
        }

        /* Modern Table Designs & Enforced Sans-Serif Font */
        .table-responsive {
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .table-responsive .table {
            margin-bottom: 0;
            border: none !important;
        }

        .table-responsive .table th,
        .table-responsive .table td,
        .statement-table th,
        .statement-table td {
            font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        }

        .table-responsive .table th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            font-size: 0.72rem !important;
            letter-spacing: 0.8px !important;
            padding: 12px 16px !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            border-bottom: 2px solid #e2e8f0 !important;
        }

        .table-responsive .table td {
            padding: 12px 16px !important;
            font-size: 0.84rem !important;
            color: #475569 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            border-bottom: 1px solid #f1f5f9 !important;
            background-color: transparent !important;
        }

        .table-responsive .table td .highlight-text {
            color: #0f172a !important;
            font-weight: 600;
        }

        .table-responsive .table tbody tr {
            transition: all 0.15s ease-in-out !important;
        }

        .table-responsive .table tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* Input styling & Enforced Sans-Serif Font */
        .form-control-custom {
            height: 38px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            color: #000000 !important;
            font-size: 0.85rem !important;
            outline: none !important;
            box-shadow: none !important;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        }

        .form-control-custom:focus {
            border-color: #D9251C !important;
            box-shadow: 0 0 0 3px rgba(217, 37, 28, 0.1) !important;
        }

        /* Statement Card Layouts */
        .statement-table th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            font-size: 0.72rem !important;
            letter-spacing: 0.8px !important;
            padding: 10px 12px !important;
            border: none !important;
            border-bottom: 2px solid #e2e8f0 !important;
        }

        .statement-table td {
            padding: 12px 14px !important;
            font-size: 0.86rem !important;
            color: #475569 !important;
            border: none !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        .statement-table tr {
            transition: all 0.15s ease-in-out !important;
        }
        
        .statement-table tr:hover {
            background-color: #f8fafc !important;
        }

        /* Custom Pagination Styling */
        .pagination .page-item.active .page-link {
            background-color: #D9251C !important;
            border-color: #D9251C !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15) !important;
        }

        .pagination .page-link {
            color: #475569 !important;
            border-color: #cbd5e1 !important;
            padding: 8px 14px !important;
            font-size: 0.85rem !important;
            transition: all 0.15s ease-in-out !important;
            background-color: #ffffff !important;
        }

        .pagination .page-link:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }
    </style>
    @endpush

    <div class="container-fluid p-0">
        <!-- Master Title Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="rpt-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fs-22 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px; color: #0f172a !important;">Financial & Profitability Reports</h4>
                        <p class="small mb-0" style="color: #475569 !important;">Automated financial statements & profitability analysis compiled from live ledger and sales transactions.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Overview Metric Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm h-100" style="border-radius: 10px; border: 1px solid #e2e8f0; background-color: #ffffff;">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(59, 130, 246, 0.08); color: #3b82f6; flex-shrink: 0;">
                            <i class="las la-wallet fs-24"></i>
                        </div>
                        <div>
                            <span class="small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem; color: #475569 !important;">Total Corporate Assets</span>
                            <h4 class="fw-bold mt-1 mb-0" style="letter-spacing: -0.5px; color: #0f172a !important; font-size: 1.2rem;">₱{{ number_format($metrics['total_assets'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100" style="border-radius: 10px; border: 1px solid #e2e8f0; background-color: #ffffff;">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(16, 185, 129, 0.08); color: #10b981; flex-shrink: 0;">
                            <i class="las la-hand-holding-usd fs-24"></i>
                        </div>
                        <div>
                            <span class="small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem; color: #475569 !important;">Total Revenue & Inflows</span>
                            <h4 class="fw-bold mt-1 mb-0" style="letter-spacing: -0.5px; color: #0f172a !important; font-size: 1.2rem;">₱{{ number_format($metrics['total_revenue'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100" style="border-radius: 10px; border: 1px solid #e2e8f0; background-color: #ffffff;">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(245, 158, 11, 0.08); color: #f59e0b; flex-shrink: 0;">
                            <i class="las la-file-invoice-dollar fs-24"></i>
                        </div>
                        <div>
                            <span class="small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem; color: #475569 !important;">Total Expenses & Outflows</span>
                            <h4 class="fw-bold mt-1 mb-0" style="letter-spacing: -0.5px; color: #0f172a !important; font-size: 1.2rem;">₱{{ number_format($metrics['total_expenses'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100" style="border-radius: 10px; border: 1px solid #e2e8f0; background-color: #ffffff;">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(217, 37, 28, 0.08); color: #D9251C; flex-shrink: 0;">
                            <i class="las la-chart-pie fs-24"></i>
                        </div>
                        <div>
                            <span class="small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem; color: #475569 !important;">Net Operating Profit</span>
                            <h4 class="fw-bold mt-1 mb-0" style="color: #D9251C; letter-spacing: -0.5px; font-size: 1.2rem;">₱{{ number_format($metrics['net_profit'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Navigation Sidebar (col-md-3) -->
            <div class="col-md-3">
                <div class="card shadow-sm mb-4" style="border-radius: 10px; border: 1px solid #e2e8f0; background: #ffffff;">
                    <div class="card-body p-0 py-3">
                        <div class="list-group list-group-flush border-0">
                            <div class="rpt-sidebar-header">
                                Financial Statements
                            </div>
                            @php
                                $finStatements = ['Balance Sheet', 'Income Statement', 'Cash Flow', 'Trial Balance', 'General Ledger', 'Subsidiary Ledgers'];
                            @endphp
                            @foreach($finStatements as $rpt)
                                @if(in_array($rpt, $reportsList))
                                <a href="{{ route('admin-finance.financial-reports.index', ['report' => $rpt, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="rpt-sidebar-item {{ $selectedReport == $rpt ? 'active' : '' }}">
                                    <i class="las @if($rpt === 'Balance Sheet') la-balance-scale @elseif($rpt === 'Income Statement') la-file-invoice-dollar @elseif($rpt === 'Cash Flow') la-exchange-alt @elseif($rpt === 'Trial Balance') la-calculator @elseif($rpt === 'Subsidiary Ledgers') la-book-open @else la-book @endif me-2"></i> {{ $rpt }}
                                </a>
                                @endif
                            @endforeach

                            <div class="rpt-sidebar-header">
                                Profitability Analysis
                            </div>
                            @php
                                $profitability = ['Profit by Product', 'Profit by Customer', 'Profit by Sales Channel', 'Profit by Salesperson'];
                            @endphp
                            @foreach($profitability as $rpt)
                                @if(in_array($rpt, $reportsList))
                                <a href="{{ route('admin-finance.financial-reports.index', ['report' => $rpt, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="rpt-sidebar-item {{ $selectedReport == $rpt ? 'active' : '' }}">
                                    <i class="las @if($rpt === 'Profit by Product') la-box @elseif($rpt === 'Profit by Customer') la-user-tie @elseif($rpt === 'Profit by Sales Channel') la-network-wired @else la-user-tag @endif me-2"></i> {{ $rpt }}
                                </a>
                                @endif
                            @endforeach

                            <div class="rpt-sidebar-header">
                                Transaction Logs
                            </div>
                            @php
                                $transactions = ['Sales Reports', 'Expense Reports'];
                            @endphp
                            @foreach($transactions as $rpt)
                                @if(in_array($rpt, $reportsList))
                                <a href="{{ route('admin-finance.financial-reports.index', ['report' => $rpt, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="rpt-sidebar-item {{ $selectedReport == $rpt ? 'active' : '' }}">
                                    <i class="las @if($rpt === 'Sales Reports') la-chart-line @else la-receipt @endif me-2"></i> {{ $rpt }}
                                </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Content Area (col-md-9) -->
            <div class="col-md-9">
                <div class="card shadow-sm mb-4" style="border-radius: 10px; border: 1px solid #e2e8f0; background: #ffffff;">
                    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark fs-18">
                                <i class="las la-file-alt me-2" style="color: #D9251C;"></i>{{ $selectedReport }} Statement
                            </h5>
                            <p class="small mb-0" style="color: #475569 !important;">Compiled from General Ledger & modules for the selected period.</p>
                        </div>
                        <form action="{{ route('admin-finance.financial-reports.index') }}" method="GET" class="d-flex gap-3 align-items-center">
                            <input type="hidden" name="report" value="{{ $selectedReport }}">
                            <span class="small fw-bold text-uppercase" style="color: #475569 !important; letter-spacing: 0.5px; font-size: 0.72rem;">Period:</span>
                            <div class="d-flex align-items-center gap-2">
                                <input type="date" name="start_date" class="form-control-custom" value="{{ $startDate }}" style="width: 160px; padding: 0 12px;">
                                <span class="small" style="color: #475569 !important;">to</span>
                                <input type="date" name="end_date" class="form-control-custom" value="{{ $endDate }}" style="width: 160px; padding: 0 12px;">
                            </div>
                            <button type="submit" class="btn text-white px-3 fw-bold d-inline-flex align-items-center justify-content-center" style="background-color: #D9251C; border-color: #D9251C; height: 38px; border-radius: 6px; font-size: 0.85rem;">
                                <i class="las la-sync me-1"></i> Generate
                            </button>
                        </form>
                    </div>

                    <div class="card-body pt-3">
                        @if($selectedReport === 'Balance Sheet')
                        @php
                            $totalCurrentAssets = collect($reportData['current_assets'])->sum('amount');
                            $totalNonCurrentAssets = collect($reportData['non_current_assets'])->sum('amount');
                            $totalAssetsSum = $totalCurrentAssets + $totalNonCurrentAssets;

                            $totalLiabilitiesSum = collect($reportData['liabilities'])->sum('amount');
                            $totalEquitySum = collect($reportData['equity'])->sum('amount');
                            $totalLiabEquitySum = $totalLiabilitiesSum + $totalEquitySum;
                        @endphp
                        <!-- 1. BALANCE SHEET -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-uppercase border-bottom pb-2" style="color: #D9251C;">Assets (Current & Non-Current)</h6>
                                <table class="table table-sm align-middle statement-table mb-3">
                                    <thead>
                                        <tr>
                                            <th>Current Assets Account</th>
                                            <th class="text-end">Balance (₱)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportData['current_assets'] as $ca)
                                            @if(!empty($ca['is_group']))
                                            <tr class="bg-light border-top border-bottom">
                                                <td class="fw-bold" style="color: #D9251C; padding-left: 10px;">
                                                    <i class="las la-layer-group me-1 fs-15"></i> {{ $ca['group_name'] }}
                                                    <span class="badge bg-white text-secondary border ms-1 fw-normal" style="font-size: 0.68rem;">Account Group</span>
                                                </td>
                                                <td class="text-end fw-bold" style="color: #0f172a;">₱{{ number_format($ca['amount'], 2) }}</td>
                                            </tr>
                                            @foreach($ca['accounts'] as $sub)
                                            <tr>
                                                <td class="ps-4 text-muted small" style="font-size: 0.82rem;">
                                                    <i class="las la-angle-right me-1 text-secondary opacity-50"></i> {{ $sub['code'] }} - {{ $sub['name'] }}
                                                </td>
                                                <td class="text-end text-muted small" style="font-size: 0.82rem;">₱{{ number_format($sub['amount'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td>{{ $ca['account'] }}</td>
                                                <td class="text-end fw-bold" style="color: #0f172a;">₱{{ number_format($ca['amount'], 2) }}</td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light border-top">
                                            <td class="fw-bold text-uppercase small" style="color: #475569;">Subtotal Current Assets</td>
                                            <td class="text-end fw-bold" style="color: #0f172a;">₱{{ number_format($totalCurrentAssets, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>

                                <table class="table table-sm align-middle statement-table mb-3">
                                    <thead>
                                        <tr>
                                            <th>Non-Current Assets Account</th>
                                            <th class="text-end">Balance (₱)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportData['non_current_assets'] as $nca)
                                            @if(!empty($nca['is_group']))
                                            <tr class="bg-light border-top border-bottom">
                                                <td class="fw-bold" style="color: #D9251C; padding-left: 10px;">
                                                    <i class="las la-layer-group me-1 fs-15"></i> {{ $nca['group_name'] }}
                                                    <span class="badge bg-white text-secondary border ms-1 fw-normal" style="font-size: 0.68rem;">Account Group</span>
                                                </td>
                                                <td class="text-end fw-bold" style="color: #0f172a;">₱{{ number_format($nca['amount'], 2) }}</td>
                                            </tr>
                                            @foreach($nca['accounts'] as $sub)
                                            <tr>
                                                <td class="ps-4 text-muted small" style="font-size: 0.82rem;">
                                                    <i class="las la-angle-right me-1 text-secondary opacity-50"></i> {{ $sub['code'] }} - {{ $sub['name'] }}
                                                </td>
                                                <td class="text-end text-muted small" style="font-size: 0.82rem;">₱{{ number_format($sub['amount'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td>{{ $nca['account'] }}</td>
                                                <td class="text-end fw-bold" style="color: #0f172a;">₱{{ number_format($nca['amount'], 2) }}</td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light border-top">
                                            <td class="fw-bold text-uppercase small" style="color: #475569;">Subtotal Non-Current Assets</td>
                                            <td class="text-end fw-bold" style="color: #0f172a;">₱{{ number_format($totalNonCurrentAssets, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>

                                <!-- Total Assets Summary Card -->
                                <div class="p-3 rounded d-flex justify-content-between align-items-center" style="background-color: rgba(217, 37, 28, 0.08); border: 1.5px solid rgba(217, 37, 28, 0.25); border-radius: 8px;">
                                    <div>
                                        <span class="fw-bold text-uppercase d-block" style="color: #D9251C; font-size: 0.88rem; letter-spacing: 0.5px;">TOTAL ASSETS</span>
                                        <span class="text-muted small">Current + Non-Current Assets</span>
                                    </div>
                                    <span class="fw-bold fs-16" style="color: #D9251C;">₱{{ number_format($totalAssetsSum, 2) }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="fw-bold text-uppercase border-bottom pb-2 text-dark">Liabilities & Equity</h6>
                                <table class="table table-sm align-middle statement-table mb-3">
                                    <thead>
                                        <tr>
                                            <th>Liabilities Account</th>
                                            <th class="text-end">Balance (₱)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportData['liabilities'] as $liab)
                                            @if(!empty($liab['is_group']))
                                            <tr class="bg-light border-top border-bottom">
                                                <td class="fw-bold text-danger" style="padding-left: 10px;">
                                                    <i class="las la-layer-group me-1 fs-15"></i> {{ $liab['group_name'] }}
                                                    <span class="badge bg-white text-secondary border ms-1 fw-normal" style="font-size: 0.68rem;">Account Group</span>
                                                </td>
                                                <td class="text-end fw-bold text-danger">₱{{ number_format($liab['amount'], 2) }}</td>
                                            </tr>
                                            @foreach($liab['accounts'] as $sub)
                                            <tr>
                                                <td class="ps-4 text-muted small" style="font-size: 0.82rem;">
                                                    <i class="las la-angle-right me-1 text-secondary opacity-50"></i> {{ $sub['code'] }} - {{ $sub['name'] }}
                                                </td>
                                                <td class="text-end text-muted small" style="font-size: 0.82rem;">₱{{ number_format($sub['amount'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td>{{ $liab['account'] }}</td>
                                                <td class="text-end fw-bold text-danger">₱{{ number_format($liab['amount'], 2) }}</td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light border-top">
                                            <td class="fw-bold text-uppercase small" style="color: #475569;">Total Liabilities</td>
                                            <td class="text-end fw-bold text-danger">₱{{ number_format($totalLiabilitiesSum, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>

                                <table class="table table-sm align-middle statement-table mb-3">
                                    <thead>
                                        <tr>
                                            <th>Equity Account</th>
                                            <th class="text-end">Balance (₱)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportData['equity'] as $eq)
                                            @if(!empty($eq['is_group']))
                                            <tr class="bg-light border-top border-bottom">
                                                <td class="fw-bold text-success" style="padding-left: 10px;">
                                                    <i class="las la-layer-group me-1 fs-15"></i> {{ $eq['group_name'] }}
                                                    <span class="badge bg-white text-secondary border ms-1 fw-normal" style="font-size: 0.68rem;">Account Group</span>
                                                </td>
                                                <td class="text-end fw-bold text-success">₱{{ number_format($eq['amount'], 2) }}</td>
                                            </tr>
                                            @foreach($eq['accounts'] as $sub)
                                            <tr>
                                                <td class="ps-4 text-muted small" style="font-size: 0.82rem;">
                                                    <i class="las la-angle-right me-1 text-secondary opacity-50"></i> {{ $sub['code'] }} - {{ $sub['name'] }}
                                                </td>
                                                <td class="text-end text-muted small" style="font-size: 0.82rem;">₱{{ number_format($sub['amount'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td>{{ $eq['account'] }}</td>
                                                <td class="text-end fw-bold text-success">₱{{ number_format($eq['amount'], 2) }}</td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light border-top">
                                            <td class="fw-bold text-uppercase small" style="color: #475569;">Total Equity</td>
                                            <td class="text-end fw-bold text-success">₱{{ number_format($totalEquitySum, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>

                                <!-- Total Liabilities & Equity Summary Card -->
                                <div class="p-3 rounded d-flex justify-content-between align-items-center" style="background-color: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 8px;">
                                    <div>
                                        <span class="fw-bold text-uppercase d-block" style="color: #0f172a; font-size: 0.88rem; letter-spacing: 0.5px;">TOTAL LIABILITIES & EQUITY</span>
                                        <span class="text-muted small">Total Obligations + Retained Equity</span>
                                    </div>
                                    <span class="fw-bold fs-16" style="color: #0f172a;">₱{{ number_format($totalLiabEquitySum, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        @elseif($selectedReport === 'Cash Flow')
                        <!-- CASH FLOW STATEMENT -->
                        <div class="row g-4">
                            <div class="col-md-8 mx-auto">
                                <div class="bg-white p-4 border rounded shadow-sm" style="border-color: #e2e8f0 !important; border-radius: 10px !important;">
                                    <h5 class="fw-bold text-center text-uppercase mb-4 pb-2 border-bottom text-dark" style="letter-spacing: 0.5px; color: #0f172a !important; border-bottom: 2px solid #f1f5f9 !important;">
                                        Statement of Cash Flows
                                    </h5>
                                    
                                    <!-- 1. OPERATING ACTIVITIES -->
                                    <h6 class="fw-bold text-uppercase pb-1 mb-2 border-bottom" style="color: #0f172a; font-size: 0.8rem; letter-spacing: 0.5px;">1. Cash Flows from Operating Activities</h6>
                                    <table class="table table-sm statement-table align-middle mb-3">
                                        <tbody>
                                            @php $netOps = 0; @endphp
                                            @foreach($reportData['operating'] as $op)
                                            @php $netOps += $op['amount']; @endphp
                                            <tr>
                                                <td class="ps-3" style="color: #475569 !important; font-weight: 500; font-size: 0.86rem;">{{ $op['category'] }}</td>
                                                <td class="text-end fw-bold fs-14 @if($op['amount'] < 0) text-danger @else text-dark @endif">
                                                    @if($op['amount'] < 0)
                                                    (₱{{ number_format(abs($op['amount']), 2) }})
                                                    @else
                                                    ₱{{ number_format($op['amount'], 2) }}
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            <tr style="background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                                                <td class="fw-bold text-uppercase fs-13" style="color: #475569 !important; padding: 10px 14px;">Net Cash from Operating Activities</td>
                                                <td class="text-end fw-bold fs-14" style="color: #0f172a !important; padding: 10px 14px;">
                                                    @if($netOps < 0)
                                                    (₱{{ number_format(abs($netOps), 2) }})
                                                    @else
                                                    ₱{{ number_format($netOps, 2) }}
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- 2. INVESTING ACTIVITIES -->
                                    <h6 class="fw-bold text-uppercase pb-1 mb-2 border-bottom" style="color: #0f172a; font-size: 0.8rem; letter-spacing: 0.5px;">2. Cash Flows from Investing Activities</h6>
                                    <table class="table table-sm statement-table align-middle mb-3">
                                        <tbody>
                                            @php $netInv = 0; @endphp
                                            @foreach($reportData['investing'] as $inv)
                                            @php $netInv += $inv['amount']; @endphp
                                            <tr>
                                                <td class="ps-3" style="color: #475569 !important; font-weight: 500; font-size: 0.86rem;">{{ $inv['category'] }}</td>
                                                <td class="text-end fw-bold fs-14 @if($inv['amount'] < 0) text-danger @else text-dark @endif">
                                                    @if($inv['amount'] < 0)
                                                    (₱{{ number_format(abs($inv['amount']), 2) }})
                                                    @else
                                                    ₱{{ number_format($inv['amount'], 2) }}
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            <tr style="background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                                                <td class="fw-bold text-uppercase fs-13" style="color: #475569 !important; padding: 10px 14px;">Net Cash from Investing Activities</td>
                                                <td class="text-end fw-bold fs-14" style="color: #0f172a !important; padding: 10px 14px;">
                                                    @if($netInv < 0)
                                                    (₱{{ number_format(abs($netInv), 2) }})
                                                    @else
                                                    ₱{{ number_format($netInv, 2) }}
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- 3. FINANCING ACTIVITIES -->
                                    <h6 class="fw-bold text-uppercase pb-1 mb-2 border-bottom" style="color: #0f172a; font-size: 0.8rem; letter-spacing: 0.5px;">3. Cash Flows from Financing Activities</h6>
                                    <table class="table table-sm statement-table align-middle mb-3">
                                        <tbody>
                                            @php $netFin = 0; @endphp
                                            @foreach($reportData['financing'] as $fin)
                                            @php $netFin += $fin['amount']; @endphp
                                            <tr>
                                                <td class="ps-3" style="color: #475569 !important; font-weight: 500; font-size: 0.86rem;">{{ $fin['category'] }}</td>
                                                <td class="text-end fw-bold fs-14 text-dark">₱{{ number_format($fin['amount'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                            <tr style="background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                                                <td class="fw-bold text-uppercase fs-13" style="color: #475569 !important; padding: 10px 14px;">Net Cash from Financing Activities</td>
                                                <td class="text-end fw-bold fs-14" style="color: #0f172a !important; padding: 10px 14px;">₱{{ number_format($netFin, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Summary / Cash Reconciliation -->
                                    <h6 class="fw-bold text-uppercase pb-1 mb-2 border-bottom pt-2" style="color: #0f172a; font-size: 0.8rem; letter-spacing: 0.5px;">Cash Reconciliation Summary</h6>
                                    <table class="table table-sm statement-table align-middle mb-4">
                                        <tbody>
                                            <tr>
                                                <td style="color: #475569 !important; font-weight: 500; font-size: 0.86rem;">Net Increase / (Decrease) in Cash</td>
                                                <td class="text-end fw-bold fs-14 @if($reportData['summary']['net_change'] < 0) text-danger @else text-success @endif">
                                                    @if($reportData['summary']['net_change'] < 0)
                                                    (₱{{ number_format(abs($reportData['summary']['net_change']), 2) }})
                                                    @else
                                                    ₱{{ number_format($reportData['summary']['net_change'], 2) }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #475569 !important; font-weight: 500; font-size: 0.86rem;">Cash at Beginning of Period</td>
                                                <td class="text-end fw-bold text-dark fs-14">₱{{ number_format($reportData['summary']['beginning'], 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Cash at End of Period Callout -->
                                    <div class="d-flex justify-content-between align-items-center p-3 rounded" style="background-color: #D9251C; box-shadow: 0 4px 6px -1px rgba(217, 37, 28, 0.15);">
                                         <span class="fw-bold text-uppercase fs-14 text-white" style="letter-spacing: 0.5px;">Cash at End of Period</span>
                                         <span class="fw-bold fs-18 text-white">₱{{ number_format($reportData['summary']['ending'], 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @elseif($selectedReport === 'Income Statement')
                        <!-- INCOME STATEMENT -->
                        <div class="row g-4">
                            <div class="col-md-8 mx-auto">
                                <div class="bg-white p-4 border rounded shadow-sm" style="border-color: #e2e8f0 !important; border-radius: 10px !important;">
                                    <h5 class="fw-bold text-center text-uppercase mb-4 pb-2 border-bottom text-dark" style="letter-spacing: 0.5px; color: #0f172a !important; border-bottom: 2px solid #f1f5f9 !important;">
                                        Income Statement
                                    </h5>
                                    
                                    <!-- 1. REVENUE -->
                                    <h6 class="fw-bold text-uppercase pb-1 mb-2 border-bottom" style="color: #0f172a; font-size: 0.8rem; letter-spacing: 0.5px;">1. Revenue</h6>
                                    <table class="table table-sm statement-table align-middle mb-4">
                                        <tbody>
                                            @php $totalRev = 0; @endphp
                                            @foreach($reportData['revenue'] as $rev)
                                            @php $totalRev += $rev['amount']; @endphp
                                            <tr>
                                                <td class="ps-3" style="color: #475569 !important; font-weight: 500; font-size: 0.86rem;">{{ $rev['category'] }}</td>
                                                <td class="text-end fw-bold" style="color: #0f172a !important; font-size: 0.86rem;">₱{{ number_format($rev['amount'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                            <tr style="background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                                                <td class="fw-bold text-uppercase fs-13" style="color: #475569 !important; padding: 10px 14px;">Total Revenue</td>
                                                <td class="text-end fw-bold fs-14" style="color: #0f172a !important; padding: 10px 14px;">₱{{ number_format($totalRev, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- 2. COGS -->
                                    <h6 class="fw-bold text-uppercase pb-1 mb-2 border-bottom" style="color: #0f172a; font-size: 0.8rem; letter-spacing: 0.5px;">2. Cost of Goods Sold (COGS)</h6>
                                    <table class="table table-sm statement-table align-middle mb-4">
                                        <tbody>
                                            @php $totalCogs = 0; @endphp
                                            @foreach($reportData['cogs'] as $cogs)
                                            @php $totalCogs += $cogs['amount']; @endphp
                                            <tr>
                                                <td class="ps-3" style="color: #475569 !important; font-weight: 500; font-size: 0.86rem;">{{ $cogs['category'] }}</td>
                                                <td class="text-end fw-bold text-danger" style="font-size: 0.86rem;">₱{{ number_format($cogs['amount'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                            <tr style="background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                                                <td class="fw-bold text-uppercase fs-13" style="color: #475569 !important; padding: 10px 14px;">Total Cost of Goods Sold</td>
                                                <td class="text-end fw-bold fs-14 text-danger" style="padding: 10px 14px;">₱{{ number_format($totalCogs, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Gross Profit Callout -->
                                    @php $grossProfit = $totalRev - $totalCogs; @endphp
                                    <div class="d-flex justify-content-between align-items-center p-3 mb-4 rounded" style="background-color: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.15);">
                                         <span class="fw-bold text-uppercase fs-13" style="color: #10b981; letter-spacing: 0.5px;">Gross Profit</span>
                                         <span class="fw-bold fs-16" style="color: #10b981;">₱{{ number_format($grossProfit, 2) }}</span>
                                    </div>

                                    <!-- 3. OPERATING EXPENSES -->
                                    <h6 class="fw-bold text-uppercase pb-1 mb-2 border-bottom" style="color: #0f172a; font-size: 0.8rem; letter-spacing: 0.5px;">3. Operating Expenses</h6>
                                    <table class="table table-sm statement-table align-middle mb-4">
                                        <tbody>
                                            @php $totalOpex = 0; @endphp
                                            @foreach($reportData['operating_expenses'] as $opex)
                                            @php $totalOpex += $opex['amount']; @endphp
                                            <tr>
                                                <td class="ps-3" style="color: #475569 !important; font-weight: 500; font-size: 0.86rem;">{{ $opex['category'] }}</td>
                                                <td class="text-end fw-bold text-danger" style="font-size: 0.86rem;">₱{{ number_format($opex['amount'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                            <tr style="background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                                                <td class="fw-bold text-uppercase fs-13" style="color: #475569 !important; padding: 10px 14px;">Total Operating Expenses</td>
                                                <td class="text-end fw-bold fs-14 text-danger" style="padding: 10px 14px;">₱{{ number_format($totalOpex, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Net Income Callout -->
                                    @php $netIncome = $grossProfit - $totalOpex; @endphp
                                    <div class="d-flex justify-content-between align-items-center p-3 rounded" style="background-color: #D9251C; box-shadow: 0 4px 6px -1px rgba(217, 37, 28, 0.15);">
                                         <span class="fw-bold text-uppercase fs-14 text-white" style="letter-spacing: 0.5px;">Net Income / (Loss)</span>
                                         <span class="fw-bold fs-18 text-white">₱{{ number_format($netIncome, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @elseif($selectedReport === 'Profit by Product')
                        <!-- 2. PROFIT BY PRODUCT -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Product / Book Title</th>
                                        <th class="text-center">Units Sold</th>
                                        <th class="text-end">Gross Revenue</th>
                                        <th class="text-end">COGS</th>
                                        <th class="text-end">Net Profit</th>
                                        <th class="text-center">Profit Margin %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $row)
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark">{{ $row['sku'] }}</span></td>
                                        <td><span class="fw-bold text-dark fs-14">{{ $row['name'] }}</span></td>
                                        <td class="text-center fw-bold text-dark">{{ number_format($row['sales_qty']) }}</td>
                                        <td class="text-end fw-bold text-success">₱{{ number_format($row['revenue'], 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($row['cogs'], 2) }}</td>
                                        <td class="text-end fw-bold" style="color: #D9251C;">
                                            ₱{{ number_format($row['profit'], 2) }}
                                        </td>
                                        <td class="text-center fw-bold text-dark">{{ $row['margin_pct'] }}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($reportData instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div id="paginationContainer" class="mt-4 d-flex justify-content-end pe-4">
                            {{ $reportData->onEachSide(0)->links('pagination::bootstrap-4') }}
                        </div>
                        @endif

                        @elseif($selectedReport === 'Profit by Sales Channel')
                        <!-- 3. PROFIT BY SALES CHANNEL -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Sales Channel</th>
                                        <th class="text-end">Revenue</th>
                                        <th class="text-end">Expenses</th>
                                        <th class="text-end">Net Profit</th>
                                        <th class="text-center">Operating Margin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $ch)
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark fs-14">{{ $ch['channel'] }}</span></td>
                                        <td class="text-end fw-bold text-success">₱{{ number_format($ch['revenue'], 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($ch['expenses'], 2) }}</td>
                                        <td class="text-end fw-bold" style="color: #D9251C;">
                                            ₱{{ number_format($ch['profit'], 2) }}
                                        </td>
                                        <td class="text-center fw-bold text-dark">{{ $ch['margin'] }}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @elseif($selectedReport === 'Profit by Customer')
                        <!-- 4. PROFIT BY CUSTOMER -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Customer / Account Name</th>
                                        <th>Customer Type</th>
                                        <th class="text-end">Customer Sales Revenue</th>
                                        <th class="text-end">Direct Cost</th>
                                        <th class="text-end">Net Profit Contribution</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $cust)
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark fs-14">{{ $cust['customer'] }}</span></td>
                                        <td><span class="badge bg-light text-dark border">{{ $cust['type'] }}</span></td>
                                        <td class="text-end fw-bold text-success">₱{{ number_format($cust['revenue'], 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($cust['cost'], 2) }}</td>
                                        <td class="text-end fw-bold" style="color: #D9251C;">
                                            ₱{{ number_format($cust['net_profit'], 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($reportData instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div id="paginationContainer" class="mt-4 d-flex justify-content-end pe-4">
                            {{ $reportData->onEachSide(0)->links('pagination::bootstrap-4') }}
                        </div>
                        @endif
                        @elseif($selectedReport === 'Sales Reports')
                        <!-- SALES REPORTS TABLE -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Order Number</th>
                                        <th>Customer / Payee</th>
                                        <th>Sales Channel</th>
                                        <th>Status</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $grandTotal = 0; @endphp
                                    @forelse($reportData as $sale)
                                    @php $grandTotal += $sale->effective_amount; @endphp
                                    <tr class="hover-row">
                                        <td><span class="text-dark">{{ \Carbon\Carbon::parse($sale->effective_date)->format('M d, Y g:i A') }}</span></td>
                                        <td><span class="fw-bold text-dark">{{ $sale->so_number }}</span></td>
                                        <td><span class="text-dark">{{ $sale->customer->customer_name ?? 'Walk-In Customer' }}</span></td>
                                        <td>
                                            @php
                                                $channelDisplay = 'Standard SO';
                                                if ($sale->type === 'calculator_pos') $channelDisplay = 'Direct POS';
                                                elseif ($sale->type === 'ecom_direct') $channelDisplay = 'E-Com Direct';
                                                elseif ($sale->type === 'area_sales_consignment') $channelDisplay = 'NBS Consignment';
                                            @endphp
                                            <span class="badge bg-light text-dark border">{{ $channelDisplay }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = 'bg-light text-secondary border';
                                                if ($sale->status === 'completed') $statusClass = 'bg-soft-success text-success';
                                                elseif ($sale->status === 'cancelled') $statusClass = 'bg-soft-danger text-danger';
                                            @endphp
                                            <span class="badge {{ $statusClass }}" @if($sale->status === 'completed') style="background-color: rgba(16, 185, 129, 0.1); color: #10b981;" @elseif($sale->status === 'cancelled') style="background-color: rgba(217, 37, 28, 0.1); color: #D9251C;" @endif>{{ strtoupper(str_replace('_', ' ', $sale->status)) }}</span>
                                        </td>
                                        <td class="text-end fw-bold text-dark">₱{{ number_format($sale->effective_amount, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No sales transactions found in the selected period.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                @if($reportData->isNotEmpty())
                                <tfoot>
                                    <tr class="fw-bold" style="background-color: #f8fafc; border-top: 2px solid #e2e8f0;">
                                        <td colspan="5" class="text-uppercase text-end" style="padding: 12px 16px; font-size: 0.84rem; color: #475569;">Total Sales (Period Total)</td>
                                        <td class="text-end" style="padding: 12px 16px; font-size: 0.84rem; color: #D9251C;">₱{{ number_format($totalSalesSum ?? $grandTotal, 2) }}</td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>

                        @if($reportData instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div id="paginationContainer" class="mt-4 d-flex justify-content-end pe-4">
                            {{ $reportData->onEachSide(0)->links('pagination::bootstrap-4') }}
                        </div>
                        @endif

                        @elseif($selectedReport === 'Expense Reports')
                        <!-- EXPENSE REPORTS TABLE -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Expense Title / Description</th>
                                        <th>Charged Department</th>
                                        <th>Added By</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $grandExpenseTotal = 0; @endphp
                                    @forelse($reportData as $exp)
                                    @php $grandExpenseTotal += $exp->amount; @endphp
                                    <tr class="hover-row">
                                        <td><span class="text-dark">{{ \Carbon\Carbon::parse($exp->expense_date)->format('M d, Y') }}</span></td>
                                        <td>
                                            <span class="fw-bold text-dark fs-14">{{ $exp->title }}</span>
                                            @if($exp->notes)
                                            <small class="text-muted d-block">{{ $exp->notes }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $exp->department->name ?? 'General Admin' }}</span>
                                        </td>
                                        <td><span class="text-dark">{{ $exp->added_by ?: 'System' }}</span></td>
                                        <td class="text-end fw-bold text-danger">₱{{ number_format($exp->amount, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No expense transactions found in the selected period.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                @if($reportData->isNotEmpty())
                                <tfoot>
                                    <tr class="fw-bold" style="background-color: #f8fafc; border-top: 2px solid #e2e8f0;">
                                        <td colspan="4" class="text-uppercase text-end" style="padding: 12px 16px; font-size: 0.84rem; color: #475569;">Total Expenses (Period Total)</td>
                                        <td class="text-end text-danger" style="padding: 12px 16px; font-size: 0.84rem;">₱{{ number_format($totalExpenseSum ?? $grandExpenseTotal, 2) }}</td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>

                        @if($reportData instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div id="paginationContainer" class="mt-4 d-flex justify-content-end pe-4">
                            {{ $reportData->onEachSide(0)->links('pagination::bootstrap-4') }}
                        </div>
                        @endif

                        @elseif($selectedReport === 'Profit by Salesperson')
                        <!-- 5. PROFIT BY SALESPERSON -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Sales Executive Name</th>
                                        <th>Assigned Sales Territory</th>
                                        <th class="text-end">Sales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $sp)
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark fs-14">{{ $sp['salesperson'] }}</span></td>
                                        <td><span class="badge bg-light text-dark border">{{ $sp['territory'] }}</span></td>
                                        <td class="text-end fw-bold text-success">₱{{ number_format($sp['achieved'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($reportData instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div id="paginationContainer" class="mt-4 d-flex justify-content-end pe-4">
                            {{ $reportData->onEachSide(0)->links('pagination::bootstrap-4') }}
                        </div>
                        @endif

                        @elseif($selectedReport === 'Trial Balance')
                        <!-- TRIAL BALANCE STATEMENT -->
                        <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                            <div>
                                <span class="fw-bold text-uppercase d-block" style="color: #0f172a; font-size: 0.85rem; letter-spacing: 0.5px;">Summary Trial Balance</span>
                                <span class="text-muted small">Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
                            </div>
                            <div>
                                @if($reportData['is_balanced'])
                                <span class="badge bg-success-subtle text-success border border-success px-3 py-2 fw-bold rounded-pill" style="font-size: 0.82rem;">
                                    <i class="las la-check-circle me-1"></i> BALANCED (₱{{ number_format($reportData['total_debits'], 2) }})
                                </span>
                                @else
                                <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 fw-bold rounded-pill" style="font-size: 0.82rem;">
                                    <i class="las la-exclamation-triangle me-1"></i> UNBALANCED DISCREPANCY (₱{{ number_format(abs($reportData['total_debits'] - $reportData['total_credits']), 2) }})
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle statement-table mb-0">
                                <thead>
                                    <tr style="background-color: #f8fafc;">
                                        <th style="width: 120px;">Code</th>
                                        <th>Account Description</th>
                                        <th style="width: 130px;">Account Type</th>
                                        <th class="text-end" style="width: 180px;">Debit (₱)</th>
                                        <th class="text-end" style="width: 180px;">Credit (₱)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData['accounts'] as $acc)
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-secondary">{{ $acc['code'] }}</span></td>
                                        <td>
                                            <span class="fw-bold text-dark fs-14">{{ $acc['name'] }}</span>
                                            @if($acc['category'])
                                            <small class="text-muted d-block">{{ $acc['category'] }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $acc['type'] }}</span>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            {{ $acc['debit'] > 0 ? '₱' . number_format($acc['debit'], 2) : '—' }}
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            {{ $acc['credit'] > 0 ? '₱' . number_format($acc['credit'], 2) : '—' }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No active chart of accounts found with transactions in this period.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold table-light border-top" style="border-top: 2px solid #cbd5e1 !important;">
                                        <td colspan="3" class="text-uppercase text-end" style="padding: 14px 16px; font-size: 0.88rem; color: #0f172a;">Grand Total Debits & Credits</td>
                                        <td class="text-end text-dark fs-15" style="padding: 14px 16px;">₱{{ number_format($reportData['total_debits'], 2) }}</td>
                                        <td class="text-end text-dark fs-15" style="padding: 14px 16px;">₱{{ number_format($reportData['total_credits'], 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if(isset($reportData['accounts']) && $reportData['accounts'] instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div id="paginationContainer" class="mt-4 d-flex justify-content-end pe-4">
                            {{ $reportData['accounts']->onEachSide(0)->links('pagination::bootstrap-4') }}
                        </div>
                        @endif

                        @elseif($selectedReport === 'General Ledger')
                        <!-- GENERAL LEDGER STATEMENT (ASSET, LIABILITY & EQUITY ACCOUNTS) -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="card shadow-sm h-100" style="border-radius: 10px; border: 1px solid #e2e8f0; background-color: #ffffff;">
                                    <div class="card-body p-3 d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: rgba(16, 185, 129, 0.08); color: #10b981; flex-shrink: 0;">
                                            <i class="las la-arrow-down fs-22"></i>
                                        </div>
                                        <div>
                                            <span class="small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 0.68rem; color: #475569 !important;">Total Debits (Period)</span>
                                            <h4 class="fw-bold mt-1 mb-0 text-success" style="letter-spacing: -0.5px; font-size: 1.15rem;">₱{{ number_format($reportData['total_debits'], 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card shadow-sm h-100" style="border-radius: 10px; border: 1px solid #e2e8f0; background-color: #ffffff;">
                                    <div class="card-body p-3 d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: rgba(217, 37, 28, 0.08); color: #D9251C; flex-shrink: 0;">
                                            <i class="las la-arrow-up fs-22"></i>
                                        </div>
                                        <div>
                                            <span class="small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 0.68rem; color: #475569 !important;">Total Credits (Period)</span>
                                            <h4 class="fw-bold mt-1 mb-0 text-danger" style="letter-spacing: -0.5px; font-size: 1.15rem;">₱{{ number_format($reportData['total_credits'], 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card shadow-sm h-100" style="border-radius: 10px; border: 1px solid #e2e8f0; background-color: #ffffff;">
                                    <div class="card-body p-3 d-flex align-items-center gap-3">
                                        @php
                                            $netGlActivity = $reportData['total_debits'] - $reportData['total_credits'];
                                            $isGlNetDebit = $netGlActivity >= 0;
                                        @endphp
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: rgba(59, 130, 246, 0.08); color: #3b82f6; flex-shrink: 0;">
                                            <i class="las la-balance-scale fs-22"></i>
                                        </div>
                                        <div>
                                            <span class="small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 0.68rem; color: #475569 !important;">Net Period Activity</span>
                                            <h4 class="fw-bold mt-1 mb-0 {{ $isGlNetDebit ? 'text-primary' : 'text-warning' }}" style="letter-spacing: -0.5px; font-size: 1.15rem;">
                                                ₱{{ number_format(abs($netGlActivity), 2) }} {{ $isGlNetDebit ? '(DR)' : '(CR)' }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- General Ledger Account Selector Filter Bar -->
                        <div class="p-3 mb-4 rounded border" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">
                            <form action="{{ route('admin-finance.financial-reports.index') }}" method="GET" class="row g-3 align-items-end">
                                <input type="hidden" name="report" value="General Ledger">
                                <input type="hidden" name="start_date" value="{{ $startDate }}">
                                <input type="hidden" name="end_date" value="{{ $endDate }}">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold text-uppercase small mb-1" style="font-size: 0.7rem; color: #475569; letter-spacing: 0.5px;">Select General Ledger Account (Asset, Liability & Equity)</label>
                                    <select name="account_id" class="form-select form-control-custom" onchange="this.form.submit()" style="height: 38px !important; border-color: #cbd5e1 !important; color: #000000 !important; font-size: 0.85rem !important;">
                                        @foreach($reportData['accounts']->groupBy('type') as $type => $group)
                                            <optgroup label="{{ strtoupper($type) }} ACCOUNTS">
                                                @foreach($group as $acc)
                                                    <option value="{{ $acc->id }}" {{ (request('account_id') == $acc->id || ($reportData['selected_account'] && $reportData['selected_account']->id == $acc->id)) ? 'selected' : '' }}>
                                                        {{ $acc->code }} - {{ $acc->name }} ({{ $acc->type }})
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn text-white fw-bold w-100 d-inline-flex align-items-center justify-content-center" style="background-color: #D9251C; border-color: #D9251C; height: 38px; border-radius: 6px; font-size: 0.85rem;">
                                        <i class="las la-filter me-1"></i> Filter Ledger
                                    </button>
                                    <button type="button" onclick="window.print()" class="btn btn-outline-secondary bg-white btn-print d-inline-flex align-items-center justify-content-center flex-shrink-0" title="Print General Ledger" style="height: 38px; width: 44px; border-color: #cbd5e1 !important; border-radius: 6px; color: #0f172a !important; box-shadow: none;">
                                        <i class="las la-print fs-20" style="color: #0f172a !important;"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- General Ledger Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle statement-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 130px;">Posting Date</th>
                                        <th style="width: 150px;">Voucher Ref</th>
                                        <th style="width: 180px;">Payee / Entity</th>
                                        <th>Memo / Description</th>
                                        <th class="text-end" style="width: 140px;">Debit (₱)</th>
                                        <th class="text-end" style="width: 140px;">Credit (₱)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData['items'] as $item)
                                    <tr class="hover-row">
                                        <td><span class="text-dark">{{ \Carbon\Carbon::parse($item->journalEntry->date ?? $item->created_at)->format('M d, Y') }}</span></td>
                                        <td>
                                            <span class="fw-bold" style="color: #D9251C;">
                                                #{{ $item->journalEntry->entry_no ?? ('JV-' . $item->journal_entry_id) }}
                                            </span>
                                        </td>
                                        <td><span class="text-dark fw-semibold">{{ $item->name ?: '—' }}</span></td>
                                        <td><span class="text-muted small">{{ $item->memo ?: ($item->journalEntry->memo ?? 'General Ledger Entry') }}</span></td>
                                        <td class="text-end fw-bold text-success">
                                            {{ $item->debit > 0 ? '₱' . number_format($item->debit, 2) : '—' }}
                                        </td>
                                        <td class="text-end fw-bold text-danger">
                                            {{ $item->credit > 0 ? '₱' . number_format($item->credit, 2) : '—' }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No general ledger postings found for the selected account in this period.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(isset($reportData['items']) && $reportData['items'] instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div id="paginationContainer" class="mt-4 d-flex justify-content-end pe-4">
                            {{ $reportData['items']->onEachSide(0)->links('pagination::bootstrap-4') }}
                        </div>
                        @endif

                        @elseif($selectedReport === 'Subsidiary Ledgers')
                        <!-- SUBSIDIARY LEDGER STATEMENT (INCOME & EXPENSE ACCOUNTS) -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="card shadow-sm h-100" style="border-radius: 10px; border: 1px solid #e2e8f0; background-color: #ffffff;">
                                    <div class="card-body p-3 d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: rgba(16, 185, 129, 0.08); color: #10b981; flex-shrink: 0;">
                                            <i class="las la-hand-holding-usd fs-22"></i>
                                        </div>
                                        <div>
                                            <span class="small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 0.68rem; color: #475569 !important;">Total Income Credits (Period)</span>
                                            <h4 class="fw-bold mt-1 mb-0" style="letter-spacing: -0.5px; color: #0f172a !important; font-size: 1.15rem;">₱{{ number_format($reportData['total_income_credits'], 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card shadow-sm h-100" style="border-radius: 10px; border: 1px solid #e2e8f0; background-color: #ffffff;">
                                    <div class="card-body p-3 d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: rgba(245, 158, 11, 0.08); color: #f59e0b; flex-shrink: 0;">
                                            <i class="las la-receipt fs-22"></i>
                                        </div>
                                        <div>
                                            <span class="small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 0.68rem; color: #475569 !important;">Total Expense Debits (Period)</span>
                                            <h4 class="fw-bold mt-1 mb-0" style="letter-spacing: -0.5px; color: #0f172a !important; font-size: 1.15rem;">₱{{ number_format($reportData['total_expense_debits'], 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card shadow-sm h-100" style="border-radius: 10px; border: 1px solid #e2e8f0; background-color: #ffffff;">
                                    <div class="card-body p-3 d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: rgba(217, 37, 28, 0.08); color: #D9251C; flex-shrink: 0;">
                                            <i class="las la-book-open fs-22"></i>
                                        </div>
                                        <div>
                                            <span class="small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 0.68rem; color: #475569 !important;">Active Selected Ledger</span>
                                            <h4 class="fw-bold mt-1 mb-0 text-truncate" style="letter-spacing: -0.5px; color: #0f172a !important; font-size: 1.05rem; max-width: 220px;" title="{{ $reportData['selected_account'] ? ($reportData['selected_account']->code . ' - ' . $reportData['selected_account']->name) : 'None' }}">
                                                {{ $reportData['selected_account'] ? ($reportData['selected_account']->code . ' - ' . $reportData['selected_account']->name) : 'N/A' }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Selector Filter Bar -->
                        <div class="p-3 mb-4 rounded border" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">
                            <form action="{{ route('admin-finance.financial-reports.index') }}" method="GET" class="row g-3 align-items-end">
                                <input type="hidden" name="report" value="Subsidiary Ledgers">
                                <input type="hidden" name="start_date" value="{{ $startDate }}">
                                <input type="hidden" name="end_date" value="{{ $endDate }}">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold text-uppercase small mb-1" style="font-size: 0.7rem; color: #475569; letter-spacing: 0.5px;">Target Subsidiary Account (Income & Expense Categories)</label>
                                    <select name="account_id" class="form-select form-control-custom" onchange="this.form.submit()" style="height: 38px !important; border-color: #cbd5e1 !important; color: #000000 !important; font-size: 0.85rem !important;">
                                        @foreach($reportData['accounts']->groupBy('type') as $type => $group)
                                            <optgroup label="{{ strtoupper($type) }} ACCOUNTS">
                                                @foreach($group as $acc)
                                                    <option value="{{ $acc->id }}" {{ (request('account_id') == $acc->id || ($reportData['selected_account'] && $reportData['selected_account']->id == $acc->id)) ? 'selected' : '' }}>
                                                        {{ $acc->code }} - {{ $acc->name }} ({{ $acc->type }})
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn text-white fw-bold w-100 d-inline-flex align-items-center justify-content-center" style="background-color: #D9251C; border-color: #D9251C; height: 38px; border-radius: 6px; font-size: 0.85rem;">
                                        <i class="las la-filter me-1"></i> Filter Ledger
                                    </button>
                                    <button type="button" onclick="window.print()" class="btn btn-outline-secondary bg-white btn-print d-inline-flex align-items-center justify-content-center flex-shrink-0" title="Print Subsidiary Ledger" style="height: 38px; width: 44px; border-color: #cbd5e1 !important; border-radius: 6px; color: #0f172a !important; box-shadow: none;">
                                        <i class="las la-print fs-20" style="color: #0f172a !important;"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle statement-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 130px;">Posting Date</th>
                                        <th style="width: 150px;">Voucher Ref</th>
                                        <th style="width: 180px;">Payee / Entity</th>
                                        <th>Memo / Description</th>
                                        <th class="text-end" style="width: 140px;">Debit (₱)</th>
                                        <th class="text-end" style="width: 140px;">Credit (₱)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData['items'] as $item)
                                    <tr class="hover-row">
                                        <td><span class="text-dark">{{ \Carbon\Carbon::parse($item->journalEntry->date ?? $item->created_at)->format('M d, Y') }}</span></td>
                                        <td>
                                            <span class="fw-bold" style="color: #D9251C;">
                                                #{{ $item->journalEntry->entry_no ?? ('JV-' . $item->journal_entry_id) }}
                                            </span>
                                        </td>
                                        <td><span class="text-dark fw-semibold">{{ $item->name ?: '—' }}</span></td>
                                        <td><span class="text-muted small">{{ $item->memo ?: ($item->journalEntry->memo ?? 'Subsidiary Ledger Entry') }}</span></td>
                                        <td class="text-end fw-bold text-danger">
                                            {{ $item->debit > 0 ? '₱' . number_format($item->debit, 2) : '—' }}
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            {{ $item->credit > 0 ? '₱' . number_format($item->credit, 2) : '—' }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No subsidiary ledger postings found for the selected account in this period.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(isset($reportData['items']) && $reportData['items'] instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div id="paginationContainer" class="mt-4 d-flex justify-content-end pe-4">
                            {{ $reportData['items']->onEachSide(0)->links('pagination::bootstrap-4') }}
                        </div>
                        @endif

                        @else
                        <!-- GENERIC FINANCIAL STATEMENT VIEW (P&L, Cash Flow, Trial Balance, GL, Subsidiary, Sales, Expenses, Department) -->
                        <div class="p-4 bg-light rounded border text-center my-3">
                            <i class="las la-check-circle fs-40 text-success mb-2"></i>
                            <h5 class="fw-bold text-dark">Automated {{ $selectedReport }} Statement</h5>
                            <p class="text-muted small mb-3">Live statement compiled from General Ledger & Chart of Accounts for {{ $startDate }} to {{ $endDate }}.</p>
                            <button class="btn btn-danger btn-sm text-white px-4 fw-bold" style="background-color: #D9251C; border-radius: 6px; height: 38px; border: none;" onclick="window.print()">
                                Print Full {{ $selectedReport }} Document
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
