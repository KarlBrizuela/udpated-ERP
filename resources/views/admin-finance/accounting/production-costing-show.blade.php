<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        /* Section 2: Page Layout & Grid Width Expansion (Claretian ERP Guidelines) */
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
            padding-bottom: 40px !important;
        }

        .compact-header-card {
            background: #ffffff;
            border-radius: 10px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            margin-bottom: 1.25rem;
        }

        .compact-tile {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
            transition: all 0.15s ease-in-out;
        }

        .compact-tile:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
            background-color: #f8fafc;
        }

        .progress-slim {
            height: 5px;
            border-radius: 3px;
            background-color: #e2e8f0;
        }
    </style>
    @endpush

    <!-- Main Container Wrapper -->
    <div class="container-fluid p-0">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
            <i class="las la-check-circle me-2 fs-18"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Master Title Header Card (Compact Header) -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="compact-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('admin-finance.accounting.production-costing') }}" class="btn btn-outline-secondary btn-sm px-3 d-inline-flex align-items-center gap-1" style="height: 38px; border-radius: 6px; font-weight: 600;">
                            <i class="las la-arrow-left fs-16"></i> Back
                        </a>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-light text-dark border px-2 py-1 font-monospace" style="font-size: 0.76rem;">{{ $costing->job_number }}</span>
                                <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.76rem;">
                                    <i class="las la-barcode me-1"></i> {{ $costing->book ? $costing->book->sku : 'Custom Job' }}
                                </span>
                            </div>
                            <h6 class="fw-bold mb-0" style="color: #0f172a !important; font-size: 0.95rem; line-height: 1.35; max-width: 850px;">{{ $costing->job_title }}</h6>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2" style="flex-shrink: 0;">
                        <button class="btn btn-outline-secondary btn-sm px-3 d-inline-flex align-items-center gap-2" style="height: 38px; border-radius: 6px; font-weight: 600;" onclick="window.print()">
                            <i class="las la-print fs-18"></i> Print Sheet
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Summary Cards Strip (Top-Down Layout per Section 14) -->
        <div class="row mb-3" style="align-items: flex-start;">
            <div class="col-md-3">
                <div class="card shadow-sm" style="border-radius: 8px; border: 1px solid #e2e8f0; background-color: #ffffff;">
                    <div class="card-body p-3">
                        <span class="d-block text-uppercase fw-bold mb-1" style="color: #475569 !important; font-size: 0.68rem; letter-spacing: 0.5px;">TOTAL JOB PRODUCTION COGS</span>
                        <h4 class="fw-bold mb-0" style="color: #D9251C !important; font-size: 1.35rem;">₱{{ number_format($costing->total_cogs, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm" style="border-radius: 8px; border: 1px solid #e2e8f0; background-color: #ffffff;">
                    <div class="card-body p-3">
                        <span class="d-block text-uppercase fw-bold mb-1" style="color: #475569 !important; font-size: 0.68rem; letter-spacing: 0.5px;">UNIT COGS RATE</span>
                        <h4 class="fw-bold mb-0" style="color: #0ea5e9 !important; font-size: 1.35rem;">₱{{ number_format($costing->unit_cogs, 2) }} <span class="fs-12 text-muted fw-normal">/ copy</span></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm" style="border-radius: 8px; border: 1px solid #e2e8f0; background-color: #ffffff;">
                    <div class="card-body p-3">
                        <span class="d-block text-uppercase fw-bold mb-1" style="color: #475569 !important; font-size: 0.68rem; letter-spacing: 0.5px;">RUN QUANTITY PRODUCED</span>
                        <h4 class="fw-bold mb-0" style="color: #0f172a !important; font-size: 1.35rem;">{{ number_format($costing->quantity_produced) }} <span class="fs-12 text-muted fw-normal">Copies</span></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm" style="border-radius: 8px; border: 1px solid #e2e8f0; background-color: #ffffff;">
                    <div class="card-body p-3">
                        <span class="d-block text-uppercase fw-bold mb-1" style="color: #475569 !important; font-size: 0.68rem; letter-spacing: 0.5px;">TOTAL PAGE COUNT</span>
                        <h4 class="fw-bold mb-0" style="color: #0f172a !important; font-size: 1.35rem;">{{ number_format($costing->pages_count) }} <span class="fs-12 text-muted fw-normal">Pages</span></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- 12-Component Cost Breakdown Grid Card (2-Column Compact Layout for Zero Scrolling) -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-3" style="border-radius: 10px; border: 1px solid #e2e8f0; background-color: #ffffff;">
                    <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #e2e8f0 !important;">
                        <div>
                            <h6 class="mb-0 fw-bold" style="color: #0f172a !important; font-size: 0.95rem;">
                                <i class="las la-layer-group me-1" style="color: #D9251C;"></i> Itemized 12 Cost Components Valuation
                            </h6>
                            <p class="small mb-0" style="color: #475569 !important; font-size: 0.78rem;">Calculated directly from Production module parameters</p>
                        </div>
                        <span class="badge bg-light text-secondary border px-3 py-1 font-monospace" style="font-size: 0.76rem;">12 Components</span>
                    </div>

                    <div class="card-body p-3">
                        @php
                            $tot = max(1, $costing->total_cogs);
                            $components = [
                                ['name' => '1. Paper Stock', 'val' => $costing->paper_cost, 'icon' => 'las la-scroll', 'color' => '#D9251C', 'cat' => 'Material'],
                                ['name' => '2. Printing Ink', 'val' => $costing->ink_cost, 'icon' => 'las la-tint', 'color' => '#0ea5e9', 'cat' => 'Material'],
                                ['name' => '3. Direct Labor', 'val' => $costing->labor_cost, 'icon' => 'las la-user-cog', 'color' => '#f59e0b', 'cat' => 'Labor'],
                                ['name' => '4. Electricity Power', 'val' => $costing->electricity_cost, 'icon' => 'las la-bolt', 'color' => '#ef4444', 'cat' => 'Energy'],
                                ['name' => '5. Press Machine', 'val' => $costing->machine_cost, 'icon' => 'las la-industry', 'color' => '#64748b', 'cat' => 'Equipment'],
                                ['name' => '6. Binding & Finishing', 'val' => $costing->binding_cost, 'icon' => 'las la-book-open', 'color' => '#0f172a', 'cat' => 'Finishing'],
                                ['name' => '7. UV Lamination', 'val' => $costing->uv_cost, 'icon' => 'las la-sun', 'color' => '#f59e0b', 'cat' => 'Coating'],
                                ['name' => '8. Shrink Wrap', 'val' => $costing->shrink_wrap_cost, 'icon' => 'las la-box-open', 'color' => '#10b981', 'cat' => 'Packing'],
                                ['name' => '9. Outer Packaging', 'val' => $costing->packaging_cost, 'icon' => 'las la-boxes', 'color' => '#3b82f6', 'cat' => 'Packing'],
                                ['name' => '10. Freight Delivery', 'val' => $costing->freight_cost, 'icon' => 'las la-shipping-fast', 'color' => '#6366f1', 'cat' => 'Logistics'],
                                ['name' => '11. Warehouse Handling', 'val' => $costing->warehouse_cost, 'icon' => 'las la-warehouse', 'color' => '#8b5cf6', 'cat' => 'Storage'],
                                ['name' => '12. Factory Overhead', 'val' => $costing->overhead_cost, 'icon' => 'las la-building', 'color' => '#ec4899', 'cat' => 'Overhead'],
                            ];
                        @endphp

                        <!-- Compact 2-Column Grid (6 items per column, fits on screen) -->
                        <div class="row g-2">
                            @foreach($components as $c)
                            @php
                                $pct = round(($c['val'] / $tot) * 100, 1);
                                $unitVal = round($c['val'] / max(1, $costing->quantity_produced), 2);
                            @endphp
                            <div class="col-md-6">
                                <div class="compact-tile">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="{{ $c['icon'] }} fs-16" style="color: {{ $c['color'] }};"></i>
                                            <span class="fw-bold" style="color: #0f172a !important; font-size: 0.82rem;">{{ $c['name'] }}</span>
                                        </div>
                                        <div class="text-end">
                                            <span class="fw-bold" style="color: #0f172a !important; font-size: 0.88rem;">₱{{ number_format($c['val'], 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-1" style="font-size: 0.74rem; color: #475569;">
                                        <span>Rate: ₱{{ number_format($unitVal, 2) }} / unit</span>
                                        <span class="fw-bold font-monospace">{{ $pct }}%</span>
                                    </div>
                                    <div class="progress progress-slim">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $pct }}%; background-color: {{ $c['color'] }};"></div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Clean Summary Footer Strip -->
                        @php
                            $directMaterials = $costing->paper_cost + $costing->ink_cost + $costing->labor_cost + $costing->electricity_cost;
                            $mfgOverhead = $costing->machine_cost + $costing->binding_cost + $costing->uv_cost + $costing->shrink_wrap_cost + $costing->packaging_cost + $costing->freight_cost + $costing->warehouse_cost + $costing->overhead_cost;
                        @endphp
                        <div class="p-3 bg-light rounded mt-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 border" style="border-radius: 8px !important;">
                            <div class="d-flex align-items-center gap-4">
                                <span class="small" style="color: #475569 !important;">
                                    Direct Materials & Labor: <strong style="color: #0f172a !important;">₱{{ number_format($directMaterials, 2) }}</strong>
                                </span>
                                <span class="small" style="color: #475569 !important;">
                                    Manufacturing & Overhead: <strong style="color: #0f172a !important;">₱{{ number_format($mfgOverhead, 2) }}</strong>
                                </span>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold text-uppercase me-2" style="color: #0f172a !important; font-size: 0.85rem;">Total Cost of Goods Sold (COGS):</span>
                                <span class="fw-bold fs-18" style="color: #D9251C;">₱{{ number_format($costing->total_cogs, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
