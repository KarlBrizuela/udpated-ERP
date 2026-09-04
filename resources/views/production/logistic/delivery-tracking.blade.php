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

        <!-- Tracking Table -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h4 class="fs-18 mb-0 font-w600">Active Shipments</h4>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="table-responsive">
                            <table class="table tracking-table mb-0" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Ref Number</th>
                                        <th>Customer</th>
                                        <th>Driver Assigned</th>
                                        <th>Current Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($deliveries as $delivery)
                                    <tr>
                                        <td class="align-middle">
                                            <span class="text-black font-w600">{{ $delivery->so_number }}</span>
                                            <div class="text-muted small">Updated {{ $delivery->updated_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="text-black">{{ $delivery->customer->customer_name ?? 'N/A' }}</span>
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
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="las la-search-location fs-50 mb-3 d-block opacity-25"></i>
                                                No active deliveries currently tracked.
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
        </div>
    </div>
</x-app-layout>
