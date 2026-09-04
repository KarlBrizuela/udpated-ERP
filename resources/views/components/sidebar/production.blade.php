@php
    $user = auth()->user();
    $isSuperAdmin = $user->isSuperAdmin();
    
    // Check permissions for specific modules
    $hasDashboard = $user->hasPermission('production.dashboard');
    $hasInventory = $user->hasPermission('production.inventory') || $user->hasAnyPermission(['production.inventory.overview', 'production.inventory.add_stock', 'production.inventory.received']);
    $hasLogistics = $user->hasPermission('production.logistic') || $user->hasAnyPermission(['production.logistic.pick_lists', 'production.logistic.packing', 'production.logistic.delivery_scheduling', 'production.logistic.delivery_receipts', 'production.logistic.driver_dashboard', 'production.logistic.delivery_tracking', 'production.logistic.purchase_orders', 'production.logistic.receiving_reports', 'production.logistic.freight_quotation_review', 'production.logistic.rider_collections', 'production.logistic.acknowledgement_receipt']);
    $hasDTO = $user->hasPermission('production.dto') || $user->hasPermission('production.dto.job_request_form');
    $hasFORD = $user->hasPermission('production.ford') || $user->hasAnyPermission(['production.ford.auto_debit', 'production.ford.client_payment_posting', 'production.ford.eford_payout', 'production.ford.payment_request', 'production.ford.purchase_order', 'production.ford.request_for_quotation', 'production.ford.sales_order', 'production.ford.transmittal']);
    $hasPrinting = $user->hasPermission('production.printing') || $user->hasPermission('production.printing.request_payment_to_printer');
    $hasPettyCashVoucher = $user->hasPermission('admin_finance.petty_cash_voucher');
    $hasFreightVoucher = $user->hasPermission('admin_finance.freight_voucher');
    $hasApprovalQueue = $user->hasPermission('production.approval_queue');
    $hasMyRequests = $user->hasPermission('production.my_requests');
@endphp

