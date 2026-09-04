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
        .status-delivered { background-color: #d4edda; color: #155724; }
        .status-in-transit { background-color: #cce5ff; color: #004085; }
        .status-pending { background-color: #fff3cd; color: #856404; }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex">
                    <div>
                        <h4 class="fs-24 mb-0 text-black">Delivery Receipts</h4>
                    </div>
                    <a href="{{ route('marketing.delivery-receipt') }}" class="btn btn-primary rounded d-flex align-items-center" style="background: #ff0000; border: none;">
                        <i class="las la-plus me-2"></i> Create New Receipt
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="deliveryReceiptsTable" class="display" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>DR Number</th>
                                    <th>Sales Order</th>
                                    <th>Customer</th>
                                    <th>Delivery Date</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Prepared By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>DR-2026-0001</strong></td>
                                    <td>SO-2026-001</td>
                                    <td>National Book Store</td>
                                    <td>2026-01-16</td>
                                    <td>₱64,500.00</td>
                                    <td><span class="status-badge status-delivered">Delivered</span></td>
                                    <td>Johndoe</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('marketing.delivery-receipt') }}?id=DR-2026-0001" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-eye"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp"><i class="las la-print"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>DR-2026-0002</strong></td>
                                    <td>SO-2026-002</td>
                                    <td>Pandayan Bookshop</td>
                                    <td>2026-01-17</td>
                                    <td>₱28,900.00</td>
                                    <td><span class="status-badge status-in-transit">In Transit</span></td>
                                    <td>Johndoe</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('marketing.delivery-receipt') }}?id=DR-2026-0002" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-eye"></i></a>
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
            $('#deliveryReceiptsTable').DataTable({
                order: [[3, 'desc']],
                pageLength: 25,
                responsive: true
            });
        });
    </script>
    @endpush
</x-app-layout>
