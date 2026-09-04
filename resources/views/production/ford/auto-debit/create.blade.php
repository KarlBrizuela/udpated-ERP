<x-app-layout :title="'Create Auto Debit'" :sidebar="'production'">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <!-- Form Section -->
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
                        <div class="document-title">AUTO DEBIT LETTER GENERATION</div>
                    </div>

                    <form id="autoDebitForm" class="form-section">
                        <!-- Debit Information -->
                        <div class="customer-section">
                            <div class="customer-details">
                                <h5>Debit Information</h5>
                                <div class="form-group">
                                    <label>Date:</label>
                                    <input type="date" name="date" id="formDate" required>
                                </div>
                                <div class="form-group">
                                    <label>Amount (PHP):</label>
                                    <input type="number" name="amount" id="formAmount" placeholder="Enter amount" min="0" step="0.01" required>
                                </div>
                                <div class="form-group">
                                    <label>Debit Date:</label>
                                    <input type="date" name="debit_date" id="formDebitDate" required>
                                </div>
                            </div>
                            <div class="order-details">
                                <h5>Transaction Details</h5>
                                <div class="form-group">
                                    <label>Item/Reason (Customs duties and taxes for):</label>
                                    <input type="text" name="item_reason" id="formItemReason" placeholder="Enter item/reason" required>
                                </div>
                                <div class="form-group">
                                    <label>Source/Origin (from):</label>
                                    <input type="text" name="source_origin" id="formSourceOrigin" placeholder="Enter source/origin" required>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <a href="{{ route('production.ford.auto-debit') }}" class="btn btn-light">
                                <i class="las la-times"></i> Cancel
                            </a>
                            <button type="button" class="btn btn-primary" onclick="updateGeneratedLetter()">
                                <i class="las la-check"></i> Generate Letter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .order-form {
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
        .form-header .company-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .form-header .company-logo {
            width: 60px;
            height: 60px;
            background: #ff0000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2rem;
            font-weight: bold;
            flex-shrink: 0;
        }
        .form-header .company-details {
            flex: 1;
        }
        .form-header .company-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
        }
        .form-header .company-address {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.1rem;
        }
        .form-header .company-contact {
            font-size: 0.9rem;
            color: #666;
        }
        .form-header .document-title {
            text-align: center;
            font-size: 1.75rem;
            font-weight: 700;
            color: #333;
            margin-top: 1rem;
            letter-spacing: 1px;
        }
        .customer-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 1.5rem;
        }
        .customer-details, .order-details {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
        }
        .customer-details h5, .order-details h5 {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }
        .form-group {
            margin-bottom: 0.75rem;
        }
        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
            display: block;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
            font-size: 0.9rem;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e0e0e0;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function updateGeneratedLetter() {
            const date = document.getElementById('formDate').value;
            const amount = document.getElementById('formAmount').value;
            const debitDate = document.getElementById('formDebitDate').value;
            const itemReason = document.getElementById('formItemReason').value;
            const sourceOrigin = document.getElementById('formSourceOrigin').value;

            if (!date || !amount || !debitDate || !itemReason || !sourceOrigin) {
                alert('Please fill in all required fields.');
                return;
            }

            const genBtn = document.querySelector('button[onclick="updateGeneratedLetter()"]');
            if (genBtn) {
                genBtn.disabled = true;
                genBtn.textContent = 'Generating...';
            }

            const form = document.getElementById('autoDebitForm');
            const formData = new FormData(form);
            formData.append('_token', '{{ csrf_token() }}');

            fetch("{{ route('production.ford.auto-debit.store') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = `/production/ford/auto-debit/${data.id}`;
                } else {
                    alert(data.message || 'An error occurred.');
                    if (genBtn) {
                        genBtn.disabled = false;
                        genBtn.innerHTML = '<i class="las la-check"></i> Generate Letter';
                    }
                }
            })
            .catch(err => {
                console.error(err);
                alert('Connection error occurred.');
                if (genBtn) {
                    genBtn.disabled = false;
                    genBtn.innerHTML = '<i class="las la-check"></i> Generate Letter';
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
