<x-app-layout :title="'Foreign Sales Orders'" :sidebar="'production'">
    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        .status-pending_prod_approval { background-color: #fff3cd; color: #856404; }
        .status-picking { background-color: #d1ecf1; color: #0c5460; }
        .status-completed { background-color: #c3e6cb; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
    </style>
    @endpush

    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="las la-check-circle me-1 fs-18"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="las la-exclamation-circle me-1 fs-18"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0 d-flex align-items-center justify-content-between flex-nowrap" style="gap: 1rem;">
                        <div style="flex-shrink: 0;">
                            <h4 class="fs-20 mb-0 text-black text-nowrap">Foreign Sales Orders</h4>
                        </div>
                        <div class="d-flex align-items-center flex-nowrap gap-2" style="flex-shrink: 0;">
                            <form method="GET" action="{{ route('production.ford.sales-order') }}" class="d-flex align-items-center flex-nowrap gap-2 mb-0">
                                <!-- Filter by Status -->
                                <select name="status" class="form-select form-select-sm" style="height: 40px; width: 180px; border-radius: 6px; border: 1px solid #ced4da;" onchange="this.form.submit()">
                                    <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                                    <option value="pending_prod_approval" {{ request('status') == 'pending_prod_approval' ? 'selected' : '' }}>Pending Prod Approval</option>
                                    <option value="picking" {{ request('status') == 'picking' ? 'selected' : '' }}>Picking / In Logistics</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>

                                <div class="input-group input-group-sm flex-nowrap" style="width: 220px;">
                                    <input type="text" name="search" class="form-control" placeholder="Search orders..." value="{{ request('search') }}" style="height: 40px; border-radius: 6px 0 0 6px; border: 1px solid #ced4da;">
                                    <button type="submit" class="btn btn-danger d-flex align-items-center justify-content-center" style="background: #ff0000; border-color: #ff0000; height: 40px; width: 40px; border-radius: 0 6px 6px 0;">
                                        <i class="las la-search fs-16"></i>
                                    </button>
                                </div>
                                @if(request('search') || (request('status') && request('status') !== 'all'))
                                    <a href="{{ route('production.ford.sales-order') }}" 
                                       class="d-flex align-items-center justify-content-center" 
                                       style="height: 40px; width: 40px; border-radius: 6px; border: 1px solid #ff0000; background: #ffffff; color: #ff0000; padding: 0; font-size: 1.25rem; flex-shrink: 0; text-decoration: none;" 
                                       title="Clear Filters">
                                        <i class="las la-times"></i>
                                    </a>
                                @endif
                            </form>
                            <a href="{{ route('production.ford.sales-order.create') }}" class="btn btn-danger text-white rounded text-nowrap" style="background: #ff0000; border: none; height: 40px; padding: 0 1.5rem; flex-shrink: 0; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                Create Foreign Sales Order
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="fordSalesOrdersTable" class="display table table-striped table-hover" style="width: 100%">
                                <thead style="background-color: #ff0000; color: #ffffff;">
                                    <tr>
                                        <th style="background-color: #ff0000; color: #ffffff;">Order Number</th>
                                        <th style="background-color: #ff0000; color: #ffffff;">Customer / Representative</th>
                                        <th style="background-color: #ff0000; color: #ffffff;">Order Date</th>
                                        <th style="background-color: #ff0000; color: #ffffff;">Transaction Type</th>
                                        <th style="background-color: #ff0000; color: #ffffff;">Currency</th>
                                        <th style="background-color: #ff0000; color: #ffffff;">Total Amount</th>
                                        <th style="background-color: #ff0000; color: #ffffff;">Items Count</th>
                                        <th style="background-color: #ff0000; color: #ffffff;">Status</th>
                                        <th style="background-color: #ff0000; color: #ffffff;" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                    @php
                                        $sym = ($order->currency === 'USD' ? '$' : '₱');
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $order->so_number }}</strong></td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $order->customer?->customer_name ?: ($order->customer?->company_name ?? 'Foreign Customer') }}</div>
                                            @if($order->customer_representative)
                                                <small class="text-muted"><i class="las la-user me-1"></i>{{ $order->customer_representative }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $order->created_at?->format('Y-m-d') ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-secondary text-uppercase">{{ str_replace('_', ' ', $order->type ?? 'foreign') }}</span>
                                        </td>
                                        <td><span class="fw-bold">{{ $order->currency ?? 'USD' }} ({{ $sym }})</span></td>
                                        <td class="fw-bold text-success">{{ $sym }} {{ number_format($order->total_amount, 2) }}</td>
                                        <td class="text-center">{{ $order->items?->count() ?? 0 }} pcs</td>
                                        <td>
                                            <span class="status-badge status-{{ $order->status }}">
                                                {{ strtoupper(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <a href="{{ route('production.sales-order.review', $order->id) }}" class="btn btn-sm btn-info text-white me-1" title="Review / View Sales Order">
                                                    <i class="las la-eye me-1"></i> Review / View
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <i class="las la-inbox fs-24 d-block mb-1"></i> No Foreign Sales Orders found. Click <strong>Create Foreign Sales Order</strong> to create one.
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
