<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .freight-card {
            background: #fff;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            border: 1px solid #edf2f7;
        }

        .section-divider {
            height: 1px;
            background: #edf2f7;
            margin: 2rem 0;
            position: relative;
        }

        .section-title {
            font-size: 0.875rem;
            font-weight: 800;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .section-title i {
            font-size: 1.25rem;
            margin-right: 0.75rem;
            color: #ff0000;
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            font-size: 0.8125rem;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 1.5px solid #e2e8f0;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #ff0000;
            box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.1);
        }

        .input-helper {
            font-size: 0.75rem;
            color: #718096;
            margin-top: 0.25rem;
        }

        .billing-actions {
            position: sticky;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 1.25rem 2rem;
            border-top: 1px solid #e2e8f0;
            margin: 0 -2.5rem -2.5rem -2.5rem;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            z-index: 100;
        }

        .courier-option {
            border: 1.5px solid #e2e8f0;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }

        .courier-option:hover {
            border-color: #cbd5e0;
            background: #f8fafc;
        }

        .courier-option.active {
            border-color: #ff0000;
            background: rgba(255, 0, 0, 0.02);
        }

        .courier-option input[type="radio"] {
            margin-right: 1rem;
        }

        .btn-back {
            color: #4a5568;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin-bottom: 1.5rem;
            transition: color 0.2s;
        }

        .btn-back:hover {
            color: #ff0000;
        }

        .auto-assigned-badge {
            background: #ebf8ff;
            color: #2b6cb0;
            padding: 0.25rem 0.75rem;
            border-radius: 100px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-9 mx-auto">
            <a href="{{ route('admin-finance.credit-collection.billing') }}?tab=freight" class="btn-back">
                <i class="las la-arrow-left me-2"></i> Back to Freight Billing
            </a>

            <div class="freight-card">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <h3 class="fw-bold mb-0">Create New Freight Bill</h3>
                    <div class="text-end">
                        <span class="text-muted small">Status:</span>
                        <span class="badge badge-secondary light ms-2">DRAFT</span>
                    </div>
                </div>

                <form action="{{ route('admin-finance.credit-collection.freight-billing.store') }}" method="POST" id="freightBillForm">
                    @csrf
                    <!-- Bill Information -->
                    <div class="section-title">
                        <i class="las la-file-invoice"></i> Bill Information
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Bill Number</label>
                            <div class="d-flex align-items-center">
                                <input type="text" name="bill_number" class="form-control bg-light" value="F{{ date('Y') }}-{{ rand(100,999) }}" readonly>
                                <span class="auto-assigned-badge ms-2 text-nowrap">Auto-assigned</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bill Date</label>
                            <input type="date" name="bill_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <!-- Customer Information -->
                    <div class="section-title">
                        <i class="las la-user-tie"></i> Customer Information
                    </div>
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <select name="customer_id" class="form-select" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->customer_id }}">{{ $customer->customer_name ?? $customer->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <!-- Delivery Details -->
                    <div class="section-title">
                        <i class="las la-truck"></i> Delivery Details
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Delivery Reference Number <span class="text-danger">*</span></label>
                            <input type="text" name="delivery_reference" class="form-control" placeholder="e.g., 2469 from JRMT">
                            <div class="input-helper">Reference ID from the logistics source</div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Courier Service</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="courier-option">
                                        <input type="radio" name="carrier" value="JRMT Resources">
                                        <span>JRMT Resources</span>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="courier-option">
                                        <input type="radio" name="carrier" value="LBC">
                                        <span>LBC</span>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="courier-option">
                                        <input type="radio" name="carrier" value="J&T Express">
                                        <span>J&T Express</span>
                                    </label>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <div class="d-flex align-items-center">
                                        <label class="courier-option mb-0 me-3" style="min-width: 120px;">
                                            <input type="radio" name="carrier" value="other" id="radioOther">
                                            <span>Other:</span>
                                        </label>
                                        <input type="text" id="otherCourier" class="form-control" placeholder="Specify courier name" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Destination / Delivery To</label>
                            <input type="text" name="destination" class="form-control" placeholder="e.g., Kidapawan c/o Sr. Grace R. Alutaya">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Description</label>
                            <input type="text" name="description" class="form-control" value="Delivery charge">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">SI#/Tracking Number <span class="text-muted fw-normal">(Optional)</span></label>
                            <input type="text" name="tracking_number" class="form-control" placeholder="Tracking ID">
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <!-- Billing Details -->
                    <div class="section-title">
                        <i class="las la-hand-holding-usd"></i> Billing Details
                    </div>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">₱</span>
                                <input type="number" name="amount" step="0.01" class="form-control fw-bold" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Payment Terms</label>
                            <select class="form-select" id="paymentTerms">
                                <option value="7">7 days</option>
                                <option value="15" selected>15 days</option>
                                <option value="30">30 days</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Due Date</label>
                            <input type="text" id="dueDateDisplay" class="form-control bg-light" value="Dec 03, 2025" readonly>
                            <div class="input-helper">Auto-calculated based on terms</div>
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <!-- Internal Notes -->
                    <div class="section-title">
                        <i class="las la-sticky-note"></i> Internal Notes
                    </div>
                    <div class="col-md-12">
                        <label class="form-label text-muted fw-normal">Optional, not shown on the official bill</label>
                        <textarea class="form-control" rows="4" placeholder="Add any internal remarks or special instructions..."></textarea>
                    </div>

                    <div class="mt-5 pt-3"></div> <!-- Spacer for sticky actions -->

                    <div class="billing-actions d-flex justify-content-end align-items-center">
                        <a href="{{ route('admin-finance.credit-collection.billing') }}?tab=freight" class="btn btn-light btn-sm px-4 me-2 shadow-sm">Cancel</a>
                        <button type="submit" name="status" value="draft" class="btn btn-outline-primary btn-sm px-4 me-2 shadow-sm">Save Draft</button>
                        <button type="submit" name="status" value="pending" class="btn btn-primary btn-sm px-4 shadow">
                            Submit for Approval <i class="las la-paper-plane ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Draft Saved Modal -->
    <div class="modal fade" id="draftSavedModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-body p-5 text-center">
                    <div class="mb-4">
                        <div class="display-4 text-success mb-3">
                            <i class="las la-check-circle"></i>
                        </div>
                        <h4 class="fw-bold">Draft Saved</h4>
                        <p class="text-muted mb-0">Freight Bill <span id="savedBillNumber" class="fw-bold text-dark">F2025-141</span> saved as draft.</p>
                        <p class="text-muted">You can continue editing it later.</p>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Continue Editing</button>
                        <a href="{{ route('admin-finance.credit-collection.billing') }}?tab=freight" class="btn btn-light">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const courierRadios = document.querySelectorAll('input[name="courier"]');
            const otherCourierInput = document.getElementById('otherCourier');
            const paymentTermsSelect = document.getElementById('paymentTerms');
            const dueDateDisplay = document.getElementById('dueDateDisplay');

            // Handle Courier Service "Other" option
            courierRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Visual state
                    courierRadios.forEach(r => r.closest('.courier-option').classList.remove('active'));
                    this.closest('.courier-option').classList.add('active');

                    // Disable/Enable other input
                    if (this.value === 'other') {
                        otherCourierInput.disabled = false;
                        otherCourierInput.required = true;
                        otherCourierInput.focus();
                    } else {
                        otherCourierInput.disabled = true;
                        otherCourierInput.required = false;
                        otherCourierInput.value = '';
                    }
                });
            });

            otherCourierInput.addEventListener('input', function() {
                if (document.getElementById('radioOther').checked) {
                    document.getElementById('radioOther').value = this.value;
                }
            });

            // Handle Due Date Calculation
            function updateDueDate() {
                const days = parseInt(paymentTermsSelect.value);
                if (isNaN(days)) return;

                const date = new Date();
                date.setDate(date.getDate() + days);
                
                const options = { month: 'short', day: '2-digit', year: 'numeric' };
                dueDateDisplay.value = date.toLocaleDateString('en-US', options);
            }

            paymentTermsSelect.addEventListener('change', updateDueDate);
            
            // Handle Save Draft Modal
            const btnSaveDraft = document.getElementById('btnSaveDraft');
            const draftSavedModal = new bootstrap.Modal(document.getElementById('draftSavedModal'));
            
            if (btnSaveDraft) {
                btnSaveDraft.addEventListener('click', function() {
                    // In a real app, this would trigger an AJAX save first
                    draftSavedModal.show();
                });
            }

            // Initial calculation
            updateDueDate();
        });
    </script>
    @endpush
</x-app-layout>
