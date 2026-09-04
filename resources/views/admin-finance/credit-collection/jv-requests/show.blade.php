<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0 d-flex align-items-center">
                <a href="{{ route('admin-finance.credit-collection.billing', ['tab' => 'jv']) }}" class="btn btn-outline-primary btn-xxs me-3"><i class="las la-arrow-left"></i></a>
                <div class="welcome-text">
                    <h4>Compilation #{{ $request->jv_number }}</h4>
                    <p class="mb-0">Summary Report Compilation</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex gap-2 flex-wrap">
                <!-- Step 3: Always available after creation -->
                <a href="{{ route('admin-finance.credit-collection.jv-requests.print-summary', $request->id) }}" target="_blank" class="btn btn-outline-dark btn-sm shadow-sm">
                    <i class="las la-file-alt me-1"></i> Print Summary Report
                </a>

                <!-- Approval: For Accounting (Admin & Finance Manager only) -->
                @php $pos = auth()->user()->position ?? ''; @endphp
                @if($request->status == 'pending_accounting' && (strpos($pos, 'Manager') !== false || $pos == 'Super Admin'))
                <form action="{{ route('admin-finance.credit-collection.jv-requests.approve', $request->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-primary btn-sm shadow">
                        <i class="las la-check-circle me-1"></i> Approve Compilation (Accounting)
                    </button>
                </form>
                @endif

                <!-- Next Stage: After Accounting Approval -> Staff Preparation -->
                @if($request->status == 'accounting_verified')
                <a href="{{ route('admin-finance.credit-collection.jv-requests.prepare-adjustment', $request->id) }}" class="btn btn-info btn-sm shadow text-white">
                    <i class="las la-magic me-1"></i> Phase 2: Prepare Adjustment Form
                </a>
                @endif

                <!-- Manager Approval Stage -->
                @if($request->status == 'pending_manager_approval' && (strpos($pos, 'Manager') !== false || $pos == 'Super Admin'))
                <form action="{{ route('admin-finance.credit-collection.jv-requests.manager-approve', $request->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-primary btn-sm shadow">
                        <i class="las la-signature me-1"></i> Final Manager Approval
                    </button>
                </form>
                <a href="{{ route('admin-finance.credit-collection.jv-requests.prepare-adjustment', $request->id) }}" class="btn btn-outline-info btn-sm shadow-sm">
                    <i class="las la-edit me-1"></i> Edit Details
                </a>
                @endif

                <!-- Final Stage: Ready to Print -->
                @if($request->status == 'ready_to_print' || $request->status == 'posted')
                <a href="{{ route('admin-finance.credit-collection.jv-requests.print', $request->id) }}" target="_blank" class="btn btn-success btn-sm shadow">
                    <i class="las la-print me-1"></i> Print Blue Form (Official)
                </a>
                @endif
            </div>
        </div>


        <div class="row">
            <div class="col-xl-9">
                <div class="card h-auto">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Compilation Details</h5>
                        <span class="badge badge-outline-info">{{ $request->category }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <thead class="bg-primary-light">
                                    <tr class="text-dark fw-bold">
                                        <th class="py-2 text-center" style="width: 50px">#</th>
                                        <th class="py-2">Reference No.</th>
                                        <th class="py-2">Entity Name</th>
                                        <th class="py-2 text-end">Amount</th>
                                        <th class="py-2">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 1; @endphp
                                    @foreach($request->items as $item)
                                    <tr>
                                        <td class="text-center">{{ $i++ }}</td>
                                        <td class="fw-medium">{{ $item->reference_no }}</td>
                                        <td>{{ $item->customer_name }}</td>
                                        <td class="text-end fw-bold">₱ {{ number_format($item->amount, 2) }}</td>
                                        <td class="text-muted small italic">{{ $item->remarks }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr class="fw-bold fs-16">
                                        <td colspan="3" class="text-end py-3">GRAND TOTAL: </td>
                                        <td class="text-end text-primary py-3">₱ {{ number_format($request->total_amount, 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                <div class="card h-auto">
                    <div class="card-header"><h5 class="card-title">Metadata</h5></div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Date Prepared:</strong>
                                <span>{{ \Carbon\Carbon::parse($request->date)->format('M d, Y') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Prepared By:</strong>
                                <span>{{ $request->requestor->name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Verified By (Acctg):</strong>
                                <span>{{ $request->approver->name ?? '---' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Approved By (FM):</strong>
                                <span>{{ $request->managerApprover->name ?? '---' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Status:</strong>
                                @if($request->status == 'pending_accounting')
                                    <span class="badge badge-warning light text-dark">Pending Accounting Review</span>
                                @elseif($request->status == 'approved')
                                    <span class="badge badge-success light">Verified by Accounting</span>
                                @elseif($request->status == 'pending_manager_approval')
                                    <span class="badge badge-info light">Awaiting Manager Approval</span>
                                @elseif($request->status == 'ready_to_print')
                                    <span class="badge badge-primary">Ready to Print</span>
                                @else
                                    <span class="badge badge-secondary light">{{ strtoupper(str_replace('_', ' ', $request->status)) }}</span>
                                @endif
                            </li>
                            @if($request->supporting_documents)
                            <li class="list-group-item px-0">
                                <strong>Supporting Documents:</strong>
                                <div class="mt-2">
                                    <a href="{{ route('admin-finance.credit-collection.jv-requests.download-supporting', $request->id) }}" class="btn btn-outline-primary btn-xxs shadow-sm w-100">
                                        <i class="las la-download me-1"></i> Download Attachments
                                    </a>
                                </div>
                            </li>
                            @endif
                        </ul>
                        <div class="mt-4">
                            <h6 class="fw-bold small text-muted text-uppercase mb-2">Reason for Adjustment:</h6>
                            <p class="small bg-light p-2 rounded border">{{ $request->reason ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
