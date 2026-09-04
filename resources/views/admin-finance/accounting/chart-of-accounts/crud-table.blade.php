<div class="card shadow-sm border-0 mb-4" style="border-radius: 8px; border: 1px solid #e2e8f0; background: #ffffff;">
    <div class="card-header bg-white border-0 pt-3 pb-3 px-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <!-- Left Side: Title & Counter -->
            <div class="d-flex align-items-center gap-3" style="flex-shrink: 0;">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(217, 37, 28, 0.08); color: #D9251C;">
                    <i class="las la-list-alt fs-20"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold fs-16" style="color: #000000;">Chart of Accounts Master List</h5>
                    <p class="text-muted small mb-0">Manage accounting codes, names, categories, and account statuses.</p>
                </div>
            </div>

            <!-- Right Side: Filter Form & Add Account Button (Right Aligned) -->
            <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 ms-auto">
                <form method="GET" action="{{ route('admin-finance.accounting.chart-of-accounts') }}" class="d-flex flex-wrap align-items-center justify-content-end gap-2 m-0" id="coaFilterForm">
                    <input type="hidden" name="main_tab" value="crud">

                    <!-- Category Dropdown Filter -->
                    <select name="category" class="form-select form-select-sm" style="height: 38px; border-color: #cbd5e1; border-radius: 4px; font-size: 0.82rem; color: #000000; outline: none; width: 150px;" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <option value="Asset" {{ request('category') === 'Asset' ? 'selected' : '' }}>Asset</option>
                        <option value="Liability" {{ request('category') === 'Liability' ? 'selected' : '' }}>Liability</option>
                        <option value="Equity" {{ request('category') === 'Equity' ? 'selected' : '' }}>Equity</option>
                        <option value="Income" {{ request('category') === 'Income' ? 'selected' : '' }}>Income</option>
                        <option value="Expense" {{ request('category') === 'Expense' ? 'selected' : '' }}>Expense</option>
                    </select>

                    <!-- Search Input Group -->
                    <div class="input-group input-group-sm" style="width: 230px;">
                        <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 10px; border-top-left-radius: 4px; border-bottom-left-radius: 4px;">
                            <i class="las la-search text-muted fs-16"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search code, name..." value="{{ request('search') }}" style="height: 38px; border-color: #cbd5e1; border-top-right-radius: 4px; border-bottom-right-radius: 4px; font-size: 0.82rem; padding-left: 0; outline: none; box-shadow: none;">
                    </div>

                    <!-- Search & Clear Buttons -->
                    <button type="submit" class="btn btn-sm text-white d-inline-flex align-items-center justify-content-center fw-bold" style="height: 38px; background-color: #D9251C; border-radius: 4px; padding: 0 14px; border: none;">
                        <i class="las la-filter me-1"></i> Filter
                    </button>
                    @if(request('search') || request('category'))
                        <a href="{{ route('admin-finance.accounting.chart-of-accounts', ['main_tab' => 'crud']) }}" class="btn btn-sm btn-light border d-inline-flex align-items-center justify-content-center" style="height: 38px; border-radius: 4px; padding: 0 12px; color: #475569;">
                            <i class="las la-times me-1"></i> Clear
                        </a>
                    @endif
                </form>

                <!-- Add Account Button -->
                <button type="button" class="btn btn-sm text-white d-inline-flex align-items-center justify-content-center fw-bold ms-md-1" style="height: 38px; background-color: #D9251C; border-radius: 4px; padding: 0 16px; border: none; box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15);" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                    <i class="las la-plus-circle fs-18 me-1"></i> Add New Account
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.8px; padding: 12px 16px; border-bottom: 2px solid #e2e8f0; width: 120px;">Account Code</th>
                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.8px; padding: 12px 16px; border-bottom: 2px solid #e2e8f0;">Account Name</th>
                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.8px; padding: 12px 16px; border-bottom: 2px solid #e2e8f0; width: 140px; text-align: center;">Type / Category</th>
                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.8px; padding: 12px 16px; border-bottom: 2px solid #e2e8f0; width: 160px;">Account Group</th>
                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.8px; padding: 12px 16px; border-bottom: 2px solid #e2e8f0; width: 160px;">Sub-Category</th>
                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.8px; padding: 12px 16px; border-bottom: 2px solid #e2e8f0; width: 120px; text-align: center;">Status</th>
                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.8px; padding: 12px 16px; border-bottom: 2px solid #e2e8f0; width: 130px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allAccounts as $account)
                    <tr style="transition: all 0.15s ease-in-out;" class="hover-row">
                        <td style="padding: 12px 16px; font-size: 0.84rem; color: #0f172a; font-weight: 700;">
                            {{ $account->code }}
                        </td>
                        <td style="padding: 12px 16px; font-size: 0.84rem; color: #000000; font-weight: 600;">
                            {{ $account->name }}
                        </td>
                        <td style="padding: 12px 16px; font-size: 0.84rem; text-align: center;">
                            @php
                                $badgeStyle = 'background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;';
                                if ($account->type === 'Liability') {
                                    $badgeStyle = 'background-color: rgba(245, 158, 11, 0.1); color: #f59e0b;';
                                } elseif ($account->type === 'Equity') {
                                    $badgeStyle = 'background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;';
                                } elseif ($account->type === 'Income') {
                                    $badgeStyle = 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;';
                                } elseif ($account->type === 'Expense') {
                                    $badgeStyle = 'background-color: rgba(239, 68, 68, 0.1); color: #ef4444;';
                                }
                            @endphp
                            <span class="badge px-2.5 py-1 rounded-pill small fw-bold" style="{{ $badgeStyle }} font-size: 0.72rem;">
                                {{ $account->type }}
                            </span>
                        </td>
                        <td style="padding: 12px 16px; font-size: 0.82rem; color: #475569;">
                            @if($account->accountGroup)
                                <span class="badge bg-light text-dark border px-2 py-1 rounded small"><i class="las la-folder me-1 text-primary"></i>{{ $account->accountGroup->name }}</span>
                            @else
                                <span class="text-muted small">None</span>
                            @endif
                        </td>
                        <td style="padding: 12px 16px; font-size: 0.82rem; color: #475569;">
                            {{ $account->category ?? 'General' }}
                        </td>
                        <td style="padding: 12px 16px; text-align: center;">
                            <span class="badge status-badge {{ $account->is_active ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ $account->is_active ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $account->id }}">
                                {{ $account->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="padding: 12px 16px; text-align: center;">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                <!-- Edit Action Button -->
                                <button type="button" class="btn btn-warning shadow btn-xs sharp text-white btn-edit-account" 
                                    data-id="{{ $account->id }}" 
                                    data-code="{{ $account->code }}" 
                                    data-name="{{ $account->name }}" 
                                    data-type="{{ $account->type }}" 
                                    data-category="{{ $account->category }}" 
                                    data-account-group-id="{{ $account->account_group_id }}"
                                    data-active="{{ $account->is_active }}"
                                    title="Edit Account">
                                    <i class="las la-pen"></i>
                                </button>

                                <!-- Delete Action Button -->
                                <button type="button" class="btn btn-danger shadow btn-xs sharp btn-delete-account" 
                                    data-id="{{ $account->id }}" 
                                    data-name="{{ $account->name }} ({{ $account->code }})"
                                    title="Delete Account">
                                    <i class="las la-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted" style="font-size: 0.85rem;">
                            <i class="las la-folder-open fs-24 d-block mb-1"></i>
                            No Chart of Accounts records found matching criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginator Links -->
        <div id="paginationContainer" class="p-3 d-flex justify-content-end border-top" style="border-color: #f1f5f9 !important;">
            {{ $allAccounts->onEachSide(0)->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
