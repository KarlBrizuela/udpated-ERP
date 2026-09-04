<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSourcePriceAtSaleToSalesOrderItemsSafe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_order_items', 'source_price_at_sale')) {
                $table->decimal('source_price_at_sale', 10, 2)->default(0)->after('price');
            }
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
            if (Schema::hasColumn('sales_order_items', 'source_price_at_sale')) {
                $table->dropColumn('source_price_at_sale');
            }
        });
    }
}