<nav class="modern-nav-menu">
	@if($hasDashboard)
	<a href="{{ route('production.dashboard') }}" class="modern-nav-item {{ request()->routeIs('production.dashboard') ? 'active' : '' }}" data-page="dashboard">
		<div class="modern-nav-icon">
			<i class="flaticon-381-home-2"></i>
		</div>
		<span class="modern-nav-label">Dashboard</span>
	</a>
	<a href="{{ route('production.executive-dashboard.index') }}" class="modern-nav-item {{ request()->is('production/executive-dashboard*') ? 'active' : '' }}">
		<div class="modern-nav-icon">
			<i class="las la-chart-bar"></i>
		</div>
		<span class="modern-nav-label">Executive Dashboard</span>
	</a>
    @endif
    
    @if($hasMyRequests)
    <a href="{{ route('production.my-requests') }}" class="modern-nav-item {{ request()->routeIs('production.my-requests') ? 'active' : '' }}">
        <div class="modern-nav-icon">
            <i class="las la-envelope-open-text"></i>
        </div>
        <span class="modern-nav-label">My Requests</span>
    </a>
    @endif

    @if($hasApprovalQueue)
    <a href="{{ route('production.approval-queue') }}" class="modern-nav-item {{ request()->routeIs('production.approval-queue') ? 'active' : '' }}">
        <div class="modern-nav-icon">
            <i class="las la-clipboard-check"></i>
        </div>
        <span class="modern-nav-label">Approval Queue</span>
    </a>
    @endif
	
	<!-- Master Inventory -->
	@if($hasInventory)
	<a href="{{ route('production.inventory.master') }}" class="modern-nav-item {{ request()->routeIs('production.inventory.master') ? 'active' : '' }}">
		<div class="modern-nav-icon">
			<i class="las la-boxes"></i>
		</div>
		<span class="modern-nav-label">Master Inventory</span>
	</a>
	@endif

	<!-- Inventory Management -->
	@if($hasInventory)
	<div class="modern-nav-group {{ (request()->is('production/inventory*') && !request()->routeIs('production.inventory.master')) ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="inventory">
			<div class="modern-nav-icon">
				<i class="las la-warehouse"></i>
			</div>
			<span class="modern-nav-label">Inventory Management</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="inventory">
			@if($user->hasPermission('production.inventory.overview'))
			<a href="{{ route('production.inventory.overview') }}" class="modern-nav-subitem {{ request()->routeIs('production.inventory.overview') ? 'active' : '' }}">Inventory Overview</a>
			@endif
			@if($user->hasPermission('production.inventory.add_stock'))
			<a href="{{ route('production.inventory.add-stock') }}" class="modern-nav-subitem {{ request()->routeIs('production.inventory.add-stock') ? 'active' : '' }}">Add Stock</a>
			@endif
			@if($user->hasPermission('production.inventory.received'))
			<a href="{{ route('production.inventory.received') }}" class="modern-nav-subitem {{ request()->routeIs('production.inventory.received') ? 'active' : '' }}">Received Items</a>
			@endif
		</div>
	</div>
	@endif

	<!-- Production Costing -->
	<div class="modern-nav-group {{ request()->is('production/costing*') ? 'active' : '' }}">
		<a href="{{ route('production.costing.index') }}" class="modern-nav-item">
			<div class="modern-nav-icon">
				<i class="las la-calculator"></i>
			</div>
			<span class="modern-nav-label">Production Costing</span>
		</a>
	</div>

	<!-- Fixed Assets -->
	<div class="modern-nav-group {{ request()->is('production/assets*') ? 'active' : '' }}">
		<a href="{{ route('production.assets.index') }}" class="modern-nav-item">
			<div class="modern-nav-icon">
				<i class="las la-tools"></i>
			</div>
			<span class="modern-nav-label">Fixed Assets</span>
		</a>
	</div>

	<!-- Logistics -->
	@if($hasLogistics)
	<div class="modern-nav-group {{ request()->is('production/logistic*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="logistics">
			<div class="modern-nav-icon">
				<i class="las la-boxes"></i>
			</div>
			<span class="modern-nav-label">Logistics</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="logistics">
			@if($user->hasPermission('production.logistic.pick_lists'))
			<a href="{{ route('production.logistic.pick-list-list') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.pick-list-list') ? 'active' : '' }}">Pick Lists</a>
			@endif
			@if($user->hasPermission('production.logistic.packing'))
			<a href="{{ route('production.logistic.packing-management') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.packing-management') ? 'active' : '' }}">Packing</a>
			@endif
			@if($user->hasPermission('production.logistic.delivery_scheduling') || $user->isSuperAdmin())
			<a href="{{ route('production.logistic.pickup-requests.index') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.pickup-requests.*') ? 'active' : '' }}">Logistics Service Order</a>
			@endif
			@if($user->hasPermission('production.logistic.delivery_scheduling'))
			<a href="{{ route('production.logistic.delivery-scheduling') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.delivery-scheduling') ? 'active' : '' }}">Delivery Scheduling</a>
			@endif
			@if($user->hasPermission('production.logistic.delivery_receipts'))
			<a href="{{ route('production.logistic.delivery-receipt-list') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.delivery-receipt-list') ? 'active' : '' }}">Delivery Receipts</a>
			@endif
			@if($user->hasPermission('production.logistic.driver_dashboard'))
			<a href="{{ route('production.logistic.driver-dashboard') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.driver-dashboard') ? 'active' : '' }}">Driver Dashboard</a>
			@endif
			@if($user->hasPermission('production.logistic.delivery_tracking'))
			<a href="{{ route('production.logistic.delivery-tracking') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.delivery-tracking') ? 'active' : '' }}">Delivery Tracking</a>
			@endif
			@if($user->hasPermission('production.logistic.purchase_orders'))
			<a href="{{ route('production.logistic.purchase-order-list') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.purchase-order-list') ? 'active' : '' }}">Purchase Orders</a>
			@endif
			@if($user->hasPermission('production.logistic.receiving_reports'))
			<a href="{{ route('production.logistic.receiving-report-list') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.receiving-report-list') ? 'active' : '' }}">Receiving Reports</a>
			@endif
			@if($user->hasPermission('production.logistic.freight_quotation_review'))
			<a href="{{ route('production.logistic.pending-freight-quotations') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.pending-freight-quotations') ? 'active' : '' }}">
				<i class="bi bi-inbox me-1"></i>Freight Quotation (Review)
			</a>
			@endif
			@if($user->hasPermission('production.logistic.rider_collections'))
			<a href="{{ route('rider.collections.index') }}" class="modern-nav-subitem {{ request()->routeIs('rider.collections.*') ? 'active' : '' }}">Rider Collections</a>
			@endif
			@if($user->hasPermission('production.logistic.area_consignment') || $user->hasPermission('production.logistic.acknowledgement_receipt') || $user->isSuperAdmin())
			<a href="{{ route('production.logistic.area-consignment') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.area-consignment') ? 'active' : '' }}">
				<i class="las la-boxes me-1"></i>Consignment Receipt
			</a>
			@endif
			@if($user->hasPermission('production.logistic.acknowledgement_receipt'))
			<a href="{{ route('production.logistic.acknowledgement-receipt') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.acknowledgement-receipt') ? 'active' : '' }}">
				<i class="las la-file-import me-1"></i>Acknowledgement Receipt
			</a>
			@endif
		</div>
	</div>
	@endif

	<!-- DTO -->
	@if($hasDTO)
	<div class="modern-nav-group {{ request()->is('production/dto*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="dto">
			<div class="modern-nav-icon">
				<i class="las la-truck"></i>
			</div>
			<span class="modern-nav-label">DTO</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="dto">
			@if($user->hasPermission('production.dto.job_request_form'))
			<a href="{{ route('production.dto.job-request-form') }}" class="modern-nav-subitem {{ request()->routeIs('production.dto.job-request-form') ? 'active' : '' }}">Job Request Form</a>
			@endif
		</div>
	</div>
	@endif

	<!-- FORD -->
	@if($hasFORD)
	<div class="modern-nav-group {{ request()->is('production/ford*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="ford">
			<div class="modern-nav-icon">
				<i class="las la-warehouse"></i>
			</div>
			<span class="modern-nav-label">FORD</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="ford">
			@if($user->hasPermission('production.ford.auto_debit'))
			<a href="{{ route('production.ford.auto-debit') }}" class="modern-nav-subitem {{ request()->routeIs('production.ford.auto-debit') ? 'active' : '' }}">Auto Debit</a>
			@endif
			@if($user->hasPermission('production.ford.client_payment_posting'))
			<a href="{{ route('production.ford.client-payment-posting') }}" class="modern-nav-subitem {{ request()->routeIs('production.ford.client-payment-posting') ? 'active' : '' }}">Client Payment for posting</a>
			@endif
			@if($user->hasPermission('production.ford.eford_payout'))
			<a href="{{ route('production.ford.eford-payout') }}" class="modern-nav-subitem {{ request()->routeIs('production.ford.eford-payout') ? 'active' : '' }}">E-FORD Payout</a>
			@endif
			@if($user->hasPermission('production.ford.payment_request'))
			<a href="{{ route('production.ford.payment-request') }}" class="modern-nav-subitem {{ request()->routeIs('production.ford.payment-request') ? 'active' : '' }}">Payment Request</a>
			@endif
			@if($user->hasPermission('production.ford.purchase_order'))
			<a href="{{ route('production.ford.purchase-order') }}" class="modern-nav-subitem {{ request()->routeIs('production.ford.purchase-order') ? 'active' : '' }}">Purchase Order</a>
			@endif
			@if($user->hasPermission('production.ford.request_for_quotation'))
			<a href="{{ route('production.ford.request-for-quotation') }}" class="modern-nav-subitem {{ request()->routeIs('production.ford.request-for-quotation') ? 'active' : '' }}">Request for Quotation</a>
			@endif
			<a href="{{ route('production.ford.freight-quotation.index') }}" class="modern-nav-subitem {{ request()->routeIs('production.ford.freight-quotation.*') ? 'active' : '' }}">Freight Quotation</a>
			@if($user->hasPermission('production.ford.sales_order'))
			<a href="{{ route('production.ford.sales-order') }}" class="modern-nav-subitem {{ request()->routeIs('production.ford.sales-order') ? 'active' : '' }}">Sales Order</a>
			@endif
			@if($user->hasPermission('production.ford.transmittal'))
			<a href="{{ route('production.ford.transmittal') }}" class="modern-nav-subitem {{ request()->routeIs('production.ford.transmittal') ? 'active' : '' }}">Transmittal</a>
			@endif
		</div>
	</div>
	@endif

	<!-- Printing Services -->
	@if($hasPrinting)
	<div class="modern-nav-group {{ request()->is('production/printing*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="printing">
			<div class="modern-nav-icon">
				<i class="las la-print"></i>
			</div>
			<span class="modern-nav-label">Printing Services</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="printing">
			@if($user->hasPermission('production.printing.request_payment_to_printer'))
			<a href="{{ route('production.printing.request-payment-to-printer') }}" class="modern-nav-subitem {{ request()->routeIs('production.printing.request-payment-to-printer') ? 'active' : '' }}">Request Payment to Printer</a>
			@endif
		</div>
	</div>
	@endif

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
			<a href="{{ route('admin-finance.petty-cash.index', ['sidebar' => 'production']) }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.petty-cash.*') ? 'active' : '' }}">Petty Cash Voucher</a>
			@endif
			@if($hasFreightVoucher)
			<a href="{{ route('admin-finance.freight-voucher.index', ['sidebar' => 'production']) }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.freight-voucher.*') ? 'active' : '' }}">Freight Voucher</a>
			@endif
		</div>
	</div>
	@endif
</nav>
