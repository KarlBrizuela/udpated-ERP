<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .exe-header-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.75rem;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            border: 0;
            margin-bottom: 1.5rem;
        }

        .hero-kpi-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 1.5rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .hero-kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08) !important;
            border-color: #D9251C;
        }

        .hero-kpi-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
        }

        .exe-section-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 0;
            height: 100%;
        }

        .hover-row {
            transition: all 0.2s ease-in-out;
        }

        .hover-row:hover {
            background-color: #f8fafc !important;
        }
    </style>
    @endpush

    <div class="container-fluid">
        <!-- Master Title Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="exe-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fs-26 fw-bold text-dark mb-1" style="letter-spacing: -0.5px;">Executive Dashboard & Strategic Intelligence</h4>
                        <p class="text-muted small mb-0">High-level executive metrics, sales velocity, cash position, AR/AP risk control, inventory valuation, and budget performance.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-danger btn-sm px-3 text-white rounded shadow-sm d-flex align-items-center gap-2" style="background-color: #D9251C; border-color: #D9251C; height: 42px;" onclick="window.print()">
                            <i class="las la-print fs-18"></i> Print Executive Summary
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- 14 Hero Executive Large KPI Cards (Interactive & Drill-Down) -->
        <!-- 12 Hero Executive Large KPI Cards (Interactive & List Modals) -->
        <div class="row g-3 mb-4">
            <!-- 1. Today's Sales -->
            <div class="col-md-3" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#todaysSalesModal">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Today's Sales</span>
                        <div class="hero-kpi-icon bg-success-subtle text-success">
                            <i class="las la-chart-line"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">₱{{ number_format($kpis['todays_sales'], 2) }}</h3>
                    <span class="text-success small fw-bold"><i class="las la-arrow-up"></i> Click for list</span>
                </div>
            </div>

            <!-- 2. This Month's Revenue -->
            <div class="col-md-3" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#thisMonthsRevenueModal">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">This Month's Revenue</span>
                        <div class="hero-kpi-icon bg-primary-subtle text-primary">
                            <i class="las la-money-bill-wave"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-primary mb-1">₱{{ number_format($kpis['this_months_revenue'], 2) }}</h3>
                    <span class="text-muted small">MTD Revenue • Click for list</span>
                </div>
            </div>

            <!-- 3. Net Income -->
            <div class="col-md-3" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#netIncomeModal">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Net Income</span>
                        <div class="hero-kpi-icon bg-success-subtle text-success">
                            <i class="las la-piggy-bank"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-success mb-1">₱{{ number_format($kpis['net_income'], 2) }}</h3>
                    <span class="text-success small fw-bold"><i class="las la-check-circle"></i> View Profit & Loss</span>
                </div>
            </div>

            <!-- 4. Cash Position -->
            <div class="col-md-3" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#cashModal">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Cash</span>
                        <div class="hero-kpi-icon text-white" style="background-color: #D9251C;">
                            <i class="las la-wallet"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">₱{{ number_format($kpis['cash_position'], 2) }}</h3>
                    <span class="text-muted small">Bank Accounts • Click for list</span>
                </div>
            </div>

            <!-- 5. Outstanding Receivables -->
            <div class="col-md-3" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#outstandingArModal">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Outstanding AR</span>
                        <div class="hero-kpi-icon bg-warning-subtle text-warning">
                            <i class="las la-hand-holding-usd"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">₱{{ number_format($kpis['outstanding_receivables'], 2) }}</h3>
                    <span class="text-danger small fw-bold"><i class="las la-exclamation-triangle"></i> View AR Invoices</span>
                </div>
            </div>

            <!-- 6. Payables Due -->
            <div class="col-md-3" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#payablesDueModal">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Payables Due</span>
                        <div class="hero-kpi-icon bg-danger-subtle text-danger">
                            <i class="las la-file-invoice-dollar"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-danger mb-1">₱{{ number_format($kpis['payables_due'], 2) }}</h3>
                    <span class="text-muted small">Supplier Bills • Click for list</span>
                </div>
            </div>

            <!-- 7. Production Cost -->
            <div class="col-md-3" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#productionCostModal">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Production Cost</span>
                        <div class="hero-kpi-icon bg-info-subtle text-info">
                            <i class="las la-industry"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">₱{{ number_format($kpis['production_cost'], 2) }}</h3>
                    <span class="text-muted small">Job Costing • Click for list</span>
                </div>
            </div>

            <!-- 8. Inventory Value -->
            <div class="col-md-3" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#inventoryValueModal">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Inventory Value</span>
                        <div class="hero-kpi-icon bg-secondary-subtle text-secondary">
                            <i class="las la-boxes"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">₱{{ number_format($kpis['inventory_value'], 2) }}</h3>
                    <span class="text-muted small">Stock Valuation • Click for list</span>
                </div>
            </div>

            <!-- 9. Payroll This Month -->
            <div class="col-md-3" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#payrollModal">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Payroll This Month</span>
                        <div class="hero-kpi-icon bg-primary-subtle text-primary">
                            <i class="las la-users font-24"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">₱{{ number_format($kpis['payroll_this_month'], 2) }}</h3>
                    <span class="text-muted small">Salaries • Click for list</span>
                </div>
            </div>

            <!-- 10. Tax Due -->
            <div class="col-md-3" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#taxDueModal">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Tax Due</span>
                        <div class="hero-kpi-icon bg-dark-subtle text-dark">
                            <i class="las la-balance-scale"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">₱{{ number_format($kpis['tax_due'], 2) }}</h3>
                    <span class="text-muted small">BIR Withholding • Click for list</span>
                </div>
            </div>

            <!-- 11. Donation Income -->
            <div class="col-md-3" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#donationIncomeModal">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Donation Income</span>
                        <div class="hero-kpi-icon bg-success-subtle text-success">
                            <i class="las la-hand-holding-heart"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-success mb-1">₱{{ number_format($kpis['donation_income'], 2) }}</h3>
                    <span class="text-muted small">Donors • Click for list</span>
                </div>
            </div>

            <!-- 12. Forecasted Cash -->
            <div class="col-md-3" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#forecastedCashModal">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Forecasted Cash (30D)</span>
                        <div class="hero-kpi-icon bg-danger-subtle text-danger">
                            <i class="las la-chart-line"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: #D9251C;">₱{{ number_format($kpis['forecasted_cash'], 2) }}</h3>
                    <span class="text-muted small">30D Forecast • Click for list</span>
                </div>
            </div>
        </div>

        <!-- Strategic Rankings & Intelligence Lists (Books & Customers) -->
        <div class="row g-4 mb-4">
            <!-- 1. Top Selling Books -->
            <div class="col-md-3">
                <div class="exe-section-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="las la-trophy text-warning me-1"></i>Top Selling Books</h6>
                        <span class="badge bg-success-subtle text-success">Best Sellers</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Book Title</th>
                                    <th class="text-end">Sales (₱)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topSellingBooks as $bk)
                                <tr class="hover-row">
                                    <td>
                                        <span class="fw-bold text-dark small d-block">{{ $bk['name'] }}</span>
                                        <span class="text-muted small">{{ $bk['units_sold'] }} unit(s) sold</span>
                                    </td>
                                    <td class="text-end fw-bold text-success small">₱{{ number_format($bk['revenue'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 2. Worst Selling Books -->
            <div class="col-md-3">
                <div class="exe-section-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="las la-exclamation-circle text-danger me-1"></i>Worst Selling Books</h6>
                        <span class="badge bg-danger-subtle text-danger">Low Velocity</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Book Title</th>
                                    <th class="text-end">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($worstSellingBooks as $wb)
                                <tr class="hover-row">
                                    <td><span class="fw-bold text-dark small d-block">{{ $wb['name'] }}</span></td>
                                    <td class="text-end text-muted small">{{ number_format($wb['stock_remaining']) }} pcs</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. Best Customers -->
            <div class="col-md-3">
                <div class="exe-section-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="las la-star text-primary me-1"></i>Best Customers</h6>
                        <span class="badge bg-primary-subtle text-primary">Top Buyers</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Customer</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bestCustomers as $cust)
                                <tr class="hover-row">
                                    <td><span class="fw-bold text-dark small d-block">{{ $cust['name'] }}</span></td>
                                    <td class="text-end fw-bold text-primary small">₱{{ number_format($cust['revenue'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 4. Most Overdue Customers -->
            <div class="col-md-3">
                <div class="exe-section-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="las la-user-clock text-danger me-1"></i>Most Overdue AR</h6>
                        <span class="badge bg-danger-subtle text-danger">Credit Risk</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Customer</th>
                                    <th class="text-end">Overdue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mostOverdueCustomers as $ov)
                                <tr class="hover-row">
                                    <td>
                                        <span class="fw-bold text-dark small d-block">{{ $ov['name'] }}</span>
                                        <span class="text-danger small">{{ $ov['days_overdue'] }} days past due</span>
                                    </td>
                                    <td class="text-end fw-bold text-danger small">₱{{ number_format($ov['amount'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 1. Today's Sales Modal -->
    <div class="modal fade" id="todaysSalesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #D9251C;">
                    <h5 class="modal-title fw-bold"><i class="las la-chart-line me-2"></i>Today's Realized Sales List</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Real-time transactions collected today</span>
                        <h4 class="fw-bold text-success mb-0">Total: ₱{{ number_format($kpis['todays_sales'], 2) }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="todaysSalesTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>SO / Invoice #</th>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Payment Method</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todaysSalesOrdersList as $so)
                                <tr>
                                    <td><span class="fw-bold font-monospace text-dark">{{ $so->so_number }}</span></td>
                                    <td>{{ $so->customer_name ?? ($so->customer->customer_name ?? ($so->customer->company_name ?? 'Walk-in Customer')) }}</td>
                                    <td><span class="badge bg-light text-dark border text-uppercase">{{ $so->type }}</span></td>
                                    <td class="text-capitalize">{{ $so->payment_method ?: 'Cash' }}</td>
                                    <td class="text-end fw-bold text-success">₱{{ number_format($so->total_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No sales order transactions recorded today yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between align-items-center py-2" id="todaysSalesPagination"></div>
            </div>
        </div>
    </div>

    <!-- 2. This Month's Revenue Modal -->
    <div class="modal fade" id="thisMonthsRevenueModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #D9251C;">
                    <h5 class="modal-title fw-bold"><i class="las la-money-bill-wave me-2"></i>This Month's Revenue Breakdown</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Month-To-Date collected and realized revenue</span>
                        <h4 class="fw-bold text-primary mb-0">Total: ₱{{ number_format($kpis['this_months_revenue'], 2) }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="thisMonthsRevenueTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>SO #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($thisMonthsRevenueOrdersList as $ro)
                                <tr>
                                    <td><span class="fw-bold font-monospace text-dark">{{ $ro->so_number }}</span></td>
                                    <td>{{ $ro->customer_name ?? ($ro->customer->customer_name ?? ($ro->customer->company_name ?? 'Client Account')) }}</td>
                                    <td class="small text-muted">{{ $ro->created_at->format('M d, Y') }}</td>
                                    <td><span class="badge bg-success-subtle text-success text-capitalize">{{ $ro->status }}</span></td>
                                    <td class="text-end fw-bold text-primary">₱{{ number_format($ro->total_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No monthly sales revenue records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between align-items-center py-2" id="thisMonthsRevenuePagination"></div>
            </div>
        </div>
    </div>

    <!-- 3. Net Income Modal -->
    <div class="modal fade" id="netIncomeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #D9251C;">
                    <h5 class="modal-title fw-bold"><i class="las la-piggy-bank me-2"></i>Net Income & Profitability Summary</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="card bg-light border-0 p-3 mb-3" style="border-radius: 10px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted fw-bold">Gross Revenue (MTD)</span>
                            <span class="fw-bold text-primary fs-16">+ ₱{{ number_format($kpis['this_months_revenue'], 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted fw-bold">Total Operating Outflows & Expenses</span>
                            <span class="fw-bold text-danger fs-16">- ₱{{ number_format($kpis['total_expenses'] ?? 0, 2) }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark fs-15">Net Operating Income</span>
                            <span class="fw-bold text-success fs-18">₱{{ number_format($kpis['net_income'], 2) }}</span>
                        </div>
                    </div>
                    <p class="text-muted small text-center mb-0"><i class="las la-info-circle me-1"></i> Net Operating Margin calculated dynamically from revenue realizations less operational disbursements.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Cash Modal -->
    <div class="modal fade" id="cashModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #D9251C;">
                    <h5 class="modal-title fw-bold"><i class="las la-wallet me-2"></i>Cash Receipts & Collections Breakdown</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Liquid cash inflows and realized cash sales</span>
                        <h4 class="fw-bold text-dark mb-0">Total Cash: ₱{{ number_format($kpis['cash_position'], 2) }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="cashTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Ref #</th>
                                    <th>Customer / Source Description</th>
                                    <th>Category</th>
                                    <th>Payment Channel</th>
                                    <th class="text-end">Cash Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cashAccountsList as $acc)
                                <tr>
                                    <td><span class="fw-bold font-monospace text-dark">{{ $acc->account_code }}</span></td>
                                    <td>{{ $acc->bank_name }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $acc->account_name }}</span></td>
                                    <td class="text-muted small">{{ $acc->account_number }}</td>
                                    <td class="text-end fw-bold text-success">₱{{ number_format($acc->current_balance, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No liquid cash receipts recorded.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between align-items-center py-2" id="cashPagination"></div>
            </div>
        </div>
    </div>

    <!-- 5. Outstanding AR Modal -->
    <div class="modal fade" id="outstandingArModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #D9251C;">
                    <h5 class="modal-title fw-bold"><i class="las la-hand-holding-usd me-2"></i>Outstanding Accounts Receivable</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Pending customer invoices and receivables</span>
                        <h4 class="fw-bold text-warning mb-0">Total: ₱{{ number_format($kpis['outstanding_receivables'], 2) }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="outstandingArTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Customer / Client</th>
                                    <th>Status</th>
                                    <th class="text-end">Invoice Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($outstandingARList as $inv)
                                <tr>
                                    <td><span class="fw-bold font-monospace text-dark">{{ $inv->invoice_number ?? $inv->so_number ?? 'INV-REG' }}</span></td>
                                    <td>{{ $inv->customer_name ?? 'Client Account' }}</td>
                                    <td><span class="badge bg-warning text-dark px-2 py-1 rounded-pill">{{ $inv->status ?? 'Unpaid' }}</span></td>
                                    <td class="text-end fw-bold text-warning">₱{{ number_format($inv->total_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No uncollected sales invoices recorded.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between align-items-center py-2" id="outstandingArPagination"></div>
            </div>
        </div>
    </div>

    <!-- 6. Payables Due Modal -->
    <div class="modal fade" id="payablesDueModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #D9251C;">
                    <h5 class="modal-title fw-bold"><i class="las la-file-invoice-dollar me-2"></i>Accounts Payable & Supplier Liabilities</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Pending supplier bills and payables due</span>
                        <h4 class="fw-bold text-danger mb-0">Total: ₱{{ number_format($kpis['payables_due'], 2) }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="payablesDueTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Supplier Invoice #</th>
                                    <th>Supplier</th>
                                    <th>Due Date</th>
                                    <th class="text-end">Balance Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payablesDueList as $py)
                                <tr>
                                    <td><span class="fw-bold font-monospace text-dark">{{ $py->invoice_number }}</span></td>
                                    <td>{{ $py->supplier_name ?? 'Supplier Vendor' }}</td>
                                    <td class="small text-muted">{{ $py->due_date ?? 'N/A' }}</td>
                                    <td class="text-end fw-bold text-danger">₱{{ number_format($py->total_amount - ($py->amount_paid ?? 0), 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No pending supplier payables due.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between align-items-center py-2" id="payablesDuePagination"></div>
            </div>
        </div>
    </div>

    <!-- 7. Production Cost Modal -->
    <div class="modal fade" id="productionCostModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #D9251C;">
                    <h5 class="modal-title fw-bold"><i class="las la-industry me-2"></i>Production Costing Jobs Breakdown</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Printing and publication job costing summaries</span>
                        <h4 class="fw-bold text-dark mb-0">Total COGS: ₱{{ number_format($kpis['production_cost'], 2) }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="productionCostTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Job #</th>
                                    <th>Publication Title</th>
                                    <th>Copies</th>
                                    <th class="text-end">Total Job COGS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productionCostList as $pc)
                                <tr>
                                    <td><span class="fw-bold font-monospace text-dark">JOB-{{ $pc->id }}</span></td>
                                    <td><span class="fw-bold text-dark">{{ $pc->publication_title ?: 'Production Order' }}</span></td>
                                    <td><span class="badge bg-light text-dark border">{{ number_format($pc->copies ?? 0) }} pcs</span></td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($pc->total_cogs ?? 0, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No production job costings calculated yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between align-items-center py-2" id="productionCostPagination"></div>
            </div>
        </div>
    </div>

    <!-- 8. Inventory Value Modal -->
    <div class="modal fade" id="inventoryValueModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #D9251C;">
                    <h5 class="modal-title fw-bold"><i class="las la-boxes me-2"></i>Inventory Stock Valuation Breakdown</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Warehouse book stock & inventory valuation</span>
                        <h4 class="fw-bold text-dark mb-0">Valuation: ₱{{ number_format($kpis['inventory_value'], 2) }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="inventoryValueTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Book Title</th>
                                    <th>Unit Price</th>
                                    <th>Quantity in Stock</th>
                                    <th class="text-end">Total Valuation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventoryValueList as $iv)
                                <tr>
                                    <td><span class="fw-bold text-dark">{{ $iv->book_name }}</span></td>
                                    <td>₱{{ number_format($iv->price, 2) }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ number_format($iv->quantity) }} pcs</span></td>
                                    <td class="text-end fw-bold text-danger">₱{{ number_format($iv->total_val, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No warehouse inventory records logged.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between align-items-center py-2" id="inventoryValuePagination"></div>
            </div>
        </div>
    </div>

    <!-- 9. Payroll Modal -->
    <div class="modal fade" id="payrollModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #D9251C;">
                    <h5 class="modal-title fw-bold"><i class="las la-users me-2"></i>Payroll & Salary Disbursements</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Monthly personnel salary transactions</span>
                        <h4 class="fw-bold text-primary mb-0">Total: ₱{{ number_format($kpis['payroll_this_month'], 2) }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="payrollTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Ref #</th>
                                    <th>Transaction Description</th>
                                    <th>Date</th>
                                    <th class="text-end">Disbursement Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payrollList as $pr)
                                <tr>
                                    <td><span class="fw-bold font-monospace text-dark">PR-{{ $pr->id }}</span></td>
                                    <td>{{ $pr->description ?: 'Personnel Salary Disbursement' }}</td>
                                    <td class="small text-muted">{{ $pr->transaction_date }}</td>
                                    <td class="text-end fw-bold text-primary">₱{{ number_format($pr->amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No payroll transactions disbursed this month.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between align-items-center py-2" id="payrollPagination"></div>
            </div>
        </div>
    </div>

    <!-- 10. Tax Due Modal -->
    <div class="modal fade" id="taxDueModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #D9251C;">
                    <h5 class="modal-title fw-bold"><i class="las la-balance-scale me-2"></i>BIR Tax Obligations & Remittances</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">BIR Withholding & statutory tax dues</span>
                        <h4 class="fw-bold text-dark mb-0">Tax Due: ₱{{ number_format($kpis['tax_due'], 2) }}</h4>
                    </div>
                    <p class="text-muted small text-center mb-0">Statutory BIR withholding taxes calculated from active supplier invoice remittances and corporate tax filings.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 11. Donation Income Modal -->
    <div class="modal fade" id="donationIncomeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #D9251C;">
                    <h5 class="modal-title fw-bold"><i class="las la-hand-holding-heart me-2"></i>Donation & Restricted Fund Income</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Donor contributions and grants</span>
                        <h4 class="fw-bold text-success mb-0">Total: ₱{{ number_format($kpis['donation_income'], 2) }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="donationIncomeTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Donor Name</th>
                                    <th>Donation Type</th>
                                    <th>Date Received</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($donationIncomeList as $dn)
                                <tr>
                                    <td><span class="fw-bold text-dark">{{ $dn->donor_name ?? 'Benefactor / Anonymous' }}</span></td>
                                    <td><span class="badge bg-light text-dark border">{{ $dn->type ?? 'Cash Donation' }}</span></td>
                                    <td class="small text-muted">{{ $dn->created_at ? $dn->created_at->format('M d, Y') : 'N/A' }}</td>
                                    <td class="text-end fw-bold text-success">₱{{ number_format($dn->amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No donor contributions recorded yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between align-items-center py-2" id="donationIncomePagination"></div>
            </div>
        </div>
    </div>

    <!-- 12. Forecasted Cash Modal -->
    <div class="modal fade" id="forecastedCashModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #D9251C;">
                    <h5 class="modal-title fw-bold"><i class="las la-chart-line me-2"></i>30-Day Liquidity Cashflow Forecast</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="card bg-light border-0 p-3 mb-3" style="border-radius: 10px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted fw-bold">Current Liquid Cash</span>
                            <span class="fw-bold text-dark fs-15">₱{{ number_format($kpis['cash_position'], 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted fw-bold">(+) Expected AR Collections</span>
                            <span class="fw-bold text-success fs-15">+ ₱{{ number_format($kpis['outstanding_receivables'], 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted fw-bold">(-) Pending AP Obligations</span>
                            <span class="fw-bold text-danger fs-15">- ₱{{ number_format($kpis['payables_due'], 2) }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark fs-15">Forecasted 30D Position</span>
                            <span class="fw-bold fs-18" style="color: #D9251C;">₱{{ number_format($kpis['forecasted_cash'], 2) }}</span>
                        </div>
                    </div>
                    <p class="text-muted small text-center mb-0"><i class="las la-shield-alt me-1"></i> Projected treasury balance calculated from liquid reserves + 30-day projected net inflow.</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function renderModalPagination(tableId, containerId, itemsPerPage = 5) {
            const table = document.getElementById(tableId);
            if (!table) return;
            const tbody = table.querySelector('tbody');
            if (!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('tr')).filter(r => !r.querySelector('td[colspan]'));
            const totalItems = rows.length;
            const container = document.getElementById(containerId);
            if (!container) return;

            if (totalItems === 0) {
                container.innerHTML = '<span class="text-muted small">No entries found</span>';
                return;
            }

            let currentPage = 1;
            const totalPages = Math.ceil(totalItems / itemsPerPage);

            function showPage(page) {
                currentPage = page;
                const start = (page - 1) * itemsPerPage;
                const end = start + itemsPerPage;

                rows.forEach((row, idx) => {
                    row.style.display = (idx >= start && idx < end) ? '' : 'none';
                });

                const startItem = start + 1;
                const endItem = Math.min(end, totalItems);

                let html = `<span class="text-muted small">Showing <strong>${startItem}-${endItem}</strong> of <strong>${totalItems}</strong> entries</span>`;
                
                if (totalPages > 1) {
                    html += `<div class="btn-group btn-group-sm mb-0">`;
                    html += `<button type="button" class="btn btn-outline-secondary py-1 px-2 ${currentPage === 1 ? 'disabled' : ''}" onclick="window['page_${tableId}'](${currentPage - 1})"><i class="las la-angle-left"></i> Prev</button>`;
                    
                    for (let p = 1; p <= totalPages; p++) {
                        const activeClass = p === currentPage ? 'btn-danger text-white fw-bold' : 'btn-outline-secondary';
                        html += `<button type="button" class="btn ${activeClass} py-1 px-2" onclick="window['page_${tableId}'](${p})">${p}</button>`;
                    }
                    
                    html += `<button type="button" class="btn btn-outline-secondary py-1 px-2 ${currentPage === totalPages ? 'disabled' : ''}" onclick="window['page_${tableId}'](${currentPage + 1})">Next <i class="las la-angle-right"></i></button>`;
                    html += `</div>`;
                }
                container.innerHTML = html;
            }

            window['page_' + tableId] = function(page) {
                if (page >= 1 && page <= totalPages) {
                    showPage(page);
                }
            };

            showPage(1);
        }

        document.addEventListener('DOMContentLoaded', function() {
            renderModalPagination('todaysSalesTable', 'todaysSalesPagination', 5);
            renderModalPagination('thisMonthsRevenueTable', 'thisMonthsRevenuePagination', 5);
            renderModalPagination('cashTable', 'cashPagination', 5);
            renderModalPagination('outstandingArTable', 'outstandingArPagination', 5);
            renderModalPagination('payablesDueTable', 'payablesDuePagination', 5);
            renderModalPagination('productionCostTable', 'productionCostPagination', 5);
            renderModalPagination('inventoryValueTable', 'inventoryValuePagination', 5);
            renderModalPagination('payrollTable', 'payrollPagination', 5);
            renderModalPagination('donationIncomeTable', 'donationIncomePagination', 5);
        });
    </script>
    @endpush
</x-app-layout>
