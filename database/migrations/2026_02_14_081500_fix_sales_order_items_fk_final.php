<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixSalesOrderItemsFkFinal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to avoid Doctrine dependency
        $tablePrefix = DB::getTablePrefix();
        $table = $tablePrefix . 'sales_order_items';
        
        // Try to drop existing keys (ignoring errors if they don't exist)
        try {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `sales_order_items_product_id_foreign`");
        } catch (\Exception $e) {
            try {
                DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `sales_order_items_product_id_foreign`");
            } catch (\Exception $ex) {}
        }

        // Clean up orphan records that would fail the constraint
        DB::statement("DELETE FROM `{$table}` WHERE `product_id` NOT IN (SELECT `id` FROM `products`)");

        // Add correct foreign key
        DB::statement("ALTER TABLE `{$table}` ADD CONSTRAINT `sales_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No revert
    }
}
