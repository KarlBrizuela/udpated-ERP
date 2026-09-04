<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ValidationSeeder extends Seeder
{
    public function run()
    {
        // Ensure an admin user exists
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin User',
                'email' => 'admin@claretian.com',
                'password' => Hash::make('password'),
            ]);
        }

        // Ensure a product exists
        if (Product::count() == 0) {
            Product::create([
                'name' => 'The Claretian Mission',
                'sku' => 'BOOK-001',
                'price' => 500.00,
                'stock' => 100,
                'is_active' => true,
                'unit' => 'pcs',
            ]);
        }

        // Ensure a customer exists
        if (Customer::count() == 0) {
            Customer::create([
                'customer_name' => 'John Doe',
                'company_name' => 'Doe Corp',
                'account_number' => 'ACC-' . Str::random(5),
                'main_email' => 'john@doe.com',
            ]);
        }
    }
}
