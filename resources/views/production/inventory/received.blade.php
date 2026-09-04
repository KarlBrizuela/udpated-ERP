<x-app-layout :title="'Received Items'" :sidebar="'production'">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Display Success Message -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <!-- Display Error Message -->
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Display Validation Errors -->
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <div class="card">
                    <div class="card-header border-0 pb-0 d-sm-flex d-block">
                        <div>
                            <h4 class="card-title mb-1">Received Transaction Logs</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="receivedItemsTable" class="display" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Receipt Number</th>
                                        <th>Ref Number</th>
                                        <th>Supplier/Source</th>
                                        <th>Receipt Date</th>
                                        <th>Book</th>
                                        <th>Quantity</th>
                                        <th>Total cost</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($receivedItems as $item)
                                    <tr>
                                        <td><strong>#REC-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                        <td>{{ $item->reference_number ?? '-' }}</td>
                                        <td>{{ $item->supplier ?? ucfirst($item->source) }}</td>
                                        <td>{{ $item->transaction_date ? \Carbon\Carbon::parse($item->transaction_date)->format('Y-m-d') : $item->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $item->book->name ?? 'Bundle / Non-Book' }} {{ isset($item->book->sku) ? '(' . $item->book->sku . ')' : '' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>₱{{ number_format($item->total_cost, 2) }}</td>
                                        <td>
                                            @if($item->status == 'completed')
                                                <span class="badge badge-success">Completed</span>
                                            @elseif($item->status == 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @else
                                                <span class="badge badge-danger">Cancelled</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Stock Modal -->
    <div class="modal fade" id="addStockModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Received Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                <div class="modal-body">
                    <form id="addStockForm" method="POST" action="{{ route('production.inventory.store-stock') }}">
                        @csrf
                        
                        <!-- Row 1: Ref No & Reference Date -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Reference Number</label>
                                <input type="text" id="referenceNumber" name="reference_number" class="form-control" placeholder="PO # / Invoice #">
                            </div>
                             <div class="col-md-6">
                                <label class="form-label">Receipt Date <span class="text-danger">*</span></label>
                                <input type="date" id="transactionDate" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <!-- Row 2: Supplier/Source & Status -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Supplier / Source <span class="text-danger">*</span></label>
                                <select id="stockSourceSelect" name="stockSource" class="form-control wide" required>
                                    <option value="supplier">Supplier</option>
                                    <option value="local">Local</option>
                                    <option value="international">International</option>
                                    <option value="return">Return</option>
                                    <option value="adjustment">Adjustment</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control wide" required>
                                    <option value="completed">Completed</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 3: Product -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0">Book <span class="text-danger">*</span></label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="newProductToggle" name="new_product_mode" value="1">
                                        <label class="form-check-label" for="newProductToggle">New Book</label>
                                    </div>
                                </div>
                                
                                <!-- Existing Product Select -->
                                <div id="existingProductContainer">
                                    <select id="productSelect" name="book_id" class="form-control selectpicker" data-live-search="true">
                                        <option value="">-- Select Book --</option>
                                        @foreach($books as $book)
                                            <option value="{{ $book->id }}">
                                                {{ $book->name }} ({{ $book->sku }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="mt-2 p-2 bg-light rounded" id="productInfo" style="display:none; font-size: 0.85rem;">
                                        <span class="me-3"><strong>SKU:</strong> <span id="productSKU">-</span></span>
                                        <span class="me-3"><strong>Stock:</strong> <span id="currentStock">-</span></span>
                                        <span><strong>Price:</strong> <span id="productPrice">-</span></span>
                                    </div>
                                </div>

                                <!-- New Product Fields -->
                                <div id="newProductContainer" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-12 mb-2">
                                            <input type="text" id="newProductName" name="new_book_name" class="form-control" placeholder="Book Name">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <input type="text" id="newProductSku" name="new_book_sku" class="form-control" placeholder="SKU">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row 4: Quantity & Total Cost -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" id="quantityInput" name="quantity" class="form-control" min="1" required placeholder="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Total Cost (₱) <span class="text-danger">*</span></label>
                                <input type="number" id="totalCostInput" name="total_cost" class="form-control" min="0" step="0.01" required placeholder="0.00">
                            </div>
                        </div>
                        
                        <!-- Hidden / Optional -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Notes (Optional)</label>
                                <textarea id="notesTextarea" name="notes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addStockForm" class="btn btn-primary" style="background: #ff0000; border-color: #ff0000;">Add Items</button>
                </div>
            </div>
        </div>
    </div>
    
    @push('modals')
    <!-- Edit Transaction Modal -->
    <div class="modal fade" id="editStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Transaction Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editStatusForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editTransactionId">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Reference Number</label>
                                <input type="text" id="editReference" name="reference_number" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Receipt Date</label>
                                <input type="date" id="editDate" name="transaction_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Supplier / Source</label>
                                <select id="editSource" name="stockSource" class="form-control" required>
                                    <option value="supplier">Supplier</option>
                                    <option value="local">Local</option>
                                    <option value="international">International</option>
                                    <option value="return">Return</option>
                                    <option value="adjustment">Adjustment</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select id="editStatusSelect" name="status" class="form-control" required>
                                    <option value="completed">Completed</option>
                                    <option value="pending">Pending</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Quantity</label>
                                <input type="number" id="editQuantity" name="quantity" class="form-control" min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Total Cost (₱)</label>
                                <input type="number" id="editCost" name="total_cost" class="form-control" min="0" step="0.01" required>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <small><i class="fas fa-info-circle"></i> <strong>Note:</strong> Changing the <strong>Quantity</strong> or <strong>Status</strong> will automatically recalculate and update the Master Stock levels.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveStatusBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    @endpush

    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <style>
        /* Received Items Styles */
        .dataTables_wrapper {
            font-size: 14px;
        }
        #receivedItemsTable {
            width: 100% !important;
            margin: 0;
            clear: both;
            border-collapse: separate;
            border-spacing: 0;
        }
        #receivedItemsTable thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            position: relative;
        }
        #receivedItemsTable tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
            color: #000;
        }
        #receivedItemsTable tbody tr:last-child td {
            border-bottom: none;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        .status-complete {
            background-color: #d1ecf1;
            color: #0c5460;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#receivedItemsTable').DataTable({
                order: [[ 0, "desc" ]] // Sort by Receipt Number desc
            });

            // Toggle New Product Mode
            $('#newProductToggle').change(function() {
                if($(this).is(':checked')) {
                    $('#existingProductContainer').hide();
                    $('#newProductContainer').show();
                    
                    // Toggle Requirements
                    $('#productSelect').removeAttr('required');
                    $('#newProductName').attr('required', true);
                    $('#newProductSku').attr('required', true);
                    
                    // Reset Select
                    $('#productSelect').val('').selectpicker('refresh');
                    $('#productInfo').hide();
                } else {
                    $('#existingProductContainer').show();
                    $('#newProductContainer').hide();
                    
                    // Toggle Requirements
                    $('#productSelect').attr('required', true);
                    $('#newProductName').removeAttr('required');
                    $('#newProductSku').removeAttr('required');
                }
            });

            // Handle Product Selection in Modal
            $('#productSelect').change(function() {
                var productId = $(this).val();
                if (productId) {
                    $('#productInfo').show();
                    $('#productSKU').text('Loading...');
                    $('#currentStock').text('Loading...');
                    $('#productPrice').text('Loading...');
                    
                    $.ajax({
                        url: '/production/inventory/get-product-details/' + productId, // Updated to match route
                        type: 'GET',
                        success: function(data) {
                            $('#productSKU').text(data.sku);
                            $('#currentStock').text(data.current_stock);
                            $('#productPrice').text('₱' + data.price);
                        },
                        error: function() {
                            $('#productSKU').text('Error');
                            $('#currentStock').text('-');
                            $('#productPrice').text('-');
                        }
                    });
                } else {
                    $('#productInfo').hide();
                }
            });

            // Focus input on modal open
            /*
            $('#addStockModal').on('shown.bs.modal', function () {
                $('#barcodeInput').focus();
            });
            */
           
           // Handle Edit Status Click (Event Delegation for DataTables)
           $(document).on('click', '.edit-status-btn', function() {
               var id = $(this).data('id');
               var status = $(this).data('status');
               var reference = $(this).data('reference');
               var date = $(this).data('date');
               var source = $(this).data('source');
               var quantity = $(this).data('quantity');
               var cost = $(this).data('cost');

               $('#editTransactionId').val(id);
               $('#editReference').val(reference);
               $('#editDate').val(date);
               $('#editSource').val(source).change(); // Fallback if using selectpicker, but standard select is used now
               $('#editStatusSelect').val(status).change();
               $('#editQuantity').val(quantity);
               $('#editCost').val(cost);
           });

           // Handle Save Status
            $('#saveStatusBtn').click(function() {
                var id = $('#editTransactionId').val();
                var formData = $('#editStatusForm').serialize(); // Serialize all fields

                $.ajax({
                    url: '/production/inventory/update-transaction/' + id,
                    type: 'PUT',
                    data: formData, // Send all data
                    success: function(response) {
                        $('#editStatusModal').modal('hide');
                        location.reload(); // Reload to see changes
                    },
                    error: function(xhr) {
                        alert('Error updating transaction: ' + (xhr.responseJSON.message || 'Unknown Error'));
                    }
                });
            });

            // Handle Delete
            $('.delete-btn').click(function() {
                var id = $(this).data('id');
                var token = $('meta[name="csrf-token"]').attr('content');

                if(confirm('Are you sure you want to delete this transaction? This will revert any stock changes.')) {
                    $.ajax({
                        url: '/production/inventory/destroy-transaction/' + id,
                        type: 'DELETE',
                        data: {
                            _token: token
                        },
                        success: function(response) {
                            location.reload();
                        },
                        error: function(xhr) {
                            alert('Error deleting transaction: ' + xhr.responseJSON.message);
                        }
                    });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
