<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="row">
        <div class="col-12">
            <div class="card order-form">
                <!-- Form Header -->
                <div class="form-header">
                    <div class="company-info">
                        <div class="company-logo">C</div>
                        <div class="company-details">
                            <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                            <div class="company-address">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                            <div class="company-contact">Tel. No.: 921-3984</div>
                        </div>
                    </div>
                    <div class="document-title">SALES ORDER</div>
                </div>

                @php
                    $isEdit = isset($order);
                    $selectedType = old('type', $isEdit ? $order->type : 'paid');
                    $selectedAreaSalesStaffId = old('area_sales_staff_id', $isEdit ? $order->area_sales_staff_id : null);
                    
                    $discountValue = 0;
                    $discountType = 'amount';
                    if ($isEdit) {
                        if ($order->discount_percentage && $order->discount_percentage > 0) {
                            $discountValue = $order->discount_percentage;
                            $discountType = 'percentage';
                        } elseif ($order->discount_amount && $order->discount_amount > 0) {
                            $discountValue = $order->discount_amount;
                            $discountType = 'amount';
                        }
                    }
                @endphp
                <form id="soForm" action="{{ $isEdit ? route('marketing.sales-orders.update', $order->id) : route('marketing.sales-orders.store', array_filter(['source' => request('source', old('source'))])) }}" method="POST" enctype="multipart/form-data" class="form-section">
                    @csrf
                    <input type="hidden" name="source" value="{{ request('source', old('source')) }}">
                    @if($isEdit)
                        @method('PUT')
                    @endif
                    
                    <!-- Customer and Order Details -->
                    <div class="customer-section">
                        <div class="customer-details">
                            <h5>Customer Information</h5>
                            <div class="form-group">
                                <label>Company:</label>
                                <select class="form-control selectpicker" data-live-search="true" data-size="8" data-live-search-placeholder="Search company..." name="customer_id" id="customerSelect" {{ $selectedType === 'area_sales_consignment' ? '' : 'required' }}>
                                    <option value="" selected disabled>Select Company...</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->customer_id }}" 
                                            data-address="{{ $customer->shipping_address ?? $customer->billing_address ?? 'No address found' }}"
                                            data-customer-name="{{ $customer->customer_name ?? '' }}"
                                            data-phone="{{ $customer->mobile ?: ($customer->main_phone ?: ($customer->work_phone ?: '')) }}"
                                            data-representatives='@json($customer->representatives ?? [])'
                                            {{ old('customer_id', $isEdit ? $order->customer_id : null) == $customer->customer_id ? 'selected' : '' }}>
                                            {{ $customer->customer_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="form-group">
                                <label>Customer Name:</label>
                                <select class="form-control" name="customer_representative" id="customerRepresentativeSelect">
                                    <option value="">Select Representative...</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Contact:</label>
                                <input type="text" class="form-control" name="customer_contact" id="customerContactInput" placeholder="Contact number of representative..." value="{{ old('customer_contact', $isEdit ? ($order->customer_contact ?? '') : '') }}">
                            </div>
                            <!-- Added Address Field -->
                            <div class="form-group">
                                <label>Address:</label>
                                <textarea class="form-control" name="billing_address" id="billingAddress" rows="2" placeholder="Customer address...">{{ $isEdit ? $order->billing_address : '' }}</textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Transaction Type:</label>
                                <select class="form-control" name="type" id="transactionType" required>
                                    <option value="paid" {{ $selectedType == 'paid' ? 'selected' : '' }}>Paid Transaction</option>
                                    <option value="charge" {{ $selectedType == 'charge' ? 'selected' : '' }}>Charge Transaction</option>
                                    <option value="area_consignment" {{ $selectedType == 'area_consignment' ? 'selected' : '' }}>Area Consignment</option>
                                    <option value="area_sales_consignment" {{ $selectedType == 'area_sales_consignment' ? 'selected' : '' }}>Area Sales Consignment</option>
                                    <option value="direct_consignment" {{ $selectedType == 'direct_consignment' ? 'selected' : '' }}>Direct Consignment</option>
                                    <option value="foreign" {{ $selectedType == 'foreign' ? 'selected' : '' }}>Foreign Order</option>
                                    <option value="complimentary" {{ $selectedType == 'complimentary' ? 'selected' : '' }}>Complimentary</option>
                                    <option value="cod" {{ $selectedType == 'cod' ? 'selected' : '' }}>Due on Receipt(COD)</option>
                                    <option value="evaluation" {{ $selectedType == 'evaluation' ? 'selected' : '' }}>Evaluation</option>
                                </select>
                            </div>
                            <div class="form-group" id="areaSalesStaffGroup" style="{{ $selectedType === 'area_sales_consignment' ? '' : 'display: none;' }}">
                                <label>Area Sales Staff:</label>
                                <select class="form-control selectpicker" data-live-search="true" data-size="8" data-live-search-placeholder="Search staff..." name="area_sales_staff_id" id="areaSalesStaffSelect" {{ $selectedType === 'area_sales_consignment' ? 'required' : '' }}>
                                    <option value="" selected disabled>Select Area Sales Staff...</option>
                                    @foreach($areaSalesStaff ?? [] as $staff)
                                        <option value="{{ $staff->id }}" {{ (string) $selectedAreaSalesStaffId === (string) $staff->id ? 'selected' : '' }}>
                                            {{ $staff->name }}{{ $staff->position ? ' - '.$staff->position : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('area_sales_staff_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="form-group">
                                <label>Remarks:</label>
                                <textarea class="form-control" name="remarks" rows="2" placeholder="Additional notes...">{{ $isEdit ? $order->remarks : '' }}</textarea>
                            </div>
                        </div>
                        <div class="order-details">
                            <h5>Order Information</h5>
                            <div class="form-group">
                                <label>Date:</label>
                                <input type="date" class="form-control" value="{{ $isEdit ? $order->created_at->format('Y-m-d') : date('Y-m-d') }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>S.O. #:</label>
                                <input type="text" class="form-control" name="so_number" value="{{ $isEdit ? $order->so_number : 'SO-'.date('Y').'-'.rand(1000,9999) }}" readonly>
                            </div>
                            
                            <!-- Added Terms and REF# -->
                            <div class="form-group">
                                <label>Terms:</label>
                                <input type="text" class="form-control" name="terms" placeholder="e.g. 30 Days" value="{{ $isEdit ? $order->terms : '' }}">
                            </div>
                            <div class="form-group">
                                <label>REF #:</label>
                                <input type="text" class="form-control" name="ref_number" placeholder="PO Reference..." value="{{ $isEdit ? $order->ref_number : '' }}">
                            </div>

                            <div class="form-group">
                                <label>Freight Option:</label>
                                <select class="form-control" name="freight_option" id="freightOptionSelect">
                                    <option value="">Select Freight Option</option>
                                    <option value="freight_collect" {{ ($isEdit && $order->freight_option == 'freight_collect') ? 'selected' : '' }}>Freight Collect</option>
                                    <option value="freight_billing" {{ ($isEdit && $order->freight_option == 'freight_billing') ? 'selected' : '' }}>Freight Billing</option>
                                    <option value="bill_client" {{ ($isEdit && $order->freight_option == 'bill_client') ? 'selected' : '' }}>Bill Client</option>
                                </select>
                            </div>

                            <div class="form-group" id="forwarderGroup" style="display: {{ ($isEdit && $order->freight_option) ? 'block' : 'none' }};">
                                <label>Forwarder / Carrier:</label>
                                <input type="text" class="form-control" name="forwarder" id="forwarderInput" placeholder="Enter Forwarder (e.g. LBC, J&T, 2GO, AP Cargo)" value="{{ $isEdit ? ($order->forwarder ?? '') : '' }}">
                            </div>

                            <div class="form-group" id="serviceFeeGroup" style="display: none;">
                                <label>Service Fee:</label>
                                <input type="number" class="form-control" name="service_fee" value="50.00" readonly>
                            </div>

                            <div class="form-group">
                                <label>PO Attachment:</label>
                                @if($isEdit && $order->attachment)
                                    <div class="mb-2">
                                        <a href="/storage/{{ $order->attachment }}" target="_blank" class="text-primary"><i class="bi bi-paperclip"></i> View Current PO</a>
                                    </div>
                                @endif
                                <!-- Premium Upload UI -->
                                <div class="upload-area p-3 border rounded-3 text-center bg-light cursor-pointer position-relative" id="uploadAreaPO" style="border: 2px dashed #ccc !important; transition: all 0.3s ease;">
                                    <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" name="attachment" id="attachmentInputPO" accept=".pdf,.jpg,.jpeg,.png">
                                    
                                    <div class="upload-content" id="uploadContentPO">
                                        <div class="mb-1">
                                            <i class="bi bi-cloud-arrow-up fs-3 text-primary"></i>
                                        </div>
                                        <h6 class="fw-bold mb-0">Upload PO File</h6>
                                    </div>

                                    <div class="file-preview d-none" id="filePreviewPO">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-file-earmark-text fs-4 text-primary"></i>
                                            <div class="text-start">
                                                <h6 class="fw-bold mb-0 text-dark" id="fileNamePO" style="font-size: 0.8rem;">filename.pdf</h6>
                                            </div>
                                            <button type="button" class="btn btn-close btn-sm ms-1" id="removeFilePO"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label>Proof of Payment Attachment:</label>
                                @if($isEdit && $order->proof_of_payment)
                                    <div class="mb-2">
                                        <a href="/storage/{{ $order->proof_of_payment }}" target="_blank" class="text-primary"><i class="bi bi-paperclip"></i> View Current Proof of Payment</a>
                                    </div>
                                @endif
                                <!-- Premium Upload UI -->
                                <div class="upload-area p-3 border rounded-3 text-center bg-light cursor-pointer position-relative" id="uploadAreaPayment" style="border: 2px dashed #ccc !important; transition: all 0.3s ease;">
                                    <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" name="proof_of_payment" id="attachmentInputPayment" accept=".pdf,.jpg,.jpeg,.png">
                                    
                                    <div class="upload-content" id="uploadContentPayment">
                                        <div class="mb-1">
                                            <i class="bi bi-cloud-arrow-up fs-3 text-primary"></i>
                                        </div>
                                        <h6 class="fw-bold mb-0">Upload Proof of Payment</h6>
                                    </div>

                                    <div class="file-preview d-none" id="filePreviewPayment">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-file-earmark-text fs-4 text-primary"></i>
                                            <div class="text-start">
                                                <h6 class="fw-bold mb-0 text-dark" id="fileNamePayment" style="font-size: 0.8rem;">filename.pdf</h6>
                                            </div>
                                            <button type="button" class="btn btn-close btn-sm ms-1" id="removeFilePayment"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <button type="button" class="btn-add-row" id="addItemBtn" onclick="if(typeof addRow === 'function') addRow();">
                        <i class="las la-plus me-2"></i>Add Item
                    </button>

                    <table class="form-table" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width: 70px;">QTY</th>
                                <th style="width: 70px;">UNIT</th>
                                <th>DESCRIPTION / PRODUCT</th>
                                <th style="width: 110px;">ISBN</th>
                                <th style="width: 90px;">AREA</th> <!-- Added AREA -->
                                <th style="width: 110px;">UNIT PRICE</th>
                                <th style="width: 120px;">DISCOUNT</th>
                                <th style="width: 110px;">AMOUNT</th>
                                <th style="width: 80px;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <!-- Dynamic rows via JS -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7" class="text-end text-uppercase"><strong>Items Subtotal:</strong></td>
                                <td class="text-end fw-bold fs-5" id="subtotalAmount">₱ 0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="7" class="text-end text-uppercase">
                                    <div class="d-inline-flex align-items-center justify-content-end gap-2">
                                        <strong>Discount:</strong>
                                        <input type="number" step="any" min="0" name="discount_value" id="discountValue" class="form-control form-control-sm text-end" style="width: 100px; display: inline-block;" value="{{ old('discount_value', $discountValue) }}">
                                        <select name="discount_type" id="discountType" class="form-select form-select-sm" style="width: 80px; display: inline-block;">
                                            <option value="amount" {{ old('discount_type', $discountType) === 'amount' ? 'selected' : '' }}>₱ (Amt)</option>
                                            <option value="percentage" {{ old('discount_type', $discountType) === 'percentage' ? 'selected' : '' }}>% (Pct)</option>
                                        </select>
                                    </div>
                                </td>
                                <td class="text-end fw-bold text-danger fs-5" id="discountAmountDisplay">- ₱ 0.00</td>
                                <td></td>
                            </tr>
                            @if($isEdit && $order->freight_charges)
                            <tr class="bg-light">
                                <td colspan="7" class="text-end text-uppercase"><strong>Freight Charges:</strong></td>
                                <td class="text-end fw-bold fs-5" id="freightChargesDisplay">₱ {{ number_format($order->freight_charges, 2) }}</td>
                                <td></td>
                            </tr>
                            @endif
                            <tr id="serviceFeeTotalRow" style="display: none;">
                                <td colspan="7" class="text-end text-uppercase"><strong>Service Fee:</strong></td>
                                <td class="text-end fw-bold fs-5">₱ 50.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="7" class="text-end text-uppercase"><strong>Total Amount:</strong></td>
                                <td class="text-end fw-bold fs-5" id="grandTotal">₱ 0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="form-actions">
                        <a href="{{ route('marketing.sales-orders.list') }}" class="btn btn-light border">Cancel</a>
                        {{--
                        <button type="submit" class="btn btn-secondary px-4" id="saveDraftBtn" name="action" value="draft">
                            <i class="las la-save me-2"></i>{{ $isEdit ? 'Update as Draft' : 'Save as Draft' }}
                        </button>
                        --}}
                        <button type="submit" class="btn btn-primary px-4" id="saveBtn" name="action" value="submit">
                            <i class="las la-check me-2"></i>{{ $isEdit ? 'Update Sales Order' : 'Create Sales Order' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden Product Options for JS clone -->
    <select id="productSource" class="d-none">
        <option value="" disabled selected>Select Product...</option>
        @foreach($products as $product)
            @php
                $imgUrl = $product->image ?? asset('images/no-book-cover.svg');
                $dispName = $product->display_name ?? $product->name;
                $stockVal = $product->stock ?? 0;
                $htmlContent = '<div class="d-flex align-items-center gap-2"><img src="'.$imgUrl.'" style="width:26px; height:26px; object-fit:cover; border-radius:4px; border:1px solid #ccc; flex-shrink:0;"> <span style="font-size:0.85rem; font-weight:500; flex:1;">'.e($dispName).'</span> <span class="badge bg-light text-dark border ms-auto" style="font-size:0.75rem; font-weight:normal;">Stock: '.$stockVal.'</span></div>';
            @endphp
            <option value="{{ $product->id }}" 
                data-price="{{ $product->price }}" 
                data-isbn="{{ $product->isbn ?? '' }}"
                data-stock="{{ $stockVal }}"
                data-image="{{ $imgUrl }}"
                data-content="{!! htmlspecialchars($htmlContent, ENT_QUOTES, 'UTF-8') !!}">
                {{ $dispName }} (Stock: {{ $stockVal }})
            </option>
        @endforeach
    </select>

    @push('styles')
    <style>
        .order-form { background: #fff; border-radius: 8px; padding: 2rem; box-shadow: 0 0 20px rgba(0, 0, 0, 0.05); }
        .form-header { margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #e0e0e0; }
        .form-header .company-info { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
        .form-header .company-logo { width: 60px; height: 60px; background: #ff0000; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; font-weight: bold; flex-shrink: 0; }
        .form-header .company-details { flex: 1; }
        .form-header .company-name { font-size: 1.25rem; font-weight: 700; color: #333; margin-bottom: 0.25rem; text-transform: uppercase; }
        .form-header .company-address, .form-header .company-contact { font-size: 0.9rem; color: #666; margin-bottom: 0.1rem; }
        .form-header .document-title { text-align: center; font-size: 1.75rem; font-weight: 700; color: #333; margin-top: 1rem; letter-spacing: 1px; }
        
        .customer-section { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem; }
        .customer-details, .order-details { background: #f8f9fa; padding: 1rem; border-radius: 6px; }
        .customer-details h5, .order-details h5 { font-weight: 600; color: #333; margin-bottom: 0.75rem; font-size: 0.95rem; }
        
        .form-group { margin-bottom: 0.75rem; }
        .form-group label { font-weight: 600; color: #333; margin-bottom: 0.25rem; display: block; font-size: 0.9rem; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; border: 1px solid #ddd; border-radius: 4px; padding: 0.5rem; font-size: 0.9rem; }
        
        .form-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; table-layout: fixed; }
        .form-table thead { background: #ff0000; color: #fff; }
        .form-table th { padding: 0.75rem; text-align: left; font-weight: 600; font-size: 0.9rem; border: 1px solid #ddd; overflow: hidden; white-space: nowrap; }
        .form-table td { padding: 0.5rem; border: 1px solid #ddd; vertical-align: middle !important; }
        /* Adjusted inputs to blend in better inside table */
        .form-table input.qty-input, .form-table input.price-input, .form-table input.isbn-input, .form-table select { width: 100%; border: none; padding: 0.5rem; background: transparent; }
        .form-table input:focus, .form-table select:focus { outline: 2px solid #ff0000; outline-offset: -2px; background: #fff; }
        .form-table tfoot { background: #f8f9fa; font-weight: 600; }
        .form-table tfoot td { padding: 0.75rem; border-top: 2px solid #333; overflow: visible; }
        
        .btn-add-row { background: #ff0000; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 4px; margin-bottom: 1rem; cursor: pointer; transition: background 0.3s; }
        .btn-add-row:hover { background: #ff6666; }
        
        .form-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid #e0e0e0; }
        
        /* Selectpicker overrides */
        .bootstrap-select .btn { 
            background-color: transparent !important; 
            border: none !important; 
            padding: 0 0.5rem !important; /* Added left padding for text */
            height: 38px !important;
            font-size: 0.9rem !important;
            text-align: left !important;
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
        }
        .bootstrap-select .dropdown-toggle:focus { outline: none !important; }
        .bootstrap-select .filter-option {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            text-align: left !important;
        }

        .product-select-td { 
            text-align: left; 
            vertical-align: middle !important;
            max-width: 0; /* forces the cell to respect table-layout:fixed */
            overflow: visible; /* MUST be visible so the dropdown menu can escape the cell */
        }
        /* Prevent the selectpicker button from stretching the column */
        .product-select-td .bootstrap-select {
            width: 100% !important;
            max-width: 100% !important;
        }
        .product-select-td .bootstrap-select .btn {
            width: 100% !important;
            max-width: 100% !important;
            overflow: hidden;
        }
        /* Truncate the displayed book name with ellipsis — dropdown itself stays intact */
        .product-select-td .bootstrap-select .filter-option-inner-inner {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }
        
        /* Consistency across inputs */
        .form-table input, .form-table .bootstrap-select {
            height: 38px;
        }
        
        /* Remove button styling */
        .remove-row {
            background-color: #ff0000 !important;
            color: white !important;
            padding: 0.4rem 0.6rem !important;
            font-size: 0.9rem;
            border: none !important;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        /* Prevent bootstrap-select dropdown from overflowing viewport and hiding search box */
        .bootstrap-select .dropdown-menu {
            max-height: 360px !important;
            overflow: hidden !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18) !important;
            border-radius: 6px !important;
        }
        .bootstrap-select .bs-searchbox {
            position: sticky !important;
            top: 0 !important;
            z-index: 1050 !important;
            background: #ffffff !important;
            padding: 8px 10px !important;
            border-bottom: 1px solid #e9ecef !important;
        }
        .bootstrap-select .bs-searchbox input {
            font-size: 0.9rem !important;
            padding: 0.4rem 0.75rem !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
        }
        .bootstrap-select .dropdown-menu .inner {
            max-height: 280px !important;
            overflow-y: auto !important;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addItemBtn = document.getElementById('addItemBtn');
            const itemsBody = document.getElementById('itemsBody');
            const productSource = document.getElementById('productSource');
            const grandTotalEl = document.getElementById('grandTotal');
            const customerSelect = document.getElementById('customerSelect');
            const transactionType = document.getElementById('transactionType');
            const areaSalesStaffGroup = document.getElementById('areaSalesStaffGroup');
            const areaSalesStaffSelect = document.getElementById('areaSalesStaffSelect');
            // Initialize bootstrap-select on customer dropdown (enable live search)
            if (typeof $ !== 'undefined' && $.fn && $.fn.selectpicker) {
                $(customerSelect).selectpicker({ size: 8, liveSearch: true });
                $(areaSalesStaffSelect).selectpicker({ size: 8, liveSearch: true });
            }
            const billingAddress = document.getElementById('billingAddress');

            function refreshSelectpicker(select) {
                if (select && typeof $ !== 'undefined' && $.fn && $.fn.selectpicker) {
                    $(select).selectpicker('refresh');
                }
            }

            function toggleAreaSalesConsignmentFields() {
                const isAreaSalesConsignment = transactionType?.value === 'area_sales_consignment';

                if (areaSalesStaffGroup) {
                    areaSalesStaffGroup.style.display = isAreaSalesConsignment ? '' : 'none';
                }

                if (areaSalesStaffSelect) {
                    areaSalesStaffSelect.required = isAreaSalesConsignment;
                    if (!isAreaSalesConsignment) {
                        areaSalesStaffSelect.value = '';
                    }
                    refreshSelectpicker(areaSalesStaffSelect);
                }

                if (customerSelect) {
                    customerSelect.required = !isAreaSalesConsignment;
                    refreshSelectpicker(customerSelect);
                }
            }

            if (transactionType) {
                transactionType.addEventListener('change', toggleAreaSalesConsignmentFields);
                toggleAreaSalesConsignmentFields();
            }

            // Freight Option Handler
            const freightOptionSelect = document.querySelector('select[name="freight_option"]');
            const serviceFeeGroup = document.getElementById('serviceFeeGroup');
            const serviceFeeTotalRow = document.getElementById('serviceFeeTotalRow');
            const forwarderGroup = document.getElementById('forwarderGroup');

            function toggleServiceFee() {
                const shouldShowServiceFee = freightOptionSelect?.value === 'freight_collect';
                if (serviceFeeGroup) {
                    serviceFeeGroup.style.display = shouldShowServiceFee ? 'block' : 'none';
                }
                if (serviceFeeTotalRow) {
                    serviceFeeTotalRow.style.display = shouldShowServiceFee ? '' : 'none';
                }
                const hasFreightOption = !!freightOptionSelect?.value;
                if (forwarderGroup) {
                    forwarderGroup.style.display = hasFreightOption ? 'block' : 'none';
                }
            }
            
            if (freightOptionSelect) {
                freightOptionSelect.addEventListener('change', function() {
                    toggleServiceFee();
                    updateGrandTotal();
                });
                
                toggleServiceFee();
            }

            // Auto-fill address & Customer Name Representatives & Contact
            const contactInput = document.getElementById('customerContactInput');
            const repSelect = document.getElementById('customerRepresentativeSelect');

            customerSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                const address = option.getAttribute('data-address');
                const companyPhone = option.getAttribute('data-phone') || '';

                if(address && address !== 'No address found') {
                    billingAddress.value = address;
                } else {
                    billingAddress.value = '';
                }

                // Populate Customer Name / Representative Dropdown
                if (repSelect) {
                    repSelect.innerHTML = '<option value="">Select Representative...</option>';

                    const primaryName = option.getAttribute('data-customer-name');
                    const repsData = option.getAttribute('data-representatives');

                    let repsArray = [];
                    if (repsData) {
                        try { repsArray = JSON.parse(repsData); } catch(e) { repsArray = []; }
                    }

                    let selectedRepPhone = '';

                    if (Array.isArray(repsArray) && repsArray.length > 0) {
                        repsArray.forEach((rep, index) => {
                            const repName = rep.name || rep.rep_name;
                            const repPhone = rep.phone || rep.mobile || rep.contact || rep.contact_number || rep.phone_number || '';

                            if (repName) {
                                const opt = document.createElement('option');
                                opt.value = repName;
                                opt.textContent = repName;
                                opt.setAttribute('data-phone', repPhone);
                                if (index === 0) {
                                    opt.selected = true;
                                    selectedRepPhone = repPhone;
                                }
                                repSelect.appendChild(opt);
                            }
                        });
                    }

                    if (contactInput) {
                        contactInput.value = selectedRepPhone || companyPhone;
                    }
                }
            });

            if (repSelect) {
                repSelect.addEventListener('change', function() {
                    const selectedOpt = this.options[this.selectedIndex];
                    const companyOpt = customerSelect.options[customerSelect.selectedIndex];
                    const companyPhone = companyOpt ? (companyOpt.getAttribute('data-phone') || '') : '';

                    if (selectedOpt) {
                        const repPhone = selectedOpt.getAttribute('data-phone');
                        if (contactInput) {
                            contactInput.value = repPhone || companyPhone;
                        }
                    }
                });
            }

            // PO Upload UI Logic
            const uploadAreaPO = document.getElementById('uploadAreaPO');
            const attachmentInputPO = document.getElementById('attachmentInputPO');
            const uploadContentPO = document.getElementById('uploadContentPO');
            const filePreviewPO = document.getElementById('filePreviewPO');
            const fileNamePO = document.getElementById('fileNamePO');
            const removeFilePO = document.getElementById('removeFilePO');

            if (uploadAreaPO) {
                uploadAreaPO.addEventListener('dragover', () => uploadAreaPO.style.borderColor = '#0d6efd');
                uploadAreaPO.addEventListener('dragleave', () => uploadAreaPO.style.borderColor = '#ccc');
            }
            if (attachmentInputPO) {
                attachmentInputPO.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        fileNamePO.textContent = file.name;
                        uploadContentPO.classList.add('d-none');
                        filePreviewPO.classList.remove('d-none');
                        uploadAreaPO.classList.remove('bg-light');
                        uploadAreaPO.classList.add('bg-white', 'border-primary');
                    }
                });
            }
            if (removeFilePO) {
                removeFilePO.addEventListener('click', function(e) {
                    e.preventDefault();
                    attachmentInputPO.value = '';
                    uploadContentPO.classList.remove('d-none');
                    filePreviewPO.classList.add('d-none');
                    uploadAreaPO.classList.add('bg-light');
                    uploadAreaPO.classList.remove('bg-white', 'border-primary');
                });
            }

            // Payment Upload UI Logic
            const uploadAreaPayment = document.getElementById('uploadAreaPayment');
            const attachmentInputPayment = document.getElementById('attachmentInputPayment');
            const uploadContentPayment = document.getElementById('uploadContentPayment');
            const filePreviewPayment = document.getElementById('filePreviewPayment');
            const fileNamePayment = document.getElementById('fileNamePayment');
            const removeFilePayment = document.getElementById('removeFilePayment');

            if (uploadAreaPayment) {
                uploadAreaPayment.addEventListener('dragover', () => uploadAreaPayment.style.borderColor = '#0d6efd');
                uploadAreaPayment.addEventListener('dragleave', () => uploadAreaPayment.style.borderColor = '#ccc');
            }
            if (attachmentInputPayment) {
                attachmentInputPayment.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        fileNamePayment.textContent = file.name;
                        uploadContentPayment.classList.add('d-none');
                        filePreviewPayment.classList.remove('d-none');
                        uploadAreaPayment.classList.remove('bg-light');
                        uploadAreaPayment.classList.add('bg-white', 'border-primary');
                    }
                });
            }
                if (removeFilePayment) {
                    removeFilePayment.addEventListener('click', function(e) {
                        e.preventDefault();
                        attachmentInputPayment.value = '';
                        uploadContentPayment.classList.remove('d-none');
                        filePreviewPayment.classList.add('d-none');
                        uploadAreaPayment.classList.add('bg-light');
                        uploadAreaPayment.classList.remove('bg-white', 'border-primary');
                    });
                }

            function calculateRow(row) {
                if (!row) return;
                const qtyInput = row.querySelector('.qty-input');
                const priceInput = row.querySelector('.price-input');
                const discountValueInput = row.querySelector('.discount-input');
                const discountTypeSelect = row.querySelector('.discount-type-select');

                const qty = parseFloat(qtyInput?.value) || 0;
                const price = parseFloat(priceInput?.value) || 0;
                const discountVal = parseFloat(discountValueInput?.value) || 0;
                const discountType = discountTypeSelect?.value || 'percentage';

                const gross = qty * price;
                let itemDiscountAmount = 0;
                if (discountType === 'percentage') {
                    itemDiscountAmount = gross * (discountVal / 100);
                } else {
                    itemDiscountAmount = discountVal;
                }

                const netSubtotal = Math.max(0, gross - itemDiscountAmount);
                const subtotalEl = row.querySelector('.subtotal-display');
                if (subtotalEl) subtotalEl.textContent = '₱ ' + netSubtotal.toFixed(2);
                updateGrandTotal();
            }

            // Global recalc triggered by qty/price inline oninput
            window.soRowRecalc = function(el) {
                calculateRow(el.closest('tr'));
            };

            function updateGrandTotal() {
                let total = 0;
                document.querySelectorAll('#itemsBody tr').forEach(row => {
                    const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
                    const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
                    const discountVal = parseFloat(row.querySelector('.discount-input')?.value) || 0;
                    const discountType = row.querySelector('.discount-type-select')?.value || 'percentage';

                    const gross = qty * price;
                    let itemDiscountAmount = 0;
                    if (discountType === 'percentage') {
                        itemDiscountAmount = gross * (discountVal / 100);
                    } else {
                        itemDiscountAmount = discountVal;
                    }

                    total += Math.max(0, gross - itemDiscountAmount);
                });

                const subtotalEl = document.getElementById('subtotalAmount');
                if (subtotalEl) subtotalEl.textContent = '₱ ' + total.toFixed(2);

                const discountVal = parseFloat(document.getElementById('discountValue')?.value) || 0;
                const discountType = document.getElementById('discountType')?.value || 'amount';
                const discountAmount = discountType === 'percentage' ? total * (discountVal / 100) : discountVal;

                const discountEl = document.getElementById('discountAmountDisplay');
                if (discountEl) discountEl.textContent = '- ₱ ' + discountAmount.toFixed(2);

                const freightEl = document.getElementById('freightChargesDisplay');
                const freightCharges = freightEl ? parseFloat(freightEl.textContent.replace(/[^\d.]/g, '')) || 0 : 0;
                const freightOpt = document.getElementById('freightOptionSelect');
                const serviceFee = freightOpt?.value === 'freight_collect' ? 50 : 0;

                // Show/hide service fee elements based on selected option
                const serviceFeeGroup = document.getElementById('serviceFeeGroup');
                const serviceFeeTotalRow = document.getElementById('serviceFeeTotalRow');
                if (freightOpt?.value === 'freight_collect') {
                    if (serviceFeeGroup) serviceFeeGroup.style.display = 'block';
                    if (serviceFeeTotalRow) serviceFeeTotalRow.style.display = '';
                } else {
                    if (serviceFeeGroup) serviceFeeGroup.style.display = 'none';
                    if (serviceFeeTotalRow) serviceFeeTotalRow.style.display = 'none';
                }

                const grandTotal = Math.max(0, total - discountAmount + freightCharges + serviceFee);
                const grandTotalEl = document.getElementById('grandTotal');
                if (grandTotalEl) grandTotalEl.textContent = '₱ ' + grandTotal.toFixed(2);
            }

            // Wire inputs
            document.getElementById('discountValue')?.addEventListener('input', updateGrandTotal);
            document.getElementById('discountType')?.addEventListener('change', updateGrandTotal);
            document.getElementById('freightOptionSelect')?.addEventListener('change', updateGrandTotal);

            // Document-level delegation for selectpicker product change
            $(document).on('changed.bs.select', '.product-select', function() {
                const select = this;
                const tr = select.closest('tr');
                if (!tr) return;

                const selectedOpt = select.options[select.selectedIndex];
                const priceInput = tr.querySelector('.price-input');
                const isbnInput = tr.querySelector('.isbn-input');
                const availabilityEl = tr.querySelector('.availability');
                const imageEl = tr.querySelector('.product-image-preview');

                if (!selectedOpt || !selectedOpt.value) {
                    if (priceInput) priceInput.value = '';
                    if (isbnInput) isbnInput.value = '';
                    calculateRow(tr);
                    return;
                }

                const price = parseFloat(selectedOpt.getAttribute('data-price')) || 0;
                const isbn = selectedOpt.getAttribute('data-isbn') || '';
                const stock = parseInt(selectedOpt.getAttribute('data-stock')) || 0;
                const image = selectedOpt.getAttribute('data-image') || '';

                if (priceInput) priceInput.value = price.toFixed(2);
                if (isbnInput) isbnInput.value = isbn;
                if (imageEl && image) imageEl.src = image;

                if (availabilityEl) {
                    if (stock <= 0) {
                        availabilityEl.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-exclamation-triangle me-1"></i>Out of Stock (0)</span>';
                    } else {
                        availabilityEl.innerHTML = '<span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Stock: ' + stock + '</span>';
                    }
                }

                calculateRow(tr);
            });

            // Also handle native change event as fallback
            document.getElementById('itemsBody')?.addEventListener('change', function(e) {
                if (e.target.classList.contains('product-select')) {
                    const tr = e.target.closest('tr');
                    if (tr) calculateRow(tr);
                }
            });

            const defaultCover = '{{ asset("images/no-book-cover.svg") }}';
            const existingItems = @json($isEdit ? $order->items : []);

            function addRow(data = null) {
                const tr = document.createElement('tr');
                const uniqueId = Date.now() + Math.random().toString(36).substring(7);

                const qtyVal = data ? data.quantity : 1;
                const unitVal = data ? (data.unit || 'pcs') : 'pcs';
                const productId = data ? (data.book_index_id ? ('index_' + data.book_index_id) : (data.bundle_id ? ('bundle_' + data.bundle_id) : ('book_' + (data.book_id || data.product_id)))) : '';
                const isbnVal = data ? (data.isbn || '') : '';
                const priceVal = data ? parseFloat(data.price) : '';
                const discountVal = data ? (parseFloat(data.discount_value) || 0) : 0;
                const discountTypeVal = data ? (data.discount_type || 'percentage') : 'percentage';

                let subtotalVal = 0;
                if (data) {
                    const gross = (parseFloat(data.quantity) || 0) * (parseFloat(data.price) || 0);
                    let dAmt = 0;
                    if (discountTypeVal === 'percentage') {
                        dAmt = gross * (discountVal / 100);
                    } else {
                        dAmt = discountVal;
                    }
                    subtotalVal = Math.max(0, gross - dAmt);
                }

                tr.innerHTML = `
                    <td>
                        <input type="number" class="qty-input" name="items[new_${uniqueId}][quantity]" min="1" value="${qtyVal}" required oninput="window.soRowRecalc(this)" style="width: 100%; text-align: center;">
                    </td>
                    <td>
                        <select class="form-control" name="items[new_${uniqueId}][unit]" style="border:none; text-align:center;">
                            <option value="pcs" ${unitVal === 'pcs' ? 'selected' : ''}>pcs</option>
                            <option value="set" ${unitVal === 'set' ? 'selected' : ''}>set</option>
                        </select>
                    </td>
                    <td class="product-select-td" style="vertical-align: middle;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="product-image-container border rounded bg-white" style="width: 42px; height: 42px; min-width: 42px; overflow: hidden; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                <img src="${defaultCover}" class="product-image-preview" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="flex-grow-1" style="min-width: 0;">
                                <select class="form-control product-select selectpicker" data-live-search="true" data-size="8" data-live-search-placeholder="Search product..." name="items[new_${uniqueId}][product_id]" required>
                                    ${productSource.innerHTML}
                                </select>
                            </div>
                        </div>
                    </td>
                    <td>
                        <input type="text" class="isbn-input" name="items[new_${uniqueId}][isbn]" value="${isbnVal}" readonly style="width: 100%; border: none; background: transparent;">
                        <div class="availability small text-muted mt-1">Stock: -</div>
                    </td>
                    <td>
                        <input type="text" class="area-input" name="items[new_${uniqueId}][area]" value="${data ? (data.area || '') : ''}" placeholder="Area..." style="width: 100%; border: none; background: transparent; height: 38px;">
                    </td>
                    <td>
                        <input type="number" class="price-input" name="items[new_${uniqueId}][price]" step="0.01" value="${priceVal !== '' ? parseFloat(priceVal).toFixed(2) : ''}" required oninput="window.soRowRecalc(this)" style="width: 100%; text-align: right; border: 1px solid #eee;">
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            <input type="number" step="any" min="0" class="discount-input" name="items[new_${uniqueId}][discount_value]" value="${discountVal > 0 ? discountVal : ''}" placeholder="0" oninput="window.soRowRecalc(this)" style="width: 55%; text-align: right; border: 1px solid #eee; border-radius: 4px; padding: 0.25rem 0.3rem;">
                            <select class="form-select form-select-sm discount-type-select" name="items[new_${uniqueId}][discount_type]" onchange="window.soRowRecalc(this)" style="width: 45%; font-size: 0.8rem; padding: 0.25rem 0.2rem; border-radius: 4px;">
                                <option value="percentage" ${discountTypeVal === 'percentage' ? 'selected' : ''}>%</option>
                                <option value="amount" ${discountTypeVal === 'amount' ? 'selected' : ''}>₱</option>
                            </select>
                        </div>
                    </td>
                    <td class="subtotal-display fw-bold text-end pe-3">₱ ${subtotalVal.toFixed(2)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm remove-row"><i class="bi bi-trash me-1"></i>Remove</button>
                    </td>
                `;

                const select = tr.querySelector('.product-select');
                const removeBtn = tr.querySelector('.remove-row');

                removeBtn.addEventListener('click', function() {
                    tr.remove();
                    updateGrandTotal();
                });

                itemsBody.appendChild(tr);

                // Initialize selectpicker
                if ($.fn.selectpicker) {
                    $(select).selectpicker({ size: 8, liveSearch: true, liveSearchPlaceholder: 'Search product...' });
                }

                // Set selected product in edit mode
                if (productId) {
                    $(select).selectpicker('val', productId);
                    // Populate price, isbn, stock from the selected option
                    const selectedOpt = select.options[select.selectedIndex];
                    if (selectedOpt && selectedOpt.value) {
                        const priceInput = tr.querySelector('.price-input');
                        const isbnInput = tr.querySelector('.isbn-input');
                        const availabilityEl = tr.querySelector('.availability');
                        if (priceInput && !priceInput.value) priceInput.value = parseFloat(selectedOpt.getAttribute('data-price') || 0).toFixed(2);
                        if (isbnInput && !isbnInput.value) isbnInput.value = selectedOpt.getAttribute('data-isbn') || '';
                        const stock = parseInt(selectedOpt.getAttribute('data-stock')) || 0;
                        if (availabilityEl) {
                            availabilityEl.innerHTML = stock <= 0
                                ? '<span class="text-danger fw-bold"><i class="bi bi-exclamation-triangle me-1"></i>Out of Stock (0)</span>'
                                : '<span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Stock: ' + stock + '</span>';
                        }
                    }
                    calculateRow(tr);
                }

                updateGrandTotal();
            }

            window.addRow = addRow;

            addItemBtn.addEventListener('click', () => {
                if (itemsBody.querySelectorAll('tr').length >= 24) {
                    alert('Maximum of 24 products allowed per order.');
                    return;
                }
                addRow();
            });
            
            // Initialize rows
            if (existingItems.length > 0) {
                existingItems.forEach(item => {
                    addRow(item);
                });
            } else {
                addRow();
            }

            // Form submission feedback and validation
            const soForm = document.getElementById('soForm');
            const saveBtn = document.getElementById('saveBtn');

            soForm.addEventListener('submit', function(e) {
                // Check if items exist
                if (itemsBody.rows.length === 0) {
                    e.preventDefault();
                    alert('Please add at least one item to the order.');
                    return;
                }

                let validationErrors = [];
                let rowCount = 0;

                $(itemsBody).find('tr').each(function(index) {
                    rowCount++;
                    const select = $(this).find('select.product-select');
                    const qtyInput = $(this).find('input.qty-input');
                    const productId = select.val();
                    const qty = parseInt(qtyInput.val()) || 0;

                    if (!productId) {
                        validationErrors.push(`Item #${rowCount}: Please select a product.`);
                        return;
                    }

                    const selectedOption = select.find('option:selected');
                    const productName = selectedOption.text().trim();
                    const stock = selectedOption.data('stock') !== undefined ? parseInt(selectedOption.data('stock')) : 0;

                    if (qty <= 0) {
                        validationErrors.push(`"${productName}": Quantity must be at least 1.`);
                    } else if (stock <= 0) {
                        validationErrors.push(`"${productName}": Item is OUT OF STOCK (Stock: 0). Cannot create order for out-of-stock items.`);
                    } else if (qty > stock) {
                        validationErrors.push(`"${productName}": Requested quantity (${qty}) exceeds available stock (${stock}).`);
                    }
                });

                if (validationErrors.length > 0) {
                    e.preventDefault();
                    alert('Cannot proceed with Sales Order:\n\n• ' + validationErrors.join('\n• '));
                    return;
                }

                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="las la-spinner la-spin me-2"></i> {{ $isEdit ? "Updating..." : "Creating..." }}';
            });
        });
    </script>
    @endpush
</x-app-layout>
