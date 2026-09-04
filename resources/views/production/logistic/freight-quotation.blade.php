<x-app-layout :title="$title" :sidebar="$sidebar">
    @push('styles')
    <style>
        .quotation-container { background: #fff; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 24px rgba(0,0,0,0.06); margin-bottom: 2rem; }
        .page-header { margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; }
        .page-header h1 { font-size: 1.75rem; font-weight: 700; color: #333; margin: 0; }
        .btn-create { background: linear-gradient(135deg, #cc0000, #ff3333); color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: 6px; cursor: pointer; font-weight: 600; transition: all 0.3s; }
        .btn-create:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(255,0,0,0.3); }
        
        .quotation-header { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 2rem; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 2px solid #e0e0e0; }
        .header-item { }
        .header-item label { font-weight: 600; color: #666; font-size: 0.9rem; display: block; margin-bottom: 0.5rem; }
        .header-item input, .header-item .value { background: #f8f9fa; border: 2px solid #ddd; border-radius: 6px; padding: 0.75rem; font-size: 0.95rem; width: 100%; transition: all 0.2s; }
        .header-item .value { background: #fff; font-weight: 600; color: #333; }
        .header-item input.is-invalid { border-color: #dc3545 !important; background: #fff5f5; }
        .header-item .error-text { color: #dc3545; font-size: 0.85rem; margin-top: 0.25rem; font-weight: 600; }
        
        .shipment-column input.is-invalid, .shipment-column textarea.is-invalid, .shipment-column select.is-invalid { border-color: #dc3545 !important; border-width: 2px; background: #fff5f5; }
        .shipment-column .error-text { color: #dc3545; font-size: 0.75rem; margin-top: 0.2rem; font-weight: 600; }
        
        .rate-row input.is-invalid { border-color: #dc3545 !important; border-width: 2px; background: #fff5f5; }
        .rate-row .error-text { color: #dc3545; font-size: 0.75rem; margin-top: 0.2rem; font-weight: 600; }
        
        .cargo-table input.is-invalid, .cargo-table select.is-invalid { border-color: #dc3545 !important; border-width: 2px; background: #fff5f5; }
        
        .section-header { font-size: 1.1rem; font-weight: 700; color: #333; margin: 1.5rem 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid #cc0000; }
        
        .section-box { background: #f8f9fa; border: 1px solid #ddd; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; }
        
        .shipment-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; }
        .shipment-column { }
        .shipment-column h6 { font-weight: 700; color: #333; margin-bottom: 0.75rem; font-size: 0.95rem; }
        .shipment-column .field { margin-bottom: 0.75rem; }
        .shipment-column label { font-weight: 600; color: #666; font-size: 0.85rem; display: block; margin-bottom: 0.25rem; }
        .shipment-column input, .shipment-column textarea, .shipment-column select { width: 100%; border: 1px solid #ddd; border-radius: 4px; padding: 0.5rem; font-size: 0.9rem; }
        .shipment-column textarea { resize: vertical; min-height: 60px; }
        
        .cargo-table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
        .cargo-table th { background: #f0f0f0; padding: 0.75rem; text-align: left; font-weight: 600; font-size: 0.9rem; border: 1px solid #ddd; }
        .cargo-table td { padding: 0.75rem; border: 1px solid #ddd; }
        .cargo-table input, .cargo-table select { width: 100%; border: 1px solid #ddd; border-radius: 4px; padding: 0.5rem; font-size: 0.85rem; }
        
        .rate-breakdown { background: #f8f9fa; border: 1px solid #ddd; border-radius: 8px; padding: 1.5rem; }
        .rate-row { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem; align-items: center; }
        .rate-row label { font-weight: 600; color: #333; }
        .rate-row input { border: 1px solid #ddd; border-radius: 4px; padding: 0.5rem; font-size: 0.9rem; text-align: right; }
        .rate-total { border-top: 2px solid #333; padding-top: 1rem; font-weight: 700; color: #333; }
        
        .total-section { background: linear-gradient(135deg, #fff0f0, #ffe0e0); border: 2px solid #cc0000; border-radius: 8px; padding: 2rem; text-align: center; margin-top: 2rem; }
        .total-section h3 { font-size: 0.95rem; font-weight: 600; color: #666; margin-bottom: 0.5rem; }
        .total-amount { font-size: 2rem; font-weight: 700; color: #cc0000; }
        
        .action-buttons { display: flex; gap: 1rem; margin-top: 2rem; justify-content: flex-end; }
        .btn-submit { background: linear-gradient(135deg, #cc0000, #ff3333); color: #fff; border: none; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 1rem; transition: all 0.3s; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(255,0,0,0.3); }
        .btn-cancel { background: #6c757d; color: #fff; border: none; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 1rem; }
        .btn-cancel:hover { background: #545b62; }
        
        .table { width: 100%; border-collapse: collapse; }
        .table thead { background: linear-gradient(135deg, #cc0000, #ff0000); color: #fff; }
        .table th { padding: 1rem; text-align: left; font-weight: 600; font-size: 0.9rem; }
        .table td { padding: 0.75rem 1rem; border-bottom: 1px solid #e0e0e0; }
        .table tbody tr:hover { background: #f8f9fa; }
        
        .status-badge { padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .btn-sm { padding: 0.4rem 0.8rem; font-size: 0.85rem; border-radius: 4px; cursor: pointer; border: none; }
        .btn-view { background: #007bff; color: #fff; }
        .btn-view:hover { background: #0056b3; }
        .btn-edit { background: #28a745; color: #fff; }
        .btn-edit:hover { background: #1e7e34; }
        .btn-delete { background: #dc3545; color: #fff; }
        .btn-delete:hover { background: #bb2d3b; }
        
        .empty-state { text-align: center; padding: 3rem 1rem; color: #999; }
        .empty-state i { font-size: 3rem; color: #ddd; margin-bottom: 1rem; display: block; }
        
        @media (max-width: 768px) {
            .quotation-header { grid-template-columns: 1fr; }
            .shipment-grid { grid-template-columns: 1fr; }
            .rate-row { grid-template-columns: 1fr; }
        }
    </style>
    @endpush

    <div class="quotation-container">
        <div class="page-header">
            <h1><i class="las la-file-invoice-dollar me-2"></i>Freight Quotation</h1>
            <button class="btn-create" onclick="toggleCreateForm()">
                <i class="las la-plus me-2"></i>New Quotation
            </button>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="las la-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="las la-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="las la-exclamation-circle me-2"></i>Validation Errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Create/Edit Form -->
        <div id="quotationForm" style="display: none; margin-bottom: 2rem;">
            <form id="freightQuotationForm" method="POST" action="{{ route('production.logistic.freight-quotation.store') }}">
                @csrf
                
                <!-- Header Section -->
                <div class="quotation-header">
                    <div class="header-item">
                        <label>Quote #:</label>
                        <input type="text" name="quote_number" id="quoteNumber" placeholder="FRT-2026-001" readonly required>
                        @error('quote_number')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                    <div class="header-item">
                        <label>Date:</label>
                        <input type="date" name="quote_date" value="{{ old('quote_date') }}" required>
                        @error('quote_date')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                    <div class="header-item">
                        <label>Validity: (Days)</label>
                        <input type="number" name="validity_days" value="{{ old('validity_days', 2) }}" min="1" required>
                        @error('validity_days')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- 1. SHIPMENT DETAILS -->
                <h3 class="section-header">1. Shipment Details</h3>
                <div class="section-box">
                    <div class="shipment-grid">
                        <!-- Origin -->
                        <div class="shipment-column">
                            <h6>Origin (Pick-up)</h6>
                            <div class="field">
                                <label>Contact:</label>
                                <input type="text" name="origin_contact" placeholder="Enter contact name">
                            </div>
                            <div class="field">
                                <label>Address:</label>
                                <textarea name="origin_address" placeholder="Enter full address"></textarea>
                            </div>
                            <div class="field">
                                <label>Province:</label>
                                <input type="text" name="origin_province" placeholder="Enter province">
                            </div>
                        </div>

                        <!-- Destination -->
                        <div class="shipment-column">
                            <h6>Destination (Delivery)</h6>
                            <div class="field">
                                <label>Contact:</label>
                                <input type="text" name="destination_contact" placeholder="Enter contact name">
                            </div>
                            <div class="field">
                                <label>Address:</label>
                                <textarea name="destination_address" placeholder="Enter full address"></textarea>
                            </div>
                            <div class="field">
                                <label>Province:</label>
                                <input type="text" name="destination_province" placeholder="Enter province">
                            </div>
                        </div>

                        <!-- Service Details -->
                        <div class="shipment-column">
                            <h6>Service Details</h6>
                            <div class="field">
                                <label>Mode:</label>
                                <select name="service_mode">
                                    <option value="">Select Mode</option>
                                    <option value="Sea Freight">Sea Freight</option>
                                    <option value="Air Freight">Air Freight</option>
                                    <option value="Land Freight">Land Freight</option>
                                    <option value="Mixed">Mixed</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Freight Option:</label>
                                <select name="freight_option" id="freightOption">
                                    <option value="">Select Freight Option</option>
                                    <option value="freight_collect" {{ old('freight_option') === 'freight_collect' ? 'selected' : '' }}>Freight Collect</option>
                                    <option value="freight_billing" {{ old('freight_option') === 'freight_billing' ? 'selected' : '' }}>Freight Billing</option>
                                    <option value="bill_client" {{ old('freight_option') === 'bill_client' ? 'selected' : '' }}>Bill Client</option>
                                </select>
                                @error('freight_option')<div class="error-text">{{ $message }}</div>@enderror
                            </div>
                            <div class="field" id="forwarderField" style="display: {{ old('freight_option') === 'bill_client' ? 'block' : 'none' }};">
                                <label>Forwarder <span class="text-danger">*</span>:</label>
                                <input type="text" name="forwarder" placeholder="Enter Forwarder (e.g. LBC, J&T, 2GO, AP Cargo)" value="{{ old('forwarder') }}">
                            </div>
                            <div class="field">
                                <label>Carrier:</label>
                                <input type="text" name="service_carrier" placeholder="Enter carrier name">
                            </div>
                            <div class="field">
                                <label>Remarks:</label>
                                <textarea name="service_remarks" placeholder="Enter remarks"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. CARGO DESCRIPTION -->
                <h3 class="section-header">2. Cargo Description</h3>
                <div class="section-box">
                    <table class="cargo-table">
                        <thead>
                            <tr>
                                <th>Qty</th>
                                <th>Package Type</th>
                                <th>Dimensions (L×W×H)</th>
                                <th>Gross Weight (kg)</th>
                                <th>Vol. Weight (kg)</th>
                            </tr>
                        </thead>
                        <tbody id="cargoRows">
                            <tr>
                                <td><input type="number" name="cargo_qty[]" min="1" placeholder="0"></td>
                                <td><select name="cargo_package_type[]"><option value="">Select Type</option><option>Box</option><option>Parcel</option><option>Carton</option><option>Crate</option><option>LCL</option><option>FCL</option><option>Pallets</option></select></td>
                                <td><input type="text" name="cargo_dimensions[]" placeholder="L × W × H"></td>
                                <td><input type="number" name="cargo_gross_weight[]" step="0.01" placeholder="0"></td>
                                <td><input type="number" name="cargo_vol_weight[]" step="0.01" placeholder="0"></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn-create" onclick="addCargoRow()" style="font-size: 0.9rem;">
                        <i class="las la-plus me-1"></i>Add Row
                    </button>
                </div>

                <!-- 3. RATE BREAKDOWN -->
                <h3 class="section-header">3. Rate Breakdown</h3>
                <div class="rate-breakdown">
                    <div class="rate-row">
                        <label>Estimated Freight</label>
                        <input type="number" name="estimated_freight" step="0.01" placeholder="0.00" id="freightAmount">
                        <input type="number" name="estimated_freight_total" id="freightTotal" readonly style="background: #f0f0f0;">
                    </div>

                    <div class="rate-row">
                        <label id="serviceFeeLabel">Service Fee</label>
                        <div></div>
                        <input type="number" name="service_fee_total" id="serviceFeeTotal" readonly style="background: #f0f0f0;">
                    </div>
                    <div class="rate-row rate-total">
                        <label>TOTAL QUOTATION AMOUNT:</label>
                        <div></div>
                        <input type="number" name="total_amount" id="totalAmount" readonly style="background: #f0f0f0; font-size: 1.1rem; font-weight: 700;">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button type="button" class="btn-cancel" onclick="toggleCreateForm()">Cancel</button>
                    <button type="submit" class="btn-submit">Save Quotation</button>
                </div>
            </form>
        </div>

        <!-- Quotations List -->
        @if($quotations->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Quote #</th>
                            <th>Origin → Destination</th>
                            <th>Total Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotations as $quotation)
                            <tr>
                                <td><strong>{{ $quotation->quote_number ?? 'N/A' }}</strong></td>
                                <td>{{ substr($quotation->origin_address ?? '', 0, 20) }}... → {{ substr($quotation->destination_address ?? '', 0, 20) }}...</td>
                                <td><strong>₱{{ number_format($quotation->total_amount ?? 0, 2) }}</strong></td>
                                <td>{{ $quotation->created_at->format('M d, Y') }}</td>
                                <td>
                                    <span class="status-badge status-{{ strtolower($quotation->status ?? 'pending') }}">
                                        {{ $quotation->status ?? 'Pending' }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-sm btn-view" onclick="viewQuotation({{ $quotation->id }})">View</button>
                                    <button class="btn-sm btn-delete" onclick="deleteQuotation({{ $quotation->id }})">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $quotations->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="las la-inbox"></i>
                <h3>No Quotations Found</h3>
                <p>There are no freight quotations yet. Create one to get started!</p>
            </div>
        @endif
    </div>

    <script>
        // Apply error styling to fields with validation errors
        document.addEventListener('DOMContentLoaded', function() {
            const errors = @json($errors->getMessages());
            
            // Mark fields with errors by adding is-invalid class
            for (const field in errors) {
                const inputs = document.querySelectorAll(`[name="${field}"]`);
                inputs.forEach(input => {
                    input.classList.add('is-invalid');
                    // Add error message after the field
                    if (errors[field] && errors[field][0]) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'error-text';
                        errorDiv.textContent = errors[field][0];
                        
                        // Check if error message already exists
                        if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('error-text')) {
                            input.parentNode.insertBefore(errorDiv, input.nextSibling);
                        }
                    }
                });
            }
            
            // If form has errors, show the form automatically
            if (Object.keys(errors).length > 0) {
                const form = document.getElementById('quotationForm');
                form.style.display = 'block';
                form.scrollIntoView({ behavior: 'smooth' });
            }
        });

        function toggleCreateForm() {
            const form = document.getElementById('quotationForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
            if (form.style.display === 'block') {
                // Clear all form fields when opening
                document.getElementById('freightQuotationForm').reset();
                generateQuoteNumber();
                form.scrollIntoView({ behavior: 'smooth' });
            }
        }

        function generateQuoteNumber() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            const timestamp = Date.now();
            const random = Math.floor(Math.random() * 9000) + 1000; // 4-digit random
            document.getElementById('quoteNumber').value = `FRT-${year}${month}${day}-${random}`;
            console.log('Generated Quote Number:', document.getElementById('quoteNumber').value);
        }

        function addCargoRow() {
            const tbody = document.getElementById('cargoRows');
            const newRow = tbody.querySelector('tr').cloneNode(true);
            newRow.querySelectorAll('input, select').forEach(el => el.value = '');
            tbody.appendChild(newRow);
        }

        // Validate form before submission
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('freightQuotationForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const quoteNumber = document.getElementById('quoteNumber').value;
                    const quoteDate = document.querySelector('input[name="quote_date"]').value;
                    const estimatedFreight = document.getElementById('freightAmount').value;
                    const totalAmount = document.getElementById('totalAmount').value;

                    if (!quoteNumber) {
                        e.preventDefault();
                        document.getElementById('quoteNumber').classList.add('is-invalid');
                        return false;
                    }
                    if (!quoteDate) {
                        e.preventDefault();
                        document.querySelector('input[name="quote_date"]').classList.add('is-invalid');
                        return false;
                    }
                    if (estimatedFreight === '' || parseFloat(estimatedFreight) < 0) {
                        e.preventDefault();
                        document.getElementById('freightAmount').classList.add('is-invalid');
                        return false;
                    }
                    if (totalAmount === '' || parseFloat(totalAmount) < 0) {
                        e.preventDefault();
                        document.getElementById('totalAmount').classList.add('is-invalid');
                        return false;
                    }
                    console.log('Form submission data:', {quoteNumber, quoteDate, estimatedFreight, totalAmount});
                });
            }

            const freightInput = document.getElementById('freightAmount');
            const freightOptionInput = document.getElementById('freightOption');
            const serviceFeeLabel = document.getElementById('serviceFeeLabel');

            function calculateTotals() {
                const freight = parseFloat(freightInput.value) || 0;
                const isFreightCollect = freightOptionInput?.value === 'freight_collect';

                // Service fee is fixed at 50 pesos only for freight collect
                const serviceFee = isFreightCollect ? 50 : 0;

                // Update label based on freight option
                if (serviceFeeLabel) {
                    if (isFreightCollect) {
                        serviceFeeLabel.textContent = 'Service Fee (₱50 - Freight Collect)';
                    } else if (freightOptionInput?.value === 'freight_billing') {
                        serviceFeeLabel.textContent = 'Service Fee (No charge)';
                    } else {
                        serviceFeeLabel.textContent = 'Service Fee';
                    }
                }

                // Calculate totals
                const total = freight + serviceFee;

                document.getElementById('freightTotal').value = freight.toFixed(2);
                document.getElementById('serviceFeeTotal').value = serviceFee.toFixed(2);
                const forwarderField = document.getElementById('forwarderField');
                if (forwarderField) {
                    forwarderField.style.display = freightOptionInput?.value === 'bill_client' ? 'block' : 'none';
                }

                freightInput?.addEventListener('input', calculateTotals);
                freightOptionInput?.addEventListener('change', calculateTotals);
                calculateTotals();
            });

        function viewQuotation(id) {
            alert('View quotation feature coming soon for ID: ' + id);
        }

        function deleteQuotation(id) {
            if (confirm('Are you sure you want to delete this quotation?')) {
                alert('Delete quotation feature coming soon for ID: ' + id);
            }
        }
    </script>
</x-app-layout>
