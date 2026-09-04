<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropIsbnColumnsFromTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'isbn')) {
                $table->dropColumn('isbn');
            }
        });

        Schema::table('sales_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('sales_order_items', 'isbn')) {
                $table->dropColumn('isbn');
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
        Schema::table('products', function (Blueprint $table) {
            $table->string('isbn')->nullable();
        });

        Schema::table('sales_order_items', function (Blueprint $table) {
            $table->string('isbn')->nullable()->after('product_id');
        });
    }
}
