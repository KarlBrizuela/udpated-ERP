<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
        <style>
            /* Financial Overview chart container - fill available space */
            #financialOverviewChart {
                flex: 1;
                min-height: 400px !important;
                height: 100% !important;
            }

            /* Ensure chart container fills space */
            .row>.col-xl-8>.card .card-body {
                flex: 1;
                min-height: 450px;
                display: flex;
                flex-direction: column;
            }

            /* Time Period Selector Styling */
            .period-btn {
                border-radius: 0;
                font-weight: 500;
                padding: 0.5rem 1.5rem;
                transition: all 0.3s ease;
            }

            .period-btn:first-child {
                border-top-left-radius: 0.375rem;
                border-bottom-left-radius: 0.375rem;
            }

            .period-btn:last-child {
                border-top-right-radius: 0.375rem;
                border-bottom-right-radius: 0.375rem;
            }

            .period-btn.active {
                background-color: #dc3545 !important;
                border-color: #dc3545 !important;
                color: white !important;
                outline: none !important;
                box-shadow: none !important;
            }

            .period-btn.active:hover {
                color: white !important;
            }

            .period-btn:hover:not(.active) {
                background-color: #ffe7e7;
                border-color: #dc3545;
                color: #dc3545;
            }

            /* Dashboard stat cards - cleaner look */
            .card-bd .card-body {
                padding: 1.5rem;
            }

            .num-text {
                font-size: 1.75rem;
                line-height: 1.2;
                margin-bottom: 0.25rem;
            }

            .fs-14 {
                font-size: 0.875rem;
                font-weight: 500;
            }

            .text-muted {
                font-size: 0.75rem;
                margin-top: 0.25rem;
            }
        </style>
    @endpush

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-black font-w600">View Financial Statistics</h5>
                        <div class="btn-group" role="group" aria-label="Time period selector">
                            <button type="button" class="btn btn-outline-danger period-btn {{ ($period ?? 'monthly') === 'daily' ? 'active' : '' }}"
                                data-period="daily">Daily</button>
                            <button type="button" class="btn btn-outline-danger period-btn {{ ($period ?? 'monthly') === 'weekly' ? 'active' : '' }}"
                                data-period="weekly">Weekly</button>
                            <button type="button" class="btn btn-outline-danger period-btn {{ ($period ?? 'monthly') === 'monthly' ? 'active' : '' }}"
                                data-period="monthly">Monthly</button>
                            <button type="button" class="btn btn-outline-danger period-btn {{ ($period ?? 'monthly') === 'yearly' ? 'active' : '' }}"
                                data-period="yearly">Yearly</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function(){
            document.querySelectorAll('.period-btn').forEach(btn => {
                btn.addEventListener('click', function(){
                    const p = this.dataset.period;
                    const url = new URL(window.location.href);
                    url.searchParams.set('period', p);
                    window.location.href = url.toString();
                });
            });
        })();
    </script>
    @endpush

    <div class="row">
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card card-bd">
                <div class="bg-primary card-border"></div>
                <div class="card-body box-style">
                    <div class="media align-items-center">
                        <div class="media-body me-3">
                            <h2 class="num-text text-black font-w700" id="totalRevenueAmount">₱ {{ number_format($totalRevenue ?? 0, 2) }}</h2>
                            <span class="fs-14">Total Revenue</span>
                            <small class="text-muted d-block" id="revenuePeriodLabel">{{ $periodLabel ?? 'This Month' }}</small>
                        </div>
                        <i class="las la-peso-sign" style="font-size: 2rem; color: #ff0000;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card card-bd">
                <div class="bg-success card-border"></div>
                <div class="card-body box-style">
                    <div class="media align-items-center">
                        <div class="media-body me-3">
                            <h2 class="num-text text-black font-w700" id="accountsReceivableAmount">₱ {{ number_format($accountsReceivable ?? 0, 2) }}</h2>
                            <span class="fs-14">Accounts Receivable</span>
                            <small class="text-muted d-block" id="arPeriodLabel">{{ $periodLabel ?? 'This Month' }}</small>
                        </div>
                        <i class="las la-money-bill-wave" style="font-size: 2rem; color: #68CF29;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card card-bd">
                <div class="bg-warning card-border"></div>
                <div class="card-body box-style">
                    <div class="media align-items-center">
                        <div class="media-body me-3">
                            <h2 class="num-text text-black font-w700" id="netProfitAmount">₱ {{ number_format($netProfit ?? 0, 2) }}</h2>
                            <span class="fs-14">Net Profit</span>
                            <small class="text-muted d-block" id="profitPeriodLabel">{{ $periodLabel ?? 'This Month' }}</small>
                        </div>
                        <i class="las la-chart-line" style="font-size: 2rem; color: #FFAC30;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card card-bd">
                <div class="bg-info card-border"></div>
                <div class="card-body box-style">
                    <div class="media align-items-center">
                        <div class="media-body me-3">
                            <h2 class="num-text text-black font-w700" id="totalExpensesAmount">₱ {{ number_format($totalExpenses ?? 0, 2) }}</h2>
                            <span class="fs-14">Total Expenses</span>
                            <small class="text-muted d-block" id="expensesPeriodLabel">{{ $periodLabel ?? 'This Month' }}</small>
                        </div>
                        <i class="las la-file-invoice-dollar" style="font-size: 2rem; color: #51A6F5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-xxl-8">
            <div class="card" style="min-height: 500px; display: flex; flex-direction: column;">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">Financial Overview</h4>
                </div>
                <div class="card-body"
                    style="flex: 1; min-height: 450px; display: flex; flex-direction: column; padding: 1.5rem;">
                    <div id="financialOverviewChart" style="flex: 1; min-height: 400px;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-xxl-4">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">Position Summary</h4>
                </div>
                <div class="card-body" style="flex: 1;">
                    @php
                        $colors = ['bg-primary', 'bg-success', 'bg-warning', 'bg-info', 'bg-danger', 'bg-secondary'];
                        $colorIndex = 0;
                    @endphp
                    @forelse($positionSummary ?? [] as $position)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-black font-w600">{{ $position['name'] }}</span>
                                <span class="text-black font-w600">{{ $position['count'] }} users</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar {{ $colors[$colorIndex++ % count($colors)] }}" role="progressbar"
                                    style="width: {{ $position['percentage'] }}%"
                                    aria-valuenow="{{ $position['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted">No position data available in this division.</div>
                    @endforelse

                    @if(isset($positionSummary) && $positionSummary->count() > 0)
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between">
                                <span class="text-black font-w600 font-w700">Total Users</span>
                                <span class="text-black font-w600 font-w700">{{ $totalDivisionUsers ?? 0 }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h4 class="fs-20 mb-0 text-black">Pending Approvals</h4>
                        <a href="{{ route('admin-finance.approval-queue') }}" class="btn btn-primary btn-sm">View
                            All</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th><strong>Request Type</strong></th>
                                    <th><strong>Requested By</strong></th>
                                    <th><strong>Department</strong></th>
                                    <th><strong>Amount</strong></th>
                                    <th><strong>Date</strong></th>
                                    <th><strong>Status</strong></th>
                                    <th><strong>Action</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingApprovals ?? [] as $p)
                                <tr>
                                    <td><span class="badge light badge-primary">{{ $p['type'] }}</span></td>
                                    <td>{{ $p['submitted_by'] }}</td>
                                    <td>{{ $p['department'] }}</td>
                                    <td>
                                        @if(is_numeric($p['amount'] ?? null))
                                            PhP {{ number_format((float) $p['amount'], 2) }}
                                        @else
                                            {{ $p['amount'] ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($p['date'])->format('Y-m-d') }}</td>
                                    @php
                                        $rawStatus = $p['status'] ?? '';
                                        $statusLabel = $rawStatus ? ucfirst(strtolower(str_replace('_', ' ', $rawStatus))) : '';
                                        $focusKey = ($p['type'] ?? 'item') . '_' . ($p['id'] ?? '');
                                    @endphp
                                    <td><span class="badge light badge-warning">{{ $statusLabel }}</span></td>
                                    <td>
                                        <a href="{{ route('admin-finance.approval-queue') }}?focus={{ urlencode($focusKey) }}" class="btn btn-primary btn-sm">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No pending approvals.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('vendor/chart.js/Chart.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/apexchart/apexchart.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                if (window.ApexCharts) {
                    const chartElement = document.getElementById('financialOverviewChart');
                    if (chartElement) {
                        const containerHeight = chartElement.offsetHeight || 400;
                        const categories = {!! json_encode($chartCategories ?? []) !!};
                        const revenueData = {!! json_encode($chartRevenue ?? []) !!};
                        const expensesData = {!! json_encode($chartExpenses ?? []) !!};

                        const chart = new ApexCharts(chartElement, {
                            series: [{
                                name: 'Revenue',
                                data: revenueData
                            }, {
                                name: 'Expenses',
                                data: expensesData
                            }],
                            chart: {
                                height: containerHeight,
                                type: 'bar',
                                toolbar: { show: false },
                                zoom: { enabled: false }
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '55%',
                                    borderRadius: 4
                                }
                            },
                            colors: ['#ff0000', '#51A6F5'],
                            xaxis: {
                                categories: categories,
                            },
                            yaxis: {
                                labels: {
                                    formatter: function (val) {
                                        return '₱' + (val / 1000).toFixed(0) + 'K';
                                    }
                                }
                            },
                            tooltip: {
                                y: {
                                    formatter: function (val) {
                                        return '₱' + Number(val).toLocaleString();
                                    }
                                }
                            }
                        });
                        chart.render();
                    }
                }
            }, 500);
        });
    </script>
    @endpush
</x-app-layout>
