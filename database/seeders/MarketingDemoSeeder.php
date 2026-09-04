<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MarketingDemoSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // Create demo customers (match customers table columns)
        $customerIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $acct = 'ACCT-DEMO-'.str_pad($i, 3, '0', STR_PAD_LEFT);
            $existing = DB::table('customers')->where('account_number', $acct)->first();
            if ($existing) {
                $customerIds[] = $existing->customer_id ?? $existing->id ?? null;
                continue;
            }

            $id = DB::table('customers')->insertGetId([
                'customer_name' => 'Demo Customer '.$i,
                'company_name' => 'Demo Company '.$i,
                'account_number' => $acct,
                'main_email' => "demo{$i}@example.com",
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $customerIds[] = $id;
        }

        // Ensure some demo books exist (books table uses 'name' and 'cost')
        $bookIds = DB::table('books')->limit(5)->pluck('id')->toArray();
        if (count($bookIds) < 3) {
            $bookIds = [];
            for ($b = 1; $b <= 5; $b++) {
                $bookIds[] = DB::table('books')->insertGetId([
                    'name' => "Demo Book {$b}",
                    'sku' => 'DEMO-SKU-'.Str::upper(Str::random(6)),
                    'cost' => 100 + ($b * 50),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $platforms = ['area_sales','direct_pos','ecom_pos'];

        // Pick a prepared_by user if exists, otherwise leave null (prepared_by is nullable)
        $preparedBy = DB::table('users')->value('id');

        // Create sales orders across platforms and days in current month
        $start = Carbon::now()->startOfMonth();
        for ($day = 1; $day <= 10; $day++) {
            $date = $start->copy()->addDays($day - 1)->setTime(rand(8,18), rand(0,59), 0);
            $platform = $platforms[array_rand($platforms)];
            $customer_id = $customerIds[array_rand($customerIds)];

            $total = 0;
            // Map platform to allowed 'type' enum values defined in migrations
            $type = 'paid';
            if ($platform === 'area_sales') {
                $type = 'area_consignment';
            } elseif ($platform === 'direct_pos') {
                $type = 'direct_consignment';
            } elseif ($platform === 'ecom_pos') {
                $type = 'ecom_direct';
            }

            // Map our internal platform keys to the platform enum values in DB
            // allowed: ['lazada', 'shopee', 'tiktok', 'website', 'facebook', 'other']
            if ($platform === 'ecom_pos') {
                $platformDb = 'website';
            } else {
                $platformDb = 'other';
            }

            $soId = DB::table('sales_orders')->insertGetId([
                'customer_id' => $customer_id,
                'so_number' => 'SO-DEMO-'.Str::upper(Str::random(6)),
                'type' => $type,
                'status' => 'completed',
                'total_amount' => 0,
                'prepared_by' => $preparedBy,
                'platform' => $platformDb,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            // add 1-3 items
            $itemsCount = rand(1,3);
            for ($it = 0; $it < $itemsCount; $it++) {
                $book_id = $bookIds[array_rand($bookIds)];
                $qty = rand(1,4);
                // Prefer product price if exists (products.book_id -> price), else book cost
                $price = DB::table('products')->where('book_id', $book_id)->value('price');
                if (is_null($price)) {
                    $price = DB::table('books')->where('id', $book_id)->value('cost') ?: (100 + rand(1,5)*10);
                }
                $subtotal = $price * $qty;
                $total += $subtotal;

                DB::table('sales_order_items')->insert([
                    'sales_order_id' => $soId,
                    'book_id' => $book_id,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }

            // update total_amount
            DB::table('sales_orders')->where('id', $soId)->update(['total_amount' => $total]);
        }

        $this->command->info('Marketing demo data seeded.');
    }
}
