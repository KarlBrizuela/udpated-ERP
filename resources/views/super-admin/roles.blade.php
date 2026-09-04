<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="row">
        <div class="col-xl-12">
            <div class="custom-tab-1">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#userAccess"><i class="la la-users me-2"></i> User Access</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#rolePermissions"><i class="la la-user-shield me-2"></i> Role Permissions</a>
                    </li>
                </ul>
                <div class="tab-content pt-4">
                    <!-- User Access Tab -->
                    <div class="tab-pane fade show active" id="userAccess" role="tabpanel">
                        <div class="card">
                            <div class="card-header border-0 pb-0">
                                <h4 class="card-title">Assign Divisions to Users</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-responsive-md user-access-table">
                                        <thead>
                                            <tr>
                                                <th><strong>NAME</strong></th>
                                                <th><strong>ROLE / POSITION</strong></th>
                                                <th><strong>ASSIGNED DIVISIONS</strong></th>
                                                <th><strong>STATUS</strong></th>
                                                <th><strong>ACTION</strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($users as $user)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="w-space-no">{{ $user->name }}</span>
                                                    </div>
                                                    <small class="text-muted">{{ $user->email }}</small>
                                                </td>
                                                <td>
                                                    @php
                                                        $badgeClass = match($user->position) {
                                                            'Super Admin' => 'badge-primary',
                                                            'Director' => 'badge-info',
                                                            'Manager' => 'badge-success',
                                                            default => 'badge-secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge light {{ $badgeClass }}">{{ $user->position ?? 'N/A' }}</span>
                                                    @if($user->sales_team)
                                                        <span class="badge light badge-primary ms-1"><i class="fas fa-users me-1"></i>{{ $user->sales_team }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @foreach($user->divisions as $userDiv)
                                                        @php
                                                            $badgeClass = 'badge-secondary';
                                                            if($userDiv->division == 'Marketing Division') $badgeClass = 'badge-success';
                                                            else if($userDiv->division == 'Production Division') $badgeClass = 'badge-warning';
                                                            else if($userDiv->division == 'Admin & Finance Division') $badgeClass = 'badge-info';
                                                        @endphp
                                                        <span class="badge light {{ $badgeClass }} mb-1">{{ $userDiv->division }}</span>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @if($user->status)
                                                        <span class="badge light badge-success">Active</span>
                                                    @else
                                                        <span class="badge light badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="#" class="btn btn-primary shadow btn-xs sharp me-1"
                                                            data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                                            <i class="fas fa-pencil-alt"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Role Permissions Tab -->
                    <div class="tab-pane fade" id="rolePermissions" role="tabpanel">
                        <div class="card">
                            <div class="card-header border-0 pb-0">
                                <h4 class="card-title">Manage Role Permissions (Sidebar Access)</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-responsive-md roles-table">
                                        <thead>
                                            <tr>
                                                <th><strong>ROLE NAME</strong></th>
                                                <th><strong>DIVISION</strong></th>
                                                <th><strong>PERMISSIONS</strong></th>
                                                <th><strong>STATUS</strong></th>
                                                <th><strong>ACTION</strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($roles as $roleItem)
                                            <tr>
                                                <td><strong>{{ $roleItem->name }}</strong></td>
                                                <td>{{ $roleItem->division }}</td>
                                                <td>
                                                    <span class="badge light badge-info">{{ count($roleItem->permissions ?? []) }} Actions</span>
                                                </td>
                                                <td>
                                                    @if($roleItem->is_active)
                                                        <span class="badge light badge-success">Active</span>
                                                    @else
                                                        <span class="badge light badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="#" class="btn btn-primary shadow btn-xs sharp me-1"
                                                            data-bs-toggle="modal" data-bs-target="#editRoleModal{{ $roleItem->id }}">
                                                            <i class="fas fa-cog"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
    {{-- User Edit Modals --}}
    @foreach($users as $user)
    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('roles.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Access: {{ $user->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label font-w600">Position / Role</label>
                            <select name="position" class="form-control default-select">
                                @foreach($positions as $posOption)
                                    <option value="{{ $posOption }}" {{ $user->position == $posOption ? 'selected' : '' }}>{{ $posOption }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-w600">Sales Team / Area Team</label>
                            <select name="sales_team" class="form-control default-select">
                                <option value="" {{ empty($user->sales_team) ? 'selected' : '' }}>None (No Team)</option>
                                <option value="Team A" {{ $user->sales_team === 'Team A' ? 'selected' : '' }}>Team A</option>
                                <option value="Team B" {{ $user->sales_team === 'Team B' ? 'selected' : '' }}>Team B</option>
                                <option value="Team C" {{ $user->sales_team === 'Team C' ? 'selected' : '' }}>Team C</option>
                                <option value="Book Sales" {{ $user->sales_team === 'Book Sales' ? 'selected' : '' }}>Book Sales</option>
                                <option value="MIBF" {{ $user->sales_team === 'MIBF' ? 'selected' : '' }}>MIBF</option>
                            </select>
                        </div>
                        <div class="form-group mt-4">
                            <label class="form-label font-w600 mb-3">Assigned Divisions Access</label>
                            @foreach(['Marketing Division', 'Production Division', 'Admin & Finance Division'] as $divName)
                            <div class="form-check custom-checkbox mb-3">
                                <input type="checkbox" class="form-check-input" id="checkDiv{{ $divName }}{{ $user->id }}" name="divisions[]" value="{{ $divName }}"
                                    {{ $user->divisions->contains('division', $divName) ? 'checked' : '' }}>
                                <label class="form-check-label" for="checkDiv{{ $divName }}{{ $user->id }}">{{ $divName }}</label>
                            </div>
                            @endforeach
                        </div>

                        <hr>

                        <!-- User Specific Permissions -->
                        <div class="form-group mt-4">
                            <label class="form-label font-w600 mb-3 text-primary">Specific Sidebar Access (Manual Selection)</label>
                            <p class="small text-muted mb-3"><strong>Tip:</strong> Permissions from the selected role will be automatically checked. You can add more permissions by checking additional boxes. Leave all unchecked to use role defaults only.</p>
                            
                            <div class="row">
                                @foreach($availablePermissions as $groupName => $perms)
                                <div class="col-md-12 mb-3">
                                    <h6 class="text-black border-bottom pb-1" style="font-size: 0.9rem;">{{ $groupName }}</h6>
                                    <div class="row">
                                        @foreach($perms as $key => $label)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check custom-checkbox">
                                                @php
                                                    // Use user's manual permissions if set, otherwise fall back to role permissions
                                                    $permissionsToCheck = !is_null($user->permissions) 
                                                        ? $user->permissions 
                                                        : ($rolePermissionsMap[$user->position] ?? []);
                                                @endphp
                                                <input type="checkbox" class="form-check-input" id="user_perm_{{ $user->id }}_{{ str_replace('.', '_', $key) }}" name="permissions[]" value="{{ $key }}"
                                                    {{ in_array($key, $permissionsToCheck) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="user_perm_{{ $user->id }}_{{ str_replace('.', '_', $key) }}" style="font-size: 0.8rem;">{{ $label }}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Role Permission Modals --}}
    @foreach($roles as $roleItem)
    <div class="modal fade" id="editRoleModal{{ $roleItem->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('roles.permissions.update', $roleItem->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Permissions: {{ $roleItem->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="role_active_{{ $roleItem->id }}" value="1" {{ $roleItem->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label font-w600" for="role_active_{{ $roleItem->id }}">Active Role (Enabled)</label>
                                </div>
                                <p class="small text-muted">When inactive, the role defaults are ignored for all users.</p>
                            </div>
                        </div>
                        <div class="row">
                            @foreach($availablePermissions as $groupName => $perms)
                            <div class="col-md-12 mb-4">
                                <h6 class="text-primary border-bottom pb-2">{{ $groupName }}</h6>
                                <div class="row">
                                    @foreach($perms as $key => $label)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check custom-checkbox">
                                            <input type="checkbox" class="form-check-input" id="perm_{{ $roleItem->id }}_{{ str_replace('.', '_', $key) }}" name="permissions[]" value="{{ $key }}"
                                                {{ in_array($key, $roleItem->permissions ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm_{{ $roleItem->id }}_{{ str_replace('.', '_', $key) }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Permissions</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
    @endpush

    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .custom-tab-1 .nav-tabs .nav-link { font-weight: 600; border-radius: 0; }
        .custom-tab-1 .nav-tabs .nav-link.active { color: #2b2a29; border-bottom: 3px solid #2b2a29; }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        // Role Permissions Map
        const rolePermissionsMap = {!! json_encode($rolePermissionsMap) !!};

        $(document).ready(function() {
            $('.user-access-table, .roles-table').DataTable();

            // Function to sync permissions based on selected position
            function syncPermissionsWithRole(positionSelect) {
                const selectedPosition = positionSelect.val();
                const form = positionSelect.closest('form');
                const modalContent = form.closest('.modal-content');
                
                if (selectedPosition && rolePermissionsMap[selectedPosition]) {
                    const rolePermissions = rolePermissionsMap[selectedPosition];
                    
                    // First, uncheck all permission checkboxes
                    modalContent.find('input[name="permissions[]"]').prop('checked', false);
                    
                    // Then, check the ones that match the role's permissions
                    rolePermissions.forEach(permission => {
                        // Find checkbox by value
                        modalContent.find('input[name="permissions[]"][value="' + permission + '"]').prop('checked', true);
                    });
                }
            }

            // Handle position change in user modal
            $(document).on('change', 'select[name="position"]', function() {
                syncPermissionsWithRole($(this));
            });

            // Also sync when modal is shown (for existing users)
            $('.modal').on('show.bs.modal', function() {
                const positionSelect = $(this).find('select[name="position"]');
                if (positionSelect.length) {
                    syncPermissionsWithRole(positionSelect);
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
