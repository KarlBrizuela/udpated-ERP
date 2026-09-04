<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Journal Voucher Requests</h4>
                    <p class="mb-0">Summary Reports & JV Applications</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <a href="{{ route('admin-finance.credit-collection.jv-requests.create') }}" class="btn btn-primary btn-sm shadow">
                    <i class="las la-plus me-1"></i> New JV Request
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-responsive-sm">
                                <thead>
                                    <tr>
                                        <th>JV Number</th>
                                        <th>Date</th>
                                        <th>Requested By</th>
                                        <th>Category</th>
                                        <th class="text-end">Total Amount</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $req)
                                    <tr>
                                        <td class="fw-bold text-primary">#{{ $req->jv_number }}</td>
                                        <td>{{ \Carbon\Carbon::parse($req->date)->format('M d, Y') }}</td>
                                        <td>{{ $req->requestor->name ?? 'Unknown' }}</td>
                                        <td><span class="badge badge-outline-info">{{ $req->category }}</span></td>
                                        <td class="text-end fw-bold">₱ {{ number_format($req->total_amount, 2) }}</td>
                                        <td>
                                            @if($req->status == 'pending_accounting')
                                                <span class="badge badge-warning light text-dark">Pending Accounting</span>
                                            @elseif($req->status == 'accounting_verified')
                                                <span class="badge badge-success light">Verified by Accounting</span>
                                            @elseif($req->status == 'posted')
                                                <span class="badge badge-primary">Posted to GL</span>
                                            @else
                                                <span class="badge badge-dark light">{{ ucfirst($req->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin-finance.credit-collection.jv-requests.show', $req->id) }}" class="btn btn-info btn-xs shadow px-2">
                                                <i class="las la-eye"></i> View Details
                                            </a>
                                            <a href="{{ route('admin-finance.credit-collection.jv-requests.print', $req->id) }}" target="_blank" class="btn btn-dark btn-xs shadow px-2">
                                                <i class="las la-print"></i> Print JV
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @if($requests->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No JV requests found.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
