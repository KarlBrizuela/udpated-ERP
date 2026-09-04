<x-app-layout :title="$title" :sidebar="$sidebar">
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
                <div class="card shadow-sm border-left-danger" style="border-left: 4px solid #dc3545;">
                    <div class="card-body">
                        <h5 class="text-danger"><i class="las la-file-invoice-dollar"></i> Total Reports</h5>
                        <h3>{{ $reports->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-left-success" style="border-left: 4px solid #28a745;">
                    <div class="card-body">
                        <h5 class="text-success"><i class="las la-wallet"></i> Total Net Amount</h5>
                        <h3>PhP {{ number_format($reports->sum('total_amount'), 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-left-info" style="border-left: 4px solid #17a2b8;">
                    <div class="card-body">
                        <h5 class="text-info"><i class="las la-truck"></i> Total Freight</h5>
                        <h3>PhP {{ number_format($reports->sum('total_freight'), 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-left-primary" style="border-left: 4px solid #007bff;">
                    <div class="card-body">
                        <h5 class="text-primary"><i class="las la-chart-bar"></i> Total Gross Sales</h5>
                        <h3>PhP {{ number_format($reports->sum('total_gross_sales'), 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="card shadow">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="fs-20 mb-0 text-black">E-FORD Payout Reports (Accounting)</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="accountingEfordPayoutsTable" class="display table table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Report #</th>
                                <th>Date Submitted</th>
                                <th>Prepared By</th>
                                <th>Customer Selected</th>
                                <th>Period</th>
                                <th>Net Amount</th>
                                <th>Freight</th>
                                <th>Gross Sales</th>
                                <th>Attachments</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td><strong>EF-{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                    <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $report->creator->name ?? 'N/A' }}</td>
                                    <td>{{ $report->customer->customer_name ?? 'N/A' }}</td>
                                    <td>{{ $report->period }}</td>
                                    <td>PhP {{ number_format($report->total_amount, 2) }}</td>
                                    <td>PhP {{ number_format($report->total_freight, 2) }}</td>
                                    <td><strong>PhP {{ number_format($report->total_gross_sales, 2) }}</strong></td>
                                    <td>
                                        @if($report->attachments && count($report->attachments) > 0)
                                            @if(count($report->attachments) === 1)
                                                <a href="{{ route('admin-finance.accounting.eford-payouts.download', ['id' => $report->id, 'index' => 0]) }}" class="badge bg-danger text-white text-decoration-none" target="_blank" title="Click to view file">
                                                    <i class="las la-paperclip"></i> 1 file
                                                </a>
                                            @else
                                                <div class="dropdown">
                                                    <span class="badge bg-danger text-white dropdown-toggle" style="cursor: pointer;" id="attachDrop{{ $report->id }}" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false" title="Click to see all files">
                                                        <i class="las la-paperclip"></i> {{ count($report->attachments) }} file(s)
                                                    </span>
                                                    <ul class="dropdown-menu shadow-sm" aria-labelledby="attachDrop{{ $report->id }}">
                                                        @foreach($report->attachments as $index => $path)
                                                            <li>
                                                                <a class="dropdown-item text-dark" href="{{ route('admin-finance.accounting.eford-payouts.download', ['id' => $report->id, 'index' => $index]) }}" target="_blank">
                                                                    <i class="las la-file-pdf text-danger me-1"></i> {{ basename($path) }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted">None</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <a href="{{ route('admin-finance.accounting.eford-payouts.show', $report->id) }}" class="btn btn-primary btn-xs mb-1" target="_blank">
                                                <i class="las la-eye"></i> View Report
                                            </a>
                                            @if($report->attachments && count($report->attachments) > 0)
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-outline-danger btn-xs dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="las la-download"></i> Files
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @foreach($report->attachments as $index => $path)
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('admin-finance.accounting.eford-payouts.download', ['id' => $report->id, 'index' => $index]) }}">
                                                                    <i class="las la-file"></i> {{ basename($path) }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
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
        .border-left-success { border-left: 4px solid #28a745 !important; }
        .border-left-info { border-left: 4px solid #17a2b8 !important; }
        .border-left-primary { border-left: 4px solid #007bff !important; }
        .btn-xs {
            padding: 0.35rem 0.65rem;
            font-size: 0.875rem;
        }
        .dropdown-item {
            font-size: 0.85rem;
            padding: 0.25rem 1rem;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#accountingEfordPayoutsTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 25
            });
        });
    </script>
    @endpush
</x-app-layout>
