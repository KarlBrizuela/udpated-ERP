<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_cash_advances', function (Blueprint $table) {
            $table->dropColumn(['installment_count', 'repayment_start_date', 'is_liquidation_required']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_cash_advances', function (Blueprint $table) {
            $table->integer('installment_count')->default(1);
            $table->date('repayment_start_date')->nullable();
            $table->boolean('is_liquidation_required')->default(false);
        });
    }
};
