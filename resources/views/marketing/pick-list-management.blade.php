<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .pick-list-form {
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
        .order-info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 1.5rem;
        }
        .order-info-box {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
        }
        .order-info-box h5 {
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
        .form-group input, .form-group select {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
            font-size: 0.9rem;
        }
        .pick-list-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }
        .pick-list-table thead {
            background: #ff0000;
            color: #fff;
        }
        .pick-list-table th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid #ddd;
        }
        .pick-list-table td {
            padding: 0.5rem;
            border: 1px solid #ddd;
        }
        .pick-list-table input[type="number"], .pick-list-table select {
            width: 100%;
            border: none;
            padding: 0.5rem;
            background: transparent;
        }
        .pick-list-table input[type="number"] { text-align: right; }
        .pick-list-table input:focus { outline: 2px solid #ff0000; background: #fff; }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e0e0e0;
        }
        @media print {
            .sidebar-container, .header, .form-actions { display: none !important; }
            .content-body { margin-left: 0 !important; padding: 0 !important; }
            .pick-list-form { box-shadow: none !important; }
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="card pick-list-form">
                <div class="form-header">
                    <h2 class="document-title">PICK LIST MANAGEMENT</h2>
                </div>

                <div class="order-info-section">
                    <div class="order-info-box">
                        <h5>Order Information</h5>
                        <div class="form-group">
                            <label>Sales Order Number:</label>
                            <select id="salesOrderSelect" class="form-control" onchange="loadOrderDetails(this.value)">
                                <option value="">Select Sales Order</option>
                                <option value="1">SO-2026-001 - National Book Store</option>
                                <option value="2">SO-2026-002 - Pandayan Bookshop</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Order Date:</label>
                            <input type="text" id="orderDate" readonly>
                        </div>
                        <div class="form-group">
                            <label>Customer:</label>
                            <input type="text" id="customerName" readonly>
                        </div>
                    </div>
                    <div class="order-info-box">
                        <h5>Pick List Information</h5>
                        <div class="form-group">
                            <label>Pick List Number:</label>
                            <input type="text" id="pickListNumber" placeholder="Auto-generated" readonly>
                        </div>
                        <div class="form-group">
                            <label>Status:</label>
                            <select id="pickListStatus" class="form-control">
                                <option value="draft">Draft</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Prepared By:</label>
                            <input type="text" id="preparedBy" value="Johndoe" readonly>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold mb-3">Pick List Items</h5>
                <div class="table-responsive">
                    <table class="pick-list-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Product</th>
                                <th style="width: 150px;">Location</th>
                                <th style="width: 120px; text-align: right;">Requested Qty</th>
                                <th style="width: 120px; text-align: right;">Picked Qty</th>
                                <th style="width: 120px;">Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody id="pickListTableBody">
                            <tr>
                                <td>1</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <select class="product-select flex-grow-1" onchange="loadProductLocation(this)">
                                            <option value="">Select Product</option>
                                            <option value="1">Holy Bible - NIV</option>
                                            <option value="2">Purpose Driven Life</option>
                                        </select>
                                        <span class="badge bg-primary ms-1">Book</span>
                                    </div>
                                </td>
                                <td>
                                    <select class="location-select">
                                        <option value="">Select Location</option>
                                        <option value="main">Main Warehouse</option>
                                        <option value="shop">Bookstore</option>
                                    </select>
                                </td>
                                <td><input type="number" class="requested-qty-input" value="0" readonly></td>
                                <td><input type="number" class="picked-qty-input" value="0" onchange="updateSummary()"></td>
                                <td>
                                    <select class="status-select">
                                        <option value="pending">Pending</option>
                                        <option value="picked">Picked</option>
                                        <option value="short">Short</option>
                                    </select>
                                </td>
                                <td><input type="text" placeholder="Notes"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="order-info-section mt-4">
                    <div class="order-info-box">
                        <h5>Summary</h5>
                        <div class="form-group">
                            <label>Total Items:</label>
                            <input type="text" id="totalItems" value="1" readonly>
                        </div>
                        <div class="form-group">
                            <label>Items Picked:</label>
                            <input type="text" id="itemsPicked" value="0" readonly>
                        </div>
                    </div>
                    <div class="order-info-box">
                        <h5>Actions</h5>
                        <button type="button" class="btn btn-danger w-100 mb-2" onclick="addPickListItem()">Add Item</button>
                        <button type="button" class="btn btn-secondary w-100" onclick="window.print()">Print Pick List</button>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-light" onclick="history.back()">Back</button>
                    <button type="button" class="btn btn-primary" onclick="alert('Draft Saved')">Save Draft</button>
                    <button type="button" class="btn btn-success" onclick="alert('Completed')">Complete</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pickListNo = 'PL-' + new Date().getFullYear() + '-' + Math.floor(1000 + Math.random() * 9000);
            document.getElementById('pickListNumber').value = pickListNo;
        });

        function loadOrderDetails(id) {
            if (id == '1') {
                document.getElementById('orderDate').value = '2026-01-15';
                document.getElementById('customerName').value = 'National Book Store';
            } else if (id == '2') {
                document.getElementById('orderDate').value = '2026-01-16';
                document.getElementById('customerName').value = 'Pandayan Bookshop';
            }
        }

        function loadProductLocation(select) {
            const row = select.closest('tr');
            if (select.value == '1') row.querySelector('.location-select').value = 'main';
            if (select.value == '2') row.querySelector('.location-select').value = 'shop';
        }

        function addPickListItem() {
            const tbody = document.getElementById('pickListTableBody');
            const newRow = tbody.rows[0].cloneNode(true);
            newRow.cells[0].textContent = tbody.rows.length + 1;
            newRow.querySelectorAll('input').forEach(i => i.value = i.defaultValue);
            newRow.querySelectorAll('select').forEach(s => s.value = '');
            tbody.appendChild(newRow);
            updateSummary();
        }

        function updateSummary() {
            const rows = document.querySelectorAll('#pickListTableBody tr');
            document.getElementById('totalItems').value = rows.length;
            let picked = 0;
            rows.forEach(r => {
                if (r.querySelector('.picked-qty-input').value > 0) picked++;
            });
            document.getElementById('itemsPicked').value = picked;
        }
    </script>
    @endpush
</x-app-layout>
