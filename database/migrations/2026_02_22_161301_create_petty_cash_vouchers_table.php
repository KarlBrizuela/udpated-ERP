<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePettyCashVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('petty_cash_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('pcv_number')->unique();
            $table->date('date');
            $table->string('pay_to');
            $table->text('particulars');
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('expense_account_id')->nullable(); // Foreign key to chart_of_accounts
            $table->string('approved_by')->nullable();
            $table->string('received_by')->nullable();
            $table->enum('status', ['open', 'liquidated'])->default('open');
            $table->unsignedBigInteger('journal_entry_id')->nullable(); // Assigned upon liquidation
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('expense_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('petty_cash_vouchers');
    }
}
