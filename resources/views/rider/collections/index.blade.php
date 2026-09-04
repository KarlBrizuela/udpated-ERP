@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 mb-0 text-gray-800">COD Collections</h1>
            <p class="text-muted small">Pending deliveries requiring cash collection</p>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-warning text-uppercase mb-1 font-weight-bold small">Pending Collections</div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-info text-uppercase mb-1 font-weight-bold small">Collected</div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['collected'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-secondary text-uppercase mb-1 font-weight-bold small">To Hand Over</div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stats['handed_over'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-primary text-uppercase mb-1 font-weight-bold small">Total Collected</div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800">₱{{ number_format($stats['total_collected'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collections Table -->
    <div class="card shadow">
        <div class="card-header border-bottom py-3">
            <h6 class="m-0 font-weight-bold text-primary">My Collections</h6>
        </div>
        <div class="card-body">
            @if($collections->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>SO Number</th>
                                <th>Customer</th>
                                <th>Transaction Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Delivery Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($collections as $collection)
                                <tr>
                                    <td>
                                        <strong>{{ $collection->salesOrder->so_number }}</strong>
                                    </td>
                                    <td>
                                        {{ $collection->salesOrder->customer->customer_name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        @if($collection->transaction_type === 'COD')
                                            <span class="badge badge-danger">{{ $collection->transaction_type }}</span>
                                        @elseif($collection->transaction_type === 'Credit')
                                            <span class="badge badge-info">{{ $collection->transaction_type }}</span>
                                        @elseif($collection->transaction_type === 'Prepaid')
                                            <span class="badge badge-success">{{ $collection->transaction_type }}</span>
                                        @elseif($collection->transaction_type === 'Evaluation')
                                            <span class="badge badge-primary">{{ $collection->transaction_type }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $collection->transaction_type ?? 'Other' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-success">₱{{ number_format($collection->amount_to_collect, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($collection->status === 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($collection->status === 'collected')
                                            <span class="badge badge-info">Collected</span>
                                        @elseif($collection->status === 'handed_over')
                                            <span class="badge badge-secondary">Handed Over</span>
                                        @elseif($collection->status === 'verified')
                                            <span class="badge badge-success">Verified</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $collection->created_at->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('rider.collections.show', $collection->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
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
                    <i class="fas fa-info-circle"></i> No pending collections at this time.
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .card-body {
        padding: 1.5rem;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc;
    }
    .border-left-secondary {
        border-left: 0.25rem solid #858796;
    }
    .text-warning {
        color: #f6c23e !important;
    }
    .text-info {
        color: #36b9cc !important;
    }
    .text-secondary {
        color: #858796 !important;
    }
</style>
@endsection
