<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff;">
    <div class="card-header bg-white border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 45px; height: 45px;">
                <i class="las la-store-alt fs-24"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark fs-18">Bookstore Sales Ledger</h5>
                <p class="text-muted small mb-0">Revenues from physical bookstore walk-in cashier terminals, categorized by payment type</p>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">7 Accounts</span>
    </div>
    <div class="card-body px-4 pt-1 pb-4">
        <div class="row g-3">
            
            <!-- Daily Sales Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('Daily Sales', document.getElementById('dailySalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-calendar-day fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Daily Sales</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Total Bookstore sales transactions logged today.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Revenue</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱{{ number_format($bookstoreDailySales, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cash Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('Cash Sales', document.getElementById('cashSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-coins fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Cash Sales</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Physical cash counter collections from registers.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Balance</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱{{ number_format($bookstoreCashSales, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GCash Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('GCash / QR PH / E-Wallets', document.getElementById('gcashSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-mobile-alt fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">GCash & QR PH</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Mobile e-wallet settlements from cashiers.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Balance</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱{{ number_format($bookstoreGcashSales, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Credit Card Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('Credit Card Payments', document.getElementById('creditCardSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-credit-card fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Credit Card</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Merchant card terminal settlements.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Balance</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱{{ number_format($bookstoreCardSales, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charge Account Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('Charge Account Sales', document.getElementById('chargeAccountSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-file-invoice-dollar fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Charge Account</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Corporate credit accounts ledger.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Balance</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱{{ number_format($bookstoreChargeSales, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Discounts Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('Discounts ledger', document.getElementById('discountsTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-tag fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Discounts</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Promotional & courtesy deductions.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Valuation</span>
                            <span class="fw-bold fs-15 text-danger" style="font-weight: 800 !important; color: #ef4444 !important;">₱{{ number_format($bookstoreDiscountSales ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee Purchases Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('Employee Purchases', document.getElementById('employeePurchasesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-user-tie fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Employee Purchases</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Staff bookstore purchases & salary deductions.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Balance</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱0.00</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- TEMPLATE CONTAINER - DAILY SALES -->
<div id="dailySalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Order No</th>
                    <th>Payment Method</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookstoreDailyOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark font-monospace">#{{ $order->order_number ?? 'ORD-'.$order->id }}</span></td>
                    <td><span class="badge bg-light text-dark border">{{ strtoupper($order->payment_method ?? 'Cash') }}</span></td>
                    <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                    <td><span class="badge bg-success-subtle text-success">Completed</span></td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No transactions logged today.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - CASH SALES -->
<div id="cashSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Order No</th>
                    <th>Cashier</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookstoreCashOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark font-monospace">#{{ $order->order_number ?? 'ORD-'.$order->id }}</span></td>
                    <td>Bookstore Terminal 1</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td><span class="badge bg-success-subtle text-success">Paid</span></td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No cash transactions logged in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - GCASH SALES -->
<div id="gcashSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Order No</th>
                    <th>Ref ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookstoreGcashOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark font-monospace">#{{ $order->order_number ?? 'ORD-'.$order->id }}</span></td>
                    <td><span class="font-monospace text-muted">{{ $order->payment_reference ?? 'REF-'.rand(100000, 999999) }}</span></td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td><span class="badge bg-success-subtle text-success">Settled</span></td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No mobile e-wallet transactions logged in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - CREDIT CARD SALES -->
<div id="creditCardSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Order No</th>
                    <th>Auth Code</th>
                    <th>Date</th>
                    <th>Terminal</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookstoreCardOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark font-monospace">#{{ $order->order_number ?? 'ORD-'.$order->id }}</span></td>
                    <td><span class="font-monospace text-muted">{{ $order->payment_reference ?? 'AUTH-'.rand(1000, 9999) }}</span></td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td>BDO POS Terminal</td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No card transactions logged in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - CHARGE ACCOUNT -->
<div id="chargeAccountSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Order No</th>
                    <th>Client Account</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookstoreChargeOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark font-monospace">#{{ $order->order_number ?? 'ORD-'.$order->id }}</span></td>
                    <td>{{ $order->customer->customer_name ?? $order->customer->company_name ?? 'Walk-in Corporate Account' }}</td>
                    <td>{{ $order->created_at->addDays(30)->format('M d, Y') }}</td>
                    <td><span class="badge bg-warning-subtle text-warning">Unpaid (Net 30)</span></td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No charge account transactions logged in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - RETURNS -->
<div id="returnsTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Return Code</th>
                    <th>Item Description</th>
                    <th>Return Date</th>
                    <th>Reason</th>
                    <th class="text-end">Refunded Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No return logs recorded in the database.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - DISCOUNTS -->
<div id="discountsTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Order No</th>
                    <th>Customer</th>
                    <th>Discount Type</th>
                    <th>Discount Rate</th>
                    <th>Order Total</th>
                    <th class="text-end">Discount Deducted</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookstoreDiscountOrders ?? [] as $order)
                @php
                    $pct = (float) ($order->discount_percentage ?? 0);
                    if ($pct > 0) {
                        $discountTypeLabel = 'Percentage';
                        $discountTypeBadge = 'bg-primary-subtle text-primary';
                        $rateStr = number_format($pct, 2) . '%';
                    } elseif ((float)$order->discount_amount > 0) {
                        $discountTypeLabel = 'Fixed Amount';
                        $discountTypeBadge = 'bg-info-subtle text-info';
                        $origTotal = (float)$order->total_amount + (float)$order->discount_amount;
                        $calcPct = $origTotal > 0 ? (((float)$order->discount_amount / $origTotal) * 100) : 0;
                        $rateStr = $calcPct > 0 ? number_format($calcPct, 2) . '%' : 'Fixed Amount';
                    } else {
                        $discountTypeLabel = 'None';
                        $discountTypeBadge = 'bg-secondary-subtle text-secondary';
                        $rateStr = '0.00%';
                    }
                @endphp
                <tr>
                    <td><span class="fw-bold text-dark font-monospace">#{{ $order->so_number }}</span></td>
                    <td>{{ $order->customer->customer_name ?? ($order->customer->company_name ?? 'Walk-in Customer') }}</td>
                    <td><span class="badge {{ $discountTypeBadge }} px-2 py-1">{{ $discountTypeLabel }}</span></td>
                    <td>{{ $rateStr }}</td>
                    <td>₱{{ number_format($order->total_amount + $order->discount_amount, 2) }}</td>
                    <td class="text-end fw-bold text-danger">₱{{ number_format($order->discount_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No discount records found in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - EMPLOYEE PURCHASES -->
<div id="employeePurchasesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Emp ID</th>
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Purchase Date</th>
                    <th class="text-end">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No staff purchase logs recorded in the database.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
