<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff;">
    <div class="card-header bg-white border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 45px; height: 45px;">
                <i class="las la-building fs-24"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark fs-18">Wholesale Key Accounts Ledger</h5>
                <p class="text-muted small mb-0">Sales ledgers and performance history mapped by major retail bookstore chains and independent outlets</p>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">5 Accounts</span>
    </div>
    <div class="card-body px-4 pt-1 pb-4">
        <div class="row g-3">
            
            <!-- National Book Store Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('Key Account: National Book Store', document.getElementById('nbsTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-building fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">National Book Store</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">NBS branch network sales.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Balance</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pandayan Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('Key Account: Pandayan Bookshop', document.getElementById('pandayanTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-book-reader fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Pandayan Bookshop</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Pandayan branch network sales.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Balance</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SM Store Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('Key Account: SM Store (Homeworld)', document.getElementById('smTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-shopping-cart fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">SM Store</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">SM department store sales.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Balance</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Chains Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('Key Account: Other Bookstore Chains', document.getElementById('otherChainsTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-link fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Other Chains</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Rex, National, Goodwill outlets.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Balance</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Independent Bookstores Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('Key Account: Independent Bookstores', document.getElementById('indieTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-store fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Independent Bookstores</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Local private book dealerships.</p>
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

<!-- TEMPLATE - NATIONAL BOOK STORE -->
<div id="nbsTemplate" style="display: none;">
    <div class="row mb-3 pb-3 border-bottom align-items-center g-2">
        <div class="col-md-4">
            <span class="text-muted small d-block">Account Name</span>
            <strong class="text-dark">National Book Store Inc.</strong>
        </div>
        <div class="col-md-4">
            <span class="text-muted small d-block">Payment Terms</span>
            <strong class="text-dark">Net 60 Days (Rebates: 3%)</strong>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="text-muted small d-block">Outstanding Balance</span>
            <strong class="text-danger fs-16">₱0.00</strong>
        </div>
    </div>

    <ul class="nav nav-tabs modal-tabs mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#nbs-orders" type="button" role="tab">Orders & Delivery</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nbs-invoices" type="button" role="tab">Invoices & Aging</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nbs-collections" type="button" role="tab">Collections & Rebates</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nbs-returns" type="button" role="tab">Returns & Credit</button>
        </li>
    </ul>

    <div class="tab-content pt-2">
        <!-- Orders -->
        <div class="tab-pane fade show active" id="nbs-orders" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Ref Number</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-end">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No wholesale orders recorded in the database.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Invoices & Aging -->
        <div class="tab-pane fade" id="nbs-invoices" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>Date</th>
                            <th>Aging Category</th>
                            <th class="text-end">Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No wholesale invoices recorded in the database.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Collections & Rebates -->
        <div class="tab-pane fade" id="nbs-collections" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Collection Receipt</th>
                            <th>Payment Date</th>
                            <th>Deductions / Fee</th>
                            <th>Volume Rebate Given</th>
                            <th class="text-end">Net Collected</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No collections recorded in the current period.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Returns & Credit Notes -->
        <div class="tab-pane fade" id="nbs-returns" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>CN Reference</th>
                            <th>Date</th>
                            <th>Deduction Reason</th>
                            <th class="text-end">Value Credited</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No returned items or credit notes recorded.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- TEMPLATE - PANDAYAN -->
<div id="pandayanTemplate" style="display: none;">
    <div class="row mb-3 pb-3 border-bottom align-items-center g-2">
        <div class="col-md-4">
            <span class="text-muted small d-block">Account Name</span>
            <strong class="text-dark">Pandayan Bookshop Inc.</strong>
        </div>
        <div class="col-md-4">
            <span class="text-muted small d-block">Payment Terms</span>
            <strong class="text-dark">Net 30 Days (Rebates: 2.5%)</strong>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="text-muted small d-block">Outstanding Balance</span>
            <strong class="text-danger fs-16">₱0.00</strong>
        </div>
    </div>

    <ul class="nav nav-tabs modal-tabs mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pandayan-orders" type="button" role="tab">Orders & Delivery</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pandayan-invoices" type="button" role="tab">Invoices & Aging</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pandayan-collections" type="button" role="tab">Collections</button>
        </li>
    </ul>

    <div class="tab-content pt-2">
        <div class="tab-pane fade show active" id="pandayan-orders" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Ref Number</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-end">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No wholesale orders recorded.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="pandayan-invoices" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>Date</th>
                            <th>Aging Category</th>
                            <th class="text-end">Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No wholesale invoices recorded.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="pandayan-collections" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Collection Receipt</th>
                            <th>Payment Date</th>
                            <th class="text-end">Net Collected</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">No collections recorded in the current period.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- TEMPLATE - SM STORE -->
<div id="smTemplate" style="display: none;">
    <div class="row mb-3 pb-3 border-bottom align-items-center g-2">
        <div class="col-md-4">
            <span class="text-muted small d-block">Account Name</span>
            <strong class="text-dark">SM Prime Holdings (Homeworld)</strong>
        </div>
        <div class="col-md-4">
            <span class="text-muted small d-block">Payment Terms</span>
            <strong class="text-dark">Net 60 Days (Rebates: 5%)</strong>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="text-muted small d-block">Outstanding Balance</span>
            <strong class="text-danger fs-16">₱0.00</strong>
        </div>
    </div>

    <ul class="nav nav-tabs modal-tabs mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#sm-orders" type="button" role="tab">Orders & Delivery</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#sm-invoices" type="button" role="tab">Invoices & Aging</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#sm-collections" type="button" role="tab">Collections</button>
        </li>
    </ul>

    <div class="tab-content pt-2">
        <div class="tab-pane fade show active" id="sm-orders" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Ref Number</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-end">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No wholesale orders recorded.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="sm-invoices" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>Date</th>
                            <th>Aging Category</th>
                            <th class="text-end">Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No wholesale invoices recorded.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="sm-collections" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Collection Receipt</th>
                            <th>Payment Date</th>
                            <th class="text-end">Net Collected</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">No collections recorded in the current period.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- TEMPLATE - OTHER CHAINS -->
<div id="otherChainsTemplate" style="display: none;">
    <div class="row mb-3 pb-3 border-bottom align-items-center g-2">
        <div class="col-md-4">
            <span class="text-muted small d-block">Account Name</span>
            <strong class="text-dark">Other Bookstore Chains (Rex, National, Goodwill)</strong>
        </div>
        <div class="col-md-4">
            <span class="text-muted small d-block">Payment Terms</span>
            <strong class="text-dark">Net 30/60 Days Variable</strong>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="text-muted small d-block">Outstanding Balance</span>
            <strong class="text-danger fs-16">₱0.00</strong>
        </div>
    </div>

    <ul class="nav nav-tabs modal-tabs mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#other-orders" type="button" role="tab">Orders & Delivery</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#other-invoices" type="button" role="tab">Invoices & Aging</button>
        </li>
    </ul>

    <div class="tab-content pt-2">
        <div class="tab-pane fade show active" id="other-orders" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Chain Partner</th>
                            <th>Ref Number</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-end">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No wholesale orders recorded.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="other-invoices" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Chain Partner</th>
                            <th>Invoice Number</th>
                            <th>Date</th>
                            <th>Aging Category</th>
                            <th class="text-end">Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No wholesale invoices recorded.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- TEMPLATE - INDEPENDENTS -->
<div id="indieTemplate" style="display: none;">
    <div class="row mb-3 pb-3 border-bottom align-items-center g-2">
        <div class="col-md-4">
            <span class="text-muted small d-block">Account Name</span>
            <strong class="text-dark">Independent Bookstores Dealers Network</strong>
        </div>
        <div class="col-md-4">
            <span class="text-muted small d-block">Payment Terms</span>
            <strong class="text-dark">Cash on Delivery / Net 15 Days</strong>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="text-muted small d-block">Outstanding Balance</span>
            <strong class="text-danger fs-16">₱0.00</strong>
        </div>
    </div>

    <ul class="nav nav-tabs modal-tabs mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#indie-orders" type="button" role="tab">Orders & Delivery</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#indie-invoices" type="button" role="tab">Invoices & Aging</button>
        </li>
    </ul>

    <div class="tab-content pt-2">
        <div class="tab-pane fade show active" id="indie-orders" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Dealer Partner</th>
                            <th>Ref Number</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-end">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No wholesale orders recorded.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="indie-invoices" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Dealer Partner</th>
                            <th>Invoice Number</th>
                            <th>Date</th>
                            <th>Aging Category</th>
                            <th class="text-end">Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No wholesale invoices recorded.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
