<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .status-pending_mkt_approval { background: #fff3cd; color: #856404; }
        .status-pending_prod_approval { background: #e0f2ff; color: #004085; }
        .status-picking { background: #d1ecf1; color: #0c5460; }
        .status-pending_si_prep { background: #cce5ff; color: #004085; }
        .status-pending_si_approval { background: #e2d9ff; color: #4b0082; }
        .status-ready_for_delivery { background: #d4edda; color: #155724; }
        .status-completed { background: #c3e6cb; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .status-gathered { background: #d1ecf1; color: #0c5460; }
        .type-badge { padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .type-foreign { background: #e0cffc; color: #4a148c; }
        .type-local { background: #d1ecf1; color: #0c5460; }
    </style>
    @endpush

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="las la-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card p-4" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0"><i class="las la-list me-2"></i>All Website Direct Invoices</h4>
                    <a href="{{ route('marketing.direct-invoice.website') }}" class="btn btn-primary" style="background:#ff0000; border:none;">
                        <i class="las la-plus me-1"></i> New Invoice
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Prepared By</th>
                                <th>Date</th>
                                <th>Attachments</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $inv)
                            <tr>
                                <td class="fw-bold">{{ $inv->so_number }}</td>
                                <td>{{ $inv->customer->customer_name ?? 'N/A' }}</td>
                                <td><span class="type-badge type-{{ $inv->transaction_subtype }}">{{ ucfirst($inv->transaction_subtype ?? 'N/A') }}</span></td>
                                <td><span class="status-badge status-{{ $inv->status }}">
                                    @php
                                        $displayStatus = str_replace('_', ' ', $inv->status);
                                        if ($inv->status == 'pending_si_prep') $displayStatus = 'Gathered (In SI Prep)';
                                        if ($inv->status == 'pending_dr_prep') $displayStatus = 'SI Signed (In DR Prep)';
                                    @endphp
                                    {{ ucwords($displayStatus) }}
                                </span></td>
                                <td>₱{{ number_format($inv->total_amount, 2) }}</td>
                                <td>{{ $inv->preparedBy->name ?? 'N/A' }}</td>
                                <td>{{ $inv->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if($inv->proof_of_payment)
                                        <a href="/storage/{{ $inv->proof_of_payment }}" target="_blank" class="btn btn-sm btn-outline-warning">POP</a>
                                    @endif
                                    @if($inv->order_list_attachment)
                                        <a href="/storage/{{ $inv->order_list_attachment }}" target="_blank" class="btn btn-sm btn-outline-info">OL</a>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $canApprove = false;
                                        $userPos = auth()->user()->position ?? '';
                                        $isManager = str_contains($userPos, 'Manager') || str_contains($userPos, 'Supervisor') || $userPos === 'Super Admin';
                                        if ($isManager && in_array($inv->status, ['pending_mkt_approval', 'pending_prod_approval'])) {
                                            $canApprove = true;
                                        }
                                    @endphp
                                    @if($canApprove)
                                        <form action="{{ route('marketing.direct-invoice.website.approve', $inv->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve this invoice?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success"><i class="las la-check me-1"></i>Approve</button>
                                        </form>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No website invoices found. <a href="{{ route('marketing.direct-invoice.website') }}">Create one now</a>.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($invoices->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $invoices->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
