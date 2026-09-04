<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex" style="padding-bottom: 0.5rem;">
                    <div>
                        <h2 class="mb-0 text-black" style="font-size: 2.5rem; font-weight: 700;">{{ $title }}</h2>
                    </div>
                    <div class="d-flex align-items-center mt-3 mt-sm-0">
                        <select id="filterDivision" class="default-select style-1 me-3">
                            <option value="All Divisions">All Divisions</option>
                            <option value="Admin & Finance Division">Admin & Finance Division</option>
                            <option value="Marketing Division">Marketing Division</option>
                            <option value="Production Division">Production Division</option>
                        </select>
                        <select id="filterPosition" class="default-select style-1 me-3">
                            <option value="All Positions">All Positions</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos }}">{{ $pos }}</option>
                            @endforeach
                        </select>
                        <a href="javascript:void(0);" class="btn btn-primary rounded d-flex align-items-center"
                            data-bs-toggle="modal" data-bs-target="#addUserModal"
                            style="gap: 0.5rem; padding: 0.5rem 1rem; height: 38px; min-height: 38px; line-height: 1.5; box-sizing: border-box; border: none; background: #ff0000; color: #ffffff; font-weight: 500;">
                            <i class="las la-plus"
                                style="font-size: 1rem; line-height: 1; margin: 0; padding: 0; background: transparent; border: none; box-shadow: none;"></i>
                            <span style="font-size: 0.875rem; white-space: nowrap;">Add New User</span>
                        </a>
                    </div>
                </div>
                <div class="card-body" style="padding-top: 0.5rem;">
                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th style="width:50px;">
                                        <div class="form-check custom-checkbox checkbox-success check-lg me-3">
                                            <input type="checkbox" class="form-check-input" id="checkAll" required="">
                                            <label class="form-check-label" for="checkAll"></label>
                                        </div>
                                    </th>
                                    <th><strong>EMPLOYEE #</strong></th>
                                    <th><strong>NAME</strong></th>
                                    <th><strong>EMAIL</strong></th>
                                    <th><strong>DIVISION</strong></th>
                                    <th><strong>DEPARTMENT</strong></th>
                                    <th><strong>POSITION</strong></th>
                                    <th><strong>STATUS</strong></th>
                                    <th><strong>ACTION</strong></th>
                                </tr>
                            </thead>
                            <tbody id="userTableBody">
                                @forelse($users as $index => $user)
                                    <tr data-user-id="{{ $user->id }}">
                                        <td>
                                            <div class="form-check custom-checkbox checkbox-success check-lg me-3">
                                                <input type="checkbox" class="form-check-input"
                                                    id="customCheckBox{{ $index }}" required="">
                                                <label class="form-check-label" for="customCheckBox{{ $index }}"></label>
                                            </div>
                                        </td>
                                        <td><strong>{{ $user->employee_number ?? 'N/A' }}</strong></td>
                                        <td>
                                            <div class="d-flex align-items-center"><span
                                                    class="w-space-no">{{ $user->name }}</span></div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->division ?? 'N/A' }}</td>
                                        <td>{{ $user->department ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $badgeClass = match ($user->position) {
                                                    'Super Admin' => 'badge-primary',
                                                    'Director' => 'badge-info',
                                                    'Manager' => 'badge-success',
                                                    default => 'badge-secondary'
                                                };
                                            @endphp
                                            <span
                                                class="badge light {{ $badgeClass }}">{{ $user->position ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i
                                                    class="fa fa-circle {{ $user->status ? 'text-success' : 'text-danger' }} me-1"></i>
                                                {{ $user->status ? 'Active' : 'Inactive' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="#" class="btn btn-primary shadow btn-xs sharp me-1 edit-user-btn"
                                                    data-bs-toggle="modal" data-bs-target="#editUserModal"
                                                    data-user-id="{{ $user->id }}"
                                                    data-employee-number="{{ $user->employee_number }}"
                                                    data-first-name="{{ $user->first_name }}"
                                                    data-last-name="{{ $user->last_name }}"
                                                    data-middle-initial="{{ $user->middle_initial }}"
                                                    data-email="{{ $user->email }}" data-division="{{ $user->division }}"
                                                    data-department="{{ $user->department }}"
                                                    data-position="{{ $user->position }}" data-status="{{ $user->status }}">
                                                    <i class="fas fa-pencil-alt"></i></a>
                                                <a href="#" class="btn btn-danger shadow btn-xs sharp delete-user-btn"
                                                    data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                                                    data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">
                                                    <i class="fa fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No users found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addUserForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Employee Number</label>
                                        <input type="text" name="employee_number" class="form-control"
                                            placeholder="e.g. EMP-001" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Division</label>
                                        <select name="division" id="add_division" class="default-select form-control"
                                            required>
                                            <option value="">Select Division</option>
                                            <option value="All Divisions">All Divisions</option>
                                            <option value="Admin & Finance Division">Admin & Finance Division</option>
                                            <option value="Marketing Division">Marketing Division</option>
                                            <option value="Production Division">Production Division</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" name="last_name" class="form-control" placeholder="Last Name"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label">First Name</label>
                                        <input type="text" name="first_name" class="form-control" placeholder="First Name"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label">M.I.</label>
                                        <input type="text" name="middle_initial" class="form-control" placeholder="M.I."
                                            maxlength="5">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" placeholder="Enter email"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control"
                                            placeholder="Enter password" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Department</label>
                                        <select name="department" id="add_department" class="default-select form-control">
                                            <option value="">Select Department</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Position</label>
                                        <select name="position" id="add_position" class="default-select form-control"
                                            required>
                                            <option value="">Select Position</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="status" id="userActive"
                                                value="1" checked>
                                            <label class="form-check-label" for="userActive">Active User</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="createUserBtn">Create User</button>
                    </div>
                </div>
            </div>
        </div>



        <!-- Edit User Modal -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editUserForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="user_id" id="edit_user_id">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Employee Number</label>
                                        <input type="text" name="employee_number" id="edit_employee_number"
                                            class="form-control" placeholder="e.g. EMP-001" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Division</label>
                                        <select name="division" id="edit_division" class="default-select form-control"
                                            required>
                                            <option value="">Select Division</option>
                                            <option value="All Divisions">All Divisions</option>
                                            <option value="Admin & Finance Division">Admin & Finance Division</option>
                                            <option value="Marketing Division">Marketing Division</option>
                                            <option value="Production Division">Production Division</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" name="last_name" id="edit_last_name" class="form-control"
                                            placeholder="e.g. Doe" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label">First Name</label>
                                        <input type="text" name="first_name" id="edit_first_name" class="form-control"
                                            placeholder="e.g. John" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label">M.I.</label>
                                        <input type="text" name="middle_initial" id="edit_middle_initial"
                                            class="form-control" placeholder="e.g. A" maxlength="5">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" id="edit_email" class="form-control"
                                            placeholder="e.g. john.doe@claretian.com" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Password (leave blank to keep current)</label>
                                        <input type="password" name="password" id="edit_password" class="form-control"
                                            placeholder="Enter new password">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Department</label>
                                        <select name="department" id="edit_department" class="default-select form-control">
                                            <option value="">Select Department</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Position</label>
                                        <select name="position" id="edit_position" class="default-select form-control"
                                            required>
                                            <option value="">Select Position</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="status"
                                                id="edit_userActive" value="1">
                                            <label class="form-check-label" for="edit_userActive">Active User</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="updateUserBtn">Update User</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete User Modal -->
        <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete <strong id="delete_user_name"></strong>?</p>
                        <input type="hidden" id="delete_user_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Success Modal -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title text-white"><i class="las la-check-circle me-2"></i>Success</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <!-- ... -->
                    <div class="modal-body text-center py-5">
                        <i class="las la-check-circle text-success mb-3" style="font-size: 4rem;"></i>
                        <h4 class="mb-2">Success!</h4>
                        <p class="mb-0" id="successMessage">Operation completed successfully.</p>
                    </div>
                    <div class="modal-footer justify-content-center border-0">
                        <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Modal -->
        <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white"><i class="las la-exclamation-circle me-2"></i>Error</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-5">
                        <i class="las la-exclamation-circle text-danger mb-3" style="font-size: 4rem;"></i>
                        <h4 class="mb-2">Error</h4>
                        <p class="mb-0" id="errorMessage">An error occurred.</p>
                    </div>
                    <div class="modal-footer justify-content-center border-0">
                        <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function () {
                // Helper function to show modals
                function showSuccess(message) {
                    // Ensure all other modals are hidden first
                    $('.modal').not('#successModal').modal('hide');

                    // Small delay to allow previous modal backdrop to clear
                    setTimeout(function () {
                        $('#successMessage').text(message);
                        $('#successModal').appendTo('body').modal('show');
                    }, 500);
                }

                function showError(message) {
                    // Ensure all other modals are hidden first
                    $('.modal').not('#errorModal').modal('hide');

                    setTimeout(function () {
                        $('#errorMessage').html(message.replace(/\n/g, '<br>'));
                        $('#errorModal').appendTo('body').modal('show');
                    }, 500);
                }

                // CSRF Token Setup
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Add User
                $('#createUserBtn').click(function () {
                    let formData = new FormData($('#addUserForm')[0]);

                    // Convert checkbox to boolean (1 or 0)
                    formData.set('status', $('#userActive').is(':checked') ? '1' : '0');

                    $.ajax({
                        url: '{{ route("users.store") }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.success) {
                                $('#addUserModal').modal('hide');
                                $('#addUserForm')[0].reset();

                                showSuccess('User created successfully!');

                                // Reload page after success modal closes
                                $('#successModal').on('hidden.bs.modal', function () {
                                    location.reload();
                                });
                            }
                        },
                        error: function (xhr) {
                            let errors = xhr.responseJSON.errors;
                            let errorMessage = 'Error creating user:\n';

                            if (errors) {
                                Object.keys(errors).forEach(function (key) {
                                    errorMessage += errors[key][0] + '\n';
                                });
                            } else {
                                errorMessage += xhr.responseJSON.message || 'Unknown error occurred';
                            }

                            showError(errorMessage);
                        }
                    });
                });

                // Cascading Logic
                const departmentStructure = {
                    "All Divisions": {
                        "All Department": ["Director"]
                    },
                    "Admin & Finance Division": {
                        "Admin & Finance": ["Manager"],
                        "Accounting": ["Senior Accounting Staff", "Accounting Staff", "Inventory Staff", "Cashier"],
                        "Credit and Collection": ["Credit and Collection Staff", "Senior Inventory and Collection Staff", "Inventory and Collection Staff", "Billing Staff"],
                        "MIS": ["MIS Supervisor", "MIS Staff"],
                        "GSD": ["GSD Supervisor"],
                        "HR": ["HR Staff", "Manager"]
                    },
                    "Marketing Division": {
                        "Marketing": ["Manager"],
                        "Area Sales": ["Senior Supervisor", "Account Executive", "Marketing Coordinator"],
                        "Direct Sales": ["Senior Supervisor", "Bookstore Staff", "Bookschain Staff", "Booksales Staff"],
                        "Ads and Promo": ["Marketing Staff"]
                    },
                    "Production Division": {
                        "Editorial Product and Development": ["DTO Supervisor"],
                        "Foreign Order and Rights": ["Senior Ford Staff", "Ford Staff"],
                        "Logistics": ["Senior Logistics Staff", "Logistics Staff", "Driver"],
                        "Printing Services": ["Printing Services Staff"]
                    }
                };

                function populateDepartments(divisionSelect, departmentSelect, selectedDepartment = null) {
                    const division = $(divisionSelect).val();
                    const $deptSelect = $(departmentSelect);
                    $deptSelect.empty().append('<option value="">Select Department</option>');

                    if (division && departmentStructure[division]) {
                        Object.keys(departmentStructure[division]).forEach(dept => {
                            const selected = dept === selectedDepartment ? 'selected' : '';
                            $deptSelect.append(`<option value="${dept}" ${selected}>${dept}</option>`);
                        });
                    }

                    // Refresh Bootstrap Select
                    if (typeof $.fn.selectpicker === 'function') {
                        $deptSelect.selectpicker('refresh');
                    }
                }

                function populatePositions(divisionSelect, departmentSelect, positionSelect, selectedPosition = null) {
                    const division = $(divisionSelect).val();
                    const department = $(departmentSelect).val();
                    const $posSelect = $(positionSelect);
                    $posSelect.empty().append('<option value="">Select Position</option>');

                    if (division && department && departmentStructure[division] && departmentStructure[division][department]) {
                        departmentStructure[division][department].forEach(pos => {
                            const selected = pos === selectedPosition ? 'selected' : '';
                            $posSelect.append(`<option value="${pos}" ${selected}>${pos}</option>`);
                        });
                    }

                    // Refresh Bootstrap Select
                    if (typeof $.fn.selectpicker === 'function') {
                        $posSelect.selectpicker('refresh');
                    }
                }

                // Bind Events for Add Modal
                $('#add_division').change(function () {
                    populateDepartments('#add_division', '#add_department');
                    populatePositions('#add_division', '#add_department', '#add_position');
                });

                $('#add_department').change(function () {
                    populatePositions('#add_division', '#add_department', '#add_position');
                });

                // Bind Events for Edit Modal
                $('#edit_division').change(function () {
                    populateDepartments('#edit_division', '#edit_department');
                    populatePositions('#edit_division', '#edit_department', '#edit_position');
                });

                $('#edit_department').change(function () {
                    populatePositions('#edit_division', '#edit_department', '#edit_position');
                });

                // Edit User - Populate Modal (Updated with cascading trigger and Event Delegation)
                $(document).on('click', '.edit-user-btn', function () {
                    let userId = $(this).data('user-id');
                    let employeeNumber = $(this).data('employee-number');
                    let firstName = $(this).data('first-name');
                    let lastName = $(this).data('last-name');
                    let middleInitial = $(this).data('middle-initial');
                    let email = $(this).data('email');
                    let division = $(this).data('division');
                    let department = $(this).data('department');
                    let position = $(this).data('position');
                    let status = $(this).data('status');

                    $('#edit_user_id').val(userId);
                    $('#edit_employee_number').val(employeeNumber);
                    $('#edit_first_name').val(firstName);
                    $('#edit_last_name').val(lastName);
                    $('#edit_middle_initial').val(middleInitial);
                    $('#edit_email').val(email);
                    $('#edit_division').val(division);

                    // Set Division first and refresh UI
                    $('#edit_division').val(division);
                    if (typeof $.fn.selectpicker === 'function') {
                        $('#edit_division').selectpicker('refresh');
                    }

                    // Manually trigger population logic with selected values
                    // Note: populateDepartments reads the .val() from division, which is now set
                    populateDepartments('#edit_division', '#edit_department', department);
                    $('#edit_department').val(department);
                    if (typeof $.fn.selectpicker === 'function') {
                        $('#edit_department').selectpicker('refresh');
                    }

                    populatePositions('#edit_division', '#edit_department', '#edit_position', position);
                    $('#edit_position').val(position);
                    if (typeof $.fn.selectpicker === 'function') {
                        $('#edit_position').selectpicker('refresh');
                    }

                    // Properly set checkbox state
                    $('#edit_userActive').prop('checked', status == 1);
                });

                // Update User
                $('#updateUserBtn').click(function () {
                    let userId = $('#edit_user_id').val();
                    let formData = $('#editUserForm').serialize();

                    $.ajax({
                        url: '{{ url("/super-admin/users") }}/' + userId,
                        type: 'PUT',
                        data: formData,
                        success: function (response) {
                            if (response.success) {
                                $('#editUserModal').modal('hide');
                                showSuccess('User updated successfully!');

                                // Reload page after success modal closes
                                $('#successModal').on('hidden.bs.modal', function () {
                                    location.reload();
                                });
                            }
                        },
                        error: function (xhr) {
                            let errors = xhr.responseJSON.errors;
                            let errorMessage = 'Error updating user:\n';

                            if (errors) {
                                Object.keys(errors).forEach(function (key) {
                                    errorMessage += errors[key][0] + '\n';
                                });
                            } else {
                                errorMessage += xhr.responseJSON.message || 'Unknown error occurred';
                            }

                            showError(errorMessage);
                        }
                    });
                });

                // Delete User - Populate Modal (Matched with Edit Button logic)
                $(document).on('click', '.delete-user-btn', function () {
                    let userId = $(this).data('user-id');
                    let userName = $(this).data('user-name');

                    $('#delete_user_id').val(userId);
                    $('#delete_user_name').text(userName);
                });

                // Confirm Delete
                $('#confirmDeleteBtn').click(function () {
                    let userId = $('#delete_user_id').val();

                    $.ajax({
                        url: '{{ url("/super-admin/users") }}/' + userId,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            if (response.success) {
                                $('#deleteUserModal').modal('hide');
                                showSuccess('User deleted successfully!');

                                // Reload page after success modal closes
                                $('#successModal').on('hidden.bs.modal', function () {
                                    location.reload();
                                });
                            }
                        },
                        error: function (xhr) {
                            showError('Error deleting user: ' + (xhr.responseJSON.message || 'Unknown error occurred'));
                        }
                    });
                });

                // Frontend Filtering
                function filterUsersTable() {
                    var selectedDivision = $('#filterDivision').val();
                    var selectedPosition = $('#filterPosition').val();

                    $('#userTableBody tr').each(function () {
                        // Check if it's the "No users found" row
                        if ($(this).find('td[colspan="8"]').length > 0) {
                            return; // Skip this row
                        }

                        var rowDivision = $(this).find('td:eq(4)').text().trim();
                        // Position is inside a span in column 6
                        var rowPosition = $(this).find('td:eq(6) span').text().trim();

                        var matchDivision = (selectedDivision === "All Divisions" || rowDivision === selectedDivision);
                        var matchPosition = (selectedPosition === "All Positions" || rowPosition === selectedPosition);

                        if (matchDivision && matchPosition) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });

                    // Check if all user rows are hidden, and show a "No users found" message if needed
                    var visibleRows = $('#userTableBody tr:visible').not('tr:has(td[colspan="8"])').length;
                    if (visibleRows === 0) {
                        if ($('#noUsersRow').length === 0) {
                            $('#userTableBody').append('<tr id="noUsersRow"><td colspan="8" class="text-center">No users match the selected filters.</td></tr>');
                        } else {
                            $('#noUsersRow').show();
                        }
                    } else {
                        $('#noUsersRow').hide();
                    }
                }

                // Bind change events to the filter dropdowns
                $('#filterDivision, #filterPosition').on('change', function () {
                    filterUsersTable();
                });

                // Trigger initial filter on load in case there are defaults
                filterUsersTable();
            });
        </script>
    @endpush
</x-app-layout>