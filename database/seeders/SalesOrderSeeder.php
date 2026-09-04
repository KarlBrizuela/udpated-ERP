<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;

class SalesOrderSeeder extends Seeder
{
    public function run()
    {
        $customer = Customer::first();
        $product = Product::first();
        $admin = User::first();

        if (!$customer || !$product) {
            $this->command->info('Please run Customer and Product seeders first.');
            return;
        }

        // 1. Paid Transaction (Completed Flow)
        $so1 = SalesOrder::create([
            'customer_id' => $customer->customer_id,
            'so_number' => 'SO-2026-0001',
            'type' => 'paid',
            'status' => 'draft',
            'total_amount' => 500.00,
            'tax_amount' => 0.00,
            'prepared_by' => $admin->id,
            'remarks' => 'Sample Paid Transaction',
        ]);

        SalesOrderItem::create([
            'sales_order_id' => $so1->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 500.00,
            'subtotal' => 500.00,
        ]);

        // 2. Consignment Transaction (Different Flow)
        $so2 = SalesOrder::create([
            'customer_id' => $customer->customer_id,
            'so_number' => 'SO-2026-0002',
            'type' => 'area_consignment',
            'status' => 'mkt_approved', // Simulated next step
            'total_amount' => 1000.00,
            'tax_amount' => 0.00,
            'prepared_by' => $admin->id,
            'approved_by_mkt' => $admin->id,
            'remarks' => 'Sample Consignment for Area Sales',
        ]);
        
        SalesOrderItem::create([
            'sales_order_id' => $so2->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 500.00,
            'subtotal' => 1000.00,
        ]);

        $this->command->info('Sales Orders seeded successfully.');
    }
}
