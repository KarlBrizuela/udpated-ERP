<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEcomFieldsToSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->enum('platform', ['lazada', 'shopee', 'tiktok', 'website', 'facebook', 'other'])->nullable()->after('type');
            $table->decimal('shipping_fee', 10, 2)->default(0)->after('tax_amount');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid')->after('total_amount');
            $table->string('tracking_number')->nullable()->after('payment_reference');
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
            $table->dropColumn(['platform', 'shipping_fee', 'payment_status', 'tracking_number']);
        });
    }
}
