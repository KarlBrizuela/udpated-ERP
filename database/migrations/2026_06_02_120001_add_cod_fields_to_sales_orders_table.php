<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodFieldsToSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            // Add transaction type (COD, Credit, Prepaid, etc.)
            if (!Schema::hasColumn('sales_orders', 'transaction_type')) {
                $table->enum('transaction_type', ['COD', 'Credit', 'Prepaid', 'Check', 'Other'])->default('COD')->after('type');
            }
            
            // Add collection status for tracking COD payments
            if (!Schema::hasColumn('sales_orders', 'collection_status')) {
                $table->enum('collection_status', ['pending_collection', 'collected', 'handed_over', 'reconciled'])->default('pending_collection')->after('transaction_type');
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
            if (Schema::hasColumn('sales_orders', 'transaction_type')) {
                $table->dropColumn('transaction_type');
            }
            if (Schema::hasColumn('sales_orders', 'collection_status')) {
                $table->dropColumn('collection_status');
            }
        });
    }
}
