<x-app-layout :title="'Auto Debit'" :sidebar="'production'">
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
                        <div class="document-title">AUTO DEBIT</div>
                    </div>

                    <form id="autoDebitForm" class="form-section">
                        <!-- Debit Information -->
                        <div class="customer-section">
                            <div class="customer-details">
                                <h5>Debit Information</h5>
                                <div class="form-group">
                                    <label>Date:</label>
                                    <input type="date" name="date" id="formDate">
                                </div>
                                <div class="form-group">
                                    <label>Amount (PHP):</label>
                                    <input type="number" name="amount" id="formAmount" placeholder="Enter amount" min="0" step="0.01">
                                </div>
                                <div class="form-group">
                                    <label>Debit Date:</label>
                                    <input type="date" name="debit_date" id="formDebitDate">
                                </div>
                            </div>
                            <div class="order-details">
                                <h5>Transaction Details</h5>
                                <div class="form-group">
                                    <label>Item/Reason (Customs duties and taxes for):</label>
                                    <input type="text" name="item_reason" id="formItemReason" placeholder="Enter item/reason">
                                </div>
                                <div class="form-group">
                                    <label>Source/Origin (from):</label>
                                    <input type="text" name="source_origin" id="formSourceOrigin" placeholder="Enter source/origin">
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn btn-light" onclick="resetGeneratedLetter()">
                                <i class="las la-undo"></i> Reset
                            </button>
                            <button type="button" class="btn btn-primary" onclick="updateGeneratedLetter()">
                                <i class="las la-check"></i> Generate Letter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Generated Letter Section -->
                <div class="generated-letter-section" id="generatedLetterSection" style="display: none;">
                    <div class="d-flex gap-2 mb-3 report-actions">
                        <button type="button" class="btn btn-secondary" onclick="backToForm()">
                            <i class="las la-arrow-left"></i> Back
                        </button>
                        <button type="button" class="btn btn-success" onclick="printReport()">
                            <i class="las la-print"></i> Print Letter
                        </button>
                    </div>
                    <div class="generated-letter" id="generatedLetter">
                        <div class="memo-header">
                            <div class="memo-header-row">
                                <span class="memo-header-label">DATE </span>: <span class="memo-header-value" id="reportDate">_____________</span>
                            </div>
                            <div class="memo-header-row">
                                <span class="memo-header-label">TO </span>: <span class="memo-header-value">BPI COMMONWEALTH AVE., Q.C.</span>
                            </div>
                            <div class="memo-header-row">
                                <span class="memo-header-label">FROM </span>: <span class="memo-header-value">SR. ANNA MARIA R. VIOJAN, RMI / FR. DENNIS G. TAMAYO, CMF</span>
                            </div>
                        </div>

                        <div class="memo-body">
                            <div class="memo-body-text">
                                Please debit our Current Account Number <strong>3201-0268-07</strong> (Corporate Account - Claretian Communications Foundation, Inc.)
                                the amount of <strong id="reportAmountWords">______________________</strong> PESOS (P <strong id="reportAmountPHP">_________</strong>) 
                                value on <span id="reportDebitDate">_____________</span>; 
                                representing <span id="reportItemReason">___________________________</span> for <span id="reportSourceOrigin">___________________________</span>.
                            </div>
                            <div class="memo-body-text">
                                Thank you.
                            </div>
                        </div>

                        <div class="memo-footer">
                            <div class="memo-signature" style="display: flex; gap: 50px;">
                                <div style="flex: 1; text-align: center;">
                                    <div style="border-bottom: 1px solid #000; margin-bottom: 5px; padding-top: 40px;"></div>
                                    <strong>SR. ANNA MARIA R. VIOJAN, RMI</strong><br>
                                    Director Treasurer
                                </div>
                                <div style="flex: 1; text-align: center;">
                                    <div style="border-bottom: 1px solid #000; margin-bottom: 5px; padding-top: 40px;"></div>
                                    <strong>FR. DENNIS G. TAMAYO, CMF</strong><br>
                                    Executive Director
                                </div>
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

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e0e0e0;
        }

        /* Generated Letter Section Styles (unchanged) */
        .generated-letter-section {
            max-width: 800px;
            margin: 0;
        }
        .generated-letter {
            background: #fff;
            border: 1px solid #ddd;
            padding: 40px;
            margin-top: 30px;
            min-height: 600px;
            font-family: 'Times New Roman', serif;
        }
        .memo-header {
            margin-bottom: 30px;
        }
        .memo-header-row {
            margin-bottom: 10px;
        }
        .memo-header-label {
            display: inline-block;
            width: 80px;
            font-weight: bold;
            color: #000;
        }
        .memo-header-value {
            display: inline-block;
            color: #000;
        }
        .memo-body {
            margin: 30px 0;
            line-height: 1.8;
        }
        .memo-body-text {
            margin-bottom: 15px;
        }
        .memo-footer {
            margin-top: 40px;
        }
        .memo-signature {
            margin-top: 30px;
            margin-bottom: 20px;
        }
        .memo-signature div {
            margin-bottom: 5px;
        }
        .blank-field {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 200px;
            padding: 0 5px;
            margin: 0 5px;
        }

        @media print {
            .sidebar,
            .header,
            .form-actions,
            .report-actions {
                display: none !important;
            }

            .order-form {
                display: none !important;
                box-shadow: none;
            }
            
            body {
                background-color: #fff;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function convertToWords(num) {
            if (num === 0 || isNaN(num)) return "ZERO";
            const a = ['','ONE ','TWO ','THREE ','FOUR ', 'FIVE ','SIX ','SEVEN ','EIGHT ','NINE ','TEN ','ELEVEN ','TWELVE ','THIRTEEN ','FOURTEEN ','FIFTEEN ','SIXTEEN ','SEVENTEEN ','EIGHTEEN ','NINETEEN '];
            const b = ['', '', 'TWENTY ','THIRTY ','FORTY ','FIFTY ', 'SIXTY ','SEVENTY ','EIGHTY ','NINETY '];

            const formatHundreds = (n) => {
                let str = '';
                if (n > 99) {
                    str += a[Math.floor(n / 100)] + 'HUNDRED ';
                    n %= 100;
                }
                if (n > 19) {
                    str += b[Math.floor(n / 10)];
                    n %= 10;
                }
                if (n > 0) {
                    str += a[n];
                }
                return str;
            };

            const numStr = parseFloat(num).toFixed(2);
            const parts = numStr.split('.');
            let whole = parseInt(parts[0], 10);
            const cents = parseInt(parts[1], 10);

            if (whole === 0) return `ZERO AND ${cents}/100`;

            let str = '';
            if (whole >= 1000000) {
                str += formatHundreds(Math.floor(whole / 1000000)) + 'MILLION ';
                whole %= 1000000;
            }
            if (whole >= 1000) {
                str += formatHundreds(Math.floor(whole / 1000)) + 'THOUSAND ';
                whole %= 1000;
            }
            if (whole > 0) {
                str += formatHundreds(whole);
            }

            let result = str.trim();
            if (cents > 0) {
                result += ` AND ${cents}/100`;
            }
            return result;
        }

        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            return months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
        }

        function updateGeneratedLetter() {
            const date = document.getElementById('formDate').value;
            const amount = document.getElementById('formAmount').value;
            const debitDate = document.getElementById('formDebitDate').value;
            const itemReason = document.getElementById('formItemReason').value;
            const sourceOrigin = document.getElementById('formSourceOrigin').value;

            if (!date || !amount || !debitDate || !itemReason || !sourceOrigin) {
                if (window.showAlert) {
                    window.showAlert('Please fill in all required fields.', 'warning');
                } else {
                    alert('Please fill in all required fields.');
                }
                return;
            }

            document.querySelector('.order-form').style.display = 'none';
            document.getElementById('generatedLetterSection').style.display = 'block';
            
            const parsedAmount = parseFloat(amount) || 0;

            document.getElementById('reportDate').innerHTML = formatDate(date);
            document.getElementById('reportAmountWords').innerHTML = convertToWords(parsedAmount);
            document.getElementById('reportAmountPHP').innerHTML = parsedAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('reportDebitDate').innerHTML = formatDate(debitDate);
            document.getElementById('reportItemReason').innerHTML = itemReason;
            document.getElementById('reportSourceOrigin').innerHTML = sourceOrigin;
        }

        function resetGeneratedLetter() {
            document.getElementById('autoDebitForm').reset();
            document.getElementById('generatedLetterSection').style.display = 'none';
        }

        function backToForm() {
            document.querySelector('.order-form').style.display = 'block';
            document.getElementById('generatedLetterSection').style.display = 'none';
        }

        function printReport() {
            window.print();
        }
    </script>
    @endpush
</x-app-layout>
