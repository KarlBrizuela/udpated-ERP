<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // 1. Define Common Permissions
        $marketingPerms = [
            'marketing.dashboard',
            'marketing.customers',
            'marketing.area_sales',
            'marketing.direct_sales',
            'marketing.ads_promo',
            'marketing.ecom',
            'marketing.book_mgmt',
            'marketing.supplier_mgmt',
            'marketing.approval_queue',
            'marketing.my_requests'
        ];

        $productionPerms = [
            'production.dashboard',
            'production.inventory',
            'production.logistic',
            'production.dto',
            'production.ford',
            'production.printing',
            'production.approval_queue',
            'production.my_requests'
        ];

        $adminFinancePerms = [
            'admin_finance.dashboard',
            'admin_finance.mis',
            'admin_finance.gsd',
            'admin_finance.credit_collection',
            'admin_finance.accounting',
            'admin_finance.expense_management',
            'admin_finance.service_requests',
            'admin_finance.hr',
            'admin_finance.approval_queue',
            'admin_finance.my_requests'
        ];

        // 2. Create Roles with Permissions
        
        // COMMON / MISC
        $this->createRole('Super Admin', 'All Divisions', array_merge($adminFinancePerms, $marketingPerms, $productionPerms, ['admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher']));
        $this->createRole('Director', 'All Divisions', array_merge($adminFinancePerms, $marketingPerms, $productionPerms, ['admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher']));
        $this->createRole('Manager', 'All Divisions', array_merge($adminFinancePerms, $marketingPerms, ['admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher']));

        // ADMIN AND FINANCE
        $this->createRole('MIS Super Admin', 'Admin & Finance Division', array_merge($adminFinancePerms, $marketingPerms, $productionPerms, ['admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher']));
        $this->createRole('A&F Manager', 'Admin & Finance Division', array_merge(['admin_finance.dashboard', 'admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher'], ['admin_finance.dashboard', 'admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher']));
        
        // Accounting
        $accountingStaffPerms = ['admin_finance.dashboard', 'admin_finance.accounting', 'admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher', 'admin_finance.approval_queue', 'admin_finance.my_requests'];
        $this->createRole('Senior Accounting Staff', 'Admin & Finance Division', $accountingStaffPerms);
        $this->createRole('Accounting Staff', 'Admin & Finance Division', $accountingStaffPerms);
        $this->createRole('Inventory Staff', 'Admin & Finance Division', $accountingStaffPerms);
        $this->createRole('Cashier', 'Admin & Finance Division', $accountingStaffPerms);

        // Credit and Collection
        $ccStaffPerms = ['admin_finance.dashboard', 'admin_finance.credit_collection', 'admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher', 'admin_finance.approval_queue', 'admin_finance.my_requests'];
        $this->createRole('Credit and Collection Staff', 'Admin & Finance Division', $ccStaffPerms);
        $this->createRole('Senior Inventory and Collection Staff', 'Admin & Finance Division', $ccStaffPerms);
        $this->createRole('Inventory and Collection Staff', 'Admin & Finance Division', $ccStaffPerms);
        $this->createRole('Billing Staff', 'Admin & Finance Division', $ccStaffPerms);

        // MIS / GSD / HR
        $this->createRole('MIS Supervisor', 'Admin & Finance Division', ['admin_finance.dashboard', 'admin_finance.mis', 'admin_finance.service_requests', 'admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher', 'admin_finance.my_requests']);
        $this->createRole('MIS Staff', 'Admin & Finance Division', ['admin_finance.dashboard', 'admin_finance.mis', 'admin_finance.service_requests', 'admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher', 'admin_finance.my_requests']);
        $this->createRole('GSD Supervisor', 'Admin & Finance Division', ['admin_finance.dashboard', 'admin_finance.gsd', 'admin_finance.service_requests', 'admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher', 'admin_finance.my_requests']);
        $this->createRole('HR Staff', 'Admin & Finance Division', ['admin_finance.dashboard', 'admin_finance.hr', 'admin_finance.service_requests', 'admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher', 'admin_finance.my_requests']);

        // MARKETING
        $this->createRole('Marketing Manager', 'Marketing Division', array_merge($marketingPerms, ['marketing.dashboard', 'admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher']));
        $this->createRole('Marketing Coordinator', 'Marketing Division', ['marketing.dashboard', 'marketing.area_sales', 'marketing.customers', 'admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher', 'marketing.my_requests']);
        $this->createRole('Senior Supervisor', 'Marketing Division', ['marketing.dashboard', 'marketing.area_sales', 'marketing.customers', 'admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher', 'marketing.my_requests']);
        $this->createRole('Account Executive', 'Marketing Division', ['marketing.dashboard', 'marketing.area_sales', 'marketing.customers', 'admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher', 'marketing.my_requests']);
        
        $directSalesPerms = ['marketing.dashboard', 'marketing.direct_sales', 'marketing.customers', 'admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher', 'marketing.my_requests'];
        $this->createRole('Bookstore Staff', 'Marketing Division', $directSalesPerms);
        $this->createRole('Bookschain Staff', 'Marketing Division', $directSalesPerms);
        $this->createRole('Booksales Staff', 'Marketing Division', $directSalesPerms);
        $this->createRole('Marketing Staff', 'Marketing Division', ['marketing.dashboard', 'marketing.ads_promo', 'admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher', 'marketing.my_requests']);

        // PRODUCTION
        $this->createRole('DTO Supervisor', 'Production Division', ['production.dashboard', 'production.dto', 'production.inventory', 'admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher', 'production.my_requests']);
        
        $fordPerms = ['production.dashboard', 'production.ford', 'admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher', 'production.approval_queue', 'production.my_requests'];
        $this->createRole('Senior Ford Staff', 'Production Division', $fordPerms);
        $this->createRole('Ford Staff', 'Production Division', $fordPerms);
        
        $logisticsPerms = ['production.dashboard', 'production.logistic', 'production.inventory', 'production.my_requests', 'admin_finance.petty_cash_voucher', 'admin_finance.freight_voucher'];
        $this->createRole('Senior Logistics Staff', 'Production Division', $logisticsPerms);
        $this->createRole('Logistics Staff', 'Production Division', $logisticsPerms);
        
        $driverPerms = ['production.dashboard', 'production.logistic', 'production.my_requests'];
        $this->createRole('Driver', 'Production Division', $driverPerms);
        
        $this->createRole('Printing Services Staff', 'Production Division', ['production.dashboard', 'production.printing', 'admin_finance.petty_cash_voucher', 'production.my_requests']);

    }

    private function createRole($name, $division, $permissions)
    {
        Role::updateOrCreate(
            ['name' => $name],
            [
                'division' => $division,
                'permissions' => $permissions,
                'is_active' => true,
                'description' => $name . ' role in ' . $division
            ]
        );
    }
}
