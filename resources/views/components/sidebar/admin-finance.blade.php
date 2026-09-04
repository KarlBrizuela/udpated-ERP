@php
    $user = auth()->user();
    $isSuperAdmin = $user->isSuperAdmin();
    
    // Check permissions for specific modules
    $hasDashboard = $user->hasPermission('admin_finance.dashboard');
    $hasMIS = $user->hasPermission('admin_finance.mis') || $user->hasPermission('admin_finance.mis.job_orders');
    $hasGSD = $user->hasPermission('admin_finance.gsd') || $user->hasAnyPermission(['admin_finance.gsd.asset_management', 'admin_finance.gsd.job_orders']);
    $hasCreditCollection = $user->hasPermission('admin_finance.credit_collection') || $user->hasAnyPermission(['admin_finance.credit_collection.billing', 'admin_finance.credit_collection.reports', 'admin_finance.credit_collection.invoice']);
    $hasAccounting = $user->hasPermission('admin_finance.accounting') || $user->hasAnyPermission(['admin_finance.accounting.general_journal', 'admin_finance.accounting.sales_invoice', 'admin_finance.accounting.check_voucher', 'admin_finance.accounting.materials_requisition', 'admin_finance.accounting.material_requests', 'admin_finance.accounting.cash_advance_liquidation', 'admin_finance.accounting.cod_collections', 'admin_finance.accounting.office_supplies', 'admin_finance.accounting.expenses']);
    $hasPettyCashVoucher = $user->hasPermission('admin_finance.petty_cash_voucher');
    $hasHR = $user->hasPermission('admin_finance.hr') || $user->hasPermission('admin_finance.hr.job_orders');
    $hasApprovalQueue = $user->hasPermission('admin_finance.approval_queue');
    $hasMyRequests = $user->hasPermission('admin_finance.my_requests');
    $hasServiceRequests = $user->hasPermission('admin_finance.service_requests');
    $hasFreightVoucher = $user->hasPermission('admin_finance.freight_voucher');
    $hasChartOfAccounts = $user->hasPermission('admin_finance.accounting.chart_of_accounts') || $user->hasPermission('admin_finance.accounting');
@endphp

