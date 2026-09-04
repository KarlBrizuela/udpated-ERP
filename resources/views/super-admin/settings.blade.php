<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .qr-preview-container {
            width: 200px;
            height: 200px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
            overflow: hidden;
            background: #f8f9fa;
        }

        .qr-preview-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .qr-preview-placeholder {
            color: #999;
            text-align: center;
            font-size: 14px;
        }

        .btn-group-custom {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        /* Custom File Upload Styling */
        .custom-file-upload {
            display: inline-block;
            padding: 10px 20px;
            cursor: pointer;
            background: #f8f9fa;
            color: #333;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
        }

        .custom-file-upload:hover {
            background: #ff0000;
            color: white;
            border-color: #ff0000;
        }

        .custom-file-upload i {
            margin-right: 8px;
            font-size: 18px;
        }

        .file-input-hidden {
            display: none;
        }

        .file-name-display {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            color: #666;
            font-style: italic;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header border-0" style="padding-bottom: 0.5rem;">
                    <h2 class="mb-0 text-black" style="font-size: 2.5rem; font-weight: 700;">{{ $title }}</h2>
                </div>
                <div class="card-body" style="padding-top: 0.5rem;">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#transaction">Transaction
                                Settings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#pos-config">POS
                                Configuration</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <!-- POS Configuration Settings -->
                        <div id="pos-config" class="tab-pane fade">
                            <div class="pt-4">
                                <form id="posSettingsForm">
                                    <div class="settings-section mb-5">
                                        <h4 class="text-black mb-3"><i class="las la-cog me-2"></i>Sales &
                                            Calculation Rules</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label font-w600">VAT/Tax Rate
                                                        (%)</label>
                                                    <input type="number" class="form-control" id="taxRate"
                                                        placeholder="e.g. 12" step="0.01" min="0">
                                                    <small class="text-muted">Enter the percentage value
                                                        (e.g., 12 for 12%)</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label font-w600">Order ID
                                                        Prefix</label>
                                                    <input type="text" class="form-control" id="orderPrefix"
                                                        placeholder="e.g. ECOM, ORDER, CLA">
                                                    <small class="text-muted">Prefix used for newly
                                                        generated order numbers</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="btn-group-custom">
                                        <button type="button" class="btn btn-primary btn-lg"
                                            onclick="savePOSSettings()">
                                            <i class="las la-save me-2"></i>Save POS Settings
                                        </button>
                                        <button type="button" class="btn btn-light btn-lg"
                                            id="resetPOSBtn" onclick="resetForm()" disabled>
                                            <i class="las la-undo me-2"></i>Reset Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Transaction Settings -->
                        <div id="transaction" class="tab-pane fade show active">
                            <div class="pt-4">
                                <form id="transactionSettingsForm">
                                    <div class="settings-section mb-5">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h4 class="text-black mb-0"><i class="las la-wallet me-2"></i>GCash Details</h4>
                                            <button type="button" class="btn btn-outline-danger btn-xs" onclick="clearPaymentMethod('gcash')">
                                                <i class="las la-trash-alt me-1"></i>Clear GCash
                                            </button>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label font-w600">GCash Number</label>
                                                    <input type="text" class="form-control" id="gcashNumber"
                                                        placeholder="e.g. 09171234567">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label class="form-label font-w600">GCash QR
                                                        Code</label>
                                                    <label for="gcashQRInput" class="custom-file-upload">
                                                        <i class="las la-cloud-upload-alt"></i> Upload QR
                                                        Image
                                                    </label>
                                                    <input type="file" class="file-input-hidden"
                                                        id="gcashQRInput" accept="image/*"
                                                        onchange="previewImage(this, 'gcashQRPreview')">
                                                    <span id="gcashQRInputName" class="file-name-display">No
                                                        file chosen</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="qr-preview-container">
                                                    <img id="gcashQRPreview" src="" alt="GCash QR Preview"
                                                        style="display: none;">
                                                    <div id="gcashQRPlaceholder"
                                                        class="qr-preview-placeholder">
                                                        <i class="las la-qrcode fs-2 me-1"></i><br>QR Code
                                                        Preview
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <!-- PayMaya Settings -->
                                    <div class="settings-section mb-5 mt-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h4 class="text-black mb-0"><i class="las la-wallet me-2"></i>PayMaya Details</h4>
                                            <button type="button" class="btn btn-outline-danger btn-xs" onclick="clearPaymentMethod('paymaya')">
                                                <i class="las la-trash-alt me-1"></i>Clear PayMaya
                                            </button>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label font-w600">PayMaya Number</label>
                                                    <input type="text" class="form-control" id="paymayaNumber" placeholder="e.g. 09187654321">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label class="form-label font-w600">PayMaya QR Code</label>
                                                    <label for="paymayaQRInput" class="custom-file-upload">
                                                        <i class="las la-cloud-upload-alt"></i> Upload QR Image
                                                    </label>
                                                    <input type="file" class="file-input-hidden" id="paymayaQRInput" accept="image/*" onchange="previewImage(this, 'paymayaQRPreview')">
                                                    <span id="paymayaQRInputName" class="file-name-display">No file chosen</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="qr-preview-container">
                                                    <img id="paymayaQRPreview" src="" alt="PayMaya QR Preview" style="display: none;">
                                                    <div id="paymayaQRPlaceholder" class="qr-preview-placeholder">
                                                        <i class="las la-qrcode fs-2 me-1"></i><br>QR Code Preview
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <!-- Bank Transfer Settings -->
                                    <div class="settings-section mb-5 mt-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h4 class="text-black mb-0"><i class="las la-university me-2"></i>Bank Transfer Details</h4>
                                            <button type="button" class="btn btn-outline-danger btn-xs" onclick="clearPaymentMethod('bank')">
                                                <i class="las la-university me-1"></i>Clear Bank
                                            </button>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label font-w600">Bank Name</label>
                                                    <input type="text" class="form-control" id="bankName" placeholder="e.g. BDO, BPI, Landbank">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label class="form-label font-w600">Account Name</label>
                                                    <input type="text" class="form-control" id="accountName" placeholder="e.g. Claretian Bookstore">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label class="form-label font-w600">Account Number</label>
                                                    <input type="text" class="form-control" id="accountNumber" placeholder="e.g. 001234567890">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label font-w600">Bank Transfer QR Code (Optional)</label>
                                                    <label for="bankQRInput" class="custom-file-upload">
                                                        <i class="las la-cloud-upload-alt"></i> Upload QR Image
                                                    </label>
                                                    <input type="file" class="file-input-hidden" id="bankQRInput" accept="image/*" onchange="previewImage(this, 'bankQRPreview')">
                                                    <span id="bankQRInputName" class="file-name-display">No file chosen</span>
                                                </div>
                                                <div class="qr-preview-container">
                                                    <img id="bankQRPreview" src="" alt="Bank QR Preview" style="display: none;">
                                                    <div id="bankQRPlaceholder" class="qr-preview-placeholder">
                                                        <i class="las la-qrcode fs-2 me-1"></i><br>QR Code Preview
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="btn-group-custom">
                                        <button type="button" class="btn btn-primary btn-lg"
                                            onclick="saveTransactionSettings()">
                                            <i class="las la-save me-2"></i>Save All Settings
                                        </button>
                                        <button type="button" class="btn btn-light btn-lg"
                                            id="resetTransactionBtn" onclick="resetForm()" disabled>
                                            <i class="las la-undo me-2"></i>Reset Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white"><i class="las la-check-circle me-2"></i>Success</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="successMessage" class="mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for Clear -->
    <div class="modal fade" id="clearConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white"><i class="las la-exclamation-triangle me-2"></i>Confirm Clear</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="clearConfirmMessage" class="mb-0">Are you sure you want to clear these details? This will only take effect once you click SAVE ALL SETTINGS.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmClearBtn">Clear Details</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Global variable to store base64 images
        let settingImages = {
            gcashQR: null,
            paymayaQR: null,
            bankQR: null
        };

        // Global variable to store initial loaded state for comparison
        let initialSettings = {};

        function checkFormChanges() {
            // Check POS Config Changes
            const currentPosConfig = {
                taxRate: document.getElementById('taxRate')?.value || '',
                orderPrefix: document.getElementById('orderPrefix')?.value || ''
            };
            const posChanged = JSON.stringify(currentPosConfig) !== JSON.stringify(initialSettings.posConfig || {});
            const resetPOSBtn = document.getElementById('resetPOSBtn');
            if (resetPOSBtn) resetPOSBtn.disabled = !posChanged;

            // Check Transaction Settings Changes
            const currentTransaction = {
                gcashNumber: document.getElementById('gcashNumber')?.value || '',
                gcashQR: settingImages.gcashQR || null,
                paymayaNumber: document.getElementById('paymayaNumber')?.value || '',
                paymayaQR: settingImages.paymayaQR || null,
                bankName: document.getElementById('bankName')?.value || '',
                accountName: document.getElementById('accountName')?.value || '',
                accountNumber: document.getElementById('accountNumber')?.value || '',
                bankQR: settingImages.bankQR || null
            };
            const transChanged = JSON.stringify(currentTransaction) !== JSON.stringify(initialSettings.transaction || {});
            const resetTransBtn = document.getElementById('resetTransactionBtn');
            if (resetTransBtn) resetTransBtn.disabled = !transChanged;
        }

        // Function to preview image and convert to base64
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const placeholder = document.getElementById(previewId.replace('Preview', 'Placeholder'));
            const fileNameDisplay = document.getElementById(input.id + 'Name');
            const file = input.files[0];
            const reader = new FileReader();

            if (file) {
                if (fileNameDisplay) fileNameDisplay.textContent = file.name;

                reader.onloadend = function () {
                    preview.src = reader.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';

                    // Store in our object (based on the input ID)
                    if (input.id === 'gcashQRInput') settingImages.gcashQR = reader.result;
                    if (input.id === 'paymayaQRInput') settingImages.paymayaQR = reader.result;
                    if (input.id === 'bankQRInput') settingImages.bankQR = reader.result;
                }
                reader.readAsDataURL(file);
            } else {
                if (fileNameDisplay) fileNameDisplay.textContent = 'No file chosen';
                preview.src = "";
                preview.style.display = 'none';
                placeholder.style.display = 'block';
                
                if (input.id === 'gcashQRInput') settingImages.gcashQR = null;
                if (input.id === 'paymayaQRInput') settingImages.paymayaQR = null;
                if (input.id === 'bankQRInput') settingImages.bankQR = null;
            }
            checkFormChanges();
        }

        // Function to save transaction settings to database
        function saveTransactionSettings() {
            const settings = {
                gcash: {
                    number: document.getElementById('gcashNumber').value,
                    qr: settingImages.gcashQR
                },
                paymaya: {
                    number: document.getElementById('paymayaNumber').value,
                    qr: settingImages.paymayaQR
                },
                bank: {
                    name: document.getElementById('bankName').value,
                    accountName: document.getElementById('accountName').value,
                    accountNumber: document.getElementById('accountNumber').value,
                    qr: settingImages.bankQR
                }
            };

            fetch('{{ route('settings.payment.save') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(settings)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessModal('Transaction settings saved successfully!');
                    // Update initial state to newly saved values
                    setTimeout(() => loadSettings(), 500);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to save settings');
            });
        }

        // Function to save POS Configuration
        function savePOSSettings() {
            const posConfig = {
                taxRate: parseFloat(document.getElementById('taxRate')?.value) || 12,
                currencySymbol: document.getElementById('currencySymbol')?.value || '₱',
                orderPrefix: document.getElementById('orderPrefix')?.value || 'POS'
            };

            fetch('{{ route('settings.payment.save') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ pos_config: posConfig })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessModal('POS configuration saved successfully!');
                    // Update initial state to newly saved values
                    setTimeout(() => loadSettings(), 500);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to save POS settings');
            });
        }

        // Function to load settings from database
        function loadSettings() {
            fetch('{{ route('marketing.pos.payment-settings') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.settings) {
                        const settings = data.settings;
                        
                        // Load GCash
                        if (settings.gcash) {
                            document.getElementById('gcashNumber').value = settings.gcash.number || '';
                            if (settings.gcash.qr) {
                                displayPreview('gcashQRPreview', 'gcashQRPlaceholder', settings.gcash.qr);
                                settingImages.gcashQR = settings.gcash.qr;
                            }
                        }

                        // Load PayMaya
                        if (settings.paymaya) {
                            document.getElementById('paymayaNumber').value = settings.paymaya.number || '';
                            if (settings.paymaya.qr) {
                                displayPreview('paymayaQRPreview', 'paymayaQRPlaceholder', settings.paymaya.qr);
                                settingImages.paymayaQR = settings.paymaya.qr;
                            }
                        }

                        // Load Bank
                        if (settings.bank) {
                            document.getElementById('bankName').value = settings.bank.name || '';
                            document.getElementById('accountName').value = settings.bank.accountName || '';
                            document.getElementById('accountNumber').value = settings.bank.accountNumber || '';
                            if (settings.bank.qr) {
                                displayPreview('bankQRPreview', 'bankQRPlaceholder', settings.bank.qr);
                                settingImages.bankQR = settings.bank.qr;
                            }
                        }

                        // Load POS Config
                        if (settings.posConfig || settings.pos_config) {
                            const posConfig = settings.posConfig || settings.pos_config;
                            if (document.getElementById('taxRate')) document.getElementById('taxRate').value = posConfig.taxRate || '12';
                            if (document.getElementById('currencySymbol')) document.getElementById('currencySymbol').value = posConfig.currencySymbol || '₱';
                            if (document.getElementById('orderPrefix')) document.getElementById('orderPrefix').value = posConfig.orderPrefix || 'POS';
                        }

                        // Save initial state for comparison
                        initialSettings = {
                            posConfig: {
                                taxRate: document.getElementById('taxRate')?.value || '',
                                orderPrefix: document.getElementById('orderPrefix')?.value || ''
                            },
                            transaction: {
                                gcashNumber: document.getElementById('gcashNumber')?.value || '',
                                gcashQR: settingImages.gcashQR || null,
                                paymayaNumber: document.getElementById('paymayaNumber')?.value || '',
                                paymayaQR: settingImages.paymayaQR || null,
                                bankName: document.getElementById('bankName')?.value || '',
                                accountName: document.getElementById('accountName')?.value || '',
                                accountNumber: document.getElementById('accountNumber')?.value || '',
                                bankQR: settingImages.bankQR || null
                            }
                        };
                        checkFormChanges();
                    }
                })
                .catch(error => {
                    console.error('Error loading settings:', error);
                });
        }

        function displayPreview(previewId, placeholderId, base64) {
            const preview = document.getElementById(previewId);
            const placeholder = document.getElementById(placeholderId);
            preview.src = base64;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        }

        function resetForm() {
            loadSettings();
        }

        function showSuccessModal(message) {
            document.getElementById('successMessage').textContent = message;
            new bootstrap.Modal(document.getElementById('successModal')).show();
        }

        // Function to clear a payment method locally
        let currentMethodToClear = null;
        const clearConfirmModal = new bootstrap.Modal(document.getElementById('clearConfirmModal'));

        function clearPaymentMethod(method) {
            currentMethodToClear = method;
            document.getElementById('clearConfirmMessage').textContent = `Are you sure you want to clear the ${method.toUpperCase()} details? This will only take effect once you click SAVE ALL SETTINGS.`;
            clearConfirmModal.show();
        }

        document.getElementById('confirmClearBtn').addEventListener('click', function() {
            const method = currentMethodToClear;
            if (method === 'gcash') {
                document.getElementById('gcashNumber').value = '';
                document.getElementById('gcashQRPreview').src = '';
                document.getElementById('gcashQRPreview').style.display = 'none';
                document.getElementById('gcashQRPlaceholder').style.display = 'block';
                document.getElementById('gcashQRInputName').textContent = 'No file chosen';
                document.getElementById('gcashQRInput').value = '';
                settingImages.gcashQR = null;
            } else if (method === 'paymaya') {
                document.getElementById('paymayaNumber').value = '';
                document.getElementById('paymayaQRPreview').src = '';
                document.getElementById('paymayaQRPreview').style.display = 'none';
                document.getElementById('paymayaQRPlaceholder').style.display = 'block';
                document.getElementById('paymayaQRInputName').textContent = 'No file chosen';
                document.getElementById('paymayaQRInput').value = '';
                settingImages.paymayaQR = null;
            } else if (method === 'bank') {
                document.getElementById('bankName').value = '';
                document.getElementById('accountName').value = '';
                document.getElementById('accountNumber').value = '';
                document.getElementById('bankQRPreview').src = '';
                document.getElementById('bankQRPreview').style.display = 'none';
                document.getElementById('bankQRPlaceholder').style.display = 'block';
                document.getElementById('bankQRInputName').textContent = 'No file chosen';
                document.getElementById('bankQRInput').value = '';
                settingImages.bankQR = null;
            }
            clearConfirmModal.hide();
            checkFormChanges();
        });

        document.addEventListener('DOMContentLoaded', function () {
            loadSettings();

            // Attach listeners to all inputs to check for changes
            const textInputs = document.querySelectorAll('#posSettingsForm input, #transactionSettingsForm input[type="text"], #transactionSettingsForm input[type="number"]');
            textInputs.forEach(input => {
                input.addEventListener('input', checkFormChanges);
            });
        });
    </script>
    @endpush
</x-app-layout>
