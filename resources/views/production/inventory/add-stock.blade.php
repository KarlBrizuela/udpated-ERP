<x-app-layout :title="'Add Stock'" :sidebar="'production'">
    <div class="container-fluid">
        <!-- Display Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card stock-form">

                    <div class="form-header">
                        <h2 class="document-title">Add Stock to Inventory</h2>
                        <p class="text-muted">Select a completed received item below to auto-fill the form, then enter the quantity to add.</p>
                    </div>

                    <form id="addStockForm" method="POST" action="{{ route('production.inventory.process-add-stock') }}">
                        @csrf
                        
                        <!-- Select Completed Received Item -->
                        <div class="form-section">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Select a completed received item to auto-fill the form.
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Select Completed Received Item <span class="text-danger">*</span></label>
                                    <select id="receivedItemSelect" name="transaction_id" class="form-control" required>
                                        <option value="">-- Select Received Item --</option>
                                        @foreach($completedItems as $item)
                                            <option value="{{ $item->id }}" 
                                                data-reference="{{ $item->reference_number }}"
                                                data-date="{{ $item->transaction_date }}"
                                                data-source="{{ $item->source }}"
                                                data-quantity="{{ $item->quantity }}"
                                                data-cost="{{ $item->total_cost }}"
                                                data-book="{{ $item->book->name ?? 'Unknown' }}"
                                                data-price="{{ $item->book->price ?? 0 }}">
                                                #REC-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }} - {{ $item->book->name ?? 'Unknown' }} ({{ \Carbon\Carbon::parse($item->transaction_date)->format('M d, Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Found {{ count($completedItems) }} completed item(s)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Row 1: Ref No & Receipt Date -->
                        <div class="form-section">
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
                        </div>

                        <!-- Row 2: Supplier/Source & Product -->
                        <div class="form-section">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Supplier / Source <span class="text-danger">*</span></label>
                                    <select id="stockSourceSelect" name="stockSource" class="form-control" required>
                                        <option value="">-- Select Source --</option>
                                        <option value="supplier">Supplier</option>
                                        <option value="local">Local</option>
                                        <option value="international">International</option>
                                        <option value="return">Return</option>
                                        <option value="adjustment">Adjustment</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Book Title</label>
                                    <input type="text" id="bookDisplay" class="form-control" placeholder="Book name" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Row 3: Quantity, Selling Price & Total Cost -->
                        <div class="form-section">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Quantity to Add <span class="text-danger">*</span></label>
                                    <input type="number" id="quantityInput" name="quantity" class="form-control" min="1" required placeholder="Enter quantity">
                                    <small class="text-muted">Quantity to add to inventory</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Selling Price (₱)</label>
                                    <input type="number" id="sellingPriceInput" name="selling_price" class="form-control" min="0" step="0.01" placeholder="0.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Total Cost (₱) <span class="text-danger">*</span></label>
                                    <input type="number" id="totalCostInput" name="total_cost" class="form-control" min="0" step="0.01" required placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary-custom" onclick="window.location.reload()">Reset</button>
                            <button type="submit" class="btn btn-primary-custom">Add Stock</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <style>
        .stock-form {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .form-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }

        .form-header .document-title {
            text-align: center;
            font-size: 1.75rem;
            font-weight: 700;
            color: #333;
            margin-top: 1rem;
            letter-spacing: 1px;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-section .form-control,
        .form-section .bootstrap-select {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
        }

        .form-section .form-control:focus,
        .form-section .bootstrap-select:focus {
            outline: 2px solid #ff0000;
            outline-offset: -2px;
            border-color: #ff0000;
        }

        .barcode-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }

        .barcode-input-group {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .barcode-input-group input {
            flex: 1;
        }

        .barcode-input-group .btn-scan {
            background: #ff0000;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .barcode-input-group .btn-scan:hover {
            background: #ff6666;
        }

        .product-info {
            background: #e8f5e9;
            padding: 1rem;
            border-radius: 6px;
            margin-top: 1rem;
            display: none;
        }

        .product-info.show {
            display: block;
        }

        .product-info h6 {
            margin: 0 0 0.5rem 0;
            color: #2e7d32;
        }

        .product-info p {
            margin: 0.25rem 0;
            color: #555;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e0e0e0;
        }

        .btn-primary-custom {
            background: #ff0000;
            color: #fff;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-primary-custom:hover {
            background: #ff6666;
        }

        .btn-secondary-custom {
            background: #6c757d;
            color: #fff;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-secondary-custom:hover {
            background: #5a6268;
        }

        .row {
            margin-bottom: 1rem;
        }

        @media print {
            .sidebar,
            .header,
            .form-actions {
                display: none;
            }

            .stock-form {
                box-shadow: none;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        console.log('Add Stock page JavaScript loaded');
        
        $(document).ready(function() {
            console.log('jQuery ready, setting up auto-fill');
            
            // Auto-fill form when received item is selected
            $('#receivedItemSelect').on('change', function() {
                console.log('Dropdown changed!');
                const selectedOption = $(this).find('option:selected');
                const selectedValue = selectedOption.val();
                
                console.log('Selected value:', selectedValue);
                
                if (selectedValue) {
                    // Get data from selected option using .data() method
                    const reference = selectedOption.data('reference');
                    const date = selectedOption.data('date');
                    const source = selectedOption.data('source');
                    const quantity = selectedOption.data('quantity');
                    const cost = selectedOption.data('cost');
                    const book = selectedOption.data('book');
                    const price = selectedOption.data('price');
                    
                    console.log('Auto-filling with:', {reference, date, source, quantity, cost, book, price});
                    
                    // Fill form fields
                    $('#referenceNumber').val(reference || '');
                    $('#transactionDate').val(date || '');
                    $('#stockSourceSelect').val(source || '');
                    $('#bookDisplay').val(book || '');
                    $('#quantityInput').val(quantity || '').focus();
                    $('#totalCostInput').val(cost || '');
                    $('#sellingPriceInput').val(price || '');
                    
                    console.log('Form auto-filled successfully!');
                    alert('Form auto-filled! Please review the values.');
                } else {
                    console.log('No value selected, clearing form');
                    // Clear form
                    $('#referenceNumber').val('');
                    $('#transactionDate').val('{{ date("Y-m-d") }}');
                    $('#stockSourceSelect').val('');
                    $('#bookDisplay').val('');
                    $('#quantityInput').val('');
                    $('#totalCostInput').val('');
                    $('#sellingPriceInput').val('');
                }
            });
            
            console.log('Auto-fill handler attached');
        });
    </script>
    @endpush
</x-app-layout>
