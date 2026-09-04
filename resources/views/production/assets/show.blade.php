<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .asset-card {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .maint-row {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .maint-row:hover {
            background-color: #f8fafc;
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
                    <a href="{{ route('production.assets.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                        <i class="las la-arrow-left me-1"></i> Back to Asset Register
                    </a>
                    <h4 class="fs-24 fw-bold text-dark mb-0">{{ $asset->name }}</h4>
                    <p class="text-muted small mb-0">Code: <span class="font-monospace fw-bold text-dark">{{ $asset->asset_code }}</span> | Category: <span class="badge bg-light text-dark border">{{ $asset->category }}</span></p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-danger btn-sm px-3 text-white rounded shadow-sm d-flex align-items-center gap-2" style="background-color: #D9251C; border-color: #D9251C; height: 40px;" data-bs-toggle="modal" data-bs-target="#logMaintenanceModal">
                        <i class="las la-plus-circle fs-18"></i> Log Maintenance / Repair
                    </button>
                    <button class="btn btn-outline-secondary btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="height: 40px;" onclick="window.print()">
                        <i class="las la-print fs-18"></i> Print Profile
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left: Asset Valuation & Specifications -->
            <div class="col-md-5 mb-4">
                <div class="asset-card mb-4">
                    <h6 class="fw-bold text-uppercase text-muted small mb-3">Asset Financial & Spec Summary</h6>
                    
                    <div class="p-3 rounded text-white mb-3" style="background-color: #D9251C;">
                        <span class="small text-white-50 d-block text-uppercase fw-bold">Current Net Book Value</span>
                        <h3 class="fw-bold mb-0">₱{{ number_format($asset->current_value, 2) }}</h3>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light">
                                <span class="text-muted small d-block">Original Cost</span>
                                <span class="fw-bold text-dark fs-16">₱{{ number_format($asset->purchase_price, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light">
                                <span class="text-muted small d-block">Accum. Depreciation</span>
                                <span class="fw-bold text-warning fs-16">₱{{ number_format($asset->accumulated_depreciation, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light">
                                <span class="text-muted small d-block">Total Repair Costs</span>
                                <span class="fw-bold fs-16" style="color: #D9251C;">₱{{ number_format($asset->total_repair_cost, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light">
                                <span class="text-muted small d-block">Useful Life</span>
                                <span class="fw-bold text-dark fs-16">{{ $asset->useful_life_years }} Years</span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="fw-bold text-dark small mb-3">Machine Details & Warranty</h6>
                    <div class="mb-2">
                        <span class="text-muted small d-block">Serial Number:</span>
                        <span class="fw-bold font-monospace text-dark">{{ $asset->serial_number ?: 'N/A' }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted small d-block">Purchase Date & Supplier:</span>
                        <span class="fw-bold text-dark">{{ $asset->purchase_date ? $asset->purchase_date->format('F d, Y') : 'N/A' }}</span>
                        <span class="text-muted small">({{ $asset->supplier ?: 'Direct Vendor' }})</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted small d-block">Warranty Status:</span>
                        @if($asset->warranty_expiry)
                            @if($asset->warranty_expiry->isFuture())
                            <span class="badge bg-success-subtle text-success">Active until {{ $asset->warranty_expiry->format('M d, Y') }}</span>
                            @else
                            <span class="badge bg-danger-subtle text-danger">Expired on {{ $asset->warranty_expiry->format('M d, Y') }}</span>
                            @endif
                        @else
                        <span class="text-muted small">No warranty logged</span>
                        @endif
                    </div>
                    <div class="mb-2">
                        <span class="text-muted small d-block">Facility Location:</span>
                        <span class="fw-bold text-dark">{{ $asset->location }}</span>
                    </div>
                </div>
            </div>

            <!-- Right: Maintenance & Repair History Log -->
            <div class="col-md-7 mb-4">
                <div class="asset-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Maintenance & Repair History Log</h5>
                            <p class="text-muted small mb-0">Cumulative maintenance records and service expenses</p>
                        </div>
                        <span class="badge text-white px-3 py-2 fs-14" style="background-color: #D9251C;">Total Repairs: ₱{{ number_format($asset->total_repair_cost, 2) }}</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-dark text-white small text-uppercase">
                                <tr>
                                    <th>Date</th>
                                    <th>Service / Repair Title</th>
                                    <th>Technician</th>
                                    <th class="text-end text-warning">Repair Cost</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($asset->maintenanceLogs as $log)
                                <tr>
                                    <td><span class="fw-bold text-dark small">{{ $log->maintenance_date ? $log->maintenance_date->format('M d, Y') : 'N/A' }}</span></td>
                                    <td><span class="fw-bold text-dark fs-14">{{ $log->title }}</span></td>
                                    <td><span class="text-muted small">{{ $log->technician ?: 'In-house Staff' }}</span></td>
                                    <td class="text-end fw-bold" style="color: #D9251C;">₱{{ number_format($log->repair_cost, 2) }}</td>
                                    <td><span class="text-muted small">{{ $log->details ?: 'Routine service' }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No maintenance or repair history recorded yet. Click "Log Maintenance / Repair" above to log service activities.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: LOG MAINTENANCE / REPAIR -->
    <div class="modal fade" id="logMaintenanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('production.assets.maintenance.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="production_fixed_asset_id" value="{{ $asset->id }}">
                    <div class="modal-header text-white" style="background-color: #D9251C;">
                        <h5 class="modal-title fw-bold"><i class="las la-tools me-2"></i>Log Maintenance or Repair</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Service / Repair Date <span class="text-danger">*</span></label>
                            <input type="date" name="maintenance_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Activity / Repair Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Roller replacement, Calibration, Oil change" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Technician / Service Vendor</label>
                            <input type="text" name="technician" class="form-control" placeholder="e.g. Engr. Santos / Heidelberg Service">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Repair / Maintenance Cost (₱) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="repair_cost" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Work Details & Replaced Parts</label>
                            <textarea name="details" class="form-control" rows="2" placeholder="Replaced upper roller gear, tested pressure alignment..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Save Repair Log</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
