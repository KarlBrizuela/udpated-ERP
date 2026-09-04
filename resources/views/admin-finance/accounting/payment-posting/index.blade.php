<x-app-layout :title="$title" :role="$role" :sidebar="'admin-finance'">
    @push('styles')
    <style>
        /* Container Gutter & Widescreen Grid Expansion per SKILL.md Section 2 */
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
            padding-bottom: 80px !important;
        }

        /* Primary Branding Color (Claretian Red) */
        .btn-claretian-primary {
            background-color: #D9251C !important;
            border-color: #D9251C !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 10px rgba(217, 37, 28, 0.2) !important;
            transition: all 0.15s ease-in-out !important;
        }
        .btn-claretian-primary:hover {
            background-color: #b81c14 !important;
            border-color: #b81c14 !important;
            color: #ffffff !important;
        }

        /* Modern Table Design per SKILL.md Section 3 */
        .claretian-table {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }
        .claretian-table thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
            font-size: 0.72rem !important;
            padding: 12px 16px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            border-top: none !important;
        }
        .claretian-table tbody td {
            padding: 12px 16px !important;
            color: #475569 !important;
            font-size: 0.84rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
            vertical-align: middle !important;
        }
        .claretian-table tbody tr {
            transition: all 0.15s ease-in-out !important;
        }
        .claretian-table tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* Row Action Buttons per SKILL.md Section 3 */
        .btn-xs.sharp {
            width: 30px !important;
            height: 30px !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 4px !important;
        }

        /* Tab Navigation per SKILL.md Section 1 */
        .nav-tabs .nav-link {
            color: #475569 !important;
            font-weight: 600 !important;
            font-size: 0.88rem !important;
            border: none !important;
            border-bottom: 2px solid transparent !important;
            padding: 0.75rem 1.25rem !important;
            background: transparent !important;
        }
        .nav-tabs .nav-link.active {
            color: #D9251C !important;
            border-bottom: 2px solid #D9251C !important;
        }

        /* Server-Side Pagination per SKILL.md Section 4 */
        .pagination .page-item.active .page-link {
            background-color: #D9251C !important;
            border-color: #D9251C !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15) !important;
        }
        .pagination .page-link {
            color: #475569 !important;
            border-color: #cbd5e1 !important;
            padding: 6px 12px !important;
            font-size: 0.83rem !important;
            background-color: #ffffff !important;
        }
        .pagination .page-link:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }

        /* Modal Guidelines per SKILL.md Section 7 */
        .claretian-modal-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 1.25rem 1.5rem !important;
        }
        .claretian-modal-title {
            color: #000000 !important;
            font-weight: 700 !important;
            font-size: 1.1rem !important;
        }
        .claretian-form-label {
            color: #475569 !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            margin-bottom: 0.4rem !important;
        }
        .claretian-form-control {
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            font-size: 0.85rem !important;
            color: #000000 !important;
            height: 38px !important;
            padding: 0.4rem 0.75rem !important;
        }
        .claretian-form-control:focus {
            border-color: #D9251C !important;
            box-shadow: 0 0 0 0.2rem rgba(217, 37, 28, 0.15) !important;
            outline: none !important;
        }
        .amount-display {
            white-space: nowrap !important;
            display: inline-block !important;
            font-weight: 700 !important;
            color: #0f5132 !important;
            font-size: 0.95rem !important;
        }
    </style>
    @endpush

    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-xl-12">
                <div class="card border-0 shadow-sm rounded-3">
                    {{-- Card Header --}}
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="card-title fw-bold text-dark mb-0 fs-18">Client Payment Posting Requests</h4>
                        <button type="button" class="btn btn-claretian-primary btn-sm d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#createPostingModal" style="height: 38px; padding: 0 18px; border-radius: 6px;">
                            <i class="las la-plus-circle me-1 fs-18"></i> Create Payment Posting
                        </button>
                    </div>

                    <div class="card-body p-4">
                        {{-- Alert Message --}}
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
                                <i class="las la-check-circle me-1 fs-16"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background-color: #f8d7da; color: #842029;">
                                <i class="las la-exclamation-triangle me-1 fs-16"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- Nav Tabs & Search Box per SKILL.md Section 5 --}}
                        <div class="d-flex justify-content-between align-items-center border-bottom mb-4 flex-wrap gap-3">
                            <ul class="nav nav-tabs border-0" id="postingTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                                        Pending Posting
                                        <span class="badge rounded-pill ms-1" style="background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
                                            {{ $postings->filter(fn($p) => $p->status === 'pending')->count() }}
                                        </span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="posted-tab" data-bs-toggle="tab" data-bs-target="#posted" type="button" role="tab">
                                        Posted Payments
                                        <span class="badge rounded-pill ms-1" style="background-color: #d1e7dd; color: #0f5132; border: 1px solid #a3cfbb;">
                                            {{ $postings->filter(fn($p) => $p->status === 'posted')->count() }}
                                        </span>
                                    </button>
                                </li>
                            </ul>

                            <!-- Search Form per SKILL.md Section 5 -->
                            <form action="{{ route('admin-finance.accounting.payment-posting.index') }}" method="GET" class="d-flex align-items-center gap-2 mb-2">
                                <div class="input-group input-group-sm" style="width: 280px;">
                                    <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 10px; border-top-left-radius: 4px; border-bottom-left-radius: 4px;">
                                        <i class="las la-search text-muted fs-16"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search client, letter, ref..." value="{{ request('search') }}" style="height: 38px; border-color: #cbd5e1; border-top-right-radius: 4px; border-bottom-right-radius: 4px; font-size: 0.82rem; padding-left: 0; outline: none; box-shadow: none;">
                                </div>
                                <button type="submit" class="btn btn-claretian-primary btn-sm d-inline-flex align-items-center justify-content-center" style="height: 38px; padding: 0 14px; border-radius: 4px;" title="Search">
                                    Search
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('admin-finance.accounting.payment-posting.index') }}" class="btn btn-light border btn-sm d-inline-flex align-items-center" style="height: 38px; padding: 0 12px; color: #475569;" title="Clear Search">
                                        Clear
                                    </a>
                                @endif
                            </form>
                        </div>

                        {{-- Tab Content --}}
                        <div class="tab-content" id="postingTabsContent">
                            {{-- Pending Tab --}}
                            <div class="tab-pane fade show active" id="pending" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table claretian-table align-middle">
                                        <thead>
                                            <tr>
                                                <th>LETTER NO.</th>
                                                <th>DATE</th>
                                                <th>CLIENT</th>
                                                <th>INVOICE / REF NO.</th>
                                                <th>METHOD & DEPOSIT ACCOUNT</th>
                                                <th class="text-end">AMOUNT</th>
                                                <th class="text-center">STATUS</th>
                                                <th class="text-end">ACTIONS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($postings->filter(fn($p) => $p->status === 'pending') as $posting)
                                            @php
                                                $item = $posting->items->first();
                                                $clientName = $item && $item->customer ? $item->customer->customer_name : 'N/A';
                                                $refDisplay = $item ? ($item->invoice_no ? 'SI# ' . $item->invoice_no : ($item->receipt_no ? 'OR# ' . $item->receipt_no : ($item->reference_no ?: 'N/A'))) : 'N/A';
                                                $methodDisplay = $item ? ucfirst($item->payment_method ?? 'cash') : 'Cash';
                                                $accountDisplay = $item && $item->account ? ($item->account->code . ' - ' . $item->account->name) : 'Cash in Bank';
                                            @endphp
                                            <tr>
                                                <td><strong style="color: #0f172a;">PP-{{ str_pad($posting->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                                <td>{{ date('M d, Y', strtotime($posting->date)) }}</td>
                                                <td><strong style="color: #0f172a;">{{ $clientName }}</strong></td>
                                                <td><span class="badge rounded-pill bg-light text-dark border">{{ $refDisplay }}</span></td>
                                                <td>
                                                    <div class="fw-bold" style="color: #0f172a; font-size: 0.83rem;">{{ $methodDisplay }}</div>
                                                    <div class="text-muted small"><i class="las la-university me-1"></i>{{ $accountDisplay }}</div>
                                                </td>
                                                <td class="text-end">
                                                    <span class="amount-display">₱{{ number_format($posting->items->sum('amount') ?? 0, 2) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge rounded-pill text-dark" style="background-color: #fef3c7; border: 1px solid #fde68a;">Pending</span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-inline-flex gap-1 justify-content-end">
                                                        <a href="{{ route('admin-finance.accounting.payment-posting.show', $posting->id) }}" class="btn btn-info shadow btn-xs sharp text-white" title="View Details">
                                                            <i class="las la-eye fs-14"></i>
                                                        </a>
                                                        <form action="{{ route('admin-finance.accounting.payment-posting.post', $posting->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark this Payment Posting as Posted? This will create a Journal Entry in GL & COA.');">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success shadow btn-xs sharp text-white" title="Post Payment to GL & COA" style="background-color: #10b981; border-color: #10b981;">
                                                                <i class="las la-check fs-14"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-5"><i class="las la-check-double fs-32 d-block mb-2 text-slate-400"></i>No pending payment posting requests.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Posted Tab --}}
                            <div class="tab-pane fade" id="posted" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table claretian-table">
                                        <thead>
                                            <tr>
                                                <th>LETTER NO.</th>
                                                <th>DATE</th>
                                                <th>CLIENT</th>
                                                <th>INVOICE / REF NO.</th>
                                                <th>METHOD & DEPOSIT ACCOUNT</th>
                                                <th class="text-end">AMOUNT</th>
                                                <th class="text-center">STATUS</th>
                                                <th class="text-end">ACTIONS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($postings->filter(fn($p) => $p->status === 'posted') as $posting)
                                            @php
                                                $item = $posting->items->first();
                                                $clientName = $item && $item->customer ? $item->customer->customer_name : 'N/A';
                                                $refDisplay = $item ? ($item->invoice_no ? 'SI# ' . $item->invoice_no : ($item->receipt_no ? 'OR# ' . $item->receipt_no : ($item->reference_no ?: 'N/A'))) : 'N/A';
                                                $methodDisplay = $item ? ucfirst($item->payment_method ?? 'cash') : 'Cash';
                                                $accountDisplay = $item && $item->account ? ($item->account->code . ' - ' . $item->account->name) : 'Cash in Bank';
                                            @endphp
                                            <tr>
                                                <td><strong style="color: #0f172a;">PP-{{ str_pad($posting->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                                <td>{{ date('M d, Y', strtotime($posting->date)) }}</td>
                                                <td><strong style="color: #0f172a;">{{ $clientName }}</strong></td>
                                                <td><span class="badge rounded-pill bg-light text-dark border">{{ $refDisplay }}</span></td>
                                                <td>
                                                    <div class="fw-bold" style="color: #0f172a; font-size: 0.83rem;">{{ $methodDisplay }}</div>
                                                    <div class="text-muted small"><i class="las la-university me-1"></i>{{ $accountDisplay }}</div>
                                                </td>
                                                <td class="text-end">
                                                    <span class="amount-display">₱{{ number_format($posting->items->sum('amount') ?? 0, 2) }}</span>
                                                </td>
                                                <td class="text-center"><span class="badge rounded-pill text-white" style="background-color: #10b981;">Posted</span></td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin-finance.accounting.payment-posting.show', $posting->id) }}" class="btn btn-info shadow btn-xs sharp text-white" title="View Details">
                                                        <i class="las la-eye fs-14"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-5"><i class="las la-history fs-32 d-block mb-2 text-slate-400"></i>No history of posted payments.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Pagination Container per SKILL.md Section 4 --}}
                        @if($postings->hasPages())
                        <div id="paginationContainer" class="mt-4 d-flex justify-content-end pe-4">
                            {{ $postings->onEachSide(0)->links('pagination::bootstrap-4') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Create Payment Posting Modal (Strict Adherence to SKILL.md Section 7) --}}
    <div class="modal fade" id="createPostingModal" tabindex="-1" aria-labelledby="createPostingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 8px; overflow: hidden;">
                <form action="{{ route('admin-finance.accounting.payment-posting.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Clean White Header per SKILL.md Section 7.1 -->
                    <div class="modal-header claretian-modal-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="las la-file-invoice-dollar fs-20 text-dark"></i>
                            <h5 class="modal-title claretian-modal-title mb-0" id="createPostingModalLabel">Create Client Payment Posting Request</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 bg-white">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="claretian-form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-control claretian-form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="claretian-form-label">Client / Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" class="form-select claretian-form-control" required>
                                    <option value="">-- Select Client --</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->customer_id }}">{{ $c->customer_name }} ({{ $c->customer_code ?: 'CUST-' . $c->customer_id }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="claretian-form-label">Reference No.</label>
                                <input type="text" name="reference_no" class="form-control claretian-form-control" placeholder="e.g. REF-2026-001">
                            </div>
                            <div class="col-md-4">
                                <label class="claretian-form-label">Invoice No. (SI)</label>
                                <input type="text" name="invoice_no" class="form-control claretian-form-control" placeholder="e.g. SI-10492">
                            </div>
                            <div class="col-md-4">
                                <label class="claretian-form-label">Receipt No. (OR/CR)</label>
                                <input type="text" name="receipt_no" class="form-control claretian-form-control" placeholder="e.g. OR-88392">
                            </div>

                            <div class="col-md-6">
                                <label class="claretian-form-label">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" id="paymentMethodSelect" class="form-select claretian-form-control" required>
                                    <option value="cash">Cash</option>
                                    <option value="deposit">Bank Deposit / Transfer</option>
                                    <option value="gcash">GCash / E-Wallet</option>
                                    <option value="check">Check</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="claretian-form-label">Deposit COA Account <span class="text-danger">*</span></label>
                                <select name="chart_of_account_id" id="coaAccountSelect" class="form-select claretian-form-control" required>
                                    @foreach($depositAccounts as $acc)
                                        <option value="{{ $acc->id }}" data-code="{{ $acc->code }}" data-name="{{ strtolower($acc->name) }}">
                                            {{ $acc->code }} - {{ $acc->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="text-muted mt-1" style="font-size: 0.72rem;">Target Cash/Bank account to be Debited in General Ledger.</div>
                            </div>

                            {{-- Check Details (Dynamic) --}}
                            <div class="col-12" id="checkFieldsContainer" style="display: none;">
                                <div class="p-3 rounded border" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">
                                    <h6 class="fw-bold mb-3 text-dark fs-14"><i class="las la-money-check me-1" style="color: #D9251C;"></i> Check Payment Details</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="claretian-form-label">Check Number</label>
                                            <input type="text" name="check_number" class="form-control claretian-form-control" placeholder="e.g. CHK-994821">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="claretian-form-label">Check Date</label>
                                            <input type="date" name="check_date" class="form-control claretian-form-control" value="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="claretian-form-label">Bank Name</label>
                                            <input type="text" name="bank_name" class="form-control claretian-form-control" placeholder="e.g. BDO / BPI / Metrobank">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="claretian-form-label">Amount (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" class="form-control claretian-form-control" placeholder="0.00" required>
                            </div>

                            <div class="col-md-6">
                                <label class="claretian-form-label">Proof of Payment Attachment</label>
                                <input type="file" name="proof_file" class="form-control claretian-form-control" accept="image/*,application/pdf" style="padding-top: 0.3rem;">
                                <div class="text-muted mt-1" style="font-size: 0.72rem;">JPG, PNG, or PDF up to 10MB.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Horizontal Action Buttons per SKILL.md Section 7.4 -->
                    <div class="modal-footer bg-white border-top py-3 pe-4 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light border fw-semibold" data-bs-dismiss="modal" style="background-color: #ffffff; color: #475569; border-color: #cbd5e1; font-size: 0.85rem; padding: 8px 20px; border-radius: 6px;">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-claretian-primary d-inline-flex align-items-center" style="font-size: 0.85rem; padding: 8px 22px; border-radius: 6px;">
                            <i class="las la-save fs-16 me-1"></i> Submit Payment Posting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const methodSelect = document.getElementById('paymentMethodSelect');
            const checkContainer = document.getElementById('checkFieldsContainer');
            const coaSelect = document.getElementById('coaAccountSelect');

            function toggleCheckFields() {
                const method = methodSelect.value;
                if (method === 'check') {
                    checkContainer.style.display = 'block';
                } else {
                    checkContainer.style.display = 'none';
                }

                Array.from(coaSelect.options).forEach(option => {
                    const code = option.getAttribute('data-code');
                    const name = (option.getAttribute('data-name') || '').toLowerCase();
                    if (method === 'cash' && (name.includes('cash on hand') || code === '1010' || code === '1020')) {
                        option.selected = true;
                    } else if (method === 'gcash' && (name.includes('wallet') || name.includes('gcash') || name.includes('e-wallet') || code === '1020' || code === '1030')) {
                        option.selected = true;
                    } else if ((method === 'deposit' || method === 'check') && (name.includes('bank') || code === '1000')) {
                        option.selected = true;
                    }
                });
            }

            methodSelect.addEventListener('change', toggleCheckFields);
            toggleCheckFields();

            // Client-side image compression before submission
            const proofInput = document.querySelector('input[name="proof_file"]');
            if (proofInput) {
                proofInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            const img = new Image();
                            img.onload = function() {
                                const canvas = document.createElement('canvas');
                                let width = img.width;
                                let height = img.height;
                                const maxDim = 1200;

                                if (width > height && width > maxDim) {
                                    height = Math.round((height * maxDim) / width);
                                    width = maxDim;
                                } else if (height > maxDim) {
                                    width = Math.round((width * maxDim) / height);
                                    height = maxDim;
                                }

                                canvas.width = width;
                                canvas.height = height;
                                const ctx = canvas.getContext('2d');
                                ctx.drawImage(img, 0, 0, width, height);

                                canvas.toBlob(function(blob) {
                                    const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                                        type: 'image/jpeg',
                                        lastModified: Date.now()
                                    });
                                    
                                    const dataTransfer = new DataTransfer();
                                    dataTransfer.items.add(compressedFile);
                                    proofInput.files = dataTransfer.files;
                                }, 'image/jpeg', 0.7);
                            };
                            img.src = event.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
