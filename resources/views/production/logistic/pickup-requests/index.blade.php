<x-app-layout :title="$title ?? 'Logistics Service Orders'" :sidebar="$sidebar ?? 'production'">
    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .request-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border-left: 5px solid;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.08);
        }
        .stat-card.pending { border-left-color: #ffc107; }
        .stat-card.approved { border-left-color: #0d6efd; }
        .stat-card.rejected { border-left-color: #dc3545; }
        .stat-card.completed { border-left-color: #198754; }

        .stat-card h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: #333;
        }
        .stat-card p {
            margin: 0;
            color: #6c757d;
            font-weight: 500;
            font-size: 0.9rem;
        }
        .badge-type {
            font-size: 11px;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 600;
        }
        .badge-delivery { background-color: #e3f2fd; color: #0d6efd; }
        .badge-pickup { background-color: #f3e5f5; color: #8e24aa; }
        .badge-pullout { background-color: #ffe0b2; color: #e65100; }
        .badge-driver-vehicle { background-color: #d1e7dd; color: #0f5132; }
    </style>
    @endpush

    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4 align-items-center">
            <div class="col-sm-6">
                <div class="d-flex align-items-center">
                    <div class="me-3 p-3 bg-danger text-white rounded-3 shadow-sm" style="background-color: #ff0000 !important;">
                        <i class="las la-store-alt fs-24"></i>
                    </div>
                    <div>
                        <h2 class="font-w600 mb-0">Logistics Service Orders</h2>
                        <p class="mb-0 text-muted">Create and manage customized logistics requests for approvals</p>
                    </div>
                </div>
            </div>
            @if(auth()->check() && auth()->user()->position !== 'Driver')
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                <button type="button" class="btn btn-danger shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createRequestModal" style="background-color: #ff0000; border-color: #ff0000;">
                    <i class="las la-plus me-1 fs-16"></i> Create Request
                </button>
            </div>
            @endif
        </div>

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="las la-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="las la-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Summary Cards -->
        <div class="request-stats">
            <div class="stat-card pending">
                <h3>{{ $requests->where('status', 'pending_approval')->count() }}</h3>
                <p>Pending Approval</p>
            </div>
            <div class="stat-card approved">
                <h3>{{ $requests->where('status', 'approved')->count() }}</h3>
                <p>Approved (Scheduled)</p>
            </div>
            <div class="stat-card rejected">
                <h3>{{ $requests->where('status', 'rejected')->count() }}</h3>
                <p>Rejected</p>
            </div>
            <div class="stat-card completed">
                <h3>{{ $requests->where('status', 'completed')->count() }}</h3>
                <p>Completed</p>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body px-4 py-4">
                <div class="table-responsive">
                    <table id="requestsTable" class="display table mb-0" style="width: 100%">
                        <thead class="table-light">
                            <tr>
                                <th>Ref #</th>
                                <th>Type</th>
                                <th>Client / Receiver</th>
                                <th>Address</th>
                                <th>Requested Date</th>
                                <th>Driver & Vehicle</th>
                                <th>Details</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $req)
                            <tr>
                                <td class="align-middle">
                                    <span class="text-black font-w600">REQ-{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="align-middle">
                                    @if($req->type === 'delivery')
                                        <span class="badge badge-type badge-delivery"><i class="las la-truck me-1"></i>Delivery</span>
                                    @elseif($req->type === 'pickup')
                                        <span class="badge badge-type badge-pickup"><i class="las la-store-alt me-1"></i>Pickup</span>
                                    @elseif($req->type === 'driver_vehicle')
                                        <span class="badge badge-type badge-driver-vehicle"><i class="las la-id-card me-1"></i>Driver/Vehicle</span>
                                    @else
                                        <span class="badge badge-type badge-pullout"><i class="las la-arrow-circle-down me-1"></i>Pull Out</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <span class="text-black d-block">{{ $req->client_name }}</span>
                                    <small class="text-muted">By: {{ $req->createdByUser->name ?? 'N/A' }}</small>
                                </td>
                                <td class="align-middle" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $req->address }}">
                                    {{ $req->address }}
                                </td>
                                <td class="align-middle">
                                    <i class="las la-calendar me-1 text-danger"></i>
                                    {{ $req->requested_date->format('M d, Y') }}
                                </td>
                                <td class="align-middle">
                                    @php
                                        $dName = $req->driver_name ?: ($req->driver ? ($req->driver->first_name . ' ' . $req->driver->last_name) : '');
                                    @endphp
                                    @if($dName)
                                        <span class="text-black font-w600 d-block"><i class="las la-user-tag me-1 text-primary"></i>{{ $dName }}</span>
                                    @endif
                                    @if($req->vehicle)
                                        <small class="text-muted d-block"><i class="las la-truck me-1 text-success"></i>{{ $req->vehicle }}</small>
                                    @elseif(!$dName)
                                        <span class="text-muted fs-12">Unassigned</span>
                                    @endif
                                </td>
                                <td class="align-middle" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $req->items_details }}">
                                    {{ $req->items_details }}
                                </td>
                                <td class="align-middle">
                                    @php
                                        $statusClass = 'bg-warning text-dark';
                                        if ($req->status === 'approved') $statusClass = 'bg-primary text-white';
                                        elseif ($req->status === 'rejected') $statusClass = 'bg-danger text-white';
                                        elseif ($req->status === 'completed') $statusClass = 'bg-success text-white';
                                    @endphp
                                    <span class="badge px-3 py-2 fs-12 {{ $statusClass }}">
                                        {{ ucwords(str_replace('_', ' ', $req->status)) }}
                                    </span>
                                </td>
                                <td class="align-middle text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        @if(auth()->check() && auth()->user()->position === 'Driver')
                                            <button type="button" class="btn btn-info text-white shadow btn-xs sharp" data-bs-toggle="modal" data-bs-target="#viewRequestModal{{ $req->id }}" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($req->status !== 'completed')
                                            <form action="{{ route('production.logistic.pickup-requests.complete', $req->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Mark this logistics service order as completed?');">
                                                @csrf
                                                <button type="submit" class="btn btn-success shadow btn-xs sharp" title="Mark Complete">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            @endif
                                        @else
                                            <button type="button" class="btn btn-primary shadow btn-xs sharp" data-bs-toggle="modal" data-bs-target="#editRequestModal{{ $req->id }}" title="Edit Request">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <form action="{{ route('production.logistic.pickup-requests.destroy', $req->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger shadow btn-xs sharp" title="Delete Request">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <!-- View Details Modal (Driver) -->
                                    <div class="modal fade" id="viewRequestModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start">
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title font-w600">Details for REQ-{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="fw-bold text-muted small d-block mb-1">Request Type:</label>
                                                        <span class="badge badge-type badge-{{ $req->type === 'delivery' ? 'delivery' : ($req->type === 'pickup' ? 'pickup' : ($req->type === 'driver_vehicle' ? 'driver-vehicle' : 'pullout')) }}">
                                                            {{ str_replace('_', ' ', strtoupper($req->type)) }}
                                                        </span>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="fw-bold text-muted small d-block mb-1">Client / Receiver:</label>
                                                        <span class="fw-bold text-dark fs-15">{{ $req->client_name }}</span>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="fw-bold text-muted small d-block mb-1">Address / Location:</label>
                                                        <div class="p-2 bg-light rounded text-dark border">{{ $req->address }}</div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="fw-bold text-muted small d-block mb-1">Requested Date:</label>
                                                        <span class="fw-bold text-dark">{{ $req->requested_date ? $req->requested_date->format('M d, Y') : 'N/A' }}</span>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="fw-bold text-muted small d-block mb-1">Driver & Vehicle:</label>
                                                        <span class="fw-bold text-dark">{{ $dName ?: 'Unassigned' }} {{ $req->vehicle ? '('.$req->vehicle.')' : '' }}</span>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="fw-bold text-muted small d-block mb-1">Items Description & Quantity:</label>
                                                        <div class="p-2 bg-light rounded text-dark border" style="white-space: pre-wrap;">{{ $req->items_details }}</div>
                                                    </div>
                                                    @if($req->remarks)
                                                    <div class="mb-3">
                                                        <label class="fw-bold text-muted small d-block mb-1">Remarks / Special Instructions:</label>
                                                        <div class="p-2 bg-light rounded text-dark border">{{ $req->remarks }}</div>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editRequestModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start">
                                                <form action="{{ route('production.logistic.pickup-requests.update', $req->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Request REQ-{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!-- Request Type -->
                                                        <div class="mb-3 text-start">
                                                            <label class="form-label fw-bold text-dark mb-2">Request Type <span class="text-danger">*</span></label>
                                                            <div class="d-flex flex-wrap gap-3">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="type" id="typeDeliveryEdit{{ $req->id }}" value="delivery" {{ $req->type === 'delivery' ? 'checked' : '' }} required>
                                                                    <label class="form-check-label fw-600 text-dark" for="typeDeliveryEdit{{ $req->id }}" style="cursor: pointer;">
                                                                        <i class="las la-truck me-1 text-primary"></i> Delivery Request
                                                                    </label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="type" id="typePickupEdit{{ $req->id }}" value="pickup" {{ $req->type === 'pickup' ? 'checked' : '' }} required>
                                                                    <label class="form-check-label fw-600 text-dark" for="typePickupEdit{{ $req->id }}" style="cursor: pointer;">
                                                                        <i class="las la-store-alt me-1 text-purple" style="color: #8e24aa;"></i> Pickup Request
                                                                    </label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="type" id="typePulloutEdit{{ $req->id }}" value="pull_out" {{ $req->type === 'pull_out' ? 'checked' : '' }} required>
                                                                    <label class="form-check-label fw-600 text-dark" for="typePulloutEdit{{ $req->id }}" style="cursor: pointer;">
                                                                        <i class="las la-arrow-circle-down me-1 text-warning" style="color: #e65100 !important;"></i> Pull Out Request
                                                                    </label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="type" id="typeDriverVehicleEdit{{ $req->id }}" value="driver_vehicle" {{ $req->type === 'driver_vehicle' ? 'checked' : '' }} required>
                                                                    <label class="form-check-label fw-600 text-dark" for="typeDriverVehicleEdit{{ $req->id }}" style="cursor: pointer;">
                                                                        <i class="las la-id-card me-1 text-success"></i> Driver/Vehicle
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Client -->
                                                        <div class="mb-3 text-start">
                                                            <label for="client_nameEdit{{ $req->id }}" class="form-label fw-bold text-dark">Client / Receiver Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="client_nameEdit{{ $req->id }}" name="client_name" value="{{ old('client_name', $req->client_name) }}" required>
                                                        </div>

                                                        <!-- Address -->
                                                        <div class="mb-3 text-start">
                                                            <label for="addressEdit{{ $req->id }}" class="form-label fw-bold text-dark">Address / Location <span class="text-danger">*</span></label>
                                                            <textarea class="form-control" id="addressEdit{{ $req->id }}" name="address" rows="3" required>{{ old('address', $req->address) }}</textarea>
                                                        </div>

                                                        <!-- Date -->
                                                        <div class="mb-3 text-start">
                                                            <label for="requested_dateEdit{{ $req->id }}" class="form-label fw-bold text-dark">Requested Delivery / Pickup Date <span class="text-danger">*</span></label>
                                                            <input type="date" class="form-control" id="requested_dateEdit{{ $req->id }}" name="requested_date" value="{{ old('requested_date', $req->requested_date->format('Y-m-d')) }}" required>
                                                        </div>

                                                        <!-- Driver & Vehicle -->
                                                        <div class="row mb-3 text-start">
                                                            <div class="col-md-6">
                                                                <label for="driver_idEdit{{ $req->id }}" class="form-label fw-bold text-dark">Assigned Driver</label>
                                                                <select class="form-select" id="driver_idEdit{{ $req->id }}" name="driver_id">
                                                                    <option value="">-- Select Driver (Optional) --</option>
                                                                    @foreach($drivers as $d)
                                                                        <option value="{{ $d->id }}" {{ $req->driver_id == $d->id ? 'selected' : '' }}>
                                                                            {{ $d->first_name }} {{ $d->last_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="vehicleEdit{{ $req->id }}" class="form-label fw-bold text-dark">Vehicle / Plate No.</label>
                                                                <input type="text" class="form-control" id="vehicleEdit{{ $req->id }}" name="vehicle" value="{{ old('vehicle', $req->vehicle) }}" placeholder="e.g., L300 / NBO 1234">
                                                            </div>
                                                        </div>

                                                        <!-- Items -->
                                                        <div class="mb-3 text-start">
                                                            <label for="items_detailsEdit{{ $req->id }}" class="form-label fw-bold text-dark">Items Description & Quantity <span class="text-danger">*</span></label>
                                                            <textarea class="form-control" id="items_detailsEdit{{ $req->id }}" name="items_details" rows="4" required>{{ old('items_details', $req->items_details) }}</textarea>
                                                        </div>

                                                        <!-- Remarks -->
                                                        <div class="mb-0 text-start">
                                                            <label for="remarksEdit{{ $req->id }}" class="form-label fw-bold text-dark">Remarks</label>
                                                            <textarea class="form-control" id="remarksEdit{{ $req->id }}" name="remarks" rows="2">{{ old('remarks', $req->remarks) }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary shadow-sm rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger shadow-sm rounded-pill px-4" style="background-color: #ff0000; border-color: #ff0000;">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr class="empty-row">
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="las la-clipboard-list fs-50 mb-3 d-block opacity-25"></i>
                                        No Logistics Service Orders created yet.
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createRequestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-start">
                <form action="{{ route('production.logistic.pickup-requests.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Create Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Request Type -->
                        <div class="mb-3 text-start">
                            <label class="form-label fw-bold text-dark mb-2">Request Type <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="typeDeliveryCreate" value="delivery" checked required>
                                    <label class="form-check-label fw-600 text-dark" for="typeDeliveryCreate" style="cursor: pointer;">
                                        <i class="las la-truck me-1 text-primary"></i> Delivery Request
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="typePickupCreate" value="pickup" required>
                                    <label class="form-check-label fw-600 text-dark" for="typePickupCreate" style="cursor: pointer;">
                                        <i class="las la-store-alt me-1 text-purple" style="color: #8e24aa;"></i> Pickup Request
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="typePulloutCreate" value="pull_out" required>
                                    <label class="form-check-label fw-600 text-dark" for="typePulloutCreate" style="cursor: pointer;">
                                        <i class="las la-arrow-circle-down me-1 text-warning" style="color: #e65100 !important;"></i> Pull Out Request
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="typeDriverVehicleCreate" value="driver_vehicle" required>
                                    <label class="form-check-label fw-600 text-dark" for="typeDriverVehicleCreate" style="cursor: pointer;">
                                        <i class="las la-id-card me-1 text-success"></i> Driver/Vehicle
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Client -->
                        <div class="mb-3 text-start">
                            <label for="client_nameCreate" class="form-label fw-bold text-dark">Client / Receiver Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="client_nameCreate" name="client_name" placeholder="Enter client or receiver name" value="{{ old('client_name') }}" required>
                        </div>

                        <!-- Address -->
                        <div class="mb-3 text-start">
                            <label for="addressCreate" class="form-label fw-bold text-dark">Address / Location <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="addressCreate" name="address" rows="3" placeholder="Enter destination address or pickup location" required>{{ old('address') }}</textarea>
                        </div>

                        <!-- Date -->
                        <div class="mb-3 text-start">
                            <label for="requested_dateCreate" class="form-label fw-bold text-dark">Requested Delivery / Pickup Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="requested_dateCreate" name="requested_date" value="{{ old('requested_date', date('Y-m-d')) }}" required>
                        </div>

                        <!-- Driver & Vehicle -->
                        <div class="row mb-3 text-start">
                            <div class="col-md-6">
                                <label for="driver_idCreate" class="form-label fw-bold text-dark">Assigned Driver</label>
                                <select class="form-select" id="driver_idCreate" name="driver_id">
                                    <option value="">-- Select Driver (Optional) --</option>
                                    @foreach($drivers as $d)
                                        <option value="{{ $d->id }}" {{ old('driver_id') == $d->id ? 'selected' : '' }}>
                                            {{ $d->first_name }} {{ $d->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="vehicleCreate" class="form-label fw-bold text-dark">Vehicle / Plate No.</label>
                                <input type="text" class="form-control" id="vehicleCreate" name="vehicle" placeholder="e.g., L300 / NBO 1234, Motorcycle" value="{{ old('vehicle') }}">
                            </div>
                        </div>

                        <!-- Items -->
                        <div class="mb-3 text-start">
                            <label for="items_detailsCreate" class="form-label fw-bold text-dark">Items Description & Quantity <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="items_detailsCreate" name="items_details" rows="4" placeholder="Specify items, descriptions, and quantities (e.g., 50 pcs Math Textbooks, 10 packs Paper)" required>{{ old('items_details') }}</textarea>
                        </div>

                        <!-- Remarks -->
                        <div class="mb-0 text-start">
                            <label for="remarksCreate" class="form-label fw-bold text-dark">Remarks</label>
                            <textarea class="form-control" id="remarksCreate" name="remarks" rows="2" placeholder="Any additional notes or instructions...">{{ old('remarks') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary shadow-sm rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger shadow-sm rounded-pill px-4" style="background-color: #ff0000; border-color: #ff0000;">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#requestsTable tbody tr.empty-row').remove();

            $('#requestsTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 25,
                columnDefs: [{ orderable: false, targets: 8 }],
                language: {
                    emptyTable: "No orders found. Create a new request above.",
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                }
            });

            @if($errors->any())
                // Reopen create modal if validation fails
                var createModal = new bootstrap.Modal(document.getElementById('createRequestModal'));
                createModal.show();
            @endif
        });
    </script>
    @endpush
</x-app-layout>
