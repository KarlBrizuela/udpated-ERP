<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .receipt-form {
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
        .form-header .company-details { flex: 1; }
        .form-header .company-name { font-size: 1.25rem; font-weight: 700; color: #333; margin-bottom: 0.25rem; text-transform: uppercase; }
        .document-title { text-align: center; font-size: 1.75rem; font-weight: 700; color: #333; margin-top: 1rem; letter-spacing: 1px; }
        
        .form-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 6px;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .form-info-item { display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 200px; }
        .form-info-item label { font-weight: 600; color: #333; margin: 0; min-width: 80px; }
        .form-info-item .form-control { border: 1px solid #ddd; border-radius: 4px; padding: 0.5rem; flex: 1; }
        
        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }
        .receipt-table thead { background: #ff0000; color: #fff; }
        .receipt-table th { padding: 0.75rem; font-weight: 600; font-size: 0.9rem; border: 1px solid #ddd; }
        .receipt-table td { padding: 0.5rem; border: 1px solid #ddd; }
        .receipt-table input { width: 100%; border: none; padding: 0.5rem; background: transparent; }
        .receipt-table input:focus { outline: 2px solid #ff0000; background: #fff; }
        
        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e0e0e0;
        }
        .signature-box { display: flex; flex-direction: column; }
        .signature-box label { font-weight: 600; margin-bottom: 0.5rem; }
        .signature-box input { border: 1px solid #ddd; border-radius: 4px; padding: 0.5rem; margin-bottom: 2rem; }
        .signature-line { border-top: 1px solid #333; width: 200px; margin: 0 auto; padding-top: 0.5rem; text-align: center; font-size: 0.75rem; }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e0e0e0;
        }

        .nav-tabs .nav-link {
            color: #333;
            border: none;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 1rem;
        }
        .nav-tabs .nav-link:hover {
            border-bottom-color: #ff0000;
        }
        .nav-tabs .nav-link.active {
            background: transparent;
            color: #ff0000;
            border-bottom-color: #ff0000;
        }

        .table-status-badge {
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-in-transit { background: #cce5ff; color: #004085; }

        .dr-view-table {
            width: 100%;
            margin-top: 1rem;
        }
        .dr-view-table thead {
            background: #f8f9fa;
        }
        .dr-view-table th {
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #ddd;
            padding: 1rem;
        }
        .dr-view-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #eee;
        }

        @page {
            size: letter portrait; /* Short bond paper (8.5in x 11in) */
            margin: 0.35in 0.4in;
        }

        @media print {
            * { -webkit-print-color-adjust: exact !important; color-adjust: exact !important; print-color-adjust: exact !important; }
            body, html { background: #fff !important; color: #000 !important; font-size: 11px !important; line-height: 1.2 !important; margin: 0 !important; padding: 0 !important; }
            .sidebar, .header, .nav-header, .form-actions, .btn, .nav-tabs, .btn-danger, .fa-times { display: none !important; }
            .receipt-form { box-shadow: none !important; padding: 0 !important; margin: 0 !important; border: none !important; }
            .form-header { margin-bottom: 0.75rem !important; padding-bottom: 0.5rem !important; border-bottom: 2px solid #000 !important; text-align: center !important; }
            .form-header .company-logo { display: none !important; }
            .form-header .company-name { font-size: 1rem !important; font-weight: 700 !important; margin-bottom: 2px !important; }
            .document-title { font-size: 1.25rem !important; font-weight: 700 !important; margin-top: 0.35rem !important; }
            .receipt-table { width: 100% !important; border-collapse: collapse !important; margin-bottom: 0.75rem !important; font-size: 11px !important; }
            .receipt-table th, .receipt-table td { padding: 4px 6px !important; border: 1px solid #000 !important; font-size: 11px !important; }
            .receipt-table th { background: #e9ecef !important; color: #000 !important; font-weight: 700 !important; }
            .signature-section { margin-top: 1.25rem !important; page-break-inside: avoid !important; }
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs border-bottom px-4 pt-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="create-tab" data-bs-toggle="tab" data-bs-target="#create-pane" type="button" role="tab" aria-controls="create-pane" aria-selected="true">
                            <i class="fas fa-plus me-2"></i>Create New DR
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="view-tab" data-bs-toggle="tab" data-bs-target="#view-pane" type="button" role="tab" aria-controls="view-pane" aria-selected="false">
                            <i class="fas fa-list me-2"></i>View All DRs ({{ count($deliveryReceipts) }})
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content p-4">
                    <!-- Create Tab -->
                    <div class="tab-pane fade show active" id="create-pane" role="tabpanel" aria-labelledby="create-tab">
                        <div class="receipt-form">
                <div class="form-header">
                    <div class="company-info">
                        <img src="{{ asset('images/claeritian_logo.png') }}" alt="Claretian Logo" class="company-logo-img me-2" style="height: 50px; width: auto; object-fit: contain;">
                        <div class="company-details">
                            <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                            <div class="company-address">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                        </div>
                    </div>
                    <div class="document-title">DELIVERY RECEIPT</div>
                    <div class="text-center text-muted small fw-bold mb-1">NON-VAT REGISTERED</div>
                    <div class="text-center extra-small text-muted italic mb-2">"This document is not valid for claim of input taxes."</div>
                </div>

                <div class="form-info-row">
                    <div class="form-info-item">
                        <label>DR No.:</label>
                        <input type="text" class="form-control" id="receiptNumber" placeholder="Enter DR number">
                    </div>
                    <div class="form-info-item">
                        <label>Date:</label>
                        <input type="date" class="form-control" id="receiptDate">
                    </div>
                    <div class="form-info-item">
                        <label>Sales Order:</label>
                        <select class="form-control" id="salesOrder" onchange="loadSalesOrderDetails(this.value)">
                            <option value="">Select Sales Order</option>
                            @foreach($salesOrders as $order)
                            <option value="{{ $order->id }}" data-so-number="{{ $order->so_number }}" data-customer-id="{{ $order->customer_id }}" data-customer-name="{{ $order->customer->customer_name ?? 'N/A' }}" data-address="{{ $order->shipping_address ?? $order->customer->customer_address ?? '' }}" data-items="{{ json_encode($order->items->map(function($item) { $name = $item->bookIndex?->display_name ?? ($item->bookIndex?->title ?? ($item->book?->name ?? ($item->bundle?->name ?? ($item->product?->name ?? ($item->item_name ?? 'N/A'))))); $price = (float)($item->unit_price ?? ($item->price ?? 0)); return ['quantity' => (float)$item->quantity, 'description' => $name, 'unit_price' => $price, 'amount' => (float)$item->quantity * $price]; })) }}">
                                #{{ $order->so_number }} - {{ $order->customer->customer_name ?? 'N/A' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Delivered To:</label>
                    <div class="d-flex align-items-center gap-2">
                        <select class="form-control" id="recipient">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->customer_id }}" data-address="{{ $customer->shipping_address ?? $customer->customer_address ?? '' }}" data-is-bad="{{ $customer->is_bad_client ? '1' : '0' }}">{{ $customer->customer_name }}</option>
                            @endforeach
                        </select>
                        <span id="recipientStatus" class="badge bg-secondary" style="min-width:90px; text-align:center;">&nbsp;</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Delivery Address:</label>
                    <textarea class="form-control" rows="2" id="deliveryAddress" placeholder="Enter delivery address"></textarea>
                </div>

                <button type="button" class="btn btn-danger btn-sm mb-3" onclick="addRow()">+ Add Row</button>
                
                <div class="table-responsive">
                    <table class="receipt-table">
                        <thead>
                            <tr>
                                <th style="width: 100px;">QUANTITY</th>
                                <th>DESCRIPTION</th>
                                <th style="width: 130px; text-align: right;">UNIT PRICE</th>
                                <th style="width: 110px; text-align: center;">DISCOUNT</th>
                                <th style="width: 130px; text-align: right;">AMOUNT</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="receiptTableBody">
                            <tr>
                                <td><input type="number" class="qty-input" value="0"></td>
                                <td><input type="text" placeholder="Product description"></td>
                                <td><input type="number" class="price-input" value="0.00" step="0.01"></td>
                                <td><input type="text" class="discount-input text-center" placeholder="0%" value="-"></td>
                                <td><input type="number" class="amount-input" value="0.00" readonly></td>
                                <td class="text-center"><button type="button" class="btn btn-xs sharp btn-danger" onclick="removeRow(this)"><i class="fa fa-times"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="signature-section">
                    <div class="signature-box">
                        <label>Prepared by:</label>
                        <input type="text" value="{{ auth()->user()->name ?? 'N/A' }}" readonly>
                        <div class="signature-line">SIGNATURE</div>
                    </div>
                    <div class="signature-box">
                        <label>Received by:</label>
                        <input type="text">
                        <div class="signature-line">SIGNATURE</div>
                    </div>
                </div>

                            <div class="form-actions">
                                <button type="button" class="btn btn-light" onclick="window.print()">Print</button>
                                <button type="button" class="btn btn-primary" onclick="alert('Saved')">Save</button>
                                <button type="button" class="btn btn-success" onclick="alert('Submitted')">Submit</button>
                            </div>
                        </div>
                    </div>

                    <!-- View Tab -->
                    <div class="tab-pane fade" id="view-pane" role="tabpanel" aria-labelledby="view-tab">
                        <div class="table-responsive">
                            <table class="table table-hover dr-view-table">
                                <thead>
                                    <tr>
                                        <th>DR Number</th>
                                        <th>Sales Order</th>
                                        <th>Sales Invoice</th>
                                        <th>Customer</th>
                                        <th>Delivery Date</th>
                                        <th>Remaining Date</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Prepared By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($deliveryReceipts as $dr)
                                    <tr>
                                        <td><strong>#{{ $dr->dr_number }}</strong></td>
                                        <td>
                                            @if($dr->salesOrder)
                                                <a href="{{ route('marketing.sales-orders.detail', $dr->salesOrder->id) }}" class="text-primary">
                                                    {{ $dr->so_number }}
                                                </a>
                                            @else
                                                {{ $dr->so_number ?? 'N/A' }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($dr->salesInvoice)
                                                <a href="{{ route('admin-finance.accounting.sales-invoice.print', $dr->si_id) }}" class="text-primary" target="_blank">
                                                    {{ $dr->si_number }}
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $dr->customer_name ?? ($dr->customer->customer_name ?? 'N/A') }}
                                            @if($dr->customer)
                                                @if($dr->customer->is_bad_client)
                                                    <span class="badge bg-danger ms-2">Bad Client</span>
                                                @else
                                                    <span class="badge bg-success ms-2">Good Client</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($dr->delivery_date)
                                                {{ \Carbon\Carbon::parse($dr->delivery_date)->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($dr->salesOrder && $dr->delivery_date)
                                                @php
                                                    $terms = $dr->salesOrder->terms;
                                                    $daysFromTerms = 0;
                                                    if ($terms) {
                                                        $termsMap = [
                                                            'cash' => 0,
                                                            'cod' => 0,
                                                            '7_days' => 7,
                                                            '15_days' => 15,
                                                            '30_days' => 30,
                                                            '60_days' => 60,
                                                            '90_days' => 90,
                                                        ];
                                                        $daysFromTerms = $termsMap[$terms] ?? 0;
                                                    }
                                                    $remainingDate = \Carbon\Carbon::parse($dr->delivery_date)->addDays($daysFromTerms);
                                                    $today = \Carbon\Carbon::today();
                                                    $daysRemaining = $remainingDate->diffInDays($today, false);
                                                @endphp
                                                <span class="@if($daysRemaining < 0) text-danger fw-bold @elseif($daysRemaining < 7) text-warning @else text-success @endif">
                                                    {{ $remainingDate->format('M d, Y') }}
                                                    <br>
                                                    @if($daysRemaining < 0)
                                                        <small class="text-danger">{{ abs($daysRemaining) }} days overdue</small>
                                                    @else
                                                        <small>{{ $daysRemaining }} days remaining</small>
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>₱{{ number_format($dr->total_amount, 2) }}</td>
                                        <td>
                                            @php
                                                $badgeClass = match($dr->status) {
                                                    'pending' => 'status-pending',
                                                    'completed' => 'status-completed',
                                                    'in-transit' => 'status-in-transit',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="table-status-badge {{ $badgeClass }}">
                                                {{ ucfirst($dr->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $dr->preparedByUser->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp" title="View DR" onclick="viewDR({{ $dr->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp" title="Print DR">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">No delivery receipts yet. Create one from the "Create New DR" tab.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('receiptDate').value = new Date().toISOString().split('T')[0];
            document.getElementById('receiptTableBody').addEventListener('input', function(e) {
                if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
                    const row = e.target.closest('tr');
                    const qty = row.querySelector('.qty-input').value;
                    const price = row.querySelector('.price-input').value;
                    row.querySelector('.amount-input').value = (qty * price).toFixed(2);
                }
            });

            // Handle recipient (customer) dropdown change and show Good/Bad badge
            document.getElementById('recipient').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const address = selectedOption ? (selectedOption.getAttribute('data-address') || '') : '';
                document.getElementById('deliveryAddress').value = address;

                const statusEl = document.getElementById('recipientStatus');
                if (!selectedOption || !selectedOption.value) {
                    statusEl.className = 'badge bg-secondary';
                    statusEl.textContent = '';
                    return;
                }

                const isBad = selectedOption.getAttribute('data-is-bad') === '1';
                if (isBad) {
                    statusEl.className = 'badge bg-danger';
                    statusEl.textContent = 'Bad Client';
                } else {
                    statusEl.className = 'badge bg-success';
                    statusEl.textContent = 'Good Client';
                }
            });
        });

        function loadSalesOrderDetails(soId) {
            if (!soId) {
                // Clear everything if no SO selected
                document.getElementById('recipient').value = '';
                document.getElementById('deliveryAddress').value = '';
                clearReceiptTable();
                return;
            }

            const selectElement = document.getElementById('salesOrder');
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            
            // Get data from the selected option
            const soNumber = selectedOption.getAttribute('data-so-number');
            const customerId = selectedOption.getAttribute('data-customer-id');
            const customerName = selectedOption.getAttribute('data-customer-name');
            const address = selectedOption.getAttribute('data-address');
            const itemsJson = selectedOption.getAttribute('data-items');
            
            // Set customer
            document.getElementById('recipient').value = customerId;
            document.getElementById('deliveryAddress').value = address || '';
            // Update recipient status badge
            document.getElementById('recipient').dispatchEvent(new Event('change'));
            
            // Clear and populate table with SO items
            clearReceiptTable();
            if (itemsJson) {
                try {
                    const items = JSON.parse(itemsJson);
                    items.forEach((item, index) => {
                        if (index === 0) {
                            // Use existing row for first item
                            const firstRow = document.getElementById('receiptTableBody').rows[0];
                            firstRow.querySelector('.qty-input').value = item.quantity;
                            firstRow.querySelector('input[placeholder="Product description"]').value = item.description;
                            firstRow.querySelector('.price-input').value = parseFloat(item.unit_price).toFixed(2);
                            firstRow.querySelector('.amount-input').value = parseFloat(item.amount).toFixed(2);
                        } else {
                            // Add new rows for additional items
                            addRowWithData(item.quantity, item.description, item.unit_price, item.amount);
                        }
                    });
                } catch (e) {
                    console.error('Error parsing items:', e);
                }
            }
        }

        function addRowWithData(qty, description, price, amount) {
            const tbody = document.getElementById('receiptTableBody');
            const newRow = tbody.rows[0].cloneNode(true);
            newRow.querySelector('.qty-input').value = qty;
            newRow.querySelector('input[placeholder="Product description"]').value = description;
            newRow.querySelector('.price-input').value = parseFloat(price).toFixed(2);
            newRow.querySelector('.amount-input').value = parseFloat(amount).toFixed(2);
            tbody.appendChild(newRow);
        }

        function clearReceiptTable() {
            const tbody = document.getElementById('receiptTableBody');
            while (tbody.rows.length > 1) {
                tbody.deleteRow(1);
            }
            // Clear first row
            const firstRow = tbody.rows[0];
            firstRow.querySelector('.qty-input').value = '0';
            firstRow.querySelector('input[placeholder="Product description"]').value = '';
            firstRow.querySelector('.price-input').value = '0.00';
            firstRow.querySelector('.amount-input').value = '0.00';
        }

        function addRow() {
            const tbody = document.getElementById('receiptTableBody');
            const newRow = tbody.rows[0].cloneNode(true);
            newRow.querySelectorAll('input').forEach(i => i.value = i.defaultValue);
            tbody.appendChild(newRow);
        }

        function removeRow(btn) {
            if (document.getElementById('receiptTableBody').rows.length > 1) btn.closest('tr').remove();
        }

        function viewDR(drId) {
            alert('DR ID: ' + drId + ' - View functionality to be implemented');
        }
    </script>
    @endpush
</x-app-layout>
