<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff;">
    <div class="card-header bg-white border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 45px; height: 45px;">
                <i class="las la-shopping-basket fs-24"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark fs-18">E-Commerce Channels Ledger</h5>
                <p class="text-muted small mb-0">Revenues and transaction logs across website portals and integrated online merchant platforms</p>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">8 Accounts</span>
    </div>
    <div class="card-body px-4 pt-1 pb-4">
        <div class="row g-3">
            
            <!-- Website Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('Website Store Sales', document.getElementById('websiteSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-globe fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Website</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Direct online portal retail checkout.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Revenue</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱{{ number_format($ecomWebsiteSales, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shopee Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('Shopee Store Sales', document.getElementById('shopeeSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-shopping-bag fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Shopee</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Shopee mall store sales orders.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Revenue</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱{{ number_format($ecomShopeeSales, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lazada Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('Lazada Store Sales', document.getElementById('lazadaSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-shopping-cart fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Lazada</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Lazada flagship store checkout orders.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Revenue</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱{{ number_format($ecomLazadaSales, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Facebook Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('Facebook Messenger Orders', document.getElementById('facebookSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-sms fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Facebook</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Social inbox sales orders.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Revenue</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱{{ number_format($ecomFacebookSales, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TikTok Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('TikTok Shop Sales', document.getElementById('tiktokSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-video fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">TikTok</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">TikTok Shop checkout orders.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Revenue</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱{{ number_format($ecomTiktokSales, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Gateway Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('Payment Gateway Fees & Settlements', document.getElementById('paymentGatewayTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-credit-card fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Payment Gateway</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Gateway fees & merchant cuts.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Balance</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('Shipping Fees Collection Ledger', document.getElementById('shippingTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-shipping-fast fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Shipping</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Courier waybill fee collections.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Balance</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Refunds Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('E-Commerce Refunds Ledger', document.getElementById('ecomRefundsTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-undo fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Refunds</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Customer return refund balances.</p>
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

<!-- TEMPLATE CONTAINER - WEBSITE -->
<div id="websiteSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Customer Name</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ecomWebsiteOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark font-monospace">#{{ $order->order_number ?? 'WEB-'.$order->id }}</span></td>
                    <td>{{ $order->customer->customer_name ?? $order->customer->company_name ?? 'Online Buyer' }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td><span class="badge bg-success-subtle text-success">Paid</span></td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No website orders recorded in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - SHOPEE -->
<div id="shopeeSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Shopee Order ID</th>
                    <th>Buyer Username</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ecomShopeeOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark font-monospace">#{{ $order->tracking_number ?? 'SHP-'.$order->id }}</span></td>
                    <td>{{ $order->customer_name ?? 'shopee_buyer' }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td><span class="badge bg-success-subtle text-success">Delivered</span></td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No Shopee orders recorded in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - LAZADA -->
<div id="lazadaSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Lazada Order ID</th>
                    <th>Buyer Name</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ecomLazadaOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark font-monospace">#{{ $order->tracking_number ?? 'LAZ-'.$order->id }}</span></td>
                    <td>{{ $order->customer_name ?? 'Lazada Customer' }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td><span class="badge bg-success-subtle text-success">Delivered</span></td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No Lazada orders recorded in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - FACEBOOK -->
<div id="facebookSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Messenger Reference</th>
                    <th>Customer Name</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ecomFacebookOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark font-monospace">#{{ $order->order_number ?? 'FB-'.$order->id }}</span></td>
                    <td>{{ $order->customer->customer_name ?? $order->customer->company_name ?? 'FB Buyer' }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td><span class="badge bg-success-subtle text-success">Processed</span></td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No Facebook Messenger orders recorded in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - TIKTOK -->
<div id="tiktokSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>TikTok Order ID</th>
                    <th>Buyer Username</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ecomTiktokOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark font-monospace">#{{ $order->tracking_number ?? 'TT-'.$order->id }}</span></td>
                    <td>{{ $order->customer_name ?? 'tiktok_buyer' }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td><span class="badge bg-success-subtle text-success">Delivered</span></td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No TikTok Shop orders recorded in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - PAYMENT GATEWAY -->
<div id="paymentGatewayTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Gateway Name</th>
                    <th class="text-end">Gross Amount</th>
                    <th class="text-center">Gateway Fee</th>
                    <th class="text-end">Net Payout</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No gateway transactions recorded in the database.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - SHIPPING -->
<div id="shippingTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Courier Waybill</th>
                    <th>Order No</th>
                    <th class="text-end">Collected Shipping</th>
                    <th class="text-end">Actual Courier Charge</th>
                    <th class="text-end">Variance Surplus / (Deficit)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No shipping transactions recorded in the database.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - REFUNDS -->
<div id="ecomRefundsTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Refund Code</th>
                    <th>Order No</th>
                    <th>Channel</th>
                    <th>Refund Reason</th>
                    <th>Date Approved</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No return refunds recorded in the database.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
