<div class="card shadow-sm border-0 mb-4" style="border-radius: 8px; border: 1px solid #e2e8f0; background: #ffffff;">
    <div class="card-header bg-white border-0 pt-3 pb-3 px-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <!-- Left Side: Title & Counter -->
            <div class="d-flex align-items-center gap-3" style="flex-shrink: 0;">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(217, 37, 28, 0.08); color: #D9251C;">
                    <i class="las la-layer-group fs-20"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold fs-16" style="color: #000000;">Account Groups Management</h5>
                    <p class="text-muted small mb-0">Create and manage custom groups (e.g. Bank) under base Categories to aggregate account cards.</p>
                </div>
            </div>

            <!-- Right Side: Add Account Group Button -->
            <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 ms-auto">
                <button type="button" class="btn btn-sm text-white d-inline-flex align-items-center justify-content-center fw-bold" style="height: 38px; background-color: #D9251C; border-radius: 4px; padding: 0 16px; border: none; box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15);" data-bs-toggle="modal" data-bs-target="#addAccountGroupModal">
                    <i class="las la-plus-circle fs-18 me-1"></i> Add Account Group
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.8px; padding: 12px 16px; border-bottom: 2px solid #e2e8f0; width: 220px;">Group Name</th>
                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.8px; padding: 12px 16px; border-bottom: 2px solid #e2e8f0; width: 160px; text-align: center;">Base Category</th>
                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.8px; padding: 12px 16px; border-bottom: 2px solid #e2e8f0;">Description</th>
                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.8px; padding: 12px 16px; border-bottom: 2px solid #e2e8f0; width: 160px; text-align: center;">Linked Accounts</th>
                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.8px; padding: 12px 16px; border-bottom: 2px solid #e2e8f0; width: 130px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allAccountGroups as $group)
                    <tr style="transition: all 0.15s ease-in-out;" class="hover-row">
                        <td style="padding: 12px 16px; font-size: 0.84rem; color: #0f172a; font-weight: 700;">
                            <i class="las la-folder me-1 text-primary"></i> {{ $group->name }}
                        </td>
                        <td style="padding: 12px 16px; font-size: 0.84rem; text-align: center;">
                            @php
                                $badgeStyle = 'background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;';
                                if ($group->type === 'Liability') {
                                    $badgeStyle = 'background-color: rgba(245, 158, 11, 0.1); color: #f59e0b;';
                                } elseif ($group->type === 'Equity') {
                                    $badgeStyle = 'background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;';
                                } elseif ($group->type === 'Income') {
                                    $badgeStyle = 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;';
                                } elseif ($group->type === 'Expense') {
                                    $badgeStyle = 'background-color: rgba(239, 68, 68, 0.1); color: #ef4444;';
                                }
                            @endphp
                            <span class="badge px-2.5 py-1 rounded-pill small fw-bold" style="{{ $badgeStyle }} font-size: 0.72rem;">
                                {{ $group->type }}
                            </span>
                        </td>
                        <td style="padding: 12px 16px; font-size: 0.82rem; color: #475569;">
                            {{ $group->description ?: 'No description provided' }}
                        </td>
                        <td style="padding: 12px 16px; text-align: center;">
                            <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill small fw-bold">
                                {{ $group->accounts_count }} {{ Str::plural('Account', $group->accounts_count) }}
                            </span>
                        </td>
                        <td style="padding: 12px 16px; text-align: center;">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                <!-- Edit Group Button -->
                                <button type="button" class="btn btn-warning shadow btn-xs sharp text-white" 
                                    onclick="editAccountGroup({{ $group->id }}, '{{ addslashes($group->name) }}', '{{ $group->type }}', '{{ addslashes($group->description ?? '') }}')"
                                    title="Edit Account Group">
                                    <i class="las la-pen"></i>
                                </button>

                                <!-- Delete Group Button -->
                                <button type="button" class="btn btn-danger shadow btn-xs sharp" 
                                    onclick="deleteAccountGroup({{ $group->id }}, '{{ addslashes($group->name) }}')"
                                    title="Delete Account Group">
                                    <i class="las la-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted" style="font-size: 0.85rem;">
                            <i class="las la-folder-open fs-24 d-block mb-1"></i>
                            No Account Groups registered. Click "Add Account Group" above to create one.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
