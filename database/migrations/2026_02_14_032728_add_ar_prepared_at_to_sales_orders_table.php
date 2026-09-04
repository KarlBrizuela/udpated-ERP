<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArPreparedAtToSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('ar_prepared_by')->nullable()->after('dr_prepared_by');
            $table->datetime('ar_prepared_at')->nullable()->after('ar_prepared_by');
            
            $table->foreign('ar_prepared_by')->references('id')->on('users');
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
            $table->dropForeign(['ar_prepared_by']);
            $table->dropColumn(['ar_prepared_by', 'ar_prepared_at']);
        });
    }
}
