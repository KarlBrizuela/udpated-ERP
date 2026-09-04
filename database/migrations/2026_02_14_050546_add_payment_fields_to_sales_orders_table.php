<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'gcash', 'paymaya', 'card', 'bank'])->nullable()->after('status');
            $table->string('payment_reference')->nullable()->after('payment_method'); // For digital payments
            $table->decimal('cash_received', 15, 2)->nullable()->after('payment_reference'); // For cash payments
            $table->decimal('change_amount', 15, 2)->nullable()->after('cash_received'); // For cash payments
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
            $table->dropColumn(['payment_method', 'payment_reference', 'cash_received', 'change_amount']);
        });
    }
}
