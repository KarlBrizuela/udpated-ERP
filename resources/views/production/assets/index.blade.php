<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .asset-header-card {
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

        .category-pill {
            font-size: 0.82rem;
            font-weight: 600;
            padding: 7px 15px;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            display: inline-block;
        }

        .category-pill.active {
            background-color: #D9251C;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(217, 37, 28, 0.25);
        }

        .category-pill:not(.active) {
            background-color: #f8f9fa;
            color: #495057;
            border: 1px solid #e9ecef;
        }

        .category-pill:not(.active):hover {
            background-color: #e9ecef;
            color: #212529;
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

        <!-- Master Title Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="asset-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fs-24 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Production Fixed Assets</h4>
                        <p class="text-muted small mb-0">Track all production machinery (Digital Press, RISO, Vehicles, Computers, Furniture, Buildings), warranties, depreciation, and repair history.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-danger btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2 text-white" style="background-color: #D9251C; border-color: #D9251C; height: 40px;" data-bs-toggle="modal" data-bs-target="#addAssetModal">
                            <i class="las la-plus-circle fs-18"></i> Register Machine / Asset
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
                            <i class="las la-coins fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Net Book Value</span>
                            <h4 class="fw-bold text-dark mb-0">₱{{ number_format($metrics['total_net_book_value'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-primary" style="width: 50px; height: 50px;">
                            <i class="las la-file-invoice-dollar fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Original Purchase Cost</span>
                            <h4 class="fw-bold text-primary mb-0">₱{{ number_format($metrics['total_original_value'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-warning" style="width: 50px; height: 50px;">
                            <i class="las la-chart-line fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Accumulated Depreciation</span>
                            <h4 class="fw-bold text-warning mb-0">₱{{ number_format($metrics['total_accumulated_depreciation'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; color: #D9251C;">
                            <i class="las la-tools fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Repair Costs</span>
                            <h4 class="fw-bold mb-0" style="color: #D9251C;">₱{{ number_format($metrics['total_repair_cost'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Master Fixed Assets Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark fs-18">Fixed Asset Register</h5>
                            <p class="text-muted small mb-0">Production machinery, equipment inventory, and asset valuation</p>
                        </div>
                        <form action="{{ route('production.assets.index') }}" method="GET" class="d-flex gap-2 align-items-center">
                            <select name="category" class="form-select form-select-sm border-light-subtle rounded-pill px-3" style="width: 160px; font-weight: 500;">
                                <option value="All" {{ $selectedCategory == 'All' ? 'selected' : '' }}>All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ $selectedCategory == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="search" class="form-control form-control-sm border-light-subtle rounded-pill px-3" placeholder="Search Code, Name..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-sm text-white px-3 rounded-pill" style="background-color: #D9251C; border-color: #D9251C; font-weight: 600;">Filter</button>
                        </form>
                    </div>

                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark text-white small text-uppercase">
                                    <tr>
                                        <th>Asset Code</th>
                                        <th>Machine / Asset Name</th>
                                        <th>Category</th>
                                        <th class="text-end">Purchase Price</th>
                                        <th class="text-end">Current Book Value</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assets as $ast)
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark font-monospace">{{ $ast->asset_code }}</span></td>
                                        <td>
                                            <span class="fw-bold text-dark d-block fs-14">{{ $ast->name }}</span>
                                            <span class="text-muted small">{{ $ast->location ?: 'Main Facility' }}</span>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $ast->category }}</span></td>
                                        <td class="text-end fw-bold text-dark">₱{{ number_format($ast->purchase_price, 2) }}</td>
                                        <td class="text-end fw-bold text-danger">₱{{ number_format($ast->current_value, 2) }}</td>
                                        <td class="text-center">
                                            @if($ast->status === 'Operational')
                                            <span class="badge bg-success text-white px-3 py-1 rounded-pill">Operational</span>
                                            @elseif($ast->status === 'Under Maintenance')
                                            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill">Maintenance</span>
                                            @else
                                            <span class="badge bg-secondary text-white px-3 py-1 rounded-pill">{{ $ast->status }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('production.assets.show', $ast->id) }}" class="btn btn-sm btn-outline-primary px-2 py-1" title="View Profile">
                                                    <i class="las la-eye"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-warning px-2 py-1" data-bs-toggle="modal" data-bs-target="#editAssetModal_{{ $ast->id }}" title="Edit Asset">
                                                    <i class="las la-edit"></i>
                                                </button>
                                                <form action="{{ route('production.assets.destroy', $ast->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete asset {{ $ast->name }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Delete Asset">
                                                        <i class="las la-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- EDIT MODAL -->
                                    <div class="modal fade" id="editAssetModal_{{ $ast->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content border-0 shadow">
                                                <form action="{{ route('production.assets.update', $ast->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header text-white" style="background-color: #D9251C;">
                                                        <h5 class="modal-title fw-bold"><i class="las la-edit me-2"></i>Edit Machine / Fixed Asset</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="row g-3">
                                                            <div class="col-md-8">
                                                                <label class="form-label fw-bold small text-muted">Machine / Asset Name <span class="text-danger">*</span></label>
                                                                <input type="text" name="name" class="form-control" value="{{ $ast->name }}" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label fw-bold small text-muted">Asset Category <span class="text-danger">*</span></label>
                                                                <select name="category" class="form-select" required>
                                                                    @foreach($categories as $cat)
                                                                    <option value="{{ $cat }}" {{ $ast->category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label fw-bold small text-muted">Purchase Date <span class="text-danger">*</span></label>
                                                                <input type="date" name="purchase_date" class="form-control" value="{{ $ast->purchase_date ? $ast->purchase_date->format('Y-m-d') : '' }}" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label fw-bold small text-muted">Purchase Price (₱) <span class="text-danger">*</span></label>
                                                                <input type="number" step="0.01" name="purchase_price" class="form-control" value="{{ $ast->purchase_price }}" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label fw-bold small text-muted">Status <span class="text-danger">*</span></label>
                                                                <select name="status" class="form-select" required>
                                                                    <option value="Operational" {{ $ast->status === 'Operational' ? 'selected' : '' }}>Operational</option>
                                                                    <option value="Under Maintenance" {{ $ast->status === 'Under Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                                                    <option value="Decommissioned" {{ $ast->status === 'Decommissioned' ? 'selected' : '' }}>Decommissioned</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-bold small text-muted">Supplier Name</label>
                                                                <input type="text" name="supplier" class="form-control" value="{{ $ast->supplier }}">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-bold small text-muted">Serial Number</label>
                                                                <input type="text" name="serial_number" class="form-control" value="{{ $ast->serial_number }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Update Asset</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No fixed assets registered yet. Click "Register Machine / Asset" above to add your first machinery or property.</td>
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

    <!-- MODAL: REGISTER MACHINE / ASSET -->
    <div class="modal fade" id="addAssetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('production.assets.store') }}" method="POST">
                    @csrf
                    <div class="modal-header text-white" style="background-color: #D9251C;">
                        <h5 class="modal-title fw-bold"><i class="las la-tools me-2"></i>Register New Machine / Fixed Asset</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold small text-muted">Machine / Asset Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Digital Press, Printing Machine, Delivery Van" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Asset Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Purchase Price (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="purchase_price" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Asset Location / Facility</label>
                                <input type="text" name="location" class="form-control" value="Main Production Facility">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Supplier Name / Source</label>
                                <input type="text" name="supplier" class="form-control" placeholder="e.g. Heidelberg PH, Riso Inc">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Serial Number</label>
                                <input type="text" name="serial_number" class="form-control" placeholder="e.g. SN-99482710">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Save Asset Profile</button>
                    </div>
                </form>
            </div>
        </div>
</x-app-layout>
