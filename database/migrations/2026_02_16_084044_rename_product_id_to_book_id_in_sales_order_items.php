<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameProductIdToBookIdInSalesOrderItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tablePrefix = DB::getTablePrefix();
        $table = $tablePrefix . 'sales_order_items';
        $productsTable = $tablePrefix . 'products';

        // 1. Drop existing FK
        try {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `sales_order_items_product_id_foreign`");
        } catch (\Exception $e) {}

        // 2. Add book_id column
        if (!Schema::hasColumn('sales_order_items', 'book_id')) {
            Schema::table('sales_order_items', function (Blueprint $table) {
                $table->unsignedBigInteger('book_id')->nullable()->after('product_id');
            });
        }

        // 3. Migrate data: Link items to books via the products table link
        if (Schema::hasTable('products') && Schema::hasColumn('sales_order_items', 'product_id')) {
            DB::statement("UPDATE `{$table}` soi 
                        JOIN `{$productsTable}` p ON soi.product_id = p.id 
                        SET soi.book_id = p.book_id 
                        WHERE soi.book_id IS NULL");
        }

        // 4. Drop product_id and make book_id required
        if (Schema::hasColumn('sales_order_items', 'product_id')) {
            Schema::table('sales_order_items', function (Blueprint $table) {
                $table->dropColumn('product_id');
            });
        }

        DB::statement("ALTER TABLE `{$table}` MODIFY `book_id` BIGINT UNSIGNED NOT NULL");

        // 5. Add new FK
        Schema::table('sales_order_items', function (Blueprint $table) {
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_order_items', function (Blueprint $table) {
            $table->dropForeign(['book_id']);
            $table->renameColumn('book_id', 'product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
}
