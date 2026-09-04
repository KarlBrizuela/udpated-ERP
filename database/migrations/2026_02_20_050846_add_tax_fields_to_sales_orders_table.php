<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxFieldsToSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->decimal('withholding_tax_amount', 15, 2)->default(0)->after('tax_amount');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('withholding_tax_amount');
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('discount_amount');
            $table->boolean('is_non_vat')->default(true)->after('discount_percentage');
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
            $table->dropColumn(['withholding_tax_amount', 'discount_amount', 'discount_percentage', 'is_non_vat']);
        });
    }
}
