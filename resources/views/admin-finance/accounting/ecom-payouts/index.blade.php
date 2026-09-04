<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="container-fluid">
        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="las la-check-circle me-2"></i>
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="las la-exclamation-triangle me-2"></i>
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Summary Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-left-primary" style="border-left: 4px solid #0d6efd;">
                    <div class="card-body">
                        <h6 class="text-primary font-weight-bold text-uppercase small"><i class="las la-file-invoice"></i> Total Orders</h6>
                        <h3 class="mb-0 fw-bold">{{ $orders->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-left-success" style="border-left: 4px solid #198754;">
                    <div class="card-body">
                        <h6 class="text-success font-weight-bold text-uppercase small"><i class="las la-check-double"></i> Completed Payouts</h6>
                        <h3 class="mb-0 fw-bold">{{ $orders->where('ecom_payout_status', 'completed')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-left-warning" style="border-left: 4px solid #ffc107;">
                    <div class="card-body">
                        <h6 class="text-warning font-weight-bold text-uppercase small"><i class="las la-clock"></i> Pending Payouts</h6>
                        <h3 class="mb-0 fw-bold">{{ $orders->where('ecom_payout_status', '!=', 'completed')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-left-info" style="border-left: 4px solid #0dcaf0;">
                    <div class="card-body">
                        <h6 class="text-info font-weight-bold text-uppercase small"><i class="las la-wallet"></i> Total Amount</h6>
                        <h3 class="mb-0 fw-bold">₱{{ number_format($orders->sum('total_amount'), 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="card shadow border-0" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-4 pb-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <h4 class="fs-20 mb-0 text-black fw-bold">E-com Direct Payout Reconciliation</h4>
                    @if(($filters['platform'] ?? '') || ($filters['date_from'] ?? '') || ($filters['date_to'] ?? ''))
                        <span class="badge bg-warning text-dark ms-1">Filtered</span>
                    @endif
                </div>

                <!-- Inline Filters -->
                <form method="GET" action="{{ route('admin-finance.accounting.ecom-payouts.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
                    <select name="platform" class="form-select form-select-sm border-light-subtle rounded-pill px-3" style="width: 140px; font-weight: 500;">
                        <option value="">All Platforms</option>
                        @foreach($platforms as $p)
                            <option value="{{ $p }}" {{ strtolower($filters['platform'] ?? '') === strtolower($p) ? 'selected' : '' }}>
                                {{ ucfirst($p) }}
                            </option>
                        @endforeach
                    </select>

                    <input type="date" name="date_from" class="form-control form-control-sm border-light-subtle rounded-pill px-3" value="{{ $filters['date_from'] ?? '' }}" style="width: 140px;" title="Date From">

                    <span class="text-muted small">to</span>

                    <input type="date" name="date_to" class="form-control form-control-sm border-light-subtle rounded-pill px-3" value="{{ $filters['date_to'] ?? '' }}" style="width: 140px;" title="Date To">

                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3" style="background:#ff0000; border:none; font-weight:600;">
                        <i class="las la-filter me-1"></i> Filter
                    </button>

                    @if(($filters['platform'] ?? '') || ($filters['date_from'] ?? '') || ($filters['date_to'] ?? ''))
                        <a href="{{ route('admin-finance.accounting.ecom-payouts.index') }}" class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center text-muted" style="width: 32px; height: 32px;" title="Clear Filters">
                            <i class="las la-times fs-16"></i>
                        </a>
                    @endif
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="ecomPayoutsTable" class="display table table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Order/Invoice #</th>
                                <th>Platform</th>
                                <th>Date Finalized</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Attachments</th>
                                <th>Payout Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td><strong>{{ $order->so_number }}</strong></td>
                                    <td class="text-capitalize">
                                        @if($order->ecom_platform === 'lazada')
                                            <span class="badge bg-primary text-white"><i class="las la-shopping-bag me-1"></i> Lazada</span>
                                        @elseif($order->ecom_platform === 'shopee')
                                            <span class="badge bg-warning text-dark"><i class="las la-shopping-basket me-1"></i> Shopee</span>
                                        @elseif($order->ecom_platform === 'tiktok')
                                            <span class="badge bg-dark text-white"><i class="las la-music me-1"></i> TikTok</span>
                                        @else
                                            <span class="badge bg-secondary text-white">{{ $order->ecom_platform ?? 'E-commerce' }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->si_prepared_at ? \Carbon\Carbon::parse($order->si_prepared_at)->format('Y-m-d H:i') : $order->updated_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                    <td class="fw-bold">₱{{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            @if($order->pick_list_attachment)
                                                <a href="{{ asset('storage/' . $order->pick_list_attachment) }}" target="_blank" class="badge bg-info text-white text-decoration-none">
                                                    <i class="las la-paperclip"></i> Pick List
                                                </a>
                                            @endif
                                            @if($order->proof_of_payment)
                                                <a href="{{ asset('storage/' . $order->proof_of_payment) }}" target="_blank" class="badge bg-success text-white text-decoration-none">
                                                    <i class="las la-receipt"></i> Proof of Payment
                                                </a>
                                            @endif
                                            @if(!$order->pick_list_attachment && !$order->proof_of_payment)
                                                <span class="text-muted small">No attachments</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($order->ecom_payout_status === 'completed')
                                            <span class="badge bg-success text-white"><i class="las la-check-circle me-1"></i> Completed</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="las la-clock me-1"></i> Pending Payout</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('admin-finance.accounting.ecom-payouts.toggle', $order->id) }}" method="POST">
                                            @csrf
                                            @if($order->ecom_payout_status === 'completed')
                                                <button type="submit" class="btn btn-outline-warning btn-xs">
                                                    <i class="las la-undo-alt me-1"></i> Mark Pending
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-success btn-xs text-white">
                                                    <i class="las la-check me-1"></i> Mark Completed
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .border-left-danger { border-left: 4px solid #dc3545 !important; }
        .border-left-success { border-left: 4px solid #198754 !important; }
        .border-left-warning { border-left: 4px solid #ffc107 !important; }
        .border-left-primary { border-left: 4px solid #0d6efd !important; }
        .border-left-info { border-left: 4px solid #0dcaf0 !important; }
        .btn-xs {
            padding: 0.35rem 0.65rem;
            font-size: 0.825rem;
            border-radius: 4px;
        }
        .table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#ecomPayoutsTable').DataTable({
                order: [[2, 'desc']],
                pageLength: 25
            });
        });
    </script>
    @endpush
</x-app-layout>
