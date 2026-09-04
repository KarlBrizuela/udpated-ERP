<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEcomInvoiceFieldsToSalesOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('pick_list_attachment')->nullable()->after('order_list_attachment');
            $table->string('shipping_label_attachment')->nullable()->after('pick_list_attachment');
            $table->string('ecom_platform')->nullable()->after('transaction_subtype'); // lazada, shopee, tiktok
            $table->string('platform_order_id')->nullable()->after('ecom_platform');
        });
    }

    public function down()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn(['pick_list_attachment', 'shipping_label_attachment', 'ecom_platform', 'platform_order_id']);
        });
    }
}
