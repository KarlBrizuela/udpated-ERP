<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        /* Section 2: Page Layout & Grid Width Expansion (Claretian ERP Guidelines) */
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
            padding-bottom: 80px !important;
        }

        .cost-header-card {
            background: #ffffff;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }

        /* Section 3: Modern Table Designs (General Journal Style) */
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
        .table-responsive .table td {
            font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        }

        /* Header Style (thead th): Light Off-White background #f8fafc, Dark Slate Gray #475569, Uppercase, 0.72rem */
        .table-responsive .table thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            font-size: 0.72rem !important;
            letter-spacing: 0.8px !important;
            padding: 14px 18px !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            border-bottom: 2px solid #e2e8f0 !important;
        }

        /* Body Style (tbody td): 0.84rem, padding 14px 18px, bottom border 1px solid #f1f5f9, NO vertical lines */
        .table-responsive .table tbody td {
            padding: 14px 18px !important;
            font-size: 0.84rem !important;
            color: #475569 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            border-bottom: 1px solid #f1f5f9 !important;
            background-color: transparent !important;
            vertical-align: middle !important;
        }

        /* Hover State */
        .table-responsive .table tbody tr {
            transition: all 0.15s ease-in-out !important;
        }

        .table-responsive .table tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* Itemized Component Cards inside Modals */
        .cost-component-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 14px;
            transition: all 0.15s ease-in-out;
        }

        .cost-component-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
    </style>
    @endpush

    <!-- Section 2: Remove Double Nesting (<div class="container-fluid p-0">) -->
    <div class="container-fluid p-0">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="las la-check-circle me-2 fs-18"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="las la-exclamation-circle me-2 fs-18"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Master Title Header with Flex-Shrink Controls (Section 14) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="cost-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <!-- Titles & Value Fields: Deep Black (#000000 or #0f172a) -->
                        <h4 class="fs-22 mb-1 fw-bold" style="color: #0f172a !important; letter-spacing: -0.5px;">Production Costing & COGS Accounting</h4>
                        <!-- Keys & Labels: Dark Slate Gray (#475569) -->
                        <p class="small mb-0" style="color: #475569 !important;">Automated costing aggregation integrated with the Production ERP module. No manual accounting computation required.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2" style="flex-shrink: 0;">
                        <form action="{{ route('production.costing.sync') }}" method="POST" class="d-inline" onsubmit="const btn = this.querySelector('button'); btn.disabled = true; btn.innerHTML = '<i class=\'las la-spinner la-spin fs-18\'></i> Syncing...';">
                            @csrf
                            <button type="submit" class="btn text-white btn-sm px-3 shadow-sm d-inline-flex align-items-center justify-content-center gap-2" style="background-color: #0d6efd; border-color: #0d6efd; height: 38px; border-radius: 6px; font-weight: 600;" title="Pull latest costing snapshots from Production ERP (erpccfi.claretianpublications.ph)">
                                <i class="las la-sync fs-18"></i> Sync from Production ERP
                            </button>
                        </form>
                        {{--
                        <button class="btn text-white btn-sm px-3 shadow-sm d-inline-flex align-items-center justify-content-center gap-2" style="background-color: #D9251C; border-color: #D9251C; height: 38px; border-radius: 6px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#autoCalculateModal">
                            <i class="las la-magic fs-18"></i> Auto-Calculate Costing
                        </button>
                        --}}
                        <button class="btn btn-outline-secondary btn-sm px-3 shadow-sm d-inline-flex align-items-center justify-content-center gap-2" style="height: 38px; border-radius: 6px; font-weight: 600;" onclick="window.print()">
                            <i class="las la-print fs-18"></i> Print Costing Ledger
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 14: Dashboard Metric Card Layouts (Top-Down Metric Layout) -->
        <div class="row mb-4" style="align-items: flex-start;">
            <div class="col-md-3">
                <div class="card shadow-sm h-100" style="border-radius: 10px; border: 1px solid #e2e8f0; background-color: #ffffff; height: auto !important;">
                    <div class="card-body p-3">
                        <span class="d-block text-uppercase fw-bold mb-1" style="color: #475569 !important; font-size: 0.70rem; letter-spacing: 0.5px;">TOTAL PRODUCTION COGS</span>
                        <h3 class="fw-bold mb-0" style="color: #0f172a !important; font-size: 1.5rem;">₱{{ number_format($metrics['total_cogs'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100" style="border-radius: 10px; border: 1px solid #e2e8f0; background-color: #ffffff; height: auto !important;">
                    <div class="card-body p-3">
                        <span class="d-block text-uppercase fw-bold mb-1" style="color: #475569 !important; font-size: 0.70rem; letter-spacing: 0.5px;">AVG UNIT COGS RATE</span>
                        <h3 class="fw-bold mb-0" style="color: #D9251C !important; font-size: 1.5rem;">₱{{ number_format($metrics['avg_unit_cogs'], 2) }} <span class="fs-12 text-muted fw-normal">/ copy</span></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100" style="border-radius: 10px; border: 1px solid #e2e8f0; background-color: #ffffff; height: auto !important;">
                    <div class="card-body p-3">
                        <span class="d-block text-uppercase fw-bold mb-1" style="color: #475569 !important; font-size: 0.70rem; letter-spacing: 0.5px;">TOTAL UNITS PRODUCED</span>
                        <h3 class="fw-bold mb-0" style="color: #0f172a !important; font-size: 1.5rem;">{{ number_format($metrics['total_qty_produced']) }} <span class="fs-12 text-muted fw-normal">Copies</span></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100" style="border-radius: 10px; border: 1px solid #e2e8f0; background-color: #ffffff; height: auto !important;">
                    <div class="card-body p-3">
                        <span class="d-block text-uppercase fw-bold mb-1" style="color: #475569 !important; font-size: 0.70rem; letter-spacing: 0.5px;">ACTIVE COSTED JOBS</span>
                        <h3 class="fw-bold mb-0" style="color: #0f172a !important; font-size: 1.5rem;">{{ $metrics['active_jobs_count'] }} <span class="fs-12 text-muted fw-normal">Jobs</span></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clean, Uncrowded Production Costing Summary Table (General Journal Style - Section 3) -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px; border: 1px solid #e2e8f0; background-color: #ffffff;">
                    <!-- Section 5: Search & Filter Form Layouts at Card Header -->
                    <div class="card-header bg-white border-0 pt-4 pb-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" style="border-bottom: 1px solid #e2e8f0 !important;">
                        <div>
                            <h5 class="mb-1 fw-bold" style="color: #0f172a !important; font-size: 1.1rem;">
                                <i class="las la-book-open me-2" style="color: #D9251C;"></i> Production Costing & COGS Summary Ledger
                            </h5>
                            <p class="small mb-0" style="color: #475569 !important;">Overview of production cost allocations, total COGS, and unit rates across active jobs</p>
                        </div>
                        <form action="{{ route('admin-finance.accounting.production-costing') }}" method="GET" class="d-flex align-items-center gap-2">
                            <!-- Input Group Component (Section 5) -->
                            <div class="input-group input-group-sm" style="width: 260px;">
                                <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 10px; border-top-left-radius: 4px; border-bottom-left-radius: 4px;">
                                    <i class="las la-search text-muted fs-16"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Search Job No or Title..." value="{{ request('search') }}" style="height: 38px; border-color: #cbd5e1; border-top-right-radius: 4px; border-bottom-right-radius: 4px; font-size: 0.82rem; color: #000000; outline: none; box-shadow: none;">
                            </div>
                            <!-- Separate Action Buttons (Section 5) -->
                            <button type="submit" class="btn text-white px-3 fw-bold d-inline-flex align-items-center justify-content-center" style="background-color: #D9251C; border-color: #D9251C; height: 38px; border-radius: 4px; font-size: 0.85rem;">
                                Search
                            </button>
                            @if(request('search'))
                            <a href="{{ route('admin-finance.accounting.production-costing') }}" class="btn btn-light border text-muted px-2 d-inline-flex align-items-center justify-content-center" style="height: 38px; border-radius: 4px; font-size: 0.82rem;">Clear</a>
                            @endif
                        </form>
                    </div>

                    <div class="card-body p-0">
                        <!-- Streamlined Summary Table: No pagination, clear horizontal alignment, no cramped columns -->
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 130px; white-space: nowrap;">JOB NO</th>
                                        <th style="min-width: 360px;">TITLE / CATALOG BOOK</th>
                                        <th class="text-center" style="width: 120px; white-space: nowrap;">RUN QTY / PAGES</th>
                                        <th class="text-end" style="width: 140px; white-space: nowrap;" title="Direct Materials & Labor: Paper, Ink, Labor, Electricity">
                                            DIRECT MATERIALS <i class="las la-info-circle text-muted fs-13"></i>
                                        </th>
                                        <th class="text-end" style="width: 150px; white-space: nowrap;" title="Manufacturing Overhead: Machine, Binding, UV, Shrink Wrap, Packaging, Freight, Warehouse, Overhead">
                                            MFG & OVERHEAD <i class="las la-info-circle text-muted fs-13"></i>
                                        </th>
                                        <th class="text-end" style="width: 110px; white-space: nowrap; color: #D9251C !important;">TOTAL COGS</th>
                                        <th class="text-end" style="width: 100px; white-space: nowrap; color: #0ea5e9 !important;">UNIT COGS</th>
                                        <th class="text-center" style="width: 120px; white-space: nowrap;">ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($costings as $cost)
                                    @php
                                        $directMaterials = $cost->paper_cost + $cost->ink_cost + $cost->labor_cost + $cost->electricity_cost;
                                        $mfgOverhead = $cost->machine_cost + $cost->binding_cost + $cost->uv_cost + $cost->shrink_wrap_cost + $cost->packaging_cost + $cost->freight_cost + $cost->warehouse_cost + $cost->overhead_cost;
                                        
                                        $directMaterialsTooltip = "Paper: ₱" . number_format($cost->paper_cost, 2) . " | Ink: ₱" . number_format($cost->ink_cost, 2) . " | Labor: ₱" . number_format($cost->labor_cost, 2) . " | Power: ₱" . number_format($cost->electricity_cost, 2);
                                        $mfgOverheadTooltip = "Machine: ₱" . number_format($cost->machine_cost, 2) . " | Binding: ₱" . number_format($cost->binding_cost, 2) . " | UV: ₱" . number_format($cost->uv_cost, 2) . " | Shrink: ₱" . number_format($cost->shrink_wrap_cost, 2) . " | Pack: ₱" . number_format($cost->packaging_cost, 2) . " | Freight: ₱" . number_format($cost->freight_cost, 2) . " | WH: ₱" . number_format($cost->warehouse_cost, 2) . " | Overhead: ₱" . number_format($cost->overhead_cost, 2);
                                    @endphp
                                    <tr>
                                        <td style="white-space: nowrap;">
                                            <span class="fw-bold font-monospace" style="color: #0f172a !important;">{{ $cost->job_number }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold d-block mb-1" style="color: #0f172a !important; font-size: 0.88rem; line-height: 1.35;">{{ $cost->job_title }}</span>
                                            <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.72rem; font-weight: 500;">
                                                <i class="las la-barcode me-1"></i> {{ $cost->book ? $cost->book->sku : 'Custom Job' }}
                                            </span>
                                        </td>
                                        <td class="text-center" style="white-space: nowrap;">
                                            <span class="fw-bold d-block" style="color: #0f172a !important;">{{ number_format($cost->quantity_produced) }} copies</span>
                                            <span class="small" style="color: #475569 !important;">{{ number_format($cost->pages_count) }} pages</span>
                                        </td>
                                        <td class="text-end" style="white-space: nowrap;" title="{{ $directMaterialsTooltip }}">
                                            <span class="fw-bold d-block" style="color: #0f172a !important;">₱{{ number_format($directMaterials, 2) }}</span>
                                            <span class="d-block" style="color: #475569 !important; font-size: 0.70rem;">Paper, Ink, Labor, Power</span>
                                        </td>
                                        <td class="text-end" style="white-space: nowrap;" title="{{ $mfgOverheadTooltip }}">
                                            <span class="fw-bold d-block" style="color: #0f172a !important;">₱{{ number_format($mfgOverhead, 2) }}</span>
                                            <span class="d-block" style="color: #475569 !important; font-size: 0.70rem;">Machine, Bind, Freight, etc.</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold d-block" style="color: #D9251C !important; font-size: 0.95rem;">₱{{ number_format($cost->total_cogs, 2) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold d-block" style="color: #0ea5e9 !important; font-size: 0.95rem;">₱{{ number_format($cost->unit_cogs, 2) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <!-- Action 1: View Breakdown Modal (Section 6) -->
                                                <button type="button" class="btn btn-info shadow btn-xs sharp text-white" title="View 12-Component Cost Breakdown" data-bs-toggle="modal" data-bs-target="#costDetailsModal{{ $cost->id }}">
                                                    <i class="las la-eye"></i>
                                                </button>
                                                <!-- Action 2: View Full Costing Sheet Page -->
                                                <a href="{{ route('admin-finance.accounting.production-costing.show', $cost->id) }}" class="btn btn-sm btn-outline-danger px-2 py-1 d-inline-flex align-items-center gap-1" style="color: #D9251C; border-color: #D9251C; border-radius: 4px; font-size: 0.76rem; font-weight: 600;" title="Open Detailed Production Job Sheet">
                                                    <i class="las la-file-alt"></i> Sheet
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- SECTION 6: DETAILED COSTING BREAKDOWN MODAL (MODAL-XL) -->
                                    <div class="modal fade" id="costDetailsModal{{ $cost->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-xl">
                                            <div class="modal-content border-0 shadow">
                                                <!-- Section 6 Header: Customer/Job Info Left, Outstanding / Total COGS Right -->
                                                <div class="modal-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="badge bg-light text-muted border px-2 py-1 mb-1 font-monospace">{{ $cost->job_number }}</span>
                                                        <h4 class="fw-bold mb-1" style="color: #000000 !important;">{{ $cost->job_title }}</h4>
                                                        <p class="small text-muted mb-0">
                                                            <i class="las la-book me-1"></i> Catalog Book: <strong>{{ $cost->book ? $cost->book->name . ' (' . $cost->book->sku . ')' : 'Custom Printing Job' }}</strong>
                                                            | <i class="las la-copy me-1"></i> Run Qty: <strong>{{ number_format($cost->quantity_produced) }} Copies</strong>
                                                            | <i class="las la-file me-1"></i> Pages: <strong>{{ number_format($cost->pages_count) }} Pages</strong>
                                                        </p>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="text-end">
                                                            <span class="d-block text-uppercase text-muted fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">Unit COGS Rate</span>
                                                            <span class="fw-bold" style="color: #0ea5e9; font-size: 1.1rem;">₱{{ number_format($cost->unit_cogs, 2) }} / unit</span>
                                                        </div>
                                                        <!-- High-Contrast Total COGS Badge (Section 6) -->
                                                        <span class="px-3 py-2 rounded fw-bold text-danger d-inline-block" style="font-size: 1.25rem; background-color: rgba(217, 37, 28, 0.08); border: 1px solid rgba(217, 37, 28, 0.15);">
                                                            ₱{{ number_format($cost->total_cogs, 2) }}
                                                        </span>
                                                        <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                </div>

                                                <div class="modal-body p-4 bg-light">
                                                    <h6 class="fw-bold mb-3 text-uppercase" style="color: #475569 !important; font-size: 0.76rem; letter-spacing: 0.8px;">
                                                        <i class="las la-layer-group me-1" style="color: #D9251C;"></i> Itemized 12 Cost Components Valuation
                                                    </h6>

                                                    <!-- 12-Component Grid Tiles -->
                                                    <div class="row g-3">
                                                        <!-- 1. Paper -->
                                                        <div class="col-md-3">
                                                            <div class="cost-component-card">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <span class="small text-muted fw-bold"><i class="las la-scroll text-primary me-1"></i> Paper Stock</span>
                                                                    <span class="badge bg-light text-dark border">Material</span>
                                                                </div>
                                                                <h5 class="fw-bold mb-0" style="color: #0f172a;">₱{{ number_format($cost->paper_cost, 2) }}</h5>
                                                            </div>
                                                        </div>
                                                        <!-- 2. Ink -->
                                                        <div class="col-md-3">
                                                            <div class="cost-component-card">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <span class="small text-muted fw-bold"><i class="las la-tint text-info me-1"></i> Printing Ink</span>
                                                                    <span class="badge bg-light text-dark border">Material</span>
                                                                </div>
                                                                <h5 class="fw-bold mb-0" style="color: #0f172a;">₱{{ number_format($cost->ink_cost, 2) }}</h5>
                                                            </div>
                                                        </div>
                                                        <!-- 3. Labor -->
                                                        <div class="col-md-3">
                                                            <div class="cost-component-card">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <span class="small text-muted fw-bold"><i class="las la-user-cog text-warning me-1"></i> Direct Labor</span>
                                                                    <span class="badge bg-light text-dark border">Labor</span>
                                                                </div>
                                                                <h5 class="fw-bold mb-0" style="color: #0f172a;">₱{{ number_format($cost->labor_cost, 2) }}</h5>
                                                            </div>
                                                        </div>
                                                        <!-- 4. Electricity -->
                                                        <div class="col-md-3">
                                                            <div class="cost-component-card">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <span class="small text-muted fw-bold"><i class="las la-bolt text-danger me-1"></i> Electricity Power</span>
                                                                    <span class="badge bg-light text-dark border">Energy</span>
                                                                </div>
                                                                <h5 class="fw-bold mb-0" style="color: #0f172a;">₱{{ number_format($cost->electricity_cost, 2) }}</h5>
                                                            </div>
                                                        </div>

                                                        <!-- 5. Machine -->
                                                        <div class="col-md-3">
                                                            <div class="cost-component-card">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <span class="small text-muted fw-bold"><i class="las la-cogs text-secondary me-1"></i> Press Machine</span>
                                                                    <span class="badge bg-light text-dark border">Equipment</span>
                                                                </div>
                                                                <h5 class="fw-bold mb-0" style="color: #0f172a;">₱{{ number_format($cost->machine_cost, 2) }}</h5>
                                                            </div>
                                                        </div>
                                                        <!-- 6. Binding -->
                                                        <div class="col-md-3">
                                                            <div class="cost-component-card">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <span class="small text-muted fw-bold"><i class="las la-book-open text-primary me-1"></i> Binding Finishing</span>
                                                                    <span class="badge bg-light text-dark border">Finishing</span>
                                                                </div>
                                                                <h5 class="fw-bold mb-0" style="color: #0f172a;">₱{{ number_format($cost->binding_cost, 2) }}</h5>
                                                            </div>
                                                        </div>
                                                        <!-- 7. UV Coating -->
                                                        <div class="col-md-3">
                                                            <div class="cost-component-card">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <span class="small text-muted fw-bold"><i class="las la-sun text-warning me-1"></i> UV Lamination</span>
                                                                    <span class="badge bg-light text-dark border">Coating</span>
                                                                </div>
                                                                <h5 class="fw-bold mb-0" style="color: #0f172a;">₱{{ number_format($cost->uv_cost, 2) }}</h5>
                                                            </div>
                                                        </div>
                                                        <!-- 8. Shrink Wrap -->
                                                        <div class="col-md-3">
                                                            <div class="cost-component-card">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <span class="small text-muted fw-bold"><i class="las la-box-open text-success me-1"></i> Shrink Wrap</span>
                                                                    <span class="badge bg-light text-dark border">Packing</span>
                                                                </div>
                                                                <h5 class="fw-bold mb-0" style="color: #0f172a;">₱{{ number_format($cost->shrink_wrap_cost, 2) }}</h5>
                                                            </div>
                                                        </div>

                                                        <!-- 9. Packaging -->
                                                        <div class="col-md-3">
                                                            <div class="cost-component-card">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <span class="small text-muted fw-bold"><i class="las la-boxes text-info me-1"></i> Outer Packaging</span>
                                                                    <span class="badge bg-light text-dark border">Packing</span>
                                                                </div>
                                                                <h5 class="fw-bold mb-0" style="color: #0f172a;">₱{{ number_format($cost->packaging_cost, 2) }}</h5>
                                                            </div>
                                                        </div>
                                                        <!-- 10. Freight -->
                                                        <div class="col-md-3">
                                                            <div class="cost-component-card">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <span class="small text-muted fw-bold"><i class="las la-shipping-fast text-dark me-1"></i> Freight & Delivery</span>
                                                                    <span class="badge bg-light text-dark border">Logistics</span>
                                                                </div>
                                                                <h5 class="fw-bold mb-0" style="color: #0f172a;">₱{{ number_format($cost->freight_cost, 2) }}</h5>
                                                            </div>
                                                        </div>
                                                        <!-- 11. Warehouse -->
                                                        <div class="col-md-3">
                                                            <div class="cost-component-card">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <span class="small text-muted fw-bold"><i class="las la-warehouse text-muted me-1"></i> Warehouse Storage</span>
                                                                    <span class="badge bg-light text-dark border">Storage</span>
                                                                </div>
                                                                <h5 class="fw-bold mb-0" style="color: #0f172a;">₱{{ number_format($cost->warehouse_cost, 2) }}</h5>
                                                            </div>
                                                        </div>
                                                        <!-- 12. Factory Overhead -->
                                                        <div class="col-md-3">
                                                            <div class="cost-component-card">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <span class="small text-muted fw-bold"><i class="las la-building text-danger me-1"></i> Factory Overhead</span>
                                                                    <span class="badge bg-light text-dark border">Overhead</span>
                                                                </div>
                                                                <h5 class="fw-bold mb-0" style="color: #0f172a;">₱{{ number_format($cost->overhead_cost, 2) }}</h5>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Section 6: Definition list summary with dashed bottom borders -->
                                                    <div class="card mt-4 border-0 shadow-sm" style="border-radius: 8px;">
                                                        <div class="card-body p-3 bg-white" style="border-radius: 8px;">
                                                            <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                                                <span class="text-muted small"><i class="las la-layer-group me-1"></i> Direct Materials & Labor Subtotal</span>
                                                                <span class="fw-bold text-dark small">₱{{ number_format($directMaterials, 2) }}</span>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom: 1px dashed #e2e8f0 !important;">
                                                                <span class="text-muted small"><i class="las la-industry me-1"></i> Manufacturing & Factory Overhead Subtotal</span>
                                                                <span class="fw-bold text-dark small">₱{{ number_format($mfgOverhead, 2) }}</span>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-between py-2 pt-3">
                                                                <span class="fw-bold text-dark"><i class="las la-calculator me-1" style="color: #D9251C;"></i> Total Cost of Goods Sold (COGS)</span>
                                                                <span class="fw-bold fs-18 text-danger">₱{{ number_format($cost->total_cogs, 2) }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                                                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Close</button>
                                                    <a href="{{ route('admin-finance.accounting.production-costing.show', $cost->id) }}" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C; border-radius: 6px;">
                                                        <i class="las la-external-link-alt me-1"></i> Open Full Job Costing Sheet Page
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4" style="color: #475569 !important;">No production costings found. Click "Sync from Production ERP" above to import live costings!</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Section 4: Card Footer with Pagination & Entries Summary -->
                    <div class="card-footer bg-white border-top py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2" style="border-top: 1px solid #e2e8f0 !important; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
                        <span class="small" style="color: #475569 !important;">
                            Showing <span class="fw-bold" style="color: #0f172a !important;">{{ $costings->firstItem() ?? 0 }}</span> to <span class="fw-bold" style="color: #0f172a !important;">{{ $costings->lastItem() ?? 0 }}</span> of <span class="fw-bold" style="color: #0f172a !important;">{{ $costings->total() }}</span> production costings
                        </span>
                        <div id="paginationContainer" class="d-flex justify-content-end">
                            {{ $costings->onEachSide(0)->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--
    <!-- Section 7: Form Modals Design Guidelines -->
    <div class="modal fade" id="autoCalculateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('production.costing.calculate') }}" method="POST">
                    @csrf
                    <!-- Section 7: Clean white header (#ffffff) with bold pure black title (#000000) -->
                    <div class="modal-header bg-white border-bottom pt-3 pb-3">
                        <h5 class="modal-title fw-bold" style="color: #000000 !important;"><i class="las la-magic me-2" style="color: #D9251C;"></i>Auto-Calculate Production Costing</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="small mb-3" style="color: #475569 !important;">The calculation engine will automatically compute all 12 cost components (Paper, Ink, Labor, Electricity, Machine, Binding, UV, Shrink Wrap, Packaging, Freight, Warehouse, Overhead) and COGS from Production specifications.</p>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <!-- Section 7: Form Labels Dark Slate Gray (#475569), uppercase, 0.72rem, bold 600 -->
                                <label class="form-label fw-bold text-uppercase" style="color: #475569 !important; font-size: 0.72rem; letter-spacing: 0.5px;">Job / Book Title <span class="text-danger">*</span></label>
                                <input type="text" name="job_title" class="form-control" placeholder="e.g. Daily Gospel 2026 Edition" style="height: 38px; border-color: #cbd5e1; border-radius: 6px; color: #000000;" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-uppercase" style="color: #475569 !important; font-size: 0.72rem; letter-spacing: 0.5px;">Linked Catalog Book (Optional)</label>
                                <select name="book_id" class="form-select" style="height: 38px; border-color: #cbd5e1; border-radius: 6px; color: #000000;">
                                    <option value="">None / Custom Printing Job</option>
                                    @foreach($books as $bk)
                                    <option value="{{ $bk->id }}">{{ $bk->name }} ({{ $bk->sku }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-uppercase" style="color: #475569 !important; font-size: 0.72rem; letter-spacing: 0.5px;">Total Run Quantity (Copies) <span class="text-danger">*</span></label>
                                <input type="number" name="quantity_produced" class="form-control" value="1000" min="1" style="height: 38px; border-color: #cbd5e1; border-radius: 6px; color: #000000;" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-uppercase" style="color: #475569 !important; font-size: 0.72rem; letter-spacing: 0.5px;">Page Count <span class="text-danger">*</span></label>
                                <input type="number" name="pages_count" class="form-control" value="128" min="1" style="height: 38px; border-color: #cbd5e1; border-radius: 6px; color: #000000;" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-uppercase" style="color: #475569 !important; font-size: 0.72rem; letter-spacing: 0.5px;">Binding Style</label>
                                <select name="binding_type" class="form-select" style="height: 38px; border-color: #cbd5e1; border-radius: 6px; color: #000000;">
                                    <option value="Perfect Binding">Perfect Binding (Standard)</option>
                                    <option value="Hardbound">Hardbound / Case Bound</option>
                                    <option value="Saddle Stitch">Saddle Stitch (Booklet)</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-center gap-4 pt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="has_uv" value="1" id="uvCheck" checked>
                                    <label class="form-check-label fw-bold small" for="uvCheck" style="color: #475569;">UV Lamination Coating</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="has_shrink_wrap" value="1" id="shrinkCheck" checked>
                                    <label class="form-check-label fw-bold small" for="shrinkCheck" style="color: #475569;">Individual Shrink Wrap</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top pt-3 pb-3">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="height: 38px; border-radius: 6px;">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C; height: 38px; border-radius: 6px;">Run Automated Calculation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    --}}
</x-app-layout>
