<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;

class ChartOfAccountSeeder extends Seeder
{
    public function run()
    {
        $accounts = [
            // Assets
            ['code' => '1000', 'name' => 'Cash in Bank', 'type' => 'Asset', 'category' => 'Current Asset'],
            ['code' => '1010', 'name' => 'Cash in Bank (Other)', 'type' => 'Asset', 'category' => 'Current Asset'],
            ['code' => '1020', 'name' => 'Petty Cash Fund', 'type' => 'Asset', 'category' => 'Current Asset'],
            ['code' => '1030', 'name' => 'Undeposited Funds', 'type' => 'Asset', 'category' => 'Current Asset'],
            ['code' => '1200', 'name' => 'Trade/Accounts Receivable', 'type' => 'Asset', 'category' => 'Current Asset'],
            ['code' => '1300', 'name' => 'Inventory - Books', 'type' => 'Asset', 'category' => 'Current Asset'],
            ['code' => '1310', 'name' => 'Inventory - Consignment', 'type' => 'Asset', 'category' => 'Current Asset'],
            
            // Liabilities
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'Liability', 'category' => 'Current Liability'],
            ['code' => '2100', 'name' => 'Withholding Tax Payable', 'type' => 'Liability', 'category' => 'Current Liability'],
            
            // Equity
            ['code' => '3000', 'name' => 'Retained Earnings', 'type' => 'Equity', 'category' => 'Equity'],
            
            // Income
            ['code' => '4000', 'name' => 'Sales - Books', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4010', 'name' => 'Sales - Consignment', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4100', 'name' => 'Sales Discount', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4200', 'name' => 'Other Income', 'type' => 'Income', 'category' => 'Other Income'],
            
            // Expenses
            ['code' => '5000', 'name' => 'Cost of Sales', 'type' => 'Expense', 'category' => 'COGS'],
            ['code' => '6000', 'name' => 'Supplies Expense', 'type' => 'Expense', 'category' => 'Operating Expense'],
            ['code' => '6010', 'name' => 'Travel & Transportation', 'type' => 'Expense', 'category' => 'Operating Expense'],
            ['code' => '6020', 'name' => 'Communication Expense', 'type' => 'Expense', 'category' => 'Operating Expense'],
            ['code' => '6030', 'name' => 'Representation Expense', 'type' => 'Expense', 'category' => 'Operating Expense'],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::updateOrCreate(['code' => $account['code']], $account);
        }
    }
}
