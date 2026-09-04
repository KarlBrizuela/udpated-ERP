<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Supplier::updateOrCreate(
            ['supplier_code' => 'SUP-001'],
            [
                'company_name' => 'ABC Wholesale Corp',
                'contact_person' => 'John Smith',
                'email' => 'john.smith@abcwholesale.com',
                'phone' => '+1 (555) 123-4567',
                'status' => 'active',
            ]
        );
    }
}
