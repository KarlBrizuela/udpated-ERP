<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrackingFieldsToSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            // New Tracking Fields
            $table->unsignedBigInteger('approved_by_prod')->nullable()->after('approved_by_acct');
            $table->unsignedBigInteger('signed_by_af_manager')->nullable()->after('approved_by_prod');
            $table->unsignedBigInteger('si_prepared_by')->nullable()->after('signed_by_af_manager');
            $table->unsignedBigInteger('dr_prepared_by')->nullable()->after('si_prepared_by');
            
            $table->datetime('mkt_approved_at')->nullable()->after('approved_by_mkt');
            $table->datetime('acct_approved_at')->nullable()->after('approved_by_acct');
            $table->datetime('prod_approved_at')->nullable()->after('approved_by_prod');
            $table->datetime('signed_at')->nullable()->after('signed_by_af_manager');
            $table->datetime('si_prepared_at')->nullable()->after('si_prepared_by');
            $table->datetime('dr_prepared_at')->nullable()->after('dr_prepared_by');
            
            // Foreign Keys
            $table->foreign('approved_by_prod')->references('id')->on('users');
            $table->foreign('signed_by_af_manager')->references('id')->on('users');
            $table->foreign('si_prepared_by')->references('id')->on('users');
            $table->foreign('dr_prepared_by')->references('id')->on('users');
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
            $table->dropForeign(['approved_by_prod']);
            $table->dropForeign(['signed_by_af_manager']);
            $table->dropForeign(['si_prepared_by']);
            $table->dropForeign(['dr_prepared_by']);
            
            $table->dropColumn([
                'approved_by_prod', 'signed_by_af_manager', 'si_prepared_by', 'dr_prepared_by',
                'mkt_approved_at', 'acct_approved_at', 'prod_approved_at',
                'signed_at', 'si_prepared_at', 'dr_prepared_at'
            ]);
        });
    }
}
