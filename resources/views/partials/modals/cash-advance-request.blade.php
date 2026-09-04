<!-- Cash Advance Request Modal -->
<div class="modal fade" id="createCashAdvanceModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="cashAdvanceForm" action="{{ route('employee.cash-advance.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">New Cash Advance Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Name</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Employee ID</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->employee_number }}" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Department</label>
                            <select name="department" class="form-control" required>
                                <option value="Direct" {{ old('department', auth()->user()->department) === 'Direct' ? 'selected' : '' }}>Direct</option>
                                <option value="MIS" {{ old('department', auth()->user()->department) === 'MIS' ? 'selected' : '' }}>MIS</option>
                                <option value="GSD" {{ old('department', auth()->user()->department) === 'GSD' ? 'selected' : '' }}>GSD</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Position</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->position }}" readonly disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Amount (PhP)</label>
                            <input type="number" name="amount" class="form-control" step="0.01" required placeholder="0.00" min="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date Needed</label>
                            <input type="date" name="date_needed" class="form-control" required min="{{ date('Y-m-d') }}">
                        </div>


                        <div class="col-12">
                            <label class="form-label fw-bold">Purpose of Advance</label>
                            <textarea name="purpose" class="form-control" rows="4" required placeholder="State the purpose of your cash advance request..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
