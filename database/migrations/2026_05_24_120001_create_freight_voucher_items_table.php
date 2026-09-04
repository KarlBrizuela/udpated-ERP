<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreightVoucherItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freight_voucher_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freight_voucher_id')->constrained('freight_vouchers')->onDelete('cascade');
            $table->text('particulars');
            $table->decimal('amount', 15, 2);
            $table->foreignId('expense_account_id')->nullable()->constrained('chart_of_accounts')->onDelete('set null');
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
        Schema::dropIfExists('freight_voucher_items');
    }
}
