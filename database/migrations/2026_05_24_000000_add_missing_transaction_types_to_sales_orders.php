<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddMissingTransactionTypesToSalesOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Change type column from ENUM to VARCHAR to allow more flexibility
        DB::statement("ALTER TABLE sales_orders MODIFY COLUMN type VARCHAR(50) DEFAULT 'paid'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to ENUM with original values
        DB::statement("ALTER TABLE sales_orders MODIFY COLUMN type ENUM('paid', 'charge', 'area_consignment', 'direct_consignment', 'foreign', 'complimentary', 'website_direct', 'ecom_direct', 'calculator_pos') DEFAULT 'paid'");
    }
}
