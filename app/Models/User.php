<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Role;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_number',
        'first_name',
        'last_name',
        'middle_initial',
        'email',
        'password',
        'plain_password',
        'division',
        'department',
        'position',
        'permissions',
        'sales_team',
        'status',
    ];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_initial} {$this->last_name}");
    }
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'permissions' => 'array',
    ];

	public function profile()
	{
		return $this->hasOne(UserProfile::class);
	}

	public function divisions()
	{
		return $this->hasMany(UserDivision::class);
	}

    /**
     * Check if the user is a Super Admin.
     * 
     * @return bool
     */
    public function isSuperAdmin()
    {
        // Check by position
        if ($this->position === 'Super Admin') {
            return true;
        }

        // List of authorized super admin emails
        $superAdminEmails = [
            'admin@claretianpublications.ph',
            'director@claretianpublications.ph',
            'admin@intra-code.com',
            'admin@clarentian.com', // Added from live server logs
        ];

        return in_array($this->email, $superAdminEmails);
    }

    /**
     * Check if user has specific permission.
     * 
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $acceptedPermissions = array_merge([$permission], $this->getParentPermissions($permission));

        // 1. Check user-level permissions first (Manual override)
        // We only use the manual override if it's NOT NULL.
        // If it's an array (including empty []), it means you manually set their access.
        if (!is_null($this->permissions)) {
            return !empty(array_intersect($acceptedPermissions, $this->permissions));
        }

        // 2. Fallback to role-level permissions if permissions column is NULL
        $role = Role::where('name', $this->position)->first();
        if (!$role || !$role->is_active || !$role->permissions || !is_array($role->permissions)) {
            return false;
        }

        return !empty(array_intersect($acceptedPermissions, $role->permissions));
    }

    public function hasAnyPermission(array $permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    private function getParentPermissions($permission)
    {
        $parentMap = [
            'marketing.area_sales' => [
                'marketing.area_sales.sales_orders',
                'marketing.area_sales.freight_quotations',
                'marketing.area_sales.direct_invoice_website',
                'marketing.area_sales.direct_invoice_ecom',
                'marketing.area_sales.acknowledgement_receipt',
                'marketing.area_sales.credit_memo',
                'marketing.area_sales.proof_of_payment',
            ],
            'marketing.direct_sales' => [
                'marketing.direct_sales.pos',
                'marketing.direct_sales.products',
                'marketing.direct_sales.nbs_import',
            ],
            'marketing.ads_promo' => [
                'marketing.ads_promo.marketing_plan',
                'marketing.ads_promo.sponsors',
            ],
            'marketing.ecom' => [
                'marketing.ecom.pos',
            ],
            'marketing.book_mgmt' => [
                'marketing.book_mgmt.book_list',
                'marketing.book_mgmt.consignment',
            ],
            'production.inventory' => [
                'production.inventory.overview',
                'production.inventory.add_stock',
                'production.inventory.received',
            ],
            'production.logistic' => [
                'production.logistic.pick_lists',
                'production.logistic.packing',
                'production.logistic.delivery_scheduling',
                'production.logistic.delivery_receipts',
                'production.logistic.driver_dashboard',
                'production.logistic.delivery_tracking',
                'production.logistic.purchase_orders',
                'production.logistic.receiving_reports',
                'production.logistic.freight_quotation_review',
                'production.logistic.rider_collections',
                'production.logistic.area_consignment',
            ],
            'production.dto' => [
                'production.dto.job_request_form',
            ],
            'production.ford' => [
                'production.ford.auto_debit',
                'production.ford.client_payment_posting',
                'production.ford.eford_payout',
                'production.ford.payment_request',
                'production.ford.purchase_order',
                'production.ford.request_for_quotation',
                'production.ford.sales_order',
                'production.ford.transmittal',
            ],
            'production.printing' => [
                'production.printing.request_payment_to_printer',
            ],
            'admin_finance.accounting' => [
                'admin_finance.accounting.general_journal',
                'admin_finance.accounting.sales_invoice',
                'admin_finance.accounting.check_voucher',
                'admin_finance.accounting.materials_requisition',
                'admin_finance.accounting.material_requests',
                'admin_finance.accounting.cash_advance_liquidation',
                'admin_finance.accounting.cod_collections',
                'admin_finance.accounting.cashier',
                'admin_finance.accounting.payment_posting',
                'admin_finance.accounting.auto_debit',
                'admin_finance.accounting.office_supplies',
                'admin_finance.accounting.expenses',
                'admin_finance.accounting.chart_of_accounts',
            ],
            'admin_finance.finance' => [
                'admin_finance.petty_cash_voucher',
                'admin_finance.freight_voucher',
            ],
            'admin_finance.credit_collection' => [
                'admin_finance.credit_collection.billing',
                'admin_finance.credit_collection.reports',
                'admin_finance.credit_collection.invoice',
            ],
            'admin_finance.gsd' => [
                'admin_finance.gsd.asset_management',
                'admin_finance.gsd.job_orders',
            ],
            'admin_finance.hr' => [
                'admin_finance.hr.job_orders',
            ],
            'admin_finance.mis' => [
                'admin_finance.mis.job_orders',
            ],
        ];

        foreach ($parentMap as $parentPermission => $childPermissions) {
            if (in_array($permission, $childPermissions)) {
                return [$parentPermission];
            }
        }

        return [];
    }

    /**
     * Get the first available route name for the user based on their permissions.
     */
    public function getFirstAvailableRoute()
    {
        if ($this->isSuperAdmin()) {
            return 'dashboard';
        }

        // Define route priority (matches sidebar order)
        $priorities = [
            // Dashboards
            'marketing.dashboard' => 'marketing.dashboard',
            'production.dashboard' => 'production.dashboard',
            'admin_finance.dashboard' => 'admin-finance.dashboard',

            // Common Tools
            'marketing.my_requests' => 'marketing.my-requests',
            'production.my_requests' => 'production.my-requests',
            'admin_finance.my_requests' => 'admin-finance.my-requests',
            'marketing.approval_queue' => 'marketing.approval-queue',
            'production.approval_queue' => 'production.approval-queue',
            'admin_finance.approval_queue' => 'admin-finance.approval-queue',

            // Marketing
            'marketing.customers' => 'marketing.customers',
            'marketing.area_sales' => 'marketing.sales-orders.list',
            'marketing.area_sales.sales_orders' => 'marketing.sales-orders.list',
            'marketing.area_sales.freight_quotations' => 'marketing.freight-quotations.list',
            'marketing.area_sales.direct_invoice_website' => 'marketing.direct-invoice.website',
            'marketing.area_sales.direct_invoice_ecom' => 'marketing.direct-invoice.ecom',
            'marketing.area_sales.acknowledgement_receipt' => 'marketing.acknowledgement-receipt',
            'marketing.area_sales.credit_memo' => 'marketing.credit-memo',
            'marketing.area_sales.proof_of_payment' => 'marketing.proof-of-payment',
            'marketing.direct_sales' => 'marketing.pos.sale',
            'marketing.direct_sales.pos' => 'marketing.pos.sale',
            'marketing.direct_sales.products' => 'marketing.pos.products',
            'marketing.direct_sales.nbs_import' => 'marketing.nbs-import.index',
            'marketing.ads_promo' => 'marketing.ads-promo.crpr',
            'marketing.ads_promo.marketing_plan' => 'marketing.ads-promo.crpr',
            'marketing.ads_promo.sponsors' => 'marketing.ads-promo.sponsors',
            'marketing.ecom' => 'marketing.ecom.pos',
            'marketing.ecom.pos' => 'marketing.ecom.pos',
            'marketing.book_mgmt' => 'marketing.products',
            'marketing.book_mgmt.book_list' => 'marketing.products',
            'marketing.book_mgmt.consignment' => 'marketing.consignment.index',
            'marketing.supplier_mgmt' => 'marketing.suppliers',

            // Production
            'production.inventory' => 'production.inventory.overview',
            'production.inventory.overview' => 'production.inventory.overview',
            'production.inventory.add_stock' => 'production.inventory.add-stock',
            'production.inventory.received' => 'production.inventory.received',
            'production.logistic' => 'production.logistic.pick-list-list',
            'production.logistic.pick_lists' => 'production.logistic.pick-list-list',
            'production.logistic.packing' => 'production.logistic.packing-management',
            'production.logistic.delivery_scheduling' => 'production.logistic.delivery-scheduling',
            'production.logistic.delivery_receipts' => 'production.logistic.delivery-receipt-list',
            'production.logistic.driver_dashboard' => 'production.logistic.driver-dashboard',
            'production.logistic.delivery_tracking' => 'production.logistic.delivery-tracking',
            'production.logistic.purchase_orders' => 'production.logistic.purchase-order-list',
            'production.logistic.receiving_reports' => 'production.logistic.receiving-report-list',
            'production.logistic.freight_quotation_review' => 'production.logistic.pending-freight-quotations',
            'production.logistic.rider_collections' => 'rider.collections.index',
            'production.logistic.area_consignment' => 'production.logistic.area-consignment',
            'production.dto' => 'production.dto.job-request-form',
            'production.ford' => 'production.ford.client-payment-posting',
            'production.dto.job_request_form' => 'production.dto.job-request-form',
            'production.ford.auto_debit' => 'production.ford.auto-debit',
            'production.ford.client_payment_posting' => 'production.ford.client-payment-posting',
            'production.ford.eford_payout' => 'production.ford.eford-payout',
            'production.ford.payment_request' => 'production.ford.payment-request',
            'production.ford.purchase_order' => 'production.ford.purchase-order',
            'production.ford.request_for_quotation' => 'production.ford.request-for-quotation',
            'production.ford.sales_order' => 'production.ford.sales-order',
            'production.ford.transmittal' => 'production.ford.transmittal',
            'production.printing' => 'production.printing.request-payment-to-printer',
            'production.printing.request_payment_to_printer' => 'production.printing.request-payment-to-printer',

            // Admin & Finance
            'admin_finance.accounting' => 'admin-finance.accounting.sales-invoice',
            'admin_finance.accounting.general_journal' => 'accounting.journal.index',
            'admin_finance.accounting.sales_invoice' => 'admin-finance.accounting.sales-invoice',
            'admin_finance.accounting.check_voucher' => 'admin-finance.check-voucher',
            'admin_finance.accounting.materials_requisition' => 'admin-finance.accounting.materials-requisition',
            'admin_finance.accounting.material_requests' => 'admin-finance.accounting.material-requests.incoming',
            'admin_finance.accounting.cash_advance_liquidation' => 'admin-finance.accounting.expense-management',
            'admin_finance.accounting.cod_collections' => 'cashier.collections.index',
            'admin_finance.accounting.cashier' => 'admin-finance.accounting.cashier.index',
            'admin_finance.accounting.payment_posting' => 'admin-finance.accounting.payment-posting.index',
            'admin_finance.accounting.auto_debit' => 'admin-finance.accounting.auto-debits.index',
            'admin_finance.accounting.office_supplies' => 'admin-finance.accounting.office-supplies.index',
            'admin_finance.accounting.expenses' => 'admin-finance.accounting.expenses.index',
            'admin_finance.petty_cash_voucher' => 'admin-finance.petty-cash.index',
            'admin_finance.freight_voucher' => 'admin-finance.freight-voucher.index',
            'admin_finance.credit_collection' => 'admin-finance.credit-collection.billing',
            'admin_finance.credit_collection.billing' => 'admin-finance.credit-collection.billing',
            'admin_finance.credit_collection.reports' => 'admin-finance.credit-collection.reports',
            'admin_finance.credit_collection.invoice' => 'admin-finance.credit-collection.invoice',
            'admin_finance.gsd' => 'admin-finance.gsd.asset-management',
            'admin_finance.gsd.asset_management' => 'admin-finance.gsd.asset-management',
            'admin_finance.gsd.job_orders' => 'admin-finance.gsd.job-orders',
            'admin_finance.mis' => 'admin-finance.mis.job-orders',
            'admin_finance.mis.job_orders' => 'admin-finance.mis.job-orders',
            'admin_finance.hr' => 'admin-finance.hr.job-orders',
            'admin_finance.hr.job_orders' => 'admin-finance.hr.job-orders',
            'admin_finance.service_requests' => 'admin-finance.service-requests.create',
        ];

        foreach ($priorities as $perm => $route) {
            if ($this->hasPermission($perm)) {
                return $route;
            }
        }

        return 'profile';
    }

    /**
     * Override delete to modify unique columns so they can be reused by new users.
     */
    public function delete()
    {
        // Prevent modifying it multiple times if soft deleted again
        if (!$this->trashed()) {
            $this->employee_number = $this->employee_number . '_del_' . $this->id;
            $this->email = $this->email . '_del_' . $this->id;
            $this->save();
        }

        return parent::delete();
    }
}
