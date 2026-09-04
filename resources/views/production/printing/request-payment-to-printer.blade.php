<x-app-layout :title="'Request Payment to Printer'" :sidebar="'production'">
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
                        <div class="document-title">REQUEST FOR CHECK PAYMENT</div>
                    </div>

                    <form id="checkPaymentForm" class="form-section">
                        <!-- Request Information -->
                        <div class="customer-section">
                            <div class="customer-details">
                                <h5>Request Information</h5>
                                <div class="form-group">
                                    <label>Date:</label>
                                    <input type="date" name="date" id="formDate">
                                </div>
                                <div class="form-group">
                                    <label>Payee:</label>
                                    <input type="text" name="payee" id="formPayee" placeholder="Enter payee name">
                                </div>
                                <div class="form-group">
                                    <label>Amount:</label>
                                    <input type="number" name="amount" id="formAmount" placeholder="0.00" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="order-details">
                                <h5>Payment Details</h5>
                                <div class="form-group">
                                    <label>Due Date:</label>
                                    <input type="date" name="due_date" id="formDueDate">
                                </div>
                                <div class="form-group">
                                    <label>Attachments:</label>
                                    <ul class="attachment-list">
                                        <li>
                                            <input type="checkbox" name="attachment_quotation" id="attachmentQuotation">
                                            <label for="attachmentQuotation" style="display: inline; font-weight: normal;">Signed Quotation/Conforme:</label>
                                            <input type="text" name="quotation_ref" id="quotationRef" placeholder="Reference">
                                        </li>
                                        <li>
                                            <input type="checkbox" name="attachment_receiving" id="attachmentReceiving">
                                            <label for="attachmentReceiving" style="display: inline; font-weight: normal;">Receiving Report:</label>
                                            <input type="text" name="receiving_ref" id="receivingRef" placeholder="Reference">
                                        </li>
                                        <li>
                                            <input type="checkbox" name="attachment_delivery" id="attachmentDelivery">
                                            <label for="attachmentDelivery" style="display: inline; font-weight: normal;">Delivery Report:</label>
                                            <input type="text" name="delivery_ref" id="deliveryRef" placeholder="Reference">
                                        </li>
                                        <li>
                                            <input type="checkbox" name="attachment_billing" id="attachmentBilling">
                                            <label for="attachmentBilling" style="display: inline; font-weight: normal;">Billing Invoice:</label>
                                            <input type="text" name="billing_ref" id="billingRef" placeholder="Reference">
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn btn-light" onclick="resetGeneratedDocument()">
                                <i class="las la-undo"></i> Reset
                            </button>
                            <button type="button" class="btn btn-primary" onclick="updateGeneratedDocument()">
                                <i class="las la-check"></i> Generate Document
                            </button>
                        </div>
                    </form>
                <!-- Generated Document Section -->
                <div class="generated-document-section mt-4" id="generatedDocumentWrapper" style="display: none;">
                    <div class="card">
                        <div class="card-body">
                            <div class="generated-document" id="generatedDocument">
                                <div class="company-header">
                                    <h2>CLARETIAN COMMUNICATIONS FOUNDATION INC.</h2>
                                    <div>8 Mayumi St., UP Village, Diliman, Quezon City</div>
                                </div>
                                <div class="document-title">REQUEST FOR CHECK PAYMENT</div>
                                
                                <div class="request-header">
                                    <div class="request-header-row">
                                        <span class="request-header-label">DATE:</span>
                                        <span class="request-header-value blank-field" id="lblDate"></span>
                                    </div>
                                    <div class="request-header-row">
                                        <span class="request-header-label">PAYEE:</span>
                                        <span class="request-header-value blank-field" id="lblPayee"></span>
                                    </div>
                                    <div class="request-header-row">
                                        <span class="request-header-label">AMOUNT:</span>
                                        <span class="request-header-value blank-field" id="lblAmount"></span>
                                    </div>
                                    <div class="request-header-row">
                                        <span class="request-header-label">DUE DATE:</span>
                                        <span class="request-header-value blank-field" id="lblDueDate"></span>
                                    </div>
                                </div>

                                <div class="attachment-section">
                                    <strong>ATTACHMENTS:</strong>
                                    <ul>
                                        <li id="lblAttachmentQuotation" style="display: none;">[x] Signed Quotation/Conforme: <span id="lblQuotationRef"></span></li>
                                        <li id="lblAttachmentReceiving" style="display: none;">[x] Receiving Report: <span id="lblReceivingRef"></span></li>
                                        <li id="lblAttachmentDelivery" style="display: none;">[x] Delivery Report: <span id="lblDeliveryRef"></span></li>
                                        <li id="lblAttachmentBilling" style="display: none;">[x] Billing Invoice: <span id="lblBillingRef"></span></li>
                                    </ul>
                                </div>

                                <div class="signature-section">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="signature-box">
                                                <strong>Prepared by:</strong>
                                                <div class="signature-line"></div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="signature-box">
                                                <strong>Checked by:</strong>
                                                <div class="signature-line"></div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="signature-box">
                                                <strong>Approved by:</strong>
                                                <div class="signature-line"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 text-right">
                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('generatedDocumentWrapper').style.display='none'">Close</button>
                                <button type="button" class="btn btn-primary" onclick="printDocument()"><i class="las la-print"></i> Print</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('styles')
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <style>
        /* Marketing Division Form Styles */
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

        .customer-details,
        .order-details {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
        }

        .customer-details h5,
        .order-details h5 {
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

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
            font-size: 0.9rem;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }

        .form-group input[type="checkbox"] {
            width: auto;
            margin-right: 0.5rem;
        }

        .attachment-list {
            list-style: none;
            padding-left: 0;
        }

        .attachment-list li {
            margin-bottom: 0.5rem;
        }

        .attachment-list li input[type="text"] {
            margin-left: 0.5rem;
            width: calc(100% - 200px);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e0e0e0;
        }

        /* Generated Document Section Styles */
        .generated-document-section {
            max-width: 1000px;
            margin: 0;
        }
        .generated-document {
            background: #fff;
            border: 1px solid #ddd;
            padding: 40px;
            margin-top: 30px;
            min-height: 600px;
            font-family: 'Times New Roman', serif;
        }
        .document-header {
            margin-bottom: 30px;
        }
        .company-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .company-header h2 {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 20px;
        }
        .document-title {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin: 20px 0;
        }
        .request-header {
            margin: 20px 0;
            line-height: 2;
        }
        .request-header-row {
            margin-bottom: 5px;
        }
        .request-header-label {
            display: inline-block;
            width: 120px;
            font-weight: bold;
        }
        .request-header-value {
            display: inline-block;
        }
        .blank-field {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 250px;
            padding: 0 5px;
        }
        .attachment-section {
            margin: 20px 0;
        }
        .attachment-section ul {
            list-style: none;
            padding-left: 0;
            margin: 10px 0;
        }
        .attachment-section li {
            margin-bottom: 8px;
        }
        .signature-section {
            margin-top: 40px;
        }
        .signature-box {
            margin-bottom: 30px;
        }
        .signature-box strong {
            display: block;
            margin-bottom: 10px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
        }

        @media print {
            .sidebar,
            .header,
            .form-actions {
                display: none;
            }

            .order-form {
                box-shadow: none;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            return months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
        }

        function updateGeneratedDocument() {
            const date = document.getElementById('formDate').value;
            const payee = document.getElementById('formPayee').value;
            const amount = document.getElementById('formAmount').value;
            const dueDate = document.getElementById('formDueDate').value;

            // Date validation
            if (date && dueDate) {
                const reqDate = new Date(date);
                const dDate = new Date(dueDate);
                reqDate.setHours(0,0,0,0);
                dDate.setHours(0,0,0,0);

                if (reqDate > dDate) {
                    if(window.showAlert) {
                        window.showAlert('Error: Cannot process. The Date is past the Due Date.', 'error');
                    } else {
                        alert('Error: Cannot process. The Date is past the Due Date.');
                    }
                    return;
                }
            }

            document.getElementById('lblDate').textContent = date ? formatDate(date) : '__________________';
            document.getElementById('lblPayee').textContent = payee || '__________________';
            document.getElementById('lblAmount').textContent = amount ? '₱ ' + parseFloat(amount).toLocaleString('en-US', {minimumFractionDigits: 2}) : '__________________';
            document.getElementById('lblDueDate').textContent = dueDate ? formatDate(dueDate) : '__________________';

            // Attachments
            const attachments = [
                { id: 'attachmentQuotation', labelId: 'lblAttachmentQuotation', refId: 'quotationRef', lblRefId: 'lblQuotationRef' },
                { id: 'attachmentReceiving', labelId: 'lblAttachmentReceiving', refId: 'receivingRef', lblRefId: 'lblReceivingRef' },
                { id: 'attachmentDelivery', labelId: 'lblAttachmentDelivery', refId: 'deliveryRef', lblRefId: 'lblDeliveryRef' },
                { id: 'attachmentBilling', labelId: 'lblAttachmentBilling', refId: 'billingRef', lblRefId: 'lblBillingRef' }
            ];

            attachments.forEach(att => {
                const isChecked = document.getElementById(att.id).checked;
                const label = document.getElementById(att.labelId);
                const ref = document.getElementById(att.refId).value;
                const lblRef = document.getElementById(att.lblRefId);

                if (isChecked) {
                    label.style.display = 'list-item';
                    lblRef.textContent = ref || 'N/A';
                } else {
                    label.style.display = 'none';
                }
            });

            document.getElementById('generatedDocumentWrapper').style.display = 'block';
            document.getElementById('generatedDocumentWrapper').scrollIntoView({ behavior: 'smooth' });
        }

        function resetGeneratedDocument() {
            document.getElementById('checkPaymentForm').reset();
            document.getElementById('generatedDocumentWrapper').style.display = 'none';
        }

        function printDocument() {
            const printContent = document.getElementById('generatedDocument').innerHTML;
            const originalContent = document.body.innerHTML;
            
            document.body.innerHTML = '<div class="print-wrapper" style="padding: 2.5rem; font-family: \'Times New Roman\', serif;">' + printContent + '</div>';
            window.print();
            
            // Reload to restore state and bindings
            location.reload();
        }
    </script>
    @endpush

</x-app-layout>
