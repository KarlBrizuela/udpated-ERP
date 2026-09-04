<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .cost-header-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.75rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 0;
            margin-bottom: 1.5rem;
        }

        .hover-row {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-row:hover {
            background-color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
        }

        .cogs-badge {
            font-family: monospace;
            font-size: 0.95rem;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 6px;
            background-color: rgba(217, 37, 28, 0.1);
            color: #D9251C;
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
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="las la-exclamation-circle me-2 fs-18"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Master Title Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="cost-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fs-24 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Automated Production Costing</h4>
                        <p class="text-muted small mb-0">Automatic cost aggregation directly from Production module parameters. No manual accounting entry required.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <form action="{{ route('production.costing.sync') }}" method="POST" class="d-inline" onsubmit="const btn = this.querySelector('button'); btn.disabled = true; btn.innerHTML = '<i class=\'las la-spinner la-spin fs-18\'></i> Syncing...';">
                            @csrf
                            <button type="submit" class="btn text-white btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="background-color: #0d6efd; border-color: #0d6efd; height: 40px;" title="Pull latest costing snapshots from http://erpccfi.claretianpublications.ph">
                                <i class="las la-sync fs-18"></i> Sync from Production ERP
                            </button>
                        </form>
                        <button class="btn text-white btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="background-color: #D9251C; border-color: #D9251C; height: 40px;" data-bs-toggle="modal" data-bs-target="#autoCalculateModal">
                            <i class="las la-magic fs-18"></i> Auto-Calculate Costing
                        </button>
                        <button class="btn btn-outline-secondary btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="height: 40px;" onclick="window.print()">
                            <i class="las la-print fs-18"></i> Print Costing Summary
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric summary cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; color: #D9251C;">
                            <i class="las la-calculator fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Production COGS</span>
                            <h4 class="fw-bold text-dark mb-0">₱{{ number_format($metrics['total_cogs'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; color: #D9251C;">
                            <i class="las la-tag fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Average Unit COGS Rate</span>
                            <h4 class="fw-bold mb-0" style="color: #D9251C;">₱{{ number_format($metrics['avg_unit_cogs'], 2) }} / unit</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; color: #e53935;">
                            <i class="las la-book fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Units Produced</span>
                            <h4 class="fw-bold text-dark mb-0">{{ number_format($metrics['total_qty_produced']) }} Copies</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-secondary" style="width: 50px; height: 50px;">
                            <i class="las la-tasks fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Costed Production Jobs</span>
                            <h4 class="fw-bold text-secondary mb-0">{{ $metrics['active_jobs_count'] }} Jobs</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Master Production Costing Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark fs-18">Automated Production Costing Ledger</h5>
                            <p class="text-muted small mb-0">Itemized 12-component cost breakdown & COGS valuation per job</p>
                        </div>
                        <form action="{{ route('production.costing.index') }}" method="GET" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search Job or Title..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-sm text-white px-3" style="background-color: #D9251C; border-color: #D9251C;">Search</button>
                        </form>
                    </div>

                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark text-white small text-uppercase">
                                    <tr>
                                        <th>Job No</th>
                                        <th>Title / Book</th>
                                        <th class="text-end">Run Qty</th>
                                        <th class="text-end">Pages</th>
                                        <th class="text-end" title="Paper Cost">Paper</th>
                                        <th class="text-end" title="Ink Cost">Ink</th>
                                        <th class="text-end" title="Labor Cost">Labor</th>
                                        <th class="text-end" title="Electricity Cost">Power</th>
                                        <th class="text-end" title="Machine Cost">Machine</th>
                                        <th class="text-end" title="Binding Cost">Binding</th>
                                        <th class="text-end" title="UV Coating">UV</th>
                                        <th class="text-end" title="Shrink Wrap">Shrink</th>
                                        <th class="text-end" title="Packaging">Pack</th>
                                        <th class="text-end" title="Freight Allocation">Freight</th>
                                        <th class="text-end" title="Warehouse Allocation">WH</th>
                                        <th class="text-end" title="Factory Overhead">Overhead</th>
                                        <th class="text-end" style="background-color: #D9251C;">Total COGS</th>
                                        <th class="text-end" style="background-color: #e53935;">Unit COGS</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($costings as $cost)
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark font-monospace">{{ $cost->job_number }}</span></td>
                                        <td>
                                            <span class="fw-bold text-dark d-block fs-14">{{ $cost->job_title }}</span>
                                            <span class="text-muted small">{{ $cost->book ? $cost->book->sku : 'Custom Job' }}</span>
                                        </td>
                                        <td class="text-end fw-bold text-dark">{{ number_format($cost->quantity_produced) }}</td>
                                        <td class="text-end text-muted">{{ number_format($cost->pages_count) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($cost->paper_cost, 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($cost->ink_cost, 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($cost->labor_cost, 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($cost->electricity_cost, 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($cost->machine_cost, 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($cost->binding_cost, 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($cost->uv_cost, 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($cost->shrink_wrap_cost, 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($cost->packaging_cost, 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($cost->freight_cost, 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($cost->warehouse_cost, 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($cost->overhead_cost, 2) }}</td>
                                        <td class="text-end fw-bold text-white" style="background-color: #D9251C;">
                                            ₱{{ number_format($cost->total_cogs, 2) }}
                                        </td>
                                        <td class="text-end fw-bold text-white" style="background-color: #e53935;">
                                            ₱{{ number_format($cost->unit_cogs, 2) }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('production.costing.show', $cost->id) }}" class="btn btn-sm btn-outline-danger px-2 py-1" style="color: #D9251C; border-color: #D9251C;">
                                                <i class="las la-eye"></i> View Sheet
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="19" class="text-center py-4 text-muted">No production costings generated yet. Click "Auto-Calculate Costing" above to generate your first job costing!</td>
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

    <!-- MODAL: AUTO-CALCULATE PRODUCTION COSTING -->
    <div class="modal fade" id="autoCalculateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('production.costing.calculate') }}" method="POST">
                    @csrf
                    <div class="modal-header text-white" style="background-color: #D9251C;">
                        <h5 class="modal-title fw-bold"><i class="las la-magic me-2"></i>Auto-Calculate Production Costing</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-3">The calculation engine will automatically calculate all 12 cost components (Paper, Ink, Labor, Electricity, Machine, Binding, UV, Shrink Wrap, Packaging, Freight, Warehouse, Overhead) and COGS from Production specifications.</p>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold small text-muted">Job / Book Title <span class="text-danger">*</span></label>
                                <input type="text" name="job_title" class="form-control" placeholder="e.g. Daily Gospel 2026 Edition" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Linked Catalog Book (Optional)</label>
                                <select name="book_id" class="form-select">
                                    <option value="">None / Custom Printing Job</option>
                                    @foreach($books as $bk)
                                    <option value="{{ $bk->id }}">{{ $bk->name }} ({{ $bk->sku }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Total Run Quantity (Copies) <span class="text-danger">*</span></label>
                                <input type="number" name="quantity_produced" class="form-control" value="1000" min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Page Count <span class="text-danger">*</span></label>
                                <input type="number" name="pages_count" class="form-control" value="128" min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Binding Style</label>
                                <select name="binding_type" class="form-select">
                                    <option value="Perfect Binding">Perfect Binding (Standard)</option>
                                    <option value="Hardbound">Hardbound / Case Bound</option>
                                    <option value="Saddle Stitch">Saddle Stitch (Booklet)</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-center gap-4 pt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="has_uv" value="1" id="uvCheck" checked>
                                    <label class="form-check-label fw-bold small" for="uvCheck">UV Lamination Coating</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="has_shrink_wrap" value="1" id="shrinkCheck" checked>
                                    <label class="form-check-label fw-bold small" for="shrinkCheck">Individual Shrink Wrap</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Run Automated Calculation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
