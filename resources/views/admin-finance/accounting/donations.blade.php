<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
            padding-bottom: 80px !important;
        }

        .don-header-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem 1.75rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            margin-bottom: 1.5rem;
        }

        .btn-don-primary {
            background-color: #D9251C;
            border-color: #D9251C;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.85rem;
            border-radius: 6px;
            padding: 8px 16px;
            transition: all 0.2s ease;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-don-primary:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
            color: #ffffff;
        }

        /* KPI Cards Style Matching COA */
        .don-kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .don-kpi-card:hover {
            transform: translateY(-4px) !important;
            border-color: #D9251C !important;
            box-shadow: 0 12px 24px -5px rgba(217, 37, 28, 0.12), 0 4px 12px -2px rgba(217, 37, 28, 0.08) !important;
        }

        .kpi-icon-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Modern Table overrides */
        .table-modern {
            margin-bottom: 0 !important;
            border: none !important;
        }
        .table-modern thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            font-size: 0.72rem !important;
            letter-spacing: 0.8px !important;
            padding: 12px 16px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            border-top: none !important;
        }
        .table-modern tbody td {
            padding: 12px 16px !important;
            font-size: 0.84rem !important;
            color: #475569 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            vertical-align: middle !important;
        }
        .table-modern tbody tr {
            transition: all 0.15s ease-in-out !important;
        }
        .table-modern tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* Form Modal overrides */
        .modal-content {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }
        .modal-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 18px 24px !important;
        }
        .modal-header .modal-title {
            font-size: 0.95rem !important;
            font-weight: 700 !important;
            color: #000000 !important;
        }
        .form-label {
            color: #475569 !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            font-size: 0.72rem !important;
            letter-spacing: 0.5px !important;
        }
        .form-control, .form-select {
            border-color: #cbd5e1 !important;
            border-radius: 6px !important;
            color: #000000 !important;
            font-size: 0.85rem !important;
            padding: 8px 12px !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: #D9251C !important;
            box-shadow: 0 0 0 0.2rem rgba(217, 37, 28, 0.15) !important;
            outline: none !important;
        }
    </style>
    @endpush

    <div class="container-fluid">
        <!-- Master Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="don-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(217, 37, 28, 0.08); color: #D9251C;">
                            <i class="las la-hand-holding-heart fs-24"></i>
                        </div>
                        <div>
                            <h4 class="fs-20 mb-1 fw-bold text-dark" style="letter-spacing: -0.3px;">Donations Module</h4>
                            <p class="text-muted small mb-0">Record and track donor contributions, cash gifts, in-kind donations, and restricted funds.</p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-don-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#recordDonationModal">
                            <i class="las la-plus-circle fs-16"></i> Record Donation
                        </button>
                        <button class="btn btn-sm btn-light border px-3 d-flex align-items-center gap-2 fw-semibold text-secondary" style="height: 38px; border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#registerDonorModal">
                            <i class="las la-user-plus fs-16"></i> Register Donor
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric summary cards -->
        <div class="row g-3 mb-4">
            <!-- Total Cash Raised Card -->
            <div class="col-md-4">
                <div class="don-kpi-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="kpi-icon-wrapper" style="background-color: rgba(16, 185, 129, 0.08); color: #16a34a;">
                            <i class="las la-hand-holding-usd fs-20"></i>
                        </div>
                        <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                    </div>
                    <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Cash Donations</h6>
                    <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Total cash contributions received in this fiscal period.</p>
                    <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                        <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Total</span>
                        <span class="fw-bold fs-15 text-dark" style="font-weight: 800 !important;">₱{{ number_format($metrics['total_cash_raised'], 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- In-Kind Contributions Card -->
            <div class="col-md-4">
                <div class="don-kpi-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="kpi-icon-wrapper" style="background-color: rgba(37, 99, 235, 0.08); color: #2563eb;">
                            <i class="las la-gift fs-20"></i>
                        </div>
                        <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                    </div>
                    <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">In-Kind Contributions</h6>
                    <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Total in-kind distributions (books, equipment, assets).</p>
                    <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                        <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Count</span>
                        <span class="fw-bold fs-15 text-dark" style="font-weight: 800 !important;">{{ $metrics['total_in_kind_count'] }} Donations</span>
                    </div>
                </div>
            </div>

            <!-- Registered Donors Card -->
            <div class="col-md-4">
                <div class="don-kpi-card h-100" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#donorsListModal" title="Click to view Donor List">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="kpi-icon-wrapper" style="background-color: rgba(217, 37, 28, 0.08); color: #D9251C;">
                            <i class="las la-users fs-20"></i>
                        </div>
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2.5 py-1 small fw-bold" style="font-size: 0.7rem;">Click to View List</span>
                    </div>
                    <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Registered Donors</h6>
                    <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Active partners and organizations in the donor network.</p>
                    <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                        <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Active</span>
                        <span class="fw-bold fs-15 text-dark" style="font-weight: 800 !important;">{{ $metrics['active_donors_count'] }} Donors</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donations Ledger Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #e2e8f0 !important;">
                    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="las la-list me-2 fs-18"></i>Donations Ledger</h6>
                        
                        <form method="GET" action="{{ route('admin-finance.donations.index') }}" class="d-flex align-items-center">
                            <div class="input-group" style="width: 280px;">
                                <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 10px; border-top-left-radius: 4px; border-bottom-left-radius: 4px;">
                                    <i class="las la-search text-muted fs-16"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0 border-end-0" placeholder="Search donor, receipt..." value="{{ $search ?? '' }}" style="height: 38px; border-color: #cbd5e1; font-size: 0.82rem; padding-left: 0; outline: none; box-shadow: none;">
                                <button type="submit" class="btn text-white px-3 d-inline-flex align-items-center justify-content-center" style="height: 38px; background-color: #D9251C; border-color: #D9251C; border-top-right-radius: 4px; border-bottom-right-radius: 4px; font-weight: 600; font-size: 0.82rem; line-height: 1 !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                                    Search
                                </button>
                            </div>
                            @if(!empty($search))
                                <a href="{{ route('admin-finance.donations.index') }}" class="btn btn-sm btn-light border ms-2 d-inline-flex align-items-center justify-content-center" style="height: 38px; padding: 0 12px; border-radius: 4px; font-weight: 600; color: #475569;">
                                    Clear
                                </a>
                            @endif
                        </form>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-modern align-middle">
                                <thead>
                                    <tr>
                                        <th>Donation No</th>
                                        <th>Donor Name</th>
                                        <th>Type</th>
                                        <th class="text-end">Amount / Value</th>
                                        <th>Purpose / Details</th>
                                        <th>Date</th>
                                        <th class="text-center" style="width: 120px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($donations as $don)
                                    <tr>
                                        <td><span class="fw-bold text-dark font-monospace">{{ $don->donation_no }}</span></td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $don->donor->name ?? 'Anonymous' }}</span>
                                            @if($don->donor && $don->donor->type)
                                                <small class="text-muted d-block" style="font-size: 0.75rem;">({{ $don->donor->type }})</small>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-secondary-subtle text-secondary px-2.5 py-1">{{ $don->donation_type }}</span></td>
                                        <td class="text-end fw-bold text-dark">₱{{ number_format($don->amount, 2) }}</td>
                                        <td>
                                            <span class="text-muted small">
                                                {{ $don->restricted_fund_purpose ?: ($don->item_description ?: ($don->project_supported ?: 'General Fund')) }}
                                            </span>
                                        </td>
                                        <td><span class="text-muted small">{{ $don->donation_date ? \Carbon\Carbon::parse($don->donation_date)->format('M d, Y') : 'N/A' }}</span></td>
                                        <td class="text-center">
                                            <a href="{{ route('admin-finance.donations.show', $don->id) }}" class="btn btn-info shadow btn-xs sharp text-white me-1" title="View Details">
                                                <i class="las la-eye"></i>
                                            </a>
                                            <form action="{{ route('admin-finance.donations.destroy', $don->id) }}" method="POST" class="d-inline delete-donation-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger shadow btn-xs sharp btn-delete-donation-confirm" title="Delete Donation">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="las la-hand-holding-heart fs-48 mb-2 d-block text-secondary"></i>
                                            No donation records found. Click "Record Donation" above to add one!
                                        </td>
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

    <!-- Simple Record Donation Modal -->
    <div class="modal fade" id="recordDonationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark"><i class="las la-plus-circle me-2 text-danger"></i> Record Contribution</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin-finance.donations.donation.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label">Select Donor *</label>
                            <select name="donor_id" class="form-select" required>
                                <option value="" disabled selected>Select Donor...</option>
                                @foreach($donors as $dnr)
                                    <option value="{{ $dnr->id }}">{{ $dnr->name }} ({{ $dnr->type }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Donation Type *</label>
                                <select name="donation_type" class="form-select" required>
                                    <option value="Cash">Cash / Transfer</option>
                                    <option value="Books">Books / Print Materials</option>
                                    <option value="Equipment">Equipment / Asset</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Amount / Value (₱) *</label>
                                <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date *</label>
                            <input type="date" name="donation_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Purpose / Item Details</label>
                            <textarea name="item_description" class="form-control" rows="2" placeholder="e.g., Book donation for library or General Cash Fund..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 6px;">Cancel</button>
                        <button type="submit" class="btn btn-don-primary px-4">Save Donation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Register Donor Modal -->
    <div class="modal fade" id="registerDonorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark"><i class="las la-user-plus me-2 text-danger"></i> Register Donor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin-finance.donations.donor.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label">Donor Name / Organization *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g., St. Paul Educational Trust" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Donor Type *</label>
                                <select name="type" class="form-select" required>
                                    <option value="Individual">Individual</option>
                                    <option value="Corporate">Corporate</option>
                                    <option value="Institutional">Institutional</option>
                                    <option value="Foundation">Foundation</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="donor@example.com">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="e.g., 0917-123-4567">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 6px;">Cancel</button>
                        <button type="submit" class="btn btn-don-primary px-4">Register Donor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Registered Donors List Modal (XL) -->
    <div class="modal fade" id="donorsListModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-dark"><i class="las la-users me-2 text-danger"></i> Registered Donors Management</h5>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-sm btn-outline-danger px-3 fw-semibold" style="height: 34px; border-radius: 4px;" data-bs-toggle="modal" data-bs-target="#registerDonorModal">
                            <i class="las la-plus-circle me-1"></i> Register New Donor
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-modern align-middle">
                            <thead>
                                <tr>
                                    <th>Donor Code</th>
                                    <th>Donor Name</th>
                                    <th>Type</th>
                                    <th>Email / Contact</th>
                                    <th class="text-end">Total Donated</th>
                                    <th>Registered Date</th>
                                    <th class="text-center" style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($donors as $dnr)
                                <tr>
                                    <td><span class="fw-bold text-dark font-monospace">{{ $dnr->donor_code ?: 'DNR-'.$dnr->id }}</span></td>
                                    <td><span class="fw-bold text-dark">{{ $dnr->name }}</span></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary px-2.5 py-1">{{ $dnr->type }}</span></td>
                                    <td>
                                        <small class="text-muted d-block">{{ $dnr->email ?: 'N/A' }}</small>
                                        <small class="text-muted">{{ $dnr->phone ?: '' }}</small>
                                    </td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($dnr->total_donated ?: 0, 2) }}</td>
                                    <td><span class="text-muted small">{{ $dnr->created_at ? $dnr->created_at->format('M d, Y') : 'N/A' }}</span></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-warning shadow btn-xs sharp text-white" data-bs-toggle="modal" data-bs-target="#editDonorModal_{{ $dnr->id }}" title="Edit Donor">
                                                <i class="las la-pen"></i>
                                            </button>
                                            <form action="{{ route('admin-finance.donations.donor.destroy', $dnr->id) }}" method="POST" class="d-inline delete-donor-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger shadow btn-xs sharp btn-delete-donor-confirm" title="Delete Donor">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No donors registered yet. Click "Register New Donor" to add one!</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 6px;">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Donor Modals -->
    @foreach($donors as $dnr)
    <div class="modal fade" id="editDonorModal_{{ $dnr->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark"><i class="las la-edit me-2 text-danger"></i> Edit Donor Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin-finance.donations.donor.update', $dnr->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label">Donor Name / Organization *</label>
                            <input type="text" name="name" class="form-control" value="{{ $dnr->name }}" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Donor Type *</label>
                                <select name="type" class="form-select" required>
                                    <option value="Individual" {{ $dnr->type == 'Individual' ? 'selected' : '' }}>Individual</option>
                                    <option value="Corporate" {{ $dnr->type == 'Corporate' ? 'selected' : '' }}>Corporate</option>
                                    <option value="Institutional" {{ $dnr->type == 'Institutional' ? 'selected' : '' }}>Institutional</option>
                                    <option value="Foundation" {{ $dnr->type == 'Foundation' ? 'selected' : '' }}>Foundation</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $dnr->email }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ $dnr->phone }}">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 6px;">Cancel</button>
                        <button type="submit" class="btn btn-don-primary px-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete Donation Confirmation
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-delete-donation-confirm');
                if (btn) {
                    e.preventDefault();
                    const form = btn.closest('form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This action will permanently delete this donation ledger entry and reverse any linked restricted fund balance.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#D9251C',
                        cancelButtonColor: '#475569',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });

            // Delete Donor Confirmation
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-delete-donor-confirm');
                if (btn) {
                    e.preventDefault();
                    const form = btn.closest('form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This action will permanently delete this donor registry and all historical metadata associated with it.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#D9251C',
                        cancelButtonColor: '#475569',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
