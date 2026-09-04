<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/chartist/css/chartist.min.css') }}">
    <style>
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

        .period-btn:hover:not(.active) {
            background-color: #ffe7e7;
            border-color: #dc3545;
            color: #dc3545;
        }
        
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

    <!-- Time Period Selector -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-black font-w600">View Sales Statistics</h5>
                        <div class="btn-group" role="group" aria-label="Time period selector">
                            <button type="button" class="btn btn-outline-danger period-btn {{ $period == 'daily' ? 'active' : '' }}" data-period="daily">Daily</button>
                            <button type="button" class="btn btn-outline-danger period-btn {{ $period == 'weekly' ? 'active' : '' }}" data-period="weekly">Weekly</button>
                            <button type="button" class="btn btn-outline-danger period-btn {{ $period == 'monthly' ? 'active' : '' }}" data-period="monthly">Monthly</button>
                            <button type="button" class="btn btn-outline-danger period-btn {{ $period == 'yearly' ? 'active' : '' }}" data-period="yearly">Yearly</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card card-bd">
                <div class="bg-primary card-border"></div>
                <div class="card-body box-style">
                    <div class="media align-items-center">
                        <div class="media-body me-3">
                            <h2 class="num-text text-black font-w700" id="totalSalesAmount">₱ {{ number_format($totalSales ?? 0, 2) }}</h2>
                                <span class="fs-14">Total Sales</span>
                                <small class="text-muted d-block" id="salesPeriodLabel">{{ $periodLabel ?? 'This Month' }}</small>
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
                            <h2 class="num-text text-black font-w700" id="totalOrdersCount">{{ $totalOrders ?? 0 }}</h2>
                            <span class="fs-14">Total Orders</span>
                            <small class="text-muted d-block" id="ordersPeriodLabel">{{ $periodLabel ?? 'This Month' }}</small>
                        </div>
                        <i class="las la-shopping-bag" style="font-size: 2rem; color: #68CF29;"></i>
                    </div>
                </div>
            </div>
        </div>
        {{-- 
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card card-bd">
                <div class="bg-warning card-border"></div>
                <div class="card-body box-style">
                    <div class="media align-items-center">
                        <div class="media-body me-3">
                            <h2 class="num-text text-black font-w700" id="averageOrderValue">₱ {{ number_format($avgOrder ?? 0, 2) }}</h2>
                            <span class="fs-14">Average Order Value</span>
                            <small class="text-muted d-block" id="avgOrderPeriodLabel">{{ $periodLabel ?? 'This Month' }}</small>
                        </div>
                        <i class="las la-chart-line" style="font-size: 2rem; color: #FFAC30;"></i>
                    </div>
                </div>
            </div>
        </div>
        --}}
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card card-bd">
                <div class="bg-info card-border"></div>
                <div class="card-body box-style">
                    <div class="media align-items-center">
                        <div class="media-body me-3">
                            <h2 class="num-text text-black font-w700" id="topChannelSales">₱ {{ number_format(optional($topChannel)->total ?? 0, 2) }}</h2>
                            <span class="fs-14">Top Channel</span>
                            @php
                                $topLabel = optional($topChannel)->platform ?? 'N/A';
                                $topMap = [
                                    'lazada' => 'Lazada',
                                    'shopee' => 'Shopee',
                                    'tiktok' => 'TikTok',
                                    'website' => 'Website',
                                    'facebook' => 'Facebook',
                                    'other' => 'Other',
                                    'area_sales' => 'Area Sales',
                                    'direct_pos' => 'Direct POS',
                                    'ecom_pos' => 'E‑commerce',
                                    'pos' => 'POS',
                                    'so' => 'SO',
                                    'nbs' => 'NBS',
                                    'e-com' => 'E-Com',
                                ];
                                $topDisplay = $topMap[strtolower($topLabel)] ?? ucwords(str_replace(['_','-'], ' ', strtolower($topLabel)));
                            @endphp
                            <small class="text-muted d-block" id="topChannelLabel">{{ $topDisplay }}</small>
                        </div>
                        <i class="las la-store" style="font-size: 2rem; color: #51A6F5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-xxl-8">
            <div class="card" style="min-height: 500px; display: flex; flex-direction: column;">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">Sales Overview <span id="chartPeriodLabel" class="text-muted fs-14">(This Month)</span></h4>
                </div>
                <div class="card-body" style="flex: 1; min-height: 450px; display: flex; flex-direction: column; padding: 1.5rem;">
                    <div id="salesChart" class="timeline-chart" style="flex: 1; min-height: 400px;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-xxl-4">
            <div class="card" style="min-height: 500px; display: flex; flex-direction: column;">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">Sales Channels</h4>
                </div>
                <div class="card-body" style="flex: 1;">
                    @php
                        $channelsTotal = collect($channels ?? [])->sum('total') ?: ($totalSales ?? 0);
                        $barColors = ['bg-primary','bg-success','bg-warning','bg-info','bg-secondary'];
                    @endphp

                    @if(!empty($channels) && count($channels) > 0)
                        @foreach($channels as $idx => $ch)
                            @php
                                $label = $ch->platform ?? 'Unknown';
                                $map = [
                                    'lazada' => 'Lazada',
                                    'shopee' => 'Shopee',
                                    'tiktok' => 'TikTok',
                                    'website' => 'Website',
                                    'facebook' => 'Facebook',
                                    'other' => 'Other',
                                    'area_sales' => 'Area Sales',
                                    'direct_pos' => 'Direct POS',
                                    'ecom_pos' => 'E‑commerce',
                                    'area_consignment' => 'Area Consignment',
                                    'direct_consignment' => 'Direct Consignment',
                                    'ecom_direct' => 'E‑commerce',
                                    'paid' => 'Paid',
                                    'pos' => 'POS',
                                    'so' => 'SO',
                                    'nbs' => 'NBS',
                                    'e-com' => 'E-Com',
                                ];
                                $key = strtolower($label);
                                $displayLabel = $map[$key] ?? ucwords(str_replace(['_','-'], ' ', $key));
                                $amount = (float)$ch->total;
                                $pct = $channelsTotal > 0 ? round(($amount / $channelsTotal) * 100) : 0;
                                $color = $barColors[$idx % count($barColors)];
                            @endphp
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-black font-w600">{{ $displayLabel }}</span>
                                    <span class="text-black font-w600">₱ {{ number_format($amount, 2) }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar {{ $color }}" role="progressbar" style="width: {{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-black font-w600">Area Sales</span>
                                <span class="text-black font-w600">₱0.00</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-black font-w600">Direct POS</span>
                                <span class="text-black font-w600">₱0.00</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-black font-w600">ECOM POS</span>
                                <span class="text-black font-w600">₱0.00</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('vendor/chartist/js/chartist.min.js') }}"></script>
    <script src="{{ asset('vendor/apexchart/apexchart.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize sales chart
            if (window.ApexCharts) {
                        const salesChartElement = document.getElementById('salesChart');
                if (salesChartElement) {
                    const salesChart = new ApexCharts(salesChartElement, {
                        series: [{
                            name: 'Sales',
                            data: {!! json_encode($chartRevenue ?? []) !!}
                        }],
                        chart: {
                            type: 'area',
                            height: '100%',
                            toolbar: { show: false }
                        },
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 2 },
                        xaxis: {
                            categories: {!! json_encode($chartCategories ?? []) !!}
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.7,
                                opacityTo: 0.3,
                                stops: [0, 90, 100]
                            }
                        },
                        colors: ['#ff0000'],
                        tooltip: {
                            y: {
                                formatter: function (val) {
                                    return '₱' + val.toLocaleString();
                                }
                            }
                        }
                    });
                    salesChart.render();
                }
            }

            // Period selector: reloads the page with query parameter
            document.querySelectorAll('.period-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const selectedPeriod = this.dataset.period;
                    window.location.href = "{{ route('marketing.dashboard') }}?period=" + selectedPeriod;
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
