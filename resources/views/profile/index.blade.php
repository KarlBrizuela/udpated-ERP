<x-app-layout title="User Profile" role="{{ auth()->user()->position }}" :sidebar="$sidebar">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title">User Profile</h4>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center border-end">
                            <div class="profile-photo mb-4 mt-3">
                                <img src="{{ asset('images/profile/8.jpg') }}" class="img-fluid rounded-circle shadow" alt="" style="width: 160px; height: 160px; object-fit: cover; border: 4px solid #fff;">
                            </div>
                            <h3 class="mb-1 text-black fw-bold">{{ auth()->user()->name }}</h3>
                            <p class="text-muted mb-2"><i class="fas fa-briefcase me-2 text-primary"></i>{{ auth()->user()->position ?? 'No Position Assigned' }}</p>
                        </div>
                        <div class="col-md-8">
                            <div class="profile-personal-info ps-md-4">
                                <h4 class="text-primary mb-4 pb-2 border-bottom">Work Information</h4>
                                <div class="row mb-3">
                                    <div class="col-sm-4 col-5">
                                        <h5 class="f-w-500 mb-0"><i class="fas fa-id-card me-2 text-muted"></i>Employee # <span class="pull-right">:</span></h5>
                                    </div>
                                    <div class="col-sm-8 col-7"><span>{{ auth()->user()->employee_number ?? 'N/A' }}</span></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 col-5">
                                        <h5 class="f-w-500 mb-0"><i class="fas fa-sitemap me-2 text-muted"></i>Division <span class="pull-right">:</span></h5>
                                    </div>
                                    <div class="col-sm-8 col-7"><span>{{ auth()->user()->division ?? 'Not Assigned' }}</span></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 col-5">
                                        <h5 class="f-w-500 mb-0"><i class="fas fa-building me-2 text-muted"></i>Department <span class="pull-right">:</span></h5>
                                    </div>
                                    <div class="col-sm-8 col-7"><span>{{ auth()->user()->department ?? 'Not Assigned' }}</span></div>
                                </div>

                                <h4 class="text-primary mb-4 pb-2 border-bottom mt-5">Account Information</h4>
                                <div class="row mb-4 align-items-center">
                                    <div class="col-sm-4 col-5">
                                        <h5 class="f-w-500 mb-0"><i class="fas fa-envelope me-2 text-muted"></i>Email <span class="pull-right">:</span></h5>
                                    </div>
                                    <div class="col-sm-8 col-7 d-flex justify-content-between align-items-center">
                                        <span>{{ auth()->user()->email }}</span>
                                        @if(auth()->user()->position === 'Director' || auth()->user()->position === 'Super Admin')
                                        <form action="{{ route('profile.test-email') }}" method="POST" class="ms-2">
                                            @csrf
                                            <button type="submit" class="btn btn-info btn-xs shadow" title="Send a test email to verify SMTP settings">
                                                <i class="fas fa-paper-plane me-1"></i> Test Email
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h5 class="text-primary mb-3">Change Password</h5>
                                        <form action="{{ route('profile.change-password') }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Current Password</label>
                                                    <input type="password" name="current_password" class="form-control" required>
                                                    @error('current_password')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">New Password</label>
                                                    <input type="password" name="new_password" class="form-control" required>
                                                    @error('new_password')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Confirm New Password</label>
                                                    <input type="password" name="new_password_confirmation" class="form-control" required>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm mt-2">Update Password</button>
                                            
                                            @if(session('success'))
                                                <div class="alert alert-success mt-3 solid alert-dismissible fade show">
                                                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                                                    <strong>Success!</strong> {{ session('success') }}
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                                                </div>
                                            @endif
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if(count($approvals) > 0)
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title">Pending Approvals</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-sm">
                            <thead>
                                <tr>
                                    <th>Document Type</th>
                                    <th>Reference #</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($approvals as $approval)
                                <tr>
                                    <td>{{ $approval['document_type'] }}</td>
                                    <td>{{ $approval['reference_no'] }}</td>
                                    <td>{{ $approval['date'] }}</td>
                                    <td>{{ $approval['description'] }}</td>
                                    <td><span class="badge badge-warning">{{ ucwords(str_replace('_', ' ', $approval['status'])) }}</span></td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ $approval['link'] }}" class="btn btn-primary shadow btn-xs sharp" title="View Details"><i class="fas fa-eye"></i></a>
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
    @endif
    @if(auth()->user()->position === 'Manager' || auth()->user()->position === 'MIS Supervisor' || auth()->user()->position === 'Super Admin')
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title">Pending Job Orders</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-sm">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Requested By</th>
                                    <th>Details</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingJobOrders as $order)
                                <tr>
                                    <td>
                                        @if($order['type'] === 'CCTV')
                                            <span class="badge bg-info">CCTV Request</span>
                                        @elseif($order['type'] === 'Material')
                                            <span class="badge bg-success">Material Request</span>
                                        @elseif($order['type'] === 'QB')
                                            <span class="badge bg-warning">QB Request</span>
                                        @elseif($order['type'] === 'Service')
                                            <span class="badge bg-primary">Service Request</span>
                                        @elseif($order['type'] === 'Undertime')
                                            <span class="badge bg-secondary">Undertime Request</span>
                                        @endif
                                    </td>
                                    <td>{{ $order['date'] }}</td>
                                    <td>{{ $order['requested_by'] }}</td>
                                    <td>{{ $order['details'] }}</td>
                                    <td>
                                        <div class="d-flex">
                                            @if(in_array($order['type'], ['QB', 'Service', 'Undertime']))
                                                <button type="button" class="btn btn-primary shadow btn-xs sharp me-1" data-bs-toggle="modal" data-bs-target="#viewModal{{ $order['type'] }}{{ $order['id'] }}" title="View"><i class="fas fa-eye"></i></button>
                                            @else
                                                <a href="{{ route('admin-finance.mis.job-orders') }}" class="btn btn-primary shadow btn-xs sharp me-1" title="View"><i class="fas fa-eye"></i></a>
                                            @endif

                                            @if($order['type'] === 'CCTV')
                                                <form action="{{ route('admin-finance.mis.cctv-requests.update', $order['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Approve"><i class="fas fa-check"></i></button>
                                                </form>
                                                <form action="{{ route('admin-finance.mis.cctv-requests.update', $order['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-danger shadow btn-xs sharp" title="Reject"><i class="fas fa-times"></i></button>
                                                </form>
                                            @elseif($order['type'] === 'Material')
                                                <form action="{{ route('admin-finance.mis.material-requests.update', $order['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Approve"><i class="fas fa-check"></i></button>
                                                </form>
                                                <form action="{{ route('admin-finance.mis.material-requests.update', $order['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-danger shadow btn-xs sharp" title="Reject"><i class="fas fa-times"></i></button>
                                                </form>
                                            @elseif($order['type'] === 'QB')
                                                <form action="{{ route('admin-finance.mis.qb-requests.update', $order['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Approve"><i class="fas fa-check"></i></button>
                                                </form>
                                                <form action="{{ route('admin-finance.mis.qb-requests.update', $order['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-danger shadow btn-xs sharp" title="Reject"><i class="fas fa-times"></i></button>
                                                </form>
                                            @elseif($order['type'] === 'Service')
                                                <form action="{{ route('admin-finance.mis.service-requests.update', $order['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Approve"><i class="fas fa-check"></i></button>
                                                </form>
                                                <form action="{{ route('admin-finance.mis.service-requests.update', $order['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-danger shadow btn-xs sharp" title="Reject"><i class="fas fa-times"></i></button>
                                                </form>
                                            @elseif($order['type'] === 'Undertime')
                                                <form action="{{ route('admin-finance.mis.undertime-requests.update', $order['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Approve"><i class="fas fa-check"></i></button>
                                                </form>
                                                <form action="{{ route('admin-finance.mis.undertime-requests.update', $order['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-danger shadow btn-xs sharp" title="Reject"><i class="fas fa-times"></i></button>
                                                </form>
                                            @endif
                                        </div>

                                        {{-- Modals for QB, Service, Undertime --}}
                                        @if($order['type'] === 'QB')
                                            <div class="modal fade" id="viewModalQB{{ $order['id'] }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">QB Request Details</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3"><strong>Requested By:</strong> {{ $order['requested_by'] }}</div>
                                                                <div class="col-md-6 mb-3"><strong>Date:</strong> {{ $order['date'] }}</div>
                                                                <div class="col-md-12 mb-3"><strong>Customer Item Name:</strong> {{ $order['original']->customer_item_name }}</div>
                                                                
                                                                @if($order['original']->items && $order['original']->items->count() > 0)
                                                                <div class="col-md-12">
                                                                    <h6>Items Changes:</h6>
                                                                    <table class="table table-bordered table-sm">
                                                                        <thead><tr><th>From</th><th>To</th></tr></thead>
                                                                        <tbody>
                                                                            @foreach($order['original']->items as $item)
                                                                            <tr>
                                                                                <td>{{ $item->from_value }}</td>
                                                                                <td>{{ $item->to_value }}</td>
                                                                            </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer justify-content-between">
                                                            <div>
                                                                <form action="{{ route('admin-finance.mis.qb-requests.update', $order['id']) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status" value="approved">
                                                                    <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Approve"><i class="fas fa-check"></i></button>
                                                                </form>
                                                                <form action="{{ route('admin-finance.mis.qb-requests.update', $order['id']) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status" value="rejected">
                                                                    <button type="submit" class="btn btn-danger shadow btn-xs sharp" title="Reject"><i class="fas fa-times"></i></button>
                                                                </form>
                                                            </div>
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($order['type'] === 'Service')
                                            <div class="modal fade" id="viewModalService{{ $order['id'] }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Service Request Details</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <div class="mb-3"><strong>Requestor Name:</strong> {{ $order['original']->requestor_name }}</div>
                                                            <div class="mb-3"><strong>Date:</strong> {{ $order['original']->date }}</div>
                                                            <div class="mb-3"><strong>Nature of Request:</strong> <p>{{ $order['original']->nature_of_request }}</p></div>
                                                        </div>
                                                        <div class="modal-footer justify-content-between">
                                                            <div>
                                                                <form action="{{ route('admin-finance.mis.service-requests.update', $order['id']) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status" value="approved">
                                                                    <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Approve"><i class="fas fa-check"></i></button>
                                                                </form>
                                                                <form action="{{ route('admin-finance.mis.service-requests.update', $order['id']) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status" value="rejected">
                                                                    <button type="submit" class="btn btn-danger shadow btn-xs sharp" title="Reject"><i class="fas fa-times"></i></button>
                                                                </form>
                                                            </div>
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($order['type'] === 'Undertime')
                                            <div class="modal fade" id="viewModalUndertime{{ $order['id'] }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Undertime Request Details</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <div class="mb-3"><strong>Employee Name:</strong> {{ $order['original']->employee_name }}</div>
                                                            <div class="mb-3"><strong>Date:</strong> {{ $order['original']->date }}</div>
                                                            <div class="row mb-3">
                                                                <div class="col-6"><strong>Time From:</strong> {{ $order['original']->time_from }}</div>
                                                                <div class="col-6"><strong>Time To:</strong> {{ $order['original']->time_to }}</div>
                                                            </div>
                                                            <div class="mb-3"><strong>Reason:</strong> <p>{{ $order['original']->reason }}</p></div>
                                                        </div>
                                                        <div class="modal-footer justify-content-between">
                                                            <div>
                                                                <form action="{{ route('admin-finance.mis.undertime-requests.update', $order['id']) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status" value="approved">
                                                                    <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Approve"><i class="fas fa-check"></i></button>
                                                                </form>
                                                                <form action="{{ route('admin-finance.mis.undertime-requests.update', $order['id']) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status" value="rejected">
                                                                    <button type="submit" class="btn btn-danger shadow btn-xs sharp" title="Reject"><i class="fas fa-times"></i></button>
                                                                </form>
                                                            </div>
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No pending job orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
