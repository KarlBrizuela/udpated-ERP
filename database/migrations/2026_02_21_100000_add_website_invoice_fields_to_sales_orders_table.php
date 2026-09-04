<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWebsiteInvoiceFieldsToSalesOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('proof_of_payment')->nullable()->after('attachment');
            $table->string('order_list_attachment')->nullable()->after('proof_of_payment');
            $table->string('transaction_subtype')->nullable()->after('type'); // 'foreign' or 'local' for website_direct
        });
    }

    public function down()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn(['proof_of_payment', 'order_list_attachment', 'transaction_subtype']);
        });
    }
}
