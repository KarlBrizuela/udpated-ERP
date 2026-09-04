@php
    $user = auth()->user();
    $isSuperAdmin = $user->isSuperAdmin();
    
    // Check permissions for specific modules
    $hasDashboard = $user->hasPermission('marketing.dashboard');
    $hasCustomers = $user->hasPermission('marketing.customers');
    $hasAreaSales = $user->hasPermission('marketing.area_sales') || $user->hasAnyPermission(['marketing.area_sales.sales_orders', 'marketing.area_sales.freight_quotations', 'marketing.area_sales.direct_invoice_website', 'marketing.area_sales.direct_invoice_ecom', 'marketing.area_sales.acknowledgement_receipt', 'marketing.area_sales.credit_memo', 'marketing.area_sales.proof_of_payment']);
    $hasDirectSales = $user->hasPermission('marketing.direct_sales') || $user->hasAnyPermission(['marketing.direct_sales.pos', 'marketing.direct_sales.products', 'marketing.direct_sales.nbs_import']);
    $hasAdsAndPromo = $user->hasPermission('marketing.ads_promo') || $user->hasAnyPermission(['marketing.ads_promo.marketing_plan', 'marketing.ads_promo.sponsors']);
    $hasEcom = $user->hasPermission('marketing.ecom') || $user->hasPermission('marketing.ecom.pos');
    $hasBookMgmt = $user->hasPermission('marketing.book_mgmt') || $user->hasAnyPermission(['marketing.book_mgmt.book_list', 'marketing.book_mgmt.consignment']);
    $hasSupplierMgmt = $user->hasPermission('marketing.supplier_mgmt');
    $hasPettyCashVoucher = $user->hasPermission('admin_finance.petty_cash_voucher');
    $hasFreightVoucher = $user->hasPermission('admin_finance.freight_voucher');
    $hasApprovalQueue = $user->hasPermission('marketing.approval_queue');
    $hasMyRequests = $user->hasPermission('marketing.my_requests');
@endphp

