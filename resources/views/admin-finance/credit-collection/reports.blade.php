<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .report-card {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .report-header {
            margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #e0e0e0;
        }

        .document-title {
            text-align: center; font-size: 1.75rem; font-weight: 700;
            color: #333; margin-top: 1rem; text-transform: uppercase;
        }

        .filter-section {
            background: #f8f9fa;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .table-report thead th {
            background: #f1f3f5;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: #495057;
            border-top: none;
        }

        .amount-column { text-align: right; font-family: 'Courier New', Courier, monospace; font-weight: 600; }
        
        .tab-content { padding-top: 1.5rem; }

        .report-summary-box {
            border-left: 4px solid #ff0000;
            background: #fffafa;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0 4px 4px 0;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card report-card">
                <div class="report-header">
                    <div class="document-title">CREDIT & COLLECTION REPORTS</div>
                </div>

                <!-- Main Tabs -->
                <ul class="nav nav-tabs" id="reportTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="ar-aging-tab" data-bs-toggle="tab" href="#ar-aging" role="tab">AR Aging Summary</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="sales-summary-tab" data-bs-toggle="tab" href="#sales-summary" role="tab">Sales by Customer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="collection-report-tab" data-bs-toggle="tab" href="#collection-report" role="tab">Collection Report</a>
                    </li>
                </ul>

                <div class="tab-content" id="reportTabsContent">
                    <!-- AR Aging Summary Tab -->
                    <div class="tab-pane fade show active" id="ar-aging" role="tabpanel">
                        <div class="filter-section">
                            <form action="{{ route('admin-finance.credit-collection.reports') }}" method="GET" class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-uppercase">Filter by Group</label>
                                    <select name="ar_group" class="form-select shadow-sm border-0">
                                        <option value="customer_type" {{ ($filters['arGroup'] ?? '') == 'customer_type' ? 'selected' : '' }}>Customer Type (Team A/B/C)</option>
                                        <option value="class" {{ ($filters['arGroup'] ?? '') == 'class' ? 'selected' : '' }}>Class (Provincial/BIC)</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-uppercase">Value</label>
                                    <select name="ar_value" class="form-select shadow-sm border-0">
                                        <option value="Team A" {{ ($filters['arVal'] ?? '') == 'Team A' ? 'selected' : '' }}>TEAM A</option>
                                        <option value="Team B" {{ ($filters['arVal'] ?? '') == 'Team B' ? 'selected' : '' }}>TEAM B</option>
                                        <option value="Team C" {{ ($filters['arVal'] ?? '') == 'Team C' ? 'selected' : '' }}>TEAM C</option>
                                        <option value="BIC" {{ ($filters['arVal'] ?? '') == 'BIC' ? 'selected' : '' }}>BIC</option>
                                        <option value="LAG" {{ ($filters['arVal'] ?? '') == 'LAG' ? 'selected' : '' }}>LAG (Provincial)</option>
                                        <option value="MNL" {{ ($filters['arVal'] ?? '') == 'MNL' ? 'selected' : '' }}>MNL (Metro Manila)</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-uppercase">As of Date</label>
                                    <input type="date" name="ar_date" class="form-control shadow-sm border-0" value="{{ $filters['arDate'] ?? date('Y-m-d') }}">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100 shadow"><i class="las la-sync me-1"></i> Update Report</button>
                                </div>
                            </form>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="report-summary-box">
                                <span class="text-muted small d-block">Total Open Balance</span>
                                <h4 class="mb-0 fw-bold text-dark">₱ {{ number_format($arTotalOpenBalance ?? 0, 2) }}</h4>
                            </div>
                            <button class="btn btn-outline-success btn-sm px-4 shadow-sm"><i class="las la-file-excel me-1"></i> Export to Excel</button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-report table-hover border">
                                <thead>
                                    <tr>
                                        <th>Customer Name</th>
                                        <th class="amount-column">Current</th>
                                        <th class="amount-column">1 - 30</th>
                                        <th class="amount-column">31 - 60</th>
                                        <th class="amount-column">61 - 90</th>
                                        <th class="amount-column">> 90</th>
                                        <th class="amount-column">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($arCustomers ?? [] as $customer)
                                    <tr>
                                        <td>{{ $customer->customer_name ?? $customer->company_name }}</td>
                                        <td class="amount-column">{{ number_format($customer->current, 2) }}</td>
                                        <td class="amount-column">{{ number_format($customer->days_1_30, 2) }}</td>
                                        <td class="amount-column">{{ number_format($customer->days_31_60, 2) }}</td>
                                        <td class="amount-column">{{ number_format($customer->days_61_90, 2) }}</td>
                                        <td class="amount-column">{{ number_format($customer->over_90, 2) }}</td>
                                        <td class="amount-column text-primary fw-bold">{{ number_format($customer->total_ar, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No outstanding balances found for this group.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Sales Summary Tab -->
                    <div class="tab-pane fade" id="sales-summary" role="tabpanel">
                        <div class="filter-section">
                            <form action="{{ route('admin-finance.credit-collection.reports') }}" method="GET" class="row g-3 align-items-end">
                                <input type="hidden" name="tab" value="sales-summary">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-uppercase">Customer Type</label>
                                    <select name="sales_type" class="form-select shadow-sm border-0">
                                        <option value="Team A" {{ ($filters['salesType'] ?? '') == 'Team A' ? 'selected' : '' }}>TEAM A</option>
                                        <option value="Team B" {{ ($filters['salesType'] ?? '') == 'Team B' ? 'selected' : '' }}>TEAM B</option>
                                        <option value="Team C" {{ ($filters['salesType'] ?? '') == 'Team C' ? 'selected' : '' }}>TEAM C</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-uppercase">Start Date</label>
                                    <input type="date" name="sales_start" class="form-control shadow-sm border-0" value="{{ $filters['salesStart'] ?? date('Y-m-01') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-uppercase">End Date</label>
                                    <input type="date" name="sales_end" class="form-control shadow-sm border-0" value="{{ $filters['salesEnd'] ?? date('Y-m-t') }}">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100 shadow"><i class="las la-sync me-1"></i> Update Report</button>
                                </div>
                            </form>
                        </div>

                        <div class="alert alert-info py-2 border-0 shadow-sm mb-4">
                            <i class="las la-info-circle me-1"></i> <strong>Note:</strong> Delivery charges and freight are automatically excluded from the "Sales" column below.
                        </div>

                        <div class="table-responsive">
                            <table class="table table-report table-hover border">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Trans No.</th>
                                        <th class="amount-column">Sales Amount</th>
                                        <th class="amount-column">VAT</th>
                                        <th class="amount-column">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $tsales = 0; $tvat = 0; $ttotal = 0; @endphp
                                    @forelse($salesOrders ?? [] as $order)
                                    @php 
                                        $tsales += $order->net_sales;
                                        $tvat += $order->tax_amount;
                                        $ttotal += ($order->net_sales + $order->tax_amount);
                                    @endphp
                                    <tr>
                                        <td>{{ Carbon\Carbon::parse($order->created_at)->format('m/d/Y') }}</td>
                                        <td>{{ $order->customer->customer_name ?? $order->customer->company_name ?? 'Unknown' }}</td>
                                        <td>{{ $order->so_number }}</td>
                                        <td class="amount-column">{{ number_format($order->net_sales, 2) }}</td>
                                        <td class="amount-column">{{ number_format($order->tax_amount, 2) }}</td>
                                        <td class="amount-column fw-bold">{{ number_format($order->net_sales + $order->tax_amount, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No sales found for this period.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light fw-bold text-dark">
                                        <td colspan="3" class="text-end">GRAND TOTAL</td>
                                        <td class="amount-column">{{ number_format($tsales, 2) }}</td>
                                        <td class="amount-column">{{ number_format($tvat, 2) }}</td>
                                        <td class="amount-column text-primary fs-5">{{ number_format($ttotal, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Collection Report Tab -->
                    <div class="tab-pane fade" id="collection-report" role="tabpanel">
                        <div class="filter-section">
                            <form action="{{ route('admin-finance.credit-collection.reports') }}" method="GET" class="row g-3">
                                <input type="hidden" name="tab" value="collection-report">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-uppercase">Period</label>
                                    <div class="input-group">
                                        <input type="date" name="coll_start" class="form-control shadow-sm border-0" value="{{ $filters['collStart'] ?? date('Y-m-01') }}">
                                        <span class="input-group-text bg-white border-0">to</span>
                                        <input type="date" name="coll_end" class="form-control shadow-sm border-0" value="{{ $filters['collEnd'] ?? date('Y-m-t') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-uppercase">Collection Group</label>
                                    <select name="coll_group" class="form-select shadow-sm border-0">
                                        <option value="Teams" {{ ($filters['collGroup'] ?? '') == 'Teams' ? 'selected' : '' }}>Teams (A, B, C, PCBS, etc.)</option>
                                        <option value="Ecom" {{ ($filters['collGroup'] ?? '') == 'Ecom' ? 'selected' : '' }}>Ecom (Lazada, Shopee, Tiktok)</option>
                                        <option value="Events" {{ ($filters['collGroup'] ?? '') == 'Events' ? 'selected' : '' }}>Events (Ads & Promo)</option>
                                        <option value="Booksale" {{ ($filters['collGroup'] ?? '') == 'Booksale' ? 'selected' : '' }}>Booksale (Direct Sales)</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100 shadow"><i class="las la-filter me-1"></i> Apply Filter</button>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-report border">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Transaction Ref</th>
                                        <th>Customer</th>
                                        <th class="amount-column">Payment Received</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-light fw-bold text-uppercase">
                                        <td colspan="3">GROUP: {{ $filters['collGroup'] ?? 'Teams' }}</td>
                                        <td class="amount-column">{{ number_format(($collections ?? collect())->sum('amount'), 2) }}</td>
                                    </tr>

                                    @forelse($collections ?? [] as $payment)
                                    <tr>
                                        <td>{{ Carbon\Carbon::parse($payment->payment_date)->format('m/d/Y') }}</td>
                                        <td>{{ $payment->reference_number ?? 'PYMT-' . $payment->id }}</td>
                                        <td>{{ $payment->customer->customer_name ?? $payment->customer->company_name ?? 'Unknown' }}</td>
                                        <td class="amount-column">{{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No collections recorded for this filter constraint.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="bg-primary text-white">
                                        <td colspan="3" class="fw-bold">GRAND TOTAL COLLECTIONS</td>
                                        <td class="amount-column fw-bold fs-5">₱ {{ number_format(($collections ?? collect())->sum('amount'), 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
