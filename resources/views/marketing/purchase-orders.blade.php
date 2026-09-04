<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .status-badge { padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; display: inline-block; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-approved { background-color: #d1ecf1; color: #0c5460; }
        .status-in-transit { background-color: #d4edda; color: #155724; }
        .status-received { background-color: #d1ecf1; color: #0c5460; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex">
                    <div>
                        <h4 class="fs-20 mb-0 text-black">Purchase Orders</h4>
                    </div>
                    <div class="d-flex align-items-center mt-3 mt-sm-0">
                        <input type="text" class="form-control me-3" placeholder="Search POs..." id="poSearch" style="max-width: 300px;">
                        <button type="button" class="btn btn-primary rounded d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#createPOModal" style="background: #ff0000; border: none;">
                            <i class="las la-plus me-2"></i>
                            <span>Create New PO</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="purchaseOrderTable" class="display" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>PO Number</th>
                                    <th>Supplier</th>
                                    <th>Order Date</th>
                                    <th>Expected Delivery</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>#PO-001</strong></td>
                                    <td>ABC Wholesale Corp</td>
                                    <td>2024-01-15</td>
                                    <td>2024-01-25</td>
                                    <td>₱12,950.00</td>
                                    <td><span class="status-badge status-approved">Approved</span></td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1" data-bs-toggle="modal" data-bs-target="#viewPOModal"><i class="fas fa-eye"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-warning shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-success shadow btn-xs sharp me-1"><i class="fas fa-print"></i></a>
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

    @push('modals')
    <!-- Create/Edit Purchase Order Modal -->
    <div class="modal fade" id="createPOModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Purchase Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createPOForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-black font-w500">Supplier <span class="text-danger">*</span></label>
                                <select class="form-control" name="supplier_id" required>
                                    <option value="">Select Supplier</option>
                                    <option value="1">ABC Wholesale Corp</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="text-black font-w500">Order Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="order_date" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="text-black font-w500">Expected Delivery <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="expected_delivery" required>
                            </div>
                        </div>
                        <!-- Items Table and other fields -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="savePOBtn">Save Purchase Order</button>
                </div>
            </div>
        </div>
    </div>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#purchaseOrderTable').DataTable({
                pageLength: 25,
                responsive: true
            });
        });
    </script>
    @endpush
</x-app-layout>
