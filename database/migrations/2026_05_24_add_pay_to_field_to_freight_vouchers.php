<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPayToFieldToFreightVouchers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('freight_vouchers', function (Blueprint $table) {
            if (!Schema::hasColumn('freight_vouchers', 'pay_to')) {
                $table->string('pay_to')->nullable()->after('supplier_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('freight_vouchers', function (Blueprint $table) {
            if (Schema::hasColumn('freight_vouchers', 'pay_to')) {
                $table->dropColumn('pay_to');
            }
        });
    }
}
