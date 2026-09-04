<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @php
        $indexRoute = $indexRoute ?? 'marketing.freight-quotations.list';
        $createRoute = $createRoute ?? 'marketing.freight-quotations.create';
        $showRoute = $showRoute ?? 'marketing.freight-quotations.show';
    @endphp
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Freight Quotations</h5>
                        <a href="{{ route($createRoute) }}" class="btn btn-light btn-sm">
                            <i class="bi bi-plus me-1"></i>New Quotation
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Status Filters & Search Bar -->
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div class="d-flex gap-1 flex-wrap">
                                <a href="{{ route($indexRoute, array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}" 
                                   class="btn btn-sm {{ $currentStatus === 'all' ? 'btn-danger' : 'btn-outline-secondary' }}">
                                    All
                                </a>
                                <a href="{{ route($indexRoute, array_merge(request()->except('status', 'page'), ['status' => 'draft'])) }}" 
                                   class="btn btn-sm {{ $currentStatus === 'draft' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                    <i class="bi bi-pencil-square me-1"></i>Draft
                                </a>
                                <a href="{{ route($indexRoute, array_merge(request()->except('status', 'page'), ['status' => 'pending_logistics'])) }}" 
                                   class="btn btn-sm {{ $currentStatus === 'pending_logistics' ? 'btn-warning' : 'btn-outline-secondary' }}">
                                    <i class="bi bi-hourglass-split me-1"></i>Pending Review
                                </a>
                                <a href="{{ route($indexRoute, array_merge(request()->except('status', 'page'), ['status' => 'approved'])) }}" 
                                   class="btn btn-sm {{ $currentStatus === 'approved' ? 'btn-success' : 'btn-outline-secondary' }}">
                                    <i class="bi bi-check-circle me-1"></i>Approved
                                </a>
                                <a href="{{ route($indexRoute, array_merge(request()->except('status', 'page'), ['status' => 'linked_to_so'])) }}" 
                                   class="btn btn-sm {{ $currentStatus === 'linked_to_so' ? 'btn-info' : 'btn-outline-secondary' }}">
                                    <i class="bi bi-link-45deg me-1"></i>Linked to SO
                                </a>
                            </div>

                            <!-- Search Form -->
                            <form action="{{ route($indexRoute) }}" method="GET" class="d-flex align-items-center gap-1">
                                <input type="hidden" name="status" value="{{ $currentStatus }}">
                                <div style="width: 220px; height: 32px; display: flex; align-items: center; border: 1px solid #ced4da; border-radius: 4px; background-color: #fff; padding: 0 10px; box-sizing: border-box;">
                                    <i class="fas fa-search text-muted me-2" style="font-size: 0.85rem;"></i>
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search..." value="{{ request('search') }}" 
                                           style="border: none !important; background: transparent !important; padding: 0 !important; height: 100%; font-size: 0.82rem; color: #333; outline: none !important; box-shadow: none !important;">
                                    @if(request('search'))
                                        <a href="{{ route('marketing.freight-quotations.list', ['status' => $currentStatus]) }}" class="text-muted d-inline-flex align-items-center ms-1" title="Clear search" style="text-decoration: none;">
                                            <i class="fas fa-times-circle" style="color: #999; font-size: 0.9rem; cursor: pointer;"></i>
                                        </a>
                                    @endif
                                </div>
                                <button type="submit" class="btn btn-sm btn-danger text-white rounded d-inline-flex align-items-center justify-content-center gap-1" style="height: 32px; padding: 0 12px; font-size: 0.8rem; background-color: #D9251C; border: none;">
                                    <i class="fas fa-search" style="font-size: 0.8rem;"></i>
                                    <span>Search</span>
                                </button>
                            </form>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($quotations->isEmpty())
                            <div class="alert alert-info text-center py-4">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                <strong>No freight quotations found</strong>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Quote #</th>
                                            <th>Status</th>
                                            <th>Origin → Destination</th>
                                            <th>Mode</th>
                                            <th>Total Amount</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($quotations as $quotation)
                                            <tr>
                                                <td><strong>{{ $quotation->quote_number }}</strong></td>
                                                <td>
                                                    @php
                                                        $statusClass = [
                                                            'draft' => 'primary',
                                                            'pending_logistics' => 'warning',
                                                            'approved' => 'success',
                                                            'linked_to_so' => 'info',
                                                        ];
                                                        $statusIcon = [
                                                            'draft' => 'pencil-square',
                                                            'pending_logistics' => 'hourglass-split',
                                                            'approved' => 'check-circle',
                                                            'linked_to_so' => 'link-45deg',
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $statusClass[$quotation->workflow_status] ?? 'secondary' }}">
                                                        <i class="bi bi-{{ $statusIcon[$quotation->workflow_status] ?? '' }} me-1"></i>
                                                        {{ ucfirst(str_replace('_', ' ', $quotation->workflow_status)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>
                                                        <strong>{{ $quotation->origin_province }}</strong><br>
                                                        ↓<br>
                                                        <strong>{{ $quotation->destination_province }}</strong>
                                                    </small>
                                                </td>
                                                <td>
                                                    {{ $quotation->service_mode }}
                                                    @php $fwd = $quotation->forwarder ?? $quotation->salesOrder?->forwarder ?? $quotation->freight_mode; @endphp
                                                    @if($fwd)
                                                        <br><small class="text-primary fw-bold"><i class="bi bi-truck me-1"></i>{{ $fwd }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $fqCurr = $quotation->currency ?? 'PHP';
                                                        $fqSym = ($fqCurr === 'USD' ? '$' : '₱');
                                                    @endphp
                                                    @if(in_array($quotation->workflow_status, ['approved', 'linked_to_so']))
                                                        <strong class="text-success">{{ $fqSym }}{{ number_format($quotation->total_amount, 2) }}</strong>
                                                    @elseif($quotation->total_amount > 0)
                                                        <strong class="text-danger">{{ $fqSym }}{{ number_format($quotation->total_amount, 2) }}</strong>
                                                    @else
                                                        <span class="text-muted">Pending quote</span>
                                                    @endif
                                                </td>
                                                <td><small>{{ $quotation->created_at->format('M d, Y') }}</small></td>
                                                <td>
                                                    <div class="d-flex gap-1 align-items-center">
                                                        <a href="{{ route($showRoute, $quotation->id) }}" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye me-1"></i>View
                                                        </a>
                                                        @if($quotation->workflow_status === 'approved' && !$quotation->sales_order_id)
                                                            <form method="POST" action="{{ route('marketing.freight-quotations.create-so-directly', $quotation->id) }}" style="display: inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success" title="Create Sales Order from this quotation">
                                                                    <i class="bi bi-plus-circle me-1"></i>Create SO
                                                                </button>
                                                            </form>
                                                        @elseif($quotation->sales_order_id)
                                                            <a href="{{ route('marketing.sales-orders.show', $quotation->sales_order_id) }}" 
                                                               class="btn btn-sm btn-info" title="View linked Sales Order">
                                                                <i class="bi bi-box-arrow-up-right me-1"></i>View SO
                                                            </a>
                                                        @endif
                                                        @if(!$quotation->sales_order_id)
                                                            <form method="POST" action="{{ route('marketing.freight-quotations.destroy', $quotation->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this freight quotation?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Quotation">
                                                                    <i class="bi bi-trash me-1"></i>Delete
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

                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                                <div class="text-muted small">
                                    Showing {{ $quotations->firstItem() ?? 0 }} to {{ $quotations->lastItem() ?? 0 }} of {{ $quotations->total() }} entries
                                </div>
                                <div>
                                    {{ $quotations->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
