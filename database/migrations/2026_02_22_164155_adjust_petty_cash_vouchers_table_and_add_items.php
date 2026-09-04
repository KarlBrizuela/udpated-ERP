<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdjustPettyCashVouchersTableAndAddItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('petty_cash_vouchers', function (Blueprint $table) {
            $table->dropForeign(['expense_account_id']);
            $table->dropColumn(['particulars', 'amount', 'expense_account_id']);
        });

        Schema::create('petty_cash_voucher_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petty_cash_voucher_id')->constrained('petty_cash_vouchers')->onDelete('cascade');
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
        Schema::dropIfExists('petty_cash_voucher_items');
        
        Schema::table('petty_cash_vouchers', function (Blueprint $table) {
            $table->text('particulars')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->unsignedBigInteger('expense_account_id')->nullable();
        });
    }
}
