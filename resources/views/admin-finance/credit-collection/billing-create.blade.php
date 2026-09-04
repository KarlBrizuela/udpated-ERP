<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .preparation-card {
            background: #fff;
            border-radius: 8px;
            padding: 2.5rem;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            border: 1px solid #e9ecef;
        }

        .statement-header-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #f8f9fa;
        }

        .header-item label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .header-item .value {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
        }

        .particulars-table th {
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem;
            background: #f8f9fa;
        }

        .particulars-table td {
            vertical-align: middle;
            padding: 0.75rem 1rem;
        }

        .footer-section {
            margin-top: 3rem;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 4rem;
        }

        .payment-info {
            background: #fdfdfd;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px dashed #dee2e6;
        }

        .signatory-item {
            margin-top: 2rem;
            border-top: 1px solid #333;
            width: 250px;
            padding-top: 0.5rem;
            text-align: center;
        }

        .signatory-item .name {
            font-weight: 700;
            color: #333;
        }

        .signatory-item .role {
            font-size: 0.75rem;
            color: #666;
        }

        .invoice-actions {
            position: sticky;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            border-top: 1px solid #eee;
            margin: 0 -1.5rem;
            z-index: 1000;
        }
    </style>
    @endpush

    <form action="{{ route('admin-finance.credit-collection.billing.store') }}" method="POST" id="soa-form">
        @csrf
        @if($mode == 'create')
            <input type="hidden" name="customer_id" value="{{ $order->customer_id }}">
            <input type="hidden" name="sales_order_ids[]" value="{{ $order->id }}">
        @elseif($mode == 'edit')
            <input type="hidden" name="customer_id" value="{{ $soa->customer_id }}">
            @foreach($soa->salesOrders as $so)
                <input type="hidden" name="sales_order_ids[]" value="{{ $so->id }}">
            @endforeach
        @endif
        {{-- manual mode: customer_id submitted via select --}}

        <div class="row">
            <div class="col-xl-11 mx-auto">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-0">{{ $mode == 'manual' ? 'Add SOA' : 'Statement Preparation' }}</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin-finance.credit-collection.billing') }}">Billing</a></li>
                                <li class="breadcrumb-item active">Preparation</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        {{-- <button type="submit" name="status" value="draft" class="btn btn-outline-secondary rounded shadow-sm px-4 d-flex align-items-center justify-content-center" style="height: 40px !important; padding-top: 0 !important; padding-bottom: 0 !important;"><i class="las la-save"></i>Save Draft</button> --}}
                        <button type="submit" name="status" value="for_approval" class="btn btn-primary rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 40px !important; padding-top: 0 !important; padding-bottom: 0 !important;"><i class="las la-check-circle"></i>Submit for Approval</button>
                    </div>
                </div>

            <div class="preparation-card">
                <!-- Statement Header -->
                <div class="mb-5">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center gap-4">
                            <img src="{{ asset('images/claeritian_logo.png') }}" alt="Logo" style="height: 70px;">
                            <div>
                                <h5 class="fw-bold text-danger mb-0" style="font-size: 1.1rem; letter-spacing: 0.5px;">Claretian Communications Foundation, Inc.</h5>
                                <div class="text-muted" style="font-size: 0.75rem; line-height: 1.4;">
                                    8 Mayumi Street, U.P. P.O. Box 4, Diliman 1101 Quezon City, Philippines<br>
                                    Tel.: (02) 921-3984 · Fax: (02) 921-6205<br>
                                    Email: ccfi@claretianpublications.com · Website: www.claretianpublications.ph
                                </div>
                            </div>
                        </div>
                        <div class="text-end text-muted small">
                            {{ $mode == 'manual' ? 'MANUAL CREATE' : strtoupper($mode) . ' MODE' }}
                        </div>
                    </div>
                    <div class="text-center mt-4 pt-3 border-top">
                        <h2 class="fw-bold text-primary mb-0" style="letter-spacing: 2px;">ACCOUNT STATEMENT</h2>
                    </div>
                </div>

                <div class="statement-header-info">
                    <div class="header-item">
                        <label>Statement Number</label>
                        <input type="text" name="soa_number" class="form-control form-control-sm fw-bold border" value="{{ isset($soa) ? $soa->soa_number : 'AS-'.date('Y').'-'.rand(1000,9999) }}" required>
                    </div>
                    <div class="header-item">
                        <label>Statement Date</label>
                        <input type="date" name="soa_date" class="form-control form-control-sm fw-bold border" value="{{ isset($soa) ? $soa->created_at->format('Y-m-d') : now()->format('Y-m-d') }}">
                    </div>
                    <div class="header-item">
                        <label>Billing Period</label>
                        <div class="d-flex gap-2">
                            <input type="date" name="billing_period_start" class="form-control form-control-sm border" value="{{ isset($soa) ? $soa->billing_period_start : now()->startOfMonth()->format('Y-m-d') }}">
                            <input type="date" name="billing_period_end" class="form-control form-control-sm border" value="{{ isset($soa) ? $soa->billing_period_end : now()->endOfMonth()->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted text-uppercase small mb-3">Bill To:</label>
                        <div class="p-3 bg-white rounded border">
                            @if($mode == 'manual')
                                <div class="mb-2">
                                    <label class="extra-small text-muted fw-bold">Select Customer</label>
                                    <select name="customer_id" id="customer_select" class="form-select form-select-sm border" required onchange="fillCustomerInfo(this)">
                                        <option value="">-- Select Customer --</option>
                                        @foreach($customers as $cust)
                                            <option value="{{ $cust->customer_id }}"
                                                data-contact="{{ $cust->contact_person }}"
                                                data-address="{{ $cust->billing_address }}"
                                                data-name="{{ $cust->customer_name ?? $cust->company_name }}">
                                                {{ $cust->customer_name ?? $cust->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="extra-small text-muted fw-bold">Contact Person</label>
                                    <input type="text" id="customer_contact" name="contact_person" class="form-control form-control-sm border">
                                </div>
                                <div>
                                    <label class="extra-small text-muted fw-bold">Billing Address</label>
                                    <textarea id="customer_address" name="billing_address" class="form-control form-control-sm border" rows="2"></textarea>
                                </div>
                            @else
                                <div class="mb-2">
                                    <label class="extra-small text-muted fw-bold">Customer Name</label>
                                    <input type="text" class="form-control form-control-sm border" value="{{ $mode == 'create' ? ($order->customer->customer_name ?? $order->customer->company_name ?? '') : (isset($soa) ? ($soa->customer->customer_name ?? $soa->customer->company_name ?? '') : '') }}" readonly>
                                </div>
                                <div class="mb-2">
                                    <label class="extra-small text-muted fw-bold">Contact Person</label>
                                    <input type="text" name="contact_person" class="form-control form-control-sm border" value="{{ $mode == 'create' ? ($order->customer->contact_person ?? '') : (isset($soa) ? ($soa->contact_person ?? $soa->customer->contact_person ?? '') : '') }}">
                                </div>
                                <div>
                                    <label class="extra-small text-muted fw-bold">Billing Address</label>
                                    <textarea name="billing_address" class="form-control form-control-sm border" rows="2">{{ $mode == 'create' ? ($order->customer->billing_address ?? '') : (isset($soa) ? ($soa->billing_address ?? $soa->customer->billing_address ?? '') : '') }}</textarea>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <label class="form-label fw-bold small text-muted text-uppercase small mb-3">Reference Information:</label>
                        <div class="small d-flex justify-content-end align-items-center mb-1">
                            <span class="fw-bold me-2">Contract #:</span>
                            <input type="text" class="form-control form-control-sm text-end border" style="width: 150px;" value="CONT-2026-0042">
                        </div>
                        <div class="small d-flex justify-content-end align-items-center mb-1">
                            <span class="fw-bold me-2">Dept:</span>
                            <input type="text" class="form-control form-control-sm text-end border" style="width: 150px;" value="Ads & Promo">
                        </div>
                        <div class="small d-flex justify-content-end align-items-center">
                            <span class="fw-bold me-2">Period:</span>
                            <input type="text" class="form-control form-control-sm text-end border" style="width: 150px;" value="Jan 2026">
                        </div>
                    </div>
                </div>

                <!-- Particulars Table -->
                <div class="table-responsive mb-3">
                    <table class="table particulars-table border" id="particulars-table">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Item / Service</th>
                                <th style="width: 35%;">Description</th>
                                <th style="width: 15%;">Qty/Size</th>
                                <th style="text-align: right; width: 15%;">Unit Price</th>
                                <th style="text-align: right; width: 15%;">Amount</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            @if($mode == 'create')
                            <tr class="item-row">
                                <td><input type="text" name="items[0][service]" class="form-control form-control-sm border" value="Order #{{ $order->so_number }}"></td>
                                <td><input type="text" name="items[0][description]" class="form-control form-control-sm border" value="Sales Order Reference"></td>
                                <td><input type="text" name="items[0][qty]" class="form-control form-control-sm border item-qty" value="1"></td>
                                <td><input type="number" name="items[0][price]" class="form-control form-control-sm border text-end unit-price" value="{{ $order->remaining_balance }}"></td>
                                <td align="right" class="fw-bold row-amount">₱ {{ number_format($order->remaining_balance, 2) }}</td>
                                <td></td>
                            </tr>
                            @elseif($mode == 'manual')
                            <tr class="item-row">
                                <td><input type="text" name="items[0][service]" class="form-control form-control-sm border" placeholder="Item / Service"></td>
                                <td><input type="text" name="items[0][description]" class="form-control form-control-sm border" placeholder="Description"></td>
                                <td><input type="text" name="items[0][qty]" class="form-control form-control-sm border item-qty" value="1"></td>
                                <td><input type="number" name="items[0][price]" class="form-control form-control-sm border text-end unit-price" value="0.00"></td>
                                <td align="right" class="fw-bold row-amount">₱ 0.00</td>
                                <td class="text-center"></td>
                            </tr>
                            @else
                                @foreach($soa->items ?? [] as $index => $item)
                                <tr class="item-row">
                                    <td><input type="text" name="items[{{ $index }}][service]" class="form-control form-control-sm border" value="{{ $item['service'] }}"></td>
                                    <td><input type="text" name="items[{{ $index }}][description]" class="form-control form-control-sm border" value="{{ $item['description'] }}"></td>
                                    <td><input type="text" name="items[{{ $index }}][qty]" class="form-control form-control-sm border item-qty" value="{{ $item['qty'] }}"></td>
                                    <td><input type="number" name="items[{{ $index }}][price]" class="form-control form-control-sm border text-end unit-price" value="{{ $item['price'] }}"></td>
                                    <td align="right" class="fw-bold row-amount">₱ {{ number_format($item['qty'] * $item['price'], 2) }}</td>
                                    <td></td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                             <!-- Totals -->
                             <tr>
                                <td colspan="3" rowspan="3" class="border-0 align-top pt-4">
                                    @if($mode == 'manual')
                                        <button type="button" class="btn btn-success btn-sm rounded shadow-sm px-4 d-flex align-items-center gap-1" id="btn-add-row">
                                            <i class="las la-plus"></i> Add Row
                                        </button>
                                    @endif
                                    <div class="alert alert-info py-2 small mt-3 mb-0">
                                        <i class="las la-info-circle me-1"></i> Totals are automatically calculated based on the input particulars.
                                    </div>
                                </td>
                                <td align="right" class="fw-bold bg-light">Subtotal</td>
                                <td align="right" class="fw-bold bg-light" id="subtotal">₱ {{ number_format($mode == 'create' ? $order->final_total : ($mode == 'manual' ? 0 : $soa->total_amount), 2) }}</td>
                                <td class="bg-light"></td>
                            </tr>
                            <tr>
                                <td align="right" class="fw-bold">VAT (0%)</td>
                                <td align="right" class="fw-bold" id="vat-amount">₱ 0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td align="right" class="fw-bold bg-primary text-white">TOTAL AMOUNT</td>
                                <td align="right" class="fw-bold bg-primary text-white" id="total-amount-display">₱ {{ number_format($mode == 'create' ? $order->final_total : ($mode == 'manual' ? 0 : $soa->total_amount), 2) }}</td>
                                <input type="hidden" name="total_amount" id="total-amount-input" value="{{ $mode == 'create' ? $order->final_total : ($mode == 'manual' ? 0 : $soa->total_amount) }}">
                                <td class="bg-primary"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Footer Section -->
                <div class="footer-section">
                    <div>
                        <h6 class="fw-bold text-uppercase small text-muted mb-3">Payment Instructions</h6>
                        <div class="payment-info border">
                            <div class="small mb-3">Please make checks payable to <span class="fw-bold">CLARENTIAN MULTIMEDIA</span> or deposit through:</div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="fw-bold small">METROBANK</div>
                                    <div class="extra-small text-muted">Acc No: 1234-5678-90</div>
                                </div>
                                <div class="col-6">
                                    <div class="fw-bold small">BPI</div>
                                    <div class="extra-small text-muted">Acc No: 0987-6543-21</div>
                                </div>
                                <div class="col-6">
                                    <div class="fw-bold small">BDO</div>
                                    <div class="extra-small text-muted">Acc No: 1122-3344-55</div>
                                </div>
                            </div>
                            <div class="mt-4 extra-small text-muted">
                                <i class="las la-phone me-1"></i> Question? Contact Billing at billing@clarentian.com
                            </div>
                        </div>
                    </div>
                    <div>
                        <h6 class="fw-bold text-uppercase small text-muted mb-3">Signatories</h6>
                        <div class="signatory-item">
                            <div class="name">Finance Manager</div>
                            <div class="role small">Prepared By (Billing)</div>
                        </div>
                        <div class="signatory-item">
                            <div class="name">General Manager</div>
                            <div class="role small">Pending Approval (Manager)</div>
                        </div>
                    </div>
                </div>

                <div class="invoice-actions d-flex justify-content-end align-items-center">
                    <a href="{{ route('admin-finance.credit-collection.billing') }}" class="btn btn-light px-4 me-2 shadow-sm border">Cancel</a>
                    {{-- <button type="submit" name="status" value="draft" class="btn btn-outline-primary px-4 me-2 shadow-sm">Save Draft</button> --}}
                    <button type="submit" name="status" value="pending" class="btn btn-primary px-4 shadow">
                        Submit for Approval <i class="las la-paper-plane ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        let rowCount = {{ $mode == 'create' || $mode == 'manual' ? 1 : ($soa->items->count() ?? 1) }};

        const addRowBtn = document.getElementById('btn-add-row');
        if (addRowBtn) addRowBtn.addEventListener('click', function() {
            const tableBody = document.getElementById('table-body');
            const newRow = document.createElement('tr');
            newRow.className = 'item-row';
            newRow.innerHTML = `
                <td><input type="text" name="items[${rowCount}][service]" class="form-control form-control-sm border"></td>
                <td><input type="text" name="items[${rowCount}][description]" class="form-control form-control-sm border"></td>
                <td><input type="text" name="items[${rowCount}][qty]" class="form-control form-control-sm border item-qty" value="1"></td>
                <td><input type="number" name="items[${rowCount}][price]" class="form-control form-control-sm border text-end unit-price" value="0.00"></td>
                <td align="right" class="fw-bold row-amount">₱ 0.00</td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-xs btn-remove-row"><i class="las la-times"></i></button>
                </td>
            `;
            tableBody.appendChild(newRow);
            rowCount++;

            // Add event listener to remove button
            newRow.querySelector('.btn-remove-row').addEventListener('click', function() {
                newRow.remove();
                calculateTotals();
            });

            // Add event listeners to unit price and qty for total calculation
            newRow.querySelector('.unit-price').addEventListener('input', calculateTotals);
            newRow.querySelector('.item-qty').addEventListener('input', calculateTotals);
        });

        function fillCustomerInfo(select) {
            const opt = select.options[select.selectedIndex];
            const contact = document.getElementById('customer_contact');
            const address = document.getElementById('customer_address');
            if (contact) contact.value = opt.dataset.contact || '';
            if (address) address.value = opt.dataset.address || '';
        }

        // Event listener for initial row
        document.querySelectorAll('.unit-price, .item-qty').forEach(input => {
            input.addEventListener('input', calculateTotals);
        });

        function calculateTotals() {
            let subtotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const qtyInput = row.querySelector('.item-qty');
                const qty = qtyInput ? (parseFloat(qtyInput.value) || 1) : 1;
                const price = parseFloat(row.querySelector('.unit-price').value) || 0;
                const rowAmount = qty * price;
                subtotal += rowAmount;
                row.querySelector('.row-amount').textContent = '₱ ' + rowAmount.toLocaleString(undefined, {minimumFractionDigits: 2});
            });

            const vat = 0; // 0% as per current design
            const total = subtotal + vat;

            document.getElementById('subtotal').textContent = '₱ ' + subtotal.toLocaleString(undefined, {minimumFractionDigits: 2});
            document.getElementById('total-amount-display').textContent = '₱ ' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
            document.getElementById('total-amount-input').value = total.toFixed(2);
        }

        // Run calculation on page load to sync correct totals
        calculateTotals();
    </script>
    @endpush
</x-app-layout>
