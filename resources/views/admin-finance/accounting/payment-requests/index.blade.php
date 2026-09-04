<x-app-layout :title="$title" :sidebar="$sidebar">
    <div class="container-fluid">
        <!-- Success/Error Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="las la-check-circle me-2"></i>
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="las la-exclamation-triangle me-2"></i>
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Summary Statistics Card -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-left-success" style="border-left: 4px solid #28a745;">
                    <div class="card-body">
                        <h5 class="text-success"><i class="las la-check-double"></i> Ready to Schedule</h5>
                        <h3>{{ $requests->where('status', 'approved')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-left-primary" style="border-left: 4px solid #007bff;">
                    <div class="card-body">
                        <h5 class="text-primary"><i class="las la-calendar-day"></i> Scheduled Payments</h5>
                        <h3>{{ $requests->where('status', 'scheduled')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-left-info" style="border-left: 4px solid #17a2b8;">
                    <div class="card-body">
                        <h5 class="text-info"><i class="las la-receipt"></i> Completed Payments</h5>
                        <h3>{{ $requests->where('status', 'paid')->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="card shadow">
            <div class="card-header border-0 pb-0">
                <h4 class="fs-20 mb-0 text-black">Payment Requests Processing</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="accountingPaymentRequestsTable" class="display table table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Ref #</th>
                                <th>Date Created</th>
                                <th>Submitted By</th>
                                <th>Pay To</th>
                                <th>PO #</th>
                                <th>Total Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $req)
                                <tr>
                                    <td><strong>PR-{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                    <td>{{ $req->date ? $req->date->format('Y-m-d') : $req->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $req->requester->name ?? 'N/A' }}</td>
                                    <td>{{ $req->payment_to }}</td>
                                    <td>{{ $req->po_number ?? 'N/A' }}</td>
                                    <td>PhP {{ number_format($req->total_amount, 2) }}</td>
                                    <td>{{ $req->due_date ? $req->due_date->format('Y-m-d') : 'N/A' }}</td>
                                    <td>
                                        @php
                                            $status = $req->status;
                                            $badge = 'success';
                                            $statusText = 'Ready for Schedule';
                                            if ($status === 'scheduled') {
                                                $badge = 'primary';
                                                $statusText = 'Scheduled (' . ($req->scheduled_payment_date ? $req->scheduled_payment_date->format('Y-m-d') : '') . ')';
                                            } elseif ($status === 'paid') {
                                                $badge = 'success';
                                                $statusText = 'Paid / Settled';
                                            }
                                        @endphp
                                        <span class="badge badge-{{ $badge }}">{{ $statusText }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('payment-requests.show', $req->id) }}" class="btn btn-info btn-xs" title="View Request Details">
                                                <i class="las la-eye"></i> View
                                            </a>
                                            
                                            @if($req->status === 'approved')
                                                <button type="button" class="btn btn-success btn-xs" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#schedulePaymentModal" 
                                                        data-id="{{ $req->id }}" 
                                                        data-ref="PR-{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}"
                                                        data-amount="PhP {{ number_format($req->total_amount, 2) }}"
                                                        data-payto="{{ $req->payment_to }}"
                                                        data-due="{{ $req->due_date ? $req->due_date->format('Y-m-d') : '' }}">
                                                    <i class="las la-calendar-plus"></i> Schedule
                                                </button>
                                            @endif

                                            @if($req->status === 'scheduled')
                                                <form action="{{ route('admin-finance.accounting.payment-requests.pay', $req->id) }}" method="POST" onsubmit="return confirm('Mark this request as Paid?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-xs">
                                                        <i class="las la-check-double"></i> Mark Paid
                                                    </button>
                                                </form>
                                            @endif
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

    <!-- Schedule Payment Modal -->
    <div class="modal fade" id="schedulePaymentModal" tabindex="-1" aria-labelledby="schedulePaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white" style="background: #28a745;">
                    <h5 class="modal-title text-white" id="schedulePaymentModalLabel">
                        <i class="las la-calendar-plus me-2"></i>Schedule Payment Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="schedulePaymentForm" method="POST" action="">
                    @csrf
                    <div class="modal-body">
                        <!-- Request Details Block -->
                        <div class="p-3 mb-3 bg-light rounded border">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <small class="text-muted d-block">Reference</small>
                                    <strong id="modalRefNo">-</strong>
                                </div>
                                <div class="col-6 mb-2">
                                    <small class="text-muted d-block">Total Amount</small>
                                    <strong id="modalAmount" class="text-success">-</strong>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">Payee</small>
                                    <strong id="modalPayTo">-</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Form Input Fields -->
                        <div class="mb-3">
                            <label for="scheduled_payment_date" class="form-label font-weight-bold">Scheduled Payment Date:</label>
                            <input type="date" class="form-control" name="scheduled_payment_date" id="scheduled_payment_date" required>
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label font-weight-bold">Payment Method:</label>
                            <select class="form-control" name="payment_method" id="payment_method" required>
                                <option value="" disabled selected>-- Select Method --</option>
                                <option value="Check">Check</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cash">Cash</option>
                                <option value="Auto Debit">Auto Debit</option>
                                <option value="E-Ford Payout">E-Ford Payout</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="payment_reference" class="form-label font-weight-bold">Reference / Check #:</label>
                            <input type="text" class="form-control" name="payment_reference" id="payment_reference" placeholder="Check number or bank reference number">
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label font-weight-bold">Accounting Remarks / Notes:</label>
                            <textarea class="form-control" name="remarks" id="remarks" rows="2" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Schedule Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .border-left-success { border-left: 4px solid #28a745 !important; }
        .border-left-primary { border-left: 4px solid #007bff !important; }
        .border-left-info { border-left: 4px solid #17a2b8 !important; }
        .btn-xs {
            padding: 0.35rem 0.65rem;
            font-size: 0.875rem;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#accountingPaymentRequestsTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 25
            });

            // Handle data feeding into the Modal
            const scheduleModal = document.getElementById('schedulePaymentModal');
            if (scheduleModal) {
                scheduleModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const ref = button.getAttribute('data-ref');
                    const amount = button.getAttribute('data-amount');
                    const payto = button.getAttribute('data-payto');
                    const due = button.getAttribute('data-due');

                    // Set preview text
                    document.getElementById('modalRefNo').textContent = ref;
                    document.getElementById('modalAmount').textContent = amount;
                    document.getElementById('modalPayTo').textContent = payto;
                    
                    // Autofill date from Due date if present
                    if(due) {
                        document.getElementById('scheduled_payment_date').value = due;
                    } else {
                        // Default to today
                        document.getElementById('scheduled_payment_date').value = new Date().toISOString().split('T')[0];
                    }

                    // Dynamically set action URL
                    const actionUrl = `{{ url('admin-finance/accounting/payment-requests') }}/${id}/schedule`;
                    document.getElementById('schedulePaymentForm').setAttribute('action', actionUrl);
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
