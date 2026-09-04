<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatementOfAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statement_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('soa_number')->unique();
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->onDelete('cascade');
            $table->date('billing_period_start');
            $table->date('billing_period_end');
            $table->decimal('total_amount', 15, 2);
            $table->string('status')->default('draft'); // draft, for_approval, approved
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statement_of_accounts');
    }
}
