@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">Verify COD Payments</h1>
            <p class="text-muted small">Verify cash collections from riders</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('cashier.daily-report') }}" class="btn btn-info">
                <i class="fas fa-chart-bar"></i> Daily Report
            </a>
            <a href="{{ route('cashier.export') }}" class="btn btn-success">
                <i class="fas fa-download"></i> Export
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-secondary text-uppercase mb-1 font-weight-bold small">Awaiting Verification</div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['awaiting_verification'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-warning text-uppercase mb-1 font-weight-bold small">Pending Amount</div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800">₱{{ number_format($stats['total_pending'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-success text-uppercase mb-1 font-weight-bold small">Verified Today</div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['verified_today'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-primary text-uppercase mb-1 font-weight-bold small">Total Verified Today</div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800">₱{{ number_format($stats['total_verified_today'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collections Table -->
    <div class="card shadow">
        <div class="card-header border-bottom py-3">
            <h6 class="m-0 font-weight-bold text-primary">Collections Awaiting Verification</h6>
        </div>
        <div class="card-body">
            @if($collections->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>SO Number</th>
                                <th>Customer</th>
                                <th>Rider</th>
                                <th>Amount to Collect</th>
                                <th>Amount Collected</th>
                                <th>Status</th>
                                <th>Collected Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($collections as $collection)
                                <tr>
                                    <td><strong>{{ $collection->salesOrder->so_number }}</strong></td>
                                    <td>{{ $collection->salesOrder->customer->customer_name ?? 'N/A' }}</td>
                                    <td>{{ $collection->rider->first_name ?? '' }} {{ $collection->rider->last_name ?? 'N/A' }}</td>
                                    <td><strong>₱{{ number_format($collection->amount_to_collect, 2) }}</strong></td>
                                    <td>
                                        @if($collection->amount_collected)
                                            <strong class="text-success">₱{{ number_format($collection->amount_collected, 2) }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($collection->status === 'collected')
                                            <span class="badge badge-info">Collected</span>
                                        @elseif($collection->status === 'handed_over')
                                            <span class="badge badge-secondary">Handed Over</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($collection->collected_at)
                                            {{ $collection->collected_at->format('M d, Y h:i A') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('cashier.collections.show', $collection->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Verify
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $collections->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No collections awaiting verification at this time.
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .card-body {
        padding: 1.5rem;
    }
    .border-left-secondary {
        border-left: 0.25rem solid #858796;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a;
    }
    .text-secondary {
        color: #858796 !important;
    }
</style>
@endsection
