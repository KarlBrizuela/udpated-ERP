<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
            $table->string('so_number')->unique(); // SO-2026-0001
            $table->enum('type', [
                'paid', 
                'charge', 
                'area_consignment', 
                'direct_consignment', 
                'foreign', 
                'complimentary', 
                'website_direct', 
                'ecom_direct',
                'calculator_pos' // For simple POS transactions
            ])->default('paid');
            $table->string('status')->default('draft'); // draft, mkt_approved, acct_approved, picking, gathered, invoice_prep, packed, dispatched, completed, cancelled
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->foreignId('prepared_by')->nullable()->constrained('users'); // Staff who created it
            $table->foreignId('approved_by_mkt')->nullable()->constrained('users');
            $table->foreignId('approved_by_acct')->nullable()->constrained('users');
            $table->text('remarks')->nullable();
            $table->string('attachment')->nullable(); // Path to POP, PO, Letter
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_orders');
    }
}