@push('styles')
<style>
    .submenu-divider {
        height: 1px !important;
        background: rgba(0, 0, 0, 0.08) !important;
        margin: 10px 20px 10px 45px !important;
        display: block !important;
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
	@if($hasDashboard)
	<a href="{{ route('admin-finance.dashboard') }}" class="modern-nav-item {{ request()->routeIs('admin-finance.dashboard') ? 'active' : '' }}" data-page="dashboard">
		<div class="modern-nav-icon">
			<i class="flaticon-381-home-2"></i>
		</div>
		<span class="modern-nav-label">Dashboard</span>
	</a>
    @endif

    @if($hasMyRequests)
	<a href="{{ route('admin-finance.my-requests') }}" class="modern-nav-item {{ request()->routeIs('admin-finance.my-requests') ? 'active' : '' }}" data-page="my-requests">
		<div class="modern-nav-icon">
			<i class="las la-envelope-open-text"></i>
		</div>
		<span class="modern-nav-label">My Requests</span>
	</a>
    @endif

    @if($hasApprovalQueue)
	<a href="{{ route('admin-finance.approval-queue') }}" class="modern-nav-item {{ request()->routeIs('admin-finance.approval-queue') ? 'active' : '' }}" data-page="approval-queue">
		<div class="modern-nav-icon">
			<i class="las la-clipboard-check"></i>
		</div>
		<span class="modern-nav-label">Approval Queue</span>
	</a>
    @endif

	<!-- Accounting Dropdown -->
	@if($hasAccounting)
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
	@if($hasPettyCashVoucher || $hasFreightVoucher)
	<div class="modern-nav-group {{ request()->is('admin-finance/petty-cash*', 'admin-finance/freight-voucher*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="finance">
			<div class="modern-nav-icon">
				<i class="las la-wallet"></i>
			</div>
			<span class="modern-nav-label">Finance</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="finance" style="padding-top: 5px; padding-bottom: 5px;">
			@if($hasPettyCashVoucher)
			<a href="{{ route('admin-finance.petty-cash.index', ['sidebar' => 'admin-finance']) }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.petty-cash.*') ? 'active' : '' }}">Petty Cash Voucher</a>
			@endif
			@if($hasFreightVoucher)
			<a href="{{ route('admin-finance.freight-voucher.index', ['sidebar' => 'admin-finance']) }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.freight-voucher.*') ? 'active' : '' }}">Freight Voucher</a>
			@endif
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
			@if($user->hasPermission('admin_finance.accounting') || $isSuperAdmin)
			<a href="{{ route('admin-finance.accounting.inventory-valuation') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.inventory-valuation') ? 'active' : '' }}">Cost of Goods Sold / Inventory Accounting</a>
			@endif
			@if($user->hasPermission('admin_finance.accounting') || $isSuperAdmin)
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
			@if($user->hasPermission('admin_finance.gsd') || $isSuperAdmin)
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

	<!-- Credit and Collection -->
    @if($hasCreditCollection)
	<div class="modern-nav-group {{ request()->is('admin-finance/credit-collection*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="credit-collection">
			<div class="modern-nav-icon">
				<i class="las la-money-bill-wave"></i>
			</div>
			<span class="modern-nav-label">Credit and Collection</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="credit-collection">
			@if($user->hasPermission('admin_finance.credit_collection.billing'))
			<a href="{{ route('admin-finance.credit-collection.billing') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.credit-collection.billing') && !request()->has('tab') ? 'active' : '' }}">Billing</a>
			@endif
			@if($user->hasPermission('admin_finance.credit_collection.billing'))
			<a href="{{ route('admin-finance.credit-collection.reconsignment.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.credit-collection.reconsignment.index') ? 'active' : '' }}">Reconsignments</a>
			@endif
			@if($user->hasPermission('admin_finance.credit_collection.reports'))
			<a href="{{ route('admin-finance.credit-collection.reports') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.credit-collection.reports') ? 'active' : '' }}">Reports</a>
			@endif
			@if($user->hasPermission('admin_finance.credit_collection.invoice'))
			<a href="{{ route('admin-finance.credit-collection.invoice') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.credit-collection.invoice') ? 'active' : '' }}">Invoice</a>
			@endif
		</div>
	</div>
    @endif

	<!-- GSD -->
    @if($hasGSD)
	<div class="modern-nav-group {{ request()->is('admin-finance/gsd*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="gsd">
			<div class="modern-nav-icon">
				<i class="las la-tools"></i>
			</div>
			<span class="modern-nav-label">GSD</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="gsd">
			@if($user->hasPermission('admin_finance.gsd.asset_management'))
			<a href="{{ route('admin-finance.gsd.asset-management') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.gsd.asset-management') ? 'active' : '' }}">Asset Management</a>
			@endif
			@if($user->hasPermission('admin_finance.gsd.job_orders'))
			<a href="{{ route('admin-finance.gsd.job-orders') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.gsd.job-orders') ? 'active' : '' }}">Job Orders</a>
			@endif
		</div>
	</div>
    @endif

	<!-- HR -->
    @if($hasHR)
	<div class="modern-nav-group {{ request()->is('admin-finance/hr*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="hr">
			<div class="modern-nav-icon">
				<i class="las la-user-tie"></i>
			</div>
			<span class="modern-nav-label">HR</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="hr">
			@if($user->hasPermission('admin_finance.hr.job_orders'))
			<a href="{{ route('admin-finance.hr.job-orders') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.hr.job-orders') ? 'active' : '' }}">Job Orders</a>
			@endif
		</div>
	</div>
    @endif

	<!-- MIS -->
    @if($hasMIS)
	<div class="modern-nav-group {{ request()->is('admin-finance/mis*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="mis">
			<div class="modern-nav-icon">
				<i class="las la-server"></i>
			</div>
			<span class="modern-nav-label">MIS</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="mis">
			@if($user->hasPermission('admin_finance.mis.job_orders'))
			<a href="{{ route('admin-finance.mis.job-orders') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.mis.job-orders') ? 'active' : '' }}">Job Orders</a>
			@endif
		</div>
	</div>
    @endif

	<!-- Service Requests -->
	@if($hasServiceRequests)
	<a href="{{ route('admin-finance.service-requests.create') }}" class="modern-nav-item {{ request()->routeIs('admin-finance.service-requests.create') ? 'active' : '' }}" data-page="service-requests">
		<div class="modern-nav-icon">
			<i class="las la-tools"></i>
		</div>
		<span class="modern-nav-label">Create Service Request</span>
	</a>
	@endif

</nav>
