<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToSalesOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_orders', 'terms')) {
                $table->string('terms')->nullable()->after('status');
            }
            if (!Schema::hasColumn('sales_orders', 'ref_number')) {
                $table->string('ref_number')->nullable()->after('terms');
            }
            if (!Schema::hasColumn('sales_orders', 'shipping_address')) {
                $table->text('shipping_address')->nullable()->after('ref_number');
            }
            if (!Schema::hasColumn('sales_orders', 'billing_address')) {
                $table->text('billing_address')->nullable()->after('shipping_address');
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
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn(['terms', 'ref_number', 'shipping_address', 'billing_address']);
        });
    }
}
