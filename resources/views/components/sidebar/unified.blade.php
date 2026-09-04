@php
    $user = auth()->user();
    
    // Check division access (for users in other divisions who might have cross-division permissions)
    $hasMarketingDivision = $user->divisions()->where('division', 'Marketing Division')->exists() || $user->division === 'Marketing Division' || $user->isSuperAdmin();
    $hasProductionDivision = $user->divisions()->where('division', 'Production Division')->exists() || $user->division === 'Production Division' || $user->isSuperAdmin();
    $hasAdminFinanceDivision = $user->divisions()->where('division', 'Admin & Finance Division')->exists() || $user->division === 'Admin & Finance Division' || $user->isSuperAdmin();
    
    // Marketing Permissions
    $hasDashM = $user->hasPermission('marketing.dashboard') && $hasMarketingDivision;
    $hasCust = $user->hasPermission('marketing.customers') && $hasMarketingDivision;
    $hasAreaS = $user->hasPermission('marketing.area_sales') && $hasMarketingDivision;
    $hasDirectS = $user->hasPermission('marketing.direct_sales') && $hasMarketingDivision;
    $hasAds = $user->hasPermission('marketing.ads_promo') && $hasMarketingDivision;
    $hasEcomM = $user->hasPermission('marketing.ecom') && $hasMarketingDivision;
    $hasBook = $user->hasPermission('marketing.book_mgmt') && $hasMarketingDivision;
    $hasSupp = $user->hasPermission('marketing.supplier_mgmt') && $hasMarketingDivision;
    $hasAppM = $user->hasPermission('marketing.approval_queue') && $hasMarketingDivision;
    $hasReqM = $user->hasPermission('marketing.my_requests') && $hasMarketingDivision;

    // Production Permissions
    $hasDashP = $user->hasPermission('production.dashboard') && $hasProductionDivision;
    $hasInv = $user->hasPermission('production.inventory') && $hasProductionDivision;
    $hasLog = $user->hasPermission('production.logistic') && $hasProductionDivision;
    $hasDTO = $user->hasPermission('production.dto') && $hasProductionDivision;
    $hasFORD = $user->hasPermission('production.ford') && $hasProductionDivision;
    $hasPrint = $user->hasPermission('production.printing') && $hasProductionDivision;
    $hasAppP = $user->hasPermission('production.approval_queue') && $hasProductionDivision;
    $hasReqP = $user->hasPermission('production.my_requests') && $hasProductionDivision;

    // Admin & Finance Permissions
    $hasDashAF = $user->hasPermission('admin_finance.dashboard') && $hasAdminFinanceDivision;
    $hasMIS = $user->hasPermission('admin_finance.mis') && $hasAdminFinanceDivision;
    $hasGSD = $user->hasPermission('admin_finance.gsd') && $hasAdminFinanceDivision;
    $hasCC = $user->hasPermission('admin_finance.credit_collection') && $hasAdminFinanceDivision;
    $hasAcc = $user->hasPermission('admin_finance.accounting') && $hasAdminFinanceDivision;
    $hasPettyCash = true; // All users have access to petty cash
    $hasFreightVoucher = true; // All users have access to freight voucher
    $hasHR = $user->hasPermission('admin_finance.hr') && $hasAdminFinanceDivision;
    $hasAppAF = $user->hasPermission('admin_finance.approval_queue') && $hasAdminFinanceDivision;
    $hasReqAF = $user->hasPermission('admin_finance.my_requests') && $hasAdminFinanceDivision;
    $hasServiceRequestsAF = $user->hasPermission('admin_finance.service_requests') && $hasAdminFinanceDivision;
    $hasChartOfAccounts = ($user->hasPermission('admin_finance.accounting.chart_of_accounts') || $user->hasPermission('admin_finance.accounting')) && $hasAdminFinanceDivision;
@endphp

@push('styles')
<style>
    .submenu-section-header {
        font-size: 10px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 1.2px !important;
        color: #8a99ad !important;
        padding: 16px 15px 4px 45px !important;
        display: block !important;
    }
    
    /* Remove top padding for the first header in the list */
    .modern-nav-submenu > .submenu-section-header:first-child {
        padding-top: 10px !important;
    }
    
    .deznav-scroll::-webkit-scrollbar {
        width: 6px !important;
    }
    .deznav-scroll::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.02) !important;
    }
    .deznav-scroll::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.15) !important;
        border-radius: 4px !important;
    }
    .deznav-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.3) !important;
    }
