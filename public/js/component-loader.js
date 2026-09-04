/**
 * Centralized Component Loader
 * Loads nav-header, header, and sidebar components dynamically
 */

(function () {
	'use strict';

	// Page configuration mapping
	const pageConfig = {
		// Super Admin Pages
		'index': { division: 'super-admin', home: 'index.html', title: 'Super Admin Dashboard', role: 'Super Admin' },
		'super-admin-users': { division: 'super-admin', home: 'index.html', title: 'User Management', role: 'Super Admin' },
		'super-admin-roles': { division: 'super-admin', home: 'index.html', title: 'Roles & Permissions', role: 'Super Admin' },
		'super-admin-divisions': { division: 'super-admin', home: 'index.html', title: 'Divisions', role: 'Super Admin' },
		'super-admin-settings': { division: 'super-admin', home: 'index.html', title: 'System Settings', role: 'Super Admin' },

		// Marketing Division Pages
		'marketing-division-dashboard': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'Marketing Division Dashboard', role: 'Account Executive' },
		'customer-list': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'Customer Management', role: 'Account Executive' },
		'sales-order': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'Sales Order', role: 'Account Executive' },
		'sales-orders-list': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'Sales Orders List', role: 'Account Executive' },
		'direct-invoice-website': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'Direct Invoice (Website)', role: 'Account Executive' },
		'direct-invoice-ecom': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'Direct Invoice (E-com)', role: 'Account Executive' },
		'acknowledgement-receipt': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'Acknowledgement Receipt', role: 'Account Executive' },
		'credit-memo': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'Credit Memo Form', role: 'Account Executive' },
		'proof-of-payment': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'Proof of Payment', role: 'Account Executive' },
		'pos-sale': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'New Sale - Point of Sale', role: 'Cashier' },
		'pos-products': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'POS Products Management', role: 'Cashier' },
		'ecom-pos': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'E-Commerce POS', role: 'Account Executive' },
		'product-list': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'Books List', role: 'Account Executive' },
		'supplier-list': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'Supplier Management', role: 'Account Executive' },
		'purchase-orders': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'Purchase Orders', role: 'Account Executive' },
		'add-supplier': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'Add Supplier', role: 'Account Executive' },
		'app-profile': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'Profile', role: 'Account Executive' },

		// Admin & Finance Division Pages
		'admin-finance-dashboard': { division: 'admin-finance', home: 'admin-finance-dashboard.html', title: 'Admin & Finance Dashboard', role: 'Finance Manager' },
		'approval-queue': { division: 'admin-finance', home: 'admin-finance-dashboard.html', title: 'Approval Queue', role: 'Finance Manager' },
		'approval-detail': { division: 'admin-finance', home: 'admin-finance-dashboard.html', title: 'Approval Detail', role: 'Finance Manager' },
		'sales-invoice': { division: 'admin-finance', home: 'admin-finance-dashboard.html', title: 'Sales Invoice', role: 'Accounting' },
		'check-voucher': { division: 'admin-finance', home: 'admin-finance-dashboard.html', title: 'Check Voucher', role: 'Accounting' },
		'materials-supplies-requisition': { division: 'admin-finance', home: 'admin-finance-dashboard.html', title: 'Materials/Supplies Requisition', role: 'Accounting' },
		'gsd-asset-management': { division: 'admin-finance', home: 'admin-finance-dashboard.html', title: 'Asset Management', role: 'GSD Staff' },
		'gsd-maintenance-management': { division: 'admin-finance', home: 'admin-finance-dashboard.html', title: 'GSD Maintenance Management', role: 'GSD Staff' },
		'gsd-others': { division: 'admin-finance', home: 'admin-finance-dashboard.html', title: 'GSD Others', role: 'GSD Staff' },
		'job-orders': { division: 'admin-finance', home: 'admin-finance-dashboard.html', title: 'Job Orders', role: 'MIS Staff' },
		'service-request-form': { division: 'admin-finance', home: 'admin-finance-dashboard.html', title: 'Service Request Form (MIS)', role: 'MIS Staff' },
		'cctv-review-request': { division: 'admin-finance', home: 'admin-finance-dashboard.html', title: 'CCTV Review Request', role: 'MIS Staff' },
		'request-undertime': { division: 'admin-finance', home: 'admin-finance-dashboard.html', title: 'Request for Undertime', role: 'MIS Staff' },
		'qb-change-request': { division: 'admin-finance', home: 'admin-finance-dashboard.html', title: 'QB Change/Item Revision Request', role: 'MIS Staff' },
		'mis-material-request': { division: 'admin-finance', home: 'admin-finance-dashboard.html', title: 'Material Request Form', role: 'MIS Staff' },
		'cash-advance-liquidation': { division: 'admin-finance', home: 'admin-finance-dashboard.html', title: 'Cash Advance Liquidation Form', role: 'Finance Manager' },

		// Production Division Pages
		'production-division-dashboard': { division: 'production', home: 'production-division-dashboard.html', title: 'Production Division Dashboard', role: 'Logistics' },
		'inventory-overview': { division: 'production', home: 'production-division-dashboard.html', title: 'Inventory Overview', role: 'Logistics' },
		'add-stock': { division: 'production', home: 'production-division-dashboard.html', title: 'Add Stock', role: 'Logistics' },
		'received-items': { division: 'production', home: 'production-division-dashboard.html', title: 'Received Items', role: 'Logistics' },
		'pick-list-management': { division: 'production', home: 'production-division-dashboard.html', title: 'Pick List Management', role: 'Logistics' },
		'pick-list-list': { division: 'production', home: 'production-division-dashboard.html', title: 'Pick Lists', role: 'Logistics' },
		'delivery-receipt': { division: 'production', home: 'production-division-dashboard.html', title: 'Delivery Receipt', role: 'Logistics' },
		'delivery-receipt-list': { division: 'production', home: 'production-division-dashboard.html', title: 'Delivery Receipts', role: 'Logistics' },
		'order-fulfillment': { division: 'production', home: 'production-division-dashboard.html', title: 'Order Fulfillment', role: 'Logistics' },
		'packing-scheduling': { division: 'production', home: 'production-division-dashboard.html', title: 'Packing & Scheduling', role: 'Logistics' },
		'delivery-scheduling': { division: 'production', home: 'production-division-dashboard.html', title: 'Delivery Scheduling', role: 'Driver' },
		'driver-dashboard': { division: 'production', home: 'production-division-dashboard.html', title: 'Driver Dashboard', role: 'Driver' },
		'delivery-tracking': { division: 'production', home: 'production-division-dashboard.html', title: 'Delivery Tracking', role: 'Driver' },
		'dto-job-request-form': { division: 'production', home: 'production-division-dashboard.html', title: 'DTO Job Request Form', role: 'Driver' },
		'dto-purchase-order': { division: 'production', home: 'production-division-dashboard.html', title: 'DTO Purchase Order', role: 'Driver' },
		'ford-auto-debit': { division: 'production', home: 'production-division-dashboard.html', title: 'FORD Auto Debit', role: 'Logistics' },
		'ford-client-payment-posting': { division: 'production', home: 'production-division-dashboard.html', title: 'FORD Client Payment Posting', role: 'Logistics' },
		'ford-eford-payout': { division: 'production', home: 'production-division-dashboard.html', title: 'FORD E-FORD Payout', role: 'Logistics' },
		'ford-payment-request': { division: 'production', home: 'production-division-dashboard.html', title: 'FORD Payment Request', role: 'Logistics' },
		'ford-purchase-order': { division: 'production', home: 'production-division-dashboard.html', title: 'FORD Purchase Order', role: 'Logistics' },
		'ford-request-for-quotation': { division: 'production', home: 'production-division-dashboard.html', title: 'FORD Request for Quotation', role: 'Logistics' },
		'ford-sales-order': { division: 'production', home: 'production-division-dashboard.html', title: 'FORD Sales Order', role: 'Logistics' },
		'ford-transmittal': { division: 'production', home: 'production-division-dashboard.html', title: 'FORD Transmittal', role: 'Logistics' },
		'request-payment-to-printer': { division: 'production', home: 'production-division-dashboard.html', title: 'Request Payment to Printer', role: 'Logistics' },
		'add-supplier': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'Add Supplier', role: 'Account Executive' },
		'app-profile': { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'Profile', role: 'Account Executive' }
	};

	/**
	 * Get current page configuration
	 */
	function getPageConfig() {
		const currentPage = window.location.pathname.split('/').pop() || window.location.href.split('/').pop();
		const pageName = currentPage.replace('.html', '').toLowerCase();

		// Check explicit mapping
		if (pageConfig[pageName]) {
			return pageConfig[pageName];
		}

		// Fallback detection
		if (pageName.includes('marketing') || pageName.includes('sales') || pageName.includes('customer') ||
			pageName.includes('pos') || pageName.includes('ecom') || pageName.includes('direct-invoice') ||
			pageName.includes('acknowledgement') || pageName.includes('credit-memo') || pageName.includes('proof-of-payment') ||
			pageName.includes('supplier') || pageName.includes('purchase-orders') || pageName.includes('product-list')) {
			return { division: 'marketing', home: 'marketing-division-dashboard.html', title: 'Marketing Division', role: 'Account Executive' };
		}

		if (pageName.includes('admin') || pageName.includes('finance') || pageName.includes('approval') ||
			pageName.includes('check-voucher') || pageName.includes('gsd') || pageName.includes('hr') ||
			pageName.includes('mis') || pageName.includes('job-orders') || pageName.includes('service-request') ||
			pageName.includes('cctv') || pageName.includes('undertime') || pageName.includes('qb-change') ||
			pageName.includes('materials-supplies') || pageName.includes('sales-invoice')) {
			return { division: 'admin-finance', home: 'admin-finance-dashboard.html', title: 'Admin & Finance', role: 'Finance Manager' };
		}

		if (pageName.includes('production') || pageName.includes('inventory') || pageName.includes('pick-list') ||
			pageName.includes('delivery') || pageName.includes('driver') || pageName.includes('dto') ||
			pageName.includes('ford') || pageName.includes('packing') || pageName.includes('fulfillment') ||
			pageName.includes('add-stock') || pageName.includes('received-items') || pageName.includes('printer')) {
			return { division: 'production', home: 'production-division-dashboard.html', title: 'Production Division', role: 'Logistics' };
		}

		// Default to super admin
		return { division: 'super-admin', home: 'index.html', title: 'Super Admin Dashboard', role: 'Super Admin' };
	}

	/**
	 * Load component HTML
	 */
	async function loadComponent(componentPath) {
		try {
			const response = await fetch(componentPath);
			if (!response.ok) {
				throw new Error(`Failed to load component: ${response.statusText}`);
			}
			return await response.text();
		} catch (error) {
			console.error('Error loading component:', error);
			return null;
		}
	}

	/**
	 * Load nav header
	 */
	async function loadNavHeader(config) {
		const container = document.getElementById('nav-header-container');
		if (!container) return;

		const html = await loadComponent('components/nav-header.html');
		if (!html) return;

		container.innerHTML = html;

		// Set home link
		const homeLink = container.querySelector('[data-nav-home]');
		if (homeLink) {
			homeLink.setAttribute('href', config.home);
		}
	}

	/**
	 * Load header
	 */
	async function loadHeader(config) {
		const container = document.getElementById('header-container');
		if (!container) return;

		const html = await loadComponent('components/header.html');
		if (!html) return;

		container.innerHTML = html;

		// Set page title
		const titleEl = container.querySelector('[data-page-title]');
		if (titleEl) {
			titleEl.textContent = config.title;
		}

		// Set user role
		const roleEl = container.querySelector('[data-user-role]');
		if (roleEl) {
			roleEl.textContent = config.role;
		}
	}

	/**
	 * Load sidebar
	 */
	async function loadSidebar(config) {
		const container = document.getElementById('sidebar-container');
		if (!container) return;

		const sidebarHTML = await loadComponent(`components/sidebar/${config.division}.html`);
		if (!sidebarHTML) return;

		// Create sidebar structure
		const sidebarWrapper = document.createElement('div');
		sidebarWrapper.className = 'deznav modern-production-sidebar';
		sidebarWrapper.innerHTML = `
			<div class="deznav-scroll modern-sidebar-scroll">
				${sidebarHTML}
				<div class="modern-sidebar-footer">
					<p><strong>Claretian ERP</strong><br>© 2026 All Rights Reserved</p>
				</div>
			</div>
		`;

		container.innerHTML = '';
		container.appendChild(sidebarWrapper);

		// Wait for next frame to ensure DOM is ready, then initialize sidebar
		requestAnimationFrame(function () {
			setTimeout(function () {
				initializeSidebar();
			}, 50);
		});
	}

	/**
	 * Initialize sidebar functionality
	 */
	function initializeSidebar() {
		const currentPage = window.location.pathname.split('/').pop() || window.location.href.split('/').pop();

		// Set active page
		document.querySelectorAll('.modern-nav-item').forEach(function (item) {
			const href = item.getAttribute('href');
			if (href) {
				// Normalize both for exact comparison
				const linkPage = href.split('/').pop().split('?')[0].split('#')[0].toLowerCase();
				const currPage = currentPage.split('?')[0].split('#')[0].toLowerCase();

				if (linkPage === currPage || (linkPage === 'index.html' && currPage === '')) {
					item.classList.add('active');
				}
			}
		});

		// Set active submenu items
		document.querySelectorAll('.modern-nav-subitem').forEach(function (item) {
			const href = item.getAttribute('href');
			if (href) {
				// Normalize both for exact comparison
				const linkPage = href.split('/').pop().split('?')[0].split('#')[0].toLowerCase();
				const currPage = currentPage.split('?')[0].split('#')[0].toLowerCase();

				if (linkPage === currPage) {
					item.classList.add('active');
					// Open parent group
					const group = item.closest('.modern-nav-group');
					if (group) {
						group.classList.add('active');
					}
				}
			}
		});

		// Setup toggle functionality
		const sidebar = document.querySelector('.modern-production-sidebar');
		if (!sidebar) {
			console.warn('Sidebar not found for initialization');
			return;
		}

		// Function to toggle a nav group
		function toggleNavGroup(group) {
			const isActive = group.classList.contains('active');

			// Close all other groups
			document.querySelectorAll('.modern-nav-group').forEach(function (g) {
				if (g !== group) {
					g.classList.remove('active');
				}
			});

			// Toggle current group
			if (isActive) {
				group.classList.remove('active');
			} else {
				group.classList.add('active');
			}
		}

		// Use event delegation on the sidebar - this is the most reliable approach
		sidebar.addEventListener('click', function (e) {
			// Find the toggle element using closest (most reliable method)
			const toggle = e.target.closest('.modern-nav-toggle');

			// If we found a toggle, handle it
			if (toggle) {
				e.preventDefault();
				e.stopPropagation();

				const group = toggle.closest('.modern-nav-group');
				if (group) {
					toggleNavGroup(group);
					return false;
				}
			}
		}, false);
	}

	/**
	 * Initialize all components
	 */
	async function init() {
		const config = getPageConfig();

		// Load all components
		await Promise.all([
			loadNavHeader(config),
			loadHeader(config),
			loadSidebar(config)
		]);
	}

	// Start initialization
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

})();
