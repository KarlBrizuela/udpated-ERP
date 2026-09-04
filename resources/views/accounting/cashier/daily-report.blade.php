@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">Daily Cashier Report</h1>
            <p class="text-muted small">{{ now()->format('l, F d, Y') }} - COD Collections Summary</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('cashier.collections.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button onclick="window.print()" class="btn btn-info">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <!-- Daily Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-success text-uppercase mb-1 font-weight-bold small">Collections Verified</div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $report['verified_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-primary text-uppercase mb-1 font-weight-bold small">Total Amount Verified</div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800">₱{{ number_format($report['verified_amount'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-info text-uppercase mb-1 font-weight-bold small">Discrepancies Found</div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $report['discrepancies']->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-warning text-uppercase mb-1 font-weight-bold small">Riders Today</div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $report['collections_by_rider']->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collections by Rider -->
    <div class="card shadow mb-4">
        <div class="card-header border-bottom py-3">
            <h6 class="m-0 font-weight-bold text-primary">Collections by Rider</h6>
        </div>
        <div class="card-body">
            @if($report['collections_by_rider']->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="bg-light">
                            <tr>
                                <th>Rider Name</th>
                                <th class="text-center">Collections Count</th>
                                <th class="text-right">Total Amount</th>
                                <th class="text-right">Average per Collection</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grandTotal = 0; @endphp
                            @foreach($report['collections_by_rider'] as $collection)
                                @php
                                    $riderName = $collection->rider->first_name . ' ' . $collection->rider->last_name;
                                    $grandTotal += $collection->total;
                                @endphp
                                <tr>
                                    <td><strong>{{ $riderName }}</strong></td>
                                    <td class="text-center">{{ $collection->count }}</td>
                                    <td class="text-right"><strong class="text-success">₱{{ number_format($collection->total, 2) }}</strong></td>
                                    <td class="text-right">₱{{ number_format($collection->total / $collection->count, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light font-weight-bold">
                                <td colspan="2" class="text-right">TOTAL:</td>
                                <td class="text-right text-success" style="font-size: 1.1rem;">₱{{ number_format($grandTotal, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> No collections verified today.
                </div>
            @endif
        </div>
    </div>

    <!-- Discrepancies (if any) -->
    @if($report['discrepancies']->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header border-bottom py-3 bg-warning text-dark">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-exclamation-triangle"></i> Collections with Discrepancies ({{ $report['discrepancies']->count() }})
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="bg-light">
                            <tr>
                                <th>SO Number</th>
                                <th>Customer</th>
                                <th>Rider</th>
                                <th class="text-right">Amount to Collect</th>
                                <th class="text-right">Amount Collected</th>
                                <th class="text-right">Discrepancy</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report['discrepancies'] as $discrepancy)
                                @php
                                    $diff = $discrepancy->amount_collected - $discrepancy->amount_to_collect;
                                    $discrepancyType = $diff > 0 ? 'Over-collected' : 'Under-collected';
                                    $badgeClass = $diff > 0 ? 'success' : 'danger';
                                @endphp
                                <tr>
                                    <td><strong>{{ $discrepancy->salesOrder->so_number }}</strong></td>
                                    <td>{{ $discrepancy->salesOrder->customer->customer_name ?? 'N/A' }}</td>
                                    <td>{{ $discrepancy->rider->first_name ?? '' }} {{ $discrepancy->rider->last_name ?? 'N/A' }}</td>
                                    <td class="text-right">₱{{ number_format($discrepancy->amount_to_collect, 2) }}</td>
                                    <td class="text-right"><strong>₱{{ number_format($discrepancy->amount_collected, 2) }}</strong></td>
                                    <td class="text-right">
                                        <span class="badge badge-{{ $badgeClass }}">
                                            {{ $discrepancyType }}: ₱{{ number_format(abs($diff), 2) }}
                                        </span>
                                    </td>
                                    <td><small>{{ $discrepancy->discrepancy_notes ?? '-' }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Empty State -->
    @if($report['verified_count'] === 0)
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox" style="font-size: 3rem; color: #d1d3e2;"></i>
                <h4 class="text-gray-600 mt-3">No Collections Verified Today</h4>
                <p class="text-muted">Collections verified by the cashier will appear here.</p>
            </div>
        </div>
    @endif
</div>

<style media="print">
    .btn, .btn-secondary, .btn-info {
        display: none !important;
    }
</style>
@endsection
