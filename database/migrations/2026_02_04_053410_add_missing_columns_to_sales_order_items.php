<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToSalesOrderItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_order_items', 'isbn')) {
                $table->string('isbn')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('sales_order_items', 'unit')) {
                $table->string('unit')->default('pcs')->after('isbn');
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
            $table->dropColumn(['isbn', 'unit']);
        });
    }
}
