<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .detail-card {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .info-item label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #666;
            margin-bottom: 0.25rem;
        }

        .info-item p {
            font-size: 1rem;
            color: #333;
            margin-bottom: 0;
            padding: 0.5rem;
            background: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #e9ecef;
        }

        .attachment-list {
            list-style: none;
            padding: 0;
        }

        .attachment-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
        }

        .attachment-item:hover {
            border-color: #ff0000;
            background: #fff5f5;
        }

        .attachment-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .attachment-icon {
            font-size: 1.5rem;
            color: #ff0000;
        }

        .action-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-10 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0">Account Statement — {{ $soa->soa_number }}</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin-finance.credit-collection.billing') }}">Billing List</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $soa->soa_number }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    @php
                        $statusColors = ['draft' => 'secondary', 'pending' => 'warning', 'approved' => 'success', 'compiled' => 'dark'];
                        $statusColor = $statusColors[$soa->status] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $statusColor }} fs-6">{{ ucfirst($soa->status) }}</span>
                    <a href="{{ route('admin-finance.credit-collection.billing') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="las la-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>

            <div class="detail-card">
                <div class="section-title">
                    <i class="las la-file-contract text-primary"></i>
                    Statement Information
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Statement Number</label>
                        <p>{{ $soa->soa_number }}</p>
                    </div>
                    <div class="info-item">
                        <label>Status</label>
                        <p><span class="badge bg-{{ $statusColor }}">{{ ucfirst($soa->status) }}</span></p>
                    </div>
                    <div class="info-item col-12">
                        <label>Customer Name</label>
                        <p>{{ $soa->customer->customer_name ?? $soa->customer->company_name ?? 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Contact Person</label>
                        <p>{{ $soa->contact_person ?? $soa->customer->contact_person ?? '—' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Billing Address</label>
                        <p>{{ $soa->billing_address ?? $soa->customer->billing_address ?? '—' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Billing Period Start</label>
                        <p>{{ $soa->billing_period_start ? \Carbon\Carbon::parse($soa->billing_period_start)->format('M d, Y') : '—' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Billing Period End</label>
                        <p>{{ $soa->billing_period_end ? \Carbon\Carbon::parse($soa->billing_period_end)->format('M d, Y') : '—' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Total Amount</label>
                        <p class="fw-bold text-primary">₱ {{ number_format($soa->total_amount, 2) }}</p>
                    </div>
                    <div class="info-item">
                        <label>Date Created</label>
                        <p>{{ $soa->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- Particulars / Line Items --}}
            @if($soa->items && $soa->items->count() > 0)
            <div class="detail-card">
                <div class="section-title">
                    <i class="las la-list text-primary"></i>
                    Particulars
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Item / Service</th>
                                <th>Description</th>
                                <th>Qty / Size</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($soa->items as $item)
                            <tr>
                                <td>{{ $item->service }}</td>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->qty }}</td>
                                <td class="text-end">₱ {{ number_format($item->price, 2) }}</td>
                                <td class="text-end fw-bold">₱ {{ number_format((float)$item->qty * (float)$item->price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">TOTAL AMOUNT</td>
                                <td class="text-end fw-bold text-primary">₱ {{ number_format($soa->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endif

            {{-- Linked Sales Orders --}}
            @if($soa->salesOrders && $soa->salesOrders->count() > 0)
            <div class="detail-card">
                <div class="section-title">
                    <i class="las la-shopping-cart text-primary"></i>
                    Linked Sales Orders
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($soa->salesOrders as $so)
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="fw-bold text-primary">{{ $so->so_number }}</span>
                        <span>₱ {{ number_format($so->final_total, 2) }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="detail-card bg-light">
                <div class="section-title">
                    <i class="las la-cog text-primary"></i>
                    Actions
                </div>
                <div class="action-footer">
                    <div class="text-muted small">
                        <i class="las la-info-circle me-1"></i> Review all statement details above.
                    </div>
                    <div class="d-flex gap-2">
                        @if($soa->status === 'draft')
                            <a href="{{ route('admin-finance.credit-collection.billing.edit', $soa->id) }}" class="btn btn-warning d-flex align-items-center">
                                <i class="las la-edit me-2 fs-5"></i> Edit Statement
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
