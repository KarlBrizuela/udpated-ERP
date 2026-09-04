<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .sheet-card {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .cost-row {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s ease;
        }

        .cost-row:hover {
            background-color: #f8fafc;
        }

        .progress-cost {
            height: 8px;
            border-radius: 4px;
        }
    </style>
    @endpush

    <div class="container-fluid">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="las la-check-circle me-2 fs-18"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('production.costing.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                        <i class="las la-arrow-left me-1"></i> Back to Costing Ledger
                    </a>
                    <h4 class="fs-24 fw-bold text-dark mb-0">{{ $costing->job_title }}</h4>
                    <p class="text-muted small mb-0">Official Production Costing Sheet & Itemized COGS Calculation</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm px-3" onclick="window.print()">
                        <i class="las la-print me-1"></i> Print Costing Sheet
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left: Cost Summary & Specs -->
            <div class="col-md-4 mb-4">
                <div class="sheet-card mb-4">
                    <h6 class="fw-bold text-uppercase text-muted small mb-3">Job Specifications</h6>
                    <div class="mb-3">
                        <span class="text-muted small d-block">Job Reference No</span>
                        <span class="fw-bold font-monospace text-dark fs-16">{{ $costing->job_number }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted small d-block">Run Quantity Produced</span>
                        <span class="fw-bold text-dark fs-18">{{ number_format($costing->quantity_produced) }} copies</span>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted small d-block">Page Count</span>
                        <span class="fw-bold text-dark fs-16">{{ number_format($costing->pages_count) }} pages</span>
                    </div>
                    <hr>
                    <div class="p-3 rounded text-white mb-3" style="background-color: #D9251C;">
                        <span class="small text-white-50 d-block text-uppercase fw-bold">Total Job COGS</span>
                        <h3 class="fw-bold mb-0">₱{{ number_format($costing->total_cogs, 2) }}</h3>
                    </div>
                    <div class="p-3 rounded text-white" style="background-color: #e53935;">
                        <span class="small text-white-50 d-block text-uppercase fw-bold">Unit COGS Rate</span>
                        <h3 class="fw-bold mb-0">₱{{ number_format($costing->unit_cogs, 2) }} <span class="fs-14 fw-normal">/ unit</span></h3>
                    </div>
                </div>
            </div>

            <!-- Right: Itemized 12-Component Breakdown -->
            <div class="col-md-8 mb-4">
                <div class="sheet-card">
                    <h5 class="fw-bold text-dark mb-1">Itemized 12-Component Cost Breakdown</h5>
                    <p class="text-muted small mb-4">Automatically calculated from Production module parameters</p>

                    @php
                        $tot = max(1, $costing->total_cogs);
                        $components = [
                            ['name' => '1. Paper', 'val' => $costing->paper_cost, 'icon' => 'las la-scroll', 'color' => 'bg-danger'],
                            ['name' => '2. Ink', 'val' => $costing->ink_cost, 'icon' => 'las la-fill-drip', 'color' => 'bg-info'],
                            ['name' => '3. Labor', 'val' => $costing->labor_cost, 'icon' => 'las la-user-cog', 'color' => 'bg-danger'],
                            ['name' => '4. Electricity', 'val' => $costing->electricity_cost, 'icon' => 'las la-bolt', 'color' => 'bg-warning'],
                            ['name' => '5. Machine Cost', 'val' => $costing->machine_cost, 'icon' => 'las la-industry', 'color' => 'bg-secondary'],
                            ['name' => '6. Binding', 'val' => $costing->binding_cost, 'icon' => 'las la-book-open', 'color' => 'bg-dark'],
                            ['name' => '7. UV Coating', 'val' => $costing->uv_cost, 'icon' => 'las la-sun', 'color' => 'bg-danger'],
                            ['name' => '8. Shrink Wrap', 'val' => $costing->shrink_wrap_cost, 'icon' => 'las la-box-open', 'color' => 'bg-danger'],
                            ['name' => '9. Packaging', 'val' => $costing->packaging_cost, 'icon' => 'las la-boxes', 'color' => 'bg-primary'],
                            ['name' => '10. Freight Allocation', 'val' => $costing->freight_cost, 'icon' => 'las la-truck', 'color' => 'bg-info'],
                            ['name' => '11. Warehouse Handling', 'val' => $costing->warehouse_cost, 'icon' => 'las la-warehouse', 'color' => 'bg-danger'],
                            ['name' => '12. Factory Overhead', 'val' => $costing->overhead_cost, 'icon' => 'las la-cogs', 'color' => 'bg-warning'],
                        ];
                    @endphp

                    @foreach($components as $c)
                    @php
                        $pct = round(($c['val'] / $tot) * 100, 1);
                        $unitVal = round($c['val'] / max(1, $costing->quantity_produced), 2);
                    @endphp
                    <div class="cost-row">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center gap-2">
                                <i class="{{ $c['icon'] }} fs-20 text-muted"></i>
                                <span class="fw-bold text-dark">{{ $c['name'] }}</span>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold text-dark fs-16">₱{{ number_format($c['val'], 2) }}</span>
                                <span class="text-muted small ms-2">(₱{{ number_format($unitVal, 2) }}/unit)</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="progress flex-grow-1 progress-cost">
                                <div class="progress-bar {{ $c['color'] }}" role="progressbar" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="small font-monospace text-muted fw-bold" style="width: 50px; text-align: right;">{{ $pct }}%</span>
                        </div>
                    </div>
                    @endforeach

                    <div class="p-3 bg-light rounded mt-4 d-flex justify-content-between align-items-center border">
                        <span class="fw-bold text-dark fs-16">TOTAL COST OF GOODS SOLD (COGS)</span>
                        <span class="fw-bold fs-20" style="color: #D9251C;">₱{{ number_format($costing->total_cogs, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