<nav class="modern-nav-menu">
	@if($hasDashboard)
	<a href="{{ route('marketing.dashboard') }}" class="modern-nav-item {{ request()->routeIs('marketing.dashboard') ? 'active' : '' }}" data-page="dashboard">
		<div class="modern-nav-icon">
			<i class="flaticon-381-home-2"></i>
		</div>
		<span class="modern-nav-label">Dashboard</span>
	</a>
    @endif

    @if($hasMyRequests)
    <a href="{{ route('marketing.my-requests') }}" class="modern-nav-item {{ request()->routeIs('marketing.my-requests') ? 'active' : '' }}">
        <div class="modern-nav-icon">
            <i class="las la-envelope-open-text"></i>
        </div>
        <span class="modern-nav-label">My Requests</span>
    </a>
    @endif

    @if($hasApprovalQueue)
    <a href="{{ route('marketing.approval-queue') }}" class="modern-nav-item {{ request()->routeIs('marketing.approval-queue') ? 'active' : '' }}">
        <div class="modern-nav-icon">
            <i class="las la-clipboard-check"></i>
        </div>
        <span class="modern-nav-label">Approval Queue</span>
    </a>
    @endif
	
	<!-- Customer Management -->
	@if($hasCustomers)
	<a href="{{ route('marketing.customers') }}" class="modern-nav-item {{ request()->routeIs('marketing.customers') ? 'active' : '' }}" data-page="customer-management">
		<div class="modern-nav-icon">
			<i class="las la-users"></i>
		</div>
		<span class="modern-nav-label">Customer Management</span>
	</a>
	@endif

	<!-- Company Management -->
	@if($hasCustomers)
	<a href="{{ route('marketing.companies') }}" class="modern-nav-item {{ request()->routeIs('marketing.companies') ? 'active' : '' }}" data-page="company-management">
		<div class="modern-nav-icon">
			<i class="las la-building"></i>
		</div>
		<span class="modern-nav-label">Company Management</span>
	</a>
	@endif

	<!-- Area Sales -->
	@if($hasAreaSales)
	<div class="modern-nav-group {{ request()->is('marketing/sales-orders*', 'marketing/sales-order*', 'marketing/direct-invoice*', 'marketing/acknowledgement-receipt*', 'marketing/credit-memo*', 'marketing/proof-of-payment*', 'marketing/freight-quotations*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="area-sales">
			<div class="modern-nav-icon">
				<i class="las la-map-marked-alt"></i>
			</div>
			<span class="modern-nav-label">Area Sales</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu">
			@if($user->hasPermission('marketing.area_sales.sales_orders'))
			<a href="{{ route('marketing.sales-orders.list') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.sales-orders.list', 'marketing.sales-orders.create') ? 'active' : '' }}">Sales Orders List</a>
			@endif

			@if($user->hasPermission('marketing.area_sales.freight_quotations'))
			<a href="{{ route('marketing.freight-quotations.list') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.freight-quotations.*') ? 'active' : '' }}">Freight Quotations</a>
			@endif

			@if($user->hasPermission('marketing.area_sales.direct_invoice_website'))
			<a href="{{ route('marketing.direct-invoice.website') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.direct-invoice.website') ? 'active' : '' }}">Direct Invoice (Website)</a>
			@endif
			@if($user->hasPermission('marketing.area_sales.direct_invoice_ecom'))
			<a href="{{ route('marketing.direct-invoice.ecom') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.direct-invoice.ecom') ? 'active' : '' }}">Direct Invoice (E-com)</a>
			@endif
			@if($user->hasPermission('marketing.area_sales.acknowledgement_receipt'))
			<a href="{{ route('marketing.acknowledgement-receipt') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.acknowledgement-receipt') ? 'active' : '' }}">Acknowledgement Receipt</a>
			@endif
			@if($user->hasPermission('marketing.area_sales.credit_memo'))
			<a href="{{ route('marketing.credit-memo') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.credit-memo') ? 'active' : '' }}">Credit Memo Form</a>
			@endif
			@if($user->hasPermission('marketing.area_sales.proof_of_payment'))
			<a href="{{ route('marketing.proof-of-payment') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.proof-of-payment') ? 'active' : '' }}">Proof of Payment</a>
			@endif
			<a href="{{ route('marketing.area-sales.team-stocks.index') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.area-sales.team-stocks.*') ? 'active' : '' }}">Team Stocks</a>
			<!-- Sales Reports and Territory Management removed -->
		</div>
	</div>
	@endif

	<!-- Direct Sales -->
	@if($hasDirectSales)
	<div class="modern-nav-group {{ request()->is('marketing/pos-sale*', 'marketing/pos-products*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="direct-sales">
			<div class="modern-nav-icon">
				<i class="las la-store"></i>
			</div>
			<span class="modern-nav-label">Direct Sales</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu">
			@if($user->hasPermission('marketing.direct_sales.pos'))
			<a href="{{ route('marketing.pos.sale') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.pos.sale') ? 'active' : '' }}">POS System</a>
			@endif
			@if($user->hasPermission('marketing.direct_sales.products'))
			<a href="{{ route('marketing.pos.products') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.pos.products') ? 'active' : '' }}">POS Products</a>
			@endif
			@if($user->hasPermission('marketing.direct_sales.nbs_import'))
			<a href="{{ route('marketing.nbs-import.index') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.nbs-import.index') ? 'active' : '' }}">NBS PO Import</a>
			@endif
		</div>
	</div>
	@endif

	<!-- Ads and Promo -->
	@if($hasAdsAndPromo)
	<div class="modern-nav-group {{ request()->is('marketing/ads-promo*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="ads-promo">
			<div class="modern-nav-icon">
				<i class="las la-ad"></i>
			</div>
			<span class="modern-nav-label">Ads and Promo</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu">
			<!-- Campaigns and Promotions removed -->
			@if($user->hasPermission('marketing.ads_promo.marketing_plan'))
			<a href="{{ route('marketing.ads-promo.crpr') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.ads-promo.crpr') ? 'active' : '' }}">Marketing Plan Itinerary Budget</a>
			@endif
			@if($user->hasPermission('marketing.ads_promo.sponsors'))
			<a href="{{ route('marketing.ads-promo.sponsors') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.ads-promo.sponsors') ? 'active' : '' }}">List of Sponsors</a>
			@endif
		</div>
	</div>
	@endif

	<!-- MIBF POS -->
	@if($hasEcom)
	<div class="modern-nav-group {{ request()->is('marketing/ecom-pos*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="ecom">
			<div class="modern-nav-icon">
				<i class="las la-shopping-cart"></i>
			</div>
			<span class="modern-nav-label">MIBF POS</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu">
			@if($user->hasPermission('marketing.ecom.pos'))
			<a href="{{ route('marketing.ecom.pos') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.ecom.pos') ? 'active' : '' }}">MIBF POS</a>
			@endif
		</div>
	</div>
	@endif

	<!-- Book Management (Master) -->
	@if($hasBookMgmt)
	<div class="modern-nav-group {{ request()->is('marketing/book-list*', 'marketing/non-books*', 'marketing/consignment*', 'marketing/book-indices*', 'marketing/book-bundles*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="book-management">
			<div class="modern-nav-icon">
				<i class="las la-book"></i>
			</div>
			<span class="modern-nav-label">Book Management</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu">
			@if($user->hasPermission('marketing.book_mgmt.book_list'))
			<a href="{{ route('marketing.products') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.products') ? 'active' : '' }}">Book List (Master)</a>
			<a href="{{ route('marketing.non-books') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.non-books') ? 'active' : '' }}">Non-Books</a>
			<a href="{{ route('marketing.indices') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.indices') ? 'active' : '' }}">Book Index</a>
			<a href="{{ route('marketing.bundles') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.bundles') ? 'active' : '' }}">Book Bundles</a>
			@endif
			@if($user->hasPermission('marketing.book_mgmt.consignment'))
			<a href="{{ route('marketing.consignment.index') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.consignment.index') ? 'active' : '' }}">Consignment Management</a>
			@endif
		</div>
	</div>
	@endif

	<!-- Supplier Management -->
	@if($hasSupplierMgmt)
	<a href="{{ route('marketing.suppliers') }}" class="modern-nav-item {{ request()->routeIs('marketing.suppliers') ? 'active' : '' }}" data-page="supplier">
		<div class="modern-nav-icon">
			<i class="las la-truck-loading"></i>
		</div>
		<span class="modern-nav-label">Supplier Management</span>
	</a>
	@endif

	<!-- Vendor Management -->
	<a href="{{ route('vendor-management.index') }}" class="modern-nav-item {{ request()->routeIs('vendor-management.*') ? 'active' : '' }}" data-page="vendor-management">
		<div class="modern-nav-icon">
			<i class="las la-store"></i>
		</div>
		<span class="modern-nav-label">Vendor Management</span>
	</a>

	<!-- Finance -->
    @if($hasPettyCashVoucher || $hasFreightVoucher)
	<div class="modern-nav-group {{ request()->is('admin-finance/petty-cash*', 'admin-finance/freight-voucher*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="finance">
			<div class="modern-nav-icon">
				<i class="las la-calculator"></i>
			</div>
			<span class="modern-nav-label">Finance</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="finance">
			@if($hasPettyCashVoucher)
			<a href="{{ route('admin-finance.petty-cash.index', ['sidebar' => 'marketing']) }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.petty-cash.*') ? 'active' : '' }}">Petty Cash Voucher</a>
			@endif
			@if($hasFreightVoucher)
			<a href="{{ route('admin-finance.freight-voucher.index', ['sidebar' => 'marketing']) }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.freight-voucher.*') ? 'active' : '' }}">Freight Voucher</a>
			@endif
		</div>
	</div>
	@endif

</nav>

@push('scripts')
<script>
    // Sidebar behavior is now centrally managed in app.blade.php
</script>
@endpush

