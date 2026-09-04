<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
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
        .status-draft { background-color: #e9ecef; color: #495057; }
        .status-in-progress { background-color: #cce5ff; color: #004085; }
        .status-completed { background-color: #d4edda; color: #155724; }
        .status-short { background-color: #f8d7da; color: #721c24; }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex">
                    <div>
                        <h4 class="fs-24 mb-0 text-black">Pick Lists</h4>
                    </div>
                    <a href="{{ route('marketing.pick-list.management') }}" class="btn btn-primary rounded d-flex align-items-center" style="background: #ff0000; border: none;">
                        <i class="las la-plus me-2"></i> Create New Pick List
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="pickListsTable" class="display" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Pick List Number</th>
                                    <th>Sales Order</th>
                                    <th>Customer</th>
                                    <th>Date Created</th>
                                    <th>Total Items</th>
                                    <th>Items Picked</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>PL-2026-0001</strong></td>
                                    <td>SO-2026-001</td>
                                    <td>National Book Store</td>
                                    <td>2026-01-15</td>
                                    <td>15</td>
                                    <td>15</td>
                                    <td><span class="status-badge status-completed">Completed</span></td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('marketing.pick-list.management') }}?id=PL-2026-0001" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-eye"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp"><i class="las la-print"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>PL-2026-0002</strong></td>
                                    <td>SO-2026-002</td>
                                    <td>Pandayan Bookshop</td>
                                    <td>2026-01-16</td>
                                    <td>8</td>
                                    <td>6</td>
                                    <td><span class="status-badge status-short">Short</span></td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('marketing.pick-list.management') }}?id=PL-2026-0002" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-eye"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp"><i class="las la-print"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#pickListsTable').DataTable({
                order: [[3, 'desc']],
                pageLength: 25,
                responsive: true
            });
        });
    </script>
    @endpush
</x-app-layout>