</style>
@endpush

<nav class="modern-nav-menu">
    {{-- Unified Dashboards / Common items --}}
    @if($hasDashM || $hasDashP || $hasDashAF)
    <a href="{{ $hasDashM ? route('marketing.dashboard') : ($hasDashP ? route('production.dashboard') : route('admin-finance.dashboard')) }}" class="modern-nav-item" data-page="dashboard">
        <div class="modern-nav-icon"><i class="flaticon-381-home-2"></i></div>
        <span class="modern-nav-label">Dashboard</span>
    </a>
    @endif

    {{-- Unified Requests/Approvals --}}
    @if($hasReqM || $hasReqP || $hasReqAF)
    <a href="{{ $hasReqM ? route('marketing.my-requests') : ($hasReqP ? route('production.my-requests') : route('admin-finance.my-requests')) }}" class="modern-nav-item">
        <div class="modern-nav-icon"><i class="las la-envelope-open-text"></i></div>
        <span class="modern-nav-label">My Requests</span>
    </a>
    @endif

    @if($hasAppM || $hasAppP || $hasAppAF)
    <a href="{{ $hasAppM ? route('marketing.approval-queue') : ($hasAppP ? route('production.approval-queue') : route('admin-finance.approval-queue')) }}" class="modern-nav-item">
        <div class="modern-nav-icon"><i class="las la-clipboard-check"></i></div>
        <span class="modern-nav-label">Approval Queue</span>
    </a>
    @endif

    {{-- Marketing Sections --}}
    @if($hasCust)
    <a href="{{ route('marketing.customers') }}" class="modern-nav-item"><div class="modern-nav-icon"><i class="las la-users"></i></div><span class="modern-nav-label">Customer Management</span></a>
    <a href="{{ route('marketing.companies') }}" class="modern-nav-item"><div class="modern-nav-icon"><i class="las la-building"></i></div><span class="modern-nav-label">Company Management</span></a>
    @endif
    
    @if($hasAreaS)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-map-marked-alt"></i></div><span class="modern-nav-label">Area Sales</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('marketing.sales-orders.list') }}" class="modern-nav-subitem">Sales Orders List</a>
            <a href="{{ route('marketing.direct-invoice.website') }}" class="modern-nav-subitem">Direct Invoice (Website)</a>
            <a href="{{ route('marketing.direct-invoice.ecom') }}" class="modern-nav-subitem">Direct Invoice (E-com)</a>
            <a href="{{ route('marketing.acknowledgement-receipt') }}" class="modern-nav-subitem">Acknowledgement Receipt</a>
            <a href="{{ route('marketing.credit-memo') }}" class="modern-nav-subitem">Credit Memo Form</a>
            <a href="{{ route('marketing.proof-of-payment') }}" class="modern-nav-subitem">Proof of Payment</a>
        </div>
    </div>
    @endif

    @if($hasDirectS)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-store"></i></div><span class="modern-nav-label">Direct Sales</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('marketing.pos.sale') }}" class="modern-nav-subitem">POS System</a>
            <a href="{{ route('marketing.pos.products') }}" class="modern-nav-subitem">POS Products</a>
            <a href="{{ route('marketing.nbs-import.index') }}" class="modern-nav-subitem">NBS PO Import</a>
        </div>
    </div>
    @endif

    @if($hasAds)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-ad"></i></div><span class="modern-nav-label">Ads and Promo</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('marketing.ads-promo.crpr') }}" class="modern-nav-subitem">Marketing Plan Itinerary Budget</a>
            <a href="{{ route('marketing.ads-promo.sponsors') }}" class="modern-nav-subitem">List of Sponsors</a>
        </div>
    </div>
    @endif

    @if($hasEcomM)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-shopping-cart"></i></div><span class="modern-nav-label">MIBF POS</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu"><a href="{{ route('marketing.ecom.pos') }}" class="modern-nav-subitem">MIBF POS</a></div>
    </div>
    @endif

    @if($hasBook)
    <div class="modern-nav-group {{ request()->is('marketing/book-list*', 'marketing/non-books*', 'marketing/consignment*', 'marketing/book-indices*', 'marketing/book-bundles*') ? 'active' : '' }}">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-book"></i></div><span class="modern-nav-label">Book Management</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('marketing.products') }}" class="modern-nav-subitem">Book List (Master)</a>
            <a href="{{ route('marketing.non-books') }}" class="modern-nav-subitem">Non-Books</a>
            <a href="{{ route('marketing.indices') }}" class="modern-nav-subitem">Book Index</a>
            <a href="{{ route('marketing.bundles') }}" class="modern-nav-subitem">Book Bundles</a>
            <a href="{{ route('marketing.consignment.index') }}" class="modern-nav-subitem">Consignment Management</a>
        </div>
    </div>
    @endif

    @if($hasSupp)
    <a href="{{ route('marketing.suppliers') }}" class="modern-nav-item"><div class="modern-nav-icon"><i class="las la-truck-loading"></i></div><span class="modern-nav-label">Supplier Management</span></a>
    @endif

    {{-- Production Sections --}}
    @if($hasInv)
    <a href="{{ route('production.inventory.master') }}" class="modern-nav-item {{ request()->routeIs('production.inventory.master') ? 'active' : '' }}">
        <div class="modern-nav-icon"><i class="las la-boxes"></i></div>
        <span class="modern-nav-label">Master Inventory</span>
    </a>
    <div class="modern-nav-group {{ (request()->is('production/inventory*') && !request()->routeIs('production.inventory.master')) ? 'active' : '' }}">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-warehouse"></i></div><span class="modern-nav-label">Inventory Management</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('production.inventory.overview') }}" class="modern-nav-subitem">Inventory Overview</a>
            <a href="{{ route('production.inventory.add-stock') }}" class="modern-nav-subitem">Add Stock</a>
            <a href="{{ route('production.inventory.received') }}" class="modern-nav-subitem">Received Items</a>
        </div>
    </div>
    @endif

    <div class="modern-nav-group {{ request()->is('production/executive-dashboard*') ? 'active' : '' }}">
        <a href="{{ route('production.executive-dashboard.index') }}" class="modern-nav-item">
            <div class="modern-nav-icon"><i class="las la-chart-bar"></i></div>
            <span class="modern-nav-label">Executive Dashboard</span>
        </a>
    </div>

    <div class="modern-nav-group {{ request()->is('production/costing*') ? 'active' : '' }}">
        <a href="{{ route('production.costing.index') }}" class="modern-nav-item">
            <div class="modern-nav-icon"><i class="las la-calculator"></i></div>
            <span class="modern-nav-label">Production Costing</span>
        </a>
    </div>

    <div class="modern-nav-group {{ request()->is('production/assets*') ? 'active' : '' }}">
        <a href="{{ route('production.assets.index') }}" class="modern-nav-item">
            <div class="modern-nav-icon"><i class="las la-tools"></i></div>
            <span class="modern-nav-label">Fixed Assets</span>
        </a>
    </div>

    @if($hasLog)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-boxes"></i></div><span class="modern-nav-label">Logistics</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('production.logistic.pick-list-list') }}" class="modern-nav-subitem">Pick Lists</a>
            <a href="{{ route('production.logistic.packing-management') }}" class="modern-nav-subitem">Packing</a>
            <a href="{{ route('production.logistic.pickup-requests.index') }}" class="modern-nav-subitem">Logistics Service Order</a>
            <a href="{{ route('production.logistic.delivery-scheduling') }}" class="modern-nav-subitem">Delivery Scheduling</a>
            <a href="{{ route('production.logistic.driver-dashboard') }}" class="modern-nav-subitem">Driver Dashboard</a>
            <a href="{{ route('production.logistic.delivery-tracking') }}" class="modern-nav-subitem">Delivery Tracking</a>
            <a href="{{ route('production.logistic.purchase-order-list') }}" class="modern-nav-subitem">Purchase Orders</a>
            <a href="{{ route('production.logistic.receiving-report-list') }}" class="modern-nav-subitem">Receiving Reports</a>
        </div>
    </div>
    @endif

    @if($hasFORD)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-warehouse"></i></div><span class="modern-nav-label">FORD</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('production.ford.client-payment-posting') }}" class="modern-nav-subitem">Client Payment posting</a>
            <a href="{{ route('production.ford.request-for-quotation') }}" class="modern-nav-subitem">Request for Quotation</a>
        </div>
    </div>
    @endif

    {{-- Admin & Finance Sections --}}
    <!-- Accounting Dropdown -->
    @if($hasAcc)
    <div class="modern-nav-group {{ (request()->is('admin-finance/accounting*', 'admin-finance/cashier*', 'accounting/journal*') && !request()->is('admin-finance/accounting/expenses*', 'admin-finance/accounting/inventory-valuation*', 'admin-finance/accounting/journal*', 'accounting/journal*', 'admin-finance/accounting/production-costing*')) ? 'active' : '' }}">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="accounting">
            <div class="modern-nav-icon">
                <i class="las la-calculator"></i>
            </div>
            <span class="modern-nav-label">Accounting</span>
            <i class="modern-nav-arrow las la-chevron-right"></i>
        </a>
        <div class="modern-nav-submenu" data-submenu="accounting" style="padding-top: 5px; padding-bottom: 5px;">

            @if($user->hasPermission('admin_finance.accounting.sales_invoice') || $user->hasPermission('admin_finance.accounting'))
            <a href="{{ route('admin-finance.accounting.sales-invoice') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.sales-invoice') ? 'active' : '' }}">Sales Invoice</a>
            <a href="{{ route('admin-finance.accounting.delivery-receipt-list') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.delivery-receipt*') ? 'active' : '' }}">Delivery Receipts</a>
            <a href="{{ route('admin-finance.accounting.complimentary-receipt') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.complimentary-receipt*') ? 'active' : '' }}">Complimentary Receipt</a>
            @endif
            @if($user->hasPermission('admin_finance.accounting.check_voucher'))
            <a href="{{ route('admin-finance.check-voucher') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.check-voucher') ? 'active' : '' }}">Check Voucher</a>
            @endif
            @if($user->hasPermission('admin_finance.accounting.materials_requisition'))
            <a href="{{ route('admin-finance.accounting.materials-requisition') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.materials-requisition') ? 'active' : '' }}">Materials Requisition</a>
            @endif
            @if($user->hasPermission('admin_finance.accounting.material_requests'))
            <a href="{{ route('admin-finance.accounting.material-requests.incoming') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.material-requests.incoming') ? 'active' : '' }}">Material Requests</a>
            @endif
            @if($user->hasPermission('admin_finance.accounting.cash_advance_liquidation'))
            <a href="{{ route('admin-finance.accounting.expense-management') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.expense-management') ? 'active' : '' }}">Cash Advance Liquidation</a>
            @endif
            @if($user->hasPermission('admin_finance.accounting.cod_collections'))
            <a href="{{ route('cashier.collections.index') }}" class="modern-nav-subitem {{ request()->routeIs('cashier.collections.*') ? 'active' : '' }}">COD Collections Verification</a>
            @endif
            @if($user->hasPermission('admin_finance.accounting.cashier') || $user->hasPermission('admin_finance.accounting'))
            <a href="{{ route('admin-finance.accounting.cashier.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.cashier.*') ? 'active' : '' }}">Cashier Approvals</a>
            @endif
            @if($user->hasPermission('admin_finance.accounting.payment_posting') || $user->hasPermission('admin_finance.accounting'))
            <a href="{{ route('admin-finance.accounting.payment-posting.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.payment-posting.*') ? 'active' : '' }}">Payment Posting</a>
            @endif
            @if($user->hasPermission('admin_finance.accounting.auto_debit') || $user->hasPermission('admin_finance.accounting'))
            <a href="{{ route('admin-finance.accounting.auto-debits.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.auto-debits.*') ? 'active' : '' }}">Auto Debits</a>
            @endif
            @if($user->hasPermission('admin_finance.accounting'))
            <a href="{{ route('admin-finance.accounting.payment-requests') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.payment-requests') ? 'active' : '' }}">Payment Requests</a>
            <a href="{{ route('admin-finance.accounting.eford-payouts') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.eford-payouts*') ? 'active' : '' }}">E-FORD Payouts</a>
            <a href="{{ route('admin-finance.accounting.ecom-payouts.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.ecom-payouts*') ? 'active' : '' }}">E-com Payouts</a>
            @endif
            @if($user->hasPermission('admin_finance.accounting.office_supplies') || $user->hasPermission('admin_finance.accounting'))
            <a href="{{ route('admin-finance.accounting.office-supplies.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.office-supplies.*') ? 'active' : '' }}">Office Supplies</a>
            @endif

        </div>
    </div>
    @endif

    <!-- Finance Dropdown -->
    @if($hasPettyCash || $hasFreightVoucher)
    <div class="modern-nav-group {{ request()->is('admin-finance/petty-cash*', 'admin-finance/freight-voucher*') ? 'active' : '' }}">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="finance">
            <div class="modern-nav-icon">
                <i class="las la-wallet"></i>
            </div>
            <span class="modern-nav-label">Finance</span>
            <i class="modern-nav-arrow las la-chevron-right"></i>
        </a>
        <div class="modern-nav-submenu" data-submenu="finance" style="padding-top: 5px; padding-bottom: 5px;">
            <a href="{{ route('admin-finance.petty-cash.index', ['sidebar' => 'unified']) }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.petty-cash.*') ? 'active' : '' }}">Petty Cash Voucher</a>
            <a href="{{ route('admin-finance.freight-voucher.index', ['sidebar' => 'unified']) }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.freight-voucher.*') ? 'active' : '' }}">Freight Voucher</a>
        </div>
    </div>
    @endif

    <!-- Accounting Reports Dropdown -->
    @if($hasChartOfAccounts)
    <div class="modern-nav-group {{ request()->is('admin-finance/financial-reports*', 'admin-finance/chart-of-accounts*', 'admin-finance/sales-management*', 'admin-finance/accounts-receivable*', 'admin-finance/accounts-payable*', 'admin-finance/investments*', 'admin-finance/donations*', 'admin-finance/budgeting*', 'admin-finance/cash-management*', 'production/inventory/overview*', 'admin-finance/accounting/expenses*', 'admin-finance/inventory-valuation*', 'admin-finance/accounting/journal*', 'admin-finance/general-ledger*', 'admin-finance/procurement*', 'admin-finance/accounting/office-supplies*', 'admin-finance/gsd/asset-management*', 'admin-finance/sales-returns*', 'admin-finance/purchase-returns*', 'admin-finance/accounting/production-costing*') ? 'active' : '' }}">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="accounting-reports">
            <div class="modern-nav-icon">
                <i class="las la-chart-bar"></i>
            </div>
            <span class="modern-nav-label">Accounting Reports</span>
            <i class="modern-nav-arrow las la-chevron-right"></i>
        </a>
        <div class="modern-nav-submenu" data-submenu="accounting-reports" style="padding-top: 5px; padding-bottom: 5px;">
            @if($user->hasPermission('admin_finance.accounting.general_journal'))
            <a href="{{ route('accounting.journal.index') }}" class="modern-nav-subitem {{ request()->routeIs('accounting.journal.*') ? 'active' : '' }}">General Journal</a>
            @endif
            @if($hasChartOfAccounts)
            <a href="{{ route('admin-finance.accounting.accounts-receivable') }}" class="modern-nav-subitem {{ request()->is('admin-finance/accounts-receivable*') ? 'active' : '' }}">Accounts Receivable</a>
            @endif
            @if($hasChartOfAccounts)
            <a href="{{ route('admin-finance.accounting.accounts-payable') }}" class="modern-nav-subitem {{ request()->is('admin-finance/accounts-payable*') ? 'active' : '' }}">Payables & Supplier</a>
            @endif
            @if($user->hasPermission('admin_finance.accounting.cash_advance_liquidation'))
            <a href="{{ route('admin-finance.accounting.expenses.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.expenses.*') ? 'active' : '' }}">Expense Management</a>
            @endif
            @if($user->hasPermission('admin_finance.accounting') || $user->isSuperAdmin())
            <a href="{{ route('admin-finance.accounting.inventory-valuation') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.inventory-valuation') ? 'active' : '' }}">Cost of Goods Sold / Inventory Accounting</a>
            @endif
            @if($user->hasPermission('admin_finance.accounting') || $user->isSuperAdmin())
            <a href="{{ route('admin-finance.accounting.production-costing') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.production-costing') ? 'active' : '' }}">Production Costing</a>
            @endif
            @if($user->hasPermission('admin_finance.accounting.general_journal'))
            <a href="{{ route('admin-finance.accounting.general-ledger') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.general-ledger') ? 'active' : '' }}">General Ledger</a>
            @endif
            @if($hasChartOfAccounts)
            <a href="{{ route('admin-finance.accounting.chart-of-accounts', ['tab' => 'assets']) }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.chart-of-accounts') ? 'active' : '' }}">Chart of Accounts</a>
            @endif
            @if($user->hasPermission('admin_finance.accounting.materials_requisition'))
            <a href="{{ route('admin-finance.accounting.procurement') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.procurement') ? 'active' : '' }}">Purchasing / Procurement</a>
            @endif
            @if($hasChartOfAccounts)
            <a href="{{ route('admin-finance.accounting.sales-returns.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.sales-returns.*') ? 'active' : '' }}">Sales Return</a>
            @endif
            @if($hasChartOfAccounts)
            <a href="{{ route('admin-finance.accounting.purchase-returns.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.purchase-returns.*') ? 'active' : '' }}">Purchase Return</a>
            @endif
            @if($hasChartOfAccounts)
            <a href="{{ route('admin-finance.cash-management.index') }}" class="modern-nav-subitem {{ request()->is('admin-finance/cash-management*') ? 'active' : '' }}">Cash Management</a>
            @endif
            @if($user->hasPermission('admin_finance.gsd') || $user->isSuperAdmin())
            <a href="{{ route('admin-finance.gsd.asset-management') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.gsd.asset-management') ? 'active' : '' }}">Fixed Assets</a>
            @endif
            @if($hasChartOfAccounts)
            <a href="{{ route('admin-finance.financial-reports.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.financial-reports.*') ? 'active' : '' }}">Financial Reports</a>
            @endif
            @if($hasChartOfAccounts)
            <a href="{{ route('admin-finance.accounting.sales-management', ['tab' => 'bookstore']) }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.sales-management') ? 'active' : '' }}">Sales Channel Accounting</a>
            @endif
            @if($hasChartOfAccounts)
            <a href="{{ route('admin-finance.budgeting.index') }}" class="modern-nav-subitem {{ request()->is('admin-finance/budgeting*') ? 'active' : '' }}">Budgeting</a>
            @endif
            @if($hasChartOfAccounts)
            <a href="{{ route('admin-finance.donations.index') }}" class="modern-nav-subitem {{ request()->is('admin-finance/donations*') ? 'active' : '' }}">Donations</a>
            @endif
            @if($hasChartOfAccounts)
            <a href="{{ route('admin-finance.investments.index') }}" class="modern-nav-subitem {{ request()->is('admin-finance/investments*') ? 'active' : '' }}">Investments</a>
            @endif
            @if($user->hasPermission('admin_finance.accounting.office_supplies') || $user->hasPermission('admin_finance.accounting'))
            <a href="{{ route('admin-finance.accounting.office-supplies.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.office-supplies.*') ? 'active' : '' }}">Office Supplies</a>
            @endif
        </div>
    </div>
    @endif

    @if($hasCC)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-money-bill-wave"></i></div><span class="modern-nav-label">Credit and Collection</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('admin-finance.credit-collection.billing') }}" class="modern-nav-subitem">Billing</a>
            <a href="{{ route('admin-finance.credit-collection.reconsignment.index') }}" class="modern-nav-subitem">Reconsignments</a>
            <a href="{{ route('admin-finance.credit-collection.reports') }}" class="modern-nav-subitem">Reports</a>
            <a href="{{ route('admin-finance.credit-collection.invoice') }}" class="modern-nav-subitem">Invoice</a>
        </div>
    </div>
    @endif

    @if($hasGSD)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-tools"></i></div><span class="modern-nav-label">GSD</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('admin-finance.gsd.asset-management') }}" class="modern-nav-subitem">Asset Management</a>
            <a href="{{ route('admin-finance.gsd.job-orders') }}" class="modern-nav-subitem">Job Orders</a>
        </div>
    </div>
    @endif

    @if($hasMIS)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-server"></i></div><span class="modern-nav-label">MIS</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu"><a href="{{ route('admin-finance.mis.job-orders') }}" class="modern-nav-subitem">Job Orders</a></div>
    </div>
    @endif

    @if($hasHR)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-user-tie"></i></div><span class="modern-nav-label">HR</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu"><a href="{{ route('admin-finance.hr.job-orders') }}" class="modern-nav-subitem">Job Orders</a></div>
    </div>
    @endif

    @if($hasServiceRequestsAF)
    <a href="{{ route('admin-finance.service-requests.create') }}" class="modern-nav-item">
        <div class="modern-nav-icon"><i class="las la-tools"></i></div>
        <span class="modern-nav-label">Create Service Request</span>
    </a>
    @endif

</nav>
