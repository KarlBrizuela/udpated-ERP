<x-app-layout :title="'Cost of Goods Sold & Inventory Valuation'" :sidebar="$sidebar ?? 'admin-finance'" :role="$role ?? 'Finance Manager'">
    @push('styles')
    <style>
        /* Widescreen Expansion Override */
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

        /* Card Header Tabs with Red Accent Bottom Border */
        .nav-tabs-modern {
            border-bottom: 2px solid #e2e8f0;
        }
        .nav-tabs-modern .nav-link {
            border: none !important;
            color: #475569 !important;
            font-weight: 600 !important;
            font-size: 0.88rem !important;
            padding: 12px 20px !important;
            position: relative;
            background: transparent !important;
        }
        .nav-tabs-modern .nav-link.active {
            color: #D9251C !important;
        }
        .nav-tabs-modern .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #D9251C;
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
    </style>
    @endpush

    <div class="container-fluid p-0">
        <!-- KPI Metrics Ribbon -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-label">Total In-Stock Items</div>
                            <div class="kpi-value mt-1">{{ number_format($totalStock) }}</div>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(217, 37, 28, 0.08);">
                            <i class="las la-boxes fs-24" style="color: #D9251C;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3 mb-md-0">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-label">Inventory Asset Value (Cost)</div>
                            <div class="kpi-value mt-1">₱{{ number_format($totalCostValue, 2) }}</div>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(59, 130, 246, 0.08);">
                            <i class="las la-file-invoice-dollar fs-24" style="color: #3b82f6;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3 mb-md-0">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-label">Total Retail Potential Value</div>
                            <div class="kpi-value mt-1">₱{{ number_format($totalRetailValue, 2) }}</div>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(16, 185, 129, 0.08);">
                            <i class="las la-tags fs-24" style="color: #10b929;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-label">Cost of Goods Sold (COGS)</div>
                            <div class="kpi-value mt-1">₱{{ number_format($totalCogs, 2) }}</div>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(245, 158, 11, 0.08);">
                            <i class="las la-calculator fs-24" style="color: #f59e0b;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card Section -->
        <div class="card border-0 shadow-sm" style="border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;">
            <!-- Tabbed Navigation Header -->
            <div class="card-header bg-white border-0 pt-3 pb-0 px-4">
                <ul class="nav nav-tabs nav-tabs-modern" id="valuationTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="site-valuation-tab" data-bs-toggle="tab" data-bs-target="#site-valuation" type="button" role="tab" aria-controls="site-valuation" aria-selected="true">
                            <i class="las la-warehouse me-1 fs-16"></i> Warehouse Site Valuations
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="product-valuation-tab" data-bs-toggle="tab" data-bs-target="#product-valuation" type="button" role="tab" aria-controls="product-valuation" aria-selected="false">
                            <i class="las la-book-open me-1 fs-16"></i> Product Valuation Ledger
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="cogs-ledger-tab" data-bs-toggle="tab" data-bs-target="#cogs-ledger" type="button" role="tab" aria-controls="cogs-ledger" aria-selected="false">
                            <i class="las la-history me-1 fs-16"></i> Cost of Sales Ledger (COGS)
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Tab Contents -->
            <div class="card-body p-4">
                <div class="tab-content" id="valuationTabContent">
                    
                    <!-- Tab 1: Valuation by Site -->
                    <div class="tab-pane fade show active" id="site-valuation" role="tabpanel" aria-labelledby="site-valuation-tab">
                        <div class="table-responsive">
                            <table class="table table-modern align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 150px;">Site Code</th>
                                        <th>Warehouse / Location Name</th>
                                        <th>Location Address</th>
                                        <th class="text-center" style="width: 150px;">Total Items Count</th>
                                        <th class="text-end" style="width: 200px;">Valuation (Cost Basis)</th>
                                        <th class="text-end" style="width: 200px;">Valuation (Retail Basis)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sitesValuation as $site)
                                    <tr>
                                        <td class="text-deep-black">#{{ $site->code ?? ('SITE-' . $site->id) }}</td>
                                        <td class="text-deep-black">{{ $site->name }}</td>
                                        <td>{{ $site->location ?? 'Not Specified' }}</td>
                                        <td class="text-center text-deep-black">{{ number_format($site->stock_count) }}</td>
                                        <td class="text-end text-deep-black">₱{{ number_format($site->cost_value, 2) }}</td>
                                        <td class="text-end text-deep-black" style="color: #10b929 !important;">₱{{ number_format($site->retail_value, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No active physical warehouse sites found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 2: Product Valuation Ledger -->
                    <div class="tab-pane fade" id="product-valuation" role="tabpanel" aria-labelledby="product-valuation-tab">
                        <!-- Card Header Actions: Search Form on Right, aligned nicely -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <span class="text-muted small">Search and paginate through book inventory values.</span>
                            </div>
                            
                            <!-- Search box alignment following design rules -->
                            <form action="{{ route('admin-finance.accounting.inventory-valuation') }}" method="GET" class="d-flex gap-2">
                                <input type="hidden" name="tab" value="product">
                                <div class="input-group input-group-sm" style="width: 280px;">
                                    <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 10px; border-top-left-radius: 4px; border-bottom-left-radius: 4px;">
                                        <i class="las la-search text-muted fs-16"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search SKU, name, or author..." value="{{ $search ?? '' }}" style="height: 38px; border-color: #cbd5e1; border-top-right-radius: 4px; border-bottom-right-radius: 4px; font-size: 0.82rem; padding-left: 0; outline: none; box-shadow: none;">
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm text-white px-3 font-w600" style="background-color: #D9251C; height: 38px; border-radius: 4px;">Search</button>
                                @if(!empty($search))
                                <a href="{{ route('admin-finance.accounting.inventory-valuation', ['tab' => 'product']) }}" class="btn btn-light btn-sm border px-3 d-flex align-items-center" style="height: 38px; border-radius: 4px;">Clear</a>
                                @endif
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-modern align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 140px;">SKU / Code</th>
                                        <th>Product Name / Title</th>
                                        <th class="text-center" style="width: 110px;">Stock</th>
                                        <th class="text-end" style="width: 120px;">Unit Cost</th>
                                        <th class="text-end" style="width: 120px;">Unit Price</th>
                                        <th class="text-end" style="width: 180px;">Valuation (Cost Basis)</th>
                                        <th class="text-end" style="width: 180px;">Valuation (Retail Basis)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($booksPaginated as $book)
                                    <tr>
                                        <td><strong>{{ $book->sku ?: ('BK-' . $book->id) }}</strong></td>
                                        <td class="text-deep-black">{{ $book->name }}</td>
                                        <td class="text-center">
                                            @if($book->total_stock > 0)
                                            <span class="badge bg-light text-success border border-success px-2.5 py-1 fw-bold fs-13">
                                                {{ number_format($book->total_stock) }}
                                            </span>
                                            @else
                                            <span class="text-muted small" style="opacity: 0.4;">0</span>
                                            @endif
                                        </td>
                                        <td class="text-end">₱{{ number_format($book->cost, 2) }}</td>
                                        <td class="text-end">₱{{ number_format($book->price, 2) }}</td>
                                        <td class="text-end text-deep-black">₱{{ number_format($book->total_cost_value, 2) }}</td>
                                        <td class="text-end text-deep-black" style="color: #10b929 !important;">₱{{ number_format($book->total_retail_value, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No products found matching valuation criteria.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Capsule Pagination block following Claretian Red colors -->
                        <div id="paginationContainer" class="mt-4 d-flex justify-content-end pe-0">
                            {{ $booksPaginated->links('pagination::bootstrap-4') }}
                        </div>
                    </div>

                    <!-- Tab 3: Cost of Goods Sold (COGS) Ledger -->
                    <div class="tab-pane fade" id="cogs-ledger" role="tabpanel" aria-labelledby="cogs-ledger-tab">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <span class="text-muted small">Recent transaction logs posted to Account 5000 (Cost of Sales / COGS).</span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-modern align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 140px;">Posting Date</th>
                                        <th style="width: 160px;">Reference Voucher</th>
                                        <th>Transaction Memo / Explanation</th>
                                        <th class="text-end" style="width: 180px;">Debit (Sales Cost)</th>
                                        <th class="text-end" style="width: 180px;">Credit (Adjustments)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentCogsTransactions as $item)
                                    <tr>
                                        <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="text-deep-black">
                                            @if($item->journalEntry)
                                                #{{ $item->journalEntry->entry_number ?? ('JV-' . $item->journalEntry->id) }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $item->memo ?? 'Cost of sales adjustment post' }}</td>
                                        <td class="text-end text-deep-black text-danger">
                                            @if($item->debit > 0)
                                                ₱{{ number_format($item->debit, 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-end text-deep-black text-success">
                                            @if($item->credit > 0)
                                                ₱{{ number_format($item->credit, 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No recent postings recorded in Cost of Sales.</td>
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

    <!-- Active Tab Selection Handler via URL Query -->
    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Keep tab selected on search/page redirect
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam === 'product') {
                const tabTrigger = new bootstrap.Tab(document.getElementById('product-valuation-tab'));
                tabTrigger.show();
            } else if (tabParam === 'cogs') {
                const tabTrigger = new bootstrap.Tab(document.getElementById('cogs-ledger-tab'));
                tabTrigger.show();
            }
        });
    </script>
    @endpush
</x-app-layout>
