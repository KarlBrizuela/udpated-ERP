<div class="card shadow-sm border-0 mb-4" style="border-radius: 8px; border: 1px solid #e2e8f0; background: #ffffff;">
    <div class="card-header bg-white border-0 pt-3 pb-2 px-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(239, 68, 68, 0.08); color: #ef4444;">
                <i class="las la-file-invoice-dollar fs-20"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold fs-16" style="color: #000000;">Expense Accounts</h5>
                <p class="text-muted small mb-0">Operational costs, administrative expenses, and cost of goods sold.</p>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill small fw-bold">{{ $categoryAccounts->count() }} Accounts</span>
    </div>
    <div class="card-body p-3 pt-1">
        <div class="row g-2">
            <!-- Account Group Cards (Aggregated) -->
            @foreach($categoryAccountGroups as $group)
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card expenses-card" style="background-color: #ffffff; border: 1.5px solid #ef4444 !important; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="openAccountGroupDetailModal({{ $group->id }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(239, 68, 68, 0.12); color: #ef4444;">
                                    <i class="las la-layer-group fs-20"></i>
                                </div>
                                <span class="badge px-2.5 py-1 rounded-pill small fw-bold" style="background-color: rgba(239, 68, 68, 0.1); color: #ef4444; font-size: 0.7rem;">
                                    {{ $group->accounts->count() }} {{ Str::plural('Account', $group->accounts->count()) }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-15 text-danger" style="letter-spacing: -0.2px;">{{ $group->name }}</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">{{ $group->description ?: 'Grouped Expense Accounts Card' }}</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small fw-bold" style="font-size: 0.72rem;">Total Group Balance</span>
                            <span class="fw-bold fs-14 text-danger">₱{{ number_format($group->calculated_balance, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Standalone Accounts (Without Account Group) -->
            @forelse($categoryAccounts->whereNull('account_group_id') as $acc)
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card expenses-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="openAccountLedgerModal({{ $acc->id }}, '{{ $acc->code }}', '{{ addslashes($acc->name) }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(239, 68, 68, 0.08); color: #ef4444;">
                                    <i class="las la-receipt fs-20"></i>
                                </div>
                                <span class="badge status-badge {{ $acc->is_active ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; {{ $acc->is_active ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}">
                                    {{ $acc->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">{{ $acc->name }} <span class="text-muted fw-normal" style="font-size: 0.75rem;">({{ $acc->code }})</span></h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">{{ $acc->category ?: 'Expense Account' }}</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($acc->calculated_balance ?? $acc->balance, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            @if($categoryAccountGroups->isEmpty())
            <div class="col-12 py-4 text-center text-muted">No expense accounts registered in the database.</div>
            @endif
            @endforelse
        </div>
    </div>
</div>
