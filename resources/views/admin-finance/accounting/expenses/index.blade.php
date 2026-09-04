<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        /* Widescreen Spacing Override */
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
        }

        /* Modern Table Style (Clean, borderless outside) */
        .table-responsive {
            border: none !important;
        }
        .table-modern {
            border-collapse: collapse !important;
        }
        .table-modern thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
            padding: 12px 16px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
        }
        .table-modern tbody td {
            padding: 12px 16px !important;
            color: #475569 !important;
            font-size: 0.84rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
        }
        .table-modern tbody tr {
            transition: all 0.15s ease-in-out !important;
        }
        .table-modern tbody tr:hover {
            background-color: #f8fafc !important;
        }
        .text-deep-black {
            color: #0f172a !important;
            font-weight: 600 !important;
        }

        /* Custom Capsule Pagination styling */
        .pagination .page-item.active .page-link {
            background-color: #D9251C !important;
            border-color: #D9251C !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15) !important;
            border-radius: 4px !important;
        }
        .pagination .page-link {
            color: #475569 !important;
            border-color: #cbd5e1 !important;
            padding: 8px 14px !important;
            font-size: 0.85rem !important;
            transition: all 0.15s ease-in-out !important;
            background-color: #ffffff !important;
            border-radius: 4px !important;
            margin: 0 2px !important;
        }
        .pagination .page-link:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        /* Branded Action Buttons */
        .btn-brand-red {
            background-color: #D9251C !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border: none !important;
            transition: opacity 0.2s ease;
        }
        .btn-brand-red:hover {
            opacity: 0.9 !important;
            color: #ffffff !important;
        }

        /* Modal Reference Overrides (Accounts Payable Style) */
        .modal-content {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }

        .modal-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 16px 24px !important;
        }

        .modal-header .modal-title {
            font-size: 0.95rem !important;
            font-weight: 700 !important;
            color: #000000 !important;
        }

        /* Modal Form styling overrides */
        .modal-body label.form-label {
            color: #475569 !important;
            font-weight: 600 !important;
            font-size: 0.72rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            margin-bottom: 6px !important;
            display: inline-block;
        }

        .modal-body .form-control,
        .modal-body .form-select {
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            font-size: 13px !important;
            padding: 8px 12px !important;
            color: #000000 !important;
            background-color: #ffffff !important;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
        }

        .modal-body .form-control:focus,
        .modal-body .form-select:focus {
            border-color: #D9251C !important;
            box-shadow: 0 0 0 0.2rem rgba(217, 37, 28, 0.15) !important;
            outline: 0 !important;
        }

        .modal-footer {
            border-top: 1px solid #f1f5f9 !important;
            background-color: #f8fafc !important;
            padding: 14px 24px !important;
        }
    </style>
    @endpush

    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;">
                    <!-- Card Header -->
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fs-20 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Expense Management</h4>
                            <p class="text-muted small mb-0">Record, track, and filter accounting division and departmental expenses.</p>
                        </div>
                        <button class="btn btn-brand-red btn-sm px-4 py-2 d-flex align-items-center gap-2 shadow-sm" 
                                style="border-radius: 4px; font-size: 0.88rem; height: 38px;"
                                data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                            <i class="fas fa-plus"></i> Record Expense
                        </button>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Filters -->
                        <form action="{{ route('admin-finance.accounting.expenses.index') }}" method="GET" class="d-flex justify-content-between align-items-center mb-4 gap-3">
                            <!-- Left side: Select filters -->
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 250px;">
                                    <select name="dept_id" id="dept_id" class="form-select form-select-sm" style="height: 38px; border-color: #cbd5e1; border-radius: 4px; font-size: 0.85rem;" onchange="this.form.submit()">
                                        <option value="">-- All Departments --</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->dept_id }}" {{ $dept_id == $dept->dept_id ? 'selected' : '' }}>{{ $dept->dept_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Right side: Merged Search box and controls -->
                            <div class="d-flex gap-2 align-items-center">
                                <div class="input-group input-group-sm" style="width: 280px;">
                                    <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 10px; border-top-left-radius: 4px; border-bottom-left-radius: 4px;">
                                        <i class="las la-search text-muted fs-16"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by expense title..." value="{{ $search ?? '' }}" style="height: 38px; border-color: #cbd5e1; border-top-right-radius: 4px; border-bottom-right-radius: 4px; font-size: 0.82rem; padding-left: 0; outline: none; box-shadow: none;">
                                </div>
                                <button type="submit" class="btn btn-brand-red btn-sm text-white px-3 font-w600" style="height: 38px; border-radius: 4px;">Search</button>
                                @if($search || $dept_id)
                                    <a href="{{ route('admin-finance.accounting.expenses.index') }}" class="btn btn-light btn-sm border px-3 d-flex align-items-center" style="height: 38px; border-radius: 4px;">Clear</a>
                                @endif
                            </div>
                        </form>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-modern align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 140px;">Posting Date</th>
                                        <th>Expense Title / Description</th>
                                        <th style="width: 200px;">Department</th>
                                        <th class="text-end" style="width: 160px;">Amount</th>
                                        <th style="width: 160px;">Recorded By</th>
                                        <th>Remarks / Notes</th>
                                        <th class="text-end" style="width: 180px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expenses as $expense)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') }}</td>
                                        <td class="text-deep-black">{{ $expense->title }}</td>
                                        <td>
                                            @if($expense->department)
                                                <span class="badge bg-light text-dark border px-2.5 py-1 fw-bold fs-12" style="border-radius: 4px;">
                                                    {{ $expense->department->dept_name }}
                                                </span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end text-deep-black text-danger">₱{{ number_format($expense->amount, 2) }}</td>
                                        <td>{{ $expense->addedBy->name ?? 'N/A' }}</td>
                                        <td class="text-muted small" title="{{ $expense->notes }}">{{ $expense->notes ?: '—' }}</td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-warning shadow btn-xs sharp me-1 text-white edit-btn"
                                                        data-bs-toggle="modal" data-bs-target="#editExpenseModal"
                                                        data-id="{{ $expense->id }}"
                                                        data-title="{{ $expense->title }}"
                                                        data-amount="{{ $expense->amount }}"
                                                        data-date="{{ $expense->expense_date }}"
                                                        data-dept="{{ $expense->department_id }}"
                                                        data-notes="{{ $expense->notes }}"
                                                        title="Edit">
                                                    <i class="las la-pen"></i>
                                                </button>
                                                
                                                <button type="button" class="btn btn-danger shadow btn-xs sharp"
                                                        onclick="confirmDelete({{ $expense->id }})"
                                                        title="Delete">
                                                    <i class="las la-trash"></i>
                                                </button>
                                                
                                                <form id="delete-form-{{ $expense->id }}" action="{{ route('admin-finance.accounting.expenses.destroy', $expense->id) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <div class="mb-3"><i class="fas fa-file-invoice-dollar fs-40 text-light"></i></div>
                                            <span class="fs-15">No expense records found matching your filters.</span>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Links -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted small">
                                Showing {{ $expenses->firstItem() ?? 0 }} to {{ $expenses->lastItem() ?? 0 }} of {{ $expenses->total() }} expenses
                            </div>
                            <div id="paginationContainer" class="pe-0">
                                {{ $expenses->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="addExpenseModalLabel"><i class="las la-file-invoice-dollar me-2 text-danger"></i>Record Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin-finance.accounting.expenses.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="title" class="form-label">Expense Title / Description <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="e.g. Broadband Subscription" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">Amount (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="expense_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="expense_date" name="expense_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select" id="department_id" name="department_id" required>
                                <option value="">-- Select Department --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Remarks / Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional details..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="font-weight: 600;">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4" style="background-color: #D9251C !important; border-color: #D9251C !important; color: #ffffff !important; font-weight: 600; border-radius: 4px;">Record Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="editExpenseModalLabel"><i class="las la-edit me-2 text-danger"></i>Edit Expense Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editExpenseForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Expense Title / Description <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_amount" class="form-label">Amount (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" class="form-control" id="edit_amount" name="amount" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_expense_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_expense_date" name="expense_date" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_department_id" class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_department_id" name="department_id" required>
                                <option value="">-- Select Department --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_notes" class="form-label">Remarks / Notes</label>
                            <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="font-weight: 600;">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4" style="background-color: #D9251C !important; border-color: #D9251C !important; color: #ffffff !important; font-weight: 600; border-radius: 4px;">Update Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Edit modal data population
            $('.edit-btn').on('click', function() {
                const id = $(this).data('id');
                const title = $(this).data('title');
                const amount = $(this).data('amount');
                const date = $(this).data('date');
                const dept = $(this).data('dept');
                const notes = $(this).data('notes');
                
                $('#edit_title').val(title);
                $('#edit_amount').val(amount);
                $('#edit_expense_date').val(date);
                $('#edit_department_id').val(dept);
                $('#edit_notes').val(notes);
                
                // Update form action URL dynamically
                const route = "{{ route('admin-finance.accounting.expenses.update', ':id') }}";
                $('#editExpenseForm').attr('action', route.replace(':id', id));
            });
        });

        // Use custom confirm utility from template
        function confirmDelete(id) {
            window.showConfirm("Are you sure you want to delete this expense record?", function() {
                document.getElementById('delete-form-' + id).submit();
            });
        }
    </script>
    @endpush
</x-app-layout>
