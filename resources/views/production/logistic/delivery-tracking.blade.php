<x-app-layout :title="'Delivery Tracking'" :sidebar="'production'">

    @push('styles')
    <style>
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
            white-space: nowrap;
        }
        .status-ready_for_delivery { background-color: #e0f2ff; color: #004085; }
        .status-in_transit { background-color: #fff3cd; color: #856404; }
        .status-completed { background-color: #d4edda; color: #155724; }
        .status-failed { background-color: #f8d7da; color: #721c24; }
        
        .tracking-table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 0.8rem;
            color: #495057;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }
    </style>
    <link href="{{ asset('vendor/chartist/css/chartist.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    @endpush

    <div class="container-fluid">
        <!-- Tracking Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div class="p-3 bg-info-light rounded-circle text-info">
                            <i class="las la-globe fs-30"></i>
                        </div>
                    </div>
                    <div>
                        <h2 class="font-w600 mb-0">Delivery Tracking</h2>
                        <p class="mb-0 text-muted">Monitor all active and completed deliveries in real-time</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="row mb-4">
            <div class="col-xl-12">
                <div class="card border-0 shadow-sm" style="border-radius: 10px;">
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('production.logistic.delivery-tracking') }}" id="trackingFilterForm">
                            <div class="row g-2 align-items-end">
                                <!-- Search Bar -->
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label text-muted small fw-bold mb-1">
                                        <i class="las la-search me-1 text-primary"></i>Search
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0"><i class="las la-search text-muted"></i></span>
                                        <input type="text" name="search" id="trackingSearchInput" class="form-control form-control-sm border-start-0" placeholder="Ref #, customer, driver, address..." value="{{ request('search') }}" autocomplete="off">
                                    </div>
                                </div>

                                <!-- Filter by Customer -->
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label text-muted small fw-bold mb-1">
                                        <i class="las la-user me-1 text-primary"></i>Customer
                                    </label>
                                    <select name="customer_id" id="filterCustomer" class="form-select form-select-sm" onchange="document.getElementById('trackingFilterForm').submit()">
                                        <option value="">All Customers</option>
                                        @foreach($customers as $c)
                                            <option value="{{ $c->customer_id }}" {{ request('customer_id') == $c->customer_id ? 'selected' : '' }}>
                                                {{ $c->customer_name ?: $c->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filter by Driver -->
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label text-muted small fw-bold mb-1">
                                        <i class="las la-user-tie me-1 text-primary"></i>Driver
                                    </label>
                                    <select name="driver" id="filterDriver" class="form-select form-select-sm" onchange="document.getElementById('trackingFilterForm').submit()">
                                        <option value="">All Drivers</option>
                                        <option value="unassigned" {{ request('driver') === 'unassigned' ? 'selected' : '' }}>Unassigned Only</option>
                                        @foreach($drivers as $d)
                                            <option value="{{ $d }}" {{ request('driver') === $d ? 'selected' : '' }}>
                                                {{ $d }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filter by Date -->
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label text-muted small fw-bold mb-1">
                                        <i class="las la-calendar me-1 text-primary"></i>Date
                                    </label>
                                    <input type="date" name="date" id="filterDate" class="form-control form-control-sm" value="{{ request('date') }}" onchange="document.getElementById('trackingFilterForm').submit()">
                                </div>

                                <!-- Filter Actions -->
                                <div class="col-lg-2 col-md-4 d-flex gap-1">
                                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                        <i class="las la-filter me-1"></i>Filter
                                    </button>
                                    @if(request()->hasAny(['search', 'customer_id', 'driver', 'date', 'start_date', 'end_date']))
                                        <a href="{{ route('production.logistic.delivery-tracking') }}" class="btn btn-outline-secondary btn-sm" title="Reset all filters">
                                            <i class="las la-undo-alt me-1"></i>Reset
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tracking Table -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card border-0 shadow-sm" style="border-radius: 10px;">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="fs-18 mb-0 font-w600">Active Shipments</h4>
                            <span class="badge bg-light text-dark border">{{ $deliveries->count() }} records</span>
                        </div>
                        @if(request()->hasAny(['search', 'customer_id', 'driver', 'date']))
                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                <small class="text-muted me-1">Active filters:</small>
                                @if(request('search'))
                                    <span class="badge bg-info-subtle text-info border">Search: "{{ request('search') }}"</span>
                                @endif
                                @if(request('customer_id'))
                                    @php $selectedCust = $customers->firstWhere('customer_id', request('customer_id')); @endphp
                                    <span class="badge bg-primary-subtle text-primary border">Customer: {{ $selectedCust ? ($selectedCust->customer_name ?: $selectedCust->company_name) : request('customer_id') }}</span>
                                @endif
                                @if(request('driver'))
                                    <span class="badge bg-warning-subtle text-dark border">Driver: {{ request('driver') === 'unassigned' ? 'Unassigned' : request('driver') }}</span>
                                @endif
                                @if(request('date'))
                                    <span class="badge bg-secondary-subtle text-dark border">Date: {{ \Carbon\Carbon::parse(request('date'))->format('M d, Y') }}</span>
                                @endif
                                <a href="{{ route('production.logistic.delivery-tracking') }}" class="text-danger small text-decoration-none ms-1">
                                    <i class="las la-times"></i> Clear
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="table-responsive">
                            <table class="table tracking-table mb-0" id="deliveryTrackingTable" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Ref Number</th>
                                        <th>Customer</th>
                                        <th>Driver Assigned</th>
                                        <th>Current Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="deliveryTrackingTableBody">
                                    @forelse($deliveries as $delivery)
                                    <tr class="delivery-row" 
                                        data-ref="{{ strtolower($delivery->so_number) }}"
                                        data-customer="{{ strtolower($delivery->customer->customer_name ?? $delivery->customer->company_name ?? '') }}"
                                        data-driver="{{ strtolower($delivery->driver ?? 'unassigned') }}"
                                        data-date="{{ $delivery->delivery_date ? \Carbon\Carbon::parse($delivery->delivery_date)->format('Y-m-d') : $delivery->updated_at->format('Y-m-d') }}">
                                        <td class="align-middle">
                                            <span class="text-black font-w600">{{ $delivery->so_number }}</span>
                                            <div class="text-muted small">Updated {{ $delivery->updated_at->diffForHumans() }}</div>
                                            @if($delivery->delivery_date)
                                                <div class="text-primary small" style="font-size: 0.75rem;">
                                                    <i class="las la-calendar me-1"></i>Delivery: {{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <span class="text-black font-w500">{{ $delivery->customer->customer_name ?? $delivery->customer->company_name ?? 'N/A' }}</span>
                                            @if($delivery->shipping_address)
                                                <small class="text-muted d-block text-truncate" style="max-width: 250px;" title="{{ $delivery->shipping_address }}">
                                                    <i class="las la-map-marker me-1"></i>{{ $delivery->shipping_address }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <div class="me-2 p-2 bg-light rounded shadow-sm">
                                                    <i class="las la-user-tie text-info"></i>
                                                </div>
                                                <div>
                                                    <span class="text-black font-w500 d-block">{{ $delivery->driver ?? 'Unassigned' }}</span>
                                                    @if($delivery->plate_number)
                                                        <small class="text-muted text-uppercase">{{ $delivery->plate_number }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="status-badge status-{{ $delivery->status }}">
                                                {{ ucwords(str_replace('_', ' ', $delivery->status)) }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <a href="{{ route('production.logistic.print-transmittal', $delivery->id) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded shadow-sm">
                                                <i class="las la-file-alt me-1"></i> Transmittal
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr id="emptyDeliveriesRow">
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="las la-search-location fs-50 mb-3 d-block opacity-25"></i>
                                                @if(request()->hasAny(['search', 'customer_id', 'driver', 'date']))
                                                    No deliveries found matching your search or filters.
                                                    <div class="mt-2">
                                                        <a href="{{ route('production.logistic.delivery-tracking') }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="las la-undo-alt me-1"></i>Clear all filters
                                                        </a>
                                                    </div>
                                                @else
                                                    No active deliveries currently tracked.
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                    <tr id="noLiveMatchRow" class="d-none">
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="las la-search-location fs-50 mb-3 d-block opacity-25"></i>
                                                No deliveries match your current search query.
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('trackingSearchInput');
            const tableBody = document.getElementById('deliveryTrackingTableBody');
            const noLiveMatchRow = document.getElementById('noLiveMatchRow');

            if (searchInput && tableBody) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim().toLowerCase();
                    const rows = tableBody.querySelectorAll('.delivery-row');
                    let visibleCount = 0;

                    rows.forEach(function(row) {
                        const rowText = row.textContent.toLowerCase();
                        if (!query || rowText.includes(query)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    if (noLiveMatchRow) {
                        if (visibleCount === 0 && rows.length > 0) {
                            noLiveMatchRow.classList.remove('d-none');
                        } else {
                            noLiveMatchRow.classList.add('d-none');
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
